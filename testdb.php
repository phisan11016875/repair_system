<<<<<<< HEAD
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'classes/database_config.php';
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "เชื่อมต่อฐานข้อมูลสำเร็จ!";
    
    // ทดสอบดึงข้อมูล
    $stmt = $conn->query("SELECT * FROM psd_users LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($user);
    echo "</pre>";
    
} catch(Exception $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
=======
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'classes/database_config.php';
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "เชื่อมต่อฐานข้อมูลสำเร็จ!";
    
    // ทดสอบดึงข้อมูล
    $stmt = $conn->query("SELECT * FROM psd_users LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($user);
    echo "</pre>";
    
} catch(Exception $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
>>>>>>> 19b2a46099fb6a0a5d18007dc5257919a6c8c9d4
}