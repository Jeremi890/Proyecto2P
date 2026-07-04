<?php 
/** 
 * @var object $stats 
 * @var array $alertasStock 
 * @var array $movimientosRecientes 
 * @var int $total_categorias 
 * @var int $total_proveedores 
 */ 
?>
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <span class="badge badge-purple" style="margin-bottom: 0.5rem;"><i class='bx bx-rocket'></i> Dashboard Operativo</span>
        <h1 style="font-size: 2.2rem; font-weight: 700; color: var(--text-dark); margin: 0;">Panel de Control de Inventario</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Bienvenido al sistema inteligente de gestión. Aquí tienes el resumen en tiempo real.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="<?= url('producto/create') ?>" class="btn btn-primary">
            <i class='bx bx-plus'></i> Nuevo Producto
        </a>
        <a href="<?= url('movimiento/create') ?>" class="btn btn-success">
            <i class='bx bx-transfer'></i> Registrar Movimiento
        </a>
    </div>
</div>

<!-- ==========================================================================
     TARJETAS DE MÉTRICAS GLOBALES (STAT CARDS)
     ========================================================================== -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class='bx bx-box'></i></div>
        <div class="stat-info">
            <h4>Catálogo de Productos</h4>
            <div class="stat-value"><?= number_format($stats->total_productos ?? 0) ?> <span style="font-size: 0.9rem; font-weight: 400; color: var(--text-muted);">ref</span></div>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">Total de <?= number_format($stats->total_unidades ?? 0) ?> unidades físicas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green"><i class='bx bx-money'></i></div>
        <div class="stat-info">
            <h4>Valor en Inventario</h4>
            <div class="stat-value">$<?= number_format($stats->valor_total_venta ?? 0, 2) ?></div>
            <p style="font-size: 0.8rem; color: var(--success); margin-top: 0.25rem;">Costo compra: $<?= number_format($stats->valor_total_compra ?? 0, 2) ?></p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple"><i class='bx bx-purchase-tag-alt'></i></div>
        <div class="stat-info">
            <h4>Directorio y Clases</h4>
            <div class="stat-value"><?= number_format($total_categorias ?? 0) ?> / <?= number_format($total_proveedores ?? 0) ?></div>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">Categorías y Proveedores</p>
        </div>
    </div>

    <div class="stat-card" style="<?= ($stats->total_alerta_stock > 0) ? 'border-color: var(--danger); box-shadow: 0 0 20px rgba(239, 68, 68, 0.1);' : '' ?>">
        <div class="stat-icon red"><i class='bx bx-error'></i></div>
        <div class="stat-info">
            <h4>Alertas de Stock Bajo</h4>
            <div class="stat-value" style="color: <?= ($stats->total_alerta_stock > 0) ? 'var(--danger)' : 'var(--text-dark)' ?>;">
                <?= number_format($stats->total_alerta_stock ?? 0) ?>
            </div>
            <p style="font-size: 0.8rem; color: <?= ($stats->total_alerta_stock > 0) ? 'var(--danger)' : 'var(--text-muted)' ?>; margin-top: 0.25rem;">
                <?= ($stats->total_alerta_stock > 0) ? '¡Requiere reposición urgente!' : 'Inventario en niveles óptimos' ?>
            </p>
        </div>
    </div>
</div>

<!-- ==========================================================================
     ALERTA ROJA DE STOCK CRÍTICO (SE MUESTRA SÓLO SI HAY PRODUCTOS AGOTÁNDOSE)
     ========================================================================== -->
<?php if (!empty($alertasStock)): ?>
<div class="card" style="margin-bottom: 2rem; border-color: rgba(239, 68, 68, 0.4); background: #fff5f5;">
    <div class="card-header" style="border-bottom-color: rgba(239, 68, 68, 0.2);">
        <div class="card-title" style="color: var(--danger);">
            <i class='bx bx-alarm-exclamation'></i> Atención: Productos en Stock Crítico o Agotados
        </div>
        <a href="<?= url('movimiento/create') ?>" class="btn btn-sm btn-danger">
            <i class='bx bx-import'></i> Comprar Stock Ahora
        </a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Código SKU</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock Actual</th>
                    <th>Mínimo Requerido</th>
                    <th>Estado de Alerta</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alertasStock as $prod): ?>
                <tr>
                    <td><code style="background: rgba(0,0,0,0.05); padding: 3px 6px; border-radius: 4px; color: var(--text-dark);"><?= e($prod->codigo) ?></code></td>
                    <td style="font-weight: 600; color: var(--text-main);"><?= e($prod->nombre) ?></td>
                    <td><span class="badge badge-info"><?= e($prod->categoria_nombre) ?></span></td>
                    <td style="font-family: 'JetBrains Mono', monospace; font-size: 1.1rem; font-weight: 700; color: var(--danger);">
                        <?= number_format($prod->stock) ?> <?= e($prod->unidad_medida) ?>
                    </td>
                    <td style="color: var(--text-muted);"><?= number_format($prod->stock_minimo) ?> <?= e($prod->unidad_medida) ?></td>
                    <td>
                        <?php if ($prod->stock == 0): ?>
                            <span class="badge badge-danger"><i class='bx bx-x-circle'></i> AGOTADO (0%)</span>
                        <?php else: ?>
                            <span class="badge badge-warning"><i class='bx bx-error'></i> CRÍTICO</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= url('movimiento/create?prod_id=' . $prod->id) ?>" class="btn btn-sm btn-primary">
                            <i class='bx bx-plus-circle'></i> Reponer
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- ==========================================================================
     SECCIÓN DE ACTIVIDAD RECIENTE Y ACCESOS RÁPIDOS
     ========================================================================== -->
<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; align-items: start;">
    <!-- Últimos Movimientos -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class='bx bx-transfer'></i> Últimos Movimientos de Stock</div>
            <a href="<?= url('movimiento/index') ?>" style="color: var(--primary); text-decoration: none; font-size: 0.85rem; font-weight: 600;">Ver Historial Completo ➔</a>
        </div>
        
        <?php if (empty($movimientosRecientes)): ?>
            <p style="text-align: center; color: var(--text-muted); padding: 2rem 0;">No se han registrado movimientos de inventario recientemente.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Tipo</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Proveedor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimientosRecientes as $mov): ?>
                        <tr>
                            <td style="font-size: 0.85rem; color: var(--text-muted);">
                                <?= date('d/m/Y H:i', strtotime($mov->fecha_movimiento)) ?>
                            </td>
                            <td>
                                <?php if ($mov->tipo_movimiento === 'ENTRADA'): ?>
                                    <span class="badge badge-success"><i class='bx bx-down-arrow-alt'></i> ENTRADA</span>
                                <?php else: ?>
                                    <span class="badge badge-purple"><i class='bx bx-up-arrow-alt'></i> SALIDA</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 500; color: var(--text-main);"><?= e($mov->producto_nombre) ?></td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: <?= ($mov->tipo_movimiento === 'ENTRADA') ? 'var(--success)' : '#8b5cf6' ?>;">
                                <?= ($mov->tipo_movimiento === 'ENTRADA') ? '+' : '-' ?><?= number_format($mov->cantidad) ?>
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-dark);">
                                <?= e($mov->proveedor_nombre ?: '— (Venta / Mostrador)') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
