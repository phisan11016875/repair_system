<<<<<<< HEAD
<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Department.php';
require_once 'classes/Logger.php';

// ถ้าล็อกอินแล้วให้ไปหน้า dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . $_SESSION['level'] . '/dashboard.php');
    exit();
}

        try {
            $db = new Database();
            $conn = $db->getConnection();
            $departmentManager = new Department();
            $logger = new Logger('auth');

            // ดึงข้อมูลแผนก
            $departments = $departmentManager->getAllDepartments();
        } catch (Exception $e) {
            error_log("Error in register.php: " . $e->getMessage());
            $error = "เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง";
            $departments = array();
        }
// จัดการการสมัครสมาชิก
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // ตรวจสอบข้อมูลที่จำเป็น
        $required_fields = array('username', 'password', 'confirm_password', 'fullname', 'email', 'dept_id');
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("กรุณากรอกข้อมูลให้ครบถ้วน");
            }
        }

        // ตรวจสอบรหัสผ่าน
        if ($_POST['password'] !== $_POST['confirm_password']) {
            throw new Exception("รหัสผ่านไม่ตรงกัน");
        }

        if (strlen($_POST['password']) < 6) {
            throw new Exception("รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร");
        }

        // ตรวจสอบรูปแบบอีเมล
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("รูปแบบอีเมลไม่ถูกต้อง");
        }

        // ตรวจสอบชื่อผู้ใช้และอีเมลซ้ำ
        $stmt = $conn->prepare("SELECT COUNT(*) FROM psd_users WHERE user_name = ? OR email = ?");
        $stmt->execute([$_POST['username'], $_POST['email']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("ชื่อผู้ใช้หรืออีเมลนี้มีในระบบแล้ว");
        }

        // เข้ารหัสรหัสผ่าน
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // เพิ่มข้อมูลผู้ใช้
        $stmt = $conn->prepare("
            INSERT INTO psd_users (
                user_name, pass_word, fullname, email, telephone, 
                dept_id, position, level, created_at
            ) VALUES (
                :username, :password, :fullname, :email, :telephone,
                :dept_id, :position, 'user', NOW()
            )
        ");

            // เพิ่มฟังก์ชันทำความสะอาดข้อมูล
            function cleanInput($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }

            // ทำความสะอาดข้อมูลก่อนบันทึก
            $stmt->execute(array(
                ':username' => $_POST['username'],
                ':password' => $hashed_password,
                ':fullname' => $_POST['fullname'],
                ':email' => $_POST['email'],
                ':telephone' => isset($_POST['telephone']) ? $_POST['telephone'] : null,
                ':dept_id' => $_POST['dept_id'],
                ':position' => isset($_POST['position']) ? $_POST['position'] : null
            ));

            $userId = $conn->lastInsertId();
        
        // บันทึก log
        $logger->write(
            'REGISTER',
            "New user registration: {$_POST['username']}",
            $userId,
            'success'
        );

        $success = "สมัครสมาชิกเรียบร้อยแล้ว กรุณาเข้าสู่ระบบ";

    } catch (Exception $e) {
        $error = $e->getMessage();
        $logger->write(
            'REGISTER',
            "Registration failed: {$error}",
            null,
            'error'
        );
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ โรงพยาบาลห้วยเกิ้ง</title>
    <!-- Google Font: Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <!-- Custom styles -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        .register-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0072ff 0%, #00c6ff 100%);
        }
        .register-box {
            width: 600px;
            margin: 0;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        .register-logo {
            margin-bottom: 25px;
        }
        .register-card-body {
            padding: 35px;
            border-radius: 10px;
        }
    </style>
</head>
<body class="hold-transition register-page">
    <div class="register-box">
        <div class="register-logo">
            <img src="assets/img/logohk5_TW.png" alt="Logo" height="60">
            <h4>สมัครสมาชิก</h4>
            <h5>ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ โรงพยาบาลห้วยเกิ้ง</h5>
        </div>

        <div class="card">
            <div class="register-card-body">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $success; ?>
                        <div class="mt-2">
                            <a href="login.php" class="btn btn-primary">เข้าสู่ระบบ</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (!isset($success)): ?>
                    <form method="post" action="" id="registerForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="username" name="username" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-user"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fullname">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-user-circle"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">รหัสผ่าน <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">อีเมล <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="email" class="form-control" id="email" name="email" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-envelope"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telephone">เบอร์โทรศัพท์</label>
                                    <div class="input-group">
                                        <input type="tel" class="form-control" id="telephone" name="telephone">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-phone"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dept_id">แผนก <span class="text-danger">*</span></label>
                                    <select class="form-control select2bs4" id="dept_id" name="dept_id" required>
                                        <option value="">เลือกแผนก</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo htmlspecialchars($dept['dept_id']); ?>">
                                                <?php echo htmlspecialchars($dept['dept_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position">ตำแหน่ง</label>
                                    <input type="text" class="form-control" id="position" name="position">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block">สมัครสมาชิก</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>

                <div class="mt-3 text-center">
                    <p>มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
    <script>
    $(document).ready(function() {
    // Initialize Select2
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    });

    // Form validation
    $('#registerForm').on('submit', function(e) {
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
        var errors = [];

        if (password !== confirmPassword) {
            errors.push('รหัสผ่านไม่ตรงกัน');
        }

        if (password.length < 6) {
            errors.push('รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร');
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
            return false;
        }
    });
});
    </script>
</body>
=======
<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Department.php';
require_once 'classes/Logger.php';

// ถ้าล็อกอินแล้วให้ไปหน้า dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . $_SESSION['level'] . '/dashboard.php');
    exit();
}

        try {
            $db = new Database();
            $conn = $db->getConnection();
            $departmentManager = new Department();
            $logger = new Logger('auth');

            // ดึงข้อมูลแผนก
            $departments = $departmentManager->getAllDepartments();
        } catch (Exception $e) {
            error_log("Error in register.php: " . $e->getMessage());
            $error = "เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง";
            $departments = array();
        }
// จัดการการสมัครสมาชิก
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // ตรวจสอบข้อมูลที่จำเป็น
        $required_fields = array('username', 'password', 'confirm_password', 'fullname', 'email', 'dept_id');
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("กรุณากรอกข้อมูลให้ครบถ้วน");
            }
        }

        // ตรวจสอบรหัสผ่าน
        if ($_POST['password'] !== $_POST['confirm_password']) {
            throw new Exception("รหัสผ่านไม่ตรงกัน");
        }

        if (strlen($_POST['password']) < 6) {
            throw new Exception("รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร");
        }

        // ตรวจสอบรูปแบบอีเมล
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("รูปแบบอีเมลไม่ถูกต้อง");
        }

        // ตรวจสอบชื่อผู้ใช้และอีเมลซ้ำ
        $stmt = $conn->prepare("SELECT COUNT(*) FROM psd_users WHERE user_name = ? OR email = ?");
        $stmt->execute([$_POST['username'], $_POST['email']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("ชื่อผู้ใช้หรืออีเมลนี้มีในระบบแล้ว");
        }

        // เข้ารหัสรหัสผ่าน
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // เพิ่มข้อมูลผู้ใช้
        $stmt = $conn->prepare("
            INSERT INTO psd_users (
                user_name, pass_word, fullname, email, telephone, 
                dept_id, position, level, created_at
            ) VALUES (
                :username, :password, :fullname, :email, :telephone,
                :dept_id, :position, 'user', NOW()
            )
        ");

            // เพิ่มฟังก์ชันทำความสะอาดข้อมูล
            function cleanInput($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }

            // ทำความสะอาดข้อมูลก่อนบันทึก
            $stmt->execute(array(
                ':username' => $_POST['username'],
                ':password' => $hashed_password,
                ':fullname' => $_POST['fullname'],
                ':email' => $_POST['email'],
                ':telephone' => isset($_POST['telephone']) ? $_POST['telephone'] : null,
                ':dept_id' => $_POST['dept_id'],
                ':position' => isset($_POST['position']) ? $_POST['position'] : null
            ));

            $userId = $conn->lastInsertId();
        
        // บันทึก log
        $logger->write(
            'REGISTER',
            "New user registration: {$_POST['username']}",
            $userId,
            'success'
        );

        $success = "สมัครสมาชิกเรียบร้อยแล้ว กรุณาเข้าสู่ระบบ";

    } catch (Exception $e) {
        $error = $e->getMessage();
        $logger->write(
            'REGISTER',
            "Registration failed: {$error}",
            null,
            'error'
        );
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ โรงพยาบาลห้วยเกิ้ง</title>
    <!-- Google Font: Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <!-- Custom styles -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        .register-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0072ff 0%, #00c6ff 100%);
        }
        .register-box {
            width: 600px;
            margin: 0;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        .register-logo {
            margin-bottom: 25px;
        }
        .register-card-body {
            padding: 35px;
            border-radius: 10px;
        }
    </style>
</head>
<body class="hold-transition register-page">
    <div class="register-box">
        <div class="register-logo">
            <img src="assets/img/logohk5_TW.png" alt="Logo" height="60">
            <h4>สมัครสมาชิก</h4>
            <h5>ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ โรงพยาบาลห้วยเกิ้ง</h5>
        </div>

        <div class="card">
            <div class="register-card-body">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $success; ?>
                        <div class="mt-2">
                            <a href="login.php" class="btn btn-primary">เข้าสู่ระบบ</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (!isset($success)): ?>
                    <form method="post" action="" id="registerForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="username" name="username" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-user"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fullname">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-user-circle"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">รหัสผ่าน <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">อีเมล <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="email" class="form-control" id="email" name="email" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-envelope"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telephone">เบอร์โทรศัพท์</label>
                                    <div class="input-group">
                                        <input type="tel" class="form-control" id="telephone" name="telephone">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-phone"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dept_id">แผนก <span class="text-danger">*</span></label>
                                    <select class="form-control select2bs4" id="dept_id" name="dept_id" required>
                                        <option value="">เลือกแผนก</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo htmlspecialchars($dept['dept_id']); ?>">
                                                <?php echo htmlspecialchars($dept['dept_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position">ตำแหน่ง</label>
                                    <input type="text" class="form-control" id="position" name="position">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block">สมัครสมาชิก</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>

                <div class="mt-3 text-center">
                    <p>มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
    <script>
    $(document).ready(function() {
    // Initialize Select2
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    });

    // Form validation
    $('#registerForm').on('submit', function(e) {
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
        var errors = [];

        if (password !== confirmPassword) {
            errors.push('รหัสผ่านไม่ตรงกัน');
        }

        if (password.length < 6) {
            errors.push('รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร');
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
            return false;
        }
    });
});
    </script>
</body>
>>>>>>> 19b2a46099fb6a0a5d18007dc5257919a6c8c9d4
</html>