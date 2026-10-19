
<?<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli("localhost", "root", "", "music_db");

if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>