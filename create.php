<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $MaSV = $_POST['MaSV'];
    $HoTen = $_POST['HoTen'];
    $GioiTinh = $_POST['GioiTinh'];
    $NgaySinh = $_POST['NgaySinh'];
    $MaNganh = $_POST['MaNganh'];

    // Thư mục lưu ảnh
    $target_dir = "uploads/";

    // Kiểm tra và xử lý hình ảnh
    $Hinh = basename($_FILES["Hinh"]["name"]);
    $target_file = $target_dir . $Hinh;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra file có phải là ảnh không
    $check = getimagesize($_FILES["Hinh"]["tmp_name"]);
    if ($check === false) {
        die("File không phải là hình ảnh.");
    }

    // Giới hạn kích thước file (dưới 2MB)
    if ($_FILES["Hinh"]["size"] > 2 * 1024 * 1024) {
        die("Dung lượng ảnh quá lớn (tối đa 2MB).");
    }

    // Chỉ cho phép định dạng JPG, PNG, GIF
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_types)) {
        die("Chỉ hỗ trợ định dạng JPG, JPEG, PNG, GIF.");
    }

    // Kiểm tra và di chuyển file tải lên
    if (!move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file)) {
        die("Lỗi khi tải ảnh lên.");
    }

    // Chuẩn bị câu lệnh SQL an toàn
    $sql = "INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $MaSV, $HoTen, $GioiTinh, $NgaySinh, $Hinh, $MaNganh);

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit();
    } else {
        echo "Lỗi: " . $conn->error;
    }

    // Đóng statement và kết nối
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sinh Viên</title>
</head>
<body>
    <h2>Thêm Sinh Viên</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="MaSV" placeholder="Mã sinh viên" required><br>
        <input type="text" name="HoTen" placeholder="Họ và tên" required><br>
        <select name="GioiTinh">
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
        </select><br>
        <input type="date" name="NgaySinh" required><br>
        <input type="file" name="Hinh" accept=".jpg,.jpeg,.png,.gif" required><br>
        <input type="text" name="MaNganh" placeholder="Mã Ngành" required><br>
        <button type="submit">Thêm</button>
    </form>
</body>
</html>
