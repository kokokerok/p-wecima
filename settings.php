<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعدادات الموقع - لوحة تحكم WeCima</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin-style.css">
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
                    <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> الإعدادات</a></li>
                    <li><a href="template.php"><i class="fas fa-palette"></i> تخصيص القالب</a></li>
                    <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> الإحصائيات</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
                </ul>
            </div>
        </div>
        
        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <div class="header">
                <h1>إعدادات الموقع</h1>
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
            
            <!-- محتوى الإعدادات -->
            <div class="form-container">
                <div class="settings-tabs">
                    <div class="tabs-header">
                        <button class="tab-btn active" data-tab="general">إعدادات عامة</button>
                        <button class="tab-btn" data-tab="seo">إعدادات SEO</button>
                        <button class="tab-btn" data-tab="social">وسائل التواصل الاجتماعي</button>
                        <button class="tab-btn" data-tab="api">إعدادات API</button>
                        <button class="tab-btn" data-tab="email">إعدادات البريد الإلكتروني</button>
                    </div>
                    
                    <div class="tabs-content">
                        <!-- إعدادات عامة -->
                        <div class="tab-content active" id="general">
                            <h2>الإعدادات العامة</h2>
                            <form>
                                <div class="form-group">
                                    <label for="site_name">اسم الموقع</label>
                                    <input type="text" id="site_name" class="form-control" value="WeCima">
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_description">وصف الموقع</label>
                                    <textarea id="site_description" class="form-control">موقع WeCima لمشاهدة الأفلام والمسلسلات العربية والأجنبية</textarea>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="site_logo">شعار الموقع</label>
                                        <div class="file-upload">
                                            <input type="file" id="site_logo" class="file-input">
                                            <label for="site_logo" class="file-label">
                                                <i class="fas fa-upload"></i> اختر ملفًا
                                            </label>
                                            <span class="file-name">لم يتم اختيار ملف</span>
                                        </div>
                                        <div class="logo-preview">
                                            <img src="https://via.placeholder.com/150x50" alt="شعار الموقع">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="site_favicon">أيقونة الموقع</label>
                                        <div class="file-upload">
                                            <input type="file" id="site_favicon" class="file-input">
                                            <label for="site_favicon" class="file-label">
                                                <i class="fas fa-upload"></i> اختر ملفًا
                                            </label>
                                            <span class="file-name">لم يتم اختيار ملف</span>
                                        </div>
                                        <div class="favicon-preview">
                                            <img src="https://via.placeholder.com/32" alt="أيقونة الموقع">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="items_per_page">عدد العناصر في الصفحة</label>
                                        <input type="number" id="items_per_page" class="form-control" value="20">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="default_language">اللغة الافتراضية</label>
                                        <select id="default_language" class="form-control">
                                            <option value="ar" selected>العربية</option>
                                            <option value="en">الإنجليزية</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>خيارات التسجيل</label>
                                    <div class="checkbox-group">
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="allow_registration" checked>
                                            <label for="allow_registration">السماح بالتسجيل</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="email_verification" checked>
                                            <label for="email_verification">تفعيل التحقق من البريد الإلكتروني</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="allow_comments" checked>
                                            <label for="allow_comments">السماح بالتعليقات</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                    <button type="reset" class="btn btn-secondary">إعادة تعيين</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- إعدادات SEO -->
                        <div class="tab-content" id="seo">
                            <h2>إعدادات تحسين محركات البحث (SEO)</h2>
                            <form>
                                <div class="form-group">
                                    <label for="meta_title">العنوان الافتراضي (Meta Title)</label>
                                    <input type="text" id="meta_title" class="form-control" value="WeCima - موقع مشاهدة الأفلام والمسلسلات">
                                </div>
                                
                                <div class="form-group">
                                    <label for="meta_description">الوصف الافتراضي (Meta Description)</label>
                                    <textarea id="meta_description" class="form-control">WeCima - موقع مشاهدة الأفلام والمسلسلات العربية والأجنبية اون لاين بجودة عالية</textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="meta_keywords">الكلمات المفتاحية (Meta Keywords)</label>
                                    <input type="text" id="meta_keywords" class="form-control" value="أفلام, مسلسلات, أفلام عربية, مسلسلات أجنبية, مشاهدة اون لاين">
                                    <small class="form-text">افصل بين الكلمات المفتاحية بفواصل</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="google_analytics">كود Google Analytics</label>
                                    <textarea id="google_analytics" class="form-control" placeholder="ضع كود التتبع هنا"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>خيارات إضافية</label>
                                    <div class="checkbox-group">
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="enable_sitemap" checked>
                                            <label for="enable_sitemap">تفعيل خريطة الموقع (Sitemap)</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="enable_robots" checked>
                                            <label for="enable_robots">تفعيل ملف robots.txt</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="canonical_urls" checked>
                                            <label for="canonical_urls">تفعيل الروابط القانونية (Canonical URLs)</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                    <button type="reset" class="btn btn-secondary">إعادة تعيين</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- وسائل التواصل الاجتماعي -->
                        <div class="tab-content" id="social">
                            <h2>إعدادات وسائل التواصل الاجتماعي</h2>
                            <form>
                                <div class="form-group">
                                    <label for="facebook_url">رابط صفحة Facebook</label>
                                    <div class="input-with-icon">
                                        <i class="fab fa-facebook-f"></i>
                                        <input type="url" id="facebook_url" class="form-control" value="https://facebook.com/wecima">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="twitter_url">رابط حساب Twitter</label>
                                    <div class="input-with-icon">
                                        <i class="fab fa-twitter"></i>
                                        <input type="url" id="twitter_url" class="form-control" value="https://twitter.com/wecima">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="instagram_url">رابط حساب Instagram</label>
                                    <div class="input-with-icon">
                                        <i class="fab fa-instagram"></i>
                                        <input type="url" id="instagram_url" class="form-control" value="https://instagram.com/wecima">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="youtube_url">رابط قناة YouTube</label>
                                    <div class="input-with-icon">
                                        <i class="fab fa-youtube"></i>
                                        <input type="url" id="youtube_url" class="form-control" value="https://youtube.com/wecima">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>خيارات المشاركة</label>
                                    <div class="checkbox-group">
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="enable_sharing" checked>
                                            <label for="enable_sharing">تفعيل أزرار المشاركة</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="show_share_count" checked>
                                            <label for="show_share_count">عرض عدد المشاركات</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                    <button type="reset" class="btn btn-secondary">إعادة تعيين</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- إعدادات API -->
                        <div class="tab-content" id="api">
                            <h2>إعدادات واجهة برمجة التطبيقات (API)</h2>
                            <form>
                                <div class="form-group">
                                    <label for="tmdb_api_key">مفتاح TMDB API</label>
                                    <input type="text" id="tmdb_api_key" class="form-control" value="1a2b3c4d5e6f7g8h9i0j">
                                    <small class="form-text">مفتاح API لقاعدة بيانات الأفلام (TMDB)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="youtube_api_key">مفتاح YouTube API</label>
                                    <input type="text" id="youtube_api_key" class="form-control" value="AIzaSyBxxx_xxxxxxxxxxxxxxxx">
                                </div>
                                
                                <div class="form-group">
                                    <label>خيارات API</label>
                                    <div class="checkbox-group">
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="enable_public_api" checked>
                                            <label for="enable_public_api">تفعيل واجهة API العامة</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="require_api_key" checked>
                                            <label for="require_api_key">طلب مفتاح API للوصول</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="api_rate_limiting" checked>
                                            <label for="api_rate_limiting">تفعيل تحديد معدل الطلبات</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="api_rate_limit">الحد الأقصى للطلبات (في الدقيقة)</label>
                                    <input type="number" id="api_rate_limit" class="form-control" value="60">
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                    <button type="reset" class="btn btn-secondary">إعادة تعيين</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- إعدادات البريد الإلكتروني -->
                        <div class="tab-content" id="email">
                            <h2>إعدادات البريد الإلكتروني</h2>
                            <form>
                                <div class="form-group">
                                    <label for="mail_driver">نوع خدمة البريد</label>
                                    <select id="mail_driver" class="form-control">
                                        <option value="smtp" selected>SMTP</option>
                                        <option value="sendmail">Sendmail</option>
                                        <option value="mailgun">Mailgun</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="mail_host">خادم SMTP</label>
                                    <input type="text" id="mail_host" class="form-control" value="smtp.gmail.com">
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="mail_port">منفذ SMTP</label>
                                        <input type="number" id="mail_port" class="form-control" value="587">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="mail_encryption">تشفير SMTP</label>
                                        <select id="mail_encryption" class="form-control">
                                            <option value="tls" selected>TLS</option>
                                            <option value="ssl">SSL</option>
                                            <option value="none">بدون تشفير</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="mail_username">اسم المستخدم</label>
                                        <input type="text" id="mail_username" class="form-control" value="info@wecima.com">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="mail_password">كلمة المرور</label>
                                        <input type="password" id="mail_password" class="form-control" value="********">
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="mail_from_address">عنوان المرسل</label>
                                        <input type="email" id="mail_from_address" class="form-control" value="no-reply@wecima.com">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="mail_from_name">اسم المرسل</label>
                                        <input type="text" id="mail_from_name" class="form-control" value="WeCima">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <button type="button" class="btn btn-secondary">اختبار الإعدادات</button>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                    <button type="reset" class="btn btn-secondary">إعادة تعيين</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // تبديل التبويبات
            $('.tab-btn').click(function() {
                var tabId = $(this).data('tab');
                
                // إزالة الفئة النشطة من جميع الأزرار والمحتويات
                $('.tab-btn').removeClass('active');
                $('.tab-content').removeClass('active');
                
                // إضافة الفئة النشطة للزر والمحتوى المحدد
                $(this).addClass('active');
                $('#' + tabId).addClass('active');
            });
            
            // عرض اسم الملف المحدد
            $('.file-input').change(function() {
                var fileName = $(this).val().split('\\').pop();
                if (fileName) {
                    $(this).next().next('.file-name').text(fileName);
                } else {
                    $(this).next().next('.file-name').text('لم يتم اختيار ملف');
                }
            });
        });
    </script>
</body>
</html>