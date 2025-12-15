<div class="text-center pages-head">
    <span class="font-pages-head">เงินอุดหนุนเด็กแรกเกิด</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<!-- Modal สำหรับการยืนยันการยื่นเรื่องโดยไม่เข้าสู่ระบบ -->
<div class="modal fade" id="guestConfirmModal" tabindex="-1" aria-labelledby="guestConfirmModalLabel" aria-hidden="true"
    style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content kid-modal-content"
            style="border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(76, 175, 80, 0.2), 0 8px 25px rgba(0,0,0,0.08); background: linear-gradient(135deg, #ffffff 0%, #e8f5e8 100%); overflow: hidden;">
            <div class="modal-header kid-modal-header"
                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(129, 199, 132, 0.1) 100%); color: #388e3c; border-radius: 20px 20px 0 0; border-bottom: 1px solid rgba(76, 175, 80, 0.2); backdrop-filter: blur(10px);">
                <h5 class="modal-title kid-modal-title" id="guestConfirmModalLabel"
                    style="font-weight: 600; color: #388e3c; width: 100%; text-align: center;">
                    <i class="fas fa-baby me-2"
                        style="color: #388e3c;"></i>ยินดีต้อนรับสู่ระบบยื่นเรื่องเงินสนับสนุนเด็กแรกเกิด
                </h5>
            </div>
            <div class="modal-body kid-modal-body text-center"
                style="padding: 2.5rem; background: linear-gradient(135deg, #ffffff 0%, #e8f5e8 100%);">
                <div class="mb-4">
                    <div
                        style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(76, 175, 80, 0.15) 0%, rgba(129, 199, 132, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);">
                        <i class="fas fa-user-shield"
                            style="font-size: 2.5rem; color: #388e3c; text-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);"></i>
                    </div>
                </div>
                <h5 class="mb-3" style="color: #388e3c; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    เริ่มต้นการใช้งาน</h5>
                <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.6; color: #424242;">
                    เข้าสู่ระบบเพื่อติดตามสถานะการยื่นเรื่องและได้รับการแจ้งเตือน สะดวกรวดเร็ว ปลอดภัย ประหยัดเวลา
                    ไม่ต้องกรอกข้อมูลซ้ำ
                    ไม่มีใครค้นหาข้อมูลของคุณได้ หรือดำเนินการต่อโดยไม่ต้องเข้าสู่ระบบ
                    ไม่ปลอดภัยบุคคลอื่นสามารถค้นหาข้อมูลได้จากหน้าติดตาม</p>

                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-lg kid-login-btn" onclick="redirectToLogin()"
                        style="background: linear-gradient(135deg, #388e3c 0%, #2e7d32 100%); border: none; color: white; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4); transition: all 0.3s ease; font-size: 1.1rem;">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                    </button>
                    <button type="button" class="btn btn-lg kid-guest-btn" onclick="proceedAsGuest()"
                        style="background: rgba(76, 175, 80, 0.08); border: 2px solid rgba(76, 175, 80, 0.3); color: #388e3c; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; transition: all 0.3s ease; font-size: 1.1rem; backdrop-filter: blur(10px);">
                        <i class="fas fa-paper-plane me-2"></i>ดำเนินการต่อโดยไม่เข้าสู่ระบบ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center pages-head">
    <span class="font-pages-head"
        style="font-size: 2.8rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(108, 117, 125, 0.2);">เงินสนับสนุนเด็กแรกเกิด</span>
</div>

<div class="bg-pages" style="background: #ffffff; min-height: 100vh; padding: 2rem 0;">
    <!-- *** เพิ่มส่วนดาวน์โหลดแบบฟอร์ม *** -->
    <div class="container-pages-news mb-4"
        style="position: relative; z-index: 10; background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 1000px; overflow: hidden;">

        <!-- เพิ่ม decorative element -->
        <div
            style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #388e3c, #66bb6a, #388e3c); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;">
        </div>

        <div class="text-center mb-4">
            <h4 style="color: #388e3c; font-weight: 600; margin-bottom: 1rem;">
                <i class="fas fa-download me-2"></i>แบบฟอร์มเงินสนับสนุนเด็กแรกเกิด
            </h4>
            <p style="color: #666; font-size: 1rem;">กรุณาดาวน์โหลดแบบฟอร์มที่เหมาะสม กรอกข้อมูลให้ครบถ้วน
                แล้วนำมาแนบด้านล่าง</p>
        </div>

        <div class="row g-3">
            <?php if (!empty($kid_aw_form)): ?>
                <?php foreach ($kid_aw_form as $rs): ?>
                    <div class="col-md-4">
                        <div class="form-download-card"
                            style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.05) 0%, rgba(129, 199, 132, 0.05) 100%); border: 1px solid rgba(76, 175, 80, 0.2); border-radius: 15px; padding: 1.5rem; text-align: center; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.1);">
                            <div class="mb-3">
                                <i class="fas fa-file-pdf" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
                            </div>
                            <h6
                                style="color: #388e3c; font-weight: 600; margin-bottom: 1rem; min-height: 50px; display: flex; align-items: center; justify-content: center; text-align: center;">
                                <?= $rs->kid_aw_form_name; ?>
                            </h6>
                            <p style="font-size: 0.8rem; color: #666; margin-bottom: 1rem;">
                                ประเภท: <?= ucfirst($rs->kid_aw_form_type); ?>
                            </p>
                            <a href="<?= base_url('docs/file/' . $rs->kid_aw_form_file); ?>" download
                                class="btn btn-primary btn-download-form"
                                style="background: linear-gradient(135deg, #388e3c 0%, #2e7d32 100%); border: none; border-radius: 10px; padding: 0.7rem 1.5rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3); width: 100%;">
                                <i class="fas fa-download me-2"></i>ดาวน์โหลด
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- แบบฟอร์มตัวอย่างถ้าไม่มีข้อมูลจากฐานข้อมูล -->
                <div class="col-md-6">
                    <div class="form-download-card"
                        style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.05) 0%, rgba(129, 199, 132, 0.05) 100%); border: 1px solid rgba(76, 175, 80, 0.2); border-radius: 15px; padding: 1.5rem; text-align: center; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.1);">
                        <div class="mb-3">
                            <i class="fas fa-file-pdf" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
                        </div>
                        <h6
                            style="color: #388e3c; font-weight: 600; margin-bottom: 1rem; min-height: 50px; display: flex; align-items: center; justify-content: center; text-align: center;">
                            แบบฟอร์มการขอรับเงินสนับสนุนเด็กแรกเกิด
                        </h6>
                        <a href="#" class="btn btn-primary btn-download-form"
                            style="background: linear-gradient(135deg, #388e3c 0%, #2e7d32 100%); border: none; border-radius: 10px; padding: 0.7rem 1.5rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3); width: 100%;">
                            <i class="fas fa-download me-2"></i>ดาวน์โหลด
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-download-card"
                        style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.05) 0%, rgba(129, 199, 132, 0.05) 100%); border: 1px solid rgba(76, 175, 80, 0.2); border-radius: 15px; padding: 1.5rem; text-align: center; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.1);">
                        <div class="mb-3">
                            <i class="fas fa-file-pdf" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
                        </div>
                        <h6
                            style="color: #388e3c; font-weight: 600; margin-bottom: 1rem; min-height: 50px; display: flex; align-items: center; justify-content: center; text-align: center;">
                            แบบฟอร์มหนังสือมอบอำนาจ
                        </h6>
                        <a href="#" class="btn btn-primary btn-download-form"
                            style="background: linear-gradient(135deg, #388e3c 0%, #2e7d32 100%); border: none; border-radius: 10px; padding: 0.7rem 1.5rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3); width: 100%;">
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
                        <li>ดาวน์โหลดแบบฟอร์มที่เหมาะสมกับประเภทเงินสนับสนุนที่ต้องการสมัคร</li>
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
        id="kid_aw_ods_form">

        <!-- เพิ่ม decorative element -->
        <div
            style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #388e3c, #66bb6a, #388e3c); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;">
        </div>

        <!-- ปุ่มติดตามสถานะ -->
        <div class="d-flex justify-content-end mb-3">
            <button type="button" onclick="redirectToTrackStatus()" class="btn track-status-btn" style="background: linear-gradient(135deg, #388e3c 0%, #2e7d32 100%); 
                           border: none; 
                           color: white; 
                           padding: 0.7rem 1.5rem; 
                           border-radius: 12px; 
                           font-size: 0.95rem; 
                           font-weight: 600; 
                           transition: all 0.3s ease; 
                           box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3); 
                           position: relative;
                           overflow: hidden;
                           cursor: pointer;">
                <span style="position: relative; z-index: 2;">
                    <i class="fas fa-search me-2"></i>ติดตามสถานะเงินสนับสนุนเด็ก
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
                    // User ที่ login (public หรือ staff) → ไปหน้า my_kid_aw_ods
                    window.location.href = '<?= site_url('Kid_aw_ods/my_kid_aw_ods'); ?>';
                } else {
                    // Guest user → ไปหน้า follow_kid_aw_ods
                    window.location.href = '<?= site_url('Kid_aw_ods/follow_kid_aw_ods'); ?>';
                }
            }

            // *** เพิ่ม hover effect ***
            document.addEventListener('DOMContentLoaded', function () {
                const trackBtn = document.querySelector('.track-status-btn');
                if (trackBtn) {
                    trackBtn.addEventListener('mouseenter', function () {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 6px 20px rgba(76, 175, 80, 0.4)';
                        this.style.background = 'linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%)';
                    });

                    trackBtn.addEventListener('mouseleave', function () {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = '0 4px 15px rgba(76, 175, 80, 0.3)';
                        this.style.background = 'linear-gradient(135deg, #388e3c 0%, #2e7d32 100%)';
                    });
                }
            });
        </script>

        <!-- ประเภทเงินสนับสนุนเด็ก -->
        <div class="kid-type-selector"
            style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 15px; padding: 1rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15);">
            <div class="form-label-wrapper"
                style="margin-bottom: 1rem; background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                <label class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                    <i class="fas fa-baby me-2" style="color: #388e3c;"></i>ประเภทเงินสนับสนุนเด็ก
                </label>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check p-3 kid-form-check"
                        style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                        <input class="form-check-input" type="radio" name="kid_aw_ods_type" id="children"
                            value="children" checked style="transform: scale(1.2); margin: 0;">
                        <label class="form-check-label fw-bold" for="children"
                            style="color: #424242; margin-left: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-baby me-2" style="color: #4caf50;"></i>เด็กแรกเกิด
                        </label>
                    </div>
                </div>
                <!--  <div class="col-md-6">
                    <div class="form-check p-3 kid-form-check" style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                        <input class="form-check-input" type="radio" name="kid_aw_ods_type" id="disabled" value="disabled" style="transform: scale(1.2); margin: 0;">
                        <label class="form-check-label fw-bold" for="disabled" style="color: #424242; margin-left: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-wheelchair me-2" style="color: #ff9800;"></i>เด็กพิการ
                        </label>
                    </div>
                </div> -->
            </div>
        </div>

        <div class="underline">
            <form id="kidAwOdsForm" action="<?php echo site_url('Kid_aw_ods/add_kid_aw_ods'); ?>" method="post"
                class="form-horizontal" enctype="multipart/form-data" onsubmit="return false;">
                <input type="hidden" name="form_token" id="formToken" value="">
                <br>

                <div class="row">
                    <!-- ชื่อ-นามสกุล (ผู้ปกครอง/ผู้รับผิดชอบ) -->
                    <div class="col-md-5">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                                    <i class="fas fa-user me-2" style="color: #388e3c;"></i>ชื่อ-นามสกุล ผู้ปกครอง<span
                                        style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="text" name="kid_aw_ods_by" id="kid_aw_ods_by" class="form-control" required
                                    placeholder="เช่น นางสาว สมหญิง ใจดี"
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="kid_aw_ods_by_feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- เบอร์โทรศัพท์ -->
                    <div class="col-md-3">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                                    <i class="fas fa-phone me-2" style="color: #388e3c;"></i>เบอร์โทรศัพท์<span
                                        style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="tel" id="kid_aw_ods_phone" name="kid_aw_ods_phone" class="form-control"
                                    required placeholder="เช่น 0812345678" pattern="\d{10}"
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="kid_aw_ods_phone_feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- อีเมล -->
                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                                    <i class="fas fa-envelope me-2" style="color: #388e3c;"></i>อีเมล<small
                                        style="color: #6c757d; font-weight: 400; margin-left: 0.5rem;">(Optional)</small>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="email" name="kid_aw_ods_email" class="form-control"
                                    placeholder="example@youremail.com"
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="kid_aw_ods_email_feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- เลขบัตรประชาชน -->
                <div class="form-group mb-4">
                    <div class="form-label-wrapper"
                        style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                        <label class="form-label"
                            style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                            <i class="fas fa-id-card me-2" style="color: #388e3c;"></i>เลขบัตรประจำตัวประชาชน
                            ผู้ปกครอง<span style="color: #dc3545; margin-left: 0.2rem;">*</span>
                        </label>
                    </div>
                    <div class="col-sm-12">
                        <input type="text" name="kid_aw_ods_number" id="kid_aw_ods_number" class="form-control" required
                            placeholder="เลขบัตรประจำตัวประชาชน 13 หลัก" maxlength="13"
                            style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                        <div class="invalid-feedback" id="kid_aw_ods_number_feedback"></div>
                    </div>
                </div>

                <!-- *** ระบบที่อยู่แบบละเอียด *** -->
                <div class="form-group mb-4">
                    <div class="form-label-wrapper"
                        style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                        <label class="form-label"
                            style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                            <i class="fas fa-map-marker-alt me-2" style="color: #388e3c;"></i>ที่อยู่<span
                                style="color: #dc3545; margin-left: 0.2rem;">*</span>
                        </label>
                    </div>

                    <!-- ที่อยู่เพิ่มเติม (บังคับกรอก) -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="input-wrapper">
                                <input type="text" id="additional_address_field" class="form-control" required
                                    placeholder="กรอกที่อยู่เพิ่มเติม (บ้านเลขที่ ซอย ถนน หมู่บ้าน) *"
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                <i class="fas fa-map-marker-alt input-icon"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #388e3c;"></i>
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
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                <i class="fas fa-mail-bulk input-icon"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #388e3c;"></i>
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
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                <i class="fas fa-map input-icon"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #388e3c;"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-wrapper">
                                <select id="amphoe_field" class="form-control" disabled
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <option value="">เลือกอำเภอ</option>
                                </select>
                                <i class="fas fa-city input-icon"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #388e3c;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- ตำบล -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                                    <i class="fas fa-home me-2" style="color: #388e3c;"></i>ตำบล<span
                                        style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="input-wrapper">
                                <select id="district_field" name="district_field" class="form-control" disabled required
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <option value="">เลือกตำบล *</option>
                                </select>
                                <i class="fas fa-home input-icon"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #388e3c;"></i>
                            </div>
                            <div class="invalid-feedback" id="district_field_feedback"></div>
                        </div>
                    </div>

                    <!-- ที่อยู่รวม (ซ่อน - ส่งไปยัง kid_aw_ods_address) -->
                    <input type="hidden" name="kid_aw_ods_address" id="full_address_field" value="">

                    <!-- *** เพิ่มใหม่: ซ่อน hidden fields สำหรับข้อมูลที่อยู่แยก *** -->
                    <input type="hidden" name="guest_province" id="guest_province_field" value="">
                    <input type="hidden" name="guest_amphoe" id="guest_amphoe_field" value="">
                    <input type="hidden" name="guest_district" id="guest_district_field" value="">
                    <input type="hidden" name="guest_zipcode" id="guest_zipcode_field" value="">

                    <!-- แสดงที่อยู่ที่รวมแล้ว -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info" id="address_preview"
                                style="display: none; border-radius: 15px; background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(129, 199, 132, 0.1) 100%); border: 1px solid rgba(76, 175, 80, 0.3);">
                                <strong><i class="fas fa-eye"></i> ที่อยู่ที่จะบันทึก:</strong>
                                <div id="address_preview_text"></div>
                            </div>
                        </div>
                    </div>

                    <div class="invalid-feedback" id="kid_aw_ods_address_feedback"></div>
                </div>

                <br>

                <div class="row" style="padding-bottom: 20px;">
                    <!-- ไฟล์แนบ -->
                    <div class="col-9">
                        <div class="form-group">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #424242;">
                                    <i class="fas fa-paperclip me-2" style="color: #388e3c;"></i>ไฟล์แนบ<small
                                        style="color: #6c757d; font-weight: 400; margin-left: 0.5rem;">(รูปภาพ หรือ
                                        PDF)</small>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <!-- File Upload Zone -->
                                <div class="file-upload-wrapper"
                                    style="border: 2px dashed #dee2e6; border-radius: 15px; padding: 1.5rem; text-align: center; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); transition: all 0.3s ease; cursor: pointer; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15);"
                                    ondrop="handleDrop(event)" ondragover="handleDragOver(event)"
                                    ondragenter="handleDragEnter(event)" ondragleave="handleDragLeave(event)">
                                    <div id="upload-placeholder" class="upload-placeholder">
                                        <i class="fas fa-cloud-upload-alt"
                                            style="font-size: 2rem; color: #388e3c; margin-bottom: 0.5rem;"></i>
                                        <p style="margin: 0; color: #6c757d; font-size: 1rem;">คลิกเพื่อเลือกไฟล์
                                            หรือลากไฟล์มาวางที่นี่</p>
                                        <small class="text-muted mt-2 d-block">รองรับไฟล์: JPG, PNG, PDF (สูงสุด 3
                                            ไฟล์)(ไม่เกิน 5 MB)</small>
                                    </div>
                                </div>
                                <input type="file" id="kid_aw_ods_files" name="kid_aw_ods_files[]" class="form-control"
                                    accept="image/*,.pdf" multiple onchange="handleFileSelect(this)"
                                    style="display: none;">

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
                            <button type="button" id="submitKidAwOdsBtn" class="btn modern-submit-btn"
                                onclick="handleKidAwOdsSubmit(event)"
                                style="background: linear-gradient(135deg, #388e3c 0%, #2e7d32 100%); border: none; color: white; padding: 1rem 2rem; border-radius: 15px; font-size: 1.1rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3); position: relative; overflow: hidden; min-width: 150px;">
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
                                "สำเนาถูกต้องประกอบการลงทะเบียนรับเงินสนับสนุนเด็กแรกเกิด"
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong style="color: #f57c00;">3.</strong>
                            เงินสนับสนุนเด็กแรกเกิดมีวัตถุประสงค์เพื่อ
                            <strong>ช่วยเหลือค่าใช้จ่ายในการเลี้ยงดูเด็ก</strong>
                            ในช่วงแรกเกิด
                        </div>
                        <div class="mb-0">
                            <strong style="color: #f57c00;">4.</strong>
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
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.2) !important;
        background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%) !important;
    }

    .btn-download-form:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4) !important;
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%) !important;
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
        color: #388e3c;
        pointer-events: none;
        z-index: 2;
    }

    /* Form styling */
    .form-label-wrapper:hover {
        background: linear-gradient(135deg, rgba(76, 175, 80, 0.12) 0%, rgba(129, 199, 132, 0.12) 100%) !important;
        box-shadow: 0 6px 16px rgba(76, 175, 80, 0.2) !important;
        transform: translateY(-2px);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: transparent !important;
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.25) !important;
        transform: translateY(-1px);
        background: linear-gradient(135deg, #ffffff 0%, #e8f5e8 100%) !important;
    }

    .track-status-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4) !important;
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%) !important;
        color: white !important;
        text-decoration: none !important;
    }

    .modern-submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(76, 175, 80, 0.4) !important;
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%) !important;
    }

    .modern-submit-btn:hover .btn-shine {
        left: 100%;
    }

    .kid-form-check:hover {
        background: rgba(76, 175, 80, 0.05) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.1);
    }

    .form-check-input:checked {
        background-color: #388e3c;
        border-color: #388e3c;
    }

    .file-upload-wrapper:hover {
        background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%) !important;
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.2) !important;
        transform: translateY(-2px);
        border-color: #388e3c !important;
    }

    .file-upload-wrapper.drag-over {
        background: linear-gradient(135deg, #f1f8e9 0%, #e8f5e8 100%) !important;
        border-color: #388e3c !important;
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3) !important;
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
    .kid-modal-content {
        z-index: 9999 !important;
    }

    .kid-login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.5) !important;
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%) !important;
    }

    .kid-guest-btn:hover {
        background: rgba(76, 175, 80, 0.15) !important;
        border-color: rgba(76, 175, 80, 0.5) !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3) !important;
    }

    /* User info display */
    #logged-in-user-info {
        border-radius: 15px;
        background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%);
        border: 1px solid rgba(76, 175, 80, 0.3);
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
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
    // *** JavaScript Code - Complete Kid AW ODS System ***

    // ตัวแปร Global
    const isUserLoggedIn = <?= json_encode($is_logged_in ?? false); ?>;
    const userInfo = <?= json_encode($user_info ?? null); ?>;
    const userAddress = <?= json_encode($user_address ?? null); ?>;
    let hasConfirmedAsGuest = isUserLoggedIn;
    let guestModalInstance = null;
    let idCardModalInstance = null;
    let selectedFiles = [];
    const maxFiles = 3;
    const maxFileSize = 5 * 1024 * 1024; // 5MB
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
                    <p style="color: #388e3c; font-size: 1.1rem; margin-bottom: 1rem;">
                        <i class="fas fa-user-check me-2"></i>
                        ยินดีต้อนรับ <strong>${userInfo.name}</strong>
                    </p>
                    <div style="background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%); 
                                padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                        <p style="margin: 0; color: #2e7d32;">
                            <i class="fas fa-magic me-2"></i>
                            ข้อมูลของคุณจะถูกใส่ในฟอร์มโดยอัตโนมัติ
                        </p>
                    </div>
                </div>
            `,
                confirmButtonText: 'เริ่มยื่นเรื่อง',
                confirmButtonColor: '#388e3c',
                timer: 5000,
                timerProgressBar: true
            });

            sessionStorage.removeItem('redirect_after_login');
        }
    }

    // เมื่อเอกสารโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('kidAwOdsForm');
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
                const fileInput = document.getElementById('kid_aw_ods_files');
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

        console.log('✅ Kid AW ODS form initialized with address system & ID card validation');
    });

    // *** ฟังก์ชัน Address System - เหมือนกับ Elderly ***
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
        <select id="province_field" class="form-control" style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
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
               style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
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

        // ใส่เฉพาะที่อยู่เพิ่มเติมใน kid_aw_ods_address
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
                document.getElementById('kidAwOdsForm').appendChild(hiddenField);
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
                icon.style.cssText = 'position: absolute; right: 45px; top: 50%; transform: translateY(-50%); color: #388e3c; z-index: 3;';
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
        const form = document.getElementById('kidAwOdsForm');
        if (form) {
            form.action = '<?= site_url('Kid_aw_ods/add_kid_aw_ods'); ?>';
            console.log('✅ Form action updated to:', form.action);
        }
    }

    // ตั้งค่าการตรวจสอบเบอร์โทร
    function setupPhoneValidation() {
        const phoneInput = document.getElementById('kid_aw_ods_phone');
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
        const idInput = document.getElementById('kid_aw_ods_number');
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

        const nameField = document.querySelector('input[name="kid_aw_ods_by"]');
        const phoneField = document.querySelector('input[name="kid_aw_ods_phone"]');
        const emailField = document.querySelector('input[name="kid_aw_ods_email"]');
        const idCardField = document.querySelector('input[name="kid_aw_ods_number"]');

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
                background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%); 
                border: 1px solid rgba(76, 175, 80, 0.3);
                margin-bottom: 2.5rem;
                padding: 2rem;
                box-shadow: 0 8px 25px rgba(76, 175, 80, 0.15);
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
                            background: linear-gradient(135deg, rgba(76, 175, 80, 0.15) 0%, rgba(129, 199, 132, 0.15) 100%); 
                            border-radius: 50%; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center;
                            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
                            margin: 0 auto 1rem;
                        ">
                            <i class="fas fa-user-check" style="font-size: 2.2rem; color: #388e3c;"></i>
                        </div>
                        <h4 class="mb-2" style="color: #2e7d32; font-weight: 700; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <i class="fas fa-check-circle me-2"></i>ใช้ข้อมูลจากบัญชีของคุณ
                        </h4>
                        <p class="mb-0" style="color: #2e7d32; font-size: 1rem; opacity: 0.8;">
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
                                    border: 1px solid rgba(76, 175, 80, 0.1);
                                    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.08);
                                    height: 100%;
                                ">
                                    <h6 style="color: #388e3c; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center;">
                                        <i class="fas fa-user-circle me-2" style="font-size: 1.2rem;"></i>
                                        ข้อมูลส่วนตัว
                                    </h6>
                                    <div class="info-item mb-3" style="display: flex; align-items: center; padding: 0.5rem 0;">
                                        <div class="info-icon" style="
                                            width: 40px; 
                                            height: 40px; 
                                            background: linear-gradient(135deg, #388e3c, #66bb6a); 
                                            border-radius: 10px; 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center;
                                            margin-right: 1rem;
                                            box-shadow: 0 3px 10px rgba(76, 175, 80, 0.3);
                                        ">
                                            <i class="fas fa-user" style="color: white; font-size: 1rem;"></i>
                                        </div>
                                        <div class="info-text" style="flex: 1;">
                                            <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.2rem;">ชื่อ-นามสกุล</div>
                                            <div style="font-size: 1.1rem; font-weight: 600; color: #2e7d32;">${userName}</div>
                                        </div>
                                    </div>
                                    <div class="info-item" style="display: flex; align-items: center; padding: 0.5rem 0;">
                                        <div class="info-icon" style="
                                            width: 40px; 
                                            height: 40px; 
                                            background: linear-gradient(135deg, #388e3c, #66bb6a); 
                                            border-radius: 10px; 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center;
                                            margin-right: 1rem;
                                            box-shadow: 0 3px 10px rgba(76, 175, 80, 0.3);
                                        ">
                                            <i class="fas fa-envelope" style="color: white; font-size: 1rem;"></i>
                                        </div>
                                        <div class="info-text" style="flex: 1;">
                                            <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.2rem;">อีเมล</div>
                                            <div style="font-size: 1.1rem; font-weight: 600; color: #2e7d32; word-break: break-all;">${userEmail}</div>
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
                                    border: 1px solid rgba(76, 175, 80, 0.1);
                                    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.08);
                                    height: 100%;
                                ">
                                    <h6 style="color: #388e3c; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center;">
                                        <i class="fas fa-address-book me-2" style="font-size: 1.2rem;"></i>
                                        ข้อมูลติดต่อ
                                    </h6>
                                    <div class="info-item mb-3" style="display: flex; align-items: center; padding: 0.5rem 0;">
                                        <div class="info-icon" style="
                                            width: 40px; 
                                            height: 40px; 
                                            background: linear-gradient(135deg, #388e3c, #66bb6a); 
                                            border-radius: 10px; 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center;
                                            margin-right: 1rem;
                                            box-shadow: 0 3px 10px rgba(76, 175, 80, 0.3);
                                        ">
                                            <i class="fas fa-phone" style="color: white; font-size: 1rem;"></i>
                                        </div>
                                        <div class="info-text" style="flex: 1;">
                                            <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.2rem;">เบอร์โทรศัพท์</div>
                                            <div style="font-size: 1.1rem; font-weight: 600; color: #2e7d32;">${userPhone}</div>
                                        </div>
                                    </div>
                                    <div class="info-item" style="display: flex; align-items: flex-start; padding: 0.5rem 0;">
                                        <div class="info-icon" style="
                                            width: 40px; 
                                            height: 40px; 
                                            background: linear-gradient(135deg, #388e3c, #66bb6a); 
                                            border-radius: 10px; 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center;
                                            margin-right: 1rem;
                                            margin-top: 0.2rem;
                                            box-shadow: 0 3px 10px rgba(76, 175, 80, 0.3);
                                            flex-shrink: 0;
                                        ">
                                            <i class="fas fa-map-marker-alt" style="color: white; font-size: 1rem;"></i>
                                        </div>
                                        <div class="info-text" style="flex: 1;">
                                            <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.2rem;">ที่อยู่</div>
                                            <div style="font-size: 1rem; font-weight: 500; color: #2e7d32; line-height: 1.4; word-break: break-word;">${userAddressText}</div>
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
                                        ระบบจะนำข้อมูลจากบัญชีของคุณมาใช้โดยอัตโนมัติ คุณสามารถยื่นเรื่องเงินสนับสนุนเด็กได้ทันที
                                        และติดตามสถานะได้จากหน้าบัญชีของคุณ
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

            const formContainer = document.getElementById('kid_aw_ods_form');
            const firstFormGroup = formContainer.querySelector('.form-group, .kid-type-selector');
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
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4) !important;
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
                text: 'คุณสามารถยื่นเรื่องเงินสนับสนุนเด็กได้ทันที ข้อมูลของคุณจะถูกใช้โดยอัตโนมัติ',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: 'linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%)',
                color: '#2e7d32'
            });
        }
    }



    function showDuplicateSubmissionModal() {
        Swal.fire({
            title: '<span style="color: #dc3545;">พบการยื่นเรื่องซ้ำ!</span>',
            html: `
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.25) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2.5rem; color: #dc3545;"></i>
                </div>
                <h4 style="color: #dc3545; font-weight: 600; margin-bottom: 1rem;">
                    มีการยื่นเรื่องแล้วใน 24 ชั่วโมงที่ผ่านมา
                </h4>
                <div style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%); 
                           padding: 1.5rem; border-radius: 15px; margin: 1rem 0;
                           border: 1px solid rgba(220, 53, 69, 0.2);">
                    <p style="color: #721c24; font-size: 1.05rem; line-height: 1.6; margin: 0;">
                        <i class="fas fa-info-circle me-2" style="color: #dc3545;"></i>
                        พบการยื่นเรื่องเงินสนับสนุนเด็กด้วย<br>
                        <strong>เลขบัตรประชาชน</strong> หรือ <strong>เบอร์โทรศัพท์</strong><br>
                        เดียวกันในระบบแล้ว
                    </p>
                </div>
                
                <div style="text-align: left; margin: 1.5rem 0;">
                    <h6 style="color: #dc3545; font-weight: 600; margin-bottom: 1rem;">
                        <i class="fas fa-lightbulb me-2"></i>แนวทางแก้ไข:
                    </h6>
                    <ul style="color: #721c24; line-height: 1.8; margin: 0; padding-left: 1.5rem;">
                        <li><strong>ตรวจสอบสถานะ:</strong> ใช้ปุ่ม "ติดตามสถานะ" เพื่อดูความคืบหน้า</li>
                        <li><strong>รอ 24 ชั่วโมง:</strong> หากต้องการยื่นเรื่องใหม่</li>
                        <li><strong>ติดต่อเจ้าหน้าที่:</strong> หากมีปัญหาหรือข้อสงสัย</li>
                    </ul>
                </div>
                
                <div style="background: rgba(23, 162, 184, 0.1); padding: 1rem; border-radius: 12px; margin: 1rem 0;">
                    <p style="color: #0c5460; margin: 0; font-size: 0.95rem;">
                        <i class="fas fa-phone me-2"></i>
                        <strong>ติดต่อสำนักงาน:</strong> โทร <?php echo get_config_value('phone_1'); ?>
                    </p>
                </div>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-search me-2"></i>ติดตามสถานะ',
            cancelButtonText: '<i class="fas fa-times me-2"></i>ปิด',
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'duplicate-warning-modal',
                title: 'duplicate-warning-title',
                content: 'duplicate-warning-content'
            },
            buttonsStyling: true
        }).then((result) => {
            if (result.isConfirmed) {
                // *** แยก redirect ตาม user type ***
                if (isUserLoggedIn) {
                    // User ที่ login -> ไปหน้า my_kid_aw_ods
                    window.location.href = '<?= site_url('Kid_aw_ods/my_kid_aw_ods'); ?>';
                } else {
                    // Guest user -> ไปหน้า follow_kid_aw_ods
                    window.location.href = '<?= site_url('Kid_aw_ods/follow_kid_aw_ods'); ?>';
                }
            }
        });
    }

    // แสดง modal สำหรับเลขบัตรประชาชน
    function showIdCardModal() {
        Swal.fire({
            title: '<span style="color: #388e3c;">กรอกเลขบัตรประจำตัวประชาชน</span>',
            html: `
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.25) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);">
                    <i class="fas fa-id-card" style="font-size: 2.5rem; color: #dc3545;"></i>
                </div>
                <p style="color: #666; font-size: 1.1rem; line-height: 1.6; margin-bottom: 1rem;">
                    <strong style="color: #dc3545;">จำเป็นต่อการให้บริการ</strong><br>
                    กรุณากรอกเลขบัตรประจำตัวประชาชน 13 หลัก<br>
                    เพื่อใช้ในการยื่นเรื่องเงินสนับสนุนเด็กและติดตามสถานะ
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
            confirmButtonColor: '#388e3c',
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
            html: '<div style="text-align: center;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #388e3c;"></i><br><br>กำลังอัพเดทข้อมูลของคุณ</div>',
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

                    const idCardField = document.querySelector('input[name="kid_aw_ods_number"]');
                    if (idCardField) {
                        idCardField.value = idCardNumber;
                        idCardField.readOnly = true;
                        idCardField.style.backgroundColor = '#f8f9fa';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ!',
                        text: 'อัพเดทเลขบัตรประจำตัวประชาชนแล้ว ดำเนินการยื่นเรื่องต่อ',
                        confirmButtonColor: '#388e3c',
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

        const fileInput = document.getElementById('kid_aw_ods_files');
        fileInput.files = e.dataTransfer.files;
        handleFileSelect(fileInput);
    }

    // Form submission
    // *** เพิ่ม: Debug reCAPTCHA variables ตั้งแต่เริ่มต้น ***
    console.log('🔑 Initial reCAPTCHA check for Kid Aw Ods Form:');
    console.log('- RECAPTCHA_SITE_KEY:', typeof window.RECAPTCHA_SITE_KEY !== 'undefined' ? window.RECAPTCHA_SITE_KEY : 'UNDEFINED');
    console.log('- recaptchaReady:', typeof window.recaptchaReady !== 'undefined' ? window.recaptchaReady : 'UNDEFINED');
    console.log('- SKIP_RECAPTCHA_FOR_DEV:', typeof window.SKIP_RECAPTCHA_FOR_DEV !== 'undefined' ? window.SKIP_RECAPTCHA_FOR_DEV : 'UNDEFINED');
    console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

    // Form submission
    function handleKidAwOdsSubmit(event) {
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
            const form = document.getElementById('kidAwOdsForm');

            if (!form || !form.checkValidity()) {
                console.warn('❌ Form validation failed');

                const fieldNameMapping = {
                    'kid_aw_ods_by': 'ชื่อ-นามสกุล ผู้ปกครอง',
                    'kid_aw_ods_phone': 'เบอร์โทรศัพท์',
                    'kid_aw_ods_email': 'อีเมล',
                    'kid_aw_ods_number': 'เลขบัตรประชาชน ผู้ปกครอง',
                    'kid_aw_ods_address': 'ที่อยู่',
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
                } else if (isUserLoggedIn) {
                    console.log('✅ Logged in user - proceeding despite form validity check');
                } else {
                    showAlert('warning', 'กรุณาตรวจสอบข้อมูล', 'มีข้อมูลที่ไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง');
                }

                if (!isUserLoggedIn || visibleInvalidFields.length > 0) {
                    return;
                }
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
                const idCardValue = document.querySelector('input[name="kid_aw_ods_number"]')?.value?.trim();

                if (!idCardValue || !/^\d{13}$/.test(idCardValue)) {
                    showAlert('error', 'เลขบัตรประชาชนไม่ถูกต้อง', 'กรุณากรอกเลขบัตรประจำตัวประชาชน 13 หลัก (ตัวเลขเท่านั้น)');
                    document.querySelector('input[name="kid_aw_ods_number"]')?.focus();
                    return;
                }

                if (!validateThaiIdCard(idCardValue)) {
                    showAlert('error', 'เลขบัตรประชาชนไม่ถูกต้อง', 'เลขบัตรประจำตัวประชาชนที่กรอกไม่ถูกต้องตามมาตรฐาน กรุณาตรวจสอบอีกครั้ง');
                    document.querySelector('input[name="kid_aw_ods_number"]')?.focus();
                    return;
                }
            }

            if (formSubmitting) return;
            formSubmitting = true;

            const submitBtn = document.getElementById('submitKidAwOdsBtn');
            const originalContent = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังยื่นเรื่อง...';

            console.log('📝 Kid Aw Ods form submitted');

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

            // ตรวจสอบว่ามี reCAPTCHA หรือไม่
            if (shouldUseRecaptcha) {
                console.log('🛡️ Executing reCAPTCHA...');

                grecaptcha.ready(function () {
                    console.log('🔧 grecaptcha.ready() called for kid aw ods');

                    grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                        action: 'kid_aw_ods_submit'
                    }).then(function (token) {
                        console.log('✅ reCAPTCHA token received for kid aw ods:', token.substring(0, 50) + '...');
                        console.log('📏 Token length:', token.length);

                        performKidAwOdsSubmitWithRecaptcha(form, token, submitBtn, originalContent);
                    }).catch(function (error) {
                        console.error('❌ reCAPTCHA execution failed for kid aw ods:', error);
                        console.log('🔄 Falling back to submit without reCAPTCHA');
                        performKidAwOdsSubmitWithoutRecaptcha(form, submitBtn, originalContent);
                    });
                });
            } else {
                console.log('⚠️ reCAPTCHA not available, submitting without verification');
                console.log('📋 Reasons breakdown:');
                console.log('- SITE_KEY exists:', !!window.RECAPTCHA_SITE_KEY);
                console.log('- reCAPTCHA ready:', !!window.recaptchaReady);
                console.log('- Skip dev mode:', !!window.SKIP_RECAPTCHA_FOR_DEV);
                console.log('- grecaptcha available:', typeof grecaptcha !== 'undefined');

                performKidAwOdsSubmitWithoutRecaptcha(form, submitBtn, originalContent);
            }

        } catch (error) {
            console.error('Submit form error:', error);
            showAlert('error', 'เกิดข้อผิดพลาดในระบบ', 'ไม่สามารถส่งฟอร์มได้ กรุณาลองใหม่');
            formSubmitting = false;
        }
    }

    // *** เพิ่ม: Submit Function พร้อม reCAPTCHA ***
    function performKidAwOdsSubmitWithRecaptcha(form, recaptchaToken, submitBtn, originalContent) {
        console.log('📤 Submitting kid aw ods with reCAPTCHA token...');

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

            // เพิ่มประเภทเงินสนับสนุนเด็ก
            const kidAwOdsType = document.querySelector('input[name="kid_aw_ods_type"]:checked');
            if (kidAwOdsType) {
                formData.append('kid_aw_ods_type', kidAwOdsType.value);
            }

            // เพิ่มข้อมูลที่อยู่สำหรับ guest
            if (!isUserLoggedIn) {
                const guestProvince = document.querySelector('#guest_province_field')?.value || '';
                const guestAmphoe = document.querySelector('#guest_amphoe_field')?.value || '';
                const guestDistrict = document.querySelector('#guest_district_field')?.value || '';
                const guestZipcode = document.querySelector('#guest_zipcode_field')?.value || '';

                if (guestProvince) formData.append('guest_province', guestProvince);
                if (guestAmphoe) formData.append('guest_amphoe', guestAmphoe);
                if (guestDistrict) formData.append('guest_district', guestDistrict);
                if (guestZipcode) formData.append('guest_zipcode', guestZipcode);
            }

            // เพิ่มไฟล์
            if (selectedFiles && selectedFiles.length > 0) {
                selectedFiles.forEach((file, index) => {
                    formData.append('kid_aw_ods_files[]', file, file.name);
                });
            }

            // *** เพิ่ม: reCAPTCHA parameters ***
            formData.append('g-recaptcha-response', recaptchaToken);
            formData.append('recaptcha_action', 'kid_aw_ods_submit');
            formData.append('recaptcha_source', 'kid_aw_ods_form');
            formData.append('user_type_detected', isUserLoggedIn ? 'member' : 'guest');
            formData.append('ajax_request', '1');
            formData.append('client_timestamp', new Date().toISOString());
            formData.append('user_agent_info', navigator.userAgent);
            formData.append('is_anonymous', isUserLoggedIn ? '0' : '1');

            console.log('📦 FormData contents for kid aw ods:');
            for (let [key, value] of formData.entries()) {
                if (key === 'g-recaptcha-response') {
                    console.log('- ' + key + ':', value.substring(0, 50) + '...');
                } else if (key.includes('files')) {
                    console.log('- ' + key + ':', '[FILE]');
                } else {
                    console.log('- ' + key + ':', value);
                }
            }

            // ส่งข้อมูล
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                if (text.trim().length > 0) {
                                    console.error('Server response (non-JSON):', text);
                                    throw new Error('Server returned non-JSON response');
                                }
                                return {
                                    success: true,
                                    message: 'ยื่นเรื่องเงินสนับสนุนเด็กสำเร็จ',
                                    kid_aw_ods_id: 'K' + Date.now().toString().slice(-6)
                                };
                            }
                        });
                    }
                })
                .then(jsonResponse => {
                    handleKidAwOdsSubmitResponse(jsonResponse);
                })
                .catch(error => {
                    handleKidAwOdsSubmitError(error);
                })
                .finally(() => {
                    restoreKidAwOdsSubmitButton(submitBtn, originalContent);
                });

        } catch (error) {
            console.error('Kid Aw Ods submit with reCAPTCHA error:', error);
            handleKidAwOdsSubmitError(error);
            restoreKidAwOdsSubmitButton(submitBtn, originalContent);
        }
    }

    // *** เพิ่ม: Submit Function แบบปกติ ***
    function performKidAwOdsSubmitWithoutRecaptcha(form, submitBtn, originalContent) {
        console.log('📤 Submitting kid aw ods without reCAPTCHA...');

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

            // เพิ่มประเภทเงินสนับสนุนเด็ก
            const kidAwOdsType = document.querySelector('input[name="kid_aw_ods_type"]:checked');
            if (kidAwOdsType) {
                formData.append('kid_aw_ods_type', kidAwOdsType.value);
            }

            // เพิ่มข้อมูลที่อยู่สำหรับ guest
            if (!isUserLoggedIn) {
                const guestProvince = document.querySelector('#guest_province_field')?.value || '';
                const guestAmphoe = document.querySelector('#guest_amphoe_field')?.value || '';
                const guestDistrict = document.querySelector('#guest_district_field')?.value || '';
                const guestZipcode = document.querySelector('#guest_zipcode_field')?.value || '';

                if (guestProvince) formData.append('guest_province', guestProvince);
                if (guestAmphoe) formData.append('guest_amphoe', guestAmphoe);
                if (guestDistrict) formData.append('guest_district', guestDistrict);
                if (guestZipcode) formData.append('guest_zipcode', guestZipcode);
            }

            // เพิ่มไฟล์
            if (selectedFiles && selectedFiles.length > 0) {
                selectedFiles.forEach((file, index) => {
                    formData.append('kid_aw_ods_files[]', file, file.name);
                });
            }

            // เพิ่ม dev mode flag
            formData.append('dev_mode', '1');

            // ส่งข้อมูล
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                if (text.trim().length > 0) {
                                    console.error('Server response (non-JSON):', text);
                                    throw new Error('Server returned non-JSON response');
                                }
                                return {
                                    success: true,
                                    message: 'ยื่นเรื่องเงินสนับสนุนเด็กสำเร็จ',
                                    kid_aw_ods_id: 'K' + Date.now().toString().slice(-6)
                                };
                            }
                        });
                    }
                })
                .then(jsonResponse => {
                    handleKidAwOdsSubmitResponse(jsonResponse);
                })
                .catch(error => {
                    handleKidAwOdsSubmitError(error);
                })
                .finally(() => {
                    restoreKidAwOdsSubmitButton(submitBtn, originalContent);
                });

        } catch (error) {
            console.error('Kid Aw Ods submit without reCAPTCHA error:', error);
            handleKidAwOdsSubmitError(error);
            restoreKidAwOdsSubmitButton(submitBtn, originalContent);
        }
    }

    // *** เพิ่ม: จัดการ Response ***
    function handleKidAwOdsSubmitResponse(jsonResponse) {
        if (jsonResponse.success) {
            const kidAwOdsId = jsonResponse.kid_aw_ods_id || 'K' + Date.now().toString().slice(-6);

            // *** แยกข้อความตาม user type ***
            const userTypeText = isUserLoggedIn ?
                'คุณสามารถติดตามสถานะได้ในหน้าบัญชีของคุณ' :
                'คุณสามารถติดตามสถานะได้ด้วยหมายเลขอ้างอิงนี้';

            const confirmButtonText = isUserLoggedIn ?
                '<i class="fas fa-user-check me-2"></i>ไปยังหน้าบัญชีของฉัน' :
                '<i class="fas fa-search me-2"></i>ติดตามสถานะ';

            Swal.fire({
                icon: 'success',
                title: 'ยื่นเรื่องเงินสนับสนุนเด็กสำเร็จ!',
                html: `
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, rgba(76, 175, 80, 0.15) 0%, rgba(129, 199, 132, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);">
                    <i class="fas fa-check-circle" style="font-size: 2.5rem; color: #388e3c;"></i>
                </div>
                
                <div style="background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%); 
                            padding: 1.5rem; border-radius: 15px; margin: 1rem 0;
                            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);">
                    <h3 style="color: #2e7d32; margin-bottom: 0.5rem; font-weight: 600;">
                        📝 หมายเลขอ้างอิง
                    </h3>
                    <div style="font-size: 2.2rem; font-weight: bold; color: #2e7d32; 
                               letter-spacing: 2px; margin: 1rem 0;">
                        ${kidAwOdsId}
                    </div>
                    <button onclick="copyRefId('${kidAwOdsId}')" 
                            class="btn btn-sm btn-outline-success mt-2"
                            style="border-radius: 12px; padding: 0.5rem 1rem; font-weight: 600;">
                        <i class="fas fa-copy me-1"></i> คัดลอกหมายเลข
                    </button>
                </div>
                
                <div style="background: rgba(76, 175, 80, 0.1); padding: 1rem; border-radius: 12px; margin: 1rem 0;">
                    <p style="color: #388e3c; margin: 0; font-size: 0.95rem;">
                        <i class="fas fa-info-circle me-2"></i>${userTypeText}
                    </p>
                </div>
                
                <p style="color: #666; margin-top: 1rem;">ขอบคุณสำหรับการยื่นเรื่องเงินสนับสนุนเด็กแรกเกิด</p>
            </div>
        `,
                confirmButtonText: confirmButtonText,
                confirmButtonColor: '#388e3c',
                showCancelButton: true,
                cancelButtonText: '<i class="fas fa-times me-1"></i>ปิด',
                cancelButtonColor: '#6c757d',
                buttonsStyling: true,
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // *** แยก redirect ตาม user type ***
                    if (isUserLoggedIn) {
                        // User ที่ login -> ไปหน้า my_kid_aw_ods
                        window.location.href = `<?= site_url('Kid_aw_ods/my_kid_aw_ods'); ?>`;
                    } else {
                        // Guest user -> ไปหน้า follow_kid_aw_ods พร้อม kid_aw_ods_id
                        window.location.href = `<?= site_url('Kid_aw_ods/follow_kid_aw_ods'); ?>?ref=${kidAwOdsId}`;
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
    function handleKidAwOdsSubmitError(error) {
        console.error('Kid Aw Ods submit error:', error);

        // ตรวจสอบว่าเป็น error การยื่นเรื่องซ้ำหรือไม่
        if (error.message && error.message.includes('พบการยื่นเรื่องด้วยเลขบัตรประชาชน')) {
            showDuplicateSubmissionModal();
        } else {
            showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถยื่นเรื่องได้ กรุณาลองใหม่อีกครั้ง');
        }
    }

    // *** เพิ่ม: คืนค่าปุ่มเป็นสถานะเดิม ***
    function restoreKidAwOdsSubmitButton(submitBtn, originalContent) {
        setTimeout(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
            }
            formSubmitting = false;
        }, 100);
    }

    function resetForm() {
        const form = document.getElementById('kidAwOdsForm');
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

        // รีเซ็ตประเภทเงินสนับสนุนเด็ก
        document.getElementById('children').checked = true;
    }

    // *** เพิ่ม: ตรวจสอบการโหลด reCAPTCHA เมื่อ DOM พร้อม ***
    document.addEventListener('DOMContentLoaded', function () {
        // *** เพิ่ม: ตรวจสอบการโหลด reCAPTCHA ***
        if (window.RECAPTCHA_SITE_KEY && !window.recaptchaReady) {
            console.log('⏳ Waiting for reCAPTCHA to load for kid aw ods form...');

            let checkInterval = setInterval(function () {
                if (window.recaptchaReady) {
                    console.log('✅ reCAPTCHA is now ready for kid aw ods form');
                    clearInterval(checkInterval);
                }
            }, 100);

            setTimeout(function () {
                if (!window.recaptchaReady) {
                    console.log('⚠️ reCAPTCHA timeout after 10 seconds for kid aw ods form');
                    clearInterval(checkInterval);
                }
            }, 10000);
        }
    });

    //////////////////////////////////////////////////////////////////////
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
                keyboard: false
            });
            guestModalInstance.show();
        } else {
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
        }
    }

    function hideModal() {
        const modalElement = document.getElementById('guestConfirmModal');
        if (guestModalInstance) {
            guestModalInstance.hide();
            guestModalInstance = null;
        } else if (modalElement) {
            modalElement.style.display = 'none';
            modalElement.classList.remove('show');
        }
    }

    function proceedAsGuest() {
        hasConfirmedAsGuest = true;
        hideModal();
        showAlert('info', 'ดำเนินการต่อโดยไม่เข้าสู่ระบบ', 'คุณสามารถกรอกข้อมูลและยื่นเรื่องเงินสนับสนุนเด็กได้แล้ว', 2000);
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
            confirmButtonColor: '#388e3c',
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
            background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%);
            border: 1px solid rgba(76, 175, 80, 0.3);
            border-radius: 15px;
            margin-bottom: 2rem;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.15);
        `;

            infoBox.innerHTML = `
            <div class="row align-items-center">
                <div class="col-auto">
                    <div style="
                        width: 50px; 
                        height: 50px; 
                        background: linear-gradient(135deg, rgba(76, 175, 80, 0.15) 0%, rgba(129, 199, 132, 0.15) 100%); 
                        border-radius: 50%; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center;
                        box-shadow: 0 4px 10px rgba(76, 175, 80, 0.2);
                    ">
                        <i class="fas fa-user-check" style="font-size: 1.5rem; color: #388e3c;"></i>
                    </div>
                </div>
                <div class="col">
                    <h6 class="mb-1" style="color: #2e7d32; font-weight: 600;">
                        <i class="fas fa-info-circle me-2"></i>ข้อมูลจากบัญชีของคุณ
                    </h6>
                    <p class="mb-0" style="color: #424242; font-size: 0.9rem;">
                        ระบบจะใช้ข้อมูลส่วนตัวและที่อยู่จากบัญชีของคุณโดยอัตโนมัติ
                        <strong style="color: #388e3c;">${userInfo.name || 'ผู้ใช้'}</strong>
                    </p>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn-close" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()"></button>
                </div>
            </div>
        `;

            const formContainer = document.getElementById('kid_aw_ods_form');
            const typeSelector = formContainer.querySelector('.kid-type-selector');
            if (typeSelector) {
                typeSelector.parentNode.insertBefore(infoBox, typeSelector);
            }
        });
    }

    console.log('✅ Kid AW ODS Form - Complete JavaScript System Ready!');
</script>