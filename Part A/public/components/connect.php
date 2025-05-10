<?php
$host = '127.0.0.1';
$dbname = 'shop_db';
$username = 'root';       // 先用root用户测试
$password = '';           // root用户默认密码为空

try {
    $connect = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
    
    function create_unique_id() {
        $charecters = 
            "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWSYZ";
        $random = "";
        for($i = 0; $i < 20; $i++)
            $random .= $charecters[mt_rand(0, strlen($charecters) - 1)];
        return $random;
    }
?>
ALTER TABLE orders 
ADD COLUMN date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 
COMMENT 'creation time';