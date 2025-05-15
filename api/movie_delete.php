<?php
/**
 * API لحذف فيلم
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
$stmt = $conn->prepare("SELECT poster_path, backdrop_path FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'الفيلم غير موجود']);
    exit;
}

$movie = $result->fetch_assoc();

try {
    // بدء المعاملة
    $conn->begin_transaction();
    
    // حذف الأنواع المرتبطة بالفيلم
    $stmt = $conn->prepare("DELETE FROM movie_genres WHERE movie_id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    
    // حذف روابط المشاهدة والتحميل
    $stmt = $conn->prepare("DELETE FROM movie_links WHERE movie_id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    
    // حذف التعليقات المرتبطة بالفيلم
    $stmt = $conn->prepare("DELETE FROM comments WHERE movie_id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    
    // حذف الفيلم
    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $movie_id);
    
    if ($stmt->execute()) {
        // إتمام المعاملة
        $conn->commit();
        
        // حذف الصور المرتبطة بالفيلم
        if (!empty($movie['poster_path']) && file_exists($movie['poster_path'])) {
            unlink($movie['poster_path']);
            
            // حذف الصورة المصغرة
            $thumb_path = str_replace('posters/', 'posters/thumbs/', $movie['poster_path']);
            if (file_exists($thumb_path)) {
                unlink($thumb_path);
            }
        }
        
        if (!empty($movie['backdrop_path']) && file_exists($movie['backdrop_path'])) {
            unlink($movie['backdrop_path']);
        }
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'تم حذف الفيلم بنجاح']);
    } else {
        // التراجع عن المعاملة في حالة الفشل
        $conn->rollback();
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'فشل في حذف الفيلم: ' . $stmt->error]);
    }
} catch (Exception $e) {
    // التراجع عن المعاملة في حالة حدوث استثناء
    $conn->rollback();
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'حدث خطأ: ' . $e->getMessage()]);
}
?>