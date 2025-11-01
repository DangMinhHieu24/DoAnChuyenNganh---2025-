<!-- Admin Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm" style="z-index: 1030;">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Xem website -->
                <li class="nav-item me-2">
                    <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_URL; ?>" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>Xem website
                    </a>
                </li>
                
                <!-- Notifications -->
                <li class="nav-item dropdown me-3">
                    <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fs-5 text-muted"></i>
                        <?php 
                        // Đếm pending bookings
                        if (!isset($bookingModel)) {
                            require_once dirname(__DIR__, 2) . '/models/Booking.php';
                            $bookingModel = new Booking($db);
                        }
                        $pending_count = count($bookingModel->getAllBookings(['status' => 'pending'], 100, 0));
                        if ($pending_count > 0): 
                        ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                            <?php echo $pending_count > 99 ? '99+' : $pending_count; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="width: 320px; z-index: 9999;">
                        <li class="px-3 py-2 border-bottom">
                            <h6 class="mb-0"><i class="fas fa-bell me-2 text-primary"></i>Thông báo</h6>
                        </li>
                        <?php if ($pending_count > 0): ?>
                        <li>
                            <a class="dropdown-item py-3" href="<?php echo BASE_URL; ?>/admin/bookings.php?status=pending">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-calendar-check text-warning fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-1 fw-semibold">Lịch hẹn chờ xác nhận</p>
                                        <small class="text-muted">Có <?php echo $pending_count; ?> lịch hẹn cần xử lý</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fs-1 mb-2 d-block"></i>
                            <small>Không có thông báo mới</small>
                        </li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider my-0"></li>
                        <li>
                            <a class="dropdown-item text-center text-primary py-2 small fw-semibold" href="<?php echo BASE_URL; ?>/admin/bookings.php">
                                Xem tất cả lịch hẹn
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo getAvatarUrl($_SESSION['avatar'] ?? null); ?>" alt="Avatar" width="36" height="36" class="rounded-circle me-2" style="border: 2px solid #0d6efd;">
                        <div class="d-none d-md-block">
                            <strong class="d-block" style="line-height: 1.2;"><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>
                            <small class="text-muted">Administrator</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 220px; z-index: 9999;">
                        <li class="px-3 py-2 border-bottom">
                            <small class="text-muted">Quản trị viên</small>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="<?php echo BASE_URL; ?>/pages/profile.php">
                                <i class="fas fa-user-circle text-primary me-2"></i> Tài khoản của tôi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="<?php echo BASE_URL; ?>/pages/change-password.php">
                                <i class="fas fa-key text-warning me-2"></i> Đổi mật khẩu
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="<?php echo BASE_URL; ?>/admin/settings.php">
                                <i class="fas fa-cog text-info me-2"></i> Cài đặt hệ thống
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger fw-semibold" href="<?php echo BASE_URL; ?>/auth/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php 
// Hiển thị flash message
$flash = getFlashMessage();
if ($flash): 
?>
<div class="container-fluid mt-3">
    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $flash['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>
