<?php
/**
 * Staff Booking Detail - Chi tiết lịch hẹn
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Booking.php';

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

// Lấy booking ID
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($booking_id <= 0) {
    setFlashMessage('error', 'Không tìm thấy lịch hẹn');
    redirect(BASE_URL . '/staff/dashboard.php');
}

// Lấy thông tin booking
$booking = $bookingModel->getBookingById($booking_id);

if (!$booking) {
    setFlashMessage('error', 'Không tìm thấy lịch hẹn');
    redirect(BASE_URL . '/staff/dashboard.php');
}

$page_title = 'Chi tiết lịch hẹn #' . $booking_id;
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                <i class="fas fa-cut"></i> <?php echo SITE_NAME; ?>
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-arrow-left"></i> Quay lại Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-calendar-check text-primary"></i> Chi tiết lịch hẹn</h2>
                    <span class="badge <?php 
                        echo $booking['status'] === 'completed' ? 'bg-success' : 
                            ($booking['status'] === 'confirmed' ? 'bg-info' : 
                            ($booking['status'] === 'cancelled' ? 'bg-danger' : 'bg-warning')); 
                    ?> fs-6">
                        <?php 
                        $status_text = [
                            'pending' => 'Chờ xác nhận',
                            'confirmed' => 'Đã xác nhận',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy',
                            'no_show' => 'Không đến'
                        ];
                        echo $status_text[$booking['status']] ?? $booking['status'];
                        ?>
                    </span>
                </div>

                <!-- Booking Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin lịch hẹn</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <p class="mb-2"><strong><i class="fas fa-hashtag me-2"></i>Mã lịch hẹn:</strong></p>
                                <p class="text-muted">#<?php echo $booking['booking_id']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong><i class="far fa-calendar me-2"></i>Ngày:</strong></p>
                                <p class="text-muted"><?php echo formatDate($booking['booking_date']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong><i class="far fa-clock me-2"></i>Giờ:</strong></p>
                                <p class="text-muted"><?php echo formatTime($booking['booking_time']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong><i class="fas fa-hourglass-half me-2"></i>Thời gian dự kiến:</strong></p>
                                <p class="text-muted"><?php echo $booking['duration']; ?> phút</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Thông tin khách hàng</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong><i class="fas fa-user-circle me-2"></i>Họ tên:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($booking['customer_name']); ?></p>
                        
                        <p class="mb-2"><strong><i class="fas fa-phone me-2"></i>Số điện thoại:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($booking['customer_phone']); ?></p>
                        
                        <p class="mb-2"><strong><i class="fas fa-envelope me-2"></i>Email:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($booking['customer_email']); ?></p>
                    </div>
                </div>

                <!-- Service Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-cut me-2"></i>Thông tin dịch vụ</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong><i class="fas fa-scissors me-2"></i>Dịch vụ:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($booking['service_name']); ?></p>
                        
                        <p class="mb-2"><strong><i class="fas fa-money-bill-wave me-2"></i>Giá:</strong></p>
                        <p class="text-muted fs-5 text-primary fw-bold"><?php echo formatCurrency($booking['price']); ?></p>
                        
                        <?php if (!empty($booking['notes'])): ?>
                        <p class="mb-2"><strong><i class="fas fa-comment me-2"></i>Ghi chú:</strong></p>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <a href="dashboard.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
