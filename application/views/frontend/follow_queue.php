<div class="text-center pages-head">
    <span class="font-pages-head">ติดตามสถานะคิว</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<!-- หน้าติดตามสถานะคิว -->
<div class="text-center pages-head">
    <span class="font-pages-head"
        style="font-size: 2.8rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(108, 117, 125, 0.2);">ติดตามสถานะคิว</span>
</div>

<div class="bg-pages" style="background: #ffffff; min-height: 100vh; padding: 2rem 0;">
    <div class="container-pages-news" style="position: relative; z-index: 10;">

        <!-- ข้อความแจ้งเตือน -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert"
                style="border-radius: 15px; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: 1px solid rgba(40, 167, 69, 0.3); box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);">
                <i class="fas fa-check-circle me-2"></i><?= $success_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert"
                style="border-radius: 15px; background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border: 1px solid rgba(220, 53, 69, 0.3); box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- รายการคิวของผู้ใช้ (แสดงเมื่อ login แล้ว) -->
        <?php if (!empty($is_logged_in) && !empty($user_queues)): ?>
            <div class="container-pages-news mb-4"
                style="background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 1000px; overflow: hidden;"
                id="user_queues_section">
                <div
                    style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #28a745, #20c997, #28a745); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;">
                </div>

                <div class="row align-items-center mb-4">
                    <div class="col-auto">
                        <div
                            style="width: 70px; height: 70px; background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(32, 201, 151, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);">
                            <i class="fas fa-user-check" style="font-size: 2rem; color: #28a745;"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h4 style="color: #2c3e50; margin-bottom: 0.5rem; font-weight: 700;">
                            <i class="fas fa-list-ul me-2" style="color: #28a745;"></i>คิวของคุณ
                        </h4>
                        <p style="color: #6c757d; margin: 0;">รายการคิวทั้งหมดในบัญชีของคุณ</p>
                    </div>
                </div>

                <div class="row" id="user_queues_container">
                    <?php foreach ($user_queues as $queue): ?>
                        <div class="col-md-6 mb-3">
                            <div class="queue-card"
                                style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 15px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 1px solid rgba(102, 126, 234, 0.1); cursor: pointer;"
                                onclick="searchQueueById('<?= htmlspecialchars($queue['queue_id'] ?? '') ?>')">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="queue-id-badge"
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.3rem 0.8rem; border-radius: 8px; font-size: 0.9rem; font-weight: 600;">
                                        <?= htmlspecialchars($queue['queue_id'] ?? '') ?>
                                    </span>
                                    <span
                                        class="queue-status-badge status-<?= str_replace(' ', '-', strtolower($queue['queue_status'] ?? 'unknown')) ?>"
                                        style="padding: 0.3rem 0.8rem; border-radius: 8px; font-size: 0.8rem; font-weight: 500;">
                                        <?= htmlspecialchars($queue['queue_status'] ?? '') ?>
                                    </span>
                                </div>
                                <h6 style="color: #2c3e50; margin-bottom: 0.5rem; font-weight: 600;">
                                    <?= htmlspecialchars($queue['queue_topic'] ?? '') ?>
                                </h6>
                                <p style="color: #6c757d; margin: 0; font-size: 0.9rem;">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($queue['queue_date'] ?? 'now'))) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ฟอร์มค้นหาคิว -->
        <div class="container-pages-news mb-4"
            style="background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 800px; overflow: hidden;"
            id="search_form">
            <div
                style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #667eea, #764ba2, #667eea); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;">
            </div>

            <div class="text-center mb-4">
                <div
                    style="width: 80px; height: 80px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); margin: 0 auto 1.5rem;">
                    <i class="fas fa-search" style="font-size: 2.5rem; color: #667eea;"></i>
                </div>
                <h3 style="color: #2c3e50; margin-bottom: 0.5rem; font-weight: 700;">
                    <i class="fas fa-clipboard-list me-2" style="color: #667eea;"></i>ค้นหาสถานะคิว
                </h3>
                <p style="color: #6c757d; margin: 0; font-size: 1.1rem;">กรอกหมายเลขคิวของคุณเพื่อติดตามสถานะ</p>
            </div>

            <!-- ฟอร์มค้นหา -->
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <div class="form-label-wrapper text-center"
                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);">
                            <label class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-ticket-alt me-2" style="color: #667eea;"></i>หมายเลขคิว
                            </label>
                        </div>
                        <input type="text" id="search_queue_id" class="form-control text-center"
                            placeholder="กรอกหมายเลขคิว เช่น Q20241201001"
                            style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.2rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); text-transform: uppercase; font-weight: 600; letter-spacing: 1px;">
                    </div>
                </div>
            </div>

            <!-- ปุ่มค้นหา - อยู่กลางกล่อง -->
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <button type="button" id="search_queue_btn" class="btn w-100"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; padding: 1rem 2rem; border-radius: 15px; font-size: 1.1rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden;">
                        <span style="position: relative; z-index: 2;">
                            <i class="fas fa-search me-2"></i>ค้นหา
                        </span>
                        <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; z-index: 1;"
                            class="btn-shine"></div>
                    </button>
                </div>
            </div>
        </div>

        <!-- ปุ่มจองคิวใหม่ -->
        <div class="text-center mb-4">
            <a href="<?php echo site_url('Queue/adding_queue'); ?>" class="btn btn-outline-primary"
                style="border-radius: 12px; padding: 0.8rem 2rem; font-weight: 600; transition: all 0.3s ease; border: 2px solid #667eea; color: #667eea;">
                <i class="fas fa-plus-circle me-2"></i>จองคิวใหม่
            </a>
        </div>

        <!-- ผลการค้นหา -->
        <div class="container-pages-news"
            style="background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 1000px; overflow: hidden; display: none;"
            id="search_results">
            <div
                style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #fd7e14, #ffc107, #fd7e14); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;">
            </div>

            <!-- ข้อมูลคิว -->
            <div id="queue_details_section">
                <!-- จะถูกเติมด้วย JavaScript -->
            </div>

            <!-- ประวัติการเปลี่ยนสถานะ -->
            <div id="queue_history_section" style="margin-top: 2rem;">
                <!-- จะถูกเติมด้วย JavaScript -->
            </div>

            <!-- ไฟล์แนบ -->
            <div id="queue_files_section" style="margin-top: 2rem;">
                <!-- จะถูกเติมด้วย JavaScript -->
            </div>

            <!-- ปุ่มยกเลิกคิว -->
            <div id="queue_actions_section" style="margin-top: 2rem; text-align: center;">
                <!-- จะถูกเติมด้วย JavaScript -->
            </div>
        </div>

        <!-- ข้อความไม่พบผลการค้นหา (ไม่ได้ใช้แล้ว - ใช้ Modal แทน) -->
        <div class="container-pages-news text-center"
            style="background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 3rem; margin: 0 auto; max-width: 600px; display: none;"
            id="no_results">
            <div
                style="width: 120px; height: 120px; margin: 0 auto 2rem; background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.25) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-search" style="font-size: 3rem; color: #dc3545;"></i>
            </div>
            <h4 style="color: #dc3545; margin-bottom: 1rem; font-weight: 600;">ไม่พบข้อมูลคิว</h4>
            <p style="color: #6c757d; margin-bottom: 2rem;">ไม่พบหมายเลขคิวที่ระบุ กรุณาตรวจสอบหมายเลขคิวอีกครั้ง</p>
            <button type="button" class="btn btn-outline-primary" onclick="resetSearch()"
                style="border-radius: 12px; padding: 0.8rem 2rem; font-weight: 600;">
                <i class="fas fa-redo me-2"></i>ค้นหาใหม่
            </button>
        </div>
    </div>
</div>

<!-- CSS เพิ่มเติม -->
<style>
    /* Queue Status Badges */
    .queue-status-badge {
        font-weight: 600;
        text-align: center;
        display: inline-block;
    }

    .status-รอยืนยันการจอง,
    .status-รอยืนยัน {
        background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);
        color: #212529;
    }

    .status-ยืนยันแล้ว,
    .status-อนุมัติแล้ว {
        background: linear-gradient(135deg, #20c997 0%, #25d9cc 100%);
        color: white;
    }

    .status-กำลังดำเนินการ,
    .status-ดำเนินการ {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }

    .status-เสร็จสิ้น,
    .status-สำเร็จ {
        background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
        color: white;
    }

    .status-ยกเลิก,
    .status-ปฏิเสธ {
        background: linear-gradient(135deg, #dc3545 0%, #e4606d 100%);
        color: white;
    }

    .status-รอเอกสาร {
        background: linear-gradient(135deg, #fd7e14 0%, #ff922b 100%);
        color: white;
    }

    /* Queue Card Hover Effects */
    .queue-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2) !important;
        border-color: rgba(102, 126, 234, 0.3) !important;
    }

    /* Search Button Hover */
    #search_queue_btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4) !important;
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%) !important;
    }

    #search_queue_btn:hover .btn-shine {
        left: 100%;
    }

    /* Form Focus Effects */
    #search_queue_id:focus {
        border-color: transparent !important;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25) !important;
        transform: translateY(-1px);
        background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%) !important;
    }

    /* Animation */
    @keyframes gradientShift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
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

    .fade-in-up {
        animation: fadeInUp 0.5s ease-out;
    }

    /* Status Timeline */
    .status-timeline {
        position: relative;
        padding-left: 2rem;
    }

    .status-timeline::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    }

    .status-timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .status-timeline-item::before {
        content: '';
        position: absolute;
        left: -1.5rem;
        top: 0.5rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background: white;
        border: 3px solid #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    .status-timeline-item.current::before {
        background: #667eea;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        50% {
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.6), 0 0 0 8px rgba(102, 126, 234, 0.1);
        }

        100% {
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }
    }

    /* File Preview */
    .file-preview-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid rgba(102, 126, 234, 0.1);
        transition: all 0.3s ease;
    }

    .file-preview-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
    }

    /* Image Preview Specific Styles */
    .image-preview:hover .image-overlay {
        opacity: 1;
    }

    .image-preview img {
        transition: transform 0.3s ease;
    }

    .image-preview:hover img {
        transform: scale(1.05);
    }

    /* PDF Preview Specific Styles */
    .pdf-preview:hover {
        border-color: rgba(220, 53, 69, 0.3);
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.15);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .font-pages-head {
            font-size: 2rem !important;
        }

        .container-pages-news {
            margin: 0 1rem !important;
            padding: 1.5rem !important;
        }

        .queue-card {
            margin-bottom: 1rem !important;
        }

        #search_queue_btn {
            margin-top: 1rem;
        }
    }
</style>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // *** ตัวแปร Global แก้ไขแล้ว ***
    const searchUrl = '<?= site_url("Queue/search_queue") ?>';
    const cancelUrl = '<?= site_url("Queue/cancel_queue") ?>';
    const isLoggedIn = <?= json_encode($is_logged_in ?? false) ?>;
    const userType = '<?= $user_type ?? "guest" ?>';
    const userId = <?= json_encode(($is_logged_in && isset($user_info['id'])) ? $user_info['id'] : null) ?>;
    const autoSearch = '<?= $auto_search ?? "" ?>';
    const fromSuccess = <?= json_encode($from_success ?? false) ?>;

    // *** เพิ่มการตรวจสอบ Activity Slider ***
    const hasActivitySlider = <?= json_encode($has_activity_slider ?? false) ?>;

    // *** Global Error Handler สำหรับ Activity Slider ***
    window.addEventListener('error', function (event) {
        if (event.error && event.error.message && event.error.message.includes('initializeActivitySlider')) {
            console.warn('🔧 Activity slider error caught and suppressed:', event.error.message);
            event.preventDefault();
            return false;
        }
    });

    // Unhandled promise rejection handler
    window.addEventListener('unhandledrejection', function (event) {
        if (event.reason && event.reason.toString().includes('initializeActivitySlider')) {
            console.warn('🔧 Activity slider promise rejection caught:', event.reason);
            event.preventDefault();
        }
    });

    // *** สร้าง fallback function เพื่อป้องกัน error ***
    if (typeof initializeActivitySlider === 'undefined') {
        window.initializeActivitySlider = function () {
            console.log('📋 Activity slider: Fallback function called - no implementation needed');
            return Promise.resolve(false);
        };
    }

    // เมื่อเอกสารโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function () {
        try {
            // ตั้งค่า event listeners
            setupEventListeners();

            // *** แก้ไข: ตรวจสอบ Activity Slider แบบปลอดภัย ***
            if (hasActivitySlider) {
                // ตรวจสอบว่ามี function และ element ที่จำเป็น
                if (typeof initializeActivitySlider === 'function') {
                    const sliderContainers = [
                        '.activity-slider-container',
                        '.activity-slider',
                        '#activity-slider',
                        '.swiper-container',
                        '.activity-carousel'
                    ];

                    let sliderContainer = null;
                    for (let selector of sliderContainers) {
                        sliderContainer = document.querySelector(selector);
                        if (sliderContainer) break;
                    }

                    if (sliderContainer) {
                        try {
                            const result = initializeActivitySlider();
                            if (result instanceof Promise) {
                                result.then(() => {
                                    console.log('✅ Activity slider initialized successfully (async)');
                                }).catch(error => {
                                    console.warn('⚠️ Activity slider initialization failed (async):', error.message);
                                });
                            } else {
                                console.log('✅ Activity slider initialized successfully (sync)');
                            }
                        } catch (error) {
                            console.warn('⚠️ Activity slider initialization failed:', error.message);
                        }
                    } else {
                        console.log('ℹ️ Activity slider container not found on this page');
                    }
                } else {
                    console.log('ℹ️ initializeActivitySlider function not available');
                }
            } else {
                console.log('ℹ️ Activity slider not enabled for this page');
            }

            // *** ตรวจสอบการกลับมาจาก login ***
            checkReturnFromLogin();

            // Auto search ถ้ามี parameter
            if (autoSearch) {
                document.getElementById('search_queue_id').value = autoSearch.toUpperCase();
                searchQueue();
            }

            // แสดงข้อความต้อนรับถ้ามาจากการจองสำเร็จ
            if (fromSuccess) {
                showWelcomeMessage();
            }

            console.log('✅ Follow queue page initialized successfully');

        } catch (error) {
            console.error('❌ Error initializing follow queue page:', error);
        }
    });

    // *** ตรวจสอบและสร้าง functions ที่จำเป็น ***
    const requiredFunctions = {
        setupEventListeners: function () {
            console.log('📋 Setting up event listeners...');

            // ปุ่มค้นหา
            const searchBtn = document.getElementById('search_queue_btn');
            if (searchBtn) {
                searchBtn.addEventListener('click', searchQueue);
            }

            // Enter key ในช่องค้นหา
            const searchInput = document.getElementById('search_queue_id');
            if (searchInput) {
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        searchQueue();
                    }
                });

                // แปลงเป็นตัวพิมพ์ใหญ่อัตโนมัติ
                searchInput.addEventListener('input', function (e) {
                    e.target.value = e.target.value.toUpperCase();
                });
            }
        },

        checkReturnFromLogin: function () {
            console.log('📋 Checking return from login...');
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const isFromLogin = urlParams.has('from_login') || urlParams.has('redirect');

                // ตรวจสอบ sessionStorage
                const shouldReturnToSearch = sessionStorage.getItem('return_to_search');
                const savedQueueId = sessionStorage.getItem('queue_search_after_login');

                if (isLoggedIn && (isFromLogin || shouldReturnToSearch)) {
                    // ลบข้อมูลจาก sessionStorage
                    sessionStorage.removeItem('return_to_search');
                    sessionStorage.removeItem('queue_search_after_login');
                    sessionStorage.removeItem('redirect_after_login');

                    // ลบ parameter จาก URL
                    if (urlParams.has('from_login') || urlParams.has('redirect')) {
                        const newUrl = window.location.pathname;
                        window.history.replaceState({}, document.title, newUrl);
                    }

                    if (savedQueueId) {
                        // แสดงข้อความต้อนรับ
                        Swal.fire({
                            icon: 'success',
                            title: 'เข้าสู่ระบบสำเร็จ!',
                            html: `
                            <div style="text-align: center;">
                                <p style="color: #28a745; font-size: 1.1rem;">
                                    ยินดีต้อนรับ <strong>${userType === 'public' ? 'สมาชิก' : 'เจ้าหน้าที่'}</strong>
                                </p>
                                <div style="background: #e6edff; padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                                    <p style="margin: 0; color: #4c63d2;">
                                        <i class="fas fa-search me-2"></i>
                                        กำลังค้นหาคิว <strong>${savedQueueId}</strong>
                                    </p>
                                </div>
                            </div>
                        `,
                            timer: 3000,
                            showConfirmButton: false
                        });

                        // ใส่หมายเลขคิวและค้นหา
                        setTimeout(() => {
                            document.getElementById('search_queue_id').value = savedQueueId.toUpperCase();
                            searchQueue();
                        }, 1000);
                    } else {
                        // Login สำเร็จแต่ไม่มีคิวให้ค้นหา
                        Swal.fire({
                            icon: 'success',
                            title: 'เข้าสู่ระบบสำเร็จ!',
                            text: `ยินดีต้อนรับ ${userType === 'public' ? 'สมาชิก' : 'เจ้าหน้าที่'}`,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                }

            } catch (error) {
                console.error('Error checking return from login:', error);
            }
        },

        searchQueue: function () {
            console.log('📋 Searching queue...');
            // Implementation จะอยู่ด้านล่าง
        },

        showWelcomeMessage: function () {
            console.log('📋 Showing welcome message...');
            Swal.fire({
                icon: 'success',
                title: 'จองคิวสำเร็จ!',
                text: 'คุณสามารถติดตามสถานะคิวได้ที่นี่',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: 'linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%)',
                color: '#155724'
            });
        }
    };

    // สร้าง functions ที่จำเป็น
    Object.keys(requiredFunctions).forEach(funcName => {
        if (typeof window[funcName] === 'undefined') {
            window[funcName] = requiredFunctions[funcName];
            console.log(`🔧 Created function: ${funcName}`);
        }
    });


    // *** เพิ่ม: Debug reCAPTCHA variables ตั้งแต่เริ่มต้น ***
    console.log('🔑 Initial reCAPTCHA check for Queue Search:');
    console.log('- RECAPTCHA_SITE_KEY:', typeof window.RECAPTCHA_SITE_KEY !== 'undefined' ? window.RECAPTCHA_SITE_KEY : 'UNDEFINED');
    console.log('- recaptchaReady:', typeof window.recaptchaReady !== 'undefined' ? window.recaptchaReady : 'UNDEFINED');
    console.log('- SKIP_RECAPTCHA_FOR_DEV:', typeof window.SKIP_RECAPTCHA_FOR_DEV !== 'undefined' ? window.SKIP_RECAPTCHA_FOR_DEV : 'UNDEFINED');
    console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

    // *** ฟังก์ชันค้นหาคิว - เพิ่ม reCAPTCHA ***
    async function searchQueue() {
        const queueId = document.getElementById('search_queue_id').value.trim();

        if (!queueId) {
            showAlert('warning', 'กรุณากรอกหมายเลขคิว', 'กรุณากรอกหมายเลขคิวที่ต้องการค้นหา');
            return;
        }

        const searchBtn = document.getElementById('search_queue_btn');
        if (!searchBtn) {
            console.error('Search button not found');
            return;
        }

        const originalContent = searchBtn.innerHTML;

        // แสดง loading
        searchBtn.disabled = true;
        searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังค้นหา...';

        console.log('📝 Queue search submitted - ID:', queueId);

        // *** เพิ่ม: Debug reCAPTCHA status แบบละเอียด ***
        console.log('🔍 Checking reCAPTCHA status...');
        console.log('- RECAPTCHA_SITE_KEY:', window.RECAPTCHA_SITE_KEY);
        console.log('- recaptchaReady:', window.recaptchaReady);
        console.log('- SKIP_RECAPTCHA_FOR_DEV:', window.SKIP_RECAPTCHA_FOR_DEV);
        console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

        // *** เพิ่ม: ตรวจสอบเงื่อนไข reCAPTCHA แบบละเอียด ***
        const hasRecaptchaKey = window.RECAPTCHA_SITE_KEY && window.RECAPTCHA_SITE_KEY !== '';
        const isRecaptchaReady = window.recaptchaReady === true;
        const isNotSkipDev = !window.SKIP_RECAPTCHA_FOR_DEV;
        const isGrecaptchaAvailable = typeof grecaptcha !== 'undefined';

        console.log('🔍 reCAPTCHA condition check:');
        console.log('- hasRecaptchaKey:', hasRecaptchaKey);
        console.log('- isRecaptchaReady:', isRecaptchaReady);
        console.log('- isNotSkipDev:', isNotSkipDev);
        console.log('- isGrecaptchaAvailable:', isGrecaptchaAvailable);

        const shouldUseRecaptcha = hasRecaptchaKey && isRecaptchaReady && isNotSkipDev && isGrecaptchaAvailable;
        console.log('🔍 Should use reCAPTCHA:', shouldUseRecaptcha);

        try {
            // ตรวจสอบว่ามี reCAPTCHA หรือไม่
            if (shouldUseRecaptcha) {
                console.log('🛡️ Executing reCAPTCHA...');

                try {
                    const recaptchaToken = await executeRecaptchaForQueue();
                    await performQueueSearchWithRecaptcha(queueId, recaptchaToken, searchBtn, originalContent);
                } catch (recaptchaError) {
                    console.error('❌ reCAPTCHA execution failed:', recaptchaError);
                    console.log('🔄 Falling back to search without reCAPTCHA');
                    await performQueueSearchWithoutRecaptcha(queueId, searchBtn, originalContent);
                }
            } else {
                console.log('⚠️ reCAPTCHA not available, searching without verification');
                console.log('📋 Reasons breakdown:');
                console.log('- SITE_KEY exists:', !!window.RECAPTCHA_SITE_KEY);
                console.log('- reCAPTCHA ready:', !!window.recaptchaReady);
                console.log('- Skip dev mode:', !!window.SKIP_RECAPTCHA_FOR_DEV);
                console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

                await performQueueSearchWithoutRecaptcha(queueId, searchBtn, originalContent);
            }

        } catch (error) {
            console.error('Search error:', error);
            showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถค้นหาข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
            restoreQueueSearchButton(searchBtn, originalContent);
        }
    }

    // *** เพิ่ม: Execute reCAPTCHA สำหรับ Queue Search ***
    async function executeRecaptchaForQueue() {
        return new Promise((resolve, reject) => {
            grecaptcha.ready(function () {
                console.log('🔧 grecaptcha.ready() called for queue search');

                grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                    action: 'queue_search'
                }).then(function (token) {
                    console.log('✅ reCAPTCHA token received for queue search:', token.substring(0, 50) + '...');
                    console.log('📏 Token length:', token.length);
                    resolve(token);
                }).catch(function (error) {
                    console.error('❌ reCAPTCHA execution failed for queue search:', error);
                    reject(error);
                });
            });
        });
    }

    // *** เพิ่ม: Queue Search Function พร้อม reCAPTCHA ***
    async function performQueueSearchWithRecaptcha(queueId, recaptchaToken, searchBtn, originalContent) {
        console.log('📤 Submitting queue search with reCAPTCHA token...');

        try {
            const formData = new FormData();
            formData.append('queue_id', queueId);

            // ส่งข้อมูลผู้ใช้เพื่อตรวจสอบสิทธิ์
            formData.append('user_type', userType);
            if (userId) {
                formData.append('user_id', userId);
            }
            formData.append('is_logged_in', isLoggedIn ? '1' : '0');

            // *** เพิ่ม: reCAPTCHA parameters ***
            formData.append('g-recaptcha-response', recaptchaToken);
            formData.append('recaptcha_action', 'queue_search');
            formData.append('recaptcha_source', 'queue_search_form');
            formData.append('ajax_request', '1');
            formData.append('client_timestamp', new Date().toISOString());
            formData.append('user_agent_info', navigator.userAgent);
            formData.append('is_anonymous', '0');

            console.log('📦 FormData contents for queue search:');
            for (let [key, value] of formData.entries()) {
                if (key === 'g-recaptcha-response') {
                    console.log('- ' + key + ':', value.substring(0, 50) + '...');
                } else {
                    console.log('- ' + key + ':', value);
                }
            }

            console.log('🔍 Searching queue with user permission:', {
                queue_id: queueId,
                user_type: userType,
                user_id: userId,
                is_logged_in: isLoggedIn,
                has_recaptcha: true
            });

            const response = await fetch(searchUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            handleQueueSearchResponse(result);

        } catch (error) {
            handleQueueSearchError(error);
        } finally {
            restoreQueueSearchButton(searchBtn, originalContent);
        }
    }

    // *** เพิ่ม: Queue Search Function แบบปกติ ***
    async function performQueueSearchWithoutRecaptcha(queueId, searchBtn, originalContent) {
        console.log('📤 Submitting queue search without reCAPTCHA...');

        try {
            const formData = new FormData();
            formData.append('queue_id', queueId);

            // ส่งข้อมูลผู้ใช้เพื่อตรวจสอบสิทธิ์
            formData.append('user_type', userType);
            if (userId) {
                formData.append('user_id', userId);
            }
            formData.append('is_logged_in', isLoggedIn ? '1' : '0');
            formData.append('dev_mode', '1');

            console.log('🔍 Searching queue with user permission:', {
                queue_id: queueId,
                user_type: userType,
                user_id: userId,
                is_logged_in: isLoggedIn,
                has_recaptcha: false
            });

            const response = await fetch(searchUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            handleQueueSearchResponse(result);

        } catch (error) {
            handleQueueSearchError(error);
        } finally {
            restoreQueueSearchButton(searchBtn, originalContent);
        }
    }

    // *** เพิ่ม: จัดการ Response สำหรับ Queue Search ***
    function handleQueueSearchResponse(result) {
        if (result.success) {
            displaySearchResults(result.data);

            // เลื่อนไปยังผลการค้นหา
            document.getElementById('search_results').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        } else {
            // จัดการข้อผิดพลาดตามประเภท
            if (result.error_type === 'recaptcha_failed') {
                showAlert('error', 'การยืนยันความปลอดภัยไม่ผ่าน', 'กรุณาลองใหม่อีกครั้ง');
            } else if (result.error_type === 'recaptcha_missing') {
                showAlert('error', 'ไม่พบข้อมูลการยืนยันความปลอดภัย', 'กรุณาลองใหม่อีกครั้ง');
            } else if (result.error_type === 'permission_denied') {
                showPermissionDeniedMessage();
            } else if (result.error_type === 'not_found') {
                showNoResults();
            } else {
                showAlert('error', 'เกิดข้อผิดพลาด', result.message || 'ไม่สามารถค้นหาข้อมูลได้');
            }
        }
    }

    // *** เพิ่ม: จัดการ Error สำหรับ Queue Search ***
    function handleQueueSearchError(error) {
        console.error('Queue search error:', error);
        showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถค้นหาข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
    }

    // *** เพิ่ม: คืนค่าปุ่มเป็นสถานะเดิม ***
    function restoreQueueSearchButton(searchBtn, originalContent) {
        if (searchBtn) {
            searchBtn.disabled = false;
            searchBtn.innerHTML = originalContent;
        }
    }

    // *** ฟังก์ชันแสดงผลการค้นหา ***
    function displaySearchResults(data) {
        const searchResults = document.getElementById('search_results');
        const noResults = document.getElementById('no_results');

        if (!searchResults) {
            console.error('Search results container not found');
            return;
        }

        // ซ่อนข้อความไม่พบผลการค้นหา
        if (noResults) {
            noResults.style.display = 'none';
        }

        // แสดงข้อมูลคิว
        displayQueueDetails(data.queue_info);

        // แสดงประวัติ
        displayQueueHistory(data.queue_history);

        // แสดงไฟล์แนบ
        displayQueueFiles(data.queue_files);

        // แสดงปุ่มยกเลิก (ถ้าสามารถยกเลิกได้)
        displayQueueActions(data.queue_info);

        // แสดงผลการค้นหา
        searchResults.style.display = 'block';
        searchResults.classList.add('fade-in-up');
    }

    // *** ฟังก์ชันแสดงรายละเอียดคิว ***
    function displayQueueDetails(queueInfo) {
        const section = document.getElementById('queue_details_section');
        if (!section) return;

        const statusClass = getStatusClass(queueInfo.queue_status);
        const statusColor = getStatusColor(queueInfo.queue_status);

        const html = `
        <div class="row align-items-center mb-4">
            <div class="col-auto">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, ${statusColor}15 0%, ${statusColor}25 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px ${statusColor}30;">
                    <i class="fas fa-clipboard-check" style="font-size: 2.5rem; color: ${statusColor};"></i>
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h3 style="color: #2c3e50; margin: 0; font-weight: 700;">
                        หมายเลขคิว: ${queueInfo.queue_id}
                    </h3>
                    <span class="queue-status-badge ${statusClass}" style="font-size: 1rem; padding: 0.5rem 1rem;">
                        ${queueInfo.queue_status}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="info-group mb-3">
                    <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block;">
                        <i class="fas fa-clipboard-list me-2" style="color: #667eea;"></i>เรื่องที่ต้องการติดต่อ
                    </label>
                    <p style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 1rem; border-radius: 12px; margin: 0; border: 1px solid rgba(102, 126, 234, 0.1);">
                        ${queueInfo.queue_topic}
                    </p>
                </div>
                
                <div class="info-group mb-3">
                    <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block;">
                        <i class="fas fa-calendar me-2" style="color: #667eea;"></i>วันที่และเวลา
                    </label>
                    <p style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 1rem; border-radius: 12px; margin: 0; border: 1px solid rgba(102, 126, 234, 0.1);">
                        ${formatDateTime(queueInfo.queue_date)}
                    </p>
                </div>
                
                <div class="info-group mb-3">
                    <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block;">
                        <i class="fas fa-phone me-2" style="color: #667eea;"></i>เบอร์โทรศัพท์
                    </label>
                    <p style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 1rem; border-radius: 12px; margin: 0; border: 1px solid rgba(102, 126, 234, 0.1);">
                        ${censorPhoneNumber(queueInfo.queue_phone || 'ไม่ระบุ')}
                    </p>
                    <small style="color: #6c757d; font-style: italic; margin-top: 0.25rem; display: block;">
                        <i class="fas fa-shield-alt me-1"></i>เบอร์โทรศัพท์ถูกเซ็นเชอร์เพื่อปกป้องความเป็นส่วนตัว
                    </small>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-group mb-3">
                    <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block;">
                        <i class="fas fa-align-left me-2" style="color: #667eea;"></i>รายละเอียด
                    </label>
                    <div style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 1rem; border-radius: 12px; border: 1px solid rgba(102, 126, 234, 0.1); max-height: 200px; overflow-y: auto;">
                        ${queueInfo.queue_detail.replace(/\n/g, '<br>')}
                    </div>
                </div>
            </div>
        </div>
    `;

        section.innerHTML = html;
    }

    // *** เพิ่ม: ตรวจสอบการโหลด reCAPTCHA เมื่อ DOM พร้อม ***
    document.addEventListener('DOMContentLoaded', function () {
        // *** เพิ่ม: ตรวจสอบการโหลด reCAPTCHA ***
        if (window.RECAPTCHA_SITE_KEY && !window.recaptchaReady) {
            console.log('⏳ Waiting for reCAPTCHA to load for queue search...');

            let checkInterval = setInterval(function () {
                if (window.recaptchaReady) {
                    console.log('✅ reCAPTCHA is now ready for queue search');
                    clearInterval(checkInterval);
                }
            }, 100);

            setTimeout(function () {
                if (!window.recaptchaReady) {
                    console.log('⚠️ reCAPTCHA timeout after 10 seconds for queue search');
                    clearInterval(checkInterval);
                }
            }, 10000);
        }
    });
    //////////////////////////////////////////////////////////////////////////
    // *** Helper Functions ***
    function getStatusClass(status) {
        const statusMap = {
            'รอยืนยันการจอง': 'status-รอยืนยันการจอง',
            'รอยืนยัน': 'status-รอยืนยัน',
            'ยืนยันแล้ว': 'status-ยืนยันแล้ว',
            'อนุมัติแล้ว': 'status-อนุมัติแล้ว',
            'กำลังดำเนินการ': 'status-กำลังดำเนินการ',
            'ดำเนินการ': 'status-ดำเนินการ',
            'เสร็จสิ้น': 'status-เสร็จสิ้น',
            'สำเร็จ': 'status-สำเร็จ',
            'ยกเลิก': 'status-ยกเลิก',
            'ปฏิเสธ': 'status-ปฏิเสธ',
            'รอเอกสาร': 'status-รอเอกสาร'
        };
        return statusMap[status] || 'status-unknown';
    }

    function getStatusColor(status) {
        const colorMap = {
            'รอยืนยันการจอง': '#ffc107',
            'รอยืนยัน': '#ffc107',
            'ยืนยันแล้ว': '#20c997',
            'อนุมัติแล้ว': '#20c997',
            'กำลังดำเนินการ': '#007bff',
            'ดำเนินการ': '#007bff',
            'เสร็จสิ้น': '#28a745',
            'สำเร็จ': '#28a745',
            'ยกเลิก': '#dc3545',
            'ปฏิเสธ': '#dc3545',
            'รอเอกสาร': '#fd7e14'
        };
        return colorMap[status] || '#6c757d';
    }

    // เซ็นเชอร์เบอร์โทรศัพท์ - แสดงแค่ 4 ตัวหลัง
    function censorPhoneNumber(phone) {
        if (!phone || phone === 'ไม่ระบุ') return 'ไม่ระบุ';

        // ลบช่องว่างและเครื่องหมายต่างๆ
        const cleanPhone = phone.replace(/[\s\-\(\)]/g, '');

        if (cleanPhone.length <= 4) return phone;

        const lastFour = cleanPhone.slice(-4);
        const masked = '*'.repeat(cleanPhone.length - 4);

        // จัดรูปแบบให้ดูดี เช่น ***-***-1234
        if (cleanPhone.length === 10) {
            return `***-***-${lastFour}`;
        } else if (cleanPhone.length === 9) {
            return `***-**-${lastFour}`;
        } else {
            return `${masked}-${lastFour}`;
        }
    }

    function formatDateTime(dateString) {
        if (!dateString) return 'ไม่ระบุ';

        try {
            const date = new Date(dateString);
            const thaiMonths = [
                'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
            ];

            const day = date.getDate();
            const month = thaiMonths[date.getMonth()];
            const year = date.getFullYear() + 543;
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');

            return `${day} ${month} ${year} เวลา ${hours}:${minutes} น.`;
        } catch (error) {
            return dateString;
        }
    }

    function showAlert(icon, title, text, timer = null) {
        const config = {
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: '#667eea',
            confirmButtonText: 'ตกลง'
        };

        if (timer) {
            config.timer = timer;
            config.showConfirmButton = false;
            config.toast = true;
            config.position = 'top-end';
        }

        Swal.fire(config);
    }

    // *** ฟังก์ชันอื่นๆ ที่จำเป็น (placeholder) ***
    function displayQueueHistory(history) {
        console.log('📋 Displaying queue history:', history);
        // Implementation here
    }

    function displayQueueFiles(files) {
        console.log('📋 Displaying queue files:', files);
        // Implementation here
    }

    function displayQueueActions(queueInfo) {
        console.log('📋 Displaying queue actions:', queueInfo);
        // Implementation here
    }

    function showPermissionDeniedMessage() {
        showAlert('warning', 'ไม่มีสิทธิ์เข้าถึง', 'คุณไม่มีสิทธิ์ในการดูข้อมูลคิวนี้');
    }

    function showNoResults() {
        showAlert('error', 'ไม่พบข้อมูลคิว', 'ไม่พบหมายเลขคิวที่ระบุ กรุณาตรวจสอบหมายเลขคิวอีกครั้ง');
    }

    function resetSearch() {
        const searchInput = document.getElementById('search_queue_id');
        const searchResults = document.getElementById('search_results');

        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }

        if (searchResults) {
            searchResults.style.display = 'none';
        }

        // ปิด SweetAlert ถ้ามีเปิดอยู่
        if (Swal.isVisible()) {
            Swal.close();
        }
    }

    function searchQueueById(queueId) {
        const searchInput = document.getElementById('search_queue_id');
        if (searchInput) {
            searchInput.value = queueId;
            searchQueue();
        }
    }

    // *** ตรวจสอบว่า DOM elements ที่จำเป็นมีอยู่หรือไม่ ***
    function checkRequiredElements() {
        const requiredElements = [
            'search_queue_id',
            'search_queue_btn',
            'search_results'
        ];

        const missingElements = [];

        requiredElements.forEach(elementId => {
            if (!document.getElementById(elementId)) {
                missingElements.push(elementId);
            }
        });

        if (missingElements.length > 0) {
            console.error('❌ Missing required elements:', missingElements);
            return false;
        }

        console.log('✅ All required elements found');
        return true;
    }

    // เรียกใช้การตรวจสอบ elements หลังจากโหลดเสร็จ
    setTimeout(() => {
        checkRequiredElements();
    }, 100);

    // *** ฟังก์ชันแสดงประวัติการเปลี่ยนสถานะ ***
    function displayQueueHistory(history) {
        const section = document.getElementById('queue_history_section');
        if (!section) return;

        if (!history || history.length === 0) {
            section.innerHTML = '';
            return;
        }

        let historyHtml = `
        <h4 style="color: #2c3e50; margin-bottom: 0.5rem; font-weight: 700;">
            <i class="fas fa-history me-2" style="color: #667eea;"></i>ประวัติการเปลี่ยนสถานะ
        </h4>
        <p style="color: #6c757d; font-style: italic; margin-bottom: 1.5rem; font-size: 0.9rem;">
            <i class="fas fa-shield-alt me-1"></i>ชื่อเจ้าหน้าที่ถูกเซ็นเชอร์เพื่อปกป้องความเป็นส่วนตัว
        </p>
        <div class="status-timeline">
    `;

        history.forEach((item, index) => {
            const isLatest = index === 0;
            const timelineClass = isLatest ? 'current' : '';

            historyHtml += `
            <div class="status-timeline-item ${timelineClass}">
                <div style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 1.5rem; border-radius: 15px; border: 1px solid rgba(102, 126, 234, 0.1); box-shadow: 0 2px 8px rgba(102, 126, 234, 0.05);">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="queue-status-badge ${getStatusClass(item.queue_detail_status)}" style="font-size: 0.9rem;">
                            ${item.queue_detail_status}
                        </span>
                        <small style="color: #6c757d; font-weight: 500;">
                            <i class="fas fa-clock me-1"></i>
                            ${formatDateTime(item.queue_detail_date)}
                        </small>
                    </div>
                    <p style="margin: 0; color: #495057; font-weight: 500;">
                        <i class="fas fa-user me-2"></i>${censorFullName(item.queue_detail_by)}
                    </p>
                    ${item.queue_detail_com ? `
                        <p style="margin: 0.5rem 0 0 0; color: #6c757d; font-style: italic;">
                            "${item.queue_detail_com}"
                        </p>
                    ` : ''}
                </div>
            </div>
        `;
        });

        historyHtml += '</div>';
        section.innerHTML = historyHtml;
    }

    // *** ฟังก์ชันแสดงไฟล์แนบ ***
    function displayQueueFiles(files) {
        const section = document.getElementById('queue_files_section');
        if (!section) return;

        if (!files || files.length === 0) {
            section.innerHTML = '';
            return;
        }

        let filesHtml = `
        <h4 style="color: #2c3e50; margin-bottom: 1.5rem; font-weight: 700;">
            <i class="fas fa-paperclip me-2" style="color: #667eea;"></i>ไฟล์แนบ
        </h4>
        <div class="row">
    `;

        files.forEach(file => {
            const isImage = /\.(jpg|jpeg|png|gif)$/i.test(file.queue_file_original_name);
            const isPDF = /\.pdf$/i.test(file.queue_file_original_name);
            const fileUrl = `<?= site_url('Queue/download_file/') ?>${file.queue_file_name}`;

            if (isImage) {
                // แสดง preview รูปภาพ
                filesHtml += `
                <div class="col-md-4 mb-3">
                    <div class="file-preview-item image-preview" onclick="viewImagePreview('${fileUrl}', '${file.queue_file_original_name}')" style="cursor: pointer;">
                        <div class="image-container" style="width: 100%; height: 200px; border-radius: 12px; overflow: hidden; background: #f8f9fa; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; position: relative;">
                            <img src="${fileUrl}" alt="${file.queue_file_original_name}" style="max-width: 100%; max-height: 100%; object-fit: cover; border-radius: 8px; opacity: 0; transition: opacity 0.3s;" onload="this.style.opacity='1'" onerror="showImageError(this)">
                            <div class="image-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; border-radius: 8px;">
                                <i class="fas fa-eye" style="color: white; font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <h6 style="margin-bottom: 0.5rem; color: #2c3e50; font-weight: 600; word-break: break-word; font-size: 0.9rem;">
                            ${file.queue_file_original_name}
                        </h6>
                        <p style="margin: 0; color: #6c757d; font-size: 0.8rem;">
                            <i class="fas fa-image me-1" style="color: #28a745;"></i>
                            ${formatFileSize(file.queue_file_size)}
                        </p>
                    </div>
                </div>
            `;
            } else if (isPDF) {
                // แสดง PDF ให้เปิดใน tab ใหม่
                filesHtml += `
                <div class="col-md-4 mb-3">
                    <div class="file-preview-item pdf-preview" onclick="viewPDFPreview('${fileUrl}', '${file.queue_file_original_name}')" style="cursor: pointer;">
                        <div class="text-center mb-3" style="padding: 2rem; background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.2) 100%); border-radius: 12px;">
                            <i class="fas fa-file-pdf" style="font-size: 3rem; color: #dc3545;"></i>
                        </div>
                        <h6 style="margin-bottom: 0.5rem; color: #2c3e50; font-weight: 600; word-break: break-word; font-size: 0.9rem;">
                            ${file.queue_file_original_name}
                        </h6>
                        <p style="margin: 0; color: #6c757d; font-size: 0.8rem; margin-bottom: 1rem;">
                            <i class="fas fa-file-pdf me-1" style="color: #dc3545;"></i>
                            ${formatFileSize(file.queue_file_size)}
                        </p>
                        <div class="text-center">
                            <div class="btn-group w-100" role="group">
                                <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); viewPDFPreview('${fileUrl}', '${file.queue_file_original_name}')" style="border-radius: 8px 0 0 8px;">
                                    <i class="fas fa-eye me-1"></i>ดู
                                </button>
                                <button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation(); downloadFile('${file.queue_file_name}')" style="border-radius: 0 8px 8px 0;">
                                    <i class="fas fa-download me-1"></i>ดาวน์โหลด
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            } else {
                // ไฟล์ประเภทอื่นๆ ให้ดาวน์โหลด
                const iconClass = 'fas fa-file';
                const iconColor = '#6c757d';

                filesHtml += `
                <div class="col-md-4 mb-3">
                    <div class="file-preview-item" onclick="downloadFile('${file.queue_file_name}')" style="cursor: pointer;">
                        <div class="text-center mb-3" style="padding: 2rem; background: linear-gradient(135deg, rgba(108, 117, 125, 0.1) 0%, rgba(108, 117, 125, 0.2) 100%); border-radius: 12px;">
                            <i class="${iconClass}" style="font-size: 3rem; color: ${iconColor};"></i>
                        </div>
                        <h6 style="margin-bottom: 0.5rem; color: #2c3e50; font-weight: 600; word-break: break-word; font-size: 0.9rem;">
                            ${file.queue_file_original_name}
                        </h6>
                        <p style="margin: 0; color: #6c757d; font-size: 0.8rem; margin-bottom: 1rem;">
                            <i class="fas fa-hdd me-1"></i>
                            ${formatFileSize(file.queue_file_size)}
                        </p>
                        <div class="text-center">
                            <button class="btn btn-sm btn-outline-primary w-100" style="border-radius: 8px;">
                                <i class="fas fa-download me-1"></i>ดาวน์โหลด
                            </button>
                        </div>
                    </div>
                </div>
            `;
            }
        });

        filesHtml += '</div>';
        section.innerHTML = filesHtml;
    }

    // *** ฟังก์ชันแสดงปุ่มยกเลิกคิว ***
    function displayQueueActions(queueInfo) {
        const section = document.getElementById('queue_actions_section');
        if (!section) return;

        // ตรวจสอบสิทธิ์ในการยกเลิกอย่างเข้มงวด
        const canCancel = checkCancelPermission(queueInfo);

        if (canCancel) {
            section.innerHTML = `
            <div style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, rgba(220, 53, 69, 0.1) 100%); padding: 1.5rem; border-radius: 15px; border: 1px solid rgba(220, 53, 69, 0.2);">
                <h5 style="color: #dc3545; margin-bottom: 1rem; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle me-2"></i>การยกเลิกคิว
                </h5>
                <p style="color: #6c757d; margin-bottom: 1rem; font-size: 0.9rem;">
                    หากต้องการยกเลิกคิว กรุณาแจ้งเหตุผลเพื่อให้เจ้าหน้าที่ทราบ
                </p>
                <button type="button" class="btn btn-danger" onclick="cancelQueue('${queueInfo.queue_id}')" style="border-radius: 12px; padding: 0.8rem 2rem; font-weight: 600; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);">
                    <i class="fas fa-times-circle me-2"></i>ยกเลิกคิว
                </button>
            </div>
        `;
        } else {
            section.innerHTML = '';
        }
    }

    // *** ฟังก์ชันตรวจสอบสิทธิ์การยกเลิกคิว ***
    function checkCancelPermission(queueInfo) {
        // Staff สามารถยกเลิกได้ทุกคิว
        if (userType === 'staff' && isLoggedIn) {
            return !['ยกเลิก', 'เสร็จสิ้น', 'คิวได้ถูกยกเลิก'].includes(queueInfo.queue_status);
        }

        // Public user สามารถยกเลิกได้เฉพาะคิวของตนเอง
        if (userType === 'public' && isLoggedIn && userId) {
            const isOwner = (queueInfo.queue_user_type === 'public' &&
                queueInfo.queue_user_id == userId);
            const canCancelStatus = !['ยกเลิก', 'เสร็จสิ้น', 'คิวได้ถูกยกเลิก'].includes(queueInfo.queue_status);

            return isOwner && canCancelStatus;
        }

        // Guest ไม่สามารถยกเลิกได้
        return false;
    }

    // *** ฟังก์ชันเซ็นเชอร์ชื่อ ***
    function censorFullName(fullName) {
        if (!fullName) return 'ไม่ระบุ';

        // แยกชื่อด้วยช่องว่าง
        const nameParts = fullName.trim().split(/\s+/);

        if (nameParts.length === 1) {
            // ถ้ามีแค่ชื่อเดียว
            return nameParts[0];
        } else if (nameParts.length === 2) {
            // ถ้ามี 2 ส่วน (คำนำหน้า + ชื่อ หรือ ชื่อ + นามสกุล)
            const firstPart = nameParts[0];
            const secondPart = nameParts[1];

            // ตรวจสอบว่าส่วนแรกเป็นคำนำหน้าหรือไม่
            const prefixes = ['นาย', 'นาง', 'นางสาว', 'เด็กชาย', 'เด็กหญิง', 'Mr.', 'Mrs.', 'Miss', 'Ms.'];
            const isPrefix = prefixes.some(prefix => firstPart.includes(prefix));

            if (isPrefix) {
                // ถ้าเป็นคำนำหน้า แสดง "คำนำหน้า + ชื่อ"
                return `${firstPart} ${secondPart}`;
            } else {
                // ถ้าไม่เป็นคำนำหน้า แสดงแค่ชื่อจริง ซ่อนนามสกุล
                return `${firstPart} *****`;
            }
        } else {
            // ถ้ามีมากกว่า 2 ส่วน
            const firstPart = nameParts[0];
            const prefixes = ['นาย', 'นาง', 'นางสาว', 'เด็กชาย', 'เด็กหญิง', 'Mr.', 'Mrs.', 'Miss', 'Ms.'];
            const isPrefix = prefixes.some(prefix => firstPart.includes(prefix));

            if (isPrefix && nameParts.length >= 3) {
                // มีคำนำหน้า + ชื่อ + นามสกุล
                return `${nameParts[0]} ${nameParts[1]} *****`;
            } else {
                // ไม่มีคำนำหน้า แสดงแค่ชื่อแรก
                return `${nameParts[0]} *****`;
            }
        }
    }

    // *** ฟังก์ชันจัดการไฟล์ ***
    function formatFileSize(bytes) {
        if (!bytes) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function downloadFile(fileName) {
        window.open(`<?= site_url('Queue/download_file/') ?>${fileName}`, '_blank');
    }

    function viewImagePreview(imageUrl, fileName) {
        Swal.fire({
            title: fileName,
            html: `
            <div style="text-align: center;">
                <img src="${imageUrl}" alt="${fileName}" style="max-width: 100%; max-height: 70vh; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); opacity: 0; transition: opacity 0.3s;" onload="this.style.opacity='1'">
            </div>
        `,
            showCloseButton: true,
            showConfirmButton: false,
            width: 'auto',
            padding: '1rem',
            background: '#fff',
            customClass: {
                popup: 'image-preview-popup'
            }
        });
    }

    function viewPDFPreview(pdfUrl, fileName) {
        // เปิด PDF ในหน้าใหม่
        const newWindow = window.open('', '_blank');
        newWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>${fileName}</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { 
                    margin: 0; 
                    padding: 0; 
                    background: #f5f5f5; 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                }
                .header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 1rem 2rem;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .header h1 {
                    margin: 0;
                    font-size: 1.2rem;
                    font-weight: 600;
                }
                .pdf-container {
                    width: 100%;
                    height: calc(100vh - 80px);
                    border: none;
                }
                .loading {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 200px;
                    font-size: 1.1rem;
                    color: #666;
                }
                .download-btn {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                    color: white;
                    border: none;
                    padding: 12px 20px;
                    border-radius: 50px;
                    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
                    cursor: pointer;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    z-index: 1000;
                }
                .download-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>📄 ${fileName}</h1>
            </div>
            <div class="loading" id="loading">
                <div style="text-align: center;">
                    <div style="display: inline-block; width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 10px;"></div>
                    <br>กำลังโหลด PDF...
                </div>
            </div>
            <embed src="${pdfUrl}" type="application/pdf" class="pdf-container" onload="document.getElementById('loading').style.display='none';">
            <button class="download-btn" onclick="window.open('${pdfUrl}', '_self')">
                📥 ดาวน์โหลด
            </button>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        </body>
        </html>
    `);
        newWindow.document.close();
    }

    function showImageError(imgElement) {
        if (imgElement && imgElement.parentElement) {
            imgElement.style.display = 'none';
            imgElement.parentElement.innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #dc3545;">
                <i class="fas fa-image" style="font-size: 3rem; margin-bottom: 0.5rem;"></i>
                <span style="font-size: 0.9rem;">ไม่สามารถโหลดรูปภาพได้</span>
            </div>
        `;
        }
    }

    // *** ฟังก์ชันยกเลิกคิว ***
    async function cancelQueue(queueId) {
        // ตรวจสอบสิทธิ์ก่อนยกเลิก
        if (!isLoggedIn) {
            showAlert('warning', 'กรุณาเข้าสู่ระบบ', 'จำเป็นต้องเข้าสู่ระบบก่อนยกเลิกคิว');
            return;
        }

        if (userType !== 'staff' && userType !== 'public') {
            showAlert('error', 'ไม่มีสิทธิ์', 'คุณไม่มีสิทธิ์ในการยกเลิกคิว');
            return;
        }

        const result = await Swal.fire({
            title: 'ยืนยันการยกเลิกคิว?',
            html: `
            <div style="text-align: center; margin: 1rem 0;">
                <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.25) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2.5rem; color: #dc3545;"></i>
                </div>
                <p style="margin-bottom: 1rem; color: #666;">คุณต้องการยกเลิกคิว <strong>${queueId}</strong> หรือไม่?</p>
                <textarea id="cancel_reason" class="swal2-textarea" placeholder="กรุณาระบุเหตุผลในการยกเลิก (ไม่บังคับ)" style="width: 100%; min-height: 80px; border-radius: 8px; border: 1px solid #ddd; padding: 0.5rem;"></textarea>
            </div>
        `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-times-circle me-2"></i>ยกเลิกคิว',
            cancelButtonText: '<i class="fas fa-arrow-left me-2"></i>กลับ',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            preConfirm: () => {
                return document.getElementById('cancel_reason').value.trim();
            }
        });

        if (result.isConfirmed) {
            const cancelReason = result.value || 'ผู้ใช้ยกเลิกด้วยตนเอง';

            try {
                const formData = new FormData();
                formData.append('queue_id', queueId);
                formData.append('cancel_reason', cancelReason);

                // ส่งข้อมูลผู้ใช้เพื่อตรวจสอบสิทธิ์
                formData.append('user_type', userType);
                if (userId) {
                    formData.append('user_id', userId);
                }

                const response = await fetch(cancelUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'ยกเลิกคิวสำเร็จ',
                        text: 'คิวของคุณถูกยกเลิกเรียบร้อยแล้ว',
                        confirmButtonColor: '#28a745'
                    });

                    // ค้นหาใหม่เพื่อแสดงสถานะปัจจุบัน
                    searchQueue();

                } else {
                    if (result.error_type === 'permission_denied') {
                        showAlert('error', 'ไม่มีสิทธิ์', 'คุณไม่มีสิทธิ์ในการยกเลิกคิวนี้');
                    } else {
                        showAlert('error', 'ไม่สามารถยกเลิกคิวได้', result.message);
                    }
                }

            } catch (error) {
                console.error('Cancel error:', error);
                showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถยกเลิกคิวได้ กรุณาลองใหม่อีกครั้ง');
            }
        }
    }

    console.log('✅ Follow queue JavaScript loaded successfully');
</script>