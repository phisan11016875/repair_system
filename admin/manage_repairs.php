<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__FILE__) . '/../config/configdb.php';
require_once dirname(__FILE__) . '/../classes/RepairManagement.php';
require_once dirname(__FILE__) . '/../classes/UserManagement.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

try {
    $repairManager = new RepairManagement();
    $userManager = new UserManagement();

    // ดึงข้อมูลช่างซ่อม
    $technicians = $userManager->getTechnicians();

    // จัดการการส่งข้อมูลฟอร์ม
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                if (empty($_POST['repair_id']) || empty($_POST['repair_status'])) {
                    throw new Exception("กรุณากรอกข้อมูลให้ครบถ้วน");
                }
                $data = array(
                    'repair_status' => trim($_POST['repair_status']),
                    'technician_id' => !empty($_POST['technician_id']) ? $_POST['technician_id'] : null,
                    'repair_detail' => trim($_POST['repair_detail'])
                );
                $result = $repairManager->updateRepairStatus($_POST['repair_id'], $data);
                if ($result) {
                    $success = "อัพเดทสถานะการซ่อมเรียบร้อยแล้ว";
                } else {
                    $error = "เกิดข้อผิดพลาดในการอัพเดทสถานะ";
                }
                break;
        }
    }

        // ดึงข้อมูลการแจ้งซ่อมทั้งหมด
        $repairs = $repairManager->getAllRepairs();

    } catch(Exception $e) {
        error_log("Error in manage_repairs.php: " . $e->getMessage());
        $error = "เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง";
        $repairs = array();
        $technicians = array();
    }
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการการแจ้งซ่อม - ระบบแจ้งซ่อมพัสดุ</title>
    <!-- Google Font: Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Custom styles -->
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }
        .status-badge {
            font-size: 0.9em;
            padding: 5px 10px;
        }
        .action-buttons .btn {
            margin: 0 2px;
        }
        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>จัดการการแจ้งซ่อม</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">หน้าหลัก</a></li>
                            <li class="breadcrumb-item active">จัดการการแจ้งซ่อม</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- รายการแจ้งซ่อม -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tools mr-1"></i>
                            รายการแจ้งซ่อมทั้งหมด
                        </h3>
                    </div>
                    <div class="card-body">
                        <table id="repairsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>รหัส</th>
                                    <th>พัสดุ</th>
                                    <th>แผนก</th>
                                    <th>ผู้แจ้ง</th>
                                    <th>วันที่แจ้ง</th>
                                    <th>สาเหตุ</th>
                                    <th>ช่างซ่อม</th>
                                    <th>สถานะ</th>
                                    <th>การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($repairs as $repair): ?>
                                    <tr>
                                        <td><?php echo $repair['repair_id']; ?></td>
                                        <td><?php echo $repair['brand_name'] . ' ' . $repair['models']; ?></td>
                                        <td><?php echo $repair['dept_name']; ?></td>
                                        <td><?php echo $repair['reporter_name']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($repair['date_1'])); ?></td>
                                        <td><?php echo $repair['repair_cause']; ?></td>
                                        <td><?php echo isset($repair['technician_name']) ? $repair['technician_name'] : '-'; ?></td>
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
                                            <span class="badge <?php echo $statusClass; ?> status-badge">
                                                <?php echo $statusText; ?>
                                            </span>
                                        </td>
                                        <td class="action-buttons">
                                            <button type="button" class="btn btn-sm btn-info" onclick="updateRepair(<?php echo $repair['repair_id']; ?>)">
                                                <i class="fas fa-edit"></i> อัพเดท
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="viewDetail(<?php echo $repair['repair_id']; ?>)">
                                                <i class="fas fa-eye"></i> ดูรายละเอียด
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

    <!-- Modal อัพเดทสถานะการซ่อม -->
    <div class="modal fade" id="updateRepairModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">อัพเดทสถานะการซ่อม</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="updateRepairForm" method="POST">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="repair_id" id="update_repair_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="repair_status">สถานะการซ่อม</label>
                            <select class="form-control" id="repair_status" name="repair_status" required>
                                <option value="waiting">รอดำเนินการ</option>
                                <option value="in_progress">กำลังดำเนินการ</option>
                                <option value="completed">เสร็จสิ้น</option>
                                <option value="cancelled">ยกเลิก</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="technician_id">ช่างซ่อม</label>
                            <select class="form-control select2bs4" id="technician_id" name="technician_id">
                                <option value="">เลือกช่างซ่อม</option>
                                <?php foreach ($technicians as $tech): ?>
                                    <option value="<?php echo $tech['user_id']; ?>">
                                        <?php echo $tech['fullname']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="repair_detail">รายละเอียดการซ่อม</label>
                            <textarea class="form-control" id="repair_detail" name="repair_detail" rows="3"></textarea>
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

    <!-- Modal แสดงรายละเอียด -->
    <div class="modal fade" id="viewDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">รายละเอียดการแจ้งซ่อม</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- รายละเอียดจะถูกเติมด้วย JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</div>

<!-- REQUIRED SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<script>
                $(document).ready(function() {
                    // Initialize DataTables
                    $('#repairsTable').DataTable({
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
                        },
                        "order": [[0, "desc"]]
                    });

                    // Initialize Select2
                    $('.select2bs4').select2({
                        theme: 'bootstrap4'
                    });
                });

                    // ฟังก์ชันอัพเดทการซ่อม
                    function updateRepair(repairId) {
                        $.ajax({
                            url: '../api/get_repair.php',
                            type: 'GET',
                            data: { repair_id: repairId },
                            success: function(response) {
                                try {
                                    var repair = JSON.parse(response);
                                    $('#update_repair_id').val(repair.repair_id);
                                    $('#repair_status').val(repair.repair_status || 'waiting');
                                    $('#technician_id').val(repair.technician || '').trigger('change');
                                    $('#repair_detail').val(repair.repair_detail || '');
                                    $('#updateRepairModal').modal('show');
                                } catch(e) {
                                    console.error('Error parsing repair data:', e);
                                    alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
                                }
                            },
                            error: function() {
                                alert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
                            }
                        });
                    }

                    // ฟังก์ชันดูรายละเอียด
                    function viewDetail(repairId) {
                        $.ajax({
                            url: '../api/get_repair.php',
                            type: 'GET',
                            data: { repair_id: repairId },
                            success: function(response) {
                                try {
                                    var repair = JSON.parse(response);
                                    var statusText = '';
                                    var statusClass = '';
                                    
                                    switch(repair.repair_status) {
                                        case 'waiting':
                                            statusClass = 'badge-warning';
                                            statusText = 'รอดำเนินการ';
                                            break;
                                        case 'in_progress':
                                            statusClass = 'badge-primary';
                                            statusText = 'กำลังดำเนินการ';
                                            break;
                                        case 'completed':
                                            statusClass = 'badge-success';
                                            statusText = 'เสร็จสิ้น';
                                            break;
                                        case 'cancelled':
                                            statusClass = 'badge-danger';
                                            statusText = 'ยกเลิก';
                                            break;
                                            }
                                    const detailHtml = `
                                <div class="row">...`;
                                

                var detailHtml = 
                    '<div class="row">' +
                    '    <div class="col-md-6">' +
                    '        <h6>ข้อมูลพัสดุ</h6>' +
                    '        <p><strong>รหัสพัสดุ:</strong> ' + (repair.psd_id || '-') + '</p>' +
                    '        <p><strong>ชื่อพัสดุ:</strong> ' + (repair.brand_name || '') + ' ' + (repair.models || '') + '</p>' +
                    '        <p><strong>แผนก:</strong> ' + (repair.dept_name || '-') + '</p>' +
                    '    </div>' +
                    '    <div class="col-md-6">' +
                    '        <h6>ข้อมูลการแจ้งซ่อม</h6>' +
                    '        <p><strong>รหัสแจ้งซ่อม:</strong> ' + repair.repair_id + '</p>' +
                    '        <p><strong>ผู้แจ้ง:</strong> ' + (repair.reporter_name || '-') + '</p>' +
                    '        <p><strong>วันที่แจ้ง:</strong> ' + formatDate(repair.date_1) + '</p>' +
                    '        <p><strong>สถานะ:</strong> <span class="badge ' + statusClass + '">' + statusText + '</span></p>' +
                    '    </div>' +
                    '</div>';
                    '<div class="row mt-3">' +
                '    <div class="col-12">' +
                '        <h6>สาเหตุการแจ้งซ่อม</h6>' +
                '        <p>' + repair.repair_cause + '</p>' +
                '    </div>' +
                '</div>' +
                '<div class="row mt-3">' +
                '    <div class="col-md-6">' +
                '        <h6>ช่างซ่อม</h6>' +
                '        <p>' + (repair.technician_name || '-') + '</p>' +
                '    </div>' +
                '    <div class="col-md-6">' +
                '        <h6>วันที่ซ่อมเสร็จ</h6>' +
                '        <p>' + (repair.date_2 ? new Date(repair.date_2).toLocaleDateString('th-TH') : '-') + '</p>' +
                '    </div>' +
                '</div>' +
                '<div class="row mt-3">' +
                '    <div class="col-12">' +
                '        <h6>รายละเอียดการซ่อม</h6>' +
                '        <p>' + (repair.repair_detail || '-') + '</p>' +
                '    </div>' +
                '</div>';
                    
             
                    $('#viewDetailModal .modal-body').html(detailHtml);
                $('#viewDetailModal').modal('show');
                        } catch(e) {
                            console.error('Error parsing repair data:', e);
                            alert('เกิดข้อผิดพลาดในการแสดงข้อมูล');
                        }
                    },
                    error: function() {
                        alert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
                    }
                });
            }
                // เพิ่มฟังก์ชันช่วยจัดการวันที่
                function formatDate(dateStr) {
                    if (!dateStr) return '-';
                    var d = new Date(dateStr);
                    return d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear();
                }

                
</script>

</body>
</html>