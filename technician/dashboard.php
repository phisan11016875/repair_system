<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/RepairManagement.php';

// ตรวจสอบว่าเป็นช่างซ่อม
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'technician') {
    header('Location: ../login.php');
    exit();
}

$repairManager = new RepairManagement();
$myRepairs = $repairManager->getRepairsByTechnician($_SESSION['user_id']);

// ฟังก์ชันนับสถานะ
function countRepairsByStatus($repairs, $status) {
    return count(array_filter($repairs, function($r) use ($status) {
        return $r['repair_status'] == $status;
    }));
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดช่างซ่อม - ระบบแจ้งซ่อมพัสดุ</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php 
        include '../includes/navbar.php';
        include '../includes/sidebar.php';
        ?>

        <!-- Content -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">แดชบอร์ดช่างซ่อม</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- สรุปสถานะงานซ่อม -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?php echo countRepairsByStatus($myRepairs, 'waiting'); ?></h3>
                                    <p>งานรอดำเนินการ</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo countRepairsByStatus($myRepairs, 'in_progress'); ?></h3>
                                    <p>กำลังดำเนินการ</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-tools"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?php echo countRepairsByStatus($myRepairs, 'completed'); ?></h3>
                                    <p>เสร็จสิ้น</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- รายการงานซ่อม -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">รายการงานซ่อม</h3>
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
                                        <th>การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($myRepairs as $repair): ?>
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
                                                    echo '<span class="badge badge-info">กำลังดำเนินการ</span>';
                                                    break;
                                                case 'completed':
                                                    echo '<span class="badge badge-success">เสร็จสิ้น</span>';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="updateStatus(<?php echo $repair['repair_id']; ?>)">
                                                <i class="fas fa-edit"></i> อัพเดทสถานะ
                                            </button>
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

    <!-- Modal และ Scripts ยังคงเหมือนเดิม -->

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">อัพเดทสถานะงานซ่อม</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="updateStatusForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="repair_id" id="repair_id">
                        <div class="form-group">
                            <label>สถานะ</label>
                            <select class="form-control" name="status" required>
                                <option value="waiting">รอดำเนินการ</option>
                                <option value="in_progress">กำลังดำเนินการ</option>
                                <option value="completed">เสร็จสิ้น</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>รายละเอียดการซ่อม</label>
                            <textarea class="form-control" name="repair_detail" rows="3"></textarea>
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
    <script>
    function updateStatus(repairId) {
        $('#repair_id').val(repairId);
        $('#updateStatusModal').modal('show');
    }

    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'update_repair_status.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    alert('อัพเดทสถานะเรียบร้อยแล้ว');
                    location.reload();
                } else {
                    alert('เกิดข้อผิดพลาด: ' + response.message);
                }
            }
        });
    });
    </script>
</body>
</html>