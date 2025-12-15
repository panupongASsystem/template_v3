<div class="text-center pages-head">
    <span class="font-pages-head">ยื่นเอกสารออนไลน์</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<!-- Modal สำหรับการยืนยันการยื่นเอกสารโดยไม่เข้าสู่ระบบ -->
<div class="modal fade" id="guestConfirmModal" tabindex="-1" aria-labelledby="guestConfirmModalLabel" aria-hidden="true"
    style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content esv-modal-content"
            style="border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(102, 126, 234, 0.2), 0 8px 25px rgba(0,0,0,0.08); background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); overflow: hidden;">
            <div class="modal-header esv-modal-header"
                style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); color: #2d3748; border-radius: 20px 20px 0 0; border-bottom: 1px solid rgba(102, 126, 234, 0.2); backdrop-filter: blur(10px);">
                <h5 class="modal-title esv-modal-title" id="guestConfirmModalLabel"
                    style="font-weight: 600; color: #667eea; width: 100%; text-align: center;">
                    <i class="fas fa-file-upload me-2" style="color: #667eea;"></i>ยินดีต้อนรับสู่ระบบยื่นเอกสารออนไลน์
                </h5>
            </div>
            <div class="modal-body esv-modal-body text-center"
                style="padding: 2.5rem; background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);">
                <div class="mb-4">
                    <div
                        style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
                        <i class="fas fa-user-shield"
                            style="font-size: 2.5rem; color: #667eea; text-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);"></i>
                    </div>
                </div>
                <h5 class="mb-3" style="color: #2d3748; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    เริ่มต้นการยื่นเอกสาร</h5>
                <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.6; color: #6c757d;">
                    เข้าสู่ระบบเพื่อติดตามสถานะเอกสารและได้รับการแจ้งเตือน สะดวกรวดเร็ว ปลอดภัย
                    ไม่มีใครค้นหาเอกสารของคุณได้ หรือดำเนินการต่อโดยไม่ต้องเข้าสู่ระบบ
                    ไม่ปลอดภัยบุคคลอื่นสามารถค้นหาเอกสารได้จากหน้าติดตาม</p>

                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-lg esv-login-btn" onclick="redirectToLogin()"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4); transition: all 0.3s ease; font-size: 1.1rem;">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                    </button>
                    <button type="button" class="btn btn-lg esv-guest-btn" onclick="proceedAsGuest()"
                        style="background: rgba(102, 126, 234, 0.08); border: 2px solid rgba(102, 126, 234, 0.3); color: #667eea; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; transition: all 0.3s ease; font-size: 1.1rem; backdrop-filter: blur(10px);">
                        <i class="fas fa-file-upload me-2"></i>ดำเนินการต่อโดยไม่เข้าสู่ระบบ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center pages-head">
    <span class="font-pages-head"
        style="font-size: 2.8rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(108, 117, 125, 0.2);">ยื่นเอกสารออนไลน์</span>
</div>

<div class="bg-pages" style="background: #ffffff; min-height: 100vh; padding: 2rem 0;">
    <div class="container-pages-news mb-5 mt-5"
        style="position: relative; z-index: 10; background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 1000px; overflow: hidden;"
        id="esv_form">

        <!-- เพิ่ม decorative element -->
        <div
            style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #667eea, #764ba2, #667eea); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;">
        </div>

        <!-- Alert Messages -->
        <!-- Alert Messages -->
        <div id="alertContainer"></div>

        <!-- ปุ่มติดตามสถานะ -->
        <!-- ปุ่มติดตามสถานะและดาวน์โหลดแบบฟอร์ม -->
        <div class="d-flex justify-content-end mb-3 gap-2">
            <a href="<?php echo site_url('Esv_ods/forms_online'); ?>" class="btn btn-outline-info" style="padding: 0.7rem 1.5rem; 
                      border-radius: 12px; 
                      font-size: 0.95rem; 
                      font-weight: 600; 
                      transition: all 0.3s ease; 
                      box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3); 
                      border: 2px solid #17a2b8;
                      color: #17a2b8;">
                <i class="fas fa-download me-2"></i>ดาวน์โหลดแบบฟอร์ม
            </a>
            <button type="button" onclick="redirectToTrackStatus()" class="btn track-status-btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
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

        <!-- ฟอร์มยื่นเอกสาร -->
        <div class="underline">
            <form id="esvForm" action="<?php echo site_url('esv_ods/submit'); ?>" method="post" class="form-horizontal"
                enctype="multipart/form-data" onsubmit="return false;">
                <input type="hidden" name="form_token" id="formToken" value="">
                <br>

                <!-- ประเภทเอกสาร -->
                <div class="underline">
                    <form id="esvForm" action="<?php echo site_url('esv_ods/submit'); ?>" method="post"
                        class="form-horizontal" enctype="multipart/form-data" onsubmit="return false;">
                        <input type="hidden" name="form_token" id="formToken" value="">
                        <br>

                        <!-- ประเภทเอกสาร -->
                        <div class="esv-type-selector"
                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 15px; padding: 1rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);">
                            <div class="form-label-wrapper"
                                style="margin-bottom: 1rem; background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-layer-group me-2" style="color: #667eea;"></i>ประเภทเอกสาร<span
                                        style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="row">
                                <?php if (!empty($document_types)): ?>
                                    <?php foreach ($document_types as $index => $type): ?>
                                        <div class="col-md-4">
                                            <div class="form-check p-3 esv-form-check"
                                                style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                                                <input class="form-check-input" type="radio" name="document_type"
                                                    id="type_<?php echo $type->esv_type_id; ?>"
                                                    value="<?php echo $type->esv_type_id; ?>" <?php echo $index === 0 ? 'checked' : ''; ?> style="transform: scale(1.2); margin: 0;">
                                                <label class="form-check-label fw-bold"
                                                    for="type_<?php echo $type->esv_type_id; ?>"
                                                    style="color: #495057; margin-left: 0.8rem; display: flex; align-items: center;">
                                                    <i class="<?php echo $type->esv_type_icon ?? 'fas fa-file-alt'; ?> me-2"
                                                        style="color: <?php echo $type->esv_type_color ?? '#667eea'; ?>;"></i><?php echo $type->esv_type_name; ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-md-4">
                                        <div class="form-check p-3 esv-form-check"
                                            style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                                            <input class="form-check-input" type="radio" name="document_type" id="request"
                                                value="1" checked style="transform: scale(1.2); margin: 0;">
                                            <label class="form-check-label fw-bold" for="request"
                                                style="color: #495057; margin-left: 0.8rem; display: flex; align-items: center;">
                                                <i class="fas fa-file-alt me-2" style="color: #667eea;"></i>คำขอ
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check p-3 esv-form-check"
                                            style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                                            <input class="form-check-input" type="radio" name="document_type" id="permit"
                                                value="2" style="transform: scale(1.2); margin: 0;">
                                            <label class="form-check-label fw-bold" for="permit"
                                                style="color: #495057; margin-left: 0.8rem; display: flex; align-items: center;">
                                                <i class="fas fa-file-signature me-2" style="color: #17a2b8;"></i>ใบอนุญาต
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check p-3 esv-form-check"
                                            style="background: rgba(255,255,255,0.8); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70px;">
                                            <input class="form-check-input" type="radio" name="document_type"
                                                id="certificate" value="3" style="transform: scale(1.2); margin: 0;">
                                            <label class="form-check-label fw-bold" for="certificate"
                                                style="color: #495057; margin-left: 0.8rem; display: flex; align-items: center;">
                                                <i class="fas fa-certificate me-2" style="color: #28a745;"></i>หนังสือรับรอง
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- แผนกปลายทางและหมวดหมู่เอกสาร -->
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-4">
                                    <div class="form-label-wrapper"
                                        style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                        <label class="form-label"
                                            style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                            <i class="fas fa-building me-2" style="color: #667eea;"></i>แผนกปลายทาง<span
                                                style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                        </label>
                                    </div>
                                    <div class="col-sm-12">
                                        <select name="esv_ods_department_id" id="esv_ods_department_id"
                                            class="form-control" required
                                            style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                            <option value="">เลือกแผนกที่ต้องการส่งเอกสาร</option>
                                            <?php if (!empty($departments)): ?>
                                                <?php foreach ($departments as $dept): ?>
                                                    <option value="<?php echo $dept->pid; ?>"><?php echo $dept->pname; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            <option value="other">อื่นๆ (ระบุ)</option>
                                        </select>
                                        <div class="invalid-feedback" id="esv_ods_department_id_feedback"></div>

                                        <!-- ช่องระบุแผนกอื่นๆ -->
                                        <div id="other_department" class="other-input"
                                            style="display: none; margin-top: 10px;">
                                            <input type="text" name="esv_ods_department_other" class="form-control"
                                                placeholder="ระบุแผนกอื่นๆ..."
                                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                            <div class="invalid-feedback" id="esv_ods_department_other_feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- หมวดหมู่เอกสาร - ปรับปรุงใหม่ -->
                            <div class="col-6">
                                <div class="form-group mb-4">
                                    <div class="form-label-wrapper"
                                        style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                        <label class="form-label"
                                            style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                            <i class="fas fa-folder me-2"
                                                style="color: #667eea;"></i>หมวดหมู่เอกสาร<span
                                                style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                        </label>
                                    </div>
                                    <div class="col-sm-12">
                                        <select name="esv_ods_category_id" id="esv_ods_category_id" class="form-control"
                                            required
                                            style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                            <option value="">เลือกหมวดหมู่เอกสาร</option>
                                            <?php if (!empty($categories)): ?>
                                                <?php foreach ($categories as $group_name => $group_categories): ?>
                                                    <optgroup label="<?php echo htmlspecialchars($group_name); ?>">
                                                        <?php foreach ($group_categories as $category): ?>
                                                            <option value="<?php echo $category->esv_category_id; ?>"
                                                                data-department="<?php echo $category->esv_category_department_id; ?>"
                                                                data-fee="<?php echo $category->esv_category_fee; ?>"
                                                                data-days="<?php echo $category->esv_category_process_days; ?>">
                                                                <?php echo htmlspecialchars($category->esv_category_name); ?>
                                                                <?php if ($category->esv_category_fee > 0): ?>
                                                                    (ค่าธรรมเนียม
                                                                    <?php echo number_format($category->esv_category_fee, 0); ?> บาท)
                                                                <?php endif; ?>
                                                                <?php if ($category->esv_category_process_days > 0): ?>
                                                                    - <?php echo $category->esv_category_process_days; ?> วันทำการ
                                                                <?php endif; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            <option value="other">อื่นๆ (ระบุ)</option>
                                        </select>
                                        <div class="invalid-feedback" id="esv_ods_category_id_feedback"></div>

                                        <!-- ช่องระบุหมวดหมู่อื่นๆ -->
                                        <div id="other_category" class="other-input"
                                            style="display: none; margin-top: 10px;">
                                            <input type="text" name="esv_ods_category_other" class="form-control"
                                                placeholder="ระบุหมวดหมู่เอกสารอื่นๆ..."
                                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                            <div class="invalid-feedback" id="esv_ods_category_other_feedback"></div>
                                        </div>

                                        <!-- แสดงข้อมูลหมวดหมู่ที่เลือก -->
                                        <div id="category_info" class="category-info mt-2"
                                            style="display: none; background: linear-gradient(135deg, #e3f2fd 0%, #f0f9ff 100%); padding: 15px; border-radius: 12px; font-size: 0.95em; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div id="category_description" class="mb-2"></div>
                                                    <div id="category_department" class="text-muted"></div>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <div id="category_fee" class="text-info fw-bold mb-1"></div>
                                                    <div id="category_days" class="text-warning"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- เรื่องที่ต้องการยื่นเอกสาร -->
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                <label class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-edit me-2"
                                        style="color: #667eea;"></i>เรื่องที่ต้องการยื่นเอกสาร<span
                                        style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="text" name="esv_ods_topic" class="form-control" required
                                    placeholder="กรอกเรื่องที่ต้องการยื่นเอกสาร..."
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                <div class="invalid-feedback" id="esv_ods_topic_feedback"></div>
                            </div>
                        </div>

                        <!-- ข้อมูลส่วนตัว (แสดงเฉพาะเมื่อไม่ได้ login) -->
                        <div id="personal_info_section" style="<?php echo $is_logged_in ? 'display: none;' : ''; ?>">
                            <div class="row">
                                <!-- ชื่อ-นามสกุล -->
                                <div class="col-md-5">
                                    <div class="form-group mb-4">
                                        <div class="form-label-wrapper"
                                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                            <label class="form-label"
                                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                                <i class="fas fa-user me-2"
                                                    style="color: #667eea;"></i>ชื่อ-นามสกุล<span
                                                    style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                            </label>
                                        </div>
                                        <div class="col-sm-12">
                                            <input type="text" name="esv_ods_by" id="esv_ods_by" class="form-control"
                                                <?php echo !$is_logged_in ? 'required' : ''; ?>
                                                placeholder="เช่น นางสาว น้ำใส ใจชื่นบาน"
                                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                            <div class="invalid-feedback" id="esv_ods_by_feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- เบอร์โทรศัพท์ -->
                                <div class="col-md-3">
                                    <div class="form-group mb-4">
                                        <div class="form-label-wrapper"
                                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                            <label class="form-label"
                                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                                <i class="fas fa-phone me-2"
                                                    style="color: #667eea;"></i>เบอร์โทรศัพท์<span
                                                    style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                            </label>
                                        </div>
                                        <div class="col-sm-12">
                                            <input type="tel" id="esv_ods_phone" name="esv_ods_phone"
                                                class="form-control" <?php echo !$is_logged_in ? 'required' : ''; ?>
                                                placeholder="เช่น 0812345678" pattern="\d{10}"
                                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                            <div class="invalid-feedback" id="esv_ods_phone_feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- อีเมล -->
                                <div class="col-md-4">
                                    <div class="form-group mb-4">
                                        <div class="form-label-wrapper"
                                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                            <label class="form-label"
                                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                                <i class="fas fa-envelope me-2" style="color: #667eea;"></i>อีเมล<span
                                                    style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                            </label>
                                        </div>
                                        <div class="col-sm-12">
                                            <input type="email" name="esv_ods_email" class="form-control" <?php echo !$is_logged_in ? 'required' : ''; ?> placeholder="example@youremail.com"
                                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                                            <div class="invalid-feedback" id="esv_ods_email_feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- เลขบัตรประจำตัวประชาชน -->
                            <div class="form-group mb-4">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-id-card me-2"
                                            style="color: #667eea;"></i>เลขบัตรประจำตัวประชาชน<span
                                            style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-12">
                                    <div style="position: relative;">
                                        <input type="text" name="esv_ods_id_card" id="esv_ods_id_card"
                                            class="form-control" required placeholder="เลขบัตรประจำตัวประชาชน 13 หลัก"
                                            maxlength="13"
                                            style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">

                                        <!-- Loading และ Status Icons -->
                                        <div id="id_card_loading"
                                            style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); display: none;">
                                            <i class="fas fa-spinner fa-spin text-primary"></i>
                                        </div>
                                        <div id="id_card_success"
                                            style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); display: none;">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                        <div id="id_card_error"
                                            style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); display: none;">
                                            <i class="fas fa-times-circle text-danger"></i>
                                        </div>
                                    </div>

                                    <div class="invalid-feedback" id="esv_ods_id_card_feedback"></div>
                                    <div class="valid-feedback" id="esv_ods_id_card_valid" style="display: none;">
                                        <i class="fas fa-check-circle me-1"></i>เลขบัตรประชาชนถูกต้อง
                                    </div>

                                    <!-- ข้อความแนะนำ -->
                                    <small class="form-text text-muted mt-1">
                                        <i class="fas fa-info-circle me-1"></i>กรอกเลขบัตรประชาชน 13 หลัก
                                        (ไม่ต้องใส่เครื่องหมาย -)
                                    </small>
                                </div>
                            </div>

                            <!-- ที่อยู่ -->
                            <div class="form-group mb-4">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-map-marker-alt me-2" style="color: #667eea;"></i>ที่อยู่<span
                                            style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                    </label>
                                </div>

                                <!-- รหัสไปรษณีย์ -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="input-wrapper" style="position: relative;">
                                            <input type="text" id="esv_zipcode_field" name="esv_zipcode"
                                                class="form-control"
                                                placeholder="กรอกรหัสไปรษณีย์ 5 หลัก เพื่อเติมข้อมูลอัตโนมัติ"
                                                maxlength="5" pattern="[0-9]{5}"
                                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                            <i class="fas fa-mail-bulk input-icon"
                                                style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #667eea;"></i>
                                        </div>
                                        <small class="form-text text-muted">กรอกรหัสไปรษณีย์เพื่อเติมจังหวัด อำเภอ ตำบล
                                            อัตโนมัติ</small>

                                        <!-- Loading & Error indicators -->
                                        <div id="esv_zipcode_loading" class="text-center mt-1" style="display: none;">
                                            <small class="text-primary">
                                                <i class="fas fa-spinner fa-spin"></i> กำลังค้นหา...
                                            </small>
                                        </div>
                                        <div id="esv_zipcode_error" class="mt-1" style="display: none;">
                                            <small class="text-danger"></small>
                                        </div>
                                    </div>
                                </div>

                                <!-- ที่อยู่เพิ่มเติม (บังคับกรอก) -->
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="input-wrapper" style="position: relative;">
                                            <input type="text" id="esv_additional_address_field"
                                                name="esv_additional_address" class="form-control" required
                                                placeholder="กรอกที่อยู่เพิ่มเติม (บ้านเลขที่ ซอย ถนน หมู่บ้าน) *"
                                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                            <i class="fas fa-home input-icon"
                                                style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #667eea;"></i>
                                        </div>
                                        <small class="form-text text-muted">บ้านเลขที่ ซอย ถนน
                                            หรือรายละเอียดเพิ่มเติม</small>
                                    </div>
                                </div>

                                <!-- จังหวัด -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="input-wrapper" style="position: relative;">
                                            <select id="esv_province_field" name="esv_province" class="form-control"
                                                required
                                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                                <option value="">เลือกจังหวัด *</option>
                                            </select>
                                            <i class="fas fa-map input-icon"
                                                style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #667eea;"></i>
                                        </div>
                                    </div>

                                    <!-- อำเภอ -->
                                    <div class="col-md-6">
                                        <div class="input-wrapper" style="position: relative;">
                                            <select id="esv_amphoe_field" name="esv_amphoe" class="form-control"
                                                disabled required
                                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                                <option value="">เลือกอำเภอ *</option>
                                            </select>
                                            <i class="fas fa-city input-icon"
                                                style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #667eea;"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- ตำบล -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="input-wrapper" style="position: relative;">
                                            <select id="esv_district_field" name="esv_district" class="form-control"
                                                disabled required
                                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                                <option value="">เลือกตำบล *</option>
                                            </select>
                                            <i class="fas fa-home input-icon"
                                                style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #667eea;"></i>
                                        </div>
                                        <div class="invalid-feedback" id="esv_district_field_feedback"></div>
                                    </div>
                                </div>

                                <!-- ที่อยู่รวม (ส่งไปยัง esv_ods_address) -->
                                <input type="hidden" name="esv_ods_address" id="esv_full_address_field" value="">

                                <!-- ซ่อน hidden fields สำหรับข้อมูลที่อยู่แยก -->
                                <input type="hidden" name="guest_province" id="esv_guest_province_field" value="">
                                <input type="hidden" name="guest_amphoe" id="esv_guest_amphoe_field" value="">
                                <input type="hidden" name="guest_district" id="esv_guest_district_field" value="">
                                <input type="hidden" name="guest_zipcode" id="esv_guest_zipcode_field" value="">

                                <!-- แสดงที่อยู่ที่รวมแล้ว -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info" id="esv_address_preview"
                                            style="display: none; border-radius: 15px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border: 1px solid rgba(102, 126, 234, 0.3);">
                                            <strong><i class="fas fa-eye"></i> ที่อยู่ที่จะบันทึก:</strong>
                                            <div id="esv_address_preview_text"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="invalid-feedback" id="esv_ods_address_feedback"></div>
                            </div>
                        </div>

                        <!-- รายละเอียด -->
                        <div class="form-group mb-4">
                            <div class="form-label-wrapper"
                                style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); backdrop-filter: blur(10px);">
                                <label for="esv_ods_detail" class="form-label"
                                    style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                    <i class="fas fa-align-left me-2"
                                        style="color: #667eea;"></i>รายละเอียดเพิ่มเติม<span
                                        style="color: #dc3545; margin-left: 0.2rem;">*</span>
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <textarea name="esv_ods_detail" class="form-control" id="esv_ods_detail" rows="6"
                                    required placeholder="กรอกรายละเอียดเพิ่มเติม..."
                                    style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); resize: vertical;"></textarea>
                                <div class="invalid-feedback" id="esv_ods_detail_feedback"></div>
                                <div id="detail_counter" class="char-counter"
                                    style="font-size: 0.9rem; color: #6c757d; text-align: right; margin-top: 0.5rem;">
                                </div>
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
                                            <i class="fas fa-paperclip me-2" style="color: #667eea;"></i>แนบเอกสาร<span
                                                style="color: #dc3545; margin-left: 0.2rem;">*</span><small
                                                style="color: #6c757d; font-weight: 400; margin-left: 0.5rem;">(PDF,
                                                DOC, DOCX, รูปภาพ)</small>
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
                                                <p style="margin: 0; color: #6c757d; font-size: 1rem;">
                                                    คลิกเพื่อเลือกไฟล์ หรือลากไฟล์มาวางที่นี่</p>
                                                <small class="text-muted mt-2 d-block">รองรับไฟล์: PDF, DOC, DOCX, JPG,
                                                    PNG<br>สามารถแนบได้สูงสุด 5 ไฟล์ (รวมไม่เกิน 15 MB)</small>
                                            </div>
                                        </div>
                                        <input type="file" id="esv_ods_file" name="esv_ods_file[]" class="form-control"
                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required multiple
                                            onchange="handleFileSelect(this)" style="display: none;">

                                        <!-- File Preview Area -->
                                        <div id="file-preview-area" class="file-preview-area mt-3"
                                            style="display: none;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted" style="font-size: 0.9rem;">
                                                    <i class="fas fa-paperclip me-1"></i>ไฟล์ที่เลือก (<span
                                                        id="file-count">0</span>/5)
                                                </span>
                                                <div>
                                                    <span class="text-muted me-2" style="font-size: 0.8rem;">
                                                        ขนาดรวม: <span id="total-size">0 MB</span>/15 MB
                                                    </span>
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        onclick="clearAllFiles()"
                                                        style="border-radius: 8px; font-size: 0.8rem;">
                                                        <i class="fas fa-trash me-1"></i>ลบทั้งหมด
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="preview-container" class="preview-container row"
                                                style="background: #f8f9fa; border-radius: 10px; padding: 1rem;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ปุ่มส่ง -->
                            <div class="col-3">
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" id="submitEsvBtn" class="btn modern-submit-btn"
                                        onclick="handleEsvSubmit(event)"
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; padding: 1rem 2rem; border-radius: 15px; font-size: 1.1rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden; min-width: 150px;">
                                        <span style="position: relative; z-index: 2;">
                                            <i class="fas fa-paper-plane me-2"></i>ส่งเอกสาร
                                        </span>
                                        <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; z-index: 1;"
                                            class="btn-shine"></div>
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
                            1. ผู้ยื่นคำขอดาวน์โหลดเอกสารเพื่อกรอกข้อมูลในใบคำขอต่างๆ
                            และแนบเอกสารในช่องส่งฟอร์มเอกสาร<br>
                            2. เจ้าหน้าที่รับเรื่อง พิจารณาเอกสาร<br>
                            3. แจ้งผลการดำเนินงานทางเบอร์โทรหรืออีเมลที่ผู้ยื่นคำขอแจ้งไว้<br>
                            4. เลือกแผนกปลายทางและหมวดหมู่เอกสารให้ถูกต้องเพื่อความรวดเร็วในการดำเนินการ<br>
                            5. ระบบจะส่งรหัสติดตามทางอีเมลหลังจากยื่นเอกสารสำเร็จ</span>
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

        .form-control:focus,
        .form-select:focus {
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            padding: 1rem;
            text-align: center;
        }

        .preview-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
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

        /* ID Card validation styles */
        .form-control.is-valid {
            border-color: #28a745 !important;
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.25) !important;
        }

        .form-control.is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.25) !important;
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
            .row .col-md-4,
            .row .col-6 {
                width: 100% !important;
                margin-bottom: 1rem;
            }

            .col-9,
            .col-3 {
                width: 100% !important;
            }
        }
    </style>

    <!-- JavaScript -->
    <script>
        // ===================================================================
        // *** GLOBAL VARIABLES & CONFIGURATION ***
        // ===================================================================

        const isUserLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
        const userInfo = <?php echo json_encode($user_info); ?>;

        let hasConfirmedAsGuest = isUserLoggedIn;
        let guestModalInstance = null;
        let selectedFiles = [];
        let formSubmitting = false;
        let idCardValidationTimer = null;
        let isValidatingIdCard = false;

        // File upload constraints
        const FILE_CONFIG = {
            maxFiles: 5,
            maxTotalSize: 15 * 1024 * 1024, // 15MB
            maxIndividualFileSize: 5 * 1024 * 1024, // 5MB
            allowedTypes: [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/gif'
            ]
        };

        // Address system variables
        const ESV_API_BASE_URL = 'https://addr.assystem.co.th/index.php/zip_api';
        let esvZipcodeField, esvProvinceField, esvAmphoeField, esvDistrictField;
        let esvFullAddressField, esvAdditionalAddressField;
        let esvCurrentAddressData = [];

        // ===================================================================
        // *** INITIALIZATION ***
        // ===================================================================

        document.addEventListener('DOMContentLoaded', function () {
            console.log('🚀 Starting ESV Multiple Files System...');

            initializeForm();
            initializeValidationSystems();
            initializeFileUpload();
            initializeAddressSystem();
            updateFormFieldsBasedOnLoginStatus();

            // Show modals/messages
            if (!isUserLoggedIn) {
                setTimeout(() => {
                    if (!hasConfirmedAsGuest) showModal();
                }, 1000);
            } else {
                setTimeout(showWelcomeMessage, 500);
            }

            setupFormEvents();
            initializeRecaptcha();

            console.log('✅ ESV Multiple Files form initialized');
        });

        function initializeForm() {
            const form = document.getElementById('esvForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    return false;
                });
            }
        }

        function initializeValidationSystems() {
            setupPhoneValidation();
            setupDetailValidation();
            setupIdCardValidation();
        }

        function initializeFileUpload() {
            setupMultipleFileUpload();
        }

        function initializeAddressSystem() {
            initializeEsvAddressSystem();
        }

        function initializeRecaptcha() {
            if (window.RECAPTCHA_SITE_KEY && !window.recaptchaReady) {
                console.log('⏳ Waiting for reCAPTCHA to load...');

                let checkInterval = setInterval(function () {
                    if (window.recaptchaReady) {
                        console.log('✅ reCAPTCHA is now ready');
                        clearInterval(checkInterval);
                    }
                }, 100);

                setTimeout(function () {
                    if (!window.recaptchaReady) {
                        console.log('⚠️ reCAPTCHA timeout after 10 seconds');
                        clearInterval(checkInterval);
                    }
                }, 10000);
            }
        }

        // ===================================================================
        // *** MULTIPLE FILE UPLOAD SYSTEM ***
        // ===================================================================

        function setupMultipleFileUpload() {
            const uploadWrapper = document.querySelector('.file-upload-wrapper');
            const fileInput = document.getElementById('esv_ods_file');

            if (uploadWrapper && fileInput) {
                // Click to select files
                uploadWrapper.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    fileInput.click();
                });

                // File input change event
                fileInput.addEventListener('change', function (e) {
                    handleFileSelect(this);
                });

                // Drag and drop events
                uploadWrapper.addEventListener('dragover', handleDragOver);
                uploadWrapper.addEventListener('dragenter', handleDragEnter);
                uploadWrapper.addEventListener('dragleave', handleDragLeave);
                uploadWrapper.addEventListener('drop', handleDrop);

                console.log('✅ Multiple file upload system initialized');
            }
        }

        async function handleFileSelect(input) {
            if (input._processing) return;
            input._processing = true;

            try {
                const newFiles = Array.from(input.files);
                if (newFiles.length === 0) return;

                console.log(`📁 Processing ${newFiles.length} files...`);

                // Check total file count
                if (selectedFiles.length + newFiles.length > FILE_CONFIG.maxFiles) {
                    showAlert('warning', 'ไฟล์เกินจำนวนที่อนุญาต',
                        `สามารถแนบได้สูงสุด ${FILE_CONFIG.maxFiles} ไฟล์ คุณเลือกไฟล์ไปแล้ว ${selectedFiles.length} ไฟล์`);
                    return;
                }

                let validFiles = [];
                let totalSize = calculateTotalSize();
                let rejectedFiles = [];

                for (let file of newFiles) {
                    const validation = validateSingleFile(file, totalSize);

                    if (validation.valid) {
                        validFiles.push(file);
                        totalSize += file.size;
                    } else {
                        rejectedFiles.push({
                            name: file.name,
                            reason: validation.reason
                        });
                    }
                }

                // Show rejected files
                if (rejectedFiles.length > 0) {
                    const rejectedList = rejectedFiles.map(f => `• ${f.name}: ${f.reason}`).join('\n');
                    showAlert('warning', `ไฟล์ที่ไม่สามารถแนบได้ (${rejectedFiles.length} ไฟล์)`, rejectedList);
                }

                // Add valid files
                if (validFiles.length > 0) {
                    selectedFiles.push(...validFiles);
                    updateFileDisplay();

                    const successMsg = validFiles.length === 1 ?
                        `เพิ่มไฟล์ "${validFiles[0].name}" สำเร็จ` :
                        `เพิ่มไฟล์ ${validFiles.length} ไฟล์สำเร็จ`;

                    showAlert('success', 'เพิ่มไฟล์สำเร็จ', successMsg, 2000);
                    console.log(`✅ Added ${validFiles.length} files successfully`);
                }

            } catch (error) {
                console.error('❌ Error processing files:', error);
                showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถประมวลผลไฟล์ได้');
            } finally {
                // Reset input and processing flag
                input.value = '';
                setTimeout(() => {
                    input._processing = false;
                }, 100);
            }
        }

        function validateSingleFile(file, currentTotalSize) {
            // Check file type
            if (!FILE_CONFIG.allowedTypes.includes(file.type.toLowerCase())) {
                return { valid: false, reason: 'ประเภทไฟล์ไม่รองรับ' };
            }

            // Check individual file size
            if (file.size > FILE_CONFIG.maxIndividualFileSize) {
                return { valid: false, reason: `ขนาดไฟล์เกิน ${formatFileSize(FILE_CONFIG.maxIndividualFileSize)}` };
            }

            // Check total size
            if (currentTotalSize + file.size > FILE_CONFIG.maxTotalSize) {
                return { valid: false, reason: `ขนาดรวมจะเกิน ${formatFileSize(FILE_CONFIG.maxTotalSize)}` };
            }

            // Check duplicate
            const isDuplicate = selectedFiles.some(existingFile =>
                existingFile.name === file.name && existingFile.size === file.size
            );

            if (isDuplicate) {
                return { valid: false, reason: 'ไฟล์ซ้ำ' };
            }

            // Check empty filename
            if (!file.name || file.name.trim() === '') {
                return { valid: false, reason: 'ชื่อไฟล์ไม่ถูกต้อง' };
            }

            return { valid: true };
        }

        function calculateTotalSize() {
            return selectedFiles.reduce((total, file) => total + file.size, 0);
        }

        function updateFileDisplay() {
            const uploadPlaceholder = document.getElementById('upload-placeholder');
            const previewArea = document.getElementById('file-preview-area');
            const previewContainer = document.getElementById('preview-container');
            const fileCountElement = document.getElementById('file-count');
            const totalSizeElement = document.getElementById('total-size');

            if (selectedFiles.length === 0) {
                uploadPlaceholder.style.display = 'block';
                previewArea.style.display = 'none';
                return;
            }

            uploadPlaceholder.style.display = 'none';
            previewArea.style.display = 'block';

            // Update counters
            fileCountElement.textContent = selectedFiles.length;
            totalSizeElement.textContent = formatFileSize(calculateTotalSize());

            // Create preview cards
            previewContainer.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const previewItem = createFilePreviewCard(file, index);
                previewContainer.appendChild(previewItem);
            });

            // Add animation
            setTimeout(() => {
                previewContainer.querySelectorAll('.file-preview-card').forEach((card, index) => {
                    card.style.animation = `slideInUp 0.3s ease-out ${index * 0.1}s both`;
                });
            }, 10);
        }

        function createFilePreviewCard(file, index) {
            const div = document.createElement('div');
            div.className = 'col-md-6 col-lg-4 mb-3';

            const fileInfo = getFileInfo(file);
            const isMainFile = index === 0;

            div.innerHTML = `
                <div class="file-preview-card" style="
                    position: relative; 
                    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); 
                    border-radius: 15px; 
                    overflow: hidden; 
                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15); 
                    transition: all 0.3s ease; 
                    padding: 1.5rem; 
                    text-align: center;
                    border: ${isMainFile ? '2px solid #667eea' : '1px solid #dee2e6'};
                    min-height: 140px;
                ">
                    ${isMainFile ? `
                        <div style="
                            position: absolute; 
                            top: 8px; 
                            left: 8px; 
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                            color: white; 
                            padding: 4px 8px; 
                            border-radius: 8px; 
                            font-size: 0.7rem; 
                            font-weight: 600;
                        ">
                            <i class="fas fa-star me-1"></i>หลัก
                        </div>
                    ` : ''}
                    
                    <button type="button" 
                            onclick="removeFile(${index})" 
                            style="
                                position: absolute; 
                                top: 8px; 
                                right: 8px; 
                                background: rgba(220, 53, 69, 0.9); 
                                color: white; 
                                border: none; 
                                border-radius: 50%; 
                                width: 28px; 
                                height: 28px; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center; 
                                font-size: 0.8rem; 
                                cursor: pointer; 
                                transition: all 0.2s ease;
                                z-index: 2;
                            "
                            onmouseover="this.style.background='rgba(220, 53, 69, 1)'; this.style.transform='scale(1.1)'"
                            onmouseout="this.style.background='rgba(220, 53, 69, 0.9)'; this.style.transform='scale(1)'">
                        <i class="fas fa-times"></i>
                    </button>
                    
                    <div style="margin-bottom: 1rem;">
                        <i class="${fileInfo.icon}" style="
                            font-size: 2.5rem; 
                            color: ${fileInfo.color}; 
                            text-shadow: 0 2px 8px ${fileInfo.color}30;
                        "></i>
                    </div>
                    
                    <div style="font-size: 0.9rem;">
                        <div style="
                            font-weight: 600; 
                            margin-bottom: 0.5rem; 
                            word-break: break-word; 
                            color: #2d3748;
                            line-height: 1.3;
                        ">${file.name}</div>
                        
                        <div style="color: #6c757d; margin-bottom: 0.5rem;">
                            ${formatFileSize(file.size)}
                        </div>
                        
                        <div style="
                            background: ${fileInfo.color}20; 
                            color: ${fileInfo.color}; 
                            padding: 4px 8px; 
                            border-radius: 8px; 
                            font-size: 0.7rem; 
                            font-weight: 600; 
                            display: inline-block;
                        ">
                            ${fileInfo.type}
                        </div>
                    </div>
                    
                    ${!isMainFile ? `
                        <button type="button" 
                                onclick="setMainFile(${index})"
                                style="
                                    position: absolute;
                                    bottom: 8px;
                                    left: 50%;
                                    transform: translateX(-50%);
                                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                    color: white;
                                    border: none;
                                    padding: 4px 12px;
                                    border-radius: 12px;
                                    font-size: 0.7rem;
                                    font-weight: 600;
                                    cursor: pointer;
                                    transition: all 0.2s ease;
                                    opacity: 0.8;
                                "
                                onmouseover="this.style.opacity='1'; this.style.transform='translateX(-50%) translateY(-2px)'"
                                onmouseout="this.style.opacity='0.8'; this.style.transform='translateX(-50%) translateY(0)'">
                            <i class="fas fa-star me-1"></i>ตั้งเป็นหลัก
                        </button>
                    ` : ''}
                </div>
            `;

            return div;
        }

        function getFileInfo(file) {
            if (file.type === 'application/pdf') {
                return { icon: 'fas fa-file-pdf', color: '#dc3545', type: 'PDF' };
            } else if (file.type.includes('word')) {
                return { icon: 'fas fa-file-word', color: '#2b579a', type: 'Word' };
            } else if (file.type.includes('image')) {
                return { icon: 'fas fa-file-image', color: '#28a745', type: 'รูปภาพ' };
            } else {
                return { icon: 'fas fa-file', color: '#6c757d', type: 'ไฟล์' };
            }
        }

        function removeFile(index) {
            if (index >= 0 && index < selectedFiles.length) {
                const removedFile = selectedFiles.splice(index, 1)[0];
                updateFileDisplay();
                showAlert('info', 'ลบไฟล์แล้ว', `ลบไฟล์ "${removedFile.name}" แล้ว`, 2000);
                console.log(`🗑️ Removed file: ${removedFile.name}`);
            }
        }

        function setMainFile(index) {
            if (index >= 0 && index < selectedFiles.length) {
                const [mainFile] = selectedFiles.splice(index, 1);
                selectedFiles.unshift(mainFile);
                updateFileDisplay();
                showAlert('success', 'ตั้งไฟล์หลักแล้ว', `ตั้ง "${mainFile.name}" เป็นไฟล์หลักแล้ว`, 2000);
                console.log(`⭐ Set main file: ${mainFile.name}`);
            }
        }

        function clearAllFiles() {
            selectedFiles = [];
            document.getElementById('esv_ods_file').value = '';
            updateFileDisplay();
            showAlert('info', 'ลบไฟล์ทั้งหมดแล้ว', 'ลบไฟล์ทั้งหมดเรียบร้อยแล้ว', 2000);
            console.log('🗑️ Cleared all files');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Drag & Drop handlers
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
                FILE_CONFIG.allowedTypes.includes(file.type.toLowerCase())
            );

            if (files.length === 0) {
                showAlert('error', 'ไม่พบไฟล์ที่รองรับ', 'กรุณาเลือกไฟล์ PDF, DOC, DOCX หรือรูปภาพเท่านั้น');
                return;
            }

            const fileInput = document.getElementById('esv_ods_file');
            const dt = new DataTransfer();
            files.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;

            handleFileSelect(fileInput);
        }

        // ===================================================================
        // *** ID CARD VALIDATION SYSTEM ***
        // ===================================================================

        function setupIdCardValidation() {
            const idCardField = document.getElementById('esv_ods_id_card');
            if (!idCardField) return;

            // Restrict to numbers only
            idCardField.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 13) value = value.slice(0, 13);
                e.target.value = value;

                // Clear timer
                if (idCardValidationTimer) {
                    clearTimeout(idCardValidationTimer);
                }

                clearIdCardValidation();

                if (value.length === 13) {
                    idCardValidationTimer = setTimeout(() => {
                        validateIdCardInput(value);
                    }, 500);
                } else if (value.length > 0) {
                    showIdCardError('กรุณากรอกเลขบัตรประชาชนให้ครบ 13 หลัก');
                }
            });

            idCardField.addEventListener('blur', function (e) {
                const value = e.target.value.trim();
                if (value.length === 13) {
                    validateIdCardInput(value);
                }
            });
        }

        function validateIdCardInput(idCard) {
            if (isValidatingIdCard) return;

            isValidatingIdCard = true;
            showIdCardLoading(true);

            if (!validateThaiIdCard(idCard)) {
                showIdCardError('เลขบัตรประจำตัวประชาชนไม่ถูกต้อง');
                isValidatingIdCard = false;
                return;
            }

            checkIdCardDuplicate(idCard);
        }

        function validateThaiIdCard(idCard) {
            if (!idCard || !/^\d{13}$/.test(idCard)) return false;
            if (/^(\d)\1{12}$/.test(idCard)) return false;

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

        function checkIdCardDuplicate(idCard) {
            fetch('<?php echo site_url("esv_ods/check_id_card_duplicate"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_card=' + encodeURIComponent(idCard)
            })
                .then(response => response.json())
                .then(data => {
                    showIdCardLoading(false);

                    if (data.success) {
                        showIdCardSuccess('เลขบัตรประชาชนถูกต้อง');
                    } else {
                        showIdCardError(data.message || 'เกิดข้อผิดพลาดในการตรวจสอบ');
                    }
                })
                .catch(error => {
                    console.error('ID Card validation error:', error);
                    showIdCardLoading(false);
                    showIdCardError('เกิดข้อผิดพลาดในการตรวจสอบ');
                })
                .finally(() => {
                    isValidatingIdCard = false;
                });
        }

        function showIdCardLoading(show) {
            const loadingIcon = document.getElementById('id_card_loading');
            const successIcon = document.getElementById('id_card_success');
            const errorIcon = document.getElementById('id_card_error');

            if (loadingIcon) loadingIcon.style.display = show ? 'block' : 'none';
            if (successIcon) successIcon.style.display = 'none';
            if (errorIcon) errorIcon.style.display = 'none';
        }

        function showIdCardSuccess(message) {
            const idCardField = document.getElementById('esv_ods_id_card');
            const successIcon = document.getElementById('id_card_success');
            const errorIcon = document.getElementById('id_card_error');
            const loadingIcon = document.getElementById('id_card_loading');

            if (idCardField) {
                idCardField.classList.remove('is-invalid');
                idCardField.classList.add('is-valid');
            }

            if (successIcon) successIcon.style.display = 'block';
            if (errorIcon) errorIcon.style.display = 'none';
            if (loadingIcon) loadingIcon.style.display = 'none';

            const validFeedback = document.getElementById('esv_ods_id_card_valid');
            if (validFeedback) {
                validFeedback.textContent = message;
                validFeedback.style.display = 'block';
            }

            const invalidFeedback = document.getElementById('esv_ods_id_card_feedback');
            if (invalidFeedback) {
                invalidFeedback.style.display = 'none';
            }
        }

        function showIdCardError(message) {
            const idCardField = document.getElementById('esv_ods_id_card');
            const successIcon = document.getElementById('id_card_success');
            const errorIcon = document.getElementById('id_card_error');
            const loadingIcon = document.getElementById('id_card_loading');

            if (idCardField) {
                idCardField.classList.remove('is-valid');
                idCardField.classList.add('is-invalid');
            }

            if (successIcon) successIcon.style.display = 'none';
            if (errorIcon) errorIcon.style.display = 'block';
            if (loadingIcon) loadingIcon.style.display = 'none';

            const invalidFeedback = document.getElementById('esv_ods_id_card_feedback');
            if (invalidFeedback) {
                invalidFeedback.textContent = message;
                invalidFeedback.style.display = 'block';
            }

            const validFeedback = document.getElementById('esv_ods_id_card_valid');
            if (validFeedback) {
                validFeedback.style.display = 'none';
            }
        }

        function clearIdCardValidation() {
            const idCardField = document.getElementById('esv_ods_id_card');
            const successIcon = document.getElementById('id_card_success');
            const errorIcon = document.getElementById('id_card_error');
            const loadingIcon = document.getElementById('id_card_loading');

            if (idCardField) {
                idCardField.classList.remove('is-valid', 'is-invalid');
            }

            if (successIcon) successIcon.style.display = 'none';
            if (errorIcon) errorIcon.style.display = 'none';
            if (loadingIcon) loadingIcon.style.display = 'none';

            const validFeedback = document.getElementById('esv_ods_id_card_valid');
            if (validFeedback) validFeedback.style.display = 'none';

            const invalidFeedback = document.getElementById('esv_ods_id_card_feedback');
            if (invalidFeedback) invalidFeedback.style.display = 'none';
        }

        // ===================================================================
        // *** ADDRESS SYSTEM ***
        // ===================================================================

        function initializeEsvAddressSystem() {
            try {
                esvZipcodeField = document.getElementById('esv_zipcode_field');
                esvProvinceField = document.getElementById('esv_province_field');
                esvAmphoeField = document.getElementById('esv_amphoe_field');
                esvDistrictField = document.getElementById('esv_district_field');
                esvFullAddressField = document.getElementById('esv_full_address_field');
                esvAdditionalAddressField = document.getElementById('esv_additional_address_field');

                if (!esvProvinceField) {
                    console.error('❌ ESV Address elements not found');
                    return;
                }

                loadEsvAllProvinces();
                setupAddressEventListeners();

                console.log('✅ ESV Address system initialized successfully');

            } catch (error) {
                console.error('❌ Error initializing ESV address system:', error);
            }
        }

        function setupAddressEventListeners() {
            if (esvZipcodeField) {
                esvZipcodeField.addEventListener('keypress', function (e) {
                    if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                        e.preventDefault();
                    }
                });

                esvZipcodeField.addEventListener('input', function () {
                    const zipcode = this.value.trim();

                    if (zipcode.length === 0) {
                        resetEsvToProvinceSelection();
                    } else if (zipcode.length === 5 && /^\d{5}$/.test(zipcode)) {
                        searchEsvByZipcode(zipcode);
                    } else {
                        clearEsvDependentAddressFields();
                    }
                });
            }

            esvProvinceField.addEventListener('change', function () {
                const selectedProvinceCode = this.value;

                if (selectedProvinceCode) {
                    loadEsvAmphoesByProvince(selectedProvinceCode);
                } else {
                    esvAmphoeField.innerHTML = '<option value="">เลือกอำเภอ *</option>';
                    esvAmphoeField.disabled = true;
                    esvDistrictField.innerHTML = '<option value="">เลือกตำบล *</option>';
                    esvDistrictField.disabled = true;
                }

                updateEsvFullAddress();
            });

            esvAmphoeField.addEventListener('change', function () {
                const selectedAmphoeCode = this.value;

                if (selectedAmphoeCode) {
                    loadEsvDistrictsByAmphoe(selectedAmphoeCode);
                } else {
                    esvDistrictField.innerHTML = '<option value="">เลือกตำบล *</option>';
                    esvDistrictField.disabled = true;
                }

                updateEsvFullAddress();
            });

            esvDistrictField.addEventListener('change', function () {
                updateEsvFullAddress();
            });

            if (esvAdditionalAddressField) {
                esvAdditionalAddressField.addEventListener('input', function () {
                    clearTimeout(this.updateTimeout);
                    this.updateTimeout = setTimeout(() => {
                        updateEsvFullAddress();
                    }, 300);
                });
            }
        }

        async function loadEsvAllProvinces() {
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

            populateEsvProvinceDropdown(provinces);
        }

        async function searchEsvByZipcode(zipcode) {
            console.log('🔍 ESV Searching by zipcode:', zipcode);
            showEsvAddressLoading(true);

            try {
                const response = await fetch(`${ESV_API_BASE_URL}/address/${zipcode}`);
                const data = await response.json();

                if (data.status === 'success' && data.data.length > 0) {
                    const dataWithZipcode = data.data.map(item => ({
                        ...item,
                        zipcode: zipcode,
                        searched_zipcode: zipcode
                    }));

                    esvCurrentAddressData = dataWithZipcode;
                    populateEsvFieldsFromZipcode(dataWithZipcode);
                    updateEsvFullAddress();
                } else {
                    showEsvAddressError('ไม่พบข้อมูลสำหรับรหัสไปรษณีย์นี้');
                    resetEsvToProvinceSelection();
                }
            } catch (error) {
                console.error('❌ ESV Address API Error:', error);
                showEsvAddressError('เกิดข้อผิดพลาดในการค้นหาข้อมูล');
                resetEsvToProvinceSelection();
            } finally {
                showEsvAddressLoading(false);
            }
        }

        async function loadEsvAmphoesByProvince(provinceCode) {
            console.log('Loading ESV amphoes for province:', provinceCode);
            showEsvAddressLoading(true, 'province');

            try {
                const response = await fetch(`${ESV_API_BASE_URL}/amphoes/${provinceCode}`);
                const data = await response.json();

                if (data.status === 'success' && data.data && data.data.length > 0) {
                    const processedAmphoes = data.data.map(item => ({
                        code: item.amphoe_code || item.code || item.id,
                        name: item.amphoe_name || item.name || item.name_th || 'ไม่ระบุชื่อ',
                        name_en: item.amphoe_name_en || item.name_en || '',
                        province_code: item.province_code || provinceCode
                    }));

                    populateEsvAmphoeDropdown(processedAmphoes);
                    esvAmphoeField.disabled = false;
                } else {
                    esvAmphoeField.innerHTML = '<option value="">ไม่พบข้อมูลอำเภอ</option>';
                    esvAmphoeField.disabled = true;
                }
            } catch (error) {
                console.error('ESV Amphoe API Error:', error);
                esvAmphoeField.innerHTML = '<option value="">ไม่สามารถโหลดข้อมูลอำเภอได้</option>';
                esvAmphoeField.disabled = true;
            } finally {
                showEsvAddressLoading(false);
            }
        }

        async function loadEsvDistrictsByAmphoe(amphoeCode) {
            console.log('Loading ESV districts for amphoe:', amphoeCode);
            showEsvAddressLoading(true, 'amphoe');

            try {
                const response = await fetch(`${ESV_API_BASE_URL}/districts/${amphoeCode}`);
                const data = await response.json();

                if (data.status === 'success' && data.data && data.data.length > 0) {
                    const processedDistricts = data.data.map(item => ({
                        code: item.district_code || item.code || item.id,
                        name: item.district_name || item.name || item.name_th || 'ไม่ระบุชื่อ',
                        name_en: item.district_name_en || item.name_en || '',
                        amphoe_code: item.amphoe_code || amphoeCode
                    }));

                    populateEsvDistrictDropdown(processedDistricts);
                    esvDistrictField.disabled = false;
                } else {
                    esvDistrictField.innerHTML = '<option value="">ไม่พบข้อมูลตำบล</option>';
                    esvDistrictField.disabled = true;
                }
            } catch (error) {
                console.error('ESV District API Error:', error);
                esvDistrictField.innerHTML = '<option value="">ไม่สามารถโหลดข้อมูลตำบลได้</option>';
                esvDistrictField.disabled = true;
            } finally {
                showEsvAddressLoading(false);
            }
        }

        function populateEsvFieldsFromZipcode(data) {
            if (data.length === 0) return;

            const searchedZipcode = esvZipcodeField.value.trim();
            const relevantData = data.filter(item =>
                (item.zipcode || item.searched_zipcode) === searchedZipcode
            );

            if (relevantData.length === 0) {
                console.warn('⚠️ No ESV data matches the searched zipcode');
                return;
            }

            const firstItem = relevantData[0];

            setEsvProvinceValue(firstItem.province_code, firstItem.province_name);

            loadEsvAmphoesByProvince(firstItem.province_code).then(() => {
                setTimeout(() => {
                    if (esvAmphoeField) {
                        esvAmphoeField.value = firstItem.amphoe_code;

                        loadEsvDistrictsByAmphoe(firstItem.amphoe_code).then(() => {
                            console.log('✅ Districts loaded, user can select manually');
                            updateEsvFullAddress();
                        });
                    }
                }, 100);
            });

            esvCurrentAddressData = relevantData;
        }

        function populateEsvProvinceDropdown(provinces) {
            if (!esvProvinceField) return;

            esvProvinceField.innerHTML = '<option value="">เลือกจังหวัด *</option>';

            provinces.forEach(province => {
                if (province.code && province.name) {
                    esvProvinceField.innerHTML += `<option value="${province.code}">${province.name}</option>`;
                }
            });
        }

        function populateEsvAmphoeDropdown(amphoes) {
            if (!esvAmphoeField) return;

            esvAmphoeField.innerHTML = '<option value="">เลือกอำเภอ *</option>';

            amphoes.forEach(amphoe => {
                if (amphoe && amphoe.code && amphoe.name) {
                    esvAmphoeField.innerHTML += `<option value="${amphoe.code}">${amphoe.name}</option>`;
                }
            });
        }

        function populateEsvDistrictDropdown(districts) {
            if (!esvDistrictField) return;

            esvDistrictField.innerHTML = '<option value="">เลือกตำบล *</option>';

            districts.forEach(district => {
                if (district && district.code && district.name) {
                    esvDistrictField.innerHTML += `
                        <option value="${district.code}" 
                                data-amphoe-code="${district.amphoe_code}"
                                data-zipcode="${district.zipcode || ''}">
                            ${district.name}
                        </option>
                    `;
                }
            });
        }

        function setEsvProvinceValue(provinceCode, provinceName) {
            if (!esvProvinceField) return;

            const options = esvProvinceField.querySelectorAll('option');
            for (let option of options) {
                if (option.value === provinceCode || option.textContent === provinceName) {
                    esvProvinceField.value = option.value;
                    break;
                }
            }
        }

        function resetEsvToProvinceSelection() {
            if (!esvProvinceField) return;

            esvProvinceField.value = '';

            if (esvAmphoeField) {
                esvAmphoeField.innerHTML = '<option value="">เลือกอำเภอ *</option>';
                esvAmphoeField.disabled = true;
            }

            if (esvDistrictField) {
                esvDistrictField.innerHTML = '<option value="">เลือกตำบล *</option>';
                esvDistrictField.disabled = true;
            }

            document.querySelectorAll('.esv-address-error').forEach(el => el.remove());
            updateEsvFullAddress();
        }

        function clearEsvDependentAddressFields() {
            if (esvProvinceField) {
                esvProvinceField.value = '';
            }

            if (esvAmphoeField) {
                esvAmphoeField.innerHTML = '<option value="">เลือกอำเภอ *</option>';
                esvAmphoeField.disabled = true;
            }

            if (esvDistrictField) {
                esvDistrictField.innerHTML = '<option value="">เลือกตำบล *</option>';
                esvDistrictField.disabled = true;
            }

            document.querySelectorAll('.esv-address-error').forEach(el => el.remove());
            updateEsvFullAddress();
        }

        function updateEsvFullAddress() {
            if (!esvFullAddressField) return;

            const additionalAddress = esvAdditionalAddressField ? esvAdditionalAddressField.value.trim() : '';

            esvFullAddressField.value = additionalAddress;

            const currentZipcode = esvZipcodeField ? esvZipcodeField.value : '';
            let currentProvince = '';
            let currentAmphoe = '';
            let currentDistrict = '';

            if (esvProvinceField && esvProvinceField.selectedIndex > 0) {
                currentProvince = esvProvinceField.options[esvProvinceField.selectedIndex].text;
            }

            if (esvAmphoeField && esvAmphoeField.selectedIndex > 0) {
                currentAmphoe = esvAmphoeField.options[esvAmphoeField.selectedIndex].text;
            }

            if (esvDistrictField && esvDistrictField.selectedIndex > 0) {
                currentDistrict = esvDistrictField.options[esvDistrictField.selectedIndex].text;
            }

            updateEsvHiddenAddressFields(currentProvince, currentAmphoe, currentDistrict, currentZipcode);

            const addressPreview = document.getElementById('esv_address_preview');
            const addressPreviewText = document.getElementById('esv_address_preview_text');

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

        function updateEsvHiddenAddressFields(province, amphoe, district, zipcode) {
            const hiddenFields = [
                { name: 'guest_province', id: 'esv_guest_province_field', value: province },
                { name: 'guest_amphoe', id: 'esv_guest_amphoe_field', value: amphoe },
                { name: 'guest_district', id: 'esv_guest_district_field', value: district },
                { name: 'guest_zipcode', id: 'esv_guest_zipcode_field', value: zipcode }
            ];

            hiddenFields.forEach(field => {
                let hiddenField = document.getElementById(field.id);
                if (!hiddenField) {
                    hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = field.name;
                    hiddenField.id = field.id;
                    document.getElementById('esvForm').appendChild(hiddenField);
                }
                hiddenField.value = field.value || '';
            });
        }

        function showEsvAddressLoading(show, context = 'zipcode') {
            if (show) {
                document.querySelectorAll('.esv-address-loading-icon').forEach(el => el.remove());

                let targetField;
                switch (context) {
                    case 'province':
                        targetField = esvProvinceField;
                        break;
                    case 'amphoe':
                        targetField = esvAmphoeField;
                        break;
                    case 'district':
                        targetField = esvDistrictField;
                        break;
                    default:
                        targetField = esvZipcodeField;
                }

                if (targetField && targetField.parentNode) {
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-spinner fa-spin esv-address-loading-icon';
                    icon.style.cssText = 'position: absolute; right: 45px; top: 50%; transform: translateY(-50%); color: #667eea; z-index: 3;';
                    targetField.parentNode.appendChild(icon);
                }
            } else {
                document.querySelectorAll('.esv-address-loading-icon').forEach(el => el.remove());
            }
        }

        function showEsvAddressError(message) {
            document.querySelectorAll('.esv-address-error').forEach(el => el.remove());

            if (esvZipcodeField && esvZipcodeField.parentNode) {
                const errorDiv = document.createElement('small');
                errorDiv.className = 'esv-address-error text-danger form-text';
                errorDiv.textContent = message;
                esvZipcodeField.parentNode.appendChild(errorDiv);

                setTimeout(() => {
                    errorDiv.style.opacity = '0';
                    setTimeout(() => errorDiv.remove(), 300);
                }, 5000);
            }
        }

        // ===================================================================
        // *** VALIDATION SYSTEMS ***
        // ===================================================================

        function setupPhoneValidation() {
            const phoneInput = document.getElementById('esv_ods_phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function (e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 10) value = value.slice(0, 10);
                    e.target.value = value;
                });
            }
        }

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

                detailTextarea.addEventListener('input', function (e) {
                    const value = e.target.value.trim();
                    const length = value.length;

                    updateCounter();

                    e.target.classList.remove('is-valid', 'is-invalid');

                    if (length === 0) {
                        // No styling if empty
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

        function validateEsvAddressFields() {
            if (!isUserLoggedIn) {
                const additionalAddress = document.querySelector('#esv_additional_address_field')?.value?.trim();
                const province = document.querySelector('#esv_province_field')?.value?.trim();
                const amphoe = document.querySelector('#esv_amphoe_field')?.value?.trim();
                const district = document.querySelector('#esv_district_field')?.value?.trim();

                if (!additionalAddress || additionalAddress.length < 2) {
                    showAlert('warning', 'กรุณากรอกที่อยู่เพิ่มเติม', 'กรุณากรอกบ้านเลขที่ ซอย ถนน หรือรายละเอียดเพิ่มเติม (อย่างน้อย 2 ตัวอักษร)');
                    document.querySelector('#esv_additional_address_field')?.focus();
                    return false;
                }

                if (!province) {
                    showAlert('warning', 'กรุณาเลือกจังหวัด', 'กรุณาเลือกจังหวัดหรือกรอกรหัสไปรษณีย์เพื่อค้นหาข้อมูลที่อยู่');
                    document.querySelector('#esv_province_field')?.focus();
                    return false;
                }

                if (!amphoe) {
                    showAlert('warning', 'กรุณาเลือกอำเภอ', 'กรุณาเลือกอำเภอ');
                    document.querySelector('#esv_amphoe_field')?.focus();
                    return false;
                }

                if (!district) {
                    showAlert('warning', 'กรุณาเลือกตำบล', 'กรุณาเลือกตำบล');
                    document.querySelector('#esv_district_field')?.focus();
                    return false;
                }
            }

            return true;
        }

        function validateEsvFileRequirement() {
            console.log('🔍 === ESV FILE REQUIREMENT VALIDATION START ===');

            const FILE_REQUIRED = true;
            const MIN_FILES = 1;
            const MAX_FILES = 5;

            console.log('📁 Current selected files:', selectedFiles.length);

            if (FILE_REQUIRED && (!selectedFiles || selectedFiles.length === 0)) {
                console.warn('❌ File validation failed: No files but required');

                Swal.fire({
                    icon: 'warning',
                    title: '<span style="color: #667eea; font-weight: 600;">จำเป็นต้องแนบเอกสาร</span>',
                    html: `
                        <div style="padding: 0.8rem;">
                            <div style="text-align: center; margin-bottom: 1.2rem;">
                                <div style="
                                    width: 70px; 
                                    height: 70px; 
                                    margin: 0 auto; 
                                    background: #f3e5f5; 
                                    border-radius: 50%; 
                                    display: flex; 
                                    align-items: center; 
                                    justify-content: center;
                                    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
                                    border: 2px solid #ba68c8;
                                ">
                                    <i class="fas fa-file-upload" style="font-size: 2rem; color: #667eea;"></i>
                                </div>
                                <p style="
                                    color: #424242; 
                                    font-size: 1rem; 
                                    margin-top: 1rem;
                                    font-weight: 500;
                                    margin-bottom: 0;
                                ">
                                    กรุณาแนบเอกสารประกอบการยื่นเรื่อง
                                </p>
                            </div>
                            
                            <div style="
                                background: #f8f9fa; 
                                padding: 1rem; 
                                border-radius: 12px; 
                                margin-bottom: 1rem;
                                border: 1px solid #e3f2fd;
                                box-shadow: 0 2px 6px rgba(102, 126, 234, 0.06);
                            ">
                                <h6 style="
                                    color: #667eea; 
                                    font-weight: 600; 
                                    margin-bottom: 1rem;
                                    text-align: center;
                                    font-size: 0.95rem;
                                ">
                                    <i class="fas fa-clipboard-list me-2"></i>
                                    เอกสารที่ต้องแนบ
                                </h6>
                                
                                <div style="display: grid; grid-template-columns: 1fr; gap: 0.8rem;">
                                    <div style="
                                        background: white;
                                        padding: 0.8rem;
                                        border-radius: 8px;
                                        border: 1px solid #e3f2fd;
                                        box-shadow: 0 1px 3px rgba(102, 126, 234, 0.04);
                                    ">
                                        <div style="display: flex; align-items: flex-start;">
                                            <span style="
                                                width: 22px; 
                                                height: 22px; 
                                                background: #667eea; 
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
                                                <div style="color: #667eea; font-weight: 600; margin-bottom: 0.2rem; font-size: 0.85rem;">
                                                    เอกสารที่ต้องการยื่น
                                                </div>
                                                <div style="color: #666; font-size: 0.75rem; line-height: 1.3;">
                                                    ไฟล์เอกสารหลักที่ต้องการยื่นต่อหน่วยงาน
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div style="
                                        background: white;
                                        padding: 0.8rem;
                                        border-radius: 8px;
                                        border: 1px solid rgba(102, 126, 234, 0.3);
                                        box-shadow: 0 1px 3px rgba(102, 126, 234, 0.08);
                                    ">
                                        <div style="display: flex; align-items: flex-start;">
                                            <span style="
                                                width: 22px; 
                                                height: 22px; 
                                                background: #ba68c8; 
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
                                                <div style="color: #ba68c8; font-weight: 600; margin-bottom: 0.2rem; font-size: 0.85rem;">
                                                    เอกสารประกอบ
                                                </div>
                                                <div style="color: #666; font-size: 0.75rem; line-height: 1.3;">
                                                    เอกสารเพิ่มเติมที่เกี่ยวข้อง (ถ้ามี)
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
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
                    confirmButtonColor: '#667eea',
                    showCancelButton: true,
                    cancelButtonText: '<i class="fas fa-times me-2"></i>ยกเลิก',
                    cancelButtonColor: '#757575',
                    allowOutsideClick: false,
                    width: '600px',
                    padding: '1rem'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const fileInput = document.getElementById('esv_ods_file');
                        if (fileInput) {
                            fileInput.click();

                            setTimeout(() => {
                                const fileUploadArea = document.querySelector('.file-upload-wrapper, .upload-area');
                                if (fileUploadArea) {
                                    fileUploadArea.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });

                                    fileUploadArea.style.cssText += `
                                        border: 3px solid #667eea !important;
                                        background: rgba(102, 126, 234, 0.05) !important;
                                        transform: scale(1.02);
                                        transition: all 0.3s ease;
                                        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3) !important;
                                    `;

                                    setTimeout(() => {
                                        fileUploadArea.style.cssText = fileUploadArea.style.cssText
                                            .replace(/border: 3px solid #667eea !important;/g, '')
                                            .replace(/background: rgba\(102, 126, 234, 0\.05\) !important;/g, '')
                                            .replace(/transform: scale\(1\.02\);/g, '')
                                            .replace(/transition: all 0\.3s ease;/g, '')
                                            .replace(/box-shadow: 0 8px 25px rgba\(102, 126, 234, 0\.3\) !important;/g, '');
                                    }, 3000);
                                }
                            }, 500);
                        }
                    }
                });

                return false;
            }

            if (selectedFiles.length < MIN_FILES) {
                Swal.fire({
                    icon: 'warning',
                    title: '<span style="color: #667eea;">จำนวนไฟล์ไม่เพียงพอ</span>',
                    html: `
                        <div style="text-align: center; padding: 1rem;">
                            <div style="
                                width: 60px; 
                                height: 60px; 
                                margin: 0 auto 1rem; 
                                background: #f3e5f5; 
                                border-radius: 50%; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center;
                                border: 2px solid #ba68c8;
                            ">
                                <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: #667eea;"></i>
                            </div>
                            <p style="color: #424242; font-size: 1.1rem; margin: 0; line-height: 1.4;">
                                กรุณาแนบไฟล์อย่างน้อย <strong style="color: #667eea;">${MIN_FILES}</strong> ไฟล์
                            </p>
                        </div>
                    `,
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#667eea',
                    width: '350px'
                });
                return false;
            }

            if (selectedFiles.length > MAX_FILES) {
                Swal.fire({
                    icon: 'warning',
                    title: '<span style="color: #667eea;">จำนวนไฟล์เกินกำหนด</span>',
                    html: `
                        <div style="text-align: center; padding: 1rem;">
                            <div style="
                                width: 60px; 
                                height: 60px; 
                                margin: 0 auto 1rem; 
                                background: #f3e5f5; 
                                border-radius: 50%; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center;
                                border: 2px solid #ba68c8;
                            ">
                                <i class="fas fa-times-circle" style="font-size: 1.5rem; color: #667eea;"></i>
                            </div>
                            <p style="color: #424242; font-size: 1.1rem; margin: 0; line-height: 1.4;">
                                สามารถแนบไฟล์ได้สูงสุด <strong style="color: #667eea;">${MAX_FILES}</strong> ไฟล์เท่านั้น
                            </p>
                        </div>
                    `,
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#667eea',
                    width: '350px'
                });
                return false;
            }

            console.log('✅ ESV File requirement validation passed');
            return true;
        }

        // ===================================================================
        // *** USER INTERFACE FUNCTIONS ***
        // ===================================================================

        function updateFormFieldsBasedOnLoginStatus() {
            if (!isUserLoggedIn || !userInfo) return;

            const nameField = document.querySelector('input[name="esv_ods_by"]');
            const phoneField = document.querySelector('input[name="esv_ods_phone"]');
            const emailField = document.querySelector('input[name="esv_ods_email"]');
            const personalSection = document.getElementById('personal_info_section');

            if (personalSection) {
                personalSection.style.display = 'none';
            }

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

            console.log('🔧 Updating form fields based on login status:', isUserLoggedIn);
            showLoggedInUserInfo();
        }

        function showLoggedInUserInfo() {
            if (!isUserLoggedIn || !userInfo) return;

            try {
                let userName = userInfo.name || 'ผู้ใช้';
                let userEmail = userInfo.email || 'ไม่ระบุ';
                let userPhone = userInfo.phone || 'ไม่ระบุ';

                const userInfoHTML = `
                    <div id="logged-in-user-info" class="alert alert-success" style="
                        border-radius: 20px; 
                        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); 
                        border: 1px solid rgba(102, 126, 234, 0.3);
                        margin-bottom: 2rem;
                        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25);
                        backdrop-filter: blur(10px);
                        padding: 2.5rem;
                        min-height: 180px;
                    ">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div style="
                                    width: 80px; 
                                    height: 80px; 
                                    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); 
                                    border-radius: 50%; 
                                    display: flex; 
                                    align-items: center; 
                                    justify-content: center;
                                    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.35);
                                ">
                                    <i class="fas fa-user-check" style="
                                        font-size: 2.2rem; 
                                        color: #667eea; 
                                        text-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
                                    "></i>
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="mb-3" style="
                                    color: #2d3748; 
                                    font-weight: 700; 
                                    font-size: 1.4rem;
                                ">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745;"></i>
                                    ใช้ข้อมูลจากบัญชีของคุณ
                                </h4>
                                
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="mb-2" style="
                                            background: rgba(255, 255, 255, 0.7); 
                                            padding: 0.8rem 1rem; 
                                            border-radius: 12px; 
                                            border-left: 4px solid #667eea;
                                            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
                                        ">
                                            <div style="
                                                color: #667eea; 
                                                font-size: 0.8rem; 
                                                font-weight: 600; 
                                                text-transform: uppercase; 
                                                margin-bottom: 0.3rem;
                                            ">
                                                <i class="fas fa-user me-1"></i>ชื่อ-นามสกุล
                                            </div>
                                            <div style="
                                                color: #2d3748; 
                                                font-size: 1rem; 
                                                font-weight: 600;
                                            ">${userName}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-2" style="
                                            background: rgba(255, 255, 255, 0.7); 
                                            padding: 0.8rem 1rem; 
                                            border-radius: 12px; 
                                            border-left: 4px solid #17a2b8;
                                            box-shadow: 0 2px 8px rgba(23, 162, 184, 0.1);
                                        ">
                                            <div style="
                                                color: #17a2b8; 
                                                font-size: 0.8rem; 
                                                font-weight: 600; 
                                                text-transform: uppercase; 
                                                margin-bottom: 0.3rem;
                                            ">
                                                <i class="fas fa-envelope me-1"></i>อีเมล
                                            </div>
                                            <div style="
                                                color: #2d3748; 
                                                font-size: 1rem; 
                                                font-weight: 600; 
                                                word-break: break-word;
                                            ">${userEmail}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-2" style="
                                            background: rgba(255, 255, 255, 0.7); 
                                            padding: 0.8rem 1rem; 
                                            border-radius: 12px; 
                                            border-left: 4px solid #28a745;
                                            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.1);
                                        ">
                                            <div style="
                                                color: #28a745; 
                                                font-size: 0.8rem; 
                                                font-weight: 600; 
                                                text-transform: uppercase; 
                                                margin-bottom: 0.3rem;
                                            ">
                                                <i class="fas fa-phone me-1"></i>เบอร์โทร
                                            </div>
                                            <div style="
                                                color: #2d3748; 
                                                font-size: 1rem; 
                                                font-weight: 600;
                                            ">${userPhone}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-3" style="
                                    background: rgba(102, 126, 234, 0.08); 
                                    padding: 1rem 1.2rem; 
                                    border-radius: 15px; 
                                    border: 1px solid rgba(102, 126, 234, 0.2);
                                ">
                                    <div style="
                                        color: #667eea; 
                                        font-size: 1rem; 
                                        font-weight: 600; 
                                        display: flex; 
                                        align-items: center;
                                    ">
                                        <i class="fas fa-shield-alt me-2" style="font-size: 1.1rem;"></i> 
                                        ข้อมูลจากบัญชีของคุณจะถูกใช้โดยอัตโนมัติ
                                    </div>
                                    <div style="
                                        color: #4a5568; 
                                        font-size: 0.9rem; 
                                        margin-top: 0.5rem;
                                    ">
                                        คุณสามารถยื่นเอกสารได้ทันที ไม่ต้องกรอกข้อมูลส่วนตัวซ้ำ และสามารถติดตามสถานะได้ในหน้าบัญชีของคุณ
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                const trackButton = document.querySelector('.track-status-btn').parentElement;
                trackButton.insertAdjacentHTML('afterend', userInfoHTML);

            } catch (error) {
                console.error('❌ Error showing user info:', error);
            }
        }

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

        // ===================================================================
        // *** FORM SUBMISSION SYSTEM ***
        // ===================================================================

        function setupFormEvents() {
            document.getElementById('esv_ods_department_id').addEventListener('change', function () {
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

            document.getElementById('esv_ods_category_id').addEventListener('change', function () {
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

        function handleEsvSubmit(event) {
            event.preventDefault();

            if (formSubmitting) return false;

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
                const form = document.getElementById('esvForm');

                // Validate detail field
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

                // Validate file requirement
                if (!validateEsvFileRequirement()) {
                    return;
                }

                // Validate total file size
                const totalSize = calculateTotalSize();
                if (totalSize > FILE_CONFIG.maxTotalSize) {
                    showAlert('warning', 'ขนาดไฟล์รวมเกินที่กำหนด', `ขนาดไฟล์รวม ${formatFileSize(totalSize)} เกินจากที่อนุญาต ${formatFileSize(FILE_CONFIG.maxTotalSize)}`);
                    return;
                }

                // Validate ID card for guest users
                if (!isUserLoggedIn) {
                    const idCardField = document.getElementById('esv_ods_id_card');
                    if (idCardField) {
                        const idCardValue = idCardField.value.trim();
                        if (idCardValue.length !== 13) {
                            showAlert('warning', 'เลขบัตรประชาชนไม่ครบ', 'กรุณากรอกเลขบัตรประชาชนให้ครบ 13 หลัก');
                            idCardField.focus();
                            return;
                        }

                        if (!/^\d{13}$/.test(idCardValue)) {
                            showAlert('error', 'เลขบัตรประชาชนไม่ถูกต้อง', 'กรุณากรอกเลขบัตรประจำตัวประชาชน 13 หลัก (ตัวเลขเท่านั้น)');
                            idCardField.focus();
                            return;
                        }

                        if (!validateThaiIdCard(idCardValue)) {
                            showAlert('error', 'เลขบัตรประชาชนไม่ถูกต้อง', 'เลขบัตรประจำตัวประชาชนที่กรอกไม่ถูกต้องตามมาตรฐาน กรุณาตรวจสอบอีกครั้ง');
                            idCardField.focus();
                            return;
                        }
                    }

                    // Validate address
                    if (!validateEsvAddressFields()) {
                        return;
                    }
                }

                console.log('🔄 Starting form validation...');

                // Basic form validation
                const allRequiredFields = form.querySelectorAll('[required]');
                let missingFields = [];

                allRequiredFields.forEach(field => {
                    const isVisible = field.offsetParent !== null;
                    const hasValue = field.value && field.value.trim() !== '';

                    console.log(`Field: ${field.name}, Required: ${field.required}, Visible: ${isVisible}, HasValue: ${hasValue}, Value: "${field.value}"`);

                    if (isVisible && field.required && !hasValue) {
                        missingFields.push(field.name);
                    }
                });

                if (missingFields.length > 0) {
                    console.error('Missing required fields:', missingFields);
                    const fieldNameMapping = {
                        'document_type': 'ประเภทเอกสาร',
                        'esv_ods_department_id': 'แผนกปลายทาง',
                        'esv_ods_category_id': 'หมวดหมู่เอกสาร',
                        'esv_ods_topic': 'เรื่องที่ต้องการยื่นเอกสาร',
                        'esv_ods_by': 'ชื่อ-นามสกุล',
                        'esv_ods_phone': 'เบอร์โทรศัพท์',
                        'esv_ods_email': 'อีเมล',
                        'esv_ods_id_card': 'เลขบัตรประชาชน',
                        'esv_additional_address': 'ที่อยู่เพิ่มเติม',
                        'esv_province': 'จังหวัด',
                        'esv_amphoe': 'อำเภอ',
                        'esv_district': 'ตำบล',
                        'esv_ods_detail': 'รายละเอียด'
                    };

                    const fieldNames = missingFields.map(field => fieldNameMapping[field] || field);
                    showAlert('warning', 'กรุณากรอกข้อมูลให้ครบถ้วน', `มีข้อมูลที่จำเป็นยังไม่ได้กรอก: ${fieldNames.join(', ')}`);

                    const firstMissingField = document.querySelector(`[name="${missingFields[0]}"]`);
                    if (firstMissingField) {
                        firstMissingField.focus();
                    }
                    return;
                }

                console.log('✅ All validation checks passed');

                if (formSubmitting) return;
                formSubmitting = true;

                const submitBtn = document.getElementById('submitEsvBtn');
                const originalContent = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังส่ง...';

                console.log('📝 ESV form submitted');

                // Check reCAPTCHA requirements
                console.log('🔍 User login status for ESV form:');
                console.log('- isUserLoggedIn:', isUserLoggedIn);
                console.log('- userInfo exists:', !!userInfo);

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

                if (shouldUseRecaptcha) {
                    console.log('🛡️ Executing reCAPTCHA for user type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');

                    grecaptcha.ready(function () {
                        console.log('🔧 grecaptcha.ready() called for ESV submission');

                        grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                            action: 'esv_submit'
                        }).then(function (token) {
                            console.log('✅ reCAPTCHA token received for user:', token.substring(0, 50) + '...');
                            console.log('📏 Token length:', token.length);
                            console.log('👤 User type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');

                            performEsvSubmitWithRecaptcha(form, token, submitBtn, originalContent);
                        }).catch(function (error) {
                            console.error('❌ reCAPTCHA execution failed:', error);
                            console.log('🔄 Falling back to submit without reCAPTCHA');
                            performEsvSubmitWithoutRecaptcha(form, submitBtn, originalContent);
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

                    performEsvSubmitWithoutRecaptcha(form, submitBtn, originalContent);
                }

            } catch (error) {
                console.error('Submit form error:', error);
                showAlert('error', 'เกิดข้อผิดพลาดในระบบ', 'ไม่สามารถส่งฟอร์มได้ กรุณาลองใหม่');
                formSubmitting = false;
            }
        }

        function buildEsvFullAddress() {
            let fullAddress = '';

            console.log('🏠 === ESV ADDRESS BUILDING DEBUG ===');
            console.log('isUserLoggedIn:', isUserLoggedIn);
            console.log('userInfo:', userInfo);

            if (isUserLoggedIn && userInfo) {
                console.log('🏠 Processing address for LOGGED user');
                console.log('userInfo.address:', userInfo.address);

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

                if (!fullAddress || fullAddress.trim() === '') {
                    console.log('🔄 Trying fallback address methods...');

                    if (userInfo.full_address && userInfo.full_address.trim() !== '') {
                        fullAddress = userInfo.full_address;
                        console.log('📍 Using full_address:', fullAddress);
                    } else if (userInfo.mp_address && userInfo.mp_address.trim() !== '') {
                        fullAddress = userInfo.mp_address;
                        console.log('📍 Using mp_address:', fullAddress);
                    } else if (typeof userInfo.address === 'string' && userInfo.address.trim() !== '') {
                        fullAddress = userInfo.address;
                        console.log('📍 Using address string:', fullAddress);
                    }
                }

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

                const additionalAddress = document.querySelector('input[name="esv_additional_address"]')?.value?.trim();
                const province = document.getElementById('esv_guest_province_field')?.value || '';
                const amphoe = document.getElementById('esv_guest_amphoe_field')?.value || '';
                const district = document.getElementById('esv_guest_district_field')?.value || '';


                console.log('📍 Guest address components:', {
                    additionalAddress, province, amphoe, district
                });

                if (additionalAddress) {
                    const addressParts = [
                        additionalAddress,
                        district ? `ตำบล${district}` : '',
                        amphoe ? `อำเภอ${amphoe}` : '',
                        province ? `จังหวัด${province}` : ''
                    ].filter(part => part.trim() !== '');

                    fullAddress = addressParts.join(' ');
                }
            }

            console.log('🏠 Final fullAddress result:', fullAddress);
            console.log('🏠 Address length:', fullAddress.length);

            return fullAddress;
        }

        function validateAndSetEsvAddress(formData) {
            console.log('🔍 === ESV ADDRESS VALIDATION START ===');

            let fullAddress = buildEsvFullAddress();

            if (!fullAddress || fullAddress.trim() === '' || fullAddress.length < 5) {
                console.warn('⚠️ Address validation failed, trying emergency fallback...');

                const addressDisplay = document.querySelector('.user-address, .address-display, [data-address]');
                if (addressDisplay && addressDisplay.textContent.trim() !== '') {
                    fullAddress = addressDisplay.textContent.trim();
                    console.log('🆘 Emergency address from display:', fullAddress);
                }

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

                if (!fullAddress || fullAddress.trim() === '') {
                    fullAddress = `ที่อยู่ไม่ระบุ - User ${isUserLoggedIn ? userInfo?.mp_id || 'Unknown' : 'Guest'} - ${new Date().toISOString()}`;
                    console.warn('🆘 Using emergency default address:', fullAddress);
                }
            }

            if (fullAddress.length < 10) {
                console.warn('⚠️ Address too short, padding with additional info...');
                if (isUserLoggedIn && userInfo) {
                    fullAddress += ` (User: ${userInfo.mp_name || userInfo.mp_id || 'Unknown'})`;
                }
            }

            formData.set('esv_ods_address', fullAddress);
            console.log('✅ Final address set to FormData:', fullAddress);
            console.log('🔍 === ESV ADDRESS VALIDATION END ===');

            return fullAddress;
        }

        function performEsvSubmitWithRecaptcha(form, recaptchaToken, submitBtn, originalContent) {
            console.log('📤 Submitting ESV with reCAPTCHA token...');
            console.log('👤 User type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');

            try {
                const formData = new FormData();
                const formElements = form.elements;

                // Add form data
                for (let element of formElements) {
                    if (element.type === 'file' || element.type === 'button' || element.type === 'submit') continue;
                    if (element.name && element.value !== '' && element.value !== null) {
                        formData.append(element.name, element.value);
                        console.log(`Added to FormData: ${element.name} = ${element.value}`);
                    }
                }

                // Add document type
                const documentType = document.querySelector('input[name="document_type"]:checked');
                if (documentType) {
                    formData.append('document_type', documentType.value);
                    console.log(`Document type: ${documentType.value}`);
                }

                // Process address
                const finalAddress = validateAndSetEsvAddress(formData);
                console.log('Address processed:', finalAddress);

                // Add address parts
                if (isUserLoggedIn && userInfo && userInfo.address) {
                    const addr = userInfo.address;
                    formData.set('esv_province', addr.province || '');
                    formData.set('esv_amphoe', addr.amphoe || '');
                    formData.set('esv_district', addr.district || '');
                    formData.set('esv_zipcode', addr.zipcode || '');
                } else if (!isUserLoggedIn) {
                    const province = document.getElementById('esv_guest_province_field')?.value || '';
                    const amphoe = document.getElementById('esv_guest_amphoe_field')?.value || '';
                    const district = document.getElementById('esv_guest_district_field')?.value || '';


                    formData.set('esv_province', province);
                    formData.set('esv_amphoe', amphoe);
                    formData.set('esv_district', district);
                }

                // Add files
                if (selectedFiles.length > 0) {
                    selectedFiles.forEach((file, index) => {
                        formData.append('esv_ods_file[]', file, file.name);
                        console.log(`File ${index + 1} attached: ${file.name} (${formatFileSize(file.size)})`);
                    });
                    console.log(`Total files: ${selectedFiles.length}`);
                }

                // Add reCAPTCHA and metadata
                formData.append('user_agent_info', navigator.userAgent);
                formData.append('is_anonymous', isUserLoggedIn ? '0' : '1');
                formData.append('has_recaptcha', '1');
                formData.append('submission_source', 'esv_form_with_recaptcha_v2');
                formData.append('g-recaptcha-response', recaptchaToken);
                formData.append('recaptcha_action', 'esv_submit');

                console.log('📦 FormData contents for ESV with reCAPTCHA (Enhanced):');
                console.log('- Submission mode: WITH reCAPTCHA (ENHANCED VERSION)');
                console.log('- User type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');
                console.log('- Address validation: ENHANCED');

                // Send data
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
                                        message: 'ยื่นเอกสารสำเร็จ',
                                        tracking_code: 'ESV' + Date.now().toString().slice(-6)
                                    };
                                }
                            });
                        }
                    })
                    .then(jsonResponse => {
                        console.log('✅ JSON Response processed successfully:', jsonResponse.success);
                        handleEsvSubmitResponse(jsonResponse);
                    })
                    .catch(error => {
                        console.error('❌ Fetch error during submission with reCAPTCHA:', error);
                        handleEsvSubmitError(error);
                    })
                    .finally(() => {
                        console.log('🔄 Restoring submit button state');
                        restoreEsvSubmitButton(submitBtn, originalContent);
                    });

            } catch (error) {
                console.error('💥 Critical error in performEsvSubmitWithRecaptcha:', error);
                handleEsvSubmitError(error);
                restoreEsvSubmitButton(submitBtn, originalContent);
            }
        }

        function performEsvSubmitWithoutRecaptcha(form, submitBtn, originalContent) {
            console.log('📤 Submitting ESV without reCAPTCHA token...');
            console.log('👤 User type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');

            try {
                const formData = new FormData();
                const formElements = form.elements;

                // Add form data
                for (let element of formElements) {
                    if (element.type === 'file' || element.type === 'button' || element.type === 'submit') continue;
                    if (element.name && element.value !== '' && element.value !== null) {
                        formData.append(element.name, element.value);
                        console.log(`Added to FormData: ${element.name} = ${element.value}`);
                    }
                }

                // Add document type
                const documentType = document.querySelector('input[name="document_type"]:checked');
                if (documentType) {
                    formData.append('document_type', documentType.value);
                    console.log(`Document type: ${documentType.value}`);
                }

                // Process address
                const finalAddress = validateAndSetEsvAddress(formData);

                // Add address parts
                if (isUserLoggedIn && userInfo && userInfo.address) {
                    const addr = userInfo.address;
                    formData.set('esv_province', addr.province || '');
                    formData.set('esv_amphoe', addr.amphoe || '');
                    formData.set('esv_district', addr.district || '');
                    formData.set('esv_zipcode', addr.zipcode || '');
                } else if (!isUserLoggedIn) {
                    const province = document.getElementById('esv_guest_province_field')?.value || '';
                    const amphoe = document.getElementById('esv_guest_amphoe_field')?.value || '';
                    const district = document.getElementById('esv_guest_district_field')?.value || '';


                    formData.set('esv_province', province);
                    formData.set('esv_amphoe', amphoe);
                    formData.set('esv_district', district);
                }

                // Add debug info
                formData.append('address_debug_info', JSON.stringify({
                    user_type: isUserLoggedIn ? 'logged' : 'guest',
                    address_length: finalAddress.length,
                    userInfo_exists: !!userInfo,
                    userInfo_address_exists: !!(userInfo && userInfo.address),
                    timestamp: new Date().toISOString()
                }));

                // Add files
                if (selectedFiles.length > 0) {
                    selectedFiles.forEach((file, index) => {
                        formData.append('esv_ods_file[]', file, file.name);
                        console.log(`File ${index + 1} attached: ${file.name} (${formatFileSize(file.size)})`);
                    });
                    console.log(`Total files: ${selectedFiles.length}, Total size: ${formatFileSize(calculateTotalSize())}`);
                }

                // Add metadata for normal submit
                formData.append('ajax_request', '1');
                formData.append('client_timestamp', new Date().toISOString());
                formData.append('user_agent_info', navigator.userAgent);
                formData.append('is_anonymous', isUserLoggedIn ? '0' : '1');
                formData.append('has_recaptcha', '0');
                formData.append('submission_source', 'esv_form_no_recaptcha_fallback');

                console.log('📦 FormData contents for ESV without reCAPTCHA:');
                console.log('- Submission mode: WITHOUT reCAPTCHA (FALLBACK)');
                console.log('- User type:', isUserLoggedIn ? 'LOGGED_USER' : 'GUEST_USER');
                console.log('- Skip reason: recaptcha_unavailable');

                // Send data
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
                                    console.log('🔄 Using fallback success response');
                                    return {
                                        success: true,
                                        message: 'ยื่นเอกสารสำเร็จ',
                                        tracking_code: 'ESV' + Date.now().toString().slice(-6),
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

                        handleEsvSubmitResponse(jsonResponse);
                    })
                    .catch(error => {
                        console.error('❌ Fetch error during submission without reCAPTCHA:', error);
                        handleEsvSubmitError(error);
                    })
                    .finally(() => {
                        console.log('🔄 Restoring submit button state');
                        restoreEsvSubmitButton(submitBtn, originalContent);
                    });

            } catch (error) {
                console.error('💥 Critical error in performEsvSubmitWithoutRecaptcha:', error);
                console.error('Stack trace:', error.stack);
                handleEsvSubmitError(error);
                restoreEsvSubmitButton(submitBtn, originalContent);
            }
        }

        function handleEsvSubmitResponse(jsonResponse) {
            if (jsonResponse.success) {
                const trackingCode = jsonResponse.tracking_code || jsonResponse.reference_id || 'ESV' + Date.now().toString().slice(-6);

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
                            window.location.href = '<?php echo site_url("esv_ods/my_documents"); ?>';
                        } else {
                            window.location.href = `<?php echo site_url("esv_ods/track"); ?>?code=${trackingCode}`;
                        }
                    } else {
                        resetForm();
                    }
                });
            } else {
                if (jsonResponse.error_type === 'recaptcha_failed') {
                    showAlert('error', 'การยืนยันความปลอดภัยไม่ผ่าน', 'กรุณาลองใหม่อีกครั้ง');
                } else if (jsonResponse.error_type === 'recaptcha_missing') {
                    showAlert('error', 'ไม่พบข้อมูลการยืนยันความปลอดภัย', 'กรุณาลองใหม่อีกครั้ง');
                } else {
                    throw new Error(jsonResponse.message || 'เกิดข้อผิดพลาด');
                }
            }
        }

        function handleEsvSubmitError(error) {
            console.error('ESV submit error:', error);

            let errorMessage = 'เกิดข้อผิดพลาดในการส่งข้อมูล';

            if (error.message.includes('HTML instead of JSON')) {
                errorMessage = 'เกิดข้อผิดพลาดในเซิร์ฟเวอร์ กรุณาตรวจสอบ log หรือติดต่อผู้ดูแลระบบ';
            } else if (error.message.includes('HTTP error')) {
                errorMessage = 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์ กรุณาลองใหม่อีกครั้ง';
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage = 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต';
            }

            showAlert('error', 'เกิดข้อผิดพลาด', errorMessage);
        }

        function restoreEsvSubmitButton(submitBtn, originalContent) {
            setTimeout(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                }
                formSubmitting = false;
            }, 100);
        }

        function resetForm() {
            const form = document.getElementById('esvForm');
            if (form) form.reset();
            selectedFiles = [];
            updateFileDisplay();
            updateFormFieldsBasedOnLoginStatus();

            const firstRadio = document.querySelector('input[name="document_type"]');
            if (firstRadio) firstRadio.checked = true;

            const otherDepartment = document.getElementById('other_department');
            const otherCategory = document.getElementById('other_category');
            const categoryInfo = document.getElementById('category_info');

            if (otherDepartment) otherDepartment.style.display = 'none';
            if (otherCategory) otherCategory.style.display = 'none';
            if (categoryInfo) categoryInfo.style.display = 'none';

            clearIdCardValidation();
            resetEsvToProvinceSelection();
        }

        // ===================================================================
        // *** MODAL FUNCTIONS ***
        // ===================================================================

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
                window.location.href = '<?php echo site_url("user"); ?>?redirect=' + encodeURIComponent(currentUrl);
            });
        }

        function redirectToTrackStatus() {
            if (isUserLoggedIn) {
                window.location.href = '<?php echo site_url("esv_ods/my_documents"); ?>';
            } else {
                window.location.href = '<?php echo site_url("esv_ods/track"); ?>';
            }
        }

        // ===================================================================
        // *** AJAX FUNCTIONS ***
        // ===================================================================

        function loadAllCategories() {
            const categorySelect = document.getElementById('esv_ods_category_id');

            fetch('<?php echo site_url("esv_ods/get_all_categories"); ?>', {
                method: 'POST'
            })
                .then(response => response.json())
                .then(data => {
                    console.log('All categories loaded:', data);

                    categorySelect.innerHTML = '<option value="">เลือกหมวดหมู่เอกสาร</option>';

                    if (data && data.length > 0) {
                        data.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.esv_category_id;
                            option.setAttribute('data-department', category.esv_category_department_id || '');
                            option.setAttribute('data-fee', category.esv_category_fee || 0);
                            option.setAttribute('data-days', category.esv_category_process_days || 0);

                            let optionText = category.esv_category_name;
                            if (category.esv_category_fee > 0) {
                                optionText += ` (ค่าธรรมเนียม ${parseInt(category.esv_category_fee).toLocaleString()} บาท)`;
                            }

                            option.textContent = optionText;
                            categorySelect.appendChild(option);
                        });
                    }

                    const otherOption = document.createElement('option');
                    otherOption.value = 'other';
                    otherOption.textContent = 'อื่นๆ (ระบุ)';
                    categorySelect.appendChild(otherOption);
                })
                .catch(error => {
                    console.error('Error loading all categories:', error);
                    categorySelect.innerHTML = '<option value="">เกิดข้อผิดพลาดในการโหลด</option>';
                });
        }

        function loadCategoriesByDepartment(departmentId) {
            const categorySelect = document.getElementById('esv_ods_category_id');

            categorySelect.innerHTML = '<option value="">เลือกหมวดหมู่เอกสาร</option>';

            if (departmentId && departmentId !== 'other') {
                categorySelect.innerHTML = '<option value="">กำลังโหลดหมวดหมู่...</option>';

                fetch('<?php echo site_url("esv_ods/get_categories_by_department"); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'department_id=' + departmentId
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Categories by department loaded:', data);

                        categorySelect.innerHTML = '<option value="">เลือกหมวดหมู่เอกสาร</option>';

                        if (data && data.length > 0) {
                            data.forEach(category => {
                                const option = document.createElement('option');
                                option.value = category.esv_category_id;
                                option.setAttribute('data-department', category.esv_category_department_id);
                                option.setAttribute('data-fee', category.esv_category_fee || 0);
                                option.setAttribute('data-days', category.esv_category_process_days || 0);

                                let optionText = category.esv_category_name;
                                if (category.esv_category_fee > 0) {
                                    optionText += ` (ค่าธรรมเนียม ${parseInt(category.esv_category_fee).toLocaleString()} บาท)`;
                                }
                                if (category.esv_category_process_days > 0) {
                                    optionText += ` - ${category.esv_category_process_days} วันทำการ`;
                                }

                                option.textContent = optionText;
                                categorySelect.appendChild(option);
                            });
                        } else {
                            categorySelect.innerHTML += '<option value="" disabled>ไม่พบหมวดหมู่สำหรับแผนกนี้</option>';
                        }

                        const otherOption = document.createElement('option');
                        otherOption.value = 'other';
                        otherOption.textContent = 'อื่นๆ (ระบุ)';
                        categorySelect.appendChild(otherOption);
                    })
                    .catch(error => {
                        console.error('Error loading categories:', error);
                        categorySelect.innerHTML = '<option value="">เกิดข้อผิดพลาดในการโหลดหมวดหมู่</option>';
                    });
            } else {
                loadAllCategories();
            }
        }

        function showCategoryInfo(categoryId) {
            const categoryInfo = document.getElementById('category_info');

            if (categoryId && categoryId !== 'other') {
                categoryInfo.style.display = 'block';
                categoryInfo.innerHTML = '<i class="fas fa-info-circle"></i> กำลังโหลดข้อมูลหมวดหมู่...';

                fetch('<?php echo site_url("esv_ods/get_category_info"); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'category_id=' + categoryId
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data) {
                            const category = data.data;
                            let infoHtml = `<strong>${category.esv_category_name}</strong><br>`;

                            if (category.esv_category_description) {
                                infoHtml += `${category.esv_category_description}<br>`;
                            }

                            if (category.esv_category_fee > 0) {
                                infoHtml += `<span class="text-info">ค่าธรรมเนียม: ${parseFloat(category.esv_category_fee).toLocaleString()} บาท</span><br>`;
                            }

                            if (category.esv_category_process_days > 0) {
                                infoHtml += `<span class="text-warning">ระยะเวลาดำเนินการ: ${category.esv_category_process_days} วันทำการ</span>`;
                            }

                            categoryInfo.innerHTML = infoHtml;
                        } else {
                            categoryInfo.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        categoryInfo.style.display = 'none';
                    });
            } else {
                categoryInfo.style.display = 'none';
            }
        }

        // ===================================================================
        // *** UTILITY FUNCTIONS ***
        // ===================================================================

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

        function copyTrackingCode(code) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(code).then(() => {
                    showAlert('success', 'คัดลอกสำเร็จ', `รหัสติดตาม ${code} ถูกคัดลอกแล้ว`);
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                    fallbackCopyTextToClipboard(code);
                });
            } else {
                fallbackCopyTextToClipboard(code);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showAlert('success', 'คัดลอกสำเร็จ', `รหัสติดตาม ${text} ถูกคัดลอกแล้ว`);
                } else {
                    showAlert('error', 'ไม่สามารถคัดลอกได้', 'กรุณาคัดลอกรหัสติดตามด้วยตนเอง');
                }
            } catch (err) {
                console.error('Fallback: Oops, unable to copy', err);
                showAlert('error', 'ไม่สามารถคัดลอกได้', 'กรุณาคัดลอกรหัสติดตามด้วยตนเอง');
            }

            document.body.removeChild(textArea);
        }

        function showDownloadTemplates() {
            Swal.fire({
                title: 'ดาวน์โหลดแบบฟอร์ม',
                html: `
                    <div class="list-group">
                        <a href="<?php echo base_url('downloads/forms/application_form.pdf'); ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-pdf text-danger me-2"></i>แบบฟอร์มคำขอทั่วไป
                        </a>
                        <a href="<?php echo base_url('downloads/forms/permit_form.pdf'); ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-pdf text-danger me-2"></i>แบบฟอร์มขอใบอนุญาต
                        </a>
                        <a href="<?php echo base_url('downloads/forms/certificate_form.pdf'); ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-pdf text-danger me-2"></i>แบบฟอร์มขอหนังสือรับรอง
                        </a>
                    </div>
                `,
                showCloseButton: true,
                showConfirmButton: false,
                width: '600px'
            });
        }

        // ===================================================================
        // *** AUTO-SAVE DRAFT SYSTEM ***
        // ===================================================================

        // Auto-save draft
        setInterval(function () {
            try {
                const form = document.getElementById('esvForm');
                if (!form) return;

                const formData = new FormData(form);
                const formObj = {};

                for (let [key, value] of formData.entries()) {
                    // Skip file input and array fields
                    if (key !== 'esv_ods_file' && !key.includes('[]') && value !== '') {
                        formObj[key] = value;
                    }
                }

                // Add data from checked radio buttons
                const checkedRadios = form.querySelectorAll('input[type="radio"]:checked');
                checkedRadios.forEach(radio => {
                    if (radio.name && radio.value) {
                        formObj[radio.name] = radio.value;
                    }
                });

                localStorage.setItem('esv_ods_draft', JSON.stringify(formObj));
            } catch (error) {
                console.error('Error saving draft:', error);
            }
        }, 30000); // Every 30 seconds

        // Load draft on page load
        window.addEventListener('load', function () {
            try {
                const draft = localStorage.getItem('esv_ods_draft');
                if (draft && !isUserLoggedIn) { // Load draft only when not logged in
                    const draftData = JSON.parse(draft);

                    Object.keys(draftData).forEach(key => {
                        try {
                            const element = document.querySelector(`[name="${key}"]`);

                            if (element && !element.value) {
                                // Skip file input
                                if (element.type === 'file') {
                                    return; // Cannot set value for file input
                                }

                                if (element.type === 'radio') {
                                    const radioElement = document.querySelector(`[name="${key}"][value="${draftData[key]}"]`);
                                    if (radioElement) {
                                        radioElement.checked = true;
                                    }
                                } else if (element.type === 'checkbox') {
                                    element.checked = draftData[key] === 'on' || draftData[key] === true;
                                } else {
                                    element.value = draftData[key];
                                }
                            }
                        } catch (elementError) {
                            console.warn(`Error setting value for ${key}:`, elementError);
                        }
                    });

                    console.log('✅ Draft loaded successfully');
                }
            } catch (e) {
                console.error('Error loading draft:', e);
                // Remove damaged draft
                localStorage.removeItem('esv_ods_draft');
            }
        });

        // ===================================================================
        // *** GLOBAL FUNCTION ASSIGNMENTS ***
        // ===================================================================

        // Make functions globally available
        window.handleEsvSubmit = handleEsvSubmit;
        window.removeFile = removeFile;
        window.setMainFile = setMainFile;
        window.clearAllFiles = clearAllFiles;
        window.clearFile = clearAllFiles; // Backward compatibility
        window.proceedAsGuest = proceedAsGuest;
        window.redirectToLogin = redirectToLogin;
        window.redirectToTrackStatus = redirectToTrackStatus;
        window.showDownloadTemplates = showDownloadTemplates;
        window.copyTrackingCode = copyTrackingCode;
        window.loadCategoriesByDepartment = loadCategoriesByDepartment;
        window.showCategoryInfo = showCategoryInfo;

        console.log('✅ All ESV functions and validations loaded successfully');
        console.log('✅ ESV reCAPTCHA integration loaded successfully');

    </script>

    <!-- JavaScript สำหรับจัดการหมวดหมู่แบบใหม่ -->
    <script>
        function updateCategoryOptions(groupedCategories) {
            const categorySelect = document.getElementById('esv_ods_category_id');

            // Reset to default options
            categorySelect.innerHTML = '<option value="">เลือกหมวดหมู่เอกสาร</option>';

            if (Object.keys(groupedCategories).length > 0) {
                Object.keys(groupedCategories).forEach(groupName => {
                    const optgroup = document.createElement('optgroup');
                    optgroup.label = groupName;

                    groupedCategories[groupName].forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.esv_category_id;
                        option.setAttribute('data-department', category.esv_category_department_id);
                        option.setAttribute('data-fee', category.esv_category_fee);
                        option.setAttribute('data-days', category.esv_category_process_days);

                        let optionText = category.esv_category_name;
                        if (category.esv_category_fee > 0) {
                            optionText += ` (ค่าธรรมเนียม ${parseFloat(category.esv_category_fee).toLocaleString()} บาท)`;
                        }
                        if (category.esv_category_process_days > 0) {
                            optionText += ` - ${category.esv_category_process_days} วันทำการ`;
                        }

                        option.textContent = optionText;
                        optgroup.appendChild(option);
                    });

                    categorySelect.appendChild(optgroup);
                });
            }

            // เพิ่ม option "อื่นๆ"
            const otherOption = document.createElement('option');
            otherOption.value = 'other';
            otherOption.textContent = 'อื่นๆ (ระบุ)';
            categorySelect.appendChild(otherOption);
        }

        function loadCategoriesByDepartment(departmentId) {
            if (departmentId && departmentId !== 'other') {
                fetch('<?php echo site_url("esv_ods/get_categories_by_department"); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'department_id=' + departmentId
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.grouped) {
                            updateCategoryOptions(data.grouped);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        }

        // Enhanced category info display
        function showCategoryInfo(categoryId) {
            const categoryInfo = document.getElementById('category_info');
            const categorySelect = document.getElementById('esv_ods_category_id');
            const selectedOption = categorySelect.querySelector(`option[value="${categoryId}"]`);

            if (categoryId && categoryId !== 'other' && selectedOption) {
                const department = selectedOption.getAttribute('data-department');
                const fee = parseFloat(selectedOption.getAttribute('data-fee')) || 0;
                const days = parseInt(selectedOption.getAttribute('data-days')) || 0;

                const categoryName = selectedOption.textContent.split(' (')[0]; // Get clean name

                // Build info HTML
                let infoHtml = `<div class="d-flex align-items-center mb-2">
                           <i class="fas fa-folder-open me-2" style="color: #667eea;"></i>
                           <strong>${categoryName}</strong>
                       </div>`;

                const infoItems = [];

                if (fee > 0) {
                    infoItems.push(`<span class="badge bg-info me-2">
                               <i class="fas fa-money-bill-wave me-1"></i>
                               ค่าธรรมเนียม: ${fee.toLocaleString()} บาท
                           </span>`);
                }

                if (days > 0) {
                    infoItems.push(`<span class="badge bg-warning text-dark">
                               <i class="fas fa-clock me-1"></i>
                               ระยะเวลา: ${days} วันทำการ
                           </span>`);
                }

                if (infoItems.length > 0) {
                    infoHtml += `<div class="mt-2">${infoItems.join('')}</div>`;
                }

                document.getElementById('category_description').innerHTML = infoHtml;
                categoryInfo.style.display = 'block';
            } else {
                categoryInfo.style.display = 'none';
            }
        }
    </script>





    <!-- Font Awesome และ Bootstrap  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>