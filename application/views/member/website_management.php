<!-- views/member/website_management.php -->
<div class="ml-72 p-8">
    <script>
        var site_url = '<?php echo site_url(); ?>';
    </script>
    
    <!-- ส่วนหัว -->
    <div class="mb-8">
        <h1 class="text-2xl font-medium text-gray-800">จัดการเว็บไซต์</h1>
        <p class="text-gray-500">จัดการสิทธิ์ผู้ใช้ในการเข้าถึงและแก้ไขส่วนต่างๆ ของเว็บไซต์</p>
    </div>

    <!-- ฟอร์มหลัก -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <div class="p-8 border-b border-gray-100">
            <h2 class="text-xl font-medium text-gray-800">รายชื่อผู้ใช้งานที่มีสิทธิ์จัดการเว็บไซต์</h2>
        </div>

     
        <!-- ส่วนแสดงข้อมูลผู้ใช้และการจัดการ -->
<div class="p-8">
    <!-- Header actions -->
    <div class="flex justify-between items-center mb-6">
        <button type="button" onclick="importWebUsers()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-user-plus mr-2"></i>เพิ่มผู้ใช้
        </button>
        <!-- แสดงสรุปจำนวนผู้ใช้ -->
        <div class="text-gray-600">
            แสดง <?php echo $start_row; ?> ถึง <?php echo $end_row; ?> จากทั้งหมด <?php echo $total_rows; ?> รายการ
        </div>
    </div>

    <!-- ตารางแสดงรายชื่อผู้ใช้ -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
    <th class="px-6 py-3 text-left text-base font-medium text-gray-700 uppercase tracking-wider">ชื่อ-นามสกุล</th>
    <th class="px-6 py-3 text-left text-base font-medium text-gray-700 uppercase tracking-wider">อีเมล</th>
    <th class="px-6 py-3 text-left text-base font-medium text-gray-700 uppercase tracking-wider">สิทธิ์การใช้งาน</th>
    <th class="px-6 py-3 text-center text-base font-medium text-gray-700 uppercase tracking-wider">จัดการ</th>
</tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (isset($web_users) && !empty($web_users)): ?>
                    <?php foreach ($web_users as $user): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <?php echo $user->m_fname . ' ' . $user->m_lname; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php echo $user->m_email; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if (isset($user->has_all_permissions) && $user->has_all_permissions): ?>
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                        ทั้งหมด
                                    </span>
                                <?php elseif (!empty($user->permissions)): ?>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach (array_slice($user->permissions, 0, 3) as $perm): ?>
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                                <?php echo $perm; ?>
                                            </span>
                                        <?php endforeach; ?>
                                        
                                        <?php if (count($user->permissions) > 3): ?>
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full cursor-pointer" 
                                                  title="<?php echo htmlspecialchars(implode(', ', $user->permissions)); ?>">
                                                +<?php echo count($user->permissions) - 3; ?> อื่นๆ
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-500">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" onclick="editWebUser(<?php echo $user->m_id; ?>)" 
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" onclick="removeWebUser(<?php echo $user->m_id; ?>)"
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
        <?php echo $pagination; ?>
    </div>
</div>
		
		
		
    </div>
</div>

<script>
async function editWebUser(userId) {
    try {
        // แสดง loading แบบปิดเองไม่ได้
        Swal.fire({
            title: 'กำลังโหลดข้อมูล...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // ดึงข้อมูลผู้ใช้
        const user = <?php echo json_encode($web_users); ?>.find(u => u.m_id == userId);
        if (!user) {
            Swal.close();
            throw new Error('ไม่พบข้อมูลผู้ใช้');
        }

        // ดึงข้อมูลสิทธิ์
        const allPermissions = <?php echo json_encode($all_permissions); ?>;
        
        // กรองเอาเฉพาะสิทธิ์ที่ไม่ใช่ "ทั้งหมด" (grant_user_id != 1)
        const filteredPermissions = allPermissions.filter(perm => perm.grant_user_id != 1);
        
        // เตรียมข้อมูลสิทธิ์ที่มีอยู่
        let userPermIds = [];
        let hasAllPermissions = false;
        
        if (user.grant_user_ref_id) {
            userPermIds = user.grant_user_ref_id.split(',');
            hasAllPermissions = userPermIds.includes('1');
        }

        // ปิด loading
        Swal.close();
        
        // สร้าง HTML สำหรับ dialog
        const dialogHtml = `
            <div>
                <h3 class="text-left font-medium mb-2">ข้อมูลผู้ใช้</h3>
                <div class="text-left mb-4 p-3 bg-gray-50 rounded-lg">
                    <p><strong>ชื่อ-นามสกุล:</strong> ${user.m_fname} ${user.m_lname}</p>
                    <p><strong>อีเมล:</strong> ${user.m_email || '-'}</p>
                </div>
            </div>
            <div>
                <h3 class="text-left font-medium mb-2">กำหนดสิทธิ์</h3>
                <div class="flex items-center mb-3">
                    <input type="checkbox" id="all_permissions" name="all_permissions" value="1" class="form-checkbox h-4 w-4 text-blue-600" ${hasAllPermissions ? 'checked' : ''}>
                    <label class="ml-2 font-medium">สิทธิ์ทั้งหมด</label>
                </div>
                <div id="specific_permissions" class="max-h-60 overflow-y-auto border rounded-lg p-2 ${hasAllPermissions ? 'opacity-50 pointer-events-none' : ''}">
                    ${filteredPermissions.map(perm => `
                        <div class="flex items-center space-x-3 mb-2">
                            <input type="checkbox" name="permissions[]" value="${perm.grant_user_id}" 
                                   class="permission-checkbox form-checkbox h-4 w-4 text-blue-600"
                                   ${hasAllPermissions ? 'disabled' : ''} 
                                   ${!hasAllPermissions && userPermIds.includes(perm.grant_user_id.toString()) ? 'checked' : ''}>
                            <label>${perm.grant_user_name}</label>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        // แสดง dialog แก้ไขสิทธิ์แบบไม่มี showLoaderOnConfirm
        const result = await Swal.fire({
            title: 'แก้ไขสิทธิ์การใช้งาน',
            html: dialogHtml,
            width: '600px',
            customClass: {
                popup: 'swal-wide',
                content: 'text-left'
            },
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก',
            willOpen: () => {
                const allPermCheckbox = document.getElementById('all_permissions');
                allPermCheckbox.addEventListener('change', function() {
                    const specificPerms = document.getElementById('specific_permissions');
                    const checkboxes = document.querySelectorAll('.permission-checkbox');
                    
                    if (this.checked) {
                        specificPerms.classList.add('opacity-50', 'pointer-events-none');
                        checkboxes.forEach(cb => {
                            cb.disabled = true;
                            cb.checked = false;
                        });
                    } else {
                        specificPerms.classList.remove('opacity-50', 'pointer-events-none');
                        checkboxes.forEach(cb => {
                            cb.disabled = false;
                        });
                    }
                });
            }
        });

        if (result.isConfirmed) {
            // รวบรวมข้อมูล
            const allPerms = document.getElementById('all_permissions').checked;
            let selectedPermissions = [];
            
            if (allPerms) {
                selectedPermissions = ['1']; // สิทธิ์ทั้งหมด
            } else {
                const perms = document.querySelectorAll('input[name="permissions[]"]:checked');
                if (perms.length === 0) {
                    await Swal.fire({
                        icon: 'error',
                        title: 'ข้อมูลไม่ครบถ้วน',
                        text: 'กรุณาเลือกสิทธิ์อย่างน้อย 1 รายการ'
                    });
                    return;
                }
                selectedPermissions = Array.from(perms).map(cb => cb.value);
            }

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
                const response = await fetch(`${site_url}/System_member/update_web_user_permissions`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        permissions: selectedPermissions
                    })
                });

                const data = await response.json();
                
                // ปิด loading
                Swal.close();
                
                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'บันทึกข้อมูลเรียบร้อยแล้ว'
                    });
                    location.reload();
                } else {
                    throw new Error(data.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
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
	
	
	
	
	
	
	
	

async function importWebUsers() {
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
        const response = await fetch(`${site_url}/System_member/get_available_web_users`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Cache-Control': 'no-cache'
            }
        });

        // ตรวจสอบ response
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error: ${response.status}, ${errorText}`);
        }

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'ไม่สามารถดึงข้อมูลผู้ใช้ได้');
        }

        const users = result.data;
        if (!users || users.length === 0) {
            // ปิด loading ก่อนแสดง dialog ใหม่
            Swal.close();
            
            await Swal.fire({
                icon: 'info',
                title: 'ไม่พบผู้ใช้',
                text: 'ไม่มีผู้ใช้ที่สามารถเพิ่มได้'
            });
            return;
        }

        // ดึงข้อมูลสิทธิ์
        const allPermissions = <?php echo json_encode($all_permissions); ?>;
        
        // กรองเอาเฉพาะสิทธิ์ที่ไม่ใช่ "ทั้งหมด" (grant_user_id != 1)
        const filteredPermissions = allPermissions.filter(perm => perm.grant_user_id != 1);

        // ปิด loading ก่อนแสดง dialog ใหม่
        Swal.close();

        // แสดง dialog เลือกผู้ใช้และสิทธิ์
        const result2 = await Swal.fire({
            title: 'เพิ่มผู้ใช้',
            html: `
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
                    <h3 class="text-left font-medium mb-2">กำหนดสิทธิ์</h3>
                    <div class="flex items-center mb-3">
                        <input type="checkbox" id="all_permissions" name="all_permissions" value="1" class="form-checkbox h-4 w-4 text-blue-600">
                        <label class="ml-2 font-medium">สิทธิ์ทั้งหมด</label>
                    </div>
                    <div id="specific_permissions" class="max-h-60 overflow-y-auto border rounded-lg p-2">
                        ${filteredPermissions.map(perm => `
                            <div class="flex items-center space-x-3 mb-2">
                                <input type="checkbox" name="permissions[]" value="${perm.grant_user_id}" 
                                       class="permission-checkbox form-checkbox h-4 w-4 text-blue-600">
                                <label>${perm.grant_user_name}</label>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `,
            width: '600px',
            customClass: {
                popup: 'swal-wide',
                htmlContainer: 'text-left'
            },
            showCancelButton: true,
            confirmButtonText: 'เพิ่ม',
            cancelButtonText: 'ยกเลิก',
            willOpen: () => {
                // เพิ่ม event listener สำหรับตัวเลือก "สิทธิ์ทั้งหมด" ทันทีที่ dialog เปิด
                const allPermCheckbox = document.getElementById('all_permissions');
                allPermCheckbox.addEventListener('change', function() {
                    const specificPerms = document.getElementById('specific_permissions');
                    const checkboxes = document.querySelectorAll('.permission-checkbox');
                    
                    if (this.checked) {
                        // ถ้าเลือก "สิทธิ์ทั้งหมด" ให้ disable ตัวเลือกย่อยทั้งหมด
                        specificPerms.classList.add('opacity-50', 'pointer-events-none');
                        checkboxes.forEach(cb => {
                            cb.disabled = true;
                            cb.checked = false;
                        });
                    } else {
                        // ถ้ายกเลิกการเลือก "สิทธิ์ทั้งหมด" ให้ enable ตัวเลือกย่อยทั้งหมด
                        specificPerms.classList.remove('opacity-50', 'pointer-events-none');
                        checkboxes.forEach(cb => {
                            cb.disabled = false;
                        });
                    }
                });
            }
        });

        if (result2.isConfirmed) {
            // รวบรวมข้อมูล
            const selected = document.querySelectorAll('input[name="selected_users[]"]:checked');
            if (selected.length === 0) {
                await Swal.fire({
                    icon: 'error',
                    title: 'ข้อมูลไม่ครบถ้วน',
                    text: 'กรุณาเลือกผู้ใช้อย่างน้อย 1 คน'
                });
                return;
            }
            
            const selectedUsers = Array.from(selected).map(cb => cb.value);
            let selectedPermissions = [];
            
            // ตรวจสอบว่าเลือกสิทธิ์ทั้งหมดหรือไม่
            const allPerms = document.getElementById('all_permissions').checked;
            if (allPerms) {
                selectedPermissions = ['1']; // สิทธิ์ทั้งหมด
            } else {
                const perms = document.querySelectorAll('input[name="permissions[]"]:checked');
                if (perms.length === 0) {
                    await Swal.fire({
                        icon: 'error',
                        title: 'ข้อมูลไม่ครบถ้วน',
                        text: 'กรุณาเลือกสิทธิ์อย่างน้อย 1 รายการ'
                    });
                    return;
                }
                selectedPermissions = Array.from(perms).map(cb => cb.value);
            }
            
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
                const addResponse = await fetch(`${site_url}/System_member/add_web_users`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        user_ids: selectedUsers,
                        permissions: selectedPermissions
                    })
                });

                const addResult = await addResponse.json();

                // ปิด loading
                Swal.close();

                if (addResult.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'เพิ่มผู้ใช้เรียบร้อยแล้ว'
                    });
                    location.reload();
                } else {
                    throw new Error(addResult.message || 'ไม่สามารถเพิ่มผู้ใช้ได้');
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
	
	
	
	
	

async function removeWebUser(userId) {
    try {
        // แสดง dialog ยืนยันการลบ
        const confirmResult = await Swal.fire({
            title: 'ยืนยันการลบ',
            text: 'คุณต้องการลบสิทธิ์การจัดการเว็บไซต์ของผู้ใช้นี้ใช่หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก',
            showLoaderOnConfirm: true,
            preConfirm: async () => {
                try {
                    const response = await fetch(`${site_url}/System_member/remove_web_user`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            user_id: userId
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error: ${response.status}`);
                    }

                    const result = await response.json();
                    if (!result.success) {
                        throw new Error(result.message);
                    }

                    return result;
                } catch (error) {
                    Swal.showValidationMessage(error.message);
                }
            }
        });

        if (confirmResult.isConfirmed) {
            await Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: 'ลบสิทธิ์เรียบร้อยแล้ว'
            });
            location.reload();
        }
    } catch (error) {
        console.error('Remove error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: error.message || 'ไม่สามารถลบสิทธิ์ได้'
        });
    }
}
</script>