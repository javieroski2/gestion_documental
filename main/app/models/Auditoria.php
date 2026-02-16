<?php
/**
 * Auditoria Model
 * Sistema de Gestión Documental
 */

class Auditoria extends Model {
    
    protected $table = 'auditoria';
    
    /**
     * Registrar acción
     */
    public function registrar($accion, $tabla = null, $registro_id = null, $datos_anteriores = null, $datos_nuevos = null) {
        $data = [
            'usuario_id' => $_SESSION['user_id'] ?? null,
            'accion' => $accion,
            'tabla' => $tabla,
            'registro_id' => $registro_id,
            'datos_anteriores' => $datos_anteriores ? json_encode($datos_anteriores) : null,
            'datos_nuevos' => $datos_nuevos ? json_encode($datos_nuevos) : null,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];
        
        return $this->insert($data);
    }
    
    /**
     * Obtener historial con datos de usuario
     */
    public function getHistorial($limite = 100) {
        $sql = "SELECT a.*, u.nombre as usuario_nombre, u.email as usuario_email 
                FROM {$this->table} a 
                LEFT JOIN usuarios u ON a.usuario_id = u.id 
                ORDER BY a.fecha DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener por tabla
     */
    public function getPorTabla($tabla, $limite = 50) {
        $sql = "SELECT a.*, u.nombre as usuario_nombre 
                FROM {$this->table} a 
                LEFT JOIN usuarios u ON a.usuario_id = u.id 
                WHERE a.tabla = ? 
                ORDER BY a.fecha DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tabla, $limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener por usuario
     */
    public function getPorUsuario($usuario_id, $limite = 50) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE usuario_id = ? 
                ORDER BY fecha DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario_id, $limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
