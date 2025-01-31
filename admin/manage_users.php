<?php
session_start();
require_once '../config/configdb.php';
require_once '../classes/UserManagement.php';
require_once '../classes/Department.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$userManager = new UserManagement();
$departmentManager = new Department();

// ดึงข้อมูลแผนก
$departments = $departmentManager->getAllDepartments();

// จัดการการส่งข้อมูลฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $result = $userManager->addUser($_POST);
                if ($result === 'duplicate') {
                    $error = "ชื่อผู้ใช้หรืออีเมลนี้มีในระบบแล้ว";
                } elseif ($result) {
                    $success = "เพิ่มผู้ใช้เรียบร้อยแล้ว";
                } else {
                    $error = "เกิดข้อผิดพลาดในการเพิ่มผู้ใช้";
                }
                break;

            case 'edit':
                $result = $userManager->updateUser($_POST['user_id'], $_POST);
                if ($result) {
                    $success = "แก้ไขข้อมูลผู้ใช้เรียบร้อยแล้ว";
                } else {
                    $error = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล";
                }
                break;

            case 'delete':
                $result = $userManager->deleteUser($_POST['user_id']);
                if ($result) {
                    $success = "ลบผู้ใช้เรียบร้อยแล้ว";
                } else {
                    $error = "เกิดข้อผิดพลาดในการลบผู้ใช้";
                }
                break;
        }
    }
}

// ดึงข้อมูลผู้ใช้ทั้งหมด
$users = $userManager->getAllUsers();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้ - ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ โรงพยาบาลห้วยเกิ้ง</title>
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
        .action-buttons .btn {
            margin: 0 2px;
        }
        .user-role {
            font-size: 0.9em;
            padding: 5px 10px;
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
                        <h1>จัดการผู้ใช้งาน</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">หน้าหลัก</a></li>
                            <li class="breadcrumb-item active">จัดการผู้ใช้งาน</li>
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

                <!-- เพิ่มผู้ใช้ใหม่ -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus mr-1"></i>
                            เพิ่มผู้ใช้ใหม่
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="addUserForm" method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fullname">ชื่อ-นามสกุล</label>
                                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="username">ชื่อผู้ใช้</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password">รหัสผ่าน</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">อีเมล</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="telephone">เบอร์โทรศัพท์</label>
                                        <input type="tel" class="form-control" id="telephone" name="telephone">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="position">ตำแหน่ง</label>
                                        <input type="text" class="form-control" id="position" name="position">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="dept_id">แผนก</label>
                                        <select class="form-control select2bs4" id="dept_id" name="dept_id" required>
                                            <option value="">เลือกแผนก</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?php echo $dept['dept_id']; ?>">
                                                    <?php echo $dept['dept_name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="level">ระดับผู้ใช้</label>
                                        <select class="form-control" id="level" name="level" required>
                                            <option value="user">ผู้ใช้ทั่วไป</option>
                                            <option value="technician">ช่างซ่อม</option>
                                            <option value="admin">ผู้ดูแลระบบ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> บันทึก
                            </button>
                        </form>
                    </div>
                </div>

                <!-- รายการผู้ใช้ -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users mr-1"></i>
                            รายการผู้ใช้ทั้งหมด
                        </h3>
                    </div>
                    <div class="card-body">
                        <table id="usersTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>รหัส</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>ชื่อผู้ใช้</th>
                                    <th>แผนก</th>
                                    <th>ตำแหน่ง</th>
                                    <th>ระดับผู้ใช้</th>
                                    <th>อีเมล</th>
                                    <th>เบอร์โทร</th>
                                    <th>การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['user_id']; ?></td>
                                        <td><?php echo $user['fullname']; ?></td>
                                        <td><?php echo $user['user_name']; ?></td>
                                        <td><?php echo $user['dept_name']; ?></td>
                                        <td><?php echo $user['position']; ?></td>
                                        <td>
                                            <?php
                                            $roleClass = '';
                                            $roleText = '';
                                            switch($user['level']) {
                                                case 'admin':
                                                    $roleClass = 'badge-danger';
                                                    $roleText = 'ผู้ดูแลระบบ';
                                                    break;
                                                case 'technician':
                                                    $roleClass = 'badge-info';
                                                    $roleText = 'ช่างซ่อม';
                                                    break;
                                                default:
                                                    $roleClass = 'badge-secondary';
                                                    $roleText = 'ผู้ใช้ทั่วไป';
                                            }
                                            ?>
                                            <span class="badge <?php echo $roleClass; ?> user-role">
                                                <?php echo $roleText; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $user['email']; ?></td>
                                        <td><?php echo $user['telephone']; ?></td>
                                        <td class="action-buttons">
                                            <button type="button" class="btn btn-sm btn-info" onclick="editUser(<?php echo $user['user_id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['user_id']; ?>)">
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
        </section>
    </div>

    <!-- Modal แก้ไขผู้ใช้ -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขข้อมูลผู้ใช้</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editUserForm" method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="modal-body">
                        <!-- ฟอร์มแก้ไขจะถูกเติมด้วย JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    </div>
                </form>
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
    $('#usersTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
        }
    });

    // Initialize Select2
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    });
});

// ฟังก์ชันแก้ไขผู้ใช้
function editUser(user_id) {
    $.get('../api/get_user.php', { user_id: user_id }, function(data) {
        const user = JSON.parse(data);
        $('#edit_user_id').val(user.user_id);
        
        // เติมข้อมูลในฟอร์มแก้ไข
        $('#editUserModal .modal-body').html(`
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="edit_fullname">ชื่อ-นามสกุล</label>
                        <input type="text" class="form-control" id="edit_fullname" name="fullname" value="${user.fullname}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="edit_username">ชื่อผู้ใช้</label>
                        <input type="text" class="form-control" id="edit_username" value="${user.user_name}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="edit_password">รหัสผ่าน (เว้นว่างถ้าไม่ต้องการเปลี่ยน)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="edit_email">อีเมล</label>
                        <input type="email" class="form-control" id="edit_email" name="email" value="${user.email}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="edit_telephone">เบอร์โทรศัพท์</label>
                        <input type="tel" class="form-control" id="edit_telephone" name="telephone" value="${user.telephone || ''}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="edit_position">ตำแหน่ง</label>
                        <input type="text" class="form-control" id="edit_position" name="position" value="${user.position || ''}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="edit_dept_id">แผนก</label>
                        <select class="form-control select2bs4" id="edit_dept_id" name="dept_id" required>
                            <option value="">เลือกแผนก</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['dept_id']; ?>">
                                    <?php echo $dept['dept_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="edit_level">ระดับผู้ใช้</label>
                        <select class="form-control" id="edit_level" name="level" required>
                            <option value="user">ผู้ใช้ทั่วไป</option>
                            <option value="technician">ช่างซ่อม</option>
                            <option value="admin">ผู้ดูแลระบบ</option>
                        </select>
                    </div>
                </div>
            </div>
        `);

        // กำหนดค่าให้กับ select
        $('#edit_dept_id').val(user.dept_id);
        $('#edit_level').val(user.level);

        // Initialize Select2 in modal
        $('#edit_dept_id').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#editUserModal')
        });

        $('#editUserModal').modal('show');
    });
}

// ฟังก์ชันลบผู้ใช้
function deleteUser(user_id) {
    if (confirm('คุณต้องการลบผู้ใช้นี้ใช่หรือไม่?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="user_id" value="${user_id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

</body>
</html>