<?php
/**
 * @var array $data
 * @var object[] $categorias
 * @var string|null $error
 */
?>
<div style="max-width: 850px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <a href="<?= url('producto/index') ?>" class="btn btn-secondary btn-sm"><i class='bx bx-left-arrow-alt'></i> Volver al catálogo</a>
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--text-dark); margin: 0;"><i class='bx bx-plus'></i> Registrar Nuevo Producto</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Ingresa un nuevo artículo para habilitar el control de existencias y valorización.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <i class='bx bx-error'></i> <?= e($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="<?= url('producto/create') ?>" method="POST" class="needs-validation" novalidate>
            <!-- 1. Identificación y Clasificación -->
            <h3 style="font-size: 1.1rem; color: var(--primary); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                <i class='bx bx-purchase-tag-alt'></i> Identificación del Artículo
            </h3>

            <div class="form-grid">
                <div class="form-group">
                    <label for="codigo" class="form-label">Código SKU / Barras <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="codigo" name="codigo" class="form-control" value="<?= e($data['codigo'] ?? '') ?>" placeholder="Ej. LAP-ASUS-G15, SKU-0001" required data-validate="required" style="text-transform: uppercase;">
                    <div class="form-text">Debe ser un código único e irrepetible.</div>
                </div>

                <div class="form-group">
                    <label for="categoria_id" class="form-label">Categoría del Catálogo <span style="color: var(--danger);">*</span></label>
                    <select id="categoria_id" name="categoria_id" class="form-select" required data-validate="required">
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat->id ?>" <?= (($data['categoria_id'] ?? '') == $cat->id) ? 'selected' : '' ?>>
                                <?= e($cat->nombre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="nombre" class="form-label">Nombre del Producto / Equipo <span style="color: var(--danger);">*</span></label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($data['nombre'] ?? '') ?>" placeholder="Ej. Laptop ASUS ROG Zephyrus G15 - 32GB RAM" required data-validate="required">
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label">Especificaciones o Especificación Técnica</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="2" placeholder="Detalles de procesador, pantalla, garantía, etc..."><?= e($data['descripcion'] ?? '') ?></textarea>
            </div>

            <!-- 2. Precios y Finanzas -->
            <h3 style="font-size: 1.1rem; color: var(--success); margin: 1.5rem 0 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                <i class='bx bx-money'></i> Valorización y Costos ($)
            </h3>

            <div class="form-grid">
                <div class="form-group">
                    <label for="precio_compra" class="form-label">Precio de Compra (Costo al Proveedor) <span style="color: var(--danger);">*</span></label>
                    <input type="number" id="precio_compra" name="precio_compra" class="form-control" step="0.01" min="0" value="<?= e($data['precio_compra'] ?? '0.00') ?>" required data-type="number">
                </div>

                <div class="form-group">
                    <label for="precio_venta" class="form-label">Precio de Venta al Público <span style="color: var(--danger);">*</span></label>
                    <input type="number" id="precio_venta" name="precio_venta" class="form-control" step="0.01" min="0" value="<?= e($data['precio_venta'] ?? '0.00') ?>" required data-type="number" style="font-weight: 700; color: var(--success);">
                </div>
            </div>

            <!-- 3. Parámetros de Almacén y Stock -->
            <h3 style="font-size: 1.1rem; color: var(--info); margin: 1.5rem 0 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                <i class='bx bx-package'></i> Parámetros de Stock y Alerta
            </h3>

            <div class="form-grid">
                <div class="form-group">
                    <label for="stock" class="form-label">Stock Inicial Físico <span style="color: var(--danger);">*</span></label>
                    <input type="number" id="stock" name="stock" class="form-control" min="0" value="<?= e($data['stock'] ?? '0') ?>" required data-type="number">
                    <div class="form-text">Cantidad actual disponible en mostrador/almacén.</div>
                </div>

                <div class="form-group">
                    <label for="stock_minimo" class="form-label">Stock Mínimo para Alerta Roja <span style="color: var(--danger);">*</span></label>
                    <input type="number" id="stock_minimo" name="stock_minimo" class="form-control" min="1" value="<?= e($data['stock_minimo'] ?? '5') ?>" required data-type="number">
                    <div class="form-text">Si el stock cae a este valor, se activará la alerta crítica.</div>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                    <input type="text" id="unidad_medida" name="unidad_medida" class="form-control" value="<?= e($data['unidad_medida'] ?? 'Unidad') ?>" placeholder="Unidad, Caja, Pieza, Kg, Litro">
                </div>

                <div class="form-group">
                    <label class="form-label">Estado del Artículo</label>
                    <div style="background: var(--bg-main); padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); display: flex; align-items: center; gap: 0.75rem;">
                        <input type="checkbox" id="estado" name="estado" value="1" <?= (!isset($data['estado']) || $data['estado'] == 1) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--primary);">
                        <label for="estado" style="color: var(--text-main); font-weight: 500; cursor: pointer;">Producto Activo para Venta/Compra</label>
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="<?= url('producto/index') ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.75rem;">
                    <i class='bx bx-save'></i> Guardar y Activar Producto
                </button>
            </div>
        </form>
    </div>
</div>
