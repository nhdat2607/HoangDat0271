<?php
include 'db.php';

// Kiểm tra nếu có tham số id được truyền vào
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Chuẩn bị truy vấn SQL an toàn với prepared statement
    $stmt = $conn->prepare("DELETE FROM SinhVien WHERE MaSV = ?");
    $stmt->bind_param("s", $id); // "s" là kiểu string vì MaSV là CHAR(10)
    
    // Thực thi truy vấn
    if ($stmt->execute()) {
        header("Location: index.php?success=1");
    } else {
        header("Location: index.php?error=1");
    }

    // Đóng statement
    $stmt->close();
}

// Đóng kết nối
$conn->close();
?>
