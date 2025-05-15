<?php
/**
 * ملف إعدادات التطبيق
 * 
 * يحتوي على جميع إعدادات التطبيق الأساسية وبيانات الاتصال بقاعدة البيانات
 * 
 * @package WeCima
 * @version 1.0.0
 */

// منع الوصول المباشر للملف
if (!defined('WECIMA_APP')) {
    die('الوصول المباشر لهذا الملف غير مسموح!');
}

// إعدادات البيئة
define('ENVIRONMENT', 'development'); // development, production, testing

// ضبط إعدادات الخطأ حسب البيئة
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_NAME', 'wecima_db');
define('DB_USER', 'wecima_user');
define('DB_PASS', 'strong_password_here');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');
define('DB_PREFIX', 'wc_');

// إعدادات URL
define('SITE_URL', 'https://wecima.com');
define('ADMIN_URL', SITE_URL . '/admin');
define('API_URL', SITE_URL . '/api');

// إعدادات المسارات
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('CACHE_PATH', ROOT_PATH . '/cache');

// إعدادات الأمان
define('AUTH_KEY', 'put your unique phrase here');
define('SECURE_AUTH_KEY', 'put your unique phrase here');
define('LOGGED_IN_KEY', 'put your unique phrase here');
define('NONCE_KEY', 'put your unique phrase here');
define('AUTH_SALT', 'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT', 'put your unique phrase here');
define('NONCE_SALT', 'put your unique phrase here');

// إعدادات الجلسة
define('SESSION_NAME', 'wecima_session');
define('SESSION_LIFETIME', 7200); // بالثواني (2 ساعة)
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', false);
define('SESSION_HTTPONLY', true);

// إعدادات ملفات تعريف الارتباط
define('COOKIE_LIFETIME', 2592000); // بالثواني (30 يوم)
define('COOKIE_PATH', '/');
define('COOKIE_DOMAIN', '');
define('COOKIE_SECURE', false);
define('COOKIE_HTTPONLY', true);

// إعدادات البريد الإلكتروني
define('MAIL_HOST', 'smtp.example.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'info@wecima.com');
define('MAIL_PASSWORD', 'your_email_password');
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', 'info@wecima.com');
define('MAIL_FROM_NAME', 'WeCima');

// إعدادات API
define('API_KEY', 'your_api_key_here');
define('TMDB_API_KEY', 'your_tmdb_api_key_here');
define('YOUTUBE_API_KEY', 'your_youtube_api_key_here');

// إعدادات التحميل
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10 ميجابايت
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_VIDEO_TYPES', ['mp4', 'webm', 'ogg']);

// إعدادات التخزين المؤقت
define('ENABLE_CACHE', true);
define('CACHE_LIFETIME', 3600); // بالثواني (1 ساعة)

// إعدادات التحقق من الروبوتات
define('RECAPTCHA_SITE_KEY', 'your_recaptcha_site_key');
define('RECAPTCHA_SECRET_KEY', 'your_recaptcha_secret_key');

// إعدادات الدفع
define('PAYMENT_GATEWAY', 'stripe'); // stripe, paypal, etc.
define('STRIPE_PUBLIC_KEY', 'your_stripe_public_key');
define('STRIPE_SECRET_KEY', 'your_stripe_secret_key');
define('PAYPAL_CLIENT_ID', 'your_paypal_client_id');
define('PAYPAL_SECRET', 'your_paypal_secret');

// إعدادات التحليلات
define('GOOGLE_ANALYTICS_ID', 'UA-XXXXXXXXX-X');

// إعدادات التصحيح
define('DEBUG_MODE', ENVIRONMENT === 'development');
define('DEBUG_LOG', ROOT_PATH . '/logs/debug.log');

/**
 * اتصال قاعدة البيانات
 */
function wecima_db_connect() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
        } else {
            die("حدث خطأ في الاتصال بقاعدة البيانات. يرجى المحاولة لاحقًا.");
        }
    }
}

// تحميل ملفات الأمان والوظائف الأساسية
require_once INCLUDES_PATH . '/security.php';
require_once INCLUDES_PATH . '/functions.php';