<?php
/**
 * Front Controller
 * Sistema de Gestión Documental
 */

// Iniciar sesión
session_start();

// Cargar configuración
require_once '../app/config/database.php';
require_once '../app/config/config.php';

// Cargar core
require_once '../core/Database.php';
require_once '../core/Model.php';
require_once '../core/Controller.php';
require_once '../core/App.php';

// Iniciar aplicación
$app = new App();
