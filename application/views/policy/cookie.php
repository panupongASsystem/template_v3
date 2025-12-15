<style>
    /* Cookie Policy Specific Styles */
    .cookie-header {
        background: linear-gradient(135deg, #FA8BFF 0%, #2BD2FF 52%, #2BFF88 90%);
        padding: 100px 0 120px;
        position: relative;
        overflow: hidden;
        margin-top: -70px;
    }

    .cookie-header::before {
        content: '🍪';
        position: absolute;
        font-size: 300px;
        opacity: 0.1;
        top: -50px;
        right: -50px;
        transform: rotate(-15deg);
    }

    .cookie-icon-box {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.3);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
        font-size: 36px;
        color: white;
    }

    .cookie-manager {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin: -50px auto 40px;
        position: relative;
        z-index: 10;
        max-width: 1000px;
    }

    .cookie-category {
        border: 2px solid var(--light);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .cookie-category:hover {
        border-color: #2BD2FF;
        box-shadow: 0 5px 20px rgba(43, 210, 255, 0.2);
    }

    .cookie-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }

    .cookie-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .cookie-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 30px;
    }

    .cookie-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

	.policy-subtitle {
            color: rgba(255,255,255,0.9);
            text-align: center;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .policy-meta {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .policy-meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.9);
            font-size: 0.95rem;
        }

        .policy-meta-item i {
            font-size: 1.1rem;
        }
	
    input:checked + .cookie-slider {
        background: linear-gradient(135deg, #FA8BFF 0%, #2BD2FF 100%);
    }

    input:disabled + .cookie-slider {
        opacity: 0.5;
        cursor: not-allowed;
    }

    input:checked + .cookie-slider:before {
        transform: translateX(30px);
    }
</style>

<div class="cookie-header">
    <div class="container text-center">
        <div class="cookie-icon-box" data-aos="zoom-in">
            <i class="fas fa-cookie-bite"></i>
        </div>
        <h1 class="text-white mb-3" data-aos="fade-up" data-aos-delay="100">
            นโยบายการใช้คุกกี้
        </h1>
        <p class="policy-subtitle" data-aos="fade-up" data-aos-delay="200">
                    <?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?>
                </p>
                <div class="policy-meta" data-aos="fade-up" data-aos-delay="300">
                    <div class="policy-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>ปรับปรุงล่าสุด: 1 มกราคม 2568</span>
                    </div>
                    <div class="policy-meta-item">
                        <i class="fas fa-tag"></i>
                        <span>เวอร์ชัน 2.0</span>
                    </div>
                    <div class="policy-meta-item">
                        <i class="fas fa-clock"></i>
                        <span>อ่าน 15 นาที</span>
                    </div>
                </div>
		
    </div>
</div>

<div class="container">
    <!-- Cookie Manager -->
    <div class="cookie-manager" data-aos="fade-up">
        <h3 class="text-center mb-4">จัดการคุกกี้ของคุณ</h3>
        <p class="text-center text-muted mb-4">เลือกประเภทคุกกี้ที่คุณต้องการอนุญาต</p>
        
        <?php if(isset($cookie_categories)): foreach($cookie_categories as $category): ?>
        <div class="cookie-category">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h5 class="mb-2">
                        <?php echo $category['name']; ?>
                        <?php if($category['required']): ?>
                        <span class="badge bg-success ms-2">จำเป็น</span>
                        <?php endif; ?>
                    </h5>
                    <p class="text-muted mb-0"><?php echo $category['description']; ?></p>
                </div>
                <label class="cookie-switch">
                    <input type="checkbox" 
                           id="cookie_<?php echo $category['category_id']; ?>"
                           <?php echo $category['required'] ? 'checked disabled' : ''; ?>>
                    <span class="cookie-slider"></span>
                </label>
            </div>
        </div>
        <?php endforeach; endif; ?>
        
        <div class="text-center mt-4">
            <button class="btn btn-outline-secondary me-2" onclick="rejectAllCookies()">
                ปฏิเสธทั้งหมด
            </button>
            <button class="btn btn-primary" onclick="acceptSelectedCookies()">
                บันทึกการตั้งค่า
            </button>
            <button class="btn btn-success" onclick="acceptAllCookies()">
                ยอมรับทั้งหมด
            </button>
        </div>
    </div>

    <!-- Cookie Information -->
    <div class="row mt-5">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-body p-5">
                    <h2 class="mb-4">คุกกี้คืออะไร?</h2>
                    <p>คุกกี้ (Cookies) คือไฟล์ข้อมูลขนาดเล็กที่เว็บไซต์ส่งไปเก็บไว้ในคอมพิวเตอร์หรืออุปกรณ์ของท่าน เพื่อจดจำข้อมูลการใช้งาน ทำให้การใช้งานเว็บไซต์สะดวกและมีประสิทธิภาพมากขึ้น</p>
                    
                    <h3 class="mt-4">ประเภทของคุกกี้ที่เราใช้</h3>
                    <div class="accordion mt-3" id="cookieAccordion">
                        <?php if(isset($cookie_categories)): foreach($cookie_categories as $index => $cat): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" 
                                        type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#cat<?php echo $index; ?>">
                                    <?php echo $cat['name']; ?>
                                </button>
                            </h2>
                            <div id="cat<?php echo $index; ?>" 
                                 class="accordion-collapse collapse <?php echo $index == 0 ? 'show' : ''; ?>" 
                                 data-bs-parent="#cookieAccordion">
                                <div class="accordion-body">
                                    <p><?php echo $cat['description']; ?></p>
                                    <?php if(!empty($cat['cookies'])): ?>
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>ชื่อคุกกี้</th>
                                                <th>วัตถุประสงค์</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($cat['cookies'] as $name => $desc): ?>
                                            <tr>
                                                <td><code><?php echo $name; ?></code></td>
                                                <td><?php echo $desc; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>

                    <h3 class="mt-5">การจัดการคุกกี้ในเบราว์เซอร์</h3>
                    <p>ท่านสามารถตั้งค่าเบราว์เซอร์เพื่อปฏิเสธคุกกี้ทั้งหมดหรือแจ้งเตือนเมื่อมีการส่งคุกกี้ได้:</p>
                    <ul>
                        <li><a href="https://support.google.com/chrome/answer/95647" target="_blank">Google Chrome</a></li>
                        <li><a href="https://support.mozilla.org/en-US/kb/cookies" target="_blank">Mozilla Firefox</a></li>
                        <li><a href="https://support.apple.com/guide/safari/manage-cookies-sfri11471/mac" target="_blank">Safari</a></li>
                        <li><a href="https://support.microsoft.com/en-us/microsoft-edge/manage-cookies-in-microsoft-edge" target="_blank">Microsoft Edge</a></li>
                    </ul>

                    <?php if(isset($faqs) && !empty($faqs)): ?>
                    <h3 class="mt-5">คำถามที่พบบ่อย</h3>
                    <div class="accordion mt-3" id="faqAccordion">
                        <?php foreach($faqs as $index => $faq): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#faq<?php echo $index; ?>">
                                    <?php echo $faq['question']; ?>
                                </button>
                            </h2>
                            <div id="faq<?php echo $index; ?>" class="accordion-collapse collapse" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <?php echo $faq['answer']; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function acceptAllCookies() {
    document.querySelectorAll('input[type="checkbox"]:not(:disabled)').forEach(cb => cb.checked = true);
    saveCookieSettings();
}

function rejectAllCookies() {
    document.querySelectorAll('input[type="checkbox"]:not(:disabled)').forEach(cb => cb.checked = false);
    saveCookieSettings();
}

function acceptSelectedCookies() {
    saveCookieSettings();
}

function saveCookieSettings() {
    const settings = {
        necessary: true,
        functional: document.getElementById('cookie_functional')?.checked || false,
        analytics: document.getElementById('cookie_analytics')?.checked || false,
        marketing: document.getElementById('cookie_marketing')?.checked || false
    };
    
    localStorage.setItem('cookieSettings', JSON.stringify(settings));
    localStorage.setItem('cookieConsent', 'accepted');
    
    // Save to server via AJAX
    fetch('<?php echo site_url("policy/save_cookie_consent"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            consent_type: 'partial',
            functional: settings.functional,
            analytics: settings.analytics,
            marketing: settings.marketing
        })
    });
    
    alert('บันทึกการตั้งค่าคุกกี้เรียบร้อยแล้ว');
}
</script>
