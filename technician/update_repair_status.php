<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/RepairManagement.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตรวจสอบว่าเป็นช่างซ่อม
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'technician') {
    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => false, 
        'message' => 'Unauthorized'
    ));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // ตรวจสอบข้อมูลที่ส่งมา
        if (!isset($_POST['repair_id']) || !isset($_POST['status']) || !isset($_POST['repair_detail'])) {
            throw new Exception('Missing required parameters');
        }

        $repair_id = intval($_POST['repair_id']);
        $status = trim($_POST['status']);
        $repair_detail = trim($_POST['repair_detail']);

        // ตรวจสอบความถูกต้องของสถานะ
        $valid_statuses = array('waiting', 'in_progress', 'completed', 'cancelled');
        if (!in_array($status, $valid_statuses)) {
            throw new Exception('Invalid status');
        }

        // สร้าง array ข้อมูลสำหรับอัพเดท
        $data = array(
            'repair_status' => $status,
            'repair_detail' => $repair_detail,
            'technician_id' => $_SESSION['user_id']
        );

        $repairManager = new RepairManagement();
        $result = $repairManager->updateRepairStatus($repair_id, $data);

        if ($result) {
            // บันทึก log การอัพเดท
            error_log("Repair #$repair_id updated to $status by user #{$_SESSION['user_id']}");
            
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => true,
                'message' => 'อัพเดทสถานะเรียบร้อยแล้ว'
            ));
        } else {
            throw new Exception('Failed to update repair status');
        }

    } catch (Exception $e) {
        error_log("Error updating repair status: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ));
    }
    exit();
} else {
    // ถ้าไม่ใช่ POST request
    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => false,
        'message' => 'Invalid request method'
    ));
    exit();
}
?>