<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $MaSV = trim($_POST['MaSV']);
    $password = trim($_POST['password']);

    if (empty($MaSV) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        // Truy vấn SQL
        $stmt = $conn->prepare("SELECT MaSV, HoTen, MatKhau FROM SinhVien WHERE MaSV = ?");
        $stmt->bind_param("s", $MaSV);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc(); // Lấy dữ liệu từ CSDL
        $stmt->close();

        if ($user) { // Kiểm tra nếu có sinh viên tồn tại
            if ($password === $user['MatKhau']) { // Kiểm tra trực tiếp
                $_SESSION['MaSV'] = $user['MaSV'];
                $_SESSION['HoTen'] = $user['HoTen'];
                echo "<script>alert('Đăng nhập thành công!'); window.location='hocphan.php';</script>";
                exit();
            }
        }
        $error = "Sai thông tin đăng nhập!";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Đăng Nhập</h2>
    <form method="post">
        <div class="mb-3">
            <label for="MaSV" class="form-label">Mã Sinh Viên:</label>
            <input type="text" class="form-control" name="MaSV" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu:</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <?php if (!empty($error)) echo "<p class='text-danger'>$error</p>"; ?>
        <button type="submit" class="btn btn-primary">Đăng Nhập</button>
    </form>
</div>
</body>
</html>
