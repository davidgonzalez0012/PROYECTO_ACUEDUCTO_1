<!--
    Vista de login del sistema de tickets.
    Aquí se muestra el formulario de acceso para todos los roles (admin, soporte, empleado).
    Incluye mensajes de error y estilos personalizados.
-->
<!DOCTYPE html> <!-- Declaración del tipo de documento HTML -->
<html lang="en"> <!-- Inicio del documento HTML, idioma inglés (puedes cambiar a 'es' si prefieres) -->

<head>
  <!-- Metadatos y enlaces a estilos -->
  <meta charset="UTF-8"> <!-- Codificación de caracteres UTF-8 -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive para móviles -->
  <title>FAST Tickets - TI</title> <!-- Título de la pestaña del navegador -->
  <!-- Bootstrap desde CDN para estilos rápidos y responsivos -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome para iconos bonitos -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- Estilos personalizados para el login -->
  <link rel="stylesheet" href="<?php echo base_url('css/style_login.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('css/style_pagina_web.css'); ?>">
</head>

<body class="login-body"> <!-- Cuerpo de la página, clase para fondo personalizado -->
  <div class="container d-flex justify-content-center align-items-center vh-100"> <!-- Contenedor centrado vertical y horizontalmente -->
    <div class="login-card col-md-6"> <!-- Tarjeta de login con ancho medio -->
      <h2 class="text-center mb-4 text-white">Bienvenido a FAST TIKETS</h2> <!-- Título principal -->
      <?php if (session('error')): ?> <!-- Si hay un error en la sesión, lo muestra -->
        <div class="alert alert-danger"><?= session('error') ?></div> <!-- Mensaje de error -->
      <?php endif; ?>
      <form method="post" action="<?= site_url('auth/login') ?>"> <!-- Formulario de login, método POST -->
        <div class="mb-3"> <!-- Grupo de campo -->
          <label for="email " class="form-label text-white">Correo electrónico</label> <!-- Etiqueta del campo -->
          <input type="email" class="form-control" id="email" name="email" placeholder="usuario@ejemplo.com" required> <!-- Input de email -->
        </div>
        <div class="mb-3"> <!-- Grupo de campo -->
          <label for="password" class="form-label text-white">Contraseña</label> <!-- Etiqueta del campo -->
          <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required> <!-- Input de contraseña -->
        </div>
        <div class="mb-3"> <!-- Grupo de campo -->
          <label for="rol" class="form-label text-white">Tipo de usuario</label> <!-- Etiqueta del campo -->
          <select id="rol" name="rol" class="form-select"> <!-- Selector de rol -->
            <option value="ADMIN">Administrador</option> <!-- Opción admin -->
            <option value="SOPORTE">Soporte Técnico</option> <!-- Opción soporte -->
            <option value="EMPLEADO">Empleado</option> <!-- Opción empleado -->
          </select>
        </div>
        <div class="d-grid"> <!-- Botón ocupa todo el ancho -->
          <button type="submit" class="btn btn-primary">Iniciar sesión</button> <!-- Botón de login -->
        </div>
      </form>
      <p class="text-center text-muted mt-3">&copy; 2025 FAST TIKETS</p> <!-- Pie de página -->
    </div>
  </div>

</body>
<script>
  // Script para evitar que el usuario regrese a la página anterior con el botón atrás
  if (window.history && window.history.pushState) {
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function() {
      window.history.pushState(null, "", window.location.href);
    };
  }
</script>

</html> <!-- Fin del documento HTML -->