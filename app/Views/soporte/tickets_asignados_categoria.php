<?php echo $this->extend('layouts/app_soporte') ?>
<?php echo $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-info text-white rounded-top-4 border-0 d-flex align-items-center justify-content-between">
                    <h3 class="mb-0"><i class="fas fa-tags me-2"></i>Tickets asignados por categoría</h3>
                    <span class="badge bg-light text-info fs-6"><?= count($tickets) ?> tickets</span>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($tickets)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Estado</th>
                                        <th>Prioridad</th>
                                        <th>Fecha de creación</th>
                                        <th>Categoría</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $ticket): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary"><?= esc($ticket['ID']) ?></span>
                                            </td>
                                            <td>
                                                <strong><?= esc($ticket['TITULO']) ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                    $estado = esc($ticket['ESTADO']);
                                                    $estadoClass = 'secondary';
                                                    if ($estado === 'ABIERTO') $estadoClass = 'success';
                                                    elseif ($estado === 'EN PROCESO') $estadoClass = 'warning';
                                                    elseif ($estado === 'CERRADO') $estadoClass = 'danger';
                                                ?>
                                                <span class="badge bg-<?= $estadoClass ?>"><?= $estado ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                    $prioridad = esc($ticket['PRIORIDAD']);
                                                    $prioridadClass = 'secondary';
                                                    if ($prioridad === 'ALTA') $prioridadClass = 'danger';
                                                    elseif ($prioridad === 'MEDIA') $prioridadClass = 'warning';
                                                    elseif ($prioridad === 'BAJA') $prioridadClass = 'info';
                                                ?>
                                                <span class="badge bg-<?= $prioridadClass ?>"><?= $prioridad ?></span>
                                            </td>
                                            <td>
                                                <i class="far fa-calendar-alt me-1 text-info"></i>
                                                <?= esc($ticket['FECHA_CREACION']) ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?= esc($ticket['CATEGORIA_NOMBRE']) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('ver_datalles_ticket/' . $ticket['ID']) ?>" class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-eye"></i> Ver ticket
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center mb-0">
                            <i class="fas fa-info-circle me-2"></i>No hay tickets asignados a tus categorías.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>