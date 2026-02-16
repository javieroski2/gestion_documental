<?php
/**
 * Dashboard Controller
 * Sistema de Gestión Documental
 */

class DashboardController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $rol_id = $_SESSION['rol_id'];
        
        // Obtener estadísticas
        $stats = $this->obtenerEstadisticas();
        
        // Cargar vista según rol
        switch ($rol_id) {
            case ROL_SUPER_ADMIN:
                $this->view('dashboard/superadmin', ['stats' => $stats]);
                break;
            case ROL_ADMIN:
                $this->view('dashboard/admin', ['stats' => $stats]);
                break;
            case ROL_VALIDADOR:
                $this->view('dashboard/validador', ['stats' => $stats]);
                break;
            case ROL_GESTOR:
                $this->view('dashboard/gestor', ['stats' => $stats]);
                break;
            default:
                $this->redirect('auth/logout');
        }
    }
    
    /**
     * Obtener estadísticas del sistema
     */
    private function obtenerEstadisticas() {
        $stats = [
            'usuarios' => 0,
            'documentos' => 0,
            'pendientes' => 0,
            'aprobados' => 0,
            'rechazados' => 0,
            'documentos_recientes' => [],
            'documentos_por_mes' => []
        ];
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Estadísticas de usuarios
            $userModel = $this->model('User');
            $stats['usuarios'] = $userModel->count();
            
            // Estadísticas de documentos
            $documentoModel = $this->model('Documento');
            $stats['documentos'] = $documentoModel->count();
            $stats['pendientes'] = $documentoModel->contarPorEstado('pendiente');
            $stats['aprobados'] = $documentoModel->contarPorEstado('aprobado');
            $stats['rechazados'] = $documentoModel->contarPorEstado('rechazado');
            
            // Documentos recientes (últimos 5)
            $sql = "SELECT d.id, d.titulo, d.estado_validacion, d.fecha_creacion,
                           CONCAT(u.nombre, ' ', COALESCE(u.apellidos, '')) as usuario
                    FROM documentos d
                    LEFT JOIN usuarios u ON d.usuario_id = u.id
                    ORDER BY d.fecha_creacion DESC
                    LIMIT 5";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $stats['documentos_recientes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Documentos por mes (últimos 6 meses)
            $sql = "SELECT 
                        DATE_FORMAT(fecha_creacion, '%b') as mes,
                        COUNT(*) as total
                    FROM documentos
                    WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m')
                    ORDER BY fecha_creacion ASC
                    LIMIT 6";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $stats['documentos_por_mes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            // Si falla, dejar valores en 0
        }
        
        return $stats;
    }
}
