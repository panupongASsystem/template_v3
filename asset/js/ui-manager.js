/* 
 * ui-manager.js
 * ------------
 * ไฟล์สำหรับการจัดการ UI และการแสดงผลต่างๆ 
 * เช่น การแสดง Modal, แจ้งเตือน และการจัดการอินเทอร์เฟซ
 */

// ฟังก์ชันแสดง error modal ด้วย SweetAlert2
function showErrorModal(icon, title, message) {
    Swal.fire({
        icon: icon, // 'warning', 'error', 'info', 'success'
        title: title,
        text: message,
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#3085d6'
    });
}

// ฟังก์ชันแสดง blocked modal ด้วย SweetAlert2
function showBlockedModal(remainingSeconds, blockLevel = 1) {
    let timerInterval;
    let blockMessage = 'การเข้าสู่ระบบถูกระงับชั่วคราว';
    let blockDuration = '3 นาที';
    
    // ปรับข้อความตามระดับการบล็อค
    if (blockLevel == 2) {
        blockMessage = 'การเข้าสู่ระบบถูกระงับชั่วคราว (เข้าใช้งานผิดพลาดหลายครั้ง)';
        blockDuration = '10 นาที';
    }
    
    Swal.fire({
        title: blockMessage,
        html: `คุณพยายามเข้าสู่ระบบผิดพลาดหลายครั้ง<br>กรุณารอ <b id="countdown">${formatTime(remainingSeconds)}</b>`,
        icon: 'warning',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        timer: remainingSeconds * 1000,
        didOpen: () => {
            const content = Swal.getHtmlContainer();
            const $ = content.querySelector.bind(content);
            const countdownElement = $('#countdown');
            timerInterval = setInterval(() => {
                const secondsLeft = Math.ceil(Swal.getTimerLeft() / 1000);
                countdownElement.textContent = formatTime(secondsLeft);
            }, 1000);
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
            Swal.fire({
                icon: 'info',
                title: 'ข้อจำกัดหมดอายุแล้ว',
                text: 'คุณสามารถพยายามเข้าสู่ระบบได้อีกครั้ง',
                confirmButtonText: 'ตกลง'
            });
        }
    });
}

// ฟังก์ชันสำหรับแสดง modal เมื่อล็อกอินล้มเหลว
function showLoginFailedModal(message, attempts, remainingAttempts) {
    let modalContent = '';
    
    // กรณีที่ยังไม่ถูกบล็อค
    if (remainingAttempts > 0) {
        modalContent = `
            <div class="text-center mb-3">
                <i class="fa fa-exclamation-triangle text-warning" style="font-size: 48px;"></i>
            </div>
            <p>${message}</p>
            <p class="text-danger">คุณมีโอกาสลองอีก ${remainingAttempts} ครั้ง ก่อนที่ระบบจะระงับการเข้าใช้งานชั่วคราว</p>
        `;
    } else {
        // กรณีที่ถูกบล็อคแล้ว (remainingAttempts = 0)
        modalContent = `
            <div class="text-center mb-3">
                <i class="fa fa-lock text-danger" style="font-size: 48px;"></i>
            </div>
            <p class="text-danger font-weight-bold">${message}</p>
            <p>ระบบได้ระงับการเข้าใช้งานชั่วคราว เพื่อความปลอดภัยของบัญชีคุณ</p>
        `;
    }
    
    Swal.fire({
        title: 'เข้าสู่ระบบไม่สำเร็จ',
        html: modalContent,
        icon: attempts >= 3 ? 'error' : 'warning',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#3085d6',
        allowOutsideClick: false
    });
}

// ฟังก์ชันจัดรูปแบบเวลา
function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes}:${secs.toString().padStart(2, '0')}`;
}

// ฟังก์ชันแสดงตัวโหลด
function showLoading(message = 'กำลังดำเนินการ...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// ฟังก์ชันซ่อนตัวโหลด
function hideLoading() {
    Swal.close();
}

// Export ฟังก์ชันสำหรับให้ไฟล์อื่นใช้งาน
window.UIManager = {
    showErrorModal: showErrorModal,
    showBlockedModal: showBlockedModal,
    showLoginFailedModal: showLoginFailedModal,
    showLoading: showLoading,
    hideLoading: hideLoading
};