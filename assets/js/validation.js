/**
 * ملف التحقق من صحة النماذج لموقع WeCima
 * يستخدم للتحقق من صحة البيانات المدخلة في النماذج
 */

const WeCimaValidation = {
    // رسائل الخطأ
    errorMessages: {
        required: 'هذا الحقل مطلوب',
        email: 'يرجى إدخال بريد إلكتروني صحيح',
        minLength: 'يجب أن يحتوي هذا الحقل على الأقل {min} حرف',
        maxLength: 'يجب أن لا يتجاوز هذا الحقل {max} حرف',
        number: 'يرجى إدخال رقم صحيح',
        url: 'يرجى إدخال رابط صحيح',
        match: 'الحقول غير متطابقة',
        year: 'يرجى إدخال سنة صحيحة',
        fileType: 'نوع الملف غير مدعوم',
        fileSize: 'حجم الملف كبير جدًا'
    },
    
    // تهيئة التحقق من صحة النماذج
    init: function() {
        document.addEventListener('DOMContentLoaded', () => {
            // التحقق من وجود نماذج
            const forms = document.querySelectorAll('form[data-validate="true"]');
            
            forms.forEach(form => {
                this.setupFormValidation(form);
            });
            
            // إعداد التحقق من صحة الحقول بشكل مباشر
            const validateInputs = document.querySelectorAll('[data-validate]');
            
            validateInputs.forEach(input => {
                if (!input.form || !input.form.hasAttribute('data-validate')) {
                    this.setupInputValidation(input);
                }
            });
        });
    },
    
    // إعداد التحقق من صحة النموذج
    setupFormValidation: function(form) {
        // إضافة حدث عند تقديم النموذج
        form.addEventListener('submit', (e) => {
            // التحقق من صحة جميع الحقول
            const isValid = this.validateForm(form);
            
            // منع تقديم النموذج إذا كان غير صحيح
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // إضافة التحقق من صحة الحقول عند تغييرها
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            this.setupInputValidation(input);
        });
    },
    
    // إعداد التحقق من صحة الحقل
    setupInputValidation: function(input) {
        // إضافة حدث عند تغيير قيمة الحقل
        input.addEventListener('blur', () => {
            this.validateInput(input);
        });
        
        // إضافة حدث عند الكتابة في الحقل
        input.addEventListener('input', () => {
            // إزالة رسالة الخطأ إذا كانت موجودة
            const errorElement = this.getErrorElement(input);
            
            if (errorElement) {
                errorElement.textContent = '';
                errorElement.style.display = 'none';
                input.classList.remove('is-invalid');
            }
        });
    },
    
    // التحقق من صحة النموذج
    validateForm: function(form) {
        let isValid = true;
        
        // التحقق من صحة جميع الحقول
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            // التحقق من صحة الحقل
            const inputValid = this.validateInput(input);
            
            // تحديث حالة النموذج
            isValid = isValid && inputValid;
        });
        
        return isValid;
    },
    
    // التحقق من صحة الحقل
    validateInput: function(input) {
        // تجاهل الحقول المعطلة
        if (input.disabled) {
            return true;
        }
        
        let isValid = true;
        let errorMessage = '';
        
        // الحصول على قواعد التحقق
        const rules = this.getValidationRules(input);
        
        // التحقق من كل قاعدة
        for (const rule in rules) {
            if (rules.hasOwnProperty(rule)) {
                const ruleValue = rules[rule];
                
                // التحقق من القاعدة
                const ruleValid = this.validateRule(input, rule, ruleValue);
                
                // إذا كانت القاعدة غير صحيحة
                if (!ruleValid.valid) {
                    isValid = false;
                    errorMessage = ruleValid.message;
                    break;
                }
            }
        }
        
        // عرض رسالة الخطأ إذا كان الحقل غير صحيح
        this.showError(input, errorMessage, !isValid);
        
        return isValid;
    },
    
    // التحقق من قاعدة
    validateRule: function(input, rule, ruleValue) {
        const value = input.value.trim();
        
        switch (rule) {
            case 'required':
                if (ruleValue && value === '') {
                    return {
                        valid: false,
                        message: this.errorMessages.required
                    };
                }
                break;
                
            case 'email':
                if (ruleValue && value !== '') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    
                    if (!emailRegex.test(value)) {
                        return {
                            valid: false,
                            message: this.errorMessages.email
                        };
                    }
                }
                break;
                
            case 'minLength':
                if (value !== '' && value.length < ruleValue) {
                    return {
                        valid: false,
                        message: this.errorMessages.minLength.replace('{min}', ruleValue)
                    };
                }
                break;
                
            case 'maxLength':
                if (value !== '' && value.length > ruleValue) {
                    return {
                        valid: false,
                        message: this.errorMessages.maxLength.replace('{max}', ruleValue)
                    };
                }
                break;
                
            case 'number':
                if (ruleValue && value !== '') {
                    const numberRegex = /^-?\d+(\.\d+)?$/;
                    
                    if (!numberRegex.test(value)) {
                        return {
                            valid: false,
                            message: this.errorMessages.number
                        };
                    }
                }
                break;
                
            case 'url':
                if (ruleValue && value !== '') {
                    try {
                        new URL(value);
                    } catch (e) {
                        return {
                            valid: false,
                            message: this.errorMessages.url
                        };
                    }
                }
                break;
                
            case 'match':
                if (ruleValue && value !== '') {
                    const matchInput = document.getElementById(ruleValue);
                    
                    if (matchInput && value !== matchInput.value) {
                        return {
                            valid: false,
                            message: this.errorMessages.match
                        };
                    }
                }
                break;
                
            case 'year':
                if (ruleValue && value !== '') {
                    const year = parseInt(value);
                    const currentYear = new Date().getFullYear();
                    
                    if (isNaN(year) || year < 1900 || year > currentYear + 5) {
                        return {
                            valid: false,
                            message: this.errorMessages.year
                        };
                    }
                }
                break;
                
            case 'fileType':
                if (ruleValue && input.type === 'file' && input.files.length > 0) {
                    const file = input.files[0];
                    const allowedTypes = ruleValue.split(',').map(type => type.trim());
                    
                    // التحقق من نوع الملف
                    const fileType = file.type;
                    const fileExtension = file.name.split('.').pop().toLowerCase();
                    
                    let isValidType = false;
                    
                    for (const type of allowedTypes) {
                        if (fileType.includes(type) || type === fileExtension) {
                            isValidType = true;
                            break;
                        }
                    }
                    
                    if (!isValidType) {
                        return {
                            valid: false,
                            message: this.errorMessages.fileType
                        };
                    }
                }
                break;
                
            case 'fileSize':
                if (ruleValue && input.type === 'file' && input.files.length > 0) {
                    const file = input.files[0];
                    const maxSize = parseInt(ruleValue) * 1024 * 1024; // تحويل إلى بايت
                    
                    if (file.size > maxSize) {
                        return {
                            valid: false,
                            message: this.errorMessages.fileSize
                        };
                    }
                }
                break;
        }
        
        return { valid: true };
    },
    
    // الحصول على قواعد التحقق
    getValidationRules: function(input) {
        const rules = {};
        
        // التحقق من السمات
        if (input.hasAttribute('required')) {
            rules.required = true;
        }
        
        if (input.hasAttribute('data-validate-email')) {
            rules.email = true;
        }
        
        if (input.hasAttribute('data-validate-min-length')) {
            rules.minLength = parseInt(input.getAttribute('data-validate-min-length'));
        }
        
        if (input.hasAttribute('data-validate-max-length')) {
            rules.maxLength = parseInt(input.getAttribute('data-validate-max-length'));
        }
        
        if (input.hasAttribute('data-validate-number')) {
            rules.number = true;
        }
        
        if (input.hasAttribute('data-validate-url')) {
            rules.url = true;
        }
        
        if (input.hasAttribute('data-validate-match')) {
            rules.match = input.getAttribute('data-validate-match');
        }
        
        if (input.hasAttribute('data-validate-year')) {
            rules.year = true;
        }
        
        if (input.hasAttribute('data-validate-file-type')) {
            rules.fileType = input.getAttribute('data-validate-file-type');
        }
        
        if (input.hasAttribute('data-validate-file-size')) {
            rules.fileSize = input.getAttribute('data-validate-file-size');
        }
        
        return rules;
    },
    
    // عرض رسالة الخطأ
    showError: function(input, message, show) {
        // الحصول على عنصر الخطأ
        let errorElement = this.getErrorElement(input);
        
        // إنشاء عنصر الخطأ إذا لم يكن موجودًا
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            input.parentNode.appendChild(errorElement);
        }
        
        // تحديث رسالة الخطأ
        errorElement.textContent = message;
        
        // عرض أو إخفاء رسالة الخطأ
        errorElement.style.display = show ? 'block' : 'none';
        
        // إضافة أو إزالة فئة الخطأ
        if (show) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        } else {
            input.classList.remove('is-invalid');
            
            // إضافة فئة صحيح إذا كان الحقل غير فارغ
            if (input.value.trim() !== '') {
                input.classList.add('is-valid');
            }
        }
    },
    
    // الحصول على عنصر الخطأ
    getErrorElement: function(input) {
        // البحث عن عنصر الخطأ
        const errorElement = input.parentNode.querySelector('.invalid-feedback');
        
        return errorElement;
    }
};

// تهيئة التحقق من صحة النماذج
WeCimaValidation.init();