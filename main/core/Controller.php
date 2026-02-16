<?php
/**
 * Base Controller
 * Sistema de Gestión Documental
 */

class Controller {
    
    /**
     * Cargar modelo
     */
    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }
    
    /**
     * Cargar vista
     */
    public function view($view, $data = []) {
        extract($data);
        
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die('Vista no existe: ' . $view);
        }
    }
    
    /**
     * Redireccionar
     */
    public function redirect($url) {
        header('Location: ' . URL_BASE . $url);
        exit;
    }
    
    /**
     * Verificar autenticación
     */
    public function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }
    }
    
    /**
     * Verificar rol
     */
    public function requireRole($roles) {
        $this->requireAuth();
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        if (!in_array($_SESSION['rol_id'], $roles)) {
            $this->redirect('dashboard');
        }
    }
    
    /**
     * Responder JSON
     */
    public function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Mensajes flash
     */
    public function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    public function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
