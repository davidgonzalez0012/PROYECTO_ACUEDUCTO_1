<?php echo $this->extend('layouts/app_empleado'); ?>

<?php echo $this->section('content'); ?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="row g-4">
                    <!-- Columna Izquierda: Detalles del Ticket -->
                    <div class="col-md-7" style="flex: 0 0 60%; max-width: 60%;">
                        <div class="card shadow-lg border-0 rounded-4 mb-4 h-100" style="background: #f8fafc; border-left: 6px solid #0d6efd;">
                            <div class="card-header bg-primary text-white rounded-top-4 border-0">
                                <h2 class="mb-0 d-flex align-items-center gap-2" style="font-size:2rem;">
                                    <i class="fas fa-ticket-alt"></i>
                                    Detalles del Ticket
                                </h2>
                            </div>
                            <div class="card-body p-4">
                                <?php if (isset($ticket)): ?>
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <p class="fs-5 mb-1"><strong class="text-primary">Ticket #<?= $ticket['ID'] ?></strong></p>
                                            <p class="mb-1" style="font-size:1.2rem;"><strong>Título:</strong> <span class="text-dark-emphasis"> <?= esc($ticket['TITULO']) ?></span></p>
                                            <p class="mb-1" style="font-size:1.2rem;"><strong>Estado:</strong>
                                                <?php
                                                    $estadoClass = match($ticket['ESTADO']) {
                                                        'ABIERTO' => 'bg-success',
                                                        'EN_PROCESO' => 'bg-warning text-dark',
                                                        'CERRADO' => 'bg-secondary',
                                                        default => 'bg-info'
                                                    };
                                                ?>
                                                <span class="badge <?= $estadoClass ?> px-3 py-2 fs-6"> <?= esc($ticket['ESTADO']) ?> </span>
                                            </p>
                                            <p class="mb-1" style="font-size:1.2rem;"><strong>Prioridad:</strong>
                                                <?php
                                                    $prioridadClass = match($ticket['PRIORIDAD']) {
                                                        'CRITICA' => 'bg-danger',
                                                        'ALTA' => 'bg-warning',
                                                        'MEDIA' => 'bg-info',
                                                        'BAJA' => 'bg-success',
                                                        default => 'bg-secondary'
                                                    };
                                                ?>
                                                <span class="badge <?= $prioridadClass ?> px-3 py-2 fs-6"> <?= esc($ticket['PRIORIDAD']) ?> </span>
                                            </p>
                                            <p class="mb-1" style="font-size:1.2rem;"><strong>Categoría:</strong> <span class="text-dark-emphasis"> <?= esc($ticket['CATEGORIA_NOMBRE']) ?></span></p>
                                            <p class="mb-1" style="font-size:1.2rem;"><strong>Fecha de Creación:</strong>
                                                <?php
                                                    $fecha = isset($ticket['FECHA_CREACION']) ? $ticket['FECHA_CREACION'] : null;
                                                    if ($fecha && strtotime($fecha) > 0) {
                                                        echo date('d/m/Y H:i', strtotime($fecha));
                                                    } else {
                                                        echo 'Fecha no disponible';
                                                    }
                                                ?>
                                            </p>
                                            <div class="mt-3 p-3 rounded-3 bg-light border" style="font-size:1.1rem;">
                                                <strong>Descripción:</strong><br> <span class="text-dark-emphasis"> <?= esc($ticket['DESCRIPCION']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        No se encontró información del ticket.
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-end mt-4">
                                    <a href="<?= base_url('mis_tickets') ?>" class="btn btn-outline-secondary px-4">
                                        <i class="fas fa-arrow-left me-2"></i>Volver a mis tickets
                                    </a>
                                    <?php if (isset($ticket)): ?>
                                        <a href="<?= base_url('editar_ticket/' . $ticket['ID']) ?>" class="btn btn-primary px-4 ms-2">
                                            <i class="fas fa-edit me-2"></i>Editar Ticket
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Columna Derecha: Chat de Comentarios -->
                    <div class="col-md-5" style="flex: 0 0 40%; max-width: 40%;">
                        <div class="card mt-0 shadow-lg border-0 rounded-4 h-100 d-flex flex-column">
                            <div class="card-header bg-secondary text-white rounded-top-4 border-0">
                                <h5 class="mb-0">Conversación con Soporte</h5>
                            </div>
                            <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                                <?php if (isset($comentarios) && count($comentarios) > 0): ?>
                                    <?php foreach ($comentarios as $comentario): ?>
                                        <?php if ($comentario['USUARIO_ID'] == session()->get('id')): ?>
                                            <div class="text-end mb-2">
                                                <span class="badge bg-primary">Tú</span>
                                                <div class="alert alert-primary d-inline-block"> <?= esc($comentario['CONTENIDO']) ?> </div>
                                                <small class="text-muted ms-2"> <?= esc($comentario['CREATED_AT']) ?> </small>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-start mb-2">
                                                <span class="badge bg-success"> <?= esc($comentario['USUARIO_NOMBRE'] ?? 'Soporte') ?> </span>
                                                <div class="alert alert-success d-inline-block"> <?= esc($comentario['CONTENIDO']) ?> </div>
                                                <small class="text-muted ms-2"> <?= esc($comentario['CREATED_AT']) ?> </small>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-info text-center">No hay comentarios aún.</div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <form action="<?= base_url('tickets/comentar/' . $ticket['ID']) ?>" method="post" class="d-flex gap-2">
                                    <textarea name="comentario" class="form-control" rows="1" placeholder="Escribe tu comentario..." required style="resize:none;"></textarea>
                                    <button type="submit" class="btn btn-primary">Enviar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php echo $this->endSection(); ?> 