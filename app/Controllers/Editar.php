<?php

namespace App\Controllers;
use App\Models\TicketModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controlador para la edición de tickets por parte del empleado.
 * Permite mostrar el formulario de edición y actualizar los datos del ticket.
 */
class Editar extends BaseController
{
    /**
     * Muestra el formulario para editar un ticket de empleado.
     * Carga las dependencias y subcategorías necesarias para el formulario.
     */
    public function editar_ticket_empleado($id)
    {
        $ticketModel = new TicketModel();
        $dependenciaModel = new \App\Models\DependenciaModel();
        $subcategoriaModel = new \App\Models\Subcategoria();

        $ticket = $ticketModel->find($id);

        if (!$ticket) {
            // Si no existe el ticket, redirige con error
            return redirect()->to('/mis_tickets')->with('error', 'Ticket no encontrado');
        }

        // Solución al problema OCILob
        if (isset($ticket['DESCRIPCION']) && is_object($ticket['DESCRIPCION']) && get_class($ticket['DESCRIPCION']) === 'OCILob') {
            $ticket['DESCRIPCION'] = $ticket['DESCRIPCION']->load();
        }

        $dependencias = $dependenciaModel->findAll();
        $subcategorias = [];
        foreach ($subcategoriaModel->findAll() as $subcat) {
            $subcategorias[$subcat['CATEGORIA_ID']][] = [
                'id' => $subcat['ID'],
                'nombre' => $subcat['NOMBRE']
            ];
        }

        return view('empleado/crear_ticket', [
            'ticket' => $ticket,
            'dependencias' => $dependencias,
            'subcategorias' => $subcategorias
        ]);
    }

    /**
     * Actualiza los datos de un ticket de empleado.
     * @param int $id El ID del ticket a actualizar.
     */
public function actualizar_ticket($id)
{
    $ticketModel = new TicketModel();
    $session = session();
    $usuario_id = $session->get('id');

    // Obtén los datos del formulario
    $data = [
        'TITULO'          => $this->request->getPost('TITULO'),
        'DESCRIPCION'     => $this->request->getPost('DESCRIPCION'),
        'CATEGORIA_ID'    => $this->request->getPost('CATEGORIA_ID'),
        'SUBCATEGORIA_ID' => $this->request->getPost('SUBCATEGORIA_ID'),
        'DEPENDENCIA_ID'  => $this->request->getPost('DEPENDENCIA_ID'),
        'PRIORIDAD'       => $this->request->getPost('PRIORIDAD'),
        'ESTADO'          => $this->request->getPost('ESTADO')
    ];

    // Actualiza el ticket
    $ticketModel->update($id, $data);

    // Manejo de archivo adjunto (si se subió uno nuevo)
    $archivo = $this->request->getFile('archivo');
    if ($archivo && $archivo->isValid() && !$archivo->hasMoved()) {
        $nombreArchivo = $archivo->getName();
        $ruta = WRITEPATH . 'uploads/' . $nombreArchivo;

        if (!is_dir(WRITEPATH . 'uploads')) {
            mkdir(WRITEPATH . 'uploads', 0755, true);
        }

        $archivo->move(WRITEPATH . 'uploads', $nombreArchivo);

        if (file_exists($ruta)) {
            // Inserta el nuevo archivo en la tabla ARCHIVOS
            $db = \Config\Database::connect();
            $sql = "INSERT INTO ARCHIVOS (
                TICKET_ID, 
                USUARIO_ID, 
                NOMBRE_ARCHIVO, 
                RUTA_ARCHIVO, 
                TAMANO, 
                UPLOADED_AT
            ) VALUES (?, ?, ?, ?, ?, SYSTIMESTAMP)";

            $db->query($sql, [
                $id,
                $usuario_id,
                $nombreArchivo,
                $ruta,
                $archivo->getSize()
            ]);
        }
    }

    return redirect()->to('/mis_tickets')->with('mensaje', 'Ticket actualizado correctamente');

}
}
