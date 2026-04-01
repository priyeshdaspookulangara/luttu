<?php
class Category {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($name, $parent_id = null, $custom_attributes = null) {
        $sql = "INSERT INTO categories (name, parent_id, custom_attributes) VALUES (?, ?, ?)";
        return $this->db->insert($sql, [$name, $parent_id, json_encode($custom_attributes)], "sis");
    }

    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $result = $this->db->query($sql);
        $data = [];
        if ($result && is_object($result)) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getById($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $result = $this->db->query($sql, [$id], "i");
        if ($result && is_object($result)) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function update($id, $name, $parent_id = null, $custom_attributes = null) {
        $sql = "UPDATE categories SET name = ?, parent_id = ?, custom_attributes = ? WHERE id = ?";
        return $this->db->update($sql, [$name, $parent_id, json_encode($custom_attributes), $id], "sisi");
    }

    public function delete($id) {
        $sql = "DELETE FROM categories WHERE id = ?";
        return $this->db->update($sql, [$id], "i");
    }
}
?>
