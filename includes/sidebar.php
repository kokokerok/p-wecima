<?php
// الحصول على الصفحة الحالية
$current_page = basename($_SERVER['PHP_SELF']);

// التحقق من صلاحيات المستخدم
$user_permissions = getUserPermissions($_SESSION['admin_id']);

// تعريف عناصر القائمة
$menu_items = [
    [
        'url' => 'index.php',
        'title' => 'الرئيسية',
        'icon' => 'fas fa-tachometer-alt',
        'permission' => 'dashboard_view'
    ],
    [
        'url' => 'movies.php',
        'title' => 'الأفلام',
        'icon' => 'fas fa-film',
        'permission' => 'movies_view'
    ],
    [
        'url' => 'series.php',
        'title' => 'المسلسلات',
        'icon' => 'fas fa-tv',
        'permission' => 'series_view'
    ],
    [
        'url' => 'episodes.php',
        'title' => 'الحلقات',
        'icon' => 'fas fa-play-circle',
        'permission' => 'episodes_view'
    ],
    [
        'url' => 'categories.php',
        'title' => 'التصنيفات',
        'icon' => 'fas fa-tags',
        'permission' => 'categories_view'
    ],
    [
        'url' => 'actors.php',
        'title' => 'الممثلين',
        'icon' => 'fas fa-user-tie',
        'permission' => 'actors_view'
    ],
    [
        'url' => 'users.php',
        'title' => 'المستخدمين',
        'icon' => 'fas fa-users',
        'permission' => 'users_view'
    ],
    [
        'url' => 'comments.php',
        'title' => 'التعليقات',
        'icon' => 'fas fa-comments',
        'permission' => 'comments_view'
    ],
    [
        'url' => 'settings.php',
        'title' => 'الإعدادات',
        'icon' => 'fas fa-cog',
        'permission' => 'settings_view'
    ],
    [
        'url' => 'template.php',
        'title' => 'تخصيص القالب',
        'icon' => 'fas fa-palette',
        'permission' => 'template_view'
    ],
    [
        'url' => 'statistics.php',
        'title' => 'الإحصائيات',
        'icon' => 'fas fa-chart-bar',
        'permission' => 'statistics_view'
    ]
];
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>WeCima</h2>
        <p>لوحة التحكم</p>
    </div>
    
    <div class="sidebar-menu">
        <ul>
            <?php foreach ($menu_items as $item): ?>
                <?php if (hasPermission($user_permissions, $item['permission'])): ?>
                <li>
                    <a href="<?php echo $item['url']; ?>" <?php echo ($current_page == $item['url']) ? 'class="active"' : ''; ?>>
                        <i class="<?php echo $item['icon']; ?>"></i> <?php echo $item['title']; ?>
                    </a>
                </li>
                <?php endif; ?>
            <?php endforeach; ?>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
        </ul>
    </div>
</div>