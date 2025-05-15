<?php
/**
 * API لتعديل مسلسل
 */

// استدعاء ملف الدوال المساعدة
require_once '../includes/functions.php';

// التحقق من تسجيل الدخول وصلاحيات المستخدم
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'غير مصرح لك بالوصول']);
    exit;
}

// التحقق من طريقة الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'طريقة الطلب غير صحيحة']);
    exit;
}

// استلام البيانات
$series_id = (int)($_POST['series_id'] ?? 0);
$title = sanitizeInput($_POST['title'] ?? '');
$description = sanitizeInput($_POST['description'] ?? '');
$category_id = (int)($_POST['category_id'] ?? 0);
$year = (int)($_POST['year'] ?? 0);
$rating = (float)($_POST['rating'] ?? 0);
$status = sanitizeInput($_POST['status'] ?? 'active');
$trailer_url = sanitizeInput($_POST['trailer_url'] ?? '');
$seasons_count = (int)($_POST['seasons_count'] ?? 0);
$episodes_count = (int)($_POST['episodes_count'] ?? 0);
$tags = sanitizeInput($_POST['tags'] ?? '');
$slug = createSlug($title);

// التحقق من البيانات
if ($series_id <= 0 || empty($title) || empty($description) || $category_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'جميع الحقول المطلوبة يجب ملؤها']);
    exit;
}

// الاتصال بقاعدة البيانات
$conn = dbConnect();

// التحقق من وجود المسلسل
$checkStmt = $conn->prepare("SELECT poster FROM series WHERE id = ?");
$checkStmt->bind_param("i", $series_id);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'المسلسل غير موجود']);
    $checkStmt->close();
    $conn->close();
    exit;
}

$seriesData = $result->fetch_assoc();
$currentPoster = $seriesData['poster'];

// معالجة الصورة
$poster_path = $currentPoster;
if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
    $poster_path = uploadImage($_FILES['poster'], '../uploads/series/posters/');
    if (!$poster_path) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'فشل في تحميل صورة الغلاف']);
        exit;
    }
    
    // إنشاء صورة مصغرة
    createThumbnail($poster_path, '../uploads/series/thumbs/', 300, 450);
    
    // حذف الصورة القديمة إذا كانت موجودة
    if (!empty($currentPoster) && file_exists($currentPoster)) {
        unlink($currentPoster);
        
        // حذف الصورة المصغرة القديمة
        $oldThumb = str_replace('posters', 'thumbs', $currentPoster);
        if (file_exists($oldThumb)) {
            unlink($oldThumb);
        }
    }
}

// تحديث بيانات المسلسل
$stmt = $conn->prepare("UPDATE series SET title = ?, description = ?, category_id = ?, year = ?, rating = ?, status = ?, poster = ?, trailer_url = ?, seasons_count = ?, episodes_count = ?, tags = ?, slug = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("ssiidsssiissi", $title, $description, $category_id, $year, $rating, $status, $poster_path, $trailer_url, $seasons_count, $episodes_count, $tags, $slug, $series_id);

if ($stmt->execute()) {
    // حذف الأوسمة القديمة
    $deleteTagsStmt = $conn->prepare("DELETE FROM series_tags WHERE series_id = ?");
    $deleteTagsStmt->bind_param("i", $series_id);
    $deleteTagsStmt->execute();
    
    // إضافة الأوسمة الجديدة
    if (!empty($tags)) {
        $tagArray = explode(',', $tags);
        foreach ($tagArray as $tag) {
            $tag = trim($tag);
            if (!empty($tag)) {
                // التحقق من وجود الوسم
                $tagStmt = $conn->prepare("SELECT id FROM tags WHERE name = ?");
                $tagStmt->bind_param("s", $tag);
                $tagStmt->execute();
                $tagResult = $tagStmt->get_result();
                
                if ($tagResult->num_rows > 0) {
                    // الوسم موجود بالفعل
                    $tagRow = $tagResult->fetch_assoc();
                    $tag_id = $tagRow['id'];
                } else {
                    // إنشاء وسم جديد
                    $newTagStmt = $conn->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)");
                    $tagSlug = createSlug($tag);
                    $newTagStmt->bind_param("ss", $tag, $tagSlug);
                    $newTagStmt->execute();
                    $tag_id = $conn->insert_id;
                }
                
                // ربط الوسم بالمسلسل
                $linkStmt = $conn->prepare("INSERT INTO series_tags (series_id, tag_id) VALUES (?, ?)");
                $linkStmt->bind_param("ii", $series_id, $tag_id);
                $linkStmt->execute();
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'تم تحديث المسلسل بنجاح']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء تحديث المسلسل: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>