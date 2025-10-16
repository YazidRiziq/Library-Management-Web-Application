<?php
// config/connections.php

class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "librix";
    public $conn;

    public function __construct() {
        $this->connectDatabase();
    }

    private function connectDatabase() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->conn->connect_error) {
            die("Database Connection Failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>