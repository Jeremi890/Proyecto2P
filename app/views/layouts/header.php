<?php
// Obtener mensaje flash si existe
$flash = get_flash_message();
$activeMenu = $active_menu ?? 'home';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? 'NexusStock MVC | Control Inteligente de Inventario') ?></title>
    <!-- Estilos Premium de la Aplicación -->
    <link rel="stylesheet" href="<?= url('css/styles.css') ?>">
    <!-- Iconos de Google Fonts / Boxicons (Opción ligera y estética) -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

    <div class="app-layout">
        <!-- ==========================================================================
             BARRA LATERAL (SIDEBAR)
             ========================================================================== -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="<?= url('') ?>" class="brand">
                    <div class="brand-icon"><i class='bx bx-bolt-circle'></i></div>
                    <div class="brand-text">Nexus<span>Stock</span></div>
                </a>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-links">
                    <li class="nav-item">
                        <a href="<?= url('') ?>" class="<?= $activeMenu === 'home' ? 'active' : '' ?>">
                            <i class='bx bx-grid-alt'></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('categoria/index') ?>" class="<?= $activeMenu === 'categorias' ? 'active' : '' ?>">
                            <i class='bx bx-purchase-tag-alt'></i> <span>Categorías</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('producto/index') ?>" class="<?= $activeMenu === 'productos' ? 'active' : '' ?>">
                            <i class='bx bx-box'></i> <span>Productos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('proveedor/index') ?>" class="<?= $activeMenu === 'proveedores' ? 'active' : '' ?>">
                            <i class='bx bx-buildings'></i> <span>Proveedores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('movimiento/index') ?>" class="<?= $activeMenu === 'movimientos' ? 'active' : '' ?>">
                            <i class='bx bx-transfer'></i> <span>Movimientos</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- ==========================================================================
             CONTENEDOR PRINCIPAL DEL SISTEMA
             ========================================================================== -->
        <div class="main-wrapper">
            <main class="main-content">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= e($flash['type']) ?>" role="alert">
                        <span><?= $flash['text'] ?></span>
                    </div>
                <?php endif; ?>
