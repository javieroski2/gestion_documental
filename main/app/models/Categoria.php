<?php
/**
 * Categoria Model
 * Sistema de Gestión Documental
 */

class Categoria extends Model {
    
    protected $table = 'categorias';
    
    /**
     * Obtener categorías activas
     */
    public function getActivas() {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE estado = 1 ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cambiar estado
     */
    public function cambiarEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }
    
    /**
     * Verificar si tiene documentos asociados
     */
    public function tieneDocumentos($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM documentos WHERE categoria_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
}
