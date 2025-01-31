<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/RepairManagement.php';

// ตรวจสอบว่าเป็นช่างซ่อม
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'technician') {
    header('Location: ../login.php');
    exit();
}

try {
    $repairManager = new RepairManagement();
    $repairs = $repairManager->getRepairsByTechnician($_SESSION['user_id']);

    // แยกงานซ่อมตามสถานะ
    $waiting_repairs = array_filter($repairs, function($r) {
        return $r['repair_status'] == 'waiting';
    });
    $in_progress_repairs = array_filter($repairs, function($r) {
        return $r['repair_status'] == 'in_progress';
    });
    $completed_repairs = array_filter($repairs, function($r) {
        return $r['repair_status'] == 'completed';
    });
} catch(Exception $e) {
    error_log("Error loading repairs: " . $e->getMessage());
    $waiting_repairs = array();
    $in_progress_repairs = array();
    $completed_repairs = array();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>งานซ่อม - ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ โรงพยาบาลห้วยเกิ้ง</title>
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
                            <h1>จัดการงานซ่อม</h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <!-- งานรอดำเนินการ -->
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">งานรอดำเนินการ</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped repair-table">
                                <thead>
                                    <tr>
                                        <th>รหัส</th>
                                        <th>พัสดุ</th>
                                        <th>แผนก</th>
                                        <th>ผู้แจ้ง</th>
                                        <th>วันที่แจ้ง</th>
                                        <th>สาเหตุ</th>
                                        <th>การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($waiting_repairs as $repair): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($repair['repair_id']); ?></td>
                                            <td><?php echo htmlspecialchars($repair['brand_name'] . ' ' . $repair['models']); ?></td>
                                            <td><?php echo htmlspecialchars($repair['dept_name']); ?></td>
                                            <td><?php echo htmlspecialchars($repair['reporter_name']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($repair['date_1'])); ?></td>
                                            <td><?php echo htmlspecialchars($repair['repair_cause']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" onclick="startRepair(<?php echo $repair['repair_id']; ?>)">
                                                    <i class="fas fa-play"></i> เริ่มซ่อม
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- งานที่กำลังดำเนินการ -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">กำลังดำเนินการ</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped repair-table">
                                <thead>
                                    <tr>
                                        <th>รหัส</th>
                                        <th>พัสดุ</th>
                                        <th>แผนก</th>
                                        <th>ผู้แจ้ง</th>
                                        <th>วันที่แจ้ง</th>
                                        <th>สาเหตุ</th>
                                        <th>การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($in_progress_repairs as $repair): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($repair['repair_id']); ?></td>
                                            <td><?php echo htmlspecialchars($repair['brand_name'] . ' ' . $repair['models']); ?></td>
                                            <td><?php echo htmlspecialchars($repair['dept_name']); ?></td>
                                            <td><?php echo htmlspecialchars($repair['reporter_name']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($repair['date_1'])); ?></td>
                                            <td><?php echo htmlspecialchars($repair['repair_cause']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-success btn-sm" onclick="completeRepair(<?php echo $repair['repair_id']; ?>)">
                                                    <i class="fas fa-check"></i> ซ่อมเสร็จ
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- งานที่เสร็จสิ้น -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">งานเสร็จสิ้น</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped repair-table">
                                <thead>
                                    <tr>
                                        <th>รหัส</th>
                                        <th>พัสดุ</th>
                                        <th>แผนก</th>
                                        <th>ผู้แจ้ง</th>
                                        <th>วันที่แจ้ง</th>
                                        <th>วันที่เสร็จ</th>
                                        <th>รายละเอียด</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($completed_repairs as $repair): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($repair['repair_id']); ?></td>
                                            <td><?php echo htmlspecialchars($repair['brand_name'] . ' ' . $repair['models']); ?></td>
                                            <td><?php echo htmlspecialchars($repair['dept_name']); ?></td>
                                            <td><?php echo htmlspecialchars($repair['reporter_name']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($repair['date_1'])); ?></td>
                                            <td><?php echo isset($repair['date_2']) ? date('d/m/Y', strtotime($repair['date_2'])) : '-'; ?></td>
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

    <!-- Modal อัพเดทสถานะ -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">อัพเดทสถานะงานซ่อม</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="updateStatusForm">
                    <div class="modal-body">
                        <input type="hidden" name="repair_id" id="repair_id">
                        <input type="hidden" name="status" id="repair_status">
                        <div class="form-group">
                            <label>รายละเอียดการซ่อม</label>
                            <textarea class="form-control" name="repair_detail" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

    <script>
    $(document).ready(function() {
        $('.repair-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            }
        });
    });

    function startRepair(repairId) {
        $('#repair_id').val(repairId);
        $('#repair_status').val('in_progress');
        $('#updateStatusModal').modal('show');
    }

    function completeRepair(repairId) {
        $('#repair_id').val(repairId);
        $('#repair_status').val('completed');
        $('#updateStatusModal').modal('show');
    }

    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'update_repair_status.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    alert('อัพเดทสถานะเรียบร้อยแล้ว');
                    location.reload();
                } else {
                    alert('เกิดข้อผิดพลาด: ' + response.message);
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            }
        });
    });
    </script>
</body>
</html>