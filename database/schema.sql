CREATE DATABASE IF NOT EXISTS mushroom_id_teste;
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

SET foreign_key_checks = 1;
