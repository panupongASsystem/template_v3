<?php 
function is_active_menu($segment_number, ...$uris) {
    $CI = &get_instance();
    $current_segment = $CI->uri->segment($segment_number);
    
    // เช็คว่า current segment ตรงกับ uri ใดๆ ที่ส่งมาหรือไม่
    foreach($uris as $uri) {
        if($current_segment === $uri) {
            return 'bg-blue-50 text-blue-600 shadow-md scale-[1.02]';
        }
    }
    return 'text-gray-600 hover:bg-white';
}
?>
<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 w-72 bg-white shadow-xl flex flex-col transition-all duration-300">
    <!-- Header -->
    <div class="mt-5 flex items-center justify-center h-24 border-b bg-gradient-to-r from-gray-50 to-white">
        <div class="flex flex-col items-center mt-2">
            <div class="p-2 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl shadow-md">
                <i class="fa-solid fa-hand-holding-dollar fa-2xl h-5 w-8 text-blue-600"></i>
            </div>
            <h1 class="text-xl font-semibold text-gray-800 mt-3 mb-4">ระบบชำระภาษี</h1>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-6 space-y-2 overflow-y-auto mt-8">

        <!-- หน้าหลัก -->
        <a href="<?php echo site_url('System_tax'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo ($this->uri->segment(1) === 'System_tax' && !$this->uri->segment(2)) ? 'bg-blue-50 text-blue-600 shadow-md' : 'text-gray-600 hover:bg-white'; ?>">
            <i class="fa fa-home mr-3"></i>
            <span>หน้าหลัก</span>
        </a>

        <!-- จัดการระบบการชำระภาษี -->
        <a href="<?php echo site_url('System_tax/main'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo is_active_menu(2, 'main'); ?>">
            <i class="fa fa-list-alt mr-3"></i>
            <span>รายการภาษีทั้งหมด</span>
        </a>

        <!-- ภาษีที่ดินและสิ่งปลูกสร้าง -->
        <a href="<?php echo site_url('System_tax/land_tax'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo is_active_menu(2, 'land_tax','lan_tax_penalty_settings'); ?>">
            <i class="fa-solid fa-warehouse mr-3"></i>
            <span>ภาษีที่ดินและสิ่งปลูกสร้าง</span>
        </a>

        <!-- ภาษีป้าย -->
        <a href="<?php echo site_url('System_tax/signboard_tax'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo is_active_menu(2, 'signboard_tax','signboard_tax_penalty_settings'); ?>">
            <i class="fas fa-sign mr-3"></i>
            <span>ภาษีป้าย</span>
        </a>

        <!-- ภาษีท้องถิ่น -->
        <a href="<?php echo site_url('System_tax/local_tax'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo is_active_menu(2, 'local_tax','local_tax_penalty_settings'); ?>">
            <i class="fas fa-landmark mr-3"></i>
            <span>ภาษีท้องถิ่น</span>
        </a>

        <!-- จัดการวันครบกำหนด -->
        <a href="<?php echo site_url('System_tax/due_dates'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo is_active_menu(2, 'due_dates'); ?>">
            <i class="fas fa-calendar-alt mr-3"></i>
            <span>วันครบกำหนดจ่ายภาษี</span>
        </a>

        <!-- ประวัติการเข้าใช้งาน -->
        <a href="<?php echo site_url('System_tax/user_logs'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] <?php echo is_active_menu(2, 'user_logs'); ?>">
            <i class="fas fa-history mr-3"></i>
            <span>ประวัติการเข้าใช้งาน</span>
        </a>


        <!-- ย้อนกลับหน้าเลือกระบบ -->
        <a href="<?php echo site_url('User/Choice'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] text-gray-600 hover:bg-white">
            <i class="fa fa-reply mr-3"></i>
            <span>ไปยังหน้าเลือกระบบ</span>
        </a>
    </nav>

    <div class="border-t p-4">
        <a href="<?php echo site_url('User/logout'); ?>"
            class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:scale-[1.02] text-red-600 hover:bg-white">
            <i class="fa fa-sign-out mr-3"></i>
            <span>ออกจากระบบ</span>
        </a>
    </div>
</div>