<?php
// application/views/member/google_drive_user_dashboard.php (Fixed Error Handling)
?>
<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">📁 Google Drive กลาง</h1>
            <p class="text-gray-600 mt-2">เข้าใช้งาน Google Drive ขององค์กรผ่าน Google Drive app</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="checkSystemStatus()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>ตรวจสอบสถานะ
            </button>
            <a href="<?php echo site_url('google_drive_user/request_access'); ?>" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>ขอเข้าใช้งาน
            </a>
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

    <!-- System Status Check -->
    <?php 
    $system_available = false;
    $token_valid = false;
    
    // ตรวจสอบ System Storage
    if (isset($available_folders) && !empty($available_folders)) {
        $system_available = true;
    }
    
    // ตรวจสอบ Token status (เพิ่มตรรกะตรวจสอบ)
    $token_status = 'unknown';
    if ($this->db->table_exists('tbl_google_drive_system_storage')) {
        $system_storage = $this->db->where('is_active', 1)->get('tbl_google_drive_system_storage')->row();
        if ($system_storage && $system_storage->google_access_token) {
            $token_data = json_decode($system_storage->google_access_token, true);
            if ($token_data && isset($token_data['access_token'])) {
                if ($system_storage->google_token_expires && strtotime($system_storage->google_token_expires) > time()) {
                    $token_status = 'valid';
                    $token_valid = true;
                } else {
                    $token_status = 'expired';
                }
            } else {
                $token_status = 'invalid';
            }
        } else {
            $token_status = 'missing';
        }
    }
    ?>

    <!-- System Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- System Status -->
        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 <?php echo $system_available ? 'border-green-500' : 'border-red-500'; ?>">
            <div class="flex items-center">
                <div class="p-3 rounded-full <?php echo $system_available ? 'bg-green-100' : 'bg-red-100'; ?>">
                    <i class="fas fa-server text-2xl <?php echo $system_available ? 'text-green-600' : 'text-red-600'; ?>"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">สถานะระบบ</h4>
                    <p class="text-xl font-semibold <?php echo $system_available ? 'text-green-600' : 'text-red-600'; ?>">
                        <?php echo $system_available ? 'พร้อมใช้งาน' : 'ไม่พร้อม'; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Token Status -->
        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 <?php echo $token_valid ? 'border-green-500' : 'border-yellow-500'; ?>">
            <div class="flex items-center">
                <div class="p-3 rounded-full <?php echo $token_valid ? 'bg-green-100' : 'bg-yellow-100'; ?>">
                    <i class="fas fa-key text-2xl <?php echo $token_valid ? 'text-green-600' : 'text-yellow-600'; ?>"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">Access Token</h4>
                    <p class="text-xl font-semibold <?php echo $token_valid ? 'text-green-600' : 'text-yellow-600'; ?>">
                        <?php 
                        $status_text = [
                            'valid' => 'ใช้งานได้',
                            'expired' => 'หมดอายุ',
                            'invalid' => 'ไม่ถูกต้อง',
                            'missing' => 'ไม่พบ',
                            'unknown' => 'ไม่ทราบ'
                        ];
                        echo $status_text[$token_status];
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Shared Folders Count -->
        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-folder-open text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">โฟลเดอร์ที่ใช้งาน</h4>
                    <p class="text-xl font-semibold text-blue-600">
                        <?php echo count($shared_folders ?? []); ?> โฟลเดอร์
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Token Issues Warning -->
    <?php if (!$token_valid): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-2xl text-yellow-600 mr-4 mt-1"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-yellow-800 mb-2">⚠️ ปัญหา Authentication</h3>
                    <div class="text-yellow-700 space-y-2">
                        <?php if ($token_status === 'expired'): ?>
                            <p>• Access Token หมดอายุแล้ว ระบบไม่สามารถแชร์โฟลเดอร์ได้</p>
                            <p>• <strong>วิธีแก้ไข:</strong> แจ้งผู้ดูแลระบบให้ Refresh Token ใหม่</p>
                        <?php elseif ($token_status === 'invalid'): ?>
                            <p>• Access Token ไม่ถูกต้องหรือเสียหาย</p>
                            <p>• <strong>วิธีแก้ไข:</strong> แจ้งผู้ดูแลระบบให้เชื่อมต่อ Google Account ใหม่</p>
                        <?php elseif ($token_status === 'missing'): ?>
                            <p>• ยังไม่ได้เชื่อมต่อ Google Account สำหรับระบบ</p>
                            <p>• <strong>วิธีแก้ไข:</strong> แจ้งผู้ดูแลระบบให้ตั้งค่า System Storage</p>
                        <?php else: ?>
                            <p>• ไม่สามารถตรวจสอบสถานะ Token ได้</p>
                            <p>• <strong>วิธีแก้ไข:</strong> ติดต่อผู้ดูแลระบบ</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-4 flex space-x-3">
                        <button onclick="contactAdmin()" 
                                class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm">
                            <i class="fas fa-phone mr-2"></i>ติดต่อผู้ดูแลระบบ
                        </button>
                        <button onclick="refreshTokenStatus()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            <i class="fas fa-sync-alt mr-2"></i>ตรวจสอบอีกครั้ง
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Google Account Section -->
    <div class="bg-white rounded-xl shadow-lg mb-8">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fab fa-google text-blue-500 mr-2"></i>Google Account ของคุณ
            </h3>
        </div>
        <div class="p-6">
            <?php if ($user_google_account): ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fab fa-google text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($user_google_account); ?></p>
                            <p class="text-sm text-gray-500">Google Account ที่เชื่อมต่อ</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>เชื่อมต่อแล้ว
                    </span>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fab fa-google text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-600 mb-4">ยังไม่ได้เชื่อมต่อ Google Account</p>
                    <p class="text-sm text-gray-500 mb-6">
                        เชื่อมต่อ Google Account เพื่อใช้งาน Google Drive ผ่าน app
                    </p>
                    <a href="<?php echo site_url('google_drive_user/request_access'); ?>" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fab fa-google mr-2"></i>เชื่อมต่อ Google Account
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Available Folders Section -->
    <div class="bg-white rounded-xl shadow-lg mb-8">
        <div class="p-6 border-b border-gray-100">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-folder text-green-500 mr-2"></i>โฟลเดอร์ที่สามารถขอเข้าใช้งาน
                </h3>
                <span class="text-sm text-gray-500">
                    ตามสิทธิ์ของตำแหน่ง: <strong><?php echo htmlspecialchars($user_info->pname ?? 'ไม่ระบุ'); ?></strong>
                </span>
            </div>
        </div>

        <div class="p-6">
            <?php if (!empty($available_folders)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($available_folders as $folder): ?>
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <i class="<?php echo getFolderIcon($folder->folder_type); ?> text-2xl mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-gray-800"><?php echo htmlspecialchars($folder->folder_name); ?></h4>
                                    <p class="text-sm text-gray-500"><?php echo getFolderTypeName($folder->folder_type); ?></p>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <?php 
                            $is_shared = false;
                            if (!empty($shared_folders)) {
                                foreach ($shared_folders as $shared) {
                                    if ($shared->folder_id === $folder->folder_id) {
                                        $is_shared = true;
                                        break;
                                    }
                                }
                            }
                            ?>
                            
                            <?php if ($is_shared): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>ใช้งานได้
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-lock mr-1"></i>ยังไม่ได้แชร์
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if ($folder->folder_description): ?>
                        <p class="text-xs text-gray-600 mb-3"><?php echo htmlspecialchars($folder->folder_description); ?></p>
                        <?php endif; ?>

                        <div class="flex space-x-2">
                            <?php if ($is_shared): ?>
                                <a href="https://drive.google.com/drive/folders/<?php echo $folder->folder_id; ?>" 
                                   target="_blank"
                                   class="flex-1 text-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    <i class="fab fa-google-drive mr-1"></i>เปิดใน Drive
                                </a>
                                <?php if ($token_valid): ?>
                                <button onclick="requestFolderAccess('<?php echo $folder->folder_id; ?>', '<?php echo addslashes($folder->folder_name); ?>')" 
                                        class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm"
                                        title="แชร์ให้ Google Account อื่น">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($token_valid): ?>
                                <button onclick="requestFolderAccess('<?php echo $folder->folder_id; ?>', '<?php echo addslashes($folder->folder_name); ?>')" 
                                        class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                    <i class="fas fa-paper-plane mr-1"></i>ขอเข้าใช้งาน
                                </button>
                                <?php else: ?>
                                <button disabled
                                        class="flex-1 px-3 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed text-sm"
                                        title="ระบบมีปัญหา Token กรุณาติดต่อผู้ดูแลระบบ">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>ระบบมีปัญหา
                                </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-folder-open text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-600 mb-2">ไม่มีโฟลเดอร์ที่สามารถขอเข้าใช้งาน</p>
                    <p class="text-sm text-gray-500">กรุณาติดต่อผู้ดูแลระบบเพื่อสอบถามสิทธิ์การเข้าใช้งาน</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Current Shared Folders -->
    <?php if (!empty($shared_folders)): ?>
    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-share-alt text-purple-500 mr-2"></i>โฟลเดอร์ที่กำลังใช้งาน
            </h3>
            <p class="text-gray-600 text-sm mt-1">โฟลเดอร์ที่ถูกแชร์ให้กับ Google Account ของคุณ</p>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                <?php foreach ($shared_folders as $shared): ?>
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="<?php echo getFolderIcon($shared->folder_type ?? 'system'); ?> text-2xl mr-4"></i>
                        <div>
                            <h4 class="font-medium text-gray-800"><?php echo htmlspecialchars($shared->folder_name ?? 'Unknown Folder'); ?></h4>
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span>แชร์เมื่อ: <?php echo date('d/m/Y H:i', strtotime($shared->shared_at)); ?></span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                    <?php echo ucfirst($shared->permission_level); ?>
                                </span>
                                <?php if ($shared->auto_approved): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                    <i class="fas fa-magic mr-1"></i>อัตโนมัติ
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="https://drive.google.com/drive/folders/<?php echo $shared->folder_id; ?>" 
                           target="_blank"
                           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                            <i class="fab fa-google-drive mr-1"></i>เปิดใน Drive
                        </a>
                        
                        <button onclick="showFolderInfo('<?php echo $shared->folder_id; ?>', '<?php echo addslashes($shared->folder_name ?? ''); ?>')" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            <i class="fas fa-info-circle mr-1"></i>รายละเอียด
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Help & Support Section -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">
            <i class="fas fa-question-circle mr-2"></i>วิธีการใช้งาน Google Drive กลาง
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-blue-700">
            <div>
                <h4 class="font-medium mb-2">📱 ใช้งานผ่าน Google Drive App</h4>
                <ul class="text-sm space-y-1">
                    <li>• ติดตั้ง Google Drive app ในมือถือ/คอมพิวเตอร์</li>
                    <li>• เข้าสู่ระบบด้วย Google Account ที่ได้รับการแชร์</li>
                    <li>• โฟลเดอร์จะปรากฏใน "แชร์กับฉัน" (Shared with me)</li>
                    <li>• คลิกขวาเพื่อ "เพิ่มใน Drive ของฉัน" สำหรับการเข้าถึงที่ง่ายขึ้น</li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-medium mb-2">🔐 ข้อปฏิบัติเพื่อความปลอดภัย</h4>
                <ul class="text-sm space-y-1">
                    <li>• ใช้งานเฉพาะไฟล์ที่เกี่ยวข้องกับงาน</li>
                    <li>• ไม่แชร์ไฟล์ให้บุคคลภายนอกโดยไม่ได้รับอนุญาต</li>
                    <li>• รักษาความปลอดภัยของข้อมูลสำคัญ</li>
                    <li>• แจ้งผู้ดูแลระบบหากพบความผิดปกติ</li>
                </ul>
            </div>
        </div>
        
        <div class="mt-4 flex space-x-3">
            <button onclick="contactAdmin()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                <i class="fas fa-phone mr-2"></i>ติดต่อผู้ดูแลระบบ
            </button>
            <a href="<?php echo site_url('google_drive_user/my_shared_folders'); ?>" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                <i class="fas fa-history mr-2"></i>ประวัติการใช้งาน
            </a>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Request Folder Access Modal -->
<div id="requestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">ขอเข้าใช้งานโฟลเดอร์</h3>
                    <button onclick="closeRequestModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="modalContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Folder Info Modal -->
<div id="infoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">ข้อมูลโฟลเดอร์</h3>
                    <button onclick="closeInfoModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="folderInfoContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ตัวแปรสำหรับจัดการ Token Status
let systemTokenValid = <?php echo $token_valid ? 'true' : 'false'; ?>;
let tokenStatus = '<?php echo $token_status; ?>';

// Enhanced Error Handling Functions
function handleAjaxError(xhr, status, error) {
    console.error('AJAX Error Details:', {
        status: xhr.status,
        statusText: xhr.statusText,
        responseText: xhr.responseText,
        error: error
    });
    
    let errorMessage = 'เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์';
    
    if (xhr.status === 0) {
        errorMessage = 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต';
    } else if (xhr.status === 404) {
        errorMessage = 'ไม่พบหน้าที่ร้องขอ (404)';
    } else if (xhr.status === 500) {
        errorMessage = 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์ (500)';
    } else if (xhr.status === 403) {
        errorMessage = 'ไม่มีสิทธิ์เข้าถึง (403)';
    }
    
    return errorMessage;
}

function safeParseJSON(text) {
    try {
        return JSON.parse(text);
    } catch (e) {
        console.error('JSON Parse Error:', e);
        return null;
    }
}

// ตรวจสอบสถานะระบบ (Enhanced Error Handling)
function checkSystemStatus() {
    showLoading('กำลังตรวจสอบสถานะระบบ...');
    
    fetch('<?php echo site_url('google_drive_user/check_service_status'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.text();
    })
    .then(text => {
        const data = safeParseJSON(text);
        
        if (!data) {
            throw new Error('Invalid JSON response from server');
        }
        
        hideLoading();
        
        if (data.success) {
            let statusHtml = '<div class="space-y-3">';
            statusHtml += '<div class="flex items-center justify-between">';
            statusHtml += '<span>Google Client:</span>';
            statusHtml += '<span class="' + (data.data.google_client_available ? 'text-green-600' : 'text-red-600') + '">' + (data.data.google_client_available ? '✅' : '❌') + '</span>';
            statusHtml += '</div>';
            
            statusHtml += '<div class="flex items-center justify-between">';
            statusHtml += '<span>Drive Service:</span>';
            statusHtml += '<span class="' + (data.data.drive_service_available ? 'text-green-600' : 'text-red-600') + '">' + (data.data.drive_service_available ? '✅' : '❌') + '</span>';
            statusHtml += '</div>';
            
            statusHtml += '<div class="flex items-center justify-between">';
            statusHtml += '<span>System Storage:</span>';
            statusHtml += '<span class="' + (data.data.system_storage_available ? 'text-green-600' : 'text-red-600') + '">' + (data.data.system_storage_available ? '✅' : '❌') + '</span>';
            statusHtml += '</div>';
            
            statusHtml += '<div class="flex items-center justify-between">';
            statusHtml += '<span>Access Token:</span>';
            statusHtml += '<span class="' + (data.data.access_token_valid ? 'text-green-600' : 'text-red-600') + '">' + (data.data.access_token_valid ? '✅' : '❌') + '</span>';
            statusHtml += '</div>';
            
            statusHtml += '<div class="flex items-center justify-between">';
            statusHtml += '<span>Can Share Folders:</span>';
            statusHtml += '<span class="' + (data.data.can_share_folders ? 'text-green-600' : 'text-red-600') + '">' + (data.data.can_share_folders ? '✅' : '❌') + '</span>';
            statusHtml += '</div>';
            statusHtml += '</div>';
            
            // อัปเดตตัวแปร
            systemTokenValid = data.data.access_token_valid && data.data.can_share_folders;
            
            Swal.fire({
                title: 'สถานะระบบ',
                html: statusHtml,
                icon: systemTokenValid ? 'success' : 'warning',
                confirmButtonText: 'ตกลง',
                width: '500px'
            });
            
            // รีเฟรชหน้าถ้าสถานะเปลี่ยน
            if (systemTokenValid && tokenStatus !== 'valid') {
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถตรวจสอบสถานะได้',
                text: data.message || 'ไม่ทราบสาเหตุ',
                confirmButtonText: 'ตกลง'
            });
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Check system status error:', error);
        
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาดในการตรวจสอบสถานะ',
            html: `
                <div class="text-left">
                    <p class="mb-3">ไม่สามารถตรวจสอบสถานะระบบได้</p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <p class="text-sm text-red-700"><strong>Error:</strong> ${error.message}</p>
                        <p class="text-sm text-red-700 mt-2">กรุณาตรวจสอบ:</p>
                        <ul class="text-sm text-red-700 mt-1 space-y-1">
                            <li>• การเชื่อมต่ออินเทอร์เน็ต</li>
                            <li>• สถานะเซิร์ฟเวอร์</li>
                            <li>• Console log ในเบราว์เซอร์</li>
                        </ul>
                    </div>
                </div>
            `,
            confirmButtonText: 'ตกลง',
            width: '600px'
        });
    });
}

// รีเฟรชสถานะ Token
function refreshTokenStatus() {
    checkSystemStatus();
}

// ขอเข้าใช้งานโฟลเดอร์ (Enhanced)
function requestFolderAccess(folderId, folderName) {
    if (!systemTokenValid) {
        Swal.fire({
            icon: 'warning',
            title: 'ระบบมีปัญหา Token',
            html: `
                <div class="text-left">
                    <p class="mb-3">ขณะนี้ระบบมีปัญหาเกี่ยวกับ Access Token ทำให้ไม่สามารถแชร์โฟลเดอร์ได้</p>
                    <p class="text-sm text-gray-600"><strong>สาเหตุ:</strong> ${getTokenStatusMessage()}</p>
                    <p class="text-sm text-gray-600 mt-2"><strong>วิธีแก้ไข:</strong> กรุณาติดต่อผู้ดูแลระบบเพื่อดำเนินการแก้ไข</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'ติดต่อผู้ดูแลระบบ',
            cancelButtonText: 'ตกลง',
            confirmButtonColor: '#f59e0b'
        }).then((result) => {
            if (result.isConfirmed) {
                contactAdmin();
            }
        });
        return;
    }

    document.getElementById('modalContent').innerHTML = `
        <form id="requestForm">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">โฟลเดอร์ที่ขอ</label>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-folder text-blue-500 mr-2"></i>
                        <span class="font-medium">${folderName}</span>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Google Email ของคุณ</label>
                <input type="email" id="userGoogleEmail" required
                       value="${'<?php echo htmlspecialchars($user_google_account ?? ''); ?>'}"
                       placeholder="your.email@gmail.com"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">โฟลเดอร์จะถูกแชร์ให้กับ Google Email นี้</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">เหตุผล (ไม่บังคับ)</label>
                <textarea id="accessReason" rows="3" 
                          placeholder="ระบุเหตุผลในการขอใช้งาน..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="closeRequestModal()" 
                        class="flex-1 px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                    ยกเลิก
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    ส่งคำขอ
                </button>
            </div>
        </form>
    `;

    document.getElementById('requestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('userGoogleEmail').value;
        const reason = document.getElementById('accessReason').value;
        
        if (!email || !email.includes('@')) {
            Swal.fire({
                icon: 'error',
                title: 'กรุณากรอก Google Email',
                text: 'กรุณากรอก Google Email Address ที่ถูกต้อง',
                confirmButtonText: 'ตกลง'
            });
            return;
        }
        
        shareFolder(folderId, email, reason);
    });

    document.getElementById('requestModal').classList.remove('hidden');
}

// แชร์โฟลเดอร์ (Enhanced Error Handling)
function shareFolder(folderId, userEmail, reason) {
    showLoading('กำลังแชร์โฟลเดอร์...');
    
    fetch('<?php echo site_url('google_drive_user/share_folder'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `folder_id=${encodeURIComponent(folderId)}&user_google_email=${encodeURIComponent(userEmail)}&permission_level=reader&reason=${encodeURIComponent(reason)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.text();
    })
    .then(text => {
        const data = safeParseJSON(text);
        
        if (!data) {
            throw new Error('Invalid JSON response from server');
        }
        
        hideLoading();
        closeRequestModal();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'แชร์โฟลเดอร์สำเร็จ!',
                html: `
                    <div class="text-left">
                        <p class="mb-3">โฟลเดอร์ถูกแชร์ให้กับ <strong>${userEmail}</strong> เรียบร้อยแล้ว</p>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-sm text-blue-700"><strong>📱 ขั้นตอนถัดไป:</strong></p>
                            <ol class="text-sm text-blue-700 mt-2 space-y-1">
                                <li>1. เปิด Google Drive app ในมือถือ/คอมพิวเตอร์</li>
                                <li>2. เข้าสู่ระบบด้วย ${userEmail}</li>
                                <li>3. ไปที่ "แชร์กับฉัน" (Shared with me)</li>
                                <li>4. คลิกขวาที่โฟลเดอร์ → "เพิ่มใน Drive ของฉัน"</li>
                            </ol>
                        </div>
                        ${data.data && data.data.folder_url ? '<p class="mt-3 text-sm"><a href="' + data.data.folder_url + '" target="_blank" class="text-blue-600 hover:underline">🔗 เปิดโฟลเดอร์ใน Google Drive</a></p>' : ''}
                    </div>
                `,
                confirmButtonText: 'เข้าใจแล้ว',
                width: '600px'
            }).then(() => {
                location.reload(); // รีเฟรชเพื่อแสดงโฟลเดอร์ใหม่
            });
        } else {
            handleShareError(data);
        }
    })
    .catch(error => {
        hideLoading();
        closeRequestModal();
        console.error('Share folder error:', error);
        
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาดระหว่างการแชร์',
            html: `
                <div class="text-left">
                    <p class="mb-3">ไม่สามารถแชร์โฟลเดอร์ได้</p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <p class="text-sm text-red-700"><strong>Error:</strong> ${error.message}</p>
                        <p class="text-sm text-red-700 mt-2">กรุณาตรวจสอบ:</p>
                        <ul class="text-sm text-red-700 mt-1 space-y-1">
                            <li>• การเชื่อมต่ออินเทอร์เน็ต</li>
                            <li>• สถานะเซิร์ฟเวอร์</li>
                            <li>• ลองใหม่อีกครั้งในอีกสักครู่</li>
                        </ul>
                    </div>
                </div>
            `,
            confirmButtonText: 'ตกลง',
            width: '600px'
        });
    });
}

// Handle Share Error (Enhanced)
function handleShareError(data) {
    const message = data.message || 'ไม่ทราบสาเหตุ';
    
    // ตรวจสอบ error type
    if (message.includes('401') || message.includes('authentication') || message.includes('token')) {
        Swal.fire({
            icon: 'error',
            title: 'ปัญหา Authentication (401)',
            html: `
                <div class="text-left">
                    <p class="mb-3 text-red-600">ไม่สามารถแชร์โฟลเดอร์ได้เนื่องจาก Access Token หมดอายุหรือไม่ถูกต้อง</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                        <p class="text-sm font-medium text-yellow-800">💡 วิธีแก้ไข:</p>
                        <ul class="text-sm text-yellow-700 mt-2 space-y-1">
                            <li>• แจ้งผู้ดูแลระบบให้ Refresh Google Access Token</li>
                            <li>• หรือเชื่อมต่อ Google Account ใหม่ในระบบ</li>
                            <li>• ตรวจสอบว่า Google Account ของระบบยังใช้งานได้</li>
                        </ul>
                    </div>
                    <p class="text-sm text-gray-600">Error: ${message}</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'ติดต่อผู้ดูแลระบบ',
            cancelButtonText: 'ตกลง',
            confirmButtonColor: '#f59e0b',
            width: '600px'
        }).then((result) => {
            if (result.isConfirmed) {
                contactAdmin();
            }
        });
    } else if (message.includes('Organization not found')) {
        Swal.fire({
            icon: 'error',
            title: 'ปัญหาการตั้งค่าระบบ',
            html: `
                <div class="text-left">
                    <p class="mb-3 text-red-600">ระบบไม่พบข้อมูลองค์กร หรือการตั้งค่าไม่ถูกต้อง</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                        <p class="text-sm font-medium text-yellow-800">💡 สาเหตุที่เป็นไปได้:</p>
                        <ul class="text-sm text-yellow-700 mt-2 space-y-1">
                            <li>• ระบบยังไม่ได้ตั้งค่า System Storage</li>
                            <li>• ข้อมูลองค์กรถูกลบหรือเปลี่ยนแปลง</li>
                            <li>• การเชื่อมต่อ API มีปัญหา</li>
                        </ul>
                    </div>
                    <p class="text-sm text-gray-600">กรุณาติดต่อผู้ดูแลระบบเพื่อตรวจสอบการตั้งค่า</p>
                </div>
            `,
            confirmButtonText: 'ติดต่อผู้ดูแลระบบ',
            confirmButtonColor: '#f59e0b',
            width: '600px'
        }).then(() => {
            contactAdmin();
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'ไม่สามารถแชร์โฟลเดอร์ได้',
            html: `
                <div class="text-left">
                    <p class="mb-3">${message}</p>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <p class="text-sm text-gray-600"><strong>💡 คำแนะนำ:</strong></p>
                        <ul class="text-sm text-gray-600 mt-2 space-y-1">
                            <li>• ตรวจสอบ Google Email ที่กรอกให้ถูกต้อง</li>
                            <li>• ลองใหม่อีกครั้งในอีกสักครู่</li>
                            <li>• ติดต่อผู้ดูแลระบบหากปัญหายังคงอยู่</li>
                        </ul>
                    </div>
                </div>
            `,
            confirmButtonText: 'ตกลง',
            width: '600px'
        });
    }
}

// แสดงข้อมูลโฟลเดอร์
function showFolderInfo(folderId, folderName) {
    document.getElementById('folderInfoContent').innerHTML = `
        <div class="space-y-4">
            <div>
                <h4 class="font-medium text-gray-800 mb-2">${folderName}</h4>
                <p class="text-sm text-gray-600">Folder ID: ${folderId}</p>
            </div>
            
            <div class="border-t pt-4">
                <h5 class="font-medium text-gray-700 mb-2">ลิงก์ Google Drive</h5>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <a href="https://drive.google.com/drive/folders/${folderId}" 
                       target="_blank" 
                       class="text-blue-600 hover:underline text-sm break-all">
                        https://drive.google.com/drive/folders/${folderId}
                    </a>
                </div>
            </div>
            
            <div class="border-t pt-4">
                <h5 class="font-medium text-gray-700 mb-2">การใช้งาน</h5>
                <div class="text-sm text-gray-600 space-y-1">
                    <p>• เปิดผ่าน Google Drive app หรือ Web</p>
                    <p>• อัปโหลด/ดาวน์โหลดไฟล์ได้ตามสิทธิ์</p>
                    <p>• แชร์ไฟล์ภายในโฟลเดอร์ให้เพื่อนร่วมงาน</p>
                </div>
            </div>
            
            <div class="flex space-x-3 pt-4">
                <a href="https://drive.google.com/drive/folders/${folderId}" 
                   target="_blank"
                   class="flex-1 text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fab fa-google-drive mr-1"></i>เปิดใน Drive
                </a>
                <button onclick="closeInfoModal()" 
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                    ปิด
                </button>
            </div>
        </div>
    `;

    document.getElementById('infoModal').classList.remove('hidden');
}

// ติดต่อผู้ดูแลระบบ
function contactAdmin() {
    Swal.fire({
        title: 'ติดต่อผู้ดูแลระบบ',
        html: `
            <div class="text-left">
                <p class="mb-4">กรุณาติดต่อผู้ดูแลระบบเพื่อแก้ไขปัญหา Google Drive Token</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-2">📞 ช่องทางติดต่อ:</h4>
                    <div class="text-sm text-blue-700 space-y-1">
                        <p>• อีเมล: admin@organization.com</p>
                        <p>• โทรศัพท์: 02-xxx-xxxx</p>
                        <p>• Line: @admin_support</p>
                        <p>• ระบบ Helpdesk ภายในองค์กร</p>
                    </div>
                </div>
                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-sm text-yellow-700"><strong>🔧 ปัญหาที่ต้องแจ้ง:</strong></p>
                    <p class="text-sm text-yellow-700">"Google Drive Access Token หมดอายุ/มีปัญหา - ไม่สามารถแชร์โฟลเดอร์ได้"</p>
                </div>
            </div>
        `,
        confirmButtonText: 'เข้าใจแล้ว',
        width: '500px'
    });
}

// Helper functions
function getTokenStatusMessage() {
    const messages = {
        'expired': 'Access Token หมดอายุแล้ว',
        'invalid': 'Access Token ไม่ถูกต้องหรือเสียหาย',
        'missing': 'ไม่พบ Access Token ในระบบ',
        'unknown': 'ไม่สามารถตรวจสอบสถานะ Token ได้'
    };
    return messages[tokenStatus] || 'ไม่ทราบสาเหตุ';
}

function closeRequestModal() {
    document.getElementById('requestModal').classList.add('hidden');
}

function closeInfoModal() {
    document.getElementById('infoModal').classList.add('hidden');
}

function showLoading(message = 'กำลังโหลด...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function hideLoading() {
    Swal.close();
}

// Helper functions สำหรับ Icon และชื่อ
<?php
function getFolderIcon($folder_type) {
    $icons = [
        'admin' => 'fas fa-user-shield text-red-500',
        'department' => 'fas fa-building text-blue-500',
        'shared' => 'fas fa-share-alt text-green-500',
        'user' => 'fas fa-user text-purple-500',
        'system' => 'fas fa-cog text-gray-500'
    ];
    
    return $icons[$folder_type] ?? 'fas fa-folder text-gray-500';
}

function getFolderTypeName($folder_type) {
    $names = [
        'admin' => 'ผู้ดูแลระบบ',
        'department' => 'แผนก',
        'shared' => 'ส่วนกลาง',
        'user' => 'ผู้ใช้งาน',
        'system' => 'ระบบ'
    ];
    
    return $names[$folder_type] ?? 'ทั่วไป';
}
?>

// Auto-check token status every 5 minutes
setInterval(function() {
    if (!systemTokenValid) {
        console.log('Checking token status...');
        checkSystemStatus();
    }
}, 300000); // 5 minutes

// Initial check when page loads
document.addEventListener('DOMContentLoaded', function() {
    // แสดงแจ้งเตือนถ้า Token มีปัญหา
    if (!systemTokenValid && tokenStatus !== 'unknown') {
        setTimeout(() => {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-yellow-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>ระบบมีปัญหา Token - กรุณาติดต่อผู้ดูแลระบบ</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            
            // Auto hide after 10 seconds
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    notification.remove();
                }
            }, 10000);
        }, 2000);
    }
    
    // Debug logging
    console.log('Dashboard loaded:', {
        systemTokenValid: systemTokenValid,
        tokenStatus: tokenStatus,
        availableFolders: <?php echo count($available_folders ?? []); ?>,
        sharedFolders: <?php echo count($shared_folders ?? []); ?>
    });
});
</script>