<?php
session_start();
require_once '../configdb.php';
require_once '../classes/UserManagement.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing user_id parameter']);
    exit();
}

$userManager = new UserManagement();
$user = $userManager->getUserById($_GET['user_id']);

if ($user) {
    // ลบรหัสผ่านออกจากข้อมูลที่จะส่งกลับ
    unset($user['pass_word']);
    echo json_encode($user);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
}
