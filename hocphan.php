<?php
session_start();
include 'config.php'; // Kết nối database

// Kiểm tra nếu chưa đăng nhập
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Bạn cần đăng nhập để đăng ký học phần!'); window.location='index.php';</script>";
    exit();
}

// Lấy danh sách học phần
$sql_hp = "SELECT * FROM HocPhan1";
$result_hp = $conn->query($sql_hp);

// Lấy MaSV của sinh viên đăng nhập
$username = $_SESSION['username'];
$query_sv = $conn->prepare("SELECT MaSV FROM SinhVien WHERE Username = ?");
$query_sv->bind_param("s", $username);
$query_sv->execute();
$res_sv = $query_sv->get_result();
$sv_data = $res_sv->fetch_assoc();
$MaSV = $sv_data['MaSV'];

// Xử lý đăng ký học phần
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_hp'])) {
    $MaHP = $_POST['MaHP'];

    // Kiểm tra xem sinh viên đã đăng ký môn này chưa
    $check_stmt = $conn->prepare("SELECT * FROM DangKyHocPhan WHERE MaSV=? AND MaHP=?");
    $check_stmt->bind_param("ss", $MaSV, $MaHP);
    $check_stmt->execute();
    $check_res = $check_stmt->get_result();

    if ($check_res->num_rows > 0) {
        echo "<script>alert('Bạn đã đăng ký môn này!');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO DangKyHocPhan (MaSV, MaHP) VALUES (?, ?)");
        $stmt->bind_param("ss", $MaSV, $MaHP);
        if ($stmt->execute()) {
            echo "<script>alert('Đăng ký thành công!'); window.location='hocphan.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi đăng ký!');</script>";
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
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Quản Lý Sinh Viên</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Trang Chủ</a></li>
                <li class="nav-item"><a class="nav-link active" href="hocphan.php">Học Phần</a></li>
                <?php if(isset($_SESSION['username'])): ?>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Đăng Xuất (<?= $_SESSION['username'] ?>)</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Đăng Nhập</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">Đăng Ký Học Phần</h2>
    
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Mã Học Phần</th>
                <th>Tên Học Phần</th>
                <th>Số Tín Chỉ</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row_hp = $result_hp->fetch_assoc()) { ?>
            <tr>
                <td><?= $row_hp['MaHP'] ?></td>
                <td><?= $row_hp['TenHP'] ?></td>
                <td><?= $row_hp['SoTinChi'] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="MaHP" value="<?= $row_hp['MaHP'] ?>">
                        <button type="submit" name="register_hp" class="btn btn-primary btn-sm">Đăng Ký</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
