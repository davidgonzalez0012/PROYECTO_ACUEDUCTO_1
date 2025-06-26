<?php echo $this->extend('layouts/app'); ?>

<?php echo $this->section('content'); ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-primary text-white text-center">
                    <h2 class="h4 mb-0">Detalles del Ticket</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($ticket)): ?>
                        <div class="row g-4 mb-3">
                            <div class="col-12 col-md-6">
                                <p><strong>Título:</strong> <?= $ticket['TITULO'] ?></p>
                                <p><strong>Estado:</strong> 
                                    <span class="badge <?= $ticket['ESTADO'] == 'ABIERTO' ? 'bg-success' : ($ticket['ESTADO'] == 'EN_PROCESO' ? 'bg-warning text-dark' : 'bg-danger') ?>">
                                        <?= $ticket['ESTADO'] == 'ABIERTO' ? 'Abierto' : ($ticket['ESTADO'] == 'EN_PROCESO' ? 'En Proceso' : 'Cerrado') ?>
                                    </span>
                                </p>
                                <p><strong>Prioridad:</strong> 
                                    <span class="badge <?= $ticket['PRIORIDAD'] == 'ALTA' ? 'bg-danger' : ($ticket['PRIORIDAD'] == 'MEDIA' ? 'bg-warning text-dark' : 'bg-info text-dark') ?>">
                                        <?= ucfirst(strtolower($ticket['PRIORIDAD'])) ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-12 col-md-6">
                                <p><strong>Categoría:</strong> <?= $ticket['CATEGORIA_NOMBRE'] ?></p>
                                <p><strong>Fecha de Creación:</strong> <?= $ticket['FECHA_CREACION'] ?></p>
                                <p><strong>Descripción:</strong> <?= $ticket['DESCRIPCION'] ?></p>
                            </div>
                        </div>
                        <!-- Formulario para actualizar el estado del ticket -->
                        <form action="<?= base_url('administrador/actualizar_estado_ticket/' . $ticket['ID']) ?>" method="post" class="row g-2 align-items-end mb-3">
                            <div class="col-12 col-md-6">
                                <label for="estado" class="form-label mb-1"><strong>Cambiar Estado:</strong></label>
                                <select name="ESTADO" id="estado" class="form-select">
                                    <option value="ABIERTO" <?= $ticket['ESTADO'] == 'ABIERTO' ? 'selected' : '' ?>>ABIERTO</option>
                                    <option value="EN_PROCESO" <?= $ticket['ESTADO'] == 'EN_PROCESO' ? 'selected' : '' ?>>EN PROCESO</option>
                                    <option value="CERRADO" <?= $ticket['ESTADO'] == 'CERRADO' ? 'selected' : '' ?>>FINALIZADO</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-warning w-100"><i class="fas fa-sync-alt"></i> Actualizar Estado</button>
                            </div>
                        </form>
                        <?php
                           
                            $urlVolver = base_url('tickets/todos');
                            if (isset($ticket['CATEGORIA_ID'])) {
                                if ($ticket['CATEGORIA_ID'] == 1) {
                                    $urlVolver = base_url('tickets_categoria/1');
                                } elseif ($ticket['CATEGORIA_ID'] == 2) {
                                    $urlVolver = base_url('tickets_categoria/2');
                                } elseif ($ticket['CATEGORIA_ID'] == 3) {
                                    $urlVolver = base_url('tickets_categoria/3');
                                }
                            }
                        ?>
                        <div class="d-flex justify-content-end">
                            <a href="<?= $urlVolver ?>" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">
                            No se encontró información del ticket.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endSection(); ?>