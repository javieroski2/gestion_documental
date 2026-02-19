<?php
/**
 * Auth Controller
 * Sistema de Gestión Documental
 */

class AuthController extends Controller {
    
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('User');
    }
    
    public function login() {
        // Si ya está autenticado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            if ($this->isSessionExpired()) {
                $this->forceLogoutByInactivity();
            }

            $_SESSION['last_activity'] = time();
            $this->redirect('dashboard');
        }

        if (isset($_GET['timeout']) && $_GET['timeout'] == '1') {
            $error = 'La sesión expiró por inactividad. Inicie sesión nuevamente.';
        }
        
        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Validar
            if (empty($email) || empty($password)) {
                $error = 'Por favor complete todos los campos';
                $this->view('auth/login', ['error' => $error]);
                return;
            }
            
            // Buscar usuario
            $user = $this->userModel->findByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                // Verificar estado
                if ($user['estado'] != 1) {
                    $error = 'Usuario inactivo';
                    $this->view('auth/login', ['error' => $error]);
                    return;
                }
                
                // Iniciar sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['apellidos'] = $user['apellidos'] ?? '';
                $_SESSION['cargo'] = $user['cargo'] ?? '';
                $_SESSION['foto'] = $user['foto'] ?? '';
                $_SESSION['email'] = $user['email'];
                $_SESSION['rol_id'] = $user['rol_id'];
                $_SESSION['rol_nombre'] = $this->userModel->getRolNombre($user['rol_id']);
                $_SESSION['last_activity'] = time();
                
                // Registrar auditoría
                $auditoriaModel = $this->model('Auditoria');
                $auditoriaModel->registrar('Inició sesión exitosamente', 'usuarios', $user['id']);
                
                $this->redirect('dashboard');
            } else {
                $error = 'Credenciales incorrectas';
                $this->view('auth/login', ['error' => $error]);
            }
        } else {
            // Mostrar formulario
            $this->view('auth/login');
        }
    }
    
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            
            // Registrar auditoría antes de cerrar sesión
            $auditoriaModel = $this->model('Auditoria');
            $auditoriaModel->registrar('Cerró sesión', 'usuarios', $userId);
        }
        
        session_destroy();
        $this->redirect('auth/login');
    }
}
