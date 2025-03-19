<?php
include 'db.php'; // Kết nối database

// Kiểm tra nếu có tham số id trên URL
if (isset($_GET['id'])) {
    $MaSV = $_GET['id'];

    // Truy vấn lấy thông tin sinh viên theo MaSV
    $sql = "SELECT * FROM SinhVien WHERE MaSV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $MaSV);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $sinhvien = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy sinh viên!";
        exit();
    }
} else {
    echo "Thiếu ID sinh viên!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết Sinh Viên</title>
</head>
<body>
    <h2>Thông tin Sinh Viên</h2>
    <p><strong>Mã SV:</strong> <?php echo $sinhvien['MaSV']; ?></p>
    <p><strong>Họ và Tên:</strong> <?php echo $sinhvien['HoTen']; ?></p>
    <p><strong>Giới tính:</strong> <?php echo $sinhvien['GioiTinh']; ?></p>
    <p><strong>Ngày sinh:</strong> <?php echo $sinhvien['NgaySinh']; ?></p>
    <p><strong>Mã Ngành:</strong> <?php echo $sinhvien['MaNganh']; ?></p>
    <p><strong>Hình ảnh:</strong></p>
    <img src="uploads/<?php echo $sinhvien['Hinh']; ?>" alt="Hình ảnh sinh viên" width="150">

    <br><br>
    <a href="index.php">Quay lại danh sách</a>
</body>
</html>
