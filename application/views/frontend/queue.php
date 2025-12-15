<div class="text-center pages-head">
    <span class="font-pages-head">จองคิวติดต่อราชการ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<!-- Modal สำหรับการยืนยันการจองคิวโดยไม่เข้าสู่ระบบ -->
<div class="modal fade" id="guestConfirmModal" tabindex="-1" aria-labelledby="guestConfirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"
            style="border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(102, 126, 234, 0.2), 0 8px 25px rgba(0,0,0,0.08); background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%); overflow: hidden;">
            <div class="modal-header"
                style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); color: #2c3e50; border-radius: 20px 20px 0 0; border-bottom: 1px solid rgba(102, 126, 234, 0.2); backdrop-filter: blur(10px);">
                <h5 class="modal-title" id="guestConfirmModalLabel"
                    style="font-weight: 600; color: #667eea; width: 100%; text-align: center;">
                    <i class="fas fa-calendar-check me-2" style="color: #667eea;"></i>ยินดีต้อนรับสู่ระบบจองคิว
                </h5>
            </div>
            <div class="modal-body text-center"
                style="padding: 2.5rem; background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%);">
                <div class="mb-4">
                    <div
                        style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
                        <i class="fas fa-user-shield"
                            style="font-size: 2.5rem; color: #667eea; text-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);"></i>
                    </div>
                </div>
                <h5 class="mb-3" style="color: #2c3e50; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    เริ่มต้นการใช้งาน</h5>
                <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.6; color: #6c757d;">
                    เข้าสู่ระบบเพื่อติดตามสถานะการจองคิวและได้รับการแจ้งเตือน สะดวกรวดเร็ว ปลอดภัย
                    ไม่มีไครค้นหาคิวของคุณได้ หรือดำเนินการต่อโดยไม่ต้องเข้าสู่ระบบ
                    ไม่ปลอดภัยบุคคลอื่นสามารถค้นหาคิวได้จากหน้าติดตามคิว</p>

                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-lg" onclick="redirectToLogin()"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4); transition: all 0.3s ease; font-size: 1.1rem;">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                    </button>
                    <button type="button" class="btn btn-lg" onclick="proceedAsGuest()"
                        style="background: rgba(102, 126, 234, 0.08); border: 2px solid rgba(102, 126, 234, 0.3); color: #667eea; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; transition: all 0.3s ease; font-size: 1.1rem; backdrop-filter: blur(10px);">
                        <i class="fas fa-calendar-plus me-2"></i>ดำเนินการต่อโดยไม่เข้าสู่ระบบ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center pages-head">
    <span class="font-pages-head"
        style="font-size: 2.8rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(108, 117, 125, 0.2);">จองคิวติดต่อราชการ</span>
</div>

<div class="bg-pages" style="background: #ffffff; min-height: 100vh; padding: 2rem 0;">
    <div class="container-pages-news" style="position: relative; z-index: 10;">
        <div class="container-pages-news mb-5 mt-5"
            style="position: relative; z-index: 10; background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 1000px; overflow: hidden;"
            id="queue_form">

            <!-- เพิ่ม decorative element -->
            <div
                style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #667eea, #764ba2, #667eea); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;">
            </div>

            <!-- ปุ่มติดตามสถานะ -->
            <div class="d-flex justify-content-end mb-3">
                <a href="<?php echo site_url('Queue/follow_queue'); ?>" class="btn track-status-btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                          border: none; 
                          color: white; 
                          padding: 0.7rem 1.5rem; 
                          border-radius: 12px; 
                          font-size: 0.95rem; 
                          font-weight: 600; 
                          transition: all 0.3s ease; 
                          box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); 
                          text-decoration: none;
                          position: relative;
                          overflow: hidden;">
                    <span style="position: relative; z-index: 2;">
                        <i class="fas fa-search me-2"></i>ติดตามสถานะจองคิว
                    </span>
                </a>
            </div>

            <div class="underline">
                <form id="queueForm" action="<?php echo site_url('Pages/add_queue'); ?>" method="post"
                    class="form-horizontal" enctype="multipart/form-data" onsubmit="return false;">
                    <input type="hidden" name="form_token" id="formToken" value="">
                    <br>

                    <!-- เรื่องที่ต้องการติดต่อ -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper"
                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                            <label class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-clipboard-list me-2"
                                    style="color: #667eea;"></i>เรื่องที่ต้องการติดต่อ<span
                                    style="color: #dc3545; margin-left: 0.2rem;">*</span>
                            </label>
                        </div>
                        <div class="col-sm-12">
                            <input type="text" name="queue_topic" class="form-control" required
                                placeholder="กรอกเรื่องที่ต้องการติดต่อ..."
                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                            <div class="invalid-feedback" id="queue_topic_feedback"></div>
                        </div>
                    </div>

                    <!-- วันที่และช่วงเวลา -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper"
                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                            <label class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-calendar me-2"
                                    style="color: #667eea;"></i>วันที่และช่วงเวลาที่ต้องการ<span
                                    style="color: #dc3545; margin-left: 0.2rem;">*</span>
                            </label>
                        </div>
                        <div class="row">
                            <!-- วันที่ -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold mb-2">
                                    <i class="fas fa-calendar-day me-2" style="color: #667eea;"></i>วันที่
                                </label>
                                <input type="date" name="queue_date_temp" id="queue_date" class="form-control" required
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="queue_date_feedback"></div>
                                <!-- แสดงวันที่เป็นไทย -->
                                <small class="text-success mt-1 d-block" id="thai_date_display"
                                    style="font-weight: 500;"></small>
                            </div>

                            <!-- ช่วงเวลา -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold mb-2">
                                    <i class="fas fa-clock me-2" style="color: #667eea;"></i>ช่วงเวลา
                                </label>
                                <!-- ช่วงเวลาที่เลือก (hidden input) -->
                                <input type="hidden" name="queue_time" id="queue_time" required>

                                <!-- Time Slots Container -->
                                <div id="time_slots_container" class="time-slots-container">
                                    <div class="time-slot" data-time="08:00-09:00">
                                        <i class="fas fa-sun"></i> 8:00 - 9:00
                                    </div>
                                    <div class="time-slot" data-time="09:00-10:00">
                                        <i class="fas fa-sun"></i> 9:00 - 10:00
                                    </div>
                                    <div class="time-slot" data-time="10:00-11:00">
                                        <i class="fas fa-sun"></i> 10:00 - 11:00
                                    </div>
                                    <div class="time-slot" data-time="11:00-12:00">
                                        <i class="fas fa-sun"></i> 11:00 - 12:00
                                    </div>
                                    <div class="time-slot" data-time="13:00-14:00">
                                        <i class="fas fa-sun"></i> 13:00 - 14:00
                                    </div>
                                    <div class="time-slot" data-time="14:00-15:00">
                                        <i class="fas fa-sun"></i> 14:00 - 15:00
                                    </div>
                                    <div class="time-slot" data-time="15:00-16:00">
                                        <i class="fas fa-sun"></i> 15:00 - 16:00
                                    </div>
                                </div>

                                <div class="invalid-feedback" id="queue_time_feedback"></div>
                                <!-- แสดงช่วงเวลาที่เลือก -->
                                <small class="text-success mt-2 d-block" id="selected_time_display"
                                    style="display: none !important; font-weight: 600;">
                                    <i class="fas fa-check-circle me-1"></i>เลือกช่วงเวลา: <span
                                        id="selected_time_text"></span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <!-- ชื่อ-นามสกุล -->
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-user me-2" style="color: #667eea;"></i>ชื่อ-นามสกุล<span
                                            style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-12">
                                    <input type="text" name="queue_by" id="queue_by" class="form-control" required
                                        placeholder="เช่น นาย สมชาย ใจดี"
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                    <div class="invalid-feedback" id="queue_by_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <!-- เบอร์โทรศัพท์ -->
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-phone me-2" style="color: #667eea;"></i>เบอร์โทรศัพท์<span
                                            style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-12">
                                    <input type="tel" name="queue_phone" id="queue_phone" class="form-control" required
                                        placeholder="เช่น 0812345678" pattern="\d{10}"
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                    <div class="invalid-feedback" id="queue_phone_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- อีเมล -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper"
                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                            <label class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-envelope me-2" style="color: #667eea;"></i>อีเมล<small
                                    style="color: #6c757d; font-weight: 400; margin-left: 0.5rem;">(Optional)</small>
                            </label>
                        </div>
                        <div class="col-sm-12">
                            <input type="email" name="queue_email" id="queue_email" class="form-control"
                                placeholder="เช่น somchai@gmail.com"
                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                            <div class="invalid-feedback" id="queue_email_feedback"></div>
                        </div>
                    </div>

                    <!-- เลขบัตรประชาชน -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper"
                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                            <label class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-id-card me-2" style="color: #667eea;"></i>เลขบัตรประจำตัวประชาชน<span
                                    style="color: #dc3545; margin-left: 0.2rem;">*</span>
                            </label>
                        </div>
                        <div class="col-sm-12">
                            <input type="text" name="queue_number" id="queue_number" class="form-control" required
                                placeholder="เลขบัตรประจำตัวประชาชน 13 หลัก" maxlength="13"
                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                            <div class="invalid-feedback" id="queue_number_feedback"></div>
                        </div>
                    </div>

                    <!-- *** ระบบที่อยู่แบบละเอียด *** -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper"
                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                            <label class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-map-marker-alt me-2" style="color: #667eea;"></i>ที่อยู่<span
                                    style="color: #dc3545; margin-left: 0.2rem;">*</span>
                            </label>
                        </div>

                        <!-- ที่อยู่เพิ่มเติม (บังคับกรอก) -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-wrapper">
                                    <input type="text" id="additional_address_field" class="form-control" required
                                        placeholder="กรอกที่อยู่เพิ่มเติม (บ้านเลขที่ ซอย ถนน หมู่บ้าน) *"
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <i class="fas fa-map-marker-alt input-icon"
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #667eea;"></i>
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
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <i class="fas fa-mail-bulk input-icon"
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #667eea;"></i>
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
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <i class="fas fa-map input-icon"
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #667eea;"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-wrapper">
                                    <select id="amphoe_field" class="form-control" disabled
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                        <option value="">เลือกอำเภอ</option>
                                    </select>
                                    <i class="fas fa-city input-icon"
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #667eea;"></i>
                                </div>
                            </div>
                        </div>

                        <!-- ตำบล -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-home me-2" style="color: #667eea;"></i>ตำบล<span
                                            style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                    </label>
                                </div>
                                <div class="input-wrapper">
                                    <select id="district_field" name="district_field" class="form-control" disabled
                                        required
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                        <option value="">เลือกตำบล *</option>
                                    </select>
                                    <i class="fas fa-home input-icon"
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #667eea;"></i>
                                </div>
                                <div class="invalid-feedback" id="district_field_feedback"></div>
                            </div>
                        </div>

                        <!-- ที่อยู่รวม (ซ่อน - ส่งไปยัง queue_address) -->
                        <input type="hidden" name="queue_address" id="full_address_field" value="">

                        <!-- *** เพิ่มใหม่: ซ่อน hidden fields สำหรับข้อมูลที่อยู่แยก *** -->
                        <input type="hidden" name="guest_province" id="guest_province_field" value="">
                        <input type="hidden" name="guest_amphoe" id="guest_amphoe_field" value="">
                        <input type="hidden" name="guest_district" id="guest_district_field" value="">
                        <input type="hidden" name="guest_zipcode" id="guest_zipcode_field" value="">

                        <!-- แสดงที่อยู่ที่รวมแล้ว -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" id="address_preview"
                                    style="display: none; border-radius: 15px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border: 1px solid rgba(102, 126, 234, 0.3);">
                                    <strong><i class="fas fa-eye"></i> ที่อยู่ที่จะบันทึก:</strong>
                                    <div id="address_preview_text"></div>
                                </div>
                            </div>
                        </div>

                        <div class="invalid-feedback" id="queue_address_feedback"></div>
                    </div>

                    <!-- รายละเอียด -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper"
                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                            <label for="queueDetailTextarea" class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-align-left me-2" style="color: #667eea;"></i>รายละเอียดเพิ่มเติม<span
                                    style="color: #dc3545; margin-left: 0.2rem;">*</span>
                            </label>
                        </div>
                        <div class="col-sm-12">
                            <textarea name="queue_detail" class="form-control" id="queueDetailTextarea" rows="4"
                                required placeholder="กรอกรายละเอียดเพิ่มเติม..."
                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); resize: vertical;"></textarea>
                            <div class="invalid-feedback" id="queue_detail_feedback"></div>
                        </div>
                    </div>

                    <br>

                    <div class="row" style="padding-bottom: 20px;">
                        <!-- ไฟล์แนบ -->
                        <div class="col-9">
                            <div class="form-group">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-paperclip me-2" style="color: #667eea;"></i>ไฟล์แนบ<small
                                            style="color: #6c757d; font-weight: 400; margin-left: 0.5rem;">(รูปภาพ หรือ
                                            PDF)</small>
                                    </label>
                                </div>
                                <div class="col-sm-12">
                                    <!-- File Upload Zone -->
                                    <div class="file-upload-wrapper"
                                        style="border: 2px dashed #dee2e6; border-radius: 15px; padding: 1.5rem; text-align: center; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); transition: all 0.3s ease; cursor: pointer; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15);"
                                        ondrop="handleDrop(event)" ondragover="handleDragOver(event)"
                                        ondragenter="handleDragEnter(event)" ondragleave="handleDragLeave(event)">
                                        <div id="upload-placeholder" class="upload-placeholder">
                                            <i class="fas fa-cloud-upload-alt"
                                                style="font-size: 2rem; color: #667eea; margin-bottom: 0.5rem;"></i>
                                            <p style="margin: 0; color: #6c757d; font-size: 1rem;">คลิกเพื่อเลือกไฟล์
                                                หรือลากไฟล์มาวางที่นี่</p>
                                            <small class="text-muted mt-2 d-block">รองรับไฟล์: JPG, PNG, PDF (สูงสุด 3
                                                ไฟล์)(ไม่เกิน 5 MB)</small>
                                        </div>
                                    </div>
                                    <input type="file" id="queue_files" name="queue_files[]" class="form-control"
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
                                                onclick="clearAllFiles()"
                                                style="border-radius: 8px; font-size: 0.8rem;">
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
                                <button type="button" id="submitQueueBtn" class="btn modern-submit-btn"
                                    onclick="handleQueueSubmit(event)"
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; padding: 1rem 2rem; border-radius: 15px; font-size: 1.1rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden; min-width: 150px;">
                                    <span style="position: relative; z-index: 2;">
                                        <i class="fas fa-calendar-plus me-2"></i>จองคิว
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
        color: #667eea;
        pointer-events: none;
        z-index: 2;
    }

    /* Time Slots Styling */
    .time-slots-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .time-slot {
        padding: 0.75rem 1rem;
        border: 2px solid rgba(102, 126, 234, 0.2);
        border-radius: 12px;
        text-align: center;
        cursor: pointer;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        transition: all 0.3s ease;
        font-size: 0.9rem;
        font-weight: 500;
        color: #495057;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        position: relative;
        overflow: hidden;
    }

    .time-slot:hover {
        border-color: #667eea;
        background: linear-gradient(135deg, #f0f4ff 0%, #e6edff 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
        color: #667eea;
    }

    .time-slot.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        animation: timeSlotSelect 0.3s ease;
    }

    .time-slot.selected:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    }

    .time-slot i {
        margin-right: 0.5rem;
        font-size: 0.8rem;
    }

    .time-slot.selected i {
        color: #ffd700;
    }

    /* เพิ่ม animation สำหรับ time slots */
    @keyframes timeSlotSelect {
        0% {
            transform: scale(1) translateY(-2px);
        }

        50% {
            transform: scale(1.05) translateY(-2px);
        }

        100% {
            transform: scale(1) translateY(-2px);
        }
    }

    /* Custom date input styling */
    input[type="date"] {
        position: relative;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        color: #667eea;
        cursor: pointer;
        font-size: 1.2rem;
    }

    /* Thai date display styling */
    #thai_date_display {
        color: #28a745;
        font-weight: 500;
        font-style: italic;
    }

    #selected_time_display {
        font-weight: 600;
    }

    .track-status-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%) !important;
        color: white !important;
        text-decoration: none !important;
    }

    .form-label-wrapper:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.12) 0%, rgba(118, 75, 162, 0.12) 100%) !important;
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.2) !important;
        transform: translateY(-2px);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: transparent !important;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25) !important;
        transform: translateY(-1px);
        background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%) !important;
    }

    .modern-submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4) !important;
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%) !important;
    }

    .modern-submit-btn:hover .btn-shine {
        left: 100%;
    }

    .file-upload-wrapper {
        transition: all 0.3s ease;
    }

    .file-upload-wrapper:hover {
        background: linear-gradient(135deg, #f0f4ff 0%, #e6edff 100%) !important;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2) !important;
        transform: translateY(-2px);
        border-color: #667eea !important;
    }

    .file-upload-wrapper.drag-over {
        background: linear-gradient(135deg, #e6edff 0%, #dde6ff 100%) !important;
        border-color: #667eea !important;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3) !important;
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .time-slots-container {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }

        .time-slot {
            padding: 0.6rem 0.8rem;
            font-size: 0.85rem;
        }

        .font-pages-head {
            font-size: 2rem !important;
        }

        .container-pages-news {
            margin: 0 1rem !important;
            padding: 1.5rem !important;
        }

        .row .col-md-6 {
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




    /* *** เพิ่มใน CSS ส่วน <style> ของหน้า queue form *** */

    /* Validation Error Effects */
    .form-control.validation-error,
    .form-select.validation-error {
        border: 2px solid #ff5722 !important;
        box-shadow: 0 0 15px rgba(255, 87, 34, 0.3) !important;
        background-color: rgba(255, 87, 34, 0.05) !important;
        animation: shakeError 0.5s ease-in-out;
    }

    .form-control.validation-error:focus,
    .form-select.validation-error:focus {
        border-color: #ff5722 !important;
        box-shadow: 0 0 20px rgba(255, 87, 34, 0.4) !important;
    }

    /* Shake Animation for Error Fields */
    @keyframes shakeError {

        0%,
        100% {
            transform: translateX(0);
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            transform: translateX(-5px);
        }

        20%,
        40%,
        60%,
        80% {
            transform: translateX(5px);
        }
    }

    /* Enhanced Time Slots Error State */
    .time-slots-container.error {
        border: 2px solid #ff5722 !important;
        background: linear-gradient(135deg, rgba(255, 87, 34, 0.1) 0%, rgba(255, 87, 34, 0.05) 100%) !important;
        animation: pulseError 1s ease-in-out infinite alternate;
    }

    @keyframes pulseError {
        from {
            box-shadow: 0 0 10px rgba(255, 87, 34, 0.3);
        }

        to {
            box-shadow: 0 0 20px rgba(255, 87, 34, 0.5);
        }
    }

    /* Help Text Animations */
    .help-text {
        display: block;
        margin-top: 0.5rem;
        padding: 0.5rem 0.75rem;
        background: linear-gradient(135deg, rgba(255, 87, 34, 0.1) 0%, rgba(255, 87, 34, 0.05) 100%);
        border-left: 3px solid #ff5722;
        border-radius: 5px;
        font-size: 0.9rem;
        animation: slideInDown 0.3s ease-out;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Enhanced Modal Styles for SweetAlert */
    .swal2-popup.validation-error-modal {
        border-radius: 20px !important;
        border: none !important;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
        backdrop-filter: blur(10px) !important;
        overflow: hidden !important;
    }

    .swal2-popup.validation-error-modal .swal2-title {
        font-size: 1.5rem !important;
        font-weight: 600 !important;
        color: #333 !important;
        margin-bottom: 1rem !important;
    }

    .swal2-popup.validation-error-modal .swal2-html-container {
        padding: 0 !important;
        margin: 0 !important;
    }

    .swal2-popup.validation-error-modal .swal2-confirm {
        border-radius: 12px !important;
        font-weight: 600 !important;
        padding: 0.75rem 1.5rem !important;
        transition: all 0.3s ease !important;
    }

    .swal2-popup.validation-error-modal .swal2-confirm:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    /* *** เพิ่ม CSS สำหรับ Toast Notification *** */
    .swal2-popup.field-focus-toast {
        border-radius: 12px !important;
        font-size: 0.9rem !important;
        padding: 0.75rem 1rem !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        border-left: 4px solid #667eea !important;
    }

    .swal2-popup.field-focus-toast .swal2-title {
        font-size: 0.95rem !important;
        margin: 0 !important;
        color: #333 !important;
    }

    /* *** Animation สำหรับ Modal Close *** */
    .swal2-popup.swal2-hide {
        animation: swal2-hide-custom 0.2s ease-out forwards !important;
    }

    @keyframes swal2-hide-custom {
        0% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }

        100% {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }
    }

    /* Focus Ring Enhancement */
    .form-control:focus,
    .form-select:focus {
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25) !important;
        border-color: #667eea !important;
        transform: translateY(-1px);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    /* Special Focus for Required Fields */
    .form-control[required]:focus,
    .form-select[required]:focus {
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25),
            0 0 10px rgba(102, 126, 234, 0.15) !important;
    }

    /* Address Field Focus Enhancement */
    #zipcode_field:focus {
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25),
            0 0 15px rgba(102, 126, 234, 0.2) !important;
        border-color: #667eea !important;
        background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%) !important;
    }

    #district_field:focus {
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25) !important;
        border-color: #667eea !important;
    }

    /* Time Slot Focus Enhancement */
    .time-slot:focus,
    .time-slot.focused {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25),
            0 4px 15px rgba(102, 126, 234, 0.2) !important;
        transform: translateY(-2px) scale(1.02);
        z-index: 10;
        position: relative;
    }

    /* Error State for Time Slots */
    .time-slot.error {
        border-color: #ff5722 !important;
        background: linear-gradient(135deg, rgba(255, 87, 34, 0.1) 0%, rgba(255, 87, 34, 0.05) 100%) !important;
        animation: shakeError 0.5s ease-in-out;
    }

    /* Form Group Error State */
    .form-group.has-error .form-label-wrapper {
        background: linear-gradient(135deg, rgba(255, 87, 34, 0.15) 0%, rgba(255, 87, 34, 0.1) 100%) !important;
        border-left: 4px solid #ff5722;
    }

    .form-group.has-error .form-label {
        color: #ff5722 !important;
    }

    /* Success State Enhancement */
    .form-control.is-valid,
    .form-select.is-valid {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.15) !important;
        background-image: none !important;
    }

    .form-control.is-valid:focus,
    .form-select.is-valid:focus {
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.25),
            0 0 10px rgba(40, 167, 69, 0.15) !important;
    }

    /* Loading State for Submit Button */
    .modern-submit-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none !important;
    }

    .modern-submit-btn:disabled:hover {
        transform: none !important;
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3) !important;
    }

    /* Responsive Enhancements */
    @media (max-width: 768px) {
        .swal2-popup.validation-error-modal {
            width: 90% !important;
            margin: 1rem !important;
        }

        .help-text {
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
        }

        .form-control.validation-error,
        .form-select.validation-error {
            animation: none;
            /* ปิด shake animation ในมือถือ */
        }
    }

    /* Accessibility Enhancements */
    @media (prefers-reduced-motion: reduce) {

        .form-control.validation-error,
        .form-select.validation-error,
        .time-slot.error {
            animation: none;
        }

        .help-text {
            animation: none;
        }

        .time-slots-container.error {
            animation: none;
        }
    }

    /* High Contrast Mode Support */
    @media (prefers-contrast: high) {

        .form-control.validation-error,
        .form-select.validation-error {
            border-width: 3px !important;
            background-color: rgba(255, 87, 34, 0.1) !important;
        }

        .help-text {
            border-left-width: 4px;
            background-color: rgba(255, 87, 34, 0.15) !important;
        }
    }

    /* Dark Mode Support (if needed) */
    @media (prefers-color-scheme: dark) {
        .help-text {
            background: linear-gradient(135deg, rgba(255, 87, 34, 0.2) 0%, rgba(255, 87, 34, 0.1) 100%);
            color: #fff;
        }

        .form-control.validation-error,
        .form-select.validation-error {
            background-color: rgba(255, 87, 34, 0.1) !important;
            color: #fff !important;
        }
    }

    /* Smooth Scroll Enhancement */
    html {
        scroll-behavior: smooth;
    }

    /* Custom Scrollbar for Better UX */
    .container-pages-news::-webkit-scrollbar {
        width: 8px;
    }

    .container-pages-news::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .container-pages-news::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }

    .container-pages-news::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    }
</style>

<!-- Font Awesome และ Bootstrap -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ตัวแปร Global
    const isUserLoggedIn = <?= json_encode($is_logged_in ?? false); ?>;
    const userInfo = <?= json_encode($user_info ?? null); ?>;
    const userAddress = <?= json_encode($user_address ?? null); ?>;
    let hasConfirmedAsGuest = isUserLoggedIn;
    let guestModalInstance = null;
    let idCardModalInstance = null; // *** เพิ่มใหม่: Modal สำหรับเลขบัตรประชาชน ***
    let selectedFiles = [];
    const maxFiles = 3;
    const maxFileSize = 5 * 1024 * 1024; // 10MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
    let formSubmitting = false;

    // *** ตัวแปรสำหรับ Address System ***
    const API_BASE_URL = 'https://addr.assystem.co.th/index.php/zip_api';
    let zipcodeField, provinceField, amphoeField, districtField, fullAddressField, additionalAddressField;
    let currentAddressData = [];

    function checkReturnFromLogin() {
        // ตรวจสอบว่ามาจากการ login หรือไม่
        const urlParams = new URLSearchParams(window.location.search);
        const fromLogin = urlParams.get('from_login');

        if (fromLogin === 'success' && isUserLoggedIn && userInfo) {
            // ลบ parameter จาก URL
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);

            // แสดงข้อความต้อนรับ
            Swal.fire({
                icon: 'success',
                title: 'เข้าสู่ระบบสำเร็จ!',
                html: `
                <div style="text-align: center;">
                    <p style="color: #28a745; font-size: 1.1rem; margin-bottom: 1rem;">
                        <i class="fas fa-user-check me-2"></i>
                        ยินดีต้อนรับ <strong>${userInfo.name}</strong>
                    </p>
                    <div style="background: linear-gradient(135deg, #e6edff 0%, #dde6ff 100%); 
                                padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                        <p style="margin: 0; color: #4c63d2;">
                            <i class="fas fa-magic me-2"></i>
                            ข้อมูลของคุณจะถูกใส่ในฟอร์มโดยอัตโนมัติ
                        </p>
                    </div>
                </div>
            `,
                confirmButtonText: 'เริ่มจองคิว',
                confirmButtonColor: '#667eea',
                timer: 5000,
                timerProgressBar: true
            });

            // ลบ redirect URL จาก sessionStorage
            sessionStorage.removeItem('redirect_after_login');
        }
    }

    // เมื่อเอกสารโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function () {
        // ป้องกัน default form submission
        const form = document.getElementById('queueForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                return false;
            });
        }

        // *** แก้ไข Form Action URL ***
        fixFormAction();

        // *** เริ่มต้น Address System ***
        initializeAddressSystem();

        updateFormFieldsBasedOnLoginStatus();
        setupPhoneValidation();
        setupIdCardValidation();
        setupDateTimeValidation();
        setupTimeSlots(); // *** เพิ่มใหม่ ***

        // ตั้งค่า upload wrapper
        const uploadWrapper = document.querySelector('.file-upload-wrapper');
        if (uploadWrapper) {
            uploadWrapper.addEventListener('click', function (e) {
                e.preventDefault();
                const fileInput = document.getElementById('queue_files');
                if (fileInput) fileInput.click();
            });
        }

        // แสดง modal สำหรับ guest
        if (!isUserLoggedIn) {
            setTimeout(() => {
                if (!hasConfirmedAsGuest) showModal();
            }, 1000);
        } else {
            setTimeout(showWelcomeMessage, 500);
        }

        checkReturnFromLogin();

        // console.log('✅ Queue form initialized with date/time system');
    });

    // อัพเดทฟิลด์ตามสถานะ login
    function updateFormFieldsBasedOnLoginStatus() {
        if (!isUserLoggedIn || !userInfo) return;

        const nameField = document.querySelector('input[name="queue_by"]');
        const phoneField = document.querySelector('input[name="queue_phone"]');
        const emailField = document.querySelector('input[name="queue_email"]');
        const idCardField = document.querySelector('input[name="queue_number"]');

        // *** เพิ่มใหม่: จัดการฟิลด์ที่อยู่ ***
        const additionalAddressField = document.querySelector('#additional_address_field');
        const zipcodeField = document.querySelector('#zipcode_field');
        const provinceField = document.querySelector('#province_field');
        const amphoeField = document.querySelector('#amphoe_field');
        const districtField = document.querySelector('#district_field');

        // *** แก้ไข: ซ่อนฟิลด์และลบ required attribute ***
        if (nameField && userInfo.name) {
            nameField.value = userInfo.name;
            nameField.readOnly = true;
            nameField.style.backgroundColor = '#f8f9fa';
            nameField.removeAttribute('required'); // *** เพิ่มใหม่ ***
            nameField.closest('.form-group').style.display = 'none';
        }

        if (phoneField && userInfo.phone) {
            phoneField.value = userInfo.phone;
            phoneField.readOnly = true;
            phoneField.style.backgroundColor = '#f8f9fa';
            phoneField.removeAttribute('required'); // *** เพิ่มใหม่ ***
            phoneField.closest('.col-md-6').style.display = 'none';
        }

        if (emailField && userInfo.email) {
            emailField.value = userInfo.email;
            emailField.readOnly = true;
            emailField.style.backgroundColor = '#f8f9fa';
            emailField.removeAttribute('required'); // *** เพิ่มใหม่ ***
            emailField.closest('.form-group').style.display = 'none';
        }

        // *** แก้ไข: จัดการเลขบัตรประชาชน ***
        if (idCardField) {
            if (userInfo.number) {
                idCardField.value = userInfo.number;
                idCardField.readOnly = true;
                idCardField.style.backgroundColor = '#f8f9fa';
                idCardField.removeAttribute('required');
            } else {
                // *** เพิ่มใหม่: ถ้าไม่มีเลขบัตรประชาชน ให้แสดง modal ***
                console.warn('⚠️ User has no ID card number - showing modal');
                idCardField.removeAttribute('required');
                // จะแสดง modal ใน handleQueueSubmit แทน
            }

            // *** เพิ่มใหม่: ซ่อนฟิลด์เลขบัตรประชาชนสำหรับ logged-in user ***
            idCardField.closest('.form-group').style.display = 'none';
        }

        // *** เพิ่มใหม่: จัดการข้อมูลที่อยู่สำหรับ user ที่ login ***
        if (userAddress && userAddress.parsed) {
            const parsed = userAddress.parsed;

            // *** แก้ไข: ซ่อนระบบที่อยู่และลบ required ***
            const addressContainer = document.querySelector('#zipcode_field')?.closest('.form-group');
            if (addressContainer) {
                addressContainer.style.display = 'none';

                // ลบ required attribute จากฟิลด์ที่อยู่ทั้งหมด
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

        // แสดงข้อความแจ้งให้ทราบว่าใช้ข้อมูลจากบัญชี
        showLoggedInUserInfo();
    }

    // *** เพิ่มใหม่: แสดงข้อมูล user ที่ login ***
    function showLoggedInUserInfo() {
        try {
            // ลบข้อความเดิม (ถ้ามี)
            const existingInfo = document.getElementById('logged-in-user-info');
            if (existingInfo) {
                existingInfo.remove();
            }

            // สร้างข้อมูล user
            let userName = userInfo.name || 'ผู้ใช้';
            let userEmail = userInfo.email || 'ไม่ระบุ';
            let userPhone = userInfo.phone || 'ไม่ระบุ';
            let userAddressText = '';

            // จัดการข้อมูลที่อยู่
            if (userAddress && userAddress.parsed) {
                userAddressText = userAddress.parsed.full_address || 'ใช้ข้อมูลจากบัญชี';
            } else if (userAddress && userAddress.full_address) {
                userAddressText = userAddress.full_address;
            } else {
                userAddressText = 'ใช้ข้อมูลจากบัญชี';
            }

            // สร้าง HTML แสดงข้อมูล user
            const userInfoHTML = `
            <div id="logged-in-user-info" class="alert alert-success" style="
                border-radius: 15px; 
                background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); 
                border: 1px solid rgba(40, 167, 69, 0.3);
                margin-bottom: 2rem;
                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
                backdrop-filter: blur(10px);
            ">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div style="
                            width: 60px; 
                            height: 60px; 
                            background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(25, 135, 84, 0.15) 100%); 
                            border-radius: 50%; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center;
                            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
                        ">
                            <i class="fas fa-user-check" style="font-size: 1.8rem; color: #198754;"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h5 class="mb-2" style="color: #155724; font-weight: 600;">
                            <i class="fas fa-check-circle me-2"></i>ใช้ข้อมูลจากบัญชีของคุณ
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-user me-2"></i><strong>ชื่อ:</strong> ${userName}</div>
                                <div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-envelope me-2"></i><strong>อีเมล:</strong> ${userEmail}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-phone me-2"></i><strong>เบอร์โทร:</strong> ${userPhone}</div>
                                <div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-map-marker-alt me-2"></i><strong>ที่อยู่:</strong> ${userAddressText}</div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small style="color: #155724; display: flex; align-items: center;">
                                <i class="fas fa-info-circle me-1"></i> 
                                ระบบจะใช้ข้อมูลจากบัญชีของคุณโดยอัตโนมัติ
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;

            // แทรกข้อความหลังหัวข้อฟอร์ม
            const formContainer = document.getElementById('queue_form');
            const firstFormGroup = formContainer.querySelector('.form-group');
            if (firstFormGroup) {
                firstFormGroup.insertAdjacentHTML('beforebegin', userInfoHTML);
            }

            //console.log('✅ User info display created');

        } catch (error) {
            console.error('❌ Error showing user info:', error);
        }
    }

    // *** ฟังก์ชันจัดการวันที่และเวลา ***
    function setupDateTimeValidation() {
        const dateInput = document.querySelector('input[name="queue_date_temp"]');
        if (dateInput) {
            // ตั้งค่าวันที่ขั้นต่ำเป็นพรุ่งนี้
            const now = new Date();
            const tomorrow = new Date(now.getTime() + 24 * 60 * 60 * 1000);
            dateInput.min = tomorrow.toISOString().split('T')[0];

            // แสดงวันที่เป็นภาษาไทยเมื่อเลือกวันที่
            dateInput.addEventListener('change', function () {
                updateThaiDateDisplay(this.value);
            });
        }
    }

    // *** เพิ่มใหม่: ฟังก์ชันจัดการ Time Slots ***
    function setupTimeSlots() {
        const timeSlots = document.querySelectorAll('.time-slot');
        const queueTimeInput = document.getElementById('queue_time');
        const selectedTimeDisplay = document.getElementById('selected_time_display');
        const selectedTimeText = document.getElementById('selected_time_text');

        timeSlots.forEach(slot => {
            slot.addEventListener('click', function () {
                // ลบ selected class จากทุก slot
                timeSlots.forEach(s => s.classList.remove('selected'));

                // เพิ่ม selected class ให้ slot ที่เลือก
                this.classList.add('selected');

                // อัพเดทค่าใน hidden input
                const timeValue = this.getAttribute('data-time');
                queueTimeInput.value = timeValue;

                // แสดงช่วงเวลาที่เลือก
                selectedTimeText.textContent = timeValue.replace('-', ' - ');
                selectedTimeDisplay.style.display = 'block';

                // ลบ invalid feedback
                queueTimeInput.classList.remove('is-invalid');
                const feedback = document.getElementById('queue_time_feedback');
                if (feedback) {
                    feedback.style.display = 'none';
                }

                // console.log('✅ Time slot selected:', timeValue);
            });
        });
    }

    // *** เพิ่มใหม่: ฟังก์ชันแปลงวันที่เป็นภาษาไทย ***
    function updateThaiDateDisplay(dateValue) {
        if (!dateValue) {
            document.getElementById('thai_date_display').textContent = '';
            return;
        }

        try {
            const date = new Date(dateValue);
            const thaiDate = formatDateToThai(date);
            document.getElementById('thai_date_display').innerHTML = `<i class="fas fa-calendar-alt me-1"></i>${thaiDate}`;
        } catch (error) {
            console.error('Error formatting Thai date:', error);
        }
    }

    // *** เพิ่มใหม่: ฟังก์ชันแปลงวันที่เป็นรูปแบบไทย ***
    function formatDateToThai(date) {
        const thaiMonths = [
            'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
            'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        ];

        const thaiDays = [
            'อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'
        ];

        const day = date.getDate();
        const month = thaiMonths[date.getMonth()];
        const year = date.getFullYear() + 543; // แปลงเป็นปีไทย
        const dayOfWeek = thaiDays[date.getDay()];

        return `วัน${dayOfWeek}ที่ ${day} ${month} ${year}`;
    }

    // *** เพิ่มใหม่: ฟังก์ชันรวมวันที่และเวลาก่อนส่งฟอร์ม ***
    function combineDateTime() {
        const dateValue = document.getElementById('queue_date').value;
        const timeValue = document.getElementById('queue_time').value;

        if (!dateValue || !timeValue) {
            return null;
        }

        // แปลงเวลาเป็นรูปแบบที่เหมาะสม (เอาเวลาเริ่มต้น)
        const startTime = timeValue.split('-')[0];
        const dateTimeString = `${dateValue} ${startTime}:00`;

        console.log('📅 Combined DateTime:', dateTimeString);
        return dateTimeString;
    }

    // *** ฟังก์ชัน Address System ***
    function initializeAddressSystem() {
        try {
            // เริ่มต้น elements
            zipcodeField = document.getElementById('zipcode_field');
            provinceField = document.getElementById('province_field');
            amphoeField = document.getElementById('amphoe_field');
            districtField = document.getElementById('district_field');
            fullAddressField = document.getElementById('full_address_field');
            additionalAddressField = document.getElementById('additional_address_field');

            console.log('🔧 Address elements initialized:', {
                zipcode: !!zipcodeField,
                province: !!provinceField,
                amphoe: !!amphoeField,
                district: !!districtField,
                fullAddress: !!fullAddressField,
                additionalAddress: !!additionalAddressField
            });

            if (!zipcodeField) {
                console.error('❌ Address elements not found');
                return;
            }

            // โหลดรายการจังหวัดทั้งหมด
            loadAllProvinces();

            // จำกัดให้กรอกเฉพาะตัวเลขในช่องรหัสไปรษณีย์
            zipcodeField.addEventListener('keypress', function (e) {
                if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                    e.preventDefault();
                }
            });

            // เมื่อกรอกรหัสไปรษณีย์
            zipcodeField.addEventListener('input', function () {
                const zipcode = this.value.trim();
                console.log('📮 Zipcode input changed:', zipcode);

                if (zipcode.length === 0) {
                    resetToProvinceSelection();
                } else if (zipcode.length === 5 && /^\d{5}$/.test(zipcode)) {
                    searchByZipcode(zipcode);
                } else {
                    clearDependentAddressFields();
                }
            });

            // เมื่อเลือกอำเภอ
            amphoeField.addEventListener('change', function () {
                const selectedAmphoeCode = this.value;
                console.log('🏙️ Amphoe changed to:', selectedAmphoeCode);

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

            // เมื่อเลือกตำบล
            districtField.addEventListener('change', function () {
                const selectedDistrictCode = this.value;
                console.log('🏘️ District changed to:', selectedDistrictCode);

                if (selectedDistrictCode) {
                    // หา zipcode จากข้อมูลที่มีแล้ว
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

            // Event Listener สำหรับช่องที่อยู่เพิ่มเติม
            if (additionalAddressField) {
                additionalAddressField.addEventListener('input', function () {
                    clearTimeout(this.updateTimeout);
                    this.updateTimeout = setTimeout(() => {
                        updateFullAddress();
                    }, 300);
                });
            }

            // console.log('✅ Address system initialized successfully');

        } catch (error) {
            console.error('❌ Error initializing address system:', error);
        }
    }

    // ฟังก์ชันดึงรายการจังหวัดทั้งหมด
    async function loadAllProvinces() {
        console.log('Loading all provinces...');

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

            console.log('📦 API Response for zipcode:', data);

            if (data.status === 'success' && data.data.length > 0) {
                const dataWithZipcode = data.data.map(item => ({
                    ...item,
                    zipcode: zipcode,
                    searched_zipcode: zipcode
                }));

                // console.log('✅ Enhanced data with zipcode:', dataWithZipcode);

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
                    // console.log('✅ Zipcode field updated:', zipcode);
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

        console.log('📝 Populating fields from zipcode data:', data);

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

        // Auto-select อำเภอถ้ามีเพียงอำเภอเดียว
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
        <select id="province_field" class="form-control" style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
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
               style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
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

        // ใส่เฉพาะที่อยู่เพิ่มเติมใน queue_address
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
        // สร้าง hidden fields ถ้ายังไม่มี
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
                document.getElementById('queueForm').appendChild(hiddenField);
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
                icon.style.cssText = 'position: absolute; right: 45px; top: 50%; transform: translateY(-50%); color: #667eea; z-index: 3;';
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

    // Validation functions
    function setupPhoneValidation() {
        const phoneInput = document.getElementById('queue_phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 10) value = value.slice(0, 10);
                e.target.value = value;
            });
        }
    }

    function setupIdCardValidation() {
        const idInput = document.getElementById('queue_number');
        if (idInput) {
            idInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 13) value = value.slice(0, 13);
                e.target.value = value;
            });
        }
    }

    // File handling
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

    // Drag & Drop
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

        const fileInput = document.getElementById('queue_files');
        fileInput.files = e.dataTransfer.files;
        handleFileSelect(fileInput);
    }

    // Form submission
    function handleQueueSubmit(event) {
        event.preventDefault();

        if (formSubmitting) return false;

        // *** เพิ่มใหม่: เช็คเลขบัตรประชาชนสำหรับ public user ***
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

    // *** เพิ่มฟังก์ชันสำหรับ reCAPTCHA verification ***
    // แทนที่ฟังก์ชัน submitForm() เดิมด้วยโค้ดนี้
    async function submitForm() {
        try {
            const form = document.getElementById('queueForm');

            if (formSubmitting) return;
            formSubmitting = true;

            const submitBtn = document.getElementById('submitQueueBtn');
            const originalContent = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังส่ง...';

            // *** แก้ไข: ตรวจสอบ reCAPTCHA ก่อนที่จะทำอะไรต่อ ***
            console.log('🔐 Checking reCAPTCHA status...');
            console.log('reCAPTCHA Ready:', window.recaptchaReady);
            console.log('Site Key:', window.RECAPTCHA_SITE_KEY ? 'SET' : 'NOT SET');
            console.log('Skip Dev:', window.SKIP_RECAPTCHA_FOR_DEV);

            let recaptchaToken = null;

            // ตรวจสอบว่า reCAPTCHA พร้อมใช้งานหรือไม่
            if (window.recaptchaReady && window.RECAPTCHA_SITE_KEY && typeof grecaptcha !== 'undefined') {
                try {
                    console.log('🔄 Generating reCAPTCHA token...');

                    // รอให้ reCAPTCHA สร้าง token
                    recaptchaToken = await grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                        action: 'queue_submit'
                    });

                    console.log('✅ reCAPTCHA token generated:', recaptchaToken ? 'SUCCESS' : 'FAILED');

                } catch (error) {
                    console.error('❌ reCAPTCHA token generation failed:', error);

                    // แสดงข้อผิดพลาดให้ user ทราบ
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถยืนยันตัวตนได้',
                        text: 'การยืนยันความปลอดภัยล้มเหลว กรุณารีเฟรชหน้าและลองใหม่อีกครั้ง',
                        confirmButtonColor: '#dc3545'
                    });

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    formSubmitting = false;
                    return;
                }
            } else {
                console.warn('⚠️ reCAPTCHA not available - checking if dev mode');

                // ถ้าไม่ใช่ dev mode ให้แสดงข้อผิดพลาด
                if (!window.SKIP_RECAPTCHA_FOR_DEV) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ระบบความปลอดภัยไม่พร้อม',
                        text: 'กรุณารีเฟรชหน้าและลองใหม่อีกครั้ง',
                        confirmButtonColor: '#ff9800'
                    });

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    formSubmitting = false;
                    return;
                }
            }

            // สร้าง FormData
            const formData = new FormData();

            // *** เพิ่ม reCAPTCHA Token และข้อมูลที่เกี่ยวข้อง ***
            if (recaptchaToken) {
                formData.append('g-recaptcha-response', recaptchaToken);
                formData.append('recaptcha_action', 'queue_submit');
                formData.append('recaptcha_source', 'queue_form');
                console.log('🔐 Added reCAPTCHA token to form');
            } else {
                console.log('⚠️ No reCAPTCHA token - proceeding without verification');
            }

            // เพิ่มข้อมูลเพิ่มเติม
            formData.append('client_timestamp', Date.now());
            formData.append('user_agent_info', navigator.userAgent);
            formData.append('user_type_detected', isUserLoggedIn ? 'citizen' : 'guest');

            if (window.SKIP_RECAPTCHA_FOR_DEV) {
                formData.append('dev_mode', '1');
            }

            // *** รวมวันที่และเวลา ***
            const combinedDateTime = combineDateTime();
            if (combinedDateTime) {
                formData.append('queue_date', combinedDateTime);
                formData.append('queue_time_slot', document.getElementById('queue_time').value);
            }

            // เพิ่มข้อมูลฟอร์มอื่นๆ
            const formElements = form.elements;
            for (let element of formElements) {
                if (element.type === 'file' || element.type === 'button' || element.type === 'submit') continue;
                if (element.name && element.value !== '' && element.name !== 'queue_date_temp') {
                    formData.append(element.name, element.value);
                }
            }

            // เพิ่มข้อมูลที่อยู่สำหรับ guest
            if (!isUserLoggedIn) {
                const guestFields = ['guest_province', 'guest_amphoe', 'guest_district', 'guest_zipcode'];
                guestFields.forEach(fieldName => {
                    const field = document.querySelector(`#${fieldName}_field`);
                    if (field && field.value) {
                        formData.append(fieldName, field.value);
                    }
                });
            }

            // เพิ่มไฟล์
            if (selectedFiles && selectedFiles.length > 0) {
                selectedFiles.forEach((file, index) => {
                    formData.append('queue_files[]', file, file.name);
                });
            }

            // ส่งข้อมูล
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            // จัดการ response
            const contentType = response.headers.get('content-type');
            let jsonResponse;

            if (contentType && contentType.includes('application/json')) {
                jsonResponse = await response.json();
            } else {
                const text = await response.text();
                try {
                    jsonResponse = JSON.parse(text);
                } catch (e) {
                    if (text.trim().length > 0) {
                        console.error('Server response (non-JSON):', text);
                        throw new Error('Server returned non-JSON response');
                    }
                    jsonResponse = {
                        success: true,
                        message: 'จองคิวสำเร็จ',
                        queue_id: 'Q' + Date.now().toString().slice(-6)
                    };
                }
            }

            // จัดการผลลัพธ์
            if (jsonResponse.success === false) {
                const errorType = jsonResponse.error_type || 'unknown';
                let errorMessage = jsonResponse.message || 'เกิดข้อผิดพลาดในการจองคิว';

                // จัดการ error ต่างๆ
                if (errorType === 'recaptcha_missing') {
                    errorMessage = 'ไม่พบข้อมูลการยืนยันตัวตน กรุณารีเฟรชหน้าและลองใหม่อีกครั้ง';
                    showAlert('error', 'ไม่สามารถยืนยันตัวตนได้', errorMessage);
                    return;
                } else if (errorType === 'recaptcha_failed') {
                    errorMessage = 'การยืนยันตัวตนไม่ผ่าน กรุณาลองใหม่อีกครั้ง';
                    showAlert('error', 'ไม่สามารถยืนยันตัวตนได้', errorMessage);
                    return;
                }

                // ตรวจสอบ validation error
                if (jsonResponse.show_modal || jsonResponse.validation_type) {
                    handleValidationError(jsonResponse);
                } else {
                    showAlert('error', 'เกิดข้อผิดพลาด', errorMessage);
                }
                return;
            }

            // แสดงผลสำเร็จ
            const queueId = jsonResponse.queue_id || 'Q' + Date.now().toString().slice(-6);
            const recaptchaInfo = jsonResponse.recaptcha_verified ?
                '<div style="background: rgba(40, 167, 69, 0.1); padding: 0.5rem 1rem; border-radius: 8px; margin: 0.5rem 0;"><small style="color: #28a745;"><i class="fas fa-shield-check me-1"></i>ยืนยันความปลอดภัยแล้ว</small></div>' :
                '';

            Swal.fire({
                icon: 'success',
                title: 'จองคิวสำเร็จ!',
                html: `
                <div style="text-align: center;">
                    <div style="background: linear-gradient(135deg, #e8f5e8 0%, #f0f9f0 100%); 
                                padding: 1.5rem; border-radius: 15px; margin: 1rem 0;">
                        <h3 style="color: #155724; margin-bottom: 0.5rem;">
                            🎫 หมายเลขคิว
                        </h3>
                        <div style="font-size: 2rem; font-weight: bold; color: #155724;">
                            ${queueId}
                        </div>
                        <button onclick="copyQueueId('${queueId}')" 
                                class="btn btn-sm btn-outline-success mt-2">
                            <i class="fas fa-copy"></i> คัดลอกหมายเลข
                        </button>
                    </div>
                    ${recaptchaInfo}
                    <p style="color: #666;">กรุณาเก็บหมายเลขนี้ไว้สำหรับติดตามสถานะ</p>
                </div>
            `,
                confirmButtonText: 'ติดตามสถานะ',
                confirmButtonColor: '#667eea',
                showCancelButton: true,
                cancelButtonText: 'ปิด'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (isUserLoggedIn) {
                        window.location.href = `/Queue/my_queue_detail/${queueId}`;
                    } else {
                        window.location.href = `/Queue/follow_queue?auto_search=${queueId}`;
                    }
                } else {
                    resetForm();
                }
            });

        } catch (error) {
            console.error('Submit form error:', error);
            showAlert('error', 'เกิดข้อผิดพลาดในระบบ', 'ไม่สามารถส่งฟอร์มได้ กรุณาลองใหม่');
        } finally {
            setTimeout(() => {
                const submitBtn = document.getElementById('submitQueueBtn');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span style="position: relative; z-index: 2;"><i class="fas fa-calendar-plus me-2"></i>จองคิว</span>';
                }
                formSubmitting = false;
            }, 100);
        }
    }

    // *** ฟังก์ชันตรวจสอบสถานะ reCAPTCHA ***
    function checkRecaptchaStatus() {
        const status = {
            ready: window.recaptchaReady || false,
            siteKey: window.RECAPTCHA_SITE_KEY || null,
            skipForDev: window.SKIP_RECAPTCHA_FOR_DEV || false,
            grecaptchaAvailable: typeof grecaptcha !== 'undefined'
        };

        console.log('🔍 reCAPTCHA Status Check:', status);
        return status;
    }

    // *** ฟังก์ชันสร้าง reCAPTCHA Token ***
    async function generateRecaptchaToken(action = 'queue_submit') {
        console.log('🔐 Generating reCAPTCHA token for action:', action);

        if (!window.recaptchaReady || !window.RECAPTCHA_SITE_KEY || typeof grecaptcha === 'undefined') {
            console.warn('⚠️ reCAPTCHA not ready or not available');
            return null;
        }

        try {
            const token = await grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                action: action
            });

            console.log('✅ reCAPTCHA token generated successfully');
            return token;
        } catch (error) {
            console.error('❌ Error generating reCAPTCHA token:', error);
            throw error;
        }
    }

    // *** ฟังก์ชันรอให้ reCAPTCHA พร้อม ***
    function waitForRecaptcha(timeout = 5000) {
        return new Promise((resolve, reject) => {
            if (window.recaptchaReady) {
                resolve(true);
                return;
            }

            const startTime = Date.now();
            const checkInterval = setInterval(() => {
                if (window.recaptchaReady) {
                    clearInterval(checkInterval);
                    resolve(true);
                } else if (Date.now() - startTime > timeout) {
                    clearInterval(checkInterval);
                    reject(new Error('reCAPTCHA timeout'));
                }
            }, 100);
        });
    }

    function handleValidationError(errorResponse) {
        // *** จัดการ modal สำหรับเลขบัตรประชาชน ***
        if (errorResponse.show_id_card_modal) {
            showIdCardModal();
            return;
        }

        // *** สร้าง icon และสีตาม validation_type ***
        let modalIcon = 'warning';
        let modalColor = '#ff9800';
        let iconClass = 'fas fa-exclamation-triangle';

        switch (errorResponse.validation_type) {
            case 'required_field':
                modalIcon = 'warning';
                modalColor = '#ff9800';
                iconClass = 'fas fa-exclamation-triangle';
                break;
            case 'invalid_format':
            case 'invalid_id_card':
            case 'invalid_email':
                modalIcon = 'error';
                modalColor = '#f44336';
                iconClass = 'fas fa-times-circle';
                break;
            case 'min_length':
            case 'max_length':
                modalIcon = 'info';
                modalColor = '#2196f3';
                iconClass = 'fas fa-info-circle';
                break;
            case 'invalid_date':
                modalIcon = 'warning';
                modalColor = '#ff5722';
                iconClass = 'fas fa-calendar-times';
                break;
            default:
                modalIcon = 'warning';
                modalColor = '#ff9800';
                iconClass = 'fas fa-exclamation-triangle';
        }

        // *** แสดง SweetAlert modal ***
        Swal.fire({
            icon: modalIcon,
            title: 'ข้อมูลไม่ครบถ้วน',
            html: `
            <div style="text-align: center; padding: 1rem;">
                <div style="margin-bottom: 1.5rem;">
                    <i class="${iconClass}" style="font-size: 3rem; color: ${modalColor}; margin-bottom: 1rem;"></i>
                </div>
                <div style="background: linear-gradient(135deg, rgba(255, 152, 0, 0.1) 0%, rgba(255, 193, 7, 0.1) 100%); 
                            padding: 1.5rem; border-radius: 15px; margin: 1rem 0; 
                            border-left: 4px solid ${modalColor};">
                    <p style="font-size: 1.1rem; margin: 0; color: #333; line-height: 1.6;">
                        ${errorResponse.message}
                    </p>
                </div>
            </div>
        `,
            confirmButtonText: '<i class="fas fa-check me-2"></i>ตกลง',
            confirmButtonColor: modalColor,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'validation-error-modal',
                title: 'validation-error-title',
                content: 'validation-error-content'
            },
            didOpen: () => {
                // เพิ่ม animation เล็กน้อย
                const popup = Swal.getPopup();
                popup.style.animation = 'fadeInUp 0.4s ease-out';
            },
            // *** เพิ่ม willClose callback เพื่อจัดการการปิด modal ***
            willClose: () => {
                // ทำอะไรก่อนปิด modal (ถ้าจำเป็น)
                console.log('Modal is closing...');
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // *** เมื่อกดตกลง ให้ไปยังช่องที่ต้องแก้ไขทันที ***
                focusOnField(errorResponse.focus_field, errorResponse.field);
            }
        });
    }

    function focusOnField(focusSelector, fieldName) {
        try {
            // *** ตรวจสอบและปิด modal ที่เหลืออยู่ ***
            if (Swal.isVisible()) {
                Swal.close();
            }

            let targetElement = null;

            // *** หาช่องที่ต้อง focus ***
            if (focusSelector) {
                targetElement = document.querySelector(focusSelector);
            }

            // *** fallback หาช่องตาม field name ***
            if (!targetElement && fieldName) {
                const fallbackSelectors = [
                    `input[name="${fieldName}"]`,
                    `textarea[name="${fieldName}"]`,
                    `select[name="${fieldName}"]`,
                    `#${fieldName}`,
                    `.${fieldName}`
                ];

                for (const selector of fallbackSelectors) {
                    targetElement = document.querySelector(selector);
                    if (targetElement) break;
                }
            }

            if (targetElement) {
                // *** รอให้ modal ปิดก่อนแล้วค่อย scroll และ focus ***
                setTimeout(() => {
                    // *** Scroll ไปยังช่องนั้น ***
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'nearest'
                    });

                    // *** เพิ่มเอฟเฟกต์เน้นหลังจาก scroll เสร็จ ***
                    setTimeout(() => {
                        // เน้นช่อง
                        targetElement.style.boxShadow = '0 0 15px rgba(255, 87, 34, 0.6)';
                        targetElement.style.borderColor = '#ff5722';
                        targetElement.style.transition = 'all 0.3s ease';
                        targetElement.classList.add('validation-error');

                        // Focus
                        if (typeof targetElement.focus === 'function') {
                            targetElement.focus();

                            // สำหรับ input text ให้ select ข้อความทั้งหมด
                            if (targetElement.type === 'text' || targetElement.type === 'email' || targetElement.type === 'tel') {
                                targetElement.select();
                            }
                        }

                        // *** เพิ่ม toast notification เล็กๆ ***
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'กรุณาแก้ไขข้อมูลในช่องที่เน้น',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'field-focus-toast'
                            }
                        });

                        // ลบเอฟเฟกต์หลังจาก 5 วินาที
                        setTimeout(() => {
                            targetElement.style.boxShadow = '';
                            targetElement.style.borderColor = '';
                            targetElement.classList.remove('validation-error');
                        }, 5000);

                    }, 500); // รอให้ scroll เสร็จก่อน

                }, 200); // รอให้ modal ปิดก่อน

                // *** การจัดการพิเศษสำหรับช่องประเภทต่างๆ ***
                setTimeout(() => {
                    handleSpecialFieldFocus(fieldName, targetElement);
                }, 300);

            } else {
                console.warn('Target field not found:', focusSelector, fieldName);

                // *** แสดง toast แทน modal ใหญ่ ***
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'กรุณาตรวจสอบข้อมูลในฟอร์ม',
                    text: 'กรุณาตรวจสอบข้อมูลที่กรอกและแก้ไขให้ถูกต้อง',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true
                });
            }

        } catch (error) {
            console.error('Error focusing on field:', error);

            // *** ปิด modal ในกรณีเกิดข้อผิดพลาด ***
            if (Swal.isVisible()) {
                Swal.close();
            }
        }
    }


    function handleSpecialFieldFocus(fieldName, targetElement) {
        switch (fieldName) {
            case 'queue_time':
                // สำหรับ time slots - เน้น container
                const timeSlotsContainer = document.getElementById('time_slots_container');
                if (timeSlotsContainer) {
                    timeSlotsContainer.style.border = '2px solid #ff5722';
                    timeSlotsContainer.style.borderRadius = '12px';
                    timeSlotsContainer.style.backgroundColor = 'rgba(255, 87, 34, 0.05)';

                    setTimeout(() => {
                        timeSlotsContainer.style.border = '';
                        timeSlotsContainer.style.backgroundColor = '';
                    }, 3000);
                }
                break;

            case 'queue_date':
                // สำหรับวันที่ - เพิ่มข้อความช่วยเหลือ
                const dateInput = document.querySelector('input[name="queue_date_temp"]');
                if (dateInput) {
                    const helpText = document.createElement('small');
                    helpText.className = 'help-text';
                    helpText.style.color = '#ff5722';
                    helpText.style.fontWeight = 'bold';
                    helpText.innerHTML = '<i class="fas fa-info-circle me-1"></i>กรุณาเลือกวันที่ตั้งแต่พรุ่งนี้เป็นต้นไป';

                    if (!dateInput.parentNode.querySelector('.help-text')) {
                        dateInput.parentNode.appendChild(helpText);

                        setTimeout(() => {
                            helpText.remove();
                        }, 5000);
                    }
                }
                break;

            case 'guest_district':
                // สำหรับตำบล - แสดงคำแนะนำ
                const districtField = document.querySelector('select[name="district_field"]');
                if (districtField) {
                    const helpText = document.createElement('small');
                    helpText.className = 'help-text';
                    helpText.style.color = '#ff5722';
                    helpText.style.fontWeight = 'bold';
                    helpText.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i>กรุณากรอกรหัสไปรษณีย์เพื่อค้นหาข้อมูลที่อยู่อัตโนมัติ';

                    if (!districtField.parentNode.querySelector('.help-text')) {
                        districtField.parentNode.appendChild(helpText);

                        setTimeout(() => {
                            helpText.remove();
                        }, 5000);
                    }

                    // Focus ไปที่ช่องรหัสไปรษณีย์แทน
                    const zipcodeField = document.querySelector('#zipcode_field');
                    if (zipcodeField) {
                        setTimeout(() => {
                            zipcodeField.focus();
                            zipcodeField.style.boxShadow = '0 0 15px rgba(102, 126, 234, 0.6)';
                            zipcodeField.style.borderColor = '#667eea';

                            setTimeout(() => {
                                zipcodeField.style.boxShadow = '';
                                zipcodeField.style.borderColor = '';
                            }, 3000);
                        }, 100);
                    }
                }
                break;
        }
    }


    function resetForm() {
        const form = document.getElementById('queueForm');
        if (form) form.reset();
        selectedFiles = [];
        updateFileDisplay();
        updateFormFieldsBasedOnLoginStatus();

        // *** เพิ่มใหม่: รีเซ็ต Time Slots และ Date Display ***
        const timeSlots = document.querySelectorAll('.time-slot');
        timeSlots.forEach(slot => slot.classList.remove('selected'));

        document.getElementById('queue_time').value = '';
        document.getElementById('selected_time_display').style.display = 'none';
        document.getElementById('thai_date_display').innerHTML = '';

        // *** รีเซ็ต Address fields ***
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
    }

    function copyQueueId(queueId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(queueId).then(() => {
                showAlert('success', 'คัดลอกแล้ว', `คัดลอกหมายเลขคิว ${queueId} แล้ว`, 2000);
            });
        }
    }

    // *** เพิ่มฟังก์ชัน reCAPTCHA helpers สำหรับ queue ***
    function checkRecaptchaStatus() {
        return {
            ready: window.recaptchaReady || false,
            siteKey: window.RECAPTCHA_SITE_KEY || null,
            skipForDev: window.SKIP_RECAPTCHA_FOR_DEV || false
        };
    }

    async function generateRecaptchaToken(action = 'queue_submit') {
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

    function copyQueueId(queueId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(queueId).then(() => {
                showAlert('success', 'คัดลอกแล้ว', `คัดลอกหมายเลขคิว ${queueId} แล้ว`, 2000);
            });
        }
    }


    // Modal functions
    function showModal() {
        const modalElement = document.getElementById('guestConfirmModal');
        if (!modalElement) return;

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            guestModalInstance = new bootstrap.Modal(modalElement);
            guestModalInstance.show();
        } else {
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
        }
    }

    const userInfoHTML = `
    <div id="logged-in-user-info" class="alert alert-success" style="
        border-radius: 15px; 
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); 
        border: 1px solid rgba(40, 167, 69, 0.3);
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
        backdrop-filter: blur(10px);
    ">
        <div class="row align-items-center">
            <div class="col-auto">
                <div style="
                    width: 60px; 
                    height: 60px; 
                    background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(25, 135, 84, 0.15) 100%); 
                    border-radius: 50%; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center;
                    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
                ">
                    <i class="fas fa-user-check" style="font-size: 1.8rem; color: #198754;"></i>
                </div>
            </div>
            <div class="col">
                <h5 class="mb-2" style="color: #155724; font-weight: 600;">
                    <i class="fas fa-check-circle me-2"></i>ใช้ข้อมูลจากบัญชีของคุณ
                </h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-user me-2"></i><strong>ชื่อ:</strong> ${userName}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-envelope me-2"></i><strong>อีเมล:</strong> ${userEmail}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-phone me-2"></i><strong>เบอร์โทร:</strong> ${userPhone}</div>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-12">
                        <div class="mb-1" style="color: #155724; font-size: 0.9rem;"><i class="fas fa-map-marker-alt me-2"></i><strong>ที่อยู่:</strong> ${userAddressText}</div>
                    </div>
                </div>
                <div class="mt-2">
                    <small style="color: #155724; display: flex; align-items: center;">
                        <i class="fas fa-info-circle me-1"></i> 
                        ระบบจะใช้ข้อมูลจากบัญชีของคุณโดยอัตโนมัติ
                    </small>
                </div>
            </div>
        </div>
    </div>
`;


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
        showAlert('info', 'ดำเนินการต่อโดยไม่เข้าสู่ระบบ', 'คุณสามารถกรอกข้อมูลและจองคิวได้แล้ว', 2000);
    }

    function redirectToLogin() {
        // ซ่อน modal ก่อน
        hideModal();

        // *** เก็บ URL ปัจจุบันไว้ใน sessionStorage สำหรับกลับมาหลัง login ***
        const currentUrl = window.location.href;
        sessionStorage.setItem('redirect_after_login', currentUrl);

        // แสดงข้อความแจ้งเตือน
        Swal.fire({
            icon: 'info',
            title: 'กำลังนำท่านไปหน้าเข้าสู่ระบบ',
            text: 'หลังจากเข้าสู่ระบบสำเร็จ จะกลับมายังหน้านี้โดยอัตโนมัติ',
            timer: 2000,
            showConfirmButton: false,
            timerProgressBar: true
        }).then(() => {
            // *** แก้ไข: ใช้ relative URL แทน site_url ***
            window.location.href = '/User?redirect=' + encodeURIComponent(currentUrl);
        });
    }

    // *** แก้ไข: เพิ่มฟังก์ชันแก้ไข Form Action ***
    function fixFormAction() {
        const form = document.getElementById('queueForm');
        if (form) {
            // เปลี่ยน action URL ให้ถูกต้อง
            form.action = '/Queue/add_queue';
            // console.log('✅ Form action updated to:', form.action);
        }
    }

    function showWelcomeMessage() {
        if (isUserLoggedIn && userInfo && userInfo.name) {
            Swal.fire({
                icon: 'success',
                title: `ยินดีต้อนรับ ${userInfo.name}`,
                text: 'คุณสามารถจองคิวได้ทันที ข้อมูลของคุณจะถูกใช้โดยอัตโนมัติ',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: 'linear-gradient(135deg, #e6edff 0%, #dde6ff 100%)',
                color: '#4c63d2'
            });
        }
    }

    function showAlert(icon, title, text, timer = null, focusField = null) {
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

        // *** เพิ่มการ focus หลังจากปิด alert ***
        if (focusField) {
            config.didClose = () => {
                focusOnField(focusField);
            };
        }

        Swal.fire(config);
    }

    // *** เพิ่ม CSS สำหรับ validation modal ***
    const validationModalCSS = `
<style>
.validation-error-modal {
    border-radius: 20px !important;
    overflow: hidden !important;
}

.validation-error-title {
    font-size: 1.5rem !important;
    font-weight: 600 !important;
}

.validation-error-content {
    padding: 0 !important;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translate3d(0, 30px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

.help-text {
    display: block;
    margin-top: 0.5rem;
    animation: fadeInDown 0.3s ease-out;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
`;

    // *** เพิ่ม CSS เมื่อเอกสารโหลดเสร็จ ***
    document.addEventListener('DOMContentLoaded', function () {
        // เพิ่ม CSS
        document.head.insertAdjacentHTML('beforeend', validationModalCSS);
    });





    // *** เพิ่มฟังก์ชันตรวจสอบเลขบัตรประชาชนไทย ***
    function validateThaiIdCard(idCard) {
        // ตรวจสอบรูปแบบพื้นฐาน
        if (!idCard || !/^\d{13}$/.test(idCard)) {
            return false;
        }

        // ตรวจสอบเลขซ้ำทั้งหมด
        if (/^(\d)\1{12}$/.test(idCard)) {
            return false;
        }

        // ตรวจสอบด้วยอัลกอริทึม MOD 11 (Thai ID Card Algorithm)
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

    // *** แก้ไขฟังก์ชัน showIdCardModal ***
    function showIdCardModal() {
        Swal.fire({
            title: '<span style="color: #667eea;">กรอกเลขบัตรประจำตัวประชาชน</span>',
            html: `
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.25) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);">
                    <i class="fas fa-id-card" style="font-size: 2.5rem; color: #dc3545;"></i>
                </div>
                <p style="color: #666; font-size: 1.1rem; line-height: 1.6; margin-bottom: 1rem;">
                    <strong style="color: #dc3545;">จำเป็นต่อการให้บริการ</strong><br>
                    กรุณากรอกเลขบัตรประจำตัวประชาชน 13 หลัก<br>
                    เพื่อใช้ในการจองคิวและติดตามสถานะ
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
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false,
            allowEscapeKey: false,
            preConfirm: () => {
                const idCard = document.getElementById('swal-id-card').value.trim();
                const errorDiv = document.getElementById('id-card-error');

                // *** แก้ไข: Validation ที่เข้มข้นขึ้น ***
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

                // *** เพิ่มใหม่: ตรวจสอบด้วยอัลกอริทึมไทย ***
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
                    // อนุญาตเฉพาะตัวเลข
                    e.target.value = e.target.value.replace(/\D/g, '');

                    // ซ่อน error/check เมื่อพิมพ์
                    errorDiv.style.display = 'none';
                    checkDiv.style.display = 'none';

                    // *** เพิ่มใหม่: ตรวจสอบแบบ real-time ***
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

                // *** เพิ่มใหม่: ตรวจสอบเมื่อ paste ***
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
                showAlert('info', 'การจองคิวถูกยกเลิก', 'จำเป็นต้องมีเลขบัตรประจำตัวประชาชนเพื่อการให้บริการ');
            }
        });
    }

    // *** เพิ่มใหม่: ฟังก์ชัน AJAX Update เลขบัตรประชาชน ***
    function updateUserIdCard(idCardNumber) {
        const loadingAlert = Swal.fire({
            title: 'กำลังบันทึก...',
            html: '<div style="text-align: center;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #667eea;"></i><br><br>กำลังอัพเดทข้อมูลของคุณ</div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false
        });

        // AJAX Request
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
                    // อัพเดท userInfo ใน JavaScript
                    if (userInfo) {
                        userInfo.number = idCardNumber;
                    }

                    // อัพเดทฟิลด์ในฟอร์ม
                    const idCardField = document.querySelector('input[name="queue_number"]');
                    if (idCardField) {
                        idCardField.value = idCardNumber;
                        idCardField.readOnly = true;
                        idCardField.style.backgroundColor = '#f8f9fa';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ!',
                        text: 'อัพเดทเลขบัตรประจำตัวประชาชนแล้ว ดำเนินการจองคิวต่อ',
                        confirmButtonColor: '#667eea',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        // ดำเนินการจองคิวต่อ
                        submitForm();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: data.message || 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                        confirmButtonColor: '#dc3545'
                    }).then(() => {
                        // แสดง modal อีกครั้ง
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
</script>