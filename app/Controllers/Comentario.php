<?php

namespace App\Controllers;
use App\Models\ComentarioModel;
use App\Models\TicketModel;
use App\Models\UsuarioModel;    
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controlador para la gestión de comentarios en los tickets.
 * Permite agregar comentarios y notificar al usuario correspondiente.
 */
class Comentario extends BaseController
{
    /**
     * Permite agregar un comentario a un ticket específico.
     * Guarda el comentario, obtiene los datos del ticket y usuario, y envía una notificación por correo.
     */
    public function comentar($id)
    {
        $comentarioModel = new ComentarioModel();
        $ticketModel = new TicketModel();
        $usuarioModel = new UsuarioModel();
        $session = session();
        $usuario_id = $session->get('id');
        $contenido = $this->request->getPost('comentario');

        // Guardar comentario
        $result = $comentarioModel->insert([
            'TICKET_ID' => $id,
            'USUARIO_ID' => $usuario_id,
            'CONTENIDO' => $contenido,
            'ES_SOLUCION' => 'N',
            'VISIBLE_USUARIO' => 'S',
        ]);

        if ($result === false) {
            // Si hay errores, los muestro para depuración
            dd($comentarioModel->errors());
        }

        // Obtener datos del ticket y usuario para el correo
        $ticket = $ticketModel->find($id);
        $usuarioTicket = $usuarioModel->find($ticket['USUARIO_ID']);
        // Si por alguna razón $usuarioTicket es un builder, usa first()
        if (!is_array($usuarioTicket)) {
            $usuarioTicket = $usuarioModel->where('ID', $ticket['USUARIO_ID'])->first();
        }

        // Enviar correo de notificación al usuario del ticket
        $email = \Config\Services::email();
        $email->setTo($usuarioTicket['EMAIL']);
        $email->setSubject('Respuesta a tu ticket #' . $ticket['ID']);
        $email->setMessage("Hola " . $usuarioTicket['NOMBRE'] . ",\n\nEl equipo de soporte ha respondido a tu ticket:\n\n" . $contenido . "\n\nPuedes ver más detalles en la plataforma.");
        $email->send();

        $rol = $session->get('rol');
        // Si no hay rol en sesión, deducir por la URL de origen
        if (!$rol) {
            $referer = $this->request->getServer('HTTP_REFERER');
            if (strpos($referer, 'soporte') !== false) {
                $rol = 'SOPORTE';
            } else {
                $rol = 'EMPLEADO';
            }
        }
        if ($rol === 'SOPORTE') {
            return redirect()->to('ver_datalles_ticket/' . $id)->with('mensaje', 'Respuesta enviada correctamente.');
        } else {
            return redirect()->to('ver_ticket/' . $id)->with('mensaje', 'Respuesta enviada correctamente.');
        }
    }
}