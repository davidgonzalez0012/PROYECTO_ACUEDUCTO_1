<?php

namespace App\Controllers;
use App\Models\TicketModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controlador para la gestión de tickets por categoría.
 * Permite listar tickets filtrados por categoría para administrador y soporte.
 */
class Categoria extends BaseController
{
    /**
     * Muestra los tickets de una categoría específica para el administrador.
     * Calcula el total de tickets y los pasa a la vista.
     */
    public function index($categoria)
    {
        $ticketModel = new TicketModel();
        $tickets = $ticketModel->where('CATEGORIA_ID', $categoria)->findAll();
        $totalTickets = count($tickets);

        $data = [
            'categoria' => $categoria,
            'tickets'   => $tickets,
            'totalTickets' => $totalTickets, // Total de tickets en la categoría
        ];

        return view('administrador/tickets', $data);
    }

    /**
     * Muestra los tickets de una categoría específica para el soporte.
     * Ordena los tickets por ID descendente y calcula el total.
     */
    public function categoria_tickets_soporte($categoria)
    {
        $ticketModel = new TicketModel();
        $tickets = $ticketModel->where('CATEGORIA_ID', $categoria)
                               ->orderBy('ID', 'DESC')
                               ->findAll();
        $totalTickets_soporte = count($tickets);
        $data= [
            'categoria' => $categoria,
            'tickets'   => $tickets,
            'totalTickets_soporte' => $totalTickets_soporte,
        ];
        return view('soporte/lista_tickets_por_categoria_soporte', $data);
    }
}
