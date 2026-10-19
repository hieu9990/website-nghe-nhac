
<?php
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($fullname === '' || $username === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 6) {
        $error = 'Vui lòng nhập đủ thông tin và mật khẩu ít nhất 6 ký tự.';
    } else {
        $check = $conn->prepare(
            "SELECT id FROM users WHERE username = ? OR email = ?"
        );
        $check->bind_param("ss", $username, $email);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $error = 'Tên đăng nhập hoặc email đã tồn tại.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users
                (username, password, fullname, email)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss", $username, $hash, $fullname, $email
            );

            if ($stmt->execute()) {
                header('Location: login.php');
                exit;
            } else {
                $error = 'Đăng ký thất bại. Hãy thử lại.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="content">
        <h2>Đăng ký tài khoản</h2>

        <?php if ($error !== ''): ?>
            <p style="color:red">
                <?php echo htmlspecialchars($error); ?>
            </p>
        <?php endif; ?>

        <form method="post">
            <p>Họ và tên</p>
            <input type="text" name="fullname" required>

            <p>Tên đăng nhập</p>
            <input type="text" name="username" required>

            <p>Email</p>
            <input type="email" name="email" required>

            <p>Mật khẩu (ít nhất 6 ký tự)</p>
            <input type="password" name="password"
                   minlength="6" required>

            <p>
                <button type="submit">Đăng ký</button>
            </p>
        </form>

        <a href="login.php">Đã có tài khoản? Đăng nhập</a>
        <br><br>
        <a href="index.php">Quay lại trang chủ</a>
    </main>
</body>
</html>
