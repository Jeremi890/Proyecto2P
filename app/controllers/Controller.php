<?php
/**
 * CONTROLADOR BASE
 * Clase padre para todos los controladores. Encapsula el renderizado de vistas y paso de datos.
 */
class Controller {
    
    /**
     * Renderizar una vista MVC englobada dentro del Header y Footer del layout
     * 
     * @param string $view Ruta relativa a la carpeta 'views/' (ej. 'categorias/index')
     * @param array $data Variables asociativas que estarán disponibles dentro de la vista
     */
    protected function view(string $view, array $data = []) {
        // Convertir el array asociativo en variables individuales (ej. ['categorias' => $cat] -> $categorias)
        extract($data);
        
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            // Cargar barra de navegación y encabezado HTML
            require_once __DIR__ . '/../views/layouts/header.php';
            
            // Cargar contenido principal de la vista solicitada
            require_once $viewFile;
            
            // Cargar pie de página, scripts y modales
            require_once __DIR__ . '/../views/layouts/footer.php';
        } else {
            die("<div style='background: #fee2e2; color: #7f1d1d; padding: 20px; font-family: sans-serif;'>
                    <h3>⚠️ Error de Vista MVC</h3>
                    <p>No se pudo localizar el archivo de vista: <code>app/views/{$view}.php</code></p>
                 </div>");
        }
    }

    /**
     * Responder con un JSON (Útil para AJAX, validaciones o endpoints API en vivo)
     */
    protected function json(array $data, int $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
