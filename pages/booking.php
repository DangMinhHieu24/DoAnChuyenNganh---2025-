<?php
/**
 * Booking Page - Trang đặt lịch
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Service.php';
require_once '../models/Staff.php';
require_once '../models/Booking.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    setFlashMessage('warning', 'Vui lòng đăng nhập để đặt lịch');
    redirect(BASE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();

$serviceModel = new Service($db);
$staffModel = new Staff($db);
$bookingModel = new Booking($db);

$error = '';
$success = '';

// Lấy service_id từ URL
$service_id = isset($_GET['service']) ? (int)$_GET['service'] : null;

// Lấy tất cả dịch vụ
$services = $serviceModel->getAllServices();

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = (int)$_POST['service_id'];
    $staff_id = (int)$_POST['staff_id'];
    $booking_date = sanitize($_POST['booking_date']);
    $booking_time = sanitize($_POST['booking_time']);
    $notes = sanitize($_POST['notes']);

    // Validation
    if (empty($service_id) || empty($staff_id) || empty($booking_date) || empty($booking_time)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } elseif (isPastDate($booking_date)) {
        $error = 'Không thể đặt lịch cho ngày trong quá khứ';
    } elseif (isPastDateTime($booking_date, $booking_time)) {
        $error = 'Không thể đặt lịch cho thời gian đã qua';
    } else {
        // Lấy thông tin dịch vụ
        $service = $serviceModel->getServiceById($service_id);
        
        if (!$service) {
            $error = 'Dịch vụ không tồn tại';
        } else {
            // Kiểm tra slot thời gian có trống không
            if (!$bookingModel->checkAvailability($staff_id, $booking_date, $booking_time, $service['duration'])) {
                $error = 'Thời gian này đã có người đặt. Vui lòng chọn thời gian khác.';
            } else {
                // Tạo booking
                $bookingModel->customer_id = getCurrentUserId();
                $bookingModel->service_id = $service_id;
                $bookingModel->staff_id = $staff_id;
                $bookingModel->booking_date = $booking_date;
                $bookingModel->booking_time = $booking_time;
                $bookingModel->duration = $service['duration'];
                $bookingModel->total_price = $service['price'];
                $bookingModel->status = 'pending';
                $bookingModel->payment_status = 'unpaid';
                $bookingModel->payment_method = 'cash';
                $bookingModel->notes = $notes;

                if ($bookingModel->create()) {
                    setFlashMessage('success', 'Đặt lịch thành công! Chúng tôi sẽ xác nhận lịch hẹn của bạn sớm nhất.');
                    redirect(BASE_URL . '/pages/my-bookings.php');
                } else {
                    $error = 'Có lỗi xảy ra. Vui lòng thử lại.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lịch - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header bg-light py-5">
        <div class="container">
            <h1 class="fw-bold mb-2">Đặt lịch hẹn</h1>
            <p class="text-muted mb-0">Chọn dịch vụ và thời gian phù hợp với bạn</p>
        </div>
    </section>

    <!-- Booking Form -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Progress Steps -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Tiến trình đặt lịch</small>
                                    <small class="text-muted"><span id="currentStep">0</span>/5 bước</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" id="progressBar" 
                                         style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <form method="POST" action="" id="bookingForm">
                                <!-- Chọn dịch vụ -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <span class="badge bg-primary me-2">1</span>
                                        <i class="fas fa-cut me-2"></i>Chọn dịch vụ <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg" name="service_id" id="serviceSelect" required>
                                        <option value="">-- Chọn dịch vụ --</option>
                                        <?php 
                                        $current_category = '';
                                        foreach ($services as $service): 
                                            if ($current_category != $service['category_name']) {
                                                if ($current_category != '') echo '</optgroup>';
                                                echo '<optgroup label="' . htmlspecialchars($service['category_name']) . '">';
                                                $current_category = $service['category_name'];
                                            }
                                        ?>
                                            <option value="<?php echo $service['service_id']; ?>" 
                                                    data-price="<?php echo $service['price']; ?>"
                                                    data-duration="<?php echo $service['duration']; ?>"
                                                    <?php echo ($service_id == $service['service_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($service['service_name']); ?> 
                                                (<?php echo formatCurrency($service['price']); ?> - <?php echo $service['duration']; ?> phút)
                                            </option>
                                        <?php 
                                        endforeach; 
                                        if ($current_category != '') echo '</optgroup>';
                                        ?>
                                    </select>
                                    <div id="serviceInfo" class="mt-2"></div>
                                </div>

                                <!-- Chọn nhân viên -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <span class="badge bg-secondary me-2" id="step2Badge">2</span>
                                        <i class="fas fa-user-tie me-2"></i>Chọn nhân viên <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg" name="staff_id" id="staffSelect" required disabled 
                                            data-bs-toggle="tooltip" data-bs-placement="top" 
                                            title="Vui lòng chọn dịch vụ trước">
                                        <option value="">-- Vui lòng chọn dịch vụ trước --</option>
                                    </select>
                                    <div id="staffInfo" class="mt-2"></div>
                                </div>

                                <!-- Chọn ngày -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <span class="badge bg-secondary me-2" id="step3Badge">3</span>
                                        <i class="fas fa-calendar me-2"></i>Chọn ngày <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control form-control-lg" name="booking_date" 
                                           id="bookingDate" required disabled
                                           data-bs-toggle="tooltip" data-bs-placement="top" 
                                           title="Vui lòng chọn nhân viên trước"
                                           min="<?php echo date('Y-m-d'); ?>" 
                                           max="<?php echo date('Y-m-d', strtotime('+' . BOOKING_ADVANCE_DAYS . ' days')); ?>">
                                </div>

                                <!-- Chọn giờ -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <span class="badge bg-secondary me-2" id="step4Badge">4</span>
                                        <i class="fas fa-clock me-2"></i>Chọn giờ <span class="text-danger">*</span>
                                    </label>
                                    <div id="timeSlots" class="time-slots-container">
                                        <p class="text-muted">Vui lòng chọn dịch vụ, nhân viên và ngày trước</p>
                                    </div>
                                    <input type="hidden" name="booking_time" id="bookingTime" required>
                                </div>

                                <!-- Ghi chú -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <span class="badge bg-secondary me-2" id="step5Badge">5</span>
                                        <i class="fas fa-comment me-2"></i>Ghi chú (không bắt buộc)
                                    </label>
                                    <textarea class="form-control" name="notes" rows="3" 
                                              placeholder="Nhập ghi chú hoặc yêu cầu đặc biệt..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn" disabled>
                                    <i class="fas fa-calendar-check me-2"></i>Xác nhận đặt lịch
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin đặt lịch</h6>
                        </div>
                        <div class="card-body">
                            <div id="bookingSummary">
                                <p class="text-muted text-center py-4">Chưa có thông tin</p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Lưu ý</h6>
                            <ul class="small text-muted mb-0">
                                <li class="mb-2">Vui lòng đến đúng giờ hẹn</li>
                                <li class="mb-2">Hủy lịch trước 24h để tránh phí phạt</li>
                                <li class="mb-2">Thanh toán tại salon sau khi hoàn thành dịch vụ</li>
                                <li>Liên hệ hotline nếu cần hỗ trợ</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
        let selectedService = null;
        let selectedStaff = null;
        let selectedDate = null;
        let selectedTime = null;
        let currentStep = 0;

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Update progress
        function updateProgress(step) {
            currentStep = step;
            const progress = (step / 5) * 100;
            document.getElementById('currentStep').textContent = step;
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('progressBar').setAttribute('aria-valuenow', progress);
            
            // Update badge colors
            for (let i = 1; i <= 5; i++) {
                const badge = document.getElementById(`step${i}Badge`);
                if (badge) {
                    if (i <= step) {
                        badge.classList.remove('bg-secondary');
                        badge.classList.add('bg-success');
                    } else if (i === step + 1) {
                        badge.classList.remove('bg-secondary', 'bg-success');
                        badge.classList.add('bg-primary');
                    } else {
                        badge.classList.remove('bg-primary', 'bg-success');
                        badge.classList.add('bg-secondary');
                    }
                }
            }
        }

        // Khi chọn dịch vụ
        document.getElementById('serviceSelect').addEventListener('change', function() {
            const serviceId = this.value;
            if (!serviceId) {
                resetForm();
                return;
            }

            const option = this.options[this.selectedIndex];
            selectedService = {
                id: serviceId,
                name: option.text.split('(')[0].trim(),
                price: option.dataset.price,
                duration: option.dataset.duration
            };

            // Hiển thị thông tin dịch vụ
            document.getElementById('serviceInfo').innerHTML = `
                <div class="alert alert-info">
                    <strong>Giá:</strong> ${formatCurrency(selectedService.price)} | 
                    <strong>Thời gian:</strong> ${selectedService.duration} phút
                </div>
            `;

            // Load nhân viên
            loadStaff(serviceId);
            updateProgress(1);
            updateSummary();
        });

        // Load danh sách nhân viên
        function loadStaff(serviceId) {
            fetch(`${BASE_URL}/api/get-staff.php?service_id=${serviceId}`)
                .then(response => response.json())
                .then(data => {
                    const staffSelect = document.getElementById('staffSelect');
                    staffSelect.innerHTML = '<option value="">-- Chọn nhân viên --</option>';
                    
                    if (data.success && data.staff.length > 0) {
                        data.staff.forEach(staff => {
                            const option = document.createElement('option');
                            option.value = staff.staff_id;
                            option.textContent = `${staff.full_name} (${staff.rating}⭐)`;
                            option.dataset.name = staff.full_name;
                            option.dataset.rating = staff.rating;
                            staffSelect.appendChild(option);
                        });
                        staffSelect.disabled = false;
                    } else {
                        staffSelect.innerHTML = '<option value="">Không có nhân viên phù hợp</option>';
                    }
                });
        }

        // Khi chọn nhân viên
        document.getElementById('staffSelect').addEventListener('change', function() {
            const staffId = this.value;
            if (!staffId) {
                document.getElementById('bookingDate').disabled = true;
                return;
            }

            const option = this.options[this.selectedIndex];
            selectedStaff = {
                id: staffId,
                name: option.dataset.name,
                rating: option.dataset.rating
            };

            document.getElementById('bookingDate').disabled = false;
            updateProgress(2);
            updateSummary();
        });

        // Khi chọn ngày
        document.getElementById('bookingDate').addEventListener('change', function() {
            selectedDate = this.value;
            if (selectedDate && selectedStaff && selectedService) {
                loadTimeSlots();
            }
            updateProgress(3);
            updateSummary();
        });

        // Load time slots
        function loadTimeSlots() {
            const timeSlotsDiv = document.getElementById('timeSlots');
            timeSlotsDiv.innerHTML = '<p class="text-muted">Đang tải...</p>';

            fetch(`${BASE_URL}/api/get-time-slots.php?staff_id=${selectedStaff.id}&date=${selectedDate}&duration=${selectedService.duration}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.slots.length > 0) {
                        let html = '<div class="row g-2">';
                        data.slots.forEach(slot => {
                            html += `
                                <div class="col-4 col-md-3">
                                    <button type="button" class="btn btn-outline-primary w-100 time-slot-btn" 
                                            data-time="${slot}">
                                        ${slot}
                                    </button>
                                </div>
                            `;
                        });
                        html += '</div>';
                        timeSlotsDiv.innerHTML = html;

                        // Add click event to time slot buttons
                        document.querySelectorAll('.time-slot-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('active'));
                                this.classList.add('active');
                                selectedTime = this.dataset.time;
                                document.getElementById('bookingTime').value = selectedTime;
                                document.getElementById('submitBtn').disabled = false;
                                updateProgress(4);
                                updateSummary();
                            });
                        });
                    } else {
                        timeSlotsDiv.innerHTML = '<p class="text-danger">Không có khung giờ trống trong ngày này</p>';
                    }
                });
        }

        // Update booking summary
        function updateSummary() {
            let html = '';
            
            if (selectedService) {
                html += `
                    <div class="mb-3">
                        <small class="text-muted">Dịch vụ</small>
                        <div class="fw-bold">${selectedService.name}</div>
                    </div>
                `;
            }
            
            if (selectedStaff) {
                html += `
                    <div class="mb-3">
                        <small class="text-muted">Nhân viên</small>
                        <div class="fw-bold">${selectedStaff.name} (${selectedStaff.rating}⭐)</div>
                    </div>
                `;
            }
            
            if (selectedDate) {
                html += `
                    <div class="mb-3">
                        <small class="text-muted">Ngày</small>
                        <div class="fw-bold">${formatDate(selectedDate)}</div>
                    </div>
                `;
            }
            
            if (selectedTime) {
                html += `
                    <div class="mb-3">
                        <small class="text-muted">Giờ</small>
                        <div class="fw-bold">${selectedTime}</div>
                    </div>
                `;
            }
            
            if (selectedService) {
                html += `
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Tổng tiền:</strong>
                        <h5 class="text-primary mb-0">${formatCurrency(selectedService.price)}</h5>
                    </div>
                `;
            }
            
            document.getElementById('bookingSummary').innerHTML = html || '<p class="text-muted text-center py-4">Chưa có thông tin</p>';
        }

        // Helper functions
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('vi-VN', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }

        function resetForm() {
            selectedService = null;
            selectedStaff = null;
            selectedDate = null;
            selectedTime = null;
            document.getElementById('serviceInfo').innerHTML = '';
            document.getElementById('staffSelect').innerHTML = '<option value="">-- Vui lòng chọn dịch vụ trước --</option>';
            document.getElementById('staffSelect').disabled = true;
            document.getElementById('bookingDate').disabled = true;
            document.getElementById('timeSlots').innerHTML = '<p class="text-muted">Vui lòng chọn dịch vụ, nhân viên và ngày trước</p>';
            document.getElementById('submitBtn').disabled = true;
            updateSummary();
        }

        // Trigger change if service is pre-selected
        window.addEventListener('load', function() {
            const serviceSelect = document.getElementById('serviceSelect');
            if (serviceSelect.value) {
                serviceSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>
