<<<<<<< HEAD
<?php
session_start();
//require_once 'config/configdb.php';
require_once 'config/database.php';
require_once 'classes/Logger.php';
$logger = new Logger('auth');


// จัดการการล็อกอิน
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    $username = trim($_POST['username']);
    $password = $_POST['password'];

// ตรวจสอบว่ามีการล็อกอินอยู่แล้วหรือไม่
if (isset($_SESSION['user_id'])) {
    $redirect_page = $_SESSION['level'] . '/dashboard.php';
    header("Location: $redirect_page");
    exit();
}


    try {
        $stmt = $conn->prepare("SELECT * FROM psd_users WHERE user_name = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

       if ($user && password_verify($password, $user['pass_word'])) {
    // สร้าง session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['user_name'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['level'] = $user['level'];
    $_SESSION['dept_id'] = $user['dept_id'];

    // บันทึกเวลาล็อกอินล่าสุด
    $stmt = $conn->prepare("UPDATE psd_users SET last_login = NOW() WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user['user_id']);
    $stmt->execute();

    // เก็บ log การล็อกอิน
    $logger->loginLog($user['user_id'], $user['user_name']);

    // เปลี่ยนเส้นทางตามระดับผู้ใช้
    if ($user['level']=='admin') {
        $redirect_page = 'admin/dashboard.php';
        header("Location: $redirect_page");
    }elseif ($user['level']=='technician') {
        $redirect_page = 'technician/dashboard.php';
        header("Location: $redirect_page");
    }else{
        $redirect_page = 'user/dashboard.php';
        header("Location: $redirect_page");
    }

    exit();
            } else {
                // เก็บ log การล็อกอินไม่สำเร็จ
                $logger->loginLog(null, $username, 'failed', 'Invalid username or password');
                $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
            }

                } catch(PDOException $e) {
                    $error = "เกิดข้อผิดพลาด: " . $e->getMessage();
                }
            }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ โรงพยาบาลห้วยเกิ้ง</title>
    <!-- Google Font: Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Custom styles -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0072ff 0%, #00c6ff 100%);
        }
        .login-box {
            width: 360px;
            margin: 0;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        .login-logo {
            margin-bottom: 25px;
        }
        .login-logo img {
            max-width: 150px;
            height: auto;
        }
        .login-card-body {
            padding: 35px;
            border-radius: 10px;
        }
        .input-group-text {
            background: transparent;
            border-left: none;
        }
        .form-control {
            border-right: none;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }
        .btn-primary {
            background: #0072ff;
            border: none;
            padding: 10px;
        }
        .btn-primary:hover {
            background: #005ae0;
        }
        .forgot-password {
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="assets/img/logohk5_TW.png" height="120" alt="Logo">
            <h4>ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์</h4>
        </div>

        <div class="card">
            <div class="login-card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="ชื่อผู้ใช้" name="username" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="รหัสผ่าน" name="password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
                        </div>
                    </div>
                </form>

                <div class="d-flex justify-content-between forgot-password">
                    <a href="register.php">สมัครสมาชิก</a>
                    <a href="forgot_password.php">ลืมรหัสผ่าน?</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>-->
</body>
=======
<?php
session_start();
//require_once 'config/configdb.php';
require_once 'config/database.php';
require_once 'classes/Logger.php';
$logger = new Logger('auth');


// จัดการการล็อกอิน
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    $username = trim($_POST['username']);
    $password = $_POST['password'];

// ตรวจสอบว่ามีการล็อกอินอยู่แล้วหรือไม่
if (isset($_SESSION['user_id'])) {
    $redirect_page = $_SESSION['level'] . '/dashboard.php';
    header("Location: $redirect_page");
    exit();
}


    try {
        $stmt = $conn->prepare("SELECT * FROM psd_users WHERE user_name = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

       if ($user && password_verify($password, $user['pass_word'])) {
    // สร้าง session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['user_name'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['level'] = $user['level'];
    $_SESSION['dept_id'] = $user['dept_id'];

    // บันทึกเวลาล็อกอินล่าสุด
    $stmt = $conn->prepare("UPDATE psd_users SET last_login = NOW() WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user['user_id']);
    $stmt->execute();

    // เก็บ log การล็อกอิน
    $logger->loginLog($user['user_id'], $user['user_name']);

    // เปลี่ยนเส้นทางตามระดับผู้ใช้
    if ($user['level']=='admin') {
        $redirect_page = 'admin/dashboard.php';
        header("Location: $redirect_page");
    }elseif ($user['level']=='technician') {
        $redirect_page = 'technician/dashboard.php';
        header("Location: $redirect_page");
    }else{
        $redirect_page = 'user/dashboard.php';
        header("Location: $redirect_page");
    }

    exit();
            } else {
                // เก็บ log การล็อกอินไม่สำเร็จ
                $logger->loginLog(null, $username, 'failed', 'Invalid username or password');
                $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
            }

                } catch(PDOException $e) {
                    $error = "เกิดข้อผิดพลาด: " . $e->getMessage();
                }
            }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ โรงพยาบาลห้วยเกิ้ง</title>
    <!-- Google Font: Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Custom styles -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0072ff 0%, #00c6ff 100%);
        }
        .login-box {
            width: 360px;
            margin: 0;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        .login-logo {
            margin-bottom: 25px;
        }
        .login-logo img {
            max-width: 150px;
            height: auto;
        }
        .login-card-body {
            padding: 35px;
            border-radius: 10px;
        }
        .input-group-text {
            background: transparent;
            border-left: none;
        }
        .form-control {
            border-right: none;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }
        .btn-primary {
            background: #0072ff;
            border: none;
            padding: 10px;
        }
        .btn-primary:hover {
            background: #005ae0;
        }
        .forgot-password {
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="assets/img/logohk5_TW.png" height="120" alt="Logo">
            <h4>ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์</h4>
        </div>

        <div class="card">
            <div class="login-card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="ชื่อผู้ใช้" name="username" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="รหัสผ่าน" name="password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
                        </div>
                    </div>
                </form>

                <div class="d-flex justify-content-between forgot-password">
                    <a href="register.php">สมัครสมาชิก</a>
                    <a href="forgot_password.php">ลืมรหัสผ่าน?</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>-->
</body>
>>>>>>> 19b2a46099fb6a0a5d18007dc5257919a6c8c9d4
</html>