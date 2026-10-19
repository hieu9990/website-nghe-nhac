
<?php
require_once 'db.php';

$keyword = trim($_GET['search'] ?? '');

$sql = "SELECT songs.id, songs.title, songs.file_path, songs.image,
               artists.name AS artist_name,
               categories.name AS category_name
        FROM songs
        LEFT JOIN artists ON songs.artist_id = artists.id
        LEFT JOIN categories ON songs.category_id = categories.id";

if ($keyword !== '') {
    $sql .= " WHERE songs.title LIKE ?
              OR artists.name LIKE ?
              OR categories.name LIKE ?";
}

$sql .= " ORDER BY songs.id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Loi truy van SQL: " . $conn->error);
}

if ($keyword !== '') {
    $search = "%" . $keyword . "%";
    $stmt->bind_param("sss", $search, $search, $search);
}

$stmt->execute();
$result = $stmt->get_result();

$isAdmin = isset($_SESSION['user_id'])
    && ($_SESSION['role'] ?? '') === 'admin';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách bài hát - Music Website</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">♫ Music Website</div>
    <nav>
        <a href="index.php">Trang chủ</a>
        <a href="songs.php">Bài hát</a>
        <a href="favorites.php">Yêu thích</a>
        <a href="contact.php">Liên hệ</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <span class="user-info">
                Xin chào,
                <?= htmlspecialchars(
                    $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Bạn'
                ) ?>
            </span>
            <a href="logout.php">Đăng xuất</a>
        <?php else: ?>
            <a href="login.php">Đăng nhập</a>
            <a href="register.php">Đăng ký</a>
        <?php endif; ?>
    </nav>
</header>

<main class="content">
    <h1>Danh sách bài hát</h1>
    <?php if (isset($_GET['deleted'])): ?>
    <p style="color:green">Đã xóa bài hát thành công!</p>
<?php endif; ?>

    <?php if (isset($_GET['added'])): ?>
        <p style="color:green">Đã thêm bài hát thành công!</p>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <p style="color:green">Đã cập nhật bài hát thành công!</p>
    <?php endif; ?>

    <form method="get" action="songs.php">
        <input
            type="text"
            name="search"
            placeholder="Nhập tên bài hát hoặc ca sĩ..."
            value="<?= htmlspecialchars($keyword) ?>"
        >
        <button type="submit">Tìm kiếm</button>
    </form>

    <?php if ($isAdmin): ?>
        <p>
            <a href="add_song.php">+ Thêm bài hát mới</a>
        </p>
    <?php endif; ?>

    <div class="song-list">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($song = $result->fetch_assoc()): ?>
                <div class="song-card">

                    <img
                        src="<?= htmlspecialchars(
                            $song['image'] ?: 'images/default.jpg'
                        ) ?>"
                        alt="Ảnh bìa bài hát"
                        onerror="this.onerror=null;this.src='https://placehold.co/300x200?text=Music';"
                    >

                    <h3>
                        <?= htmlspecialchars($song['title']) ?>
                    </h3>

                    <p>
                        Ca sĩ:
                        <?= htmlspecialchars(
                            $song['artist_name'] ?? 'Chưa cập nhật'
                        ) ?>
                    </p>

                    <p>
                        Thể loại:
                        <?= htmlspecialchars(
                            $song['category_name'] ?? 'Chưa phân loại'
                        ) ?>
                    </p>

                    <?php if (!empty($song['file_path'])): ?>
                        <audio controls preload="none">
                            <source
                                src="<?= htmlspecialchars($song['file_path']) ?>"
                                type="audio/mpeg"
                            >
                            Trình duyệt không hỗ trợ phát nhạc.
                        </audio>
                    <?php else: ?>
                        <p>Chưa có file nhạc.</p>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <p>
                            
<form method="post" action="favorite.php">
    <input type="hidden" name="song_id"
           value="<?= (int)$song['id'] ?>">
    <button type="submit">♡ Yêu thích</button>
</form>

                        </p>
                    <?php endif; ?>

                    <?php if ($isAdmin): ?>
                        <p>
                            <a href="edit_song.php?id=<?= (int)$song['id'] ?>">
                                Sửa
                            </a>

                            |

<form method="post" action="delete_song.php"
      onsubmit="return confirm('Bạn có chắc muốn xóa bài hát này?');">
    <input type="hidden" name="id"
           value="<?= (int)$song['id'] ?>">
    <button type="submit">Xóa</button>
</form>

                        </p>
                    <?php endif; ?>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Không tìm thấy bài hát nào.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <h3>Music Website</h3>
    <p>Website nghe nhạc trực tuyến phục vụ học tập</p>
    <p>© 2026 Music Website</p>
</footer>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
