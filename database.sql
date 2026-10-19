CREATE DATABASE IF NOT EXISTS music_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE music_db;
CREATE TABLE IF NOT EXISTS bai_hat (
 id INT AUTO_INCREMENT PRIMARY KEY,
 ten_bai_hat VARCHAR(150) NOT NULL,
 ca_si VARCHAR(100) NOT NULL,
 the_loai VARCHAR(80) NOT NULL,
 duong_dan_nhac VARCHAR(255) NULL,
 ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO bai_hat (ten_bai_hat,ca_si,the_loai) VALUES
('Nang Am Xa Dan','Son Tung M-TP','Pop'),('Lac Troi','Son Tung M-TP','Pop'),('See Tinh','Hoang Thuy Linh','Dance');

