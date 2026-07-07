<?php
/**
 * ENRUTADOR PRINCIPAL (FRONT CONTROLLER)
 * Punto de entrada único para todas las peticiones HTTP de la aplicación.
 * Implementa el patrón Front Controller para despachar rutas al Controlador y Acción adecuados.
 */

// 1. Cargar archivo de configuración global
require_once __DIR__ . '/../app/config/config.php';

// 2. Cargar manejador de Base de Datos
require_once __DIR__ . '/../app/models/Database.php';

// 3. Autocargador de clases (Models y Controllers)
spl_autoload_register(function ($className) {
    $modelPath = __DIR__ . '/../app/models/' . $className . '.php';
    $controllerPath = __DIR__ . '/../app/controllers/' . $className . '.php';
    
    if (file_exists($modelPath)) {
        require_once $modelPath;
    } elseif (file_exists($controllerPath)) {
        require_once $controllerPath;
    }
});

// 4. Analizar la URL solicitada (Soporta '?url=modulo/accion/id' o URIs limpias)
$url = $_GET['url'] ?? '';

if (empty($url)) {
    // Si no hay parámetro 'url', intentar obtener de REQUEST_URI (para servidor integrado de PHP o Apache con mod_rewrite)
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    
    // Remover el nombre del script o directorio base si está presente
    $basePath = dirname($scriptName);
    $basePath = ($basePath === '\\' || $basePath === '/') ? '' : $basePath;
    
    if (!empty($basePath) && strpos($requestUri, $basePath) === 0) {
        $requestUri = substr($requestUri, strlen($basePath));
    }
    
    // Remover parámetros GET extra de la cadena
    $url = explode('?', $requestUri)[0];
}

// Limpiar barra inicial y final
$url = trim($url, '/');

// Si sigue vacío o es 'index.php', redirigir al controlador por defecto
if (empty($url) || $url === 'index.php' || strtolower($url) === 'home') {
    $controllerName = 'HomeController';
    $actionName = 'index';
    $params = [];
} else {
    // Dividir la URL en partes: [modulo, accion, parametro_id]
    $urlParts = explode('/', $url);
    
    // Nombre de controlador: 'categoria' -> 'CategoriaController'
    $moduleName = ucfirst(strtolower(array_shift($urlParts)));
    $controllerName = $moduleName . 'Controller';
    
    // Nombre de acción: por defecto 'index'
    $actionName = !empty($urlParts) ? strtolower(array_shift($urlParts)) : 'index';
    
    // Parámetros adicionales (ej. ID de registro)
    $params = $urlParts;
}

// 5. Despacho de la solicitud al Controlador y Acción correspondientes
$controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    $controller = new $controllerName();
    
    if (method_exists($controller, $actionName)) {
        // Ejecutar el método con los parámetros pasados (ej. $controller->edit($id))
        call_user_func_array([$controller, $actionName], $params);
    } else {
        // Acción no encontrada en el controlador -> Error 404
        show_404("La acción '<strong>" . htmlspecialchars($actionName) . "</strong>' no existe en el módulo <strong>" . htmlspecialchars($moduleName ?? 'Home') . "</strong>.");
    }
} else {
    // Controlador no encontrado -> Error 404
    show_404("El módulo o página solicitado (<strong>" . htmlspecialchars($url) . "</strong>) no existe.");
}

/**
 * Pantalla de Error 404 elegante e integrada con el diseño del sistema
 */
function show_404(string $mensaje) {
    http_response_code(404);
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 - Página No Encontrada | NexusStock MVC</title>
        <link rel="stylesheet" href="' . url('css/styles.css') . '">
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    </head>
    <body style="background: #0f172a; color: #f8fafc; font-family: \'Outfit\', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0;">
        <div style="background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(12px); padding: 40px; border-radius: 16px; text-align: center; max-width: 500px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
            <div style="font-size: 64px; margin-bottom: 10px;">🔍</div>
            <h1 style="font-size: 36px; margin: 0; color: #6366f1;">Error 404</h1>
            <h2 style="font-size: 20px; font-weight: 400; color: #cbd5e1; margin-top: 5px;">Página no encontrada</h2>
            <p style="color: #94a3b8; margin: 20px 0; line-height: 1.6;">' . $mensaje . '</p>
            <a href="' . url('') . '" style="display: inline-block; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; transition: all 0.3s;">
                ⬅ Volver al Dashboard
            </a>
        </div>
    </body>
    </html>';
    exit;
}
