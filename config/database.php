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
        // Use .env values if present, otherwise fall back to existing local defaults.
        // Support both DB_USERNAME/DB_PASSWORD and DB_USER/DB_PASS naming.
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'mama_mboga_db';

        // Username: try DB_USERNAME, then DB_USER, then fallback
        $this->username = getenv('DB_USERNAME')
            ?: (getenv('DB_USER') ?: 'wiseman');

        // Password: try DB_PASSWORD, then DB_PASS, then fallback
        $this->password = getenv('DB_PASSWORD')
            ?: (getenv('DB_PASS') ?: 'nopassword');
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
