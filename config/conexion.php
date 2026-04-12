<?php
class Database {
    private $host = "localhost";
    private $db_name = "guevaInfo";
    private $username = "root";
    private $password = "";

    public function getConnection() {
        try {
            return new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username,
                $this->password
            );
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}