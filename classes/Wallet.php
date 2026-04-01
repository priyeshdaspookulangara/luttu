<?php
class Wallet {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getBalance($user_id) {
        $sql = "SELECT SUM(CASE WHEN transaction_type = 'credit' THEN amount_pv ELSE -amount_pv END) as balance
                FROM wallet_transactions WHERE user_id = ?";
        $res = $this->db->query($sql, [$user_id], "i");
        return !empty($res) ? ($res[0]['balance'] ?? 0) : 0;
    }

    public function addTransaction($user_id, $amount_pv, $type, $description, $reference_id = null) {
        $sql = "INSERT INTO wallet_transactions (user_id, amount_pv, transaction_type, description, reference_id)
                VALUES (?, ?, ?, ?, ?)";
        return $this->db->insert($sql, [$user_id, $amount_pv, $type, $description, $reference_id], "idsss");
    }

    public function getTransactions($user_id) {
        $sql = "SELECT * FROM wallet_transactions WHERE user_id = ? ORDER BY created_at DESC";
        return $this->db->query($sql, [$user_id], "i"); // Returns array
    }

    public function getCurrentPVSettings() {
        $sql = "SELECT * FROM pv_settings WHERE effective_from <= CURRENT_DATE ORDER BY effective_from DESC LIMIT 1";
        $res = $this->db->query($sql);
        return !empty($res) ? $res[0] : null;
    }

    public function createWithdrawalRequest($user_id, $amount_pv) {
        $settings = $this->getCurrentPVSettings();
        if (!$settings) return false;

        $balance = $this->getBalance($user_id);
        if ($balance < $amount_pv || $amount_pv < $settings['min_withdrawal']) {
            return false;
        }

        $amount_cash = $amount_pv * $settings['cash_per_pv'];
        $sql = "INSERT INTO withdrawal_requests (user_id, amount_pv, amount_cash) VALUES (?, ?, ?)";
        $withdrawal_id = $this->db->insert($sql, [$user_id, $amount_pv, $amount_cash], "idd");

        if ($withdrawal_id) {
            $this->addTransaction($user_id, $amount_pv, 'debit', 'Withdrawal Request', $withdrawal_id);
            return $withdrawal_id;
        }
        return false;
    }

    public function getWithdrawalRequests($user_id = null) {
        if ($user_id) {
            $sql = "SELECT * FROM withdrawal_requests WHERE user_id = ? ORDER BY requested_at DESC";
            return $this->db->query($sql, [$user_id], "i");
        } else {
            $sql = "SELECT w.*, u.username FROM withdrawal_requests w JOIN users u ON w.user_id = u.id ORDER BY requested_at DESC";
            return $this->db->query($sql);
        }
    }

    public function updateWithdrawalStatus($id, $status) {
        $sql = "UPDATE withdrawal_requests SET status = ?, processed_at = CURRENT_TIMESTAMP WHERE id = ?";
        $result = $this->db->update($sql, [$status, $id], "si");

        if ($status === 'rejected') {
            $sql = "SELECT user_id, amount_pv FROM withdrawal_requests WHERE id = ?";
            $res = $this->db->query($sql, [$id], "i");
            if (!empty($res)) {
                $req = $res[0];
                $this->addTransaction($req['user_id'], $req['amount_pv'], 'credit', 'Withdrawal Refunded', $id);
            }
        }
        return $result;
    }
}
?>
