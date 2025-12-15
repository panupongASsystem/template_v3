<script>
/**
 * member_log_search-filter.js - สำหรับการค้นหาและกรองข้อมูลในตารางกิจกรรมผู้ใช้
 */

document.addEventListener('DOMContentLoaded', function() {
    // เพิ่มฟังก์ชันอัปเดตการแสดงผลสำหรับทุกการค้นหา
    const updateSearchDisplay = function() {
        // ตรวจสอบการค้นหาด้วยคำค้น
        const searchInput = document.querySelector('input[name="search"]');
        const activityTypeSelect = document.querySelector('select[name="activity_type"]');
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        
        // ตรวจสอบว่ามีการใช้ตัวกรองใดบ้าง
        const searchValue = searchInput ? searchInput.value : '';
        const activityType = activityTypeSelect ? activityTypeSelect.value : '';
        const startDate = startDateInput ? startDateInput.value : '';
        const endDate = endDateInput ? endDateInput.value : '';
        
        // สร้างพื้นที่สำหรับแสดงข้อความการค้นหา
        let searchInfoContainer = document.getElementById('searchInfoContainer');
        if (!searchInfoContainer) {
            searchInfoContainer = document.createElement('div');
            searchInfoContainer.id = 'searchInfoContainer';
            searchInfoContainer.style.marginBottom = '1.5rem';
            
            const tableContainer = document.querySelector('.card:last-of-type');
            if (tableContainer && tableContainer.parentNode) {
                tableContainer.parentNode.insertBefore(searchInfoContainer, tableContainer);
            }
        }
        
        // เคลียร์ข้อความเดิม
        searchInfoContainer.innerHTML = '';
        
        // แสดงข้อความการค้นหาตามเงื่อนไข
        if (searchValue) {
            const searchInfo = document.createElement('div');
            searchInfo.className = 'alert alert-primary d-flex align-items-center mb-2';
            searchInfo.innerHTML = `
                <i class="fas fa-search me-2"></i>
                <div>กำลังค้นหาด้วยคำว่า <strong>"${searchValue}"</strong></div>
            `;
            searchInfoContainer.appendChild(searchInfo);
        }
        
        if (activityType) {
            let activityTypeText = '';
            switch(activityType) {
                case 'login':
                    activityTypeText = 'เข้าสู่ระบบ';
                    break;
                case 'logout':
                    activityTypeText = 'ออกจากระบบ';
                    break;
                case 'failed':
                    activityTypeText = 'เข้าสู่ระบบล้มเหลว';
                    break;
                default:
                    activityTypeText = activityType;
            }
            
            const activityInfo = document.createElement('div');
            activityInfo.className = 'alert alert-success d-flex align-items-center mb-2';
            activityInfo.innerHTML = `
                <i class="fas fa-filter me-2"></i>
                <div>กำลังกรองข้อมูลประเภท <strong>${activityTypeText}</strong></div>
            `;
            searchInfoContainer.appendChild(activityInfo);
        }
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            
            const dateRangeInfo = document.createElement('div');
            dateRangeInfo.className = 'alert alert-info d-flex align-items-center mb-2';
            dateRangeInfo.innerHTML = `
                <i class="fas fa-calendar-alt me-2"></i>
                <div>กำลังแสดงข้อมูลระหว่างวันที่ <strong>${start.toLocaleDateString('th-TH', options)}</strong> ถึง <strong>${end.toLocaleDateString('th-TH', options)}</strong></div>
            `;
            searchInfoContainer.appendChild(dateRangeInfo);
        }
    };
    
    // ตั้งค่าตัวตรวจสอบวันที่และเวลา
    function setupDateConstraints() {
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        
        if (startDateInput && endDateInput) {
            // ตั้งค่าวันที่สูงสุดเป็นวันปัจจุบัน
            const today = new Date().toISOString().split('T')[0];
            startDateInput.max = today;
            endDateInput.max = today;
            
            startDateInput.addEventListener('change', function() {
                if (endDateInput.value && this.value > endDateInput.value) {
                    endDateInput.value = this.value;
                }
                endDateInput.min = this.value;
                updateSearchDisplay();
            });
            
            endDateInput.addEventListener('change', function() {
                if (startDateInput.value && this.value < startDateInput.value) {
                    startDateInput.value = this.value;
                }
                startDateInput.max = this.value;
                updateSearchDisplay();
            });
            
            // ตั้งค่าเริ่มต้น min value
            if (startDateInput.value) {
                endDateInput.min = startDateInput.value;
            }
        }
    }
    
    // ตั้งค่า event listeners สำหรับฟอร์มค้นหา
    function setupSearchFormListeners() {
        const searchInput = document.querySelector('input[name="search"]');
        const activityTypeSelect = document.querySelector('select[name="activity_type"]');
        
        if (searchInput) {
            searchInput.addEventListener('input', updateSearchDisplay);
        }
        
        if (activityTypeSelect) {
            activityTypeSelect.addEventListener('change', updateSearchDisplay);
        }
        
        // ปุ่มรีเซ็ตการค้นหา
        const resetButton = document.querySelector('.btn-outline-secondary[href]');
        if (resetButton) {
            resetButton.addEventListener('click', function(e) {
                e.preventDefault();
                const form = document.querySelector('form');
                if (form) {
                    // รีเซ็ตฟอร์ม
                    form.reset();
                    // ส่งฟอร์ม
                    form.submit();
                } else {
                    // ถ้าไม่พบฟอร์ม ให้ redirect ตาม href
                    window.location.href = this.getAttribute('href');
                }
            });
        }
    }
    
    // เริ่มต้นการทำงาน
    setupDateConstraints();
    setupSearchFormListeners();
    updateSearchDisplay();
});
</script>