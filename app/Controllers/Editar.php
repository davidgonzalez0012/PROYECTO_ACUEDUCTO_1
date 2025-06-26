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
     * Actualiza los datos de un ticket editado por el empleado.
     * Solo actualiza los campos que han cambiado.
     */
    public function actualizar_ticket_empleado($id)
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
}
