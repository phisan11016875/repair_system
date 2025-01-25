<?php
session_start();
require_once 'classes/Database.php';  // เพิ่มการเรียกใช้ไฟล์ Database class

if (isset($_SESSION['user_id'])) {
    try {
        // บันทึกเวลาออกจากระบบ
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("UPDATE psd_users SET last_logout = NOW() WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();

    } catch(PDOException $e) {
        error_log("Logout Error: " . $e->getMessage());
    }
}

// ล้าง session ทั้งหมด
session_unset();
session_destroy();

// ลบ cookie ที่เกี่ยวข้อง
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// แสดง alert และ redirect
?>
<script>
    alert('ออกจากระบบสำเร็จ');
    window.location.href = 'index.php';
</script>