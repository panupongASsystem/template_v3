<div class="text-center pages-head">
    <span class="font-pages-head">แบบฟอร์มออนไลน์</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<!-- Modal สำหรับการยืนยันการยื่นเอกสารโดยไม่เข้าสู่ระบบ -->
<div class="modal fade" id="guestConfirmModal" tabindex="-1" aria-labelledby="guestConfirmModalLabel" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content esv-modal-content" style="border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(102, 126, 234, 0.2), 0 8px 25px rgba(0,0,0,0.08); background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); overflow: hidden;">
            <div class="modal-header esv-modal-header" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); color: #2d3748; border-radius: 20px 20px 0 0; border-bottom: 1px solid rgba(102, 126, 234, 0.2); backdrop-filter: blur(10px);">
                <h5 class="modal-title esv-modal-title" id="guestConfirmModalLabel" style="font-weight: 600; color: #667eea; width: 100%; text-align: center;">
                    <i class="fas fa-file-upload me-2" style="color: #667eea;"></i>ยินดีต้อนรับสู่ระบบยื่นเอกสารออนไลน์
                </h5>
            </div>
            <div class="modal-body esv-modal-body text-center" style="padding: 2.5rem; background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);">
                <div class="mb-4">
                    <div style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
                        <i class="fas fa-user-shield" style="font-size: 2.5rem; color: #667eea; text-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);"></i>
                    </div>
                </div>
                <h5 class="mb-3" style="color: #2d3748; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">เริ่มต้นการยื่นเอกสาร</h5>
                <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.6; color: #6c757d;">เข้าสู่ระบบเพื่อติดตามสถานะเอกสารและได้รับการแจ้งเตือน สะดวกรวดเร็ว ปลอดภัย ไม่มีใครค้นหาเอกสารของคุณได้ หรือดำเนินการต่อโดยไม่ต้องเข้าสู่ระบบ ไม่ปลอดภัยบุคคลอื่นสามารถค้นหาเอกสารได้จากหน้าติดตาม</p>
                
                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-lg esv-login-btn" onclick="redirectToLogin()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4); transition: all 0.3s ease; font-size: 1.1rem;">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                    </button>
                    <button type="button" class="btn btn-lg esv-guest-btn" onclick="proceedAsGuest()" style="background: rgba(102, 126, 234, 0.08); border: 2px solid rgba(102, 126, 234, 0.3); color: #667eea; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; transition: all 0.3s ease; font-size: 1.1rem; backdrop-filter: blur(10px);">
                        <i class="fas fa-file-upload me-2"></i>ดำเนินการต่อโดยไม่เข้าสู่ระบบ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center pages-head">
    <span class="font-pages-head" style="font-size: 2.8rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(108, 117, 125, 0.2);">ยื่นเอกสารออนไลน์</span>
</div>

<div class="bg-pages" style="background: #ffffff; min-height: 100vh; padding: 2rem 0;">
    <div class="container-pages-news mb-5 mt-5" style="position: relative; z-index: 10; background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 1000px; overflow: hidden;" id="esv_form">
        
        <!-- เพิ่ม decorative element -->
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #667eea, #764ba2, #667eea); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;"></div>
        
        <!-- Alert Messages -->
        <div id="alertContainer"></div>
        
        <!-- ปุ่มติดตามสถานะ -->
        <div class="d-flex justify-content-end mb-3">
            <button type="button" 
                    onclick="redirectToTrackStatus()" 
                    class="btn track-status-btn" 
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                           border: none; 
                           color: white; 
                           padding: 0.7rem 1.5rem; 
                           border-radius: 12px; 
                           font-size: 0.95rem; 
                           font-weight: 600; 
                           transition: all 0.3s ease; 
                           box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); 
                           position: relative;
                           overflow: hidden;
                           cursor: pointer;">
                <span style="position: relative; z-index: 2;">
                    <i class="fas fa-search me-2"></i>ติดตามสถานะเอกสาร
                </span>
            </button>
        </div>

        <!-- แสดงข้อมูล User ที่ Login -->
        <div id="logged-in-user-info" style="display: none;"></div>
        
        <!-- ประเภทเอกสาร -->
        <div class="esv-type-selector" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 15px; padding: 1rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);">
            <div class="form-label-wrapper" style="margin-bottom: 1rem; background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                <label class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                    <i class="fas fa-layer-group me-2" style="color: #667eea;"></i>ประเภทเอกสาร
                </label>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-check p-3 esv-form-check" style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                        <input class="form-check-input" type="radio" name="document_type" id="request" value="request" checked style="transform: scale(1.2); margin: 0;">
                        <label class="form-check-label fw-bold" for="request" style="color: #495057; margin-left: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-file-alt me-2" style="color: #667eea;"></i>คำขอ
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check p-3 esv-form-check" style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                        <input class="form-check-input" type="radio" name="document_type" id="permit" value="permit" style="transform: scale(1.2); margin: 0;">
                        <label class="form-check-label fw-bold" for="permit" style="color: #495057; margin-left: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-file-signature me-2" style="color: #17a2b8;"></i>ใบอนุญาต
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check p-3 esv-form-check" style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                        <input class="form-check-input" type="radio" name="document_type" id="certificate" value="certificate" style="transform: scale(1.2); margin: 0;">
                        <label class="form-check-label fw-bold" for="certificate" style="color: #495057; margin-left: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-certificate me-2" style="color: #28a745;"></i>หนังสือรับรอง
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="underline">
            <form id="esvForm" 
                  action="/esv_ods/submit" 
                  method="post" 
                  class="form-horizontal" 
                  enctype="multipart/form-data"
                  onsubmit="return false;">
                <input type="hidden" name="form_token" id="formToken" value="">
                <br>
                
                <!-- แผนกปลายทางและหมวดหมู่เอกสาร -->
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-building me-2" style="color: #667eea;"></i>แผนกปลายทาง<span style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <select name="esv_ods_department_id" id="esv_ods_department_id" class="form-control" required style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                    <option value="">เลือกแผนกที่ต้องการส่งเอกสาร</option>
                                    <option value="1">สำนักงานปลัด</option>
                                    <option value="2">กองการศึกษา</option>
                                    <option value="3">กองคลัง</option>
                                    <option value="4">กองช่าง</option>
                                    <option value="5">กองสาธารณสุข</option>
                                    <option value="other">อื่นๆ (ระบุ)</option>
                                </select>
                                <div class="invalid-feedback" id="esv_ods_department_id_feedback"></div>
                                
                                <!-- ช่องระบุแผนกอื่นๆ -->
                                <div id="other_department" class="other-input" style="display: none; margin-top: 10px;">
                                    <input type="text" name="esv_ods_department_other" class="form-control" placeholder="ระบุแผนกอื่นๆ..." style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                    <div class="invalid-feedback" id="esv_ods_department_other_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-folder me-2" style="color: #667eea;"></i>หมวดหมู่เอกสาร<span style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <select name="esv_ods_category_id" id="esv_ods_category_id" class="form-control" required style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                    <option value="">เลือกหมวดหมู่เอกสาร</option>
                                    <optgroup label="หมวดหมู่ทั่วไป">
                                        <option value="1">ใบอนุญาติประกอบการ</option>
                                        <option value="2">ใบรับรองต่างๆ</option>
                                        <option value="3">คำขอรับบริการ</option>
                                    </optgroup>
                                    <optgroup label="เอกสารเฉพาะแผนก">
                                        <option value="4">ใบอนุญาตก่อสร้าง (ค่าธรรมเนียม 500 บาท)</option>
                                        <option value="5">ใบรับรองการศึกษา (ค่าธรรมเนียม 50 บาท)</option>
                                        <option value="6">หนังสือรับรองเงินเดือน (ค่าธรรมเนียม 20 บาท)</option>
                                    </optgroup>
                                    <option value="other">อื่นๆ (ระบุ)</option>
                                </select>
                                <div class="invalid-feedback" id="esv_ods_category_id_feedback"></div>
                                
                                <!-- ช่องระบุหมวดหมู่อื่นๆ -->
                                <div id="other_category" class="other-input" style="display: none; margin-top: 10px;">
                                    <input type="text" name="esv_ods_category_other" class="form-control" placeholder="ระบุหมวดหมู่เอกสารอื่นๆ..." style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                    <div class="invalid-feedback" id="esv_ods_category_other_feedback"></div>
                                </div>
                                
                                <!-- แสดงข้อมูลหมวดหมู่ที่เลือก -->
                                <div id="category_info" class="category-info mt-2" style="display: none; background: #e3f2fd; padding: 10px; border-radius: 5px; font-size: 0.9em;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- เรื่องที่ต้องการยื่นเอกสาร -->
                <div class="form-group mb-4">
                    <div class="form-label-wrapper" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                        <label class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                            <i class="fas fa-edit me-2" style="color: #667eea;"></i>เรื่องที่ต้องการยื่นเอกสาร<span style="color: #dc3545; margin-left: 0.2rem;">*</span>
                        </label>
                    </div>
                    <div class="col-sm-12">
                        <input type="text" name="esv_ods_topic" class="form-control" required placeholder="กรอกเรื่องที่ต้องการยื่นเอกสาร..." style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                        <div class="invalid-feedback" id="esv_ods_topic_feedback"></div>
                    </div>
                </div>

                <div class="row">
                    <!-- ชื่อ-นามสกุล -->
                    <div class="col-md-5">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-user me-2" style="color: #667eea;"></i>ชื่อ-นามสกุล<span style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="text" name="esv_ods_by" id="esv_ods_by" class="form-control" required placeholder="เช่น นางสาว น้ำใส ใจชื่นบาน" style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="esv_ods_by_feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- เบอร์โทรศัพท์ -->
                    <div class="col-md-3">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-phone me-2" style="color: #667eea;"></i>เบอร์โทรศัพท์<span style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="tel" id="esv_ods_phone" name="esv_ods_phone" class="form-control" required placeholder="เช่น 0812345678" pattern="\d{10}" style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="esv_ods_phone_feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- อีเมล -->
                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-envelope me-2" style="color: #667eea;"></i>อีเมล<span style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="email" name="esv_ods_email" class="form-control" required placeholder="example@youremail.com" style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="esv_ods_email_feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ที่อยู่ -->
                <div class="form-group mb-4">
                    <div class="form-label-wrapper" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                        <label class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                            <i class="fas fa-map-marker-alt me-2" style="color: #667eea;"></i>ที่อยู่<span style="color: #dc3545; margin-left: 0.2rem;">*</span>
                        </label>
                    </div>
                    <div class="col-sm-12">
                        <input type="text" name="esv_ods_address" class="form-control" required placeholder="กรอกข้อมูลที่อยู่ของคุณ" style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                        <div class="invalid-feedback" id="esv_ods_address_feedback"></div>
                    </div>
                </div>

                <!-- รายละเอียด -->
                <div class="form-group mb-4">
                    <div class="form-label-wrapper" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                        <label for="esv_ods_detail" class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                            <i class="fas fa-align-left me-2" style="color: #667eea;"></i>รายละเอียดเพิ่มเติม<span style="color: #dc3545; margin-left: 0.2rem;">*</span>
                        </label>
                    </div>
                    <div class="col-sm-12">
                        <textarea name="esv_ods_detail" class="form-control" id="esv_ods_detail" rows="6" required placeholder="กรอกรายละเอียดเพิ่มเติม..." style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); resize: vertical;"></textarea>
                        <div class="invalid-feedback" id="esv_ods_detail_feedback"></div>
                        <div id="detail_counter" class="char-counter" style="font-size: 0.9rem; color: #6c757d; text-align: right; margin-top: 0.5rem;"></div>
                    </div>
                </div>
                
                <br>
                
                <div class="row" style="padding-bottom: 20px;">
                    <!-- ไฟล์แนบ -->
                    <div class="col-9">
                        <div class="form-group">
                            <div class="form-label-wrapper" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label" style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-paperclip me-2" style="color: #667eea;"></i>แนบเอกสาร<span style="color: #dc3545; margin-left: 0.2rem;">*</span><small style="color: #6c757d; font-weight: 400; margin-left: 0.5rem;">(PDF, DOC, DOCX, รูปภาพ)</small>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <!-- File Upload Zone -->
                                <div class="file-upload-wrapper" style="border: 2px dashed #dee2e6; border-radius: 15px; padding: 1.5rem; text-align: center; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); transition: all 0.3s ease; cursor: pointer; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15);" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragenter="handleDragEnter(event)" ondragleave="handleDragLeave(event)">
                                    <div id="upload-placeholder" class="upload-placeholder">
                                        <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #667eea; margin-bottom: 0.5rem;"></i>
                                        <p style="margin: 0; color: #6c757d; font-size: 1rem;">คลิกเพื่อเลือกไฟล์ หรือลากไฟล์มาวางที่นี่</p>
                                        <small class="text-muted mt-2 d-block">รองรับไฟล์: PDF, DOC, DOCX, JPG, PNG (สูงสุด 1 ไฟล์)(ไม่เกิน 10 MB)</small>
                                    </div>
                                </div>
                                <input type="file" id="esv_ods_file" name="esv_ods_file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required onchange="handleFileSelect(this)" style="display: none;">
                                
                                <!-- File Preview Area -->
                                <div id="file-preview-area" class="file-preview-area mt-3" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted" style="font-size: 0.9rem;">
                                            <i class="fas fa-paperclip me-1"></i>ไฟล์ที่เลือก
                                        </span>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearFile()" style="border-radius: 8px; font-size: 0.8rem;">
                                            <i class="fas fa-times me-1"></i>ลบไฟล์
                                        </button>
                                    </div>
                                    <div id="preview-container" class="preview-container" style="background: #f8f9fa; border-radius: 10px; padding: 1rem;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ปุ่มส่ง -->
                    <div class="col-3">
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" 
                                    id="submitEsvBtn" 
                                    class="btn modern-submit-btn" 
                                    onclick="handleEsvSubmit(event)"
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; padding: 1rem 2rem; border-radius: 15px; font-size: 1.1rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden; min-width: 150px;">
                                <span style="position: relative; z-index: 2;">
                                    <i class="fas fa-paper-plane me-2"></i>ส่งเอกสาร
                                </span>
                                <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; z-index: 1;" class="btn-shine"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- หมายเหตุและปุ่มเพิ่มเติม -->
        <div class="row mt-4">
            <div class="col-6" style="color: #dc3545;">
                <span><b>หมายเหตุ</b><br>
                    1. ผู้ยื่นคำขอดาวน์โหลดเอกสารเพื่อกรอกข้อมูลในใบคำขอต่างๆ และแนบเอกสารในช่องส่งฟอร์มเอกสาร<br>
                    2. เจ้าหน้าที่รับเรื่อง พิจารณาเอกสาร<br>
                    3. แจ้งผลการดำเนินงานทางเบอร์โทรหรืออีเมลที่ผู้ยื่นคำขอแจ้งไว้<br>
                    4. เลือกแผนกปลายทางและหมวดหมู่เอกสารให้ถูกต้องเพื่อความรวดเร็วในการดำเนินการ<br>
                    5. ระบบจะส่งรหัสติดตามทางอีเมลหลังจากยื่นเอกสารสำเร็จ</span>
            </div>
            <div class="col-6 text-end">
                <button type="button" onclick="showDownloadTemplates()" class="btn btn-info me-2" style="border-radius: 12px;">
                    <i class="fas fa-download"></i> ดาวน์โหลดแบบฟอร์ม
                </button>
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
    background: #ffffff;
}

/* Form styling */
.form-label-wrapper:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.12) 0%, rgba(118, 75, 162, 0.12) 100%) !important;
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.2) !important;
    transform: translateY(-2px);
}

.form-control:focus, .form-select:focus {
    border-color: transparent !important;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25) !important;
    transform: translateY(-1px);
    background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%) !important;
}

.track-status-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
    background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%) !important;
}

.modern-submit-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4) !important;
    background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%) !important;
}

.modern-submit-btn:hover .btn-shine {
    left: 100%;
}

.esv-form-check:hover {
    background: rgba(102, 126, 234, 0.05) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.file-upload-wrapper:hover {
    background: linear-gradient(135deg, #f0f4ff 0%, #e8f2ff 100%) !important;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2) !important;
    transform: translateY(-2px);
    border-color: #667eea !important;
}

.file-upload-wrapper.drag-over {
    background: linear-gradient(135deg, #e8f2ff 0%, #f0f4ff 100%) !important;
    border-color: #667eea !important;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3) !important;
    transform: scale(1.02);
}

.preview-item {
    position: relative;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    padding: 1rem;
    text-align: center;
}

.preview-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
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
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

/* Modal specific styles */
.esv-modal-content {
    z-index: 9999 !important;
}

.esv-login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5) !important;
    background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%) !important;
}

.esv-guest-btn:hover {
    background: rgba(102, 126, 234, 0.15) !important;
    border-color: rgba(102, 126, 234, 0.5) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3) !important;
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
    
    .row .col-md-5, .row .col-md-3, .row .col-md-4, .row .col-6 {
        width: 100% !important;
        margin-bottom: 1rem;
    }
    
    .col-9, .col-3 {
        width: 100% !important;
    }
}
</style>

<!-- Font Awesome และ Bootstrap -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// *** JavaScript Code - Complete ESV System ***

// ตัวแปร Global
const isUserLoggedIn = false; // จะได้จาก PHP
const userInfo = null; // จะได้จาก PHP
let hasConfirmedAsGuest = isUserLoggedIn;
let guestModalInstance = null;
let selectedFile = null;
const maxFileSize = 10 * 1024 * 1024; // 10MB
const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/jpg', 'image/png'];
let formSubmitting = false;

// เมื่อเอกสารโหลดเสร็จ
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('esvForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            return false;
        });
    }
    
    setupPhoneValidation();
    setupDetailValidation();
    updateFormFieldsBasedOnLoginStatus();
    
    const uploadWrapper = document.querySelector('.file-upload-wrapper');
    if (uploadWrapper) {
        uploadWrapper.addEventListener('click', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('esv_ods_file');
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

    setupFormEvents();
    console.log('✅ ESV form initialized');
});

// ตั้งค่า Event Listeners
function setupFormEvents() {
    // แผนก dropdown
    document.getElementById('esv_ods_department_id').addEventListener('change', function() {
        const otherDept = document.getElementById('other_department');
        if (this.value === 'other') {
            otherDept.style.display = 'block';
            otherDept.querySelector('input').setAttribute('required', 'required');
        } else {
            otherDept.style.display = 'none';
            otherDept.querySelector('input').removeAttribute('required');
            otherDept.querySelector('input').value = '';
        }
        loadCategoriesByDepartment(this.value);
    });

    // หมวดหมู่ dropdown
    document.getElementById('esv_ods_category_id').addEventListener('change', function() {
        const otherCategory = document.getElementById('other_category');
        const categoryInfo = document.getElementById('category_info');
        
        if (this.value === 'other') {
            otherCategory.style.display = 'block';
            otherCategory.querySelector('input').setAttribute('required', 'required');
            categoryInfo.style.display = 'none';
        } else {
            otherCategory.style.display = 'none';
            otherCategory.querySelector('input').removeAttribute('required');
            otherCategory.querySelector('input').value = '';
            showCategoryInfo(this.value);
        }
    });
}

// ตั้งค่าการตรวจสอบเบอร์โทร
function setupPhoneValidation() {
    const phoneInput = document.getElementById('esv_ods_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) value = value.slice(0, 10);
            e.target.value = value;
        });
    }
}

// ตั้งค่าการตรวจสอบรายละเอียด
function setupDetailValidation() {
    const detailTextarea = document.getElementById('esv_ods_detail');
    if (detailTextarea) {
        const minLength = 10;
        const maxLength = 2000;
        
        let counterElement = document.getElementById('detail_counter');
        if (!counterElement) {
            counterElement = document.createElement('div');
            counterElement.id = 'detail_counter';
            counterElement.className = 'char-counter';
            counterElement.style.cssText = 'font-size: 0.9rem; color: #6c757d; text-align: right; margin-top: 0.5rem;';
            detailTextarea.parentNode.appendChild(counterElement);
        }
        
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
        
        detailTextarea.addEventListener('input', function(e) {
            const value = e.target.value.trim();
            const length = value.length;
            
            updateCounter();
            
            e.target.classList.remove('is-valid', 'is-invalid');
            
            if (length === 0) {
                // ไม่แสดงอะไรถ้ายังไม่กรอก
            } else if (length < minLength) {
                e.target.classList.add('is-invalid');
            } else if (length > maxLength) {
                e.target.classList.add('is-invalid');
            } else {
                e.target.classList.add('is-valid');
            }
        });
        
        updateCounter();
    }
}

// อัพเดทฟิลด์ตามสถานะ login
function updateFormFieldsBasedOnLoginStatus() {
    if (!isUserLoggedIn || !userInfo) return;
    
    const nameField = document.querySelector('input[name="esv_ods_by"]');
    const phoneField = document.querySelector('input[name="esv_ods_phone"]');
    const emailField = document.querySelector('input[name="esv_ods_email"]');
    
    if (nameField && userInfo.name) {
        nameField.value = userInfo.name;
        nameField.readOnly = true;
        nameField.style.backgroundColor = '#f8f9fa';
    }
    
    if (phoneField && userInfo.phone) {
        phoneField.value = userInfo.phone;
        phoneField.readOnly = true;
        phoneField.style.backgroundColor = '#f8f9fa';
    }
    
    if (emailField && userInfo.email) {
        emailField.value = userInfo.email;
        emailField.readOnly = true;
        emailField.style.backgroundColor = '#f8f9fa';
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
        
        const userInfoHTML = `
            <div id="logged-in-user-info" class="alert alert-success" style="
                border-radius: 15px; 
                background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); 
                border: 1px solid rgba(102, 126, 234, 0.3);
                margin-bottom: 2rem;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
                backdrop-filter: blur(10px);
            ">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div style="
                            width: 60px; 
                            height: 60px; 
                            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); 
                            border-radius: 50%; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center;
                            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
                        ">
                            <i class="fas fa-user-check" style="font-size: 1.8rem; color: #667eea;"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h5 class="mb-2" style="color: #2d3748; font-weight: 600;">
                            <i class="fas fa-check-circle me-2"></i>ใช้ข้อมูลจากบัญชีของคุณ
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1" style="color: #4a5568; font-size: 0.9rem;"><i class="fas fa-user me-2"></i><strong>ชื่อ:</strong> ${userName}</div>
                                <div class="mb-1" style="color: #4a5568; font-size: 0.9rem;"><i class="fas fa-envelope me-2"></i><strong>อีเมล:</strong> ${userEmail}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1" style="color: #4a5568; font-size: 0.9rem;"><i class="fas fa-phone me-2"></i><strong>เบอร์โทร:</strong> ${userPhone}</div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small style="color: #4a5568; display: flex; align-items: center;">
                                <i class="fas fa-info-circle me-1"></i> 
                                ระบบจะใช้ข้อมูลจากบัญชีของคุณโดยอัตโนมัติ
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        const formContainer = document.getElementById('esv_form');
        const firstFormGroup = formContainer.querySelector('.form-group');
        if (firstFormGroup) {
            firstFormGroup.insertAdjacentHTML('beforebegin', userInfoHTML);
        }
        
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
            text: 'คุณสามารถยื่นเอกสารได้ทันที ข้อมูลของคุณจะถูกใช้โดยอัตโนมัติ',
            timer: 3000,
            showConfirmButton: false,
            position: 'top-end',
            toast: true,
            background: 'linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%)',
            color: '#2d3748'
        });
    }
}

// *** File handling functions ***
async function handleFileSelect(input) {
    if (input._processing) return;
    input._processing = true;
    
    const file = input.files[0];
    if (!file) {
        input._processing = false;
        return;
    }
    
    if (!validateFile(file)) {
        input._processing = false;
        input.value = '';
        return;
    }
    
    selectedFile = file;
    updateFileDisplay();
    
    setTimeout(() => {
        input._processing = false;
    }, 100);
}

function validateFile(file) {
    if (!allowedTypes.includes(file.type.toLowerCase())) {
        showAlert('error', 'ประเภทไฟล์ไม่ถูกต้อง', 'รองรับเฉพาะไฟล์ PDF, DOC, DOCX และรูปภาพเท่านั้น');
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
    
    if (!selectedFile) {
        uploadPlaceholder.style.display = 'block';
        previewArea.style.display = 'none';
        return;
    }
    
    uploadPlaceholder.style.display = 'none';
    previewArea.style.display = 'block';
    
    const previewItem = createPreviewItem(selectedFile);
    previewContainer.innerHTML = '';
    previewContainer.appendChild(previewItem);
}

function createPreviewItem(file) {
    const div = document.createElement('div');
    div.className = 'preview-item';
    
    let icon = 'fas fa-file';
    let color = '#6c757d';
    
    if (file.type === 'application/pdf') {
        icon = 'fas fa-file-pdf';
        color = '#dc3545';
    } else if (file.type.includes('word')) {
        icon = 'fas fa-file-word';
        color = '#2b579a';
    } else if (file.type.includes('image')) {
        icon = 'fas fa-file-image';
        color = '#28a745';
    }
    
    div.innerHTML = `
        <i class="${icon}" style="font-size: 2rem; color: ${color}; margin-bottom: 0.5rem;"></i>
        <div>
            <strong>${file.name}</strong><br>
            <small>ขนาด: ${formatFileSize(file.size)}</small>
        </div>
    `;
    
    return div;
}

function clearFile() {
    selectedFile = null;
    document.getElementById('esv_ods_file').value = '';
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
        showAlert('error', 'ไม่พบไฟล์ที่รองรับ', 'กรุณาเลือกไฟล์ PDF, DOC, DOCX หรือรูปภาพเท่านั้น');
        return;
    }
    
    const fileInput = document.getElementById('esv_ods_file');
    fileInput.files = e.dataTransfer.files;
    handleFileSelect(fileInput);
}

// Form submission
function handleEsvSubmit(event) {
    event.preventDefault();
    
    if (formSubmitting) return false;
    
    if (!isUserLoggedIn && !hasConfirmedAsGuest) {
        showModal();
    } else {
        submitForm();
    }
    
    return false;
}

function submitForm() {
    try {
        const form = document.getElementById('esvForm');
        
        // ตรวจสอบรายละเอียดเพิ่มเติม
        const detailField = document.querySelector('textarea[name="esv_ods_detail"]');
        if (detailField) {
            const detailValue = detailField.value.trim();
            const minDetailLength = 10;
            
            if (detailValue.length < minDetailLength) {
                showAlert('warning', 
                    'รายละเอียดไม่เพียงพอ', 
                    `กรุณากรอกรายละเอียดเพิ่มเติมอย่างน้อย ${minDetailLength} ตัวอักษร (ปัจจุบัน ${detailValue.length} ตัวอักษร)`
                );
                detailField.focus();
                detailField.classList.add('is-invalid');
                return;
            }
        }
        
        // ตรวจสอบไฟล์แนบ
        if (!selectedFile) {
            showAlert('warning', 'กรุณาแนบเอกสาร', 'จำเป็นต้องแนบเอกสารเพื่อยื่นคำขอ');
            document.getElementById('esv_ods_file').focus();
            return;
        }
        
        if (!form || !form.checkValidity()) {
            console.warn('❌ Form validation failed');
            
            const fieldNameMapping = {
                'esv_ods_department_id': 'แผนกปลายทาง',
                'esv_ods_category_id': 'หมวดหมู่เอกสาร',
                'esv_ods_topic': 'เรื่องที่ต้องการยื่นเอกสาร',
                'esv_ods_by': 'ชื่อ-นามสกุล',
                'esv_ods_phone': 'เบอร์โทรศัพท์',
                'esv_ods_email': 'อีเมล',
                'esv_ods_address': 'ที่อยู่',
                'esv_ods_detail': 'รายละเอียด'
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
                    return fieldNameMapping[field.name] || field.name || 'ฟิลด์ที่ไม่ทราบชื่อ';
                });
                
                showAlert('warning', 'กรุณากรอกข้อมูลให้ครบถ้วน', 'มีข้อมูลที่จำเป็นยังไม่ได้กรอก: ' + fieldNames.join(', '));
                
                if (visibleInvalidFields[0]) {
                    visibleInvalidFields[0].focus();
                }
            }
            
            return;
        }
        
        if (formSubmitting) return;
        formSubmitting = true;
        
        const submitBtn = document.getElementById('submitEsvBtn');
        const originalContent = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังส่ง...';
        
        const formData = new FormData();
        const formElements = form.elements;
        
        // เพิ่มข้อมูลฟอร์ม
        for (let element of formElements) {
            if (element.type === 'file' || element.type === 'button' || element.type === 'submit') continue;
            if (element.name && element.value !== '') {
                formData.append(element.name, element.value);
            }
        }
        
        // เพิ่มประเภทเอกสาร
        const documentType = document.querySelector('input[name="document_type"]:checked');
        if (documentType) {
            formData.append('document_type', documentType.value);
        }
        
        // เพิ่มไฟล์
        if (selectedFile) {
            formData.append('esv_ods_file', selectedFile, selectedFile.name);
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
                            message: 'ยื่นเอกสารสำเร็จ',
                            tracking_code: 'ESV' + Date.now().toString().slice(-6)
                        };
                    }
                });
            }
        })
        .then(jsonResponse => {
            if (jsonResponse.success) {
                const trackingCode = jsonResponse.tracking_code || 'ESV' + Date.now().toString().slice(-6);
                
                const userTypeText = isUserLoggedIn ? 
                    'คุณสามารถติดตามสถานะได้ในหน้าบัญชีของคุณ' : 
                    'คุณสามารถติดตามสถานะได้ด้วยรหัสติดตามนี้';
                
                const confirmButtonText = isUserLoggedIn ? 
                    '<i class="fas fa-user-check me-2"></i>ไปยังหน้าบัญชีของฉัน' : 
                    '<i class="fas fa-search me-2"></i>ติดตามสถานะ';
                
                Swal.fire({
                    icon: 'success',
                    title: 'ยื่นเอกสารสำเร็จ!',
                    html: `
                        <div style="text-align: center;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
                                <i class="fas fa-check-circle" style="font-size: 2.5rem; color: #667eea;"></i>
                            </div>
                            
                            <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); 
                                        padding: 1.5rem; border-radius: 15px; margin: 1rem 0;
                                        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);">
                                <h3 style="color: #2d3748; margin-bottom: 0.5rem; font-weight: 600;">
                                    📝 รหัสติดตาม
                                </h3>
                                <div style="font-size: 2.2rem; font-weight: bold; color: #667eea; 
                                           letter-spacing: 2px; margin: 1rem 0;">
                                    ${trackingCode}
                                </div>
                                <button onclick="copyTrackingCode('${trackingCode}')" 
                                        class="btn btn-sm btn-outline-primary mt-2"
                                        style="border-radius: 12px; padding: 0.5rem 1rem; font-weight: 600;">
                                    <i class="fas fa-copy me-1"></i> คัดลอกรหัส
                                </button>
                            </div>
                            
                            <div style="background: rgba(23, 162, 184, 0.1); padding: 1rem; border-radius: 12px; margin: 1rem 0;">
                                <p style="color: #17a2b8; margin: 0; font-size: 0.95rem;">
                                    <i class="fas fa-info-circle me-2"></i>${userTypeText}
                                </p>
                            </div>
                            
                            <p style="color: #666; margin-top: 1rem;">ขอบคุณที่ใช้บริการยื่นเอกสารออนไลน์</p>
                        </div>
                    `,
                    confirmButtonText: confirmButtonText,
                    confirmButtonColor: '#667eea',
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
                        if (isUserLoggedIn) {
                            window.location.href = '/esv_ods/my_documents';
                        } else {
                            window.location.href = `/esv_ods/track?code=${trackingCode}`;
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
            showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถยื่นเอกสารได้ กรุณาลองใหม่อีกครั้ง');
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
    const form = document.getElementById('esvForm');
    if (form) form.reset();
    selectedFile = null;
    updateFileDisplay();
    updateFormFieldsBasedOnLoginStatus();
    
    // รีเซ็ตประเภทเอกสาร
    document.getElementById('request').checked = true;
    
    // ซ่อน other inputs
    document.getElementById('other_department').style.display = 'none';
    document.getElementById('other_category').style.display = 'none';
    document.getElementById('category_info').style.display = 'none';
}

function copyTrackingCode(code) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(code).then(() => {
            showAlert('success', 'คัดลอกแล้ว', `คัดลอกรหัสติดตาม ${code} แล้ว`, 2000);
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
    showAlert('info', 'ดำเนินการต่อโดยไม่เข้าสู่ระบบ', 'คุณสามารถกรอกข้อมูลและยื่นเอกสารได้แล้ว', 2000);
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
        window.location.href = '/user?redirect=' + encodeURIComponent(currentUrl);
    });
}

// ติดตามสถานะ
function redirectToTrackStatus() {
    const userType = isUserLoggedIn ? (userInfo.user_type || 'public') : 'guest';
    
    if (isUserLoggedIn) {
        window.location.href = '/esv_ods/my_documents';
    } else {
        window.location.href = '/esv_ods/track';
    }
}

// AJAX Functions
function loadCategoriesByDepartment(departmentId) {
    if (departmentId && departmentId !== 'other') {
        fetch('/esv_ods/get_categories_by_department', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'department_id=' + departmentId
        })
        .then(response => response.json())
        .then(data => {
            updateCategoryOptions(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function updateCategoryOptions(categories) {
    const categorySelect = document.getElementById('esv_ods_category_id');
    
    if (categories.length > 0) {
        let newOptions = '<optgroup label="หมวดหมู่ที่เกี่ยวข้อง">';
        categories.forEach(category => {
            newOptions += `<option value="${category.esv_category_id}">${category.esv_category_name}`;
            if (category.esv_category_fee > 0) {
                newOptions += ` (ค่าธรรมเนียม ${parseFloat(category.esv_category_fee).toLocaleString()} บาท)`;
            }
            newOptions += '</option>';
        });
        newOptions += '</optgroup>';
        
        const otherOption = '<option value="other">อื่นๆ (ระบุ)</option>';
        categorySelect.innerHTML = categorySelect.innerHTML.replace(otherOption, '') + newOptions + otherOption;
    }
}

function showCategoryInfo(categoryId) {
    const categoryInfo = document.getElementById('category_info');
    
    if (categoryId && categoryId !== 'other') {
        categoryInfo.style.display = 'block';
        categoryInfo.innerHTML = '<i class="fas fa-info-circle"></i> กำลังโหลดข้อมูลหมวดหมู่...';
        
        // TODO: เรียก AJAX เพื่อดึงข้อมูลรายละเอียดหมวดหมู่
    } else {
        categoryInfo.style.display = 'none';
    }
}

function showDownloadTemplates() {
    Swal.fire({
        title: 'ดาวน์โหลดแบบฟอร์ม',
        html: `
            <div class="list-group">
                <a href="/downloads/forms/application_form.pdf" class="list-group-item list-group-item-action">
                    <i class="fas fa-file-pdf text-danger me-2"></i>แบบฟอร์มคำขอทั่วไป
                </a>
                <a href="/downloads/forms/permit_form.pdf" class="list-group-item list-group-item-action">
                    <i class="fas fa-file-pdf text-danger me-2"></i>แบบฟอร์มขอใบอนุญาต
                </a>
                <a href="/downloads/forms/certificate_form.pdf" class="list-group-item list-group-item-action">
                    <i class="fas fa-file-pdf text-danger me-2"></i>แบบฟอร์มขอหนังสือรับรอง
                </a>
            </div>
        `,
        showCloseButton: true,
        showConfirmButton: false,
        width: '600px'
    });
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

// Auto-save draft (optional)
setInterval(function() {
    const formData = new FormData(document.getElementById('esvForm'));
    const formObj = {};
    for (let [key, value] of formData.entries()) {
        if (key !== 'esv_ods_file') {
            formObj[key] = value;
        }
    }
    localStorage.setItem('esv_ods_draft', JSON.stringify(formObj));
}, 30000); // ทุก 30 วินาที

// โหลด draft เมื่อเปิดหน้า
window.addEventListener('load', function() {
    const draft = localStorage.getItem('esv_ods_draft');
    if (draft) {
        try {
            const draftData = JSON.parse(draft);
            Object.keys(draftData).forEach(key => {
                const element = document.querySelector(`[name="${key}"]`);
                if (element && !element.value) {
                    element.value = draftData[key];
                }
            });
        } catch (e) {
            console.error('Error loading draft:', e);
        }
    }
});
</script>