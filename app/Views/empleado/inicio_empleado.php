<?php echo $this->extend('layouts/app_empleado'); ?>

<?php echo $this->section('content'); ?>

<section class="hero-section">
  <div class="container">
    <h1 class="display-5 fw-bold">Bienvenido a FAST TIKETS</h1>
    <p class="lead">Sistema de gestión de soporte para empleados de la AAP</p>
  </div>
</section>

<!-- SECCIÓN DE TICKETS -->
<section id="tickets" class="py-5">
  <div class="container">
    <h2 class="mb-4">Mis Tickets Recientes</h2>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Título</th>
            <th>Estado</th>
            <th>Prioridad</th>
            <th>Fecha de creación</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($tickets_recientes)): ?>
            <?php foreach ($tickets_recientes as $ticket): ?>
              <tr>
                <td><?= esc($ticket['ID']) ?></td>
                <td><?= esc($ticket['TITULO']) ?></td>
                <td>
                  <?php
                    $estadoClass = match($ticket['ESTADO']) {
                      'ABIERTO' => 'bg-success',
                      'EN_PROCESO' => 'bg-warning text-dark',
                      'CERRADO' => 'bg-secondary',
                      default => 'bg-info'
                    };
                  ?>
                  <span class="badge <?= $estadoClass ?>"> <?= esc($ticket['ESTADO']) ?> </span>
                </td>
                <td>
                  <?php
                    $prioridadClass = match($ticket['PRIORIDAD']) {
                      'CRITICA' => 'bg-danger',
                      'ALTA' => 'bg-warning',
                      'MEDIA' => 'bg-info',
                      'BAJA' => 'bg-success',
                      default => 'bg-secondary'
                    };
                  ?>
                  <span class="badge <?= $prioridadClass ?>"> <?= esc($ticket['PRIORIDAD']) ?> </span>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($ticket['FECHA_CREACION'])) ?></td>
                <td>
                  <a href="<?= base_url('ver_ticket/' . $ticket['ID']) ?>" class="btn btn-sm btn-outline-primary">Ver</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center">No tienes tickets recientes.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php echo $this->endSection(); ?>