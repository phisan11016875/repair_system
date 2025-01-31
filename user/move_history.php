<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/AssetManagement.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // ดึงประวัติการโยกย้ายพัสดุทั้งหมดของแผนก
    $stmt = $conn->prepare("
        SELECT m.*, 
            r.brand_name, r.models,
            d1.dept_name as dept_old,
            d2.dept_name as dept_new,
            u.fullname as user_name
        FROM psd_move m
        LEFT JOIN psd_recieve r ON m.psd_id = r.psd_id
        LEFT JOIN department d1 ON m.deptid_old = d1.dept_id
        LEFT JOIN department d2 ON m.deptid_new = d2.dept_id
        LEFT JOIN psd_users u ON m.user_id = u.user_id
        WHERE m.deptid_old = :dept_id OR m.deptid_new = :dept_id
        ORDER BY m.date_move DESC
    ");
    
    $stmt->bindParam(':dept_id', $_SESSION['dept_id']);
    $stmt->execute();
    $moves = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    error_log("Error: " . $e->getMessage());
    //$error = "เกิดข้อผิดพลาดในการดึงข้อมูล";
    $moves = array();   
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการโยกย้ายพัสดุ/ครุภัณฑ์ - ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ โรงพยาบาลห้วยเกิ้ง</title>
    <?php include '../includes/header.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include '../includes/navbar.php'; ?>
        <?php include '../includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>ประวัติการโยกย้ายพัสดุ/ครุภัณฑ์</h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-exchange-alt mr-1"></i>
                                รายการโยกย้ายพัสดุ/ครุภัณฑ์
                            </h3>
                        </div>
                        <div class="card-body">
                            <table id="moveHistory" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="10%">วันที่โยกย้าย</th>
                                        <th>รหัสพัสดุ</th>
                                        <th>รายการ</th>
                                        <th>แผนกเดิม</th>
                                        <th>แผนกใหม่</th>
                                        <th>สาเหตุการโยกย้าย</th>
                                        <th>ผู้ดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($moves)): ?>
                                        <?php foreach ($moves as $move): ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y', strtotime($move['date_move'])); ?></td>
                                                <td><?php echo htmlspecialchars($move['psd_id']); ?></td>
                                                <td><?php echo htmlspecialchars($move['brand_name'] . ' ' . $move['models']); ?></td>
                                                <td><?php echo htmlspecialchars($move['dept_old']); ?></td>
                                                <td><?php echo htmlspecialchars($move['dept_new']); ?></td>
                                                <td><?php echo htmlspecialchars($move['move_cause']); ?></td>
                                                <td><?php echo htmlspecialchars($move['user_name']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7">ไม่พบข้อมูลการเคลื่อนย้าย</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../includes/footer.php'; ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#moveHistory').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            },
            "order": [[0, "desc"]],
            "pageLength": 10
        });
    });
    </script>
</body>
</html>