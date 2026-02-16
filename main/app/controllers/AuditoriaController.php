<?php
/**
 * Auditoria Controller
 * Sistema de Gestión Documental
 */

class AuditoriaController extends Controller {
    
    private $auditoriaModel;
    
    public function __construct() {
        $this->requireRole([ROL_SUPER_ADMIN]);
        $this->auditoriaModel = $this->model('Auditoria');
    }
    
    /**
     * Listar auditoría
     */
    public function index() {
        $registros = [];
        
        try {
            $registros = $this->auditoriaModel->getHistorial(50);
        } catch (Exception $e) {
            // Si falla, mostrar vacío
        }
        
        $stats = [
            'total' => count($registros),
            'hoy' => 0,
            'semana' => 0,
            'usuarios_activos' => 1
        ];
        
        $this->view('auditoria/index', [
            'registros' => $registros,
            'stats' => $stats
        ]);
    }
    
    /**
     * Ver detalles
     */
    public function detalles($id) {
        try {
            $registro = $this->auditoriaModel->getById($id);
            
            if ($registro) {
                $this->json([
                    'success' => true,
                    'registro' => $registro
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);
            }
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al obtener detalles'
            ], 500);
        }
    }
}
