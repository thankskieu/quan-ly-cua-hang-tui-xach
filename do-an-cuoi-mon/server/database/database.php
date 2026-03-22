<?php
class Database {
    private $host = 'localhost';
    private $db   = 'tui_xach'; // <- chỉnh tên DB nếu khác
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';
    private $pdo = null;

    public function connect() {
        if ($this->pdo) return $this->pdo;
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            return $this->pdo;
        } catch (PDOException $e) {
            // Khi debug, show message; khi production, log thay vì die
            die("DB connection failed: " . $e->getMessage());
        }
    }
}
