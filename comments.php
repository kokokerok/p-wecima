<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة التعليقات - لوحة تحكم WeCima</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #e50914;
            --secondary-color: #221f1f;
            --dark-color: #141414;
            --light-color: #f4f4f4;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            direction: rtl;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* القائمة الجانبية */
        .sidebar {
            width: 250px;
            background-color: var(--dark-color);
            color: #fff;
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header h2 {
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(229, 9, 20, 0.8);
            color: #fff;
        }
        
        .sidebar-menu i {
            margin-left: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* المحتوى الرئيسي */
        .main-content {
            flex: 1;
            margin-right: 250px;
            padding: 20px;
        }
        
        .header {
            background-color: #fff;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 24px;
            color: var(--dark-color);
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 10px;
        }
        
        .user-info .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            left: 0;
            background-color: #fff;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 5px;
        }
        
        .dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        /* جدول البيانات */
        .data-table {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .table-header {
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table-header h2 {
            font-size: 18px;
            color: var(--dark-color);
        }
        
        .table-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
        }
        
        .btn-primary:hover {
            background-color: #c70811;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th, table td {
            padding: 12px 15px;
            text-align: right;
            border-bottom: 1px solid #e9ecef;
        }
        
        table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        table tr:hover {
            background-color: #f8f9fa;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status.approved {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
        }
        
        .status.pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }
        
        .status.rejected {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }
        
        .action-btns {
            display: flex;
            gap: 5px;
        }
        
        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .action-btn.approve {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
        }
        
        .action-btn.approve:hover {
            background-color: var(--success-color);
            color: #fff;
        }
        
        .action-btn.reject {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }
        
        .action-btn.reject:hover {
            background-color: var(--danger-color);
            color: #fff;
        }
        
        .action-btn.delete {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
        
        .action-btn.delete:hover {
            background-color: #6c757d;
            color: #fff;
        }
        
        .comment-content {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* تصميم متجاوب */
        @media (max-width: 992px) {
            .sidebar {
                width: 200px;
            }
            
            .main-content {
                margin-right: 200px;
            }
        }
        
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-right: 0;
            }
            
            .comment-content {
                max-width: 150px;
            }
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
                    <li><a href="index.html"><i class="fas fa-tachometer-alt"></i> الرئيسية</a></li>
                    <li><a href="movies.php"><i class="fas fa-film"></i> الأفلام</a></li>
                    <li><a href="series.php"><i class="fas fa-tv"></i> المسلسلات</a></li>
                    <li><a href="episodes.php"><i class="fas fa-play-circle"></i> الحلقات</a></li>
                    <li><a href="categories.php"><i class="fas fa-tags"></i> التصنيفات</a></li>
                    <li><a href="actors.php"><i class="fas fa-user-tie"></i> الممثلين</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> المستخدمين</a></li>
                    <li><a href="comments.php" class="active"><i class="fas fa-comments"></i> التعليقات</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a></li>
                    <li><a href="template.php"><i class="fas fa-palette"></i> تخصيص القالب</a></li>
                    <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> الإحصائيات</a></li>
                    <li><a href="#"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
                </ul>
            </div>
        </div>
        
        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <div class="header">
                <h1>إدارة التعليقات</h1>
                <div class="user-info">
                    <div class="dropdown">
                        <div class="user-details">
                            <img src="https://via.placeholder.com/40" alt="صورة المستخدم">
                            <span>مدير الموقع</span>
                        </div>
                        <div class="dropdown-content">
                            <a href="#"><i class="fas fa-user"></i> الملف الشخصي</a>
                            <a href="#"><i class="fas fa-cog"></i> الإعدادات</a>
                            <a href="#"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- جدول التعليقات -->
            <div class="data-table">
                <div class="table-header">
                    <h2>جميع التعليقات</h2>
                    <div class="table-actions">
                        <button class="btn btn-secondary"><i class="fas fa-filter"></i> تصفية</button>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستخدم</th>
                            <th>المحتوى</th>
                            <th>نوع المحتوى</th>
                            <th>عنوان المحتوى</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>أحمد محمد</td>
                            <td class="comment-content">فيلم رائع جداً، أحببت القصة والأداء التمثيلي كان ممتاز!</td>
                            <td>فيلم</td>
                            <td>اسم الفيلم 1</td>
                            <td>2023/06/15</td>
                            <td><span class="status approved">موافق عليه</span></td>
                            <td>
                                <div class="action-btns">
                                    <div class="action-btn approve"><i class="fas fa-check"></i></div>
                                    <div class="action-btn reject"><i class="fas fa-times"></i></div>
                                    <div class="action-btn delete"><i class="fas fa-trash"></i></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>سارة أحمد</td>
                            <td class="comment-content">لم يعجبني هذا المسلسل، القصة مملة والأحداث بطيئة جداً.</td>
                            <td>مسلسل</td>
                            <td>اسم المسلسل 2</td>
                            <td>2023/06/14</td>
                            <td><span class="status approved">موافق عليه</span></td>
                            <td>
                                <div class="action-btns">
                                    <div class="action-btn approve"><i class="fas fa-check"></i></div>
                                    <div class="action-btn reject"><i class="fas fa-times"></i></div>
                                    <div class="action-btn delete"><i class="fas fa-trash"></i></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>محمد علي</td>
                            <td class="comment-content">هذه الحلقة كانت مليئة بالأحداث المثيرة، أنتظر الحلقة القادمة بفارغ الصبر!</td>
                            <td>حلقة</td>
                            <td>اسم المسلسل 1 - الحلقة 5</td>
                            <td>2023/06/13</td>
                            <td><span class="status pending">قيد المراجعة</span></td>
                            <td>
                                <div class="action-btns">
                                    <div class="action-btn approve"><i class="fas fa-check"></i></div>
                                    <div class="action-btn reject"><i class="fas fa-times"></i></div>
                                    <div class="action-btn delete"><i class="fas fa-trash"></i></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>فاطمة خالد</td>
                            <td class="comment-content">هذا محتوى غير لائق ويحتوي على كلمات نابية وإساءة للآخرين.</td>
                            <td>فيلم</td>
                            <td>اسم الفيلم 3</td>
                            <td>2023/06/12</td>
                            <td><span class="status rejected">مرفوض</span></td>
                            <td>
                                <div class="action-btns">
                                    <div class="action-btn approve"><i class="fas fa-check"></i></div>
                                    <div class="action-btn reject"><i class="fas fa-times"></i></div>
                                    <div class="action-btn delete"><i class="fas fa-trash"></i></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>خالد إبراهيم</td>
                            <td class="comment-content">أداء الممثل الرئيسي كان مذهلاً في هذا الفيلم، يستحق جائزة!</td>
                            <td>فيلم</td>
                            <td>اسم الفيلم 2</td>
                            <td>2023/06/11</td>
                            <td><span class="status pending">قيد المراجعة</span></td>
                            <td>
                                <div class="action-btns">
                                    <div class="action-btn approve"><i class="fas fa-check"></i></div>
                                    <div class="action-btn reject"><i class="fas fa-times"></i></div>
                                    <div class="action-btn delete"><i class="fas fa-trash"></i></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- نموذج الرد على التعليق -->
            <div class="data-table">
                <div class="table-header">
                    <h2>الرد على التعليق</h2>
                </div>
                
                <div style="padding: 20px;">
                    <div class="form-group">
                        <label>التعليق المحدد</label>
                        <div style="padding: 15px; background-color: #f8f9fa; border-radius: 5px; margin-bottom: 15px;">
                            <p><strong>محمد علي:</strong> هذه الحلقة كانت مليئة بالأحداث المثيرة، أنتظر الحلقة القادمة بفارغ الصبر!</p>
                            <small>اسم المسلسل 1 - الحلقة 5 | 2023/06/13</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="reply">الرد</label>
                        <textarea id="reply" class="form-control" placeholder="اكتب ردك هنا..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button class="btn btn-primary">إرسال الرد</button>
                        <button class="btn btn-secondary">إلغاء</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>