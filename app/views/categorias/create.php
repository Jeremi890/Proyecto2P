<div style="max-width: 700px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <a href="<?= url('categoria/index') ?>" class="btn btn-secondary btn-sm"><i class='bx bx-left-arrow-alt'></i> Volver al listado</a>
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--text-dark); margin: 0;"><i class='bx bx-plus'></i> Crear Nueva Categoría</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Agrega una nueva agrupación para tus productos.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <i class='bx bx-error'></i> <?= e($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="<?= url('categoria/create') ?>" method="POST" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="nombre" class="form-label">Nombre de la Categoría <span style="color: var(--danger);">*</span></label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($data['nombre'] ?? '') ?>" placeholder="Ej. Laptops y Computadoras, Tarjetas Gráficas, etc." required data-validate="required">
                <div class="form-text">Nombre único para identificar esta familia en el almacén.</div>
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción o Notas Adicionales</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Describe brevemente qué tipo de artículos se agrupan en esta categoría..."><?= e($data['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Estado Inicial en el Sistema</label>
                <div style="background: var(--bg-main); padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); display: flex; align-items: center; gap: 0.75rem;">
                    <input type="checkbox" id="estado" name="estado" value="1" <?= (!isset($data['estado']) || $data['estado'] == 1) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <label for="estado" style="color: var(--text-main); font-weight: 500; cursor: pointer;">Categoría Activa y Visible</label>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="<?= url('categoria/index') ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-save'></i> Guardar Categoría
                </button>
            </div>
        </form>
    </div>
</div>
