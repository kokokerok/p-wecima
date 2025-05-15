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

// حذف فيلم
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $movie_id = $_GET['delete'];
    
    // حذف الصور المرتبطة بالفيلم
    $image_query = "SELECT poster, cover FROM movies WHERE id = ?";
    $image_stmt = $conn->prepare($image_query);
    $image_stmt->bind_param("i", $movie_id);
    $image_stmt->execute();
    $image_result = $image_stmt->get_result();
    
    if ($image_row = $image_result->fetch_assoc()) {
        if (!empty($image_row['poster']) && file_exists($image_row['poster'])) {
            unlink($image_row['poster']);
        }
        if (!empty($image_row['cover']) && file_exists($image_row['cover'])) {
            unlink($image_row['cover']);
        }
    }
    
    // حذف العلاقات مع الممثلين
    $delete_actors = "DELETE FROM movie_actors WHERE movie_id = ?";
    $delete_actors_stmt = $conn->prepare($delete_actors);
    $delete_actors_stmt->bind_param("i", $movie_id);
    $delete_actors_stmt->execute();
    
    // حذف العلاقات مع التصنيفات
    $delete_categories = "DELETE FROM movie_categories WHERE movie_id = ?";
    $delete_categories_stmt = $conn->prepare($delete_categories);
    $delete_categories_stmt->bind_param("i", $movie_id);
    $delete_categories_stmt->execute();
    
    // حذف التعليقات
    $delete_comments = "DELETE FROM comments WHERE movie_id = ?";
    $delete_comments_stmt = $conn->prepare($delete_comments);
    $delete_comments_stmt->bind_param("i", $movie_id);
    $delete_comments_stmt->execute();
    
    // حذف الفيلم
    $delete_query = "DELETE FROM movies WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $movie_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "تم حذف الفيلم بنجاح";
    } else {
        $error_message = "حدث خطأ أثناء حذف الفيلم: " . $conn->error;
    }
}

// تغيير حالة الفيلم
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $movie_id = $_GET['toggle_status'];
    
    // الحصول على الحالة الحالية
    $status_query = "SELECT status FROM movies WHERE id = ?";
    $status_stmt = $conn->prepare($status_query);
    $status_stmt->bind_param("i", $movie_id);
    $status_stmt->execute();
    $status_result = $status_stmt->get_result();
    $status_row = $status_result->fetch_assoc();
    
    // تبديل الحالة
    $new_status = ($status_row['status'] == 'active') ? 'inactive' : 'active';
    
    $update_status = "UPDATE movies SET status = ? WHERE id = ?";
    $update_status_stmt = $conn->prepare($update_status);
    $update_status_stmt->bind_param("si", $new_status, $movie_id);
    
    if ($update_status_stmt->execute()) {
        $success_message = "تم تغيير حالة الفيلم بنجاح";
    } else {
        $error_message = "حدث خطأ أثناء تغيير حالة الفيلم: " . $conn->error;
    }
}

// البحث والتصفية
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// بناء استعلام البحث
$query = "SELECT m.*, GROUP_CONCAT(DISTINCT c.name SEPARATOR '، ') as categories 
          FROM movies m 
          LEFT JOIN movie_categories mc ON m.id = mc.movie_id 
          LEFT JOIN categories c ON mc.category_id = c.id";

$where_conditions = [];
$params = [];
$types = "";

if (!empty($search)) {
    $where_conditions[] = "(m.title LIKE ? OR m.description LIKE ?)";
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
    $where_conditions[] = "m.status = ?";
    $params[] = $status;
    $types .= "s";
}

if (!empty($year)) {
    $where_conditions[] = "m.year = ?";
    $params[] = $year;
    $types .= "i";
}

if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}

$query .= " GROUP BY m.id ORDER BY m.created_at DESC";

// الصفحات
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// الحصول على إجمالي عدد السجلات
$count_query = "SELECT COUNT(DISTINCT m.id) as total FROM movies m 
                LEFT JOIN movie_categories mc ON m.id = mc.movie_id 
                LEFT JOIN categories c ON mc.category_id = c.id";

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

// إضافة حدود الصفحة إلى الاستعلام الرئيسي
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

// الحصول على قائمة السنوات للتصفية
$years_query = "SELECT DISTINCT year FROM movies ORDER BY year DESC";
$years_result = $conn->query($years_query);
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الأفلام - لوحة تحكم WeCima</title>
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
            
            <!-- عنوان الصفحة وزر الإضافة -->
            <div class="page-header">
                <h2>إدارة الأفلام</h2>
                <a href="add_movie.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة فيلم جديد
                </a>
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
                            <select name="year" class="form-control">
                                <option value="">جميع السنوات</option>
                                <?php while ($year_row = $years_result->fetch_assoc()): ?>
                                    <option value="<?php echo $year_row['year']; ?>" <?php echo ($year == $year_row['year']) ? 'selected' : ''; ?>>
                                        <?php echo $year_row['year']; ?>
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
                        
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-secondary">تصفية</button>
                            <a href="movies.php" class="btn btn-light">إعادة تعيين</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- جدول الأفلام -->
            <div class="data-table">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الصورة</th>
                                <th>اسم الفيلم</th>
                                <th>التصنيف</th>
                                <th>السنة</th>
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
                                                 class="thumbnail">
                                        </td>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['categories'] ?? 'غير مصنف'); ?></td>
                                        <td><?php echo $row['year']; ?></td>
                                        <td><?php echo $row['rating']; ?></td>
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
                                            }
                                            ?>
                                            <span class="status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        </td>
                                        <td><?php echo date('Y/m/d', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <div class="action-btns">
                                                <a href="view_movie.php?id=<?php echo $row['id']; ?>" class="action-btn view" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit_movie.php?id=<?php echo $row['id']; ?>" class="action-btn edit" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="movies.php?toggle_status=<?php echo $row['id']; ?>" class="action-btn status" title="تغيير الحالة">
                                                    <i class="fas fa-toggle-on"></i>
                                                </a>
                                                <a href="movies.php?delete=<?php echo $row['id']; ?>" class="action-btn delete" title="حذف" 
                                                   onclick="return confirm('هل أنت متأكد من حذف هذا الفيلم؟');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="no-data">لا توجد أفلام متاحة</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- ترقيم الصفحات -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode($status) : ''; ?><?php echo !empty($year) ? '&year=' . urlencode($year) : ''; ?>" class="page-link">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                            <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode($status) : ''; ?><?php echo !empty($year) ? '&year=' . urlencode($year) : ''; ?>" class="page-link">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        for ($i = $start_page; $i <= $end_page; $i++):
                        ?>
                            <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode($status) : ''; ?><?php echo !empty($year) ? '&year=' . urlencode($year) : ''; ?>" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode($status) : ''; ?><?php echo !empty($year) ? '&year=' . urlencode($year) : ''; ?>" class="page-link">
                                <i class="fas fa-angle-left"></i>
                            </a>
                            <a href="?page=<?php echo $total_pages; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode($status) : ''; ?><?php echo !empty($year) ? '&year=' . urlencode($year) : ''; ?>" class="page-link">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- ملفات JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>
<?php
// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>