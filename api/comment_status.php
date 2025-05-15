<?php
/**
 * API لتغيير حالة التعليق
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
$comment_id = (int)($_POST['comment_id'] ?? 0);
$status = sanitizeInput($_POST['status'] ?? '');

// التحقق من البيانات
if ($comment_id <= 0 || !in_array($status, ['approved', 'pending', 'spam'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'البيانات غير صحيحة']);
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

// تحديث حالة التعليق
$stmt = $conn->prepare("UPDATE comments SET status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $status, $comment_id);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'تم تحديث حالة التعليق بنجاح']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء تحديث حالة التعليق: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>