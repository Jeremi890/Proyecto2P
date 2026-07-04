<?php
/**
 * ==============================================================================
 * MODELO: Categoria
 * Gestión de acceso a datos para la tabla 'categorias' usando PDO
 * ==============================================================================
 */
class Categoria {
    /** @var Database */
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener todas las categorías con el conteo de productos asociados
     */
    public function getAll() {
        $sql = "SELECT c.*, COUNT(p.id) as total_productos 
                FROM categorias c 
                LEFT JOIN productos p ON c.id = p.categoria_id 
                GROUP BY c.id 
                ORDER BY c.nombre ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Obtener una categoría por su ID
     */
    public function getById(int $id) {
        $sql = "SELECT * FROM categorias WHERE id = :id LIMIT 1";
        return $this->db->query($sql, [':id' => $id])->fetch();
    }

    /**
     * Crear una nueva categoría
     */
    public function create(array $data) {
        $sql = "INSERT INTO categorias (nombre, descripcion, icono, estado) 
                VALUES (:nombre, :descripcion, :icono, :estado)";
        return $this->db->query($sql, [
            ':nombre' => trim($data['nombre']),
            ':descripcion' => trim($data['descripcion'] ?? ''),
            ':icono' => trim($data['icono'] ?? 'folder'),
            ':estado' => isset($data['estado']) ? (int)$data['estado'] : 1
        ]);
    }

    /**
     * Actualizar una categoría existente
     */
    public function update(int $id, array $data) {
        $sql = "UPDATE categorias 
                SET nombre = :nombre, descripcion = :descripcion, icono = :icono, estado = :estado 
                WHERE id = :id";
        return $this->db->query($sql, [
            ':nombre' => trim($data['nombre']),
            ':descripcion' => trim($data['descripcion'] ?? ''),
            ':icono' => trim($data['icono'] ?? 'folder'),
            ':estado' => isset($data['estado']) ? (int)$data['estado'] : 1,
            ':id' => (int)$id
        ]);
    }

    /**
     * Eliminar una categoría por ID
     * Retorna falso si tiene productos asociados
     */
    public function delete(int $id) {
        // Verificar si la categoría tiene productos vinculados
        $checkSql = "SELECT COUNT(*) as count FROM productos WHERE categoria_id = :id";
        $result = $this->db->query($checkSql, [':id' => $id])->fetch();
        if ($result && $result->count > 0) {
            return false; // No se puede eliminar porque hay productos que dependen de ella
        }

        $sql = "DELETE FROM categorias WHERE id = :id";
        return $this->db->query($sql, [':id' => $id]);
    }

    /**
     * Verificar si existe una categoría con el mismo nombre (para evitar duplicados)
     */
    public function existsByName(string $nombre, ?int $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM categorias WHERE LOWER(nombre) = LOWER(:nombre) AND id != :id";
            $res = $this->db->query($sql, [':nombre' => trim($nombre), ':id' => $excludeId])->fetch();
        } else {
            $sql = "SELECT COUNT(*) as count FROM categorias WHERE LOWER(nombre) = LOWER(:nombre)";
            $res = $this->db->query($sql, [':nombre' => trim($nombre)])->fetch();
        }
        return $res && $res->count > 0;
    }
}
