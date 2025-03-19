<?php
session_start();
include 'db.php';

// Kiểm tra nếu sinh viên chưa đăng nhập
if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit();
}

$MaSV = $_SESSION['MaSV'];
$error = "";
$success = "";

// Xử lý lưu thông tin đăng ký
if (isset($_POST['save_all'])) {
    // Xóa dữ liệu cũ của sinh viên (tránh trùng lặp)
    $delete_old = "DELETE FROM LuuDangKy WHERE MaSV = ?";
    $stmt = $conn->prepare($delete_old);
    $stmt->bind_param("s", $MaSV);
    $stmt->execute();
    $stmt->close();

    // Lấy danh sách học phần đã đăng ký
    $sql = "SELECT hp.MaHP, hp.TenHP, dk.SoTinChi FROM DangKyHocPhan dk 
            INNER JOIN HocPhan hp ON dk.MaHP = hp.MaHP 
            WHERE dk.MaSV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $MaSV);
    $stmt->execute();
    $result = $stmt->get_result();

    // Thêm dữ liệu vào bảng LuuDangKy
    $insert_sql = "INSERT INTO LuuDangKy (MaSV, MaHP, TenHP, SoChi) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_sql);

    while ($row = $result->fetch_assoc()) {
        $stmt_insert->bind_param("sssi", $MaSV, $row['MaHP'], $row['TenHP'], $row['SoChi']);
        $stmt_insert->execute();
    }

    $stmt_insert->close();
    $success = "Thông tin đăng ký đã được lưu!";
}

// Lấy danh sách học phần đã đăng ký
$sql = "SELECT hp.MaHP, hp.TenHP, hp.SoTinChi FROM DangKyHocPhan dk 
        INNER JOIN HocPhan hp ON dk.MaHP = hp.MaHP 
        WHERE dk.MaSV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $MaSV);
$stmt->execute();
$result = $stmt->get_result();

$totalCourses = $result->num_rows;
$totalCredits = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Học Phần Đã Đăng Ký</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Học Phần Đã Đăng Ký</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($totalCourses > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã HP</th>
                    <th>Tên Học Phần</th>
                    <th>Số Chỉ</th>
                    <th>Hủy Đăng Ký</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { 
                    $totalCredits += $row['SoTinChi']; ?>
                    <tr>
                        <td><?= htmlspecialchars($row['MaHP']) ?></td>
                        <td><?= htmlspecialchars($row['TenHP']) ?></td>
                        <td><?= htmlspecialchars($row['SoTinChi']) ?></td>
                        <td>
                            <form method="post" action="huy_dangky.php">
                                <input type="hidden" name="MaHP" value="<?= htmlspecialchars($row['MaHP']) ?>">
                                <button type="submit" class="btn btn-danger">Hủy</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p><strong>Số lượng học phần:</strong> <?= $totalCourses ?></p>
        <p><strong>Tổng số tín chỉ:</strong> <?= $totalCredits ?></p>
        
        <form method="post">
            <button type="submit" name="save_all" class="btn btn-success">Lưu Đăng Ký</button>
            <button type="submit" name="delete_all" class="btn btn-warning">Xóa tất cả</button>
        </form>
    <?php else: ?>
        <p class="text-center text-danger">Bạn chưa đăng ký học phần nào.</p>
    <?php endif; ?>
    
    <a href="dangky.php" class="btn btn-primary">Quay lại đăng ký</a>
</div>
</body>
</html>

<?php $conn->close(); ?>
