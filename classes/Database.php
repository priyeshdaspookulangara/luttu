<?php
class Database {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function query($sql, $params = [], $types = "") {
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $this->conn->error . " SQL: " . $sql);
        }

        if (!empty($params)) {
            if (empty($types)) {
                $types = str_repeat('s', count($params));
            }
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $data = [];
        if ($result && is_object($result)) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
        }

        $stmt->close();
        return $data; // Always returns an ARRAY of associative arrays
    }

    public function insert($sql, $params = [], $types = "") {
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $this->conn->error);
        }

        if (!empty($params)) {
            if (empty($types)) {
                $types = str_repeat('s', count($params));
            }
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }

        $insert_id = $stmt->insert_id;
        $stmt->close();
        return $insert_id;
    }

    public function update($sql, $params = [], $types = "") {
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $this->conn->error);
        }

        if (!empty($params)) {
            if (empty($types)) {
                $types = str_repeat('s', count($params));
            }
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }

        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $affected_rows >= 0;
    }
}
?>
