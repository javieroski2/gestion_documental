<?php
/**
 * Diagnóstico del Sistema
 * Verifica que todos los archivos existan y no tengan errores
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico del Sistema</h1>";
echo "<style>body { font-family: Arial; padding: 20px; } .ok { color: green; } .error { color: red; }</style>";

// 1. Verificar archivos core
echo "<h2>1. Archivos Core</h2>";
$core_files = [
    '../core/App.php',
    '../core/Controller.php',
    '../core/Model.php',
    '../core/Database.php'
];

foreach ($core_files as $file) {
    if (file_exists($file)) {
        echo "<p class='ok'>✓ $file existe</p>";
    } else {
        echo "<p class='error'>✗ $file NO EXISTE</p>";
    }
}

// 2. Verificar configuración
echo "<h2>2. Configuración</h2>";
if (file_exists('../app/config/config.php')) {
    require_once '../app/config/config.php';
    echo "<p class='ok'>✓ config.php cargado</p>";
    echo "<p>URL_BASE: " . URL_BASE . "</p>";
} else {
    echo "<p class='error'>✗ config.php NO EXISTE</p>";
}

if (file_exists('../app/config/database.php')) {
    require_once '../app/config/database.php';
    echo "<p class='ok'>✓ database.php cargado</p>";
} else {
    echo "<p class='error'>✗ database.php NO EXISTE</p>";
}

// 3. Verificar conexión a BD
echo "<h2>3. Conexión a Base de Datos</h2>";
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS
    );
    echo "<p class='ok'>✓ Conexión a BD exitosa</p>";
    echo "<p>Base de datos: " . DB_NAME . "</p>";
    
    // Verificar tablas
    $stmt = $pdo->query("SHOW TABLES");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Tablas encontradas: " . count($tablas) . "</p>";
    echo "<ul>";
    foreach ($tablas as $tabla) {
        echo "<li>$tabla</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p class='error'>✗ Error de conexión: " . $e->getMessage() . "</p>";
}

// 4. Verificar controladores
echo "<h2>4. Controladores</h2>";
$controllers = [
    '../app/controllers/HomeController.php',
    '../app/controllers/AuthController.php',
    '../app/controllers/DashboardController.php',
    '../app/controllers/UsuarioController.php',
    '../app/controllers/CategoriaController.php',
    '../app/controllers/ConfiguracionController.php',
    '../app/controllers/AuditoriaController.php'
];

foreach ($controllers as $file) {
    if (file_exists($file)) {
        echo "<p class='ok'>✓ " . basename($file) . " existe</p>";
        
        // Verificar errores de sintaxis
        $output = shell_exec("php -l $file 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "<p class='ok'>  → Sin errores de sintaxis</p>";
        } else {
            echo "<p class='error'>  → ERROR DE SINTAXIS: $output</p>";
        }
    } else {
        echo "<p class='error'>✗ " . basename($file) . " NO EXISTE</p>";
    }
}

// 5. Verificar modelos
echo "<h2>5. Modelos</h2>";
$models = [
    '../app/models/User.php',
    '../app/models/Categoria.php',
    '../app/models/Auditoria.php'
];

foreach ($models as $file) {
    if (file_exists($file)) {
        echo "<p class='ok'>✓ " . basename($file) . " existe</p>";
    } else {
        echo "<p class='error'>✗ " . basename($file) . " NO EXISTE</p>";
    }
}

// 6. Verificar .htaccess
echo "<h2>6. Archivos .htaccess</h2>";
if (file_exists('.htaccess')) {
    echo "<p class='ok'>✓ public/.htaccess existe</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents('.htaccess')) . "</pre>";
} else {
    echo "<p class='error'>✗ public/.htaccess NO EXISTE</p>";
}

if (file_exists('../.htaccess')) {
    echo "<p class='ok'>✓ root/.htaccess existe</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents('../.htaccess')) . "</pre>";
} else {
    echo "<p class='error'>✗ root/.htaccess NO EXISTE</p>";
}

// 7. Verificar permisos
echo "<h2>7. Información PHP</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";

echo "<hr>";
echo "<h2>¿Qué hacer ahora?</h2>";
echo "<p>1. Si ves errores en rojo arriba, esos son los problemas</p>";
echo "<p>2. Comparte una captura de esta página</p>";
echo "<p>3. Intenta acceder a: <a href='index.php'>index.php</a></p>";
?>
