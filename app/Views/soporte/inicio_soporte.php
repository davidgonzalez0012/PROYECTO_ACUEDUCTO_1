<?php echo $this->extend('layouts/app_soporte'); ?>

<?php echo $this->section('content'); ?>

<!-- Dashboard -->
<div id="dashboard" class="page active">
    <div class="row mb-4">
        <div class="col">
            <h2 class="text-white mb-4">
                <i class="fas fa-chart-pie me-2"></i>
                Panel de Control
            </h2>
        </div>
    </div>
    <!-- Stats Cards -->
    <div class="row justify-content-center g-4 mb-4">
        <!-- Tickets Activos -->
        <div class="col-12 col-md-3">
            <div class="card glass-card border-0 h-100">
                <a href="<?= base_url('tickets/todos-tickets_soporte') ?>" class="text-decoration-none">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-ticket-alt fa-2x text-primary mb-3"></i>
                        <h3 class="fw-bold mb-2"><?= isset($totalTickets) ? esc($totalTickets) : '0' ?></h3>
                        <p class="text-muted mb-0">Tickets Activos</p>
                    </div>
                </a>
            </div>
        </div>
        <!-- Resueltos Hoy -->
        <div class="col-12 col-md-3">
            <div class="card glass-card border-0 h-100">
                <a href="<?= base_url('tickets/resueltoshoy/soporte') ?>" class="text-decoration-none">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                        <h3 class="fw-bold mb-2"><?= isset($resueltosHoy) ? esc($resueltosHoy) : '0' ?></h3>
                        <p class="text-muted mb-0">Resueltos Hoy</p>
                    </div>
                </a>
            </div>
        </div>
        <!-- Tickets Cerrados (nuevo) -->
        <div class="col-12 col-md-3">
            <div class="card glass-card border-0 h-100">
                <a href="<?= base_url('tickets/resueltos/soporte') ?>" class="text-decoration-none">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-archive fa-2x text-secondary mb-3"></i>
                        <h3 class="fw-bold mb-2"><?= isset($cerradosTotal) ? esc($cerradosTotal) : '0' ?></h3>
                        <p class="text-muted mb-0">Tickets Cerrados</p>
                    </div>
                </a>
            </div>
        </div>
        <!-- Alta Prioridad (solo abiertos) -->
        <div class="col-12 col-md-3">
            <div class="card glass-card border-0 h-100">
                <a href="<?= base_url('tickets/alta_prioridad/soporte') ?>" class="text-decoration-none">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                        <h3 class="fw-bold mb-2"><?= isset($altaPrioridad) ? esc($altaPrioridad) : '0' ?></h3>
                        <p class="text-muted mb-0">Alta Prioridad (Abiertos)</p>
                    </div>
                </a>
            </div>
        </div>
        <!-- Tickets Asignados por Categoría -->
        <div class="col-12 col-md-3">
            <div class="card glass-card border-0 h-100">
                <a href="<?= base_url('tickets/asignados_categoria/soporte') ?>" class="text-decoration-none">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-user-tag fa-2x text-info mb-3"></i>
                        <h3 class="fw-bold mb-2"><?= isset($asignadosCategoria) ? esc($asignadosCategoria) : '0' ?></h3>
                        <p class="text-muted mb-0"> Tus Tickets Asignados por Categoría</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <!-- Recent Tickets -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card glass-card border-0">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Tickets Recientes
                    </h5>
                    <a href="<?= base_url('tickets/todos-tickets_soporte') ?>" class="btn btn-sm btn-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Título</th>
                                    <th>Solicitante</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recientes)): ?>
                                    <?php foreach ($recientes as $ticket): ?>
                                        <?php if (strtoupper($ticket['ESTADO']) === 'ABIERTO' || strtoupper($ticket['ESTADO']) === 'EN_PROCESO'): ?>
                                            <tr>
                                                <td>#<?= esc($ticket['ID']) ?></td>
                                                <td><?= esc($ticket['TITULO']) ?></td>
                                                <td><?= esc($ticket['SOLICITANTE']) ?></td>
                                                <td>
                                                    <span class="badge <?= $ticket['PRIORIDAD'] === 'Alta' ? 'bg-danger' : ($ticket['PRIORIDAD'] === 'Media' ? 'bg-warning' : 'bg-info') ?>">
                                                        <?= esc($ticket['PRIORIDAD']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $ticket['ESTADO'] === 'Resuelto' ? 'success' : ($ticket['ESTADO'] === 'En Proceso' ? 'warning' : 'primary') ?>">
                                                        <?= esc($ticket['ESTADO']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No hay tickets recientes.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card glass-card border-0">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Tickets por Categoría
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Hardware</span>
                            <span class="badge bg-primary"><?= esc($hardwarePct_soporte) ?>%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: <?= $hardwarePct_soporte ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Software</span>
                            <span class="badge bg-success"><?= esc($softwarePct_soporte) ?>%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: <?= $softwarePct_soporte ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Redes</span>
                            <span class="badge bg-warning"><?= esc($redesPct_soporte) ?>%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: <?= $redesPct_soporte ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endSection(); ?>