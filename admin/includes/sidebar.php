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
    <div class="p-3">
        <div class="text-center text-white-50 small">
            <p class="mb-1">© <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
            <p class="mb-0">Version 1.0</p>
        </div>
    </div>
</div>
