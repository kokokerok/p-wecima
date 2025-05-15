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

// الحصول على بيانات المستخدم
$admin_id = $_SESSION['admin_id'];
$query = "SELECT * FROM admins WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// معالجة تحديث البيانات
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // تحديث البيانات الشخصية
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $username = trim($_POST['username']);
        $bio = trim($_POST['bio']);
        
        // التحقق من البيانات
        if (empty($name) || empty($email) || empty($username)) {
            $error_message = 'جميع الحقول المطلوبة يجب ملؤها';
        } else {
            // التحقق من البريد الإلكتروني
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_message = 'البريد الإلكتروني غير صالح';
            } else {
                // التحقق من وجود اسم المستخدم أو البريد الإلكتروني
                $check_query = "SELECT id FROM admins WHERE (email = ? OR username = ?) AND id != ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("ssi", $email, $username, $admin_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    $error_message = 'اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل';
                } else {
                    // تحديث البيانات
                    $update_query = "UPDATE admins SET name = ?, email = ?, username = ?, bio = ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param("ssssi", $name, $email, $username, $bio, $admin_id);
                    
                    if ($update_stmt->execute()) {
                        $success_message = 'تم تحديث البيانات الشخصية بنجاح';
                        
                        // تحديث بيانات الجلسة
                        $_SESSION['admin_name'] = $name;
                        $_SESSION['admin_username'] = $username;
                        
                        // تحديث بيانات المستخدم المعروضة
                        $admin['name'] = $name;
                        $admin['email'] = $email;
                        $admin['username'] = $username;
                        $admin['bio'] = $bio;
                    } else {
                        $error_message = 'حدث خطأ أثناء تحديث البيانات: ' . $conn->error;
                    }
                }
            }
        }
    }
    
    // تغيير كلمة المرور
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // التحقق من البيانات
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = 'جميع حقول كلمة المرور مطلوبة';
        } elseif ($new_password !== $confirm_password) {
            $error_message = 'كلمة المرور الجديدة وتأكيدها غير متطابقين';
        } elseif (strlen($new_password) < 8) {
            $error_message = 'يجب أن تكون كلمة المرور الجديدة 8 أحرف على الأقل';
        } else {
            // التحقق من كلمة المرور الحالية
            $password_query = "SELECT password FROM admins WHERE id = ?";
            $password_stmt = $conn->prepare($password_query);
            $password_stmt->bind_param("i", $admin_id);
            $password_stmt->execute();
            $password_result = $password_stmt->get_result();
            $password_row = $password_result->fetch_assoc();
            
            if (password_verify($current_password, $password_row['password'])) {
                // تحديث كلمة المرور
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_password_query = "UPDATE admins SET password = ? WHERE id = ?";
                $update_password_stmt = $conn->prepare($update_password_query);
                $update_password_stmt->bind_param("si", $hashed_password, $admin_id);
                
                if ($update_password_stmt->execute()) {
                    $success_message = 'تم تغيير كلمة المرور بنجاح';
                } else {
                    $error_message = 'حدث خطأ أثناء تحديث كلمة المرور: ' . $conn->error;
                }
            } else {
                $error_message = 'كلمة المرور الحالية غير صحيحة';
            }
        }
    }
    
    // تحديث الصورة الشخصية
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2 ميجابايت
        
        $file_type = $_FILES['profile_image']['type'];
        $file_size = $_FILES['profile_image']['size'];
        
        if (!in_array($file_type, $allowed_types)) {
            $error_message = 'نوع الملف غير مسموح به. يرجى استخدام JPEG أو PNG أو GIF';
        } elseif ($file_size > $max_size) {
            $error_message = 'حجم الملف كبير جدًا. الحد الأقصى هو 2 ميجابايت';
        } else {
            $upload_dir = 'uploads/profile_images/';
            
            // إنشاء المجلد إذا لم يكن موجودًا
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // إنشاء اسم فريد للملف
            $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $file_name = $admin_id . '_' . time() . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                // تحديث مسار الصورة في قاعدة البيانات
                $update_image_query = "UPDATE admins SET profile_image = ? WHERE id = ?";
                $update_image_stmt = $conn->prepare($update_image_query);
                $update_image_stmt->bind_param("si", $target_file, $admin_id);
                
                if ($update_image_stmt->execute()) {
                    $success_message = 'تم تحديث الصورة الشخصية بنجاح';
                    $admin['profile_image'] = $target_file;
                } else {
                    $error_message = 'حدث خطأ أثناء تحديث الصورة: ' . $conn->error;
                }
            } else {
                $error_message = 'حدث خطأ أثناء رفع الصورة';
            }
        }
    }
}

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي - لوحة تحكم WeCima</title>
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
            
            <!-- بطاقة الملف الشخصي -->
            <div class="profile-container">
                <div class="profile-header">
                    <h2>الملف الشخصي</h2>
                    <p>إدارة معلوماتك الشخصية وتغيير كلمة المرور</p>
                </div>
                
                <div class="profile-content">
                    <div class="profile-sidebar">
                        <div class="profile-image-container">
                            <img src="<?php echo !empty($admin['profile_image']) ? $admin['profile_image'] : 'https://via.placeholder.com/150'; ?>" alt="الصورة الشخصية">
                            
                            <form action="" method="post" enctype="multipart/form-data" class="profile-image-form">
                                <div class="form-group">
                                    <label for="profile_image" class="upload-btn">
                                        <i class="fas fa-camera"></i> تغيير الصورة
                                    </label>
                                    <input type="file" id="profile_image" name="profile_image" class="hidden-input" onchange="this.form.submit()">
                                </div>
                            </form>
                        </div>
                        
                        <div class="profile-info">
                            <h3><?php echo htmlspecialchars($admin['name']); ?></h3>
                            <p class="profile-role">مدير الموقع</p>
                            <p class="profile-date">
                                <i class="fas fa-calendar-alt"></i> تاريخ الانضمام: <?php echo date('Y/m/d', strtotime($admin['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="profile-tabs">
                        <div class="tabs-header">
                            <button class="tab-btn active" data-tab="personal-info">المعلومات الشخصية</button>
                            <button class="tab-btn" data-tab="change-password">تغيير كلمة المرور</button>
                        </div>
                        
                        <div class="tabs-content">
                            <!-- قسم المعلومات الشخصية -->
                            <div class="tab-pane active" id="personal-info">
                                <form action="" method="post" class="profile-form">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="name">الاسم الكامل</label>
                                            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="username">اسم المستخدم</label>
                                            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email">البريد الإلكتروني</label>
                                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="bio">نبذة شخصية</label>
                                        <textarea id="bio" name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($admin['bio'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button type="submit" name="update_profile" class="btn btn-primary">حفظ التغييرات</button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- قسم تغيير كلمة المرور -->
                            <div class="tab-pane" id="change-password">
                                <form action="" method="post" class="profile-form">
                                    <div class="form-group">
                                        <label for="current_password">كلمة المرور الحالية</label>
                                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="new_password">كلمة المرور الجديدة</label>
                                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                                            <small class="form-text">يجب أن تكون 8 أحرف على الأقل</small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="confirm_password">تأكيد كلمة المرور</label>
                                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button type="submit" name="change_password" class="btn btn-primary">تغيير كلمة المرور</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ملفات JavaScript -->
    <script src="assets/js/main.js"></script>
    <script>
        // التبديل بين علامات التبويب
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // إزالة الفئة النشطة من جميع الأزرار
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    // إضافة الفئة النشطة للزر المضغوط
                    this.classList.add('active');
                    
                    // إخفاء جميع علامات التبويب
                    tabPanes.forEach(pane => pane.classList.remove('active'));
                    
                    // إظهار علامة التبويب المطلوبة
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>