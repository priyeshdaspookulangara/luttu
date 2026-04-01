<?php
class Order {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($user_id, $product_id, $order_id, $price, $pv_earned) {
        $sql = "INSERT INTO orders (user_id, product_id, order_id, price, pv_earned) VALUES (?, ?, ?, ?, ?)";
        return $this->db->insert($sql, [$user_id, $product_id, $order_id, $price, $pv_earned], "iisdd");
    }

    public function getByUserId($user_id) {
        $sql = "SELECT o.*, p.name as product_name
                FROM orders o
                JOIN products p ON o.product_id = p.id
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC";
        return $this->db->query($sql, [$user_id], "i");
    }
}
?>
