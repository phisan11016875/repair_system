
<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/FSNManager.php';

// ตรวจสอบสิทธิ์
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

try {
    $fsnManager = new FSNManager();
    
    // จัดการการส่งข้อมูลฟอร์ม
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_group':
                // Validate input
                if (empty($_POST['group_id']) || !preg_match('/^[A-Za-z0-9]{2}$/', $_POST['group_id'])) {
                    $_SESSION['error'] = "รหัสกลุ่มไม่ถูกต้อง";
                    break;
                }
                if (empty($_POST['group_name'])) {
                    $_SESSION['error'] = "กรุณากรอกชื่อกลุ่ม";
                    break;
                }
                
                $data = array(
                    'group_id' => strtoupper(trim($_POST['group_id'])),
                    'group_name' => trim($_POST['group_name']),
                    'description' => trim($_POST['description']),
                    'created_by' => $_SESSION['user_id']
                );
                
                if ($fsnManager->addGroup($data)) {
                    $_SESSION['success'] = "เพิ่มกลุ่มพัสดุเรียบร้อยแล้ว";
                } else {
                    $_SESSION['error'] = "เกิดข้อผิดพลาดในการเพิ่มกลุ่มพัสดุ หรือรหัสกลุ่มซ้ำ";
                }
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
                break;

            case 'add_class':
                if (empty($_POST['class_id']) || !preg_match('/^[A-Za-z0-9]{2}$/', $_POST['class_id'])) {
                    $_SESSION['error'] = "รหัสประเภทไม่ถูกต้อง";
                    break;
                }
                
                $data = array(
                    'group_id' => trim($_POST['group_id']),
                    'class_id' => trim($_POST['class_id']),
                    'class_name' => trim($_POST['class_name']),
                    'description' => trim($_POST['description']),
                    'created_by' => $_SESSION['user_id']
                );
                
                if ($fsnManager->addClass($data)) {
                    $_SESSION['success'] = "เพิ่มประเภทพัสดุเรียบร้อยแล้ว";
                } else {
                    $_SESSION['error'] = "เกิดข้อผิดพลาดในการเพิ่มประเภทพัสดุ";
                }
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
                break;

            case 'add_type':
                if (empty($_POST['type_id']) || !preg_match('/^[A-Za-z0-9]{3}$/', $_POST['type_id'])) {
                    $_SESSION['error'] = "รหัสชนิดไม่ถูกต้อง";
                    break;
                }
                
                $data = array(
                    'group_class' => trim($_POST['group_id'] . $_POST['class_id']),
                    'type_id' => trim($_POST['type_id']),
                    'type_name' => trim($_POST['type_name']),
                    'description' => trim($_POST['description']),
                    'created_by' => $_SESSION['user_id']
                );
                
                if ($fsnManager->addType($data)) {
                    $_SESSION['success'] = "เพิ่มชนิดพัสดุเรียบร้อยแล้ว";
                } else {
                    $_SESSION['error'] = "เกิดข้อผิดพลาดในการเพิ่มชนิดพัสดุ";
                }
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
                break;
        }
    }

    // ดึงข้อมูลทั้งหมด
    $groups = $fsnManager->getAllGroups();
    
    // Get flash messages
    if (isset($_SESSION['success'])) {
        $success = $_SESSION['success'];
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        $error = $_SESSION['error'];
        unset($_SESSION['error']);
    }

} catch(Exception $e) {
    error_log("Error in manage_fsn.php: " . $e->getMessage());
    $error = "เกิดข้อผิดพลาดในระบบ";
    $groups = array();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการระบบ FSN - ระบบจัดการพัสดุ/ครุกัณฑ์</title>
    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.min.css" rel="stylesheet">
    <?php include '../includes/header.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include '../includes/navbar.php'; ?>
        <?php include '../includes/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">จัดการระบบพัสดุ/ครุภัณฑ์ (FSN)</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Alert Messages -->
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

                    <!-- Main Card -->
                    <div class="card">
                        <!-- Tab Headers -->
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#groups" data-toggle="tab">กลุ่มพัสดุ</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#classes" data-toggle="tab">ประเภทพัสดุ</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#types" data-toggle="tab">ชนิดพัสดุ</a>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab Contents -->
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- กลุ่มพัสดุ Tab -->
                                <div class="tab-pane active" id="groups">
                                    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addGroupModal">
                                        <i class="fas fa-plus"></i> เพิ่มกลุ่มพัสดุ
                                    </button>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="groupsTable">
                                            <thead>
                                                <tr>
                                                    <th>รหัส</th>
                                                    <th>ชื่อกลุ่ม</th>
                                                    <th>คำอธิบาย</th>
                                                    <th>ผู้สร้าง</th>
                                                    <th>วันที่สร้าง</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($groups as $group): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($group['group_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($group['group_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($group['description']); ?></td>
                                                    <td><?php echo htmlspecialchars($group['created_by_name']); ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($group['created_at'])); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- ประเภทพัสดุ Tab -->
                                <div class="tab-pane" id="classes">
                                    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addClassModal">
                                        <i class="fas fa-plus"></i> เพิ่มประเภทพัสดุ
                                    </button>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="classesTable">
                                            <thead>
                                                <tr>
                                                    <th>กลุ่มพัสดุ</th>
                                                    <th>รหัสประเภท</th>
                                                    <th>ชื่อประเภท</th>
                                                    <th>คำอธิบาย</th>
                                                    <th>ผู้สร้าง</th>
                                                    <th>วันที่สร้าง</th>
                                                </tr>
                                            </thead>
                                            <tbody id="classesContent">
                                                <!-- จะถูกเติมด้วย AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- ชนิดพัสดุ Tab -->
                                <div class="tab-pane" id="types">
                                    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addTypeModal">
                                        <i class="fas fa-plus"></i> เพิ่มชนิดพัสดุ
                                    </button>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="typesTable">
                                            <thead>
                                                <tr>
                                                    <th>กลุ่ม-ประเภท</th>
                                                    <th>รหัสชนิด</th>
                                                    <th>ชื่อชนิด</th>
                                                    <th>คำอธิบาย</th>
                                                    <th>ผู้สร้าง</th>
                                                    <th>วันที่สร้าง</th>
                                                </tr>
                                            </thead>
                                            <tbody id="typesContent">
                                                <!-- จะถูกเติมด้วย AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../includes/footer.php'; ?>
    </div>
    <!-- Modal เพิ่มกลุ่มพัสดุ -->
<div class="modal fade" id="addGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มกลุ่มพัสดุ</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addGroupForm" method="POST">
                <input type="hidden" name="action" value="add_group">
                <div class="modal-body">
                    <div class="form-group">
                        <label>รหัสกลุ่ม <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="group_id" maxlength="2" required 
                               pattern="[A-Za-z0-9]{2}" title="กรุณากรอกตัวเลขหรือตัวอักษร 2 ตัว">
                        <small class="text-muted">ตัวเลขหรือตัวอักษร 2 ตัว</small>
                    </div>
                    <div class="form-group">
                        <label>ชื่อกลุ่ม <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="group_name" required>
                    </div>
                    <div class="form-group">
                        <label>คำอธิบาย</label>
                        <textarea class="form-control" name="description"></textarea>
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

<!-- Modal เพิ่มประเภทพัสดุ -->
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มประเภทพัสดุ</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addClassForm" method="POST">
                <input type="hidden" name="action" value="add_class">
                <div class="modal-body">
                    <div class="form-group">
                        <label>กลุ่มพัสดุ <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="group_id" required>
                            <option value="">เลือกกลุ่มพัสดุ</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?php echo htmlspecialchars($group['group_id']); ?>">
                                    <?php echo htmlspecialchars($group['group_id'] . ' - ' . $group['group_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>รหัสประเภท <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="class_id" maxlength="2" required
                               pattern="[A-Za-z0-9]{2}" title="กรุณากรอกตัวเลขหรือตัวอักษร 2 ตัว">
                        <small class="text-muted">ตัวเลขหรือตัวอักษร 2 ตัว</small>
                    </div>
                    <div class="form-group">
                        <label>ชื่อประเภท <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="class_name" required>
                    </div>
                    <div class="form-group">
                        <label>คำอธิบาย</label>
                        <textarea class="form-control" name="description"></textarea>
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

<!-- Modal เพิ่มชนิดพัสดุ -->
<div class="modal fade" id="addTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มชนิดพัสดุ</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addTypeForm" method="POST">
                <input type="hidden" name="action" value="add_type">
                <div class="modal-body">
                    <div class="form-group">
                        <label>กลุ่มพัสดุ <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="group_id" id="typeGroupSelect" required>
                            <option value="">เลือกกลุ่มพัสดุ</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?php echo htmlspecialchars($group['group_id']); ?>">
                                    <?php echo htmlspecialchars($group['group_id'] . ' - ' . $group['group_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ประเภทพัสดุ <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="class_id" id="typeClassSelect" required disabled>
                            <option value="">เลือกประเภทพัสดุ</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>รหัสชนิด <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="type_id" maxlength="3" required
                               pattern="[A-Za-z0-9]{3}" title="กรุณากรอกตัวเลขหรือตัวอักษร 3 ตัว">
                        <small class="text-muted">ตัวเลขหรือตัวอักษร 3 ตัว</small>
                    </div>
                    <div class="form-group">
                        <label>ชื่อชนิด <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="type_name" required>
                    </div>
                    <div class="form-group">
                        <label>คำอธิบาย</label>
                        <textarea class="form-control" name="description"></textarea>
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
<!-- Required Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize plugins
    initializePlugins();
    
    // Event handlers
    initializeEventHandlers();
    
    // Load initial data for active tab
    if($('#classes.active').length) loadClasses();
    if($('#types.active').length) loadTypes();
});

function initializePlugins() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        dropdownParent: $('.modal')
    });

    // Initialize DataTables
    $('#groupsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
        },
        "order": [[0, "asc"]]
    });
}

function initializeEventHandlers() {
    // Form validations
    $('#addGroupForm').on('submit', function(e) {
        return validateForm(e, this, 2);
    });

    $('#addClassForm').on('submit', function(e) {
        return validateForm(e, this, 2);
    });

    $('#addTypeForm').on('submit', function(e) {
        return validateForm(e, this, 3);
    });

    // Handle FSN Class Loading
    $('#typeGroupSelect').change(function() {
        var groupId = $(this).val();
        if (groupId) {
            loadFSNClasses(groupId);
        } else {
            $('#typeClassSelect').html('<option value="">เลือกประเภทพัสดุ</option>').prop('disabled', true);
        }
    });

    // Tab change handler
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        const targetId = $(e.target).attr('href');
        if (targetId === '#classes') {
            loadClasses();
        } else if (targetId === '#types') {
            loadTypes();
        }
    });

    // Reset forms on modal close
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('form').removeClass('was-validated');
        $(this).find('select').val('').trigger('change');
        if ($(this).attr('id') === 'addTypeModal') {
            $('#typeClassSelect').prop('disabled', true);
        }
    });

    // Auto-hide alerts
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 3000);
}

function validateForm(e, form, codeLength) {
    const $form = $(form);
    const id = $form.find('input[type="text"]').first().val();
    const name = $form.find('input[type="text"]').eq(1).val();
    
    if (!id || !name) {
        e.preventDefault();
        alert('กรุณากรอกข้อมูลให้ครบถ้วน');
        return false;
    }
    
    const pattern = new RegExp(`^[A-Za-z0-9]{${codeLength}}$`);
    if (!pattern.test(id)) {
        e.preventDefault();
        alert(`รหัสต้องเป็นตัวเลขหรือตัวอักษร ${codeLength} ตัวเท่านั้น`);
        return false;
    }
    
    return true;
}

function loadFSNClasses(groupId) {
    $.ajax({
        url: '../api/get_fsn_classes.php',
        method: 'GET',
        data: { group_id: groupId },
        success: function(response) {
            if (response.success) {
                var options = '<option value="">เลือกประเภทพัสดุ</option>';
                response.data.forEach(function(item) {
                    options += `<option value="${item.class_id}">${item.class_id} - ${item.class_name}</option>`;
                });
                $('#typeClassSelect').html(options).prop('disabled', false);
            } else {
                alert('เกิดข้อผิดพลาด: ' + response.message);
            }
        },
        error: function() {
            alert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
        }
    });
}

function loadClasses() {
    $.ajax({
        url: '../api/get_fsn_classes.php',
        method: 'GET',
        success: function(response) {
            if ($.fn.DataTable.isDataTable('#classesTable')) {
                $('#classesTable').DataTable().destroy();
            }
            
            var html = '';
            if (response.success) {
                response.data.forEach(function(item) {
                    html += `<tr>
                        <td>${item.group_id} - ${item.group_name}</td>
                        <td>${item.class_id}</td>
                        <td>${item.class_name}</td>
                        <td>${item.description || '-'}</td>
                        <td>${item.created_by_name || '-'}</td>
                        <td>${formatDate(item.created_at)}</td>
                    </tr>`;
                });
            }
            $('#classesContent').html(html);
            
            $('#classesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
                }
            });
        },
        error: function() {
            $('#classesContent').html('<tr><td colspan="6" class="text-center">ไม่สามารถโหลดข้อมูลได้</td></tr>');
        }
    });
}

function loadTypes() {
    $.ajax({
        url: '../api/get_fsn_types.php',
        method: 'GET',
        beforeSend: function() {
            $('#typesContent').html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</td></tr>');
        },
        success: function(response) {
            if ($.fn.DataTable.isDataTable('#typesTable')) {
                $('#typesTable').DataTable().destroy();
            }
            
            let html = '';
            if (response.success && response.data.length > 0) {
                response.data.forEach(function(item) {
                    html += `<tr>
                        <td>${item.group_class}</td>
                        <td>${item.type_id}</td>
                        <td>${item.type_name}</td>
                        <td>${item.description || '-'}</td>
                        <td>${item.created_by_name || '-'}</td>
                        <td>${formatDate(item.created_at)}</td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="6" class="text-center">ไม่พบข้อมูล</td></tr>';
            }
            $('#typesContent').html(html);
            
            $('#typesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            $('#typesContent').html('<tr><td colspan="6" class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>');
        }
    });
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    var date = new Date(dateStr);
    return date.getDate() + '/' + 
           (date.getMonth() + 1) + '/' + 
           date.getFullYear() + ' ' +
           String(date.getHours()).padStart(2, '0') + ':' +
           String(date.getMinutes()).padStart(2, '0');
}
</script>
</body>
</html>