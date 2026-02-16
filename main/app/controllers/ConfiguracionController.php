<?php
/**
 * Configuracion Controller
 * Sistema de Gestión Documental
 */

class ConfiguracionController extends Controller {
    
    public function __construct() {
        $this->requireRole([ROL_SUPER_ADMIN]);
    }
    
    /**
     * Panel de configuración
     */
    public function index() {
        $stats = [
            'usuarios' => 1,
            'categorias' => 6,
            'documentos' => 0,
            'pendientes' => 0,
            'espacio' => '0 MB'
        ];
        
        $this->view('configuracion/index', ['stats' => $stats]);
    }
    
    /**
     * Backup de base de datos
     */
    public function backup() {
        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        $filepath = sys_get_temp_dir() . '/' . $filename;
        
        $command = sprintf(
            'mysqldump -h %s -u %s -p%s %s > %s 2>&1',
            DB_HOST,
            DB_USER,
            DB_PASSWORD,
            DB_NAME,
            $filepath
        );
        
        exec($command, $output, $return);
        
        if ($return === 0 && file_exists($filepath) && filesize($filepath) > 0) {
            // Registrar en auditoría
            $auditoriaModel = $this->model('Auditoria');
            $auditoriaModel->registrar('Generó backup de base de datos', 'configuracion', null);
            
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            unlink($filepath);
            exit;
        } else {
            $this->setFlash('danger', 'Error al generar backup. Verifica que mysqldump esté disponible.');
            $this->redirect('configuracion');
        }
    }
    
    /**
     * Limpiar caché del sistema
     */
    public function limpiarCache() {
        // Limpiar sesiones antiguas
        $sessionPath = session_save_path();
        if ($sessionPath && is_dir($sessionPath)) {
            $files = glob($sessionPath . '/sess_*');
            $cleaned = 0;
            foreach ($files as $file) {
                if (filemtime($file) < time() - 86400) { // Más de 24 horas
                    unlink($file);
                    $cleaned++;
                }
            }
        }
        
        // Limpiar archivos temporales
        $tempFiles = glob(sys_get_temp_dir() . '/backup_*.sql');
        foreach ($tempFiles as $file) {
            if (filemtime($file) < time() - 3600) { // Más de 1 hora
                unlink($file);
            }
        }
        
        // Registrar en auditoría
        $auditoriaModel = $this->model('Auditoria');
        $auditoriaModel->registrar('Limpió caché del sistema', 'configuracion', null);
        
        $this->setFlash('success', 'Caché limpiado exitosamente. Archivos eliminados: ' . ($cleaned ?? 0));
        $this->redirect('configuracion');
    }
    
    /**
     * Optimizar sistema
     */
    public function optimizar() {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Optimizar tablas
            $tablas = ['usuarios', 'documentos', 'categorias', 'auditoria'];
            $optimizadas = 0;
            
            foreach ($tablas as $tabla) {
                $stmt = $db->prepare("OPTIMIZE TABLE $tabla");
                if ($stmt->execute()) {
                    $optimizadas++;
                }
            }
            
            // Limpiar auditoría antigua (más de 90 días)
            $stmt = $db->prepare("DELETE FROM auditoria WHERE fecha < DATE_SUB(NOW(), INTERVAL 90 DAY)");
            $stmt->execute();
            $eliminados = $stmt->rowCount();
            
            // Registrar en auditoría
            $auditoriaModel = $this->model('Auditoria');
            $auditoriaModel->registrar('Optimizó el sistema', 'configuracion', null);
            
            $mensaje = "Sistema optimizado. Tablas: $optimizadas, Registros antiguos eliminados: $eliminados";
            $this->setFlash('success', $mensaje);
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error al optimizar: ' . $e->getMessage());
        }
        
        $this->redirect('configuracion');
    }
}
