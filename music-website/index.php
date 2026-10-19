
<?php
require_once 'db.php';

$keyword = trim($_GET['search'] ?? '');

$sql = "SELECT songs.id, songs.title, songs.file_path,
               songs.image,
               artists.name AS artist_name,
               categories.name AS category_name
        FROM songs
        LEFT JOIN artists ON songs.artist_id = artists.id
        LEFT JOIN categories ON songs.category_id = categories.id";

if ($keyword !== '') {
    $sql .= " WHERE songs.title LIKE ?
              OR artists.name LIKE ?";
}

$sql .= " ORDER BY songs.id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Loi truy van SQL: " . $conn->error);
}

if ($keyword !== '') {
    $search = "%" . $keyword . "%";
    $stmt->bind_param("ss", $search, $search);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music World - Nghe nhac truc tuyen</title>
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


<section class="hero">
    <h1>Thế giới âm nhạc của bạn</h1>
    <p>Khám phá bài hát yêu thích và tận hưởng giai điệu mỗi ngày.</p>
</section>


<section class="content" id="songs">
    <h2>Danh sach bai hat</h2>

<form method="get" action="index.php">
    <input
        type="text"
        name="search"
        placeholder="Nhập tên bài hát hoặc ca sĩ..."
        value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
    >
    <button type="submit">Tìm kiếm</button>
</form>

    <div class="song-list">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($song = $result->fetch_assoc()): ?>
            <div class="song-card">
                <img
                    src="<?php echo htmlspecialchars(
                        $song['image'] ?: 'images/default.jpg'
                    ); ?>"
                    alt="Anh bai hat"
                    onerror="this.onerror=null;this.src='https://placehold.co/300x200?text=Music';"
                >

                <h3><?php  echo htmlspecialchars($song['title']); ?></h3>

                <p>Ca si:
                    <?php echo htmlspecialchars(
                        $song['artist_name'] ?? 'Chua cap nhat'
                    ); ?>
                </p>

                <p>The loai:
                    <?php echo htmlspecialchars(
                        $song['category_name'] ?? 'Chua phan loai'
                    ); ?>
                </p>

                <?php if (!empty($song['file_path'])): ?>
                    <audio controls preload="none">
                        <source
                            src="<?php echo htmlspecialchars($song['file_path']); ?>"
                            type="audio/mpeg"
                        >
                    </audio>
                    
                <?php else: ?>
                    <p>Chua co file nhac.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Chua co bai hat nao trong co so du lieu.</p>
    <?php endif; ?>
    </div>
</section>

<footer id="contact">
    <h3>Music World</h3>
    <p>Website nghe nhac truc tuyen phuc vu hoc tap</p>
    <p>Email: musicworld@example.com</p>
    <p>© 2026 Music World</p>
</footer>

</body>
</html>
<?php
$conn->close();
?>
