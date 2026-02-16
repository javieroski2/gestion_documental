<?php
/**
 * VERIFICADOR SIMPLE DE LIBRERÍAS
 * No requiere ZipArchive ni extensiones especiales
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ Verificador de Librerías PDF</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { max-width: 800px; }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            margin-bottom: 20px;
        }
        .status-ok {
            background: #d4edda;
            border: 2px solid #28a745;
            padding: 20px;
            border-radius: 10px;
            margin: 10px 0;
        }
        .status-error {
            background: #f8d7da;
            border: 2px solid #dc3545;
            padding: 20px;
            border-radius: 10px;
            margin: 10px 0;
        }
        .big-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .code-box {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 14px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center text-white mb-4">
            <h1 class="display-4">✅ Verificador de Librerías PDF</h1>
            <p class="lead">Comprueba si FPDF y FPDI están instalados correctamente</p>
        </div>

        <div class="card">
            <div class="card-body">
                <?php
                $baseDir = dirname(__DIR__);
                $librariesDir = $baseDir . '/app/libraries';
                
                // Verificar FPDF
                $fpdfFile = $librariesDir . '/fpdf/fpdf.php';
                $fpdfOk = file_exists($fpdfFile);
                
                // Verificar FPDI
                $fpdiFile = $librariesDir . '/fpdi/src/autoload.php';
                $fpdiOk = file_exists($fpdiFile);
                
                $todoOk = $fpdfOk && $fpdiOk;
                ?>

                <h3 class="text-center mb-4">Estado de Instalación</h3>

                <!-- FPDF -->
                <div class="<?php echo $fpdfOk ? 'status-ok' : 'status-error'; ?>">
                    <div class="text-center big-icon">
                        <?php if ($fpdfOk): ?>
                            ✅
                        <?php else: ?>
                            ❌
                        <?php endif; ?>
                    </div>
                    <h4 class="text-center">
                        FPDF: 
                        <?php if ($fpdfOk): ?>
                            <span class="text-success">INSTALADO ✓</span>
                        <?php else: ?>
                            <span class="text-danger">NO INSTALADO ✗</span>
                        <?php endif; ?>
                    </h4>
                    <div class="code-box">
                        <?php echo $fpdfFile; ?>
                    </div>
                    <?php if ($fpdfOk): ?>
                        <p class="text-center mb-0 text-success">
                            <strong>El archivo existe y está en la ubicación correcta</strong>
                        </p>
                    <?php else: ?>
                        <p class="text-center mb-0 text-danger">
                            <strong>El archivo NO existe. Revisa la instalación.</strong>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- FPDI -->
                <div class="<?php echo $fpdiOk ? 'status-ok' : 'status-error'; ?>">
                    <div class="text-center big-icon">
                        <?php if ($fpdiOk): ?>
                            ✅
                        <?php else: ?>
                            ❌
                        <?php endif; ?>
                    </div>
                    <h4 class="text-center">
                        FPDI: 
                        <?php if ($fpdiOk): ?>
                            <span class="text-success">INSTALADO ✓</span>
                        <?php else: ?>
                            <span class="text-danger">NO INSTALADO ✗</span>
                        <?php endif; ?>
                    </h4>
                    <div class="code-box">
                        <?php echo $fpdiFile; ?>
                    </div>
                    <?php if ($fpdiOk): ?>
                        <p class="text-center mb-0 text-success">
                            <strong>El archivo existe y está en la ubicación correcta</strong>
                        </p>
                    <?php else: ?>
                        <p class="text-center mb-0 text-danger">
                            <strong>El archivo NO existe. Revisa la instalación.</strong>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Resultado Final -->
                <?php if ($todoOk): ?>
                    <div class="alert alert-success text-center mt-4">
                        <h3>🎉 ¡TODO INSTALADO CORRECTAMENTE!</h3>
                        <p class="mb-3">Las librerías están listas para usar.</p>
                        <p class="mb-0">
                            <strong>Siguiente paso:</strong> 
                            <a href="<?php echo URL_BASE ?? '../'; ?>" class="btn btn-success">
                                Ir al Sistema
                            </a>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger text-center mt-4">
                        <h3>⚠️ Instalación Incompleta</h3>
                        <p>Algunas librerías faltan. Sigue las instrucciones de instalación manual.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Instrucciones -->
        <?php if (!$todoOk): ?>
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0">📋 Instrucciones de Instalación Manual</h5>
            </div>
            <div class="card-body">
                <h6>PASO 1: Descargar</h6>
                <ul>
                    <li>
                        <a href="http://www.fpdf.org/en/download/fpdf184.zip" target="_blank">
                            Descargar FPDF
                        </a>
                    </li>
                    <li>
                        <a href="https://github.com/Setasign/FPDI/archive/refs/heads/master.zip" target="_blank">
                            Descargar FPDI
                        </a>
                    </li>
                </ul>

                <h6>PASO 2: Extraer y Copiar</h6>
                <p>Estructura correcta:</p>
                <div class="code-box">
app/libraries/fpdf/fpdf.php
app/libraries/fpdi/src/autoload.php
                </div>

                <h6>PASO 3: Verificar</h6>
                <p>
                    <button onclick="location.reload()" class="btn btn-primary">
                        🔄 Recargar esta Página
                    </button>
                </p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Info Adicional -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">ℹ️ Información</h5>
            </div>
            <div class="card-body">
                <p><strong>Directorio de librerías:</strong></p>
                <div class="code-box">
                    <?php echo $librariesDir; ?>
                </div>

                <p class="mb-0"><strong>Archivos que deben existir:</strong></p>
                <ul>
                    <li><code>fpdf/fpdf.php</code></li>
                    <li><code>fpdi/src/autoload.php</code></li>
                </ul>
            </div>
        </div>

        <!-- Prueba de Funcionamiento -->
        <?php if ($todoOk): ?>
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">🧪 Prueba de Funcionamiento</h5>
            </div>
            <div class="card-body">
                <?php
                try {
                    require_once $fpdfFile;
                    require_once $fpdiFile;
                    
                    // Intentar crear instancia
                    $pdf = new \setasign\Fpdi\Fpdi();
                    
                    echo '<div class="alert alert-success">';
                    echo '<strong>✅ Prueba Exitosa!</strong><br>';
                    echo 'Las librerías se cargaron correctamente y están funcionando.';
                    echo '</div>';
                    
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger">';
                    echo '<strong>❌ Error en Prueba:</strong><br>';
                    echo htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
