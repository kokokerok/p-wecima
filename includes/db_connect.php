<?php
/**
 * ملف الاتصال بقاعدة البيانات
 * يستخدم هذا الملف لإنشاء اتصال بقاعدة البيانات واستخدامه في جميع أنحاء التطبيق
 */

// معلومات الاتصال بقاعدة البيانات
$db_host = 'localhost';      // اسم المضيف
$db_user = 'wecima_user';    // اسم المستخدم
$db_pass = 'wecima_pass';    // كلمة المرور
$db_name = 'wecima_db';      // اسم قاعدة البيانات

// إنشاء اتصال بقاعدة البيانات
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// التحقق من نجاح الاتصال
if (!$conn) {
    // تسجيل الخطأ في ملف السجل
    error_log('فشل الاتصال بقاعدة البيانات: ' . mysqli_connect_error());
    
    // عرض رسالة خطأ للمستخدم
    die('
        <div style="text-align: center; font-family: Arial, sans-serif; margin-top: 100px; direction: rtl;">
            <h1 style="color: #e50914;">خطأ في الاتصال بقاعدة البيانات</h1>
            <p>نعتذر، حدث خطأ أثناء محاولة الاتصال بقاعدة البيانات.</p>
            <p>يرجى المحاولة مرة أخرى لاحقًا أو الاتصال بمسؤول النظام.</p>
        </div>
    ');
}

// ضبط ترميز الاتصال إلى UTF-8
mysqli_set_charset($conn, 'utf8mb4');

/**
 * دالة لتنفيذ استعلام آمن باستخدام الاستعلامات المعدة
 * 
 * @param string $query الاستعلام مع علامات الاستفهام للقيم
 * @param string $types أنواع البيانات (s للنصوص، i للأرقام الصحيحة، d للأرقام العشرية، b للبيانات الثنائية)
 * @param array $params مصفوفة القيم التي سيتم تمريرها للاستعلام
 * @return mysqli_stmt كائن الاستعلام المعد
 */
function prepareQuery($query, $types = '', $params = []) {
    global $conn;
    
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        error_log('خطأ في إعداد الاستعلام: ' . mysqli_error($conn));
        return false;
    }
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    return $stmt;
}

/**
 * دالة لتنفيذ استعلام آمن وإرجاع النتائج كمصفوفة
 * 
 * @param string $query الاستعلام مع علامات الاستفهام للقيم
 * @param string $types أنواع البيانات
 * @param array $params مصفوفة القيم
 * @return array مصفوفة النتائج
 */
function executeQuery($query, $types = '', $params = []) {
    $stmt = prepareQuery($query, $types, $params);
    
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        error_log('خطأ في تنفيذ الاستعلام: ' . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $data;
}

/**
 * دالة لتنفيذ استعلام آمن وإرجاع صف واحد فقط
 * 
 * @param string $query الاستعلام مع علامات الاستفهام للقيم
 * @param string $types أنواع البيانات
 * @param array $params مصفوفة القيم
 * @return array|null مصفوفة تمثل الصف أو null إذا لم يتم العثور على نتائج
 */
function executeQuerySingle($query, $types = '', $params = []) {
    $result = executeQuery($query, $types, $params);
    
    if ($result && count($result) > 0) {
        return $result[0];
    }
    
    return null;
}

/**
 * دالة لتنفيذ استعلام إدراج أو تحديث أو حذف وإرجاع عدد الصفوف المتأثرة
 * 
 * @param string $query الاستعلام مع علامات الاستفهام للقيم
 * @param string $types أنواع البيانات
 * @param array $params مصفوفة القيم
 * @return int|bool عدد الصفوف المتأثرة أو false في حالة الخطأ
 */
function executeNonQuery($query, $types = '', $params = []) {
    $stmt = prepareQuery($query, $types, $params);
    
    if (!$stmt) {
        return false;
    }
    
    $result = mysqli_stmt_execute($stmt);
    
    if (!$result) {
        error_log('خطأ في تنفيذ الاستعلام: ' . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
    
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    
    return $affected_rows;
}

/**
 * دالة للحصول على معرف آخر صف تم إدراجه
 * 
 * @return int معرف آخر صف تم إدراجه
 */
function getLastInsertId() {
    global $conn;
    return mysqli_insert_id($conn);
}

/**
 * دالة لتنظيف البيانات المدخلة لمنع هجمات حقن SQL
 * 
 * @param string $data البيانات المراد تنظيفها
 * @return string البيانات بعد التنظيف
 */
function sanitizeInput($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}