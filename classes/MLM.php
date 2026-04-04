<?php
class MLM {
    private $db;
    private $wallet;

    // Gold Plan: 5 Levels
    // L1: 20%, L2: 10%, L3: 5%, L4: 3%, L5: 2%
    private $gold_plan_levels = [
        1 => 20.0,
        2 => 10.0,
        3 => 5.0,
        4 => 3.0,
        5 => 2.0
    ];

    public function __construct($db) {
        $this->db = $db;
        if (!class_exists('Wallet')) {
            require_once __DIR__ . '/Wallet.php';
        }
        $this->wallet = new Wallet($db);
    }

    public function distributeCommission($buyer_id, $order_id, $base_pv) {
        $buyer = $this->getUserById($buyer_id);
        if (!$buyer || !$buyer['sponsor_id']) {
            return; // No sponsor, no multi-level commission
        }

        $buyer_username = $buyer['username'] ?? 'User #'.$buyer_id;
        $current_user_id = $buyer['sponsor_id'];
        $level = 1;

        while ($current_user_id && $level <= 5) {
            $percentage = $this->gold_plan_levels[$level] ?? 0;
            if ($percentage <= 0) break;

            $commission_pv = ($base_pv * $percentage) / 100;

            if ($commission_pv > 0) {
                // Record in commissions table
                $sql = "INSERT INTO commissions (user_id, order_id, buyer_id, level, amount_pv, percentage)
                        VALUES (?, ?, ?, ?, ?, ?)";
                $this->db->insert($sql, [$current_user_id, $order_id, $buyer_id, $level, $commission_pv, $percentage], "isisdd");

                // Credit PV to user's wallet
                $description = "Level $level MLM Commission from Order $order_id (Buyer: $buyer_username)";
                $this->wallet->addTransaction($current_user_id, $commission_pv, 'credit', $description, $order_id);
            }

            // Move up to the next sponsor
            $sponsor = $this->getUserById($current_user_id);
            $current_user_id = $sponsor ? $sponsor['sponsor_id'] : null;
            $level++;
        }
    }

    private function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $res = $this->db->query($sql, [$id], "i");
        return !empty($res) ? $res[0] : null;
    }

    public function getDownline($user_id, $level = 1, $max_level = 5) {
        if ($level > $max_level) return [];

        $sql = "SELECT id, username, member_code, created_at FROM users WHERE sponsor_id = ?";
        $members = $this->db->query($sql, [$user_id], "i");

        foreach ($members as &$member) {
            $member['level'] = $level;
            $member['children'] = $this->getDownline($member['id'], $level + 1, $max_level);
        }

        return $members;
    }

    public function getCommissions($user_id) {
        $sql = "SELECT c.*, u.username as buyer_username
                FROM commissions c
                JOIN users u ON c.buyer_id = u.id
                WHERE c.user_id = ?
                ORDER BY c.created_at DESC";
        return $this->db->query($sql, [$user_id], "i");
    }
}
?>
