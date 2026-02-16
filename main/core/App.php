<?php
/**
 * Core Application Router
 * Sistema de Gestión Documental
 */

class App {
    
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];
    
    public function __construct() {
        $url = $this->parseUrl();
        
        // Buscar controlador
        if (isset($url[0]) && file_exists('../app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]);
            unset($url[0]);
        }
        
        // Cargar controlador
        require_once '../app/controllers/' . $this->controller . 'Controller.php';
        $this->controller = $this->controller . 'Controller';
        $this->controller = new $this->controller;
        
        // Buscar método
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        // Obtener parámetros
        $this->params = $url ? array_values($url) : [];
        
        // Ejecutar
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    public function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
    }
}
