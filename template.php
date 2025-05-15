<?php
// التأكد من تسجيل الدخول
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// استدعاء ملف الاتصال بقاعدة البيانات
require_once 'config/db.php';

// معالجة النموذج عند الإرسال
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_template'])) {
        // تحديث إعدادات القالب
        $site_logo = $_POST['site_logo'];
        $primary_color = $_POST['primary_color'];
        $secondary_color = $_POST['secondary_color'];
        $footer_text = $_POST['footer_text'];
        $home_layout = $_POST['home_layout'];
        $enable_dark_mode = isset($_POST['enable_dark_mode']) ? 1 : 0;
        
        // تحديث في قاعدة البيانات
        $stmt = $conn->prepare("UPDATE site_settings SET 
            site_logo = ?, 
            primary_color = ?, 
            secondary_color = ?, 
            footer_text = ?, 
            home_layout = ?, 
            enable_dark_mode = ?
            WHERE id = 1");
            
        $stmt->bind_param("sssssi", $site_logo, $primary_color, $secondary_color, $footer_text, $home_layout, $enable_dark_mode);
        
        if ($stmt->execute()) {
            $success_message = "تم تحديث إعدادات القالب بنجاح!";
        } else {
            $error_message = "حدث خطأ أثناء تحديث الإعدادات: " . $conn->error;
        }
    }
    
    // معالجة تحميل الشعار
    if (isset($_FILES['logo_upload']) && $_FILES['logo_upload']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['logo_upload']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($ext), $allowed)) {
            $new_filename = 'logo_' . time() . '.' . $ext;
            $upload_path = 'uploads/logos/' . $new_filename;
            
            if (move_uploaded_file($_FILES['logo_upload']['tmp_name'], $upload_path)) {
                // تحديث مسار الشعار في قاعدة البيانات
                $stmt = $conn->prepare("UPDATE site_settings SET site_logo = ? WHERE id = 1");
                $stmt->bind_param("s", $upload_path);
                
                if ($stmt->execute()) {
                    $success_message = "تم تحميل الشعار وتحديث الإعدادات بنجاح!";
                } else {
                    $error_message = "تم تحميل الشعار ولكن حدث خطأ أثناء تحديث الإعدادات: " . $conn->error;
                }
            } else {
                $error_message = "حدث خطأ أثناء تحميل الشعار.";
            }
        } else {
            $error_message = "نوع الملف غير مسموح به. الأنواع المسموح بها: JPG, JPEG, PNG, GIF.";
        }
    }
}

// استرجاع إعدادات القالب الحالية
$stmt = $conn->prepare("SELECT * FROM site_settings WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تخصيص القالب - لوحة تحكم WeCima</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Color Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- القائمة الجانبية -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- المحتوى الرئيسي -->
        <div class="main-content">
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
            
            <!-- نموذج تخصيص القالب -->
            <div class="form-container">
                <div class="form-header">
                    <h2><i class="fas fa-palette"></i> تخصيص قالب الموقع</h2>
                    <p>قم بتخصيص مظهر موقعك من خلال الخيارات أدناه</p>
                </div>
                
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-header">
                            <h3>الشعار والألوان</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>شعار الموقع الحالي</label>
                                <div class="current-logo">
                                    <img src="<?php echo $settings['site_logo']; ?>" alt="شعار الموقع" style="max-width: 200px;">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>تحميل شعار جديد</label>
                                <input type="file" name="logo_upload" class="form-control">
                                <small>الأبعاد الموصى بها: 200×60 بكسل. الصيغ المدعومة: JPG, PNG, GIF</small>
                            </div>
                            
                            <div class="form-group">
                                <label>رابط الشعار (أو اترك فارغًا لاستخدام الملف المحمل)</label>
                                <input type="text" name="site_logo" class="form-control" value="<?php echo $settings['site_logo']; ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>اللون الرئيسي</label>
                                    <div class="color-picker-wrapper">
                                        <input type="text" name="primary_color" class="form-control color-input" value="<?php echo $settings['primary_color']; ?>">
                                        <div class="color-picker" id="primary-color-picker"></div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>اللون الثانوي</label>
                                    <div class="color-picker-wrapper">
                                        <input type="text" name="secondary_color" class="form-control color-input" value="<?php echo $settings['secondary_color']; ?>">
                                        <div class="color-picker" id="secondary-color-picker"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3>تخطيط الموقع</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>تخطيط الصفحة الرئيسية</label>
                                <select name="home_layout" class="form-control">
                                    <option value="grid" <?php echo ($settings['home_layout'] == 'grid') ? 'selected' : ''; ?>>شبكة</option>
                                    <option value="list" <?php echo ($settings['home_layout'] == 'list') ? 'selected' : ''; ?>>قائمة</option>
                                    <option value="mixed" <?php echo ($settings['home_layout'] == 'mixed') ? 'selected' : ''; ?>>مختلط</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>نص التذييل</label>
                                <textarea name="footer_text" class="form-control"><?php echo $settings['footer_text']; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="enable_dark_mode" <?php echo ($settings['enable_dark_mode'] == 1) ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                    تفعيل الوضع الليلي
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3>معاينة التخصيص</h3>
                        </div>
                        <div class="card-body">
                            <div class="template-preview">
                                <div class="preview-header" style="background-color: <?php echo $settings['primary_color']; ?>">
                                    <div class="preview-logo">
                                        <img src="<?php echo $settings['site_logo']; ?>" alt="Logo">
                                    </div>
                                    <div class="preview-nav">
                                        <div class="nav-item active">الرئيسية</div>
                                        <div class="nav-item">أفلام</div>
                                        <div class="nav-item">مسلسلات</div>
                                        <div class="nav-item">تصنيفات</div>
                                    </div>
                                </div>
                                <div class="preview-content">
                                    <div class="preview-card" style="border-color: <?php echo $settings['secondary_color']; ?>">
                                        <div class="preview-card-header" style="background-color: <?php echo $settings['secondary_color']; ?>">
                                            عنوان الفيلم
                                        </div>
                                        <div class="preview-card-body">
                                            محتوى الفيلم
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-footer" style="background-color: <?php echo $settings['primary_color']; ?>">
                                    <?php echo $settings['footer_text']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions mt-4">
                        <button type="submit" name="update_template" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التغييرات</button>
                        <button type="reset" class="btn btn-secondary"><i class="fas fa-undo"></i> إعادة تعيين</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Color Picker JS -->
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
    <script>
        // إعداد منتقي الألوان
        const createColorPicker = (el, input) => {
            const pickr = Pickr.create({
                el: el,
                theme: 'classic',
                default: input.value || '#e50914',
                components: {
                    preview: true,
                    opacity: true,
                    hue: true,
                    interaction: {
                        hex: true,
                        rgba: true,
                        hsla: false,
                        hsva: false,
                        cmyk: false,
                        input: true,
                        clear: false,
                        save: true
                    }
                }
            });
            
            pickr.on('save', (color) => {
                const hexColor = color.toHEXA().toString();
                input.value = hexColor;
                
                // تحديث المعاينة
                if (input.name === 'primary_color') {
                    document.querySelector('.preview-header').style.backgroundColor = hexColor;
                    document.querySelector('.preview-footer').style.backgroundColor = hexColor;
                } else if (input.name === 'secondary_color') {
                    document.querySelector('.preview-card').style.borderColor = hexColor;
                    document.querySelector('.preview-card-header').style.backgroundColor = hexColor;
                }
                
                pickr.hide();
            });
            
            return pickr;
        };
        
        // تهيئة منتقيات الألوان
        document.addEventListener('DOMContentLoaded', () => {
            const primaryInput = document.querySelector('input[name="primary_color"]');
            const secondaryInput = document.querySelector('input[name="secondary_color"]');
            
            createColorPicker('#primary-color-picker', primaryInput);
            createColorPicker('#secondary-color-picker', secondaryInput);
        });
    </script>
</body>
</html>