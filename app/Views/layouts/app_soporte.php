<!--
    Vista de layout principal para el panel de soporte.
    Define la estructura base (sidebar, header, loader, etc.) para todas las páginas de soporte.
    Muestra el nombre del usuario logueado y el menú lateral con las opciones de soporte.
-->
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos y enlaces a estilos -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAST Tickets - Soporte</title>
    <!-- Bootstrap y estilos propios -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('css/style_pagina_web.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('css/style_contenidoEmpleado.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('css/style_login.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('css/style_loader.css'); ?>">
</head>
<body>
    <!-- Loader animado mientras carga la página -->
    <div class="loader-wrapper">
        <div class="loader"></div>
        <div class="loader-text">Cargando...</div>
    </div>
    <div class="container-fluid">
        <!-- Botón para mostrar el sidebar en móvil -->
        <button class="btn btn-primary d-md-none my-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
            <i class="fas fa-bars"></i>
        </button>
        <div class="row">
            <!-- Sidebar fijo para escritorio -->
            <div class="col-md-3 col-lg-2 d-none d-md-block bb-blue sidebar">
                <div class="position-sticky">
                    <div class="w-100">
                        <img src="<?= base_url('images/logoAcueducto.png') ?>" alt="Logo" class="img-fluid w-100" style="height: auto; object-fit: contain;">
                    </div>
                    <div class="p-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-circle fa-2x text-white me-2"></i>
                            <div>
                                <h6 class="text-white mb-0">
                                    <?php 
                                        $session = session();
                                        $nombreUsuario = $session->get('nombre') ?? 'Usuario';
                                        echo esc($nombreUsuario);
                                    ?>
                                </h6>
                                <small class="text-light">Panel de Control</small>
                            </div>
                        </div>
                    </div>
                    <!-- Menú lateral con las opciones principales de soporte -->
                    <nav class="nav flex-column p-3">
                        <a class="nav-link active text-white mb-2" href="<?= base_url('/inicio_soporte') ?>">
                            <i class="fas fa-chart-pie me-2"></i> Panel de Control
                        </a>
                        <a class="nav-link text-white mb-2" data-bs-toggle="collapse" href="#categoriasSubmenu" role="button" aria-expanded="false" aria-controls="categoriasSubmenu">
                            <i class="fas fa-tags me-2"></i> Tickets por Categorías <i class="fas fa-caret-down ms-1"></i>
                        </a>
                        <div class="collapse ps-4" id="categoriasSubmenu">
                            <a class="nav-link text-white-50 mb-2" href="<?= base_url('tickets_categoria/soporte/2') ?>">Software</a>
                            <a class="nav-link text-white-50 mb-2" href="<?= base_url('tickets_categoria/soporte/1') ?>">Hardware</a>
                            <a class="nav-link text-white-50 mb-2" href="<?= base_url('tickets_categoria/soporte/3') ?>">Redes</a>
                        </div>
                        <a class="nav-link text-white mb-2" href="<?= base_url('dependencias_soporte') ?>">
                            <i class="fas fa-users me-2"></i> Dependencias
                        </a>
                        <hr class="border-light opacity-10 my-3">
                        <a class="nav-link text-white" href="<?= base_url('login') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                        </a>
                    </nav>
                </div>
            </div>
            <!-- Contenido principal de la página (se inserta con renderSection) -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 main-content">
                <div class="content-wrapper">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
            <!-- Sidebar para móvil (offcanvas) -->
            <div class="offcanvas offcanvas-start d-md-none bb-blue sidebar" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
                <div class="offcanvas-header">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <div class="p-4">
                        <h4 class="sidebar-brand">
                            <i class="fas fa-headset me-2"></i>
                            TI Support
                        </h4>
                    </div>
                    <!-- Menú lateral para móvil -->
                    <nav class="nav flex-column p-3">
                        <a class="nav-link active text-white mb-2" href="<?= base_url('inicio_soporte') ?>">
                            <i class="fas fa-chart-pie me-2"></i> Panel de Control
                        </a>
                        <a class="nav-link text-white mb-2" data-bs-toggle="collapse" href="#categoriasSubmenuMobile" role="button" aria-expanded="false" aria-controls="categoriasSubmenuMobile">
                            <i class="fas fa-tags me-2"></i> Tickets por Categorías <i class="fas fa-caret-down ms-1"></i>
                        </a>
                        <div class="collapse ps-4" id="categoriasSubmenuMobile">
                            <a class="nav-link text-white-50 mb-2" href="<?= base_url('tickets_categoria/soporte/2') ?>">Software</a>
                            <a class="nav-link text-white-50 mb-2" href="<?= base_url('tickets_categoria/soporte/1') ?>">Hardware</a>
                            <a class="nav-link text-white-50 mb-2" href="<?= base_url('tickets_categoria/soporte/3') ?>">Redes</a>
                        </div>
                        <a class="nav-link text-white mb-2" href="<?= base_url('dependencias_soporte') ?>">
                            <i class="fas fa-users me-2"></i> Dependencias
                        </a>
                        <hr class="border-light opacity-10 my-3">
                        <a class="nav-link text-white" href="<?= base_url('logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts de Bootstrap y loader -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url('javascript/funcion_loader.js'); ?>"></script>
</body>
</html>