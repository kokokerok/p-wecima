<?php
/**
 * API لإضافة فيلم جديد
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
$title = sanitizeInput($_POST['title'] ?? '');
$original_title = sanitizeInput($_POST['original_title'] ?? '');
$description = cleanHTML($_POST['description'] ?? '');
$year = (int)($_POST['year'] ?? 0);
$duration = (int)($_POST['duration'] ?? 0);
$category_id = (int)($_POST['category_id'] ?? 0);
$rating = (float)($_POST['rating'] ?? 0);
$trailer_url = sanitizeInput($_POST['trailer_url'] ?? '');
$status = sanitizeInput($_POST['status'] ?? 'pending');
$featured = isset($_POST['featured']) ? 1 : 0;
$tags = sanitizeInput($_POST['tags'] ?? '');

// التحقق من البيانات المطلوبة
if (empty($title) || empty($description) || empty($year) || empty($category_id)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'جميع الحقول المطلوبة يجب ملؤها']);
    exit;
}

// إنشاء الرابط الصديق لمحركات البحث
$slug = createSlug($title);

// معالجة الصورة
$poster_path = '';
if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
    $poster_path = uploadImage($_FILES['poster'], '../uploads/posters/');
    if (!$poster_path) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'فشل في رفع صورة الملصق']);
        exit;
    }
    
    // إنشاء صورة مصغرة
    createThumbnail($poster_path, '../uploads/posters/thumbs/');
}

// معالجة صورة الخلفية
$backdrop_path = '';
if (isset($_FILES['backdrop']) && $_FILES['backdrop']['error'] === UPLOAD_ERR_OK) {
    $backdrop_path = uploadImage($_FILES['backdrop'], '../uploads/backdrops/');
    if (!$backdrop_path) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'فشل في رفع صورة الخلفية']);
        exit;
    }
}

// إدخال البيانات في قاعدة البيانات
try {
    $stmt = $conn->prepare("INSERT INTO movies (title, original_title, slug, description, year, duration, 
                           category_id, rating, poster_path, backdrop_path, trailer_url, status, featured, 
                           tags, created_at, updated_at) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    
    $stmt->bind_param("ssssiiidssssis", $title, $original_title, $slug, $description, $year, $duration, 
                     $category_id, $rating, $poster_path, $backdrop_path, $trailer_url, $status, $featured, $tags);
    
    if ($stmt->execute()) {
        $movie_id = $conn->insert_id;
        
        // معالجة الأنواع المتعددة
        if (isset($_POST['genres']) && is_array($_POST['genres'])) {
            foreach ($_POST['genres'] as $genre_id) {
                $genre_id = (int)$genre_id;
                $stmt = $conn->prepare("INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $movie_id, $genre_id);
                $stmt->execute();
            }
        }
        
        // معالجة روابط المشاهدة
        if (isset($_POST['watch_links']) && is_array($_POST['watch_links'])) {
            foreach ($_POST['watch_links'] as $link) {
                $server_name = sanitizeInput($link['server_name'] ?? '');
                $url = sanitizeInput($link['url'] ?? '');
                
                if (!empty($server_name) && !empty($url)) {
                    $stmt = $conn->prepare("INSERT INTO movie_links (movie_id, server_name, url, type) VALUES (?, ?, ?, 'watch')");
                    $stmt->bind_param("iss", $movie_id, $server_name, $url);
                    $stmt->execute();
                }
            }
        }
        
        // معالجة روابط التحميل
        if (isset($_POST['download_links']) && is_array($_POST['download_links'])) {
            foreach ($_POST['download_links'] as $link) {
                $server_name = sanitizeInput($link['server_name'] ?? '');
                $url = sanitizeInput($link['url'] ?? '');
                $quality = sanitizeInput($link['quality'] ?? '');
                
                if (!empty($server_name) && !empty($url)) {
                    $stmt = $conn->prepare("INSERT INTO movie_links (movie_id, server_name, url, quality, type) VALUES (?, ?, ?, ?, 'download')");
                    $stmt->bind_param("isss", $movie_id, $server_name, $url, $quality);
                    $stmt->execute();
                }
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'تم إضافة الفيلم بنجاح', 'movie_id' => $movie_id]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'فشل في إضافة الفيلم: ' . $stmt->error]);
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'حدث خطأ: ' . $e->getMessage()]);
}
?>