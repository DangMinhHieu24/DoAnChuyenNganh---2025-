<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/User.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(BASE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

// Xử lý khóa/mở khóa tài khoản
if (isset($_POST['toggle_status'])) {
    $user_id = (int)$_POST['user_id'];
    $new_status = sanitize($_POST['new_status']);
    
    if ($userModel->updateStatus($user_id, $new_status)) {
        setFlashMessage('success', 'Cập nhật trạng thái thành công');
    } else {
        setFlashMessage('danger', 'Có lỗi xảy ra');
    }
    redirect($_SERVER['PHP_SELF']);
}

$customers = $userModel->getAllUsers('customer', null);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khách hàng - Admin</title>
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
                    <h2 class="fw-bold mb-0">Quản lý khách hàng</h2>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Ảnh</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Điện thoại</th>
                                        <th>Địa chỉ</th>
                                        <th>Ngày đăng ký</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><?php echo $customer['user_id']; ?></td>
                                        <td>
                                            <img src="<?php echo getAvatarUrl($customer['avatar']); ?>" alt="" width="40" height="40" class="rounded-circle">
                                        </td>
                                        <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['address'] ?? '-'); ?></td>
                                        <td><?php echo formatDate($customer['created_at']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $customer['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo $customer['status'] == 'active' ? 'Hoạt động' : 'Khóa'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Xác nhận thay đổi trạng thái tài khoản?')">
                                                <input type="hidden" name="user_id" value="<?php echo $customer['user_id']; ?>">
                                                <input type="hidden" name="new_status" value="<?php echo $customer['status'] == 'active' ? 'inactive' : 'active'; ?>">
                                                <?php if ($customer['status'] == 'active'): ?>
                                                    <button type="submit" name="toggle_status" class="btn btn-sm btn-danger" title="Khóa tài khoản">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" name="toggle_status" class="btn btn-sm btn-success" title="Mở khóa">
                                                        <i class="fas fa-unlock"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
