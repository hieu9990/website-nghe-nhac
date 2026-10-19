
<?php
require_once 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $artist_id = (int)($_POST['artist_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $file_path = trim($_POST['file_path'] ?? '');
    $image = trim($_POST['image'] ?? '');

    if ($title === '') {
        $message = 'Vui long nhap ten bai hat!';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO songs
            (title, artist_id, category_id, file_path, image)
            VALUES (?, NULLIF(?, 0), NULLIF(?, 0), ?, ?)"
        );

        if (!$stmt) {
            $message = 'Loi SQL: ' . $conn->error;
        } else {
            $stmt->bind_param(
                "siiss",
                $title,
                $artist_id,
                $category_id,
                $file_path,
                $image
            );

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: songs.php');
                exit;
            } else {
                $message = 'Khong them duoc bai hat: ' . $stmt->error;
            }

            $stmt->close();
        }
    }
}

$artists = $conn->query("SELECT id, name FROM artists ORDER BY name");
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");

if (!$artists || !$categories) {
    die('Loi doc ca si/the loai: ' . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm bài hát</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<section class="content">
    <h2>Thêm bài hát mới</h2>

    <?php if ($message !== ''): ?>
        <p style="color:red">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <p>Tên bài hát</p>
        <input type="text" name="title" required
               style="width:100%;padding:10px">

        <p>Ca sĩ</p>
        <select name="artist_id" style="width:100%;padding:10px">
            <option value="0">Chưa chọn</option>
            <?php while ($a = $artists->fetch_assoc()): ?>
                <option value="<?php echo $a['id']; ?>">
                    <?php echo htmlspecialchars($a['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <p>Thể loại</p>
        <select name="category_id" style="width:100%;padding:10px">
            <option value="0">Chưa chọn</option>
            <?php while ($c = $categories->fetch_assoc()): ?>
                <option value="<?php echo $c['id']; ?>">
                    <?php echo htmlspecialchars($c['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <p>Đường dẫn MP3</p>
        <input type="text" name="file_path"
               placeholder="uploads/song1.mp3"
               style="width:100%;padding:10px">

        <p>Đường dẫn ảnh</p>
        <input type="text" name="image"
               placeholder="images/song1.jpg"
               style="width:100%;padding:10px">

        <br><br>
        <button type="submit" class="btn">Lưu bài hát</button>
        <a href="songs.php">Quay lại</a>
    </form>
</section>
</body>
</html>
<?php $conn->close(); ?>
