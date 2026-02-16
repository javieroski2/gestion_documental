<?php
/**
 * Crear Carpetas Necesarias
 * Ejecutar una sola vez: http://localhost/sistema-gestion-documental/public/crear-carpetas.php
 */

echo "<h1>Creando Carpetas del Sistema</h1>";
echo "<style>body { font-family: Arial; padding: 20px; } .ok { color: green; } .error { color: red; }</style>";

$carpetas = [
    '../public/uploads',
    '../public/uploads/documentos',
    '../public/uploads/usuarios',
    '../public/uploads/temporal'
];

foreach ($carpetas as $carpeta) {
    if (!file_exists($carpeta)) {
        if (mkdir($carpeta, 0777, true)) {
            echo "<p class='ok'>✓ Carpeta creada: $carpeta</p>";
        } else {
            echo "<p class='error'>✗ Error al crear: $carpeta</p>";
        }
    } else {
        echo "<p class='ok'>✓ Ya existe: $carpeta</p>";
    }
    
    // Verificar permisos
    if (is_writable($carpeta)) {
        echo "<p class='ok'>  → Carpeta escribible</p>";
    } else {
        echo "<p class='error'>  → Carpeta NO escribible (ajustar permisos)</p>";
    }
}

echo "<hr>";
echo "<h2>✅ Verificación Completa</h2>";
echo "<p>Estructura de carpetas:</p>";
echo "<pre>";
echo "public/\n";
echo "  └─ uploads/\n";
echo "      ├─ documentos/  (para archivos subidos)\n";
echo "      ├─ usuarios/    (para fotos de perfil)\n";
echo "      └─ temporal/    (archivos temporales)\n";
echo "</pre>";

echo "<hr>";
echo "<p><strong>IMPORTANTE:</strong> Elimina este archivo después de usarlo:</p>";
echo "<p><code>public/crear-carpetas.php</code></p>";
echo "<p><a href='index.php'>← Volver al sistema</a></p>";
?>
