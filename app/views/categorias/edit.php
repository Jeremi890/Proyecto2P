<?php
/**
 * @var int $id
 * @var array $data
 * @var string|null $error
 */
?>
<div style="max-width: 700px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <a href="<?= url('categoria/index') ?>" class="btn btn-secondary btn-sm"><i class='bx bx-left-arrow-alt'></i> Volver al listado</a>
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--text-dark); margin: 0;"><i class='bx bx-edit'></i> Editar Categoría #<?= e($id) ?></h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Modifica los detalles o el estado de esta agrupación.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <i class='bx bx-error'></i> <?= e($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="<?= url('categoria/edit/' . $id) ?>" method="POST" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="nombre" class="form-label">Nombre de la Categoría <span style="color: var(--danger);">*</span></label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($data['nombre'] ?? '') ?>" required data-validate="required">
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="3"><?= e($data['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Estado de la Categoría</label>
                <div style="background: var(--bg-main); padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); display: flex; align-items: center; gap: 0.75rem;">
                    <input type="checkbox" id="estado" name="estado" value="1" <?= (!isset($data['estado']) || $data['estado'] == 1) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <label for="estado" style="color: var(--text-main); font-weight: 500; cursor: pointer;">Categoría Activa</label>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="<?= url('categoria/index') ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-refresh'></i> Actualizar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
