<?php
/**
 * Timbre PDF - Agrega timbre directamente al archivo PDF
 * Usa FPDF + FPDI para modificar PDFs existentes
 */

class TimbrePDF {
    
    /**
     * Agregar timbre a PDF existente
     */
    public static function agregarTimbreAPDF($rutaPDF, $documento, $validador) {
        // Verificar si existen las librerías
        if (!self::verificarLibrerias()) {
            return false;
        }
        
        require_once '../app/libraries/fpdf/fpdf.php';
        require_once '../app/libraries/fpdi/src/autoload.php';
        
        try {
            // Crear nuevo PDF con FPDI
            $pdf = new \setasign\Fpdi\Fpdi();
            
            // Obtener número de páginas
            $pageCount = $pdf->setSourceFile($rutaPDF);
            
            // Procesar cada página
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                // Importar página
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                
                // Crear página con mismo tamaño
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                
                // Usar template (página original)
                $pdf->useTemplate($templateId);
                
                // Agregar timbre solo en primera página
                if ($pageNo == 1) {
                    self::dibujarTimbre($pdf, $size, $documento, $validador);
                }
            }
            
            // Guardar PDF modificado
            $nombreNuevo = str_replace('.pdf', '_firmado.pdf', basename($rutaPDF));
            $rutaNueva = dirname($rutaPDF) . '/' . $nombreNuevo;
            
            $pdf->Output('F', $rutaNueva);
            
            return $rutaNueva;
            
        } catch (Exception $e) {
            error_log("Error al agregar timbre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Dibujar timbre en el PDF
     */
    private static function dibujarTimbre($pdf, $size, $documento, $validador) {
        // Posición del timbre (esquina superior derecha)
        $x = $size['width'] - 70;
        $y = 10;
        $ancho = 60;
        $alto = 50;
        
        // Fondo verde con transparencia
        $pdf->SetFillColor(40, 167, 69); // Verde
        $pdf->SetDrawColor(255, 255, 255); // Borde blanco
        $pdf->SetLineWidth(1);
        
        // Rectángulo redondeado
        $pdf->RoundedRect($x, $y, $ancho, $alto, 3, 'FD');
        
        // Texto "APROBADO"
        $pdf->SetTextColor(255, 255, 255); // Blanco
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY($x, $y + 5);
        $pdf->Cell($ancho, 6, utf8_decode('✓ APROBADO'), 0, 1, 'C');
        
        // Línea separadora
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->Line($x + 5, $y + 13, $x + $ancho - 5, $y + 13);
        
        // Nombre del validador
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetXY($x, $y + 15);
        $nombreCorto = self::cortarTexto($validador['nombre'], 15);
        $pdf->Cell($ancho, 4, utf8_decode($nombreCorto), 0, 1, 'C');
        
        // Cargo
        $pdf->SetXY($x, $y + 19);
        $cargoCorto = self::cortarTexto($validador['cargo'] ?? 'Validador', 15);
        $pdf->Cell($ancho, 4, utf8_decode($cargoCorto), 0, 1, 'C');
        
        // Fecha
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY($x, $y + 25);
        $fecha = date('d/m/Y', strtotime($documento['fecha_validacion']));
        $pdf->Cell($ancho, 4, $fecha, 0, 1, 'C');
        
        // Hora
        $pdf->SetXY($x, $y + 29);
        $hora = date('H:i:s', strtotime($documento['fecha_validacion']));
        $pdf->Cell($ancho, 4, $hora, 0, 1, 'C');
        
        // Código de validación (pequeño)
        $pdf->SetFont('Arial', '', 5);
        $pdf->SetXY($x, $y + 34);
        $codigo = 'VAL-' . $documento['id'];
        $pdf->Cell($ancho, 3, $codigo, 0, 1, 'C');
        
        // Icono de certificado (usando símbolo)
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY($x, $y + 39);
        $pdf->Cell($ancho, 4, utf8_decode('🔐'), 0, 1, 'C');
    }
    
    /**
     * Cortar texto si es muy largo
     */
    private static function cortarTexto($texto, $max = 20) {
        if (strlen($texto) > $max) {
            return substr($texto, 0, $max - 3) . '...';
        }
        return $texto;
    }
    
    /**
     * Verificar si están instaladas las librerías
     */
    public static function verificarLibrerias() {
        $fpdf = file_exists('../app/libraries/fpdf/fpdf.php');
        $fpdi = file_exists('../app/libraries/fpdi/src/autoload.php');
        
        return $fpdf && $fpdi;
    }
    
    /**
     * Obtener estado de instalación
     */
    public static function estadoInstalacion() {
        return [
            'fpdf' => file_exists('../app/libraries/fpdf/fpdf.php'),
            'fpdi' => file_exists('../app/libraries/fpdi/src/autoload.php'),
            'instalado' => self::verificarLibrerias()
        ];
    }
}
