<?php
/**
 * Login Page - Trang đăng nhập
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/User.php';

// Nếu đã đăng nhập, redirect
if (isLoggedIn()) {
    $role = getCurrentUserRole();
    if ($role === 'admin') {
        redirect(BASE_URL . '/admin/dashboard.php');
    } else {
        redirect(BASE_URL . '/pages/profile.php');
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        $userModel = new User($db);

        if ($userModel->login($username, $password)) {
            // Lưu thông tin vào session
            $_SESSION['user_id'] = $userModel->user_id;
            $_SESSION['username'] = $userModel->username;
            $_SESSION['full_name'] = $userModel->full_name;
            $_SESSION['email'] = $userModel->email;
            $_SESSION['role'] = $userModel->role;
            $_SESSION['avatar'] = $userModel->avatar;

            // Remember me
            if ($remember) {
                setcookie('user_id', $userModel->user_id, time() + (86400 * 30), '/');
            }

            // Redirect theo role
            if ($userModel->role === 'admin') {
                redirect(BASE_URL . '/admin/dashboard.php');
            } elseif ($userModel->role === 'staff') {
                redirect(BASE_URL . '/staff/dashboard.php');
            } else {
                redirect(BASE_URL . '/pages/profile.php');
            }
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body class="auth-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Đăng nhập</h2>
                            <p class="text-muted">Chào mừng bạn trở lại!</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Tên đăng nhập hoặc Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                           required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </button>

                            <div class="text-center">
                                <p class="mb-2">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                                <a href="forgot-password.php" class="text-muted small">Quên mật khẩu?</a>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>Về trang chủ
                            </a>
                        </div>

                        <div class="mt-4 p-3 bg-light rounded">
                            <p class="mb-2 fw-bold small">Tài khoản demo:</p>
                            <p class="mb-1 small"><strong>Admin:</strong> admin / admin123</p>
                            <p class="mb-1 small"><strong>Khách hàng:</strong> customer1 / admin123</p>
                            <p class="mb-0 small"><strong>Nhân viên:</strong> staff1 / admin123</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>
