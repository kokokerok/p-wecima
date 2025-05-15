<?php
// بدء الجلسة
session_start();

// التحقق إذا كان المستخدم مسجل دخوله بالفعل
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// استدعاء ملف الاتصال بقاعدة البيانات
require_once 'includes/db_connect.php';

// متغيرات لتخزين رسائل الخطأ والنجاح
$error = "";
$success = "";

// التحقق من إرسال النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // الحصول على بيانات المستخدم
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // التحقق من وجود المستخدم
    $query = "SELECT * FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        
        // التحقق من كلمة المرور
        if (password_verify($password, $admin['password'])) {
            // تخزين بيانات المستخدم في الجلسة
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_image'] = $admin['image'];
            $_SESSION['admin_role'] = $admin['role'];
            
            // تحديث آخر تسجيل دخول
            $update_query = "UPDATE admins SET last_login = NOW() WHERE id = " . $admin['id'];
            mysqli_query($conn, $update_query);
            
            // إعادة التوجيه إلى الصفحة الرئيسية
            header("Location: index.php");
            exit();
        } else {
            $error = "كلمة المرور غير صحيحة";
        }
    } else {
        $error = "اسم المستخدم غير موجود";
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - لوحة تحكم WeCima</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Cairo Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: var(--dark-color);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
        }
        
        .login-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 400px;
            padding: 30px;
            text-align: center;
        }
        
        .login-logo {
            margin-bottom: 20px;
        }
        
        .login-logo h1 {
            color: var(--primary-color);
            font-size: 32px;
            margin-bottom: 5px;
        }
        
        .login-logo p {
            color: #6c757d;
            font-size: 16px;
        }
        
        .login-form {
            text-align: right;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(229, 9, 20, 0.25);
        }
        
        .btn-login {
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        .btn-login:hover {
            background-color: #c70811;
        }
        
        .forgot-password {
            display: block;
            margin-top: 15px;
            color: #6c757d;
            text-decoration: none;
            font-size: 14px;
        }
        
        .forgot-password:hover {
            color: var(--primary-color);
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <h1>WeCima</h1>
            <p>لوحة تحكم الموقع</p>
        </div>
        
        <?php if (!empty($error)) { ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
        <?php } ?>
        
        <?php if (!empty($success)) { ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
        <?php } ?>
        
        <form class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">اسم المستخدم</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">تسجيل الدخول</button>
            
            <a href="forgot_password.php" class="forgot-password">نسيت كلمة المرور؟</a>
        </form>
    </div>
</body>
</html> 