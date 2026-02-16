<?php
/**
 * Documento Controller
 * Gestión de documentos del sistema
 */

class DocumentoController extends Controller {
    
    private $documentoModel;
    private $categoriaModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->documentoModel = $this->model('Documento');
        $this->categoriaModel = $this->model('Categoria');
    }
    
    /**
     * Listar todos los documentos
     */
    public function index() {
        $documentos = $this->documentoModel->getAllWithDetails();
        $this->view('documentos/index', ['documentos' => $documentos]);
    }
    
    /**
     * Mis documentos (usuario actual)
     */
    public function misDocumentos() {
        $documentos = $this->documentoModel->getByUsuario($_SESSION['user_id']);
        $this->view('documentos/mis-documentos', ['documentos' => $documentos]);
    }
    
    /**
     * Documentos pendientes de validación
     */
    public function pendientes() {
        $this->requireRole([ROL_SUPER_ADMIN, ROL_ADMIN, ROL_VALIDADOR]);
        $documentos = $this->documentoModel->getPendientes();
        $this->view('documentos/pendientes', ['documentos' => $documentos]);
    }
    
    /**
     * Formulario de subir documento
     */
    public function subir() {
        $this->requireRole([ROL_SUPER_ADMIN, ROL_ADMIN, ROL_GESTOR]);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errors = $this->validarSubida($_FILES, $_POST);
            
            if (empty($errors)) {
                // Procesar archivo
                $archivo = $_FILES['archivo'];
                $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
                $rutaDestino = '../public/uploads/documentos/' . $nombreArchivo;
                
                if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                    // Guardar en BD
                    $data = [
                        'categoria_id' => $_POST['categoria_id'],
                        'usuario_id' => $_SESSION['user_id'],
                        'titulo' => trim($_POST['titulo']),
                        'descripcion' => trim($_POST['descripcion'] ?? ''),
                        'archivo' => $nombreArchivo,
                        'extension' => $extension,
                        'tamano' => $archivo['size']
                    ];
                    
                    if ($this->documentoModel->crear($data)) {
                        // Registrar en auditoría
                        $auditoriaModel = $this->model('Auditoria');
                        $auditoriaModel->registrar(
                            'Subió documento: ' . $data['titulo'],
                            'documentos',
                            null,
                            null,
                            json_encode($data)
                        );
                        
                        $this->setFlash('success', 'Documento subido exitosamente. Pendiente de validación.');
                        $this->redirect('documento/mis-documentos');
                    } else {
                        $errors['general'] = 'Error al guardar en base de datos';
                    }
                } else {
                    $errors['archivo'] = 'Error al subir el archivo';
                }
            }
            
            if (!empty($errors)) {
                $this->view('documentos/subir', [
                    'errors' => $errors,
                    'data' => $_POST,
                    'categorias' => $this->categoriaModel->getActivas()
                ]);
                return;
            }
        }
        
        $this->view('documentos/subir', [
            'categorias' => $this->categoriaModel->getActivas()
        ]);
    }
    
    /**
     * Validar documento (aprobar/rechazar)
     */
    public function validar($id) {
        $this->requireRole([ROL_SUPER_ADMIN, ROL_ADMIN, ROL_VALIDADOR]);
        
        $documento = $this->documentoModel->getById($id);
        
        if (!$documento) {
            $this->setFlash('error', 'Documento no encontrado');
            $this->redirect('documento/pendientes');
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $estado = $_POST['estado']; // 'aprobado' o 'rechazado'
            $observaciones = trim($_POST['observaciones'] ?? '');
            
            if ($this->documentoModel->validar($id, $estado, $_SESSION['user_id'], $observaciones)) {
                // Registrar en auditoría
                $auditoriaModel = $this->model('Auditoria');
                $accion = $estado == 'aprobado' ? 'Aprobó documento' : 'Rechazó documento';
                $auditoriaModel->registrar(
                    $accion . ': ' . $documento['titulo'],
                    'documentos',
                    $id,
                    json_encode(['estado' => $documento['estado_validacion']]),
                    json_encode(['estado' => $estado, 'observaciones' => $observaciones])
                );
                
                // Si fue aprobado, generar certificado de validación
                if ($estado == 'aprobado') {
                    require_once '../app/helpers/TimbreElectronico.php';
                    
                    // Obtener datos completos del documento y validador
                    $docCompleto = $this->documentoModel->getById($id);
                    $userModel = $this->model('User');
                    $validador = $userModel->getById($_SESSION['user_id']);
                    
                    // Agregar nombre de usuario al documento
                    $docCompleto['usuario_nombre'] = $this->obtenerNombreUsuario($docCompleto['usuario_id']);
                    $docCompleto['fecha_validacion'] = date('Y-m-d H:i:s');
                    
                    // Generar certificado HTML
                    $certificado = TimbreElectronico::generarCertificado($docCompleto, $validador);
                    
                    // Guardar certificado en carpeta
                    $rutaCertificado = '../public/uploads/certificados/';
                    if (!file_exists($rutaCertificado)) {
                        mkdir($rutaCertificado, 0777, true);
                    }
                    
                    $nombreCertificado = 'certificado_' . $id . '_' . time() . '.html';
                    file_put_contents($rutaCertificado . $nombreCertificado, $certificado);
                    
                    // Si es PDF, agregar timbre al archivo
                    if ($docCompleto['extension'] == 'pdf') {
                        require_once '../app/helpers/TimbrePDF.php';
                        
                        $rutaPDF = '../public/uploads/documentos/' . $docCompleto['archivo'];
                        
                        if (file_exists($rutaPDF) && TimbrePDF::verificarLibrerias()) {
                            $pdfFirmado = TimbrePDF::agregarTimbreAPDF($rutaPDF, $docCompleto, $validador);
                            
                            if ($pdfFirmado) {
                                // PDF con timbre creado exitosamente
                                // Guardado como: archivo_firmado.pdf
                                $this->setFlash('info', 'Se generó una versión del PDF con timbre digital');
                            }
                        }
                    }
                }
                
                $mensaje = $estado == 'aprobado' ? 'Documento aprobado exitosamente' : 'Documento rechazado';
                $this->setFlash('success', $mensaje);
                $this->redirect('documento/pendientes');
            } else {
                $this->setFlash('error', 'Error al validar documento');
            }
        }
        
        $this->view('documentos/validar', ['documento' => $documento]);
    }
    
    /**
     * Descargar documento
     */
    public function descargar($id) {
        $documento = $this->documentoModel->getById($id);
        
        if (!$documento) {
            die('Documento no encontrado');
        }
        
        // Verificar permisos
        $puedeDescargar = false;
        
        // Super admin y admin pueden descargar todo
        if ($_SESSION['rol_id'] == ROL_SUPER_ADMIN || $_SESSION['rol_id'] == ROL_ADMIN) {
            $puedeDescargar = true;
        }
        // Validador puede descargar documentos pendientes
        elseif ($_SESSION['rol_id'] == ROL_VALIDADOR && $documento['estado_validacion'] == 'pendiente') {
            $puedeDescargar = true;
        }
        // Usuario puede descargar sus propios documentos
        elseif ($documento['usuario_id'] == $_SESSION['user_id']) {
            $puedeDescargar = true;
        }
        // Cualquiera puede descargar documentos aprobados
        elseif ($documento['estado_validacion'] == 'aprobado') {
            $puedeDescargar = true;
        }
        
        if (!$puedeDescargar) {
            die('No tiene permisos para descargar este documento');
        }
        
        $rutaArchivo = '../public/uploads/documentos/' . $documento['archivo'];
        
        if (file_exists($rutaArchivo)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $documento['titulo'] . '.' . $documento['extension'] . '"');
            header('Content-Length: ' . filesize($rutaArchivo));
            readfile($rutaArchivo);
            exit;
        } else {
            die('Archivo no encontrado');
        }
    }
    
    /**
     * Eliminar documento
     */
    public function eliminar($id) {
        $documento = $this->documentoModel->getById($id);
        
        if (!$documento) {
            $this->json(['success' => false, 'message' => 'Documento no encontrado'], 404);
        }
        
        // Solo el dueño o super admin pueden eliminar
        if ($documento['usuario_id'] != $_SESSION['user_id'] && $_SESSION['rol_id'] != ROL_SUPER_ADMIN) {
            $this->json(['success' => false, 'message' => 'No tiene permisos para eliminar este documento'], 403);
        }
        
        // Eliminar archivo físico
        $rutaArchivo = '../public/uploads/documentos/' . $documento['archivo'];
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }
        
        // Eliminar de BD
        if ($this->documentoModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'Documento eliminado exitosamente']);
        } else {
            $this->json(['success' => false, 'message' => 'Error al eliminar documento'], 500);
        }
    }
    
    /**
     * Validar subida de documento
     */
    private function validarSubida($files, $data) {
        $errors = [];
        
        // Validar título
        if (empty($data['titulo'])) {
            $errors['titulo'] = 'El título es requerido';
        }
        
        // Validar categoría
        if (empty($data['categoria_id'])) {
            $errors['categoria_id'] = 'Debe seleccionar una categoría';
        }
        
        // Validar archivo
        if (empty($files['archivo']['name'])) {
            $errors['archivo'] = 'Debe seleccionar un archivo';
        } else {
            $archivo = $files['archivo'];
            
            // Validar tamaño
            if ($archivo['size'] > MAX_FILE_SIZE) {
                $errors['archivo'] = 'El archivo excede el tamaño máximo permitido (' . (MAX_FILE_SIZE / 1048576) . ' MB)';
            }
            
            // Validar extensión
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, ALLOWED_EXTENSIONS)) {
                $errors['archivo'] = 'Extensión no permitida. Extensiones válidas: ' . implode(', ', ALLOWED_EXTENSIONS);
            }
            
            // Validar errores de subida
            if ($archivo['error'] !== UPLOAD_ERR_OK) {
                $errors['archivo'] = 'Error al subir el archivo';
            }
        }
        
        return $errors;
    }
    
    /**
     * Ver certificado de validación
     */
    public function certificado($id) {
        $documento = $this->documentoModel->getById($id);
        
        if (!$documento) {
            die('Documento no encontrado');
        }
        
        if ($documento['estado_validacion'] != 'aprobado') {
            die('Este documento no ha sido aprobado aún');
        }
        
        // Verificar si existe certificado guardado
        $rutaCertificado = '../public/uploads/certificados/certificado_' . $id . '_*.html';
        $archivos = glob($rutaCertificado);
        
        if (!empty($archivos)) {
            // Mostrar certificado existente
            $contenido = file_get_contents($archivos[0]);
            echo $contenido;
        } else {
            // Generar certificado nuevo
            require_once '../app/helpers/TimbreElectronico.php';
            
            $userModel = $this->model('User');
            $validador = $userModel->getById($documento['validador_id']);
            $documento['usuario_nombre'] = $this->obtenerNombreUsuario($documento['usuario_id']);
            
            $certificado = TimbreElectronico::generarCertificado($documento, $validador);
            echo $certificado;
        }
        exit;
    }
    
    /**
     * Descargar PDF con timbre
     */
    public function descargarFirmado($id) {
        $documento = $this->documentoModel->getById($id);
        
        if (!$documento) {
            die('Documento no encontrado');
        }
        
        if ($documento['estado_validacion'] != 'aprobado') {
            die('Solo se pueden descargar PDFs firmados de documentos aprobados');
        }
        
        $rutaOriginal = '../public/uploads/documentos/' . $documento['archivo'];
        $rutaFirmado = str_replace('.pdf', '_firmado.pdf', $rutaOriginal);
        
        // Si existe la versión firmada, descargarla
        if (file_exists($rutaFirmado)) {
            $nombreDescarga = str_replace('.pdf', '_firmado.pdf', $documento['titulo']);
            
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $nombreDescarga . '"');
            header('Content-Length: ' . filesize($rutaFirmado));
            readfile($rutaFirmado);
            exit;
        } else {
            die('No existe versión firmada de este documento. Instala las librerías PDF y re-aprueba el documento.');
        }
    }
    
    /**
     * Obtener nombre completo de usuario
     */
    private function obtenerNombreUsuario($usuarioId) {
        $userModel = $this->model('User');
        $usuario = $userModel->getById($usuarioId);
        
        if ($usuario) {
            return $usuario['nombre'] . ' ' . ($usuario['apellidos'] ?? '');
        }
        
        return 'Usuario desconocido';
    }
}
