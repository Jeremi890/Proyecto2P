<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <span class="badge badge-purple" style="margin-bottom: 0.5rem;"><i class='bx bx-buildings'></i> Directorio Comercial</span>
        <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-dark); margin: 0;">Gestión de Proveedores</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Directorio de empresas y mayoristas asociados para la provisión de stock.</p>
    </div>
    <div>
        <a href="<?= url('proveedor/create') ?>" class="btn btn-primary">
            <i class='bx bx-plus'></i> Nuevo Proveedor
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class='bx bx-id-card'></i> Directorio de Empresas Registradas</div>
        <div style="width: 280px;">
            <input type="text" id="tableQuickFilter" class="form-control" placeholder="Buscar por RUC o empresa..." style="padding: 0.5rem 0.8rem; font-size: 0.85rem;">
        </div>
    </div>

    <?php if (empty($proveedores)): ?>
        <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;"><i class='bx bx-buildings'></i></div>
            <h3 style="color: var(--text-dark); margin-bottom: 0.5rem;">Directorio Comercial Vacío</h3>
            <p style="margin-bottom: 1.5rem;">Registra a tu primer proveedor para vincular las entradas de compra y stock.</p>
            <a href="<?= url('proveedor/create') ?>" class="btn btn-primary">Registrar Primer Proveedor</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>RUC / NIT</th>
                        <th>Empresa</th>
                        <th>Persona de Contacto</th>
                        <th>Contacto Directo</th>
                        <th>Transacciones</th>
                        <th>Estado</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proveedores as $prov): ?>
                    <tr>
                        <td style="font-family: 'JetBrains Mono', monospace; font-weight: 600; color: var(--primary);">
                            <?= e($prov->ruc) ?>
                        </td>
                        <td style="font-weight: 600; color: var(--text-main);">
                            <?= e($prov->nombre_empresa) ?>
                        </td>
                        <td style="color: var(--text-muted);">
                            <?= e($prov->contacto_persona ?: '— No especificado —') ?>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: var(--text-main); margin-bottom: 2px;"><i class='bx bx-phone'></i> <?= e($prov->telefono) ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><i class='bx bx-envelope'></i> <?= e($prov->email) ?></div>
                        </td>
                        <td>
                            <?php if ($prov->total_movimientos > 0): ?>
                                <span class="badge badge-info"><i class='bx bx-transfer'></i> <?= number_format($prov->total_movimientos) ?> op.</span>
                            <?php else: ?>
                                <span class="badge badge-secondary" style="background: var(--bg-main); color: var(--text-muted);">0 op.</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($prov->estado == 1): ?>
                                <span class="badge badge-success">Activo</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <div class="action-buttons" style="justify-content: flex-end;">
                                <a href="<?= url('proveedor/edit/' . $prov->id) ?>" class="btn btn-sm btn-secondary" title="Editar datos">
                                    <i class='bx bx-edit'></i> Editar
                                </a>
                                <a href="<?= url('proveedor/delete/' . $prov->id) ?>" class="btn btn-sm btn-danger btn-delete-confirm" data-name="al proveedor '<?= e($prov->nombre_empresa) ?>'" title="Eliminar proveedor">
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
