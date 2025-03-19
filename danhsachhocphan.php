<?php
session_start();
include 'db.php';

// Kiểm tra nếu sinh viên chưa đăng nhập
if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit();
}

$MaSV = $_SESSION['MaSV'];

// Lấy danh sách học phần mà sinh viên đã đăng ký
$sql = "SELECT hp.MaHP, hp.TenHP, dk.SoChi 
        FROM DangKyHocPhan dk 
        INNER JOIN HocPhan hp ON dk.MaHP = hp.MaHP 
        WHERE dk.MaSV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $MaSV);
$stmt->execute();
$result = $stmt->get_result();
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

    <?php if ($result->num_rows > 0): ?>
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
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['MaHP']) ?></td>
                        <td><?= htmlspecialchars($row['TenHP']) ?></td>
                        <td><?= htmlspecialchars($row['SoChi']) ?></td>
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
    <?php else: ?>
        <p class="text-center text-danger">Bạn chưa đăng ký học phần nào.</p>
    <?php endif; ?>

    <a href="dangky.php" class="btn btn-primary">Quay lại đăng ký</a>
</div>
</body>
</html>

<?php $conn->close(); ?>
