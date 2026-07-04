<?php
/**
 * @var int $id
 * @var array $data
 * @var string|null $error
 */
?>
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <a href="<?= url('proveedor/index') ?>" class="btn btn-secondary btn-sm"><i class='bx bx-left-arrow-alt'></i> Volver al directorio</a>
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--text-dark); margin: 0;"><i class='bx bx-edit'></i> Editar Proveedor #<?= e($id) ?></h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Actualiza la información comercial o de contacto.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <i class='bx bx-error'></i> <?= e($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="<?= url('proveedor/edit/' . $id) ?>" method="POST" class="needs-validation" novalidate>
            <div class="form-grid">
                <div class="form-group">
                    <label for="ruc" class="form-label">RUC / NIT <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="ruc" name="ruc" class="form-control" value="<?= e($data['ruc'] ?? '') ?>" required data-validate="ruc" maxlength="13">
                </div>

                <div class="form-group">
                    <label for="nombre_empresa" class="form-label">Razón Social <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="nombre_empresa" name="nombre_empresa" class="form-control" value="<?= e($data['nombre_empresa'] ?? '') ?>" required data-validate="required">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="contacto_persona" class="form-label">Persona de Contacto</label>
                    <input type="text" id="contacto_persona" name="contacto_persona" class="form-control" value="<?= e($data['contacto_persona'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="telefono" class="form-label">Teléfono / Celular <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="telefono" name="telefono" class="form-control" value="<?= e($data['telefono'] ?? '') ?>" required data-validate="required">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="email" class="form-label">Correo Electrónico <span style="color: var(--danger);">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= e($data['email'] ?? '') ?>" required data-type="email">
                </div>

                <div class="form-group">
                    <label class="form-label">Estado de la Cuenta</label>
                    <div style="background: var(--bg-main); padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); display: flex; align-items: center; gap: 0.75rem;">
                        <input type="checkbox" id="estado" name="estado" value="1" <?= (!isset($data['estado']) || $data['estado'] == 1) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--primary);">
                        <label for="estado" style="color: var(--text-main); font-weight: 500; cursor: pointer;">Proveedor Activo</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="direccion" class="form-label">Dirección Fiscal</label>
                <textarea id="direccion" name="direccion" class="form-control" rows="2"><?= e($data['direccion'] ?? '') ?></textarea>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="<?= url('proveedor/index') ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-refresh'></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
