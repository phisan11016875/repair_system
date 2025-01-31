<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'classes/Database.php';
    $db = new Database();
    $conn = $db->getConnection();
    echo "เชื่อมต่อฐานข้อมูลสำเร็จ!";
    
    // ทดสอบ query
    $stmt = $conn->query("SELECT VERSION() as version");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<br>MySQL Version: " . $result['version'];
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    // แสดงรายละเอียดเพิ่มเติมสำหรับ debug
    if ($e instanceof PDOException) {
        echo "<br>PDO Error Code: " . $e->getCode();
    }
}
?>