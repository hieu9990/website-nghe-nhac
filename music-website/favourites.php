
<?php
session_start();
require_once "db.php";

// Kiem tra dang nhap
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Lay danh sach bai hat yeu thich
$sql = "SELECT songs.*
        FROM favorites
        JOIN songs ON favorites.song_id = songs.id
        WHERE favorites.user_id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Loi truy van: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài hát yêu thích</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Website nghe nhạc trực tuyến</h1>
    <nav>
        <a href="index.php">Trang chủ</a>
        <a href="songs.php">Danh sách bài hát</a>
        <a href="favorites.php">Bài hát yêu thích</a>
        <a href="contact.php">Liên hệ</a>
        <a href="logout.php">Đăng xuất</a>
    </nav>
</header>

<main class="container">
    <h2>Danh sách bài hát yêu thích</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="song-list">
            <?php while ($song = $result->fetch_assoc()): ?>
                <div class="song-item">
                    <h3>
                        <?= htmlspecialchars($song["title"] ?? $song["name"] ?? "Bài hát") ?>
                    </h3>

                    <?php if (!empty($song["artist"])): ?>
                        <p>Ca sĩ: <?= htmlspecialchars($song["artist"]) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($song["file_path"])): ?>
                        <audio controls>
                            <source src="<?= htmlspecialchars($song["file_path"]) ?>">
                        </audio>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Bạn chưa có bài hát yêu thích nào.</p>
        <a href="songs.php">Khám phá bài hát</a>
    <?php endif; ?>

</main>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
