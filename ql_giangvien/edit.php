<?php
include 'db.php';

$old_id = isset($_GET['id']) ? trim($_GET['id']) : '';

$stmt = $conn->prepare("SELECT * FROM GiangVien WHERE IDGV = ?");
$stmt->bind_param("s", $old_id);
$stmt->execute();
$res = $stmt->get_result();

if (!$res || $res->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$gv = $res->fetch_assoc();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_idgv = trim($_POST['idgv']);
    $fullname = trim($_POST['fullname']);
    $sumclass = intval($_POST['sumclass']);
    $imageName = $gv['Image'];

    if (empty($new_idgv) || empty($fullname)) {
        $error = "Vui lòng nhập Mã giảng viên và Họ tên.";
    } else {
        if ($new_idgv !== $old_id) {
            $stmt_check = $conn->prepare("SELECT IDGV FROM GiangVien WHERE IDGV = ?");
            $stmt_check->bind_param("s", $new_idgv);
            $stmt_check->execute();
            if ($stmt_check->get_result()->num_rows > 0) {
                $error = "Mã giảng viên '$new_idgv' đã tồn tại!";
            }
        }

        if (empty($error)) {
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $newImageName = time() . '_' . uniqid() . '.' . $ext;
                
                if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
                if (move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $newImageName)) {
                    if (!empty($gv['Image']) && file_exists("uploads/" . $gv['Image'])) {
                        unlink("uploads/" . $gv['Image']);
                    }
                    $imageName = $newImageName;
                }
            }

            $stmt_update = $conn->prepare("UPDATE GiangVien SET IDGV = ?, Fullname = ?, Image = ?, SumClass = ? WHERE IDGV = ?");
            $stmt_update->bind_param("sssis", $new_idgv, $fullname, $imageName, $sumclass, $old_id);
            
            if ($stmt_update->execute()) {
                header("Location: index.php");
                exit();
            } else {
                $error = "Có lỗi xảy ra: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Giảng Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Cập Nhật Thông Tin Giảng Viên</h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Mã Giảng Viên (*):</label>
                    <input type="text" name="idgv" class="form-control" value="<?= htmlspecialchars($gv['IDGV']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Họ và Tên Giảng Viên (*):</label>
                    <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($gv['Fullname']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tổng Số Lớp:</label>
                    <input type="number" name="sumclass" class="form-control" value="<?= $gv['SumClass'] ?>" min="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hình Ảnh Hiện Tại:</label><br>
                    <?php if (!empty($gv['Image']) && file_exists("uploads/" . $gv['Image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($gv['Image']) ?>" alt="Avatar" class="rounded mb-2" width="80" height="80" style="object-fit: cover;">
                    <?php else: ?>
                        <p class="text-muted fs-7">Chưa có ảnh</p>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Để trống nếu không muốn thay đổi ảnh.</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-warning">Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>