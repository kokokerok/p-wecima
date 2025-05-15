/**
 * ملف الرسوم البيانية لموقع WeCima
 * يستخدم مكتبة Chart.js لإنشاء رسوم بيانية تفاعلية
 */

// التأكد من تحميل مكتبة Chart.js
if (typeof Chart === 'undefined') {
    console.error('مكتبة Chart.js غير موجودة. يرجى تضمينها قبل هذا الملف.');
}

// كائن يحتوي على جميع الرسوم البيانية
const WeCimaCharts = {
    // تخزين مراجع الرسوم البيانية
    charts: {},
    
    // تهيئة الرسوم البيانية
    init: function() {
        // تهيئة الرسوم البيانية عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', () => {
            // التحقق من وجود عناصر الرسوم البيانية
            if (document.getElementById('moviesChart')) {
                this.createMoviesChart();
            }
            
            if (document.getElementById('seriesChart')) {
                this.createSeriesChart();
            }
            
            if (document.getElementById('usersChart')) {
                this.createUsersChart();
            }
            
            if (document.getElementById('trafficChart')) {
                this.createTrafficChart();
            }
            
            if (document.getElementById('commentsChart')) {
                this.createCommentsChart();
            }
        });
    },
    
    // إنشاء رسم بياني للأفلام
    createMoviesChart: function() {
        const ctx = document.getElementById('moviesChart').getContext('2d');
        
        // الحصول على البيانات من الخادم
        this.fetchData('/api/stats.php?type=movies', (data) => {
            // إنشاء رسم بياني للأفلام حسب التصنيف
            if (data && data.by_category) {
                const categories = data.by_category.map(item => item.name);
                const counts = data.by_category.map(item => item.count);
                
                this.charts.moviesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: categories,
                        datasets: [{
                            label: 'عدد الأفلام',
                            data: counts,
                            backgroundColor: 'rgba(229, 9, 20, 0.7)',
                            borderColor: 'rgba(229, 9, 20, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'توزيع الأفلام حسب التصنيف',
                                font: {
                                    size: 16
                                }
                            },
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    },
    
    // إنشاء رسم بياني للمسلسلات
    createSeriesChart: function() {
        const ctx = document.getElementById('seriesChart').getContext('2d');
        
        // الحصول على البيانات من الخادم
        this.fetchData('/api/stats.php?type=series', (data) => {
            // إنشاء رسم بياني للمسلسلات حسب السنة
            if (data && data.by_year) {
                const years = data.by_year.map(item => item.year);
                const counts = data.by_year.map(item => item.count);
                
                this.charts.seriesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: years,
                        datasets: [{
                            label: 'عدد المسلسلات',
                            data: counts,
                            backgroundColor: 'rgba(40, 167, 69, 0.2)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'توزيع المسلسلات حسب السنة',
                                font: {
                                    size: 16
                                }
                            },
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    },
    
    // إنشاء رسم بياني للمستخدمين
    createUsersChart: function() {
        const ctx = document.getElementById('usersChart').getContext('2d');
        
        // الحصول على البيانات من الخادم
        this.fetchData('/api/stats.php?type=users', (data) => {
            // إنشاء رسم بياني للمستخدمين الجدد حسب الشهر
            if (data && data.by_month) {
                const months = data.by_month.map(item => {
                    const date = new Date(item.month + '-01');
                    return date.toLocaleDateString('ar-EG', { month: 'short', year: 'numeric' });
                });
                const counts = data.by_month.map(item => item.count);
                
                this.charts.usersChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'المستخدمين الجدد',
                            data: counts,
                            backgroundColor: 'rgba(255, 193, 7, 0.2)',
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'المستخدمين الجدد حسب الشهر',
                                font: {
                                    size: 16
                                }
                            },
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    },
    
    // إنشاء رسم بياني لحركة المرور
    createTrafficChart: function() {
        const ctx = document.getElementById('trafficChart').getContext('2d');
        
        // الحصول على البيانات من الخادم
        this.fetchData('/api/stats.php?type=traffic', (data) => {
            // إنشاء رسم بياني لحركة المرور حسب اليوم
            if (data && data.by_day) {
                const days = data.by_day.map(item => {
                    const date = new Date(item.day);
                    return date.toLocaleDateString('ar-EG', { day: 'numeric', month: 'short' });
                });
                const counts = data.by_day.map(item => item.count);
                
                this.charts.trafficChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: days,
                        datasets: [{
                            label: 'عدد الزيارات',
                            data: counts,
                            backgroundColor: 'rgba(13, 110, 253, 0.2)',
                            borderColor: 'rgba(13, 110, 253, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'حركة المرور اليومية',
                                font: {
                                    size: 16
                                }
                            },
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    },
    
    // إنشاء رسم بياني للتعليقات
    createCommentsChart: function() {
        const ctx = document.getElementById('commentsChart').getContext('2d');
        
        // الحصول على البيانات من الخادم
        this.fetchData('/api/stats.php?type=comments', (data) => {
            // إنشاء رسم بياني دائري لتوزيع التعليقات حسب نوع المحتوى
            if (data && data.by_content_type) {
                const types = data.by_content_type.map(item => {
                    return item.type === 'movie' ? 'أفلام' : 'مسلسلات';
                });
                const counts = data.by_content_type.map(item => item.count);
                
                this.charts.commentsChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: types,
                        datasets: [{
                            data: counts,
                            backgroundColor: [
                                'rgba(229, 9, 20, 0.7)',
                                'rgba(40, 167, 69, 0.7)'
                            ],
                            borderColor: [
                                'rgba(229, 9, 20, 1)',
                                'rgba(40, 167, 69, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'توزيع التعليقات حسب نوع المحتوى',
                                font: {
                                    size: 16
                                }
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
    },
    
    // تحديث الرسوم البيانية
    updateCharts: function() {
        // تحديث جميع الرسوم البيانية
        for (const chartName in this.charts) {
            if (this.charts.hasOwnProperty(chartName)) {
                this.charts[chartName].update();
            }
        }
    },
    
    // جلب البيانات من الخادم
    fetchData: function(url, callback) {
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('فشل في جلب البيانات');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    callback(data.data);
                } else {
                    console.error('خطأ في البيانات:', data.message);
                }
            })
            .catch(error => {
                console.error('خطأ في جلب البيانات:', error);
            });
    }
};

// تهيئة الرسوم البيانية
WeCimaCharts.init();