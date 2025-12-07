<?php
/**
 * Staff Dashboard - Trang quản lý của nhân viên
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Booking.php';
require_once '../models/Staff.php';

// Kiểm tra đăng nhập và quyền staff
if (!isLoggedIn()) {
    redirect(BASE_URL . '/auth/login.php');
}

if (getCurrentUserRole() !== 'staff') {
    redirect(BASE_URL . '/index.php');
}

$database = new Database();
$db = $database->getConnection();
$bookingModel = new Booking($db);
$staffModel = new Staff($db);

// Lấy thông tin staff
$user_id = $_SESSION['user_id'];
$staff_info = $staffModel->getStaffByUserId($user_id);

if (!$staff_info) {
    echo "<div class='alert alert-danger'>Không tìm thấy thông tin nhân viên!</div>";
    exit;
}

$staff_id = $staff_info['staff_id'];

// Lấy thống kê tổng quan
$overall_stats = $bookingModel->getStaffStatistics($staff_id);
$monthly_stats = $bookingModel->getStaffMonthlyStatistics($staff_id);

// Lấy lịch hẹn của staff hôm nay
$today = date('Y-m-d');
$today_bookings = $bookingModel->getBookingsByStaffAndDate($staff_id, $today);

// Lấy lịch hẹn sắp tới
$upcoming_bookings = $bookingModel->getUpcomingBookingsByStaff($staff_id, 10);

// Thống kê
$total_today = count($today_bookings);
$completed_today = count(array_filter($today_bookings, function($b) { return $b['status'] === 'completed'; }));
$pending_today = count(array_filter($today_bookings, function($b) { return $b['status'] === 'confirmed'; }));

$page_title = 'Dashboard Nhân viên';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm" style="z-index: 1030;">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-primary" href="<?php echo BASE_URL; ?>">
                <i class="fas fa-cut"></i> <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <a class="nav-link active fw-semibold text-dark" href="dashboard.php">
                            <i class="fas fa-home text-primary"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center text-dark" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo getAvatarUrl($_SESSION['avatar'] ?? null); ?>" alt="Avatar" width="36" height="36" class="rounded-circle me-2" style="border: 3px solid #0d6efd;">
                            <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="userDropdown" style="min-width: 220px; z-index: 9999;">
                            <li class="px-3 py-2 border-bottom">
                                <small class="text-muted">Nhân viên</small>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="<?php echo BASE_URL; ?>/pages/profile.php">
                                    <i class="fas fa-user-circle text-primary me-2"></i> Hồ sơ cá nhân
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="<?php echo BASE_URL; ?>/pages/change-password.php">
                                    <i class="fas fa-key text-warning me-2"></i> Đổi mật khẩu
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

    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <main class="col-12 px-md-4 py-4">
                <!-- Welcome -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-tachometer-alt text-primary"></i> Dashboard Nhân viên
                    </h1>
                    <div class="text-muted">
                        <i class="far fa-calendar"></i> <?php echo date('d/m/Y'); ?>
                    </div>
                </div>

                <!-- Alert Welcome -->
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Xin chào, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</strong> 
                    Chúc bạn có một ngày làm việc hiệu quả!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <!-- Thống kê tổng quan -->
                <h5 class="mb-3"><i class="fas fa-chart-line text-success"></i> Thống kê tổng quan</h5>
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="fas fa-star text-warning fa-2x mb-2"></i>
                                <h6 class="text-muted mb-1">Đánh giá</h6>
                                <h3 class="mb-0 text-info"><?php echo number_format($staff_info['rating'] ?? 0, 1); ?> <i class="fas fa-star text-warning"></i></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <i class="fas fa-list-check text-primary fa-2x mb-2"></i>
                                <h6 class="text-muted mb-1">Tổng lịch hẹn</h6>
                                <h3 class="mb-0 text-primary"><?php echo number_format($overall_stats['total_bookings'] ?? 0); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="fas fa-dollar-sign text-success fa-2x mb-2"></i>
                                <h6 class="text-muted mb-1">Doanh thu tháng này</h6>
                                <h3 class="mb-0 text-success"><?php echo formatCurrency($monthly_stats['monthly_revenue']); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-check text-warning fa-2x mb-2"></i>
                                <h6 class="text-muted mb-1">Hoàn thành tháng này</h6>
                                <h3 class="mb-0 text-warning"><?php echo number_format($monthly_stats['completed_bookings'] ?? 0); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3"><i class="fas fa-calendar-day text-primary"></i> Thống kê hôm nay</h5>
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card border-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Lịch hẹn hôm nay</h6>
                                        <h2 class="mb-0"><?php echo $total_today; ?></h2>
                                    </div>
                                    <div class="text-primary">
                                        <i class="fas fa-calendar-day fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Đã hoàn thành</h6>
                                        <h2 class="mb-0"><?php echo $completed_today; ?></h2>
                                    </div>
                                    <div class="text-success">
                                        <i class="fas fa-check-circle fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card border-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Đang chờ</h6>
                                        <h2 class="mb-0"><?php echo $pending_today; ?></h2>
                                    </div>
                                    <div class="text-warning">
                                        <i class="fas fa-clock fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Bookings -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-check"></i> Lịch hẹn hôm nay
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($today_bookings)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Không có lịch hẹn nào hôm nay.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Giờ</th>
                                            <th>Khách hàng</th>
                                            <th>SĐT</th>
                                            <th>Dịch vụ</th>
                                            <th>TG</th>
                                            <th>Giá</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($today_bookings as $booking): ?>
                                        <tr data-booking-id="<?php echo $booking['booking_id']; ?>">
                                            <td><strong><?php echo date('H:i', strtotime($booking['booking_time'])); ?></strong></td>
                                            <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['customer_phone']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                            <td><?php echo $booking['duration']; ?>'</td>
                                            <td><?php echo formatCurrency($booking['total_price']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo getBookingStatusBadge($booking['status']); ?> status-badge">
                                                    <i class="fas <?php echo getBookingStatusIcon($booking['status']); ?>"></i>
                                                    <?php echo getBookingStatusText($booking['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="booking-detail.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-sm btn-primary" title="Xem"><i class="fas fa-eye"></i></a>
                                                    <?php if ($booking['status'] == 'pending'): ?>
                                                        <button class="btn btn-sm btn-success btn-confirm" data-id="<?php echo $booking['booking_id']; ?>" title="Xác nhận"><i class="fas fa-check"></i></button>
                                                    <?php endif; ?>
                                                    <?php if ($booking['status'] == 'confirmed'): ?>
                                                        <button class="btn btn-sm btn-info btn-complete" data-id="<?php echo $booking['booking_id']; ?>" title="Hoàn thành"><i class="fas fa-check-double"></i></button>
                                                    <?php endif; ?>
                                                    <?php if ($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
                                                        <button class="btn btn-sm btn-danger btn-cancel" data-id="<?php echo $booking['booking_id']; ?>" title="Hủy"><i class="fas fa-times"></i></button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Upcoming Bookings -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt"></i> Lịch hẹn sắp tới
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($upcoming_bookings)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Không có lịch hẹn sắp tới.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ngày</th>
                                            <th>Giờ</th>
                                            <th>Khách hàng</th>
                                            <th>Dịch vụ</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($upcoming_bookings as $booking): ?>
                                        <tr data-booking-id="<?php echo $booking['booking_id']; ?>">
                                            <td><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></td>
                                            <td><?php echo date('H:i', strtotime($booking['booking_time'])); ?></td>
                                            <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo getBookingStatusBadge($booking['status']); ?>">
                                                    <i class="fas <?php echo getBookingStatusIcon($booking['status']); ?>"></i>
                                                    <?php echo getBookingStatusText($booking['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="booking-detail.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-sm btn-primary" title="Xem"><i class="fas fa-eye"></i></a>
                                                    <?php if ($booking['status'] == 'pending'): ?>
                                                        <button class="btn btn-sm btn-success btn-confirm" data-id="<?php echo $booking['booking_id']; ?>" title="Xác nhận"><i class="fas fa-check"></i></button>
                                                    <?php endif; ?>
                                                    <?php if ($booking['status'] == 'confirmed'): ?>
                                                        <button class="btn btn-sm btn-info btn-complete" data-id="<?php echo $booking['booking_id']; ?>" title="Hoàn thành"><i class="fas fa-check-double"></i></button>
                                                    <?php endif; ?>
                                                    <?php if ($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
                                                        <button class="btn btn-sm btn-danger btn-cancel" data-id="<?php echo $booking['booking_id']; ?>" title="Hủy"><i class="fas fa-times"></i></button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';

        // Xác nhận booking
        document.querySelectorAll('.btn-confirm').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Xác nhận lịch hẹn này?')) {
                    updateBookingStatus(this.dataset.id, 'confirmed');
                }
            });
        });

        // Hoàn thành booking
        document.querySelectorAll('.btn-complete').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Đánh dấu hoàn thành?')) {
                    updateBookingStatus(this.dataset.id, 'completed');
                }
            });
        });

        // Hủy booking
        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Hủy lịch hẹn này?')) {
                    updateBookingStatus(this.dataset.id, 'cancelled');
                }
            });
        });

        // Hàm cập nhật trạng thái
        function updateBookingStatus(bookingId, newStatus) {
            fetch(`${BASE_URL}/api/staff/update-booking-status.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({booking_id: bookingId, status: newStatus})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cập nhật thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                alert('Có lỗi xảy ra!');
                console.error(error);
            });
        }

        // Auto refresh mỗi 5 phút
        setTimeout(() => location.reload(), 300000);
    </script>
</body>
</html>
