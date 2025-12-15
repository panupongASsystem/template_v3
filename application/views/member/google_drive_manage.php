<?php
// application/views/member/google_drive_manage.php - Complete Updated Version
?>
<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">จัดการสมาชิก Google Drive</h2>
            <p class="text-gray-600">
                ภาพรวมและประวัติการใช้งาน Google Drive 
                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                    <?php echo ($storage_mode === 'centralized') ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                    <?php echo ($storage_mode === 'centralized') ? 'Centralized Storage' : 'User-based Storage'; ?>
                </span>
            </p>
        </div>
        <div class="flex space-x-3">
            <!-- Bulk Actions -->
            <?php if ($storage_mode === 'centralized'): ?>
            <div class="relative">
                <button id="bulkActionsBtn" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center">
                    <i class="fas fa-tasks mr-2"></i>การจัดการแบบกลุ่ม
                    <i class="fas fa-chevron-down ml-2"></i>
                </button>
                <div id="bulkActionsMenu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border z-50">
                    <div class="py-1">
                        <button onclick="bulkGrantStorageAccess()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-plus text-green-500 mr-2"></i>อนุมัติการใช้งาน (หลายคน)
                        </button>
                        <button onclick="bulkToggleAccess(true)" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-toggle-on text-green-500 mr-2"></i>เปิดใช้งาน Google Drive (ทั้งหมด)
                        </button>
                        <button onclick="bulkToggleAccess(false)" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-toggle-off text-red-500 mr-2"></i>ปิดใช้งาน Google Drive (ทั้งหมด)
                        </button>
                        <div class="border-t border-gray-100"></div>

                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <button onclick="batchUpdateFolderNames()" 
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700" 
                    title="อัปเดตชื่อโฟลเดอร์จาก Google Drive">
                <i class="fas fa-sync mr-2"></i>อัปเดตโฟลเดอร์
            </button>
            
            <button onclick="toggleStorageMode()" 
                    class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                <i class="fas fa-exchange-alt mr-2"></i>เปลี่ยนโหมด
            </button>
            
            <button onclick="forceUpdateStorage()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-sync mr-2"></i>อัปเดต Storage
            </button>
            
            <button onclick="refreshData()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-sync-alt mr-2"></i>รีเฟรช
            </button>
        </div>
    </div>

    <!-- System Storage Info Card -->
    <?php if (isset($system_storage) && $system_storage): ?>
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full mr-4">
                    <i class="fas fa-cloud text-2xl text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900">System Storage กลาง</h3>
                    <p class="text-blue-700">
                        Google Account: <strong><?php echo $system_storage->google_account_email; ?></strong>
                    </p>
                    <p class="text-sm text-blue-600">
                        พื้นที่ใช้งาน (ดึงข้อมูลจาก Google Drive): <?php echo number_format($system_storage->storage_usage_percent, 1); ?>% 
                        (<?php echo $system_storage->active_users; ?> users, <?php echo $system_storage->total_folders; ?> folders)
                    </p>
                </div>
            </div>
            <div class="text-right">
                <div class="w-16 h-16 relative">
                    <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                        <path class="text-gray-300" stroke="currentColor" stroke-width="3" fill="none" 
                              d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        <path class="text-blue-500" stroke="currentColor" stroke-width="3" fill="none" 
                              stroke-dasharray="<?php echo $system_storage->storage_usage_percent; ?>, 100"
                              d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-semibold text-blue-600">
                            <?php echo number_format($system_storage->storage_usage_percent, 0); ?>%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- ผู้ใช้ทั้งหมด / Total Users -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">
                        <?php echo ($storage_mode === 'centralized') ? 'ผู้ใช้ทั้งหมด' : 'สมาชิกทั้งหมด'; ?>
                    </h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php 
                        if ($storage_mode === 'centralized') {
                            echo isset($statistics['total_users']) ? number_format($statistics['total_users']) : '0';
                        } else {
                            echo isset($statistics['total_members']) ? number_format($statistics['total_members']) : '0';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- ผู้ใช้ที่มีสิทธิ์ / Active Users -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-green-100 to-green-50 rounded-xl">
                    <i class="fas fa-user-check text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">
                        <?php echo ($storage_mode === 'centralized') ? 'ผู้ใช้ที่มีสิทธิ์' : 'สมาชิกที่เชื่อมต่อ'; ?>
                    </h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php 
                        if ($storage_mode === 'centralized') {
                            echo isset($statistics['active_users']) ? number_format($statistics['active_users']) : '0';
                        } else {
                            echo isset($statistics['connected_members']) ? number_format($statistics['connected_members']) : '0';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- จำนวน Folders -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-50 rounded-xl">
                    <i class="fas fa-folder text-2xl text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">Folders ทั้งหมด</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo isset($statistics['total_folders']) ? number_format($statistics['total_folders']) : '0'; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- ไฟล์ที่ Sync / Total Files -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-orange-100 to-orange-50 rounded-xl">
                    <i class="fas fa-file text-2xl text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">
                        <?php echo ($storage_mode === 'centralized') ? 'ไฟล์ทั้งหมด' : 'ไฟล์ที่ Sync'; ?>
                    </h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php 
                        if ($storage_mode === 'centralized') {
                            echo isset($statistics['total_files']) ? number_format($statistics['total_files']) : '0';
                        } else {
                            echo isset($statistics['synced_files']) ? number_format($statistics['synced_files']) : '0';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Members/Users Table -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    <?php echo ($storage_mode === 'centralized') ? 'ผู้ใช้งานทั้งหมด (System Storage)' : 'สมาชิกที่เชื่อมต่อ Google Drive'; ?>
                </h3>
                <div class="flex space-x-3">
                    <input type="text" id="searchMembers" placeholder="ค้นหา..." 
                           class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <?php if ($storage_mode === 'centralized'): ?>
                    <button onclick="showBulkSelectionMode()" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-check-square mr-2"></i>เลือกหลายรายการ
                    </button>
                    <?php endif; ?>
                    <button onclick="exportMembers()" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <?php if ($storage_mode === 'centralized'): ?>
                        <th class="px-6 py-3 text-gray-600">
                            <input type="checkbox" id="selectAll" class="hidden">
                            <label for="selectAll" class="cursor-pointer">เลือก</label>
                        </th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-gray-600">สมาชิก</th>
                        <th class="px-6 py-3 text-gray-600">
                            <?php if ($storage_mode === 'centralized'): ?>
                                Storage Quota / การอนุมัติ
                            <?php else: ?>
                                Google Account
                            <?php endif; ?>
                        </th>
                        <th class="px-6 py-3 text-gray-600">ตำแหน่ง</th>
                        <th class="px-6 py-3 text-gray-600">
                            <?php if ($storage_mode === 'centralized'): ?>
                                สถานะการใช้งาน
                            <?php else: ?>
                                Folders
                            <?php endif; ?>
                        </th>
                        <th class="px-6 py-3 text-gray-600">
                            <?php echo ($storage_mode === 'centralized') ? 'เข้าใช้ล่าสุด' : 'วันที่เชื่อมต่อ'; ?>
                        </th>
                        <th class="px-6 py-3 text-gray-600">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y" id="membersTable">
                    <?php if ($storage_mode === 'centralized' && !empty($storage_users)): ?>
                        <!-- Centralized Storage Users - แสดงทั้งที่มีสิทธิ์และไม่มีสิทธิ์ -->
                        <?php foreach ($storage_users as $user): ?>
                            <tr class="hover:bg-gray-50 <?php echo !$user->storage_access_granted ? 'bg-gray-50 opacity-75' : ''; ?>" 
                                data-member-id="<?php echo $user->m_id; ?>">
                                
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="member-checkbox hidden" value="<?php echo $user->m_id; ?>">
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-500"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-gray-800 font-medium">
                                                <?php echo $user->m_fname . ' ' . $user->m_lname; ?>
                                                <!-- Badge แสดงสถานะ -->
                                                <?php if (!$user->storage_access_granted): ?>
    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
        <i class="fas fa-clock mr-1"></i>รอการอนุมัติ
    </span>
<?php endif; ?>
                                            </div>
                                            <div class="text-gray-600 text-sm">
                                                <?php echo $user->m_email; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <?php if ($user->storage_access_granted): ?>
    <!-- แสดง Storage Quota สำหรับผู้ที่มีสิทธิ์ -->
    <div class="flex items-center">
        <div class="flex-1">
            <div class="text-sm text-gray-600">
                <?php echo number_format($user->storage_quota_used / 1048576, 1); ?> MB / 
                <?php echo number_format($user->storage_quota_limit / 1048576, 1); ?> MB
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                <?php $usage_percent = ($user->storage_quota_used / $user->storage_quota_limit) * 100; ?>
                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo min($usage_percent, 100); ?>%"></div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- แสดงข้อความสำหรับผู้ที่ยังไม่มีสิทธิ์ -->
    <div class="text-center py-2">
        <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm bg-gray-100 text-gray-600">
            <i class="fas fa-lock mr-2"></i>
            ยังไม่ได้รับสิทธิ์
        </span>
        <button onclick="grantStorageAccess(<?php echo $user->m_id; ?>, '<?php echo addslashes($user->m_fname . ' ' . $user->m_lname); ?>')" 
                class="mt-1 block w-full px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
            <i class="fas fa-plus mr-1"></i>อนุมัติใช้งาน
        </button>
    </div>
<?php endif; ?>
                                </td>
                                
                                <td class="px-6 py-4 text-gray-600">
                                    <?php echo $user->pname ?: '-'; ?>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <?php if ($user->storage_access_granted): ?>
                                        <!-- สำหรับผู้ที่มีสิทธิ์ - แสดง Toggle และสถานะ -->
                                        <div class="flex items-center space-x-2">
                                            <!-- Google Drive Toggle Switch -->
                                            <div class="flex items-center">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" 
       class="sr-only peer google-drive-toggle" 
       <?php echo $user->storage_access_granted ? 'checked' : ''; ?>
       data-member-id="<?php echo $user->m_id; ?>"
       data-member-name="<?php echo htmlspecialchars($user->m_fname . ' ' . $user->m_lname); ?>"
       data-initial-state="<?php echo $user->storage_access_granted ? '1' : '0'; ?>">

                                                    <div class="relative w-11 h-6 <?php echo $user->storage_access_granted ? 'bg-blue-600' : 'bg-gray-300'; ?> peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all <?php echo $user->storage_access_granted ? 'after:translate-x-full' : ''; ?>"></div>

                                                </label>
                                                <span class="ml-2 text-sm text-gray-600 toggle-status">
    <?php echo $user->storage_access_granted ? 'เปิด' : 'ปิด'; ?>
</span>
                                            </div>
                                            
                                            <!-- Status Badge -->
                                            <?php if ($user->storage_access_granted): ?>
    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
        <i class="fas fa-check-circle mr-1"></i>ใช้งานได้
    </span>
<?php else: ?>
    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
        <i class="fas fa-times-circle mr-1"></i>ปิดใช้งาน
    </span>
<?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <!-- สำหรับผู้ที่ยังไม่มีสิทธิ์ - แสดงข้อความ -->
                                        <div class="text-center">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                <i class="fas fa-minus-circle mr-1"></i>ไม่สามารถใช้งาน
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="px-6 py-4 text-gray-600">
                                    <?php if ($user->storage_access_granted): ?>
                                        <?php echo $user->last_storage_access ? date('d/m/Y H:i', strtotime($user->last_storage_access)) : 'ยังไม่เคย'; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <button onclick="viewUserProfile(<?php echo $user->m_id; ?>)" 
                                                class="w-8 h-8 flex items-center justify-center rounded bg-blue-50 text-blue-600 hover:bg-blue-100" 
                                                title="ดูโปรไฟล์ผู้ใช้">
                                            <i class="fas fa-user"></i>
                                        </button>
                                        
                                        <?php if ($user->storage_access_granted): ?>
                                            <!-- ปุ่มสำหรับผู้ที่มีสิทธิ์ -->
                                            <button onclick="manageUserStorage(<?php echo $user->m_id; ?>, '<?php echo addslashes($user->m_fname . ' ' . $user->m_lname); ?>', <?php echo $user->storage_quota_limit; ?>, <?php echo $user->storage_quota_used; ?>)" 
                                                    class="w-8 h-8 flex items-center justify-center rounded bg-green-50 text-green-600 hover:bg-green-100" 
                                                    title="จัดการ Storage">
                                                <i class="fas fa-cogs"></i>
                                            </button>
                                        <?php else: ?>
                                            <!-- ปุ่มสำหรับผู้ที่ยังไม่มีสิทธิ์ -->
                                            <button onclick="grantStorageAccess(<?php echo $user->m_id; ?>, '<?php echo addslashes($user->m_fname . ' ' . $user->m_lname); ?>')" 
                                                    class="w-8 h-8 flex items-center justify-center rounded bg-green-50 text-green-600 hover:bg-green-100" 
                                                    title="อนุมัติใช้งาน Storage">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($user->google_email): ?>
                                            <button onclick="resetGoogleConnection(<?php echo $user->m_id; ?>)" 
                                                    class="w-8 h-8 flex items-center justify-center rounded bg-orange-50 text-orange-600 hover:bg-orange-100" 
                                                    title="Reset Google Connection">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php elseif ($storage_mode === 'user_based' && !empty($connected_members)): ?>
                        <!-- User-based Storage Members (แบบเดิม) -->
                        <?php foreach ($connected_members as $member): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-500"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-gray-800 font-medium">
                                                <?php echo $member->m_fname . ' ' . $member->m_lname; ?>
                                            </div>
                                            <div class="text-gray-600 text-sm">
                                                ID: <?php echo $member->m_id; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-800"><?php echo $member->google_email ?: '-'; ?></div>
                                    <?php if ($member->google_drive_enabled && $member->google_email): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>เชื่อมต่อแล้ว
                                        </span>
                                    <?php elseif ($member->google_drive_enabled): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-exclamation-circle mr-1"></i>รอเชื่อมต่อ
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i>ปิดใช้งาน
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?php echo $member->pname ?: '-'; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <?php echo $member->total_folders ?: '0'; ?> folders
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?php echo $member->last_storage_access ? date('d/m/Y H:i', strtotime($member->last_storage_access)) : '-'; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <button onclick="viewMemberLogs(<?php echo $member->m_id; ?>)" 
                                                class="w-8 h-8 flex items-center justify-center rounded bg-blue-50 text-blue-600 hover:bg-blue-100" 
                                                title="ดู Logs">
                                            <i class="fas fa-history"></i>
                                        </button>
                                        <button onclick="manageMemberDrive(<?php echo $member->m_id; ?>, '<?php echo addslashes($member->m_fname . ' ' . $member->m_lname); ?>')" 
                                                class="w-8 h-8 flex items-center justify-center rounded bg-green-50 text-green-600 hover:bg-green-100" 
                                                title="จัดการ Drive">
                                            <i class="fab fa-google-drive"></i>
                                        </button>
                                        <button onclick="disconnectMember(<?php echo $member->m_id; ?>)" 
                                                class="w-8 h-8 flex items-center justify-center rounded bg-red-50 text-red-600 hover:bg-red-100" 
                                                title="ตัดการเชื่อมต่อ">
                                            <i class="fas fa-unlink"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo ($storage_mode === 'centralized') ? '7' : '6'; ?>" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                <p>
                                    <?php echo ($storage_mode === 'centralized') ? 'ไม่พบข้อมูลผู้ใช้งาน' : 'ยังไม่มีสมาชิกที่เชื่อมต่อ Google Drive'; ?>
                                </p>
                                <?php if ($storage_mode === 'centralized'): ?>
                                    <button onclick="window.location.href='<?php echo site_url('google_drive_system/setup'); ?>'" 
                                            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        ตั้งค่า System Storage
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity Logs -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">กิจกรรมล่าสุด</h3>
                <div class="flex space-x-2">
                    <button onclick="loadRecentLogs()" 
                            class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                        <i class="fas fa-sync-alt mr-1"></i>รีเฟรช
                    </button>
                    <a href="<?php echo site_url(($storage_mode === 'centralized') ? 'google_drive_system/reports?type=activities' : 'google_drive/all_logs'); ?>" 
                       class="text-blue-600 hover:text-blue-800 font-medium">
                        ดูทั้งหมด <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="space-y-4" id="recentActivityLogs">
                <!-- Recent logs will be loaded here -->
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-spinner fa-spin text-3xl text-gray-300 mb-3"></i>
                    <p>กำลังโหลดกิจกรรมล่าสุด...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Storage Quota Management Modal -->
<div id="storageQuotaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="quotaModalTitle">
                    ปรับ Storage Quota
                </h3>
                <button onclick="closeQuotaModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mt-2">
                <!-- User Info -->
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-gray-500"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900" id="quotaUserName">ชื่อผู้ใช้</div>
                            <div class="text-sm text-gray-500">การใช้งานปัจจุบัน</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="flex justify-between text-sm mb-1">
                            <span id="quotaCurrentUsage">0 MB</span>
                            <span id="quotaCurrentLimit">1,024 MB</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="quotaProgressBar" class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Quota Settings -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ขนาด Storage Quota ใหม่
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="newQuotaInput" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="1024"
                                   min="1"
                                   max="999999"
                                   step="1">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-500 text-sm" id="quotaUnit">MB</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            ขั้นต่ำ: 1 MB | แนะนำ: 1,024 MB (1 GB) | สูงสุด: 999,999 MB หรือ Unlimited
                        </p>
                    </div>
                    
                    <!-- Quick Presets -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ตัวเลือกเร็ว
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="setQuickQuota(512)" 
                                    class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                512 MB
                            </button>
                            <button onclick="setQuickQuota(1024)" 
                                    class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                1 GB
                            </button>
                            <button onclick="setQuickQuota(2048)" 
                                    class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                2 GB
                            </button>
                            <button onclick="setQuickQuota(5120)" 
                                    class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                5 GB
                            </button>
                            <button onclick="setQuickQuota(10240)" 
                                    class="px-3 py-2 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                10 GB
                            </button>
                            <button onclick="setQuickQuota(999999)" 
                                    class="px-3 py-2 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200">
                                <i class="fas fa-infinity mr-1"></i>Unlimited
                            </button>
                        </div>
                    </div>
                    
                    <!-- Warning if new quota < current usage -->
                    <div id="quotaWarning" class="hidden p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mr-2 mt-0.5"></i>
                            <div class="text-sm text-yellow-700">
                                <strong>คำเตือน:</strong> ขนาด Quota ใหม่น้อยกว่าพื้นที่ที่ใช้งานอยู่ 
                                ผู้ใช้อาจไม่สามารถอัปโหลดไฟล์เพิ่มเติมได้
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeQuotaModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        ยกเลิก
                    </button>
                    <button onclick="confirmQuotaUpdate()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" 
                            id="confirmQuotaBtn">
                        อัปเดต Quota
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JavaScript สำหรับ All Features -->
<script>
// Global variables
const currentStorageMode = '<?php echo $storage_mode; ?>';
let selectedStorageMode = currentStorageMode;
let bulkSelectionMode = false;
let currentQuotaUserId = null;

console.log('Current Storage Mode:', currentStorageMode);

// ===== Google Drive Toggle Functions =====

/**
 * 🔄 Enhanced Toggle Google Drive Access - Auto Create Personal Folder
 */
function toggleGoogleDriveAccess(memberId, memberName, enabled) {
    const action = enabled ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
    
    console.log('=== Enhanced Toggle Debug Info ===');
    console.log('Member ID:', memberId);
    console.log('Member Name:', memberName);
    console.log('Enabled:', enabled);
    console.log('Action:', action);
    console.log('Storage Mode:', currentStorageMode);
    
    Swal.fire({
        title: `ยืนยันการ${action} Google Drive`,
        html: `
            <div class="text-center">
                <p>คุณต้องการ${action} Google Drive สำหรับ <strong>${memberName}</strong> หรือไม่?</p>
                ${enabled && currentStorageMode === 'centralized' ? 
                    `<div class="mt-3 p-3 bg-blue-50 rounded-lg">
                        <i class="fas fa-folder text-blue-500 mr-2"></i>
                        <div class="text-sm">
                            <strong>จะทำการ:</strong><br>
                            • สร้าง Personal Folder อัตโนมัติ<br>
                            • เพิ่มสิทธิ์ Default ตามตำแหน่งงาน<br>
                            • กำหนด Storage Quota 1GB
                        </div>
                    </div>` : 
                    ''}
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: action,
        cancelButtonText: 'ยกเลิก',
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            // 🆕 เรียกใช้ Enhanced Function
            enhancedToggleGoogleDriveStep(memberId, memberName, enabled);
        } else {
            resetToggleSwitch(memberId, !enabled);
        }
    });
}


	
	function enhancedToggleGoogleDriveStep(memberId, memberName, enabled) {
    const requestData = `member_id=${memberId}&enabled=${enabled ? 1 : 0}`;
    console.log('Enhanced Toggle Request:', requestData);
    
    fetch('<?php echo site_url('google_drive/enhanced_toggle_google_drive_access'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: requestData
    })
    .then(response => {
        console.log('Enhanced Toggle Response Status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.text();
    })
    .then(text => {
        console.log('Enhanced Toggle Raw Response:', text);
        
        if (!text || text.trim() === '') {
            throw new Error('Empty response from server');
        }
        
        try {
            const data = JSON.parse(text);
            console.log('Enhanced Toggle Parsed JSON:', data);
            
            if (data.success) {
                // อัปเดต UI
                updateToggleSwitch(memberId, enabled);
                updateConnectionStatusCentralized(memberId, enabled);
                
                // แสดงผลสำเร็จ
                Swal.fire({
                    title: 'สำเร็จ! 🎉',
                    html: `
                        <div class="text-center">
                            <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                            <p><strong>${memberName}</strong> ${enabled ? 'เปิดใช้งาน' : 'ปิดใช้งาน'} Google Drive แล้ว</p>
                            ${enabled ? 
                                `<div class="mt-3 p-3 bg-green-50 rounded-lg">
                                    <div class="text-sm text-green-700">
                                        ✅ สร้าง Personal Folder เรียบร้อย<br>
                                        ✅ เพิ่มสิทธิ์ Default ตามตำแหน่ง<br>
                                        ✅ กำหนด Storage Quota 1GB
                                    </div>
                                </div>` : 
                                ''}
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                });
            } else {
                Swal.fire('เกิดข้อผิดพลาด', data.message || 'Unknown error', 'error');
                resetToggleSwitch(memberId, !enabled);
            }
        } catch (parseError) {
            console.error('Enhanced Toggle JSON Parse Error:', parseError);
            Swal.fire('JSON Parse Error', 'ไม่สามารถแปลงข้อมูลได้', 'error');
            resetToggleSwitch(memberId, !enabled);
        }
    })
    .catch(error => {
        console.error('Enhanced Toggle Fetch Error:', error);
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
        resetToggleSwitch(memberId, !enabled);
    });
}
	
	
/**
 * 🔄 Step 1: Toggle Google Drive Status
 */
function toggleGoogleDriveStep1(memberId, memberName, enabled) {
    const requestData = `member_id=${memberId}&enabled=${enabled ? 1 : 0}`;
    console.log('Step 1 - Toggle Request:', requestData);
    
    fetch('<?php echo site_url('google_drive/simple_toggle'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: requestData
    })
    .then(response => {
        console.log('Step 1 Response Status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.text();
    })
    .then(text => {
        console.log('Step 1 Raw Response:', text);
        
        if (!text || text.trim() === '') {
            throw new Error('Empty response from server');
        }
        
        try {
            const data = JSON.parse(text);
            console.log('Step 1 Parsed JSON:', data);
            
            if (data.success) {
                // อัปเดต UI
                updateToggleSwitch(memberId, enabled);
                updateConnectionStatusCentralized(memberId, enabled);
                
                // ถ้าเปิดใช้งาน ให้ไปขั้นตอนที่ 2: สร้าง/ตรวจสอบ Personal Folder
                if (enabled && currentStorageMode === 'centralized') {
                    console.log('Moving to Step 2: Check/Create Personal Folder');
                    toggleGoogleDriveStep2(memberId, memberName);
                } else {
                    // ถ้าปิดใช้งาน แค่แสดงผลสำเร็จ
                    Swal.fire('สำเร็จ', `${enabled ? 'เปิด' : 'ปิด'}ใช้งาน Google Drive สำหรับ ${memberName} แล้ว`, 'success');
                }
            } else {
                Swal.fire('เกิดข้อผิดพลาด', data.message || 'Unknown error', 'error');
                resetToggleSwitch(memberId, !enabled);
            }
        } catch (parseError) {
            console.error('Step 1 JSON Parse Error:', parseError);
            Swal.fire('JSON Parse Error', 'ไม่สามารถแปลงข้อมูลได้', 'error');
            resetToggleSwitch(memberId, !enabled);
        }
    })
    .catch(error => {
        console.error('Step 1 Fetch Error:', error);
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
        resetToggleSwitch(memberId, !enabled);
    });
}

/**
 * 🆕 Step 2: Check/Create Personal Folder
 */
function toggleGoogleDriveStep2(memberId, memberName) {
    console.log('Step 2 - Checking Personal Folder for Member:', memberId);
    
    // แสดง Loading ขณะตรวจสอบ/สร้าง Personal Folder
    Swal.fire({
        title: 'กำลังตรวจสอบ Personal Folder',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-folder text-blue-500 text-4xl"></i>
                </div>
                <p>กำลังตรวจสอบและสร้าง Personal Folder สำหรับ</p>
                <p class="font-semibold text-blue-600">${memberName}</p>
                <div class="mt-3 flex justify-center">
                    <div class="animate-pulse flex space-x-1">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    </div>
                </div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // 🔄 เปลี่ยนจาก check_user_personal_folder เป็น create_user_personal_folder โดยตรง
    fetch('<?php echo site_url('google_drive/create_user_personal_folder'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: `member_id=${memberId}`
    })
    .then(response => {
        console.log('Step 2 Response Status:', response.status);
        console.log('Step 2 Response Headers:', response.headers.get('content-type'));
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.text();
    })
    .then(text => {
        console.log('Step 2 Raw Response:', text);
        console.log('Step 2 Response Length:', text.length);
        
        // ตรวจสอบว่า Response ว่างหรือไม่
        if (!text || text.trim() === '') {
            console.error('Step 2: Empty response from server');
            // ถ้า Response ว่าง แต่ toggle สำเร็จแล้ว ให้แสดงข้อความสำเร็จ
            Swal.fire('เปิดใช้งาน Google Drive สำเร็จ', 
                `${memberName} สามารถใช้งาน Google Drive ได้แล้ว\n\nหมายเหตุ: ไม่สามารถสร้าง Personal Folder ได้`, 
                'success');
            return;
        }
        
        // ตรวจสอบว่าเป็น JSON หรือไม่
        let data;
        try {
            data = JSON.parse(text);
            console.log('Step 2 Parsed Response:', data);
        } catch (parseError) {
            console.error('Step 2 JSON Parse Error:', parseError);
            console.error('Step 2 Response Text:', text);
            
            // ถ้า parse ไม่ได้ อาจเป็น HTML Error page
            if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                console.error('Step 2: Received HTML instead of JSON - likely 404 or server error');
                Swal.fire('เปิดใช้งาน Google Drive สำเร็จ', 
                    `${memberName} สามารถใช้งาน Google Drive ได้แล้ว\n\nหมายเหตุ: ไม่สามารถสร้าง Personal Folder ได้ (ฟีเจอร์อาจยังไม่พร้อม)`, 
                    'success');
            } else {
                Swal.fire('เปิดใช้งาน Google Drive สำเร็จ', 
                    `${memberName} สามารถใช้งาน Google Drive ได้แล้ว\n\nหมายเหตุ: ไม่สามารถแปลงข้อมูล Personal Folder ได้`, 
                    'success');
            }
            return;
        }
        
        // ประมวลผล Response ที่ Parse สำเร็จ
        if (data && data.success) {
            // สร้าง Personal Folder สำเร็จ
            Swal.fire({
                title: 'เปิดใช้งาน Google Drive สำเร็จ!',
                html: `
                    <div class="text-center">
                        <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                        <p><strong>${memberName}</strong> สามารถใช้งาน Google Drive ได้แล้ว</p>
                        <div class="mt-3 p-3 bg-green-50 rounded-lg">
                            <i class="fas fa-folder${data.data && data.data.message && data.data.message.includes('อยู่แล้ว') ? '' : '-plus'} text-green-600 mr-2"></i>
                            <span class="text-green-700">Personal Folder: <strong>${data.data ? data.data.folder_name : 'พร้อมใช้งาน'}</strong></span>
                        </div>
                        ${data.data && data.data.web_view_link ? 
                            `<div class="mt-2">
                                <a href="${data.data.web_view_link}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i>เปิดดูโฟลเดอร์
                                </a>
                            </div>` : 
                            ''}
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'ตกลง'
            });
        } else {
            // Response มี error หรือไม่สำเร็จ
            console.log('Step 2: API returned error or unsuccessful response');
            Swal.fire('เปิดใช้งาน Google Drive สำเร็จ', 
                `${memberName} สามารถใช้งาน Google Drive ได้แล้ว\n\nหมายเหตุ: ${data && data.message ? data.message : 'ไม่สามารถสร้าง Personal Folder ได้'}`, 
                'success');
        }
    })
    .catch(error => {
        console.error('Step 2 Fetch Error:', error);
        console.error('Step 2 Error Details:', {
            name: error.name,
            message: error.message,
            stack: error.stack
        });
        
        // ถ้าสร้าง Personal Folder ไม่ได้ แต่ toggle สำเร็จแล้ว
        Swal.fire('เปิดใช้งาน Google Drive สำเร็จ', 
            `${memberName} สามารถใช้งาน Google Drive ได้แล้ว\n\nหมายเหตุ: ไม่สามารถสร้าง Personal Folder ได้ (${error.message})`, 
            'success');
    });
}


/**
 * 🆕 Step 3: Create Personal Folder
 */
function toggleGoogleDriveStep3(memberId, memberName) {
    console.log('Step 3 - Creating Personal Folder for Member:', memberId);
    
    // อัปเดต Loading message
    Swal.update({
        title: 'กำลังสร้าง Personal Folder',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-folder-plus text-blue-500 text-4xl"></i>
                </div>
                <p>กำลังสร้าง Personal Folder สำหรับ</p>
                <p class="font-semibold text-blue-600">${memberName}</p>
                <div class="mt-3 text-sm text-gray-600">
                    <p>โฟลเดอร์จะถูกสร้างในระบบ Google Drive กลาง</p>
                </div>
            </div>
        `
    });
    
    // สร้าง Personal Folder
    fetch('<?php echo site_url('google_drive/create_user_personal_folder'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: `member_id=${memberId}`
    })
    .then(response => {
        console.log('Step 3 Create Response Status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Step 3 Create Raw Response:', text);
        
        try {
            const data = JSON.parse(text);
            console.log('Step 3 Create Parsed Response:', data);
            
            if (data.success) {
                // สร้าง Personal Folder สำเร็จ
                Swal.fire({
                    title: 'เปิดใช้งาน Google Drive สำเร็จ!',
                    html: `
                        <div class="text-center">
                            <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                            <p><strong>${memberName}</strong> สามารถใช้งาน Google Drive ได้แล้ว</p>
                            <div class="mt-3 p-3 bg-green-50 rounded-lg">
                                <i class="fas fa-folder-plus text-green-600 mr-2"></i>
                                <span class="text-green-700">สร้าง Personal Folder: <strong>${data.folder_name}</strong></span>
                            </div>
                            ${data.web_view_link ? 
                                `<div class="mt-2">
                                    <a href="${data.web_view_link}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-external-link-alt mr-1"></i>เปิดดูโฟลเดอร์
                                    </a>
                                </div>` : 
                                ''}
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                });
            } else {
                // สร้าง Personal Folder ไม่สำเร็จ แต่ toggle สำเร็จแล้ว
                Swal.fire({
                    title: 'เปิดใช้งาน Google Drive สำเร็จ',
                    html: `
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-3"></i>
                            <p><strong>${memberName}</strong> สามารถใช้งาน Google Drive ได้แล้ว</p>
                            <div class="mt-3 p-3 bg-yellow-50 rounded-lg">
                                <i class="fas fa-exclamation-circle text-yellow-600 mr-2"></i>
                                <span class="text-yellow-700">หมายเหตุ: ${data.message}</span>
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    confirmButtonText: 'ตกลง'
                });
            }
        } catch (parseError) {
            console.error('Step 3 Create JSON Parse Error:', parseError);
            // ถ้า parse ไม่ได้ แต่ toggle สำเร็จแล้ว
            Swal.fire('เปิดใช้งาน Google Drive สำเร็จ', 
                `${memberName} สามารถใช้งาน Google Drive ได้แล้ว\n\nหมายเหตุ: ไม่สามารถสร้าง Personal Folder ได้`, 
                'success');
        }
    })
    .catch(error => {
        console.error('Step 3 Create Fetch Error:', error);
        console.error('Step 3 Create Error Details:', {
            name: error.name,
            message: error.message,
            stack: error.stack
        });
        
        // ถ้าสร้าง Personal Folder ไม่ได้ แต่ toggle สำเร็จแล้ว
        Swal.fire('เปิดใช้งาน Google Drive สำเร็จ', 
            `${memberName} สามารถใช้งาน Google Drive ได้แล้ว\n\nหมายเหตุ: ไม่สามารถสร้าง Personal Folder ได้ (${error.message})`, 
            'success');
    });
}

/**
 * อัปเดต Connection Status สำหรับ Centralized Storage
 */
function updateConnectionStatusCentralized(memberId, enabled) {
    const row = document.querySelector(`tr[data-member-id="${memberId}"]`);
    if (!row) return;
    
    const statusContainer = row.querySelector('.inline-flex.items-center.px-2.py-1.rounded-full');
    
    if (statusContainer) {
        if (currentStorageMode === 'centralized') {
            if (enabled) {
                statusContainer.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
                statusContainer.innerHTML = '<i class="fas fa-check-circle mr-1"></i>ใช้งานได้';
            } else {
                statusContainer.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
                statusContainer.innerHTML = '<i class="fas fa-times-circle mr-1"></i>ปิดใช้งาน';
            }
        } else {
            if (enabled) {
                statusContainer.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800';
                statusContainer.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i>รอเชื่อมต่อ';
            } else {
                statusContainer.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
                statusContainer.innerHTML = '<i class="fas fa-times-circle mr-1"></i>ปิดใช้งาน';
            }
        }
    }
}

/**
 * อัปเดต Toggle Switch UI
 */
function updateToggleSwitch(memberId, enabled) {
    const toggle = document.querySelector(`input[data-member-id="${memberId}"]`);
    const statusText = toggle?.closest('.flex')?.querySelector('.toggle-status');
    
    if (toggle) {
        toggle.checked = enabled;
        
        const toggleContainer = toggle.nextElementSibling;
        if (toggleContainer) {
            toggleContainer.className = toggleContainer.className.replace(
                /(bg-blue-600|bg-gray-300)/g, 
                enabled ? 'bg-blue-600' : 'bg-gray-300'
            );
            
            toggleContainer.className = toggleContainer.className.replace(
                /(after:translate-x-full|after:translate-x-0)/g, 
                enabled ? 'after:translate-x-full' : 'after:translate-x-0'
            );
        }
        
        if (statusText) {
            statusText.textContent = enabled ? 'เปิด' : 'ปิด';
        }
    }
}

/**
 * Reset Toggle Switch
 */
function resetToggleSwitch(memberId, enabled) {
    updateToggleSwitch(memberId, enabled);
}

/**
 * Reset Google Connection
 */
function resetGoogleConnection(memberId) {
    Swal.fire({
        title: 'ยืนยันการ Reset Google Connection',
        text: 'การ Reset จะลบข้อมูลการเชื่อมต่อ Google Drive ทั้งหมด คุณแน่ใจหรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Reset',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?php echo site_url('google_drive/reset_user_google_connection'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `member_id=${memberId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('สำเร็จ', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
            });
        }
    });
}

// ===== Storage Access Management Functions =====

/**
 * อนุมัติให้ผู้ใช้เข้าใช้ Storage
 */
function grantStorageAccess(userId, userName) {
    Swal.fire({
        title: 'อนุมัติการใช้งาน Storage',
        html: `
            <div class="text-left">
                <div class="bg-blue-50 p-4 rounded-lg mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-user text-blue-500 mr-3"></i>
                        <div>
                            <div class="font-medium text-gray-800">${userName}</div>
                            <div class="text-sm text-gray-600">ผู้ใช้ที่ต้องการเข้าใช้ Storage</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        กำหนด Storage Quota
                    </label>
                    <select id="storageQuotaSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="536870912">512 MB</option>
                        <option value="1073741824" selected>1 GB (แนะนำ)</option>
                        <option value="2147483648">2 GB</option>
                        <option value="5368709120">5 GB</option>
                        <option value="10737418240">10 GB</option>
                        <option value="999999999999999">Unlimited</option>
                    </select>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex">
                        <i class="fas fa-info-circle text-yellow-500 mr-2 mt-0.5"></i>
                        <div class="text-sm text-yellow-700">
                            <strong>หมายเหตุ:</strong> ผู้ใช้จะได้รับสิทธิ์เข้าใช้ System Storage และสามารถอัปโหลดไฟล์ได้ตามขนาดที่กำหนด
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-check mr-2"></i>อนุมัติ',
        cancelButtonText: 'ยกเลิก',
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        preConfirm: () => {
            const quotaLimit = document.getElementById('storageQuotaSelect').value;
            return { quotaLimit };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { quotaLimit } = result.value;
            
            console.log('Grant Storage Access:', {
                userId: userId,
                userName: userName,
                quotaLimit: quotaLimit
            });
            
            fetch('<?php echo site_url('google_drive/grant_storage_access'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: `member_id=${userId}&quota_limit=${quotaLimit}`
            })
            .then(response => {
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);
                
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed response:', data);
                    
                    if (data.success) {
                        Swal.fire({
                            title: 'อนุมัติเรียบร้อย!',
                            text: `${userName} ได้รับสิทธิ์เข้าใช้ Storage แล้ว`,
                            icon: 'success',
                            confirmButtonText: 'รีเฟรชหน้า'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', data.message || 'ไม่สามารถอนุมัติได้', 'error');
                    }
                } catch (parseError) {
                    console.error('JSON Parse Error:', parseError);
                    console.error('Response text:', text);
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถแปลงข้อมูลจากเซิร์ฟเวอร์ได้', 'error');
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                Swal.fire('เกิดข้อผิดพลาด', `ไม่สามารถติดต่อเซิร์ฟเวอร์ได้: ${error.message}`, 'error');
            });
        }
    });
}

/**
 * เพิกถอนสิทธิ์การใช้งาน Storage - ฟังก์ชันนี้ถูกลบออกแล้ว
 */
// function revokeStorageAccess() - REMOVED

/**
 * เพิกถอนสิทธิ์แบบหลายคน - ฟังก์ชันนี้ถูกลบออกแล้ว
 */
// function bulkRevokeStorageAccess() - REMOVED
function bulkGrantStorageAccess() {
    const selectedMembers = Array.from(document.querySelectorAll('.member-checkbox:checked'))
                                .map(cb => cb.value);
    
    if (selectedMembers.length === 0) {
        Swal.fire('กรุณาเลือกผู้ใช้งาน', 'กรุณาเลือกผู้ใช้งานที่ต้องการอนุมัติ', 'warning');
        return;
    }
    
    // กรองเฉพาะผู้ใช้ที่ยังไม่มีสิทธิ์
    const waitingUsers = selectedMembers.filter(memberId => {
        const row = document.querySelector(`tr[data-member-id="${memberId}"]`);
        const hasAccess = row && !row.classList.contains('opacity-75');
        return !hasAccess;
    });
    
    if (waitingUsers.length === 0) {
        Swal.fire('ไม่พบผู้ใช้ที่รอการอนุมัติ', 'ผู้ใช้ที่เลือกได้รับสิทธิ์แล้วทั้งหมด', 'info');
        return;
    }
    
    Swal.fire({
        title: 'อนุมัติการใช้งาน Storage (หลายคน)',
        html: `
            <div class="text-left">
                <div class="bg-blue-50 p-4 rounded-lg mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-users text-blue-500 mr-3"></i>
                        <div>
                            <div class="font-medium text-gray-800">จำนวนผู้ใช้ที่จะอนุมัติ: ${waitingUsers.length} คน</div>
                            <div class="text-sm text-gray-600">ผู้ใช้ที่รอการอนุมัติ</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        กำหนด Storage Quota (สำหรับทุกคน)
                    </label>
                    <select id="bulkStorageQuotaSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="536870912">512 MB</option>
                        <option value="1073741824" selected>1 GB (แนะนำ)</option>
                        <option value="2147483648">2 GB</option>
                        <option value="5368709120">5 GB</option>
                        <option value="10737418240">10 GB</option>
                        <option value="999999999999999">Unlimited</option>
                    </select>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex">
                        <i class="fas fa-info-circle text-yellow-500 mr-2 mt-0.5"></i>
                        <div class="text-sm text-yellow-700">
                            <strong>หมายเหตุ:</strong> ผู้ใช้ทั้งหมดจะได้รับสิทธิ์เข้าใช้ System Storage พร้อมกัน
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `<i class="fas fa-check mr-2"></i>อนุมัติ ${waitingUsers.length} คน`,
        cancelButtonText: 'ยกเลิก',
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        preConfirm: () => {
            const quotaLimit = document.getElementById('bulkStorageQuotaSelect').value;
            return { quotaLimit, userIds: waitingUsers };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { quotaLimit, userIds } = result.value;
            
            fetch('<?php echo site_url('google_drive/bulk_grant_storage_access'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: `member_ids=${JSON.stringify(userIds)}&quota_limit=${quotaLimit}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'อนุมัติเรียบร้อย!',
                        text: `อนุมัติผู้ใช้งาน ${data.granted_count} คน เรียบร้อยแล้ว`,
                        icon: 'success',
                        confirmButtonText: 'รีเฟรชหน้า'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
            });
        }
    });
}

/**
 * Bulk Actions - อนุมัติแบบหลายคน
 */

/**
 * View User Profile
 */
function viewUserProfile(userId) {
    window.location.href = `<?php echo site_url('google_drive_system/user_usage'); ?>?user_id=${userId}`;
}

/**
 * Manage User Storage - เปิด Modal จัดการ Storage
 */
function manageUserStorage(userId, userName, currentLimit, currentUsed) {
    currentQuotaUserId = userId;
    
    document.getElementById('quotaUserName').textContent = userName;
    document.getElementById('quotaCurrentUsage').textContent = `${(currentUsed / 1048576).toFixed(1)} MB`;
    document.getElementById('quotaCurrentLimit').textContent = `${(currentLimit / 1048576).toFixed(0)} MB`;
    
    const usagePercent = (currentUsed / currentLimit) * 100;
    document.getElementById('quotaProgressBar').style.width = `${Math.min(usagePercent, 100)}%`;
    
    document.getElementById('newQuotaInput').value = Math.round(currentLimit / 1048576);
    
    document.getElementById('storageQuotaModal').classList.remove('hidden');
    
    setTimeout(() => {
        document.getElementById('newQuotaInput').focus();
        document.getElementById('newQuotaInput').select();
    }, 100);
}

/**
 * ปิด Quota Modal
 */
function closeQuotaModal() {
    document.getElementById('storageQuotaModal').classList.add('hidden');
    document.getElementById('quotaWarning').classList.add('hidden');
    currentQuotaUserId = null;
}

/**
 * ตั้งค่า Quick Quota
 */
function setQuickQuota(quotaMB) {
    document.getElementById('newQuotaInput').value = quotaMB === 999999 ? 999999 : quotaMB;
    checkQuotaWarning();
    
    const input = document.getElementById('newQuotaInput');
    if (quotaMB === 999999) {
        input.setAttribute('data-unlimited', 'true');
        input.style.color = '#059669';
        input.style.fontWeight = 'bold';
        document.getElementById('quotaUnit').textContent = 'Unlimited';
    } else {
        input.removeAttribute('data-unlimited');
        input.style.color = '';
        input.style.fontWeight = '';
        document.getElementById('quotaUnit').textContent = 'MB';
    }
}

/**
 * ตรวจสอบคำเตือน Quota
 */
function checkQuotaWarning() {
    const newQuotaMB = parseInt(document.getElementById('newQuotaInput').value) || 0;
    const currentUsageMB = parseFloat(document.getElementById('quotaCurrentUsage').textContent);
    
    const warningDiv = document.getElementById('quotaWarning');
    
    if (newQuotaMB === 999999) {
        warningDiv.classList.add('hidden');
        return;
    }
    
    if (newQuotaMB < currentUsageMB && newQuotaMB > 0) {
        warningDiv.classList.remove('hidden');
    } else {
        warningDiv.classList.add('hidden');
    }
}

/**
 * ยืนยันการอัปเดต Quota
 */
function confirmQuotaUpdate() {
    const newQuotaMB = parseInt(document.getElementById('newQuotaInput').value);
    
    if (!newQuotaMB || newQuotaMB < 1) {
        Swal.fire('ข้อมูลไม่ถูกต้อง', 'กรุณาใส่ขนาด Quota ที่ถูกต้อง (ขั้นต่ำ 1 MB)', 'error');
        return;
    }
    
    if (!currentQuotaUserId) {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบข้อมูลผู้ใช้', 'error');
        return;
    }
    
    let newQuotaBytes;
    if (newQuotaMB === 999999) {
        newQuotaBytes = 999999999999999;
    } else {
        newQuotaBytes = newQuotaMB * 1048576;
    }
    
    Swal.fire({
        title: 'ยืนยันการอัปเดต Storage Quota',
        text: `คุณต้องการเปลี่ยน Storage Quota เป็น ${newQuotaMB === 999999 ? 'Unlimited' : newQuotaMB + ' MB'} หรือไม่?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'อัปเดต',
        cancelButtonText: 'ยกเลิก',
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('user_id', currentQuotaUserId);
            formData.append('new_quota', newQuotaBytes);
            formData.append('new_quota_mb', newQuotaMB);
            formData.append('is_unlimited', newQuotaMB === 999999 ? '1' : '0');
            
            fetch('<?php echo site_url('google_drive_system/update_user_quota'); ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    
                    if (data.success) {
                        Swal.fire('สำเร็จ', data.message, 'success').then(() => {
                            closeQuotaModal();
                            location.reload();
                        });
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', data.message || 'ไม่สามารถอัปเดต Quota ได้', 'error');
                    }
                } catch (parseError) {
                    console.error('JSON Parse Error:', parseError);
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถแปลงข้อมูลจากเซิร์ฟเวอร์ได้', 'error');
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                Swal.fire('เกิดข้อผิดพลาด', `ไม่สามารถติดต่อเซิร์ฟเวอร์ได้: ${error.message}`, 'error');
            });
        }
    });
}

// ===== Bulk Actions Functions =====

/**
 * Bulk Toggle Access
 */
function bulkToggleAccess(enabled) {
    const selectedMembers = Array.from(document.querySelectorAll('.member-checkbox:checked'))
                                .map(cb => cb.value);
    
    if (selectedMembers.length === 0) {
        Swal.fire('กรุณาเลือกผู้ใช้งาน', 'กรุณาเลือกผู้ใช้งานที่ต้องการจัดการ', 'warning');
        return;
    }
    
    const action = enabled ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
    
    Swal.fire({
        title: `ยืนยันการ${action} Google Drive`,
        text: `คุณต้องการ${action} Google Drive สำหรับผู้ใช้งาน ${selectedMembers.length} รายการหรือไม่?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: action,
        cancelButtonText: 'ยกเลิก',
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?php echo site_url('google_drive/bulk_toggle_drive_access'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `member_ids=${JSON.stringify(selectedMembers)}&enabled=${enabled ? 1 : 0}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('สำเร็จ', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
            });
        }
    });
}

/**
 * Show Bulk Selection Mode
 */
function showBulkSelectionMode() {
    bulkSelectionMode = !bulkSelectionMode;
    const checkboxes = document.querySelectorAll('.member-checkbox');
    const selectAllLabel = document.querySelector('label[for="selectAll"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.classList.toggle('hidden', !bulkSelectionMode);
    });
    
    if (bulkSelectionMode) {
        selectAllLabel.textContent = 'เลือกทั้งหมด';
        document.getElementById('selectAll').classList.remove('hidden');
    } else {
        selectAllLabel.textContent = 'เลือก';
        document.getElementById('selectAll').classList.add('hidden');
        checkboxes.forEach(checkbox => checkbox.checked = false);
        document.getElementById('selectAll').checked = false;
    }
}

// ===== Activity Logs Functions =====

/**
 * โหลดกิจกรรมล่าสุด
 */
function loadRecentLogs() {
    const container = document.getElementById('recentActivityLogs');
    
    container.innerHTML = `
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-3xl text-gray-300 mb-3"></i>
            <p>กำลังโหลดกิจกรรมล่าสุด...</p>
        </div>
    `;
    
    const logsUrl = '<?php echo site_url('google_drive/get_recent_logs'); ?>';
    
    fetch(logsUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        cache: 'no-cache'
    })
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            throw new Error('Response is not JSON');
        }
    })
    .then(data => {
        if (data.success && data.data && data.data.logs && data.data.logs.length > 0) {
            container.innerHTML = data.data.logs.map(log => `
                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <i class="${getLogIcon(log.action_type)} text-lg ${getLogIconColor(log.status || 'success')}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">
                                ${log.member_name || 'Unknown'} - ${log.action_type.replace(/_/g, ' ')}
                            </p>
                            <span class="text-xs text-gray-500">
                                ${formatDate(log.created_at)}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-700">${log.action_description || 'No description'}</p>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-history text-3xl text-gray-300 mb-3"></i>
                    <p>ยังไม่มีกิจกรรมล่าสุด</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading recent logs:', error);
        container.innerHTML = `
            <div class="text-center py-8 text-red-500">
                <i class="fas fa-exclamation-triangle text-3xl text-red-300 mb-3"></i>
                <p>ไม่สามารถโหลดกิจกรรมได้</p>
                <p class="text-xs mt-2">${error.message}</p>
                <button onclick="loadRecentLogs()" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    ลองใหม่
                </button>
            </div>
        `;
    });
}

// ===== Helper Functions =====

function getLogIcon(actionType) {
    const icons = {
        'connect': 'fas fa-link',
        'disconnect': 'fas fa-unlink', 
        'create_folder': 'fas fa-folder-plus',
        'delete_folder': 'fas fa-folder-minus',
        'upload': 'fas fa-upload',
        'upload_file': 'fas fa-upload',
        'delete_file': 'fas fa-file-times',
        'grant_permission': 'fas fa-user-plus',
        'grant_access': 'fas fa-user-plus',
        'revoke_permission': 'fas fa-user-minus',
        'revoke_access': 'fas fa-user-minus',
        'sync_files': 'fas fa-sync-alt',
        'setup': 'fas fa-cogs',
        'change_mode': 'fas fa-exchange-alt',
        'error': 'fas fa-exclamation-triangle',
        'toggle_user_access': 'fas fa-toggle-on'
    };
    return icons[actionType] || 'fas fa-info-circle';
}

function getLogIconColor(status) {
    const colors = {
        'success': 'text-green-500',
        'failed': 'text-red-500', 
        'pending': 'text-yellow-500',
        'error': 'text-red-500',
        'warning': 'text-orange-500'
    };
    return colors[status] || 'text-gray-500';
}

function formatDate(dateString) {
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
            return 'Invalid date';
        }
        
        return date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (error) {
        console.error('Date formatting error:', error);
        return dateString;
    }
}

// ===== Other Functions =====

function refreshData() {
    location.reload();
}

function toggleStorageMode() {
    console.log('Toggle storage mode');
}

function batchUpdateFolderNames() {
    Swal.fire({
        title: 'อัปเดตชื่อโฟลเดอร์',
        text: 'กำลังอัปเดตชื่อโฟลเดอร์ทั้งหมดจาก Google Drive...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('<?php echo site_url('google_drive/update_folder_names'); ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(data => {
        Swal.fire({
            title: 'อัปเดตเสร็จสิ้น',
            html: '<div style="max-height: 300px; overflow-y: auto;">' + data + '</div>',
            icon: 'success',
            confirmButtonText: 'รีเฟรชหน้า'
        }).then(() => {
            location.reload();
        });
    })
    .catch(error => {
        Swal.fire({
            title: 'เกิดข้อผิดพลาด',
            text: error.message,
            icon: 'error'
        });
    });
}

function exportMembers() {
    const exportUrl = currentStorageMode === 'centralized' 
        ? '<?php echo site_url('google_drive/export_users'); ?>'
        : '<?php echo site_url('google_drive/export_members'); ?>';
    window.open(exportUrl, '_blank');
}

function forceUpdateStorage() {
    Swal.fire({
        title: 'กำลังอัปเดต Storage...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('<?php echo site_url('google_drive/force_update_storage'); ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('สำเร็จ', data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
    });
}

// User-based Storage Functions  
function viewMemberLogs(memberId) {
    window.open(`<?php echo site_url('google_drive/view_logs'); ?>?member_id=${memberId}`, '_blank');
}

function manageMemberDrive(memberId, memberName) {
    Swal.fire({
        title: `จัดการ Google Drive - ${memberName}`,
        text: 'ฟีเจอร์นี้อยู่ระหว่างการพัฒนา',
        icon: 'info'
    });
}

function disconnectMember(memberId) {
    Swal.fire({
        title: 'ยืนยันการตัดการเชื่อมต่อ',
        text: 'คุณต้องการตัดการเชื่อมต่อ Google Drive ของสมาชิกนี้หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ตัดการเชื่อมต่อ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('สำเร็จ', 'ตัดการเชื่อมต่อเรียบร้อยแล้ว', 'success');
        }
    });
}

// ===== Event Listeners =====

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Google Drive Toggle Switches
    document.querySelectorAll('.google-drive-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const memberId = this.dataset.memberId;
            const memberName = this.dataset.memberName;
            const enabled = this.checked;
            
            toggleGoogleDriveAccess(memberId, memberName, enabled);
        });
    });
    
    // Select All Checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.member-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Bulk Actions Menu Toggle
    const bulkActionsBtn = document.getElementById('bulkActionsBtn');
    if (bulkActionsBtn) {
        bulkActionsBtn.addEventListener('click', function() {
            document.getElementById('bulkActionsMenu').classList.toggle('hidden');
        });
    }
    
    // Close bulk actions menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('bulkActionsMenu');
        const button = document.getElementById('bulkActionsBtn');
        
        if (menu && button && !menu.contains(event.target) && !button.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
    
    // Quota Input Event Listeners
    const quotaInput = document.getElementById('newQuotaInput');
    if (quotaInput) {
        quotaInput.addEventListener('input', function() {
            checkQuotaWarning();
            
            const value = parseInt(this.value);
            if (value === 999999) {
                this.setAttribute('data-unlimited', 'true');
                this.style.color = '#059669';
                this.style.fontWeight = 'bold';
                document.getElementById('quotaUnit').textContent = 'Unlimited';
            } else {
                this.removeAttribute('data-unlimited');
                this.style.color = '';
                this.style.fontWeight = '';
                document.getElementById('quotaUnit').textContent = 'MB';
            }
        });
        
        quotaInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                confirmQuotaUpdate();
            }
        });
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('storageQuotaModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeQuotaModal();
            }
        });
    }
    
    // Search functionality
    const searchInput = document.getElementById('searchMembers');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#membersTable tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const shouldShow = text.includes(searchTerm);
                row.style.display = shouldShow ? '' : 'none';
            });
        });
    }
    
    // โหลดกิจกรรมล่าสุด
    loadRecentLogs();
});

// ===== Keyboard Shortcuts =====
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('storageQuotaModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeQuotaModal();
        }
    }
    
    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        refreshData();
    }
});
</script>

<!-- CSS Styles -->
<style>
/* Toggle Switch Styles */
.google-drive-toggle + div {
    transition: all 0.3s ease;
}

.google-drive-toggle:checked + div {
    background-color: #2563eb !important;
}

.google-drive-toggle:not(:checked) + div {
    background-color: #d1d5db !important;
}

.google-drive-toggle + div::after {
    transition: transform 0.3s ease;
}

.google-drive-toggle:checked + div::after {
    transform: translateX(100%) !important;
}

.google-drive-toggle:not(:checked) + div::after {
    transform: translateX(0) !important;
}

.google-drive-toggle:focus + div {
    ring: 4px;
    ring-color: rgba(59, 130, 246, 0.5);
}

.google-drive-toggle:disabled + div {
    opacity: 0.5;
    cursor: not-allowed;
}

.google-drive-toggle + div {
    width: 2.75rem !important;
    height: 1.5rem !important;
    border-radius: 9999px !important;
    position: relative !important;
}

.google-drive-toggle + div::after {
    content: '' !important;
    position: absolute !important;
    top: 2px !important;
    left: 2px !important;
    background-color: white !important;
    border: 1px solid #d1d5db !important;
    border-radius: 50% !important;
    height: 1.25rem !important;
    width: 1.25rem !important;
}

/* Modal Styles */
#storageQuotaModal {
    backdrop-filter: blur(4px);
    animation: fadeIn 0.2s ease-out;
}

#storageQuotaModal > div {
    animation: slideIn 0.3s ease-out;
    max-height: 90vh;
    overflow-y: auto;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { 
        opacity: 0;
        transform: translateY(-20px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

/* Input Focus Styles */
#newQuotaInput:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Quick Preset Buttons */
#storageQuotaModal button[onclick*="setQuickQuota"] {
    transition: all 0.2s ease;
}

#storageQuotaModal button[onclick*="setQuickQuota"]:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Progress Bar Animation */
#quotaProgressBar {
    transition: width 0.5s ease-in-out;
}

/* Warning Box */
#quotaWarning {
    animation: warningSlide 0.3s ease-out;
}

@keyframes warningSlide {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Bulk Selection */
.bulk-selection-active .member-checkbox {
    display: block !important;
}

/* Search Input Highlight */
#searchMembers:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Table Row Hover Effects */
#membersTable tr:hover {
    background-color: #f9fafb;
    transition: background-color 0.2s ease;
}

/* Action Button Hover Effects */
.flex.space-x-2 button {
    transition: all 0.2s ease;
}

.flex.space-x-2 button:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Loading Spinner */
.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Toggle Status Text */
.toggle-status {
    font-weight: 500;
    transition: color 0.3s ease;
}

/* Waiting User Styles */
tr.opacity-75 {
    background-color: #f9fafb !important;
}

tr.opacity-75:hover {
    background-color: #f3f4f6 !important;
}

/* Badge Pulse Animation */
.inline-flex.items-center.px-2.py-1.rounded-full.text-xs.font-medium.bg-yellow-100.text-yellow-800 {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .ml-72 {
        margin-left: 0;
        padding: 1rem;
    }
    
    .google-drive-toggle + div {
        width: 2.5rem;
        height: 1.25rem;
    }
    
    .google-drive-toggle + div::after {
        width: 1rem;
        height: 1rem;
    }
    
    #storageQuotaModal > div {
        margin: 1rem;
        width: calc(100% - 2rem);
    }
    
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
}

/* Print Styles */
@media print {
    #storageQuotaModal,
    .fixed,
    button,
    .hover\:bg-gray-50 {
        display: none !important;
    }
}

/* Accessibility Improvements */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Focus Visible for Keyboard Navigation */
button:focus-visible,
input:focus-visible {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* High Contrast Mode Support */
@media (prefers-contrast: high) {
    .bg-blue-600 {
        background-color: #1e40af;
    }
    
    .text-blue-600 {
        color: #1e40af;
    }
    
    .border {
        border-width: 2px;
    }
}
</style>