<div class="text-center pages-head">
    <span class="font-pages-head">กระทู้ถาม - ตอบ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">
<!-- Modal สำหรับการยืนยันการตั้งกระทู้โดยไม่เข้าสู่ระบบ -->
<div class="modal fade" id="guestConfirmModal" tabindex="-1" aria-labelledby="guestConfirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"
            style="border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(173, 216, 230, 0.2), 0 8px 25px rgba(0,0,0,0.08); background: linear-gradient(135deg, #ffffff 0%, #f0f8ff 100%); overflow: hidden;">
            <div class="modal-header"
                style="background: linear-gradient(135deg, rgba(173, 216, 230, 0.1) 0%, rgba(135, 206, 250, 0.1) 100%); color: #2c3e50; border-radius: 20px 20px 0 0; border-bottom: 1px solid rgba(173, 216, 230, 0.2); backdrop-filter: blur(10px);">
                <h5 class="modal-title" id="guestConfirmModalLabel"
                    style="font-weight: 600; color: #4682b4; width: 100%; text-align: center;">
                    <i class="fas fa-sparkles me-2" style="color: #87ceeb;"></i>ยินดีต้อนรับสู่การตั้งกระทู้
                </h5>
            </div>
            <div class="modal-body text-center"
                style="padding: 2.5rem; background: linear-gradient(135deg, #ffffff 0%, #f0f8ff 100%);">
                <div class="mb-4">
                    <div
                        style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(173, 216, 230, 0.15) 0%, rgba(135, 206, 250, 0.15) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(173, 216, 230, 0.3);">
                        <i class="fas fa-user-circle"
                            style="font-size: 2.5rem; color: #4682b4; text-shadow: 0 2px 8px rgba(173, 216, 230, 0.4);"></i>
                    </div>
                </div>
                <h5 class="mb-3" style="color: #2c3e50; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    เริ่มต้นการใช้งาน</h5>
                <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.6; color: #6c757d;">
                    เข้าสู่ระบบเพื่อจัดการโพสต์และติดตามการตอบกลับหรือดำเนินการต่อโดยไม่ต้องเข้าสู่ระบบ</p>

                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-lg" onclick="redirectToLogin()"
                        style="background: linear-gradient(135deg, #87ceeb 0%, #4682b4 100%); border: none; color: white; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; box-shadow: 0 6px 20px rgba(135, 206, 250, 0.4); transition: all 0.3s ease; font-size: 1.1rem;">
                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                    </button>
                    <button type="button" class="btn btn-lg" onclick="proceedAsGuest()"
                        style="background: rgba(173, 216, 230, 0.08); border: 2px solid rgba(173, 216, 230, 0.3); color: #4682b4; border-radius: 15px; padding: 1rem 1.5rem; font-weight: 600; transition: all 0.3s ease; font-size: 1.1rem; backdrop-filter: blur(10px);">
                        <i class="fas fa-edit me-2"></i>ดำเนินการต่อโดยไม่เข้าสู่ระบบ
                    </button>
                </div>

                <!-- เพิ่ม decorative elements -->
                <div
                    style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(173, 216, 230, 0.08) 0%, rgba(135, 206, 250, 0.08) 100%); border-radius: 50%; z-index: -1;">
                </div>
                <div
                    style="position: absolute; bottom: -30px; left: -30px; width: 60px; height: 60px; background: linear-gradient(135deg, rgba(135, 206, 250, 0.08) 0%, rgba(173, 216, 230, 0.08) 100%); border-radius: 50%; z-index: -1;">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center pages-head">
    <span class="font-pages-head"
        style="font-size: 2.8rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(108, 117, 125, 0.2);">ตั้งกระทู้ถาม -
        ตอบ</span>
</div>

<div class="bg-pages" style="background: #ffffff; min-height: 100vh; padding: 2rem 0;">
    <div class="container-pages-news" style="position: relative; z-index: 10;">
        <div class="container-pages-news mb-5 mt-5"
            style="position: relative; z-index: 10; background: white; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); padding: 2rem; margin: 0 auto; max-width: 900px; overflow: hidden;"
            id="q_a">

            <!-- เพิ่ม decorative element -->
            <div
                style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #6c757d, #495057, #6c757d); background-size: 200% 100%; animation: gradientShift 3s ease-in-out infinite;">
            </div>



            <!-- ปุ่มดูกระทู้ทั้งหมด - เพิ่มใหม่ -->
            <div class="d-flex justify-content-end mb-3">
                <a href="<?php echo site_url('Pages/q_a'); ?>" class="btn view-all-btn" style="background: linear-gradient(135deg, #4682b4 0%, #87ceeb 100%); 
                  border: none; 
                  color: white; 
                  padding: 0.7rem 1.5rem; 
                  border-radius: 12px; 
                  font-size: 0.95rem; 
                  font-weight: 600; 
                  transition: all 0.3s ease; 
                  box-shadow: 0 4px 15px rgba(70, 130, 180, 0.3); 
                  text-decoration: none;
                  position: relative;
                  overflow: hidden;">
                    <span style="position: relative; z-index: 2;">
                        <i class="fas fa-list me-2"></i>ดูกระทู้ทั้งหมด
                    </span>
                    <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; z-index: 1;"
                        class="btn-shine-view"></div>
                </a>
            </div>



            <div class="underline">
                <form id="qaForm" action="<?php echo site_url('Pages/add_q_a'); ?>" method="post"
                    class="form-horizontal" enctype="multipart/form-data" onsubmit="return false;">
                    <input type="hidden" name="form_token" id="formToken" value="">
                    <br>

                    <!-- หัวข้อคำถาม -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper"
                            style="background: linear-gradient(135deg, rgba(108, 117, 125, 0.08) 0%, rgba(134, 142, 150, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(108, 117, 125, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                            <label class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-question-circle me-2" style="color: #6c757d;"></i>หัวข้อคำถาม<span
                                    style="color: #e74c3c; margin-left: 0.2rem;">*</span>
                            </label>
                        </div>
                        <div class="col-sm-12">
                            <input type="text" name="q_a_msg" class="form-control" required placeholder="กรอกคำถาม..."
                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(108, 117, 125, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                            <div class="invalid-feedback" id="q_a_msg_feedback"></div>
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <!-- ชื่อ -->
                        <div class="col-6">
                            <div class="form-group mb-4">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(108, 117, 125, 0.08) 0%, rgba(134, 142, 150, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(108, 117, 125, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-user me-2" style="color: #6c757d;"></i>ชื่อ<span
                                            style="color: #e74c3c; margin-left: 0.2rem;">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-12">
                                    <input type="text" name="q_a_by" class="form-control" required
                                        placeholder="เช่น นาย สมชาย ใจดี"
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(108, 117, 125, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <div class="invalid-feedback" id="q_a_by_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <!-- อีเมล -->
                        <div class="col-6">
                            <div class="form-group mb-4">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(108, 117, 125, 0.08) 0%, rgba(134, 142, 150, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(108, 117, 125, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-envelope me-2" style="color: #6c757d;"></i>อีเมล<span
                                            style="color: #e74c3c; margin-left: 0.2rem;">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-12">
                                    <input type="email" name="q_a_email" class="form-control" required
                                        placeholder="เช่น somchai@gmail.com"
                                        style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(108, 117, 125, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px);">
                                    <div class="invalid-feedback" id="q_a_email_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <!-- รายละเอียด -->
                    <div class="form-group mb-4">
                        <div class="form-label-wrapper"
                            style="background: linear-gradient(135deg, rgba(108, 117, 125, 0.08) 0%, rgba(134, 142, 150, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(108, 117, 125, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                            <label for="exampleFormControlTextarea1" class="form-label"
                                style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                <i class="fas fa-align-left me-2" style="color: #6c757d;"></i>รายละเอียด<span
                                    style="color: #e74c3c; margin-left: 0.2rem;">*</span>
                            </label>
                        </div>
                        <div class="col-sm-12">
                            <textarea name="q_a_detail" class="form-control" id="exampleFormControlTextarea1" rows="6"
                                placeholder="กรอกรายละเอียดเพิ่มเติม..."
                                style="border: none; border-radius: 15px; padding: 1rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(108, 117, 125, 0.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); backdrop-filter: blur(10px); resize: vertical;"></textarea>
                            <div class="invalid-feedback" id="q_a_detail_feedback"></div>
                        </div>
                    </div>

                    <br>

                    <div class="row" style="padding-bottom: 20px;">
                        <!-- รูปภาพ -->
                        <div class="col-9">
                            <div class="form-group">
                                <div class="form-label-wrapper"
                                    style="background: linear-gradient(135deg, rgba(108, 117, 125, 0.08) 0%, rgba(134, 142, 150, 0.08) 100%); border-radius: 12px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; box-shadow: 0 4px 12px rgba(108, 117, 125, 0.15); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                                    <label class="form-label"
                                        style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #495057;">
                                        <i class="fas fa-images me-2" style="color: #6c757d;"></i>รูปภาพเพิ่มเติม<small
                                            style="color: #6c757d; font-weight: 400; margin-left: 0.5rem;">(สามารถเพิ่มได้หลายรูป)</small>
                                    </label>
                                </div>
                                <div class="col-sm-12">
                                    <!-- File Upload Zone -->
                                    <div class="file-upload-wrapper"
                                        style="border: 2px dashed #dee2e6; border-radius: 15px; padding: 1.5rem; text-align: center; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); transition: all 0.3s ease; cursor: pointer; box-shadow: 0 6px 20px rgba(108, 117, 125, 0.15); backdrop-filter: blur(10px);"
                                        ondrop="handleDrop(event)" ondragover="handleDragOver(event)"
                                        ondragenter="handleDragEnter(event)" ondragleave="handleDragLeave(event)">
                                        <div id="upload-placeholder" class="upload-placeholder">
                                            <i class="fas fa-cloud-upload-alt"
                                                style="font-size: 2rem; color: #6c757d; margin-bottom: 0.5rem;"></i>
                                            <p style="margin: 0; color: #6c757d; font-size: 1rem;">คลิกเพื่อเลือกรูปภาพ
                                                หรือลากไฟล์มาวางที่นี่</p>
                                            <small class="text-muted mt-2 d-block">รองรับไฟล์: JPG, JPEG, PNG (สูงสุด 5
                                                รูป)(ไม่เกิน 5 MB)</small>
                                        </div>
                                    </div>
                                    <input type="file" id="q_a_imgs" name="q_a_imgs[]" class="form-control"
                                        accept="image/*" multiple onchange="handleFileSelect(this)"
                                        style="display: none;">

                                    <!-- File Preview Area -->
                                    <div id="file-preview-area" class="file-preview-area mt-3" style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted" style="font-size: 0.9rem;">
                                                <i class="fas fa-images me-1"></i>ไฟล์ที่เลือก (<span
                                                    id="file-count">0</span>/5)
                                            </span>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="clearAllFiles()"
                                                style="border-radius: 8px; font-size: 0.8rem;">
                                                <i class="fas fa-times me-1"></i>ลบทั้งหมด
                                            </button>
                                        </div>
                                        <div id="preview-container" class="preview-container"
                                            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1rem; max-height: 300px; overflow-y: auto; padding: 1rem; background: #f8f9fa; border-radius: 10px; border: 1px solid #e9ecef;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ปุ่มส่ง -->
                        <div class="col-3">
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" id="submitQaBtn" class="btn modern-submit-btn"
                                    onclick="handleQaSubmit(event)"
                                    style="background: linear-gradient(135deg, #a8e6cf 0%, #88d8a3 100%); border: none; color: #2d5a3d; padding: 1rem 2rem; border-radius: 15px; font-size: 1.1rem; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(136, 216, 163, 0.3); position: relative; overflow: hidden; min-width: 150px;">
                                    <span style="position: relative; z-index: 2;">
                                        <i class="fas fa-paper-plane me-2"></i>ส่งกระทู้
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



<!-- เพิ่ม CSS สำหรับปุ่ม -->
<style>
    /* CSS สำหรับปุ่มดูกระทู้ทั้งหมด */
    .view-all-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(70, 130, 180, 0.4) !important;
        background: linear-gradient(135deg, #1e90ff 0%, #4682b4 100%) !important;
        color: white !important;
        text-decoration: none !important;
    }

    .view-all-btn:hover .btn-shine-view {
        left: 100%;
    }

    .view-all-btn:active {
        transform: translateY(-1px);
    }

    .view-all-btn:focus {
        outline: none;
        box-shadow: 0 6px 20px rgba(70, 130, 180, 0.4) !important;
    }

    /* Responsive สำหรับปุ่ม */
    @media (max-width: 768px) {
        .view-all-btn {
            font-size: 0.85rem !important;
            padding: 0.6rem 1.2rem !important;
        }
    }
</style>


<style>
    @keyframes gradientShift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    /* เพิ่ม hover effect สำหรับ form labels */
    .form-label-wrapper:hover {
        background: linear-gradient(135deg, rgba(108, 117, 125, 0.12) 0%, rgba(134, 142, 150, 0.12) 100%) !important;
        box-shadow: 0 6px 16px rgba(108, 117, 125, 0.2) !important;
        transform: translateY(-2px);
    }

    .form-control:focus {
        border-color: transparent !important;
        box-shadow: 0 8px 25px rgba(108, 117, 125, 0.25) !important;
        transform: translateY(-1px);
        background: linear-gradient(135deg, #ffffff 0%, #f1f3f4 100%) !important;
    }

    .modern-submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(136, 216, 163, 0.4) !important;
        background: linear-gradient(135deg, #88d8a3 0%, #7dd87f 100%) !important;
    }

    .modern-submit-btn:hover .btn-shine {
        left: 100%;
    }

    .modern-submit-btn:active {
        transform: translateY(-1px);
    }

    /* File Upload Styles */
    .file-upload-wrapper {
        transition: all 0.3s ease;
    }

    .file-upload-wrapper:hover {
        background: linear-gradient(135deg, #f1f3f4 0%, #e9ecef 100%) !important;
        box-shadow: 0 8px 25px rgba(108, 117, 125, 0.2) !important;
        transform: translateY(-2px);
        border-color: #6c757d !important;
    }

    .file-upload-wrapper.drag-over {
        background: linear-gradient(135deg, #e3f2fd 0%, #f0f8ff 100%) !important;
        border-color: #2196f3 !important;
        box-shadow: 0 8px 25px rgba(33, 150, 243, 0.3) !important;
        transform: scale(1.02);
    }

    /* File Preview Styles */
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

    .preview-image {
        width: 100%;
        height: 80px;
        object-fit: cover;
        border-radius: 6px 6px 0 0;
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
        backdrop-filter: blur(4px);
    }

    .remove-file:hover {
        background: rgba(220, 53, 69, 1);
        transform: scale(1.1);
    }

    /* Progress animation */
    @keyframes uploadProgress {
        0% {
            width: 0%;
        }

        100% {
            width: 100%;
        }
    }

    .upload-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, #4caf50, #8bc34a);
        border-radius: 0 0 6px 6px;
        animation: uploadProgress 1.5s ease-in-out;
    }

    /* เพิ่ม hover effects สำหรับปุ่มใน modal */
    .modal-body button:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }

    .modal-body button:first-of-type:hover {
        background: linear-gradient(135deg, #4682b4 0%, #1e90ff 100%) !important;
        box-shadow: 0 8px 25px rgba(135, 206, 250, 0.5) !important;
    }

    .modal-body button:last-of-type:hover {
        background: linear-gradient(135deg, rgba(173, 216, 230, 0.15) 0%, rgba(135, 206, 250, 0.15) 100%) !important;
        border-color: rgba(173, 216, 230, 0.5) !important;
        box-shadow: 0 6px 20px rgba(173, 216, 230, 0.3) !important;
    }

    .modal-body button:active {
        transform: translateY(0);
    }

    /* เพิ่ม animation สำหรับ modal */
    .modal.fade .modal-dialog {
        transform: scale(0.8) translateY(-50px);
        transition: all 0.3s ease;
    }

    .modal.show .modal-dialog {
        transform: scale(1) translateY(0);
    }

    /* เพิ่ม glassmorphism effect */
    .modal-header {
        backdrop-filter: blur(10px) !important;
    }

    /* เพิ่ม loading animation */
    .loading-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .font-pages-head {
            font-size: 2rem !important;
        }

        .container-pages-news {
            margin: 0 1rem !important;
            padding: 1.5rem !important;
        }

        .row .col-6 {
            width: 100% !important;
            margin-bottom: 1rem;
        }

        .col-9,
        .col-3 {
            width: 100% !important;
        }

        .preview-container {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)) !important;
        }
    }
</style>

<!-- Font Awesome สำหรับ icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Bootstrap CSS และ JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ==============================================
    // แก้ไข Error และเพิ่ม Error Handling
    // ==============================================

    // แก้ไข 1: เพิ่ม error handling และป้องกัน console error
    window.addEventListener('error', function (e) {
        // ป้องกัน error จาก extension หรือ external script
        if (e.message && (e.message.includes('message channel') || e.message.includes('check_login_status'))) {
            e.preventDefault();
            return true;
        }
    });

    // แก้ไข 2: เพิ่ม check_login_status function ที่หายไป
    function check_login_status() {
        try {
            // ตรวจสอบสถานะ login จากตัวแปรที่มีอยู่
            return typeof window.isUserLoggedIn !== 'undefined' ? window.isUserLoggedIn : false;
        } catch (error) {
            console.log('Check login status error (ignored):', error);
            return false;
        }
    }

    // แก้ไข 3: ป้องกัน error จาก undefined variables
    const safeGetVariable = (varName, defaultValue = null) => {
        try {
            return typeof window[varName] !== 'undefined' ? window[varName] : defaultValue;
        } catch (e) {
            return defaultValue;
        }
    };

    // ==============================================
    // ตัวแปร Global ที่ปลอดภัย
    // ==============================================

    // รับข้อมูลการ login จาก PHP (คืนค่าเป็นรูปแบบเดิม)
    const isUserLoggedIn = <?= json_encode($is_logged_in); ?>;
    const userInfo = <?= json_encode($user_info); ?>;

    // ตัวแปรสำหรับเก็บสถานะ guest
    let hasConfirmedAsGuest = isUserLoggedIn; // ถ้า login แล้วถือว่ายืนยันแล้ว

    // ตัวแปรสำหรับเก็บ modal instance
    let guestModalInstance = null;

    // ตัวแปรสำหรับเก็บไฟล์ที่เลือก
    let selectedFiles = [];
    const maxFiles = 5;
    const maxFileSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

    // ตัวแปรป้องกันการส่งซ้ำ
    let formSubmitting = false;

    // Debug ข้อมูล login status
    console.log('Login Status:', isUserLoggedIn);
    console.log('User Info:', userInfo);

    // ==============================================
    // ฟังก์ชันหลัก
    // ==============================================

    // ฟังก์ชันสร้าง form token
    function generateFormToken() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    // ฟังก์ชันจัดการ field ตามสถานะ login - แก้ไขให้ปลอดภัย
    function updateFormFieldsBasedOnLoginStatus() {
        try {
            const nameField = document.querySelector('input[name="q_a_by"]');
            const emailField = document.querySelector('input[name="q_a_email"]');

            console.log('🔧 userInfo structure:', userInfo);

            if (isUserLoggedIn && userInfo) {
                let userName = '';
                let userEmail = '';

                // ✅ รองรับทั้งโครงสร้างจาก Model และ Controller
                if (userInfo.user_info) {
                    // จาก Model: userInfo.user_info.email
                    userName = userInfo.user_info.name || '';
                    userEmail = userInfo.user_info.email || userInfo.user_info.username || '';
                } else {
                    // จาก Controller โดยตรง: userInfo.email
                    userName = userInfo.name || '';
                    userEmail = userInfo.email || userInfo.username || '';
                }

                // ✅ ถ้ายังไม่มี email ลองหาจาก root level
                if (!userEmail) {
                    userEmail = userInfo.email || userInfo.username || '';
                }

                console.log('📧 Final Email:', userEmail);
                console.log('👤 Final Name:', userName);

                // ตั้งค่า Name field
                if (nameField && userName) {
                    nameField.value = userName;
                    nameField.readOnly = true;
                    nameField.style.backgroundColor = '#f8f9fa';
                    nameField.style.cursor = 'not-allowed';
                    console.log('✅ Name field updated');
                }

                // ตั้งค่า Email field
                if (emailField && userEmail) {
                    emailField.value = userEmail;
                    emailField.readOnly = true;
                    emailField.style.backgroundColor = '#f8f9fa';
                    emailField.style.cursor = 'not-allowed';
                    console.log('✅ Email field updated');
                }

                // เพิ่มข้อความแจ้งเตือน
                if (userName || userEmail) {
                    addLoginInfoMessage(nameField, 'ใช้ข้อมูลจากบัญชีของคุณ');
                    addLoginInfoMessage(emailField, 'ใช้ข้อมูลจากบัญชีของคุณ');
                }

            } else {
                console.log('👤 Setting up for guest user');

                // Reset สำหรับ guest
                [nameField, emailField].forEach(field => {
                    if (field) {
                        field.readOnly = false;
                        field.style.backgroundColor = '';
                        field.style.cursor = '';
                        field.value = '';
                    }
                });

                // ลบข้อความแจ้งเตือน
                document.querySelectorAll('.login-info').forEach(info => info.remove());
            }
        } catch (error) {
            console.error('❌ Form update error:', error);
        }
    }

    // ==============================================
    // ฟังก์ชันจัดการไฟล์
    // ==============================================

    // ฟังก์ชันจัดการการเลือกไฟล์
    function handleFileSelect(input) {
        if (input._processing) {
            console.log('File selection already in progress, skipping...');
            return;
        }

        input._processing = true;
        console.log('Starting file selection process...');

        const files = Array.from(input.files);

        if (files.length === 0) {
            input._processing = false;
            return;
        }

        if (selectedFiles.length + files.length > maxFiles) {
            showAlert('warning', 'เกินจำนวนที่กำหนด', `คุณสามารถอัพโหลดได้สูงสุด ${maxFiles} รูปภาพเท่านั้น`);
            input._processing = false;
            input.value = '';
            return;
        }

        let validFiles = [];
        let duplicateCount = 0;

        for (let file of files) {
            if (!validateFile(file)) {
                input._processing = false;
                input.value = '';
                return;
            }

            // ตรวจสอบไฟล์ซ้ำ
            const fileSignature = `${file.name}_${file.size}_${file.type}`;
            const isDuplicate = selectedFiles.some(existingFile => {
                const existingSignature = `${existingFile.name}_${existingFile.size}_${existingFile.type}`;
                return existingSignature === fileSignature;
            });

            if (isDuplicate) {
                duplicateCount++;
                continue;
            }

            file.id = Date.now() + Math.random() + Math.random();
            validFiles.push(file);
        }

        if (duplicateCount > 0) {
            showAlert('info', 'มีไฟล์ซ้ำ', `พบไฟล์ซ้ำ ${duplicateCount} ไฟล์ จะเพิ่มเฉพาะไฟล์ใหม่เท่านั้น`, 3000);
        }

        selectedFiles = [...selectedFiles, ...validFiles];
        updateFileDisplay();

        setTimeout(() => {
            input.value = '';
            input._processing = false;
        }, 100);
    }

    // ฟังก์ชันตรวจสอบไฟล์
    function validateFile(file) {
        if (!allowedTypes.includes(file.type.toLowerCase())) {
            showAlert('error', 'ประเภทไฟล์ไม่ถูกต้อง', 'รองรับเฉพาะไฟล์ JPG, JPEG, PNG, GIF และ WebP เท่านั้น');
            return false;
        }

        if (file.size > maxFileSize) {
            showAlert('error', 'ไฟล์ใหญ่เกินไป', `ขนาดไฟล์ต้องไม่เกิน ${maxFileSize / (1024 * 1024)} MB`);
            return false;
        }

        return true;
    }

    // ฟังก์ชันแสดงไฟล์ที่เลือก
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

    // ฟังก์ชันสร้าง preview item
    function createPreviewItem(file, index) {
        const div = document.createElement('div');
        div.className = 'preview-item';
        div.setAttribute('data-file-id', file.id);

        const img = document.createElement('img');
        img.className = 'preview-image';
        img.alt = file.name;

        const reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);

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

        div.appendChild(img);
        div.appendChild(info);
        div.appendChild(removeBtn);

        const progress = document.createElement('div');
        progress.className = 'upload-progress';
        div.appendChild(progress);

        return div;
    }

    // ฟังก์ชันลบไฟล์
    function removeFile(fileId) {
        selectedFiles = selectedFiles.filter(file => file.id !== fileId);
        updateFileDisplay();
    }

    // ฟังก์ชันลบไฟล์ทั้งหมด
    function clearAllFiles() {
        selectedFiles = [];
        updateFileDisplay();
        showAlert('info', 'ลบไฟล์แล้ว', 'ลบไฟล์ทั้งหมดเรียบร้อยแล้ว', 2000);
    }

    // ฟังก์ชันแปลงขนาดไฟล์
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // ==============================================
    // ฟังก์ชันจัดการ Drag & Drop
    // ==============================================

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

        const files = Array.from(e.dataTransfer.files);
        const imageFiles = files.filter(file => file.type.startsWith('image/'));

        if (imageFiles.length !== files.length) {
            showAlert('warning', 'ไฟล์บางไฟล์ไม่ใช่รูปภาพ', 'จะอัพโหลดเฉพาะไฟล์รูปภาพเท่านั้น');
        }

        if (imageFiles.length === 0) {
            showAlert('error', 'ไม่พบไฟล์รูปภาพ', 'กรุณาเลือกไฟล์รูปภาพเท่านั้น');
            return;
        }

        if (selectedFiles.length + imageFiles.length > maxFiles) {
            showAlert('warning', 'เกินจำนวนที่กำหนด', `คุณสามารถอัพโหลดได้สูงสุด ${maxFiles} รูปภาพเท่านั้น`);
            return;
        }

        let validFiles = [];
        let invalidCount = 0;
        let duplicateCount = 0;

        for (let file of imageFiles) {
            if (!validateFile(file)) {
                invalidCount++;
                continue;
            }

            const fileSignature = `${file.name}_${file.size}_${file.type}`;
            const isDuplicate = selectedFiles.some(existingFile => {
                const existingSignature = `${existingFile.name}_${existingFile.size}_${existingFile.type}`;
                return existingSignature === fileSignature;
            });

            if (isDuplicate) {
                duplicateCount++;
                continue;
            }

            file.id = Date.now() + Math.random() + Math.random();
            validFiles.push(file);
        }

        selectedFiles = [...selectedFiles, ...validFiles];

        if (validFiles.length > 0) {
            updateFileDisplay();
            showAlert('success', 'เพิ่มไฟล์สำเร็จ', `เพิ่มไฟล์ ${validFiles.length} ไฟล์เรียบร้อยแล้ว`, 2000);
        }

        if (duplicateCount > 0 || invalidCount > 0) {
            let message = '';
            if (duplicateCount > 0) message += `มีไฟล์ซ้ำ ${duplicateCount} ไฟล์ `;
            if (invalidCount > 0) message += `มีไฟล์ไม่ถูกต้อง ${invalidCount} ไฟล์`;
            showAlert('warning', 'มีไฟล์ที่ไม่สามารถเพิ่มได้', message.trim());
        }
    }

    // ==============================================
    // ฟังก์ชันแสดงข้อความแจ้งเตือน
    // ==============================================

    function showAlert(icon, title, text, timer = null) {
        const config = {
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: '#6c757d',
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

    // ==============================================
    // ฟังก์ชันจัดการการส่งฟอร์ม - แก้ไขให้ปลอดภัย
    // ==============================================

    // ฟังก์ชันจัดการการส่งฟอร์ม - แก้ไขให้ปลอดภัยพร้อม reCAPTCHA
    function handleQaSubmit(event) {
        event.preventDefault();

        try {
            if (formSubmitting) {
                console.log('Form submission already in progress');
                return false;
            }

            if (!isUserLoggedIn && !hasConfirmedAsGuest) {
                console.log('👤 Guest user needs confirmation');
                showModal();
            } else {
                console.log('✅ User authorized, submitting form');
                submitForm();
            }
        } catch (error) {
            console.error('Handle submit error:', error);
            showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถส่งฟอร์มได้ กรุณาลองใหม่');
        }

        return false;
    }

    // ฟังก์ชันส่งฟอร์ม - แก้ไขให้ปลอดภัย
    // ฟังก์ชัน submitForm() ที่ปรับปรุงใหม่ทั้งหมด
    // ฟังก์ชันส่งฟอร์ม - ปรับปรุงใหม่พร้อม reCAPTCHA
    function submitForm() {
        try {
            const form = document.getElementById('qaForm');

            if (!form) {
                throw new Error('Form not found');
            }

            if (form.checkValidity()) {
                if (formSubmitting) {
                    return;
                }

                formSubmitting = true;

                const submitBtn = document.getElementById('submitQaBtn');
                const originalContent = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังตรวจสอบ...';

                // *** เพิ่ม: ตรวจสอบและขอ reCAPTCHA token ***
                executeRecaptchaAndSubmit(form, submitBtn, originalContent);
            } else {
                showAlert('warning', 'กรุณากรอกข้อมูลให้ครบถ้วน', 'มีข้อมูลที่จำเป็นยังไม่ได้กรอก');
            }
        } catch (error) {
            console.error('Submit form error:', error);
            showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถส่งฟอร์มได้ กรุณาลองใหม่');
            formSubmitting = false;
        }
    }

    // ฟังก์ชันขอ reCAPTCHA token และส่งฟอร์ม
    function executeRecaptchaAndSubmit(form, submitBtn, originalContent) {
        try {
            // ตรวจสอบการตั้งค่า reCAPTCHA
            if (!window.RECAPTCHA_SITE_KEY) {
                console.warn('⚠️ reCAPTCHA Site Key ไม่ได้ตั้งค่า - ข้าม reCAPTCHA สำหรับ Development');

                if (window.SKIP_RECAPTCHA_FOR_DEV) {
                    console.log('🔧 Development mode: ข้าม reCAPTCHA และส่งฟอร์มโดยตรง');
                    performFormSubmission(form, submitBtn, originalContent, null);
                    return;
                }

                resetSubmitButton(submitBtn, originalContent);
                showAlert('error', 'การยืนยันตัวตนไม่พร้อม', 'กรุณาติดต่อผู้ดูแลระบบ');
                return;
            }

            if (!window.recaptchaReady) {
                console.warn('⚠️ reCAPTCHA ยังไม่พร้อม - รอสักครู่');
                resetSubmitButton(submitBtn, originalContent);
                showAlert('warning', 'กรุณารอสักครู่', 'ระบบยืนยันตัวตนกำลังโหลด กรุณาลองใหม่อีกครั้ง');
                return;
            }

            // ✅ กำหนด reCAPTCHA action และ source ตาม user type (FLEXIBLE VERSION)
            let recaptchaAction = 'qa_public_submit'; // default สำหรับประชาชน
            let userTypeDetected = 'public';
            let sourceType = 'qa_form';

            // ✅ ปรับปรุงการตรวจสอบ user type ให้ครอบคลุมมากขึ้น
            if (window.isUserLoggedIn && window.userInfo) {
                try {
                    console.log('🔍 User info structure:', window.userInfo);

                    let userType = null;
                    let userId = null;

                    // ✅ ตรวจสอบจากหลายแหล่งข้อมูล
                    if (window.userInfo.user_type) {
                        userType = window.userInfo.user_type;
                    } else if (window.userInfo.type) {
                        userType = window.userInfo.type;
                    } else if (window.userInfo.system) {
                        userType = window.userInfo.system;
                    } else if (window.userInfo.level) {
                        userType = window.userInfo.level;
                    } else if (window.userInfo.m_level) {
                        userType = window.userInfo.m_level;
                    }

                    // ✅ ตรวจสอบ user ID
                    if (window.userInfo.m_id) {
                        userId = window.userInfo.m_id;
                        userType = userType || 'staff';
                    } else if (window.userInfo.user_id) {
                        userId = window.userInfo.user_id;
                    } else if (window.userInfo.id) {
                        userId = window.userInfo.id;
                    }

                    console.log('🔍 User type detection details:', {
                        userType: userType,
                        userId: userId,
                        rawUserInfo: window.userInfo
                    });

                    // ✅ กำหนด staff levels และตรวจสอบ
                    const staffLevels = [
                        'staff', 'admin', 'system_admin', 'super_admin', 'user_admin',
                        'เจ้าหน้าที่', 'ผู้ดูแลระบบ', 'administrator',
                        '1', '2', '3', '4', '5'
                    ];

                    // ✅ ตรวจสอบว่าเป็น staff หรือไม่ (ตรวจสอบจาก userType เท่านั้น)
                    const isStaff = userType && (
                        staffLevels.includes(String(userType).toLowerCase()) ||
                        staffLevels.includes(userType)
                    );

                    if (isStaff) {
                        recaptchaAction = 'qa_admin_submit';
                        userTypeDetected = 'staff';
                        sourceType = 'staff_portal';
                        console.log('👤 Staff/Admin user detected:', {
                            userType: userType,
                            userId: userId,
                            action: recaptchaAction
                        });
                    } else {
                        recaptchaAction = 'qa_public_submit';
                        userTypeDetected = 'citizen';
                        sourceType = 'member_portal';
                        console.log('👥 Public/Citizen user detected:', {
                            userType: userType,
                            userId: userId,
                            action: recaptchaAction
                        });
                    }

                } catch (userTypeError) {
                    console.warn('⚠️ Error detecting user type:', userTypeError);
                    console.log('🔄 Falling back to public user');
                    recaptchaAction = 'qa_guest_submit';
                    userTypeDetected = 'guest';
                    sourceType = 'fallback_portal';
                }
            } else {
                console.log('👤 Guest user detected (not logged in)');
                recaptchaAction = 'qa_guest_submit';
                userTypeDetected = 'guest';
                sourceType = 'guest_portal';
            }

            // ✅ เพิ่มข้อมูล source เพิ่มเติมสำหรับการ debug
            const additionalSource = {
                page: 'qa_form',
                feature: 'create_topic',
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent.substring(0, 50)
            };

            console.log('🔐 Final reCAPTCHA configuration:', {
                action: recaptchaAction,
                userType: userTypeDetected,
                source: sourceType,
                additional: additionalSource
            });

            submitBtn.innerHTML = '<i class="fas fa-shield-alt fa-spin me-2"></i>ยืนยันตัวตน...';

            // ✅ Execute reCAPTCHA with correct action
            grecaptcha.execute(window.RECAPTCHA_SITE_KEY, {
                action: recaptchaAction
            })
                .then(function (token) {
                    console.log('✅ reCAPTCHA token received for action:', recaptchaAction);
                    console.log('📝 Token preview:', token.substring(0, 20) + '...');
                    console.log('📊 Token length:', token.length);

                    submitBtn.innerHTML = '<i class="fas fa-paper-plane fa-spin me-2"></i>กำลังส่ง...';

                    // ✅ เพิ่ม hidden fields ทั้งหมดที่จำเป็น
                    addHiddenField(form, 'g-recaptcha-response', token);
                    addHiddenField(form, 'recaptcha_action', recaptchaAction);
                    addHiddenField(form, 'recaptcha_source', sourceType);
                    addHiddenField(form, 'user_type_detected', userTypeDetected);

                    // ✅ Debug fields
                    addHiddenField(form, 'debug_recaptcha_action', recaptchaAction);
                    addHiddenField(form, 'debug_user_type_detected', userTypeDetected);
                    addHiddenField(form, 'debug_source_type', sourceType);

                    // ✅ Additional context
                    addHiddenField(form, 'form_source', 'qa_submission');
                    addHiddenField(form, 'client_timestamp', additionalSource.timestamp);
                    addHiddenField(form, 'user_agent_info', additionalSource.userAgent);

                    console.log('📋 All form fields added:', {
                        'g-recaptcha-response': token.substring(0, 20) + '...',
                        'recaptcha_action': recaptchaAction,
                        'recaptcha_source': sourceType,
                        'user_type_detected': userTypeDetected
                    });

                    // ส่งฟอร์มพร้อม token และข้อมูลที่ครบถ้วน
                    performFormSubmission(form, submitBtn, originalContent, token);
                })
                .catch(function (error) {
                    console.error('❌ reCAPTCHA Error for action', recaptchaAction + ':', error);
                    resetSubmitButton(submitBtn, originalContent);

                    let errorMessage = 'การยืนยันตัวตนล้มเหลว กรุณาลองใหม่อีกครั้ง';

                    if (error.message) {
                        if (error.message.includes('network')) {
                            errorMessage = 'ปัญหาการเชื่อมต่อเครือข่าย กรุณาตรวจสอบอินเทอร์เน็ต';
                        } else if (error.message.includes('timeout')) {
                            errorMessage = 'การยืนยันตัวตนใช้เวลานานเกินไป กรุณาลองใหม่';
                        } else if (error.message.includes('key')) {
                            errorMessage = 'ปัญหาการกำหนดค่า reCAPTCHA กรุณาติดต่อผู้ดูแลระบบ';
                        }
                    }

                    showAlert('error', 'การยืนยันตัวตนล้มเหลว', errorMessage + ' หรือรีเฟรชหน้า');
                });

        } catch (error) {
            console.error('❌ executeRecaptchaAndSubmit Error:', error);
            resetSubmitButton(submitBtn, originalContent);
            showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถยืนยันตัวตนได้ กรุณาลองใหม่');
        }
    }


    // ✅ เพิ่ม: Helper function สำหรับเพิ่ม hidden field
    function addHiddenField(form, name, value) {
        try {
            // ลบ field เดิมถ้ามี
            const existingField = form.querySelector(`input[name="${name}"]`);
            if (existingField) {
                existingField.remove();
            }

            // เพิ่ม field ใหม่
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = name;
            hiddenField.value = value;
            form.appendChild(hiddenField);

            console.log(`✅ เพิ่ม hidden field: ${name} = ${value}`);
            return true;
        } catch (error) {
            console.error('❌ Error adding hidden field:', error);
            return false;
        }
    }

    // ฟังก์ชันส่งฟอร์มจริง

    // ฟังก์ชัน reset submit button
    function resetSubmitButton(submitBtn, originalContent) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
        formSubmitting = false;
    }
    function performFormSubmission(form, submitBtn, originalContent, recaptchaToken) {
        try {
            console.log('🚀 Starting form submission with complete data...');

            // ✅ เพิ่ม form token
            const formToken = generateFormToken();
            const tokenField = document.getElementById('formToken');
            if (tokenField) {
                tokenField.value = formToken;
            }

            const formData = new FormData();

            // ✅ เพิ่มข้อมูลฟอร์มทั้งหมด
            const formElements = form.elements;
            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];

                if (element.type === 'file' || element.type === 'button' || element.type === 'submit') {
                    continue;
                }

                if (element.name && element.value !== '') {
                    formData.append(element.name, element.value);
                    console.log(`📝 Form field: ${element.name} = ${element.value}`);
                }
            }

            // ✅ เพิ่มไฟล์ที่เลือก
            if (typeof selectedFiles !== 'undefined' && selectedFiles.length > 0) {
                selectedFiles.forEach((file, index) => {
                    formData.append('q_a_imgs[]', file);
                    console.log(`📎 File ${index + 1}: ${file.name} (${file.size} bytes)`);
                });
            }

            // ✅ เพิ่ม reCAPTCHA และข้อมูล source อย่างครบถ้วน
            if (recaptchaToken) {
                // Token
                formData.append('g-recaptcha-response', recaptchaToken);

                // Source information
                const sourceType = form.querySelector('input[name="recaptcha_source"]')?.value || 'unknown';
                const actionType = form.querySelector('input[name="recaptcha_action"]')?.value || 'unknown';
                const userType = form.querySelector('input[name="user_type_detected"]')?.value || 'unknown';

                formData.append('recaptcha_source', sourceType);
                formData.append('recaptcha_action', actionType);
                formData.append('user_type_detected', userType);

                console.log('✅ reCAPTCHA data added to form:', {
                    token_length: recaptchaToken.length,
                    source: sourceType,
                    action: actionType,
                    user_type: userType
                });
            } else {
                console.log('⚠️ No reCAPTCHA token (Development mode)');
                formData.append('skip_recaptcha', '1');
            }

            // ✅ บอกให้ Controller ส่ง JSON response
            formData.append('ajax_request', '1');

            // ✅ เพิ่มข้อมูล debug เพิ่มเติม
            formData.append('js_debug_info', JSON.stringify({
                timestamp: new Date().toISOString(),
                user_agent: navigator.userAgent.substring(0, 100),
                screen_resolution: `${screen.width}x${screen.height}`,
                page_url: window.location.href
            }));

            console.log('📨 Sending form data to:', form.action);

            // ✅ ส่งข้อมูล
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(response => {
                    console.log('📥 Response received:', {
                        status: response.status,
                        statusText: response.statusText,
                        url: response.url
                    });

                    const contentType = response.headers.get('content-type');
                    console.log('📋 Content-Type:', contentType);

                    if (contentType && contentType.includes('application/json')) {
                        return response.json().then(jsonData => {
                            console.log('📊 JSON Response:', jsonData);
                            return handleJsonResponse(jsonData);
                        });
                    } else {
                        return response.text().then(text => {
                            console.log('📄 HTML/Text Response length:', text.length);
                            return handleHtmlResponse(text, response);
                        });
                    }
                })
                .then(result => {
                    if (result && result.handled === true) {
                        console.log('✅ Response handled successfully');
                        return;
                    }

                    // Fallback success handling
                    console.log('🔄 Using fallback success handling');
                    showAlert('success', 'ส่งกระทู้สำเร็จ!', 'กระทู้ของคุณถูกส่งเรียบร้อยแล้ว');
                    resetFormAfterSubmit();
                    setTimeout(() => {
                        window.location.href = '<?= site_url("Pages/q_a"); ?>';
                    }, 2000);
                })
                .catch(error => {
                    console.error('❌ Fetch error:', error);
                    handleSubmissionError(error);
                })
                .finally(() => {
                    resetSubmitButton(submitBtn, originalContent);
                });

        } catch (error) {
            console.error('❌ performFormSubmission error:', error);
            resetSubmitButton(submitBtn, originalContent);
            showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถส่งฟอร์มได้ กรุณาลองใหม่');
        }
    }


    // ฟังก์ชันจัดการ JSON response
    function handleJsonResponse(jsonData) {
        // ตรวจสอบ reCAPTCHA error
        if (jsonData.error_type === 'recaptcha_missing' || jsonData.error_type === 'recaptcha_failed') {
            console.log('❌ reCAPTCHA Error:', jsonData.message);
            showAlert('warning', 'การยืนยันตัวตนล้มเหลว', jsonData.message || 'กรุณาลองใหม่อีกครั้ง');
            return { handled: true };
        }

        // ตรวจสอบ URL detection ใน JSON
        if (jsonData.has_url === true ||
            jsonData.url_detected === true ||
            jsonData.block_submit === true) {
            console.log('URL detected via JSON:', jsonData.url_detected_fields || jsonData.url_fields || []);
            showUrlErrorModal(
                jsonData.url_detected_fields || jsonData.url_fields || [],
                jsonData.message || 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ'
            );
            return { handled: true };
        }

        // ตรวจสอบ vulgar_detected ใน JSON
        if (jsonData.vulgar_detected === true || jsonData.has_vulgar === true) {
            console.log('Vulgar detected via JSON:', jsonData.vulgar_words);
            showVulgarErrorModalWithWords(jsonData.vulgar_words || []);
            return { handled: true };
        }

        // ตรวจสอบ success/error ใน JSON
        if (jsonData.success === false) {
            if (jsonData.error_type === 'url_content') {
                showUrlErrorModal(
                    jsonData.url_fields || [],
                    jsonData.message || 'พบ URL ในข้อความ'
                );
                return { handled: true };
            }

            if (jsonData.error_type === 'vulgar_content') {
                showVulgarErrorModalWithWords(jsonData.vulgar_words || []);
                return { handled: true };
            }

            throw new Error(jsonData.message || 'Server error');
        }

        if (jsonData.success === true) {
            showAlert('success', 'ส่งกระทู้สำเร็จ!', jsonData.message || 'กระทู้ของคุณถูกส่งเรียบร้อยแล้ว');
            resetFormAfterSubmit();
            setTimeout(() => {
                window.location.href = '<?= site_url("Pages/q_a"); ?>';
            }, 2000);
            return { handled: true };
        }

        return { handled: false, data: jsonData };
    }

    // ฟังก์ชันจัดการ HTML response
    function handleHtmlResponse(text, response) {
        // ตรวจสอบ URL detection ใน HTML response
        if (text.includes('has_url') ||
            text.includes('url_detected') ||
            text.includes('block_submit') ||
            text.includes('ไม่อนุญาตให้มี URL') ||
            text.includes('check_no_urls') ||
            text.includes('URL-like pattern found')) {

            console.log('URL content detected in HTML response');

            // พยายามดึง URL fields จาก HTML
            let urlFields = [];
            try {
                const urlFieldsMatch = text.match(/url_detected_fields['"]\s*:\s*\[(.*?)\]/);
                if (urlFieldsMatch && urlFieldsMatch[1]) {
                    urlFields = urlFieldsMatch[1]
                        .split(',')
                        .map(field => field.replace(/['"]/g, '').trim())
                        .filter(field => field.length > 0);
                }
            } catch (e) {
                console.log('Could not extract URL fields from HTML');
            }

            showUrlErrorModal(urlFields, 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ กรุณาแก้ไขและลองใหม่');
            return { handled: true };
        }

        // ตรวจสอบ PHP Session Flash Messages ใน HTML
        if (text.includes('save_vulgar') ||
            text.includes('vulgar_words') ||
            text.includes('พบคำไม่เหมาะสม') ||
            text.includes('vulgar')) {

            console.log('Vulgar content detected in HTML response');

            // พยายามดึง vulgar words จาก HTML ถ้ามี
            let vulgarWords = [];
            try {
                const vulgarMatch = text.match(/vulgar_words['"]\s*:\s*\[(.*?)\]/);
                if (vulgarMatch && vulgarMatch[1]) {
                    vulgarWords = vulgarMatch[1]
                        .split(',')
                        .map(word => word.replace(/['"]/g, '').trim())
                        .filter(word => word.length > 0);
                }
            } catch (e) {
                console.log('Could not extract vulgar words from HTML');
            }

            showVulgarErrorModalWithWords(vulgarWords);
            return { handled: true };
        }

        // ตรวจสอบ redirect สำเร็จ
        if (response.ok && (response.url.includes('/q_a') || text.includes('q_a'))) {
            console.log('Successful redirect detected');
            showAlert('success', 'ส่งกระทู้สำเร็จ!', 'กระทู้ของคุณถูกส่งเรียบร้อยแล้ว');
            resetFormAfterSubmit();
            setTimeout(() => {
                window.location.href = '<?= site_url("Pages/q_a"); ?>';
            }, 2000);
            return { handled: true };
        }

        return { handled: false, text: text };
    }

    // ฟังก์ชันจัดการ error
    function handleSubmissionError(error) {
        let errorMessage = 'ไม่สามารถส่งกระทู้ได้ กรุณาลองใหม่อีกครั้ง';

        // ตรวจสอบประเภท error
        if (error.message.includes('URL') ||
            error.message.includes('url') ||
            error.message.includes('ลิงก์')) {
            showUrlErrorModal([], 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ');
        } else if (error.message.includes('vulgar') || error.message.includes('คำไม่เหมาะสม')) {
            showVulgarErrorModal();
        } else if (error.message.includes('reCAPTCHA') || error.message.includes('recaptcha')) {
            showAlert('warning', 'การยืนยันตัวตนล้มเหลว', 'กรุณาลองใหม่อีกครั้ง หรือรีเฟรชหน้า');
        } else {
            showAlert('error', 'เกิดข้อผิดพลาด', errorMessage);
        }
    }

    function showVulgarErrorModal() {
        Swal.fire({
            icon: 'error',
            title: '⚠️ พบเนื้อหาไม่เหมาะสม',
            html: `
            <div style="text-align: left; padding: 1rem;">
                <p style="margin-bottom: 1rem; color: #721c24;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>ไม่สามารถบันทึกกระทู้ได้</strong>
                </p>
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <p style="margin: 0; color: #721c24; font-size: 0.95rem;">
                        📝 <strong>สาเหตุ:</strong> พบคำหรือข้อความที่ไม่เหมาะสมในกระทู้ของคุณ
                    </p>
                </div>
                <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 1rem;">
                    <p style="margin: 0; color: #0c5460; font-size: 0.9rem;">
                        💡 <strong>แนะนำ:</strong> กรุณาตรวจสอบและแก้ไขข้อความให้เหมาะสม แล้วลองส่งใหม่อีกครั้ง
                    </p>
                </div>
            </div>
        `,
            confirmButtonColor: '#dc3545',
            confirmButtonText: '<i class="fas fa-edit me-2"></i>แก้ไขข้อความ',
            allowOutsideClick: false,
            customClass: {
                popup: 'vulgar-error-modal',
                title: 'vulgar-error-title',
                htmlContainer: 'vulgar-error-content'
            }
        });
    }

    // ฟังก์ชันเพื่อ decode Unicode escape sequences และ HTML entities
    function decodeWord(word) {
        try {
            console.log('🔍 Decoding word:', word);

            let decodedWord = word;

            // 1. Decode Unicode escape sequences (\u0e2a\u0e38\u0e20\u0e32\u0e1e)
            if (decodedWord.includes('\\u')) {
                try {
                    decodedWord = JSON.parse('"' + decodedWord + '"');
                    console.log('📝 Unicode decoded:', decodedWord);
                } catch (e) {
                    console.log('⚠️ Unicode decode failed:', e.message);
                }
            }

            // 2. Decode HTML entities (&amp;, &lt;, &gt;, etc.)
            if (decodedWord.includes('&')) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = decodedWord;
                decodedWord = tempDiv.textContent || tempDiv.innerText || decodedWord;
                console.log('📝 HTML entity decoded:', decodedWord);
            }

            // 3. Decode URL encoding (%20, %E0%B8%AA, etc.)
            if (decodedWord.includes('%')) {
                try {
                    decodedWord = decodeURIComponent(decodedWord);
                    console.log('📝 URL decoded:', decodedWord);
                } catch (e) {
                    console.log('⚠️ URL decode failed:', e.message);
                }
            }

            // 4. Trim whitespace
            decodedWord = decodedWord.trim();

            console.log('✅ Final decoded word:', decodedWord);
            return decodedWord;

        } catch (error) {
            console.error('❌ Error decoding word:', word, error);
            return word; // Return original if decoding fails
        }
    }


    function showVulgarErrorModalWithWords(vulgarWords = []) {
        console.log('🚨 showVulgarErrorModalWithWords called with:', vulgarWords);

        let wordsHtml = '';
        let processedWords = [];

        if (vulgarWords && vulgarWords.length > 0) {
            // Process และ decode แต่ละคำ
            processedWords = vulgarWords.map(word => {
                const originalWord = word;
                const decodedWord = decodeWord(word);

                // เก็บข้อมูลทั้ง original และ decoded เพื่อการ debug
                return {
                    original: originalWord,
                    decoded: decodedWord,
                    display: decodedWord || originalWord // ใช้ decoded ถ้ามี ไม่งั้นใช้ original
                };
            });

            console.log('📋 Processed words:', processedWords);

            wordsHtml = `
            <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 1rem; margin: 1rem 0;">
                <p style="margin: 0 0 0.5rem 0; color: #856404; font-weight: bold;">
                    🚫 <strong>คำที่ไม่เหมาะสม:</strong>
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    ${processedWords.map(wordObj => `
                        <span style="
                            background: #f8d7da; 
                            color: #721c24; 
                            padding: 0.3rem 0.6rem; 
                            border-radius: 15px; 
                            font-size: 0.85rem;
                            border: 1px solid #f5c6cb;
                        " title="Original: ${wordObj.original}">${wordObj.display}</span>
                    `).join('')}
                </div>
            </div>
        `;
        }

        Swal.fire({
            icon: 'error',
            title: '⚠️ พบเนื้อหาไม่เหมาะสม',
            html: `
            <div style="text-align: left; padding: 1rem;">
                <p style="margin-bottom: 1rem; color: #721c24;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>ไม่สามารถบันทึกกระทู้ได้</strong>
                </p>
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <p style="margin: 0; color: #721c24; font-size: 0.95rem;">
                        📝 <strong>สาเหตุ:</strong> พบคำหรือข้อความที่ไม่เหมาะสมในกระทู้ของคุณ
                    </p>
                </div>
                ${wordsHtml}
                <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 1rem;">
                    <p style="margin: 0; color: #0c5460; font-size: 0.9rem;">
                        💡 <strong>แนะนำ:</strong> กรุณาลบหรือแก้ไขคำดังกล่าวออกจากข้อความ แล้วลองส่งใหม่อีกครั้ง
                    </p>
                </div>
            </div>
        `,
            confirmButtonColor: '#dc3545',
            confirmButtonText: '<i class="fas fa-edit me-2"></i>แก้ไขข้อความ',
            allowOutsideClick: false,
            customClass: {
                popup: 'vulgar-error-modal',
                title: 'vulgar-error-title',
                htmlContainer: 'vulgar-error-content'
            },
            width: '600px'
        });
    }

    // ฟังก์ชันสำหรับแสดง Modal เตือน URL
    function showUrlErrorModal(urlFields = [], message = '') {
        console.log('🚨 showUrlErrorModal called with fields:', urlFields, 'message:', message);

        let fieldsHtml = '';

        if (urlFields && urlFields.length > 0) {
            // แปลงชื่อฟิลด์เป็นภาษาไทย
            const fieldNames = {
                'q_a_msg': 'หัวข้อคำถาม',
                'q_a_detail': 'รายละเอียดคำถาม',
                'q_a_by': 'ชื่อผู้ถาม',
                'q_a_reply_detail': 'รายละเอียดการตอบ',
                'q_a_reply_by': 'ชื่อผู้ตอบ'
            };

            const displayFields = urlFields.map(field => fieldNames[field] || field);

            fieldsHtml = `
            <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 1rem; margin: 1rem 0;">
                <p style="margin: 0 0 0.5rem 0; color: #856404; font-weight: bold;">
                    🎯 <strong>พบ URL ในฟิลด์:</strong>
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    ${displayFields.map(field => `
                        <span style="
                            background: #ffeaa7; 
                            color: #856404; 
                            padding: 0.3rem 0.6rem; 
                            border-radius: 15px; 
                            font-size: 0.85rem;
                            border: 1px solid #fdcb6e;
                            font-weight: 500;
                        ">${field}</span>
                    `).join('')}
                </div>
            </div>
        `;
        }

        const displayMessage = message || 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ';

        Swal.fire({
            icon: 'warning',
            title: '🔗 พบ URL ในข้อความ',
            html: `
            <div style="text-align: left; padding: 1rem;">
                <p style="margin-bottom: 1rem; color: #856404;">
                    <i class="fas fa-link me-2"></i>
                    <strong>ไม่สามารถบันทึกกระทู้ได้</strong>
                </p>
                <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <p style="margin: 0; color: #856404; font-size: 0.95rem;">
                        🚫 <strong>สาเหตุ:</strong> ${displayMessage}
                    </p>
                </div>
                ${fieldsHtml}
                <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 1rem;">
                    <p style="margin: 0; color: #0c5460; font-size: 0.9rem;">
                        💡 <strong>แนะนำ:</strong> กรุณาลบ URL, ลิงก์, หรือชื่อเว็บไซต์ออกจากข้อความ แล้วลองส่งใหม่อีกครั้ง
                    </p>
                </div>
                <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                    <p style="margin: 0; color: #6c757d; font-size: 0.85rem;">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>ตัวอย่าง URL ที่ไม่อนุญาต:</strong> google.com, www.facebook.com, https://example.com
                    </p>
                </div>
            </div>
        `,
            confirmButtonColor: '#ffc107',
            confirmButtonText: '<i class="fas fa-edit me-2"></i>แก้ไขข้อความ',
            allowOutsideClick: false,
            customClass: {
                popup: 'url-error-modal',
                title: 'url-error-title',
                htmlContainer: 'url-error-content'
            },
            width: '600px'
        });
    }

    // ฟังก์ชันหลักสำหรับจัดการ AJAX Response
    function handleAjaxResponse(response) {
        console.log('📨 AJAX Response received:', response);

        // ตรวจสอบ URL Detection ก่อน
        if (response.url_detected === true) {
            console.log('🔗 URL detected, showing URL error modal');
            showUrlErrorModal(response.url_fields || [], response.message || '');
            return;
        }

        // ตรวจสอบ Vulgar Detection
        if (response.vulgar_detected === true) {
            console.log('🚫 Vulgar content detected, showing vulgar error modal');
            showVulgarErrorModalWithWords(response.vulgar_words || []);
            return;
        }

        // ตรวจสอบ Validation Error
        if (response.validation_error === true) {
            console.log('📝 Validation error detected');
            Swal.fire({
                icon: 'warning',
                title: 'ข้อมูลไม่ครบถ้วน',
                text: response.message || 'กรุณาตรวจสอบข้อมูลและลองใหม่อีกครั้ง',
                confirmButtonColor: '#ffc107',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        // Success case
        if (response.success === true) {
            console.log('✅ Success response');
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: response.message || 'บันทึกข้อมูลเรียบร้อยแล้ว',
                confirmButtonColor: '#28a745',
                confirmButtonText: 'ตกลง'
            }).then(() => {
                // Redirect หรือ refresh หน้า
                if (response.redirect_url) {
                    window.location.href = response.redirect_url;
                } else {
                    window.location.reload();
                }
            });
            return;
        }

        // Generic error case
        console.log('❌ Generic error response');
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: response.message || 'ไม่สามารถดำเนินการได้ กรุณาลองใหม่อีกครั้ง',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'ตกลง'
        });
    }

    // ============================================
    // เพิ่ม CSS สำหรับ Modal
    // ============================================

    // เพิ่มใน <style> tag ของหน้า
    const vulgarModalStyles = `
<style>
.vulgar-error-modal {
    border-radius: 20px !important;
    box-shadow: 0 20px 60px rgba(220, 53, 69, 0.3) !important;
}

.vulgar-error-title {
    color: #721c24 !important;
    font-size: 1.4rem !important;
    font-weight: 700 !important;
}

.vulgar-error-content {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
}

.swal2-confirm.swal2-styled {
    border-radius: 12px !important;
    font-weight: 600 !important;
    padding: 0.7rem 1.5rem !important;
    transition: all 0.3s ease !important;
}

.swal2-confirm.swal2-styled:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
}
</style>
`;

    // เพิ่ม CSS เข้าไปใน head
    document.head.insertAdjacentHTML('beforeend', vulgarModalStyles);




    // ฟังก์ชัน reset form หลังส่งสำเร็จ
    function resetFormAfterSubmit() {
        try {
            const form = document.getElementById('qaForm');
            if (form) {
                form.reset();
            }

            selectedFiles = [];
            updateFileDisplay();

            const fileInput = document.getElementById('q_a_imgs');
            if (fileInput) {
                fileInput.value = '';
                fileInput._processing = false;
            }

            const tokenField = document.getElementById('formToken');
            if (tokenField) {
                tokenField.value = '';
            }
        } catch (error) {
            console.error('Reset form error:', error);
        }
    }

    // ==============================================
    // ฟังก์ชันจัดการ Modal - แก้ไขให้ปลอดภัย
    // ==============================================

    function showModal() {
        try {
            const modalElement = document.getElementById('guestConfirmModal');

            if (!modalElement) {
                console.error('Modal element not found');
                return;
            }

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try {
                    guestModalInstance = new bootstrap.Modal(modalElement);
                    guestModalInstance.show();
                    return;
                } catch (e) {
                    console.log('Bootstrap 5 method failed, using fallback:', e);
                }
            }

            // Fallback method
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            modalElement.style.paddingRight = '15px';
            document.body.classList.add('modal-open');
            document.body.style.paddingRight = '15px';

            if (!document.querySelector('.modal-backdrop')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.setAttribute('data-custom-backdrop', 'true');
                document.body.appendChild(backdrop);
            }
        } catch (error) {
            console.error('Show modal error:', error);
        }
    }

    function hideModal() {
        try {
            const modalElement = document.getElementById('guestConfirmModal');

            if (guestModalInstance && typeof bootstrap !== 'undefined') {
                try {
                    guestModalInstance.hide();
                    guestModalInstance = null;
                    return;
                } catch (e) {
                    console.log('Bootstrap 5 hide failed, using fallback:', e);
                }
            }

            // Fallback method
            if (modalElement) {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                modalElement.style.paddingRight = '';
            }

            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';

            const backdrop = document.querySelector('.modal-backdrop[data-custom-backdrop="true"]');
            if (backdrop) {
                backdrop.remove();
            }
        } catch (error) {
            console.error('Hide modal error:', error);
        }
    }

    // ฟังก์ชันดำเนินการต่อโดยไม่ login
    function proceedAsGuest() {
        try {
            hasConfirmedAsGuest = true;
            hideModal();
            showAlert('info', 'ดำเนินการต่อโดยไม่เข้าสู่ระบบ', 'คุณสามารถกรอกข้อมูลและตั้งกระทู้ได้แล้ว', 2000);
        } catch (error) {
            console.error('Proceed as guest error:', error);
        }
    }

    // ฟังก์ชันเปลี่ยนเส้นทางไปหน้า login
    function redirectToLogin() {
        try {
            hideModal();
            const currentUrl = window.location.href;
            sessionStorage.setItem('redirect_after_login', currentUrl);
            // ปรับเปลี่ยน URL ตาม CodeIgniter 3
            window.open('/User', '_blank');
        } catch (error) {
            console.error('Redirect to login error:', error);
            // fallback
            window.open('/User', '_blank');
        }
    }

    // ฟังก์ชันแสดง welcome message สำหรับ user ที่ login แล้ว
    function showWelcomeMessageIfLoggedIn() {
        try {
            if (isUserLoggedIn && userInfo) {
                // ตรวจสอบโครงสร้างข้อมูล user
                let userName = 'ผู้ใช้';

                if (userInfo.user_info && userInfo.user_info.name) {
                    userName = userInfo.user_info.name;
                } else if (userInfo.name) {
                    userName = userInfo.name;
                }

                Swal.fire({
                    icon: 'success',
                    title: `ยินดีต้อนรับ ${userName}`,
                    text: 'คุณสามารถตั้งกระทู้ได้ทันที ข้อมูลของคุณจะถูกใช้โดยอัตโนมัติ',
                    timer: 3000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true,
                    background: 'linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%)',
                    color: '#155724'
                });
            }
        } catch (error) {
            console.error('Show welcome message error:', error);
        }
    }

    // ==============================================
    // Event Listeners - แก้ไขให้ปลอดภัย
    // ==============================================

    document.addEventListener('DOMContentLoaded', function () {
        try {
            // ป้องกัน default form submission
            const form = document.getElementById('qaForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    return false;
                });
            }

            // อัพเดท form fields ตามสถานะ login
            updateFormFieldsBasedOnLoginStatus();

            // ตั้งค่า upload wrapper
            let uploadWrapperInitialized = false;

            document.addEventListener('dragover', function (e) {
                e.preventDefault();
            });

            document.addEventListener('drop', function (e) {
                e.preventDefault();
            });

            const uploadWrapper = document.querySelector('.file-upload-wrapper');
            if (uploadWrapper && !uploadWrapperInitialized) {
                uploadWrapper.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const fileInput = document.getElementById('q_a_imgs');
                    if (fileInput) {
                        fileInput.click();
                    }
                });
                uploadWrapperInitialized = true;
            }

            // แสดง modal เฉพาะกรณีที่ไม่ได้ login
            if (!isUserLoggedIn) {
                setTimeout(function () {
                    if (!hasConfirmedAsGuest) {
                        showModal();
                    }
                }, 1000);
            } else {
                // แสดงข้อความต้อนรับสำหรับ user ที่ login แล้ว
                setTimeout(function () {
                    showWelcomeMessageIfLoggedIn();
                }, 500);
            }

            // เพิ่ม animation เมื่อหน้าโหลดเสร็จ
            const formContainer = document.querySelector('.container-pages-news');
            if (formContainer) {
                formContainer.style.opacity = '0';
                formContainer.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    formContainer.style.transition = 'all 0.6s ease';
                    formContainer.style.opacity = '1';
                    formContainer.style.transform = 'translateY(0)';
                }, 100);
            }

            console.log('✅ Page initialized successfully');

        } catch (error) {
            console.error('DOMContentLoaded error:', error);
        }
    });

    // ==============================================
    // ป้องกัน Error เพิ่มเติม
    // ==============================================

    // ป้องกัน error จาก browser extension
    if (typeof chrome !== 'undefined' && chrome.runtime) {
        try {
            chrome.runtime.onMessage = chrome.runtime.onMessage || function () { };
        } catch (extensionError) {
            console.log('Chrome extension error (ignored):', extensionError);
        }
    }

    // ป้องกัน undefined function errors
    window.onerror = function (msg, url, lineNo, columnNo, error) {
        if (msg.includes('check_login_status') || msg.includes('message channel')) {
            console.log('Ignored error:', msg);
            return true; // ป้องกันไม่ให้แสดง error ใน console
        }
        return false;
    };

    // ป้องกัน Promise rejection errors
    window.addEventListener('unhandledrejection', function (event) {
        if (event.reason && event.reason.message &&
            (event.reason.message.includes('message channel') ||
                event.reason.message.includes('check_login_status'))) {
            console.log('Ignored promise rejection:', event.reason);
            event.preventDefault();
        }
    });

    // สร้าง global variables ที่ปลอดภัย
    window.isUserLoggedIn = isUserLoggedIn;
    window.userInfo = userInfo;
    window.hasConfirmedAsGuest = hasConfirmedAsGuest;

    console.log('🛡️ Error protection initialized');
    console.log('✅ All systems ready');

</script>