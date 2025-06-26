<?php echo $this->extend('layouts/app_soporte'); ?>

<?php echo $this->section('content'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h2 class="mb-0">Listado de Tickets Soporte</h2>
                    <?php if (isset($categoria)): ?>
                        <h5 class="mt-2">Categoría: <span class="badge bg-info text-dark"><?= ucfirst($categoria) ?></span></h5>
                    <?php endif; ?>
                </div>
                <div class="card-body bg-light">
                    <?php if (isset($titulo)): ?>
                        <div class="alert alert-info text-center mb-4">
                            <strong><?= esc($titulo) ?> (<?= esc($totalTickets) ?>)</strong>
                        </div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Asunto</th>
                                    <th>Estado</th>
                                    <th>Prioridad</th>
                                    <th>Fecha de creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tickets)): ?>
                                    <?php foreach ($tickets as $ticket): ?>
                                        <?php
                                        $esResuelto = isset($titulo) && ($titulo === 'Tickets Resueltos' || $titulo === 'Tickets Resueltos Hoy');
                                        $esActivo = strtoupper($ticket['ESTADO']) === 'ABIERTO' || strtoupper($ticket['ESTADO']) === 'EN_PROCESO';
                                        // Badge color para estado
                                        $estado = strtoupper($ticket['ESTADO']);
                                        $badgeEstado = $estado === 'ABIERTO' ? 'success' : ($estado === 'EN_PROCESO' ? 'warning' : 'secondary');
                                        // Badge color para prioridad
                                        $prioridad = strtoupper($ticket['PRIORIDAD']);
                                        $badgePrioridad = $prioridad === 'ALTA' ? 'danger' : ($prioridad === 'MEDIA' ? 'warning' : ($prioridad === 'CRITICA' ? 'purple' : 'info'));
                                        ?>
                                        <?php if (($esResuelto && $estado === 'CERRADO') || (!$esResuelto && $esActivo)): ?>
                                            <tr>
                                                <td><?= esc($ticket['ID']) ?></td>
                                                <td><?= esc($ticket['TITULO']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $badgeEstado ?>">
                                                        <?= esc(ucfirst(strtolower($ticket['ESTADO']))) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $badgePrioridad ?>">
                                                        <?= esc(ucfirst(strtolower($ticket['PRIORIDAD']))) ?>
                                                    </span>
                                                </td>
                                                <td><?= esc($ticket['FECHA_CREACION']) ?></td>
                                                <td>
                                                    <a href="<?= base_url('ver_datalles_ticket/' . $ticket['ID']) ?>" class="btn btn-sm btn-primary me-1">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No hay tickets en esta categoría.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="<?= base_url('inicio_soporte') ?>" class="btn btn-success mt-4">
                        <i class="bi bi-arrow-left"></i> Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endsection(); ?>