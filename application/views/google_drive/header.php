<?php
/**
 * Google Drive Member Header View
 * Apple-inspired design header สำหรับ Google Drive interface
 */
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดเก็บข้อมูลแบบคลาวด์ (Cloud Storage) - File Management</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
	
    <!-- Apple-inspired Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<meta name="google-site-verification" content="w2TuDX5ngu3fpyZeIa93ENLpq6VEfAAaxMT-5PyrP5w" />
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">

    <!-- Header -->
    <header class="glass-card apple-shadow sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fab fa-google-drive text-white text-lg"></i>
                    </div>
                    <div>
                        <div class="flex items-center space-x-3">
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                                Drive Integration
                            </h1>
                            <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
                                <span class="trial-badge">
                                    <i class="fas fa-flask mr-1"></i>Trial
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-gray-600">
                            <?php 
                            // ใช้ get_config_value เพื่อดึงชื่อหน่วยงาน
                            $organization_name = get_config_value('fname', 'Personal File Management');
                            echo htmlspecialchars($organization_name);
                            ?>
                            <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
                                • ขีดจำกัด 1GB
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- User Info -->
                <div class="flex items-center space-x-4">
                    
                    
                    <div class="hidden md:flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-800" id="currentUserName">
                                <?php echo $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname'); ?>
                            </p>
                            <p class="text-xs text-gray-600" id="currentUserPosition">
                                <?php echo $this->session->userdata('position_name') ?? 'Staff'; ?>
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-sm">
                                <?php echo strtoupper(substr($this->session->userdata('m_fname'), 0, 1)); ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Menu Button -->
                    <button onclick="toggleUserMenu()" class="p-2 text-gray-600 hover:text-gray-800 hover:bg-white/50 rounded-lg transition-colors">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- User Menu (Hidden by default) -->
    <div id="userMenu" class="hidden fixed top-20 right-4 z-50 glass-card apple-shadow-lg rounded-2xl w-64 py-2">
        <div class="px-4 py-3 border-b border-gray-200/50">
            <p class="text-sm font-medium text-gray-800">Account Settings</p>
        </div>
        <div class="py-2">
            
            <?php if (isset($is_trial_mode) && $is_trial_mode): ?>
            <button onclick="showUpgradeModal()" class="w-full flex items-center px-4 py-2 text-sm text-orange-600 hover:bg-orange-50 transition-colors">
                <i class="fas fa-arrow-up mr-3"></i>อัปเกรดแผน
            </button>
            <?php endif; ?>
            <div class="border-t border-gray-200/50 my-2"></div>
            <a href="<?php echo site_url('User/logout'); ?>" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                <i class="fas fa-sign-out-alt mr-3"></i>Logout
            </a>
        </div>
    </div>