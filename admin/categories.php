<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Category.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(BASE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();
$categoryModel = new Category($db);

$categories = $categoryModel->getAllCategories(null);

if (isset($_POST['add_category'])) {
    $categoryModel->category_name = sanitize($_POST['category_name']);
    $categoryModel->description = sanitize($_POST['description']);
    $categoryModel->icon = sanitize($_POST['icon']);
    $categoryModel->display_order = (int)$_POST['display_order'];
    $categoryModel->status = 'active';
    
    if ($categoryModel->create()) {
        setFlashMessage('success', 'Thêm danh mục thành công');
    } else {
        setFlashMessage('danger', 'Có lỗi xảy ra');
    }
    redirect($_SERVER['PHP_SELF']);
}

if (isset($_POST['edit_category'])) {
    $categoryModel->category_id = (int)$_POST['category_id'];
    $categoryModel->category_name = sanitize($_POST['category_name']);
    $categoryModel->description = sanitize($_POST['description']);
    $categoryModel->icon = sanitize($_POST['icon']);
    $categoryModel->display_order = (int)$_POST['display_order'];
    $categoryModel->status = sanitize($_POST['status']);
    
    if ($categoryModel->update()) {
        setFlashMessage('success', 'Cập nhật danh mục thành công');
    } else {
        setFlashMessage('danger', 'Có lỗi xảy ra');
    }
    redirect($_SERVER['PHP_SELF']);
}

if (isset($_POST['toggle_status'])) {
    $category_id = (int)$_POST['category_id'];
    $new_status = sanitize($_POST['new_status']);
    
    if ($categoryModel->updateStatus($category_id, $new_status)) {
        setFlashMessage('success', 'Cập nhật trạng thái thành công');
    } else {
        setFlashMessage('danger', 'Có lỗi xảy ra');
    }
    redirect($_SERVER['PHP_SELF']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục - Admin</title>
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
                    <h2 class="fw-bold mb-0">Quản lý danh mục</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus me-2"></i>Thêm danh mục
                    </button>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Icon</th>
                                        <th>Tên danh mục</th>
                                        <th>Mô tả</th>
                                        <th>Thứ tự</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?php echo $category['category_id']; ?></td>
                                        <td><i class="fas <?php echo $category['icon']; ?> fa-2x text-primary"></i></td>
                                        <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars($category['description']); ?></td>
                                        <td><?php echo $category['display_order']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $category['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo $category['status'] == 'active' ? 'Hoạt động' : 'Tạm dừng'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $category['category_id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Xác nhận thay đổi trạng thái?')">
                                                <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                                <input type="hidden" name="new_status" value="<?php echo $category['status'] == 'active' ? 'inactive' : 'active'; ?>">
                                                <?php if ($category['status'] == 'active'): ?>
                                                    <button type="submit" name="toggle_status" class="btn btn-sm btn-secondary" title="Tạm dừng">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" name="toggle_status" class="btn btn-sm btn-success" title="Kích hoạt">
                                                        <i class="fas fa-play"></i>
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

    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm danh mục mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên danh mục</label>
                            <input type="text" class="form-control" name="category_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon (Font Awesome class)</label>
                            <input type="text" class="form-control" name="icon" placeholder="fa-scissors" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" class="form-control" name="display_order" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" name="add_category" class="btn btn-primary">Thêm danh mục</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modals -->
    <?php foreach ($categories as $category): ?>
    <div class="modal fade" id="editModal<?php echo $category['category_id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Sửa danh mục</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên danh mục</label>
                            <input type="text" class="form-control" name="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="2"><?php echo htmlspecialchars($category['description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon (Font Awesome class)</label>
                            <input type="text" class="form-control" name="icon" value="<?php echo htmlspecialchars($category['icon']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" class="form-control" name="display_order" value="<?php echo $category['display_order']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="active" <?php echo $category['status'] == 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                <option value="inactive" <?php echo $category['status'] == 'inactive' ? 'selected' : ''; ?>>Tạm dừng</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" name="edit_category" class="btn btn-warning">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
