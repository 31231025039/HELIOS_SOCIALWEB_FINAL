<?php
// app/models/Database.php

class Database {
    // Phải dùng 'protected' để các Model con kế thừa và sử dụng được biến này
    protected $db; 

    public function __construct() {
        $host = 'sql300.infinityfree.com';
        $dbname = 'if0_42151250_helios'; 
        $username = 'if0_42151250';
        $password = 'pE2d4nzSdxGD1';

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
