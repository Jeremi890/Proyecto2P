<?php
/**
 * @var object[] $movimientos
 */
?>
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <span class="badge badge-purple" style="margin-bottom: 0.5rem;"><i class='bx bx-history'></i> Histórico Transaccional</span>
        <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-dark); margin: 0;">Movimientos de Almacén</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Registro auditable de Entradas por compra y Salidas por ventas o despachos.</p>
    </div>
    <div>
        <a href="<?= url('movimiento/create') ?>" class="btn btn-success">
            <i class='bx bx-plus'></i> Registrar Operación
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class='bx bx-bar-chart-alt-2'></i> Historial de Entradas y Salidas</div>
        <div style="width: 280px;">
            <input type="text" id="tableQuickFilter" class="form-control" placeholder="Filtrar por producto o proveedor..." style="padding: 0.5rem 0.8rem; font-size: 0.85rem;">
        </div>
    </div>

    <?php if (empty($movimientos)): ?>
        <div style="text-align: center; padding: 3.5rem 1rem; color: var(--text-muted);">
            <div style="font-size: 3.5rem; margin-bottom: 0.5rem;"><i class='bx bx-history'></i></div>
            <h3 style="color: var(--text-dark); margin-bottom: 0.5rem;">No hay transacciones registradas</h3>
            <p style="margin-bottom: 1.5rem;">Realiza tu primera operación de abastecimiento o salida de stock para el catálogo.</p>
            <a href="<?= url('movimiento/create') ?>" class="btn btn-success">Registrar Primer Movimiento</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID #</th>
                        <th>Fecha y Hora</th>
                        <th>Tipo Operación</th>
                        <th>Producto / SKU</th>
                        <th>Cantidad</th>
                        <th>Costo Unit.</th>
                        <th>Total ($)</th>
                        <th>Proveedor / Destino</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $mov): ?>
                    <tr>
                        <td style="color: var(--text-muted); font-family: 'JetBrains Mono', monospace;">
                            #<?= sprintf('%04d', $mov->id) ?>
                        </td>
                        <td style="font-size: 0.85rem; color: var(--text-main);">
                            <div><?= date('d/m/Y', strtotime($mov->fecha_movimiento)) ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);"><i class='bx bx-time-five'></i> <?= date('H:i:s', strtotime($mov->fecha_movimiento)) ?></div>
                        </td>
                        <td>
                            <?php if ($mov->tipo_movimiento === 'ENTRADA'): ?>
                                <span class="badge badge-success" style="font-size: 0.8rem;"><i class='bx bx-down-arrow-alt'></i> ENTRADA</span>
                            <?php else: ?>
                                <span class="badge badge-purple" style="font-size: 0.8rem;"><i class='bx bx-up-arrow-alt'></i> SALIDA</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-main);"><?= e($mov->producto_nombre) ?></div>
                            <code style="font-size: 0.75rem; color: var(--primary); background: rgba(0,0,0,0.05); padding: 2px 5px; border-radius: 3px;">
                                <?= e($mov->producto_codigo) ?>
                            </code>
                        </td>
                        <td style="font-family: 'JetBrains Mono', monospace; font-size: 1.1rem; font-weight: 700; color: <?= ($mov->tipo_movimiento === 'ENTRADA') ? 'var(--success)' : 'var(--info)' ?>;">
                            <?= ($mov->tipo_movimiento === 'ENTRADA') ? '+' : '-' ?><?= number_format($mov->cantidad) ?> <span style="font-size: 0.75rem; font-weight: 400; color: var(--text-muted);"><?= e($mov->unidad_medida) ?></span>
                        </td>
                        <td style="font-family: 'JetBrains Mono', monospace; color: var(--text-muted);">
                            $<?= number_format($mov->costo_unitario, 2) ?>
                        </td>
                        <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--text-main);">
                            $<?= number_format($mov->cantidad * $mov->costo_unitario, 2) ?>
                        </td>
                        <td style="font-size: 0.85rem; color: var(--text-main);">
                            <?php if ($mov->proveedor_nombre): ?>
                                <span style="color: var(--primary);"><i class='bx bx-buildings'></i> <?= e($mov->proveedor_nombre) ?></span>
                            <?php else: ?>
                                <span style="color: var(--text-muted);">— (Venta / Retiro interno)</span>
                            <?php endif; ?>
                            <?php if (!empty($mov->notas)): ?>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; font-style: italic;">
                                    "<?= e($mov->notas) ?>"
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($mov->comprobante_url)): ?>
                                <div style="margin-top: 4px;">
                                    <a href="<?= e(BASE_URL) ?>/uploads/movimientos/<?= e($mov->comprobante_url) ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.1rem 0.4rem; font-size: 0.75rem; background: var(--bg-main); border: 1px solid var(--border-color); color: var(--primary); text-decoration: none;">
                                        <i class='bx bx-file'></i> Ver Comprobante
                                    </a>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
