<?php echo $this->extend('layouts/app'); ?>

<?php echo $this->section('content'); ?>
// dd($dependencias); ?>
<section id="crear" class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-7">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-primary text-white rounded-top-4 border-0">
                        <h2 class="mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-ticket-alt"></i>
                            Crear Nuevo Ticket
                        </h2>
                    </div>
                    <div class="card-body p-4">
                        <form method="post" action="<?= site_url('tickets/guardar') ?>" enctype="multipart/form-data">
                            <div class="row g-4">
                                <div class="mb-3 col-md-6">
                                    <label for="categoria" class="form-label fw-bold">Categoría</label>
                                    <select id="categoria" name="CATEGORIA_ID" class="form-select" onchange="actualizarSubcategorias()">
                                        <option value="1">Hardware</option>
                                        <option value="2">Software</option>
                                        <option value="3">Red</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="subcategoria" class="form-label fw-bold">Subcategoría</label>
                                    <select id="subcategoria" name="SUBCATEGORIA_ID" class="form-select"></select>
                                </div>
                                <div class="mb-3">
                                    <label for="dependencia" class="form-label fw-bold">Dependencia</label>
                                    <select name="DEPENDENCIA_ID" id="DEPENDENCIA_ID" class="form-select" required>
                                        <option value="">Seleccione una dependencia</option>
                                        <?php if (isset($dependencias) && is_array($dependencias)): ?>
                                            <?php foreach($dependencias as $dep): ?>
                                                <option value="<?= esc($dep['ID']) ?>">
                                                    <?= esc($dep['NOMBRE']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="titulo" class="form-label fw-bold">Título</label>
                                    <input type="text" id="titulo" name="TITULO" class="form-control" placeholder="Breve descripción del problema" required>
                                </div>
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label fw-bold">Descripción detallada</label>
                                    <textarea id="descripcion" name="DESCRIPCION" class="form-control" rows="5" required></textarea>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="prioridad" class="form-label fw-bold">Prioridad</label>
                                    <select id="prioridad" name="PRIORIDAD" class="form-select">
                                        <option value="BAJA">Baja</option>
                                        <option value="MEDIA">Media</option>
                                        <option value="ALTA">Alta</option>
                                        <option value="CRITICA">Crítica</option>
                                    </select>
                                </div>
                                <input type="hidden" name="ESTADO" value="ABIERTO">
                                <div class="mb-3 col-md-6">
                                    <label for="archivo" class="form-label fw-bold">Archivo adjunto</label>
                                    <input class="form-control" type="file" id="archivo" name="archivo">
                                </div>
                                <div class="d-flex gap-2 mt-4 justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                        <i class="fas fa-paper-plane me-2"></i>Enviar Ticket
                                    </button>
                                    <a class="btn btn-outline-secondary px-4" href="inicio_admin">
                                        <i class="fas fa-arrow-left me-2"></i>Volver
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const subcategorias = <?= json_encode($subcategorias ?? []) ?>;

function actualizarSubcategorias() {
    const categoria = document.getElementById('categoria').value;
    const subcategoriaSelect = document.getElementById('subcategoria');
    subcategoriaSelect.innerHTML = '';
    if (subcategorias[categoria]) {
        subcategorias[categoria].forEach(function(subcat) {
            let option = document.createElement('option');
            option.value = subcat.id;
            option.text = subcat.nombre;
            subcategoriaSelect.appendChild(option);
        });
    }
}

// subcategorías al cargar la página
window.onload = actualizarSubcategorias;
</script>

<?php echo $this->endSection(); ?>