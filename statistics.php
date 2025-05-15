<?php
// تضمين ملف الاتصال بقاعدة البيانات
include 'config/db_connect.php';

// التحقق من تسجيل الدخول
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// استعلام لجلب إحصائيات الموقع
$query_stats = "SELECT 
    (SELECT COUNT(*) FROM movies) as total_movies,
    (SELECT COUNT(*) FROM series) as total_series,
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM comments) as total_comments,
    (SELECT COUNT(*) FROM categories) as total_categories,
    (SELECT COUNT(*) FROM actors) as total_actors";

$result_stats = mysqli_query($conn, $query_stats);
$stats = mysqli_fetch_assoc($result_stats);

// استعلام لجلب إحصائيات المشاهدات الشهرية
$query_monthly_views = "SELECT 
    MONTH(view_date) as month, 
    COUNT(*) as views 
    FROM views 
    WHERE view_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH) 
    GROUP BY MONTH(view_date) 
    ORDER BY month";

$result_monthly_views = mysqli_query($conn, $query_monthly_views);
$monthly_views = [];
while ($row = mysqli_fetch_assoc($result_monthly_views)) {
    $monthly_views[$row['month']] = $row['views'];
}

// استعلام لجلب أكثر الأفلام مشاهدة
$query_top_movies = "SELECT m.id, m.title, COUNT(v.id) as views 
    FROM movies m 
    JOIN views v ON m.id = v.movie_id 
    GROUP BY m.id 
    ORDER BY views DESC 
    LIMIT 5";

$result_top_movies = mysqli_query($conn, $query_top_movies);

// استعلام لجلب أكثر المسلسلات مشاهدة
$query_top_series = "SELECT s.id, s.title, COUNT(v.id) as views 
    FROM series s 
    JOIN views v ON s.id = v.series_id 
    GROUP BY s.id 
    ORDER BY views DESC 
    LIMIT 5";

$result_top_series = mysqli_query($conn, $query_top_series);

// استعلام لجلب توزيع المستخدمين حسب البلد
$query_users_by_country = "SELECT country, COUNT(*) as count 
    FROM users 
    GROUP BY country 
    ORDER BY count DESC 
    LIMIT 10";

$result_users_by_country = mysqli_query($conn, $query_users_by_country);

// استعلام لجلب توزيع المستخدمين حسب العمر
$query_users_by_age = "SELECT 
    CASE 
        WHEN age BETWEEN 13 AND 17 THEN '13-17'
        WHEN age BETWEEN 18 AND 24 THEN '18-24'
        WHEN age BETWEEN 25 AND 34 THEN '25-34'
        WHEN age BETWEEN 35 AND 44 THEN '35-44'
        WHEN age BETWEEN 45 AND 54 THEN '45-54'
        ELSE '55+' 
    END as age_group,
    COUNT(*) as count
    FROM users
    GROUP BY age_group
    ORDER BY CASE 
        WHEN age_group = '13-17' THEN 1
        WHEN age_group = '18-24' THEN 2
        WHEN age_group = '25-34' THEN 3
        WHEN age_group = '35-44' THEN 4
        WHEN age_group = '45-54' THEN 5
        ELSE 6
    END";

$result_users_by_age = mysqli_query($conn, $query_users_by_age);
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إحصائيات الموقع - لوحة تحكم WeCima</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/admin-style.css">
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .stat-card-lg {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .stat-card-lg h3 {
            margin-bottom: 15px;
            color: var(--dark-color);
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        
        .stat-card-lg h3 i {
            margin-left: 10px;
            color: var(--primary-color);
        }
        
        .top-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .top-item:last-child {
            border-bottom: none;
        }
        
        .top-item .rank {
            font-weight: bold;
            color: var(--primary-color);
            margin-left: 10px;
        }
        
        .top-item .views {
            color: #6c757d;
        }
        
        .date-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .date-filter select {
            padding: 8px 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- القائمة الجانبية -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>WeCima</h2>
                <p>لوحة التحكم</p>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> الرئيسية</a></li>
                    <li><a href="movies.php"><i class="fas fa-film"></i> الأفلام</a></li>
                    <li><a href="series.php"><i class="fas fa-tv"></i> المسلسلات</a></li>
                    <li><a href="episodes.php"><i class="fas fa-play-circle"></i> الحلقات</a></li>
                    <li><a href="categories.php"><i class="fas fa-tags"></i> التصنيفات</a></li>
                    <li><a href="actors.php"><i class="fas fa-user-tie"></i> الممثلين</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> المستخدمين</a></li>
                    <li><a href="comments.php"><i class="fas fa-comments"></i> التعليقات</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a></li>
                    <li><a href="template.php"><i class="fas fa-palette"></i> تخصيص القالب</a></li>
                    <li><a href="statistics.php" class="active"><i class="fas fa-chart-bar"></i> الإحصائيات</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
                </ul>
            </div>
        </div>
        
        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <div class="header">
                <h1>إحصائيات الموقع</h1>
                <div class="user-info">
                    <div class="dropdown">
                        <div class="user-details">
                            <img src="uploads/admin/<?php echo $_SESSION['admin_image']; ?>" alt="صورة المستخدم">
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
            
            <!-- فلتر التاريخ -->
            <div class="date-filter">
                <select id="period-filter">
                    <option value="week">آخر أسبوع</option>
                    <option value="month" selected>آخر شهر</option>
                    <option value="3months">آخر 3 أشهر</option>
                    <option value="6months">آخر 6 أشهر</option>
                    <option value="year">آخر سنة</option>
                </select>
                
                <button class="btn btn-primary" id="apply-filter">تطبيق</button>
            </div>
            
            <!-- البطاقات الإحصائية -->
            <div class="stats-cards">
                <div class="card stat-card">
                    <div class="icon movies">
                        <i class="fas fa-film"></i>
                    </div>
                    <div class="content">
                        <h3><?php echo $stats['total_movies']; ?></h3>
                        <p>إجمالي الأفلام</p>
                    </div>
                </div>
                
                <div class="card stat-card">
                    <div class="icon series">
                        <i class="fas fa-tv"></i>
                    </div>
                    <div class="content">
                        <h3><?php echo $stats['total_series']; ?></h3>
                        <p>إجمالي المسلسلات</p>
                    </div>
                </div>
                
                <div class="card stat-card">
                    <div class="icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="content">
                        <h3><?php echo $stats['total_users']; ?></h3>
                        <p>إجمالي المستخدمين</p>
                    </div>
                </div>
                
                <div class="card stat-card">
                    <div class="icon comments">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="content">
                        <h3><?php echo $stats['total_comments']; ?></h3>
                        <p>إجمالي التعليقات</p>
                    </div>
                </div>
                
                <div class="card stat-card">
                    <div class="icon" style="background-color: rgba(153, 102, 255, 0.1); color: #9966ff;">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="content">
                        <h3><?php echo $stats['total_categories']; ?></h3>
                        <p>إجمالي التصنيفات</p>
                    </div>
                </div>
                
                <div class="card stat-card">
                    <div class="icon" style="background-color: rgba(255, 159, 64, 0.1); color: #ff9f40;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="content">
                        <h3><?php echo $stats['total_actors']; ?></h3>
                        <p>إجمالي الممثلين</p>
                    </div>
                </div>
            </div>
            
            <!-- الرسوم البيانية -->
            <div class="chart-container">
                <canvas id="viewsChart"></canvas>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card-lg">
                    <h3><i class="fas fa-chart-pie"></i> توزيع المستخدمين حسب البلد</h3>
                    <canvas id="countryChart"></canvas>
                </div>
                
                <div class="stat-card-lg">
                    <h3><i class="fas fa-chart-pie"></i> توزيع المستخدمين حسب العمر</h3>
                    <canvas id="ageChart"></canvas>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card-lg">
                    <h3><i class="fas fa-trophy"></i> أكثر الأفلام مشاهدة</h3>
                    <?php $i = 1; while ($movie = mysqli_fetch_assoc($result_top_movies)): ?>
                    <div class="top-item">
                        <div>
                            <span class="rank">#<?php echo $i++; ?></span>
                            <span class="title"><?php echo $movie['title']; ?></span>
                        </div>
                        <span class="views"><?php echo number_format($movie['views']); ?> مشاهدة</span>
                    </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="stat-card-lg">
                    <h3><i class="fas fa-trophy"></i> أكثر المسلسلات مشاهدة</h3>
                    <?php $i = 1; while ($series = mysqli_fetch_assoc($result_top_series)): ?>
                    <div class="top-item">
                        <div>
                            <span class="rank">#<?php echo $i++; ?></span>
                            <span class="title"><?php echo $series['title']; ?></span>
                        </div>
                        <span class="views"><?php echo number_format($series['views']); ?> مشاهدة</span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card-lg">
                    <h3><i class="fas fa-chart-line"></i> نشاط المستخدمين</h3>
                    <canvas id="userActivityChart"></canvas>
                </div>
                
                <div class="stat-card-lg">
                    <h3><i class="fas fa-chart-bar"></i> التعليقات الشهرية</h3>
                    <canvas id="commentsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // بيانات المشاهدات الشهرية
        const monthNames = ['يناير', 'فبراير', 'مارس', 'إبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        const monthlyData = [
            <?php
            for ($i = 1; $i <= 12; $i++) {
                echo isset($monthly_views[$i]) ? $monthly_views[$i] : 0;
                if ($i < 12) echo ', ';
            }
            ?>
        ];
        
        // رسم بياني للمشاهدات
        const viewsCtx = document.getElementById('viewsChart').getContext('2d');
        const viewsChart = new Chart(viewsCtx, {
            type: 'line',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'المشاهدات الشهرية',
                    data: monthlyData,
                    backgroundColor: 'rgba(229, 9, 20, 0.1)',
                    borderColor: '#e50914',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: '#e50914',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'المشاهدات الشهرية',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // بيانات توزيع المستخدمين حسب البلد
        const countryLabels = [
            <?php
            $countries = [];
            $countryCounts = [];
            while ($country = mysqli_fetch_assoc($result_users_by_country)) {
                $countries[] = "'" . $country['country'] . "'";
                $countryCounts[] = $country['count'];
            }
            echo implode(', ', $countries);
            ?>
        ];
        
        const countryData = [
            <?php echo implode(', ', $countryCounts); ?>
        ];
        
        // رسم بياني للبلدان
        const countryCtx = document.getElementById('countryChart').getContext('2d');
        const countryChart = new Chart(countryCtx, {
            type: 'pie',
            data: {
                labels: countryLabels,
                datasets: [{
                    data: countryData,
                    backgroundColor: [
                        '#e50914', '#28a745', '#ffc107', '#0d6efd', '#6c757d',
                        '#9966ff', '#ff9f40', '#36a2eb', '#4bc0c0', '#c9cbcf'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        
        // بيانات توزيع المستخدمين حسب العمر
        const ageLabels = [
            <?php
            $ages = [];
            $ageCounts = [];
            while ($age = mysqli_fetch_assoc($result_users_by_age)) {
                $ages[] = "'" . $age['age_group'] . "'";
                $ageCounts[] = $age['count'];
            }
            echo implode(', ', $ages);
            ?>
        ];
        
        const ageData = [
            <?php echo implode(', ', $ageCounts); ?>
        ];
        
        // رسم بياني للأعمار
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        const ageChart = new Chart(ageCtx, {
            type: 'pie',
            data: {
                labels: ageLabels,
                datasets: [{
                    data: ageData,
                    backgroundColor: [
                        '#e50914', '#28a745', '#ffc107', '#0d6efd', '#6c757d', '#9966ff'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        
        // بيانات نشاط المستخدمين
        const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
        const userActivityChart = new Chart(userActivityCtx, {
            type: 'line',
            data: {
                labels: ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
                datasets: [{
                    label: 'تسجيلات الدخول',
                    data: [120, 190, 150, 170, 180, 210, 250],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    tension: 0.3
                }, {
                    label: 'المشاهدات',
                    data: [320, 390, 350, 470, 480, 510, 650],
                    borderColor: '#e50914',
                    backgroundColor: 'rgba(229, 9, 20, 0.1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'نشاط المستخدمين الأسبوعي',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // بيانات التعليقات الشهرية
        const commentsCtx = document.getElementById('commentsChart').getContext('2d');
        const commentsChart = new Chart(commentsCtx, {
            type: 'bar',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'التعليقات',
                    data: [250, 320, 280, 300, 270, 350, 400, 380, 420, 450, 500, 550],
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'التعليقات الشهرية',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // تحديث الرسوم البيانية عند تغيير الفترة الزمنية
        document.getElementById('apply-filter').addEventListener('click', function() {
            const period = document.getElementById('period-filter').value;
            // هنا يمكن إضافة كود لتحديث البيانات بناءً على الفترة المحددة
            // مثال: إرسال طلب AJAX لجلب البيانات الجديدة ثم تحديث الرسوم البيانية
            alert('تم تحديد الفترة: ' + period);
        });
    </script>
</body>
</html>