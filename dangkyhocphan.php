<?php
session_start();
include 'config.php'; // Kết nối database

if (!isset($_GET['mahp'])) {
    echo "<script>alert('Không có học phần nào được chọn!'); window.location='index.php';</script>";
    exit();
}

$mahp = $_GET['mahp'];

// Lấy thông tin học phần
$sql_hp = "SELECT * FROM HocPhan WHERE MaHP = ?";
$stmt = $conn->prepare($sql_hp);
$stmt->bind_param("s", $mahp);
$stmt->execute();
$result = $stmt->get_result();
$hocphan = $result->fetch_assoc();

if (!$hocphan) {
    echo "<script>alert('Học phần không tồn tại!'); window.location='index.php';</script>";
    exit();
}

// Xử lý khi sinh viên gửi form đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $masv = $_POST['masv'];

    // Kiểm tra xem sinh viên đã đăng ký học phần này chưa
    $check_stmt = $conn->prepare("SELECT * FROM DangKyHocPhan WHERE MaSV = ? AND MaHP = ?");
    $check_stmt->bind_param("ss", $masv, $mahp);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Bạn đã đăng ký học phần này rồi!'); window.location='index.php';</script>";
    } else {
        // Thêm vào bảng đăng ký
        $stmt = $conn->prepare("INSERT INTO DangKyHocPhan (MaSV, MaHP) VALUES (?, ?)");
        $stmt->bind_param("ss", $masv, $mahp);
        if ($stmt->execute()) {
            echo "<script>alert('Đăng ký thành công!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Học Phần</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center">ĐĂNG KÝ HỌC PHẦN</h2>
    <div class="card p-4">
        <h4>Học Phần: <?= $hocphan['TenHP'] ?> (<?= $hocphan['SoTinChi'] ?> tín chỉ)</h4>
        <form method="POST">
            <div class="mb-3">
                <label for="masv" class="form-label">Mã Sinh Viên:</label>
                <input type="text" name="masv" class="form-control" placeholder="Nhập mã sinh viên" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Xác Nhận Đăng Ký</button>
        </form>
    </div>
</div>
</body>
</html>
