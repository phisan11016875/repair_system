<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../classes/Database.php';
require_once '../classes/AssetManagement.php';
require_once '../classes/RepairManagement.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

try {
    $assetManager = new AssetManagement();
    $repairManager = new RepairManagement();

    // ดึงข้อมูลสถิติ
    $assetStats = $assetManager->getAssetStatistics();
    $repairStats = $repairManager->getRepairStatistics();

    // ดึงข้อมูลการแจ้งซ่อมล่าสุด
    $repairs = $repairManager->getAllRepairs();
    $recentRepairs = array_slice($repairs, 0, 5); // แสดง 5 รายการล่าสุด

    // จัดการสถิติ
    if (!$assetStats) {
        $assetStats = array(
            'total_assets' => 0,
            'status_counts' => array(
                'active' => 0,
                'repair' => 0,
                'inactive' => 0
            )
        );
    }

    if (!$repairStats) {
        $repairStats = array(
            'total_repairs' => 0,
            'status_counts' => array(
                'waiting' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'cancelled' => 0
            )
        );
    }

    // นับจำนวนตามสถานะ
    $repairStatusCount = array(
        'waiting' => isset($repairStats['status_counts']['waiting']) ? $repairStats['status_counts']['waiting'] : 0,
        'in_progress' => isset($repairStats['status_counts']['in_progress']) ? $repairStats['status_counts']['in_progress'] : 0,
        'completed' => isset($repairStats['status_counts']['completed']) ? $repairStats['status_counts']['completed'] : 0,
        'cancelled' => isset($repairStats['status_counts']['cancelled']) ? $repairStats['status_counts']['cancelled'] : 0
    );

    $assetStatusCount = array(
        'active' => isset($assetStats['status_counts']['active']) ? $assetStats['status_counts']['active'] : 0,
        'repair' => isset($assetStats['status_counts']['repair']) ? $assetStats['status_counts']['repair'] : 0,
        'inactive' => isset($assetStats['status_counts']['inactive']) ? $assetStats['status_counts']['inactive'] : 0
    );

} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    // จัดการ error อย่างเหมาะสม
    $error = "เกิดข้อผิดพลาดในการโหลดข้อมูล";
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ด - ระบบแจ้งซ่อม</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include '../includes/navbar.php'; ?>
        <?php include '../includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">แดชบอร์ด</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Info boxes -->
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-boxes"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">พัสดุทั้งหมด</span>
                                    <span class="info-box-number"><?php echo $assetStats['total_assets']; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">รอดำเนินการ</span>
                                    <span class="info-box-number"><?php echo $repairStatusCount['waiting']; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-tools"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">กำลังดำเนินการ</span>
                                    <span class="info-box-number"><?php echo $repairStatusCount['in_progress']; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">เสร็จสิ้น</span>
                                    <span class="info-box-number"><?php echo $repairStatusCount['completed']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- รายการแจ้งซ่อมล่าสุด -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">รายการแจ้งซ่อมล่าสุด</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>รหัส</th>
                                        <th>พัสดุ</th>
                                        <th>แผนก</th>
                                        <th>ผู้แจ้ง</th>
                                        <th>วันที่แจ้ง</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentRepairs as $repair): ?>
                                    <tr>
                                        <td><?php echo $repair['repair_id']; ?></td>
                                        <td><?php echo $repair['brand_name'] . ' ' . $repair['models']; ?></td>
                                        <td><?php echo $repair['dept_name']; ?></td>
                                        <td><?php echo $repair['reporter_name']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($repair['date_1'])); ?></td>
                                        <td>
                                            <?php
                                            switch($repair['repair_status']) {
                                                case 'waiting':
                                                    echo '<span class="badge badge-warning">รอดำเนินการ</span>';
                                                    break;
                                                case 'in_progress':
                                                    echo '<span class="badge badge-primary">กำลังดำเนินการ</span>';
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
</body>
</html>