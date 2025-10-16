<?php
// app/models/BaseModel.php

require_once __DIR__ . '/../../config/connections.php';

class BaseModel {
    protected $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Fungsi umum untuk query
    protected function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);

        if ($params) {
            // Buat tipe data parameter (semua string by default)
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt;
    }

    // Fungsi umum untuk ambil banyak data
    protected function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Fungsi umum untuk ambil satu data
    protected function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>
