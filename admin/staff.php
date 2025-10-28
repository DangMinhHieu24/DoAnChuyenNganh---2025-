<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Staff.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(BASE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();
$staffModel = new Staff($db);

$staff_list = $staffModel->getAllStaff(null);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhân viên - Admin</title>
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
                    <h2 class="fw-bold mb-0">Quản lý nhân viên</h2>
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
                                        <th>Điện thoại</th>
                                        <th>Email</th>
                                        <th>Chuyên môn</th>
                                        <th>Đánh giá</th>
                                        <th>Số lịch hẹn</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($staff_list as $staff): ?>
                                    <tr>
                                        <td><?php echo $staff['staff_id']; ?></td>
                                        <td>
                                            <img src="<?php echo getAvatarUrl($staff['avatar']); ?>" alt="" width="40" height="40" class="rounded-circle">
                                        </td>
                                        <td><?php echo htmlspecialchars($staff['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['specialization']); ?></td>
                                        <td><?php echo $staff['rating']; ?> ⭐</td>
                                        <td><?php echo $staff['total_bookings']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $staff['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo $staff['status'] == 'active' ? 'Hoạt động' : 'Nghỉ'; ?>
                                            </span>
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
