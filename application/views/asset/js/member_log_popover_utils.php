<script>
/**
 * member_log_popover-utils.js - สำหรับการจัดการ Popover แสดงข้อมูลอุปกรณ์ของผู้ใช้
 */

document.addEventListener('DOMContentLoaded', function() {
    // ฟังก์ชันสำหรับคัดลอกข้อความ - ทำให้เป็น global function เพื่อเรียกใช้ใน HTML
    window.copyToClipboard = function(button, event) {
        // ดึงข้อมูลที่ต้องการคัดลอก
        const textToCopy = button.getAttribute('data-copy');
        
        // วิธีการคัดลอกแบบใหม่สำหรับเบราว์เซอร์ทันสมัย
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textToCopy).then(() => {
                showCopyFeedback(button);
            }).catch(err => {
                console.error('Clipboard API failed:', err);
                fallbackCopyMethod(textToCopy, button);
            });
        } else {
            // วิธีการคัดลอกแบบเก่าสำหรับเบราว์เซอร์รุ่นเก่า
            fallbackCopyMethod(textToCopy, button);
        }
        
        // ป้องกันการปิด popover เมื่อคลิกปุ่ม
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        return false;
    };
    
    // วิธีการคัดลอกแบบเก่า
    function fallbackCopyMethod(text, button) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.left = '-9999px';
        textarea.style.top = '-9999px';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showCopyFeedback(button);
            } else {
                console.error('Fallback: execCommand copy failed');
            }
        } catch (err) {
            console.error('Fallback: copy command error:', err);
        }
        
        document.body.removeChild(textarea);
    }
    
    // แสดงผลตอบกลับการคัดลอก
    function showCopyFeedback(button) {
        const originalText = button.innerHTML;
        const originalClass = button.className;
        
        // เปลี่ยนข้อความและสีปุ่ม
        button.innerHTML = '<i class="fas fa-check me-1"></i>คัดลอกสำเร็จ';
        button.classList.remove('btn-dark');
        button.classList.add('btn-success');
        
        // เปลี่ยนกลับหลังจาก 2 วินาที
        setTimeout(function() {
            button.innerHTML = originalText;
            button.className = originalClass;
        }, 2000);
    }
    
    // ฟังก์ชันจัดการการคลิกปุ่ม Copy
    function copyButtonClickHandler(e) {
        e.preventDefault();
        e.stopPropagation();
        copyToClipboard(this, e);
    }
    
    // สร้าง Popover สำหรับทุก device-info-container
    function initializeDeviceInfoPopovers() {
        const deviceInfoContainers = document.querySelectorAll('.device-info-container');
        
        // เริ่มสร้าง popover สำหรับแต่ละ container
        deviceInfoContainers.forEach(container => {
            // ดึงข้อมูล content จาก element ที่ซ่อนไว้
            const contentId = container.getAttribute('data-content-id');
            const contentElement = document.getElementById(contentId);
            let popoverContent = '';
            
            if (contentElement) {
                // ใช้เนื้อหาจาก element ที่ซ่อนไว้
                popoverContent = contentElement.innerHTML;
            } else if (container.getAttribute('data-json')) {
                // ถ้าไม่พบ element ที่ซ่อนไว้ ให้ใช้ข้อมูลจาก data-json
                try {
                    const jsonData = atob(container.getAttribute('data-json'));
                    popoverContent = `
                        <div class="popover-content-container">
                            <pre style="max-height:300px;overflow-y:auto;white-space:pre-wrap;font-size:12px;">${jsonData}</pre>
                            <button class="btn btn-xs btn-sm btn-dark copy-btn mt-1" style="font-size:11px;padding:3px 8px;background-color:#454F5B;" 
                                    onclick="copyToClipboard(this, event)" 
                                    data-copy="${jsonData.replace(/"/g, '&quot;')}">
                                <i class="fas fa-copy me-1"></i>คัดลอก
                            </button>
                        </div>
                    `;
                } catch (e) {
                    console.error('Error decoding JSON data:', e);
                    popoverContent = '<div class="text-danger">ไม่สามารถแสดงข้อมูลได้</div>';
                }
            }
            
            // สร้าง popover
            const popover = new bootstrap.Popover(container, {
                container: 'body',
                sanitize: false,
                trigger: 'focus',
                html: true,
                content: popoverContent,
                placement: 'auto',
                boundary: 'viewport',
                fallbackPlacements: ['right', 'left', 'top', 'bottom']
            });
            
            // เพิ่ม event listener สำหรับการคลิก
            container.addEventListener('click', function() {
                // ปิด popover อื่นๆ ก่อนเปิดอันใหม่
                deviceInfoContainers.forEach(other => {
                    if (other !== container) {
                        const otherPopover = bootstrap.Popover.getInstance(other);
                        if (otherPopover) {
                            otherPopover.hide();
                        }
                    }
                });
                
                // ตั้งค่า focus เพื่อแสดง popover
                this.focus();
            });
            
            // จัดการเมื่อ popover แสดง
            container.addEventListener('shown.bs.popover', function() {
                // หา popover element ที่เพิ่งแสดง
                const popoverElement = document.querySelector('.popover');
                if (popoverElement) {
                    // เพิ่ม event listener ให้กับปุ่ม copy
                    const copyButtons = popoverElement.querySelectorAll('.copy-btn');
                    copyButtons.forEach(btn => {
                        // ลบ event listener เดิมเพื่อป้องกันการซ้ำซ้อน
                        btn.removeEventListener('click', copyButtonClickHandler);
                        // เพิ่ม event listener ใหม่
                        btn.addEventListener('click', copyButtonClickHandler);
                    });
                }
            });
        });
    }
    
    // จัดการการปิด Popover เมื่อคลิกนอกพื้นที่
    function setupPopoverCloseHandlers() {
        const deviceInfoContainers = document.querySelectorAll('.device-info-container');
        
        // จัดการคลิกนอก popover เพื่อปิด
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.popover') && !e.target.closest('.device-info-container')) {
                deviceInfoContainers.forEach(container => {
                    const popoverInstance = bootstrap.Popover.getInstance(container);
                    if (popoverInstance) {
                        popoverInstance.hide();
                    }
                });
            }
        });
        
        // ป้องกันการปิด popover เมื่อคลิกภายใน popover
        document.addEventListener('click', function(e) {
            if (e.target.closest('.popover') && !e.target.closest('.copy-btn')) {
                e.stopPropagation();
            }
        }, true);
    }
    
    // เริ่มต้นการทำงาน
    initializeDeviceInfoPopovers();
    setupPopoverCloseHandlers();
});
</script>