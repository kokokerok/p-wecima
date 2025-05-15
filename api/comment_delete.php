<?php
/**
 * API لحذف تعليق
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

// استلام معرف التعليق
$comment_id = (int)($_POST['comment_id'] ?? 0);

if ($comment_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'معرف التعليق غير صحيح']);
    exit;
}

// الاتصال بقاعدة البيانات
$conn = dbConnect();

// التحقق من وجود التعليق
$checkStmt = $conn->prepare("SELECT id FROM comments WHERE id = ?");
$checkStmt->bind_param("i", $comment_id);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'التعليق غير موجود']);
    $checkStmt->close();
    $conn->close();
    exit;
}

// حذف التعليق
$stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'تم حذف التعليق بنجاح']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء حذف التعليق: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>