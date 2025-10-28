<!-- Admin Sidebar -->
<div class="sidebar d-flex flex-column p-3" style="width: 280px;">
    <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="d-flex align-items-center mb-3 text-white text-decoration-none">
        <i class="fas fa-cut fa-2x me-2"></i>
        <span class="fs-4 fw-bold">Admin Panel</span>
    </a>
    <hr class="text-white">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt me-2"></i>Bảng điều khiển
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/bookings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check me-2"></i>Quản lý lịch hẹn
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/services.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>">
                <i class="fas fa-cut me-2"></i>Quản lý dịch vụ
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/categories.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                <i class="fas fa-list me-2"></i>Danh mục dịch vụ
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/staff.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'staff.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-tie me-2"></i>Quản lý nhân viên
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/customers.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>">
                <i class="fas fa-users me-2"></i>Quản lý khách hàng
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/promotions.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'promotions.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags me-2"></i>Quản lý khuyến mãi
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/reports.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar me-2"></i>Báo cáo thống kê
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog me-2"></i>Cài đặt
            </a>
        </li>
    </ul>
    <hr class="text-white">
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
            <img src="<?php echo getAvatarUrl($_SESSION['avatar'] ?? null); ?>" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/profile.php">Tài khoản</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/auth/logout.php">Đăng xuất</a></li>
        </ul>
    </div>
</div>
