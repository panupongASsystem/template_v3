<?php
/**
 * Terms of Service Google Drive View
 * Path: application/views/google_drive/Terms_of_Service_Google_Drive.php
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
    'email_legal' => get_config_value('email_1') ?: 'legal@' . $current_domain,
    'email_support' => get_config_value('email_1') ?: 'support@' . $current_domain,
    'phone' => get_config_value('phone_1') ?: '02-XXX-XXXX',
    'website' => $current_domain,
    'working_hours' => 'จันทร์ - ศุกร์: 8.00 - 16.00 น.'
];



// ฟีเจอร์ของบริการ
$service_features = [
    ['icon' => 'fas fa-link', 'color' => 'green', 'title' => 'การเชื่อมต่อ Google Drive', 'desc' => 'เชื่อมต่อและซิงค์ข้อมูลอัตโนมัติ'],
    ['icon' => 'fas fa-folder-plus', 'color' => 'blue', 'title' => 'การสร้างโฟลเดอร์อัตโนมัติ', 'desc' => 'สร้างโครงสร้างโฟลเดอร์ตามองค์กร'],
    ['icon' => 'fas fa-key', 'color' => 'purple', 'title' => 'การจัดการสิทธิ์', 'desc' => 'ควบคุมสิทธิ์ตามตำแหน่งงาน'],
    ['icon' => 'fas fa-chart-line', 'color' => 'orange', 'title' => 'การติดตามการใช้งาน', 'desc' => 'รายงานและสถิติการใช้งาน']
];

// ประเภทผู้ใช้
$user_types = [
    ['icon' => 'fas fa-user-shield', 'color' => 'red', 'title' => 'ผู้ดูแลระบบ', 'desc' => 'สิทธิ์เต็มรูปแบบในการจัดการ'],
    ['icon' => 'fas fa-user-tie', 'color' => 'green', 'title' => 'ผู้ดูแลแผนก', 'desc' => 'จัดการเฉพาะแผนกที่รับผิดชอบ'],
    ['icon' => 'fas fa-user', 'color' => 'purple', 'title' => 'พนักงานทั่วไป', 'desc' => 'เข้าถึงตามสิทธิ์ที่ได้รับ'],
    ['icon' => 'fas fa-eye', 'color' => 'orange', 'title' => 'ผู้ดูข้อมูล', 'desc' => 'อ่านและดูข้อมูลเท่านั้น']
];

// ฟังก์ชันสำหรับ CSS classes
function getColorClasses($color) {
    $colors = [
        'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'border' => 'border-red-200'],
        'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'border' => 'border-green-200'],
        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'border' => 'border-blue-200'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'border' => 'border-purple-200'],
        'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'border' => 'border-orange-200'],
        'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'border' => 'border-yellow-200']
    ];
    return $colors[$color] ?? $colors['blue'];
}
?>
<!DOCTYPE html>
<html lang="<?php echo $language ?? 'th'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'ข้อกำหนดการใช้บริการ - ' . $company_name; ?></title>
    <meta name="description" content="<?php echo $meta_description ?? 'ข้อกำหนดและเงื่อนไขการใช้บริการ Google Drive Integration สำหรับ ' . $company_name; ?>">
    
    <!-- Stylesheets -->
    <script src="https://cdn.tailwindcss.com"></script>
	 <link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
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
        
        .floating-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(243, 244, 246, 0.8);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
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
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <!-- Floating Header -->
    <div class="floating-header sticky top-0 z-50 py-4">
        <div class="max-w-5xl mx-auto px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="icon-box bg-gradient-to-br from-blue-500 to-purple-600">
                        <i class="fas fa-file-contract text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">ข้อกำหนดการใช้บริการ</h1>
                        <p class="text-sm text-gray-600">Terms of Service</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <span class="text-sm text-gray-500">อัปเดต: <?php echo $current_date_th; ?></span>
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-green-600">มีผลบังคับใช้</span>
                </div>
                <div class="flex items-center space-x-2">
                    <!--  <a href="<?php echo site_url('google_drive_legal/change_language/en'); ?>" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">EN</a> -->
                    <a href="<?php echo site_url('google_drive_legal/change_language/th'); ?>" class="px-3 py-1 text-sm bg-blue-100 text-blue-600 rounded-lg">TH</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="relative overflow-hidden py-16">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50"></div>
        <div class="relative max-w-5xl mx-auto px-6 text-center">
            <div class="inline-flex items-center px-6 py-3 rounded-full bg-white shadow-lg border border-gray-100 mb-8">
                <i class="fas fa-shield-check text-green-500 mr-2"></i>
                <span class="text-sm font-medium text-gray-700">บริการปลอดภัย · ได้รับการรับรอง</span>
            </div>
            
            <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                ข้อกำหนดและ
                <span class="text-gradient">เงื่อนไขการใช้บริการ</span>
            </h1>
            
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                บริการจัดการเอกสาร <strong><?php echo $service_name; ?></strong> ของ <strong><?php echo $company_name; ?></strong>
                <br>กรุณาอ่านและทำความเข้าใจข้อกำหนดเหล่านี้ก่อนการใช้งาน
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-calendar-check text-blue-500 mr-2"></i>
                    <span class="text-sm">มีผลตั้งแต่ <?php echo $current_date_th; ?></span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-language text-purple-500 mr-2"></i>
                    <span class="text-sm">ภาษาไทย - Thailand</span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-code-branch text-green-500 mr-2"></i>
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
                    ['id' => 'acceptance', 'icon' => 'fas fa-handshake', 'color' => 'green', 'title' => 'การยอมรับข้อกำหนด', 'desc' => 'ข้อตกลงเบื้องต้น'],
                    ['id' => 'service', 'icon' => 'fas fa-cloud', 'color' => 'blue', 'title' => 'คำอธิบายบริการ', 'desc' => 'ฟีเจอร์และการใช้งาน'],
                    ['id' => 'responsibilities', 'icon' => 'fas fa-user-check', 'color' => 'purple', 'title' => 'ความรับผิดชอบ', 'desc' => 'หน้าที่ของผู้ใช้'],
                    ['id' => 'prohibited', 'icon' => 'fas fa-ban', 'color' => 'red', 'title' => 'การใช้งานที่ห้าม', 'desc' => 'สิ่งที่ไม่อนุญาต'],
                    ['id' => 'google-drive', 'icon' => 'fab fa-google-drive', 'color' => 'yellow', 'title' => 'Google Drive', 'desc' => 'การเชื่อมต่อและสิทธิ์'],
                    ['id' => 'contact', 'icon' => 'fas fa-envelope', 'color' => 'orange', 'title' => 'ติดต่อเรา', 'desc' => 'ช่องทางสื่อสาร']
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

        <!-- Acceptance Section -->
        <div id="acceptance" class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-green-400 to-green-600 mr-6">
                    <i class="fas fa-handshake text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">การยอมรับข้อกำหนด</h2>
                    <p class="text-lg text-gray-600">ข้อตกลงเบื้องต้นในการใช้บริการ</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-6 text-lg leading-relaxed">
                ยินดีต้อนรับสู่บริการ <strong><?php echo $service_name; ?></strong> ของ <strong><?php echo $company_name; ?></strong> 
                การใช้บริการของเราถือว่าคุณได้อ่าน เข้าใจ และยอมรับข้อกำหนดและเงื่อนไขดังต่อไปนี้
            </p>
            
            <div class="warning-modern">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-4 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-red-800 mb-2">ข้อความสำคัญ</h4>
                        <p class="text-red-700">
                            หากคุณไม่เห็นด้วยกับข้อกำหนดใดๆ กรุณาหยุดการใช้บริการทันที
                            การใช้งานต่อไปจะถือว่าท่านยอมรับข้อกำหนดทั้งหมด
                        </p>
                    </div>
                </div>
            </div>

            <div class="highlight-modern">
                <h4 class="font-semibold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                    ขอบเขตการใช้บังคับ
                </h4>
                <ul class="space-y-2 text-blue-700">
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                        <span>ข้อกำหนดนี้ใช้บังคับกับการใช้งาน Google Drive Integration เท่านั้น</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                        <span>เราปฏิบัติตามกฎหมายคุ้มครองข้อมูลส่วนบุคคล (PDPA) ของประเทศไทย</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                        <span>บริการนี้เป็นส่วนเสริมของระบบหลักขององค์กร</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Service Description -->
        <div id="service" class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-blue-400 to-blue-600 mr-6">
                    <i class="fas fa-cloud text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">คำอธิบายบริการ</h2>
                    <p class="text-lg text-gray-600">ฟีเจอร์และความสามารถของระบบ</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-8 text-lg leading-relaxed">
                บริการ <?php echo $service_name; ?> เป็นระบบจัดการเอกสารที่เชื่อมต่อกับ Google Drive 
                เพื่อช่วยให้องค์กรจัดการไฟล์และโฟลเดอร์ได้อย่างมีประสิทธิภาพ
            </p>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-star text-yellow-500 mr-3"></i>
                        ฟีเจอร์หลัก
                    </h3>
                    <div class="space-y-4">
                        <?php foreach ($service_features as $feature): 
                            $colorClass = getColorClasses($feature['color']);
                        ?>
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-8 h-8 <?php echo $colorClass['bg']; ?> rounded-lg flex items-center justify-center mr-4 mt-1">
                                <i class="<?php echo $feature['icon'] . ' ' . $colorClass['text']; ?>"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900"><?php echo $feature['title']; ?></h4>
                                <p class="text-gray-600 text-sm"><?php echo $feature['desc']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-users text-blue-500 mr-3"></i>
                        ประเภทผู้ใช้
                    </h3>
                    <div class="space-y-4">
                        <?php foreach ($user_types as $user): 
                            $colorClass = getColorClasses($user['color']);
                        ?>
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-8 h-8 <?php echo $colorClass['bg']; ?> rounded-lg flex items-center justify-center mr-4 mt-1">
                                <i class="<?php echo $user['icon'] . ' ' . $colorClass['text']; ?>"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900"><?php echo $user['title']; ?></h4>
                                <p class="text-gray-600 text-sm"><?php echo $user['desc']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Responsibilities -->
        <div id="responsibilities" class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-purple-400 to-purple-600 mr-6">
                    <i class="fas fa-user-check text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">ความรับผิดชอบของผู้ใช้</h2>
                    <p class="text-lg text-gray-600">หน้าที่และข้อปฏิบัติสำหรับผู้ใช้บริการ</p>
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="success-modern">
                    <h3 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-3"></i>
                        สิ่งที่ควรทำ
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-green-600 mr-3 mt-1"></i>
                            <span class="text-green-700">ใช้งานด้วยความปลอดภัยและรับผิดชอบ</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-key text-green-600 mr-3 mt-1"></i>
                            <span class="text-green-700">เก็บรักษาข้อมูลเข้าสู่ระบบให้ปลอดภัย</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-user-shield text-green-600 mr-3 mt-1"></i>
                            <span class="text-green-700">ใช้งานภายใต้สิทธิ์ที่ได้รับอนุญาต</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-bell text-green-600 mr-3 mt-1"></i>
                            <span class="text-green-700">แจ้งปัญหาหรือพฤติกรรมผิดปกติทันที</span>
                        </div>
                    </div>
                </div>
                
                <div class="warning-modern">
                    <h3 class="text-xl font-bold text-red-800 mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                        สิ่งที่ไม่ควรทำ
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-times text-red-600 mr-3 mt-1"></i>
                            <span class="text-red-700">เปิดเผยข้อมูลเข้าสู่ระบบให้ผู้อื่น</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-times text-red-600 mr-3 mt-1"></i>
                            <span class="text-red-700">เข้าถึงข้อมูลที่ไม่ได้รับอนุญาต</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-times text-red-600 mr-3 mt-1"></i>
                            <span class="text-red-700">ใช้บริการเพื่อกิจกรรมผิดกฎหมาย</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-times text-red-600 mr-3 mt-1"></i>
                            <span class="text-red-700">ละเมิดสิทธิ์ทางปัญญาของผู้อื่น</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prohibited Uses -->
        <div id="prohibited" class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-red-400 to-red-600 mr-6">
                    <i class="fas fa-ban text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">การใช้งานที่ห้าม</h2>
                    <p class="text-lg text-gray-600">กิจกรรมและพฤติกรรมที่ไม่อนุญาต</p>
                </div>
            </div>
            
            <div class="warning-modern">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-4 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-red-800 mb-2">คำเตือนสำคัญ</h4>
                        <p class="text-red-700 font-semibold">
                            การฝ่าฝืนข้อกำหนดเหล่านี้อาจส่งผลให้บัญชีถูกระงับหรือยกเลิกได้ทันที
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-shield-alt text-red-500 mr-2"></i>
                        ด้านความปลอดภัย
                    </h4>
                    <div class="space-y-3">
                        <div class="flex items-start p-3 bg-red-50 rounded-lg border border-red-200">
                            <i class="fas fa-virus text-red-500 mr-3 mt-1"></i>
                            <span class="text-red-700 text-sm">อัปโหลดมัลแวร์หรือไวรัส</span>
                        </div>
                        <div class="flex items-start p-3 bg-red-50 rounded-lg border border-red-200">
                            <i class="fas fa-user-secret text-red-500 mr-3 mt-1"></i>
                            <span class="text-red-700 text-sm">พยายามเข้าถึงระบบโดยไม่ได้รับอนุญาต</span>
                        </div>
                        <div class="flex items-start p-3 bg-red-50 rounded-lg border border-red-200">
                            <i class="fas fa-eye-slash text-red-500 mr-3 mt-1"></i>
                            <span class="text-red-700 text-sm">การสอดแนมหรือดักฟังข้อมูล</span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-gavel text-purple-500 mr-2"></i>
                        ด้านกฎหมาย
                    </h4>
                    <div class="space-y-3">
                        <div class="flex items-start p-3 bg-purple-50 rounded-lg border border-purple-200">
                            <i class="fas fa-copyright text-purple-500 mr-3 mt-1"></i>
                            <span class="text-purple-700 text-sm">ละเมิดลิขสิทธิ์หรือทรัพย์สินทางปัญญา</span>
                        </div>
                        <div class="flex items-start p-3 bg-purple-50 rounded-lg border border-purple-200">
                            <i class="fas fa-ban text-purple-500 mr-3 mt-1"></i>
                            <span class="text-purple-700 text-sm">เผยแพร่เนื้อหาที่ผิดกฎหมาย</span>
                        </div>
                        <div class="flex items-start p-3 bg-purple-50 rounded-lg border border-purple-200">
                            <i class="fas fa-balance-scale text-purple-500 mr-3 mt-1"></i>
                            <span class="text-purple-700 text-sm">กิจกรรมที่ขัดต่อกฎหมายไทย</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Google Drive Integration -->
        <div id="google-drive" class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-yellow-400 to-orange-500 mr-6">
                    <i class="fab fa-google-drive text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">การเชื่อมต่อ Google Drive</h2>
                    <p class="text-lg text-gray-600">สิทธิ์การเข้าถึงและข้อกำหนดพิเศษ</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-8 text-lg">เมื่อคุณเชื่อมต่อ Google Drive กับระบบของเรา เราจะขอสิทธิ์ดังต่อไปนี้:</p>
            
            <div class="info-modern mb-8">
                <h4 class="font-semibold text-yellow-800 mb-4 flex items-center text-xl">
                    <i class="fas fa-key text-yellow-600 mr-3"></i>
                    สิทธิ์ที่จำเป็น
                </h4>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div class="flex items-start p-3 bg-white rounded-lg border border-yellow-200">
                            <i class="fas fa-folder text-blue-500 mr-3 mt-1"></i>
                            <div>
                                <h5 class="font-semibold text-gray-900">จัดการโฟลเดอร์</h5>
                                <p class="text-sm text-gray-600">สร้าง แก้ไข และลบโฟลเดอร์</p>
                            </div>
                        </div>
                        <div class="flex items-start p-3 bg-white rounded-lg border border-yellow-200">
                            <i class="fas fa-file text-green-500 mr-3 mt-1"></i>
                            <div>
                                <h5 class="font-semibold text-gray-900">จัดการไฟล์</h5>
                                <p class="text-sm text-gray-600">อัปโหลด ดาวน์โหลด และจัดการไฟล์</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-start p-3 bg-white rounded-lg border border-yellow-200">
                            <i class="fas fa-share text-purple-500 mr-3 mt-1"></i>
                            <div>
                                <h5 class="font-semibold text-gray-900">แชร์และสิทธิ์</h5>
                                <p class="text-sm text-gray-600">กำหนดสิทธิ์การเข้าถึงโฟลเดอร์</p>
                            </div>
                        </div>
                        <div class="flex items-start p-3 bg-white rounded-lg border border-yellow-200">
                            <i class="fas fa-info-circle text-orange-500 mr-3 mt-1"></i>
                            <div>
                                <h5 class="font-semibold text-gray-900">ข้อมูลโปรไฟล์</h5>
                                <p class="text-sm text-gray-600">ชื่อ อีเมล และรูปโปรไฟล์</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="success-modern">
                    <h4 class="font-semibold text-green-800 mb-4 flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        สิ่งที่เราทำ
                    </h4>
                    <ul class="space-y-2 text-green-700">
                        <li class="flex items-start">
                            <i class="fas fa-dot-circle text-green-600 mr-2 mt-1 text-xs"></i>
                            <span>เข้าถึงเฉพาะโฟลเดอร์ที่เกี่ยวข้องกับองค์กร</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-dot-circle text-green-600 mr-2 mt-1 text-xs"></i>
                            <span>ใช้สิทธิ์เพื่อจุดประสงค์การทำงานเท่านั้น</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-dot-circle text-green-600 mr-2 mt-1 text-xs"></i>
                            <span>เก็บรักษาข้อมูลอย่างปลอดภัย</span>
                        </li>
                    </ul>
                </div>
                
                <div class="highlight-modern">
                    <h4 class="font-semibold text-blue-800 mb-4 flex items-center">
                        <i class="fas fa-shield-check text-blue-600 mr-2"></i>
                        สิ่งที่เราไม่ทำ
                    </h4>
                    <ul class="space-y-2 text-blue-700">
                        <li class="flex items-start">
                            <i class="fas fa-times text-red-500 mr-2 mt-1"></i>
                            <span>เข้าถึงไฟล์ส่วนตัวที่ไม่เกี่ยวข้อง</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-times text-red-500 mr-2 mt-1"></i>
                            <span>แชร์ข้อมูลให้บุคคลที่สาม</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-times text-red-500 mr-2 mt-1"></i>
                            <span>ใช้ข้อมูลเพื่อการโฆษณา</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div id="contact" class="modern-card p-8 mb-8">
            <div class="flex items-start mb-6">
                <div class="icon-box bg-gradient-to-br from-orange-400 to-orange-600 mr-6">
                    <i class="fas fa-envelope text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">ติดต่อเรา</h2>
                    <p class="text-lg text-gray-600">ช่องทางการติดต่อสำหรับข้อสงสัยและการสนับสนุน</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-8 text-lg">
                หากมีคำถามเกี่ยวกับข้อกำหนดนี้ หรือต้องการความช่วยเหลือ กรุณาติดต่อเราผ่านช่องทางด้านล่าง:
            </p>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="success-modern">
                    <h4 class="font-bold text-green-800 mb-6 flex items-center">
                        <i class="fas fa-balance-scale text-green-600 mr-3"></i>
                        ฝ่ายกฎหมายและปฏิบัติการ
                    </h4>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-white rounded-lg border border-green-200">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">อีเมล</p>
                                <p class="text-gray-600"><?php echo $company_info['email_legal']; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-white rounded-lg border border-green-200">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-phone text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">โทรศัพท์</p>
                                <p class="text-gray-600"><?php echo $company_info['phone']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="highlight-modern">
                    <h4 class="font-bold text-blue-800 mb-6 flex items-center">
                        <i class="fas fa-headset text-blue-600 mr-3"></i>
                        ฝ่ายเทคนิคและสนับสนุน
                    </h4>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-white rounded-lg border border-blue-200">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-envelope text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">อีเมล</p>
                                <p class="text-gray-600"><?php echo $company_info['email_support']; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-white rounded-lg border border-blue-200">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-phone text-orange-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">โทรศัพท์</p>
                                <p class="text-gray-600"><?php echo $company_info['phone']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="info-modern mt-8">
                <div class="text-center">
                    <h4 class="font-bold text-yellow-800 mb-4 flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 mr-3"></i>
                        เวลาทำการ
                    </h4>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-center p-3 bg-white rounded-lg border border-yellow-200">
                            <i class="fas fa-business-time text-orange-500 mr-3"></i>
                            <span class="text-gray-700"><?php echo $company_info['working_hours']; ?></span>
                        </div>
                        <div class="flex items-center justify-center p-3 bg-white rounded-lg border border-yellow-200">
                            <i class="fas fa-calendar-times text-red-500 mr-3"></i>
                            <span class="text-gray-700">หยุดวันเสาร์ - อาทิตย์ และวันหยุดราชการ</span>
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
                <a href="<?php echo site_url('google_drive_legal/privacy'); ?>" class="group block p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-shield-alt text-white"></i>
                        </div>
                        <h3 class="font-semibold text-blue-800">นโยบายความเป็นส่วนตัว</h3>
                    </div>
                    <p class="text-blue-700 text-sm">การคุ้มครองและจัดการข้อมูลส่วนบุคคล</p>
                    <div class="flex items-center mt-4 text-blue-600">
                        <span class="text-sm mr-2">อ่านเพิ่มเติม</span>
                        <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>
                
                <a href="<?php echo site_url('google_drive_legal/api_terms'); ?>" class="group block p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-code text-white"></i>
                        </div>
                        <h3 class="font-semibold text-green-800">API Documentation</h3>
                    </div>
                    <p class="text-green-700 text-sm">ข้อมูลข้อกำหนดในรูปแบบ JSON</p>
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
                        <i class="fas fa-shield-check text-gray-800 text-xl"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-bold"><?php echo $company_name; ?></h3>
                        <p class="text-gray-400 text-sm"><?php echo $service_name; ?> Service</p>
                    </div>
                </div>
                
                <div class="border-t border-gray-700 pt-6">
                    <p class="text-lg mb-2">© <?php echo (date('Y') + 543); ?> <?php echo get_config_value('fname'); ?> - สงวนลิขสิทธิ์</p>
                    <p class="text-gray-400">ข้อกำหนดและเงื่อนไขการใช้บริการ <?php echo $service_name; ?></p>
                    
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
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop" class="fixed bottom-6 right-6 w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 opacity-0 invisible">
        <i class="fas fa-arrow-up"></i>
    </button>

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
    </script>
</body>
</html>