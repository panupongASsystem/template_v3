<!-- View file: application/views/components/cookie.php -->
<?php if ($show_cookie_consent): ?>
    <div id="cookie-consent" class="cookie-banner" style="display: none;">
        <div class="cookie-content">
            <h3>เว็บไซต์นี้ใช้คุกกี้</h3>
            <p><?php echo get_config_value('fname'); ?> ใช้คุกกี้เพื่อเพิ่มประสิทธิภาพ และประสบการณ์ที่ดีในการใช้งานเว็บไซต์<br>
                คุณสามารถเลือกตั้งค่าความยินยอมการใช้คุกกี้ได้ โดยคลิก "การตั้งค่าคุกกี้"</p>
            <div class="cookie-buttons">
                <button id="show-details" class="btn-secondary">การตั้งค่าคุกกี้</button>
                <a href="<?php echo site_url('Policy/cookie'); ?>" class="btn-secondary" style="text-decoration: none;">นโยบาย การใช้งานคุ๊กกี้</a>
                <button id="accept-cookie" class="btn-primary">ยอมรับทั้งหมด</button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="cookie-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>การตั้งค่าความเป็นส่วนตัว</h3>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <!-- คุกกี้พื้นฐาน -->
                <div class="cookie-section">
                    <div class="cookie-header">
                        <div class="cookie-title">คุกกี้พื้นฐานที่จำเป็น</div>
                        <div class="always-active">เปิดใช้งานตลอดเวลา</div>
                    </div>
                    <div class="cookie-description">
                        คุกกี้พื้นฐานที่จำเป็น เพื่อช่วยให้การทำงานหลักของเว็บไซต์ใช้งานได้ รวมถึงการเข้าถึงพื้นที่ที่มีปลอดภัยต่าง ๆ ของเว็บไซต์ หากไม่มีคุกกี้นี้เว็บไซต์จะไม่สามารถทำงานได้อย่างเหมาะสม และจะใช้งานได้โดยการตั้งค่าเริ่มต้น โดยไม่สามารถปิดการใช้งานได้
                    </div>
                </div>

                <!-- คุกกี้วิเคราะห์ -->
                <div class="cookie-section">
                    <div class="cookie-header">
                        <div class="cookie-title">คุกกี้ในส่วนวิเคราะห์</div>
                        <label class="switch">
                            <input type="checkbox" id="analytics-cookie">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="cookie-description">
                        คุกกี้ในส่วนวิเคราะห์ จะช่วยให้เว็บไซต์เข้าใจรูปแบบการใช้งานของผู้เข้าชมและช่วยปรับปรุงประสบการณ์การใช้งาน โดยการเก็บรวบรวมข้อมูลและรายงานผลการใช้งานของผู้ใช้งาน
                    </div>
                </div>

                <!-- คุกกี้การตลาด -->
                <div class="cookie-section">
                    <div class="cookie-header">
                        <div class="cookie-title">คุกกี้ในส่วนการตลาด</div>
                        <label class="switch">
                            <input type="checkbox" id="marketing-cookie">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="cookie-description">
                        คุกกี้ในส่วนการตลาด ใช้เพื่อติดตามพฤติกรรมผู้เข้าชมเว็บไซต์เพื่อแสดงโฆษณาที่เหมาะสมสำหรับผู้ใช้งานแต่ละรายและเพื่อเพิ่มประสิทธิผลการโฆษณาสำหรับผู้เผยแพร่และผู้โฆษณาสำหรับบุคคลที่สาม
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="confirm-btn">ยืนยันตัวเลือกของฉัน</button>
            </div>
        </div>
    </div>

    <style>
        /* CSS เดิมทั้งหมด - ไม่เปลี่ยน */
        .cookie-banner {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            z-index: 1000;
            backdrop-filter: blur(8px);
        }

        .cookie-content {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .cookie-content h3 {
            font-size: 1.5rem;
            margin: 0;
            color: #333;
        }

        .cookie-content p {
            margin: 0;
            font-size: 1.2rem;
            color: #666;
            line-height: 1.5;
        }

        .cookie-buttons {
            display: flex;
            gap: 0.75rem;
            margin-left: auto;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: transparent;
            color: #4b5563;
            padding: 0.75rem 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
        }

        .cookie-section {
            padding: 20px;
            border-bottom: 1px solid #E5E7EB;
        }

        .cookie-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .cookie-title {
            font-weight: 600;
            font-size: 16px;
            color: #111827;
        }

        .cookie-description {
            font-size: 14px;
            color: #6B7280;
            line-height: 1.5;
        }

        .always-active {
            color: #10B981;
            font-size: 14px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #E5E7EB;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #3B82F6;
        }

        input:checked+.slider:before {
            transform: translateX(16px);
        }

        .modal-content {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            border-radius: 8px;
            padding: 0;
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .modal-footer {
            padding: 20px;
            display: flex;
            justify-content: flex-end;
            border-top: 1px solid #fff;
        }

        .confirm-btn {
            background: #3B82F6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .close {
            position: absolute;
            right: 1.5rem;
            top: 1.5rem;
            font-size: 24px;
            color: #9ca3af;
            cursor: pointer;
            transition: color 0.2s;
        }

        .close:hover {
            color: #4b5563;
        }
    </style>

    <script>
        // ========================================
        // Browser Fingerprint Generator
        // ========================================
        async function generateFingerprint() {
            try {
                const components = {
                    screenResolution: `${screen.width}x${screen.height}`,
                    screenColorDepth: screen.colorDepth,
                    userAgent: navigator.userAgent,
                    language: navigator.language,
                    platform: navigator.platform,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                    timezoneOffset: new Date().getTimezoneOffset(),
                    hardwareConcurrency: navigator.hardwareConcurrency || 'unknown',
                    deviceMemory: navigator.deviceMemory || 'unknown',
                    cookieEnabled: navigator.cookieEnabled,
                    doNotTrack: navigator.doNotTrack || 'unknown'
                };

                const fingerprintString = JSON.stringify(components);
                const fingerprint = await hashString(fingerprintString);
                
                return fingerprint;
            } catch (error) {
                console.error('Fingerprint error:', error);
                return await hashString(navigator.userAgent + screen.width + screen.height);
            }
        }

        async function hashString(str) {
            const encoder = new TextEncoder();
            const data = encoder.encode(str);
            const hashBuffer = await crypto.subtle.digest('SHA-256', data);
            const hashArray = Array.from(new Uint8Array(hashBuffer));
            const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
            return hashHex;
        }

        // ========================================
        // Cookie Consent
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            if (!getCookie('cookie')) {
                document.getElementById('cookie-consent').style.display = 'block';
            }

            document.getElementById('accept-cookie').addEventListener('click', function() {
                document.getElementById('analytics-cookie').checked = true;
                document.getElementById('marketing-cookie').checked = true;
                acceptCookie();
            });

            document.querySelector('.confirm-btn').addEventListener('click', function() {
                acceptCookie();
            });

            // Modal Controls
            var modal = document.getElementById('cookie-modal');
            var btn = document.getElementById('show-details');
            var span = document.getElementsByClassName('close')[0];

            btn.onclick = function() {
                modal.style.display = 'block';
            }

            span.onclick = function() {
                modal.style.display = 'none';
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        });

        async function acceptCookie() {
            try {
                // สร้าง fingerprint
                const fingerprint = await generateFingerprint();
                localStorage.setItem('browser_fingerprint', fingerprint);

                const cookieTypes = {
                    analytics: document.getElementById('analytics-cookie').checked,
                    marketing: document.getElementById('marketing-cookie').checked
                };

                // ส่งข้อมูล (เหมือนเดิม + fingerprint)
                const response = await fetch('<?php echo base_url("Cookie/accept"); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        session_id: '<?php echo session_id(); ?>',
                        device: navigator.userAgent,
                        fingerprint: fingerprint,
                        analytics: cookieTypes.analytics,
                        marketing: cookieTypes.marketing
                    })
                });

                // แก้ไข: รับ response เป็น text ก่อน แล้วค่อย parse
                const responseText = await response.text();
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    // ถ้า parse ไม่ได้ ให้ถือว่า success
                    console.warn('Response is not JSON, assuming success');
                    data = { success: true };
                }

                // ซ่อน banner เสมอ (ไม่ว่า API จะตอบอะไร)
                document.getElementById('cookie-consent').style.display = 'none';
                document.getElementById('cookie-modal').style.display = 'none';
                setCookie('cookie', 'accepted', 30);

            } catch (error) {
                console.error('Error:', error);
                // แม้ error ก็ให้ซ่อน banner (UX ที่ดี)
                document.getElementById('cookie-consent').style.display = 'none';
                document.getElementById('cookie-modal').style.display = 'none';
                setCookie('cookie', 'accepted', 30);
            }
        }

        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
    </script>
<?php endif; ?>