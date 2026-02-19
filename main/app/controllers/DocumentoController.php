<?php
/**
 * Documento Controller
 * Gestión de documentos del sistema
 */

class DocumentoController extends Controller {

    private const MAX_ARCHIVOS_POR_SUBIDA = 5;
    
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
                $archivos = $this->normalizarArchivosSubidos($_FILES['archivo']);
                $auditoriaModel = $this->model('Auditoria');
                $documentosGuardados = 0;

                foreach ($archivos as $index => $archivo) {
                    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                    $nombreArchivo = uniqid() . '_' . time() . '_' . $index . '.' . $extension;
                    $rutaDestino = '../public/uploads/documentos/' . $nombreArchivo;

                    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                        $errors['archivo'] = 'Error al subir uno de los archivos seleccionados';
                        break;
                    }

                    $data = [
                        'categoria_id' => $_POST['categoria_id'],
                        'usuario_id' => $_SESSION['user_id'],
                        'titulo' => $this->generarTituloDocumento(trim($_POST['titulo']), $archivo['name'], $index, count($archivos)),
                        'descripcion' => trim($_POST['descripcion'] ?? ''),
                        'archivo' => $nombreArchivo,
                        'extension' => $extension,
                        'tamano' => $archivo['size']
                    ];

                    if (!$this->documentoModel->crear($data)) {
                        @unlink($rutaDestino);
                        $errors['general'] = 'Error al guardar en base de datos';
                        break;
                    }

                    $auditoriaModel->registrar(
                        'Subió documento: ' . $data['titulo'],
                        'documentos',
                        null,
                        null,
                        json_encode($data)
                    );

                    $documentosGuardados++;
                }

                if (empty($errors) && $documentosGuardados > 0) {
                    $mensaje = $documentosGuardados === 1
                        ? 'Documento subido exitosamente. Pendiente de validación.'
                        : 'Se subieron ' . $documentosGuardados . ' documentos exitosamente. Pendientes de validación.';

                    $this->setFlash('success', $mensaje);
                    $this->redirect('documento/mis-documentos');
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
        
        $archivos = $this->normalizarArchivosSubidos($files['archivo'] ?? []);

        // Validar título
        if (empty($data['titulo']) && count($archivos) === 1) {
            $errors['titulo'] = 'El título es requerido';
        }
        
        // Validar categoría
        if (empty($data['categoria_id'])) {
            $errors['categoria_id'] = 'Debe seleccionar una categoría';
        }
        
        // Validar archivo
        if (empty($archivos)) {
            $errors['archivo'] = 'Debe seleccionar un archivo';
        } else {
            if (count($archivos) > self::MAX_ARCHIVOS_POR_SUBIDA) {
                $errors['archivo'] = 'Solo puede subir hasta ' . self::MAX_ARCHIVOS_POR_SUBIDA . ' archivos por envío';
            }

            foreach ($archivos as $archivo) {
                // Validar tamaño
                if ($archivo['size'] > MAX_FILE_SIZE) {
                    $errors['archivo'] = 'Uno de los archivos excede el tamaño máximo permitido (' . (MAX_FILE_SIZE / 1048576) . ' MB)';
                    break;
                }

                // Validar extensión
                $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                if (!in_array($extension, ALLOWED_EXTENSIONS)) {
                    $errors['archivo'] = 'Extensión no permitida. Extensiones válidas: ' . implode(', ', ALLOWED_EXTENSIONS);
                    break;
                }

                // Validar errores de subida
                if ($archivo['error'] !== UPLOAD_ERR_OK) {
                    $errors['archivo'] = 'Error al subir uno de los archivos';
                    break;
                }
            }
        }
        
        return $errors;
    }

    /**
     * Normalizar el arreglo de archivos para soportar carga múltiple
     */
    private function normalizarArchivosSubidos($archivoInput) {
        $archivos = [];

        if (empty($archivoInput) || empty($archivoInput['name'])) {
            return $archivos;
        }

        if (is_array($archivoInput['name'])) {
            foreach ($archivoInput['name'] as $index => $name) {
                if (empty($name)) {
                    continue;
                }

                $archivos[] = [
                    'name' => $name,
                    'type' => $archivoInput['type'][$index] ?? '',
                    'tmp_name' => $archivoInput['tmp_name'][$index] ?? '',
                    'error' => $archivoInput['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                    'size' => $archivoInput['size'][$index] ?? 0
                ];
            }

            return $archivos;
        }

        $archivos[] = $archivoInput;
        return $archivos;
    }

    /**
     * Generar título para el documento según el orden de carga
     */
    private function generarTituloDocumento($tituloBase, $nombreOriginal, $index, $totalArchivos) {
        if ($totalArchivos === 1) {
            return !empty($tituloBase)
                ? $tituloBase
                : pathinfo($nombreOriginal, PATHINFO_FILENAME);
        }

        if (!empty($tituloBase)) {
            return $tituloBase . ' (' . ($index + 1) . ')';
        }

        return pathinfo($nombreOriginal, PATHINFO_FILENAME);
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
