<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>">
            <i class="fas fa-cut text-primary"></i> <?php echo SITE_NAME; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/pages/services.php">Dịch vụ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/pages/booking.php">Đặt lịch</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/pages/ai-hair-consultant.php">
                        <i class="fas fa-magic"></i> AI Tư Vấn
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/pages/contact.php">Liên hệ</a>
                </li>
                
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="<?php echo getAvatarUrl($_SESSION['avatar'] ?? null); ?>" alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                            <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/dashboard.php">
                                    <i class="fas fa-tachometer-alt me-2"></i>Quản trị
                                </a></li>
                            <?php elseif (isStaff()): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/staff/dashboard.php">
                                    <i class="fas fa-tachometer-alt me-2"></i>Bảng điều khiển
                                </a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/profile.php">
                                <i class="fas fa-user me-2"></i>Tài khoản
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/my-bookings.php">
                                <i class="fas fa-calendar-alt me-2"></i>Lịch hẹn của tôi
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/auth/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/auth/login.php">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-lg-2" href="<?php echo BASE_URL; ?>/auth/register.php">Đăng ký</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php 
// Hiển thị flash message nếu có
$flash = getFlashMessage();
if ($flash): 
?>
<div class="container mt-3">
    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $flash['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>
