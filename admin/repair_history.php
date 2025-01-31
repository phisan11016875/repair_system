<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/RepairManagement.php';

// ตรวจสอบการล็อกอิน
/*if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}*/

$repairManager = new RepairManagement();
$repairs = $repairManager->getRepairsByUser($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการแจ้งซ่อม - ระบบแจ้งซ่อมพัสดุ</title>
    <?php include '../includes/header.php'; ?>
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include '../includes/navbar.php'; ?>
        <?php include '../includes/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>ประวัติการแจ้งซ่อม</h1>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <table id="repairHistory" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>รหัสแจ้งซ่อม</th>
                                        <th>พัสดุ</th>
                                        <th>วันที่แจ้ง</th>
                                        <th>สาเหตุ</th>
                                        <th>ช่างซ่อม</th>
                                        <th>สถานะ</th>
                                        <th>วันที่เสร็จ</th>
                                        <th>รายละเอียดการซ่อม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($repairs as $repair): ?>
                                        <tr>
                                            <td><?php echo $repair['repair_id']; ?></td>
                                            <td><?php echo $repair['brand_name'] . ' ' . $repair['models']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($repair['date_1'])); ?></td>
                                            <td><?php echo $repair['repair_cause']; ?></td>
                                            <td><?php echo isset($repair['technician_name']) ? $repair['technician_name'] : '-'; ?></td>
                                            <td>
                                                <?php
                                                switch($repair['repair_status']) {
                                                    case 'waiting':
                                                        echo '<span class="badge badge-warning">รอดำเนินการ</span>';
                                                        break;
                                                    case 'in_progress':
                                                        echo '<span class="badge badge-info">กำลังดำเนินการ</span>';
                                                        break;
                                                    case 'completed':
                                                        echo '<span class="badge badge-success">เสร็จสิ้น</span>';
                                                        break;
                                                    case 'cancelled':
                                                        echo '<span class="badge badge-danger">ยกเลิก</span>';
                                                        break;
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                echo !empty($repair['date_2']) ? date('d/m/Y', strtotime($repair['date_2'])) : '-'; 
                                                ?>
                                            </td>
                                            <td><?php echo isset($repair['repair_detail']) ? $repair['repair_detail'] : '-'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../includes/footer.php'; ?>
    </div>

    <!-- Required Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#repairHistory').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            },
            "order": [[0, "desc"]], // เรียงตามรหัสแจ้งซ่อมล่าสุด
            "pageLength": 10
        });
    });
    </script>
</body>
</html>