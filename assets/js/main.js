/**
 * ملف JavaScript الرئيسي لموقع WeCima
 * يحتوي على الوظائف الأساسية للموقع
 */

// تنفيذ الكود عند اكتمال تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // تهيئة المتغيرات العامة
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const toggleSidebarBtn = document.querySelector('.toggle-sidebar');
    
    // تهيئة الإشعارات
    initNotifications();
    
    // تهيئة الجداول
    initDataTables();
    
    // تهيئة النوافذ المنبثقة
    initModals();
    
    // تهيئة أزرار الإجراءات
    initActionButtons();
    
    // تهيئة التنقل بين الصفحات
    initNavigation();
    
    // تهيئة زر تبديل القائمة الجانبية للشاشات الصغيرة
    if (toggleSidebarBtn) {
        toggleSidebarBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });
    }
    
    // تحميل الإحصائيات في الصفحة الرئيسية
    if (document.querySelector('.stats-cards')) {
        loadDashboardStats();
    }
});

/**
 * تهيئة نظام الإشعارات
 */
function initNotifications() {
    // الحصول على عناصر الإشعارات
    const notificationBtn = document.querySelector('.notification-btn');
    const notificationDropdown = document.querySelector('.notification-dropdown');
    
    if (notificationBtn && notificationDropdown) {
        // إظهار/إخفاء قائمة الإشعارات عند النقر على زر الإشعارات
        notificationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            notificationDropdown.classList.toggle('show');
            
            // تحديث حالة الإشعارات إلى "مقروءة" عند فتح القائمة
            if (notificationDropdown.classList.contains('show')) {
                markNotificationsAsRead();
            }
        });
        
        // إخفاء قائمة الإشعارات عند النقر خارجها
        document.addEventListener('click', function(e) {
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('show');
            }
        });
        
        // تحميل الإشعارات
        loadNotifications();
    }
}

/**
 * تحميل الإشعارات من الخادم
 */
function loadNotifications() {
    const notificationList = document.querySelector('.notification-list');
    
    if (notificationList) {
        // إرسال طلب AJAX للحصول على الإشعارات
        fetch('api/notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // تحديث عدد الإشعارات غير المقروءة
                    updateUnreadCount(data.unread_count);
                    
                    // عرض الإشعارات
                    notificationList.innerHTML = '';
                    
                    if (data.notifications.length > 0) {
                        data.notifications.forEach(notification => {
                            const notificationItem = document.createElement('div');
                            notificationItem.className = `notification-item ${notification.is_read ? '' : 'unread'}`;
                            notificationItem.innerHTML = `
                                <div class="notification-icon ${notification.type}">
                                    <i class="fas ${getNotificationIcon(notification.type)}"></i>
                                </div>
                                <div class="notification-content">
                                    <p>${notification.message}</p>
                                    <span class="notification-time">${notification.time}</span>
                                </div>
                            `;
                            notificationList.appendChild(notificationItem);
                        });
                    } else {
                        notificationList.innerHTML = '<div class="no-notifications">لا توجد إشعارات</div>';
                    }
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل الإشعارات:', error);
            });
    }
}

/**
 * تحديث عدد الإشعارات غير المقروءة
 */
function updateUnreadCount(count) {
    const unreadBadge = document.querySelector('.notification-badge');
    
    if (unreadBadge) {
        if (count > 0) {
            unreadBadge.textContent = count;
            unreadBadge.style.display = 'block';
        } else {
            unreadBadge.style.display = 'none';
        }
    }
}

/**
 * تعيين الإشعارات كمقروءة
 */
function markNotificationsAsRead() {
    fetch('api/notifications_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // تحديث عدد الإشعارات غير المقروءة
            updateUnreadCount(0);
            
            // تحديث حالة الإشعارات في واجهة المستخدم
            const unreadItems = document.querySelectorAll('.notification-item.unread');
            unreadItems.forEach(item => {
                item.classList.remove('unread');
            });
        }
    })
    .catch(error => {
        console.error('خطأ في تعيين الإشعارات كمقروءة:', error);
    });
}

/**
 * الحصول على أيقونة الإشعار حسب النوع
 */
function getNotificationIcon(type) {
    switch (type) {
        case 'comment':
            return 'fa-comment';
        case 'user':
            return 'fa-user';
        case 'movie':
            return 'fa-film';
        case 'series':
            return 'fa-tv';
        case 'warning':
            return 'fa-exclamation-triangle';
        default:
            return 'fa-bell';
    }
}

/**
 * تهيئة جداول البيانات
 */
function initDataTables() {
    const tables = document.querySelectorAll('.data-table table');
    
    tables.forEach(table => {
        // إضافة وظائف الفرز والبحث والتصفح للجداول
        const tableHeader = table.querySelector('thead');
        const tableBody = table.querySelector('tbody');
        
        if (tableHeader && tableBody) {
            // إضافة أحداث النقر على رؤوس الأعمدة للفرز
            const headerCells = tableHeader.querySelectorAll('th');
            headerCells.forEach(cell => {
                if (!cell.classList.contains('no-sort')) {
                    cell.addEventListener('click', function() {
                        sortTable(table, Array.from(headerCells).indexOf(cell));
                    });
                    cell.classList.add('sortable');
                }
            });
        }
        
        // إضافة حقل البحث للجدول
        const tableContainer = table.closest('.data-table');
        if (tableContainer) {
            const tableHeader = tableContainer.querySelector('.table-header');
            if (tableHeader) {
                const searchInput = document.createElement('input');
                searchInput.type = 'text';
                searchInput.className = 'table-search';
                searchInput.placeholder = 'بحث...';
                searchInput.addEventListener('input', function() {
                    searchTable(table, this.value);
                });
                
                const searchContainer = document.createElement('div');
                searchContainer.className = 'search-container';
                searchContainer.appendChild(searchInput);
                
                const tableActions = tableHeader.querySelector('.table-actions');
                if (tableActions) {
                    tableActions.insertBefore(searchContainer, tableActions.firstChild);
                } else {
                    tableHeader.appendChild(searchContainer);
                }
            }
        }
    });
}

/**
 * فرز الجدول حسب العمود المحدد
 */
function sortTable(table, columnIndex) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const headerCells = table.querySelectorAll('thead th');
    const headerCell = headerCells[columnIndex];
    
    // تحديد اتجاه الفرز (تصاعدي أو تنازلي)
    const sortDirection = headerCell.classList.contains('sort-asc') ? 'desc' : 'asc';
    
    // إزالة فئات الفرز من جميع خلايا الرأس
    headerCells.forEach(cell => {
        cell.classList.remove('sort-asc', 'sort-desc');
    });
    
    // إضافة فئة الفرز الحالية
    headerCell.classList.add(`sort-${sortDirection}`);
    
    // فرز الصفوف
    rows.sort((a, b) => {
        const cellA = a.querySelectorAll('td')[columnIndex].textContent.trim();
        const cellB = b.querySelectorAll('td')[columnIndex].textContent.trim();
        
        // التحقق مما إذا كانت القيم أرقامًا
        const numA = parseFloat(cellA);
        const numB = parseFloat(cellB);
        
        if (!isNaN(numA) && !isNaN(numB)) {
            return sortDirection === 'asc' ? numA - numB : numB - numA;
        } else {
            // فرز نصي
            return sortDirection === 'asc' 
                ? cellA.localeCompare(cellB, 'ar')
                : cellB.localeCompare(cellA, 'ar');
        }
    });
    
    // إعادة ترتيب الصفوف في الجدول
    rows.forEach(row => {
        tbody.appendChild(row);
    });
}

/**
 * البحث في الجدول
 */
function searchTable(table, query) {
    const rows = table.querySelectorAll('tbody tr');
    const lowerQuery = query.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(lowerQuery)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

/**
 * تهيئة النوافذ المنبثقة
 */
function initModals() {
    // الحصول على جميع أزرار فتح النوافذ المنبثقة
    const modalTriggers = document.querySelectorAll('[data-modal]');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            
            const modalId = this.getAttribute('data-modal');
            const modal = document.getElementById(modalId);
            
            if (modal) {
                openModal(modal);
            }
        });
    });
    
    // إضافة أحداث إغلاق النوافذ المنبثقة
    const closeButtons = document.querySelectorAll('.modal-close, .modal-cancel');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            closeModal(modal);
        });
    });
    
    // إغلاق النافذة المنبثقة عند النقر خارجها
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this);
            }
        });
    });
}

/**
 * فتح نافذة منبثقة
 */
function openModal(modal) {
    modal.classList.add('show');
    document.body.classList.add('modal-open');
}

/**
 * إغلاق نافذة منبثقة
 */
function closeModal(modal) {
    modal.classList.remove('show');
    document.body.classList.remove('modal-open');
}

/**
 * تهيئة أزرار الإجراءات
 */
function initActionButtons() {
    // أزرار التعديل
    const editButtons = document.querySelectorAll('.action-btn.edit');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const id = row.getAttribute('data-id');
            const type = row.getAttribute('data-type');
            
            if (id && type) {
                window.location.href = `${type}_edit.php?id=${id}`;
            }
        });
    });
    
    // أزرار العرض
    const viewButtons = document.querySelectorAll('.action-btn.view');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const id = row.getAttribute('data-id');
            const type = row.getAttribute('data-type');
            
            if (id && type) {
                window.location.href = `${type}_view.php?id=${id}`;
            }
        });
    });
    
    // أزرار الحذف
    const deleteButtons = document.querySelectorAll('.action-btn.delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const id = row.getAttribute('data-id');
            const type = row.getAttribute('data-type');
            const name = row.querySelector('td:nth-child(2)').textContent;
            
            if (id && type) {
                if (confirm(`هل أنت متأكد من حذف "${name}"؟`)) {
                    deleteItem(id, type, row);
                }
            }
        });
    });
    
    // أزرار تغيير الحالة
    const statusButtons = document.querySelectorAll('.status-toggle');
    statusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const id = row.getAttribute('data-id');
            const type = row.getAttribute('data-type');
            const currentStatus = this.getAttribute('data-status');
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            
            if (id && type) {
                toggleStatus(id, type, newStatus, this);
            }
        });
    });
}

/**
 * حذف عنصر
 */
function deleteItem(id, type, row) {
    fetch(`api/${type}_delete.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // إزالة الصف من الجدول
            row.remove();
            
            // عرض رسالة نجاح
            showNotification('تم الحذف بنجاح', 'success');
        } else {
            // عرض رسالة خطأ
            showNotification(data.message || 'حدث خطأ أثناء الحذف', 'error');
        }
    })
    .catch(error => {
        console.error('خطأ في حذف العنصر:', error);
        showNotification('حدث خطأ أثناء الحذف', 'error');
    });
}

/**
 * تغيير حالة عنصر
 */
function toggleStatus(id, type, newStatus, button) {
    fetch(`api/${type}_status.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id, status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // تحديث حالة الزر
            button.setAttribute('data-status', newStatus);
            
            // تحديث نص الحالة
            const statusCell = button.closest('td').previousElementSibling;
            if (statusCell) {
                statusCell.innerHTML = `<span class="status ${newStatus}">${newStatus === 'active' ? 'نشط' : 'غير نشط'}</span>`;
            }
            
            // عرض رسالة نجاح
            showNotification('تم تغيير الحالة بنجاح', 'success');
        } else {
            // عرض رسالة خطأ
            showNotification(data.message || 'حدث خطأ أثناء تغيير الحالة', 'error');
        }
    })
    .catch(error => {
        console.error('خطأ في تغيير الحالة:', error);
        showNotification('حدث خطأ أثناء تغيير الحالة', 'error');
    });
}

/**
 * عرض إشعار للمستخدم
 */
function showNotification(message, type = 'info') {
    // إنشاء عنصر الإشعار
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${getNotificationTypeIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close"><i class="fas fa-times"></i></button>
    `;
    
    // إضافة الإشعار إلى الصفحة
    const notificationsContainer = document.querySelector('.notifications-container');
    if (!notificationsContainer) {
        // إنشاء حاوية الإشعارات إذا لم تكن موجودة
        const container = document.createElement('div');
        container.className = 'notifications-container';
        document.body.appendChild(container);
        container.appendChild(notification);
    } else {
        notificationsContainer.appendChild(notification);
    }
    
    // إضافة حدث إغلاق الإشعار
    const closeButton = notification.querySelector('.notification-close');
    closeButton.addEventListener('click', function() {
        notification.remove();
    });
    
    // إخفاء الإشعار تلقائيًا بعد 5 ثوانٍ
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

/**
 * الحصول على أيقونة نوع الإشعار
 */
function getNotificationTypeIcon(type) {
    switch (type) {
        case 'success':
            return 'fa-check-circle';
        case 'error':
            return 'fa-exclamation-circle';
        case 'warning':
            return 'fa-exclamation-triangle';
        default:
            return 'fa-info-circle';
    }
}

/**
 * تهيئة التنقل بين الصفحات
 */
function initNavigation() {
    // تحديد الرابط النشط في القائمة الجانبية
    const currentPath = window.location.pathname;
    const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
    
    sidebarLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentPath.includes(href) && href !== '#') {
            link.classList.add('active');
        }
    });
}

/**
 * تحميل إحصائيات لوحة التحكم
 */
function loadDashboardStats() {
    fetch('api/stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // تحديث بطاقات الإحصائيات
                updateStatCards(data.data);
                
                // تحميل الرسوم البيانية إذا كانت موجودة
                if (typeof initCharts === 'function') {
                    initCharts(data.data);
                }
            }
        })
        .catch(error => {
            console.error('خطأ في تحميل الإحصائيات:', error);
        });
}

/**
 * تحديث بطاقات الإحصائيات
 */
function updateStatCards(data) {
    // تحديث إحصائيات الأفلام
    const moviesCard = document.querySelector('.stat-card .content h3[data-stat="movies"]');
    if (moviesCard && data.movies) {
        moviesCard.textContent = data.movies.total;
    }
    
    // تحديث إحصائيات المسلسلات
    const seriesCard = document.querySelector('.stat-card .content h3[data-stat="series"]');
    if (seriesCard && data.series) {
        seriesCard.textContent = data.series.total;
    }
    
    // تحديث إحصائيات المستخدمين
    const usersCard = document.querySelector('.stat-card .content h3[data-stat="users"]');
    if (usersCard && data.users) {
        usersCard.textContent = data.users.total;
    }
    
    // تحديث إحصائيات التعليقات
    const commentsCard = document.querySelector('.stat-card .content h3[data-stat="comments"]');
    if (commentsCard && data.comments) {
        commentsCard.textContent = data.comments.total;
    }
}