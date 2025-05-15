<?php
/**
 * API لتعديل فيلم
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

// استلام معرف الفيلم
$movie_id = (int)($_POST['movie_id'] ?? 0);

if ($movie_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'معرف الفيلم غير صحيح']);
    exit;
}

// التحقق من وجود الفيلم
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'الفيلم غير موجود']);
    exit;
}

$movie = $result->fetch_assoc();

// استلام البيانات
$title = sanitizeInput($_POST['title'] ?? $movie['title']);
$original_title = sanitizeInput($_POST['original_title'] ?? $movie['original_title']);
$description = cleanHTML($_POST['description'] ?? $movie['description']);
$year = (int)($_POST['year'] ?? $movie['year']);
$duration = (int)($_POST['duration'] ?? $movie['duration']);
$category_id = (int)($_POST['category_id'] ?? $movie['category_id']);
$rating = (float)($_POST['rating'] ?? $movie['rating']);
$trailer_url = sanitizeInput($_POST['trailer_url'] ?? $movie['trailer_url']);
$status = sanitizeInput($_POST['status'] ?? $movie['status']);
$featured = isset($_POST['featured']) ? 1 : (int)$movie['featured'];
$tags = sanitizeInput($_POST['tags'] ?? $movie['tags']);

// التحقق من البيانات المطلوبة
if (empty($title) || empty($description) || empty($year) || empty($category_id)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'جميع الحقول المطلوبة يجب ملؤها']);
    exit;
}

// إنشاء الرابط الصديق لمحركات البحث إذا تم تغيير العنوان
$slug = $movie['slug'];
if ($title !== $movie['title']) {
    $slug = createSlug($title);
}

// معالجة الصورة
$poster_path = $movie['poster_path'];
if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
    $new_poster_path = uploadImage($_FILES['poster'], '../uploads/posters/');
    if ($new_poster_path) {
        // حذف الصورة القديمة إذا كانت موجودة
        if (!empty($poster_path) && file_exists($poster_path)) {
            unlink($poster_path);
            
            // حذف الصورة المصغرة القديمة
            $old_thumb = str_replace('posters/', 'posters/thumbs/', $poster_path);
            if (file_exists($old_thumb)) {
                unlink($old_thumb);
            }
        }
        
        $poster_path = $new_poster_path;
        
        // إنشاء صورة مصغرة جديدة
        createThumbnail($poster_path, '../uploads/posters/thumbs/');
    }
}

// معالجة صورة الخلفية
$backdrop_path = $movie['backdrop_path'];
if (isset($_FILES['backdrop']) && $_FILES['backdrop']['error'] === UPLOAD_ERR_OK) {
    $new_backdrop_path = uploadImage($_FILES['backdrop'], '../uploads/backdrops/');
    if ($new_backdrop_path) {
        // حذف الصورة القديمة إذا كانت موجودة
        if (!empty($backdrop_path) && file_exists($backdrop_path)) {
            unlink($backdrop_path);
        }
        
        $backdrop_path = $new_backdrop_path;
    }
}

// تحديث البيانات في قاعدة البيانات
try {
    $stmt = $conn->prepare("UPDATE movies SET title = ?, original_title = ?, slug = ?, description = ?, 
                           year = ?, duration = ?, category_id = ?, rating = ?, poster_path = ?, 
                           backdrop_path = ?, trailer_url = ?, status = ?, featured = ?, tags = ?, 
                           updated_at = NOW() WHERE id = ?");
    
    $stmt->bind_param("ssssiiidssssisi", $title, $original_title, $slug, $description, $year, $duration, 
                     $category_id, $rating, $poster_path, $backdrop_path, $trailer_url, $status, $featured, 
                     $tags, $movie_id);
    
    if ($stmt->execute()) {
        // حذف الأنواع القديمة
        $stmt = $conn->prepare("DELETE FROM movie_genres WHERE movie_id = ?");
        $stmt->bind_param("i", $movie_id);
        $stmt->execute();
        
        // إضافة الأنواع الجديدة
        if (isset($_POST['genres']) && is_array($_POST['genres'])) {
            foreach ($_POST['genres'] as $genre_id) {
                $genre_id = (int)$genre_id;
                $stmt = $conn->prepare("INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $movie_id, $genre_id);
                $stmt->execute();
            }
        }
        
        // معالجة روابط المشاهدة
        if (isset($_POST['update_links']) && $_POST['update_links'] === 'yes') {
            // حذف الروابط القديمة
            $stmt = $conn->prepare("DELETE FROM movie_links WHERE movie_id = ? AND type = 'watch'");
            $stmt->bind_param("i", $movie_id);
            $stmt->execute();
            
            // إضافة الروابط الجديدة
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
        }
        
        // معالجة روابط التحميل
        if (isset($_POST['update_download_links']) && $_POST['update_download_links'] === 'yes') {
            // حذف الروابط القديمة
            $stmt = $conn->prepare("DELETE FROM movie_links WHERE movie_id = ? AND type = 'download'");
            $stmt->bind_param("i", $movie_id);
            $stmt->execute();
            
            // إضافة الروابط الجديدة
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
        }
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'تم تحديث الفيلم بنجاح']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'فشل في تحديث الفيلم: ' . $stmt->error]);
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'حدث خطأ: ' . $e->getMessage()]);
}
?>