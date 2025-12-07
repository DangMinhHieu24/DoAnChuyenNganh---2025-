<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(BASE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();

if (isset($_POST['update_settings'])) {
    setFlashMessage('success', 'Cập nhật cài đặt thành công');
    redirect($_SERVER['PHP_SELF']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt - Admin</title>
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
                <h2 class="fw-bold mb-4">Cài đặt hệ thống</h2>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <h6 class="fw-bold mb-3">Thông tin chung</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tên website</label>
                                    <input type="text" class="form-control" value="<?php echo SITE_NAME; ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo SITE_EMAIL; ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control" value="<?php echo SITE_PHONE; ?>" readonly>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h6 class="fw-bold mb-3">Cài đặt đặt lịch</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số ngày đặt trước tối đa</label>
                                    <input type="number" class="form-control" value="<?php echo BOOKING_ADVANCE_DAYS; ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số giờ hủy lịch tối thiểu</label>
                                    <input type="number" class="form-control" value="<?php echo BOOKING_CANCEL_HOURS; ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Giờ mở cửa</label>
                                    <input type="time" class="form-control" value="<?php echo WORKING_START_TIME; ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Giờ đóng cửa</label>
                                    <input type="time" class="form-control" value="<?php echo WORKING_END_TIME; ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Để thay đổi cài đặt, vui lòng chỉnh sửa file <code>config/config.php</code>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
