
<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: songs.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$song_id = (int)($_POST['song_id'] ?? 0);

if ($song_id <= 0) {
    header('Location: songs.php');
    exit;
}

// Kiem tra bai hat co ton tai khong
$check = $conn->prepare("SELECT id FROM songs WHERE id = ?");
$check->bind_param("i", $song_id);
$check->execute();
$exists = $check->get_result()->num_rows > 0;
$check->close();

if (!$exists) {
    header('Location: songs.php');
    exit;
}

// Them vao danh sach yeu thich
$stmt = $conn->prepare(
    "INSERT IGNORE INTO favorites (user_id, song_id)
     VALUES (?, ?)"
);
$stmt->bind_param("ii", $user_id, $song_id);
$stmt->execute();
$stmt->close();

header('Location: favorites.php');
exit;
?>
