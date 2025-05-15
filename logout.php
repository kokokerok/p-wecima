<?php
// بدء الجلسة
session_start();

// تسجيل وقت الخروج في قاعدة البيانات إذا كان المستخدم مسجل دخوله
if (isset($_SESSION['admin_id'])) {
    // استدعاء ملف الاتصال بقاعدة البيانات
    require_once 'includes/db_connect.php';
    
    // تحديث وقت آخر نشاط
    $admin_id = $_SESSION['admin_id'];
    $query = "UPDATE admins SET last_activity = NOW() WHERE id = $admin_id";
    mysqli_query($conn, $query);
}

// حذف جميع متغيرات الجلسة
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

// إعادة التوجيه إلى صفحة تسجيل الدخول
header("Location: login.php");
exit();
?>