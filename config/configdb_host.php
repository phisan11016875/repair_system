<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'hkhospital_repair_system');
define('DB_USER', 'hkhospital_pisan');
define('DB_PASS', 'HK11016hk');

// Path configuration
define('BASE_URL', 'http://huaikoenghospital.moph.go.th/repair_system');
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/repair_system');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CLASSES_PATH', ROOT_PATH . '/classes');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/logs/error.log');
?>