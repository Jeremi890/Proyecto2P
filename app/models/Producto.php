<?php
/**
 * ==============================================================================
 * MODELO: Producto
 * Gestión de acceso a datos para la tabla 'productos' usando PDO
 * Contiene consultas con JOIN, control de stock y estadísticas para el Dashboard
 * ==============================================================================
 */
class Producto {
    /** @var Database */
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener todos los productos unidos (JOIN) con su categoría
     */
    public function getAll($categoria_id = null, $search = null) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre, c.icono as categoria_icono 
                FROM productos p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                WHERE 1=1";
        $params = [];

        if ($categoria_id) {
            $sql .= " AND p.categoria_id = :cat_id";
            $params[':cat_id'] = $categoria_id;
        }

        if ($search) {
            $sql .= " AND (p.nombre LIKE :search OR p.codigo LIKE :search OR p.descripcion LIKE :search)";
            $params[':search'] = "%" . trim($search) . "%";
        }

        $sql .= " ORDER BY p.nombre ASC";
        return $this->db->query($sql, $params)->fetchAll();
    }

    /**
     * Obtener un producto individual por ID incluyendo información de categoría
     */
    public function getById(int $id) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.id = :id LIMIT 1";
        return $this->db->query($sql, [':id' => $id])->fetch();
    }

    /**
     * Crear un nuevo producto en el catálogo
     */
    public function create(array $data) {
        $sql = "INSERT INTO productos (categoria_id, codigo, nombre, descripcion, precio_compra, precio_venta, stock, stock_minimo, unidad_medida, imagen_url, estado) 
                VALUES (:categoria_id, :codigo, :nombre, :descripcion, :precio_compra, :precio_venta, :stock, :stock_minimo, :unidad_medida, :imagen_url, :estado)";
        return $this->db->query($sql, [
            ':categoria_id' => (int)$data['categoria_id'],
            ':codigo' => strtoupper(trim($data['codigo'])),
            ':nombre' => trim($data['nombre']),
            ':descripcion' => trim($data['descripcion'] ?? ''),
            ':precio_compra' => (float)$data['precio_compra'],
            ':precio_venta' => (float)$data['precio_venta'],
            ':stock' => (int)($data['stock'] ?? 0),
            ':stock_minimo' => (int)($data['stock_minimo'] ?? 5),
            ':unidad_medida' => trim($data['unidad_medida'] ?? 'Unidad'),
            ':imagen_url' => trim($data['imagen_url'] ?? 'default.png'),
            ':estado' => isset($data['estado']) ? (int)$data['estado'] : 1
        ]);
    }

    /**
     * Actualizar los datos generales de un producto
     */
    public function update(int $id, array $data) {
        $sql = "UPDATE productos 
                SET categoria_id = :categoria_id, codigo = :codigo, nombre = :nombre, descripcion = :descripcion, 
                    precio_compra = :precio_compra, precio_venta = :precio_venta, stock = :stock, 
                    stock_minimo = :stock_minimo, unidad_medida = :unidad_medida, imagen_url = :imagen_url, estado = :estado 
                WHERE id = :id";
        return $this->db->query($sql, [
            ':categoria_id' => (int)$data['categoria_id'],
            ':codigo' => strtoupper(trim($data['codigo'])),
            ':nombre' => trim($data['nombre']),
            ':descripcion' => trim($data['descripcion'] ?? ''),
            ':precio_compra' => (float)$data['precio_compra'],
            ':precio_venta' => (float)$data['precio_venta'],
            ':stock' => (int)$data['stock'],
            ':stock_minimo' => (int)($data['stock_minimo'] ?? 5),
            ':unidad_medida' => trim($data['unidad_medida'] ?? 'Unidad'),
            ':imagen_url' => trim($data['imagen_url'] ?? 'default.png'),
            ':estado' => isset($data['estado']) ? (int)$data['estado'] : 1,
            ':id' => (int)$id
        ]);
    }

    /**
     * Actualizar el stock automáticamente (llamado desde Movimientos)
     * $cambio puede ser positivo (+10 en ENTRADA) o negativo (-5 en SALIDA)
     */
    public function updateStock(int $id, int $cambio) {
        $sql = "UPDATE productos SET stock = stock + (:cambio) WHERE id = :id";
        return $this->db->query($sql, [':cambio' => (int)$cambio, ':id' => (int)$id]);
    }

    /**
     * Eliminar producto del sistema
     */
    public function delete(int $id) {
        $sql = "DELETE FROM productos WHERE id = :id";
        return $this->db->query($sql, [':id' => $id]);
    }

    /**
     * Verificar si el código SKU/Barras ya está registrado
     */
    public function existsByCodigo(string $codigo, ?int $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM productos WHERE LOWER(codigo) = LOWER(:codigo) AND id != :id";
            $res = $this->db->query($sql, [':codigo' => trim($codigo), ':id' => $excludeId])->fetch();
        } else {
            $sql = "SELECT COUNT(*) as count FROM productos WHERE LOWER(codigo) = LOWER(:codigo)";
            $res = $this->db->query($sql, [':codigo' => trim($codigo)])->fetch();
        }
        return $res && $res->count > 0;
    }

    /**
     * Obtener productos en alerta roja (stock actual <= stock mínimo)
     */
    public function getLowStock() {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.stock <= p.stock_minimo AND p.estado = 1 
                ORDER BY p.stock ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Obtener estadísticas consolidadas para las tarjetas del Dashboard
     */
    public function getSummaryStats() {
        $sql = "SELECT 
                    COUNT(*) as total_productos,
                    SUM(stock) as total_unidades,
                    SUM(stock * precio_venta) as valor_total_venta,
                    SUM(stock * precio_compra) as valor_total_compra,
                    SUM(CASE WHEN stock <= stock_minimo THEN 1 ELSE 0 END) as total_alerta_stock
                FROM productos WHERE estado = 1";
        return $this->db->query($sql)->fetch();
    }
}
