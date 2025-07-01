<?php

namespace App\Controllers;

use App\Models\DependenciaModel;
use App\Models\TicketModel;
use App\Controllers\BaseController;
use App\Models\Subcategoria;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ComentarioModel;
use App\Models\CategoriaModel;

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
            'SUBCATEGORIA_ID' => $this->request->getPost('SUBCATEGORIA_ID') !== '' ? $this->request->getPost('SUBCATEGORIA_ID') : null,
            'DEPENDENCIA_ID'  => $dependencia_final !== '' ? $dependencia_final : null,
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
            log_message('debug', 'Archivo válido para mover: ' . $archivo->getName());
            $nombreArchivo = $archivo->getName();
            $ruta = WRITEPATH . 'uploads/' . $nombreArchivo;

            if (!is_dir(WRITEPATH . 'uploads')) {
                mkdir(WRITEPATH . 'uploads', 0755, true);
            }

            $archivo->move(WRITEPATH . 'uploads', $nombreArchivo);

            if (!file_exists($ruta)) {
                log_message('error', 'El archivo no se movió correctamente: ' . $ruta);
            } else {
                log_message('debug', 'Archivo movido correctamente: ' . $ruta);
                $db = \Config\Database::connect();
                $sql = "INSERT INTO ARCHIVOS (
                    TICKET_ID, 
                    USUARIO_ID, 
                    NOMBRE_ARCHIVO, 
                    RUTA_ARCHIVO, 
                    TAMANO, 
                    UPLOADED_AT
                ) VALUES (?, ?, ?, ?, ?, SYSTIMESTAMP)";

                $result = $db->query($sql, [
                    $ticketId,
                    $usuario_id,
                    $nombreArchivo,
                    $ruta,
                    $archivo->getSize()
                ]);

                if (!$result) {
                    log_message('error', 'No se insertó el archivo en la base de datos: ' . print_r($db->error(), true));
                } else {
                    log_message('debug', 'Archivo insertado en la base de datos');
                }
            }
        } else {
            log_message('error', 'Archivo no válido o ya movido: ' . print_r($archivo, true));
        }

        // 1. Obtener datos del encargado de la categoría
        $categoria_id = $this->request->getPost('CATEGORIA_ID');
        $categoriaModel = new CategoriaModel();
        $categoria = $categoriaModel->find($categoria_id);

        $encargado_id = $categoria['ENCARAGADO_ID'];

        $usuarioModel = new UsuarioModel();
        $encargado = $usuarioModel->find($encargado_id);
        $correoEncargado = $encargado['EMAIL'];

        // 2. Obtener datos del usuario que creó el ticket
        $usuario = $usuarioModel->find($usuario_id);

        // 3. Preparar y enviar el correo
        $email = \Config\Services::email();
        $email->setTo($correoEncargado);
        $email->setSubject('Nuevo ticket creado en tu categoría');

        $mensaje = "
            <h2>Nuevo Ticket Creado</h2>
            <p><strong>Título:</strong> {$data['TITULO']}</p>
            <p><strong>Descripción:</strong> {$data['DESCRIPCION']}</p>
            <p><strong>Prioridad:</strong> {$data['PRIORIDAD']}</p>
            <hr>
            <h3>Datos del usuario que emitió el ticket:</h3>
            <p><strong>Nombre:</strong> {$usuario['NOMBRE']}</p>
            <p><strong>Email:</strong> {$usuario['EMAIL']}</p>
        ";
        $email->attach('uploads/' . $nombreArchivo);
        $email->setMessage($mensaje);
        $email->setMailType('html');

        $enviado = $email->send() ? 'S' : 'N';

        // $notificacionModel = new \App\Models\NotificacionModel();
        // $notificacionModel->insert([
        //     'USUARIO_ID' => $encargado_id,
        //     'TICKET_ID' => $ticketId,
        //     'TIPO' => 'EMAIL',
        //     'ASUNTO' => 'Nuevo ticket creado en tu categoría',
        //     'MENSAJE' => $mensaje,
        //     'ENVIADO' => $enviado,
        //     'SENT_AT' => $enviado === 'S' ? date('Y-m-d H:i:s') : null,
        //     'CREATED_AT' => date('Y-m-d H:i:s')
        // ]);
        // Insertar notificación en la base de datos
        $db = \Config\Database::connect();
        $sql = "INSERT INTO NOTIFICACIONES (
            USUARIO_ID, TICKET_ID, TIPO, ASUNTO, MENSAJE, ENVIADO, SENT_AT, CREATED_AT
        ) VALUES (?, ?, ?, ?, ?, ?, TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'), TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS'))";

        $db->query($sql, [
            $encargado_id,
            $ticketId,
            'EMAIL',
            'Nuevo ticket creado en tu categoría',
            $mensaje,
            $enviado,
            $enviado === 'S' ? date('Y-m-d H:i:s') : null,
            date('Y-m-d H:i:s')
        ]);

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
            return redirect()->to('/mis_tickets')->with('error', 'Ticket no encontrado');
        }

        // Conversión de OCILob y fechas
        if (is_object($ticket['DESCRIPCION']) && get_class($ticket['DESCRIPCION']) === 'OCILob') {
            $ticket['DESCRIPCION'] = $ticket['DESCRIPCION']->load();
        }
        if (isset($ticket['FECHA_CREACION']) && is_object($ticket['FECHA_CREACION'])) {
            if (method_exists($ticket['FECHA_CREACION'], 'format')) {
                $ticket['FECHA_CREACION'] = $ticket['FECHA_CREACION']->format('Y-m-d H:i:s');
            } elseif (method_exists($ticket['FECHA_CREACION'], 'load')) {
                $ticket['FECHA_CREACION'] = $ticket['FECHA_CREACION']->load();
            }
        }

        // Obtener comentarios del ticket
        $comentarioModel = new ComentarioModel();
        $comentarios = $comentarioModel->getComentariosPorTicket($id, true);

        foreach ($comentarios as &$comentario) {
            if (is_object($comentario['CONTENIDO']) && get_class($comentario['CONTENIDO']) === 'OCILob') {
                $comentario['CONTENIDO'] = $comentario['CONTENIDO']->load();
            }
        }
        unset($comentario);

        // Obtener archivos adjuntos del ticket
        $archivos = $db->query("SELECT * FROM ARCHIVOS WHERE TICKET_ID = ?", [$id])->getResultArray();

        // Retorna la vista con los detalles del ticket, comentarios y archivos
        return view('empleado/ver_ticket', [
            'ticket' => $ticket,
            'CATEGORIA_ID' => $ticket['CATEGORIA_ID'],
            'comentarios' => $comentarios,
            'archivos' => $archivos
        ]);
    }

    public function descargarArchivo($nombreArchivo)
    {
        $ruta = WRITEPATH . 'uploads/' . $nombreArchivo;

        if (!is_file($ruta)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Archivo no encontrado");
        }

        // Alternativa: Detectar MIME por extensión
        $extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
        $mimes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'pdf'  => 'application/pdf',
            // agrega más si lo necesitas
        ];
        $mime = $mimes[$extension] ?? 'application/octet-stream';

        if (str_starts_with($mime, 'image/') || $mime === 'application/pdf') {
            return $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', 'inline; filename="' . $nombreArchivo . '"')
                ->setBody(file_get_contents($ruta));
        }

        return $this->response->download($ruta, null);
    }
}
