<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = trim($_GET['id']);

    $stmt = $conn->prepare("SELECT Image FROM GiangVien WHERE IDGV = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['Image']) && file_exists("uploads/" . $row['Image'])) {
            unlink("uploads/" . $row['Image']);
        }
    }

    $stmt_del = $conn->prepare("DELETE FROM GiangVien WHERE IDGV = ?");
    $stmt_del->bind_param("s", $id);
    $stmt_del->execute();
}

header("Location: index.php");
exit();
?>