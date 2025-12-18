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
                                <button onclick="createFolderStructure()" id="createFolderBtn"
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
                                        <p class="text-green-700 text-sm mt-1">สามารถเริ่มใช้งาน Centralized Storage ได้แล้ว
                                        </p>
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
                                <span class="text-blue-800 font-medium">
                                    <?php echo htmlspecialchars($system_storage->google_account_email); ?>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Storage Name</label>
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <span class="text-gray-800">
                                    <?php echo htmlspecialchars($system_storage->storage_name); ?>
                                </span>
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
                                        style="width: <?php echo min(100, $system_storage->storage_usage_percent); ?>%">
                                    </div>
                                </div>
                                <div class="text-center mt-1">
                                    <span class="text-xs text-gray-500">
                                        <?php echo $system_storage->storage_usage_percent; ?>%
                                    </span>
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

    <!-- User Management Section -->
    <?php if ($system_storage && $setup_status['folder_structure_created']): ?>
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
                            <input type="text" id="searchUsers" placeholder="ค้นหาผู้ใช้..."
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        <select id="filterByStatus" onchange="filterUsers()"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">ทั้งหมด</option>
                            <option value="active">เปิดใช้งาน</option>
                            <option value="inactive">ปิดใช้งาน</option>
                            <option value="has_folder">มีโฟลเดอร์แล้ว</option>
                            <option value="no_folder">ยังไม่มีโฟลเดอร์</option>
                        </select>
                        <select id="filterByPosition" onchange="filterUsers()"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">ทุกตำแหน่ง</option>
                        </select>
                    </div>
                    <div class="text-sm text-gray-600">
                        แสดง <span id="showingCount">0</span> จาก <span id="totalCount">0</span> คน
                    </div>
                </div>

                <!-- User List Table -->
                <div class="overflow-x-auto">
                    <div id="userListLoading" class="text-center py-8">
                        <div
                            class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent">
                        </div>
                        <p class="text-gray-600 mt-2">กำลังโหลดข้อมูลผู้ใช้...</p>
                    </div>

                    <table id="userTable" class="min-w-full bg-white border border-gray-200 rounded-lg hidden">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ผู้ใช้</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ตำแหน่ง</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    สถานะ</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Personal Folder</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    สิทธิ์</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    การดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody" class="bg-white divide-y divide-gray-200">
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
                        แสดง <span id="pageStart">0</span>-<span id="pageEnd">0</span> จาก <span id="pageTotal">0</span>
                        รายการ
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="changePage('prev')" id="prevBtn"
                            class="px-3 py-1 bg-gray-200 text-gray-600 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            ก่อนหน้า
                        </button>
                        <div id="pageNumbers" class="flex space-x-1">
                        </div>
                        <button onclick="changePage('next')" id="nextBtn"
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
                        <input type="checkbox" id="centralizedMode" onchange="toggleStorageMode(this)" <?php echo ($this->config->item('system_storage_mode') === 'centralized') ? 'checked' : ''; ?>
                            class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                        </div>
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
                        <input type="checkbox" id="autoCreateUserFolders"
                            onchange="toggleSetting(this, 'auto_create_user_folders')" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                        </div>
                    </label>
                </div>

                <!-- Default User Quota -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h3 class="font-medium text-gray-800 mb-3">Quota เริ่มต้นสำหรับ User</h3>
                    <div class="flex items-center space-x-4">
                        <select id="defaultUserQuota" onchange="updateSetting('default_user_quota', this.value)"
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
                        <select id="systemStorageLimit" onchange="updateSystemStorageLimit(this.value)"
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

                <!-- Trial Storage Limit -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h3 class="font-medium text-gray-800 mb-3">ขีดจำกัด Trial Storage</h3>
                    <div class="flex items-center space-x-4">
                        <?php
                        $trial_limit = $this->db->select('setting_value')
                            ->from('tbl_google_drive_settings')
                            ->where('setting_key', 'trial_storage_limit')
                            ->where('is_active', 1)
                            ->get()->row();
                        $trial_value = $trial_limit ? $trial_limit->setting_value : '5368709120';
                        ?>
                        <select id="trialStorageLimit" onchange="updateSetting('trial_storage_limit', this.value)"
                            class="px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="1073741824" <?= ($trial_value == '1073741824') ? 'selected' : ''; ?>>1 GB
                            </option>
                            <option value="2147483648" <?= ($trial_value == '2147483648') ? 'selected' : ''; ?>>2 GB
                            </option>
                            <option value="5368709120" <?= ($trial_value == '5368709120') ? 'selected' : ''; ?>>5 GB
                            </option>
                            <option value="10737418240" <?= ($trial_value == '10737418240') ? 'selected' : ''; ?>>10 GB
                            </option>
                            <option value="21474836480" <?= ($trial_value == '21474836480') ? 'selected' : ''; ?>>20 GB
                            </option>
                        </select>
                        <span class="text-sm text-gray-600">สำหรับ Trial Mode</span>
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
                                    <li>• โฟลเดอร์และไฟล์ทั้งหมดใน Google Drive System</li>
                                    <li>• ข้อมูลใน tbl_google_drive_system_folders</li>
                                    <li>• ข้อมูลใน tbl_google_drive_folders</li>
                                    <li>• ข้อมูลใน tbl_google_drive_folder_permissions</li>
                                    <li>• ข้อมูลใน tbl_google_drive_member_folder_access</li>
                                    <li>• ข้อมูลใน tbl_google_drive_logs</li>
                                    <li>• ข้อมูลใน tbl_google_drive_permissions</li>
                                    <li>• ข้อมูลใน tbl_google_drive_settings</li>
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

</div>

<script>
    // JavaScript Functions for System Setup
    let allUsers = [];
    let filteredUsers = [];
    let currentPage = 1;
    const usersPerPage = 30;
    let selectedUsers = new Set();

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
                        createFolderStructure();
                    }
                });
            });
    }

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
                    text: error.message,
                    width: '600px',
                    confirmButtonText: 'ตกลง'
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
                </div>
            </div>
        </div>
    `;
    }

    function generateSuccessMessage(data) {
        const stats = data.stats || {};
        const folders = stats.folders_created || 0;
        const permissions = stats.permissions_assigned || 0;
        const users = stats.users_processed || 0;

        return `
        <div class="text-left">
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
        </div>
    `;
    }

    function generateErrorMessage(errorMessage) {
        return `
        <div class="text-left">
            <p class="mb-4 text-red-600">${escapeHtml(errorMessage)}</p>
        </div>
    `;
    }

    function showDetailedReport(data) {
        Swal.fire({
            title: '📊 รายงานรายละเอียด',
            html: '<div class="text-center">ดูรายละเอียดเพิ่มเติมในคอนโซล</div>',
            width: '700px',
            confirmButtonText: 'ปิด'
        });
    }

    function toggleStorageMode(checkbox, force = false) {
        if (!force && !checkbox.checked) {
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
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                });
            });
    }

    /**
     * ✅ UPDATE Setting แบบใหม่ - ส่งไปที่ save_settings() แทน
     */
    function updateSetting(settingKey, value) {
        fetch('<?php echo site_url('google_drive_system/update_setting_ajax'); ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                setting_key: settingKey,
                value: value
            })
        })
            .then(response => response.json())  // ✅ แน่ใจว่าเป็น JSON
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'อัปเดตการตั้งค่าเรียบร้อย',
                        toast: true,
                        position: 'top-end',
                        timer: 2000
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    toast: true,
                    position: 'top-end',
                    timer: 3000
                });
            });
    }

    function updateSystemStorageLimit(value) {
        updateSetting('system_storage_limit', value);
    }

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

    document.addEventListener('DOMContentLoaded', function () {
        loadCurrentSettings();

        if (document.getElementById('userTable')) {
            loadUserList();
            loadPositionFilter();
        }
    });

    function loadCurrentSettings() {
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

    function loadUserList() {
        console.log('🔍 === loadUserList: Start ===');

        showLoading(true);

        const apiUrl = '<?php echo site_url('google_drive_system/get_all_users_for_management'); ?> ';
        console.log('📡 API URL:', apiUrl);

        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                console.log('📡 Response received:', {
                    status: response.status,
                    statusText: response.statusText,
                    ok: response.ok,
                    headers: {
                        contentType: response.headers.get('content-type')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📦 Raw API Response:', data);
                console.log('✅ Success:', data.success);
                console.log('📝 Message:', data.message);

                if (data.success) {
                    console.log('👥 === User Data Analysis ===');
                    console.log('Total users received:', data.data.users.length);

                    // วิเคราะห์ข้อมูล users
                    if (data.data.users.length > 0) {
                        const firstUser = data.data.users[0];
                        console.log('👤 First user sample:', {
                            m_id: firstUser.m_id,
                            full_name: firstUser.full_name,
                            m_email: firstUser.m_email,
                            storage_access_granted: firstUser.storage_access_granted,
                            has_personal_folder: firstUser.has_personal_folder,
                            personal_folder_id: firstUser.personal_folder_id
                        });

                        // ตรวจสอบ permissions
                        console.log('🔑 === Permissions Check ===');
                        console.log('First user has permissions property?', 'permissions' in firstUser);
                        console.log('First user permissions value:', firstUser.permissions);
                        console.log('First user permissions type:', typeof firstUser.permissions);
                        console.log('First user permissions is array?', Array.isArray(firstUser.permissions));

                        if (Array.isArray(firstUser.permissions)) {
                            console.log('First user permissions count:', firstUser.permissions.length);
                            if (firstUser.permissions.length > 0) {
                                console.log('First permission sample:', firstUser.permissions[0]);
                            }
                        }

                        // นับผู้ใช้ที่มี permissions
                        const usersWithPermissions = data.data.users.filter(u =>
                            Array.isArray(u.permissions) && u.permissions.length > 0
                        );
                        console.log('Users with permissions:', usersWithPermissions.length);

                        // แสดงรายละเอียด users ที่มี permissions (สูงสุด 3 คน)
                        usersWithPermissions.slice(0, 3).forEach((user, index) => {
                            console.log(`User ${index + 1} with permissions:`, {
                                id: user.m_id,
                                name: user.full_name,
                                permissions_count: user.permissions.length,
                                permissions: user.permissions
                            });
                        });

                        // นับผู้ใช้ที่ไม่มี permissions
                        const usersWithoutPermissions = data.data.users.filter(u =>
                            !Array.isArray(u.permissions) || u.permissions.length === 0
                        );
                        console.log('Users WITHOUT permissions:', usersWithoutPermissions.length);

                        if (usersWithoutPermissions.length > 0 && usersWithoutPermissions.length <= 3) {
                            console.log('Sample users without permissions:',
                                usersWithoutPermissions.map(u => ({
                                    id: u.m_id,
                                    name: u.full_name,
                                    permissions: u.permissions
                                }))
                            );
                        }
                    }

                    // วิเคราะห์ stats
                    console.log('📊 === Statistics ===');
                    console.log('Stats received:', data.data.stats);

                    // เก็บข้อมูลลง global variables
                    allUsers = data.data.users || [];
                    filteredUsers = [...allUsers];

                    console.log('✅ allUsers updated, length:', allUsers.length);
                    console.log('✅ filteredUsers updated, length:', filteredUsers.length);

                    // อัพเดท UI
                    updateSummaryStats(data.data.stats);
                    console.log('✅ Summary stats updated');

                    renderUserTable();
                    console.log('✅ User table rendered');

                    showLoading(false);
                    console.log('✅ Loading hidden');

                    console.log('🎉 === loadUserList: Success ===');

                } else {
                    throw new Error(data.message || 'ไม่สามารถโหลดข้อมูลผู้ใช้ได้');
                }
            })
            .catch(error => {
                console.error('❌ === loadUserList: Error ===');
                console.error('Error type:', error.name);
                console.error('Error message:', error.message);
                console.error('Error stack:', error.stack);

                showLoading(false);
                showError('ไม่สามารถโหลดข้อมูลผู้ใช้ได้: ' + error.message);

                console.log('❌ === loadUserList: Failed ===');
            });
    }

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

    function updateSummaryStats(stats) {
        document.getElementById('totalUsers').textContent = stats.total_users || 0;
        document.getElementById('activeUsers').textContent = stats.active_users || 0;
        document.getElementById('usersWithFolders').textContent = stats.users_with_folders || 0;
        document.getElementById('pendingUsers').textContent = stats.pending_users || 0;
    }

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

    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: message,
            confirmButtonText: 'ตกลง'
        });
    }

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
            <div class="text-sm">
                ${renderUserPermissions(user)}
            </div>
        </td>
        <td class="px-4 py-3">
            <div class="flex space-x-2">
                <button onclick="window.location.href='<?= site_url('google_drive_system/user_permissions/') ?>${user.m_id}'" 
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

    //================ Render User Permissions ================
    /**
     * ✅ renderUserPermissions - ใช้ functions ที่แก้ไขแล้ว
     */
    function renderUserPermissions(user) {
        try {
            console.log('=== renderUserPermissions v2: Start ===');
            console.log('User:', user.m_id, user.full_name);

            let permissions = [];

            // ✅ ตรวจสอบหลาย property
            if (user.folder_permissions && Array.isArray(user.folder_permissions)) {
                permissions = user.folder_permissions;
                console.log('Using user.folder_permissions:', permissions.length);
            } else if (user.permissions && Array.isArray(user.permissions)) {
                permissions = user.permissions;
                console.log('Using user.permissions:', permissions.length);
            } else if (user.member_folder_access && Array.isArray(user.member_folder_access)) {
                permissions = user.member_folder_access;
                console.log('Using user.member_folder_access:', permissions.length);
            }

            // ✅ ถ้าไม่มี permissions
            if (permissions.length === 0) {
                if (user.storage_access_granted == 1) {
                    return `
                    <div class="flex flex-col space-y-1">
                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            <i class="fas fa-database mr-1"></i>Storage Access
                        </span>
                        <span class="text-gray-400 text-xs">ยังไม่มีสิทธิ์โฟลเดอร์</span>
                    </div>
                `;
                }

                return `
                <div class="flex items-center">
                    <i class="fas fa-lock text-gray-400 mr-1"></i>
                    <span class="text-gray-500 text-xs">ไม่มีสิทธิ์</span>
                </div>
            `;
            }

            // ✅ กรองเฉพาะ unique permissions
            const uniquePermissions = getUniquePermissions(permissions);
            const totalPermissions = uniquePermissions.length;

            console.log(`Total unique permissions: ${totalPermissions}`);

            // ✅ ถ้ามี 3 รายการหรือน้อยกว่า → แสดงทีละรายการ
            if (totalPermissions <= 3) {
                console.log('Rendering individual permissions (≤3)');

                const permissionItems = uniquePermissions.map(permission => {
                    const { label, colorClass, folderName } = getPermissionStyle(permission);

                    return `
                    <div class="flex items-center space-x-1 mb-1">
                        <span class="inline-block px-2 py-1 ${colorClass} rounded-full text-xs" 
                              title="${getPermissionTooltip(permission)}">
                            ${label}
                        </span>
                        <span class="text-xs text-gray-600 truncate max-w-24" 
                              title="${escapeHtml(folderName)}">
                            ${escapeHtml(folderName)}
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

            // ✅ ถ้ามีมากกว่า 3 → แสดงแบบสรุป
            else {
                console.log('Rendering summary permissions (>3)');

                const permissionSummary = getSummaryByType(uniquePermissions);

                console.log('Permission summary:', permissionSummary);

                return `
                <div class="flex flex-col space-y-1">
                    <div class="flex flex-wrap items-center gap-1">
                        ${permissionSummary.map(summary =>
                    `<span class="inline-block px-2 py-1 ${summary.colorClass} rounded-full text-xs" 
                                   title="${escapeHtml(summary.tooltip)}">
                                ${summary.label}
                            </span>`
                ).join('')}
                    </div>
                    <div class="text-xs text-gray-500">
                        ${totalPermissions} โฟลเดอร์
                    </div>
                </div>
            `;
            }

        } catch (error) {
            console.error('renderUserPermissions error:', error);
            console.error('Error stack:', error.stack);

            return `
            <span class="text-red-500 text-xs">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                ข้อผิดพลาด
            </span>
        `;
        }
    }

    /**
     * ✅ FIXED: getUniquePermissions - ป้องกันการนับซ้ำ
     */
    function getUniquePermissions(permissions) {
        try {
            const seen = new Set();
            const unique = [];

            console.log('=== getUniquePermissions: Before Unique ===');
            console.log('Total permissions:', permissions.length);

            permissions.forEach((permission, index) => {
                if (!permission) {
                    console.warn(`Permission at index ${index} is null/undefined`);
                    return;
                }

                // ✅ สร้าง unique identifier ที่ดีกว่า
                const folderId = permission.folder_id ||
                    permission.folder_table_id ||
                    permission.google_drive_folder_id;

                const folderName = permission.folder_name || permission.name;
                const accessType = permission.access_type || permission.access_level || 'read';

                let key = '';

                // ✅ ลำดับความสำคัญในการสร้าง key:
                // 1. ใช้ folder_id (ถ้ามี)
                // 2. ใช้ folder_name (ถ้าไม่มี folder_id)
                // 3. ใช้ permission ID + timestamp (ถ้าไม่มีทั้ง 2)

                if (folderId) {
                    key = `${folderId}_${accessType}`;
                } else if (folderName && folderName !== 'ไม่ระบุชื่อ' && !folderName.startsWith('Folder ')) {
                    // ถ้ามีชื่อโฟลเดอร์ที่เป็นชื่อจริง (ไม่ใช่ placeholder)
                    key = `name_${folderName}_${accessType}`;
                } else if (permission.id) {
                    // ใช้ permission ID เป็น fallback
                    key = `perm_${permission.id}`;
                } else {
                    // สร้าง unique key จาก index และข้อมูลที่มี
                    key = `unknown_${index}_${accessType}_${Date.now()}_${Math.random()}`;
                }

                if (!seen.has(key)) {
                    seen.add(key);
                    unique.push(permission);
                    console.log(`Added permission: key="${key}", folder="${folderName || folderId}", type="${accessType}"`);
                } else {
                    console.log(`Skipped duplicate: key="${key}", folder="${folderName || folderId}", type="${accessType}"`);
                }
            });

            console.log('=== getUniquePermissions: After Unique ===');
            console.log('Unique permissions:', unique.length);
            console.log('Keys used:', Array.from(seen));

            return unique;

        } catch (error) {
            console.error('getUniquePermissions error:', error);
            return permissions || [];
        }
    }

    function getPermissionStyle(permission) {
        try {
            const accessType = permission.access_type || permission.access_level || permission.permission_level || 'read';
            const folderType = permission.folder_type || 'unknown';

            let folderName = permission.folder_name || permission.name || 'ไม่ระบุชื่อ';

            if (folderName.length > 15) {
                folderName = folderName.substring(0, 12) + '...';
            }

            let colorClass = '';
            let label = '';

            switch (accessType.toLowerCase()) {
                case 'owner':
                    colorClass = 'bg-purple-100 text-purple-800';
                    label = 'Owner';
                    break;
                case 'admin':
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
     * ✅ FIXED: getSummaryByType - แก้ปัญหาการนับซ้ำ
     */
    function getSummaryByType(permissions) {
        try {
            console.log('=== getSummaryByType v2: Start ===');
            console.log('Input permissions:', permissions.length);

            // ✅ สรุปตาม access_type เท่านั้น (ไม่แยกตาม folder_type)
            const summary = {
                owner: {
                    accessType: 'owner',
                    count: 0,
                    folders: [],
                    folderIds: new Set()
                },
                admin: {
                    accessType: 'admin',
                    count: 0,
                    folders: [],
                    folderIds: new Set()
                },
                write: {
                    accessType: 'write',
                    count: 0,
                    folders: [],
                    folderIds: new Set()
                },
                read: {
                    accessType: 'read',
                    count: 0,
                    folders: [],
                    folderIds: new Set()
                }
            };

            permissions.forEach((permission, index) => {
                const accessType = (permission.access_type || permission.access_level || 'read').toLowerCase();
                const folderName = permission.folder_name || permission.name || 'ไม่ระบุชื่อ';

                // ✅ สร้าง unique identifier
                const folderId = permission.folder_id ||
                    permission.folder_table_id ||
                    permission.google_drive_folder_id;

                let folderIdentifier;
                if (folderId) {
                    folderIdentifier = `id_${folderId}`;
                } else if (folderName && folderName !== 'ไม่ระบุชื่อ' && !folderName.startsWith('Folder ')) {
                    folderIdentifier = `name_${folderName}`;
                } else if (permission.id) {
                    folderIdentifier = `perm_${permission.id}`;
                } else {
                    folderIdentifier = `unknown_${index}_${Date.now()}_${Math.random()}`;
                }

                // ✅ จับคู่ access_type กับ category
                let category;
                switch (accessType) {
                    case 'owner':
                        category = 'owner';
                        break;
                    case 'admin':
                        category = 'admin';
                        break;
                    case 'write':
                    case 'writer':
                    case 'read_write':
                        category = 'write';
                        break;
                    case 'read':
                    case 'reader':
                    case 'read_only':
                        category = 'read';
                        break;
                    case 'commenter':
                        // ✅ commenter นับเป็น read
                        category = 'read';
                        break;
                    default:
                        console.warn(`Unknown access_type: ${accessType}, treating as read`);
                        category = 'read';
                }

                // ✅ ตรวจสอบว่าโฟลเดอร์นี้เคยนับแล้วหรือยัง
                if (!summary[category].folderIds.has(folderIdentifier)) {
                    summary[category].folderIds.add(folderIdentifier);
                    summary[category].count++;
                    summary[category].folders.push(folderName);

                    console.log(`[${category}] Added: identifier="${folderIdentifier}", name="${folderName}", count=${summary[category].count}`);
                } else {
                    console.log(`[${category}] Skipped duplicate: identifier="${folderIdentifier}"`);
                }
            });

            console.log('=== getSummaryByType v2: Summary ===');
            Object.keys(summary).forEach(category => {
                if (summary[category].count > 0) {
                    console.log(`${category}: ${summary[category].count} folders`);
                    console.log(`  Names:`, summary[category].folders.slice(0, 3));
                }
            });

            // ✅ สร้าง summary array (เรียงตาม priority)
            const summaryArray = [];

            // Owner
            if (summary.owner.count > 0) {
                summaryArray.push({
                    label: `👑 Owner (${summary.owner.count})`,
                    colorClass: 'bg-purple-100 text-purple-800',
                    tooltip: createTooltip('Owner', summary.owner)
                });
            }

            // Admin
            if (summary.admin.count > 0) {
                summaryArray.push({
                    label: `🔴 Admin (${summary.admin.count})`,
                    colorClass: 'bg-red-100 text-red-800',
                    tooltip: createTooltip('Admin', summary.admin)
                });
            }

            // Write
            if (summary.write.count > 0) {
                summaryArray.push({
                    label: `🟢 Write (${summary.write.count})`,
                    colorClass: 'bg-green-100 text-green-800',
                    tooltip: createTooltip('Write', summary.write)
                });
            }

            // Read
            if (summary.read.count > 0) {
                summaryArray.push({
                    label: `🔵 Read (${summary.read.count})`,
                    colorClass: 'bg-blue-100 text-blue-800',
                    tooltip: createTooltip('Read', summary.read)
                });
            }

            console.log('=== getSummaryByType v2: End ===');
            console.log('Final summary:', summaryArray);

            return summaryArray;

        } catch (error) {
            console.error('getSummaryByType error:', error);
            console.error('Error stack:', error.stack);
            return [];
        }
    }

    /**
 * ✅ Helper: สร้าง tooltip
 */
    function createTooltip(label, summaryData) {
        const count = summaryData.count;
        const folders = summaryData.folders;

        let tooltip = `${label} ใน ${count} โฟลเดอร์:\n`;

        // แสดงชื่อโฟลเดอร์ (สูงสุด 5 รายการ)
        const displayFolders = folders.slice(0, 5);
        tooltip += displayFolders.join(', ');

        // ถ้ามีเกิน 5 รายการ
        if (folders.length > 5) {
            tooltip += `\n...และอีก ${folders.length - 5} โฟลเดอร์`;
        }

        return tooltip;
    }

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

    document.getElementById('searchUsers').addEventListener('input', function () {
        filterUsers();
    });

    function filterUsers() {
        const searchTerm = document.getElementById('searchUsers').value.toLowerCase();
        const statusFilter = document.getElementById('filterByStatus').value;
        const positionFilter = document.getElementById('filterByPosition').value;

        filteredUsers = allUsers.filter(user => {
            const matchesSearch = !searchTerm ||
                (user.full_name && user.full_name.toLowerCase().includes(searchTerm)) ||
                (user.m_email && user.m_email.toLowerCase().includes(searchTerm));

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

            const matchesPosition = positionFilter === 'all' || user.ref_pid == positionFilter;

            return matchesSearch && matchesStatus && matchesPosition;
        });

        currentPage = 1;
        renderUserTable();
    }

    function updatePagination() {
        const totalPages = Math.ceil(filteredUsers.length / usersPerPage);
        const start = (currentPage - 1) * usersPerPage + 1;
        const end = Math.min(currentPage * usersPerPage, filteredUsers.length);

        document.getElementById('pageStart').textContent = start;
        document.getElementById('pageEnd').textContent = end;
        document.getElementById('pageTotal').textContent = filteredUsers.length;

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

    function toggleUserStatus(userId, isActive) {
        try {
            const action = isActive ? 'enable' : 'disable';

            console.log(`🔄 Toggle user ${userId} to ${action}`);

            setToggleLoading(userId, true);
            setRowProcessing(userId, true);

            if (isActive) {
                setToggleCreating(userId, true);
            }

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
                    setToggleLoading(userId, false);
                    setRowProcessing(userId, false);
                    setToggleCreating(userId, false);

                    Swal.close();

                    if (data.success) {
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
                                location.reload();
                            });
                        } else {
                            showToast(data.message || (isActive ? 'เปิดใช้งาน Storage แล้ว' : 'ปิดใช้งาน Storage แล้ว'), 'success');

                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }

                    } else {
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

                    setToggleLoading(userId, false);
                    setRowProcessing(userId, false);
                    setToggleCreating(userId, false);

                    Swal.close();

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

            const toggleElement = document.querySelector(`input[onchange*="${userId}"]`);
            if (toggleElement) {
                toggleElement.checked = !isActive;
            }
        }
    }

    function setToggleLoading(userId, isLoading) {
        try {
            const toggleElements = document.querySelectorAll(`input[onchange*="${userId}"]`);

            if (!toggleElements || toggleElements.length === 0) {
                console.warn(`No toggles found for user ${userId}`);
                return;
            }

            toggleElements.forEach(toggle => {
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

    function bulkAssignPermissions() {
        if (selectedUsers.size === 0) {
            showError('กรุณาเลือกผู้ใช้อย่างน้อย 1 คน');
            return;
        }

        Swal.fire({
            title: 'กำหนดสิทธิ์หลายคน',
            text: 'ฟีเจอร์นี้จะพัฒนาในขั้นตอนถัดไป',
            icon: 'info'
        });
    }

    function refreshUserList() {
        clearSelection();
        loadUserList();
    }

    function viewUserDetails(userId) {
        window.location.href = `<?php echo site_url('google_drive_system/user_details/'); ?>${userId}`;
    }

    function openUserFolder(folderId) {
        if (folderId) {
            window.open(`https://drive.google.com/drive/folders/${folderId}`, '_blank');
        }
    }

    function showToast(message, type = 'info') {
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
            alert(message);
        }
    }

    function escapeHtml(text) {
        if (text === null || text === undefined) {
            return '';
        }

        text = String(text);

        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

<style>
    /* Toggle Switch Styles */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }

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

    .toggle-switch input:checked+.toggle-slider {
        background-color: #4CAF50;
    }

    .toggle-switch input:focus+.toggle-slider {
        box-shadow: 0 0 1px #4CAF50;
    }

    .toggle-switch input:checked+.toggle-slider:before {
        transform: translateX(20px);
    }

    .storage-toggle input:checked+.toggle-slider {
        background-color: #2563eb !important;
    }

    .setting-toggle input:checked+.toggle-slider {
        background-color: #10b981 !important;
    }

    .auto-create-toggle input:checked+.toggle-slider {
        background-color: #8b5cf6 !important;
    }

    .toggle-slider:hover {
        opacity: 0.8;
    }

    .toggle-switch input:disabled+.toggle-slider {
        opacity: 0.5;
        cursor: not-allowed;
    }

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
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }

        100% {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }

    .row-success {
        background-color: #f0fdf4 !important;
        border-left: 4px solid #22c55e;
        transition: background-color 0.5s ease;
    }

    .row-success:hover {
        background-color: #dcfce7 !important;
    }

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
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    .toggle-switch.creating input+.toggle-slider {
        background: linear-gradient(45deg, #3b82f6, #1e40af) !important;
        animation: gradient-shift 1.5s infinite;
    }

    @keyframes gradient-shift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    .toggle-switch.success input+.toggle-slider {
        background-color: #10b981 !important;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
        animation: success-glow 0.5s ease-out;
    }

    @keyframes success-glow {
        0% {
            box-shadow: 0 0 0 rgba(16, 185, 129, 0);
        }

        50% {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
        }

        100% {
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
        }
    }

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

        .toggle-switch input:checked+.toggle-slider:before {
            transform: translateX(18px);
        }

        .toggle-loading::after {
            width: 10px;
            height: 10px;
        }
    }
</style>

<?php
function format_bytes_helper($bytes, $precision = 2)
{
    $bytes = max(0, (int) $bytes);

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