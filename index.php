<?php
// تعريف ثابت للتحقق من الوصول المباشر
define('ADMIN_ACCESS', true);

// بدء جلسة المستخدم
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// تضمين ملف الاتصال بقاعدة البيانات
require_once 'includes/db_connect.php';

// تضمين ملف الوظائف المساعدة
require_once 'includes/functions.php';

// الحصول على إحصائيات الموقع
$stats = [
    'total_movies' => getCount($conn, 'movies'),
    'total_series' => getCount($conn, 'series'),
    'total_users' => getCount($conn, 'users'),
    'total_comments' => getCount($conn, 'comments'),
    'total_categories' => getCount($conn, 'categories'),
    'total_actors' => getCount($conn, 'actors'),
    'total_episodes' => getCount($conn, 'episodes'),
    'monthly_movie_views' => getMonthlyViews($conn, 'movies'),
    'monthly_series_views' => getMonthlyViews($conn, 'series')
];

// الحصول على أحدث الأفلام
$latest_movies = getLatestItems($conn, 'movies', 5);

// الحصول على أحدث المسلسلات
$latest_series = getLatestItems($conn, 'series', 5);

// الحصول على أحدث التعليقات
$latest_comments = getLatestComments($conn, 5);

// تضمين ملف الرأس
include 'includes/header.php';
?>

<!-- المحتوى الرئيسي -->
<div class="main-content">
    <div class="header">
        <h1>لوحة التحكم</h1>
        <div class="user-info">
            <div class="dropdown">
                <div class="user-details">
                    <img src="<?php echo getAdminAvatar($_SESSION['admin_id']); ?>" alt="صورة المستخدم">
                    <span><?php echo $_SESSION['admin_name']; ?></span>
                </div>
                <div class="dropdown-content">
                    <a href="profile.php"><i class="fas fa-user"></i> الملف الشخصي</a>
                    <a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- البطاقات الإحصائية -->
    <div class="stats-cards">
        <div class="card stat-card">
            <div class="icon movies">
                <i class="fas fa-film"></i>
            </div>
            <div class="content">
                <h3><?php echo number_format($stats['total_movies']); ?></h3>
                <p>إجمالي الأفلام</p>
            </div>
        </div>
        
        <div class="card stat-card">
            <div class="icon series">
                <i class="fas fa-tv"></i>
            </div>
            <div class="content">
                <h3><?php echo number_format($stats['total_series']); ?></h3>
                <p>إجمالي المسلسلات</p>
            </div>
        </div>
        
        <div class="card stat-card">
            <div class="icon users">
                <i class="fas fa-users"></i>
            </div>
            <div class="content">
                <h3><?php echo number_format($stats['total_users']); ?></h3>
                <p>إجمالي المستخدمين</p>
            </div>
        </div>
        
        <div class="card stat-card">
            <div class="icon comments">
                <i class="fas fa-comments"></i>
            </div>
            <div class="content">
                <h3><?php echo number_format($stats['total_comments']); ?></h3>
                <p>إجمالي التعليقات</p>
            </div>
        </div>
    </div>
    
    <!-- الرسوم البيانية -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>إحصائيات المشاهدات</h2>
                </div>
                <div class="card-body">
                    <canvas id="statsChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h2>توزيع المحتوى</h2>
                </div>
                <div class="card-body">
                    <canvas id="contentDistribution" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- جدول الأفلام -->
    <div class="data-table">
        <div class="table-header">
            <h2>أحدث الأفلام</h2>
            <div class="table-actions">
                <a href="movies.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة فيلم جديد</a>
                <a href="movies.php" class="btn btn-secondary"><i class="fas fa-list"></i> عرض الكل</a>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>#</th>
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
                <?php if (count($latest_movies) > 0): ?>
                    <?php foreach ($latest_movies as $index => $movie): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($movie['title']); ?></td>
                            <td><?php echo getCategoriesString($movie['id'], $conn, 'movie'); ?></td>
                            <td><?php echo $movie['year']; ?></td>
                            <td><?php echo $movie['rating']; ?></td>
                            <td>
                                <span class="status <?php echo getStatusClass($movie['status']); ?>">
                                    <?php echo getStatusText($movie['status']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($movie['created_at']); ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="movies.php?action=view&id=<?php echo $movie['id']; ?>" class="action-btn view" data-tooltip="عرض"><i class="fas fa-eye"></i></a>
                                    <a href="movies.php?action=edit&id=<?php echo $movie['id']; ?>" class="action-btn edit" data-tooltip="تعديل"><i class="fas fa-edit"></i></a>
                                    <a href="javascript:void(0);" onclick="confirmDelete('movie', <?php echo $movie['id']; ?>)" class="action-btn delete" data-tooltip="حذف"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">لا توجد أفلام لعرضها</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- جدول المسلسلات -->
    <div class="data-table">
        <div class="table-header">
            <h2>أحدث المسلسلات</h2>
            <div class="table-actions">
                <a href="series.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة مسلسل جديد</a>
                <a href="series.php" class="btn btn-secondary"><i class="fas fa-list"></i> عرض الكل</a>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم المسلسل</th>
                    <th>التصنيف</th>
                    <th>المواسم</th>
                    <th>التقييم</th>
                    <th>الحالة</th>
                    <th>تاريخ الإضافة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($latest_series) > 0): ?>
                    <?php foreach ($latest_series as $index => $series): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($series['title']); ?></td>
                            <td><?php echo getCategoriesString($series['id'], $conn, 'series'); ?></td>
                            <td><?php echo $series['seasons_count']; ?></td>
                            <td><?php echo $series['rating']; ?></td>
                            <td>
                                <span class="status <?php echo getStatusClass($series['status']); ?>">
                                    <?php echo getStatusText($series['status']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($series['created_at']); ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="series.php?action=view&id=<?php echo $series['id']; ?>" class="action-btn view" data-tooltip="عرض"><i class="fas fa-eye"></i></a>
                                    <a href="series.php?action=edit&id=<?php echo $series['id']; ?>" class="action-btn edit" data-tooltip="تعديل"><i class="fas fa-edit"></i></a>
                                    <a href="javascript:void(0);" onclick="confirmDelete('series', <?php echo $series['id']; ?>)" class="action-btn delete" data-tooltip="حذف"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">لا توجد مسلسلات لعرضها</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- أحدث التعليقات -->
    <div class="data-table">
        <div class="table-header">
            <h2>أحدث التعليقات</h2>
            <div class="table-actions">
                <a href="comments.php" class="btn btn-secondary"><i class="fas fa-list"></i> عرض الكل</a>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>المستخدم</th>
                    <th>المحتوى</th>
                    <th>النوع</th>
                    <th>العنوان</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($latest_comments) > 0): ?>
                    <?php foreach ($latest_comments as $index => $comment): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($comment['username']); ?></td>
                            <td class="comment-content"><?php echo htmlspecialchars(substr($comment['content'], 0, 50)) . (strlen($comment['content']) > 50 ? '...' : ''); ?></td>
                            <td><?php echo $comment['content_type'] == 'movie' ? 'فيلم' : 'مسلسل'; ?></td>
                            <td><?php echo htmlspecialchars($comment['content_title']); ?></td>
                            <td><?php echo formatDate($comment['created_at']); ?></td>
                            <td>
                                <span class="status <?php echo getStatusClass($comment['status']); ?>">
                                    <?php echo getStatusText($comment['status'], 'comment'); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="comments.php?action=view&id=<?php echo $comment['id']; ?>" class="action-btn view" data-tooltip="عرض"><i class="fas fa-eye"></i></a>
                                    <a href="javascript:void(0);" onclick="changeCommentStatus(<?php echo $comment['id']; ?>, '<?php echo $comment['status'] == 1 ? 0 : 1; ?>')" class="action-btn <?php echo $comment['status'] == 1 ? 'delete' : 'edit'; ?>" data-tooltip="<?php echo $comment['status'] == 1 ? 'تعطيل' : 'تفعيل'; ?>">
                                        <i class="fas <?php echo $comment['status'] == 1 ? 'fa-ban' : 'fa-check'; ?>"></i>
                                    </a>
                                    <a href="javascript:void(0);" onclick="confirmDelete('comment', <?php echo $comment['id']; ?>)" class="action-btn delete" data-tooltip="حذف"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">لا توجد تعليقات لعرضها</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// بيانات الرسوم البيانية
const viewsData = {
    labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
    datasets: [{
        label: 'الأفلام',
        data: <?php echo json_encode($stats['monthly_movie_views']); ?>,
        borderColor: '#e50914',
        backgroundColor: 'rgba(229, 9, 20, 0.1)',
        tension: 0.4,
        fill: true
    }, {
        label: 'المسلسلات',
        data: <?php echo json_encode($stats['monthly_series_views']); ?>,
        borderColor: '#28a745',
        backgroundColor: 'rgba(40, 167, 69, 0.1)',
        tension: 0.4,
        fill: true
    }]
};

const contentData = {
    labels: ['أفلام', 'مسلسلات', 'حلقات', 'تصنيفات', 'ممثلين'],
    datasets: [{
        data: [
            <?php echo $stats['total_movies']; ?>,
            <?php echo $stats['total_series']; ?>,
            <?php echo $stats['total_episodes']; ?>,
            <?php echo $stats['total_categories']; ?>,
            <?php echo $stats['total_actors']; ?>
        ],
        backgroundColor: [
            '#e50914',
            '#28a745',
            '#ffc107',
            '#17a2b8',
            '#6f42c1'
        ]
    }]
};

// تهيئة الرسوم البيانية عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // رسم بياني للمشاهدات
    const statsChart = new Chart(document.getElementById('statsChart'), {
        type: 'line',
        data: viewsData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    rtl: true,
                    labels: {
                        font: {
                            family: 'Cairo'
                        }
                    }
                },
                tooltip: {
                    rtl: true,
                    titleFont: {
                        family: 'Cairo'
                    },
                    bodyFont: {
                        family: 'Cairo'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            family: 'Cairo'
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: 'Cairo'
                        }
                    }
                }
            }
        }
    });
    
    // رسم بياني دائري لتوزيع المحتوى
    const contentDistribution = new Chart(document.getElementById('contentDistribution'), {
        type: 'doughnut',
        data: contentData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    rtl: true,
                    labels: {
                        font: {
                            family: 'Cairo'
                        }
                    }
                },
                tooltip: {
                    rtl: true,
                    titleFont: {
                        family: 'Cairo'
                    },
                    bodyFont: {
                        family: 'Cairo'
                    }
                }
            }
        }
    });
});

// دالة تأكيد الحذف
function confirmDelete(type, id) {
    let message = '';
    
    switch(type) {
        case 'movie':
            message = 'هل أنت متأكد من حذف هذا الفيلم؟';
            break;
        case 'series':
            message = 'هل أنت متأكد من حذف هذا المسلسل؟';
            break;
        case 'comment':
            message = 'هل أنت متأكد من حذف هذا التعليق؟';
            break;
        default:
            message = 'هل أنت متأكد من إتمام عملية الحذف؟';
    }
    
    if (confirm(message)) {
        // إرسال طلب الحذف
        fetch(`api/${type}_delete.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم الحذف بنجاح');
                // إعادة تحميل الصفحة بعد الحذف
                window.location.reload();
            } else {
                alert('حدث خطأ أثناء الحذف: ' + data.message);
            }
        })
        .catch(error => {
            alert('حدث خطأ في الاتصال بالخادم');
            console.error('Error:', error);
        });
    }
}

// دالة تغيير حالة التعليق
function changeCommentStatus(id, status) {
    fetch('api/comment_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id, status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم تغيير حالة التعليق بنجاح');
            // إعادة تحميل الصفحة بعد التغيير
            window.location.reload();
        } else {
            alert('حدث خطأ أثناء تغيير الحالة: ' + data.message);
        }
    })
    .catch(error => {
        alert('حدث خطأ في الاتصال بالخادم');
        console.error('Error:', error);
    });
}
</script>

<?php
// تضمين ملف التذييل
include 'includes/footer.php';
?>