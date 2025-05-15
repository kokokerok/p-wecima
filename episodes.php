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

// الحصول على معرف المسلسل من الرابط
$series_id = isset($_GET['series_id']) ? intval($_GET['series_id']) : 0;

// التحقق من وجود المسلسل
$series_name = '';
if ($series_id > 0) {
    $series_query = "SELECT title FROM series WHERE id = ?";
    $series_stmt = $conn->prepare($series_query);
    $series_stmt->bind_param("i", $series_id);
    $series_stmt->execute();
    $series_result = $series_stmt->get_result();
    
    if ($series_result->num_rows > 0) {
        $series_data = $series_result->fetch_assoc();
        $series_name = $series_data['title'];
    } else {
        // إذا لم يتم العثور على المسلسل، يتم إعادة التوجيه إلى صفحة المسلسلات
        header("Location: series.php");
        exit();
    }
}

// معالجة الإجراءات
$success_message = '';
$error_message = '';

// حذف حلقة
if (isset($_GET['delete']) && isset($_GET['episode_id'])) {
    $episode_id = intval($_GET['episode_id']);
    
    // التحقق من وجود الحلقة
    $check_query = "SELECT * FROM episodes WHERE id = ? AND series_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $episode_id, $series_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // حذف الحلقة
        $delete_query = "DELETE FROM episodes WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $episode_id);
        
        if ($delete_stmt->execute()) {
            $success_message = 'تم حذف الحلقة بنجاح';
        } else {
            $error_message = 'حدث خطأ أثناء حذف الحلقة: ' . $conn->error;
        }
    } else {
        $error_message = 'الحلقة غير موجودة';
    }
}

// إضافة حلقة جديدة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_episode'])) {
    $episode_number = trim($_POST['episode_number']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $duration = trim($_POST['duration']);
    $release_date = trim($_POST['release_date']);
    $video_url = trim($_POST['video_url']);
    $status = $_POST['status'];
    
    // التحقق من البيانات
    if (empty($episode_number) || empty($title) || empty($video_url)) {
        $error_message = 'يرجى ملء جميع الحقول المطلوبة';
    } else {
        // التحقق من وجود رقم الحلقة
        $check_query = "SELECT id FROM episodes WHERE episode_number = ? AND series_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ii", $episode_number, $series_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = 'رقم الحلقة موجود بالفعل لهذا المسلسل';
        } else {
            // إضافة الحلقة الجديدة
            $insert_query = "INSERT INTO episodes (series_id, episode_number, title, description, duration, release_date, video_url, status, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("iisssiss", $series_id, $episode_number, $title, $description, $duration, $release_date, $video_url, $status);
            
            if ($insert_stmt->execute()) {
                $success_message = 'تمت إضافة الحلقة بنجاح';
                
                // إعادة تعيين الحقول
                $episode_number = $title = $description = $duration = $release_date = $video_url = '';
                $status = 'active';
            } else {
                $error_message = 'حدث خطأ أثناء إضافة الحلقة: ' . $conn->error;
            }
        }
    }
}

// تحديث حلقة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_episode'])) {
    $episode_id = intval($_POST['episode_id']);
    $episode_number = trim($_POST['episode_number']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $duration = trim($_POST['duration']);
    $release_date = trim($_POST['release_date']);
    $video_url = trim($_POST['video_url']);
    $status = $_POST['status'];
    
    // التحقق من البيانات
    if (empty($episode_number) || empty($title) || empty($video_url)) {
        $error_message = 'يرجى ملء جميع الحقول المطلوبة';
    } else {
        // التحقق من وجود رقم الحلقة (باستثناء الحلقة الحالية)
        $check_query = "SELECT id FROM episodes WHERE episode_number = ? AND series_id = ? AND id != ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("iii", $episode_number, $series_id, $episode_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = 'رقم الحلقة موجود بالفعل لهذا المسلسل';
        } else {
            // تحديث الحلقة
            $update_query = "UPDATE episodes SET 
                            episode_number = ?, 
                            title = ?, 
                            description = ?, 
                            duration = ?, 
                            release_date = ?, 
                            video_url = ?, 
                            status = ?, 
                            updated_at = NOW() 
                            WHERE id = ? AND series_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("issssssii", $episode_number, $title, $description, $duration, $release_date, $video_url, $status, $episode_id, $series_id);
            
            if ($update_stmt->execute()) {
                $success_message = 'تم تحديث الحلقة بنجاح';
            } else {
                $error_message = 'حدث خطأ أثناء تحديث الحلقة: ' . $conn->error;
            }
        }
    }
}

// الحصول على قائمة الحلقات
$episodes = [];
$episodes_query = "SELECT * FROM episodes WHERE series_id = ? ORDER BY episode_number ASC";
$episodes_stmt = $conn->prepare($episodes_query);
$episodes_stmt->bind_param("i", $series_id);
$episodes_stmt->execute();
$episodes_result = $episodes_stmt->get_result();

while ($row = $episodes_result->fetch_assoc()) {
    $episodes[] = $row;
}

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة حلقات المسلسل - لوحة تحكم WeCima</title>
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
            
            <!-- عنوان الصفحة -->
            <div class="page-header">
                <h2>إدارة حلقات المسلسل: <?php echo htmlspecialchars($series_name); ?></h2>
                <a href="series.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة إلى المسلسلات
                </a>
            </div>
            
            <!-- نموذج إضافة حلقة جديدة -->
            <div class="form-container">
                <h3>إضافة حلقة جديدة</h3>
                <form action="" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="episode_number">رقم الحلقة *</label>
                            <input type="number" id="episode_number" name="episode_number" class="form-control" value="" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="title">عنوان الحلقة *</label>
                            <input type="text" id="title" name="title" class="form-control" value="" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="duration">مدة الحلقة</label>
                            <input type="text" id="duration" name="duration" class="form-control" placeholder="مثال: 45 دقيقة">
                        </div>
                        
                        <div class="form-group">
                            <label for="release_date">تاريخ العرض</label>
                            <input type="date" id="release_date" name="release_date" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">وصف الحلقة</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="video_url">رابط الفيديو *</label>
                        <input type="text" id="video_url" name="video_url" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">الحالة</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active">نشط</option>
                            <option value="pending">قيد المراجعة</option>
                            <option value="inactive">غير نشط</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="add_episode" class="btn btn-primary">إضافة الحلقة</button>
                    </div>
                </form>
            </div>
            
            <!-- جدول الحلقات -->
            <div class="data-table">
                <div class="table-header">
                    <h3>قائمة الحلقات</h3>
                </div>
                
                <?php if (count($episodes) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>رقم الحلقة</th>
                                <th>العنوان</th>
                                <th>المدة</th>
                                <th>تاريخ العرض</th>
                                <th>الحالة</th>
                                <th>تاريخ الإضافة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($episodes as $episode): ?>
                                <tr>
                                    <td><?php echo $episode['episode_number']; ?></td>
                                    <td><?php echo htmlspecialchars($episode['title']); ?></td>
                                    <td><?php echo htmlspecialchars($episode['duration']); ?></td>
                                    <td><?php echo $episode['release_date']; ?></td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        $status_text = '';
                                        
                                        switch ($episode['status']) {
                                            case 'active':
                                                $status_class = 'active';
                                                $status_text = 'نشط';
                                                break;
                                            case 'pending':
                                                $status_class = 'pending';
                                                $status_text = 'قيد المراجعة';
                                                break;
                                            case 'inactive':
                                                $status_class = 'inactive';
                                                $status_text = 'غير نشط';
                                                break;
                                        }
                                        ?>
                                        <span class="status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                    </td>
                                    <td><?php echo date('Y/m/d', strtotime($episode['created_at'])); ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="#" class="action-btn view episode-view" data-id="<?php echo $episode['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="action-btn edit episode-edit" data-id="<?php echo $episode['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="episodes.php?series_id=<?php echo $series_id; ?>&delete=1&episode_id=<?php echo $episode['id']; ?>" class="action-btn delete" onclick="return confirm('هل أنت متأكد من حذف هذه الحلقة؟');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-data">
                        <p>لا توجد حلقات لهذا المسلسل حتى الآن.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- نافذة منبثقة لعرض الحلقة -->
            <div id="viewEpisodeModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>تفاصيل الحلقة</h3>
                        <span class="close">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div class="episode-details">
                            <div class="detail-row">
                                <span class="detail-label">رقم الحلقة:</span>
                                <span class="detail-value" id="view-episode-number"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">العنوان:</span>
                                <span class="detail-value" id="view-title"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">الوصف:</span>
                                <span class="detail-value" id="view-description"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">المدة:</span>
                                <span class="detail-value" id="view-duration"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">تاريخ العرض:</span>
                                <span class="detail-value" id="view-release-date"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">رابط الفيديو:</span>
                                <span class="detail-value" id="view-video-url"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">الحالة:</span>
                                <span class="detail-value" id="view-status"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- نافذة منبثقة لتعديل الحلقة -->
            <div id="editEpisodeModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>تعديل الحلقة</h3>
                        <span class="close">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="edit-episode-form">
                            <input type="hidden" name="episode_id" id="edit-episode-id">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="edit-episode-number">رقم الحلقة *</label>
                                    <input type="number" id="edit-episode-number" name="episode_number" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="edit-title">عنوان الحلقة *</label>
                                    <input type="text" id="edit-title" name="title" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="edit-duration">مدة الحلقة</label>
                                    <input type="text" id="edit-duration" name="duration" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label for="edit-release-date">تاريخ العرض</label>
                                    <input type="date" id="edit-release-date" name="release_date" class="form-control">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit-description">وصف الحلقة</label>
                                <textarea id="edit-description" name="description" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit-video-url">رابط الفيديو *</label>
                                <input type="text" id="edit-video-url" name="video_url" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit-status">الحالة</label>
                                <select id="edit-status" name="status" class="form-control">
                                    <option value="active">نشط</option>
                                    <option value="pending">قيد المراجعة</option>
                                    <option value="inactive">غير نشط</option>
                                </select>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="update_episode" class="btn btn-primary">حفظ التغييرات</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ملفات JavaScript -->
    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // الحصول على النوافذ المنبثقة
            const viewModal = document.getElementById('viewEpisodeModal');
            const editModal = document.getElementById('editEpisodeModal');
            
            // الحصول على أزرار الإغلاق
            const closeButtons = document.querySelectorAll('.close');
            
            // إغلاق النوافذ المنبثقة عند النقر على زر الإغلاق
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    viewModal.style.display = 'none';
                    editModal.style.display = 'none';
                });
            });
            
            // إغلاق النوافذ المنبثقة عند النقر خارجها
            window.addEventListener('click', function(event) {
                if (event.target === viewModal) {
                    viewModal.style.display = 'none';
                }
                if (event.target === editModal) {
                    editModal.style.display = 'none';
                }
            });
            
            // بيانات الحلقات
            const episodes = <?php echo json_encode($episodes); ?>;
            
            // أزرار عرض الحلقة
            const viewButtons = document.querySelectorAll('.episode-view');
            viewButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const episodeId = this.getAttribute('data-id');
                    const episode = episodes.find(ep => ep.id == episodeId);
                    
                    if (episode) {
                        // ملء بيانات النافذة المنبثقة
                        document.getElementById('view-episode-number').textContent = episode.episode_number;
                        document.getElementById('view-title').textContent = episode.title;
                        document.getElementById('view-description').textContent = episode.description || 'لا يوجد وصف';
                        document.getElementById('view-duration').textContent = episode.duration || 'غير محدد';
                        document.getElementById('view-release-date').textContent = episode.release_date || 'غير محدد';
                        document.getElementById('view-video-url').textContent = episode.video_url;
                        
                        let statusText = '';
                        switch (episode.status) {
                            case 'active':
                                statusText = 'نشط';
                                break;
                            case 'pending':
                                statusText = 'قيد المراجعة';
                                break;
                            case 'inactive':
                                statusText = 'غير نشط';
                                break;
                            default:
                                statusText = episode.status;
                        }
                        document.getElementById('view-status').textContent = statusText;
                        
                        // عرض النافذة المنبثقة
                        viewModal.style.display = 'block';
                    }
                });
            });
            
            // أزرار تعديل الحلقة
            const editButtons = document.querySelectorAll('.episode-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const episodeId = this.getAttribute('data-id');
                    const episode = episodes.find(ep => ep.id == episodeId);
                    
                    if (episode) {
                        // ملء نموذج التعديل
                        document.getElementById('edit-episode-id').value = episode.id;
                        document.getElementById('edit-episode-number').value = episode.episode_number;
                        document.getElementById('edit-title').value = episode.title;
                        document.getElementById('edit-description').value = episode.description || '';
                        document.getElementById('edit-duration').value = episode.duration || '';
                        document.getElementById('edit-release-date').value = episode.release_date || '';
                        document.getElementById('edit-video-url').value = episode.video_url;
                        document.getElementById('edit-status').value = episode.status;
                        
                        // عرض النافذة المنبثقة
                        editModal.style.display = 'block';
                    }
                });
            });
        });
    </script>
</body>
</html>