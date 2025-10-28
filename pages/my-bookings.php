<?php
/**
 * My Bookings Page - Lịch hẹn của tôi (REBUILT)
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Booking.php';
require_once '../models/Review.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    redirect(BASE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();
$bookingModel = new Booking($db);
$reviewModel = new Review($db);

$customer_id = getCurrentUserId();
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : null;

// Lấy danh sách bookings
$bookings = $bookingModel->getBookingsByCustomer($customer_id, $status_filter);
$upcoming_bookings = $bookingModel->getUpcomingBookings($customer_id, 3);

// Xử lý hủy booking
if (isset($_POST['cancel_booking'])) {
    $booking_id = (int)$_POST['booking_id'];
    $cancellation_reason = sanitize($_POST['cancellation_reason']);
    
    $booking = $bookingModel->getBookingById($booking_id);
    if ($booking && $booking['customer_id'] == $customer_id) {
        $booking_datetime = $booking['booking_date'] . ' ' . $booking['booking_time'];
        $hours_before = (strtotime($booking_datetime) - time()) / 3600;
        
        if ($hours_before >= BOOKING_CANCEL_HOURS) {
            if ($bookingModel->cancelBooking($booking_id, $cancellation_reason)) {
                setFlashMessage('success', 'Đã hủy lịch hẹn thành công');
            } else {
                setFlashMessage('error', 'Không thể hủy lịch hẹn');
            }
        } else {
            setFlashMessage('error', 'Chỉ có thể hủy lịch trước ' . BOOKING_CANCEL_HOURS . ' giờ');
        }
    }
    redirect($_SERVER['PHP_SELF']);
}

// Xử lý đánh giá
if (isset($_POST['submit_review'])) {
    $booking_id = (int)$_POST['booking_id'];
    $rating = (int)$_POST['rating'];
    $comment = sanitize($_POST['comment']);
    
    $booking = $bookingModel->getBookingById($booking_id);
    if ($booking && $booking['customer_id'] == $customer_id && $booking['status'] === 'completed') {
        if (!$reviewModel->hasReviewed($booking_id)) {
            $reviewModel->booking_id = $booking_id;
            $reviewModel->customer_id = $customer_id;
            $reviewModel->service_id = $booking['service_id'];
            $reviewModel->staff_id = $booking['staff_id'];
            $reviewModel->rating = $rating;
            $reviewModel->comment = $comment;
            
            if ($reviewModel->create()) {
                setFlashMessage('success', 'Đánh giá thành công!');
            } else {
                setFlashMessage('error', 'Có lỗi xảy ra!');
            }
        } else {
            setFlashMessage('error', 'Bạn đã đánh giá lịch hẹn này rồi!');
        }
    }
    redirect($_SERVER['PHP_SELF']);
}

$page_title = 'Lịch hẹn của tôi';
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
    <?php include '../includes/header.php'; ?>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="<?php echo getAvatarUrl($_SESSION['avatar']); ?>" 
                                     alt="Avatar" class="rounded-circle mb-2" width="80" height="80">
                                <h6 class="mb-0"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h6>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="profile.php" class="list-group-item list-group-item-action">
                                    <i class="fas fa-user me-2"></i>Thông tin tài khoản
                                </a>
                                <a href="my-bookings.php" class="list-group-item list-group-item-action active">
                                    <i class="fas fa-calendar-alt me-2"></i>Lịch hẹn của tôi
                                </a>
                                <a href="change-password.php" class="list-group-item list-group-item-action">
                                    <i class="fas fa-key me-2"></i>Đổi mật khẩu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Lịch hẹn của tôi</h5>
                                <a href="booking.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Đặt lịch mới
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filter -->
                            <div class="mb-4">
                                <div class="btn-group" role="group">
                                    <a href="?status=" class="btn btn-outline-primary <?php echo !$status_filter ? 'active' : ''; ?>">
                                        Tất cả
                                    </a>
                                    <a href="?status=pending" class="btn btn-outline-warning <?php echo $status_filter == 'pending' ? 'active' : ''; ?>">
                                        Chờ xác nhận
                                    </a>
                                    <a href="?status=confirmed" class="btn btn-outline-info <?php echo $status_filter == 'confirmed' ? 'active' : ''; ?>">
                                        Đã xác nhận
                                    </a>
                                    <a href="?status=completed" class="btn btn-outline-success <?php echo $status_filter == 'completed' ? 'active' : ''; ?>">
                                        Hoàn thành
                                    </a>
                                    <a href="?status=cancelled" class="btn btn-outline-danger <?php echo $status_filter == 'cancelled' ? 'active' : ''; ?>">
                                        Đã hủy
                                    </a>
                                </div>
                            </div>

                            <!-- Upcoming Bookings Alert -->
                            <?php if (!empty($upcoming_bookings) && !$status_filter): ?>
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-bell me-2"></i>Lịch hẹn sắp tới</h6>
                                <?php foreach ($upcoming_bookings as $upcoming): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong><?php echo htmlspecialchars($upcoming['service_name']); ?></strong><br>
                                        <small><?php echo formatDate($upcoming['booking_date']); ?> - <?php echo formatTime($upcoming['booking_time']); ?></small>
                                    </div>
                                    <span class="badge bg-<?php echo getBookingStatusBadge($upcoming['status']); ?>">
                                        <?php echo getBookingStatusText($upcoming['status']); ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <!-- Bookings List -->
                            <?php if (empty($bookings)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Chưa có lịch hẹn nào</h5>
                                    <a href="booking.php" class="btn btn-primary mt-3">Đặt lịch ngay</a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Mã</th>
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
                                                    <strong><?php echo htmlspecialchars($booking['service_name']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($booking['category_name']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($booking['staff_name']); ?></td>
                                                <td>
                                                    <?php echo formatDate($booking['booking_date']); ?><br>
                                                    <small class="text-muted"><?php echo formatTime($booking['booking_time']); ?></small>
                                                </td>
                                                <td><?php echo formatCurrency($booking['total_price']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo getBookingStatusBadge($booking['status']); ?>">
                                                        <?php echo getBookingStatusText($booking['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                                                data-bs-target="#detailModal<?php echo $booking['booking_id']; ?>"
                                                                title="Xem chi tiết">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <?php if ($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                                                data-bs-target="#cancelModal<?php echo $booking['booking_id']; ?>"
                                                                title="Hủy lịch">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                        <?php if ($booking['status'] == 'completed' && !$reviewModel->hasReviewed($booking['booking_id'])): ?>
                                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                                data-bs-target="#reviewModal<?php echo $booking['booking_id']; ?>"
                                                                title="Đánh giá">
                                                            <i class="fas fa-star"></i>
                                                        </button>
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
                </div>
            </div>
        </div>
    </section>

    <!-- Modals Section -->
    <?php foreach ($bookings as $booking): ?>
        <!-- Detail Modal -->
        <div class="modal" id="detailModal<?php echo $booking['booking_id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chi tiết lịch hẹn #<?php echo $booking['booking_id']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <dl class="row">
                            <dt class="col-sm-4">Dịch vụ:</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($booking['service_name']); ?></dd>
                            
                            <dt class="col-sm-4">Danh mục:</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($booking['category_name']); ?></dd>
                            
                            <dt class="col-sm-4">Nhân viên:</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($booking['staff_name']); ?></dd>
                            
                            <dt class="col-sm-4">Ngày:</dt>
                            <dd class="col-sm-8"><?php echo formatDate($booking['booking_date']); ?></dd>
                            
                            <dt class="col-sm-4">Giờ:</dt>
                            <dd class="col-sm-8"><?php echo formatTime($booking['booking_time']); ?></dd>
                            
                            <dt class="col-sm-4">Thời gian:</dt>
                            <dd class="col-sm-8"><?php echo $booking['duration']; ?> phút</dd>
                            
                            <dt class="col-sm-4">Giá:</dt>
                            <dd class="col-sm-8"><strong class="text-primary"><?php echo formatCurrency($booking['total_price']); ?></strong></dd>
                            
                            <dt class="col-sm-4">Trạng thái:</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-<?php echo getBookingStatusBadge($booking['status']); ?>">
                                    <?php echo getBookingStatusText($booking['status']); ?>
                                </span>
                            </dd>
                            
                            <?php if ($booking['notes']): ?>
                            <dt class="col-sm-4">Ghi chú:</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></dd>
                            <?php endif; ?>
                            
                            <?php if ($booking['cancellation_reason']): ?>
                            <dt class="col-sm-4">Lý do hủy:</dt>
                            <dd class="col-sm-8 text-danger"><?php echo nl2br(htmlspecialchars($booking['cancellation_reason'])); ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Modal -->
        <?php if ($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
        <div class="modal" id="cancelModal<?php echo $booking['booking_id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <h5 class="modal-title">Hủy lịch hẹn</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Bạn có chắc chắn muốn hủy lịch hẹn này?</p>
                            <div class="mb-3">
                                <label class="form-label">Lý do hủy:</label>
                                <textarea class="form-control" name="cancellation_reason" rows="3" required></textarea>
                            </div>
                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" name="cancel_booking" class="btn btn-danger">Xác nhận hủy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Review Modal -->
        <?php if ($booking['status'] == 'completed' && !$reviewModel->hasReviewed($booking['booking_id'])): ?>
        <div class="modal" id="reviewModal<?php echo $booking['booking_id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <h5 class="modal-title">Đánh giá dịch vụ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Dịch vụ: <strong><?php echo htmlspecialchars($booking['service_name']); ?></strong></label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nhân viên: <strong><?php echo htmlspecialchars($booking['staff_name']); ?></strong></label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Đánh giá <span class="text-danger">*</span></label>
                                <div class="star-rating">
                                    <input type="radio" name="rating" value="5" id="star5_<?php echo $booking['booking_id']; ?>" required>
                                    <label for="star5_<?php echo $booking['booking_id']; ?>"><i class="fas fa-star"></i></label>
                                    <input type="radio" name="rating" value="4" id="star4_<?php echo $booking['booking_id']; ?>">
                                    <label for="star4_<?php echo $booking['booking_id']; ?>"><i class="fas fa-star"></i></label>
                                    <input type="radio" name="rating" value="3" id="star3_<?php echo $booking['booking_id']; ?>">
                                    <label for="star3_<?php echo $booking['booking_id']; ?>"><i class="fas fa-star"></i></label>
                                    <input type="radio" name="rating" value="2" id="star2_<?php echo $booking['booking_id']; ?>">
                                    <label for="star2_<?php echo $booking['booking_id']; ?>"><i class="fas fa-star"></i></label>
                                    <input type="radio" name="rating" value="1" id="star1_<?php echo $booking['booking_id']; ?>">
                                    <label for="star1_<?php echo $booking['booking_id']; ?>"><i class="fas fa-star"></i></label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nhận xét</label>
                                <textarea class="form-control" name="comment" rows="4" placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                            </div>
                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" name="submit_review" class="btn btn-warning">
                                <i class="fas fa-star"></i> Gửi đánh giá
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <style>
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        font-size: 2rem;
        gap: 5px;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        cursor: pointer;
        color: #ddd;
        transition: color 0.2s;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #ffc107;
    }
    </style>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
