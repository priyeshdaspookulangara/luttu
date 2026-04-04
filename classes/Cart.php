<?php
class Cart {
    public function __construct() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function add($product_id, $quantity = 1) {
        $product_id = (int)$product_id;
        $quantity = (int)$quantity;
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    public function remove($product_id) {
        unset($_SESSION['cart'][$product_id]);
    }

    public function update($product_id, $quantity) {
        $product_id = (int)$product_id;
        $quantity = (int)$quantity;
        if ($quantity <= 0) {
            $this->remove($product_id);
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    public function getItems() {
        return $_SESSION['cart'];
    }

    public function clear() {
        $_SESSION['cart'] = [];
    }

    public function getTotalCount() {
        return array_sum($_SESSION['cart']);
    }
}
?>
