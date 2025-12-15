<?php
// application/views/member/google_drive_system_files.php (Enhanced UX - Click to Open Files)
?>
<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">📁 จัดการสิทธิ์ โฟลเดอร์และไฟล์ </h1>
            <p class="text-gray-600 mt-2">จัดการสิทธิ์ โฟลเดอร์และไฟล์  และจัดการไฟล์ใน Centralized Google Drive Storage</p>
        </div>
        <div class="flex space-x-3">
            
            <button onclick="showUploadModal()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                    id="uploadBtn"
                    <?php echo (!$system_storage || !$system_storage->folder_structure_created) ? 'disabled' : ''; ?>>
                <i class="fas fa-upload mr-2"></i>อัปโหลดไฟล์
            </button>
            <button onclick="showCreateFolderModal()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    id="createFolderBtn"
                    <?php echo (!$system_storage || !$system_storage->folder_structure_created) ? 'disabled' : ''; ?>>
                <i class="fas fa-folder-plus mr-2"></i>สร้างโฟลเดอร์
            </button>
            <button onclick="refreshFileList()" 
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>รีเฟรช
            </button>
        </div>
    </div>

    <!-- System Status Check -->
    <?php if (!$system_storage): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-6 py-4 rounded-lg mb-8">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-2xl mr-4"></i>
                <div>
                    <h3 class="font-semibold">ระบบยังไม่พร้อมใช้งาน</h3>
                    <p class="mt-1">กรุณาตั้งค่า System Storage ก่อนใช้งาน</p>
                    <div class="mt-3">
                        <a href="<?php echo site_url('google_drive_system/setup'); ?>" 
                           class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            ไปตั้งค่า System Storage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif (!$system_storage->folder_structure_created): ?>
        <div class="bg-orange-100 border border-orange-400 text-orange-800 px-6 py-4 rounded-lg mb-8">
            <div class="flex items-center">
                <i class="fas fa-cogs text-2xl mr-4"></i>
                <div>
                    <h3 class="font-semibold">โครงสร้างโฟลเดอร์ยังไม่พร้อม</h3>
                    <p class="mt-1">กรุณาสร้างโครงสร้างโฟลเดอร์ก่อนใช้งาน</p>
                    <div class="mt-3">
                        <a href="<?php echo site_url('google_drive_system/setup'); ?>" 
                           class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                            สร้างโครงสร้างโฟลเดอร์
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Storage Summary -->
    <?php if ($system_storage): ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Storage -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl">
                    <i class="fas fa-hdd text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">พื้นที่ใช้งาน</h4>
                    <p class="text-xl font-semibold text-gray-800">
                        <?php echo isset($system_storage->total_storage_used_formatted) ? $system_storage->total_storage_used_formatted : '0 B'; ?>
                    </p>
                    <p class="text-sm text-gray-500">
                        / <?php echo isset($system_storage->max_storage_limit_formatted) ? $system_storage->max_storage_limit_formatted : '100 GB'; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Folders -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-green-100 to-green-50 rounded-xl">
                    <i class="fas fa-folder text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">โฟลเดอร์ทั้งหมด</h4>
                    <p class="text-xl font-semibold text-gray-800">
                        <?php echo $system_storage->total_folders ?? 0; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Files -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-50 rounded-xl">
                    <i class="fas fa-file text-2xl text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ไฟล์ทั้งหมด</h4>
                    <p class="text-xl font-semibold text-gray-800" id="totalFilesCount">
                        <?php echo $system_storage->total_files ?? 0; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-orange-100 to-orange-50 rounded-xl">
                    <i class="fas fa-users text-2xl text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ผู้ใช้งาน</h4>
                    <p class="text-xl font-semibold text-gray-800">
                        <?php echo $system_storage->active_users ?? 0; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigation Breadcrumb -->
    <div class="bg-white rounded-lg shadow border border-gray-100 p-4 mb-6">
        <nav class="flex items-center space-x-2 text-sm" id="breadcrumb">
            <i class="fas fa-home text-gray-400"></i>
            <span class="text-gray-400">/</span>
            <button onclick="loadRootFolders()" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                Organization Drive
            </button>
            <span class="text-gray-400" id="breadcrumbPath"></span>
        </nav>
    </div>

    

    <!-- File Browser -->
    <div class="bg-white rounded-lg shadow border border-gray-100" id="fileBrowserContainer">
        <!-- Toolbar -->
        <div class="border-b border-gray-200 p-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">ดู:</label>
                        <select id="viewMode" onchange="changeViewMode(this.value)" 
                                class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500">
                            <option value="list">รายการ</option>
                            <option value="grid">ตาราง</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">เรียง:</label>
                        <select id="sortBy" onchange="sortFiles(this.value)" 
                                class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500">
                            <option value="name">ชื่อ</option>
                            <option value="modified">วันที่แก้ไข</option>
                            <option value="size">ขนาด</option>
                            <option value="type">ประเภท</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <input type="text" id="searchInput" placeholder="🔍 ค้นหาไฟล์..." 
                           onkeyup="searchFiles(this.value)"
                           class="text-sm border border-gray-300 rounded px-3 py-1 w-48 focus:ring-2 focus:ring-blue-500">
                    <button onclick="refreshFileList()" 
                            class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded transition-colors"
                            title="รีเฟรช">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Drop Zone Overlay (Hidden by default) -->
        <div id="dropZoneOverlay" class="hidden absolute inset-0 z-50 bg-blue-500 bg-opacity-20 border-4 border-dashed border-blue-500 rounded-lg flex items-center justify-center backdrop-blur-sm">
            <div class="text-center animate-bounce">
                <i class="fas fa-cloud-upload-alt text-6xl text-blue-600 mb-4"></i>
                <h3 class="text-2xl font-bold text-blue-800 mb-2">วางไฟล์ที่นี่เพื่ออัปโหลด</h3>
                <p class="text-blue-700">รองรับไฟล์หลายไฟล์พร้อมกัน</p>
            </div>
        </div>

        <!-- File List Container -->
        <div id="fileListContainer" class="min-h-96 relative">
            <!-- Loading State -->
            <div id="loadingState" class="flex items-center justify-center py-16">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                    <p class="text-gray-600">กำลังโหลดไฟล์จาก Google Drive...</p>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="hidden flex items-center justify-center py-16">
                <div class="text-center">
                    <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-800 mb-2">โฟลเดอร์ว่าง</h3>
                    <p class="text-gray-600 mb-4">ยังไม่มีไฟล์ในโฟลเดอร์นี้</p>
                    <?php if ($system_storage && $system_storage->folder_structure_created): ?>
                    <div class="space-y-2">
                        <button onclick="showUploadModal()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-upload mr-2"></i>อัปโหลดไฟล์แรก
                        </button>
                        <p class="text-sm text-gray-500">หรือลากไฟล์มาวางในพื้นที่นี้</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Error State -->
            <div id="errorState" class="hidden flex items-center justify-center py-16">
                <div class="text-center">
                    <i class="fas fa-exclamation-circle text-6xl text-red-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-red-800 mb-2">เกิดข้อผิดพลาด</h3>
                    <p class="text-red-600 mb-4" id="errorMessage">ไม่สามารถโหลดข้อมูลได้</p>
                    <button onclick="refreshFileList()" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-redo mr-2"></i>ลองใหม่
                    </button>
                </div>
            </div>

            <!-- File List -->
            <div id="fileList" class="hidden">
                <!-- Files will be loaded here -->
            </div>
        </div>
    </div>

    <!-- System Storage Info -->
    <?php if ($system_storage): ?>
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">
            <i class="fab fa-google-drive mr-2"></i>ข้อมูล System Storage
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-blue-700">
            <div>
                <p><strong>Google Account:</strong> <?php echo htmlspecialchars($system_storage->google_account_email ?? 'ไม่ได้ระบุ'); ?></p>
                <p><strong>Storage Name:</strong> <?php echo htmlspecialchars($system_storage->storage_name ?? 'Organization Storage'); ?></p>
            </div>
            <div>
                <p><strong>สร้างเมื่อ:</strong> <?php echo isset($system_storage->created_at) ? date('d/m/Y H:i', strtotime($system_storage->created_at)) : 'ไม่ทราบ'; ?></p>
                <p><strong>สถานะ:</strong> 
                    <?php if (isset($system_storage->is_active) && $system_storage->is_active && isset($system_storage->folder_structure_created) && $system_storage->folder_structure_created): ?>
                        <span class="text-green-600">🟢 พร้อมใช้งาน</span>
                    <?php else: ?>
                        <span class="text-yellow-600">🟡 ยังไม่พร้อม</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Enhanced Upload Modal with Progress -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">📤 อัปโหลดไฟล์</h3>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 transition-colors" id="closeModalBtn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">อัปโหลดไปยัง:</label>
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-folder text-blue-600 mr-2"></i>
                    <span class="text-blue-800 font-medium" id="currentFolderDisplay">Organization Drive</span>
                </div>
                <p class="text-blue-600 text-sm mt-1">โฟลเดอร์ปัจจุบัน</p>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">เลือกไฟล์:</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors" id="modalDropZone">
                <input type="file" id="fileInput" multiple class="hidden" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar" onchange="handleFileSelect(this)">
                <div onclick="document.getElementById('fileInput').click()" class="cursor-pointer">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600">คลิกเพื่อเลือกไฟล์ หรือลากไฟล์มาวาง</p>
                    <p class="text-sm text-gray-500 mt-1">รองรับไฟล์: PDF, Word, Excel, PowerPoint, รูปภาพ, Text, ZIP</p>
                    <p class="text-xs text-gray-400 mt-1">ขนาดสูงสุด 100MB ต่อไฟล์</p>
                </div>
            </div>
        </div>

        <div id="selectedFiles" class="hidden mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">ไฟล์ที่เลือก:</h4>
            <div id="selectedFilesList" class="space-y-2 max-h-32 overflow-y-auto"></div>
        </div>

        <div class="flex justify-end space-x-3">
            <button onclick="closeUploadModal()" 
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50 transition-colors"
                    id="cancelBtn">
                ยกเลิก
            </button>
            <button onclick="startUpload()" id="uploadStartBtn" disabled
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                อัปโหลด
            </button>
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div id="createFolderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">📁 สร้างโฟลเดอร์ใหม่</h3>
            <button onclick="closeCreateFolderModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">สร้างในโฟลเดอร์:</label>
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-folder text-blue-600 mr-2"></i>
                    <span class="text-blue-800 font-medium" id="createFolderParentDisplay">Organization Drive</span>
                </div>
                <p class="text-blue-600 text-sm mt-1">โฟลเดอร์ปัจจุบัน</p>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">ชื่อโฟลเดอร์:</label>
            <input type="text" id="newFolderName" placeholder="ใส่ชื่อโฟลเดอร์..." 
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
                   onkeypress="if(event.key==='Enter') createNewFolder()">
        </div>

        <div class="flex justify-end space-x-3">
            <button onclick="closeCreateFolderModal()" 
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                ยกเลิก
            </button>
            <button onclick="createNewFolder()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                สร้างโฟลเดอร์
            </button>
        </div>
    </div>
</div>


<!-- Folder Permissions Management Modal -->
<!-- ================================ -->
<!-- 2. ENHANCED FOLDER PERMISSIONS MODAL -->
<!-- ================================ -->

<!-- Basic Folder Permissions Modal (สำหรับ Root folder) -->
<div id="folderPermissionsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">🔐 จัดการสิทธิ์โฟลเดอร์</h3>
                <p class="text-gray-600 mt-1" id="currentFolderNamePermissions">โฟลเดอร์: -</p>
            </div>
            <button onclick="closeFolderPermissionsModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <!-- Permission Summary -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600" id="ownerCount">0</div>
                    <div class="text-sm text-green-800">เจ้าของ</div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600" id="adminCount">0</div>
                    <div class="text-sm text-blue-800">ผู้ดูแล</div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-600" id="writeCount">0</div>
                    <div class="text-sm text-yellow-800">แก้ไขได้</div>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-gray-600" id="readCount">0</div>
                    <div class="text-sm text-gray-800">ดูอย่างเดียว</div>
                </div>
            </div>

            <!-- Add New Permission -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-gray-800 mb-3">➕ เพิ่มสิทธิ์ใหม่</h4>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">เลือกผู้ใช้:</label>
                        <select id="newPermissionUser" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            <option value="">-- เลือกผู้ใช้ --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ระดับสิทธิ์:</label>
                        <select id="newPermissionLevel" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            <option value="read">ดูอย่างเดียว</option>
                            <option value="write">แก้ไขได้</option>
                            <option value="admin">ผู้ดูแล</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">หมดอายุ:</label>
                        <input type="date" id="newPermissionExpiry" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div id="rootPermissionOptions" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">ใช้กับ Subfolder:</label>
                        <label class="flex items-center">
                            <input type="checkbox" id="applyToChildren" class="mr-2">
                            <span class="text-sm">ใช้กับโฟลเดอร์ย่อย</span>
                        </label>
                    </div>
                    <div class="flex items-end">
                        <button onclick="addFolderPermission()" 
                                class="w-full bg-green-600 text-white rounded px-4 py-2 text-sm hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus mr-1"></i>เพิ่มสิทธิ์
                        </button>
                    </div>
                </div>
            </div>

            <!-- Existing Permissions -->
            <div class="bg-white border border-gray-200 rounded-lg">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h4 class="font-semibold text-gray-800">👥 สิทธิ์ปัจจุบัน</h4>
                </div>
                <div id="existingPermissionsList" class="max-h-96 overflow-y-auto">
                    <!-- จะถูกโหลดด้วย JavaScript -->
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50">
            <button onclick="closeFolderPermissionsModal()" 
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                ปิด
            </button>
            <button onclick="saveFolderPermissions()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
            </button>
        </div>
    </div>
</div>

<!-- ================================ -->
<!-- 3. ADVANCED SUBFOLDER PERMISSIONS MODAL -->
<!-- ================================ -->

<div id="subfolderPermissionsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl mx-4 max-h-screen overflow-y-auto">
        <div class="p-6 border-b">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-semibold">🔐 จัดการสิทธิ์โฟลเดอร์ย่อย (Advanced)</h3>
                    <div class="flex items-center mt-2 text-sm text-gray-600">
                        <i class="fas fa-route mr-2"></i>
                        <span id="subfolderBreadcrumb">Organization Drive / แผนก HR / สรรหาบุคลากร</span>
                    </div>
                </div>
                <button onclick="closeSubfolderPermissionsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Permission Mode Selector -->
            <div class="mt-4 flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-700">โหมดการจัดการสิทธิ์:</span>
                <div class="flex space-x-2">
                    <button onclick="setPermissionMode('inherited')" id="btn-inherited"
                            class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                        📎 Inherited Only
                    </button>
                    <button onclick="setPermissionMode('override')" id="btn-override"
                            class="px-3 py-1 text-xs rounded-full bg-orange-100 text-orange-800 hover:bg-orange-200 transition-colors">
                        🔴 Override
                    </button>
                    <button onclick="setPermissionMode('combined')" id="btn-combined"
                            class="px-3 py-1 text-xs rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors">
                        🟡 Combined
                    </button>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Inherited Permissions Section -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-semibold text-blue-800">
                        📎 สิทธิ์สืบทอดจาก Parent Folders
                    </h4>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="enableInheritance" checked onchange="toggleInheritance()">
                        <label for="enableInheritance" class="text-sm text-blue-700">
                            เปิดใช้งานการสืบทอดสิทธิ์
                        </label>
                    </div>
                </div>
                <div id="inheritedPermissionsList" class="max-h-48 overflow-y-auto">
                    <!-- จะโหลดด้วย JavaScript -->
                </div>
            </div>

            <!-- Direct Permissions Section -->
            <div class="bg-white border border-gray-200 rounded-lg mb-6">
                <div class="px-4 py-3 border-b bg-gray-50">
                    <h4 class="font-semibold text-gray-800">
                        ⚡ สิทธิ์เฉพาะโฟลเดอร์นี้
                    </h4>
                    <p class="text-sm text-gray-600 mt-1">
                        สิทธิ์เหล่านี้จะเขียนทับหรือรวมกับสิทธิ์สืบทอด
                    </p>
                </div>
                
                <!-- Add New Direct Permission -->
                <div class="p-4 bg-yellow-50 border-b">
                    <h5 class="font-medium text-yellow-800 mb-3">➕ เพิ่มสิทธิ์เฉพาะ</h5>
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">ผู้ใช้</label>
                            <select id="newDirectPermissionUser" class="w-full border rounded px-3 py-2 text-sm">
                                <option value="">-- เลือกผู้ใช้ --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">ระดับสิทธิ์</label>
                            <select id="newDirectPermissionLevel" class="w-full border rounded px-3 py-2 text-sm">
                                <option value="read">ดูอย่างเดียว</option>
                                <option value="write">แก้ไขได้</option>
                                <option value="admin">ผู้ดูแล</option>
                                <option value="no_access">ไม่มีสิทธิ์</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">การกระทำ</label>
                            <select id="permissionAction" class="w-full border rounded px-3 py-2 text-sm">
                                <option value="override">🔴 เขียนทับ</option>
                                <option value="combined">🟡 รวมกัน</option>
                                <option value="direct">🟢 เฉพาะ</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">หมดอายุ</label>
                            <input type="date" id="newDirectPermissionExpiry" 
                                   class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                        <div class="flex items-center">
                            <label class="flex items-center text-xs">
                                <input type="checkbox" id="applyToSubfolders" class="mr-1">
                                ใช้กับ Subfolder
                            </label>
                        </div>
                        <div>
                            <button onclick="addDirectPermission()" 
                                    class="w-full bg-yellow-600 text-white rounded px-4 py-2 text-sm hover:bg-yellow-700 transition-colors">
                                เพิ่มสิทธิ์
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Direct Permissions List -->
                <div id="directPermissionsList" class="max-h-64 overflow-y-auto">
                    <!-- จะโหลดด้วย JavaScript -->
                </div>
            </div>

            <!-- Permission Preview -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-semibold text-green-800 mb-3">
                    👁️ ผลลัพธ์สิทธิ์ที่มีผล (Effective Permissions Preview)
                </h4>
                <div id="effectivePermissionsList">
                    <!-- แสดงสิทธิ์รวมที่จะมีผลจริง -->
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center p-6 border-t bg-gray-50">
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-2"></i>
                การเปลี่ยนแปลงจะมีผลทันทีหลังจากบันทึก
            </div>
            <div class="flex space-x-3">
                <button onclick="previewEffectivePermissions()" 
                        class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors">
                    <i class="fas fa-eye mr-2"></i>ดูตัวอย่าง
                </button>
                <button onclick="closeSubfolderPermissionsModal()" 
                        class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50 transition-colors">
                    ปิด
                </button>
                <button onclick="saveSubfolderPermissions()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </div>
    </div>
</div>


<!-- เพิ่มในส่วน renderFolderPermissionColumn() -->
<div class="subfolder-management-options">
    <!-- ปุ่มสำหรับ Root/Parent Folders -->
    <div class="root-folder-options mb-2">
        <button onclick="manageFolderPermissions('${item.id}', '${escapeHtml(item.name)}')" 
                class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors">
            <i class="fas fa-user-cog mr-1"></i>จัดการสิทธิ์
        </button>
    </div>
    
    <!-- ปุ่มสำหรับ Subfolders -->
    <div class="subfolder-options">
        <button onclick="manageSubfolderPermissions('${item.id}', '${escapeHtml(item.name)}', '${getCurrentPath()}')" 
                class="px-3 py-1 bg-purple-600 text-white rounded text-sm hover:bg-purple-700 transition-colors">
            <i class="fas fa-cogs mr-1"></i>สิทธิ์ขั้นสูง
        </button>
    </div>
</div>



<!-- =============================================
🎯 Subfolder Permission Management Modal
Modal สำหรับจัดการสิทธิ์ขั้นสูง
============================================= -->

<div id="subfolderAdvancedModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-5xl mx-4 max-h-screen overflow-y-auto">
        <!-- Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-blue-50">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">⚙️ จัดการสิทธิ์ขั้นสูง</h3>
                <div class="flex items-center mt-2 text-sm text-gray-600">
                    <i class="fas fa-route mr-2"></i>
                    <span id="advancedBreadcrumb">Organization Drive / โฟลเดอร์ปัจจุบัน</span>
                </div>
            </div>
            <button onclick="closeSubfolderAdvancedModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Inherited Status -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600" id="inheritedPermissionCount">0</div>
                    <div class="text-sm text-blue-800">สิทธิ์สืบทอด</div>
                    <div class="text-xs text-blue-600 mt-1">จาก Parent</div>
                </div>
                
                <!-- Direct Status -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600" id="directPermissionCount">0</div>
                    <div class="text-sm text-green-800">สิทธิ์เฉพาะ</div>
                    <div class="text-xs text-green-600 mt-1">เฉพาะโฟลเดอร์นี้</div>
                </div>
                
                <!-- Override Status -->
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-orange-600" id="overridePermissionCount">0</div>
                    <div class="text-sm text-orange-800">Override</div>
                    <div class="text-xs text-orange-600 mt-1">เขียนทับ</div>
                </div>
                
                <!-- Effective Total -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600" id="totalEffectiveCount">0</div>
                    <div class="text-sm text-purple-800">รวมที่มีผล</div>
                    <div class="text-xs text-purple-600 mt-1">Effective</div>
                </div>
            </div>

            <!-- Management Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- สิทธิ์สืบทอด -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-semibold text-blue-800">
                            📎 สิทธิ์สืบทอด (Inherited)
                        </h4>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="enableInheritanceAdvanced" checked onchange="toggleAdvancedInheritance()">
                            <label for="enableInheritanceAdvanced" class="text-sm text-blue-700">
                                เปิดใช้งาน
                            </label>
                        </div>
                    </div>
                    <div id="inheritedPermissionsListAdvanced" class="max-h-48 overflow-y-auto">
                        <!-- จะโหลดด้วย JavaScript -->
                    </div>
                    <div class="mt-3 text-xs text-blue-600">
                        💡 สิทธิ์เหล่านี้มาจาก Parent Folder
                    </div>
                </div>

                <!-- สิทธิ์เฉพาะ -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-semibold text-green-800 mb-3">
                        ⚡ สิทธิ์เฉพาะ (Direct Override)
                    </h4>
                    <div id="directPermissionsListAdvanced" class="max-h-48 overflow-y-auto mb-3">
                        <!-- จะโหลดด้วย JavaScript -->
                    </div>
                    
                    <!-- Add New Direct Permission -->
                    <div class="border-t border-green-200 pt-3">
                        <h5 class="font-medium text-green-800 mb-2">➕ เพิ่มสิทธิ์เฉพาะ</h5>
                        <div class="grid grid-cols-3 gap-2">
                            <select id="newDirectUserAdvanced" class="text-sm border rounded px-2 py-1">
                                <option value="">-- เลือกผู้ใช้ --</option>
                            </select>
                            <select id="newDirectAccessAdvanced" class="text-sm border rounded px-2 py-1">
                                <option value="no_access">🚫 ไม่มีสิทธิ์</option>
                                <option value="read">👁️ ดูอย่างเดียว</option>
                                <option value="write">✏️ แก้ไขได้</option>
                                <option value="admin">👑 ผู้ดูแล</option>
                            </select>
                            <button onclick="addDirectOverride()" 
                                    class="text-sm bg-green-600 text-white rounded px-2 py-1 hover:bg-green-700">
                                เพิ่ม
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Effective Permissions -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-semibold text-purple-800">
                        👁️ สิทธิ์ที่มีผลจริง (Effective Permissions)
                    </h4>
                    <button onclick="refreshEffectivePreview()" 
                            class="text-sm bg-purple-600 text-white rounded px-3 py-1 hover:bg-purple-700">
                        <i class="fas fa-sync-alt mr-1"></i>รีเฟรช
                    </button>
                </div>
                <div id="effectivePermissionsPreview" class="max-h-64 overflow-y-auto">
                    <!-- จะโหลดด้วย JavaScript -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-between items-center p-6 border-t border-gray-200 bg-gray-50">
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-2"></i>
                การเปลี่ยนแปลงจะมีผลทันทีหลังจากบันทึก
            </div>
            <div class="flex space-x-3">
                <button onclick="resetAdvancedChanges()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                    <i class="fas fa-undo mr-2"></i>รีเซ็ต
                </button>
                <button onclick="closeSubfolderAdvancedModal()" 
                        class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50 transition-colors">
                    ปิด
                </button>
                <button onclick="saveAdvancedPermissions()" 
                        class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </div>
    </div>
</div>


<script>
// Global Variables
let currentFolder = 'root';
let fileListData = [];
let viewMode = 'list';
let isLoading = false;
let breadcrumbData = [];
let allFolders = [];
let dragCounter = 0;

// System constants
const SYSTEM_READY = <?php echo ($system_storage && $system_storage->folder_structure_created) ? 'true' : 'false'; ?>;
const API_BASE_URL = '<?php echo site_url('google_drive_system/'); ?>';

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('Enhanced Google Drive System Files Manager initialized');
    
    if (SYSTEM_READY) {
        initializeFileManager();
    } else {
        showSystemNotReady();
    }
    
    setupEnhancedDragAndDrop();
});

// Initialize File Manager
function initializeFileManager() {
    console.log('Initializing file manager with real Google Drive API...');
    loadRootFolders();
    loadFolderOptions();
}

// Show System Not Ready State
function showSystemNotReady() {
    showErrorState('ระบบยังไม่พร้อมใช้งาน กรุณาตั้งค่า System Storage ก่อน');
    
    // Disable buttons
    const buttons = ['uploadBtn', 'createFolderBtn'];
    buttons.forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    });
}
	
	
	function toggleAdvancedInheritance() {
    const checkbox = document.getElementById('enableInheritanceAdvanced');
    const isEnabled = checkbox ? checkbox.checked : false;
    
    console.log('🔄 Toggle advanced inheritance:', isEnabled);
    
    if (!currentManagingFolderId) {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบ ID โฟลเดอร์', 'error');
        return;
    }
    
    // แสดงข้อความยืนยัน
    const action = isEnabled ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
    const description = isEnabled ? 
        'โฟลเดอร์นี้จะรับสิทธิ์จาก Parent Folder อัตโนมัติ' : 
        'โฟลเดอร์นี้จะใช้เฉพาะสิทธิ์ที่ตั้งค่าโดยตรง และจะลบสิทธิ์สืบทอดออกจากสิทธิ์ที่มีผล';
        
    Swal.fire({
        title: `${action}การสืบทอดสิทธิ์?`,
        html: `
            <div class="text-left">
                <p class="mb-3">${description}</p>
                <div class="bg-${isEnabled ? 'blue' : 'orange'}-50 border border-${isEnabled ? 'blue' : 'orange'}-200 rounded-lg p-3">
                    <h4 class="font-medium text-${isEnabled ? 'blue' : 'orange'}-800 mb-2">
                        ${isEnabled ? '📎 เมื่อเปิดใช้งาน:' : '🔒 เมื่อปิดใช้งาน:'}
                    </h4>
                    <ul class="text-sm text-${isEnabled ? 'blue' : 'orange'}-700 space-y-1">
                        ${isEnabled ? `
                            <li>• จะรับสิทธิ์จาก Parent Folder</li>
                            <li>• สิทธิ์จะอัปเดตอัตโนมัติเมื่อ Parent เปลี่ยน</li>
                            <li>• สามารถเพิ่มสิทธิ์เฉพาะได้เพิ่มเติม</li>
                            <li>• จะเพิ่มผู้ใช้กลับเข้าใน Effective Permissions</li>
                        ` : `
                            <li>• จะใช้เฉพาะสิทธิ์ที่ตั้งค่าโดยตรง</li>
                            <li>• ไม่รับสิทธิ์จาก Parent Folder</li>
                            <li>• ต้องจัดการสิทธิ์เองทั้งหมด</li>
                            <li class="text-red-700 font-medium">• จะลบผู้ใช้ที่มีเฉพาะสิทธิ์สืบทอดออกจาก Effective Permissions</li>
                        `}
                    </ul>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `${action}`,
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: isEnabled ? '#3b82f6' : '#f59e0b'
    }).then((result) => {
        if (result.isConfirmed) {
            performToggleInheritance(isEnabled);
        } else {
            // ยกเลิก - คืนค่า checkbox
            checkbox.checked = !isEnabled;
        }
    });
}

	
	
	function performToggleInheritance(enableInheritance) {
    const formData = new FormData();
    formData.append('folder_id', currentManagingFolderId);
    formData.append('enable_inheritance', enableInheritance ? '1' : '0');
    
    fetch(API_BASE_URL + 'toggle_folder_inheritance', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'เปลี่ยนการตั้งค่าสำเร็จ! 🎉',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            // 🔄 Real-time Update: อัปเดต Inherited และ Effective Permissions ทันที
            if (enableInheritance) {
                // เปิดใช้งาน: โหลด inherited permissions ใหม่
                loadInheritedPermissions(currentManagingFolderId);
            } else {
                // ปิดใช้งาน: ล้าง inherited permissions และอัปเดต effective
                currentInheritedPermissions = [];
                renderInheritedPermissions([]);
                
                // อัปเดต effective permissions (เอาเฉพาะ direct permissions)
                updateEffectivePermissionsAfterInheritanceToggle(false);
            }
            
            // อัปเดต summary counts
            updateAdvancedSummary();
            
        } else {
            Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Toggle inheritance error:', error);
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเปลี่ยนการตั้งค่าได้', 'error');
    });
}
	
	
	function updateEffectivePermissionsAfterInheritanceToggle(enableInheritance) {
    console.log('🔄 Updating effective permissions after inheritance toggle:', enableInheritance);
    
    if (enableInheritance) {
        // เปิดใช้งาน: รวม inherited + direct
        const combinedPermissions = [...currentInheritedPermissions, ...currentDirectPermissions];
        
        // กรองให้คนละคนหนึ่งสิทธิ์ (direct override inherited)
        const effectivePermissions = [];
        const processedMembers = new Set();
        
        // Direct permissions ก่อน (มีลำดับความสำคัญสูงกว่า)
        currentDirectPermissions.forEach(permission => {
            if (!processedMembers.has(permission.member_id)) {
                effectivePermissions.push({
                    ...permission,
                    permission_source_type: 'direct',
                    source_description: 'สิทธิ์เฉพาะ',
                    final_access_type: permission.access_type
                });
                processedMembers.add(permission.member_id);
            }
        });
        
        // Inherited permissions (สำหรับคนที่ยังไม่มี direct)
        currentInheritedPermissions.forEach(permission => {
            if (!processedMembers.has(permission.member_id)) {
                effectivePermissions.push({
                    ...permission,
                    permission_source_type: 'inherited',
                    source_description: 'สืบทอดจาก Parent',
                    final_access_type: permission.access_type
                });
                processedMembers.add(permission.member_id);
            }
        });
        
        currentEffectivePermissions = effectivePermissions;
        
    } else {
        // ปิดใช้งาน: เอาเฉพาะ direct permissions
        currentEffectivePermissions = currentDirectPermissions.map(permission => ({
            ...permission,
            permission_source_type: 'direct',
            source_description: 'สิทธิ์เฉพาะ',
            final_access_type: permission.access_type
        }));
    }
    
    // Render effective permissions ใหม่
    renderEffectivePermissions(currentEffectivePermissions);
    
    console.log(`✅ Updated effective permissions: ${currentEffectivePermissions.length} users`);
}

	
	
	
	function toggleInheritance() {
    const checkbox = document.getElementById('enableInheritance');
    const isEnabled = checkbox ? checkbox.checked : false;
    
    console.log('🔄 Toggle inheritance (normal mode):', isEnabled);
    
    // ใช้ฟังก์ชันเดียวกัน
    toggleAdvancedInheritance();
}


	
	

// Enhanced Drag and Drop Setup
function setupEnhancedDragAndDrop() {
    console.log('Setting up enhanced drag and drop...');
    
    const fileListContainer = document.getElementById('fileListContainer');
    const fileBrowserContainer = document.getElementById('fileBrowserContainer');
    const dropZoneOverlay = document.getElementById('dropZoneOverlay');
    const modalDropZone = document.getElementById('modalDropZone');
    
    if (!fileListContainer || !fileBrowserContainer) {
        console.warn('Drop zone containers not found');
        return;
    }

    // Main file browser area drag and drop
    fileBrowserContainer.addEventListener('dragenter', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dragCounter++;
        
        if (SYSTEM_READY && e.dataTransfer.types.includes('Files')) {
            fileBrowserContainer.style.position = 'relative';
            dropZoneOverlay.classList.remove('hidden');
        }
    });

    fileBrowserContainer.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dragCounter--;
        
        if (dragCounter === 0) {
            dropZoneOverlay.classList.add('hidden');
        }
    });

    fileBrowserContainer.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        if (SYSTEM_READY && e.dataTransfer.types.includes('Files')) {
            e.dataTransfer.dropEffect = 'copy';
        }
    });

    fileBrowserContainer.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dragCounter = 0;
        
        dropZoneOverlay.classList.add('hidden');
        
        if (!SYSTEM_READY) {
            Swal.fire({
                icon: 'warning',
                title: 'ระบบยังไม่พร้อม',
                text: 'กรุณาตั้งค่า System Storage ก่อนใช้งาน',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        const files = Array.from(e.dataTransfer.files);
        if (files.length > 0) {
            console.log('Files dropped:', files.length, 'files');
            handleDroppedFiles(files);
        }
    });

    // Modal drop zone setup
    if (modalDropZone) {
        modalDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            modalDropZone.classList.add('border-blue-400', 'bg-blue-50');
        });

        modalDropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            if (!modalDropZone.contains(e.relatedTarget)) {
                modalDropZone.classList.remove('border-blue-400', 'bg-blue-50');
            }
        });

        modalDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            modalDropZone.classList.remove('border-blue-400', 'bg-blue-50');
            
            const files = Array.from(e.dataTransfer.files);
            if (files.length > 0) {
                const fileInput = document.getElementById('fileInput');
                const dt = new DataTransfer();
                files.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
                handleFileSelect(fileInput);
            }
        });
    }

    // Prevent default drag behaviors on window
    window.addEventListener('dragover', (e) => {
        if (e.target === document.body || e.target === document.documentElement) {
            e.preventDefault();
        }
    });

    window.addEventListener('drop', (e) => {
        if (e.target === document.body || e.target === document.documentElement) {
            e.preventDefault();
        }
    });
}

// Handle Dropped Files
function handleDroppedFiles(files) {
    console.log('Processing dropped files:', files.length);
    
    // ตรวจสอบไฟล์
    const maxSize = 100 * 1024 * 1024; // 100MB
    const allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];
    
    const validFiles = [];
    const invalidFiles = [];

    files.forEach(file => {
        const extension = file.name.split('.').pop().toLowerCase();
        
        if (file.size > maxSize) {
            invalidFiles.push({ file: file, reason: 'ขนาดไฟล์เกิน 100MB' });
        } else if (!allowedTypes.includes(extension)) {
            invalidFiles.push({ file: file, reason: 'ประเภทไฟล์ไม่ได้รับอนุญาต' });
        } else {
            validFiles.push(file);
        }
    });

    if (invalidFiles.length > 0) {
        let errorMessage = 'ไฟล์เหล่านี้ไม่สามารถอัปโหลดได้:\n\n';
        invalidFiles.forEach(item => {
            errorMessage += `• ${item.file.name} - ${item.reason}\n`;
        });
        
        if (validFiles.length > 0) {
            errorMessage += `\nไฟล์ที่ถูกต้อง ${validFiles.length} ไฟล์จะถูกเลือกสำหรับอัปโหลด`;
        }

        Swal.fire({
            icon: 'warning',
            title: 'ไฟล์บางไฟล์ไม่ถูกต้อง',
            text: errorMessage,
            confirmButtonText: 'ตกลง'
        });
    }

    if (validFiles.length > 0) {
        // เปิด Upload Modal และใส่ไฟล์
        showUploadModal();
        
        // ใส่ไฟล์ใน modal
        const fileInput = document.getElementById('fileInput');
        const dt = new DataTransfer();
        validFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        handleFileSelect(fileInput);
        
        // Auto upload หากมีไฟล์เดียว
        if (validFiles.length === 1) {
            setTimeout(() => {
                const uploadBtn = document.getElementById('uploadStartBtn');
                if (uploadBtn && !uploadBtn.disabled) {
                    startUpload();
                }
            }, 500);
        }
    }
}

// Load Root Folders (Real API)
function loadRootFolders() {
    console.log('Loading root folders from Google Drive API...');
    currentFolder = 'root';
    updateBreadcrumb([]);
    showLoadingState();
    
    fetch(API_BASE_URL + 'get_folder_contents', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'folder_id=root'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            fileListData = data.data;
            console.log('Root folders loaded from API:', fileListData.length, 'items');
            renderFileList();
            updateFileCount();
        } else {
            showErrorState(data.message || 'ไม่สามารถโหลดโฟลเดอร์หลักได้');
        }
    })
    .catch(error => {
        console.error('Error loading root folders:', error);
        showErrorState('เกิดข้อผิดพลาดในการเชื่อมต่อ Google Drive');
    });
}

// Load Folder Contents (Real API)
function loadFolderContents(folderId) {
    console.log('Loading folder contents from Google Drive API:', folderId);
    currentFolder = folderId;
    showLoadingState();
    
    // Load breadcrumbs first
    loadBreadcrumbs(folderId);
    
    fetch(API_BASE_URL + 'get_folder_contents', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'folder_id=' + encodeURIComponent(folderId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            fileListData = data.data;
            console.log('Folder contents loaded from API:', fileListData.length, 'items');
            renderFileList();
            updateFileCount();
        } else {
            showErrorState(data.message || 'ไม่สามารถโหลดเนื้อหาโฟลเดอร์ได้');
        }
    })
    .catch(error => {
        console.error('Error loading folder contents:', error);
        showErrorState('เกิดข้อผิดพลาดในการเชื่อมต่อ Google Drive');
    });
}

// Load Breadcrumbs (Real API)
function loadBreadcrumbs(folderId) {
    if (folderId === 'root') {
        updateBreadcrumb([]);
        return;
    }
    
    fetch(API_BASE_URL + 'get_folder_breadcrumbs', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'folder_id=' + encodeURIComponent(folderId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            updateBreadcrumb(data.data);
        }
    })
    .catch(error => {
        console.error('Error loading breadcrumbs:', error);
    });
}

// Update Breadcrumb
function updateBreadcrumb(breadcrumbs) {
    const pathElement = document.getElementById('breadcrumbPath');
    breadcrumbData = breadcrumbs;
    
    if (breadcrumbs.length === 0) {
        pathElement.innerHTML = '';
    } else {
        let html = '';
        breadcrumbs.forEach((crumb, index) => {
            html += ` / <button onclick="loadFolderContents('${crumb.id}')" class="text-blue-600 hover:text-blue-800 hover:underline">${escapeHtml(crumb.name)}</button>`;
        });
        pathElement.innerHTML = html;
    }
}

// Show States
function showLoadingState() {
    console.log('Showing loading state');
    document.getElementById('loadingState').style.display = 'flex';
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('errorState').style.display = 'none';
    document.getElementById('fileList').style.display = 'none';
    isLoading = true;
}

function showEmptyState() {
    console.log('Showing empty state');
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('emptyState').style.display = 'flex';
    document.getElementById('errorState').style.display = 'none';
    document.getElementById('fileList').style.display = 'none';
    isLoading = false;
}

function showErrorState(message = 'เกิดข้อผิดพลาด') {
    console.log('Showing error state:', message);
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('errorState').style.display = 'flex';
    document.getElementById('fileList').style.display = 'none';
    document.getElementById('errorMessage').textContent = message;
    isLoading = false;
}

function showFileList() {
    console.log('Showing file list');
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('errorState').style.display = 'none';
    document.getElementById('fileList').style.display = 'block';
    isLoading = false;
}

// อัปเดต renderFileList() ให้เรียกใช้ enhanced version
function renderFileList() {
    console.log('Rendering file list:', fileListData.length, 'items');
    
    if (typeof renderEnhancedListView === 'function') {
        // ใช้ enhanced version ถ้ามี
        renderEnhancedListView();
    } else {
        // ใช้ basic version ถ้าไม่มี enhanced
        renderBasicListView();
    }
}

// เพิ่ม basic version สำหรับ fallback
function renderBasicListView() {
    console.log('Rendering basic file list:', fileListData.length, 'items');
    
    if (fileListData.length === 0) {
        showEmptyState();
        return;
    }

    const container = document.getElementById('fileList');
    let html = `
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แก้ไขล่าสุด</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ขนาด</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการสิทธิ์</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
    `;

    fileListData.forEach(item => {
        const isFolder = item.type === 'folder';
        const iconClass = item.icon || (isFolder ? 'fas fa-folder text-yellow-500' : 'fas fa-file text-gray-500');
        
        html += `
            <tr class="hover:bg-gray-50 cursor-pointer transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <i class="${iconClass} text-lg mr-3"></i>
                        <span class="text-sm font-medium text-gray-900">${escapeHtml(item.name)}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.modified || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.size || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${isFolder ? `
                        <button onclick="manageFolderPermissions('${item.id}', '${escapeHtml(item.name)}')" 
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline">
                            <i class="fas fa-user-cog mr-1"></i>จัดการสิทธิ์
                        </button>
                    ` : '<span class="text-gray-400">-</span>'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex space-x-2">
                        <button class="text-purple-600 hover:text-purple-800" title="แชร์">
                            <i class="fas fa-share"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-800" title="ลบ">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
    showFileList();
}

// Enhanced Render List View (คลิกแถวไฟล์เปิดใน Google Drive)
function renderListView() {
    let html = `
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แก้ไขล่าสุด</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ขนาด</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการสิทธิ์</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การจัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
    `;

    fileListData.forEach(item => {
    const isRealData = item.real_data === true;
    const iconClass = item.icon || (item.type === 'folder' ? 'fas fa-folder text-yellow-500' : 'fas fa-file text-gray-500');
    const isFile = item.type === 'file';
    const isFolder = item.type === 'folder';
    
    const rowClass = isFile ? 'hover:bg-blue-50 cursor-pointer' : 'hover:bg-gray-50 cursor-pointer';
    const rowOnClick = isFile && item.webViewLink ? 
        `onclick="openInGoogleDrive('${item.webViewLink}')"` : 
        `onclick="handleRowClick('${item.id}', '${item.type}')"`;
    
    html += `
        <tr class="${rowClass} transition-colors" ${rowOnClick}>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <i class="${iconClass} text-lg mr-3"></i>
                    <div>
                        <span class="text-sm font-medium text-gray-900">${escapeHtml(item.name)}</span>
                        ${isRealData ? '<span class="ml-2 text-xs text-green-600">• Live</span>' : ''}
                        ${item.description ? `<p class="text-xs text-gray-500">${escapeHtml(item.description)}</p>` : ''}
                        ${isFile ? '<p class="text-xs text-blue-600">💡 คลิกเพื่อเปิดใน Google Drive</p>' : ''}
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.modified || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.size || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" onclick="event.stopPropagation();">
                ${isFolder ? `
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-users mr-1"></i>
                            ${item.permission_count || 0} คน
                        </span>
                        <button onclick="manageFolderPermissions('${item.id}', '${escapeHtml(item.name)}')" 
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline transition-colors"
                                title="จัดการสิทธิ์โฟลเดอร์">
                            <i class="fas fa-user-cog mr-1"></i>จัดการสิทธิ์
                        </button>
                    </div>
                ` : '<span class="text-gray-400 text-sm">-</span>'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" onclick="event.stopPropagation();">
                <div class="flex space-x-2">
                    ${item.type === 'file' ? 
                        `<button onclick="downloadFile('${item.id}', '${escapeHtml(item.name)}')" class="text-green-600 hover:text-green-800" title="ดาวน์โหลด"><i class="fas fa-download"></i></button>` : ''
                    }
                    <button onclick="shareItem('${item.id}', '${item.type}')" class="text-purple-600 hover:text-purple-800" title="แชร์"><i class="fas fa-share"></i></button>
                    <button onclick="deleteItem('${item.id}', '${item.type}')" class="text-red-600 hover:text-red-800" title="ลบ"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        </tr>
    `;
});

    html += `
                </tbody>
            </table>
        </div>
    `;
    
    return html;
}

// Enhanced Render Grid View (คลิกไฟล์เปิดใน Google Drive)
function renderGridView() {
    let html = '<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 p-6">';
    
    fileListData.forEach(item => {
        const isRealData = item.real_data === true;
        const iconClass = item.icon || (item.type === 'folder' ? 'fas fa-folder text-yellow-500' : 'fas fa-file text-gray-500');
        const isFile = item.type === 'file';
        
        // สำหรับไฟล์: เพิ่ม cursor pointer และ onclick ไปยัง Google Drive
        // สำหรับโฟลเดอร์: onclick ไปยัง folder contents
        const cardClass = isFile ? 'border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-blue-300 transition-all cursor-pointer text-center group' : 'border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer text-center group';
        const cardOnClick = isFile && item.webViewLink ? 
            `onclick="openInGoogleDrive('${item.webViewLink}')"` : 
            `onclick="handleRowClick('${item.id}', '${item.type}')"`;
        
        html += `
            <div class="${cardClass}" ${cardOnClick}>
                <i class="${iconClass} text-4xl mb-2"></i>
                <p class="text-sm font-medium text-gray-900 truncate" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</p>
                <p class="text-xs text-gray-500 mt-1">${item.size || '-'}</p>
                ${isRealData ? '<div class="text-xs text-green-600 mt-1">• Live</div>' : ''}
                ${isFile ? '<div class="text-xs text-blue-600 mt-1">💡 คลิกเพื่อเปิด</div>' : ''}
                <div class="mt-2 opacity-0 group-hover:opacity-100 transition-opacity" onclick="event.stopPropagation();">
                    <div class="flex justify-center space-x-1">
                        ${item.type === 'file' ? 
                            `<button onclick="downloadFile('${item.id}', '${escapeHtml(item.name)}')" class="p-1 text-green-600 hover:bg-green-50 rounded" title="ดาวน์โหลด"><i class="fas fa-download text-sm"></i></button>` : ''
                        }
                        <button onclick="shareItem('${item.id}', '${item.type}')" class="p-1 text-purple-600 hover:bg-purple-50 rounded" title="แชร์"><i class="fas fa-share text-sm"></i></button>
                        <button onclick="deleteItem('${item.id}', '${item.type}')" class="p-1 text-red-600 hover:bg-red-50 rounded" title="ลบ"><i class="fas fa-trash text-sm"></i></button>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    return html;
}

// File Operations
function handleRowClick(itemId, itemType) {
    console.log('Row clicked:', itemId, itemType);
    
    if (itemType === 'folder') {
        openFolder(itemId);
    } else {
        // ไฟล์จะถูกจัดการที่ระดับ row onclick แล้ว
        console.log('File clicked - should be handled by row onclick');
    }
}

function selectFile(fileId) {
    console.log('Selected file:', fileId);
    // TODO: Implement file selection highlight
}

function openFolder(folderId) {
    console.log('Opening folder:', folderId);
    loadFolderContents(folderId);
}

function downloadFile(fileId, fileName) {
    console.log('Download file:', fileId, fileName);
    
    const downloadUrl = API_BASE_URL + 'download_file?file_id=' + encodeURIComponent(fileId);
    
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = fileName;
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // แสดงข้อความแจ้งเตือน
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'info',
        title: `กำลังดาวน์โหลด ${fileName}`,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

function openInGoogleDrive(webViewLink) {
    console.log('Opening in Google Drive:', webViewLink);
    
    // เปิดใน tab ใหม่
    window.open(webViewLink, '_blank');
    
    // แสดงข้อความแจ้งเตือน
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'เปิดไฟล์ใน Google Drive แล้ว',
        showConfirmButton: false,
        timer: 2000
    });
}

function shareItem(itemId, itemType) {
    console.log('Share item:', itemId, itemType);
    
    // หา item ที่จะแชร์
    const item = fileListData.find(i => i.id === itemId);
    if (!item) {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบรายการที่ต้องการแชร์', 'error');
        return;
    }
    
    showShareModal(item);
}

// Share Modal Functions
function showShareModal(item) {
    const isFile = item.type === 'file';
    const itemTypeThai = isFile ? 'ไฟล์' : 'โฟลเดอร์';
    
    Swal.fire({
        title: `📤 แชร์${itemTypeThai}`,
        html: `
            <div class="text-left">
                <!-- Item Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center mb-2">
                        <i class="${item.icon} text-lg mr-3"></i>
                        <span class="font-medium text-gray-800">${escapeHtml(item.name)}</span>
                    </div>
                    <p class="text-sm text-gray-600">
                        ประเภท: ${itemTypeThai} • ขนาด: ${item.size || '-'}
                    </p>
                </div>

                <!-- Share Options -->
                <div class="space-y-4">
                    <!-- Quick Share Link -->
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-3">🔗 แชร์ด้วยลิงก์</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">สิทธิ์การเข้าถึง:</label>
                                <select id="linkPermission" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                    <option value="reader">ดูอย่างเดียว</option>
                                    <option value="commenter">ดูและแสดงความคิดเห็น</option>
                                    <option value="writer">แก้ไขได้</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ใครสามารถเข้าถึงได้:</label>
                                <select id="linkAccess" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                    <option value="restricted">เฉพาะคนที่ได้รับลิงก์</option>
                                    <option value="anyone">ทุกคนที่มีลิงก์</option>
                                </select>
                            </div>
                            <button onclick="generateShareLink('${item.id}', '${item.type}')" 
                                    class="w-full bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition-colors">
                                <i class="fas fa-link mr-2"></i>สร้างลิงก์แชร์
                            </button>
                        </div>
                    </div>

                    <!-- Share with Specific People -->
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-3">👥 แชร์กับบุคคลเฉพาะ</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล:</label>
                                <input type="email" id="shareEmail" placeholder="example@email.com" 
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">สิทธิ์:</label>
                                <select id="emailPermission" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                    <option value="reader">ดูอย่างเดียว</option>
                                    <option value="commenter">ดูและแสดงความคิดเห็น</option>
                                    <option value="writer">แก้ไขได้</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ข้อความ (ไม่บังคับ):</label>
                                <textarea id="shareMessage" placeholder="เพิ่มข้อความที่จะส่งไปพร้อมการแชร์..." 
                                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm h-20 resize-none"></textarea>
                            </div>
                            <button onclick="shareWithEmail('${item.id}', '${item.type}')" 
                                    class="w-full bg-green-600 text-white rounded-lg px-4 py-2 hover:bg-green-700 transition-colors">
                                <i class="fas fa-envelope mr-2"></i>ส่งคำเชิญ
                            </button>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-3">⚡ การกระทำด่วน</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="copyGoogleDriveLink('${item.webViewLink || ''}')" 
                                    class="bg-purple-600 text-white rounded-lg px-3 py-2 text-sm hover:bg-purple-700 transition-colors">
                                <i class="fab fa-google-drive mr-1"></i>Copy Link
                            </button>
                            <button onclick="openShareInGoogleDrive('${item.webViewLink || ''}')" 
                                    class="bg-orange-600 text-white rounded-lg px-3 py-2 text-sm hover:bg-orange-700 transition-colors">
                                <i class="fas fa-external-link-alt mr-1"></i>แชร์ใน Drive
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `,
        width: '500px',
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'ปิด',
        customClass: {
            container: 'share-modal-container'
        }
    });
}

// Generate Share Link
function generateShareLink(itemId, itemType) {
    console.log('Generating share link for:', itemId, itemType);
    
    const linkPermission = document.getElementById('linkPermission').value;
    const linkAccess = document.getElementById('linkAccess').value;
    
    // แสดง loading
    Swal.showLoading();
    
    // สร้าง FormData สำหรับส่งข้อมูล
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('item_type', itemType);
    formData.append('permission', linkPermission);
    formData.append('access', linkAccess);
    
    fetch(API_BASE_URL + 'create_share_link', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
            // ไม่ใส่ Content-Type เพื่อให้ browser ตั้งค่าเองสำหรับ FormData
        },
        body: formData
    })
    .then(response => {
        console.log('Share link response status:', response.status);
        
        // ตรวจสอบ Content-Type
        const contentType = response.headers.get('content-type');
        console.log('Response content-type:', contentType);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        // ตรวจสอบว่าเป็น JSON หรือไม่
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // ถ้าไม่ใช่ JSON อาจเป็น HTML error page
            return response.text().then(text => {
                console.error('Non-JSON response received:', text.substring(0, 500));
                throw new Error('Server returned HTML instead of JSON - Internal Server Error');
            });
        }
    })
    .then(data => {
        console.log('Share link response data:', data);
        
        if (data.success && data.data && data.data.share_link) {
            showShareLinkResult(data.data);
        } else {
            const errorMessage = data.message || 'ไม่สามารถสร้างลิงก์แชร์ได้';
            console.error('Share link creation failed:', errorMessage);
            
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถสร้างลิงก์แชร์ได้',
                html: `
                    <div class="text-left">
                        <p class="mb-3">${errorMessage}</p>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <h4 class="font-medium text-yellow-800 mb-2">💡 ทางเลือกอื่น:</h4>
                            <ol class="text-sm text-yellow-700 space-y-1">
                                <li>1. เปิดไฟล์ใน Google Drive โดยตรง</li>
                                <li>2. ใช้ฟีเจอร์ Share ของ Google Drive</li>
                                <li>3. ตรวจสอบสิทธิ์การเข้าถึงไฟล์</li>
                            </ol>
                        </div>
                    </div>
                `,
                width: '500px',
                confirmButtonText: 'ตกลง',
                showCancelButton: true,
                cancelButtonText: 'เปิดใน Google Drive',
                cancelButtonColor: '#3b82f6'
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    // เปิดไฟล์ใน Google Drive
                    const item = fileListData.find(i => i.id === itemId);
                    if (item && item.webViewLink) {
                        window.open(item.webViewLink, '_blank');
                    }
                }
            });
        }
    })
    .catch(error => {
        console.error('Generate share link error:', error);
        
        let errorTitle = 'เกิดข้อผิดพลาด';
        let errorMessage = 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้';
        let troubleshooting = '';
        
        if (error.message.includes('Internal Server Error')) {
            errorTitle = 'เซิร์ฟเวอร์มีปัญหา';
            errorMessage = 'เซิร์ฟเวอร์มีข้อผิดพลาดภายใน กรุณาลองใหม่อีกครั้ง';
            troubleshooting = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-3">
                    <h4 class="font-medium text-red-800 mb-2">🔧 วิธีแก้ไข:</h4>
                    <ul class="text-sm text-red-700 space-y-1">
                        <li>• ตรวจสอบ Google Drive Token ที่หน้า Token Manager</li>
                        <li>• ลองรีเฟรช Token หากหมดอายุ</li>
                        <li>• ตรวจสอบ Error Log ของเซิร์ฟเวอร์</li>
                        <li>• ติดต่อผู้ดูแลระบบหากปัญหายังคงอยู่</li>
                    </ul>
                </div>
            `;
        } else if (error.message.includes('HTTP 400')) {
            errorTitle = 'ข้อมูลไม่ถูกต้อง';
            errorMessage = 'การตั้งค่าการแชร์ไม่ถูกต้อง';
        } else if (error.message.includes('HTTP 403')) {
            errorTitle = 'ไม่มีสิทธิ์';
            errorMessage = 'ไม่มีสิทธิ์ในการแชร์ไฟล์นี้';
        } else if (error.message.includes('HTTP 404')) {
            errorTitle = 'ไม่พบไฟล์';
            errorMessage = 'ไม่พบไฟล์ที่ต้องการแชร์';
        }
        
        Swal.fire({
            icon: 'error',
            title: errorTitle,
            html: `
                <div class="text-left">
                    <p class="mb-3">${errorMessage}</p>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <p class="text-sm text-gray-600">
                            <strong>รายละเอียดข้อผิดพลาด:</strong><br>
                            ${error.message}
                        </p>
                    </div>
                    ${troubleshooting}
                </div>
            `,
            width: '600px',
            confirmButtonText: 'ตกลง',
            showCancelButton: true,
            cancelButtonText: 'ไปหน้า Token Manager',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                window.open(API_BASE_URL.replace('/google_drive_system/', '/google_drive_system/token_manager'), '_blank');
            }
        });
    });
}


// Show Share Link Result
function showShareLinkResult(shareData) {
    const shareLink = shareData.share_link;
    const permissionText = {
        'reader': 'ดูอย่างเดียว',
        'commenter': 'ดูและแสดงความคิดเห็น', 
        'writer': 'แก้ไขได้'
    };
    
    Swal.fire({
        title: '🎉 สร้างลิงก์แชร์สำเร็จ',
        html: `
            <div class="text-left">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-green-800 mb-2">ลิงก์แชร์ของคุณ:</h4>
                    <div class="flex items-center space-x-2">
                        <input type="text" value="${shareLink}" readonly 
                               class="flex-1 bg-white border border-green-300 rounded px-3 py-2 text-sm font-mono text-green-700"
                               id="generatedShareLink">
                        <button onclick="copyToClipboard('generatedShareLink')" 
                                class="bg-green-600 text-white rounded px-3 py-2 text-sm hover:bg-green-700 transition-colors">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <p class="text-sm text-green-700 mt-2">
                        <strong>สิทธิ์:</strong> ${permissionText[shareData.permission] || shareData.permission}
                    </p>
                </div>
                
                
                
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-500">
                        💡 ลิงก์นี้จะใช้งานได้ตามการตั้งค่าสิทธิ์ที่เลือก
                    </p>
                </div>
            </div>
        `,
        width: '500px',
        confirmButtonText: 'เสร็จสิ้น',
        showCancelButton: true,
        cancelButtonText: 'แชร์อีก'
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            // กลับไปหน้าแชร์หลัก
            const item = fileListData.find(i => i.id === shareData.item_id);
            if (item) {
                showShareModal(item);
            }
        }
    });
}

// Share with Email
function shareWithEmail(itemId, itemType) {
    console.log('Sharing with email for:', itemId, itemType);
    
    const email = document.getElementById('shareEmail').value.trim();
    const permission = document.getElementById('emailPermission').value;
    const message = document.getElementById('shareMessage').value.trim();
    
    if (!email) {
        Swal.fire('กรุณาใส่อีเมล', 'โปรดระบุอีเมลของผู้ที่ต้องการแชร์', 'warning');
        return;
    }
    
    // ตรวจสอบรูปแบบอีเมล
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        Swal.fire('รูปแบบอีเมลไม่ถูกต้อง', 'โปรดระบุอีเมลที่ถูกต้อง', 'warning');
        return;
    }
    
    // แสดง loading
    Swal.showLoading();
    
    // สร้าง FormData
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('item_type', itemType);
    formData.append('email', email);
    formData.append('permission', permission);
    formData.append('message', message);
    
    fetch(API_BASE_URL + 'share_with_email', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        console.log('Share with email response status:', response.status);
        
        const contentType = response.headers.get('content-type');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => {
                console.error('Non-JSON response received:', text.substring(0, 500));
                throw new Error('Server returned HTML instead of JSON');
            });
        }
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'แชร์สำเร็จ! 🎉',
                html: `
                    <div class="text-center">
                        <p class="mb-4">ส่งคำเชิญแชร์ไปยัง <strong>${email}</strong> เรียบร้อยแล้ว</p>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <p class="text-sm text-green-700">
                                <i class="fas fa-check-circle mr-2"></i>
                                ผู้รับจะได้รับอีเมลแจ้งเตือนและสามารถเข้าถึงได้ทันที
                            </p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'เสร็จสิ้น',
                showCancelButton: true,
                cancelButtonText: 'แชร์กับคนอื่น'
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    // กลับไปหน้าแชร์หลัก
                    const item = fileListData.find(i => i.id === itemId);
                    if (item) {
                        showShareModal(item);
                    }
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถแชร์ได้',
                html: `
                    <div class="text-left">
                        <p class="mb-3">${data.message || 'เกิดข้อผิดพลาดในการแชร์'}</p>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <h4 class="font-medium text-yellow-800 mb-2">💡 ลองทางเลือกอื่น:</h4>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li>• ตรวจสอบว่าอีเมลถูกต้อง</li>
                                <li>• ลองแชร์ผ่าน Google Drive โดยตรง</li>
                                <li>• สร้างลิงก์แชร์แทน</li>
                            </ul>
                        </div>
                    </div>
                `,
                confirmButtonText: 'ตกลง'
            });
        }
    })
    .catch(error => {
        console.error('Share with email error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถแชร์กับอีเมลได้: ' + error.message,
            confirmButtonText: 'ตกลง'
        });
    });
}

// Quick Actions
function copyGoogleDriveLink(webViewLink) {
    if (!webViewLink) {
        Swal.fire('ไม่พบลิงก์', 'ไม่สามารถคัดลอกลิงก์ Google Drive ได้', 'warning');
        return;
    }
    
    copyToClipboard(null, webViewLink);
}

function openShareInGoogleDrive(webViewLink) {
    if (!webViewLink) {
        Swal.fire('ไม่พบลิงก์', 'ไม่สามารถเปิด Google Drive ได้', 'warning');
        return;
    }
    
    // เปิด Google Drive และปิด modal
    window.open(webViewLink, '_blank');
    Swal.close();
    
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'info',
        title: 'เปิด Google Drive แล้ว - ใช้ฟีเจอร์แชร์ของ Google Drive ได้เลย',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true
    });
}

// Social Sharing Functions
function shareViaEmail(link) {
    const subject = encodeURIComponent('แชร์ไฟล์จาก Organization Drive');
    const body = encodeURIComponent(`สวัสดีครับ/ค่ะ\n\nขอแชร์ไฟล์จาก Organization Drive\n\nลิงก์: ${link}\n\nขอบคุณครับ/ค่ะ`);
    
    window.open(`mailto:?subject=${subject}&body=${body}`, '_blank');
}

function shareViaLine(link) {
    const text = encodeURIComponent(`แชร์ไฟล์จาก Organization Drive: ${link}`);
    window.open(`https://social-plugins.line.me/lineit/share?url=${encodeURIComponent(link)}&text=${text}`, '_blank');
}

// Copy to Clipboard Function
function copyToClipboard(elementId, directText = null) {
    let textToCopy;
    
    if (directText) {
        textToCopy = directText;
    } else if (elementId) {
        const element = document.getElementById(elementId);
        if (!element) {
            Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบข้อมูลที่จะคัดลอก', 'error');
            return;
        }
        textToCopy = element.value || element.textContent;
    } else {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบข้อมูลที่จะคัดลอก', 'error');
        return;
    }
    
    // ใช้ Clipboard API ถ้าสามารถใช้ได้
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(textToCopy).then(() => {
            showCopySuccess();
        }).catch(err => {
            console.error('Clipboard API failed:', err);
            fallbackCopyTextToClipboard(textToCopy);
        });
    } else {
        // Fallback method
        fallbackCopyTextToClipboard(textToCopy);
    }
}

function fallbackCopyTextToClipboard(text) {
    try {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        const successful = document.execCommand('copy');
        document.body.removeChild(textArea);
        
        if (successful) {
            showCopySuccess();
        } else {
            Swal.fire('ไม่สามารถคัดลอกได้', 'โปรดคัดลอกด้วยตนเอง', 'warning');
        }
    } catch (err) {
        console.error('Fallback copy failed:', err);
        Swal.fire('ไม่สามารถคัดลอกได้', 'โปรดคัดลอกด้วยตนเอง', 'warning');
    }
}

function showCopySuccess() {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'คัดลอกลิงก์แล้ว! 📋',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
}

function deleteItem(itemId, itemType) {
    Swal.fire({
        title: 'ยืนยันการลบ',
        text: `คุณต้องการลบ${itemType === 'folder' ? 'โฟลเดอร์' : 'ไฟล์'}นี้หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#ef4444'
    }).then((result) => {
        if (result.isConfirmed) {
            performDeleteItem(itemId, itemType);
        }
    });
}

function performDeleteItem(itemId, itemType) {
    const deleteBtn = event.target;
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    deleteBtn.disabled = true;
    
    fetch(API_BASE_URL + 'delete_item', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `item_id=${encodeURIComponent(itemId)}&item_type=${encodeURIComponent(itemType)}`
    })
    .then(response => response.json())
    .then(data => {
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
        
        if (data.success) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'ลบเรียบร้อย',
                showConfirmButton: false,
                timer: 3000
            });
            refreshFileList();
        } else {
            Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
        }
    })
    .catch(error => {
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถลบรายการได้', 'error');
    });
}

// View and Sort Functions
function changeViewMode(mode) {
    console.log('Changing view mode to:', mode);
    viewMode = mode;
    if (fileListData.length > 0) {
        renderFileList();
    }
}

function sortFiles(sortBy) {
    console.log('Sorting by:', sortBy);
    
    fileListData.sort((a, b) => {
        switch (sortBy) {
            case 'name':
                return a.name.localeCompare(b.name, 'th');
            case 'modified':
                return new Date(b.modified || 0) - new Date(a.modified || 0);
            case 'size':
                if (a.type === 'folder' && b.type === 'folder') return 0;
                if (a.type === 'folder') return -1;
                if (b.type === 'folder') return 1;
                return (parseInt(a.size) || 0) - (parseInt(b.size) || 0);
            case 'type':
                if (a.type !== b.type) {
                    return a.type === 'folder' ? -1 : 1;
                }
                return a.name.localeCompare(b.name, 'th');
            default:
                return 0;
        }
    });
    
    if (fileListData.length > 0) {
        renderFileList();
    }
}

function searchFiles(query) {
    console.log('Searching for:', query);
    
    if (!query.trim()) {
        renderFileList();
        return;
    }
    
    const filtered = fileListData.filter(item => 
        item.name.toLowerCase().includes(query.toLowerCase())
    );
    
    const originalData = [...fileListData];
    fileListData = filtered;
    renderFileList();
    
    setTimeout(() => {
        if (!document.getElementById('searchInput').value.trim()) {
            fileListData = originalData;
            renderFileList();
        }
    }, 100);
}

function refreshFileList() {
    console.log('Refreshing file list');
    
    if (isLoading) {
        console.log('Already loading, skipping refresh');
        return;
    }
    
    if (currentFolder === 'root') {
        loadRootFolders();
    } else {
        loadFolderContents(currentFolder);
    }
}

// Enhanced Modal Functions
function showUploadModal() {
    console.log('Showing upload modal');
    updateCurrentFolderDisplay();
    document.getElementById('uploadModal').classList.remove('hidden');
}

function updateCurrentFolderDisplay() {
    const displayElement = document.getElementById('currentFolderDisplay');
    if (!displayElement) return;
    
    if (currentFolder === 'root') {
        displayElement.textContent = 'Organization Drive';
    } else {
        let folderPath = 'Organization Drive';
        if (breadcrumbData && breadcrumbData.length > 0) {
            const folderNames = breadcrumbData.map(crumb => crumb.name);
            folderPath += ' / ' + folderNames.join(' / ');
        }
        displayElement.textContent = folderPath;
    }
}

function closeUploadModal() {
    console.log('Closing upload modal');
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('fileInput').value = '';
    document.getElementById('selectedFiles').classList.add('hidden');
    document.getElementById('uploadStartBtn').disabled = true;
}

function showCreateFolderModal() {
    console.log('Showing create folder modal');
    updateCreateFolderParentDisplay();
    document.getElementById('createFolderModal').classList.remove('hidden');
}

function updateCreateFolderParentDisplay() {
    const displayElement = document.getElementById('createFolderParentDisplay');
    if (!displayElement) return;
    
    if (currentFolder === 'root') {
        displayElement.textContent = 'Organization Drive';
    } else {
        let folderPath = 'Organization Drive';
        if (breadcrumbData && breadcrumbData.length > 0) {
            const folderNames = breadcrumbData.map(crumb => crumb.name);
            folderPath += ' / ' + folderNames.join(' / ');
        }
        displayElement.textContent = folderPath;
    }
}

function closeCreateFolderModal() {
    console.log('Closing create folder modal');
    document.getElementById('createFolderModal').classList.add('hidden');
    document.getElementById('newFolderName').value = '';
}

// Load Folder Options for Dropdowns
function loadFolderOptions() {
    console.log('Loading folder options from Google Drive API...');
    
    fetch(API_BASE_URL + 'get_folder_list', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            allFolders = data.data;
            console.log('Loaded', allFolders.length, 'folders for dropdowns');
        }
    })
    .catch(error => {
        console.error('Error loading folder options:', error);
    });
}

// Enhanced File Upload Functions
function handleFileSelect(input) {
    console.log('Files selected:', input.files.length);
    
    const files = Array.from(input.files);
    if (files.length > 0) {
        const maxSize = 100 * 1024 * 1024; // 100MB
        const oversizedFiles = files.filter(file => file.size > maxSize);
        
        if (oversizedFiles.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'ไฟล์ขนาดใหญ่เกินไป',
                html: `
                    <div class="text-left">
                        <p class="mb-2">ไฟล์เหล่านี้มีขนาดใหญ่เกิน 100MB:</p>
                        <ul class="list-disc pl-5 text-sm">
                            ${oversizedFiles.map(file => `<li>${escapeHtml(file.name)} (${formatFileSize(file.size)})</li>`).join('')}
                        </ul>
                        <p class="mt-2 text-sm text-gray-600">กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 100MB</p>
                    </div>
                `,
                confirmButtonText: 'ตกลง'
            });
            
            const validFiles = files.filter(file => file.size <= maxSize);
            if (validFiles.length > 0) {
                updateFileInputWithValidFiles(validFiles);
                displaySelectedFiles(validFiles);
                document.getElementById('uploadStartBtn').disabled = false;
            } else {
                document.getElementById('selectedFiles').classList.add('hidden');
                document.getElementById('uploadStartBtn').disabled = true;
            }
            return;
        }
        
        displaySelectedFiles(files);
        document.getElementById('uploadStartBtn').disabled = false;
    } else {
        document.getElementById('selectedFiles').classList.add('hidden');
        document.getElementById('uploadStartBtn').disabled = true;
    }
}

function updateFileInputWithValidFiles(validFiles) {
    const fileInput = document.getElementById('fileInput');
    const dt = new DataTransfer();
    validFiles.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
}

function displaySelectedFiles(files) {
    console.log('Displaying selected files:', files.length);
    
    const container = document.getElementById('selectedFiles');
    const fileList = document.getElementById('selectedFilesList');
    
    let html = '';
    files.forEach((file, index) => {
        const size = formatFileSize(file.size);
        const fileType = getFileIcon(file.name);
        
        html += `
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <div class="flex items-center">
                    <i class="${fileType.icon} ${fileType.color} mr-2"></i>
                    <span class="text-sm">${escapeHtml(file.name)}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500">${size}</span>
                    <button onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    fileList.innerHTML = html;
    container.classList.remove('hidden');
}

function removeFile(index) {
    console.log('Removing file at index:', index);
    
    const fileInput = document.getElementById('fileInput');
    const dt = new DataTransfer();
    
    Array.from(fileInput.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    fileInput.files = dt.files;
    handleFileSelect(fileInput);
}

// Enhanced Upload with Auto-close
function startUpload() {
    console.log('Starting upload to current folder:', currentFolder);
    
    const files = document.getElementById('fileInput').files;
    
    if (files.length === 0) {
        Swal.fire('เกิดข้อผิดพลาด', 'กรุณาเลือกไฟล์', 'error');
        return;
    }
    
    const uploadFolderId = currentFolder === 'root' ? null : currentFolder;
    
    // แสดง progress modal พร้อม auto-close
    let uploadProgress = 0;
    let uploadedCount = 0;
    const totalFiles = files.length;
    
    Swal.fire({
        title: 'กำลังอัปโหลด...',
        html: `
            <div class="text-left">
                <p class="mb-4">อัปโหลด <strong>${totalFiles}</strong> ไฟล์ไปยังโฟลเดอร์ปัจจุบัน</p>
                <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                    <div id="uploadProgressBar" class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span id="uploadStatus">เตรียมการอัปโหลด...</span>
                    <span id="uploadPercent">0%</span>
                </div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            startFileUploadProcess(files, uploadFolderId, totalFiles);
        }
    });
}

function startFileUploadProcess(files, folderId, totalFiles) {
    let uploadedCount = 0;
    let failedCount = 0;
    const uploadResults = [];
    
    // Upload files one by one
    uploadFilesSequentially(files, folderId, 0, uploadedCount, failedCount, uploadResults, totalFiles);
}

function uploadFilesSequentially(files, folderId, index, uploadedCount, failedCount, uploadResults, totalFiles) {
    if (index >= files.length) {
        // All files processed - Auto close modal after success
        showUploadCompleteWithAutoClose(uploadedCount, failedCount, uploadResults, totalFiles);
        return;
    }
    
    const file = files[index];
    const currentFileNum = index + 1;
    
    // Update status
    updateUploadProgress(currentFileNum, totalFiles, `อัปโหลด: ${file.name}`);
    
    // Create FormData
    const formData = new FormData();
    formData.append('file', file);
    formData.append('folder_id', folderId || '');
    formData.append('parent_folder_id', folderId || '');
    
    // Upload file via API
    fetch(API_BASE_URL + 'upload_file', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            uploadedCount++;
            uploadResults.push({
                file: file.name,
                status: 'success',
                message: data.message || 'อัปโหลดสำเร็จ'
            });
        } else {
            failedCount++;
            uploadResults.push({
                file: file.name,
                status: 'error',
                message: data.message || 'อัปโหลดล้มเหลว'
            });
        }
        
        // Continue with next file
        uploadFilesSequentially(files, folderId, index + 1, uploadedCount, failedCount, uploadResults, totalFiles);
    })
    .catch(error => {
        console.error('Upload error:', error);
        failedCount++;
        uploadResults.push({
            file: file.name,
            status: 'error',
            message: 'เกิดข้อผิดพลาดในการอัปโหลด: ' + error.message
        });
        
        // Continue with next file
        uploadFilesSequentially(files, folderId, index + 1, uploadedCount, failedCount, uploadResults, totalFiles);
    });
}

function updateUploadProgress(current, total, status) {
    const percent = Math.round((current / total) * 100);
    
    const progressBar = document.getElementById('uploadProgressBar');
    const statusEl = document.getElementById('uploadStatus');
    const percentEl = document.getElementById('uploadPercent');
    
    if (progressBar) progressBar.style.width = percent + '%';
    if (statusEl) statusEl.textContent = status;
    if (percentEl) percentEl.textContent = percent + '%';
}

// Enhanced Upload Complete with Auto-close (2 seconds)
function showUploadCompleteWithAutoClose(uploadedCount, failedCount, uploadResults, totalFiles) {
    const successCount = uploadedCount;
    const errorCount = failedCount;
    
    // Auto-close if all successful
    if (successCount > 0 && errorCount === 0) {
        // Show success message with countdown
        let countdown = 2;
        const countdownTimer = setInterval(() => {
            Swal.update({
                title: `อัปโหลดสำเร็จ! (${countdown}s)`,
                html: `
                    <div class="text-center">
                        <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                        <h3 class="text-lg font-semibold text-green-800 mb-2">อัปโหลดเสร็จสิ้น</h3>
                        <div class="bg-green-50 rounded-lg p-4 mb-4">
                            <div class="text-2xl font-bold text-green-600">${successCount}</div>
                            <div class="text-sm text-green-800">ไฟล์อัปโหลดสำเร็จ</div>
                        </div>
                        <p class="text-gray-600">กำลังปิดหน้าต่างอัตโนมัติ...</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                            <div class="bg-green-600 h-2 rounded-full transition-all duration-1000" style="width: ${((2-countdown+1)/2)*100}%"></div>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                allowOutsideClick: false
            });
            
            countdown--;
            if (countdown < 0) {
                clearInterval(countdownTimer);
                Swal.close();
                
                // Close upload modal and refresh
                closeUploadModal();
                refreshFileList();
                
                // Show toast notification
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `อัปโหลด ${successCount} ไฟล์สำเร็จ`,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        }, 1000);
    } else {
        // Show detailed results for mixed success/failure
        let resultHtml = `
            <div class="text-left">
                <div class="mb-4">
                    <h3 class="font-semibold text-lg mb-2">ผลการอัปโหลด</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">${successCount}</div>
                            <div class="text-sm text-green-800">สำเร็จ</div>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">${errorCount}</div>
                            <div class="text-sm text-red-800">ล้มเหลว</div>
                        </div>
                    </div>
                </div>
        `;
        
        if (uploadResults.length > 0) {
            resultHtml += `
                <div class="max-h-48 overflow-y-auto border rounded p-3">
                    <h4 class="font-medium mb-2">รายละเอียด:</h4>
            `;
            
            uploadResults.forEach(result => {
                const iconClass = result.status === 'success' ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500';
                resultHtml += `
                    <div class="flex items-start mb-2 text-sm">
                        <i class="${iconClass} mr-2 mt-0.5"></i>
                        <div>
                            <div class="font-medium">${escapeHtml(result.file)}</div>
                            <div class="text-gray-600">${escapeHtml(result.message)}</div>
                        </div>
                    </div>
                `;
            });
            
            resultHtml += `</div>`;
        }
        
        resultHtml += `</div>`;
        
        const title = successCount > 0 ? 'อัปโหลดเสร็จสิ้น' : 'อัปโหลดล้มเหลว';
        const icon = successCount > 0 ? 'success' : 'error';
        
        Swal.fire({
            title: title,
            html: resultHtml,
            icon: icon,
            confirmButtonText: 'ตกลง',
            width: '600px'
        }).then(() => {
            closeUploadModal();
            if (successCount > 0) {
                refreshFileList();
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `อัปโหลด ${successCount} ไฟล์สำเร็จ`,
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
    }
}

// Create Folder Functions
function createNewFolder() {
    console.log('Creating new folder in current location:', currentFolder);
    
    const folderName = document.getElementById('newFolderName').value.trim();
    
    if (!folderName) {
        Swal.fire('เกิดข้อผิดพลาด', 'กรุณาใส่ชื่อโฟลเดอร์', 'error');
        return;
    }
    
    const parentId = currentFolder === 'root' ? null : currentFolder;
    
    Swal.fire({
        title: 'กำลังสร้างโฟลเดอร์...',
        text: `สร้างโฟลเดอร์ "${folderName}"`,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const formData = new FormData();
    formData.append('folder_name', folderName);
    formData.append('parent_id', parentId || '');
    
    fetch(API_BASE_URL + 'create_folder', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'สร้างโฟลเดอร์สำเร็จ',
                text: data.message || `สร้างโฟลเดอร์ "${folderName}" เรียบร้อยแล้ว`,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                closeCreateFolderModal();
                refreshFileList();
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `สร้างโฟลเดอร์ "${folderName}" สำเร็จ`,
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถสร้างโฟลเดอร์ได้',
                text: data.message || 'เกิดข้อผิดพลาดในการสร้างโฟลเดอร์',
                confirmButtonText: 'ตกลง'
            });
        }
    })
    .catch(error => {
        console.error('Create folder error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้',
            confirmButtonText: 'ตกลง'
        });
    });
}

// Helper Functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    
    const icons = {
        'pdf': { icon: 'fas fa-file-pdf', color: 'text-red-500' },
        'doc': { icon: 'fas fa-file-word', color: 'text-blue-500' },
        'docx': { icon: 'fas fa-file-word', color: 'text-blue-500' },
        'xls': { icon: 'fas fa-file-excel', color: 'text-green-500' },
        'xlsx': { icon: 'fas fa-file-excel', color: 'text-green-500' },
        'ppt': { icon: 'fas fa-file-powerpoint', color: 'text-orange-500' },
        'pptx': { icon: 'fas fa-file-powerpoint', color: 'text-orange-500' },
        'jpg': { icon: 'fas fa-file-image', color: 'text-purple-500' },
        'jpeg': { icon: 'fas fa-file-image', color: 'text-purple-500' },
        'png': { icon: 'fas fa-file-image', color: 'text-purple-500' },
        'gif': { icon: 'fas fa-file-image', color: 'text-purple-500' },
        'zip': { icon: 'fas fa-file-archive', color: 'text-yellow-500' },
        'rar': { icon: 'fas fa-file-archive', color: 'text-yellow-500' },
        'txt': { icon: 'fas fa-file-alt', color: 'text-gray-500' }
    };
    
    return icons[ext] || { icon: 'fas fa-file', color: 'text-gray-500' };
}

function updateFileCount() {
    const fileCount = fileListData.filter(item => item.type === 'file').length;
    const totalFilesElement = document.getElementById('totalFilesCount');
    if (totalFilesElement && currentFolder === 'root') {
        totalFilesElement.textContent = fileCount;
    }
}

// Enhanced Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + U = Upload
    if ((e.ctrlKey || e.metaKey) && e.key === 'u') {
        e.preventDefault();
        if (!document.getElementById('uploadBtn').disabled) {
            showUploadModal();
        }
    }
    
    // Ctrl/Cmd + Shift + N = New Folder
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'N') {
        e.preventDefault();
        if (!document.getElementById('createFolderBtn').disabled) {
            showCreateFolderModal();
        }
    }
    
    // F5 = Refresh
    if (e.key === 'F5') {
        e.preventDefault();
        refreshFileList();
    }
    
    // Escape = Close modals
    if (e.key === 'Escape') {
        const modals = ['uploadModal', 'createFolderModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && !modal.classList.contains('hidden')) {
                if (modalId === 'uploadModal') closeUploadModal();
                if (modalId === 'createFolderModal') closeCreateFolderModal();
            }
        });
    }
});

// Enhanced Visual Feedback
function addLoadingStateToButton(buttonId, loadingText = 'กำลังโหลด...') {
    const button = document.getElementById(buttonId);
    if (button) {
        button.disabled = true;
        button.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>${loadingText}`;
    }
}

function removeLoadingStateFromButton(buttonId, originalText, originalIcon = '') {
    const button = document.getElementById(buttonId);
    if (button) {
        button.disabled = false;
        button.innerHTML = `${originalIcon ? `<i class="${originalIcon} mr-2"></i>` : ''}${originalText}`;
    }
}

// Enhanced Error Handling
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    if (e.error && e.error.message) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาดระบบ',
            text: 'กรุณารีเฟรชหน้าเว็บและลองใหม่อีกครั้ง',
            confirmButtonText: 'รีเฟรช',
            showCancelButton: true,
            cancelButtonText: 'ปิด'
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    }
});

// Initialize enhanced features when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth transitions
    const style = document.createElement('style');
    style.textContent = `
        .transition-all { transition: all 0.3s ease; }
        .transition-colors { transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease; }
        .transition-shadow { transition: box-shadow 0.3s ease; }
        .animate-bounce { animation: bounce 1s infinite; }
        .animate-spin { animation: spin 1s linear infinite; }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(-25%); animation-timing-function: cubic-bezier(0.8,0,1,1); }
            50% { transform: none; animation-timing-function: cubic-bezier(0,0,0.2,1); }
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        #dropZoneOverlay {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        
        .drag-over {
            background-color: rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
            transform: scale(1.02);
        }
        
        /* File hover effects */
        .file-row:hover {
            background-color: rgba(59, 130, 246, 0.05);
            border-left: 4px solid #3b82f6;
        }
        
        .folder-row:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        /* Enhanced button styles */
        .btn-enhanced {
            position: relative;
            overflow: hidden;
        }
        
        .btn-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-enhanced:hover::before {
            left: 100%;
        }
        
        /* Focus styles for accessibility */
        input:focus, select:focus, button:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
    `;
    document.head.appendChild(style);
});

console.log('Enhanced Google Drive System Files Manager loaded with improved UX - Click files to open directly in Google Drive');
</script>

<?php if ($system_storage && $system_storage->folder_structure_created): ?>
<script>
console.log('System is ready, enhanced file manager initialized with direct file opening');
</script>
<?php else: ?>
<script>
console.log('System not ready, enhanced file manager disabled');
</script>
<?php endif; ?>


<script>
// =============================================
// CLEAN PERMISSIONS SCRIPT - REAL DATA ONLY
// ไม่มี Temporary Storage หรือ Demo Data
// =============================================

// เพิ่มตัวแปร Global ใหม่
let currentInheritedPermissions = [];
let currentDirectPermissions = [];
let currentEffectivePermissions = [];
let currentPermissionMode = 'inherited';
let currentFolderPermissions = [];

// =============================================
// 1. ENHANCED RENDERING FUNCTIONS
// =============================================

// อัปเดต renderListView() ให้แสดงข้อมูลสิทธิ์
function renderEnhancedListView() {
    console.log('Rendering enhanced file list with permissions data:', fileListData.length, 'items');
    
    if (fileListData.length === 0) {
        showEmptyState();
        return;
    }

    const container = document.getElementById('fileList');
    let html = `
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แก้ไขล่าสุด</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ขนาด</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สิทธิ์โฟลเดอร์</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
    `;

    fileListData.forEach(item => {
        const isRealData = item.real_data === true;
        const iconClass = item.icon || (item.type === 'folder' ? 'fas fa-folder text-yellow-500' : 'fas fa-file text-gray-500');
        const isFile = item.type === 'file';
        const isFolder = item.type === 'folder';
        
        const rowClass = isFile ? 'hover:bg-blue-50 cursor-pointer' : 'hover:bg-gray-50 cursor-pointer';
        const rowOnClick = isFile && item.webViewLink ? 
            `onclick="openInGoogleDrive('${item.webViewLink}')"` : 
            `onclick="handleRowClick('${item.id}', '${item.type}')"`;
        
        html += `
            <tr class="${rowClass} transition-colors" ${rowOnClick}>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <i class="${iconClass} text-lg mr-3"></i>
                        <div>
                            <span class="text-sm font-medium text-gray-900">${escapeHtml(item.name)}</span>
                            ${isRealData ? '<span class="ml-2 text-xs text-green-600">• Live</span>' : ''}
                            ${item.description ? `<p class="text-xs text-gray-500">${escapeHtml(item.description)}</p>` : ''}
                            ${isFile ? '<p class="text-xs text-blue-600">💡 คลิกเพื่อเปิดใน Google Drive</p>' : ''}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.modified || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.size || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" onclick="event.stopPropagation();">
                    ${isFolder ? renderFolderPermissionColumn(item) : '<span class="text-gray-400 text-sm">-</span>'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" onclick="event.stopPropagation();">
                    <div class="flex space-x-2">
                        ${item.type === 'file' ? 
                            `<button onclick="downloadFile('${item.id}', '${escapeHtml(item.name)}')" class="text-green-600 hover:text-green-800" title="ดาวน์โหลด"><i class="fas fa-download"></i></button>` : ''
                        }
                        <button onclick="shareItem('${item.id}', '${item.type}')" class="text-purple-600 hover:text-purple-800" title="แชร์"><i class="fas fa-share"></i></button>
                        <button onclick="deleteItem('${item.id}', '${item.type}')" class="text-red-600 hover:text-red-800" title="ลบ"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
    showFileList();
}

// สร้างคอลัมน์แสดงสิทธิ์โฟลเดอร์
function renderFolderPermissionColumn(item) {
    const permissionCount = item.permission_count || 0;
    const inheritedCount = item.inherited_count || 0;
    const directCount = item.direct_count || 0;
    const overrideCount = item.override_count || 0;
    
    const indicators = item.permission_indicators || {};
    
    let html = `<div class="flex flex-col space-y-1">`;
    
    // แสดงจำนวนสิทธิ์รวม
    html += `
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                <i class="fas fa-users mr-1"></i>
                ${permissionCount} คน
            </span>
        </div>
    `;
    
    // แสดง indicators
    if (permissionCount > 0) {
        html += `<div class="flex items-center space-x-1">`;
        
        if (indicators.has_inherited) {
            html += `<span class="text-xs text-blue-600" title="มีสิทธิ์สืบทอด ${inheritedCount} คน"><i class="fas fa-link"></i>${inheritedCount}</span>`;
        }
        if (indicators.has_direct) {
            html += `<span class="text-xs text-green-600" title="มีสิทธิ์เฉพาะ ${directCount} คน"><i class="fas fa-star"></i>${directCount}</span>`;
        }
        if (indicators.has_override) {
            html += `<span class="text-xs text-orange-600" title="มีการเขียนทับสิทธิ์ ${overrideCount} คน"><i class="fas fa-exclamation-triangle"></i>${overrideCount}</span>`;
        }
        
        html += `</div>`;
    }
    
    // ปุ่มจัดการสิทธิ์
    const isRootOrParent = currentFolder === 'root' || item.is_parent_folder;
    const buttonText = isRootOrParent ? 'จัดการสิทธิ์' : 'สิทธิ์ขั้นสูง';
    const buttonClass = isRootOrParent ? 'text-blue-600 hover:text-blue-800' : 'text-purple-600 hover:text-purple-800';
    const buttonFunction = isRootOrParent ? 
        `manageFolderPermissions('${item.id}', '${escapeHtml(item.name)}')` : 
        `manageSubfolderPermissions('${item.id}', '${escapeHtml(item.name)}', '${getCurrentPath()}')`;
    
    html += `
        <div class="mt-1">
            <button onclick="${buttonFunction}" 
                    class="${buttonClass} text-sm font-medium hover:underline transition-colors"
                    title="${buttonText}">
                <i class="fas fa-user-cog mr-1"></i>${buttonText}
            </button>
        </div>
    `;
    
    html += `</div>`;
    return html;
}

// =============================================
// 2. CORE PERMISSION FUNCTIONS
// =============================================

// แก้ไข loadFolderPermissions() ให้ใช้ข้อมูลจริงเท่านั้น
function loadFolderPermissions(folderId) {
    console.log('🔄 Loading folder permissions for:', folderId);
    
    // แสดง loading state
    showPermissionsLoading();
    
    fetch(API_BASE_URL + 'get_folder_permissions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'folder_id=' + encodeURIComponent(folderId)
    })
    .then(response => {
        console.log('📡 API Response Status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Response is not JSON');
        }
        
        return response.json();
    })
    .then(data => {
        console.log('✅ Permissions loaded:', data);
        
        if (data.success && data.data) {
            // ใช้ข้อมูลจริงจาก API
            currentFolderPermissions = data.data.direct || data.data.effective || [];
            
            console.log('📊 Current permissions count:', currentFolderPermissions.length);
            
            renderExistingPermissions();
            updatePermissionSummary();
        } else {
            throw new Error(data.message || 'No permission data available');
        }
    })
    .catch(error => {
        console.error('❌ Error loading folder permissions:', error);
        showPermissionsError(error.message);
    });
}


// เพิ่มสิทธิ์โฟลเดอร์ (ใช้ข้อมูลจริงเท่านั้น)
function addFolderPermission() {
    const userId = document.getElementById('newPermissionUser')?.value;
    const accessLevel = document.getElementById('newPermissionLevel')?.value || 'read';
    const expiryDate = document.getElementById('newPermissionExpiry')?.value;
    
    // 🔄 Auto Inherit เป็น Default (เสมอ)
    const applyToChildren = true; // ✅ เปิดเสมอ
    
    if (!userId) {
        Swal.fire('กรุณาเลือกผู้ใช้', 'โปรดเลือกผู้ใช้ที่ต้องการให้สิทธิ์', 'warning');
        return;
    }
    
    if (!currentManagingFolderId) {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบ ID โฟลเดอร์', 'error');
        return;
    }
    
    // แสดงข้อความยืนยันพร้อมอธิบาย Auto Inherit
    Swal.fire({
        title: '➕ เพิ่มสิทธิ์พร้อมสืบทอด',
        html: `
            <div class="text-left">
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">🔄 Auto Inherit (สืบทอดอัตโนมัติ)</h4>
                    <div class="text-sm text-blue-700 space-y-2">
                        <p><strong>ผู้ใช้:</strong> <span class="text-blue-900">${getSelectedUserName()}</span></p>
                        <p><strong>สิทธิ์:</strong> <span class="text-blue-900">${getAccessTypeText(accessLevel)}</span></p>
                        <p><strong>ขอบเขต:</strong> <span class="text-blue-900">โฟลเดอร์นี้ + โฟลเดอร์ย่อยทั้งหมด</span></p>
                        ${expiryDate ? `<p><strong>หมดอายุ:</strong> <span class="text-blue-900">${formatDateThai(expiryDate)}</span></p>` : ''}
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-green-600 mr-2 mt-1"></i>
                        <div class="text-sm text-green-700">
                            <p class="font-medium mb-1">💡 ข้อดีของ Auto Inherit:</p>
                            <ul class="list-disc pl-4 space-y-1">
                                <li>ไม่ต้องเพิ่มสิทธิ์ที่โฟลเดอร์ย่อยทีละโฟลเดอร์</li>
                                <li>โฟลเดอร์ใหม่ในอนาคตจะได้สิทธิ์อัตโนมัติ</li>
                                <li>จัดการง่าย แก้ไขที่เดียวได้ทุกที่</li>
                                <li>สามารถ Override ที่โฟลเดอร์ย่อยได้ภายหลัง</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `,
        width: '500px',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '✅ เพิ่มสิทธิ์ (Auto Inherit)',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#10b981'
    }).then((result) => {
        if (result.isConfirmed) {
            performAddFolderPermission(userId, accessLevel, expiryDate, applyToChildren);
        }
    });
}
	
	
	/**
 * 🔄 ดำเนินการเพิ่มสิทธิ์จริง
 */
function performAddFolderPermission(userId, accessLevel, expiryDate, applyToChildren) {
    // แสดง loading พร้อมข้อความ Auto Inherit
    Swal.fire({
        title: 'กำลังเพิ่มสิทธิ์...',
        html: `
            <div class="text-center">
                <div class="mb-3">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                </div>
                <p class="text-gray-600">กำลังเพิ่มสิทธิ์และสืบทอดไปโฟลเดอร์ย่อย...</p>
                <p class="text-sm text-gray-500 mt-2">อาจใช้เวลาสักครู่สำหรับโฟลเดอร์ที่มีโฟลเดอร์ย่อยเยอะ</p>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });

    const formData = new FormData();
    formData.append('folder_id', currentManagingFolderId);
    formData.append('member_id', userId);
    formData.append('access_type', accessLevel);
    formData.append('expires_at', expiryDate || '');
    formData.append('apply_to_children', applyToChildren ? '1' : '0');
    
    fetch(API_BASE_URL + 'add_folder_permission', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // แสดงผลสำเร็จพร้อมอธิบายผลลัพธ์
            Swal.fire({
                icon: 'success',
                title: 'เพิ่มสิทธิ์สำเร็จ! 🎉',
                html: `
                    <div class="text-left">
                        <p class="mb-4">${data.message}</p>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <h4 class="font-medium text-green-800 mb-2">📋 สิ่งที่เกิดขึ้น:</h4>
                            <ul class="text-sm text-green-700 space-y-1">
                                <li>✅ เพิ่มสิทธิ์ที่โฟลเดอร์นี้แล้ว</li>
                                <li>🔄 สิทธิ์สืบทอดไปโฟลเดอร์ย่อยอัตโนมัติ</li>
                                <li>📁 โฟลเดอร์ใหม่ในอนาคตจะได้สิทธิ์อัตโนมัติ</li>
                                <li>⚙️ สามารถแก้ไขที่โฟลเดอร์ย่อยได้ภายหลัง</li>
                            </ul>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-600">
                                💡 หากต้องการปรับสิทธิ์เฉพาะโฟลเดอร์ย่อย ให้ไปที่ "สิทธิ์ขั้นสูง"
                            </p>
                        </div>
                    </div>
                `,
                width: '500px',
                timer: 5000,
                showConfirmButton: true,
                confirmButtonText: 'เสร็จสิ้น'
            });
            
            resetPermissionForm();
            loadFolderPermissions(currentManagingFolderId);
            loadAvailableUsers();
        } else {
            throw new Error(data.message || 'ไม่สามารถเพิ่มสิทธิ์ได้');
        }
    })
    .catch(error => {
        console.error('Error adding folder permission:', error);
        Swal.fire({
            icon: 'error',
            title: 'ไม่สามารถเพิ่มสิทธิ์ได้',
            html: `
                <div class="text-left">
                    <p class="mb-3">${error.message}</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <h4 class="font-medium text-yellow-800 mb-2">💡 ทางเลือกอื่น:</h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• ลองเพิ่มเฉพาะโฟลเดอร์นี้ก่อน (ไม่ Auto Inherit)</li>
                            <li>• ตรวจสอบว่าผู้ใช้มีสิทธิ์อยู่แล้วหรือไม่</li>
                            <li>• ลองใหม่อีกครั้งในภายหลัง</li>
                        </ul>
                    </div>
                </div>
            `,
            confirmButtonText: 'ตกลง'
        });
    });
}
	
	
	/**
 * 🎯 แสดงโครงสร้างสิทธิ์ที่จะเกิดขึ้น (Preview)
 */
function previewInheritanceStructure() {
    if (!currentManagingFolderId) {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบ ID โฟลเดอร์', 'error');
        return;
    }
    
    // แสดง Modal แสดงโครงสร้างที่จะได้สิทธิ์
    Swal.fire({
        title: '🗂️ โครงสร้างการสืบทอดสิทธิ์',
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-800 mb-3">📋 สิทธิ์จะถูกใช้กับ:</h4>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-folder text-blue-600 mr-2"></i>
                                <span class="font-medium">โฟลเดอร์ปัจจุบัน</span>
                                <span class="ml-auto text-green-600">✅ ได้สิทธิ์</span>
                            </div>
                            <div class="pl-4 border-l-2 border-gray-300 space-y-1">
                                <div class="flex items-center">
                                    <i class="fas fa-folder text-yellow-600 mr-2"></i>
                                    <span>โฟลเดอร์ย่อยที่มีอยู่</span>
                                    <span class="ml-auto text-green-600">✅ ได้สิทธิ์</span>
                                </div>
                                <div class="pl-4 border-l-2 border-gray-300">
                                    <div class="flex items-center">
                                        <i class="fas fa-folder text-orange-600 mr-2"></i>
                                        <span>โฟลเดอร์ย่อยของโฟลเดอร์ย่อย</span>
                                        <span class="ml-auto text-green-600">✅ ได้สิทธิ์</span>
                                    </div>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    <span>โฟลเดอร์ใหม่ในอนาคต</span>
                                    <span class="ml-auto text-green-600">✅ ได้สิทธิ์</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <h4 class="font-medium text-blue-800 mb-2">🔧 การปรับแต่งภายหลัง:</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• ไปที่โฟลเดอร์ย่อย → "สิทธิ์ขั้นสูง"</li>
                        <li>• เลือก "Override" เพื่อเขียนทับสิทธิ์</li>
                        <li>• หรือ "ไม่มีสิทธิ์" เพื่อยกเลิกการเข้าถึง</li>
                        <li>• ปรับระดับสิทธิ์ (อ่าน/เขียน/ผู้ดูแล)</li>
                    </ul>
                </div>
            </div>
        `,
        width: '600px',
        confirmButtonText: 'เข้าใจแล้ว'
    });
}

	
	
	function getSelectedUserName() {
    const userSelect = document.getElementById('newPermissionUser');
    if (userSelect && userSelect.selectedIndex > 0) {
        return userSelect.options[userSelect.selectedIndex].textContent;
    }
    return 'ผู้ใช้ที่เลือก';
}
	
	
	
	function enhanceAutoInheritUI() {
    // ซ่อน checkbox apply_to_children เนื่องจากเปิดเสมอ
    const applyToChildrenContainer = document.getElementById('rootPermissionOptions');
    if (applyToChildrenContainer) {
        applyToChildrenContainer.style.display = 'none';
    }
    
    // เพิ่มข้อความอธิบาย Auto Inherit
    const addPermissionSection = document.querySelector('.bg-gray-50');
    if (addPermissionSection && !addPermissionSection.querySelector('.auto-inherit-notice')) {
        const notice = document.createElement('div');
        notice.className = 'auto-inherit-notice mt-3 p-3 bg-green-50 border border-green-200 rounded-lg';
        notice.innerHTML = `
            <div class="flex items-start">
                <i class="fas fa-magic text-green-600 mr-2 mt-1"></i>
                <div class="text-sm text-green-700">
                    <p class="font-medium mb-1">🔄 Auto Inherit เปิดใช้งาน</p>
                    <p>สิทธิ์จะสืบทอดไปโฟลเดอร์ย่อยอัตโนมัติ ปรับแต่งได้ที่ "สิทธิ์ขั้นสูง"</p>
                </div>
            </div>
        `;
        addPermissionSection.appendChild(notice);
    }
    
    // เพิ่มปุ่ม Preview Structure
    const buttonContainer = document.querySelector('.bg-green-600');
    if (buttonContainer && buttonContainer.parentElement && !buttonContainer.parentElement.querySelector('.preview-btn')) {
        const previewBtn = document.createElement('button');
        previewBtn.type = 'button';
        previewBtn.className = 'preview-btn w-full mt-2 bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition-colors';
        previewBtn.innerHTML = '<i class="fas fa-eye mr-2"></i>ดูโครงสร้างการสืบทอด';
        previewBtn.onclick = previewInheritanceStructure;
        buttonContainer.parentElement.insertBefore(previewBtn, buttonContainer.nextSibling);
    }
}
	
	

// โหลดรายการผู้ใช้ที่สามารถเพิ่มสิทธิ์ได้ (ใช้ข้อมูลจริงเท่านั้น)
function loadAvailableUsers() {
    console.log('🔄 Loading available users for dropdown...');
    
    // แสดง loading state ใน dropdown
    showUsersLoading();
    
    // สร้าง request แบบ Enhanced
    const fetchOptions = {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Cache-Control': 'no-cache'
        }
    };
    
    fetch(API_BASE_URL + 'get_available_users', fetchOptions)
    .then(response => {
        console.log('📡 Users API Response Status:', response.status);
        console.log('📡 Users API Headers:', response.headers);
        
        const contentType = response.headers.get('content-type');
        console.log('📄 Users API Content-Type:', contentType);
        
        if (!response.ok) {
            // ถ้า status ไม่ OK
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `HTTP ${response.status}: ${response.statusText}`);
                });
            } else {
                return response.text().then(text => {
                    console.error('❌ Non-JSON error response:', text.substring(0, 500));
                    
                    if (text.includes('Fatal error') || text.includes('Parse error')) {
                        const phpError = extractPHPError(text);
                        throw new Error(`PHP Error: ${phpError}`);
                    } else {
                        throw new Error(`HTTP ${response.status}: Server returned HTML instead of JSON`);
                    }
                });
            }
        }
        
        // ตรวจสอบ Content-Type
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('❌ Success response but not JSON:', text.substring(0, 500));
                
                // ลองหา JSON ใน HTML
                const jsonMatch = text.match(/\{.*\}/s);
                if (jsonMatch) {
                    try {
                        return JSON.parse(jsonMatch[0]);
                    } catch (e) {
                        console.error('❌ Failed to extract JSON from HTML');
                    }
                }
                
                throw new Error('Server returned HTML instead of JSON - Check get_available_users method');
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('✅ Users loaded successfully:', data);
        
        if (data && data.success && data.data && Array.isArray(data.data)) {
            // อัปเดต dropdown ทั้งหมด
            updateAllUserDropdowns(data.data);
            
            // แสดงสถิติ
            if (data.stats) {
                console.log('📊 User Stats:', data.stats);
            }
            
            // แสดงข้อความสำเร็จ
            showUsersLoadSuccess(data.data.length);
        } else {
            throw new Error('Invalid users data format: ' + JSON.stringify(data));
        }
    })
    .catch(error => {
        console.error('❌ Error loading available users:', error);
        showUsersLoadError(error.message);
    });
}
	
	
	function updateAllUserDropdowns(users) {
    console.log('🔄 Updating all user dropdowns with', users.length, 'users');
    
    // รายการ dropdown ทั้งหมดที่ต้องอัปเดต
    const dropdownIds = [
        'newPermissionUser',           // Basic permissions modal
        'newDirectPermissionUser',     // Advanced permissions modal  
        'newDirectUserAdvanced'        // Advanced subfolder modal
    ];
    
    dropdownIds.forEach(dropdownId => {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) {
            updateUserDropdown(dropdown, users);
        } else {
            console.warn(`⚠️ Dropdown ${dropdownId} not found`);
        }
    });
}
	
	
	function updateUserDropdown(dropdown, users) {
    console.log('📋 Updating dropdown:', dropdown.id, 'with', users.length, 'users');
    
    // เคลียร์ options เดิม
    dropdown.innerHTML = '<option value="">-- เลือกผู้ใช้ --</option>';
    
    // ตรวจสอบว่ามี users หรือไม่
    if (!users || !Array.isArray(users) || users.length === 0) {
        dropdown.innerHTML = '<option value="">-- ไม่มีผู้ใช้ในระบบ --</option>';
        dropdown.disabled = true;
        return;
    }
    
    // กรองผู้ใช้ที่ยังไม่มีสิทธิ์ (ถ้ามีข้อมูล permissions)
    const availableUsers = filterAvailableUsers(users);
    
    if (availableUsers.length === 0) {
        dropdown.innerHTML = '<option value="">-- ผู้ใช้ทุกคนมีสิทธิ์แล้ว --</option>';
        dropdown.disabled = true;
        return;
    }
    
    // เพิ่ม users ลง dropdown
    availableUsers.forEach(user => {
        const option = document.createElement('option');
        option.value = user.m_id;
        
        // สร้างข้อความแสดงผล
        let displayText = user.name || 'ไม่ระบุชื่อ';
        if (user.position_name && user.position_name !== 'ไม่ระบุ') {
            displayText += ` - ${user.position_name}`;
        }
        if (user.google_email) {
            displayText += ` (${user.google_email})`;
        }
        
        option.textContent = displayText;
        
        // เพิ่ม data attributes
        option.setAttribute('data-email', user.google_email || '');
        option.setAttribute('data-position', user.position_name || '');
        option.setAttribute('data-storage-enabled', user.storage_access_granted ? '1' : '0');
        
        dropdown.appendChild(option);
    });
    
    // เปิดใช้งาน dropdown
    dropdown.disabled = false;
    
    console.log(`✅ Updated dropdown ${dropdown.id} with ${availableUsers.length} available users`);
}

	
	function filterAvailableUsers(users) {
    if (!users || !Array.isArray(users)) {
        return [];
    }
    
    // ถ้าไม่มีข้อมูล permissions ปัจจุบัน ให้แสดงทุกคน
    if (!currentFolderPermissions || !Array.isArray(currentFolderPermissions)) {
        return users;
    }
    
    // กรองผู้ใช้ที่ยังไม่มีสิทธิ์
    return users.filter(user => {
        const hasPermission = currentFolderPermissions.some(permission => 
            permission.member_id == user.m_id || permission.m_id == user.m_id
        );
        return !hasPermission;
    });
}

	
	
	function showUsersLoading() {
    const dropdownIds = [
        'newPermissionUser',
        'newDirectPermissionUser', 
        'newDirectUserAdvanced'
    ];
    
    dropdownIds.forEach(dropdownId => {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) {
            dropdown.innerHTML = '<option value="">⏳ กำลังโหลดผู้ใช้...</option>';
            dropdown.disabled = true;
        }
    });
}
	
	
	function showUsersLoadSuccess(userCount) {
    // แสดง toast notification
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `โหลดผู้ใช้สำเร็จ (${userCount} คน)`,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    }
}

	
	function showUsersLoadError(errorMessage) {
    console.error('❌ Users load error:', errorMessage);
    
    const dropdownIds = [
        'newPermissionUser',
        'newDirectPermissionUser',
        'newDirectUserAdvanced'
    ];
    
    dropdownIds.forEach(dropdownId => {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) {
            dropdown.innerHTML = '<option value="">❌ ไม่สามารถโหลดผู้ใช้ได้</option>';
            dropdown.disabled = true;
            
            // เพิ่มปุ่ม retry ถ้ายังไม่มี
            const parent = dropdown.parentElement;
            if (parent && !parent.querySelector('.retry-users-btn')) {
                const retryBtn = document.createElement('button');
                retryBtn.type = 'button';
                retryBtn.className = 'retry-users-btn mt-2 px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors';
                retryBtn.innerHTML = '<i class="fas fa-redo mr-1"></i>ลองใหม่';
                retryBtn.onclick = () => {
                    retryBtn.remove();
                    loadAvailableUsers();
                };
                parent.appendChild(retryBtn);
            }
        }
    });
    
    // แสดง error notification
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'ไม่สามารถโหลดรายชื่อผู้ใช้ได้',
            html: `
                <div class="text-left">
                    <p class="mb-3">${escapeHtml(errorMessage)}</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <h4 class="font-medium text-yellow-800 mb-2">💡 วิธีแก้ไข:</h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• ตรวจสอบ method get_available_users() ใน Controller</li>
                            <li>• ตรวจสอบ database connection</li>
                            <li>• ตรวจสอบ tbl_member table</li>
                            <li>• ลองเรียก API ใหม่</li>
                        </ul>
                    </div>
                    <div class="mt-3 text-center">
                        <button onclick="loadAvailableUsers()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            <i class="fas fa-redo mr-2"></i>ลองใหม่
                        </button>
                    </div>
                </div>
            `,
            width: '500px',
            confirmButtonText: 'ตกลง'
        });
    }
}

	
	

// โหลดสถิติสิทธิ์ (ใช้ข้อมูลจริงเท่านั้น)
function loadPermissionStats(folderId) {
    console.log('📊 Loading permission stats for:', folderId);
    
    fetch(API_BASE_URL + 'get_folder_permission_stats', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'folder_id=' + encodeURIComponent(folderId)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.data) {
            updatePermissionSummary(data.data);
        } else {
            console.warn('⚠️ No stats data, using calculated stats');
            updatePermissionSummary(); // ใช้การคำนวณจาก currentFolderPermissions
        }
    })
    .catch(error => {
        console.warn('⚠️ Stats API error, using calculated stats:', error.message);
        updatePermissionSummary(); // ใช้การคำนวณจาก currentFolderPermissions
    });
}


// ลบสิทธิ์โฟลเดอร์ (ใช้ข้อมูลจริงเท่านั้น)
function removeFolderPermission(index) {
    const permission = currentFolderPermissions[index];
    
    if (!permission || !permission.id) {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบข้อมูลสิทธิ์ที่ต้องการลบ', 'error');
        return;
    }
    
    Swal.fire({
        title: 'ยืนยันการลบสิทธิ์',
        html: `
            <div class="text-left">
                <p class="mb-4">คุณต้องการลบสิทธิ์ของ <strong>${escapeHtml(permission.member_name)}</strong> หรือไม่?</p>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                        <span class="text-red-800 text-sm">การลบสิทธิ์นี้ไม่สามารถยกเลิกได้</span>
                    </div>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ลบสิทธิ์',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#ef4444'
    }).then((result) => {
        if (result.isConfirmed) {
            performRemovePermission(permission.id);
        }
    });
}
	
	
	function performRemovePermission(permissionId) {
    // แสดง loading
    Swal.fire({
        title: 'กำลังลบสิทธิ์...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const formData = new FormData();
    formData.append('permission_id', permissionId);
    
    fetch(API_BASE_URL + 'remove_folder_permission', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'ลบสิทธิ์สำเร็จ! 🎉',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            // Reload permissions
            loadFolderPermissions(currentManagingFolderId);
        } else {
            throw new Error(data.message || 'ไม่สามารถลบสิทธิ์ได้');
        }
    })
    .catch(error => {
        console.error('Error removing permission:', error);
        Swal.fire({
            icon: 'error',
            title: 'ไม่สามารถลบสิทธิ์ได้',
            text: error.message,
            confirmButtonText: 'ตกลง'
        });
    });
}
	


// แก้ไขสิทธิ์โฟลเดอร์ (ใช้ข้อมูลจริงเท่านั้น)
function editFolderPermission(index) {
    const permission = currentFolderPermissions[index];
    
    if (!permission || !permission.id) {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบข้อมูลสิทธิ์ที่ต้องการแก้ไข', 'error');
        return;
    }
    
    Swal.fire({
        title: '✏️ แก้ไขสิทธิ์',
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ผู้ใช้:</label>
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-user text-gray-600 mr-2"></i>
                            <span class="font-medium">${escapeHtml(permission.member_name)}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ระดับสิทธิ์:</label>
                    <select id="editPermissionLevel" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="read" ${permission.access_type === 'read' ? 'selected' : ''}>ดูอย่างเดียว</option>
                        <option value="write" ${permission.access_type === 'write' ? 'selected' : ''}>แก้ไขได้</option>
                        <option value="admin" ${permission.access_type === 'admin' ? 'selected' : ''}>ผู้ดูแล</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">หมดอายุ:</label>
                    <input type="date" id="editPermissionExpiry" 
                           value="${permission.expires_at ? permission.expires_at.split(' ')[0] : ''}"
                           class="w-full border border-gray-300 rounded px-3 py-2">
                </div>
            </div>
        `,
        width: '500px',
        confirmButtonText: 'บันทึกการแก้ไข',
        showCancelButton: true,
        cancelButtonText: 'ยกเลิก',
        preConfirm: () => {
            return {
                permission_id: permission.id,
                access_type: document.getElementById('editPermissionLevel').value,
                expires_at: document.getElementById('editPermissionExpiry').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateFolderPermission(result.value);
        }
    });
}


// อัปเดตสิทธิ์โฟลเดอร์ (ใช้ข้อมูลจริงเท่านั้น)
function updateFolderPermission(data) {
    // แสดง loading
    Swal.fire({
        title: 'กำลังบันทึก...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const formData = new FormData();
    formData.append('permission_id', data.permission_id);
    formData.append('access_type', data.access_type);
    formData.append('expires_at', data.expires_at || '');
    
    fetch(API_BASE_URL + 'update_folder_permission', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'แก้ไขสิทธิ์สำเร็จ! 🎉',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            // Reload permissions
            loadFolderPermissions(currentManagingFolderId);
        } else {
            throw new Error(data.message || 'ไม่สามารถแก้ไขสิทธิ์ได้');
        }
    })
    .catch(error => {
        console.error('Error updating permission:', error);
        Swal.fire({
            icon: 'error',
            title: 'ไม่สามารถแก้ไขสิทธิ์ได้',
            text: error.message,
            confirmButtonText: 'ตกลง'
        });
    });
}

// =============================================
// 3. UI RENDERING FUNCTIONS
// =============================================

function renderExistingPermissions() {
    const container = document.getElementById('existingPermissionsList');
    if (!container) {
        console.warn('⚠️ existingPermissionsList container not found');
        return;
    }
    
    console.log('🎨 Rendering existing permissions:', currentFolderPermissions?.length || 0);
    
    if (!currentFolderPermissions || currentFolderPermissions.length === 0) {
        container.innerHTML = `
            <div class="p-8 text-center fade-in-up">
                <div class="mb-4">
                    <i class="fas fa-users text-4xl text-gray-300"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-600 mb-2">ยังไม่มีการตั้งสิทธิ์</h4>
                <p class="text-gray-500 text-sm mb-4">เพิ่มสิทธิ์ผู้ใช้โดยใช้ฟอร์มด้านบน</p>
                <button onclick="loadAvailableUsers()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>เพิ่มสิทธิ์แรก
                </button>
            </div>
        `;
        return;
    }
    
    let html = '';
    currentFolderPermissions.forEach((permission, index) => {
        const accessLevelText = getAccessTypeText(permission.access_type);
        const accessLevelColor = getAccessTypeColor(permission.access_type);
        const isExpired = permission.expires_at && new Date(permission.expires_at) < new Date();
        const canEdit = permission.access_type !== 'owner';
        
        html += `
            <div class="permission-item flex items-center justify-between p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors fade-in-up ${isExpired ? 'opacity-60 bg-red-50' : ''}" 
                 style="animation-delay: ${index * 0.1}s">
                <div class="flex items-center space-x-4">
                    <!-- Avatar -->
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-lg"></i>
                        </div>
                        ${isExpired ? '<div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center"><i class="fas fa-exclamation text-white text-xs"></i></div>' : ''}
                    </div>
                    
                    <!-- User Info -->
                    <div>
                        <h5 class="font-semibold text-gray-900">
                            ${escapeHtml(permission.member_name || 'ไม่ระบุชื่อ')}
                        </h5>
                        <p class="text-sm text-gray-600">
                            ${escapeHtml(permission.position_name || 'ไม่ระบุตำแหน่ง')}
                        </p>
                        
                        <!-- Permission Details -->
                        <div class="flex items-center space-x-2 mt-1 text-xs">
                            <span class="text-gray-500">
                                โดย: ${escapeHtml(permission.granted_by_name || 'ระบบ')}
                            </span>
                            ${permission.granted_at ? `
                                <span class="text-gray-400">•</span>
                                <span class="text-gray-500">
                                    ${formatDateThai(permission.granted_at)}
                                </span>
                            ` : ''}
                            ${permission.expires_at ? `
                                <span class="text-gray-400">•</span>
                                <span class="text-xs ${isExpired ? 'text-red-600 font-medium' : 'text-orange-600'}">
                                    หมดอายุ: ${formatDateThai(permission.expires_at)}
                                    ${isExpired ? ' (หมดอายุแล้ว)' : ''}
                                </span>
                            ` : ''}
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center space-x-3">
                    <!-- Permission Badge -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${accessLevelColor} transition-colors">
                        ${getAccessTypeIcon(permission.access_type)}
                        ${accessLevelText}
                    </span>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-1">
                        ${canEdit ? `
                            <button onclick="editFolderPermission(${index})" 
                                    class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" 
                                    title="แก้ไขสิทธิ์">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="removeFolderPermission(${index})" 
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors" 
                                    title="ลบสิทธิ์">
                                <i class="fas fa-trash"></i>
                            </button>
                        ` : `
                            <button class="p-2 text-gray-400 cursor-not-allowed rounded-lg" 
                                    title="ไม่สามารถแก้ไขได้" disabled>
                                <i class="fas fa-lock"></i>
                            </button>
                        `}
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    // เพิ่ม animation counter
    setTimeout(() => {
        const items = container.querySelectorAll('.permission-item');
        items.forEach((item, index) => {
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }, 50);
}

function populateUserSelect(users) {
    console.log('Populating user select with', users.length, 'users');
    
    const userSelect = document.getElementById('newPermissionUser');
    if (!userSelect) {
        console.error('newPermissionUser element not found');
        return;
    }
    
    // เคลียร์ options เดิม
    userSelect.innerHTML = '<option value="">-- เลือกผู้ใช้ --</option>';
    
    // ตรวจสอบว่ามี users หรือไม่
    if (!users || !Array.isArray(users) || users.length === 0) {
        userSelect.innerHTML = '<option value="">-- ไม่มีผู้ใช้ในระบบ --</option>';
        return;
    }
    
    // ใช้ currentFolderPermissions อย่างปลอดภัย
    const existingPermissions = currentFolderPermissions || [];
    
    users.forEach(user => {
        // ตรวจสอบว่าผู้ใช้นี้มีสิทธิ์อยู่แล้วหรือไม่
        const hasPermission = existingPermissions.some(p => 
            p.member_id == user.m_id || p.m_id == user.m_id
        );
        
        if (!hasPermission) {
            const option = document.createElement('option');
            option.value = user.m_id;
            option.textContent = `${user.name} - ${user.position_name || 'ไม่ระบุตำแหน่ง'}`;
            userSelect.appendChild(option);
        }
    });
    
    // ตรวจสอบว่ามี options หรือไม่
    if (userSelect.children.length === 1) { // มีแค่ option แรก
        userSelect.innerHTML = '<option value="">-- ผู้ใช้ทุกคนมีสิทธิ์แล้ว --</option>';
    }
    
    console.log('User select populated with', userSelect.children.length - 1, 'available users');
}

// =============================================
// 4. MODAL MANAGEMENT FUNCTIONS
// =============================================

// อัปเดต manageFolderPermissions() ให้ robust ขึ้น
function manageFolderPermissions(folderId, folderName) {
    console.log('🔐 Managing permissions for folder:', folderId, folderName);
    
    currentManagingFolderId = folderId;
    
    // อัปเดต UI
    const folderNameElement = document.getElementById('currentFolderNamePermissions');
    if (folderNameElement) {
        folderNameElement.textContent = `โฟลเดอร์: ${folderName}`;
    }
    
    // แสดง modal
    const modal = document.getElementById('folderPermissionsModal');
    if (modal) {
        modal.classList.remove('hidden');
        
        // โหลดข้อมูลจาก API ทันที
        loadFolderPermissions(folderId);
        loadAvailableUsers();
        loadPermissionStats(folderId);
        
    } else {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบ Modal สำหรับจัดการสิทธิ์', 'error');
    }
}


/**
 * ปิด Basic Folder Permissions Modal
 */
function closeFolderPermissionsModal() {
    console.log('Closing basic folder permissions modal');
    
    // ซ่อน modal หลัก
    const modal = document.getElementById('folderPermissionsModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    
    // รีเซ็ตตัวแปร global ที่ใช้งาน
    currentManagingFolderId = null;
    
    // รีเซ็ตฟอร์ม
    resetPermissionForm();
    
    // ล้างข้อมูล permissions
    clearPermissionsDisplay();
}

/**
 * บันทึกการเปลี่ยนแปลงสิทธิ์
 */
function saveFolderPermissions() {
    if (!currentManagingFolderId) {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบ ID โฟลเดอร์ที่ต้องการจัดการ', 'error');
        return;
    }
    
    const permissionCount = currentFolderPermissions ? currentFolderPermissions.length : 0;
    
    Swal.fire({
        title: 'บันทึกการเปลี่ยนแปลง',
        html: `
            <div class="text-left">
                <p class="mb-3">คุณต้องการบันทึกการตั้งค่าสิทธิ์หรือไม่?</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <h4 class="font-medium text-blue-800 mb-2">สรุปสิทธิ์:</h4>
                    <div class="text-sm text-blue-700">
                        จำนวนผู้ใช้ที่มีสิทธิ์: <strong>${permissionCount}</strong> คน
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#3b82f6'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ! 🎉',
                text: 'การตั้งค่าสิทธิ์ได้รับการบันทึกเรียบร้อยแล้ว',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                closeFolderPermissionsModal();
                // รีเฟรชรายการไฟล์ถ้ามีฟังก์ชัน
                if (typeof refreshFileList === 'function') {
                    refreshFileList();
                }
            });
        }
    });
}

// ปิด Subfolder Permissions Modal (ถ้ามี)
function closeSubfolderPermissionsModal() {
    console.log('Closing subfolder permissions modal');
    
    const modal = document.getElementById('subfolderPermissionsModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    
    // รีเซ็ตตัวแปร subfolder
    currentManagingFolderId = null;
    currentInheritedPermissions = [];
    currentDirectPermissions = [];
    currentEffectivePermissions = [];
    currentPermissionMode = 'inherited';
}

// =============================================
// 5. HELPER FUNCTIONS
// =============================================

// อัปเดตสถิติสิทธิ์
function updatePermissionSummary(stats = null) {
    console.log('📊 Updating permission summary:', stats);
    
    // ถ้าไม่มี stats ให้คำนวณจาก currentFolderPermissions
    if (!stats && currentFolderPermissions) {
        stats = {
            owner: 0,
            admin: 0,
            write: 0,
            read: 0,
            total: currentFolderPermissions.length
        };
        
        currentFolderPermissions.forEach(permission => {
            if (permission.expires_at && new Date(permission.expires_at) < new Date()) {
                return; // ข้าม permission ที่หมดอายุ
            }
            
            switch(permission.access_type) {
                case 'owner':
                    stats.owner++;
                    break;
                case 'admin':
                    stats.admin++;
                    break;
                case 'write':
                case 'read_write':
                    stats.write++;
                    break;
                case 'read':
                case 'read_only':
                    stats.read++;
                    break;
            }
        });
    }
    
    // ตั้งค่าเริ่มต้นถ้าไม่มี stats
    if (!stats) {
        stats = { owner: 0, admin: 0, write: 0, read: 0, total: 0 };
    }
    
    // อัปเดต UI พร้อม animation
    const elements = {
        'ownerCount': stats.owner || 0,
        'adminCount': stats.admin || 0,
        'writeCount': stats.write || 0,
        'readCount': stats.read || 0
    };
    
    Object.entries(elements).forEach(([elementId, count]) => {
        const element = document.getElementById(elementId);
        if (element) {
            // เพิ่ม animation การนับ
            animateNumber(element, parseInt(element.textContent) || 0, count, 500);
        }
    });
}
	
	
	function showPermissionsLoading() {
    const container = document.getElementById('existingPermissionsList');
    if (container) {
        container.innerHTML = `
            <div class="p-8 text-center">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-blue-600 border-t-transparent mb-4"></div>
                <p class="text-gray-600 font-medium">กำลังโหลดข้อมูลสิทธิ์...</p>
            </div>
        `;
    }
    
    // Reset summary counts
    ['ownerCount', 'adminCount', 'writeCount', 'readCount'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = '⏳';
        }
    });
}

function hidePermissionsLoading() {
    // จะถูกแทนที่โดย renderExistingPermissions()
}

function showPermissionsError(errorMessage) {
    const container = document.getElementById('existingPermissionsList');
    if (container) {
        container.innerHTML = `
            <div class="p-8 text-center">
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle text-5xl text-red-400"></i>
                </div>
                <h4 class="font-semibold text-red-800 mb-2">ไม่สามารถโหลดข้อมูลสิทธิ์ได้</h4>
                <p class="text-red-600 text-sm mb-4">${escapeHtml(errorMessage)}</p>
                <button onclick="loadFolderPermissions(currentManagingFolderId)" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-redo mr-2"></i>ลองใหม่
                </button>
            </div>
        `;
    }
}

function showUsersError(errorMessage) {
    const userSelect = document.getElementById('newPermissionUser');
    if (userSelect) {
        userSelect.innerHTML = `
            <option value="">❌ ไม่สามารถโหลดผู้ใช้ได้</option>
        `;
        userSelect.disabled = true;
        
        // Show retry button
        const parent = userSelect.parentElement;
        if (parent && !parent.querySelector('.retry-btn')) {
            const retryBtn = document.createElement('button');
            retryBtn.type = 'button';
            retryBtn.className = 'retry-btn mt-2 px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700';
            retryBtn.innerHTML = '<i class="fas fa-redo mr-1"></i>ลองใหม่';
            retryBtn.onclick = () => {
                retryBtn.remove();
                userSelect.disabled = false;
                loadAvailableUsers();
            };
            parent.appendChild(retryBtn);
        }
    }
}


// =============================================
// 🆕 NEW: Fallback Data Functions
// =============================================

function loadFallbackPermissions(folderId) {
    console.log('🔄 Loading fallback permissions for:', folderId);
    
    // ใช้ข้อมูล fallback เพื่อให้ UI ยังใช้งานได้
    currentFolderPermissions = [
        {
            id: 'fallback_1',
            member_id: 1,
            member_name: 'ผู้ดูแลระบบ',
            position_name: 'Admin',
            access_type: 'admin',
            granted_by_name: 'ระบบ',
            granted_at: new Date().toISOString(),
            expires_at: null
        }
    ];
    
    renderExistingPermissions();
    updatePermissionSummary();
}

function getFallbackUsers() {
    return [
        {
            m_id: 1,
            name: 'ผู้ดูแลระบบ',
            position_name: 'Admin',
            google_drive_enabled: true
        },
        {
            m_id: 2, 
            name: 'ผู้ใช้ทดสอบ',
            position_name: 'User',
            google_drive_enabled: true
        }
    ];
}
	
	
	function animateNumber(element, from, to, duration) {
    if (from === to) {
        element.textContent = to;
        return;
    }
    
    const start = Date.now();
    const step = () => {
        const progress = Math.min((Date.now() - start) / duration, 1);
        const current = Math.round(from + (to - from) * progress);
        element.textContent = current;
        
        if (progress < 1) {
            requestAnimationFrame(step);
        }
    };
    step();
}

	

	function getAccessTypeIcon(accessType) {
    const icons = {
        'owner': '<i class="fas fa-crown mr-1"></i>',
        'admin': '<i class="fas fa-user-shield mr-1"></i>',
        'write': '<i class="fas fa-edit mr-1"></i>',
        'read': '<i class="fas fa-eye mr-1"></i>',
        'no_access': '<i class="fas fa-ban mr-1"></i>'
    };
    return icons[accessType] || '<i class="fas fa-user mr-1"></i>';
}
	
	
	function formatDateThai(dateStr) {
    if (!dateStr) return '';
    try {
        const date = new Date(dateStr);
        return date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return dateStr;
    }
}
	

function resetPermissionForm() {
    const userSelect = document.getElementById('newPermissionUser');
    const permissionSelect = document.getElementById('newPermissionLevel');
    const expiryInput = document.getElementById('newPermissionExpiry');
    
    if (userSelect) userSelect.value = '';
    if (permissionSelect) permissionSelect.value = 'read';
    if (expiryInput) expiryInput.value = '';
}

// 🔄 เรียกใช้เมื่อเปิด modal
const originalManageFolderPermissions = window.manageFolderPermissions;
window.manageFolderPermissions = function(folderId, folderName) {
    console.log('🔐 Enhanced Managing permissions for folder:', folderId, folderName);
    
    // เรียก function เดิม
    if (originalManageFolderPermissions) {
        originalManageFolderPermissions(folderId, folderName);
    }
    
    // โหลด users ทันทีหลังจากเปิด modal
    setTimeout(() => {
        loadAvailableUsers();
    }, 300);
};

	
	const originalManageSubfolderPermissions = window.manageSubfolderPermissions;
window.manageSubfolderPermissions = function(folderId, folderName, parentPath) {
    console.log('⚙️ Enhanced Managing subfolder permissions:', folderId, folderName);
    
    // เรียก function เดิม
    if (originalManageSubfolderPermissions) {
        originalManageSubfolderPermissions(folderId, folderName, parentPath);
    }
    
    // โหลด users ทันทีหลังจากเปิด modal
    setTimeout(() => {
        loadAvailableUsers();
    }, 300);
};
	
	document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 DOM loaded - initializing user dropdown system');
    
    // ถ้ามี permission modal อยู่แล้ว ให้โหลด users ทันที
    const permissionModal = document.getElementById('folderPermissionsModal');
    if (permissionModal && !permissionModal.classList.contains('hidden')) {
        setTimeout(() => {
            loadAvailableUsers();
        }, 500);
    }
});


function clearPermissionsDisplay() {
    const existingPermissions = document.getElementById('existingPermissionsList');
    if (existingPermissions) {
        existingPermissions.innerHTML = '<div class="p-4 text-center text-gray-500">ไม่มีข้อมูลสิทธิ์</div>';
    }
    
    // รีเซ็ตสถิติ
    const elements = ['ownerCount', 'adminCount', 'writeCount', 'readCount'];
    elements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) element.textContent = '0';
    });
}

// แสดง Error Message สำหรับ API
function showPermissionError(apiEndpoint, errorMessage) {
    Swal.fire({
        icon: 'error',
        title: 'API Error',
        html: `
            <div class="text-left">
                <p class="mb-3">ไม่สามารถเชื่อมต่อ API ได้</p>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <h4 class="font-medium text-red-800 mb-2">รายละเอียดข้อผิดพลาด:</h4>
                    <p class="text-sm text-red-700 mb-2"><strong>Endpoint:</strong> ${apiEndpoint}</p>
                    <p class="text-sm text-red-700"><strong>Error:</strong> ${errorMessage}</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
                    <h4 class="font-medium text-yellow-800 mb-2">API endpoints ที่จำเป็น:</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• add_folder_permission</li>
                        <li>• get_folder_permissions</li>
                        <li>• get_available_users</li>
                        <li>• get_folder_permission_stats</li>
                        <li>• remove_folder_permission</li>
                        <li>• update_folder_permission</li>
                    </ul>
                </div>
            </div>
        `,
        confirmButtonText: 'ตกลง'
    });
}

// Helper Functions สำหรับ UI
function getAccessTypeText(accessType) {
    const map = {
        'owner': 'เจ้าของ',
        'admin': 'ผู้ดูแล', 
        'write': 'แก้ไขได้',
        'read': 'ดูอย่างเดียว',
        'no_access': 'ไม่มีสิทธิ์'
    };
    return map[accessType] || accessType;
}

function getAccessTypeColor(accessType) {
    const map = {
        'owner': 'bg-green-100 text-green-800',
        'admin': 'bg-blue-100 text-blue-800',
        'write': 'bg-yellow-100 text-yellow-800',
        'read': 'bg-gray-100 text-gray-800',
        'no_access': 'bg-red-100 text-red-800'
    };
    return map[accessType] || 'bg-gray-100 text-gray-800';
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    try {
        const date = new Date(dateStr);
        return date.toLocaleDateString('th-TH');
    } catch (e) {
        return dateStr;
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getCurrentPath() {
    // ใช้ breadcrumb data หากมี
    if (breadcrumbData && breadcrumbData.length > 0) {
        let path = 'Organization Drive';
        breadcrumbData.forEach(crumb => {
            path += ' / ' + crumb.name;
        });
        return path;
    }
    return 'Organization Drive';
}

// =============================================
// 6. SUBFOLDER PERMISSIONS (Advanced)
// =============================================

// จัดการสิทธิ์โฟลเดอร์ย่อย (Advanced)
function manageSubfolderPermissions(folderId, folderName, parentPath) {
    console.log('Managing advanced permissions for subfolder:', folderId, folderName);
    
    currentManagingFolderId = folderId;
    currentPermissionMode = 'inherited';
    
    // อัปเดต breadcrumb
    const breadcrumbElement = document.getElementById('subfolderBreadcrumb');
    if (breadcrumbElement) {
        breadcrumbElement.textContent = parentPath + ' / ' + folderName;
    }
    
    // แสดง modal
    const modal = document.getElementById('subfolderPermissionsModal');
    if (modal) {
        modal.classList.remove('hidden');
        
        // โหลดข้อมูล (จะแสดง error ถ้า API ไม่พร้อม)
        loadInheritedPermissions(folderId);
        loadDirectPermissions(folderId);
        loadEffectivePermissions(folderId);
    } else {
        // Fallback ไปใช้ basic modal
        manageFolderPermissions(folderId, folderName);
    }
}

// โหลดสิทธิ์สืบทอด (ใช้ข้อมูลจริงเท่านั้น)
function loadInheritedPermissions(folderId) {
    console.log('📎 Loading inherited permissions for:', folderId);
    
    fetch(API_BASE_URL + 'get_inherited_permissions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'folder_id=' + encodeURIComponent(folderId)
    })
    .then(response => {
        console.log('📡 get_inherited_permissions response status:', response.status);
        
        const contentType = response.headers.get('content-type');
        
        if (!response.ok) {
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `HTTP ${response.status}`);
                });
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        }
        
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => {
                console.error('Non-JSON response received:', text.substring(0, 300));
                throw new Error('Server returned HTML instead of JSON');
            });
        }
    })
    .then(data => {
        if (data && data.success && data.data !== undefined) {
            currentInheritedPermissions = data.data || [];
            renderInheritedPermissions(currentInheritedPermissions);
        } else {
            throw new Error(data.message || 'Invalid API response format');
        }
    })
    .catch(error => {
        console.error('❌ Error loading inherited permissions:', error);
        currentInheritedPermissions = [];
        renderInheritedPermissions([]);
        showInheritedError(error.message);
    });
}


// โหลดสิทธิ์เฉพาะ (ใช้ข้อมูลจริงเท่านั้น)
function loadDirectPermissions(folderId) {
    console.log('⚡ Loading direct permissions for:', folderId);
    
    fetch(API_BASE_URL + 'get_direct_permissions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'folder_id=' + encodeURIComponent(folderId)
    })
    .then(response => {
        console.log('📡 get_direct_permissions response status:', response.status);
        
        const contentType = response.headers.get('content-type');
        
        if (!response.ok) {
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `HTTP ${response.status}`);
                });
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        }
        
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => {
                console.error('Non-JSON response received:', text.substring(0, 300));
                throw new Error('Server returned HTML instead of JSON');
            });
        }
    })
    .then(data => {
        if (data && data.success && data.data !== undefined) {
            currentDirectPermissions = data.data || [];
            renderDirectPermissions(currentDirectPermissions);
        } else {
            throw new Error(data.message || 'Invalid API response format');
        }
    })
    .catch(error => {
        console.error('❌ Error loading direct permissions:', error);
        currentDirectPermissions = [];
        renderDirectPermissions([]);
        showDirectError(error.message);
    });
}

// โหลดสิทธิ์ที่มีผลจริง (ใช้ข้อมูลจริงเท่านั้น)
function loadEffectivePermissions(folderId) {
    console.log('👁️ Loading effective permissions for:', folderId);
    
    fetch(API_BASE_URL + 'get_effective_permissions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'folder_id=' + encodeURIComponent(folderId)
    })
    .then(response => {
        console.log('📡 get_effective_permissions response status:', response.status);
        
        // ตรวจสอบ Content-Type ก่อน
        const contentType = response.headers.get('content-type');
        console.log('Response content-type:', contentType);
        
        if (!response.ok) {
            // ถ้า response ไม่ OK แต่อาจจะเป็น JSON error
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `HTTP ${response.status}: ${response.statusText}`);
                });
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText} - Server returned non-JSON response`);
            }
        }
        
        // ตรวจสอบว่าเป็น JSON หรือไม่
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // ถ้าไม่ใช่ JSON อาจเป็น HTML error page
            return response.text().then(text => {
                console.error('Non-JSON response received:', text.substring(0, 500));
                throw new Error('Server returned HTML instead of JSON - Possible PHP error or missing endpoint');
            });
        }
    })
    .then(data => {
        console.log('✅ Effective permissions data:', data);
        
        if (data && data.success && data.data !== undefined) {
            currentEffectivePermissions = data.data || [];
            renderEffectivePermissions(currentEffectivePermissions);
            updateAdvancedSummary();
        } else {
            // API returned success: false หรือ invalid format
            console.warn('⚠️ API returned unsuccessful response:', data);
            throw new Error(data.message || 'Invalid API response format');
        }
    })
    .catch(error => {
        console.error('❌ Error loading effective permissions:', error);
        
        // แยกประเภท error เพื่อจัดการที่เหมาะสม
        let errorTitle = 'ไม่สามารถโหลดสิทธิ์ที่มีผลได้';
        let errorMessage = error.message;
        let shouldUseFallback = false;
        let troubleshooting = '';
        
        if (error.message.includes('HTTP 500')) {
            errorTitle = 'เซิร์ฟเวอร์มีปัญหา (HTTP 500)';
            errorMessage = 'มีข้อผิดพลาดภายในเซิร์ฟเวอร์';
            shouldUseFallback = true;
            troubleshooting = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-3">
                    <h4 class="font-medium text-red-800 mb-2">🔧 วิธีแก้ไข HTTP 500:</h4>
                    <ul class="text-sm text-red-700 space-y-1">
                        <li>• ตรวจสอบ PHP Error Log ของเซิร์ฟเวอร์</li>
                        <li>• ตรวจสอบว่า method get_effective_permissions() มีอยู่ใน Controller</li>
                        <li>• ตรวจสอบการเชื่อมต่อฐานข้อมูล</li>
                        <li>• ตรวจสอบ table tbl_google_drive_member_folder_access</li>
                    </ul>
                </div>
            `;
        } else if (error.message.includes('HTML instead of JSON')) {
            errorTitle = 'API Endpoint ไม่ถูกต้อง';
            errorMessage = 'Endpoint get_effective_permissions ยังไม่ได้ implement หรือมี PHP Error';
            shouldUseFallback = true;
            troubleshooting = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
                    <h4 class="font-medium text-yellow-800 mb-2">💡 วิธีแก้ไข:</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• เพิ่ม method get_effective_permissions() ใน Controller</li>
                        <li>• ตรวจสอบ routing ของ CodeIgniter</li>
                        <li>• ตรวจสอบ .htaccess file</li>
                    </ul>
                </div>
            `;
        } else if (error.message.includes('HTTP 404')) {
            errorTitle = 'ไม่พบ API Endpoint';
            errorMessage = 'Method get_effective_permissions ไม่มีอยู่';
            shouldUseFallback = true;
            troubleshooting = `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-3">
                    <h4 class="font-medium text-blue-800 mb-2">📝 สิ่งที่ต้องทำ:</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• เพิ่ม method get_effective_permissions() ใน Google_drive_system.php</li>
                        <li>• ใช้ code ที่แนบมาเพื่อ implement method</li>
                    </ul>
                </div>
            `;
        }
        
        // ถ้าควรใช้ fallback จะทำการคำนวณจากข้อมูลที่มีอยู่
        if (shouldUseFallback) {
            console.log('🔄 Using fallback calculation for effective permissions');
            const fallbackData = calculateEffectiveFromExisting();
            currentEffectivePermissions = fallbackData;
            renderEffectivePermissions(fallbackData);
            updateAdvancedSummary();
            
            // แสดง warning แทน error
            showEffectivePermissionsWarning(errorMessage);
        } else {
            // แสดง error dialog
            currentEffectivePermissions = [];
            renderEffectivePermissions([]);
            showEffectivePermissionsError(errorTitle, errorMessage, troubleshooting);
        }
    });
}

	
	
	function showEffectivePermissionsError(title, message, troubleshooting = '') {
    const container = document.getElementById('effectivePermissionsPreview');
    if (container) {
        container.innerHTML = `
            <div class="text-center py-8">
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle text-5xl text-red-400"></i>
                </div>
                <h4 class="font-semibold text-red-800 mb-2">${escapeHtml(title)}</h4>
                <p class="text-red-600 text-sm mb-4">${escapeHtml(message)}</p>
                
                ${troubleshooting}
                
                <div class="mt-4 space-y-2">
                    <button onclick="refreshEffectivePreview()" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-redo mr-2"></i>ลองใหม่
                    </button>
                    <button onclick="useCalculatedFallback()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-calculator mr-2"></i>ใช้การคำนวณ Fallback
                    </button>
                </div>
                
                <div class="mt-4 text-xs text-gray-500">
                    💡 หากปัญหานี้เกิดขึ้นต่อเนื่อง กรุณาติดต่อผู้ดูแลระบบ
                </div>
            </div>
        `;
    }
}
	
	
function useCalculatedFallback() {
    console.log('🔄 Manually triggering fallback calculation');
    const fallbackData = calculateEffectiveFromExisting();
    currentEffectivePermissions = fallbackData;
    renderEffectivePermissions(fallbackData);
    updateAdvancedSummary();
    
    // แสดง toast notification
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'ใช้การคำนวณ Fallback แล้ว',
            text: 'แสดงผลจากข้อมูลที่มีอยู่ในระบบ',
            showConfirmButton: false,
            timer: 3000
        });
    }
}
	
	
	
	
	// คำนวณ Effective Permissions จากข้อมูลที่มีอยู่ (Fallback)
function calculateEffectiveFromExisting() {
    console.log('📊 Calculating effective permissions from existing data');
    
    const effectivePermissions = [];
    const processedMembers = new Set();
    
    // รวม Direct permissions ก่อน (มีลำดับความสำคัญสูงกว่า)
    if (currentDirectPermissions && Array.isArray(currentDirectPermissions)) {
        currentDirectPermissions.forEach(permission => {
            if (permission && permission.member_id && !processedMembers.has(permission.member_id)) {
                const effectivePermission = {
                    ...permission,
                    permission_source_type: 'direct',
                    source_description: 'สิทธิ์เฉพาะ',
                    final_access_type: permission.access_type,
                    is_fallback: true
                };
                effectivePermissions.push(effectivePermission);
                processedMembers.add(permission.member_id);
            }
        });
    }
    
    // ตามด้วย Inherited permissions (สำหรับคนที่ยังไม่มี direct permission)
    if (currentInheritedPermissions && Array.isArray(currentInheritedPermissions)) {
        currentInheritedPermissions.forEach(permission => {
            if (permission && permission.member_id && !processedMembers.has(permission.member_id)) {
                const effectivePermission = {
                    ...permission,
                    permission_source_type: 'inherited',
                    source_description: 'สืบทอดจาก Parent',
                    final_access_type: permission.access_type,
                    is_fallback: true
                };
                effectivePermissions.push(effectivePermission);
                processedMembers.add(permission.member_id);
            }
        });
    }
    
    // ถ้าไม่มีทั้งสองแบบ ใช้ current folder permissions
    if (effectivePermissions.length === 0 && currentFolderPermissions && Array.isArray(currentFolderPermissions)) {
        currentFolderPermissions.forEach(permission => {
            if (permission && permission.member_id && !processedMembers.has(permission.member_id)) {
                const effectivePermission = {
                    ...permission,
                    permission_source_type: 'basic',
                    source_description: 'สิทธิ์พื้นฐาน',
                    final_access_type: permission.access_type,
                    is_fallback: true
                };
                effectivePermissions.push(effectivePermission);
                processedMembers.add(permission.member_id);
            }
        });
    }
    
    console.log(`📋 Calculated ${effectivePermissions.length} effective permissions from existing data`);
    return effectivePermissions;
}


// แสดง Warning แทน Error สำหรับ Effective Permissions
function showEffectivePermissionsWarning(errorMessage) {
    const container = document.getElementById('effectivePermissionsPreview');
    if (container && container.querySelector('.fallback-warning')) {
        return; // มี warning อยู่แล้ว
    }
    
    // เพิ่ม warning banner
    if (container) {
        const warningBanner = document.createElement('div');
        warningBanner.className = 'fallback-warning bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3';
        warningBanner.innerHTML = `
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2 mt-1"></i>
                <div class="text-sm flex-1">
                    <p class="text-yellow-800 font-medium">⚠️ ใช้การคำนวณ Fallback</p>
                    <p class="text-yellow-700">API มีปัญหา: ${escapeHtml(errorMessage)}</p>
                    <p class="text-yellow-600 mt-1">แสดงผลจากข้อมูลที่มีอยู่ แต่อาจไม่ครบถ้วน</p>
                </div>
                <div class="flex space-x-1 ml-2">
                    <button onclick="refreshEffectivePreview()" 
                            class="text-yellow-600 hover:text-yellow-800" title="ลองเรียก API ใหม่">
                        <i class="fas fa-redo text-sm"></i>
                    </button>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                            class="text-yellow-600 hover:text-yellow-800" title="ปิด warning">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
        `;
        container.insertBefore(warningBanner, container.firstChild);
    }
}

	
	
// Render Functions สำหรับ Advanced Permissions
function renderInheritedPermissions(permissions) {
    console.log('📎 Rendering inherited permissions:', permissions.length);
    
    const container = document.getElementById('inheritedPermissionsListAdvanced');
    if (!container) {
        console.warn('inheritedPermissionsListAdvanced container not found');
        return;
    }
    
    if (!permissions || permissions.length === 0) {
        container.innerHTML = `
            <div class="text-center py-6 text-blue-600">
                <i class="fas fa-link text-3xl mb-3 opacity-50"></i>
                <p class="font-medium">ไม่มีสิทธิ์สืบทอด</p>
                <p class="text-sm text-blue-500 mt-1">สิทธิ์จะมาจากโฟลเดอร์ Parent</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="space-y-2">';
    
    permissions.forEach(permission => {
        const accessLevelText = getAccessTypeText(permission.access_type);
        const accessLevelColor = getAccessTypeColor(permission.access_type);
        const isMock = permission.is_mock || false;
        
        html += `
            <div class="flex items-center justify-between py-2 px-3 bg-white border border-blue-200 rounded-lg ${isMock ? 'border-dashed' : ''}">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <h6 class="font-medium text-blue-900">${escapeHtml(permission.member_name || 'ผู้ใช้')}</h6>
                        <p class="text-xs text-blue-600">
                            📎 จาก: ${escapeHtml(permission.inherited_from_name || 'Parent Folder')}
                            ${permission.inheritance_level ? ` (ระดับ ${permission.inheritance_level})` : ''}
                            ${isMock ? ' <span class="text-yellow-600">(ตัวอย่าง)</span>' : ''}
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${accessLevelColor}">
                        ${getAccessTypeIcon(permission.access_type)}
                        ${accessLevelText}
                    </span>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    // เพิ่มข้อมูลสรุป
    html += `
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between text-sm">
                <span class="text-blue-800">📊 สรุป: ${permissions.length} รายการสิทธิ์สืบทอด</span>
                <button onclick="refreshInheritedPermissions()" 
                        class="text-blue-600 hover:text-blue-800 text-xs">
                    <i class="fas fa-sync-alt mr-1"></i>รีเฟรช
                </button>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}

function renderDirectPermissions(permissions) {
    console.log('⚡ Rendering direct permissions:', permissions.length);
    
    const container = document.getElementById('directPermissionsListAdvanced');
    if (!container) {
        console.warn('directPermissionsListAdvanced container not found');
        return;
    }
    
    if (!permissions || permissions.length === 0) {
        container.innerHTML = `
            <div class="text-center py-6 text-green-600">
                <i class="fas fa-star text-3xl mb-3 opacity-50"></i>
                <p class="font-medium">ยังไม่มีสิทธิ์เฉพาะ</p>
                <p class="text-sm text-green-500 mt-1">เพิ่มสิทธิ์เฉพาะสำหรับโฟลเดอร์นี้</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="space-y-2">';
    
    permissions.forEach((permission, index) => {
        const accessLevelText = getAccessTypeText(permission.access_type);
        const accessLevelColor = getAccessTypeColor(permission.access_type);
        const isExpired = permission.expires_at && new Date(permission.expires_at) < new Date();
        const canEdit = permission.access_type !== 'owner';
        
        html += `
            <div class="flex items-center justify-between p-3 bg-white border border-green-200 rounded-lg ${isExpired ? 'opacity-60 bg-red-50' : ''}">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-green-50 rounded-full flex items-center justify-center">
                        <i class="fas fa-star text-green-600"></i>
                    </div>
                    <div>
                        <h5 class="font-medium text-gray-900">${escapeHtml(permission.member_name || 'ผู้ใช้')}</h5>
                        <p class="text-sm text-gray-500">${escapeHtml(permission.position_name || 'ไม่ระบุตำแหน่ง')}</p>
                        <div class="text-xs text-gray-500 mt-1">
                            ${permission.permission_mode ? `โหมด: ${permission.permission_mode}` : ''}
                            ${permission.expires_at ? ` • หมดอายุ: ${formatDateThai(permission.expires_at)}` : ''}
                            ${isExpired ? ' <span class="text-red-600">(หมดอายุแล้ว)</span>' : ''}
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${accessLevelColor}">
                        ${getAccessTypeIcon(permission.access_type)}
                        ${accessLevelText}
                    </span>
                    
                    ${canEdit ? `
                        <div class="flex space-x-1">
                            <button onclick="editDirectPermission(${index})" 
                                    class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded" 
                                    title="แก้ไข">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <button onclick="removeDirectPermission(${index})" 
                                    class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded" 
                                    title="ลบ">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    // เพิ่มข้อมูลสรุป
    html += `
        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center justify-between text-sm">
                <span class="text-green-800">📊 สรุป: ${permissions.length} รายการสิทธิ์เฉพาะ</span>
                <button onclick="refreshDirectPermissions()" 
                        class="text-green-600 hover:text-green-800 text-xs">
                    <i class="fas fa-sync-alt mr-1"></i>รีเฟรช
                </button>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}

function renderEffectivePermissions(permissions) {
    console.log('👁️ Rendering effective permissions:', permissions.length);
    
    const container = document.getElementById('effectivePermissionsPreview');
    if (!container) {
        console.warn('effectivePermissionsPreview container not found');
        return;
    }
    
    if (!permissions || permissions.length === 0) {
        container.innerHTML = `
            <div class="text-center py-6 text-purple-600">
                <i class="fas fa-eye-slash text-3xl mb-3 opacity-50"></i>
                <p class="font-medium">ไม่มีสิทธิ์ที่มีผล</p>
                <p class="text-sm text-purple-500 mt-1">ไม่มีใครสามารถเข้าถึงโฟลเดอร์นี้ได้</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="space-y-2">';
    
    permissions.forEach(permission => {
        const accessLevelText = getAccessTypeText(permission.final_access_type || permission.access_type);
        const accessLevelColor = getAccessTypeColor(permission.final_access_type || permission.access_type);
        const sourceType = permission.permission_source_type;
        const isMock = permission.is_mock || false;
        
        // กำหนดสีตาม source type
        let sourceColor = 'text-gray-600';
        let sourceIcon = 'fas fa-info-circle';
        
        if (sourceType === 'inherited') {
            sourceColor = 'text-blue-600';
            sourceIcon = 'fas fa-link';
        } else if (sourceType === 'direct') {
            sourceColor = 'text-green-600';
            sourceIcon = 'fas fa-star';
        }
        
        html += `
            <div class="flex items-center justify-between p-3 bg-white border border-purple-200 rounded-lg ${isMock ? 'border-dashed' : ''}">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <h6 class="font-medium text-purple-900">${escapeHtml(permission.member_name || 'ผู้ใช้')}</h6>
                        <div class="flex items-center text-xs ${sourceColor} mt-1">
                            <i class="${sourceIcon} mr-1"></i>
                            <span>${permission.source_description || 'สิทธิ์ที่มีผล'}</span>
                            ${isMock ? '<span class="ml-2 text-yellow-600">(ตัวอย่าง)</span>' : ''}
                        </div>
                    </div>
                </div>
                
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${accessLevelColor}">
                    ${getAccessTypeIcon(permission.final_access_type || permission.access_type)}
                    ${accessLevelText}
                </span>
            </div>
        `;
    });
    
    html += '</div>';
    
    // เพิ่มข้อมูลสรุปและคำอธิบาย
    const inheritedCount = permissions.filter(p => p.permission_source_type === 'inherited').length;
    const directCount = permissions.filter(p => p.permission_source_type === 'direct').length;
    
    // ตรวจสอบสถานะ inheritance
    const inheritanceCheckbox = document.getElementById('enableInheritanceAdvanced');
    const isInheritanceEnabled = inheritanceCheckbox ? inheritanceCheckbox.checked : true;
    
    html += `
        <div class="mt-4 p-3 bg-purple-50 border border-purple-200 rounded-lg">
            <div class="text-sm text-purple-800 mb-2">
                <div class="flex items-center justify-between">
                    <span class="font-medium">📊 สรุปสิทธิ์ที่มีผล</span>
                    <button onclick="refreshEffectivePreview()" 
                            class="text-purple-600 hover:text-purple-800 text-xs">
                        <i class="fas fa-sync-alt mr-1"></i>รีเฟรช
                    </button>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-2 text-xs">
                    <div class="text-center">
                        <span class="font-medium">${permissions.length}</span>
                        <div class="text-purple-600">รวม</div>
                    </div>
                    <div class="text-center">
                        <span class="font-medium text-blue-600">${inheritedCount}</span>
                        <div class="text-blue-600">สืบทอด</div>
                    </div>
                    <div class="text-center">
                        <span class="font-medium text-green-600">${directCount}</span>
                        <div class="text-green-600">เฉพาะ</div>
                    </div>
                </div>
            </div>
            <div class="text-xs text-purple-600">
                💡 นี่คือสิทธิ์ที่ผู้ใช้จะได้รับจริงในโฟลเดอร์นี้
                ${!isInheritanceEnabled ? '<span class="text-orange-600 font-medium"> (การสืบทอดปิดอยู่ - แสดงเฉพาะสิทธิ์ตรง)</span>' : ''}
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}


// =============================================
// 7. INITIALIZATION
// =============================================

// Export functions to global scope
window.manageFolderPermissions = manageFolderPermissions;
window.closeFolderPermissionsModal = closeFolderPermissionsModal;
window.saveFolderPermissions = saveFolderPermissions;
window.addFolderPermission = addFolderPermission;
window.removeFolderPermission = removeFolderPermission;
window.editFolderPermission = editFolderPermission;
window.manageSubfolderPermissions = manageSubfolderPermissions;
window.closeSubfolderPermissionsModal = closeSubfolderPermissionsModal;

// Initialize เมื่อ DOM โหลดเสร็จ
document.addEventListener('DOMContentLoaded', function() {
    // เพิ่ม event listeners สำหรับ ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModals = [
                'folderPermissionsModal',
                'subfolderPermissionsModal'
            ];
            
            openModals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden')) {
                    switch(modalId) {
                        case 'folderPermissionsModal':
                            closeFolderPermissionsModal();
                            break;
                        case 'subfolderPermissionsModal':
                            closeSubfolderPermissionsModal();
                            break;
                    }
                }
            });
        }
    });
    
    console.log('✅ Clean permissions management system initialized (Real Data Only)');
});

console.log('📋 Clean permissions script loaded successfully - Real Data Only!');
</script>

<script>
// =============================================
// ⚙️ ADVANCED SUBFOLDER MANAGEMENT
// =============================================

/**
 * เปิด Modal สิทธิ์ขั้นสูง
 */
function manageSubfolderPermissions(folderId, folderName, parentPath) {
    console.log('⚙️ Managing advanced permissions for:', folderId, folderName);
    
    currentManagingFolderId = folderId;
    
    // อัปเดต breadcrumb
    const breadcrumbElement = document.getElementById('advancedBreadcrumb');
    if (breadcrumbElement) {
        breadcrumbElement.textContent = parentPath + ' / ' + folderName;
    }
    
    // แสดง modal
    const modal = document.getElementById('subfolderAdvancedModal');
    if (modal) {
        modal.classList.remove('hidden');
        
        // โหลดข้อมูลทันที
        loadAdvancedPermissionData(folderId);
    } else {
        // Fallback ไปใช้ basic modal
        manageFolderPermissions(folderId, folderName);
    }
}

/**
 * โหลดข้อมูลสิทธิ์ขั้นสูง
 */
function loadAdvancedPermissionData(folderId) {
    // แสดง loading
    showAdvancedLoading();
    
    // โหลดข้อมูลแบบ parallel
    Promise.all([
        loadInheritedPermissions(folderId),
        loadDirectPermissions(folderId),
        loadEffectivePermissions(folderId),
        loadAvailableUsers()
    ]).then(() => {
        hideAdvancedLoading();
        updateAdvancedSummary();
    }).catch(error => {
        console.error('Error loading advanced permission data:', error);
        hideAdvancedLoading();
    });
}

/**
 * แสดง Loading สำหรับ Advanced Modal
 */
function showAdvancedLoading() {
    const containers = [
        'inheritedPermissionsListAdvanced',
        'directPermissionsListAdvanced', 
        'effectivePermissionsPreview'
    ];
    
    containers.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.innerHTML = `
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-300 border-t-blue-600 mb-3"></div>
                    <p class="text-sm text-gray-600">กำลังโหลดข้อมูลสิทธิ์...</p>
                </div>
            `;
        }
    });
    
    // Reset counters
    ['inheritedPermissionCount', 'directPermissionCount', 'overridePermissionCount', 'totalEffectiveCount'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = '⏳';
        }
    });
}

	
	
function showInheritedError(errorMessage) {
    const container = document.getElementById('inheritedPermissionsListAdvanced');
    if (container) {
        container.innerHTML = `
            <div class="text-center py-6 text-red-600">
                <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                <p class="font-medium">ไม่สามารถโหลดสิทธิ์สืบทอดได้</p>
                <p class="text-sm text-red-500 mt-1">${escapeHtml(errorMessage)}</p>
                <button onclick="refreshInheritedPermissions()" 
                        class="mt-3 px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                    <i class="fas fa-redo mr-1"></i>ลองใหม่
                </button>
            </div>
        `;
    }
}

	
	
	/**
 * 🔧 Edit/Remove Direct Permission Functions
 */
function editDirectPermission(index) {
    const permission = currentDirectPermissions[index];
    if (!permission) return;
    
    Swal.fire({
        title: '✏️ แก้ไขสิทธิ์เฉพาะ',
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">ผู้ใช้:</label>
                    <div class="p-2 bg-gray-50 rounded">${escapeHtml(permission.member_name)}</div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">สิทธิ์:</label>
                    <select id="editDirectAccess" class="w-full border rounded px-3 py-2">
                        <option value="no_access" ${permission.access_type === 'no_access' ? 'selected' : ''}>🚫 ไม่มีสิทธิ์</option>
                        <option value="read" ${permission.access_type === 'read' ? 'selected' : ''}>👁️ ดูอย่างเดียว</option>
                        <option value="write" ${permission.access_type === 'write' ? 'selected' : ''}>✏️ แก้ไขได้</option>
                        <option value="admin" ${permission.access_type === 'admin' ? 'selected' : ''}>👑 ผู้ดูแล</option>
                    </select>
                </div>
            </div>
        `,
        confirmButtonText: 'บันทึก',
        showCancelButton: true,
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            const newAccessType = document.getElementById('editDirectAccess').value;
            updateDirectPermission(permission.id, newAccessType);
        }
    });
}

	
	function removeDirectPermission(index) {
    const permission = currentDirectPermissions[index];
    if (!permission) return;
    
    Swal.fire({
        title: 'ลบสิทธิ์เฉพาะ',
        text: `ลบสิทธิ์เฉพาะของ ${permission.member_name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#ef4444'
    }).then((result) => {
        if (result.isConfirmed) {
            performRemovePermission(permission.id);
        }
    });
}
	
	

	
	function updateDirectPermission(permissionId, newAccessType) {
    // ใช้ API update_folder_permission ที่มีอยู่แล้ว
    updateFolderPermission({
        permission_id: permissionId,
        access_type: newAccessType
    });
}
	
	
function showDirectError(errorMessage) {
    const container = document.getElementById('directPermissionsListAdvanced');
    if (container) {
        container.innerHTML = `
            <div class="text-center py-6 text-red-600">
                <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                <p class="font-medium">ไม่สามารถโหลดสิทธิ์เฉพาะได้</p>
                <p class="text-sm text-red-500 mt-1">${escapeHtml(errorMessage)}</p>
                <button onclick="refreshDirectPermissions()" 
                        class="mt-3 px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                    <i class="fas fa-redo mr-1"></i>ลองใหม่
                </button>
            </div>
        `;
    }
}

	


function hideAdvancedLoading() {
    // จะถูกแทนที่โดย render functions
}

/**
 * อัปเดตสรุปสิทธิ์
 */
function updateAdvancedSummary() {
    const inheritedCount = currentInheritedPermissions?.length || 0;
    const directCount = currentDirectPermissions?.length || 0;
    const effectiveCount = currentEffectivePermissions?.length || 0;
    const overrideCount = directCount; // สำหรับตอนนี้
    
    document.getElementById('inheritedPermissionCount').textContent = inheritedCount;
    document.getElementById('directPermissionCount').textContent = directCount;
    document.getElementById('overridePermissionCount').textContent = overrideCount;
    document.getElementById('totalEffectiveCount').textContent = effectiveCount;
}

/**
 * เพิ่มสิทธิ์ Override เฉพาะ
 */
function addDirectOverride() {
    const userId = document.getElementById('newDirectUserAdvanced')?.value;
    const accessType = document.getElementById('newDirectAccessAdvanced')?.value;
    
    if (!userId) {
        Swal.fire('กรุณาเลือกผู้ใช้', '', 'warning');
        return;
    }
    
    // แสดงยืนยัน
    const actionText = accessType === 'no_access' ? 'ยกเลิกสิทธิ์' : 'เพิ่มสิทธิ์เฉพาะ';
    const actionColor = accessType === 'no_access' ? 'text-red-600' : 'text-green-600';
    
    Swal.fire({
        title: `${actionText}สำหรับโฟลเดอร์นี้เฉพาะ`,
        html: `
            <div class="text-left">
                <p class="mb-3">การกระทำนี้จะ:</p>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <ul class="text-sm space-y-1">
                        <li class="flex items-center">
                            <i class="fas fa-arrow-right text-yellow-600 mr-2"></i>
                            <span class="${actionColor} font-medium">${actionText}เฉพาะโฟลเดอร์นี้</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-arrow-right text-yellow-600 mr-2"></i>
                            <span>Override สิทธิ์ที่สืบทอดมา (ถ้ามี)</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-arrow-right text-yellow-600 mr-2"></i>
                            <span>ไม่กระทบโฟลเดอร์อื่น</span>
                        </li>
                    </ul>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            performDirectOverride(userId, accessType);
        }
    });
}

/**
 * ดำเนินการ Override จริง
 */
function performDirectOverride(userId, accessType) {
    const formData = new FormData();
    formData.append('folder_id', currentManagingFolderId);
    formData.append('member_id', userId);
    formData.append('access_type', accessType);
    formData.append('permission_action', 'override');
    formData.append('apply_to_subfolders', '0'); // ไม่ใช้กับ subfolder
    
    fetch(API_BASE_URL + 'add_direct_folder_permission', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Override สำเร็จ! 🎉',
                timer: 2000,
                showConfirmButton: false
            });
            
            // Reset form และ reload
            document.getElementById('newDirectUserAdvanced').value = '';
            document.getElementById('newDirectAccessAdvanced').value = 'read';
            loadAdvancedPermissionData(currentManagingFolderId);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        Swal.fire('เกิดข้อผิดพลาด', error.message, 'error');
    });
}

/**
 * 🔄 Refresh Functions
 */
function refreshInheritedPermissions() {
    if (currentManagingFolderId) {
        loadInheritedPermissions(currentManagingFolderId);
    }
}

function refreshDirectPermissions() {
    if (currentManagingFolderId) {
        loadDirectPermissions(currentManagingFolderId);
    }
}

function refreshEffectivePreview() {
    if (currentManagingFolderId) {
        // ตรวจสอบสถานะ inheritance ก่อน
        const inheritanceCheckbox = document.getElementById('enableInheritanceAdvanced');
        const isInheritanceEnabled = inheritanceCheckbox ? inheritanceCheckbox.checked : true;
        
        if (isInheritanceEnabled) {
            // ถ้าเปิด inheritance ให้โหลดจาก API
            loadEffectivePermissions(currentManagingFolderId);
        } else {
            // ถ้าปิด inheritance ให้คำนวณจาก direct permissions เท่านั้น
            updateEffectivePermissionsAfterInheritanceToggle(false);
        }
    }
}

/**
 * รีเซ็ตการเปลี่ยนแปลง
 */
function resetAdvancedChanges() {
    Swal.fire({
        title: 'รีเซ็ตการเปลี่ยนแปลง?',
        text: 'การเปลี่ยนแปลงที่ยังไม่บันทึกจะหายไป',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'รีเซ็ต',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            loadAdvancedPermissionData(currentManagingFolderId);
        }
    });
}

/**
 * บันทึกการเปลี่ยนแปลงขั้นสูง
 */
function saveAdvancedPermissions() {
    Swal.fire({
        title: 'บันทึกการเปลี่ยนแปลง',
        html: `
            <div class="text-left">
                <p class="mb-3">ยืนยันการบันทึกการตั้งค่าสิทธิ์ขั้นสูง?</p>
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <h4 class="font-medium text-green-800 mb-2">📋 สิ่งที่จะเกิดขึ้น:</h4>
                    <ul class="text-sm text-green-700 space-y-1">
                        <li>✅ สิทธิ์ Override จะมีผลทันที</li>
                        <li>✅ การตั้งค่าจะถูกบันทึกถาวร</li>
                        <li>✅ ผู้ใช้จะเห็นการเปลี่ยนแปลงทันที</li>
                    </ul>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ! 🎉',
                text: 'การตั้งค่าสิทธิ์ขั้นสูงได้รับการบันทึกแล้ว',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                closeSubfolderAdvancedModal();
                if (typeof refreshFileList === 'function') {
                    refreshFileList();
                }
            });
        }
    });
}

/**
 * ปิด Modal ขั้นสูง
 */
function closeSubfolderAdvancedModal() {
    const modal = document.getElementById('subfolderAdvancedModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    
    // รีเซ็ตตัวแปร
    currentManagingFolderId = null;
    currentInheritedPermissions = [];
    currentDirectPermissions = [];
    currentEffectivePermissions = [];
}

console.log('⚙️ Advanced Subfolder Management UI loaded!');
</script>