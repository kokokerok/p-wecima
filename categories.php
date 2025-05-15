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

// معالجة إضافة تصنيف جديد
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['name']);
        $slug = trim($_POST['slug']);
        $description = trim($_POST['description']);
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : NULL;
        
        // التحقق من البيانات
        if (empty($name)) {
            $error_message = 'يرجى إدخال اسم التصنيف';
        } else {
            // إنشاء slug إذا كان فارغًا
            if (empty($slug)) {
                $slug = create_slug($name);
            }
            
            // التحقق من وجود التصنيف
            $check_query = "SELECT id FROM categories WHERE name = ? OR slug = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("ss", $name, $slug);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $error_message = 'التصنيف موجود بالفعل';
            } else {
                // إضافة التصنيف الجديد
                $insert_query = "INSERT INTO categories (name, slug, description, parent_id) VALUES (?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("sssi", $name, $slug, $description, $parent_id);
                
                if ($insert_stmt->execute()) {
                    $success_message = 'تم إضافة التصنيف بنجاح';
                } else {
                    $error_message = 'حدث خطأ أثناء إضافة التصنيف: ' . $conn->error;
                }
            }
        }
    }
    
    // معالجة تحديث التصنيف
    if (isset($_POST['update_category'])) {
        $category_id = (int)$_POST['category_id'];
        $name = trim($_POST['name']);
        $slug = trim($_POST['slug']);
        $description = trim($_POST['description']);
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : NULL;
        
        // التحقق من البيانات
        if (empty($name)) {
            $error_message = 'يرجى إدخال اسم التصنيف';
        } else {
            // إنشاء slug إذا كان فارغًا
            if (empty($slug)) {
                $slug = create_slug($name);
            }
            
            // التحقق من وجود التصنيف
            $check_query = "SELECT id FROM categories WHERE (name = ? OR slug = ?) AND id != ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("ssi", $name, $slug, $category_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $error_message = 'التصنيف موجود بالفعل';
            } else {
                // تحديث التصنيف
                $update_query = "UPDATE categories SET name = ?, slug = ?, description = ?, parent_id = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("sssii", $name, $slug, $description, $parent_id, $category_id);
                
                if ($update_stmt->execute()) {
                    $success_message = 'تم تحديث التصنيف بنجاح';
                } else {
                    $error_message = 'حدث خطأ أثناء تحديث التصنيف: ' . $conn->error;
                }
            }
        }
    }
    
    // معالجة حذف التصنيف
    if (isset($_POST['delete_category'])) {
        $category_id = (int)$_POST['category_id'];
        
        // التحقق من استخدام التصنيف في الأفلام أو المسلسلات
        $check_usage_query = "SELECT COUNT(*) as count FROM movie_categories WHERE category_id = ?
                             UNION ALL
                             SELECT COUNT(*) as count FROM series_categories WHERE category_id = ?";
        $check_usage_stmt = $conn->prepare($check_usage_query);
        $check_usage_stmt->bind_param("ii", $category_id, $category_id);
        $check_usage_stmt->execute();
        $check_usage_result = $check_usage_stmt->get_result();
        $usage_count = 0;
        
        while ($row = $check_usage_result->fetch_assoc()) {
            $usage_count += $row['count'];
        }
        
        if ($usage_count > 0) {
            $error_message = 'لا يمكن حذف التصنيف لأنه مستخدم في أفلام أو مسلسلات';
        } else {
            // حذف التصنيف
            $delete_query = "DELETE FROM categories WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("i", $category_id);
            
            if ($delete_stmt->execute()) {
                $success_message = 'تم حذف التصنيف بنجاح';
            } else {
                $error_message = 'حدث خطأ أثناء حذف التصنيف: ' . $conn->error;
            }
        }
    }
}

// الحصول على قائمة التصنيفات
$categories_query = "SELECT c.*, p.name as parent_name 
                    FROM categories c 
                    LEFT JOIN categories p ON c.parent_id = p.id 
                    ORDER BY c.name ASC";
$categories_result = $conn->query($categories_query);
$categories = [];

if ($categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// الحصول على قائمة التصنيفات الرئيسية للقائمة المنسدلة
$parent_categories_query = "SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name ASC";
$parent_categories_result = $conn->query($parent_categories_query);
$parent_categories = [];

if ($parent_categories_result->num_rows > 0) {
    while ($row = $parent_categories_result->fetch_assoc()) {
        $parent_categories[] = $row;
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
    <title>إدارة التصنيفات - لوحة تحكم WeCima</title>
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
            
            <!-- نموذج إضافة تصنيف جديد -->
            <div class="form-container">
                <div class="form-header">
                    <h2>إضافة تصنيف جديد</h2>
                </div>
                
                <form action="" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">اسم التصنيف</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="slug">الاسم اللطيف (Slug)</label>
                            <input type="text" id="slug" name="slug" class="form-control">
                            <small class="form-text">اتركه فارغًا ليتم إنشاؤه تلقائيًا</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="parent_id">التصنيف الأب</label>
                        <select id="parent_id" name="parent_id" class="form-control">
                            <option value="">بدون تصنيف أب</option>
                            <?php foreach ($parent_categories as $parent): ?>
                                <option value="<?php echo $parent['id']; ?>"><?php echo htmlspecialchars($parent['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">الوصف</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="add_category" class="btn btn-primary">إضافة التصنيف</button>
                    </div>
                </form>
            </div>
            
            <!-- جدول التصنيفات -->
            <div class="data-table">
                <div class="table-header">
                    <h2>قائمة التصنيفات</h2>
                    <div class="table-actions">
                        <input type="text" id="categorySearch" class="form-control search-input" placeholder="بحث...">
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم التصنيف</th>
                            <th>الاسم اللطيف</th>
                            <th>التصنيف الأب</th>
                            <th>عدد الأفلام</th>
                            <th>عدد المسلسلات</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="7" class="text-center">لا توجد تصنيفات</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $index => $category): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['slug']); ?></td>
                                    <td><?php echo $category['parent_name'] ? htmlspecialchars($category['parent_name']) : '-'; ?></td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-btn edit" onclick="editCategory(<?php echo $category['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete" onclick="deleteCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- نموذج تعديل التصنيف (مخفي) -->
            <div id="editCategoryModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>تعديل التصنيف</h2>
                        <span class="close">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="editCategoryForm">
                            <input type="hidden" id="edit_category_id" name="category_id">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="edit_name">اسم التصنيف</label>
                                    <input type="text" id="edit_name" name="name" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="edit_slug">الاسم اللطيف (Slug)</label>
                                    <input type="text" id="edit_slug" name="slug" class="form-control">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_parent_id">التصنيف الأب</label>
                                <select id="edit_parent_id" name="parent_id" class="form-control">
                                    <option value="">بدون تصنيف أب</option>
                                    <?php foreach ($parent_categories as $parent): ?>
                                        <option value="<?php echo $parent['id']; ?>"><?php echo htmlspecialchars($parent['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_description">الوصف</label>
                                <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="update_category" class="btn btn-primary">حفظ التغييرات</button>
                                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">إلغاء</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- نموذج حذف التصنيف (مخفي) -->
            <div id="deleteCategoryModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>حذف التصنيف</h2>
                        <span class="close">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد من حذف التصنيف: <span id="delete_category_name"></span>؟</p>
                        <form action="" method="post">
                            <input type="hidden" id="delete_category_id" name="category_id">
                            
                            <div class="form-actions">
                                <button type="submit" name="delete_category" class="btn btn-danger">حذف</button>
                                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">إلغاء</button>
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
        // البحث في جدول التصنيفات
        document.getElementById('categorySearch').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                const categoryName = row.cells[1].textContent.toLowerCase();
                const categorySlug = row.cells[2].textContent.toLowerCase();
                
                if (categoryName.includes(searchValue) || categorySlug.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // تعديل التصنيف
        function editCategory(categoryId) {
            // الحصول على بيانات التصنيف من الخادم
            fetch(`get_category.php?id=${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_category_id').value = data.id;
                    document.getElementById('edit_name').value = data.name;
                    document.getElementById('edit_slug').value = data.slug;
                    document.getElementById('edit_description').value = data.description;
                    
                    const parentSelect = document.getElementById('edit_parent_id');
                    if (data.parent_id) {
                        parentSelect.value = data.parent_id;
                    } else {
                        parentSelect.value = '';
                    }
                    
                    // عرض النموذج
                    document.getElementById('editCategoryModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء جلب بيانات التصنيف');
                });
        }
        
        // إغلاق نموذج التعديل
        function closeEditModal() {
            document.getElementById('editCategoryModal').style.display = 'none';
        }
        
        // حذف التصنيف
        function deleteCategory(categoryId, categoryName) {
            document.getElementById('delete_category_id').value = categoryId;
            document.getElementById('delete_category_name').textContent = categoryName;
            document.getElementById('deleteCategoryModal').style.display = 'block';
        }
        
        // إغلاق نموذج الحذف
        function closeDeleteModal() {
            document.getElementById('deleteCategoryModal').style.display = 'none';
        }
        
        // إغلاق النوافذ المنبثقة عند النقر على زر الإغلاق
        document.querySelectorAll('.close').forEach(closeBtn => {
            closeBtn.addEventListener('click', function() {
                this.closest('.modal').style.display = 'none';
            });
        });
        
        // إغلاق النوافذ المنبثقة عند النقر خارجها
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        });
    </script>
</body>
</html>