<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'newaycs');

// Configuración de la aplicación
define('APP_NAME', 'AyCS - Inventario');
define('APP_URL', 'http://localhost/AYCS2');
define('APP_ROOT', dirname(dirname(__FILE__)));

// Configuración de rutas
define('CONTROLLERS_PATH', APP_ROOT . '/controllers/');
define('MODELS_PATH', APP_ROOT . '/models/');
define('VIEWS_PATH', APP_ROOT . '/views/');
define('LAYOUTS_PATH', APP_ROOT . '/layouts/');
define('UPLOADS_PATH', APP_ROOT . '/uploads/');
define('ASSETS_PATH', APP_ROOT . '/assets/');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Cargar archivos necesarios
require_once APP_ROOT . '/includes/functions.php';
require_once APP_ROOT . '/includes/session.php';
require_once APP_ROOT . '/includes/database.php';
require_once APP_ROOT . '/includes/upload.php';
require_once APP_ROOT . '/includes/sql.php'; 