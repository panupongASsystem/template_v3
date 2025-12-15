<?php
/**
 * Google Drive Member Main Content View
 * เนื้อหาหลักของ interface (ไม่รวม header/footer)
 * รวม Enhanced Share Modal
 */
?>

<!-- Trial Warning Banner -->
<?php if (isset($is_trial_mode) && $is_trial_mode): ?>
<div class="trial-warning mx-4 mt-4 rounded-2xl p-4 mb-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-flask text-white text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-orange-800">คุณกำลังใช้งาน Trial Version</h3>
                <p class="text-orange-700">ขีดจำกัดพื้นที่ 1GB • ฟีเจอร์บางอย่างถูกจำกัด • อัปเกรดเพื่อใช้งานเต็มรูปแบบ</p>
            </div>
        </div>
        <button onclick="showUpgradeModal()" class="px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg font-semibold">
            <i class="fas fa-arrow-up mr-2"></i>อัปเกรด
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Main Content -->
<main class="max-w-[80%] mx-auto px-2 sm:px-4 lg:px-6 py-8">    
    <!-- Page Header -->
    <div class="glass-card apple-shadow rounded-3xl p-8 mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex items-center space-x-6">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl flex items-center justify-center shadow-lg animate-float">
                    <i class="fas fa-folder-open text-3xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent mb-2">
                        ระบบจัดเก็บข้อมูลแบบคลาวด์ (Cloud Storage)
                    </h1>
                    <p class="text-gray-600 text-lg">
                        <?php echo htmlspecialchars(get_config_value('fname', 'หน่วยงาน')); ?>
                    </p>
                    <div class="flex items-center mt-2 space-x-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-circle text-green-400 mr-1 text-xs"></i>
                            เชื่อมต่อแล้ว
                        </span>
                        <span class="text-sm text-gray-500" id="connectionStatus">
                            <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
                                โหมดทดลอง - ขีดจำกัด 1GB
                            <?php else: ?>
                                เชื่อมต่อ Google Drive แล้ว
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
			
			
			
			<!-- เพิ่มปุ่มกลับสู่สมาร์ทออฟฟิต ที่มุมขวาบน -->
            <div class="flex items-center space-x-4">
                <!-- กลับสู่สมาร์ทออฟฟิต Button -->
                <a href="<?php echo site_url('User/Choice'); ?>" 
                   class="flex items-center px-6 py-3 text-gray-700 hover:text-blue-600 bg-white/80 hover:bg-blue-50 border border-gray-200 hover:border-blue-300 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg font-medium group">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 group-hover:from-blue-500 group-hover:to-blue-600 rounded-xl flex items-center justify-center mr-3 transition-all duration-300 shadow-sm">
                        <i class="fas fa-home text-blue-600 group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <div class="text-left">
                        <div class="text-sm font-semibold">กลับสู่สมาร์ทออฟฟิต</div>
                        <div class="text-xs text-gray-500 group-hover:text-blue-500 transition-colors duration-300">Smart Office</div>
                    </div>
                </a>
                
                <!-- Mobile Back Button (Hidden on desktop) -->
                <a href="<?php echo site_url('User/Choice'); ?>" 
                   class="lg:hidden p-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 bg-white/80 border border-gray-200 hover:border-blue-300 rounded-xl transition-all duration-300 transform hover:scale-105" 
                   title="กลับสู่สมาร์ทออฟฟิต">
                    <i class="fas fa-home text-lg"></i>
                </a>
            </div>
			
			
        </div>
    </div>

    <!-- Storage Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Personal Quota -->
        <div class="glass-card apple-shadow rounded-2xl p-6 card-hover">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-hdd text-2xl text-green-600"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">พื้นที่ใช้งานของฉัน</h4>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="personalQuotaUsed">-</p>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-3">
                        <div class="bg-gradient-to-r from-green-400 to-green-500 h-2.5 rounded-full transition-all duration-1000" 
                             id="personalQuotaBar" style="width: 0%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1" id="quotaDetails">กำลังโหลด...</p>
                    <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
                    <p class="text-xs text-orange-600 mt-1 font-medium">ทดลองใช้: จำกัด 1GB</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- My Files Count -->
        <div class="glass-card apple-shadow rounded-2xl p-6 card-hover">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-file-alt text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">ไฟล์ของฉัน</h4>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="myFilesCount">0</p>
                    <p class="text-xs text-gray-500 mt-1">ไฟล์ที่อัปโหลด</p>
                </div>
            </div>
        </div>

        <!-- Accessible Folders -->
        <div class="glass-card apple-shadow rounded-2xl p-6 card-hover">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-folder-open text-2xl text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">โฟลเดอร์ที่เข้าถึงได้</h4>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="accessibleFoldersCount">0</p>
                    <p class="text-xs text-gray-500 mt-1">โฟลเดอร์ที่เข้าถึงได้</p>
                </div>
            </div>
        </div>

        <!-- Last Access -->
        <div class="glass-card apple-shadow rounded-2xl p-6 card-hover">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-clock text-2xl text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">กิจกรรมล่าสุด</h4>
                    <p class="text-sm font-bold text-gray-800 mt-1" id="lastAccess">-</p>
                    <p class="text-xs text-gray-500 mt-1">การเข้าใช้ล่าสุด</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb Navigation -->
    <div class="glass-card apple-shadow rounded-2xl p-6 mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl shadow-sm">
                <i class="fas fa-map-marked-alt text-blue-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-1">ตำแหน่งปัจจุบัน</h3>
                <nav class="flex items-center space-x-2 text-sm" id="breadcrumb">
                    <div class="flex items-center space-x-2 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl px-4 py-2">
                        <i class="fas fa-home text-gray-400"></i>
                        <span class="text-gray-400">/</span>
                        <button onclick="loadAccessibleFolders()" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors hover:underline">
                            Google Drive
                        </button>
                        <span class="text-gray-400 flex-wrap" id="breadcrumbPath"></span>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Controls -->
    <div class="glass-card apple-shadow rounded-2xl p-6 mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <!-- Quick Actions Section -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl shadow-sm">
                        <i class="fas fa-bolt text-blue-600"></i>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-3">
                    <button onclick="handleUploadClick()" 
                            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium"
                            id="uploadBtn">
                        <i class="fas fa-cloud-upload-alt mr-2"></i>อัปโหลดไฟล์
                    </button>
                    <button onclick="handleCreateFolderClick()" 
                            class="px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium"
                            id="createFolderBtn">
                        <i class="fas fa-folder-plus mr-2"></i>สร้างโฟลเดอร์
                    </button>
					
					 
                <button onclick="navigateBack()" 
        class="px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium"
        id="backBtn"
        title="ย้อนกลับโฟลเดอร์ก่อนหน้า">
    <i class="fas fa-arrow-left mr-2"></i>ย้อนกลับ
</button>
					
					
                    <button onclick="refreshFiles()" 
                            class="p-2 text-gray-600 bg-white/50 border border-gray-200/50 rounded-xl hover:text-gray-800 hover:bg-white/80 transition-all duration-300 shadow-sm hover:shadow-md"
                            title="รีเฟรช">
                        <i class="fas fa-sync-alt mr-2"></i>รีเฟรช
                    </button>
                </div>
            </div>
            
            <!-- Display Controls -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- View Mode Buttons -->
                <div class="flex items-center bg-white/50 rounded-xl p-1 border border-gray-200/50">
                    <button id="treeViewBtn" onclick="changeViewMode('tree')" 
                            class="view-mode-btn active p-2.5 rounded-lg transition-all duration-200" 
                            title="มุมมองแผนผัง">
                        <i class="fas fa-sitemap text-sm"></i>
                    </button>
                    <button id="gridViewBtn" onclick="changeViewMode('grid')" 
                            class="view-mode-btn p-2.5 rounded-lg transition-all duration-200" 
                            title="มุมมองตาราง">
                        <i class="fas fa-th text-sm"></i>
                    </button>
                    <button id="listViewBtn" onclick="changeViewMode('list')" 
                            class="view-mode-btn p-2.5 rounded-lg transition-all duration-200" 
                            title="มุมมองรายการ">
                        <i class="fas fa-list text-sm"></i>
                    </button>
                </div>
                
                <!-- Sort Buttons -->
                <div class="flex items-center bg-white/50 rounded-xl p-1 border border-gray-200/50">
                    <button id="sortNameBtn" onclick="sortFiles('name')" 
                            class="sort-btn active p-2.5 rounded-lg transition-all duration-200" 
                            title="เรียงตามชื่อ">
                        <i class="fas fa-sort-alpha-down text-sm"></i>
                    </button>
                    <button id="sortDateBtn" onclick="sortFiles('modified')" 
                            class="sort-btn p-2.5 rounded-lg transition-all duration-200" 
                            title="เรียงตามวันที่แก้ไข">
                        <i class="fas fa-sort-numeric-down text-sm"></i>
                    </button>
                    <button id="sortSizeBtn" onclick="sortFiles('size')" 
                            class="sort-btn p-2.5 rounded-lg transition-all duration-200" 
                            title="เรียงตามขนาด">
                        <i class="fas fa-sort-amount-down text-sm"></i>
                    </button>
                    <button id="sortTypeBtn" onclick="sortFiles('type')" 
                            class="sort-btn p-2.5 rounded-lg transition-all duration-200" 
                            title="เรียงตามประเภท">
                        <i class="fas fa-sort text-sm"></i>
                    </button>
                </div>
                
                <!-- Search Input -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchInput" placeholder="ค้นหาไฟล์..." 
                           onkeyup="searchFiles(this.value)"
                           class="text-sm bg-white/50 border border-gray-200/50 rounded-xl pl-10 pr-4 py-2 w-64 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                </div>
            </div>
        </div>
    </div>

    <!-- File Browser -->
    <div class="glass-card apple-shadow rounded-2xl overflow-hidden min-h-96 relative" id="fileBrowserContainer">
        <!-- Folder Tree Sidebar (only in tree view) -->
        <div id="folderTreeSidebar" class="hidden absolute left-0 top-0 w-80 h-full bg-white/90 backdrop-blur-20 border-r border-gray-200/50 z-10">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-sitemap mr-3 text-blue-600"></i>
                    โครงสร้างโฟลเดอร์
                </h3>
                <div id="folderTreeContent" class="space-y-1">
                    <!-- Tree content will be loaded here -->
                </div>
            </div>
        </div>
        
        <!-- Drop Zone Overlay -->
        <div id="dropZoneOverlay" class="hidden absolute inset-0 z-50 bg-gradient-to-br from-blue-500/20 to-purple-500/20 border-4 border-dashed border-blue-500 rounded-2xl flex items-center justify-center apple-blur">
            <div class="text-center animate-bounce">
                <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mb-8 mx-auto shadow-2xl">
                    <i class="fas fa-cloud-upload-alt text-5xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold text-blue-800 mb-3">วางไฟล์ที่นี่</h3>
                <p class="text-blue-600 text-lg">รองรับไฟล์หลายไฟล์พร้อมกัน</p>
                <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
                <p class="text-orange-600 text-sm mt-2 font-medium">ทดลอง: ขีดจำกัด 1GB</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="flex items-center justify-center py-32">
            <div class="text-center">
                <div class="w-20 h-20 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-8 mx-auto"></div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">กำลังโหลดไฟล์...</h3>
                <p class="text-gray-600">กำลังดึงข้อมูลจาก 
                    <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
                        ระบบทดลอง
                    <?php else: ?>
                        Google Drive
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden flex items-center justify-center py-32">
            <div class="text-center max-w-lg mx-auto">
                <div class="w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-8 mx-auto shadow-lg">
                    <i class="fas fa-folder-open text-6xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">ยังไม่มีไฟล์</h3>
                <p class="text-gray-600 mb-8 text-lg">เริ่มต้นด้วยการอัปโหลดไฟล์แรกของคุณ หรือสร้างโฟลเดอร์ใหม่</p>
                <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
                <p class="text-orange-600 mb-4 text-sm font-medium">โหมดทดลอง - สามารถอัปโหลดได้สูงสุด 1GB</p>
                <?php endif; ?>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="handleUploadClick()" 
                            class="px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold">
                        <i class="fas fa-cloud-upload-alt mr-3"></i>อัปโหลดไฟล์แรก
                    </button>
                    <button onclick="handleCreateFolderClick()" 
                            class="px-8 py-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-2xl hover:from-purple-600 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold">
                        <i class="fas fa-folder-plus mr-3"></i>สร้างโฟลเดอร์
                    </button>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <div id="errorState" class="hidden flex items-center justify-center py-32">
            <div class="text-center max-w-lg mx-auto">
                <div class="w-32 h-32 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center mb-8 mx-auto shadow-lg">
                    <i class="fas fa-exclamation-triangle text-6xl text-red-500"></i>
                </div>
                <h3 class="text-2xl font-bold text-red-800 mb-4">เกิดข้อผิดพลาด</h3>
                <p class="text-red-600 mb-8 text-lg" id="errorMessage">ไม่สามารถโหลดข้อมูลได้</p>
                <button onclick="refreshFiles()" 
                        class="px-8 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-2xl hover:from-red-600 hover:to-red-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold">
                    <i class="fas fa-redo mr-3"></i>ลองใหม่อีกครั้ง
                </button>
            </div>
        </div>

        <!-- File List -->
        <div id="fileList" class="hidden p-6" style="margin-left: 0;">
            <!-- Files will be loaded here -->
        </div>
    </div>
</main>

<!-- ✅ Enhanced Permission Info Footer - แสดงสิทธิ์ตามโฟลเดอร์ปัจจุบัน  -->
<footer class="mt-12 mb-8">
    <div class="max-w-[80%] mx-auto px-2 sm:px-4 lg:px-6">
        <div class="glass-card apple-shadow rounded-2xl p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-shield-alt text-white"></i>
                </div>
                สิทธิ์การเข้าถึงของคุณ
                <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
                <span class="trial-badge ml-3">ทดลอง</span>
                <?php endif; ?>
                
                <!-- Real-time Folder Info -->
                <div class="ml-4 flex items-center text-sm">
                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-map-marker-alt text-blue-600 text-xs"></i>
                    </div>
                    <span class="text-gray-600">สำหรับโฟลเดอร์ปัจจุบัน</span>
                </div>
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" id="permissionInfo">
                
                <!-- Current Folder Permissions -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Folder Context -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                        <h4 class="font-semibold text-gray-800 flex items-center mb-4">
                            <i class="fas fa-folder-open text-blue-600 mr-3"></i>
                            ข้อมูลโฟลเดอร์ปัจจุบัน
                        </h4>
                        
                        <div class="space-y-3">
                            <!-- Permission Level Display -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div id="permissionLevel" class="font-semibold text-gray-800 mb-2">
                                    กำลังโหลด...
                                </div>
                                <p class="text-sm text-gray-600" id="permissionDescription">
                                    กำลังตรวจสอบสิทธิ์...
                                </p>
                            </div>

                            <!-- Permission Source & Details -->
                            <div id="permissionDetails" class="hidden bg-white rounded-lg p-4 shadow-sm">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-700">แหล่งที่มาของสิทธิ์:</span>
                                        <span id="permissionSource" class="text-gray-600 ml-2">-</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">ให้สิทธิ์โดย:</span>
                                        <span id="grantedBy" class="text-gray-600 ml-2">-</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">เมื่อ:</span>
                                        <span id="grantedAt" class="text-gray-600 ml-2">-</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">หมดอายุ:</span>
                                        <span id="expiresAt" class="text-gray-600 ml-2">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                        <h5 class="font-medium text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-bolt text-orange-500 mr-2"></i>
                            การกระทำด่วน
                        </h5>
                        <div class="space-y-2">
                            <button onclick="updatePermissionInfoForCurrentFolder()" 
                                    class="w-full flex items-center justify-center px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                <i class="fas fa-sync-alt text-blue-600 mr-2"></i>
                                ตรวจสอบสิทธิ์อีกครั้ง
                            </button>
                            <button onclick="loadAccessibleFolders()" 
                                    class="w-full flex items-center justify-center px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                <i class="fas fa-home text-green-600 mr-2"></i>
                                กลับโฟลเดอร์หลัก
                            </button>
                        </div>
                    </div>
					
					
                </div>
                
                <!-- Available Actions -->
                <div class="space-y-6">
                    <div>
                        <h4 class="font-semibold text-gray-800 flex items-center mb-4">
                            <i class="fas fa-cogs text-purple-600 mr-3"></i>
                            การทำงานที่ใช้ได้
                        </h4>
                        <div class="space-y-3" id="availableActions">
                            <!-- Actions will be loaded here -->
                            <div class="flex items-center justify-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                                <span class="ml-3 text-gray-600">กำลังโหลด...</span>
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
			
			<div class=" pt-6">
                    
                    <div class="flex items-center justify-center mt-6 space-x-6">
                        <a href="<?php echo site_url('google_drive_legal/privacy'); ?>" class="text-gray-400 hover:text-black transition-colors">
                            <i class="fas fa-shield-alt mr-2"></i>นโยบายความเป็นส่วนตัว
                        </a>
                        <a href="<?php echo site_url('google_drive_legal/terms'); ?>" class="text-gray-400 hover:text-black transition-colors">
                            <i class="fas fa-file-contract mr-2"></i>ข้อกำหนดการใช้งาน
                        </a>
                        
                    </div>
                    
                    <div class="mt-6 flex items-center justify-center space-x-4">
                        <div class="flex items-center">
                            <i class="fas fa-shield-check text-green-400 mr-2"></i>
                            <span class="text-sm text-gray-400">PDPA Compliant</span>
                        </div>
                        <div class="w-1 h-1 bg-gray-600 rounded-full"></div>
                        <div class="flex items-center">
                            <i class="fas fa-lock text-blue-400 mr-2"></i>
                            <span class="text-sm text-gray-400">SSL Secured</span>
                        </div>
                        
                        
                    </div>
                </div>
            
            <!-- Trial Mode Warning -->
            <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
            <div class="mt-6 p-6 trial-warning rounded-xl">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-orange-800 mb-2">ข้อจำกัดการทดลอง</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-orange-700">
                                <div class="flex items-center">
                                    <i class="fas fa-hdd text-orange-600 mr-2"></i>
                                    <span>ขีดจำกัดพื้นที่ 1GB</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-share text-orange-600 mr-2"></i>
                                    <span>ไม่สามารถแชร์ไฟล์</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-download text-orange-600 mr-2"></i>
                                    <span>ไม่สามารถดาวน์โหลดไฟล์</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-flask text-orange-600 mr-2"></i>
                                    <span>ข้อมูลตัวอย่างเท่านั้น</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button onclick="showUpgradeModal()" 
                            class="px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 font-medium shadow-lg flex-shrink-0">
                        <i class="fas fa-arrow-up mr-2"></i>
                        อัปเกรด
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- System Status -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            <span>ระบบทำงานปกติ</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-server text-gray-400 mr-2"></i>
                            <span>โหมด: <?php echo isset($storage_mode) ? ($storage_mode === 'centralized' ? 'Centralized' : 'User-based') : 'Unknown'; ?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-clock text-gray-400 mr-2"></i>
                            <span id="lastPermissionCheck">อัปเดตล่าสุด: กำลังโหลด...</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-400">API Version: v1.3.0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>  

<!-- Enhanced Share Modal -->
<div id="shareModal" class="hidden fixed inset-0 bg-black/50 apple-blur flex items-center justify-center z-50 p-4">
    <div class="glass-card apple-shadow-lg rounded-2xl w-full max-w-md mx-4 transform transition-all duration-300">
        
        <!-- Header - Compact -->
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-share-alt text-blue-500 mr-2"></i>แชร์
                <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
                <span class="trial-badge ml-2">ทดลอง</span>
                <?php endif; ?>
            </h3>
            <button onclick="closeShareModal()" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <!-- Trial Warning (Compact) -->
            <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
            <div class="trial-warning rounded-xl p-3">
                <div class="flex items-center">
                    <i class="fas fa-lock text-orange-600 mr-2"></i>
                    <div>
                        <p class="font-medium text-orange-800 text-sm">ข้อจำกัดโหมดทดลอง</p>
                        <p class="text-xs text-orange-700">การแชร์ไฟล์ใช้งานได้เฉพาะเวอร์ชันเต็มเท่านั้น</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- File Info - Simplified -->
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mr-3 shadow-sm" id="shareFileIcon">
                        <i class="fas fa-file text-gray-500"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-medium text-gray-900 truncate" id="shareFileName">ชื่อไฟล์</h4>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="text-xs text-gray-500" id="shareFileSize">ขนาด: -</span>
                            <span class="text-gray-300">•</span>
                            <span class="text-xs text-gray-500" id="shareFileModified">แก้ไข: -</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Share Options - Compact -->
            <div class="space-y-3">
                <!-- Email Share -->
                <div class="share-type-card bg-white rounded-xl p-4 border border-gray-200" id="sharePeopleCard">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-envelope text-purple-600 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">แชร์ผ่านอีเมล</h4>
                            <p class="text-xs text-gray-500">ส่งการเข้าถึงให้บุคคลเฉพาะ</p>
                        </div>
                    </div>
                    
                    <!-- Email Options (Always Visible) -->
                    <div class="mt-4 pt-3 border-t border-gray-100 space-y-3">
                        <input type="email" id="shareEmail" placeholder="name@example.com" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        
                        <div class="flex space-x-2">
                            <button onclick="setEmailPermission('reader')" class="email-permission-btn active flex-1 px-3 py-2 bg-purple-500 text-white rounded-lg text-sm font-medium" data-permission="reader">
                                <i class="fas fa-eye mr-1"></i>ดูได้
                            </button>
                            <button onclick="setEmailPermission('writer')" class="email-permission-btn flex-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium" data-permission="writer">
                                <i class="fas fa-edit mr-1"></i>แก้ไขได้
                            </button>
                        </div>

                        <textarea id="shareMessage" placeholder="เพิ่มข้อความ (ไม่บังคับ)..." 
                                  class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 h-16 resize-none text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                        
                        <button onclick="shareWithEmail()" id="shareEmailBtn" class="w-full px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 font-medium text-sm transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>ส่งการแชร์
                        </button>
                    </div>
                </div>
            </div>

            

        </div>
    </div>
</div>





<!-- Trial Upgrade Modal -->
<?php if (isset($is_trial_mode) && $is_trial_mode): ?>
<div id="upgradeModal" class="hidden fixed inset-0 bg-black/50 apple-blur flex items-center justify-center z-50 p-4">
    <div class="glass-card apple-shadow-lg rounded-3xl p-8 w-full max-w-4xl mx-4 transform transition-all duration-300">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-3xl font-bold text-gray-800 flex items-center">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-arrow-up text-white"></i>
                </div>
                อัปเกรดแผนการใช้งาน
            </h3>
            <button onclick="closeUpgradeModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Pricing Plans -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- 100GB Plan -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 hover:border-blue-300 transition-all duration-300 transform hover:-translate-y-1">
                <div class="text-center">
                    <h4 class="text-xl font-bold text-gray-800 mb-2">100 GB</h4>
                    <p class="text-2xl font-bold text-green-600 mb-6">฿2,000<span class="text-lg font-normal text-gray-600">/ปี</span></p>
                   
                    
                    <div class="space-y-3 mb-6 text-left">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>พื้นที่เก็บข้อมูล 100 GB
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>เชื่อมต่อ API เข้าถึง Google Drive
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>ตัวเลือกในการเพิ่มสิทธิ์ของสมาชิกเว็บไชต์
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>แชร์ข้อมูลภายในองค์กรราคาประหยัด
                        </div>
                    </div>
                    
                    <button onclick="selectPlan('100GB')" class="w-full px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-300 font-semibold">
                        ฿2,000 / ปี
                    </button>
                </div>
            </div>

            <!-- 200GB Plan (Recommended) -->
            <div class="bg-white rounded-2xl p-6 border-2 border-blue-500 relative hover:border-blue-600 transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                    <span class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-1 rounded-full text-sm font-semibold">แนะนำ</span>
                </div>
                <div class="text-center">
                    <h4 class="text-xl font-bold text-gray-800 mb-2">200 GB</h4>
                    
                   
                    <p class="text-2xl font-bold text-green-600 mb-6">฿3,500<span class="text-lg font-normal text-gray-600">/ปี</span></p>
                    
                    <div class="space-y-3 mb-6 text-left">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>พื้นที่เก็บข้อมูล 200 GB
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>เชื่อมต่อ API เข้าถึง Google Drive
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>ตัวเลือกในการเพิ่มสิทธิ์ของสมาชิกเว็บไชต์
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>แชร์ข้อมูลภายในองค์กรราคาประหยัด
                        </div>
                    </div>
                    
                    <button onclick="selectPlan('200GB')" class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 font-semibold shadow-lg">
                        ฿3,500 / ปี
                    </button>
                </div>
            </div>

            <!-- 2TB Plan -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 hover:border-purple-300 transition-all duration-300 transform hover:-translate-y-1">
                <div class="text-center">
                    <h4 class="text-xl font-bold text-gray-800 mb-2">2 TB</h4>
                    
                 
                    <p class="text-2xl font-bold text-green-600 mb-6">฿7,500<span class="text-lg font-normal text-gray-600">/ปี</span></p>
                    
                    <div class="space-y-3 mb-6 text-left">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>พื้นที่เก็บข้อมูล 2 TB
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>เชื่อมต่อ API เข้าถึง Google Drive
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>ตัวเลือกในการเพิ่มสิทธิ์ของสมาชิกเว็บไชต์
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check text-green-500 mr-3"></i>แชร์ข้อมูลภายในองค์กรราคาประหยัด
                        </div>
                    </div>
                    
                    <button onclick="selectPlan('2TB')" class="w-full px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-300 font-semibold">
                        ฿7,500 / ปี
                    </button>
                </div>
            </div>
        </div>

        <!-- Google One Benefits -->
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-6 mb-6">
            <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/0/09/Google_One_logo.svg" alt="Google One" class="w-6 h-6 mr-3">
                Google Drive มาพร้อมกับ
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div class="flex items-center">
                    <i class="fas fa-plug  text-purple-600 mr-3"></i>
                    <span class="text-sm">เชื่อมต่อ API เข้าถึง Google Drive</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-shield-alt text-blue-600 mr-3"></i>
                    <span class="text-sm">ตัวเลือกในการเพิ่มสิทธิ์ของสมาชิกเว็บไชต์</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-users text-purple-600 mr-3"></i>
                    <span class="text-sm">แชร์ข้อมูลภายในองค์กรราคาประหยัด</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="closeUpgradeModal()" class="px-8 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-semibold transition-colors">
                ใช้ Trial ต่อ
            </button>
            <button onclick="contactAdmin()" class="px-8 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-300 font-semibold shadow-lg">
                <i class="fas fa-phone mr-2"></i>ติดต่อฝ่ายขาย
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black/50 apple-blur flex items-center justify-center z-50 p-4">
    <div class="glass-card apple-shadow-lg rounded-3xl p-8 w-full max-w-lg mx-4 transform transition-all duration-300">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-cloud-upload-alt text-white"></i>
                </div>
                อัปโหลดไฟล์
                <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
                <span class="trial-badge ml-3">ทดลอง</span>
                <?php endif; ?>
            </h3>
            <button onclick="closeUploadModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Trial Warning in Upload Modal -->
        <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
        <div class="trial-warning rounded-xl p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-orange-600 mr-3"></i>
                <div>
                    <p class="font-semibold text-orange-800">โหมดทดลอง</p>
                    <p class="text-sm text-orange-700">ขีดจำกัดพื้นที่ 1GB • ไฟล์จะถูกจัดเก็บในระบบทดลอง</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">อัปโหลดไปที่:</label>
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-4 border border-blue-200/50">
                <div class="flex items-center">
                    <i class="fas fa-folder text-blue-600 mr-3 text-lg"></i>
                    <div>
                        <span class="text-blue-800 font-semibold" id="currentFolderDisplay">โฟลเดอร์ของฉัน</span>
                        <p class="text-blue-600 text-sm mt-1">โฟลเดอร์ปัจจุบัน</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">เลือกไฟล์:</label>
            <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-blue-400 hover:bg-blue-50/50 transition-all duration-300 cursor-pointer" 
                 id="modalDropZone" onclick="document.getElementById('fileInput').click()">
                <input type="file" id="fileInput" multiple class="hidden" 
                       accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar" 
                       onchange="handleFileSelect(this)">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-cloud-upload-alt text-3xl text-blue-600"></i>
                </div>
                <p class="text-gray-700 font-semibold mb-2">คลิกเพื่อเลือกไฟล์ หรือลากไฟล์มาวางที่นี่</p>
                <p class="text-sm text-gray-500 mb-1">รองรับ: PDF, Word, Excel, PowerPoint, รูปภาพ, ข้อความ, ZIP</p>
                <p class="text-xs text-gray-400">
                    ขนาดสูงสุด 100MB ต่อไฟล์
                    <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
                    • ทดลอง: ขีดจำกัด 1GB รวม
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div id="selectedFiles" class="hidden mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">ไฟล์ที่เลือก:</h4>
            <div id="selectedFilesList" class="space-y-2 max-h-32 overflow-y-auto bg-gray-50 rounded-xl p-3">
                <!-- Selected files will appear here -->
            </div>
        </div>

        <div class="flex gap-3">
            <button onclick="closeUploadModal()" 
                    class="flex-1 px-6 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-semibold transition-colors">
                ยกเลิก
            </button>
            <button onclick="startUpload()" id="uploadStartBtn" disabled
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed font-semibold transition-all duration-200">
                อัปโหลดไฟล์
            </button>
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div id="createFolderModal" class="hidden fixed inset-0 bg-black/50 apple-blur flex items-center justify-center z-50 p-4">
    <div class="glass-card apple-shadow-lg rounded-3xl p-8 w-full max-w-md mx-4 transform transition-all duration-300">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-folder-plus text-white"></i>
                </div>
                สร้างโฟลเดอร์
                <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
                <span class="trial-badge ml-3">ทดลอง</span>
                <?php endif; ?>
            </h3>
            <button onclick="closeCreateFolderModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">สร้างที่:</label>
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-4 border border-purple-200/50">
                <div class="flex items-center">
                    <i class="fas fa-folder text-purple-600 mr-3 text-lg"></i>
                    <div>
                        <span class="text-purple-800 font-semibold" id="createFolderParentDisplay">โฟลเดอร์ของฉัน</span>
                        <p class="text-purple-600 text-sm mt-1">โฟลเดอร์ปัจจุบัน</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">ชื่อโฟลเดอร์:</label>
            <input type="text" id="newFolderName" placeholder="ใส่ชื่อโฟลเดอร์..." 
                   class="w-full bg-white/80 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                   onkeypress="if(event.key==='Enter') createNewFolder()">
        </div>

        <div class="flex gap-3">
            <button onclick="closeCreateFolderModal()" 
                    class="flex-1 px-6 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-semibold transition-colors">
                ยกเลิก
            </button>
            <button onclick="createNewFolder()" 
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:from-purple-600 hover:to-purple-700 font-semibold transition-all duration-200">
                สร้างโฟลเดอร์
            </button>
        </div>
    </div>
</div>
