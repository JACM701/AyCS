<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// -----------------------------------------------------------------------
// DEFINE SEPERATOR ALIASES
// -----------------------------------------------------------------------
define('URL_SEPARATOR', '/');
define('DS', DIRECTORY_SEPARATOR);

// -----------------------------------------------------------------------
// DEFINE ROOT PATHS
// -----------------------------------------------------------------------
define('SITE_ROOT', realpath(dirname(__FILE__) . '/..'));
define('LIB_PATH_INC', SITE_ROOT . DS . 'includes' . DS);
define('PUBLIC_PATH', SITE_ROOT . DS . 'public' . DS);
define('ASSETS_PATH', SITE_ROOT . DS . 'libs' . DS);

// -----------------------------------------------------------------------
// LOAD CONFIGURATION FILES
// -----------------------------------------------------------------------
require_once(LIB_PATH_INC . 'config.php');
require_once(LIB_PATH_INC . 'functions.php');
require_once(LIB_PATH_INC . 'session.php');
require_once(LIB_PATH_INC . 'upload.php');
require_once(LIB_PATH_INC . 'database.php');
require_once(LIB_PATH_INC . 'sql.php');
?>
