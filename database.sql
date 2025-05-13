-- Forum database structuur

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100) NOT NULL,
  role ENUM('admin','moderator','user') DEFAULT 'user',
  banned TINYINT(1) DEFAULT 0,
  bio TEXT DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS forums (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  post_permission ENUM('all','moderators','admins') DEFAULT 'all',
  read_permission ENUM('all','moderators','admins') DEFAULT 'all',
  age_restriction ENUM('none','12+','16+','18+') DEFAULT 'none'
);

CREATE TABLE IF NOT EXISTS threads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  forum_id INT NOT NULL,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  closed TINYINT(1) DEFAULT 0,
  announcement TINYINT(1) DEFAULT 0,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (forum_id) REFERENCES forums(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  thread_id INT NOT NULL,
  user_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME,
  FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS settings (
  id INT PRIMARY KEY,
  forum_name VARCHAR(100) NOT NULL,
  forum_description TEXT,
  language VARCHAR(10) NOT NULL DEFAULT 'en',
  logo_url VARCHAR(255) DEFAULT NULL,
  footer_text TEXT DEFAULT NULL
);

-- Voeg een standaard settings record toe (pas aan naar wens)
INSERT INTO settings (id, forum_name, forum_description, language, logo_url, footer_text) 
VALUES (1, 'My Forum', 'Welcome to your new forum!', 'en', NULL, '© 2024 My Forum. All rights reserved.'); 