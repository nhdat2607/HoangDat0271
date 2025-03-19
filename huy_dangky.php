<?php
session_start();
include 'db.php';

// Kiểm tra nếu sinh viên chưa đăng nhập
if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['MaHP'])) {
    $MaSV = $_SESSION['MaSV'];
    $MaHP = $_POST['MaHP'];

    // Xóa học phần đã đăng ký
    $sql = "DELETE FROM DangKyHocPhan WHERE MaSV = ? AND MaHP = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $MaSV, $MaHP);

    if ($stmt->execute()) {
        header("Location: xem_dangky.php?status=success");
        exit();
    } else {
        header("Location: xem_dangky.php?status=error");
        exit();
    }
}

$conn->close();
?>
