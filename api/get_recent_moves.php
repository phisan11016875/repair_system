<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("
        SELECT 
            m.move_id,
            m.date_move,
            p.brand_name,
            p.models,
            u.fullname as moved_by,
            d1.dept_name as dept_old,
            d2.dept_name as dept_new,
            m.move_cause
        FROM psd_move m
        JOIN psd_recieve p ON m.psd_id = p.psd_id
        JOIN psd_users u ON m.user_id = u.user_id
        JOIN department d1 ON m.deptid_old = d1.dept_id
        JOIN department d2 ON m.deptid_new = d2.dept_id
        ORDER BY m.date_move DESC
        LIMIT 10
    ");
    $stmt->execute();
    $moves = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // สร้าง HTML สำหรับแสดงประวัติ
    foreach ($moves as $move) {
        echo '<div class="timeline-item">';
        echo '<div class="timeline-date">' . date('d/m/Y', strtotime($move['date_move'])) . '</div>';
        echo '<h4>' . $move['brand_name'] . ' ' . $move['models'] . '</h4>';
        echo '<p><strong>จาก:</strong> ' . $move['dept_old'] . ' <i class="fas fa-arrow-right"></i> <strong>ไปยัง:</strong> ' . $move['dept_new'] . '</p>';
        echo '<p><strong>เหตุผล:</strong> ' . $move['move_cause'] . '</p>';
        echo '<p><small class="text-muted">ดำเนินการโดย: ' . $move['moved_by'] . '</small></p>';
        echo '</div>';
    }
} catch(PDOException $e) {
    error_log("Error getting move history: " . $e->getMessage());
    echo '<div class="alert alert-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
}