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
 * Controlador para la gestión y visualización de tickets.
 * Permite listar, filtrar y mostrar tickets según diferentes criterios y roles.
 */
class Ticket extends BaseController
{
    /**
     * Muestra todos los tickets para el administrador.
     */
    public function todos()
    {
        $ticketModel = new TicketModel();
        $tickets = $ticketModel->findAll();
        $totalTickets = $ticketModel->countAll();         

        return view('administrador/tickets', [
            'tickets' => $tickets,
            'totalTickets' => $totalTickets
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Muestra todos los tickets para el soporte.
     */
    public function todos_soporte()
    {
        $ticketModel = new TicketModel();
        $tickets = $ticketModel->findAll();
        $totalTickets = $ticketModel->countAll();

        return view('soporte/lista_tickets_por_categoria_soporte', [
            'tickets' => $tickets,
            'totalTickets' => $totalTickets
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Muestra los tickets resueltos para el administrador.
     */
    public function resueltos()
    {
        $ticketModel = new TicketModel();
        $tickets = $ticketModel->where('ESTADO', 'CERRADO')->findAll();
        $totalTickets = count($tickets);
        return view('administrador/tickets', [
            'tickets' => $tickets,
            'totalTickets' => $totalTickets,
            'titulo' => 'Tickets Resueltos'
        ]);
    }

        /**
     * Muestra los tickets resueltos (cerrados) HOY para el administrador.
     */
    public function resueltosHoy()
    {
        $ticketModel = new TicketModel();
        $hoy = date('Y-m-d');
        $tickets = $ticketModel
            ->where('ESTADO', 'CERRADO')
            ->where("TRUNC(FECHA_ACTUALIZACION) = TO_DATE('$hoy', 'YYYY-MM-DD')")
            ->findAll();
        $totalTickets = count($tickets);
        return view('administrador/tickets', [
            'tickets' => $tickets,
            'totalTickets' => $totalTickets,
            'titulo' => 'Tickets Resueltos Hoy'
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Muestra los tickets resueltos para el soporte.
     */
    public function resueltos_soporte()
    {
        $ticketModel = new TicketModel();
        $tickets = $ticketModel->where('ESTADO', 'CERRADO')->findAll();
        $totalTickets = count($tickets);
        return view('soporte/lista_tickets_por_categoria_soporte', [
            'tickets' => $tickets,
            'totalTickets' => $totalTickets,
            'titulo' => 'Tickets Resueltos'
        ]);
    }

      /**
     * Muestra los tickets resueltos (cerrados) HOY para el soporte.
     */
    public function resueltosHoy_soporte()
    {
        $ticketModel = new TicketModel();
        $hoy = date('Y-m-d');
        $tickets = $ticketModel
            ->where('ESTADO', 'CERRADO')
            ->where("TRUNC(FECHA_ACTUALIZACION) = TO_DATE('$hoy', 'YYYY-MM-DD')")
            ->findAll();
        $totalTickets = count($tickets);
        return view('soporte/lista_tickets_por_categoria_soporte', [
            'tickets' => $tickets,
            'totalTickets' => $totalTickets,
            'titulo' => 'Tickets Resueltos Hoy'
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Muestra los tickets de alta prioridad para el administrador.
     */
    public function altaPrioridad()
    {
        $ticketModel = new TicketModel();
        $tickets = $ticketModel
            ->where('PRIORIDAD', 'ALTA')
            ->where('ESTADO', 'ABIERTO') // Solo los abiertos
            ->findAll();
        $totalTickets = count($tickets);
        return view('administrador/tickets', [
            'tickets' => $tickets,
            'totalTickets' => $totalTickets,
            'titulo' => 'Tickets de Alta Prioridad',
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Muestra los tickets de alta prioridad para el soporte.
     */
    public function altaPrioridad_soporte()
    {
        $ticketModel = new TicketModel();
        $tickets = $ticketModel
            ->where('PRIORIDAD', 'ALTA')
            ->where('ESTADO', 'ABIERTO') // Solo los abiertos
            ->findAll();
        $totalTickets = count($tickets);
        return view('soporte/lista_tickets_por_categoria_soporte', [
            'tickets' => $tickets,
            'totalTickets' => $totalTickets,
            'titulo' => 'Tickets de Alta Prioridad'
        ]);
    }


}