<?php
// application/views/member/google_drive_settings.php - Fixed Version with Working Toggle Switches
?>

<!-- เพิ่ม CSS สำหรับ Toggle Switch -->
<style>
/* Custom Toggle Switch CSS */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 48px;
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
    background-color: #cbd5e0;
    transition: 0.3s;
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
    transition: 0.3s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

input:checked + .toggle-slider {
    background-color: #3b82f6;
}

input:focus + .toggle-slider {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

input:checked + .toggle-slider:before {
    transform: translateX(24px);
}

/* สำหรับ toggle ที่เป็นสีเขียว */
.toggle-green input:checked + .toggle-slider {
    background-color: #10b981;
}

.toggle-green input:focus + .toggle-slider {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
}

/* สำหรับ toggle ที่ถูกปิดใช้งาน */
.toggle-disabled {
    opacity: 0.6;
    pointer-events: none;
}

/* แสดงสถานะ ON/OFF */
.toggle-status {
    margin-left: 12px;
    font-size: 14px;
    font-weight: 500;
    min-width: 35px;
}

.toggle-status.on {
    color: #10b981;
}

.toggle-status.off {
    color: #6b7280;
}
</style>

<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">ตั้งค่า Google Drive</h2>
            <p class="text-gray-600">กำหนดค่าการเชื่อมต่อและการใช้งาน Google Drive</p>
        </div>
        <div class="flex space-x-3">
            
            <button onclick="testConnection()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-link mr-2"></i>ทดสอบการเชื่อมต่อ
            </button>
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

    <!-- Modern Settings Cards -->
    <div class="space-y-6">
        <!-- Google OAuth Settings Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <form method="POST" action="<?php echo site_url('google_drive/settings'); ?>" id="settingsForm">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fab fa-google text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">การตั้งค่า Google OAuth</h3>
                            <p class="text-sm text-gray-600">กำหนดค่าการเชื่อมต่อ Google Drive API</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Google Client ID -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Google Client ID
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="google_client_id" 
                                       value="<?php echo htmlspecialchars($settings['google_client_id'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                       placeholder="ใส่ Google Client ID">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-key text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                ได้จาก Google Cloud Console → APIs & Services → Credentials
                            </p>
                        </div>

                        <!-- Google Client Secret -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Google Client Secret
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       name="google_client_secret" 
                                       value="<?php echo htmlspecialchars($settings['google_client_secret'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                       placeholder="ใส่ Google Client Secret">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" onclick="togglePasswordVisibility(this)" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-shield-alt mr-1"></i>
                                เก็บเป็นความลับ อย่าแชร์ให้ผู้อื่น
                            </p>
                        </div>

                        <!-- Google Redirect URI -->
                        <div class="lg:col-span-2 space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Redirect URI
                            </label>
                            <div class="flex">
                                <input type="url" 
                                       name="google_redirect_uri" 
                                       value="<?php echo htmlspecialchars($settings['google_redirect_uri'] ?? site_url('google_drive/oauth_callback')); ?>"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                       readonly>
                                <button type="button" onclick="copyToClipboard(this.previousElementSibling)" 
                                        class="px-4 py-3 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-copy text-gray-600"></i>
                                </button>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-sm text-blue-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <strong>สำคัญ:</strong> URL นี้ต้องเพิ่มใน Google Cloud Console → APIs & Services → Credentials → OAuth 2.0 Client IDs → Authorized redirect URIs
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        <!-- System Settings Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-cogs text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">การตั้งค่าระบบ</h3>
                        <p class="text-sm text-gray-600">กำหนดค่าการทำงานของระบบ Google Drive</p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Enable Google Drive -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div>
                                <label class="text-sm font-medium text-gray-700">
                                    เปิดใช้งาน Google Drive
                                </label>
                                <p class="text-xs text-gray-500 mt-1">
                                    เปิด/ปิดการใช้งาน Google Drive ทั้งระบบ
                                </p>
                            </div>
                            <div class="flex items-center">
                                <label class="toggle-switch">
                                    <input type="checkbox" 
                                           name="google_drive_enabled" 
                                           id="google_drive_enabled"
                                           value="1"
                                           <?php echo (!empty($settings['google_drive_enabled']) && $settings['google_drive_enabled'] == '1') ? 'checked' : ''; ?>
                                           onchange="handleToggleChange(this, 'google_drive_enabled')">
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-status" id="google_drive_enabled_status">
                                    <?php echo (!empty($settings['google_drive_enabled']) && $settings['google_drive_enabled'] == '1') ? 'เปิด' : 'ปิด'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Auto Create Folders -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div>
                                <label class="text-sm font-medium text-gray-700">
                                    สร้าง Folder อัตโนมัติ
                                </label>
                                <p class="text-xs text-gray-500 mt-1">
                                    สร้าง folder อัตโนมัติตามตำแหน่งเมื่อเชื่อมต่อ
                                </p>
                            </div>
                            <div class="flex items-center">
                                <label class="toggle-switch toggle-green">
                                    <input type="checkbox" 
                                           name="auto_create_folders" 
                                           id="auto_create_folders"
                                           value="1"
                                           <?php echo (!empty($settings['auto_create_folders']) && $settings['auto_create_folders'] == '1') ? 'checked' : ''; ?>
                                           onchange="handleToggleChange(this, 'auto_create_folders')">
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-status" id="auto_create_folders_status">
                                    <?php echo (!empty($settings['auto_create_folders']) && $settings['auto_create_folders'] == '1') ? 'เปิด' : 'ปิด'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Max File Size -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            ขนาดไฟล์สูงสุด
                        </label>
                        <select name="max_file_size" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="52428800" <?php echo (!empty($settings['max_file_size']) && $settings['max_file_size'] == '52428800') ? 'selected' : ''; ?>>50 MB</option>
                            <option value="104857600" <?php echo (empty($settings['max_file_size']) || $settings['max_file_size'] == '104857600') ? 'selected' : ''; ?>>100 MB</option>
                            <option value="209715200" <?php echo (!empty($settings['max_file_size']) && $settings['max_file_size'] == '209715200') ? 'selected' : ''; ?>>200 MB</option>
                            <option value="524288000" <?php echo (!empty($settings['max_file_size']) && $settings['max_file_size'] == '524288000') ? 'selected' : ''; ?>>500 MB</option>
                            <option value="1073741824" <?php echo (!empty($settings['max_file_size']) && $settings['max_file_size'] == '1073741824') ? 'selected' : ''; ?>>1 GB</option>
                        </select>
                        <p class="text-xs text-gray-500">
                            ขนาดไฟล์สูงสุดที่อนุญาตให้อัปโหลด
                        </p>
                    </div>

                    <!-- Allowed File Types -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            ประเภทไฟล์ที่อนุญาต
                        </label>
                        <textarea name="allowed_file_types" 
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                                  placeholder="jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar"><?php echo htmlspecialchars($settings['allowed_file_types'] ?? 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar'); ?></textarea>
                        <p class="text-xs text-gray-500">
                            ใส่นามสกุลไฟล์คั่นด้วยเครื่องหมายจุลภาค (,)
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-info-circle text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">ข้อมูลระบบ</h3>
                        <p class="text-sm text-gray-600">สถิติและข้อมูลการใช้งาน</p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Connected Members -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-green-600 font-medium">API ที่เชื่อมต่อ</p>
                                <p class="text-2xl font-bold text-green-800" id="connectedMembers">
                                    <?php
                                    try {
                                        $connected = $this->db->where('google_drive_enabled', 1)->count_all_results('tbl_member');
                                        echo number_format($connected);
                                    } catch (Exception $e) {
                                        echo '0';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Folders -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-folder text-white text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-blue-600 font-medium">จำนวน Folders</p>
                                <p class="text-2xl font-bold text-blue-800" id="totalFolders">
                                    <?php
                                    try {
                                        $folders = 0;
                                        if ($this->db->table_exists('tbl_google_drive_folders')) {
                                            $folders = $this->db->where('is_active', 1)->count_all_results('tbl_google_drive_folders');
                                        }
                                        echo number_format($folders);
                                    } catch (Exception $e) {
                                        echo '0';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Database Status -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl border border-purple-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-database text-white text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-purple-600 font-medium">สถานะฐานข้อมูล</p>
                                <p class="text-lg font-bold text-purple-800">
                                    <i class="fas fa-check-circle mr-1"></i>พร้อมใช้งาน
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 sm:space-x-4">
                <!-- Left Side Actions -->
                <div class="flex flex-wrap gap-3">
                    <button type="button" 
                            onclick="resetToDefault()" 
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                        <i class="fas fa-undo mr-2"></i>รีเซ็ตเป็นค่าเริ่มต้น
                    </button>
                    <button type="button" 
                            onclick="exportSettings()" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                        <i class="fas fa-download mr-2"></i>Export การตั้งค่า
                    </button>
                    <button type="button" 
                            onclick="previewSettings()" 
                            class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors flex items-center">
                        <i class="fas fa-eye mr-2"></i>ดูตัวอย่าง
                    </button>
                </div>
                
                <!-- Right Side Actions -->
                <div class="flex gap-3">
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center font-medium">
                        <i class="fas fa-save mr-2"></i>บันทึกการตั้งค่า
                    </button>
                </div>
            </div>
        </div>
            </form>
    </div>

    <!-- Help Section -->
    <div class="mt-8 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start">
            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-question-circle text-white text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">
                    วิธีการตั้งค่า Google OAuth
                </h3>
                <div class="text-blue-700 space-y-3">
                    <div>
                        <p class="font-medium">1. สร้าง Project ใน Google Cloud Console:</p>
                        <ul class="ml-4 mt-1 space-y-1 text-sm">
                            <li>• เข้า <a href="https://console.cloud.google.com" target="_blank" class="underline hover:text-blue-900">Google Cloud Console</a></li>
                            <li>• สร้าง Project ใหม่หรือเลือก Project ที่มีอยู่</li>
                        </ul>
                    </div>
                    
                    <div>
                        <p class="font-medium">2. เปิดใช้งาน Google Drive API:</p>
                        <ul class="ml-4 mt-1 space-y-1 text-sm">
                            <li>• ไป APIs & Services → Library</li>
                            <li>• ค้นหา "Google Drive API" และกด Enable</li>
                        </ul>
                    </div>
                    
                    <div>
                        <p class="font-medium">3. สร้าง OAuth 2.0 Credentials:</p>
                        <ul class="ml-4 mt-1 space-y-1 text-sm">
                            <li>• ไป APIs & Services → Credentials</li>
                            <li>• กด "Create Credentials" → "OAuth 2.0 Client ID"</li>
                            <li>• เลือก Application type: "Web application"</li>
                            <li>• เพิ่ม Authorized redirect URI: <code class="bg-white px-2 py-1 rounded text-xs font-mono"><?php echo site_url('google_drive/oauth_callback'); ?></code></li>
                        </ul>
                    </div>
                    
                    <div>
                        <p class="font-medium">4. Copy Client ID และ Client Secret มาใส่ในฟอร์มด้านบน</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for toggle functionality
let toggleInProgress = false;

// ฟังก์ชันอัปเดตสถานะ Toggle
function updateToggleStatus(toggleId, isEnabled) {
    const statusElement = document.getElementById(toggleId + '_status');
    if (statusElement) {
        statusElement.textContent = isEnabled ? 'เปิด' : 'ปิด';
        statusElement.className = isEnabled ? 'toggle-status on' : 'toggle-status off';
    }
}

// Toggle password visibility
function togglePasswordVisibility(button) {
    const input = button.parentElement.previousElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Copy to clipboard
function copyToClipboard(input) {
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Show success message
    const button = input.nextElementSibling;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check text-green-600"></i>';
    button.classList.add('bg-green-100', 'border-green-300');
    
    setTimeout(() => {
        button.innerHTML = originalContent;
        button.classList.remove('bg-green-100', 'border-green-300');
    }, 2000);
}

// Handle Toggle Change - ฟังก์ชันหลักสำหรับ Toggle (ปรับปรุงการจัดการ error)
function handleToggleChange(toggleElement, settingKey) {
    if (toggleInProgress) {
        return false;
    }

    toggleInProgress = true;
    const newValue = toggleElement.checked ? '1' : '0';
    const originalState = !toggleElement.checked;

    // แสดง Loading state
    showToggleLoading(toggleElement, true);

    // ส่งคำขอ AJAX
    fetch('<?php echo site_url('google_drive/toggle_setting'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `setting_key=${encodeURIComponent(settingKey)}&value=${encodeURIComponent(newValue)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        // ตรวจสอบ content-type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Response is not JSON');
        }
        
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            
            if (data.success) {
                // แสดงข้อความสำเร็จ
                showToggleSuccess(settingKey, newValue === '1');
                
                // อัปเดตสถานะ Toggle
                updateToggleStatus(settingKey, newValue === '1');
                
                // อัปเดต UI อื่นๆ ถ้าจำเป็น
                handleToggleUIUpdates(settingKey, newValue === '1');
                
            } else {
                // คืนค่า toggle กลับเป็นเดิม
                toggleElement.checked = originalState;
                
                // แสดงข้อผิดพลาด
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: data.message || 'ไม่สามารถเปลี่ยนแปลงการตั้งค่าได้',
                    confirmButtonText: 'ตกลง'
                });
            }
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            console.error('Response text:', text);
            
            // คืนค่า toggle กลับเป็นเดิม
            toggleElement.checked = originalState;
            
            // แสดงข้อผิดพลาด
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'เซิร์ฟเวอร์ตอบกลับในรูปแบบที่ไม่ถูกต้อง',
                confirmButtonText: 'ตกลง'
            });
        }
    })
    .catch(error => {
        console.error('Toggle error:', error);
        
        // คืนค่า toggle กลับเป็นเดิม
        toggleElement.checked = originalState;
        
        // แสดงข้อผิดพลาดตามประเภท
        let errorMessage = 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง';
        
        if (error.message.includes('404')) {
            errorMessage = 'ไม่พบฟังก์ชัน toggle_setting ในระบบ';
        } else if (error.message.includes('500')) {
            errorMessage = 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์';
        } else if (error.message.includes('not JSON')) {
            errorMessage = 'เซิร์ฟเวอร์ตอบกลับในรูปแบบที่ไม่ถูกต้อง';
        }
        
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ',
            text: errorMessage,
            confirmButtonText: 'ตกลง'
        });
    })
    .finally(() => {
        // ซ่อน Loading state
        showToggleLoading(toggleElement, false);
        toggleInProgress = false;
    });
}

// แสดง/ซ่อน Loading state สำหรับ Toggle
function showToggleLoading(toggleElement, show) {
    const toggleContainer = toggleElement.closest('.flex');
    
    if (show) {
        // เพิ่ม loading class
        toggleElement.disabled = true;
        toggleContainer.style.opacity = '0.6';
        toggleContainer.style.pointerEvents = 'none';
    } else {
        // ลบ loading class
        toggleElement.disabled = false;
        toggleContainer.style.opacity = '1';
        toggleContainer.style.pointerEvents = 'auto';
    }
}

// แสดงข้อความสำเร็จสำหรับ Toggle
function showToggleSuccess(settingKey, enabled) {
    const messages = {
        'google_drive_enabled': enabled ? 'เปิดใช้งาน Google Drive แล้ว' : 'ปิดใช้งาน Google Drive แล้ว',
        'auto_create_folders': enabled ? 'เปิดการสร้าง Folder อัตโนมัติแล้ว' : 'ปิดการสร้าง Folder อัตโนมัติแล้ว',
        'cache_enabled': enabled ? 'เปิดใช้งาน Cache แล้ว' : 'ปิดใช้งาน Cache แล้ว',
        'logging_enabled': enabled ? 'เปิดใช้งาน Logging แล้ว' : 'ปิดใช้งาน Logging แล้ว'
    };

    const message = messages[settingKey] || (enabled ? 'เปิดใช้งานการตั้งค่าแล้ว' : 'ปิดใช้งานการตั้งค่าแล้ว');

    // แสดง Toast notification
    Swal.fire({
        icon: 'success',
        title: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
}

// จัดการ UI Updates เมื่อ Toggle เปลี่ยน
function handleToggleUIUpdates(settingKey, enabled) {
    switch (settingKey) {
        case 'google_drive_enabled':
            if (!enabled) {
                // เมื่อปิด Google Drive แสดงคำเตือน
                setTimeout(() => {
                    Swal.fire({
                        icon: 'info',
                        title: 'Google Drive ถูกปิดใช้งาน',
                        text: 'การเชื่อมต่อที่มีอยู่จะยังคงอยู่ แต่ไม่สามารถเชื่อมต่อใหม่ได้',
                        confirmButtonText: 'เข้าใจแล้ว'
                    });
                }, 2500);
            } else {
                // ตรวจสอบการตั้งค่า OAuth เมื่อเปิด
                const clientId = document.querySelector('input[name="google_client_id"]').value;
                const clientSecret = document.querySelector('input[name="google_client_secret"]').value;
                
                if (!clientId || !clientSecret) {
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'warning',
                            title: 'ยังไม่ได้ตั้งค่า OAuth',
                            text: 'กรุณาใส่ Google Client ID และ Client Secret เพื่อให้ระบบทำงานได้อย่างสมบูรณ์',
                            confirmButtonText: 'เข้าใจแล้ว'
                        });
                    }, 2500);
                }
            }
            break;

        case 'auto_create_folders':
            if (!enabled) {
                setTimeout(() => {
                    Swal.fire({
                        icon: 'info',
                        title: 'การสร้าง Folder อัตโนมัติถูกปิด',
                        text: 'สมาชิกจะต้องสร้าง Folder ด้วยตนเองเมื่อเชื่อมต่อ Google Drive',
                        confirmButtonText: 'เข้าใจแล้ว'
                    });
                }, 2500);
            }
            break;

        case 'logging_enabled':
            if (!enabled) {
                setTimeout(() => {
                    Swal.fire({
                        icon: 'warning',
                        title: 'การบันทึก Log ถูกปิด',
                        text: 'ระบบจะไม่บันทึกประวัติการใช้งาน Google Drive อีกต่อไป',
                        confirmButtonText: 'เข้าใจแล้ว'
                    });
                }, 2500);
            }
            break;
    }
}

// โหลดสถานะ Toggle เมื่อหน้าโหลดเสร็จ
document.addEventListener('DOMContentLoaded', function() {
    // เริ่มต้นด้วยการใช้ค่า default จาก HTML
    useDefaultToggleStatus();
    
    // จากนั้นลองโหลดจากเซิร์ฟเวอร์ (ถ้ามี API)
    setTimeout(() => {
        loadToggleStatus();
    }, 500);
});

// โหลดสถานะ Toggle ทั้งหมด
function loadToggleStatus() {
    // ตรวจสอบว่ามี function get_all_toggle_status หรือไม่ก่อน
    fetch('<?php echo site_url('google_drive/get_all_toggle_status'); ?>', {
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
        
        // ตรวจสอบว่า response มี content-type เป็น JSON หรือไม่
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Response is not JSON');
        }
        
        return response.text(); // ใช้ text() แทน json() เพื่อ debug
    })
    .then(text => {
        try {
            // ลองแปลง text เป็น JSON
            const data = JSON.parse(text);
            
            if (data.success && data.data) {
                // อัปเดตสถานะ Toggle ทั้งหมด
                Object.keys(data.data).forEach(settingKey => {
                    const toggleElement = document.getElementById(settingKey);
                    if (toggleElement) {
                        toggleElement.checked = data.data[settingKey].boolean_value;
                        updateToggleStatus(settingKey, data.data[settingKey].boolean_value);
                    }
                });
                console.log('Toggle status loaded successfully');
            } else {
                console.warn('Toggle status response:', data);
                // ใช้ค่า default จาก HTML
                useDefaultToggleStatus();
            }
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            console.error('Response text:', text);
            // ใช้ค่า default จาก HTML
            useDefaultToggleStatus();
        }
    })
    .catch(error => {
        console.error('Load toggle status error:', error);
        // ใช้ค่า default จาก HTML เมื่อเกิด error
        useDefaultToggleStatus();
    });
}

// ใช้ค่า default จาก HTML element
function useDefaultToggleStatus() {
    console.log('Using default toggle status from HTML');
    
    // อ่านค่าจาก checkbox elements ที่มีอยู่
    const toggles = ['google_drive_enabled', 'auto_create_folders'];
    
    toggles.forEach(settingKey => {
        const toggleElement = document.getElementById(settingKey);
        if (toggleElement) {
            const isChecked = toggleElement.checked;
            updateToggleStatus(settingKey, isChecked);
            console.log(`${settingKey}: ${isChecked ? 'เปิด' : 'ปิด'}`);
        }
    });
}

// Test Connection Function - Fixed JSON Response
function testConnection() {
    const clientId = document.querySelector('input[name="google_client_id"]').value;
    const clientSecret = document.querySelector('input[name="google_client_secret"]').value;
    const redirectUri = document.querySelector('input[name="google_redirect_uri"]').value;

    // Validation
    if (!clientId || !clientSecret) {
        Swal.fire({
            icon: 'warning',
            title: 'ข้อมูลไม่ครบ',
            text: 'กรุณาใส่ Google Client ID และ Client Secret',
            confirmButtonText: 'ตกลง'
        });
        return;
    }

    // Show loading
    Swal.fire({
        title: 'ทดสอบการเชื่อมต่อ',
        text: 'กำลังทดสอบการเชื่อมต่อ Google Drive...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Send test request
    fetch('<?php echo site_url('google_drive/test_connection'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `client_id=${encodeURIComponent(clientId)}&client_secret=${encodeURIComponent(clientSecret)}&redirect_uri=${encodeURIComponent(redirectUri)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            let resultHtml = '<div class="text-left">';
            resultHtml += '<p class="text-green-600 mb-3"><i class="fas fa-check-circle mr-2"></i>การทดสอบผ่านทุกขั้นตอน</p>';
            
            if (data.data && data.data.test_results) {
                const results = data.data.test_results;
                
                if (results.oauth_status && results.oauth_status.success) {
                    resultHtml += '<p class="text-sm text-gray-600 mb-1">✅ OAuth Configuration: ถูกต้อง</p>';
                }
                
                if (results.drive_api_status) {
                    if (results.drive_api_status.success) {
                        resultHtml += '<p class="text-sm text-gray-600 mb-1">✅ Google Drive API: พร้อมใช้งาน</p>';
                    } else {
                        resultHtml += '<p class="text-sm text-orange-600 mb-1">⚠️ Google Drive API: ' + results.drive_api_status.message + '</p>';
                    }
                }
                
                if (results.library_version) {
                    resultHtml += '<p class="text-sm text-gray-600 mb-1">📦 Library Version: ' + results.library_version + '</p>';
                }
                
                if (results.timestamp) {
                    resultHtml += '<p class="text-xs text-gray-500 mt-2">ทดสอบเมื่อ: ' + results.timestamp + '</p>';
                }
            }
            resultHtml += '</div>';

            Swal.fire({
                icon: 'success',
                title: 'การเชื่อมต่อสำเร็จ',
                html: resultHtml,
                width: '500px',
                confirmButtonText: 'ตกลง'
            });
        } else {
            let errorHtml = '<div class="text-left">';
            errorHtml += '<p class="text-red-600 mb-3">' + (data.message || 'การเชื่อมต่อล้มเหลว') + '</p>';
            
            if (data.debug) {
                errorHtml += '<p class="text-xs text-gray-500 mb-3">Debug: ' + JSON.stringify(data.debug) + '</p>';
            }
            
            errorHtml += '<div class="mt-4 p-3 bg-yellow-50 rounded">';
            errorHtml += '<p class="text-sm font-medium text-yellow-800 mb-2">💡 คำแนะนำ:</p>';
            errorHtml += '<ul class="text-sm text-yellow-700 space-y-1">';
            errorHtml += '<li>• ตรวจสอบ Client ID ให้ลงท้ายด้วย .apps.googleusercontent.com</li>';
            errorHtml += '<li>• ตรวจสอบ Client Secret ไม่มีช่องว่าง</li>';
            errorHtml += '<li>• ตรวจสอบ Redirect URI ใน Google Console ให้ตรงกัน</li>';
            errorHtml += '<li>• ตรวจสอบ Google Drive API ถูก Enable แล้ว</li>';
            errorHtml += '</ul>';
            errorHtml += '</div>';
            errorHtml += '</div>';

            Swal.fire({
                icon: 'error',
                title: 'การเชื่อมต่อล้มเหลว',
                html: errorHtml,
                width: '600px',
                confirmButtonText: 'ตกลง'
            });
        }
    })
    .catch(error => {
        console.error('Test connection error:', error);
        
        let errorHtml = '<div class="text-left">';
        errorHtml += '<p class="text-red-600 mb-3">ไม่สามารถติดต่อเซิร์ฟเวอร์ได้</p>';
        errorHtml += '<div class="mt-4 p-3 bg-red-50 rounded">';
        errorHtml += '<p class="text-sm text-red-700">กรุณาตรวจสอบ:</p>';
        errorHtml += '<ul class="text-sm text-red-700 mt-1 space-y-1">';
        errorHtml += '<li>• การเชื่อมต่ออินเทอร์เน็ต</li>';
        errorHtml += '<li>• ฟังก์ชัน test_connection ใน Controller</li>';
        errorHtml += '<li>• Log ของเซิร์ฟเวอร์</li>';
        errorHtml += '<li>• Error: ' + error.message + '</li>';
        errorHtml += '</ul>';
        errorHtml += '</div>';
        errorHtml += '</div>';

        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            html: errorHtml,
            width: '500px',
            confirmButtonText: 'ตกลง'
        });
    });
}

// Reset to default
function resetToDefault() {
    Swal.fire({
        title: 'ยืนยันการรีเซ็ต',
        text: 'คุณต้องการรีเซ็ตการตั้งค่าเป็นค่าเริ่มต้นหรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'รีเซ็ต',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            // Reset values
            document.querySelector('input[name="google_client_id"]').value = '';
            document.querySelector('input[name="google_client_secret"]').value = '';
            document.querySelector('input[name="google_redirect_uri"]').value = '<?php echo site_url('google_drive/oauth_callback'); ?>';
            document.querySelector('input[name="google_drive_enabled"]').checked = true;
            document.querySelector('input[name="auto_create_folders"]').checked = true;
            document.querySelector('select[name="max_file_size"]').value = '104857600';
            document.querySelector('textarea[name="allowed_file_types"]').value = 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar';

            // อัปเดตสถานะ Toggle
            updateToggleStatus('google_drive_enabled', true);
            updateToggleStatus('auto_create_folders', true);

            Swal.fire({
                icon: 'success',
                title: 'รีเซ็ตเรียบร้อย',
                text: 'ค่าการตั้งค่าถูกรีเซ็ตเป็นค่าเริ่มต้นแล้ว',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

// Export settings
function exportSettings() {
    const settings = {
        google_client_id: document.querySelector('input[name="google_client_id"]').value,
        google_redirect_uri: document.querySelector('input[name="google_redirect_uri"]').value,
        google_drive_enabled: document.querySelector('input[name="google_drive_enabled"]').checked,
        auto_create_folders: document.querySelector('input[name="auto_create_folders"]').checked,
        max_file_size: document.querySelector('select[name="max_file_size"]').value,
        allowed_file_types: document.querySelector('textarea[name="allowed_file_types"]').value
    };

    const dataStr = JSON.stringify(settings, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    
    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = 'google_drive_settings_' + new Date().toISOString().slice(0, 10) + '.json';
    link.click();

    Swal.fire({
        icon: 'success',
        title: 'Export สำเร็จ',
        text: 'ไฟล์การตั้งค่าถูกดาวน์โหลดแล้ว',
        timer: 1500,
        showConfirmButton: false
    });
}

// Preview settings
function previewSettings() {
    const settings = {
        'Google Client ID': document.querySelector('input[name="google_client_id"]').value || 'ไม่ได้ตั้งค่า',
        'Redirect URI': document.querySelector('input[name="google_redirect_uri"]').value,
        'เปิดใช้งาน Google Drive': document.querySelector('input[name="google_drive_enabled"]').checked ? 'เปิด' : 'ปิด',
        'สร้าง Folder อัตโนมัติ': document.querySelector('input[name="auto_create_folders"]').checked ? 'เปิด' : 'ปิด',
        'ขนาดไฟล์สูงสุด': Math.round(document.querySelector('select[name="max_file_size"]').value / 1048576) + ' MB',
        'ประเภทไฟล์ที่อนุญาต': document.querySelector('textarea[name="allowed_file_types"]').value
    };

    let html = '<div class="text-left">';
    for (const [key, value] of Object.entries(settings)) {
        html += `<p class="mb-2"><strong>${key}:</strong> <span class="text-gray-600">${value}</span></p>`;
    }
    html += '</div>';

    Swal.fire({
        title: 'ตัวอย่างการตั้งค่า',
        html: html,
        width: '600px',
        confirmButtonText: 'ตกลง'
    });
}

// Form validation
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    const clientId = document.querySelector('input[name="google_client_id"]').value;
    const clientSecret = document.querySelector('input[name="google_client_secret"]').value;
    const driveEnabled = document.querySelector('input[name="google_drive_enabled"]').checked;

    if (driveEnabled && (!clientId || !clientSecret)) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'ข้อมูลไม่ครบ',
            text: 'กรุณาใส่ Google Client ID และ Client Secret เมื่อเปิดใช้งาน Google Drive',
            confirmButtonText: 'ตกลง'
        });
        return false;
    }

    // Show loading
    Swal.fire({
        title: 'กำลังบันทึก...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
});
</script>