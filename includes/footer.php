    </div>
    <!-- نهاية المحتوى الرئيسي -->
</div>
<!-- نهاية حاوية لوحة التحكم -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS (إذا كنت تستخدم Bootstrap) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- سكريبت مخصص -->
<script>
    // تفعيل التنبيهات
    function showAlert(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        `;
        
        document.querySelector('.main-content').prepend(alertDiv);
        
        // إخفاء التنبيه تلقائيًا بعد 5 ثوانٍ
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    }
    
    // تأكيد الحذف
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-confirm')) {
            if (!confirm('هل أنت متأكد من رغبتك في الحذف؟ لا يمكن التراجع عن هذا الإجراء.')) {
                e.preventDefault();
            }
        }
    });
    
    // تبديل القائمة الجانبية في الشاشات الصغيرة
    const toggleSidebarBtn = document.getElementById('toggle-sidebar');
    if (toggleSidebarBtn) {
        toggleSidebarBtn.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    }
    
    // تحميل الصور المختارة
    const imageInputs = document.querySelectorAll('.image-upload');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const preview = this.parentElement.querySelector('.image-preview');
            if (preview && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // تحديث حالة القائمة النشطة
    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        const menuItems = document.querySelectorAll('.sidebar-menu a');
        
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (currentPath.includes(href) && href !== '#') {
                item.classList.add('active');
            }
        });
    });
    
    // إضافة حقول ديناميكية (مثل ممثلين أو مواسم)
    const addFieldBtns = document.querySelectorAll('.add-field-btn');
    addFieldBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const container = document.getElementById(this.dataset.container);
            const template = document.getElementById(this.dataset.template).content.cloneNode(true);
            container.appendChild(template);
            
            // تحديث أرقام الحقول
            const fields = container.querySelectorAll('.dynamic-field');
            fields.forEach((field, index) => {
                field.querySelector('.field-number').textContent = index + 1;
            });
            
            // إضافة حدث لزر الحذف
            const removeBtn = container.lastElementChild.querySelector('.remove-field-btn');
            removeBtn.addEventListener('click', function() {
                this.closest('.dynamic-field').remove();
                
                // تحديث أرقام الحقول بعد الحذف
                const updatedFields = container.querySelectorAll('.dynamic-field');
                updatedFields.forEach((field, index) => {
                    field.querySelector('.field-number').textContent = index + 1;
                });
            });
        });
    });
    
    // تفعيل محرر النصوص المتقدم (إذا كان موجودًا)
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '.rich-editor',
            directionality: 'rtl',
            language: 'ar',
            plugins: 'link image lists table code',
            toolbar: 'undo redo | formatselect | bold italic | alignright aligncenter alignleft | bullist numlist | link image | table | code',
            height: 300
        });
    }
    
    // تفعيل اختيار التاريخ (إذا كان موجودًا)
    const dateInputs = document.querySelectorAll('.date-picker');
    if (typeof flatpickr !== 'undefined' && dateInputs.length > 0) {
        flatpickr(dateInputs, {
            dateFormat: "Y-m-d",
            locale: "ar"
        });
    }
    
    // تفعيل اختيار متعدد (إذا كان موجودًا)
    const multiSelects = document.querySelectorAll('.multi-select');
    if (typeof Choices !== 'undefined' && multiSelects.length > 0) {
        multiSelects.forEach(select => {
            new Choices(select, {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'اختر...',
                itemSelectText: 'اضغط للاختيار'
            });
        });
    }
</script>

<!-- سكريبت الرسوم البيانية (إذا كان موجودًا في الصفحة) -->
<?php if (isset($page) && $page === 'statistics'): ?>
<script>
    // تحديث الرسوم البيانية عند تغيير الفترة الزمنية
    document.getElementById('apply-filter').addEventListener('click', function() {
        const period = document.getElementById('period-filter').value;
        
        // إرسال طلب AJAX لجلب البيانات الجديدة
        fetch(`api/statistics.php?period=${period}`)
            .then(response => response.json())
            .then(data => {
                // تحديث الرسوم البيانية بالبيانات الجديدة
                updateCharts(data);
            })
            .catch(error => {
                console.error('خطأ في جلب البيانات:', error);
                showAlert('حدث خطأ أثناء تحديث البيانات', 'danger');
            });
    });
    
    function updateCharts(data) {
        // تحديث الرسوم البيانية بناءً على البيانات المستلمة
        // هذه مجرد أمثلة، يجب تعديلها حسب هيكل البيانات الفعلي
        
        // تحديث رسم المشاهدات
        if (viewsChart && data.views) {
            viewsChart.data.labels = data.views.labels;
            viewsChart.data.datasets[0].data = data.views.data;
            viewsChart.update();
        }
        
        // تحديث رسم البلدان
        if (countryChart && data.countries) {
            countryChart.data.labels = data.countries.labels;
            countryChart.data.datasets[0].data = data.countries.data;
            countryChart.update();
        }
        
        // تحديث رسم الأعمار
        if (ageChart && data.ages) {
            ageChart.data.labels = data.ages.labels;
            ageChart.data.datasets[0].data = data.ages.data;
            ageChart.update();
        }
        
        // تحديث رسم نشاط المستخدمين
        if (userActivityChart && data.activity) {
            userActivityChart.data.datasets[0].data = data.activity.logins;
            userActivityChart.data.datasets[1].data = data.activity.views;
            userActivityChart.update();
        }
        
        // تحديث رسم التعليقات
        if (commentsChart && data.comments) {
            commentsChart.data.datasets[0].data = data.comments.data;
            commentsChart.update();
        }
        
        // تحديث البطاقات الإحصائية
        if (data.stats) {
            document.querySelector('.stat-card:nth-child(1) .content h3').textContent = data.stats.movies;
            document.querySelector('.stat-card:nth-child(2) .content h3').textContent = data.stats.series;
            document.querySelector('.stat-card:nth-child(3) .content h3').textContent = data.stats.users;
            document.querySelector('.stat-card:nth-child(4) .content h3').textContent = data.stats.comments;
        }
    }
</script>
<?php endif; ?>

</body>
</html>