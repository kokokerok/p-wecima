<?php
// بدء جلسة المستخدم
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// استيراد ملف الاتصال بقاعدة البيانات
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// معالجة حذف المسلسل
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $series_id = $_GET['delete'];
    
    // حذف المسلسل
    $delete_query = "DELETE FROM series WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $series_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "تم حذف المسلسل بنجاح";
    } else {
        $error_message = "حدث خطأ أثناء حذف المسلسل: " . $conn->error;
    }
}

// معالجة تغيير حالة المسلسل
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $series_id = $_GET['toggle_status'];
    
    // الحصول على الحالة الحالية
    $status_query = "SELECT status FROM series WHERE id = ?";
    $status_stmt = $conn->prepare($status_query);
    $status_stmt->bind_param("i", $series_id);
    $status_stmt->execute();
    $status_result = $status_stmt->get_result();
    $status_row = $status_result->fetch_assoc();
    
    // تغيير الحالة
    $new_status = ($status_row['status'] == 'active') ? 'inactive' : 'active';
    $update_status_query = "UPDATE series SET status = ? WHERE id = ?";
    $update_status_stmt = $conn->prepare($update_status_query);
    $update_status_stmt->bind_param("si", $new_status, $series_id);
    
    if ($update_status_stmt->execute()) {
        $success_message = "تم تغيير حالة المسلسل بنجاح";
    } else {
        $error_message = "حدث خطأ أثناء تغيير حالة المسلسل: " . $conn->error;
    }
}

// البحث والتصفية
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// بناء استعلام البحث
$query = "SELECT s.*, GROUP_CONCAT(c.name SEPARATOR '، ') as categories 
          FROM series s 
          LEFT JOIN series_categories sc ON s.id = sc.series_id 
          LEFT JOIN categories c ON sc.category_id = c.id";

$where_conditions = [];
$params = [];
$types = "";

if (!empty($search)) {
    $where_conditions[] = "(s.title LIKE ? OR s.description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($category)) {
    $where_conditions[] = "c.id = ?";
    $params[] = $category;
    $types .= "i";
}

if (!empty($status)) {
    $where_conditions[] = "s.status = ?";
    $params[] = $status;
    $types .= "s";
}

if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}

$query .= " GROUP BY s.id ORDER BY s.$sort $order";

// التقسيم إلى صفحات
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

$count_query = "SELECT COUNT(DISTINCT s.id) as total FROM series s 
                LEFT JOIN series_categories sc ON s.id = sc.series_id 
                LEFT JOIN categories c ON sc.category_id = c.id";

if (!empty($where_conditions)) {
    $count_query .= " WHERE " . implode(" AND ", $where_conditions);
}

$count_stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// إضافة حدود للاستعلام الرئيسي
$query .= " LIMIT ?, ?";
$params[] = $offset;
$params[] = $records_per_page;
$types .= "ii";

// تنفيذ الاستعلام
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// الحصول على قائمة التصنيفات للتصفية
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = $conn->query($categories_query);

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المسلسلات - لوحة تحكم WeCima</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- الخطوط العربية -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">
    <!-- ملف CSS الرئيسي -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <!-- القائمة الجانبية -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <!-- رأس الصفحة -->
            <?php include 'includes/header.php'; ?>
            
            <!-- رسائل النجاح والخطأ -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- عنوان الصفحة والإجراءات -->
            <div class="page-header">
                <h2>إدارة المسلسلات</h2>
                <div class="page-actions">
                    <a href="add_series.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة مسلسل جديد
                    </a>
                </div>
            </div>
            
            <!-- نموذج البحث والتصفية -->
            <div class="filter-container">
                <form action="" method="get" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <select name="category" class="form-control">
                                <option value="">جميع التصنيفات</option>
                                <?php while ($category_row = $categories_result->fetch_assoc()): ?>
                                    <option value="<?php echo $category_row['id']; ?>" <?php echo ($category == $category_row['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category_row['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <select name="status" class="form-control">
                                <option value="">جميع الحالات</option>
                                <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>نشط</option>
                                <option value="inactive" <?php echo ($status == 'inactive') ? 'selected' : ''; ?>>غير نشط</option>
                                <option value="pending" <?php echo ($status == 'pending') ? 'selected' : ''; ?>>قيد المراجعة</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <select name="sort" class="form-control">
                                <option value="created_at" <?php echo ($sort == 'created_at') ? 'selected' : ''; ?>>تاريخ الإضافة</option>
                                <option value="title" <?php echo ($sort == 'title') ? 'selected' : ''; ?>>العنوان</option>
                                <option value="year" <?php echo ($sort == 'year') ? 'selected' : ''; ?>>السنة</option>
                                <option value="rating" <?php echo ($sort == 'rating') ? 'selected' : ''; ?>>التقييم</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <select name="order" class="form-control">
                                <option value="DESC" <?php echo ($order == 'DESC') ? 'selected' : ''; ?>>تنازلي</option>
                                <option value="ASC" <?php echo ($order == 'ASC') ? 'selected' : ''; ?>>تصاعدي</option>
                            </select>
                        </div>
                        
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-filter"></i> تصفية
                            </button>
                            <a href="series.php" class="btn btn-light">
                                <i class="fas fa-sync-alt"></i> إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- جدول المسلسلات -->
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>اسم المسلسل</th>
                            <th>التصنيف</th>
                            <th>السنة</th>
                            <th>الحلقات</th>
                            <th>التقييم</th>
                            <th>الحالة</th>
                            <th>تاريخ الإضافة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $counter = $offset + 1; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td>
                                    <td>
                                        <img src="<?php echo !empty($row['poster']) ? $row['poster'] : 'assets/images/no-poster.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                             class="table-image">
                                    </td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['categories'] ?? 'غير مصنف'); ?></td>
                                    <td><?php echo htmlspecialchars($row['year']); ?></td>
                                    <td>
                                        <a href="episodes.php?series_id=<?php echo $row['id']; ?>" class="badge">
                                            <?php echo $row['episodes_count'] ?? 0; ?> حلقة
                                        </a>
                                    </td>
                                    <td><?php echo number_format($row['rating'], 1); ?></td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        $status_text = '';
                                        
                                        switch ($row['status']) {
                                            case 'active':
                                                $status_class = 'active';
                                                $status_text = 'نشط';
                                                break;
                                            case 'inactive':
                                                $status_class = 'inactive';
                                                $status_text = 'غير نشط';
                                                break;
                                            case 'pending':
                                                $status_class = 'pending';
                                                $status_text = 'قيد المراجعة';
                                                break;
                                            default:
                                                $status_class = 'inactive';
                                                $status_text = 'غير معروف';
                                        }
                                        ?>
                                        <span class="status <?php echo $status_class; ?>">
                                            <?php echo $status_text; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y/m/d', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="view_series.php?id=<?php echo $row['id']; ?>" class="action-btn view" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit_series.php?id=<?php echo $row['id']; ?>" class="action-btn edit" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="episodes.php?series_id=<?php echo $row['id']; ?>" class="action-btn episodes" title="الحلقات">
                                                <i class="fas fa-list"></i>
                                            </a>
                                            <a href="series.php?toggle_status=<?php echo $row['id']; ?>" class="action-btn status" title="تغيير الحالة">
                                                <?php if ($row['status'] == 'active'): ?>
                                                    <i class="fas fa-toggle-on"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-toggle-off"></i>
                                                <?php endif; ?>
                                            </a>
                                            <a href="series.php?delete=<?php echo $row['id']; ?>" class="action-btn delete" title="حذف" 
                                               onclick="return confirm('هل أنت متأكد من حذف هذا المسلسل؟')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="no-data">لا توجد مسلسلات متاحة</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- ترقيم الصفحات -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode($status) : ''; ?><?php echo !empty($sort) ? '&sort=' . urlencode($sort) : ''; ?><?php echo !empty($order) ? '&order=' . urlencode($order) : ''; ?>" class="page-link">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                        <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode($status) : ''; ?><?php echo !empty($sort) ? '&sort=' . urlencode($sort) : ''; ?><?php echo !empty($order) ? '&order=' . urlencode($order) : ''; ?>" class="page-link">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++):
                    ?>
                        <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode($status) : ''; ?><?php echo !empty($sort) ? '&sort=' . urlencode($sort) : ''; ?><?php echo !empty($order) ? '&order=' . urlencode($order) : ''; ?>" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode($status) : ''; ?><?php echo !empty($sort) ? '&sort=' . urlencode($sort) : ''; ?><?php echo !empty($order) ? '&order=' . urlencode($order) : ''; ?>" class="page-link">
                            <i class="fas fa-angle-left"></i>
                        </a>
                        <a href="?page=<?php echo $total_pages; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode($status) : ''; ?><?php echo !empty($sort) ? '&sort=' . urlencode($sort) : ''; ?><?php echo !empty($order) ? '&order=' . urlencode($order) : ''; ?>" class="page-link">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- ملفات JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>