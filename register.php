<?php
// التحقق من وجود جلسة مسجلة
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'super_admin') {
    // إعادة التوجيه إلى صفحة تسجيل الدخول إذا لم يكن المستخدم مسجل دخول أو ليس لديه صلاحيات كافية
    header('Location: login.php');
    exit;
}

// تضمين ملف الاتصال بقاعدة البيانات
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

$errors = [];
$success = false;

// معالجة النموذج عند الإرسال
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // التحقق من البيانات المدخلة
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    
    // التحقق من اسم المستخدم
    if (empty($username)) {
        $errors[] = 'يرجى إدخال اسم المستخدم';
    } elseif (strlen($username) < 4) {
        $errors[] = 'يجب أن يكون اسم المستخدم 4 أحرف على الأقل';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'اسم المستخدم يجب أن يحتوي على أحرف وأرقام وشرطات سفلية فقط';
    } elseif (isUsernameExists($conn, $username)) {
        $errors[] = 'اسم المستخدم موجود بالفعل، يرجى اختيار اسم آخر';
    }
    
    // التحقق من البريد الإلكتروني
    if (empty($email)) {
        $errors[] = 'يرجى إدخال البريد الإلكتروني';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'يرجى إدخال بريد إلكتروني صحيح';
    } elseif (isEmailExists($conn, $email)) {
        $errors[] = 'البريد الإلكتروني موجود بالفعل، يرجى استخدام بريد آخر';
    }
    
    // التحقق من كلمة المرور
    if (empty($password)) {
        $errors[] = 'يرجى إدخال كلمة المرور';
    } elseif (strlen($password) < 8) {
        $errors[] = 'يجب أن تكون كلمة المرور 8 أحرف على الأقل';
    }
    
    // التحقق من تأكيد كلمة المرور
    if ($password !== $confirm_password) {
        $errors[] = 'كلمة المرور وتأكيدها غير متطابقين';
    }
    
    // التحقق من الدور
    if (empty($role)) {
        $errors[] = 'يرجى اختيار دور المستخدم';
    }
    
    // إذا لم تكن هناك أخطاء، قم بإضافة المستخدم الجديد
    if (empty($errors)) {
        // تشفير كلمة المرور
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // إعداد استعلام إدراج المستخدم
        $query = "INSERT INTO admins (username, email, password, full_name, role, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssss', $username, $email, $hashed_password, $full_name, $role);
        
        if ($stmt->execute()) {
            $success = true;
            // تفريغ البيانات بعد النجاح
            $username = $email = $password = $confirm_password = $full_name = '';
            $role = '';
        } else {
            $errors[] = 'حدث خطأ أثناء إنشاء الحساب: ' . $conn->error;
        }
        
        $stmt->close();
    }
}

// استعلام لجلب الأدوار المتاحة
$roles_query = "SELECT * FROM admin_roles WHERE id != 1"; // استبعاد دور super_admin
$roles_result = $conn->query($roles_query);
$roles = [];

if ($roles_result && $roles_result->num_rows > 0) {
    while ($row = $roles_result->fetch_assoc()) {
        $roles[] = $row;
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة مدير جديد - لوحة تحكم WeCima</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- الخطوط العربية -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">
    <!-- ملف CSS الرئيسي -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <!-- تضمين القائمة الجانبية -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <!-- تضمين الهيدر -->
            <?php include 'includes/header.php'; ?>
            
            <!-- نموذج إضافة مدير جديد -->
            <div class="form-container">
                <div class="form-header">
                    <h2><i class="fas fa-user-plus"></i> إضافة مدير جديد</h2>
                </div>
                
                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> تم إنشاء حساب المدير بنجاح!
                </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                        <li><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">اسم المستخدم <span class="required">*</span></label>
                            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                            <small>يجب أن يكون 4 أحرف على الأقل، ويحتوي على أحرف وأرقام وشرطات سفلية فقط</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">البريد الإلكتروني <span class="required">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">كلمة المرور <span class="required">*</span></label>
                            <input type="password" id="password" name="password" class="form-control" required>
                            <small>يجب أن تكون 8 أحرف على الأقل</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">تأكيد كلمة المرور <span class="required">*</span></label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">الاسم الكامل</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($full_name ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="role">الدور <span class="required">*</span></label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">-- اختر الدور --</option>
                                <?php foreach ($roles as $role_item): ?>
                                <option value="<?php echo $role_item['role_name']; ?>" <?php echo ($role === $role_item['role_name']) ? 'selected' : ''; ?>>
                                    <?php echo $role_item['display_name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
                        <a href="users.php" class="btn btn-secondary"><i class="fas fa-times"></i> إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ملفات JavaScript -->
    <script src="assets/js/main.js"></script>
    <script>
        // التحقق من تطابق كلمة المرور
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('كلمة المرور وتأكيدها غير متطابقين');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>