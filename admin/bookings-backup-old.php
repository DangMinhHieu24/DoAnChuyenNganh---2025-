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
                                            <button class="btn btn-sm btn-info btn-view-booking" 
                                                    data-booking-id="<?php echo $booking['booking_id']; ?>"
                                                    data-customer="<?php echo htmlspecialchars($booking['customer_name']); ?>"
                                                    data-phone="<?php echo htmlspecialchars($booking['customer_phone']); ?>"
                                                    data-email="<?php echo htmlspecialchars($booking['customer_email'] ?? 'N/A'); ?>"
                                                    data-service="<?php echo htmlspecialchars($booking['service_name']); ?>"
                                                    data-staff="<?php echo htmlspecialchars($booking['staff_name']); ?>"
                                                    data-duration="<?php echo $booking['duration']; ?>"
                                                    data-date="<?php echo formatDate($booking['booking_date']); ?>"
                                                    data-time="<?php echo formatTime($booking['booking_time']); ?>"
                                                    data-price="<?php echo formatCurrency($booking['total_price']); ?>"
                                                    data-status="<?php echo $booking['status']; ?>"
                                                    data-notes="<?php echo htmlspecialchars($booking['notes'] ?? ''); ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Single Modal for all bookings -->
                        <div class="modal" id="bookingModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="modalTitle">Chi tiết lịch hẹn</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <h6 class="text-muted mb-3">Thông tin khách hàng</h6>
                                                <p><strong>Họ tên:</strong> <span id="customer-name"></span></p>
                                                <p><strong>Điện thoại:</strong> <span id="customer-phone"></span></p>
                                                <p><strong>Email:</strong> <span id="customer-email"></span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-muted mb-3">Thông tin dịch vụ</h6>
                                                <p><strong>Dịch vụ:</strong> <span id="service-name"></span></p>
                                                <p><strong>Nhân viên:</strong> <span id="staff-name"></span></p>
                                                <p><strong>Thời gian:</strong> <span id="duration"></span> phút</p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p><strong>Ngày hẹn:</strong> <span id="booking-date"></span></p>
                                                <p><strong>Giờ hẹn:</strong> <span id="booking-time"></span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Tổng giá:</strong> <span class="text-primary fw-bold" id="total-price"></span></p>
                                                <p><strong>Trạng thái:</strong> <span id="booking-status-badge"></span></p>
                                            </div>
                                        </div>
                                        <div id="booking-notes-container"></div>
                                        
                                        <hr>
                                        
                                        <form method="POST" action="" id="updateStatusForm">
                                            <input type="hidden" name="booking_id" id="booking-id-input">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Cập nhật trạng thái:</label>
                                                <select class="form-select" name="status" id="status-select" required>
                                                    <option value="pending">Chờ xác nhận</option>
                                                    <option value="confirmed">Đã xác nhận</option>
                                                    <option value="completed">Hoàn thành</option>
                                                    <option value="cancelled">Đã hủy</option>
                                                    <option value="no_show">Không đến</option>
                                                </select>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-secondary" id="btnCloseModal">Đóng</button>
                                                <button type="submit" name="update_status" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i>Cập nhật
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('bookingModal');
        
        // Khởi tạo modal với config chặt chẽ
        const bookingModal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',  // Không đóng khi click outside
            keyboard: false,     // Không đóng khi nhấn ESC
            focus: false         // Không auto-focus
        });
        
        // Ngăn modal tự động mở
        modalElement.addEventListener('show.bs.modal', function(e) {
            // Chỉ cho phép mở modal từ button click, không từ các event khác
            if (!window.allowModalOpen) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            window.allowModalOpen = false; // Reset flag
        });
        
        // Xử lý click button xem chi tiết
        document.querySelectorAll('.btn-view-booking').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Lấy dữ liệu từ data attributes
                const bookingId = this.dataset.bookingId;
                const customer = this.dataset.customer;
                const phone = this.dataset.phone;
                const email = this.dataset.email;
                const service = this.dataset.service;
                const staff = this.dataset.staff;
                const duration = this.dataset.duration;
                const date = this.dataset.date;
                const time = this.dataset.time;
                const price = this.dataset.price;
                const status = this.dataset.status;
                const notes = this.dataset.notes;
                
                // Điền dữ liệu vào modal
                document.getElementById('modalTitle').textContent = 'Chi tiết lịch hẹn #' + bookingId;
                document.getElementById('customer-name').textContent = customer;
                document.getElementById('customer-phone').textContent = phone;
                document.getElementById('customer-email').textContent = email;
                document.getElementById('service-name').textContent = service;
                document.getElementById('staff-name').textContent = staff;
                document.getElementById('duration').textContent = duration;
                document.getElementById('booking-date').textContent = date;
                document.getElementById('booking-time').textContent = time;
                document.getElementById('total-price').textContent = price;
                document.getElementById('booking-id-input').value = bookingId;
                document.getElementById('status-select').value = status;
                
                // Status badge
                const statusBadges = {
                    'pending': '<span class="badge bg-warning">Chờ xác nhận</span>',
                    'confirmed': '<span class="badge bg-info">Đã xác nhận</span>',
                    'completed': '<span class="badge bg-success">Hoàn thành</span>',
                    'cancelled': '<span class="badge bg-danger">Đã hủy</span>',
                    'no_show': '<span class="badge bg-secondary">Không đến</span>'
                };
                document.getElementById('booking-status-badge').innerHTML = statusBadges[status] || status;
                
                // Ghi chú
                const notesContainer = document.getElementById('booking-notes-container');
                if (notes && notes.trim() !== '') {
                    notesContainer.innerHTML = '<div class="alert alert-info"><strong>Ghi chú:</strong> ' + notes + '</div>';
                } else {
                    notesContainer.innerHTML = '';
                }
                
                // Set flag cho phép mở modal
                window.allowModalOpen = true;
                
                // Hiển thị modal
                bookingModal.show();
            });
        });
        
        // Xử lý nút Đóng
        document.getElementById('btnCloseModal').addEventListener('click', function() {
            bookingModal.hide();
        });
        
        // Xử lý nút X
        modalElement.querySelector('.btn-close').addEventListener('click', function() {
            bookingModal.hide();
        });
        
        // Xử lý submit form
        document.getElementById('updateStatusForm').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang xử lý...';
            bookingModal.hide();
        });
        
        // CHẶN MODAL TỰ ĐỘNG MỞ KHI CHUỘT RỜI KHỎI WINDOW
        let isModalOpen = false;
        
        modalElement.addEventListener('shown.bs.modal', function() {
            isModalOpen = true;
        });
        
        modalElement.addEventListener('hidden.bs.modal', function() {
            isModalOpen = false;
            window.allowModalOpen = false;
        });
        
        // Chặn tất cả mouse events có thể trigger modal
        document.addEventListener('mouseleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            // Không làm gì cả
        }, true);
        
        document.addEventListener('mouseout', function(e) {
            // Chỉ xử lý nếu chuột ra khỏi window
            if (e.target === document.documentElement || e.target === document.body) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);
        
        // Chặn focus/blur events có thể trigger modal
        window.addEventListener('blur', function(e) {
            e.preventDefault();
            e.stopPropagation();
        }, true);
        
        window.addEventListener('focus', function(e) {
            e.preventDefault();
            e.stopPropagation();
        }, true);
    });
    </script>
</body>
</html>
