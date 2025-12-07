<?php
/**
 * Review Model
 */
class Review {
    private $conn;
    private $table = 'reviews';

    public $review_id;
    public $booking_id;
    public $customer_id;
    public $service_id;
    public $staff_id;
    public $rating;
    public $comment;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Tạo đánh giá mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  SET booking_id = :booking_id,
                      customer_id = :customer_id,
                      service_id = :service_id,
                      staff_id = :staff_id,
                      rating = :rating,
                      comment = :comment";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':booking_id', $this->booking_id);
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':service_id', $this->service_id);
        $stmt->bindParam(':staff_id', $this->staff_id);
        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':comment', $this->comment);

        if ($stmt->execute()) {
            // Cập nhật rating cho service và staff
            $this->updateServiceRating($this->service_id);
            $this->updateStaffRating($this->staff_id);
            return true;
        }
        return false;
    }

    /**
     * Kiểm tra đã đánh giá chưa
     */
    public function hasReviewed($booking_id) {
        $query = "SELECT review_id FROM " . $this->table . " 
                  WHERE booking_id = :booking_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Lấy đánh giá theo dịch vụ
     */
    public function getByService($service_id, $limit = 10) {
        $query = "SELECT r.*, u.full_name, u.avatar, b.booking_date
                  FROM " . $this->table . " r
                  JOIN users u ON r.customer_id = u.user_id
                  JOIN bookings b ON r.booking_id = b.booking_id
                  WHERE r.service_id = :service_id
                  ORDER BY r.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':service_id', $service_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy đánh giá theo nhân viên
     */
    public function getByStaff($staff_id, $limit = 10) {
        $query = "SELECT r.*, u.full_name, s.service_name
                  FROM " . $this->table . " r
                  JOIN users u ON r.customer_id = u.user_id
                  JOIN services s ON r.service_id = s.service_id
                  WHERE r.staff_id = :staff_id
                  ORDER BY r.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật rating dịch vụ
     */
    private function updateServiceRating($service_id) {
        $query = "UPDATE services 
                  SET rating = (
                      SELECT AVG(rating) 
                      FROM " . $this->table . " 
                      WHERE service_id = :service_id
                  )
                  WHERE service_id = :service_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':service_id', $service_id);
        return $stmt->execute();
    }

    /**
     * Cập nhật rating nhân viên
     */
    private function updateStaffRating($staff_id) {
        $query = "UPDATE staff 
                  SET rating = (
                      SELECT AVG(rating) 
                      FROM " . $this->table . " 
                      WHERE staff_id = :staff_id
                  )
                  WHERE staff_id = :staff_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        return $stmt->execute();
    }
}
?>
