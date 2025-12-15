<script>
    // แก้ไขฟังก์ชัน checkLoginStatus ให้รองรับ URL ภายนอก
    function checkLoginStatus(systemType) {
        <?php if (!$this->session->userdata('mp_id')): ?>
            // ถ้ายังไม่ได้ล็อกอิน แสดง SweetAlert แจ้งเตือน
            Swal.fire({
                title: 'กรุณาเข้าสู่ระบบ',
                text: 'คุณจำเป็นต้องเข้าสู่ระบบก่อนใช้งานระบบบริการนี้',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#90A4AE',
                cancelButtonColor: '#CFD8DC',
                confirmButtonText: 'เข้าสู่ระบบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // เรียกใช้ controller ที่ตั้งค่า session และ redirect ไปหน้า login
                    window.location.href = '<?php echo site_url('User'); ?>';
                }
            });
        <?php else: ?>
            // ถ้าล็อกอินแล้ว ให้ไปยังระบบที่เลือก
            if (systemType === 'tax_system') {
                // Redirect ไปยังระบบจ่ายภาษี 
                // ในที่นี้ยังไม่มี controller จริง จึงแสดง alert ทดสอบ
                Swal.fire({
                    title: 'กำลังเข้าสู่ระบบจ่ายภาษี',
                    text: 'ระบบกำลังนำคุณไปยังระบบจ่ายภาษี',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else if (systemType === 'queue_system') {
                // Redirect ไปยังระบบจองคิวรถ
                // ในที่นี้ยังไม่มี controller จริง จึงแสดง alert ทดสอบ
                Swal.fire({
                    title: 'กำลังเข้าสู่ระบบจองคิวรถ',
                    text: 'ระบบกำลังนำคุณไปยังระบบจองคิวรถ',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
            // เพิ่มเงื่อนไขสำหรับตรวจสอบ URL ภายนอก
            else if (systemType.includes('.')) {
                // ถ้าเป็น URL ภายนอก (มีจุด . ในข้อความ) ให้เปิด URL นั้นโดยตรง
                Swal.fire({
                    title: 'กำลังนำคุณไปยังระบบภายนอก',
                    text: 'ระบบกำลังนำคุณไปยัง ' + systemType,
                    icon: 'info',
                    timer: 2000,
                    showConfirmButton: false,
                    willClose: () => {
                        // เพิ่ม protocol ถ้าไม่มี
                        let url = systemType;
                        if (!url.startsWith('http://') && !url.startsWith('https://')) {
                            url = 'https://' + url;
                        }
                        // เปิดในหน้าต่างใหม่
                        window.open(url, '_blank');
                    }
                });
            }
        <?php endif; ?>
    }

    // แสดง SweetAlert เมื่อเข้าสู่ระบบสำเร็จ
    <?php if ($this->session->flashdata('login_success')): ?>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: 'เข้าสู่ระบบสำเร็จ',
                text: 'ยินดีต้อนรับเข้าสู่ระบบบริการอิเล็กทรอนิกส์',
                icon: 'success',
                timer: 2000
				showConfirmButton: false
            });
        });
    <?php endif; ?>
	
	
	
	// JavaScript สำหรับ Notification System
let invitationShown = false;
let notificationDropdownOpen = false;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Service Systems page ready, initializing notifications...');
    
    // เริ่มต้นระบบการแจ้งเตือน
    initializeNotifications();
    
    // ปิด notification dropdown เมื่อคลิกที่อื่น
    document.addEventListener('click', function(event) {
        const bellContainer = document.querySelector('.notification-bell-container');
        if (bellContainer && !bellContainer.contains(event.target)) {
            closeNotificationDropdown();
        }
    });
    
    // ตรวจสอบสถานะ 2FA
    check2FAStatusAndShowInvitation();
});

// ฟังก์ชันเริ่มต้นระบบการแจ้งเตือน
function initializeNotifications() {
    // ตรวจสอบการแจ้งเตือนใหม่ทุก 30 วินาที
    setInterval(function() {
        refreshNotifications();
    }, 30000);
    
    console.log('Notifications system initialized');
}

// ฟังก์ชันสลับการแสดง/ซ่อน notification dropdown
function toggleNotifications() {
    if (notificationDropdownOpen) {
        closeNotificationDropdown();
    } else {
        openNotificationDropdown();
    }
}

// ฟังก์ชันเปิด notification dropdown
function openNotificationDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown) {
        dropdown.classList.add('show');
        notificationDropdownOpen = true;
        markNotificationsAsViewed();
    }
}

// ฟังก์ชันปิด notification dropdown
function closeNotificationDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown) {
        dropdown.classList.remove('show');
        notificationDropdownOpen = false;
    }
}

// ฟังก์ชันจัดการเมื่อคลิกที่การแจ้งเตือน
function handleNotificationClick(notificationId, url) {
    // ทำเครื่องหมายว่าอ่านแล้ว
    markNotificationAsRead(notificationId);
    
    // ปิด dropdown
    closeNotificationDropdown();
    
    // เปิดลิงก์
    if (url && url !== '' && url !== '#') {
        if (url.startsWith('http') || url.startsWith('//')) {
            window.open(url, '_blank');
        } else {
            window.location.href = url;
        }
    }
}

// ฟังก์ชันทำเครื่องหมายการแจ้งเตือนว่าอ่านแล้ว
function markNotificationAsRead(notificationId) {
    fetch(base_url + 'notifications/mark_as_read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // อัปเดต UI - ลบ class 'unread'
            const notificationItem = document.querySelector(`[onclick*="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('unread');
            }
            
            // อัปเดต badge count
            updateNotificationBadge();
            
            console.log('Notification marked as read:', notificationId);
        } else {
            console.error('Failed to mark notification as read:', data.message);
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function markNotificationsAsViewed() {
    console.log('Notifications viewed');
}

// ฟังก์ชันรีเฟรชการแจ้งเตือน
function refreshNotifications() {
    fetch(base_url + 'notifications/get_recent', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            updateNotificationList(data.notifications);
            updateNotificationBadge(data.unread_count);
            console.log('Notifications refreshed successfully');
        } else {
            console.error('Failed to refresh notifications:', data.message);
        }
    })
    .catch(error => {
        console.error('Error refreshing notifications:', error);
    });
}

// ฟังก์ชันอัปเดตรายการการแจ้งเตือน
function updateNotificationList(notifications) {
    const notificationList = document.querySelector('.notification-list');
    if (!notificationList) return;
    
    if (!notifications || notifications.length === 0) {
        notificationList.innerHTML = `
            <div class="notification-empty">
                <i class="bi bi-bell-slash"></i>
                <p>ไม่มีการแจ้งเตือน</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    notifications.forEach(notification => {
        html += `
            <div class="notification-item ${notification.is_read ? '' : 'unread'}" 
                 onclick="handleNotificationClick(${notification.notification_id}, '${notification.url || '#'}')">
                <div class="notification-icon">
                    <i class="${notification.icon || 'bi bi-bell'}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${escapeHtml(notification.title)}</div>
                    <div class="notification-message">${escapeHtml(notification.message)}</div>
                    <div class="notification-time">${timeago(notification.created_at)}</div>
                </div>
            </div>
        `;
    });
    
    notificationList.innerHTML = html;
}

// ฟังก์ชันอัปเดต badge การแจ้งเตือน
function updateNotificationBadge(count = null) {
    if (count === null) {
        count = document.querySelectorAll('.notification-item.unread').length;
    }
    
    const badge = document.querySelector('.notification-badge');
    const countElement = document.querySelector('.notification-count');
    
    if (count > 0) {
        if (badge) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        }
        if (countElement) {
            countElement.textContent = count + ' ใหม่';
        }
    } else {
        if (badge) {
            badge.style.display = 'none';
        }
        if (countElement) {
            countElement.textContent = '0 ใหม่';
        }
    }
}

// ฟังก์ชัน escape HTML เพื่อป้องกัน XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ฟังก์ชันแสดงเวลาแบบ timeago (ง่ายๆ)
function timeago(dateString) {
    try {
        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);
        
        if (diff < 60) return 'เมื่อสักครู่';
        if (diff < 3600) return Math.floor(diff / 60) + ' นาทีที่แล้ว';
        if (diff < 86400) return Math.floor(diff / 3600) + ' ชั่วโมงที่แล้ว';
        if (diff < 604800) return Math.floor(diff / 86400) + ' วันที่แล้ว';
        
        return date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (e) {
        return 'เมื่อสักครู่';
    }
}

// ฟังก์ชันตรวจสอบสถานะ 2FA (ถ้ามี)
function check2FAStatusAndShowInvitation() {
    // ใส่โค้ดตรวจสอบ 2FA ตรงนี้ถ้าต้องการ
    console.log('2FA status check completed');
}
</script>