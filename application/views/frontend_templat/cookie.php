<!-- View file: application/views/frontend_templat/cookie.php -->
<?php if ($show_cookie_consent): ?>

    <!-- Cookie Banner -->
    <div id="cookie-consent" class="cookie-banner" style="display: none;">
        <div class="cookie-content">
            <div class="cookie-icon">
                <!-- Shield Privacy Icon -->
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L4 6V12C4 16.4183 7.58172 20 12 20C16.4183 20 20 16.4183 20 12V6L12 2Z"
                        fill="url(#shieldGradient)" stroke="#1e40af" stroke-width="1.5" />
                    <path d="M9 12L11 14L15 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <defs>
                        <linearGradient id="shieldGradient" x1="4" y1="2" x2="20" y2="20">
                            <stop offset="0%" stop-color="#3b82f6" />
                            <stop offset="100%" stop-color="#1e40af" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <div class="cookie-text">
                <h3>นโยบายการใช้คุกกี้</h3>
                <p><?php echo get_config_value('fname'); ?> ใช้คุกกี้เพื่อเพิ่มประสิทธิภาพและประสบการณ์ที่ดีในการใช้งานเว็บไซต์ ท่านสามารถเลือกตั้งค่าความยินยอมการใช้คุกกี้ได้ตามความต้องการ</p>
            </div>
            <div class="cookie-buttons">
                <button id="show-details" class="btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                    </svg>
                    ตั้งค่า
                </button>
                <a href="<?php echo site_url('Policy/cookie'); ?>" class="btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                        <polyline points="10 9 9 9 8 9" />
                    </svg>
                    นโยบาย
                </a>
                <button id="accept-cookie" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    ยอมรับทั้งหมด
                </button>
            </div>
        </div>
    </div>

    <!-- Cookie Settings Modal -->
    <div id="cookie-modal" class="cookie-modal" style="display: none;">
        <div class="modal-backdrop" id="modal-backdrop"></div>
        <div class="modal-container">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <div class="header-title">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L4 6V12C4 16.4183 7.58172 20 12 20C16.4183 20 20 16.4183 20 12V6L12 2Z"
                                fill="#3b82f6" stroke="#1e40af" stroke-width="1.5" />
                            <rect x="10" y="8" width="4" height="6" rx="1" fill="white" />
                            <circle cx="12" cy="16" r="1" fill="white" />
                        </svg>
                        <h3>การตั้งค่าความเป็นส่วนตัว</h3>
                    </div>
                    <button class="close-btn" id="close-modal">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <!-- คุกกี้พื้นฐาน -->
                    <div class="cookie-option essential">
                        <div class="option-header">
                            <div class="option-info">
                                <div class="option-icon essential-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2L4 6V12C4 16.4183 7.58172 20 12 20C16.4183 20 20 16.4183 20 12V6L12 2Z" />
                                        <polyline points="9 12 11 14 15 10" />
                                    </svg>
                                </div>
                                <h4>คุกกี้พื้นฐานที่จำเป็น</h4>
                            </div>
                            <span class="always-on">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                เปิดใช้งานเสมอ
                            </span>
                        </div>
                        <p class="option-description">
                            คุกกี้ที่จำเป็นสำหรับการทำงานของเว็บไซต์ เพื่อให้ท่านสามารถเข้าถึงข้อมูลและบริการต่างๆ ได้อย่างปลอดภัย หากปิดการใช้งานคุกกี้นี้ เว็บไซต์จะไม่สามารถทำงานได้อย่างเหมาะสม
                        </p>
                    </div>

                    <!-- คุกกี้วิเคราะห์ -->
                    <div class="cookie-option">
                        <div class="option-header">
                            <div class="option-info">
                                <div class="option-icon analytics-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="20" x2="18" y2="10" />
                                        <line x1="12" y1="20" x2="12" y2="4" />
                                        <line x1="6" y1="20" x2="6" y2="14" />
                                    </svg>
                                </div>
                                <h4>คุกกี้เพื่อการวิเคราะห์</h4>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="analytics-cookie">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <p class="option-description">
                            คุกกี้ที่ช่วยให้เราเข้าใจรูปแบบการใช้งานของผู้เข้าชม เพื่อนำข้อมูลมาปรับปรุงและพัฒนาเว็บไซต์ให้ตอบสนองความต้องการของประชาชนได้ดียิ่งขึ้น
                        </p>
                    </div>

                    <!-- คุกกี้การตลาด -->
                    <div class="cookie-option">
                        <div class="option-header">
                            <div class="option-info">
                                <div class="option-icon marketing-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                                    </svg>
                                </div>
                                <h4>คุกกี้เพื่อการสื่อสาร</h4>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="marketing-cookie">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <p class="option-description">
                            คุกกี้ที่ใช้เพื่อนำเสนอข้อมูลข่าวสารและบริการที่เกี่ยวข้องกับความสนใจของท่าน เพื่อให้ท่านได้รับข้อมูลที่เป็นประโยชน์มากที่สุด
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button class="btn-outline" id="reject-all">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                        ปฏิเสธทั้งหมด
                    </button>
                    <button class="btn-primary confirm-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        ยืนยันการตั้งค่า
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================
   Variables
   ============================================ */
        :root {
            --gov-primary: #1e40af;
            --gov-primary-light: #3b82f6;
            --gov-secondary: #1e3a5f;
            --gov-success: #047857;
            --gov-success-light: #10b981;
            --gov-gray-50: #f8fafc;
            --gov-gray-100: #f1f5f9;
            --gov-gray-200: #e2e8f0;
            --gov-gray-300: #cbd5e1;
            --gov-gray-500: #64748b;
            --gov-gray-700: #334155;
            --gov-gray-900: #0f172a;
            --gov-radius: 12px;
            --gov-radius-lg: 16px;
            --gov-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --gov-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* ============================================
   Cookie Banner
   ============================================ */
        .cookie-banner {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 40px);
            max-width: 960px;
            background: linear-gradient(135deg, #ffffff 0%, var(--gov-gray-50) 100%);
            border: 1px solid var(--gov-gray-200);
            border-left: 4px solid var(--gov-primary);
            border-radius: var(--gov-radius-lg);
            box-shadow: var(--gov-shadow-lg);
            z-index: 99999;
            animation: slideUp 0.4s ease-out forwards;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        .cookie-content {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 24px;
        }

        .cookie-icon {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cookie-text {
            flex: 1;
        }

        .cookie-text h3 {
            margin: 0 0 8px 0;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gov-gray-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cookie-text p {
            margin: 0;
            font-size: 0.875rem;
            color: var(--gov-gray-500);
            line-height: 1.6;
        }

        .cookie-buttons {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }

        /* ============================================
   Buttons
   ============================================ */
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: white;
            border: 1px solid var(--gov-gray-200);
            border-radius: var(--gov-radius);
            color: var(--gov-gray-700);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: var(--gov-gray-50);
            border-color: var(--gov-primary-light);
            color: var(--gov-primary);
        }

        .btn-secondary svg {
            opacity: 0.7;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--gov-primary-light) 0%, var(--gov-primary) 100%);
            border: none;
            border-radius: var(--gov-radius);
            color: white;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(30, 64, 175, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: transparent;
            border: 1px solid var(--gov-gray-200);
            border-radius: var(--gov-radius);
            color: var(--gov-gray-500);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-outline:hover {
            background: var(--gov-gray-50);
            border-color: var(--gov-gray-300);
        }

        /* ============================================
   Modal - แก้ไข z-index
   ============================================ */
        .cookie-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 100000;
        }

        /* Backdrop อยู่ชั้นล่าง */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            z-index: 100001;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Container อยู่เหนือ backdrop */
        .modal-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: calc(100% - 40px);
            max-width: 560px;
            max-height: calc(100vh - 40px);
            z-index: 100002;
            animation: modalIn 0.3s ease;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        .modal-content {
            background: white;
            border-radius: var(--gov-radius-lg);
            box-shadow: var(--gov-shadow-lg);
            overflow: hidden;
            position: relative;
            z-index: 100003;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background: linear-gradient(135deg, var(--gov-primary) 0%, var(--gov-secondary) 100%);
            color: white;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-title svg {
            flex-shrink: 0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .close-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .modal-body {
            padding: 20px;
            max-height: 380px;
            overflow-y: auto;
        }

        .cookie-option {
            padding: 18px;
            margin-bottom: 12px;
            background: var(--gov-gray-50);
            border: 1px solid var(--gov-gray-200);
            border-radius: var(--gov-radius);
            transition: all 0.2s ease;
        }

        .cookie-option:last-child {
            margin-bottom: 0;
        }

        .cookie-option:hover {
            border-color: var(--gov-gray-300);
        }

        .cookie-option.essential {
            background: linear-gradient(135deg, rgba(4, 120, 87, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%);
            border-color: rgba(4, 120, 87, 0.2);
        }

        .option-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .option-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .option-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 8px;
            box-shadow: var(--gov-shadow);
        }

        .option-icon.essential-icon {
            background: linear-gradient(135deg, var(--gov-success-light) 0%, var(--gov-success) 100%);
            color: white;
        }

        .option-icon.analytics-icon {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            color: white;
        }

        .option-icon.marketing-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .option-info h4 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--gov-gray-900);
        }

        .always-on {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            color: var(--gov-success);
            font-weight: 600;
            background: rgba(4, 120, 87, 0.1);
            padding: 4px 10px;
            border-radius: 20px;
        }

        .option-description {
            margin: 0;
            font-size: 0.8rem;
            color: var(--gov-gray-500);
            line-height: 1.6;
        }

        /* ============================================
   Toggle Switch - เพิ่ม z-index และ position
   ============================================ */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 26px;
            cursor: pointer;
            z-index: 100004;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }

        .toggle-slider {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gov-gray-200);
            border-radius: 26px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .toggle-slider::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .toggle-switch input:checked+.toggle-slider {
            background: linear-gradient(135deg, var(--gov-primary-light) 0%, var(--gov-primary) 100%);
        }

        .toggle-switch input:checked+.toggle-slider::before {
            transform: translateX(22px);
        }

        .toggle-switch:hover .toggle-slider {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 16px 24px;
            background: var(--gov-gray-50);
            border-top: 1px solid var(--gov-gray-200);
        }

        /* ============================================
   Scrollbar
   ============================================ */
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: var(--gov-gray-300);
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: var(--gov-gray-400);
        }

        /* ============================================
   Responsive
   ============================================ */
        @media (max-width: 768px) {
            .cookie-banner {
                bottom: 10px;
                width: calc(100% - 20px);
                border-radius: var(--gov-radius);
            }

            .cookie-content {
                flex-direction: column;
                text-align: center;
                padding: 20px;
                gap: 16px;
            }

            .cookie-buttons {
                width: 100%;
                flex-direction: column;
            }

            .btn-secondary,
            .btn-primary {
                width: 100%;
                justify-content: center;
            }

            .modal-container {
                width: calc(100% - 20px);
            }

            .option-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .modal-footer {
                flex-direction: column;
            }

            .btn-outline,
            .confirm-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        (function() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCookieConsent);
            } else {
                initCookieConsent();
            }

            function initCookieConsent() {
                var banner = document.getElementById('cookie-consent');
                var modal = document.getElementById('cookie-modal');
                var showDetailsBtn = document.getElementById('show-details');
                var closeModalBtn = document.getElementById('close-modal');
                var acceptBtn = document.getElementById('accept-cookie');
                var confirmBtn = document.querySelector('.confirm-btn');
                var rejectAllBtn = document.getElementById('reject-all');
                var backdrop = document.getElementById('modal-backdrop');

                if (!banner || !modal) {
                    return;
                }

                // ตรวจสอบว่าเคยยอมรับคุกกี้หรือยัง
                var hasAccepted = getCookie('cookie') || getCookie('cookie_consent');

                if (!hasAccepted) {
                    setTimeout(function() {
                        banner.style.display = 'block';
                    }, 500);
                }

                // เปิด Modal
                if (showDetailsBtn) {
                    showDetailsBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        modal.style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    });
                }

                // ปิด Modal
                function closeModal() {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }

                if (closeModalBtn) {
                    closeModalBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        closeModal();
                    });
                }

                if (backdrop) {
                    backdrop.addEventListener('click', function(e) {
                        if (e.target === backdrop) {
                            closeModal();
                        }
                    });
                }

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && modal.style.display === 'block') {
                        closeModal();
                    }
                });

                // ยอมรับทั้งหมด
                if (acceptBtn) {
                    acceptBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        document.getElementById('analytics-cookie').checked = true;
                        document.getElementById('marketing-cookie').checked = true;
                        saveCookieConsent();
                    });
                }

                // ยืนยันตัวเลือก
                if (confirmBtn) {
                    confirmBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        saveCookieConsent();
                    });
                }

                // ปฏิเสธทั้งหมด
                if (rejectAllBtn) {
                    rejectAllBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        document.getElementById('analytics-cookie').checked = false;
                        document.getElementById('marketing-cookie').checked = false;
                        saveCookieConsent();
                    });
                }

                // บันทึก Cookie Consent
                function saveCookieConsent() {
                    var analytics = document.getElementById('analytics-cookie').checked;
                    var marketing = document.getElementById('marketing-cookie').checked;

                    fetch('<?php echo base_url("Cookie/accept"); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                session_id: '<?php echo session_id(); ?>',
                                device: navigator.userAgent,
                                analytics: analytics,
                                marketing: marketing
                            })
                        })
                        .then(function(response) {
                            return response.json();
                        })
                        .then(function(data) {
                            if (data.success) {
                                banner.style.display = 'none';
                                closeModal();
                                setCookie('cookie', 'accepted', 365);
                            }
                        })
                        .catch(function(error) {
                            console.error('Cookie consent error:', error);
                            banner.style.display = 'none';
                            closeModal();
                            setCookie('cookie', 'accepted', 365);
                        });
                }

                function setCookie(name, value, days) {
                    var expires = '';
                    if (days) {
                        var date = new Date();
                        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                        expires = '; expires=' + date.toUTCString();
                    }
                    document.cookie = name + '=' + (value || '') + expires + '; path=/';
                }

                function getCookie(name) {
                    var nameEQ = name + '=';
                    var ca = document.cookie.split(';');
                    for (var i = 0; i < ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
                    }
                    return null;
                }
            }
        })();
    </script>

<?php endif; ?>