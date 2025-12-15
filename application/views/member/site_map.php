<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">จัดการระบบหลัก</h2>
            <?php if (!$is_system_admin): ?>
                <p class="text-sm text-gray-500 mt-2">* เฉพาะผู้ดูแลระบบสูงสุดเท่านั้นที่สามารถเปลี่ยนแปลงสถานะได้</p>
            <?php endif; ?>
        </div>
        
        <?php if ($is_system_admin): ?>
<button onclick="openCreateModuleModal()" 
        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
    </svg>
    สร้างระบบหลัก
</button>
<?php endif; ?>
    </div>
	
	
	
	
	

    <!-- Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">รายชื่อระบบทั้งหมด (tempc2_db.tbl_member_module_menus)</h3>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
    <tr class="bg-gray-50 text-left">
        <th class="px-6 py-3 text-gray-600">ชื่อระบบ</th>
		<th class="px-6 py-3 text-gray-600 text-center">Module ID</th>
        <th class="px-6 py-3 text-gray-600 text-center">เวอร์ชั่น(is_trial (Trial=1, Full=0))</th>
        <th class="px-6 py-3 text-gray-600 text-center">สถานะการใช้งาน</th>
        <th class="px-6 py-3 text-gray-600 text-center">จำนวนผู้ใช้งาน</th>
        <?php if ($is_system_admin): ?>
        <th class="px-6 py-3 text-gray-600 text-center">จัดการ</th>
        <?php endif; ?>
    </tr>
</thead>
<tbody class="divide-y">
    <?php foreach ($modules as $module): ?>
    <tr class="hover:bg-gray-50">
		
		
                            <td class="px-6 py-4">
                                <div class="text-gray-800 font-medium"><?php echo $module->name; ?></div>
<div class="text-sm text-gray-500"><?php echo $module->description; ?></div>
                            </td>
		<td class="px-6 py-4 text-center">
                        <span class="text-gray-600"><?php echo $module->id; ?></span>
                    </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center items-center space-x-2">
                                    <span class="text-sm text-gray-500">Trial</span>
                                    <div class="relative inline-flex items-center">
                                        <label class="flex items-center cursor-pointer <?php echo !$is_system_admin ? 'opacity-50' : ''; ?>">
                                            <div class="relative">
                                                <input type="checkbox"
    class="sr-only"
    onchange="toggleStatus(this, <?php echo $module->id; ?>, 'trial')"
    <?php echo $module->is_trial == 0 ? 'checked' : ''; ?>  
    <?php echo !$is_system_admin ? 'disabled' : ''; ?>>
                                                <div class="block bg-gray-300 w-14 h-8 rounded-full"></div>
                                                <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition transform duration-300 ease-in-out"></div>
                                            </div>
                                        </label>
                                    </div>
                                    <span class="text-sm text-gray-500">Full</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center items-center space-x-2">
                                    <span class="text-sm text-gray-500">ปิด</span>
                                    <div class="relative inline-flex items-center">
                                        <label class="flex items-center cursor-pointer <?php echo !$is_system_admin ? 'opacity-50' : ''; ?>">
                                            <div class="relative">
                                                <input type="checkbox"
    class="sr-only"
    onchange="toggleStatus(this, <?php echo $module->id; ?>, 'status')"
    <?php echo $module->status == 1 ? 'checked' : ''; ?>
    <?php echo !$is_system_admin ? 'disabled' : ''; ?>>
                                                <div class="block bg-gray-300 w-14 h-8 rounded-full"></div>
                                                <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition transform duration-300 ease-in-out"></div>
                                            </div>
                                        </label>
                                    </div>
                                    <span class="text-sm text-gray-500">เปิด</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">
                                <?php echo number_format($module->user_count); ?> คน
                            </td>
		<?php if ($is_system_admin): ?>
        <td class="px-6 py-4">
    <div class="flex justify-center space-x-2">
        <button onclick="viewModuleDetails(<?php echo $module->id; ?>)"
                class="text-indigo-600 hover:text-indigo-800" 
                title="ดูรายละเอียด">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
            </svg>
        </button>
        <button onclick="editModule(<?php echo $module->id; ?>)" 
                class="text-blue-600 hover:text-blue-800"
                title="แก้ไข">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
        </button>
        <button onclick="deleteModule(<?php echo $module->id; ?>, '<?php echo addslashes($module->name); ?>')" 
                class="text-red-600 hover:text-red-800"
                title="ลบ">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
</tbody>

        <div class="p-6 border-t">
            <div class="flex justify-between items-center">
                <div class="text-gray-600">
                    แสดง <?php echo $start_row; ?>-<?php echo $end_row; ?> จาก <?php echo $total_rows; ?> รายการ
                </div>
                <?php echo $pagination; ?>
            </div>
        </div>
    </div>
</div>



<!-- Modal สร้าง/แก้ไขระบบ -->
<div id="moduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[800px] shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-xl font-semibold text-gray-800" id="modalTitle">สร้างระบบใหม่</h3>
                <button onclick="closeModuleModal()" class="text-gray-400 hover:text-gray-500">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
</button>
            </div>

            <form id="moduleForm" class="mt-4">
                <input type="hidden" id="moduleId" name="id">
                
                <div class="grid grid-cols-2 gap-6">
                    <!-- ข้อมูลระบบ -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-700">ข้อมูลระบบ</h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อระบบ *</label>
                            <input type="text" id="moduleName" name="name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">รหัสระบบ *</label>
                            <input type="text" id="moduleCode" name="code" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">รายละเอียด</label>
                            <textarea id="moduleDescription" name="description" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ลำดับการแสดงผล *</label>
                            <input type="number" id="displayOrder" name="display_order" required min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- เมนูระบบ -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-medium text-gray-700">เงื่อนไขระบบ</h4>
                            <button type="button" onclick="addMenuItem()" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-base">
    เพิ่มเงื่อนไขระบบ
</button>

                        </div>

                        <div class="overflow-y-auto max-h-[400px] pr-2 space-y-3" id="menuList">
                            <!-- Menu items จะถูกเพิ่มที่นี่ด้วย JavaScript -->
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                    <button type="button" onclick="closeModuleModal()"
        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
    ยกเลิก
</button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

		
		
		

		
		
		
		
		
		
		
<!-- Template สำหรับรายการเมนู -->
<template id="menuItemTemplate">
    <div class="menu-item bg-gray-50 p-3 rounded-lg border relative">
        <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-red-500" onclick="removeMenuItem(this)">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อเงื่อนไข (name) * </label>
                <input type="text" name="menu_names[]" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">รหัสเงื่อนไข (code) *</label>
                <input type="text" name="menu_codes[]" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
			
			
			<!--   -->


            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">เงื่อนไข URL (url)(ถ้ามี)</label>
                <input type="text" name="menu_urls[]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
			
			

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ไอคอน (icon)(ถ้ามี)</label>
                <input type="text" name="menu_icons[]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div> 

			
			

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">เงื่อนไขหลัก (parent_id</label>
                <select name="menu_parents[]" onchange="handleParentMenuChange(this)">
                    <option value="">-- ไม่มี --</option>
                </select>
            </div>
            <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">เงื่อนไขเพิ่มเติม (display_order)</label>
    <input type="number" name="menu_orders[]" min="1" value="1" disabled
           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
</div>

            <div class="col-span-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="menu_status[]" value="1" checked
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">เปิดใช้งาน (status = 1)</span>
                </label>
            </div>
        </div>
    </div>
</template>		
		


		
		
		
		
		
			<!-- Modal แสดงรายละเอียด -->
<div id="viewModuleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[1000px] shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Header -->
            <div class="flex justify-between items-center pb-3 border-b">
                <div>
                    <h3 class="text-2xl font-semibold text-gray-800">รายละเอียดระบบ (tbl_member_module_menus)</h3>
                    <p class="text-sm text-gray-500 mt-1">Module ID: <span id="viewModuleId" class="font-medium">-</span></p>
                </div>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="space-y-6 mt-6">
                <!-- ข้อมูลทั่วไป -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500">ชื่อระบบ</p>
                            <p class="text-base font-medium text-gray-900 mt-1" id="viewModuleName">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">รหัสระบบ</p>
                            <p class="text-base font-medium text-gray-900 mt-1" id="viewModuleCode">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">ลำดับการแสดงผล</p>
                            <p class="text-base font-medium text-gray-900 mt-1" id="viewModuleOrder">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">จำนวนผู้ใช้งาน</p>
                            <p class="text-base font-medium text-gray-900 mt-1" id="viewModuleUsers">-</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <p class="text-sm text-gray-500">รายละเอียด</p>
                        <p class="text-base text-gray-900 mt-1" id="viewModuleDesc">-</p>
                    </div>
                </div>

                <!-- สถานะ -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-2">เวอร์ชั่น</p>
                            <div id="viewModuleVersionBadge" class="inline-flex rounded-full px-3 py-1 text-sm font-medium"></div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-2">สถานะการใช้งาน</p>
                            <div id="viewModuleStatusBadge" class="inline-flex rounded-full px-3 py-1 text-sm font-medium"></div>
                        </div>
                    </div>
                </div>

                <!-- รายการเงื่อนไข -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 bg-gray-50 border-b">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">รายการเงื่อนไข</h3>
                    </div>
                    <div class="p-6 space-y-6" id="viewModuleMenus">
                        <!-- JavaScript จะเติมข้อมูลที่นี่ -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
		
		
		
		
		
		


<script>
    var site_url = '<?php echo site_url(); ?>';
    var is_system_admin = <?php echo $is_system_admin ? 'true' : 'false'; ?>;

    function toggleStatus(element, moduleId, type) {
    if (!is_system_admin) {
        Swal.fire({
            title: 'ไม่มีสิทธิ์ดำเนินการ',
            text: 'เฉพาะผู้ดูแลระบบสูงสุดเท่านั้นที่สามารถเปลี่ยนแปลงสถานะได้',
            icon: 'warning'
        });
        return;
    }

    const isChecked = element.checked;
    const status = isChecked ? 1 : 0;
    
    // ย้อนกลับสถานะชั่วคราว
    element.checked = !isChecked;

    const actionText = type === 'trial' ? 
        (isChecked ? 'Full Version' : 'Trial Version') : 
        (isChecked ? 'เปิดการใช้งาน' : 'ปิดการใช้งาน');

    Swal.fire({
        title: 'ยืนยันการเปลี่ยนสถานะ?',
        text: `คุณต้องการเปลี่ยนเป็น ${actionText} ใช่หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#EF4444',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            // แสดง loading
            Swal.fire({
                title: 'กำลังดำเนินการ...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: site_url + 'System_member/toggle_module_status',
                type: 'POST',
                dataType: 'json',
                data: {
                    module_id: moduleId,
                    type: type,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'สำเร็จ!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        console.error('Error response:', response);
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด!',
                            text: response.message,
                            icon: 'error'
                        }).then(() => {
                            element.checked = !isChecked;
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    try {
                        const response = JSON.parse(xhr.responseText);
                        console.log('Parsed error response:', response);
                    } catch (e) {
                        console.log('Raw error response:', xhr.responseText);
                    }
                    
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง',
                        icon: 'error'
                    }).then(() => {
                        element.checked = !isChecked;
                    });
                }
            });
        } else {
            // ถ้ากดยกเลิก ให้ย้อนกลับสถานะ
            element.checked = !isChecked;
        }
    });
}
</script>

<style>
    input:checked ~ .dot {
        transform: translateX(100%);
    }
    
    input:checked ~ .block {
        background-color: #10B981;
    }

    .dot {
        transition: all 0.3s ease-in-out;
    }

    label.opacity-50 {
        cursor: not-allowed;
    }

    label.opacity-50 input:disabled ~ .block {
        background-color: #E5E7EB;
    }

    label.opacity-50 input:disabled ~ .dot {
        background-color: #D1D5DB;
    }
</style>


<script>
// Modal handlers
function openCreateModuleModal() {
    // Reset form
    document.getElementById('moduleForm').reset();
    document.getElementById('moduleId').value = '';
    document.getElementById('menuList').innerHTML = '';
    document.getElementById('modalTitle').textContent = 'สร้างระบบใหม่';
    document.getElementById('moduleModal').classList.remove('hidden');
}

function closeModuleModal() {
    document.getElementById('moduleModal').classList.add('hidden');
    document.getElementById('moduleForm').reset();
    document.getElementById('moduleId').value = '';
    document.getElementById('menuList').innerHTML = '';
}

function editModule(id) {
    console.log('editModule called with id:', id);
    
    // แสดง loading
    Swal.fire({
        title: 'กำลังโหลดข้อมูล...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // ดึงข้อมูลระบบ
    $.ajax({
        url: `${site_url}System_member/get_module/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Full response:', response);
            Swal.close();
            
            if (response.success) {
                const { module, menus } = response.data;
                
                // เติมข้อมูลโมดูล
                document.getElementById('moduleId').value = module.id;
                document.getElementById('moduleName').value = module.name;
                document.getElementById('moduleCode').value = module.code;
                document.getElementById('moduleDescription').value = module.description;
                document.getElementById('displayOrder').value = module.display_order;

                // เคลียร์เมนูเก่า
                const menuList = document.getElementById('menuList');
                menuList.innerHTML = '';

                // สร้าง menu items ใหม่
                if (menus && menus.length > 0) {
                    menus.forEach((menu, index) => {
                        // Clone template
                        const template = document.getElementById('menuItemTemplate');
                        const clone = template.content.cloneNode(true);
                        const menuItem = clone.querySelector('.menu-item');
                        
                        // Add hidden input for menu ID
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'menu_ids[]';
                        hiddenInput.value = menu.id;
                        menuItem.appendChild(hiddenInput);
                        
                        // Fill in the values
                        const inputs = {
                            name: menuItem.querySelector('input[name="menu_names[]"]'),
                            code: menuItem.querySelector('input[name="menu_codes[]"]'),
                            url: menuItem.querySelector('input[name="menu_urls[]"]'),
                            icon: menuItem.querySelector('input[name="menu_icons[]"]'),
                            order: menuItem.querySelector('input[name="menu_orders[]"]'),
                            status: menuItem.querySelector('input[name="menu_status[]"]'),
                            parent: menuItem.querySelector('select[name="menu_parents[]"]')
                        };

                        // Set values with null checks
                        if (inputs.name) inputs.name.value = menu.name || '';
                        if (inputs.code) inputs.code.value = menu.code || '';
                        if (inputs.url) inputs.url.value = menu.url || '';
                        if (inputs.icon) inputs.icon.value = menu.icon || '';
                        if (inputs.order) inputs.order.value = menu.display_order || '1';
                        if (inputs.status) {
                            inputs.status.checked = Boolean(menu.status);
                            inputs.status.value = '1';
                        }

                        // Add to DOM
                        menuList.appendChild(menuItem);
                    });

                    // Update parent options after all menus are added
                    updateParentMenuOptions();

                    // Set parent values
                    const menuItems = menuList.querySelectorAll('.menu-item');
                    menus.forEach((menu, index) => {
                        if (menu.parent_id) {
                            const select = menuItems[index].querySelector('select[name="menu_parents[]"]');
                            if (select) {
                                select.value = menu.parent_id;
                            }
                        }
                    });
                }

                // แสดง Modal
                document.getElementById('moduleModal').classList.remove('hidden');
                document.getElementById('modalTitle').textContent = 'แก้ไขระบบ';

            } else {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: response.message,
                    icon: 'error'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {xhr, status, error});
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                icon: 'error'
            });
        }
    });
}


	
	
	// ฟังก์ชันโหลดตัวเลือกเมนูหลัก
function getParentMenus(currentMenuId = null) {
    const menuItems = document.querySelectorAll('.menu-item');
    const moduleId = document.getElementById('moduleId').value;
    let options = [{
        id: '',
        name: '-- ไม่มี --'
    }];

    // รวบรวมเมนูที่มีอยู่
    menuItems.forEach(item => {
        const id = item.querySelector('input[name="menu_ids[]"]')?.value || item.dataset.menuId;
        const name = item.querySelector('input[name="menu_names[]"]').value;

        // ไม่รวมตัวเองและเมนูย่อยของตัวเอง
        if (id !== currentMenuId && !isChildMenu(id, currentMenuId)) {
            options.push({
                id: id,
                name: name
            });
        }
    });

    return options;
}

// ฟังก์ชันตรวจสอบว่าเป็นเมนูย่อยหรือไม่
function isChildMenu(menuCode, parentCode) {
    if (!menuCode || !parentCode) return false;
    
    const menuItems = document.querySelectorAll('.menu-item');
    let currentMenu = Array.from(menuItems)
        .find(item => item.querySelector('input[name="menu_codes[]"]').value === menuCode);
    
    while (currentMenu) {
        const parentSelect = currentMenu.querySelector('select[name="menu_parents[]"]');
        const currentParentCode = parentSelect.value;
        
        if (currentParentCode === parentCode) return true;
        if (!currentParentCode) break;
        
        currentMenu = Array.from(menuItems)
            .find(item => item.querySelector('input[name="menu_codes[]"]').value === currentParentCode);
    }
    
    return false;
}

// ฟังก์ชันอัพเดทตัวเลือกเมนูหลัก
function updateParentMenuOptions() {
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach((menuItem) => {
        const menuIdElement = menuItem.querySelector('input[name="menu_ids[]"]');
        const parentSelect = menuItem.querySelector('select[name="menu_parents[]"]');
        
        // ตรวจสอบว่า parentSelect มีอยู่จริง
        if (!parentSelect) return;
        
        const currentValue = parentSelect.value;
        
        // เคลียร์ตัวเลือกเดิม
        parentSelect.innerHTML = '<option value="">-- ไม่มี --</option>';
        
        // เพิ่มตัวเลือกใหม่
        menuItems.forEach((otherItem) => {
            // ข้ามตัวเอง
            if (otherItem === menuItem) return;
            
            const otherMenuNameElement = otherItem.querySelector('input[name="menu_names[]"]');
            const otherMenuCodeElement = otherItem.querySelector('input[name="menu_codes[]"]');
            
            // ตรวจสอบว่า element ที่จำเป็นมีอยู่
            if (otherMenuNameElement && otherMenuCodeElement) {
                const otherMenuName = otherMenuNameElement.value;
                const otherMenuCode = otherMenuCodeElement.value;
                
                if (otherMenuName && otherMenuCode) {
                    const option = document.createElement('option');
                    option.value = otherMenuCode; // ใช้ code เป็น value
                    option.textContent = otherMenuName;
                    
                    // เลือกตัวเลือกที่ตรงกับค่าปัจจุบัน
                    if (option.value === currentValue) {
                        option.selected = true;
                    }
                    
                    parentSelect.appendChild(option);
                }
            }
        });
    });
}

// Event Listeners
function setupMenuEventListeners(menuItem) {
    const nameInput = menuItem.querySelector('input[name="menu_names[]"]');
    const parentSelect = menuItem.querySelector('select[name="menu_parents[]"]');

    // เมื่อเปลี่ยนชื่อเมนู
    nameInput.addEventListener('input', () => {
        updateParentMenuOptions();
    });

    // เมื่อเปลี่ยนเมนูหลัก
    parentSelect.addEventListener('change', function() {
        const menuId = menuItem.querySelector('input[name="menu_ids[]"]')?.value || menuItem.dataset.menuId;
        const selectedParentId = this.value;

        // ตรวจสอบ circular reference
        if (selectedParentId && isChildMenu(selectedParentId, menuId)) {
            Swal.fire({
                title: 'ข้อผิดพลาด!',
                text: 'ไม่สามารถเลือกเมนูย่อยเป็นเมนูหลักได้',
                icon: 'error'
            });
            this.value = '';
            return;
        }

        updateParentMenuOptions();
    });
}
// Function to remove menu item
function removeMenuItem(button) {
    const menuItem = button.closest('.menu-item');
    if (menuItem) {
        menuItem.remove();
        // Update parent options after removal
        updateParentMenuOptions();
    }
}
	
	
	
	
	
// ปิด modal เมื่อคลิกพื้นหลัง
document.getElementById('moduleModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModuleModal();
    }
});

// Menu handlers
function addMenuItem(existingMenu = null) {
    // Clone template
    const template = document.getElementById('menuItemTemplate');
    const menuList = document.getElementById('menuList');
    const clone = template.content.cloneNode(true);
    const menuItem = clone.querySelector('.menu-item');

    // สร้าง unique id สำหรับเมนูใหม่หรือใช้ id เดิม
    const menuId = existingMenu?.id || 'new_menu_' + Date.now();
    menuItem.dataset.menuId = menuId;

    // จัดการ input fields
    const inputs = {
        name: menuItem.querySelector('input[name="menu_names[]"]'),
        code: menuItem.querySelector('input[name="menu_codes[]"]'),
        url: menuItem.querySelector('input[name="menu_urls[]"]'),
        icon: menuItem.querySelector('input[name="menu_icons[]"]'),
        order: menuItem.querySelector('input[name="menu_orders[]"]'),
        status: menuItem.querySelector('input[name="menu_status[]"]'),
        parent: menuItem.querySelector('select[name="menu_parents[]"]')
    };

    // ถ้ามีข้อมูลเดิม ใส่ข้อมูลลงใน inputs
    if (existingMenu) {
        // เพิ่ม hidden input สำหรับเก็บ ID
        if (existingMenu.id) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'menu_ids[]';
            hiddenInput.value = existingMenu.id;
            menuItem.appendChild(hiddenInput);
        }

        // ใส่ค่าในฟิลด์ต่างๆ
        inputs.name.value = existingMenu.name || '';
        inputs.code.value = existingMenu.code || '';
        inputs.url.value = existingMenu.url || '';
        inputs.icon.value = existingMenu.icon || '';
        inputs.order.value = existingMenu.display_order || '1';
        inputs.status.checked = Boolean(existingMenu.status);
    } else {
        // ค่าเริ่มต้นสำหรับเมนูใหม่
        inputs.order.value = '1';
        inputs.status.checked = true;
    }

    // เพิ่ม event listeners
    inputs.name.addEventListener('input', () => {
        updateParentMenuOptions();
    });

    inputs.parent.addEventListener('change', function() {
        if (!validateParentSelection(this)) {
            this.value = '';
        }
    });

    // ปุ่มลบเมนู
    const removeButton = menuItem.querySelector('button[onclick="removeMenuItem(this)"]');
    if (removeButton) {
        removeButton.onclick = function() {
            if (confirm('คุณต้องการลบเมนูนี้ใช่หรือไม่?')) {
                menuItem.remove();
                updateParentMenuOptions();
            }
        };
    }

    // เพิ่ม validation
    inputs.name.required = true;
    inputs.code.required = true;
    inputs.order.required = true;
    inputs.order.min = "1";
    inputs.order.type = "number";

    // สร้าง parent options
    const currentMenus = Array.from(menuList.querySelectorAll('.menu-item')).map(item => ({
        id: item.dataset.menuId,
        name: item.querySelector('input[name="menu_names[]"]').value,
        parent_id: item.querySelector('select[name="menu_parents[]"]').value
    }));

    inputs.parent.innerHTML = '<option value="">-- ไม่มี --</option>';
    currentMenus.forEach(menu => {
        if (menu.id !== menuId && !isChildOf(menu.id, existingMenu?.parent_id)) {
            const option = document.createElement('option');
            option.value = menu.id;
            option.textContent = menu.name;
            if (existingMenu && existingMenu.parent_id === menu.id) {
                option.selected = true;
            }
            inputs.parent.appendChild(option);
        }
    });

    // เพิ่มเมนูใหม่เข้าไปใน DOM
    menuList.appendChild(menuItem);

    // อัพเดท parent options สำหรับทุกเมนู
    updateParentMenuOptions();

    // Scroll to new menu item
    menuItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    return menuItem;
}

// ฟังก์ชันช่วยตรวจสอบว่าเป็น child ของเมนูที่กำหนดหรือไม่
function isChildOf(menuId, parentId) {
    if (!parentId) return false;
    
    const menuItems = document.querySelectorAll('.menu-item');
    let currentParentId = parentId;
    
    while (currentParentId) {
        if (currentParentId === menuId) return true;
        
        const parentItem = Array.from(menuItems).find(item => item.dataset.menuId === currentParentId);
        if (!parentItem) break;
        
        currentParentId = parentItem.querySelector('select[name="menu_parents[]"]').value;
    }
    
    return false;
}

// ฟังก์ชันตรวจสอบความถูกต้องของ parent selection
function validateParentSelection(select) {
    const menuItem = select.closest('.menu-item');
    const menuId = menuItem.dataset.menuId;
    const selectedParentId = select.value;
    
    if (!selectedParentId) return true;
    
    // ตรวจสอบว่าไม่ได้เลือกตัวเองเป็น parent
    if (menuId === selectedParentId) {
        Swal.fire({
            title: 'ข้อผิดพลาด!',
            text: 'ไม่สามารถเลือกตัวเองเป็นเมนูหลักได้',
            icon: 'error'
        });
        return false;
    }
    
    // ตรวจสอบ circular reference
    if (isChildOf(menuId, selectedParentId)) {
        Swal.fire({
            title: 'ข้อผิดพลาด!',
            text: 'ไม่สามารถเลือกเมนูย่อยเป็นเมนูหลักได้',
            icon: 'error'
        });
        return false;
    }
    
    return true;
}



	
	
	
	
	
	
	
	

function updateParentMenuOptions() {
    const menuItems = document.querySelectorAll('.menu-item');
    const parentSelects = document.querySelectorAll('select[name="menu_parents[]"]');
    
    parentSelects.forEach((select, currentIndex) => {
        const currentValue = select.value;
        select.innerHTML = '<option value="">-- ไม่มี --</option>';
        
        menuItems.forEach((item, itemIndex) => {
            if (currentIndex !== itemIndex) {
                const nameInput = item.querySelector('input[name="menu_names[]"]');
                const codeInput = item.querySelector('input[name="menu_codes[]"]');
                const menuId = item.querySelector('input[name="menu_ids[]"]');
                
                if (nameInput && nameInput.value) {
                    const option = document.createElement('option');
                    option.value = menuId ? menuId.value : codeInput.value;
                    option.textContent = nameInput.value;
                    if (option.value === currentValue) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                }
            }
        });
    });
}
	
	
	
	
	
	
</script>

		
		
<script>		
// ฟังก์ชันดึงรายการเมนูที่สามารถเป็นเมนูหลักได้
function getAvailableParentMenus(currentMenuId = null) {
    const menuItems = document.querySelectorAll('.menu-item');
    const currentMenuItem = currentMenuId ? 
        document.querySelector(`.menu-item[data-menu-id="${currentMenuId}"]`) : null;
    
    let options = [{
        id: '',
        name: '-- ไม่มี --'
    }];

    // วนลูปผ่านเมนูทั้งหมด
    menuItems.forEach((item, index) => {
        // ถ้าเป็นเมนูปัจจุบัน ให้ข้าม
        if (item === currentMenuItem) return;
        
        const name = item.querySelector('input[name="menu_names[]"]').value;
        const id = item.querySelector('input[name="menu_ids[]"]')?.value || item.dataset.menuId;
        
        // เช็คว่าเป็นเมนูที่สร้างก่อนเมนูปัจจุบันหรือไม่
        const currentIndex = Array.from(menuItems).indexOf(currentMenuItem);
        if (currentMenuItem === null || index < currentIndex) {
            // เพิ่มเข้าตัวเลือกถ้า:
            // 1. ไม่ใช่ตัวเอง
            // 2. ไม่ใช่เมนูย่อยของตัวเอง
            // 3. เป็นเมนูที่สร้างก่อนเมนูปัจจุบัน
            if (!isChildMenu(id, currentMenuId)) {
                options.push({
                    id: id,
                    name: name
                });
            }
        }
    });

    return options;
}

// ฟังก์ชันอัพเดทตัวเลือกเมนูหลักสำหรับเมนูที่กำหนด
function updateParentMenuSelect(menuItem) {
    const menuId = menuItem.dataset.menuId;
    const parentSelect = menuItem.querySelector('select[name="menu_parents[]"]');
    const currentValue = parentSelect.value;

    // ดึงรายการเมนูที่สามารถเป็นเมนูหลักได้
    const options = getAvailableParentMenus(menuId);
    
    // อัพเดท select options
    parentSelect.innerHTML = '';
    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option.id;
        optionElement.textContent = option.name;
        if (option.id === currentValue) {
            optionElement.selected = true;
        }
        parentSelect.appendChild(optionElement);
    });
}

// ฟังก์ชันอัพเดทตัวเลือกเมนูหลักทั้งหมด
function updateAllParentMenuOptions() {
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(menuItem => {
        updateParentMenuSelect(menuItem);
    });
}

// เพิ่มเมนูใหม่
function addMenuItem() {
    const template = document.getElementById('menuItemTemplate');
    const menuList = document.getElementById('menuList');
    const clone = template.content.cloneNode(true);
    const menuItem = clone.querySelector('.menu-item');

    // สร้าง unique id
    const menuId = 'menu_' + Date.now();
    menuItem.dataset.menuId = menuId;

    // เก็บค่าเลือกเมนูหลักปัจจุบันไว้
    const currentParentSelects = document.querySelectorAll('.menu-item select[name="menu_parents[]"]');
    const currentValues = Array.from(currentParentSelects).map(select => ({
        id: select.closest('.menu-item').dataset.menuId,
        value: select.value
    }));

    // เพิ่มเมนูใหม่
    menuList.appendChild(menuItem);

    // คืนค่าที่เลือกไว้ให้เมนูเดิม
    currentValues.forEach(({id, value}) => {
        const select = document.querySelector(`.menu-item[data-menu-id="${id}"] select[name="menu_parents[]"]`);
        if (select) {
            select.value = value;
        }
    });

    // อัพเดทตัวเลือกเมนูหลัก
    updateParentMenuOptions();
}

// Event handler สำหรับการเลือกเมนูหลัก
function handleParentMenuChange(select) {
    const menuItem = select.closest('.menu-item');
    const currentId = menuItem.dataset.menuId;
    const selectedParentId = select.value;
    
    // ถ้าเลือก "ไม่มี" ให้ผ่านไป
    if (!selectedParentId) return;
    
    // ตรวจสอบว่าไม่ใช่ตัวเอง
    if (currentId === selectedParentId) {
        Swal.fire({
            title: 'ข้อผิดพลาด!',
            text: 'ไม่สามารถเลือกตัวเองเป็นเมนูหลักได้',
            icon: 'error'
        });
        select.value = '';
        return;
    }
    
    // ตรวจสอบว่าไม่ใช่ child ของตัวเอง
    if (isChildMenu(selectedParentId, currentId)) {
        Swal.fire({
            title: 'ข้อผิดพลาด!',
            text: 'ไม่สามารถเลือกเมนูย่อยเป็นเมนูหลักได้',
            icon: 'error'
        });
        select.value = '';
        return;
    }
    
    // อัพเดทตัวเลือกทั้งหมด
    updateParentMenuOptions();
}

// ฟังก์ชันลบเมนู
function removeMenuItem(button) {
    button.closest('.menu-item').remove();
    updateParentMenuOptions();
}

// อัพเดทตัวเลือกเมนูหลัก
function updateParentMenuOptions() {
    const menuItems = document.querySelectorAll('.menu-item');
    const parentSelects = document.querySelectorAll('select[name="menu_parents[]"]');
    
    // เก็บค่าที่เลือกไว้
    const selectedValues = Array.from(parentSelects).map(select => select.value);
    
    // สร้างตัวเลือกใหม่
    parentSelects.forEach((select, index) => {
        const currentValue = selectedValues[index];
        select.innerHTML = '<option value="">-- ไม่มี --</option>';
        
        menuItems.forEach((item, menuIndex) => {
            const nameInput = item.querySelector('input[name="menu_names[]"]');
            const codeInput = item.querySelector('input[name="menu_codes[]"]');
            if (nameInput && nameInput.value && index !== menuIndex) {
                const option = document.createElement('option');
                option.value = codeInput.value;
                option.textContent = nameInput.value;
                if (currentValue === codeInput.value) {
                    option.selected = true;
                }
                select.appendChild(option);
            }
        });
    });
}
	
	
	
	
	function deleteModule(id, name) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: `คุณต้องการลบระบบ "${name}" ใช่หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#9CA3AF',
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            // แสดง loading
            Swal.fire({
                title: 'กำลังลบข้อมูล...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `${site_url}System_member/delete_module/${id}`,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'สำเร็จ!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด!',
                            text: response.message || 'เกิดข้อผิดพลาดในการลบข้อมูล',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'เกิดข้อผิดพลาดในการลบข้อมูล';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch(e) {
                        console.error('Error parsing response:', e);
                    }

                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        text: errorMessage,
                        icon: 'error'
                    });
                }
            });
        }
    });
}

// เพิ่ม Event Listeners
// เพิ่ม Event Listener สำหรับ form submission
document.getElementById('moduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // ตรวจสอบข้อมูลที่จำเป็น
    const name = document.getElementById('moduleName').value;
    const code = document.getElementById('moduleCode').value;
    const displayOrder = document.getElementById('displayOrder').value;
    const moduleId = document.getElementById('moduleId').value;

    if (!name || !code || !displayOrder) {
        Swal.fire({
            title: 'ข้อผิดพลาด!',
            text: 'กรุณากรอกข้อมูลให้ครบถ้วน',
            icon: 'error'
        });
        return;
    }

    // รวบรวมข้อมูลเมนู
    const menuItems = document.querySelectorAll('.menu-item');
    const menus = [];
    
    menuItems.forEach(item => {
        // เพิ่มการตรวจสอบการมีอยู่ของ element
        const menuIdElement = item.querySelector('input[name="menu_ids[]"]');
        const menuNameElement = item.querySelector('input[name="menu_names[]"]');
        const menuCodeElement = item.querySelector('input[name="menu_codes[]"]');
        const menuUrlElement = item.querySelector('input[name="menu_urls[]"]');
        const menuIconElement = item.querySelector('input[name="menu_icons[]"]');
        const menuParentElement = item.querySelector('select[name="menu_parents[]"]');
        const menuOrderElement = item.querySelector('input[name="menu_orders[]"]');
        const menuStatusElement = item.querySelector('input[name="menu_status[]"]');
        
        // ตรวจสอบว่า element จำเป็นมีอยู่ก่อน
        if (menuNameElement && menuCodeElement) {
            const menuItem = {
                name: menuNameElement.value || '',
                code: menuCodeElement.value || '',
                url: menuUrlElement ? (menuUrlElement.value || '') : '',
                icon: menuIconElement ? (menuIconElement.value || '') : '',
                parent_code: menuParentElement ? (menuParentElement.value || '') : '',
                display_order: menuOrderElement ? (menuOrderElement.value || '1') : '1',
                status: menuStatusElement ? menuStatusElement.checked : false
            };
            
            // เพิ่ม id ถ้ามี (สำหรับกรณีแก้ไข)
            if (menuIdElement && menuIdElement.value) {
                menuItem.id = menuIdElement.value;
            }
            
            menus.push(menuItem);
        }
    });

    console.log('Module data:', {
        id: moduleId,
        name: name,
        code: code,
        description: document.getElementById('moduleDescription').value,
        display_order: displayOrder,
        menus: menus
    });

    // แสดง confirmation dialog
    Swal.fire({
        title: 'ยืนยันการบันทึก?',
        text: "คุณต้องการบันทึกข้อมูลใช่หรือไม่?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#EF4444',
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            // แสดง loading
            Swal.fire({
                title: 'กำลังบันทึกข้อมูล...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // ส่งข้อมูล
            $.ajax({
                url: `${site_url}System_member/save_module`,
                type: 'POST',
                data: {
                    id: moduleId,
                    name: name,
                    code: code,
                    description: document.getElementById('moduleDescription').value || '',
                    display_order: displayOrder,
                    menus: JSON.stringify(menus)
                },
                success: function(response) {
                    try {
                        // ถ้า response เป็น string ให้แปลงเป็น JSON
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        
                        if (response.success) {
                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด!',
                                text: response.message || 'ไม่สามารถบันทึกข้อมูลได้',
                                icon: 'error'
                            });
                        }
                    } catch (error) {
                        console.error('Error parsing response:', error, response);
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'ไม่สามารถประมวลผลการตอบกลับจากเซิร์ฟเวอร์',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้',
                        icon: 'error'
                    });
                }
            });
        }
    });
});
	
	</script>
		
		
		
		
		<script>
			
			
			
			// เพิ่มฟังก์ชันโหลด parent menus
function loadParentMenus(moduleId, menuId = null) {
    $.ajax({
        url: base_url + 'System_member/get_available_parent_menus',
        method: 'GET',
        data: {
            module_id: moduleId,
            exclude_id: menuId
        },
        success: function(response) {
            if (response.success) {
                const parentSelect = $('.parent-menu-select');
                parentSelect.empty();
                parentSelect.append('<option value="">-- เลือกเมนูหลัก --</option>');
                
                response.data.forEach(function(menu) {
                    parentSelect.append(
                        `<option value="${menu.id}">${menu.name}</option>`
                    );
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading parent menus:', error);
        }
    });
}

// อัพเดทเมนูเมื่อมีการเปลี่ยนแปลง parent
function updateMenuStructure() {
    const menus = [];
    $('.menu-item').each(function() {
        const menuItem = {
            name: $(this).find('.menu-name').val(),
            code: $(this).find('.menu-code').val(),
            url: $(this).find('.menu-url').val(),
            icon: $(this).find('.menu-icon').val(),
            parent_id: $(this).find('.parent-menu-select').val(),
            display_order: $(this).find('.menu-order').val(),
            status: $(this).find('.menu-status').prop('checked') ? 1 : 0
        };
        menus.push(menuItem);
    });
    
    return menus;
}



// Event handlers
$(document).on('change', '.parent-menu-select', function() {
    validateParentSelection(this);
    updateMenuStructure();
});

// เมื่อเพิ่มเมนูใหม่
$(document).on('click', '.add-menu-btn', function() {
    const moduleId = $('#module_id').val();
    loadParentMenus(moduleId);
});
	</script>




<!-- JavaScript สำหรับ modal -->
<script>
function viewModuleDetails(id) {
    // แสดง loading
    Swal.fire({
        title: 'กำลังโหลดข้อมูล...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // ดึงข้อมูล
    $.ajax({
        url: `${site_url}System_member/get_module/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                const { module, menus } = response.data;
                
                // แสดง Module ID ที่ header
                document.getElementById('viewModuleId').textContent = module.id;
                
                // ข้อมูลทั่วไป
                document.getElementById('viewModuleName').textContent = module.name;
                document.getElementById('viewModuleCode').textContent = module.code;
                document.getElementById('viewModuleOrder').textContent = module.status_op;
                document.getElementById('viewModuleDesc').textContent = module.description || '-';
                document.getElementById('viewModuleUsers').textContent = `${module.user_count || 0} คน`;

                // สถานะ
                const versionBadge = document.getElementById('viewModuleVersionBadge');
                versionBadge.textContent = module.is_trial ? 'Trial Version' : 'Full Version';
                versionBadge.className = `inline-flex rounded-full px-3 py-1 text-sm font-medium ${
                    module.is_trial ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'
                }`;

                const statusBadge = document.getElementById('viewModuleStatusBadge');
                statusBadge.textContent = module.status ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                statusBadge.className = `inline-flex rounded-full px-3 py-1 text-sm font-medium ${
                    module.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                }`;

                // เมนูและเงื่อนไข
                const menuContainer = document.getElementById('viewModuleMenus');
                menuContainer.innerHTML = '';

                if (menus && menus.length > 0) {
                    menus.forEach(menu => {
                        const menuItem = document.createElement('div');
                        menuItem.className = 'p-4 border rounded-lg bg-gray-50';
                        
                        const menuContent = `
                            <div class="flex gap-4 flex-wrap">
                                <div class="min-w-[120px]">
                                    <span class="text-sm text-gray-500">Menu ID (id):</span>
                                    <span class="ml-2 text-gray-900">${menu.id}</span>
                                </div>
                                <div class="min-w-[200px]">
                                    <span class="text-sm text-gray-500">เงื่อนไข (name):</span>
                                    <span class="ml-2 text-gray-900">${menu.name}</span>
                                </div>
                                <div class="min-w-[150px]">
                                    <span class="text-sm text-gray-500">รหัส (code):</span>
                                    <span class="ml-2 text-gray-900">${menu.code}</span>
                                </div>
                                <div class="min-w-[200px]">
                                    <span class="text-sm text-gray-500">URL (url):</span>
                                    <span class="ml-2 text-gray-900">${menu.url || '-'}</span>
                                </div>
                                <div class="min-w-[120px]">
                                    <span class="text-sm text-gray-500">Parent ID (parent_id):</span>
                                    <span class="ml-2 text-gray-900">${menu.parent_id || '-'}</span>
                                </div>
                                <div class="min-w-[120px]">
                                    <span class="text-sm text-gray-500">สถานะ (status):</span>
                                    <span class="ml-2 ${menu.status ? 'text-green-600' : 'text-red-600'}">
                                        ${menu.status ? 'เปิดใช้งาน' : 'ปิดใช้งาน'}
                                    </span>
                                </div>
                            </div>
                        `;
                        
                        menuItem.innerHTML = menuContent;
                        menuContainer.appendChild(menuItem);
                    });
                } else {
                    menuContainer.innerHTML = `
                        <div class="text-center text-gray-500">
                            ไม่พบข้อมูลเงื่อนไข
                        </div>
                    `;
                }

                // แสดง modal
                document.getElementById('viewModuleModal').classList.remove('hidden');

            } else {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: response.message,
                    icon: 'error'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {xhr, status, error});
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                icon: 'error'
            });
        }
    });
}

function closeViewModal() {
    document.getElementById('viewModuleModal').classList.add('hidden');
}

// ปิด modal เมื่อคลิกพื้นหลัง
document.getElementById('viewModuleModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeViewModal();
    }
});

// ปิด modal เมื่อกด ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('viewModuleModal').classList.contains('hidden')) {
        closeViewModal();
    }
});
</script>