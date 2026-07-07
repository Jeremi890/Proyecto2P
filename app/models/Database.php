<?php
/**
 * ==============================================================================
 * CLASE DE BASE DE DATOS - PATRÓN SINGLETON CON PDO
 * Gestiona la conexión segura a MySQL evitando inyecciones SQL y conexiones duplicadas
 * ==============================================================================
 */
class Database {
    /** @var self|null Instancia única de la conexión */
    private static $instance = null;
    
    /** @var PDO Objeto de conexión a base de datos */
    private $pdo;

    /**
     * Constructor privado para aplicar el patrón Singleton
     */
    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,         // Retornar objetos por defecto ($fila->campo)
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Usar sentencias preparadas reales en MySQL
            PDO::ATTR_PERSISTENT         => false                   // Conexiones limpias
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Manejo elegante de error de conexión para depuración rápida
            die("<div style='font-family: Arial, sans-serif; background: #fee2e2; border: 1px solid #ef4444; color: #7f1d1d; padding: 20px; border-radius: 8px; margin: 20px;'>
                    <h3 style='margin-top:0;'>⚠️ Error de Conexión a la Base de Datos</h3>
                    <p>No se pudo conectar a la base de datos MySQL <strong>" . DB_NAME . "</strong> en <strong>" . DB_HOST . "</strong>.</p>
                    <p><strong>Detalle técnico:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
                    <hr style='border:none; border-top:1px solid #ef4444; margin: 15px 0;'>
                    <p style='font-size: 0.9em;'>👉 Asegúrate de haber importado el archivo <code>database/database.sql</code> en tu servidor MySQL (XAMPP/Laragon/phpMyAdmin).</p>
                 </div>");
        }
    }

    /**
     * Evitar clonación del objeto Singleton
     */
    private function __clone() {}

    /**
     * Obtener la instancia única de la conexión
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtener el objeto PDO nativo
     */
    public function getConnection() {
        return $this->pdo;
    }

    /**
     * Método auxiliar para preparar y ejecutar consultas rápidamente
     */
    public function query(string $sql, array $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
