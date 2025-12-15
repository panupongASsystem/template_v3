/* 
 * form-handler.js
 * --------------
 * ไฟล์สำหรับการจัดการฟอร์มล็อกอิน การตรวจสอบข้อมูล 
 * และการส่งข้อมูลไปยังเซิร์ฟเวอร์
 */

// ฟังก์ชันเพิ่ม hidden field ในฟอร์ม
function appendHiddenField(form, name, value) {
    // Debug: ตรวจสอบค่าของ value
    console.log(`Appending field ${name}, value type: ${typeof value}, value:`, value);
    
    // เช็คว่ามี field นี้อยู่แล้วหรือไม่
    if (!form.querySelector(`input[name="${name}"]`)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value || '';
        form.appendChild(input);
        
        // ตรวจสอบว่า value เป็น string หรือไม่
        let displayValue = '';
        if (typeof value === 'string') {
            displayValue = value.length > 20 ? value.substring(0, 20) + '...' : value;
        } else if (value !== null && value !== undefined) {
            // ถ้าไม่ใช่ string ให้แปลงเป็น string ก่อน
            displayValue = String(value);
            displayValue = displayValue.length > 20 ? displayValue.substring(0, 20) + '...' : displayValue;
        }
        
        console.log(`Added ${name}: ${displayValue}`);
    }
}

// เพิ่มฟังก์ชันสำหรับเพิ่มข้อมูลอุปกรณ์ทั้งหมดเข้าไปในฟอร์ม
async function addAllDeviceInfo(formId = 'reCAPTCHA3') {
    const form = document.getElementById(formId);
    if (!form) {
        console.error(`Form '${formId}' not found`);
        return;
    }
    
    try {
        // ดึงข้อมูลอุปกรณ์จาก Fingerprint API
        let deviceData;
        try {
            deviceData = await window.FingerprintJS.collectDeviceInfo();
        } catch (e) {
            console.error("Error collecting device info:", e);
            deviceData = {
                basicData: { fingerprint: 'error-collecting-data' },
                fullInfo: { error: 'Failed to collect device data' }
            };
        }
        
        // ตรวจสอบว่าข้อมูลมีโครงสร้างที่ถูกต้องหรือไม่
        const basicData = deviceData && deviceData.basicData ? deviceData.basicData : {};
        const fullInfo = deviceData && deviceData.fullInfo ? deviceData.fullInfo : {};
        
        // เพิ่มข้อมูลพื้นฐานลงในฟอร์ม - ใช้ try/catch แยกแต่ละรายการ
        for (const key in basicData) {
            try {
                const value = basicData[key];
                const stringValue = typeof value === 'string' ? value : String(value);
                appendHiddenField(form, key, stringValue);
            } catch (err) {
                console.error(`Error adding field ${key}:`, err);
            }
        }
        
        // เพิ่ม device_fingerprint ลงในฟอร์ม
        try {
            const deviceInfoStr = JSON.stringify(fullInfo);
            appendHiddenField(form, 'device_fingerprint', deviceInfoStr);
        } catch (err) {
            console.error('Error adding device_fingerprint:', err);
            appendHiddenField(form, 'device_fingerprint', '{}');
        }
        
        return true;
    } catch (error) {
        console.error('Error adding device info:', error);
        return false;
    }
}

// ฟังก์ชันส่งฟอร์มด้วย AJAX
function submitLoginForm(formId = 'reCAPTCHA3', apiUrl) {
    console.log("Submitting login form via AJAX");
    
    const form = document.getElementById(formId);
    if (!form) {
        console.error(`Form '${formId}' not found`);
        return;
    }
    
    // เก็บข้อมูลจากฟอร์ม
    const formData = new FormData(form);
    
    // ส่งข้อมูลด้วย fetch API
    fetch(apiUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log("Login response:", data);
        
        if (data.status === 'success') {
            // ล็อกอินสำเร็จ ให้ redirect ไปยังหน้าที่กำหนด
            window.location.href = data.redirect;
        } 
        else if (data.status === 'blocked') {
            // ถูกบล็อค ให้แสดง modal
            UIManager.showBlockedModal(data.remaining_time);
        }
        else {
            // แสดงข้อความผิดพลาด
            if (data.attempts && data.remaining_attempts > 0) {
                UIManager.showErrorModal(
                    'warning',
                    'ล็อกอินไม่สำเร็จ',
                    `${data.message} (คุณมีโอกาสล็อกอินอีก ${data.remaining_attempts} ครั้ง)`
                );
            } else {
                UIManager.showErrorModal(
                    'error',
                    'ล็อกอินไม่สำเร็จ',
                    data.message
                );
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        UIManager.showErrorModal(
            'error',
            'เกิดข้อผิดพลาด',
            'ไม่สามารถติดต่อกับเซิร์ฟเวอร์ได้'
        );
    });
}

// ฟังก์ชั่นตรวจสอบและส่งฟอร์มพร้อม fingerprint
async function submitLoginWithFingerprint(formId = 'reCAPTCHA3', apiUrl) {
    try {
        // เช็คว่ามี form อยู่หรือไม่
        const form = document.getElementById(formId);
        if (!form) {
            console.error(`Form '${formId}' not found`);
            return;
        }
        
        let fingerprintInput = form.querySelector('input[name="fingerprint"]');
        
        // ถ้ายังไม่มี ให้สร้างใหม่
        if (!fingerprintInput) {
            console.log("Creating new fingerprint");
            const fingerprint = await window.FingerprintJS.generate();
            
            fingerprintInput = document.createElement('input');
            fingerprintInput.type = 'hidden';
            fingerprintInput.name = 'fingerprint';
            fingerprintInput.value = fingerprint;
            
            form.appendChild(fingerprintInput);
            console.log("Fingerprint added to form:", fingerprint);
        } else {
            console.log("Fingerprint already exists");
        }
        
        // เพิ่มข้อมูลอุปกรณ์
        await addAllDeviceInfo(formId);
        
        // ส่งข้อมูลด้วย AJAX
        submitLoginForm(formId, apiUrl);
    } catch (e) {
        console.error("Error in submitLoginWithFingerprint:", e);
        // ส่งฟอร์มตามปกติถ้ามีข้อผิดพลาด
        const form = document.getElementById(formId);
        if (form) form.submit();
    }
}

// ฟังก์ชั่นสำหรับเพิ่ม reCAPTCHA และส่งฟอร์ม
function setupReCaptchaAndSubmit(formId = 'reCAPTCHA3', reCaptchaSiteKey, apiUrl) {
    const form = document.getElementById(formId);
    if (!form) {
        console.error(`Form '${formId}' not found`);
        return;
    }
    
    // เพิ่ม Event Listener สำหรับฟอร์ม
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // ตรวจสอบว่า reCAPTCHA พร้อมใช้งานหรือไม่
        if (typeof grecaptcha === 'undefined') {
            console.error('reCAPTCHA not loaded');
            return;
        }
        
        grecaptcha.ready(function() {
            grecaptcha.execute(reCaptchaSiteKey, { action: 'login' })
            .then(function(token) {
                // เพิ่ม token ลงในฟอร์ม
                appendHiddenField(form, 'g-recaptcha-response', token);
                
                // ส่งฟอร์ม
                form.submit();
            });
        });
    });
}

// Export ฟังก์ชันสำหรับให้ไฟล์อื่นใช้งาน
window.FormHandler = {
    appendHiddenField: appendHiddenField,
    addAllDeviceInfo: addAllDeviceInfo,
    submitLoginForm: submitLoginForm,
    submitLoginWithFingerprint: submitLoginWithFingerprint,
    setupReCaptchaAndSubmit: setupReCaptchaAndSubmit
};