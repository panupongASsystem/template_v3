<script>
/**
 * member_log_user-log.js - ไฟล์หลักสำหรับการเริ่มต้นทำงานและการจัดการทั่วไปของหน้าแสดงกิจกรรมผู้ใช้
 */

document.addEventListener('DOMContentLoaded', function() {
    /**
     * กำหนดค่าเริ่มต้นสำหรับการทำงาน
     */
    function initializeApp() {
        // กำหนด baseUrl สำหรับการเรียก API
        window.baseUrl = window.location.origin + '/';
        
        // ตรวจสอบถ้าอยู่ใน subdirectory
        const baseElement = document.querySelector('base');
        if (baseElement && baseElement.href) {
            window.baseUrl = baseElement.href;
        }
        
        console.log(`Base URL set to: ${window.baseUrl}`);
        
        // เตรียมการเริ่มต้นสำหรับแต่ละส่วน
        initializeComponents();
    }
    
    /**
     * เริ่มต้นองค์ประกอบต่างๆ ในระบบ
     */
    function initializeComponents() {
        // หมายเหตุ: แต่ละไฟล์แยกต่างหากจะจัดการส่วนของตัวเองเมื่อ DOMContentLoaded
        // แต่บางกรณีเราอาจต้องเรียกฟังก์ชันโดยตรงหรือส่งต่อข้อมูล
        
        // ตั้งค่าสำหรับแอนิเมชันบนหน้าเว็บ
        setupAppAnimations();
    }
    
    /**
     * ตั้งค่าแอนิเมชันเพิ่มเติมสำหรับหน้าแอป
     */
    function setupAppAnimations() {
        // แอนิเมชันการโหลดหน้า - ทำให้การ์ดปรากฏทีละชิ้น
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.4s ease-out';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + (index * 100)); // แสดงทีละชิ้นโดยมีระยะห่าง 100ms
        });
    }
    
    /**
     * จัดการกับข้อผิดพลาดของระบบ
     */
    function setupErrorHandling() {
        window.addEventListener('error', function(e) {
            console.error('Global error caught:', e.error || e.message);
            // สามารถเพิ่มการแจ้งเตือนหรือการบันทึกข้อผิดพลาดได้ตรงนี้
        });
    }
    
    // เริ่มต้นแอปพลิเคชัน
    initializeApp();
    setupErrorHandling();
    
    console.log('User log application initialized successfully.');
});
</script>