<?php
session_start();
require_once '../configdb.php';
require_once '../classes/RepairManagement.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['repair_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing repair_id parameter']);
    exit();
}

$repairManager = new RepairManagement();
$repair = $repairManager->getRepairById($_GET['repair_id']);

if ($repair) {
    echo json_encode($repair);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Repair not found']);
}
