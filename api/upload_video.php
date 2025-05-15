<?php
/**
 * API لرفع الفيديوهات
 * 
 * يستخدم هذا الملف لرفع الفيديوهات إلى الخادم
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
if (!isset($_FILES['video']) || $_FILES['video']['error'] === UPLOAD_ERR_NO_FILE) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'لم يتم تحديد أي ملف للرفع'
    ]);
    exit;
}

// الحصول على نوع الفيديو (movie, episode, trailer, etc.)
$videoType = isset($_POST['type']) ? sanitizeInput($_POST['type']) : 'general';

// الحصول على معرف المحتوى (فيلم أو مسلسل)
$contentId = isset($_POST['content_id']) ? (int)$_POST['content_id'] : 0;

// الحصول على رقم الحلقة (في حالة المسلسلات)
$episodeNumber = isset($_POST['episode_number']) ? (int)$_POST['episode_number'] : null;

// الحصول على رقم الموسم (في حالة المسلسلات)
$seasonNumber = isset($_POST['season_number']) ? (int)$_POST['season_number'] : null;

// تحديد مجلد الحفظ حسب نوع الفيديو
switch ($videoType) {
    case 'movie':
        $uploadDir = '../uploads/movies/';
        break;
    case 'episode':
        $uploadDir = '../uploads/series/';
        if ($seasonNumber) {
            $uploadDir .= 'season_' . $seasonNumber . '/';
        }
        break;
    case 'trailer':
        $uploadDir = '../uploads/trailers/';
        break;
    default:
        $uploadDir = '../uploads/videos/';
        break;
}

// التأكد من وجود المجلد
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// تحديد أنواع الملفات المسموح بها
$allowedTypes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];

// تحديد الحجم الأقصى للملف (500 ميجابايت)
$maxSize = 500 * 1024 * 1024;

// التحقق من نوع الملف
if (!in_array($_FILES['video']['type'], $allowedTypes)) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'نوع الملف غير مسموح به. الأنواع المسموح بها هي: MP4, WebM, OGG, QuickTime'
    ]);
    exit;
}

// التحقق من حجم الملف
if ($_FILES['video']['size'] > $maxSize) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'حجم الملف كبير جدًا. الحد الأقصى هو 500 ميجابايت'
    ]);
    exit;
}

// إنشاء اسم فريد للملف
$fileExtension = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
$fileName = '';

// تحديد اسم الملف حسب نوع الفيديو
if ($videoType === 'movie') {
    $fileName = 'movie_' . $contentId . '_' . uniqid() . '.' . $fileExtension;
} elseif ($videoType === 'episode' && $episodeNumber) {
    $fileName = 'series_' . $contentId . '_s' . $seasonNumber . 'e' . $episodeNumber . '_' . uniqid() . '.' . $fileExtension;
} elseif ($videoType === 'trailer') {
    $fileName = 'trailer_' . $contentId . '_' . uniqid() . '.' . $fileExtension;
} else {
    $fileName = 'video_' . uniqid() . '.' . $fileExtension;
}

$uploadPath = $uploadDir . $fileName;

// رفع الملف
if (move_uploaded_file($_FILES['video']['tmp_name'], $uploadPath)) {
    // إدخال معلومات الفيديو في قاعدة البيانات
    $videoUrl = str_replace('../', '', $uploadPath);
    $videoTitle = isset($_POST['title']) ? sanitizeInput($_POST['title']) : '';
    $videoDescription = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';
    
    // إضافة الفيديو إلى قاعدة البيانات
    $conn = dbConnect();
    
    if ($videoType === 'movie') {
        // تحديث رابط الفيديو في جدول الأفلام
        $stmt = $conn->prepare("UPDATE movies SET video_url = ? WHERE id = ?");
        $stmt->bind_param("si", $videoUrl, $contentId);
        $stmt->execute();
    } elseif ($videoType === 'episode' && $episodeNumber) {
        // إضافة حلقة جديدة
        $stmt = $conn->prepare("INSERT INTO episodes (series_id, season_number, episode_number, title, description, video_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisss", $contentId, $seasonNumber, $episodeNumber, $videoTitle, $videoDescription, $videoUrl);
        $stmt->execute();
    } elseif ($videoType === 'trailer') {
        // تحديث رابط الإعلان التشويقي
        if (strpos($contentId, 'movie_') === 0) {
            $movieId = substr($contentId, 6);
            $stmt = $conn->prepare("UPDATE movies SET trailer_url = ? WHERE id = ?");
            $stmt->bind_param("si", $videoUrl, $movieId);
        } else {
            $seriesId = substr($contentId, 7);
            $stmt = $conn->prepare("UPDATE series SET trailer_url = ? WHERE id = ?");
            $stmt->bind_param("si", $videoUrl, $seriesId);
        }
        $stmt->execute();
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'تم رفع الفيديو بنجاح',
        'data' => [
            'video_url' => $videoUrl,
            'video_type' => $videoType,
            'content_id' => $contentId,
            'file_name' => $fileName
        ]
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'فشل في رفع الفيديو'
    ]);
}
?>