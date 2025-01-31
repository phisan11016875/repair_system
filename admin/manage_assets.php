<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../classes/Database.php';
require_once '../classes/AssetManagement.php';
require_once '../classes/Department.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

try {
    $assetManager = new AssetManagement();
    $departmentManager = new Department();

    // ดึงข้อมูลแผนก
    $departments = $departmentManager->getAllDepartments();

    // จัดการการส่งข้อมูลฟอร์ม
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                if (empty($_POST['brand_name']) || empty($_POST['dept_id'])) {
                    $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
                    break;
                }
                $postData = array(
                    'brand_name' => trim($_POST['brand_name']),
                    'models' => trim($_POST['models']),
                    'type_recieve' => trim($_POST['type_recieve']),
                    'dept_id' => intval($_POST['dept_id']),
                    'psd_total' => intval($_POST['psd_total']),
                    'date_recieve' => $_POST['date_recieve'],
                    'psd_status' => 'active'
                );
                $result = $assetManager->addAsset($postData);
                if ($result) {
                    $success = "เพิ่มพัสดุเรียบร้อยแล้ว";
                } else {
                    $error = "เกิดข้อผิดพลาดในการเพิ่มพัสดุ";
                }
                break;

            case 'edit':
                if (empty($_POST['psd_id']) || empty($_POST['brand_name'])) {
                    $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
                    break;
                }
                $updateData = array(
                    'brand_name' => trim($_POST['brand_name']),
                    'models' => trim($_POST['models']),
                    'type_recieve' => trim($_POST['type_recieve']),
                    'dept_id' => intval($_POST['dept_id']),
                    'psd_total' => intval($_POST['psd_total']),
                    'date_recieve' => $_POST['date_recieve'],
                    'psd_status' => isset($_POST['psd_status']) ? $_POST['psd_status'] : 'active'
                );
                $result = $assetManager->updateAsset(intval($_POST['psd_id']), $updateData);
                if ($result) {
                    $success = "แก้ไขข้อมูลพัสดุเรียบร้อยแล้ว";
                } else {
                    $error = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล";
                }
                break;

            case 'delete':
                if (empty($_POST['psd_id'])) {
                    $error = "ไม่พบรหัสพัสดุที่ต้องการลบ";
                    break;
                }
                $result = $assetManager->deleteAsset(intval($_POST['psd_id']));
                if ($result) {
                    $success = "ลบพัสดุเรียบร้อยแล้ว";
                } else {
                    $error = "เกิดข้อผิดพลาดในการลบพัสดุ";
                }
                break;
        }
    }

    // ดึงข้อมูลพัสดุทั้งหมด
    $assets = $assetManager->getAllAssets();

} catch(Exception $e) {
    error_log("Error in manage_assets.php: " . $e->getMessage());
    $error = "เกิดข้อผิดพลาดในการทำงาน กรุณาลองใหม่อีกครั้ง";
    $assets = array();
    $departments = array();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการพัสดุ - ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        .action-buttons .btn { margin: 0 2px; }
        .badge { font-size: 0.9em; padding: 5px 10px; }
    </style>
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
                            <h1>จัดการพัสดุ/ครุภัณฑ์</h1>
                        </div>
                    </div>
                </div>
            </section>

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

                    <!-- เพิ่มพัสดุใหม่ -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus-circle mr-1"></i>
                                เพิ่มรายการพัสดุ/ครุภัณฑ์ใหม่
                            </h3>
                        </div>
                        <div class="card-body">
                            <form id="addAssetForm" method="POST">
                                <input type="hidden" name="action" value="add">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="brand_name">ยี่ห้อ <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="brand_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="models">รุ่น</label>
                                            <input type="text" class="form-control" name="models">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="type_recieve">ประเภท</label>
                                            <input type="text" class="form-control" name="type_recieve">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="dept_id">แผนก <span class="text-danger">*</span></label>
                                            <select class="form-control" name="dept_id" required>
                                                <option value="">เลือกแผนก</option>
                                                <?php foreach ($departments as $dept): ?>
                                                    <option value="<?php echo htmlspecialchars($dept['dept_id']); ?>">
                                                        <?php echo htmlspecialchars($dept['dept_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="psd_total">จำนวน</label>
                                            <input type="number" class="form-control" name="psd_total" value="1" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date_recieve">วันที่รับ</label>
                                            <input type="date" class="form-control" name="date_recieve" 
                                                   value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> บันทึก
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- รายการพัสดุ -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i>
                                รายการพัสดุ/ครุภัณฑ์ทั้งหมด
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="assetsTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>รหัส</th>
                                            <th>ยี่ห้อ</th>
                                            <th>รุ่น</th>
                                            <th>ประเภท</th>
                                            <th>แผนก</th>
                                            <th>จำนวน</th>
                                            <th>วันที่รับ</th>
                                            <th>สถานะ</th>
                                            <th>การจัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($assets as $asset): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($asset['psd_id']); ?></td>
                                                <td><?php echo htmlspecialchars($asset['brand_name']); ?></td>
                                                <td><?php echo htmlspecialchars($asset['models']); ?></td>
                                                <td><?php echo htmlspecialchars($asset['type_recieve']); ?></td>
                                                <td><?php echo htmlspecialchars($asset['dept_name']); ?></td>
                                                <td><?php echo htmlspecialchars($asset['psd_total']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($asset['date_recieve'])); ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = '';
                                                    $statusText = '';
                                                    switch($asset['psd_status']) {
                                                        case 'active':
                                                            $statusClass = 'badge-success';
                                                            $statusText = 'ใช้งานปกติ';
                                                            break;
                                                        case 'repair':
                                                            $statusClass = 'badge-warning';
                                                            $statusText = 'กำลังซ่อม';
                                                            break;
                                                        case 'inactive':
                                                            $statusClass = 'badge-danger';
                                                            $statusText = 'ไม่ใช้งาน';
                                                            break;
                                                        default:
                                                            $statusClass = 'badge-secondary';
                                                            $statusText = 'ไม่ระบุ';
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?>">
                                                        <?php echo $statusText; ?>
                                                    </span>
                                                </td>
                                                <td class="action-buttons">
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            onclick="editAsset(<?php echo $asset['psd_id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="confirmDelete(<?php echo $asset['psd_id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../includes/footer.php'; ?>
    </div>

    <!-- Modal แก้ไขพัสดุ -->
    <div class="modal fade" id="editAssetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขข้อมูลพัสดุ/ครุภัณฑ์</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="editAssetForm" method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="psd_id" id="edit_psd_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_brand_name">ยี่ห้อ</label>
                                    <input type="text" class="form-control" name="brand_name" id="edit_brand_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_models">รุ่น</label>
                                    <input type="text" class="form-control" name="models" id="edit_models">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_type_recieve">ประเภท</label>
                                    <input type="text" class="form-control" name="type_recieve" id="edit_type_recieve">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_dept_id">แผนก</label>
                                    <select class="form-control" name="dept_id" id="edit_dept_id" required>
                                        <option value="">เลือกแผนก</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo htmlspecialchars($dept['dept_id']); ?>">
                                                <?php echo htmlspecialchars($dept['dept_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_psd_total">จำนวน</label>
                                    <input type="number" class="form-control" name="psd_total" id="edit_psd_total" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_date_recieve">วันที่รับ</label>
                                    <input type="date" class="form-control" name="date_recieve" id="edit_date_recieve">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_psd_status">สถานะ</label>
                            <select class="form-control" name="psd_status" id="edit_psd_status">
                                <option value="active">ใช้งานปกติ</option>
                                <option value="repair">กำลังซ่อม</option>
                                <option value="inactive">ไม่ใช้งาน</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal ยืนยันการลบ -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ยืนยันการลบ</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="psd_id" id="delete_psd_id">
                    <div class="modal-body">
                        <p>คุณแน่ใจหรือไม่ที่จะลบพัสดุ/ครุภัณฑ์รายการนี้?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-danger">ยืนยันการลบ</button>
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
        $('#assetsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            },
            "order": [[0, "desc"]]
        });
    });

    function editAsset(psdId) {
        $.ajax({
            url: '../api/get_asset.php',
            type: 'GET',
            data: { psd_id: psdId },
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    $('#edit_psd_id').val(data.psd_id);
                    $('#edit_brand_name').val(data.brand_name);
                    $('#edit_models').val(data.models);
                    $('#edit_type_recieve').val(data.type_recieve);
                    $('#edit_dept_id').val(data.dept_id);
                    $('#edit_psd_total').val(data.psd_total);
                    $('#edit_date_recieve').val(data.date_recieve);
                    $('#edit_psd_status').val(data.psd_status);
                    $('#editAssetModal').modal('show');
                } catch(e) {
                    alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
                    console.error(e);
                }
            },
            error: function() {
                alert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
            }
        });
    }

    function confirmDelete(psdId) {
        $('#delete_psd_id').val(psdId);
        $('#deleteConfirmModal').modal('show');
    }
    </script>
</body>
</html>