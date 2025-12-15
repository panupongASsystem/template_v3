<!-- *** Loading Modal - Apple Style (White Background) *** -->
<div id="dashboardLoadingModal" class="loading-modal">
    <div class="loading-modal-content">
        <div class="loading-animation">
            <div class="apple-spinner">
                <div class="spinner-segment"></div>
                <div class="spinner-segment"></div>
                <div class="spinner-segment"></div>
                <div class="spinner-segment"></div>
                <div class="spinner-segment"></div>
                <div class="spinner-segment"></div>
                <div class="spinner-segment"></div>
                <div class="spinner-segment"></div>
                <div class="spinner-segment"></div>
                <div class="spinner-segment"></div>
                <div class="spinner-segment"></div>
                <div class="spinner-segment"></div>
            </div>
        </div>
        <div class="loading-text">
            <h3 id="loadingTitle">กำลังเตรียมข้อมูล</h3>
            <p id="loadingDescription">กำลังโหลดระบบรายงาน...</p>
            <div class="loading-progress">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <span class="progress-text" id="progressText">0%</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="index-container" id="mainContent" style="opacity: 0; transform: translateY(20px);">
    
    <!-- *** แถวที่ 1: สถานะระบบ + พื้นที่จัดเก็บข้อมูล *** -->
    <div class="summary-cards-large">
        <!-- System Status Card -->
        <div class="summary-card-large">
            <div class="summary-card-header system-header">
                <div class="summary-icon system">
                    <i class="fas fa-server"></i>
                </div>
                <div class="summary-title">
                    <i class="fas fa-cogs me-2"></i>
                    สถานะระบบ
                </div>
            </div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value" style="color: var(--success-color-dark);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-label">ออนไลน์</div>
                    <div class="stat-description">ระบบทำงานปกติ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">99.9%</div>
                    <div class="stat-label">Uptime</div>
                    <div class="stat-description">เวลาทำงานของระบบ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo date('d'); ?></div>
                    <div class="stat-label">วันที่</div>
                    <div class="stat-description">วันที่ปัจจุบัน</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo date('m'); ?></div>
                    <div class="stat-label">เดือน</div>
                    <div class="stat-description">เดือนปัจจุบัน</div>
                </div>
            </div>
        </div>
		
        <!-- Storage Summary -->
        <a href="<?php echo site_url('System_reports/storage'); ?>" class="summary-card-large clickable-card">
            <div class="summary-card-header storage-header">
                <div class="summary-icon storage">
                    <i class="fas fa-hdd"></i>
                </div>
                <div class="summary-title">
                    <i class="fas fa-database me-2"></i>
                    พื้นที่จัดเก็บข้อมูล
                </div>
            </div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value" id="storage-percentage"><?php echo number_format($reports_summary['storage']['percentage'] ?? 0, 1); ?>%</div>
                    <div class="stat-label">ใช้งานแล้ว</div>
                    <div class="stat-description">% ของพื้นที่ทั้งหมด</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="storage-free"><?php echo number_format($reports_summary['storage']['free'] ?? 0, 1); ?></div>
                    <div class="stat-label">GB ว่าง</div>
                    <div class="stat-description">พื้นที่ที่เหลือ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="storage-used"><?php echo number_format($reports_summary['storage']['used'] ?? 0, 1); ?></div>
                    <div class="stat-label">GB ใช้งาน</div>
                    <div class="stat-description">พื้นที่ที่ใช้ไปแล้ว</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="storage-total"><?php echo number_format($reports_summary['storage']['total'] ?? 0, 1); ?></div>
                    <div class="stat-label">GB ทั้งหมด</div>
                    <div class="stat-description">ขนาดพื้นที่รวม</div>
                </div>
            </div>
        </a>   
    </div>

    <!-- *** แถวที่ 2: การจองคิว + แจ้งเรื่อง/ร้องเรียน *** -->
    <div class="summary-cards-large">
        <!-- Queue Statistics Card -->
        <a href="<?php echo site_url('Queue/queue_report'); ?>" class="summary-card-large clickable-card queue-card">
            <div class="summary-card-header queue-header">
                <div class="summary-icon queue">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="summary-title">
                    <i class="fas fa-users me-2"></i>
                    จองคิวติดต่อราชการ
                </div>
            </div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value" id="queue-total"><?php echo number_format($reports_summary['queue_stats']['total'] ?? 0); ?></div>
                    <div class="stat-label">ทั้งหมด</div>
                    <div class="stat-description">คิวรวมในระบบ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="queue-pending" style="color: #ff9800;"><?php echo number_format($reports_summary['queue_stats']['pending'] ?? 0); ?></div>
                    <div class="stat-label">รอยืนยัน</div>
                    <div class="stat-description">รอการอนุมัติ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="queue-progress" style="color: #2196f3;"><?php echo number_format($reports_summary['queue_stats']['in_progress'] ?? 0); ?></div>
                    <div class="stat-label">ดำเนินการ</div>
                    <div class="stat-description">อยู่ระหว่างดำเนินการ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="queue-completed" style="color: var(--success-color-dark);"><?php echo number_format($reports_summary['queue_stats']['completed'] ?? 0); ?></div>
                    <div class="stat-label">เสร็จสิ้น</div>
                    <div class="stat-description">ดำเนินการเรียบร้อย</div>
                </div>
            </div>
        </a>

        <!-- Complains Summary -->
        <a href="<?php echo site_url('System_reports/complain'); ?>" class="summary-card-large clickable-card">
            <div class="summary-card-header complain-header">
                <div class="summary-icon complain">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="summary-title">
                    <i class="fas fa-clipboard-list me-2"></i>
                    แจ้งเรื่อง/ร้องเรียน
                </div>
            </div>
            <div class="summary-stats-5">
                <div class="stat-item">
                    <div class="stat-value" id="complains-total"><?php echo number_format($reports_summary['complains']['total'] ?? 0); ?></div>
                    <div class="stat-label">ทั้งหมด</div>
                    <div class="stat-description">เรื่องร้องเรียนรวม</div>
                </div>
				
				<div class="stat-item">
            <div class="stat-value" id="complains-waiting" style="color: #ff9800;"><?php echo number_format($reports_summary['complains']['waiting'] ?? 0); ?></div>
            <div class="stat-label">รอรับเรื่อง</div>
            <div class="stat-description">รอการรับเรื่อง</div>
        </div>
				
                <div class="stat-item">
                    <div class="stat-value" id="complains-pending"><?php echo number_format($reports_summary['complains']['pending'] ?? 0); ?></div>
                    <div class="stat-label">รอดำเนินการ</div>
                    <div class="stat-description">ยังไม่ได้แก้ไข</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="complains-in-progress"><?php echo number_format($reports_summary['complains']['in_progress'] ?? 0); ?></div>
                    <div class="stat-label">กำลังดำเนินการ</div>
                    <div class="stat-description">อยู่ระหว่างแก้ไข</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="complains-completed"><?php echo number_format($reports_summary['complains']['completed'] ?? 0); ?></div>
                    <div class="stat-label">เสร็จสิ้น</div>
                    <div class="stat-description">แก้ไขเรียบร้อย</div>
                </div>
            </div>
        </a>
    </div>  

    <!-- *** แถวที่ 3: สถิติเว็บไซต์ + ความคิดเห็นจากประชาชน *** -->
    <div class="summary-cards-large">
        <!-- Website Statistics Summary -->
        <a href="<?php echo site_url('System_reports/website_stats'); ?>" class="summary-card-large clickable-card">
            <div class="summary-card-header web-stats-header">
                <div class="summary-icon web-stats">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="summary-title">
                    <i class="fas fa-globe me-2"></i>
                    สถิติการใช้งานเว็บไซต์
                </div>
            </div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value" id="web-pageviews"><?php echo number_format($reports_summary['web_stats']['total_pageviews'] ?? 0); ?></div>
                    <div class="stat-label">การเข้าชม (7 วัน)</div>
                    <div class="stat-description">จำนวนหน้าเว็บที่ถูกเปิดดู</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="web-visitors"><?php echo number_format($reports_summary['web_stats']['total_visitors'] ?? 0); ?></div>
                    <div class="stat-label">ผู้เยี่ยมชม</div>
                    <div class="stat-description">คนที่เข้ามาดู (ไม่นับซ้ำ)</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="web-online"><?php echo number_format($reports_summary['web_stats']['online_users'] ?? 0); ?></div>
                    <div class="stat-label">ออนไลน์</div>
                    <div class="stat-description">ผู้ใช้ที่เข้าชมอยู่ตอนนี้</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="web-avg-pages"><?php echo number_format($reports_summary['web_stats']['avg_pages_per_visitor'] ?? 0, 2); ?></div>
                    <div class="stat-label">เฉลี่ย/คน</div>
                    <div class="stat-description">หน้าที่ดูต่อคน</div>
                </div>
            </div>
        </a>

        <!-- Suggestions Card -->
        <a href="<?php echo site_url('Suggestions/suggestions_report'); ?>" class="summary-card-large clickable-card suggestions-card">
            <div class="summary-card-header suggestions-header">
                <div class="summary-icon suggestions">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="summary-title">
                    <i class="fas fa-comments me-2"></i>
                    รับฟังความคิดเห็น เรื่องเสนอแนะ จากประชาชน
                </div>
            </div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value" id="suggestions-total"><?php echo number_format($reports_summary['suggestions']['total'] ?? 0); ?></div>
                    <div class="stat-label">ทั้งหมด</div>
                    <div class="stat-description">ความคิดเห็นรวม</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="suggestions-new" style="color: #2196f3;"><?php echo number_format($reports_summary['suggestions']['new'] ?? 0); ?></div>
                    <div class="stat-label">ใหม่</div>
                    <div class="stat-description">ข้อเสนอแนะใหม่</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="suggestions-reviewed" style="color: #ff9800;"><?php echo number_format($reports_summary['suggestions']['reviewed'] ?? 0); ?></div>
                    <div class="stat-label">รับเรื่องแล้ว</div>
                    <div class="stat-description">รับเรื่องเสนอแนะแล้ว</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="suggestions-implemented" style="color: var(--success-color-dark);"><?php echo number_format($reports_summary['suggestions']['implemented'] ?? 0); ?></div>
                    <div class="stat-label">นำไปใช้</div>
                    <div class="stat-description">นำไปปรับปรุงแล้ว</div>
                </div>
            </div>
        </a>
    </div>

    <!-- *** แถวที่ 4: เบี้ยยังชีพและเงินอุดหนุนเด็ก *** -->
    <div class="summary-cards-large">
        <!-- Elder & Disability Allowance Card -->
        <a href="<?php echo site_url('Elderly_aw_ods/elderly_aw_ods'); ?>" class="summary-card-large clickable-card elder-allowance-card">
            <div class="summary-card-header elder-allowance-header">
                <div class="summary-icon elder-allowance">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div class="summary-title">
                    <i class="fas fa-wheelchair me-2"></i>
                    เอกสารยื่นขอเบี้ยยังชีพผู้สูงอายุ/พิการ
                </div>
            </div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value" id="elder-allowance-total"><?php echo number_format($reports_summary['elder_allowance']['total'] ?? 0); ?></div>
                    <div class="stat-label">ทั้งหมด</div>
                    <div class="stat-description">คำขอรวมในระบบ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="elder-allowance-submitted" style="color: #ff9800;"><?php echo number_format($reports_summary['elder_allowance']['submitted'] ?? 0); ?></div>
                    <div class="stat-label">ยื่นเรื่องแล้ว</div>
                    <div class="stat-description">รอการตรวจสอบ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="elder-allowance-reviewing" style="color: #2196f3;"><?php echo number_format($reports_summary['elder_allowance']['reviewing'] ?? 0); ?></div>
                    <div class="stat-label">กำลังพิจารณา</div>
                    <div class="stat-description">อยู่ระหว่างดำเนินการ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="elder-allowance-completed" style="color: var(--success-color-dark);"><?php echo number_format($reports_summary['elder_allowance']['completed'] ?? 0); ?></div>
                    <div class="stat-label">เสร็จสิ้น</div>
                    <div class="stat-description">ดำเนินการเรียบร้อย</div>
                </div>
            </div>
        </a>

        <!-- Child Birth Allowance Card -->
        <a href="<?php echo site_url('Kid_aw_ods/kid_aw_ods'); ?>" class="summary-card-large clickable-card child-allowance-card">
            <div class="summary-card-header child-allowance-header">
                <div class="summary-icon child-allowance">
                    <i class="fas fa-baby"></i>
                </div>
                <div class="summary-title">
                    <i class="fas fa-gift me-2"></i>
                    เอกสารยื่นขอเงินอุดหนุนเด็กแรกเกิด
                </div>
            </div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value" id="child-allowance-total"><?php echo number_format($reports_summary['child_allowance']['total'] ?? 0); ?></div>
                    <div class="stat-label">ทั้งหมด</div>
                    <div class="stat-description">คำขอรวมในระบบ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="child-allowance-submitted" style="color: #ff9800;"><?php echo number_format($reports_summary['child_allowance']['submitted'] ?? 0); ?></div>
                    <div class="stat-label">ยื่นเรื่องแล้ว</div>
                    <div class="stat-description">รอการตรวจสอบ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="child-allowance-reviewing" style="color: #2196f3;"><?php echo number_format($reports_summary['child_allowance']['reviewing'] ?? 0); ?></div>
                    <div class="stat-label">กำลังพิจารณา</div>
                    <div class="stat-description">อยู่ระหว่างดำเนินการ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="child-allowance-completed" style="color: var(--success-color-dark);"><?php echo number_format($reports_summary['child_allowance']['completed'] ?? 0); ?></div>
                    <div class="stat-label">เสร็จสิ้น</div>
                    <div class="stat-description">ดำเนินการเรียบร้อย</div>
                </div>
            </div>
        </a>
    </div>

    <!-- *** แถวที่ 5: ยื่นเอกสารออนไลน์ + แจ้งเรื่องทุจริต (ตรวจสอบสิทธิ์) *** -->
    <div class="summary-cards-large" id="row-5-cards">
        <!-- ESV Online Document Submission Card -->
        <a href="<?php echo site_url('Esv_ods/admin_management'); ?>" class="summary-card-large clickable-card esv-document-card">
            <div class="summary-card-header esv-document-header">
                <div class="summary-icon esv-document">
                    <i class="fas fa-file-upload"></i>
                </div>
                <div class="summary-title">
                    <i class="fas fa-cloud-upload-alt me-2"></i>
                    ระบบยื่นเอกสารออนไลน์
                </div>
            </div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value" id="esv-documents-total"><?php echo number_format($reports_summary['esv_documents']['total'] ?? 0); ?></div>
                    <div class="stat-label">ทั้งหมด</div>
                    <div class="stat-description">เอกสารรวมในระบบ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="esv-documents-pending" style="color: #ff9800;"><?php echo number_format($reports_summary['esv_documents']['pending'] ?? 0); ?></div>
                    <div class="stat-label">รอดำเนินการ</div>
                    <div class="stat-description">รอการตรวจสอบ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="esv-documents-processing" style="color: #2196f3;"><?php echo number_format($reports_summary['esv_documents']['processing'] ?? 0); ?></div>
                    <div class="stat-label">กำลังดำเนินการ</div>
                    <div class="stat-description">อยู่ระหว่างดำเนินการ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="esv-documents-completed" style="color: var(--success-color-dark);"><?php echo number_format($reports_summary['esv_documents']['completed'] ?? 0); ?></div>
                    <div class="stat-label">เสร็จสิ้น</div>
                    <div class="stat-description">ดำเนินการเรียบร้อย</div>
                </div>
            </div>
        </a>

        <!-- *** Anti-Corruption Report Card - แจ้งเรื่องทุจริต (เฉพาะสิทธิ์) *** -->
        <!-- *** Anti-Corruption Report Card - แจ้งเรื่องทุจริต (เฉพาะสิทธิ์) *** -->
        <?php 
        // ตรวจสอบสิทธิ์การเข้าถึงการ์ด "แจ้งเรื่องทุจริต"
        $show_corruption_card = false;
        
        // ตรวจสอบ session หรือ user data
        if ($this->session->userdata('m_system')) {
            $user_system = $this->session->userdata('m_system');
            $grant_user_ref_id = $this->session->userdata('grant_user_ref_id');
            
            // เงื่อนไขการแสดงการ์ด
            if ($user_system === 'system_admin' || 
                $user_system === 'super_admin') {
                $show_corruption_card = true;
            } elseif ($user_system === 'user_admin' && !empty($grant_user_ref_id)) {
                // 🔥 แก้ไข: ใช้ logic เดียวกับ Controller
                $grant_ids = explode(',', $grant_user_ref_id);
                $grant_ids = array_map('trim', $grant_ids);
                
                // เช็คเฉพาะว่ามี "107" ใน array หรือไม่
                if (in_array('107', $grant_ids)) {
                    $show_corruption_card = true;
                }
            }
        }
        
        if ($show_corruption_card): ?>
        <a href="<?php echo site_url('Corruption/admin_management'); ?>" class="summary-card-large clickable-card corruption-report-card">
            <div class="summary-card-header corruption-report-header">
                <div class="summary-icon corruption-report" style="position: relative;">
                    <i class="fas fa-shield-alt"></i>
                    <span class="corruption-notification-badge" style="display: none;">0</span>
                </div>
                <div class="summary-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    แจ้งเรื่องทุจริต
                    <span class="admin-only-badge ms-2">เฉพาะผู้ดูแล</span>
                </div>
            </div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value corruption-stat-total" id="corruption-reports-total"><?php echo number_format($reports_summary['corruption_reports']['total'] ?? 0); ?></div>
                    <div class="stat-label">ทั้งหมด</div>
                    <div class="stat-description">รายงานทุจริตรวม</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value corruption-stat-pending" id="corruption-reports-pending"><?php echo number_format($reports_summary['corruption_reports']['pending'] ?? 0); ?></div>
                    <div class="stat-label">รอดำเนินการ</div>
                    <div class="stat-description">รอการตรวจสอบ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value corruption-stat-in-progress" id="corruption-reports-in-progress"><?php echo number_format($reports_summary['corruption_reports']['in_progress'] ?? 0); ?></div>
                    <div class="stat-label">ดำเนินการแล้ว</div>
                    <div class="stat-description">อยู่ระหว่างดำเนินการ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value corruption-stat-closed" id="corruption-reports-closed"><?php echo number_format($reports_summary['corruption_reports']['closed'] ?? 0); ?></div>
                    <div class="stat-label">ปิดเรื่อง</div>
                    <div class="stat-description">ดำเนินการเรียบร้อย</div>
                </div>
            </div>
        </a>
        <?php else: ?>
        <!-- *** Placeholder Card สำหรับผู้ใช้ที่ไม่มีสิทธิ์ *** -->
        <div class="summary-card-large placeholder-card">
            <div class="summary-card-header placeholder-header">
                <div class="summary-icon placeholder">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="summary-title">
                    <i class="fas fa-info-circle me-2"></i>
                    ข้อมูลเฉพาะผู้ดูแลระบบ
                </div>
            </div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value" style="color: #999;">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="stat-label">ไม่มีสิทธิ์</div>
                    <div class="stat-description">ข้อมูลนี้เฉพาะผู้ดูแลระบบ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" style="color: #999;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="stat-label">ป้องกัน</div>
                    <div class="stat-description">ข้อมูลได้รับการปกป้อง</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" style="color: #999;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stat-label">ความปลอดภัย</div>
                    <div class="stat-description">ข้อมูลละเอียดอ่อน</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" style="color: #999;">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div class="stat-label">จำกัดสิทธิ์</div>
                    <div class="stat-description">เฉพาะผู้มีสิทธิ์เท่านั้น</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
		
		
		
    </div>
	
	
	
	
	<!-- แถวที่ 6: แบบประเมินความพึงพอใจ + ข้อมูลสำรอง -->
<div class="summary-cards-large">
    <!-- Assessment Summary Card -->
    <a href="<?php echo site_url('System_reports/assessment_admin'); ?>" class="summary-card-large clickable-card assessment-card">
        <div class="summary-card-header assessment-header">
            <div class="summary-icon assessment">
                <i class="fas fa-poll"></i>
            </div>
            <div class="summary-title">
                <i class="fas fa-star me-2"></i>
                แบบประเมินความพึงพอใจการให้บริการ
            </div>
        </div>
        <div class="summary-stats">
            <div class="stat-item">
                <div class="stat-value" id="assessment-total"><?php echo number_format($reports_summary['assessment']['total'] ?? 0); ?></div>
                <div class="stat-label">ทั้งหมด</div>
                <div class="stat-description">ผู้ตอบแบบประเมิน</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" id="assessment-today" style="color: #2196f3;"><?php echo number_format($reports_summary['assessment']['today'] ?? 0); ?></div>
                <div class="stat-label">วันนี้</div>
                <div class="stat-description">ผู้ตอบวันนี้</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" id="assessment-score" style="color: #ff9800;"><?php echo number_format($reports_summary['assessment']['avg_score'] ?? 0, 2); ?></div>
                <div class="stat-label">คะแนนเฉลี่ย</div>
                <div class="stat-description">คะแนนโดยรวม/5.00</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" id="assessment-questions" style="color: var(--success-color-dark);"><?php echo number_format($reports_summary['assessment']['questions'] ?? 0); ?></div>
                <div class="stat-label">คำถาม</div>
                <div class="stat-description">จำนวนคำถาม</div>
            </div>
        </div>
    </a>

    <!-- Analytics Summary Card 
    <a href="<?php echo site_url('System_reports/analytics'); ?>" class="summary-card-large clickable-card analytics-card">
        <div class="summary-card-header analytics-header">
            <div class="summary-icon analytics">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="summary-title">
                <i class="fas fa-analytics me-2"></i>
                สถิติและการวิเคราะห์ระบบ
            </div>
        </div>
        <div class="summary-stats">
            <div class="stat-item">
                <div class="stat-value" id="analytics-reports"><?php echo number_format($reports_summary['analytics']['total_reports'] ?? 0); ?></div>
                <div class="stat-label">รายงาน</div>
                <div class="stat-description">รายงานทั้งหมด</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" id="analytics-active" style="color: #4caf50;"><?php echo number_format($reports_summary['analytics']['active_systems'] ?? 0); ?></div>
                <div class="stat-label">ระบบใช้งาน</div>
                <div class="stat-description">ระบบที่ใช้งานอยู่</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" id="analytics-performance" style="color: #2196f3;"><?php echo number_format($reports_summary['analytics']['performance'] ?? 95); ?>%</div>
                <div class="stat-label">ประสิทธิภาพ</div>
                <div class="stat-description">ประสิทธิภาพระบบ</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" id="analytics-satisfaction" style="color: #ff9800;"><?php echo number_format($reports_summary['analytics']['satisfaction'] ?? 0, 1); ?></div>
                <div class="stat-label">ความพึงพอใจ</div>
                <div class="stat-description">คะแนนพึงพอใจ</div>
            </div>
        </div>
    </a> -->
</div>

	
	

    <!-- Quick Actions -->
    <div class="mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            เครื่องมือระบบ
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6 col-sm-12">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="window.reportsIndex.refreshData()">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    รีเฟรชข้อมูลทั้งหมด
                                </button>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <button type="button" class="btn btn-outline-dark w-100" onclick="window.reportsIndex.showSystemInfo()">
                                    <i class="fas fa-info-circle me-2"></i>
                                    ข้อมูลระบบ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ CSS Styles -->


<style>
	
	
	body {
    padding-top: 30px !important;
}
	
	
/* Assessment Card Styles */
.assessment-header {
    background: linear-gradient(135deg, #e3f2fd 0%, #f0f8ff 100%);
}

.summary-icon.assessment {
    background: rgba(33, 150, 243, 0.15);
    color: #2196f3;
}

/* Analytics Card Styles */
.analytics-header {
    background: linear-gradient(135deg, #f3e5f5 0%, #fce4ec 100%);
}

.summary-icon.analytics {
    background: rgba(156, 39, 176, 0.15);
    color: #9c27b0;
}

/* เอฟเฟ็กต์พิเศษสำหรับการ์ดแบบประเมิน */
.assessment-card:hover .summary-icon.assessment {
    transform: scale(1.1) rotate(5deg);
    transition: transform 0.3s ease;
}

.analytics-card:hover .summary-icon.analytics {
    transform: scale(1.1) rotate(-5deg);
    transition: transform 0.3s ease;
}

/* Badge สำหรับคะแนนประเมิน */
.assessment-card .stat-value {
    position: relative;
}

.assessment-card .stat-item:nth-child(3) .stat-value::after {
    content: '/5.00';
    font-size: 12px;
    color: #999;
    margin-left: 2px;
}

/* Pulse Animation สำหรับข้อมูลใหม่ */
.assessment-card.new-data {
    animation: pulse-assessment 2s infinite;
}

@keyframes pulse-assessment {
    0% { 
        box-shadow: 0 6px 20px rgba(0,0,0,0.06); 
    }
    50% { 
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.2); 
    }
    100% { 
        box-shadow: 0 6px 20px rgba(0,0,0,0.06); 
    }
}

/* Loading state สำหรับ assessment card */
.assessment-card.loading::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 30px;
    height: 30px;
    margin: -15px 0 0 -15px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #2196f3;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    z-index: 10;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .assessment-card .summary-title,
    .analytics-card .summary-title {
        font-size: 16px;
    }
    
    .assessment-card .stat-value,
    .analytics-card .stat-value {
        font-size: 24px;
    }
}
</style>

<style>
	
	
	/* เพิ่ม CSS สำหรับ 5 กล่อง */
.summary-stats-5 {
    padding: 30px;
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
}

/* Responsive สำหรับ 5 กล่อง */
@media (max-width: 768px) {
    .summary-stats-5 {
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        padding: 25px;
    }
}

@media (max-width: 480px) {
    .summary-stats-5 {
        grid-template-columns: repeat(2, 1fr);
        padding: 20px;
    }
}
	
	
/* *** Apple-Style Loading Modal *** */
.loading-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    transition: all 0.5s ease;
}

.loading-modal-content {
    text-align: center;
    color: #333;
    max-width: 400px;
    padding: 40px;
}

.loading-animation {
    margin-bottom: 30px;
}

.apple-spinner {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    position: relative;
}

.spinner-segment {
    position: absolute;
    width: 6px;
    height: 20px;
    background: rgba(51, 51, 51, 0.2);
    border-radius: 3px;
    top: 0;
    left: 50%;
    margin-left: -3px;
    transform-origin: 3px 40px;
    animation: spinner-fade 1.2s infinite ease-in-out;
}

.spinner-segment:nth-child(1) { transform: rotate(0deg); animation-delay: -1.1s; }
.spinner-segment:nth-child(2) { transform: rotate(30deg); animation-delay: -1.0s; }
.spinner-segment:nth-child(3) { transform: rotate(60deg); animation-delay: -0.9s; }
.spinner-segment:nth-child(4) { transform: rotate(90deg); animation-delay: -0.8s; }
.spinner-segment:nth-child(5) { transform: rotate(120deg); animation-delay: -0.7s; }
.spinner-segment:nth-child(6) { transform: rotate(150deg); animation-delay: -0.6s; }
.spinner-segment:nth-child(7) { transform: rotate(180deg); animation-delay: -0.5s; }
.spinner-segment:nth-child(8) { transform: rotate(210deg); animation-delay: -0.4s; }
.spinner-segment:nth-child(9) { transform: rotate(240deg); animation-delay: -0.3s; }
.spinner-segment:nth-child(10) { transform: rotate(270deg); animation-delay: -0.2s; }
.spinner-segment:nth-child(11) { transform: rotate(300deg); animation-delay: -0.1s; }
.spinner-segment:nth-child(12) { transform: rotate(330deg); animation-delay: 0s; }

@keyframes spinner-fade {
    0%, 39%, 100% { opacity: 0.2; }
    40% { opacity: 1; }
}

.loading-text h3 {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
    opacity: 0;
    animation: fadeInUp 0.6s ease forwards;
    animation-delay: 0.3s;
}

.loading-text p {
    font-size: 16px;
    color: #666;
    margin-bottom: 30px;
    opacity: 0;
    animation: fadeInUp 0.6s ease forwards;
    animation-delay: 0.5s;
}

.loading-progress {
    opacity: 0;
    animation: fadeInUp 0.6s ease forwards;
    animation-delay: 0.7s;
}

.progress-bar {
    width: 100%;
    height: 4px;
    background: rgba(51, 51, 51, 0.1);
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #007AFF, #5AC8FA);
    border-radius: 2px;
    transition: width 0.3s ease;
    width: 0%;
}

.progress-text {
    font-size: 14px;
    color: #666;
    font-weight: 500;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* *** Content Animation *** */
.content-fade-in {
    animation: contentFadeIn 0.8s ease forwards;
}

@keyframes contentFadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

:root {
    --primary-color: #e8f1fc;
    --secondary-color: #f0e8fc;
    --success-color: #e8f8f0;
    --success-color-dark: #4caf50;
    --warning-color: #fff8e8;
    --danger-color: #ffe8ea;
    --info-color: #e8f4f8;
    --light-color: #f9f9f9;
    --dark-color: #6c7b7f;
    --storage-color: #ffe8e8;
    --complain-color: #fff5e8;
    --web-stats-color: #f2e8f8;
    --system-color: #e8f5e8;
    --queue-color: #e3f2fd;
    --suggestions-color: #f0e8ff;
    --elder-allowance-color: #fff3e0;
    --child-allowance-color: #f0f4ff;
    --esv-document-color: #e8fff4;
    --corruption-report-color: #fff0f0;
}

.index-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Summary Cards Large */
.summary-cards-large {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
    margin-bottom: 30px;
    justify-content: center;
    max-width: 1400px;
    margin-left: auto;
    margin-right: auto;
}

.summary-card-large {
    background: linear-gradient(135deg, #ffffff 0%, #fcfcfc 100%);
    border-radius: 20px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.3);
    text-decoration: none;
    color: inherit;
    min-height: 220px;
}

.summary-card-large:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    text-decoration: none;
    color: inherit;
}

.clickable-card {
    text-decoration: none;
    color: inherit;
    cursor: pointer;
    transition: all 0.3s ease;
}

.clickable-card:hover {
    text-decoration: none;
    color: inherit;
    transform: translateY(-5px) scale(1.01);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

.summary-card-header {
    padding: 25px;
    color: #6b6b6b;
    display: flex;
    align-items: center;
    gap: 15px;
    justify-content: flex-start;
}

/* Header backgrounds for each card type */
.system-header {
    background: linear-gradient(135deg, var(--system-color) 0%, #f0f8f0 100%);
}

.queue-header {
    background: linear-gradient(135deg, var(--queue-color) 0%, #f1f8ff 100%);
}

.storage-header {
    background: linear-gradient(135deg, var(--storage-color) 0%, #fff0f0 100%);
}

.complain-header {
    background: linear-gradient(135deg, var(--complain-color) 0%, #fffaf0 100%);
}

.web-stats-header {
    background: linear-gradient(135deg, var(--web-stats-color) 0%, #f8f0ff 100%);
}

.suggestions-header {
    background: linear-gradient(135deg, var(--suggestions-color) 0%, #f8f0ff 100%);
}

.elder-allowance-header {
    background: linear-gradient(135deg, var(--elder-allowance-color) 0%, #fff8f0 100%);
}

.child-allowance-header {
    background: linear-gradient(135deg, var(--child-allowance-color) 0%, #f8faff 100%);
}

.esv-document-header {
    background: linear-gradient(135deg, var(--esv-document-color) 0%, #f0fff8 100%);
}

.corruption-report-header {
    background: linear-gradient(135deg, var(--corruption-report-color) 0%, #fffaf8 100%);
}

/* *** Placeholder Card Styles *** */
.placeholder-header {
    background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
}

.summary-icon.placeholder {
    background: rgba(153, 153, 153, 0.15);
    color: #999;
}

.placeholder-card {
    opacity: 0.7;
    cursor: not-allowed;
}

.placeholder-card:hover {
    transform: none !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06) !important;
}

.placeholder-card .summary-title {
    color: #777;
}

.placeholder-card .stat-value {
    color: #999 !important;
}

.summary-icon {
    width: 55px;
    height: 55px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    backdrop-filter: blur(10px);
    position: relative;
}

.summary-icon.system {
    background: rgba(76, 175, 80, 0.15);
    color: #4caf50;
}

.summary-icon.queue {
    background: rgba(33, 150, 243, 0.15);
    color: #2196f3;
}

.summary-icon.storage {
    background: rgba(244, 67, 54, 0.15);
    color: #f44336;
}

.summary-icon.complain {
    background: rgba(255, 152, 0, 0.15);
    color: #ff9800;
}

.summary-icon.web-stats {
    background: rgba(63, 81, 181, 0.15);
    color: #3f51b5;
}

.summary-icon.suggestions {
    background: rgba(156, 39, 176, 0.15);
    color: #9c27b0;
}

.summary-icon.elder-allowance {
    background: rgba(255, 152, 0, 0.15);
    color: #ff9800;
}

.summary-icon.child-allowance {
    background: rgba(33, 150, 243, 0.15);
    color: #2196f3;
}

.summary-icon.esv-document {
    background: rgba(76, 175, 80, 0.15);
    color: #4caf50;
}

.summary-icon.corruption-report {
    background: rgba(244, 67, 54, 0.15);
    color: #dc3545;
}

.summary-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    color: #5a5a5a;
    display: flex;
    align-items: center;
}

.summary-title i {
    font-size: 16px;
    opacity: 0.8;
}

.summary-stats {
    padding: 30px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
}

/* Special grid for 3 stats (Anti-Corruption Card) */
.corruption-stats-3 {
    padding: 30px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
}

.stat-item {
    text-align: center;
    padding: 10px;
    border-radius: 12px;
    background: rgba(255,255,255,0.6);
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

.stat-item:hover {
    transform: translateY(-2px);
    background: rgba(255,255,255,0.8);
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
}

.stat-value {
    font-size: 26px;
    font-weight: 800;
    color: #4a4a4a;
    margin-bottom: 8px;
    line-height: 1.1;
    text-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.stat-label {
    font-size: 12px;
    color: #7a7a7a;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    line-height: 1.3;
    margin-bottom: 3px;
}

.stat-description {
    font-size: 10px;
    color: #a8a8a8;
    font-weight: 400;
    line-height: 1.2;
    margin-top: 2px;
}

/* *** Corruption Card Specific Styles *** */

/* Status indicators สำหรับการ์ดทุจริต */
.corruption-report-card.status-normal .summary-icon.corruption-report {
    background: rgba(76, 175, 80, 0.15);
    color: #4caf50;
}

.corruption-report-card.status-warning .summary-icon.corruption-report {
    background: rgba(255, 152, 0, 0.15);
    color: #ff9800;
}

.corruption-report-card.status-urgent .summary-icon.corruption-report {
    background: rgba(244, 67, 54, 0.15);
    color: #f44336;
}

/* Notification badge */
.corruption-notification-badge {
    position: absolute;
    top: -5px;
    right: -10px;
    background: #f44336;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    animation: pulse 2s infinite;
    z-index: 10;
	text-align: center;
    line-height: 1.5;
    padding-left: 1px;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* เอฟเฟ็กต์เมื่ออัปเดตข้อมูล */
.stat-value.updated {
    animation: highlight-corruption 0.8s ease;
}

@keyframes highlight-corruption {
    0% { 
        background-color: rgba(244, 67, 54, 0.2); 
        transform: scale(1.05); 
    }
    100% { 
        background-color: transparent; 
        transform: scale(1); 
    }
}

/* สีสถานะสำหรับ stat-value */
.corruption-stat-total {
    color: #6c7b7f !important;
}

.corruption-stat-pending {
    color: #ff9800 !important;
}

.corruption-stat-in-progress {
    color: #2196f3 !important;
}

.corruption-stat-closed {
    color: #4caf50 !important;
}

/* การ์ดที่ loading */
.corruption-report-card.loading {
    opacity: 0.7;
    pointer-events: none;
}

.corruption-report-card.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* เอฟเฟ็กต์เมื่อ hover การ์ดทุจริต */
.corruption-report-card:hover .summary-icon.corruption-report {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

/* Badge สำหรับแสดงสิทธิ์เฉพาะผู้ดูแล */
.admin-only-badge {
    background: linear-gradient(45deg, #ff6b6b, #ee5a24);
    color: white;
    font-size: 9px;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Quick Actions Styles */
.btn {
    border-radius: 15px;
    font-weight: 500;
    transition: all 0.3s ease;
    border-width: 1.5px;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.btn-outline-secondary {
    border-color: #d0d0d0;
    color: #8a8a8a;
}

.btn-outline-secondary:hover {
    background: #e8e8e8;
    border-color: #d0d0d0;
    color: #6b6b6b;
}

.btn-outline-dark {
    border-color: var(--dark-color);
    color: var(--dark-color);
}

.btn-outline-dark:hover {
    background: var(--dark-color);
    border-color: var(--dark-color);
    color: #ffffff;
}

/* Animation for loading states */
.stat-value, .summary-title {
    transition: all 0.3s ease;
}

.updating {
    opacity: 0.6;
    pointer-events: none;
}

.updated {
    animation: highlight 0.8s ease;
}

@keyframes highlight {
    0% { background-color: rgba(33, 150, 243, 0.2); transform: scale(1.05); }
    100% { background-color: transparent; transform: scale(1); }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .summary-cards-large {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .index-container {
        padding: 15px;
    }
    
    .summary-cards-large {
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .summary-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        padding: 25px;
    }
    
    .corruption-stats-3 {
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        padding: 25px;
    }
    
    .stat-item {
        padding: 8px;
    }
    
    .stat-value {
        font-size: 22px;
    }
    
    .stat-label {
        font-size: 11px;
        letter-spacing: 0.6px;
    }
    
    .corruption-notification-badge {
        width: 16px;
        height: 16px;
        font-size: 8px;
        top: -3px;
        right: -3px;
    }
    
    .admin-only-badge {
        font-size: 8px;
        padding: 1px 4px;
    }
}

@media (max-width: 480px) {
    .summary-stats {
        grid-template-columns: 1fr;
        padding: 20px;
    }
    
    .corruption-stats-3 {
        grid-template-columns: 1fr;
        padding: 20px;
    }
    
    .stat-item {
        padding: 12px;
        margin-bottom: 8px;
    }
    
    .stat-value {
        font-size: 24px;
    }
    
    .stat-label {
        font-size: 12px;
    }
}
</style>

<!-- ✅ JavaScript - รองรับสถิติครบทุกระบบ รวมเอกสารออนไลน์และแจ้งเรื่องทุจริต -->
<script>
// ✅ สร้าง namespace สำหรับหน้า index
window.reportsIndex = {
    // เก็บค่า PHP สำหรับใช้ใน JavaScript
    config: {
        exportUrl: '<?php echo site_url("System_reports/export_excel/"); ?>',
        summaryApiUrl: '<?php echo site_url("System_reports/api_summary_data"); ?>',
        webStatsApiUrl: '<?php echo site_url("System_reports/api_web_stats_summary"); ?>',
        queueApiUrl: '<?php echo site_url("System_reports/api_queue_summary"); ?>',
        suggestionsApiUrl: '<?php echo site_url("Suggestions/api_suggestions_summary"); ?>',
        elderAllowanceApiUrl: '<?php echo site_url("Elderly_aw_ods/api_elderly_summary"); ?>',
        childAllowanceApiUrl: '<?php echo site_url("Kid_aw_ods/api_allowance_summary"); ?>',
        esvDocumentsApiUrl: '<?php echo site_url("Esv_ods/api_esv_summary"); ?>',
        // *** แก้ไข URL สำหรับ Corruption API ***
        corruptionReportsApiUrl: '<?php echo site_url("Corruption/api_corruption_summary"); ?>',
        hasCorruptionAccess: <?php echo $show_corruption_card ? 'true' : 'false'; ?>
    },

    // *** Loading System Variables ***
    loadingProgress: 0,
    totalSteps: 8,
    completedSteps: 0,
    loadingTexts: [
        'กำลังเชื่อมต่อเซิร์ฟเวอร์...',
        'กำลังโหลดข้อมูลพื้นฐาน...',
        'กำลังเตรียมข้อมูลสถิติ...',
        'กำลังโหลดข้อมูลเรียลไทม์...',
        'กำลังโหลดข้อมูลเอกสาร...',
        'กำลังโหลดข้อมูลรายงานทุจริต...',
        'กำลังตรวจสอบสิทธิ์การเข้าถึง...',
        'เตรียมข้อมูลเสร็จสิ้น...'
    ],
    
    // ✅ ฟังก์ชันเริ่มต้น
    init: function() {
       // console.log('🚀 Reports Index - เริ่มต้นระบบ');
        
        // แสดง Loading Modal ทันที
        this.showLoadingModal();
        
        // ตรวจสอบ jQuery
        if (typeof jQuery === 'undefined') {
            console.warn('jQuery ยังไม่พร้อม จะลองใหม่...');
            setTimeout(this.init.bind(this), 100);
            return;
        }
        
        // เริ่มกระบวนการโหลด
        this.startLoadingSequence();
    },

    // *** แสดง Loading Modal สไตล์ Apple ***
    showLoadingModal: function() {
        const modal = document.getElementById('dashboardLoadingModal');
        if (modal) {
            modal.style.display = 'flex';
            
            // เริ่ม progress
            setTimeout(() => {
                this.updateProgress(0);
            }, 500);
        }
    },

    // *** อัปเดต Progress Bar ***
    updateProgress: function(percentage) {
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        
        if (progressFill && progressText) {
            progressFill.style.width = percentage + '%';
            progressText.textContent = Math.round(percentage) + '%';
        }
    },

    // *** อัปเดตข้อความโหลด ***
    updateLoadingText: function(title, description) {
        const titleElement = document.getElementById('loadingTitle');
        const descElement = document.getElementById('loadingDescription');
        
        if (titleElement) titleElement.textContent = title;
        if (descElement) descElement.textContent = description;
    },

    // *** เริ่มกระบวนการโหลดแบบ Sequence (เร็วขึ้น) ***
    startLoadingSequence: function() {
        let step = 0;
        const steps = [
            { title: 'เชื่อมต่อระบบ', desc: 'กำลังเชื่อมต่อกับเซิร์ฟเวอร์...', progress: 15 },
            { title: 'ตรวจสอบสิทธิ์', desc: 'กำลังตรวจสอบสิทธิ์การเข้าถึง...', progress: 30 },
            { title: 'โหลดข้อมูล', desc: 'กำลังโหลดข้อมูลระบบ...', progress: 50 },
            { title: 'โหลดเอกสาร', desc: 'กำลังโหลดข้อมูลเอกสารออนไลน์...', progress: 70 },
            { title: 'โหลดแจ้งเรื่อง ', desc: 'กำลังโหลดข้อมูลแจ้งเรื่อง...', progress: 85 },
            { title: 'เสร็จสิ้น', desc: 'เตรียมข้อมูลเสร็จสิ้น!', progress: 100 }
        ];

        const runStep = () => {
            if (step < steps.length) {
                const currentStep = steps[step];
                this.updateLoadingText(currentStep.title, currentStep.desc);
                this.updateProgress(currentStep.progress);
                
                step++;
                setTimeout(runStep, 100);  // ลดเวลา
            } else {
                // เสร็จสิ้นการโหลด
                setTimeout(() => {
                    this.hideLoadingModal();
                }, 90); // ลดเวลา
            }
        };

        runStep();
    },

    // *** ซ่อน Loading Modal และแสดง Content ***
    hideLoadingModal: function() {
        const modal = document.getElementById('dashboardLoadingModal');
        const content = document.getElementById('mainContent');
        
        if (modal) {
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.9)';
            
            setTimeout(() => {
                modal.style.display = 'none';
                
                // แสดง Content พร้อม Animation
                if (content) {
                    content.style.opacity = '1';
                    content.style.transform = 'translateY(0)';
                    content.classList.add('content-fade-in');
                }
                
                // เริ่มโหลดข้อมูลจริงทันที (ไม่ต้องรอ)
                this.loadActualData();
                
            }, 300);
        }
    },

    // *** โหลดข้อมูลจริงทันที ***
    loadActualData: function() {
        // เริ่มระบบปกติ
        this.setupCardEffects();
        this.setupScrollAnimations();
        this.startAutoRefresh();
        
        // โหลดข้อมูลทั้งหมดทันที
        setTimeout(() => {
            this.refreshSummaryData();
            this.refreshWebStatsData();
            this.refreshQueueData();
            this.refreshSuggestionsData();
            this.refreshElderAllowanceData();
            this.refreshChildAllowanceData();
            this.refreshEsvDocumentsData();
            
            // โหลดข้อมูลทุจริตเฉพาะผู้ที่มีสิทธิ์
            if (this.config.hasCorruptionAccess) {
                this.refreshCorruptionReportsData();
            }
            
           // console.log('📊 All data loaded successfully');
        }, 100);
        
        // แสดงการ์ดทีละใบ
        this.animateCardsSequentially();
        
       // console.log('✅ Dashboard ready!');
    },

    // *** Animation การ์ดทีละใบ ***
    animateCardsSequentially: function() {
        const cards = document.querySelectorAll('.summary-card-large');
        
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0) scale(1)';
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            }, index * 150);
        });
    },

    // โหลดข้อมูลเริ่มต้น (ปรับปรุงใหม่)
    loadInitialData: function() {
       // console.log('📊 Loading initial dashboard data...');
        // ข้อมูลจะถูกโหลดผ่าง showDataPreparation แล้ว
    },
    
    // เพิ่มเอฟเฟ็กต์ hover สำหรับ summary cards
    setupCardEffects: function() {
        const summaryCards = document.querySelectorAll('.summary-card-large');
        
        summaryCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
                this.style.transition = 'all 0.3s ease';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    },
    
    // เพิ่มเอฟเฟ็กต์การโหลดแบบลื่นไหล
    setupScrollAnimations: function() {
        const cards = document.querySelectorAll('.summary-card-large');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    entry.target.style.transition = 'all 0.2s ease';
                }
            });
        });

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            observer.observe(card);
        });
    },
    
    // เริ่ม auto-refresh (ทุก 5 นาที)
    startAutoRefresh: function() {
        setInterval(() => {
            this.refreshSummaryData();
            this.refreshWebStatsData();
            this.refreshQueueData();
            this.refreshSuggestionsData();
            this.refreshElderAllowanceData();
            this.refreshChildAllowanceData();
            this.refreshEsvDocumentsData();
            
            // รีเฟรชข้อมูลทุจริตเฉพาะผู้ที่มีสิทธิ์
            if (this.config.hasCorruptionAccess) {
                this.refreshCorruptionReportsData();
            }
        }, 300000); // 5 minutes
        
        // เริ่มการตรวจสอบข้อมูลทุจริตเป็นพิเศษ
        this.startCorruptionReportsMonitoring();
    },
    
    // ฟังก์ชันรีเฟรชข้อมูลทั้งหมด
    refreshData: function() {
        if (typeof showLoading === 'function') {
            showLoading();
        }
        
        const message = this.config.hasCorruptionAccess ? 
            'กำลังรีเฟรชข้อมูลทั้งหมด (รวมเอกสารออนไลน์และรายงานทุจริต)...' :
            'กำลังรีเฟรชข้อมูลทั้งหมด (รวมเอกสารออนไลน์)...';
        
        if (typeof showAlert === 'function') {
            showAlert(message, 'info');
        }
        
        // รีเฟรชข้อมูลทั้งหมด
        this.refreshSummaryData();
        this.refreshWebStatsData();
        this.refreshQueueData();
        this.refreshSuggestionsData();
        this.refreshElderAllowanceData();
        this.refreshChildAllowanceData();
        this.refreshEsvDocumentsData();
        
        // รีเฟรชข้อมูลทุจริตเฉพาะผู้ที่มีสิทธิ์
        if (this.config.hasCorruptionAccess) {
            this.refreshCorruptionReportsData();
        }
        
        setTimeout(function() {
            if (typeof hideLoading === 'function') {
                hideLoading();
            }
            const successMessage = window.reportsIndex.config.hasCorruptionAccess ?
                'รีเฟรชข้อมูลเสร็จสิ้น (รวมเอกสารออนไลน์และรายงานทุจริต)' :
                'รีเฟรชข้อมูลเสร็จสิ้น (รวมเอกสารออนไลน์)';
            
            if (typeof showAlert === 'function') {
                showAlert(successMessage, 'success');
            }
        }, 2000);
    },
    
    // *** ฟังก์ชันรีเฟรชข้อมูลรายงานทุจริต ***
    refreshCorruptionReportsData: function() {
        if (typeof jQuery === 'undefined') {
            console.warn('jQuery ไม่พร้อม ข้ามการ refresh corruption reports data');
            return;
        }
        
        // ตรวจสอบสิทธิ์ก่อน
        if (!this.config.hasCorruptionAccess) {
            console.info('ไม่มีสิทธิ์เข้าถึงข้อมูลรายงานทุจริต');
            return;
        }
        
        jQuery.ajax({
            url: this.config.corruptionReportsApiUrl,
            type: 'GET',
            dataType: 'json',
            timeout: 15000,
            success: (data) => {
                this.updateCorruptionReportsCards(data);
               // console.log('🛡️ Corruption reports data refreshed:', data);
            },
            error: function(xhr, status, error) {
                console.warn('Failed to refresh corruption reports data:', error);
                
                // แสดงข้อมูลเริ่มต้นถ้าเกิด error
                window.reportsIndex.updateCorruptionReportsCards({
                    success: true,
                    corruption_reports: {
                        total: 0,
                        pending: 0,
                        in_progress: 0,
                        closed: 0
                    }
                });
            }
        });
    },

    // *** ฟังก์ชันอัปเดตการ์ดรายงานทุจริต ***
    updateCorruptionReportsCards: function(data) {
        try {
            if (data.corruption_reports) {
                const totalElement = document.getElementById('corruption-reports-total');
                const pendingElement = document.getElementById('corruption-reports-pending');
                const inProgressElement = document.getElementById('corruption-reports-in-progress');
                const closedElement = document.getElementById('corruption-reports-closed');
                
                if (totalElement) {
                    const newValue = window.formatNumber ? 
                        window.formatNumber(data.corruption_reports.total) : 
                        data.corruption_reports.total;
                    totalElement.textContent = newValue;
                    totalElement.classList.add('updated');
                    setTimeout(() => totalElement.classList.remove('updated'), 800);
                }
                
                if (pendingElement) {
                    const newValue = window.formatNumber ? 
                        window.formatNumber(data.corruption_reports.pending) : 
                        data.corruption_reports.pending;
                    pendingElement.textContent = newValue;
                    pendingElement.classList.add('updated');
                    setTimeout(() => pendingElement.classList.remove('updated'), 800);
                }
                
                if (inProgressElement) {
                    const newValue = window.formatNumber ? 
                        window.formatNumber(data.corruption_reports.in_progress) : 
                        data.corruption_reports.in_progress;
                    inProgressElement.textContent = newValue;
                    inProgressElement.classList.add('updated');
                    setTimeout(() => inProgressElement.classList.remove('updated'), 800);
                }
                
                if (closedElement) {
                    const newValue = window.formatNumber ? 
                        window.formatNumber(data.corruption_reports.closed) : 
                        data.corruption_reports.closed;
                    closedElement.textContent = newValue;
                    closedElement.classList.add('updated');
                    setTimeout(() => closedElement.classList.remove('updated'), 800);
                }
                
                // อัปเดต badge หรือ indicator ถ้ามี
                this.updateCorruptionStatusIndicators(data.corruption_reports);
            }
            
           // console.log('✅ Corruption reports cards updated successfully');
        } catch (error) {
            console.error('❌ Error updating corruption reports cards:', error);
        }
    },

    // *** ฟังก์ชันอัปเดต indicators เพิ่มเติม ***
    updateCorruptionStatusIndicators: function(corruptionData) {
        try {
            // อัปเดต notification badge ถ้ามีเรื่องใหม่
            const notificationBadge = document.querySelector('.corruption-notification-badge');
            if (notificationBadge && corruptionData.pending > 0) {
                notificationBadge.textContent = corruptionData.pending;
                notificationBadge.style.display = 'inline-block';
            } else if (notificationBadge) {
                notificationBadge.style.display = 'none';
            }
            
            // อัปเดตสีของการ์ดตามสถานะ
            const corruptionCard = document.querySelector('.corruption-report-card');
            if (corruptionCard) {
                // เอาคลาสเก่าออก
                corruptionCard.classList.remove('status-normal', 'status-warning', 'status-urgent');
                
                // เพิ่มคลาสใหม่ตามจำนวน
                if (corruptionData.pending === 0) {
                    corruptionCard.classList.add('status-normal');
                } else if (corruptionData.pending <= 5) {
                    corruptionCard.classList.add('status-warning');
                } else {
                    corruptionCard.classList.add('status-urgent');
                }
            }
            
        } catch (error) {
            console.error('Error updating corruption status indicators:', error);
        }
    },

    // *** ฟังก์ชันเพิ่มเติมสำหรับจัดการ real-time updates ***
    startCorruptionReportsMonitoring: function() {
        if (!this.config.hasCorruptionAccess) {
            return;
        }
        
        // อัปเดตข้อมูลทุจริตทุก 30 วินาที (เพราะเป็นข้อมูลสำคัญ)
        setInterval(() => {
            this.refreshCorruptionReportsData();
        }, 30000); // 30 seconds
        
       // console.log('🛡️ Corruption reports monitoring started');
    },

    // *** ฟังก์ชันแสดงรายละเอียดเพิ่มเติม ***
    showCorruptionReportsDetail: function() {
        if (!this.config.hasCorruptionAccess) {
            console.warn('ไม่มีสิทธิ์เข้าถึงรายละเอียดรายงานทุจริต');
            return;
        }
        
        // เปิดหน้าจัดการรายงานทุจริต
        window.open(site_url('Corruption/admin_management'), '_blank');
    },
    
    // *** ฟังก์ชันรีเฟรชข้อมูลเอกสารออนไลน์ ***
    refreshEsvDocumentsData: function() {
        if (typeof jQuery === 'undefined') {
            console.warn('jQuery ไม่พร้อม ข้ามการ refresh esv documents data');
            return;
        }
        
        jQuery.ajax({
            url: this.config.esvDocumentsApiUrl,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: (data) => {
                this.updateEsvDocumentsCards(data);
               // console.log('📄 ESV documents data refreshed:', data);
            },
            error: function(xhr, status, error) {
                console.warn('Failed to refresh esv documents data:', error);
                // ใส่ค่าเริ่มต้นถ้าเกิด error
                window.reportsIndex.updateEsvDocumentsCards({
                    success: true,
                    esv_documents: {
                        total: 0,
                        pending: 0,
                        processing: 0,
                        completed: 0
                    }
                });
            }
        });
    },

    // *** ฟังก์ชันอัปเดตการ์ดเอกสารออนไลน์ ***
    updateEsvDocumentsCards: function(data) {
        try {
            if (data.esv_documents) {
                const totalElement = document.getElementById('esv-documents-total');
                const pendingElement = document.getElementById('esv-documents-pending');
                const processingElement = document.getElementById('esv-documents-processing');
                const completedElement = document.getElementById('esv-documents-completed');
                
                if (totalElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.esv_documents.total) : data.esv_documents.total;
                    totalElement.textContent = newValue;
                    totalElement.classList.add('updated');
                    setTimeout(() => totalElement.classList.remove('updated'), 800);
                }
                
                if (pendingElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.esv_documents.pending) : data.esv_documents.pending;
                    pendingElement.textContent = newValue;
                    pendingElement.classList.add('updated');
                    setTimeout(() => pendingElement.classList.remove('updated'), 800);
                }
                
                if (processingElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.esv_documents.processing) : data.esv_documents.processing;
                    processingElement.textContent = newValue;
                    processingElement.classList.add('updated');
                    setTimeout(() => processingElement.classList.remove('updated'), 800);
                }
                
                if (completedElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.esv_documents.completed) : data.esv_documents.completed;
                    completedElement.textContent = newValue;
                    completedElement.classList.add('updated');
                    setTimeout(() => completedElement.classList.remove('updated'), 800);
                }
            }
            
           // console.log('✅ ESV documents cards updated successfully');
        } catch (error) {
            console.error('❌ Error updating esv documents cards:', error);
        }
    },

    // *** ฟังก์ชันรีเฟรชข้อมูลเงินอุดหนุนเด็กแรกเกิด ***
    refreshChildAllowanceData: function() {
        if (typeof jQuery === 'undefined') {
            console.warn('jQuery ไม่พร้อม ข้ามการ refresh child allowance data');
            return;
        }
        
        jQuery.ajax({
            url: this.config.childAllowanceApiUrl,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: (data) => {
                this.updateChildAllowanceCards(data);
               // console.log('👶 Child allowance data refreshed:', data);
            },
            error: function(xhr, status, error) {
                console.warn('Failed to refresh child allowance data:', error);
                // ใส่ค่าเริ่มต้นถ้าเกิด error
                window.reportsIndex.updateChildAllowanceCards({
                    success: true,
                    child_allowance: {
                        total: 0,
                        submitted: 0,
                        reviewing: 0,
                        completed: 0
                    }
                });
            }
        });
    },

    // *** ฟังก์ชันอัปเดตการ์ดเงินอุดหนุนเด็กแรกเกิด ***
    updateChildAllowanceCards: function(data) {
        try {
            if (data.child_allowance) {
                const totalElement = document.getElementById('child-allowance-total');
                const submittedElement = document.getElementById('child-allowance-submitted');
                const reviewingElement = document.getElementById('child-allowance-reviewing');
                const completedElement = document.getElementById('child-allowance-completed');
                
                if (totalElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.child_allowance.total) : data.child_allowance.total;
                    totalElement.textContent = newValue;
                    totalElement.classList.add('updated');
                    setTimeout(() => totalElement.classList.remove('updated'), 800);
                }
                
                if (submittedElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.child_allowance.submitted) : data.child_allowance.submitted;
                    submittedElement.textContent = newValue;
                    submittedElement.classList.add('updated');
                    setTimeout(() => submittedElement.classList.remove('updated'), 800);
                }
                
                if (reviewingElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.child_allowance.reviewing) : data.child_allowance.reviewing;
                    reviewingElement.textContent = newValue;
                    reviewingElement.classList.add('updated');
                    setTimeout(() => reviewingElement.classList.remove('updated'), 800);
                }
                
                if (completedElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.child_allowance.completed) : data.child_allowance.completed;
                    completedElement.textContent = newValue;
                    completedElement.classList.add('updated');
                    setTimeout(() => completedElement.classList.remove('updated'), 800);
                }
            }
            
           // console.log('✅ Child allowance cards updated successfully');
        } catch (error) {
            console.error('❌ Error updating child allowance cards:', error);
        }
    },

    // ฟังก์ชันรีเฟรชข้อมูลสรุป
    refreshSummaryData: function() {
        if (typeof jQuery === 'undefined') {
            console.warn('jQuery ไม่พร้อม ข้ามการ refresh summary data');
            return;
        }
        
        jQuery.ajax({
            url: this.config.summaryApiUrl,
            type: 'GET',
            dataType: 'json',
            success: (data) => {
                this.updateSummaryCards(data);
              //  console.log('📊 Summary data refreshed:', data);
            },
            error: function(xhr, status, error) {
                console.warn('Failed to refresh summary data:', error);
            }
        });
    },
    
    // ฟังก์ชันรีเฟรชสถิติเว็บไซต์
    refreshWebStatsData: function() {
        if (typeof jQuery === 'undefined') return;
        
        jQuery.ajax({
            url: this.config.webStatsApiUrl,
            type: 'GET',
            dataType: 'json',
            success: (data) => {
                this.updateWebStatsCards(data);
               // console.log('🌐 Web stats data refreshed:', data);
            },
            error: function(xhr, status, error) {
                console.warn('Failed to refresh web stats data:', error);
            }
        });
    },

    // ฟังก์ชันรีเฟรชข้อมูลคิว
    refreshQueueData: function() {
        if (typeof jQuery === 'undefined') return;
        
        jQuery.ajax({
            url: this.config.queueApiUrl,
            type: 'GET',
            dataType: 'json',
            success: (data) => {
                this.updateQueueCards(data);
              //  console.log('📅 Queue data refreshed:', data);
            },
            error: function(xhr, status, error) {
                console.warn('Failed to refresh queue data:', error);
            }
        });
    },

    // ฟังก์ชันรีเฟรชข้อมูล Suggestions
    refreshSuggestionsData: function() {
        if (typeof jQuery === 'undefined') return;
        
        jQuery.ajax({
            url: this.config.suggestionsApiUrl,
            type: 'GET',
            dataType: 'json',
            success: (data) => {
                this.updateSuggestionsCards(data);
               // console.log('💡 Suggestions data refreshed:', data);
            },
            error: function(xhr, status, error) {
                console.warn('Failed to refresh suggestions data:', error);
            }
        });
    },

    // ฟังก์ชันรีเฟรชข้อมูลเบี้ยยังชีพผู้สูงอายุ/พิการ
    refreshElderAllowanceData: function() {
        if (typeof jQuery === 'undefined') return;
        
        jQuery.ajax({
            url: this.config.elderAllowanceApiUrl,
            type: 'GET',
            dataType: 'json',
            success: (data) => {
                this.updateElderAllowanceCards(data);
              //  console.log('👴 Elder allowance data refreshed:', data);
            },
            error: function(xhr, status, error) {
                console.warn('Failed to refresh elder allowance data:', error);
            }
        });
    },
    
    // ฟังก์ชันอัปเดต summary cards
    // ฟังก์ชันอัปเดต summary cards
updateSummaryCards: function(data) {
    try {
        if (data.storage) {
            document.getElementById('storage-percentage').textContent = data.storage.percentage.toFixed(1) + '%';
            document.getElementById('storage-free').textContent = data.storage.free.toFixed(1);
            document.getElementById('storage-used').textContent = data.storage.used.toFixed(1);
            document.getElementById('storage-total').textContent = data.storage.total.toFixed(1);
        }
        
        if (data.complains) {
            document.getElementById('complains-total').textContent = window.formatNumber ? window.formatNumber(data.complains.total) : data.complains.total;
            // 🆕 เพิ่มการอัปเดตกล่อง รอรับเรื่อง
            document.getElementById('complains-waiting').textContent = window.formatNumber ? window.formatNumber(data.complains.waiting) : data.complains.waiting;
            document.getElementById('complains-pending').textContent = window.formatNumber ? window.formatNumber(data.complains.pending) : data.complains.pending;
            document.getElementById('complains-completed').textContent = window.formatNumber ? window.formatNumber(data.complains.completed) : data.complains.completed;
            document.getElementById('complains-in-progress').textContent = window.formatNumber ? window.formatNumber(data.complains.in_progress) : data.complains.in_progress;
        }
        
        console.log('✅ Summary cards updated successfully');
    } catch (error) {
        console.warn('Error updating summary cards:', error);
    }
},
    
    // ฟังก์ชันอัปเดตสถิติเว็บไซต์
    updateWebStatsCards: function(data) {
        try {
            if (data.web_stats) {
                const pageviewsElement = document.getElementById('web-pageviews');
                const visitorsElement = document.getElementById('web-visitors');
                const onlineElement = document.getElementById('web-online');
                const avgPagesElement = document.getElementById('web-avg-pages');
                
                if (pageviewsElement) {
                    pageviewsElement.textContent = window.formatNumber ? window.formatNumber(data.web_stats.total_pageviews) : data.web_stats.total_pageviews;
                    pageviewsElement.classList.add('updated');
                    setTimeout(() => pageviewsElement.classList.remove('updated'), 800);
                }
                
                if (visitorsElement) {
                    visitorsElement.textContent = window.formatNumber ? window.formatNumber(data.web_stats.total_visitors) : data.web_stats.total_visitors;
                    visitorsElement.classList.add('updated');
                    setTimeout(() => visitorsElement.classList.remove('updated'), 800);
                }
                
                if (onlineElement) {
                    onlineElement.textContent = window.formatNumber ? window.formatNumber(data.web_stats.online_users) : data.web_stats.online_users;
                    onlineElement.classList.add('updated');
                    setTimeout(() => onlineElement.classList.remove('updated'), 800);
                }
                
                if (avgPagesElement) {
                    const avgPages = data.web_stats.avg_pages_per_visitor || 0;
                    avgPagesElement.textContent = parseFloat(avgPages).toFixed(2);
                    avgPagesElement.classList.add('updated');
                    setTimeout(() => avgPagesElement.classList.remove('updated'), 800);
                }
            }
            
           // console.log('✅ Web stats cards updated successfully');
        } catch (error) {
            console.warn('Error updating web stats cards:', error);
        }
    },

    // ฟังก์ชันอัปเดตข้อมูลคิว
    updateQueueCards: function(data) {
        try {
            if (data.queue_stats) {
                const totalElement = document.getElementById('queue-total');
                const pendingElement = document.getElementById('queue-pending');
                const progressElement = document.getElementById('queue-progress');
                const completedElement = document.getElementById('queue-completed');
                
                if (totalElement) {
                    totalElement.textContent = window.formatNumber ? window.formatNumber(data.queue_stats.total) : data.queue_stats.total;
                    totalElement.classList.add('updated');
                    setTimeout(() => totalElement.classList.remove('updated'), 800);
                }
                
                if (pendingElement) {
                    pendingElement.textContent = window.formatNumber ? window.formatNumber(data.queue_stats.pending) : data.queue_stats.pending;
                    pendingElement.classList.add('updated');
                    setTimeout(() => pendingElement.classList.remove('updated'), 800);
                }
                
                if (progressElement) {
                    progressElement.textContent = window.formatNumber ? window.formatNumber(data.queue_stats.in_progress) : data.queue_stats.in_progress;
                    progressElement.classList.add('updated');
                    setTimeout(() => progressElement.classList.remove('updated'), 800);
                }
                
                if (completedElement) {
                    completedElement.textContent = window.formatNumber ? window.formatNumber(data.queue_stats.completed) : data.queue_stats.completed;
                    completedElement.classList.add('updated');
                    setTimeout(() => completedElement.classList.remove('updated'), 800);
                }
            }
            
           // console.log('✅ Queue cards updated successfully');
        } catch (error) {
            console.warn('Error updating queue cards:', error);
        }
    },

    // ฟังก์ชันอัปเดตข้อมูล Suggestions
    updateSuggestionsCards: function(data) {
        try {
            if (data.suggestions) {
                const totalElement = document.getElementById('suggestions-total');
                const newElement = document.getElementById('suggestions-new');
                const reviewedElement = document.getElementById('suggestions-reviewed');
                const implementedElement = document.getElementById('suggestions-implemented');
                
                if (totalElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.suggestions.total) : data.suggestions.total;
                    totalElement.textContent = newValue;
                    totalElement.classList.add('updated');
                    setTimeout(() => totalElement.classList.remove('updated'), 800);
                }
                
                if (newElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.suggestions.new) : data.suggestions.new;
                    newElement.textContent = newValue;
                    newElement.classList.add('updated');
                    setTimeout(() => newElement.classList.remove('updated'), 800);
                }
                
                if (reviewedElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.suggestions.reviewed) : data.suggestions.reviewed;
                    reviewedElement.textContent = newValue;
                    reviewedElement.classList.add('updated');
                    setTimeout(() => reviewedElement.classList.remove('updated'), 800);
                }
                
                if (implementedElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.suggestions.implemented) : data.suggestions.implemented;
                    implementedElement.textContent = newValue;
                    implementedElement.classList.add('updated');
                    setTimeout(() => implementedElement.classList.remove('updated'), 800);
                }
            }
            
           // console.log('✅ Suggestions cards updated successfully');
        } catch (error) {
            console.error('❌ Error updating suggestions cards:', error);
        }
    },

    // ฟังก์ชันอัปเดตข้อมูลเบี้ยยังชีพผู้สูงอายุ/พิการ
    updateElderAllowanceCards: function(data) {
        try {
            if (data.elder_allowance) {
                const totalElement = document.getElementById('elder-allowance-total');
                const submittedElement = document.getElementById('elder-allowance-submitted');
                const reviewingElement = document.getElementById('elder-allowance-reviewing');
                const completedElement = document.getElementById('elder-allowance-completed');
                
                if (totalElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.elder_allowance.total) : data.elder_allowance.total;
                    totalElement.textContent = newValue;
                    totalElement.classList.add('updated');
                    setTimeout(() => totalElement.classList.remove('updated'), 800);
                }
                
                if (submittedElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.elder_allowance.submitted) : data.elder_allowance.submitted;
                    submittedElement.textContent = newValue;
                    submittedElement.classList.add('updated');
                    setTimeout(() => submittedElement.classList.remove('updated'), 800);
                }
                
                if (reviewingElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.elder_allowance.reviewing) : data.elder_allowance.reviewing;
                    reviewingElement.textContent = newValue;
                    reviewingElement.classList.add('updated');
                    setTimeout(() => reviewingElement.classList.remove('updated'), 800);
                }
                
                if (completedElement) {
                    const newValue = window.formatNumber ? window.formatNumber(data.elder_allowance.completed) : data.elder_allowance.completed;
                    completedElement.textContent = newValue;
                    completedElement.classList.add('updated');
                    setTimeout(() => completedElement.classList.remove('updated'), 800);
                }
            }
            
           // console.log('✅ Elder allowance cards updated successfully');
        } catch (error) {
            console.error('❌ Error updating elder allowance cards:', error);
        }
    },
    
    // ฟังก์ชันแสดงข้อมูลระบบ
    showSystemInfo: function() {
        const systemInfo = {
            browser: navigator.userAgent,
            language: navigator.language,
            platform: navigator.platform,
            cookieEnabled: navigator.cookieEnabled,
            screenResolution: screen.width + 'x' + screen.height,
            colorDepth: screen.colorDepth,
            currentTime: new Date().toLocaleString('th-TH'),
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        };
        
        const infoHtml = `
            <div class="modal fade" id="systemInfoModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-info-circle me-2"></i>
                                ข้อมูลระบบ
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <th width="30%">เบราว์เซอร์</th>
                                            <td>${systemInfo.browser}</td>
                                        </tr>
                                        <tr>
                                            <th>ภาษา</th>
                                            <td>${systemInfo.language}</td>
                                        </tr>
                                        <tr>
                                            <th>แพลตฟอร์ม</th>
                                            <td>${systemInfo.platform}</td>
                                        </tr>
                                        <tr>
                                            <th>รองรับคุกกี้</th>
                                            <td>${systemInfo.cookieEnabled ? 'ใช่' : 'ไม่'}</td>
                                        </tr>
                                        <tr>
                                            <th>ความละเอียดหน้าจอ</th>
                                            <td>${systemInfo.screenResolution}</td>
                                        </tr>
                                        <tr>
                                            <th>ความลึกสี</th>
                                            <td>${systemInfo.colorDepth} บิต</td>
                                        </tr>
                                        <tr>
                                            <th>เวลาปัจจุบัน</th>
                                            <td>${systemInfo.currentTime}</td>
                                        </tr>
                                        <tr>
                                            <th>เขตเวลา</th>
                                            <td>${systemInfo.timezone}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        jQuery('body').append(infoHtml);
        jQuery('#systemInfoModal').modal('show');
        
        jQuery('#systemInfoModal').on('hidden.bs.modal', function() {
            jQuery(this).remove();
        });
    }
};

// ✅ Helper function สำหรับจัดรูปแบบตัวเลข
window.formatNumber = function(num) {
    if (num === null || num === undefined) return '0';
    return new Intl.NumberFormat('th-TH').format(num);
};

// ✅ เรียกใช้เมื่อ DOM พร้อม
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        // เริ่มต้นระบบทันทีที่ DOM พร้อม
        window.reportsIndex.init();
    });
} else {
    // ถ้า DOM พร้อมแล้ว เริ่มเลย
    window.reportsIndex.init();
}

//console.log("📚 Complete Dashboard with Anti-Corruption Reports Ready! 🍎✨📄🛡️");
</script>


<script>
// เพิ่ม Assessment API URL ใน config
window.reportsIndex.config.assessmentApiUrl = '<?php echo site_url("System_reports/api_assessment_summary"); ?>';

// ฟังก์ชันรีเฟรชข้อมูลแบบประเมิน
window.reportsIndex.refreshAssessmentData = function() {
    if (typeof jQuery === 'undefined') {
        console.warn('jQuery ไม่พร้อม ข้ามการ refresh assessment data');
        return;
    }
    
    jQuery.ajax({
        url: this.config.assessmentApiUrl,
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: (data) => {
            this.updateAssessmentCards(data);
            console.log('📊 Assessment data refreshed:', data);
        },
        error: function(xhr, status, error) {
            console.warn('Failed to refresh assessment data:', error);
            // ใส่ค่าเริ่มต้นถ้าเกิด error
            window.reportsIndex.updateAssessmentCards({
                success: true,
                assessment: {
                    total: 0,
                    today: 0,
                    avg_score: 0,
                    questions: 0
                }
            });
        }
    });
};

// ฟังก์ชันอัปเดตการ์ดแบบประเมิน
window.reportsIndex.updateAssessmentCards = function(data) {
    try {
        if (data.assessment) {
            const totalElement = document.getElementById('assessment-total');
            const todayElement = document.getElementById('assessment-today');
            const scoreElement = document.getElementById('assessment-score');
            const questionsElement = document.getElementById('assessment-questions');
            
            if (totalElement) {
                const newValue = window.formatNumber ? 
                    window.formatNumber(data.assessment.total) : 
                    data.assessment.total;
                totalElement.textContent = newValue;
                totalElement.classList.add('updated');
                setTimeout(() => totalElement.classList.remove('updated'), 800);
            }
            
            if (todayElement) {
                const newValue = window.formatNumber ? 
                    window.formatNumber(data.assessment.today) : 
                    data.assessment.today;
                todayElement.textContent = newValue;
                todayElement.classList.add('updated');
                setTimeout(() => todayElement.classList.remove('updated'), 800);
                
                // เพิ่มเอฟเฟ็กต์ pulse ถ้ามีการตอบใหม่
                if (data.assessment.today > 0) {
                    const card = document.querySelector('.assessment-card');
                    if (card) {
                        card.classList.add('new-data');
                        setTimeout(() => card.classList.remove('new-data'), 3000);
                    }
                }
            }
            
            if (scoreElement) {
                const score = parseFloat(data.assessment.avg_score || 0);
                scoreElement.textContent = score.toFixed(2);
                scoreElement.classList.add('updated');
                setTimeout(() => scoreElement.classList.remove('updated'), 800);
                
                // เปลี่ยนสีตามคะแนน
                if (score >= 4.5) {
                    scoreElement.style.color = '#4caf50'; // เขียว
                } else if (score >= 4.0) {
                    scoreElement.style.color = '#2196f3'; // น้ำเงิน
                } else if (score >= 3.5) {
                    scoreElement.style.color = '#ff9800'; // ส้ม
                } else {
                    scoreElement.style.color = '#f44336'; // แดง
                }
            }
            
            if (questionsElement) {
                const newValue = window.formatNumber ? 
                    window.formatNumber(data.assessment.questions) : 
                    data.assessment.questions;
                questionsElement.textContent = newValue;
                questionsElement.classList.add('updated');
                setTimeout(() => questionsElement.classList.remove('updated'), 800);
            }
        }
        
        console.log('✅ Assessment cards updated successfully');
    } catch (error) {
        console.error('❌ Error updating assessment cards:', error);
    }
};

// เพิ่มการรีเฟรชข้อมูลแบบประเมินใน refreshData function
const originalRefreshData = window.reportsIndex.refreshData;
window.reportsIndex.refreshData = function() {
    // เรียก original function
    originalRefreshData.call(this);
    
    // เพิ่มการรีเฟรชข้อมูลแบบประเมิน
    this.refreshAssessmentData();
};

// เพิ่มการรีเฟรชข้อมูลแบบประเมินใน loadActualData function
const originalLoadActualData = window.reportsIndex.loadActualData;
window.reportsIndex.loadActualData = function() {
    // เรียก original function
    originalLoadActualData.call(this);
    
    // เพิ่มการโหลดข้อมูลแบบประเมิน
    setTimeout(() => {
        this.refreshAssessmentData();
    }, 200);
};

// เพิ่มการรีเฟรชข้อมูลแบบประเมินใน auto-refresh
const originalStartAutoRefresh = window.reportsIndex.startAutoRefresh;
window.reportsIndex.startAutoRefresh = function() {
    // เรียก original function
    originalStartAutoRefresh.call(this);
    
    // เพิ่ม auto-refresh สำหรับแบบประเมิน
    setInterval(() => {
        this.refreshAssessmentData();
    }, 300000); // 5 minutes
};

console.log("📊 Assessment Summary Card Added Successfully! ⭐");
</script>