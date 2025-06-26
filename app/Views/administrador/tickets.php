<?php echo $this->extend('layouts/app'); ?>

<?php echo $this->section('content'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <?php if (isset($categoria)): ?>
                        <h5 class="mt-2">Categoría: <span class="badge bg-info text-dark"><?= ucfirst($categoria) ?></span></h5>
                    <?php endif; ?>
                </div>
                <div class="card-body bg-light">
                    <h2 class="text-center"><?= esc($titulo ?? 'Listado de Tickets') ?></h2>
                    <p>Total de tickets: <strong><?= esc($totalTickets) ?></strong></p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-dark">
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
                                <?php if (!empty($tickets)): ?>
                                    <?php foreach ($tickets as $ticket): ?>
                                        <?php
                                            $esResuelto = isset($titulo) && ($titulo === 'Tickets Resueltos' || $titulo === 'Tickets Resueltos Hoy');
                                            $esActivo = strtoupper($ticket['ESTADO']) === 'ABIERTO' || strtoupper($ticket['ESTADO']) === 'EN_PROCESO';
                                        ?>
                                        <?php if (($esResuelto && strtoupper($ticket['ESTADO']) === 'CERRADO') || (!$esResuelto && $esActivo)): ?>
                                            <tr>
                                                <td><?= esc($ticket['ID']) ?></td>
                                                <td><?= esc($ticket['TITULO']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= strtoupper($ticket['ESTADO']) === 'ABIERTO' ? 'success' : (strtoupper($ticket['ESTADO']) === 'EN_PROCESO' ? 'warning' : 'secondary') ?>">
                                                        <?= esc($ticket['ESTADO']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $ticket['PRIORIDAD'] === 'Alta' ? 'danger' : ($ticket['PRIORIDAD'] === 'Media' ? 'warning' : 'secondary') ?>">
                                                        <?= esc($ticket['PRIORIDAD']) ?>
                                                    </span>
                                                </td>
                                                <td><?= esc($ticket['FECHA_CREACION']) ?></td>
                                                <td>
                                                    <a href="<?= base_url('ver_tickets/' . $ticket['ID']) ?>" class="btn btn-sm btn-primary me-1">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                    <!-- <a href="<?= base_url('editar_tickets/' . $ticket['ID']) ?>" class="btn btn-sm btn-secondary me-1">
                                                        <i class="fas fa-edit"></i> Finalizado
                                                    </a> -->
                                                    <!-- <a href="<?= base_url('eliminar_ticket/' . $ticket['ID']) ?>" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash-alt"></i> Eliminar
                                                    </a> -->
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
                    <a href="<?= base_url('inicio_admin') ?>" class="btn btn-success mt-4">
                        <i class="bi bi-arrow-left"></i> Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endsection(); ?>