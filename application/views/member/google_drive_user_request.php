<?php
// application/views/member/google_drive_user_request.php
?>
<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">📋 ขอเข้าใช้งาน Google Drive</h1>
            <p class="text-gray-600 mt-2">กรอกข้อมูลเพื่อขอเข้าใช้งาน Google Drive ผ่าน Google Drive app</p>
        </div>
        <div class="flex space-x-3">
            <a href="<?php echo site_url('google_drive_user'); ?>" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>กลับ
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
    <?php if (!$system_storage || !$system_storage->folder_structure_created): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-6 py-4 rounded-lg mb-8">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-2xl mr-4"></i>
                <div>
                    <h3 class="font-semibold">ระบบยังไม่พร้อมใช้งาน</h3>
                    <p class="mt-1">Google Drive Storage ยังไม่ได้ตั้งค่า กรุณาติดต่อผู้ดูแลระบบ</p>
                </div>
            </div>
        </div>
    <?php else: ?>

    <!-- Request Form -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <form method="POST" action="<?php echo site_url('google_drive_user/request_access'); ?>" id="requestForm">
            
            <!-- User Information -->
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user text-blue-500 mr-2"></i>ข้อมูลผู้ขอ
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ชื่อ-นามสกุล</label>
                        <input type="text" value="<?php echo htmlspecialchars($user_info->m_fname . ' ' . $user_info->m_lname); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ตำแหน่ง</label>
                        <input type="text" value="<?php echo htmlspecialchars($user_info->pname ?? 'ไม่ระบุ'); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                    </div>
                </div>
            </div>

            <!-- Google Account -->
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fab fa-google text-blue-500 mr-2"></i>Google Account
                </h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Google Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="user_google_email" required
                           value="<?php echo htmlspecialchars($user_info->google_email ?? ''); ?>"
                           placeholder="example@gmail.com"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <p class="text-sm text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        ใส่ Google Email ที่คุณใช้งาน Google Drive (Gmail, G Suite, หรือ Google Workspace)
                    </p>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-2">📱 เตรียมความพร้อม Google Drive App</h4>
                    <div class="text-blue-700 text-sm space-y-2">
                        <p>• ตรวจสอบว่าคุณมี <strong>Google Drive app</strong> ในมือถือ/คอมพิวเตอร์แล้ว</p>
                        <p>• ตรวจสอบว่าคุณ <strong>เข้าสู่ระบบ</strong> ด้วย Google Email ที่กรอกด้านบนแล้ว</p>
                        <p>• หลังจากได้รับการแชร์ โฟลเดอร์จะปรากฏใน <strong>"แชร์กับฉัน"</strong> (Shared with me)</p>
                    </div>
                </div>
            </div>

            <!-- Folder Selection -->
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-folder text-green-500 mr-2"></i>เลือกโฟลเดอร์ที่ต้องการเข้าใช้งาน
                </h3>
                
                <p class="text-gray-600 text-sm mb-4">เลือกโฟลเดอร์ที่คุณต้องการเข้าใช้งานผ่าน Google Drive app</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php 
                    $available_folders = [];
                    if ($system_storage && $this->db->table_exists('tbl_google_drive_system_folders')) {
                        $query = $this->db->select('*')
                                         ->from('tbl_google_drive_system_folders')
                                         ->where('is_active', 1);
                        
                        $position_id = $user_info->ref_pid;
                        
                        // กำหนดสิทธิ์ตามตำแหน่ง
                        if (in_array($position_id, [1, 2])) {
                            // Admin - ทุกโฟลเดอร์
                        } elseif ($position_id == 3) {
                            // User Admin - ยกเว้น Admin folder
                            $query->where('folder_type !=', 'admin');
                        } else {
                            // End User - เฉพาะตำแหน่งและ Shared
                            $query->where('(folder_type = "shared" OR created_for_position = ' . $position_id . ')');
                        }
                        
                        $available_folders = $query->order_by('folder_type', 'ASC')
                                                  ->order_by('folder_name', 'ASC')
                                                  ->get()
                                                  ->result();
                    }
                    ?>
                    
                    <?php if (!empty($available_folders)): ?>
                        <?php foreach ($available_folders as $folder): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" name="requested_folders[]" 
                                       value="<?php echo $folder->folder_id; ?>"
                                       class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center mb-2">
                                        <i class="<?php echo getFolderIcon($folder->folder_type); ?> text-xl mr-2"></i>
                                        <div>
                                            <h4 class="font-medium text-gray-800"><?php echo htmlspecialchars($folder->folder_name); ?></h4>
                                            <p class="text-sm text-gray-500"><?php echo getFolderTypeName($folder->folder_type); ?></p>
                                        </div>
                                    </div>
                                    
                                    <?php if ($folder->folder_description): ?>
                                    <p class="text-xs text-gray-600"><?php echo htmlspecialchars($folder->folder_description); ?></p>
                                    <?php endif; ?>
                                </div>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-8 text-gray-500">
                            <i class="fas fa-folder-open text-4xl text-gray-300 mb-4"></i>
                            <p>ไม่มีโฟลเดอร์ที่สามารถขอเข้าถึงได้</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4 flex items-center">
                    <button type="button" onclick="selectAllFolders()" 
                            class="text-blue-600 hover:text-blue-800 text-sm mr-4">
                        <i class="fas fa-check-square mr-1"></i>เลือกทั้งหมด
                    </button>
                    <button type="button" onclick="unselectAllFolders()" 
                            class="text-gray-600 hover:text-gray-800 text-sm">
                        <i class="fas fa-square mr-1"></i>ยกเลิกทั้งหมด
                    </button>
                </div>
            </div>

            <!-- Reason -->
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-comment text-purple-500 mr-2"></i>เหตุผลในการขอใช้งาน
                </h3>
                
                <textarea name="access_reason" rows="4" 
                          placeholder="กรุณาระบุเหตุผลในการขอเข้าใช้งาน Google Drive (เช่น สำหรับงานประจำ, โครงการพิเศษ, ฯลฯ)"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                <p class="text-sm text-gray-500 mt-1">ข้อมูลนี้จะช่วยให้ผู้ดูแลระบบสามารถพิจารณาคำขอได้อย่างเหมาะสม</p>
            </div>

            <!-- Terms and Conditions -->
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-shield-alt text-red-500 mr-2"></i>ข้อตกลงและเงื่อนไข
                </h3>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="text-sm text-gray-700 space-y-2">
                        <p><strong>📋 การใช้งาน Google Drive ขององค์กร:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>ใช้งานเฉพาะไฟล์ที่เกี่ยวข้องกับงานขององค์กรเท่านั้น</li>
                            <li>ไม่นำไฟล์ส่วนตัวเข้ามาเก็บในโฟลเดอร์ขององค์กร</li>
                            <li>ไม่แชร์ไฟล์ให้บุคคลภายนอกโดยไม่ได้รับอนุญาต</li>
                            <li>รักษาความปลอดภัยของข้อมูลและไม่เปิดเผยข้อมูลสำคัญ</li>
                        </ul>
                        
                        <p class="mt-3"><strong>🔒 ความปลอดภัย:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>ใช้ Google Account ที่มีความปลอดภัยสูง (2FA แนะนำ)</li>
                            <li>ไม่เข้าใช้งานจากอุปกรณ์สาธารณะหรือไม่ปลอดภัย</li>
                            <li>แจ้งผู้ดูแลระบบทันทีหากพบความผิดปกติ</li>
                        </ul>
                        
                        <p class="mt-3"><strong>⚠️ ข้อจำกัด:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>ผู้ดูแลระบบสามารถเพิกถอนสิทธิ์ได้ตลอดเวลา</li>
                            <li>การใช้งานจะถูกบันทึกและตรวจสอบได้</li>
                            <li>ห้ามดาวน์โหลดไฟล์จำนวนมากเพื่อเก็บไว้ใช้ส่วนตัว</li>
                        </ul>
                    </div>
                </div>
                
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" name="accept_terms" required
                           class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-3 text-sm text-gray-700">
                        ข้าพเจ้ายอมรับ<strong>ข้อตกลงและเงื่อนไข</strong>ในการใช้งาน Google Drive ขององค์กร 
                        และจะปฏิบัติตามกฎระเบียบที่กำหนดอย่างเคร่งครัด
                    </span>
                </label>
            </div>

            <!-- Submit -->
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-clock mr-1"></i>
                        คำขอจะได้รับการพิจารณาภายใน 1-2 วันทำการ
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="<?php echo site_url('google_drive_user'); ?>" 
                           class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                            ยกเลิก
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-paper-plane mr-2"></i>ส่งคำขอ
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <?php endif; ?>

    <!-- Help Section -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">
            <i class="fas fa-question-circle mr-2"></i>คำถามที่พบบ่อย
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-blue-700">
            <div>
                <h4 class="font-medium mb-2">Q: ต้องใช้ Google Email ประเภทไหน?</h4>
                <p class="text-sm">A: Gmail, G Suite, Google Workspace หรือ Email ที่เชื่อมต่อกับ Google Account</p>
                
                <h4 class="font-medium mb-2 mt-4">Q: ใช้เวลานานแค่ไหนในการอนุมัติ?</h4>
                <p class="text-sm">A: ปกติ 1-2 วันทำการ สำหรับ Admin อาจอนุมัติทันที</p>
            </div>
            
            <div>
                <h4 class="font-medium mb-2">Q: สามารถใช้ Google Drive app ได้เลยหรือไม่?</h4>
                <p class="text-sm">A: ได้ หลังจากได้รับการแชร์โฟลเดอร์แล้ว</p>
                
                <h4 class="font-medium mb-2 mt-4">Q: ต้องการความช่วยเหลือ?</h4>
                <p class="text-sm">A: ติดต่อผู้ดูแลระบบหรือ IT Support</p>
            </div>
        </div>
    </div>
</div>

<script>
// Form Functions
function selectAllFolders() {
    const checkboxes = document.querySelectorAll('input[name="requested_folders[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function unselectAllFolders() {
    const checkboxes = document.querySelectorAll('input[name="requested_folders[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Form Validation
document.getElementById('requestForm').addEventListener('submit', function(e) {
    const email = document.querySelector('input[name="user_google_email"]').value;
    const folders = document.querySelectorAll('input[name="requested_folders[]"]:checked');
    const terms = document.querySelector('input[name="accept_terms"]').checked;
    
    // ตรวจสอบ Email
    if (!email || !email.includes('@')) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'กรุณากรอก Google Email',
            text: 'กรุณากรอก Google Email Address ที่ถูกต้อง',
            confirmButtonText: 'ตกลง'
        });
        return;
    }
    
    // ตรวจสอบว่าเป็น Google Email
    const googleEmailPattern = /@(gmail\.com|.*\.edu|.*\.ac\.th|.*\.gov|.*\.go\.th)$/;
    if (!googleEmailPattern.test(email)) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'ตรวจสอบ Google Email',
            text: 'กรุณาใส่ Google Email ที่ถูกต้อง (Gmail, G Suite, หรือ Google Workspace)',
            confirmButtonText: 'ตกลง'
        });
        return;
    }
    
    // ตรวจสอบการเลือกโฟลเดอร์
    if (folders.length === 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'กรุณาเลือกโฟลเดอร์',
            text: 'กรุณาเลือกอย่างน้อย 1 โฟลเดอร์ที่ต้องการเข้าใช้งาน',
            confirmButtonText: 'ตกลง'
        });
        return;
    }
    
    // ตรวจสอบการยอมรับเงื่อนไข
    if (!terms) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'กรุณายอมรับเงื่อนไข',
            text: 'กรุณายอมรับข้อตกลงและเงื่อนไขในการใช้งาน',
            confirmButtonText: 'ตกลง'
        });
        return;
    }
    
    // แสดงข้อความยืนยัน
    e.preventDefault();
    Swal.fire({
        title: 'ยืนยันการส่งคำขอ',
        html: `
            <div class="text-left">
                <p><strong>Google Email:</strong> ${email}</p>
                <p><strong>โฟลเดอร์ที่เลือก:</strong> ${folders.length} โฟลเดอร์</p>
                <p class="mt-3 text-sm text-gray-600">คำขอจะถูกส่งไปยังผู้ดูแลระบบเพื่อพิจารณา</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ส่งคำขอ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#16a34a'
    }).then((result) => {
        if (result.isConfirmed) {
            // ส่งฟอร์ม
            this.submit();
        }
    });
});

// Helper Functions (reuse from dashboard)
<?php
// Include helper functions
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
</script>