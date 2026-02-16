<?php
/**
 * Instalador de Librerías para Modificar PDFs
 * Descarga e instala FPDF y FPDI automáticamente
 */

// Configuración
$baseDir = dirname(__DIR__);
$librariesDir = $baseDir . '/app/libraries';

// URLs de descarga
$fpdfUrl = 'http://www.fpdf.org/en/download/fpdf184.zip';
$fpdiUrl = 'https://github.com/Setasign/FPDI/archive/refs/heads/master.zip';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Instalador de Librerías PDF</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { padding: 40px; background: #f8f9fa; }
        .container { max-width: 900px; }
        .card { margin-bottom: 20px; }
        .step { background: #e3f2fd; padding: 20px; margin: 15px 0; border-left: 4px solid #2196F3; border-radius: 5px; }
        .success { background: #c8e6c9; border-left-color: #4CAF50; }
        .error { background: #ffcdd2; border-left-color: #f44336; }
        .warning { background: #fff3cd; border-left-color: #ffc107; }
        pre { background: #263238; color: #aed581; padding: 15px; border-radius: 5px; }
        .btn-lg { margin: 10px 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">📦 Instalador de Librerías para Modificar PDFs</h1>
        
        <?php
        // Verificar estado actual
        $fpdfInstalado = file_exists($librariesDir . '/fpdf/fpdf.php');
        $fpdiInstalado = file_exists($librariesDir . '/fpdi/src/autoload.php');
        $todoInstalado = $fpdfInstalado && $fpdiInstalado;
        
        if ($todoInstalado) {
            echo '<div class="alert alert-success">';
            echo '<h4>✅ ¡Librerías Ya Instaladas!</h4>';
            echo '<p>Las librerías FPDF y FPDI están instaladas correctamente.</p>';
            echo '<p>Puedes agregar timbres a los PDFs ahora.</p>';
            echo '<a href="' . $_SERVER['PHP_SELF'] . '?test=1" class="btn btn-primary">Probar Sistema</a>';
            echo '</div>';
        }
        
        // Probar sistema
        if (isset($_GET['test']) && $todoInstalado) {
            echo '<div class="card">';
            echo '<div class="card-body">';
            echo '<h5>🧪 Prueba del Sistema</h5>';
            
            require_once $librariesDir . '/fpdf/fpdf.php';
            require_once $librariesDir . '/fpdi/src/autoload.php';
            
            try {
                $pdf = new \setasign\Fpdi\Fpdi();
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 16);
                $pdf->Cell(0, 10, 'Prueba exitosa!', 0, 1, 'C');
                
                $testFile = dirname(__FILE__) . '/test_timbre.pdf';
                $pdf->Output('F', $testFile);
                
                if (file_exists($testFile)) {
                    echo '<div class="alert alert-success">';
                    echo '<strong>✅ Sistema Funcionando!</strong><br>';
                    echo 'Se generó un PDF de prueba correctamente.';
                    echo '</div>';
                    unlink($testFile);
                } else {
                    echo '<div class="alert alert-danger">❌ Error al generar PDF de prueba</div>';
                }
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">❌ Error: ' . $e->getMessage() . '</div>';
            }
            
            echo '</div></div>';
        }
        ?>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📋 Estado de Instalación</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>FPDF</strong> (Generar PDFs)</td>
                        <td>
                            <?php if ($fpdfInstalado): ?>
                                <span class="badge badge-success">✓ Instalado</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ No instalado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>FPDI</strong> (Modificar PDFs)</td>
                        <td>
                            <?php if ($fpdiInstalado): ?>
                                <span class="badge badge-success">✓ Instalado</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ No instalado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <?php if (!$todoInstalado): ?>
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0">⚙️ Instalación Manual (Recomendada)</h5>
            </div>
            <div class="card-body">
                <div class="step">
                    <h5>Paso 1: Descargar Librerías</h5>
                    <p>Descarga estos archivos ZIP:</p>
                    <a href="http://www.fpdf.org/en/download/fpdf184.zip" target="_blank" class="btn btn-primary">
                        Descargar FPDF
                    </a>
                    <a href="https://github.com/Setasign/FPDI/archive/refs/heads/master.zip" target="_blank" class="btn btn-primary">
                        Descargar FPDI
                    </a>
                </div>
                
                <div class="step">
                    <h5>Paso 2: Crear Carpeta</h5>
                    <p>Crea esta carpeta si no existe:</p>
                    <pre><?php echo $librariesDir; ?></pre>
                </div>
                
                <div class="step">
                    <h5>Paso 3: Extraer FPDF</h5>
                    <ol>
                        <li>Descomprime <code>fpdf184.zip</code></li>
                        <li>Copia la carpeta <code>fpdf184</code></li>
                        <li>Pégala en <code>app/libraries/</code></li>
                        <li>Renombra <code>fpdf184</code> a <code>fpdf</code></li>
                    </ol>
                    <p>Resultado: <code>app/libraries/fpdf/fpdf.php</code></p>
                </div>
                
                <div class="step">
                    <h5>Paso 4: Extraer FPDI</h5>
                    <ol>
                        <li>Descomprime <code>FPDI-master.zip</code></li>
                        <li>Copia la carpeta <code>FPDI-master</code></li>
                        <li>Pégala en <code>app/libraries/</code></li>
                        <li>Renombra <code>FPDI-master</code> a <code>fpdi</code></li>
                    </ol>
                    <p>Resultado: <code>app/libraries/fpdi/src/autoload.php</code></p>
                </div>
                
                <div class="step success">
                    <h5>Paso 5: Verificar</h5>
                    <p>Recarga esta página para verificar la instalación.</p>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-success">
                        Verificar Instalación
                    </a>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info">
            <h5>💡 Alternativa: Librerías Ya Incluidas</h5>
            <p>Si prefieres no instalar librerías, te proporcionaré un ZIP con todo incluido.</p>
            <p><strong>Ventaja:</strong> Solo descargar y extraer, sin pasos adicionales.</p>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">📖 ¿Qué hace esto?</h5>
            </div>
            <div class="card-body">
                <p>Una vez instaladas las librerías, el sistema podrá:</p>
                <ul>
                    <li>✅ Abrir PDFs existentes</li>
                    <li>✅ Agregar timbre visual directamente al PDF</li>
                    <li>✅ Guardar PDF modificado con timbre permanente</li>
                    <li>✅ Mantener PDF original intacto (opcionalmente)</li>
                </ul>
                
                <p><strong>El timbre incluirá:</strong></p>
                <ul>
                    <li>Sello verde "APROBADO"</li>
                    <li>Nombre del validador</li>
                    <li>Cargo</li>
                    <li>Fecha y hora exacta</li>
                    <li>Código de validación</li>
                </ul>
            </div>
        </div>
        
        <div class="alert alert-secondary">
            <strong>Nota de Seguridad:</strong> Las librerías se instalan localmente en tu servidor.
            No envían datos a terceros.
        </div>
    </div>
</body>
</html>
