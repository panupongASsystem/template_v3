<?php
/**
 * ฟังก์ชันตรวจสอบ path รูปภาพโปรไฟล์ตามลำดับ
 * 1. docs/ชื่อรูป
 * 2. docs/img/ชื่อรูป
 * 3. docs/img/avatar/ชื่อรูป
 * 4. ถ้าไม่เจอใช้ docs/default_user.png
 */
function get_member_profile_image($mp_img) {
    if (empty($mp_img)) {
        return base_url('docs/default_user.png');
    }
    
    // ตรวจสอบ path ตามลำดับความสำคัญ
    $paths = [
        'docs/' . $mp_img,
        'docs/img/' . $mp_img,
        'docs/img/avatar/' . $mp_img
    ];
    
    foreach ($paths as $path) {
        if (file_exists(FCPATH . $path)) {
            return base_url($path);
        }
    }
    
    // ถ้าไม่เจอในที่ใดเลย ใช้ default
    return base_url('docs/default_user.png');
}
?>

<div class="ml-72 p-8">
    <!-- Header -->
	<div class="flex justify-between items-center mb-8">
		<div>
			<h2 class="text-2xl font-semibold text-gray-800">จัดการสมาชิกภายนอก</h2>
			<p class="text-gray-600">จัดการข้อมูลสมาชิกภายนอกทั้งหมดในระบบ</p>
		</div>
		<div class="flex space-x-3">
			<!-- ปุ่มเดิมที่ถูก comment ไว้
			<button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
				<a href="<?php echo site_url('System_member/add_member_external'); ?>">
					<i class="fas fa-plus mr-2"></i>เพิ่มสมาชิกภายนอกใหม่
				</a>
			</button>
			-->
		</div>
	</div>


    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- สมาชิกภายนอกทั้งหมด -->
        <?php if (isset($total_members) && $total_members > 0) : ?>
            <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl">
                        <i class="fas fa-users text-2xl text-blue-800"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-gray-500 font-medium">สมาชิกภายนอกทั้งหมด</h4>
                        <p class="text-2xl font-semibold text-gray-800">
                            <?php echo number_format($total_members); ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>


	<!-- Table -->
	<div class="bg-white rounded-lg shadow">
		<div class="p-6 border-b">
			<div class="flex justify-between items-center">
				<h3 class="text-lg font-semibold text-gray-800">รายชื่อสมาชิกภายนอกทั้งหมด</h3>
				<form method="GET" action="<?php echo site_url('System_member/member_web_external'); ?>" class="flex space-x-4">

					<!-- ปุ่ม Export CSV - แสดงเฉพาะ system_admin -->
					<?php if ($user_system == 'system_admin'): ?>
						<button onclick="exportMemberData()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
							<i class="fas fa-download mr-2"></i>Export CSV
						</button>
					<?php endif; ?>

					<div class="relative">
						<input type="text" 
							   name="search"
							   value="<?php echo $this->input->get('search'); ?>"
							   placeholder="ค้นหาสมาชิก..."
							   class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
						<button type="submit" class="absolute left-1 text-gray-400" style="margin-top: 10px; margin-left: 5px;">
							<i class="fas fa-search"></i>
						</button>
					</div>
				</form>
			</div>
		</div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3 text-gray-600 cursor-pointer hover:bg-gray-100" onclick="sortTable('name')">
                            <div class="flex items-center">
                                ชื่อ-นามสกุล
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-gray-600">อีเมล</th>
                        <th class="px-6 py-3 text-gray-600">เบอร์โทรศัพท์</th>
                        <th class="px-6 py-3 text-gray-600">เลขประจำตัวประชาชน</th>
                        <th class="px-6 py-3 text-gray-600">จัดการ</th>
                        <th class="px-6 py-3 text-gray-600">สถานะ</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if(isset($members) && count($members) > 0): ?>
                        <?php foreach ($members as $member): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="<?php echo get_member_profile_image($member->mp_img); ?>" 
                                             class="w-10 h-10 rounded-full object-cover">
                                        <div class="ml-4">
                                            <div class="text-gray-800">
                                                <?php echo $member->mp_fname; ?> <?php echo $member->mp_lname; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600"><?php echo $member->mp_email; ?></td>
								<!-- เบอร์โทรศัพท์ (ซ่อนเลข 4 หลักกลาง) -->
								<td class="px-6 py-4 text-gray-600">
									<?php 
									$phone = $member->mp_phone;
									if(!empty($phone) && strlen($phone) >= 10) {
										// เอาตัวอักษรที่ไม่ใช่ตัวเลขออก
										$clean_phone = preg_replace('/\D/', '', $phone);

										if(strlen($clean_phone) == 10) {
											// แสดงเลข 3 หลักแรก และ 3 หลักสุดท้าย เช่น 081-****-678
											echo substr($clean_phone, 0, 3) . '-****-' . substr($clean_phone, 7, 3);
										} elseif(strlen($clean_phone) > 10) {
											// กรณีเบอร์มากกว่า 10 หลัก
											echo substr($clean_phone, 0, 3) . '-****-' . substr($clean_phone, -3);
										} else {
											echo $phone;
										}
									} else {
										echo $phone ?: '-';
									}
									?>
								</td>

								<!-- เลขบัตรประจำตัวประชาชน (ซ่อนเลข 6 หลักกลาง) -->
								<td class="px-6 py-4 text-gray-600">
									<?php 
									$id_card = $member->mp_number;
									if(!empty($id_card) && strlen($id_card) == 13) {
										// แสดงเลข 1 หลักแรก และ 3 หลักสุดท้าย เช่น 1-****-****-12-3
										echo substr($id_card, 0, 1) . '-****-****-' . 
											 substr($id_card, 10, 2) . '-' . 
											 substr($id_card, 12, 1);
									} else {
										echo $id_card ?: '-';
									}
									?>
								</td>
								
								
								
                                <td class="px-6 py-4">
    <div class="flex space-x-3">
        <a href="javascript:void(0);" 
           onclick="viewMemberDetails(<?php echo htmlspecialchars(json_encode($member)); ?>)"
           class="w-8 h-8 flex items-center justify-center rounded bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-800"> 
            <i class="fas fa-eye"></i> 
        </a>
        <a href="<?php echo site_url('System_member/edit_member_external/' . $member->mp_id); ?>" 
           class="w-8 h-8 flex items-center justify-center rounded bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-800"> 
            <i class="fas fa-edit"></i> 
        </a>
        <a href="javascript:void(0);" 
           onclick="confirmDeleteExternal(<?php echo $member->mp_id; ?>, '<?php echo $member->mp_fname; ?> <?php echo $member->mp_lname; ?>');" 
           class="w-8 h-8 flex items-center justify-center rounded bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800"> 
            <i class="fas fa-trash"></i> 
        </a>
    </div>
</td>
								
								
								
                                <td class="px-6 py-4">
                                    <div class="toggle-switch-container">
                                        <label class="toggle-switch">
                                            <input type="checkbox" 
                                                   class="toggle-switch-checkbox user-status-toggle" 
                                                   data-member-id="<?php echo $member->mp_id; ?>"
                                                   <?php echo ($member->mp_status == 1) ? 'checked' : ''; ?>>
                                            <span class="toggle-switch-slider"></span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูลสมาชิกภายนอก</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

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

<script>
// กำหนดตัวแปร site_url
const site_url = '<?php echo site_url(); ?>';

// ฟังก์ชันสำหรับตรวจสอบรูปภาพที่ใช้งานได้
function checkImageExists(imageUrl) {
    return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => resolve(imageUrl);
        img.onerror = () => resolve(null);
        img.src = imageUrl;
    });
}

// ฟังก์ชันหา path รูปภาพที่ถูกต้อง
async function getMemberProfileImage(mp_img) {
    if (!mp_img) {
        return `${site_url}docs/default_user.png`;
    }
    
    // ลำดับ path ที่ต้องตรวจสอบ
    const paths = [
        `${site_url}docs/${mp_img}`,
        `${site_url}docs/img/${mp_img}`,
        `${site_url}docs/img/avatar/${mp_img}`
    ];
    
    // ตรวจสอบทีละ path
    for (const path of paths) {
        const result = await checkImageExists(path);
        if (result) {
            return result;
        }
    }
    
    // ถ้าไม่เจอเลย ใช้ default
    return `${site_url}docs/default_user.png`;
}

// ฟังก์ชันฟอร์แมตวันเกิด
function formatBirthdate(birthdate) {
    if (!birthdate) return '';
    
    const date = new Date(birthdate);
    const thaiMonths = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    
    const day = date.getDate();
    const month = thaiMonths[date.getMonth()];
    const year = date.getFullYear() + 543; // แปลงเป็น พ.ศ.
    
    return `${day} ${month} ${year}`;
}

// ฟังก์ชันสำหรับแสดงข้อมูลสมาชิกทั้งหมด
window.viewMemberDetails = async function(member) {
    // ฟอร์แมตวันที่ลงทะเบียน
    let registeredDate = '';
    if (member.mp_registered_date) {
        const date = new Date(member.mp_registered_date);
        registeredDate = date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // ฟอร์แมตวันที่อัปเดต
    let updatedDate = '';
    if (member.mp_updated_date) {
        const date = new Date(member.mp_updated_date);
        updatedDate = date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // ฟอร์แมตวันเกิด
    const birthdateFormatted = formatBirthdate(member.mp_birthdate);
    
    // ฟอร์แมตเบอร์โทรศัพท์ (ซ่อนเลข 4 หลักกลาง)
    let formattedPhone = member.mp_phone;
    if (member.mp_phone && member.mp_phone.length >= 10) {
        // แสดงเลข 3 หลักแรก และ 3 หลักสุดท้าย เช่น 081-****-123
        const phone = member.mp_phone.replace(/\D/g, ''); // เอาตัวอักษรที่ไม่ใช่ตัวเลขออก
        if (phone.length === 10) {
            formattedPhone = `${phone.substring(0, 3)}-****-${phone.substring(7, 10)}`;
        } else if (phone.length > 10) {
            formattedPhone = `${phone.substring(0, 3)}-****-${phone.substring(phone.length-3)}`;
        }
    }
    
    // ฟอร์แมตเลขบัตรประชาชน (ซ่อนเลข 6 หลักกลาง)
    let formattedIDCard = member.mp_number;
    if (member.mp_number && member.mp_number.length === 13) {
        // แสดงเลข 1 หลักแรก และ 6 หลักสุดท้าย เช่น 1-****-****-**-4
        formattedIDCard = `${member.mp_number.substring(0, 1)}-****-****-${member.mp_number.substring(10, 12)}-${member.mp_number.substring(12, 13)}`;
    }
    
    // ตรวจสอบและดึง URL รูปภาพที่ถูกต้อง
    const profileImg = await getMemberProfileImage(member.mp_img);
    
    Swal.fire({
        title: '<span class="text-gradient">ข้อมูลสมาชิกภายนอก</span>',
        html: `
            <div class="text-left">
                <div class="profile-container">
                    <div class="profile-image-container">
                        <img src="${profileImg}" alt="Profile" class="profile-image">
                    </div>
                </div>
                <div class="member-name">${member.mp_fname} ${member.mp_lname}</div>
                ${birthdateFormatted ? `<div class="member-birthdate"><i class="fas fa-birthday-cake mr-2"></i>${birthdateFormatted}</div>` : ''}
                
                <div class="info-container">
                    <div class="info-section">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <p class="info-label">อีเมล</p>
                                <p class="info-value">${member.mp_email}</p>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="info-content">
                                <p class="info-label">เบอร์โทรศัพท์</p>
                                <p class="info-value">${formattedPhone}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-section">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="info-content">
                                <p class="info-label">เลขประจำตัวประชาชน</p>
                                <p class="info-value">${formattedIDCard}</p>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-toggle-on"></i>
                            </div>
                            <div class="info-content">
                                <p class="info-label">สถานะ</p>
                                <p class="info-value status-indicator ${member.mp_status == 1 ? 'status-active' : 'status-inactive'}">
                                    ${member.mp_status == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน'}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-card full-width">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <p class="info-label">ที่อยู่</p>
                            <p class="info-value">${member.mp_address || '-'}</p>
                        </div>
                    </div>
                    
                    <div class="info-section">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="info-content">
                                <p class="info-label">วันที่ลงทะเบียน</p>
                                <p class="info-value">${registeredDate || '-'}</p>
                            </div>
                        </div>
                        
                        ${member.mp_updated_date ? `
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <div class="info-content">
                                <p class="info-label">อัปเดตล่าสุดโดย</p>
                                <p class="info-value">${member.mp_updated_by || '-'}</p>
                                <p class="info-subvalue">${updatedDate}</p>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
                
                <div class="footer-actions">
                    <button class="action-button edit-button" onclick="window.location.href='${site_url}/System_member/edit_member_external/${member.mp_id}'">
                        <i class="fas fa-edit mr-2"></i> แก้ไขข้อมูล
                    </button>
                </div>
            </div>
        `,
        width: '650px',
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
            popup: 'apple-style-popup',
            closeButton: 'apple-style-close'
        }
    });
};

// ฟังก์ชันสำหรับลบข้อมูล (ใช้ Fetch API)
window.confirmDeleteExternal = function(mp_id, memberName) {
    console.log('Confirm delete called for member:', mp_id, memberName);
    
    Swal.fire({
        title: 'ยืนยันการลบข้อมูล',
        html: `
            <div class="text-left">
                <p class="mb-2">ต้องการลบข้อมูลของ <span class="font-medium">${memberName}</span> หรือไม่?</p>
                <div class="mt-4 p-3 bg-yellow-50 rounded-lg">
                    <p class="text-yellow-800 text-sm">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        การลบข้อมูลจะไม่สามารถกู้คืนได้
                    </p>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ยืนยันการลบ',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            container: 'delete-modal',
            popup: 'rounded-lg shadow-lg',
            content: 'text-left'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            proceedWithDeletionExternal(mp_id);
        }
    });
};

// ฟังก์ชันดำเนินการลบ
function proceedWithDeletionExternal(mp_id) {    
    Swal.fire({
        title: 'กำลังดำเนินการ...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // ส่งคำขอลบ
    fetch(`${site_url}/System_member/del_member_external/${mp_id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(response => {
        if (response.status) {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: 'ลบข้อมูลเรียบร้อยแล้ว',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: response.message || 'ไม่สามารถลบข้อมูลได้'
            });
        }
    })
    .catch(error => {
        console.error('Error deleting member:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
        });
    });
}

// ฟังก์ชันสำหรับการเรียงลำดับตาราง
function sortTable(column) {
    // รับค่าพารามิเตอร์ปัจจุบันจาก URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentSortBy = urlParams.get('sort_by') || 'mp_id';
    const currentSortOrder = urlParams.get('sort_order') || 'desc';
    
    // กำหนดทิศทางการเรียงลำดับใหม่
    let newSortOrder = 'asc';
    if (column === currentSortBy && currentSortOrder === 'asc') {
        newSortOrder = 'desc';
    }
    
    // ตั้งค่าพารามิเตอร์ใหม่
    urlParams.set('sort_by', column);
    urlParams.set('sort_order', newSortOrder);
    
    // นำทางไปยัง URL ใหม่
    window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
}

// เพิ่ม JavaScript สำหรับ Toggle สถานะสมาชิก
(function() {
    // รอให้ DOM โหลดเสร็จก่อนเริ่มทำงาน
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing toggles...');
        
        // เพิ่ม Event Listener ให้กับทุก toggle
        document.querySelectorAll('.user-status-toggle').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                const memberId = this.getAttribute('data-member-id');
                const status = this.checked ? 1 : 0;
                console.log(`Toggle clicked: member ${memberId}, status ${status}`);
                toggleMemberStatusExternal(memberId, status);
            });
        });
    });
    
    // ฟังก์ชันสำหรับอัพเดทสถานะผู้ใช้
    window.toggleMemberStatusExternal = function(memberId, status) {
        console.log('Toggle status function called:', memberId, status);
        
        // แสดง loading
        Swal.fire({
            title: 'กำลังดำเนินการ...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // สร้าง FormData object
        const formData = new FormData();
        formData.append('member_id', memberId);
        formData.append('status', status);
        
        // ส่งคำขอด้วย Fetch API
        fetch(`${site_url}/System_member/toggle_member_status_external`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response received:', response);
            return response.json();
        })
        .then(data => {
            console.log('Data:', data);
            if (data.success) {
                const statusText = status == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: `${statusText}ผู้ใช้เรียบร้อยแล้ว`,
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                throw new Error(data.message || 'ไม่สามารถอัพเดทสถานะได้');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: error.message || 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
            });
            
            // คืนค่า toggle เป็นสถานะเดิม (กรณีเกิดข้อผิดพลาด)
            const checkbox = document.querySelector(`.user-status-toggle[data-member-id="${memberId}"]`);
            if (checkbox) {
                checkbox.checked = !status;
            }
        });
    };
})();
</script>

<script>
function exportMemberData() {
    // แสดง loading
    Swal.fire({
        title: 'กำลังเตรียมข้อมูล...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // ส่งคำขอ export
    const currentUrl = new URL(window.location);
    const search = currentUrl.searchParams.get('search') || '';
    
    const exportUrl = `<?php echo site_url('System_member/export_member_csv'); ?>?search=${encodeURIComponent(search)}`;
    
    // สร้าง link download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `member_public_${new Date().toISOString().slice(0,10)}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // ปิด loading หลังจาก 1 วินาที
    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: 'success',
            title: 'ส่งออกข้อมูลสำเร็จ',
            text: 'ไฟล์ CSV ถูกดาวน์โหลดแล้ว',
            timer: 2000,
            showConfirmButton: false
        });
    }, 1000);
}
</script>

<style>
/* เพิ่ม CSS สำหรับวันเกิด */
.member-birthdate {
    text-align: center;
    color: #6b7280;
    font-size: 0.95rem;
    margin-top: 0.5rem;
    margin-bottom: 1.5rem;
}

.member-birthdate i {
    color: #f59e0b;
}
</style>