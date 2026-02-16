<?php
/**
 * Timbre Electrónico
 * Genera certificados de validación para documentos aprobados
 */

class TimbreElectronico {
    
    /**
     * Generar certificado HTML de validación
     */
    public static function generarCertificado($documento, $validador) {
        $codigoValidacion = self::generarCodigoUnico($documento['id']);
        
        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificado de Validación</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            padding: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .certificado {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            position: relative;
            border: 3px solid #28a745;
        }
        .sello {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 150px;
            height: 150px;
            border: 5px solid #28a745;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            font-weight: bold;
            text-align: center;
            transform: rotate(-15deg);
        }
        .sello .estado {
            font-size: 24px;
            text-transform: uppercase;
        }
        .sello .fecha {
            font-size: 12px;
            margin-top: 5px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
            font-size: 32px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .subtitulo {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
            font-size: 18px;
        }
        .info-documento {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            margin: 30px 0;
            border-left: 5px solid #28a745;
        }
        .info-documento h3 {
            color: #28a745;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .campo {
            margin: 15px 0;
            display: flex;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        .campo .label {
            font-weight: bold;
            color: #495057;
            min-width: 200px;
        }
        .campo .valor {
            color: #212529;
            flex: 1;
        }
        .timbre-datos {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            padding: 25px;
            border-radius: 10px;
            margin: 30px 0;
            border: 2px dashed #2196F3;
        }
        .timbre-datos h3 {
            color: #1976D2;
            margin-bottom: 15px;
            text-align: center;
        }
        .firma {
            margin-top: 40px;
            text-align: center;
        }
        .firma .linea {
            border-top: 2px solid #333;
            width: 300px;
            margin: 0 auto 10px;
        }
        .codigo-validacion {
            background: #212529;
            color: #28a745;
            padding: 15px;
            text-align: center;
            font-family: monospace;
            font-size: 18px;
            letter-spacing: 3px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="certificado">
        <div class="sello">
            <div class="estado">✓ APROBADO</div>
            <div class="fecha">' . date('d/m/Y', strtotime($documento['fecha_validacion'])) . '</div>
        </div>
        
        <h1>🔐 Certificado de Validación</h1>
        <p class="subtitulo">Documento Oficialmente Aprobado</p>
        
        <div class="info-documento">
            <h3>📄 Información del Documento</h3>
            <div class="campo">
                <span class="label">Título:</span>
                <span class="valor">' . htmlspecialchars($documento['titulo']) . '</span>
            </div>
            <div class="campo">
                <span class="label">Categoría:</span>
                <span class="valor">' . htmlspecialchars($documento['categoria'] ?? 'General') . '</span>
            </div>
            <div class="campo">
                <span class="label">Fecha de Ingreso:</span>
                <span class="valor">' . date('d/m/Y H:i', strtotime($documento['fecha_creacion'])) . '</span>
            </div>
            <div class="campo">
                <span class="label">Usuario Emisor:</span>
                <span class="valor">' . htmlspecialchars($documento['usuario_nombre']) . '</span>
            </div>
        </div>
        
        <div class="timbre-datos">
            <h3>✓ Datos de Validación</h3>
            <div class="campo">
                <span class="label">Estado:</span>
                <span class="valor" style="color: #28a745; font-weight: bold;">APROBADO</span>
            </div>
            <div class="campo">
                <span class="label">Validado por:</span>
                <span class="valor">' . htmlspecialchars($validador['nombre'] . ' ' . ($validador['apellidos'] ?? '')) . '</span>
            </div>
            <div class="campo">
                <span class="label">Cargo del Validador:</span>
                <span class="valor">' . htmlspecialchars($validador['cargo'] ?? 'Validador') . '</span>
            </div>
            <div class="campo">
                <span class="label">Fecha de Validación:</span>
                <span class="valor">' . date('d/m/Y', strtotime($documento['fecha_validacion'])) . '</span>
            </div>
            <div class="campo">
                <span class="label">Hora de Validación:</span>
                <span class="valor">' . date('H:i:s', strtotime($documento['fecha_validacion'])) . '</span>
            </div>
        </div>
        
        <div class="codigo-validacion">
            CÓDIGO DE VALIDACIÓN: ' . $codigoValidacion . '
        </div>
        
        <div class="qr-code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($codigoValidacion) . '" alt="Código QR">
            <p style="font-size: 12px; color: #6c757d; margin-top: 10px;">Escanea para verificar</p>
        </div>
        
        <div class="firma">
            <div class="linea"></div>
            <p><strong>' . htmlspecialchars($validador['nombre'] . ' ' . ($validador['apellidos'] ?? '')) . '</strong></p>
            <p>' . htmlspecialchars($validador['cargo'] ?? 'Validador') . '</p>
            <p style="font-size: 12px; color: #6c757d;">' . date('d/m/Y H:i:s', strtotime($documento['fecha_validacion'])) . '</p>
        </div>
        
        <div class="footer">
            <p>Este certificado fue generado electrónicamente por el Sistema de Gestión Documental</p>
            <p>Documento ID: ' . $documento['id'] . ' | Validación ID: ' . $codigoValidacion . '</p>
            <p>© ' . date('Y') . ' - Todos los derechos reservados</p>
        </div>
        
        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()" style="background: #28a745; color: white; border: none; padding: 15px 30px; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 5px;">
                🖨️ Imprimir Certificado
            </button>
            <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 15px 30px; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 5px;">
                ❌ Cerrar
            </button>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Generar código único de validación
     */
    private static function generarCodigoUnico($documentoId) {
        $timestamp = time();
        $random = rand(1000, 9999);
        $hash = substr(md5($documentoId . $timestamp . $random), 0, 8);
        
        return strtoupper('VAL-' . $documentoId . '-' . $hash);
    }
    
    /**
     * Verificar código de validación
     */
    public static function verificarCodigo($codigo, $documentoId) {
        // Extraer ID del documento del código
        $partes = explode('-', $codigo);
        
        if (count($partes) >= 2 && $partes[1] == $documentoId) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Generar marca de agua para vista web
     */
    public static function generarMarcaAgua($documento, $validador) {
        return '
        <div style="position: absolute; top: 20px; right: 20px; z-index: 1000;">
            <div style="background: linear-gradient(135deg, #28a745, #20c997); 
                        color: white; 
                        padding: 15px 25px; 
                        border-radius: 10px; 
                        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
                        text-align: center;
                        border: 3px solid white;">
                <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">
                    ✓ DOCUMENTO APROBADO
                </div>
                <div style="font-size: 12px; opacity: 0.9;">
                    ' . htmlspecialchars($validador['nombre']) . '
                </div>
                <div style="font-size: 11px; opacity: 0.8;">
                    ' . date('d/m/Y H:i', strtotime($documento['fecha_validacion'])) . '
                </div>
            </div>
        </div>';
    }
}
