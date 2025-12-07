<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Booking.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(BASE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();
$bookingModel = new Booking($db);

$today = date('Y-m-d');
$this_month = date('Y-m');
$this_year = date('Y');

$today_stats = $bookingModel->getStats($today, $today);
$month_stats = $bookingModel->getStats($this_month.'-01', date('Y-m-t'));
$year_stats = $bookingModel->getStats($this_year.'-01-01', $this_year.'-12-31');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo thống kê - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="d-flex">
        <?php include 'includes/sidebar.php'; ?>
        <div class="flex-grow-1">
            <?php include 'includes/navbar.php'; ?>
            <div class="container-fluid p-4">
                <h2 class="fw-bold mb-4">Báo cáo thống kê</h2>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-3">Hôm nay</h6>
                                <h3 class="fw-bold text-primary"><?php echo formatCurrency($today_stats['total_revenue'] ?? 0); ?></h3>
                                <p class="mb-0 small text-muted"><?php echo $today_stats['total_bookings'] ?? 0; ?> lịch hẹn</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-3">Tháng này</h6>
                                <h3 class="fw-bold text-success"><?php echo formatCurrency($month_stats['total_revenue'] ?? 0); ?></h3>
                                <p class="mb-0 small text-muted"><?php echo $month_stats['total_bookings'] ?? 0; ?> lịch hẹn</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-3">Năm nay</h6>
                                <h3 class="fw-bold text-info"><?php echo formatCurrency($year_stats['total_revenue'] ?? 0); ?></h3>
                                <p class="mb-0 small text-muted"><?php echo $year_stats['total_bookings'] ?? 0; ?> lịch hẹn</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Thống kê theo trạng thái</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Hoàn thành</span>
                                        <strong><?php echo $month_stats['completed'] ?? 0; ?></strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: <?php echo ($month_stats['total_bookings'] > 0) ? ($month_stats['completed'] / $month_stats['total_bookings'] * 100) : 0; ?>%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Đã xác nhận</span>
                                        <strong><?php echo $month_stats['confirmed'] ?? 0; ?></strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" style="width: <?php echo ($month_stats['total_bookings'] > 0) ? ($month_stats['confirmed'] / $month_stats['total_bookings'] * 100) : 0; ?>%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Chờ xác nhận</span>
                                        <strong><?php echo $month_stats['pending'] ?? 0; ?></strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: <?php echo ($month_stats['total_bookings'] > 0) ? ($month_stats['pending'] / $month_stats['total_bookings'] * 100) : 0; ?>%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Đã hủy</span>
                                        <strong><?php echo $month_stats['cancelled'] ?? 0; ?></strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" style="width: <?php echo ($month_stats['total_bookings'] > 0) ? ($month_stats['cancelled'] / $month_stats['total_bookings'] * 100) : 0; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
