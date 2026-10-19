
<?php
require_once 'db.php';

// Kiem tra quyen admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    die("Ban khong co quyen xoa bai hat. <a href='index.php'>Ve trang chu</a>");
}

// Chi cho phep xoa bang POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: songs.php');
    exit;
}

// Lay ID bai hat
$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: songs.php');
    exit;
}

// Xoa bai hat khoi danh sach yeu thich truoc
$stmt = $conn->prepare("DELETE FROM favorites WHERE song_id = ?");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    die("Loi xoa yeu thich: " . htmlspecialchars($stmt->error));
}
$stmt->close();

// Xoa bai hat
$stmt = $conn->prepare("DELETE FROM songs WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: songs.php?deleted=1');
    exit;
} else {
    die("Loi xoa bai hat: " . htmlspecialchars($stmt->error));
}
?>
