<?php
/**
 * API لرفع الصور
 * 
 * يستخدم هذا الملف لرفع الصور إلى الخادم
 */

// تضمين ملف الدوال المساعدة
require_once '../includes/functions.php';

// التحقق من تسجيل الدخول
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'غير مصرح لك بالوصول إلى هذه الصفحة'
    ]);
    exit;
}

// التحقق من طريقة الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'طريقة الطلب غير صحيحة'
    ]);
    exit;
}

// التحقق من وجود ملف
if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'لم يتم تحديد أي ملف للرفع'
    ]);
    exit;
}

// الحصول على نوع الصورة (poster, thumbnail, etc.)
$imageType = isset($_POST['type']) ? sanitizeInput($_POST['type']) : 'general';

// تحديد مجلد الحفظ حسب نوع الصورة
switch ($imageType) {
    case 'poster':
        $uploadDir = '../uploads/posters/';
        break;
    case 'thumbnail':
        $uploadDir = '../uploads/thumbnails/';
        break;
    case 'backdrop':
        $uploadDir = '../uploads/backdrops/';
        break;
    case 'profile':
        $uploadDir = '../uploads/profiles/';
        break;
    default:
        $uploadDir = '../uploads/images/';
        break;
}

// التأكد من وجود المجلد
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// تحديد أنواع الملفات المسموح بها
$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

// تحديد الحجم الأقصى للملف (2 ميجابايت)
$maxSize = 2 * 1024 * 1024;

// رفع الصورة
$uploadedFile = uploadImage($_FILES['image'], $uploadDir, $allowedTypes, $maxSize);

if ($uploadedFile) {
    // إنشاء صورة مصغرة إذا كان نوع الصورة poster أو backdrop
    $thumbnailPath = null;
    if ($imageType === 'poster' || $imageType === 'backdrop') {
        $thumbsDir = '../uploads/thumbs/';
        $thumbnailPath = createThumbnail($uploadedFile, $thumbsDir, 300, 200);
    }
    
    // إرجاع رابط الصورة
    $imageUrl = str_replace('../', '', $uploadedFile);
    $thumbnailUrl = $thumbnailPath ? str_replace('../', '', $thumbnailPath) : null;
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'تم رفع الصورة بنجاح',
        'data' => [
            'image_url' => $imageUrl,
            'thumbnail_url' => $thumbnailUrl,
            'image_type' => $imageType
        ]
    ]);
} else {
    // في حالة فشل رفع الصورة
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'فشل في رفع الصورة. تأكد من أن الصورة بتنسيق صحيح وحجمها لا يتجاوز 2 ميجابايت'
    ]);
}
?>