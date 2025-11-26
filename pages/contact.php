<?php require_once '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <section class="page-header bg-light py-5">
        <div class="container">
            <h1 class="fw-bold mb-2">Liên hệ với chúng tôi</h1>
            <p class="text-muted mb-0">Chúng tôi luôn sẵn sàng hỗ trợ bạn</p>
        </div>
    </section>
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Thông tin liên hệ</h5>
                            <div class="mb-4">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-map-marker-alt fa-2x text-primary me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Địa chỉ</h6>
                                        <p class="text-muted mb-0"><?php echo SITE_ADDRESS; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-phone fa-2x text-success me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Điện thoại</h6>
                                        <p class="text-muted mb-0"><?php echo SITE_PHONE; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-envelope fa-2x text-info me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Email</h6>
                                        <p class="text-muted mb-0"><?php echo SITE_EMAIL; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Gửi tin nhắn</h5>
                            <form id="contactForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Họ và tên</label>
                                        <input type="text" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Số điện thoại</label>
                                        <input type="tel" class="form-control" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Nội dung</label>
                                        <textarea class="form-control" rows="5" required></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg">      
                                    <i class="fas fa-paper-plane me-2"></i>Gửi tin nhắn
                                </button>
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
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.');
            this.reset();
        });
    </script>
</body>
</html>
