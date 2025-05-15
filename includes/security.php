<?php
/**
 * ملف دوال الأمان لموقع WeCima
 * 
 * يحتوي هذا الملف على مجموعة من الدوال المساعدة للتعامل مع أمان الموقع
 * مثل تنظيف المدخلات، التحقق من الجلسات، منع هجمات حقن SQL وغيرها
 */

// منع الوصول المباشر للملف
if (!defined('BASEPATH')) {
    exit('لا يمكن الوصول المباشر لهذا الملف');
}

/**
 * دالة لتنظيف المدخلات من الرموز الخاصة
 * 
 * @param string|array $data البيانات المراد تنظيفها
 * @return string|array البيانات بعد التنظيف
 */
function clean_input($data) {
    if (is_array($data)) {
        $cleaned = [];
        foreach ($data as $key => $value) {
            $cleaned[$key] = clean_input($value);
        }
        return $cleaned;
    }
    
    // إزالة المسافات الزائدة
    $data = trim($data);
    // إزالة الباك سلاش
    $data = stripslashes($data);
    // تحويل الرموز الخاصة إلى كيانات HTML
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

/**
 * دالة للتحقق من صحة البريد الإلكتروني
 * 
 * @param string $email البريد الإلكتروني المراد التحقق منه
 * @return bool نتيجة التحقق
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * دالة لإنشاء كلمة مرور مشفرة
 * 
 * @param string $password كلمة المرور المراد تشفيرها
 * @return string كلمة المرور المشفرة
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * دالة للتحقق من صحة كلمة المرور
 * 
 * @param string $password كلمة المرور المدخلة
 * @param string $hashed_password كلمة المرور المشفرة المخزنة
 * @return bool نتيجة التحقق
 */
function verify_password($password, $hashed_password) {
    return password_verify($password, $hashed_password);
}

/**
 * دالة لإنشاء رمز CSRF لحماية النماذج
 * 
 * @return string رمز CSRF
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * دالة للتحقق من صحة رمز CSRF
 * 
 * @param string $token الرمز المرسل من النموذج
 * @return bool نتيجة التحقق
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    
    return true;
}

/**
 * دالة لإنشاء رمز تأكيد البريد الإلكتروني
 * 
 * @return string رمز التأكيد
 */
function generate_verification_token() {
    return bin2hex(random_bytes(16));
}

/**
 * دالة لإنشاء رمز إعادة تعيين كلمة المرور
 * 
 * @return string رمز إعادة التعيين
 */
function generate_reset_token() {
    return bin2hex(random_bytes(16)) . '_' . time();
}

/**
 * دالة للتحقق من صلاحية رمز إعادة تعيين كلمة المرور
 * 
 * @param string $token الرمز المراد التحقق منه
 * @param int $expiry_hours عدد ساعات الصلاحية
 * @return bool نتيجة التحقق
 */
function is_valid_reset_token($token, $expiry_hours = 24) {
    $parts = explode('_', $token);
    if (count($parts) !== 2) {
        return false;
    }
    
    $timestamp = (int)$parts[1];
    $expiry_time = $timestamp + ($expiry_hours * 3600);
    
    return time() <= $expiry_time;
}

/**
 * دالة لمنع هجمات XSS
 * 
 * @param string $string النص المراد تنظيفه
 * @return string النص بعد التنظيف
 */
function xss_clean($string) {
    // قائمة بالعلامات المسموح بها
    $allowed_tags = '<p><br><a><strong><em><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><img>';
    
    // تنظيف النص من العلامات غير المسموح بها
    $string = strip_tags($string, $allowed_tags);
    
    // إزالة السمات الخطرة من العلامات
    $string = preg_replace('/<(.*?)javascript:(.*?)>(.*?)<\/(.*?)>/i', '', $string);
    $string = preg_replace('/<(.*?)onclick=(.*?)>(.*?)<\/(.*?)>/i', '', $string);
    $string = preg_replace('/<(.*?)onerror=(.*?)>(.*?)<\/(.*?)>/i', '', $string);
    
    return $string;
}

/**
 * دالة للتحقق من صحة الجلسة
 * 
 * @return bool نتيجة التحقق
 */
function is_session_valid() {
    // التحقق من وجود معرف الجلسة
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    // التحقق من تطابق عنوان IP
    if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        return false;
    }
    
    // التحقق من وقت انتهاء الجلسة
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        // إذا مر 30 دقيقة بدون نشاط
        session_unset();
        session_destroy();
        return false;
    }
    
    // تحديث وقت آخر نشاط
    $_SESSION['last_activity'] = time();
    
    return true;
}

/**
 * دالة لتسجيل محاولات تسجيل الدخول الفاشلة
 * 
 * @param string $username اسم المستخدم
 * @param string $ip عنوان IP
 * @return void
 */
function log_failed_login($username, $ip) {
    // يمكن تخزين هذه المعلومات في قاعدة البيانات
    $log_file = BASEPATH . '/logs/failed_logins.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] محاولة فاشلة لتسجيل الدخول: المستخدم: $username, IP: $ip\n";
    
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

/**
 * دالة للتحقق من عدد محاولات تسجيل الدخول الفاشلة
 * 
 * @param string $ip عنوان IP
 * @param int $max_attempts الحد الأقصى للمحاولات
 * @param int $timeframe الإطار الزمني بالثواني
 * @return bool هل تم تجاوز الحد الأقصى
 */
function is_brute_force($ip, $max_attempts = 5, $timeframe = 300) {
    $log_file = BASEPATH . '/logs/failed_logins.log';
    
    if (!file_exists($log_file)) {
        return false;
    }
    
    $logs = file($log_file);
    $count = 0;
    $current_time = time();
    
    foreach ($logs as $log) {
        if (strpos($log, "IP: $ip") !== false) {
            $log_time = strtotime(substr($log, 1, 19));
            if (($current_time - $log_time) <= $timeframe) {
                $count++;
            }
        }
    }
    
    return $count >= $max_attempts;
}

/**
 * دالة لتوليد رمز تحقق من خطوتين
 * 
 * @return string رمز التحقق
 */
function generate_2fa_code() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * دالة للتحقق من صحة رمز التحقق من خطوتين
 * 
 * @param string $input_code الرمز المدخل
 * @param string $stored_code الرمز المخزن
 * @param int $expiry_minutes مدة صلاحية الرمز بالدقائق
 * @return bool نتيجة التحقق
 */
function verify_2fa_code($input_code, $stored_code, $generated_time, $expiry_minutes = 10) {
    // التحقق من انتهاء صلاحية الرمز
    if (time() - $generated_time > ($expiry_minutes * 60)) {
        return false;
    }
    
    // التحقق من تطابق الرمز
    return $input_code === $stored_code;
}