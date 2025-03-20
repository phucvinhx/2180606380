<?php
include 'config.php';

if (isset($_GET['id'])) {
    $maHP = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM HocPhan WHERE MaHP = ?");
    $stmt->bind_param("s", $maHP);

    if ($stmt->execute()) {
        echo "<script>alert('Xóa học phần thành công!'); window.location='hocphan.php';</script>";
    } else {
        echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
    }
    $stmt->close();
}
?>
