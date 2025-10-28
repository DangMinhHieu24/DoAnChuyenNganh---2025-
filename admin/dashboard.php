<?php
/**
 * Admin Dashboard
 * Bảng điều khiển quản trị
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Booking.php';
require_once '../models/Service.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || !isAdmin()) {
    redirect(BASE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();

$userModel = new User($db);
$bookingModel = new Booking($db);
$serviceModel = new Service($db);

// Lấy thống kê
$total_customers = $userModel->countUsers('customer', 'active');
$total_staff = $userModel->countUsers('staff', 'active');
$total_services = count($serviceModel->getAllServices(null, 'active'));

// Thống kê booking
$today = date('Y-m-d');
$this_month_start = date('Y-m-01');
$this_month_end = date('Y-m-t');

$today_stats = $bookingModel->getStats($today, $today);
$month_stats = $bookingModel->getStats($this_month_start, $this_month_end);

// Booking gần đây
$recent_bookings = $bookingModel->getAllBookings([], 10, 0);

// Pending bookings
$pending_bookings = $bookingModel->getAllBookings(['status' => 'pending'], 5, 0);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điều khiển - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="d-flex">
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <?php include 'includes/navbar.php'; ?>

            <div class="container-fluid p-4">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Bảng điều khiển</h2>
                        <p class="text-muted mb-0">Chào mừng trở lại, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
                    </div>
                    <div>
                        <span class="text-muted"><i class="far fa-calendar me-2"></i><?php echo formatDate(date('Y-m-d')); ?></span>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Khách hàng</p>
                                    <h3 class="fw-bold mb-0"><?php echo $total_customers; ?></h3>
                                </div>
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Nhân viên</p>
                                    <h3 class="fw-bold mb-0"><?php echo $total_staff; ?></h3>
                                </div>
                                <div class="stat-icon bg-success bg-opacity-10 text-success">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Dịch vụ</p>
                                    <h3 class="fw-bold mb-0"><?php echo $total_services; ?></h3>
                                </div>
                                <div class="stat-icon bg-info bg-opacity-10 text-info">
                                    <i class="fas fa-cut"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Doanh thu tháng</p>
                                    <h3 class="fw-bold mb-0"><?php echo formatCurrency($month_stats['total_revenue'] ?? 0); ?></h3>
                                </div>
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today Stats -->
                <div class="row g-4 mb-4">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i>Thống kê hôm nay</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="p-3">
                                            <h4 class="fw-bold text-primary"><?php echo $today_stats['total_bookings'] ?? 0; ?></h4>
                                            <p class="text-muted mb-0">Tổng lịch hẹn</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3">
                                            <h4 class="fw-bold text-warning"><?php echo $today_stats['pending'] ?? 0; ?></h4>
                                            <p class="text-muted mb-0">Chờ xác nhận</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3">
                                            <h4 class="fw-bold text-info"><?php echo $today_stats['confirmed'] ?? 0; ?></h4>
                                            <p class="text-muted mb-0">Đã xác nhận</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3">
                                            <h4 class="fw-bold text-success"><?php echo $today_stats['completed'] ?? 0; ?></h4>
                                            <p class="text-muted mb-0">Hoàn thành</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Pending Bookings -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-clock me-2"></i>Lịch hẹn chờ xác nhận</h6>
                                <a href="bookings.php?status=pending" class="btn btn-sm btn-primary">Xem tất cả</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($pending_bookings)): ?>
                                    <p class="text-muted text-center py-4">Không có lịch hẹn chờ xác nhận</p>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($pending_bookings as $booking): ?>
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($booking['customer_name']); ?></h6>
                                                    <p class="mb-1 small text-muted">
                                                        <i class="fas fa-cut me-1"></i><?php echo htmlspecialchars($booking['service_name']); ?>
                                                    </p>
                                                    <p class="mb-0 small">
                                                        <i class="far fa-calendar me-1"></i><?php echo formatDate($booking['booking_date']); ?>
                                                        <i class="far fa-clock ms-2 me-1"></i><?php echo formatTime($booking['booking_time']); ?>
                                                    </p>
                                                </div>
                                                <div>
                                                    <a href="bookings.php" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i>Lịch hẹn gần đây</h6>
                                <a href="bookings.php" class="btn btn-sm btn-primary">Xem tất cả</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_bookings)): ?>
                                    <p class="text-muted text-center py-4">Chưa có lịch hẹn nào</p>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach (array_slice($recent_bookings, 0, 5) as $booking): ?>
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($booking['customer_name']); ?></h6>
                                                    <p class="mb-1 small text-muted">
                                                        <?php echo htmlspecialchars($booking['service_name']); ?>
                                                    </p>
                                                    <p class="mb-0 small">
                                                        <?php echo formatDate($booking['booking_date']); ?> - 
                                                        <?php echo formatTime($booking['booking_time']); ?>
                                                    </p>
                                                </div>
                                                <div>
                                                    <span class="badge bg-<?php echo getBookingStatusBadge($booking['status']); ?>">
                                                        <?php echo getBookingStatusText($booking['status']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
