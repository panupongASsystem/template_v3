/* 
 * login-init.js
 * -------------
 * ไฟล์หลักสำหรับการเริ่มต้นระบบล็อกอิน
 * ทำหน้าที่เชื่อมต่อองค์ประกอบต่างๆ เข้าด้วยกัน
 */

// ค่า config สำหรับระบบล็อกอิน
const LoginConfig = {
    formIds: ['citizenLoginForm', 'staffLoginForm'],
    apiUrl: (function() {
        // ตรวจสอบว่า base_url มีค่าหรือไม่
        let baseUrl = window.base_url || '';
        
        // ตรวจสอบว่า base_url ลงท้ายด้วย / หรือไม่
        if (baseUrl && !baseUrl.endsWith('/')) {
            baseUrl += '/';
        }
        
        // ถ้า base_url ยังคงเป็น empty string ให้ใช้ค่าเริ่มต้นเป็น root path
        if (!baseUrl) {
            baseUrl = '/';
        }
        
        return baseUrl + 'auth_api/check_login';
    })(),
    reCaptchaSiteKey: window.RECAPTCHA_KEY || ''
};

// ฟังก์ชันเริ่มต้นระบบล็อกอิน
async function initLoginSystem() {
    console.log("Initializing login system...");

    try {
        // ตั้งค่าสำหรับทุกฟอร์ม
        for (const formId of LoginConfig.formIds) {
            const form = document.getElementById(formId);
            if (!form) {
                console.error(`Form '${formId}' not found`);
                continue;  // ข้ามไปฟอร์มถัดไป
            }

            // สร้าง fingerprint และเพิ่มลงในฟอร์ม
            if (window.FingerprintJS && typeof window.FingerprintJS.generate === 'function') {
                const fingerprint = await window.FingerprintJS.generate();
                window.FormHandler.appendHiddenField(form, 'fingerprint', fingerprint);
            }

            // เพิ่มข้อมูลอุปกรณ์
            if (window.FormHandler && typeof window.FormHandler.addAllDeviceInfo === 'function') {
                await window.FormHandler.addAllDeviceInfo(formId);
            }

            // ตั้งค่าการส่งฟอร์ม
            setupFormSubmission(form);
        }

        // ตั้งค่า event listeners อื่นๆ
        setupEventListeners();

        console.log("Login system initialized successfully");
    } catch (error) {
        console.error("Error initializing login system:", error);
    }
}

// ฟังก์ชันตั้งค่าการส่งฟอร์ม
function setupFormSubmission(form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // แสดงตัวโหลด
        window.UIManager.showLoading('กำลังเข้าสู่ระบบ...');

        // ตรวจสอบว่า reCAPTCHA พร้อมใช้งานหรือไม่
        if (typeof grecaptcha === 'undefined') {
            console.error('reCAPTCHA not loaded');
            window.UIManager.hideLoading();
            window.UIManager.showErrorModal('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถโหลด reCAPTCHA ได้');
            return;
        }

        // ดึง site key จากปุ่มหรือใช้จาก config
        const siteKey = form.querySelector('button[type="submit"]').getAttribute('data-sitekey') || LoginConfig.reCaptchaSiteKey;

        // เรียกใช้ reCAPTCHA อย่างถูกต้อง
        try {
            grecaptcha.ready(function () {
                grecaptcha.execute(siteKey, { action: 'login' })
                    .then(function (token) {
                        // เพิ่ม token ลงในฟอร์ม
                        window.FormHandler.appendHiddenField(form, 'g-recaptcha-response', token);

                        // ส่งฟอร์มด้วย AJAX
                        submitFormWithAjax(form);
                    })
                    .catch(function (error) {
                        console.error('reCAPTCHA error:', error);
                        window.UIManager.hideLoading();
                        window.UIManager.showErrorModal('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถยืนยันตัวตนผ่าน reCAPTCHA ได้');
                        
                        // ให้ส่งฟอร์มโดยไม่ใช้ reCAPTCHA ในกรณีที่เกิดข้อผิดพลาด
                        submitFormWithAjax(form);
                    });
            });
        } catch (error) {
            console.error('reCAPTCHA execution error:', error);
            window.UIManager.hideLoading();
            
            // ให้ส่งฟอร์มโดยไม่ใช้ reCAPTCHA ในกรณีที่เกิดข้อผิดพลาด
            submitFormWithAjax(form);
        }
    });
}

// ฟังก์ชันส่งฟอร์มด้วย AJAX
function submitFormWithAjax(form) {
    console.log("Submitting form via AJAX");
    
    // เพิ่ม debugging - แสดงข้อมูลที่กำลังจะส่ง
    const formData = new FormData(form);
    console.log("Form ID:", form.id, "Action:", form.action);
    console.log("API URL:", LoginConfig.apiUrl);
    
    // แสดงข้อมูลทั้งหมดในฟอร์ม
    console.log("Form data contents:");
    for (let [key, value] of formData.entries()) {
        // แสดงค่าอย่างปลอดภัย (ไม่แสดงรหัสผ่านเต็ม)
        let displayValue = value;
        if (key.includes('password')) {
            displayValue = '*'.repeat(value.length);
        } else if (typeof value === 'string' && value.length > 100) {
            displayValue = value.substring(0, 100) + '...';
        }
        console.log(`- ${key}: ${displayValue}`);
    }
    
    // ตรวจสอบว่ามี user_type ส่งไปด้วยหรือไม่
    if (!formData.has('user_type')) {
        console.warn("Warning: user_type is missing in form data!");
        // เพิ่ม user_type ตามประเภทฟอร์ม
        if (form.id === 'citizenLoginForm') {
            formData.append('user_type', 'public');
            console.log("Added missing user_type: public");
        } else if (form.id === 'staffLoginForm') {
            formData.append('user_type', 'staff');
            console.log("Added missing user_type: staff");
        }
    }

    // ตรวจสอบว่ามี fingerprint ส่งไปด้วยหรือไม่
    if (!formData.has('fingerprint')) {
        console.warn("Warning: fingerprint is missing in form data!");
        // สร้าง fingerprint จาก timestamp และ random
        const randomFingerprint = Math.random().toString(36).substring(2) + Date.now().toString(36);
        formData.append('fingerprint', randomFingerprint);
        console.log("Generated random fingerprint:", randomFingerprint);
    }

// ส่งข้อมูลด้วย fetch API
fetch(LoginConfig.apiUrl, {
    method: 'POST',
    body: formData,
    headers: {
        'X-Requested-With': 'XMLHttpRequest', // เพิ่ม header เพื่อระบุว่าเป็น AJAX request
        'Accept': 'application/json'
    },
    credentials: 'same-origin' // ส่ง cookies ไปด้วย
})
.then(response => {
    console.log("Response status:", response.status);
    console.log("Response headers:", response.headers);
    
    // ตรวจสอบประเภทของข้อมูลที่ตอบกลับมา
    const contentType = response.headers.get('content-type');
    console.log("Content type:", contentType);
    
    if (!response.ok) {
        console.error("Server returned error status:", response.status);
        // ถ้าเกิด error 500 ให้ลองส่งฟอร์มโดยตรงแทน
        if (response.status === 500) {
            console.warn("Server error 500, trying to submit form directly");
            
            // ส่งข้อความแสดงแก่ผู้ใช้
            window.UIManager.hideLoading();
            window.UIManager.showErrorModal('warning', 'กำลังเข้าสู่ระบบ', 'ระบบกำลังดำเนินการเข้าสู่ระบบ กรุณารอสักครู่...');
            
            // ถ้าฟอร์มมี action ให้ส่งโดยตรง
            if (form.action && form.action !== '') {
                console.log("Submitting directly to:", form.action);
                
                // สร้างฟอร์มใหม่และส่งโดยตรง
                const directForm = document.createElement('form');
                directForm.method = 'POST';
                directForm.action = form.action;
                directForm.style.display = 'none';
                
                // คัดลอกข้อมูลจาก formData
                for (let [key, value] of formData.entries()) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    directForm.appendChild(input);
                }
                
                document.body.appendChild(directForm);
                directForm.submit();
                return;
            }
        }
        
        throw new Error('Network response was not ok: ' + response.status);
    }
    
    // อ่านเป็น text ก่อนเสมอ
    return response.text().then(text => {
        console.log("Response text preview:", text.substring(0, 100));
        
        // ตรวจสอบว่ามี JSON อยู่ในการตอบกลับหรือไม่
        const jsonStartIndex = text.indexOf('{');
        const jsonEndIndex = text.lastIndexOf('}');
            
        if (jsonStartIndex >= 0 && jsonEndIndex > jsonStartIndex) {
            // กรณีที่มี HTML error และตามด้วย JSON
            if (text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html')) {
                console.warn("Response contains both HTML and JSON. Extracting JSON part...");
                
                // ตัดเอาเฉพาะส่วนที่เป็น JSON
                const jsonStr = text.substring(jsonStartIndex, jsonEndIndex + 1);
                try {
                    return JSON.parse(jsonStr);
                } catch (e) {
                    console.error("Failed to parse extracted JSON:", e);
                    throw new Error('Response contains HTML and invalid JSON');
                }
            } else if (jsonStartIndex === 0) {
                // กรณีที่เป็น JSON ปกติ
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error("Failed to parse JSON:", e);
                    throw new Error('Response is not valid JSON');
                }
            }
        }
        
        // กรณีที่เป็น HTML อย่างเดียว
        if (text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html')) {
            console.warn("Response is HTML, not JSON! Trying fallback method...");
            
            // ลองวิธีสำรอง - ส่งฟอร์มโดยตรง
            window.UIManager.hideLoading();
            window.UIManager.showErrorModal('warning', 'กำลังเข้าสู่ระบบ', 'ระบบกำลังดำเนินการด้วยวิธีสำรอง กรุณารอสักครู่...');
            
            // ถ้าฟอร์มมี action ให้ส่งโดยตรง
            if (form.action && form.action !== '') {
                // ส่งฟอร์มโดยตรงหลังจากรอสักครู่
                setTimeout(() => {
                    const directForm = document.createElement('form');
                    directForm.method = 'POST';
                    directForm.action = form.action;
                    directForm.style.display = 'none';
                    
                    // คัดลอกข้อมูลจาก formData
                    for (let [key, value] of formData.entries()) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value;
                        directForm.appendChild(input);
                    }
                    
                    document.body.appendChild(directForm);
                    directForm.submit();
                }, 2000);
            }
            
            throw new Error('Server returned HTML instead of JSON');
        }
            
            try {
                // ถ้าไม่ใช่ HTML ให้แปลงเป็น JSON
                return JSON.parse(text);
            } catch (e) {
                console.error("Failed to parse JSON:", e);
                throw new Error('Response is not valid JSON: ' + text.substring(0, 100));
            }
        });
    })
    .then(data => {
        console.log("Login response data:", data);
        window.UIManager.hideLoading();

        if (data.status === 'success') {
            // ล็อกอินสำเร็จ
            console.log("Login successful, data:", data);
            
            // ใช้ redirect URL จากการตอบกลับ JSON
            if (data.redirect) {
                console.log("Redirecting to:", data.redirect);
                window.location.href = data.redirect;
            } else {
                // กรณีที่ไม่มี redirect URL ให้ใช้ค่าเริ่มต้นตามประเภทผู้ใช้
                const isPublic = form.id === 'citizenLoginForm';
                const defaultUrl = isPublic ? 
                    (window.base_url ? window.base_url + 'Pages/service_systems' : '/Pages/service_systems') :
                    (window.base_url ? window.base_url + 'User/choice' : '/User/choice');
                
                console.log("No redirect URL provided, using default:", defaultUrl);
                window.location.href = defaultUrl;
            }
        }
        else if (data.status === 'blocked') {
            // ถูกบล็อค ให้แสดง modal
            console.log("Account is blocked. Remaining time:", data.remaining_time);
            window.UIManager.showBlockedModal(data.remaining_time, data.block_level || 1);
        }
        else {
            // แสดง modal แจ้งเตือนล็อกอินล้มเหลว
            console.log("Login failed:", data.message);
            window.UIManager.showLoginFailedModal(data.message, data.attempts, data.remaining_attempts);
        }
    })
    .catch(error => {
        console.error('Error details:', error);
        window.UIManager.hideLoading();
        
        // เพิ่ม debug info
        const errorInfo = {
            message: error.message,
            stack: error.stack,
            formId: form.id,
            endpoint: LoginConfig.apiUrl
        };
        console.error('Detailed error info:', errorInfo);
        
        // แสดงข้อความแสดงข้อผิดพลาดที่เฉพาะเจาะจงมากขึ้น
        if (error.message.includes('JSON')) {
            window.UIManager.showErrorModal('error', 'เกิดข้อผิดพลาดในการประมวลผล', 'เซิร์ฟเวอร์ส่งข้อมูลกลับมาไม่ถูกต้อง');
        } else if (error.message.includes('Network response')) {
            window.UIManager.showErrorModal('error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'เซิร์ฟเวอร์ตอบกลับด้วยสถานะข้อผิดพลาด - กรุณาตรวจสอบ Error Log');
        } else {
            window.UIManager.showErrorModal('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถติดต่อกับเซิร์ฟเวอร์ได้');
        }
        
        // บันทึก error log
        try {
            const errorLog = {
                timestamp: new Date().toISOString(),
                error: error.message,
                formId: form.id,
                userType: formData.get('user_type')
            };
            console.log("Error log:", errorLog);
            
            // อาจจะเก็บใน localStorage สำหรับการดีบักในอนาคต
            const storedErrors = JSON.parse(localStorage.getItem('login_errors') || '[]');
            storedErrors.push(errorLog);
            localStorage.setItem('login_errors', JSON.stringify(storedErrors.slice(-5))); // เก็บแค่ 5 รายการล่าสุด
        } catch (e) {
            console.error("Could not save error log:", e);
        }
    });
}

// ฟังก์ชันตั้งค่า event listeners อื่นๆ
function setupEventListeners() {
    // เพิ่ม event listeners อื่นๆ ตามต้องการ
    // เช่น การแสดง/ซ่อนรหัสผ่าน, การจำชื่อผู้ใช้, ฯลฯ

    // ตัวอย่าง: toggle การแสดงรหัสผ่าน
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function () {
            const passwordField = document.querySelector(this.getAttribute('data-target'));
            if (passwordField) {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);

                // เปลี่ยนไอคอน
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            }
        });
    });
}

// เริ่มต้นเมื่อ DOM โหลดเสร็จ
document.addEventListener('DOMContentLoaded', function () {
    console.log("DOM content loaded");
    initLoginSystem();
});