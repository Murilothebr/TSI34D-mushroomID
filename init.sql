CREATE DATABASE IF NOT EXISTS mushroom_id_teste;
CREATE DATABASE IF NOT EXISTS mushroom_id;

GRANT ALL PRIVILEGES ON mushroom_id_teste.* TO 'admin'@'%';
GRANT ALL PRIVILEGES ON mushroom_id.* TO 'admin'@'%';
FLUSH PRIVILEGES;

USE mushroom_id_teste;

SET foreign_key_checks = 0;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    encrypted_password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0
);

INSERT INTO users (name, email, encrypted_password, is_admin) VALUES
('Fulano', 'fulano@example.com', '$2y$10$UotfYd9ZjpxqzNaKZM5fVOfZeGdtqRGBkxGJlfadc7UJv2vDanPEK', 0),
('Admin', 'admin@example.com', '$2y$10$RIy7UDovnQltsY/AcD6Xf.LYc6oe5/UQL7za8BE3lLCVTMQIebsVe', 1);

SET foreign_key_checks = 1;

USE mushroom_id;

SET foreign_key_checks = 0;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    encrypted_password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0
);

INSERT INTO users (name, email, encrypted_password, is_admin) VALUES
('Fulano', 'fulano@example.com', '$2y$10$UotfYd9ZjpxqzNaKZM5fVOfZeGdtqRGBkxGJlfadc7UJv2vDanPEK', 0),
('Admin', 'admin@example.com', '$2y$10$RIy7UDovnQltsY/AcD6Xf.LYc6oe5/UQL7za8BE3lLCVTMQIebsVe', 1);

SET foreign_key_checks = 1;
