<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">จัดการสมาชิกภายใน</h2>
            <p class="text-gray-600">จัดการข้อมูลสมาชิกทั้งหมดในระบบ</p>
        </div>
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <a href="<?php echo site_url('System_member/add_member'); ?>">
                <i class="fas fa-plus mr-2"></i>เพิ่มสมาชิกใหม่ภายใน
            </a>
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- สมาชิกทั้งหมด -->
        <?php if (isset($total_members) && $total_members > 0): ?>
            <div
                class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl">
                        <i class="fas fa-users text-2xl text-blue-800"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-gray-500 font-medium">สมาชิกภายในทั้งหมด</h4>
                        <p class="text-2xl font-semibold text-gray-800">
                            <?php echo number_format($total_members); ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- แสดงจำนวนตามตำแหน่ง -->
        <?php if (isset($positions)): ?>
            <?php
            // สำหรับ System Types (m_system)
            $system_types = [
                'system_admin' => [
                    'name' => 'ผู้ดูแลระบบสูงสุด',
                    'gradient_class' => 'from-red-100 to-red-50',
                    'icon_color' => 'text-red-800',
                    'count' => 0
                ],
                'super_admin' => [
                    'name' => 'ผู้ดูแลระบบ',
                    'gradient_class' => 'from-blue-100 to-blue-50',
                    'icon_color' => 'text-blue-800',
                    'count' => 0
                ],
                'user_admin' => [
                    'name' => 'ผู้ดูแลเฉพาะส่วน',
                    'gradient_class' => 'from-green-100 to-green-50',
                    'icon_color' => 'text-green-800',
                    'count' => 0
                ]
            ];

            // นับจำนวนตาม m_system
            if (isset($members)) {
                foreach ($members as $member) {
                    if (array_key_exists($member->m_system, $system_types)) {
                        $system_types[$member->m_system]['count']++;
                    }
                }
            }

            // แสดงการ์ดสำหรับ system types
            foreach ($system_types as $type => $data) {
                if ($data['count'] > 0 && ($user_system == 'system_admin' || ($user_system == 'super_admin' && $type != 'system_admin'))) {
                    ?>
                    <div
                        class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br <?php echo $data['gradient_class']; ?> rounded-xl">
                                <i class="fas fa-user-shield text-2xl <?php echo $data['icon_color']; ?>"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-gray-500 font-medium"><?php echo $data['name']; ?></h4>
                                <p class="text-2xl font-semibold text-gray-800">
                                    <?php echo number_format($data['count']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }

            // แสดงการ์ดสำหรับตำแหน่งอื่นๆ
            foreach ($positions as $position):
                if ($position->pid != 1 && $position->pid != 2):   // ไม่แสดง pid 1 และ 2
                    $count = isset($member_counts[$position->pid]) ? $member_counts[$position->pid] : 0;
                    if ($count > 0):
                        ?>
                        <div
                            class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="flex items-center">
                                <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-50 rounded-xl">
                                    <i class="fas fa-user-tie text-2xl text-purple-800"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-gray-500 font-medium"><?php echo $position->pname; ?></h4>
                                    <p class="text-2xl font-semibold text-gray-800">
                                        <?php echo number_format($count); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php
                    endif;
                endif;
            endforeach;
        endif;
        ?>
    </div>



    <!-- Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">รายชื่อสมาชิกทั้งหมด</h3>


                <form method="GET" action="<?php echo site_url('System_member/member_web'); ?>" class="flex space-x-4">
                    <div class="relative">
                        <input type="text" name="search" value="<?php echo $this->input->get('search'); ?>"
                            placeholder="ค้นหาสมาชิก..."
                            class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <button type="submit" class="absolute left-1 text-gray-400"
                            style="margin-top: 10px; margin-left: 5px;">
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
                        <th class="px-6 py-3 text-gray-600 cursor-pointer hover:bg-gray-100"
                            onclick="sortTable('name')">
                            <div class="flex items-center">
                                ชื่อ-นามสกุล
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-gray-600 cursor-pointer hover:bg-gray-100"
                            onclick="sortTable('position')">
                            <div class="flex items-center">
                                ตำแหน่ง
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-gray-600 cursor-pointer hover:bg-gray-100"
                            onclick="sortTable('type')">
                            <div class="flex items-center">
                                ประเภทผู้ใช้
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-gray-600 cursor-pointer hover:bg-gray-100"
                            onclick="sortTable('system')">
                            <div class="flex items-center">
                                ระบบที่เปิด
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-gray-600 cursor-pointer hover:bg-gray-100"
                            onclick="sortTable('date')">
                            <div class="flex items-center">
                                วันที่สร้าง
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-gray-600">จัดการ</th>
                        <th class="px-6 py-3 text-gray-600">สถานะ</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($members as $member): ?>
                        <?php 
                        // ======= เพิ่มส่วนนี้: เตรียม img_path สำหรับส่งไป JavaScript =======
                        $img_path = 'default_user.png';
                        if (!empty($member->m_img)) {
                            $path_img = FCPATH . 'docs/img/' . $member->m_img;
                            $path_avatar = FCPATH . 'docs/img/avatar/' . $member->m_img;
                            
                            if (file_exists($path_img)) {
                                $img_path = 'img/' . $member->m_img;
                            } elseif (file_exists($path_avatar)) {
                                $img_path = 'img/avatar/' . $member->m_img;
                            }
                        }
                        $member->img_path = $img_path; // เพิ่มข้อมูล path ที่ถูกต้องเข้าไปใน object
                        // ======= จบส่วนที่เพิ่ม =======
                        ?>
                        
                        <?php if ($user_system == 'super_admin' && $member->m_system == 'system_admin')
                            continue; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <?php
                                    $img = 'default_user.png'; // ค่าตั้งต้น
                                
                                    if (!empty($member->m_img)) {

                                        // path ที่จะตรวจสอบไฟล์จริงใน server
                                        $path_img = FCPATH . 'docs/img/' . $member->m_img;
                                        $path_avatar = FCPATH . 'docs/img/avatar/' . $member->m_img;

                                        if (file_exists($path_img)) {
                                            $img = 'img/' . $member->m_img;
                                        } elseif (file_exists($path_avatar)) {
                                            $img = 'img/avatar/' . $member->m_img;
                                        }
                                    }
                                    ?>
                                    <img src="<?php echo base_url('docs/' . $img); ?>" class="w-10 h-10 rounded-full"
                                        alt="user avatar" />

                                    <div class="ml-4">
                                        <div class="text-gray-800">
                                            <?php echo $member->m_fname; ?>&nbsp;<?php echo $member->m_lname; ?>
                                        </div>
                                        <div class="text-gray-600 text-sm">
                                            @<?php echo $member->m_username; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>



                            <!-- <td class="px-6 py-4 text-gray-600"><?php echo $member->m_email; ?></td>  -->




                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs">
                                <?php echo $member->pname; ?>
                            </td>


                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 <?php echo get_system_badge_color($member->m_system); ?> rounded-full text-sm inline-block">
                                    <?php echo get_system_display_name($member->m_system); ?>
                                </span>
                            </td>



                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <?php if (!empty($member->system_names)):
                                        $system_names = explode(',', $member->system_names);
                                        foreach ($system_names as $system):
                                            if (!empty($system)):
                                                ?>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo get_system_badge_color($system); ?>">
                                                    <?php echo $system; ?>
                                                </span>
                                            <?php
                                            endif;
                                        endforeach;
                                    else:
                                        ?>
                                        <span class="text-gray-400 text-sm">ไม่มีระบบที่เข้าถึง</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-gray-600">
                                        <?php echo date('d M Y, H:i:s', strtotime($member->m_datesave)); ?>
                                    </span>
                                </div>
                            </td>





                            <td class="px-6 py-4">
                                <div class="flex space-x-3">
                                    <a href="javascript:void(0);"
                                        onclick="viewMemberDetails(<?php echo htmlspecialchars(json_encode($member)); ?>)"
                                        class="w-8 h-8 flex items-center justify-center rounded bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-800">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo site_url('System_member/edit_member/' . $member->m_id); ?>"
                                        class="w-8 h-8 flex items-center justify-center rounded bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0);"
                                        onclick="confirmDelete(<?php echo $member->m_id; ?>, '<?php echo $member->m_fname; ?> <?php echo $member->m_lname; ?>');"
                                        class="w-8 h-8 flex items-center justify-center rounded bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>





                            <td class="px-6 py-4">
                                <div class="toggle-switch-container">
                                    <label class="toggle-switch">
                                        <input type="checkbox" class="toggle-switch-checkbox user-status-toggle"
                                            data-member-id="<?php echo $member->m_id; ?>" <?php echo ($member->m_status == 1) ? 'checked' : ''; ?>>
                                        <span class="toggle-switch-slider"></span>
                                    </label>

                                </div>
                            </td>



                        </tr>
                    <?php endforeach; ?>
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

<?php
function get_system_badge_color($system_name)
{
    $colors = [
        'bg-blue-100 text-blue-800',
        'bg-green-100 text-green-800',
        'bg-yellow-100 text-yellow-800',
        'bg-purple-100 text-purple-800',
        'bg-pink-100 text-pink-800',
        'bg-indigo-100 text-indigo-800',
        'bg-red-100 text-red-800',
        'bg-orange-100 text-orange-800',
        'bg-amber-100 text-amber-800',
        'bg-lime-100 text-lime-800',
        'bg-emerald-100 text-emerald-800'
    ];

    static $module_colors = [];

    // ถ้ายังไม่มีการกำหนดสีให้กับโมดูล
    if (empty($module_colors)) {
        $CI =& get_instance();
        $modules = $CI->db->get('tbl_member_modules')->result();

        // สุ่มสลับลำดับสี
        $shuffled_colors = $colors;
        shuffle($shuffled_colors);

        // กำหนดสีให้กับแต่ละโมดูล
        foreach ($modules as $index => $module) {
            $color_index = $index % count($shuffled_colors);
            $module_colors[$module->name] = $shuffled_colors[$color_index];
        }
    }

    // ส่งคืนสีที่กำหนดให้กับโมดูล หรือสีเริ่มต้นถ้าไม่พบโมดูล
    return $module_colors[$system_name] ?? $colors[0];
}

function get_system_display_name($system)
{
    switch ($system) {
        case 'system_admin':
            return 'ผู้ดูแลระบบสูงสุด';
        case 'super_admin':
            return 'ผู้ดูแลระบบ';
        case 'user_admin':
            return 'ผู้ดูแลเฉพาะส่วน';
        case 'end_user':
            return 'ผู้ใช้งานทั่วไป';
        default:
            return $system;
    }
}
?>

<script>
    // กำหนดตัวแปร site_url
    const site_url = '<?php echo site_url(); ?>';

    // ฟังก์ชันสำหรับแสดงข้อมูลสมาชิกทั้งหมด
    window.viewMemberDetails = function (member) {
        // ฟอร์แมตวันที่ลงทะเบียน
        let registeredDate = '';
        if (member.m_datesave) {
            const date = new Date(member.m_datesave);
            registeredDate = date.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // ======= ปรับปรุงส่วนนี้: จัดการรูปโปรไฟล์ให้ตรวจสอบ path ที่ถูกต้อง =======
        let profileImg = `${site_url}/docs/default_user.png`; // ค่าเริ่มต้น
        
        if (member.m_img) {
            // ใช้ img_path ที่ส่งมาจาก PHP (ตรวจสอบไฟล์จริงแล้ว)
            if (member.img_path) {
                profileImg = `${site_url}/docs/${member.img_path}`;
            } else {
                // fallback กรณีไม่มี img_path
                profileImg = `${site_url}/docs/img/${member.m_img}`;
            }
        }
        // ======= จบส่วนที่ปรับปรุง =======

        // แปลงข้อมูลระบบที่เข้าถึง
        let systemAccess = [];
        if (member.system_names) {
            systemAccess = member.system_names.split(',').filter(item => item !== '');
        }

        Swal.fire({
            title: '<span class="text-gradient">ข้อมูลสมาชิกภายใน</span>',
            html: `
            <div class="text-left">
                <div class="profile-container">
                    <div class="profile-image-container">
                        <img src="${profileImg}" 
                             alt="Profile" 
                             class="profile-image"
                             onerror="this.src='${site_url}/docs/default_user.png'">
                    </div>
                </div>
                <div class="member-name">${member.m_fname} ${member.m_lname}</div>
                
                <div class="info-container">
                    <div class="info-section">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="info-content">
                                <p class="info-label">ชื่อผู้ใช้</p>
                                <p class="info-value">@${member.m_username}</p>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <p class="info-label">อีเมล</p>
                                <p class="info-value">${member.m_email || '-'}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-section">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="info-content">
                                <p class="info-label">เบอร์โทรศัพท์</p>
                                <p class="info-value">${member.m_phone || '-'}</p>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="info-content">
                                <p class="info-label">ประเภทผู้ใช้</p>
                                <p class="info-value status-indicator">
                                    ${getSystemDisplayName(member.m_system)}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-card full-width">
                        <div class="info-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="info-content">
                            <p class="info-label">ตำแหน่ง</p>
                            <p class="info-value">${member.pname || '-'}</p>
                        </div>
                    </div>
                    
                    <div class="info-card full-width">
                        <div class="info-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="info-content">
                            <p class="info-label">ระบบที่เข้าถึง</p>
                            <div class="info-value system-tags">
                                ${systemAccess.length > 0 ?
                    systemAccess.map(system =>
                        `<span class="system-tag">${system}</span>`
                    ).join('') :
                    '<span class="text-gray-400">ไม่มีระบบที่เข้าถึง</span>'
                }
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-section">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="info-content">
                                <p class="info-label">วันที่สร้าง</p>
                                <p class="info-value">${registeredDate || '-'}</p>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-toggle-on"></i>
                            </div>
                            <div class="info-content">
                                <p class="info-label">สถานะ</p>
                                <p class="info-value status-indicator ${member.m_status == 1 ? 'status-active' : 'status-inactive'}">
                                    ${member.m_status == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน'}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="footer-actions">
                    <button class="action-button edit-button" onclick="window.location.href='${site_url}/System_member/edit_member/${member.m_id}'">
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

    // ฟังก์ชันสำหรับแปลงชื่อประเภทผู้ใช้
    function getSystemDisplayName(system) {
        switch (system) {
            case 'system_admin':
                return 'ผู้ดูแลระบบสูงสุด';
            case 'super_admin':
                return 'ผู้ดูแลระบบ';
            case 'user_admin':
                return 'ผู้ดูแลเฉพาะส่วน';
            case 'end_user':
                return 'ผู้ใช้งานทั่วไป';
            default:
                return system;
        }
    }

    // คำสั่ง JavaScript สำหรับควบคุม toggle status และฟังก์ชันอื่นๆ
    (function () {
        // รอให้ DOM โหลดเสร็จก่อนเริ่มทำงาน
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM loaded, initializing toggles...');

            // เพิ่ม Event Listener ให้กับทุก toggle
            document.querySelectorAll('.user-status-toggle').forEach(function (toggle) {
                toggle.addEventListener('change', function () {
                    const memberId = this.getAttribute('data-member-id');
                    const status = this.checked ? 1 : 0;
                    console.log(`Toggle clicked: member ${memberId}, status ${status}`);
                    toggleMemberStatus(memberId, status);
                });
            });
        });

        // ฟังก์ชันสำหรับอัพเดทสถานะผู้ใช้
        window.toggleMemberStatus = function (memberId, status) {
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
            fetch(`${site_url}/System_member/toggle_member_status`, {
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

        // ฟังก์ชันสำหรับลบข้อมูล (ใช้ Fetch API)
        window.confirmDelete = function (m_id, memberName) {
            console.log('Confirm delete called for member:', m_id, memberName);

            fetch(`${site_url}/System_member/get_member_systems/${m_id}`)
                .then(response => response.json())
                .then(response => {
                    if (response.success) {
                        let systemsHtml = '';
                        if (response.data && response.data.length > 0) {
                            systemsHtml = '<div class="mt-4"><p class="font-medium text-red-600 mb-2">ระบบที่จะถูกลบ:</p><ul class="space-y-2">';
                            response.data.forEach(system => {
                                systemsHtml += `
                            <li class="flex items-center justify-between bg-gray-50 p-2 rounded">
                                <div>
                                    <span class="font-medium">${system.module_name}</span>
                                    <br>
                                    <span class="text-sm text-gray-600">สิทธิ์ที่ได้รับ: ${system.permission_count} รายการ</span>
                                </div>
                            </li>
                        `;
                            });
                            systemsHtml += '</ul></div>';
                        }

                        Swal.fire({
                            title: 'ยืนยันการลบข้อมูล',
                            html: `
                        <div class="text-left">
                            <p class="mb-2">ต้องการลบข้อมูลของ <span class="font-medium">${memberName}</span> หรือไม่?</p>
                            ${systemsHtml}
                            <div class="mt-4 p-3 bg-yellow-50 rounded-lg">
                                <p class="text-yellow-800 text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                    การลบข้อมูลจะลบสิทธิ์การใช้งานระบบทั้งหมดของสมาชิกนี้
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
                                proceedWithDeletion(m_id);
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching systems:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถโหลดข้อมูลระบบได้'
                    });
                });
        };

        // ฟังก์ชันดำเนินการลบ
        function proceedWithDeletion(m_id) {
            Swal.fire({
                title: 'กำลังดำเนินการ...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`${site_url}/System_member/del_member/${m_id}`, {
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

        // เพิ่มฟังก์ชัน proceedWithDeletion เข้าไปใน window object ด้วย
        window.proceedWithDeletion = proceedWithDeletion;
    })();

    // ฟังก์ชันสำหรับการเรียงลำดับตาราง
    function sortTable(column) {
        // รับค่าพารามิเตอร์ปัจจุบันจาก URL
        const urlParams = new URLSearchParams(window.location.search);
        const currentSortBy = urlParams.get('sort_by') || 'm_id';
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
</script>