<?php
/**
 * ==============================================================================
 * MODELO: Proveedor
 * Gestión de acceso a datos para la tabla 'proveedores' usando PDO
 * ==============================================================================
 */
class Proveedor {
    /** @var Database */
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener todos los proveedores con el conteo de transacciones/movimientos
     */
    public function getAll() {
        $sql = "SELECT pr.*, COUNT(m.id) as total_movimientos 
                FROM proveedores pr 
                LEFT JOIN movimientos m ON pr.id = m.proveedor_id 
                GROUP BY pr.id 
                ORDER BY pr.nombre_empresa ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Obtener un proveedor por su ID
     */
    public function getById(int $id) {
        $sql = "SELECT * FROM proveedores WHERE id = :id LIMIT 1";
        return $this->db->query($sql, [':id' => $id])->fetch();
    }

    /**
     * Crear un nuevo proveedor
     */
    public function create(array $data) {
        $sql = "INSERT INTO proveedores (ruc, nombre_empresa, contacto_persona, email, telefono, direccion, estado) 
                VALUES (:ruc, :nombre_empresa, :contacto_persona, :email, :telefono, :direccion, :estado)";
        return $this->db->query($sql, [
            ':ruc' => trim($data['ruc']),
            ':nombre_empresa' => trim($data['nombre_empresa']),
            ':contacto_persona' => trim($data['contacto_persona'] ?? ''),
            ':email' => trim($data['email']),
            ':telefono' => trim($data['telefono']),
            ':direccion' => trim($data['direccion'] ?? ''),
            ':estado' => isset($data['estado']) ? (int)$data['estado'] : 1
        ]);
    }

    /**
     * Actualizar un proveedor existente
     */
    public function update(int $id, array $data) {
        $sql = "UPDATE proveedores 
                SET ruc = :ruc, nombre_empresa = :nombre_empresa, contacto_persona = :contacto_persona, 
                    email = :email, telefono = :telefono, direccion = :direccion, estado = :estado 
                WHERE id = :id";
        return $this->db->query($sql, [
            ':ruc' => trim($data['ruc']),
            ':nombre_empresa' => trim($data['nombre_empresa']),
            ':contacto_persona' => trim($data['contacto_persona'] ?? ''),
            ':email' => trim($data['email']),
            ':telefono' => trim($data['telefono']),
            ':direccion' => trim($data['direccion'] ?? ''),
            ':estado' => isset($data['estado']) ? (int)$data['estado'] : 1,
            ':id' => (int)$id
        ]);
    }

    /**
     * Eliminar un proveedor por ID
     */
    public function delete(int $id) {
        $sql = "DELETE FROM proveedores WHERE id = :id";
        return $this->db->query($sql, [':id' => $id]);
    }

    /**
     * Verificar si existe un proveedor con el mismo RUC/NIT
     */
    public function existsByRuc(string $ruc, ?int $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM proveedores WHERE ruc = :ruc AND id != :id";
            $res = $this->db->query($sql, [':ruc' => trim($ruc), ':id' => $excludeId])->fetch();
        } else {
            $sql = "SELECT COUNT(*) as count FROM proveedores WHERE ruc = :ruc";
            $res = $this->db->query($sql, [':ruc' => trim($ruc)])->fetch();
        }
        return $res && $res->count > 0;
    }
}
