<?php
/**
 * API لتغيير حالة المستخدم
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
$user_id = (int)($_POST['user_id'] ?? 0);
$status = sanitizeInput($_POST['status'] ?? '');

// التحقق من البيانات
if ($user_id <= 0 || !in_array($status, ['active', 'inactive', 'banned'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'البيانات غير صحيحة']);
    exit;
}

// الاتصال بقاعدة البيانات
$conn = dbConnect();

// التحقق من وجود المستخدم
$checkStmt = $conn->prepare("SELECT id, role FROM users WHERE id = ?");
$checkStmt->bind_param("i", $user_id);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'المستخدم غير موجود']);
    $checkStmt->close();
    $conn->close();
    exit;
}

$userData = $result->fetch_assoc();

// منع تغيير حالة المدير
if ($userData['role'] === 'admin' && $_SESSION['user_id'] !== $user_id) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'لا يمكن تغيير حالة المدير']);
    $checkStmt->close();
    $conn->close();
    exit;
}

// تحديث حالة المستخدم
$stmt = $conn->prepare("UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $status, $user_id);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'تم تحديث حالة المستخدم بنجاح']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء تحديث حالة المستخدم: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>