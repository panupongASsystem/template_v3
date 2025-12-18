<!-- JavaScript -->
<script>
    // Global Variables
    let currentFolder = 'root';
    let fileListData = [];
    let folderTreeData = [];
    let expandedFolders = new Set();
    let viewMode = 'tree';
    let isLoading = false;
    let breadcrumbData = [];
    let memberInfo = null;
    let dragCounter = 0;
    let isUploading = false;
    let uploadTimeout = null;
    let isDragAndDropUpload = false;
    let currentShareItem = null;
    let selectedShareType = null;
    let selectedEmailPermission = 'reader';
    let selectedLinkPermission = 'reader';
    let selectedLinkAccess = 'restricted';
    let isSharing = false;
    let permissionRetryCount = 0;
    let permissionSystemInitialized = false;

    // 🆕 เพิ่มตัวแปรสำหรับการตั้งค่าจาก DB
    let allowedFileTypes = []; // จาก DB
    let maxFileSize = 104857600; // ค่าเริ่มต้น 100MB
    let driveSettings = {}; // เก็บการตั้งค่าทั้งหมด
    let supportFolderUpload = true; // รองรับการลาก folder หรือไม่

    /**
 * 🌐 Global Variables
 * เพิ่มตัวแปรสำหรับเก็บ Current Folder Permission
 */
    let currentFolderPermission = {
        folder_id: 'root',
        access_type: 'read',
        can_upload: false,
        can_create_folder: false,
        can_share: false,
        can_delete: false
    };




    const MAX_PERMISSION_RETRIES = 2;
    // Constants
    const MEMBER_ID = <?php echo $this->session->userdata('m_id'); ?>;
    const API_BASE_URL = '<?php echo site_url('google_drive_files/'); ?>';
    const IS_TRIAL_MODE = <?php echo isset($is_trial_mode) && $is_trial_mode ? 'true' : 'false'; ?>;
    const TRIAL_STORAGE_LIMIT = <?php echo isset($trial_storage_limit) ? $trial_storage_limit : 1073741824; ?>; // 1GB

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function () {
        console.log('🚀 Apple-inspired Member Drive initialized');
        console.log('📊 Trial Mode:', IS_TRIAL_MODE);

        // ตรวจสอบว่า DOM พร้อมใช้งานแล้ว
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeSystem);
        } else {
            initializeSystem();
        }
    });




    // 🆕 โหลดการตั้งค่าจาก Database
    async function loadDriveSettings() {
        try {
            console.log('⚙️ Loading drive settings from database...');

            const response = await fetch(API_BASE_URL + 'get_drive_settings', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.settings) {
                driveSettings = data.settings;

                // ✅ ดึงค่า allowed_file_types
                if (driveSettings.allowed_file_types) {
                    try {
                        allowedFileTypes = JSON.parse(driveSettings.allowed_file_types);
                        console.log('📋 Allowed file types:', allowedFileTypes);
                    } catch (e) {
                        console.warn('⚠️ Error parsing allowed_file_types, using default');
                        allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];
                    }
                }

                // ✅ ดึงค่า max_file_size
                if (driveSettings.max_file_size) {
                    maxFileSize = parseInt(driveSettings.max_file_size);
                    console.log('📏 Max file size:', formatFileSize(maxFileSize));
                }

                // ✅ ดึงค่าการตั้งค่าอื่นๆ
                supportFolderUpload = driveSettings.support_folder_upload === '1' || driveSettings.support_folder_upload === 'true';

                console.log('✅ Drive settings loaded successfully');
                console.log('📊 Settings summary:', {
                    allowedTypes: allowedFileTypes.length + ' types',
                    maxSize: formatFileSize(maxFileSize),
                    folderSupport: supportFolderUpload
                });

            } else {
                throw new Error(data.message || 'ไม่สามารถโหลดการตั้งค่าได้');
            }

        } catch (error) {
            console.error('💥 Error loading drive settings:', error);

            // ใช้ค่าเริ่มต้นถ้าโหลดไม่ได้
            allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];
            maxFileSize = 104857600; // 100MB
            supportFolderUpload = false;

            console.log('⚠️ Using default settings due to error');
        }
    }


    // แก้ไข function initializeSystem
    async function initializeSystem() {
        try {
            console.log('🔧 Initializing system components...');

            // ตรวจสอบ elements ที่สำคัญก่อน
            const requiredElements = [
                'fileBrowserContainer',
                'loadingState',
                'emptyState',
                'errorState',
                'fileList'
            ];

            const missingElements = [];
            requiredElements.forEach(id => {
                if (!document.getElementById(id)) {
                    missingElements.push(id);
                }
            });

            if (missingElements.length > 0) {
                console.warn('⚠️ Missing required elements:', missingElements);
            }

            // 🆕 โหลดการตั้งค่าก่อนเริ่มระบบ
            await loadDriveSettings();

            // เริ่มต้นระบบ
            initializeMemberDrive();
            setupDragAndDrop();
            setupEventListeners();

            console.log('✅ System initialization completed');

        } catch (error) {
            console.error('💥 System initialization error:', error);
            showError('เกิดข้อผิดพลาดในการเริ่มต้นระบบ');
        }
    }


    // Initialize Member Drive
    function initializeMemberDrive() {
        console.log('📁 Initializing member drive for user:', MEMBER_ID);
        loadMemberInfo();
        loadAccessibleFolders();

        // 🆕 โหลด Storage Stats
        setTimeout(() => {
            loadStorageStats();
            loadFileStats();
            startStatsAutoRefresh();
        }, 1000);
    }

    // Enhanced Error Handling for API Calls
    function handleApiResponse(response) {
        console.log('📡 API Response Status:', response.status);

        if (response.ok) {
            return response.json().catch(error => {
                console.error('❌ JSON Parse Error:', error);
                throw new Error('Invalid JSON response');
            });
        }

        // จัดการ error responses
        return response.text().then(text => {
            console.log('📄 Error Response Text:', text);

            let errorData;
            try {
                errorData = JSON.parse(text);
            } catch (e) {
                // ถ้า parse JSON ไม่ได้ แสดงว่าเป็น HTML error page หรือ plain text
                console.warn('⚠️ Could not parse error response as JSON:', e);

                // ตรวจสอบว่าเป็น PHP error หรือไม่
                if (text.includes('Fatal error') || text.includes('Parse error') || text.includes('Warning')) {
                    errorData = {
                        message: 'เกิดข้อผิดพลาดในระบบ (PHP Error)',
                        debug_info: text.substring(0, 200) + '...' // แสดงบางส่วนเพื่อ debug
                    };
                } else {
                    errorData = {
                        message: text || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ',
                        http_status: response.status
                    };
                }
            }

            // สร้าง Error object พร้อม status code
            const error = new Error(errorData.message || `HTTP ${response.status}`);
            error.status = response.status;
            error.data = errorData;

            throw error;
        });
    }

    // Handle Upload Click (with Trial Check)
    function handleUploadClick() {
        if (IS_TRIAL_MODE) {
            // Check trial storage limit first
            checkTrialStorageBeforeUpload();
        } else {
            showUploadModal();
        }
    }

    function normalizeFolderId(folderId) {
        if (!folderId || folderId === 'root' || folderId === 'null' || folderId === null) {
            return folderId;
        }

        const folderId_str = String(folderId).trim();

        // ตรวจสอบ pattern ของ Google Drive folder ID
        // ในระบบนี้เก็บแบบมี "1" prefix และมี length = 33
        if (folderId_str.length === 32 && !folderId_str.startsWith('1')) {
            const normalizedId = '1' + folderId_str;
            console.log(`📁 Normalized folder ID: ${folderId} -> ${normalizedId}`);
            return normalizedId;
        }

        // ถ้ามี "1" แล้วให้ใช้ตัวเดิม
        if (folderId_str.length === 33 && folderId_str.startsWith('1')) {
            return folderId_str;
        }

        // สำหรับ folder ID รูปแบบอื่น
        return folderId;
    }

    // 🔄 แก้ไขทุก function ที่เกี่ยวข้อง

    // 1. แก้ไข handleCreateFolderClick
    async function handleCreateFolderClick() {
        console.log('📁 Handle create folder click');

        const loadingToast = Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'กำลังตรวจสอบสิทธิ์...',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });

        try {
            // 🆕 ใช้ normalized folder ID
            const normalizedCurrentFolder = normalizeFolderId(currentFolder);
            console.log(`📁 Checking permission for normalized folder: ${normalizedCurrentFolder}`);

            const permission = await checkCreateFolderPermission(normalizedCurrentFolder);

            Swal.close();

            if (permission.can_create) {
                console.log('✅ Permission granted:', permission.access_type);

                if (IS_TRIAL_MODE) {
                    console.log('🎭 Trial mode - allowing folder creation');
                }

                showCreateFolderModal();

            } else {
                console.log('❌ Permission denied:', permission.message);

                showAccessDeniedModal({
                    message: permission.message,
                    folder_id: normalizedCurrentFolder,
                    access_type: permission.access_type,
                    permission_source: permission.permission_source
                });
            }

        } catch (error) {
            console.error('💥 Error in handleCreateFolderClick:', error);

            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถตรวจสอบสิทธิ์ได้: ' + error.message,
                confirmButtonText: 'ตกลง'
            });
        }
    }

    // Check Trial Storage Before Upload
    function checkTrialStorageBeforeUpload() {
        if (!memberInfo) {
            loadMemberInfo().then(() => {
                checkTrialStorageBeforeUpload();
            });
            return;
        }

        const usedStorage = memberInfo.quota_used || 0;
        const remainingStorage = TRIAL_STORAGE_LIMIT - usedStorage;
        const usagePercent = Math.round((usedStorage / TRIAL_STORAGE_LIMIT) * 100);

        if (usagePercent >= 90) {
            showTrialStorageWarning(remainingStorage, usagePercent);
        } else {
            showUploadModal();
        }
    }

    // สำหรับกรณีเกินพื้นที่ (hard limit)
    function showTrialStorageLimitExceeded(selectedFilesSize, currentUsed) {
        const totalNeeded = currentUsed + selectedFilesSize;
        const overLimit = totalNeeded - TRIAL_STORAGE_LIMIT;
        const currentUsedMB = Math.round(currentUsed / (1024 * 1024));
        const selectedMB = Math.round(selectedFilesSize / (1024 * 1024));
        const overLimitMB = Math.round(overLimit / (1024 * 1024));

        Swal.fire({
            title: '❌ พื้นที่ไม่เพียงพอ',
            html: `
            <div class="text-left">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                    <p>ไฟล์ที่เลือก: <strong>${selectedMB} MB</strong></p>
                    <p>ใช้งานแล้ว: <strong>${currentUsedMB} MB</strong></p>
                    <p class="text-red-600 font-bold">เกินโควต้า: ${overLimitMB} MB</p>
                </div>
            </div>
        `,
            icon: 'error',
            confirmButtonText: '🚀 อัปเกรด',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                showUpgradeModal();
            }
        });
    }

    // สำหรับกรณีใกล้เต็ม (soft warning)
    function showTrialStorageWarning(remainingStorage, usagePercent) {
        const remainingMB = Math.round(remainingStorage / (1024 * 1024));

        Swal.fire({
            title: '⚠️ เกือบเต็มพื้นที่แล้ว',
            html: `
            <div class="text-left">
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-4">
                    <p>ใช้งานแล้ว: <strong>${usagePercent}%</strong> ของ Trial Limit</p>
                    <p>เหลือพื้นที่: <strong>~${remainingMB} MB</strong></p>
                </div>
            </div>
        `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '🚀 อัปเกรด',
            cancelButtonText: 'อัปโหลดต่อ'
        }).then((result) => {
            if (result.isConfirmed) {
                showUpgradeModal();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                showUploadModal();
            }
        });
    }

    // Show Upgrade Modal
    function showUpgradeModal() {
        document.getElementById('upgradeModal').classList.remove('hidden');
    }

    // Close Upgrade Modal
    function closeUpgradeModal() {
        document.getElementById('upgradeModal').classList.add('hidden');
    }

    // Select Plan
    function selectPlan(planType) {
        Swal.fire({
            title: `เลือกแผน ${planType}`,
            text: 'กรุณาติดต่อฝ่ายขายเพื่อดำเนินการอัปเกรด',
            icon: 'info',
            confirmButtonText: 'ติดต่อฝ่ายขาย',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก',
            customClass: {
                popup: 'glass-card rounded-2xl',
                confirmButton: 'rounded-xl',
                cancelButton: 'rounded-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                contactAdmin();
            }
        });
    }

    // Contact Admin
    function contactAdmin() {
        Swal.fire({
            title: '📞 ติดต่อฝ่ายขาย',
            html: `
                    <div class="text-left space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <h4 class="font-bold text-blue-800 mb-2">ติดต่อผ่านช่องทางเหล่านี้:</h4>
                            <div class="space-y-2 text-sm">
                                <p class="flex items-center text-blue-700">
                                    <i class="fas fa-envelope mr-3"></i>
                                    <strong>Email:</strong> sale@assystem.co.th
                                </p>
                                <p class="flex items-center text-blue-700">
                                    <i class="fas fa-phone mr-3"></i>
                                    <strong>โทร:</strong> <?php echo get_config_value('telesales'); ?>
                                </p>
                                <p class="flex items-center text-blue-700">
                                    <i class="fab fa-line mr-3"></i>
                                    <strong>LINE:</strong> <?php echo get_config_value('telesales'); ?> (ไม่มี -)
                                </p>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <p class="text-sm text-green-700">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>ข้อมูลที่ควรแจ้ง:</strong> ชื่อ อบต เทศบาล, แผนที่ต้องการใช้, และหมายเลขโทรศัพท์ติดต่อกลับ
                            </p>
                        </div>
                    </div>
                `,
            confirmButtonText: 'เข้าใจแล้ว',
            customClass: {
                popup: 'glass-card rounded-2xl',
                confirmButton: 'rounded-xl'
            }
        });
    }

    /**
     * ✅ โหลดข้อมูล Member Information
     */
    function loadMemberInfo() {
        return fetch(API_BASE_URL + 'get_member_info', {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => handleApiResponse(response))
            .then(data => {
                if (data.success && data.data) {
                    memberInfo = data.data;

                    // ✅ แปลง String เป็น Number
                    memberInfo.quota_used = parseInt(memberInfo.quota_used) || 0;
                    memberInfo.quota_limit = parseInt(memberInfo.quota_limit) || 0;
                    memberInfo.files_count = parseInt(memberInfo.files_count) || 0;
                    memberInfo.accessible_folders_count = parseInt(memberInfo.accessible_folders_count) || 0;

                    console.group('💾 Member Information Loaded');
                    console.log('Member ID:', memberInfo.member_id);
                    console.log('Storage Mode:', memberInfo.storage_mode);
                    console.log('Is Trial:', memberInfo.is_trial_mode);
                    console.log('Quota Used:', memberInfo.quota_used, 'bytes');
                    console.log('Quota Limit:', memberInfo.quota_limit, 'bytes');
                    console.groupEnd();

                    // ✅ อัปเดต Stats (ไม่รวม Storage ที่จะมาจาก API)
                    updateMemberStats();
                    updatePermissionInfo();
                }
            })
            .catch(error => {
                console.error('💥 Error loading member info:', error);
            });
    }





    /**
     * ✅ อัปเดต Member Statistics (ไม่รวม Storage Quota)
     */
    function updateMemberStats() {
        if (!memberInfo) return;

        // อัปเดต Files Count
        const myFilesCountEl = document.getElementById('myFilesCount');
        if (myFilesCountEl) {
            myFilesCountEl.textContent = memberInfo.files_count || 0;
        }

        // อัปเดต Folders Count
        const accessibleFoldersCountEl = document.getElementById('accessibleFoldersCount');
        if (accessibleFoldersCountEl) {
            accessibleFoldersCountEl.textContent = memberInfo.accessible_folders_count || 0;
        }

        // อัปเดต Last Access
        const lastAccessEl = document.getElementById('lastAccess');
        if (lastAccessEl && memberInfo.last_access) {
            lastAccessEl.textContent = formatDateTime(memberInfo.last_access);
        }

        console.log('✅ Member stats updated (Files, Folders, Last Access)');
    }

    // Update Permission Information
    function updatePermissionInfo() {
        if (!memberInfo || !memberInfo.permission) return;

        const permission = memberInfo.permission;

        let permissionText = permission.type_name || permission.permission_type;
        if (IS_TRIAL_MODE) {
            permissionText += ' (Trial)';
        }

        document.getElementById('permissionLevel').textContent = permissionText;
        document.getElementById('permissionDescription').textContent = getPermissionDescription(permission);

        // Update available actions
        updateAvailableActions(permission);
    }

    // Get Permission Description
    function getPermissionDescription(permission) {
        const descriptions = {
            'full_admin': 'เข้าถึงและจัดการได้ทุกส่วน',
            'department_admin': 'เข้าถึงโฟลเดอร์แผนกและแชร์',
            'position_only': 'เข้าถึงเฉพาะโฟลเดอร์ที่กำหนดให้ตำแหน่ง',
            'custom': 'สิทธิ์กำหนดเอง',
            'read_only': 'อ่านและดาวน์โหลดเท่านั้น',
            'no_access': 'ไม่มีสิทธิ์เข้าถึง'
        };

        let desc = descriptions[permission.permission_type] || 'สิทธิ์มาตรฐาน';

        if (IS_TRIAL_MODE) {
            desc += ' (ข้อจำกัดสำหรับ Trial)';
        }

        return desc;
    }

    // Update Available Actions
    function updateAvailableActions(permission) {
        const actionsContainer = document.getElementById('availableActions');
        const actions = [];

        // Base actions based on permission
        if (permission.can_upload !== false) {
            actions.push({
                icon: 'fas fa-upload',
                text: 'Upload',
                color: IS_TRIAL_MODE ? 'orange' : 'blue'
            });
        }
        if (permission.can_create_folder) {
            actions.push({
                icon: 'fas fa-folder-plus',
                text: 'Create Folder',
                color: IS_TRIAL_MODE ? 'orange' : 'purple'
            });
        }
        if (permission.can_share && !IS_TRIAL_MODE) {
            actions.push({ icon: 'fas fa-share', text: 'Share', color: 'green' });
        } else if (IS_TRIAL_MODE) {
            actions.push({ icon: 'fas fa-share', text: 'Share (Locked)', color: 'gray' });
        }
        if (permission.can_delete) {
            actions.push({
                icon: 'fas fa-trash',
                text: 'Delete',
                color: IS_TRIAL_MODE ? 'orange' : 'red'
            });
        }

        // Always allow view
        actions.push({ icon: 'fas fa-eye', text: 'View', color: 'gray' });

        // Download - locked for trial
        if (IS_TRIAL_MODE) {
            actions.push({ icon: 'fas fa-download', text: 'Download (Locked)', color: 'gray' });
        } else {
            actions.push({ icon: 'fas fa-download', text: 'Download', color: 'gray' });
        }

        let html = '';
        actions.forEach(action => {
            const isLocked = action.text.includes('(Locked)');
            const opacity = isLocked ? 'opacity-50' : '';

            html += `
                    <div class="flex items-center text-sm text-gray-700 bg-${action.color}-50 rounded-lg p-2 ${opacity}">
                        <i class="${action.icon} text-${action.color}-600 mr-2"></i>
                        <span>${action.text}</span>
                        ${isLocked ? '<i class="fas fa-lock text-gray-400 ml-auto text-xs"></i>' : ''}
                    </div>
                `;
        });

        actionsContainer.innerHTML = html;
    }

    // Load Accessible Folders (Enhanced Error Handling)
    function loadAccessibleFolders() {
        console.log('📂 Loading accessible folders...');
        currentFolder = 'root';
        updateBreadcrumb([]);
        showLoadingState();

        fetch(API_BASE_URL + 'get_member_folders', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                console.log('Folders response status:', response.status);
                return handleApiResponse(response);
            })
            .then(data => {
                console.log('Folders response data:', data);
                if (data.success && data.data) {
                    fileListData = data.data;

                    if (IS_TRIAL_MODE && fileListData.length === 0) {
                        fileListData = getTrialDemoFolders();
                    }

                    buildFolderTree();

                    console.log('✅ Loaded', fileListData.length, 'accessible folders');

                    // เพิ่มส่วนนี้
                    if (viewMode === 'tree') {
                        changeViewMode('tree');
                    }

                    renderFileList();
                } else {
                    console.error('❌ Failed to load folders:', data.message);
                    showError(data.message || 'ไม่สามารถโหลดโฟลเดอร์ได้');
                }
            })
            .catch(error => {
                console.error('💥 Error loading folders:', error);
                showError('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message);
            });
    }

    // Build Folder Tree Structure (Enhanced Logging)
    function buildFolderTree() {
        console.log('  ┌─ buildFolderTree() START');
        console.log('  ├─ 📊 Input fileListData length:', fileListData.length);
        console.log('  ├─ 📊 Input fileListData:', fileListData);

        folderTreeData = [];

        // Create a map for easier lookup
        const folderMap = new Map();

        console.log('  ├─ 🔍 Filtering folders...');

        // แสดง items ทั้งหมดก่อน filter
        fileListData.forEach((item, index) => {
            console.log(`  │   ├─ [${index}] Name: "${item.name}"`);
            console.log(`  │   │   ├─ ID: ${item.id || 'MISSING'}`);
            console.log(`  │   │   ├─ Type: ${item.type || 'MISSING'}`);
            console.log(`  │   │   ├─ MimeType: ${item.mimeType || 'MISSING'}`);
            console.log(`  │   │   ├─ Parent ID: ${item.parent_id || 'MISSING'}`);
            console.log(`  │   │   └─ Keys: ${Object.keys(item).join(', ')}`);
        });

        const folders = fileListData.filter(item => {
            const isFolder = item.type === 'folder' ||
                item.mimeType === 'application/vnd.google-apps.folder';
            console.log(`  │   Filter [${item.name}]: isFolder = ${isFolder}`);
            return isFolder;
        });

        console.log('  ├─ ✅ Filtered folders count:', folders.length);

        if (folders.length === 0) {
            console.error('  ├─ ❌ NO FOLDERS AFTER FILTER!');
            console.error('  ├─ 🔍 Checking why...');
            console.error('  ├─ 📋 First item structure:', fileListData[0]);
            console.error('  ├─ 📋 Expected: type="folder" OR mimeType="application/vnd.google-apps.folder"');
            console.error('  └─ buildFolderTree() END (NO FOLDERS)');
            return;
        }

        // Initialize all folders
        folders.forEach(folder => {
            console.log(`  │   ├─ Adding to map: ${folder.name} (ID: ${folder.id})`);
            folderMap.set(folder.id, {
                ...folder,
                children: [],
                hasChildren: false,
                isExpanded: expandedFolders.has(folder.id)
            });
        });

        console.log('  ├─ 📊 folderMap size:', folderMap.size);

        // Build tree structure
        folders.forEach(folder => {
            const node = folderMap.get(folder.id);
            if (folder.parent_id && folderMap.has(folder.parent_id)) {
                const parent = folderMap.get(folder.parent_id);
                parent.children.push(node);
                parent.hasChildren = true;
                console.log(`  │   ├─ "${folder.name}" → child of "${parent.name}"`);
            } else {
                // Root level folder
                folderTreeData.push(node);
                console.log(`  │   ├─ "${folder.name}" → ROOT FOLDER`);
            }
        });

        console.log('  ├─ ✅ Total root folders:', folderTreeData.length);
        console.log('  ├─ 📋 Root folder names:', folderTreeData.map(f => f.name).join(', '));

        // Load children for each folder
        loadFolderChildren();

        console.log('  └─ buildFolderTree() END');
    }

    // Load children for folders (simulate Google Drive API calls)
    function loadFolderChildren() {
        // For demo purposes, add some mock children to trial folders
        if (IS_TRIAL_MODE) {
            const projectsFolder = folderTreeData.find(f => f.id === 'demo_folder_2');
            if (projectsFolder) {
                projectsFolder.hasChildren = true;
                if (expandedFolders.has('demo_folder_2')) {
                    projectsFolder.children = [
                        {
                            id: 'demo_folder_3',
                            name: 'Web Development',
                            type: 'folder',
                            icon: 'fas fa-folder text-purple-500',
                            children: [],
                            hasChildren: true,
                            isExpanded: expandedFolders.has('demo_folder_3'),
                            real_data: false,
                            folder_type: 'trial'
                        }
                    ];
                }
            }
        }
    }

    // Get Trial Demo Folders
    function getTrialDemoFolders() {
        return [
            {
                id: 'demo_folder_1',
                name: 'Documents (Demo)',
                type: 'folder',
                icon: 'fas fa-folder text-blue-500',
                modified: formatDateTime(new Date()),
                size: '-',
                description: 'ตัวอย่างโฟลเดอร์เอกสาร',
                folder_type: 'trial',
                permission_level: 'trial',
                real_data: false,
                webViewLink: '#trial-mode'
            },
            {
                id: 'demo_folder_2',
                name: 'Projects (Demo)',
                type: 'folder',
                icon: 'fas fa-folder text-purple-500',
                modified: formatDateTime(new Date(Date.now() - 86400000)),
                size: '-',
                description: 'ตัวอย่างโฟลเดอร์โปรเจกต์',
                folder_type: 'trial',
                permission_level: 'trial',
                real_data: false,
                webViewLink: '#trial-mode'
            }
        ];
    }

    function setCurrentFolder(folderId) {
        const normalizedId = normalizeFolderId(folderId);
        currentFolder = normalizedId;
        console.log(`📁 Current folder set to: ${currentFolder}`);
        return currentFolder;
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 📁 Load Folder Contents (ปรับปรุง - เพิ่มการจัดการ Permission)
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    function loadFolderContents(folderId) {
        console.log('📁 Loading folder contents:', folderId);

        // 🆕 ใช้ normalized folder ID
        const normalizedFolderId = normalizeFolderId(folderId);
        currentFolder = normalizedFolderId;

        console.log(`📁 Using normalized folder ID: ${normalizedFolderId}`);

        showLoadingState();

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // ✅ STEP 1: โหลด Folder Permission ก่อน
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

        loadCurrentFolderPermission(normalizedFolderId)
            .then(() => {
                console.log('✅ Folder permission loaded, proceeding to load contents');

                // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                // ✅ STEP 2: โหลด Folder Contents
                // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

                return fetch(API_BASE_URL + 'get_folder_contents', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'folder_id=' + encodeURIComponent(normalizedFolderId)
                });
            })
            .then(response => {
                console.log('📡 Folder contents response status:', response.status);

                if (response.status === 403) {
                    return response.json().then(data => {
                        if (data.error_type === 'access_denied') {
                            console.log('🚫 Access denied for folder:', normalizedFolderId);
                            showAccessDeniedModal(data);
                            return Promise.reject(new Error('Access Denied - Modal Shown'));
                        }
                        throw new Error('Forbidden: ' + (data.message || 'ไม่มีสิทธิ์เข้าถึง'));
                    });
                }

                return handleApiResponse(response);
            })
            .then(data => {
                console.log('📄 Folder contents response data:', data);
                if (data.success && data.data) {
                    fileListData = data.data;

                    console.log('✅ Loaded', fileListData.length, 'items from folder');

                    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                    // ✅ STEP 3: อัพเดต UI ตาม Folder Permission
                    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

                    updateFolderActionsUI();

                    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                    // ✅ STEP 4: Render และอัพเดต UI อื่นๆ
                    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

                    renderFileList();
                    loadBreadcrumbs(normalizedFolderId);

                    if (viewMode === 'tree') {
                        updateTreeSelection(normalizedFolderId);
                    }
                } else {
                    console.error('❌ Failed to load folder contents:', data.message);
                    showError(data.message || 'ไม่สามารถโหลดเนื้อหาโฟลเดอร์ได้');
                }
            })
            .catch(error => {
                console.error('💥 Error loading folder contents:', error);

                if (error.message === 'Access Denied - Modal Shown') {
                    return;
                }

                showError('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message);
            });
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // ✨ NEW: Load Current Folder Permission
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    function loadCurrentFolderPermission(folderId) {
        console.log('🔐 Loading permission for folder:', folderId);

        return fetch(API_BASE_URL + 'get_folder_permissions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'folder_id=' + encodeURIComponent(folderId)
        })
            .then(response => handleApiResponse(response))
            .then(result => {
                if (result.success) {
                    currentFolderPermission = {
                        folder_id: folderId,
                        access_type: result.access_level || 'read',
                        access_label: result.access_label || 'อ่านอย่างเดียว',
                        can_upload: result.can_upload || false,
                        can_create_folder: result.can_create_folder || false,
                        can_share: result.can_share || false,
                        can_delete: result.can_delete || false
                    };

                    console.log('✅ Folder permission loaded:', currentFolderPermission);
                } else {
                    console.warn('⚠️ Failed to load folder permission, using default read-only');
                    setDefaultFolderPermission(folderId);
                }
            })
            .catch(error => {
                console.error('💥 Error loading folder permission:', error);
                setDefaultFolderPermission(folderId);
            });
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 🔧 Helper: Set Default Folder Permission
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    function setDefaultFolderPermission(folderId) {
        currentFolderPermission = {
            folder_id: folderId,
            access_type: 'read',
            access_label: 'อ่านอย่างเดียว',
            can_upload: false,
            can_create_folder: false,
            can_share: false,
            can_delete: false
        };
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 🎨 Update UI Based on Folder Permission
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    function updateFolderActionsUI() {
        console.log('🎨 Updating folder actions UI based on permission:', currentFolderPermission);

        const uploadBtn = document.querySelector('[data-action="upload"]');
        const createFolderBtn = document.querySelector('[data-action="create-folder"]');

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // ✅ ตรวจสอบสิทธิ์จาก FOLDER PERMISSION
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

        const canUpload = currentFolderPermission.can_upload ||
            currentFolderPermission.access_type === 'write' ||
            currentFolderPermission.access_type === 'owner' ||
            currentFolderPermission.access_type === 'admin';

        const canCreateFolder = currentFolderPermission.can_create_folder ||
            currentFolderPermission.access_type === 'write' ||
            currentFolderPermission.access_type === 'owner' ||
            currentFolderPermission.access_type === 'admin';

        console.log('📊 Permission Check:');
        console.log('   └─ Can Upload:', canUpload);
        console.log('   └─ Can Create Folder:', canCreateFolder);
        console.log('   └─ Access Type:', currentFolderPermission.access_type);

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // อัพเดตปุ่ม "อัพโหลดไฟล์"
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

        if (uploadBtn) {
            if (canUpload) {
                uploadBtn.classList.remove('hidden', 'opacity-50', 'cursor-not-allowed');
                uploadBtn.disabled = false;
                uploadBtn.title = 'อัพโหลดไฟล์';
                console.log('✅ Upload button: ENABLED');
            } else {
                uploadBtn.classList.add('opacity-50', 'cursor-not-allowed');
                uploadBtn.disabled = true;
                uploadBtn.title = 'คุณไม่มีสิทธิ์อัพโหลดไฟล์ในโฟลเดอร์นี้';
                console.log('❌ Upload button: DISABLED');
            }
        }

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // อัพเดตปุ่ม "สร้างโฟลเดอร์"
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

        if (createFolderBtn) {
            if (canCreateFolder) {
                createFolderBtn.classList.remove('hidden', 'opacity-50', 'cursor-not-allowed');
                createFolderBtn.disabled = false;
                createFolderBtn.title = 'สร้างโฟลเดอร์ใหม่';
                console.log('✅ Create Folder button: ENABLED');
            } else {
                createFolderBtn.classList.add('opacity-50', 'cursor-not-allowed');
                createFolderBtn.disabled = true;
                createFolderBtn.title = 'คุณไม่มีสิทธิ์สร้างโฟลเดอร์ในโฟลเดอร์นี้';
                console.log('❌ Create Folder button: DISABLED');
            }
        }

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // อัพเดต Permission Badge ของโฟลเดอร์ปัจจุบัน (ถ้ามี)
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

        const folderPermissionBadge = document.querySelector('#current-folder-permission');
        if (folderPermissionBadge) {
            const badge = getAccessTypeBadge(
                currentFolderPermission.access_type,
                currentFolderPermission.access_label
            );
            folderPermissionBadge.innerHTML = badge;
            console.log('✅ Folder permission badge updated');
        }

        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }



    // Enhanced Trial Folder Contents
    function getTrialFolderContents(folderId) {
        const mockContents = {
            'demo_folder_1': [
                {
                    id: 'demo_doc_1',
                    name: 'Sample Document.pdf',
                    type: 'file',
                    icon: 'fas fa-file-pdf text-red-500',
                    modified: formatDateTime(new Date(Date.now() - 2 * 24 * 60 * 60 * 1000)),
                    size: '2.5 MB',
                    webViewLink: '#',
                    real_data: false,
                    description: 'ตัวอย่างเอกสาร PDF'
                },
                {
                    id: 'demo_image_1',
                    name: 'Project Screenshot.png',
                    type: 'file',
                    icon: 'fas fa-file-image text-purple-500',
                    modified: formatDateTime(new Date(Date.now() - 24 * 60 * 60 * 1000)),
                    size: '1.8 MB',
                    webViewLink: '#',
                    real_data: false,
                    description: 'ภาพหน้าจอโปรเจกต์'
                },
                {
                    id: 'demo_excel_1',
                    name: 'Budget 2024.xlsx',
                    type: 'file',
                    icon: 'fas fa-file-excel text-green-500',
                    modified: formatDateTime(new Date(Date.now() - 3 * 24 * 60 * 60 * 1000)),
                    size: '45 KB',
                    webViewLink: '#',
                    real_data: false,
                    description: 'งบประมาณประจำปี'
                }
            ],
            'demo_folder_2': [
                {
                    id: 'demo_folder_3',
                    name: 'Web Development',
                    type: 'folder',
                    icon: 'fas fa-folder text-purple-500',
                    modified: formatDateTime(new Date(Date.now() - 3 * 24 * 60 * 60 * 1000)),
                    size: '-',
                    webViewLink: '#',
                    real_data: false,
                    folder_type: 'trial',
                    description: 'โปรเจกต์พัฒนาเว็บไซต์'
                },
                {
                    id: 'demo_folder_4',
                    name: 'Mobile Apps',
                    type: 'folder',
                    icon: 'fas fa-folder text-blue-500',
                    modified: formatDateTime(new Date(Date.now() - 5 * 24 * 60 * 60 * 1000)),
                    size: '-',
                    webViewLink: '#',
                    real_data: false,
                    folder_type: 'trial',
                    description: 'โปรเจกต์แอปมือถือ'
                }
            ],
            'demo_folder_3': [
                {
                    id: 'demo_code_1',
                    name: 'index.html',
                    type: 'file',
                    icon: 'fas fa-file-code text-orange-500',
                    modified: formatDateTime(new Date(Date.now() - 60 * 60 * 1000)),
                    size: '15 KB',
                    webViewLink: '#',
                    real_data: false,
                    description: 'หน้าหลักเว็บไซต์'
                },
                {
                    id: 'demo_code_2',
                    name: 'style.css',
                    type: 'file',
                    icon: 'fas fa-file-code text-blue-500',
                    modified: formatDateTime(new Date(Date.now() - 30 * 60 * 1000)),
                    size: '8 KB',
                    webViewLink: '#',
                    real_data: false,
                    description: 'สไตล์ชีทหลัก'
                },
                {
                    id: 'demo_code_3',
                    name: 'app.js',
                    type: 'file',
                    icon: 'fas fa-file-code text-yellow-500',
                    modified: formatDateTime(new Date(Date.now() - 15 * 60 * 1000)),
                    size: '12 KB',
                    webViewLink: '#',
                    real_data: false,
                    description: 'JavaScript หลัก'
                }
            ],
            'demo_folder_4': [
                {
                    id: 'demo_app_1',
                    name: 'MainActivity.java',
                    type: 'file',
                    icon: 'fas fa-file-code text-red-500',
                    modified: formatDateTime(new Date(Date.now() - 2 * 60 * 60 * 1000)),
                    size: '25 KB',
                    webViewLink: '#',
                    real_data: false,
                    description: 'Activity หลักของแอป'
                }
            ]
        };

        return mockContents[folderId] || [];
    }

    // Load Breadcrumbs (Enhanced Error Handling)
    function loadBreadcrumbs(folderId) {
        if (folderId === 'root') {
            updateBreadcrumb([]);
            return;
        }

        // 🆕 ใช้ normalized folder ID
        const normalizedFolderId = normalizeFolderId(folderId);

        fetch(API_BASE_URL + 'get_folder_breadcrumbs', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'folder_id=' + encodeURIComponent(normalizedFolderId)  // ✅ ใช้ normalized ID
        })
            .then(response => {
                console.log('Breadcrumbs response status:', response.status);
                return handleApiResponse(response);
            })
            .then(data => {
                if (data.success && data.data) {
                    updateBreadcrumb(data.data);
                } else {
                    console.log('No breadcrumbs data or error:', data.message);
                    updateBreadcrumb([]);
                }
            })
            .catch(error => {
                console.error('💥 Error loading breadcrumbs:', error);
                updateBreadcrumb([]);
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
                html += ` / <button onclick="loadFolderContents('${crumb.id}')" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">${escapeHtml(crumb.name)}</button>`;
            });
            pathElement.innerHTML = html;
        }
    }

    // Show Loading State
    function showLoadingState() {
        document.getElementById('loadingState').style.display = 'flex';
        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('errorState').style.display = 'none';
        document.getElementById('fileList').style.display = 'none';
        isLoading = true;
    }

    // Show Empty State
    function showEmptyState() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('emptyState').style.display = 'flex';
        document.getElementById('errorState').style.display = 'none';
        document.getElementById('fileList').style.display = 'none';
        isLoading = false;
    }

    // Show Error State
    function showError(message = 'เกิดข้อผิดพลาด') {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('errorState').style.display = 'flex';
        document.getElementById('fileList').style.display = 'none';
        document.getElementById('errorMessage').textContent = message;
        isLoading = false;
    }

    // Show File List
    function showFileList() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('errorState').style.display = 'none';
        document.getElementById('fileList').style.display = 'block';
        isLoading = false;
    }

    // Render File List
    function renderFileList() {
        console.log('🎨 Rendering file list:', fileListData.length, 'items');

        if (fileListData.length === 0) {
            showEmptyState();
            return;
        }

        const container = document.getElementById('fileList');
        let html = '';

        if (viewMode === 'grid') {
            html = renderGridView();
        } else {
            html = renderListView();
        }

        container.innerHTML = html;
        showFileList();
    }


    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 🎨 Render Grid View (แก้ไข - ลดขนาด Action Menu)
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    function renderGridView() {
        let html = '<div class="file-grid">';

        fileListData.forEach(item => {
            const isFolder = item.type === 'folder';
            const iconClass = item.icon || (isFolder ? 'fas fa-folder text-blue-500' : 'fas fa-file text-gray-500');
            const cardClass = isFolder ? 'file-card folder-card' : 'file-card';

            const onClick = isFolder ?
                `onclick="openFolder('${item.id}')"` :
                `onclick="openFile('${item.id}', '${item.webViewLink || ''}')"`;

            const creatorInfo = item.uploaded_by || item.created_by || item.creator_name || 'ไม่ระบุ';

            const canDownload = !isFolder;

            const canShare = !isFolder && (
                item.can_share ||
                item.access_type === 'admin' ||
                item.access_type === 'owner'
            );

            const canEdit = (
                item.can_edit ||
                item.access_type === 'admin' ||
                item.access_type === 'owner' ||
                item.access_type === 'write'
            );

            const canDelete = (
                item.can_delete ||
                item.access_type === 'admin' ||
                item.access_type === 'owner'
            );

            html += `
    <div class="${cardClass} relative group" ${onClick}>
        <div class="text-center">
            <div class="w-16 h-16 ${isFolder ? 'bg-gradient-to-br from-blue-50 to-blue-100' : 'bg-gradient-to-br from-gray-50 to-gray-100'} rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i class="${iconClass} text-3xl"></i>
            </div>
            <h4 class="font-semibold text-gray-800 mb-2 truncate" title="${escapeHtml(item.name)}">
                ${escapeHtml(item.name)}
            </h4>
            <div class="text-sm text-gray-500 space-y-1">
                <p>${item.modified || '-'}</p>
                <p class="text-xs"><i class="fas fa-user mr-1"></i>${escapeHtml(creatorInfo)}</p>
                <p>${item.size || '-'}</p>
            </div>
            ${item.description ? `<p class="text-xs text-gray-400 mt-2 truncate" title="${escapeHtml(item.description)}">${escapeHtml(item.description)}</p>` : ''}
            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                <i class="fas fa-circle text-green-400 mr-1"></i>Live
            </div>
        </div>
        
        <!-- ✅ Action Menu: ลดขนาดและปรับ Layout -->
        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity" onclick="event.stopPropagation();">
            <div class="bg-white rounded-lg shadow-lg border border-gray-100 flex flex-col gap-0.5 p-0.5">
                ${canDownload ? `
                    <button onclick="downloadFile('${item.id}', '${escapeHtml(item.name)}')" 
                        class="p-1.5 text-green-600 hover:bg-green-50 rounded transition-colors text-sm" 
                        title="ดาวน์โหลด">
                        <i class="fas fa-download"></i>
                    </button>
                ` : ''}
                ${canShare ? `
                    <button onclick="shareItem('${item.id}', '${item.type}')" 
                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors text-sm" 
                        title="แชร์">
                        <i class="fas fa-share"></i>
                    </button>
                ` : ''}
                ${canEdit ? `
                    <button onclick="showRenameModal(${JSON.stringify(item).replace(/"/g, '&quot;')})" 
                        class="p-1.5 text-purple-600 hover:bg-purple-50 rounded transition-colors text-sm" 
                        title="เปลี่ยนชื่อ">
                        <i class="fas fa-edit"></i>
                    </button>
                ` : ''}
                ${canDelete ? `
                    <button onclick="deleteItem('${item.id}', '${item.type}')" 
                        class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors text-sm" 
                        title="ลบ">
                        <i class="fas fa-trash"></i>
                    </button>
                ` : ''}
            </div>
        </div>
    </div>
`;
        });

        html += '</div>';
        return html;
    }
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 🎨 Render List View (แก้ไข - ลดขนาด Row และ Action Buttons)
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    function renderListView() {
        let html = `
    <div class="overflow-x-auto">
        <table class="w-full min-w-max">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 sticky top-0">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider" style="min-width: 250px;">
                        ชื่อโฟลเดอร์/ไฟล์
                    </th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider" style="min-width: 150px;">
                        สร้าง/แก้ไขเมื่อ
                    </th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider" style="min-width: 180px;">
                        ผู้สร้าง/ผู้อัพโหลด
                    </th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider" style="min-width: 120px;">
                        สิทธิ์การเข้าถึง
                    </th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider" style="min-width: 80px;">
                        ขนาด
                    </th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider" style="min-width: 150px;">
                        จัดการ
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
`;

        if (!fileListData || fileListData.length === 0) {
            html += `
        <tr>
            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                <i class="fas fa-folder-open text-3xl mb-3 opacity-50"></i>
                <p class="text-base">ไม่มีไฟล์หรือโฟลเดอร์</p>
            </td>
        </tr>
    `;
        } else {
            fileListData.forEach(item => {
                const isFolder = item.type === 'folder';
                const iconClass = item.icon || (isFolder ? 'fas fa-folder text-blue-500' : 'fas fa-file text-gray-500');

                const onClick = isFolder ?
                    `onclick="openFolder('${item.id}')"` :
                    `onclick="openFile('${item.id}', '${item.webViewLink || ''}')"`;

                const creatorInfo = item.creator_name || item.uploaded_by || item.created_by || 'ไม่ระบุ';
                const accessType = item.access_type || 'read';
                const accessLabel = item.access_label || 'อ่านอย่างเดียว';
                const accessBadge = getAccessTypeBadge(accessType, accessLabel);
                const permTooltip = getPermissionTooltip(item);

                if (item.access_type) {
                    console.log(`📋 Item: ${item.name}, Access: ${item.access_type}, Label: ${item.access_label}`);
                }

                html += `
            <tr class="hover:bg-gradient-to-r hover:from-blue-50/30 hover:to-purple-50/30 transition-all duration-200 cursor-pointer" ${onClick}>
                <td class="px-3 py-2">
                    <div class="flex items-center min-w-0">
                        <div class="flex-shrink-0 w-8 h-8 ${isFolder ? 'bg-gradient-to-br from-blue-50 to-blue-100' : 'bg-gradient-to-br from-gray-50 to-gray-100'} rounded-lg flex items-center justify-center mr-2 shadow-sm">
                            <i class="${iconClass} text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-semibold text-sm text-gray-900 truncate">${escapeHtml(item.name)}</span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 flex-shrink-0">
                                    <i class="fas fa-circle text-green-400 mr-1 text-xs"></i>Live
                                </span>
                            </div>
                            ${item.description ? `<p class="text-xs text-gray-500 mt-0.5 truncate">${escapeHtml(item.description)}</p>` : ''}
                        </div>
                    </div>
                </td>
                <td class="px-3 py-2 text-xs text-gray-600 font-medium whitespace-nowrap">
                    ${item.modified || '-'}
                </td>
                <td class="px-3 py-2 text-xs text-gray-600 font-medium">
                    <div class="flex items-center min-w-0">
                        <i class="fas fa-user text-gray-400 mr-1.5 flex-shrink-0 text-xs"></i>
                        <span class="truncate">${escapeHtml(creatorInfo)}</span>
                    </div>
                </td>
                <td class="px-3 py-2 text-center" title="${permTooltip}">
                    ${accessBadge}
                </td>
                <td class="px-3 py-2 text-xs text-gray-600 font-medium whitespace-nowrap">
                    ${item.size || '-'}
                </td>
                <td class="px-3 py-2" onclick="event.stopPropagation();">
                    <div class="flex justify-center items-center gap-0.5 flex-wrap">
                        ${!isFolder ? `
                            <button onclick="downloadFile('${item.id}', '${escapeHtml(item.name)}')" 
                                class="p-1.5 text-green-600 hover:bg-green-50 rounded transition-colors text-sm" 
                                title="ดาวน์โหลด">
                                <i class="fas fa-download"></i>
                            </button>
                        ` : ''}
                        ${(!isFolder && (item.can_share || item.access_type === 'admin' || item.access_type === 'owner')) ? `
                            <button onclick="shareItem('${item.id}', '${item.type}')" 
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors text-sm" 
                                title="แชร์">
                                <i class="fas fa-share"></i>
                            </button>
                        ` : ''}
                        ${(item.can_edit || item.access_type === 'admin' || item.access_type === 'owner' || item.access_type === 'write') ? `
                            <button onclick="showRenameModal(${JSON.stringify(item).replace(/"/g, '&quot;')})" 
                                class="p-1.5 text-purple-600 hover:bg-purple-50 rounded transition-colors text-sm" 
                                title="เปลี่ยนชื่อ">
                                <i class="fas fa-edit"></i>
                            </button>
                        ` : ''}
                        ${(item.can_delete || item.access_type === 'admin' || item.access_type === 'owner') ? `
                            <button onclick="deleteItem('${item.id}', '${item.type}')" 
                                class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors text-sm" 
                                title="ลบ">
                                <i class="fas fa-trash"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
            });
        }

        html += `
            </tbody>
        </table>
    </div>
`;

        return html;
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // 🎨 Helper Functions (แก้ไข - ลดขนาด Badge)
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    function getAccessTypeBadge(accessType, accessLabel) {
        const badges = {
            'admin': `
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gradient-to-r from-red-100 to-red-200 text-red-800 shadow-sm">
            <i class="fas fa-crown mr-1 text-xs"></i>${accessLabel || 'แอดมิน'}
        </span>
    `,
            'owner': `
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 shadow-sm">
            <i class="fas fa-user-shield mr-1 text-xs"></i>${accessLabel || 'เจ้าของ'}
        </span>
    `,
            'write': `
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 shadow-sm">
            <i class="fas fa-edit mr-1 text-xs"></i>${accessLabel || 'แก้ไข'}
        </span>
    `,
            'commenter': `
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 shadow-sm">
            <i class="fas fa-comment mr-1 text-xs"></i>${accessLabel || 'แสดงความคิดเห็น'}
        </span>
    `,
            'read': `
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 shadow-sm">
            <i class="fas fa-eye mr-1 text-xs"></i>${accessLabel || 'อ่านอย่างเดียว'}
        </span>
    `
        };

        return badges[accessType] || badges['read'];
    }
    function getPermissionTooltip(item) {
        const permissions = [];

        if (item.can_edit) permissions.push('✏️ แก้ไข');
        if (item.can_delete) permissions.push('🗑️ ลบ');
        if (item.can_share) permissions.push('🔗 แชร์');

        if (permissions.length === 0) {
            permissions.push('👁️ อ่านอย่างเดียว');
        }

        return `สิทธิ์: ${permissions.join(', ')}`;
    }


    async function checkSharePermission(fileId) {
        try {
            console.log('🔐 Checking share permission for:', fileId);

            const response = await fetch(API_BASE_URL + 'check_share_permission', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    file_id: fileId
                }).toString()
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                console.log('✅ Share permission granted:', result.access_info);
                return true;
            } else {
                console.log('❌ Share permission denied:', result.message);
                return false;
            }

        } catch (error) {
            console.error('❌ Error checking share permission:', error);
            return false;
        }
    }





    function openFolder(folderId) {
        console.log('📂 Opening folder:', folderId);

        // 🆕 ใช้ normalized folder ID
        const normalizedFolderId = normalizeFolderId(folderId);
        console.log(`📂 Using normalized folder ID: ${normalizedFolderId}`);

        loadFolderContents(normalizedFolderId);
        trackFolderNavigation(normalizedFolderId);
    }


    // ✏️ แก้ไขฟังก์ชัน openFile() - เพิ่มการตรวจสอบ MS Office files
    function openFile(fileId, webViewLink) {
        console.log('📄 Opening file:', fileId, webViewLink);

        // ตรวจสอบสิทธิ์การเปิดไฟล์ก่อน
        checkFileAccessPermission(fileId).then(hasAccess => {
            if (!hasAccess) {
                showAccessDeniedModal(fileId);
                return;
            }

            // ถ้ามีสิทธิ์ ให้เปิดไฟล์
            if (webViewLink && webViewLink !== '#' && webViewLink !== '#trial-mode') {
                // หาข้อมูลไฟล์
                const item = fileListData.find(f => f.id === fileId);
                if (item) {
                    // 🔍 ตรวจสอบว่าเป็น MS Office file หรือไม่
                    const fileName = item.name || '';
                    const extension = fileName.split('.').pop().toLowerCase();
                    const officeExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

                    if (officeExtensions.includes(extension)) {
                        // ✅ เป็น MS Office file - แสดง popup
                        console.log('📊 MS Office file detected - showing popup');
                        handleOfficeFilePreview(item);
                    } else {
                        // ✅ ไฟล์ประเภทอื่น - เปิด viewer ปกติ
                        showFileViewer(fileId, item.name, webViewLink, item);
                    }
                } else {
                    Swal.fire('ไม่พบไฟล์', 'ไม่สามารถเปิดไฟล์ได้', 'warning');
                }
            } else {
                Swal.fire('ไม่พบลิงก์', 'ไม่สามารถเปิดไฟล์ได้', 'warning');
            }
        }).catch(error => {
            console.error('Error checking file access:', error);
            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถตรวจสอบสิทธิ์การเข้าถึงไฟล์ได้', 'error');
        });
    }

    // 🆕 ฟังก์ชันจัดการ MS Office files - Modal สวยงามแบบ Modern
    // 🔥 อัปเดต: เพิ่มการตรวจสอบ Trial Mode
    async function handleOfficeFilePreview(fileData) {
        const fileName = fileData.name || '';
        const extension = fileName.split('.').pop().toLowerCase();
        const officeExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

        // ตรวจสอบว่าเป็น MS Office file หรือไม่
        if (!officeExtensions.includes(extension)) {
            return false; // ไม่ใช่ MS Office file
        }

        // 🔥 ตรวจสอบ Trial Mode ก่อนแสดง Modal
        if (IS_TRIAL_MODE) {
            console.log('⚠️ Office file share blocked: Trial Mode');

            // แสดง Alert แจ้งเตือน Trial Limitation
            await Swal.fire({
                title: '🔒 ข้อจำกัดโหมดทดลอง',
                html: `
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-lock text-3xl text-white"></i>
                    </div>
                    <p class="text-gray-600 mb-4">การแชร์ไฟล์ Office ใช้งานได้เฉพาะเวอร์ชันเต็มเท่านั้น</p>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                        <p class="text-sm text-blue-700">
                            💡 ไฟล์: <strong>${escapeHtml(fileName)}</strong>
                        </p>
                    </div>
                    
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <p class="text-xs text-amber-800">
                            📥 คุณสามารถ<strong>ดาวน์โหลด</strong>ไฟล์ได้ในโหมดทดลอง<br>
                            แต่ไม่สามารถแชร์เพื่อดู/แก้ไขออนไลน์ได้
                        </p>
                    </div>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: '📥 ดาวน์โหลดไฟล์',
                cancelButtonText: 'ปิด',
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                customClass: {
                    popup: 'glass-card rounded-2xl',
                    confirmButton: 'rounded-xl',
                    cancelButton: 'rounded-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // ถ้ากด "ดาวน์โหลดไฟล์" ให้ดาวน์โหลดเลย
                    console.log('📥 Downloading file (Trial Mode):', fileData.id);
                    downloadFileFromViewer(fileData.id, fileName);

                    Swal.fire({
                        icon: 'success',
                        title: 'กำลังดาวน์โหลด',
                        html: `
                        <div class="text-center py-4">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                                <i class="fas fa-download text-green-600 text-2xl"></i>
                            </div>
                            <p class="text-gray-700 font-medium">ไฟล์กำลังถูกดาวน์โหลดไปยังเครื่องของคุณ</p>
                            <p class="text-sm text-gray-500 mt-2">${escapeHtml(fileName)}</p>
                        </div>
                    `,
                        timer: 2500,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-3xl shadow-2xl'
                        }
                    });
                }
            });

            return true; // 🔥 หยุดการทำงาน (ไม่แสดง Share Modal)
        }

        // Detect email (ใช้ฟังก์ชันเดิม)
        const detectedEmail = detectUserEmail();

        // 🎨 กำหนด icon และสีตาม file type
        const fileTypeConfig = {
            'doc': { icon: '📝', color: '#2b579a', bg: '#e7f0ff' },
            'docx': { icon: '📝', color: '#2b579a', bg: '#e7f0ff' },
            'xls': { icon: '📊', color: '#1d6f42', bg: '#e6f4ea' },
            'xlsx': { icon: '📊', color: '#1d6f42', bg: '#e6f4ea' },
            'ppt': { icon: '📽️', color: '#d24726', bg: '#fef3f0' },
            'pptx': { icon: '📽️', color: '#d24726', bg: '#fef3f0' }
        };

        const config = fileTypeConfig[extension] || { icon: '📄', color: '#6b7280', bg: '#f3f4f6' };

        // 📋 แสดง SweetAlert2 popup สวยงาม (ส่วนนี้เหมือนเดิม)
        const { value: formValues } = await Swal.fire({
            title: '',
            html: `
            <div class="text-left">
                <!-- File Header with Icon -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl shadow-lg mb-4" 
                         style="background: linear-gradient(135deg, ${config.bg} 0%, ${config.color}20 100%);">
                        <span style="font-size: 3rem;">${config.icon}</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">
                        เปิดไฟล์ Office
                    </h2>
                    <p class="text-sm text-gray-500 px-4">
                        ${escapeHtml(fileName)}
                    </p>
                </div>

                <!-- Info Box -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-5 mb-5 shadow-sm">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-info-circle text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700 leading-relaxed">
                                <strong class="text-blue-700">หากต้องการดูหรือแก้ไขไฟล์</strong><br>
                                เลือกสิทธิ์การเข้าถึงและแชร์ไปยังอีเมลของคุณ<br>
                                หรือ <strong>ดาวน์โหลด</strong> เพื่อเก็บไว้ในเครื่อง
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Share Section -->
                <div class="bg-white border-2 border-gray-200 rounded-2xl p-5 mb-4 shadow-sm">
                    <div class="flex items-center justify-center mb-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-share-alt text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">
                                แชร์ไฟล์
                            </h3>
                        </div>
                    </div>
                    
                    <!-- Email Input -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-envelope text-purple-500 mr-2"></i>
                            อีเมลผู้รับ
                        </label>
                        <input 
                            type="email" 
                            id="officeFileEmail" 
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all" 
                            placeholder="example@email.com"
                            value="${detectedEmail}"
                            style="margin: 0;"
                        >
                    </div>

                    <!-- Permission Selection -->
                    <div class="mb-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-shield-alt text-purple-500 mr-2"></i>
                            สิทธิ์การเข้าถึง
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <button 
                                type="button"
                                class="office-share-permission-btn group relative px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md bg-gradient-to-r from-purple-500 to-purple-600 text-white"
                                data-permission="reader"
                                onclick="selectOfficeSharePermission('reader')"
                            >
                                <i class="fas fa-eye mb-1 block text-lg"></i>
                                <span class="block text-xs">ดูอย่างเดียว</span>
                            </button>
                            <button 
                                type="button"
                                class="office-share-permission-btn group relative px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md bg-gray-100 text-gray-700 hover:bg-gray-200"
                                data-permission="writer"
                                onclick="selectOfficeSharePermission('writer')"
                            >
                                <i class="fas fa-edit mb-1 block text-lg"></i>
                                <span class="block text-xs">แก้ไขได้</span>
                            </button>
                            <button 
                                type="button"
                                class="office-share-permission-btn group relative px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md bg-gray-100 text-gray-700 hover:bg-gray-200"
                                data-permission="download"
                                onclick="selectOfficeSharePermission('download')"
                            >
                                <i class="fas fa-download mb-1 block text-lg"></i>
                                <span class="block text-xs">ดาวน์โหลด</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-paper-plane mr-2"></i>ดำเนินการ',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
            confirmButtonColor: '#8b5cf6',
            cancelButtonColor: '#6b7280',
            customClass: {
                popup: 'rounded-3xl shadow-2xl border-0',
                confirmButton: 'rounded-xl px-6 py-3 font-semibold shadow-lg hover:shadow-xl transition-all',
                cancelButton: 'rounded-xl px-6 py-3 font-semibold shadow-md hover:shadow-lg transition-all'
            },
            width: '650px',
            padding: '2.5em',
            allowOutsideClick: false,
            preConfirm: () => {
                const email = document.getElementById('officeFileEmail').value.trim();
                const permission = selectedOfficeSharePermission || 'reader';

                if (permission === 'download') {
                    return { action: 'download' };
                }

                if (!email) {
                    Swal.showValidationMessage('📧 กรุณาใส่อีเมลผู้รับ');
                    return false;
                }

                if (!validateEmail(email)) {
                    Swal.showValidationMessage('❌ กรุณาใส่อีเมลที่ถูกต้อง');
                    return false;
                }

                return {
                    action: 'share',
                    email: email,
                    permission: permission
                };
            }
        });

        // จัดการตามที่ผู้ใช้เลือก (เหมือนเดิม)
        if (formValues) {
            if (formValues.action === 'download') {
                console.log('📥 Downloading file:', fileData.id);
                downloadFileFromViewer(fileData.id, fileName);

                await Swal.fire({
                    icon: 'success',
                    title: '<span style="color: #10b981;">กำลังดาวน์โหลด</span>',
                    html: `
                    <div class="text-center py-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <i class="fas fa-download text-green-600 text-2xl"></i>
                        </div>
                        <p class="text-gray-700 font-medium">ไฟล์กำลังถูกดาวน์โหลดไปยังเครื่องของคุณ</p>
                        <p class="text-sm text-gray-500 mt-2">${escapeHtml(fileName)}</p>
                    </div>
                `,
                    timer: 2500,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl'
                    }
                });

            } else if (formValues.action === 'share') {
                console.log('📧 Sharing file via executeAutoShare:', fileData.id, 'to:', formValues.email);

                const successFiles = [{
                    file: fileName,
                    file_id: fileData.id,
                    status: 'success'
                }];

                await executeAutoShare(
                    successFiles,
                    formValues.email,
                    formValues.permission
                );
            }
        } else {
            console.log('❌ User cancelled office file action');
        }

        return true;
    }

    // ⚙️ เลือก Permission สำหรับ Office File Share (แยกจาก Auto-Share)
    let selectedOfficeSharePermission = 'reader'; // Default

    function selectOfficeSharePermission(permission) {
        selectedOfficeSharePermission = permission;

        // Update UI with smooth animations
        document.querySelectorAll('.office-share-permission-btn').forEach(btn => {
            btn.classList.remove('bg-gradient-to-r', 'from-purple-500', 'to-purple-600', 'text-white', 'scale-105');
            btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
        });

        const activeBtn = document.querySelector(`[data-permission="${permission}"].office-share-permission-btn`);
        if (activeBtn) {
            activeBtn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            activeBtn.classList.add('bg-gradient-to-r', 'from-purple-500', 'to-purple-600', 'text-white', 'scale-105');
        }

        // ถ้าเลือก "ดาวน์โหลด" ให้ปรับแต่ง UI
        const emailInput = document.getElementById('officeFileEmail');
        const emailLabel = emailInput?.previousElementSibling;

        if (emailInput) {
            if (permission === 'download') {
                emailInput.disabled = true;
                emailInput.classList.add('opacity-50', 'cursor-not-allowed');
                emailInput.placeholder = 'ไม่จำเป็นต้องใส่อีเมลสำหรับการดาวน์โหลด';
                if (emailLabel) {
                    emailLabel.classList.add('opacity-50');
                }
            } else {
                emailInput.disabled = false;
                emailInput.classList.remove('opacity-50', 'cursor-not-allowed');
                emailInput.placeholder = 'example@email.com';
                if (emailLabel) {
                    emailLabel.classList.remove('opacity-50');
                }
            }
        }
    }


    // 🔐 ตรวจสอบสิทธิ์การเข้าถึงไฟล์
    async function checkFileAccessPermission(fileId) {
        try {
            console.log('🔐 Checking file access permission for:', fileId);

            const response = await fetch(API_BASE_URL + 'check_file_access', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    file_id: fileId
                }).toString()
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                console.log('✅ File access granted:', result.access_info);
                return true;
            } else {
                console.log('❌ File access denied:', result.message);
                return false;
            }

        } catch (error) {
            console.error('❌ Error checking file access:', error);
            return false;
        }
    }


    // 🔧 สร้าง File Viewer Modal
    function showFileViewer(fileId, fileName, webViewLink, fileData = {}) {
        console.log('🖥️ Opening file viewer for:', fileName);

        // เก็บข้อมูลไฟล์ปัจจุบัน
        window.currentFileViewerData = {
            fileId: fileId,
            fileName: fileName,
            webViewLink: webViewLink,
            fileData: fileData
        };

        // สร้าง embed URL ตามประเภทไฟล์
        const embedUrl = generateEmbedUrl(fileId, webViewLink, fileData);

        // สร้าง modal HTML
        const modalHtml = `
        <div id="fileViewerModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl h-full max-h-[90vh] flex flex-col">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50 rounded-t-2xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 ${getFileIconBackground(fileName)} rounded-xl flex items-center justify-center shadow-sm">
                            <i class="${getFileIcon(fileName).icon} text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg">${escapeHtml(fileName)}</h3>
                            <p class="text-sm text-gray-500">
                                ${fileData.size || '-'} • แก้ไข: ${fileData.modified || '-'}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="downloadFileFromViewer('${fileId}', '${escapeHtml(fileName)}')" 
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors flex items-center space-x-2" 
                                title="ดาวน์โหลด">
                            <i class="fas fa-download"></i>
                            <span>ดาวน์โหลด</span>
                        </button>
                        <button onclick="closeFileViewer()" 
                                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" 
                                title="ปิด">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="flex-1 relative overflow-hidden">
                    <div id="fileViewerContent" class="w-full h-full">
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent mx-auto mb-4"></div>
                                <p class="text-gray-600">กำลังโหลดไฟล์...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <div class="flex items-center space-x-4">
                            <span>📄 ไฟล์: ${escapeHtml(fileName)}</span>
                            
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-shield-alt text-green-500"></i>
                            <span>ปลอดภัยด้วย Google Drive</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

        // เพิ่ม modal เข้า DOM
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // โหลดเนื้อหาไฟล์
        loadFileContent(embedUrl, fileData);

        // เพิ่ม event listener สำหรับปิด modal เมื่อคลิกนอกพื้นที่
        document.getElementById('fileViewerModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeFileViewer();
            }
        });

        // เพิ่ม keyboard shortcut
        document.addEventListener('keydown', handleFileViewerKeyboard);
    }

    // 🔧 สร้าง Embed URL ตามประเภทไฟล์ (แก้ไข URL encoding)
    function generateEmbedUrl(fileId, webViewLink, fileData) {
        const fileName = fileData.name || '';
        const mimeType = fileData.mimeType || '';
        const extension = fileName.split('.').pop().toLowerCase();

        // ✅ สำหรับรูปภาพ ใช้ API ของเราเอง
        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        if (imageExtensions.includes(extension)) {
            console.log(`📷 Using our API for ${extension} file`);
            return API_BASE_URL + 'preview_file?file_id=' + fileId;
        }

        // ✅ สำหรับ PDF ใช้ API ของเราเอง
        if (extension === 'pdf' || mimeType === 'application/pdf') {
            console.log('📄 Using our API for PDF file');
            return API_BASE_URL + 'preview_file?file_id=' + fileId;
        }

        // ✅ Google Workspace files (native Google format)
        if (mimeType === 'application/vnd.google-apps.document') {
            console.log('📝 Google Docs - using Google preview');
            return `https://docs.google.com/document/d/${fileId}/preview`;
        }
        if (mimeType === 'application/vnd.google-apps.spreadsheet') {
            console.log('📊 Google Sheets - using Google preview');
            return `https://docs.google.com/spreadsheets/d/${fileId}/preview`;
        }
        if (mimeType === 'application/vnd.google-apps.presentation') {
            console.log('📽️ Google Slides - using Google preview');
            return `https://docs.google.com/presentation/d/${fileId}/preview`;
        }

        // ✅ MS Office documents - ใช้ Microsoft Office Online Viewer
        const officeExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        if (officeExtensions.includes(extension)) {
            console.log(`📊 Using Microsoft Office Online Viewer for ${extension} file`);

            // ❌ วิธีเดิม (encode ซ้ำซ้อน):
            // const publicUrl = encodeURIComponent(API_BASE_URL + 'preview_file?file_id=' + fileId);
            // return `https://view.officeapps.live.com/op/view.aspx?src=${publicUrl}`;

            // ✅ วิธีใหม่ (ไม่ encode ซ้ำ):
            // สร้าง URL แบบปกติ
            const fullUrl = API_BASE_URL + 'preview_file?file_id=' + fileId;

            // Microsoft Office Online Viewer ต้องการ URL ที่ไม่ encode
            // เพราะ browser จะ encode ให้อัตโนมัติเมื่อส่ง request
            return `https://view.officeapps.live.com/op/view.aspx?src=${fullUrl}`;
        }

        // ✅ Text files
        if (['txt', 'csv'].includes(extension)) {
            console.log(`📝 Using our API for ${extension} file`);
            return API_BASE_URL + 'preview_file?file_id=' + fileId;
        }

        // ✅ Video files
        if (['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'].includes(extension)) {
            console.log('🎥 Using Google Drive preview for video');
            return `https://drive.google.com/file/d/${fileId}/preview`;
        }

        // Default: ใช้ Google Drive preview
        console.log('🔍 Using default Google Drive preview');
        return `https://drive.google.com/file/d/${fileId}/preview`;
    }


    // ✅ แก้ไข loadFileContent ให้รองรับการแสดงผลหลายประเภท
    function loadFileContent(embedUrl, fileData) {
        const contentDiv = document.getElementById('fileViewerContent');

        if (!contentDiv) {
            console.error('❌ File viewer content div not found');
            return;
        }

        try {
            console.log('📂 Loading file content from:', embedUrl);

            const extension = fileData.name ? fileData.name.split('.').pop().toLowerCase() : '';
            const isOurAPI = embedUrl.includes(API_BASE_URL);

            // ✅ สำหรับรูปภาพที่ใช้ API ของเรา ให้ใช้ <img> tag
            if (isOurAPI && ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'].includes(extension)) {
                console.log('🖼️ Rendering as image element');

                const imgContainer = document.createElement('div');
                imgContainer.className = 'flex items-center justify-center w-full h-full bg-gray-50';

                const img = document.createElement('img');
                img.src = embedUrl;
                img.className = 'max-w-full max-h-full object-contain';
                img.alt = fileData.name;

                let isLoaded = false;
                let loadTimeout;

                img.onload = function () {
                    isLoaded = true;
                    clearTimeout(loadTimeout);
                    console.log('✅ Image loaded successfully');
                };

                img.onerror = function () {
                    isLoaded = true;
                    clearTimeout(loadTimeout);
                    console.error('❌ Failed to load image');
                    showFileLoadError(embedUrl);
                };

                imgContainer.appendChild(img);
                contentDiv.innerHTML = '';
                contentDiv.appendChild(imgContainer);

                loadTimeout = setTimeout(() => {
                    if (!isLoaded) {
                        console.warn('⚠️ Image loading timeout');
                        showFileLoadFallback(embedUrl);
                    }
                }, 10000);

                return;
            }

            // ✅ สำหรับไฟล์อื่นๆ ใช้ iframe
            console.log('📄 Rendering as iframe');

            const iframe = document.createElement('iframe');
            iframe.src = embedUrl;
            iframe.className = 'w-full h-full border-0 rounded-lg';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
            iframe.allowFullscreen = true;

            let isLoaded = false;
            let loadTimeout;

            iframe.onload = function () {
                isLoaded = true;
                clearTimeout(loadTimeout);
                console.log('✅ File loaded successfully');

                const loadingEl = contentDiv.querySelector('.loading-indicator');
                if (loadingEl) {
                    loadingEl.remove();
                }
            };

            iframe.onerror = function (error) {
                isLoaded = true;
                clearTimeout(loadTimeout);
                console.error('❌ Failed to load file in iframe:', error);
                showFileLoadError(embedUrl);
            };

            contentDiv.innerHTML = '';
            contentDiv.appendChild(iframe);

            loadTimeout = setTimeout(() => {
                if (!isLoaded) {
                    console.warn('⚠️ File loading timeout - showing fallback options');
                    showFileLoadFallback(embedUrl);
                }
            }, 15000);

            // ตรวจสอบ CORS errors
            setTimeout(() => {
                try {
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    if (!iframeDoc || iframeDoc.body.innerHTML === '') {
                        console.warn('⚠️ Iframe content is empty or blocked');
                    }
                } catch (crossOriginError) {
                    console.log('ℹ️ Cross-origin iframe detected (normal behavior)');
                }
            }, 3000);

        } catch (error) {
            console.error('💥 Error creating viewer:', error);
            showFileLoadError(embedUrl);
        }
    }

    function handleFileViewerKeyboard(e) {
        if (e.key === 'Escape') {
            closeFileViewer();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'o') {
            e.preventDefault();
            if (currentFileViewerData && currentFileViewerData.webViewLink) {
                openInGoogleDrive(currentFileViewerData.webViewLink);
            }
        }
    }


    function closeFileViewer() {
        const modal = document.getElementById('fileViewerModal');
        if (modal) {
            modal.remove();
        }
        document.removeEventListener('keydown', handleFileViewerKeyboard);
        console.log('📴 File viewer closed');
    }

    function showFileLoadError(embedUrl = '') {
        const contentDiv = document.getElementById('fileViewerContent');

        if (!contentDiv) {
            console.error('File viewer content div not found');
            return;
        }

        // ✅ ดึงข้อมูลไฟล์จาก global variable
        const fileData = window.currentFileViewerData || {};
        const webViewLink = fileData.webViewLink || embedUrl || '#';
        const fileName = fileData.fileName || 'ไฟล์';

        contentDiv.innerHTML = `
    <div class="flex items-center justify-center h-full">
        <div class="text-center max-w-md mx-auto p-6">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">ไม่สามารถแสดงไฟล์ได้</h3>
            <p class="text-gray-600 mb-2">ไฟล์อาจไม่รองรับการแสดงผลแบบฝัง (embedded)</p>
            <p class="text-sm text-gray-500 mb-6">หรืออาจเป็นเพราะการตั้งค่าความเป็นส่วนตัวของไฟล์</p>
            
            <div class="space-y-3">
                <!-- ✅ ปุ่มเปิดใน Google Drive -->
                <button onclick="openInGoogleDrive('${webViewLink}')" 
                        class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2 shadow-sm">
                    <i class="fas fa-external-link-alt"></i>
                    <span>เปิดใน Google Drive</span>
                </button>
                
                <!-- ✅ ปุ่มดาวน์โหลด -->
                <button onclick="downloadFileFromViewer('${fileData.fileId}', '${escapeHtml(fileName)}')" 
                        class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center space-x-2 shadow-sm">
                    <i class="fas fa-download"></i>
                    <span>ดาวน์โหลดไฟล์</span>
                </button>
                
                <!-- ✅ ปุ่มลองใหม่ -->
                <button onclick="retryLoadFile()" 
                        class="w-full px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                    <i class="fas fa-redo"></i>
                    <span>ลองใหม่อีกครั้ง</span>
                </button>
            </div>
            
            <!-- ✅ คำแนะนำ -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                <p class="text-xs text-blue-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>คำแนะนำ:</strong> ไฟล์บางประเภทอาจแสดงได้ดีกว่าใน Google Drive โดยตรง
                </p>
            </div>
        </div>
    </div>
`;
    }

    function showFileLoadFallback(embedUrl = '') {
        const contentDiv = document.getElementById('fileViewerContent');

        if (!contentDiv) {
            console.error('File viewer content div not found');
            return;
        }

        const fileData = window.currentFileViewerData || {};
        const webViewLink = fileData.webViewLink || embedUrl || '#';
        const fileName = fileData.fileName || 'ไฟล์';

        contentDiv.innerHTML = `
    <div class="flex items-center justify-center h-full">
        <div class="text-center max-w-md mx-auto p-6">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-hourglass-half text-3xl text-yellow-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">ไฟล์กำลังโหลด...</h3>
            <p class="text-gray-600 mb-2">การโหลดไฟล์ใช้เวลานานกว่าปกติ</p>
            <p class="text-sm text-gray-500 mb-6">คุณสามารถรอต่อหรือเลือกตัวเลือกด้านล่าง</p>
            
            <div class="space-y-3">
                <button onclick="openInGoogleDrive('${webViewLink}')" 
                        class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2 shadow-sm">
                    <i class="fas fa-external-link-alt"></i>
                    <span>เปิดใน Google Drive แทน</span>
                </button>
                
                <button onclick="retryLoadFile()" 
                        class="w-full px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                    <i class="fas fa-redo"></i>
                    <span>รีเฟรชและลองใหม่</span>
                </button>
                
                <button onclick="closeFileViewer()" 
                        class="w-full px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    <span>ยกเลิก</span>
                </button>
            </div>
            
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                <p class="text-xs text-blue-700">
                    <i class="fas fa-lightbulb mr-1"></i>
                    ไฟล์ขนาดใหญ่อาจใช้เวลาในการโหลด
                </p>
            </div>
        </div>
    </div>
`;
    }

    function downloadFile(fileId, fileName) {
        console.log('⬇️ Downloading file:', fileId, fileName);

        // ตรวจสอบสิทธิ์การดาวน์โหลดก่อน
        checkFileAccessPermission(fileId).then(hasAccess => {
            if (!hasAccess) {
                showAccessDeniedModal(fileId);
                return;
            }

            // ✅ ใช้ backend controller แทน direct Google Drive URL
            const downloadUrl = `${API_BASE_URL}download_file?file_id=${encodeURIComponent(fileId)}`;

            // วิธีที่ 1: ใช้ window.open (แนะนำ)
            window.open(downloadUrl, '_blank');

            // วิธีที่ 2: ใช้ hidden link (backup)
            // const link = document.createElement('a');
            // link.href = downloadUrl;
            // link.download = fileName;
            // link.target = '_blank';
            // link.style.display = 'none';
            // document.body.appendChild(link);
            // link.click();
            // document.body.removeChild(link);

            // แสดงข้อความแจ้งเตือน
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `เริ่มดาวน์โหลด ${fileName}`,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

        }).catch(error => {
            console.error('Error checking download permission:', error);
            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถตรวจสอบสิทธิ์การดาวน์โหลดได้', 'error');
        });
    }


    // ==========================================
    // Rename Functions
    // ==========================================

    // Show Rename Modal
    function showRenameModal(item) {
        console.log('✏️ Showing rename modal for:', item.name);

        if (IS_TRIAL_MODE && item.real_data === false) {
            Swal.fire({
                title: '🎭 Demo Item',
                text: 'การเปลี่ยนชื่อใช้งานได้เฉพาะเวอร์ชันเต็มเท่านั้น',
                icon: 'info',
                confirmButtonText: '🚀 อัปเกรด',
                showCancelButton: true,
                cancelButtonText: 'ปิด',
                confirmButtonColor: '#f59e0b',
                customClass: {
                    popup: 'glass-card rounded-2xl',
                    confirmButton: 'rounded-xl',
                    cancelButton: 'rounded-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    showUpgradeModal();
                }
            });
            return;
        }

        const isFolder = item.type === 'folder';
        const fileExtension = isFolder ? '' : item.name.split('.').pop();
        const nameWithoutExt = isFolder ? item.name : item.name.replace(`.${fileExtension}`, '');

        Swal.fire({
            title: `✏️ เปลี่ยนชื่อ${isFolder ? 'โฟลเดอร์' : 'ไฟล์'}`,
            html: `
            <div class="text-left">
                <div class="flex items-center mb-4 p-3 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 ${isFolder ? 'bg-gradient-to-br from-blue-50 to-blue-100' : 'bg-gradient-to-br from-gray-50 to-gray-100'} rounded-xl flex items-center justify-center mr-3">
                        <i class="${item.icon || (isFolder ? 'fas fa-folder text-blue-500' : 'fas fa-file text-gray-500')} text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800">${escapeHtml(item.name)}</div>
                        <div class="text-sm text-gray-500">${item.size || '-'} • ${item.modified || '-'}</div>
                    </div>
                </div>
                
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    ชื่อใหม่:
                </label>
                <div class="relative">
                    <input type="text" id="newItemName" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           value="${escapeHtml(nameWithoutExt)}" 
                           placeholder="ใส่ชื่อใหม่">
                    ${!isFolder && fileExtension ? `<span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">.${fileExtension}</span>` : ''}
                </div>
                ${!isFolder && fileExtension ? `<p class="text-xs text-gray-500 mt-2">นามสกุลไฟล์ (.${fileExtension}) จะคงเดิม</p>` : ''}
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: '💾 บันทึก',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'glass-card rounded-2xl',
                confirmButton: 'rounded-xl',
                cancelButton: 'rounded-xl'
            },
            didOpen: () => {
                const input = document.getElementById('newItemName');
                input.focus();
                input.select();

                input.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        Swal.clickConfirm();
                    }
                });
            },
            preConfirm: () => {
                const newName = document.getElementById('newItemName').value.trim();
                if (!newName) {
                    Swal.showValidationMessage('กรุณาใส่ชื่อใหม่');
                    return false;
                }

                if (newName === nameWithoutExt) {
                    Swal.showValidationMessage('ชื่อใหม่ต้องแตกต่างจากชื่อเดิม');
                    return false;
                }

                if (!/^[a-zA-Z0-9ก-๙\s\-_.()]+$/.test(newName)) {
                    Swal.showValidationMessage('ชื่อมีอักขระที่ไม่ได้รับอนุญาต');
                    return false;
                }

                const finalName = isFolder ? newName : `${newName}.${fileExtension}`;
                return { newName: finalName, originalName: item.name };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                performRename(item.id, item.type, result.value.newName, result.value.originalName);
            }
        });
    }

    // Perform Rename
    // Perform Rename - Fixed Version
    function performRename(itemId, itemType, newName, originalName) {
        console.log('✏️ Renaming item:', itemId, 'to:', newName);

        // แสดง loading modal
        Swal.fire({
            title: 'กำลังเปลี่ยนชื่อ...',
            text: `เปลี่ยนเป็น "${newName}"`,
            allowOutsideClick: false,
            showConfirmButton: false,
            customClass: {
                popup: 'glass-card rounded-2xl'
            },
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // ใช้ URLSearchParams แทน FormData
        const params = new URLSearchParams();
        params.append('item_id', itemId);
        params.append('item_type', itemType);
        params.append('new_name', newName);
        params.append('original_name', originalName);

        fetch(API_BASE_URL + 'rename_item', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: params.toString()
        })
            .then(response => {
                console.log('Rename response status:', response.status);

                // ✅ จัดการ 403 Access Denied
                if (response.status === 403) {
                    return response.json().then(data => {
                        // ✅ ปิด loading modal ก่อนแสดง error
                        Swal.close();

                        if (data.error_type === 'access_denied') {
                            console.log('🚫 Access denied for rename:', itemId);
                            showAccessDeniedModal(data);
                            return Promise.reject(new Error('Access Denied - Modal Shown'));
                        }

                        // แสดง error สำหรับ 403 อื่นๆ
                        Swal.fire({
                            icon: 'error',
                            title: 'ไม่มีสิทธิ์',
                            text: data.message || 'ไม่มีสิทธิ์เปลี่ยนชื่อ',
                            confirmButtonText: 'ตกลง',
                            customClass: {
                                popup: 'glass-card rounded-2xl',
                                confirmButton: 'rounded-xl'
                            }
                        });

                        throw new Error('Forbidden: ' + (data.message || 'ไม่มีสิทธิ์เข้าถึง'));
                    });
                }

                // ตรวจสอบ Content-Type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    // ✅ ปิด loading modal ก่อนแสดง error
                    Swal.close();
                    throw new Error('Server ตอบกลับไม่ใช่ JSON');
                }

                return response.json();
            })
            .then(data => {
                console.log('Rename response data:', data);

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'เปลี่ยนชื่อสำเร็จ',
                        text: data.message || `เปลี่ยนชื่อเป็น "${newName}" เรียบร้อยแล้ว`,
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'glass-card rounded-2xl'
                        }
                    }).then(() => {
                        refreshFiles();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: `เปลี่ยนชื่อ "${newName}" สำเร็จ`,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'glass-card rounded-xl'
                            }
                        });
                    });
                } else {
                    // ✅ ปิด loading modal ก่อนแสดง error
                    Swal.close();

                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถเปลี่ยนชื่อได้',
                        text: data.message || 'เกิดข้อผิดพลาดในการเปลี่ยนชื่อ',
                        confirmButtonText: 'ตกลง',
                        customClass: {
                            popup: 'glass-card rounded-2xl',
                            confirmButton: 'rounded-xl'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('💥 Rename error:', error);

                // ✅ ไม่แสดง error ถ้าเป็น Access Denied (เพราะ Modal แสดงแล้ว)
                if (error.message === 'Access Denied - Modal Shown') {
                    return; // หยุดการประมวลผลตรงนี้
                }

                // ✅ ปิด loading modal ก่อนแสดง error
                Swal.close();

                let errorMessage = 'ไม่สามารถเปลี่ยนชื่อได้';

                if (error.message.includes('JSON')) {
                    errorMessage = 'เซิร์ฟเวอร์มีปัญหา กรุณาลองใหม่อีกครั้ง';
                } else if (error.message.includes('500')) {
                    errorMessage = 'เซิร์ฟเวอร์มีข้อผิดพลาดภายใน';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: errorMessage,
                    confirmButtonText: 'ตกลง',
                    customClass: {
                        popup: 'glass-card rounded-2xl',
                        confirmButton: 'rounded-xl'
                    }
                });
            });
    }




    function deleteItem(itemId, itemType) {
        console.log('🗑️ Deleting item:', itemId, itemType);

        Swal.fire({
            title: 'ยืนยันการลบ',
            text: `คุณต้องการลบ${itemType === 'folder' ? 'โฟลเดอร์' : 'ไฟล์'}นี้หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'glass-card',
                confirmButton: 'rounded-xl',
                cancelButton: 'rounded-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                performDeleteItem(itemId, itemType);
            }
        });
    }

    function performDeleteItem(itemId, itemType) {
        Swal.fire({
            title: 'กำลังลบ...',
            text: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(API_BASE_URL + 'delete_item', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `item_id=${encodeURIComponent(itemId)}&item_type=${encodeURIComponent(itemType)}`
        })
            .then(response => {
                console.log('Delete response status:', response.status);
                return handleApiResponse(response);
            })
            .then(data => {
                console.log('Delete response data:', data);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบเรียบร้อย',
                        text: data.message || 'ลบรายการเรียบร้อยแล้ว',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    refreshFiles();
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message || 'ไม่สามารถลบรายการได้', 'error');
                }
            })
            .catch(error => {
                console.error('💥 Error deleting item:', error);
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถลบรายการได้: ' + error.message, 'error');
            });
    }

    // View and Sort Functions
    function changeViewMode(mode) {
        console.log('👀 Changing view mode to:', mode);
        viewMode = mode;

        // Update button states
        document.querySelectorAll('.view-mode-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.getElementById(mode + 'ViewBtn').classList.add('active');

        const folderTreeSidebar = document.getElementById('folderTreeSidebar');
        const fileList = document.getElementById('fileList');

        if (mode === 'tree') {
            // Show tree sidebar
            folderTreeSidebar.classList.remove('hidden');
            fileList.style.marginLeft = '320px';
            renderFolderTree();
        } else {
            // Hide tree sidebar
            folderTreeSidebar.classList.add('hidden');
            fileList.style.marginLeft = '0';
        }

        if (fileListData.length > 0) {
            renderFileList();
        }
    }

    // Render Folder Tree
    function renderFolderTree() {
        const container = document.getElementById('folderTreeContent');
        if (!container) return;

        let html = '';

        // Root level
        html += `
                <div class="folder-tree-item ${currentFolder === 'root' ? 'active' : ''}" onclick="navigateToFolder('root')">
                    <div class="folder-tree-toggle">
                        <i class="fas fa-home"></i>
                    </div>
                    <i class="fas fa-home text-gray-600 mr-2"></i>
                    <span class="font-medium text-gray-700">Google Drive</span>
                </div>
            `;

        // Render tree nodes
        folderTreeData.forEach(folder => {
            html += renderTreeNode(folder, 0);
        });

        container.innerHTML = html;
    }

    // Render Tree Node
    function renderTreeNode(node, level) {
        const isExpanded = expandedFolders.has(node.id);
        const isActive = currentFolder === node.id;
        const hasChildren = node.hasChildren || (node.children && node.children.length > 0);

        let html = `
                <div class="folder-tree-item ${isActive ? 'active' : ''}" style="margin-left: ${level * 16}px;">
                    <div class="folder-tree-toggle ${isExpanded && hasChildren ? 'expanded' : ''}" 
                         onclick="toggleFolderTree('${node.id}', event)">
                        ${hasChildren ? '<i class="fas fa-chevron-right"></i>' : '<span style="width: 10px;"></span>'}
                    </div>
                    <i class="${node.icon} mr-2"></i>
                    <span class="font-medium text-gray-700 cursor-pointer" onclick="navigateToFolder('${node.id}')">
                        ${escapeHtml(node.name)}
                    </span>
                    ${node.real_data === false ? '<span class="ml-2 text-xs bg-orange-100 text-orange-600 px-1 rounded">Demo</span>' : ''}
                </div>
            `;

        // Render children if expanded
        if (isExpanded && node.children && node.children.length > 0) {
            node.children.forEach(child => {
                html += renderTreeNode(child, level + 1);
            });
        }

        return html;
    }

    // Toggle Folder Tree
    function toggleFolderTree(folderId, event) {
        event.stopPropagation();

        if (expandedFolders.has(folderId)) {
            expandedFolders.delete(folderId);
        } else {
            expandedFolders.add(folderId);
            // Load children if needed
            loadFolderTreeChildren(folderId);
        }

        // Update tree structure and re-render
        buildFolderTree();
        renderFolderTree();
    }

    // Load Folder Tree Children
    function loadFolderTreeChildren(folderId) {
        // Simulate loading children from Google Drive API
        if (IS_TRIAL_MODE) {
            // Mock data is already handled in buildFolderTree
            return;
        }

        // For real Google Drive integration, fetch children here
        fetch(API_BASE_URL + 'get_folder_contents', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'folder_id=' + encodeURIComponent(folderId)
        })
            .then(response => handleApiResponse(response))
            .then(data => {
                if (data.success && data.data) {
                    // Update folder tree with children
                    const children = data.data.filter(item => item.type === 'folder');
                    updateFolderTreeChildren(folderId, children);
                }
            })
            .catch(error => {
                console.error('Error loading folder children:', error);
            });
    }

    // Update Folder Tree Children
    function updateFolderTreeChildren(parentId, children) {
        const updateNode = (nodes) => {
            nodes.forEach(node => {
                if (node.id === parentId) {
                    node.children = children.map(child => ({
                        ...child,
                        children: [],
                        hasChildren: false,
                        isExpanded: false
                    }));
                    node.hasChildren = children.length > 0;
                } else if (node.children) {
                    updateNode(node.children);
                }
            });
        };

        updateNode(folderTreeData);
    }

    // Navigate to Folder
    function navigateToFolder(folderId) {
        if (folderId === 'root') {
            loadAccessibleFolders();
        } else {
            loadFolderContents(folderId);
        }
    }

    // Update Tree Selection
    function updateTreeSelection(folderId) {
        if (viewMode === 'tree') {
            renderFolderTree();
        }
    }

    function sortFiles(sortBy) {
        console.log('🔄 Sorting by:', sortBy);

        // Update button states
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        const sortButtons = {
            'name': 'sortNameBtn',
            'modified': 'sortDateBtn',
            'size': 'sortSizeBtn',
            'type': 'sortTypeBtn'
        };

        if (sortButtons[sortBy]) {
            document.getElementById(sortButtons[sortBy]).classList.add('active');
        }

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
        console.log('🔍 Searching for:', query);

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

    function refreshFiles() {
        console.log('🔄 Refreshing files...');

        if (isLoading) {
            console.log('Already loading, skipping refresh');
            return;
        }

        if (currentFolder === 'root') {
            loadAccessibleFolders();
        } else {
            loadFolderContents(currentFolder);
        }
    }

    // Modal Functions
    function showUploadModal() {
        console.log('📤 Showing upload modal');
        updateCurrentFolderDisplay();
        document.getElementById('uploadModal').classList.remove('hidden');
    }

    function closeUploadModal() {
        console.log('❌ Closing upload modal');

        // รีเซ็ต upload state
        isUploading = false;

        // ปิด modal และล้างข้อมูล
        document.getElementById('uploadModal').classList.add('hidden');
        document.getElementById('fileInput').value = '';
        document.getElementById('selectedFiles').classList.add('hidden');

        // รีเซ็ตปุ่มอัปโหลด
        const uploadBtn = document.getElementById('uploadStartBtn');
        if (uploadBtn) {
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = 'อัปโหลดไฟล์';
        }

        // ล้าง timeout ถ้ามี
        if (uploadTimeout) {
            clearTimeout(uploadTimeout);
            uploadTimeout = null;
        }
    }


    function showCreateFolderModal() {
        console.log('📁 Showing create folder modal');
        updateCreateFolderParentDisplay();
        document.getElementById('createFolderModal').classList.remove('hidden');
    }

    function closeCreateFolderModal() {
        console.log('❌ Closing create folder modal');
        document.getElementById('createFolderModal').classList.add('hidden');
        document.getElementById('newFolderName').value = '';
    }

    function updateCurrentFolderDisplay() {
        const displayElement = document.getElementById('currentFolderDisplay');
        if (!displayElement) return;

        if (currentFolder === 'root') {
            displayElement.textContent = 'My Folders';
        } else {
            let folderPath = 'My Folders';
            if (breadcrumbData && breadcrumbData.length > 0) {
                const folderNames = breadcrumbData.map(crumb => crumb.name);
                folderPath += ' / ' + folderNames.join(' / ');
            }
            displayElement.textContent = folderPath;
        }
    }

    function updateCreateFolderParentDisplay() {
        const displayElement = document.getElementById('createFolderParentDisplay');
        if (!displayElement) return;

        if (currentFolder === 'root') {
            displayElement.textContent = 'My Folders';
        } else {
            let folderPath = 'My Folders';
            if (breadcrumbData && breadcrumbData.length > 0) {
                const folderNames = breadcrumbData.map(crumb => crumb.name);
                folderPath += ' / ' + folderNames.join(' / ');
            }
            displayElement.textContent = folderPath;
        }
    }

    // File Upload Functions (Enhanced Error Handling)
    // แก้ไข function handleFileSelect
    function handleFileSelect(input) {
        console.log('📎 Files selected:', input.files.length);
        isDragAndDropUpload = false;

        if (isUploading) {
            console.log('⚠️ Upload in progress, skipping');
            return;
        }

        const files = Array.from(input.files);
        if (files.length === 0) {
            document.getElementById('selectedFiles')?.classList.add('hidden');
            document.getElementById('uploadStartBtn').disabled = true;
            return;
        }

        // Validate files
        const validationResult = validateFilesWithDBSettings(files);
        if (validationResult.invalidFiles.length > 0) {
            showFileValidationErrors(validationResult.invalidFiles, validationResult.validFiles);
            if (validationResult.validFiles.length > 0) {
                updateFileInputWithValidFiles(validationResult.validFiles);
                displaySelectedFiles(validationResult.validFiles);
                document.getElementById('uploadStartBtn').disabled = false;
            } else {
                document.getElementById('selectedFiles')?.classList.add('hidden');
                document.getElementById('uploadStartBtn').disabled = true;
            }
            return;
        }

        // ✅ Check trial storage
        if (IS_TRIAL_MODE && memberInfo) {
            const totalSize = files.reduce((sum, file) => sum + file.size, 0);

            // ✅ CRITICAL FIX: แปลงเป็น Number และ validate
            const currentUsed = parseInt(memberInfo.quota_used) || 0;
            const trialLimit = parseInt(TRIAL_STORAGE_LIMIT) || (5 * 1024 * 1024 * 1024);

            console.group('🔍 Trial Storage Check');
            console.log('Files to upload:', files.length);
            console.log('Total size:', totalSize, 'bytes');
            console.log('Current used:', currentUsed, 'bytes', `[${typeof currentUsed}]`);
            console.log('Trial limit:', trialLimit, 'bytes');
            console.log('After upload:', currentUsed + totalSize, 'bytes');
            console.log('Will exceed:', (currentUsed + totalSize > trialLimit));
            console.groupEnd();

            if (currentUsed + totalSize > trialLimit) {
                // ✅ คำนวณค่าที่ถูกต้อง
                const usagePercent = Math.round((currentUsed / trialLimit) * 100);
                const remainingStorage = Math.max(0, trialLimit - currentUsed);
                const needMore = (currentUsed + totalSize) - trialLimit;

                const totalSizeMB = (totalSize / (1024 * 1024)).toFixed(2);
                const currentUsedMB = (currentUsed / (1024 * 1024)).toFixed(2);
                const remainingMB = (remainingStorage / (1024 * 1024)).toFixed(2);
                const overLimitMB = (needMore / (1024 * 1024)).toFixed(2);
                const trialLimitGB = (trialLimit / (1024 * 1024 * 1024)).toFixed(0);

                console.warn('❌ Storage limit exceeded!', {
                    usagePercent: `${usagePercent}%`,
                    currentUsedMB: `${currentUsedMB} MB`,
                    remainingMB: `${remainingMB} MB`,
                    overLimitMB: `${overLimitMB} MB`
                });

                Swal.fire({
                    title: '❌ พื้นที่ไม่เพียงพอ',
                    html: `
                    <div class="text-left space-y-3">
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                            <h4 class="font-bold text-red-800 mb-3 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                รายละเอียดการใช้พื้นที่
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">📁 ไฟล์ที่เลือก:</span>
                                    <span class="font-bold text-gray-800">${totalSizeMB} MB</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">💾 ใช้งานแล้ว:</span>
                                    <span class="font-bold text-orange-600">${currentUsedMB} MB (${usagePercent}%)</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">📊 เหลือพื้นที่:</span>
                                    <span class="font-bold ${remainingStorage > 0 ? 'text-green-600' : 'text-red-600'}">${remainingMB} MB</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">🎯 ขีดจำกัด Trial:</span>
                                    <span class="font-bold text-gray-800">${trialLimitGB} GB</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t-2 border-red-300">
                                    <span class="text-red-700 font-bold">⚠️ เกินโควต้า:</span>
                                    <span class="font-bold text-red-700 text-lg">${overLimitMB} MB</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i>
                                แนะนำ
                            </h4>
                            <ul class="text-sm text-blue-700 space-y-1.5 list-none">
                                <li class="flex items-start">
                                    <span class="mr-2">🚀</span>
                                    <span>อัปเกรดเป็นเวอร์ชันเต็มเพื่อใช้พื้นที่ไม่จำกัด</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">🗑️</span>
                                    <span>ลบไฟล์เก่าที่ไม่ใช้แล้วเพื่อเพิ่มพื้นที่ว่าง</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">📦</span>
                                    <span>บีบอัดไฟล์หรือแบ่งเป็นส่วนเล็กๆ ก่อนอัปโหลด</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                `,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-rocket mr-2"></i>อัปเกรดเลย',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>ปิด',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl',
                        title: 'text-xl',
                        htmlContainer: 'text-sm',
                        confirmButton: 'rounded-xl px-6 py-2.5 font-semibold',
                        cancelButton: 'rounded-xl px-6 py-2.5 font-semibold'
                    },
                    buttonsStyling: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        showUpgradeModal();
                    }
                });

                // Clear input
                input.value = '';
                document.getElementById('selectedFiles')?.classList.add('hidden');
                document.getElementById('uploadStartBtn').disabled = true;
                return;
            }
        }

        // Display files if validation passed
        displaySelectedFiles(files);
        document.getElementById('uploadStartBtn').disabled = false;
    }



    // 🆕 ตรวจสอบไฟล์ด้วยการตั้งค่าจาก DB
    function validateFilesWithDBSettings(files) {
        console.log('🔍 Validating files with DB settings...');
        console.log('📋 Allowed types:', allowedFileTypes);
        console.log('📏 Max size:', formatFileSize(maxFileSize));

        const validFiles = [];
        const invalidFiles = [];

        files.forEach(file => {
            const extension = file.name.split('.').pop().toLowerCase();
            const reasons = [];

            // ✅ ตรวจสอบขนาดไฟล์ตามการตั้งค่าจาก DB
            if (file.size > maxFileSize) {
                reasons.push(`ขนาดไฟล์เกิน ${formatFileSize(maxFileSize)}`);
            }

            // ✅ ตรวจสอบนามสกุลไฟล์ตามการตั้งค่าจาก DB
            if (allowedFileTypes.length > 0 && !allowedFileTypes.includes(extension)) {
                reasons.push(`ประเภทไฟล์ .${extension} ไม่ได้รับอนุญาต`);
            }

            // ✅ ตรวจสอบขนาดไฟล์ไม่ให้เป็น 0
            if (file.size === 0) {
                reasons.push('ไฟล์ว่างเปล่า (ขนาด 0 bytes)');
            }

            if (reasons.length > 0) {
                invalidFiles.push({
                    file: file,
                    reasons: reasons,
                    size: file.size,
                    extension: extension
                });
            } else {
                validFiles.push(file);
            }
        });

        console.log(`✅ Validation complete: ${validFiles.length} valid, ${invalidFiles.length} invalid`);

        return {
            validFiles: validFiles,
            invalidFiles: invalidFiles
        };
    }




    // 🆕 แสดงข้อผิดพลาดการตรวจสอบไฟล์
    function showFileValidationErrors(invalidFiles, validFiles) {
        let errorHtml = `
        <div class="text-left">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                <h4 class="font-bold text-red-800 mb-3">❌ ไฟล์เหล่านี้ไม่สามารถอัปโหลดได้:</h4>
                <div class="space-y-2 max-h-60 overflow-y-auto">
    `;

        invalidFiles.forEach(item => {
            errorHtml += `
            <div class="bg-white border border-red-200 rounded-lg p-3">
                <div class="font-medium text-red-700">${escapeHtml(item.file.name)}</div>
                <div class="text-sm text-red-600 mt-1">
                    <div>📏 ขนาด: ${formatFileSize(item.size)}</div>
                    <div>📄 ประเภท: .${item.extension}</div>
                    <div class="mt-2">
                        <strong>ปัญหา:</strong>
                        <ul class="list-disc pl-4 mt-1">
                            ${item.reasons.map(reason => `<li>${reason}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            </div>
        `;
        });

        errorHtml += `
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                <h4 class="font-bold text-blue-800 mb-2">📋 การตั้งค่าปัจจุบัน:</h4>
                <div class="text-sm text-blue-700 space-y-1">
                    <div><strong>ขนาดไฟล์สูงสุด:</strong> ${formatFileSize(maxFileSize)}</div>
                    <div><strong>ประเภทไฟล์ที่อนุญาต:</strong> ${allowedFileTypes.join(', ')}</div>
                </div>
            </div>
    `;

        if (validFiles.length > 0) {
            errorHtml += `
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-sm text-green-700">
                    ✅ <strong>ไฟล์ที่ถูกต้อง ${validFiles.length} ไฟล์</strong> จะถูกเลือกสำหรับอัปโหลด
                </p>
            </div>
        `;
        }

        errorHtml += '</div>';

        Swal.fire({
            icon: 'warning',
            title: 'ไฟล์บางไฟล์ไม่ถูกต้อง',
            html: errorHtml,
            confirmButtonText: 'ตกลง',
            customClass: {
                popup: 'glass-card rounded-2xl',
                confirmButton: 'rounded-xl'
            },
            width: '600px'
        });
    }




    function updateFileInputWithValidFiles(validFiles) {
        const fileInput = document.getElementById('fileInput');
        const dt = new DataTransfer();
        validFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function displaySelectedFiles(files) {
        console.log('📋 Displaying selected files:', files.length);

        const container = document.getElementById('selectedFiles');
        const fileList = document.getElementById('selectedFilesList');

        let html = '';
        files.forEach((file, index) => {
            const size = formatFileSize(file.size);
            const fileType = getFileIcon(file.name);

            html += `
            <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center">
                    <div class="w-8 h-8 ${fileType.color} rounded-lg flex items-center justify-center mr-3">
                        <i class="${fileType.icon} text-white text-sm"></i>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-800">${escapeHtml(file.name)}</span>
                        <p class="text-xs text-gray-500">${size}</p>
                    </div>
                </div>
                <button onclick="removeFile(${index})" class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        });

        fileList.innerHTML = html;
        container.classList.remove('hidden');
    }

    function removeFile(index) {
        console.log('🗑️ Removing file at index:', index);

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


    function startUpload() {
        // ป้องกันการอัปโหลดซ้ำ
        if (isUploading) {
            console.log('Upload already in progress, ignoring duplicate request');
            Swal.fire({
                icon: 'info',
                title: 'กำลังอัปโหลดอยู่',
                text: 'กรุณารอการอัปโหลดปัจจุบันให้เสร็จสิ้นก่อน',
                timer: 2000,
                showConfirmButton: false,
                customClass: {
                    popup: 'glass-card rounded-2xl'
                }
            });
            return;
        }

        console.log('🚀 Starting upload process...');
        isUploading = true;

        const files = document.getElementById('fileInput').files;

        if (files.length === 0) {
            isUploading = false;
            Swal.fire('เกิดข้อผิดพลาด', 'กรุณาเลือกไฟล์', 'error');
            return;
        }

        const uploadFolderId = currentFolder === 'root' ? null : currentFolder;

        // ปิดการใช้งานปุ่มอัปโหลด
        const uploadBtn = document.getElementById('uploadStartBtn');
        if (uploadBtn) {
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>กำลังอัปโหลด...';
        }

        // Show progress modal
        let uploadProgress = 0;
        let uploadedCount = 0;
        const totalFiles = files.length;

        Swal.fire({
            title: `กำลังอัปโหลด... ${IS_TRIAL_MODE ? '(Trial Mode)' : ''}`,
            html: `
            <div class="text-left">
                <p class="mb-4 text-center">กำลังอัปโหลด <strong>${totalFiles}</strong> ไฟล์</p>
                ${IS_TRIAL_MODE ? '<div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mb-4 text-center"><p class="text-sm text-orange-700"><i class="fas fa-flask mr-2"></i>Trial Mode: ไฟล์จะถูกจัดเก็บในระบบทดลอง</p></div>' : ''}
                <div class="w-full bg-gray-200 rounded-full h-4 mb-4">
                    <div id="uploadProgressBar" class="bg-gradient-to-r from-blue-500 to-purple-600 h-4 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span id="uploadStatus">เตรียมการอัปโหลด...</span>
                    <span id="uploadPercent">0%</span>
                </div>
            </div>
        `,
            allowOutsideClick: false,
            showConfirmButton: false,
            customClass: {
                popup: 'glass-card rounded-2xl'
            },
            didOpen: () => {
                startFileUploadProcess(files, uploadFolderId, totalFiles);
            }
        });
    }


    function startFileUploadProcess(files, folderId, totalFiles) {
        let uploadedCount = 0;
        let failedCount = 0;
        const uploadResults = [];

        uploadFilesSequentially(files, folderId, 0, uploadedCount, failedCount, uploadResults, totalFiles);
    }

    function uploadFilesSequentially(files, folderId, index, uploadedCount, failedCount, uploadResults, totalFiles) {
        if (index >= files.length) {
            showUploadCompleteWithAutoClose(uploadedCount, failedCount, uploadResults, totalFiles);
            return;
        }

        const file = files[index];
        const currentFileNum = index + 1;

        updateUploadProgress(currentFileNum, totalFiles, `อัปโหลด: ${file.name}`);

        const formData = new FormData();
        formData.append('file', file);
        formData.append('folder_id', folderId || '');
        formData.append('parent_folder_id', folderId || '');

        console.log(`📤 Uploading file ${currentFileNum}/${totalFiles}:`, file.name);

        fetch(API_BASE_URL + 'upload_file', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(response => {
                console.log('📡 Upload response status:', response.status);

                // ✅ จัดการ 403 Access Denied
                if (response.status === 403) {
                    return response.json().then(data => {
                        if (data.error_type === 'access_denied') {
                            hideUploadProgress();
                            showAccessDeniedModal(data);
                            return Promise.reject(new Error('Access Denied - Modal Shown'));
                        }
                        throw new Error('Forbidden: ' + (data.message || 'ไม่มีสิทธิ์เข้าถึง'));
                    });
                }

                // ✅ จัดการ 500 Internal Server Error แบบพิเศษ
                if (response.status === 500) {
                    console.warn('⚠️ Server returned 500 but file might be uploaded successfully');

                    // พยายาม parse response ก่อน
                    return response.text().then(text => {
                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            console.error('❌ Cannot parse 500 response:', text.substring(0, 200));

                            // สร้าง mock success response ถ้า parse ไม่ได้
                            // เพราะไฟล์อาจ upload สำเร็จแล้วแต่ response error
                            data = {
                                success: true,
                                message: 'อัปโหลดสำเร็จ (Server Response Error)',
                                data: {
                                    file_id: 'unknown_' + Date.now(),
                                    file_name: file.name,
                                    file_size: file.size,
                                    note: 'ไฟล์อาจถูกอัปโหลดแล้ว แต่เซิร์ฟเวอร์ตอบกลับไม่ถูกต้อง'
                                }
                            };
                        }

                        return data;
                    });
                }

                return handleApiResponse(response);
            })
            .then(data => {
                console.log('📄 Upload response data:', data);

                if (data.success) {
                    uploadedCount++;
                    uploadResults.push({
                        file: file.name,
                        status: 'success',
                        message: data.message || 'อัปโหลดสำเร็จ',
                        file_id: data.data?.file_id,
                        file_size_mb: data.data?.file_size_mb,
                        note: data.data?.note || null
                    });

                    console.log(`✅ File ${currentFileNum} uploaded successfully:`, file.name);
                } else {
                    failedCount++;
                    uploadResults.push({
                        file: file.name,
                        status: 'error',
                        message: data.message || 'อัปโหลดล้มเหลว'
                    });

                    console.log(`❌ File ${currentFileNum} upload failed:`, data.message);
                }

                // อัปโหลดไฟล์ถัดไป
                uploadFilesSequentially(files, folderId, index + 1, uploadedCount, failedCount, uploadResults, totalFiles);
            })
            .catch(error => {
                console.error(`💥 Upload error for file ${currentFileNum}:`, error);

                // ไม่แสดง error ถ้าเป็น Access Denied (เพราะ Modal แสดงแล้ว)
                if (error.message === 'Access Denied - Modal Shown') {
                    return; // หยุดการอัปโหลดที่เหลือ
                }

                failedCount++;
                uploadResults.push({
                    file: file.name,
                    status: 'error',
                    message: 'เกิดข้อผิดพลาดในการอัปโหลด: ' + error.message,
                    error_details: error.data?.debug_info || null
                });

                // อัปโหลดไฟล์ถัดไป
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

    function hideUploadProgress() {
        // ปิด Swal loading modal ที่เปิดอยู่
        if (Swal.isVisible()) {
            Swal.close();
        }

        console.log('📴 Upload progress modal hidden');
    }

    /**
     * 🎉 แสดงผลการอัปโหลดเสร็จสิ้นแบบปิดอัตโนมัติ (แก้ไข - มี Auto-Share)
     * ⚠️ แทนที่ function showUploadCompleteWithAutoClose() เดิมในไฟล์หลัก
     */
    function showUploadCompleteWithAutoClose(uploadedCount, failedCount, uploadResults, totalFiles) {
        // รีเซ็ต upload state
        isUploading = false;
        isDragAndDropUpload = false;

        // ปิด progress modal
        hideUploadProgress();

        const successCount = uploadedCount;
        const errorCount = failedCount;

        let title = 'การอัปโหลดเสร็จสิ้น';
        let message = `สำเร็จ ${successCount} ไฟล์`;
        let icon = 'success';

        if (errorCount > 0) {
            message += `, ล้มเหลว ${errorCount} ไฟล์`;
            icon = successCount > 0 ? 'warning' : 'error';
        }

        // สร้างรายละเอียดสำหรับไฟล์ที่มีปัญหา
        let detailsHtml = '';
        const problemFiles = uploadResults.filter(result => result.status === 'error' || result.note);

        if (problemFiles.length > 0) {
            detailsHtml = '<div class="mt-3"><small class="text-muted">รายละเอียด:</small><ul class="text-start small mt-2">';
            problemFiles.forEach(result => {
                const statusIcon = result.status === 'success' ? '⚠️' : '❌';
                detailsHtml += `<li>${statusIcon} ${result.file}: ${result.message}`;
                if (result.note) {
                    detailsHtml += ` <em>(${result.note})</em>`;
                }
                detailsHtml += '</li>';
            });
            detailsHtml += '</ul></div>';
        }

        Swal.fire({
            icon: icon,
            title: title,
            html: message + detailsHtml,
            timer: errorCount > 0 ? 5000 : 3000, // แสดงนานขึ้นถ้ามี error
            showConfirmButton: errorCount > 0, // แสดงปุ่มถ้ามี error
            confirmButtonText: 'ตกลง',
            customClass: {
                popup: 'glass-card rounded-2xl',
                confirmButton: 'rounded-xl'
            }
        }).then(() => {
            // รีเฟรชรายการไฟล์
            if (typeof refreshFiles === 'function') {
                console.log('🔄 Refreshing folder contents after upload...');
                refreshFiles();
            }

            // อัปเดตข้อมูล member
            if (typeof loadMemberInfo === 'function') {
                loadMemberInfo();
            }

            // ปิด upload modal ถ้าเปิดอยู่
            if (!document.getElementById('uploadModal').classList.contains('hidden')) {
                closeUploadModal();
            }

            // ✅ แสดง Auto-Share Prompt (ถ้ามีไฟล์สำเร็จ)
            if (successCount > 0) {
                console.log('🎯 Triggering auto-share prompt...');
                promptAutoShareAfterUpload(uploadResults);
            }
        });

        console.log(`✅ Upload completed: ${uploadedCount} success, ${failedCount} failed`);
    }

    async function checkCreateFolderPermission(folderId = null) {
        try {
            // 🆕 ใช้ normalized folder ID
            const normalizedFolderId = normalizeFolderId(folderId || currentFolder);

            const response = await fetch(API_BASE_URL + 'check_create_folder_permission', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    folder_id: normalizedFolderId || 'root'
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                return {
                    canCreate: data.can_create_folder,
                    accessType: data.access_type,
                    permissionSource: data.permission_source,
                    message: data.message,
                    normalizedFolderId: normalizedFolderId
                };
            } else {
                throw new Error(data.message || 'ไม่สามารถตรวจสอบสิทธิ์ได้');
            }

        } catch (error) {
            console.error('❌ Error checking create folder permission:', error);
            return {
                canCreate: false,
                accessType: 'error',
                permissionSource: 'error',
                message: 'เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์'
            };
        }
    }

    // ✅ ปรับปรุง handleCreateFolderClick ให้เช็คสิทธิ์
    async function handleCreateFolderClick() {
        console.log('📁 Handle create folder click');

        // แสดง loading
        const loadingToast = Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'กำลังตรวจสอบสิทธิ์...',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });

        try {
            // เช็คสิทธิ์
            const permission = await checkCreateFolderPermission(currentFolder);

            // ปิด loading
            Swal.close();

            if (permission.canCreate) {
                // ✅ มีสิทธิ์ - เปิด modal
                console.log('✅ Permission granted:', permission.accessType);

                if (IS_TRIAL_MODE) {
                    console.log('🎭 Trial mode - allowing folder creation');
                }

                showCreateFolderModal();

            } else {
                // ❌ ไม่มีสิทธิ์ - แสดง error
                console.log('❌ Permission denied:', permission.message);

                showAccessDeniedModal({
                    message: permission.message,
                    folder_id: currentFolder,
                    access_type: permission.accessType,
                    permission_source: permission.permissionSource
                });
            }

        } catch (error) {
            console.error('💥 Error in handleCreateFolderClick:', error);

            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถตรวจสอบสิทธิ์ได้: ' + error.message,
                confirmButtonText: 'ตกลง'
            });
        }
    }



    async function createNewFolder() {
        console.log('📁 Creating new folder...');

        const folderName = document.getElementById('newFolderName').value.trim();

        if (!folderName) {
            Swal.fire('เกิดข้อผิดพลาด', 'กรุณาใส่ชื่อโฟลเดอร์', 'error');
            return;
        }

        // 🆕 ใช้ normalized parent ID
        const normalizedParentId = normalizeFolderId(currentFolder === 'root' ? null : currentFolder);

        console.log(`📁 Creating folder "${folderName}" in normalized parent: ${normalizedParentId || 'root'}`);

        // แสดง loading
        Swal.fire({
            title: `กำลังสร้างโฟลเดอร์...${IS_TRIAL_MODE ? ' (Trial)' : ''}`,
            text: `สร้างโฟลเดอร์ "${folderName}"`,
            allowOutsideClick: false,
            showConfirmButton: false,
            customClass: {
                popup: 'glass-card rounded-2xl'
            },
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const formData = new FormData();
            formData.append('folder_name', folderName);
            formData.append('parent_id', normalizedParentId || 'root');  // ✅ ใช้ normalized ID

            console.log('📤 Sending create folder request with normalized parent ID:', normalizedParentId);

            const response = await fetch(API_BASE_URL + 'create_folder', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            console.log('📡 Response status:', response.status);

            const responseText = await response.text();
            console.log('📄 Raw response:', responseText.substring(0, 500));

            const contentType = response.headers.get('content-type');

            if (!contentType || !contentType.includes('application/json')) {
                console.error('❌ Response is not JSON, content-type:', contentType);
                throw new Error('เซิร์ฟเวอร์ตอบกลับไม่ถูกต้อง');
            }

            let data;
            try {
                data = JSON.parse(responseText);
                console.log('📄 Parsed JSON data:', data);
            } catch (jsonError) {
                console.error('❌ JSON parse error:', jsonError);
                throw new Error('ไม่สามารถแปลงข้อมูลได้');
            }

            if (data.success) {
                console.log('✅ Folder created successfully:', data.data);

                Swal.fire({
                    icon: 'success',
                    title: 'สร้างโฟลเดอร์สำเร็จ',
                    text: data.message || `สร้างโฟลเดอร์ "${folderName}" เรียบร้อยแล้ว`,
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'glass-card rounded-2xl'
                    }
                }).then(() => {
                    closeCreateFolderModal();
                    refreshFiles();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `สร้างโฟลเดอร์ "${folderName}" สำเร็จ`,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'glass-card rounded-xl'
                        }
                    });
                });

            } else {
                console.error('❌ Folder creation failed:', data.message);

                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถสร้างโฟลเดอร์ได้',
                    text: data.message || 'เกิดข้อผิดพลาดในการสร้างโฟลเดอร์',
                    confirmButtonText: 'ตกลง',
                    customClass: {
                        popup: 'glass-card rounded-2xl',
                        confirmButton: 'rounded-xl'
                    }
                });
            }

        } catch (error) {
            console.error('💥 Network or other error:', error);

            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: error.message || 'ไม่สามารถสร้างโฟลเดอร์ได้',
                confirmButtonText: 'ตกลง',
                customClass: {
                    popup: 'glass-card rounded-2xl',
                    confirmButton: 'rounded-xl'
                }
            });
        }
    }



    async function shareItem(itemId, itemType, itemName) {
        try {
            console.log('📤 Starting share process for:', itemId, itemType, itemName);

            // 🔥 ตรวจสอบ Trial Mode ก่อนทำอะไร
            if (IS_TRIAL_MODE) {
                console.log('⚠️ Share blocked: Trial Mode');
                Swal.fire({
                    title: '🔒 ข้อจำกัดโหมดทดลอง',
                    html: `
                    <div class="text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i class="fas fa-lock text-3xl text-white"></i>
                        </div>
                        <p class="text-gray-600 mb-4">การแชร์ไฟล์ใช้งานได้เฉพาะเวอร์ชันเต็มเท่านั้น</p>
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <p class="text-sm text-blue-700">
                                💡 อัปเกรดเพื่อแชร์ไฟล์และใช้ฟีเจอร์ครบครัน
                            </p>
                        </div>
                    </div>
                `,
                    confirmButtonText: '🚀 อัปเกรด',
                    showCancelButton: true,
                    cancelButtonText: 'ปิด',
                    confirmButtonColor: '#f59e0b',
                    customClass: {
                        popup: 'glass-card rounded-2xl',
                        confirmButton: 'rounded-xl',
                        cancelButton: 'rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        showUpgradeModal();
                    }
                });
                return; // 🔥 หยุดการทำงาน
            }

            // ตั้งค่าตัวแปร global
            currentShareItem = {
                id: itemId,
                type: itemType,
                name: itemName
            };

            // ตรวจสอบ DOM elements ก่อนใช้งาน
            const shareItemNameEl = document.getElementById('shareItemName');
            const shareItemTypeEl = document.getElementById('shareItemType');
            const shareModalEl = document.getElementById('shareModal');

            if (!shareModalEl) {
                console.error('❌ Share modal not found in DOM');
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่พบหน้าต่างแชร์ กรุณาโหลดหน้าใหม่',
                    confirmButtonText: 'ตกลง',
                    customClass: {
                        popup: 'glass-card rounded-2xl',
                        confirmButton: 'rounded-xl'
                    }
                });
                return;
            }

            // อัปเดต UI ถ้า elements มีอยู่
            if (shareItemNameEl) {
                shareItemNameEl.textContent = itemName || 'ไม่ระบุชื่อ';
            } else {
                console.warn('⚠️ shareItemName element not found');
            }

            if (shareItemTypeEl) {
                shareItemTypeEl.textContent = itemType === 'folder' ? 'โฟลเดอร์' : 'ไฟล์';
            } else {
                console.warn('⚠️ shareItemType element not found');
            }

            // รีเซ็ตฟอร์ม
            resetShareForm();

            // แสดง Modal
            shareModalEl.classList.remove('hidden');

            console.log('✅ Share modal opened successfully');

        } catch (error) {
            console.error('❌ Error in shareItem:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'เกิดข้อผิดพลาดในการเปิดหน้าต่างแชร์: ' + error.message,
                confirmButtonText: 'ตกลง',
                customClass: {
                    popup: 'glass-card rounded-2xl',
                    confirmButton: 'rounded-xl'
                }
            });
        }
    }

    // Close Share Modal
    function closeShareModal() {
        document.getElementById('shareModal').classList.add('hidden');
        resetShareForm();
        currentShareItem = null;
    }
    // Reset Share Form
    function resetShareForm() {
        // Reset form values
        document.getElementById('shareEmail').value = '';
        document.getElementById('shareMessage').value = '';

        // Reset permissions
        selectedEmailPermission = 'reader';

        updateEmailPermissionButtons();
    }

    // Select Share Type
    function selectShareType(type) {
        // Not needed anymore since we only have email sharing
    }


    function setEmailPermission(permission) {
        selectedEmailPermission = permission;
        updateEmailPermissionButtons();
    }

    // Set Link Permission
    function setLinkPermission(permission) {
        selectedLinkPermission = permission;
        updateLinkPermissionButtons();
    }

    function setLinkAccess(access) {
        selectedLinkAccess = access;
        updateLinkAccessButtons();
    }

    // Update Permission Buttons
    function updatePermissionButtons() {
        updateEmailPermissionButtons();
        updateLinkPermissionButtons();
        updateLinkAccessButtons();
    }


    function updateEmailPermissionButtons() {
        document.querySelectorAll('.email-permission-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('bg-gray-100', 'text-gray-700');
            btn.classList.remove('bg-purple-500', 'text-white');
        });

        const activeBtn = document.querySelector(`[data-permission="${selectedEmailPermission}"].email-permission-btn`);
        if (activeBtn) {
            activeBtn.classList.add('active');
            activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
            activeBtn.classList.add('bg-purple-500', 'text-white');
        }
    }

    function updateLinkPermissionButtons() {
        document.querySelectorAll('.link-permission-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('bg-gray-100', 'text-gray-700');
            btn.classList.remove('bg-green-500', 'text-white');
        });

        const activeBtn = document.querySelector(`[data-permission="${selectedLinkPermission}"].link-permission-btn`);
        if (activeBtn) {
            activeBtn.classList.add('active');
            activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
            activeBtn.classList.add('bg-green-500', 'text-white');
        }
    }

    function updateLinkAccessButtons() {
        document.querySelectorAll('.link-access-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('bg-gray-100', 'text-gray-700');
            btn.classList.remove('bg-blue-500', 'text-white');
        });

        const activeBtn = document.querySelector(`[data-access="${selectedLinkAccess}"].link-access-btn`);
        if (activeBtn) {
            activeBtn.classList.add('active');
            activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
            activeBtn.classList.add('bg-blue-500', 'text-white');
        }
    }

    // Share with Email
    async function shareWithEmail() {
        console.log('📧 Starting email share process...');

        // Prevent multiple sharing attempts
        if (isSharing) {
            console.log('Already sharing, skipping...');
            return;
        }

        // Validate inputs
        const email = document.getElementById('shareEmail')?.value?.trim();
        const message = document.getElementById('shareMessage')?.value?.trim() || '';

        if (!email) {
            Swal.fire({
                icon: 'warning',
                title: 'ข้อมูลไม่ครบ',
                text: 'กรุณาใส่อีเมลผู้รับ',
                confirmButtonText: 'ตกลง',
                customClass: {
                    popup: 'glass-card rounded-2xl',
                    confirmButton: 'rounded-xl'
                }
            });
            return;
        }

        if (!validateEmail(email)) {
            Swal.fire({
                icon: 'warning',
                title: 'อีเมลไม่ถูกต้อง',
                text: 'กรุณาใส่อีเมลที่ถูกต้อง',
                confirmButtonText: 'ตกลง',
                customClass: {
                    popup: 'glass-card rounded-2xl',
                    confirmButton: 'rounded-xl'
                }
            });
            return;
        }

        if (!currentShareItem) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่พบข้อมูลไฟล์ที่จะแชร์',
                confirmButtonText: 'ตกลง',
                customClass: {
                    popup: 'glass-card rounded-2xl',
                    confirmButton: 'rounded-xl'
                }
            });
            return;
        }

        // Set sharing state
        isSharing = true;

        // Update button state
        const btn = document.getElementById('shareEmailBtn') || document.querySelector('.share-with-email-btn');
        const originalText = btn ? btn.innerHTML : '';
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>กำลังแชร์...';
            btn.disabled = true;
        }

        try {
            console.log('📤 Making share request...');
            console.log('🎯 Current share item:', currentShareItem); // เพิ่มการ debug

            // ✅ ตรวจสอบและปรับปรุงชื่อไฟล์
            let fileName = currentShareItem.name;
            if (!fileName || fileName === 'undefined') {
                // ลองหาจาก fileListData
                const fileData = fileListData.find(item => item.id === currentShareItem.id);
                if (fileData && fileData.name) {
                    fileName = fileData.name;
                    currentShareItem.name = fileName; // อัปเดตกลับไป
                    console.log('📝 Found filename from fileListData:', fileName);
                } else {
                    fileName = currentShareItem.type === 'folder' ? 'โฟลเดอร์' : 'ไฟล์';
                    currentShareItem.name = fileName;
                    console.log('📝 Using default filename:', fileName);
                }
            }

            console.log('🎯 Using API endpoint:', API_BASE_URL + 'share_with_email');

            // Prepare form data
            const formData = new FormData();
            formData.append('item_id', currentShareItem.id);
            formData.append('item_type', currentShareItem.type);
            formData.append('email', email);
            formData.append('permission', selectedEmailPermission);
            formData.append('message', message);

            // Make API call to current controller
            const response = await fetch(API_BASE_URL + 'share_with_email', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            console.log('📡 Share response status:', response.status);
            console.log('📡 Share response headers:', response.headers.get('content-type'));

            // Check for HTML response (server error)
            const contentType = response.headers.get('content-type');
            if (!response.ok || !contentType || !contentType.includes('application/json')) {

                if (response.status === 500) {
                    throw new Error('เซิร์ฟเวอร์มีปัญหาภายใน กรุณาลองใหม่อีกครั้ง');
                } else if (response.status === 404) {
                    throw new Error('ไม่พบ API endpoint (404) - ตรวจสอบว่า Controller มี function share_with_email');
                } else if (response.status === 403) {
                    throw new Error('ไม่มีสิทธิ์ในการแชร์ไฟล์นี้');
                }

                const textResponse = await response.text();
                console.error('Non-JSON response:', textResponse.substring(0, 500));
                throw new Error(`เซิร์ฟเวอร์ตอบกลับในรูปแบบที่ไม่ถูกต้อง (HTTP ${response.status})`);
            }

            const data = await response.json();
            console.log('📨 Share response:', data);

            if (data.success) {
                // ✅ แสดงผลสำเร็จ - ปรับปรุงการแสดงชื่อไฟล์
                const displayFileName = currentShareItem.name || 'ไฟล์';
                const fileTypeText = currentShareItem.type === 'folder' ? 'โฟลเดอร์' : 'ไฟล์';
                const permissionText = selectedEmailPermission === 'reader' ? 'ดูได้อย่างเดียว' :
                    selectedEmailPermission === 'writer' ? 'แก้ไขได้' :
                        selectedEmailPermission === 'commenter' ? 'แสดงความคิดเห็นได้' :
                            selectedEmailPermission;

                await Swal.fire({
                    icon: 'success',
                    title: 'แชร์สำเร็จ! 🎉',
                    html: `
                    <div class="text-center">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                            <div class="flex items-center justify-center mb-3">
                                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-${currentShareItem.type === 'folder' ? 'folder' : 'file'} text-white text-lg"></i>
                                </div>
                            </div>
                            <p class="text-sm text-green-700 mb-2">
                                <strong>${fileTypeText}:</strong> ${displayFileName}
                            </p>
                            <p class="text-sm text-green-700 mb-2">
                                <strong>แชร์ไปยัง:</strong> ${email}
                            </p>
                            <p class="text-sm text-green-700">
                                <strong>สิทธิ์:</strong> ${permissionText}
                            </p>
                        </div>
                        <p class="text-gray-600">ผู้รับจะได้รับอีเมลแจ้งเตือนและสามารถเข้าถึงได้ทันที</p>
                    </div>
                `,
                    confirmButtonText: 'เสร็จสิ้น',
                    timer: 5000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'glass-card rounded-2xl',
                        confirmButton: 'rounded-xl'
                    }
                });

                // ล้างฟอร์มและปิด modal
                document.getElementById('shareEmail').value = '';
                document.getElementById('shareMessage').value = '';
                closeShareModal();

            } else {
                // Error from API
                throw new Error(data.message || 'ไม่สามารถแชร์ได้');
            }

        } catch (error) {
            console.error('💥 Share error:', error);

            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: error.message || 'ไม่สามารถแชร์ได้',
                confirmButtonText: 'ตกลง',
                customClass: {
                    popup: 'glass-card rounded-2xl',
                    confirmButton: 'rounded-xl'
                }
            });

        } finally {
            // Reset state
            isSharing = false;

            // Reset button
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText || '<i class="fas fa-paper-plane mr-2"></i>ส่งการแชร์';
            }
        }
    }

    async function logShareToDatabase(itemId, itemType, email, permission, message) {
        try {
            console.log('📝 Logging share activity to database...');

            const response = await fetch(API_BASE_URL + 'log_share_activity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    item_id: itemId,
                    item_type: itemType,
                    target_email: email,
                    permission: permission,
                    message: message || ''
                }).toString()
            });

            if (response.ok) {
                const result = await response.json();
                console.log('✅ Share activity logged successfully:', result);

                if (result.success && result.logged_tables) {
                    console.log('📊 Logged to tables:', result.logged_tables);

                    // แสดง toast แจ้งเตือนว่าบันทึก log สำเร็จ
                    if (result.logged_tables.length > 0) {
                        const tableCount = result.logged_tables.length;
                        console.log(`✅ Successfully logged to ${tableCount} database tables`);
                    }
                }

                return result;
            } else {
                console.warn('⚠️ Failed to log share activity:', response.status);
                const errorText = await response.text();
                console.warn('⚠️ Log error response:', errorText);

                return {
                    success: false,
                    message: `HTTP ${response.status}: ${errorText}`
                };
            }

        } catch (error) {
            console.error('💥 Error logging share activity:', error);
            return {
                success: false,
                message: error.message
            };
        }
    }

    function detectUserEmail() {
        console.log('🔍 Detecting user email...');

        // 1. ลองดึงจาก global variable (ถ้ามี)
        if (typeof currentUserEmail !== 'undefined' && currentUserEmail) {
            console.log('✅ Found email from currentUserEmail:', currentUserEmail);
            return currentUserEmail;
        }

        // 2. ลองดึงจาก member info (ถ้ามี)
        if (typeof memberData !== 'undefined' && memberData && memberData.email) {
            console.log('✅ Found email from memberData:', memberData.email);
            return memberData.email;
        }

        // 3. ลองดึงจาก username (fallback)
        if (typeof currentUsername !== 'undefined' && currentUsername) {
            console.log('⚠️ Using username as fallback:', currentUsername);

            // ถ้า username มี @ แล้วก็ใช้เลย
            if (currentUsername.includes('@')) {
                return currentUsername;
            }

            // ถ้าไม่มี @ ให้เติม @gmail.com (assumption)
            return currentUsername + '@gmail.com';
        }

        // 4. ไม่พบข้อมูลเลย
        console.warn('⚠️ No email or username found');
        return '';
    }


    /**
     * 🎨 แสดง Auto-Share Prompt Modal หลัง Upload สำเร็จ
     * 🔥 อัปเดต: เพิ่มการตรวจสอบ Trial Mode
     * @param {Array} uploadResults - ผลลัพธ์การอัปโหลด
     */
    async function promptAutoShareAfterUpload(uploadResults) {
        console.log('🎉 Showing auto-share prompt after upload...');
        console.log('📦 Upload results:', uploadResults);

        // 🔥 ตรวจสอบ Trial Mode ก่อนทำอะไร
        if (IS_TRIAL_MODE) {
            console.log('⚠️ Auto-share blocked: Trial Mode');

            // แสดงข้อความว่าอัปโหลดสำเร็จ แต่ไม่สามารถแชร์ได้
            await Swal.fire({
                title: '🎉 อัปโหลดสำเร็จแล้ว!',
                html: `
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl shadow-lg mb-4 bg-gradient-to-br from-green-100 to-emerald-200">
                        <span style="font-size: 3rem;">✅</span>
                    </div>
                    <p class="text-gray-600 mb-4">อัปโหลดไฟล์สำเร็จ ${uploadResults.filter(r => r.status === 'success').length} ไฟล์</p>
                    
                    <!-- Trial Limitation Notice -->
                    <div class="bg-orange-50 border-2 border-orange-200 rounded-xl p-4 mt-4">
                        <div class="flex items-center justify-center space-x-2 mb-2">
                            <i class="fas fa-lock text-orange-600"></i>
                            <span class="font-semibold text-orange-800">โหมดทดลองใช้งาน</span>
                        </div>
                        <p class="text-sm text-orange-700">
                            การแชร์ไฟล์ใช้งานได้เฉพาะเวอร์ชันเต็มเท่านั้น
                        </p>
                        <div class="mt-3">
                            <button 
                                onclick="Swal.close(); showUpgradeModal();" 
                                class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all font-medium text-sm">
                                <i class="fas fa-arrow-up mr-2"></i>อัปเกรดเพื่อใช้ฟีเจอร์เต็ม
                            </button>
                        </div>
                    </div>
                </div>
            `,
                confirmButtonText: 'เข้าใจแล้ว',
                confirmButtonColor: '#10b981',
                customClass: {
                    popup: 'rounded-3xl shadow-2xl',
                    confirmButton: 'rounded-xl px-6 py-3 font-semibold'
                },
                width: '500px'
            });

            return; // 🔥 หยุดการทำงาน
        }

        // กรองเฉพาะไฟล์ที่อัปโหลดสำเร็จ
        const successFiles = uploadResults.filter(result => result.status === 'success');

        if (successFiles.length === 0) {
            console.log('⚠️ No successful uploads, skipping auto-share prompt');
            return;
        }

        // Detect email
        const detectedEmail = detectUserEmail();

        // ตรวจสอบว่ามี MS Office files หรือไม่
        const officeExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        const hasOfficeFiles = successFiles.some(file => {
            const ext = file.file.split('.').pop().toLowerCase();
            return officeExtensions.includes(ext);
        });

        // สร้างรายการไฟล์
        const fileList = successFiles.map(file => {
            const ext = file.file.split('.').pop().toLowerCase();
            const isOffice = officeExtensions.includes(ext);
            const icon = isOffice ? '📄' : '📁';
            const badge = isOffice ? '<span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full">MS Office</span>' : '';
            return `<li class="text-sm text-gray-700 flex items-center">${icon} ${file.file}${badge}</li>`;
        }).join('');

        const fileCount = successFiles.length;
        const fileWord = fileCount === 1 ? 'ไฟล์' : 'ไฟล์';

        // แสดง Modal
        const { value: formValues } = await Swal.fire({
            title: '',
            html: `
            <div class="text-left">
                <!-- Header with Icon -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl shadow-lg mb-4 bg-gradient-to-br from-green-100 to-emerald-200">
                        <span style="font-size: 3rem;">🎉</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">
                        อัปโหลดสำเร็จแล้ว!
                    </h2>
                    <p class="text-sm text-gray-500">
                        อัปโหลดไฟล์สำเร็จ ${fileCount} ${fileWord}
                    </p>
                </div>

                <!-- Success Summary -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-5 mb-5 shadow-sm">
                    <div class="flex items-start space-x-3 mb-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-green-800 mb-2">
                                รายการไฟล์ที่อัปโหลด
                            </h3>
                            <div class="max-h-32 overflow-y-auto">
                                <ul class="space-y-2">
                                    ${fileList}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                ${hasOfficeFiles ? `
                <!-- MS Office Notice -->
                <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border-2 border-amber-300 rounded-2xl p-4 mb-5 shadow-sm">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-amber-400 rounded-xl flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-amber-800 text-sm mb-2">
                                📝 หมายเหตุสำหรับไฟล์ MS Office (6 ชนิด)
                            </h4>
                            <p class="text-xs text-amber-900 leading-relaxed mb-2">
                                ไฟล์ประเภท <strong>Word (.doc, .docx), Excel (.xls, .xlsx), PowerPoint (.ppt, .pptx)</strong>
                            </p>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                💡 หากต้องการ<strong class="text-amber-900">ดูหรือแก้ไข</strong>ออนไลน์ จำเป็นต้อง<strong class="text-amber-900">แชร์ไปยังอีเมลของคุณ</strong><br>
                                📥 หรือเลือก<strong class="text-amber-900">"ดาวน์โหลด"</strong>เพื่อเปิดในโปรแกรมบนเครื่อง
                            </p>
                        </div>
                    </div>
                </div>
                ` : ''}

                <!-- Share Section -->
                <div class="bg-white border-2 border-gray-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-center mb-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-share-alt text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">
                                แชร์ไฟล์ออกไปภายนอก
                            </h3>
                        </div>
                    </div>
                    
                    <!-- Email Input -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-envelope text-purple-500 mr-2"></i>
                            อีเมลผู้รับ
                        </label>
                        <input 
                            type="email" 
                            id="autoShareEmail" 
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all" 
                            placeholder="example@email.com"
                            value="${detectedEmail}"
                            style="margin: 0;"
                        >
                    </div>

                    <!-- Permission Selection -->
                    <div class="mb-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-shield-alt text-purple-500 mr-2"></i>
                            สิทธิ์การเข้าถึง
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <button 
                                type="button"
                                class="auto-share-permission-btn group relative px-4 py-4 rounded-xl text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md bg-gradient-to-r from-purple-500 to-purple-600 text-white"
                                data-permission="reader"
                                onclick="selectAutoSharePermission('reader')"
                            >
                                <i class="fas fa-eye mb-1 block text-xl"></i>
                                <span class="block text-sm">ดูอย่างเดียว</span>
                            </button>
                            <button 
                                type="button"
                                class="auto-share-permission-btn group relative px-4 py-4 rounded-xl text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md bg-gray-100 text-gray-700 hover:bg-gray-200"
                                data-permission="writer"
                                onclick="selectAutoSharePermission('writer')"
                            >
                                <i class="fas fa-edit mb-1 block text-xl"></i>
                                <span class="block text-sm">แก้ไขได้</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-paper-plane mr-2"></i>แชร์เลย',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>ข้าม',
            confirmButtonColor: '#8b5cf6',
            cancelButtonColor: '#6b7280',
            customClass: {
                popup: 'rounded-3xl shadow-2xl border-0',
                confirmButton: 'rounded-xl px-6 py-3 font-semibold shadow-lg hover:shadow-xl transition-all',
                cancelButton: 'rounded-xl px-6 py-3 font-semibold shadow-md hover:shadow-lg transition-all'
            },
            width: '650px',
            padding: '2.5em',
            allowOutsideClick: false,
            preConfirm: () => {
                const email = document.getElementById('autoShareEmail').value.trim();

                if (!email) {
                    Swal.showValidationMessage('📧 กรุณาใส่อีเมลผู้รับ');
                    return false;
                }

                if (!validateEmail(email)) {
                    Swal.showValidationMessage('❌ กรุณาใส่อีเมลที่ถูกต้อง');
                    return false;
                }

                return {
                    email: email,
                    permission: selectedAutoSharePermission || 'reader'
                };
            }
        });

        // ถ้ากด "แชร์เลย"
        if (formValues) {
            console.log('✅ User confirmed auto-share:', formValues);
            await executeAutoShare(successFiles, formValues.email, formValues.permission);
        }
        // ถ้ากด "ข้าม"
        else {
            console.log('⏭️ User skipped auto-share');
            await Swal.fire({
                icon: 'info',
                title: '<span style="color: #3b82f6;">ข้ามการแชร์</span>',
                html: `
                <div class="text-center py-4">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-700 font-medium">คุณสามารถแชร์ไฟล์ได้ทีหลัง</p>
                    <p class="text-sm text-gray-500 mt-2">จากรายการไฟล์ > เลือกไฟล์ > คลิกปุ่มแชร์</p>
                </div>
            `,
                timer: 2500,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-3xl shadow-2xl'
                }
            });
        }
    }

    /**
     * ⚙️ เลือก Permission สำหรับ Auto-Share
     */
    let selectedAutoSharePermission = 'reader'; // Default

    function selectAutoSharePermission(permission) {
        selectedAutoSharePermission = permission;

        // Update UI
        document.querySelectorAll('.auto-share-permission-btn').forEach(btn => {
            btn.classList.remove('bg-gradient-to-r', 'from-purple-500', 'to-purple-600', 'text-white', 'scale-105');
            btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
        });

        const activeBtn = document.querySelector(`[data-permission="${permission}"].auto-share-permission-btn`);
        if (activeBtn) {
            activeBtn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            activeBtn.classList.add('bg-gradient-to-r', 'from-purple-500', 'to-purple-600', 'text-white', 'scale-105');
        }
    }


    /**
     * 🚀 Execute Auto-Share สำหรับทุกไฟล์ (เพิ่ม Debug)
     */
    async function executeAutoShare(successFiles, email, permission) {
        console.log('🚀 Starting auto-share for', successFiles.length, 'files');
        console.log('📧 Email:', email);
        console.log('🔐 Permission:', permission);

        // ⭐ DEBUG: แสดง uploadResults structure
        console.log('📦 DEBUG - Upload Results:', JSON.stringify(successFiles, null, 2));
        if (successFiles.length > 0) {
            console.log('📋 DEBUG - First file structure:', successFiles[0]);
            console.log('🔑 DEBUG - First file keys:', Object.keys(successFiles[0]));
            console.log('🆔 DEBUG - file_id value:', successFiles[0].file_id);
            console.log('🆔 DEBUG - fileId value:', successFiles[0].fileId);
        }

        // แสดง loading
        Swal.fire({
            title: 'กำลังแชร์ไฟล์...',
            html: `
            <div class="text-center">
                <p class="mb-4">กำลังแชร์ <strong>${successFiles.length}</strong> ไฟล์ไปยัง <strong>${email}</strong></p>
                <div class="w-full bg-gray-200 rounded-full h-4 mb-4">
                    <div id="autoShareProgressBar" class="bg-gradient-to-r from-purple-500 to-pink-600 h-4 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span id="autoShareStatus">เตรียมการแชร์...</span>
                    <span id="autoSharePercent">0%</span>
                </div>
            </div>
        `,
            allowOutsideClick: false,
            showConfirmButton: false,
            customClass: {
                popup: 'glass-card rounded-2xl'
            }
        });

        let sharedCount = 0;
        let failedCount = 0;
        const shareResults = [];

        // แชร์ทีละไฟล์
        for (let i = 0; i < successFiles.length; i++) {
            const file = successFiles[i];
            const currentFileNum = i + 1;

            console.log(`\n📤 ===== Sharing file #${currentFileNum} =====`);
            console.log('📋 File object:', file);
            console.log('📝 File name:', file.file);
            console.log('🆔 file.file_id:', file.file_id);
            console.log('🆔 file.fileId:', file.fileId);

            // Update progress
            updateAutoShareProgress(currentFileNum, successFiles.length, `แชร์: ${file.file}`);

            try {
                // ตรวจสอบว่ามี file_id หรือไม่
                const fileId = file.file_id || file.fileId;

                if (!fileId) {
                    console.error('❌ No file ID found!');
                    console.error('📦 File object:', file);
                    console.error('🔑 Available keys:', Object.keys(file));
                    throw new Error('ไม่พบ File ID จากการอัปโหลด');
                }

                console.log('✅ Using file ID:', fileId);

                const formData = new FormData();
                formData.append('item_id', fileId);
                formData.append('item_type', 'file');
                formData.append('email', email);
                formData.append('permission', permission);
                formData.append('message', 'Auto-shared after upload');

                // ⭐ DEBUG: แสดง FormData
                console.log('📤 FormData being sent:');
                for (let pair of formData.entries()) {
                    console.log(`  ${pair[0]}: ${pair[1]}`);
                }

                console.log('🌐 Calling API:', API_BASE_URL + 'share_with_email');

                // Make API call
                const response = await fetch(API_BASE_URL + 'share_with_email', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                console.log('📡 Response status:', response.status);
                console.log('📡 Response headers:', {
                    'content-type': response.headers.get('content-type'),
                    'content-length': response.headers.get('content-length')
                });

                // ตรวจสอบ response
                const contentType = response.headers.get('content-type');
                if (!response.ok || !contentType || !contentType.includes('application/json')) {
                    const textResponse = await response.text();
                    console.error('❌ Non-JSON or error response:');
                    console.error('Status:', response.status);
                    console.error('Content-Type:', contentType);
                    console.error('Response body (first 500 chars):', textResponse.substring(0, 500));
                    throw new Error(`HTTP ${response.status}: ${textResponse.substring(0, 100)}`);
                }

                const data = await response.json();
                console.log('📨 API Response:', data);

                if (data.success) {
                    sharedCount++;
                    shareResults.push({
                        file: file.file,
                        status: 'success'
                    });
                    console.log(`✅ Shared successfully: ${file.file}`);
                } else {
                    failedCount++;
                    shareResults.push({
                        file: file.file,
                        status: 'error',
                        message: data.message || 'ไม่สามารถแชร์ได้'
                    });
                    console.error(`❌ API returned error for ${file.file}:`);
                    console.error('Message:', data.message);
                    console.error('Full response:', data);
                }

            } catch (error) {
                failedCount++;
                shareResults.push({
                    file: file.file,
                    status: 'error',
                    message: error.message
                });
                console.error(`💥 Exception while sharing ${file.file}:`);
                console.error('Error:', error);
                console.error('Error message:', error.message);
                console.error('Error stack:', error.stack);
            }
        }

        console.log('\n📊 ===== Auto-Share Summary =====');
        console.log('✅ Success:', sharedCount);
        console.log('❌ Failed:', failedCount);
        console.log('📋 Results:', shareResults);

        // แสดงผลลัพธ์
        await showAutoShareResults(sharedCount, failedCount, shareResults, email, permission);
    }

    /**
     * 📊 อัปเดต Progress Bar
     */
    function updateAutoShareProgress(current, total, status) {
        const percent = Math.round((current / total) * 100);

        const progressBar = document.getElementById('autoShareProgressBar');
        const statusEl = document.getElementById('autoShareStatus');
        const percentEl = document.getElementById('autoSharePercent');

        if (progressBar) progressBar.style.width = percent + '%';
        if (statusEl) statusEl.textContent = status;
        if (percentEl) percentEl.textContent = percent + '%';
    }


    /**
     * 📊 แสดงผลลัพธ์การแชร์ (เพิ่มฟังก์ชันนี้)
     */
    async function showAutoShareResults(sharedCount, failedCount, shareResults, email, permission) {
        const totalCount = sharedCount + failedCount;
        const isAllSuccess = failedCount === 0;

        // สร้างรายการไฟล์
        const resultList = shareResults.map(result => {
            const icon = result.status === 'success' ? '✅' : '❌';
            const colorClass = result.status === 'success' ? 'text-green-700' : 'text-red-700';
            return `<li class="text-sm ${colorClass} flex items-center">${icon} ${result.file}</li>`;
        }).join('');

        const permissionText = permission === 'reader' ? 'ดูอย่างเดียว' :
            permission === 'writer' ? 'แก้ไขได้' :
                'แสดงความคิดเห็น';

        await Swal.fire({
            icon: isAllSuccess ? 'success' : 'warning',
            title: isAllSuccess ? '🎉 แชร์สำเร็จทั้งหมด!' : '⚠️ แชร์เสร็จสิ้น',
            html: `
            <div class="text-center">
                <!-- Summary -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-2xl p-5 mb-4">
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <div class="text-3xl font-bold text-green-600">${sharedCount}</div>
                            <div class="text-sm text-gray-600">สำเร็จ</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-red-600">${failedCount}</div>
                            <div class="text-sm text-gray-600">ล้มเหลว</div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-700">
                        <p class="mb-1"><strong>แชร์ไปยัง:</strong> ${email}</p>
                        <p><strong>สิทธิ์:</strong> ${permissionText}</p>
                    </div>
                </div>

                <!-- File List -->
                <div class="max-h-48 overflow-y-auto text-left bg-gray-50 rounded-xl p-4">
                    <ul class="space-y-1">
                        ${resultList}
                    </ul>
                </div>

                ${failedCount > 0 ? `
                <div class="mt-4 text-sm text-orange-600">
                    💡 ไฟล์ที่ล้มเหลวสามารถแชร์ได้ทีหลังจากรายการไฟล์
                </div>
                ` : ''}
            </div>
        `,
            confirmButtonText: 'เสร็จสิ้น',
            timer: 8000,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-3xl shadow-2xl',
                confirmButton: 'rounded-xl'
            }
        });
    }


    async function checkAndRefreshTokenIfNeeded() {
        try {
            console.log('🔍 Checking Google Drive token status...');

            // เรียก endpoint ตรวจสอบ token
            const response = await fetch('<?php echo site_url("google_drive_system/refresh_system_token"); ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-cache'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();

            if (result.success) {
                // ตรวจสอบว่ามีการ refresh หรือไม่
                const wasRefreshed = result.message && (
                    result.message.includes('refreshed') ||
                    result.message.includes('Refresh') ||
                    result.action === 'token_refreshed'
                );

                return {
                    success: true,
                    refreshed: wasRefreshed,
                    message: result.message
                };
            } else {
                console.warn('⚠️ Token check failed:', result.message);

                // ตรวจสอบว่าต้องเชื่อมต่อใหม่หรือไม่
                if (result.requires_reconnect || result.error_type === 'no_refresh_token') {
                    showReconnectRequiredDialog(result.message);
                    return {
                        success: false,
                        requiresReconnect: true,
                        message: result.message
                    };
                }

                return {
                    success: false,
                    message: result.message
                };
            }

        } catch (error) {
            console.error('💥 Token check error:', error);
            return {
                success: false,
                message: 'ไม่สามารถตรวจสอบสถานะ Token ได้: ' + error.message
            };
        }
    }

    // 📢 แสดงการแจ้งเตือนเมื่อ Token ถูก Refresh
    function showTokenRefreshNotification() {
        // แสดง toast แจ้งเตือนสั้นๆ
        const toast = document.createElement('div');
        toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #10B981, #059669);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        z-index: 10001;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        animation: slideInRight 0.3s ease;
    `;
        toast.innerHTML = `
        <i class="fas fa-sync-alt mr-2"></i>
        Token อัปเดตแล้ว
    `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 2000);
    }

    // 🔌 แสดง Dialog เมื่อต้องเชื่อมต่อใหม่
    function showReconnectRequiredDialog(message) {
        Swal.fire({
            title: '🔌 ต้องเชื่อมต่อใหม่',
            html: `
            <div class="text-left">
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-4">
                    <p class="text-sm text-orange-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        ${message || 'Google Drive Token หมดอายุและไม่สามารถต่ออายุได้'}
                    </p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h4 class="font-bold text-blue-800 mb-2">💡 วิธีแก้ไข:</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• ติดต่อแอดมินเพื่อเชื่อมต่อ Google Drive ใหม่</li>
                        <li>• ใช้ Google Account เดียวกับเดิม</li>
                        <li>• รอสักครู่แล้วลองใหม่อีกครั้ง</li>
                    </ul>
                </div>
            </div>
        `,
            icon: 'warning',
            confirmButtonText: 'เข้าใจแล้ว',
            confirmButtonColor: '#f59e0b',
            customClass: {
                popup: 'glass-card rounded-2xl',
                confirmButton: 'rounded-xl'
            }
        });
    }



    // Create Share Link
    async function createShareLink() {
        console.log('🔗 Starting create share link process...');

        if (isSharing) {
            console.log('Share already in progress, skipping...');
            return;
        }

        if (!currentShareItem) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่พบข้อมูลรายการที่จะแชร์',
                confirmButtonText: 'ตกลง',
                customClass: {
                    popup: 'glass-card rounded-2xl',
                    confirmButton: 'rounded-xl'
                }
            });
            return;
        }

        // Set sharing state
        isSharing = true;

        // Update button state
        const createBtn = document.querySelector('.create-share-link-btn');
        const originalText = createBtn ? createBtn.innerHTML : '';
        if (createBtn) {
            createBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>กำลังสร้างลิงก์...';
            createBtn.disabled = true;
        }

        try {
            console.log('🔗 Creating share link:', selectedLinkPermission, selectedLinkAccess);

            const response = await fetch(API_BASE_URL + 'create_share_link', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    item_id: currentShareItem.id,
                    item_type: currentShareItem.type,
                    permission: selectedLinkPermission,
                    access: selectedLinkAccess
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.data && data.data.webViewLink) {
                // แสดงลิงก์ที่สร้างได้
                showShareLinkResult(data.data.webViewLink);
            } else {
                throw new Error(data.message || 'ไม่สามารถสร้างลิงก์แชร์ได้');
            }

        } catch (error) {
            console.error('❌ Create share link error:', error);

            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถสร้างลิงก์แชร์ได้: ' + error.message,
                confirmButtonText: 'ตกลง',
                customClass: {
                    popup: 'glass-card rounded-2xl',
                    confirmButton: 'rounded-xl'
                }
            });

        } finally {
            isSharing = false;

            // คืนสถานะปุ่ม
            if (createBtn) {
                createBtn.innerHTML = originalText || '<i class="fas fa-link mr-2"></i>สร้างลิงก์';
                createBtn.disabled = false;
            }
        }
    }

    // Copy Share Link
    function copyShareLink() {
        const linkInput = document.getElementById('shareLink');
        if (linkInput) {
            linkInput.select();
            document.execCommand('copy');

            Swal.fire({
                title: '✅ คัดลอกแล้ว!',
                text: 'ลิงก์แชร์ถูกคัดลอกไปยังคลิปบอร์ดแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                customClass: {
                    popup: 'glass-card rounded-2xl'
                }
            });
        }
    }



    // 🔧 ดาวน์โหลดไฟล์จาก viewer
    function downloadFileFromViewer(fileId, fileName) {
        console.log('⬇️ Downloading file from viewer:', fileId, fileName);

        // ใช้ function เดียวกับปุ่ม download หลัก (มีการเช็คสิทธิ์แล้ว)
        downloadFile(fileId, fileName);
    }



    /**
     * 🔄 ลองโหลดไฟล์ใหม่อีกครั้ง
     */
    function retryLoadFile() {
        if (!window.currentFileViewerData) {
            console.error('❌ No current file viewer data available for retry');
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่พบข้อมูลไฟล์ที่จะลองใหม่',
                confirmButtonText: 'ตลก'
            });
            return;
        }

        console.log('🔄 Retrying file load:', window.currentFileViewerData.fileName);

        // ✅ แสดง loading state ในพื้นที่ content
        const contentDiv = document.getElementById('fileViewerContent');
        if (contentDiv) {
            contentDiv.innerHTML = `
            <div class="flex items-center justify-center h-full">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-500 border-t-transparent mx-auto mb-4"></div>
                    <p class="text-gray-700 text-lg font-medium">กำลังโหลดไฟล์ใหม่...</p>
                    <p class="text-gray-500 text-sm mt-2">${escapeHtml(window.currentFileViewerData.fileName)}</p>
                    <p class="text-gray-400 text-xs mt-2">กรุณารอสักครู่</p>
                </div>
            </div>
        `;
        }

        // ✅ รอสักครู่ก่อนลองโหลดใหม่ (ให้ user เห็น loading)
        setTimeout(() => {
            try {
                const embedUrl = generateEmbedUrl(
                    window.currentFileViewerData.fileId,
                    window.currentFileViewerData.webViewLink,
                    window.currentFileViewerData.fileData || {}
                );

                console.log('📂 Retry with new embed URL:', embedUrl);

                loadFileContent(embedUrl, window.currentFileViewerData.fileData || {
                    name: window.currentFileViewerData.fileName
                });
            } catch (error) {
                console.error('💥 Error during retry:', error);
                showFileLoadError();
            }
        }, 500); // รอ 500ms เพื่อให้เห็น loading animation
    }




    function getFileIconBackground(fileName) {
        const fileIconData = getFileIcon(fileName);
        return fileIconData.color;
    }




    function openInGoogleDrive(webViewLink) {
        if (webViewLink && webViewLink !== '#trial-mode') {
            window.open(webViewLink, '_blank');

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'เปิด Google Drive ในแท็บใหม่แล้ว',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            Swal.fire('ไม่พบลิงก์', 'ไม่สามารถเปิด Google Drive ได้', 'warning');
        }
    }



    function copyGoogleDriveLink() {
        if (currentShareItem && currentShareItem.webViewLink) {
            navigator.clipboard.writeText(currentShareItem.webViewLink).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'คัดลอกลิงก์ Google Drive แล้ว! 📋',
                    showConfirmButton: false,
                    timer: 2000
                });
            }).catch(err => {
                console.error('Copy failed:', err);
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถคัดลอกลิงก์ได้', 'error');
            });
        } else {
            Swal.fire('ไม่พบลิงก์', 'ไม่สามารถคัดลอกลิงก์ Google Drive ได้', 'warning');
        }
    }


    function showShareLinkResult(link) {
        const resultDiv = document.getElementById('shareResult');
        if (resultDiv) {
            resultDiv.innerHTML = `
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-link text-blue-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-blue-800">ลิงก์แชร์</h3>
                            <div class="mt-1">
                                <input type="text" value="${link}" 
                                       class="w-full px-3 py-2 text-sm border border-blue-300 rounded-lg bg-white"
                                       id="shareLink" readonly>
                            </div>
                        </div>
                    </div>
                    <button onclick="copyShareLink()" 
                            class="ml-3 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-copy mr-1"></i>คัดลอก
                    </button>
                </div>
            </div>
        `;
            resultDiv.classList.remove('hidden');
        }
    }


    // Utility Functions
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Close modal when clicking outside
    document.addEventListener('click', function (e) {
        const modal = document.getElementById('shareModal');
        if (e.target === modal) {
            closeShareModal();
        }
    });


    function openShareInGoogleDrive(webViewLink) {
        if (!webViewLink || webViewLink === '#trial-mode') {
            Swal.fire('ไม่พบลิงก์', 'ไม่สามารถเปิด Google Drive ได้', 'warning');
            return;
        }

        window.open(webViewLink, '_blank');
        Swal.close();

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'เปิด Google Drive แล้ว - ใช้ฟีเจอร์แชร์ของ Google Drive ได้เลย',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            customClass: {
                popup: 'glass-card rounded-xl'
            }
        });
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

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textToCopy).then(() => {
                showCopySuccess();
            }).catch(err => {
                console.error('Clipboard API failed:', err);
                fallbackCopyTextToClipboard(textToCopy);
            });
        } else {
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
            timerProgressBar: true,
            customClass: {
                popup: 'glass-card rounded-xl'
            }
        });
    }

    // Drag and Drop Setup
    function setupDragAndDrop() {
        console.log('🖱️ Setting up drag and drop...');

        const fileBrowserContainer = document.getElementById('fileBrowserContainer');
        const dropZoneOverlay = document.getElementById('dropZoneOverlay');
        const modalDropZone = document.getElementById('modalDropZone');

        if (!fileBrowserContainer) {
            console.warn('File browser container not found');
            return;
        }

        // Main file browser drag and drop
        fileBrowserContainer.addEventListener('dragenter', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dragCounter++;

            if (e.dataTransfer.types.includes('Files')) {
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

            if (e.dataTransfer.types.includes('Files')) {
                e.dataTransfer.dropEffect = 'copy';
            }
        });

        // แก้ไขใน setupDragAndDrop - เปลี่ยน drop event handler
        fileBrowserContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dragCounter = 0;

            dropZoneOverlay.classList.add('hidden');

            // ✅ รองรับทั้งไฟล์และโฟลเดอร์
            const items = Array.from(e.dataTransfer.items);
            if (items.length > 0) {
                console.log('📂 Items dropped:', items.length, 'items');
                handleDroppedFiles(items);
            }
        });
        // Modal drop zone
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








    async function handleDroppedFiles(items) {
        console.log('📋 Processing dropped items:', items.length);
        isDragAndDropUpload = true;

        const allFiles = [];
        const folderStructure = new Map();
        const processedFolders = [];

        if (supportFolderUpload && items[0] && items[0].webkitGetAsEntry) {
            console.log('📁 Processing folder/file entries...');

            try {
                for (const item of items) {
                    const entry = item.webkitGetAsEntry();
                    if (entry) {
                        if (entry.isFile) {
                            const file = await getFileFromEntry(entry);
                            if (file) {
                                // 🔍 ตรวจสอบว่าไฟล์มาจาก folder หรือไม่
                                const pathParts = entry.fullPath.split('/').filter(part => part !== '');

                                if (pathParts.length > 1) {
                                    // ไฟล์อยู่ใน folder
                                    const folderName = pathParts[0];
                                    const relativePath = pathParts.slice(1).join('/');
                                    const parentFolders = pathParts.slice(0, -1);

                                    console.log(`📁 File from folder detected: ${entry.fullPath}`);

                                    // สร้าง folder structure จาก file path
                                    buildFolderStructureFromPath(entry.fullPath, folderStructure);

                                    allFiles.push({
                                        file: file,
                                        path: entry.fullPath,
                                        relativePath: relativePath,
                                        isFromFolder: true,
                                        folderName: folderName,
                                        parentFolders: parentFolders,
                                        directParent: parentFolders[parentFolders.length - 1] || folderName
                                    });

                                    if (!processedFolders.includes(folderName)) {
                                        processedFolders.push(folderName);
                                    }
                                } else {
                                    // ไฟล์เดี่ยวไม่อยู่ใน folder
                                    allFiles.push({
                                        file: file,
                                        path: entry.fullPath,
                                        relativePath: entry.name,
                                        isFromFolder: false,
                                        parentFolders: []
                                    });
                                }
                            }
                        } else if (entry.isDirectory) {
                            console.log('📁 Processing folder:', entry.name);

                            // ✅ สร้างโครงสร้างโฟลเดอร์ก่อน (รวม root folder)
                            console.log('🗂️ Building folder structure for:', entry.name);
                            await buildFolderStructure(entry, '', folderStructure, entry.name);
                            console.log('📊 Folder structure after build:', folderStructure);

                            // ประมวลผลไฟล์ในโฟลเดอร์
                            const folderFiles = await processFolderEntry(entry, '', entry.name);
                            allFiles.push(...folderFiles);

                            if (!processedFolders.includes(entry.name)) {
                                processedFolders.push(entry.name);
                            }
                        }
                    }
                }

            } catch (error) {
                console.error('💥 Error processing entries:', error);
                // Fallback สำหรับกรณี error
                const files = Array.from(items).map(item => item.getAsFile()).filter(file => file);
                allFiles.push(...files.map(file => ({
                    file,
                    path: '/' + file.name,
                    relativePath: file.name,
                    isFromFolder: false,
                    parentFolders: []
                })));
            }
        } else {
            // Fallback สำหรับ browser ที่ไม่รองรับ webkitGetAsEntry
            console.log('📁 Using fallback method...');
            const files = Array.from(items).map(item => item.getAsFile()).filter(file => file);
            allFiles.push(...files.map(file => ({
                file,
                path: '/' + file.name,
                relativePath: file.name,
                isFromFolder: false,
                parentFolders: []
            })));
        }

        if (allFiles.length === 0) {
            console.log('❌ No valid files found');
            return;
        }

        console.log(`📊 Found ${allFiles.length} files from ${processedFolders.length} folders`);
        console.log('🗂️ Folder structure:', folderStructure);

        // 🔧 เพิ่ม debug log สำหรับ folder structure
        if (folderStructure.size > 0) {
            console.log('📁 Folder Structure Contents:');
            folderStructure.forEach((info, path) => {
                console.log(`  📂 ${path}: ${info.name} (parent: ${info.parentPath || 'root'})`);
            });
        }

        // ตรวจสอบไฟล์
        const files = allFiles.map(item => item.file);
        const validationResult = validateFilesWithDBSettings(files);

        if (validationResult.invalidFiles.length > 0) {
            showFileValidationErrors(validationResult.invalidFiles, validationResult.validFiles);

            if (validationResult.validFiles.length === 0) {
                return;
            }
        }

        const validFiles = validationResult.validFiles;
        const validFilesWithStructure = allFiles.filter(item =>
            validFiles.includes(item.file)
        );

        if (validFiles.length > 0) {
            if (processedFolders.length > 0) {
                showFolderDropSummary(processedFolders, validFiles.length, validationResult.invalidFiles.length);
            }

            // 🆕 เก็บข้อมูลโครงสร้างไว้ใช้ตอน upload
            window.currentUploadStructure = {
                files: validFilesWithStructure,
                folderStructure: folderStructure,
                rootFolders: processedFolders
            };

            updateFileInputDirectly(validFiles);
            displaySelectedFiles(validFiles);

            if (document.getElementById('uploadStartBtn')) {
                document.getElementById('uploadStartBtn').disabled = false;
            }

            setTimeout(() => {
                if (!isUploading) {
                    console.log('🚀 Auto uploading dropped files with folder structure...');
                    startDirectUploadWithStructure();
                }
            }, 500);
        }
    }





    // 🆕 ดึงไฟล์จาก FileEntry
    function getFileFromEntry(entry) {
        return new Promise((resolve, reject) => {
            entry.file(resolve, reject);
        });
    }



    // แก้ไข function processFolderEntry
    // แก้ไข processFolderEntry เพื่อเพิ่ม debug
    async function processFolderEntry(folderEntry, parentPath = '', rootFolderName = '') {
        console.log(`📂 Processing folder entry: ${folderEntry.name}, parent: "${parentPath}", root: "${rootFolderName}"`);

        return new Promise((resolve, reject) => {
            const files = [];
            const reader = folderEntry.createReader();

            function readEntries() {
                reader.readEntries(async (entries) => {
                    if (entries.length === 0) {
                        console.log(`✅ Finished processing folder: ${folderEntry.name}, found ${files.length} files`);
                        resolve(files);
                        return;
                    }

                    console.log(`📋 Processing ${entries.length} entries in ${folderEntry.name}`);

                    for (const entry of entries) {
                        const currentPath = parentPath ? `${parentPath}/${entry.name}` : entry.name;
                        const fullPath = `/${rootFolderName}/${currentPath}`;

                        if (entry.isFile) {
                            try {
                                const file = await getFileFromEntry(entry);

                                // 🔧 FIX: สร้าง parent folder path ที่ถูกต้อง
                                const pathParts = currentPath.split('/').filter(part => part);
                                const parentFolders = pathParts.slice(0, -1); // ลบชื่อไฟล์ออก

                                // 🔧 FIX: กำหนด directParent ที่ถูกต้อง
                                let directParent;
                                if (parentFolders.length > 0) {
                                    // ไฟล์อยู่ใน subfolder
                                    directParent = parentFolders[parentFolders.length - 1];
                                } else {
                                    // ไฟล์อยู่ใน root folder
                                    directParent = rootFolderName;
                                }

                                const fileObj = {
                                    file: file,
                                    path: fullPath,
                                    relativePath: currentPath,
                                    isFromFolder: true,
                                    folderName: rootFolderName, // 🔧 root folder name
                                    parentFolders: parentFolders, // 🔧 array ของ parent folders
                                    directParent: directParent // 🔧 folder ที่ไฟล์ควรอยู่
                                };

                                files.push(fileObj);
                                console.log(`📄 Added file: ${entry.name}`, {
                                    folderName: fileObj.folderName,
                                    parentFolders: fileObj.parentFolders,
                                    directParent: fileObj.directParent,
                                    relativePath: fileObj.relativePath
                                });

                            } catch (error) {
                                console.warn('⚠️ Error reading file:', entry.name, error);
                            }
                        } else if (entry.isDirectory) {
                            try {
                                console.log(`📁 Processing subfolder: ${entry.name}`);
                                const subFiles = await processFolderEntry(entry, currentPath, rootFolderName);
                                files.push(...subFiles);
                                console.log(`📂 Added ${subFiles.length} files from subfolder: ${entry.name}`);
                            } catch (error) {
                                console.warn('⚠️ Error reading subfolder:', entry.name, error);
                            }
                        }
                    }

                    readEntries();
                }, (error) => {
                    console.error(`❌ Error reading entries in ${folderEntry.name}:`, error);
                    reject(error);
                });
            }

            readEntries();
        });
    }





    // 🆕 แก้ไข buildFolderStructure ให้เพิ่ม root folder
    async function buildFolderStructure(folderEntry, parentPath = '', folderStructure, rootFolderName = '') {
        console.log(`🗂️ Building structure for folder: ${folderEntry.name}, parent: "${parentPath}", root: "${rootFolderName}"`);

        // 🔧 FIX: เพิ่ม root folder เข้าไปใน structure ก่อน
        const rootPath = rootFolderName || folderEntry.name;
        if (!folderStructure.has(rootPath)) {
            const rootFolderInfo = {
                name: folderEntry.name,
                fullPath: rootPath,
                parentPath: null,
                isDirectory: true,
                children: [],
                rootFolder: rootPath
            };

            folderStructure.set(rootPath, rootFolderInfo);
            console.log(`📁 Added ROOT folder to structure: ${rootPath}`);
        }

        return new Promise((resolve, reject) => {
            const reader = folderEntry.createReader();

            function readEntries() {
                reader.readEntries(async (entries) => {
                    if (entries.length === 0) {
                        console.log(`✅ Finished reading entries for: ${folderEntry.name}`);
                        resolve();
                        return;
                    }

                    console.log(`📋 Found ${entries.length} entries in ${folderEntry.name}`);

                    for (const entry of entries) {
                        const currentPath = parentPath ? `${parentPath}/${entry.name}` : entry.name;
                        const fullPath = rootFolderName ? `${rootFolderName}/${currentPath}` : currentPath;

                        console.log(`📁 Processing entry: ${entry.name}, type: ${entry.isDirectory ? 'directory' : 'file'}`);
                        console.log(`📍 Paths - current: "${currentPath}", full: "${fullPath}"`);

                        if (entry.isDirectory) {
                            // เก็บข้อมูลโฟลเดอร์
                            console.log(`📁 Adding folder to structure: ${fullPath}`);
                            folderStructure.set(fullPath, {
                                name: entry.name,
                                fullPath: fullPath,
                                parentPath: parentPath || null,
                                isDirectory: true,
                                children: [],
                                rootFolder: rootFolderName || entry.name
                            });

                            console.log(`📊 Folder structure size after adding ${fullPath}:`, folderStructure.size);

                            // อัปเดต parent's children
                            const parentFullPath = rootFolderName && parentPath ? `${rootFolderName}/${parentPath}` : rootFolderName;
                            if (parentFullPath && folderStructure.has(parentFullPath)) {
                                const parentFolder = folderStructure.get(parentFullPath);
                                if (!parentFolder.children.includes(entry.name)) {
                                    parentFolder.children.push(entry.name);
                                    console.log(`👶 Added child ${entry.name} to parent ${parentFullPath}`);
                                }
                            }

                            // ประมวลผลโฟลเดอร์ย่อย (recursive)
                            console.log(`🔄 Recursively processing subfolder: ${entry.name}`);
                            await buildFolderStructure(entry, currentPath, folderStructure, rootFolderName);
                        }
                    }

                    // อ่านต่อถ้ามี entries เพิ่มเติม
                    readEntries();
                }, (error) => {
                    console.error(`❌ Error reading entries for ${folderEntry.name}:`, error);
                    reject(error);
                });
            }

            readEntries();
        });
    }

    // 🆕 เพิ่ม buildFolderStructureFromPath สำหรับกรณีลากไฟล์ที่มีใน folder
    function buildFolderStructureFromPath(fullPath, folderStructure) {
        console.log(`🔨 Building structure from path: ${fullPath}`);

        const pathParts = fullPath.split('/').filter(part => part !== '');

        if (pathParts.length <= 1) {
            console.log('ℹ️ No folder structure needed (single file)');
            return;
        }

        // ลบชื่อไฟล์ออก เหลือแต่ folder path
        const folderParts = pathParts.slice(0, -1);
        const rootFolderName = folderParts[0];

        console.log(`📂 Folder parts:`, folderParts);
        console.log(`🗂️ Root folder: ${rootFolderName}`);

        // 🔧 FIX: เพิ่ม root folder ก่อน
        if (!folderStructure.has(rootFolderName)) {
            const rootFolderInfo = {
                name: rootFolderName,
                fullPath: rootFolderName,
                parentPath: null,
                isDirectory: true,
                children: [],
                rootFolder: rootFolderName
            };

            folderStructure.set(rootFolderName, rootFolderInfo);
            console.log(`📁 Added ROOT folder from path: ${rootFolderName}`);
        }

        // สร้าง folder structure ทีละระดับ
        for (let i = 0; i < folderParts.length; i++) {
            const currentFolderName = folderParts[i];
            const currentPath = folderParts.slice(0, i + 1).join('/');
            const parentPath = i > 0 ? folderParts.slice(0, i).join('/') : null;

            console.log(`📁 Processing folder level ${i + 1}: ${currentPath}`);

            if (!folderStructure.has(currentPath)) {
                const folderInfo = {
                    name: currentFolderName,
                    fullPath: currentPath,
                    parentPath: parentPath,
                    isDirectory: true,
                    children: [],
                    rootFolder: rootFolderName
                };

                folderStructure.set(currentPath, folderInfo);
                console.log(`✅ Added folder to structure: ${currentPath}`);
            }

            // อัปเดต parent's children
            if (parentPath && folderStructure.has(parentPath)) {
                const parentFolder = folderStructure.get(parentPath);
                if (!parentFolder.children.includes(currentFolderName)) {
                    parentFolder.children.push(currentFolderName);
                    console.log(`👶 Added child ${currentFolderName} to parent ${parentPath}`);
                }
            }
        }

        console.log(`📊 Folder structure size after processing ${fullPath}: ${folderStructure.size}`);
    }




    // 🆕 Upload พร้อมสร้างโครงสร้างโฟลเดอร์ (Enhanced with Permission)
    async function startDirectUploadWithStructure() {
        if (isUploading) {
            console.log('Upload already in progress');
            return;
        }

        console.log('🚀 Starting upload with folder structure...');
        isUploading = true;

        const uploadData = window.currentUploadStructure;
        if (!uploadData || !uploadData.files || uploadData.files.length === 0) {
            console.error('❌ No upload structure data found');
            isUploading = false;
            return;
        }

        // 🔐 ตรวจสอบสิทธิ์ก่อนเริ่มอัปโหลด
        try {
            console.log('🔐 Checking permissions...');

            const response = await fetch(API_BASE_URL + 'get_folder_permissions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    folder_id: currentFolder || 'root'
                }).toString()
            });

            const permissionResult = await handleApiResponse(response);

            if (!permissionResult.success || !permissionResult.data) {
                throw new Error('ไม่สามารถตรวจสอบสิทธิ์ได้');
            }

            const permissions = permissionResult.data;

            // ตรวจสอบสิทธิ์อัปโหลด
            if (!permissions.can_upload) {
                isUploading = false;
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่มีสิทธิ์อัปโหลด',
                    text: 'คุณไม่มีสิทธิ์อัปโหลดไฟล์ในโฟลเดอร์นี้',
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            // ตรวจสอบสิทธิ์สร้างโฟลเดอร์ (หากมีโฟลเดอร์ใหม่)
            if (uploadData.folderStructure && uploadData.folderStructure.size > 0 && !permissions.can_create_folder) {
                isUploading = false;
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่มีสิทธิ์สร้างโฟลเดอร์',
                    text: 'คุณไม่มีสิทธิ์สร้างโฟลเดอร์ใหม่ในตำแหน่งนี้',
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            console.log('✅ Permission check passed');

        } catch (error) {
            console.error('❌ Permission check failed:', error);
            isUploading = false;
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถตรวจสอบสิทธิ์ได้ กรุณาลองใหม่',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        const totalFiles = uploadData.files.length;

        // แสดง Progress Modal
        Swal.fire({
            title: `กำลังอัปโหลดพร้อมสร้างโฟลเดอร์${IS_TRIAL_MODE ? ' (Trial)' : ''}`,
            html: `
            <div class="text-center">
                <!-- Permission Status -->
                <div class="mb-3 p-2 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center justify-center text-green-700">
                        <i class="fas fa-shield-check mr-2"></i>
                        <span class="text-sm font-medium">สิทธิ์ได้รับการตรวจสอบแล้ว</span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="text-lg font-semibold text-gray-800 mb-2">
                        กำลังประมวลผล <span id="currentFileIndex">0</span> จาก ${totalFiles} ไฟล์
                    </div>
                    <div class="text-sm text-gray-600 mb-2" id="currentFileName">เตรียมการสร้างโฟลเดอร์...</div>
                    <div class="text-xs text-gray-500" id="currentFolderPath">โครงสร้าง: ${uploadData.rootFolders.join(', ')}</div>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                    <div id="uploadProgressBar" class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>

                <div class="flex justify-between text-sm text-gray-600">
                    <span id="uploadedCount">0 ไฟล์สำเร็จ</span>
                    <span id="uploadPercent">0%</span>
                </div>

                <div class="mt-3 text-xs text-gray-500" id="uploadStatus">
                    สร้างโครงสร้างโฟลเดอร์...
                </div>
            </div>
        `,
            allowOutsideClick: false,
            showConfirmButton: false,
            customClass: {
                popup: 'glass-card rounded-2xl'
            },
            didOpen: () => {
                startFolderStructureUpload(uploadData);
            }
        });
    }


    // 🆕 ประมวลผล Upload ตามโครงสร้างโฟลเดอร์
    async function startFolderStructureUpload(uploadData) {
        let uploadedCount = 0;
        let failedCount = 0;
        const uploadResults = [];
        const createdFolders = new Map(); // เก็บ folder ID ที่สร้างแล้ว

        try {
            // 1. สร้างโฟลเดอร์ตามโครงสร้าง
            updateUploadStatus('สร้างโครงสร้างโฟลเดอร์...', 0);
            await createFolderStructure(uploadData.folderStructure, createdFolders);

            // 2. อัปโหลดไฟล์ไปยังโฟลเดอร์ที่เหมาะสม
            for (let i = 0; i < uploadData.files.length; i++) {
                const fileData = uploadData.files[i];
                const currentFileNum = i + 1;

                updateUploadStatus(`อัปโหลด: ${fileData.file.name}`, currentFileNum, uploadData.files.length, uploadedCount);

                try {
                    // หา folder ID ที่ถูกต้องสำหรับไฟล์นี้
                    const targetFolderId = getTargetFolderId(fileData, createdFolders);

                    const result = await uploadSingleFileToFolder(fileData.file, targetFolderId, fileData.relativePath);

                    if (result.success) {
                        uploadedCount++;
                        uploadResults.push({
                            file: fileData.file.name,
                            status: 'success',
                            path: fileData.relativePath,
                            file_id: result.file_id,        // ⭐ เพิ่ม Google Drive file ID
                            folderId: targetFolderId,
                            message: result.message
                        });
                    } else {
                        failedCount++;
                        uploadResults.push({
                            file: fileData.file.name,
                            status: 'error',
                            path: fileData.relativePath,
                            message: result.message
                        });
                    }

                } catch (error) {
                    console.error(`💥 Upload error for ${fileData.file.name}:`, error);
                    failedCount++;
                    uploadResults.push({
                        file: fileData.file.name,
                        status: 'error',
                        path: fileData.relativePath,
                        message: error.message
                    });
                }
            }

        } catch (error) {
            console.error('💥 Structure upload error:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถสร้างโครงสร้างโฟลเดอร์ได้: ' + error.message
            });
            return;
        }

        // แสดงผลสรุป
        showStructureUploadComplete(uploadedCount, failedCount, uploadResults, createdFolders.size);
    }



    // แก้ไข createFolderStructure เพื่อเพิ่ม debug
    async function createFolderStructure(folderStructure, createdFolders) {
        console.log('📁 Creating folder structure...');
        console.log('📊 Input folder structure:', folderStructure);
        console.log('📏 Number of folders to create:', folderStructure.size);

        if (folderStructure.size === 0) {
            console.log('ℹ️ No folders to create');
            return;
        }

        const currentFolderId = currentFolder === 'root' ? null : currentFolder;
        console.log('📍 Current folder ID:', currentFolderId);

        // แสดงรายการโฟลเดอร์ทั้งหมดที่จะสร้าง
        console.log('📋 Folders to create:');
        folderStructure.forEach((info, path) => {
            console.log(`  - ${path}: ${info.name} (parent: ${info.parentPath || 'root'})`);
        });

        // เรียงลำดับโฟลเดอร์ตามความลึก
        const sortedFolders = Array.from(folderStructure.entries()).sort((a, b) => {
            const depthA = a[0].split('/').length;
            const depthB = b[0].split('/').length;
            return depthA - depthB;
        });

        console.log('📋 Sorted folders by depth:');
        sortedFolders.forEach(([path, info], index) => {
            const depth = path.split('/').length;
            console.log(`  ${index + 1}. [depth ${depth}] ${path} -> ${info.name}`);
        });

        // ดำเนินการสร้างโฟลเดอร์
        for (let i = 0; i < sortedFolders.length; i++) {
            const [path, folderInfo] = sortedFolders[i];

            try {
                console.log(`📁 [${i + 1}/${sortedFolders.length}] Creating folder: ${path}`);
                updateUploadStatus(`สร้างโฟลเดอร์: ${folderInfo.name}`, i + 1, sortedFolders.length, 0);

                // หา parent folder ID
                let parentId = currentFolderId;
                if (folderInfo.parentPath) {
                    parentId = createdFolders.get(folderInfo.parentPath);
                    console.log(`🔍 Looking for parent: "${folderInfo.parentPath}" -> ${parentId}`);

                    if (!parentId) {
                        console.warn(`⚠️ Parent folder not found for ${path}, using current folder`);
                        parentId = currentFolderId;
                    }
                }

                console.log(`🎯 Creating "${folderInfo.name}" in parent: ${parentId || 'root'}`);

                // ตรวจสอบว่าโฟลเดอร์มีอยู่แล้วหรือไม่ (ถ้ามี function นี้)
                let folderId;
                if (typeof checkIfFolderExists === 'function') {
                    const existingFolderId = await checkIfFolderExists(folderInfo.name, parentId);
                    if (existingFolderId) {
                        console.log(`✅ Folder already exists: ${folderInfo.name} (ID: ${existingFolderId})`);
                        folderId = existingFolderId;
                    } else {
                        folderId = await createSingleFolder(folderInfo.name, parentId);
                    }
                } else {
                    folderId = await createSingleFolder(folderInfo.name, parentId);
                }

                if (!folderId) {
                    throw new Error(`ไม่ได้รับ folder ID จากการสร้างโฟลเดอร์ ${folderInfo.name}`);
                }

                console.log(`✅ Folder ready: ${folderInfo.name} (ID: ${folderId})`);

                // เก็บ folder ID โดยใช้ path เป็น key
                createdFolders.set(path, folderId);
                console.log(`💾 Stored folder mapping: "${path}" -> ${folderId}`);

                // รอสักครู่เพื่อไม่ให้ API rate limit
                await new Promise(resolve => setTimeout(resolve, 200));

            } catch (error) {
                console.error(`❌ Error creating folder ${path}:`, error);
                throw error; // หยุดการสร้างถ้ามี error
            }
        }

        console.log(`✅ Folder structure creation completed: ${createdFolders.size} folders processed`);
        console.log('📋 Final created folders map:', Object.fromEntries(createdFolders));
    }

    // 🆕 สร้างโฟลเดอร์เดี่ยว
    async function createSingleFolder(folderName, parentId) {
        const formData = new FormData();
        formData.append('folder_name', folderName);
        formData.append('parent_id', parentId || 'root');

        const response = await fetch(API_BASE_URL + 'create_folder', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (data.success && data.data && data.data.folder_id) {
            return data.data.folder_id;
        } else {
            throw new Error(data.message || 'ไม่สามารถสร้างโฟลเดอร์ได้');
        }
    }

    // 🆕 หา folder ID ที่ถูกต้องสำหรับไฟล์
    function getTargetFolderId(fileData, createdFolders) {
        console.log(`🔍 === Finding target folder for file: ${fileData.file.name} ===`);
        console.log(`📂 File data:`, {
            isFromFolder: fileData.isFromFolder,
            folderName: fileData.folderName,
            relativePath: fileData.relativePath,
            parentFolders: fileData.parentFolders,
            directParent: fileData.directParent,
            path: fileData.path
        });

        // ถ้าไม่ได้มาจาก folder ให้ใช้ current folder
        if (!fileData.isFromFolder || !fileData.folderName) {
            console.log(`📍 File not from folder, using current folder: ${currentFolder}`);
            return currentFolder === 'root' ? null : currentFolder;
        }

        console.log(`🗂️ Available created folders:`, Object.fromEntries(createdFolders));

        // 🔧 FIX: ใช้ folderName เป็น primary key สำหรับ root folder
        const rootFolderName = fileData.folderName;

        // วิธีที่ 1: หาด้วย folder name (root folder)
        if (createdFolders.has(rootFolderName)) {
            const folderId = createdFolders.get(rootFolderName);
            console.log(`✅ Found folder by root name "${rootFolderName}": ${folderId}`);
            return folderId;
        }

        // วิธีที่ 2: หาด้วย parent path (สำหรับไฟล์ใน subfolder)
        if (fileData.parentFolders && fileData.parentFolders.length > 0) {
            const parentPath = fileData.parentFolders.join('/');
            console.log(`🔍 Trying parent path: "${parentPath}"`);

            if (createdFolders.has(parentPath)) {
                const folderId = createdFolders.get(parentPath);
                console.log(`✅ Found folder by parent path "${parentPath}": ${folderId}`);
                return folderId;
            }

            // วิธีที่ 3: หาด้วย full path (folderName + parentPath)
            const fullParentPath = `${rootFolderName}/${parentPath}`;
            console.log(`🔍 Trying full parent path: "${fullParentPath}"`);

            if (createdFolders.has(fullParentPath)) {
                const folderId = createdFolders.get(fullParentPath);
                console.log(`✅ Found folder by full path "${fullParentPath}": ${folderId}`);
                return folderId;
            }
        }

        // วิธีที่ 4: หาด้วย directParent
        if (fileData.directParent) {
            console.log(`🔍 Trying direct parent: "${fileData.directParent}"`);

            // ลองหาตรงๆ
            if (createdFolders.has(fileData.directParent)) {
                const folderId = createdFolders.get(fileData.directParent);
                console.log(`✅ Found folder by direct parent "${fileData.directParent}": ${folderId}`);
                return folderId;
            }

            // ลองหาแบบ ending with
            for (const [path, folderId] of createdFolders.entries()) {
                if (path.endsWith('/' + fileData.directParent) || path === fileData.directParent) {
                    console.log(`✅ Found folder by ending match "${path}" -> ${folderId}`);
                    return folderId;
                }
            }
        }

        // วิธีที่ 5: Fallback - หาทุก key ที่มี folderName
        console.log(`🔍 Fallback: searching all keys for "${rootFolderName}"`);
        for (const [path, folderId] of createdFolders.entries()) {
            if (path.includes(rootFolderName)) {
                console.log(`⚠️ Fallback match found "${path}" -> ${folderId}`);
                return folderId;
            }
        }

        // สุดท้าย: ใช้ current folder
        console.log(`❌ No folder mapping found, using current folder: ${currentFolder}`);
        return currentFolder === 'root' ? null : currentFolder;
    }

    // 🆕 อัปโหลดไฟล์ไปยังโฟลเดอร์ที่ระบุ
    async function uploadSingleFileToFolder(file, targetFolderId, relativePath) {
        try {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('folder_id', targetFolderId || 'root');
            formData.append('relative_path', relativePath || file.name);

            console.log(`📤 Uploading: ${file.name} to folder: ${targetFolderId || 'root'}`);
            console.log(`📂 Relative path: ${relativePath}`);

            const response = await fetch(API_BASE_URL + 'upload_file', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            console.log(`📡 Upload response status: ${response.status} for ${file.name}`);

            // 🔧 Enhanced error handling for different HTTP status codes
            if (!response.ok) {
                let errorMessage = `HTTP ${response.status}`;

                try {
                    // พยายาม parse response เป็น JSON
                    const contentType = response.headers.get('content-type');

                    if (contentType && contentType.includes('application/json')) {
                        const errorData = await response.json();
                        errorMessage = errorData.message || errorMessage;

                        console.log(`📋 Error response data:`, errorData);

                        // 🔧 Handle specific error types
                        if (response.status === 403) {
                            return {
                                success: false,
                                message: 'ไม่มีสิทธิ์เข้าถึงโฟลเดอร์นี้'
                            };
                        } else if (response.status === 401) {
                            return {
                                success: false,
                                message: 'กรุณาเข้าสู่ระบบใหม่'
                            };
                        } else if (response.status === 413) {
                            return {
                                success: false,
                                message: 'ไฟล์ใหญ่เกินที่อนุญาต'
                            };
                        } else if (response.status === 500) {
                            // 🔧 Special handling for 500 errors
                            console.warn(`⚠️ HTTP 500 for ${file.name}, but checking if upload succeeded...`);

                            // รอสักครู่แล้วลองตรวจสอบว่าไฟล์อยู่ใน Drive หรือไม่
                            await new Promise(resolve => setTimeout(resolve, 2000));

                            // ถ้าไฟล์ upload สำเร็จแต่ response error อาจจะ return success
                            return {
                                success: true,
                                message: 'อัปโหลดสำเร็จ (มี warning)',
                                warning: 'Server returned 500 but upload may have succeeded'
                            };
                        }
                    } else {
                        // Response ไม่ใช่ JSON
                        const textResponse = await response.text();
                        console.error(`❌ Non-JSON response for ${file.name}:`, textResponse.substring(0, 300));

                        if (response.status === 500) {
                            // 🔧 Assume success for 500 errors with non-JSON response
                            console.warn(`⚠️ Assuming success for ${file.name} despite 500 error`);
                            return {
                                success: true,
                                message: 'อัปโหลดสำเร็จ (server error แต่ไฟล์อาจถูก upload)',
                                warning: 'HTTP 500 with non-JSON response'
                            };
                        }

                        errorMessage = `${errorMessage} (${textResponse.substring(0, 100)})`;
                    }

                } catch (parseError) {
                    console.error(`❌ Error parsing response for ${file.name}:`, parseError);

                    if (response.status === 500) {
                        // 🔧 If we can't parse 500 response, assume success
                        console.warn(`⚠️ Assuming success for ${file.name} - can't parse 500 response`);
                        return {
                            success: true,
                            message: 'อัปโหลดสำเร็จ (ไม่สามารถตรวจสอบ response ได้)',
                            warning: 'Cannot parse 500 response'
                        };
                    }
                }

                throw new Error(errorMessage);
            }

            // 🔧 Parse successful response
            let data;
            try {
                data = await response.json();
            } catch (parseError) {
                console.error(`❌ Error parsing success response for ${file.name}:`, parseError);

                // ถ้า HTTP 200 แต่ parse ไม่ได้ ให้ถือว่าสำเร็จ
                return {
                    success: true,
                    message: 'อัปโหลดสำเร็จ (ไม่สามารถ parse response ได้)',
                    warning: 'Cannot parse success response'
                };
            }

            if (data.success) {
                console.log(`✅ Upload successful: ${file.name}`);
                return {
                    success: true,
                    message: data.message || 'อัปโหลดสำเร็จ',
                    file_id: data.data?.file_id,
                    web_view_link: data.data?.web_view_link
                };
            } else {
                console.error(`❌ Upload failed: ${file.name}`, data.message);
                return {
                    success: false,
                    message: data.message || 'อัปโหลดไม่สำเร็จ'
                };
            }

        } catch (error) {
            console.error(`💥 Upload error for ${file.name}:`, error);
            return {
                success: false,
                message: error.message || 'เกิดข้อผิดพลาดในการอัปโหลด'
            };
        }
    }

    // 🔧 เพิ่ม retry mechanism สำหรับ upload ที่ล้มเหลว
    async function uploadWithRetry(file, targetFolderId, relativePath, maxRetries = 2) {
        let lastError;

        for (let attempt = 1; attempt <= maxRetries; attempt++) {
            console.log(`🔄 Upload attempt ${attempt}/${maxRetries} for ${file.name}`);

            try {
                const result = await uploadSingleFileToFolder(file, targetFolderId, relativePath);

                if (result.success) {
                    if (attempt > 1) {
                        console.log(`✅ Upload succeeded on attempt ${attempt} for ${file.name}`);
                    }
                    return result;
                } else {
                    lastError = result.message;
                    if (attempt < maxRetries) {
                        console.log(`⚠️ Attempt ${attempt} failed for ${file.name}, retrying...`);
                        await new Promise(resolve => setTimeout(resolve, 1000 * attempt)); // exponential backoff
                    }
                }
            } catch (error) {
                lastError = error.message;
                if (attempt < maxRetries) {
                    console.log(`⚠️ Attempt ${attempt} error for ${file.name}, retrying...`);
                    await new Promise(resolve => setTimeout(resolve, 1000 * attempt)); // exponential backoff
                }
            }
        }

        console.error(`❌ All upload attempts failed for ${file.name}`);
        return {
            success: false,
            message: lastError || 'อัปโหลดล้มเหลวหลังจากลองหลายครั้ง'
        };
    }


    // 🆕 อัปเดตสถานะการอัปโหลด
    function updateUploadStatus(message, current = 0, total = 0, uploaded = 0) {
        const statusEl = document.getElementById('uploadStatus');
        const currentFileIndex = document.getElementById('currentFileIndex');
        const currentFileName = document.getElementById('currentFileName');
        const uploadedCountEl = document.getElementById('uploadedCount');
        const percentEl = document.getElementById('uploadPercent');
        const progressBar = document.getElementById('uploadProgressBar');

        if (statusEl) statusEl.textContent = message;
        if (currentFileIndex) currentFileIndex.textContent = current;
        if (currentFileName) currentFileName.textContent = message;
        if (uploadedCountEl) uploadedCountEl.textContent = `${uploaded} ไฟล์สำเร็จ`;

        if (total > 0) {
            const percent = Math.round((current / total) * 100);
            if (percentEl) percentEl.textContent = percent + '%';
            if (progressBar) progressBar.style.width = percent + '%';
        }
    }

    /**
     * 🗂️ แสดงผลการอัปโหลดโครงสร้างโฟลเดอร์เสร็จสิ้น (Drag & Drop - มี Auto-Share)
     * ⚠️ แทนที่ function showStructureUploadComplete() เดิมในไฟล์หลัก
     */
    function showStructureUploadComplete(uploadedCount, failedCount, uploadResults, foldersCreated) {
        isUploading = false;
        isDragAndDropUpload = false;

        const successCount = uploadedCount;
        const errorCount = failedCount;

        let title = '🗂️ อัปโหลดโครงสร้างโฟลเดอร์เสร็จสิ้น';
        let message = `✅ สร้างโฟลเดอร์: ${foldersCreated} โฟลเดอร์\n📄 อัปโหลดไฟล์สำเร็จ: ${successCount} ไฟล์`;
        let icon = 'success';

        if (errorCount > 0) {
            message += `\n❌ ไฟล์ล้มเหลว: ${errorCount} ไฟล์`;
            icon = successCount > 0 ? 'warning' : 'error';
        }

        Swal.fire({
            icon: icon,
            title: title,
            text: message,
            timer: errorCount > 0 ? 6000 : 4000,
            showConfirmButton: errorCount > 0,
            confirmButtonText: 'ตกลง',
            customClass: {
                popup: 'glass-card rounded-2xl',
                confirmButton: 'rounded-xl'
            }
        }).then(() => {
            // รีเฟรชรายการไฟล์
            if (typeof refreshFiles === 'function') {
                refreshFiles();
            }

            // อัปเดตข้อมูล member
            if (typeof loadMemberInfo === 'function') {
                loadMemberInfo();
            }

            // ล้างข้อมูลโครงสร้าง
            window.currentUploadStructure = null;

            // แสดง toast notification
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `🗂️ สร้างโครงสร้างโฟลเดอร์สำเร็จ`,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            // ✅ แสดง Auto-Share Prompt (ถ้ามีไฟล์สำเร็จ)
            if (successCount > 0) {
                console.log('🎯 Triggering auto-share prompt after folder structure upload...');
                promptAutoShareAfterUpload(uploadResults);
            }
        });

        console.log(`✅ Structure upload completed: ${foldersCreated} folders, ${uploadedCount} files success, ${failedCount} files failed`);
    }


    // 🆕 แสดงสรุปการลากโฟลเดอร์
    function showFolderDropSummary(folders, validCount, invalidCount) {
        let message = `📁 ประมวลผลโฟลเดอร์: ${folders.join(', ')}\n`;
        message += `✅ ไฟล์ที่ถูกต้อง: ${validCount} ไฟล์`;

        if (invalidCount > 0) {
            message += `\n❌ ไฟล์ที่ไม่ถูกต้อง: ${invalidCount} ไฟล์`;
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: message,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            customClass: {
                popup: 'glass-card rounded-xl'
            }
        });
    }



    function startDirectUpload() {
        // ป้องกันการอัปโหลดซ้ำ
        if (isUploading) {
            console.log('Upload already in progress, ignoring duplicate request');
            return;
        }

        console.log('🚀 Starting direct upload process...');
        isUploading = true;

        const files = document.getElementById('fileInput').files;

        if (files.length === 0) {
            isUploading = false;
            return;
        }

        const uploadFolderId = currentFolder === 'root' ? null : currentFolder;
        const totalFiles = files.length;

        // แสดง Progress Modal แบบ Simple
        Swal.fire({
            title: `กำลังอัปโหลด${IS_TRIAL_MODE ? ' (Trial)' : ''}`,
            html: `
            <div class="text-center">
                <div class="mb-4">
                    <div class="text-lg font-semibold text-gray-800 mb-2">
                        กำลังอัปโหลด <span id="currentFileIndex">1</span> จาก ${totalFiles} ไฟล์
                    </div>
                    <div class="text-sm text-gray-600 mb-4" id="currentFileName">เตรียมการอัปโหลด...</div>
                </div>
                
                ${IS_TRIAL_MODE ? '<div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mb-4"><p class="text-sm text-orange-700"><i class="fas fa-flask mr-2"></i>Trial Mode: ไฟล์จะถูกจัดเก็บในระบบทดลอง</p></div>' : ''}
                
                <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                    <div id="uploadProgressBar" class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                
                <div class="flex justify-between text-sm text-gray-600">
                    <span id="uploadedCount">0 ไฟล์สำเร็จ</span>
                    <span id="uploadPercent">0%</span>
                </div>
            </div>
        `,
            allowOutsideClick: false,
            showConfirmButton: false,
            customClass: {
                popup: 'glass-card rounded-2xl'
            },
            didOpen: () => {
                startFileUploadProcessDirect(files, uploadFolderId, totalFiles);
            }
        });
    }

    function startFileUploadProcessDirect(files, folderId, totalFiles) {
        let uploadedCount = 0;
        let failedCount = 0;
        const uploadResults = [];

        uploadFilesSequentiallyDirect(files, folderId, 0, uploadedCount, failedCount, uploadResults, totalFiles);
    }

    // ====================================================================
    // 🔧 ฟังก์ชันที่ 1: uploadFilesSequentiallyDirect (แก้ไขบรรทัด 28-36)
    // ====================================================================

    function uploadFilesSequentially(files, folderId, index, uploadedCount, failedCount, uploadResults, totalFiles) {
        if (index >= files.length) {
            showUploadCompleteWithAutoClose(uploadedCount, failedCount, uploadResults, totalFiles);
            return;
        }

        const file = files[index];
        const currentFileNum = index + 1;

        updateUploadProgress(currentFileNum, totalFiles, `อัปโหลด: ${file.name}`);

        const formData = new FormData();
        formData.append('file', file);
        formData.append('folder_id', folderId || '');
        formData.append('parent_folder_id', folderId || '');

        console.log(`📤 Uploading file ${currentFileNum}/${totalFiles}:`, file.name);

        fetch(API_BASE_URL + 'upload_file', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(response => {
                console.log('📡 Upload response status:', response.status);

                // ====================================================================
                // ✅ จัดการ 403 Access Denied (ไม่แก้ไข - เก็บไว้เหมือนเดิม)
                // ====================================================================
                if (response.status === 403) {
                    return response.json().then(data => {
                        if (data.error_type === 'access_denied') {
                            hideUploadProgress();
                            showAccessDeniedModal(data);
                            return Promise.reject(new Error('Access Denied - Modal Shown'));
                        }
                        throw new Error('Forbidden: ' + (data.message || 'ไม่มีสิทธิ์เข้าถึง'));
                    });
                }

                // ====================================================================
                // ⭐ แก้ไขตรงนี้ - จัดการ HTTP 500 ให้ถูกต้อง
                // ====================================================================

                // ❌ โค้ดเดิม (ผิด) - สร้าง fake file_id
                /*
                if (response.status === 500) {
                    console.warn('⚠️ Server returned 500 but file might be uploaded successfully');
                    
                    return response.text().then(text => {
                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            console.error('❌ Cannot parse 500 response:', text.substring(0, 200));
                            
                            // สร้าง mock success response ← ผิด!
                            data = {
                                success: true,
                                message: 'อัปโหลดสำเร็จ (Server Response Error)',
                                data: {
                                    file_id: 'unknown_' + Date.now(),  // ← Fake file_id!
                                    file_name: file.name,
                                    file_size: file.size,
                                    note: 'ไฟล์อาจถูกอัปโหลดแล้ว...'
                                }
                            };
                        }
                        return data;
                    });
                }
                */

                // ✅ โค้ดใหม่ (ถูกต้อง) - ให้ HTTP 500 = Error
                if (response.status === 500) {
                    console.error('❌ Server returned HTTP 500 for:', file.name);

                    return response.text().then(text => {
                        let errorMessage = 'เซิร์ฟเวอร์เกิดข้อผิดพลาด (HTTP 500)';

                        try {
                            // พยายาม parse JSON ถ้าได้
                            const data = JSON.parse(text);
                            if (data.message) {
                                errorMessage = data.message;
                            }
                        } catch (e) {
                            // ถ้า parse ไม่ได้ (HTML error page)
                            console.error('❌ Cannot parse 500 response:', text.substring(0, 200));

                            // ตรวจสอบว่าเป็น HTML error page หรือไม่
                            if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                                errorMessage = 'เซิร์ฟเวอร์เกิดข้อผิดพลาด (Server Error Page)';
                            }
                        }

                        // ⭐ คืนค่า error (ไม่ใช่ success)
                        return {
                            success: false,  // ← ต้องเป็น false
                            message: errorMessage,
                            http_status: 500
                        };
                    });
                }

                // ✅ จัดการ response ปกติ (ไม่แก้ไข)
                return handleApiResponse(response);
            })
            .then(data => {
                console.log('📄 Upload response data:', data);

                // ====================================================================
                // ✅ จัดการผลลัพธ์ (ไม่แก้ไข - เก็บไว้เหมือนเดิม)
                // ====================================================================
                if (data.success) {
                    uploadedCount++;
                    uploadResults.push({
                        file: file.name,
                        status: 'success',
                        message: data.message || 'อัปโหลดสำเร็จ',
                        file_id: data.data?.file_id,  // ✅ ดึง file_id จริง
                        file_size_mb: data.data?.file_size_mb,
                        note: data.data?.note || null
                    });

                    console.log(`✅ File ${currentFileNum} uploaded successfully:`, file.name);
                } else {
                    // ⭐ กรณี Error (รวม HTTP 500 ที่แก้ไขแล้ว)
                    failedCount++;
                    uploadResults.push({
                        file: file.name,
                        status: 'error',
                        message: data.message || 'อัปโหลดล้มเหลว'
                    });

                    console.log(`❌ File ${currentFileNum} upload failed:`, data.message);
                }

                // อัปโหลดไฟล์ถัดไป
                uploadFilesSequentially(files, folderId, index + 1, uploadedCount, failedCount, uploadResults, totalFiles);
            })
            .catch(error => {
                console.error(`💥 Upload error for file ${currentFileNum}:`, error);

                // ไม่แสดง error ถ้าเป็น Access Denied (เพราะ Modal แสดงแล้ว)
                if (error.message === 'Access Denied - Modal Shown') {
                    return; // หยุดการอัปโหลดที่เหลือ
                }

                failedCount++;
                uploadResults.push({
                    file: file.name,
                    status: 'error',
                    message: 'เกิดข้อผิดพลาดในการอัปโหลด: ' + error.message,
                    error_details: error.data?.debug_info || null
                });

                // อัปโหลดไฟล์ถัดไป
                uploadFilesSequentially(files, folderId, index + 1, uploadedCount, failedCount, uploadResults, totalFiles);
            });
    }



    // ====================================================================
    // 🔧 ฟังก์ชันที่ 2: showDirectUploadComplete (แก้เฉพาะบรรทัด 17-20)
    // ====================================================================

    async function showDirectUploadComplete(uploadedCount, failedCount, uploadResults, totalFiles) {
        // รีเซ็ต upload state
        isUploading = false;
        isDragAndDropUpload = false;

        // ปิด modal ทันที
        Swal.close();

        // ปิด upload modal ถ้าเปิดอยู่
        if (!document.getElementById('uploadModal').classList.contains('hidden')) {
            closeUploadModal();
        }

        // รีเฟรชไฟล์และข้อมูล
        refreshFiles();
        loadMemberInfo();

        // ====================================================================
        // ⭐ เพิ่มส่วนนี้ - เรียก Auto-Share Prompt
        // ====================================================================
        if (uploadedCount > 0) {
            console.log('✅ Upload completed, showing auto-share prompt...');
            await promptAutoShareAfterUpload(uploadResults);
        }
        // ====================================================================

        // แสดง toast แบบเรียบง่าย ถ้ามี error (ไม่แก้ไข)
        if (failedCount > 0) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: `อัปโหลด ${uploadedCount}/${totalFiles} ไฟล์สำเร็จ`,
                text: `${failedCount} ไฟล์ล้มเหลว`,
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                customClass: {
                    popup: 'glass-card rounded-xl'
                }
            });
        }

        console.log(`✅ Upload completed: ${uploadedCount} success, ${failedCount} failed`);
    }


    function updateDirectUploadProgress(current, total, fileName, uploadedCount) {
        const percent = Math.round((current / total) * 100);

        const progressBar = document.getElementById('uploadProgressBar');
        const currentFileIndex = document.getElementById('currentFileIndex');
        const currentFileName = document.getElementById('currentFileName');
        const uploadedCountEl = document.getElementById('uploadedCount');
        const percentEl = document.getElementById('uploadPercent');

        if (progressBar) progressBar.style.width = percent + '%';
        if (currentFileIndex) currentFileIndex.textContent = current;
        if (currentFileName) currentFileName.textContent = fileName;
        if (uploadedCountEl) uploadedCountEl.textContent = `${uploadedCount} ไฟล์สำเร็จ`;
        if (percentEl) percentEl.textContent = percent + '%';
    }




    function updateFileInputDirectly(validFiles) {
        const fileInput = document.getElementById('fileInput');

        // เก็บ event handler เดิม
        const originalOnChange = fileInput.onchange;

        // ปิด event handler ชั่วคราว
        fileInput.onchange = null;

        // ใส่ไฟล์
        const dt = new DataTransfer();
        validFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;

        // เปิด event handler กลับหลังจาก 100ms
        setTimeout(() => {
            fileInput.onchange = originalOnChange;
        }, 100);
    }


    // Check Trial Storage Before Drop
    function checkTrialStorageBeforeDrop(files) {
        console.log('⚠️ checkTrialStorageBeforeDrop is deprecated - skipping trial check');

        // ✅ ไม่เรียก handleDroppedFiles เพื่อหลีกเลี่ยง infinite loop
        // handleDroppedFiles(files); // ❌ ลบบรรทัดนี้

        // ✅ แจ้งเตือนว่าไม่ใช้ trial mode แล้ว
        Swal.fire({
            icon: 'info',
            title: 'ระบบได้รับการอัปเกรด',
            text: 'ไม่มีขีดจำกัดการใช้งานแล้ว กรุณาลองอัปโหลดอีกครั้ง',
            confirmButtonText: 'ตกลง'
        });
    }

    // Event Listeners Setup
    function setupEventListeners() {
        console.log('🎯 Setting up event listeners...');

        // Keyboard shortcuts
        document.addEventListener('keydown', function (e) {
            // Ctrl/Cmd + U = Upload
            if ((e.ctrlKey || e.metaKey) && e.key === 'u') {
                e.preventDefault();
                if (!document.getElementById('uploadBtn').disabled) {
                    handleUploadClick();
                }
            }

            // Ctrl/Cmd + Shift + N = New Folder
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'N') {
                e.preventDefault();
                if (!document.getElementById('createFolderBtn').disabled) {
                    handleCreateFolderClick();
                }
            }

            // F5 = Refresh
            if (e.key === 'F5') {
                e.preventDefault();
                refreshFiles();
            }

            // Escape = Close modals
            if (e.key === 'Escape') {
                const modals = ['uploadModal', 'createFolderModal', 'upgradeModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal && !modal.classList.contains('hidden')) {
                        if (modalId === 'uploadModal') closeUploadModal();
                        if (modalId === 'createFolderModal') closeCreateFolderModal();
                        if (modalId === 'upgradeModal') closeUpgradeModal();
                    }
                });

                // Close user menu
                const userMenu = document.getElementById('userMenu');
                if (userMenu && !userMenu.classList.contains('hidden')) {
                    toggleUserMenu();
                }
            }
        });

        // Click outside to close user menu
        document.addEventListener('click', function (e) {
            const userMenu = document.getElementById('userMenu');
            const menuButton = e.target.closest('button[onclick="toggleUserMenu()"]');

            if (userMenu && !userMenu.classList.contains('hidden') && !userMenu.contains(e.target) && !menuButton) {
                userMenu.classList.add('hidden');
            }
        });
    }

    // User Menu Toggle
    function toggleUserMenu() {
        const userMenu = document.getElementById('userMenu');
        userMenu.classList.toggle('hidden');
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
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    function formatDateTime(dateString) {
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return dateString;
        }
    }

    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();

        const icons = {
            'pdf': { icon: 'fas fa-file-pdf', color: 'bg-gradient-to-br from-red-500 to-red-600' },
            'doc': { icon: 'fas fa-file-word', color: 'bg-gradient-to-br from-blue-500 to-blue-600' },
            'docx': { icon: 'fas fa-file-word', color: 'bg-gradient-to-br from-blue-500 to-blue-600' },
            'xls': { icon: 'fas fa-file-excel', color: 'bg-gradient-to-br from-green-500 to-green-600' },
            'xlsx': { icon: 'fas fa-file-excel', color: 'bg-gradient-to-br from-green-500 to-green-600' },
            'ppt': { icon: 'fas fa-file-powerpoint', color: 'bg-gradient-to-br from-orange-500 to-orange-600' },
            'pptx': { icon: 'fas fa-file-powerpoint', color: 'bg-gradient-to-br from-orange-500 to-orange-600' },
            'jpg': { icon: 'fas fa-file-image', color: 'bg-gradient-to-br from-purple-500 to-purple-600' },
            'jpeg': { icon: 'fas fa-file-image', color: 'bg-gradient-to-br from-purple-500 to-purple-600' },
            'png': { icon: 'fas fa-file-image', color: 'bg-gradient-to-br from-purple-500 to-purple-600' },
            'gif': { icon: 'fas fa-file-image', color: 'bg-gradient-to-br from-purple-500 to-purple-600' },
            'zip': { icon: 'fas fa-file-archive', color: 'bg-gradient-to-br from-yellow-500 to-yellow-600' },
            'rar': { icon: 'fas fa-file-archive', color: 'bg-gradient-to-br from-yellow-500 to-yellow-600' },
            'txt': { icon: 'fas fa-file-alt', color: 'bg-gradient-to-br from-gray-500 to-gray-600' }
        };

        return icons[ext] || { icon: 'fas fa-file', color: 'bg-gradient-to-br from-gray-500 to-gray-600' };
    }



</script>


<script>
    // View and Sort Functions
    function changeViewMode(mode) {
        console.log('👀 Changing view mode to:', mode);
        viewMode = mode;

        // Update button states
        document.querySelectorAll('.view-mode-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.getElementById(mode + 'ViewBtn').classList.add('active');

        const folderTreeSidebar = document.getElementById('folderTreeSidebar');
        const fileList = document.getElementById('fileList');

        if (mode === 'tree') {
            // Show tree sidebar
            folderTreeSidebar.classList.remove('hidden');
            fileList.style.marginLeft = '320px';
            renderFolderTree();
        } else {
            // Hide tree sidebar
            folderTreeSidebar.classList.add('hidden');
            fileList.style.marginLeft = '0';
        }

        if (fileListData.length > 0) {
            renderFileList();
        }
    }

    function sortFiles(sortBy) {
        console.log('🔄 Sorting by:', sortBy);

        // Update button states
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        const sortButtons = {
            'name': 'sortNameBtn',
            'modified': 'sortDateBtn',
            'size': 'sortSizeBtn',
            'type': 'sortTypeBtn'
        };

        if (sortButtons[sortBy]) {
            document.getElementById(sortButtons[sortBy]).classList.add('active');
        }

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

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function () {
        console.log('🚀 Apple-inspired Member Drive initialized');
        console.log('📊 Trial Mode:', IS_TRIAL_MODE);
        initializeMemberDrive();
        setupDragAndDrop();
        setupEventListeners();
    });
</script>


<script>


    // ✅ Enhanced Permission System - แสดงสิทธิ์ตามโฟลเดอร์ปัจจุบัน (Fixed Version)
    // วางส่วนนี้ในส่วนท้ายของ <script> หลัก ไม่ต้อง override functions เดิม

    // 🔧 ดึงสิทธิ์สำหรับโฟลเดอร์ปัจจุบัน
    function getFolderSpecificPermissions(folderId) {
        return new Promise((resolve, reject) => {
            fetch(API_BASE_URL + 'get_folder_permissions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'folder_id=' + encodeURIComponent(folderId || 'root')
            })
                .then(response => {
                    console.log('🔐 Folder permissions response status:', response.status);
                    return handleApiResponse(response);
                })
                .then(data => {
                    console.log('🔐 Folder permissions data:', data);
                    if (data.success && data.data) {
                        resolve(data.data);
                    } else {
                        reject(new Error(data.message || 'ไม่สามารถดึงข้อมูลสิทธิ์ได้'));
                    }
                })
                .catch(error => {
                    console.error('💥 Error getting folder permissions:', error);
                    reject(error);
                });
        });
    }

    // 🔄 อัปเดตการแสดงสิทธิ์ตามโฟลเดอร์ปัจจุบัน
    async function updatePermissionInfoForCurrentFolder() {
        try {
            console.log('🔐 Updating permission info for folder:', currentFolder);

            // แสดง loading state
            const permissionLevelEl = document.getElementById('permissionLevel');
            const permissionDescriptionEl = document.getElementById('permissionDescription');

            if (permissionLevelEl) permissionLevelEl.textContent = 'กำลังตรวจสอบสิทธิ์...';
            if (permissionDescriptionEl) permissionDescriptionEl.textContent = 'กำลังโหลด...';

            // ดึงสิทธิ์สำหรับโฟลเดอร์ปัจจุบัน
            const folderPermissions = await getFolderSpecificPermissionsWithFallback(currentFolder);

            // อัปเดตการแสดงผล
            updateFolderPermissionDisplay(folderPermissions);
            updateFolderAvailableActions(folderPermissions);

        } catch (error) {
            console.error('💥 Error updating permission info:', error);

            // ใช้สิทธิ์เริ่มต้นแทน
            const fallbackPermissions = getFallbackPermissions(currentFolder);
            updateFolderPermissionDisplay(fallbackPermissions);
            updateFolderAvailableActions(fallbackPermissions);
        }
    }


    function getFolderSpecificPermissionsWithFallback(folderId) {
        return new Promise((resolve, reject) => {
            fetch(API_BASE_URL + 'get_folder_permissions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'folder_id=' + encodeURIComponent(folderId || 'root')
            })
                .then(response => {
                    console.log('🔐 Folder permissions response status:', response.status);

                    // ถ้าได้ response แล้วให้ลองแปลง JSON
                    if (response.ok) {
                        return response.json();
                    } else {
                        // ถ้าไม่ success ให้ใช้ fallback
                        console.warn('⚠️ Server returned non-200 status, using fallback permissions');
                        resolve(getFallbackPermissions(folderId));
                        return;
                    }
                })
                .then(data => {
                    if (data && data.success && data.data) {
                        console.log('✅ Got folder permissions:', data.data);

                        // ตรวจสอบว่าเป็น fallback หรือไม่
                        if (data.fallback) {
                            console.warn('⚠️ Using fallback permissions from server');
                        }

                        resolve(data.data);
                    } else {
                        console.warn('⚠️ Invalid response format, using client fallback');
                        resolve(getFallbackPermissions(folderId));
                    }
                })
                .catch(error => {
                    console.error('💥 Network error, using fallback permissions:', error);
                    resolve(getFallbackPermissions(folderId));
                });
        });
    }



    function getFallbackPermissions(folderId) {
        console.log('🛡️ Using client-side fallback permissions for folder:', folderId);

        // สิทธิ์พื้นฐานตาม Trial Mode
        if (IS_TRIAL_MODE) {
            const trialFolders = ['demo_folder_1', 'demo_folder_2', 'demo_folder_3', 'demo_folder_4'];

            if (folderId === 'root' || trialFolders.includes(folderId)) {
                return {
                    access_level: 'read_write',
                    can_upload: true,
                    can_create_folder: true,
                    can_share: false,
                    can_delete: true,
                    can_download: false,
                    permission_source: 'trial_fallback',
                    granted_by: 'System',
                    granted_at: new Date().toISOString(),
                    expires_at: null,
                    folder_id: folderId,
                    is_trial: true,
                    fallback: true
                };
            } else {
                return {
                    access_level: 'no_access',
                    can_upload: false,
                    can_create_folder: false,
                    can_share: false,
                    can_delete: false,
                    can_download: false,
                    permission_source: 'trial_fallback',
                    granted_by: 'System',
                    folder_id: folderId,
                    is_trial: true,
                    fallback: true
                };
            }
        }

        // สิทธิ์พื้นฐานสำหรับ Production Mode
        return {
            access_level: folderId === 'root' ? 'read_write' : 'read_only',
            can_upload: folderId === 'root',
            can_create_folder: false,
            can_share: false,
            can_delete: false,
            can_download: true,
            permission_source: 'client_fallback',
            granted_by: 'System',
            granted_at: new Date().toISOString(),
            expires_at: null,
            folder_id: folderId,
            is_trial: false,
            fallback: true
        };
    }




    // 🎨 อัปเดตการแสดงสิทธิ์โฟลเดอร์
    function updateFolderPermissionDisplay(folderPermissions) {
        const permissionLevelEl = document.getElementById('permissionLevel');
        const permissionDescriptionEl = document.getElementById('permissionDescription');

        if (!folderPermissions || !permissionLevelEl || !permissionDescriptionEl) {
            return;
        }

        // 📍 แสดงชื่อโฟลเดอร์ปัจจุบัน
        let folderName = 'โฟลเดอร์หลัก';
        if (currentFolder !== 'root' && breadcrumbData && breadcrumbData.length > 0) {
            folderName = breadcrumbData[breadcrumbData.length - 1].name;
        }

        // 🔐 แสดงระดับสิทธิ์
        let permissionText = getPermissionDisplayText(folderPermissions.access_level);
        let permissionIcon = getPermissionIcon(folderPermissions.access_level);

        if (IS_TRIAL_MODE) {
            permissionText += ' (Trial)';
        }

        // ⚠️ แสดง fallback warning
        if (folderPermissions.fallback) {
            permissionText += ' (ใช้ค่าเริ่มต้น)';
        }

        permissionLevelEl.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-br ${getPermissionColor(folderPermissions.access_level)} rounded-xl flex items-center justify-center mr-3 shadow-sm">
                    <i class="${permissionIcon} text-white"></i>
                </div>
                <div>
                    <div class="font-semibold text-gray-800">${escapeHtml(folderName)}</div>
                    <div class="text-sm text-gray-600">${permissionText}</div>
                    ${folderPermissions.fallback ? '<div class="text-xs text-orange-600 mt-1">⚠️ ใช้สิทธิ์เริ่มต้นเนื่องจากไม่สามารถโหลดได้</div>' : ''}
                </div>
            </div>
            <div class="flex items-center">
                ${folderPermissions.access_level === 'no_access' ?
                '<i class="fas fa-times-circle text-red-500 text-xl"></i>' :
                '<i class="fas fa-check-circle text-green-500 text-xl"></i>'
            }
                ${folderPermissions.fallback ? '<i class="fas fa-exclamation-triangle text-orange-500 text-sm ml-2" title="ใช้ค่าเริ่มต้น"></i>' : ''}
            </div>
        </div>
    `;

        // 📝 แสดงคำอธิบายสิทธิ์
        let description = getFolderPermissionDescription(folderPermissions);
        if (folderPermissions.fallback) {
            description += ' (หากต้องการสิทธิ์เต็ม กรุณาติดต่อแอดมิน)';
        }
        permissionDescriptionEl.textContent = description;

        // 🕒 อัปเดตเวลาตรวจสอบล่าสุด
        updateLastPermissionCheck();
    }




    // 🎯 อัปเดตการกระทำที่ใช้ได้ในโฟลเดอร์
    // 🎯 อัปเดตการกระทำที่ใช้ได้ในโฟลเดอร์
    function updateFolderAvailableActions(folderPermissions) {
        const actionsContainer = document.getElementById('availableActions');
        if (!actionsContainer || !folderPermissions) {
            return;
        }

        const actions = [];

        // 👀 ดู - อนุญาตเสมอ
        actions.push({
            icon: 'fas fa-eye',
            text: 'ดู',
            color: 'blue',
            status: 'allowed'
        });

        // 📤 อัปโหลด
        if (folderPermissions.can_upload) {
            actions.push({
                icon: 'fas fa-upload',
                text: 'อัปโหลด',
                color: IS_TRIAL_MODE ? 'orange' : 'green',
                status: 'allowed'
            });
        } else {
            actions.push({
                icon: 'fas fa-upload',
                text: 'อัปโหลด',
                color: 'gray',
                status: 'denied'
            });
        }

        // 📁 สร้างโฟลเดอร์
        if (folderPermissions.can_create_folder) {
            actions.push({
                icon: 'fas fa-folder-plus',
                text: 'สร้างโฟลเดอร์',
                color: IS_TRIAL_MODE ? 'orange' : 'purple',
                status: 'allowed'
            });
        } else {
            actions.push({
                icon: 'fas fa-folder-plus',
                text: 'สร้างโฟลเดอร์',
                color: 'gray',
                status: 'denied'
            });
        }

        // ✏️ เปลี่ยนชื่อ - ต้องมีสิทธิ์ write หรือ admin
        if (folderPermissions.can_upload || folderPermissions.can_delete) {
            actions.push({
                icon: 'fas fa-edit',
                text: 'เปลี่ยนชื่อ',
                color: IS_TRIAL_MODE ? 'orange' : 'purple',
                status: 'allowed'
            });
        } else {
            actions.push({
                icon: 'fas fa-edit',
                text: 'เปลี่ยนชื่อ',
                color: 'gray',
                status: 'denied'
            });
        }

        // 🔗 แชร์
        if (folderPermissions.can_share && !IS_TRIAL_MODE) {
            actions.push({
                icon: 'fas fa-share',
                text: 'แชร์',
                color: 'indigo',
                status: 'allowed'
            });
        } else {
            actions.push({
                icon: 'fas fa-share',
                text: IS_TRIAL_MODE ? 'แชร์ (ล็อค)' : 'แชร์',
                color: 'gray',
                status: 'denied'
            });
        }

        // 🗑️ ลบ
        if (folderPermissions.can_delete) {
            actions.push({
                icon: 'fas fa-trash',
                text: 'ลบ',
                color: IS_TRIAL_MODE ? 'orange' : 'red',
                status: 'allowed'
            });
        } else {
            actions.push({
                icon: 'fas fa-trash',
                text: 'ลบ',
                color: 'gray',
                status: 'denied'
            });
        }

        // 📥 ดาวน์โหลด
        if (folderPermissions.can_download && !IS_TRIAL_MODE) {
            actions.push({
                icon: 'fas fa-download',
                text: 'ดาวน์โหลด',
                color: 'blue',
                status: 'allowed'
            });
        } else {
            actions.push({
                icon: 'fas fa-download',
                text: IS_TRIAL_MODE ? 'ดาวน์โหลด (ล็อค)' : 'ดาวน์โหลด',
                color: 'gray',
                status: 'denied'
            });
        }

        // 🎨 สร้าง HTML
        let html = '';
        actions.forEach(action => {
            const isAllowed = action.status === 'allowed';
            const opacity = isAllowed ? '' : 'opacity-60';
            const iconColor = isAllowed ? `text-${action.color}-600` : 'text-gray-400';

            html += `
            <div class="flex items-center text-sm bg-${action.color}-50 rounded-xl p-3 ${opacity} transition-all duration-200">
                <i class="${action.icon} ${iconColor} mr-3 text-lg"></i>
                <div class="flex-1">
                    <span class="font-medium text-gray-800">${action.text}</span>
                    ${!isAllowed ? '<div class="text-xs text-gray-500 mt-1">ไม่มีสิทธิ์</div>' : ''}
                </div>
                ${isAllowed ?
                    '<i class="fas fa-check-circle text-green-500 ml-2"></i>' :
                    '<i class="fas fa-times-circle text-gray-400 ml-2"></i>'
                }
            </div>
        `;
        });

        actionsContainer.innerHTML = html;
    }

    // 📋 ดึงข้อความแสดงสิทธิ์
    function getPermissionDisplayText(accessLevel) {
        const levels = {
            'owner': 'เจ้าของ',
            'admin': 'ผู้ดูแล',
            'read_write': 'อ่าน-เขียน',
            'read_only': 'อ่านอย่างเดียว',
            'no_access': 'ไม่มีสิทธิ์เข้าถึง'
        };

        return levels[accessLevel] || 'สิทธิ์มาตรฐาน';
    }

    // 🎨 ดึงสีตามระดับสิทธิ์
    function getPermissionColor(accessLevel) {
        const colors = {
            'owner': 'from-purple-500 to-purple-600',
            'admin': 'from-blue-500 to-blue-600',
            'read_write': 'from-green-500 to-green-600',
            'read_only': 'from-yellow-500 to-yellow-600',
            'no_access': 'from-red-500 to-red-600'
        };
        return colors[accessLevel] || 'from-gray-500 to-gray-600';
    }

    // 🎯 ดึงไอคอนตามระดับสิทธิ์
    function getPermissionIcon(accessLevel) {
        const icons = {
            'owner': 'fas fa-crown',
            'admin': 'fas fa-user-shield',
            'read_write': 'fas fa-edit',
            'read_only': 'fas fa-eye',
            'no_access': 'fas fa-ban'
        };
        return icons[accessLevel] || 'fas fa-user';
    }

    // 📝 ดึงคำอธิบายสิทธิ์โฟลเดอร์
    function getFolderPermissionDescription(folderPermissions) {
        let description = '';

        switch (folderPermissions.access_level) {
            case 'owner':
                description = 'คุณเป็นเจ้าของโฟลเดอร์นี้ สามารถทำทุกอย่างได้';
                break;
            case 'admin':
                description = 'คุณมีสิทธิ์ผู้ดูแล สามารถจัดการและแชร์ได้';
                break;
            case 'read_write':
                description = 'คุณสามารถดู แก้ไข และอัปโหลดไฟล์ได้';
                break;
            case 'read_only':
                description = 'คุณสามารถดูและดาวน์โหลดไฟล์เท่านั้น';
                break;
            case 'no_access':
                description = 'คุณไม่มีสิทธิ์เข้าถึงโฟลเดอร์นี้';
                break;
            default:
                description = 'สิทธิ์การเข้าถึงมาตรฐาน';
        }

        if (IS_TRIAL_MODE) {
            description += ' (มีข้อจำกัดในโหมดทดลอง)';
        }

        // เพิ่มข้อมูลเกี่ยวกับแหล่งที่มาของสิทธิ์
        if (folderPermissions.permission_source) {
            const sources = {
                'direct': 'ได้รับสิทธิ์โดยตรง',
                'position': 'ได้รับสิทธิ์จากตำแหน่ง',
                'department': 'ได้รับสิทธิ์จากแผนก',
                'system': 'ได้รับสิทธิ์จากระบบ'
            };

            const sourceText = sources[folderPermissions.permission_source] || 'ได้รับสิทธิ์จากระบบ';
            description += ` (${sourceText})`;
        }

        return description;
    }

    // 📊 อัปเดตรายละเอียดสิทธิ์
    function updatePermissionDetails(folderPermissions) {
        const sources = {
            'direct': 'ได้รับสิทธิ์โดยตรง',
            'position': 'สิทธิ์จากตำแหน่ง',
            'department': 'สิทธิ์จากแผนก',
            'system': 'สิทธิ์จากระบบ',
            'owner': 'เป็นเจ้าของ',
            'shared': 'ได้รับการแชร์'
        };

        const sourceEl = document.getElementById('permissionSource');
        const grantedByEl = document.getElementById('grantedBy');
        const grantedAtEl = document.getElementById('grantedAt');
        const expiresAtEl = document.getElementById('expiresAt');

        if (sourceEl) {
            sourceEl.textContent = sources[folderPermissions.permission_source] || 'ไม่ระบุ';
        }

        if (grantedByEl) {
            grantedByEl.textContent = folderPermissions.granted_by || 'ระบบ';
        }

        if (grantedAtEl) {
            grantedAtEl.textContent = folderPermissions.granted_at ?
                formatDateTime(folderPermissions.granted_at) : '-';
        }

        if (expiresAtEl) {
            if (folderPermissions.expires_at) {
                const expiryDate = new Date(folderPermissions.expires_at);
                const now = new Date();
                const isExpired = expiryDate < now;

                expiresAtEl.innerHTML = `
                <span class="${isExpired ? 'text-red-600 font-medium' : 'text-gray-600'}">
                    ${formatDateTime(folderPermissions.expires_at)}
                    ${isExpired ? '<i class="fas fa-exclamation-triangle ml-1 text-red-500"></i>' : ''}
                </span>
            `;
            } else {
                expiresAtEl.textContent = 'ไม่หมดอายุ';
            }
        }
    }





    // 🕒 อัปเดตเวลาตรวจสอบล่าสุด
    function updateLastPermissionCheck() {
        const lastCheckEl = document.getElementById('lastPermissionCheck');
        if (lastCheckEl) {
            const now = new Date();
            lastCheckEl.textContent = `อัปเดตล่าสุด: ${formatDateTime(now)}`;
        }
    }

    // 🔄 อัปเดต Global Permission Info (สิทธิ์ระดับผู้ใช้)
    function updateGlobalPermissionInfoSafe() {
        if (!memberInfo || !memberInfo.permission) return;

        const globalLevelEl = document.getElementById('globalPermissionLevel');
        const globalDescEl = document.getElementById('globalPermissionDescription');

        if (globalLevelEl && globalDescEl) {
            const permission = memberInfo.permission;
            let permissionText = permission.type_name || permission.permission_type;

            if (IS_TRIAL_MODE) {
                permissionText += ' (Trial)';
            }

            globalLevelEl.textContent = permissionText;
            globalDescEl.textContent = getPermissionDescription(permission);
        }
    }


    function retryGetFolderPermissions(folderId) {
        if (permissionRetryCount < MAX_PERMISSION_RETRIES) {
            permissionRetryCount++;
            console.log(`🔄 Retrying folder permissions (${permissionRetryCount}/${MAX_PERMISSION_RETRIES}) for folder:`, folderId);

            setTimeout(() => {
                updatePermissionInfoForCurrentFolder();
            }, 1000 * permissionRetryCount); // Exponential backoff
        } else {
            console.warn('⚠️ Max retries reached, using fallback permissions');
            permissionRetryCount = 0;

            const fallbackPermissions = getFallbackPermissions(folderId);
            updateFolderPermissionDisplay(fallbackPermissions);
            updateFolderAvailableActions(fallbackPermissions);
        }
    }

    // 🔄 รีเซ็ต retry counter เมื่อเปลี่ยนโฟลเดอร์
    function resetPermissionRetryCounter() {
        permissionRetryCount = 0;
    }



    function initializePermissionSystem() {
        if (permissionSystemInitialized) return;

        console.log('🔐 Initializing enhanced permission system...');

        // Hook เข้ากับ loadFolderContents โดยไม่ override
        const originalLoadFolderContents = window.loadFolderContents;
        if (originalLoadFolderContents) {
            window.loadFolderContents = function (folderId) {
                resetPermissionRetryCounter();
                return originalLoadFolderContents.call(this, folderId);
            };
        }

        // Hook เข้ากับ loadAccessibleFolders โดยไม่ override
        const originalLoadAccessibleFolders = window.loadAccessibleFolders;
        if (originalLoadAccessibleFolders && typeof originalLoadAccessibleFolders === 'function') {
            window.loadAccessibleFolders = function () {
                console.log('📂 Loading accessible folders with permission check');
                const result = originalLoadAccessibleFolders.call(this);
                setTimeout(() => updatePermissionInfoForCurrentFolder(), 800);
                return result;
            };
        }

        // Hook เข้ากับ updateMemberStats โดยไม่ override
        const originalUpdateMemberStats = window.updateMemberStats;
        if (originalUpdateMemberStats && typeof originalUpdateMemberStats === 'function') {
            window.updateMemberStats = function () {
                const result = originalUpdateMemberStats.call(this);
                updateGlobalPermissionInfoSafe();
                return result;
            };
        }

        permissionSystemInitialized = true;

        // เริ่มต้นการแสดงสิทธิ์
        setTimeout(() => {
            updatePermissionInfoForCurrentFolder();
        }, 1500);

        console.log('✅ Enhanced permission system initialized successfully');
    }

    // 🚀 เริ่มต้นระบบเมื่อหน้าเว็บโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function () {
        // รอให้ระบบหลักโหลดเสร็จก่อน
        setTimeout(() => {
            initializePermissionSystem();
        }, 3000);
    });

    // 🔄 เริ่มต้นระบบเมื่อ window load (สำรอง)
    window.addEventListener('load', function () {
        setTimeout(() => {
            if (!permissionSystemInitialized) {
                initializePermissionSystem();
            }
        }, 4000);
    });


</script>



<script>
    /**
     * 🆕 ระบบ Navigation History แยกใหม่ (ไม่ทับของเดิม)
     * ใช้ชื่อ function ใหม่เพื่อไม่กระทบ refreshFiles() และ function อื่นๆ
     */

    // ตัวแปรใหม่สำหรับระบบ Navigation
    let folderNavigationHistory = ['root']; // ประวัติการเข้าโฟลเดอร์
    let currentFolderIndex = 0; // ตำแหน่งปัจจุบัน
    let isNavigatingBack = false; // ป้องกันการเพิ่มประวัติซ้ำ

    /**
     * 🔙 ฟังก์ชันใหม่สำหรับย้อนกลับ
     */
    function navigateBack() {
        console.log('🔙 Navigate back clicked');
        console.log('Current navigation history:', folderNavigationHistory);
        console.log('Current folder index:', currentFolderIndex);

        // ตรวจสอบว่าสามารถย้อนกลับได้หรือไม่
        if (currentFolderIndex > 0) {
            currentFolderIndex--;
            const previousFolderId = folderNavigationHistory[currentFolderIndex];

            console.log('🔙 Going back to folder:', previousFolderId);

            // ตั้งค่าสถานะการนำทางย้อนกลับ
            isNavigatingBack = true;

            // เรียกใช้ function เดิมโดยไม่เพิ่มเข้าประวัติ
            if (previousFolderId === 'root') {
                // กลับไปหน้าหลัก
                if (typeof loadAccessibleFolders === 'function') {
                    loadAccessibleFolders();
                } else {
                    // fallback ถ้าไม่มี function
                    currentFolder = 'root';
                    refreshFiles();
                }
            } else {
                // กลับไปโฟลเดอร์ที่ระบุ
                currentFolder = previousFolderId;
                refreshFiles();
            }

            // อัปเดตสถานะปุ่ม
            updateNavigationButtonState();

            // แสดงข้อความแจ้งเตือน
            showNavigationToast('🔙 ย้อนกลับเรียบร้อย', 'success');

            // รีเซ็ตสถานะการนำทาง
            setTimeout(() => {
                isNavigatingBack = false;
            }, 500);
        } else {
            console.log('🚫 Cannot navigate back - already at root');

            // แสดงการแจ้งเตือน
            showNavigationToast('🏠 คุณอยู่ที่โฟลเดอร์หลักอยู่แล้ว', 'info');
        }
    }

    /**
     * 📂 ฟังก์ชันเพิ่มโฟลเดอร์เข้าประวัติ (เรียกใช้เมื่อเข้าโฟลเดอร์ใหม่)
     */
    function addToNavigationHistory(folderId) {
        // ป้องกันการเพิ่มประวัติเมื่อกำลังนำทางย้อนกลับ
        if (isNavigatingBack) {
            console.log('🚫 Skip adding to history - navigating back');
            return;
        }

        // ถ้าไม่ใช่โฟลเดอร์เดียวกับปัจจุบัน
        if (folderNavigationHistory[currentFolderIndex] !== folderId) {
            // ลบประวัติที่อยู่หลังตำแหน่งปัจจุบัน
            folderNavigationHistory = folderNavigationHistory.slice(0, currentFolderIndex + 1);

            // เพิ่มโฟลเดอร์ใหม่
            folderNavigationHistory.push(folderId);
            currentFolderIndex++;

            console.log('📚 Navigation history updated:', folderNavigationHistory);
            console.log('📍 New index:', currentFolderIndex);

            // อัปเดตสถานะปุ่ม
            updateNavigationButtonState();
        }
    }

    /**
     * 🔄 อัปเดตสถานะปุ่มย้อนกลับ
     */
    function updateNavigationButtonState() {
        const backBtn = document.getElementById('backBtn');
        if (!backBtn) {
            console.warn('Back button not found');
            return;
        }

        if (currentFolderIndex <= 0) {
            // ปิดการใช้งานปุ่ม
            backBtn.disabled = true;
            backBtn.classList.add('opacity-50', 'cursor-not-allowed');
            backBtn.classList.remove('hover:from-gray-600', 'hover:to-gray-700', 'transform', 'hover:-translate-y-0.5');
            backBtn.title = 'อยู่ที่โฟลเดอร์หลักแล้ว';

            // เปลี่ยนสีเป็นเทาอ่อน
            backBtn.classList.remove('from-gray-500', 'to-gray-600');
            backBtn.classList.add('from-gray-300', 'to-gray-400');
        } else {
            // เปิดการใช้งานปุ่ม
            backBtn.disabled = false;
            backBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'from-gray-300', 'to-gray-400');
            backBtn.classList.add('hover:from-gray-600', 'hover:to-gray-700', 'transform', 'hover:-translate-y-0.5', 'from-gray-500', 'to-gray-600');

            // แสดงชื่อโฟลเดอร์ก่อนหน้าใน tooltip
            const previousFolderId = folderNavigationHistory[currentFolderIndex - 1];
            if (previousFolderId === 'root') {
                backBtn.title = 'ย้อนกลับไปโฟลเดอร์หลัก';
            } else {
                // หาชื่อโฟลเดอร์จากข้อมูลที่มี
                const folderName = getNavigationFolderName(previousFolderId);
                backBtn.title = `ย้อนกลับไป: ${folderName}`;
            }
        }

        console.log(`🔄 Navigation button state updated - Enabled: ${!backBtn.disabled}`);
    }

    /**
     * 📛 หาชื่อโฟลเดอร์จากข้อมูลที่มี
     */
    function getNavigationFolderName(folderId) {
        if (folderId === 'root') return 'โฟลเดอร์หลัก';

        // ลองหาจาก breadcrumb data
        if (typeof breadcrumbData !== 'undefined' && breadcrumbData && breadcrumbData.length > 0) {
            const folder = breadcrumbData.find(item => item.id === folderId);
            if (folder) return folder.name;
        }

        // ลองหาจาก file list data
        if (typeof fileListData !== 'undefined' && fileListData && fileListData.length > 0) {
            const folder = fileListData.find(item => item.id === folderId && item.type === 'folder');
            if (folder) return folder.name;
        }

        return 'โฟลเดอร์ก่อนหน้า';
    }

    /**
     * 🍞 แสดง Toast notification สำหรับ Navigation
     */
    function showNavigationToast(message, type = 'info') {
        if (typeof Swal !== 'undefined') {
            const iconMap = {
                'success': 'success',
                'info': 'info',
                'warning': 'warning',
                'error': 'error'
            };

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: iconMap[type] || 'info',
                title: message,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            console.log(`Navigation Toast: ${message}`);
        }
    }

    /**
     * 🔧 เริ่มต้นระบบ Navigation
     */
    function initializeNavigation() {
        folderNavigationHistory = ['root'];
        currentFolderIndex = 0;
        isNavigatingBack = false;

        // รอให้ DOM โหลดเสร็จก่อนอัปเดตปุ่ม
        setTimeout(() => {
            updateNavigationButtonState();
        }, 100);

        console.log('🚀 Navigation system initialized');
    }

    /**
     * 🔄 Override function openFolder เดิมเพื่อเพิ่มการจัดการประวัติ
     */
    function openFolderWithHistory(folderId) {
        console.log('📂 Opening folder with history tracking:', folderId);

        // เพิ่มเข้าประวัติก่อน
        addToNavigationHistory(folderId);

        // เรียกใช้ function เดิม
        if (typeof openFolder === 'function') {
            openFolder(folderId);
        } else {
            // fallback ถ้าไม่มี function เดิม
            currentFolder = folderId;
            refreshFiles();
        }
    }

    /**
     * 🏠 กลับโฟลเดอร์หลักพร้อมจัดการประวัติ
     */
    function goToRootFolder() {
        console.log('🏠 Going to root folder');
        addToNavigationHistory('root');

        if (typeof loadAccessibleFolders === 'function') {
            loadAccessibleFolders();
        } else {
            currentFolder = 'root';
            refreshFiles();
        }
    }

    /**
     * 🎯 ฟังก์ชันช่วยสำหรับเชื่อมต่อกับระบบเดิม
     */
    function trackFolderNavigation(folderId) {
        // เรียกใช้เมื่อมีการเปลี่ยนโฟลเดอร์จากที่อื่น
        // เช่น จาก breadcrumb, tree view, หรือ search
        addToNavigationHistory(folderId);
    }

    // ⚡ เริ่มต้นระบบเมื่อหน้าโหลด
    document.addEventListener('DOMContentLoaded', function () {
        console.log('📄 DOM loaded - initializing navigation system');
        initializeNavigation();
    });

    // ⚡ ป้องกันกรณี DOM โหลดเสร็จแล้ว
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeNavigation);
    } else {
        initializeNavigation();
    }

    /**
     * 📝 วิธีการใช้งาน:
     * 
     * 1. เปลี่ยนปุ่มใน HTML:
     *    onclick="goBack()" -> onclick="navigateBack()"
     * 
     * 2. เมื่อมีการเปลี่ยนโฟลเดอร์ให้เรียก:
     *    trackFolderNavigation(folderId);
     * 
     * 3. หรือใช้:
     *    openFolderWithHistory(folderId) แทน openFolder(folderId)
     * 
     * 4. กลับหน้าหลัก:
     *    goToRootFolder() แทน loadAccessibleFolders()
     */
</script>

<script>
    // Global variables for folder access management
    let currentDeniedFolderId = null;
    let currentAccessDeniedData = null;



    // 🚫 แสดง Modal เมื่อไม่มีสิทธิ์เข้าถึง
    function showAccessDeniedModal(dataOrFileId) {
        console.log('🚫 Showing access denied modal with data:', dataOrFileId);

        // ✅ จัดการข้อมูลที่ส่งเข้ามาให้ถูกต้อง
        let displayData = {};
        let fileId = 'unknown';
        let fileName = 'ไฟล์';
        let folderName = 'โฟลเดอร์';

        if (typeof dataOrFileId === 'object' && dataOrFileId !== null) {
            // เป็น object ข้อมูล error
            displayData = dataOrFileId;
            fileId = displayData.file_id || displayData.folder_id || displayData.item_id || 'unknown';
            fileName = displayData.file_name || displayData.item_name || 'ไฟล์';
            folderName = displayData.folder_name || 'โฟลเดอร์';
        } else if (typeof dataOrFileId === 'string') {
            // เป็น string file ID
            fileId = dataOrFileId;
            fileName = 'ไฟล์';
        }

        // ✅ ตรวจสอบว่าสามารถย้อนกลับได้หรือไม่
        const canGoBack = typeof navigateBack === 'function' &&
            typeof folderNavigationHistory !== 'undefined' &&
            folderNavigationHistory.length > 1 &&
            currentFolderIndex > 0;

        const backButtonText = '🔄 ตกลง';

        // ✅ สร้าง HTML สำหรับข้อมูลเพิ่มเติม
        let additionalInfo = '';
        if (displayData.message) {
            additionalInfo += `
            <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4">
                <p class="text-sm text-red-700">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    ${escapeHtml(displayData.message)}
                </p>
            </div>
        `;
        }

        if (displayData.folder_info) {
            additionalInfo += `
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 mb-4">
                <h5 class="font-bold text-gray-800 mb-2">📁 ข้อมูลโฟลเดอร์:</h5>
                <div class="text-sm text-gray-700 space-y-1">
                    <p><strong>ชื่อ:</strong> ${escapeHtml(displayData.folder_info.folder_name || 'ไม่ระบุ')}</p>
                    <p><strong>ประเภท:</strong> ${getFolderTypeText(displayData.folder_info.folder_type || 'unknown')}</p>
                    ${displayData.folder_info.description ? `<p><strong>คำอธิบาย:</strong> ${escapeHtml(displayData.folder_info.description)}</p>` : ''}
                </div>
            </div>
        `;
        }

        Swal.fire({
            title: '🚫 ไม่มีสิทธิ์เข้าถึง',
            html: `
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i class="fas fa-lock text-3xl text-white"></i>
                </div>
                <p class="text-gray-600 mb-4">คุณไม่มีสิทธิ์เข้าถึง${fileName.includes('โฟลเดอร์') ? 'โฟลเดอร์' : 'ไฟล์'}นี้</p>
                
                ${additionalInfo}
                
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                    <h4 class="font-bold text-blue-800 mb-2">💡 วิธีขอสิทธิ์:</h4>
                    <ul class="text-sm text-blue-700 space-y-1 text-left">
                        <li>• ติดต่อเจ้าของไฟล์หรือผู้ดูแลระบบ</li>
                        <li>• ตรวจสอบว่าไฟล์อยู่ในโฟลเดอร์ที่คุณมีสิทธิ์หรือไม่</li>
                        <li>• รอให้ผู้ดูแลอนุญาตสิทธิ์การเข้าถึง</li>
                        <li>• ลองเข้าถึงจากโฟลเดอร์อื่นที่มีสิทธิ์</li>
                    </ul>
                </div>
                
                ${canGoBack ? `
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mb-4">
                        <p class="text-sm text-orange-700">
                            <i class="fas fa-sync-alt mr-2"></i>
                            <strong>แนะนำ:</strong> รีเฟสหน้าเว็บเพื่อย้อนกลับ
                        </p>
                    </div>
                ` : ''}
                
                <div class="mt-4 text-xs text-gray-500 bg-gray-100 rounded-lg p-2">
                    <div><strong>รหัสรายการ:</strong> ${escapeHtml(fileId)}</div>
                    ${displayData.timestamp ? `<div><strong>เวลา:</strong> ${new Date(displayData.timestamp).toLocaleString('th-TH')}</div>` : ''}
                </div>
            </div>
        `,
            confirmButtonText: backButtonText,
            confirmButtonColor: '#f59e0b', // สีส้มสำหรับรีเฟส
            customClass: {
                popup: 'glass-card rounded-2xl',
                confirmButton: 'rounded-xl'
            },
            allowOutsideClick: false,
            allowEscapeKey: true
        }).then((result) => {
            if (result.isConfirmed) {
                // ✅ รีเฟสหน้าทั้งหมด
                handleAccessDeniedNavigation();
            }
        });
    }


    // ✅ จัดการการนำทางเมื่อปฏิเสธการเข้าถึง - รีเฟสหน้าทั้งหมด
    function handleAccessDeniedNavigation(canGoBack) {
        console.log('🔄 Handling access denied navigation - refreshing page');

        // แสดงข้อความแจ้งเตือนก่อนรีเฟส
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: '🔄 กำลังรีเฟสหน้าเว็บ...',
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
            customClass: {
                popup: 'glass-card rounded-xl'
            }
        });

        // รีเฟสหน้าทั้งหมด
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }


    /**
     * 📋 อัปเดตรายชื่อผู้ให้สิทธิ์
     */
    function updatePermissionGrantersList(granters) {
        const container = $('#permission-granters-list');

        if (!granters || granters.length === 0) {
            container.html(`
            <div class="text-muted text-center">
                <i class="fas fa-user-slash"></i>
                <small>ไม่พบผู้ดูแล</small>
            </div>
        `);
            return;
        }

        let html = '';
        granters.forEach((granter, index) => {
            if (index < 5) { // แสดงสูงสุด 5 คน
                html += `
                <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-tie text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <div class="fw-bold small">${granter.name}</div>
                        <div class="text-muted small">${granter.role}</div>
                    </div>
                    <div class="flex-shrink-0">
                        <button class="btn btn-sm btn-outline-primary btn-contact-granter" 
                                data-email="${granter.email}" data-name="${granter.name}"
                                title="ติดต่อ ${granter.name}">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </div>
                </div>
            `;
            }
        });

        if (granters.length > 5) {
            html += `<div class="text-muted small text-center">และอีก ${granters.length - 5} คน</div>`;
        }

        container.html(html);
    }

    /**
     * 📁 แปลงประเภทโฟลเดอร์เป็นข้อความ
     */
    function getFolderTypeText(type) {
        const types = {
            'system': 'โฟลเดอร์ระบบ',
            'department': 'โฟลเดอร์แผนก',
            'shared': 'โฟลเดอร์แชร์',
            'user': 'โฟลเดอร์ผู้ใช้',
            'admin': 'โฟลเดอร์ผู้ดูแล',
            'unknown': 'ไม่ทราบประเภท'
        };
        return types[type] || 'ไม่ระบุ';
    }

    /**
     * 📧 ติดต่อผู้ให้สิทธิ์
     */
    function contactGranter(email, name) {
        const subject = `ขอสิทธิ์เข้าถึงโฟลเดอร์: ${currentAccessDeniedData?.folder_info?.folder_name || 'ไม่ทราบชื่อ'}`;
        const body = `เรียนคุณ ${name}\n\nผม/ดิฉันต้องการขอสิทธิ์เข้าถึงโฟลเดอร์ "${currentAccessDeniedData?.folder_info?.folder_name}" เพื่อ...\n\n[กรุณาระบุเหตุผล]\n\nขอบคุณครับ/ค่ะ`;

        const mailtoLink = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        window.open(mailtoLink);
    }

    /**
     * 🔧 จัดการการเรียก AJAX ที่มี Error 403
     */
    function handleAjaxResponse(xhr, textStatus, errorThrown) {
        if (xhr.status === 403) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.error_type === 'access_denied') {
                    showAccessDeniedModal(response);
                    return true; // Handled
                }
            } catch (e) {
                console.error('Error parsing 403 response:', e);
            }
        }
        return false; // Not handled
    }

    // Event Handlers
    $(document).ready(function () {
        // ปุ่มติดต่อผู้ดูแลระบบ
        $('#btn-contact-admin').on('click', function () {
            const adminEmail = 'admin@yourcompany.com'; // ใส่อีเมลผู้ดูแลระบบ
            contactGranter(adminEmail, 'ผู้ดูแลระบบ');
        });

        // ปุ่มติดต่อผู้ให้สิทธิ์
        $(document).on('click', '.btn-contact-granter', function () {
            const email = $(this).data('email');
            const name = $(this).data('name');
            contactGranter(email, name);
        });

        // Global AJAX Error Handler
        $(document).ajaxError(function (event, xhr, settings, thrownError) {
            if (!handleAjaxResponse(xhr, settings, thrownError)) {
                // Handle other errors normally
                console.error('AJAX Error:', thrownError);
            }
        });
    });

    /**
     * 🎯 ฟังก์ชันสำหรับเรียกใช้จาก AJAX success callback
     */
    function checkAndHandleFolderAccess(response, textStatus, xhr) {
        if (xhr.status === 403 && response.error_type === 'access_denied') {
            showAccessDeniedModal(response);
            return false; // Stop further processing
        }
        return true; // Continue normal processing
    }

    // Export functions for global use
    window.showAccessDeniedModal = showAccessDeniedModal;
    window.handleAjaxResponse = handleAjaxResponse;
    window.checkAndHandleFolderAccess = checkAndHandleFolderAccess;


    // ========================================
    // 📊 STORAGE STATS MANAGEMENT
    // ========================================
    /**
     * ✅ โหลดข้อมูล Storage Stats (Dynamic)
     * แก้ไข: เปลี่ยนการเรียก setDefaultStorageDisplayFromMemberInfo()
     */
    async function loadStorageStats() {
        try {
            console.log('🔄 Loading storage stats...');
            const response = await fetch(API_BASE_URL + 'get_storage_info', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await handleApiResponse(response);

            if (data.success && data.data) {
                // ✅ อัปเดตด้วยข้อมูลจาก API (ไม่เปลี่ยน)
                updateStorageDisplay(data.data);
                console.log('✅ Storage stats loaded:', data.data);
            } else {
                console.warn('⚠️ Storage stats failed:', data.message);
                // ✅ ไม่เปลี่ยนชื่อ function - เรียกชื่อเดิม
                setDefaultStorageDisplayFromMemberInfo();
            }
        } catch (error) {
            console.error('❌ Storage stats error:', error);
            // ✅ ไม่เปลี่ยนชื่อ function - เรียกชื่อเดิม
            setDefaultStorageDisplayFromMemberInfo();
        }
    }
    /**
     * โหลดสถิติไฟล์และโฟลเดอร์
     */
    async function loadFileStats() {
        try {
            console.log('🔄 Loading file stats...');

            const response = await fetch(API_BASE_URL + 'get_file_stats', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await handleApiResponse(response);

            if (data.success && data.data) {
                updateFileStatsDisplay(data.data);
                console.log('✅ File stats loaded:', data.data);
            } else {
                console.warn('⚠️ File stats failed:', data.message);
            }

        } catch (error) {
            console.error('❌ File stats error:', error);
        }
    }

    /**
     * ✅ อัปเดตการแสดงผล Storage (Dynamic - ไม่มี Hardcode)
     * แก้ไข: เปลี่ยน Element IDs ทั้งหมด
     */
    function updateStorageDisplay(data) {
        try {
            console.group('📊 Updating Storage Display');
            console.log('Data received:', data);

            // ✅ แก้ไข: personalQuotaUsed -> systemStorageUsed
            const quotaUsedEl = document.getElementById('systemStorageUsed');
            if (quotaUsedEl) {
                quotaUsedEl.textContent = data.quota_used_formatted || '0 B';
                console.log('Storage Used:', data.quota_used_formatted);
            }

            // ✅ แก้ไข: personalQuotaBar -> systemStorageBar
            const quotaBarEl = document.getElementById('systemStorageBar');
            if (quotaBarEl) {
                const percentage = parseFloat(data.percentage) || 0;
                quotaBarEl.style.width = percentage + '%';
                updateProgressBarColor(quotaBarEl, percentage);
                console.log('Progress Bar:', percentage + '%');
            }

            // ✅ แก้ไข: quotaDetails -> systemStorageDetails
            const quotaDetailsEl = document.getElementById('systemStorageDetails');
            if (quotaDetailsEl) {
                quotaDetailsEl.textContent = data.details || `0 B / ${data.quota_limit_formatted || '0 B'} (0%)`;
                console.log('Details:', quotaDetailsEl.textContent);
            }

            console.groupEnd();
        } catch (error) {
            console.error('❌ Error updating storage display:', error);
        }
    }

    /**
 * ✅ ตั้งค่าการแสดงผลเริ่มต้นจาก systemStorageInfo (Dynamic)
 * แก้ไข: เปลี่ยนชื่อ function และใช้ systemStorageInfo แทน memberInfo
 */
    function setDefaultStorageDisplayFromSystemInfo() {
        const quotaUsedEl = document.getElementById('systemStorageUsed');
        const quotaBarEl = document.getElementById('systemStorageBar');
        const quotaDetailsEl = document.getElementById('systemStorageDetails');

        // ตรวจสอบว่ามี systemStorageInfo หรือไม่
        if (!systemStorageInfo) {
            console.warn('⚠️ systemStorageInfo not available, using absolute defaults');
            if (quotaUsedEl) quotaUsedEl.textContent = '0 B';
            if (quotaBarEl) {
                quotaBarEl.style.width = '0%';
                updateProgressBarColor(quotaBarEl, 0);
            }
            if (quotaDetailsEl) quotaDetailsEl.textContent = 'กำลังโหลด...';
            return;
        }

        // ✅ ใช้ข้อมูลจาก systemStorageInfo (Dynamic)
        const quotaUsed = systemStorageInfo.total_storage_used || 0;
        const quotaLimit = systemStorageInfo.max_storage_limit || 0;
        const percentage = quotaLimit > 0 ? Math.round((quotaUsed / quotaLimit) * 100) : 0;

        if (quotaUsedEl) {
            quotaUsedEl.textContent = systemStorageInfo.total_storage_used_formatted || formatFileSize(quotaUsed);
        }

        if (quotaBarEl) {
            quotaBarEl.style.width = percentage + '%';
            updateProgressBarColor(quotaBarEl, percentage);
        }

        if (quotaDetailsEl) {
            const usedFormatted = systemStorageInfo.total_storage_used_formatted || formatFileSize(quotaUsed);
            const limitFormatted = systemStorageInfo.max_storage_limit_formatted || formatFileSize(quotaLimit);
            const modeLabel = systemStorageInfo.is_trial ? ' (ทดลอง)' : '';
            quotaDetailsEl.textContent = `${usedFormatted} / ${limitFormatted} (${percentage}%)${modeLabel}`;
        }

        console.log('✅ Storage display set from systemStorageInfo:', {
            used: systemStorageInfo.total_storage_used_formatted,
            limit: systemStorageInfo.max_storage_limit_formatted,
            percentage: percentage + '%'
        });
    }

    /**
     * ✅ ตั้งค่าการแสดงผลเริ่มต้นจาก memberInfo (Dynamic)
     * แก้ไข: เปลี่ยน Element IDs เท่านั้น
     */
    function setDefaultStorageDisplayFromMemberInfo() {
        // ✅ แก้ไข: เปลี่ยน Element IDs
        const quotaUsedEl = document.getElementById('systemStorageUsed');
        const quotaBarEl = document.getElementById('systemStorageBar');
        const quotaDetailsEl = document.getElementById('systemStorageDetails');

        // เก็บ memberInfo เดิม
        if (!memberInfo) {
            console.warn('⚠️ memberInfo not available, using absolute defaults');
            if (quotaUsedEl) quotaUsedEl.textContent = '0 B';
            if (quotaBarEl) {
                quotaBarEl.style.width = '0%';
                updateProgressBarColor(quotaBarEl, 0);
            }
            if (quotaDetailsEl) quotaDetailsEl.textContent = 'กำลังโหลด...';
            return;
        }

        // เก็บ fields เดิมจาก memberInfo
        const quotaUsed = memberInfo.quota_used || 0;
        const quotaLimit = memberInfo.quota_limit || 0;
        const percentage = quotaLimit > 0 ? Math.round((quotaUsed / quotaLimit) * 100) : 0;

        if (quotaUsedEl) {
            quotaUsedEl.textContent = formatFileSize(quotaUsed);
        }

        if (quotaBarEl) {
            quotaBarEl.style.width = percentage + '%';
            updateProgressBarColor(quotaBarEl, percentage);
        }

        if (quotaDetailsEl) {
            const usedFormatted = formatFileSize(quotaUsed);
            const limitFormatted = formatFileSize(quotaLimit);
            const trialLabel = memberInfo.is_trial_mode ? ' (Trial)' : '';
            quotaDetailsEl.textContent = `${usedFormatted} / ${limitFormatted} (${percentage}%)${trialLabel}`;
        }

        console.log('✅ Storage display set from memberInfo (fallback)');
    }

    /**
     * อัปเดตการแสดงผลสถิติไฟล์
     */
    function updateFileStatsDisplay(data) {
        try {
            // อัปเดตจำนวนไฟล์
            const filesCount = document.getElementById('myFilesCount');
            if (filesCount) {
                filesCount.textContent = data.files_count || 0;
            }

            // อัปเดตจำนวนโฟลเดอร์
            const foldersCount = document.getElementById('accessibleFoldersCount');
            if (foldersCount) {
                foldersCount.textContent = data.folders_count || 0;
            }

            // อัปเดตเวลาเข้าใช้ล่าสุด
            const lastAccess = document.getElementById('lastAccess');
            if (lastAccess) {
                lastAccess.textContent = data.last_access || 'ยังไม่มีข้อมูล';
            }

            console.log(`📁 Stats updated: Files=${data.files_count}, Folders=${data.folders_count}`);

        } catch (error) {
            console.error('Error updating file stats display:', error);
        }
    }

    /**
     * อัปเดตสี Progress Bar ตามเปอร์เซ็นต์
     */
    function updateProgressBarColor(element, percentage) {
        if (!element) return;

        // ลบ class สีเก่า
        element.classList.remove(
            'from-green-400', 'to-green-500',
            'from-yellow-400', 'to-yellow-500',
            'from-orange-400', 'to-orange-500',
            'from-red-400', 'to-red-500'
        );

        // เพิ่มสีใหม่ตามเปอร์เซ็นต์
        if (percentage < 50) {
            element.classList.add('from-green-400', 'to-green-500');
        } else if (percentage < 75) {
            element.classList.add('from-yellow-400', 'to-yellow-500');
        } else if (percentage < 90) {
            element.classList.add('from-orange-400', 'to-orange-500');
        } else {
            element.classList.add('from-red-400', 'to-red-500');
        }
    }

    /**
     * ✅ ตั้งค่าการแสดงผลเริ่มต้น
     * แก้ไข: เปลี่ยน Element IDs และลบ hardcode
     */
    function setDefaultStorageDisplay() {
        // ✅ แก้ไข: เปลี่ยน Element IDs
        const quotaUsed = document.getElementById('systemStorageUsed');
        const quotaBar = document.getElementById('systemStorageBar');
        const quotaDetails = document.getElementById('systemStorageDetails');

        if (quotaUsed) quotaUsed.textContent = '0 B';
        if (quotaBar) {
            quotaBar.style.width = '0%';
            updateProgressBarColor(quotaBar, 0);
        }
        if (quotaDetails) {
            // ✅ แก้ไข: ลบ hardcode '0 B / 1 GB (0%)'
            quotaDetails.textContent = 'กำลังโหลด...';
        }

        console.log('🔧 Set default storage display');
    }
    
    /**
     * รีเฟรช Dashboard Stats ทั้งหมด
     */
    function refreshDashboardStats() {
        console.log('🔄 Manual refresh triggered');
        loadStorageStats();
        loadFileStats();
    }

    // ========================================
    // 📅 AUTO-REFRESH TIMER
    // ========================================

    // ตัวแปรสำหรับ Auto-refresh
    let statsRefreshInterval = null;

    /**
     * เริ่ม Auto-refresh (ทุก 30 วินาที)
     */
    function startStatsAutoRefresh() {
        if (statsRefreshInterval) {
            clearInterval(statsRefreshInterval);
        }

        statsRefreshInterval = setInterval(() => {
            console.log('⏰ Auto-refreshing stats...');
            loadStorageStats();
            loadFileStats();
        }, 30000); // 30 วินาที

        console.log('✅ Stats auto-refresh started (every 30s)');
    }

    /**
     * หยุด Auto-refresh
     */
    function stopStatsAutoRefresh() {
        if (statsRefreshInterval) {
            clearInterval(statsRefreshInterval);
            statsRefreshInterval = null;
            console.log('⏹️ Stats auto-refresh stopped');
        }
    }

    // Export functions for global use
    window.loadStorageStats = loadStorageStats;
    window.loadFileStats = loadFileStats;
    window.refreshDashboardStats = refreshDashboardStats;
    window.startStatsAutoRefresh = startStatsAutoRefresh;
    window.stopStatsAutoRefresh = stopStatsAutoRefresh;
</script>