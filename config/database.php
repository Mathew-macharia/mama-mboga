<?php
// config/database.php

// Load environment variables (.env) so we can read DB credentials from there
// Falls back to the original hard‑coded values if env vars are not set.
require_once __DIR__ . '/env_loader.php';

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Use .env values if present, otherwise fall back to existing local defaults
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'mama_mboga_db';
        $this->username = getenv('DB_USERNAME') ?: 'wisemann';
        $this->password = getenv('DB_PASSWORD') ?: 'nopassword';
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
