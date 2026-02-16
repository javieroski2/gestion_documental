<?php
/**
 * Home Controller
 * Sistema de Gestión Documental
 */

class HomeController extends Controller {
    
    public function index() {
        // Si está autenticado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }
        
        // Si no, mostrar login
        $this->redirect('auth/login');
    }
}
