<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
    <title><?php echo get_config_value('fname'); ?> - ระบบแอดมิน</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Google Fonts -->
    <link
        href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Kanit:wght@300;400;500;600;700&display=swap'
        rel='stylesheet'>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <!-- 🎯 ===== TOUR SYSTEM START ===== -->

    <!-- 1. Intro.js Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/minified/introjs.min.css">
    <script src="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/intro.min.js"></script>

    <!-- 2. Custom Tour Styles - White Clean Theme -->
    <style>
        /* ===== MODERN WHITE CLEAN TOOLTIP STYLES ===== */
        .introjs-tooltip {
            border: none !important;
            border-radius: 16px !important;
            background: #ffffff !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 0 1px rgba(0, 0, 0, 0.1) !important;
            font-family: 'Inter', 'Kanit', sans-serif !important;
            min-width: 500px;
            max-width: 550px;
        }

        /* Header Section */
        .introjs-tooltip-header {
            padding: 20px 24px 20px 28px !important;
            background: #ffffff !important;
            border-bottom: 1px solid #e8e8e8 !important;
            border-radius: 16px 16px 0 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        .introjs-tooltip-title {
            color: #1a1a1a !important;
            font-size: 19px !important;
            font-weight: 700 !important;
            margin: 0 !important;
            letter-spacing: -0.3px;
            flex: 1 !important;
            padding-right: 12px !important;
        }

        /* Skip Button in Header (X) */
        .introjs-skipbutton {
            position: relative !important;
            background: transparent !important;
            color: #9ca3af !important;
            border: none !important;
            width: 32px !important;
            height: 32px !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            font-size: 20px !important;
            font-weight: 400 !important;
            line-height: 1 !important;
        }

        .introjs-skipbutton:hover {
            background: #f3f4f6 !important;
            color: #4b5563 !important;
            transform: none !important;
            box-shadow: none !important;
        }

        /* Body Section */
        .introjs-tooltiptext {
            padding: 24px 28px !important;
            color: #2d3748 !important;
            font-size: 15px !important;
            line-height: 1.7 !important;
            background: #ffffff !important;
        }

        .introjs-tooltiptext p {
            margin-bottom: 12px !important;
            color: #2d3748 !important;
        }

        .introjs-tooltiptext strong {
            color: #1a1a1a !important;
            font-weight: 700 !important;
        }

        .introjs-tooltiptext ul {
            margin: 14px 0 !important;
            padding-left: 22px !important;
            color: #2d3748 !important;
        }

        .introjs-tooltiptext li {
            margin-bottom: 8px !important;
            color: #2d3748 !important;
            line-height: 1.6 !important;
        }

        .introjs-tooltiptext small {
            color: #6c757d !important;
            font-size: 13px !important;
        }

        /* Button Section */
        .introjs-tooltipbuttons {
            padding: 18px 28px !important;
            background: #ffffff !important;
            border-radius: 0 0 16px 16px !important;
            display: flex !important;
            gap: 10px !important;
            justify-content: flex-end !important;
            border-top: 1px solid #e8e8e8 !important;
        }

        .introjs-button {
            border: none !important;
            border-radius: 10px !important;
            padding: 11px 24px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            letter-spacing: 0;
        }

        /* Next Button - เทาเข้ม */
        .introjs-nextbutton {
            background: #4b5563 !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 2px 8px rgba(75, 85, 99, 0.2) !important;
        }

        .introjs-nextbutton:hover {
            background: #374151 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(75, 85, 99, 0.3) !important;
        }

        /* Done Button - สีเขียว */
        .introjs-donebutton {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3) !important;
        }

        .introjs-donebutton:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4) !important;
        }

        /* Previous Button */
        .introjs-prevbutton {
            background: #ffffff !important;
            color: #4b5563 !important;
            border: 1.5px solid #d1d5db !important;
        }

        .introjs-prevbutton:hover {
            background: #f9fafb !important;
            border-color: #9ca3af !important;
            transform: translateY(-1px) !important;
        }

        .introjs-button:disabled {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
            transform: none !important;
        }

        /* Highlight Layer */
        .introjs-helperLayer {
            background: #ffffff !important;
            border: 3px solid #4b5563 !important;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.7),
                0 8px 32px rgba(0, 0, 0, 0.25) !important;
            border-radius: 12px !important;
        }

        .introjs-overlay {
            background: rgba(0, 0, 0, 0.7) !important;
        }

        /* Progress Bar */
        .introjs-progress {
            background: #e5e7eb !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            height: 6px !important;
            margin-top: 18px !important;
        }

        .introjs-progressbar {
            background: linear-gradient(90deg, #4b5563 0%, #6b7280 100%) !important;
            border-radius: 12px !important;
            transition: width 0.3s ease !important;
            box-shadow: 0 1px 3px rgba(75, 85, 99, 0.2) !important;
        }

        /* Arrow */
        .introjs-arrow {
            border-color: transparent !important;
        }

        .introjs-arrow.top {
            border-bottom-color: #ffffff !important;
        }

        .introjs-arrow.bottom {
            border-top-color: #ffffff !important;
        }

        .introjs-arrow.left {
            border-right-color: #ffffff !important;
        }

        .introjs-arrow.right {
            border-left-color: #ffffff !important;
        }

        /* Pulse Animation */
        @keyframes intro-pulse {
            0% {
                box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.7),
                    0 0 0 0 rgba(75, 85, 99, 0.5);
            }

            50% {
                box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.7),
                    0 0 0 18px rgba(75, 85, 99, 0);
            }

            100% {
                box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.7),
                    0 0 0 0 rgba(75, 85, 99, 0);
            }
        }

        .introjs-helperLayer.pulse {
            animation: intro-pulse 2.5s infinite !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .introjs-tooltip {
                min-width: 340px !important;
                max-width: 92vw !important;
            }

            .introjs-tooltip-header {
                padding: 18px 20px 18px 24px !important;
            }

            .introjs-tooltip-title {
                font-size: 18px !important;
            }

            .introjs-tooltiptext {
                font-size: 14px !important;
                padding: 20px 24px !important;
            }

            .introjs-button {
                padding: 10px 20px !important;
                font-size: 13px !important;
            }

            .introjs-tooltipbuttons {
                padding: 16px 24px !important;
            }
        }

        @media (max-width: 480px) {
            .introjs-tooltip {
                min-width: 300px !important;
            }

            .introjs-tooltiptext {
                font-size: 13px !important;
                padding: 18px 20px !important;
            }

            .introjs-button {
                padding: 9px 18px !important;
                font-size: 12px !important;
            }
        }

        /* Custom Content Boxes */
        .intro-info-box {
            background: #f3f4f6 !important;
            border-radius: 10px !important;
            padding: 14px 16px !important;
            margin: 12px 0 !important;
            border-left: 3px solid #4b5563 !important;
        }

        .intro-info-box p,
        .intro-info-box strong {
            color: #1f2937 !important;
        }

        .intro-warning-box {
            background: #fef3c7 !important;
            border-radius: 10px !important;
            padding: 14px 16px !important;
            margin: 12px 0 !important;
            border-left: 3px solid #f59e0b !important;
        }

        .intro-warning-box strong,
        .intro-warning-box small {
            color: #92400e !important;
        }

        .intro-feature-grid {
            display: flex !important;
            gap: 12px !important;
            margin-top: 16px !important;
        }

        .intro-feature-item {
            flex: 1 !important;
            padding: 16px 12px !important;
            background: #f9fafb !important;
            border-radius: 10px !important;
            text-align: center !important;
            border: 2px solid #e5e7eb !important;
            transition: all 0.2s ease !important;
        }

        .intro-feature-item:hover {
            background: #ffffff !important;
            border-color: #d1d5db !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
            transform: translateY(-2px) !important;
        }

        .intro-icon {
            font-size: 32px !important;
            margin-bottom: 8px !important;
            display: block !important;
        }

        .intro-feature-item strong {
            display: block !important;
            margin-bottom: 6px !important;
            color: #1a1a1a !important;
            font-size: 14px !important;
            font-weight: 600 !important;
        }

        .intro-feature-item small {
            color: #6b7280 !important;
            font-size: 12px !important;
            line-height: 1.4 !important;
            display: block !important;
        }
    </style>

    <!-- 3. Tour Logic Script - Production Ready -->
    <script>
        // ⚙️ Global Configuration
        window.base_url = '<?php echo base_url(); ?>';

        // 🔧 Tour Manager Class
        class TourManager {
            constructor() {
                this.tourKey = 'system_admin_tour_data';
                this.currentIntro = null;
            }

            // ✅ Production Mode: แสดงวันละ 1 ครั้ง
            shouldShowTour() {
                // Comment ออก 
                // const storedData = this.getTourData();
                // const today = new Date().toISOString().split('T')[0];

                // if (!storedData || storedData.date !== today) {
                //     return true;
                // }

                return false;

                // 🧪 Development Mode: แสดงทุกครั้ง (ใช้บรรทัดนี้แทนเมื่อทดสอบ)
                // return true;
            }

            getTourData() {
                try {
                    const data = localStorage.getItem(this.tourKey);
                    return data ? JSON.parse(data) : null;
                } catch (e) {
                    console.error('Error reading tour data:', e);
                    return null;
                }
            }

            markTourCompleted() {
                const today = new Date().toISOString().split('T')[0];
                const tourData = {
                    completed: true,
                    date: today,
                    timestamp: Date.now()
                };
                localStorage.setItem(this.tourKey, JSON.stringify(tourData));
                console.log('✅ Tour completed for today:', today);
            }

            resetTour() {
                localStorage.removeItem(this.tourKey);
                console.log('🔄 Tour data reset');
            }

            getCurrentPage() {
                const path = window.location.pathname;
                if (path.includes('System_admin')) return 'System_admin';
                if (path.includes('news_backend')) return 'news_backend';
                if (path.includes('operation_reauf_backend')) return 'operation_reauf_backend';

                // ✅ ITA System - ตรวจสอบจากเฉพาะเจาะจงก่อน แล้วค่อยไปทั่วไป
                if (path.includes('ita_year_backend/editing_link')) return 'Ita_year_link_form_edit';
                if (path.includes('ita_year_backend/index_link')) return 'Ita_year_link';
                if (path.includes('ita_year_backend/index_topic')) return 'Ita_year_topic';
                if (path.includes('Ita_year_backend')) return 'Ita_year_backend';

                return null;
            }

            scrollToElement(element) {
                if (!element) return;

                const elementRect = element.getBoundingClientRect();
                const absoluteElementTop = elementRect.top + window.pageYOffset;
                const middle = absoluteElementTop - (window.innerHeight / 2) + (elementRect.height / 2);

                window.scrollTo({
                    top: Math.max(0, middle),
                    behavior: 'smooth'
                });
            }

            startTour(page) {
                console.log('🚀 Starting tour for page:', page);

                const tours = {
                    'System_admin': this.getSystemAdminTour(),
                    'news_backend': this.getNewsBackendTour(),
                    'operation_reauf_backend': this.getOperationReaufTour(),
                    'Ita_year_backend': this.getItaYearBackendTour(),
                    'Ita_year_topic': this.getItaYearTopicTour(),
                    'Ita_year_link': this.getItaYearLinkTour(),
                    'Ita_year_link_form_edit': this.getItaYearLinkFormEditTour()
                };

                const tour = tours[page];
                if (!tour) {
                    console.warn('❌ No tour found for page:', page);
                    return;
                }

                const validTour = tour.filter(step => {
                    if (!step.element) return true;
                    const el = document.querySelector(step.element);
                    if (!el) {
                        console.warn('⚠️ Element not found:', step.element);
                        return false;
                    }
                    return true;
                });

                if (validTour.length === 0) {
                    console.warn('❌ No valid tour steps found');
                    return;
                }

                this.currentIntro = introJs();
                this.currentIntro.setOptions({
                    steps: validTour,
                    showProgress: true,
                    showBullets: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: true,
                    scrollToElement: true,
                    scrollPadding: 100,
                    nextLabel: 'ถัดไป →',
                    prevLabel: '← ย้อนกลับ',
                    skipLabel: '✕',
                    doneLabel: 'เสร็จสิ้น ✓',
                    tooltipClass: 'customTooltip',
                    highlightClass: 'pulse',

                });

                this.currentIntro.onafterchange((targetElement) => {
                    if (targetElement) {
                        setTimeout(() => {
                            this.scrollToElement(targetElement);
                        }, 300);
                    }

                    // ✅ เพิ่ม Logic สำหรับ System_admin
                    if (page === 'System_admin') {
                        const currentStep = this.currentIntro._currentStep;

                        if (currentStep === 3) {
                            // หาปุ่ม Done และเปลี่ยนเป็น Next พร้อม redirect
                            setTimeout(() => {
                                const doneButton = document.querySelector('.introjs-donebutton');
                                if (doneButton) {
                                    // เปลี่ยน text และ class
                                    doneButton.textContent = 'ถัดไป →';
                                    doneButton.classList.remove('introjs-donebutton');
                                    doneButton.classList.add('introjs-nextbutton');

                                    // ✅ ลบ event listener เดิมและเพิ่มใหม่
                                    const newButton = doneButton.cloneNode(true);
                                    doneButton.parentNode.replaceChild(newButton, doneButton);

                                    // เพิ่ม event ใหม่สำหรับ redirect
                                    newButton.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        window.location.href = window.base_url + 'news_backend?tour=continue';
                                    });
                                }
                            }, 50);
                        }
                    }

                    // ✅ เพิ่มตรงนี้ - Logic สำหรับ Ita_year_backend
                    if (page === 'Ita_year_backend') {
                        const currentStep = this.currentIntro._currentStep;

                        if (currentStep === 8) {
                            console.log('📍 ITA Step 8 - Installing custom button');

                            setTimeout(() => {
                                const doneButton = document.querySelector('.introjs-donebutton');
                                console.log('🔍 Done button found:', !!doneButton);

                                if (doneButton) {
                                    // เปลี่ยน label และ class
                                    doneButton.textContent = 'ถัดไป →';
                                    doneButton.classList.remove('introjs-donebutton');
                                    doneButton.classList.add('introjs-nextbutton');

                                    // ลบ event เดิมและเพิ่มใหม่
                                    const newButton = doneButton.cloneNode(true);
                                    doneButton.parentNode.replaceChild(newButton, doneButton);

                                    // เพิ่ม event ใหม่สำหรับ redirect
                                    newButton.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        e.stopPropagation();

                                        console.log('🚀 Redirect button clicked!');

                                        const firstRow = document.querySelector('tbody tr:first-child td:last-child a:nth-child(1)');
                                        console.log('🔗 First row link:', firstRow ? firstRow.href : 'NOT FOUND');

                                        if (firstRow && firstRow.href) {
                                            const redirectUrl = firstRow.href + '?tour=continue';
                                            console.log('✅ Redirecting to:', redirectUrl);
                                            window.location.href = redirectUrl;
                                        } else {
                                            console.error('❌ Cannot find first row link!');
                                            alert('ไม่พบข้อมูลในตาราง กรุณาเพิ่มข้อมูลก่อน');
                                        }
                                    });

                                    console.log('✅ Custom redirect button installed');
                                }
                            }, 100);
                        }
                    }

                    // ✅ เพิ่มตรงนี้ - Logic สำหรับ Ita_year_topic
                    if (page === 'Ita_year_topic') {
                        const currentStep = this.currentIntro._currentStep;

                        if (currentStep === 8) {
                            console.log('📍 ITA Topic Step 8 - Installing custom button');

                            setTimeout(() => {
                                const doneButton = document.querySelector('.introjs-donebutton');
                                console.log('🔍 Done button found:', !!doneButton);

                                if (doneButton) {
                                    doneButton.textContent = 'ถัดไป →';
                                    doneButton.classList.remove('introjs-donebutton');
                                    doneButton.classList.add('introjs-nextbutton');

                                    const newButton = doneButton.cloneNode(true);
                                    doneButton.parentNode.replaceChild(newButton, doneButton);

                                    newButton.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        e.stopPropagation();

                                        console.log('🚀 Redirect to index_link');

                                        const firstRow = document.querySelector('tbody tr:first-child td:last-child a:nth-child(1)');
                                        if (firstRow && firstRow.href) {
                                            window.location.href = firstRow.href + '?tour=continue';
                                        } else {
                                            alert('ไม่พบข้อมูลในตาราง กรุณาเพิ่มข้อมูลก่อน');
                                        }
                                    });

                                    console.log('✅ Topic button installed');
                                }
                            }, 100);
                        }
                    }

                    // ✅ เพิ่มตรงนี้ - Logic สำหรับ Ita_year_link
                    if (page === 'Ita_year_link') {
                        const currentStep = this.currentIntro._currentStep;

                        if (currentStep === 8) {
                            console.log('📍 ITA Link Step 8 - Installing custom button');

                            setTimeout(() => {
                                const doneButton = document.querySelector('.introjs-donebutton');
                                console.log('🔍 Done button found:', !!doneButton);

                                if (doneButton) {
                                    doneButton.textContent = 'ถัดไป →';
                                    doneButton.classList.remove('introjs-donebutton');
                                    doneButton.classList.add('introjs-nextbutton');

                                    const newButton = doneButton.cloneNode(true);
                                    doneButton.parentNode.replaceChild(newButton, doneButton);

                                    newButton.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        e.stopPropagation();

                                        console.log('🚀 Redirect to editing_link');

                                        const firstRow = document.querySelector('tbody tr:first-child td:last-child a:nth-child(1)');
                                        if (firstRow && firstRow.href) {
                                            window.location.href = firstRow.href + '?tour=continue';
                                        } else {
                                            alert('ไม่พบข้อมูลในตาราง กรุณาเพิ่มข้อมูลก่อน');
                                        }
                                    });

                                    console.log('✅ Link button installed');
                                }
                            }, 100);
                        }
                    }

                });

                this.currentIntro.oncomplete(() => {
                    this.markTourCompleted();
                    console.log('✅ Tour completed');

                    // ✅ FIX 1: แสดงปุ่มกลับเมื่อจบ
                    const btn = document.getElementById('manualTourBtn');
                    if (btn) {
                        btn.style.display = 'inline-flex';
                    }

                    if (page !== 'System_admin') {
                        setTimeout(() => {
                            window.location.href = window.base_url + 'System_admin';
                        }, 1500);
                    }
                });

                this.currentIntro.onexit(() => {
                    this.markTourCompleted();
                    console.log('⏭️ Tour skipped');

                    // ✅ FIX 1: แสดงปุ่มกลับเมื่อกด Skip
                    const btn = document.getElementById('manualTourBtn');
                    if (btn) {
                        btn.style.display = 'inline-flex';
                    }
                });

                this.currentIntro.start();
            }
            // 📋 Tour Steps: System_admin (Steps 1-4)
            getSystemAdminTour() {
                return [{
                        title: '🎉 ยินดีต้อนรับผู้ดูแลระบบ',
                        intro: `
                <div style="text-align: center;">
                    <p style="font-size: 16px; margin-bottom: 16px; line-height: 1.7;">
                        ยินดีต้อนรับสู่ระบบจัดการเว็บไซต์<br>
                        เราจะแนะนำการใช้งานภายใน <strong>2-3 นาที</strong>
                    </p>
                    <div class="intro-info-box">
                        <p style="font-size: 14px; margin: 0; line-height: 1.6;">
                            ⏰ Tour จะแสดงอัตโนมัติวันละ 1 ครั้ง<br>
                            คุณสามารถกด "ข้าม" ได้ตลอดเวลา
                        </p>
                    </div>
                </div>
            `
                    },
                    {
                        element: '#accordionSidebar',
                        title: '📋 เมนูหลัก',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">เมนูหลักสำหรับจัดการระบบทั้งหมด:</p>
                <ul style="margin: 12px 0; padding-left: 22px; line-height: 1.9;">
                    <li><strong>จัดการข้อมูล</strong> - ข่าวสาร แบนเนอร์ กิจกรรม</li>
                    <li><strong>โครงสร้างบุคลากร</strong> - จัดการข้อมูลบุคลากร</li>
                    <li><strong>แผนงาน</strong> - แผนพัฒนา งบประมาณ</li>
                    <li><strong>การดำเนินงาน</strong> - รายงาน ITA LPA</li>
                </ul>
            `,
                        position: 'right'
                    },
                    {
                        element: '#searchInput',
                        title: '🔍 ค้นหาข้อมูล',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">ใช้ช่องนี้ค้นหาเมนูหรือข้อมูลในระบบ</p>
                <div class="intro-info-box">
                    <strong style="display: block; margin-bottom: 8px;">ตัวอย่างการค้นหา:</strong>
                    <p style="margin: 5px 0; line-height: 1.6; font-size: 14px;">
                        • พิมพ์ "<strong>ประชาสัมพันธ์</strong>" → แสดงเมนูข่าวประชาสัมพันธ์<br>
                        • พิมพ์ "<strong>แบนเนอร์</strong>" → แสดงเมนูจัดการแบนเนอร์/สไลด์โชว์<br>
                        • พิมพ์ "<strong>บุคลากร</strong>" → แสดงเมนูโครงสร้างบุคลากร<br>
                        • พิมพ์ "<strong>ประกาศ</strong>" → แสดงเมนูประกาศทั้งหมด
                    </p>
                </div>
            `,
                        position: 'bottom'
                    },
                    {
                        title: '➡️ ไปหน้าจัดการข่าวประชาสัมพันธ์',
                        intro: `
                <div style="text-align: center; padding: 5px;">
                    <p style="font-size: 16px; margin-bottom: 16px; line-height: 1.7;">
                        ต่อไปเราจะแนะนำการใช้งาน<br>
                        <strong style="font-size: 17px;">📰 จัดการข่าวประชาสัมพันธ์</strong>
                    </p>
                    <div class="intro-info-box">
                        <p style="font-size: 14px; margin: 0;">
                            คลิก "<strong>ถัดไป</strong>" เพื่อไปยังหน้าจัดการข่าว<br>
                            ส่วนลงข้อมูลหลัก<br><strong>ครอบคลุมมากกว่า 90% </strong>ของเว็บไซต์<br>
                            <small>หัวหลัก: ประชาสัมพันธ์, กิจกรรม, ประกาศ เป็นต้น</small>
                        </p>
                    </div>
                </div>
            `
                    }
                ];
            }

            // 📋 Tour Steps: news_backend (Steps 5-7)
            getNewsBackendTour() {
                return [{
                        element: '.add-btn[href*="news_backend/adding"]',
                        title: '➕ ปุ่มเพิ่มข้อมูล',
                        intro: `
                            <p style="margin-bottom: 12px; font-size: 15px;">คลิกที่ปุ่มนี้เพื่อเพิ่มข่าวประชาสัมพันธ์ใหม่</p>
                            <div class="intro-info-box">
                                <strong style="display: block; margin-bottom: 10px;">ฟังก์ชันหลัก:</strong>
                                <ul style="margin: 0; padding-left: 22px; line-height: 1.8;">
                                    <li>📸 อัพโหลดรูปภาพปก</li>
                                    <li>📎 แนบไฟล์เอกสาร (PDF, DOC)</li>
                                    <li>✍️ เขียนรายละเอียดข่าว</li>
                                    <li>⏰ ตั้งเวลาเผยแพร่</li>
                                </ul>
                            </div>
                        `,
                        position: 'bottom'
                    },
                    {
                        element: '#newdataTables tbody tr:first-child',
                        title: '🎯 เครื่องมือจัดการข่าว',
                        intro: `
                            <p style="margin-bottom: 12px; font-size: 15px;">ในแต่ละแถวมีเครื่องมือจัดการ 3 ตัว:</p>
                            <div class="intro-feature-grid">
                                <div class="intro-feature-item">
                                    <span class="intro-icon">🟢</span>
                                    <strong>Toggle</strong>
                                    <small>เปิด/ปิด<br>การแสดงผล</small>
                                </div>
                                <div class="intro-feature-item">
                                    <span class="intro-icon">✏️</span>
                                    <strong>แก้ไข</strong>
                                    <small>แก้ไข<br>ข้อมูล</small>
                                </div>
                                <div class="intro-feature-item">
                                    <span class="intro-icon">🗑️</span>
                                    <strong>ลบ</strong>
                                    <small>ลบข่าว<br>ออก</small>
                                </div>
                            </div>
                        `,
                        position: 'top'
                    },
                    {
                        title: '➡️ ไปหน้ารายงานติดตาม',
                        intro: `
                            <div style="text-align: center; padding: 5px;">
                                <p style="font-size: 16px; margin-bottom: 16px; line-height: 1.7;">
                                    ต่อไปเราจะแนะนำการใช้งาน<br>
                                    <strong style="font-size: 17px;">📊 รายงานติดตามและประเมินผล</strong>
                                </p>
                                <div class="intro-info-box">
                                    <p style="font-size: 14px; margin: 0;">
                                        คลิก "<strong>ถัดไป</strong>" เพื่อดำเนินการต่อ<br>
                                        <small>(ระบบจะเปลี่ยนหน้าอัตโนมัติ)</small>
                                    </p>
                                </div>
                            </div>
                        `
                    }
                ];
            }

            // 📋 Tour Steps: operation_reauf_backend (Steps 8-10)
            getOperationReaufTour() {
                return [{
                        element: '.add-btn.insert-vulgar-btn, .add-btn[href*="operation_reauf_backend/adding"]',
                        title: '➕ เพิ่มหัวข้อรายงาน',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    คลิกที่ปุ่มนี้เพื่อเพิ่ม<strong>หัวข้อรายงาน</strong>ใหม่
                </p>
                <div class="intro-info-box">
                    <strong style="display: block; margin-bottom: 10px;">
                        💡 หัวข้อรายงาน คือหมวดหมู่สำหรับจัดกลุ่มรายงาน
                    </strong>
                    <p style="margin: 0; font-size: 14px; line-height: 1.7;">
                        เช่น "รายงานงบการเงิน", "รายงานผลการดำเนินงาน" ฯลฯ<br>
                        <small style="color: #6c757d;">
                            แต่ละหัวข้อจะมีรายงานย่อยภายในได้หลายรายการ
                        </small>
                    </p>
                </div>
                <div class="intro-warning-box">
                    <small style="font-size: 13px;">
                        <strong>⚠️ ขั้นตอน:</strong> 
                        เพิ่มหัวข้อก่อน → แล้วเพิ่มรายงานภายในหัวข้อ
                    </small>
                </div>
                <div class="intro-info-box" style="margin-top: 14px; border-left: 3px solid #10b981; background: #f0fdf4;">
                    <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #065f46;">
                        <strong style="color: #047857;">📌 จำไว้:</strong> หน้ารายงานนี้มี 2 ระดับ<br>
                        <small style="color: #047857;">
                            1. หัวข้อรายงาน (หน้านี้)<br>
                            2. รายงานภายในหัวข้อ (กดปุ่ม ➕ เพื่อเข้าไป)
                        </small>
                    </p>
                </div>
            `,
                        position: 'bottom'
                    },
                    {
                        element: '#newdataTables tbody tr:first-child',
                        title: '🎯 การจัดการหัวข้อรายงาน',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    ในแต่ละแถวของหัวข้อมีเครื่องมือจัดการ:
                </p>
                <div class="intro-info-box">
                    <ul style="margin: 0; padding-left: 22px; line-height: 1.9;">
                        <li><strong>➕ ดูรายละเอียด</strong> - เข้าไปดูและเพิ่มรายงานในหัวข้อนี้</li>
                        <li><strong>✏️ แก้ไข</strong> - แก้ไขชื่อหัวข้อ</li>
                        <li><strong>🗑️ ลบ</strong> - ลบหัวข้อออก</li>
                    </ul>
                </div>
                <div class="intro-info-box" style="margin-top: 14px; border-left: 3px solid #4b5563;">
                    <p style="margin: 0; font-size: 14px; line-height: 1.6;">
                        <strong>💡 เคล็ดลับ:</strong> 
                        คลิกปุ่ม <strong>➕ ดูรายละเอียด</strong> เพื่อเข้าไปจัดการรายงานภายในหัวข้อนั้นๆ
                    </p>
                </div>
            `,
                        position: 'top'
                    },
                    {
                        title: '🎊 ทัวร์เสร็จสมบูรณ์!',
                        intro: `
                <div style="text-align: center; padding: 30px 20px;">
                    <div style="font-size: 80px; margin-bottom: 25px; line-height: 1;">🎉</div>
                    <h3 style="margin-bottom: 18px; font-size: 28px; font-weight: 700; color: #1a1a1a;">
                        ยินดีด้วย!
                    </h3>
                    <p style="font-size: 17px; margin-bottom: 20px; line-height: 1.8; color: #2d3748;">
                        คุณได้เรียนรู้การใช้งาน<br>
                        การจัดการข้อมูลเบื้องต้นเรียบร้อยแล้ว
                    </p>
                    <div style="background: #f0fdf4; border-left: 4px solid #10b981; border-radius: 10px; padding: 16px; margin-top: 20px;">
                        <p style="font-size: 15px; margin: 0; line-height: 1.7; color: #065f46;">
                            <strong style="color: #047857;">✨ พร้อมเริ่มต้นใช้งาน</strong><br>
                            ตอนนี้คุณสามารถจัดการข่าว รายงาน<br>
                            และข้อมูลต่างๆ ได้อย่างมั่นใจ
                        </p>
                    </div>
                    <div style="margin-top: 28px; padding: 24px; background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-radius: 16px; box-shadow: 0 8px 24px rgba(6, 182, 212, 0.25);">
                        <p style="font-size: 18px; font-weight: 700; color: #ffffff; margin: 0 0 8px 0; line-height: 1.4;">
                            🚀 พร้อมแล้วใช่ไหม?
                        </p>
                        <p style="font-size: 15px; color: rgba(255, 255, 255, 0.95); margin: 0 0 20px 0; line-height: 1.6;">
                            มาลองลงข้อมูลข่าวประชาสัมพันธ์<br>
                            ฉบับแรกของคุณกันเลย!
                        </p>
                        <button onclick="window.location.href=window.base_url+'news_backend/adding'" 
                           style="display: inline-block; background: #ffffff; color: #0891b2; padding: 14px 36px; border: none; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 16px; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15); cursor: pointer; transition: all 0.3s ease;"
                           onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 24px rgba(0, 0, 0, 0.25)';"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(0, 0, 0, 0.15)';">
                            ✍️ ไปลงข้อมูลกันเลย →
                        </button>
                    </div>
                </div>
            `
                    }
                ];
            }

            // 📋 Tour Steps: Ita_year_backend (Steps 11-19)
            getItaYearBackendTour() {
                return [{
                        title: '🎉 ยินดีต้อนรับสู่ระบบจัดการ ITA ประจำปี',
                        intro: `
                            <div style="text-align: center;">
                                <p style="font-size: 16px; margin-bottom: 16px; line-height: 1.7;">
                                    ระบบนี้ใช้สำหรับจัดการข้อมูล<br>
                                    <strong>การประเมินคุณธรรมและความโปร่งใส (ITA)</strong><br>
                                    แบ่งเป็น 3 ระดับ: <strong>ปี → หมวดหมู่ → หัวข้อ</strong>
                                </p>
                                <div class="intro-info-box">
                                    <p style="font-size: 14px; margin: 0; line-height: 1.6;">
                                        💡 ทีมพัฒนาจะเตรียมโครงสร้างไว้ให้แล้ว<br>
                                        <strong>คุณแค่ต้องแก้ไขชื่อและ URL เท่านั้น</strong>
                                    </p>
                                </div>
                            </div>
                        `
                    },
                    {
                        element: '.insert-vulgar-btn, .add-btn[data-target="#popupInsert"]',
                        title: '➕ เพิ่มข้อมูลปี พ.ศ. ใหม่',
                        intro: `
                            <p style="margin-bottom: 12px; font-size: 15px;">
                                คลิกปุ่มนี้เพื่อเพิ่มปี พ.ศ. ใหม่ (เช่น <strong>2568</strong>)
                            </p>
                            <div class="intro-warning-box">
                                <strong>⚠️ หมายเหตุสำคัญ:</strong>
                                <p style="margin: 8px 0 0 0; font-size: 14px; line-height: 1.6;">
                                    ทีมพัฒนาจะเตรียมหัวข้อไว้ให้แล้ว<br>
                                    <strong>คุณแค่ต้องแก้ไขชื่อและ URL เท่านั้น</strong>
                                </p>
                            </div>
                        `,
                        position: 'bottom'
                    },
                    {
                        element: '.btn-light[href*="Ita_year_backend"]',
                        title: '🔄 รีเฟรชข้อมูล',
                        intro: `
                            <p style="margin-bottom: 12px; font-size: 15px;">
                                คลิกเพื่อโหลดข้อมูลล่าสุดจากฐานข้อมูล
                            </p>
                            <div class="intro-info-box">
                                <p style="margin: 0; font-size: 14px;">
                                    💡 <strong>เคล็ดลับ:</strong><br>
                                    ใช้เมื่อมีการเปลี่ยนแปลงข้อมูล<br>
                                    และต้องการดูผลล่าสุด
                                </p>
                            </div>
                        `,
                        position: 'bottom'
                    },
                    {
                        element: '#newdataTables',
                        title: '📋 รายการปี พ.ศ. ทั้งหมด',
                        intro: `
                            <p style="margin-bottom: 12px; font-size: 15px;">
                                ตารางนี้แสดงรายการปี พ.ศ. ที่มีในระบบ:
                            </p>
                            <ul style="margin: 12px 0; padding-left: 22px; line-height: 1.9;">
                                <li><strong>ลำดับ:</strong> เรียงตามลำดับการสร้าง</li>
                                <li><strong>ชื่อ:</strong> ปี พ.ศ. (เช่น 2567)</li>
                                <li><strong>อัพโหลด:</strong> ผู้ที่สร้างข้อมูล</li>
                                <li><strong>วันที่:</strong> วันที่บันทึกข้อมูล</li>
                            </ul>
                        `,
                        position: 'top'
                    },
                    {
                        element: 'thead tr th:last-child',
                        title: '🛠️ คอลัมน์จัดการ',
                        intro: `
                            <p style="margin-bottom: 12px; font-size: 15px;">
                                ในคอลัมน์นี้จะมีไอคอนสำหรับจัดการข้อมูล:
                            </p>
                            <div class="intro-feature-grid">
                                <div class="intro-feature-item">
                                    <span class="intro-icon">📁</span>
                                    <strong>+ สี่เหลี่ยม</strong>
                                    <small>เข้าจัดการ<br>หมวดหมู่</small>
                                </div>
                                <div class="intro-feature-item">
                                    <span class="intro-icon">✏️</span>
                                    <strong>ดินสอ</strong>
                                    <small>แก้ไข<br>ข้อมูลปี</small>
                                </div>
                                <div class="intro-feature-item">
                                    <span class="intro-icon">🗑️</span>
                                    <strong>ถังขยะ</strong>
                                    <small>ลบ<br>ข้อมูลปี</small>
                                </div>
                            </div>
                        `,
                        position: 'left'
                    },
                    {
                        element: 'tbody tr:first-child td:last-child a:nth-child(1)',
                        title: '📁 เข้าจัดการหมวดหมู่ภายในปี',
                        intro: `
                            <p style="margin-bottom: 12px; font-size: 15px;">
                                คลิกไอคอน <strong>➕ สี่เหลี่ยม</strong> เพื่อเข้าไปจัดการ<br>
                                <strong>หมวดหมู่การประเมิน</strong>ในปีนั้นๆ
                            </p>
                            <div class="intro-info-box">
                                <p style="margin: 0; font-size: 14px; line-height: 1.7;">
                                    <strong>ตัวอย่างหมวดหมู่:</strong><br>
                                    • ตัวชี้วัดที่ 1: ข้อมูลพื้นฐาน<br>
                                    • ตัวชี้วัดที่ 2: แผนการดำเนินงานและงบประมาณ<br>
                                    • ตัวชี้วัดที่ 3: การบริหารและพัฒนาทรัพยากรบุคคล
                                </p>
                            </div>
                        `,
                        position: 'left'
                    },
                    {
                        element: 'tbody tr:first-child td:last-child a:nth-child(2)',
                        title: '✏️ แก้ไขข้อมูลปี',
                        intro: `
                            <p style="margin-bottom: 12px; font-size: 15px;">
                                คลิกไอคอน<strong>ดินสอ</strong> เพื่อแก้ไขชื่อปี พ.ศ.
                            </p>
                            <div class="intro-info-box">
                                <p style="margin: 0; font-size: 14px; line-height: 1.6;">
                                    💡 <strong>เคล็ดลับ:</strong><br>
                                    ใช้เฉพาะตอนต้องการเปลี่ยนปี<br>
                                    เช่น แก้ 2567 เป็น 2568
                                </p>
                            </div>
                        `,
                        position: 'left'
                    },
                    {
                        element: 'tbody tr:first-child td:last-child a:nth-child(3)',
                        title: '🗑️ ลบข้อมูลปี',
                        intro: `
                            <p style="margin-bottom: 12px; font-size: 15px;">
                                คลิกไอคอน<strong>ถังขยะ</strong> เพื่อลบปีนี้ออกจากระบบ
                            </p>
                            <div style="background: #FEE2E2; border-left: 4px solid #EF4444; padding: 14px; border-radius: 8px; margin-top: 12px;">
                                <strong style="color: #991B1B; display: block; margin-bottom: 8px; font-size: 14px;">
                                    ⚠️ ระวัง: ข้อมูลทั้งหมดในปีนั้นจะถูกลบด้วย!
                                </strong>
                                <p style="margin: 0; font-size: 13px; color: #7F1D1D; line-height: 1.8;">
                                    รวมถึง:<br>
                                    • หมวดหมู่ทั้งหมด<br>
                                    • หัวข้อทั้งหมด<br>
                                    • ลิงก์เอกสารทั้งหมด
                                </p>
                            </div>
                        `,
                        position: 'left'
                    },
                    {
                        title: '➡️ ไปหน้าจัดการหมวดหมู่',
                        intro: `
                            <div style="text-align: center; padding: 20px;">
                                <div style="font-size: 60px; margin-bottom: 16px; line-height: 1;">📁</div>
                                <h3 style="margin-bottom: 16px; font-size: 20px; font-weight: 700; color: #1F2937;">
                                    พร้อมไปหน้าถัดไป!
                                </h3>
                                <p style="font-size: 15px; margin-bottom: 20px; line-height: 1.7; color: #4B5563;">
                                    ต่อไปจะเรียนรู้วิธีจัดการ<br>
                                    <strong>หมวดหมู่การประเมิน ITA</strong>
                                </p>
                                <div style="background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%); border-left: 4px solid #3B82F6; padding: 16px; border-radius: 12px;">
                                    <strong style="display: block; margin-bottom: 10px; color: #1E40AF; font-size: 15px;">
                                        🚀 ขั้นตอนถัดไป:
                                    </strong>
                                    <p style="margin: 0; text-align: left; line-height: 2; color: #1E3A8A; font-size: 14px;">
                                        1️⃣ คลิกปุ่ม "<strong>ถัดไป</strong>" ด้านล่าง<br>
                                        2️⃣ ระบบจะพาไปหน้าหมวดหมู่อัตโนมัติ<br>
                                        3️⃣ เริ่มเรียนรู้การจัดการหมวดหมู่
                                    </p>
                                </div>
                                <div style="margin-top: 16px; padding: 14px; background: #F3F4F6; border-radius: 10px;">
                                    <p style="margin: 0; font-size: 13px; color: #6B7280; line-height: 1.6;">
                                        💡 <strong>หมายเหตุ:</strong><br>
                                        ต้องมีข้อมูลในตารางอย่างน้อย 1 รายการ<br>
                                        ถ้ายังไม่มี กรุณาเพิ่มข้อมูลก่อน
                                    </p>
                                </div>
                            </div>
                        `
                    }
                ];
            }

            // 📋 Tour Steps: ita_year_topic.php - หน้าหมวดหมู่การประเมิน (Steps 20-28)
            getItaYearTopicTour() {
                return [{
                        title: '🎯 หน้าจัดการหมวดหมู่การประเมิน',
                        intro: `
                <div style="text-align: center;">
                    <p style="font-size: 16px; margin-bottom: 16px; line-height: 1.7;">
                        หน้านี้ใช้สำหรับจัดการ<br>
                        <strong>หมวดหมู่การประเมิน ITA</strong><br>
                        ภายในปีที่เลือก
                    </p>
                    <div class="intro-info-box">
                        <p style="font-size: 14px; margin: 0; line-height: 1.6;">
                            💡 <strong>โครงสร้างระบบ:</strong><br>
                            ปี → <strong style="color: #3B82F6;">หมวดหมู่</strong> → หัวข้อ
                        </p>
                    </div>
                    <div class="intro-warning-box" style="margin-top: 12px;">
                        <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                            ⚠️ ทีมพัฒนาเตรียมหมวดหมู่ไว้ให้แล้ว<br>
                            <strong>คุณไม่จำเป็นต้องเพิ่มหรือลบหมวดหมู่</strong>
                        </p>
                    </div>
                </div>
            `
                    },
                    {
                        element: '.insert-vulgar-btn, .add-btn[data-target="#popupInsert"]',
                        title: '➕ เพิ่มหมวดหมู่ (ทีมพัฒนาจัดการให้)',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    ปุ่มนี้ใช้สำหรับเพิ่มหมวดหมู่การประเมินใหม่
                </p>
                <div class="intro-info-box">
                    <strong>ℹ️ หมายเหตุสำคัญ:</strong>
                    <p style="margin: 8px 0 0 0; font-size: 14px; line-height: 1.6;">
                        ทีมพัฒนาจะเตรียมหมวดหมู่ทั้งหมดไว้ให้แล้ว<br>
                        <strong>คุณไม่จำเป็นต้องใช้ปุ่มนี้</strong>
                    </p>
                </div>
                <div style="background: #F0F9FF; border-left: 4px solid #3B82F6; padding: 12px; border-radius: 8px; margin-top: 12px;">
                    <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                        <strong>หมวดหมู่ที่เตรียมไว้ เช่น:</strong><br>
                        • ตัวชี้วัดที่ 1: ข้อมูลพื้นฐาน<br>
                        • ตัวชี้วัดที่ 2: การบริหารงาน<br>
                        • ตัวชี้วัดที่ 3: การบริการ<br>
                        และอื่นๆ ตามเกณฑ์ ITA
                    </p>
                </div>
            `,
                        position: 'bottom'
                    },
                    {
                        element: '.btn-danger[href*="Ita_year_backend"]',
                        title: '◀️ ย้อนกลับหน้าปี',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    คลิกปุ่มนี้เพื่อกลับไปหน้ารายการปี พ.ศ.
                </p>
                <div class="intro-info-box">
                    <p style="margin: 0; font-size: 14px;">
                        💡 ใช้เมื่อต้องการเปลี่ยนไปจัดการปีอื่น
                    </p>
                </div>
            `,
                        position: 'bottom'
                    },
                    {
                        element: '.btn-light[href*="index_topic"]',
                        title: '🔄 รีเฟรชข้อมูล',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    คลิกเพื่อโหลดข้อมูลล่าสุด
                </p>
                <div class="intro-info-box">
                    <p style="margin: 0; font-size: 14px;">
                        💡 ใช้หลังจากแก้ไขข้อมูล<br>
                        เพื่อดูผลการเปลี่ยนแปลง
                    </p>
                </div>
            `,
                        position: 'bottom'
                    },
                    {
                        element: '#newdataTables',
                        title: '📋 รายการหมวดหมู่การประเมิน',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    ตารางนี้แสดงหมวดหมู่การประเมิน ITA ทั้งหมด:
                </p>
                <ul style="margin: 12px 0; padding-left: 22px; line-height: 1.9;">
                    <li><strong>ลำดับ:</strong> ลำดับการแสดงผล</li>
                    <li><strong>หัวข้อ:</strong> ชื่อหมวดหมู่</li>
                    <li><strong>ข้อความ:</strong> หมายเหตุเพิ่มเติม</li>
                    <li><strong>อัพโหลด:</strong> ผู้ที่สร้างข้อมูล</li>
                    <li><strong>วันที่:</strong> วันที่บันทึกข้อมูล</li>
                </ul>
            `,
                        position: 'top'
                    },
                    {
                        element: 'thead tr th:last-child',
                        title: '🛠️ คอลัมน์จัดการ',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    ในคอลัมน์นี้จะมีไอคอนสำหรับจัดการข้อมูล:
                </p>
                <div class="intro-feature-grid">
                    <div class="intro-feature-item">
                        <span class="intro-icon">📄</span>
                        <strong>+ สี่เหลี่ยม</strong>
                        <small>เข้าจัดการ<br>หัวข้อ</small>
                    </div>
                    <div class="intro-feature-item">
                        <span class="intro-icon">✏️</span>
                        <strong>ดินสอ</strong>
                        <small>แก้ไข<br>หมวดหมู่</small>
                    </div>
                    <div class="intro-feature-item">
                        <span class="intro-icon">🗑️</span>
                        <strong>ถังขยะ</strong>
                        <small>ลบ<br>หมวดหมู่</small>
                    </div>
                </div>
            `,
                        position: 'left'
                    },
                    {
                        element: 'tbody tr:first-child td:last-child a:nth-child(1)',
                        title: '📄 เข้าจัดการหัวข้อในหมวดหมู่',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    คลิกไอคอน <strong>➕ สี่เหลี่ยม</strong> เพื่อเข้าไปจัดการ<br>
                    <strong>หัวข้อการประเมิน</strong>ในหมวดหมู่นี้
                </p>
                <div class="intro-info-box">
                    <p style="margin: 0; font-size: 14px; line-height: 1.7;">
                        <strong>ตัวอย่างหัวข้อภายในหมวดหมู่:</strong><br>
                        • ข้อ 1.1: ข้อมูลพื้นฐาน<br>
                        • ข้อ 1.2: โครงสร้างหน่วยงาน<br>
                        • ข้อ 1.3: ข้อมูลผู้บริหาร<br>
                        (แต่ละหัวข้อจะมีเอกสารแนบ)
                    </p>
                </div>
                <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 12px; border-radius: 8px; margin-top: 12px;">
                    <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                        ⭐ <strong>ขั้นตอนถัดไป:</strong><br>
                        คลิกไอคอนนี้เพื่อเข้าไปแก้ไข<br>
                        <strong>ชื่อหัวข้อและลิงก์เอกสาร</strong>
                    </p>
                </div>
            `,
                        position: 'left'
                    },
                    {
                        element: 'tbody tr:first-child td:last-child a:nth-child(2)',
                        title: '✏️ แก้ไขชื่อหมวดหมู่',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    คลิกไอคอน<strong>ดินสอ</strong> เพื่อแก้ไขชื่อหมวดหมู่
                </p>
                <div class="intro-warning-box">
                    <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                        ⚠️ <strong>ไม่แนะนำให้แก้ไข</strong><br>
                        ทีมพัฒนาได้ตั้งชื่อตามเกณฑ์ ITA แล้ว
                    </p>
                </div>
            `,
                        position: 'left'
                    },
                    {
                        title: '🎊 เข้าใจหน้าหมวดหมู่แล้ว',
                        intro: `
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 60px; margin-bottom: 16px;">✅</div>
                    <h3 style="margin-bottom: 16px; font-size: 20px; font-weight: 700; color: #1F2937;">
                        พร้อมไปหน้าถัดไป!
                    </h3>
                    <p style="font-size: 15px; margin-bottom: 20px; line-height: 1.7; color: #4B5563;">
                        ต่อไปจะเรียนรู้วิธีแก้ไข<br>
                        <strong>หัวข้อการประเมินและลิงก์เอกสาร</strong>
                    </p>
                    <div style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); border-left: 4px solid #F59E0B; padding: 16px; border-radius: 12px;">
                        <strong style="display: block; margin-bottom: 10px; color: #92400E; font-size: 15px;">
                            🚀 ขั้นตอนถัดไป:
                        </strong>
                        <p style="margin: 0; text-align: left; line-height: 2; color: #78350F; font-size: 14px;">
                            1️⃣ คลิกไอคอน ➕ ในหมวดหมู่<br>
                            2️⃣ เข้าสู่หน้าจัดการหัวข้อ<br>
                            3️⃣ แก้ไขชื่อและลิงก์เอกสาร
                        </p>
                    </div>
                </div>
            `
                    }
                ];
            }

            // 📋 Tour Steps: ita_year_link.php - หน้าหัวข้อการประเมิน (Steps 29-37)
            getItaYearLinkTour() {
                return [{
                        title: '🎯 หน้าจัดการหัวข้อการประเมิน',
                        intro: `
                <div style="text-align: center;">
                    <p style="font-size: 16px; margin-bottom: 16px; line-height: 1.7;">
                        หน้านี้ใช้สำหรับจัดการ<br>
                        <strong>หัวข้อการประเมินและลิงก์เอกสาร</strong><br>
                        ภายในหมวดหมู่ที่เลือก
                    </p>
                    <div class="intro-info-box">
                        <p style="font-size: 14px; margin: 0; line-height: 1.6;">
                            💡 <strong>โครงสร้างระบบ:</strong><br>
                            ปี → หมวดหมู่ → <strong style="color: #10B981;">หัวข้อและลิงก์</strong>
                        </p>
                    </div>
                    <div style="background: #DBEAFE; border-left: 4px solid #3B82F6; padding: 14px; border-radius: 8px; margin-top: 12px;">
                        <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                            ⭐ <strong>หน้าที่ของคุณ:</strong><br>
                            แก้ไขชื่อหัวข้อและลิงก์เอกสาร<br>
                            ให้ตรงกับข้อมูลที่ upload ไว้
                        </p>
                    </div>
                </div>
            `
                    },
                    {
                        element: '.btn-danger[href*="index_topic"]',
                        title: '◀️ ย้อนกลับหน้าหมวดหมู่',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    คลิกปุ่มนี้เพื่อกลับไปหน้ารายการหมวดหมู่
                </p>
                <div class="intro-info-box">
                    <p style="margin: 0; font-size: 14px;">
                        💡 ใช้เมื่อต้องการเปลี่ยนไปจัดการหมวดหมู่อื่น
                    </p>
                </div>
            `,
                        position: 'bottom'
                    },
                    {
                        element: '.btn-light[href*="index_link"]',
                        title: '🔄 รีเฟรชข้อมูล',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    คลิกเพื่อโหลดข้อมูลล่าสุดหลังแก้ไข
                </p>
                <div class="intro-info-box">
                    <p style="margin: 0; font-size: 14px; line-height: 1.6;">
                        💡 <strong>แนะนำ:</strong><br>
                        ใช้หลังบันทึกข้อมูลเสร็จ<br>
                        เพื่อตรวจสอบผลการแก้ไข
                    </p>
                </div>
            `,
                        position: 'bottom'
                    },
                    {
                        element: '#newdataTables',
                        title: '📋 รายการหัวข้อและลิงก์เอกสาร',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    ตารางนี้แสดงหัวข้อการประเมินและลิงก์เอกสารทั้งหมด:
                </p>
                <ul style="margin: 12px 0; padding-left: 22px; line-height: 1.9;">
                    <li><strong>ลำดับ:</strong> ลำดับการแสดงผล</li>
                    <li><strong>ชื่อ:</strong> ชื่อหัวข้อการประเมิน</li>
                    <li><strong>ชื่อของลิงค์ | ลิงค์:</strong> เอกสารประกอบ (สูงสุด 5 ลิงก์)</li>
                    <li><strong>อัพโหลด:</strong> ผู้ที่สร้างข้อมูล</li>
                    <li><strong>วันที่:</strong> วันที่บันทึกข้อมูล</li>
                </ul>
                <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 12px; border-radius: 8px; margin-top: 12px;">
                    <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                        💡 <strong>รองรับลิงก์ได้ 5 ชุด:</strong><br>
                        แต่ละหัวข้อสามารถมีเอกสารได้ถึง 5 ไฟล์<br>
                        เช่น PDF, Word, Excel, รูปภาพ, ลิงก์ภายนอก
                    </p>
                </div>
            `,
                        position: 'top'
                    },
                    {
                        element: 'thead tr th:nth-child(3)',
                        title: '🔗 คอลัมน์ลิงก์เอกสาร',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    คอลัมน์นี้แสดง<strong>ชื่อและลิงก์เอกสาร</strong>ทั้งหมด
                </p>
                <div class="intro-info-box">
                    <strong>📄 รูปแบบการแสดงผล:</strong>
                    <p style="margin: 8px 0 0 0; font-size: 13px; line-height: 1.7;">
                        <code style="background: #F3F4F6; padding: 2px 6px; border-radius: 4px;">
                        ชื่อเอกสาร | URL
                        </code><br><br>
                        <strong>ตัวอย่าง:</strong><br>
                        แผนยุทธศาสตร์ | https://example.com/plan.pdf<br>
                        คำสั่งแต่งตั้ง | https://example.com/order.pdf
                    </p>
                </div>
            `,
                        position: 'left'
                    },
                    {
                        element: 'thead tr th:last-child',
                        title: '🛠️ คอลัมน์จัดการ',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    ในคอลัมน์นี้จะมีไอคอนสำหรับจัดการข้อมูล:
                </p>
                <div class="intro-feature-grid" style="grid-template-columns: repeat(2, 1fr);">
                    <div class="intro-feature-item">
                        <span class="intro-icon" style="font-size: 32px;">✏️</span>
                        <strong>ดินสอ</strong>
                        <small>แก้ไข<br>หัวข้อและลิงก์</small>
                    </div>
                    <div class="intro-feature-item" style="border: 2px solid #EF4444;">
                        <span class="intro-icon" style="font-size: 32px;">🗑️</span>
                        <strong style="color: #DC2626;">ถังขยะ</strong>
                        <small style="color: #DC2626;"><strong>ระวัง!</strong><br>ลบข้อมูล</small>
                    </div>
                </div>
            `,
                        position: 'left'
                    },
                    {
                        element: 'tbody tr:first-child td:last-child a:nth-child(1)',
                        title: '✏️ แก้ไขหัวข้อและลิงก์ (สำคัญที่สุด)',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    คลิกไอคอน <strong>ดินสอ</strong> เพื่อเข้าสู่<br>
                    <strong>หน้าแก้ไขข้อมูล</strong>
                </p>
                <div style="background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%); border-left: 4px solid #3B82F6; padding: 16px; border-radius: 12px; margin-top: 12px;">
                    <strong style="display: block; margin-bottom: 10px; color: #1E40AF; font-size: 15px;">
                        ⭐ สิ่งที่จะแก้ไข:
                    </strong>
                    <ul style="margin: 0; padding-left: 20px; line-height: 2; color: #1E3A8A; font-size: 14px;">
                        <li><strong>ชื่อหัวข้อ:</strong> ชื่อการประเมิน</li>
                        <li><strong>ชื่อลิงก์ 1-5:</strong> ชื่อเอกสาร</li>
                        <li><strong>URL 1-5:</strong> ลิงก์เอกสาร</li>
                    </ul>
                </div>
                <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 14px; border-radius: 8px; margin-top: 12px;">
                    <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #92400E;">
                        💡 <strong>เคล็ดลับ:</strong><br>
                        ให้ชื่อลิงก์สั้น กระชับ เข้าใจง่าย<br>
                        เช่น "แผนยุทธศาสตร์ 2568" แทน "ไฟล์แผน.pdf"
                    </p>
                </div>
            `,
                        position: 'bottom'
                    },
                    {
                        element: 'tbody tr:first-child td:last-child a:nth-child(2)',
                        title: '🗑️ ⚠️ ระวัง! ปุ่มลบข้อมูล',
                        intro: `
        <div style="font-size: 13px;">
            <p style="margin-bottom: 8px;">
                คลิกไอคอน<strong>ถังขยะ</strong> เพื่อลบหัวข้อนี้ออกจากระบบ
            </p>
            
            <div style="background: #FEE2E2; border: 2px solid #EF4444; padding: 10px; border-radius: 8px;">
                <div style="text-align: center; margin-bottom: 6px;">
                    <span style="font-size: 32px;">⚠️</span>
                </div>
                <strong style="color: #991B1B; display: block; margin-bottom: 6px; font-size: 14px; text-align: center;">
                    คำเตือนสำคัญ!
                </strong>
                <div style="font-size: 12px; color: #7F1D1D; line-height: 1.6;">
                    ❌ หัวข้อนี้จะถูกลบทันที<br>
                    ❌ ลิงก์เอกสารทั้งหมดจะหายไป<br>
                    ❌ <strong>ไม่สามารถกู้คืนได้</strong>
                </div>
            </div>
            
            <div style="background: #FEF3C7; border-left: 3px solid #F59E0B; padding: 8px; border-radius: 6px; margin-top: 8px;">
                <div style="font-size: 11px; color: #92400E; line-height: 1.5;">
                    💡 <strong>แนะนำ:</strong><br>
                    • ตรวจสอบให้แน่ใจก่อนลบทุกครั้ง<br>
                    • ไม่แนะนำให้ลบหัวข้อที่ทีมพัฒนาเตรียมไว้ให้
                </div>
            </div>
        </div>
    `,
                        position: 'bottom',
                        scrollTo: 'tooltip'
                    },
                    {
                        title: '🎊 เข้าใจหน้าหัวข้อการประเมินแล้ว',
                        intro: `
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 60px; margin-bottom: 16px;">✅</div>
                    <h3 style="margin-bottom: 16px; font-size: 20px; font-weight: 700; color: #1F2937;">
                        พร้อมเรียนรู้การแก้ไข!
                    </h3>
                    <p style="font-size: 15px; margin-bottom: 20px; line-height: 1.7; color: #4B5563;">
                        ต่อไปจะเรียนรู้วิธีแก้ไขข้อมูล<br>
                        <strong>ในหน้าฟอร์มแก้ไข</strong>
                    </p>
                    <div style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); border-left: 4px solid #10B981; padding: 16px; border-radius: 12px;">
                        <strong style="display: block; margin-bottom: 10px; color: #047857; font-size: 15px;">
                            🚀 ขั้นตอนถัดไป:
                        </strong>
                        <p style="margin: 0; text-align: left; line-height: 2; color: #065F46; font-size: 14px;">
                            1️⃣ คลิกไอคอน ✏️ ดินสอ<br>
                            2️⃣ เข้าสู่หน้าแก้ไขข้อมูล<br>
                            3️⃣ กรอกชื่อและ URL ให้ถูกต้อง<br>
                            4️⃣ บันทึกข้อมูล
                        </p>
                    </div>
                </div>
            `
                    }
                ];
            }

            // 📋 Tour Steps: ita_year_link_form_edit.php - หน้าฟอร์มแก้ไขข้อมูล (Steps 38-46)
            getItaYearLinkFormEditTour() {
                return [{
                        title: '📝 หน้าแก้ไขหัวข้อและลิงก์',
                        intro: `
                <div style="text-align: center;">
                    <p style="font-size: 16px; margin-bottom: 16px; line-height: 1.7;">
                        หน้านี้ใช้สำหรับ<br>
                        <strong>แก้ไขชื่อหัวข้อและลิงก์เอกสาร</strong>
                    </p>
                    <div class="intro-info-box">
                        <p style="font-size: 14px; margin: 0; line-height: 1.6;">
                            💡 <strong>สิ่งที่จะแก้ไข:</strong><br>
                            • ชื่อหัวข้อ<br>
                            • ชื่อลิงก์เอกสาร (1-5)<br>
                            • URL เอกสาร (1-5)
                        </p>
                    </div>
                    <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 14px; border-radius: 8px; margin-top: 12px;">
                        <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                            ⭐ <strong>หน้าที่สำคัญของคุณ:</strong><br>
                            ให้ชื่อและ URL ตรงกับเอกสารที่ upload ไว้
                        </p>
                    </div>
                </div>
            `
                    },
                    {
                        element: '.form-group:nth-of-type(1) input[name="ita_year_link_name"]',
                        title: '✍️ ชื่อหัวข้อการประเมิน',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    ฟิลด์นี้ใช้กรอก<strong>ชื่อหัวข้อการประเมิน</strong>
                </p>
                <div class="intro-info-box">
                    <strong>📋 ตัวอย่างชื่อหัวข้อ:</strong>
                    <p style="margin: 8px 0 0 0; font-size: 13px; line-height: 1.7;">
                        • ข้อ 1.1 ข้อมูลพื้นฐาน<br>
                        • ข้อ 2.1 แผนยุทธศาสตร์<br>
                        • ข้อ 3.1 การบริหารงานบุคคล
                    </p>
                </div>
                <div style="background: #DBEAFE; border-left: 4px solid #3B82F6; padding: 12px; border-radius: 8px; margin-top: 12px;">
                    <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                        💡 <strong>เคล็ดลับ:</strong><br>
                        ตั้งชื่อให้ชัดเจน ตรงกับเกณฑ์ ITA<br>
                        และใส่หมายเลขข้อด้วย เช่น "ข้อ 1.1"
                    </p>
                </div>
            `,
                        position: 'bottom'
                    },
                    {
                        element: '.form-group:nth-of-type(2)',
                        title: '🔗 กลุ่มลิงก์ที่ 1 (สำคัญ)',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    ส่วนนี้ใช้กรอก<strong>ชื่อและ URL ของลิงก์แรก</strong>
                </p>
                <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 14px; border-radius: 12px;">
                    <strong style="display: block; margin-bottom: 10px; color: #047857;">
                        📄 ประกอบด้วย 2 ส่วน:
                    </strong>
                    <div style="background: white; padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                        <strong style="color: #065F46; font-size: 14px;">1. ชื่อของลิงก์เพิ่มเติม 1</strong>
                        <p style="margin: 6px 0 0 0; font-size: 13px; line-height: 1.6;">
                            ตัวอย่าง: <code style="background: #F3F4F6; padding: 2px 6px; border-radius: 4px;">แผนยุทธศาสตร์ 2568</code>
                        </p>
                    </div>
                    <div style="background: white; padding: 12px; border-radius: 8px;">
                        <strong style="color: #065F46; font-size: 14px;">2. ลิงค์เพิ่มเติม 1</strong>
                        <p style="margin: 6px 0 0 0; font-size: 13px; line-height: 1.6;">
                            ตัวอย่าง: <code style="background: #F3F4F6; padding: 2px 6px; border-radius: 4px; word-break: break-all;">https://example.com/plan.pdf</code>
                        </p>
                    </div>
                </div>
                <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 12px; border-radius: 8px; margin-top: 12px;">
                    <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                        💡 <strong>เคล็ดลับการตั้งชื่อ:</strong><br>
                        • ใช้ชื่อสั้น กระชับ เข้าใจง่าย<br>
                        • ระบุปีให้ชัดเจน เช่น "2568"<br>
                        • หลีกเลี่ยงชื่อไฟล์ เช่น "file123.pdf"
                    </p>
                </div>
            `,
                        position: 'bottom'
                    },
                    {
                        element: '.form-group:nth-of-type(3), .form-group:nth-of-type(4), .form-group:nth-of-type(5), .form-group:nth-of-type(6), .form-group:nth-of-type(7), .form-group:nth-of-type(8), .form-group:nth-of-type(9), .form-group:nth-of-type(10)',
                        title: '📚 กลุ่มลิงก์ที่ 2-5',
                        intro: `
        <div style="font-size: 13px;">
            <p style="margin-bottom: 8px;">
                ส่วนนี้สำหรับกรอก<strong>ลิงก์เพิ่มเติม</strong><br>
                รวมทั้งหมด <strong>5 ลิงก์</strong>
            </p>
            
            <div style="background: #F0FDF4; border-left: 3px solid #10B981; padding: 8px; border-radius: 6px; margin-bottom: 6px;">
                <strong style="color: #047857; font-size: 12px; display: block; margin-bottom: 6px;">
                    📋 โครงสร้างเหมือนกัน:
                </strong>
                <div style="font-size: 11px; color: #065F46; line-height: 1.5;">
                    แต่ละชุดมี 2 ฟิลด์:<br>
                    • ชื่อของลิงค์เพิ่มเติม<br>
                    • ลิงค์เพิ่มเติม (URL)
                </div>
            </div>
            
            <div style="background: #EFF6FF; border-left: 3px solid #3B82F6; padding: 8px; border-radius: 6px; margin-bottom: 6px;">
                <strong style="color: #1E40AF; font-size: 12px; display: block; margin-bottom: 6px;">
                    📝 ตัวอย่างการใช้งาน 5 ลิงก์:
                </strong>
                <div style="font-size: 11px; color: #1E40AF; line-height: 1.6;">
                    1. <strong>คำสั่งแต่งตั้ง</strong> → PDF<br>
                    2. <strong>แผนปฏิบัติการ</strong> → PDF<br>
                    3. <strong>รายงานผล</strong> → Excel<br>
                    4. <strong>เอกสารประกอบ</strong> → Word<br>
                    5. <strong>รูปภาพกิจกรรม</strong> → Image
                </div>
            </div>
            
            <div style="background: #FEF3C7; border-left: 3px solid #F59E0B; padding: 8px; border-radius: 6px;">
                <strong style="color: #92400E; font-size: 12px; display: block; margin-bottom: 4px;">
                    💡 หมายเหตุ:
                </strong>
                <div style="font-size: 11px; color: #92400E; line-height: 1.5;">
                    • ไม่จำเป็นต้องกรอกครบ 5 ลิงก์<br>
                    • กรอกเฉพาะลิงก์ที่มีเอกสารจริง<br>
                    • ถ้าไม่มีเอกสาร ปล่อยว่างไว้ได้
                </div>
            </div>
        </div>
    `,
                        position: 'bottom',
                        scrollTo: 'tooltip'
                    },
                    {
                        title: '🌐 วิธีการหา URL เอกสาร',
                        intro: `
                <div style="text-align: center;">
                    <p style="font-size: 15px; margin-bottom: 16px; line-height: 1.7;">
                        <strong>วิธีการคัดลอก URL เอกสาร</strong>
                    </p>
                </div>
                <div style="background: #DBEAFE; border-left: 4px solid #3B82F6; padding: 16px; border-radius: 12px;">
                    <strong style="display: block; margin-bottom: 12px; color: #1E40AF; font-size: 15px;">
                        📁 ถ้าเอกสารอยู่ใน Google Drive:
                    </strong>
                    <ol style="margin: 0; padding-left: 20px; line-height: 2.2; font-size: 14px; color: #1E3A8A;">
                        <li>เปิดไฟล์ใน Google Drive</li>
                        <li>คลิกขวาที่ไฟล์ → เลือก "แชร์"</li>
                        <li>คลิก "คัดลอกลิงก์"</li>
                        <li>วาง URL ในฟิลด์ "ลิงค์เพิ่มเติม"</li>
                    </ol>
                </div>
                <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 16px; border-radius: 12px; margin-top: 12px;">
                    <strong style="display: block; margin-bottom: 12px; color: #047857; font-size: 15px;">
                        🌐 ถ้าเอกสารอยู่บนเว็บไซต์:
                    </strong>
                    <ol style="margin: 0; padding-left: 20px; line-height: 2.2; font-size: 14px; color: #065F46;">
                        <li>เปิดหน้าเว็บที่มีเอกสาร</li>
                        <li>คลิกขวาที่ลิงก์เอกสาร → "คัดลอกที่อยู่ลิงก์"</li>
                        <li>หรือคัดลอก URL จากแถบที่อยู่</li>
                        <li>วาง URL ในฟิลด์</li>
                    </ol>
                </div>
                <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 14px; border-radius: 8px; margin-top: 12px;">
                    <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                        ⚠️ <strong>ข้อควรระวัง:</strong><br>
                        • ตรวจสอบว่า URL สามารถเปิดได้<br>
                        • ต้องเป็น URL เต็ม เริ่มต้นด้วย https://<br>
                        • หลีกเลี่ยง URL ที่ต้อง Login ก่อนดู
                    </p>
                </div>
            `
                    },
                    {
                        element: '.form-group:last-of-type button[type="submit"]',
                        title: '💾 บันทึกข้อมูล',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    คลิกปุ่ม<strong>"บันทึกข้อมูล"</strong><br>
                    เพื่อบันทึกการแก้ไข
                </p>
                <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 14px; border-radius: 12px;">
                    <strong style="display: block; margin-bottom: 10px; color: #047857;">
                        ✅ ก่อนบันทึก ควรตรวจสอบ:
                    </strong>
                    <ul style="margin: 0; padding-left: 20px; line-height: 2; font-size: 14px; color: #065F46;">
                        <li>ชื่อหัวข้อถูกต้อง</li>
                        <li>ชื่อลิงก์สื่อความหมายชัดเจน</li>
                        <li>URL ครบถ้วนและถูกต้อง</li>
                        <li>ทดสอบ URL เปิดได้</li>
                    </ul>
                </div>
                <div class="intro-info-box" style="margin-top: 12px;">
                    <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                        💡 <strong>หลังบันทึก:</strong><br>
                        • ระบบจะกลับไปหน้ารายการหัวข้อ<br>
                        • คลิก Refresh เพื่อดูผลการแก้ไข<br>
                        • ตรวจสอบว่าลิงก์เปิดได้
                    </p>
                </div>
            `,
                        position: 'top'
                    },
                    {
                        element: '.form-group:last-of-type a.btn-danger',
                        title: '🚫 ยกเลิกการแก้ไข',
                        intro: `
                <p style="margin-bottom: 12px; font-size: 15px;">
                    คลิกปุ่ม<strong>"ยกเลิก"</strong><br>
                    เพื่อกลับโดยไม่บันทึกการแก้ไข
                </p>
                <div class="intro-warning-box">
                    <p style="margin: 0; font-size: 13px; line-height: 1.6;">
                        ⚠️ <strong>หมายเหตุ:</strong><br>
                        การเปลี่ยนแปลงทั้งหมด<br>
                        จะไม่ถูกบันทึก
                    </p>
                </div>
            `,
                        position: 'top'
                    },
                    {
                        title: '💡 เคล็ดลับการกรอกข้อมูล',
                        intro: `
                <div style="padding: 20px;">
                    <h3 style="margin-bottom: 16px; font-size: 18px; font-weight: 700; color: #1F2937; text-align: center;">
                        เคล็ดลับสำหรับการกรอกข้อมูล
                    </h3>
                    
                    <div style="background: #DBEAFE; border-left: 4px solid #3B82F6; padding: 14px; border-radius: 8px; margin-bottom: 12px;">
                        <strong style="display: block; margin-bottom: 8px; color: #1E40AF;">
                            ✅ DO - ควรทำ:
                        </strong>
                        <ul style="margin: 0; padding-left: 20px; line-height: 2; font-size: 13px; color: #1E3A8A;">
                            <li>ใช้ชื่อที่สื่อความหมายชัดเจน</li>
                            <li>ตรวจสอบ URL ก่อนบันทึก</li>
                            <li>เรียงลำดับเอกสารตามความสำคัญ</li>
                            <li>ระบุปีและรอบการประเมิน</li>
                        </ul>
                    </div>
                    
                    <div style="background: #FEE2E2; border-left: 4px solid #EF4444; padding: 14px; border-radius: 8px;">
                        <strong style="display: block; margin-bottom: 8px; color: #991B1B;">
                            ❌ DON'T - ไม่ควรทำ:
                        </strong>
                        <ul style="margin: 0; padding-left: 20px; line-height: 2; font-size: 13px; color: #7F1D1D;">
                            <li>ใช้ชื่อไฟล์ เช่น "doc123.pdf"</li>
                            <li>ใส่ URL ที่ต้อง Login</li>
                            <li>ใส่ URL ที่หมดอายุ</li>
                            <li>ทิ้งฟิลด์ว่างโดยไม่จำเป็น</li>
                        </ul>
                    </div>
                    
                    <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 14px; border-radius: 8px; margin-top: 12px;">
                        <strong style="display: block; margin-bottom: 8px; color: #047857;">
                            📝 ตัวอย่างที่ดี:
                        </strong>
                        <div style="background: white; padding: 10px; border-radius: 6px; font-size: 12px; font-family: monospace; line-height: 1.8; color: #065F46;">
                            <strong>ชื่อ:</strong> ข้อ 1.1 ข้อมูลพื้นฐาน<br>
                            <strong>ลิงก์ 1:</strong> คำสั่งแต่งตั้งคณะกรรมการ ITA 2568<br>
                            <strong>URL 1:</strong> https://drive.google.com/file/d/xxx<br>
                            <strong>ลิงก์ 2:</strong> โครงสร้างองค์กร 2568<br>
                            <strong>URL 2:</strong> https://example.com/structure.pdf
                        </div>
                    </div>
                </div>
            `
                    },
                    {
                        title: '🎉 เรียนรู้ครบทุกหน้าแล้ว!',
                        intro: `
        <div style="text-align: center; padding: 12px; max-height: 630px; overflow-y: auto;">
            <div style="font-size: 50px; margin-bottom: 8px; line-height: 1;">🎊</div>
            <h2 style="margin-bottom: 8px; font-size: 22px; font-weight: 700; color: #1F2937;">
                ยินดีด้วย! เรียนรู้เสร็จสมบูรณ์
            </h2>
            <p style="font-size: 14px; margin-bottom: 8px; line-height: 1.5; color: #4B5563;">
                คุณพร้อมจัดการระบบ ITA แล้ว
            </p>
            
            <div style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); border: 2px solid #10B981; padding: 12px; border-radius: 12px; margin: 12px 0; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.1);">
                <strong style="display: block; margin-bottom: 10px; color: #047857; font-size: 15px;">
                    📚 สรุปสิ่งที่เรียนรู้:
                </strong>
                <div style="text-align: left;">
                    <div style="background: white; padding: 6px 8px; border-radius: 8px; margin-bottom: 6px;">
                        <strong style="color: #065F46; font-size: 13px;">1️⃣ หน้าจัดการปี</strong>
                        <p style="margin: 4px 0 0 0; font-size: 11px; line-height: 1.3; color: #047857;">
                            เพิ่ม แก้ไข ลบปี พ.ศ. และเข้าจัดการหมวดหมู่
                        </p>
                    </div>
                    <div style="background: white; padding: 6px 8px; border-radius: 8px; margin-bottom: 6px;">
                        <strong style="color: #065F46; font-size: 13px;">2️⃣ หน้าจัดการหมวดหมู่</strong>
                        <p style="margin: 4px 0 0 0; font-size: 11px; line-height: 1.3; color: #047857;">
                            ดูหมวดหมู่ที่ทีมพัฒนาเตรียมไว้ และเข้าจัดการหัวข้อ
                        </p>
                    </div>
                    <div style="background: white; padding: 6px 8px; border-radius: 8px; margin-bottom: 6px;">
                        <strong style="color: #065F46; font-size: 13px;">3️⃣ หน้าจัดการหัวข้อ</strong>
                        <p style="margin: 4px 0 0 0; font-size: 11px; line-height: 1.3; color: #047857;">
                            ดูรายการหัวข้อและลิงก์เอกสาร เข้าหน้าแก้ไข
                        </p>
                    </div>
                    <div style="background: white; padding: 6px 8px; border-radius: 8px;">
                        <strong style="color: #065F46; font-size: 13px;">4️⃣ หน้าแก้ไขข้อมูล</strong>
                        <p style="margin: 4px 0 0 0; font-size: 11px; line-height: 1.3; color: #047857;">
                            แก้ไขชื่อหัวข้อ ชื่อลิงก์ และ URL เอกสาร (1-5)
                        </p>
                    </div>
                </div>
            </div>
                                
            <div style="background: #F3F4F6; border-radius: 10px; padding: 10px; margin-top: 12px;">
                <p style="margin: 0; font-size: 12px; color: #6B7280; line-height: 1.4;">
                    💡 <strong>ต้องการดู Tour อีกครั้ง?</strong><br>
                    คลิกปุ่ม "<strong>แนะนำการใช้งาน</strong>" ได้ตลอดเวลา
                </p>
                <p style="margin: 8px 0 0 0; font-size: 12px; color: #6B7280; line-height: 1.4;">
                    📧 <strong>ติดปัญหา?</strong><br>
                    ติดต่อทีมพัฒนาระบบได้เลย
                </p>
            </div>
        </div>
    `
                    }

                ];
            }

        }

        // 🚀 Initialize Tour System on DOM Ready
        document.addEventListener('DOMContentLoaded', function() {
            const tourManager = new TourManager();
            const currentPage = tourManager.getCurrentPage();
            const urlParams = new URLSearchParams(window.location.search);

            // ✅ Reset Tour via URL
            if (urlParams.get('tour') === 'reset') {
                tourManager.resetTour();
                console.log('🔄 Tour reset via URL parameter');
                window.history.replaceState({}, '', window.location.pathname);
            }

            const tourContinue = urlParams.get('tour') === 'continue';
            if (tourContinue) {
                window.history.replaceState({}, '', window.location.pathname);
            }

            // ✅ Check if should show tour (วันละ 1 ครั้ง)
            if (currentPage && (tourManager.shouldShowTour() || tourContinue)) {
                console.log('📚 Tour should be shown for page:', currentPage);

                setTimeout(() => {
                    if (currentPage === 'System_admin') {
                        tourManager.startTour(currentPage);
                    } else if (currentPage === 'news_backend' && tourContinue) {
                        tourManager.startTour(currentPage);
                    } else if (currentPage === 'operation_reauf_backend' && tourContinue) {
                        tourManager.startTour(currentPage);
                    } else if (currentPage === 'Ita_year_backend') {
                        tourManager.startTour(currentPage);
                    } else if (currentPage === 'Ita_year_topic' && tourContinue) {
                        tourManager.startTour(currentPage);
                    } else if (currentPage === 'Ita_year_link' && tourContinue) {
                        tourManager.startTour(currentPage);
                    } else if (currentPage === 'Ita_year_link_form_edit' && tourContinue) {
                        tourManager.startTour(currentPage);
                    }
                }, 1000);
            } else {
                if (currentPage) {
                    console.log('✅ Tour already completed today for page:', currentPage);
                }
            }

            window.tourManager = tourManager;

            // ✅ Redirect Handler - Step 4 → news_backend
            if (currentPage === 'System_admin') {
                setTimeout(() => {
                    const intro = tourManager.currentIntro;
                    if (intro) {
                        intro.onbeforechange(function(targetElement) {
                            const currentStep = this._currentStep;

                            // Step 4 (index 3) คือ step สุดท้าย
                            // ถ้ากด Next จาก Step 3 → redirect แทน
                            if (currentStep === 3 && this._direction === 'forward') {
                                setTimeout(() => {
                                    window.location.href = window.base_url + 'news_backend?tour=continue';
                                }, 100);
                                return false; // ยกเลิกการไปต่อ
                            }
                            return true;
                        });

                        // ✅ เปลี่ยนปุ่ม "เสร็จสิ้น" เป็น "ถัดไป" ด้วยการแทนที่ label
                        intro.onafterchange(function(targetElement) {
                            const currentStep = this._currentStep;

                            if (currentStep === 3) {
                                // หาปุ่ม Done และเปลี่ยน text
                                const doneButton = document.querySelector('.introjs-donebutton');
                                if (doneButton) {
                                    doneButton.textContent = 'ถัดไป →';
                                    doneButton.classList.remove('introjs-donebutton');
                                    doneButton.classList.add('introjs-nextbutton');
                                }
                            }
                        });
                    }
                }, 1200);
            }

            // ✅ Redirect Handler - Step 7 → operation_reauf_backend
            if (currentPage === 'news_backend') {
                setTimeout(() => {
                    const intro = tourManager.currentIntro;
                    if (intro) {
                        intro.onbeforechange(function(targetElement) {
                            const currentStep = this._currentStep;
                            if (currentStep === 2) {
                                setTimeout(() => {
                                    window.location.href = window.base_url + 'operation_reauf_backend?tour=continue';
                                }, 500);
                                return false;
                            }
                            return true;
                        });
                    }
                }, 1200);
            }


        });

        // 🧪 Test Functions (Console Commands)
        window.testTour = function() {
            console.log('🧪 Testing tour - Resetting and reloading...');
            window.tourManager.resetTour();
            location.reload();
        };

        window.checkTourStatus = function() {
            const data = window.tourManager.getTourData();
            console.log('📊 Tour Status:', data);
            if (data) {
                console.log('   ✅ Completed:', data.completed);
                console.log('   📅 Date:', data.date);
                console.log('   🕒 Timestamp:', new Date(data.timestamp).toLocaleString('th-TH'));
            } else {
                console.log('   ❌ No tour data found');
            }
            return data;
        };

        window.resetTourNow = function() {
            console.log('🔄 Resetting tour data...');
            window.tourManager.resetTour();
            console.log('✅ Tour reset complete. Reload page to see tour again.');
        };
    </script>

    <!-- 🎯 ===== TOUR SYSTEM END ===== -->

    <!-- Video.js -->
    <link href="https://vjs.zencdn.net/7.14.3/video-js.css" rel="stylesheet">
    <script src="https://vjs.zencdn.net/7.14.3/video.js"></script>

    <!-- Font Awesome -->
    <link href="<?= base_url(); ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- SB Admin 2 CSS -->
    <link href="<?= base_url('asset/'); ?>css/sb-admin-2.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="<?= base_url(); ?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- Lightbox -->
    <link href="<?= base_url('asset/'); ?>lightbox2/src/css/lightbox.css" rel="stylesheet">

    <!-- jQuery - โหลดก่อน script อื่นๆ -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"
        onerror="console.log('Bootstrap CDN failed, using fallback')"></script>

    <!-- Fancybox CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

    <!-- 🔧 REQUIRED: Session Manager -->
    <script src="<?php echo base_url('asset/js/session-manager.js'); ?>"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        // === DISABLE ALL DEBUG/CONSOLE LOGS ===
        (function() {
            // ปิด console ทั้งหมด
            console.log = function() {};
            console.warn = function() {};
            console.info = function() {};
            console.debug = function() {};
            // เก็บ console.error ไว้เฉพาะ error ที่สำคัญ (optional)
            // console.error = function() {};
        })();
    </script>



    <style>
        :root {
            /* Modern Soft Color Palette */
            --primary-soft: #667eea;
            --primary-light: #f093fb;
            --secondary-soft: #a8edea;
            --success-soft: #88d8c0;
            --warning-soft: #ffeaa7;
            --danger-soft: #fd79a8;
            --info-soft: #74b9ff;
            --light-soft: #fdcb6e;

            /* Gradients */
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --gradient-success: linear-gradient(135deg, #88d8c0 0%, #6bb6ff 100%);
            --gradient-warning: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%);
            --gradient-info: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            --gradient-danger: linear-gradient(135deg, #fd79a8 0%, #fdcb6e 100%);

            /* Backgrounds */
            --bg-soft: #f8f9ff;
            --card-bg: #ffffff;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --card-shadow-hover: 0 8px 40px rgba(0, 0, 0, 0.12);

            /* Text Colors */
            --text-primary: #2d3748;
            --text-secondary: #4a5568;
            --text-muted: #718096;
            --text-light: #a0aec0;
        }

        body {
            font-family: 'Inter', 'Kanit', sans-serif;
            background: var(--bg-soft);
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* Card Enhancements */
        .card {
            border: none !important;
            border-radius: 20px !important;
            background: var(--card-bg) !important;
            box-shadow: var(--card-shadow) !important;
            transition: all 0.3s ease !important;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover) !important;
        }

        .card-header {
            background: transparent !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
            padding: 1.5rem !important;
        }

        .card-body {
            padding: 1.5rem !important;
        }

        /* Modern Gradients for Storage Card */
        .card-body-icon {
            background: var(--gradient-primary) !important;
            border-radius: 12px;
        }

        .bg-primary .card-body-icon {
            background: var(--gradient-primary) !important;
        }

        .bg-success .card-body-icon {
            background: var(--gradient-success) !important;
        }

        .bg-warning .card-body-icon {
            background: var(--gradient-warning) !important;
        }

        .bg-info .card-body-icon {
            background: var(--gradient-info) !important;
        }

        /* Progress Bars */
        .progress {
            border-radius: 12px !important;
            background: rgba(0, 0, 0, 0.05) !important;
            height: 8px !important;
        }

        .progress-bar {
            border-radius: 12px !important;
            background: var(--gradient-success) !important;
            transition: all 0.3s ease;
        }

        /* Custom Progress Colors */
        .progress-green .progress-bar {
            background: var(--gradient-success) !important;
        }

        .progress-orange .progress-bar {
            background: var(--gradient-warning) !important;
        }

        .progress-red .progress-bar {
            background: var(--gradient-danger) !important;
        }

        /* Typography */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 600;
            color: var(--text-primary);
        }

        .font-weight-bold {
            font-weight: 600 !important;
        }

        /* Buttons */
        .btn {
            border-radius: 12px !important;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .btn-sky {
            background: var(--gradient-success) !important;
            color: #fff !important;
            border: none !important;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-sky:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(136, 216, 192, 0.4);
            color: #fff !important;
        }

        /* Status Badges */
        .status-badge {
            border-radius: 20px !important;
            padding: 6px 12px !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            border: 2px solid !important;
            display: inline-block;
            min-width: 100px;
            text-align: center;
        }

        /* Complain Status Colors - Soft Theme */
        .status-received {
            background: rgba(116, 185, 255, 0.1) !important;
            color: #3182ce !important;
            border-color: rgba(116, 185, 255, 0.3) !important;
        }

        .status-processing {
            background: rgba(159, 122, 234, 0.1) !important;
            color: #6b46c1 !important;
            border-color: rgba(159, 122, 234, 0.3) !important;
        }

        .status-waiting {
            background: rgba(255, 178, 102, 0.1) !important;
            color: #d69e2e !important;
            border-color: rgba(255, 178, 102, 0.3) !important;
        }

        .status-completed {
            background: rgba(136, 216, 192, 0.1) !important;
            color: #38a169 !important;
            border-color: rgba(136, 216, 192, 0.3) !important;
        }

        .status-cancelled {
            background: rgba(253, 121, 168, 0.1) !important;
            color: #e53e3e !important;
            border-color: rgba(253, 121, 168, 0.3) !important;
        }

        /* Member Progress Bars */
        .member-progress {
            height: 35px !important;
            border-radius: 15px !important;
            background: rgba(0, 0, 0, 0.03) !important;
            margin-bottom: 12px !important;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
        }

        .member-progress .progress-bar {
            background: var(--gradient-success) !important;
            border: none !important;
            border-radius: 15px !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 15px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #fff !important;
            position: relative;
        }

        .member-progress .member-name {
            flex: 1;
            text-align: left;
        }

        .member-progress .member-count {
            font-weight: 600;
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 12px;
            margin-left: auto;
        }

        /* Visitor Progress Bars */
        .visitor-progress {
            height: 35px !important;
            border-radius: 15px !important;
            background: rgba(0, 0, 0, 0.03) !important;
            margin-bottom: 12px !important;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
        }

        .visitor-progress .progress-bar {
            background: var(--gradient-warning) !important;
            border: none !important;
            border-radius: 15px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 0 15px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #fff !important;
        }

        .visitor-progress .member-name {
            text-align: left;
        }

        .visitor-progress .member-count {
            text-align: right;
        }

        .visitor-progress .member-name {
            flex: 1;
            text-align: left;
        }

        .visitor-progress .member-count {
            font-weight: 600;
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 12px;
            margin-left: auto;
        }

        /* Dots for Complain Status */
        .dot_complain1,
        .dot_complain2,
        .dot_complain3,
        .dot_complain4,
        .dot_complain5 {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .dot_complain1 {
            background: var(--success-soft);
        }

        .dot_complain2 {
            background: var(--info-soft);
        }

        .dot_complain3 {
            background: var(--primary-soft);
        }

        .dot_complain4 {
            background: var(--warning-soft);
        }

        .dot_complain5 {
            background: var(--danger-soft);
        }

        /* 🚨 REQUIRED: Session Warning Modals Styles */
        .modal {
            z-index: 9999 !important;
        }

        .modal-backdrop {
            z-index: 9998 !important;
        }

        .modal-dialog {
            z-index: 10000 !important;
            position: relative;
        }

        .modal-content {
            position: relative;
            z-index: 10001 !important;
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
            overflow: hidden;
        }

        .modal-header {
            border-radius: 20px 20px 0 0 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        /* Session Modal Animations */
        .timeout-icon i,
        .logout-icon i {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .timeout-title,
        .logout-title {
            font-weight: 600;
            margin-bottom: 15px;
        }

        .timeout-message,
        .logout-message {
            line-height: 1.6;
            color: #666;
        }

        /* Responsive สำหรับ Session Modals */
        @media (max-width: 576px) {

            .timeout-icon i,
            .logout-icon i {
                font-size: 3rem !important;
            }

            .timeout-title,
            .logout-title {
                font-size: 1.2rem;
            }
        }

        /* Alert floating styles */
        .alert-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            min-width: 300px;
            max-width: 500px;
            border-radius: 16px !important;
            border: none !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Container improvements */
        .container-fluid,
        .container {
            max-width: none !important;
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        .col-xl-3,
        .col-xl-4,
        .col-xl-5,
        .col-md-3,
        .col-md-4,
        .col-md-5 {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }

        /* Text utilities */
        .text-soft {
            color: var(--text-muted) !important;
        }

        .text-primary-soft {
            color: var(--primary-soft) !important;
        }

        /* Links */
        a {
            color: var(--primary-soft);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        a:hover {
            color: var(--primary-light);
            text-decoration: none;
        }

        /* Small font adjustments */
        .small-font {
            font-size: 13px !important;
            color: var(--text-muted) !important;
            font-weight: 500;
        }

        /* View link styling */
        .view-link {
            color: var(--primary-soft) !important;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .view-link:hover {
            color: var(--primary-light) !important;
            transform: translateX(2px);
        }

        /* One line ellipsis */
        .one-line-ellipsis {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            line-height: 1.4;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gradient-primary);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gradient-secondary);
        }

        /* Chart container improvements */
        .chart-container {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
        }

        /* ApexCharts overrides */
        .apexcharts-canvas {
            font-family: 'Inter', 'Kanit', sans-serif !important;
        }

        .apexcharts-title-text {
            font-weight: 600 !important;
            fill: var(--text-primary) !important;
        }

        .apexcharts-legend-text {
            color: var(--text-secondary) !important;
            font-weight: 500 !important;
        }
    </style>

    <script>
        // Fancybox initialization - รอ DOM ready
        $(document).ready(function() {
            $('[data-fancybox="gallery"]').fancybox({
                buttons: ["zoom", "slideShow", "fullScreen", "thumbs", "close"],
                loop: true,
                protect: true
            });
        });

        // ลบ Bootstrap local references อัตโนมัติ
        document.addEventListener('DOMContentLoaded', function() {
            // ลบ script tags ที่เรียกใช้ local Bootstrap
            const scripts = document.querySelectorAll('script[src*="vendor/bootstrap"]');
            scripts.forEach(script => {
                console.log('🗑️ Removing local Bootstrap script:', script.src);
                script.remove();
            });

            // ลบ link tags ที่เรียกใช้ local Bootstrap CSS
            const links = document.querySelectorAll('link[href*="vendor/bootstrap"]');
            links.forEach(link => {
                console.log('🗑️ Removing local Bootstrap CSS:', link.href);
                link.remove();
            });

            console.log('✅ Bootstrap cleanup completed');
        });
    </script>



</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">