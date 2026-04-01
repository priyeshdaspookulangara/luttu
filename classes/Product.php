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

    public function getAll() {
        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id";
        $result = $this->db->query($sql);

        // Convert mysqli_result object to an associative array
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($id) {
        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ?";

        $result = $this->db->query($sql, [$id], "i");

        // Check if the result is an object and fetch the first row
        if ($result && is_object($result)) {
            return $result->fetch_assoc();
        }

        return null;
    }

    public function addImage($product_id, $image_path, $is_primary = 0) {
        $sql = "INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, ?)";
        return $this->db->insert($sql, [$product_id, $image_path, $is_primary], "isi");
    }

    public function getImages($product_id) {
        $sql = "SELECT * FROM product_images WHERE product_id = ?";
        $result = $this->db->query($sql, [$product_id], "i");

        // Convert result to array
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function update($id, $category_id, $name, $description, $price, $margin_amount, $pv_value, $brand, $manufacturer, $supplier, $attributes = null) {
        $sql = "UPDATE products
                SET category_id = ?, name = ?, description = ?, price = ?, margin_amount = ?, pv_value = ?, brand = ?, manufacturer = ?, supplier = ?, attributes = ?
                WHERE id = ?";

        return $this->db->update(
            $sql,
            [$category_id, $name, $description, $price, $margin_amount, $pv_value, $brand, $manufacturer, $supplier, json_encode($attributes), $id],
            "issdddssssi"
        );
    }

    public function delete($id) {
        $sql = "DELETE FROM products WHERE id = ?";
        return $this->db->update($sql, [$id], "i");
    }
}
?>
