-- database.sql - Database Structure for Ramadhan Glow Up
-- Jalankan script ini di phpMyAdmin untuk membuat tabel-tabel

CREATE DATABASE IF NOT EXISTS ramadhan_glowup;
USE ramadhan_glowup;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default categories
INSERT INTO categories (name) VALUES 
('Merawat Diri (Fisik)'),
('Menata Hati (Spiritual)');

-- Table: daily_content (Materi harian)
CREATE TABLE IF NOT EXISTS daily_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    surah_text TEXT,
    surah_name VARCHAR(255),
    title VARCHAR(255),
    sub_title VARCHAR(255),
    day INT NOT NULL,
    description TEXT,
    tips TEXT,
    daily_focus_key VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Day 1 Content
INSERT INTO daily_content (surah_text, surah_name, title, sub_title, day, description, tips, daily_focus_key) 
VALUES (
    'Sesungguhnya Allah tidak akan mengubah nasib suatu kaum hingga mereka mengubah keadaan diri mereka sendiri.',
    'QS. Ar-Ra\'d: 11',
    'Bismillah for A New Me',
    'Menata Niat & Start Awal',
    1,
    'Hari pertama puasa adalah momentum untuk menata niat dengan baik. Hindari False Hope Syndrome dengan menetapkan target yang realistis.',
    'Validasi rasanya. Bilang ke diri sendiri: "Gapapa pusing dikit, ini tanda tubuh lagi bersih-bersih". Istirahatlah sejenak (Qailulah), jangan dipaksa.',
    'INTENTION SETTING'
);

-- Table: tasks (Master task list)
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    task_description VARCHAR(255) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Day 1 Tasks
INSERT INTO tasks (category_id, task_description) VALUES 
-- Category 1: Merawat Diri (Fisik)
(1, 'Sahur Bergizi (Walau sedikit)'),
(1, 'Hydration (2 Gelas saat Sahur)'),
(1, 'Lisan yang Cantik (No complaining)'),
-- Category 2: Menata Hati (Spiritual)
(2, 'Luruskan Niat ("Nawaitu...")'),
(2, 'Memaafkan Masa Lalu'),
(2, 'Mindful Moment (Nikmati lapar)');

-- Table: daily_task (Link daily_content dengan tasks)
CREATE TABLE IF NOT EXISTS daily_task (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    daily_content_id INT NOT NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (daily_content_id) REFERENCES daily_content(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Link Day 1 tasks
INSERT INTO daily_task (task_id, daily_content_id) VALUES 
(1, 1), (2, 1), (3, 1), (4, 1), (5, 1), (6, 1);

-- Table: daily_logs (User progress per task)
CREATE TABLE IF NOT EXISTS daily_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    daily_content_id INT NOT NULL,
    daily_task_id INT NOT NULL,
    is_completed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (daily_content_id) REFERENCES daily_content(id) ON DELETE CASCADE,
    FOREIGN KEY (daily_task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_task (user_id, daily_content_id, daily_task_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: user_journals (Journaling entries)
CREATE TABLE IF NOT EXISTS user_journals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    daily_content_id INT NOT NULL,
    ramadhan_why TEXT,
    bad_habit TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (daily_content_id) REFERENCES daily_content(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_journal (user_id, daily_content_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: mood_check (Daily mood tracking)
CREATE TABLE IF NOT EXISTS mood_check (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mood INT NOT NULL COMMENT '1=Happy, 2=Neutral, 3=Tired, 4=Sad',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: water_level (Daily water intake)
CREATE TABLE IF NOT EXISTS water_level (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    level INT DEFAULT 0 COMMENT 'Number of glasses (0-8)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Display structure
SHOW TABLES;