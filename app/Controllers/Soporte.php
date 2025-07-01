<?php

namespace App\Controllers;

use App\Models\DependenciaModel;
use App\Models\TicketModel;
use App\Controllers\BaseController;
use App\Models\Subcategoria;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ComentarioModel;
use ZipArchive;

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

        // Obtener archivos adjuntos del ticket
        $archivos = $db->query("SELECT ID, NOMBRE_ARCHIVO, RUTA_ARCHIVO FROM ARCHIVOS WHERE TICKET_ID = ?", [$id])->getResultArray();

        return view('soporte/ver_datalles_ticket', [
            'ticket' => $ticket,
            'CATEGORIA_ID' => $ticket['CATEGORIA_ID'],
            'comentarios' => $comentarios,
            'archivos' => $archivos // <-- pasa los archivos a la vista
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

    /**
     * Descarga todos los archivos adjuntos de un ticket en un archivo ZIP.
     */
    public function descargar_archivos_ticket($ticket_id)
    {
        $db = \Config\Database::connect();
        $archivos = $db->query("SELECT NOMBRE_ARCHIVO, RUTA_ARCHIVO FROM ARCHIVOS WHERE TICKET_ID = ?", [$ticket_id])->getResultArray();

        if (empty($archivos)) {
            return redirect()->back()->with('error', 'No hay archivos para este ticket.');
        }

        $zip = new ZipArchive();
        $zipName = "archivos_ticket_$ticket_id.zip";
        $zipPath = WRITEPATH . 'uploads/' . $zipName;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('error', 'No se pudo crear el archivo ZIP.');
        }

        foreach ($archivos as $archivo) {
            if (file_exists($archivo['RUTA_ARCHIVO'])) {
                $zip->addFile($archivo['RUTA_ARCHIVO'], $archivo['NOMBRE_ARCHIVO']);
            }
        }
        $zip->close();

        return $this->response->download($zipPath, null)->setFileName($zipName);
    }

    public function mostrar_archivo($nombreArchivo)
    {
        $ruta = WRITEPATH . 'uploads/' . $nombreArchivo;
        if (!file_exists($ruta)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Archivo no encontrado");
        }

        // Detecta el tipo MIME (más compatible)
        $mime = mime_content_type($ruta);

        // Devuelve el archivo para visualizar en el navegador
        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . $nombreArchivo . '"')
            ->setBody(file_get_contents($ruta));
    }
}
