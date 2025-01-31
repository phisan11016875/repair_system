<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/RepairManagement.php';
require_once '../classes/AssetManagement.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

try {
    $repairManager = new RepairManagement();
    $assetManager = new AssetManagement();
    
    // ดึงพัสดุตามแผนกของผู้ใช้
    $assets = $assetManager->getAssetsByDepartment($_SESSION['dept_id']);
    
    // จัดการการส่งข้อมูลฟอร์ม
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = array(
            'psd_id' => trim($_POST['psd_id']),
            'repair_cause' => trim($_POST['repair_cause']),
            'user_id' => $_SESSION['user_id'],
            'date_repair' => date('Y-m-d')
        );

        if (empty($data['psd_id']) || empty($data['repair_cause'])) {
            $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
        } else {
            $result = $repairManager->createRepairTicket($data);
            if ($result) {
                $success = "แจ้งซ่อมเรียบร้อยแล้ว";
            } else {
                $error = "เกิดข้อผิดพลาดในการแจ้งซ่อม กรุณาลองใหม่อีกครั้ง";
            }
        }
    }

    // ดึงประวัติการแจ้งซ่อม
    $repairs = $repairManager->getRepairsByUser($_SESSION['user_id']);

} catch(Exception $e) {
    error_log("Error in report_repair.php: " . $e->getMessage());
    $error = "เกิดข้อผิดพลาดในระบบ กรุณาติดต่อผู้ดูแลระบบ";
    $assets = array();
    $repairs = array();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งซ่อมพัสดุ - ระบบแจ้งซ่อมพัสดุ</title>
    <?php include '../includes/header.php'; ?>
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include '../includes/navbar.php'; ?>
        <?php include '../includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>แจ้งซ่อมพัสดุ</h1>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <!-- แบบฟอร์มแจ้งซ่อม -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tools mr-1"></i>
                                กรอกข้อมูลแจ้งซ่อม
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="psd_id">เลือกพัสดุ <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="psd_id" required>
                                        <option value="">-- เลือกพัสดุ --</option>
                                        <?php foreach ($assets as $asset): ?>
                                            <option value="<?php echo htmlspecialchars($asset['psd_id']); ?>">
                                                <?php echo htmlspecialchars($asset['brand_name'] . ' ' . $asset['models']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="repair_cause">สาเหตุที่แจ้งซ่อม <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="repair_cause" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> บันทึกการแจ้งซ่อม
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- ประวัติการแจ้งซ่อม -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-1"></i>
                                ประวัติการแจ้งซ่อม
                            </h3>
                        </div>
                        <div class="card-body">
                            <table id="historyTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>รหัสแจ้งซ่อม</th>
                                        <th>พัสดุ</th>
                                        <th>วันที่แจ้ง</th>
                                        <th>สาเหตุ</th>
                                        <th>สถานะ</th>
                                        <th>ช่างซ่อม</th>
                                        <th>รายละเอียดการซ่อม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($repairs as $repair): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($repair['repair_id']); ?></td>
                                            <td><?php echo htmlspecialchars($repair['brand_name'] . ' ' . $repair['models']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($repair['date_1'])); ?></td>
                                            <td><?php echo htmlspecialchars($repair['repair_cause']); ?></td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                $statusText = '';
                                                switch($repair['repair_status']) {
                                                    case 'waiting':
                                                        $statusClass = 'badge-warning';
                                                        $statusText = 'รอดำเนินการ';
                                                        break;
                                                    case 'in_progress':
                                                        $statusClass = 'badge-primary';
                                                        $statusText = 'กำลังดำเนินการ';
                                                        break;
                                                    case 'completed':
                                                        $statusClass = 'badge-success';
                                                        $statusText = 'เสร็จสิ้น';
                                                        break;
                                                    case 'cancelled':
                                                        $statusClass = 'badge-danger';
                                                        $statusText = 'ยกเลิก';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?php echo $statusClass; ?>">
                                                    <?php echo $statusText; ?>
                                                </span>
                                            </td>
                                            <td><?php echo isset($repair['technician_name']) ? htmlspecialchars($repair['technician_name']) : '-'; ?></td>
                                            <td><?php echo isset($repair['repair_detail']) ? htmlspecialchars($repair['repair_detail']) : '-'; ?></td>
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>

    <script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        // Initialize DataTable
        $('#historyTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            },
            "order": [[0, "desc"]]
        });
    });
    </script>
</body>
</html>