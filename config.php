<?php
// กำหนด Base Path ของโปรเจค
define('BASE_PATH', __DIR__);
define('CONFIG_PATH', BASE_PATH . '/config');
define('CLASSES_PATH', BASE_PATH . '/classes');
define('INCLUDES_PATH', BASE_PATH . '/includes');

// Function สำหรับ autoload classes
spl_autoload_register(function ($class_name) {
    $file = CLASSES_PATH . '/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// ตั้งค่า error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตั้งค่า timezone
date_default_timezone_set('Asia/Bangkok');

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
define('DB_HOST', 'localhost');
define('DB_NAME', 'repair_system');
define('DB_USER', 'root');
define('DB_PASS', '');
?>