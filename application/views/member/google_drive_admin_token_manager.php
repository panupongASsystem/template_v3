<?php
// application/views/member/google_drive_admin_token_manager.php
?>
<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-red-600">🔧 Token Management (Admin)</h1>
            <p class="text-gray-600 mt-2">จัดการ Access Token ที่หมดอายุและแก้ไขปัญหา Authentication</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="refreshPage()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-sync-alt mr-2"></i>รีเฟรช
            </button>
            
        </div>
    </div>

    <!-- ✅ Dynamic Alert Box - แสดงตามสถานะจริง -->
    <div id="dynamicAlertBox" class="mb-8">
        <!-- Alert content will be loaded here by JavaScript -->
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Refresh Token -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-refresh text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="font-semibold text-gray-800">Refresh Token</h4>
                    <p class="text-sm text-gray-600">ลองใช้ Refresh Token ที่มีอยู่</p>
                </div>
            </div>
            <button onclick="attemptTokenRefresh()" 
                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-sync-alt mr-2"></i>ลอง Refresh Token
            </button>
        </div>

        <!-- Reconnect Google -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fab fa-google text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="font-semibold text-gray-800">เชื่อมต่อใหม่</h4>
                    <p class="text-sm text-gray-600">เชื่อมต่อ Google Account ใหม่</p>
                </div>
            </div>
            <button onclick="reconnectGoogle()" 
                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fab fa-google mr-2"></i>เชื่อมต่อใหม่
            </button>
        </div>

        <!-- Emergency Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-tools text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="font-semibold text-gray-800">เครื่องมือแก้ไข</h4>
                    <p class="text-sm text-gray-600">Debug และแก้ไขปัญหา</p>
                </div>
            </div>
            <button onclick="openDebugTools()" 
                    class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                <i class="fas fa-bug mr-2"></i>Debug Tools
            </button>
        </div>
    </div>

    <!-- Current Status -->
    <div class="bg-white rounded-xl shadow-lg mb-8">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>สถานะปัจจุบัน
            </h3>
        </div>
        <div class="p-6">
            <div id="currentStatus" class="space-y-4">
                <!-- Status will be loaded here -->
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">กำลังตรวจสอบสถานะ...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Token Details -->
    <div class="bg-white rounded-xl shadow-lg mb-8">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-key text-purple-500 mr-2"></i>รายละเอียด Token
            </h3>
        </div>
        <div class="p-6">
            <div id="tokenDetails" class="space-y-4">
                <!-- Token details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Activity Log -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-6 border-b border-gray-100">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-history text-indigo-500 mr-2"></i>Log การดำเนินการ
                </h3>
                <button onclick="clearActivityLog()" 
                        class="px-3 py-1 text-sm bg-gray-500 text-white rounded hover:bg-gray-600">
                    <i class="fas fa-trash mr-1"></i>ล้าง Log
                </button>
            </div>
        </div>
        <div class="p-6">
            <div id="activityLog" class="space-y-3 max-h-96 overflow-y-auto">
                <!-- Activity log will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- เหมือนเดิม: Modals และส่วนอื่นๆ ไม่เปลี่ยน -->
<!-- Debug Tools Modal -->
<div id="debugModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">🔧 Debug Tools</h3>
                    <button onclick="closeDebugModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="debugContent" class="space-y-6">
                    <!-- Debug content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reconnect Modal -->
<div id="reconnectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">🔄 จัดการ Google Account</h3>
                    <button onclick="closeReconnectModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <!-- Warning Alert -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="font-medium text-yellow-800 mb-2">⚠️ คำเตือนสำคัญ</h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• การเชื่อมต่อใหม่จะเขียนทับ Token เดิม</li>
                            <li>• การตัดการเชื่อมต่อจะทำให้ระบบหยุดทำงาน</li>
                            <li>• <strong>เฉพาะ System Admin เท่านั้น</strong>ที่สามารถดำเนินการได้</li>
                            <li>• แนะนำให้ใช้ Google Account เดียวกับเดิม</li>
                        </ul>
                    </div>

                    <!-- Current Account Info -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Google Account ปัจจุบัน
                        </label>
                        <div class="p-3 bg-gray-50 rounded-lg border">
                            <div class="flex items-center space-x-3">
                                <i class="fab fa-google text-xl text-blue-600"></i>
                                <span id="currentGoogleAccount" class="text-gray-800 font-medium">กำลังโหลด...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <!-- Disconnect Button -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h4 class="font-medium text-red-800 mb-2">🔓 ตัดการเชื่อมต่อ Account เดิม</h4>
                            <p class="text-sm text-red-700 mb-3">
                                ตัดการเชื่อมต่อ Google Account ปัจจุบัน (ระบบจะหยุดทำงาน)
                            </p>
                            <button onclick="disconnectCurrentAccount()" 
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-unlink mr-2"></i>ตัดการเชื่อมต่อ
                            </button>
                        </div>

                        <!-- Reconnect Button -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-800 mb-2">🔄 เชื่อมต่อ Account ใหม่</h4>
                            <p class="text-sm text-blue-700 mb-3">
                                เชื่อมต่อ Google Account ใหม่หรือเดียวกับเดิม
                            </p>
                            <button onclick="startReconnectProcess()" 
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fab fa-google mr-2"></i>เชื่อมต่อใหม่
                            </button>
                        </div>
                    </div>

                    <!-- Cancel Button -->
                    <div class="pt-3 border-t">
                        <button onclick="closeReconnectModal()" 
                                class="w-full px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>ยกเลิก
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let activityLogData = [];
let statusCheckInterval;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadCurrentStatus();
    loadTokenDetails();
    initActivityLog();
    
    // Auto refresh status every 30 seconds
    statusCheckInterval = setInterval(loadCurrentStatus, 30000);
});

// ✅ Load current system status และสร้าง Dynamic Alert
function loadCurrentStatus() {
    addToActivityLog('กำลังตรวจสอบสถานะระบบ...', 'info');
    
    fetch('<?php echo site_url('google_drive_system/check_service_status'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateStatusDisplay(data.data);
            updateDynamicAlert(data.data); // ✅ เพิ่มการอัปเดต Alert
            addToActivityLog('ตรวจสอบสถานะเรียบร้อย', 'success');
        } else {
            addToActivityLog('ไม่สามารถตรวจสอบสถานะได้: ' + data.message, 'error');
            showErrorAlert('ไม่สามารถตรวจสอบสถานะได้: ' + data.message);
        }
    })
    .catch(error => {
        addToActivityLog('เกิดข้อผิดพลาดในการตรวจสอบสถานะ', 'error');
        showErrorAlert('เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์');
        console.error('Status check error:', error);
    });
}

// ✅ สร้างฟังก์ชันใหม่สำหรับ Dynamic Alert
function updateDynamicAlert(status) {
    const alertBox = document.getElementById('dynamicAlertBox');
    
    // ตรวจสอบสถานะเพื่อแสดง Alert ที่เหมาะสม
    if (!status.access_token_valid || !status.can_share_folders) {
        // ❌ มีปัญหา - แสดง Alert แดง
        alertBox.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600 mr-4 mt-1"></i>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-800 text-xl mb-2">🚨 ปัญหาหลัก: Access Token หมดอายุ</h3>
                        <div class="text-red-700 space-y-2">
                            <p>• Google Drive API ไม่สามารถใช้งานได้เนื่องจาก Access Token หมดอายุ</p>
                            <p>• ผู้ใช้ไม่สามารถแชร์โฟลเดอร์ใหม่ได้</p>
                            <p>• ระบบต้องการ Refresh Token หรือเชื่อมต่อ Google Account ใหม่</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else if (status.token_expires_at) {
        // ✅ ตรวจสอบเวลาหมดอายุ
        const expiresTime = new Date(status.token_expires_at).getTime();
        const currentTime = Date.now();
        const timeDiff = expiresTime - currentTime;
        const minutesLeft = Math.max(0, Math.floor(timeDiff / (1000 * 60)));
        
        if (minutesLeft <= 30 && minutesLeft > 5) {
            // ⚠️ ใกล้หมดอายุ - แสดง Alert เหลือง
            alertBox.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <i class="fas fa-clock text-3xl text-yellow-600 mr-4 mt-1"></i>
                        <div class="flex-1">
                            <h3 class="font-bold text-yellow-800 text-xl mb-2">⚠️ คำเตือน: Token ใกล้หมดอายุ</h3>
                            <div class="text-yellow-700 space-y-2">
                                <p>• Access Token จะหมดอายุใน ${minutesLeft} นาที</p>
                                <p>• ระบบจะทำการ Auto-refresh อัตโนมัติ</p>
                                <p>• หากต้องการแน่ใจ สามารถ Refresh Token ด้วยตนเอง</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else if (minutesLeft <= 5 && minutesLeft > 0) {
            // 🔥 วิกฤต - แสดง Alert ส้ม
            alertBox.innerHTML = `
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-3xl text-orange-600 mr-4 mt-1"></i>
                        <div class="flex-1">
                            <h3 class="font-bold text-orange-800 text-xl mb-2">🔥 วิกฤต: Token หมดอายุภายใน ${minutesLeft} นาที!</h3>
                            <div class="text-orange-700 space-y-2">
                                <p>• ควร Refresh Token ทันที</p>
                                <p>• ระบบอาจหยุดทำงานได้ทุกเมื่อ</p>
                                <p>• คลิก "ลอง Refresh Token" ด้านล่าง</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // ✅ ทุกอย่างปกติ - แสดง Alert เขียว
            alertBox.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-3xl text-green-600 mr-4 mt-1"></i>
                        <div class="flex-1">
                            <h3 class="font-bold text-green-800 text-xl mb-2">✅ ระบบทำงานปกติ</h3>
                            <div class="text-green-700 space-y-2">
                                <p>• Google Drive API ทำงานได้ปกติ</p>
                                <p>• ผู้ใช้สามารถแชร์โฟลเดอร์ได้</p>
                                <p>• Token จะหมดอายุใน ${minutesLeft} นาที</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    } else {
        // ✅ ไม่มีข้อมูลวันหมดอายุ แต่ทำงานได้
        alertBox.innerHTML = `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-3xl text-blue-600 mr-4 mt-1"></i>
                    <div class="flex-1">
                        <h3 class="font-bold text-blue-800 text-xl mb-2">ℹ️ ข้อมูลระบบ</h3>
                        <div class="text-blue-700 space-y-2">
                            <p>• ระบบทำงานได้ปกติ</p>
                            <p>• ไม่มีข้อมูลการหมดอายุของ Token</p>
                            <p>• แนะนำให้ตรวจสอบและ Refresh Token เป็นประจำ</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

// ✅ แสดง Error Alert
function showErrorAlert(message) {
    document.getElementById('dynamicAlertBox').innerHTML = `
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-3xl text-red-600 mr-4 mt-1"></i>
                <div class="flex-1">
                    <h3 class="font-bold text-red-800 text-xl mb-2">❌ เกิดข้อผิดพลาด</h3>
                    <div class="text-red-700">
                        <p>${message}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Update status display (ไม่เปลี่ยนแปลง)
function updateStatusDisplay(status) {
    const statusHtml = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 border rounded-lg ${status.google_client_available ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'}">
                    <span class="font-medium">Google Client Library</span>
                    <span class="${status.google_client_available ? 'text-green-600' : 'text-red-600'}">
                        ${status.google_client_available ? '✅ พร้อมใช้งาน' : '❌ ไม่พร้อม'}
                        ${status.use_curl_mode ? ' (cURL Mode)' : ''}
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-4 border rounded-lg ${status.drive_service_available ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'}">
                    <span class="font-medium">Drive Service</span>
                    <span class="${status.drive_service_available ? 'text-green-600' : 'text-red-600'}">
                        ${status.drive_service_available ? '✅ พร้อมใช้งาน' : '❌ ไม่พร้อม'}
                        ${status.use_curl_mode ? ' (cURL Mode)' : ''}
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-4 border rounded-lg ${status.system_storage_available ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'}">
                    <span class="font-medium">System Storage</span>
                    <span class="${status.system_storage_available ? 'text-green-600' : 'text-red-600'}">
                        ${status.system_storage_available ? '✅ พร้อมใช้งาน' : '❌ ไม่พร้อม'}
                    </span>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 border rounded-lg ${status.access_token_valid ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'}">
                    <span class="font-medium">Access Token</span>
                    <span class="${status.access_token_valid ? 'text-green-600' : 'text-red-600'}">
                        ${status.access_token_valid ? '✅ ใช้งานได้' : '❌ หมดอายุ/ไม่ถูกต้อง'}
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-4 border rounded-lg ${status.can_share_folders ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'}">
                    <span class="font-medium">การแชร์โฟลเดอร์</span>
                    <span class="${status.can_share_folders ? 'text-green-600' : 'text-red-600'}">
                        ${status.can_share_folders ? '✅ ใช้งานได้' : '❌ ใช้งานไม่ได้'}
                    </span>
                </div>
                
                ${status.token_expires_at ? `
                <div class="p-4 border border-blue-200 bg-blue-50 rounded-lg">
                    <span class="font-medium text-blue-800">Token หมดอายุ:</span>
                    <span class="text-blue-700">${new Date(status.token_expires_at).toLocaleString('th-TH')}</span>
                </div>
                ` : ''}
            </div>
        </div>
    `;
    
    document.getElementById('currentStatus').innerHTML = statusHtml;
}

// เหลือส่วนอื่นๆ เหมือนเดิม...
// (ไม่ต้องเปลี่ยนแปลงส่วนอื่น)

// ✅ Load token details 
function loadTokenDetails() {
    fetch('<?php echo site_url('google_drive_system/debug_token_details'); ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateTokenDetailsDisplay(data.data);
        } else {
            document.getElementById('tokenDetails').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i class="fas fa-exclamation-triangle text-3xl mb-4"></i>
                    <p>ไม่สามารถโหลดรายละเอียด Token ได้</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Token details error:', error);
    });
}

// Update token details display
function updateTokenDetailsDisplay(tokenData) {
    const detailsHtml = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Google Account</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-800">${tokenData.google_email || 'ไม่ทราบ'}</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Token Type</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-800">${tokenData.token_type || 'Bearer'}</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">สถานะ</label>
                    <div class="mt-1 p-3 rounded-lg ${tokenData.is_valid ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'}">
                        <span>${tokenData.is_valid ? '✅ ใช้งานได้' : '❌ หมดอายุ/ไม่ถูกต้อง'}</span>
                    </div>
                </div>
            </div>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">วันที่หมดอายุ</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-800">${tokenData.expires_at ? new Date(tokenData.expires_at).toLocaleString('th-TH') : 'ไม่ทราบ'}</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Refresh Token</label>
                    <div class="mt-1 p-3 rounded-lg ${tokenData.has_refresh_token ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'}">
                        <span>${tokenData.has_refresh_token ? '✅ มี' : '❌ ไม่มี'}</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">เชื่อมต่อเมื่อ</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-800">${tokenData.connected_at ? new Date(tokenData.connected_at).toLocaleString('th-TH') : 'ไม่ทราบ'}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('tokenDetails').innerHTML = detailsHtml;
}

// ✅ Attempt token refresh
function attemptTokenRefresh() {
    addToActivityLog('เริ่มต้นการ Refresh Token...', 'info');
    
    Swal.fire({
        title: 'กำลัง Refresh Token',
        text: 'กรุณารอสักครู่...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('<?php echo site_url('google_drive_system/refresh_system_token'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success) {
            addToActivityLog('✅ Refresh Token สำเร็จ!', 'success');
            
            Swal.fire({
                icon: 'success',
                title: 'Refresh Token สำเร็จ!',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Access Token ถูก Refresh เรียบร้อยแล้ว</p>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <p class="text-sm text-green-700"><strong>✅ ผลลัพธ์:</strong></p>
                            <ul class="text-sm text-green-700 mt-2 space-y-1">
                                <li>• Token ใหม่ใช้งานได้แล้ว</li>
                                <li>• ระบบสามารถแชร์โฟลเดอร์ได้</li>
                                <li>• ผู้ใช้สามารถใช้งานปกติ</li>
                            </ul>
                        </div>
                    </div>
                `,
                confirmButtonText: 'เยี่ยม!',
                width: '500px'
            }).then(() => {
                // Refresh the page data
                loadCurrentStatus();
                loadTokenDetails();
            });
        } else {
            addToActivityLog('❌ Refresh Token ล้มเหลว: ' + data.message, 'error');
            
            Swal.fire({
                icon: 'error',
                title: 'Refresh Token ล้มเหลว',
                html: `
                    <div class="text-left">
                        <p class="mb-3">${data.message}</p>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-sm text-yellow-800"><strong>💡 วิธีแก้ไข:</strong></p>
                            <ul class="text-sm text-yellow-700 mt-2 space-y-1">
                                <li>• ลองเชื่อมต่อ Google Account ใหม่</li>
                                <li>• ตรวจสอบการตั้งค่า OAuth Credentials</li>
                                <li>• ตรวจสอบสิทธิ์ของ Google Account</li>
                            </ul>
                        </div>
                    </div>
                `,
                confirmButtonText: 'เข้าใจแล้ว',
                width: '600px'
            });
        }
    })
    .catch(error => {
        Swal.close();
        addToActivityLog('❌ เกิดข้อผิดพลาดในการ Refresh Token', 'error');
        
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้',
            confirmButtonText: 'ตกลง'
        });
    });
}

// Reconnect Google Account
function reconnectGoogle() {
    // Load current Google account info
    fetch('<?php echo site_url('google_drive_system/get_current_google_account'); ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('currentGoogleAccount').textContent = data.data.google_email || 'ไม่ทราบ';
        }
    })
    .catch(error => {
        document.getElementById('currentGoogleAccount').textContent = 'ไม่สามารถโหลดได้';
    });
    
    document.getElementById('reconnectModal').classList.remove('hidden');
}

// Start reconnect process
function startReconnectProcess() {
    closeReconnectModal();
    addToActivityLog('เริ่มกระบวนการเชื่อมต่อ Google Account ใหม่...', 'info');
    
    Swal.fire({
        title: 'เตรียมเชื่อมต่อ Google Account',
        text: 'ระบบกำลังเตรียม OAuth URL...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Redirect to Google OAuth
    setTimeout(() => {
        window.location.href = '<?php echo site_url('google_drive_system/connect_system_account'); ?>';
    }, 2000);
}

	
	
	// ✅ เพิ่มฟังก์ชัน disconnectCurrentAccount ที่หายไป
function disconnectCurrentAccount() {
    Swal.fire({
        title: '⚠️ ยืนยันการตัดการเชื่อมต่อ',
        html: `
            <div class="text-left">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <h4 class="font-bold text-red-800 mb-2">🚨 คำเตือนสำคัญ</h4>
                    <ul class="text-sm text-red-700 space-y-1">
                        <li>• จะตัดการเชื่อมต่อ Google Account หลักของระบบ</li>
                        <li>• <strong>ระบบ Google Drive จะหยุดทำงานทันที</strong></li>
                        <li>• ผู้ใช้ทั้งหมดจะไม่สามารถแชร์โฟลเดอร์ได้</li>
                        <li>• ต้องเชื่อมต่อ Google Account ใหม่เพื่อใช้งานต่อ</li>
                    </ul>
                </div>
                <p class="text-gray-700">กรุณาพิมพ์ <code class="bg-gray-100 px-2 py-1 rounded">DISCONNECT_SYSTEM_GOOGLE_ACCOUNT</code> เพื่อยืนยัน:</p>
            </div>
        `,
        input: 'text',
        inputPlaceholder: 'พิมพ์รหัสยืนยันที่นี่...',
        showCancelButton: true,
        confirmButtonText: '⚠️ ตัดการเชื่อมต่อ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        inputValidator: (value) => {
            if (value !== 'DISCONNECT_SYSTEM_GOOGLE_ACCOUNT') {
                return 'รหัสยืนยันไม่ถูกต้อง!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            addToActivityLog('🔓 เริ่มตัดการเชื่อมต่อ System Google Account...', 'warning');
            performDisconnectSystemAccount();
        }
    });
}

// ✅ ฟังก์ชันทำการตัดการเชื่อมต่อจริง
function performDisconnectSystemAccount() {
    Swal.fire({
        title: 'กำลังตัดการเชื่อมต่อ...',
        html: `
            <div class="space-y-3">
                <div class="flex items-center justify-center">
                    <i class="fas fa-spinner fa-spin text-3xl text-red-600"></i>
                </div>
                <p>กำลังดำเนินการตัดการเชื่อมต่อ Google Account</p>
                <div class="text-sm text-gray-600">
                    <p>• กำลัง Revoke Google Token...</p>
                    <p>• กำลังปิดการใช้งาน System Storage...</p>
                    <p>• กำลังอัปเดตสถานะระบบ...</p>
                </div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });

    // 🎯 Enhanced Fetch with better error handling
    fetch('<?php echo site_url('google_drive_system/disconnect_system_account'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json' // บอก server ว่าเราต้องการ JSON
        },
        body: 'confirm_disconnect=DISCONNECT_SYSTEM_GOOGLE_ACCOUNT'
    })
    .then(response => {
        console.log('🔍 Disconnect Response Status:', response.status);
        console.log('🔍 Response Headers:', [...response.headers.entries()]);
        
        // ✅ ตรวจสอบ Content-Type ก่อน
        const contentType = response.headers.get('content-type');
        console.log('🔍 Content-Type:', contentType);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        // ✅ ตรวจสอบว่าเป็น JSON จริงหรือไม่
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // 🚨 ถ้าไม่ใช่ JSON แสดงว่าได้ HTML error page
            return response.text().then(htmlContent => {
                console.error('❌ Received HTML instead of JSON:');
                console.error('First 500 chars:', htmlContent.substring(0, 500));
                
                // พยายามหา error message จาก HTML
                let errorMsg = 'Server returned HTML error page instead of JSON';
                
                // ลองหา PHP error message
                const phpErrorMatch = htmlContent.match(/Fatal error:(.+?)in\s/i);
                if (phpErrorMatch) {
                    errorMsg = 'PHP Error: ' + phpErrorMatch[1].trim();
                }
                
                // ลองหา CodeIgniter error
                const ciErrorMatch = htmlContent.match(/<h1>(.+?)<\/h1>/i);
                if (ciErrorMatch) {
                    errorMsg = 'CodeIgniter Error: ' + ciErrorMatch[1].trim();
                }
                
                throw new Error(errorMsg);
            });
        }
    })
    .then(data => {
        console.log('✅ Disconnect Response Data:', data);
        
        if (data && data.success) {
            addToActivityLog('✅ ตัดการเชื่อมต่อ System Google Account สำเร็จ', 'success');
            
            Swal.fire({
                icon: 'success',
                title: '✅ ตัดการเชื่อมต่อสำเร็จ',
                html: `
                    <div class="text-left">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <h4 class="font-bold text-green-800 mb-2">✅ การตัดการเชื่อมต่อเสร็จสิ้น</h4>
                            <ul class="text-sm text-green-700 space-y-1">
                                <li>• Google Account: <strong>${data.data.disconnected_account}</strong></li>
                                <li>• ตัดการเชื่อมต่อเมื่อ: ${data.data.disconnected_at}</li>
                                <li>• Revoke Token: ${data.data.revoke_success ? 'สำเร็จ' : 'ไม่สำเร็จ (Token อาจหมดอายุแล้ว)'}</li>
                                <li>• โฟลเดอร์ที่ปิด: ${data.data.folders_disabled} โฟลเดอร์</li>
                                <li>• สมาชิกที่อัปเดต: ${data.data.members_updated} คน</li>
                            </ul>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-bold text-blue-800 mb-2">📋 ขั้นตอนต่อไป</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• ระบบจะเปลี่ยนเส้นทางไปหน้าตั้งค่า</li>
                                <li>• ต้องเชื่อมต่อ Google Account ใหม่</li>
                                <li>• แนะนำให้ใช้ Google Account เดียวกับเดิม</li>
                            </ul>
                        </div>
                    </div>
                `,
                confirmButtonText: 'ไปหน้าตั้งค่า',
                allowOutsideClick: false
            }).then(() => {
                // เปลี่ยนเส้นทางไปหน้าตั้งค่า
                window.location.href = '<?php echo site_url('google_drive_system/setup'); ?>';
            });
        } else {
            // ✅ API ส่ง success: false
            const errorMessage = data?.message || 'ไม่สามารถตัดการเชื่อมต่อได้';
            addToActivityLog('❌ ตัดการเชื่อมต่อไม่สำเร็จ: ' + errorMessage, 'error');
            
            Swal.fire({
                icon: 'error',
                title: '❌ การตัดการเชื่อมต่อล้มเหลว',
                html: `
                    <div class="text-left">
                        <p class="mb-3">${errorMessage}</p>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-sm text-yellow-800"><strong>💡 วิธีแก้ไข:</strong></p>
                            <ul class="text-sm text-yellow-700 mt-2 space-y-1">
                                <li>• ตรวจสอบสิทธิ์ Admin</li>
                                <li>• ตรวจสอบการเชื่อมต่อเครือข่าย</li>
                                <li>• ลองใหม่อีกครั้ง</li>
                                <li>• หากยังไม่ได้ติดต่อผู้ดูแลระบบ</li>
                            </ul>
                        </div>
                        ${data?.data?.error_type ? `
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-3">
                                <p class="text-sm text-red-800"><strong>🔧 Debug Info:</strong></p>
                                <ul class="text-sm text-red-700 mt-1">
                                    <li>• Error Type: ${data.data.error_type}</li>
                                    <li>• File: ${data.data.error_file}</li>
                                    <li>• Line: ${data.data.error_line}</li>
                                </ul>
                            </div>
                        ` : ''}
                    </div>
                `,
                confirmButtonText: 'เข้าใจแล้ว'
            });
        }
        
        // รีเฟรชสถานะหน้าเว็บ
        setTimeout(() => {
            closeReconnectModal();
            loadCurrentStatus();
            loadTokenDetails();
        }, 1000);
        
    })
    .catch(error => {
        console.error('❌ Disconnect System Account Error:', error);
        addToActivityLog('❌ เกิดข้อผิดพลาดในการตัดการเชื่อมต่อ: ' + error.message, 'error');
        
        // 🎯 จำแนกประเภท Error เพื่อแสดงคำแนะนำที่เหมาะสม
        let errorTitle = '❌ เกิดข้อผิดพลาด';
        let errorMessage = error.message;
        let troubleshooting = '';
        
        if (error.message.includes('HTTP 500')) {
            errorTitle = '🔥 เซิร์ฟเวอร์มีปัญหา (HTTP 500)';
            errorMessage = 'มีข้อผิดพลาดภายในเซิร์ฟเวอร์ กรุณาติดต่อผู้ดูแลระบบ';
            troubleshooting = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-3">
                    <h4 class="font-medium text-red-800 mb-2">🔧 สาเหตุที่เป็นไปได้:</h4>
                    <ul class="text-sm text-red-700 space-y-1">
                        <li>• PHP Error ใน method disconnect_system_account()</li>
                        <li>• Database connection error</li>
                        <li>• Missing table หรือ column</li>
                        <li>• PHP memory limit exceeded</li>
                        <li>• File permission issues</li>
                    </ul>
                    <p class="text-sm text-red-700 mt-2"><strong>👉 ตรวจสอบ PHP Error Log เพื่อดูรายละเอียด</strong></p>
                </div>
            `;
        } else if (error.message.includes('PHP Error')) {
            errorTitle = '🐛 PHP Error';
            troubleshooting = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
                    <h4 class="font-medium text-yellow-800 mb-2">🔧 วิธีแก้ไข PHP Error:</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• ตรวจสอบ syntax error ใน PHP code</li>
                        <li>• ตรวจสอบ missing functions หรือ methods</li>
                        <li>• ตรวจสอบ PHP version compatibility</li>
                        <li>• อัปเดตโค้ดตาม error-proof version</li>
                    </ul>
                </div>
            `;
        } else if (error.message.includes('CodeIgniter Error')) {
            errorTitle = '⚙️ CodeIgniter Error';
            troubleshooting = `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-3">
                    <h4 class="font-medium text-blue-800 mb-2">🔧 วิธีแก้ไข CodeIgniter Error:</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• ตรวจสอบ routing configuration</li>
                        <li>• ตรวจสอบ controller และ method names</li>
                        <li>• ตรวจสอบ .htaccess file</li>
                        <li>• ตรวจสอบ base_url ใน config</li>
                    </ul>
                </div>
            `;
        } else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
            errorTitle = '🌐 การเชื่อมต่อขัดข้อง';
            errorMessage = 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้';
            troubleshooting = `
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mt-3">
                    <h4 class="font-medium text-gray-800 mb-2">🔧 วิธีแก้ไข Network Error:</h4>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• ตรวจสอบการเชื่อมต่ออินเทอร์เน็ต</li>
                        <li>• ตรวจสอบว่าเซิร์ฟเวอร์ทำงานปกติ</li>
                        <li>• ตรวจสอบ firewall หรือ proxy</li>
                        <li>• ลองรีเฟรชหน้าเว็บ</li>
                    </ul>
                </div>
            `;
        }
        
        Swal.fire({
            icon: 'error',
            title: errorTitle,
            html: `
                <div class="text-left">
                    <p class="mb-3">${errorMessage}</p>
                    ${troubleshooting}
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mt-3">
                        <h4 class="font-medium text-purple-800 mb-2">🔄 ทางเลือก:</h4>
                        <ul class="text-sm text-purple-700 space-y-1">
                            <li>• ลองรีเฟรชหน้าเว็บและทำใหม่</li>
                            <li>• ใช้ Debug Tools เพื่อตรวจสอบระบบ</li>
                            <li>• ติดต่อผู้ดูแลระบบเพื่อช่วยเหลือ</li>
                        </ul>
                    </div>
                </div>
            `,
            confirmButtonText: 'เข้าใจแล้ว',
            width: '600px'
        });
        
        console.error('Full Error Object:', error);
    });
}
	

// Open debug tools
function openDebugTools() {
    addToActivityLog('เปิดเครื่องมือ Debug...', 'info');
    
    document.getElementById('debugContent').innerHTML = `
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button onclick="runDiagnostics()" 
                        class="p-4 border border-blue-200 rounded-lg hover:bg-blue-50 text-left">
                    <div class="flex items-center">
                        <i class="fas fa-stethoscope text-2xl text-blue-600 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-800">Full Diagnostics</h4>
                            <p class="text-sm text-gray-600">ตรวจสอบระบบทั้งหมด</p>
                        </div>
                    </div>
                </button>
                
                <button onclick="testGoogleAPI()" 
                        class="p-4 border border-green-200 rounded-lg hover:bg-green-50 text-left">
                    <div class="flex items-center">
                        <i class="fab fa-google text-2xl text-green-600 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-800">Test Google API</h4>
                            <p class="text-sm text-gray-600">ทดสอบการเชื่อมต่อ API</p>
                        </div>
                    </div>
                </button>
                
                <button onclick="viewSystemLogs()" 
                        class="p-4 border border-purple-200 rounded-lg hover:bg-purple-50 text-left">
                    <div class="flex items-center">
                        <i class="fas fa-file-alt text-2xl text-purple-600 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-800">System Logs</h4>
                            <p class="text-sm text-gray-600">ดู Log ของระบบ</p>
                        </div>
                    </div>
                </button>
                
                <button onclick="resetSystemStorage()" 
                        class="p-4 border border-red-200 rounded-lg hover:bg-red-50 text-left">
                    <div class="flex items-center">
                        <i class="fas fa-redo text-2xl text-red-600 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-800">Reset System</h4>
                            <p class="text-sm text-gray-600">รีเซ็ตระบบ Storage</p>
                        </div>
                    </div>
                </button>
            </div>
            
            <div id="debugResults" class="mt-6">
                <!-- Debug results will appear here -->
            </div>
        </div>
    `;
    
    document.getElementById('debugModal').classList.remove('hidden');
}

// Run diagnostics
function runDiagnostics() {
    document.getElementById('debugResults').innerHTML = `
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin text-blue-600 mr-3"></i>
                <span class="text-blue-800">กำลังทำการตรวจสอบระบบ...</span>
            </div>
        </div>
    `;
    
    fetch('<?php echo site_url('google_drive_system/run_complete_diagnostics'); ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        let resultsHtml = '<div class="space-y-4">';
        
        if (data.success) {
            data.data.tests.forEach(test => {
                const statusColor = test.passed ? 'green' : 'red';
                const statusIcon = test.passed ? 'check-circle' : 'times-circle';
                
                resultsHtml += `
                    <div class="p-4 border border-${statusColor}-200 bg-${statusColor}-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-${statusIcon} text-${statusColor}-600 mr-3"></i>
                            <div class="flex-1">
                                <h4 class="font-medium text-${statusColor}-800">${test.name}</h4>
                                <p class="text-sm text-${statusColor}-700">${test.result}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            resultsHtml += `
                <div class="p-4 border border-red-200 bg-red-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                        <span class="text-red-800">ไม่สามารถทำการตรวจสอบได้: ${data.message}</span>
                    </div>
                </div>
            `;
        }
        
        resultsHtml += '</div>';
        document.getElementById('debugResults').innerHTML = resultsHtml;
    })
    .catch(error => {
        document.getElementById('debugResults').innerHTML = `
            <div class="p-4 border border-red-200 bg-red-50 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                    <span class="text-red-800">เกิดข้อผิดพลาดในการตรวจสอบ</span>
                </div>
            </div>
        `;
    });
}

// Test Google API
function testGoogleAPI() {
    document.getElementById('debugResults').innerHTML = `
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin text-green-600 mr-3"></i>
                <span class="text-green-800">กำลังทดสอบ Google API...</span>
            </div>
        </div>
    `;
    
    // Test API functionality
    fetch('<?php echo site_url('google_drive_system/test_google_api_access'); ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const statusColor = data.success ? 'green' : 'red';
        const statusIcon = data.success ? 'check-circle' : 'times-circle';
        
        document.getElementById('debugResults').innerHTML = `
            <div class="p-4 border border-${statusColor}-200 bg-${statusColor}-50 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-${statusIcon} text-${statusColor}-600 mr-3"></i>
                    <div>
                        <h4 class="font-medium text-${statusColor}-800">Google API Test</h4>
                        <p class="text-sm text-${statusColor}-700">${data.message}</p>
                        ${data.data ? `<pre class="mt-2 text-xs text-${statusColor}-600 bg-white p-2 rounded">${JSON.stringify(data.data, null, 2)}</pre>` : ''}
                    </div>
                </div>
            </div>
        `;
    })
    .catch(error => {
        document.getElementById('debugResults').innerHTML = `
            <div class="p-4 border border-red-200 bg-red-50 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-times-circle text-red-600 mr-3"></i>
                    <span class="text-red-800">ไม่สามารถทดสอบ API ได้</span>
                </div>
            </div>
        `;
    });
}

// Placeholder functions
function viewSystemLogs() {
    document.getElementById('debugResults').innerHTML = `
        <div class="p-4 border border-purple-200 bg-purple-50 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-purple-600 mr-3"></i>
                <span class="text-purple-800">ฟีเจอร์ดู System Logs จะเพิ่มเติมในอนาคต</span>
            </div>
        </div>
    `;
}

function resetSystemStorage() {
    Swal.fire({
        title: 'รีเซ็ตระบบ Storage?',
        text: 'การทำงานนี้จะลบข้อมูล Storage ทั้งหมด',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, รีเซ็ต!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('debugResults').innerHTML = `
                <div class="p-4 border border-red-200 bg-red-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-red-600 mr-3"></i>
                        <span class="text-red-800">ฟีเจอร์รีเซ็ตระบบจะเพิ่มเติมในอนาคต</span>
                    </div>
                </div>
            `;
        }
    });
}

// Activity Log functions
function initActivityLog() {
    addToActivityLog('เริ่มต้น Token Management System', 'info');
}

function addToActivityLog(message, type = 'info') {
    const timestamp = new Date().toLocaleString('th-TH');
    const logEntry = {
        timestamp: timestamp,
        message: message,
        type: type
    };
    
    activityLogData.unshift(logEntry);
    
    // Keep only last 50 entries
    if (activityLogData.length > 50) {
        activityLogData = activityLogData.slice(0, 50);
    }
    
    updateActivityLogDisplay();
}

function updateActivityLogDisplay() {
    const logHtml = activityLogData.map(entry => {
        const typeColors = {
            'info': 'text-blue-600 bg-blue-50 border-blue-200',
            'success': 'text-green-600 bg-green-50 border-green-200',
            'error': 'text-red-600 bg-red-50 border-red-200',
            'warning': 'text-yellow-600 bg-yellow-50 border-yellow-200'
        };
        
        const typeIcons = {
            'info': 'fas fa-info-circle',
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-circle',
            'warning': 'fas fa-exclamation-triangle'
        };
        
        return `
            <div class="flex items-start space-x-3 p-3 border rounded-lg ${typeColors[entry.type]}">
                <i class="${typeIcons[entry.type]} mt-1"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium">${entry.message}</p>
                    <p class="text-xs opacity-75">${entry.timestamp}</p>
                </div>
            </div>
        `;
    }).join('');
    
    document.getElementById('activityLog').innerHTML = logHtml || `
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-history text-3xl text-gray-300 mb-4"></i>
            <p>ยังไม่มี Log</p>
        </div>
    `;
}

function clearActivityLog() {
    activityLogData = [];
    updateActivityLogDisplay();
}

// Modal functions
function closeDebugModal() {
    document.getElementById('debugModal').classList.add('hidden');
}

function closeReconnectModal() {
    document.getElementById('reconnectModal').classList.add('hidden');
}

function refreshPage() {
    location.reload();
}

// Cleanup interval when leaving page
window.addEventListener('beforeunload', function() {
    if (statusCheckInterval) {
        clearInterval(statusCheckInterval);
    }
});
</script>