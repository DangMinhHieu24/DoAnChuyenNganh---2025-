<?php
/**
 * Services Page - Trang dịch vụ
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Category.php';
require_once '../models/Service.php';

$database = new Database();
$db = $database->getConnection();

$categoryModel = new Category($db);
$serviceModel = new Service($db);

// Lấy category từ URL
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Lấy danh sách categories
$categories = $categoryModel->getAllCategories();

// Lấy danh sách services
if ($search) {
    $services = $serviceModel->search($search);
} else {
    $services = $serviceModel->getAllServices($category_id);
}

// Lấy thông tin category hiện tại
$current_category = null;
if ($category_id) {
    $current_category = $categoryModel->getCategoryById($category_id);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dịch vụ - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="fw-bold mb-2">
                        <?php 
                        if ($current_category) {
                            echo htmlspecialchars($current_category['category_name']);
                        } elseif ($search) {
                            echo 'Kết quả tìm kiếm: "' . htmlspecialchars($search) . '"';
                        } else {
                            echo 'Tất cả dịch vụ';
                        }
                        ?>
                    </h1>
                    <p class="text-muted mb-0">
                        <?php 
                        if ($search) {
                            echo 'Tìm thấy ' . count($services) . ' dịch vụ';
                        } else {
                            echo 'Khám phá các dịch vụ làm đẹp chuyên nghiệp';
                        }
                        ?>
                    </p>
                </div>
                <div class="col-lg-6">
                    <form method="GET" action="" class="mt-3 mt-lg-0">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Tìm kiếm dịch vụ..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-list me-2"></i>Danh mục</h6>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="services.php" class="list-group-item list-group-item-action <?php echo !$category_id ? 'active' : ''; ?>">
                                <i class="fas fa-th me-2"></i>Tất cả dịch vụ
                            </a>
                            <?php foreach ($categories as $cat): ?>
                            <a href="services.php?category=<?php echo $cat['category_id']; ?>" 
                               class="list-group-item list-group-item-action <?php echo $category_id == $cat['category_id'] ? 'active' : ''; ?>">
                                <i class="fas <?php echo $cat['icon']; ?> me-2"></i>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Lưu ý</h6>
                            <ul class="small text-muted mb-0">
                                <li class="mb-2">Giá có thể thay đổi tùy theo độ dài tóc</li>
                                <li class="mb-2">Vui lòng đến đúng giờ hẹn</li>
                                <li class="mb-2">Hủy lịch trước 24h để được hoàn phí</li>
                                <li>Liên hệ hotline để được tư vấn</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Services List -->
                <div class="col-lg-9">
                    <?php if (empty($services)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Không tìm thấy dịch vụ nào</h5>
                            <a href="services.php" class="btn btn-primary mt-3">Xem tất cả dịch vụ</a>
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($services as $service): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="service-card h-100">
                                    <div class="service-image">
                                        <img src="<?php echo getServiceImageUrl($service['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                                        <div class="service-badge"><?php echo htmlspecialchars($service['category_name']); ?></div>
                                    </div>
                                    <div class="service-body p-4">
                                        <h5 class="mb-2"><?php echo htmlspecialchars($service['service_name']); ?></h5>
                                        <p class="text-muted small mb-3">
                                            <?php echo htmlspecialchars(substr($service['description'], 0, 80)); ?>...
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <div class="service-price"><?php echo formatCurrency($service['price']); ?></div>
                                                <small class="text-muted">
                                                    <i class="far fa-clock"></i> <?php echo $service['duration']; ?> phút
                                                </small>
                                            </div>
                                        </div>
                                        <a href="booking.php?service=<?php echo $service['service_id']; ?>" 
                                           class="btn btn-primary w-100">
                                            <i class="fas fa-calendar-check me-2"></i>Đặt lịch
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
