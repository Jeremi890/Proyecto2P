<?php
/**
 * @var array $data
 * @var object[] $productos
 * @var object[] $proveedores
 * @var string|null $error
 */
?>
<div style="max-width: 850px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <a href="<?= url('movimiento/index') ?>" class="btn btn-secondary btn-sm"><i class='bx bx-left-arrow-alt'></i> Volver al historial</a>
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--text-dark); margin: 0;"><i class='bx bx-transfer'></i> Registrar Transacción de Stock</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">El sistema actualizará las existencias automáticamente mediante una Transacción SQL.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <i class='bx bx-error'></i> <?= e($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="<?= url('movimiento/create') ?>" method="POST" class="needs-validation" novalidate id="movimientoForm" enctype="multipart/form-data">
            <!-- 1. Tipo de Operación -->
            <h3 style="font-size: 1.1rem; color: var(--primary); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                <i class='bx bx-cog'></i> Tipo de Operación
            </h3>

            <div class="form-group">
                <label class="form-label">Selecciona el flujo de la mercadería <span style="color: var(--danger);">*</span></label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <label id="lblEntrada" style="background: rgba(16, 185, 129, 0.1); border: 2px solid var(--success); padding: 1rem; border-radius: var(--radius-md); cursor: pointer; display: flex; align-items: center; gap: 0.75rem; transition: all 0.3s;">
                        <input type="radio" name="tipo_movimiento" value="ENTRADA" id="tipoEntrada" <?= (($data['tipo_movimiento'] ?? 'ENTRADA') === 'ENTRADA') ? 'checked' : '' ?> style="width: 20px; height: 20px; accent-color: var(--success);">
                        <div>
                            <div style="font-weight: 700; color: var(--success); font-size: 1.1rem;"><i class='bx bx-down-arrow-alt'></i> ENTRADA (Compra)</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Suma unidades al stock del almacén.</div>
                        </div>
                    </label>

                    <label id="lblSalida" style="background: rgba(139, 92, 246, 0.05); border: 2px solid rgba(139, 92, 246, 0.3); padding: 1rem; border-radius: var(--radius-md); cursor: pointer; display: flex; align-items: center; gap: 0.75rem; transition: all 0.3s;">
                        <input type="radio" name="tipo_movimiento" value="SALIDA" id="tipoSalida" <?= (($data['tipo_movimiento'] ?? '') === 'SALIDA') ? 'checked' : '' ?> style="width: 20px; height: 20px; accent-color: var(--info);">
                        <div>
                            <div style="font-weight: 700; color: var(--info); font-size: 1.1rem;"><i class='bx bx-up-arrow-alt'></i> SALIDA (Venta)</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Resta unidades del stock física.</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 2. Selección del Producto y Proveedor -->
            <h3 style="font-size: 1.1rem; color: var(--success); margin: 1.5rem 0 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                <i class='bx bx-box'></i> Artículo y Socio Comercial
            </h3>

            <div class="form-grid">
                <div class="form-group">
                    <label for="producto_id" class="form-label">Producto del Catálogo <span style="color: var(--danger);">*</span></label>
                    <select id="producto_id" name="producto_id" class="form-select" required data-validate="required" onchange="updateStockPreview()">
                        <?php foreach ($productos as $prod): ?>
                            <option value="<?= $prod->id ?>" data-stock="<?= $prod->stock ?>" data-precio="<?= $prod->precio_compra ?>" <?= (($data['producto_id'] ?? '') == $prod->id || (isset($_GET['prod_id']) && $_GET['prod_id'] == $prod->id)) ? 'selected' : '' ?>>
                                [<?= e($prod->codigo) ?>] <?= e($prod->nombre) ?> (Stock: <?= number_format($prod->stock) ?> <?= e($prod->unidad_medida) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="stockPreview" style="margin-top: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--primary);">
                        <i class='bx bx-bulb'></i> Stock actual disponible en sistema: <span id="valStock">0</span> unidades.
                    </div>
                </div>

                <div class="form-group" id="groupProveedor">
                    <label for="proveedor_id" class="form-label">Proveedor Suministrador</label>
                    <select id="proveedor_id" name="proveedor_id" class="form-select">
                        <option value="">— Ninguno / Retiro General —</option>
                        <?php foreach ($proveedores as $prov): ?>
                            <option value="<?= $prov->id ?>" <?= (($data['proveedor_id'] ?? '') == $prov->id) ? 'selected' : '' ?>>
                                <?= e($prov->nombre_empresa) ?> (RUC: <?= e($prov->ruc) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Recomendado para compras (Entradas).</div>
                </div>
            </div>

            <!-- 3. Cantidad y Costos -->
            <h3 style="font-size: 1.1rem; color: var(--info); margin: 1.5rem 0 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                <i class='bx bx-calculator'></i> Cantidad y Valorización
            </h3>

            <div class="form-grid">
                <div class="form-group">
                    <label for="cantidad" class="form-label">Cantidad a Mover <span style="color: var(--danger);">*</span></label>
                    <input type="number" id="cantidad" name="cantidad" class="form-control" min="1" value="<?= e($data['cantidad'] ?? '1') ?>" required data-type="number" style="font-family: 'JetBrains Mono', monospace; font-size: 1.2rem; font-weight: 700; color: var(--text-main);">
                </div>

                <div class="form-group">
                    <label for="costo_unitario" class="form-label">Costo Unitario de Operación ($)</label>
                    <input type="number" id="costo_unitario" name="costo_unitario" class="form-control" step="0.01" min="0" value="<?= e($data['costo_unitario'] ?? '0.00') ?>" required data-type="number" style="font-family: 'JetBrains Mono', monospace; font-size: 1.1rem;">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="notas" class="form-label">Referencia o Comentarios (Opcional)</label>
                    <input type="text" id="notas" name="notas" class="form-control" value="<?= e($data['notas'] ?? '') ?>" placeholder="Ej. Factura de compra #0045, Retiro para sucursal norte...">
                </div>

                <div class="form-group">
                    <label for="comprobante" class="form-label"><i class='bx bx-paperclip'></i> Adjuntar Comprobante / Factura (Opcional)</label>
                    <input type="file" id="comprobante" name="comprobante" class="form-control" accept="image/png, image/jpeg, application/pdf">
                    <div class="form-text">Formatos permitidos: PDF, JPG, PNG (Max. 2MB).</div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="<?= url('movimiento/index') ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success" style="padding: 0.75rem 1.75rem; font-size: 1rem;">
                    <i class='bx bx-check-double'></i> Ejecutar y Actualizar Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
/**
 * Efecto visual para cambiar estilos y mostrar el stock actual al seleccionar producto
 */
function updateStockPreview() {
    const select = document.getElementById('producto_id');
    const selectedOption = select.options[select.selectedIndex];
    const stock = selectedOption ? selectedOption.getAttribute('data-stock') : 0;
    const precio = selectedOption ? selectedOption.getAttribute('data-precio') : '0.00';
    
    document.getElementById('valStock').textContent = stock;
    
    // Auto-completar precio si el input está en 0
    const inputCosto = document.getElementById('costo_unitario');
    if (inputCosto && (inputCosto.value === '0' || inputCosto.value === '0.00' || inputCosto.value === '')) {
        inputCosto.value = precio;
    }
}

// Escuchar cambios entre ENTRADA y SALIDA para dar feedback visual al usuario
document.addEventListener('DOMContentLoaded', () => {
    updateStockPreview();
    
    const radioEntrada = document.getElementById('tipoEntrada');
    const radioSalida = document.getElementById('tipoSalida');
    const lblEntrada = document.getElementById('lblEntrada');
    const lblSalida = document.getElementById('lblSalida');
    
    function updateRadioStyles() {
        if (radioEntrada.checked) {
            lblEntrada.style.background = 'rgba(16, 185, 129, 0.1)';
            lblEntrada.style.borderColor = 'var(--success)';
            lblSalida.style.background = 'rgba(139, 92, 246, 0.05)';
            lblSalida.style.borderColor = 'rgba(139, 92, 246, 0.3)';
        } else {
            lblSalida.style.background = 'rgba(139, 92, 246, 0.1)';
            lblSalida.style.borderColor = 'var(--info)';
            lblEntrada.style.background = 'rgba(16, 185, 129, 0.05)';
            lblEntrada.style.borderColor = 'rgba(16, 185, 129, 0.3)';
        }
    }

    radioEntrada.addEventListener('change', updateRadioStyles);
    radioSalida.addEventListener('change', updateRadioStyles);
    updateRadioStyles();
});
</script>
