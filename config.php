<?php
$servername = "localhost"; // Máy chủ MySQL
$username = "root"; // Tài khoản mặc định của XAMPP
$password = ""; // Mặc định XAMPP không có mật khẩu
$dbname = "test1"; // Tên cơ sở dữ liệu

// Kết nối MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập mã hóa UTF-8 để hiển thị tiếng Việt
$conn->set_charset("utf8");
?>
