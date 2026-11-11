<?php
include 'db.php';

$result = $conn->query("SELECT * FROM GiangVien ORDER BY IDGV ASC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Giảng Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Danh Sách Giảng Viên</h4>
            <a href="add.php" class="btn btn-light text-primary fw-bold">+ Thêm Giảng Viên</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Mã GV</th>
                            <th>Hình ảnh</th>
                            <th>Họ và Tên</th>
                            <th>Tổng số lớp</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center font-monospace"><strong><?= htmlspecialchars($row['IDGV']) ?></strong></td>
                                    <td class="text-center">
                                        <?php if (!empty($row['Image']) && file_exists("uploads/" . $row['Image'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($row['Image']) ?>" alt="Avatar" class="rounded" width="60" height="60" style="object-fit: cover;">
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Chưa có ảnh</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($row['Fullname']) ?></strong></td>
                                    <td class="text-center"><span class="badge bg-info text-dark fs-6"><?= $row['SumClass'] ?></span></td>
                                    <td class="text-center">
                                        <a href="edit.php?id=<?= urlencode($row['IDGV']) ?>" class="btn btn-sm btn-warning">Sửa</a>
                                        <a href="delete.php?id=<?= urlencode($row['IDGV']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa giảng viên này?');">Xóa</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Chưa có dữ liệu giảng viên.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>