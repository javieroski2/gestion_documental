<?php
/**
 * User Model
 * Sistema de Gestión Documental
 */

class User extends Model {
    
    protected $table = 'usuarios';
    
    public function findByEmail($email) {
        return $this->findBy('email', $email);
    }
    
    public function getRolNombre($rol_id) {
        $stmt = $this->db->prepare("SELECT nombre FROM roles WHERE id = ?");
        $stmt->execute([$rol_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['nombre'] : '';
    }
    
    public function createUser($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['fecha_creacion'] = date('Y-m-d H:i:s');
        return $this->insert($data);
    }
    
    /**
     * Obtener usuarios con nombre de rol
     */
    public function getAllWithRole() {
        $sql = "SELECT u.*, r.nombre as rol_nombre 
                FROM {$this->table} u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                ORDER BY u.fecha_creacion DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todos los roles
     */
    public function getRoles() {
        $stmt = $this->db->query("SELECT * FROM roles ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cambiar estado del usuario
     */
    public function cambiarEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }
    
    /**
     * Actualizar usuario
     */
    public function updateUser($id, $data) {
        // Si hay password, encriptarlo
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Si no hay password, no actualizar ese campo
            unset($data['password']);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Verificar si el email ya existe (excepto el usuario actual)
     */
    public function emailExists($email, $except_id = null) {
        if ($except_id) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE email = ? AND id != ?");
            $stmt->execute([$email, $except_id]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE email = ?");
            $stmt->execute([$email]);
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
}
