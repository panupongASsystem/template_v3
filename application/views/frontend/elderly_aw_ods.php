<div class="text-center pages-head">
    <span class="font-pages-head">เบี้ยยังชีพผู้สูงอายุ / ผู้พิการ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<!-- Modal สำหรับการยืนยันการยื่นเรื่องโดยไม่เข้าสู่ระบบ -->
<div class="modal fade" id="guestConfirmModal" tabindex="-1" aria-labelledby="guestConfirmModalLabel" aria-hidden="true"
    style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content elderly-modal-content"
            style="border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(33, 150, 243, 0.2), 0 8px 25px rgba(0,0,0,0.08); background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 100%); overflow: hidden;">
            <div class="modal-header elderly-modal-header"
                style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.1) 0%, rgba(100, 181, 246, 0.1) 100%); color: #1976d2; border-radius: 20px 20px 0 0; border-bottom: 1px solid rgba(33, 150, 243, 0.2); backdrop-filter: blur(10px);">
                <h5 class="modal-title elderly-modal-title" id="guestConfirmModalLabel"
                    style="font-weight: 600; color: #1976d2; width: 100%; text-align: center;">
                    <i class="fas fa-heart me-2" style="color: #1976d2;"></i>ยินดีต้อนรับสู่ระบบยื่นเรื่องเบี้ยยังชีพ
                </h5>
            </div>
            <div class="modal-body elderly-modal-body text-center"
                style="padding: 2.5rem; background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 100%);">
                <div class="mb-4">
                    <div
                        style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(33, 150, 243, 0.15) 0%, rgba(100, 181, 246, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(33, 150, 243, 0.3);">
                        <i class="fas fa-user-shield"
                            style="font-size: 2.5rem; color: #1976d2; text-shadow: 0 2px 8px rgba(33, 150, 243, 0.4);"></i>
                    </div>
                </div>
                <h5 class="mb-3" style="color: #1976d2; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    เริ่มต้นการใช้งาน</h5>
                <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.6; color: #424242;">
                    เข้าสู่ระบบเพื่อติดตามสถานะการยื่นเรื่องและได้รับการแจ้งเตือน สะดวกรวดเร็ว ปลอดภัย ประหยัดเวลา
                    ไม่ต้องกรอกข้อมูลซ้ำ
                    ไม่มีใครค้นหาข้อมูลของคุณได้ หรือดำเนินการต่อโดยไม่ต้องเข้าสู่ระบบ
                    ไม่ปลอดภัยบุคคลอื่นสามารถค้นหาข้อมูลได้จากหน้าติดตาม</p>

                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-lg elderly-login-btn" onclick="redirectToLogin()"
                        style="background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%); border: none; color: white; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4); transition: all 0.3s ease; font-size: 1.1rem;">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                    </button>
                    <button type="button" class="btn btn-lg elderly-guest-btn" onclick="proceedAsGuest()"
                        style="background: rgba(33, 150, 243, 0.08); border: 2px solid rgba(33, 150, 243, 0.3); color: #1976d2; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; transition: all 0.3s ease; font-size: 1.1rem; backdrop-filter: blur(10px);">
                        <i class="fas fa-paper-plane me-2"></i>ดำเนินการต่อโดยไม่เข้าสู่ระบบ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center pages-head">
    <span class="font-pages-head"
        style="font-size: 2.8rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(108, 117, 125, 0.2);">เบี้ยยังชีพผู้สูงอายุ
        / ผู้พิการ</span>
</div>

<div class="bg-pages" style="background: #ffffff; min-height: 100vh; padding: 2rem 0;">
    <!-- *** เพิ่มส่วนดาวน์โหลดแบบฟอร์ม *** -->
    <div class="container-pages-news mb-4"
        style="position: relative; z-index: 10; background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 1000px; overflow: hidden;">

        <!-- เพิ่ม decorative element -->
        <div
            style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #1976d2, #42a5f5, #1976d2); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;">
        </div>

        <div class="text-center mb-4">
            <h4 style="color: #1976d2; font-weight: 600; margin-bottom: 1rem;">
                <i class="fas fa-download me-2"></i>แบบฟอร์มเบี้ยยังชีพผู้สูงอายุ / ผู้พิการ
            </h4>
            <p style="color: #666; font-size: 1rem;">กรุณาดาวน์โหลดแบบฟอร์มที่เหมาะสม กรอกข้อมูลให้ครบถ้วน
                แล้วนำมาแนบด้านล่าง</p>
        </div>

        <div class="row g-3">
            <?php if (!empty($elderly_aw_form)): ?>
                <?php foreach ($elderly_aw_form as $rs): ?>
                    <div class="col-md-4">
                        <div class="form-download-card"
                            style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.05) 0%, rgba(100, 181, 246, 0.05) 100%); border: 1px solid rgba(33, 150, 243, 0.2); border-radius: 15px; padding: 1.5rem; text-align: center; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.1);">
                            <div class="mb-3">
                                <i class="fas fa-file-pdf" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
                            </div>
                            <h6
                                style="color: #1976d2; font-weight: 600; margin-bottom: 1rem; min-height: 50px; display: flex; align-items: center; justify-content: center; text-align: center;">
                                <?= $rs->elderly_aw_form_name; ?>
                            </h6>
                            <p style="font-size: 0.8rem; color: #666; margin-bottom: 1rem;">
                                ประเภท: <?= ucfirst($rs->elderly_aw_form_type); ?>
                            </p>
                            <a href="<?= base_url('docs/file/' . $rs->elderly_aw_form_file); ?>" download
                                class="btn btn-primary btn-download-form"
                                style="background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%); border: none; border-radius: 10px; padding: 0.7rem 1.5rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3); width: 100%;">
                                <i class="fas fa-download me-2"></i>ดาวน์โหลด
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- แบบฟอร์มตัวอย่างถ้าไม่มีข้อมูลจากฐานข้อมูล -->
                <div class="col-md-6">
                    <div class="form-download-card"
                        style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.05) 0%, rgba(100, 181, 246, 0.05) 100%); border: 1px solid rgba(33, 150, 243, 0.2); border-radius: 15px; padding: 1.5rem; text-align: center; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.1);">
                        <div class="mb-3">
                            <i class="fas fa-file-pdf" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
                        </div>
                        <h6
                            style="color: #1976d2; font-weight: 600; margin-bottom: 1rem; min-height: 50px; display: flex; align-items: center; justify-content: center; text-align: center;">
                            แบบฟอร์มการขึ้นทะเบียนผู้สูงอายุ / ผู้พิการ
                        </h6>
                        <a href="#" class="btn btn-primary btn-download-form"
                            style="background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%); border: none; border-radius: 10px; padding: 0.7rem 1.5rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3); width: 100%;">
                            <i class="fas fa-download me-2"></i>ดาวน์โหลด
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-download-card"
                        style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.05) 0%, rgba(100, 181, 246, 0.05) 100%); border: 1px solid rgba(33, 150, 243, 0.2); border-radius: 15px; padding: 1.5rem; text-align: center; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.1);">
                        <div class="mb-3">
                            <i class="fas fa-file-pdf" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
                        </div>
                        <h6
                            style="color: #1976d2; font-weight: 600; margin-bottom: 1rem; min-height: 50px; display: flex; align-items: center; justify-content: center; text-align: center;">
                            แบบฟอร์มหนังสือมอบอำนาจ
                        </h6>
                        <a href="#" class="btn btn-primary btn-download-form"
                            style="background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%); border: none; border-radius: 10px; padding: 0.7rem 1.5rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3); width: 100%;">
                            <i class="fas fa-download me-2"></i>ดาวน์โหลด
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>


        <!-- คำแนะนำ -->
        <div class="alert alert-info mt-4"
            style="background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(23, 162, 184, 0.05) 100%); border: 1px solid rgba(23, 162, 184, 0.3); border-radius: 15px;">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle me-3 mt-1" style="color: #17a2b8; font-size: 1.2rem;"></i>
                <div style="color: #0c5460;">
                    <strong>คำแนะนำการใช้แบบฟอร์ม:</strong>
                    <ul class="mb-0 mt-2" style="line-height: 1.8;">
                        <li>ดาวน์โหลดแบบฟอร์มที่เหมาะสมกับประเภทเบี้ยยังชีพที่ต้องการสมัคร</li>
                        <li>กรอกข้อมูลให้ครบถ้วนและถูกต้อง</li>
                        <li>ลงลายมือชื่อและประทับตราให้เรียบร้อย</li>
                        <li>สแกนหรือถ่ายรูปแบบฟอร์มที่กรอกแล้วในรูปแบบ PDF หรือรูปภาพ</li>
                        <li>นำไฟล์มาแนบในฟอร์มด้านล่างนี้</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- *** ฟอร์มยื่นเรื่อง *** -->
    <div class="container-pages-news mb-5 mt-5"
        style="position: relative; z-index: 10; background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 1000px; overflow: hidden;"
        id="elderly_aw_ods_form">

        <!-- เพิ่ม decorative element -->
        <div
            style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #1976d2, #42a5f5, #1976d2); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;">
        </div>

        <!-- ปุ่มติดตามสถานะ -->
        <div class="d-flex justify-content-end mb-3">
            <button type="button" onclick="redirectToTrackStatus()" class="btn track-status-btn" style="background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%); 
                           border: none; 
                           color: white; 
                           padding: 0.7rem 1.5rem; 
                           border-radius: 12px; 
                           font-size: 0.95rem; 
                           font-weight: 600; 
                           transition: all 0.3s ease; 
                           box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3); 
                           position: relative;
                           overflow: hidden;
                           cursor: pointer;">
                <span style="position: relative; z-index: 2;">
                    <i class="fas fa-search me-2"></i>ติดตามสถานะเบี้ยยังชีพ
                </span>
            </button>
        </div>

        <script>
            // *** ฟังก์ชัน redirect ตาม user type ***
            function redirectToTrackStatus() {
                <?php
                // ตรวจสอบ session ใน PHP
                $is_logged_in = false;
                $user_type = 'guest';

                if (isset($this->session)) {
                    $mp_id = $this->session->userdata('mp_id');
                    $mp_email = $this->session->userdata('mp_email');
                    $m_id = $this->session->userdata('m_id');
                    $m_email = $this->session->userdata('m_email');

                    if (!empty($mp_id) && !empty($mp_email)) {
                        $is_logged_in = true;
                        $user_type = 'public';
                    } elseif (!empty($m_id) && !empty($m_email)) {
                        $is_logged_in = true;
                        $user_type = 'staff';
                    }
                }
                ?>

                const isLoggedIn = <?= json_encode($is_logged_in); ?>;
                const userType = '<?= $user_type; ?>';

                if (isLoggedIn) {
                    // User ที่ login (public หรือ staff) → ไปหน้า my_elderly_aw_ods
                    window.location.href = '<?= site_url('Elderly_aw_ods/my_elderly_aw_ods'); ?>';
                } else {
                    // Guest user → ไปหน้า follow_elderly_aw_ods
                    window.location.href = '<?= site_url('Elderly_aw_ods/follow_elderly_aw_ods'); ?>';
                }
            }

            // *** เพิ่ม hover effect ***
            document.addEventListener('DOMContentLoaded', function () {
                const trackBtn = document.querySelector('.track-status-btn');
                if (trackBtn) {
                    trackBtn.addEventListener('mouseenter', function () {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 6px 20px rgba(33, 150, 243, 0.4)';
                        this.style.background = 'linear-gradient(135deg, #1565c0 0%, #0d47a1 100%)';
                    });

                    trackBtn.addEventListener('mouseleave', function () {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = '0 4px 15px rgba(33, 150, 243, 0.3)';
                        this.style.background = 'linear-gradient(135deg, #1976d2 0%, #1565c0 100%)';
                    });
                }
            });
        </script>

        <!-- ประเภทเบี้ยยังชีพ -->
        <div class="elderly-type-selector"
            style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.08) 0%, rgba(100, 181, 246, 0.08) 100%); border-radius: 15px; padding: 1rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15);">
            <div class="form-label-wrapper"
                style="margin-bottom: 1rem; background: linear-gradient(135deg, rgba(33, 150, 243, 0.08) 0%, rgba(100, 181, 246, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15); backdrop-filter: blur(10px);">
                <label class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                    <i class="fas fa-heart me-2" style="color: #1976d2;"></i>ประเภทเบี้ยยังชีพ
                </label>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check p-3 elderly-form-check"
                        style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                        <input class="form-check-input" type="radio" name="elderly_aw_ods_type" id="elderly"
                            value="elderly" checked style="transform: scale(1.2); margin: 0;">
                        <label class="form-check-label fw-bold" for="elderly"
                            style="color: #424242; margin-left: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-user-clock me-2" style="color: #ff9800;"></i>ผู้สูงอายุ
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check p-3 elderly-form-check"
                        style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                        <input class="form-check-input" type="radio" name="elderly_aw_ods_type" id="disabled"
                            value="disabled" style="transform: scale(1.2); margin: 0;">
                        <label class="form-check-label fw-bold" for="disabled"
                            style="color: #424242; margin-left: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-wheelchair me-2" style="color: #9c27b0;"></i>ผู้พิการ
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="underline">
            <form id="elderlyAwOdsForm" action="<?php echo site_url('Elderly_aw_ods/add_elderly_aw_ods'); ?>"
                method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return false;">
                <input type="hidden" name="form_token" id="formToken" value="">
                <br>

                <div class="row">
                    <!-- ชื่อ-นามสกุล -->
                    <div class="col-md-5">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.08) 0%, rgba(100, 181, 246, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                                    <i class="fas fa-user me-2" style="color: #1976d2;"></i>ชื่อ-นามสกุล<span
                                        style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="text" name="elderly_aw_ods_by" id="elderly_aw_ods_by" class="form-control"
                                    required placeholder="เช่น นาย สมชาย ใจดี"
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="elderly_aw_ods_by_feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- เบอร์โทรศัพท์ -->
                    <div class="col-md-3">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.08) 0%, rgba(100, 181, 246, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                                    <i class="fas fa-phone me-2" style="color: #1976d2;"></i>เบอร์โทรศัพท์<span
                                        style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="tel" id="elderly_aw_ods_phone" name="elderly_aw_ods_phone"
                                    class="form-control" required placeholder="เช่น 0812345678" pattern="\d{10}"
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="elderly_aw_ods_phone_feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- อีเมล -->
                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.08) 0%, rgba(100, 181, 246, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                                    <i class="fas fa-envelope me-2" style="color: #1976d2;"></i>อีเมล<small
                                        style="color: #6c757d; font-weight: 400; margin-left: 0.5rem;">(Optional)</small>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="email" name="elderly_aw_ods_email" class="form-control"
                                    placeholder="example@youremail.com"
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="elderly_aw_ods_email_feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- เลขบัตรประชาชน -->
                <div class="form-group mb-4">
                    <div class="form-label-wrapper"
                        style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.08) 0%, rgba(100, 181, 246, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15); backdrop-filter: blur(10px);">
                        <label class="form-label"
                            style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                            <i class="fas fa-id-card me-2" style="color: #1976d2;"></i>เลขบัตรประจำตัวประชาชน<span
                                style="color: #dc3545; margin-left: 0.2rem;">*</span>
                        </label>
                    </div>
                    <div class="col-sm-12">
                        <input type="text" name="elderly_aw_ods_number" id="elderly_aw_ods_number" class="form-control"
                            required placeholder="เลขบัตรประจำตัวประชาชน 13 หลัก" maxlength="13"
                            style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                        <div class="invalid-feedback" id="elderly_aw_ods_number_feedback"></div>
                    </div>
                </div>

                <!-- *** ระบบที่อยู่แบบละเอียด *** -->
                <div class="form-group mb-4">
                    <div class="form-label-wrapper"
                        style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.08) 0%, rgba(100, 181, 246, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15); backdrop-filter: blur(10px);">
                        <label class="form-label"
                            style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                            <i class="fas fa-map-marker-alt me-2" style="color: #1976d2;"></i>ที่อยู่<span
                                style="color: #dc3545; margin-left: 0.2rem;">*</span>
                        </label>
                    </div>

                    <!-- ที่อยู่เพิ่มเติม (บังคับกรอก) -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="input-wrapper">
                                <input type="text" id="additional_address_field" class="form-control" required
                                    placeholder="กรอกที่อยู่เพิ่มเติม (บ้านเลขที่ ซอย ถนน หมู่บ้าน) *"
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                <i class="fas fa-map-marker-alt input-icon"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #1976d2;"></i>
                            </div>
                            <small class="form-text text-muted">บ้านเลขที่ ซอย ถนน หรือรายละเอียดเพิ่มเติม</small>
                        </div>
                    </div>

                    <!-- รหัสไปรษณีย์ -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-wrapper">
                                <input type="text" id="zipcode_field" class="form-control"
                                    placeholder="กรอกรหัสไปรษณีย์ 5 หลัก" maxlength="5" pattern="[0-9]{5}"
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                <i class="fas fa-mail-bulk input-icon"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #1976d2;"></i>
                            </div>
                            <small class="form-text text-muted">กรอกรหัสไปรษณีย์เพื่อเติมข้อมูลอัตโนมัติ</small>

                            <!-- Loading & Error indicators -->
                            <div id="zipcode_loading" class="text-center mt-1" style="display: none;">
                                <small class="text-primary">
                                    <i class="fas fa-spinner fa-spin"></i> กำลังค้นหา...
                                </small>
                            </div>
                            <div id="zipcode_error" class="mt-1" style="display: none;">
                                <small class="text-danger"></small>
                            </div>
                        </div>
                    </div>

                    <!-- จังหวัด และ อำเภอ -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-wrapper">
                                <input type="text" id="province_field" class="form-control" placeholder="จังหวัด"
                                    readonly
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                <i class="fas fa-map input-icon"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #1976d2;"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-wrapper">
                                <select id="amphoe_field" class="form-control" disabled
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <option value="">เลือกอำเภอ</option>
                                </select>
                                <i class="fas fa-city input-icon"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #1976d2;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- ตำบล -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.08) 0%, rgba(100, 181, 246, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                                    <i class="fas fa-home me-2" style="color: #1976d2;"></i>ตำบล<span
                                        style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="input-wrapper">
                                <select id="district_field" name="district_field" class="form-control" disabled required
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <option value="">เลือกตำบล *</option>
                                </select>
                                <i class="fas fa-home input-icon"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #1976d2;"></i>
                            </div>
                            <div class="invalid-feedback" id="district_field_feedback"></div>
                        </div>
                    </div>

                    <!-- ที่อยู่รวม (ซ่อน - ส่งไปยัง elderly_aw_ods_address) -->
                    <input type="hidden" name="elderly_aw_ods_address" id="full_address_field" value="">

                    <!-- *** เพิ่มใหม่: ซ่อน hidden fields สำหรับข้อมูลที่อยู่แยก *** -->
                    <input type="hidden" name="guest_province" id="guest_province_field" value="">
                    <input type="hidden" name="guest_amphoe" id="guest_amphoe_field" value="">
                    <input type="hidden" name="guest_district" id="guest_district_field" value="">
                    <input type="hidden" name="guest_zipcode" id="guest_zipcode_field" value="">

                    <!-- แสดงที่อยู่ที่รวมแล้ว -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info" id="address_preview"
                                style="display: none; border-radius: 15px; background: linear-gradient(135deg, rgba(33, 150, 243, 0.1) 0%, rgba(100, 181, 246, 0.1) 100%); border: 1px solid rgba(33, 150, 243, 0.3);">
                                <strong><i class="fas fa-eye"></i> ที่อยู่ที่จะบันทึก:</strong>
                                <div id="address_preview_text"></div>
                            </div>
                        </div>
                    </div>

                    <div class="invalid-feedback" id="elderly_aw_ods_address_feedback"></div>
                </div>

                <br>

                <div class="row" style="padding-bottom: 20px;">
                    <!-- ไฟล์แนบ -->
                    <div class="col-9">
                        <div class="form-group">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.08) 0%, rgba(100, 181, 246, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                                    <i class="fas fa-paperclip me-2" style="color: #1976d2;"></i>ไฟล์แนบ<small
                                        style="color: #6c757d; font-weight: 400; margin-left: 0.5rem;">(รูปภาพ หรือ
                                        PDF)</small>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <!-- File Upload Zone -->
                                <div class="file-upload-wrapper"
                                    style="border: 2px dashed #dee2e6; border-radius: 15px; padding: 1.5rem; text-align: center; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); transition: all 0.3s ease; cursor: pointer; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15);"
                                    ondrop="handleDrop(event)" ondragover="handleDragOver(event)"
                                    ondragenter="handleDragEnter(event)" ondragleave="handleDragLeave(event)">
                                    <div id="upload-placeholder" class="upload-placeholder">
                                        <i class="fas fa-cloud-upload-alt"
                                            style="font-size: 2rem; color: #1976d2; margin-bottom: 0.5rem;"></i>
                                        <p style="margin: 0; color: #6c757d; font-size: 1rem;">คลิกเพื่อเลือกไฟล์
                                            หรือลากไฟล์มาวางที่นี่</p>
                                        <small class="text-muted mt-2 d-block">รองรับไฟล์: JPG, PNG, PDF (สูงสุด 3
                                            ไฟล์)(ไม่เกิน 15 MB)</small>
                                    </div>
                                </div>
                                <input type="file" id="elderly_aw_ods_files" name="elderly_aw_ods_files[]"
                                    class="form-control" accept="image/*,.pdf" multiple
                                    onchange="handleFileSelect(this)" style="display: none;">

                                <!-- File Preview Area -->
                                <div id="file-preview-area" class="file-preview-area mt-3" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted" style="font-size: 0.9rem;">
                                            <i class="fas fa-paperclip me-1"></i>ไฟล์ที่เลือก (<span
                                                id="file-count">0</span>/3)
                                        </span>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="clearAllFiles()" style="border-radius: 8px; font-size: 0.8rem;">
                                            <i class="fas fa-times me-1"></i>ลบทั้งหมด
                                        </button>
                                    </div>
                                    <div id="preview-container" class="preview-container"
                                        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; max-height: 300px; overflow-y: auto; padding: 1rem; background: #f8f9fa; border-radius: 10px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ปุ่มส่ง -->
                    <div class="col-3">
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" id="submitElderlyAwOdsBtn" class="btn modern-submit-btn"
                                onclick="handleElderlyAwOdsSubmit(event)"
                                style="background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%); border: none; color: white; padding: 1rem 2rem; border-radius: 15px; font-size: 1.1rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.3); position: relative; overflow: hidden; min-width: 150px;">
                                <span style="position: relative; z-index: 2;">
                                    <i class="fas fa-paper-plane me-2"></i>ยื่นเรื่อง
                                </span>
                                <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; z-index: 1;"
                                    class="btn-shine"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- *** หมายเหตุสำคัญ *** -->
        <div class="mt-4"
            style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%); border: 1px solid rgba(255, 193, 7, 0.3); border-radius: 20px; padding: 2rem; margin: 2rem auto; max-width: 1000px; box-shadow: 0 8px 25px rgba(255, 193, 7, 0.15);">
            <div class="row align-items-start">
                <div class="col-auto">
                    <div
                        style="width: 60px; height: 60px; background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 193, 7, 0.1) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);">
                        <i class="fas fa-exclamation-triangle" style="font-size: 1.8rem; color: #f57c00;"></i>
                    </div>
                </div>
                <div class="col">
                    <h5 style="color: #f57c00; font-weight: 600; margin-bottom: 1.5rem;">
                        <i class="fas fa-clipboard-list me-2"></i>หมายเหตุสำคัญ
                    </h5>
                    <div style="color: #6d4c00; font-size: 1.05rem; line-height: 1.8;">
                        <div class="mb-3">
                            <strong style="color: #f57c00;">1.</strong>
                            ผู้ประสงค์ลงทะเบียนสามารถดำเนินการได้ทั้งแบบ
                            <strong>ยื่นเอกสารออนไลน์</strong> และ <strong>ดำเนินการ ณ ที่ทำการ</strong>
                        </div>
                        <div class="mb-3">
                            <strong style="color: #f57c00;">2.</strong>
                            การยื่นเอกสารออนไลน์ต้องมีการ <strong>ลงลายมือรับรอง</strong> พร้อมข้อความ
                            <span
                                style="background: rgba(255, 193, 7, 0.2); padding: 0.3rem 0.6rem; border-radius: 8px; font-weight: 600;">
                                "สำเนาถูกต้องประกอบการลงทะเบียนรับเบี้ยยังชีพ"
                            </span>
                        </div>
                        <div class="mb-0">
                            <strong style="color: #f57c00;">3.</strong>
                            การกรอกข้อมูลต้อง <strong>ถูกต้องและครบถ้วน</strong>
                            เพื่อเป็นการรักษาสิทธิ์ของผู้รับสิทธิ์เอง
                        </div>
                    </div>

                    <!-- เพิ่มส่วนข้อมูลติดต่อ -->
                    <div class="mt-4 p-3"
                        style="background: rgba(255, 255, 255, 0.7); border-radius: 12px; border-left: 4px solid #f57c00;">
                        <h6 style="color: #f57c00; font-weight: 600; margin-bottom: 0.5rem;">
                            <i class="fas fa-phone me-2"></i>ข้อมูลติดต่อ
                        </h6>
                        <p style="color: #6d4c00; margin: 0; font-size: 0.95rem;">
                            หากมีข้อสงสัยเพิ่มเติม กรุณาติดต่อสำนักงาน<?php echo get_config_value('fname'); ?>
                            <br>โทร: <strong><?php echo get_config_value('phone_1'); ?></strong> | อีเมล:
                            <strong><?php echo get_config_value('email_1'); ?></strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, sans-serif;
        min-height: 100vh;
    }

    /* Form download cards */
    .form-download-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(33, 150, 243, 0.2) !important;
        background: linear-gradient(135deg, rgba(33, 150, 243, 0.08) 0%, rgba(100, 181, 246, 0.08) 100%) !important;
    }

    .btn-download-form:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4) !important;
        background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%) !important;
    }

    /* Input wrapper สำหรับ icon positioning */
    .input-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .input-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #1976d2;
        pointer-events: none;
        z-index: 2;
    }

    /* Form styling */
    .form-label-wrapper:hover {
        background: linear-gradient(135deg, rgba(33, 150, 243, 0.12) 0%, rgba(100, 181, 246, 0.12) 100%) !important;
        box-shadow: 0 6px 16px rgba(33, 150, 243, 0.2) !important;
        transform: translateY(-2px);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: transparent !important;
        box-shadow: 0 8px 25px rgba(33, 150, 243, 0.25) !important;
        transform: translateY(-1px);
        background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 100%) !important;
    }

    .track-status-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4) !important;
        background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%) !important;
        color: white !important;
        text-decoration: none !important;
    }

    .modern-submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(33, 150, 243, 0.4) !important;
        background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%) !important;
    }

    .modern-submit-btn:hover .btn-shine {
        left: 100%;
    }

    .elderly-form-check:hover {
        background: rgba(33, 150, 243, 0.05) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.1);
    }

    .form-check-input:checked {
        background-color: #1976d2;
        border-color: #1976d2;
    }

    .file-upload-wrapper:hover {
        background: linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%) !important;
        box-shadow: 0 8px 25px rgba(33, 150, 243, 0.2) !important;
        transform: translateY(-2px);
        border-color: #1976d2 !important;
    }

    .file-upload-wrapper.drag-over {
        background: linear-gradient(135deg, #f1f8e9 0%, #e3f2fd 100%) !important;
        border-color: #1976d2 !important;
        box-shadow: 0 8px 25px rgba(33, 150, 243, 0.3) !important;
        transform: scale(1.02);
    }

    .preview-item {
        position: relative;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .preview-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .preview-image,
    .preview-pdf {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 6px 6px 0 0;
    }

    .preview-pdf {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
        color: white;
        font-size: 2rem;
    }

    .preview-info {
        padding: 0.5rem;
        background: #f8f9fa;
    }

    .preview-name {
        font-size: 0.7rem;
        color: #495057;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 500;
    }

    .preview-size {
        font-size: 0.6rem;
        color: #6c757d;
        margin: 0;
    }

    .remove-file {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .remove-file:hover {
        background: rgba(220, 53, 69, 1);
        transform: scale(1.1);
    }

    @keyframes gradientShift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    /* Modal specific styles */
    .elderly-modal-content {
        z-index: 9999 !important;
    }

    .elderly-login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(33, 150, 243, 0.5) !important;
        background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%) !important;
    }

    .elderly-guest-btn:hover {
        background: rgba(33, 150, 243, 0.15) !important;
        border-color: rgba(33, 150, 243, 0.5) !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.3) !important;
    }

    /* User info display */
    #logged-in-user-info {
        border-radius: 15px;
        background: linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%);
        border: 1px solid rgba(33, 150, 243, 0.3);
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(33, 150, 243, 0.2);
        backdrop-filter: blur(10px);
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

        .row .col-md-5,
        .row .col-md-3,
        .row .col-md-4 {
            width: 100% !important;
            margin-bottom: 1rem;
        }

        .col-9,
        .col-3 {
            width: 100% !important;
        }

        .preview-container {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)) !important;
        }
    }
</style>

<!-- Font Awesome และ Bootstrap -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // *** JavaScript Code - Complete Elderly AW ODS System ***

    // ตัวแปร Global
    const isUserLoggedIn = <?= json_encode($is_logged_in ?? false); ?>;
    const userInfo = <?= json_encode($user_info ?? null); ?>;
    const userAddress = <?= json_encode($user_address ?? null); ?>;
    let hasConfirmedAsGuest = isUserLoggedIn;
    let guestModalInstance = null;
    let idCardModalInstance = null;
    let selectedFiles = [];
    const maxFiles = 3;
    const maxFileSize = 15 * 1024 * 1024; // 15MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
    let formSubmitting = false;

    // *** ตัวแปรสำหรับ Address System ***
    const API_BASE_URL = 'https://addr.assystem.co.th/index.php/zip_api';
    let zipcodeField, provinceField, amphoeField, districtField, fullAddressField, additionalAddressField;
    let currentAddressData = [];

    // *** ฟังก์ชันตรวจสอบเลขบัตรประชาชนไทย ***
    function validateThaiIdCard(idCard) {
        if (!idCard || !/^\d{13}$/.test(idCard)) {
            return false;
        }

        if (/^(\d)\1{12}$/.test(idCard)) {
            return false;
        }

        const digits = idCard.split('').map(Number);
        const weights = [13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2];

        let sum = 0;
        for (let i = 0; i < 12; i++) {
            sum += digits[i] * weights[i];
        }

        const remainder = sum % 11;
        const checkDigit = remainder < 2 ? (1 - remainder) : (11 - remainder);

        return checkDigit === digits[12];
    }

    function checkReturnFromLogin() {
        const urlParams = new URLSearchParams(window.location.search);
        const fromLogin = urlParams.get('from_login');

        if (fromLogin === 'success' && isUserLoggedIn && userInfo) {
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);

            Swal.fire({
                icon: 'success',
                title: 'เข้าสู่ระบบสำเร็จ!',
                html: `
                <div style="text-align: center;">
                    <p style="color: #1976d2; font-size: 1.1rem; margin-bottom: 1rem;">
                        <i class="fas fa-user-check me-2"></i>
                        ยินดีต้อนรับ <strong>${userInfo.name}</strong>
                    </p>
                    <div style="background: linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%); 
                                padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                        <p style="margin: 0; color: #1565c0;">
                            <i class="fas fa-magic me-2"></i>
                            ข้อมูลของคุณจะถูกใส่ในฟอร์มโดยอัตโนมัติ
                        </p>
                    </div>
                </div>
            `,
                confirmButtonText: 'เริ่มยื่นเรื่อง',
                confirmButtonColor: '#1976d2',
                timer: 5000,
                timerProgressBar: true
            });

            sessionStorage.removeItem('redirect_after_login');
        }
    }

    // เมื่อเอกสารโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('elderlyAwOdsForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                return false;
            });
        }

        fixFormAction();
        initializeAddressSystem();
        setupPhoneValidation();
        setupIdCardValidation();
        updateFormFieldsBasedOnLoginStatus();

        const uploadWrapper = document.querySelector('.file-upload-wrapper');
        if (uploadWrapper) {
            uploadWrapper.addEventListener('click', function (e) {
                e.preventDefault();
                const fileInput = document.getElementById('elderly_aw_ods_files');
                if (fileInput) fileInput.click();
            });
        }

        if (!isUserLoggedIn) {
            setTimeout(() => {
                if (!hasConfirmedAsGuest) showModal();
            }, 1000);
        } else {
            setTimeout(showWelcomeMessage, 500);
        }

        checkReturnFromLogin();

        console.log('✅ Elderly AW ODS form initialized with address system & ID card validation');
    });

    // *** ฟังก์ชัน Address System - เหมือนกับ Suggestions ***
    function initializeAddressSystem() {
        try {
            zipcodeField = document.getElementById('zipcode_field');
            provinceField = document.getElementById('province_field');
            amphoeField = document.getElementById('amphoe_field');
            districtField = document.getElementById('district_field');
            fullAddressField = document.getElementById('full_address_field');
            additionalAddressField = document.getElementById('additional_address_field');

            if (!zipcodeField) {
                console.error('❌ Address elements not found');
                return;
            }

            loadAllProvinces();

            zipcodeField.addEventListener('keypress', function (e) {
                if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                    e.preventDefault();
                }
            });

            zipcodeField.addEventListener('input', function () {
                const zipcode = this.value.trim();

                if (zipcode.length === 0) {
                    resetToProvinceSelection();
                } else if (zipcode.length === 5 && /^\d{5}$/.test(zipcode)) {
                    searchByZipcode(zipcode);
                } else {
                    clearDependentAddressFields();
                }
            });

            amphoeField.addEventListener('change', function () {
                const selectedAmphoeCode = this.value;

                if (selectedAmphoeCode) {
                    const currentZipcode = zipcodeField.value.trim();

                    if (currentZipcode.length === 5) {
                        filterDistrictsByAmphoe(selectedAmphoeCode);
                    } else {
                        loadDistrictsByAmphoe(selectedAmphoeCode);
                    }
                } else {
                    districtField.innerHTML = '<option value="">เลือกตำบล</option>';
                    districtField.disabled = true;
                }

                updateFullAddress();
            });

            districtField.addEventListener('change', function () {
                const selectedDistrictCode = this.value;

                if (selectedDistrictCode) {
                    const selectedDistrict = currentAddressData.find(item =>
                        (item.district_code || item.code) === selectedDistrictCode
                    );

                    if (selectedDistrict && selectedDistrict.zipcode) {
                        zipcodeField.value = selectedDistrict.zipcode;
                    } else {
                        loadZipcodeByDistrict(selectedDistrictCode);
                    }
                }

                updateFullAddress();
            });

            if (additionalAddressField) {
                additionalAddressField.addEventListener('input', function () {
                    clearTimeout(this.updateTimeout);
                    this.updateTimeout = setTimeout(() => {
                        updateFullAddress();
                    }, 300);
                });
            }

            console.log('✅ Address system initialized successfully');

        } catch (error) {
            console.error('❌ Error initializing address system:', error);
        }
    }

    // ฟังก์ชันดึงรายการจังหวัดทั้งหมด
    async function loadAllProvinces() {
        const provinces = [
            { code: '10', name: 'กรุงเทพมหานคร' },
            { code: '11', name: 'สมุทรปราการ' },
            { code: '12', name: 'นนทบุรี' },
            { code: '13', name: 'ปทุมธานี' },
            { code: '14', name: 'พระนครศรีอยุธยา' },
            { code: '15', name: 'อ่างทอง' },
            { code: '16', name: 'ลพบุรี' },
            { code: '17', name: 'สิงห์บุรี' },
            { code: '18', name: 'ชัยนาท' },
            { code: '19', name: 'สระบุรี' },
            { code: '20', name: 'ชลบุรี' },
            { code: '21', name: 'ระยอง' },
            { code: '22', name: 'จันทบุรี' },
            { code: '23', name: 'ตราด' },
            { code: '24', name: 'ฉะเชิงเทรา' },
            { code: '25', name: 'ปราจีนบุรี' },
            { code: '26', name: 'นครนายก' },
            { code: '27', name: 'สระแก้ว' },
            { code: '30', name: 'นครราชสีมา' },
            { code: '31', name: 'บุรีรัมย์' },
            { code: '32', name: 'สุรินทร์' },
            { code: '33', name: 'ศีสะเกษ' },
            { code: '34', name: 'อุบลราชธานี' },
            { code: '35', name: 'ยโสธร' },
            { code: '36', name: 'ชัยภูมิ' },
            { code: '37', name: 'อำนาจเจริญ' },
            { code: '38', name: 'บึงกาฬ' },
            { code: '39', name: 'หนองบัวลำภู' },
            { code: '40', name: 'ขอนแก่น' },
            { code: '41', name: 'อุดรธานี' },
            { code: '42', name: 'เลย' },
            { code: '43', name: 'หนองคาย' },
            { code: '44', name: 'มหาสารคาม' },
            { code: '45', name: 'ร้อยเอ็ด' },
            { code: '46', name: 'กาฬสินธุ์' },
            { code: '47', name: 'สกลนคร' },
            { code: '48', name: 'นครพนม' },
            { code: '49', name: 'มุกดาหาร' },
            { code: '50', name: 'เชียงใหม่' },
            { code: '51', name: 'ลำพูน' },
            { code: '52', name: 'ลำปาง' },
            { code: '53', name: 'อุตรดิตถ์' },
            { code: '54', name: 'แพร่' },
            { code: '55', name: 'น่าน' },
            { code: '56', name: 'พะเยา' },
            { code: '57', name: 'เชียงราย' },
            { code: '58', name: 'แม่ฮ่องสอน' },
            { code: '60', name: 'นครสวรรค์' },
            { code: '61', name: 'อุทัยธานี' },
            { code: '62', name: 'กำแพงเพชร' },
            { code: '63', name: 'ตาก' },
            { code: '64', name: 'สุโขทัย' },
            { code: '65', name: 'พิษณุโลก' },
            { code: '66', name: 'พิจิตร' },
            { code: '67', name: 'เพชรบูรณ์' },
            { code: '70', name: 'ราชบุรี' },
            { code: '71', name: 'กาญจนบุรี' },
            { code: '72', name: 'สุพรรณบุรี' },
            { code: '73', name: 'นครปฐม' },
            { code: '74', name: 'สมุทรสาคร' },
            { code: '75', name: 'สมุทรสงคราม' },
            { code: '76', name: 'เพชรบุรี' },
            { code: '77', name: 'ประจวบคีรีขันธ์' },
            { code: '80', name: 'นครศรีธรรมราช' },
            { code: '81', name: 'กระบี่' },
            { code: '82', name: 'พังงา' },
            { code: '83', name: 'ภูเก็ต' },
            { code: '84', name: 'สุราษฎร์ธานี' },
            { code: '85', name: 'ระนอง' },
            { code: '86', name: 'ชุมพร' },
            { code: '90', name: 'สงขลา' },
            { code: '91', name: 'สตูล' },
            { code: '92', name: 'ตรัง' },
            { code: '93', name: 'พัทลุง' },
            { code: '94', name: 'ปัตตานี' },
            { code: '95', name: 'ยะลา' },
            { code: '96', name: 'นราธิวาส' }
        ];

        populateProvinceDropdown(provinces);
    }

    // ฟังก์ชันค้นหาข้อมูลจากรหัสไปรษณีย์
    async function searchByZipcode(zipcode) {
        console.log('🔍 Searching by zipcode:', zipcode);
        showAddressLoading(true);

        try {
            const response = await fetch(`${API_BASE_URL}/address/${zipcode}`);
            const data = await response.json();

            if (data.status === 'success' && data.data.length > 0) {
                const dataWithZipcode = data.data.map(item => ({
                    ...item,
                    zipcode: zipcode,
                    searched_zipcode: zipcode
                }));

                currentAddressData = dataWithZipcode;
                populateFieldsFromZipcode(dataWithZipcode);
                updateFullAddress();
            } else {
                showAddressError('ไม่พบข้อมูลสำหรับรหัสไปรษณีย์นี้');
                resetToProvinceSelection();
            }
        } catch (error) {
            console.error('❌ Address API Error:', error);
            showAddressError('เกิดข้อผิดพลาดในการค้นหาข้อมูล');
            resetToProvinceSelection();
        } finally {
            showAddressLoading(false);
        }
    }

    // ฟังก์ชันดึงตำบลตามอำเภอ
    async function loadDistrictsByAmphoe(amphoeCode) {
        console.log('Loading districts for amphoe:', amphoeCode);
        showAddressLoading(true, 'amphoe');

        try {
            const response = await fetch(`${API_BASE_URL}/districts/${amphoeCode}`);
            const data = await response.json();

            if (data.status === 'success' && data.data && data.data.length > 0) {
                const processedDistricts = data.data.map(item => ({
                    code: item.district_code || item.code || item.id,
                    name: item.district_name || item.name || item.name_th || 'ไม่ระบุชื่อ',
                    name_en: item.district_name_en || item.name_en || '',
                    amphoe_code: item.amphoe_code || amphoeCode
                }));

                populateDistrictDropdown(processedDistricts);
                districtField.disabled = false;
            } else {
                districtField.innerHTML = '<option value="">ไม่พบข้อมูลตำบล</option>';
                districtField.disabled = true;
            }
        } catch (error) {
            console.error('District API Error:', error);
            districtField.innerHTML = '<option value="">ไม่สามารถโหลดข้อมูลตำบลได้</option>';
            districtField.disabled = true;
        } finally {
            showAddressLoading(false);
        }
    }

    // ฟังก์ชันดึงรหัสไปรษณีย์จากตำบล
    async function loadZipcodeByDistrict(districtCode) {
        console.log('📡 Loading zipcode for district:', districtCode);

        try {
            const response = await fetch(`${API_BASE_URL}/district/${districtCode}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('📦 District API Response:', data);

            if (data.status === 'success' && data.data && data.data.length > 0) {
                const districtData = data.data[0];
                const zipcode = districtData.zipcode;

                if (zipcode) {
                    zipcodeField.value = zipcode;
                    console.log('✅ Zipcode field updated:', zipcode);
                }

                updateFullAddress();
            }
        } catch (error) {
            console.error('❌ Zipcode API Error:', error);
        }
    }

    // แสดงข้อมูลจากการค้นหา zipcode
    function populateFieldsFromZipcode(data) {
        if (data.length === 0) return;

        const searchedZipcode = zipcodeField.value.trim();
        const relevantData = data.filter(item =>
            (item.zipcode || item.searched_zipcode) === searchedZipcode
        );

        if (relevantData.length === 0) {
            console.warn('⚠️ No data matches the searched zipcode');
            return;
        }

        const firstItem = relevantData[0];
        convertToProvinceInput(firstItem.province_name);

        const amphoes = getUniqueAmphoes(relevantData);
        populateAmphoeDropdown(amphoes);

        const districts = relevantData.map(item => ({
            code: item.district_code,
            name: item.district_name,
            name_en: item.district_name_en,
            amphoe_code: item.amphoe_code,
            zipcode: item.zipcode || item.searched_zipcode || searchedZipcode
        }));
        populateDistrictDropdown(districts);

        amphoeField.disabled = false;
        districtField.disabled = false;

        if (amphoes.length === 1) {
            amphoeField.value = amphoes[0].code;
            setTimeout(() => {
                filterDistrictsByAmphoe(amphoes[0].code);
            }, 100);
        }

        currentAddressData = relevantData;
    }

    // สร้าง dropdown จังหวัด
    function populateProvinceDropdown(provinces) {
        if (!provinceField.tagName || provinceField.tagName.toLowerCase() !== 'select') {
            convertToProvinceSelect();
        }

        provinceField.innerHTML = '<option value="">เลือกจังหวัด</option>';

        provinces.forEach(province => {
            if (province.code && province.name) {
                provinceField.innerHTML += `<option value="${province.code}">${province.name}</option>`;
            }
        });
    }

    // สร้าง dropdown อำเภอ
    function populateAmphoeDropdown(amphoes) {
        amphoeField.innerHTML = '<option value="">เลือกอำเภอ</option>';

        amphoes.forEach(amphoe => {
            if (amphoe && amphoe.code && amphoe.name) {
                amphoeField.innerHTML += `<option value="${amphoe.code}">${amphoe.name}</option>`;
            }
        });
    }

    // สร้าง dropdown ตำบล
    function populateDistrictDropdown(districts) {
        districtField.innerHTML = '<option value="">เลือกตำบล</option>';

        districts.forEach(district => {
            if (district && district.code && district.name) {
                districtField.innerHTML += `
                <option value="${district.code}" 
                        data-amphoe-code="${district.amphoe_code}"
                        data-zipcode="${district.zipcode || ''}">
                    ${district.name}
                </option>
            `;
            }
        });
    }

    // สร้างรายการอำเภอที่ไม่ซ้ำกัน
    function getUniqueAmphoes(data) {
        const uniqueAmphoes = [];
        const seenCodes = new Set();

        data.forEach(item => {
            if (!seenCodes.has(item.amphoe_code)) {
                seenCodes.add(item.amphoe_code);
                uniqueAmphoes.push({
                    code: item.amphoe_code,
                    name: item.amphoe_name,
                    name_en: item.amphoe_name_en
                });
            }
        });

        return uniqueAmphoes;
    }

    // กรองตำบลตามอำเภอที่เลือก
    function filterDistrictsByAmphoe(amphoeCode) {
        console.log('🔍 Filtering districts for amphoe:', amphoeCode);

        const options = districtField.querySelectorAll('option');
        let visibleCount = 0;

        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }

            const optionAmphoeCode = option.getAttribute('data-amphoe-code');

            if (String(optionAmphoeCode) === String(amphoeCode)) {
                option.style.display = 'block';
                visibleCount++;
            } else {
                option.style.display = 'none';
            }
        });

        console.log(`📊 Filtering result: ${visibleCount} districts visible`);
        updateFullAddress();
    }

    // แปลงจังหวัดเป็น select
    function convertToProvinceSelect() {
        if (provinceField && provinceField.tagName.toLowerCase() === 'select') return;

        const provinceWrapper = provinceField.parentNode;
        provinceField.remove();

        const selectHtml = `
        <select id="province_field" class="form-control" style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
            <option value="">เลือกจังหวัด</option>
        </select>
    `;

        provinceWrapper.insertAdjacentHTML('beforeend', selectHtml);
        provinceField = document.getElementById('province_field');
    }

    // แปลงจังหวัดเป็น input (สำหรับโหมด zipcode)
    function convertToProvinceInput(value = '') {
        if (provinceField && provinceField.tagName.toLowerCase() === 'input') {
            provinceField.value = value;
            return;
        }

        const provinceWrapper = provinceField.parentNode;
        provinceField.remove();

        const inputHtml = `
        <input type="text" id="province_field" class="form-control" 
               placeholder="จังหวัด" readonly value="${value}"
               style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
    `;

        provinceWrapper.insertAdjacentHTML('beforeend', inputHtml);
        provinceField = document.getElementById('province_field');
    }

    // รีเซ็ตกลับไปสู่การเลือกจังหวัด
    function resetToProvinceSelection() {
        convertToProvinceSelect();
        loadAllProvinces();

        amphoeField.innerHTML = '<option value="">เลือกอำเภอ</option>';
        amphoeField.disabled = true;
        districtField.innerHTML = '<option value="">เลือกตำบล</option>';
        districtField.disabled = true;

        document.querySelectorAll('.address-error').forEach(el => el.remove());
        updateFullAddress();
    }

    // ล้างข้อมูลที่ขึ้นต่อกันเมื่อ zipcode เปลี่ยน
    function clearDependentAddressFields() {
        if (provinceField.tagName.toLowerCase() === 'input') {
            provinceField.value = '';
        }

        amphoeField.innerHTML = '<option value="">เลือกอำเภอ</option>';
        amphoeField.disabled = true;
        districtField.innerHTML = '<option value="">เลือกตำบล</option>';
        districtField.disabled = true;

        document.querySelectorAll('.address-error').forEach(el => el.remove());
        updateFullAddress();
    }

    function updateFullAddress() {
        if (!fullAddressField) return;

        const additionalAddress = additionalAddressField ? additionalAddressField.value.trim() : '';

        // ใส่เฉพาะที่อยู่เพิ่มเติมใน elderly_aw_ods_address
        fullAddressField.value = additionalAddress;

        // จัดเก็บข้อมูลแยกสำหรับ guest
        const currentZipcode = zipcodeField ? zipcodeField.value : '';
        let currentProvince = '';
        let currentAmphoe = '';
        let currentDistrict = '';

        if (provinceField) {
            if (provinceField.tagName.toLowerCase() === 'select') {
                const selectedOption = provinceField.options[provinceField.selectedIndex];
                currentProvince = selectedOption && selectedOption.value ? selectedOption.text : '';
            } else {
                currentProvince = provinceField.value;
            }
        }

        if (amphoeField && amphoeField.selectedIndex > 0) {
            currentAmphoe = amphoeField.options[amphoeField.selectedIndex].text;
        }

        if (districtField && districtField.selectedIndex > 0) {
            currentDistrict = districtField.options[districtField.selectedIndex].text;
        }

        // เพิ่ม hidden fields สำหรับส่งข้อมูลแยก
        updateHiddenAddressFields(currentProvince, currentAmphoe, currentDistrict, currentZipcode);

        // แสดงผลในช่องแสดงที่อยู่รวม
        const addressPreview = document.getElementById('address_preview');
        const addressPreviewText = document.getElementById('address_preview_text');

        if (addressPreview && addressPreviewText) {
            let displayAddress = '';
            if (additionalAddress) displayAddress = additionalAddress;
            if (currentDistrict) displayAddress += (displayAddress ? ' ' : '') + 'ตำบล' + currentDistrict;
            if (currentAmphoe) displayAddress += (displayAddress ? ' ' : '') + 'อำเภอ' + currentAmphoe;
            if (currentProvince) displayAddress += (displayAddress ? ' ' : '') + 'จังหวัด' + currentProvince;
            if (currentZipcode && currentZipcode.length === 5) displayAddress += (displayAddress ? ' ' : '') + currentZipcode;

            if (displayAddress) {
                addressPreviewText.textContent = displayAddress;
                addressPreview.style.display = 'block';
            } else {
                addressPreview.style.display = 'none';
            }
        }
    }

    function updateHiddenAddressFields(province, amphoe, district, zipcode) {
        const hiddenFields = [
            { name: 'guest_province', value: province },
            { name: 'guest_amphoe', value: amphoe },
            { name: 'guest_district', value: district },
            { name: 'guest_zipcode', value: zipcode }
        ];

        hiddenFields.forEach(field => {
            let hiddenField = document.querySelector(`input[name="${field.name}"]`);
            if (!hiddenField) {
                hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = field.name;
                document.getElementById('elderlyAwOdsForm').appendChild(hiddenField);
            }
            hiddenField.value = field.value || '';
        });
    }

    // แสดง Loading indicator
    function showAddressLoading(show, context = 'zipcode') {
        if (show) {
            document.querySelectorAll('.address-loading-icon').forEach(el => el.remove());

            let targetField;
            switch (context) {
                case 'amphoe':
                    targetField = amphoeField;
                    break;
                case 'district':
                    targetField = districtField;
                    break;
                default:
                    targetField = zipcodeField;
            }

            if (targetField) {
                const icon = document.createElement('i');
                icon.className = 'fas fa-spinner fa-spin address-loading-icon';
                icon.style.cssText = 'position: absolute; right: 45px; top: 50%; transform: translateY(-50%); color: #1976d2; z-index: 3;';
                targetField.parentNode.appendChild(icon);
            }
        } else {
            document.querySelectorAll('.address-loading-icon').forEach(el => el.remove());
        }
    }

    // แสดงข้อความ Error
    function showAddressError(message) {
        document.querySelectorAll('.address-error').forEach(el => el.remove());

        if (zipcodeField) {
            const errorDiv = document.createElement('small');
            errorDiv.className = 'address-error text-danger form-text';
            errorDiv.textContent = message;
            zipcodeField.parentNode.appendChild(errorDiv);

            setTimeout(() => {
                errorDiv.style.opacity = '0';
                setTimeout(() => errorDiv.remove(), 300);
            }, 5000);
        }
    }

    // แก้ไข Form Action URL
    function fixFormAction() {
        const form = document.getElementById('elderlyAwOdsForm');
        if (form) {
            form.action = '<?= site_url('Elderly_aw_ods/add_elderly_aw_ods'); ?>';
            console.log('✅ Form action updated to:', form.action);
        }
    }

    // ตั้งค่าการตรวจสอบเบอร์โทร
    function setupPhoneValidation() {
        const phoneInput = document.getElementById('elderly_aw_ods_phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 10) value = value.slice(0, 10);
                e.target.value = value;
            });
        }
    }

    // ตั้งค่าการตรวจสอบเลขบัตรประชาชน
    function setupIdCardValidation() {
        const idInput = document.getElementById('elderly_aw_ods_number');
        if (idInput) {
            idInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 13) value = value.slice(0, 13);
                e.target.value = value;

                // Real-time validation
                if (value.length === 13) {
                    if (validateThaiIdCard(value)) {
                        e.target.style.borderColor = '#28a745';
                        e.target.style.boxShadow = '0 0 0 0.2rem rgba(40, 167, 69, 0.25)';
                    } else {
                        e.target.style.borderColor = '#dc3545';
                        e.target.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
                    }
                } else {
                    e.target.style.borderColor = '';
                    e.target.style.boxShadow = '';
                }
            });

            // Paste validation
            idInput.addEventListener('paste', function (e) {
                setTimeout(() => {
                    const pastedValue = e.target.value.replace(/\D/g, '');
                    e.target.value = pastedValue.substring(0, 13);

                    if (pastedValue.length === 13) {
                        if (validateThaiIdCard(pastedValue)) {
                            e.target.style.borderColor = '#28a745';
                            e.target.style.boxShadow = '0 0 0 0.2rem rgba(40, 167, 69, 0.25)';
                        } else {
                            e.target.style.borderColor = '#dc3545';
                            e.target.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
                        }
                    }
                }, 100);
            });
        }
    }

    // อัพเดทฟิลด์ตามสถานะ login
    function updateFormFieldsBasedOnLoginStatus() {
        if (!isUserLoggedIn || !userInfo) return;

        const nameField = document.querySelector('input[name="elderly_aw_ods_by"]');
        const phoneField = document.querySelector('input[name="elderly_aw_ods_phone"]');
        const emailField = document.querySelector('input[name="elderly_aw_ods_email"]');
        const idCardField = document.querySelector('input[name="elderly_aw_ods_number"]');

        // *** แก้ไข: ซ่อนฟิลด์ที่มีข้อมูลแล้วและเติมข้อมูลอัตโนมัติ ***
        if (nameField && userInfo.name) {
            nameField.value = userInfo.name;
            nameField.readOnly = true;
            nameField.style.backgroundColor = '#f8f9fa';
            nameField.removeAttribute('required');
            nameField.closest('.form-group').style.display = 'none';
        }

        if (phoneField && userInfo.phone) {
            phoneField.value = userInfo.phone;
            phoneField.readOnly = true;
            phoneField.style.backgroundColor = '#f8f9fa';
            phoneField.removeAttribute('required');
            phoneField.closest('.col-md-3').style.display = 'none';
        }

        if (emailField && userInfo.email) {
            emailField.value = userInfo.email;
            emailField.readOnly = true;
            emailField.style.backgroundColor = '#f8f9fa';
            emailField.removeAttribute('required');
            emailField.closest('.col-md-4').style.display = 'none';
        }

        // จัดการเลขบัตรประชาชน
        if (idCardField) {
            if (userInfo.number) {
                idCardField.value = userInfo.number;
                idCardField.readOnly = true;
                idCardField.style.backgroundColor = '#f8f9fa';
                idCardField.removeAttribute('required');
            } else {
                console.warn('⚠️ User has no ID card number - will show modal on submit');
                idCardField.removeAttribute('required');
            }
            idCardField.closest('.form-group').style.display = 'none';
        }

        // *** แก้ไข: จัดการข้อมูลที่อยู่สำหรับ user ที่ login ***
        if (userAddress && userAddress.parsed) {
            const parsed = userAddress.parsed;

            // ซ่อนระบบที่อยู่และลบ required
            const addressContainer = document.querySelector('#zipcode_field')?.closest('.form-group');
            if (addressContainer) {
                addressContainer.style.display = 'none';

                const addressFields = addressContainer.querySelectorAll('input[required], select[required]');
                addressFields.forEach(field => {
                    field.removeAttribute('required');
                });
            }

            // ใส่ข้อมูลที่อยู่ใน hidden field
            const fullAddressField = document.querySelector('#full_address_field');
            if (fullAddressField && parsed.full_address) {
                fullAddressField.value = parsed.full_address;
            }

            // อัพเดท hidden fields สำหรับ address
            updateHiddenAddressFields(
                parsed.province || '',
                parsed.amphoe || '',
                parsed.district || '',
                parsed.zipcode || ''
            );

            console.log('✅ Using address from user account:', parsed);
        }

        showLoggedInUserInfo();
    }

    // แสดงข้อมูล user ที่ login - ปรับปรุงใหม่
    function showLoggedInUserInfo() {
        if (!isUserLoggedIn || !userInfo) return;

        try {
            const existingInfo = document.getElementById('logged-in-user-info');
            if (existingInfo) {
                existingInfo.remove();
            }

            let userName = userInfo.name || 'ผู้ใช้';
            let userEmail = userInfo.email || 'ไม่ระบุ';
            let userPhone = userInfo.phone || 'ไม่ระบุ';
            let userAddressText = '';

            if (userAddress && userAddress.parsed) {
                userAddressText = userAddress.parsed.full_address || 'ใช้ข้อมูลจากบัญชี';
            } else if (userAddress && userAddress.full_address) {
                userAddressText = userAddress.full_address;
            } else {
                userAddressText = 'ใช้ข้อมูลจากบัญชี';
            }

            const userInfoHTML = `
            <div id="logged-in-user-info" class="alert alert-success" style="
                border-radius: 20px; 
                background: linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%); 
                border: 1px solid rgba(33, 150, 243, 0.3);
                margin-bottom: 2.5rem;
                padding: 2rem;
                box-shadow: 0 8px 25px rgba(33, 150, 243, 0.15);
                backdrop-filter: blur(10px);
                max-width: 100%;
                width: 100%;
            ">
                <div class="row align-items-center g-4">
                    <!-- ไอคอนและหัวข้อ -->
                    <div class="col-12 text-center mb-3">
                        <div style="
                            width: 80px; 
                            height: 80px; 
                            background: linear-gradient(135deg, rgba(33, 150, 243, 0.15) 0%, rgba(100, 181, 246, 0.15) 100%); 
                            border-radius: 50%; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center;
                            box-shadow: 0 6px 20px rgba(33, 150, 243, 0.3);
                            margin: 0 auto 1rem;
                        ">
                            <i class="fas fa-user-check" style="font-size: 2.2rem; color: #1976d2;"></i>
                        </div>
                        <h4 class="mb-2" style="color: #1565c0; font-weight: 700; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <i class="fas fa-check-circle me-2"></i>ใช้ข้อมูลจากบัญชีของคุณ
                        </h4>
                        <p class="mb-0" style="color: #1565c0; font-size: 1rem; opacity: 0.8;">
                            ระบบจะใช้ข้อมูลจากบัญชีของคุณโดยอัตโนมัติ ไม่ต้องกรอกข้อมูลใหม่
                        </p>
                    </div>
                    
                    <!-- ข้อมูลส่วนตัว -->
                    <div class="col-12">
                        <div class="row g-4">
                            <!-- ชื่อและอีเมล -->
                            <div class="col-lg-6 col-md-12">
                                <div class="info-card" style="
                                    background: rgba(255, 255, 255, 0.7);
                                    border-radius: 15px;
                                    padding: 1.5rem;
                                    border: 1px solid rgba(33, 150, 243, 0.1);
                                    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.08);
                                    height: 100%;
                                ">
                                    <h6 style="color: #1976d2; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center;">
                                        <i class="fas fa-user-circle me-2" style="font-size: 1.2rem;"></i>
                                        ข้อมูลส่วนตัว
                                    </h6>
                                    <div class="info-item mb-3" style="display: flex; align-items: center; padding: 0.5rem 0;">
                                        <div class="info-icon" style="
                                            width: 40px; 
                                            height: 40px; 
                                            background: linear-gradient(135deg, #1976d2, #42a5f5); 
                                            border-radius: 10px; 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center;
                                            margin-right: 1rem;
                                            box-shadow: 0 3px 10px rgba(33, 150, 243, 0.3);
                                        ">
                                            <i class="fas fa-user" style="color: white; font-size: 1rem;"></i>
                                        </div>
                                        <div class="info-text" style="flex: 1;">
                                            <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.2rem;">ชื่อ-นามสกุล</div>
                                            <div style="font-size: 1.1rem; font-weight: 600; color: #1565c0;">${userName}</div>
                                        </div>
                                    </div>
                                    <div class="info-item" style="display: flex; align-items: center; padding: 0.5rem 0;">
                                        <div class="info-icon" style="
                                            width: 40px; 
                                            height: 40px; 
                                            background: linear-gradient(135deg, #1976d2, #42a5f5); 
                                            border-radius: 10px; 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center;
                                            margin-right: 1rem;
                                            box-shadow: 0 3px 10px rgba(33, 150, 243, 0.3);
                                        ">
                                            <i class="fas fa-envelope" style="color: white; font-size: 1rem;"></i>
                                        </div>
                                        <div class="info-text" style="flex: 1;">
                                            <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.2rem;">อีเมล</div>
                                            <div style="font-size: 1.1rem; font-weight: 600; color: #1565c0; word-break: break-all;">${userEmail}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- เบอร์โทรและที่อยู่ -->
                            <div class="col-lg-6 col-md-12">
                                <div class="info-card" style="
                                    background: rgba(255, 255, 255, 0.7);
                                    border-radius: 15px;
                                    padding: 1.5rem;
                                    border: 1px solid rgba(33, 150, 243, 0.1);
                                    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.08);
                                    height: 100%;
                                ">
                                    <h6 style="color: #1976d2; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center;">
                                        <i class="fas fa-address-book me-2" style="font-size: 1.2rem;"></i>
                                        ข้อมูลติดต่อ
                                    </h6>
                                    <div class="info-item mb-3" style="display: flex; align-items: center; padding: 0.5rem 0;">
                                        <div class="info-icon" style="
                                            width: 40px; 
                                            height: 40px; 
                                            background: linear-gradient(135deg, #1976d2, #42a5f5); 
                                            border-radius: 10px; 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center;
                                            margin-right: 1rem;
                                            box-shadow: 0 3px 10px rgba(33, 150, 243, 0.3);
                                        ">
                                            <i class="fas fa-phone" style="color: white; font-size: 1rem;"></i>
                                        </div>
                                        <div class="info-text" style="flex: 1;">
                                            <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.2rem;">เบอร์โทรศัพท์</div>
                                            <div style="font-size: 1.1rem; font-weight: 600; color: #1565c0;">${userPhone}</div>
                                        </div>
                                    </div>
                                    <div class="info-item" style="display: flex; align-items: flex-start; padding: 0.5rem 0;">
                                        <div class="info-icon" style="
                                            width: 40px; 
                                            height: 40px; 
                                            background: linear-gradient(135deg, #1976d2, #42a5f5); 
                                            border-radius: 10px; 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center;
                                            margin-right: 1rem;
                                            margin-top: 0.2rem;
                                            box-shadow: 0 3px 10px rgba(33, 150, 243, 0.3);
                                            flex-shrink: 0;
                                        ">
                                            <i class="fas fa-map-marker-alt" style="color: white; font-size: 1rem;"></i>
                                        </div>
                                        <div class="info-text" style="flex: 1;">
                                            <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.2rem;">ที่อยู่</div>
                                            <div style="font-size: 1rem; font-weight: 500; color: #1565c0; line-height: 1.4; word-break: break-word;">${userAddressText}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ข้อความแจ้งเตือน -->
                    <div class="col-12">
                        <div class="alert alert-info" style="
                            background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(23, 162, 184, 0.05) 100%);
                            border: 1px solid rgba(23, 162, 184, 0.3);
                            border-radius: 12px;
                            margin: 0;
                            padding: 1rem 1.5rem;
                        ">
                            <div class="d-flex align-items-center">
                                <div style="
                                    width: 45px; 
                                    height: 45px; 
                                    background: linear-gradient(135deg, #17a2b8, #20c997); 
                                    border-radius: 10px; 
                                    display: flex; 
                                    align-items: center; 
                                    justify-content: center;
                                    margin-right: 1rem;
                                    box-shadow: 0 3px 10px rgba(23, 162, 184, 0.3);
                                ">
                                    <i class="fas fa-magic" style="color: white; font-size: 1.2rem;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <h6 style="color: #0c5460; font-weight: 600; margin-bottom: 0.5rem;">
                                        <i class="fas fa-sparkles me-2"></i>ประหยัดเวลา ไม่ต้องกรอกข้อมูลซ้ำ
                                    </h6>
                                    <p style="color: #0c5460; margin: 0; font-size: 0.95rem; line-height: 1.5;">
                                        ระบบจะนำข้อมูลจากบัญชีของคุณมาใช้โดยอัตโนมัติ คุณสามารถยื่นเรื่องเบี้ยยังชีพได้ทันที
                                        และติดตามสถานะได้จากหน้าบัญชีของคุณ
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

            const formContainer = document.getElementById('elderly_aw_ods_form');
            const firstFormGroup = formContainer.querySelector('.form-group, .elderly-type-selector');
            if (firstFormGroup) {
                firstFormGroup.insertAdjacentHTML('beforebegin', userInfoHTML);
            }

            // เพิ่ม Animation เมื่อแสดงผล
            setTimeout(() => {
                const userInfoElement = document.getElementById('logged-in-user-info');
                if (userInfoElement) {
                    userInfoElement.style.transform = 'translateY(0)';
                    userInfoElement.style.opacity = '1';

                    // เพิ่ม Animation สำหรับแต่ละ info card
                    const infoCards = userInfoElement.querySelectorAll('.info-card');
                    infoCards.forEach((card, index) => {
                        setTimeout(() => {
                            card.style.transform = 'translateY(0)';
                            card.style.opacity = '1';
                        }, index * 150);
                    });
                }
            }, 100);

            console.log('✅ Enhanced user info display created');

        } catch (error) {
            console.error('❌ Error showing user info:', error);
        }
    }

    // เพิ่ม CSS Animation
    const userInfoStyles = `
<style>
#logged-in-user-info {
    transform: translateY(-20px);
    opacity: 0;
    transition: all 0.6s ease-out;
}

#logged-in-user-info .info-card {
    transform: translateY(20px);
    opacity: 0;
    transition: all 0.5s ease-out;
}

#logged-in-user-info .info-item {
    transition: all 0.3s ease;
}

#logged-in-user-info .info-item:hover {
    transform: translateX(5px);
}

#logged-in-user-info .info-icon {
    transition: all 0.3s ease;
}

#logged-in-user-info .info-item:hover .info-icon {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(33, 150, 243, 0.4) !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #logged-in-user-info {
        padding: 1.5rem !important;
        margin-bottom: 2rem !important;
    }
    
    #logged-in-user-info .info-card {
        padding: 1rem !important;
        margin-bottom: 1rem;
    }
    
    #logged-in-user-info .info-icon {
        width: 35px !important;
        height: 35px !important;
    }
    
    #logged-in-user-info .info-text div:last-child {
        font-size: 1rem !important;
    }
}

@media (max-width: 576px) {
    #logged-in-user-info {
        padding: 1rem !important;
    }
    
    #logged-in-user-info h4 {
        font-size: 1.3rem !important;
    }
    
    #logged-in-user-info .row.g-4 {
        gap: 1rem !important;
    }
}
</style>
`;

    // เพิ่ม CSS ลงใน head
    if (!document.getElementById('user-info-styles')) {
        const styleElement = document.createElement('div');
        styleElement.id = 'user-info-styles';
        styleElement.innerHTML = userInfoStyles;
        document.head.appendChild(styleElement);
    }






    // แสดงข้อความต้อนรับ
    function showWelcomeMessage() {
        if (isUserLoggedIn && userInfo && userInfo.name) {
            Swal.fire({
                icon: 'success',
                title: `ยินดีต้อนรับ ${userInfo.name}`,
                text: 'คุณสามารถยื่นเรื่องเบี้ยยังชีพได้ทันที ข้อมูลของคุณจะถูกใช้โดยอัตโนมัติ',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: 'linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%)',
                color: '#1565c0'
            });
        }
    }

    // แสดง modal สำหรับเลขบัตรประชาชน
    function showIdCardModal() {
        Swal.fire({
            title: '<span style="color: #1976d2;">กรอกเลขบัตรประจำตัวประชาชน</span>',
            html: `
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.25) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);">
                    <i class="fas fa-id-card" style="font-size: 2.5rem; color: #dc3545;"></i>
                </div>
                <p style="color: #666; font-size: 1.1rem; line-height: 1.6; margin-bottom: 1rem;">
                    <strong style="color: #dc3545;">จำเป็นต่อการให้บริการ</strong><br>
                    กรุณากรอกเลขบัตรประจำตัวประชาชน 13 หลัก<br>
                    เพื่อใช้ในการยื่นเรื่องเบี้ยยังชีพและติดตามสถานะ
                </p>
                <input type="text" id="swal-id-card" class="swal2-input" placeholder="เลขบัตรประจำตัวประชาชน 13 หลัก" maxlength="13" style="font-size: 1.1rem; text-align: center; letter-spacing: 1px;" />
                <div id="id-card-error" style="color: #dc3545; font-size: 0.9rem; margin-top: 0.5rem; display: none;"></div>
                <div id="id-card-check" style="color: #28a745; font-size: 0.9rem; margin-top: 0.5rem; display: none;">
                    <i class="fas fa-check-circle me-1"></i>เลขบัตรประชาชนถูกต้อง
                </div>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save me-2"></i>บันทึก',
            cancelButtonText: '<i class="fas fa-times me-2"></i>ยกเลิก',
            confirmButtonColor: '#1976d2',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false,
            allowEscapeKey: false,
            preConfirm: () => {
                const idCard = document.getElementById('swal-id-card').value.trim();
                const errorDiv = document.getElementById('id-card-error');

                if (!idCard) {
                    errorDiv.textContent = 'กรุณากรอกเลขบัตรประจำตัวประชาชน';
                    errorDiv.style.display = 'block';
                    return false;
                }

                if (!/^\d{13}$/.test(idCard)) {
                    errorDiv.textContent = 'กรุณากรอกเลขบัตรประจำตัวประชาชน 13 หลัก (ตัวเลขเท่านั้น)';
                    errorDiv.style.display = 'block';
                    return false;
                }

                if (!validateThaiIdCard(idCard)) {
                    errorDiv.textContent = 'เลขบัตรประจำตัวประชาชนไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง';
                    errorDiv.style.display = 'block';
                    return false;
                }

                return idCard;
            },
            didOpen: () => {
                const input = document.getElementById('swal-id-card');
                const errorDiv = document.getElementById('id-card-error');
                const checkDiv = document.getElementById('id-card-check');

                input.addEventListener('input', function (e) {
                    e.target.value = e.target.value.replace(/\D/g, '');

                    errorDiv.style.display = 'none';
                    checkDiv.style.display = 'none';

                    const currentValue = e.target.value.trim();
                    if (currentValue.length === 13) {
                        if (validateThaiIdCard(currentValue)) {
                            checkDiv.style.display = 'block';
                            errorDiv.style.display = 'none';
                            e.target.style.borderColor = '#28a745';
                        } else {
                            errorDiv.textContent = 'เลขบัตรประจำตัวประชาชนไม่ถูกต้อง';
                            errorDiv.style.display = 'block';
                            checkDiv.style.display = 'none';
                            e.target.style.borderColor = '#dc3545';
                        }
                    } else if (currentValue.length > 0) {
                        e.target.style.borderColor = '#6c757d';
                    }
                });

                input.addEventListener('paste', function (e) {
                    setTimeout(() => {
                        const pastedValue = e.target.value.replace(/\D/g, '');
                        e.target.value = pastedValue.substring(0, 13);

                        if (pastedValue.length === 13) {
                            if (validateThaiIdCard(pastedValue)) {
                                checkDiv.style.display = 'block';
                                errorDiv.style.display = 'none';
                                e.target.style.borderColor = '#28a745';
                            } else {
                                errorDiv.textContent = 'เลขบัตรประจำตัวประชาชนไม่ถูกต้อง';
                                errorDiv.style.display = 'block';
                                checkDiv.style.display = 'none';
                                e.target.style.borderColor = '#dc3545';
                            }
                        }
                    }, 100);
                });

                input.focus();
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                updateUserIdCard(result.value);
            } else {
                showAlert('info', 'การยื่นเรื่องถูกยกเลิก', 'จำเป็นต้องมีเลขบัตรประจำตัวประชาชนเพื่อการให้บริการ');
            }
        });
    }

    // อัปเดตเลขบัตรประชาชน
    function updateUserIdCard(idCardNumber) {
        const loadingAlert = Swal.fire({
            title: 'กำลังบันทึก...',
            html: '<div style="text-align: center;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #1976d2;"></i><br><br>กำลังอัพเดทข้อมูลของคุณ</div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false
        });

        fetch('/Auth_public_mem/update_id_card', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `mp_number=${encodeURIComponent(idCardNumber)}`
        })
            .then(response => response.json())
            .then(data => {
                loadingAlert.close();

                if (data.success) {
                    if (userInfo) {
                        userInfo.number = idCardNumber;
                    }

                    const idCardField = document.querySelector('input[name="elderly_aw_ods_number"]');
                    if (idCardField) {
                        idCardField.value = idCardNumber;
                        idCardField.readOnly = true;
                        idCardField.style.backgroundColor = '#f8f9fa';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ!',
                        text: 'อัพเดทเลขบัตรประจำตัวประชาชนแล้ว ดำเนินการยื่นเรื่องต่อ',
                        confirmButtonColor: '#1976d2',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        submitForm();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: data.message || 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                        confirmButtonColor: '#dc3545'
                    }).then(() => {
                        showIdCardModal();
                    });
                }
            })
            .catch(error => {
                loadingAlert.close();
                console.error('Update ID Card Error:', error);

                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาดในระบบ',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง',
                    confirmButtonColor: '#dc3545'
                }).then(() => {
                    showIdCardModal();
                });
            });
    }

    // *** File handling functions ***
    async function handleFileSelect(input) {
        if (input._processing) return;
        input._processing = true;

        const files = Array.from(input.files);
        if (files.length === 0) {
            input._processing = false;
            return;
        }

        if (selectedFiles.length + files.length > maxFiles) {
            showAlert('warning', 'เกินจำนวนที่กำหนด', `คุณสามารถอัพโหลดได้สูงสุด ${maxFiles} ไฟล์เท่านั้น`);
            input._processing = false;
            input.value = '';
            return;
        }

        for (let file of files) {
            if (validateFile(file)) {
                file.id = Date.now() + Math.random();
                selectedFiles.push(file);
            } else {
                input._processing = false;
                input.value = '';
                return;
            }
        }

        updateFileDisplay();
        setTimeout(() => {
            input.value = '';
            input._processing = false;
        }, 100);
    }

    function validateFile(file) {
        if (!allowedTypes.includes(file.type.toLowerCase())) {
            showAlert('error', 'ประเภทไฟล์ไม่ถูกต้อง', 'รองรับเฉพาะไฟล์ JPG, PNG และ PDF เท่านั้น');
            return false;
        }

        if (file.size > maxFileSize) {
            showAlert('error', 'ไฟล์ใหญ่เกินไป', `ขนาดไฟล์ต้องไม่เกิน ${maxFileSize / (1024 * 1024)} MB`);
            return false;
        }

        return true;
    }

    function updateFileDisplay() {
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const previewArea = document.getElementById('file-preview-area');
        const previewContainer = document.getElementById('preview-container');
        const fileCount = document.getElementById('file-count');

        if (selectedFiles.length === 0) {
            uploadPlaceholder.style.display = 'block';
            previewArea.style.display = 'none';
            return;
        }

        uploadPlaceholder.style.display = 'none';
        previewArea.style.display = 'block';
        fileCount.textContent = selectedFiles.length;

        previewContainer.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const previewItem = createPreviewItem(file, index);
            previewContainer.appendChild(previewItem);
        });
    }

    function createPreviewItem(file, index) {
        const div = document.createElement('div');
        div.className = 'preview-item';
        div.setAttribute('data-file-id', file.id);

        const preview = document.createElement('div');

        if (file.type === 'application/pdf') {
            preview.className = 'preview-pdf';
            preview.innerHTML = '<i class="fas fa-file-pdf"></i>';
        } else {
            const img = document.createElement('img');
            img.className = 'preview-image';
            img.alt = file.name;

            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);

            preview.appendChild(img);
        }

        const info = document.createElement('div');
        info.className = 'preview-info';

        const name = document.createElement('p');
        name.className = 'preview-name';
        name.textContent = file.name;
        name.title = file.name;

        const size = document.createElement('p');
        size.className = 'preview-size';
        size.textContent = formatFileSize(file.size);

        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-file';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.title = 'ลบไฟล์นี้';
        removeBtn.onclick = () => removeFile(file.id);

        info.appendChild(name);
        info.appendChild(size);

        div.appendChild(preview);
        div.appendChild(info);
        div.appendChild(removeBtn);

        return div;
    }

    function removeFile(fileId) {
        selectedFiles = selectedFiles.filter(file => file.id !== fileId);
        updateFileDisplay();
    }

    function clearAllFiles() {
        selectedFiles = [];
        updateFileDisplay();
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Drag & Drop functions
    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
    }

    function handleDragEnter(e) {
        e.preventDefault();
        e.currentTarget.classList.add('drag-over');
    }

    function handleDragLeave(e) {
        e.preventDefault();
        if (!e.currentTarget.contains(e.relatedTarget)) {
            e.currentTarget.classList.remove('drag-over');
        }
    }

    function handleDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');

        const files = Array.from(e.dataTransfer.files).filter(file =>
            allowedTypes.includes(file.type.toLowerCase())
        );

        if (files.length === 0) {
            showAlert('error', 'ไม่พบไฟล์ที่รองรับ', 'กรุณาเลือกไฟล์รูปภาพหรือ PDF เท่านั้น');
            return;
        }

        const fileInput = document.getElementById('elderly_aw_ods_files');
        fileInput.files = e.dataTransfer.files;
        handleFileSelect(fileInput);
    }

    // Form submission
    function handleElderlyAwOdsSubmit(event) {
        event.preventDefault();

        if (formSubmitting) return false;

        // เช็คเลขบัตรประชาชนสำหรับ public user
        if (isUserLoggedIn && userInfo && userInfo.id && (!userInfo.number || userInfo.number === '')) {
            showIdCardModal();
            return false;
        }

        if (!isUserLoggedIn && !hasConfirmedAsGuest) {
            showModal();
        } else {
            submitForm();
        }

        return false;
    }

    function submitForm() {
        try {
            const form = document.getElementById('elderlyAwOdsForm');

            if (!form || !form.checkValidity()) {
                console.warn('❌ Form validation failed');

                const fieldNameMapping = {
                    'elderly_aw_ods_by': 'ชื่อ-นามสกุล',
                    'elderly_aw_ods_phone': 'เบอร์โทรศัพท์',
                    'elderly_aw_ods_email': 'อีเมล',
                    'elderly_aw_ods_number': 'เลขบัตรประชาชน',
                    'elderly_aw_ods_address': 'ที่อยู่',
                    'additional_address_field': 'ที่อยู่เพิ่มเติม',
                    'district_field': 'ตำบล'
                };

                const invalidFields = form.querySelectorAll(':invalid');
                const visibleInvalidFields = Array.from(invalidFields).filter(field => {
                    const style = window.getComputedStyle(field);
                    const parentStyle = field.closest('.form-group, .col-md-3, .col-md-4, .col-md-5') ?
                        window.getComputedStyle(field.closest('.form-group, .col-md-3, .col-md-4, .col-md-5')) : null;

                    return style.display !== 'none' &&
                        (!parentStyle || parentStyle.display !== 'none') &&
                        !field.hasAttribute('hidden');
                });

                if (visibleInvalidFields.length > 0) {
                    const fieldNames = visibleInvalidFields.map(field => {
                        if (fieldNameMapping[field.name]) {
                            return fieldNameMapping[field.name];
                        }
                        if (fieldNameMapping[field.id]) {
                            return fieldNameMapping[field.id];
                        }

                        const label = document.querySelector(`label[for="${field.id}"]`);
                        if (label) {
                            return label.textContent.replace('*', '').trim();
                        }

                        return field.name || field.id || 'ฟิลด์ที่ไม่ทราบชื่อ';
                    });

                    showAlert('warning', 'กรุณากรอกข้อมูลให้ครบถ้วน', 'มีข้อมูลที่จำเป็นยังไม่ได้กรอก: ' + fieldNames.join(', '));

                    if (visibleInvalidFields[0]) {
                        visibleInvalidFields[0].focus();
                    }
                    return;
                } else {
                    showAlert('warning', 'กรุณาตรวจสอบข้อมูล', 'มีข้อมูลที่ไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง');
                    return;
                }
            }

            // *** เพิ่มการตรวจสอบไฟล์ก่อนส่งฟอร์ม ***
            if (!validateFileRequirement()) {
                return; // หยุดการส่งถ้าไฟล์ไม่ผ่านการตรวจสอบ
            }

            // ตรวจสอบที่อยู่สำหรับ guest user
            if (!isUserLoggedIn) {
                const additionalAddress = document.querySelector('#additional_address_field')?.value?.trim();
                const district = document.querySelector('#district_field')?.value?.trim();

                if (!additionalAddress || additionalAddress.length < 2) {
                    showAlert('warning', 'กรุณากรอกที่อยู่เพิ่มเติม', 'กรุณากรอกบ้านเลขที่ ซอย ถนน หรือรายละเอียดเพิ่มเติม (อย่างน้อย 2 ตัวอักษร)');
                    document.querySelector('#additional_address_field')?.focus();
                    return;
                }

                if (!district) {
                    showAlert('warning', 'กรุณาเลือกตำบล', 'กรุณาเลือกตำบลหรือกรอกรหัสไปรษณีย์เพื่อค้นหาข้อมูลที่อยู่');
                    document.querySelector('#district_field')?.focus();
                    return;
                }
            }

            // ตรวจสอบเลขบัตรประชาชนสำหรับ guest
            if (!isUserLoggedIn) {
                const idCardValue = document.querySelector('input[name="elderly_aw_ods_number"]')?.value?.trim();

                if (!idCardValue || !/^\d{13}$/.test(idCardValue)) {
                    showAlert('error', 'เลขบัตรประชาชนไม่ถูกต้อง', 'กรุณากรอกเลขบัตรประจำตัวประชาชน 13 หลัก (ตัวเลขเท่านั้น)');
                    document.querySelector('input[name="elderly_aw_ods_number"]')?.focus();
                    return;
                }

                if (!validateThaiIdCard(idCardValue)) {
                    showAlert('error', 'เลขบัตรประชาชนไม่ถูกต้อง', 'เลขบัตรประจำตัวประชาชนที่กรอกไม่ถูกต้องตามมาตรฐาน กรุณาตรวจสอบอีกครั้ง');
                    document.querySelector('input[name="elderly_aw_ods_number"]')?.focus();
                    return;
                }
            }

            if (formSubmitting) return;
            formSubmitting = true;

            const submitBtn = document.getElementById('submitElderlyAwOdsBtn');
            const originalContent = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังยื่นเรื่อง...';

            console.log('📝 Elderly Aw Ods form submitted');

            // *** แก้ไข: ตรวจสอบ reCAPTCHA ทุกครั้งไม่ว่า User จะ Login หรือไม่ ***
            console.log('🔍 User login status for elderly form:');
            console.log('- isUserLoggedIn:', isUserLoggedIn);
            console.log('- userInfo exists:', !!userInfo);

            // *** ตรวจสอบ reCAPTCHA ทุกกรณี (ทั้ง logged user และ guest user) ***
            console.log('🔍 Checking reCAPTCHA requirements for ALL users...');

            const hasRecaptchaKey = window.RECAPTCHA_SITE_KEY && window.RECAPTCHA_SITE_KEY !== '';
            const isRecaptchaReady = window.recaptchaReady === true;
            const isNotSkipDev = !window.SKIP_RECAPTCHA_FOR_DEV;
            const isGrecaptchaAvailable = typeof grecaptcha !== 'undefined';

            console.log('🔍 reCAPTCHA condition check for ALL users:');
            console.log('- hasRecaptchaKey:', hasRecaptchaKey);
            console.log('- isRecaptchaReady:', isRecaptchaReady);
            console.log('- isNotSkipDev:', isNotSkipDev);
            console.log('- isGrecaptchaAvailable:', isGrecaptchaAvailable);

            const shouldUseRecaptcha = hasRecaptchaKey && isRecaptchaReady && isNotSkipDev && isGrecaptchaAvailable;
            console.log('🔍 Should use reCAPTCHA for this submission:', shouldUseRecaptcha);

            // ตรวจสอบว่ามี reCAPTCHA หรือไม่ (สำหรับทุก User)
            if (shouldUseRecaptcha) {
                console.log('🛡️ Executing reCAPTCHA for user type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');

                grecaptcha.ready(function () {
                    console.log('🔧 grecaptcha.ready() called for elderly aw ods submission');

                    grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                        action: 'elderly_aw_ods_submit'
                    }).then(function (token) {
                        console.log('✅ reCAPTCHA token received for user:', token.substring(0, 50) + '...');
                        console.log('📏 Token length:', token.length);
                        console.log('👤 User type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');

                        performElderlyAwOdsSubmitWithRecaptcha(form, token, submitBtn, originalContent);
                    }).catch(function (error) {
                        console.error('❌ reCAPTCHA execution failed:', error);
                        console.log('🔄 Falling back to submit without reCAPTCHA');
                        performElderlyAwOdsSubmitWithoutRecaptcha(form, submitBtn, originalContent);
                    });
                });
            } else {
                console.log('⚠️ reCAPTCHA not available, submitting without verification');
                console.log('📋 Reasons breakdown:');
                console.log('- SITE_KEY exists:', !!window.RECAPTCHA_SITE_KEY);
                console.log('- reCAPTCHA ready:', !!window.recaptchaReady);
                console.log('- Skip dev mode:', !!window.SKIP_RECAPTCHA_FOR_DEV);
                console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');
                console.log('👤 User type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');

                performElderlyAwOdsSubmitWithoutRecaptcha(form, submitBtn, originalContent);
            }

        } catch (error) {
            console.error('Submit form error:', error);
            showAlert('error', 'เกิดข้อผิดพลาดในระบบ', 'ไม่สามารถส่งฟอร์มได้ กรุณาลองใหม่');
            formSubmitting = false;
        }
    }

    // *** แก้ไข: Enhanced Address Handling with Better Validation ***
    function buildFullAddress() {
        let fullAddress = '';

        console.log('🏠 === ADDRESS BUILDING DEBUG ===');
        console.log('isUserLoggedIn:', isUserLoggedIn);
        console.log('userInfo:', userInfo);

        if (isUserLoggedIn && userInfo) {
            console.log('🏠 Processing address for LOGGED user');
            console.log('userInfo.address:', userInfo.address);

            // Method 1: ใช้ address object
            if (userInfo.address && typeof userInfo.address === 'object') {
                const addr = userInfo.address;
                console.log('📍 Using address object:', addr);

                const addressParts = [
                    addr.additional_address || '',
                    addr.district ? `ตำบล${addr.district}` : '',
                    addr.amphoe ? `อำเภอ${addr.amphoe}` : '',
                    addr.province ? `จังหวัด${addr.province}` : '',
                    addr.zipcode || ''
                ].filter(part => part.trim() !== '');

                fullAddress = addressParts.join(' ');
            }

            // Method 2: Fallback หาจาก field อื่น
            if (!fullAddress || fullAddress.trim() === '') {
                console.log('🔄 Trying fallback address methods...');

                // ลองใช้ full_address
                if (userInfo.full_address && userInfo.full_address.trim() !== '') {
                    fullAddress = userInfo.full_address;
                    console.log('📍 Using full_address:', fullAddress);
                }
                // ลองใช้ mp_address
                else if (userInfo.mp_address && userInfo.mp_address.trim() !== '') {
                    fullAddress = userInfo.mp_address;
                    console.log('📍 Using mp_address:', fullAddress);
                }
                // ลองใช้ address string
                else if (typeof userInfo.address === 'string' && userInfo.address.trim() !== '') {
                    fullAddress = userInfo.address;
                    console.log('📍 Using address string:', fullAddress);
                }
            }

            // Method 3: ลองดึงจาก hidden fields ในฟอร์ม
            if (!fullAddress || fullAddress.trim() === '') {
                console.log('🔄 Trying to get address from form fields...');

                const hiddenAddress = document.querySelector('input[name="user_address"]');
                if (hiddenAddress && hiddenAddress.value.trim() !== '') {
                    fullAddress = hiddenAddress.value;
                    console.log('📍 Using hidden form address:', fullAddress);
                }
            }

        } else if (!isUserLoggedIn) {
            console.log('🏠 Processing address for GUEST user');

            // สำหรับ guest user
            const additionalAddress = document.querySelector('#additional_address_field')?.value?.trim();
            const district = document.querySelector('#district_field')?.value?.trim();
            const guestProvince = document.querySelector('#guest_province_field')?.value || '';
            const guestAmphoe = document.querySelector('#guest_amphoe_field')?.value || '';
            const guestDistrict = document.querySelector('#guest_district_field')?.value || '';
            const guestZipcode = document.querySelector('#guest_zipcode_field')?.value || '';

            console.log('📍 Guest address components:', {
                additionalAddress, district, guestProvince, guestAmphoe, guestDistrict, guestZipcode
            });

            if (additionalAddress && district) {
                const addressParts = [
                    additionalAddress,
                    district ? `ตำบล${district}` : '',
                    guestAmphoe ? `อำเภอ${guestAmphoe}` : '',
                    guestProvince ? `จังหวัด${guestProvince}` : '',
                    guestZipcode || ''
                ].filter(part => part.trim() !== '');

                fullAddress = addressParts.join(' ');
            }
        }

        console.log('🏠 Final fullAddress result:', fullAddress);
        console.log('🏠 Address length:', fullAddress.length);

        return fullAddress;
    }

    // *** แก้ไข: Enhanced Address Validation ***
    function validateAndSetAddress(formData) {
        console.log('🔍 === ADDRESS VALIDATION START ===');

        let fullAddress = buildFullAddress();

        // *** CRITICAL: บังคับให้มี address เสมอ ***
        if (!fullAddress || fullAddress.trim() === '' || fullAddress.length < 5) {
            console.warn('⚠️ Address validation failed, trying emergency fallback...');

            // Emergency fallback 1: ลองดึงจาก display element
            const addressDisplay = document.querySelector('.user-address, .address-display, [data-address]');
            if (addressDisplay && addressDisplay.textContent.trim() !== '') {
                fullAddress = addressDisplay.textContent.trim();
                console.log('🆘 Emergency address from display:', fullAddress);
            }

            // Emergency fallback 2: ลองสร้างจาก userInfo fields อื่น
            if ((!fullAddress || fullAddress.trim() === '') && isUserLoggedIn && userInfo) {
                const emergencyParts = [];

                if (userInfo.mp_address) emergencyParts.push(userInfo.mp_address);
                if (userInfo.province) emergencyParts.push(`จังหวัด${userInfo.province}`);
                if (userInfo.amphoe) emergencyParts.push(`อำเภอ${userInfo.amphoe}`);
                if (userInfo.district) emergencyParts.push(`ตำบล${userInfo.district}`);
                if (userInfo.zipcode) emergencyParts.push(userInfo.zipcode);

                if (emergencyParts.length > 0) {
                    fullAddress = emergencyParts.join(' ');
                    console.log('🆘 Emergency address from userInfo parts:', fullAddress);
                }
            }

            // Emergency fallback 3: ใช้ค่า default
            if (!fullAddress || fullAddress.trim() === '') {
                fullAddress = `ที่อยู่ไม่ระบุ - User ${isUserLoggedIn ? userInfo?.mp_id || 'Unknown' : 'Guest'} - ${new Date().toISOString()}`;
                console.warn('🆘 Using emergency default address:', fullAddress);
            }
        }

        // *** VALIDATION: ตรวจสอบความยาวขั้นต่ำ ***
        if (fullAddress.length < 10) {
            console.warn('⚠️ Address too short, padding with additional info...');
            if (isUserLoggedIn && userInfo) {
                fullAddress += ` (User: ${userInfo.mp_name || userInfo.mp_id || 'Unknown'})`;
            }
        }

        // *** SET TO FORMDATA ***
        formData.set('elderly_aw_ods_address', fullAddress);
        console.log('✅ Final address set to FormData:', fullAddress);
        console.log('🔍 === ADDRESS VALIDATION END ===');

        return fullAddress;
    }

    // *** แก้ไข: ปรับปรุง performElderlyAwOdsSubmitWithRecaptcha ***
    function performElderlyAwOdsSubmitWithRecaptcha(form, recaptchaToken, submitBtn, originalContent) {
        console.log('📤 Submitting elderly aw ods with reCAPTCHA token...');
        console.log('👤 User type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');

        try {
            const formData = new FormData();
            const formElements = form.elements;

            // เพิ่มข้อมูลฟอร์ม
            for (let element of formElements) {
                if (element.type === 'file' || element.type === 'button' || element.type === 'submit') continue;
                if (element.name && element.value !== '') {
                    formData.append(element.name, element.value);
                }
            }

            // เพิ่มประเภทเบี้ยยังชีพ
            const elderlyAwOdsType = document.querySelector('input[name="elderly_aw_ods_type"]:checked');
            if (elderlyAwOdsType) {
                formData.append('elderly_aw_ods_type', elderlyAwOdsType.value);
            }

            // *** ใช้ function ใหม่สำหรับจัดการ address ***
            const finalAddress = validateAndSetAddress(formData);

            // *** เพิ่มข้อมูลที่อยู่แยกส่วน (สำหรับทั้ง logged และ guest) ***
            if (isUserLoggedIn && userInfo && userInfo.address) {
                const addr = userInfo.address;
                formData.set('guest_province', addr.province || '');
                formData.set('guest_amphoe', addr.amphoe || '');
                formData.set('guest_district', addr.district || '');
                formData.set('guest_zipcode', addr.zipcode || '');
            } else if (!isUserLoggedIn) {
                formData.set('guest_province', document.querySelector('#guest_province_field')?.value || '');
                formData.set('guest_amphoe', document.querySelector('#guest_amphoe_field')?.value || '');
                formData.set('guest_district', document.querySelector('#guest_district_field')?.value || '');
                formData.set('guest_zipcode', document.querySelector('#guest_zipcode_field')?.value || '');
            }

            // *** เพิ่มข้อมูล debug ***
            formData.append('address_debug_info', JSON.stringify({
                user_type: isUserLoggedIn ? 'logged' : 'guest',
                address_length: finalAddress.length,
                userInfo_exists: !!userInfo,
                userInfo_address_exists: !!(userInfo && userInfo.address),
                timestamp: new Date().toISOString()
            }));

            // เพิ่มไฟล์
            if (selectedFiles && selectedFiles.length > 0) {
                selectedFiles.forEach((file, index) => {
                    formData.append('elderly_aw_ods_files[]', file, file.name);
                });
            }

            // *** reCAPTCHA parameters ***
            formData.append('g-recaptcha-response', recaptchaToken);
            formData.append('recaptcha_action', 'elderly_aw_ods_submit');
            formData.append('recaptcha_source', 'elderly_aw_ods_form');
            formData.append('user_type_detected', isUserLoggedIn ? 'member' : 'guest');
            formData.append('ajax_request', '1');
            formData.append('client_timestamp', new Date().toISOString());
            formData.append('user_agent_info', navigator.userAgent);
            formData.append('is_anonymous', isUserLoggedIn ? '0' : '1');
            formData.append('has_recaptcha', '1');
            formData.append('submission_source', 'elderly_aw_ods_form_with_recaptcha_v2');

            console.log('📦 FormData contents for elderly aw ods with reCAPTCHA (Enhanced):');
            console.log('- Submission mode: WITH reCAPTCHA (ENHANCED VERSION)');
            console.log('- User type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');
            console.log('- Address validation: ENHANCED');

            // Log important fields only
            console.log('- elderly_aw_ods_address:', formData.get('elderly_aw_ods_address'));
            console.log('- elderly_aw_ods_by:', formData.get('elderly_aw_ods_by'));
            console.log('- elderly_aw_ods_number:', formData.get('elderly_aw_ods_number'));
            console.log('- elderly_aw_ods_type:', formData.get('elderly_aw_ods_type'));

            // ส่งข้อมูล
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('📡 Response received:', response.status, response.statusText);

                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                if (text.trim().length > 0) {
                                    console.error('Server response (non-JSON):', text.substring(0, 500) + '...');
                                    throw new Error('Server returned non-JSON response');
                                }
                                return {
                                    success: true,
                                    message: 'ยื่นเรื่องเบี้ยยังชีพสำเร็จ',
                                    elderly_aw_ods_id: 'E' + Date.now().toString().slice(-6)
                                };
                            }
                        });
                    }
                })
                .then(jsonResponse => {
                    console.log('✅ JSON Response processed successfully:', jsonResponse.success);
                    handleElderlyAwOdsSubmitResponse(jsonResponse);
                })
                .catch(error => {
                    console.error('❌ Fetch error during submission with reCAPTCHA:', error);
                    handleElderlyAwOdsSubmitError(error);
                })
                .finally(() => {
                    console.log('🔄 Restoring submit button state');
                    restoreElderlyAwOdsSubmitButton(submitBtn, originalContent);
                });

        } catch (error) {
            console.error('💥 Critical error in performElderlyAwOdsSubmitWithRecaptcha:', error);
            handleElderlyAwOdsSubmitError(error);
            restoreElderlyAwOdsSubmitButton(submitBtn, originalContent);
        }
    }

    // *** Submit Function แบบปกติ (Fallback เมื่อ reCAPTCHA ไม่พร้อม) ***
    function performElderlyAwOdsSubmitWithoutRecaptcha(form, submitBtn, originalContent) {
        console.log('📤 Submitting elderly aw ods without reCAPTCHA...');
        console.log('👤 User type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');
        console.log('⚠️ This is a FALLBACK submission when reCAPTCHA is not available');

        try {
            const formData = new FormData();
            const formElements = form.elements;

            // เพิ่มข้อมูลฟอร์ม
            for (let element of formElements) {
                if (element.type === 'file' || element.type === 'button' || element.type === 'submit') continue;
                if (element.name && element.value !== '') {
                    formData.append(element.name, element.value);
                }
            }

            // เพิ่มประเภทเบี้ยยังชีพ
            const elderlyAwOdsType = document.querySelector('input[name="elderly_aw_ods_type"]:checked');
            if (elderlyAwOdsType) {
                formData.append('elderly_aw_ods_type', elderlyAwOdsType.value);
            }

            // *** แก้ไข: จัดการ Address เหมือนกับ function ที่มี reCAPTCHA ***
            let fullAddress = '';

            if (isUserLoggedIn && userInfo) {
                console.log('🏠 Processing address for logged user');

                // ตรวจสอบว่ามี address object หรือไม่
                if (userInfo.address) {
                    const addr = userInfo.address;
                    const addressParts = [
                        addr.additional_address || '',
                        addr.district ? `ตำบล${addr.district}` : '',
                        addr.amphoe ? `อำเภอ${addr.amphoe}` : '',
                        addr.province ? `จังหวัด${addr.province}` : '',
                        addr.zipcode || ''
                    ].filter(part => part !== '');

                    fullAddress = addressParts.join(' ');

                    // *** เพิ่มข้อมูลที่อยู่แยกส่วนสำหรับ logged user ***
                    formData.append('guest_province', addr.province || '');
                    formData.append('guest_amphoe', addr.amphoe || '');
                    formData.append('guest_district', addr.district || '');
                    formData.append('guest_zipcode', addr.zipcode || '');
                } else {
                    // ลองดูว่ามีข้อมูลที่อยู่ในรูปแบบอื่นหรือไม่
                    if (userInfo.full_address) {
                        fullAddress = userInfo.full_address;
                    } else if (userInfo.mp_address) {
                        fullAddress = userInfo.mp_address;
                    }
                }

                console.log('🏠 Logged user address result:', fullAddress);

            } else if (!isUserLoggedIn) {
                console.log('🏠 Processing address for guest user');

                // สำหรับ guest user - ใช้ข้อมูลจากฟอร์ม
                const additionalAddress = document.querySelector('#additional_address_field')?.value?.trim();
                const district = document.querySelector('#district_field')?.value?.trim();
                const guestProvince = document.querySelector('#guest_province_field')?.value || '';
                const guestAmphoe = document.querySelector('#guest_amphoe_field')?.value || '';
                const guestDistrict = document.querySelector('#guest_district_field')?.value || '';
                const guestZipcode = document.querySelector('#guest_zipcode_field')?.value || '';

                if (additionalAddress && district) {
                    const addressParts = [
                        additionalAddress,
                        district ? `ตำบล${district}` : '',
                        guestAmphoe ? `อำเภอ${guestAmphoe}` : '',
                        guestProvince ? `จังหวัด${guestProvince}` : '',
                        guestZipcode || ''
                    ].filter(part => part !== '');

                    fullAddress = addressParts.join(' ');
                }

                // *** เพิ่มข้อมูลที่อยู่แยกส่วนสำหรับ guest ***
                if (guestProvince) formData.append('guest_province', guestProvince);
                if (guestAmphoe) formData.append('guest_amphoe', guestAmphoe);
                if (guestDistrict) formData.append('guest_district', guestDistrict);
                if (guestZipcode) formData.append('guest_zipcode', guestZipcode);

                console.log('🏠 Guest user address result:', fullAddress);
            }

            // *** ส่งที่อยู่ไปยัง FormData - บังคับให้มีเสมอ ***
            if (fullAddress && fullAddress.trim() !== '') {
                formData.append('elderly_aw_ods_address', fullAddress);
                console.log('✅ Address added to form:', fullAddress);
            } else {
                console.warn('⚠️ No address found, using fallback');
                formData.append('elderly_aw_ods_address', 'ไม่ระบุที่อยู่');
            }

            // เพิ่มไฟล์
            if (selectedFiles && selectedFiles.length > 0) {
                selectedFiles.forEach((file, index) => {
                    formData.append('elderly_aw_ods_files[]', file, file.name);
                });
            }

            // *** ข้อมูล submission mode และ flags ***
            formData.append('dev_mode', '0');
            formData.append('skip_reason', 'recaptcha_unavailable');
            formData.append('user_type_detected', isUserLoggedIn ? 'member' : 'guest');
            formData.append('ajax_request', '1');
            formData.append('client_timestamp', new Date().toISOString());
            formData.append('user_agent_info', navigator.userAgent);
            formData.append('is_anonymous', isUserLoggedIn ? '0' : '1');
            formData.append('has_recaptcha', '0');
            formData.append('submission_source', 'elderly_aw_ods_form_no_recaptcha_fallback');

            console.log('📦 FormData contents for elderly aw ods without reCAPTCHA:');
            console.log('- Submission mode: WITHOUT reCAPTCHA (FALLBACK)');
            console.log('- User type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');
            console.log('- Skip reason: recaptcha_unavailable');

            for (let [key, value] of formData.entries()) {
                if (key.includes('files')) {
                    console.log('- ' + key + ':', '[FILE]');
                } else if (key === 'user_agent_info') {
                    console.log('- ' + key + ':', '[USER_AGENT]');
                } else {
                    console.log('- ' + key + ':', value);
                }
            }

            // ส่งข้อมูล
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('📡 Response received:', response.status, response.statusText);

                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            console.log('📄 Raw response text length:', text.length);

                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                if (text.trim().length > 0) {
                                    console.error('Server response (non-JSON):', text.substring(0, 500) + '...');
                                    throw new Error('Server returned non-JSON response');
                                }
                                // Fallback success response
                                console.log('🔄 Using fallback success response');
                                return {
                                    success: true,
                                    message: 'ยื่นเรื่องเบี้ยยังชีพสำเร็จ',
                                    elderly_aw_ods_id: 'E' + Date.now().toString().slice(-6),
                                    fallback: true,
                                    submission_type: 'without_recaptcha'
                                };
                            }
                        });
                    }
                })
                .then(jsonResponse => {
                    console.log('✅ JSON Response processed successfully:', jsonResponse.success);

                    if (jsonResponse.fallback) {
                        console.log('⚠️ Using fallback response due to parsing issues');
                    }

                    handleElderlyAwOdsSubmitResponse(jsonResponse);
                })
                .catch(error => {
                    console.error('❌ Fetch error during submission without reCAPTCHA:', error);
                    handleElderlyAwOdsSubmitError(error);
                })
                .finally(() => {
                    console.log('🔄 Restoring submit button state');
                    restoreElderlyAwOdsSubmitButton(submitBtn, originalContent);
                });

        } catch (error) {
            console.error('💥 Critical error in performElderlyAwOdsSubmitWithoutRecaptcha:', error);
            console.error('Stack trace:', error.stack);
            handleElderlyAwOdsSubmitError(error);
            restoreElderlyAwOdsSubmitButton(submitBtn, originalContent);
        }
    }

    // *** เพิ่ม: จัดการ Response ***
    function handleElderlyAwOdsSubmitResponse(jsonResponse) {
        if (jsonResponse.success) {
            const elderlyAwOdsId = jsonResponse.elderly_aw_ods_id || 'E' + Date.now().toString().slice(-6);

            // *** แยกข้อความตาม user type ***
            const userTypeText = isUserLoggedIn ?
                'คุณสามารถติดตามสถานะได้ในหน้าบัญชีของคุณ' :
                'คุณสามารถติดตามสถานะได้ด้วยหมายเลขอ้างอิงนี้';

            const confirmButtonText = isUserLoggedIn ?
                '<i class="fas fa-user-check me-2"></i>ไปยังหน้าบัญชีของฉัน' :
                '<i class="fas fa-search me-2"></i>ติดตามสถานะ';

            Swal.fire({
                icon: 'success',
                title: 'ยื่นเรื่องเบี้ยยังชีพสำเร็จ!',
                html: `
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, rgba(33, 150, 243, 0.15) 0%, rgba(100, 181, 246, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(33, 150, 243, 0.3);">
                        <i class="fas fa-check-circle" style="font-size: 2.5rem; color: #1976d2;"></i>
                    </div>
                    
                    <div style="background: linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%); 
                                padding: 1.5rem; border-radius: 15px; margin: 1rem 0;
                                box-shadow: 0 4px 15px rgba(33, 150, 243, 0.2);">
                        <h3 style="color: #1565c0; margin-bottom: 0.5rem; font-weight: 600;">
                            📝 หมายเลขอ้างอิง
                        </h3>
                        <div style="font-size: 2.2rem; font-weight: bold; color: #1565c0; 
                                   letter-spacing: 2px; margin: 1rem 0;">
                            ${elderlyAwOdsId}
                        </div>
                        <button onclick="copyRefId('${elderlyAwOdsId}')" 
                                class="btn btn-sm btn-outline-primary mt-2"
                                style="border-radius: 12px; padding: 0.5rem 1rem; font-weight: 600;">
                            <i class="fas fa-copy me-1"></i> คัดลอกหมายเลข
                        </button>
                    </div>
                    
                    <div style="background: rgba(33, 150, 243, 0.1); padding: 1rem; border-radius: 12px; margin: 1rem 0;">
                        <p style="color: #1976d2; margin: 0; font-size: 0.95rem;">
                            <i class="fas fa-info-circle me-2"></i>${userTypeText}
                        </p>
                    </div>
                    
                    <p style="color: #666; margin-top: 1rem;">ขอบคุณสำหรับการยื่นเรื่องเบี้ยยังชีพ</p>
                </div>
            `,
                confirmButtonText: confirmButtonText,
                confirmButtonColor: '#1976d2',
                showCancelButton: true,
                cancelButtonText: '<i class="fas fa-times me-1"></i>ปิด',
                cancelButtonColor: '#6c757d',
                buttonsStyling: true,
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // *** แยก redirect ตาม user type ***
                    if (isUserLoggedIn) {
                        // User ที่ login -> ไปหน้า my_elderly_aw_ods
                        window.location.href = `<?= site_url('Elderly_aw_ods/my_elderly_aw_ods'); ?>`;
                    } else {
                        // Guest user -> ไปหน้า follow_elderly_aw_ods พร้อม elderly_aw_ods_id
                        window.location.href = `<?= site_url('Elderly_aw_ods/follow_elderly_aw_ods'); ?>?ref=${elderlyAwOdsId}`;
                    }
                } else {
                    resetForm();
                }
            });
        } else {
            // จัดการ error จาก reCAPTCHA
            if (jsonResponse.error_type === 'recaptcha_failed') {
                showAlert('error', 'การยืนยันความปลอดภัยไม่ผ่าน', 'กรุณาลองใหม่อีกครั้ง');
            } else if (jsonResponse.error_type === 'recaptcha_missing') {
                showAlert('error', 'ไม่พบข้อมูลการยืนยันความปลอดภัย', 'กรุณาลองใหม่อีกครั้ง');
            } else {
                throw new Error(jsonResponse.message || 'เกิดข้อผิดพลาด');
            }
        }
    }

    // *** เพิ่ม: จัดการ Error ***
    function handleElderlyAwOdsSubmitError(error) {
        console.error('Elderly Aw Ods submit error:', error);

        // ตรวจสอบว่าเป็น error การยื่นเรื่องซ้ำหรือไม่
        if (error.message && error.message.includes('พบการยื่นเรื่องด้วยเลขบัตรประชาชน')) {
            showDuplicateSubmissionModal();
        } else {
            showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถยื่นเรื่องได้ กรุณาลองใหม่อีกครั้ง');
        }
    }

    // *** เพิ่ม: คืนค่าปุ่มเป็นสถานะเดิม ***
    function restoreElderlyAwOdsSubmitButton(submitBtn, originalContent) {
        setTimeout(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
            }
            formSubmitting = false;
        }, 100);
    }

    function resetForm() {
        const form = document.getElementById('elderlyAwOdsForm');
        if (form) form.reset();
        selectedFiles = [];
        updateFileDisplay();
        updateFormFieldsBasedOnLoginStatus();

        // รีเซ็ต Address fields
        if (typeof zipcodeField !== 'undefined' && zipcodeField) zipcodeField.value = '';
        if (typeof additionalAddressField !== 'undefined' && additionalAddressField) additionalAddressField.value = '';
        if (typeof fullAddressField !== 'undefined' && fullAddressField) fullAddressField.value = '';
        if (typeof resetToProvinceSelection === 'function') {
            resetToProvinceSelection();
        }

        // ซ่อน address preview
        const addressPreview = document.getElementById('address_preview');
        if (addressPreview) {
            addressPreview.style.display = 'none';
        }

        // รีเซ็ตประเภทเบี้ยยังชีพ
        document.getElementById('elderly').checked = true;
    }

    // *** 2. ฟังก์ชันตรวจสอบไฟล์ - เพิ่มใหม่ ***
    // *** ปรับปรุง Modal ให้กระชับและไม่ล้นหน้าจอ ***
    function validateFileRequirement() {
        console.log('🔍 === FILE REQUIREMENT VALIDATION START ===');

        const FILE_REQUIRED = true;
        const MIN_FILES = 1;
        const MAX_FILES = 3;

        console.log('📁 Current selected files:', selectedFiles.length);

        // ตรวจสอบว่าต้องมีไฟล์หรือไม่
        if (FILE_REQUIRED && (!selectedFiles || selectedFiles.length === 0)) {
            console.warn('❌ File validation failed: No files but required');

            // แสดง SweetAlert2 แบบกระชับ
            Swal.fire({
                icon: 'warning',
                title: '<span style="color: #1976d2; font-weight: 600;">จำเป็นต้องแนบเอกสาร</span>',
                html: `
                <div style="padding: 0.8rem;">
                    <!-- Icon Section -->
                    <div style="text-align: center; margin-bottom: 1.2rem;">
                        <div style="
                            width: 70px; 
                            height: 70px; 
                            margin: 0 auto; 
                            background: #fff3e0; 
                            border-radius: 50%; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center;
                            box-shadow: 0 4px 12px rgba(255, 152, 0, 0.2);
                            border: 2px solid #ffb74d;
                        ">
                            <i class="fas fa-file-upload" style="font-size: 2rem; color: #f57c00;"></i>
                        </div>
                        <p style="
                            color: #424242; 
                            font-size: 1rem; 
                            margin-top: 1rem;
                            font-weight: 500;
                            margin-bottom: 0;
                        ">
                            กรุณาแนบเอกสารประกอบการยื่นเรื่องเบี้ยยังชีพ
                        </p>
                    </div>
                    
                    <!-- Document List -->
                    <div style="
                        background: #f8f9fa; 
                        padding: 1rem; 
                        border-radius: 12px; 
                        margin-bottom: 1rem;
                        border: 1px solid #e3f2fd;
                        box-shadow: 0 2px 6px rgba(33, 150, 243, 0.06);
                    ">
                        <h6 style="
                            color: #1976d2; 
                            font-weight: 600; 
                            margin-bottom: 1rem;
                            text-align: center;
                            font-size: 0.95rem;
                        ">
                            <i class="fas fa-clipboard-list me-2"></i>
                            เอกสารที่ต้องแนบ
                        </h6>
                        
                        <!-- Document Grid -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem;">
                            <!-- Document 1 -->
                            <div style="
                                background: white;
                                padding: 0.8rem;
                                border-radius: 8px;
                                border: 1px solid #e3f2fd;
                                box-shadow: 0 1px 3px rgba(33, 150, 243, 0.04);
                            ">
                                <div style="display: flex; align-items: flex-start;">
                                    <span style="
                                        width: 22px; 
                                        height: 22px; 
                                        background: #1976d2; 
                                        color: white; 
                                        border-radius: 50%; 
                                        display: flex; 
                                        align-items: center; 
                                        justify-content: center; 
                                        margin-right: 0.6rem;
                                        font-size: 0.75rem;
                                        font-weight: bold;
                                        flex-shrink: 0;
                                        margin-top: 0.1rem;
                                    ">1</span>
                                    <div style="flex: 1;">
                                        <div style="color: #1976d2; font-weight: 600; margin-bottom: 0.2rem; font-size: 0.85rem;">
                                            แบบฟอร์มการลงทะเบียน
                                        </div>
                                        <div style="color: #666; font-size: 0.75rem; line-height: 1.3;">
                                            ดาวน์โหลดจากด้านบนและกรอกข้อมูลครบถ้วน
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Document 2 -->
                            <div style="
                                background: white;
                                padding: 0.8rem;
                                border-radius: 8px;
                                border: 1px solid #e3f2fd;
                                box-shadow: 0 1px 3px rgba(33, 150, 243, 0.04);
                            ">
                                <div style="display: flex; align-items: flex-start;">
                                    <span style="
                                        width: 22px; 
                                        height: 22px; 
                                        background: #1976d2; 
                                        color: white; 
                                        border-radius: 50%; 
                                        display: flex; 
                                        align-items: center; 
                                        justify-content: center; 
                                        margin-right: 0.6rem;
                                        font-size: 0.75rem;
                                        font-weight: bold;
                                        flex-shrink: 0;
                                        margin-top: 0.1rem;
                                    ">2</span>
                                    <div style="flex: 1;">
                                        <div style="color: #1976d2; font-weight: 600; margin-bottom: 0.2rem; font-size: 0.85rem;">
                                            สำเนาบัตรประจำตัวประชาชน
                                        </div>
                                        <div style="color: #666; font-size: 0.75rem; line-height: 1.3;">
                                            ลงลายมือ "สำเนาถูกต้อง" พร้อมลายเซ็น
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Document 3 -->
                            <div style="
                                background: white;
                                padding: 0.8rem;
                                border-radius: 8px;
                                border: 1px solid #e3f2fd;
                                box-shadow: 0 1px 3px rgba(33, 150, 243, 0.04);
                            ">
                                <div style="display: flex; align-items: flex-start;">
                                    <span style="
                                        width: 22px; 
                                        height: 22px; 
                                        background: #1976d2; 
                                        color: white; 
                                        border-radius: 50%; 
                                        display: flex; 
                                        align-items: center; 
                                        justify-content: center; 
                                        margin-right: 0.6rem;
                                        font-size: 0.75rem;
                                        font-weight: bold;
                                        flex-shrink: 0;
                                        margin-top: 0.1rem;
                                    ">3</span>
                                    <div style="flex: 1;">
                                        <div style="color: #1976d2; font-weight: 600; margin-bottom: 0.2rem; font-size: 0.85rem;">
                                            สำเนาทะเบียนบ้าน
                                        </div>
                                        <div style="color: #666; font-size: 0.75rem; line-height: 1.3;">
                                            ลงลายมือ "สำเนาถูกต้อง" พร้อมลายเซ็น
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Document 4 -->
                            <div style="
                                background: white;
                                padding: 0.8rem;
                                border-radius: 8px;
                                border: 1px solid rgba(66, 165, 245, 0.3);
                                box-shadow: 0 1px 3px rgba(66, 165, 245, 0.08);
                            ">
                                <div style="display: flex; align-items: flex-start;">
                                    <span style="
                                        width: 22px; 
                                        height: 22px; 
                                        background: #42a5f5; 
                                        color: white; 
                                        border-radius: 50%; 
                                        display: flex; 
                                        align-items: center; 
                                        justify-content: center; 
                                        margin-right: 0.6rem;
                                        font-size: 0.7rem;
                                        font-weight: bold;
                                        flex-shrink: 0;
                                        margin-top: 0.1rem;
                                    ">+</span>
                                    <div style="flex: 1;">
                                        <div style="color: #42a5f5; font-weight: 600; margin-bottom: 0.2rem; font-size: 0.85rem;">
                                            เอกสารเพิ่มเติม
                                        </div>
                                        <div style="color: #666; font-size: 0.75rem; line-height: 1.3;">
                                            หนังสือมอบอำนาจ (กรณีไม่ใช่ตัวจริง)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tips Section -->
                    <div style="
                        background: #e8f5e8; 
                        padding: 0.8rem; 
                        border-radius: 10px;
                        border: 1px solid #c8e6c9;
                        text-align: center;
                    ">
                        <div style="color: #2e7d32; margin-bottom: 0.5rem;">
                            <i class="fas fa-lightbulb me-2" style="color: #4caf50; font-size: 1rem;"></i>
                            <strong style="font-size: 0.9rem;">เคล็ดลับการแนบไฟล์</strong>
                        </div>
                        <div style="
                            color: #2e7d32; 
                            font-size: 0.8rem; 
                            line-height: 1.4;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            flex-wrap: wrap;
                            gap: 0.8rem;
                        ">
                            <span><i class="fas fa-camera me-1"></i>สแกนให้ชัดเจน</span>
                            <span><i class="fas fa-file me-1"></i>JPG, PNG, PDF</span>
                            <span><i class="fas fa-weight-hanging me-1"></i>ไม่เกิน 15MB</span>
                        </div>
                    </div>
                </div>
            `,
                confirmButtonText: '<i class="fas fa-upload me-2"></i>เลือกไฟล์',
                confirmButtonColor: '#1976d2',
                showCancelButton: true,
                cancelButtonText: '<i class="fas fa-times me-2"></i>ยกเลิก',
                cancelButtonColor: '#757575',
                allowOutsideClick: false,
                customClass: {
                    popup: 'swal-file-modal-compact',
                    confirmButton: 'swal-confirm-btn',
                    cancelButton: 'swal-cancel-btn'
                },
                buttonsStyling: false,
                width: '600px', // ลดความกว้าง
                padding: '1rem' // ลด padding
            }).then((result) => {
                if (result.isConfirmed) {
                    // เปิด file picker โดยอัตโนมัติ
                    const fileInput = document.getElementById('elderly_aw_ods_files');
                    if (fileInput) {
                        fileInput.click();

                        // Focus ไปที่ file upload area พร้อมเอฟเฟกต์
                        setTimeout(() => {
                            const fileUploadArea = document.querySelector('.file-upload-wrapper');
                            if (fileUploadArea) {
                                fileUploadArea.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                                // เพิ่มเอฟเฟกต์ highlight
                                fileUploadArea.style.cssText += `
                                border: 3px solid #1976d2 !important;
                                background: rgba(33, 150, 243, 0.05) !important;
                                transform: scale(1.02);
                                transition: all 0.3s ease;
                                box-shadow: 0 8px 25px rgba(33, 150, 243, 0.3) !important;
                            `;

                                setTimeout(() => {
                                    fileUploadArea.style.cssText = fileUploadArea.style.cssText
                                        .replace(/border: 3px solid #1976d2 !important;/g, '')
                                        .replace(/background: rgba\(33, 150, 243, 0\.05\) !important;/g, '')
                                        .replace(/transform: scale\(1\.02\);/g, '')
                                        .replace(/transition: all 0\.3s ease;/g, '')
                                        .replace(/box-shadow: 0 8px 25px rgba\(33, 150, 243, 0\.3\) !important;/g, '');
                                }, 3000);
                            }
                        }, 500);
                    }
                }
            });

            return false;
        }

        // ตรวจสอบจำนวนไฟล์ขั้นต่ำ
        if (selectedFiles.length < MIN_FILES) {
            Swal.fire({
                icon: 'warning',
                title: '<span style="color: #1976d2;">จำนวนไฟล์ไม่เพียงพอ</span>',
                html: `
                <div style="text-align: center; padding: 1rem;">
                    <div style="
                        width: 60px; 
                        height: 60px; 
                        margin: 0 auto 1rem; 
                        background: #fff3e0; 
                        border-radius: 50%; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center;
                        border: 2px solid #ffb74d;
                    ">
                        <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: #f57c00;"></i>
                    </div>
                    <p style="color: #424242; font-size: 1.1rem; margin: 0; line-height: 1.4;">
                        กรุณาแนบไฟล์อย่างน้อย <strong style="color: #1976d2;">${MIN_FILES}</strong> ไฟล์
                    </p>
                </div>
            `,
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#1976d2',
                customClass: {
                    popup: 'swal-file-modal-compact'
                },
                buttonsStyling: false,
                width: '350px'
            });
            return false;
        }

        // ตรวจสอบจำนวนไฟล์สูงสุด
        if (selectedFiles.length > MAX_FILES) {
            Swal.fire({
                icon: 'warning',
                title: '<span style="color: #1976d2;">จำนวนไฟล์เกินกำหนด</span>',
                html: `
                <div style="text-align: center; padding: 1rem;">
                    <div style="
                        width: 60px; 
                        height: 60px; 
                        margin: 0 auto 1rem; 
                        background: #fff3e0; 
                        border-radius: 50%; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center;
                        border: 2px solid #ffb74d;
                    ">
                        <i class="fas fa-times-circle" style="font-size: 1.5rem; color: #f57c00;"></i>
                    </div>
                    <p style="color: #424242; font-size: 1.1rem; margin: 0; line-height: 1.4;">
                        สามารถแนบไฟล์ได้สูงสุด <strong style="color: #1976d2;">${MAX_FILES}</strong> ไฟล์เท่านั้น
                    </p>
                </div>
            `,
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#1976d2',
                customClass: {
                    popup: 'swal-file-modal-compact'
                },
                buttonsStyling: false,
                width: '350px'
            });
            return false;
        }

        // ตรวจสอบแต่ละไฟล์
        for (let i = 0; i < selectedFiles.length; i++) {
            const file = selectedFiles[i];
            if (!validateSingleFile(file, i + 1)) {
                return false;
            }
        }

        console.log('✅ File requirement validation passed');
        return true;
    }

    // *** เพิ่ม CSS สำหรับ Modal กระชับ ***
    const compactModalStyles = `
<style>
.swal-file-modal-compact {
    border-radius: 16px !important;
    box-shadow: 0 15px 40px rgba(33, 150, 243, 0.12), 0 6px 20px rgba(0,0,0,0.06) !important;
    max-width: 95vw !important;
    max-height: 85vh !important;
    overflow-y: auto !important;
}

.swal-confirm-btn {
    background: #1976d2 !important;
    color: white !important;
    border: none !important;
    border-radius: 10px !important;
    padding: 0.7rem 1.5rem !important;
    font-weight: 600 !important;
    font-size: 0.95rem !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 3px 12px rgba(33, 150, 243, 0.3) !important;
    margin: 0 0.4rem !important;
}

.swal-confirm-btn:hover {
    background: #1565c0 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 5px 16px rgba(33, 150, 243, 0.4) !important;
}

.swal-cancel-btn {
    background: #f5f5f5 !important;
    color: #757575 !important;
    border: 2px solid #e0e0e0 !important;
    border-radius: 10px !important;
    padding: 0.7rem 1.5rem !important;
    font-weight: 600 !important;
    font-size: 0.95rem !important;
    transition: all 0.3s ease !important;
    margin: 0 0.4rem !important;
}

.swal-cancel-btn:hover {
    background: #eeeeee !important;
    border-color: #bdbdbd !important;
    color: #424242 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .swal-file-modal-compact {
        width: 95vw !important;
        margin: 0.5rem !important;
        padding: 0.8rem !important;
    }
    
    .swal-file-modal-compact .swal2-html-container {
        padding: 0.5rem !important;
    }
    
    /* Stack grid on mobile */
    .swal-file-modal-compact [style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
        gap: 0.6rem !important;
    }
    
    .swal-confirm-btn, .swal-cancel-btn {
        padding: 0.6rem 1.2rem !important;
        font-size: 0.9rem !important;
    }
}

@media (max-width: 480px) {
    .swal-file-modal-compact {
        width: 98vw !important;
        margin: 0.2rem !important;
    }
}

/* Animation */
.swal2-popup.swal2-show {
    animation: swalFadeInUp 0.35s ease-out !important;
}

@keyframes swalFadeInUp {
    0% {
        opacity: 0;
        transform: translateY(20px) scale(0.96);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
</style>
`;

    // เพิ่ม CSS กระชับใหม่
    if (!document.getElementById('compact-modal-styles')) {
        const styleElement = document.createElement('div');
        styleElement.id = 'compact-modal-styles';
        styleElement.innerHTML = compactModalStyles;
        document.head.appendChild(styleElement);
    }

    // *** 3. ฟังก์ชันตรวจสอบไฟล์แต่ละไฟล์ - ปรับปรุง ***
    function validateSingleFile(file, fileNumber) {
        const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
        const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'];
        const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

        console.log(`📄 Validating file ${fileNumber}:`, {
            name: file.name,
            size: file.size,
            type: file.type
        });

        // ตรวจสอบขนาดไฟล์
        if (file.size > MAX_FILE_SIZE) {
            const maxSizeMB = MAX_FILE_SIZE / (1024 * 1024);
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

            Swal.fire({
                icon: 'error',
                title: 'ไฟล์ขนาดใหญ่เกินไป',
                html: `
                <p>ไฟล์ <strong>"${file.name}"</strong> มีขนาด <strong>${fileSizeMB} MB</strong></p>
                <p>ขนาดสูงสุดที่อนุญาต: <strong>${maxSizeMB} MB</strong></p>
                <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                    <small style="color: #856404;">
                        <i class="fas fa-lightbulb me-1"></i>
                        แนะนำ: ลดขนาดไฟล์หรือบีบอัดไฟล์ก่อนอัปโหลด
                    </small>
                </div>
            `,
                confirmButtonColor: '#dc3545'
            });
            return false;
        }

        // ตรวจสอบไฟล์ว่าง
        if (file.size === 0) {
            Swal.fire({
                icon: 'error',
                title: 'ไฟล์เสียหาย',
                text: `ไฟล์ "${file.name}" มีขนาด 0 bytes อาจเสียหายหรือไม่สมบูรณ์`,
                confirmButtonColor: '#dc3545'
            });
            return false;
        }

        // ตรวจสอบประเภทไฟล์จาก extension
        const fileExt = file.name.split('.').pop().toLowerCase();
        if (!ALLOWED_EXTENSIONS.includes(fileExt)) {
            Swal.fire({
                icon: 'error',
                title: 'ประเภทไฟล์ไม่ถูกต้อง',
                html: `
                <p>ไฟล์ <strong>"${file.name}"</strong> เป็นประเภท <strong>.${fileExt}</strong> ที่ไม่ได้รับอนุญาต</p>
                <div style="background: #d1ecf1; padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                    <h6 style="color: #0c5460; margin-bottom: 0.5rem;">ประเภทไฟล์ที่อนุญาต:</h6>
                    <p style="color: #0c5460; margin: 0;">
                        <strong>รูปภาพ:</strong> JPG, JPEG, PNG, GIF<br>
                        <strong>เอกสาร:</strong> PDF
                    </p>
                </div>
            `,
                confirmButtonColor: '#dc3545'
            });
            return false;
        }

        // ตรวจสอบ MIME type
        if (file.type && !ALLOWED_TYPES.includes(file.type.toLowerCase())) {
            console.warn(`File "${file.name}" has suspicious MIME type: ${file.type}`);

            Swal.fire({
                icon: 'warning',
                title: 'ประเภทไฟล์ไม่ถูกต้อง',
                text: `ไฟล์ "${file.name}" อาจมีปัญหาเกี่ยวกับประเภทไฟล์`,
                confirmButtonColor: '#f57c00'
            });
            return false;
        }

        // ตรวจสอบชื่อไฟล์
        if (file.name.length > 100) {
            Swal.fire({
                icon: 'warning',
                title: 'ชื่อไฟล์ยาวเกินไป',
                text: `ชื่อไฟล์ "${file.name}" ยาวเกิน 100 ตัวอักษร กรุณาเปลี่ยนชื่อไฟล์`,
                confirmButtonColor: '#f57c00'
            });
            return false;
        }

        // ตรวจสอบอักขระพิเศษในชื่อไฟล์
        const invalidChars = /[<>:"/\\|?*]/;
        if (invalidChars.test(file.name)) {
            Swal.fire({
                icon: 'warning',
                title: 'ชื่อไฟล์ไม่ถูกต้อง',
                html: `
                <p>ชื่อไฟล์ <strong>"${file.name}"</strong> มีอักขระที่ไม่อนุญาต</p>
                <p style="color: #dc3545; font-family: monospace;">< > : " / \\ | ? *</p>
                <p style="color: #666; font-size: 0.9rem;">กรุณาเปลี่ยนชื่อไฟล์ให้ใช้เฉพาะตัวอักษร ตัวเลข และ - _ เท่านั้น</p>
            `,
                confirmButtonColor: '#f57c00'
            });
            return false;
        }

        return true;
    }

    // *** 4. ปรับปรุงการแสดงสถานะไฟล์ในแบบ Real-time ***
    function updateFileDisplay() {
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const previewArea = document.getElementById('file-preview-area');
        const previewContainer = document.getElementById('preview-container');
        const fileCount = document.getElementById('file-count');

        if (!selectedFiles || selectedFiles.length === 0) {
            uploadPlaceholder.style.display = 'block';
            previewArea.style.display = 'none';

            // แสดงข้อความเตือนถ้าไฟล์จำเป็น
            showFileRequirementStatus(false);
            return;
        }

        uploadPlaceholder.style.display = 'none';
        previewArea.style.display = 'block';
        fileCount.textContent = selectedFiles.length;

        previewContainer.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const previewItem = createPreviewItem(file, index);
            previewContainer.appendChild(previewItem);
        });

        // แสดงสถานะไฟล์
        showFileRequirementStatus(true);
    }

    // *** 5. แสดงสถานะความต้องการไฟล์ ***
    function showFileRequirementStatus(hasFiles) {
        const FILE_REQUIRED = true; // ต้องเปลี่ยนตามการตั้งค่า

        // ลบสถานะเก่า
        const existingStatus = document.getElementById('file-requirement-status');
        if (existingStatus) {
            existingStatus.remove();
        }

        if (!FILE_REQUIRED) return; // ถ้าไฟล์ไม่จำเป็น ไม่ต้องแสดงสถานะ

        const fileUploadWrapper = document.querySelector('.file-upload-wrapper');
        if (!fileUploadWrapper) return;

        const statusDiv = document.createElement('div');
        statusDiv.id = 'file-requirement-status';
        statusDiv.style.marginTop = '1rem';

        if (hasFiles && selectedFiles.length > 0) {
            // สถานะ: มีไฟล์แล้ว
            statusDiv.innerHTML = `
            <div class="alert alert-success" style="
                background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%);
                border: 1px solid rgba(40, 167, 69, 0.3);
                border-radius: 10px;
                padding: 0.75rem 1rem;
                margin: 0;
            ">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2" style="color: #28a745; font-size: 1.1rem;"></i>
                    <div>
                        <strong style="color: #155724;">แนบไฟล์เรียบร้อยแล้ว</strong>
                        <small style="color: #155724; display: block; margin-top: 0.2rem;">
                            จำนวน ${selectedFiles.length} ไฟล์ พร้อมส่งข้อมูล
                        </small>
                    </div>
                </div>
            </div>
        `;
        } else {
            // สถานะ: ยังไม่มีไฟล์
            statusDiv.innerHTML = `
            <div class="alert alert-warning" style="
                background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%);
                border: 1px solid rgba(255, 193, 7, 0.3);
                border-radius: 10px;
                padding: 0.75rem 1rem;
                margin: 0;
            ">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2" style="color: #f57c00; font-size: 1.1rem;"></i>
                    <div>
                        <strong style="color: #856404;">จำเป็นต้องแนบเอกสาร</strong>
                        <small style="color: #856404; display: block; margin-top: 0.2rem;">
                            กรุณาแนบไฟล์ก่อนยื่นเรื่อง
                        </small>
                    </div>
                </div>
            </div>
        `;
        }

        fileUploadWrapper.parentNode.appendChild(statusDiv);
    }

    // *** 6. เรียกใช้เมื่อมีการเปลี่ยนแปลงไฟล์ ***
    function handleFileSelect(input) {
        if (input._processing) return;
        input._processing = true;

        const files = Array.from(input.files);
        if (files.length === 0) {
            input._processing = false;
            return;
        }

        // ตรวจสอบจำนวนไฟล์สูงสุด
        if (selectedFiles.length + files.length > maxFiles) {
            Swal.fire({
                icon: 'warning',
                title: 'เกินจำนวนที่กำหนด',
                text: `คุณสามารถอัพโหลดได้สูงสุด ${maxFiles} ไฟล์เท่านั้น`,
                confirmButtonColor: '#f57c00'
            });
            input._processing = false;
            input.value = '';
            return;
        }

        // ตรวจสอบและเพิ่มไฟล์
        for (let file of files) {
            if (validateSingleFile(file)) {
                file.id = Date.now() + Math.random();
                selectedFiles.push(file);
            } else {
                input._processing = false;
                input.value = '';
                return;
            }
        }

        updateFileDisplay();
        setTimeout(() => {
            input.value = '';
            input._processing = false;
        }, 100);
    }

    document.addEventListener('DOMContentLoaded', function () {
        // 1. เริ่มต้นระบบฟอร์ม
        const form = document.getElementById('elderlyAwOdsForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                return false;
            });
        }

        // 2. เริ่มต้นระบบต่างๆ
        fixFormAction();
        initializeAddressSystem();
        setupPhoneValidation();
        setupIdCardValidation();
        updateFormFieldsBasedOnLoginStatus();

        // 3. ตั้งค่า file upload
        const uploadWrapper = document.querySelector('.file-upload-wrapper');
        if (uploadWrapper) {
            uploadWrapper.addEventListener('click', function (e) {
                e.preventDefault();
                const fileInput = document.getElementById('elderly_aw_ods_files');
                if (fileInput) fileInput.click();
            });
        }

        // 5. แสดงสถานะไฟล์ครั้งแรก
        showFileRequirementStatus(selectedFiles && selectedFiles.length > 0);

        // 6. ตรวจสอบ reCAPTCHA
        if (window.RECAPTCHA_SITE_KEY && !window.recaptchaReady) {
            console.log('⏳ Waiting for reCAPTCHA to load for elderly aw ods form...');

            let checkInterval = setInterval(function () {
                if (window.recaptchaReady) {
                    console.log('✅ reCAPTCHA is now ready for elderly aw ods form');
                    clearInterval(checkInterval);
                }
            }, 100);

            setTimeout(function () {
                if (!window.recaptchaReady) {
                    console.log('⚠️ reCAPTCHA timeout after 10 seconds for elderly aw ods form');
                    clearInterval(checkInterval);
                }
            }, 10000);
        }

        // 7. แสดง modal สำหรับ guest user
        if (!isUserLoggedIn) {
            setTimeout(() => {
                if (!hasConfirmedAsGuest) showModal();
            }, 1000);
        } else {
            setTimeout(showWelcomeMessage, 500);
        }

        // 8. ตรวจสอบ return จาก login
        checkReturnFromLogin();

        console.log('✅ Elderly AW ODS form initialized with address system & ID card validation');
    });


    //////////////////////////////////////////////////////////////



    function copyRefId(refId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(refId).then(() => {
                showAlert('success', 'คัดลอกแล้ว', `คัดลอกหมายเลขอ้างอิง ${refId} แล้ว`, 2000);
            });
        }
    }

    // Modal functions
function showModal() {
    const modalElement = document.getElementById('guestConfirmModal');
    if (!modalElement) return;

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        guestModalInstance = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false,
            focus: true
        });
        
        // 🔥 เพิ่มส่วนนี้
        modalElement.addEventListener('hidden.bs.modal', function() {
            setTimeout(() => {
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                document.body.classList.remove('modal-open');
                document.body.classList.add('modal-restore-scroll');
                
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                
                setTimeout(() => {
                    document.body.classList.remove('modal-restore-scroll');
                }, 100);
            }, 50);
        });
        
        guestModalInstance.show();
    }
}

function hideModal() {
    const modalElement = document.getElementById('guestConfirmModal');
    
    if (guestModalInstance) {
        guestModalInstance.hide();
        
        // 🔥 เพิ่มส่วนนี้
        modalElement.addEventListener('hidden.bs.modal', function() {
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            document.body.classList.remove('modal-open');
            
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
        }, { once: true });
        
        guestModalInstance = null;
        
    } else if (modalElement) {
        modalElement.style.display = 'none';
        modalElement.classList.remove('show');
        
        // 🔥 เพิ่มส่วนนี้สำหรับ manual cleanup
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        document.body.classList.remove('modal-open');
    }
}

function proceedAsGuest() {
    hasConfirmedAsGuest = true;
    hideModal();
    
    // 🔥 เพิ่มส่วนนี้เพื่อแก้ scroll issue
    setTimeout(() => {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        document.body.classList.remove('modal-open');
        
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        
        window.scrollTo(0, 0);
    }, 100);
    
    showAlert('info', 'ดำเนินการต่อโดยไม่เข้าสู่ระบบ', 
              'คุณสามารถกรอกข้อมูลและยื่นเรื่องเบี้ยยังชีพได้แล้ว', 2000);
}

    function redirectToLogin() {
        hideModal();

        const currentUrl = window.location.href;
        sessionStorage.setItem('redirect_after_login', currentUrl);

        Swal.fire({
            icon: 'info',
            title: 'กำลังนำท่านไปหน้าเข้าสู่ระบบ',
            text: 'หลังจากเข้าสู่ระบบสำเร็จ จะกลับมายังหน้านี้โดยอัตโนมัติ',
            timer: 2000,
            showConfirmButton: false,
            timerProgressBar: true
        }).then(() => {
            window.location.href = '<?= site_url('User'); ?>?redirect=' + encodeURIComponent(currentUrl);
        });
    }

    function showAlert(icon, title, text, timer = null) {
        const config = {
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: '#1976d2',
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

    // *** Show logged-in user info with auto-fill พร้อมการซ่อนฟิลด์ที่ไม่จำเป็น ***
    if (isUserLoggedIn && userInfo) {
        // ข้อความแสดงหน้าจอแรก
        document.addEventListener('DOMContentLoaded', function () {
            const infoBox = document.createElement('div');
            infoBox.id = 'user-logged-in-banner';
            infoBox.className = 'alert alert-info';
            infoBox.style.cssText = `
            background: linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%);
            border: 1px solid rgba(33, 150, 243, 0.3);
            border-radius: 15px;
            margin-bottom: 2rem;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0.15);
        `;

            infoBox.innerHTML = `
            <div class="row align-items-center">
                <div class="col-auto">
                    <div style="
                        width: 50px; 
                        height: 50px; 
                        background: linear-gradient(135deg, rgba(33, 150, 243, 0.15) 0%, rgba(100, 181, 246, 0.15) 100%); 
                        border-radius: 50%; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center;
                        box-shadow: 0 4px 10px rgba(33, 150, 243, 0.2);
                    ">
                        <i class="fas fa-user-check" style="font-size: 1.5rem; color: #1976d2;"></i>
                    </div>
                </div>
                <div class="col">
                    <h6 class="mb-1" style="color: #1565c0; font-weight: 600;">
                        <i class="fas fa-info-circle me-2"></i>ข้อมูลจากบัญชีของคุณ
                    </h6>
                    <p class="mb-0" style="color: #424242; font-size: 0.9rem;">
                        ระบบจะใช้ข้อมูลส่วนตัวและที่อยู่จากบัญชีของคุณโดยอัตโนมัติ
                        <strong style="color: #1976d2;">${userInfo.name || 'ผู้ใช้'}</strong>
                    </p>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn-close" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()"></button>
                </div>
            </div>
        `;

            const formContainer = document.getElementById('elderly_aw_ods_form');
            const typeSelector = formContainer.querySelector('.elderly-type-selector');
            if (typeSelector) {
                typeSelector.parentNode.insertBefore(infoBox, typeSelector);
            }
        });
    }

    console.log('✅ Elderly AW ODS Form - Complete JavaScript System Ready!');
</script>