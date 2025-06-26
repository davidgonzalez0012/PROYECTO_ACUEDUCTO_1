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
 * Controlador para la gestión de tickets por parte del soporte.
 * Permite ver detalles de tickets, comentarios y actualizar el estado de los tickets.
 */
class Soporte extends BaseController
{
    /**
     * Muestra los detalles de un ticket específico para el soporte.
     * Incluye los comentarios asociados y realiza conversiones de datos especiales.
     */
    public function ver_datalles_ticket($id)
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

        $comentarioModel = new ComentarioModel();
        $comentarios = $comentarioModel->getComentariosPorTicket($id, false);

        // Convertir OCILob a string si es necesario
        foreach ($comentarios as &$comentario) {
            if (is_object($comentario['CONTENIDO']) && get_class($comentario['CONTENIDO']) === 'OCILob') {
                $comentario['CONTENIDO'] = $comentario['CONTENIDO']->load();
            }
        }
        unset($comentario);

        return view('soporte/ver_datalles_ticket', [
            'ticket' => $ticket,
            'CATEGORIA_ID' => $ticket['CATEGORIA_ID'],
            'comentarios' => $comentarios
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   
    /**
     * Permite al soporte actualizar el estado de un ticket.
     * Si el estado es CERRADO, también actualiza la fecha de cierre.
     */
    public function actualizar_estado_ticket_soporte($id)
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
            return redirect()->to('/ver_datalles_ticket/' . $id)->with('mensaje', 'Estado actualizado correctamente');
        } else {
            return redirect()->back()->with('error', 'Error al actualizar el estado del ticket');
        }
    }
}
