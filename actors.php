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

// معالجة حذف ممثل
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $actor_id = $_GET['delete'];
    
    // التحقق من وجود الممثل
    $check_query = "SELECT id FROM actors WHERE id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $actor_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // حذف الممثل
        $delete_query = "DELETE FROM actors WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $actor_id);
        
        if ($delete_stmt->execute()) {
            // حذف العلاقات مع الأفلام والمسلسلات
            $delete_relations_query = "DELETE FROM movie_actors WHERE actor_id = ?";
            $delete_relations_stmt = $conn->prepare($delete_relations_query);
            $delete_relations_stmt->bind_param("i", $actor_id);
            $delete_relations_stmt->execute();
            
            $delete_series_relations_query = "DELETE FROM series_actors WHERE actor_id = ?";
            $delete_series_relations_stmt = $conn->prepare($delete_series_relations_query);
            $delete_series_relations_stmt->bind_param("i", $actor_id);
            $delete_series_relations_stmt->execute();
            
            $success_message = "تم حذف الممثل بنجاح";
        } else {
            $error_message = "حدث خطأ أثناء حذف الممثل: " . $conn->error;
        }
    } else {
        $error_message = "الممثل غير موجود";
    }
}

// معالجة إضافة ممثل جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_actor'])) {
    $name = trim($_POST['name']);
    $name_en = trim($_POST['name_en']);
    $bio = trim($_POST['bio']);
    $birth_date = trim($_POST['birth_date']);
    $nationality = trim($_POST['nationality']);
    
    // التحقق من البيانات
    if (empty($name) || empty($name_en)) {
        $error_message = "يرجى ملء جميع الحقول المطلوبة";
    } else {
        // التحقق من وجود الممثل
        $check_query = "SELECT id FROM actors WHERE name = ? OR name_en = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ss", $name, $name_en);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = "الممثل موجود بالفعل";
        } else {
            // معالجة الصورة
            $image_path = "";
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
                $max_size = 2 * 1024 * 1024; // 2 ميجابايت
                
                $file_type = $_FILES['image']['type'];
                $file_size = $_FILES['image']['size'];
                
                if (!in_array($file_type, $allowed_types)) {
                    $error_message = "نوع الملف غير مسموح به. يرجى استخدام JPEG أو PNG";
                } elseif ($file_size > $max_size) {
                    $error_message = "حجم الملف كبير جدًا. الحد الأقصى هو 2 ميجابايت";
                } else {
                    $upload_dir = 'uploads/actors/';
                    
                    // إنشاء المجلد إذا لم يكن موجودًا
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    // إنشاء اسم فريد للملف
                    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $file_name = uniqid() . '_' . time() . '.' . $file_extension;
                    $target_file = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        $image_path = $target_file;
                    } else {
                        $error_message = "حدث خطأ أثناء رفع الصورة";
                    }
                }
            }
            
            if (empty($error_message)) {
                // إضافة الممثل
                $insert_query = "INSERT INTO actors (name, name_en, bio, birth_date, nationality, image, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("ssssss", $name, $name_en, $bio, $birth_date, $nationality, $image_path);
                
                if ($insert_stmt->execute()) {
                    $success_message = "تم إضافة الممثل بنجاح";
                    // إعادة توجيه لتفادي إعادة إرسال النموذج
                    header("Location: actors.php?success=added");
                    exit();
                } else {
                    $error_message = "حدث خطأ أثناء إضافة الممثل: " . $conn->error;
                }
            }
        }
    }
}

// الحصول على قائمة الممثلين
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$where_clause = '';
$params = [];
$param_types = '';

if (!empty($search)) {
    $where_clause = "WHERE name LIKE ? OR name_en LIKE ?";
    $search_param = "%$search%";
    $params = [$search_param, $search_param];
    $param_types = 'ss';
}

// استعلام عدد الممثلين
$count_query = "SELECT COUNT(*) as total FROM actors $where_clause";
$count_stmt = $conn->prepare($count_query);

if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// استعلام الممثلين
$query = "SELECT * FROM actors $where_clause ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $params[] = $limit;
    $params[] = $offset;
    $param_types .= 'ii';
    $stmt->bind_param($param_types, ...$params);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$actors = $result->fetch_all(MYSQLI_ASSOC);

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الممثلين - لوحة تحكم WeCima</title>
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
            <?php if (isset($success_message) || isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                        if (isset($success_message)) {
                            echo $success_message;
                        } elseif ($_GET['success'] === 'added') {
                            echo "تم إضافة الممثل بنجاح";
                        } elseif ($_GET['success'] === 'updated') {
                            echo "تم تحديث بيانات الممثل بنجاح";
                        }
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- نموذج إضافة ممثل جديد -->
            <div class="form-container">
                <div class="form-header">
                    <h2><i class="fas fa-user-plus"></i> إضافة ممثل جديد</h2>
                </div>
                
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">اسم الممثل (بالعربية)</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="name_en">اسم الممثل (بالإنجليزية)</label>
                            <input type="text" id="name_en" name="name_en" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="birth_date">تاريخ الميلاد</label>
                            <input type="date" id="birth_date" name="birth_date" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="nationality">الجنسية</label>
                            <input type="text" id="nationality" name="nationality" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">نبذة عن الممثل</label>
                        <textarea id="bio" name="bio" class="form-control" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">صورة الممثل</label>
                        <input type="file" id="image" name="image" class="form-control">
                        <small class="form-text">الصيغ المسموح بها: JPG، JPEG، PNG. الحد الأقصى للحجم: 2 ميجابايت</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="add_actor" class="btn btn-primary">إضافة الممثل</button>
                    </div>
                </form>
            </div>
            
            <!-- جدول الممثلين -->
            <div class="data-table">
                <div class="table-header">
                    <h2><i class="fas fa-users"></i> قائمة الممثلين</h2>
                    <div class="table-actions">
                        <form action="" method="get" class="search-form">
                            <input type="text" name="search" placeholder="بحث عن ممثل..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>اسم الممثل</th>
                            <th>الاسم بالإنجليزية</th>
                            <th>الجنسية</th>
                            <th>تاريخ الميلاد</th>
                            <th>تاريخ الإضافة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($actors)): ?>
                            <tr>
                                <td colspan="8" class="text-center">لا يوجد ممثلين</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($actors as $index => $actor): ?>
                                <tr>
                                    <td><?php echo $offset + $index + 1; ?></td>
                                    <td>
                                        <img src="<?php echo !empty($actor['image']) ? $actor['image'] : 'assets/images/default-actor.jpg'; ?>" alt="<?php echo htmlspecialchars($actor['name']); ?>" class="actor-thumbnail">
                                    </td>
                                    <td><?php echo htmlspecialchars($actor['name']); ?></td>
                                    <td><?php echo htmlspecialchars($actor['name_en']); ?></td>
                                    <td><?php echo htmlspecialchars($actor['nationality'] ?? 'غير محدد'); ?></td>
                                    <td><?php echo !empty($actor['birth_date']) ? date('Y/m/d', strtotime($actor['birth_date'])) : 'غير محدد'; ?></td>
                                    <td><?php echo date('Y/m/d', strtotime($actor['created_at'])); ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="view_actor.php?id=<?php echo $actor['id']; ?>" class="action-btn view" title="عرض"><i class="fas fa-eye"></i></a>
                                            <a href="edit_actor.php?id=<?php echo $actor['id']; ?>" class="action-btn edit" title="تعديل"><i class="fas fa-edit"></i></a>
                                            <a href="actors.php?delete=<?php echo $actor['id']; ?>" class="action-btn delete" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذا الممثل؟')"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- ترقيم الصفحات -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-link"><i class="fas fa-chevron-right"></i> السابق</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-link">التالي <i class="fas fa-chevron-left"></i></a>
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