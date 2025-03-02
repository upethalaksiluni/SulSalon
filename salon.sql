-- Create database
CREATE DATABASE IF NOT EXISTS salon;
USE salon;

-- Create user table
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    gender VARCHAR(10),
    birthdate DATE,
    phone VARCHAR(20),
    email VARCHAR(100) UNIQUE NOT NULL,
    address TEXT,
    profile_image VARCHAR(255),
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    preferred_stylist VARCHAR(100),
    frequent_services TEXT,
    preferred_time VARCHAR(20),
    allergies TEXT,
    medical_conditions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert the two admin users with hashed passwords
INSERT INTO `admin` (`username`, `password`) VALUES
('Admin1', '$2y$10$9M3vbJKqJk2T1XdXYV4HxuMqK7XoKRfVm94wd0UWj9TLsV2IQW7Hy'), -- Admin@123
('Admin2', '$2y$10$l7.5sJd2cMgkEh34YBnE6.0d3EDRqyDL1Yq0anGYU3MlKP/T/89yO'); -- Admin@1234