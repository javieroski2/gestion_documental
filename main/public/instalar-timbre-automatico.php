<?php
/**
 * INSTALADOR AUTOMÁTICO DE LIBRERÍAS PDF
 * Descarga e instala FPDF y FPDI automáticamente
 */

set_time_limit(300); // 5 minutos máximo

$baseDir = dirname(__DIR__);
$librariesDir = $baseDir . '/app/libraries';
$tempDir = sys_get_temp_dir();

// Crear directorio de librerías si no existe
if (!file_exists($librariesDir)) {
    mkdir($librariesDir, 0777, true);
}

// Estado de instalación
$estado = [
    'fpdf' => file_exists($librariesDir . '/fpdf/fpdf.php'),
    'fpdi' => file_exists($librariesDir . '/fpdi/src/autoload.php')
];

// Función para descargar archivo
function descargarArchivo($url, $destino) {
    $ch = curl_init($url);
    $fp = fopen($destino, 'w+');
    
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    fclose($fp);
    
    return $httpCode == 200 && $result !== false;
}

// Verificar si ZipArchive está disponible
$zipDisponible = class_exists('ZipArchive');

// Procesar instalación automática
if (isset($_POST['instalar_auto'])) {
    if (!$zipDisponible) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?error=zip');
        exit;
    }
    
    $resultados = [];
    
    // INSTALAR FPDF
    if (!$estado['fpdf']) {
        $fpdfZip = $tempDir . '/fpdf.zip';
        $fpdfUrl = 'http://www.fpdf.org/en/download/fpdf184.zip';
        
        $resultados['fpdf_descarga'] = descargarArchivo($fpdfUrl, $fpdfZip);
        
        if ($resultados['fpdf_descarga'] && file_exists($fpdfZip)) {
            $zip = new ZipArchive;
            if ($zip->open($fpdfZip) === TRUE) {
                $zip->extractTo($librariesDir . '/fpdf_temp');
                $zip->close();
                
                // Mover archivos al lugar correcto
                $extracted = glob($librariesDir . '/fpdf_temp/*');
                if (!empty($extracted)) {
                    rename($extracted[0], $librariesDir . '/fpdf');
                    rmdir($librariesDir . '/fpdf_temp');
                    $resultados['fpdf_instalado'] = true;
                } else {
                    $resultados['fpdf_instalado'] = false;
                }
                
                unlink($fpdfZip);
            } else {
                $resultados['fpdf_instalado'] = false;
            }
        } else {
            $resultados['fpdf_instalado'] = false;
        }
    }
    
    // INSTALAR FPDI
    if (!$estado['fpdi']) {
        $fpdiZip = $tempDir . '/fpdi.zip';
        $fpdiUrl = 'https://github.com/Setasign/FPDI/archive/refs/heads/master.zip';
        
        $resultados['fpdi_descarga'] = descargarArchivo($fpdiUrl, $fpdiZip);
        
        if ($resultados['fpdi_descarga'] && file_exists($fpdiZip)) {
            $zip = new ZipArchive;
            if ($zip->open($fpdiZip) === TRUE) {
                $zip->extractTo($librariesDir . '/fpdi_temp');
                $zip->close();
                
                // Mover archivos al lugar correcto
                $extracted = glob($librariesDir . '/fpdi_temp/*');
                if (!empty($extracted)) {
                    rename($extracted[0], $librariesDir . '/fpdi');
                    rmdir($librariesDir . '/fpdi_temp');
                    $resultados['fpdi_instalado'] = true;
                } else {
                    $resultados['fpdi_instalado'] = false;
                }
                
                unlink($fpdiZip);
            } else {
                $resultados['fpdi_instalado'] = false;
            }
        } else {
            $resultados['fpdi_instalado'] = false;
        }
    }
    
    // Recargar para mostrar nuevo estado
    header('Location: ' . $_SERVER['PHP_SELF'] . '?resultado=' . urlencode(json_encode($resultados)));
    exit;
}

$resultado = isset($_GET['resultado']) ? json_decode(urldecode($_GET['resultado']), true) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 Instalador Automático - Librerías PDF</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { max-width: 900px; }
        .card { 
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
        }
        .btn-install {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 50px;
            box-shadow: 0 5px 20px rgba(40, 167, 69, 0.4);
            transition: all 0.3s;
        }
        .btn-install:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.6);
        }
        .status-badge {
            font-size: 24px;
            padding: 10px 20px;
            border-radius: 50px;
        }
        .step-number {
            width: 40px;
            height: 40px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
        .progress-bar {
            transition: width 0.3s;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .pulse { animation: pulse 2s infinite; }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center text-white mb-4">
            <h1 class="display-4"><i class="fas fa-download"></i> Instalador Automático</h1>
            <p class="lead">Librerías PDF para Timbres Digitales</p>
        </div>

        <!-- Estado Actual -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><i class="fas fa-check-circle"></i> Estado de Instalación</h4>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6 mb-3">
                        <h5>FPDF</h5>
                        <?php if ($estado['fpdf']): ?>
                            <span class="badge badge-success status-badge">
                                <i class="fas fa-check-circle"></i> Instalado
                            </span>
                        <?php else: ?>
                            <span class="badge badge-danger status-badge">
                                <i class="fas fa-times-circle"></i> No Instalado
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h5>FPDI</h5>
                        <?php if ($estado['fpdi']): ?>
                            <span class="badge badge-success status-badge">
                                <i class="fas fa-check-circle"></i> Instalado
                            </span>
                        <?php else: ?>
                            <span class="badge badge-danger status-badge">
                                <i class="fas fa-times-circle"></i> No Instalado
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($estado['fpdf'] && $estado['fpdi']): ?>
                    <div class="alert alert-success text-center mt-4">
                        <h4><i class="fas fa-trophy"></i> ¡Todo Instalado Correctamente!</h4>
                        <p class="mb-0">Ya puedes agregar timbres permanentes a los PDFs</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Resultados de Instalación -->
        <?php if ($resultado): ?>
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Resultado de Instalación</h5>
            </div>
            <div class="card-body">
                <?php if (isset($resultado['fpdf_descarga'])): ?>
                    <p>
                        <strong>FPDF Descarga:</strong>
                        <?php if ($resultado['fpdf_descarga']): ?>
                            <span class="badge badge-success">✓ Exitosa</span>
                        <?php else: ?>
                            <span class="badge badge-danger">✗ Falló</span>
                        <?php endif; ?>
                    </p>
                    <p>
                        <strong>FPDF Instalación:</strong>
                        <?php if ($resultado['fpdf_instalado']): ?>
                            <span class="badge badge-success">✓ Completa</span>
                        <?php else: ?>
                            <span class="badge badge-danger">✗ Falló</span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>

                <?php if (isset($resultado['fpdi_descarga'])): ?>
                    <p>
                        <strong>FPDI Descarga:</strong>
                        <?php if ($resultado['fpdi_descarga']): ?>
                            <span class="badge badge-success">✓ Exitosa</span>
                        <?php else: ?>
                            <span class="badge badge-danger">✗ Falló</span>
                        <?php endif; ?>
                    </p>
                    <p>
                        <strong>FPDI Instalación:</strong>
                        <?php if ($resultado['fpdi_instalado']): ?>
                            <span class="badge badge-success">✓ Completa</span>
                        <?php else: ?>
                            <span class="badge badge-danger">✗ Falló</span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Botón de Instalación -->
        <?php if (!$estado['fpdf'] || !$estado['fpdi']): ?>
        
        <!-- Error ZipArchive -->
        <?php if (isset($_GET['error']) && $_GET['error'] == 'zip'): ?>
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Error: ZipArchive No Disponible</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h5><i class="fas fa-tools"></i> La extensión ZIP de PHP no está habilitada</h5>
                    <p><strong>Solución Rápida (1 minuto):</strong></p>
                    <ol>
                        <li>Abrir XAMPP Control Panel</li>
                        <li>Click en "Config" → "PHP (php.ini)"</li>
                        <li>Buscar: <code>;extension=zip</code></li>
                        <li>Cambiar a: <code>extension=zip</code> (quitar el ;)</li>
                        <li>Guardar archivo</li>
                        <li>Reiniciar Apache en XAMPP</li>
                    </ol>
                    <p class="mb-0"><strong>O usa la INSTALACIÓN MANUAL más abajo ↓</strong></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($zipDisponible): ?>
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="fas fa-rocket"></i> Instalación Automática</h4>
            </div>
            <div class="card-body text-center py-5">
                <h5 class="mb-4">Haz click en el botón para instalar automáticamente</h5>
                
                <form method="POST" id="formInstalar">
                    <button type="submit" name="instalar_auto" class="btn btn-success btn-install pulse">
                        <i class="fas fa-magic"></i> INSTALAR AUTOMÁTICAMENTE
                    </button>
                </form>
        <?php else: ?>
        <div class="card">
            <div class="card-header bg-warning">
                <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Instalación Automática No Disponible</h4>
            </div>
            <div class="card-body text-center py-5">
                <div class="alert alert-info">
                    <p><strong>La instalación automática requiere la extensión ZIP de PHP.</strong></p>
                    <p>Usa la <strong>Instalación Manual</strong> más abajo, es igual de fácil.</p>
                </div>
        <?php endif; ?>

                <div id="loading" style="display: none;" class="mt-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Instalando...</span>
                    </div>
                    <p class="mt-3">Descargando e instalando... Por favor espera...</p>
                </div>

                <p class="text-muted mt-4 mb-0">
                    <small>Esto descargará FPDF y FPDI desde sus sitios oficiales<br>
                    Tiempo estimado: 30-60 segundos</small>
                </p>
            </div>
        </div>

        <script>
        document.getElementById('formInstalar').addEventListener('submit', function() {
            document.getElementById('loading').style.display = 'block';
            document.querySelector('.btn-install').style.display = 'none';
        });
        </script>
        <?php endif; ?>

        <!-- Instalación Manual (fallback) -->
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-wrench"></i> Instalación Manual (Si falla la automática)</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="step-number">1</div>
                    <strong>Descargar archivos:</strong>
                    <div class="ml-5 mt-2">
                        <a href="http://www.fpdf.org/en/download/fpdf184.zip" target="_blank" class="btn btn-primary btn-sm mr-2">
                            <i class="fas fa-download"></i> FPDF
                        </a>
                        <a href="https://github.com/Setasign/FPDI/archive/refs/heads/master.zip" target="_blank" class="btn btn-primary btn-sm">
                            <i class="fas fa-download"></i> FPDI
                        </a>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="step-number">2</div>
                    <strong>Extraer y copiar:</strong>
                    <div class="ml-5 mt-2">
                        <code>app/libraries/fpdf/fpdf.php</code><br>
                        <code>app/libraries/fpdi/src/autoload.php</code>
                    </div>
                </div>

                <div>
                    <div class="step-number">3</div>
                    <strong>Recargar esta página</strong>
                </div>
            </div>
        </div>

        <!-- Información -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-question-circle"></i> ¿Qué hace esto?</h5>
            </div>
            <div class="card-body">
                <p><strong>Una vez instalado, el sistema podrá:</strong></p>
                <ul>
                    <li><i class="fas fa-check text-success"></i> Abrir archivos PDF existentes</li>
                    <li><i class="fas fa-check text-success"></i> Agregar timbre visual al PDF</li>
                    <li><i class="fas fa-check text-success"></i> Guardar PDF con timbre permanente</li>
                    <li><i class="fas fa-check text-success"></i> Crear archivo "_firmado.pdf" automáticamente</li>
                </ul>

                <p class="mb-0"><strong>El timbre incluirá:</strong> Nombre validador, cargo, fecha, hora y código único</p>
            </div>
        </div>
    </div>
</body>
</html>
