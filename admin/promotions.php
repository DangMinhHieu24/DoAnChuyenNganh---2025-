<?php
/**
 * Admin - Quản lý khuyến mãi
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Promotion.php';

// Kiểm tra đăng nhập và quyền admin
if (!isLoggedIn() || !isAdmin()) {
    redirect(BASE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();
$promotionModel = new Promotion($db);

// Xử lý thêm/sửa/xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action']);
    
    if ($action === 'create' || $action === 'update') {
        $promotionModel->code = strtoupper(sanitize($_POST['code']));
        $promotionModel->title = sanitize($_POST['title']);
        $promotionModel->description = sanitize($_POST['description']);
        $promotionModel->discount_type = sanitize($_POST['discount_type']);
        $promotionModel->discount_value = (float)$_POST['discount_value'];
        $promotionModel->min_order_value = (float)$_POST['min_order_value'];
        $promotionModel->max_discount = !empty($_POST['max_discount']) ? (float)$_POST['max_discount'] : null;
        $promotionModel->usage_limit = !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null;
        $promotionModel->start_date = sanitize($_POST['start_date']);
        $promotionModel->end_date = sanitize($_POST['end_date']);
        $promotionModel->status = sanitize($_POST['status']);
        
        if ($action === 'create') {
            if ($promotionModel->create()) {
                setFlashMessage('success', 'Thêm khuyến mãi thành công!');
            } else {
                setFlashMessage('danger', 'Có lỗi xảy ra!');
            }
        } else {
            $promotionModel->promotion_id = (int)$_POST['promotion_id'];
            if ($promotionModel->update()) {
                setFlashMessage('success', 'Cập nhật khuyến mãi thành công!');
            } else {
                setFlashMessage('danger', 'Có lỗi xảy ra!');
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['promotion_id'];
        if ($promotionModel->delete($id)) {
            setFlashMessage('success', 'Xóa khuyến mãi thành công!');
        } else {
            setFlashMessage('danger', 'Có lỗi xảy ra!');
        }
    }
    redirect($_SERVER['PHP_SELF']);
}

// Lấy danh sách khuyến mãi
$promotions = $promotionModel->getAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khuyến mãi - Admin</title>
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
                    <h2 class="fw-bold mb-0">Quản lý khuyến mãi</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPromotionModal">
                        <i class="fas fa-plus me-2"></i>Thêm khuyến mãi
                    </button>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã</th>
                                    <th>Tên</th>
                                    <th>Loại</th>
                                    <th>Giá trị</th>
                                    <th>Đơn tối thiểu</th>
                                    <th>Thời gian</th>
                                    <th>Đã dùng</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($promotions as $promo): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($promo['code']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($promo['title']); ?></td>
                                    <td>
                                        <?php if ($promo['discount_type'] === 'percentage'): ?>
                                            <span class="badge bg-info">Phần trăm</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Cố định</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($promo['discount_type'] === 'percentage'): ?>
                                            <?php echo $promo['discount_value']; ?>%
                                        <?php else: ?>
                                            <?php echo formatCurrency($promo['discount_value']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo formatCurrency($promo['min_order_value']); ?></td>
                                    <td>
                                        <small>
                                            <?php echo date('d/m/Y', strtotime($promo['start_date'])); ?><br>
                                            → <?php echo date('d/m/Y', strtotime($promo['end_date'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php echo $promo['used_count']; ?>
                                        <?php if ($promo['usage_limit']): ?>
                                            / <?php echo $promo['usage_limit']; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($promo['status'] === 'active'): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Tắt</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning btn-edit" 
                                                data-id="<?php echo $promo['promotion_id']; ?>"
                                                data-promotion='<?php echo json_encode($promo); ?>'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Xác nhận xóa?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="promotion_id" value="<?php echo $promo['promotion_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
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

<!-- Add/Edit Promotion Modal -->
<div class="modal fade" id="addPromotionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="promotionForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm khuyến mãi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="promotion_id" id="promotion_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã khuyến mãi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" id="code" required>
                            <small class="text-muted">VD: SUMMER2025, WELCOME10</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên khuyến mãi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="title" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                            <select class="form-select" name="discount_type" id="discount_type" required>
                                <option value="percentage">Phần trăm (%)</option>
                                <option value="fixed">Cố định (VND)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="discount_value" id="discount_value" required min="0" step="0.01">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Giảm tối đa (VND)</label>
                            <input type="number" class="form-control" name="max_discount" id="max_discount" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giá trị đơn tối thiểu <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="min_order_value" id="min_order_value" required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giới hạn số lần dùng</label>
                            <input type="number" class="form-control" name="usage_limit" id="usage_limit" min="1">
                            <small class="text-muted">Để trống = không giới hạn</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" name="start_date" id="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" name="end_date" id="end_date" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" id="status" required>
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Tắt</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Edit promotion
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        const promo = JSON.parse(this.dataset.promotion);
        
        document.getElementById('modalTitle').textContent = 'Sửa khuyến mãi';
        document.getElementById('formAction').value = 'update';
        document.getElementById('promotion_id').value = promo.promotion_id;
        document.getElementById('code').value = promo.code;
        document.getElementById('title').value = promo.title;
        document.getElementById('description').value = promo.description || '';
        document.getElementById('discount_type').value = promo.discount_type;
        document.getElementById('discount_value').value = promo.discount_value;
        document.getElementById('max_discount').value = promo.max_discount || '';
        document.getElementById('min_order_value').value = promo.min_order_value;
        document.getElementById('usage_limit').value = promo.usage_limit || '';
        document.getElementById('start_date').value = promo.start_date.replace(' ', 'T');
        document.getElementById('end_date').value = promo.end_date.replace(' ', 'T');
        document.getElementById('status').value = promo.status;
        
        new bootstrap.Modal(document.getElementById('addPromotionModal')).show();
    });
});

// Reset form khi đóng modal
document.getElementById('addPromotionModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('promotionForm').reset();
    document.getElementById('modalTitle').textContent = 'Thêm khuyến mãi';
    document.getElementById('formAction').value = 'create';
    document.getElementById('promotion_id').value = '';
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
