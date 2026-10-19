
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

   
    $stmt = $conn->prepare(
        "SELECT id, username, password, fullname, role
         FROM users
         WHERE username = ? OR email = ?
         LIMIT 1"
    );

    if (!$stmt) {
        die("Lỗi truy vấn SQL: " . $conn->error);
    }

    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['fullname'] = !empty($user['fullname'])
            ? $user['fullname']
            : $user['username'];
        $_SESSION['role'] = !empty($user['role'])
            ? $user['role']
            : 'user';

        header('Location: index.php');
        exit;
    } else {
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
    }

}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="content">
        <h2>Đăng nhập</h2>

        <?php if ($error !== ''): ?>
            <p style="color:red">
                <?php echo htmlspecialchars($error); ?>
            </p>
        <?php endif; ?>

        <form method="post">
            <p>Tên đăng nhập hoặc email</p>
            <input type="text" name="username" required>

            <p>Mật khẩu</p>
            <input type="password" name="password" required>

            <p>
                <button type="submit">Đăng nhập</button>
            </p>
        </form>

        <a href="register.php">Chưa có tài khoản? Đăng ký</a>
        <br><br>
        <a href="index.php">Quay lại trang chủ</a>
    </main>
</body>
</html>
