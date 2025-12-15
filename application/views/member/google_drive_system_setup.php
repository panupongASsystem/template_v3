<?php
// application/views/member/google_drive_system_setup.php
?>
<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">⚙️ ตั้งค่า System Storage</h1>
            <p class="text-gray-600 mt-2">กำหนดค่าระบบ Centralized Google Drive Storage</p>
        </div>
        <div class="flex space-x-3">
            
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?php echo $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <!-- Setup Progress -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800">ขั้นตอนการตั้งค่า</h2>
            <p class="text-gray-600 mt-1">ทำตามขั้นตอนเพื่อเริ่มใช้งาน Centralized Storage</p>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Step 1: System Storage -->
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <?php if ($setup_status['has_system_storage']): ?>
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                        <?php else: ?>
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">1</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-800">เชื่อมต่อ Google Account หลัก</h3>
                        <p class="text-gray-600 mt-1">เชื่อมต่อ Google Account ที่จะใช้เป็น Storage กลางของระบบ</p>
                        
                        <?php if ($setup_status['has_system_storage']): ?>
                            <div class="mt-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fab fa-google text-green-600 mr-2"></i>
                                    <span class="text-green-800 font-medium">
                                        <?php echo htmlspecialchars($system_storage->google_account_email); ?>
                                    </span>
                                </div>
                                <p class="text-green-700 text-sm mt-1">เชื่อมต่อเรียบร้อยแล้ว</p>
                            </div>
                        <?php else: ?>
                            <div class="mt-3">
                                <button onclick="connectSystemAccount()" 
                                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fab fa-google mr-2"></i>เชื่อมต่อ Google Account
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Step 2: Folder Structure -->
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <?php if ($setup_status['folder_structure_created']): ?>
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                        <?php elseif ($setup_status['has_system_storage']): ?>
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">2</span>
                            </div>
                        <?php else: ?>
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">2</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-800">สร้างโครงสร้างโฟลเดอร์</h3>
                        <p class="text-gray-600 mt-1">สร้างโฟลเดอร์หลักและโฟลเดอร์ตามแผนกต่างๆ</p>
                        
                        <?php if ($setup_status['folder_structure_created']): ?>
                            <div class="mt-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-sitemap text-green-600 mr-2"></i>
                                    <span class="text-green-800 font-medium">โครงสร้างโฟลเดอร์พร้อมใช้งาน</span>
                                </div>
                                <p class="text-green-700 text-sm mt-1">สร้างเรียบร้อยแล้ว</p>
                            </div>
                        <?php elseif ($setup_status['has_system_storage']): ?>
                            <div class="mt-3">
                                <button onclick="createFolderStructure()" 
                                        id="createFolderBtn"
                                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-folder-plus mr-2"></i>สร้างโครงสร้างโฟลเดอร์
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="mt-3 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <p class="text-gray-600 text-sm">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    กรุณาเชื่อมต่อ Google Account ก่อน
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Step 3: System Ready -->
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <?php if ($setup_status['ready_to_use']): ?>
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                        <?php else: ?>
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">3</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-800">เริ่มใช้งานระบบ</h3>
                        <p class="text-gray-600 mt-1">ระบบพร้อมใช้งาน สามารถให้สิทธิ์ผู้ใช้และจัดการไฟล์ได้</p>
                        
                        <?php if ($setup_status['ready_to_use']): ?>
                            <div class="mt-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center">
                                            <i class="fas fa-rocket text-green-600 mr-2"></i>
                                            <span class="text-green-800 font-medium">ระบบพร้อมใช้งาน</span>
                                        </div>
                                        <p class="text-green-700 text-sm mt-1">สามารถเริ่มใช้งาน Centralized Storage ได้แล้ว</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="<?php echo site_url('google_drive_system/files'); ?>" 
                                           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                            จัดการไฟล์
                                        </a>
                                        
                                        <!-- System Reset Button - Only for System Admin -->
                                        <?php if ($this->session->userdata('m_system') === 'system_admin'): ?>
                                            <button onclick="resetGoogleDriveSystem()" 
                                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                                                <i class="fas fa-trash-alt mr-1"></i>ล้างระบบ
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mt-3 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <p class="text-gray-600 text-sm">
                                    <i class="fas fa-clock mr-1"></i>
                                    รอการตั้งค่าขั้นตอนก่อนหน้าให้เสร็จสิ้น
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current System Storage Info -->
    <?php if ($system_storage): ?>
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800">ข้อมูล System Storage ปัจจุบัน</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Google Account Info -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Google Account</label>
                        <div class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <i class="fab fa-google text-blue-600 mr-3"></i>
                            <span class="text-blue-800 font-medium"><?php echo htmlspecialchars($system_storage->google_account_email); ?></span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Storage Name</label>
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <span class="text-gray-800"><?php echo htmlspecialchars($system_storage->storage_name); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Storage Statistics -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">การใช้งาน Storage</label>
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">ใช้งาน</span>
                                <span class="text-sm font-medium text-gray-800">
                                    <?php echo format_bytes_helper($system_storage->total_storage_used); ?> / 
                                    <?php echo format_bytes_helper($system_storage->max_storage_limit); ?>
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" 
                                     style="width: <?php echo min(100, $system_storage->storage_usage_percent); ?>%"></div>
                            </div>
                            <div class="text-center mt-1">
                                <span class="text-xs text-gray-500"><?php echo $system_storage->storage_usage_percent; ?>%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">สถานะระบบ</label>
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    <span class="text-sm text-green-800">เชื่อมต่อแล้ว</span>
                                </div>
                                <div class="flex items-center">
                                    <?php if ($system_storage->folder_structure_created): ?>
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        <span class="text-sm text-green-800">โครงสร้างพร้อม</span>
                                    <?php else: ?>
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                        <span class="text-sm text-red-800">ยังไม่สร้างโครงสร้าง</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
	
	
	
	
	
	<!-- เพิ่มส่วนนี้หลังจาก Current System Storage Info และก่อน Advanced Settings -->
<?php if ($system_storage && $setup_status['folder_structure_created']): ?>
<!-- User Management Section -->
<div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
    <div class="p-6 border-b border-gray-100">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">👥 จัดการผู้ใช้งาน System Storage</h2>
                <p class="text-gray-600 mt-1">กำหนดสิทธิ์และสร้างโฟลเดอร์ส่วนตัวให้ผู้ใช้</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="refreshUserList()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    <i class="fas fa-sync-alt mr-2"></i>รีเฟรช
                </button>
               <!--  <button onclick="bulkCreatePersonalFolders()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                    <i class="fas fa-folder-plus mr-2"></i>สร้างโฟลเดอร์ทั้งหมด
                </button>  -->
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-600" id="totalUsers">0</div>
                <div class="text-sm text-blue-800">ผู้ใช้ทั้งหมด</div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-600" id="activeUsers">0</div>
                <div class="text-sm text-green-800">เปิดใช้งาน</div>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-purple-600" id="usersWithFolders">0</div>
                <div class="text-sm text-purple-800">มีโฟลเดอร์แล้ว</div>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-orange-600" id="pendingUsers">0</div>
                <div class="text-sm text-orange-800">รอดำเนินการ</div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex space-x-3">
                <div class="relative">
                    <input type="text" 
                           id="searchUsers" 
                           placeholder="ค้นหาผู้ใช้..."
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                <select id="filterByStatus" 
                        onchange="filterUsers()"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">ทั้งหมด</option>
                    <option value="active">เปิดใช้งาน</option>
                    <option value="inactive">ปิดใช้งาน</option>
                    <option value="has_folder">มีโฟลเดอร์แล้ว</option>
                    <option value="no_folder">ยังไม่มีโฟลเดอร์</option>
                </select>
                <select id="filterByPosition" 
                        onchange="filterUsers()"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">ทุกตำแหน่ง</option>
                    <!-- จะโหลดจาก AJAX -->
                </select>
            </div>
            <div class="text-sm text-gray-600">
                แสดง <span id="showingCount">0</span> จาก <span id="totalCount">0</span> คน
            </div>
        </div>

        <!-- User List Table -->
        <div class="overflow-x-auto">
            <div id="userListLoading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
                <p class="text-gray-600 mt-2">กำลังโหลดข้อมูลผู้ใช้...</p>
            </div>
            
            <table id="userTable" class="min-w-full bg-white border border-gray-200 rounded-lg hidden">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ผู้ใช้</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ตำแหน่ง</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personal Folder</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สิทธิ์</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody id="userTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- จะโหลดจาก AJAX -->
                </tbody>
            </table>
            
            <div id="noUsersFound" class="text-center py-8 hidden">
                <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600">ไม่พบผู้ใช้ตามเงื่อนไขที่ค้นหา</p>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div id="bulkActions" class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg hidden">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700">
                        เลือกแล้ว <span id="selectedCount">0</span> คน:
                    </span>
                    <button onclick="bulkToggleStatus(true)" 
                            class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                        เปิดใช้งาน
                    </button>
                    <button onclick="bulkToggleStatus(false)" 
                            class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                        ปิดใช้งาน
                    </button>
                    <button onclick="bulkCreateFolders()" 
                            class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                        สร้างโฟลเดอร์
                    </button>
                    <button onclick="bulkAssignPermissions()" 
                            class="px-3 py-1 bg-purple-600 text-white rounded text-sm hover:bg-purple-700">
                        กำหนดสิทธิ์
                    </button>
                </div>
                <button onclick="clearSelection()" 
                        class="px-3 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700">
                    ยกเลิกการเลือก
                </button>
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="mt-6 flex justify-between items-center">
            <div class="text-sm text-gray-600">
                แสดง <span id="pageStart">0</span>-<span id="pageEnd">0</span> จาก <span id="pageTotal">0</span> รายการ
            </div>
            <div class="flex space-x-2">
                <button onclick="changePage('prev')" 
                        id="prevBtn"
                        class="px-3 py-1 bg-gray-200 text-gray-600 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    ก่อนหน้า
                </button>
                <div id="pageNumbers" class="flex space-x-1">
                    <!-- จะสร้างจาก JavaScript -->
                </div>
                <button onclick="changePage('next')" 
                        id="nextBtn"
                        class="px-3 py-1 bg-gray-200 text-gray-600 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    ถัดไป
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
	
	

    <!-- Advanced Settings -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800">การตั้งค่าขั้นสูง</h2>
            <p class="text-gray-600 mt-1">กำหนดค่าเพิ่มเติมสำหรับ System Storage</p>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Storage Mode Toggle -->
                <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div>
                        <h3 class="font-medium text-blue-800">โหมด Centralized Storage</h3>
                        <p class="text-sm text-blue-600 mt-1">
                            ใช้ Google Drive กลางแทนการให้ User เชื่อมต่อ Drive ส่วนตัว
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               id="centralizedMode" 
                               onchange="toggleStorageMode(this)"
                               <?php echo ($this->config->item('system_storage_mode') === 'centralized') ? 'checked' : ''; ?>
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Auto Create User Folders -->
                <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div>
                        <h3 class="font-medium text-gray-800">สร้างโฟลเดอร์ User อัตโนมัติ</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            สร้างโฟลเดอร์ส่วนตัวให้ User เมื่อได้รับสิทธิ์เข้าใช้งาน
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               id="autoCreateUserFolders" 
                               onchange="toggleSetting(this, 'auto_create_user_folders')"
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                </div>

                <!-- Default User Quota -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h3 class="font-medium text-gray-800 mb-3">Quota เริ่มต้นสำหรับ User</h3>
                    <div class="flex items-center space-x-4">
                        <select id="defaultUserQuota" 
                                onchange="updateSetting('default_user_quota', this.value)"
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="536870912">512 MB</option>
                            <option value="1073741824" selected>1 GB</option>
                            <option value="2147483648">2 GB</option>
                            <option value="5368709120">5 GB</option>
                            <option value="10737418240">10 GB</option>
                        </select>
                        <span class="text-sm text-gray-600">ต่อ User</span>
                    </div>
                </div>

                <!-- System Storage Limit -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h3 class="font-medium text-gray-800 mb-3">ขีดจำกัด System Storage</h3>
                    <div class="flex items-center space-x-4">
                        <select id="systemStorageLimit" 
                                onchange="updateSystemStorageLimit(this.value)"
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="107374182400">100 GB</option>
                            <option value="214748364800">200 GB</option>
                            <option value="536870912000">500 GB</option>
                            <option value="1073741824000">1 TB</option>
                            <option value="unlimited">ไม่จำกัด</option>
                        </select>
                        <span class="text-sm text-gray-600">สำหรับทั้งระบบ</span>
                    </div>
                </div>

                <!-- System Reset Section - Only for System Admin -->
                <?php if ($this->session->userdata('m_system') === 'system_admin'): ?>
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h3 class="font-medium text-red-800 mb-3">
                        <i class="fas fa-exclamation-triangle mr-2"></i>ล้างข้อมูลระบบ (System Admin Only)
                    </h3>
                    <div class="space-y-3">
                        <p class="text-sm text-red-700">
                            🚨 การดำเนินการนี้จะลบข้อมูลทั้งหมดใน Google Drive และ Database ไม่สามารถย้อนกลับได้
                        </p>
                        <div class="bg-red-100 border border-red-300 rounded p-3">
    <h4 class="font-medium text-red-800 text-sm mb-2">สิ่งที่จะถูกลบ:</h4>
    <ul class="text-xs text-red-700 space-y-1">
        <!-- Google Drive Data -->
        <li>• โฟลเดอร์และไฟล์ทั้งหมดใน Google Drive System</li>
        
        <!-- Core Tables -->
        <li>• ข้อมูลใน tbl_google_drive_system_folders</li>
        <li>• ข้อมูลใน tbl_google_drive_folders</li>
        <li>• ข้อมูลใน tbl_google_drive_folder_permissions</li>
        <li>• ข้อมูลใน tbl_google_drive_member_folder_access</li>
        <li>• ข้อมูลใน tbl_google_drive_logs</li>
        <li>• ข้อมูลใน tbl_google_drive_permissions</li>
        <li>• ข้อมูลใน tbl_google_drive_settings</li>
        
        <!-- New Added Tables -->
        <li>• ข้อมูลใน tbl_google_drive_access_requests</li>
        <li>• ข้อมูลใน tbl_google_drive_file_activities</li>
        <li>• ข้อมูลใน tbl_google_drive_folder_access_logs</li>
        <li>• ข้อมูลใน tbl_google_drive_rename_activities</li>
        <li>• ข้อมูลใน tbl_google_drive_sharing_activities</li>
        <li>• ข้อมูลใน tbl_google_drive_storage_usage</li>
        <li>• ข้อมูลใน tbl_google_position_hierarchy</li>
        
        <!-- Activity & Log Tables -->
        <li>• ข้อมูลใน tbl_google_drive_activity_logs</li>
        
        <!-- Deep Clean Only (จะลบเมื่อเลือก Deep Clean) -->
        <li class="text-orange-700 font-medium">📋 Deep Clean เพิ่มเติม:</li>
        <li class="ml-4">• ข้อมูลใน tbl_google_drive_sharing</li>
        <li class="ml-4">• ข้อมูลใน tbl_google_drive_member_permissions</li>
        <li class="ml-4">• ข้อมูลใน tbl_google_drive_folder_hierarchy</li>
        <li class="ml-4">• ข้อมูลใน tbl_google_drive_shared_permissions</li>
        <li class="ml-4">• ข้อมูลใน tbl_google_drive_permission_types</li>
        <li class="ml-4">• ข้อมูลใน tbl_google_drive_folder_templates</li>
        <li class="ml-4">• ข้อมูลใน tbl_google_drive_position_permissions</li>
        
        <!-- System Reset -->
        <li class="text-purple-700 font-medium">🔧 การรีเซ็ตระบบ:</li>
        <li class="ml-4">• รีเซ็ต folder_structure_created = 0</li>
        <li class="ml-4">• ล้าง root_folder_id</li>
        <li class="ml-4">• รีเซ็ต member storage data</li>
        <li class="ml-4">• ล้าง cache และ session</li>
        
        <!-- Important Note -->
        <li class="text-green-700 font-medium mt-2">✅ สิ่งที่ยังคงอยู่:</li>
        <li class="ml-4 text-green-700">• Google Account Token และ Refresh Token</li>
        <li class="ml-4 text-green-700">• การตั้งค่า OAuth Credentials</li>
        <li class="ml-4 text-green-700">• ข้อมูล Member และ Position</li>
    </ul>
</div>
                        <button onclick="showSystemResetConfirmation()" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                            <i class="fas fa-trash-alt mr-2"></i>ล้างข้อมูลระบบทั้งหมด
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="mt-8 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">
            <i class="fas fa-lightbulb mr-2"></i>คำแนะนำการใช้งาน
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-blue-700">
            <div>
                <h4 class="font-medium mb-2">📁 Centralized Storage คืออะไร?</h4>
                <ul class="text-sm space-y-1">
                    <li>• ใช้ Google Drive กลางของระบบ</li>
                    <li>• ไม่ต้องให้ User เชื่อมต่อ Drive ส่วนตัว</li>
                    <li>• จัดการไฟล์และสิทธิ์ได้ง่ายขึ้น</li>
                    <li>• ประหยัดพื้นที่ Storage ของ User</li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-medium mb-2">⚙️ การตั้งค่าที่แนะนำ</h4>
                <ul class="text-sm space-y-1">
                    <li>• เปิดใช้งาน Centralized Mode</li>
                    <li>• ตั้ง User Quota = 1-2 GB</li>
                    <li>• เปิดการสร้างโฟลเดอร์อัตโนมัติ</li>
                    <li>• ติดตามการใช้งานเป็นประจำ</li>
                </ul>
            </div>
        </div>
    </div>
	
	
	
	<!-- Permission Management Modal -->

<!-- แก้ไข Permission Management Modal ให้มี scroll ที่ดีขึ้น -->
<div id="permissionModal" class="fixed inset-0 z-50 hidden modal-overlay">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- ✅ แก้ไข: เพิ่ม max-height และ overflow สำหรับ modal -->
        <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full modal-container">
            
            <!-- Modal Header - Fixed ไม่เลื่อน -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 rounded-t-2xl flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <i class="fas fa-key text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">จัดการสิทธิ์ผู้ใช้งาน</h2>
                            <p class="text-blue-100 text-sm" id="modalUserInfo">กำลังโหลดข้อมูลผู้ใช้...</p>
                        </div>
                    </div>
                    <button onclick="closePermissionModal()" class="text-white hover:text-gray-300 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- ✅ แก้ไข: Modal Content - ใช้ flexbox และ scroll -->
            <div class="modal-body-container">
                
                <!-- Left Sidebar - เลื่อนได้อิสระ -->
                <div class="modal-sidebar">
                    
                    <!-- User Profile Card -->
                    <div class="p-6 bg-white border-b flex-shrink-0">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                <span class="text-white text-xl font-bold" id="userInitial">U</span>
                            </div>
                            <h3 class="font-semibold text-gray-800" id="userName">ชื่อผู้ใช้</h3>
                            <p class="text-sm text-gray-600" id="userEmail">อีเมล</p>
                            <p class="text-xs text-gray-500" id="userPosition">ตำแหน่ง</p>
                        </div>
                        
                        <!-- User Status -->
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">สถานะ Storage:</span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium" id="storageStatus">
                                    <i class="fas fa-circle mr-1"></i>ไม่ทราบ
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">โฟลเดอร์ส่วนตัว:</span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium" id="personalFolderStatus">
                                    <i class="fas fa-circle mr-1"></i>ไม่ทราบ
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="p-4 border-b flex-shrink-0">
                        <h4 class="font-medium text-gray-800 mb-3">การดำเนินการด่วน</h4>
                        <div class="space-y-2">
                            <button onclick="toggleUserStorageQuick()" 
                                    class="w-full px-3 py-2 text-left text-sm bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                <i class="fas fa-toggle-on text-blue-600 mr-2"></i>
                                <span id="quickToggleText">เปิด/ปิด Storage</span>
                            </button>
                            <button onclick="createPersonalFolderQuick()" 
                                    class="w-full px-3 py-2 text-left text-sm bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                                <i class="fas fa-folder-plus text-green-600 mr-2"></i>สร้างโฟลเดอร์ส่วนตัว
                            </button>
                            <button onclick="resetUserPermissions()" 
                                    class="w-full px-3 py-2 text-left text-sm bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                <i class="fas fa-redo text-red-600 mr-2"></i>รีเซ็ตสิทธิ์ทั้งหมด
                            </button>
                        </div>
                    </div>

                    <!-- ✅ แก้ไข: Current Permissions Summary - เลื่อนได้ -->
                    <div class="p-4 flex-1 overflow-y-auto">
                        <h4 class="font-medium text-gray-800 mb-3">สิทธิ์ปัจจุบัน</h4>
                        <div id="currentPermissionsSummary" class="space-y-2 scrollable-content">
                            <div class="text-center text-gray-500 text-sm py-4">
                                <i class="fas fa-spinner fa-spin"></i> กำลังโหลด...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="modal-main-content">
                    
                    <!-- Tab Navigation - Fixed -->
                    <div class="border-b bg-white flex-shrink-0">
                        <nav class="flex space-x-1 px-6">
                            <button onclick="switchTab('folders')" 
                                    class="tab-button px-4 py-3 text-sm font-medium rounded-t-lg border-b-2 border-transparent hover:border-blue-300 active"
                                    data-tab="folders">
                                <i class="fas fa-folder mr-2"></i>สิทธิ์โฟลเดอร์
                            </button>
                            <button onclick="switchTab('system')" 
                                    class="tab-button px-4 py-3 text-sm font-medium rounded-t-lg border-b-2 border-transparent hover:border-blue-300"
                                    data-tab="system">
                                <i class="fas fa-cogs mr-2"></i>สิทธิ์ระบบ
                            </button>
                            <button onclick="switchTab('history')" 
                                    class="tab-button px-4 py-3 text-sm font-medium rounded-t-lg border-b-2 border-transparent hover:border-blue-300"
                                    data-tab="history">
                                <i class="fas fa-history mr-2"></i>ประวัติการเปลี่ยนแปลง
                            </button>
                        </nav>
                    </div>

                    <!-- ✅ แก้ไข: Tab Content - เลื่อนได้ -->
                    <div class="tab-content-container">
                        
                        <!-- Folders Permission Tab -->
                        <div id="foldersTab" class="tab-content tab-scrollable">
                            
                            <!-- Filter and Search - Fixed -->
                            <div class="flex items-center justify-between mb-6 p-6 pb-0 flex-shrink-0">
                                <div class="flex items-center space-x-4">
                                    <div class="relative">
                                        <input type="text" 
                                               id="folderSearch" 
                                               placeholder="ค้นหาโฟลเดอร์..."
                                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <select id="folderTypeFilter" 
                                            onchange="filterFolders()"
                                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="all">ทุกประเภท</option>
                                        <option value="system">โฟลเดอร์ระบบ</option>
                                        <option value="department">โฟลเดอร์แผนก</option>
                                        <option value="shared">โฟลเดอร์แชร์</option>
                                        <option value="personal">โฟลเดอร์ส่วนตัว</option>
                                    </select>
                                </div>
                                <button onclick="grantBulkPermissions()" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>เพิ่มสิทธิ์
                                </button>
                            </div>

                            <!-- ✅ Folder Permissions Grid - เลื่อนได้ -->
                            <div class="px-6 pb-6 flex-1">
                                <div id="folderPermissionsList" class="space-y-4 scrollable-content max-h-96">
                                    <!-- จะโหลดจาก JavaScript -->
                                    <div class="text-center py-8">
                                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent mb-4"></div>
                                        <p class="text-gray-600">กำลังโหลดข้อมูลโฟลเดอร์...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Permission Tab -->
                        <div id="systemTab" class="tab-content tab-scrollable hidden">
                            
                            <!-- ✅ System Permission Categories - เลื่อนได้ -->
                            <div class="p-6 scrollable-content">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    
                                    <!-- Storage Access -->
                                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                            <i class="fas fa-database text-blue-600 mr-2"></i>การเข้าถึง Storage
                                        </h3>
                                        <div class="space-y-4">
											<div class="bg-blue-50 rounded-lg p-3">
                                                <p class="text-xs text-blue-700">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    สิทธิ์จะได้รับจากการตั้งค่าใน tbl_member , storage_access_granted
                                                </p>
                                            </div>
											
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <label class="text-sm font-medium text-gray-700">เปิดใช้งาน Storage</label>
                                                    <p class="text-xs text-gray-500">อนุญาตให้เข้าถึงระบบ Storage</p>
                                                </div>
                                                <label class="permission-switch">
                                                    <input type="checkbox" id="storageAccessToggle" onchange="updateSystemPermission('storage_access')">
                                                    <span class="permission-slider"></span>
                                                </label>
                                            </div>
											<div class="bg-blue-50 rounded-lg p-3">
                                                <p class="text-xs text-blue-700">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    สิทธิ์จะได้รับจากการตั้งค่าใน tbl_google_drive_member_permissions
                                                </p>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <label class="text-sm font-medium text-gray-700">สร้างโฟลเดอร์ได้</label>
                                                    <p class="text-xs text-gray-500">สามารถสร้างโฟลเดอร์ใหม่ได้</p>
                                                </div>
                                                <label class="permission-switch">
                                                    <input type="checkbox" id="createFolderToggle" onchange="updateSystemPermission('can_create_folder')">
                                                    <span class="permission-slider"></span>
                                                </label>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <label class="text-sm font-medium text-gray-700">แชร์ไฟล์ได้</label>
                                                    <p class="text-xs text-gray-500">สามารถแชร์ไฟล์ให้ผู้อื่นได้</p>
                                                </div>
                                                <label class="permission-switch">
                                                    <input type="checkbox" id="shareFileToggle" onchange="updateSystemPermission('can_share')">
                                                    <span class="permission-slider"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quota Management -->
                                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                            <i class="fas fa-chart-pie text-green-600 mr-2"></i>จัดการ Quota
                                        </h3>
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">ขีดจำกัด Storage</label>
                                                <select id="storageQuotaSelect" 
                                                        onchange="updateStorageQuota()"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="536870912">512 MB</option>
                                                    <option value="1073741824">1 GB</option>
                                                    <option value="2147483648">2 GB</option>
                                                    <option value="5368709120">5 GB</option>
                                                    <option value="10737418240">10 GB</option>
                                                    <option value="custom">กำหนดเอง...</option>
                                                </select>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-sm text-gray-600">การใช้งานปัจจุบัน</span>
                                                    <span class="text-sm font-medium" id="currentUsage">0 B / 1 GB</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div id="usageProgressBar" class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Position-Based Permissions -->
                                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                            <i class="fas fa-users-cog text-purple-600 mr-2"></i>สิทธิ์ตามตำแหน่ง
                                        </h3>
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">ใช้สิทธิ์ตามตำแหน่งงาน</span>
                                                <label class="permission-switch">
                                                    <input type="checkbox" id="inheritPositionToggle" onchange="updatePositionInheritance()">
                                                    <span class="permission-slider"></span>
                                                </label>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">เขียนทับสิทธิ์ตำแหน่ง</span>
                                                <label class="permission-switch">
                                                    <input type="checkbox" id="overridePositionToggle" onchange="updatePositionOverride()">
                                                    <span class="permission-slider"></span>
                                                </label>
                                            </div>
                                            <div class="bg-blue-50 rounded-lg p-3">
                                                <p class="text-xs text-blue-700">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    สิทธิ์ตามตำแหน่งจะได้รับจากการตั้งค่าใน tbl_google_drive_position_permissions
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Advanced Settings -->
                                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                            <i class="fas fa-cog text-gray-600 mr-2"></i>การตั้งค่าขั้นสูง
                                        </h3>
                                        <div class="space-y-4">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <label class="text-sm font-medium text-gray-700">ลบไฟล์ได้</label>
                                                    <p class="text-xs text-gray-500">อนุญาตให้ลบไฟล์ในระบบ</p>
                                                </div>
												
												
                                                <label class="permission-switch">
                                                    <input type="checkbox" id="deleteFileToggle" onchange="updateSystemPermission('can_delete')">
                                                    <span class="permission-slider"></span>
                                                </label>
                                            </div>
											<div class="bg-blue-50 rounded-lg p-3">
                                                <p class="text-xs text-blue-700">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    สิทธิ์จะได้รับจากการตั้งค่าใน tbl_google_drive_member_permissions, can_delete
                                                </p>
                                            </div>
											
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">หมายเหตุ</label>
                                                <textarea id="permissionNotes" 
                                                          rows="3" 
                                                          placeholder="หมายเหตุเพิ่มเติมเกี่ยวกับสิทธิ์ผู้ใช้นี้..."
                                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                                            </div>
                                        </div>
                                    </div>  
                                </div>
                            </div>
                        </div>

                        <!-- History Tab -->
                        <div id="historyTab" class="tab-content tab-scrollable hidden">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4 flex-shrink-0">
                                    <h3 class="text-lg font-semibold text-gray-800">ประวัติการเปลี่ยนแปลงสิทธิ์</h3>
                                    <button onclick="exportPermissionHistory()" 
                                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
                                        <i class="fas fa-download mr-2"></i>ส่งออก
                                    </button>
                                </div>
                                
                                <!-- ✅ History Timeline - เลื่อนได้ -->
                                <div id="permissionHistoryList" class="space-y-4 scrollable-content max-h-96">
                                    <!-- จะโหลดจาก JavaScript -->
                                    <div class="text-center py-8">
                                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent mb-4"></div>
                                        <p class="text-gray-600">กำลังโหลดประวัติ...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer - Fixed -->
            <div class="border-t bg-gray-50 px-6 py-4 rounded-b-2xl flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-clock mr-1"></i>
                        อัปเดตล่าสุด: <span id="lastUpdated">-</span>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="saveAllPermissions()" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
                        </button>
                        <button onclick="closePermissionModal()" 
                                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            ปิด
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
	
	
</div>

<script>
// JavaScript Functions for System Setup
	
	let currentUserId = null;
let currentUserData = null;
let hasUnsavedChanges = false;
	

function connectSystemAccount() {
    Swal.fire({
        title: 'เชื่อมต่อ Google Account',
        text: 'คุณต้องการเชื่อมต่อ Google Account สำหรับใช้เป็น Storage กลางหรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'เชื่อมต่อ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#3b82f6'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?php echo site_url('google_drive_system/connect_system_account'); ?>';
        }
    });
}

function createFolderStructure() {
    const btn = document.getElementById('createFolderBtn');
    const originalText = btn.innerHTML;
    
    // แสดง loading
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>กำลังสร้าง...';
    btn.disabled = true;
    
    Swal.fire({
        title: 'กำลังสร้างโครงสร้างโฟลเดอร์',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"></div>
                </div>
                <p class="text-gray-600 mb-2">กำลังสร้างโครงสร้างโฟลเดอร์และกำหนดสิทธิ์...</p>
                <div class="text-sm text-gray-500">
                    <div id="progress-status">🔄 เริ่มต้นการสร้างโฟลเดอร์...</div>
                </div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false,
        width: '500px'
    });

    // เรียก API สร้างโฟลเดอร์พร้อมสิทธิ์
    fetch('<?php echo site_url('google_drive_system/create_folder_structure_with_permissions'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'auto_assign_permissions=1'
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }
        
        return response.json();
    })
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        console.log('✅ Folder creation response:', data);
        
        if (data.success) {
            // แสดงผลลัพธ์การสร้างโฟลเดอร์และสิทธิ์
            Swal.fire({
                icon: 'success',
                title: 'สร้างโครงสร้างเรียบร้อย! 🎉',
                html: generateSuccessMessage(data),
                width: '600px',
                confirmButtonText: 'เสร็จสิ้น',
                showCancelButton: true,
                cancelButtonText: 'ดูรายละเอียด'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    showDetailedReport(data);
                }
            });
        } else {
            throw new Error(data.message || 'ไม่สามารถสร้างโครงสร้างโฟลเดอร์ได้');
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        console.error('❌ Folder creation error:', error);
        
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            html: generateErrorMessage(error.message),
            width: '600px',
            confirmButtonText: 'ตกลง',
            showCancelButton: true,
            cancelButtonText: 'ลองใหม่'
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                createFolderStructure(); // เรียกตัวเองใหม่
            }
        });
    });
}

// New Function: System Reset
function resetGoogleDriveSystem() {
    showSystemResetConfirmation();
}

function showSystemResetConfirmation() {
    Swal.fire({
        title: '🚨 ยืนยันการล้างข้อมูลระบบ',
        html: `
            <div class="text-left">
                <div class="bg-red-100 border border-red-300 rounded-lg p-4 mb-4">
                    <h4 class="font-bold text-red-800 mb-2">⚠️ คำเตือนสำคัญ!</h4>
                    <p class="text-red-700 text-sm mb-3">
                        การดำเนินการนี้จะลบข้อมูลทั้งหมดและ<strong>ไม่สามารถกู้คืนได้</strong>
                    </p>
                    <ul class="text-red-700 text-xs space-y-1">
                        <li>✗ ลบโฟลเดอร์และไฟล์ทั้งหมดใน Google Drive</li>
                        <li>✗ ลบข้อมูลทั้งหมดใน Database</li>
                        <li>✗ ยกเลิกการเชื่อมต่อ Google Account</li>
                        <li>✗ ลบ Log และ Permission ทั้งหมด</li>
                    </ul>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        กรอก <strong>"RESET_ALL_DATA"</strong> เพื่อยืนยัน:
                    </label>
                    <input type="text" 
                           id="resetConfirmText" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                           placeholder="RESET_ALL_DATA">
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-yellow-800 text-sm">
                        💡 <strong>แนะนำ:</strong> Backup ข้อมูลสำคัญก่อนดำเนินการ
                    </p>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ล้างข้อมูลทั้งหมด',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        width: '600px',
        preConfirm: () => {
            const confirmText = document.getElementById('resetConfirmText').value;
            if (confirmText !== 'RESET_ALL_DATA') {
                Swal.showValidationMessage('กรุณากรอก "RESET_ALL_DATA" ให้ถูกต้อง');
                return false;
            }
            return true;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            executeSystemReset();
        }
    });
}

function executeSystemReset() {
    // แสดง progress dialog
    Swal.fire({
        title: '🔄 กำลังล้างข้อมูลระบบ',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-red-600 border-t-transparent"></div>
                </div>
                <div id="reset-progress" class="text-left bg-gray-100 rounded-lg p-4 max-h-64 overflow-y-auto">
                    <div class="text-sm text-gray-600">🔄 เริ่มต้นการล้างข้อมูล...</div>
                </div>
                <div class="mt-3 text-sm text-red-600">
                    ⚠️ กรุณาอย่าปิดหน้านี้จนกว่าจะเสร็จสิ้น
                </div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false,
        width: '600px'
    });

    // เรียก API ล้างข้อมูล
    fetch('<?php echo site_url('google_drive_system/reset_system_data'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'confirm_reset=RESET_ALL_DATA&deep_clean=1'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ System reset response:', data);
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '✅ ล้างข้อมูลเรียบร้อย',
                html: generateResetSuccessMessage(data),
                width: '600px',
                confirmButtonText: 'รีโหลดหน้า',
                allowOutsideClick: false
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'ไม่สามารถล้างข้อมูลได้');
        }
    })
    .catch(error => {
        console.error('❌ System reset error:', error);
        
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            html: `
                <div class="text-left">
                    <p class="text-red-600 mb-4">${escapeHtml(error.message)}</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="font-medium text-yellow-800 mb-2">💡 แนวทางแก้ไข:</h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• ตรวจสอบ Internet Connection</li>
                            <li>• ตรวจสอบ Google Account Permission</li>
                            <li>• ลองใหม่ในอีกครู่</li>
                            <li>• ติดต่อ System Administrator</li>
                        </ul>
                    </div>
                </div>
            `,
            width: '600px',
            confirmButtonText: 'ตกลง',
            showCancelButton: true,
            cancelButtonText: 'ลองใหม่'
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                executeSystemReset();
            }
        });
    });
}

function generateResetSuccessMessage(data) {
    const stats = data.stats || {};
    
    return `
        <div class="text-left">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <h4 class="font-bold text-green-800 mb-3">🎉 ล้างข้อมูลเรียบร้อยแล้ว</h4>
                
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="text-center p-2 bg-white rounded border">
                        <div class="text-xl font-bold text-red-600">${stats.folders_deleted || 0}</div>
                        <div class="text-xs text-gray-600">โฟลเดอร์ที่ลบ</div>
                    </div>
                    <div class="text-center p-2 bg-white rounded border">
                        <div class="text-xl font-bold text-red-600">${stats.files_deleted || 0}</div>
                        <div class="text-xs text-gray-600">ไฟล์ที่ลบ</div>
                    </div>
                    <div class="text-center p-2 bg-white rounded border">
                        <div class="text-xl font-bold text-blue-600">${stats.db_records_deleted || 0}</div>
                        <div class="text-xs text-gray-600">Records ที่ลบ</div>
                    </div>
                    <div class="text-center p-2 bg-white rounded border">
                        <div class="text-xl font-bold text-green-600">${stats.tables_cleared || 0}</div>
                        <div class="text-xs text-gray-600">ตารางที่ล้าง</div>
                    </div>
                </div>
                
                <div class="text-sm text-green-700">
                    <p class="mb-2"><strong>✅ สิ่งที่ดำเนินการเรียบร้อย:</strong></p>
                    <ul class="space-y-1 text-xs">
                        <li>• ลบโฟลเดอร์และไฟล์ทั้งหมดใน Google Drive</li>
                        <li>• ล้างข้อมูลใน Database Tables</li>
                        <li>• ยกเลิกการเชื่อมต่อ Google Account</li>
                        <li>• ลบ Access Token และ Refresh Token</li>
                        <li>• รีเซ็ต System Configuration</li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-blue-800 text-sm">
                    🔄 <strong>ขั้นตอนต่อไป:</strong> สามารถเริ่มตั้งค่าระบบใหม่ได้ทันที
                </p>
            </div>
        </div>
    `;
}
	
	
	/**
 * สร้างข้อความแสดงผลสำเร็จ
 */
function generateSuccessMessage(data) {
    const stats = data.stats || {};
    const folders = stats.folders_created || 0;
    const permissions = stats.permissions_assigned || 0;
    const users = stats.users_processed || 0;
    
    return `
        <div class="text-left">
            <!-- สถิติการสร้าง -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="text-center p-3 bg-green-50 border border-green-200 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">${folders}</div>
                    <div class="text-sm text-green-800">โฟลเดอร์</div>
                </div>
                <div class="text-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">${permissions}</div>
                    <div class="text-sm text-blue-800">สิทธิ์</div>
                </div>
                <div class="text-center p-3 bg-purple-50 border border-purple-200 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">${users}</div>
                    <div class="text-sm text-purple-800">ผู้ใช้</div>
                </div>
            </div>
            
            <!-- รายละเอียดโฟลเดอร์ -->
            <div class="space-y-3">
                <h4 class="font-semibold text-gray-800 mb-3">📁 โฟลเดอร์ที่สร้างและสิทธิ์ที่กำหนด:</h4>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-shield-alt text-red-600 mr-2"></i>
                        <span class="font-medium text-red-800">📁 Admin</span>
                    </div>
                    <ul class="text-sm text-red-700 ml-6 space-y-1">
                        <li>• System Admin และ Super Admin: <strong>ทำได้ทุกอย่าง</strong></li>
                        <li>• ผู้ใช้อื่น: <strong>ไม่มีสิทธิ์</strong></li>
                    </ul>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-building text-yellow-600 mr-2"></i>
                        <span class="font-medium text-yellow-800">📁 Departments</span>
                    </div>
                    <ul class="text-sm text-yellow-700 ml-6 space-y-1">
                        <li>• ทุกคน: <strong>ดูได้อย่างเดียว</strong> (สำหรับเข้าถึงโฟลเดอร์แผนก)</li>
                        <li>• แต่ละแผนก: <strong>สิทธิ์ตามตำแหน่ง (Auto Inherit)</strong></li>
                        <li>• System Admin/Super Admin: <strong>ทำได้ทุกอย่าง</strong></li>
                    </ul>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-share-alt text-green-600 mr-2"></i>
                        <span class="font-medium text-green-800">📁 Shared</span>
                    </div>
                    <ul class="text-sm text-green-700 ml-6 space-y-1">
                        <li>• ทุกคน (ถ้าเปิดสถานะใช้งาน): <strong>แก้ไข/อัปโหลด/ลบได้</strong></li>
                        <li>• System Admin/Super Admin: <strong>ทำได้ทุกอย่าง</strong></li>
                    </ul>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-users text-blue-600 mr-2"></i>
                        <span class="font-medium text-blue-800">📁 Users</span>
                    </div>
                    <ul class="text-sm text-blue-700 ml-6 space-y-1">
                        <li>• ทุกคน: <strong>ดูได้อย่างเดียว</strong> (สำหรับเข้าถึงโฟลเดอร์ส่วนตัว)</li>
                        <li>• โฟลเดอร์ส่วนตัวแต่ละคน: <strong>เจ้าของทำได้ทุกอย่าง</strong></li>
                        <li>• System Admin/Super Admin: <strong>ทำได้ทุกอย่าง</strong></li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span>💡 สิทธิ์เหล่านี้ใช้ระบบ <strong>Auto Inherit</strong> และสามารถปรับแต่งได้ภายหลัง</span>
                </div>
            </div>
        </div>
    `;
}

	
	
	/**
 * สร้างข้อความแสดงข้อผิดพลาด
 */
function generateErrorMessage(errorMessage) {
    return `
        <div class="text-left">
            <p class="mb-4 text-red-600">${escapeHtml(errorMessage)}</p>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-medium text-yellow-800 mb-3">🔧 วิธีแก้ไขที่เป็นไปได้:</h4>
                <ul class="text-sm text-yellow-700 space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-1"></i>
                        <span>ตรวจสอบว่า Google Account มี Token ที่ใช้งานได้</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-1"></i>
                        <span>ตรวจสอบว่า API endpoint <code>create_folder_structure_with_permissions</code> มีอยู่</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-1"></i>
                        <span>ตรวจสอบ database table <code>tbl_member</code> และ <code>tbl_position</code></span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-1"></i>
                        <span>ตรวจสอบ permission tables ที่จำเป็น</span>
                    </li>
                </ul>
            </div>
            
            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="text-sm text-blue-700">
                    <p class="font-medium mb-2">📋 Tables ที่จำเป็น:</p>
                    <ul class="list-disc pl-4 space-y-1">
                        <li><code>tbl_google_drive_system_storage</code></li>
                        <li><code>tbl_google_drive_system_folders</code></li>
                        <li><code>tbl_google_drive_folder_permissions</code></li>
                        <li><code>tbl_member</code></li>
                        <li><code>tbl_position</code></li>
                    </ul>
                </div>
            </div>
        </div>
    `;
}

	
	
	
	function showDetailedReport(data) {
    const details = data.details || {};
    
    Swal.fire({
        title: '📊 รายงานรายละเอียด',
        html: `
            <div class="text-left max-h-96 overflow-y-auto">
                <div class="space-y-4">
                    ${generateFolderDetails(details.folders)}
                    ${generatePermissionDetails(details.permissions)}
                    ${generateErrorDetails(details.errors)}
                </div>
            </div>
        `,
        width: '700px',
        confirmButtonText: 'ปิด',
        customClass: {
            popup: 'text-sm'
        }
    });
}
	
	
	/**
 * สร้างรายละเอียดโฟลเดอร์
 */
function generateFolderDetails(folders) {
    if (!folders || folders.length === 0) {
        return '<div class="text-gray-500">ไม่มีข้อมูลโฟลเดอร์</div>';
    }
    
    let html = '<div class="mb-4"><h4 class="font-semibold mb-2">📁 โฟลเดอร์ที่สร้าง:</h4><ul class="space-y-1">';
    
    folders.forEach(folder => {
        html += `
            <li class="flex items-center text-sm">
                <i class="fas fa-folder text-yellow-500 mr-2"></i>
                <span class="font-medium">${escapeHtml(folder.name)}</span>
                <span class="ml-2 text-gray-500">(${escapeHtml(folder.type)})</span>
            </li>
        `;
    });
    
    html += '</ul></div>';
    return html;
}

/**
 * สร้างรายละเอียดสิทธิ์
 */
function generatePermissionDetails(permissions) {
    if (!permissions || permissions.length === 0) {
        return '<div class="text-gray-500">ไม่มีข้อมูลสิทธิ์</div>';
    }
    
    let html = '<div class="mb-4"><h4 class="font-semibold mb-2">🔐 สิทธิ์ที่กำหนด:</h4><ul class="space-y-1">';
    
    permissions.forEach(perm => {
        html += `
            <li class="flex items-center justify-between text-sm">
                <div class="flex items-center">
                    <i class="fas fa-user text-blue-500 mr-2"></i>
                    <span>${escapeHtml(perm.user_name)}</span>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-500 mr-2">→</span>
                    <span class="font-medium">${escapeHtml(perm.folder_name)}</span>
                    <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                        ${escapeHtml(perm.access_type)}
                    </span>
                </div>
            </li>
        `;
    });
    
    html += '</ul></div>';
    return html;
}

/**
 * สร้างรายละเอียดข้อผิดพลาด
 */
function generateErrorDetails(errors) {
    if (!errors || errors.length === 0) {
        return '<div class="text-green-600">✅ ไม่มีข้อผิดพลาด</div>';
    }
    
    let html = '<div class="mb-4"><h4 class="font-semibold mb-2 text-red-600">⚠️ ข้อผิดพลาด:</h4><ul class="space-y-1">';
    
    errors.forEach(error => {
        html += `
            <li class="flex items-start text-sm text-red-600">
                <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                <span>${escapeHtml(error.message)}</span>
            </li>
        `;
    });
    
    html += '</ul></div>';
    return html;
}
	
	
	/**
 * Escape HTML สำหรับความปลอดภัย
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Progress update function (เรียกจาก backend ถ้าต้องการ)
function updateProgress(message) {
    const statusElement = document.getElementById('progress-status');
    if (statusElement) {
        statusElement.textContent = message;
    }
}
	
function toggleStorageMode(checkbox, force = false) {
    if (!force && !checkbox.checked) {
        // ถ้าปิด Centralized Mode
        Swal.fire({
            title: 'ยืนยันการเปลี่ยนแปลง',
            text: 'คุณต้องการเปลี่ยนกลับเป็น User-based Storage หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'เปลี่ยน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (!result.isConfirmed) {
                checkbox.checked = true;
                return;
            }
            updateStorageMode('user_based');
        });
    } else {
        updateStorageMode('centralized');
    }
}

function updateStorageMode(mode) {
    fetch('<?php echo site_url('google_drive/toggle_storage_mode'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `mode=${encodeURIComponent(mode)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'เปลี่ยนโหมดเรียบร้อย',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: data.message || 'ไม่สามารถอัปเดตการตั้งค่าได้'
            });
        }
    })
    .catch(error => {
        console.error('Toggle setting error:', error);
        // คืนค่า checkbox กลับ
        checkbox.checked = !checkbox.checked;
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ หรือยังไม่ได้ตั้งค่าระบบ'
        });
    });
}

function updateSetting(settingKey, value) {
    setSetting(settingKey, value)
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'อัปเดตการตั้งค่าเรียบร้อย',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'การตั้งค่าไม่สมบูรณ์',
                text: data.message || 'การตั้งค่าอาจไม่ได้รับการบันทึก',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    })
    .catch(error => {
        console.error('Update setting error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถอัปเดตการตั้งค่าได้',
            toast: true,
            position: 'top-end',
            timer: 3000
        });
    });
}

function updateSystemStorageLimit(value) {
    updateSetting('system_storage_limit', value);
}

// Helper function for formatting bytes
function formatBytes(bytes, precision = 2) {
    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
    let i = 0;
    
    while (bytes >= 1024 && i < units.length - 1) {
        bytes /= 1024;
        i++;
    }
    
    return bytes.toFixed(precision) + ' ' + units[i];
}

	
	function formatDate(dateString) {
    if (!dateString) return '-';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
	
	
// Load current settings on page load
document.addEventListener('DOMContentLoaded', function() {
    // โหลดการตั้งค่าปัจจุบัน
    loadCurrentSettings();
});

function loadCurrentSettings() {
    // โหลดสถานะ toggle ต่างๆ
    const settings = [
        'auto_create_user_folders',
        'default_user_quota',
        'system_storage_limit',
        'system_storage_mode'
    ];
    
    settings.forEach(settingKey => {
        fetch('<?php echo site_url('google_drive/get_setting_ajax'); ?>?' + 
              `setting_key=${encodeURIComponent(settingKey)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateSettingUI(settingKey, data.data.value);
            } else {
                console.log(`Setting ${settingKey} not found or disabled`);
            }
        })
        .catch(error => {
            console.log(`Could not load ${settingKey}:`, error.message);
            // ไม่แสดง error ให้ user เห็น เพราะอาจเป็นการตั้งค่าที่ยังไม่มี
        });
    });
}
	
function updateSettingUI(settingKey, value) {
    switch (settingKey) {
        case 'auto_create_user_folders':
            const autoCreateCheckbox = document.getElementById('autoCreateUserFolders');
            if (autoCreateCheckbox) {
                autoCreateCheckbox.checked = (value === '1' || value === 'true');
            }
            break;
            
        case 'default_user_quota':
            const quotaSelect = document.getElementById('defaultUserQuota');
            if (quotaSelect && value) {
                quotaSelect.value = value;
            }
            break;
            
        case 'system_storage_limit':
            const limitSelect = document.getElementById('systemStorageLimit');
            if (limitSelect && value) {
                limitSelect.value = value;
            }
            break;
            
        case 'system_storage_mode':
            const modeCheckbox = document.getElementById('centralizedMode');
            if (modeCheckbox) {
                modeCheckbox.checked = (value === 'centralized');
            }
            break;
    }
}

// เพิ่ม function set_setting สำหรับ updateSetting()
function setSetting(settingKey, value) {
    return fetch('<?php echo site_url('google_drive/set_setting_ajax'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: `setting_key=${encodeURIComponent(settingKey)}&value=${encodeURIComponent(value)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    });
}
</script>





<script>
// User Management JavaScript
let allUsers = [];
let filteredUsers = [];
let currentPage = 1;
const usersPerPage = 30;
let selectedUsers = new Set();

// โหลดข้อมูลผู้ใช้เมื่อเริ่มต้น
 document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('userTable')) {
        loadUserList();
        loadPositionFilter();
    }
});

/**
 * โหลดรายการผู้ใช้
 */
function loadUserList() {
    showLoading(true);
    
    fetch('<?php echo site_url('google_drive_system/get_all_users_for_management'); ?>', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            allUsers = data.data.users || [];
            filteredUsers = [...allUsers];
            updateSummaryStats(data.data.stats);
            renderUserTable();
            showLoading(false);
        } else {
            throw new Error(data.message || 'ไม่สามารถโหลดข้อมูลผู้ใช้ได้');
        }
    })
    .catch(error => {
        console.error('Load users error:', error);
        showLoading(false);
        showError('ไม่สามารถโหลดข้อมูลผู้ใช้ได้: ' + error.message);
    });
}

/**
 * โหลด filter ตำแหน่ง
 */
function loadPositionFilter() {
    fetch('<?php echo site_url('google_drive_system/get_positions_for_filter'); ?>', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('filterByPosition');
            data.data.forEach(position => {
                const option = document.createElement('option');
                option.value = position.pid;
                option.textContent = position.pname;
                select.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.log('Position filter load error:', error);
    });
}

/**
 * อัปเดตสถิติสรุป
 */
function updateSummaryStats(stats) {
    document.getElementById('totalUsers').textContent = stats.total_users || 0;
    document.getElementById('activeUsers').textContent = stats.active_users || 0;
    document.getElementById('usersWithFolders').textContent = stats.users_with_folders || 0;
    document.getElementById('pendingUsers').textContent = stats.pending_users || 0;
}

/**
 * แสดง/ซ่อน loading
 */
function showLoading(show) {
    const loading = document.getElementById('userListLoading');
    const table = document.getElementById('userTable');
    const noData = document.getElementById('noUsersFound');
    
    if (show) {
        loading.classList.remove('hidden');
        table.classList.add('hidden');
        noData.classList.add('hidden');
    } else {
        loading.classList.add('hidden');
        table.classList.remove('hidden');
    }
}

/**
 * แสดงข้อผิดพลาด
 */
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: message,
        confirmButtonText: 'ตกลง'
    });
}

/**
 * สร้างตาราง User
 */
function renderUserTable() {
    const tbody = document.getElementById('userTableBody');
    const start = (currentPage - 1) * usersPerPage;
    const end = start + usersPerPage;
    const pageUsers = filteredUsers.slice(start, end);
    
    if (pageUsers.length === 0) {
        document.getElementById('userTable').classList.add('hidden');
        document.getElementById('noUsersFound').classList.remove('hidden');
        return;
    }
    
    document.getElementById('userTable').classList.remove('hidden');
    document.getElementById('noUsersFound').classList.add('hidden');
    
    tbody.innerHTML = '';
    
    pageUsers.forEach(user => {
        const row = createUserRow(user);
        tbody.appendChild(row);
    });
    
    updatePagination();
    updateCounts();
}


	
	
	/**
 * ✅ แก้ไขฟังก์ชัน createUserRow - ส่วนแสดงสิทธิ์
 */
function createUserRow(user) {
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-gray-50';
    
    const isSelected = selectedUsers.has(user.m_id);
    const hasFolder = user.personal_folder_id && user.personal_folder_id.trim() !== '';
    const isActive = user.storage_access_granted == 1;
    
    tr.innerHTML = `
        <td class="px-4 py-3">
            <input type="checkbox" 
                   value="${user.m_id}" 
                   ${isSelected ? 'checked' : ''}
                   onchange="toggleUserSelection(${user.m_id})"
                   class="rounded user-checkbox">
        </td>
        <td class="px-4 py-3">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-8 w-8">
                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                        <span class="text-white text-sm font-medium">
                            ${user.m_fname ? user.m_fname.charAt(0).toUpperCase() : 'U'}
                        </span>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">
                        ${escapeHtml(user.full_name || 'ไม่ระบุชื่อ')}
                    </div>
                    <div class="text-sm text-gray-500">
                        ${escapeHtml(user.m_email || '')}
                    </div>
                </div>
            </div>
        </td>
        <td class="px-4 py-3">
            <div class="text-sm text-gray-900">${escapeHtml(user.position_name || 'ไม่ระบุ')}</div>
            <div class="text-sm text-gray-500">${escapeHtml(user.pdepartment || '')}</div>
        </td>
        <td class="px-4 py-3">
            <label class="toggle-switch storage-toggle">
                <input type="checkbox" 
                       ${isActive ? 'checked' : ''}
                       onchange="toggleUserStatus(${user.m_id}, this.checked)">
                <span class="toggle-slider"></span>
            </label>
            <div class="text-xs text-gray-500 mt-1">
                ${isActive ? 'เปิดใช้งาน' : 'ปิดใช้งาน'}
            </div>
        </td>
        <td class="px-4 py-3">
            <div class="flex items-center space-x-2">
                ${hasFolder ? 
                    `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-folder mr-1"></i>มีแล้ว
                    </span>` : 
                    `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-folder-open mr-1"></i>ยังไม่มี
                    </span>`
                }
                ${!hasFolder && isActive ? 
                    `<button onclick="createPersonalFolder(${user.m_id})" 
                             class="text-blue-600 hover:text-blue-800 text-xs">
                        สร้าง
                    </button>` : ''
                }
            </div>
        </td>
        <td class="px-4 py-3">
            <!-- ✅ แก้ไขส่วนแสดงสิทธิ์ -->
            <div class="text-sm">
                ${renderUserPermissions(user)}
            </div>
        </td>
        <td class="px-4 py-3">
            <div class="flex space-x-2">
                <button onclick="manageUserPermissions(${user.m_id})" 
                        class="text-purple-600 hover:text-purple-800 text-sm"
                        title="จัดการสิทธิ์">
                    <i class="fas fa-key"></i>
                </button>
                <button onclick="viewUserDetails(${user.m_id})" 
                        class="text-blue-600 hover:text-blue-800 text-sm"
                        title="ดูรายละเอียด">
                    <i class="fas fa-eye"></i>
                </button>
                ${hasFolder ? 
                    `<button onclick="openUserFolder('${user.personal_folder_id}')" 
                             class="text-green-600 hover:text-green-800 text-sm"
                             title="เปิดโฟลเดอร์">
                        <i class="fas fa-external-link-alt"></i>
                    </button>` : ''
                }
            </div>
        </td>
    `;
    
    return tr;
}

/**
 * ✅ ฟังก์ชันใหม่: แสดงสิทธิ์ของผู้ใช้พร้อมชื่อโฟลเดอร์
 */
function renderUserPermissions(user) {
    try {
        // ✅ 1. ตรวจสอบข้อมูลสิทธิ์จากหลายแหล่ง
        let permissions = [];
        
        // 🔍 A. สิทธิ์จาก folder permissions
        if (user.folder_permissions && Array.isArray(user.folder_permissions)) {
            permissions = user.folder_permissions;
        }
        // 🔍 B. สิทธิ์จาก permissions field
        else if (user.permissions && Array.isArray(user.permissions)) {
            permissions = user.permissions;
        }
        // 🔍 C. สิทธิ์จาก member_folder_access
        else if (user.member_folder_access && Array.isArray(user.member_folder_access)) {
            permissions = user.member_folder_access;
        }
        
        // ✅ 2. ถ้าไม่มีสิทธิ์ แต่มี storage access
        if (permissions.length === 0 && user.storage_access_granted == 1) {
            return `
                <div class="flex flex-col space-y-1">
                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                        <i class="fas fa-database mr-1"></i>Storage Access
                    </span>
                    <span class="text-gray-400 text-xs">ยังไม่มีสิทธิ์โฟลเดอร์</span>
                </div>
            `;
        }
        
        // ✅ 3. ถ้าไม่มีสิทธิ์เลย
        if (permissions.length === 0) {
            return `
                <div class="flex items-center">
                    <i class="fas fa-lock text-gray-400 mr-1"></i>
                    <span class="text-gray-500 text-xs">ไม่มีสิทธิ์</span>
                </div>
            `;
        }
        
        // ✅ 4. จัดกลุ่มสิทธิ์และแสดงชื่อโฟลเดอร์
        const uniquePermissions = getUniquePermissions(permissions);
        const totalPermissions = uniquePermissions.length;
        
        // ถ้ามีสิทธิ์น้อย แสดงแต่ละโฟลเดอร์
        if (totalPermissions <= 3) {
            const permissionItems = uniquePermissions.map(permission => {
                const { label, colorClass, folderName } = getPermissionStyle(permission);
                
                return `
                    <div class="flex items-center space-x-1 mb-1">
                        <span class="inline-block px-2 py-1 ${colorClass} rounded-full text-xs" 
                              title="${getPermissionTooltip(permission)}">
                            ${label}
                        </span>
                        <span class="text-xs text-gray-600 truncate max-w-24" 
                              title="${folderName}">
                            ${folderName}
                        </span>
                    </div>
                `;
            }).join('');
            
            return `
                <div class="flex flex-col space-y-1">
                    ${permissionItems}
                </div>
            `;
        }
        
        // ถ้ามีสิทธิ์เยอะ แสดงสรุป
        else {
            const permissionSummary = getSummaryByType(uniquePermissions);
            const hasAdminPermission = uniquePermissions.some(p => 
                (p.access_type && p.access_type === 'admin') || 
                (p.access_level && p.access_level === 'admin')
            );
            
            return `
                <div class="flex flex-col space-y-1">
                    <div class="flex flex-wrap items-center gap-1">
                        ${permissionSummary.map(summary => 
                            `<span class="inline-block px-2 py-1 ${summary.colorClass} rounded-full text-xs" 
                                   title="${summary.tooltip}">
                                ${summary.label}
                            </span>`
                        ).join('')}
                    </div>
                    <div class="text-xs text-gray-500">
                        ${totalPermissions} โฟลเดอร์${hasAdminPermission ? ' (รวม Admin)' : ''}
                    </div>
                </div>
            `;
        }
        
    } catch (error) {
        console.error('renderUserPermissions error:', error);
        return `
            <span class="text-red-500 text-xs">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                ข้อผิดพลาด
            </span>
        `;
    }
}

/**
 * ✅ ฟังก์ชันช่วย: กรองสิทธิ์ที่ซ้ำกัน
 */
function getUniquePermissions(permissions) {
    try {
        const seen = new Set();
        const unique = [];
        
        permissions.forEach(permission => {
            if (!permission) return;
            
            // สร้าง key สำหรับเช็คความซ้ำ
            let key = '';
            
            if (permission.folder_id) {
                key = `${permission.folder_id}_${permission.access_type || permission.access_level || 'read'}`;
            } else if (permission.folder_name) {
                key = `${permission.folder_name}_${permission.access_type || permission.access_level || 'read'}`;
            } else {
                key = `unknown_${Date.now()}_${Math.random()}`;
            }
            
            if (!seen.has(key)) {
                seen.add(key);
                unique.push(permission);
            }
        });
        
        return unique;
        
    } catch (error) {
        console.error('getUniquePermissions error:', error);
        return permissions || [];
    }
}

/**
 * ✅ ฟังก์ชันช่วย: กำหนดสีและข้อความของ permission badge พร้อมชื่อโฟลเดอร์
 */
function getPermissionStyle(permission) {
    try {
        // ดึงประเภทสิทธิ์
        const accessType = permission.access_type || permission.access_level || permission.permission_level || 'read';
        const folderType = permission.folder_type || 'unknown';
        
        // ดึงชื่อโฟลเดอร์
        let folderName = permission.folder_name || permission.name || 'ไม่ระบุชื่อ';
        
        // ลดขนาดชื่อโฟลเดอร์ถ้ายาวเกินไป
        if (folderName.length > 15) {
            folderName = folderName.substring(0, 12) + '...';
        }
        
        // กำหนดสีตามประเภทสิทธิ์
        let colorClass = '';
        let label = '';
        
        switch (accessType.toLowerCase()) {
            case 'admin':
            case 'owner':
                colorClass = 'bg-red-100 text-red-800';
                label = 'Admin';
                break;
            case 'write':
            case 'writer':
            case 'read_write':
                colorClass = 'bg-green-100 text-green-800';
                label = 'Write';
                break;
            case 'read':
            case 'reader':
            case 'read_only':
                colorClass = 'bg-blue-100 text-blue-800';
                label = 'Read';
                break;
            case 'commenter':
                colorClass = 'bg-yellow-100 text-yellow-800';
                label = 'Comment';
                break;
            default:
                colorClass = 'bg-gray-100 text-gray-800';
                label = accessType || 'Unknown';
        }
        
        // เพิ่มไอคอนตามประเภทโฟลเดอร์
        let icon = '';
        switch (folderType) {
            case 'system':
                icon = '🔧';
                break;
            case 'department':
                icon = '🏢';
                break;
            case 'shared':
                icon = '🤝';
                break;
            case 'personal':
                icon = '👤';
                break;
            default:
                icon = '📁';
        }
        
        return {
            label: `${icon} ${label}`,
            colorClass: colorClass,
            folderName: folderName,
            icon: icon
        };
        
    } catch (error) {
        console.error('getPermissionStyle error:', error);
        return {
            label: 'Error',
            colorClass: 'bg-red-100 text-red-800',
            folderName: 'ข้อผิดพลาด',
            icon: '❌'
        };
    }
}

/**
 * ✅ ฟังก์ชันช่วย: สร้าง tooltip สำหรับ permission
 */
function getPermissionTooltip(permission) {
    try {
        const folderName = permission.folder_name || 'ไม่ระบุชื่อ';
        const accessType = permission.access_type || permission.access_level || 'read';
        const grantedBy = permission.granted_by_name || 'ระบบ';
        const grantedAt = permission.granted_at ? formatDate(permission.granted_at) : 'ไม่ทราบ';
        
        return `โฟลเดอร์: ${folderName}\nสิทธิ์: ${accessType}\nให้โดย: ${grantedBy}\nเมื่อ: ${grantedAt}`;
        
    } catch (error) {
        console.error('getPermissionTooltip error:', error);
        return 'ข้อมูลสิทธิ์';
    }
}

/**
 * ✅ ฟังก์ชันใหม่: สรุปสิทธิ์ตามประเภท (สำหรับกรณีมีสิทธิ์เยอะ)
 */
function getSummaryByType(permissions) {
    try {
        const summary = {};
        
        permissions.forEach(permission => {
            const accessType = permission.access_type || permission.access_level || 'read';
            const folderType = permission.folder_type || 'unknown';
            const folderName = permission.folder_name || permission.name || 'ไม่ระบุชื่อ';
            
            const key = `${accessType}_${folderType}`;
            
            if (!summary[key]) {
                summary[key] = {
                    accessType: accessType,
                    folderType: folderType,
                    count: 0,
                    folders: []
                };
            }
            
            summary[key].count++;
            summary[key].folders.push(folderName);
        });
        
        // แปลงเป็น array และจัดเรียง
        return Object.values(summary).map(item => {
            const { label, colorClass, icon } = getPermissionStyleSimple(item.accessType, item.folderType);
            
            return {
                label: `${icon} ${label} (${item.count})`,
                colorClass: colorClass,
                tooltip: `${label} ใน ${item.count} โฟลเดอร์:\n${item.folders.slice(0, 5).join(', ')}${item.folders.length > 5 ? `\n...และอีก ${item.folders.length - 5} โฟลเดอร์` : ''}`
            };
        }).sort((a, b) => {
            // เรียงตาม priority: Admin > Write > Read
            const priority = { admin: 3, write: 2, read: 1 };
            const aType = a.label.toLowerCase().includes('admin') ? 'admin' : 
                         a.label.toLowerCase().includes('write') ? 'write' : 'read';
            const bType = b.label.toLowerCase().includes('admin') ? 'admin' : 
                         b.label.toLowerCase().includes('write') ? 'write' : 'read';
            
            return (priority[bType] || 0) - (priority[aType] || 0);
        });
        
    } catch (error) {
        console.error('getSummaryByType error:', error);
        return [];
    }
}

/**
 * ✅ ฟังก์ชันช่วย: สไตล์แบบง่ายสำหรับสรุป
 */
function getPermissionStyleSimple(accessType, folderType) {
    let colorClass = '';
    let label = '';
    
    switch (accessType.toLowerCase()) {
        case 'admin':
        case 'owner':
            colorClass = 'bg-red-100 text-red-800';
            label = 'Admin';
            break;
        case 'write':
        case 'writer':
        case 'read_write':
            colorClass = 'bg-green-100 text-green-800';
            label = 'Write';
            break;
        case 'read':
        case 'reader':
        case 'read_only':
            colorClass = 'bg-blue-100 text-blue-800';
            label = 'Read';
            break;
        case 'commenter':
            colorClass = 'bg-yellow-100 text-yellow-800';
            label = 'Comment';
            break;
        default:
            colorClass = 'bg-gray-100 text-gray-800';
            label = accessType || 'Unknown';
    }
    
    let icon = '';
    switch (folderType) {
        case 'system':
            icon = '🔧';
            break;
        case 'department':
            icon = '🏢';
            break;
        case 'shared':
            icon = '🤝';
            break;
        case 'personal':
            icon = '👤';
            break;
        default:
            icon = '📁';
    }
    
    return { label, colorClass, icon };
}

/**
 * ✅ ฟังก์ชันใหม่: แสดงรายละเอียดสิทธิ์ทั้งหมดของผู้ใช้
 */
function showUserPermissionDetails(userId) {
    try {
        // หาข้อมูลผู้ใช้
        const user = allUsers.find(u => u.m_id == userId);
        if (!user) {
            showToast('ไม่พบข้อมูลผู้ใช้', 'error');
            return;
        }
        
        // รวบรวมข้อมูลสิทธิ์
        let permissions = [];
        if (user.folder_permissions && Array.isArray(user.folder_permissions)) {
            permissions = user.folder_permissions;
        } else if (user.permissions && Array.isArray(user.permissions)) {
            permissions = user.permissions;
        } else if (user.member_folder_access && Array.isArray(user.member_folder_access)) {
            permissions = user.member_folder_access;
        }
        
        if (permissions.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'ไม่มีสิทธิ์โฟลเดอร์',
                text: `${user.full_name} ยังไม่มีสิทธิ์เข้าถึงโฟลเดอร์ใดๆ`,
                confirmButtonText: 'ตกลง'
            });
            return;
        }
        
        // สร้าง HTML สำหรับแสดงรายละเอียด
        const permissionsList = getUniquePermissions(permissions).map(permission => {
            const { label, colorClass, folderName, icon } = getPermissionStyle(permission);
            const grantedBy = permission.granted_by_name || 'ระบบ';
            const grantedAt = permission.granted_at ? formatDate(permission.granted_at) : 'ไม่ทราบ';
            const accessType = permission.access_type || permission.access_level || 'read';
            
            return `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">${icon}</span>
                        <div>
                            <div class="font-medium text-gray-800">${escapeHtml(permission.folder_name || 'ไม่ระบุชื่อ')}</div>
                            <div class="text-sm text-gray-600">
                                ให้สิทธิ์โดย: ${escapeHtml(grantedBy)} • ${grantedAt}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-block px-3 py-1 ${colorClass} rounded-full text-sm font-medium">
                            ${accessType.toUpperCase()}
                        </span>
                        ${permission.folder_id ? 
                            `<button onclick="openUserFolder('${permission.folder_id}')" 
                                     class="text-blue-600 hover:text-blue-800 text-sm"
                                     title="เปิดโฟลเดอร์">
                                <i class="fas fa-external-link-alt"></i>
                            </button>` : ''
                        }
                    </div>
                </div>
            `;
        }).join('');
        
        // แสดง Modal
        Swal.fire({
            title: `สิทธิ์โฟลเดอร์ของ ${user.full_name}`,
            html: `
                <div class="text-left">
                    <div class="mb-4 text-center">
                        <div class="inline-flex items-center px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                            <i class="fas fa-folder-open text-blue-600 mr-2"></i>
                            <span class="text-blue-800 font-medium">มีสิทธิ์ทั้งหมด ${permissions.length} โฟลเดอร์</span>
                        </div>
                    </div>
                    
                    <div class="max-h-96 overflow-y-auto space-y-3">
                        ${permissionsList}
                    </div>
                    
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            คลิกปุ่ม <i class="fas fa-external-link-alt"></i> เพื่อเปิดโฟลเดอร์ใน Google Drive
                        </p>
                    </div>
                </div>
            `,
            width: '600px',
            confirmButtonText: 'ปิด',
            showCancelButton: true,
            cancelButtonText: 'จัดการสิทธิ์',
            cancelButtonColor: '#3b82f6'
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                manageUserPermissions(userId);
            }
        });
        
    } catch (error) {
        console.error('showUserPermissionDetails error:', error);
        showToast('เกิดข้อผิดพลาดในการแสดงรายละเอียด', 'error');
    }
}


	
	
	function setRowSuccess(userId, duration = 5000) {
    try {
        const checkbox = document.querySelector(`input.user-checkbox[value="${userId}"]`);
        if (!checkbox) {
            console.warn(`No checkbox found for user ${userId}`);
            return;
        }
        
        const row = checkbox.closest('tr');
        if (!row) {
            console.warn(`No row found for user ${userId}`);
            return;
        }
        
        row.classList.add('row-success');
        row.classList.remove('row-processing');
        
        // ลบ success state หลังจากเวลาที่กำหนด
        setTimeout(() => {
            if (row && row.classList) {
                row.classList.remove('row-success');
            }
        }, duration);
    } catch (error) {
        console.error('setRowSuccess error:', error);
    }
}

	
	function setToggleCreating(userId, isCreating) {
    try {
        const toggleElement = document.querySelector(`input[onchange*="${userId}"]`);
        if (!toggleElement) {
            console.warn(`No toggle element found for user ${userId}`);
            return;
        }
        
        const toggleSwitch = toggleElement.closest('.toggle-switch');
        if (!toggleSwitch) {
            console.warn(`No toggle-switch found for user ${userId}`);
            return;
        }
        
        if (isCreating) {
            toggleSwitch.classList.add('creating');
            toggleSwitch.classList.remove('success');
        } else {
            toggleSwitch.classList.remove('creating');
        }
    } catch (error) {
        console.error('setToggleCreating error:', error);
    }
}

function setToggleSuccess(userId, duration = 3000) {
    try {
        const toggleElement = document.querySelector(`input[onchange*="${userId}"]`);
        if (!toggleElement) {
            console.warn(`No toggle element found for user ${userId}`);
            return;
        }
        
        const toggleSwitch = toggleElement.closest('.toggle-switch');
        if (!toggleSwitch) {
            console.warn(`No toggle-switch found for user ${userId}`);
            return;
        }
        
        toggleSwitch.classList.add('success');
        toggleSwitch.classList.remove('creating');
        
        setTimeout(() => {
            if (toggleSwitch && toggleSwitch.classList) {
                toggleSwitch.classList.remove('success');
            }
        }, duration);
    } catch (error) {
        console.error('setToggleSuccess error:', error);
    }
}

	

function setToggleLoading(userId, isLoading) {
    try {
        const toggles = document.querySelectorAll(`input[onchange*="${userId}"]`);
        
        if (!toggles || toggles.length === 0) {
            console.warn(`No toggles found for user ${userId}`);
            return;
        }
        
        toggles.forEach(toggle => {
            if (!toggle) return;
            
            const toggleSwitch = toggle.closest('.toggle-switch');
            if (!toggleSwitch) {
                console.warn(`No toggle-switch container found for user ${userId}`);
                return;
            }
            
            if (isLoading) {
                toggleSwitch.classList.add('toggle-loading');
                toggle.disabled = true;
            } else {
                toggleSwitch.classList.remove('toggle-loading');
                toggle.disabled = false;
            }
        });
    } catch (error) {
        console.error('setToggleLoading error:', error);
    }
}


// เพิ่มฟังก์ชันสำหรับ Visual Effects
function setRowProcessing(userId, isProcessing) {
    try {
        const checkbox = document.querySelector(`input.user-checkbox[value="${userId}"]`);
        if (!checkbox) {
            console.warn(`No checkbox found for user ${userId}`);
            return;
        }
        
        const row = checkbox.closest('tr');
        if (!row) {
            console.warn(`No row found for user ${userId}`);
            return;
        }
        
        if (isProcessing) {
            row.classList.add('row-processing');
            row.classList.remove('row-success');
        } else {
            row.classList.remove('row-processing');
        }
    } catch (error) {
        console.error('setRowProcessing error:', error);
    }
}

	
/**
 * การจัดการเลือก User
 */
function toggleUserSelection(userId) {
    if (selectedUsers.has(userId)) {
        selectedUsers.delete(userId);
    } else {
        selectedUsers.add(userId);
    }
    updateBulkActions();
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    
    if (selectAll.checked) {
        checkboxes.forEach(cb => {
            cb.checked = true;
            selectedUsers.add(parseInt(cb.value));
        });
    } else {
        checkboxes.forEach(cb => {
            cb.checked = false;
            selectedUsers.delete(parseInt(cb.value));
        });
    }
    updateBulkActions();
}

function clearSelection() {
    selectedUsers.clear();
    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateBulkActions();
}

function updateBulkActions() {
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    if (selectedUsers.size > 0) {
        bulkActions.classList.remove('hidden');
        selectedCount.textContent = selectedUsers.size;
    } else {
        bulkActions.classList.add('hidden');
    }
}

/**
 * การกรองและค้นหา
 */
document.getElementById('searchUsers').addEventListener('input', function() {
    filterUsers();
});

function filterUsers() {
    const searchTerm = document.getElementById('searchUsers').value.toLowerCase();
    const statusFilter = document.getElementById('filterByStatus').value;
    const positionFilter = document.getElementById('filterByPosition').value;
    
    filteredUsers = allUsers.filter(user => {
        // ค้นหาตามชื่อ, อีเมล
        const matchesSearch = !searchTerm || 
            (user.full_name && user.full_name.toLowerCase().includes(searchTerm)) ||
            (user.m_email && user.m_email.toLowerCase().includes(searchTerm));
        
        // กรองตามสถานะ
        let matchesStatus = true;
        if (statusFilter !== 'all') {
            switch (statusFilter) {
                case 'active':
                    matchesStatus = user.storage_access_granted == 1;
                    break;
                case 'inactive':
                    matchesStatus = user.storage_access_granted != 1;
                    break;
                case 'has_folder':
                    matchesStatus = user.personal_folder_id && user.personal_folder_id.trim() !== '';
                    break;
                case 'no_folder':
                    matchesStatus = !user.personal_folder_id || user.personal_folder_id.trim() === '';
                    break;
            }
        }
        
        // กรองตามตำแหน่ง
        const matchesPosition = positionFilter === 'all' || user.ref_pid == positionFilter;
        
        return matchesSearch && matchesStatus && matchesPosition;
    });
    
    currentPage = 1;
    renderUserTable();
}

/**
 * การแบ่งหน้า
 */
function updatePagination() {
    const totalPages = Math.ceil(filteredUsers.length / usersPerPage);
    const start = (currentPage - 1) * usersPerPage + 1;
    const end = Math.min(currentPage * usersPerPage, filteredUsers.length);
    
    document.getElementById('pageStart').textContent = start;
    document.getElementById('pageEnd').textContent = end;
    document.getElementById('pageTotal').textContent = filteredUsers.length;
    
    // สร้างปุ่มเลขหน้า
    const pageNumbers = document.getElementById('pageNumbers');
    pageNumbers.innerHTML = '';
    
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `px-3 py-1 rounded ${i === currentPage ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600 hover:bg-gray-300'}`;
            btn.onclick = () => changePage(i);
            pageNumbers.appendChild(btn);
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            const dots = document.createElement('span');
            dots.textContent = '...';
            dots.className = 'px-2 py-1 text-gray-500';
            pageNumbers.appendChild(dots);
        }
    }
    
    // ปุ่มก่อนหน้า/ถัดไป
    document.getElementById('prevBtn').disabled = currentPage === 1;
    document.getElementById('nextBtn').disabled = currentPage === totalPages;
}

function changePage(page) {
    const totalPages = Math.ceil(filteredUsers.length / usersPerPage);
    
    if (page === 'prev') {
        currentPage = Math.max(1, currentPage - 1);
    } else if (page === 'next') {
        currentPage = Math.min(totalPages, currentPage + 1);
    } else {
        currentPage = page;
    }
    
    renderUserTable();
}

function updateCounts() {
    document.getElementById('showingCount').textContent = filteredUsers.length;
    document.getElementById('totalCount').textContent = allUsers.length;
}

	
	
	// Helper function อัปเดตสถานะ toggle
function updateToggleStatus(userId, isActive) {
    const toggleElement = document.querySelector(`input[onchange*="${userId}"]`);
    if (toggleElement) {
        const statusText = toggleElement.closest('td').querySelector('.text-xs');
        if (statusText) {
            statusText.textContent = isActive ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            statusText.className = `text-xs mt-1 ${isActive ? 'text-green-600' : 'text-gray-500'}`;
        }
    }
}



	
	
	function refreshUserRow(userId) {
    // หา user ในข้อมูล
    const user = allUsers.find(u => u.m_id == userId);
    if (!user) return;
    
    // หาแถวในตาราง
    const checkbox = document.querySelector(`input.user-checkbox[value="${userId}"]`);
    if (!checkbox) return;
    
    const row = checkbox.closest('tr');
    if (!row) return;
    
    // อัปเดต personal folder cell
    const folderCell = row.cells[4]; // cell ที่ 5 (index 4)
    if (folderCell && user.has_personal_folder) {
        folderCell.innerHTML = `
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="fas fa-folder mr-1"></i>มีแล้ว
                </span>
                <button onclick="openUserFolder('${user.personal_folder_id}')" 
                        class="text-green-600 hover:text-green-800 text-xs">
                    เปิด
                </button>
            </div>
        `;
    }
    
    // อัปเดต action buttons
    const actionCell = row.cells[6]; // cell สุดท้าย
    if (actionCell && user.has_personal_folder) {
        const existingButtons = actionCell.querySelector('.flex');
        if (existingButtons && !existingButtons.querySelector(`[onclick*="openUserFolder"]`)) {
            existingButtons.innerHTML += `
                <button onclick="openUserFolder('${user.personal_folder_id}')" 
                        class="text-green-600 hover:text-green-800 text-sm"
                        title="เปิดโฟลเดอร์">
                    <i class="fas fa-external-link-alt"></i>
                </button>
            `;
        }
    }
}


	
	
	// Safe Mode Functions - ใช้เมื่อ CSS Classes ไม่ทำงาน
function safeSetToggleLoading(userId, isLoading) {
    try {
        const toggleElements = document.querySelectorAll(`input[onchange*="${userId}"]`);
        
        if (!toggleElements || toggleElements.length === 0) {
            console.warn(`No toggles found for user ${userId} in safe mode`);
            return;
        }
        
        toggleElements.forEach(toggle => {
            if (!toggle) return;
            
            try {
                toggle.disabled = isLoading;
                
                const slider = toggle.nextElementSibling;
                if (slider) {
                    if (isLoading) {
                        slider.classList.add('safe-mode-loading');
                        slider.style.backgroundColor = '#fbbf24';
                    } else {
                        slider.classList.remove('safe-mode-loading');
                        slider.style.backgroundColor = toggle.checked ? '#2563eb' : '#ccc';
                    }
                }
            } catch (innerError) {
                console.warn(`Inner toggle error for user ${userId}:`, innerError);
            }
        });
    } catch (error) {
        console.error('safeSetToggleLoading error:', error);
    }
}
	
	
	
	// Helper function สำหรับแสดง Toast
function showToast(message, type = 'info') {
    // ใช้ SweetAlert2 หรือ notification library ที่มีอยู่
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type === 'error' ? 'error' : 'success',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    } else {
        // Fallback ถ้าไม่มี SweetAlert
        alert(message);
    }
}
	
	
// ✅ แก้ไขแบบง่าย - เพิ่มการรีเฟรชหน้าหลัง toggle สำเร็จ
function toggleUserStatus(userId, isActive) {
    try {
        const action = isActive ? 'enable' : 'disable';
        
        console.log(`🔄 Toggle user ${userId} to ${action}`);
        
        // เริ่ม visual effects
        setToggleLoading(userId, true);
        setRowProcessing(userId, true);
        
        if (isActive) {
            setToggleCreating(userId, true);
        }
        
        // เรียก API
        fetch('<?php echo site_url('google_drive_system/toggle_user_storage_access_with_folder'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `user_id=${encodeURIComponent(userId)}&action=${encodeURIComponent(action)}&auto_create_folder=1`
        })
        .then(response => {
            return response.text().then(text => {
                let data;
                try {
                    data = JSON.parse(text);
                } catch (parseError) {
                    const cleanText = text.trim()
                        .replace(/^[^{]*/, '') 
                        .replace(/[^}]*$/, '');
                    data = JSON.parse(cleanText);
                }
                return data;
            });
        })
        .then(data => {
            // หยุด loading effects
            setToggleLoading(userId, false);
            setRowProcessing(userId, false);
            setToggleCreating(userId, false);
            
            // ปิด loading dialog
            Swal.close();
            
            if (data.success) {
                // แสดงข้อความสำเร็จ
                if (isActive && data.data && data.data.folder_created) {
                    Swal.fire({
                        icon: 'success',
                        title: '🎉 เปิดใช้งานเรียบร้อย!',
                        html: `
                            <div class="text-left">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                        <span class="font-medium text-green-800">ดำเนินการสำเร็จ</span>
                                    </div>
                                    <ul class="text-sm text-green-700 space-y-1">
                                        <li>✅ เปิดใช้งาน Storage แล้ว</li>
                                        <li>📁 สร้างโฟลเดอร์ส่วนตัว: <strong>${escapeHtml(data.data.folder_name || 'ไม่ระบุ')}</strong></li>
                                        <li>🔑 กำหนดสิทธิ์เข้าถึงแล้ว: <strong>${data.data.permissions_assigned || 0}</strong> รายการ</li>
                                    </ul>
                                </div>
                            </div>
                        `,
                        timer: 3000,
                        showConfirmButton: true,
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        // ✅ รีเฟรชหน้าหลังปิด dialog
                        location.reload();
                    });
                } else {
                    showToast(data.message || (isActive ? 'เปิดใช้งาน Storage แล้ว' : 'ปิดใช้งาน Storage แล้ว'), 'success');
                    
                    // ✅ รีเฟรชหน้าหลัง 1 วินาที
                    setTimeout(() => {
                        location.reload();
                    }, 100);
                }
                
            } else {
                // คืนค่า toggle กลับ
                const toggleElement = document.querySelector(`input[onchange*="${userId}"]`);
                if (toggleElement) {
                    toggleElement.checked = !isActive;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถดำเนินการได้',
                    text: data.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ',
                    confirmButtonText: 'ตกลง'
                });
            }
        })
        .catch(error => {
            console.error('❌ Toggle error:', error);
            
            // หยุด loading effects
            setToggleLoading(userId, false);
            setRowProcessing(userId, false);
            setToggleCreating(userId, false);
            
            // ปิด loading dialog
            Swal.close();
            
            // คืนค่า toggle กลับ
            const toggleElement = document.querySelector(`input[onchange*="${userId}"]`);
            if (toggleElement) {
                toggleElement.checked = !isActive;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                confirmButtonText: 'ตกลง'
            });
        });
        
    } catch (mainError) {
        console.error('❌ Main function error:', mainError);
        showToast('เกิดข้อผิดพลาดไม่คาดคิด กรุณาลองใหม่', 'error');
        
        // คืนค่า toggle กลับ
        const toggleElement = document.querySelector(`input[onchange*="${userId}"]`);
        if (toggleElement) {
            toggleElement.checked = !isActive;
        }
    }
}

	
	
	
	

function createPersonalFolder(userId) {
    Swal.fire({
        title: 'สร้างโฟลเดอร์ส่วนตัว',
        text: 'คุณต้องการสร้างโฟลเดอร์ส่วนตัวให้ผู้ใช้นี้หรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'สร้าง',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            executeCreatePersonalFolder(userId);
        }
    });
}

function executeCreatePersonalFolder(userId) {
    Swal.fire({
        title: 'กำลังสร้างโฟลเดอร์...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('<?php echo site_url('google_drive_system/create_single_personal_folder'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `user_id=${userId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'สร้างโฟลเดอร์เรียบร้อย',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            // อัปเดตข้อมูลและแสดงใหม่
            setTimeout(() => {
                loadUserList();
            }, 1000);
        } else {
            throw new Error(data.message || 'ไม่สามารถสร้างโฟลเดอร์ได้');
        }
    })
    .catch(error => {
        console.error('Create personal folder error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถสร้างโฟลเดอร์ส่วนตัวได้: ' + error.message
        });
    });
}

/**
 * การดำเนินการหลายคน
 */
function bulkToggleStatus(enable) {
    if (selectedUsers.size === 0) {
        showError('กรุณาเลือกผู้ใช้อย่างน้อย 1 คน');
        return;
    }
    
    const action = enable ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
    
    Swal.fire({
        title: `ยืนยัน${action}`,
        text: `คุณต้องการ${action}ผู้ใช้ที่เลือก ${selectedUsers.size} คนหรือไม่?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: action,
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            executeBulkToggleStatus(enable);
        }
    });
}

function executeBulkToggleStatus(enable) {
    const userIds = Array.from(selectedUsers);
    
    Swal.fire({
        title: `กำลัง${enable ? 'เปิด' : 'ปิด'}ใช้งานผู้ใช้...`,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('<?php echo site_url('google_drive_system/bulk_toggle_user_status'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            user_ids: userIds,
            enable: enable
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'ดำเนินการเรียบร้อย',
                text: `${enable ? 'เปิด' : 'ปิด'}ใช้งานผู้ใช้ ${data.affected_count} คน`,
                timer: 2000,
                showConfirmButton: false
            });
            
            clearSelection();
            setTimeout(() => {
                loadUserList();
            }, 1000);
        } else {
            throw new Error(data.message || 'ไม่สามารถดำเนินการได้');
        }
    })
    .catch(error => {
        console.error('Bulk toggle status error:', error);
        showError('ไม่สามารถดำเนินการได้: ' + error.message);
    });
}

function bulkCreateFolders() {
    if (selectedUsers.size === 0) {
        showError('กรุณาเลือกผู้ใช้อย่างน้อย 1 คน');
        return;
    }
    
    Swal.fire({
        title: 'สร้างโฟลเดอร์ส่วนตัว',
        text: `คุณต้องการสร้างโฟลเดอร์ส่วนตัวให้ผู้ใช้ที่เลือก ${selectedUsers.size} คนหรือไม่?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'สร้าง',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            executeBulkCreateFolders();
        }
    });
}

function executeBulkCreateFolders() {
    const userIds = Array.from(selectedUsers);
    
    Swal.fire({
        title: 'กำลังสร้างโฟลเดอร์...',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
                </div>
                <p class="text-gray-600">กำลังสร้างโฟลเดอร์ส่วนตัวให้ผู้ใช้ ${userIds.length} คน...</p>
                <div class="mt-2 text-sm text-gray-500">
                    <div id="folderProgress">0 / ${userIds.length}</div>
                </div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    fetch('<?php echo site_url('google_drive_system/bulk_create_personal_folders'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            user_ids: userIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'สร้างโฟลเดอร์เรียบร้อย',
                html: `
                    <div class="text-left">
                        <p class="mb-2">ผลการดำเนินการ:</p>
                        <ul class="text-sm space-y-1">
                            <li>✅ สร้างสำเร็จ: ${data.created_count} โฟลเดอร์</li>
                            <li>⚠️ มีอยู่แล้ว: ${data.existing_count} โฟลเดอร์</li>
                            <li>❌ ล้มเหลว: ${data.failed_count} โฟลเดอร์</li>
                        </ul>
                    </div>
                `,
                confirmButtonText: 'ตกลง'
            });
            
            clearSelection();
            setTimeout(() => {
                loadUserList();
            }, 1000);
        } else {
            throw new Error(data.message || 'ไม่สามารถสร้างโฟลเดอร์ได้');
        }
    })
    .catch(error => {
        console.error('Bulk create folders error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถสร้างโฟลเดอร์ได้: ' + error.message
        });
    });
}

function bulkCreatePersonalFolders() {
    Swal.fire({
        title: 'สร้างโฟลเดอร์ส่วนตัวให้ทุกคน',
        text: 'คุณต้องการสร้างโฟลเดอร์ส่วนตัวให้ผู้ใช้ทุกคนที่ยังไม่มีโฟลเดอร์หรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'สร้างทั้งหมด',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            executeCreateAllPersonalFolders();
        }
    });
}

function executeCreateAllPersonalFolders() {
    Swal.fire({
        title: 'กำลังสร้างโฟลเดอร์ส่วนตัว...',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-green-600 border-t-transparent"></div>
                </div>
                <p class="text-gray-600">กำลังสร้างโฟลเดอร์ส่วนตัวให้ผู้ใช้ทั้งหมด...</p>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    fetch('<?php echo site_url('google_drive_system/create_all_missing_personal_folders'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'create_all=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'สร้างโฟลเดอร์เรียบร้อย',
                html: `
                    <div class="text-left">
                        <p class="mb-2">ผลการดำเนินการ:</p>
                        <ul class="text-sm space-y-1">
                            <li>✅ สร้างใหม่: ${data.created_count} โฟลเดอร์</li>
                            <li>⚠️ มีอยู่แล้ว: ${data.existing_count} โฟลเดอร์</li>
                            <li>❌ ข้ามไป: ${data.skipped_count} คน (ไม่มีสิทธิ์)</li>
                        </ul>
                    </div>
                `,
                confirmButtonText: 'ตกลง'
            });
            
            setTimeout(() => {
                loadUserList();
            }, 1000);
        } else {
            throw new Error(data.message || 'ไม่สามารถสร้างโฟลเดอร์ได้');
        }
    })
    .catch(error => {
        console.error('Create all personal folders error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถสร้างโฟลเดอร์ได้: ' + error.message
        });
    });
}

/**
 * ฟังก์ชันอื่นๆ
 */
function refreshUserList() {
    clearSelection();
    loadUserList();
}

function manageUserPermissions(userId) {
    currentUserId = userId;
    
    // แสดง modal
    document.getElementById('permissionModal').classList.remove('hidden');
    
    // โหลดข้อมูลผู้ใช้
    loadUserPermissionData(userId);
}
	
	
	
	function loadUserPermissionData(userId) {
    showModalLoading(true);
    
    fetch(`<?php echo site_url('google_drive_system/get_user_permission_data'); ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `user_id=${encodeURIComponent(userId)}`
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        console.log('📡 Content-type:', response.headers.get('content-type'));
        
        // ✅ 1. Better Error Handling
        const contentType = response.headers.get('content-type');
        
        if (!response.ok) {
            // ✅ 2. HTML Detection
            if (contentType && contentType.includes('text/html')) {
                return response.text().then(html => {
                    console.error('🚨 Server returned HTML instead of JSON:', html.substring(0, 500));
                    throw new Error(`เซิร์ฟเวอร์เกิดข้อผิดพลาด (${response.status}) - API อาจไม่มีอยู่หรือมี PHP error`);
                });
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        }
        
        // ตรวจสอบว่าเป็น JSON หรือไม่
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('🚨 Non-JSON response:', text.substring(0, 500));
                throw new Error('เซิร์ฟเวอร์ส่งกลับข้อมูลไม่ถูกต้อง - คาดหวัง JSON แต่ได้ข้อความอื่น');
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('✅ Success response:', data);
        if (data.success) {
            currentUserData = data.data;
            populateUserInfo(data.data.user);
            populateFolderPermissions(data.data.folders);
            populateSystemPermissions(data.data.system_permissions);
            populatePermissionHistory(data.data.history);
            updatePermissionsSummary(data.data.summary);
        } else {
            throw new Error(data.message || 'ไม่สามารถโหลดข้อมูลได้');
        }
    })
    .catch(error => {
        console.error('❌ Load user permission data error:', error);
        
        // ✅ 3. Enhanced Error Messages
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            html: `
                <div class="text-left">
                    <p class="mb-2 font-medium text-red-600">ไม่สามารถโหลดข้อมูลผู้ใช้ได้</p>
                    <p class="text-sm text-gray-600 mb-3">รายละเอียด: ${escapeHtml(error.message)}</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs text-yellow-700">
                            <i class="fas fa-tools mr-1"></i>
                            <strong>🔧 วิธีแก้ไข:</strong><br>
                            • ตรวจสอบ Server Logs: <code>/var/log/apache2/error.log</code><br>
                            • ตรวจสอบ CodeIgniter Logs: <code>application/logs/</code><br>
                            • ตรวจสอบ Database Connection ใน <code>config/database.php</code><br>
                            • ตรวจสอบว่า method <code>get_user_permission_data</code> มีอยู่<br>
                            • ตรวจสอบ Permission Tables ใน Database<br>
                            • ลองล็อกเอาท์และล็อกอินใหม่<br>
                            • ตรวจสอบ Session และ Cookie
                        </p>
                    </div>
                </div>
            `,
            width: '600px',
            confirmButtonText: 'ตกลง'
        });
    })
    .finally(() => {
        showModalLoading(false);
    });
}

	

function populateUserInfo(user) {
    // อัปเดตข้อมูลผู้ใช้
    document.getElementById('userInitial').textContent = user.m_fname ? user.m_fname.charAt(0).toUpperCase() : 'U';
    document.getElementById('userName').textContent = user.full_name || 'ไม่ระบุชื่อ';
    document.getElementById('userEmail').textContent = user.m_email || '';
    document.getElementById('userPosition').textContent = user.position_name || 'ไม่ระบุตำแหน่ง';
    document.getElementById('modalUserInfo').textContent = `${user.full_name} - ${user.position_name}`;
    
    // อัปเดตสถานะ
    const storageStatus = document.getElementById('storageStatus');
    const personalFolderStatus = document.getElementById('personalFolderStatus');
    
    if (user.storage_access_granted == 1) {
        storageStatus.innerHTML = '<i class="fas fa-circle text-green-400 mr-1"></i>เปิดใช้งาน';
        storageStatus.className = 'px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
    } else {
        storageStatus.innerHTML = '<i class="fas fa-circle text-red-400 mr-1"></i>ปิดใช้งาน';
        storageStatus.className = 'px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
    }
    
    if (user.personal_folder_id) {
        personalFolderStatus.innerHTML = '<i class="fas fa-circle text-green-400 mr-1"></i>มีแล้ว';
        personalFolderStatus.className = 'px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
    } else {
        personalFolderStatus.innerHTML = '<i class="fas fa-circle text-red-400 mr-1"></i>ยังไม่มี';
        personalFolderStatus.className = 'px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
    }
    
    // อัปเดต Usage
    updateUsageDisplay(user.storage_quota_used || 0, user.storage_quota_limit || 1073741824);
}

/**
 * แสดงสิทธิ์โฟลเดอร์
 */
function populateFolderPermissions(folders) {
    const container = document.getElementById('folderPermissionsList');
    
    if (!folders || folders.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-folder-open text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600">ไม่มีสิทธิ์โฟลเดอร์</p>
                <button onclick="grantBulkPermissions()" 
                        class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    เพิ่มสิทธิ์โฟลเดอร์
                </button>
            </div>
        `;
        return;
    }
    
    let html = '';
    folders.forEach(folder => {
        const iconClass = getFolderIconClass(folder.folder_type);
        const folderName = folder.folder_name || 'ไม่ระบุชื่อ';
        const folderId = folder.folder_id;
        
        html += `
            <div class="folder-tree-item bg-white border border-gray-200 rounded-lg p-4" data-folder-id="${escapeHtml(folderId)}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 flex-1">
                        <i class="fas fa-folder ${iconClass} text-lg"></i>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-800">${escapeHtml(folderName)}</h4>
                            <p class="text-sm text-gray-500">${getFolderTypeText(folder.folder_type)}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <select onchange="updateFolderPermission('${escapeHtml(folderId)}', this.value)" 
                                class="px-3 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="no_access" ${folder.access_level === 'no_access' ? 'selected' : ''}>ไม่มีสิทธิ์</option>
                            <option value="read_only" ${folder.access_level === 'read_only' ? 'selected' : ''}>อ่านอย่างเดียว</option>
                            <option value="read_write" ${folder.access_level === 'read_write' ? 'selected' : ''}>อ่าน-เขียน</option>
                            <option value="admin" ${folder.access_level === 'admin' ? 'selected' : ''}>ผู้ดูแล</option>
                        </select>
                        <!-- ✅ แก้ไข: ส่งทั้ง folderId และ folderName -->
                        <button onclick="removeFolderPermission('${escapeHtml(folderId)}', '${escapeHtml(folderName)}')" 
                                class="text-red-600 hover:text-red-800 p-1" 
                                title="ลบสิทธิ์ ${escapeHtml(folderName)}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                ${folder.granted_by ? `
                    <div class="mt-2 text-xs text-gray-500">
                        ให้สิทธิ์โดย: ${escapeHtml(folder.granted_by_name)} เมื่อ ${formatDate(folder.granted_at)}
                    </div>
                ` : ''}
            </div>
        `;
    });
    
    container.innerHTML = html;
}

/**
 * แสดงสิทธิ์ระบบ
 */
function populateSystemPermissions(permissions) {
    if (!permissions) return;
    
    // อัปเดต toggles
    document.getElementById('storageAccessToggle').checked = permissions.storage_access_granted == 1;
    document.getElementById('createFolderToggle').checked = permissions.can_create_folder == 1;
    document.getElementById('shareFileToggle').checked = permissions.can_share == 1;
    document.getElementById('deleteFileToggle').checked = permissions.can_delete == 1;
    document.getElementById('inheritPositionToggle').checked = permissions.inherit_position != 1;
    document.getElementById('overridePositionToggle').checked = permissions.override_position == 1;
    
    // อัปเดต quota
    if (permissions.storage_quota_limit) {
        document.getElementById('storageQuotaSelect').value = permissions.storage_quota_limit;
    }
    
    // อัปเดตหมายเหตุ
    if (permissions.notes) {
        document.getElementById('permissionNotes').value = permissions.notes;
    }
}

/**
 * แสดงประวัติการเปลี่ยนแปลง
 */
function populatePermissionHistory(history) {
    const container = document.getElementById('permissionHistoryList');
    
    if (!history || history.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600">ไม่มีประวัติการเปลี่ยนแปลง</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    history.forEach(item => {
        const iconClass = getActionIconClass(item.action_type);
        const colorClass = getActionColorClass(item.action_type);
        
        html += `
            <div class="permission-history-item pl-4 py-3 ${colorClass}">
                <div class="flex items-start space-x-3">
                    <i class="fas ${iconClass} mt-1"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">${escapeHtml(item.action_description)}</p>
                        <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                            <span><i class="fas fa-user mr-1"></i>${escapeHtml(item.by_user_name || 'ระบบ')}</span>
                            <span><i class="fas fa-clock mr-1"></i>${formatDate(item.created_at)}</span>
                            ${item.ip_address ? `<span><i class="fas fa-map-marker-alt mr-1"></i>${item.ip_address}</span>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

/**
 * อัปเดตสรุปสิทธิ์
 */
function updatePermissionsSummary(summary) {
    const container = document.getElementById('currentPermissionsSummary');
    
    if (!summary) {
        container.innerHTML = '<div class="text-gray-500 text-sm">ไม่มีข้อมูลสรุป</div>';
        return;
    }
    
    let html = '';
    
    // System permissions
    html += `
        <div class="permission-badge bg-blue-50 border border-blue-200 rounded-lg p-3 mb-2">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-blue-800">Storage Access</span>
                <span class="text-xs px-2 py-1 rounded-full ${summary.storage_access ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${summary.storage_access ? 'เปิด' : 'ปิด'}
                </span>
            </div>
        </div>
    `;
    
    if (summary.folder_count > 0) {
        html += `
            <div class="permission-badge bg-purple-50 border border-purple-200 rounded-lg p-3 mb-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-purple-800">โฟลเดอร์</span>
                    <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 rounded-full">
                        ${summary.folder_count} โฟลเดอร์
                    </span>
                </div>
            </div>
        `;
    }
    
    if (summary.personal_folder) {
        html += `
            <div class="permission-badge bg-green-50 border border-green-200 rounded-lg p-3 mb-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-green-800">โฟลเดอร์ส่วนตัว</span>
                    <button onclick="openUserFolder('${summary.personal_folder}')" 
                            class="text-xs text-green-600 hover:text-green-800">
                        <i class="fas fa-external-link-alt"></i>
                    </button>
                </div>
            </div>
        `;
    }
    
    container.innerHTML = html;
}

/**
 * สลับแท็บ
 */
function switchTab(tabName) {
    // ซ่อนแท็บทั้งหมด
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // แสดงแท็บที่เลือก
    document.getElementById(tabName + 'Tab').classList.remove('hidden');
    
    // อัปเดตปุ่มแท็บ
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
}

/**
 * อัปเดตสิทธิ์โฟลเดอร์
 */
function updateFolderPermission(folderId, accessLevel) {
    hasUnsavedChanges = true;
    
    // เก็บการเปลี่ยนแปลงไว้ในหน่วยความจำ
    if (!currentUserData.pending_changes) {
        currentUserData.pending_changes = {};
    }
    
    if (!currentUserData.pending_changes.folders) {
        currentUserData.pending_changes.folders = {};
    }
    
    currentUserData.pending_changes.folders[folderId] = accessLevel;
    
    console.log('Folder permission updated:', folderId, accessLevel);
}

/**
 * อัปเดตสิทธิ์ระบบ
 */
function updateSystemPermission(permissionType) {
    hasUnsavedChanges = true;
    
    if (!currentUserData.pending_changes) {
        currentUserData.pending_changes = {};
    }
    
    if (!currentUserData.pending_changes.system) {
        currentUserData.pending_changes.system = {};
    }
    
    const checkbox = document.querySelector(`#${permissionType.replace('_', '')}Toggle, #${permissionType}Toggle`);
    if (checkbox) {
        currentUserData.pending_changes.system[permissionType] = checkbox.checked ? 1 : 0;
    }
    
    console.log('System permission updated:', permissionType, checkbox ? checkbox.checked : 'not found');
}

/**
 * บันทึกการเปลี่ยนแปลงทั้งหมด
 */
function saveAllPermissions() {
    if (!hasUnsavedChanges) {
        Swal.fire({
            icon: 'info',
            title: 'ไม่มีการเปลี่ยนแปลง',
            text: 'ไม่มีการเปลี่ยนแปลงที่ต้องบันทึก',
            timer: 2000,
            showConfirmButton: false
        });
        return Promise.resolve();
    }
    
    return new Promise((resolve, reject) => {
        Swal.fire({
            title: 'กำลังบันทึกการเปลี่ยนแปลง...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        const saveData = {
            user_id: currentUserId,
            changes: currentUserData.pending_changes || {},
            notes: document.getElementById('permissionNotes').value
        };
        
        fetch(`<?php echo site_url('google_drive_system/save_user_permissions'); ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(saveData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hasUnsavedChanges = false;
                currentUserData.pending_changes = {};
                
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกเรียบร้อย',
                    text: 'บันทึกการเปลี่ยนแปลงสิทธิ์เรียบร้อยแล้ว',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // รีโหลดข้อมูล
                loadUserPermissionData(currentUserId);
                
                resolve();
            } else {
                throw new Error(data.message || 'ไม่สามารถบันทึกได้');
            }
        })
        .catch(error => {
            console.error('Save permissions error:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถบันทึกการเปลี่ยนแปลงได้: ' + error.message
            });
            reject(error);
        });
    });
}

/**
 * การดำเนินการด่วน
 */
function toggleUserStorageQuick() {
    if (!currentUserData || !currentUserData.user) return;
    
    const isCurrentlyEnabled = currentUserData.user.storage_access_granted == 1;
    const action = isCurrentlyEnabled ? 'disable' : 'enable';
    
    toggleUserStatus(currentUserId, !isCurrentlyEnabled);
}

function createPersonalFolderQuick() {
    if (!currentUserData || !currentUserData.user) return;
    
    if (currentUserData.user.personal_folder_id) {
        Swal.fire({
            icon: 'info',
            title: 'มีโฟลเดอร์ส่วนตัวแล้ว',
            text: 'ผู้ใช้นี้มีโฟลเดอร์ส่วนตัวแล้ว'
        });
        return;
    }
    
    createPersonalFolder(currentUserId);
}

function resetUserPermissions() {
    Swal.fire({
        title: 'ยืนยันการรีเซ็ตสิทธิ์',
        text: 'คุณต้องการรีเซ็ตสิทธิ์ทั้งหมดของผู้ใช้นี้หรือไม่? การดำเนินการนี้จะลบสิทธิ์ทั้งหมดและไม่สามารถย้อนกลับได้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'รีเซ็ต',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#dc2626'
    }).then((result) => {
        if (result.isConfirmed) {
            performUserPermissionReset();
        }
    });
}

function performUserPermissionReset() {
    Swal.fire({
        title: 'กำลังรีเซ็ตสิทธิ์...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(`<?php echo site_url('google_drive_system/reset_user_permissions'); ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `user_id=${encodeURIComponent(currentUserId)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'รีเซ็ตเรียบร้อย',
                text: 'รีเซ็ตสิทธิ์ผู้ใช้เรียบร้อยแล้ว',
                timer: 2000,
                showConfirmButton: false
            });
            
            // รีโหลดข้อมูล
            loadUserPermissionData(currentUserId);
        } else {
            throw new Error(data.message || 'ไม่สามารถรีเซ็ตได้');
        }
    })
    .catch(error => {
        console.error('Reset permissions error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถรีเซ็ตสิทธิ์ได้: ' + error.message
        });
    });
}

/**
 * Helper Functions
 */
function showModalLoading(show) {
    // Implementation สำหรับ loading state ใน modal
    const loadingOverlay = document.querySelector('.modal-loading-overlay');
    if (loadingOverlay) {
        if (show) {
            loadingOverlay.classList.remove('hidden');
        } else {
            loadingOverlay.classList.add('hidden');
        }
    }
}

function getFolderIconClass(folderType) {
    switch (folderType) {
        case 'system': return 'system-folder-icon';
        case 'department': return 'folder-icon';
        case 'shared': return 'shared-folder-icon';
        case 'personal': return 'personal-folder-icon';
        default: return 'folder-icon';
    }
}

function getFolderTypeText(folderType) {
    switch (folderType) {
        case 'system': return 'โฟลเดอร์ระบบ';
        case 'department': return 'โฟลเดอร์แผนก';
        case 'shared': return 'โฟลเดอร์แชร์';
        case 'personal': return 'โฟลเดอร์ส่วนตัว';
        default: return 'ไม่ระบุ';
    }
}

function getActionIconClass(actionType) {
    switch (actionType) {
        case 'grant_permission': return 'fa-plus-circle text-green-600';
        case 'revoke_permission': return 'fa-minus-circle text-red-600';
        case 'update_permission': return 'fa-edit text-blue-600';
        case 'create_folder': return 'fa-folder-plus text-green-600';
        case 'delete_folder': return 'fa-folder-minus text-red-600';
        default: return 'fa-circle text-gray-600';
    }
}

function getActionColorClass(actionType) {
    switch (actionType) {
        case 'grant_permission': return 'border-green-200';
        case 'revoke_permission': return 'border-red-200';
        case 'update_permission': return 'border-blue-200';
        default: return 'border-gray-200';
    }
}

function updateUsageDisplay(used, limit) {
    const percentage = limit > 0 ? (used / limit) * 100 : 0;
    const usedFormatted = formatBytes(used);
    const limitFormatted = formatBytes(limit);
    
    document.getElementById('currentUsage').textContent = `${usedFormatted} / ${limitFormatted}`;
    document.getElementById('usageProgressBar').style.width = `${Math.min(percentage, 100)}%`;
    
    // เปลี่ยนสีตามการใช้งาน
    const progressBar = document.getElementById('usageProgressBar');
    if (percentage > 90) {
        progressBar.className = 'bg-red-600 h-2 rounded-full';
    } else if (percentage > 75) {
        progressBar.className = 'bg-yellow-600 h-2 rounded-full';
    } else {
        progressBar.className = 'bg-blue-600 h-2 rounded-full';
    }
}

	
	function actuallyCloseModal() {
    document.getElementById('permissionModal').classList.add('hidden');
    currentUserId = null;
    currentUserData = null;
    hasUnsavedChanges = false;
}
	
	
	/**
 * ปิด Modal จัดการสิทธิ์
 */
function closePermissionModal() {
    if (hasUnsavedChanges) {
        Swal.fire({
            title: 'มีการเปลี่ยนแปลงที่ยังไม่ได้บันทึก',
            text: 'คุณต้องการบันทึกการเปลี่ยนแปลงก่อนปิดหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'บันทึกและปิด',
            cancelButtonText: 'ปิดโดยไม่บันทึก',
            showDenyButton: true,
            denyButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                saveAllPermissions().then(() => {
                    actuallyCloseModal();
                });
            } else if (result.isDismissed) {
                actuallyCloseModal();
            }
        });
    } else {
        actuallyCloseModal();
    }
}
	
	

function viewUserDetails(userId) {
    // ไปยังหน้ารายละเอียดผู้ใช้ในหน้าเดิม (ไม่เปิด tab ใหม่)
    window.location.href = `<?php echo site_url('google_drive_system/user_details/'); ?>${userId}`;
}

function openUserFolder(folderId) {
    if (folderId) {
        window.open(`https://drive.google.com/drive/folders/${folderId}`, '_blank');
    }
}

function bulkAssignPermissions() {
    if (selectedUsers.size === 0) {
        showError('กรุณาเลือกผู้ใช้อย่างน้อย 1 คน');
        return;
    }
    
    // TODO: เปิดหน้าจัดการสิทธิ์หลายคน
    Swal.fire({
        title: 'กำหนดสิทธิ์หลายคน',
        text: 'ฟีเจอร์นี้จะพัฒนาในขั้นตอนถัดไป',
        icon: 'info'
    });
}

// Utility function
function escapeHtml(text) {
    if (text === null || text === undefined) {
        return '';
    }
    
    // แปลงเป็น string ก่อน
    text = String(text);
    
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

</script>

<script>
// Scroll shadow detection
function initScrollShadows() {
    const scrollableElements = document.querySelectorAll('.scrollable-content');
    
    scrollableElements.forEach(element => {
        element.addEventListener('scroll', function() {
            updateScrollShadows(this);
        });
        
        // Initial check
        updateScrollShadows(element);
    });
}

function updateScrollShadows(element) {
    const scrollTop = element.scrollTop;
    const scrollHeight = element.scrollHeight;
    const clientHeight = element.clientHeight;
    
    // Remove existing classes
    element.classList.remove('scrolled-top', 'scrolled-bottom');
    
    // Add scroll shadows
    if (scrollTop > 10) {
        element.classList.add('scrolled-top');
    }
    
    if (scrollTop < scrollHeight - clientHeight - 10) {
        element.classList.add('scrolled-bottom');
    }
}

// Initialize when modal opens
document.addEventListener('DOMContentLoaded', function() {
    // เรียกใช้เมื่อ modal เปิด
    const permissionModal = document.getElementById('permissionModal');
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (!permissionModal.classList.contains('hidden')) {
                setTimeout(initScrollShadows, 100);
            }
        });
    });
    
    observer.observe(permissionModal, {
        attributes: true,
        attributeFilter: ['class']
    });
});

// Smooth scroll to top function
function scrollToTop(element) {
    element.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Auto-resize textarea
function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

// เพิ่มใน permission notes textarea
document.addEventListener('DOMContentLoaded', function() {
    const notesTextarea = document.getElementById('permissionNotes');
    if (notesTextarea) {
        notesTextarea.addEventListener('input', function() {
            autoResizeTextarea(this);
        });
    }
});
	
	
	
	
	/**
 * อัปเดต Storage Quota ของผู้ใช้
 */
function updateStorageQuota() {
    try {
        const quotaSelect = document.getElementById('storageQuotaSelect');
        const selectedValue = quotaSelect.value;
        
        if (selectedValue === 'custom') {
            showCustomQuotaInput();
            return;
        }

        const quotaBytes = parseInt(selectedValue);
        const quotaMB = Math.round(quotaBytes / (1024 * 1024));
        
        // ✅ เรียก API update_user_quota
        fetch('<?php echo site_url('google_drive_system/update_user_quota'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `user_id=${encodeURIComponent(currentUserId)}&new_quota_mb=${encodeURIComponent(quotaMB)}&new_quota=${encodeURIComponent(quotaBytes)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // อัปเดตสำเร็จ
                updateUsageDisplay(currentUserData.user.storage_quota_used || 0, quotaBytes);
                Swal.fire({
                    icon: 'success',
                    title: 'อัปเดต Storage Quota เรียบร้อย',
                    text: `กำหนด Storage Quota เป็น ${data.data.new_quota_formatted} แล้ว`,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                throw new Error(data.message || 'ไม่สามารถอัปเดต Storage Quota ได้');
            }
        })
        .catch(error => {
            console.error('updateStorageQuota error:', error);
            // คืนค่า select กลับ
            quotaSelect.value = currentUserData.user.storage_quota_limit || '1073741824';
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถอัปเดต Storage Quota ได้: ' + error.message
            });
        });
        
    } catch (error) {
        console.error('updateStorageQuota main error:', error);
    }
}
/**
 * แสดงหน้าต่างกรอก Custom Quota
 */
function showCustomQuotaInput() {
    Swal.fire({
        title: 'กำหนดขนาด Storage เอง',
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ขนาด Storage:</label>
                    <div class="flex items-center space-x-2">
                        <input type="number" 
                               id="customQuotaInput" 
                               min="1" 
                               max="1000000"
                               placeholder="กรอกขนาด"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <select id="customQuotaUnit" 
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="MB">MB</option>
                            <option value="GB" selected>GB</option>
                            <option value="TB">TB</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="unlimitedCheckbox" class="mr-2">
                        <label for="unlimitedCheckbox" class="text-sm text-gray-700">ไม่จำกัดขนาด (Unlimited)</label>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        หมายเหตุ: การเปลี่ยนแปลงจะมีผลทันทีหลังจากบันทึก
                    </p>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        preConfirm: () => {
            const isUnlimited = document.getElementById('unlimitedCheckbox').checked;
            const customValue = document.getElementById('customQuotaInput').value;
            const customUnit = document.getElementById('customQuotaUnit').value;
            
            if (!isUnlimited && (!customValue || customValue <= 0)) {
                Swal.showValidationMessage('กรุณากรอกขนาด Storage');
                return false;
            }
            
            return {
                isUnlimited,
                value: parseFloat(customValue),
                unit: customUnit
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { isUnlimited, value, unit } = result.value;
            
            let quotaBytes;
            let quotaText;
            
            if (isUnlimited) {
                quotaBytes = 999999999999999; // 999TB as unlimited
                quotaText = 'Unlimited';
            } else {
                // แปลงเป็น bytes
                const multipliers = {
                    'MB': 1024 * 1024,
                    'GB': 1024 * 1024 * 1024,
                    'TB': 1024 * 1024 * 1024 * 1024
                };
                
                quotaBytes = value * multipliers[unit];
                quotaText = `${value} ${unit}`;
            }
            
            // เพิ่ม custom option ใน select
            const quotaSelect = document.getElementById('storageQuotaSelect');
            const customOption = quotaSelect.querySelector('option[value="custom"]');
            customOption.textContent = `กำหนดเอง (${quotaText})`;
            customOption.value = quotaBytes;
            
            // บันทึกการเปลี่ยนแปลง
            if (!currentUserData.pending_changes) {
                currentUserData.pending_changes = {};
            }
            
            if (!currentUserData.pending_changes.system) {
                currentUserData.pending_changes.system = {};
            }
            
            currentUserData.pending_changes.system.storage_quota_limit = quotaBytes;
            hasUnsavedChanges = true;
            
            // อัปเดต UI
            updateUsageDisplay(currentUserData.user.storage_quota_used || 0, quotaBytes);
            
            showToast(`กำหนด Storage Quota เป็น ${quotaText} แล้ว`, 'success');
        } else {
            // ถ้าผู้ใช้ยกเลิก ให้คืนค่าเดิม
            const quotaSelect = document.getElementById('storageQuotaSelect');
            quotaSelect.value = currentUserData.user.storage_quota_limit || '1073741824';
        }
    });
    
    // เพิ่ม event listener สำหรับ unlimited checkbox
    setTimeout(() => {
        const unlimitedCheckbox = document.getElementById('unlimitedCheckbox');
        const customInput = document.getElementById('customQuotaInput');
        const customUnit = document.getElementById('customQuotaUnit');
        
        if (unlimitedCheckbox && customInput && customUnit) {
            unlimitedCheckbox.addEventListener('change', function() {
                customInput.disabled = this.checked;
                customUnit.disabled = this.checked;
                
                if (this.checked) {
                    customInput.value = '';
                    customInput.placeholder = 'ไม่จำกัด';
                } else {
                    customInput.placeholder = 'กรอกขนาด';
                    customInput.focus();
                }
            });
        }
    }, 100);
}

/**
 * อัปเดต Position Inheritance
 */
function updatePositionInheritance() {
    try {
        const checkbox = document.getElementById('inheritPositionToggle');
        if (!checkbox) {
            console.error('inheritPositionToggle element not found');
            return;
        }
        
        hasUnsavedChanges = true;
        
        if (!currentUserData.pending_changes) {
            currentUserData.pending_changes = {};
        }
        
        if (!currentUserData.pending_changes.system) {
            currentUserData.pending_changes.system = {};
        }
        
        // inherit_position = !override_position
        currentUserData.pending_changes.system.inherit_position = checkbox.checked ? 1 : 0;
        currentUserData.pending_changes.system.override_position = checkbox.checked ? 0 : 1;
        
        console.log('Position inheritance updated:', checkbox.checked);
        
    } catch (error) {
        console.error('updatePositionInheritance error:', error);
    }
}

/**
 * อัปเดต Position Override
 */
function updatePositionOverride() {
    try {
        const checkbox = document.getElementById('overridePositionToggle');
        if (!checkbox) {
            console.error('overridePositionToggle element not found');
            return;
        }
        
        hasUnsavedChanges = true;
        
        if (!currentUserData.pending_changes) {
            currentUserData.pending_changes = {};
        }
        
        if (!currentUserData.pending_changes.system) {
            currentUserData.pending_changes.system = {};
        }
        
        currentUserData.pending_changes.system.override_position = checkbox.checked ? 1 : 0;
        currentUserData.pending_changes.system.inherit_position = checkbox.checked ? 0 : 1;
        
        console.log('Position override updated:', checkbox.checked);
        
    } catch (error) {
        console.error('updatePositionOverride error:', error);
    }
}

/**
 * ลบสิทธิ์โฟลเดอร์
 */
function removeFolderPermission(folderId, folderName) {
    if (!folderId) {
        console.error('Folder ID is required');
        return;
    }
    
    // ✅ แก้ไข: ถ้าไม่มี folderName ให้หาจาก DOM
    if (!folderName) {
        const folderElement = document.querySelector(`[data-folder-id="${folderId}"]`);
        if (folderElement) {
            const nameElement = folderElement.querySelector('h4, .font-medium');
            folderName = nameElement ? nameElement.textContent : 'ไม่ระบุชื่อ';
        } else {
            folderName = 'โฟลเดอร์ที่เลือก';
        }
    }
    
    Swal.fire({
        title: 'ยืนยันการลบสิทธิ์',
        html: `
            <div class="text-left">
                <p class="mb-3">คุณต้องการลบสิทธิ์การเข้าถึงโฟลเดอร์นี้หรือไม่?</p>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-folder text-red-600 mr-3 text-lg"></i>
                        <div>
                            <p class="font-medium text-red-800">${escapeHtml(folderName)}</p>
                            <p class="text-sm text-red-600">การดำเนินการนี้ไม่สามารถยกเลิกได้</p>
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ลบสิทธิ์',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (result.isConfirmed) {
            executeRemoveFolderPermission(folderId, folderName);
        }
    });
}

/**
 * ดำเนินการลบสิทธิ์โฟลเดอร์
 */
function executeRemoveFolderPermission(folderId, folderName) {
    // ✅ เพิ่ม validation
    if (!folderId) {
        console.error('Folder ID is required');
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่พบรหัสโฟลเดอร์'
        });
        return;
    }

    if (!currentUserId) {
        console.error('Current user ID is not set');
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่พบข้อมูลผู้ใช้'
        });
        return;
    }

    // ✅ แสดง loading dialog พร้อมชื่อโฟลเดอร์
    Swal.fire({
        title: 'กำลังลบสิทธิ์...',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-red-600 border-t-transparent"></div>
                </div>
                <p class="text-gray-600 mb-2">กำลังลบสิทธิ์การเข้าถึงโฟลเดอร์...</p>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mt-3">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-folder text-gray-600 mr-2"></i>
                        <span class="text-sm text-gray-700">${escapeHtml(folderName || 'โฟลเดอร์ที่เลือก')}</span>
                    </div>
                </div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    // ✅ เรียก API ลบสิทธิ์
    fetch('<?php echo site_url('google_drive_system/remove_user_folder_permission'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `folder_id=${encodeURIComponent(folderId)}&user_id=${encodeURIComponent(currentUserId)}`
    })
    .then(response => {
        console.log('📡 API Response status:', response.status);
        console.log('📡 Content-Type:', response.headers.get('content-type'));
        
        // ✅ Enhanced Error Handling
        const contentType = response.headers.get('content-type');
        
        if (!response.ok) {
            // ✅ HTML Detection - ตรวจจับเมื่อ server ส่ง HTML error แทน JSON
            if (contentType && contentType.includes('text/html')) {
                return response.text().then(html => {
                    console.error('🚨 Server returned HTML instead of JSON:', html.substring(0, 500));
                    throw new Error(`เซิร์ฟเวอร์เกิดข้อผิดพลาด (${response.status}) - API อาจไม่มีอยู่หรือมี PHP error`);
                });
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        }
        
        // ตรวจสอบว่าเป็น JSON หรือไม่
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('🚨 Non-JSON response:', text.substring(0, 500));
                throw new Error('เซิร์ฟเวอร์ส่งกลับข้อมูลไม่ถูกต้อง - คาดหวัง JSON แต่ได้ข้อความอื่น');
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('✅ API Response data:', data);
        
        if (data.success) {
            // ✅ แสดงข้อความสำเร็จ
            Swal.fire({
                icon: 'success',
                title: 'ลบสิทธิ์เรียบร้อย',
                html: `
                    <div class="text-center">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-center mb-2">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="font-medium text-green-800">ลบสิทธิ์เรียบร้อยแล้ว</span>
                            </div>
                            <div class="flex items-center justify-center">
                                <i class="fas fa-folder text-green-600 mr-2"></i>
                                <span class="text-green-700">${escapeHtml(folderName || 'โฟลเดอร์ที่เลือก')}</span>
                            </div>
                        </div>
                        
                        ${data.data && data.data.inherited_removed > 0 ? `
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-sm text-blue-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    ลบสิทธิ์ที่สืบทอดใน subfolder: <strong>${data.data.inherited_removed}</strong> รายการ
                                </p>
                            </div>
                        ` : ''}
                    </div>
                `,
                timer: 4000,
                showConfirmButton: true,
                confirmButtonText: 'ตกลง'
            });
            
            // ✅ รีโหลดข้อมูลโฟลเดอร์ในโมดอล
            if (typeof loadUserPermissionData === 'function' && currentUserId) {
                loadUserPermissionData(currentUserId);
            }
            
            // ✅ อัปเดต UI ถ้าจำเป็น
            const folderElement = document.querySelector(`[data-folder-id="${folderId}"]`);
            if (folderElement) {
                folderElement.style.opacity = '0.5';
                folderElement.style.transition = 'opacity 0.5s ease';
                
                // ซ่อนหลังจาก 2 วินาที
                setTimeout(() => {
                    if (folderElement.parentNode) {
                        folderElement.parentNode.removeChild(folderElement);
                    }
                }, 2000);
            }
            
        } else {
            throw new Error(data.message || 'ไม่สามารถลบสิทธิ์ได้');
        }
    })
    .catch(error => {
        console.error('❌ Remove folder permission error:', error);
        
        // ✅ Enhanced Error Messages
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            html: `
                <div class="text-left">
                    <p class="mb-3 font-medium text-red-600">ไม่สามารถลบสิทธิ์ได้</p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-folder text-red-600 mr-2"></i>
                            <span class="font-medium text-red-800">${escapeHtml(folderName || 'โฟลเดอร์ที่เลือก')}</span>
                        </div>
                        <p class="text-sm text-red-700">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            ${escapeHtml(error.message)}
                        </p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs text-yellow-700">
                            <i class="fas fa-tools mr-1"></i>
                            <strong>🔧 แนวทางแก้ไข:</strong><br>
                            • ตรวจสอบ server logs สำหรับ PHP errors<br>
                            • ตรวจสอบว่า API endpoint <code>remove_user_folder_permission</code> มีอยู่จริง<br>
                            • ตรวจสอบการเชื่อมต่อ database<br>
                            • ตรวจสอบว่าผู้ใช้ยังล็อกอินอยู่หรือไม่<br>
                            • ลองรีเฟรชหน้าและดำเนินการใหม่<br>
                            • ตรวจสอบตาราง <code>tbl_google_drive_member_folder_access</code>
                        </p>
                    </div>
                </div>
            `,
            width: '600px',
            confirmButtonText: 'ตกลง',
            showCancelButton: true,
            cancelButtonText: 'ลองใหม่'
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                // ลองใหม่
                executeRemoveFolderPermission(folderId, folderName);
            }
        });
    });
}
/**
 * เพิ่มสิทธิ์โฟลเดอร์หลายโฟลเดอร์
 */
function grantBulkPermissions() {
    if (!currentUserId) {
        showToast('ไม่พบข้อมูลผู้ใช้', 'error');
        return;
    }
    
    // แสดง Modal เลือกโฟลเดอร์
    showFolderSelectionModal();
}
	
	
	
	/**
 * แสดง Modal เลือกโฟลเดอร์สำหรับเพิ่มสิทธิ์
 */
function showFolderSelectionModal() {
    Swal.fire({
        title: 'เลือกโฟลเดอร์ที่ต้องการให้สิทธิ์',
        html: `
            <div class="text-left">
                <!-- Search and Filter -->
                <div class="mb-4">
                    <div class="flex space-x-3 mb-3">
                        <input type="text" 
                               id="folderSearchInput" 
                               placeholder="ค้นหาโฟลเดอร์..."
                               onkeyup="filterFolders()"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <select id="folderTypeSelect" 
                                onchange="filterFolders()"
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">ทุกประเภท</option>
                            <option value="system">โฟลเดอร์ระบบ</option>
                            <option value="department">โฟลเดอร์แผนก</option>
                            <option value="shared">โฟลเดอร์แชร์</option>
                            <option value="personal">โฟลเดอร์ส่วนตัว</option>
                        </select>
                    </div>
                </div>
                
                <!-- Loading -->
                <div id="folderLoadingSpinner" class="text-center py-4">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
                    <p class="text-gray-600 mt-2">กำลังโหลดรายการโฟลเดอร์...</p>
                </div>
                
                <!-- Folder List -->
                <div id="folderSelectionList" class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg hidden">
                    <!-- จะโหลดจาก AJAX -->
                </div>
                
                <!-- No Folders -->
                <div id="noFoldersMessage" class="text-center py-8 hidden">
                    <i class="fas fa-folder-open text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-600">ไม่มีโฟลเดอร์ที่สามารถให้สิทธิ์ได้</p>
                </div>
                
                <!-- Selected Folders Summary -->
                <div id="selectedFoldersCount" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        เลือกแล้ว <span id="selectedCount">0</span> โฟลเดอร์
                    </p>
                </div>
            </div>
        `,
        width: '600px',
        showCancelButton: true,
        confirmButtonText: 'เพิ่มสิทธิ์',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#3b82f6',
        didOpen: () => {
            loadAvailableFolders();
        },
        preConfirm: () => {
            const selectedFolders = getSelectedFolders();
            
            if (selectedFolders.length === 0) {
                Swal.showValidationMessage('กรุณาเลือกโฟลเดอร์อย่างน้อย 1 โฟลเดอร์');
                return false;
            }
            
            return selectedFolders;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            showPermissionLevelModal(result.value);
        }
    });
}

/**
 * โหลดรายการโฟลเดอร์ที่สามารถให้สิทธิ์ได้
 */
function loadAvailableFolders() {
    fetch('<?php echo site_url('google_drive_system/get_available_folders_for_permission'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `user_id=${encodeURIComponent(currentUserId)}`
    })
    .then(response => {
        // ✅ 1. Better Error Handling
        const contentType = response.headers.get('content-type');
        
        if (!response.ok) {
            // ✅ 2. HTML Detection
            if (contentType && contentType.includes('text/html')) {
                return response.text().then(html => {
                    console.error('🚨 Server returned HTML (API not found):', html.substring(0, 300));
                    throw new Error(`API endpoint ไม่มีอยู่ (${response.status}) - ยังไม่ได้สร้าง get_available_folders_for_permission`);
                });
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        }
        
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('🚨 Non-JSON response:', text.substring(0, 300));
                throw new Error('เซิร์ฟเวอร์ส่งกลับข้อมูลไม่ถูกต้อง');
            });
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderFolderList(data.data.folders);
        } else {
            throw new Error(data.message || 'ไม่สามารถโหลดรายการโฟลเดอร์ได้');
        }
    })
    .catch(error => {
        console.error('❌ Load available folders error:', error);
        document.getElementById('folderLoadingSpinner').classList.add('hidden');
        document.getElementById('noFoldersMessage').classList.remove('hidden');
        
        // ✅ 3. Enhanced Error Messages
        document.getElementById('noFoldersMessage').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
                <p class="text-red-600 mb-3 font-medium">เกิดข้อผิดพลาด</p>
                <p class="text-sm text-red-600 mb-4">${escapeHtml(error.message)}</p>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-left max-w-md mx-auto">
                    <p class="text-xs text-yellow-700">
                        <i class="fas fa-code mr-1"></i>
                        <strong>🛠️ สำหรับนักพัฒนา:</strong><br>
                        • สร้าง method <code>get_available_folders_for_permission()</code> ใน Controller<br>
                        • ตรวจสอบ routing ใน <code>routes.php</code><br>
                        • ตรวจสอบ PHP error logs<br>
                        • ตรวจสอบ Database connection<br>
                        • ตรวจสอบสิทธิ์ Admin ของผู้ใช้ปัจจุบัน
                    </p>
                </div>
            </div>
        `;
    });
}


/**
 * แสดงรายการโฟลเดอร์
 */
function renderFolderList(folders) {
    const container = document.getElementById('folderSelectionList');
    const loadingSpinner = document.getElementById('folderLoadingSpinner');
    const noFoldersMessage = document.getElementById('noFoldersMessage');
    
    loadingSpinner.classList.add('hidden');
    
    if (!folders || folders.length === 0) {
        noFoldersMessage.classList.remove('hidden');
        return;
    }
    
    container.classList.remove('hidden');
    
    let html = '';
    folders.forEach(folder => {
        const iconClass = getFolderIconClass(folder.folder_type);
        const typeText = getFolderTypeText(folder.folder_type);
        
        html += `
            <div class="folder-item p-3 border-b hover:bg-gray-50" data-folder-id="${folder.folder_id}" data-folder-type="${folder.folder_type}">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" 
                           value="${folder.folder_id}" 
                           onchange="updateSelectedFoldersCount()"
                           class="folder-checkbox mr-3 rounded">
                    <div class="flex items-center flex-1">
                        <i class="fas fa-folder ${iconClass} text-lg mr-3"></i>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">${escapeHtml(folder.folder_name)}</div>
                            <div class="text-sm text-gray-500">${typeText}</div>
                            ${folder.current_permission ? 
                                `<div class="text-xs text-blue-600 mt-1">สิทธิ์ปัจจุบัน: ${folder.current_permission}</div>` : 
                                '<div class="text-xs text-gray-400 mt-1">ยังไม่มีสิทธิ์</div>'
                            }
                        </div>
                    </div>
                </label>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

/**
 * กรองโฟลเดอร์
 */
function filterFolders() {
    const searchTerm = document.getElementById('folderSearchInput').value.toLowerCase();
    const selectedType = document.getElementById('folderTypeSelect').value;
    const folderItems = document.querySelectorAll('.folder-item');
    
    folderItems.forEach(item => {
        const folderName = item.querySelector('.font-medium').textContent.toLowerCase();
        const folderType = item.getAttribute('data-folder-type');
        
        const matchesSearch = !searchTerm || folderName.includes(searchTerm);
        const matchesType = selectedType === 'all' || folderType === selectedType;
        
        if (matchesSearch && matchesType) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

/**
 * อัปเดตจำนวนโฟลเดอร์ที่เลือก
 */
function updateSelectedFoldersCount() {
    const selectedCheckboxes = document.querySelectorAll('.folder-checkbox:checked');
    const count = selectedCheckboxes.length;
    const countElement = document.getElementById('selectedCount');
    const summaryElement = document.getElementById('selectedFoldersCount');
    
    if (count > 0) {
        countElement.textContent = count;
        summaryElement.classList.remove('hidden');
    } else {
        summaryElement.classList.add('hidden');
    }
}

/**
 * ดึงรายการโฟลเดอร์ที่เลือก
 */
function getSelectedFolders() {
    const selectedCheckboxes = document.querySelectorAll('.folder-checkbox:checked');
    const selectedFolders = [];
    
    selectedCheckboxes.forEach(checkbox => {
        const folderItem = checkbox.closest('.folder-item');
        const folderName = folderItem.querySelector('.font-medium').textContent;
        const folderType = folderItem.getAttribute('data-folder-type');
        
        selectedFolders.push({
            folder_id: checkbox.value,
            folder_name: folderName,
            folder_type: folderType
        });
    });
    
    return selectedFolders;
}

/**
 * แสดง Modal เลือกระดับสิทธิ์
 */
function showPermissionLevelModal(selectedFolders) {
    Swal.fire({
        title: 'เลือกระดับสิทธิ์',
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <h4 class="font-medium text-gray-800 mb-2">โฟลเดอร์ที่เลือก: ${selectedFolders.length} โฟลเดอร์</h4>
                    <div class="max-h-32 overflow-y-auto bg-gray-50 rounded-lg p-3">
                        ${selectedFolders.map(folder => `
                            <div class="text-sm text-gray-600 mb-1">
                                <i class="fas fa-folder mr-1"></i>${escapeHtml(folder.folder_name)}
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ระดับสิทธิ์:</label>
                    <select id="permissionLevelSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="read_only">อ่านอย่างเดียว (Read Only)</option>
                        <option value="read_write">อ่าน-เขียน (Read & Write)</option>
                        <option value="admin">ผู้ดูแล (Admin)</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="applyToSubfoldersCheck" class="mr-2 rounded">
                        <span class="text-sm text-gray-700">ใช้กับโฟลเดอร์ย่อยด้วย (Apply to Subfolders)</span>
                    </label>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">วันหมดอายุ (ไม่บังคับ):</label>
                    <input type="date" 
                           id="permissionExpiryDate" 
                           min="${new Date().toISOString().split('T')[0]}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        สิทธิ์จะมีผลทันทีหลังจากบันทึก
                    </p>
                </div>
            </div>
        `,
        width: '500px',
        showCancelButton: true,
        confirmButtonText: 'บันทึกสิทธิ์',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#10b981',
        preConfirm: () => {
            const permissionLevel = document.getElementById('permissionLevelSelect').value;
            const applyToSubfolders = document.getElementById('applyToSubfoldersCheck').checked;
            const expiryDate = document.getElementById('permissionExpiryDate').value;
            
            return {
                permission_level: permissionLevel,
                apply_to_subfolders: applyToSubfolders,
                expiry_date: expiryDate || null
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            executeGrantPermissions(selectedFolders, result.value);
        }
    });
}

/**
 * ดำเนินการให้สิทธิ์
 */
function executeGrantPermissions(selectedFolders, permissionConfig) {
    Swal.fire({
        title: 'กำลังเพิ่มสิทธิ์...',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-green-600 border-t-transparent"></div>
                </div>
                <p class="text-gray-600 mb-2">กำลังเพิ่มสิทธิ์ ${selectedFolders.length} โฟลเดอร์...</p>
                <div class="text-sm text-gray-500">
                    <div id="permissionProgress">0 / ${selectedFolders.length}</div>
                </div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    const requestData = {
        user_id: currentUserId,
        folders: selectedFolders,
        permission_level: permissionConfig.permission_level,
        apply_to_subfolders: permissionConfig.apply_to_subfolders,
        expiry_date: permissionConfig.expiry_date
    };
    
    fetch('<?php echo site_url('google_drive_system/grant_bulk_folder_permissions'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        // ✅ 1. Better Error Handling
        const contentType = response.headers.get('content-type');
        
        if (!response.ok) {
            // ✅ 2. HTML Detection
            if (contentType && contentType.includes('text/html')) {
                return response.text().then(html => {
                    console.error('🚨 Server returned HTML instead of JSON:', html.substring(0, 500));
                    throw new Error(`เซิร์ฟเวอร์เกิดข้อผิดพลาด (${response.status}) - ตรวจสอบ server logs`);
                });
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        }
        
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('🚨 Non-JSON response:', text.substring(0, 500));
                throw new Error('เซิร์ฟเวอร์ส่งกลับข้อมูลไม่ถูกต้อง');
            });
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'เพิ่มสิทธิ์เรียบร้อย',
                html: `
                    <div class="text-left">
                        <p class="mb-2 font-medium">ผลการดำเนินการ:</p>
                        <ul class="text-sm space-y-1">
                            <li class="text-green-600">✅ เพิ่มสิทธิ์สำเร็จ: <strong>${data.data.success_count}</strong> โฟลเดอร์</li>
                            <li class="text-blue-600">📁 ระดับสิทธิ์: <strong>${getPermissionLevelText(permissionConfig.permission_level)}</strong></li>
                            ${permissionConfig.apply_to_subfolders ? 
                                '<li class="text-purple-600">📂 รวมโฟลเดอร์ย่อย: <strong>ใช่</strong></li>' : 
                                '<li class="text-gray-600">📂 รวมโฟลเดอร์ย่อย: <strong>ไม่</strong></li>'
                            }
                            ${data.data.failed_count > 0 ? 
                                `<li class="text-red-600">❌ ล้มเหลว: <strong>${data.data.failed_count}</strong> โฟลเดอร์</li>` : ''
                            }
                        </ul>
                    </div>
                `,
                confirmButtonText: 'ตกลง'
            });
            
            // รีโหลดข้อมูลสิทธิ์
            loadUserPermissionData(currentUserId);
        } else {
            throw new Error(data.message || 'ไม่สามารถเพิ่มสิทธิ์ได้');
        }
    })
    .catch(error => {
        console.error('❌ Grant bulk permissions error:', error);
        
        // ✅ 3. Enhanced Error Messages
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            html: `
                <div class="text-left">
                    <p class="mb-3 font-medium text-red-600">ไม่สามารถเพิ่มสิทธิ์ได้</p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                        <p class="text-sm text-red-700">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            ${escapeHtml(error.message)}
                        </p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs text-yellow-700">
                            <i class="fas fa-tools mr-1"></i>
                            <strong>🔧 แนวทางแก้ไข:</strong><br>
                            • ตรวจสอบว่า API <code>grant_bulk_folder_permissions</code> มีอยู่ใน Controller<br>
                            • ตรวจสอบ JSON format ของข้อมูลที่ส่ง<br>
                            • ตรวจสอบ Database tables: <code>tbl_google_drive_member_folder_access</code><br>
                            • ตรวจสอบ PHP memory limit และ execution time<br>
                            • ลองลดจำนวนโฟลเดอร์ที่เลือก (< 10 โฟลเดอร์)<br>
                            • ตรวจสอบ Transaction และ Database locks
                        </p>
                    </div>
                </div>
            `,
            width: '600px',
            confirmButtonText: 'ตกลง'
        });
    });
}


	
	
	/**
 * Helper: แปลงระดับสิทธิ์เป็นข้อความ
 */
function getPermissionLevelText(level) {
    switch (level) {
        case 'read_only': return 'อ่านอย่างเดียว';
        case 'read_write': return 'อ่าน-เขียน';
        case 'admin': return 'ผู้ดูแล';
        default: return level;
    }
}
	

/**
 * กรองโฟลเดอร์ตามประเภท
 */
function filterFolders() {
    const searchInput = document.getElementById('folderSearch');
    const typeFilter = document.getElementById('folderTypeFilter');
    
    if (!searchInput || !typeFilter) {
        console.warn('Filter elements not found');
        return;
    }
    
    const searchTerm = searchInput.value.toLowerCase();
    const selectedType = typeFilter.value;
    
    const folderItems = document.querySelectorAll('.folder-tree-item');
    
    folderItems.forEach(item => {
        const folderName = item.querySelector('h4').textContent.toLowerCase();
        const folderTypeElement = item.querySelector('p');
        const folderType = folderTypeElement ? folderTypeElement.textContent.toLowerCase() : '';
        
        const matchesSearch = !searchTerm || folderName.includes(searchTerm);
        const matchesType = selectedType === 'all' || folderType.includes(selectedType);
        
        if (matchesSearch && matchesType) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

/**
 * ส่งออกประวัติสิทธิ์
 */
function exportPermissionHistory() {
    if (!currentUserId) {
        showToast('ไม่พบข้อมูลผู้ใช้', 'error');
        return;
    }
    
    Swal.fire({
        title: 'ส่งออกประวัติสิทธิ์',
        text: 'ฟีเจอร์นี้กำลังพัฒนา จะเปิดใช้งานในเร็วๆ นี้',
        icon: 'info',
        confirmButtonText: 'เข้าใจแล้ว'
    });
}

// เพิ่ม event listener สำหรับ search folder
document.addEventListener('DOMContentLoaded', function() {
    const folderSearch = document.getElementById('folderSearch');
    if (folderSearch) {
        folderSearch.addEventListener('input', function() {
            filterFolders();
        });
    }
});
	
</script>


<style>
.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

/* เมื่อ Toggle เปิด (checked) */
.toggle-switch input:checked + .toggle-slider {
    background-color: #4CAF50; /* เขียว */
}

.toggle-switch input:focus + .toggle-slider {
    box-shadow: 0 0 1px #4CAF50;
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(20px);
}

/* สำหรับ Storage Access Toggle */
.storage-toggle input:checked + .toggle-slider {
    background-color: #2563eb !important; /* น้ำเงิน */
}

/* สำหรับ Setting Toggles */
.setting-toggle input:checked + .toggle-slider {
    background-color: #10b981 !important; /* เขียว */
}

/* สำหรับ Auto Create Folders */
.auto-create-toggle input:checked + .toggle-slider {
    background-color: #8b5cf6 !important; /* ม่วง */
}

/* Hover Effects */
.toggle-slider:hover {
    opacity: 0.8;
}

/* Disabled State */
.toggle-switch input:disabled + .toggle-slider {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Loading State - ปลอดภัย */
.toggle-loading {
    opacity: 0.6;
    pointer-events: none;
    position: relative;
}

.toggle-loading::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 12px;
    height: 12px;
    border: 2px solid #666;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    z-index: 10;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Safe Mode - Fallback States */
.safe-mode-loading {
    background-color: #fbbf24 !important;
    position: relative;
}

.safe-mode-loading::before {
    content: "⏳";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 12px;
    z-index: 10;
}

.safe-mode-success {
    background-color: #10b981 !important;
    position: relative;
}

.safe-mode-success::before {
    content: "✓";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 14px;
    font-weight: bold;
    z-index: 10;
}

.safe-mode-error {
    background-color: #ef4444 !important;
    position: relative;
}

.safe-mode-error::before {
    content: "✗";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 14px;
    font-weight: bold;
    z-index: 10;
}

/* Success State - เมื่อสร้างโฟลเดอร์สำเร็จ */
.row-success {
    background-color: #f0fdf4 !important;
    border-left: 4px solid #22c55e;
    transition: background-color 0.5s ease;
}

.row-success:hover {
    background-color: #dcfce7 !important;
}

/* Processing State - ขณะกำลังประมวลผล */
.row-processing {
    background-color: #fef3c7 !important;
    border-left: 4px solid #f59e0b;
    position: relative;
}

.row-processing::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #f59e0b, transparent);
    animation: loading-bar 2s infinite;
}

@keyframes loading-bar {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Enhanced Toggle States - Safe Mode */
.toggle-switch.creating input + .toggle-slider {
    background: linear-gradient(45deg, #3b82f6, #1e40af) !important;
    animation: gradient-shift 1.5s infinite;
}

@keyframes gradient-shift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.toggle-switch.success input + .toggle-slider {
    background-color: #10b981 !important;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
    animation: success-glow 0.5s ease-out;
}

@keyframes success-glow {
    0% { box-shadow: 0 0 0 rgba(16, 185, 129, 0); }
    50% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.5); }
    100% { box-shadow: 0 0 10px rgba(16, 185, 129, 0.3); }
}

/* Progress Indicator */
.progress-indicator {
    position: relative;
    width: 100%;
    height: 4px;
    background-color: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
}

.progress-indicator::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 30%;
    background: linear-gradient(90deg, #3b82f6, #1e40af);
    border-radius: 2px;
    animation: progress-slide 2s infinite;
}

@keyframes progress-slide {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(350%); }
}

/* สำหรับ Mobile */
@media (max-width: 768px) {
    .toggle-switch {
        width: 40px;
        height: 22px;
    }
    
    .toggle-slider:before {
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
    }
    
    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(18px);
    }
    
    .toggle-loading::after {
        width: 10px;
        height: 10px;
    }
}

/* เพิ่ม CSS สำหรับสถานะต่างๆ */

/* Success State - เมื่อสร้างโฟลเดอร์สำเร็จ */
.row-success {
    background-color: #f0fdf4 !important;
    border-left: 4px solid #22c55e;
    transition: background-color 0.5s ease;
}

.row-success:hover {
    background-color: #dcfce7 !important;
}

/* Processing State - ขณะกำลังประมวลผล */
.row-processing {
    background-color: #fef3c7 !important;
    border-left: 4px solid #f59e0b;
    position: relative;
}

.row-processing::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #f59e0b, transparent);
    animation: loading-bar 2s infinite;
}

@keyframes loading-bar {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.status-badge.creating {
    background-color: #fbbf24;
    color: #92400e;
    animation: pulse 2s infinite;
}

.status-badge.success {
    background-color: #d1fae5;
    color: #065f46;
}

.status-badge.error {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Folder Status Icons */
.folder-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.folder-status .icon {
    transition: transform 0.2s ease;
}

.folder-status:hover .icon {
    transform: scale(1.1);
}

/* Enhanced Toggle States */
.toggle-switch.creating input + .toggle-slider {
    background: linear-gradient(45deg, #3b82f6, #1e40af);
    animation: gradient-shift 1.5s infinite;
}

@keyframes gradient-shift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.toggle-switch.success input + .toggle-slider {
    background-color: #10b981;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
    animation: success-glow 0.5s ease-out;
}

@keyframes success-glow {
    0% { box-shadow: 0 0 0 rgba(16, 185, 129, 0); }
    50% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.5); }
    100% { box-shadow: 0 0 10px rgba(16, 185, 129, 0.3); }
}

/* SweetAlert2 Custom Styling */
.swal2-popup {
    border-radius: 1rem;
}

.swal2-title {
    font-size: 1.5rem;
    font-weight: 600;
}

.swal2-html-container {
    font-size: 0.9rem;
    line-height: 1.5;
}

/* Loading Spinner in SweetAlert */
.swal2-loading .swal2-loader {
    border-color: #3b82f6 transparent #3b82f6 transparent;
}

/* Progress Indicator */
.progress-indicator {
    position: relative;
    width: 100%;
    height: 4px;
    background-color: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
}

.progress-indicator::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 30%;
    background: linear-gradient(90deg, #3b82f6, #1e40af);
    border-radius: 2px;
    animation: progress-slide 2s infinite;
}

@keyframes progress-slide {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(350%); }
}

/* สำหรับ Mobile */
@media (max-width: 768px) {
    .toggle-switch {
        width: 40px;
        height: 22px;
    }
    
    .toggle-slider:before {
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
    }
    
    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(18px);
    }
}
</style>
<style>
/* ===== Modal Container Layout ===== */
.modal-container {
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.modal-body-container {
    display: flex;
    flex: 1;
    min-height: 0; /* สำคัญสำหรับ flexbox scrolling */
    overflow: hidden;
}

/* ===== Left Sidebar ===== */
.modal-sidebar {
    width: 320px;
    background-color: #f9fafb;
    border-right: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ===== Main Content Area ===== */
.modal-main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
    overflow: hidden;
}

/* ===== Tab Content Container ===== */
.tab-content-container {
    flex: 1;
    overflow: hidden;
    position: relative;
}

/* ===== Tab Content Scrolling ===== */
.tab-scrollable {
    height: 100%;
    overflow-y: auto;
    overflow-x: hidden;
}

/* ===== Scrollable Content Areas ===== */
.scrollable-content {
    overflow-y: auto;
    overflow-x: hidden;
}

.scrollable-content.max-h-96 {
    max-height: 24rem;
}

/* ===== Custom Scrollbar ===== */
.modal-overlay ::-webkit-scrollbar {
    width: 8px;
}

.modal-overlay ::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.modal-overlay ::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
    border: 2px solid #f1f5f9;
}

.modal-overlay ::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Firefox scrollbar */
.modal-overlay * {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

/* ===== Scroll Shadows ===== */
.scroll-shadow {
    position: relative;
}

.scroll-shadow::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 8px;
    background: linear-gradient(to bottom, rgba(0,0,0,0.1), transparent);
    pointer-events: none;
    z-index: 10;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.scroll-shadow::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 8px;
    background: linear-gradient(to top, rgba(0,0,0,0.1), transparent);
    pointer-events: none;
    z-index: 10;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.scroll-shadow.scrolled-top::before {
    opacity: 1;
}

.scroll-shadow.scrolled-bottom::after {
    opacity: 1;
}

/* ===== Responsive Design ===== */
@media (max-width: 1024px) {
    .modal-container {
        max-height: 95vh;
        max-width: 95vw;
    }
    
    .modal-sidebar {
        width: 280px;
    }
}

@media (max-width: 768px) {
    .modal-container {
        max-height: 100vh;
        max-width: 100vw;
        margin: 0;
        border-radius: 0;
    }
    
    .modal-body-container {
        flex-direction: column;
    }
    
    .modal-sidebar {
        width: 100%;
        max-height: 200px;
        border-right: none;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .scrollable-content.max-h-96 {
        max-height: 16rem;
    }
}

/* ===== Loading States ===== */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 20;
}

/* ===== Permission Switch Styles ===== */
.permission-switch {
    position: relative;
    width: 44px;
    height: 24px;
}

.permission-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.permission-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.permission-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .permission-slider {
    background-color: #2563eb;
}

input:checked + .permission-slider:before {
    transform: translateX(20px);
}

/* ===== Tab Button Active State ===== */
.tab-button.active {
    border-bottom-color: #2563eb;
    color: #2563eb;
    background-color: #f8fafc;
}

/* ===== Folder Icons ===== */
.folder-icon {
    color: #f59e0b;
}

.system-folder-icon {
    color: #dc2626;
}

.personal-folder-icon {
    color: #059669;
}

.shared-folder-icon {
    color: #7c3aed;
}

/* ===== Animation for Better UX ===== */
.tab-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ===== Hover Effects ===== */
.permission-badge {
    transition: all 0.2s ease;
}

.permission-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.folder-tree-item {
    transition: all 0.2s ease;
}

.folder-tree-item:hover {
    background-color: #f8fafc;
}

.permission-history-item {
    border-left: 4px solid #e5e7eb;
    transition: all 0.2s ease;
}

.permission-history-item:hover {
    border-left-color: #3b82f6;
    background-color: #f8fafc;
}
</style>

<style>
.modal-overlay {
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.permission-badge {
    transition: all 0.2s ease;
}

.permission-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.folder-tree-item {
    transition: all 0.2s ease;
}

.folder-tree-item:hover {
    background-color: #f8fafc;
}

.permission-switch {
    position: relative;
    width: 44px;
    height: 24px;
}

.permission-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.permission-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.permission-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .permission-slider {
    background-color: #2563eb;
}

input:checked + .permission-slider:before {
    transform: translateX(20px);
}

.tab-button.active {
    border-bottom-color: #2563eb;
    color: #2563eb;
    background-color: #f8fafc;
}

.folder-icon {
    color: #f59e0b;
}

.system-folder-icon {
    color: #dc2626;
}

.personal-folder-icon {
    color: #059669;
}

.shared-folder-icon {
    color: #7c3aed;
}

.permission-history-item {
    border-left: 4px solid #e5e7eb;
    transition: all 0.2s ease;
}

.permission-history-item:hover {
    border-left-color: #3b82f6;
    background-color: #f8fafc;
}

/* Loading States */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    items: center;
    justify-content: center;
    z-index: 10;
}

/* Custom Scrollbar */
.modal-overlay ::-webkit-scrollbar {
    width: 6px;
}

.modal-overlay ::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.modal-overlay ::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.modal-overlay ::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<?php
// Helper function สำหรับ format bytes ใน view
function format_bytes_helper($bytes, $precision = 2) {
    $bytes = max(0, (int)$bytes);
    
    if ($bytes === 0) {
        return '0 B';
    }
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $pow = floor(log($bytes, 1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?> 