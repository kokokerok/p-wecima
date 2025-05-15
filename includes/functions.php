<?php
/**
 * ملف الدوال المساعدة
 * 
 * يحتوي هذا الملف على مجموعة من الدوال المساعدة المستخدمة في موقع WeCima
 */

// التأكد من بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * دالة الاتصال بقاعدة البيانات
 * 
 * @return mysqli كائن الاتصال بقاعدة البيانات
 */
function dbConnect() {
    $host = 'localhost';
    $username = 'wecima_user';
    $password = 'wecima_password';
    $database = 'wecima_db';
    
    $conn = new mysqli($host, $username, $password, $database);
    
    // التحقق من وجود أخطاء في الاتصال
    if ($conn->connect_error) {
        die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
    }
    
    // تعيين ترميز الاتصال إلى UTF-8
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// إنشاء متغير عام للاتصال بقاعدة البيانات
$conn = dbConnect();

/**
 * دالة تنظيف المدخلات
 * 
 * @param string $data البيانات المراد تنظيفها
 * @return string البيانات بعد التنظيف
 */
function sanitizeInput($data) {
    global $conn;
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    
    if ($conn) {
        $data = $conn->real_escape_string($data);
    }
    
    return $data;
}

/**
 * دالة التحقق من صحة البريد الإلكتروني
 * 
 * @param string $email البريد الإلكتروني المراد التحقق منه
 * @return bool نتيجة التحقق
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * دالة التحقق من صحة رقم الهاتف
 * 
 * @param string $phone رقم الهاتف المراد التحقق منه
 * @return bool نتيجة التحقق
 */
function isValidPhone($phone) {
    // التحقق من أن رقم الهاتف يتكون من أرقام فقط ويتراوح طوله بين 10 و15 رقم
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

/**
 * دالة إنشاء رابط صديق لمحركات البحث
 * 
 * @param string $string النص المراد تحويله إلى رابط
 * @return string الرابط الصديق لمحركات البحث
 */
function createSlug($string) {
    // تحويل الحروف إلى حروف صغيرة
    $string = mb_strtolower($string, 'UTF-8');
    
    // استبدال الأحرف العربية بأحرف إنجليزية مقابلة
    $arabic_replacements = [
        'أ' => 'a', 'إ' => 'a', 'آ' => 'a', 'ا' => 'a',
        'ب' => 'b', 'ت' => 't', 'ث' => 'th',
        'ج' => 'j', 'ح' => 'h', 'خ' => 'kh',
        'د' => 'd', 'ذ' => 'th',
        'ر' => 'r', 'ز' => 'z',
        'س' => 's', 'ش' => 'sh', 'ص' => 's', 'ض' => 'd',
        'ط' => 't', 'ظ' => 'z',
        'ع' => 'a', 'غ' => 'gh',
        'ف' => 'f', 'ق' => 'q', 'ك' => 'k',
        'ل' => 'l', 'م' => 'm', 'ن' => 'n',
        'ه' => 'h', 'و' => 'w', 'ي' => 'y', 'ى' => 'a',
        'ة' => 'a', 'ء' => ''
    ];
    
    foreach ($arabic_replacements as $ar => $en) {
        $string = str_replace($ar, $en, $string);
    }
    
    // استبدال المسافات والأحرف الخاصة بشرطات
    $string = preg_replace('/[^a-z0-9]/', '-', $string);
    
    // إزالة الشرطات المتكررة
    $string = preg_replace('/-+/', '-', $string);
    
    // إزالة الشرطات من بداية ونهاية النص
    $string = trim($string, '-');
    
    return $string;
}

/**
 * دالة تنسيق التاريخ بالعربية
 * 
 * @param string $date التاريخ بصيغة Y-m-d
 * @return string التاريخ بالصيغة العربية
 */
function formatArabicDate($date) {
    $timestamp = strtotime($date);
    
    $months = [
        'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
        'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
    ];
    
    $day = date('j', $timestamp);
    $month = $months[date('n', $timestamp) - 1];
    $year = date('Y', $timestamp);
    
    return $day . ' ' . $month . ' ' . $year;
}

/**
 * دالة تحويل التاريخ إلى صيغة "منذ"
 * 
 * @param string $datetime التاريخ والوقت
 * @return string الوقت المنقضي بصيغة "منذ"
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return 'منذ ' . $diff . ' ثانية';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return 'منذ ' . $minutes . ' دقيقة' . ($minutes > 1 ? '' : '');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return 'منذ ' . $hours . ' ساعة' . ($hours > 1 ? '' : '');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return 'منذ ' . $days . ' يوم' . ($days > 1 ? '' : '');
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return 'منذ ' . $weeks . ' أسبوع' . ($weeks > 1 ? '' : '');
    } elseif ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return 'منذ ' . $months . ' شهر' . ($months > 1 ? '' : '');
    } else {
        $years = floor($diff / 31536000);
        return 'منذ ' . $years . ' سنة' . ($years > 1 ? '' : '');
    }
}
/**
 * دالة تحميل الصور
 * 
 * @param array $file ملف الصورة المرفوع
 * @param string $destination مسار حفظ الصورة
 * @param array $allowedTypes أنواع الملفات المسموح بها
 * @param int $maxSize الحجم الأقصى للملف بالبايت
 * @return string|false مسار الصورة بعد التحميل أو false في حالة الفشل
 */
function uploadImage($file, $destination = 'uploads/images/', $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'], $maxSize = 2097152) {
    // التحقق من وجود أخطاء في التحميل
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // التحقق من نوع الملف
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }
    
    // التحقق من حجم الملف
    if ($file['size'] > $maxSize) {
        return false;
    }
    
    // إنشاء اسم فريد للملف
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid() . '.' . $fileExtension;
    $uploadPath = $destination . $newFileName;
    
    // التأكد من وجود المجلد
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // تحميل الملف
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return $uploadPath;
    }
    
    return false;
}

/**
 * دالة تغيير حجم الصورة
 * 
 * @param string $sourcePath مسار الصورة الأصلية
 * @param string $targetPath مسار الصورة الجديدة
 * @param int $width العرض المطلوب
 * @param int $height الارتفاع المطلوب
 * @param bool $crop هل يتم قص الصورة أم لا
 * @return bool نتيجة العملية
 */
function resizeImage($sourcePath, $targetPath, $width, $height, $crop = false) {
    // الحصول على معلومات الصورة
    list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourcePath);
    
    // إنشاء الصورة المصدر حسب نوعها
    switch ($sourceType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }
    
    // حساب الأبعاد الجديدة
    if ($crop) {
        // حساب نسبة العرض إلى الارتفاع
        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $width / $height;
        
        if ($sourceRatio > $targetRatio) {
            $newWidth = $sourceHeight * $targetRatio;
            $newHeight = $sourceHeight;
            $srcX = ($sourceWidth - $newWidth) / 2;
            $srcY = 0;
        } else {
            $newWidth = $sourceWidth;
            $newHeight = $sourceWidth / $targetRatio;
            $srcX = 0;
            $srcY = ($sourceHeight - $newHeight) / 2;
        }
        
        $targetImage = imagecreatetruecolor($width, $height);
        
        // الحفاظ على الشفافية للصور PNG
        if ($sourceType === IMAGETYPE_PNG) {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefilledrectangle($targetImage, 0, 0, $width, $height, $transparent);
        }
        
        imagecopyresampled($targetImage, $sourceImage, 0, 0, $srcX, $srcY, $width, $height, $newWidth, $newHeight);
    } else {
        // حساب الأبعاد مع الحفاظ على النسبة
        if ($sourceWidth / $sourceHeight > $width / $height) {
            $newWidth = $width;
            $newHeight = $sourceHeight * ($width / $sourceWidth);
        } else {
            $newWidth = $sourceWidth * ($height / $sourceHeight);
            $newHeight = $height;
        }
        
        $targetImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // الحفاظ على الشفافية للصور PNG
        if ($sourceType === IMAGETYPE_PNG) {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefilledrectangle($targetImage, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
    }
    
    // حفظ الصورة الجديدة
    $result = false;
    switch ($sourceType) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($targetImage, $targetPath, 90);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($targetImage, $targetPath, 9);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($targetImage, $targetPath);
            break;
    }
    
    // تحرير الذاكرة
    imagedestroy($sourceImage);
    imagedestroy($targetImage);
    
    return $result;
}

/**
 * دالة إنشاء صورة مصغرة
 * 
 * @param string $sourcePath مسار الصورة الأصلية
 * @param string $thumbsDir مجلد الصور المصغرة
 * @param int $width العرض المطلوب
 * @param int $height الارتفاع المطلوب
 * @return string|false مسار الصورة المصغرة أو false في حالة الفشل
 */
function createThumbnail($sourcePath, $thumbsDir = 'uploads/thumbs/', $width = 300, $height = 200) {
    // التأكد من وجود المجلد
    if (!is_dir($thumbsDir)) {
        mkdir($thumbsDir, 0755, true);
    }
    
    // إنشاء اسم الصورة المصغرة
    $fileName = basename($sourcePath);
    $thumbPath = $thumbsDir . $fileName;
    
    // تغيير حجم الصورة
    if (resizeImage($sourcePath, $thumbPath, $width, $height, true)) {
        return $thumbPath;
    }
    
    return false;
}

/**
 * دالة تنظيف النص من الوسوم HTML
 * 
 * @param string $text النص المراد تنظيفه
 * @param array $allowedTags الوسوم المسموح بها
 * @return string النص بعد التنظيف
 */
function cleanHTML($text, $allowedTags = '<p><br><strong><em><ul><ol><li><a><h2><h3><h4><blockquote>') {
    // إزالة جميع الوسوم ما عدا المسموح بها
    $text = strip_tags($text, $allowedTags);
    
    // تنظيف السمات غير المرغوب فيها
    $text = preg_replace('/(<[^>]+) style=("[^"]*"|\'[^\']*\'|[^\s>]*)/i', '$1', $text);
    $text = preg_replace('/(<[^>]+) class=("[^"]*"|\'[^\']*\'|[^\s>]*)/i', '$1', $text);
    $text = preg_replace('/(<[^>]+) id=("[^"]*"|\'[^\']*\'|[^\s>]*)/i', '$1', $text);
    
    // تنظيف الروابط
    $text = preg_replace_callback('/<a([^>]*)>(.*?)<\/a>/i', function($matches) {
        $attributes = $matches[1];
        $content = $matches[2];
        
        // الاحتفاظ فقط بسمة href
        $href = '';
        if (preg_match('/href=("[^"]*"|\'[^\']*\'|[^\s>]*)/i', $attributes, $hrefMatch)) {
            $href = $hrefMatch[0];
        }
        
        // إضافة سمة target="_blank" للروابط الخارجية
        if (strpos($href, 'http') !== false && strpos($href, $_SERVER['HTTP_HOST']) === false) {
            return '<a ' . $href . ' target="_blank" rel="noopener noreferrer">' . $content . '</a>';
        }
        
        return '<a ' . $href . '>' . $content . '</a>';
    }, $text);
    
    return $text;
}

/**
 * دالة اختصار النص
 * 
 * @param string $text النص المراد اختصاره
 * @param int $length الطول المطلوب
 * @param string $append النص المضاف في نهاية النص المختصر
 * @return string النص بعد الاختصار
 */
function truncateText($text, $length = 150, $append = '...') {
    // إزالة الوسوم HTML
    $text = strip_tags($text);
    
    if (mb_strlen($text, 'UTF-8') > $length) {
        $text = mb_substr($text, 0, $length, 'UTF-8');
        
        // التأكد من عدم قطع الكلمة
        $lastSpace = mb_strrpos($text, ' ', 0, 'UTF-8');
        if ($lastSpace !== false) {
            $text = mb_substr($text, 0, $lastSpace, 'UTF-8');
        }
        
        $text .= $append;
    }
    
    return $text;
}

/**
 * دالة تحويل النص إلى HTML
 * 
 * @param string $text النص المراد تحويله
 * @return string النص بعد التحويل
 */
function nl2p($text) {
    // تقسيم النص إلى فقرات
    $paragraphs = preg_split('/\n\s*\n|\r\n\s*\r\n|\r\s*\r/m', $text);
    
    // تحويل كل فقرة إلى وسم <p>
    $paragraphs = array_map(function($paragraph) {
        return '<p>' . str_replace(["\r\n", "\r", "\n"], '<br>', trim($paragraph)) . '</p>';
    }, $paragraphs);
    
    return implode('', $paragraphs);
}

/**
 * دالة تحويل الروابط النصية إلى روابط قابلة للنقر
 * 
 * @param string $text النص المراد تحويله
 * @return string النص بعد التحويل
 */
function makeClickableLinks($text) {
    // تحويل الروابط النصية إلى روابط قابلة للنقر
    $pattern = '/(https?:\/\/[^\s]+)/i';
    $replacement = '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>';
    
    return preg_replace($pattern, $replacement, $text);
}
/**
 * دالة الحصول على إحصائيات الموقع
 * 
 * @return array مصفوفة تحتوي على إحصائيات الموقع
 */
function getSiteStats() {
    global $conn;
    
    $stats = [
        'movies' => 0,
        'series' => 0,
        'users' => 0,
        'comments' => 0
    ];
    
    // عدد الأفلام
    $result = $conn->query("SELECT COUNT(*) as count FROM movies");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['movies'] = $row['count'];
    }
    
    // عدد المسلسلات
    $result = $conn->query("SELECT COUNT(*) as count FROM series");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['series'] = $row['count'];
    }
    
    // عدد المستخدمين
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['users'] = $row['count'];
    }
    
    // عدد التعليقات
    $result = $conn->query("SELECT COUNT(*) as count FROM comments");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['comments'] = $row['count'];
    }
    
    return $stats;
}

/**
 * دالة الحصول على أحدث الأفلام
 * 
 * @param int $limit عدد الأفلام المطلوبة
 * @return array مصفوفة تحتوي على بيانات الأفلام
 */
function getLatestMovies($limit = 5) {
    global $conn;
    
    $movies = [];
    
    $stmt = $conn->prepare("SELECT m.*, c.name as category_name 
                           FROM movies m 
                           LEFT JOIN categories c ON m.category_id = c.id 
                           ORDER BY m.created_at DESC 
                           LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
    
    return $movies;
}

/**
 * دالة الحصول على أحدث المسلسلات
 * 
 * @param int $limit عدد المسلسلات المطلوبة
 * @return array مصفوفة تحتوي على بيانات المسلسلات
 */
function getLatestSeries($limit = 5) {
    global $conn;
    
    $series = [];
    
    $stmt = $conn->prepare("SELECT s.*, c.name as category_name 
                           FROM series s 
                           LEFT JOIN categories c ON s.category_id = c.id 
                           ORDER BY s.created_at DESC 
                           LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $series[] = $row;
    }
    
    return $series;
}

/**
 * دالة الحصول على أحدث المستخدمين
 * 
 * @param int $limit عدد المستخدمين المطلوبين
 * @return array مصفوفة تحتوي على بيانات المستخدمين
 */
function getLatestUsers($limit = 5) {
    global $conn;
    
    $users = [];
    
    $stmt = $conn->prepare("SELECT id, username, email, role, created_at, last_login, status 
                           FROM users 
                           ORDER BY created_at DESC 
                           LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    return $users;
}

/**
 * دالة الحصول على أحدث التعليقات
 * 
 * @param int $limit عدد التعليقات المطلوبة
 * @return array مصفوفة تحتوي على بيانات التعليقات
 */
function getLatestComments($limit = 5) {
    global $conn;
    
    $comments = [];
    
    $stmt = $conn->prepare("SELECT c.*, u.username, 
                           CASE 
                               WHEN c.movie_id IS NOT NULL THEN m.title 
                               WHEN c.series_id IS NOT NULL THEN s.title 
                           END as content_title 
                           FROM comments c 
                           LEFT JOIN users u ON c.user_id = u.id 
                           LEFT JOIN movies m ON c.movie_id = m.id 
                           LEFT JOIN series s ON c.series_id = s.id 
                           ORDER BY c.created_at DESC 
                           LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    
    return $comments;
}

/**
 * دالة البحث في الموقع
 * 
 * @param string $keyword كلمة البحث
 * @param string $type نوع البحث (all, movies, series)
 * @return array نتائج البحث
 */
function searchContent($keyword, $type = 'all') {
    global $conn;
    
    $results = [
        'movies' => [],
        'series' => []
    ];
    
    $keyword = "%$keyword%";
    
    // البحث في الأفلام
    if ($type === 'all' || $type === 'movies') {
        $stmt = $conn->prepare("SELECT m.*, c.name as category_name 
                               FROM movies m 
                               LEFT JOIN categories c ON m.category_id = c.id 
                               WHERE m.title LIKE ? OR m.description LIKE ? 
                               ORDER BY m.created_at DESC");
        $stmt->bind_param("ss", $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $results['movies'][] = $row;
        }
    }
    
    // البحث في المسلسلات
    if ($type === 'all' || $type === 'series') {
        $stmt = $conn->prepare("SELECT s.*, c.name as category_name 
                               FROM series s 
                               LEFT JOIN categories c ON s.category_id = c.id 
                               WHERE s.title LIKE ? OR s.description LIKE ? 
                               ORDER BY s.created_at DESC");
        $stmt->bind_param("ss", $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $results['series'][] = $row;
        }
    }
    
    return $results;
}

/**
 * دالة إنشاء تنبيه
 * 
 * @param string $message نص التنبيه
 * @param string $type نوع التنبيه (success, warning, danger, info)
 * @return string كود HTML للتنبيه
 */
function createAlert($message, $type = 'info') {
    $icon = '';
    
    switch ($type) {
        case 'success':
            $icon = '<i class="fas fa-check-circle"></i>';
            break;
        case 'warning':
            $icon = '<i class="fas fa-exclamation-triangle"></i>';
            break;
        case 'danger':
            $icon = '<i class="fas fa-times-circle"></i>';
            break;
        case 'info':
        default:
            $icon = '<i class="fas fa-info-circle"></i>';
            break;
    }
    
    return '<div class="alert alert-' . $type . '">' . $icon . ' ' . $message . '</div>';
}

/**
 * دالة إنشاء رسالة تنبيه في الجلسة
 * 
 * @param string $message نص التنبيه
 * @param string $type نوع التنبيه (success, warning, danger, info)
 * @return void
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * دالة عرض رسالة التنبيه من الجلسة
 * 
 * @return string|null كود HTML للتنبيه أو null إذا لم توجد رسالة
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        
        return createAlert($flash['message'], $flash['type']);
    }
    
    return null;
}

/**
 * دالة التحقق من وجود قيمة في مصفوفة
 * 
 * @param string $key المفتاح
 * @param array $array المصفوفة
 * @param mixed $default القيمة الافتراضية
 * @return mixed القيمة الموجودة أو القيمة الافتراضية
 */
function getValue($key, $array, $default = '') {
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * دالة تحويل التاريخ إلى صيغة قاعدة البيانات
 * 
 * @param string $date التاريخ
 * @return string التاريخ بصيغة Y-m-d
 */
function formatDateForDB($date) {
    return date('Y-m-d', strtotime($date));
}

/**
 * دالة تحويل التاريخ والوقت إلى صيغة قاعدة البيانات
 * 
 * @param string $datetime التاريخ والوقت
 * @return string التاريخ والوقت بصيغة Y-m-d H:i:s
 */
function formatDateTimeForDB($datetime) {
    return date('Y-m-d H:i:s', strtotime($datetime));
}

/**
 * دالة إنشاء رابط صفحة
 * 
 * @param string $page اسم الصفحة
 * @param array $params المعلمات
 * @return string الرابط
 */
function createPageLink($page, $params = []) {
    $url = $page;
    
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    return $url;
}

/**
 * دالة إنشاء عناصر الترقيم
 * 
 * @param int $currentPage الصفحة الحالية
 * @param int $totalPages إجمالي عدد الصفحات
 * @param string $baseUrl الرابط الأساسي
 * @return string كود HTML لعناصر الترقيم
 */
function createPagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<div class="pagination">';
    
    // زر الصفحة السابقة
    if ($currentPage > 1) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" class="page-link prev"><i class="fas fa-chevron-right"></i></a>';
    } else {
        $html .= '<span class="page-link prev disabled"><i class="fas fa-chevron-right"></i></span>';
    }
    
    // أرقام الصفحات
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $html .= '<a href="' . $baseUrl . '?page=1" class="page-link">1</a>';
        if ($startPage > 2) {
            $html .= '<span class="page-link dots">...</span>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="page-link active">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="page-link">' . $i . '</a>';
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $html .= '<span class="page-link dots">...</span>';
        }
        $html .= '<a href="' . $baseUrl . '?page=' . $totalPages . '" class="page-link">' . $totalPages . '</a>';
    }
    
    // زر الصفحة التالية
    if ($currentPage < $totalPages) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" class="page-link next"><i class="fas fa-chevron-left"></i></a>';
    } else {
        $html .= '<span class="page-link next disabled"><i class="fas fa-chevron-left"></i></span>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * دالة تحويل النص إلى HTML آمن
 * 
 * @param string $text النص المراد تحويله
 * @return string النص بعد التحويل
 */
function safeHTML($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * دالة الحصول على اسم الشهر بالعربية
 * 
 * @param int $month رقم الشهر
 * @return string اسم الشهر بالعربية
 */
function getArabicMonth($month) {
    $months = [
        1 => 'يناير',
        2 => 'فبراير',
        3 => 'مارس',
        4 => 'أبريل',
        5 => 'مايو',
        6 => 'يونيو',
        7 => 'يوليو',
        8 => 'أغسطس',
        9 => 'سبتمبر',
        10 => 'أكتوبر',
        11 => 'نوفمبر',
        12 => 'ديسمبر'
    ];
    
    return $months[$month] ?? '';
}

/**
 * دالة الحصول على اسم اليوم بالعربية
 * 
 * @param int $day رقم اليوم (0 للأحد، 6 للسبت)
 * @return string اسم اليوم بالعربية
 */
function getArabicDay($day) {
    $days = [
        0 => 'الأحد',
        1 => 'الإثنين',
        2 => 'الثلاثاء',
        3 => 'الأربعاء',
        4 => 'الخميس',
        5 => 'الجمعة',
        6 => 'السبت'
    ];
    
    return $days[$day] ?? '';
}

// إغلاق الاتصال بقاعدة البيانات عند انتهاء تنفيذ الصفحة
register_shutdown_function(function() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
});
?>