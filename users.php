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

// تهيئة المتغيرات
$success_message = '';
$error_message = '';
$search_query = '';
$filter_status = '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// البحث والتصفية
if (isset($_GET['search']) || isset($_GET['status'])) {
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
    
    // إعادة تعيين الصفحة عند البحث
    $page = 1;
    $offset = 0;
}

// حذف مستخدم
if (isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];
    
    // التحقق من وجود المستخدم
    $check_query = "SELECT id FROM users WHERE id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // حذف المستخدم
        $delete_query = "DELETE FROM users WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $user_id);
        
        if ($delete_stmt->execute()) {
            $success_message = 'تم حذف المستخدم بنجاح';
        } else {
            $error_message = 'حدث خطأ أثناء حذف المستخدم: ' . $conn->error;
        }
    } else {
        $error_message = 'المستخدم غير موجود';
    }
}

// تغيير حالة المستخدم
if (isset($_POST['change_status'])) {
    $user_id = (int)$_POST['user_id'];
    $new_status = $_POST['new_status'];
    
    // التحقق من القيم المسموح بها
    if (!in_array($new_status, ['active', 'inactive', 'banned'])) {
        $error_message = 'حالة غير صالحة';
    } else {
        // تحديث حالة المستخدم
        $update_query = "UPDATE users SET status = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $new_status, $user_id);
        
        if ($update_stmt->execute()) {
            $success_message = 'تم تحديث حالة المستخدم بنجاح';
        } else {
            $error_message = 'حدث خطأ أثناء تحديث حالة المستخدم: ' . $conn->error;
        }
    }
}

// استعلام المستخدمين
$query = "SELECT * FROM users WHERE 1=1";
$count_query = "SELECT COUNT(*) as total FROM users WHERE 1=1";
$params = [];
$types = "";

// إضافة شروط البحث
if (!empty($search_query)) {
    $query .= " AND (username LIKE ? OR email LIKE ? OR name LIKE ?)";
    $count_query .= " AND (username LIKE ? OR email LIKE ? OR name LIKE ?)";
    $search_param = "%$search_query%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

// إضافة شروط التصفية
if (!empty($filter_status)) {
    $query .= " AND status = ?";
    $count_query .= " AND status = ?";
    $params[] = $filter_status;
    $types .= "s";
}

// إضافة الترتيب والحد
$query .= " ORDER BY created_at DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $per_page;
$types .= "ii";

// تنفيذ استعلام العدد الإجمالي
$count_stmt = $conn->prepare($count_query);
if (!empty($types) && !empty($params)) {
    // إزالة آخر معاملين (offset و per_page) من المصفوفة للاستعلام العددي
    $count_params = array_slice($params, 0, -2);
    $count_types = substr($types, 0, -2);
    
    if (!empty($count_params)) {
        $count_stmt->bind_param($count_types, ...$count_params);
    }
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total_users = $count_row['total'];
$total_pages = ceil($total_users / $per_page);

// تنفيذ استعلام المستخدمين
$stmt = $conn->prepare($query);
if (!empty($types) && !empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين - لوحة تحكم WeCima</title>
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
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- جدول المستخدمين -->
            <div class="data-table">
                <div class="table-header">
                    <h2>إدارة المستخدمين</h2>
                    <div class="table-actions">
                        <a href="add_user.php" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة مستخدم جديد</a>
                    </div>
                </div>
                
                <!-- نموذج البحث والتصفية -->
                <div class="filter-container">
                    <form action="" method="get" class="filter-form">
                        <div class="form-row">
                            <div class="form-group">
                                <input type="text" name="search" class="form-control" placeholder="بحث عن مستخدم..." value="<?php echo htmlspecialchars($search_query); ?>">
                            </div>
                            
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">جميع الحالات</option>
                                    <option value="active" <?php echo $filter_status === 'active' ? 'selected' : ''; ?>>نشط</option>
                                    <option value="inactive" <?php echo $filter_status === 'inactive' ? 'selected' : ''; ?>>غير نشط</option>
                                    <option value="banned" <?php echo $filter_status === 'banned' ? 'selected' : ''; ?>>محظور</option>
                                </select>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> بحث</button>
                                <a href="users.php" class="btn btn-light"><i class="fas fa-redo"></i> إعادة تعيين</a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- عرض المستخدمين -->
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>اسم المستخدم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الاسم الكامل</th>
                            <th>الحالة</th>
                            <th>تاريخ التسجيل</th>
                            <th>آخر تسجيل دخول</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="9" class="text-center">لا يوجد مستخدمين</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $index => $user): ?>
                                <tr>
                                    <td><?php echo $offset + $index + 1; ?></td>
                                    <td>
                                        <img src="<?php echo !empty($user['profile_image']) ? $user['profile_image'] : 'assets/images/default-user.png'; ?>" alt="صورة المستخدم" class="user-avatar">
                                    </td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        $status_text = '';
                                        
                                        switch ($user['status']) {
                                            case 'active':
                                                $status_class = 'active';
                                                $status_text = 'نشط';
                                                break;
                                            case 'inactive':
                                                $status_class = 'inactive';
                                                $status_text = 'غير نشط';
                                                break;
                                            case 'banned':
                                                $status_class = 'banned';
                                                $status_text = 'محظور';
                                                break;
                                            default:
                                                $status_class = 'inactive';
                                                $status_text = 'غير معروف';
                                        }
                                        ?>
                                        <span class="status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                    </td>
                                    <td><?php echo date('Y/m/d', strtotime($user['created_at'])); ?></td>
                                    <td><?php echo !empty($user['last_login']) ? date('Y/m/d H:i', strtotime($user['last_login'])) : 'لم يسجل الدخول بعد'; ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="view_user.php?id=<?php echo $user['id']; ?>" class="action-btn view" title="عرض"><i class="fas fa-eye"></i></a>
                                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn edit" title="تعديل"><i class="fas fa-edit"></i></a>
                                            
                                            <!-- زر تغيير الحالة -->
                                            <div class="dropdown">
                                                <div class="action-btn status-btn" title="تغيير الحالة"><i class="fas fa-exchange-alt"></i></div>
                                                <div class="dropdown-content">
                                                    <form action="" method="post">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <input type="hidden" name="change_status" value="1">
                                                        
                                                        <button type="submit" name="new_status" value="active" class="dropdown-item <?php echo $user['status'] === 'active' ? 'active' : ''; ?>">
                                                            <i class="fas fa-check-circle"></i> نشط
                                                        </button>
                                                        
                                                        <button type="submit" name="new_status" value="inactive" class="dropdown-item <?php echo $user['status'] === 'inactive' ? 'active' : ''; ?>">
                                                            <i class="fas fa-times-circle"></i> غير نشط
                                                        </button>
                                                        
                                                        <button type="submit" name="new_status" value="banned" class="dropdown-item <?php echo $user['status'] === 'banned' ? 'active' : ''; ?>">
                                                            <i class="fas fa-ban"></i> حظر
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            
                                            <!-- زر الحذف -->
                                            <div class="action-btn delete" title="حذف" data-toggle="modal" data-target="#deleteModal<?php echo $user['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </div>
                                            
                                            <!-- نافذة تأكيد الحذف -->
                                            <div class="modal" id="deleteModal<?php echo $user['id']; ?>">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3>تأكيد الحذف</h3>
                                                        <span class="close" data-dismiss="modal">&times;</span>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>هل أنت متأكد من حذف المستخدم "<?php echo htmlspecialchars($user['username']); ?>"؟</p>
                                                        <p class="text-danger">هذا الإجراء لا يمكن التراجع عنه.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <form action="" method="post">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                            <button type="submit" name="delete_user" class="btn btn-danger">حذف</button>
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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
                            <a href="?page=1<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo !empty($filter_status) ? '&status=' . urlencode($filter_status) : ''; ?>" class="page-link">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                            <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo !empty($filter_status) ? '&status=' . urlencode($filter_status) : ''; ?>" class="page-link">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        for ($i = $start_page; $i <= $end_page; $i++):
                        ?>
                            <a href="?page=<?php echo $i; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo !empty($filter_status) ? '&status=' . urlencode($filter_status) : ''; ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo !empty($filter_status) ? '&status=' . urlencode($filter_status) : ''; ?>" class="page-link">
                                <i class="fas fa-angle-left"></i>
                            </a>
                            <a href="?page=<?php echo $total_pages; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo !empty($filter_status) ? '&status=' . urlencode($filter_status) : ''; ?>" class="page-link">
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
    <script>
        // التعامل مع النوافذ المنبثقة
        document.addEventListener('DOMContentLoaded', function() {
            // فتح النافذة المنبثقة
            const modalTriggers = document.querySelectorAll('[data-toggle="modal"]');
            modalTriggers.forEach(trigger => {
                trigger.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-target');
                    const modal = document.querySelector(modalId);
                    if (modal) {
                        modal.style.display = 'block';
                    }
                });
            });
            
            // إغلاق النافذة المنبثقة
            const closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    if (modal) {
                        modal.style.display = 'none';
                    }
                });
            });
            
            // إغلاق النافذة عند النقر خارجها
            window.addEventListener('click', function(event) {
                if (event.target.classList.contains('modal')) {
                    event.target.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>