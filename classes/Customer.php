<?php
class Customer {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($username, $password) {
        // Specifically look for 'user' role
        $sql = "SELECT id, username, password, role FROM users WHERE username = ? AND role = 'user'";
        $res = $this->db->query($sql, [$username], "s");

        if (!empty($res)) {
            $customer = $res[0];
            if (password_verify($password, $customer['password'])) {
                $_SESSION['customer_id'] = $customer['id'];
                $_SESSION['customer_username'] = $customer['username'];
                $_SESSION['customer_role'] = 'user';
                return true;
            }
        }
        return false;
    }

    public function register($username, $password, $email) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Default role is 'user' for customers
        $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')";
        return $this->db->insert($sql, [$username, $hashed_password, $email], "sss");
    }

    public function isLoggedIn() {
        return isset($_SESSION['customer_id']);
    }

    public function getSessionId() {
        return $_SESSION['customer_id'] ?? null;
    }

    public function getSessionUsername() {
        return $_SESSION['customer_username'] ?? '';
    }

    public function logout() {
        unset($_SESSION['customer_id']);
        unset($_SESSION['customer_username']);
        unset($_SESSION['customer_role']);
    }
}
?>
