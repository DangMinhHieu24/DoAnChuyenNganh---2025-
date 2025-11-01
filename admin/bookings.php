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

$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$filters = [];
if ($status_filter) $filters['status'] = $status_filter;

$bookings = $bookingModel->getAllBookings($filters, $limit, $offset);
$total_bookings = $bookingModel->countBookings($filters);
$total_pages = ceil($total_bookings / $limit);

// Xử lý cập nhật status
if (isset($_POST['update_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $new_status = sanitize($_POST['status']);
    if ($bookingModel->updateStatus($booking_id, $new_status)) {
        setFlashMessage('success', 'Cập nhật trạng thái thành công');
    } else {
        setFlashMessage('danger', 'Có lỗi xảy ra');
    }
    redirect($_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý lịch hẹn - Admin</title>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold mb-0">Quản lý lịch hẹn</h2>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="btn-group" role="group">
                                <a href="?status=" class="btn btn-outline-primary <?php echo !$status_filter ? 'active' : ''; ?>">Tất cả (<?php echo $bookingModel->countBookings(); ?>)</a>
                                <a href="?status=pending" class="btn btn-outline-warning <?php echo $status_filter == 'pending' ? 'active' : ''; ?>">Chờ xác nhận</a>
                                <a href="?status=confirmed" class="btn btn-outline-info <?php echo $status_filter == 'confirmed' ? 'active' : ''; ?>">Đã xác nhận</a>
                                <a href="?status=completed" class="btn btn-outline-success <?php echo $status_filter == 'completed' ? 'active' : ''; ?>">Hoàn thành</a>
                                <a href="?status=cancelled" class="btn btn-outline-danger <?php echo $status_filter == 'cancelled' ? 'active' : ''; ?>">Đã hủy</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Khách hàng</th>
                                        <th>Dịch vụ</th>
                                        <th>Nhân viên</th>
                                        <th>Ngày & Giờ</th>
                                        <th>Giá</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td>#<?php echo $booking['booking_id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($booking['customer_phone']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['staff_name']); ?></td>
                                        <td>
                                            <?php echo formatDate($booking['booking_date']); ?><br>
                                            <small><?php echo formatTime($booking['booking_time']); ?></small>
                                        </td>
                                        <td><?php echo formatCurrency($booking['total_price']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo getBookingStatusBadge($booking['status']); ?>">
                                                <?php echo getBookingStatusText($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modal<?php echo $booking['booking_id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Modals -->
                        <?php foreach ($bookings as $booking): ?>
                        <div class="modal fade" id="modal<?php echo $booking['booking_id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Chi tiết lịch hẹn #<?php echo $booking['booking_id']; ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <h6 class="text-muted mb-3">Thông tin khách hàng</h6>
                                                <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($booking['customer_name']); ?></p>
                                                <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($booking['customer_phone']); ?></p>
                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['customer_email'] ?? 'N/A'); ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-muted mb-3">Thông tin dịch vụ</h6>
                                                <p><strong>Dịch vụ:</strong> <?php echo htmlspecialchars($booking['service_name']); ?></p>
                                                <p><strong>Nhân viên:</strong> <?php echo htmlspecialchars($booking['staff_name']); ?></p>
                                                <p><strong>Thời gian:</strong> <?php echo $booking['duration']; ?> phút</p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p><strong>Ngày hẹn:</strong> <?php echo formatDate($booking['booking_date']); ?></p>
                                                <p><strong>Giờ hẹn:</strong> <?php echo formatTime($booking['booking_time']); ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Tổng giá:</strong> <span class="text-primary fw-bold"><?php echo formatCurrency($booking['total_price']); ?></span></p>
                                                <p><strong>Trạng thái:</strong> 
                                                    <span class="badge bg-<?php echo getBookingStatusBadge($booking['status']); ?>">
                                                        <?php echo getBookingStatusText($booking['status']); ?>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <?php if ($booking['notes']): ?>
                                        <div class="alert alert-info">
                                            <strong>Ghi chú:</strong> <?php echo nl2br(htmlspecialchars($booking['notes'])); ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <hr>
                                        
                                        <form method="POST" action="">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Cập nhật trạng thái:</label>
                                                <select class="form-select" name="status" required>
                                                    <option value="pending" <?php echo $booking['status'] == 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                                    <option value="confirmed" <?php echo $booking['status'] == 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                                    <option value="completed" <?php echo $booking['status'] == 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                                                    <option value="cancelled" <?php echo $booking['status'] == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                                    <option value="no_show" <?php echo $booking['status'] == 'no_show' ? 'selected' : ''; ?>>Không đến</option>
                                                </select>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" name="update_status" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i>Cập nhật
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php if ($total_pages > 1): ?>
                        <nav>
                            <ul class="pagination">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
