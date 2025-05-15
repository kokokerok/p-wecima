<?php
/**
 * ملف التحقق من المصادقة والصلاحيات
 * 
 * يحتوي هذا الملف على الدوال المسؤولة عن:
 * - تسجيل الدخول والخروج
 * - التحقق من حالة تسجيل الدخول
 * - التحقق من صلاحيات المستخدم
 * - إدارة الجلسات
 */

// بدء الجلسة إذا لم تكن قد بدأت بالفعل
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * دالة تسجيل الدخول
 * 
 * @param string $username اسم المستخدم
 * @param string $password كلمة المرور
 * @return bool نتيجة تسجيل الدخول (نجاح/فشل)
 */
function login($username, $password) {
    global $conn;
    
    // تنظيف البيانات المدخلة
    $username = filter_var($username, FILTER_SANITIZE_STRING);
    $password = trim($password);
    
    // التحقق من وجود المستخدم في قاعدة البيانات
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // التحقق من كلمة المرور
        if (password_verify($password, $user['password'])) {
            // تخزين بيانات المستخدم في الجلسة
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            $_SESSION['last_activity'] = time();
            
            // تحديث آخر تسجيل دخول في قاعدة البيانات
            $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
            
            return true;
        }
    }
    
    return false;
}

/**
 * دالة تسجيل الخروج
 * 
 * @return void
 */
function logout() {
    // حذف متغيرات الجلسة
    $_SESSION = array();
    
    // حذف كوكيز الجلسة
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // إنهاء الجلسة
    session_destroy();
}

/**
 * دالة التحقق من حالة تسجيل الدخول
 * 
 * @return bool حالة تسجيل الدخول
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * دالة التحقق من صلاحية المستخدم
 * 
 * @param string $requiredRole الصلاحية المطلوبة
 * @return bool نتيجة التحقق
 */
function hasRole($requiredRole) {
    if (!isLoggedIn()) {
        return false;
    }
    
    // التحقق من الصلاحية
    if ($requiredRole === 'admin' && $_SESSION['role'] === 'admin') {
        return true;
    } elseif ($requiredRole === 'editor' && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'editor')) {
        return true;
    } elseif ($requiredRole === 'user' && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'editor' || $_SESSION['role'] === 'user')) {
        return true;
    }
    
    return false;
}

/**
 * دالة التحقق من صلاحية الوصول إلى صفحة معينة
 * 
 * @param string $requiredRole الصلاحية المطلوبة للصفحة
 * @return void
 */
function requireRole($requiredRole) {
    if (!hasRole($requiredRole)) {
        // إعادة التوجيه إلى صفحة تسجيل الدخول
        header("Location: login.php?error=unauthorized");
        exit;
    }
}

/**
 * دالة التحقق من نشاط المستخدم وتحديث وقت آخر نشاط
 * 
 * @param int $timeout مدة الخمول بالثواني قبل تسجيل الخروج التلقائي (افتراضياً 30 دقيقة)
 * @return void
 */
function checkSessionActivity($timeout = 1800) {
    if (isLoggedIn()) {
        // التحقق من وقت آخر نشاط
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            // تسجيل الخروج تلقائياً بعد فترة الخمول
            logout();
            header("Location: login.php?error=timeout");
            exit;
        }
        
        // تحديث وقت آخر نشاط
        $_SESSION['last_activity'] = time();
    }
}

/**
 * دالة إنشاء رمز CSRF لحماية النماذج
 * 
 * @return string رمز CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * دالة التحقق من صحة رمز CSRF
 * 
 * @param string $token الرمز المرسل من النموذج
 * @return bool نتيجة التحقق
 */
function validateCSRFToken($token) {
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    
    return false;
}

/**
 * دالة تسجيل محاولات تسجيل الدخول الفاشلة
 * 
 * @param string $username اسم المستخدم
 * @param string $ip عنوان IP
 * @return void
 */
function logFailedLogin($username, $ip) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO login_attempts (username, ip_address, attempt_time) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $username, $ip);
    $stmt->execute();
}

/**
 * دالة التحقق من عدد محاولات تسجيل الدخول الفاشلة
 * 
 * @param string $username اسم المستخدم
 * @param string $ip عنوان IP
 * @param int $maxAttempts الحد الأقصى لعدد المحاولات (افتراضياً 5)
 * @param int $timeWindow نافذة الوقت بالدقائق (افتراضياً 30 دقيقة)
 * @return bool ما إذا كان المستخدم محظوراً أم لا
 */
function isUserBlocked($username, $ip, $maxAttempts = 5, $timeWindow = 30) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM login_attempts 
                           WHERE (username = ? OR ip_address = ?) 
                           AND attempt_time > DATE_SUB(NOW(), INTERVAL ? MINUTE)");
    $stmt->bind_param("ssi", $username, $ip, $timeWindow);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['attempts'] >= $maxAttempts;
}

/**
 * دالة تغيير كلمة المرور
 * 
 * @param int $userId معرف المستخدم
 * @param string $currentPassword كلمة المرور الحالية
 * @param string $newPassword كلمة المرور الجديدة
 * @return bool نتيجة تغيير كلمة المرور
 */
function changePassword($userId, $currentPassword, $newPassword) {
    global $conn;
    
    // التحقق من كلمة المرور الحالية
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($currentPassword, $user['password'])) {
            // تشفير كلمة المرور الجديدة
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // تحديث كلمة المرور في قاعدة البيانات
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            
            return $stmt->execute();
        }
    }
    
    return false;
}

/**
 * دالة إعادة تعيين كلمة المرور (نسيت كلمة المرور)
 * 
 * @param string $email البريد الإلكتروني للمستخدم
 * @return bool نتيجة إرسال رابط إعادة التعيين
 */
function resetPasswordRequest($email) {
    global $conn;
    
    // التحقق من وجود البريد الإلكتروني
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $userId = $user['id'];
        
        // إنشاء رمز إعادة التعيين
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // صالح لمدة ساعة
        
        // حفظ الرمز في قاعدة البيانات
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $token, $expires);
        
        if ($stmt->execute()) {
            // إرسال بريد إلكتروني برابط إعادة التعيين (يجب تنفيذ هذه الوظيفة)
            $resetLink = "https://wecima.com/reset-password.php?token=" . $token;
            // sendResetEmail($email, $resetLink);
            
            return true;
        }
    }
    
    return false;
}

/**
 * دالة التحقق من صلاحية رمز إعادة تعيين كلمة المرور
 * 
 * @param string $token الرمز
 * @return int|false معرف المستخدم أو false إذا كان الرمز غير صالح
 */
function validateResetToken($token) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT user_id FROM password_resets 
                           WHERE token = ? AND expires_at > NOW() 
                           AND used = 0 LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $reset = $result->fetch_assoc();
        return $reset['user_id'];
    }
    
    return false;
}

/**
 * دالة تعيين كلمة مرور جديدة بعد إعادة التعيين
 * 
 * @param string $token الرمز
 * @param string $newPassword كلمة المرور الجديدة
 * @return bool نتيجة تعيين كلمة المرور الجديدة
 */
function completePasswordReset($token, $newPassword) {
    global $conn;
    
    $userId = validateResetToken($token);
    
    if ($userId) {
        // تشفير كلمة المرور الجديدة
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // تحديث كلمة المرور
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        
        if ($stmt->execute()) {
            // تحديث حالة الرمز إلى مستخدم
            $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            
            return true;
        }
    }
    
    return false;
}

// تنفيذ التحقق من نشاط الجلسة عند تحميل الملف
checkSessionActivity();
?>