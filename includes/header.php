<?php
// بدء الجلسة
session_start();

// التحقق من تسجيل الدخول
include_once 'auth.php';
checkLogin();

// استدعاء ملف الدوال المساعدة
include_once 'functions.php';

// الحصول على معلومات المستخدم الحالي
$admin_id = $_SESSION['admin_id'];
$admin_info = getAdminInfo($admin_id);

// الحصول على عنوان الصفحة الحالية
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getPageTitle($current_page); ?> - لوحة تحكم WeCima</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- CSS الرئيسي -->
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <!-- CSS مخصص للصفحة الحالية (إذا وجد) -->
    <?php if (file_exists("../assets/css/pages/{$current_page}.css")): ?>
    <link rel="stylesheet" href="../assets/css/pages/<?php echo $current_page; ?>.css">
    <?php endif; ?>
</head>
<body>
    <div class="admin-container">
        <!-- القائمة الجانبية -->
        <?php include_once 'sidebar.php'; ?>
        
        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <div class="header">
                <h1><?php echo getPageTitle($current_page); ?></h1>
                <div class="user-info">
                    <div class="dropdown">
                        <div class="user-details">
                            <img src="../uploads/admin/<?php echo $admin_info['image'] ?: 'default.png'; ?>" alt="صورة المستخدم">
                            <span><?php echo $admin_info['name']; ?></span>
                        </div>
                        <div class="dropdown-content">
                            <a href="profile.php"><i class="fas fa-user"></i> الملف الشخصي</a>
                            <a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- بداية محتوى الصفحة -->