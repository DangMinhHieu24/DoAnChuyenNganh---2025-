<?php
/**
 * Promotion Model
 */
class Promotion {
    private $conn;
    private $table = 'promotions';

    public $promotion_id;
    public $code;
    public $title;
    public $description;
    public $discount_type;
    public $discount_value;
    public $min_order_value;
    public $max_discount;
    public $usage_limit;
    public $used_count;
    public $start_date;
    public $end_date;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Lấy tất cả khuyến mãi
     */
    public function getAll($status = null) {
        $query = "SELECT * FROM " . $this->table;
        
        if ($status) {
            $query .= " WHERE status = :status";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy khuyến mãi đang hoạt động
     */
    public function getActivePromotions() {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE status = 'active'
                    AND start_date <= NOW()
                    AND end_date >= NOW()
                    AND (usage_limit IS NULL OR used_count < usage_limit)
                  ORDER BY discount_value DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy khuyến mãi theo ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE promotion_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy khuyến mãi theo mã code
     */
    public function getByCode($code) {
        $query = "SELECT * FROM " . $this->table . " WHERE code = :code LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra mã khuyến mãi có hợp lệ không
     */
    public function validatePromotion($code, $order_value) {
        $promo = $this->getByCode($code);
        
        if (!$promo) {
            return ['valid' => false, 'message' => 'Mã khuyến mãi không tồn tại'];
        }
        
        if ($promo['status'] !== 'active') {
            return ['valid' => false, 'message' => 'Mã khuyến mãi không hoạt động'];
        }
        
        $now = date('Y-m-d H:i:s');
        if ($now < $promo['start_date'] || $now > $promo['end_date']) {
            return ['valid' => false, 'message' => 'Mã khuyến mãi đã hết hạn'];
        }
        
        if ($promo['usage_limit'] && $promo['used_count'] >= $promo['usage_limit']) {
            return ['valid' => false, 'message' => 'Mã khuyến mãi đã hết lượt sử dụng'];
        }
        
        if ($order_value < $promo['min_order_value']) {
            return ['valid' => false, 'message' => 'Đơn hàng chưa đủ giá trị tối thiểu'];
        }
        
        // Tính discount
        $discount = 0;
        if ($promo['discount_type'] === 'percentage') {
            $discount = ($order_value * $promo['discount_value']) / 100;
            if ($promo['max_discount'] && $discount > $promo['max_discount']) {
                $discount = $promo['max_discount'];
            }
        } else {
            $discount = $promo['discount_value'];
        }
        
        return [
            'valid' => true,
            'discount' => $discount,
            'promotion' => $promo
        ];
    }

    /**
     * Tạo khuyến mãi mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  SET code = :code,
                      title = :title,
                      description = :description,
                      discount_type = :discount_type,
                      discount_value = :discount_value,
                      min_order_value = :min_order_value,
                      max_discount = :max_discount,
                      usage_limit = :usage_limit,
                      start_date = :start_date,
                      end_date = :end_date,
                      status = :status";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':discount_type', $this->discount_type);
        $stmt->bindParam(':discount_value', $this->discount_value);
        $stmt->bindParam(':min_order_value', $this->min_order_value);
        $stmt->bindParam(':max_discount', $this->max_discount);
        $stmt->bindParam(':usage_limit', $this->usage_limit);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':status', $this->status);

        return $stmt->execute();
    }

    /**
     * Cập nhật khuyến mãi
     */
    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET code = :code,
                      title = :title,
                      description = :description,
                      discount_type = :discount_type,
                      discount_value = :discount_value,
                      min_order_value = :min_order_value,
                      max_discount = :max_discount,
                      usage_limit = :usage_limit,
                      start_date = :start_date,
                      end_date = :end_date,
                      status = :status,
                      updated_at = NOW()
                  WHERE promotion_id = :promotion_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':discount_type', $this->discount_type);
        $stmt->bindParam(':discount_value', $this->discount_value);
        $stmt->bindParam(':min_order_value', $this->min_order_value);
        $stmt->bindParam(':max_discount', $this->max_discount);
        $stmt->bindParam(':usage_limit', $this->usage_limit);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':promotion_id', $this->promotion_id);

        return $stmt->execute();
    }

    /**
     * Xóa khuyến mãi
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE promotion_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Tăng số lần sử dụng
     */
    public function incrementUsage($promotion_id) {
        $query = "UPDATE " . $this->table . "
                  SET used_count = used_count + 1
                  WHERE promotion_id = :promotion_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':promotion_id', $promotion_id);
        return $stmt->execute();
    }
}
?>
