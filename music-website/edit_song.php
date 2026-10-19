
<?php
require_once 'db.php';

// Kiem tra quyen admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    die("Ban khong co quyen sua bai hat. <a href='index.php'>Ve trang chu</a>");
}

// Lay ID bai hat
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("ID bai hat khong hop le.");
}

// Lay thong tin bai hat
$stmt = $conn->prepare(
    "SELECT id, title, artist_id, category_id, file_path, image
     FROM songs
     WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();

$song = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$song) {
    die("Khong tim thay bai hat.");
}

$error = '';

// Xu ly khi bam nut Cap nhat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $artist_id = (int)($_POST['artist_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $file_path = trim($_POST['file_path'] ?? '');
    $image = trim($_POST['image'] ?? '');

    if ($title === '' || $artist_id <= 0 || $category_id <= 0) {
        $error = 'Vui long nhap ten bai hat, ca si va the loai.';
    } else {
        $update = $conn->prepare(
            "UPDATE songs
             SET title = ?, artist_id = ?, category_id = ?,
                 file_path = ?, image = ?
             WHERE id = ?"
        );

        if (!$update) {
            $error = 'Loi SQL: ' . $conn->error;
        } else {
            $update->bind_param(
                "siissi",
                $title,
                $artist_id,
                $category_id,
                $file_path,
                $image,
                $id
            );

            if ($update->execute()) {
                $update->close();
                header('Location: songs.php?updated=1');
                exit;
            } else {
                $error = 'Cap nhat that bai: ' . $update->error;
                $update->close();
            }
        }
    }

    // Hien thi lai du lieu vua nhap neu co loi
    $song['title'] = $title;
    $song['artist_id'] = $artist_id;
    $song['category_id'] = $category_id;
    $song['file_path'] = $file_path;
    $song['image'] = $image;
}

// Lay danh sach ca si va the loai
$artists = $conn->query("SELECT id, name FROM artists ORDER BY name");
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sua bai hat - Music Website</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">♫ Music Website</div>
    <nav>
        <a href="index.php">Trang chủ</a>
        <a href="songs.php">Bài hát</a>
        <a href="logout.php">Đăng xuất</a>
    </nav>
</header>

<main class="content">
    <h1>Sửa thông tin bài hát</h1>

    <?php if ($error !== ''): ?>
        <p style="color:red">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <p>Tên bài hát</p>
        <input
            type="text"
            name="title"
            value="<?= htmlspecialchars($song['title']) ?>"
            required
        >

        <p>Ca sĩ</p>
        <select name="artist_id" required>
            <option value="">-- Chọn ca sĩ --</option>

            <?php while ($artist = $artists->fetch_assoc()): ?>
                <option
                    value="<?= (int)$artist['id'] ?>"
                    <?= (int)$artist['id'] === (int)$song['artist_id']
                        ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($artist['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <p>Thể loại</p>
        <select name="category_id" required>
            <option value="">-- Chọn thể loại --</option>

            <?php while ($category = $categories->fetch_assoc()): ?>
                <option
                    value="<?= (int)$category['id'] ?>"
                    <?= (int)$category['id'] === (int)$song['category_id']
                        ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <p>Đường dẫn file nhạc</p>
        <input
            type="text"
            name="file_path"
            value="<?= htmlspecialchars($song['file_path'] ?? '') ?>"
            placeholder="uploads/baihat.mp3"
        >

        <p>Đường dẫn ảnh bìa</p>
        <input
            type="text"
            name="image"
            value="<?= htmlspecialchars($song['image'] ?? '') ?>"
            placeholder="images/bia.jpg"
        >

        <p>
            <button type="submit">Cập nhật bài hát</button>
        </p>
    </form>

    <a href="songs.php">Quay lại danh sách bài hát</a>
</main>

</body>
</html>
