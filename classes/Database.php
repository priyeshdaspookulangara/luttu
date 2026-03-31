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

        if ($params) {
            if (empty($types)) {
                $types = "";
                foreach ($params as $param) {
                    if (is_int($param)) $types .= "i";
                    elseif (is_double($param)) $types .= "d";
                    else $types .= "s";
                }
            }
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }

        $result = $stmt->get_result();

        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
        }

        $stmt->close();
        return $data; // Now always returns an ARRAY
    }

    public function insert($sql, $params = [], $types = "") {
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $this->conn->error);
        }

        if ($params) {
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

        if ($params) {
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
