<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <span class="badge badge-info" style="margin-bottom: 0.5rem;"><i class='bx bx-purchase-tag-alt'></i> Módulo de Clasificación</span>
        <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-dark); margin: 0;">Gestión de Categorías</h1>
        <p style="color: var(--text-muted); margin-top: 0.25rem;">Administra las familias o agrupaciones para el catálogo de productos.</p>
    </div>
    <div>
        <a href="<?= url('categoria/create') ?>" class="btn btn-primary">
            <i class='bx bx-plus'></i> Nueva Categoría
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class='bx bx-folder-open'></i> Directorio de Categorías Registradas</div>
        <div style="width: 280px;">
            <input type="text" id="tableQuickFilter" class="form-control" placeholder="Buscar categoría en vivo..." style="padding: 0.5rem 0.8rem; font-size: 0.85rem;">
        </div>
    </div>

    <?php if (empty($categorias)): ?>
        <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;"><i class='bx bx-folder'></i></div>
            <h3 style="color: var(--text-dark); margin-bottom: 0.5rem;">No hay categorías creadas aún</h3>
            <p style="margin-bottom: 1.5rem;">Crea tu primera categoría para empezar a organizar tu inventario.</p>
            <a href="<?= url('categoria/create') ?>" class="btn btn-primary">Crear Primera Categoría</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Categoría</th>
                        <th>Descripción</th>
                        <th>Productos Asignados</th>
                        <th>Estado</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $cat): ?>
                    <tr>
                        <td style="color: var(--text-muted); font-family: 'JetBrains Mono', monospace;">#<?= e($cat->id) ?></td>
                        <td style="font-weight: 600; color: var(--text-main);">
                            <span style="color: var(--primary); margin-right: 0.5rem;"><i class='bx bx-folder'></i></span>
                            <?= e($cat->nombre) ?>
                        </td>
                        <td style="color: var(--text-muted); max-width: 350px;">
                            <?= e($cat->descripcion ?: '— Sin descripción adicional —') ?>
                        </td>
                        <td>
                            <?php if ($cat->total_productos > 0): ?>
                                <span class="badge badge-purple" style="font-size: 0.8rem;">
                                    <i class='bx bx-box'></i> <?= number_format($cat->total_productos) ?> productos
                                </span>
                            <?php else: ?>
                                <span class="badge badge-warning" style="font-size: 0.75rem;">Vacía (0 prod.)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($cat->estado == 1): ?>
                                <span class="badge badge-success">Activa</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <div class="action-buttons" style="justify-content: flex-end;">
                                <a href="<?= url('categoria/edit/' . $cat->id) ?>" class="btn btn-sm btn-secondary" title="Editar categoría">
                                    <i class='bx bx-edit'></i> Editar
                                </a>
                                <?php if ($cat->total_productos == 0): ?>
                                    <a href="<?= url('categoria/delete/' . $cat->id) ?>" class="btn btn-sm btn-danger btn-delete-confirm" data-name="la categoría '<?= e($cat->nombre) ?>'" title="Eliminar categoría">
                                        <i class='bx bx-trash'></i> Eliminar
                                    </a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed;" title="No se puede eliminar: tiene productos asignados">
                                        <i class='bx bx-lock'></i> Protegida
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
