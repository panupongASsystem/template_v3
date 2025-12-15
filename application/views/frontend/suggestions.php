<!-- File: application/views/frontend/follow_suggestions.php -->
<div class="text-center pages-head">
    <span class="font-pages-head">รับฟังความคิดเห็น</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">


<!-- Modal สำหรับการยืนยันการส่งความคิดเห็นโดยไม่เข้าสู่ระบบ -->
<div class="modal fade" id="guestConfirmModal" tabindex="-1" aria-labelledby="guestConfirmModalLabel" aria-hidden="true"
    style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content suggestions-modal-content"
            style="border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(76, 175, 80, 0.2), 0 8px 25px rgba(0,0,0,0.08); background: linear-gradient(135deg, #ffffff 0%, #f1f8e9 100%); overflow: hidden;">
            <div class="modal-header suggestions-modal-header"
                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(129, 199, 132, 0.1) 100%); color: #2c3e50; border-radius: 20px 20px 0 0; border-bottom: 1px solid rgba(76, 175, 80, 0.2); backdrop-filter: blur(10px);">
                <h5 class="modal-title suggestions-modal-title" id="guestConfirmModalLabel"
                    style="font-weight: 600; color: #4caf50; width: 100%; text-align: center;">
                    <i class="fas fa-comments me-2" style="color: #4caf50;"></i>ยินดีต้อนรับสู่ระบบรับฟังความคิดเห็น
                </h5>
            </div>
            <div class="modal-body suggestions-modal-body text-center"
                style="padding: 2.5rem; background: linear-gradient(135deg, #ffffff 0%, #f1f8e9 100%);">
                <div class="mb-4">
                    <div
                        style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(76, 175, 80, 0.15) 0%, rgba(129, 199, 132, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);">
                        <i class="fas fa-user-shield"
                            style="font-size: 2.5rem; color: #4caf50; text-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);"></i>
                    </div>
                </div>
                <h5 class="mb-3" style="color: #2c3e50; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    เริ่มต้นการใช้งาน</h5>
                <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.6; color: #6c757d;">
                    เข้าสู่ระบบเพื่อติดตามสถานะความคิดเห็นและได้รับการแจ้งเตือน สะดวกรวดเร็ว ปลอดภัย
                    ไม่มีใครค้นหาความคิดเห็นของคุณได้ หรือดำเนินการต่อโดยไม่ต้องเข้าสู่ระบบ
                    ไม่ปลอดภัยบุคคลอื่นสามารถค้นหาความคิดเห็นได้จากหน้าติดตาม</p>

                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-lg suggestions-login-btn" onclick="redirectToLogin()"
                        style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); border: none; color: white; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4); transition: all 0.3s ease; font-size: 1.1rem;">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                    </button>
                    <button type="button" class="btn btn-lg suggestions-guest-btn" onclick="proceedAsGuest()"
                        style="background: rgba(76, 175, 80, 0.08); border: 2px solid rgba(76, 175, 80, 0.3); color: #4caf50; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; transition: all 0.3s ease; font-size: 1.1rem; backdrop-filter: blur(10px);">
                        <i class="fas fa-paper-plane me-2"></i>ดำเนินการต่อโดยไม่เข้าสู่ระบบ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center pages-head">
    <span class="font-pages-head"
        style="font-size: 2.8rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(108, 117, 125, 0.2);">รับฟังความคิดเห็น</span>
</div>

<div class="bg-pages" style="background: #ffffff; min-height: 100vh; padding: 2rem 0;">
    <div class="container-pages-news mb-5 mt-5"
        style="position: relative; z-index: 10; background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 1000px; overflow: hidden;"
        id="suggestions_form">

        <!-- เพิ่ม decorative element -->
        <div
            style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #4caf50, #81c784, #4caf50); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;">
        </div>

        <!-- ปุ่มติดตามสถานะ -->
        <div class="d-flex justify-content-end mb-3">
            <button type="button" onclick="redirectToTrackStatus()" class="btn track-status-btn" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); 
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
                    <i class="fas fa-search me-2"></i>ติดตามสถานะความคิดเห็น
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
                    // User ที่ login (public หรือ staff) → ไปหน้า my_suggestions
                    window.location.href = '<?= site_url('Suggestions/my_suggestions'); ?>';
                } else {
                    // Guest user → ไปหน้า follow_suggestions
                    window.location.href = '<?= site_url('Suggestions/follow_suggestions'); ?>';
                }
            }

            // *** เพิ่ม hover effect ***
            document.addEventListener('DOMContentLoaded', function () {
                const trackBtn = document.querySelector('.track-status-btn');
                if (trackBtn) {
                    trackBtn.addEventListener('mouseenter', function () {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 6px 20px rgba(76, 175, 80, 0.4)';
                        this.style.background = 'linear-gradient(135deg, #43a047 0%, #2e7d32 100%)';
                    });

                    trackBtn.addEventListener('mouseleave', function () {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = '0 4px 15px rgba(76, 175, 80, 0.3)';
                        this.style.background = 'linear-gradient(135deg, #4caf50 0%, #388e3c 100%)';
                    });
                }
            });
        </script>



        <!-- ประเภทความคิดเห็น -->
        <div class="suggestions-type-selector"
            style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 15px; padding: 1rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15);">
            <div class="form-label-wrapper"
                style="margin-bottom: 1rem; background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                <label class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                    <i class="fas fa-layer-group me-2" style="color: #4caf50;"></i>ประเภทความคิดเห็น
                </label>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-check p-3 suggestions-form-check"
                        style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                        <input class="form-check-input" type="radio" name="suggestion_type" id="suggestion"
                            value="suggestion" checked style="transform: scale(1.2); margin: 0;">
                        <label class="form-check-label fw-bold" for="suggestion"
                            style="color: #495057; margin-left: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-lightbulb me-2" style="color: #ffc107;"></i>ข้อเสนอแนะ
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check p-3 suggestions-form-check"
                        style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                        <input class="form-check-input" type="radio" name="suggestion_type" id="feedback"
                            value="feedback" style="transform: scale(1.2); margin: 0;">
                        <label class="form-check-label fw-bold" for="feedback"
                            style="color: #495057; margin-left: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-comment-dots me-2" style="color: #17a2b8;"></i>ความคิดเห็น
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check p-3 suggestions-form-check"
                        style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                        <input class="form-check-input" type="radio" name="suggestion_type" id="improvement"
                            value="improvement" style="transform: scale(1.2); margin: 0;">
                        <label class="form-check-label fw-bold" for="improvement"
                            style="color: #495057; margin-left: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-chart-line me-2" style="color: #28a745;"></i>การปรับปรุง
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="underline">
            <form id="suggestionsForm" action="<?php echo site_url('Suggestions/add_suggestions'); ?>" method="post"
                class="form-horizontal" enctype="multipart/form-data" onsubmit="return false;">
                <input type="hidden" name="form_token" id="formToken" value="">
                <br>

                <!-- เรื่องที่ต้องการเสนอแนะ -->
                <div class="form-group mb-4">
                    <div class="form-label-wrapper"
                        style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                        <label class="form-label"
                            style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                            <i class="fas fa-edit me-2" style="color: #4caf50;"></i>เรื่องที่ต้องการเสนอแนะ<span
                                style="color: #dc3545; margin-left: 0.2rem;">*</span>
                        </label>
                    </div>
                    <div class="col-sm-12">
                        <input type="text" name="suggestions_topic" class="form-control" required
                            placeholder="กรอกเรื่องที่ต้องการเสนอแนะ..."
                            style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                        <div class="invalid-feedback" id="suggestions_topic_feedback"></div>
                    </div>
                </div>

                <div class="row">
                    <!-- ชื่อ-นามสกุล -->
                    <div class="col-md-5">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-user me-2" style="color: #4caf50;"></i>ชื่อ-นามสกุล<span
                                        style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="text" name="suggestions_by" id="suggestions_by" class="form-control"
                                    required placeholder="เช่น นางสาว น้ำใส ใจชื่นบาน"
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="suggestions_by_feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- เบอร์โทรศัพท์ -->
                    <div class="col-md-3">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-phone me-2" style="color: #4caf50;"></i>เบอร์โทรศัพท์<span
                                        style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="tel" id="suggestions_phone" name="suggestions_phone" class="form-control"
                                    required placeholder="เช่น 0812345678" pattern="\d{10}"
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="suggestions_phone_feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- อีเมล -->
                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-envelope me-2" style="color: #4caf50;"></i>อีเมล<small
                                        style="color: #6c757d; font-weight: 400; margin-left: 0.5rem;">(Optional)</small>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="email" name="suggestions_email" class="form-control"
                                    placeholder="example@youremail.com"
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="suggestions_email_feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- เลขบัตรประชาชน -->
                <div class="form-group mb-4">
                    <div class="form-label-wrapper"
                        style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                        <label class="form-label"
                            style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                            <i class="fas fa-id-card me-2" style="color: #4caf50;"></i>เลขบัตรประจำตัวประชาชน<span
                                style="color: #dc3545; margin-left: 0.2rem;">*</span>
                        </label>
                    </div>
                    <div class="col-sm-12">
                        <input type="text" name="suggestions_number" id="suggestions_number" class="form-control"
                            required placeholder="เลขบัตรประจำตัวประชาชน 13 หลัก" maxlength="13"
                            style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                        <div class="invalid-feedback" id="suggestions_number_feedback"></div>
                    </div>
                </div>

                <!-- *** ระบบที่อยู่แบบละเอียด *** -->
                <div class="form-group mb-4">
                    <div class="form-label-wrapper"
                        style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                        <label class="form-label"
                            style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                            <i class="fas fa-map-marker-alt me-2" style="color: #4caf50;"></i>ที่อยู่<span
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
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #4caf50;"></i>
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
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #4caf50;"></i>
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
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #4caf50;"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-wrapper">
                                <select id="amphoe_field" class="form-control" disabled
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <option value="">เลือกอำเภอ</option>
                                </select>
                                <i class="fas fa-city input-icon"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #4caf50;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- ตำบล -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-home me-2" style="color: #4caf50;"></i>ตำบล<span
                                        style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="input-wrapper">
                                <select id="district_field" name="district_field" class="form-control" disabled required
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <option value="">เลือกตำบล *</option>
                                </select>
                                <i class="fas fa-home input-icon"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #4caf50;"></i>
                            </div>
                            <div class="invalid-feedback" id="district_field_feedback"></div>
                        </div>
                    </div>

                    <!-- ที่อยู่รวม (ซ่อน - ส่งไปยัง suggestions_address) -->
                    <input type="hidden" name="suggestions_address" id="full_address_field" value="">

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

                    <div class="invalid-feedback" id="suggestions_address_feedback"></div>
                </div>

                <!-- รายละเอียด -->
                <div class="form-group mb-4">
                    <div class="form-label-wrapper"
                        style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                        <label for="suggestions_detail" class="form-label"
                            style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                            <i class="fas fa-align-left me-2" style="color: #4caf50;"></i>รายละเอียดเพิ่มเติม<span
                                style="color: #dc3545; margin-left: 0.2rem;">*</span>
                        </label>
                    </div>
                    <div class="col-sm-12">
                        <textarea name="suggestions_detail" class="form-control" id="suggestions_detail" rows="6"
                            required placeholder="กรอกรายละเอียดเพิ่มเติม..."
                            style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); resize: vertical;"></textarea>
                        <div class="invalid-feedback" id="suggestions_detail_feedback"></div>
                    </div>
                </div>

                <br>

                <div class="row" style="padding-bottom: 20px;">
                    <!-- ไฟล์แนบ -->
                    <div class="col-9">
                        <div class="form-group">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(129, 199, 132, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-paperclip me-2" style="color: #4caf50;"></i>ไฟล์แนบ<small
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
                                            style="font-size: 2rem; color: #4caf50; margin-bottom: 0.5rem;"></i>
                                        <p style="margin: 0; color: #6c757d; font-size: 1rem;">คลิกเพื่อเลือกไฟล์
                                            หรือลากไฟล์มาวางที่นี่</p>
                                        <small class="text-muted mt-2 d-block">รองรับไฟล์: JPG, PNG, PDF (สูงสุด 3
                                            ไฟล์)(ไม่เกิน 5 MB)</small>
                                    </div>
                                </div>
                                <input type="file" id="suggestions_files" name="suggestions_files[]"
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
                            <button type="button" id="submitSuggestionsBtn" class="btn modern-submit-btn"
                                onclick="handleSuggestionsSubmit(event)"
                                style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); border: none; color: white; padding: 1rem 2rem; border-radius: 15px; font-size: 1.1rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3); position: relative; overflow: hidden; min-width: 150px;">
                                <span style="position: relative; z-index: 2;">
                                    <i class="fas fa-paper-plane me-2"></i>ส่งความคิดเห็น
                                </span>
                                <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; z-index: 1;"
                                    class="btn-shine"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
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
        color: #4caf50;
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
        background: linear-gradient(135deg, #ffffff 0%, #e8f5e9 100%) !important;
    }

    .track-status-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4) !important;
        background: linear-gradient(135deg, #43a047 0%, #2e7d32 100%) !important;
        color: white !important;
        text-decoration: none !important;
    }

    .modern-submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(76, 175, 80, 0.4) !important;
        background: linear-gradient(135deg, #43a047 0%, #2e7d32 100%) !important;
    }

    .modern-submit-btn:hover .btn-shine {
        left: 100%;
    }

    .suggestions-form-check:hover {
        background: rgba(76, 175, 80, 0.05) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.1);
    }

    .form-check-input:checked {
        background-color: #4caf50;
        border-color: #4caf50;
    }

    .file-upload-wrapper:hover {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%) !important;
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.2) !important;
        transform: translateY(-2px);
        border-color: #4caf50 !important;
    }

    .file-upload-wrapper.drag-over {
        background: linear-gradient(135deg, #f1f8e9 0%, #e8f5e9 100%) !important;
        border-color: #4caf50 !important;
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
    .suggestions-modal-content {
        z-index: 9999 !important;
    }

    .suggestions-login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.5) !important;
        background: linear-gradient(135deg, #43a047 0%, #2e7d32 100%) !important;
    }

    .suggestions-guest-btn:hover {
        background: rgba(76, 175, 80, 0.15) !important;
        border-color: rgba(76, 175, 80, 0.5) !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3) !important;
    }

    /* User info display */
    #logged-in-user-info {
        border-radius: 15px;
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
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
    // *** JavaScript Code - Complete with Address System & ID Card Validation ***

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
                    <p style="color: #28a745; font-size: 1.1rem; margin-bottom: 1rem;">
                        <i class="fas fa-user-check me-2"></i>
                        ยินดีต้อนรับ <strong>${userInfo.name}</strong>
                    </p>
                    <div style="background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%); 
                                padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                        <p style="margin: 0; color: #2e7d32;">
                            <i class="fas fa-magic me-2"></i>
                            ข้อมูลของคุณจะถูกใส่ในฟอร์มโดยอัตโนมัติ
                        </p>
                    </div>
                </div>
            `,
                confirmButtonText: 'เริ่มส่งความคิดเห็น',
                confirmButtonColor: '#4caf50',
                timer: 5000,
                timerProgressBar: true
            });

            sessionStorage.removeItem('redirect_after_login');
        }
    }

    // เมื่อเอกสารโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('suggestionsForm');
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
        setupDetailValidation();
        updateFormFieldsBasedOnLoginStatus();

        const uploadWrapper = document.querySelector('.file-upload-wrapper');
        if (uploadWrapper) {
            uploadWrapper.addEventListener('click', function (e) {
                e.preventDefault();
                const fileInput = document.getElementById('suggestions_files');
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

        // console.log('✅ Suggestions form initialized with address system & ID card validation');
    });

    // *** ฟังก์ชัน Address System - คัดลอกจาก Queue ***
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

            //  console.log('✅ Address system initialized successfully');

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
                    //  console.log('✅ Zipcode field updated:', zipcode);
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

        // ใส่เฉพาะที่อยู่เพิ่มเติมใน suggestions_address
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
                document.getElementById('suggestionsForm').appendChild(hiddenField);
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
                icon.style.cssText = 'position: absolute; right: 45px; top: 50%; transform: translateY(-50%); color: #4caf50; z-index: 3;';
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
        const form = document.getElementById('suggestionsForm');
        if (form) {
            form.action = '<?= site_url('Suggestions/add_suggestions'); ?>';
            //  console.log('✅ Form action updated to:', form.action);
        }
    }

    // ตั้งค่าการตรวจสอบเบอร์โทร
    function setupPhoneValidation() {
        const phoneInput = document.getElementById('suggestions_phone');
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
        const idInput = document.getElementById('suggestions_number');
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

        const nameField = document.querySelector('input[name="suggestions_by"]');
        const phoneField = document.querySelector('input[name="suggestions_phone"]');
        const emailField = document.querySelector('input[name="suggestions_email"]');
        const idCardField = document.querySelector('input[name="suggestions_number"]');

        // ซ่อนฟิลด์ที่มีข้อมูลแล้ว
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

        // จัดการข้อมูลที่อยู่สำหรับ user ที่ login
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

            // console.log('✅ Using address from user account:', parsed);
        }

        showLoggedInUserInfo();
    }

    // แสดงข้อมูล user ที่ login
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
                border-radius: 15px; 
                background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%); 
                border: 1px solid rgba(76, 175, 80, 0.3);
                margin-bottom: 2rem;
                box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
                backdrop-filter: blur(10px);
            ">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div style="
                            width: 60px; 
                            height: 60px; 
                            background: linear-gradient(135deg, rgba(76, 175, 80, 0.15) 0%, rgba(129, 199, 132, 0.15) 100%); 
                            border-radius: 50%; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center;
                            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
                        ">
                            <i class="fas fa-user-check" style="font-size: 1.8rem; color: #4caf50;"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h5 class="mb-2" style="color: #2e7d32; font-weight: 600;">
                            <i class="fas fa-check-circle me-2"></i>ใช้ข้อมูลจากบัญชีของคุณ
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1" style="color: #2e7d32; font-size: 0.9rem;"><i class="fas fa-user me-2"></i><strong>ชื่อ:</strong> ${userName}</div>
                                <div class="mb-1" style="color: #2e7d32; font-size: 0.9rem;"><i class="fas fa-envelope me-2"></i><strong>อีเมล:</strong> ${userEmail}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1" style="color: #2e7d32; font-size: 0.9rem;"><i class="fas fa-phone me-2"></i><strong>เบอร์โทร:</strong> ${userPhone}</div>
                                <div class="mb-1" style="color: #2e7d32; font-size: 0.9rem;"><i class="fas fa-map-marker-alt me-2"></i><strong>ที่อยู่:</strong> ${userAddressText}</div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small style="color: #2e7d32; display: flex; align-items: center;">
                                <i class="fas fa-info-circle me-1"></i> 
                                ระบบจะใช้ข้อมูลจากบัญชีของคุณโดยอัตโนมัติ
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;

            const formContainer = document.getElementById('suggestions_form');
            const firstFormGroup = formContainer.querySelector('.form-group');
            if (firstFormGroup) {
                firstFormGroup.insertAdjacentHTML('beforebegin', userInfoHTML);
            }

            // console.log('✅ User info display created');

        } catch (error) {
            console.error('❌ Error showing user info:', error);
        }
    }

    // แสดงข้อความต้อนรับ
    function showWelcomeMessage() {
        if (isUserLoggedIn && userInfo && userInfo.name) {
            Swal.fire({
                icon: 'success',
                title: `ยินดีต้อนรับ ${userInfo.name}`,
                text: 'คุณสามารถส่งความคิดเห็นได้ทันที ข้อมูลของคุณจะถูกใช้โดยอัตโนมัติ',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: 'linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%)',
                color: '#2e7d32'
            });
        }
    }

    // แสดง modal สำหรับเลขบัตรประชาชน
    function showIdCardModal() {
        Swal.fire({
            title: '<span style="color: #4caf50;">กรอกเลขบัตรประจำตัวประชาชน</span>',
            html: `
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.25) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);">
                    <i class="fas fa-id-card" style="font-size: 2.5rem; color: #dc3545;"></i>
                </div>
                <p style="color: #666; font-size: 1.1rem; line-height: 1.6; margin-bottom: 1rem;">
                    <strong style="color: #dc3545;">จำเป็นต่อการให้บริการ</strong><br>
                    กรุณากรอกเลขบัตรประจำตัวประชาชน 13 หลัก<br>
                    เพื่อใช้ในการส่งความคิดเห็นและติดตามสถานะ
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
            confirmButtonColor: '#4caf50',
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
                showAlert('info', 'การส่งความคิดเห็นถูกยกเลิก', 'จำเป็นต้องมีเลขบัตรประจำตัวประชาชนเพื่อการให้บริการ');
            }
        });
    }

    // อัปเดตเลขบัตรประชาชน
    function updateUserIdCard(idCardNumber) {
        const loadingAlert = Swal.fire({
            title: 'กำลังบันทึก...',
            html: '<div style="text-align: center;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #4caf50;"></i><br><br>กำลังอัพเดทข้อมูลของคุณ</div>',
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

                    const idCardField = document.querySelector('input[name="suggestions_number"]');
                    if (idCardField) {
                        idCardField.value = idCardNumber;
                        idCardField.readOnly = true;
                        idCardField.style.backgroundColor = '#f8f9fa';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ!',
                        text: 'อัพเดทเลขบัตรประจำตัวประชาชนแล้ว ดำเนินการส่งความคิดเห็นต่อ',
                        confirmButtonColor: '#4caf50',
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

        const fileInput = document.getElementById('suggestions_files');
        fileInput.files = e.dataTransfer.files;
        handleFileSelect(fileInput);
    }

    // Form submission
    function handleSuggestionsSubmit(event) {
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

    async function submitForm() {
        try {
            const form = document.getElementById('suggestionsForm');

            // *** เพิ่มการตรวจสอบรายละเอียดเพิ่มเติมแบบเข้มงวด ***
            const suggestionDetail = document.querySelector('textarea[name="suggestions_detail"]');
            if (suggestionDetail) {
                const detailValue = suggestionDetail.value.trim();
                const minDetailLength = 10;

                if (detailValue.length < minDetailLength) {
                    showAlert('warning',
                        'รายละเอียดไม่เพียงพอ',
                        `กรุณากรอกรายละเอียดเพิ่มเติมอย่างน้อย ${minDetailLength} ตัวอักษร (ปัจจุบัน ${detailValue.length} ตัวอักษร)`
                    );
                    suggestionDetail.focus();
                    suggestionDetail.classList.add('is-invalid');
                    return;
                }
            }

            if (!form || !form.checkValidity()) {
                console.warn('❌ Form validation failed');

                const fieldNameMapping = {
                    'suggestions_topic': 'เรื่องที่ต้องการเสนอแนะ',
                    'suggestions_by': 'ชื่อ-นามสกุล',
                    'suggestions_phone': 'เบอร์โทรศัพท์',
                    'suggestions_email': 'อีเมล',
                    'suggestions_number': 'เลขบัตรประชาชน',
                    'suggestions_address': 'ที่อยู่',
                    'suggestions_detail': 'รายละเอียด', // เพิ่มการแมป
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
                    // console.log('✅ Logged in user - proceeding despite form validity check');
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
                const idCardValue = document.querySelector('input[name="suggestions_number"]')?.value?.trim();

                if (!idCardValue || !/^\d{13}$/.test(idCardValue)) {
                    showAlert('error', 'เลขบัตรประชาชนไม่ถูกต้อง', 'กรุณากรอกเลขบัตรประจำตัวประชาชน 13 หลัก (ตัวเลขเท่านั้น)');
                    document.querySelector('input[name="suggestions_number"]')?.focus();
                    return;
                }

                if (!validateThaiIdCard(idCardValue)) {
                    showAlert('error', 'เลขบัตรประชาชนไม่ถูกต้อง', 'เลขบัตรประจำตัวประชาชนที่กรอกไม่ถูกต้องตามมาตรฐาน กรุณาตรวจสอบอีกครั้ง');
                    document.querySelector('input[name="suggestions_number"]')?.focus();
                    return;
                }
            }

            // *** เพิ่ม: ตรวจสอบและสร้าง reCAPTCHA Token ***
            console.log('🔐 Checking reCAPTCHA status for suggestions...');
            const recaptchaStatus = checkRecaptchaStatus();
            let recaptchaToken = null;

            if (recaptchaStatus.ready && recaptchaStatus.siteKey) {
                try {
                    console.log('🔄 Generating reCAPTCHA token for suggestions...');
                    recaptchaToken = await generateRecaptchaToken('suggestions_submit');
                    console.log('✅ reCAPTCHA token generated for suggestions');
                } catch (error) {
                    console.error('❌ reCAPTCHA token generation failed:', error);

                    showAlert('error', 'ไม่สามารถยืนยันตัวตนได้', 'การยืนยันความปลอดภัยล้มเหลว กรุณารีเฟรชหน้าและลองใหม่อีกครั้ง');
                    return;
                }
            } else if (!recaptchaStatus.skipForDev) {
                console.warn('⚠️ reCAPTCHA not ready for suggestions - proceeding without verification');
            }

            if (formSubmitting) return;
            formSubmitting = true;

            const submitBtn = document.getElementById('submitSuggestionsBtn');
            const originalContent = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังส่ง...';

            const formData = new FormData();
            const formElements = form.elements;

            // *** เพิ่ม reCAPTCHA Token และข้อมูลที่เกี่ยวข้อง ***
            if (recaptchaToken) {
                formData.append('g-recaptcha-response', recaptchaToken);
                formData.append('recaptcha_action', 'suggestions_submit');
                formData.append('recaptcha_source', 'suggestions_form');
                console.log('🔐 Added reCAPTCHA token to suggestions submission');
            }

            // เพิ่มข้อมูลเพิ่มเติมสำหรับ reCAPTCHA verification
            formData.append('client_timestamp', Date.now());
            formData.append('user_agent_info', navigator.userAgent);

            const isDevelopment = window.SKIP_RECAPTCHA_FOR_DEV || false;
            if (isDevelopment) {
                formData.append('dev_mode', '1');
            }

            const userTypeDetected = isUserLoggedIn ? 'citizen' : 'guest';
            formData.append('user_type_detected', userTypeDetected);

            // เพิ่มข้อมูลฟอร์ม
            for (let element of formElements) {
                if (element.type === 'file' || element.type === 'button' || element.type === 'submit') continue;
                if (element.name && element.value !== '') {
                    formData.append(element.name, element.value);
                }
            }

            // เพิ่มประเภทความคิดเห็น
            const suggestionType = document.querySelector('input[name="suggestion_type"]:checked');
            if (suggestionType) {
                formData.append('suggestion_type', suggestionType.value);
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
                    formData.append('suggestions_files[]', file, file.name);
                });
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
                                    message: 'ส่งความคิดเห็นสำเร็จ',
                                    suggestion_id: 'S' + Date.now().toString().slice(-6)
                                };
                            }
                        });
                    }
                })
                .then(jsonResponse => {
                    // *** ตรวจสอบ reCAPTCHA errors ***
                    if (jsonResponse.success === false) {
                        const errorType = jsonResponse.error_type || 'unknown';
                        let errorMessage = jsonResponse.message || 'เกิดข้อผิดพลาดในการส่งความคิดเห็น';

                        if (errorType === 'recaptcha_missing') {
                            errorMessage = 'ไม่พบข้อมูลการยืนยันตัวตน กรุณารีเฟรชหน้าและลองใหม่อีกครั้ง';
                        } else if (errorType === 'recaptcha_failed') {
                            errorMessage = 'การยืนยันตัวตนไม่ผ่าน กรุณาลองใหม่อีกครั้ง';
                        } else if (errorType === 'vulgar_content') {
                            errorMessage = 'พบคำไม่เหมาะสมในความคิดเห็น กรุณาแก้ไขข้อความ';
                        } else if (errorType === 'url_content') {
                            errorMessage = 'ไม่อนุญาตให้มี URL หรือลิงก์ในความคิดเห็น';
                        }

                        showAlert('error', 'ไม่สามารถส่งความคิดเห็นได้', errorMessage);
                        return;
                    }

                    if (jsonResponse.success) {
                        const suggestionId = jsonResponse.suggestion_id || 'S' + Date.now().toString().slice(-6);

                        // *** แยกข้อความตาม user type ***
                        const userTypeText = isUserLoggedIn ?
                            'คุณสามารถติดตามสถานะได้ในหน้าบัญชีของคุณ' :
                            'คุณสามารถติดตามสถานะได้ด้วยหมายเลขอ้างอิงนี้';

                        const confirmButtonText = isUserLoggedIn ?
                            '<i class="fas fa-user-check me-2"></i>ไปยังหน้าบัญชีของฉัน' :
                            '<i class="fas fa-search me-2"></i>ติดตามสถานะ';

                        // *** เพิ่มข้อมูล reCAPTCHA ใน success message ***
                        const recaptchaInfo = jsonResponse.recaptcha_verified ?
                            '<div style="background: rgba(40, 167, 69, 0.1); padding: 0.5rem 1rem; border-radius: 8px; margin: 0.5rem 0;"><small style="color: #28a745;"><i class="fas fa-shield-check me-1"></i>ยืนยันความปลอดภัยแล้ว</small></div>' :
                            '';

                        Swal.fire({
                            icon: 'success',
                            title: 'ส่งความคิดเห็นสำเร็จ!',
                            html: `
                        <div style="text-align: center;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, rgba(76, 175, 80, 0.15) 0%, rgba(129, 199, 132, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);">
                                <i class="fas fa-check-circle" style="font-size: 2.5rem; color: #4caf50;"></i>
                            </div>
                            
                            <div style="background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%); 
                                        padding: 1.5rem; border-radius: 15px; margin: 1rem 0;
                                        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);">
                                <h3 style="color: #2e7d32; margin-bottom: 0.5rem; font-weight: 600;">
                                    📝 หมายเลขอ้างอิง
                                </h3>
                                <div style="font-size: 2.2rem; font-weight: bold; color: #2e7d32; 
                                           letter-spacing: 2px; margin: 1rem 0;">
                                    ${suggestionId}
                                </div>
                                <button onclick="copyRefId('${suggestionId}')" 
                                        class="btn btn-sm btn-outline-success mt-2"
                                        style="border-radius: 12px; padding: 0.5rem 1rem; font-weight: 600;">
                                    <i class="fas fa-copy me-1"></i> คัดลอกหมายเลข
                                </button>
                            </div>
                            
                            ${recaptchaInfo}
                            
                            <div style="background: rgba(23, 162, 184, 0.1); padding: 1rem; border-radius: 12px; margin: 1rem 0;">
                                <p style="color: #17a2b8; margin: 0; font-size: 0.95rem;">
                                    <i class="fas fa-info-circle me-2"></i>${userTypeText}
                                </p>
                            </div>
                            
                            <p style="color: #666; margin-top: 1rem;">ขอบคุณสำหรับความคิดเห็นของคุณ</p>
                        </div>
                    `,
                            confirmButtonText: confirmButtonText,
                            confirmButtonColor: '#4caf50',
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
                                    // User ที่ login -> ไปหน้า my_suggestions
                                    window.location.href = `<?= site_url('Suggestions/my_suggestions'); ?>`;
                                } else {
                                    // Guest user -> ไปหน้า follow_suggestions พร้อม suggestion_id
                                    window.location.href = `<?= site_url('Suggestions/follow_suggestions'); ?>?ref=${suggestionId}`;
                                }
                            } else {
                                resetForm();
                            }
                        });
                    } else {
                        throw new Error(jsonResponse.message || 'เกิดข้อผิดพลาด');
                    }
                })
                .catch(error => {
                    console.error('Submit error:', error);
                    showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถส่งความคิดเห็นได้ กรุณาลองใหม่อีกครั้ง');
                })
                .finally(() => {
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalContent;
                        formSubmitting = false;
                    }, 100);
                });

        } catch (error) {
            console.error('Submit form error:', error);
            showAlert('error', 'เกิดข้อผิดพลาดในระบบ', 'ไม่สามารถส่งฟอร์มได้ กรุณาลองใหม่');
            formSubmitting = false;
        }
    }

    function resetForm() {
        const form = document.getElementById('suggestionsForm');
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

        // รีเซ็ตประเภทความคิดเห็น
        document.getElementById('suggestion').checked = true;
    }

    function copyRefId(refId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(refId).then(() => {
                showAlert('success', 'คัดลอกแล้ว', `คัดลอกหมายเลขอ้างอิง ${refId} แล้ว`, 2000);
            });
        }
    }

    // *** เพิ่มฟังก์ชัน reCAPTCHA helpers สำหรับ suggestions ***
    function checkRecaptchaStatus() {
        return {
            ready: window.recaptchaReady || false,
            siteKey: window.RECAPTCHA_SITE_KEY || null,
            skipForDev: window.SKIP_RECAPTCHA_FOR_DEV || false
        };
    }

    async function generateRecaptchaToken(action = 'suggestions_submit') {
        if (!window.recaptchaReady || !window.RECAPTCHA_SITE_KEY) {
            console.warn('⚠️ reCAPTCHA not ready or site key missing');
            return null;
        }

        try {
            const token = await grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                action: action
            });
            console.log('✅ reCAPTCHA token generated for action:', action);
            return token;
        } catch (error) {
            console.error('❌ Error generating reCAPTCHA token:', error);
            return null;
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////
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
        showAlert('info', 'ดำเนินการต่อโดยไม่เข้าสู่ระบบ', 'คุณสามารถกรอกข้อมูลและส่งความคิดเห็นได้แล้ว', 2000);
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
            confirmButtonColor: '#4caf50',
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



    // *** เพิ่มฟังก์ชันใหม่: ตั้งค่าการตรวจสอบรายละเอียดเพิ่มเติม ***
    function setupDetailValidation() {
        const detailTextarea = document.getElementById('suggestions_detail');
        if (detailTextarea) {
            const minLength = 10;
            const maxLength = 2000;

            // สร้าง counter element
            let counterElement = document.getElementById('detail_counter');
            if (!counterElement) {
                counterElement = document.createElement('div');
                counterElement.id = 'detail_counter';
                counterElement.className = 'char-counter';
                counterElement.style.cssText = 'font-size: 0.9rem; color: #6c757d; text-align: right; margin-top: 0.5rem;';
                detailTextarea.parentNode.appendChild(counterElement);
            }

            // อัพเดท counter
            function updateCounter() {
                const currentLength = detailTextarea.value.trim().length;

                if (currentLength < minLength) {
                    counterElement.textContent = `${currentLength}/${maxLength} ตัวอักษร (ต้องการอีก ${minLength - currentLength} ตัวอักษร)`;
                    counterElement.style.color = '#dc3545';
                } else {
                    counterElement.textContent = `${currentLength}/${maxLength} ตัวอักษร`;
                    counterElement.style.color = '#28a745';
                }
            }

            // Real-time validation
            detailTextarea.addEventListener('input', function (e) {
                const value = e.target.value.trim();
                const length = value.length;

                updateCounter();

                // ลบ class เดิม
                e.target.classList.remove('is-valid', 'is-invalid');

                if (length === 0) {
                    // ไม่แสดงอะไรถ้ายังไม่กรอก
                } else if (length < minLength) {
                    e.target.classList.add('is-invalid');

                    // แสดงข้อความ error
                    let feedback = document.getElementById('suggestions_detail_feedback');
                    if (feedback) {
                        feedback.textContent = `กรุณากรอกรายละเอียดอย่างน้อย ${minLength} ตัวอักษร (ปัจจุบัน ${length} ตัวอักษร)`;
                        feedback.style.display = 'block';
                    }
                } else if (length > maxLength) {
                    e.target.classList.add('is-invalid');

                    let feedback = document.getElementById('suggestions_detail_feedback');
                    if (feedback) {
                        feedback.textContent = `รายละเอียดยาวเกินไป (สูงสุด ${maxLength} ตัวอักษร)`;
                        feedback.style.display = 'block';
                    }
                } else {
                    e.target.classList.add('is-valid');

                    // ซ่อนข้อความ error
                    let feedback = document.getElementById('suggestions_detail_feedback');
                    if (feedback) {
                        feedback.style.display = 'none';
                    }
                }
            });

            // เรียกครั้งแรก
            updateCounter();

            // console.log('✅ Detail validation setup completed');
        }
    }



</script>