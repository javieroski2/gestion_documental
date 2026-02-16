<?php
/**
 * Reporte Controller
 * Sistema de Gestión Documental
 */

class ReporteController extends Controller {
    
    private $documentoModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->requireRole([ROL_SUPER_ADMIN, ROL_ADMIN, ROL_VALIDADOR]);
        $this->documentoModel = $this->model('Documento');
    }
    
    /**
     * Vista principal de reportes
     */
    public function index() {
        $this->view('reportes/index');
    }
    
    /**
     * Reporte de documentos por estado
     */
    public function porEstado() {
        $stats = [
            'pendiente' => $this->documentoModel->contarPorEstado('pendiente'),
            'aprobado' => $this->documentoModel->contarPorEstado('aprobado'),
            'rechazado' => $this->documentoModel->contarPorEstado('rechazado')
        ];
        
        $this->view('reportes/por-estado', ['stats' => $stats]);
    }
    
    /**
     * Reporte de tiempos de validación
     */
    public function tiemposValidacion() {
        // Crear conexión directa a BD
        $database = new Database();
        $db = $database->getConnection();
        
        $sql = "SELECT 
                    d.id,
                    d.titulo,
                    d.fecha_creacion as fecha_ingreso,
                    d.fecha_validacion,
                    DATEDIFF(d.fecha_validacion, d.fecha_creacion) as dias_proceso,
                    d.estado_validacion,
                    u.nombre as usuario,
                    v.nombre as validador,
                    c.nombre as categoria
                FROM documentos d
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                LEFT JOIN usuarios v ON d.validador_id = v.id
                LEFT JOIN categorias c ON d.categoria_id = c.id
                WHERE d.estado_validacion IN ('aprobado', 'rechazado')
                ORDER BY d.fecha_validacion DESC
                LIMIT 100";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular promedios
        $tiempos = array_column($documentos, 'dias_proceso');
        $promedio = count($tiempos) > 0 ? round(array_sum($tiempos) / count($tiempos), 1) : 0;
        
        $this->view('reportes/tiempos-validacion', [
            'documentos' => $documentos,
            'promedio' => $promedio
        ]);
    }
    
    /**
     * Reporte de documentos por mes
     */
    public function porMes() {
        $database = new Database();
        $db = $database->getConnection();
        
        $sql = "SELECT 
                    DATE_FORMAT(fecha_creacion, '%Y-%m') as mes,
                    COUNT(*) as total,
                    SUM(CASE WHEN estado_validacion = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado_validacion = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                    SUM(CASE WHEN estado_validacion = 'rechazado' THEN 1 ELSE 0 END) as rechazados
                FROM documentos
                GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m')
                ORDER BY mes DESC
                LIMIT 12";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->view('reportes/por-mes', ['datos' => $datos]);
    }
    
    /**
     * Reporte de documentos por usuario
     */
    public function porUsuario() {
        $database = new Database();
        $db = $database->getConnection();
        
        $sql = "SELECT 
                    u.id,
                    u.nombre,
                    u.apellidos,
                    COUNT(d.id) as total_documentos,
                    SUM(CASE WHEN d.estado_validacion = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN d.estado_validacion = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                    SUM(CASE WHEN d.estado_validacion = 'rechazado' THEN 1 ELSE 0 END) as rechazados
                FROM usuarios u
                LEFT JOIN documentos d ON u.id = d.usuario_id
                WHERE u.estado = 1
                GROUP BY u.id, u.nombre, u.apellidos
                ORDER BY total_documentos DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->view('reportes/por-usuario', ['usuarios' => $usuarios]);
    }
    
    /**
     * Reporte de documentos por categoría
     */
    public function porCategoria() {
        $database = new Database();
        $db = $database->getConnection();
        
        $sql = "SELECT 
                    c.nombre as categoria,
                    COUNT(d.id) as total,
                    SUM(CASE WHEN d.estado_validacion = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN d.estado_validacion = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                    SUM(CASE WHEN d.estado_validacion = 'rechazado' THEN 1 ELSE 0 END) as rechazados
                FROM categorias c
                LEFT JOIN documentos d ON c.id = d.categoria_id
                WHERE c.estado = 1
                GROUP BY c.id, c.nombre
                ORDER BY total DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->view('reportes/por-categoria', ['categorias' => $categorias]);
    }
    
    /**
     * Exportar reporte a Excel (XLSX)
     */
    public function exportar($tipo = 'general') {
        $database = new Database();
        $db = $database->getConnection();
        $filename = 'reporte_' . $tipo . '_' . date('Y-m-d_His') . '.xlsx';
        
        // Crear archivo temporal CSV (Excel lo abre como XLSX)
        $tempFile = sys_get_temp_dir() . '/' . uniqid() . '.csv';
        $output = fopen($tempFile, 'w');
        
        // BOM UTF-8 para que Excel lo abra correctamente
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        switch ($tipo) {
            case 'tiempos':
                fputcsv($output, ['ID', 'Título', 'Fecha Ingreso', 'Fecha Validación', 'Días Proceso', 'Estado', 'Usuario', 'Validador']);
                
                $sql = "SELECT 
                            d.id, 
                            d.titulo, 
                            DATE_FORMAT(d.fecha_creacion, '%d/%m/%Y') as fecha_ingreso,
                            DATE_FORMAT(d.fecha_validacion, '%d/%m/%Y %H:%i') as fecha_validacion,
                            DATEDIFF(d.fecha_validacion, d.fecha_creacion) as dias,
                            d.estado_validacion, 
                            CONCAT(u.nombre, ' ', COALESCE(u.apellidos, '')) as usuario, 
                            CONCAT(v.nombre, ' ', COALESCE(v.apellidos, '')) as validador
                        FROM documentos d
                        LEFT JOIN usuarios u ON d.usuario_id = u.id
                        LEFT JOIN usuarios v ON d.validador_id = v.id
                        WHERE d.estado_validacion IN ('aprobado', 'rechazado')
                        ORDER BY d.fecha_validacion DESC";
                break;
                
            case 'usuarios':
                fputcsv($output, ['Usuario', 'Total Documentos', 'Pendientes', 'Aprobados', 'Rechazados']);
                
                $sql = "SELECT 
                            CONCAT(u.nombre, ' ', COALESCE(u.apellidos, '')) as usuario,
                            COUNT(d.id) as total,
                            SUM(CASE WHEN d.estado_validacion = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                            SUM(CASE WHEN d.estado_validacion = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                            SUM(CASE WHEN d.estado_validacion = 'rechazado' THEN 1 ELSE 0 END) as rechazados
                        FROM usuarios u
                        LEFT JOIN documentos d ON u.id = d.usuario_id
                        WHERE u.estado = 1
                        GROUP BY u.id, u.nombre, u.apellidos
                        ORDER BY total DESC";
                break;
                
            case 'categorias':
                fputcsv($output, ['Categoría', 'Total', 'Pendientes', 'Aprobados', 'Rechazados']);
                
                $sql = "SELECT 
                            c.nombre as categoria,
                            COUNT(d.id) as total,
                            SUM(CASE WHEN d.estado_validacion = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                            SUM(CASE WHEN d.estado_validacion = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                            SUM(CASE WHEN d.estado_validacion = 'rechazado' THEN 1 ELSE 0 END) as rechazados
                        FROM categorias c
                        LEFT JOIN documentos d ON c.id = d.categoria_id
                        WHERE c.estado = 1
                        GROUP BY c.id, c.nombre
                        ORDER BY total DESC";
                break;
                
            case 'estados':
                fputcsv($output, ['Estado', 'Cantidad', 'Porcentaje']);
                
                $sql = "SELECT 
                            estado_validacion as estado,
                            COUNT(*) as cantidad,
                            CONCAT(ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM documentos)), 1), '%') as porcentaje
                        FROM documentos
                        GROUP BY estado_validacion
                        ORDER BY cantidad DESC";
                break;
                
            default:
                fputcsv($output, ['ID', 'Título', 'Categoría', 'Usuario', 'Estado', 'Fecha Creación', 'Tamaño']);
                
                $sql = "SELECT 
                            d.id, 
                            d.titulo, 
                            c.nombre as categoria, 
                            CONCAT(u.nombre, ' ', COALESCE(u.apellidos, '')) as usuario,
                            d.estado_validacion, 
                            DATE_FORMAT(d.fecha_creacion, '%d/%m/%Y') as fecha,
                            CONCAT(ROUND(d.tamano / 1024, 1), ' KB') as tamano
                        FROM documentos d
                        LEFT JOIN categorias c ON d.categoria_id = c.id
                        LEFT JOIN usuarios u ON d.usuario_id = u.id
                        ORDER BY d.fecha_creacion DESC";
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        
        // Registrar en auditoría
        $auditoriaModel = $this->model('Auditoria');
        $auditoriaModel->registrar('Exportó reporte: ' . $tipo, 'reportes', null);
        
        // Enviar archivo como Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tempFile));
        readfile($tempFile);
        unlink($tempFile);
        exit;
    }
}
