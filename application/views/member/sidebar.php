<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 w-72 bg-white shadow-xl flex flex-col transition-all duration-300">
    <!-- Header -->
    <div class="flex items-center justify-center h-24 border-b bg-gradient-to-r from-gray-50 to-white">
        <div class="flex flex-col items-center mt-2">
            <div class="p-2 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h1 class="text-xl font-semibold text-gray-800 mt-3">ระบบจัดการสมาชิก</h1>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-6 space-y-2 overflow-y-auto mt-8">
        <?php if (has_member_management_permission()): ?>
        <!-- หน้าหลัก -->
        <a href="<?php echo site_url('System_member'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo ($this->uri->segment(1) === 'System_member' && !$this->uri->segment(2)) ? 'bg-blue-50 text-blue-600 shadow-md' : 'text-gray-600 hover:bg-white'; ?>">
            <i class="fa fa-home mr-3"></i>
            <span>หน้าหลัก</span>
        </a>
        <?php endif; ?>

        <?php if ($this->session->userdata('m_system') == 'system_admin'): ?>
        <!-- จัดการระบบหลัก -->
        <a href="<?php echo site_url('System_member/site_map'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo is_active_menu(2, 'site_map'); ?>">
            <i class="fas fa-sitemap mr-3"></i>
            <span>จัดการระบบหลัก</span>
        </a>
        <?php endif; ?>

        <?php if (has_member_management_permission()): ?>
        <!-- จัดการสมาชิก (Parent menu) -->
        <div class="menu-item">
            <a href="<?php echo site_url('System_member/member_web'); ?>" 
               class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo (in_array($this->uri->segment(2), ['member_web', 'member_web_external'])) ? 'bg-blue-50 text-blue-600 shadow-md' : 'text-gray-600 hover:bg-white'; ?>">
                <div class="flex items-center">
                    <i class="fas fa-users mr-3"></i>
                    <span>จัดการสมาชิก</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform" id="memberMenuArrow" onclick="toggleSubMenu('memberSubMenu', 'memberMenuArrow', event)"></i>
            </a>
            
            <!-- Sub menu -->
            <div id="memberSubMenu" class="pl-6 mt-1 space-y-1 overflow-hidden transition-all duration-300" style="max-height: 0;">
                <!-- จัดการสมาชิกภายใน -->
                <a href="<?php echo site_url('System_member/member_web'); ?>"
                   class="flex items-center px-4 py-2 rounded-xl transition-all duration-200 hover:bg-gray-50 <?php echo $this->uri->segment(2) === 'member_web' ? 'bg-blue-50 text-blue-600' : 'text-gray-600'; ?>">
                    <i class="fas fa-user-friends mr-3"></i>
                    <span>จัดการสมาชิกภายใน</span>
                </a>
                
                <!-- จัดการสมาชิกภายนอก -->
                <a href="<?php echo site_url('System_member/member_web_external'); ?>"
                   class="flex items-center px-4 py-2 rounded-xl transition-all duration-200 hover:bg-gray-50 <?php echo $this->uri->segment(2) === 'member_web_external' ? 'bg-blue-50 text-blue-600' : 'text-gray-600'; ?>">
                    <i class="fas fa-user mr-3"></i>
                    <span>จัดการสมาชิกภายนอก</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (has_website_management_permission()): ?>
        <!-- จัดการเว็บไซต์ -->
        <a href="<?php echo site_url('System_member/website_management'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo is_active_menu(2, 'website_management'); ?>">
            <i class="fas fa-globe mr-3"></i>
            <span>จัดการเว็บไซต์</span>
        </a>
        <?php endif; ?>
		
        <!-- แสดงเมนูตามโมดูลที่เปิดใช้งาน -->
        <?php 
        $CI = &get_instance();
        // ดึงโมดูลที่เปิดใช้งานและไม่ใช่โมดูล ID 1 (จัดการสมาชิก) และ ID 2 (จัดการเว็บไซต์)
        $active_modules = $CI->db->where('status', 1)
                                ->where_not_in('id', [1, 2, 11]) // ไม่แสดงโมดูล ID 1 และ 2 ,11
                                ->order_by('display_order', 'ASC')
                                ->get('tbl_member_modules')
                                ->result();

        foreach ($active_modules as $module):
            // ตรวจสอบสิทธิ์การเข้าถึงโมดูล
            if (has_module_permission($module->id)):
        ?>
            <a href="<?php echo site_url('System_member/module/' . $module->code); ?>"
                class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo is_active_menu(3, $module->code); ?>">
                <i class="<?php echo !empty($module->icon) ? $module->icon : 'fa fa-circle'; ?> mr-3"></i>
                <span><?php echo $module->name; ?></span>
            </a>
        <?php 
            endif;
        endforeach;
        ?>
		
        <?php if (in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])): ?>
        

        <!-- ✅ Google Drive (Parent menu with submenu) -->
        <div class="menu-item">
            <a href="<?php echo site_url('google_drive_system/dashboard'); ?>" 
               class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php 
               // เช็ค active state สำหรับ Google Drive
               $is_google_drive_active = (
                   ($this->uri->segment(1) === 'Google_drive' && in_array($this->uri->segment(2), ['manage', 'settings'])) ||
                   ($this->uri->segment(1) === 'google_drive_system' && in_array($this->uri->segment(2), ['dashboard', 'files', 'token_manager', 'setup']))
               );
               echo $is_google_drive_active ? 'bg-green-50 text-green-600 shadow-md' : 'text-gray-600 hover:bg-white'; 
               ?>">
                <div class="flex items-center">
                    <i class="fab fa-google-drive mr-3 text-green-500"></i>
                    <span>Google Drive</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform" id="googleDriveMenuArrow" onclick="toggleSubMenu('googleDriveSubMenu', 'googleDriveMenuArrow', event)"></i>
            </a>
            
            <!-- Google Drive Sub menu -->
            <div id="googleDriveSubMenu" class="pl-6 mt-1 space-y-1 overflow-hidden transition-all duration-300" style="max-height: 0;">
                <!-- แดชบอร์ด -->
                <a href="<?php echo site_url('google_drive_system/dashboard'); ?>"
                   class="flex items-center px-4 py-2 rounded-xl transition-all duration-200 hover:bg-gray-50 <?php echo ($this->uri->segment(1) === 'google_drive_system' && $this->uri->segment(2) === 'dashboard') ? 'bg-green-50 text-green-600' : 'text-gray-600'; ?>">
                    <i class="fas fa-tachometer-alt mr-3 text-green-500"></i>
                    <span>แดชบอร์ด</span>
                </a>
                
                <!-- จัดการไฟล์ -->
                <a href="<?php echo site_url('google_drive_system/files'); ?>"
                   class="flex items-center px-4 py-2 rounded-xl transition-all duration-200 hover:bg-gray-50 <?php echo ($this->uri->segment(1) === 'google_drive_system' && $this->uri->segment(2) === 'files') ? 'bg-green-50 text-green-600' : 'text-gray-600'; ?>">
                    <i class="fas fa-folder mr-3 text-green-500"></i>
                    <span>จัดการสิทธิ์ใช้งาน</span>
                </a>
				
				<!-- Google Drive (Legacy - เก็บไว้เพื่อ backward compatibility) 
                <a href="<?php echo site_url('Google_drive/manage'); ?>"
                   class="flex items-center px-4 py-2 rounded-xl transition-all duration-200 hover:bg-gray-50 <?php echo ($this->uri->segment(1) === 'Google_drive' && $this->uri->segment(2) === 'manage') ? 'bg-green-50 text-green-600' : 'text-gray-600'; ?>">
                    <i class="fas fa-cog mr-3 text-green-500"></i>
                    <span>จัดการสมาชิก</span>
                </a> -->
				
                
                <!-- 🆕 การตั้งค่า Google OAuth (เฉพาะ system_admin) -->
                <?php if ($this->session->userdata('m_system') === 'system_admin'): ?>
                <a href="<?php echo site_url('google_drive/settings'); ?>"
                   class="flex items-center px-4 py-2 rounded-xl transition-all duration-200 hover:bg-gray-50 <?php echo ($this->uri->segment(1) === 'google_drive' && $this->uri->segment(2) === 'settings') ? 'bg-green-50 text-green-600' : 'text-gray-600'; ?>">
                    <i class="fas fa-cogs mr-3 text-blue-500"></i>
                    <span>ตั้งค่า Google OAuth</span>
                </a>
                <?php endif; ?>
                
                <!-- 🆕 ตั้งค่าเชื่อมต่อ Google Account (เฉพาะ system_admin) -->
                <?php if ($this->session->userdata('m_system') === 'system_admin'): ?>
                <a href="<?php echo site_url('google_drive_system/token_manager'); ?>"
                   class="flex items-center px-4 py-2 rounded-xl transition-all duration-200 hover:bg-gray-50 <?php echo ($this->uri->segment(1) === 'google_drive_system' && $this->uri->segment(2) === 'token_manager') ? 'bg-green-50 text-green-600' : 'text-gray-600'; ?>">
                    <i class="fas fa-link mr-3 text-purple-500"></i>
                    <span>ตั้งค่าเชื่อมต่อ Token </span>
                </a>
                <?php endif; ?>
                
                <!-- 🆕 ตั้งค่า System Storage (เฉพาะ system_admin) -->
                <?php if ($this->session->userdata('m_system') === 'system_admin'): ?>
                <a href="<?php echo site_url('google_drive_system/setup'); ?>"
                   class="flex items-center px-4 py-2 rounded-xl transition-all duration-200 hover:bg-gray-50 <?php echo ($this->uri->segment(1) === 'google_drive_system' && $this->uri->segment(2) === 'setup') ? 'bg-green-50 text-green-600' : 'text-gray-600'; ?>">
                    <i class="fas fa-cog mr-3 text-orange-500"></i>
                    <span>จัดการสมาชิก ตั้งค่า</span>
                </a>
                <?php endif; ?>
                
                
            </div>
        </div>
		
		<!-- ประวัติการลบ -->
        <a href="<?php echo site_url('System_member/delete_log'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo is_active_menu(2, 'delete_log'); ?>">
            <i class="fa fa-trash mr-3"></i>
            <span>ประวัติการลบสมาชิก</span>
        </a>
        <?php endif; ?>

        <!-- ย้อนกลับหน้าเลือกระบบ -->
        <a href="<?php echo site_url('User/Choice'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] text-gray-600 hover:bg-white">
            <i class="fa fa-reply mr-3"></i>
            <span>กลับหน้าสมาร์ทออฟฟิต</span>
        </a>
    </nav>

    <!-- Logout -->
    <div class="border-t p-4">
        <a href="<?php echo site_url('User/logout'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] text-red-600 hover:bg-white">
            <i class="fa fa-sign-out mr-3"></i>
            <span>ออกจากระบบ</span>
        </a>
    </div>
</div>

<style>
/* CSS สำหรับ animation ของ submenu */
#memberSubMenu, #googleDriveSubMenu {
    max-height: 0;
    transition: max-height 0.3s ease-in-out;
}
</style>

<!-- JavaScript สำหรับ toggle submenu -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ตรวจสอบว่าเป็นหน้าจัดการสมาชิกหรือไม่
    const currentPath = window.location.pathname;
    
    // Auto-expand Member submenu
    if (currentPath.includes('System_member/member_web') || 
        currentPath.includes('System_member/member_web_external')) {
        
        expandSubMenu('memberSubMenu', 'memberMenuArrow');
    }
    
    // ✅ Auto-expand Google Drive submenu (อัปเดตเพื่อรองรับเมนูใหม่)
    if (currentPath.includes('google_drive_system/') || 
        currentPath.includes('Google_drive/manage') ||
        currentPath.includes('google_drive/settings')) {
        
        expandSubMenu('googleDriveSubMenu', 'googleDriveMenuArrow');
    }
});

// ✅ ฟังก์ชันสำหรับขยาย submenu
function expandSubMenu(subMenuId, arrowId) {
    const subMenu = document.getElementById(subMenuId);
    const arrow = document.getElementById(arrowId);
    
    if (subMenu) {
        subMenu.style.maxHeight = '250px'; // เพิ่มความสูงให้รองรับ submenu ที่มี 6 รายการ
        if (arrow) {
            arrow.classList.add('transform', 'rotate-180');
        }
    }
}

// ✅ ฟังก์ชัน toggle submenu (แก้ไขให้รับ parameter arrow ID)
function toggleSubMenu(subMenuId, arrowId, event) {
    // ป้องกันการนำทางไปยังหน้าเมื่อคลิกที่ไอคอนลูกศร
    event.preventDefault();
    event.stopPropagation();
    
    const subMenu = document.getElementById(subMenuId);
    const arrow = document.getElementById(arrowId);
    
    if (subMenu.style.maxHeight === '0px' || subMenu.style.maxHeight === '') {
        subMenu.style.maxHeight = '250px'; // เพิ่มความสูงให้รองรับ submenu ที่มี 6 รายการ
        if (arrow) {
            arrow.classList.add('transform', 'rotate-180');
        }
    } else {
        subMenu.style.maxHeight = '0px';
        if (arrow) {
            arrow.classList.remove('transform', 'rotate-180');
        }
    }
}
</script>