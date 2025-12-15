<!DOCTYPE html>
<html lang="th">
<head>
	<base href="<?php echo base_url(); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* สี */
        :root {
            --primary: #6C63FF;
            --primary-dark: #5A56E0;
            --secondary: #F6F8FD;
            --success: #36B37E;
            --danger: #FF5C5C;
            --warning: #FFAB2B;
            --info: #5E6A84;
            --light: #F6F8FD;
            --dark: #202342;
            --white: #FFFFFF;
            --gray-100: #F9FAFC;
            --gray-200: #EBEEF5;
            --gray-300: #DFE3E8;
            --gray-400: #C4CDD5;
            --gray-500: #919EAB;
            --gray-600: #637381;
            --gray-700: #454F5B;
            --gray-800: #333747;
            --border-color: #EBEEF5;
        }
        
        /* ปรับแต่งรูปแบบของหน้า */
        body {
            font-family: 'Poppins', 'Prompt', sans-serif;
            background-color: var(--gray-100);
            color: var(--gray-700);
            line-height: 1.6;
        }
        
        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: 'Prompt', sans-serif;
            font-weight: 500;
            color: var(--dark);
        }
        
        .container-fluid {
            padding: 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }
        
        /* หัวข้อหลัก */
        .main-header {
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: none;
        }
        
        .main-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.25rem;
            font-size: 2rem;
        }
        
        /* การ์ดสถิติ */
        .card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 5px 25px rgba(108, 99, 255, 0.07);
            transition: all 0.3s ease;
            overflow: hidden;
            height: 100%;
            margin-bottom: 1.5rem;
            background-color: var(--white);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(108, 99, 255, 0.1);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }
        
        .card-title {
            font-size: 1.1rem;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            color: var(--dark);
        }
        
        .card-title i {
            color: var(--primary);
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        .stats-card .card-body {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .stats-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: white;
            font-size: 1.5rem;
            position: relative;
        }
        
        .stats-icon::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: inherit;
            opacity: 0.2;
            z-index: -1;
        }
        
        .stats-icon.primary {
            background-color: var(--primary);
        }
        
        .stats-icon.success {
            background-color: var(--success);
        }
        
        .stats-icon.info {
            background-color: var(--info);
        }
        
        .stats-icon.danger {
            background-color: var(--danger);
        }
        
        .stats-value {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            line-height: 1.2;
            background: linear-gradient(45deg, var(--primary-dark), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stats-card:nth-child(2) .stats-value {
            background: linear-gradient(45deg, #2EAA75, var(--success));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stats-card:nth-child(3) .stats-value {
            background: linear-gradient(45deg, #E64A4A, var(--danger));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stats-card:nth-child(4) .stats-value {
            background: linear-gradient(45deg, #4E5A70, var(--info));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stats-label {
            color: var(--gray-600);
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* ป้ายแสดงสถานะ */
        .badge {
            padding: 0.55rem 1rem;
            font-weight: 500;
            border-radius: 30px;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }
        
        .badge.bg-success {
            background-color: rgba(54, 179, 126, 0.15) !important;
            color: var(--success) !important;
        }
        
        .badge.bg-info {
            background-color: rgba(94, 106, 132, 0.15) !important;
            color: var(--info) !important;
        }
        
        .badge.bg-danger {
            background-color: rgba(255, 92, 92, 0.15) !important;
            color: var(--danger) !important;
        }
        
        .badge.bg-primary {
            background-color: rgba(108, 99, 255, 0.15) !important;
            color: var(--primary) !important;
        }
        
        /* ตาราง */
        .table {
            margin-bottom: 0;
            font-size: 0.95rem;
            color: var(--gray-700);
        }
        
        .table th {
            font-weight: 600;
            color: var(--gray-800);
            background-color: var(--secondary);
            border: none;
            padding: 1.25rem 1rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
            border-bottom-color: var(--border-color);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(108, 99, 255, 0.02);
        }
        
        .activity-time {
            font-size: 0.85rem;
            color: var(--gray-500);
            white-space: nowrap;
        }
        
        .device-icon {
            margin-right: 8px;
            opacity: 0.8;
        }
        
        /* กล่องค้นหา */
        .search-box {
            padding: 1.75rem;
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 5px 25px rgba(108, 99, 255, 0.07);
            margin-bottom: 2rem;
        }
        
        .input-group {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(108, 99, 255, 0.04);
        }
        
        .input-group-text {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-right: 0;
            color: var(--gray-500);
            padding: 0.75rem 1rem;
        }
        
        .form-control {
            border: 1px solid var(--border-color);
            border-left: 0;
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
            font-weight: 400;
            color: var(--gray-700);
        }
        
        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary);
        }
        
        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.15);
        }
        
        .input-group:focus-within .input-group-text {
            border-color: var(--primary);
            color: var(--primary);
        }
        
        .form-select {
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background-color: var(--white);
            font-weight: 400;
            color: var(--gray-700);
            box-shadow: 0 2px 10px rgba(108, 99, 255, 0.04);
        }
        
        .form-select:focus {
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.15);
            border-color: var(--primary);
        }
        
        .search-icon {
            color: var(--gray-500);
        }
        
        /* ปุ่มต่างๆ */
        .btn {
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(108, 99, 255, 0.2);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            box-shadow: 0 6px 15px rgba(108, 99, 255, 0.25);
            transform: translateY(-2px);
        }
        
        .btn-success {
            background-color: var(--success);
            border-color: var(--success);
            box-shadow: 0 4px 12px rgba(54, 179, 126, 0.2);
        }
        
        .btn-success:hover, .btn-success:focus {
            background-color: #2EAA75;
            border-color: #2EAA75;
            box-shadow: 0 6px 15px rgba(54, 179, 126, 0.25);
            transform: translateY(-2px);
        }
        
        .btn-outline-secondary {
            color: var(--gray-700);
            border-color: var(--border-color);
            background-color: var(--white);
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--secondary);
            color: var(--primary);
            border-color: var(--border-color);
            transform: translateY(-2px);
        }
        
        .btn i {
            margin-right: 8px;
            font-size: 0.9rem;
        }
        
        /* Pagination */
        .pagination {
            margin-bottom: 0;
        }
        
        .page-link {
            color: var(--primary);
            border-color: var(--border-color);
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            margin: 0 3px;
            border-radius: 8px;
        }
        
        .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(108, 99, 255, 0.25);
        }
        
        /* ส่วนท้ายตาราง */
        .card-footer {
            background-color: var(--white);
            border-top: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            font-size: 0.9rem;
        }
        
        /* Alert custom styling */
        .alert {
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            border: none;
        }
        
        .alert-primary {
            background-color: rgba(108, 99, 255, 0.08);
            color: var(--primary-dark);
        }
        
        .alert-success {
            background-color: rgba(54, 179, 126, 0.08);
            color: #2EAA75;
        }
        
        .alert-info {
            background-color: rgba(94, 106, 132, 0.08);
            color: #4E5A70;
        }
        
        /* ทำให้เว็บ responsive */
        @media (max-width: 992px) {
            .container-fluid {
                padding: 1.5rem;
            }
            
            .stats-value {
                font-size: 2rem;
            }
            
            .card {
                margin-bottom: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .container-fluid {
                padding: 1rem;
            }
            
            .main-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .main-actions {
                margin-top: 1rem;
                width: 100%;
            }
            
            .table {
                font-size: 0.85rem;
            }
            
            .table th, .table td {
                padding: 1rem 0.75rem;
            }
            
            .btn {
                font-size: 0.9rem;
                padding: 0.6rem 1rem;
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }
        
        /* เปลี่ยนธีมของกราฟ */
        canvas {
            padding: 1rem;
        }
        
        /* เอฟเฟกต์เพิ่มเติม */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 3rem 0;
        }
        
        .empty-state i {
            font-size: 3.5rem;
            color: var(--gray-300);
            margin-bottom: 1.5rem;
        }
        
        .empty-state h5 {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--gray-700);
        }
        
        .empty-state p {
            color: var(--gray-500);
            max-width: 300px;
            text-align: center;
        }
		
		/*Pointer cursor ของ Popover*/
			.device-info-container {
				cursor: pointer;
				position: relative;
				padding: 4px 8px;
				border-radius: 4px;
				transition: background-color 0.2s;
			}

			.device-info-container:hover {
				background-color: rgba(108, 99, 255, 0.05);
			}

			/* ปรับแต่ง Popover */
			.popover {
				max-width: 500px;
				box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
				border-radius: 12px;
				border: 1px solid rgba(108, 99, 255, 0.2);
			}

			.popover-header {
				background-color: rgba(108, 99, 255, 0.05);
				border-bottom: 1px solid rgba(108, 99, 255, 0.1);
				color: var(--dark);
				font-weight: 600;
				padding: 12px 15px;
				border-top-left-radius: 12px;
				border-top-right-radius: 12px;
			}

			.popover-body {
				padding: 15px;
			}

			.popover-body pre {
				margin-bottom: 0;
				background-color: rgba(0, 0, 0, 0.02);
				padding: 10px;
				border-radius: 8px;
				border: 1px solid rgba(0, 0, 0, 0.05);
			}
			.device-info-container {
			cursor: pointer;
			position: relative;
			padding: 4px 8px;
			border-radius: 4px;
			transition: background-color 0.2s;
			outline: none; /* ลบเส้น outline เมื่อ focus */
			}

			.device-info-container:hover, .device-info-container:focus {
				background-color: rgba(108, 99, 255, 0.05);
			}

			/* ปรับแต่ง Copy Button ใน Popover */
			.popover-content-container {
				position: relative;
			}

			.copy-btn {
				display: block;
				width: 100%;
				margin-top: 8px;
				transition: all 0.2s ease;
			}

			.copy-btn:focus {
				box-shadow: none;
				outline: none;
			}

			.popover-body {
				padding: 15px;
				max-width: 400px;
			}

			.popover-body pre {
				margin-bottom: 0;
				background-color: rgba(0, 0, 0, 0.02);
				padding: 10px;
				border-radius: 8px;
				border: 1px solid rgba(0, 0, 0, 0.05);
				max-height: 200px;
				overflow-y: auto;
				white-space: pre-wrap;
				word-break: break-word;
			}
		
			.custom-gray {
				color: #6c757d;
				border-color: #d0d0d0;
			}
			.custom-gray:hover {
				color: #2F4F4F;
				background-color: #f8f9fa;
				border-color: #c0c0c0;
			}
    </style>
		<meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net/; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net/; img-src 'self' data:;">
</head>
<body>

<div class="container-fluid">
    <div class="main-header">
        <div>
            <h1 class="main-title">ระบบติดตามกิจกรรมผู้ใช้</h1>
            <p class="text-muted mb-0">ตรวจสอบและวิเคราะห์กิจกรรมผู้ใช้งานในระบบ</p>
        </div>
        <div class="main-actions">
			<!-- เพิ่มปุ่มเข้าหน้าจัดการการแจ้งเตือน -->
			<a href="<?php echo base_url('Email_register'); ?>" class="btn btn-primary me-2">
    			<i class="fas fa-bell me-1"></i>จัดการการแจ้งเตือน
			</a>
            <a href="<?php echo base_url('User_log_backend/export_csv'); ?>" class="btn btn-success" id="exportBtn">
                <i class="fas fa-file-excel"></i>ส่งออกรายงาน
            </a>
        </div>
    </div>
    
    <!-- สรุปภาพรวม -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="stats-icon primary">
                        <i class="fas fa-history"></i>
                    </div>
                    <h2 class="stats-value">
                        <?php echo $this->db->count_all('tbl_member_activity_logs'); ?>
                    </h2>
                    <p class="stats-label">กิจกรรมทั้งหมด</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="stats-icon success">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <?php 
                    $this->db->where('activity_type', 'login');
                    $login_count = $this->db->count_all_results('tbl_member_activity_logs');
                    ?>
                    <h2 class="stats-value"><?php echo $login_count; ?></h2>
                    <p class="stats-label">การเข้าสู่ระบบ</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="stats-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <?php 
                    $this->db->where('activity_type', 'failed');
                    $failed_count = $this->db->count_all_results('tbl_member_activity_logs');
                    ?>
                    <h2 class="stats-value"><?php echo $failed_count; ?></h2>
                    <p class="stats-label">เข้าสู่ระบบล้มเหลว</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="stats-icon info">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <?php 
                    $this->db->select('user_id');
                    $this->db->where('activity_type', 'logout');
                    $logout_count = $this->db->count_all_results('tbl_member_activity_logs');
                    ?>
                    <h2 class="stats-value"><?php echo $logout_count; ?></h2>
                    <p class="stats-label">ออกจากระบบ</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- กราฟสถิติ -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie"></i>ประเภทกิจกรรม
                    </h5>
                </div>
                <div class="card-body p-3">
                    <canvas id="activityTypeChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
<!-- แล้วแทนที่ด้วยโค้ดนี้: -->
<div class="col-lg-6">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-user"></i>ผู้ใช้เข้าสู่ระบบบ่อยที่สุด
            </h5>
        </div>
        <div class="card-body p-3">
            <canvas id="userLoginChart" height="250"></canvas>
        </div>
    </div>
    </div>

<!-- กราฟแสดงการเข้าสู่ระบบรายวัน -->
<div class="row g-3 mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="fas fa-chart-line"></i>การเข้าสู่ระบบรายวัน
                </h5>
                <div class="d-flex align-items-center">
                    <div class="input-group me-2" style="width: 150px;">
                        <select id="monthSelector" class="form-select form-select-sm">
                            <option value="1">มกราคม</option>
                            <option value="2">กุมภาพันธ์</option>
                            <option value="3">มีนาคม</option>
                            <option value="4">เมษายน</option>
                            <option value="5">พฤษภาคม</option>
                            <option value="6">มิถุนายน</option>
                            <option value="7">กรกฎาคม</option>
                            <option value="8">สิงหาคม</option>
                            <option value="9">กันยายน</option>
                            <option value="10">ตุลาคม</option>
                            <option value="11">พฤศจิกายน</option>
                            <option value="12">ธันวาคม</option>
                        </select>
                    </div>
                    <div class="input-group me-2" style="width: 120px;">
                        <select id="yearSelector" class="form-select form-select-sm">
                            <?php 
                                $current_year = date('Y')+543; // ปีปัจจุบันเป็น พ.ศ.
                                for($i = $current_year; $i >= $current_year-5; $i--) {
                                    echo "<option value='".($i-543)."'>".$i."</option>";
                                }
                            ?>
                        </select>
                    </div>
					<button id="todayBtn" class="btn btn-sm btn-outline-secondary custom-gray">
						<i class="fas fa-calendar-day me-1"></i>ปัจจุบัน
					</button>
                </div>
            </div>
            <div style="height: 400px; position: relative;">
                <canvas id="dailyLoginChart"></canvas>
            </div>
        </div>
    </div>
</div>	
		
		
    <!-- ส่วนค้นหาและกรอง -->
    <div class="search-box mb-4">
        <div class="row g-3">
            <div class="col-lg-12">
                <form action="<?php echo base_url('user_log_backend'); ?>" method="get" class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="fas fa-search search-icon"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="ค้นหาด้วยชื่อผู้ใช้, กิจกรรม, IP" value="<?php echo $search; ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="activity_type" class="form-select">
                            <option value="">-- ทุกประเภทกิจกรรม --</option>
                            <option value="login" <?php echo ($activity_type == 'login') ? 'selected' : ''; ?>>เข้าสู่ระบบ</option>
                            <option value="logout" <?php echo ($activity_type == 'logout') ? 'selected' : ''; ?>>ออกจากระบบ</option>
                            <option value="failed" <?php echo ($activity_type == 'failed') ? 'selected' : ''; ?>>เข้าสู่ระบบล้มเหลว</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="fas fa-calendar-alt search-icon"></i>
                            </span>
                            <input type="date" name="start_date" class="form-control border-start-0" placeholder="จากวันที่" value="<?php echo $start_date; ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="fas fa-calendar-alt search-icon"></i>
                            </span>
                            <input type="date" name="end_date" class="form-control border-start-0" placeholder="ถึงวันที่" value="<?php echo $end_date; ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>ค้นหา
                        </button>
                    </div>
                    <div class="col-md-1">
                        <a href="<?php echo base_url('User_log_backend'); ?>" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- พื้นที่แสดงข้อความการค้นหา -->
    <div id="searchInfoContainer"></div>
    
    <!-- ตารางกิจกรรม -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">
                <i class="fas fa-list-alt"></i>รายการกิจกรรมผู้ใช้
            </h5>
            <span class="badge bg-primary">
                <?php echo $this->db->count_all('tbl_member_activity_logs'); ?> รายการ
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ผู้ใช้</th>
                            <th>กิจกรรม</th>
                            <th>รายละเอียด</th>
                            <th>IP Address</th>
                            <th>อุปกรณ์</th>
                            <th>เวลา</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($activities)): ?>
                            <?php foreach($activities as $activity): ?>
                                <?php 
                                   $device_info = !empty($activity->device_info) ? json_decode($activity->device_info, true) : [];
                                    
                                    // กำหนด badge ตามประเภทกิจกรรม
                                    $badge_class = '';
                                    $badge_text = $activity->activity_type;
                                    
                                    switch($activity->activity_type) {
                                        case 'login':
                                            $badge_class = 'badge bg-success';
                                            $badge_text = 'เข้าสู่ระบบ';
                                            break;
                                        case 'logout':
                                            $badge_class = 'badge bg-info';
                                            $badge_text = 'ออกจากระบบ';
                                            break;
                                        case 'failed':
                                            $badge_class = 'badge bg-danger';
                                            $badge_text = 'เข้าสู่ระบบล้มเหลว';
                                            break;
                                        default:
                                            $badge_class = 'badge bg-secondary';
                                    }
                                    
                                    // กำหนด icon ตามประเภทอุปกรณ์
                                    $device_icon = '<i class="fas fa-desktop device-icon"></i>';
                                    if(isset($device_info['type'])) {
                                        if($device_info['type'] == 'Mobile') {
                                            $device_icon = '<i class="fas fa-mobile-alt device-icon"></i>';
                                        } else if($device_info['type'] == 'Tablet') {
                                            $device_icon = '<i class="fas fa-tablet-alt device-icon"></i>';
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><?php echo $activity->id; ?></td>
                                    <td>
                                        <?php if($activity->user_id > 0): ?>
                                            <strong><?php echo $activity->full_name; ?></strong><br>
                                            <small class="text-muted">@<?php echo $activity->username; ?></small>
                                        <?php else: ?>
                                            <span class="text-danger"><?php echo $activity->username; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="<?php echo $badge_class; ?>"><?php echo $badge_text; ?></span></td>
                                    <td><?php echo $activity->activity_description; ?></td>
                                    <td>
                                        <span class="d-flex align-items-center">
                                            <i class="fas fa-network-wired me-2 text-primary"></i>
                                            <?php echo $activity->ip_address; ?>
                                        </span>
                                    </td>
																			<td>
											<!-- แสดงผล Device info-->
											<?php echo $device_icon; ?>
											<?php 
											if(isset($device_info['device']) && isset($device_info['browser'])) {
												// ลดขนาด JSON และทำให้แน่ใจว่า escape character ถูกจัดการอย่างเหมาะสม
												$formatted_json = json_encode($device_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
												$escaped_json = htmlspecialchars($formatted_json, ENT_QUOTES, 'UTF-8');

												// สร้าง unique ID สำหรับแต่ละแถว เพื่อให้แน่ใจว่า JS สามารถอ้างถึง element ได้อย่างถูกต้อง
												$unique_id = 'device_info_' . $activity->id;

												// ใช้ data-* attribute แทนการกำหนด content โดยตรง
												echo '<div class="device-info-container" 
														 data-bs-toggle="popover" 
														 data-bs-trigger="focus" 
														 data-bs-placement="right" 
														 data-bs-html="true" 
														 title="ข้อมูลอุปกรณ์ทั้งหมด" 
														 tabindex="0"
														 id="' . $unique_id . '"
														 data-content-id="' . $unique_id . '_content"
														 data-json="' . base64_encode($formatted_json) . '">';

												// แสดงข้อมูลพื้นฐาน
												echo '<div><strong>' . $device_info['type'] . ' - ' . $device_info['device'] . '</strong></div>';
												echo '<div class="small text-muted">';
												echo '<i class="fas fa-browser me-1"></i>' . $device_info['browser'] . ' ' . $device_info['browser_version'];

												// แสดงความละเอียดหน้าจอถ้ามี
												if(isset($device_info['screen_resolution'])) {
													echo ' <span class="ms-2"><i class="fas fa-desktop me-1"></i>' . $device_info['screen_resolution'] . '</span>';
												}

												// แสดงโซนเวลาในแบบย่อถ้ามี
												if(isset($device_info['timezone'])) {
													echo ' <span class="ms-2"><i class="fas fa-globe me-1"></i>' . $device_info['timezone'] . '</span>';
												}

												echo '</div>';
												echo '</div>'; // ปิด device-info-container

												// สร้างเนื้อหาซ่อนไว้ใน DOM
												echo '<div id="' . $unique_id . '_content" class="d-none">
														<div class="popover-content-container">
															<pre style="max-height:300px;overflow-y:auto;white-space:pre-wrap;font-size:12px;">' . $escaped_json . '</pre>
															<button class="btn btn-xs btn-sm btn-dark copy-btn mt-1" style="font-size:11px;padding:3px 8px;background-color:#454F5B;" 
																	onclick="copyToClipboard(this, event)" 
																	data-copy="' . $escaped_json . '">
																<i class="fas fa-copy me-1"></i>คัดลอก
															</button>
														</div>
													  </div>';
											} else {
												echo 'ไม่มีข้อมูล';
											}
											?>
										</td>
                                    <td class="activity-time">
                                        <i class="far fa-clock me-1"></i>
                                        <?php
                                            $date = new DateTime($activity->created_at);
                                            echo $date->format('d/m/Y H:i:s');
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-search"></i>
                                        <h5>ไม่พบข้อมูลกิจกรรมผู้ใช้</h5>
                                        <p>ลองค้นหาด้วยคำค้นหาอื่น หรือล้างตัวกรอง</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">แสดง <span class="fw-bold"><?php echo count($activities); ?></span> รายการ จากทั้งหมด <span class="fw-bold"><?php echo $this->db->count_all('tbl_member_activity_logs'); ?></span> รายการ</small>
                </div>
                <div>
                    <?php echo $pagination; ?>
                </div>
            </div>
        </div>
    </div>
</div>

Insert Script
<!-- Bootstrap JS และ dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- กราฟและการส่งออก -->

<script>
// ส่งตัวแปรจาก PHP ไปยัง JavaScript
var failed_count = <?php echo $this->db->where('activity_type', 'failed')->count_all_results('tbl_member_activity_logs'); ?>;
var login_count = <?php echo $this->db->where('activity_type', 'login')->count_all_results('tbl_member_activity_logs'); ?>;
var logout_count = <?php echo $this->db->where('activity_type', 'logout')->count_all_results('tbl_member_activity_logs'); ?>;

// เปลี่ยน userLoginCounts เป็น userLoginData ให้ตรงกับที่ใช้ในไฟล์ JS
var userLabels = <?php echo isset($userLabels) ? $userLabels : '[]'; ?>;
var userLoginData = <?php echo isset($userLoginCounts) ? $userLoginCounts : '[]'; ?>;

// ตั้งค่า base URL
var baseUrl = '<?php echo base_url(); ?>';
</script>
</body>
</html>