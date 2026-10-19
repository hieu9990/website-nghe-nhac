
<?php
require_once 'db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($fullname === '' || $subject === '' || $message === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Vui lòng nhập đầy đủ thông tin và email hợp lệ.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO contacts (fullname, email, subject, message)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssss",
            $fullname,
            $email,
            $subject,
            $message
        );

        if ($stmt->execute()) {
            $success = 'Gửi liên hệ thành công!';
        } else {
            $error = 'Không gửi được. Vui lòng thử lại.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - Music Website</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="content">
        <h1>Liên hệ với chúng tôi</h1>
        <p>Hãy gửi góp ý hoặc câu hỏi cho website nghe nhạc.</p>

        <?php if ($success !== ''): ?>
            <p style="color:green">
                <?= htmlspecialchars($success) ?>
            </p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p style="color:red">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="post" action="contact.php">
            <p>Họ và tên</p>
            <input type="text" name="fullname" maxlength="100" required>

            <p>Email</p>
            <input type="email" name="email" maxlength="150" required>

            <p>Tiêu đề</p>
            <input type="text" name="subject" maxlength="200" required>

            <p>Nội dung liên hệ</p>
            <textarea name="message" rows="6" required></textarea>

            <p>
                <button type="submit">Gửi liên hệ</button>
            </p>
        </form>

        <p><a href="index.php">← Quay lại trang chủ</a></p>
    </main>
</body>
</html>
