<?php
// application/views/member/google_drive_config.php
?>
<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">กำหนดสิทธิ์ Google Drive</h2>
            <p class="text-gray-600">จัดการสิทธิ์การเข้าถึง Google Drive ตามตำแหน่งและสมาชิก</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="openAddPermissionModal()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>เพิ่มสิทธิ์ใหม่
            </button>
            <button onclick="exportPermissions()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button onclick="switchTab('positions')" 
                    id="tab-positions" 
                    class="tab-button active">
                <i class="fas fa-users-cog mr-2"></i>สิทธิ์ตามตำแหน่ง
            </button>
            <button onclick="switchTab('members')" 
                    id="tab-members" 
                    class="tab-button">
                <i class="fas fa-user-cog mr-2"></i>สิทธิ์เฉพาะสมาชิก
            </button>
            <button onclick="switchTab('templates')" 
                    id="tab-templates" 
                    class="tab-button">
                <i class="fas fa-layer-group mr-2"></i>Templates
            </button>
        </nav>
    </div>

    <!-- Position Permissions Tab -->
    <div id="positions-tab" class="tab-content active">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800">สิทธิ์ตามตำแหน่ง</h3>
                <p class="text-gray-600">กำหนดสิทธิ์ Google Drive ตามตำแหน่งงาน</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-6 py-3 text-gray-600">ตำแหน่ง</th>
                            <th class="px-6 py-3 text-gray-600">ประเภทสิทธิ์</th>
                            <th class="px-6 py-3 text-gray-600">การเข้าถึง Folder</th>
                            <th class="px-6 py-3 text-gray-600">สิทธิ์การดำเนินการ</th>
                            <th class="px-6 py-3 text-gray-600">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" id="positionsTable">
                        <?php if (!empty($positions_with_permissions)): ?>
                            <?php foreach ($positions_with_permissions as $position): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="font-medium text-gray-800"><?php echo $position->pname; ?></div>
                                            <div class="text-sm text-gray-500">ID: <?php echo $position->pid; ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if ($position->permission_type): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium <?php echo getPermissionBadgeColor($position->permission_type); ?>">
                                                <?php echo $position->type_name ?: $position->permission_type; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400">ไม่ได้กำหนด</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600">
                                            <?php echo getFolderAccessText($position->permission_type); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <?php if ($position->can_create_folder): ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                    <i class="fas fa-folder-plus mr-1"></i>สร้าง
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($position->can_share): ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                    <i class="fas fa-share mr-1"></i>แชร์
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($position->can_delete): ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                                    <i class="fas fa-trash mr-1"></i>ลบ
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <button onclick="editPositionPermission(<?php echo $position->pid; ?>, '<?php echo addslashes($position->pname); ?>')" 
                                                    class="w-8 h-8 flex items-center justify-center rounded bg-blue-50 text-blue-600 hover:bg-blue-100" 
                                                    title="แก้ไข">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($position->permission_type): ?>
                                                <button onclick="deletePositionPermission(<?php echo $position->pid; ?>)" 
                                                        class="w-8 h-8 flex items-center justify-center rounded bg-red-50 text-red-600 hover:bg-red-100" 
                                                        title="ลบ">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-users-cog text-4xl text-gray-300 mb-4"></i>
                                    <p>ยังไม่มีการกำหนดสิทธิ์</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Member Permissions Tab -->
    <div id="members-tab" class="tab-content">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">สิทธิ์เฉพาะสมาชิก</h3>
                        <p class="text-gray-600">กำหนดสิทธิ์พิเศษให้กับสมาชิกเฉพาะรายที่ต้องการเขียนทับสิทธิ์ตำแหน่ง</p>
                    </div>
                    <button onclick="openMemberPermissionModal()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-user-plus mr-2"></i>เพิ่มสิทธิ์สมาชิก
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-6 py-3 text-gray-600">สมาชิก</th>
                            <th class="px-6 py-3 text-gray-600">ประเภทสิทธิ์</th>
                            <th class="px-6 py-3 text-gray-600">เขียนทับตำแหน่ง</th>
                            <th class="px-6 py-3 text-gray-600">หมายเหตุ</th>
                            <th class="px-6 py-3 text-gray-600">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" id="membersTable">
                        <?php if (!empty($members_with_custom_permissions)): ?>
                            <?php foreach ($members_with_custom_permissions as $member): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="font-medium text-gray-800">
                                                <?php echo $member->m_fname . ' ' . $member->m_lname; ?>
                                            </div>
                                            <div class="text-sm text-gray-500"><?php echo $member->m_email; ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium <?php echo getPermissionBadgeColor($member->permission_type); ?>">
                                            <?php echo $member->type_name; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if ($member->override_position): ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>เขียนทับ
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                                <i class="fas fa-plus mr-1"></i>เพิ่มเติม
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600 max-w-xs truncate" title="<?php echo $member->notes; ?>">
                                            <?php echo $member->notes ?: '-'; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <button onclick="editMemberPermission(<?php echo $member->m_id; ?>)" 
                                                    class="w-8 h-8 flex items-center justify-center rounded bg-blue-50 text-blue-600 hover:bg-blue-100" 
                                                    title="แก้ไข">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteMemberPermission(<?php echo $member->m_id; ?>)" 
                                                    class="w-8 h-8 flex items-center justify-center rounded bg-red-50 text-red-600 hover:bg-red-100" 
                                                    title="ลบ">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-user-cog text-4xl text-gray-300 mb-4"></i>
                                    <p>ยังไม่มีสมาชิกที่กำหนดสิทธิ์พิเศษ</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Templates Tab -->
    <div id="templates-tab" class="tab-content">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Folder Templates</h3>
                        <p class="text-gray-600">จัดการ Template สำหรับสร้าง Folder อัตโนมัติ</p>
                    </div>
                    <button onclick="openTemplateModal()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>เพิ่ม Template
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                <?php if (!empty($folder_templates)): ?>
                    <?php foreach ($folder_templates as $template): ?>
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-medium text-gray-800"><?php echo $template->template_name; ?></h4>
                                <div class="flex space-x-1">
                                    <button onclick="editTemplate(<?php echo $template->id; ?>)" 
                                            class="w-6 h-6 flex items-center justify-center rounded text-blue-600 hover:bg-blue-50" 
                                            title="แก้ไข">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button onclick="deleteTemplate(<?php echo $template->id; ?>)" 
                                            class="w-6 h-6 flex items-center justify-center rounded text-red-600 hover:bg-red-50" 
                                            title="ลบ">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 mb-3">
                                สำหรับ: <span class="font-medium"><?php echo $template->permission_type; ?></span>
                            </div>
                            <div class="text-xs text-gray-500 mb-3">
                                <?php 
                                $structure = json_decode($template->folder_structure, true);
                                if (isset($structure['main_folder'])) {
                                    echo "📁 " . $structure['main_folder'];
                                    if (isset($structure['subfolders'])) {
                                        echo " (" . count($structure['subfolders']) . " folders)";
                                    }
                                }
                                ?>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs <?php echo $template->auto_create ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo $template->auto_create ? 'สร้างอัตโนมัติ' : 'สร้างเมื่อต้องการ'; ?>
                                </span>
                                <button onclick="previewTemplate(<?php echo $template->id; ?>)" 
                                        class="text-xs text-blue-600 hover:text-blue-800">
                                    ดูตัวอย่าง
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <i class="fas fa-layer-group text-4xl text-gray-300 mb-4"></i>
                        <p>ยังไม่มี Template</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
function getPermissionBadgeColor($permission_type) {
    $colors = [
        'full_admin' => 'bg-red-100 text-red-800',
        'department_admin' => 'bg-blue-100 text-blue-800',
        'position_only' => 'bg-green-100 text-green-800',
        'custom' => 'bg-purple-100 text-purple-800',
        'read_only' => 'bg-gray-100 text-gray-800',
        'no_access' => 'bg-red-100 text-red-800'
    ];
    
    return $colors[$permission_type] ?? 'bg-gray-100 text-gray-800';
}

function getFolderAccessText($permission_type) {
    $access_text = [
        'full_admin' => 'ทุก Folder',
        'department_admin' => 'Folder แผนก + ส่วนกลาง',
        'position_only' => 'Folder ของตำแหน่งเท่านั้น',
        'custom' => 'กำหนดเอง',
        'read_only' => 'อ่านอย่างเดียว',
        'no_access' => 'ไม่มีสิทธิ์'
    ];
    
    return $access_text[$permission_type] ?? 'ไม่ได้กำหนด';
}
?>

<style>
.tab-button {
    @apply px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition-colors;
}

.tab-button.active {
    @apply text-blue-600 border-blue-500;
}

.tab-content {
    @apply hidden;
}

.tab-content.active {
    @apply block;
}
</style>

<script>
// Tab Switching
function switchTab(tabName) {
    // ซ่อน tab ทั้งหมด
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // แสดง tab ที่เลือก
    document.getElementById(tabName + '-tab').classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');
}

// Permission Management Functions
function openAddPermissionModal() {
    // TODO: เปิด modal สำหรับเพิ่มสิทธิ์ใหม่
    console.log('Open add permission modal');
}

function editPositionPermission(positionId, positionName) {
    // TODO: เปิด modal แก้ไขสิทธิ์ตำแหน่ง
    console.log('Edit position permission:', positionId, positionName);
}

function deletePositionPermission(positionId) {
    // TODO: ลบสิทธิ์ตำแหน่ง
    console.log('Delete position permission:', positionId);
}

function openMemberPermissionModal() {
    // TODO: เปิด modal เพิ่มสิทธิ์สมาชิก
    console.log('Open member permission modal');
}

function editMemberPermission(memberId) {
    // TODO: แก้ไขสิทธิ์สมาชิก
    console.log('Edit member permission:', memberId);
}

function deleteMemberPermission(memberId) {
    // TODO: ลบสิทธิ์สมาชิก
    console.log('Delete member permission:', memberId);
}

function openTemplateModal() {
    // TODO: เปิด modal สร้าง template
    console.log('Open template modal');
}

function editTemplate(templateId) {
    // TODO: แก้ไข template
    console.log('Edit template:', templateId);
}

function deleteTemplate(templateId) {
    // TODO: ลบ template
    console.log('Delete template:', templateId);
}

function previewTemplate(templateId) {
    // TODO: แสดงตัวอย่าง template
    console.log('Preview template:', templateId);
}

function exportPermissions() {
    // TODO: Export สิทธิ์ทั้งหมด
    window.open('<?php echo site_url('google_drive/export_permissions'); ?>', '_blank');
}
</script>