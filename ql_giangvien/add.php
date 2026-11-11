<?php
include 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idgv = trim($_POST['idgv']);
    $fullname = trim($_POST['fullname']);
    $sumclass = intval($_POST['sumclass']);
    $imageName = '';

    if (empty($idgv) || empty($fullname)) {
        $error = "Vui lòng nhập Mã giảng viên và Họ tên.";
    } else {
        $stmt_check = $conn->prepare("SELECT IDGV FROM GiangVien WHERE IDGV = ?");
        $stmt_check->bind_param("s", $idgv);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();

        if ($res_check->num_rows > 0) {
            $error = "Mã giảng viên '$idgv' đã tồn tại!";
        } else {
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageName = time() . '_' . uniqid() . '.' . $ext;
                if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
                move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $imageName);
            }

            $stmt = $conn->prepare("INSERT INTO GiangVien (IDGV, Fullname, Image, SumClass) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $idgv, $fullname, $imageName, $sumclass);
            
            if ($stmt->execute()) {
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
    <title>Thêm Giảng Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Thêm Mới Giảng Viên</h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Mã Giảng Viên (*):</label>
                    <input type="text" name="idgv" class="form-control" placeholder="Ví dụ: GV01, IT02..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Họ và Tên Giảng Viên (*):</label>
                    <input type="text" name="fullname" class="form-control" placeholder="Nhập họ và tên" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tổng Số Lớp:</label>
                    <input type="number" name="sumclass" class="form-control" value="0" min="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hình Ảnh:</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-success">Lưu Giảng Viên</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>