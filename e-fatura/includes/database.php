<?php
// Veritabanı Bağlantı Sınıfı

require_once 'config.php';

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn = null;

    // Veritabanı Bağlantısı
    public function connect() {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            return $this->conn;
        } catch(PDOException $e) {
            error_log("Bağlantı Hatası: " . $e->getMessage());
            throw new Exception("Veritabanı bağlantı hatası");
        }
    }

    // Bağlantıyı Kapat
    public function disconnect() {
        $this->conn = null;
    }

    // Transaction Başlat
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    // Commit
    public function commit() {
        return $this->conn->commit();
    }

    // Rollback
    public function rollBack() {
        return $this->conn->rollBack();
    }

    // Son Eklenen ID
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}
