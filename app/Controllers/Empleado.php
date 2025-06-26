<?php

namespace App\Controllers;

use App\Models\DependenciaModel;
use App\Models\TicketModel;
use App\Controllers\BaseController;
use App\Models\Subcategoria;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ComentarioModel;

/**
 * Controlador para la gestión de tickets por parte del empleado.
 * Permite crear, ver, listar y gestionar tickets propios.
 */
class Empleado extends BaseController
{
    /**
     * Muestra el formulario para crear un nuevo ticket como empleado.
     * Carga las dependencias y subcategorías necesarias para el formulario.
     */
    public function crear_ticket_empleado()
    {
        $dependenciaModel = new DependenciaModel();
        $subcategoriaModel = new Subcategoria();

        $dependencias = $dependenciaModel->findAll();

        // Agrupa subcategorías por CATEGORIA_ID
        $subcategorias = [];
        foreach ($subcategoriaModel->findAll() as $subcat) {
            $subcategorias[$subcat['CATEGORIA_ID']][] = [
                'id' => $subcat['ID'],
                'nombre' => $subcat['NOMBRE']
            ];
        }
        return view('empleado/crear_ticket', [
            'dependencias' => $dependencias,
            'subcategorias' => $subcategorias
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   
    /**
     * Guarda un nuevo ticket creado por el empleado.
     * Maneja la subida de archivos adjuntos y la inserción en la base de datos.
     */
    public function guardar_ticket_empleado()
    {
        $ticketModel = new TicketModel();
        $session = session();
        $usuario_id = $session->get('id');

        // Obtengo los datos del formulario
        $datos_formulario = $this->request->getPost();

        // Obtener la dependencia seleccionada en el formulario
        $dependencia_final = $this->request->getPost('DEPENDENCIA_ID');

        $data = [
            'TITULO'          => $this->request->getPost('TITULO'),
            'DESCRIPCION'     => $this->request->getPost('DESCRIPCION'),
            'USUARIO_ID'      => $usuario_id,
            'CATEGORIA_ID'    => $this->request->getPost('CATEGORIA_ID'),
            'SUBCATEGORIA_ID' => $this->request->getPost('SUBCATEGORIA_ID'),
            'DEPENDENCIA_ID'  => $dependencia_final,
            'PRIORIDAD'       => $this->request->getPost('PRIORIDAD'),
            'ESTADO'          => 'ABIERTO'
        ];

        // Intento guardar el ticket
        $ticketId = $ticketModel->insertarTicket($data);

        if (!$ticketId) {
            // Si hay errores de validación, los muestro
            if ($ticketModel->errors()) {
                dd($ticketModel->errors());
            }
            // O redirijo con mensaje de error
            return redirect()->back()->with('error', 'Error al crear el ticket')->withInput();
        }

        // Manejo de archivo adjunto (si se subió)
        $archivo = $this->request->getFile('archivo');
        if ($archivo && $archivo->isValid() && !$archivo->hasMoved()) {
            $nombreArchivo = $archivo->getName();
            $ruta = WRITEPATH . 'uploads/' . $nombreArchivo;

            // Creo el directorio si no existe
            if (!is_dir(WRITEPATH . 'uploads')) {
                mkdir(WRITEPATH . 'uploads', 0755, true);
            }

            $archivo->move(WRITEPATH . 'uploads', $nombreArchivo);

            // Guardo el archivo en la tabla ARCHIVOS
            $db = \Config\Database::connect();

            try {
                $sql = "INSERT INTO ARCHIVOS (
                    TICKET_ID, 
                    USUARIO_ID, 
                    NOMBRE_ARCHIVO, 
                    RUTA_ARCHIVO, 
                    TAMANO, 
                    UPLOADED_AT
                ) VALUES (?, ?, ?, ?, ?, SYSDATE)";

                $db->query($sql, [
                    $ticketId,
                    $usuario_id,
                    $nombreArchivo,
                    $ruta,
                    $archivo->getSize()
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Error al guardar archivo: ' . $e->getMessage());
                // Si falla el archivo, no detengo el ticket
            }
        }

        return redirect()->to('empleado/inicio_empleado')->with('mensaje', 'Ticket creado correctamente');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   
    /**
     * Muestra la lista de tickets realizados por el empleado.
     */
    public function tickets_realizados_empleado()
    {
        $session = session();
        $usuario_id = $session->get('id');

        if (!$usuario_id) {
            return redirect()->to('/login')->with('error', 'Debe iniciar sesión para ver sus tickets');
        }

        $ticketModel = new TicketModel();
        $tickets = $ticketModel->getTicketsPorUsuario($usuario_id);

        return view('empleado/mis_tickets', [
            'tickets' => $tickets
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   
    /**
     * Muestra los detalles de un ticket específico creado por el empleado.
     * Realiza conversiones de datos especiales y maneja errores si el ticket no existe.
     */
    public function ver_ticket_empleado($id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT T.ID, T.TITULO, T.ESTADO, T.PRIORIDAD, TO_CHAR(T.FECHA_CREACION, 'YYYY-MM-DD HH24:MI:SS') AS FECHA_CREACION, T.DESCRIPCION, T.CATEGORIA_ID, C.NOMBRE AS CATEGORIA_NOMBRE
            FROM TICKETS T
            JOIN CATEGORIAS C ON C.ID = T.CATEGORIA_ID
            WHERE T.ID = ?";
        $query = $db->query($sql, [$id]);
        $ticket = $query->getRowArray();

        if (!$ticket) {
            // Si no existe el ticket, redirijo con error
            return redirect()->to('/mis_tickets')->with('error', 'Ticket no encontrado');
        }

        // Si la descripción es un objeto OCILob, la convierto a string
        if (is_object($ticket['DESCRIPCION']) && get_class($ticket['DESCRIPCION']) === 'OCILob') {
            $ticket['DESCRIPCION'] = $ticket['DESCRIPCION']->load();
        }

        // Si la fecha de creación es un objeto, la convierto a string
        if (isset($ticket['FECHA_CREACION']) && is_object($ticket['FECHA_CREACION'])) {
            if (method_exists($ticket['FECHA_CREACION'], 'format')) {
                $ticket['FECHA_CREACION'] = $ticket['FECHA_CREACION']->format('Y-m-d H:i:s');
            } elseif (method_exists($ticket['FECHA_CREACION'], 'load')) {
                $ticket['FECHA_CREACION'] = $ticket['FECHA_CREACION']->load();
            }
        }

        // Obtener comentarios del ticket
        $comentarioModel = new \App\Models\ComentarioModel();
        $comentarios = $comentarioModel->getComentariosPorTicket($id, true);

        // Convertir OCILob a string si es necesario
        foreach ($comentarios as &$comentario) {
            if (is_object($comentario['CONTENIDO']) && get_class($comentario['CONTENIDO']) === 'OCILob') {
                $comentario['CONTENIDO'] = $comentario['CONTENIDO']->load();
            }
        }
        unset($comentario);

        // Retorna la vista con los detalles del ticket y comentarios
        return view('empleado/ver_ticket', [
            'ticket' => $ticket,
            'CATEGORIA_ID' => $ticket['CATEGORIA_ID'],
            'comentarios' => $comentarios
        ]);
    }
}
