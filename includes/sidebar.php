<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
        <img src="../assets/img/logohk5_TW.png" height="58" alt="Logo">
        
    </a>
    <a href="index.php" class="brand-link">
        <span class="brand-text font-weight-light">ระบบแจ้งซ่อมพัสดุ/ครุภัณฑ์ </span>
    </a>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel 
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="../assets/img/user-default.png" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo $_SESSION['fullname']; ?></a>
            </div>
        </div>-->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <!-- เมนูสำหรับ Admin -->
                <?php if($_SESSION['level'] == 'admin'): ?>
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>แดชบอร์ด</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="manage_users.php" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>จัดการผู้ใช้งาน</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="manage_fsn.php" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>จัดการระบบพัสดุ/ครุภัณฑ์ (FSN)</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="manage_assets.php" class="nav-link">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>จัดการพัสดุ/ครุภัณฑ์</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="move_assets.php" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>การโยกย้ายพัสดุ</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="manage_repairs.php" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>รายการแจ้งซ่อม</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="repair_history.php" class="nav-link">
                            <i class="nav-icon fas fa-history"></i>
                            <p>ประวัติการแจ้งซ่อม</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../logout.php" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>ออกจากระบบ</p>
                        </a>
                    </li>
                <!-- เมนูสำหรับช่างซ่อม -->
                <?php elseif($_SESSION['level'] == 'technician'): ?>
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>แดชบอร์ด</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="repair_jobs.php" class="nav-link">
                            <i class="nav-icon fas fa-wrench"></i>
                            <p>จัดการงานซ่อม</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="repair_history.php" class="nav-link">
                            <i class="nav-icon fas fa-history"></i>
                            <p>ประวัติการแจ้งซ่อม</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="move_history.php" class="nav-link">
                            <i class="nav-icon fas fa-history"></i>
                            <p>ประวัติการโยกย้าย</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../logout.php" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>ออกจากระบบ</p>
                        </a>
                    </li>
                <!-- เมนูสำหรับผู้ใช้ทั่วไป -->
                <?php else: ?>
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-home"></i>
                            <p>หน้าหลัก</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="report_repair.php" class="nav-link">
                            <i class="nav-icon fas fa-file-signature"></i>
                            <p>แจ้งซ่อมพัสดุ/ครุภัณฑ์</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="repair_history.php" class="nav-link">
                            <i class="nav-icon fas fa-history"></i>
                            <p>ประวัติการแจ้งซ่อม</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="move_history.php" class="nav-link">
                            <i class="nav-icon fas fa-history"></i>
                            <p>ประวัติการโยกย้าย</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../logout.php" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>ออกจากระบบ</p>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>