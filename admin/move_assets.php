<?php
session_start();
require_once '../config/database.php';
//require_once 'config.php';
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

            // ดึงข้อมูลพัสดุ
            $assets = $assetManager->getAllAssets();
            
            // ดึงข้อมูลแผนก
            $departments = $departmentManager->getAllDepartments();
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            $error = "เกิดข้อผิดพลาดในการโหลดข้อมูล";
            }

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ โรงพยาบาลห้วยเกิ้ง</title>
    <?php include '../includes/header.php'; ?>
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
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
                            <h1>บันทึกการโยกย้ายพัสดุ/ครุภัณฑ์</h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">แบบฟอร์มโยกย้ายพัสดุ/ครุภัณฑ์</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="moveAssetForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="psd_id">เลือกพัสดุ/ครุภัณฑ์</label>
                                            <select class="form-control select2bs4" id="psd_id" name="psd_id" required>
                                                <option value="">เลือกพัสดุ/ครุภัณฑ์</option>
                                                <?php foreach ($assets as $asset): ?>
                                                    <option value="<?php echo $asset['psd_id']; ?>" 
                                                            data-dept="<?php echo $asset['dept_id']; ?>"
                                                            data-dept-name="<?php echo $asset['dept_name']; ?>">
                                                        <?php echo $asset['brand_name'] . ' ' . $asset['models'] . 
                                                                 ' (' . $asset['dept_name'] . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>แผนกปัจจุบัน</label>
                                            <input type="text" class="form-control" id="current_dept" readonly>
                                            <input type="hidden" name="current_dept_id" id="current_dept_id">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="new_dept_id">แผนกใหม่</label>
                                            <select class="form-control select2bs4" id="new_dept_id" name="new_dept_id" required>
                                                <option value="">เลือกแผนก</option>
                                                <?php foreach ($departments as $dept): ?>
                                                    <option value="<?php echo $dept['dept_id']; ?>">
                                                        <?php echo $dept['dept_name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="move_cause">เหตุผลการโยกย้าย</label>
                                            <textarea class="form-control" id="move_cause" name="move_cause" rows="3" required></textarea>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">บันทึกการโยกย้าย</button>
                            </form>
                        </div>
                    </div>

                    <!-- ประวัติการโยกย้าย -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">ประวัติการโยกย้าย</h3>
                        </div>
                        <div class="card-body">
                            <div id="moveHistory">
                                <!-- ประวัติจะถูกโหลดด้วย AJAX -->
                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

    <script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        });

        // โหลดประวัติการโยกย้าย
        function loadMoveHistory() {
            $.get('../api/get_recent_moves.php', function(data) {
                $('#moveHistory').html(data);
            });
        }

        // อัพเดทแผนกปัจจุบันเมื่อเลือกพัสดุ
        $('#psd_id').change(function() {
            const selectedOption = $(this).find('option:selected');
            const deptId = selectedOption.data('dept');
            const deptName = selectedOption.data('dept-name');
            $('#current_dept').val(deptName);
            $('#current_dept_id').val(deptId);
        });

        // Validate form ก่อนส่ง
        $('#moveAssetForm').submit(function(e) {
            const currentDept = $('#current_dept_id').val();
            const newDept = $('#new_dept_id').val();
            
            if (currentDept === newDept) {
                e.preventDefault();
                alert('กรุณาเลือกแผนกใหม่ที่แตกต่างจากแผนกปัจจุบัน');
                return false;
            }
        });

        // โหลดประวัติเมื่อโหลดหน้า
        loadMoveHistory();
    });
    </script>
</body>
</html>