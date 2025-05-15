-- ملف SQL لإنشاء قاعدة البيانات لموقع WeCima
-- تاريخ الإنشاء: 2023-07-01

-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS wecima CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- استخدام قاعدة البيانات
USE wecima;

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    bio TEXT,
    role ENUM('user', 'editor', 'admin', 'super_admin') DEFAULT 'user',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول التصنيفات
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    parent_id INT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول الأفلام
CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    original_title VARCHAR(255),
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    poster VARCHAR(255),
    backdrop VARCHAR(255),
    trailer_url VARCHAR(255),
    release_year YEAR,
    duration INT COMMENT 'المدة بالدقائق',
    quality ENUM('CAM', 'HDCAM', 'HDTC', 'HDTS', 'DVDSCR', 'WEBDL', 'BLURAY', '4K') DEFAULT 'WEBDL',
    rating DECIMAL(3,1) DEFAULT 0.0,
    votes INT DEFAULT 0,
    views INT DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    status ENUM('published', 'pending', 'draft') DEFAULT 'pending',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول المسلسلات
CREATE TABLE IF NOT EXISTS series (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    original_title VARCHAR(255),
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    poster VARCHAR(255),
    backdrop VARCHAR(255),
    trailer_url VARCHAR(255),
    start_year YEAR,
    end_year YEAR,
    status ENUM('running', 'ended', 'canceled') DEFAULT 'running',
    rating DECIMAL(3,1) DEFAULT 0.0,
    votes INT DEFAULT 0,
    views INT DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    publish_status ENUM('published', 'pending', 'draft') DEFAULT 'pending',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول مواسم المسلسلات
CREATE TABLE IF NOT EXISTS seasons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    series_id INT NOT NULL,
    season_number INT NOT NULL,
    title VARCHAR(255),
    poster VARCHAR(255),
    release_year YEAR,
    episodes_count INT DEFAULT 0,
    description TEXT,
    status ENUM('published', 'pending', 'draft') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE,
    UNIQUE KEY (series_id, season_number)
) ENGINE=InnoDB;

-- جدول حلقات المسلسلات
CREATE TABLE IF NOT EXISTS episodes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    season_id INT NOT NULL,
    episode_number INT NOT NULL,
    title VARCHAR(255),
    description TEXT,
    duration INT COMMENT 'المدة بالدقائق',
    release_date DATE,
    thumbnail VARCHAR(255),
    views INT DEFAULT 0,
    status ENUM('published', 'pending', 'draft') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE CASCADE,
    UNIQUE KEY (season_id, episode_number)
) ENGINE=InnoDB;

-- جدول العلاقة بين الأفلام والتصنيفات
CREATE TABLE IF NOT EXISTS movie_category (
    movie_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (movie_id, category_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول العلاقة بين المسلسلات والتصنيفات
CREATE TABLE IF NOT EXISTS series_category (
    series_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (series_id, category_id),
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول الممثلين
CREATE TABLE IF NOT EXISTS actors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    image VARCHAR(255),
    bio TEXT,
    birth_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول العلاقة بين الأفلام والممثلين
CREATE TABLE IF NOT EXISTS movie_actor (
    movie_id INT NOT NULL,
    actor_id INT NOT NULL,
    role VARCHAR(100),
    is_star BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (movie_id, actor_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES actors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول العلاقة بين المسلسلات والممثلين
CREATE TABLE IF NOT EXISTS series_actor (
    series_id INT NOT NULL,
    actor_id INT NOT NULL,
    role VARCHAR(100),
    is_star BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (series_id, actor_id),
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES actors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول المخرجين
CREATE TABLE IF NOT EXISTS directors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    image VARCHAR(255),
    bio TEXT,
    birth_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول العلاقة بين الأفلام والمخرجين
CREATE TABLE IF NOT EXISTS movie_director (
    movie_id INT NOT NULL,
    director_id INT NOT NULL,
    PRIMARY KEY (movie_id, director_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (director_id) REFERENCES directors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول العلاقة بين المسلسلات والمخرجين
CREATE TABLE IF NOT EXISTS series_director (
    series_id INT NOT NULL,
    director_id INT NOT NULL,
    PRIMARY KEY (series_id, director_id),
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE,
    FOREIGN KEY (director_id) REFERENCES directors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول روابط مشاهدة الأفلام
CREATE TABLE IF NOT EXISTS movie_servers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    server_name VARCHAR(50) NOT NULL,
    server_type ENUM('embed', 'direct', 'torrent') DEFAULT 'embed',
    quality ENUM('CAM', 'HDCAM', 'HDTC', 'HDTS', 'DVDSCR', 'WEBDL', 'BLURAY', '4K') DEFAULT 'WEBDL',
    url TEXT NOT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول روابط مشاهدة الحلقات
CREATE TABLE IF NOT EXISTS episode_servers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    episode_id INT NOT NULL,
    server_name VARCHAR(50) NOT NULL,
    server_type ENUM('embed', 'direct', 'torrent') DEFAULT 'embed',
    quality ENUM('CAM', 'HDCAM', 'HDTC', 'HDTS', 'DVDSCR', 'WEBDL', 'BLURAY', '4K') DEFAULT 'WEBDL',
    url TEXT NOT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (episode_id) REFERENCES episodes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول روابط تحميل الأفلام
CREATE TABLE IF NOT EXISTS movie_downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    server_name VARCHAR(50) NOT NULL,
    quality ENUM('CAM', 'HDCAM', 'HDTC', 'HDTS', 'DVDSCR', 'WEBDL', 'BLURAY', '4K') DEFAULT 'WEBDL',
    size VARCHAR(20),
    url TEXT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول روابط تحميل الحلقات
CREATE TABLE IF NOT EXISTS episode_downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    episode_id INT NOT NULL,
    server_name VARCHAR(50) NOT NULL,
    quality ENUM('CAM', 'HDCAM', 'HDTC', 'HDTS', 'DVDSCR', 'WEBDL', 'BLURAY', '4K') DEFAULT 'WEBDL',
    size VARCHAR(20),
    url TEXT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (episode_id) REFERENCES episodes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول التعليقات
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content_type ENUM('movie', 'series', 'episode') NOT NULL,
    content_id INT NOT NULL,
    parent_id INT NULL,
    comment TEXT NOT NULL,
    status ENUM('approved', 'pending', 'spam') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- جدول التقييمات
CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content_type ENUM('movie', 'series', 'episode') NOT NULL,
    content_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, content_type, content_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول قائمة المشاهدة
CREATE TABLE IF NOT EXISTS watchlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content_type ENUM('movie', 'series') NOT NULL,
    content_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, content_type, content_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول سجل المشاهدة
CREATE TABLE IF NOT EXISTS watch_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content_type ENUM('movie', 'episode') NOT NULL,
    content_id INT NOT NULL,
    watched_time INT DEFAULT 0 COMMENT 'الوقت المشاهد بالثواني',
    total_time INT DEFAULT 0 COMMENT 'إجمالي وقت المحتوى بالثواني',
    completed BOOLEAN DEFAULT FALSE,
    last_watched TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, content_type, content_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول الإعدادات
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    is_public BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول سجل الأنشطة
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- إنشاء فهارس للبحث
CREATE FULLTEXT INDEX movies_search ON movies(title, original_title, description);
CREATE FULLTEXT INDEX series_search ON series(title, original_title, description);
CREATE FULLTEXT INDEX actors_search ON actors(name, bio);
CREATE FULLTEXT INDEX directors_search ON directors(name, bio);

-- إنشاء فهارس للأداء
CREATE INDEX idx_movies_status ON movies(status);
CREATE INDEX idx_series_status ON series(publish_status);
CREATE INDEX idx_movies_featured ON movies(featured);
CREATE INDEX idx_series_featured ON series(featured);
CREATE INDEX idx_comments_status ON comments(status);
CREATE INDEX idx_comments_content ON comments(content_type, content_id);
CREATE INDEX idx_ratings_content ON ratings(content_type, content_id);