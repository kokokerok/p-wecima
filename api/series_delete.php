<?php
/**
 * API لحذف مسلسل
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

// استلام معرف المسلسل
$series_id = (int)($_POST['series_id'] ?? 0);

if ($series_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'معرف المسلسل غير صحيح']);
    exit;
}

// الاتصال بقاعدة البيانات
$conn = dbConnect();

// الحصول على معلومات المسلسل قبل الحذف
$stmt = $conn->prepare("SELECT poster FROM series WHERE id = ?");
$stmt->bind_param("i", $series_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'المسلسل غير موجود']);
    $stmt->close();
    $conn->close();
    exit;
}

$seriesData = $result->fetch_assoc();
$poster = $seriesData['poster'];

// بدء المعاملة
$conn->begin_transaction();

try {
    // حذف الأوسمة المرتبطة بالمسلسل
    $deleteTagsStmt = $conn->prepare("DELETE FROM series_tags WHERE series_id = ?");
    $deleteTagsStmt->bind_param("i", $series_id);
    $deleteTagsStmt->execute();
    
    // حذف الحلقات المرتبطة بالمسلسل
    $deleteEpisodesStmt = $conn->prepare("DELETE FROM episodes WHERE series_id = ?");
    $deleteEpisodesStmt->bind_param("i", $series_id);
    $deleteEpisodesStmt->execute();
    
    // حذف المواسم المرتبطة بالمسلسل
    $deleteSeasonsStmt = $conn->prepare("DELETE FROM seasons WHERE series_id = ?");
    $deleteSeasonsStmt->bind_param("i", $series_id);
    $deleteSeasonsStmt->execute();
    
    // حذف التعليقات المرتبطة بالمسلسل
    $deleteCommentsStmt = $conn->prepare("DELETE FROM comments WHERE series_id = ?");
    $deleteCommentsStmt->bind_param("i", $series_id);
    $deleteCommentsStmt->execute();
    
    // حذف المسلسل نفسه
    $deleteSeriesStmt = $conn->prepare("DELETE FROM series WHERE id = ?");
    $deleteSeriesStmt->bind_param("i", $series_id);
    $deleteSeriesStmt->execute();
    
    // تأكيد المعاملة
    $conn->commit();
    
    // حذف الصور المرتبطة بالمسلسل
    if (!empty($poster) && file_exists($poster)) {
        unlink($poster);
        
        // حذف الصورة المصغرة
        $thumb = str_replace('posters', 'thumbs', $poster);
        if (file_exists($thumb)) {
            unlink($thumb);
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'تم حذف المسلسل بنجاح']);
} catch (Exception $e) {
    // التراجع عن المعاملة في حالة حدوث خطأ
    $conn->rollback();
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء حذف المسلسل: ' . $e->getMessage()]);
}

$conn->close();
?>