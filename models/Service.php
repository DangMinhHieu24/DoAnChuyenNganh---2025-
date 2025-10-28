<?php
/**
 * Service Model
 * Model quản lý dịch vụ
 */

class Service {
    private $conn;
    private $table = 'services';

    public $service_id;
    public $category_id;
    public $service_name;
    public $description;
    public $price;
    public $duration;
    public $image;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Lấy tất cả dịch vụ
     */
    public function getAllServices($category_id = null, $status = 'active') {
        $query = "SELECT s.*, c.category_name 
                  FROM " . $this->table . " s
                  LEFT JOIN categories c ON s.category_id = c.category_id
                  WHERE 1=1";
        
        if ($category_id) {
            $query .= " AND s.category_id = :category_id";
        }
        if ($status) {
            $query .= " AND s.status = :status";
        }
        
        $query .= " ORDER BY c.display_order, s.service_name";

        $stmt = $this->conn->prepare($query);
        
        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id);
        }
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy dịch vụ theo ID
     */
    public function getServiceById($id) {
        $query = "SELECT s.*, c.category_name 
                  FROM " . $this->table . " s
                  LEFT JOIN categories c ON s.category_id = c.category_id
                  WHERE s.service_id = :service_id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':service_id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    /**
     * Tạo dịch vụ mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (category_id, service_name, description, price, duration, image, status) 
                  VALUES (:category_id, :service_name, :description, :price, :duration, :image, :status)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':service_name', $this->service_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':duration', $this->duration);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            $this->service_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Cập nhật dịch vụ
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET category_id = :category_id,
                      service_name = :service_name,
                      description = :description,
                      price = :price,
                      duration = :duration,
                      status = :status";
        
        if ($this->image) {
            $query .= ", image = :image";
        }
        
        $query .= " WHERE service_id = :service_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':service_name', $this->service_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':duration', $this->duration);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':service_id', $this->service_id);
        
        if ($this->image) {
            $stmt->bindParam(':image', $this->image);
        }

        return $stmt->execute();
    }

    /**
     * Xóa dịch vụ
     */
    public function delete($service_id) {
        $query = "DELETE FROM " . $this->table . " WHERE service_id = :service_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':service_id', $service_id);
        return $stmt->execute();
    }

    /**
     * Lấy dịch vụ phổ biến
     */
    public function getPopularServices($limit = 6) {
        $query = "SELECT s.*, c.category_name, COUNT(b.booking_id) as booking_count
                  FROM " . $this->table . " s
                  LEFT JOIN categories c ON s.category_id = c.category_id
                  LEFT JOIN bookings b ON s.service_id = b.service_id
                  WHERE s.status = 'active'
                  GROUP BY s.service_id
                  ORDER BY booking_count DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm kiếm dịch vụ - Tìm chính xác theo từ khóa
     */
    public function search($keyword) {
        // Chuẩn hóa keyword
        $keyword = trim($keyword);
        if (empty($keyword)) {
            return [];
        }
        
        // Tạo pattern tìm kiếm với word boundary
        $searchWord = "% {$keyword} %";      // Từ ở giữa
        $searchStart = "{$keyword} %";       // Từ ở đầu
        $searchEnd = "% {$keyword}";         // Từ ở cuối
        $searchExact = $keyword;             // Khớp chính xác
        
        $query = "SELECT DISTINCT s.*, c.category_name,
                  (
                      CASE 
                          -- Khớp chính xác tên dịch vụ
                          WHEN LOWER(s.service_name) = LOWER(:exact_name) THEN 100
                          -- Tên bắt đầu bằng từ khóa (có khoảng trắng sau)
                          WHEN LOWER(CONCAT(s.service_name, ' ')) LIKE LOWER(:start_name) THEN 95
                          -- Khớp chính xác category
                          WHEN LOWER(c.category_name) = LOWER(:exact_cat) THEN 90
                          -- Category bắt đầu bằng từ khóa
                          WHEN LOWER(CONCAT(c.category_name, ' ')) LIKE LOWER(:start_cat) THEN 85
                          -- Từ khóa là một từ riêng trong tên (có khoảng trắng 2 bên)
                          WHEN LOWER(CONCAT(' ', s.service_name, ' ')) LIKE LOWER(:word_name) THEN 80
                          -- Tên kết thúc bằng từ khóa
                          WHEN LOWER(CONCAT(' ', s.service_name)) LIKE LOWER(:end_name) THEN 75
                          -- Từ khóa trong description (là từ riêng)
                          WHEN LOWER(CONCAT(' ', s.description, ' ')) LIKE LOWER(:word_desc) THEN 60
                          ELSE 0
                      END
                  ) as relevance_score
                  FROM " . $this->table . " s
                  LEFT JOIN categories c ON s.category_id = c.category_id
                  WHERE s.status = 'active'
                  AND (
                      -- Tìm chính xác
                      LOWER(s.service_name) = LOWER(:search_exact)
                      -- Tìm theo từ (word boundary)
                      OR LOWER(CONCAT(' ', s.service_name, ' ')) LIKE LOWER(:search_word)
                      OR LOWER(CONCAT(s.service_name, ' ')) LIKE LOWER(:search_start)
                      OR LOWER(CONCAT(' ', s.service_name)) LIKE LOWER(:search_end)
                      -- Tìm trong category
                      OR LOWER(c.category_name) = LOWER(:cat_exact)
                      OR LOWER(CONCAT(' ', c.category_name, ' ')) LIKE LOWER(:cat_word)
                      OR LOWER(CONCAT(c.category_name, ' ')) LIKE LOWER(:cat_start)
                      -- Tìm trong description (từ riêng)
                      OR LOWER(CONCAT(' ', s.description, ' ')) LIKE LOWER(:desc_word)
                  )
                  HAVING relevance_score > 0
                  ORDER BY relevance_score DESC, s.service_name ASC";

        $stmt = $this->conn->prepare($query);
        
        // Bind tất cả parameters
        $stmt->bindParam(':exact_name', $searchExact);
        $stmt->bindParam(':start_name', $searchStart);
        $stmt->bindParam(':exact_cat', $searchExact);
        $stmt->bindParam(':start_cat', $searchStart);
        $stmt->bindParam(':word_name', $searchWord);
        $stmt->bindParam(':end_name', $searchEnd);
        $stmt->bindParam(':word_desc', $searchWord);
        
        $stmt->bindParam(':search_exact', $searchExact);
        $stmt->bindParam(':search_word', $searchWord);
        $stmt->bindParam(':search_start', $searchStart);
        $stmt->bindParam(':search_end', $searchEnd);
        $stmt->bindParam(':cat_exact', $searchExact);
        $stmt->bindParam(':cat_word', $searchWord);
        $stmt->bindParam(':cat_start', $searchStart);
        $stmt->bindParam(':desc_word', $searchWord);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
