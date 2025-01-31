<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user"></i> <?php echo $_SESSION['fullname']; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="../profile.php" class="dropdown-item">
                    <i class="fas fa-user-edit mr-2"></i> แก้ไขข้อมูลส่วนตัว
                </a>
                <div class="dropdown-divider"></div>
                <a href="../logout.php" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt mr-2"></i> ออกจากระบบ
                </a>
            </div>
        </li>
    </ul>
</nav>