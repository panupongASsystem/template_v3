<?php
// application/views/member/google_drive_user_usage.php (แก้ไข format_bytes error)
?>
<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="flex items-center space-x-4">
                <button onclick="history.back()" class="p-2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left text-xl"></i>
                </button>
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">การใช้งาน Storage</h2>
                    <p class="text-gray-600">
                        ผู้ใช้: <strong><?php echo $user->full_name; ?></strong>
                        <span class="ml-2 text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">
                            <?php echo $user->position_name ?: 'ไม่ระบุตำแหน่ง'; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="flex space-x-3">
            <button onclick="refreshUsageData()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-sync-alt mr-2"></i>รีเฟรช
            </button>
            <button onclick="exportUserData()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-download mr-2"></i>Export
            </button>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- User Profile -->
            <div class="flex items-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-user text-2xl text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900"><?php echo $user->full_name; ?></h3>
                    <p class="text-blue-700"><?php echo $user->m_email; ?></p>
                    <p class="text-sm text-blue-600">สมาชิกตั้งแต่: <?php echo date('d/m/Y', strtotime($user->member_since)); ?></p>
                </div>
            </div>

            <!-- Storage Usage -->
            <div class="text-center">
                <div class="relative w-24 h-24 mx-auto mb-3">
                    <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                        <path class="text-gray-300" stroke="currentColor" stroke-width="3" fill="none" 
                              d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        <path class="text-blue-500" stroke="currentColor" stroke-width="3" fill="none" 
                              stroke-dasharray="<?php echo $user->storage_usage_percent; ?>, 100"
                              d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-semibold text-blue-600">
                            <?php echo number_format($user->storage_usage_percent, 1); ?>%
                        </span>
                    </div>
                </div>
                <p class="text-sm text-blue-700">
                    <?php echo $user->storage_quota_used_formatted; ?> / <?php echo $user->storage_quota_limit_formatted; ?>
                </p>
            </div>

            <!-- Access Info -->
            <div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-blue-700">สถานะ:</span>
                        <span class="inline-flex items-center px-2 py-1 text-sm rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>ใช้งานได้
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-blue-700">เข้าใช้ล่าสุด:</span>
                        <span class="text-blue-900">
                            <?php echo $user->last_storage_access ? date('d/m/Y H:i', strtotime($user->last_storage_access)) : 'ยังไม่เคย'; ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-blue-700">Personal Folder:</span>
                        <span class="text-blue-900">
                            <?php echo $user->personal_folder_id ? 'มี' : 'ยังไม่สร้าง'; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Files -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl">
                    <i class="fas fa-file text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ไฟล์ทั้งหมด</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo number_format($storage_stats['total_files']); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Size -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-green-100 to-green-50 rounded-xl">
                    <i class="fas fa-hdd text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ขนาดรวม</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo $storage_stats['total_size_formatted']; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Largest File -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-50 rounded-xl">
                    <i class="fas fa-file-alt text-2xl text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ไฟล์ใหญ่สุด</h4>
                    <p class="text-lg font-semibold text-gray-800">
                        <?php 
                        if ($storage_stats['largest_file']) {
                            echo $storage_stats['largest_file']->file_size_formatted;
                        } else {
                            echo '-';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- File Types -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-br from-orange-100 to-orange-50 rounded-xl">
                    <i class="fas fa-layer-group text-2xl text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 font-medium">ประเภทไฟล์</h4>
                    <p class="text-2xl font-semibold text-gray-800">
                        <?php echo count($storage_stats['file_types']); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- File Types Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">การใช้งานตามประเภทไฟล์</h3>
            <div class="space-y-3">
                <?php if (!empty($storage_stats['file_types'])): ?>
                    <?php foreach (array_slice($storage_stats['file_types'], 0, 8) as $type): ?>
                        <?php 
                        $percentage = ($storage_stats['total_size'] > 0) ? 
                            round(($type->total_size / $storage_stats['total_size']) * 100, 1) : 0;
                        ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <i class="<?php echo $type->file_type_icon; ?> text-lg"></i>
                                <span class="text-sm text-gray-700"><?php echo $type->mime_type_friendly; ?></span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <span class="text-sm text-gray-600 w-12 text-right"><?php echo $percentage; ?>%</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-8">ยังไม่มีข้อมูลประเภทไฟล์</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upload Activity Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">กิจกรรมการอัปโหลด (7 วันล่าสุด)</h3>
            <div class="space-y-3">
                <?php if (!empty($storage_stats['upload_frequency'])): ?>
                    <?php 
                    $max_uploads = max(array_column($storage_stats['upload_frequency'], 'uploads_count'));
                    foreach ($storage_stats['upload_frequency'] as $freq): 
                    ?>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 w-20">
                                <?php echo date('d/m', strtotime($freq->upload_date)); ?>
                            </span>
                            <div class="flex-1 mx-3">
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <?php $width = $max_uploads > 0 ? ($freq->uploads_count / $max_uploads) * 100 : 0; ?>
                                    <div class="bg-green-500 h-3 rounded-full" style="width: <?php echo $width; ?>%"></div>
                                </div>
                            </div>
                            <span class="text-sm text-gray-600 w-8 text-right">
                                <?php echo $freq->uploads_count; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-8">ไม่มีกิจกรรมการอัปโหลดใน 7 วันล่าสุด</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Files Table -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">ไฟล์ทั้งหมด</h3>
                <div class="flex space-x-3">
                    <input type="text" id="searchFiles" placeholder="ค้นหาไฟล์..." 
                           class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <button onclick="showFileManager()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-folder-open mr-2"></i>เปิด File Manager
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3 text-gray-600">ชื่อไฟล์</th>
                        <th class="px-6 py-3 text-gray-600">ประเภท</th>
                        <th class="px-6 py-3 text-gray-600">ขนาด</th>
                        <th class="px-6 py-3 text-gray-600">โฟลเดอร์</th>
                        <th class="px-6 py-3 text-gray-600">วันที่อัปโหลด</th>
                        <th class="px-6 py-3 text-gray-600">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y" id="filesTable">
                    <?php if (!empty($files)): ?>
                        <?php foreach ($files as $file): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <i class="<?php echo $file->file_type_icon; ?> text-lg mr-3"></i>
                                        <div>
                                            <div class="text-gray-800 font-medium">
                                                <?php echo htmlspecialchars($file->file_name); ?>
                                            </div>
                                            <div class="text-gray-600 text-sm">
                                                <?php echo htmlspecialchars($file->original_name); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?php echo $file->mime_type_friendly; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?php echo $file->file_size_formatted; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?php echo $file->folder_name ?: 'Root'; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?php echo date('d/m/Y H:i', strtotime($file->created_at)); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <?php if ($file->file_url): ?>
                                            <a href="<?php echo $file->file_url; ?>" target="_blank"
                                               class="w-8 h-8 flex items-center justify-center rounded bg-blue-50 text-blue-600 hover:bg-blue-100" 
                                               title="เปิดไฟล์">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                        <button onclick="downloadFile('<?php echo $file->file_id; ?>')" 
                                                class="w-8 h-8 flex items-center justify-center rounded bg-green-50 text-green-600 hover:bg-green-100" 
                                                title="ดาวน์โหลด">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button onclick="deleteUserFile('<?php echo $file->file_id; ?>', '<?php echo addslashes($file->file_name); ?>')" 
                                                class="w-8 h-8 flex items-center justify-center rounded bg-red-50 text-red-600 hover:bg-red-100" 
                                                title="ลบไฟล์">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-file text-4xl text-gray-300 mb-4"></i>
                                <p>ยังไม่มีไฟล์ในระบบ</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">กิจกรรมล่าสุด</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <?php if (!empty($recent_activities)): ?>
                    <?php foreach ($recent_activities as $activity): ?>
                        <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-lg text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">
                                        <?php echo ucfirst(str_replace('_', ' ', $activity['action'])); ?>
                                    </p>
                                    <span class="text-xs text-gray-500">
                                        <?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?>
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-700">
                                    <?php echo htmlspecialchars($activity['description']); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-history text-3xl text-gray-300 mb-3"></i>
                        <p>ยังไม่มีกิจกรรมล่าสุด</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Admin Actions Panel -->
    <div class="bg-white rounded-lg shadow mt-8">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-red-800">การจัดการ Admin</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button onclick="updateUserQuota()" 
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-edit mr-2"></i>แก้ไข Quota
                </button>
                
                <?php if (!$user->personal_folder_id): ?>
                <button onclick="createPersonalFolder()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-folder-plus mr-2"></i>สร้าง Personal Folder
                </button>
                <?php endif; ?>
                
                <button onclick="resetUserStorage()" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>รีเซ็ต Storage
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const userId = <?php echo $user->m_id; ?>;

// ===== Refresh Functions =====
function refreshUsageData() {
    Swal.fire({
        title: 'กำลังรีเฟรชข้อมูล...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`<?php echo site_url('google_drive_system/get_user_usage_data'); ?>?user_id=${userId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'รีเฟรชสำเร็จ',
                text: 'ข้อมูลได้รับการอัปเดตแล้ว',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
    });
}

// ===== File Management Functions =====
function downloadFile(fileId) {
    const downloadUrl = `<?php echo site_url('google_drive_system/download_file'); ?>?file_id=${fileId}`;
    window.open(downloadUrl, '_blank');
}

function deleteUserFile(fileId, fileName) {
    Swal.fire({
        title: 'ยืนยันการลบไฟล์',
        text: `คุณต้องการลบไฟล์ "${fileName}" หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ลบไฟล์',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?php echo site_url('google_drive_system/delete_user_file'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `file_id=${fileId}&user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('สำเร็จ', 'ลบไฟล์เรียบร้อยแล้ว', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
            });
        }
    });
}

function showFileManager() {
    const fileManagerUrl = `<?php echo site_url('google_drive_system/files'); ?>?user_id=${userId}`;
    window.open(fileManagerUrl, '_blank');
}

function exportUserData() {
    const exportUrl = `<?php echo site_url('google_drive_system/export_user_data'); ?>?user_id=${userId}`;
    window.open(exportUrl, '_blank');
}

// ===== Admin Functions =====
function updateUserQuota() {
    const currentQuotaGB = Math.round(<?php echo $user->storage_quota_limit; ?> / (1024 * 1024 * 1024) * 100) / 100;
    
    Swal.fire({
        title: 'แก้ไข Storage Quota',
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ขนาด Quota (GB)</label>
                    <input type="number" id="quotaInput" step="0.1" min="0.1" max="1000" 
                           value="${currentQuotaGB}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <div class="mt-2 text-sm text-gray-500">
                        ปัจจุบัน: ${currentQuotaGB} GB
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="text-sm text-blue-800">
                        <strong>ตัวอย่างขนาด Quota:</strong><br>
                        • 0.5 GB = 500 MB<br>
                        • 1 GB = 1,024 MB<br>
                        • 5 GB = 5,120 MB<br>
                        • 10 GB = 10,240 MB
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'อัปเดต Quota',
        cancelButtonText: 'ยกเลิก',
        focusConfirm: false,
        preConfirm: () => {
            const quotaGB = parseFloat(document.getElementById('quotaInput').value);
            if (isNaN(quotaGB) || quotaGB <= 0) {
                Swal.showValidationMessage('กรุณากรอกขนาด Quota ที่ถูกต้อง (มากกว่า 0)');
                return false;
            }
            if (quotaGB > 1000) {
                Swal.showValidationMessage('ขนาด Quota ไม่ควรเกิน 1,000 GB');
                return false;
            }
            return {
                quotaGB: quotaGB,
                quotaBytes: Math.round(quotaGB * 1024 * 1024 * 1024)
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { quotaGB, quotaBytes } = result.value;
            
            // แสดงการยืนยันขั้นสุดท้าย
            Swal.fire({
                title: 'ยืนยันการแก้ไข Quota',
                html: `
                    <div class="text-left">
                        <p class="mb-3"><strong>ผู้ใช้:</strong> <?php echo $user->full_name; ?></p>
                        <p class="mb-3"><strong>Quota เดิม:</strong> ${currentQuotaGB} GB</p>
                        <p class="mb-4"><strong>Quota ใหม่:</strong> ${quotaGB} GB</p>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                การเปลี่ยนแปลงนี้จะมีผลทันที และผู้ใช้จะสามารถใช้พื้นที่ตามที่กำหนดใหม่
                            </p>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ยืนยันการแก้ไข',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#f59e0b'
            }).then((confirmResult) => {
                if (confirmResult.isConfirmed) {
                    // ดำเนินการแก้ไข
                    fetch('<?php echo site_url('google_drive_system/update_user_quota'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `user_id=${userId}&quota=${quotaBytes}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'สำเร็จ!',
                                html: `
                                    <div class="text-center">
                                        <p class="mb-3">อัปเดต Storage Quota เรียบร้อยแล้ว</p>
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                            <p class="text-sm text-green-800">
                                                <strong>Quota ใหม่:</strong> ${quotaGB} GB<br>
                                                <strong>ผู้ใช้:</strong> <?php echo $user->full_name; ?>
                                            </p>
                                        </div>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: 'รีเฟรชหน้า'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
                    });
                }
            });
        }
    });
}

function createPersonalFolder() {
    Swal.fire({
        title: 'สร้าง Personal Folder',
        text: 'คุณต้องการสร้าง Personal Folder สำหรับผู้ใช้นี้หรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'สร้าง',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?php echo site_url('google_drive_system/create_personal_folder'); ?>', {
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
                    Swal.fire('สำเร็จ', 'สร้าง Personal Folder เรียบร้อยแล้ว', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                }
            });
        }
    });
}

function resetUserStorage() {
    Swal.fire({
        title: '⚠️ รีเซ็ต Storage',
        html: `
            <div class="text-left">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-red-800 mb-3">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        คำเตือน: การกระทำนี้ไม่สามารถยกเลิกได้!
                    </h4>
                    <div class="text-sm text-red-700 space-y-2">
                        <p><strong>สิ่งที่จะถูกลบ (ในระบบ):</strong></p>
                        <ul class="list-disc list-inside pl-4 space-y-1">
                            <li>รายการไฟล์ทั้งหมดของผู้ใช้ในฐานข้อมูล</li>
                            <li>สถิติการใช้งาน Storage (รีเซ็ตเป็น 0 B)</li>
                            <li>การเชื่อมโยง Personal Folder</li>
                            <li>ประวัติการเข้าใช้งาน</li>
                            <li>รายงานและกราฟการใช้งาน</li>
                        </ul>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-blue-800 mb-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        สิ่งที่ไม่ได้ลบ (ยังคงอยู่):
                    </h4>
                    <div class="text-sm text-blue-700 space-y-2">
                        <ul class="list-disc list-inside pl-4 space-y-1">
                            <li><strong>ไฟล์จริงใน Google Drive</strong> - ยังคงอยู่ครบทุกไฟล์</li>
                            <li><strong>Personal Folder จริง</strong> - ยังอยู่ใน Google Drive</li>
                            <li><strong>บัญชีผู้ใช้</strong> - สามารถเข้าระบบได้ปกติ</li>
                            <li><strong>สิทธิ์การใช้งาน</strong> - ยังคงมีสิทธิ์เข้าใช้ Storage</li>
                        </ul>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-green-800 mb-3">
                        <i class="fas fa-redo mr-2"></i>
                        หลังรีเซ็ตแล้ว:
                    </h4>
                    <div class="text-sm text-green-700 space-y-2">
                        <ul class="list-disc list-inside pl-4 space-y-1">
                            <li>ผู้ใช้เริ่มใช้งาน Storage ใหม่ได้ทันที</li>
                            <li>ระบบจะสร้าง Personal Folder ใหม่เมื่อใช้งาน</li>
                            <li>สามารถอัปโหลดไฟล์ใหม่ได้ปกติ</li>
                            <li>การใช้งานจะนับจาก 0 B อีกครั้ง</li>
                        </ul>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-yellow-800 mb-3">
                        <i class="fas fa-lightbulb mr-2"></i>
                        แนวทางปฏิบัติที่แนะนำ:
                    </h4>
                    <div class="text-sm text-yellow-700 space-y-2">
                        <ul class="list-disc list-inside pl-4 space-y-1">
                            <li>Export ข้อมูลผู้ใช้ก่อนรีเซ็ต (กดปุ่ม Export)</li>
                            <li>แจ้งผู้ใช้ให้ทราบล่วงหน้า</li>
                            <li>ตรวจสอบว่าไม่มีไฟล์สำคัญที่ต้องการเก็บไว้</li>
                            <li>ลบไฟล์จริงใน Google Drive ด้วยตนเอง (ถ้าต้องการ)</li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-2 border-red-300 rounded-lg p-4 bg-red-50">
                    <p class="text-sm text-red-800 mb-3">
                        <strong>ผู้ใช้ที่จะถูกรีเซ็ต:</strong><br>
                        <span class="text-lg font-semibold"><?php echo $user->full_name; ?></span><br>
                        <span class="text-gray-600"><?php echo $user->m_email; ?></span>
                    </p>
                    <p class="text-sm text-red-800 mb-3">
                        กรุณากรอก <strong class="bg-red-200 px-2 py-1 rounded">RESET_USER_STORAGE</strong> เพื่อยืนยัน:
                    </p>
                    <input type="text" id="confirmResetInput" placeholder="กรอก RESET_USER_STORAGE" 
                           class="w-full px-3 py-2 border-2 border-red-300 rounded-lg focus:outline-none focus:border-red-500"
                           autocomplete="off">
                </div>
            </div>
        `,
        width: '600px',
        showCancelButton: true,
        confirmButtonText: 'รีเซ็ต Storage',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        focusConfirm: false,
        preConfirm: () => {
            const confirmText = document.getElementById('confirmResetInput').value;
            if (confirmText !== 'RESET_USER_STORAGE') {
                Swal.showValidationMessage('กรุณากรอก "RESET_USER_STORAGE" ให้ถูกต้อง');
                return false;
            }
            return confirmText;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // ยืนยันครั้งสุดท้าย
            Swal.fire({
                title: 'ยืนยันการรีเซ็ตครั้งสุดท้าย',
                html: `
                    <div class="text-center">
                        <div class="text-6xl mb-4">⚠️</div>
                        <p class="text-lg font-semibold mb-3">คุณแน่ใจหรือไม่?</p>
                        <p class="text-gray-600 mb-4">
                            การดำเนินการนี้จะลบข้อมูลการใช้งาน Storage ของ<br>
                            <strong class="text-red-600"><?php echo $user->full_name; ?></strong><br>
                            ออกจากระบบอย่างถาวร
                        </p>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-sm text-red-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                ไม่สามารถยกเลิกหรือกู้คืนข้อมูลได้หลังจากนี้
                            </p>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, รีเซ็ตเลย',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280'
            }).then((finalResult) => {
                if (finalResult.isConfirmed) {
                    // แสดง Loading
                    Swal.fire({
                        title: 'กำลังรีเซ็ต Storage...',
                        html: `
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
                                <p class="text-gray-600">กรุณารอสักครู่...</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // ดำเนินการรีเซ็ต
                    fetch('<?php echo site_url('google_drive_system/reset_user_storage'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `user_id=${userId}&confirm=${result.value}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'รีเซ็ตสำเร็จ!',
                                html: `
                                    <div class="text-center">
                                        <div class="text-6xl mb-4">✅</div>
                                        <p class="text-lg font-semibold mb-3">รีเซ็ต Storage เรียบร้อยแล้ว</p>
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                            <p class="text-sm text-green-800">
                                                <strong>ผู้ใช้:</strong> <?php echo $user->full_name; ?><br>
                                                <strong>สถานะ:</strong> พร้อมใช้งานใหม่<br>
                                                <strong>การใช้งาน:</strong> 0 B / Quota
                                            </p>
                                        </div>
                                        <div class="mt-4 text-sm text-gray-600">
                                            หน้าจะรีเฟรชเพื่อแสดงข้อมูลใหม่
                                        </div>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: 'รีเฟรชหน้า'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
                    });
                }
            });
        }
    });
}

// ===== Search Function =====
document.getElementById('searchFiles').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#filesTable tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>

<style>
/* Custom styles */
.progress-ring {
    transition: stroke-dasharray 0.3s ease-in-out;
}

.file-row:hover {
    background-color: #f8fafc;
}

/* Loading animation */
.loading-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Chart styles */
.chart-bar {
    transition: width 0.3s ease-in-out;
}

.chart-bar:hover {
    opacity: 0.8;
}
</style>