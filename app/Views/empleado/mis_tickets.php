<?php echo $this->extend('layouts/app_empleado'); ?>

<?php echo $this->section('content'); ?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">
                <i class="fas fa-ticket-alt me-2"></i>
                Mis Tickets
            </h2>
        </div>
        <div class="card-body">
            <?php if (empty($tickets)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No tienes tickets creados aún.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Prioridad</th>
                                <th>Fecha de creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td><?= esc($ticket['ID']) ?></td>
                                    <td><?= esc($ticket['TITULO']) ?></td>
                                    <td><?= esc($ticket['CATEGORIA_NOMBRE']) ?></td>
                                    <td>
                                        <?php
                                        $estadoClass = match($ticket['ESTADO']) {
                                            'ABIERTO' => 'bg-success',
                                            'EN_PROCESO' => 'bg-warning',
                                            'CERRADO' => 'bg-secondary',
                                            default => 'bg-info'
                                        };
                                        ?>
                                        <span class="badge <?= $estadoClass ?>">
                                            <?= esc($ticket['ESTADO']) ?>
                                        </span>
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
                                        <span class="badge <?= $prioridadClass ?>">
                                            <?= esc($ticket['PRIORIDAD']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($ticket['FECHA_CREACION'])) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('ver_ticket/' . $ticket['ID']) ?>" 
                                               class="btn btn-sm btn-primary" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($ticket['ESTADO'] === 'ABIERTO'): ?>
                                                <a href="<?= base_url('editar_ticket/' . $ticket['ID']) ?>" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Editar ticket">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="mt-3">
                <a href="<?= base_url('inicio_empleado') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Volver al Inicio
                </a>
                <a href="<?= base_url('crear_ticket') ?>" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>
                    Crear Nuevo Ticket
                </a>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endsection(); ?>