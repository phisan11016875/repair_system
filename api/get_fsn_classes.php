<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/FSNManager.php';

header('Content-Type: application/json');

try {
    $fsnManager = new FSNManager();
    $group_id = isset($_GET['group_id']) ? $_GET['group_id'] : null;
    
    // ดึงข้อมูลประเภทพัสดุ
    $classes = $group_id 
        ? $fsnManager->getClassesByGroup($group_id)  // ดึงตามกลุ่ม
        : $fsnManager->getAllClasses();              // ดึงทั้งหมด

    echo json_encode(array(
        'success' => true,
        'data' => $classes
    ));

} catch(Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ));
}
?>