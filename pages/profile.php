<?php
/**
 * Profile Page - Trang thông tin cá nhân
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/User.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    redirect(BASE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

$user_id = getCurrentUserId();
$user = $userModel->getUserById($user_id);

$error = '';
$success = '';

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);

    if (empty($full_name) || empty($phone)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } elseif (!isValidPhone($phone)) {
        $error = 'Số điện thoại không hợp lệ';
    } else {
        $userModel->user_id = $user_id;
        $userModel->full_name = $full_name;
        $userModel->phone = $phone;
        $userModel->address = $address;

        // Xử lý upload avatar
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatar = uploadFile($_FILES['avatar'], 'avatars');
            if ($avatar) {
                // Xóa avatar cũ
                if ($user['avatar']) {
                    deleteFile($user['avatar']);
                }
                $userModel->avatar = $avatar;
            }
        }

        if ($userModel->update()) {
            // Cập nhật session
            $_SESSION['full_name'] = $full_name;
            if (isset($userModel->avatar)) {
                $_SESSION['avatar'] = $userModel->avatar;
            }
            
            $success = 'Cập nhật thông tin thành công';
            $user = $userModel->getUserById($user_id); // Reload user data
        } else {
            $error = 'Có lỗi xảy ra. Vui lòng thử lại.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - <?php echo SITE_NAME; ?></title>
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
                                <img src="<?php echo getAvatarUrl($user['avatar']); ?>" 
                                     alt="Avatar" class="rounded-circle mb-2" width="80" height="80" id="avatarPreview">
                                <h6 class="mb-0"><?php echo htmlspecialchars($user['full_name']); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="profile.php" class="list-group-item list-group-item-action active">
                                    <i class="fas fa-user me-2"></i>Thông tin tài khoản
                                </a>
                                <a href="my-bookings.php" class="list-group-item list-group-item-action">
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
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Thông tin cá nhân</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if ($success): ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label">Ảnh đại diện</label>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo getAvatarUrl($user['avatar']); ?>" 
                                                 alt="Avatar" class="rounded-circle me-3" width="80" height="80" id="currentAvatar">
                                            <div>
                                                <input type="file" class="form-control" name="avatar" id="avatarInput" accept="image/*">
                                                <small class="text-muted">JPG, PNG hoặc GIF. Tối đa 2MB.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" 
                                               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" 
                                               value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                        <small class="text-muted">Email không thể thay đổi</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">Tên đăng nhập</label>
                                        <input type="text" class="form-control" id="username" 
                                               value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                        <small class="text-muted">Tên đăng nhập không thể thay đổi</small>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="address" class="form-label">Địa chỉ</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Ngày tạo tài khoản</label>
                                                <input type="text" class="form-control" 
                                                       value="<?php echo formatDateTime($user['created_at']); ?>" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Cập nhật lần cuối</label>
                                                <input type="text" class="form-control" 
                                                       value="<?php echo formatDateTime($user['updated_at']); ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="change-password.php" class="btn btn-outline-primary">
                                        <i class="fas fa-key me-2"></i>Đổi mật khẩu
                                    </a>
                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview avatar before upload
        document.getElementById('avatarInput').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('currentAvatar').src = e.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
</body>
</html>
