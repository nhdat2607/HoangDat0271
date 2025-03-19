<?php
include 'db.php';
$id = $_GET['id'];
$sql = "SELECT * FROM SinhVien WHERE MaSV='$id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $HoTen = $_POST['HoTen'];
    $GioiTinh = $_POST['GioiTinh'];
    $NgaySinh = $_POST['NgaySinh'];
    $MaNganh = $_POST['MaNganh'];

    $sql = "UPDATE SinhVien SET HoTen='$HoTen', GioiTinh='$GioiTinh', NgaySinh='$NgaySinh', MaNganh='$MaNganh' WHERE MaSV='$id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<form method="post">
    <input type="text" name="HoTen" value="<?= $row['HoTen'] ?>" required><br>
    <select name="GioiTinh">
        <option value="Nam" <?= $row['GioiTinh'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
        <option value="Nữ" <?= $row['GioiTinh'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
    </select><br>
    <input type="date" name="NgaySinh" value="<?= $row['NgaySinh'] ?>" required><br>
    <input type="text" name="MaNganh" value="<?= $row['MaNganh'] ?>" required><br>
    <button type="submit">Cập nhật</button>
</form>
