<?php
include 'config.php';
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Bạn phải đăng nhập trước!'); window.location='login.php';</script>";
    exit;
}

// Lấy danh sách học phần
$sql = "SELECT * FROM HocPhan";
$result = $conn->query($sql);

// Xử lý đăng ký học phần
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maSV = $_SESSION['username']; // Giả sử username là MaSV
    $maHP = $_POST['maHP'];

    $sqlCheck = "SELECT * FROM DangKyHocPhan WHERE MaSV='$maSV' AND MaHP='$maHP'";
    $checkResult = $conn->query($sqlCheck);

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Bạn đã đăng ký học phần này rồi!');</script>";
    } else {
        $sqlInsert = "INSERT INTO DangKyHocPhan (MaSV, MaHP) VALUES ('$maSV', '$maHP')";
        if ($conn->query($sqlInsert) === TRUE) {
            echo "<script>alert('Đăng ký thành công!'); window.location='hocphan.php';</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }
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
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Quản Lý Sinh Viên</a>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">Đăng Ký Học Phần</h2>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã HP</th>
                <th>Tên Học Phần</th>
                <th>Số Tín Chỉ</th>
                <th>Đăng Ký</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['MaHP'] ?></td>
                    <td><?= $row['TenHP'] ?></td>
                    <td><?= $row['SoTinChi'] ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="maHP" value="<?= $row['MaHP'] ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Đăng Ký</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
