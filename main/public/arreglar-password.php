<?php
/**
 * Script para arreglar la contraseña del administrador
 * Ejecutar UNA SOLA VEZ
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_documental');
define('DB_USER', 'root');
define('DB_PASS', '');

echo "<h1>Arreglar Contraseña del Administrador</h1>";
echo "<style>body { font-family: Arial; padding: 20px; } .ok { color: green; } .error { color: red; }</style>";

try {
    // Conectar a la base de datos
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p class='ok'>✓ Conexión a base de datos exitosa</p>";
    
    // Generar hash correcto para "admin123"
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "<p>Generando nuevo hash para contraseña: <strong>$password</strong></p>";
    echo "<p>Nuevo hash: <code>$hash</code></p>";
    
    // Actualizar la contraseña del usuario admin
    $email = 'admin@sistema.com';
    
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
    $stmt->execute([$hash, $email]);
    
    echo "<p class='ok'>✓ Contraseña actualizada exitosamente</p>";
    
    echo "<hr>";
    echo "<h2>✅ ¡Listo!</h2>";
    echo "<p>Ahora puedes iniciar sesión con:</p>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> admin@sistema.com</li>";
    echo "<li><strong>Contraseña:</strong> admin123</li>";
    echo "</ul>";
    
    echo "<p><a href='index.php' style='display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al Login</a></p>";
    
    echo "<hr>";
    echo "<p style='color: red;'><strong>IMPORTANTE:</strong> Elimina este archivo (arreglar-password.php) después de usarlo por seguridad.</p>";
    
} catch (PDOException $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p>Verifica que:</p>";
    echo "<ul>";
    echo "<li>MySQL esté corriendo en XAMPP</li>";
    echo "<li>La base de datos 'gestion_documental' exista</li>";
    echo "<li>Los datos de conexión sean correctos</li>";
    echo "</ul>";
}

// Verificar el usuario
try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute(['admin@sistema.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<hr>";
        echo "<h3>Información del Usuario:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Campo</th><th>Valor</th></tr>";
        echo "<tr><td>ID</td><td>" . $user['id'] . "</td></tr>";
        echo "<tr><td>Nombre</td><td>" . $user['nombre'] . "</td></tr>";
        echo "<tr><td>Email</td><td>" . $user['email'] . "</td></tr>";
        echo "<tr><td>Rol ID</td><td>" . $user['rol_id'] . "</td></tr>";
        echo "<tr><td>Estado</td><td>" . ($user['estado'] == 1 ? 'Activo' : 'Inactivo') . "</td></tr>";
        echo "<tr><td>Hash Password</td><td><small>" . substr($user['password'], 0, 50) . "...</small></td></tr>";
        echo "</table>";
    }
} catch (Exception $e) {
    // Ignorar si falla
}
?>
