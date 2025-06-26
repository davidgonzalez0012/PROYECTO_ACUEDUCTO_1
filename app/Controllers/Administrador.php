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
 * Controlador para la gestión de tickets por parte del administrador.
 * Aquí se encuentran los métodos para crear, ver, actualizar y cambiar el estado de los tickets.
 * También se maneja la subida de archivos adjuntos.
 */
class Administrador extends BaseController
{
    /**
     * Muestra el formulario para crear un nuevo ticket como administrador.
     * Obtiene las dependencias y subcategorías para el formulario.
     */
    public function nuevo()
    {
        $dependenciaModel = new DependenciaModel();
        $subcategoriaModel = new Subcategoria();

        $dependencias = $dependenciaModel->findAll();

        // Agrupo las subcategorías por CATEGORIA_ID para facilitar el uso en el formulario
        $subcategorias = [];
        foreach ($subcategoriaModel->findAll() as $subcat) {
            $subcategorias[$subcat['CATEGORIA_ID']][] = [
                'id' => $subcat['ID'],
                'nombre' => $subcat['NOMBRE']
            ];
        }

        return view('administrador/crear_ticket_admin', [
            'dependencias' => $dependencias,
            'subcategorias' => $subcategorias
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    /**
     * Guarda un nuevo ticket en la base de datos.
     * También maneja la subida de archivos adjuntos.
     */
    public function guardar()
    {
        $ticketModel = new TicketModel();
        $session = session();
        $usuario_id = $session->get('id');

        // Obtengo los datos del formulario
        $datos_formulario = $this->request->getPost();


        //         log_message('debug', 'Datos del formulario: ' . print_r($datos_formulario, true));
        // dd($this->request->getPost('DEPENDENCIA_ID'));


        // Obtener la dependencia del usuario logueado
        // $usuarioModel = new UsuarioModel();
        //  $usuario = $usuarioModel->find($usuario_id);
        //  $dependencia_id = $usuario['DEPENDENCIA_ID'] ?? null;

        // // Si viene del formulario, usar ese valor, sino usar el del usuario
        // $dependencia_formulario = $this->request->getPost('DEPENDENCIA_ID');


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


           // Debug: ver qué se va a insertar

           //log_message('debug', 'Dependencia enviada al modelo: ' . var_export($data['DEPENDENCIA_ID'], true));
           // Usar el nuevo método insertarTicket en lugar de insert()
           
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

        return redirect()->to('/tickets/todos')->with('mensaje', 'Ticket creado correctamente');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Muestra los detalles de un ticket específico.
     * Realiza conversiones de datos especiales y maneja errores si el ticket no existe.
     */
    public function ver_ticket_admin($id)
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
            return redirect()->to('/tickets/todos')->with('error', 'Ticket no encontrado');
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

        return view('administrador/ver_tickets', [
            'ticket' => $ticket,
            'CATEGORIA_ID' => $ticket['CATEGORIA_ID']
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Actualiza los datos de un ticket.
     * Solo actualiza los campos que han cambiado.
     */
    public function actualizar($id)
    {
        $ticketModel = new TicketModel();

        $data = [
            'TITULO'          => $this->request->getPost('TITULO'),
            'DESCRIPCION'     => $this->request->getPost('DESCRIPCION'),
            'CATEGORIA_ID'    => $this->request->getPost('CATEGORIA_ID'),
            'SUBCATEGORIA_ID' => $this->request->getPost('SUBCATEGORIA_ID'),
            'DEPENDENCIA_ID'  => $this->request->getPost('DEPENDENCIA_ID'),
            'PRIORIDAD'       => $this->request->getPost('PRIORIDAD'),
            'ESTADO'          => $this->request->getPost('ESTADO')
        ];

        // Quito los valores vacíos para no sobreescribir con null
        $data = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });

        if ($ticketModel->actualizarTicket($id, $data)) {
            return redirect()->to('/tickets/ver/' . $id)->with('mensaje', 'Ticket actualizado correctamente');
        } else {
            return redirect()->back()->with('error', 'Error al actualizar el ticket')->withInput();
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Cambia el estado de un ticket (por ejemplo, a CERRADO).
     * Si el estado es CERRADO, también actualiza la fecha de cierre.
     */
    public function actualizar_estado_ticket_admin($id)
    {
        $ticketModel = new TicketModel();
        $nuevoEstado = $this->request->getPost('ESTADO');
        if (!$nuevoEstado) {
            return redirect()->back()->with('error', 'Debe seleccionar un estado.');
        }
        $data = [
            'ESTADO' => $nuevoEstado
        ];
        if ($nuevoEstado === 'CERRADO') {
            $data['FECHA_CIERRE'] = true; // Solo la clave, el modelo lo detecta y usa SYSDATE
        }
        if ($ticketModel->actualizarTicket($id, $data)) {
            return redirect()->to('/ver_tickets/' . $id)->with('mensaje', 'Estado actualizado correctamente');
        } else {
            return redirect()->back()->with('error', 'Error al actualizar el estado del ticket');
        }
    }
}
