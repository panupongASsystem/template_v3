<?php
// กำหนดให้มีเพียงหัวข้อเดียวในส่วนบน
?>
<div class="ml-72 p-8">
    <!-- Header Section -->
    <div class="bg-white p-6 rounded-xl shadow-md mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">รายงานทั้งหมด</h2>
                <p class="text-gray-600">สรุปรายงานข้อมูลทั้งหมดในระบบ</p>
            </div>
             <!-- <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">
                <a href="<?php echo site_url('System_member/add_member'); ?>">
                    <i class="fas fa-plus mr-2"></i>เพิ่มสมาชิกใหม่
                </a>
            </button>  -->
        </div>
    </div>

    <!-- รายงานสรุปทั้งหมด Section -->
    <div class="bg-white p-6 rounded-xl shadow-md mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">รายงานสรุปทั้งหมด</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
            <!-- สมาชิกภายใน -->
            <?php if (isset($total_members) && $total_members > 0) : ?>
                <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center">
                        <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl">
                            <i class="fas fa-users text-2xl text-blue-800"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-gray-500 font-medium">สมาชิกภายใน</h4>
                            <p class="text-2xl font-semibold text-gray-800">
                                <?php echo number_format($total_members); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- สมาชิกภายนอก -->
            <?php if (isset($external_members_count)) : ?>
                <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center">
                        <div class="p-3 bg-gradient-to-br from-green-100 to-green-50 rounded-xl">
                            <i class="fas fa-user-friends text-2xl text-green-800"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-gray-500 font-medium">สมาชิกภายนอก</h4>
                            <p class="text-2xl font-semibold text-gray-800">
                                <?php echo number_format($external_members_count); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- โมดูลระบบ -->
            <?php if (isset($full_modules) && isset($trial_modules)) : ?>
                <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center">
                        <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-50 rounded-xl">
                            <i class="fas fa-cogs text-2xl text-purple-800"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-gray-500 font-medium">โมดูลระบบ</h4>
                            <div>
                                <p class="text-lg font-semibold text-gray-800">Full: <?php echo number_format($full_modules); ?></p>
                                <p class="text-sm text-gray-600">Trial: <?php echo number_format($trial_modules); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ผู้ใช้งานระบบ -->
            <?php if (isset($active_users)) : ?>
                <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center">
                        <div class="p-3 bg-gradient-to-br from-yellow-100 to-yellow-50 rounded-xl">
                            <i class="fas fa-user-check text-2xl text-yellow-800"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-gray-500 font-medium">ผู้ใช้งานระบบ</h4>
                            <p class="text-2xl font-semibold text-gray-800">
                                <?php echo number_format($active_users); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ข้อมูลตามประเภทผู้ใช้ Section -->
    <div class="bg-white p-6 rounded-xl shadow-md mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">ข้อมูลตามประเภทผู้ใช้</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <?php if (isset($positions)) : ?>
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
        if(isset($members)) {
            foreach($members as $member) {
                if(array_key_exists($member->m_system, $system_types)) {
                    $system_types[$member->m_system]['count']++;
                }
            }
        }

        // แสดงการ์ดสำหรับ system types
        foreach($system_types as $type => $data) {
            if($data['count'] > 0 && ($user_system == 'system_admin' || ($user_system == 'super_admin' && $type != 'system_admin'))) {
        ?>
            <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
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
        foreach ($positions as $position) : 
            if ($position->pid != 1 && $position->pid != 2) :  // ไม่แสดง pid 1 และ 2
                $count = isset($member_counts[$position->pid]) ? $member_counts[$position->pid] : 0;
                if ($count > 0) :
        ?>
                <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
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
    </div>

    <!-- โมดูลระบบ Section -->
    <!-- โมดูลระบบ Section -->
    <div class="bg-white p-6 rounded-xl shadow-md mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">โมดูลระบบ</h3>
        
        <!-- Module Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <?php
            // กำหนดไอคอนและสีสำหรับแต่ละโมดูล
            $module_icons = [
                1 => ['icon' => 'fas fa-users', 'color' => 'blue'],
                2 => ['icon' => 'fas fa-globe', 'color' => 'purple'],
                3 => ['icon' => 'fas fa-tasks', 'color' => 'indigo'],
                4 => ['icon' => 'fas fa-envelope', 'color' => 'red'],
                5 => ['icon' => 'fas fa-car', 'color' => 'yellow'],
                6 => ['icon' => 'fas fa-users-cog', 'color' => 'green'],
                7 => ['icon' => 'fas fa-user-clock', 'color' => 'blue'],
                8 => ['icon' => 'fas fa-money-bill-wave', 'color' => 'green'],
                9 => ['icon' => 'fas fa-video', 'color' => 'red'],
                10 => ['icon' => 'fas fa-boxes', 'color' => 'purple'],
                11 => ['icon' => 'fas fa-file-invoice', 'color' => 'indigo'],
                12 => ['icon' => 'fas fa-calendar-alt', 'color' => 'blue'],
                13 => ['icon' => 'fas fa-chart-line', 'color' => 'green'],
                14 => ['icon' => 'fas fa-cog', 'color' => 'red'],
                15 => ['icon' => 'fas fa-map-marker-alt', 'color' => 'purple'],
                16 => ['icon' => 'fas fa-comments', 'color' => 'yellow'],
                17 => ['icon' => 'fas fa-clipboard-list', 'color' => 'indigo'],
                18 => ['icon' => 'fas fa-book', 'color' => 'blue'],
                19 => ['icon' => 'fas fa-tools', 'color' => 'green'],
                20 => ['icon' => 'fas fa-handshake', 'color' => 'red'],
                21 => ['icon' => 'fas fa-folder-open', 'color' => 'purple'],
                22 => ['icon' => 'fas fa-building', 'color' => 'yellow'],
                23 => ['icon' => 'fas fa-bullhorn', 'color' => 'indigo'],
                24 => ['icon' => 'fas fa-award', 'color' => 'blue'],
                25 => ['icon' => 'fas fa-id-card', 'color' => 'green'],
                26 => ['icon' => 'fas fa-database', 'color' => 'red'],
                27 => ['icon' => 'fas fa-sitemap', 'color' => 'purple'],
                28 => ['icon' => 'fas fa-project-diagram', 'color' => 'yellow'],
                29 => ['icon' => 'fas fa-search', 'color' => 'indigo'],
                30 => ['icon' => 'fas fa-truck', 'color' => 'blue']
            ];
            
            // ดึงข้อมูลโมดูลจากฐานข้อมูล
            $query = $this->db->select('mm.*, COUNT(DISTINCT ms.m_id) as user_count')
                            ->from('tbl_member_modules mm')
                            ->join('tbl_member_systems ms', 'mm.id = ms.module_id', 'left')
                            ->group_by('mm.id')
                            ->order_by('mm.display_order', 'ASC')
                            ->where('mm.status', 1)
                            ->get();
                            
            $modules = $query->result_array();
            
            // กำหนดคลาสสีสำหรับการแสดงผล
            $color_classes = [
                'blue' => [
                    'bg' => 'from-blue-100 to-blue-50',
                    'text' => 'text-blue-800',
                    'type_bg' => 'bg-blue-100',
                    'type_text' => 'text-blue-800'
                ],
                'purple' => [
                    'bg' => 'from-purple-100 to-purple-50',
                    'text' => 'text-purple-800',
                    'type_bg' => 'bg-purple-100',
                    'type_text' => 'text-purple-800'
                ],
                'green' => [
                    'bg' => 'from-green-100 to-green-50',
                    'text' => 'text-green-800',
                    'type_bg' => 'bg-green-100',
                    'type_text' => 'text-green-800'
                ],
                'yellow' => [
                    'bg' => 'from-yellow-100 to-yellow-50',
                    'text' => 'text-yellow-800',
                    'type_bg' => 'bg-yellow-100',
                    'type_text' => 'text-yellow-800'
                ],
                'red' => [
                    'bg' => 'from-red-100 to-red-50',
                    'text' => 'text-red-800',
                    'type_bg' => 'bg-red-100',
                    'type_text' => 'text-red-800'
                ],
                'indigo' => [
                    'bg' => 'from-indigo-100 to-indigo-50',
                    'text' => 'text-indigo-800',
                    'type_bg' => 'bg-indigo-100',
                    'type_text' => 'text-indigo-800'
                ]
            ];
            
            // แสดงการ์ดของโมดูลแต่ละระบบ
            foreach($modules as $module):
                // กำหนดไอคอนและสีตามประเภทโมดูล
                $module_id = $module['id'];
                $icon_data = isset($module_icons[$module_id]) ? $module_icons[$module_id] : ['icon' => 'fas fa-cubes', 'color' => 'blue'];
                
                $color = $color_classes[$icon_data['color']];
                $is_trial = $module['is_trial'] == 1;
                $type_label = $is_trial ? 'Trial Version' : 'Full Version';
                $user_count = $module['user_count'] ? $module['user_count'] : 0;
            ?>
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-all duration-300 border border-gray-100">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-gradient-to-br <?php echo $color['bg']; ?> rounded-xl">
                            <i class="<?php echo $icon_data['icon']; ?> text-2xl <?php echo $color['text']; ?>"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-medium text-gray-800"><?php echo $module['name']; ?></h4>
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $color['type_bg']; ?> <?php echo $color['type_text']; ?> mt-1 inline-block">
                                <?php echo $type_label; ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 border-t pt-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-gray-500 text-sm">จำนวนผู้ใช้งาน</span>
                            <p class="text-xl font-semibold text-gray-800"><?php echo number_format($user_count); ?> คน</p>
                        </div>
                        <a href="<?php echo site_url('System_member/module/'.$module['code']); ?>" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-colors duration-300">
                            รายละเอียด <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
            <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                <h4 class="text-lg font-medium text-gray-800 mb-2">โมดูลทั้งหมด</h4>
                <p class="text-3xl font-bold text-blue-600"><?php echo number_format($full_modules + $trial_modules); ?></p>
                <div class="flex justify-between mt-4 text-sm">
                    <span class="text-gray-500">ใช้งานทั้งหมด</span>
                    <span class="text-green-600 font-medium">100%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                <h4 class="text-lg font-medium text-gray-800 mb-2">โมดูล Full Version</h4>
                <p class="text-3xl font-bold text-indigo-600"><?php echo number_format($full_modules); ?></p>
                <div class="flex justify-between mt-4 text-sm">
                    <span class="text-gray-500">คิดเป็น</span>
                    <span class="text-indigo-600 font-medium"><?php echo number_format(($full_modules / ($full_modules + $trial_modules)) * 100, 1); ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                    <div class="bg-indigo-600 h-2 rounded-full" style="width: <?php echo ($full_modules / ($full_modules + $trial_modules)) * 100; ?>%"></div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                <h4 class="text-lg font-medium text-gray-800 mb-2">โมดูล Trial Version</h4>
                <p class="text-3xl font-bold text-purple-600"><?php echo number_format($trial_modules); ?></p>
                <div class="flex justify-between mt-4 text-sm">
                    <span class="text-gray-500">คิดเป็น</span>
                    <span class="text-purple-600 font-medium"><?php echo number_format(($trial_modules / ($full_modules + $trial_modules)) * 100, 1); ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: <?php echo ($trial_modules / ($full_modules + $trial_modules)) * 100; ?>%"></div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                <h4 class="text-lg font-medium text-gray-800 mb-2">ผู้ใช้งานรวม</h4>
                <p class="text-3xl font-bold text-green-600"><?php echo number_format($active_users); ?></p>
                <div class="flex justify-between mt-4 text-sm">
                    <span class="text-gray-500">เทียบกับสมาชิกทั้งหมด</span>
                    <span class="text-green-600 font-medium"><?php echo number_format(($active_users / $total_members) * 100, 1); ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                    <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo min(($active_users / $total_members) * 100, 100); ?>%"></div>
                </div>
            </div>
        </div>

        <!-- หมายเหตุ -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3 text-lg"></i>
                <div>
                    <p class="font-medium mb-1">หมายเหตุ:</p>
                    <ul class="list-disc list-inside ml-2 space-y-1">
                        <li>โมดูล Full Version คือโมดูลที่ได้รับการอนุมัติและใช้งานได้เต็มรูปแบบ</li>
                        <li>โมดูล Trial Version คือโมดูลที่อยู่ในช่วงทดลองใช้งาน มีข้อจำกัดบางประการ</li>
                        <li>จำนวนผู้ใช้งานคือจำนวนผู้ใช้ที่มีสิทธิ์เข้าถึงโมดูลในระบบ</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function get_system_display_name($system) {
    switch($system) {
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
function confirmDelete(id) {
    if(confirm('คุณต้องการลบข้อมูลนี้ใช่หรือไม่?')) {
        window.location.href = '<?php echo site_url("System_member/delete_member/"); ?>' + id;
    }
}
</script>