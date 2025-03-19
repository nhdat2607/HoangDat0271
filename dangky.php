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

// Xử lý đăng ký học phần
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['MaHP'])) {
    $MaHP = trim($_POST['MaHP']);

    // Kiểm tra học phần đã đăng ký chưa
    $check_sql = "SELECT 1 FROM DangKyHocPhan WHERE MaSV = ? AND MaHP = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $MaSV, $MaHP);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Bạn đã đăng ký học phần này rồi!";
    } else {
        // Lấy thông tin số chỉ của học phần
        $get_hp_sql = "SELECT SoChi FROM HocPhan WHERE MaHP = ?";
        $stmt = $conn->prepare($get_hp_sql);
        $stmt->bind_param("s", $MaHP);
        $stmt->execute();
        $hp_result = $stmt->get_result();
        
        if ($hp_result->num_rows > 0) {
            $hp_row = $hp_result->fetch_assoc();
            $SoChi = $hp_row['SoChi'] ?? 0;

            // Thêm học phần vào bảng DangKyHocPhan
            $insert_sql = "INSERT INTO DangKyHocPhan (MaSV, MaHP, SoChi) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssi", $MaSV, $MaHP, $SoChi);

            if ($stmt->execute()) {
                $success = "Đăng ký thành công!";
            } else {
                $error = "Lỗi khi đăng ký học phần!";
            }
        } else {
            $error = "Học phần không tồn tại!";
        }
    }
    $stmt->close();
}

// Lấy danh sách học phần có sẵn
$sql = "SELECT * FROM HocPhan";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký Học Phần</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Đăng Ký Học Phần</h2>

    <!-- Hiển thị thông báo -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã HP</th>
                    <th>Tên Học Phần</th>
                    <th>Số Chỉ</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['MaHP']) ?></td>
                        <td><?= htmlspecialchars($row['TenHP']) ?></td>
                        <td><?= !empty($row['SoChi']) ? htmlspecialchars($row['SoChi']) : 'Chưa cập nhật' ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="MaHP" value="<?= htmlspecialchars($row['MaHP']) ?>">
                                <button type="submit" class="btn btn-primary">Đăng Ký</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center text-danger">Không có học phần nào để đăng ký.</p>
    <?php endif; ?>

    <a href="xem_dangky.php" class="btn btn-success">Xem Học Phần Đã Đăng Ký</a>
</div>
</body>
</html>

<?php $conn->close(); ?>
