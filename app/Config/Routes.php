<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Home::index');
//rutas publicas 

//INICIAR SESION Y SALIR 
$routes->get('/login_iniciar', 'home::ingresar');
$routes->post('auth/login', 'Auth::login');
$routes->get('/login', 'Home::logout');

// RUTAS PROTEGIDAS POR SESIÓN Y ROL
$routes->group('', ['filter' => 'auth:EMPLEADO'], function($routes) {
    //rutas para contrlador de empleado
    $routes->get('/inicio_empleado', 'Home::index_empleado');
    $routes->get('empleado/inicio_empleado', 'Home::index_empleado');
    $routes->get('/crear_ticket', 'Empleado::crear_ticket_empleado');
    $routes->post('empleado/guardar_ticket_empleado', 'Empleado::guardar_ticket_empleado');
    $routes->get('/mis_tickets', 'Empleado::tickets_realizados_empleado');
    $routes->get('/ver_ticket/(:num)', 'Empleado::ver_ticket_empleado/$1');
    $routes->get('/editar_ticket/(:num)', 'Editar::editar_ticket_empleado/$1');
    $routes->post('empleado/actualizar_ticket/(:num)', 'Editar::actualizar_ticket/$1');
    $routes->get('tickets/ver/(:num)', 'Empleado::ver_ticket_empleado/$1');
    $routes->post('tickets/comentar/(:num)', 'Comentario::comentar/$1');
    $routes->get('descargar/archivo/(:any)', 'Empleado::descargarArchivo/$1');
});

$routes->group('', ['filter' => 'auth:SOPORTE'], function($routes) {
    //rutas para controlador de soporte
    $routes->get('/inicio_soporte', 'Home::index_soporte');
    $routes->get('tickets/todos-tickets_soporte', 'Ticket::todos_soporte');
    $routes->get('tickets_categoria/soporte/(:segment)', 'Categoria::categoria_tickets_soporte/$1');
    $routes->get('tickets/resueltos/soporte', 'Ticket::resueltos_soporte');
    $routes->get('tickets/resueltoshoy/soporte', 'Ticket::resueltosHoy_soporte');
    $routes->get('tickets/alta_prioridad/soporte', 'Ticket::altaPrioridad_soporte');
    $routes->get('ver_datalles_ticket/(:num)', 'Soporte::ver_datalles_ticket/$1');
    $routes->post('soporte/actualizar_estado_ticket/(:num)', 'Soporte::actualizar_estado_ticket_soporte/$1');
    $routes->get('/dependencias_soporte', 'Dependencia::index_soporte');
    $routes->get('/dependencias_soporte/(:num)', 'Dependencia::mostrarDependencia_soporte/$1');
    $routes->get('/dependencias_soporte/usuarios/(:num)', 'Dependencia::usuariosPorDependencia_soporte/$1');
    $routes->post('tickets/comentar_Soporte/(:num)', 'Comentario::comentar/$1');
});

$routes->group('', ['filter' => 'auth:ADMIN'], function($routes) {
    //rutas para controlador de admin
    $routes->get('/inicio_admin', 'Home::index_admin');
    $routes->get('/crear_ticket_admin', 'Administrador::nuevo');
    $routes->post('tickets/guardar', 'Administrador::guardar');
    $routes->get('tickets/todos', 'Ticket::todos');
    $routes->get('tickets_categoria/(:segment)', 'Categoria::index/$1');
    $routes->get('tickets/resueltos/admin', 'Ticket::resueltos');
    $routes->get('tickets/resueltoshoy/admin', 'Ticket::resueltosHoy');
    $routes->get('tickets/alta_prioridad/admin', 'Ticket::altaPrioridad');
    $routes->get('ver_tickets/(:num)', 'Administrador::ver_ticket_admin/$1');
    $routes->post('administrador/actualizar_estado_ticket/(:num)', 'Administrador::actualizar_estado_ticket_admin/$1');
    $routes->get('/dependencias', 'Dependencia::index');
    $routes->get('dependencias/(:num)', 'Dependencia::mostrarDependencia/$1');
    $routes->get('dependencias/usuarios/(:num)', 'Dependencia::usuariosPorDependencia/$1');
});




