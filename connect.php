<?php
$servername = "switchyard.proxy.rlwy.net";
$username = "root";
$password = "RiNlrwbGhjFxiyLWxMZJbZDlvGCpKjBt";
$dbname = "railway";
$port = 41855;

// إنشاء الاتصال مع إضافة المنفذ (Port)
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>