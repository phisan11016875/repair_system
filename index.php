<?php
//session_start();

// ถ้ามีการล็อกอินอยู่แล้ว ให้ redirect ไปหน้า dashboard
/*if (isset($_SESSION['user_id'])) {
    $redirect_page = $_SESSION['level'] . 'dashboard.php';
    header("Location: $redirect_page");
    exit();
}*/
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ โรงพยาบาลห้วยเกิ้ง</title>
    <!-- Google Font: Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <!-- Custom styles -->
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f8f9fa;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #0072ff 0%, #00c6ff 100%);
            color: white;
            padding: 100px 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .feature-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }
        
        .feature-box:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: #0072ff;
            margin-bottom: 20px;
        }
        
        .btn-custom {
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .btn-login {
            background-color: white;
            color: #0072ff;
        }
        
        .btn-login:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }
        
        .btn-register {
            background-color: transparent;
            border: 2px solid white;
            color: white;
            margin-left: 15px;
        }
        
        .btn-register:hover {
            background-color: white;
            color: #0072ff;
        }
        
        .navbar {
            background-color: transparent !important;
            padding: 20px 0;
        }
        
        .navbar.scrolled {
            background-color: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="assets/img/logohk5_TW.png" height="150" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!--<ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="login.php">เข้าสู่ระบบ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="register.php">สมัครสมาชิก</a>
                    </li>
                </ul>-->
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ </h1>
                    <h1 class="hero-title">โรงพยาบาลห้วยเกิ้ง</h1>
                    <p class="hero-subtitle">
                        ระบบจัดการและแจ้งซ่อมพัสดุ/ครุภัณฑ์ แบบครบวงจร สำหรับโรงพยาบาล
                        ช่วยให้การบริหารจัดการพัสดุ/ครุภัณฑ์ เป็นไปอย่างมีประสิทธิภาพ
                    </p>
                    <div class="hero-buttons">
                        <a href="login.php" class="btn btn-custom btn-login">เข้าสู่ระบบ</a>
                        <a href="register.php" class="btn btn-custom btn-register">สมัครสมาชิก</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="assets/img/hero-image.png" class="img-fluid" alt="">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <i class="fas fa-tools feature-icon"></i>
                        <h3>แจ้งซ่อมออนไลน์</h3>
                        <p>แจ้งซ่อมพัสดุได้ง่ายๆ ผ่านระบบออนไลน์ ติดตามสถานะได้ตลอดเวลา</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <i class="fas fa-chart-line feature-icon"></i>
                        <h3>ติดตามสถานะ</h3>
                        <p>ติดตามสถานะการซ่อมแบบเรียลไทม์ พร้อมการแจ้งเตือนความคืบหน้า</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <i class="fas fa-history feature-icon"></i>
                        <h3>ประวัติการซ่อม</h3>
                        <p>เก็บประวัติการซ่อมทั้งหมด เพื่อการวางแผนและบริหารจัดการที่ดียิ่งขึ้น</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-light py-4">
        <div class="container">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> ระบบแจ้งซ่อมพัสดุ โรงพยาบาล. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // เปลี่ยนสี Navbar เมื่อเลื่อนหน้า
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('.navbar').addClass('scrolled');
                $('.nav-link').removeClass('text-white');
            } else {
                $('.navbar').removeClass('scrolled');
                $('.nav-link').addClass('text-white');
            }
        });
    </script>
</body>
</html>