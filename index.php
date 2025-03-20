<?php
session_start(); // Bắt đầu session
include 'config.php'; // Kết nối database
// Lấy danh sách học phần
$sql_hp = "SELECT * FROM `HocPhan1`";
$result_hp = $conn->query($sql_hp);

// Kiểm tra đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Tránh SQL Injection bằng Prepared Statements
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        echo "<script>alert('Đăng nhập thành công!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Sai tài khoản hoặc mật khẩu!');</script>";
    }
    $stmt->close();
}

// Xử lý đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Tránh trùng lặp username
    $check_stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Tài khoản đã tồn tại!');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
    $check_stmt->close();
}


// Lấy danh sách sinh viên
$sql = "SELECT * FROM SinhVien";
$result = $conn->query($sql);
// Lấy danh sách học phần
$sql_hp = "SELECT * FROM HocPhan";
$result_hp = $conn->query($sql_hp);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .student-img { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; }
    </style>
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
                <li class="nav-item"><a class="nav-link" href="index.php">Sinh Viên</a></li>
                <?php if(isset($_SESSION['username'])): ?>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Đăng Xuất (<?= $_SESSION['username'] ?>)</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng Nhập</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Đăng Ký</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Đăng Xuất</a></li>
                    <td>
    <a href="dangkyhocphan.php?mahp=<?= $row['MaHP'] ?>" class="btn btn-success btn-sm">
        Đăng Ký
    </a>
</td>


                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">TRANG SINH VIÊN</h2>
    <a href="add.php" class="btn btn-primary mb-3">Thêm Sinh Viên</a>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>MaSV</th>
                <th>Họ Tên</th>
                <th>Giới Tính</th>
                <th>Ngày Sinh</th>
                <th>Hình</th>
                <th>Mã Ngành</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['MaSV'] ?></td>
                <td><?= $row['HoTen'] ?></td>
                <td><?= $row['GioiTinh'] ?></td>
                <td><?= date('d/m/Y', strtotime($row['NgaySinh'])) ?></td>
                <td><img src="<?= $row['Hinh'] ?>" class="student-img"></td>
                <td><?= $row['MaNganh'] ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['MaSV'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                    <a href="delete.php?id=<?= $row['MaSV'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa sinh viên này?')">Xóa</a>
                    <a href="details.php?id=<?= $row['MaSV'] ?>" class="btn btn-info btn-sm">Chi Tiết</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal Đăng Nhập -->
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Đăng Nhập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="text" name="username" class="form-control mb-2" placeholder="Tên đăng nhập" required>
                    <input type="password" name="password" class="form-control mb-2" placeholder="Mật khẩu" required>
                    <button type="submit" name="login" class="btn btn-primary w-100">Đăng Nhập</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Đăng Ký -->
<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Đăng Ký</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="text" name="username" class="form-control mb-2" placeholder="Tên đăng nhập" required>
                    <input type="password" name="password" class="form-control mb-2" placeholder="Mật khẩu" required>
                    <button type="submit" name="register" class="btn btn-success w-100">Đăng Ký</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Xác Nhận Đăng Xuất -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Xác Nhận Đăng Xuất</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn đăng xuất không?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <a href="logout.php" class="btn btn-danger">Đăng Xuất</a>
            </div>
        </div>
    </div>
</div>
<!-- Modal Đăng Ký Học Phần -->
<div class="modal fade" id="registerModal<?= $row['MaHP'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Đăng Ký Học Phần: <?= $row['TenHP'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="dangkyhocphan.php" method="POST">
                    <input type="hidden" name="mahp" value="<?= $row['MaHP'] ?>">
                    <input type="text" name="masv" class="form-control mb-2" placeholder="Nhập Mã Sinh Viên" required>
                    <button type="submit" class="btn btn-primary w-100">Xác Nhận Đăng Ký</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>