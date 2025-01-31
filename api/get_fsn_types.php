<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/FSNManager.php';

header('Content-Type: application/json');

try {
    $fsnManager = new FSNManager();
    $group_id = isset($_GET['group_id']) ? $_GET['group_id'] : null;
    $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;
    
    $types = ($group_id && $class_id) 
        ? $fsnManager->getTypesByGroupClass($group_id . $class_id) 
        : $fsnManager->getAllTypes();

    echo json_encode([
        'success' => true,
        'data' => $types
    ]);

} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}

?>