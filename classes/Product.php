<?php
class Product {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($category_id, $name, $description, $price, $margin_amount, $pv_value, $brand, $manufacturer, $supplier, $attributes = null) {
        $sql = "INSERT INTO products (category_id, name, description, price, margin_amount, pv_value, brand, manufacturer, supplier, attributes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->db->insert(
            $sql,
            [$category_id, $name, $description, $price, $margin_amount, $pv_value, $brand, $manufacturer, $supplier, json_encode($attributes)],
            "issdddssss"
        );
    }

    public function getAll($category_id = null) {
        $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
        if ($category_id) {
            $sql .= " WHERE p.category_id = ?";
            return $this->db->query($sql, [(int)$category_id], "i");
        }
        $sql .= " ORDER BY p.created_at DESC";
        return $this->db->query($sql); // Returns array
    }

    public function getById($id) {
        $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?";
        $res = $this->db->query($sql, [(int)$id], "i");
        return !empty($res) ? $res[0] : null;
    }

    public function addImage($product_id, $image_path, $is_primary = 0) {
        $sql = "INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, ?)";
        return $this->db->insert($sql, [$product_id, $image_path, $is_primary], "isi");
    }

    public function getImages($product_id) {
        $sql = "SELECT * FROM product_images WHERE product_id = ?";
        return $this->db->query($sql, [(int)$product_id], "i"); // Returns array
    }

    public function update($id, $category_id, $name, $description, $price, $margin_amount, $pv_value, $brand, $manufacturer, $supplier, $attributes = null) {
        $sql = "UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, margin_amount = ?, pv_value = ?, brand = ?, manufacturer = ?, supplier = ?, attributes = ? WHERE id = ?";
        return $this->db->update($sql, [$category_id, $name, $description, $price, $margin_amount, $pv_value, $brand, $manufacturer, $supplier, json_encode($attributes), $id], "issdddssssi");
    }

    public function delete($id) {
        $sql = "DELETE FROM products WHERE id = ?";
        return $this->db->update($sql, [$id], "i");
    }
}
?>
