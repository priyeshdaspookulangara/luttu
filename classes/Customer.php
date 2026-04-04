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

    public function register($username, $password, $email, $sponsor_code = null) {
        $sponsor_id = null;
        if ($sponsor_code) {
            $sql = "SELECT id FROM users WHERE member_code = ?";
            $res = $this->db->query($sql, [$sponsor_code], "s");
            if (empty($res)) {
                throw new Exception("Invalid Sponsor Code.");
            }
            $sponsor_id = $res[0]['id'];
        }

        $member_code = $this->generateMemberCode();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (member_code, sponsor_id, username, password, email, role) VALUES (?, ?, ?, ?, ?, 'user')";
        return $this->db->insert($sql, [$member_code, $sponsor_id, $username, $hashed_password, $email], "sisss");
    }

    private function generateMemberCode() {
        $exists = true;
        $code = '';
        while ($exists) {
            $code = 'MB' . mt_rand(100000, 999999);
            $sql = "SELECT id FROM users WHERE member_code = ?";
            $res = $this->db->query($sql, [$code], "s");
            if (empty($res)) {
                $exists = false;
            }
        }
        return $code;
    }

    public function getByMemberCode($code) {
        $sql = "SELECT * FROM users WHERE member_code = ?";
        $res = $this->db->query($sql, [$code], "s");
        return !empty($res) ? $res[0] : null;
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
