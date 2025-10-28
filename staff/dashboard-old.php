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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                <i class="fas fa-cut"></i> <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/pages/profile.php">
                            <i class="fas fa-user"></i> Hồ sơ
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
                    Chào mừng bạn đến với trang quản lý nhân viên.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

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
                                            <th>Dịch vụ</th>
                                            <th>Thời gian</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($today_bookings as $booking): ?>
                                        <tr>
                                            <td><strong><?php echo date('H:i', strtotime($booking['booking_time'])); ?></strong></td>
                                            <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                            <td><?php echo $booking['duration']; ?> phút</td>
                                            <td>
                                                <?php
                                                $badge_class = [
                                                    'pending' => 'bg-warning',
                                                    'confirmed' => 'bg-info',
                                                    'completed' => 'bg-success',
                                                    'cancelled' => 'bg-danger'
                                                ];
                                                $status_text = [
                                                    'pending' => 'Chờ xác nhận',
                                                    'confirmed' => 'Đã xác nhận',
                                                    'completed' => 'Hoàn thành',
                                                    'cancelled' => 'Đã hủy'
                                                ];
                                                ?>
                                                <span class="badge <?php echo $badge_class[$booking['status']]; ?>">
                                                    <?php echo $status_text[$booking['status']]; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="booking-detail.php?id=<?php echo $booking['booking_id']; ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Xem
                                                </a>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($upcoming_bookings as $booking): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></td>
                                            <td><?php echo date('H:i', strtotime($booking['booking_time'])); ?></td>
                                            <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                            <td>
                                                <?php
                                                $badge_class = [
                                                    'pending' => 'bg-warning',
                                                    'confirmed' => 'bg-info',
                                                    'completed' => 'bg-success',
                                                    'cancelled' => 'bg-danger'
                                                ];
                                                $status_text = [
                                                    'pending' => 'Chờ xác nhận',
                                                    'confirmed' => 'Đã xác nhận',
                                                    'completed' => 'Hoàn thành',
                                                    'cancelled' => 'Đã hủy'
                                                ];
                                                ?>
                                                <span class="badge <?php echo $badge_class[$booking['status']]; ?>">
                                                    <?php echo $status_text[$booking['status']]; ?>
                                                </span>
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
</body>
</html>
