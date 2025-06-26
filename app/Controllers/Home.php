<?php

namespace App\Controllers;
USE App\Models\TicketModel;

/**
 * Controlador principal de la aplicación.
 * Gestiona las páginas de inicio y paneles según el rol del usuario.
 */
class Home extends BaseController
{
    /**
     * Muestra la página de bienvenida por defecto.
     */
    public function index(): string
    {
        return view('welcome_message');
    }

    /**
     * Muestra la página de login.
     */
    public function ingresar(): string{
        return view('login');
    }

    /**
     * Muestra el panel de control del administrador con estadísticas y tickets recientes.
     */
    public function index_admin()
    {
        $ticketModel = new TicketModel();
        // Conteo total de tickets activos (solo abiertos o en proceso)
        $totalTickets = (new TicketModel())
            ->whereIn('ESTADO', ['ABIERTO', 'EN_PROCESO'])
            ->countAllResults();

        // Tickets resueltos hoy (cerrados hoy)
        $resueltosHoy = (new TicketModel())
            ->where('ESTADO', 'CERRADO')
            ->where("TRUNC(FECHA_ACTUALIZACION) = TO_DATE('" . date('Y-m-d') . "', 'YYYY-MM-DD')")
            ->countAllResults();

        // Tickets cerrados (todos los tiempos)
        $cerradosTotal = (new TicketModel())
            ->where('ESTADO', 'CERRADO')
            ->countAllResults();

        // Tickets de alta prioridad (solo abiertos)
        $altaPrioridad = (new TicketModel())
            ->where('PRIORIDAD', 'ALTA')
            ->where('ESTADO', 'ABIERTO')
            ->countAllResults();

        // 10 tickets más recientes
        $db = \Config\Database::connect();
        $recientes = $db->query(
            "SELECT t.*, u.NOMBRE AS SOLICITANTE
             FROM (SELECT * FROM TICKETS ORDER BY FECHA_CREACION DESC) t
             LEFT JOIN USUARIOS u ON t.USUARIO_ID = u.ID
             WHERE ROWNUM <= 10"
        )->getResultArray();

        // Conteo por categoría
        $hardware = (new TicketModel())->where('CATEGORIA_ID', 1)->countAllResults();
        $software = (new TicketModel())->where('CATEGORIA_ID', 2)->countAllResults();
        $redes = (new TicketModel())->where('CATEGORIA_ID', 3)->countAllResults();

        $hardwarePct = $totalTickets > 0 ? round(($hardware / $totalTickets) * 100) : 0;
        $softwarePct = $totalTickets > 0 ? round(($software / $totalTickets) * 100) : 0;
        $redesPct = $totalTickets > 0 ? round(($redes / $totalTickets) * 100) : 0;

        // Retorna la vista con todas las variables necesarias
        return view('administrador/inicio_administrador', [
            'recientes'      => $recientes,
            'hardwarePct'    => $hardwarePct,
            'softwarePct'    => $softwarePct,
            'redesPct'       => $redesPct,
            'totalTickets'   => $totalTickets,
            'resueltosHoy'   => $resueltosHoy,
            'cerradosTotal'  => $cerradosTotal,
            'altaPrioridad'  => $altaPrioridad,
        ]);
    }

    /**
     * Muestra el panel de control del soporte con estadísticas y tickets recientes.
     */
    public function index_soporte(): string
    {
        $ticketModel = new TicketModel();
        // Conteo total de tickets activos (solo abiertos o en proceso)
        $total = (new TicketModel())
            ->whereIn('ESTADO', ['ABIERTO', 'EN_PROCESO'])
            ->countAllResults();
        $resueltosHoy = (new TicketModel())
            ->where('ESTADO', 'CERRADO')
            ->where("TRUNC(FECHA_ACTUALIZACION) = TO_DATE('" . date('Y-m-d') . "', 'YYYY-MM-DD')")
            ->countAllResults();
        $cerradosTotal = (new TicketModel())
            ->where('ESTADO', 'CERRADO')
            ->countAllResults();
        $altaPrioridad = (new TicketModel())
            ->where('PRIORIDAD', 'ALTA')
            ->where('ESTADO', 'ABIERTO')
            ->countAllResults();
        $db = \Config\Database::connect();
        $recientes = $db->query(
            "SELECT t.*, u.NOMBRE AS SOLICITANTE
             FROM (SELECT * FROM TICKETS ORDER BY FECHA_CREACION DESC) t
             LEFT JOIN USUARIOS u ON t.USUARIO_ID = u.ID
             WHERE ROWNUM <= 10"
        )->getResultArray();
        $hardware = $ticketModel->where('CATEGORIA_ID', 1)->countAllResults();
        $software = $ticketModel->where('CATEGORIA_ID', 2)->countAllResults();
        $redes = $ticketModel->where('CATEGORIA_ID', 3)->countAllResults();
        $hardwarePct_soporte = $total ? round(($hardware / $total) * 100) : 0;
        $softwarePct_soporte = $total ? round(($software / $total) * 100) : 0;
        $redesPct_soporte = $total ? round(($redes / $total) * 100) : 0;
        return view('soporte/inicio_soporte', [
            'recientes'      => $recientes,
            'hardwarePct_soporte'    => $hardwarePct_soporte,
            'softwarePct_soporte'    => $softwarePct_soporte,
            'redesPct_soporte'       => $redesPct_soporte,
            'totalTickets'   => $total,
            'resueltosHoy'   => $resueltosHoy,
            'cerradosTotal'  => $cerradosTotal,
            'altaPrioridad'  => $altaPrioridad,
        ]);
    }

    /**
     * Muestra el panel de control del empleado con sus tickets recientes.
     */
    public function index_empleado(): string
    {
        $session = session();
        $usuario_id = $session->get('id');
        $db = \Config\Database::connect();
        $limit = 10;
        $sql = "SELECT * FROM (
                    SELECT T.ID, T.TITULO, T.ESTADO, T.PRIORIDAD, TO_CHAR(T.FECHA_CREACION, 'YYYY-MM-DD HH24:MI:SS') AS FECHA_CREACION, C.NOMBRE AS CATEGORIA_NOMBRE
                    FROM TICKETS T
                    JOIN CATEGORIAS C ON C.ID = T.CATEGORIA_ID
                    WHERE T.USUARIO_ID = ?
                    ORDER BY T.FECHA_CREACION DESC
                ) WHERE ROWNUM <= $limit";
        $query = $db->query($sql, [$usuario_id]);
        $tickets_recientes = $query->getResultArray();
        return view('empleado/inicio_empleado', ['tickets_recientes' => $tickets_recientes]);
    }

    /**
     * Cierra la sesión del usuario y lo redirige al login.
     */
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login_iniciar');
    }
}
