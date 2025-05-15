<?php
/**
 * API للحصول على إحصائيات الموقع
 * 
 * يستخدم هذا الملف للحصول على إحصائيات مختلفة عن الموقع
 */

// تضمين ملف الدوال المساعدة
require_once '../includes/functions.php';

// التحقق من تسجيل الدخول
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'غير مصرح لك بالوصول إلى هذه الصفحة'
    ]);
    exit;
}

// التحقق من طريقة الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'طريقة الطلب غير صحيحة'
    ]);
    exit;
}

// الحصول على نوع الإحصائيات المطلوبة
$statsType = isset($_GET['type']) ? sanitizeInput($_GET['type']) : 'general';

// الاتصال بقاعدة البيانات
$conn = dbConnect();

// تحضير البيانات
$data = [];

switch ($statsType) {
    case 'general':
        // الإحصائيات العامة
        $data = getSiteStats();
        break;
        
    case 'movies':
        // إحصائيات الأفلام
        $data['total'] = 0;
        $data['active'] = 0;
        $data['inactive'] = 0;
        $data['by_category'] = [];
        $data['by_year'] = [];
        $data['latest'] = [];
        
        // إجمالي عدد الأفلام
        $result = $conn->query("SELECT COUNT(*) as count FROM movies");
        if ($result && $row = $result->fetch_assoc()) {
            $data['total'] = (int)$row['count'];
        }
        
        // عدد الأفلام النشطة وغير النشطة
        $result = $conn->query("SELECT status, COUNT(*) as count FROM movies GROUP BY status");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if ($row['status'] == 'active') {
                    $data['active'] = (int)$row['count'];
                } else {
                    $data['inactive'] = (int)$row['count'];
                }
            }
        }
        
        // عدد الأفلام حسب التصنيف
        $result = $conn->query("SELECT c.name, COUNT(m.id) as count 
                               FROM movies m 
                               JOIN categories c ON m.category_id = c.id 
                               GROUP BY c.id 
                               ORDER BY count DESC 
                               LIMIT 10");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data['by_category'][] = [
                    'name' => $row['name'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        // عدد الأفلام حسب السنة
        $result = $conn->query("SELECT year, COUNT(*) as count 
                               FROM movies 
                               GROUP BY year 
                               ORDER BY year DESC 
                               LIMIT 10");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data['by_year'][] = [
                    'year' => $row['year'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        // أحدث الأفلام
        $data['latest'] = getLatestMovies(5);
        break;
        
    case 'series':
        // إحصائيات المسلسلات
        $data['total'] = 0;
        $data['active'] = 0;
        $data['inactive'] = 0;
        $data['by_category'] = [];
        $data['by_year'] = [];
        $data['latest'] = [];
        
        // إجمالي عدد المسلسلات
        $result = $conn->query("SELECT COUNT(*) as count FROM series");
        if ($result && $row = $result->fetch_assoc()) {
            $data['total'] = (int)$row['count'];
        }
        
        // عدد المسلسلات النشطة وغير النشطة
        $result = $conn->query("SELECT status, COUNT(*) as count FROM series GROUP BY status");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if ($row['status'] == 'active') {
                    $data['active'] = (int)$row['count'];
                } else {
                    $data['inactive'] = (int)$row['count'];
                }
            }
        }
        
        // عدد المسلسلات حسب التصنيف
        $result = $conn->query("SELECT c.name, COUNT(s.id) as count 
                               FROM series s 
                               JOIN categories c ON s.category_id = c.id 
                               GROUP BY c.id 
                               ORDER BY count DESC 
                               LIMIT 10");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data['by_category'][] = [
                    'name' => $row['name'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        // عدد المسلسلات حسب السنة
        $result = $conn->query("SELECT year, COUNT(*) as count 
                               FROM series 
                               GROUP BY year 
                               ORDER BY year DESC 
                               LIMIT 10");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data['by_year'][] = [
                    'year' => $row['year'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        // أحدث المسلسلات
        $data['latest'] = getLatestSeries(5);
        break;
        
    case 'users':
        // إحصائيات المستخدمين
        $data['total'] = 0;
        $data['active'] = 0;
        $data['inactive'] = 0;
        $data['by_role'] = [];
        $data['by_month'] = [];
        $data['latest'] = [];
        
        // إجمالي عدد المستخدمين
        $result = $conn->query("SELECT COUNT(*) as count FROM users");
        if ($result && $row = $result->fetch_assoc()) {
            $data['total'] = (int)$row['count'];
        }
        
        // عدد المستخدمين النشطين وغير النشطين
        $result = $conn->query("SELECT status, COUNT(*) as count FROM users GROUP BY status");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if ($row['status'] == 'active') {
                    $data['active'] = (int)$row['count'];
                } else {
                    $data['inactive'] = (int)$row['count'];
                }
            }
        }
        
        // عدد المستخدمين حسب الدور
        $result = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data['by_role'][] = [
                    'role' => $row['role'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        // عدد المستخدمين الجدد حسب الشهر (آخر 12 شهر)
        $result = $conn->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                               FROM users 
                               WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) 
                               GROUP BY month 
                               ORDER BY month");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data['by_month'][] = [
                    'month' => $row['month'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        // أحدث المستخدمين
        $data['latest'] = getLatestUsers(5);
        break;
        
    case 'comments':
        // إحصائيات التعليقات
        $data['total'] = 0;
        $data['approved'] = 0;
        $data['pending'] = 0;
        $data['by_content_type'] = [];
        $data['by_month'] = [];
        $data['latest'] = [];
        
        // إجمالي عدد التعليقات
        $result = $conn->query("SELECT COUNT(*) as count FROM comments");
        if ($result && $row = $result->fetch_assoc()) {
            $data['total'] = (int)$row['count'];
        }
        
        // عدد التعليقات المعتمدة وقيد المراجعة
        $result = $conn->query("SELECT status, COUNT(*) as count FROM comments GROUP BY status");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if ($row['status'] == 'approved') {
                    $data['approved'] = (int)$row['count'];
                } else {
                    $data['pending'] = (int)$row['count'];
                }
            }
        }
        
        // عدد التعليقات حسب نوع المحتوى
        $result = $conn->query("SELECT 
                                  CASE 
                                      WHEN movie_id IS NOT NULL THEN 'movie' 
                                      WHEN series_id IS NOT NULL THEN 'series' 
                                  END as content_type, 
                                  COUNT(*) as count 
                                FROM comments 
                                GROUP BY content_type");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data['by_content_type'][] = [
                    'type' => $row['content_type'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        // عدد التعليقات حسب الشهر (آخر 12 شهر)
        $result = $conn->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                               FROM comments 
                               WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) 
                               GROUP BY month 
                               ORDER BY month");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data['by_month'][] = [
                    'month' => $row['month'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        // أحدث التعليقات
        $data['latest'] = getLatestComments(5);
        break;
        
    case 'traffic':
        // إحصائيات حركة المرور
        $data['total_visits'] = 0;
        $data['unique_visitors'] = 0;
        $data['by_page'] = [];
        $data['by_day'] = [];
        $data['by_device'] = [];
        $data['by_browser'] = [];
        
        // إجمالي عدد الزيارات والزوار الفريدين
        $result = $conn->query("SELECT 
                                  COUNT(*) as total_visits,
                                  COUNT(DISTINCT visitor_id) as unique_visitors
                                FROM visits
                                WHERE visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        if ($result && $row = $result->fetch_assoc()) {
            $data['total_visits'] = (int)$row['total_visits'];
            $data['unique_visitors'] = (int)$row['unique_visitors'];
        }
        
        // الصفحات الأكثر زيارة
        $result = $conn->query("SELECT page_url, COUNT(*) as count 
                               FROM visits 
                               WHERE visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                               GROUP BY page_url 
                               ORDER BY count DESC 
                               LIMIT 10");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data['by_page'][] = [
                    'page' => $row['page_url'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        // الزيارات حسب اليوم (آخر 30 يوم)
        $result = $conn->query("SELECT DATE(visit_date) as day, COUNT(*) as count 
                               FROM visits 
                               WHERE visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                               GROUP BY day 
                               ORDER BY day");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data['by_day'][] = [
                    'day' => $row['day'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        // الزيارات حسب نوع الجهاز
        $result = $conn->query("SELECT device_type, COUNT(*) as count 
                               FROM visits 
                               WHERE visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                               GROUP BY device_type 
                               ORDER BY count DESC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data['by_device'][] = [
                    'device' => $row['device_type'],
                    'count' => (int)$row['count']
                ];
            }
        }
        
        // الزيارات حسب المتصفح
        $result = $conn->query("SELECT browser, COUNT(*) as count 
                               FROM visits 
                               WHERE visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                               GROUP BY browser 
                               ORDER BY count DESC 
                               LIMIT 5");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data['by_browser'][] = [
                    'browser' => $row['browser'],
                    'count' => (int)$row['count']
                ];
            }
        }
        break;
}

// إرجاع البيانات بتنسيق JSON
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'data' => $data
]);
?>