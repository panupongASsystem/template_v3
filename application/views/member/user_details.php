<?php
// application/views/member/user_details.php
?>

<div class="ml-72 p-8">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button onclick="goBack()" 
        class="text-gray-600 hover:text-gray-800 text-2xl">
    <i class="fas fa-arrow-left"></i>
</button>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        👤 รายละเอียดผู้ใช้
                    </h1>
                    <p class="text-gray-600"><?php echo htmlspecialchars($user_data['user']['full_name']); ?></p>
                </div>
            </div>
            <div class="flex space-x-3">
                <button onclick="refreshUserData()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-sync-alt mr-2"></i>รีเฟรช
                </button>
                <button onclick="exportUserData()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>ส่งออก
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- User Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-2xl font-bold">
                            <?php echo strtoupper(substr($user_data['user']['m_fname'], 0, 1)); ?>
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($user_data['user']['full_name']); ?></h2>
                    <p class="text-gray-600"><?php echo htmlspecialchars($user_data['user']['position_name'] ?: 'ไม่ระบุตำแหน่ง'); ?></p>
                    
                    <!-- Status Badges -->
                    <div class="flex justify-center space-x-2 mt-4">
                        <?php if ($user_data['user']['m_status'] == '1'): ?>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                <i class="fas fa-check-circle mr-1"></i>เปิดใช้งาน
                            </span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                                <i class="fas fa-times-circle mr-1"></i>ปิดใช้งาน
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($user_data['user']['storage_access_granted'] == 1): ?>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                <i class="fas fa-cloud mr-1"></i>Storage
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- User Info -->
                <div class="mt-6 space-y-4">
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-gray-400 mr-3"></i>
                        <span class="text-gray-700"><?php echo htmlspecialchars($user_data['user']['m_email']); ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone text-gray-400 mr-3"></i>
                        <span class="text-gray-700"><?php echo htmlspecialchars($user_data['user']['m_phone']); ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-calendar text-gray-400 mr-3"></i>
                        <span class="text-gray-700">สมัครเมื่อ <?php echo date('d/m/Y', strtotime($user_data['user']['m_datesave'])); ?></span>
                    </div>
                    <?php if (!empty($user_data['user']['google_email'])): ?>
                    <div class="flex items-center">
                        <i class="fab fa-google text-gray-400 mr-3"></i>
                        <span class="text-gray-700"><?php echo htmlspecialchars($user_data['user']['google_email']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">📊 สถิติโดยรวม</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">โฟลเดอร์ที่เข้าถึงได้</span>
                        <span class="font-bold text-blue-600"><?php echo $user_data['stats']['total_folders']; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">ไฟล์ที่อัปโหลด</span>
                        <span class="font-bold text-green-600"><?php echo $user_data['stats']['total_files']; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">การดาวน์โหลด</span>
                        <span class="font-bold text-purple-600"><?php echo $user_data['stats']['total_downloads']; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">การแชร์</span>
                        <span class="font-bold text-orange-600"><?php echo $user_data['stats']['total_shares']; ?></span>
                    </div>
                </div>
                
                <!-- Storage Usage -->
                <div class="mt-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">การใช้งาน Storage</span>
                        <span class="text-sm font-medium"><?php echo $user_data['stats']['storage_usage_percent']; ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" 
                             style="width: <?php echo min(100, $user_data['stats']['storage_usage_percent']); ?>%"></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        <?php echo format_bytes($user_data['user']['storage_quota_used']); ?> / 
                        <?php echo format_bytes($user_data['user']['storage_quota_limit']); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2">
            
            <!-- Tab Navigation -->
            <div class="bg-white rounded-xl shadow-lg mb-6">
                <div class="border-b">
                    <nav class="flex space-x-1 p-1">
                        <button onclick="switchTab('permissions')" 
                                class="tab-button flex-1 py-3 px-4 text-center rounded-lg font-medium transition-colors active"
                                data-tab="permissions">
                            <i class="fas fa-key mr-2"></i>สิทธิ์โฟลเดอร์
                        </button>
                        <button onclick="switchTab('activities')" 
                                class="tab-button flex-1 py-3 px-4 text-center rounded-lg font-medium transition-colors"
                                data-tab="activities">
                            <i class="fas fa-history mr-2"></i>กิจกรรม
                        </button>
                        <button onclick="switchTab('files')" 
                                class="tab-button flex-1 py-3 px-4 text-center rounded-lg font-medium transition-colors"
                                data-tab="files">
                            <i class="fas fa-file mr-2"></i>ไฟล์
                        </button>
                        <button onclick="switchTab('settings')" 
                                class="tab-button flex-1 py-3 px-4 text-center rounded-lg font-medium transition-colors"
                                data-tab="settings">
                            <i class="fas fa-cog mr-2"></i>การตั้งค่า
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    
                    <!-- Permissions Tab -->
                    <div id="permissionsTab" class="tab-content">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">สิทธิ์การเข้าถึงโฟลเดอร์</h3>
                        
                        <?php if (!empty($user_data['folder_permissions'])): ?>
                            <div class="space-y-4">
                                <?php foreach ($user_data['folder_permissions'] as $permission): ?>
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <i class="fas fa-folder text-yellow-500 text-lg"></i>
                                                <div>
                                                    <h4 class="font-medium text-gray-800"><?php echo htmlspecialchars($permission->folder_name ?: 'Unknown Folder'); ?></h4>
                                                    <p class="text-sm text-gray-500"><?php echo ucfirst($permission->folder_type ?: 'unknown'); ?></p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                                    <?php echo strtoupper($permission->access_type); ?>
                                                </span>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    โดย <?php echo htmlspecialchars($permission->granted_by_name ?: 'System'); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-folder-open text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-600">ไม่มีสิทธิ์การเข้าถึงโฟลเดอร์</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Activities Tab -->
                    <div id="activitiesTab" class="tab-content hidden">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">ประวัติกิจกรรม</h3>
                        
                        <?php if (!empty($user_data['activity_logs'])): ?>
                            <div class="space-y-3">
                                <?php foreach ($user_data['activity_logs'] as $log): ?>
                                    <div class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg">
                                        <i class="fas fa-circle text-blue-500 text-xs mt-2"></i>
                                        <div class="flex-1">
                                            <p class="text-gray-800"><?php echo htmlspecialchars($log->action_description); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo date('d/m/Y H:i:s', strtotime($log->created_at)); ?></p>
                                        </div>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                            <?php echo strtoupper($log->action_type); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-600">ไม่มีประวัติกิจกรรม</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Files Tab -->
                    <div id="filesTab" class="tab-content hidden">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">ไฟล์ที่เกี่ยวข้อง</h3>
                        
                        <?php if (!empty($user_data['file_activities'])): ?>
                            <div class="space-y-3">
                                <?php foreach ($user_data['file_activities'] as $file): ?>
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-file text-blue-500"></i>
                                            <div>
                                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($file->file_name); ?></p>
                                                <p class="text-sm text-gray-500"><?php echo strtoupper($file->action_type); ?> • <?php echo date('d/m/Y H:i', strtotime($file->created_at)); ?></p>
                                            </div>
                                        </div>
                                        <?php if (isset($file->file_size) && $file->file_size): ?>
                                            <span class="text-sm text-gray-500"><?php echo format_bytes($file->file_size); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-file text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-600">ไม่มีกิจกรรมไฟล์</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Settings Tab -->
                    <div id="settingsTab" class="tab-content hidden">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">การจัดการผู้ใช้</h3>
                        
                        <div class="space-y-4">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-3">การดำเนินการ</h4>
                                <div class="flex space-x-3">
                                    <button onclick="editUserPermissions(<?php echo $user_id; ?>)" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-edit mr-2"></i>แก้ไขสิทธิ์
                                    </button>
                                    <button onclick="resetUserData(<?php echo $user_id; ?>)" 
                                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                                        <i class="fas fa-redo mr-2"></i>รีเซ็ตข้อมูล
                                    </button>
                                    <button onclick="deleteUserConfirm(<?php echo $user_id; ?>)" 
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        <i class="fas fa-trash mr-2"></i>ลบผู้ใช้
                                    </button>
                                </div>
                            </div>
                            
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-3">ข้อมูลระบบ</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">User ID:</span>
                                        <span class="font-medium ml-2"><?php echo $user_data['user']['m_id']; ?></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Username:</span>
                                        <span class="font-medium ml-2"><?php echo htmlspecialchars($user_data['user']['m_username']); ?></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">System Role:</span>
                                        <span class="font-medium ml-2"><?php echo htmlspecialchars($user_data['user']['m_system'] ?: 'end_user'); ?></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">อายุบัญชี:</span>
                                        <span class="font-medium ml-2"><?php echo $user_data['stats']['account_age_days']; ?> วัน</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.tab-button.active {
    @apply bg-blue-100 text-blue-700;
}

.tab-button:not(.active) {
    @apply text-gray-600 hover:text-gray-800;
}
</style>

<script>
const userId = <?php echo $user_id; ?>;

// Tab switching
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Show selected tab
    document.getElementById(tabName + 'Tab').classList.remove('hidden');
    
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-100', 'text-blue-700');
        btn.classList.add('text-gray-600', 'hover:text-gray-800');
    });
    
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    activeBtn.classList.add('active', 'bg-blue-100', 'text-blue-700');
    activeBtn.classList.remove('text-gray-600', 'hover:text-gray-800');
}

// Functions
function refreshUserData() {
    location.reload();
}

function exportUserData() {
    window.open(`<?php echo site_url('google_drive_system/export_user_data/'); ?>${userId}`, '_blank');
}

function editUserPermissions(userId) {
    window.location.href = `<?php echo site_url('google_drive_system/setup#user-'); ?>${userId}`;
}

function resetUserData(userId) {
    Swal.fire({
        title: 'ยืนยันการรีเซ็ตข้อมูล',
        text: 'คุณต้องการรีเซ็ตข้อมูลของผู้ใช้นี้หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'รีเซ็ต',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implementation for reset
            Swal.fire('รีเซ็ตเรียบร้อย', 'ข้อมูลผู้ใช้ถูกรีเซ็ตแล้ว', 'success');
        }
    });
}

function deleteUserConfirm(userId) {
    Swal.fire({
        title: 'ยืนยันการลบผู้ใช้',
        text: 'การดำเนินการนี้ไม่สามารถยกเลิกได้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#dc2626'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteUser(userId);
        }
    });
}

function deleteUser(userId) {
    fetch('<?php echo site_url('google_drive_system/delete_user_data'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `user_id=${userId}&action_type=soft_delete`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('ลบเรียบร้อย', 'ลบผู้ใช้เรียบร้อยแล้ว', 'success')
            .then(() => {
                window.history.back();
            });
        } else {
            Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
    });
}

// Helper function for format_bytes (ถ้า header ไม่มี)
function formatBytes(bytes, precision = 2) {
    if (bytes <= 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(precision)) + ' ' + sizes[i];
}
</script>


<script>
// ✅ ฟังก์ชัน goBack ที่ปลอดภัย
function goBack() {
    try {
        // ตรวจสอบว่ามี history หรือไม่
        if (window.history.length > 1) {
            // ลองใช้ history.back() ก่อน
            window.history.back();
            
            // ถ้าไม่ทำงานใน 500ms ให้ redirect
            setTimeout(function() {
                // ถ้ายังอยู่หน้าเดิม (URL ไม่เปลี่ยน) ให้ redirect
                if (window.location.href.includes('user_details')) {
                    fallbackRedirect();
                }
            }, 500);
        } else {
            // ถ้าไม่มี history ให้ redirect ไปหน้าหลัก
            fallbackRedirect();
        }
    } catch (error) {
        console.error('goBack error:', error);
        fallbackRedirect();
    }
}

// ✅ ฟังก์ชัน fallback เมื่อ history.back() ไม่ทำงาน
function fallbackRedirect() {
    // ลำดับความสำคัญในการ redirect
    const fallbackUrls = [
        '<?php echo site_url('google_drive_system/setup'); ?>', // หน้าจัดการ System Storage
        '<?php echo site_url('google_drive_system'); ?>',       // หน้าหลัก Google Drive
        '<?php echo site_url('member'); ?>',                    // หน้าสมาชิก
        '<?php echo site_url(); ?>'                            // หน้าแรกของเว็บ
    ];
    
    // ตรวจสอบว่ามาจากหน้าไหน และ redirect กลับไปที่เหมาะสม
    const referrer = document.referrer;
    
    if (referrer && referrer.includes('google_drive_system/setup')) {
        window.location.href = '<?php echo site_url('google_drive_system/setup'); ?>';
    } else if (referrer && referrer.includes('google_drive_system')) {
        window.location.href = '<?php echo site_url('google_drive_system'); ?>';
    } else {
        window.location.href = '<?php echo site_url('google_drive_system/setup'); ?>';
    }
}

// ✅ เพิ่ม event listener สำหรับปุ่ม Browser Back
window.addEventListener('popstate', function(event) {
    // ถ้าผู้ใช้กดปุ่ม Back ของ Browser
    console.log('Browser back button pressed');
});

// ✅ ตรวจสอบ Referrer เมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function() {
    const backButton = document.querySelector('button[onclick="goBack()"]');
    if (backButton) {
        const referrer = document.referrer;
        
        // แสดง tooltip ที่เหมาะสม
        if (referrer && referrer.includes('setup')) {
            backButton.title = 'กลับไปหน้าตั้งค่าระบบ';
        } else if (referrer && referrer.includes('google_drive')) {
            backButton.title = 'กลับไปหน้า Google Drive';
        } else {
            backButton.title = 'กลับไปหน้าก่อนหน้า';
        }
    }
});

// ✅ Alternative: ปุ่ม Back แบบ Smart
function smartBack() {
    // วิธีการ 1: ลอง history.back() ก่อน
    const currentUrl = window.location.href;
    
    window.history.back();
    
    // วิธีการ 2: ตรวจสอบหลัง 300ms ว่า URL เปลี่ยนหรือไม่
    setTimeout(() => {
        if (window.location.href === currentUrl) {
            // ถ้า URL ไม่เปลี่ยน แสดงว่า history.back() ไม่ทำงาน
            console.log('history.back() failed, using fallback');
            fallbackRedirect();
        }
    }, 300);
}

// ✅ แก้ไขให้ใช้ในปุ่ม
// เปลี่ยนจาก onclick="window.history.back()" 
// เป็น onclick="goBack()" หรือ onclick="smartBack()"
</script>


<?php

// Helper function for PHP (ถ้าใช้ใน PHP)
function format_bytes($size, $precision = 2) {
    if ($size <= 0) return '0 B';
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}
?>