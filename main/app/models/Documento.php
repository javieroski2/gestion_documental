<?php
/**
 * Documento Model
 * Sistema de Gestión Documental
 */

class Documento extends Model {
    
    protected $table = 'documentos';
    
    /**
     * Obtener documentos con información de usuario y categoría
     */
    public function getAllWithDetails() {
        $sql = "SELECT d.*, 
                       c.nombre as categoria_nombre,
                       u.nombre as usuario_nombre,
                       v.nombre as validador_nombre
                FROM {$this->table} d
                LEFT JOIN categorias c ON d.categoria_id = c.id
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                LEFT JOIN usuarios v ON d.validador_id = v.id
                ORDER BY d.fecha_creacion DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener documentos por usuario
     */
    public function getByUsuario($usuario_id) {
        $sql = "SELECT d.*, c.nombre as categoria_nombre
                FROM {$this->table} d
                LEFT JOIN categorias c ON d.categoria_id = c.id
                WHERE d.usuario_id = ?
                ORDER BY d.fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener documentos pendientes de validación
     */
    public function getPendientes() {
        $sql = "SELECT d.*, 
                       c.nombre as categoria_nombre,
                       u.nombre as usuario_nombre
                FROM {$this->table} d
                LEFT JOIN categorias c ON d.categoria_id = c.id
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                WHERE d.estado_validacion = 'pendiente'
                ORDER BY d.fecha_creacion ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Validar documento
     */
    public function validar($id, $estado, $validador_id, $observaciones = null) {
        $sql = "UPDATE {$this->table} 
                SET estado_validacion = ?,
                    validador_id = ?,
                    fecha_validacion = NOW(),
                    observaciones = ?
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$estado, $validador_id, $observaciones, $id]);
    }
    
    /**
     * Crear documento con archivo
     */
    public function crear($data) {
        $data['fecha_creacion'] = date('Y-m-d H:i:s');
        $data['estado_validacion'] = 'pendiente';
        return $this->insert($data);
    }
    
    /**
     * Contar por estado
     */
    public function contarPorEstado($estado) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE estado_validacion = ?");
        $stmt->execute([$estado]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
