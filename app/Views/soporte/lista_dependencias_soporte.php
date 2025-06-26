<?php echo $this->extend('layouts/app_soporte'); ?>
<?php echo $this->section('content'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0"><i class="fas fa-building me-2"></i>Dependencias de la Empresa</h3>
                </div>
                <div class="card-body bg-light">
                    <ul class="list-group list-group-flush mb-3">
                        <?php foreach ($dependencias as $dep): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="<?= base_url('dependencias_soporte/usuarios/' . $dep['ID']) ?>" class="text-decoration-none fw-semibold text-primary">
                                    <i class="fas fa-sitemap me-2"></i><?= esc($dep['NOMBRE']) ?>
                                </a>
                                <span class="badge bg-secondary rounded-pill">Ver usuarios</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?= base_url('inicio_soporte') ?>" class="btn btn-success">
                        <i class="bi bi-arrow-left"></i> Volver al Panel Principal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endSection(); ?> 