<?php
include 'config.php'; // Kết nối database

// Kiểm tra xem có ID được truyền vào từ URL không
if (isset($_GET['id'])) {
    $MaSV = $_GET['id'];
    $sql = "SELECT * FROM SinhVien WHERE MaSV = '$MaSV'";
    $result = $conn->query($sql);

    // Kiểm tra nếu tìm thấy sinh viên
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<script>alert('Không tìm thấy sinh viên!'); window.location='index.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Thiếu mã sinh viên!'); window.location='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .student-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Quản Lý Sinh Viên</a>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Chi Tiết Sinh Viên</h2>
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-header text-center">
            <h4><?= $row['HoTen'] ?></h4>
        </div>
        <div class="card-body text-center">
            <img src="<?= $row['Hinh'] ?>" class="student-img mb-3" alt="Ảnh Sinh Viên">
            <p><strong>Mã Sinh Viên:</strong> <?= $row['MaSV'] ?></p>
            <p><strong>Giới Tính:</strong> <?= $row['GioiTinh'] ?></p>
            <p><strong>Ngày Sinh:</strong> <?= date("d/m/Y", strtotime($row['NgaySinh'])) ?></p>
            <p><strong>Mã Ngành:</strong> <?= $row['MaNganh'] ?></p>
        </div>
        <div class="card-footer text-center">
            <a href="index.php" class="btn btn-primary">Quay Lại</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
