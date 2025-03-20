<?php
include "config.php"; // Kết nối database

// Gán giá trị mặc định để tránh lỗi Undefined array key
$row = [
    'MaSV' => '',
    'HoTen' => '',
    'GioiTinh' => 'Nam',
    'NgaySinh' => '',
    'Hinh' => '',
    'MaNganh' => ''
];

// Kiểm tra nếu có MaSV trong URL
if (isset($_GET["id"])) {
    $MaSV = mysqli_real_escape_string($conn, $_GET["id"]);

    // Truy vấn lấy thông tin sinh viên
    $sql = "SELECT MaSV, HoTen, GioiTinh, NgaySinh, Hinh, IFNULL(MaNganh, '') AS MaNganh FROM SinhVien WHERE MaSV = '$MaSV'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        echo "<script>alert('Không tìm thấy sinh viên!'); window.location='index.php';</script>";
        exit();
    }
}

// Xử lý cập nhật sinh viên khi nhấn nút Save
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $MaSV = mysqli_real_escape_string($conn, $_POST["MaSV"]);
    $HoTen = mysqli_real_escape_string($conn, $_POST["HoTen"]);
    $GioiTinh = mysqli_real_escape_string($conn, $_POST["GioiTinh"]);
    $NgaySinh = mysqli_real_escape_string($conn, $_POST["NgaySinh"]);
    $MaNganh = isset($_POST["MaNganh"]) ? mysqli_real_escape_string($conn, $_POST["MaNganh"]) : NULL;

    // Kiểm tra nếu MaNganh có tồn tại trong bảng nganhhoc
    if (!empty($MaNganh)) {
        $check_sql = "SELECT MaNganh FROM nganhhoc WHERE MaNganh = '$MaNganh'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows == 0) {
            echo "<script>alert('Lỗi: Mã ngành không hợp lệ!');</script>";
            exit();
        }
    }

    // Xử lý upload ảnh mới nếu có
    if (!empty($_FILES["Hinh"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["Hinh"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowedTypes)) {
            move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file);
            $Hinh = $target_file;
        } else {
            echo "<script>alert('Chỉ chấp nhận file ảnh JPG, JPEG, PNG, GIF!');</script>";
            $Hinh = $_POST["HinhCu"]; // Giữ ảnh cũ nếu có lỗi upload
        }
    } else {
        $Hinh = $_POST["HinhCu"]; // Giữ ảnh cũ nếu không chọn ảnh mới
    }

    // Cập nhật thông tin sinh viên
    $sql = "UPDATE SinhVien SET 
            HoTen='$HoTen', 
            GioiTinh='$GioiTinh', 
            NgaySinh='$NgaySinh', 
            Hinh='$Hinh', 
            MaNganh=" . ($MaNganh ? "'$MaNganh'" : "NULL") . " 
            WHERE MaSV='$MaSV'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Cập nhật thành công!'); window.location='index.php';</script>";
        exit();
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
    <title>Sửa Sinh Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <!-- Thanh menu -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Test1</a>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php">Sinh Viên</a></li>
                    <li class="nav-item"><a class="nav-link" href="hocphan.php">Học Phần</a></li>
                    <li class="nav-item"><a class="nav-link" href="dangky.php">Đăng Ký</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Đăng Nhập</a></li>
                </ul>
            </div>
        </nav>

        <h2 class="mt-3 text-center">Hiệu chỉnh thông tin sinh viên</h2>
        <form method="POST" enctype="multipart/form-data" class="w-50 mx-auto border p-4 shadow">
            <input type="hidden" name="MaSV" value="<?= htmlspecialchars($row['MaSV']) ?>">

            <div class="mb-3">
                <label class="form-label">Họ Tên</label>
                <input type="text" name="HoTen" class="form-control" value="<?= htmlspecialchars($row['HoTen']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Giới Tính</label>
                <select name="GioiTinh" class="form-control">
                    <option value="Nam" <?= ($row['GioiTinh'] == 'Nam') ? 'selected' : '' ?>>Nam</option>
                    <option value="Nữ" <?= ($row['GioiTinh'] == 'Nữ') ? 'selected' : '' ?>>Nữ</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Ngày Sinh</label>
                <input type="date" name="NgaySinh" class="form-control" value="<?= htmlspecialchars($row['NgaySinh']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Hình</label>
                <input type="file" name="Hinh" class="form-control">
                <input type="hidden" name="HinhCu" value="<?= htmlspecialchars($row['Hinh']) ?>">
                <br>
                <?php if (!empty($row['Hinh'])): ?>
                    <img src="<?= htmlspecialchars($row['Hinh']) ?>" alt="Ảnh sinh viên" class="img-thumbnail" width="150">
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Ngành Học</label>
                <select name="MaNganh" class="form-control">
                    <option value="">-- Chọn ngành học --</option>
                    <?php
                    $nganh_sql = "SELECT MaNganh, TenNganh FROM nganhhoc";
                    $nganh_result = $conn->query($nganh_sql);
