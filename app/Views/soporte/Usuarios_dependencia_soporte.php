<?php echo $this->extend('layouts/app_soporte'); ?>
<?php echo $this->section('content'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-info text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Usuarios de la dependencia: <span class="fw-bold"><?= esc($dependencia) ?></span>
                    </h3>
                </div>
                <div class="card-body bg-light">
                    <?php if (empty($usuarios)): ?>
                        <div class="alert alert-warning text-center mb-4">
                            No hay usuarios registrados en esta dependencia.
                        </div>
                    <?php else: ?>
                        <ul class="list-group mb-3">
                            <?php foreach ($usuarios as $usuario): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-user-circle me-2 text-primary"></i>
                                        <strong><?= esc($usuario['NOMBRE']) ?></strong>
                                    </div>
                                    <span class="text-muted"><?= esc($usuario['EMAIL']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <a href="<?= base_url('/dependencias_soporte') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Dependencias
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endsection(); ?>
