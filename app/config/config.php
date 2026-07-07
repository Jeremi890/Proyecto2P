<?php
/**
 * ==============================================================================
 * ARCHIVO DE CONFIGURACIÓN PRINCIPAL - NEXUSSTOCK MVC
 * Configuración de Base de Datos, Rutas Base y Manejo de Sesiones / Mensajes Flash
 * ==============================================================================
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de zona horaria
date_default_timezone_set('America/Lima');

// Configuración de Parámetros de la Base de Datos MySQL
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: ''); 
define('DB_NAME', getenv('DB_NAME') ?: 'nexusstock_db');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la URL Base de la Aplicación
// Detecta dinámicamente si se ejecuta en servidor integrado o en subcarpeta de Apache
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = dirname($scriptName);
$baseDir = ($baseDir === '\\' || $baseDir === '/') ? '' : $baseDir;

define('BASE_URL', rtrim($protocol . $host . $baseDir, '/'));
define('APP_NAME', 'NexusStock MVC');
define('APP_VERSION', '2.0.0 Premium');

/**
 * Función de ayuda para generar URLs absolutas dentro del sistema
 */
function url(string $path = '') {
    $path = ltrim($path, '/');
    
    // Rutas vacías apuntan a la raíz del script
    if (empty($path)) {
        return BASE_URL . '/index.php';
    }
    
    // Si la ruta es para archivos estáticos, no usar index.php?url=
    if (strpos($path, 'css/') === 0 || strpos($path, 'js/') === 0 || strpos($path, 'img/') === 0) {
        return BASE_URL . '/' . $path;
    }
    
    // Para todas las demás rutas MVC, inyectar el controlador frontal explícitamente
    return BASE_URL . '/index.php?url=' . $path;
}

/**
 * Función para redireccionar a una URL del sistema
 */
function redirect(string $path) {
    header('Location: ' . url($path));
    exit;
}

/**
 * Funciones para manejo de Mensajes Flash (Notificaciones Toast en UI)
 */
function set_flash_message(string $type, string $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,     // 'success', 'danger', 'warning', 'info'
        'text' => $message
    ];
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

/**
 * Función para limpiar e imprimir datos seguros en vistas (Sanitización XSS)
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
