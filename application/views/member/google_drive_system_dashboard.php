<?php
// application/views/member/google_drive_system_dashboard.php (Complete Version with UI Improvements)
?>
<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">🗄️ System Storage Dashboard</h1>
            <p class="text-gray-600 mt-2">จัดการ Centralized Google Drive Storage</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="refreshDashboard()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>รีเฟรช
            </button>

        </div>
    </div>

    <!-- Quick Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- System Storage Status -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">System Storage</p>
                    <p class="text-2xl font-bold">
                        <?php if ($system_storage): ?>
                            <?php if ($system_storage->folder_structure_created): ?>
                                พร้อมใช้งาน
                            <?php else: ?>
                                ตั้งค่าไม่สมบูรณ์
                            <?php endif; ?>
                        <?php else: ?>
                            ยังไม่ได้ตั้งค่า
                        <?php endif; ?>
                    </p>
                </div>
                <div class="text-3xl opacity-80">
                    <?php if ($system_storage && $system_storage->folder_structure_created): ?>
                        <i class="fas fa-check-circle"></i>
                    <?php else: ?>
                        <i class="fas fa-exclamation-triangle"></i>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">ผู้ใช้งานที่ใช้งาน</p>
                    <p class="text-2xl font-bold">
                        <?php echo isset($storage_stats['connected_members']) ? number_format($storage_stats['connected_members']) : '0'; ?>
                    </p>
                </div>
                <div class="text-3xl opacity-80">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Total Folders -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">จำนวนโฟลเดอร์</p>
                    <p class="text-2xl font-bold">
                        <?php echo isset($storage_stats['total_folders']) ? number_format($storage_stats['total_folders']) : '0'; ?>
                    </p>
                </div>
                <div class="text-3xl opacity-80">
                    <i class="fas fa-folder"></i>
                </div>
            </div>
        </div>

        <!-- Storage Usage -->
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm">การใช้งาน Storage</p>
                    <p class="text-2xl font-bold">
                        <?php if ($system_storage): ?>
                            <?php echo number_format($system_storage->storage_usage_percent, 1); ?>%
                        <?php else: ?>
                            0%
                        <?php endif; ?>
                    </p>
                </div>
                <div class="text-3xl opacity-80">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- System Storage Info -->
    <?php if ($system_storage): ?>
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">ข้อมูล System Storage</h2>
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-sm text-green-600 font-medium">ออนไลน์</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Storage Details -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Google Account</label>
                            <div class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <i class="fab fa-google text-blue-600 mr-3"></i>
                                <span
                                    class="text-blue-800 font-medium"><?php echo htmlspecialchars($system_storage->google_account_email); ?></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Storage Name</label>
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <span
                                    class="text-gray-800"><?php echo htmlspecialchars($system_storage->storage_name); ?></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">สถานะโครงสร้าง</label>
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <?php if ($system_storage->folder_structure_created): ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>สร้างแล้ว
                                    </span>
                                <?php else: ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times mr-1"></i>ยังไม่สร้าง
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Storage Usage Chart -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">การใช้งาน Storage</label>
                            <div
                                class="p-4 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm text-indigo-600">ใช้งาน</span>
                                    <span class="text-sm font-medium text-indigo-800">
                                        <?php
                                        if (isset($system_storage->total_storage_used_formatted)) {
                                            echo $system_storage->total_storage_used_formatted . ' / ' . $system_storage->max_storage_limit_formatted;
                                        } else {
                                            echo format_bytes($system_storage->total_storage_used) . ' / ' . format_bytes($system_storage->max_storage_limit);
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-4 shadow-inner">
                                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-4 rounded-full transition-all duration-1000 ease-out shadow-sm"
                                        style="width: <?php echo min(100, $system_storage->storage_usage_percent); ?>%">
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <span
                                        class="text-lg font-bold text-indigo-700"><?php echo number_format($system_storage->storage_usage_percent, 2); ?>%</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">สถิติการใช้งาน</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-center hover:bg-blue-100 transition-colors">
                                    <div class="text-2xl font-bold text-blue-600">
                                        <?php echo number_format($system_storage->total_folders); ?>
                                    </div>
                                    <div class="text-sm text-blue-800">โฟลเดอร์</div>
                                </div>
                                <div
                                    class="p-4 bg-green-50 border border-green-200 rounded-lg text-center hover:bg-green-100 transition-colors">
                                    <div class="text-2xl font-bold text-green-600">
                                        <?php echo number_format($system_storage->total_files); ?>
                                    </div>
                                    <div class="text-sm text-green-800">ไฟล์</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Setup Required -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 mb-8 text-center">
            <div class="mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-yellow-800 mb-2">ต้องตั้งค่า System Storage</h3>
            <p class="text-yellow-700 mb-6">กรุณาตั้งค่า Google Account หลักและสร้างโครงสร้างโฟลเดอร์ก่อนใช้งาน</p>
            <a href="<?php echo site_url('google_drive_system/setup'); ?>"
                class="inline-flex items-center px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                <i class="fas fa-cog mr-2"></i>ไปตั้งค่า
            </a>
        </div>
    <?php endif; ?>

    <!-- ==================== IMPROVED: Recent Activities Section with Export & Filter ==================== -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8 overflow-hidden">
        <!-- Header with gradient background -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-gray-200">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <!-- Title -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-history text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">กิจกรรมล่าสุด</h2>
                        <p class="text-sm text-gray-600">ติดตามกิจกรรมและการเปลี่ยนแปลงของระบบ</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-2">
                    <!-- Date Range Filter -->
                    <div class="flex items-center space-x-2 bg-white border border-gray-300 rounded-lg px-3 py-2">
                        <i class="fas fa-calendar text-gray-500"></i>
                        <input type="date" id="filterStartDate" class="border-0 focus:ring-0 text-sm"
                            placeholder="เริ่มต้น">
                        <span class="text-gray-400">-</span>
                        <input type="date" id="filterEndDate" class="border-0 focus:ring-0 text-sm"
                            placeholder="สิ้นสุด">
                        <button onclick="applyDateFilter()"
                            class="ml-2 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>

                    <!-- Export Button -->
                    <button onclick="exportActivitiesToCSV()"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-file-csv"></i>
                        <span class="font-medium">Export CSV</span>
                    </button>

                    <!-- Refresh Button -->
                    <button onclick="loadRecentActivities()"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-blue-400 hover:text-blue-600 transition-all duration-300 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-sync-alt"></i>
                        <span class="font-medium">รีเฟรช</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Activities Content -->
        <div class="p-6">
            <div id="recentActivities" class="space-y-3">
                <!-- Recent activities will be loaded here -->
                <div class="text-center py-12 text-gray-500">
                    <div
                        class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-300 border-t-blue-600 mb-4">
                    </div>
                    <p class="text-gray-600 font-medium">กำลังโหลดกิจกรรมล่าสุด...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== IMPROVED: Folder Structure Section ==================== -->
    <?php if ($system_storage && $system_storage->folder_structure_created): ?>
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header with gradient background -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-sitemap text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">โครงสร้างโฟลเดอร์</h2>
                            <p class="text-sm text-gray-600">แสดงโครงสร้างการจัดเก็บข้อมูลของระบบ</p>
                        </div>
                    </div>
                    <button onclick="loadFolderStructure()"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-purple-400 hover:text-purple-600 transition-all duration-300 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-sync-alt"></i>
                        <span class="font-medium">รีเฟรช</span>
                    </button>
                </div>
            </div>

            <!-- Folder Structure Content -->
            <div class="p-6 bg-gray-50">
                <div id="folderStructure" class="bg-white rounded-lg border border-gray-200 p-4">
                    <!-- Folder structure will be loaded here -->
                    <div class="text-center py-12 text-gray-500">
                        <div
                            class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-300 border-t-purple-600 mb-4">
                        </div>
                        <p class="text-gray-600 font-medium">กำลังโหลดโครงสร้างโฟลเดอร์...</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="fixed bottom-6 right-6 space-y-3">
        <button onclick="openFileManager()"
            class="w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 hover:scale-110 flex items-center justify-center"
            title="จัดการไฟล์">
            <i class="fas fa-folder text-xl"></i>
        </button>
        <button onclick="openUserManager()"
            class="w-14 h-14 bg-green-600 text-white rounded-full shadow-lg hover:bg-green-700 transition-all duration-300 hover:scale-110 flex items-center justify-center"
            title="จัดการผู้ใช้">
            <i class="fas fa-users text-xl"></i>
        </button>
        <button onclick="openSettings()"
            class="w-14 h-14 bg-gray-600 text-white rounded-full shadow-lg hover:bg-gray-700 transition-all duration-300 hover:scale-110 flex items-center justify-center"
            title="ตั้งค่า">
            <i class="fas fa-cog text-xl"></i>
        </button>
    </div>
</div>

<script>
    // ==================== Dashboard JavaScript Functions (Complete Fixed Version) ====================

    function refreshDashboard() {
        showLoadingOverlay();
        setTimeout(() => {
            location.reload();
        }, 500);
    }

    function showLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        overlay.innerHTML = `
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="text-gray-700">กำลังรีเฟรช...</span>
        </div>
    `;
        document.body.appendChild(overlay);
    }

    // ==================== NEW: Date Filter Function ====================
    /**
     * Apply date filter
     */
    function applyDateFilter() {
        const startDate = document.getElementById('filterStartDate');
        const endDate = document.getElementById('filterEndDate');

        if (!startDate || !endDate) {
            console.error('Date filter elements not found');
            return;
        }

        if (!startDate.value || !endDate.value) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกวันที่',
                text: 'กรุณาเลือกวันที่เริ่มต้นและสิ้นสุด',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        loadRecentActivities(startDate.value, endDate.value);
    }

    // ==================== FIXED: Load Recent Activities (รองรับ date filter) ====================
    /**
     * Load recent activities with optional date filter
     */
    function loadRecentActivities(startDate = null, endDate = null) {
        const container = document.getElementById('recentActivities');

        if (!container) {
            console.error('recentActivities container not found');
            return;
        }

        // ถ้าไม่ส่ง parameter มา ให้ดึงจาก input
        if (!startDate || !endDate) {
            const startInput = document.getElementById('filterStartDate');
            const endInput = document.getElementById('filterEndDate');

            if (startInput && startInput.value) startDate = startInput.value;
            if (endInput && endInput.value) endDate = endInput.value;
        }

        // Loading state
        container.innerHTML = `
        <div class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-300 border-t-blue-600 mb-4"></div>
            <p class="text-gray-600 font-medium">กำลังโหลดข้อมูล...</p>
        </div>
    `;

        // Build URL with filters
        let url = '<?php echo site_url('google_drive_system/get_recent_activities'); ?>';
        const params = new URLSearchParams();

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        if (params.toString()) {
            url += '?' + params.toString();
        }

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Activities response:', data);

                if (data.success && data.data && data.data.length > 0) {
                    let html = '';
                    data.data.forEach(activity => {
                        html += createImprovedActivityItem(activity);
                    });
                    container.innerHTML = html;

                    // Apply dynamic styles
                    setTimeout(() => applyDynamicStyles(), 100);
                } else {
                    container.innerHTML = `
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-history text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-600 font-medium text-lg">ไม่พบกิจกรรม</p>
                    <p class="text-gray-500 text-sm mt-2">ไม่มีกิจกรรมในช่วงเวลาที่เลือก</p>
                </div>
            `;
                }
            })
            .catch(error => {
                console.error('Load activities error:', error);
                container.innerHTML = `
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
                </div>
                <p class="text-red-600 font-semibold text-lg mb-2">ไม่สามารถโหลดข้อมูลได้</p>
                <p class="text-gray-600 text-sm mb-4">Error: ${error.message}</p>
                <button onclick="loadRecentActivities()" 
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                    <i class="fas fa-redo mr-2"></i>ลองใหม่อีกครั้ง
                </button>
            </div>
        `;
            });
    }

    // ==================== NEW: Export to CSV ====================
    /**
     * Export activities to CSV with Thai language support
     */
    function exportActivitiesToCSV() {
        const startInput = document.getElementById('filterStartDate');
        const endInput = document.getElementById('filterEndDate');

        const startDate = startInput ? startInput.value : null;
        const endDate = endInput ? endInput.value : null;

        // Build URL with filters
        let url = '<?php echo site_url('google_drive_system/export_activities_csv'); ?>';
        const params = new URLSearchParams();

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        if (params.toString()) {
            url += '?' + params.toString();
        }

        // Show loading
        Swal.fire({
            title: 'กำลังสร้างไฟล์...',
            html: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Download file
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('Export failed');
                return response.blob();
            })
            .then(blob => {
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'activities_log_' + new Date().toISOString().split('T')[0] + '.csv';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);

                Swal.fire({
                    icon: 'success',
                    title: 'Export สำเร็จ',
                    text: 'ดาวน์โหลดไฟล์เรียบร้อยแล้ว',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Export error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Export ล้มเหลว',
                    text: 'ไม่สามารถ export ข้อมูลได้',
                    confirmButtonColor: '#3b82f6'
                });
            });
    }

    // ==================== Activity Item Creation ====================
    /**
     * Create improved activity item with modern card design
     */
    function createImprovedActivityItem(activity) {
        const timeAgo = formatTimeAgo(activity.created_at || activity.timestamp);
        const icon = getActivityIcon(activity.action_type || activity.type);
        const colorClasses = getActivityColorClasses(activity.action_type || activity.type);
        const username = activity.username || activity.user_name || activity.member_name || 'ระบบ';
        const description = activity.action_description || activity.description || 'ไม่มีรายละเอียด';

        // ✨ NEW: เพิ่ม badge สำหรับ file activities
        let typeBadge = '';
        if (activity.source === 'google_drive_file_activities') {
            typeBadge = `
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                <i class="fas fa-file-alt mr-1"></i>
                File Activity
            </span>
        `;
        }

        return `
        <div class="group relative bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 improved-activity-card" data-color="${colorClasses.borderColor}">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-5 rounded-xl transition-opacity duration-300 improved-activity-overlay" data-gradient="${colorClasses.gradientFrom},${colorClasses.gradientTo}"></div>
            
            <div class="relative flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-xl shadow-md flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300 improved-activity-icon" data-gradient="${colorClasses.gradientFrom},${colorClasses.gradientTo}">
                        <i class="${icon} text-xl text-white"></i>
                    </div>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-4 mb-2">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900 leading-snug">${description}</p>
                            ${typeBadge}
                        </div>
                        <span class="flex-shrink-0 inline-flex items-center px-2.5 py-1 bg-gray-100 text-xs font-medium text-gray-600 rounded-full">
                            <i class="far fa-clock mr-1"></i>
                            ${timeAgo}
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center improved-user-icon" data-bg="${colorClasses.lightBg}" data-text="${colorClasses.darkText}">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                        <p class="text-sm text-gray-600">${username}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    }
    
    // ==================== Activity Styling Functions ====================
    /**
     * Get color classes for activity type (รองรับ File Activities)
     */
    function getActivityColorClasses(type) {
        const colorSchemes = {
            // ========== Existing Activities ==========
            'upload_file': {
                gradientFrom: '#3b82f6',
                gradientTo: '#2563eb',
                borderColor: 'rgb(147, 197, 253)',
                lightBg: 'rgb(219, 234, 254)',
                darkText: 'rgb(37, 99, 235)'
            },
            'delete_file': {
                gradientFrom: '#ef4444',
                gradientTo: '#dc2626',
                borderColor: 'rgb(252, 165, 165)',
                lightBg: 'rgb(254, 226, 226)',
                darkText: 'rgb(220, 38, 38)'
            },
            'create_folder': {
                gradientFrom: '#10b981',
                gradientTo: '#059669',
                borderColor: 'rgb(134, 239, 172)',
                lightBg: 'rgb(209, 250, 229)',
                darkText: 'rgb(5, 150, 105)'
            },
            'delete_folder': {
                gradientFrom: '#ef4444',
                gradientTo: '#dc2626',
                borderColor: 'rgb(252, 165, 165)',
                lightBg: 'rgb(254, 226, 226)',
                darkText: 'rgb(220, 38, 38)'
            },
            'grant_permission': {
                gradientFrom: '#8b5cf6',
                gradientTo: '#7c3aed',
                borderColor: 'rgb(196, 181, 253)',
                lightBg: 'rgb(237, 233, 254)',
                darkText: 'rgb(124, 58, 237)'
            },
            'connect': {
                gradientFrom: '#10b981',
                gradientTo: '#059669',
                borderColor: 'rgb(134, 239, 172)',
                lightBg: 'rgb(209, 250, 229)',
                darkText: 'rgb(5, 150, 105)'
            },
            'disconnect': {
                gradientFrom: '#ef4444',
                gradientTo: '#dc2626',
                borderColor: 'rgb(252, 165, 165)',
                lightBg: 'rgb(254, 226, 226)',
                darkText: 'rgb(220, 38, 38)'
            },
            'system_update': {
                gradientFrom: '#f59e0b',
                gradientTo: '#d97706',
                borderColor: 'rgb(253, 230, 138)',
                lightBg: 'rgb(254, 243, 199)',
                darkText: 'rgb(217, 119, 6)'
            },

            // ========== ✨ NEW: File Activities ==========
            'upload': {  // File upload
                gradientFrom: '#3b82f6',
                gradientTo: '#2563eb',
                borderColor: 'rgb(147, 197, 253)',
                lightBg: 'rgb(219, 234, 254)',
                darkText: 'rgb(37, 99, 235)'
            },
            'edit': {  // File edit
                gradientFrom: '#f59e0b',
                gradientTo: '#d97706',
                borderColor: 'rgb(253, 230, 138)',
                lightBg: 'rgb(254, 243, 199)',
                darkText: 'rgb(217, 119, 6)'
            },
            'delete': {  // File delete
                gradientFrom: '#ef4444',
                gradientTo: '#dc2626',
                borderColor: 'rgb(252, 165, 165)',
                lightBg: 'rgb(254, 226, 226)',
                darkText: 'rgb(220, 38, 38)'
            },
            'download': {  // File download
                gradientFrom: '#14b8a6',
                gradientTo: '#0d9488',
                borderColor: 'rgb(153, 246, 228)',
                lightBg: 'rgb(204, 251, 241)',
                darkText: 'rgb(13, 148, 136)'
            },
            'share': {  // File share
                gradientFrom: '#8b5cf6',
                gradientTo: '#7c3aed',
                borderColor: 'rgb(196, 181, 253)',
                lightBg: 'rgb(237, 233, 254)',
                darkText: 'rgb(124, 58, 237)'
            },
            'rename': {  // File rename
                gradientFrom: '#06b6d4',
                gradientTo: '#0891b2',
                borderColor: 'rgb(165, 243, 252)',
                lightBg: 'rgb(207, 250, 254)',
                darkText: 'rgb(8, 145, 178)'
            },
            'move': {  // File move
                gradientFrom: '#84cc16',
                gradientTo: '#65a30d',
                borderColor: 'rgb(217, 249, 157)',
                lightBg: 'rgb(236, 252, 203)',
                darkText: 'rgb(101, 163, 13)'
            },
            'copy': {  // File copy
                gradientFrom: '#0ea5e9',
                gradientTo: '#0284c7',
                borderColor: 'rgb(186, 230, 253)',
                lightBg: 'rgb(224, 242, 254)',
                darkText: 'rgb(2, 132, 199)'
            },
            'view': {  // File view
                gradientFrom: '#6b7280',
                gradientTo: '#4b5563',
                borderColor: 'rgb(209, 213, 219)',
                lightBg: 'rgb(243, 244, 246)',
                darkText: 'rgb(75, 85, 99)'
            },

            'default': {
                gradientFrom: '#6b7280',
                gradientTo: '#4b5563',
                borderColor: 'rgb(209, 213, 219)',
                lightBg: 'rgb(243, 244, 246)',
                darkText: 'rgb(75, 85, 99)'
            }
        };
        return colorSchemes[type] || colorSchemes.default;
    }

    /**
     * Get icon for activity type (รองรับ File Activities)
     */
    function getActivityIcon(type) {
        const icons = {
            // ========== Existing Activities ==========
            'upload_file': 'fas fa-upload',
            'delete_file': 'fas fa-trash-alt',
            'create_folder': 'fas fa-folder-plus',
            'delete_folder': 'fas fa-folder-minus',
            'grant_permission': 'fas fa-user-plus',
            'revoke_permission': 'fas fa-user-minus',
            'connect': 'fas fa-link',
            'disconnect': 'fas fa-unlink',
            'system_update': 'fas fa-sync-alt',
            'sync_files': 'fas fa-sync',

            // ========== ✨ NEW: File Activities ==========
            'upload': 'fas fa-cloud-upload-alt',      // File upload
            'edit': 'fas fa-edit',                     // File edit
            'delete': 'fas fa-trash-alt',              // File delete
            'download': 'fas fa-download',             // File download
            'share': 'fas fa-share-alt',               // File share
            'rename': 'fas fa-i-cursor',               // File rename
            'move': 'fas fa-folder-open',              // File move
            'copy': 'fas fa-copy',                     // File copy
            'view': 'fas fa-eye',                      // File view

            'default': 'fas fa-info-circle'
        };
        return icons[type] || icons.default;
    }

    /**
     * Get Thai label for activity type (รองรับ File Activities)
     */
    function getActivityTypeLabel(type) {
        const labels = {
            // ========== Existing Activities ==========
            'upload_file': 'อัพโหลดไฟล์',
            'delete_file': 'ลบไฟล์',
            'create_folder': 'สร้างโฟลเดอร์',
            'delete_folder': 'ลบโฟลเดอร์',
            'grant_permission': 'ให้สิทธิ์',
            'revoke_permission': 'ถอนสิทธิ์',
            'connect': 'เชื่อมต่อ',
            'disconnect': 'ตัดการเชื่อมต่อ',
            'system_update': 'อัพเดทระบบ',

            // ========== ✨ NEW: File Activities ==========
            'upload': 'อัพโหลด',
            'edit': 'แก้ไข',
            'delete': 'ลบ',
            'download': 'ดาวน์โหลด',
            'share': 'แชร์',
            'rename': 'เปลี่ยนชื่อ',
            'move': 'ย้าย',
            'copy': 'คัดลอก',
            'view': 'ดู'
        };
        return labels[type] || type;
    }

    // ==================== Folder Structure Functions ====================
    function loadFolderStructure() {
        const container = document.getElementById('folderStructure');

        if (!container) {
            console.error('folderStructure container not found');
            return;
        }

        container.innerHTML = `
        <div class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-300 border-t-purple-600 mb-4"></div>
            <p class="text-gray-600 font-medium">กำลังโหลดข้อมูล...</p>
        </div>
    `;

        fetch('<?php echo site_url('google_drive_system/get_folder_structure'); ?>', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    let html = '<div class="space-y-1">';
                    data.data.forEach(folder => {
                        html += createImprovedFolderItem(folder);
                    });
                    html += '</div>';
                    container.innerHTML = html;

                    // Apply dynamic styles
                    setTimeout(() => applyDynamicStyles(), 100);
                } else {
                    loadMockFolderStructure();
                }
            })
            .catch(error => {
                console.error('Folder structure error:', error);
                loadMockFolderStructure();
            });
    }

    function createImprovedFolderItem(folder) {
        const indent = (folder.level || 0) * 24;
        const iconData = getFolderIconData(folder.folder_name || folder.name);
        const isSystem = folder.folder_type === 'system';
        const hasChildren = folder.has_children || false;

        return `
        <div class="group relative hover:bg-gradient-to-r hover:from-purple-50 hover:to-transparent rounded-lg transition-all duration-200"
             style="margin-left: ${indent}px;">
            ${folder.level > 0 ? `
                <div class="absolute left-0 top-0 bottom-1/2 w-px bg-gray-300"></div>
                <div class="absolute left-0 top-1/2 w-4 h-px bg-gray-300"></div>
            ` : ''}
            
            <div class="relative flex items-center py-3 px-4 cursor-pointer">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg shadow-sm flex items-center justify-center mr-3 transform group-hover:scale-110 transition-transform duration-200 improved-folder-icon" data-gradient="${iconData.gradientFrom},${iconData.gradientTo}">
                    <i class="${iconData.icon} text-white"></i>
                </div>
                
                <div class="flex-1 flex items-center justify-between">
                    <span class="font-medium text-gray-800 group-hover:text-purple-700 transition-colors">
                        ${folder.folder_name || folder.name}
                    </span>
                    <div class="flex items-center space-x-2">
                        ${isSystem ? '<span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">System</span>' : ''}
                        ${hasChildren ? '<i class="fas fa-chevron-right text-gray-400 text-sm group-hover:text-purple-600 transition-colors"></i>' : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    }

    function getFolderIconData(folderName) {
        const icons = {
            'Organization Drive': { icon: 'fas fa-building', gradientFrom: '#3b82f6', gradientTo: '#2563eb' },
            'Admin': { icon: 'fas fa-user-shield', gradientFrom: '#ef4444', gradientTo: '#dc2626' },
            'Departments': { icon: 'fas fa-sitemap', gradientFrom: '#3b82f6', gradientTo: '#4f46e5' },
            'Shared': { icon: 'fas fa-share-alt', gradientFrom: '#10b981', gradientTo: '#059669' },
            'Users': { icon: 'fas fa-users', gradientFrom: '#8b5cf6', gradientTo: '#7c3aed' },
            'ผู้บริหาร': { icon: 'fas fa-crown', gradientFrom: '#f59e0b', gradientTo: '#d97706' },
            'คณาจารย์': { icon: 'fas fa-chalkboard-teacher', gradientFrom: '#10b981', gradientTo: '#059669' },
            'เจ้าหน้าที่': { icon: 'fas fa-user-tie', gradientFrom: '#6366f1', gradientTo: '#4f46e5' },
            'default': { icon: 'fas fa-folder', gradientFrom: '#f59e0b', gradientTo: '#d97706' }
        };
        return icons[folderName] || icons.default;
    }

    function loadMockFolderStructure() {
        const container = document.getElementById('folderStructure');
        const mockStructure = [
            { name: 'Organization Drive', type: 'root', level: 0, has_children: true, folder_type: 'system' },
            { name: 'Admin', type: 'folder', level: 1, has_children: false, folder_type: 'system' },
            { name: 'Departments', type: 'folder', level: 1, has_children: true, folder_type: 'system' },
            { name: 'ผู้บริหาร', type: 'folder', level: 2, has_children: false },
            { name: 'คณาจารย์', type: 'folder', level: 2, has_children: false },
            { name: 'เจ้าหน้าที่', type: 'folder', level: 2, has_children: false },
            { name: 'Shared', type: 'folder', level: 1, has_children: false, folder_type: 'system' },
            { name: 'Users', type: 'folder', level: 1, has_children: false, folder_type: 'system' }
        ];

        let html = '<div class="space-y-1">';
        mockStructure.forEach(item => {
            html += createImprovedFolderItem(item);
        });
        html += '</div>';

        container.innerHTML = html;

        // Apply dynamic styles
        setTimeout(() => applyDynamicStyles(), 100);
    }

    // ==================== Utility Functions ====================
    function formatTimeAgo(dateString) {
        if (!dateString) return 'ไม่ทราบเวลา';

        try {
            const date = new Date(dateString);
            const now = new Date();
            const diffInMs = now - date;
            const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
            const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60));
            const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24));

            if (diffInMinutes < 1) {
                return 'เมื่อสักครู่';
            } else if (diffInMinutes < 60) {
                return diffInMinutes + ' นาทีที่แล้ว';
            } else if (diffInHours < 24) {
                return diffInHours + ' ชั่วโมงที่แล้ว';
            } else if (diffInDays < 7) {
                return diffInDays + ' วันที่แล้ว';
            } else {
                return date.toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
        } catch (e) {
            return 'ไม่ทราบเวลา';
        }
    }

    // ==================== Quick Actions ====================
    function openFileManager() {
        window.open('<?php echo site_url('google_drive_system/files'); ?>', '_blank');
    }

    function openUserManager() {
        window.open('<?php echo site_url('google_drive_system/users'); ?>', '_blank');
    }

    function openSettings() {
        window.location.href = '<?php echo site_url('google_drive_system/setup'); ?>';
    }

    // ==================== Apply Dynamic Styles ====================
    function applyDynamicStyles() {
        // Apply gradient to activity cards
        document.querySelectorAll('.improved-activity-card').forEach(card => {
            const borderColor = card.getAttribute('data-color');
            if (borderColor) {
                card.addEventListener('mouseenter', function () {
                    this.style.borderColor = borderColor;
                });
                card.addEventListener('mouseleave', function () {
                    this.style.borderColor = 'rgb(229, 231, 235)';
                });
            }
        });

        // Apply gradient to activity overlays
        document.querySelectorAll('.improved-activity-overlay').forEach(overlay => {
            const gradientData = overlay.getAttribute('data-gradient');
            if (gradientData) {
                const [from, to] = gradientData.split(',');
                overlay.style.background = `linear-gradient(to right, ${from}, ${to})`;
            }
        });

        // Apply gradient to activity icons
        document.querySelectorAll('.improved-activity-icon').forEach(icon => {
            const gradientData = icon.getAttribute('data-gradient');
            if (gradientData) {
                const [from, to] = gradientData.split(',');
                icon.style.background = `linear-gradient(to bottom right, ${from}, ${to})`;
            }
        });

        // Apply colors to user icons
        document.querySelectorAll('.improved-user-icon').forEach(icon => {
            const bg = icon.getAttribute('data-bg');
            const text = icon.getAttribute('data-text');
            if (bg) icon.style.backgroundColor = bg;
            if (text) icon.style.color = text;
        });

        // Apply gradient to folder icons
        document.querySelectorAll('.improved-folder-icon').forEach(icon => {
            const gradientData = icon.getAttribute('data-gradient');
            if (gradientData) {
                const [from, to] = gradientData.split(',');
                icon.style.background = `linear-gradient(to bottom right, ${from}, ${to})`;
            }
        });
    }

    // ==================== Initialize on Page Load ====================
    document.addEventListener('DOMContentLoaded', function () {
        // Set default date range (last 7 days)
        const today = new Date();
        const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);

        const filterEndDate = document.getElementById('filterEndDate');
        const filterStartDate = document.getElementById('filterStartDate');

        if (filterEndDate) filterEndDate.valueAsDate = today;
        if (filterStartDate) filterStartDate.valueAsDate = weekAgo;

        <?php if ($system_storage): ?>
            setTimeout(() => {
                loadRecentActivities();
                <?php if ($system_storage->folder_structure_created): ?>
                    loadFolderStructure();
                <?php endif; ?>
            }, 1000);
        <?php endif; ?>

        // Apply dynamic styles after elements are created
        setTimeout(() => {
            applyDynamicStyles();
        }, 1500);
    });

    // ==================== Auto-refresh ====================
    // Auto-refresh every 5 minutes
    setInterval(function () {
        if (document.getElementById('recentActivities') && document.visibilityState === 'visible') {
            loadRecentActivities();
        }
    }, 5 * 60 * 1000);
</script>

<!-- Enhanced CSS Styles for Google Drive Dashboard (COMPLETE VERSION) -->
<style>
    /* ===== Base Enhancements ===== */
    body {
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }

    /* ===== Loading Animation ===== */
    .loading-spinner {
        animation: spin 1s linear infinite;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        width: 20px;
        height: 20px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .loading-dots::after {
        content: '';
        animation: loading-dots 1.5s infinite;
    }

    @keyframes loading-dots {

        0%,
        20% {
            content: '';
        }

        40% {
            content: '.';
        }

        60% {
            content: '..';
        }

        80%,
        100% {
            content: '...';
        }
    }

    /* ===== Enhanced Card Styles ===== */
    .dashboard-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(229, 231, 235, 0.8);
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .dashboard-card:hover {
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .status-card {
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .status-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
        pointer-events: none;
    }

    .status-card:hover {
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        transform: scale(1.02);
    }

    /* ===== Storage Usage Bar Enhanced ===== */
    .storage-bar {
        width: 100%;
        background: rgba(209, 213, 219, 0.3);
        border-radius: 8px;
        height: 16px;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
    }

    .storage-progress {
        height: 100%;
        border-radius: 8px;
        transition: all 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .storage-progress::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% {
            left: -100%;
        }

        100% {
            left: 100%;
        }
    }

    /* Storage levels with different colors */
    .storage-progress.low {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .storage-progress.medium {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .storage-progress.high {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .storage-progress.critical {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    /* ===== Activity Item Styles Enhanced ===== */
    .activity-item {
        display: flex;
        align-items: flex-start;
        padding: 16px;
        background: rgba(249, 250, 251, 0.8);
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .activity-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.05), transparent);
        transition: left 0.5s ease;
    }

    .activity-item:hover::before {
        left: 100%;
    }

    .activity-item:hover {
        background: rgba(243, 244, 246, 0.9);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transform: translateX(4px);
        border-color: rgba(59, 130, 246, 0.2);
    }

    .activity-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }

    .activity-item:hover .activity-icon {
        transform: rotate(10deg) scale(1.1);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    /* ===== Folder Structure Styles Enhanced ===== */
    .folder-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        border: 1px solid transparent;
    }

    .folder-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: width 0.3s ease;
        border-radius: 8px 0 0 8px;
    }

    .folder-item:hover::before {
        width: 4px;
    }

    .folder-item:hover {
        background: rgba(249, 250, 251, 0.8);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateX(4px);
        border-color: rgba(59, 130, 246, 0.2);
    }

    .folder-icon {
        margin-right: 12px;
        transition: all 0.3s ease;
    }

    .folder-item:hover .folder-icon {
        transform: scale(1.1);
    }

    /* ===== Quick Action Buttons Enhanced ===== */
    .quick-action-btn {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        color: white;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .quick-action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 100%);
        border-radius: 50%;
    }

    .quick-action-btn:hover {
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        transform: scale(1.15) rotate(5deg);
    }

    .quick-action-btn:active {
        transform: scale(1.05) rotate(5deg);
    }

    /* ===== Enhanced Gradient Backgrounds ===== */
    .gradient-blue {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .gradient-green {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .gradient-purple {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }

    .gradient-indigo {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .gradient-orange {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    /* ===== Status Indicators Enhanced ===== */
    .status-online {
        width: 12px;
        height: 12px;
        background: #10b981;
        border-radius: 50%;
        position: relative;
    }

    .status-online::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        border: 2px solid #10b981;
        border-radius: 50%;
        animation: pulse-ring 2s infinite;
    }

    @keyframes pulse-ring {
        0% {
            transform: scale(0.8);
            opacity: 1;
        }

        100% {
            transform: scale(1.4);
            opacity: 0;
        }
    }

    .status-warning {
        width: 12px;
        height: 12px;
        background: #f59e0b;
        border-radius: 50%;
        animation: pulse-yellow 2s infinite;
    }

    .status-error {
        width: 12px;
        height: 12px;
        background: #ef4444;
        border-radius: 50%;
        animation: pulse-red 2s infinite;
    }

    @keyframes pulse-yellow {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
            transform: scale(1);
        }

        50% {
            box-shadow: 0 0 0 10px rgba(245, 158, 11, 0);
            transform: scale(1.1);
        }
    }

    @keyframes pulse-red {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            transform: scale(1);
        }

        50% {
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            transform: scale(1.1);
        }
    }

    /* ===== Statistics Cards Enhanced ===== */
    .stat-card {
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .stat-card:hover::before {
        left: 100%;
    }

    .stat-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 0.875rem;
        opacity: 0.8;
    }

    /* ===== Loading Overlay Enhanced ===== */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
    }

    .loading-content {
        background: white;
        border-radius: 12px;
        padding: 32px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        animation: fadeInScale 0.3s ease-out;
    }

    @keyframes fadeInScale {
        0% {
            opacity: 0;
            transform: scale(0.8);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* ===== Toast Notifications Enhanced ===== */
    .toast {
        position: fixed;
        top: 24px;
        right: 24px;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateX(400px);
        opacity: 0;
    }

    .toast.show {
        transform: translateX(0);
        opacity: 1;
    }

    .toast.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .toast.error {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .toast.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    .toast.info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    /* ===== Fade In Animation Enhanced ===== */
    .fade-in {
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(30px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== Slide In Animation Enhanced ===== */
    .slide-in {
        animation: slideInLeft 0.5s ease-out;
    }

    @keyframes slideInLeft {
        0% {
            transform: translateX(-100%);
            opacity: 0;
        }

        100% {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* ===== Bounce Animation Enhanced ===== */
    .bounce-in {
        animation: bounceInScale 0.8s ease-out;
    }

    @keyframes bounceInScale {
        0% {
            transform: scale(0.3);
            opacity: 0;
        }

        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }

        70% {
            transform: scale(0.9);
            opacity: 0.9;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* ===== Error State Enhanced ===== */
    .error-state {
        text-align: center;
        padding: 48px 24px;
        color: #ef4444;
    }

    .error-icon {
        font-size: 4rem;
        color: #fca5a5;
        margin-bottom: 16px;
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    .error-message {
        color: #dc2626;
        margin-bottom: 16px;
        font-weight: 600;
    }

    .error-details {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 8px;
    }

    /* ===== Empty State Enhanced ===== */
    .empty-state {
        text-align: center;
        padding: 64px 24px;
        color: #6b7280;
    }

    .empty-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 16px;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .empty-message {
        color: #4b5563;
        margin-bottom: 16px;
        font-weight: 500;
    }

    .empty-action {
        margin-top: 16px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .empty-action:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }

    /* ===== Success State Enhanced ===== */
    .success-state {
        text-align: center;
        padding: 48px 24px;
        color: #10b981;
    }

    .success-icon {
        font-size: 4rem;
        color: #6ee7b7;
        margin-bottom: 16px;
        animation: checkmark 0.6s ease-in-out;
    }

    @keyframes checkmark {
        0% {
            transform: scale(0);
            opacity: 0;
        }

        50% {
            transform: scale(1.2);
            opacity: 0.8;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .success-message {
        color: #059669;
        margin-bottom: 16px;
        font-weight: 600;
    }

    /* ===== Modern Glassmorphism Effect Enhanced ===== */
    .glass-card {
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        position: relative;
        overflow: hidden;
    }

    .glass-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
    }

    /* ===== Custom Scrollbar Enhanced ===== */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(243, 244, 246, 0.5);
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%);
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
    }

    /* ===== Responsive Enhancements ===== */
    @media (max-width: 768px) {
        .dashboard-card {
            margin: 0 8px;
        }

        .status-card {
            padding: 16px;
        }

        .stat-number {
            font-size: 1.5rem;
        }

        .quick-action-btn {
            width: 56px;
            height: 56px;
        }

        .activity-item {
            padding: 12px;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
        }

        .folder-item {
            padding: 10px 12px;
        }
    }

    /* ===== Print Styles ===== */
    @media print {

        .quick-action-btn,
        .loading-overlay,
        .toast {
            display: none !important;
        }

        .dashboard-card {
            box-shadow: none !important;
            border: 1px solid #e5e7eb !important;
        }

        .status-card {
            background: #f3f4f6 !important;
            color: #374151 !important;
        }
    }
</style>

<?php
// Helper function for formatting bytes
if (!function_exists('format_bytes')) {
    function format_bytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
?>