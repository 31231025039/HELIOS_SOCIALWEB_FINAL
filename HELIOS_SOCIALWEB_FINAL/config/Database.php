<?php
// app/models/Database.php

class Database {
    protected $db; 

    public function __construct() {
        $host = 'localhost';
        $dbname = 'db_helios'; 
        $username = 'root';
        $password = '';

        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Lỗi kết nối CSDL: " . $e->getMessage());
        }
    }
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }
}
?>
