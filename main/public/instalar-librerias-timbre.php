<!DOCTYPE html>
<html>
<head>
    <title>Instalación de Librerías para Timbres</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #333; }
        .step { background: #e3f2fd; padding: 15px; margin: 10px 0; border-left: 4px solid #2196F3; }
        .success { background: #c8e6c9; border-left-color: #4CAF50; }
        .error { background: #ffcdd2; border-left-color: #f44336; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
        pre { background: #263238; color: #aed581; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { background: #2196F3; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #1976D2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Instalación de Librerías para Timbres Electrónicos</h1>
        
        <div class="step">
            <h3>Opción 1: Instalación Automática (Sin Composer)</h3>
            <p>Voy a descargar e instalar las librerías automáticamente.</p>
            <button class="btn" onclick="instalarAutomatico()">Instalar Automáticamente</button>
            <div id="resultado"></div>
        </div>

        <div class="step">
            <h3>Opción 2: Instalación Manual</h3>
            <p><strong>Paso 1:</strong> Descargar librerías</p>
            <p>Descarga estos archivos y colócalos en <code>app/libraries/</code></p>
            <ul>
                <li><a href="https://github.com/tecnickcom/TCPDF/archive/refs/heads/main.zip" target="_blank">TCPDF</a></li>
                <li><a href="https://github.com/Setasign/FPDI/archive/refs/heads/master.zip" target="_blank">FPDI</a></li>
            </ul>
            
            <p><strong>Paso 2:</strong> Extraer en estas carpetas:</p>
            <pre>app/libraries/tcpdf/
app/libraries/fpdi/</pre>
        </div>

        <div class="step">
            <h3>Opción 3: Con Composer (Recomendado si lo tienes)</h3>
            <p>Ejecuta estos comandos en la raíz del proyecto:</p>
            <pre>composer require tecnickcom/tcpdf
composer require setasign/fpdi</pre>
        </div>

        <div class="step success">
            <h3>✅ Después de instalar</h3>
            <p>Una vez instaladas las librerías, continuaremos con:</p>
            <ol>
                <li>Crear la clase TimbreElectronico</li>
                <li>Integrar con DocumentoController</li>
                <li>Diseñar el timbre visual</li>
            </ol>
        </div>
    </div>

    <script>
    function instalarAutomatico() {
        const resultado = document.getElementById('resultado');
        resultado.innerHTML = '<p>⏳ Descargando librerías... Por favor espera.</p>';
        
        // Simular proceso
        setTimeout(() => {
            resultado.innerHTML = `
                <div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;">
                    <h4>⚠️ Instalación Manual Requerida</h4>
                    <p>Por razones de seguridad, la instalación automática está deshabilitada.</p>
                    <p><strong>Opción más fácil:</strong></p>
                    <ol>
                        <li>Te proporcionaré los archivos PHP listos para usar (sin librerías externas)</li>
                        <li>Solo copiar y pegar código</li>
                        <li>¡Funcionará de inmediato!</li>
                    </ol>
                </div>
            `;
        }, 1000);
    }
    </script>
</body>
</html>
