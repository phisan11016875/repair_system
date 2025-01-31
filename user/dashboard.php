<?php
session_start();
require_once '../config/configdb.php';
require_once '../classes/Database.php';
require_once '../classes/AssetManagement.php';
require_once '../classes/RepairManagement.php';

// ตรวจสอบการล็อกอิน
/*if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'user') {
    header('Location: ../login.php');
    exit();
}*/

$db = new Database();
$conn = $db->getConnection();

$assetManager = new AssetManagement();
$repairManager = new RepairManagement();

// ดึงข้อมูลพัสดุในแผนก
$departmentAssets = $assetManager->getAssetsByDepartment($_SESSION['dept_id']);

// ดึงข้อมูลการแจ้งซ่อมของผู้ใช้
$userRepairs = $repairManager->getRepairsByUser($_SESSION['user_id']);

// นับจำนวนตามสถานะ
$totalAssets = count($departmentAssets);
$totalRepairs = count($userRepairs);

// แยกจำนวนตามสถานะ
$repairStatus = [
    'waiting' => 0,
    'in_progress' => 0,
    'completed' => 0,
    'cancelled' => 0
];

foreach ($userRepairs as $repair) {
    if (isset($repairStatus[$repair['repair_status']])) {
        $repairStatus[$repair['repair_status']]++;
    }
}

// ดึงการแจ้งซ่อมล่าสุด 5 รายการ
$recentRepairs = array_slice($userRepairs, 0, 5);

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - ระบบแจ้งซ่อม</title>
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
                            <h1 class="m-0">หน้าหลัก</h1>
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
                                    <span class="info-box-text">พัสดุในแผนก</span>
                                    <span class="info-box-number"><?php echo $totalAssets; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">รอดำเนินการ</span>
                                    <span class="info-box-number"><?php echo $repairStatus['waiting']; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-tools"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">กำลังซ่อม</span>
                                    <span class="info-box-number"><?php echo $repairStatus['in_progress']; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">ซ่อมเสร็จแล้ว</span>
                                    <span class="info-box-number"><?php echo $repairStatus['completed']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- พัสดุในแผนก -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">พัสดุในแผนก</h3>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>รหัส</th>
                                                <th>รายการ</th>
                                                <th>รุ่น</th>
                                                <th>สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($departmentAssets as $asset): ?>
                                            <tr>
                                                <td><?php echo $asset['psd_id']; ?></td>
                                                <td><?php echo $asset['brand_name']; ?></td>
                                                <td><?php echo $asset['models']; ?></td>
                                                <td>
                                                    <?php
                                                    switch($asset['psd_status']) {
                                                        case 'active':
                                                            echo '<span class="badge badge-success">ใช้งานปกติ</span>';
                                                            break;
                                                        case 'repair':
                                                            echo '<span class="badge badge-warning">กำลังซ่อม</span>';
                                                            break;
                                                        case 'inactive':
                                                            echo '<span class="badge badge-danger">ไม่ใช้งาน</span>';
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

                        <!-- การแจ้งซ่อมล่าสุด -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">การแจ้งซ่อมล่าสุด</h3>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>วันที่</th>
                                                <th>พัสดุ</th>
                                                <th>สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentRepairs as $repair): ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y', strtotime($repair['date_1'])); ?></td>
                                                <td><?php echo $repair['brand_name'] . ' ' . $repair['models']; ?></td>
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