<?php
/**
 * Privacy Policy Google Drive View
 * Path: application/views/google_drive/Privacy_Policy_Google_drive.php
 * 
 * @author   System Developer
 * @version  1.0.0
 * @since    2025-08-05
 */

// ตั้งค่าข้อมูลพื้นฐาน
$current_date_th = "5 สิงหาคม " . (date('Y') + 543); // ปีไทย
$current_domain = $_SERVER['HTTP_HOST'] ?? get_config_value('domain') . '.go.th';
$company_name = $current_domain;
$service_name = "Google Drive Integration";

// ข้อมูลบริษัท
$company_info = [
    'name' => $company_name,
    'service' => $service_name,
	'fname' => get_config_value('fname'),
    'email' => get_config_value('email_1') ?: 'admin@' . $current_domain,
    'phone' => get_config_value('phone_1') ?: '02-XXX-XXXX',
    'website' => $current_domain,
    'working_hours' => 'จันทร์ - ศุกร์: 8.00 - 16.00 น.'
];

// ประเภทข้อมูลที่เก็บ
$data_types = [
    'account' => [
        'icon' => 'fas fa-user',
        'color' => 'blue',
        'title' => 'ข้อมูลบัญชีผู้ใช้',
        'desc' => 'ข้อมูลพื้นฐานสำหรับการสร้างบัญชี',
        'items' => ['ชื่อ-นามสกุล', 'ที่อยู่อีเมล', 'เบอร์โทรศัพท์', 'ตำแหน่งงาน', 'รูปโปรไฟล์']
    ],
    'google' => [
        'icon' => 'fab fa-google',
        'color' => 'green',
        'title' => 'ข้อมูล Google Account',
        'desc' => 'ข้อมูลจาก Google OAuth',
        'items' => ['อีเมล Google ที่ใช้เชื่อมต่อ', 'ชื่อใน Google Profile', 'รูปโปรไฟล์จาก Google']
    ],
    'drive' => [
        'icon' => 'fas fa-folder',
        'color' => 'purple',
        'title' => 'ข้อมูล Google Drive',
        'desc' => 'ข้อมูลการใช้งาน Drive',
        'items' => ['รายชื่อโฟลเดอร์', 'สิทธิ์การเข้าถึง', 'ประวัติการใช้งาน', 'Log การดำเนินการ']
    ],
    'technical' => [
        'icon' => 'fas fa-server',
        'color' => 'orange',
        'title' => 'ข้อมูลทางเทคนิค',
        'desc' => 'ข้อมูลการเข้าใช้งานระบบ',
        'items' => ['IP Address', 'User Agent', 'เวลาการเข้าใช้งาน', 'หน้าเว็บที่เข้าชม']
    ]
];

// สิทธิ์ Google Drive
$google_permissions = [
    ['icon' => 'fas fa-folder', 'color' => 'blue', 'title' => 'จัดการโฟลเดอร์', 'desc' => 'สร้าง แก้ไข และลบโฟลเดอร์'],
    ['icon' => 'fas fa-file', 'color' => 'green', 'title' => 'จัดการไฟล์', 'desc' => 'อัปโหลด ดาวน์โหลด และจัดการไฟล์'],
    ['icon' => 'fas fa-share', 'color' => 'purple', 'title' => 'แชร์และสิทธิ์', 'desc' => 'กำหนดสิทธิ์การเข้าถึงโฟลเดอร์'],
    ['icon' => 'fas fa-info-circle', 'color' => 'orange', 'title' => 'ข้อมูลโปรไฟล์', 'desc' => 'ชื่อ อีเมล และรูปโปรไฟล์']
];

// สิทธิ์ผู้ใช้
$user_rights = [
    ['icon' => 'fas fa-eye', 'color' => 'blue', 'title' => 'เข้าถึงข้อมูล', 'desc' => 'ขอดูข้อมูลส่วนบุคคลที่เราเก็บรักษา'],
    ['icon' => 'fas fa-edit', 'color' => 'green', 'title' => 'แก้ไขข้อมูล', 'desc' => 'ขอแก้ไขข้อมูลที่ไม่ถูกต้อง'],
    ['icon' => 'fas fa-trash', 'color' => 'red', 'title' => 'ลบข้อมูล', 'desc' => 'ขอลบข้อมูลส่วนบุคคล'],
    ['icon' => 'fas fa-download', 'color' => 'purple', 'title' => 'ส่งออกข้อมูล', 'desc' => 'ขอรับสำเนาข้อมูล'],
    ['icon' => 'fas fa-ban', 'color' => 'orange', 'title' => 'จำกัดการประมวลผล', 'desc' => 'ขอจำกัดการใช้ข้อมูล'],
    ['icon' => 'fas fa-times-circle', 'color' => 'gray', 'title' => 'ยกเลิกความยินยอม', 'desc' => 'ถอนความยินยอมการประมวลผล']
];

// ฟังก์ชันสำหรับ CSS classes
function getColorClasses($color) {
    $colors = [
        'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'border' => 'border-red-200'],
        'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'border' => 'border-green-200'],
        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'border' => 'border-blue-200'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'border' => 'border-purple-200'],
        'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'border' => 'border-orange-200'],
        'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'border' => 'border-yellow-200'],
        'gray' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'border' => 'border-gray-200']
    ];
    return $colors[$color] ?? $colors['blue'];
}
?>
<!DOCTYPE html>
<html lang="<?php echo $language ?? 'th'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'นโยบายความเป็นส่วนตัว - ' . $company_name; ?></title>
    <meta name="description" content="<?php echo $meta_description ?? 'นโยบายความเป็นส่วนตัวสำหรับการใช้งาน Google Drive Integration ของ ' . $company_name; ?>">
    
    <!-- Stylesheets -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	 <link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .modern-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(243, 244, 246, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .modern-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .icon-box {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .icon-box:hover {
            transform: scale(1.1);
        }
        
        .highlight-modern {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-left: 4px solid #0ea5e9;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .warning-modern {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-left: 4px solid #ef4444;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .success-modern {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-left: 4px solid #22c55e;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .info-modern {
            background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .purple-modern {
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            border-left: 4px solid #a855f7;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .floating-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(243, 244, 246, 0.8);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        
        .data-box {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        
        .data-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #9333ea 0%, #db2777 100%);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <!-- Floating Header -->
    <div class="floating-header sticky top-0 z-50 py-4">
        <div class="max-w-5xl mx-auto px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="icon-box bg-gradient-to-br from-purple-500 to-pink-600">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">นโยบายความเป็นส่วนตัว</h1>
                        <p class="text-sm text-gray-600">Privacy Policy</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <span class="text-sm text-gray-500">อัปเดต: <?php echo $current_date_th; ?></span>
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-green-600">มีผลบังคับใช้</span>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- <a href="<?php echo site_url('google_drive_legal/change_language/en'); ?>" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">EN</a> -->
                    <a href="<?php echo site_url('google_drive_legal/change_language/th'); ?>" class="px-3 py-1 text-sm bg-purple-100 text-purple-600 rounded-lg">TH</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="relative overflow-hidden py-16">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-50 via-white to-pink-50"></div>
        <div class="relative max-w-5xl mx-auto px-6 text-center">
            <div class="inline-flex items-center px-6 py-3 rounded-full bg-white shadow-lg border border-gray-100 mb-8">
                <i class="fas fa-lock text-purple-500 mr-2"></i>
                <span class="text-sm font-medium text-gray-700">ข้อมูลปลอดภัย · การปกป้องความเป็นส่วนตัว</span>
            </div>
            
            <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                นโยบาย
                <span class="text-gradient">ความเป็นส่วนตัว</span>
            </h1>
            
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                เราให้ความสำคัญกับความเป็นส่วนตัวและความปลอดภัยของข้อมูลส่วนบุคคล
                <br>เรียนรู้วิธีการที่เราปกป้องและจัดการข้อมูลของคุณ
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-calendar-check text-purple-500 mr-2"></i>
                    <span class="text-sm">มีผลตั้งแต่ <?php echo $current_date_th; ?></span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-shield-check text-green-500 mr-2"></i>
                    <span class="text-sm">PDPA Compliant</span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-code-branch text-blue-500 mr-2"></i>
                    <span class="text-sm">เวอร์ชัน <?php echo $version ?? '1.0'; ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 pb-16">
        <!-- Quick Navigation -->
        <div class="modern-card p-8 mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">สารบัญ</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <?php
                $nav_items = [
                    ['id' => 'overview', 'icon' => 'fas fa-shield-alt', 'color' => 'purple', 'title' => 'ภาพรวมและความมุ่งมั่น', 'desc' => 'หลักการและนโยบาย'],
                    ['id' => 'data-collection', 'icon' => 'fas fa-database', 'color' => 'green', 'title' => 'ข้อมูลที่เก็บรวบรวม', 'desc' => 'ประเภทข้อมูลต่างๆ'],
                    ['id' => 'data-usage', 'icon' => 'fas fa-cogs', 'color' => 'blue', 'title' => 'การใช้ข้อมูล', 'desc' => 'วัตถุประสงค์การใช้งาน'],
                    ['id' => 'google-drive', 'icon' => 'fab fa-google-drive', 'color' => 'yellow', 'title' => 'สิทธิ์ Google Drive', 'desc' => 'การเข้าถึงและใช้งาน'],
                    ['id' => 'security', 'icon' => 'fas fa-lock', 'color' => 'red', 'title' => 'ความปลอดภัย', 'desc' => 'มาตรการรักษาความปลอดภัย'],
                    ['id' => 'user-rights', 'icon' => 'fas fa-user-shield', 'color' => 'orange', 'title' => 'สิทธิ์ของผู้ใช้', 'desc' => 'สิทธิ์และการควบคุม']
                ];
                
                foreach ($nav_items as $item):
                    $colorClass = getColorClasses($item['color']);
                ?>
                <a href="#<?php echo $item['id']; ?>" class="flex items-center p-4 rounded-16 hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 <?php echo $colorClass['bg']; ?> rounded-12 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                        <i class="<?php echo $item['icon'] . ' ' . $colorClass['text']; ?>"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900"><?php echo $item['title']; ?></h3>
                        <p class="text-sm text-gray-600"><?php echo $item['desc']; ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Overview Section -->
        <div id="overview" class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-purple-400 to-purple-600 mr-6">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">ภาพรวมและความมุ่งมั่น</h2>
                    <p class="text-lg text-gray-600">หลักการและแนวทางในการปกป้องข้อมูลส่วนบุคคล</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-6 text-lg leading-relaxed">
                เว็บไซต์ <strong><?php echo $company_name; ?></strong> ให้ความสำคัญกับความเป็นส่วนตัวและความปลอดภัยของข้อมูลส่วนบุคคลของผู้ใช้บริการ 
                นโยบายนี้อธิบายวิธีการที่เราเก็บรวบรวม ใช้ และปกป้องข้อมูลของคุณ
            </p>
            
            <div class="highlight-modern">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 text-xl mr-4 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-blue-800 mb-2">ขอบเขตการใช้บังคับ</h4>
                        <p class="text-blue-700">
                            นโยบายนี้ใช้บังคับกับการใช้งาน Google Drive Integration และบริการที่เกี่ยวข้องเท่านั้น
                            เราปฏิบัติตามกฎหมายคุ้มครองข้อมูลส่วนบุคคล (PDPA) ของประเทศไทย
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Collection Section -->
        <div id="data-collection" class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-green-400 to-green-600 mr-6">
                    <i class="fas fa-database text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">ข้อมูลที่เราเก็บรวบรวม</h2>
                    <p class="text-lg text-gray-600">ประเภทข้อมูลต่างๆ ที่จำเป็นสำหรับการให้บริการ</p>
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                <?php foreach ($data_types as $type): 
                    $colorClass = getColorClasses($type['color']);
                ?>
                <div class="data-box">
                    <div class="flex items-start mb-4">
                        <div class="w-10 h-10 <?php echo $colorClass['bg']; ?> rounded-lg flex items-center justify-center mr-4">
                            <i class="<?php echo $type['icon'] . ' ' . $colorClass['text']; ?>"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800"><?php echo $type['title']; ?></h3>
                            <p class="text-sm text-gray-600"><?php echo $type['desc']; ?></p>
                        </div>
                    </div>
                    <ul class="space-y-2 text-gray-700">
                        <?php foreach ($type['items'] as $item): ?>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            <span><?php echo $item; ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Data Usage Section -->
        <div id="data-usage" class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-blue-400 to-blue-600 mr-6">
                    <i class="fas fa-cogs text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">การใช้ข้อมูล</h2>
                    <p class="text-lg text-gray-600">วัตถุประสงค์และแนวทางการใช้ข้อมูลของคุณ</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-8 text-lg">เราใช้ข้อมูลของคุณเพื่อวัตถุประสงค์ดังต่อไปนี้:</p>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="success-modern">
                    <h3 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                        <i class="fas fa-star text-green-600 mr-3"></i>
                        วัตถุประสงค์หลัก
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span class="text-green-700">จัดการบัญชีผู้ใช้และการเข้าสู่ระบบ</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span class="text-green-700">เชื่อมต่อและจัดการ Google Drive</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span class="text-green-700">สร้างและจัดการโฟลเดอร์ตามสิทธิ์</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span class="text-green-700">ควบคุมการเข้าถึงข้อมูล</span>
                        </div>
                    </div>
                </div>
                
                <div class="highlight-modern">
                    <h3 class="text-xl font-bold text-blue-800 mb-4 flex items-center">
                        <i class="fas fa-plus text-blue-600 mr-3"></i>
                        วัตถุประสงค์รอง
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-chart-line text-blue-600 mr-3 mt-1"></i>
                            <span class="text-blue-700">วิเคราะห์การใช้งานเพื่อปรับปรุงระบบ</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-blue-600 mr-3 mt-1"></i>
                            <span class="text-blue-700">รักษาความปลอดภัยของระบบ</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-bug text-blue-600 mr-3 mt-1"></i>
                            <span class="text-blue-700">แก้ไขปัญหาทางเทคนิค</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-headset text-blue-600 mr-3 mt-1"></i>
                            <span class="text-blue-700">ให้การสนับสนุนทางเทคนิค</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Google Drive Permissions -->
        <div id="google-drive" class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-yellow-400 to-orange-500 mr-6">
                    <i class="fab fa-google-drive text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">สิทธิ์การเข้าถึง Google Drive</h2>
                    <p class="text-lg text-gray-600">รายละเอียดสิทธิ์และการใช้งาน Google Drive</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-8 text-lg">เมื่อคุณเชื่อมต่อ Google Drive กับระบบของเรา เราจะขอสิทธิ์ดังต่อไปนี้:</p>
            
            <div class="info-modern mb-8">
                <h4 class="font-semibold text-yellow-800 mb-4 flex items-center text-xl">
                    <i class="fas fa-key text-yellow-600 mr-3"></i>
                    สิทธิ์ที่ขอ
                </h4>
                <div class="grid md:grid-cols-2 gap-6">
                    <?php foreach ($google_permissions as $permission): 
                        $colorClass = getColorClasses($permission['color']);
                    ?>
                    <div class="flex items-start p-3 bg-white rounded-lg border border-yellow-200">
                        <i class="<?php echo $permission['icon'] . ' ' . $colorClass['text']; ?> mr-3 mt-1"></i>
                        <div>
                            <h5 class="font-semibold text-gray-900"><?php echo $permission['title']; ?></h5>
                            <p class="text-sm text-gray-600"><?php echo $permission['desc']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="success-modern">
                    <h4 class="font-semibold text-green-800 mb-4 flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        การใช้งานสิทธิ์
                    </h4>
                    <ul class="space-y-2 text-green-700">
                        <li class="flex items-start">
                            <i class="fas fa-dot-circle text-green-600 mr-2 mt-1 text-xs"></i>
                            <span>เราจะใช้สิทธิ์เฉพาะในการจัดการโฟลเดอร์ที่เกี่ยวข้องกับองค์กรเท่านั้น</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-dot-circle text-green-600 mr-2 mt-1 text-xs"></i>
                            <span>เราไม่เข้าถึงไฟล์ส่วนตัวหรือโฟลเดอร์ที่ไม่เกี่ยวข้อง</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-dot-circle text-green-600 mr-2 mt-1 text-xs"></i>
                            <span>คุณสามารถยกเลิกสิทธิ์ได้ตลอดเวลาจากการตั้งค่า Google Account</span>
                        </li>
                    </ul>
                </div>
                
                <div class="highlight-modern">
                    <h4 class="font-semibold text-blue-800 mb-4 flex items-center">
                        <i class="fas fa-eye text-blue-600 mr-2"></i>
                        ขอบเขตการเข้าถึง
                    </h4>
                    <ul class="space-y-2 text-blue-700">
                        <li class="flex items-start">
                            <i class="fas fa-folder-open text-blue-600 mr-2 mt-1"></i>
                            <span>เฉพาะโฟลเดอร์ที่สร้างผ่านระบบนี้</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-user-circle text-blue-600 mr-2 mt-1"></i>
                            <span>ข้อมูลโปรไฟล์พื้นฐานเท่านั้น</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-chart-bar text-blue-600 mr-2 mt-1"></i>
                            <span>ข้อมูลการใช้งานเพื่อความปลอดภัย</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Data Security -->
        <div id="security" class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-red-400 to-red-600 mr-6">
                    <i class="fas fa-lock text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">ความปลอดภัยของข้อมูล</h2>
                    <p class="text-lg text-gray-600">มาตรการและระบบรักษาความปลอดภัย</p>
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="success-modern">
                    <h3 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                        <i class="fas fa-shield-check text-green-600 mr-3"></i>
                        มาตรการรักษาความปลอดภัย
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-green-600 mr-3 mt-1"></i>
                            <span class="text-green-700">เข้ารหัสข้อมูลแบบ HTTPS/SSL</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-key text-green-600 mr-3 mt-1"></i>
                            <span class="text-green-700">ระบบการยืนยันตัวตนหลายขั้นตอน</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-database text-green-600 mr-3 mt-1"></i>
                            <span class="text-green-700">เก็บข้อมูลในเซิร์ฟเวอร์ที่ปลอดภัย</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-user-check text-green-600 mr-3 mt-1"></i>
                            <span class="text-green-700">ควบคุมการเข้าถึงตามสิทธิ์</span>
                        </div>
                    </div>
                </div>
                
                <div class="highlight-modern">
                    <h3 class="text-xl font-bold text-blue-800 mb-4 flex items-center">
                        <i class="fas fa-tools text-blue-600 mr-3"></i>
                        การสำรองและป้องกัน
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-history text-blue-600 mr-3 mt-1"></i>
                            <span class="text-blue-700">สำรองข้อมูลสำคัญเป็นประจำ</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-eye text-blue-600 mr-3 mt-1"></i>
                            <span class="text-blue-700">ตรวจสอบการเข้าถึงอย่างต่อเนื่อง</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-blue-600 mr-3 mt-1"></i>
                            <span class="text-blue-700">แจ้งเตือนกิจกรรมผิดปกติ</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-sync text-blue-600 mr-3 mt-1"></i>
                            <span class="text-blue-700">อัปเดตระบบความปลอดภัย</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Rights -->
        <div id="user-rights" class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-orange-400 to-orange-600 mr-6">
                    <i class="fas fa-user-shield text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">สิทธิ์ของผู้ใช้</h2>
                    <p class="text-lg text-gray-600">สิทธิ์และการควบคุมข้อมูลส่วนบุคคลของคุณ</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-8 text-lg">คุณมีสิทธิ์ในการดำเนินการต่อไปนี้เกี่ยวกับข้อมูลส่วนบุคคลของคุณ:</p>
            
            <div class="grid md:grid-cols-3 gap-6">
                <?php foreach ($user_rights as $right): 
                    $colorClass = getColorClasses($right['color']);
                ?>
                <div class="data-box">
                    <div class="text-center">
                        <div class="w-12 h-12 <?php echo $colorClass['bg']; ?> rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="<?php echo $right['icon'] . ' ' . $colorClass['text']; ?> text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2"><?php echo $right['title']; ?></h4>
                        <p class="text-sm text-gray-600"><?php echo $right['desc']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-blue-400 to-blue-600 mr-6">
                    <i class="fas fa-envelope text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">ติดต่อเรา</h2>
                    <p class="text-lg text-gray-600">ช่องทางการติดต่อสำหรับข้อสงสัยเกี่ยวกับความเป็นส่วนตัว</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-8 text-lg">หากมีคำถามเกี่ยวกับนโยบายความเป็นส่วนตัวนี้ กรุณาติดต่อเราผ่าน:</p>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="highlight-modern">
                    <h4 class="font-bold text-blue-800 mb-6 flex items-center">
                        <i class="fas fa-envelope text-blue-600 mr-3"></i>
                        ข้อมูลติดต่อ
                    </h4>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-white rounded-lg border border-blue-200">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">อีเมล</p>
                                <p class="text-gray-600"><?php echo $company_info['email']; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-white rounded-lg border border-blue-200">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-globe text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">เว็บไซต์</p>
                                <p class="text-gray-600"><?php echo $company_info['website']; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-white rounded-lg border border-blue-200">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-phone text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">โทรศัพท์</p>
                                <p class="text-gray-600"><?php echo $company_info['phone']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="success-modern">
                    <h4 class="font-bold text-green-800 mb-6 flex items-center">
                        <i class="fas fa-clock text-green-600 mr-3"></i>
                        เวลาทำการ
                    </h4>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-white rounded-lg border border-green-200">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-business-time text-orange-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">วันทำการ</p>
                                <p class="text-gray-600"><?php echo $company_info['working_hours']; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-white rounded-lg border border-green-200">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-calendar-times text-red-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">วันหยุด</p>
                                <p class="text-gray-600">หยุดวันเสาร์ - อาทิตย์ และวันหยุดราชการ</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-white rounded-lg border border-green-200">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-reply text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">ระยะเวลาตอบกลับ</p>
                                <p class="text-gray-600">ภายใน 48 ชั่วโมง</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Documents -->
        <div class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-gray-400 to-gray-600 mr-6">
                    <i class="fas fa-file-alt text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">เอกสารที่เกี่ยวข้อง</h2>
                    <p class="text-lg text-gray-600">นโยบายและข้อกำหนดอื่นๆ ที่คุณควรทราบ</p>
                </div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                <a href="<?php echo site_url('google_drive_legal/terms'); ?>" class="group block p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-contract text-white"></i>
                        </div>
                        <h3 class="font-semibold text-blue-800">ข้อกำหนดการใช้บริการ</h3>
                    </div>
                    <p class="text-blue-700 text-sm">Terms of Service และเงื่อนไขการใช้งาน</p>
                    <div class="flex items-center mt-4 text-blue-600">
                        <span class="text-sm mr-2">อ่านเพิ่มเติม</span>
                        <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>
                
                <a href="<?php echo site_url('google_drive_legal/api_privacy'); ?>" class="group block p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-code text-white"></i>
                        </div>
                        <h3 class="font-semibold text-green-800">API Documentation</h3>
                    </div>
                    <p class="text-green-700 text-sm">ข้อมูลนโยบายในรูปแบบ JSON</p>
                    <div class="flex items-center mt-4 text-green-600">
                        <span class="text-sm mr-2">ดาวน์โหลด</span>
                        <i class="fas fa-download text-xs group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>
                
                <a href="<?php echo site_url('google_drive_files'); ?>" class="group block p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <i class="fab fa-google-drive text-white"></i>
                        </div>
                        <h3 class="font-semibold text-purple-800">ใช้งานระบบ</h3>
                    </div>
                    <p class="text-purple-700 text-sm">เข้าสู่ระบบจัดการ Google Drive</p>
                    <div class="flex items-center mt-4 text-purple-600">
                        <span class="text-sm mr-2">เข้าใช้งาน</span>
                        <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-900 to-gray-800 text-white py-12">
        <div class="max-w-5xl mx-auto px-6">
            <div class="text-center">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-shield-alt text-gray-800 text-xl"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-bold"><?php echo $company_name; ?></h3>
                        <p class="text-gray-400 text-sm"><?php echo $service_name; ?> Service</p>
                    </div>
                </div>
                
                <div class="border-t border-gray-700 pt-6">
                    <p class="text-lg mb-2">© <?php echo (date('Y') + 543); ?> <?php echo get_config_value('fname'); ?> - สงวนลิขสิทธิ์</p>
                    <p class="text-gray-400">นโยบายความเป็นส่วนตัวสำหรับบริการ <?php echo $service_name; ?></p>
                    
                    <div class="flex items-center justify-center mt-6 space-x-6">
                        <a href="<?php echo site_url('google_drive_legal/privacy'); ?>" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-shield-alt mr-2"></i>นโยบายความเป็นส่วนตัว
                        </a>
                        <a href="<?php echo site_url('google_drive_legal/terms'); ?>" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-file-contract mr-2"></i>ข้อกำหนดการใช้งาน
                        </a>
                        <a href="#contact" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-envelope mr-2"></i>ติดต่อเรา
                        </a>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-center space-x-4">
                        <div class="flex items-center">
                            <i class="fas fa-shield-check text-green-400 mr-2"></i>
                            <span class="text-sm text-gray-400">PDPA Compliant</span>
                        </div>
                        <div class="w-1 h-1 bg-gray-600 rounded-full"></div>
                        <div class="flex items-center">
                            <i class="fas fa-lock text-blue-400 mr-2"></i>
                            <span class="text-sm text-gray-400">SSL Secured</span>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop" class="fixed bottom-6 right-6 w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 opacity-0 invisible">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Privacy Notice Toast 
    <div id="privacyToast" class="fixed bottom-6 left-6 right-6 md:left-auto md:right-6 md:w-96 bg-white rounded-lg shadow-xl border border-gray-200 transform translate-y-full transition-transform duration-300">
        <div class="p-4">
            <div class="flex items-start">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-shield-alt text-purple-600"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 mb-1">การคุ้มครองข้อมูลส่วนบุคคล</h4>
                    <p class="text-sm text-gray-600 mb-3">เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานของคุณ</p>
                    <div class="flex space-x-3">
                        <button onclick="acceptPrivacy()" class="px-3 py-1 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">
                            ยอมรับ
                        </button>
                        <button onclick="closePrivacyToast()" class="px-3 py-1 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition-colors">
                            ปิด
                        </button>
                    </div>
                </div>
                <button onclick="closePrivacyToast()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div> --> 

    <!-- JavaScript -->
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Scroll to top button functionality
        const scrollToTopBtn = document.getElementById('scrollToTop');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.remove('opacity-0', 'invisible');
                scrollToTopBtn.classList.add('opacity-100', 'visible');
            } else {
                scrollToTopBtn.classList.add('opacity-0', 'invisible');
                scrollToTopBtn.classList.remove('opacity-100', 'visible');
            }
        });

        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Privacy toast functionality
        const privacyToast = document.getElementById('privacyToast');
        
        // Show privacy toast after 2 seconds
        setTimeout(() => {
            const hasAccepted = localStorage.getItem('privacyAccepted');
            if (!hasAccepted) {
                privacyToast.classList.remove('translate-y-full');
            }
        }, 2000);

        function acceptPrivacy() {
            localStorage.setItem('privacyAccepted', 'true');
            closePrivacyToast();
        }

        function closePrivacyToast() {
            privacyToast.classList.add('translate-y-full');
        }

        // Add loading animation
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.modern-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Add intersection observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.modern-card').forEach(card => {
            observer.observe(card);
        });

        // Data boxes hover effect
        document.querySelectorAll('.data-box').forEach(box => {
            box.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px) scale(1.02)';
            });
            
            box.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>
</body>
</html>