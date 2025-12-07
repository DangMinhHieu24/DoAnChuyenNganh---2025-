<?php
/**
 * Category Model
 * Model quản lý danh mục dịch vụ
 */

class Category {
    private $conn;
    private $table = 'categories';

    public $category_id;
    public $category_name;
    public $description;
    public $icon;
    public $display_order;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Lấy tất cả danh mục
     */
    public function getAllCategories($status = 'active') {
        $query = "SELECT * FROM " . $this->table;
        
        if ($status) {
            $query .= " WHERE status = :status";
        }
        
        $query .= " ORDER BY display_order, category_name";

        $stmt = $this->conn->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh mục theo ID
     */
    public function getCategoryById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE category_id = :category_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    /**
     * Lấy danh mục với số lượng dịch vụ
     */
    public function getCategoriesWithServiceCount() {
        $query = "SELECT c.*, COUNT(s.service_id) as service_count
                  FROM " . $this->table . " c
                  LEFT JOIN services s ON c.category_id = s.category_id AND s.status = 'active'
                  WHERE c.status = 'active'
                  GROUP BY c.category_id
                  ORDER BY c.display_order, c.category_name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo danh mục mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (category_name, description, icon, display_order, status) 
                  VALUES (:category_name, :description, :icon, :display_order, :status)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':category_name', $this->category_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':icon', $this->icon);
        $stmt->bindParam(':display_order', $this->display_order);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            $this->category_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Cập nhật danh mục
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET category_name = :category_name,
                      description = :description,
                      icon = :icon,
                      display_order = :display_order,
                      status = :status
                  WHERE category_id = :category_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':category_name', $this->category_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':icon', $this->icon);
        $stmt->bindParam(':display_order', $this->display_order);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':category_id', $this->category_id);

        return $stmt->execute();
    }

    /**
     * Xóa danh mục
     */
    public function delete($category_id) {
        $query = "DELETE FROM " . $this->table . " WHERE category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
        return $stmt->execute();
    }
}
?>
