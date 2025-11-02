<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Service.php';
require_once '../models/Category.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(BASE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();
$serviceModel = new Service($db);
$categoryModel = new Category($db);

$services = $serviceModel->getAllServices(null, null);
$categories = $categoryModel->getAllCategories(null);

// Xử lý thêm/sửa/xóa
if (isset($_POST['add_service'])) {
    $serviceModel->category_id = (int)$_POST['category_id'];
    $serviceModel->service_name = sanitize($_POST['service_name']);
    $serviceModel->description = sanitize($_POST['description']);
    $serviceModel->price = (float)$_POST['price'];
    $serviceModel->duration = (int)$_POST['duration'];
    $serviceModel->status = sanitize($_POST['status']);
    $serviceModel->image = null;
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $serviceModel->image = uploadFile($_FILES['image'], 'services');
    }
    
    if ($serviceModel->create()) {
        setFlashMessage('success', 'Thêm dịch vụ thành công');
    } else {
        setFlashMessage('danger', 'Có lỗi xảy ra');
    }
    redirect($_SERVER['PHP_SELF']);
}

if (isset($_POST['edit_service'])) {
    $serviceModel->service_id = (int)$_POST['service_id'];
    $serviceModel->category_id = (int)$_POST['category_id'];
    $serviceModel->service_name = sanitize($_POST['service_name']);
    $serviceModel->description = sanitize($_POST['description']);
    $serviceModel->price = (float)$_POST['price'];
    $serviceModel->duration = (int)$_POST['duration'];
    $serviceModel->status = sanitize($_POST['status']);
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $serviceModel->image = uploadFile($_FILES['image'], 'services');
    }
    
    if ($serviceModel->update()) {
        setFlashMessage('success', 'Cập nhật dịch vụ thành công');
    } else {
        setFlashMessage('danger', 'Có lỗi xảy ra');
    }
    redirect($_SERVER['PHP_SELF']);
}

if (isset($_POST['delete_service'])) {
    $service_id = (int)$_POST['service_id'];
    if ($serviceModel->delete($service_id)) {
        setFlashMessage('success', 'Xóa dịch vụ thành công');
    } else {
        setFlashMessage('danger', 'Không thể xóa dịch vụ');
    }
    redirect($_SERVER['PHP_SELF']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý dịch vụ - Admin</title>
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
                    <h2 class="fw-bold mb-0">Quản lý dịch vụ</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus me-2"></i>Thêm dịch vụ
                    </button>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Hình ảnh</th>
                                        <th>Tên dịch vụ</th>
                                        <th>Danh mục</th>
                                        <th>Giá</th>
                                        <th>Thời gian</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td><?php echo $service['service_id']; ?></td>
                                        <td>
                                            <img src="<?php echo getServiceImageUrl($service['image']); ?>" alt="" width="50" height="50" class="rounded">
                                        </td>
                                        <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                        <td><?php echo htmlspecialchars($service['category_name']); ?></td>
                                        <td><?php echo formatCurrency($service['price']); ?></td>
                                        <td><?php echo $service['duration']; ?> phút</td>
                                        <td>
                                            <span class="badge bg-<?php echo $service['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo $service['status'] == 'active' ? 'Hoạt động' : 'Tạm dừng'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $service['service_id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Xác nhận xóa?')">
                                                <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                                <button type="submit" name="delete_service" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm dịch vụ mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Danh mục</label>
                            <select class="form-select" name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên dịch vụ</label>
                            <input type="text" class="form-control" name="service_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá (VNĐ)</label>
                                <input type="number" class="form-control" name="price" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Thời gian (phút)</label>
                                <input type="number" class="form-control" name="duration" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="active">Hoạt động</option>
                                <option value="inactive">Tạm dừng</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" name="add_service" class="btn btn-primary">Thêm dịch vụ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modals -->
    <?php foreach ($services as $service): ?>
    <div class="modal fade" id="editModal<?php echo $service['service_id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Sửa dịch vụ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Danh mục</label>
                            <select class="form-select" name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['category_id']; ?>" <?php echo $cat['category_id'] == $service['category_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên dịch vụ</label>
                            <input type="text" class="form-control" name="service_name" value="<?php echo htmlspecialchars($service['service_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($service['description']); ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá (VNĐ)</label>
                                <input type="number" class="form-control" name="price" value="<?php echo $service['price']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Thời gian (phút)</label>
                                <input type="number" class="form-control" name="duration" value="<?php echo $service['duration']; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh hiện tại</label><br>
                            <img src="<?php echo getServiceImageUrl($service['image']); ?>" alt="" width="100" class="rounded mb-2">
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">Để trống nếu không muốn đổi ảnh</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="active" <?php echo $service['status'] == 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                <option value="inactive" <?php echo $service['status'] == 'inactive' ? 'selected' : ''; ?>>Tạm dừng</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" name="edit_service" class="btn btn-warning">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
