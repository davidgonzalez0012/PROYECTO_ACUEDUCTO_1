<?php echo $this->extend('layouts/app_soporte'); ?>

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
                                <h2 class="mb-0 d-flex align-items-center gap-2">
                                    <i class="fas fa-ticket-alt"></i>
                                    Detalles del Ticket
                                </h2>
                            </div>
                            <div class="card-body p-4">
                                <?php if (isset($ticket)): ?>
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <p class="fs-5 mb-1"><strong class="text-primary">Ticket #<?= $ticket['ID'] ?></strong></p>
                                            <p class="mb-1"><strong>Título:</strong> <span class="text-dark-emphasis"><?= esc($ticket['TITULO']) ?></span></p>
                                            <p class="mb-1"><strong>Estado:</strong>
                                                <?php
                                                    $estadoClass = match($ticket['ESTADO']) {
                                                        'ABIERTO' => 'bg-success',
                                                        'EN_PROCESO' => 'bg-warning text-dark',
                                                        'CERRADO' => 'bg-secondary',
                                                        default => 'bg-info'
                                                    };
                                                ?>
                                                <span class="badge <?= $estadoClass ?> px-3 py-2 fs-6"> <?= esc($ticket['ESTADO']) ?> </span>
                                                <!-- Formulario para actualizar el estado del ticket (SOLO SOPORTE) -->
                                                <form action="<?= base_url('soporte/actualizar_estado_ticket/' . $ticket['ID']) ?>" method="post" class="row g-2 align-items-end mb-3 mt-2">
                                                    <div class="col-auto">
                                                        <label for="estado" class="form-label mb-1"><strong>Cambiar Estado:</strong></label>
                                                        <select name="ESTADO" id="estado" class="form-select">
                                                            <option value="ABIERTO" <?= $ticket['ESTADO'] == 'ABIERTO' ? 'selected' : '' ?>>ABIERTO</option>
                                                            <option value="EN_PROCESO" <?= $ticket['ESTADO'] == 'EN_PROCESO' ? 'selected' : '' ?>>EN PROCESO</option>
                                                            <option value="CERRADO" <?= $ticket['ESTADO'] == 'CERRADO' ? 'selected' : '' ?>>CERRADO</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Actualizar Estado</button>
                                                    </div>
                                                </form>
                                            </p>
                                            <p class="mb-1"><strong>Prioridad:</strong>
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
                                            <p class="mb-1"><strong>Categoría:</strong> <span class="text-dark-emphasis"><?= esc($ticket['CATEGORIA_NOMBRE']) ?></span></p>
                                            <p class="mb-1"><strong>Fecha de Creación:</strong>
                                                <?php if (!empty($ticket['FECHA_CREACION'])): ?>
                                                    <?= date('d/m/Y H:i', strtotime($ticket['FECHA_CREACION'])) ?>
                                                <?php else: ?>
                                                    Sin fecha
                                                <?php endif; ?>
                                            </p>
                                            <div class="mt-3 p-3 rounded-3 bg-light border">
                                                <strong>Descripción:</strong><br> <span class="text-dark-emphasis"><?= esc($ticket['DESCRIPCION']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        No se encontró información del ticket.
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-end mt-4">
                                    <a href="<?= base_url('tickets/todos-tickets_soporte') ?>" class="btn btn-outline-secondary px-4">
                                        <i class="fas fa-arrow-left me-2"></i>Volver a tickets
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Columna Derecha: Chat de Comentarios -->
                    <div class="col-md-5" style="flex: 0 0 40%; max-width: 40%;">
                        <div class="card shadow-lg border-0 rounded-4 h-100 d-flex flex-column">
                            <div class="card-header bg-secondary text-white rounded-top-4 border-0">
                                <h5 class="mb-0">Historial de Comentarios</h5>
                            </div>
                            <div class="card-body p-3 d-flex flex-column" style="height: 500px; overflow-y: auto;">
                                <?php if (isset($comentarios) && count($comentarios) > 0): ?>
                                    <div class="d-flex flex-column gap-3 flex-grow-1 mb-3">
                                        <?php foreach ($comentarios as $i => $comentario): ?>
                                            <div class="card border-0 shadow-sm rounded-3 align-self-start" style="max-width: 90%;">
                                                <div class="card-body d-flex align-items-start gap-3">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                                        <?= strtoupper(mb_substr($comentario['USUARIO_NOMBRE'], 0, 1)) ?>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <strong><?= ($i+1) . '. ' . esc($comentario['USUARIO_NOMBRE']) ?></strong>
                                                            <small class="text-muted">
                                                                <?php if (!empty($comentario['CREATED_AT'])): ?>
                                                                    <?= date('d/m/Y H:i', strtotime($comentario['CREATED_AT'])) ?>
                                                                <?php else: ?>
                                                                    Sin fecha
                                                                <?php endif; ?>
                                                            </small>
                                                        </div>
                                                        <div class="mt-1"><?= esc($comentario['CONTENIDO']) ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info text-center flex-grow-1">No hay comentarios aún.</div>
                                <?php endif; ?>
                                <!-- Formulario de respuesta tipo chat -->
                                <form action="<?= base_url('tickets/comentar_Soporte/' . $ticket['ID']) ?>" method="post" class="mt-auto">
                                    <div class="input-group">
                                        <textarea name="comentario" class="form-control" rows="1" placeholder="Escribe tu respuesta o comentario..." required style="resize:none;"></textarea>
                                        <button type="submit" class="btn btn-primary">Enviar</button>
                                    </div>
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