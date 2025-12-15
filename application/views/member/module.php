<!-- module.php ที่ปรับปรุงแล้ว -->
<div class="ml-72 p-8">
	
    <script>
        var site_url = '<?php echo site_url(); ?>';
    </script>
	
    <!-- ส่วนหัว -->
    <div class="mb-8">
        <h1 class="text-2xl font-medium text-gray-800"><?php echo isset($module) ? $module->name : 'จัดการโมดูล'; ?></h1>
        <p class="text-gray-500"><?php echo isset($module) ? $module->description : ''; ?></p>
    </div>

    <!-- ฟอร์มหลัก -->
    <form id="moduleForm" method="post" class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <div class="p-8 border-b border-gray-100">
            <h2 class="text-xl font-medium text-gray-800">รายชื่อสมาชิกที่ใช้งาน<?php echo isset($module) ? $module->name : ''; ?></h2>
        </div>

        <!-- ส่วนแสดงข้อมูลผู้ใช้และการจัดการ -->
        <div class="p-8">
            <!-- Header actions -->
<div class="flex justify-between items-center mb-6">
    <button type="button" onclick="importUsers()"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        <i class="fas fa-user-plus mr-2"></i>เพิ่มผู้ใช้
    </button>
                <!-- แสดงสรุปจำนวนผู้ใช้ -->
                   <div class="text-gray-600">
        <?php 
        // ตรวจสอบตัวแปรว่ามีค่าหรือไม่ก่อนนำไปใช้
        $start = isset($start_row) ? $start_row : 1;
        $end = isset($end_row) ? $end_row : count($module_users ?? []);
        $total = isset($total_rows) ? $total_rows : count($module_users ?? []);
        
        echo "แสดง {$start} ถึง {$end} จากทั้งหมด {$total} รายการ";
        ?>
    </div>
</div>

            <!-- ตารางแสดงรายชื่อผู้ใช้ -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-lg font-medium text-gray-700 uppercase tracking-wider">ชื่อ-นามสกุล</th>
                            <th class="px-6 py-3 text-left text-lg font-medium text-gray-700 uppercase tracking-wider">อีเมล</th>
                            <th class="px-6 py-3 text-left text-lg font-medium text-gray-700 uppercase tracking-wider">สิทธิ์การใช้งาน</th>
                            <th class="px-6 py-3 text-center text-lg font-medium text-gray-700 uppercase tracking-wider">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (isset($module_users) && !empty($module_users)): ?>
                            <?php foreach ($module_users as $user): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <?php echo $user->m_fname . ' ' . $user->m_lname; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo $user->m_email; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            <?php 
                                            if (!empty($user->permissions)):
                                                $perms = explode(',', $user->permissions);
                                                foreach ($perms as $perm):
                                            ?>
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                                    <?php echo $perm; ?>
                                                </span>
                                            <?php 
                                                endforeach;
                                            endif;
                                            ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button type="button" onclick="editUser(<?php echo $user->m_id; ?>)" 
                                                class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" onclick="removeUser(<?php echo $user->m_id; ?>)"
                                                class="text-red-600 hover:text-red-900 ml-3">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    ไม่พบข้อมูลผู้ใช้งาน
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- แสดง pagination -->
<div class="flex justify-center mt-6">
    <?php echo isset($pagination) ? $pagination : ''; ?>
</div>
        </div>
    </form>
</div>

<script>
async function importUsers() {
    try {
        // แสดง loading
        Swal.fire({
            title: 'กำลังโหลดข้อมูล...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // ดึงรายชื่อผู้ใช้ที่ยังไม่มีในระบบ
        const response = await fetch(`${site_url}/System_member/get_available_users?module_id=<?php echo $module->id; ?>`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Cache-Control': 'no-cache'
            }
        });

        // ตรวจสอบ response
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'ไม่สามารถดึงข้อมูลผู้ใช้ได้');
        }

        const users = result.data;
        if (!users || users.length === 0) {
            // ปิด loading
            Swal.close();
            
            await Swal.fire({
                icon: 'info',
                title: 'ไม่พบผู้ใช้',
                text: 'ไม่มีผู้ใช้ที่สามารถเพิ่มได้'
            });
            return;
        }

        // ดึงข้อมูลสิทธิ์ที่มีในโมดูลนี้
        const moduleId = <?php echo $module->id; ?>;
        const permissionsResponse = await fetch(`${site_url}/System_member/get_module_permissions?module_id=${moduleId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Cache-Control': 'no-cache'
            }
        });

        if (!permissionsResponse.ok) {
            throw new Error(`HTTP error! status: ${permissionsResponse.status}`);
        }

        const permissionsResult = await permissionsResponse.json();
        const modulePermissions = permissionsResult.success ? permissionsResult.data : [];

        // ปิด loading
        Swal.close();

        // สร้าง HTML สำหรับเลือกผู้ใช้และสิทธิ์
        const importHtml = `
            <div class="mb-4">
                <h3 class="text-left font-medium mb-2">เลือกผู้ใช้</h3>
                <div class="max-h-40 overflow-y-auto border rounded-lg p-2 mb-4">
                    ${users.map(user => `
                        <div class="flex items-center space-x-3 mb-2">
                            <input type="checkbox" name="selected_users[]" value="${user.m_id}" 
                                   class="form-checkbox h-4 w-4 text-blue-600">
                            <label>${user.m_fname} ${user.m_lname} (${user.m_email || 'ไม่ระบุอีเมล'})</label>
                        </div>
                    `).join('')}
                </div>
            </div>
            <div>
                <h3 class="text-left font-medium mb-2">เลือกสิทธิ์การใช้งาน</h3>
                <div class="max-h-60 overflow-y-auto border rounded-lg p-2">
                    ${modulePermissions.map(perm => `
                        <div class="flex items-center space-x-3 mb-2">
                            <input type="checkbox" name="permissions[]" value="${perm.id}" 
                                   class="form-checkbox h-4 w-4 text-blue-600">
                            <label>${perm.name}</label>
                        </div>
                    `).join('')}
                </div>
            </div>
            <style>
                ::-webkit-scrollbar {
                    width: 8px;
                }
                ::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 10px;
                }
                ::-webkit-scrollbar-thumb {
                    background: #c0c0c0;
                    border-radius: 10px;
                }
                ::-webkit-scrollbar-thumb:hover {
                    background: #a0a0a0;
                }
            </style>
        `;

        // แสดง dialog เลือกผู้ใช้และสิทธิ์
        const result2 = await Swal.fire({
            title: 'เพิ่มผู้ใช้',
            html: importHtml,
            width: '600px',
            customClass: {
                popup: 'rounded-lg shadow-lg border border-gray-100',
                content: 'text-left'
            },
            showCancelButton: true,
            confirmButtonText: 'เพิ่ม',
            cancelButtonText: 'ยกเลิก'
        });

        if (result2.isConfirmed) {
            // รวบรวมข้อมูลที่เลือก
            const selectedUsers = document.querySelectorAll('input[name="selected_users[]"]:checked');
            if (selectedUsers.length === 0) {
                await Swal.fire({
                    icon: 'error',
                    title: 'ข้อมูลไม่ครบถ้วน',
                    text: 'กรุณาเลือกผู้ใช้อย่างน้อย 1 คน'
                });
                return;
            }
            
            const selectedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked');
            if (selectedPermissions.length === 0) {
                await Swal.fire({
                    icon: 'error',
                    title: 'ข้อมูลไม่ครบถ้วน',
                    text: 'กรุณาเลือกสิทธิ์อย่างน้อย 1 รายการ'
                });
                return;
            }
            
            const user_ids = Array.from(selectedUsers).map(cb => cb.value);
            const permissions = Array.from(selectedPermissions).map(cb => cb.value);

            // แสดง loading ระหว่างการบันทึก
            Swal.fire({
                title: 'กำลังบันทึกข้อมูล...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const importResponse = await fetch(`${site_url}/System_member/import_module_users`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        module_id: moduleId,
                        user_ids: user_ids,
						permissions: permissions
                    })
                });

                if (!importResponse.ok) {
                    throw new Error(`HTTP error! status: ${importResponse.status}`);
                }

                const importResult = await importResponse.json();
                
                // ปิด loading
                Swal.close();
                
                if (importResult.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'เพิ่มผู้ใช้เรียบร้อยแล้ว'
                    });
                    location.reload();
                } else {
                    throw new Error(importResult.message || 'ไม่สามารถเพิ่มผู้ใช้ได้');
                }
            } catch (error) {
                Swal.close();
                throw error;
            }
        }
    } catch (error) {
        console.error('Import error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: error.message || 'ไม่สามารถเพิ่มผู้ใช้ได้'
        });
    }
}

async function editUser(userId) {
    try {
        // แสดง loading
        Swal.fire({
            title: 'กำลังโหลดข้อมูล...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        const moduleId = <?php echo $module->id; ?>;
        
        // ดึงข้อมูลสิทธิ์ของผู้ใช้
        const response = await fetch(`${site_url}/System_member/get_member_permissions/${userId}?module_id=${moduleId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        // ปิด loading
        Swal.close();

        if (!result.success) {
            throw new Error(result.message || 'ไม่สามารถดึงข้อมูลสิทธิ์ได้');
        }

        // ดึงข้อมูลผู้ใช้
        const user = <?php echo json_encode($module_users); ?>.find(u => u.m_id == userId);
        if (!user) {
            throw new Error('ไม่พบข้อมูลผู้ใช้');
        }

        // สร้าง HTML สำหรับแสดงสิทธิ์
        const permissionsHtml = `
            <div>
                <h3 class="text-left font-medium mb-2">ข้อมูลผู้ใช้</h3>
                <div class="text-left mb-4 p-3 bg-gray-50 rounded-lg">
                    <p><strong>ชื่อ-นามสกุล:</strong> ${user.m_fname} ${user.m_lname}</p>
                    <p><strong>อีเมล:</strong> ${user.m_email || '-'}</p>
                </div>
            </div>
            <div class="max-h-96 overflow-y-auto p-4">
                <div class="space-y-4">
                    ${result.data.map(perm => `
                        <div class="perm-row flex items-center p-3 hover:bg-gray-50 rounded-lg border border-gray-100 shadow-sm cursor-pointer" data-id="${perm.id}">
                            <input type="checkbox" 
                                   id="perm_${perm.id}"
                                   name="permissions[]" 
                                   value="${perm.id}" 
                                   ${perm.is_checked == 1 ? 'checked' : ''}
                                   class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer">
                            <label for="perm_${perm.id}" class="ml-3 text-sm text-gray-700 cursor-pointer select-none w-full">
                                ${perm.name}
                            </label>
                        </div>
                    `).join('')}
                </div>
            </div>
            <style>
                ::-webkit-scrollbar {
                    width: 8px;
                }
                ::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 10px;
                }
                ::-webkit-scrollbar-thumb {
                    background: #c0c0c0;
                    border-radius: 10px;
                }
                ::-webkit-scrollbar-thumb:hover {
                    background: #a0a0a0;
                }
                .perm-row:active {
                    background-color: #e5e7eb;
                }
            </style>
        `;

        // แสดง dialog แก้ไขสิทธิ์
        const result2 = await Swal.fire({
            title: 'แก้ไขสิทธิ์การใช้งาน',
            html: permissionsHtml,
            width: '550px',
            customClass: {
                popup: 'rounded-lg shadow-lg border border-gray-100',
                content: 'text-left'
            },
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก',
            willOpen: () => {
                // เพิ่มการจัดการคลิกที่แถวเพื่อเลือก/ยกเลิกการเลือก checkbox
                const permRows = document.querySelectorAll('.perm-row');
                permRows.forEach(row => {
                    row.addEventListener('click', function(e) {
                        // ถ้าคลิกที่ checkbox โดยตรง ให้ข้ามไป
                        if (e.target.type === 'checkbox') return;
                        
                        // หาตัว checkbox ภายในแถวนี้
                        const permId = this.getAttribute('data-id');
                        const checkbox = document.getElementById(`perm_${permId}`);
                        
                        // สลับสถานะ checkbox
                        checkbox.checked = !checkbox.checked;
                    });
                });
            }
        });

        if (result2.isConfirmed) {
            // รวบรวมข้อมูลที่เลือก
            const selected = document.querySelectorAll('input[name="permissions[]"]:checked');
            if (selected.length === 0) {
                await Swal.fire({
                    icon: 'error',
                    title: 'ข้อมูลไม่ครบถ้วน',
                    text: 'กรุณาเลือกสิทธิ์อย่างน้อย 1 รายการ'
                });
                return;
            }
            
            const permissions = Array.from(selected).map(cb => cb.value);

            // แสดง loading ระหว่างการบันทึก
            Swal.fire({
                title: 'กำลังบันทึกข้อมูล...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const updateResponse = await fetch(`${site_url}/System_member/update_user_permissions`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        module_id: moduleId,
                        user_id: userId,
                        permissions: permissions
                    })
                });

                if (!updateResponse.ok) {
                    throw new Error(`HTTP error! status: ${updateResponse.status}`);
                }

                const updateResult = await updateResponse.json();
                
                // ปิด loading
                Swal.close();
                
                if (updateResult.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'อัพเดทสิทธิ์เรียบร้อยแล้ว'
                    });
                    location.reload();
                } else {
                    throw new Error(updateResult.message || 'ไม่สามารถอัพเดทสิทธิ์ได้');
                }
            } catch (error) {
                Swal.close();
                throw error;
            }
        }
    } catch (error) {
        console.error('Edit error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: error.message || 'ไม่สามารถแก้ไขสิทธิ์ได้'
        });
    }
}

async function removeUser(userId) {
    try {
        const moduleId = <?php echo $module->id; ?>;
        
        // Show confirmation dialog
        const confirmResult = await Swal.fire({
            title: 'ยืนยันการลบ',
            text: 'คุณต้องการลบผู้ใช้นี้ออกจากระบบใช่หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก',
            showLoaderOnConfirm: true,
            preConfirm: async () => {
                try {
                    const response = await fetch(`${site_url}/System_member/remove_module_user`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            module_id: moduleId
                        })
                    });

                    // Check if response is ok
                    if (!response.ok) {
                        const text = await response.text();
                        throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
                    }

                    // Try to parse response as JSON
                    let result;
                    try {
                        const text = await response.text();
                        result = JSON.parse(text);
                    } catch (e) {
                        console.error('Parse error:', e);
                        throw new Error('Invalid server response');
                    }

                    if (!result.success) {
                        throw new Error(result.message || 'เกิดข้อผิดพลาดในการลบข้อมูล');
                    }

                    return result;
                } catch (error) {
                    console.error('Remove error:', error);
                    Swal.showValidationMessage(error.message);
                }
            }
        });

        if (confirmResult.isConfirmed && confirmResult.value.success) {
            await Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: confirmResult.value.message || 'ลบข้อมูลเรียบร้อยแล้ว',
                timer: 1500
            });
            location.reload();
        }

    } catch (error) {
        console.error('Error:', error);
        await Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: error.message || 'ไม่สามารถลบข้อมูลได้'
        });
    }
}
</script>