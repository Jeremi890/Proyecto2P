<?php
/**
 * @var string|null $filtro_search
 * @var int|null $filtro_cat
 * @var object[] $categorias
 * @var object[] $productos
 */
?>
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <span class="badge badge-success" style="margin-bottom: 0.5rem;"><i class='bx bx-box'></i> Almacén Central</span>
        <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-dark); margin: 0;">Catálogo de Productos</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Control general de existencias, costos y alertas de stock mínimo.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="<?= url('movimiento/create') ?>" class="btn btn-success">
            <i class='bx bx-transfer'></i> Mover Stock
        </a>
        <a href="<?= url('producto/create') ?>" class="btn btn-primary">
            <i class='bx bx-plus'></i> Nuevo Producto
        </a>
    </div>
</div>

<!-- ==========================================================================
     BARRA DE FILTROS Y BÚSQUEDA INTERACTIVA
     ========================================================================== -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="<?= url('producto/index') ?>" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 250px;">
            <input type="text" name="search" class="form-control" placeholder="Buscar por código SKU, nombre o descripción..." value="<?= e($filtro_search ?? '') ?>">
        </div>
        <div style="width: 250px;">
            <select name="categoria_id" class="form-select" onchange="this.form.submit()">
                <option value="">Todas las Categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat->id ?>" <?= ($filtro_cat == $cat->id) ? 'selected' : '' ?>>
                        <?= e($cat->nombre) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-secondary"><i class='bx bx-filter-alt'></i> Filtrar</button>
            <?php if (!empty($filtro_search) || !empty($filtro_cat)): ?>
                <a href="<?= url('producto/index') ?>" class="btn btn-danger"><i class='bx bx-x'></i> Limpiar</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- ==========================================================================
     TABLA PRINCIPAL DEL CATÁLOGO DE PRODUCTOS
     ========================================================================== -->
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class='bx bx-list-ul'></i> Inventario Físico Valorado</div>
        <div style="width: 250px;">
            <input type="text" id="tableQuickFilter" class="form-control" placeholder="Filtro rápido en tabla..." style="padding: 0.5rem 0.8rem; font-size: 0.85rem;">
        </div>
    </div>

    <?php if (empty($productos)): ?>
        <div style="text-align: center; padding: 3.5rem 1rem; color: var(--text-muted);">
            <div style="font-size: 3.5rem; margin-bottom: 0.5rem;"><i class='bx bx-box'></i></div>
            <h3 style="color: var(--text-dark); margin-bottom: 0.5rem;">No se encontraron productos en el catálogo</h3>
            <p style="margin-bottom: 1.5rem;">Agrega tu primer artículo para activar las estadísticas y transacciones del almacén.</p>
            <a href="<?= url('producto/create') ?>" class="btn btn-primary">Registrar Primer Producto</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código SKU</th>
                        <th>Producto / Descripción</th>
                        <th>Categoría</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Stock Actual</th>
                        <th>Estado</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                    <tr>
                        <td>
                            <code style="background: rgba(0,0,0,0.05); padding: 4px 8px; border-radius: 4px; color: var(--primary); font-weight: 600;">
                                <?= e($prod->codigo) ?>
                            </code>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-main); font-size: 1rem;"><?= e($prod->nombre) ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= e($prod->descripcion ?: 'Sin descripción') ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-purple" style="font-size: 0.75rem;">
                                <?= e($prod->categoria_nombre) ?>
                            </span>
                        </td>
                        <td style="font-family: 'JetBrains Mono', monospace; color: var(--text-muted);">
                            $<?= number_format($prod->precio_compra, 2) ?>
                        </td>
                        <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--success); font-size: 1.05rem;">
                            $<?= number_format($prod->precio_venta, 2) ?>
                        </td>
                        <td>
                            <?php if ($prod->stock == 0): ?>
                                <span class="badge badge-danger" style="font-size: 0.85rem;"><i class='bx bx-x-circle'></i> 0 <?= e($prod->unidad_medida) ?> (Agotado)</span>
                            <?php elseif ($prod->stock <= $prod->stock_minimo): ?>
                                <span class="badge badge-warning" style="font-size: 0.85rem;"><i class='bx bx-error'></i> <?= number_format($prod->stock) ?> <?= e($prod->unidad_medida) ?></span>
                            <?php else: ?>
                                <span class="badge badge-success" style="font-size: 0.85rem;"><i class='bx bx-check-circle'></i> <?= number_format($prod->stock) ?> <?= e($prod->unidad_medida) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($prod->estado == 1): ?>
                                <span class="badge badge-success">Activo</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <div class="action-buttons" style="justify-content: flex-end;">
                                <a href="<?= url('movimiento/create?prod_id=' . $prod->id) ?>" class="btn btn-sm btn-success" title="Registrar Entrada/Salida de este producto">
                                    <i class='bx bx-transfer'></i> Mover
                                </a>
                                <a href="<?= url('producto/edit/' . $prod->id) ?>" class="btn btn-sm btn-secondary" title="Editar producto">
                                    <i class='bx bx-edit'></i> Editar
                                </a>
                                <a href="<?= url('producto/delete/' . $prod->id) ?>" class="btn btn-sm btn-danger btn-delete-confirm" data-name="el producto '<?= e($prod->nombre) ?>'" title="Eliminar producto">
                                    <i class='bx bx-trash'></i> Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
