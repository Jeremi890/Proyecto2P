<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/models/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Verificar si la columna existe antes de crearla
    $check = $db->query("SHOW COLUMNS FROM movimientos LIKE 'comprobante_url'");
    if ($check->rowCount() == 0) {
        $db->exec("ALTER TABLE movimientos ADD COLUMN comprobante_url VARCHAR(255) DEFAULT NULL");
        echo "Columna comprobante_url anadida correctamente.\n";
    } else {
        echo "La columna comprobante_url ya existe.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
