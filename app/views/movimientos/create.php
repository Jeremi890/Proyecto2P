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

            <div id="selectorModoIngreso" style="margin-bottom: 1.5rem; background: rgba(0,0,0,0.02); padding: 1rem; border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
                <label class="form-label" style="font-weight: 700; color: var(--text-dark); margin-bottom: 0.75rem;">¿Qué tipo de artículo deseas ingresar al almacén?</label>
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    <?php if (!empty($productos)): ?>
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: var(--primary);">
                        <input type="radio" name="modo_ingreso" value="existente" id="modoExistente" checked onchange="toggleModoIngreso()" style="accent-color: var(--primary); width: 18px; height: 18px;">
                        <span>Reponer Stock (Producto Ya Existente)</span>
                    </label>
                    <?php endif; ?>
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: var(--success);">
                        <input type="radio" name="modo_ingreso" value="nuevo" id="modoNuevo" <?= (empty($productos)) ? 'checked' : '' ?> onchange="toggleModoIngreso()" style="accent-color: var(--success); width: 18px; height: 18px;">
                        <span>Registrar Nuevo Producto en el Catálogo</span>
                    </label>
                </div>
            </div>

            <div id="panelExistente" style="<?= (empty($productos)) ? 'display: none;' : '' ?>">
                <?php if (empty($productos)): ?>
                    <div class="alert alert-warning" style="margin-bottom: 1rem;">
                        ⚠️ No hay artículos registrados aún en el catálogo. Utiliza la opción <b>"✨ Registrar Nuevo Producto"</b> para realizar tu primer alta de mercadería.
                    </div>
                <?php else: ?>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="producto_id" class="form-label">Producto del Catálogo <span style="color: var(--danger);">*</span></label>
                    <select id="producto_id" name="producto_id" class="form-select" onchange="updateStockPreview()">
                        <?php foreach ($productos as $prod): ?>
                            <option value="<?= $prod->id ?>" data-stock="<?= $prod->stock ?>" data-precio="<?= $prod->precio_compra ?>" <?= (($data['producto_id'] ?? '') == $prod->id || (isset($_GET['prod_id']) && $_GET['prod_id'] == $prod->id)) ? 'selected' : '' ?>>
                                [<?= e($prod->codigo) ?>] <?= e($prod->nombre) ?> (Stock actual: <?= number_format($prod->stock) ?> <?= e($prod->unidad_medida) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="stockPreview" style="margin-top: 0.5rem; font-size: 0.85rem; font-weight: 600; color: var(--primary);">
                        <i class='bx bx-bulb'></i> Stock actual disponible en almacén: <span id="valStock">0</span> unidades.
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div id="panelNuevo" style="<?= (!empty($productos)) ? 'display: none;' : '' ?> background: rgba(16, 185, 129, 0.05); padding: 1.5rem; border-radius: var(--radius-md); border: 2px solid var(--success); margin-bottom: 1.5rem;">
                <h4 style="color: var(--success); margin-top: 0; margin-bottom: 1rem; font-size: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class='bx bx-plus-circle'></i> Ficha Técnica del Nuevo Producto
                </h4>
                <div class="form-grid" style="margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Categoría / Familia <span style="color: var(--danger);">*</span></label>
                        <select name="nuevo_categoria_id" id="nuevo_categoria_id" class="form-select">
                            <option value="">— Seleccionar Categoría —</option>
                            <?php foreach (($categorias ?? []) as $cat): ?>
                                <option value="<?= $cat->id ?>"><?= e($cat->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Código SKU / Barras <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="nuevo_codigo" id="nuevo_codigo" class="form-control" placeholder="Ej. LAP-HP-001" style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">
                    </div>
                </div>
                <div class="form-grid" style="margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Nombre Comercial del Producto <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="nuevo_nombre" id="nuevo_nombre" class="form-control" placeholder="Ej. Laptop HP 15 Ryzen 5 8GB">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unidad de Medida <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="nuevo_unidad" class="form-control" value="Unidad" placeholder="Ej. Unidad, Caja, Kg, Litro">
                    </div>
                </div>
                <div class="form-grid" style="margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Precio de Venta al Público ($) <span style="color: var(--danger);">*</span></label>
                        <input type="number" step="0.01" min="0" name="nuevo_precio_venta" class="form-control" placeholder="0.00" style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--success);">
                        <div class="form-text">El precio de compra será tomado de tu Costo Unitario abajo.</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alerta de Stock Mínimo</label>
                        <input type="number" min="1" name="nuevo_stock_minimo" class="form-control" value="5">
                        <div class="form-text">Cantidad mínima antes de mostrar alerta roja.</div>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Descripción o Especificaciones (Opcional)</label>
                    <input type="text" name="nuevo_descripcion" class="form-control" placeholder="Detalles técnicos, color, marca...">
                </div>
            </div>

            <div class="form-group" id="groupProveedor" style="margin-bottom: 1.5rem;">
                <label for="proveedor_id" class="form-label" style="font-weight: 700; color: var(--text-dark);">Proveedor Suministrador <span id="starProveedor" style="color: var(--danger);">*</span></label>
                <select id="proveedor_id" name="proveedor_id" class="form-select">
                    <option value="">— Ninguno / Retiro General —</option>
                    <?php foreach ($proveedores as $prov): ?>
                        <option value="<?= $prov->id ?>" <?= (($data['proveedor_id'] ?? '') == $prov->id) ? 'selected' : '' ?>>
                            <?= e($prov->nombre_empresa) ?> (RUC: <?= e($prov->ruc) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Obligatorio y recomendado para toda compra o alta de mercadería (Entradas).</div>
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
    if (!select) return;
    const selectedOption = select.options[select.selectedIndex];
    const stock = selectedOption ? selectedOption.getAttribute('data-stock') : 0;
    const precio = selectedOption ? selectedOption.getAttribute('data-precio') : '0.00';
    
    const valStock = document.getElementById('valStock');
    if (valStock) valStock.textContent = stock;
    
    // Auto-completar precio si el input está en 0
    const inputCosto = document.getElementById('costo_unitario');
    if (inputCosto && (inputCosto.value === '0' || inputCosto.value === '0.00' || inputCosto.value === '')) {
        inputCosto.value = precio;
    }
}

function toggleModoIngreso() {
    const modoNuevo = document.getElementById('modoNuevo')?.checked;
    const radioSalida = document.getElementById('tipoSalida');
    const radioEntrada = document.getElementById('tipoEntrada');
    const panelExistente = document.getElementById('panelExistente');
    const panelNuevo = document.getElementById('panelNuevo');
    const selectProd = document.getElementById('producto_id');
    const inputNuevoCodigo = document.getElementById('nuevo_codigo');
    const inputNuevoNombre = document.getElementById('nuevo_nombre');
    const inputNuevoCat = document.getElementById('nuevo_categoria_id');
    
    if (modoNuevo) {
        if (panelExistente) panelExistente.style.display = 'none';
        if (panelNuevo) panelNuevo.style.display = 'block';
        if (selectProd) selectProd.required = false;
        if (inputNuevoCodigo) inputNuevoCodigo.required = true;
        if (inputNuevoNombre) inputNuevoNombre.required = true;
        if (inputNuevoCat) inputNuevoCat.required = true;
        
        // Si elige "Nuevo Producto", forzamos ENTRADA y deshabilitamos SALIDA
        if (radioSalida) {
            radioSalida.disabled = true;
            radioSalida.parentElement.style.opacity = '0.4';
            if (radioEntrada) radioEntrada.checked = true;
            if (typeof updateRadioStyles === 'function') updateRadioStyles();
        }
    } else {
        if (panelExistente) panelExistente.style.display = 'block';
        if (panelNuevo) panelNuevo.style.display = 'none';
        if (selectProd) selectProd.required = true;
        if (inputNuevoCodigo) inputNuevoCodigo.required = false;
        if (inputNuevoNombre) inputNuevoNombre.required = false;
        if (inputNuevoCat) inputNuevoCat.required = false;
        
        if (radioSalida) {
            radioSalida.disabled = false;
            radioSalida.parentElement.style.opacity = '1';
        }
    }
}

// Escuchar cambios entre ENTRADA y SALIDA para dar feedback visual al usuario
document.addEventListener('DOMContentLoaded', () => {
    updateStockPreview();
    toggleModoIngreso();
    
    const radioEntrada = document.getElementById('tipoEntrada');
    const radioSalida = document.getElementById('tipoSalida');
    const lblEntrada = document.getElementById('lblEntrada');
    const lblSalida = document.getElementById('lblSalida');
    const starProveedor = document.getElementById('starProveedor');
    const selectProv = document.getElementById('proveedor_id');
    
    function updateRadioStyles() {
        if (!radioEntrada || !lblEntrada || !lblSalida) return;
        if (radioEntrada.checked) {
            lblEntrada.style.background = 'rgba(16, 185, 129, 0.1)';
            lblEntrada.style.borderColor = 'var(--success)';
            lblSalida.style.background = 'rgba(139, 92, 246, 0.05)';
            lblSalida.style.borderColor = 'rgba(139, 92, 246, 0.3)';
            if (starProveedor) starProveedor.style.display = 'inline';
            if (selectProv) selectProv.required = true;
        } else {
            lblSalida.style.background = 'rgba(139, 92, 246, 0.1)';
            lblSalida.style.borderColor = 'var(--info)';
            lblEntrada.style.background = 'rgba(16, 185, 129, 0.05)';
            lblEntrada.style.borderColor = 'rgba(16, 185, 129, 0.3)';
            if (starProveedor) starProveedor.style.display = 'none';
            if (selectProv) selectProv.required = false;
        }
    }

    if (radioEntrada) radioEntrada.addEventListener('change', updateRadioStyles);
    if (radioSalida) radioSalida.addEventListener('change', updateRadioStyles);
    updateRadioStyles();
});
</script>
