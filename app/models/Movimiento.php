<?php
/**
 * MODELO: Movimiento
 * Gestión de transacciones de inventario (Entradas, Salidas y Ajustes)
 * Implementa Transacciones SQL (Begin / Commit / Rollback) para garantizar integridad
 */
class Movimiento {
    /** @var Database */
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener historial de movimientos con detalles de Producto y Proveedor
     */
    public function getAll($limit = 50) {
        $sql = "SELECT m.*, 
                       p.nombre as producto_nombre, p.codigo as producto_codigo, p.unidad_medida,
                       pr.nombre_empresa as proveedor_nombre 
                FROM movimientos m 
                INNER JOIN productos p ON m.producto_id = p.id 
                LEFT JOIN proveedores pr ON m.proveedor_id = pr.id 
                ORDER BY m.fecha_movimiento DESC 
                LIMIT :limit";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener un movimiento específico por ID
     */
    public function getById(int $id) {
        $sql = "SELECT m.*, p.nombre as producto_nombre, pr.nombre_empresa as proveedor_nombre 
                FROM movimientos m 
                INNER JOIN productos p ON m.producto_id = p.id 
                LEFT JOIN proveedores pr ON m.proveedor_id = pr.id 
                WHERE m.id = :id LIMIT 1";
        return $this->db->query($sql, [':id' => $id])->fetch();
    }

    /**
     * Registrar un nuevo movimiento usando TRANSACCIÓN SQL (ACID)
     * Esto asegura que si se guarda el movimiento, el stock del producto se actualice sí o sí.
     */
    public function registrar(array $data) {
        $pdo = $this->db->getConnection();
        
        try {
            // 1. Iniciar Transacción SQL
            $pdo->beginTransaction();

            // 2. Insertar el registro de movimiento
            $sqlInsert = "INSERT INTO movimientos (producto_id, proveedor_id, tipo_movimiento, cantidad, costo_unitario, notas, comprobante_url) 
                          VALUES (:producto_id, :proveedor_id, :tipo_movimiento, :cantidad, :costo_unitario, :notas, :comprobante_url)";
            
            $stmt = $pdo->prepare($sqlInsert);
            $stmt->execute([
                ':producto_id' => (int)$data['producto_id'],
                ':proveedor_id' => !empty($data['proveedor_id']) ? (int)$data['proveedor_id'] : null,
                ':tipo_movimiento' => trim($data['tipo_movimiento']),
                ':cantidad' => (int)$data['cantidad'],
                ':costo_unitario' => (float)$data['costo_unitario'],
                ':notas' => trim($data['notas'] ?? ''),
                ':comprobante_url' => $data['comprobante_url'] ?? null
            ]);

            // 3. Calcular el cambio de stock
            $cantidad = (int)$data['cantidad'];
            $tipo = trim($data['tipo_movimiento']);
            
            if ($tipo === 'ENTRADA') {
                $cambio = $cantidad;        // Suma al almacén
            } elseif ($tipo === 'SALIDA') {
                $cambio = -$cantidad;       // Resta del almacén
            } else {
                $cambio = $data['cambio_ajuste'] ?? 0; // Para ajustes manuales
            }

            // 4. Actualizar stock en la tabla productos
            $sqlUpdate = "UPDATE productos SET stock = stock + (:cambio) WHERE id = :id";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([
                ':cambio' => $cambio,
                ':id' => (int)$data['producto_id']
            ]);

            // 5. Confirmar transacción
            $pdo->commit();
            return true;

        } catch (Exception $e) {
            // Si ocurre cualquier error, revertimos todos los cambios
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e; // Lanzar excepción para capturarla en el controlador
        }
    }

    /**
     * Obtener estadísticas recientes de transacciones para el Dashboard
     */
    public function getEstadisticasRecientes() {
        $sql = "SELECT 
                    SUM(CASE WHEN tipo_movimiento = 'ENTRADA' THEN cantidad ELSE 0 END) as total_entradas_und,
                    SUM(CASE WHEN tipo_movimiento = 'SALIDA' THEN cantidad ELSE 0 END) as total_salidas_und
                FROM movimientos";
        return $this->db->query($sql)->fetch();
    }
}
