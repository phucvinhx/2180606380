<?php
include 'config.php'; // Kết nối database

// Kiểm tra nếu có ID được truyền vào từ URL
if (isset($_GET['id'])) {
    $MaSV = $_GET['id'];
    $sql = "SELECT * FROM SinhVien WHERE MaSV = '$MaSV'";
    $result = $conn->query($sql);

    // Kiểm tra nếu sinh viên tồn tại
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy sinh viên!";
        exit;
    }
} else {
    echo "Thiếu Mã Sinh Viên!";
    exit;
}

// Xử lý khi nhấn nút Cập Nhật
if (isset($_POST['update'])) {
    $HoTen = $_POST['HoTen'];
    $GioiTinh = $_POST['GioiTinh'];
    $NgaySinh = $_POST['NgaySinh'];
    $Hinh = $_POST['Hinh'];
    $MaNganh = $_POST['MaNganh'];

    $update_sql = "UPDATE SinhVien SET HoTen='$HoTen', GioiTinh='$GioiTinh', NgaySinh='$NgaySinh', Hinh='$Hinh', MaNganh='$MaNganh' WHERE MaSV='$MaSV'";
    
    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='index.php';</script>";
    } else {
        echo "Lỗi cập nhật: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Thông Tin Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center">Sửa Thông Tin Sinh Viên</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Họ Tên</label>
            <input type="text" name="HoTen" class="form-control" value="<?= $row['HoTen'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Giới Tính</label>
            <select name="GioiTinh" class="form-control">
                <option value="Nam" <?= $row['GioiTinh'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
                <option value="Nữ" <?= $row['GioiTinh'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Ngày Sinh</label>
            <input type="date" name="NgaySinh" class="form-control" value="<?= $row['NgaySinh'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Hình Ảnh (URL)</label>
            <input type="text" name="Hinh" class="form-control" value="<?= $row['Hinh'] ?>">
            <img src="<?= $row['Hinh'] ?>" class="mt-2" style="width: 100px;">
        </div>
        <div class="mb-3">
            <label class="form-label">Mã Ngành</label>
            <input type="text" name="MaNganh" class="form-control" value="<?= $row['MaNganh'] ?>" required>
        </div>
        <button type="submit" name="update" class="btn btn-success">Cập Nhật</button>
        <a href="index.php" class="btn btn-secondary">Quay Lại</a>
    </form>
</div>
</body>
</html>
