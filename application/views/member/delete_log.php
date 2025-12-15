<!-- Main Content -->
<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">ประวัติการลบข้อมูลสมาชิก</h2>
            <p class="text-gray-600">แสดงประวัติการลบสมาชิกทั้งหมดในระบบ</p>
        </div>
        <div class="flex gap-4">
            <!-- ปุ่ม Export หากต้องการเพิ่ม -->
            <!-- <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </button> -->
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" action="<?php echo site_url('System_member/delete_log'); ?>" class="flex flex-wrap gap-4">
                <!-- ค้นหาด้วยชื่อผู้ลบ -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ค้นหาผู้ลบ</label>
                    <input type="text"
                        name="search_user"
                        value="<?php echo $this->input->get('search_user'); ?>"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500">
                </div>

                <!-- Filter ตามช่วงวันที่ -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">จากวันที่</label>
                    <input type="date"
                        name="date_from"
                        value="<?php echo $this->input->get('date_from'); ?>"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500">
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ถึงวันที่</label>
                    <input type="date"
                        name="date_to"
                        value="<?php echo $this->input->get('date_to'); ?>"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="h-10 px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-search mr-2"></i>ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3 text-gray-600">ลำดับ</th>
                        <th class="px-6 py-3 text-gray-600">ผู้ลบ</th>
                        <th class="px-6 py-3 text-gray-600">ชื่อผู้ใช้</th>
                        <th class="px-6 py-3 text-gray-600">ข้อมูลที่ถูกลบ</th>
                        <th class="px-6 py-3 text-gray-600">หมายเหตุ</th>
                        <th class="px-6 py-3 text-gray-600">วันที่ลบ</th>
                        <th class="px-6 py-3 text-gray-600">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($logs as $index => $log): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-600"><?php echo $index + 1; ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo $log->deleted_by; ?></td>
                            <td class="px-6 py-4 text-gray-600">
                                <?php 
                                // แสดงชื่อผู้ใช้จากข้อมูลที่ถูกลบ
                                if (isset($log->deleted_data->m_fname) && isset($log->deleted_data->m_lname)) {
                                    echo $log->deleted_data->m_fname . ' ' . $log->deleted_data->m_lname;
                                } else if (isset($log->deleted_data->mp_fname) && isset($log->deleted_data->mp_lname)) {
                                    echo $log->deleted_data->mp_fname . ' ' . $log->deleted_data->mp_lname;
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4">
                                <button onclick="showDetails(<?php echo htmlspecialchars(json_encode($log->deleted_data)); ?>, '<?php echo $log->table_name; ?>', <?php echo $log->log_id; ?>)"
                                    class="text-blue-600 hover:text-blue-800 flex items-center transition-colors duration-200">
                                    <i class="fas fa-info-circle mr-1"></i> ดูรายละเอียด
                                </button>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?php echo $log->reason; ?></td>
                            <td class="px-6 py-4 text-gray-600">
                                <?php
                                $date = new DateTime($log->delete_date);
                                echo $date->format('d/m/Y H:i:s');
                                ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-2">
                                    <button onclick="confirmRestore(<?php echo $log->log_id; ?>, '<?php 
                                        // ตรวจสอบชื่อของข้อมูลที่ถูกลบ
                                        if (isset($log->deleted_data->m_fname) && isset($log->deleted_data->m_lname)) {
                                            echo $log->deleted_data->m_fname . ' ' . $log->deleted_data->m_lname;
                                        } else if (isset($log->deleted_data->mp_fname) && isset($log->deleted_data->mp_lname)) {
                                            echo $log->deleted_data->mp_fname . ' ' . $log->deleted_data->mp_lname;
                                        } else {
                                            echo 'ไม่ระบุชื่อ';
                                        }
                                    ?>')"
                                        class="text-green-600 hover:text-green-800 flex items-center transition-colors duration-200">
                                        <i class="fas fa-undo mr-1"></i> กู้คืน
                                    </button>
                                    <button onclick="confirmPermanentDelete(<?php echo $log->log_id; ?>, '<?php 
                                        // ตรวจสอบชื่อของข้อมูลที่ถูกลบ
                                        if (isset($log->deleted_data->m_fname) && isset($log->deleted_data->m_lname)) {
                                            echo $log->deleted_data->m_fname . ' ' . $log->deleted_data->m_lname;
                                        } else if (isset($log->deleted_data->mp_fname) && isset($log->deleted_data->mp_lname)) {
                                            echo $log->deleted_data->mp_fname . ' ' . $log->deleted_data->mp_lname;
                                        } else {
                                            echo 'ไม่ระบุชื่อ';
                                        }
                                    ?>')"
                                        class="text-red-600 hover:text-red-800 flex items-center transition-colors duration-200">
                                        <i class="fas fa-trash-alt mr-1"></i> ลบถาวร
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <?php if (isset($pagination)): ?>
            <div class="p-6 border-t">
                <div class="flex justify-between items-center">
                    <div class="text-gray-600">
                        แสดง <?php echo $start_row; ?>-<?php echo $end_row; ?> จาก <?php echo $total_rows; ?> รายการ
                    </div>
                    <?php echo $pagination; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Scripts for modals and notifications -->
<script>
    var site_url = '<?php echo site_url(); ?>';

    <?php if ($this->session->flashdata('restore_success')) { ?>
        Swal.fire({
            icon: 'success',
            title: 'กู้คืนข้อมูลสำเร็จ',
            showConfirmButton: false,
            timer: 1500
        });
    <?php } ?>

    <?php if ($this->session->flashdata('restore_error')) { ?>
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: '<?php echo $this->session->flashdata("error_message") ?: "ไม่สามารถกู้คืนข้อมูลได้"; ?>',
            confirmButtonText: 'ตกลง'
        });
    <?php } ?>

    <?php if ($this->session->flashdata('delete_success')) { ?>
        Swal.fire({
            icon: 'success',
            title: 'ลบข้อมูลถาวรสำเร็จ',
            text: 'ข้อมูลได้ถูกลบออกจากระบบอย่างถาวรแล้ว',
            showConfirmButton: false,
            timer: 1500
        });
    <?php } ?>

    // ฟังก์ชันยืนยันการกู้คืนข้อมูล
    function confirmRestore(logId, name) {
        Swal.fire({
            title: 'ยืนยันการกู้คืนข้อมูล?',
            text: `คุณต้องการกู้คืนข้อมูลของ ${name} ใช่หรือไม่?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'ใช่, กู้คืนข้อมูล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `${site_url}/System_member/restore_member/${logId}`;
            }
        });
    }

    // ฟังก์ชันยืนยันการลบถาวร
    function confirmPermanentDelete(logId, name) {
        Swal.fire({
            title: 'ยืนยันการลบถาวร?',
            text: `คุณต้องการลบข้อมูลของ ${name} อย่างถาวรใช่หรือไม่? การลบถาวรนี้ไม่สามารถกู้คืนได้อีก`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'ใช่, ลบถาวร',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `${site_url}/System_member/permanent_delete/${logId}`;
            }
        });
    }

    // ฟังก์ชันแสดงรายละเอียดข้อมูลที่ถูกลบ
    function showDetails(data, tableName, logId) {
        // กำหนดตัวแปรว่าเป็นข้อมูลสมาชิกภายในหรือภายนอก
        const isInternalMember = tableName === 'tbl_member';
        const isExternalMember = tableName === 'tbl_member_public';
        
        // สร้างลิงก์รูปโปรไฟล์
        const profileImg = isInternalMember && data.m_img 
            ? `${site_url}/docs/img/${data.m_img}` 
            : (isExternalMember && data.mp_img 
                ? `${site_url}/docs/img/${data.mp_img}` 
                : `${site_url}/docs/default_user.png`);
        
        // กำหนดชื่อผู้ใช้
        const name = isInternalMember 
            ? `${data.m_fname || ''} ${data.m_lname || ''}` 
            : (isExternalMember 
                ? `${data.mp_fname || ''} ${data.mp_lname || ''}` 
                : 'ไม่ระบุชื่อ');
        
        // แปลงชื่อประเภทผู้ใช้งาน
        const systemType = isInternalMember ? formatSystemType(data.m_system || '-') : '';
        
        // สร้าง HTML สำหรับป็อปอัพ
        let detailsHtml = `
        <div class="text-left">
            <div class="profile-container">
                <div class="profile-image-container">
                    <img src="${profileImg}" alt="Profile" class="profile-image" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #f0f0f0; margin: 0 auto 16px; display: block;">
                </div>
            </div>
            <div class="member-name" style="text-align: center; font-size: 1.5rem; font-weight: 600; margin-bottom: 16px;">${name}</div>
            
            <div class="info-container" style="max-height: 60vh; overflow-y: auto; padding-right: 16px;">
        `;
        
        if (isInternalMember) {
            // สมาชิกภายใน
            detailsHtml += `
                <div class="info-section" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="info-card" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start;">
                        <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="info-content" style="flex: 1;">
                            <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">ชื่อผู้ใช้</p>
                            <p class="info-value" style="font-size: 1rem; font-weight: 500; color: #1f2937;">@${data.m_username || '-'}</p>
                        </div>
                    </div>
                    
                    <div class="info-card" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start;">
                        <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content" style="flex: 1;">
                            <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">อีเมล</p>
                            <p class="info-value" style="font-size: 1rem; font-weight: 500; color: #1f2937;">${data.m_email || '-'}</p>
                        </div>
                    </div>
                </div>
                
                <div class="info-section" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="info-card" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start;">
                        <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="info-content" style="flex: 1;">
                            <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">เบอร์โทรศัพท์</p>
                            <p class="info-value" style="font-size: 1rem; font-weight: 500; color: #1f2937;">${data.m_phone || '-'}</p>
                        </div>
                    </div>
                    
                    <div class="info-card" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start;">
                        <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="info-content" style="flex: 1;">
                            <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">ประเภทผู้ใช้</p>
                            <p class="info-value" style="font-size: 1rem; font-weight: 500; color: #1f2937;">${systemType}</p>
                        </div>
                    </div>
                </div>
                
                <div class="info-card full-width" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start; margin-bottom: 16px;">
                    <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info-content" style="flex: 1;">
                        <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">วันที่สร้าง</p>
                        <p class="info-value" style="font-size: 1rem; font-weight: 500; color: #1f2937;">${formatDate(data.m_datesave)}</p>
                    </div>
                </div>
                
                <div class="info-section" style="display: grid; grid-template-columns: 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="info-card" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start;">
                        <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-toggle-on"></i>
                        </div>
                        <div class="info-content" style="flex: 1;">
                            <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">สถานะ</p>
                            <p class="info-value status-indicator" style="font-size: 1rem; font-weight: 500; display: inline-block; padding: 4px 12px; border-radius: 9999px; ${data.m_status == 1 ? 'background-color: #d1fae5; color: #065f46;' : 'background-color: #fee2e2; color: #b91c1c;'}">
                                ${data.m_status == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน'}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        } else if (isExternalMember) {
            // สมาชิกภายนอก
            detailsHtml += `
                <div class="info-section" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="info-card" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start;">
                        <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content" style="flex: 1;">
                            <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">อีเมล</p>
                            <p class="info-value" style="font-size: 1rem; font-weight: 500; color: #1f2937;">${data.mp_email || '-'}</p>
                        </div>
                    </div>
                    
                    <div class="info-card" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start;">
                        <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="info-content" style="flex: 1;">
                            <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">เบอร์โทรศัพท์</p>
                            <p class="info-value" style="font-size: 1rem; font-weight: 500; color: #1f2937;">${data.mp_phone || '-'}</p>
                        </div>
                    </div>
                </div>
                
                <div class="info-section" style="display: grid; grid-template-columns: 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="info-card" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start;">
                        <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="info-content" style="flex: 1;">
                            <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">เลขบัตรประชาชน</p>
                            <p class="info-value" style="font-size: 1rem; font-weight: 500; color: #1f2937;">${formatIdCard(data.mp_number)}</p>
                        </div>
                    </div>
                </div>
                
                <div class="info-card" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start; margin-bottom: 16px;">
                    <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content" style="flex: 1;">
                        <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">ที่อยู่</p>
                        <p class="info-value" style="font-size: 1rem; font-weight: 500; color: #1f2937;">${data.mp_address || '-'}</p>
                    </div>
                </div>
                
                <div class="info-section" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="info-card" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start;">
                        <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="info-content" style="flex: 1;">
                            <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">วันที่ลงทะเบียน</p>
                            <p class="info-value" style="font-size: 1rem; font-weight: 500; color: #1f2937;">${formatDate(data.mp_registered_date)}</p>
                        </div>
                    </div>
                    
                    <div class="info-card" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start;">
                        <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-toggle-on"></i>
                        </div>
                        <div class="info-content" style="flex: 1;">
                            <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">สถานะ</p>
                            <p class="info-value status-indicator" style="font-size: 1rem; font-weight: 500; display: inline-block; padding: 4px 12px; border-radius: 9999px; ${data.mp_status == 1 ? 'background-color: #d1fae5; color: #065f46;' : 'background-color: #fee2e2; color: #b91c1c;'}">
                                ${data.mp_status == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน'}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // กรณีเป็นข้อมูลอื่นๆ ให้แสดงทุก property
            detailsHtml += '<div class="grid grid-cols-1 gap-4">';
            for (const [key, value] of Object.entries(data)) {
                if (value !== null && value !== undefined) {
                    detailsHtml += `
                        <div class="info-card" style="background: #f8f9fa; border-radius: 8px; padding: 12px; display: flex; align-items: flex-start; margin-bottom: 8px;">
                            <div class="info-icon" style="background: #e1effe; color: #1e40af; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="info-content" style="flex: 1;">
                                <p class="info-label" style="font-size: 0.875rem; color: #6b7280; margin-bottom: 4px;">${formatKey(key)}</p>
                                <p class="info-value" style="font-size: 1rem; font-weight: 500; color: #1f2937;">${value}</p>
                            </div>
                        </div>
                    `;
                }
            }
            detailsHtml += '</div>';
        }
        
        // ส่วนท้าย
        detailsHtml += `
            </div>
            
            <div class="footer-actions" style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #e5e7eb; display: flex; justify-content: center; gap: 12px;">
                <button class="action-button" style="padding: 8px 16px; background-color: #10b981; color: white; border-radius: 8px; font-weight: 500; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; transition: all 0.2s;" 
                    onclick="confirmRestore(${logId}, '${name}')">
                    <i class="fas fa-undo mr-2"></i> กู้คืนข้อมูล
                </button>
                <button class="action-button" style="padding: 8px 16px; background-color: #EF4444; color: white; border-radius: 8px; font-weight: 500; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; transition: all 0.2s;" 
                    onclick="confirmPermanentDelete(${logId}, '${name}')">
                    <i class="fas fa-trash-alt mr-2"></i> ลบถาวร
                </button>
            </div>
        </div>
        `;
        
        // แสดง SweetAlert Modal
        Swal.fire({
            title: '<span style="font-size: 1.5rem; font-weight: 600;">รายละเอียดข้อมูลที่ถูกลบ</span>',
            html: detailsHtml,
            width: '700px',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                container: 'swal-wide',
                popup: 'rounded-xl',
                closeButton: 'swal-close-button'
            }
        });
    }
    
    // ฟังก์ชันช่วยสร้างแถวข้อมูล
    function createDetailRow(label, value) {
        return `
            <div class="mb-3">
                <p class="text-sm font-medium text-gray-500">${label}</p>
                <p class="mt-1 text-gray-800">${value}</p>
            </div>
        `;
    }
    
    // ฟังก์ชันจัดรูปแบบวันที่
    function formatDate(dateString) {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            return date.toLocaleString('th-TH', {
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
    
    // ฟังก์ชันแปลงชื่อ key ให้อ่านง่าย
    function formatKey(key) {
        return key
            .replace(/_/g, ' ')
            .replace(/\b\w/g, letter => letter.toUpperCase());
    }
    
    // ฟังก์ชันจัดรูปแบบเลขบัตรประชาชน
    function formatIdCard(idCard) {
        if (!idCard) return '-';
        if (idCard.length !== 13) return idCard;
        return `${idCard.substring(0, 1)}-${idCard.substring(1, 5)}-${idCard.substring(5, 10)}-${idCard.substring(10, 12)}-${idCard.substring(12)}`;
    }
    
    // ฟังก์ชันแปลงประเภทผู้ดูแลระบบ
    function formatSystemType(systemType) {
        const types = {
            'system_admin': 'ผู้ดูแลระบบสูงสุด',
            'super_admin': 'ผู้ดูแลระบบ',
            'user_admin': 'ผู้ดูแลเฉพาะส่วน',
            'end_user': 'ผู้ใช้งานทั่วไป'
        };
        return types[systemType] || systemType;
    }
</script>