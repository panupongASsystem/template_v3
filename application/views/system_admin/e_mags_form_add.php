<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-black">เพิ่มข้อมูล E-Magazine</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo site_url('E_mags_backend/add'); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                        
                        <!-- ชื่อ E-Magazine -->
                        <div class="form-group row mb-4">
                            <label class="col-sm-3 col-form-label">ชื่อ E-Magazine <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="original_name" id="original_name" class="form-control" required>
                                <small class="form-text text-muted">ชื่อที่จะแสดงให้ผู้ใช้เห็น</small>
                            </div>
                        </div>

                        <!-- ไฟล์ PDF -->
                        <div class="form-group row mb-4">
                            <label class="col-sm-3 col-form-label">ไฟล์ PDF <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="file" name="pdf_file" id="pdf_file" class="form-control" accept=".pdf" required>
                                <small class="form-text text-muted">
                                    <i class="bi bi-info-circle text-info"></i>
                                    เฉพาะไฟล์ .PDF เท่านั้น (ขนาดไม่เกิน 30MB)
                                </small>
                                <div id="pdf-preview" class="mt-2" style="display: none;">
                                    <div class="alert alert-info">
                                        <i class="bi bi-file-earmark-pdf text-danger"></i>
                                        <span id="pdf-name"></span>
                                        <span id="pdf-size" class="badge bg-secondary ms-2"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- รูปหน้าปก -->
                        <div class="form-group row mb-4">
                            <label class="col-sm-3 col-form-label">รูปหน้าปก</label>
                            <div class="col-sm-9">
                                <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/*">
                                <small class="form-text text-muted">
                                    <i class="bi bi-info-circle text-info"></i>
                                    รองรับไฟล์ .JPG, .JPEG, .PNG (ขนาดไม่เกิน 2MB) 
                                    <br>
                                    <i class="bi bi-lightbulb text-warning"></i>
                                    <strong>หากไม่อัปโหลดรูปหน้าปก ระบบจะสร้างรูปหน้าปกจากหน้าแรกของ PDF อัตโนมัติ</strong>
                                </small>
                                <div id="cover-preview" class="mt-3" style="display: none;">
                                    <div class="cover-preview-container">
                                        <img id="cover-preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 250px;">
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-image text-success"></i>
                                                <span id="cover-name"></span>
                                                <span id="cover-size" class="badge bg-secondary ms-2"></span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- คำอธิบาย (Optional) -->
                        <div class="form-group row mb-4">
                            <label class="col-sm-3 col-form-label">คำอธิบาย</label>
                            <div class="col-sm-9">
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="คำอธิบายเพิ่มเติม (ถ้ามี)"></textarea>
                                <small class="form-text text-muted">คำอธิบายจะไม่ถูกบันทึก แต่สามารถใช้เป็นข้อมูลอ้างอิงได้</small>
                            </div>
                        </div>

                        <!-- ปุ่มบันทึก -->
                        <div class="form-group row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-success btn-lg me-2" id="submit-btn">
                                    <i class="bi bi-check-circle"></i> บันทึกข้อมูล
                                </button>
                                <a class="btn btn-secondary btn-lg" href="<?= site_url('E_mags_backend'); ?>" role="button">
                                    <i class="bi bi-x-circle"></i> ยกเลิก
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- เปลี่ยนส่วน JavaScript ทั้งหมดในไฟล์ e_mags_form_add.php เป็นนี้ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
    // กำหนด PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

    // ตรวจสอบหลังจากโหลดหน้าเสร็จแล้ว
    document.addEventListener('DOMContentLoaded', function() {
        // ตรวจสอบว่ามี flash data สำหรับสร้างหน้าปกหรือไม่
        <?php if ($this->session->flashdata('need_cover_generation')): ?>
            const coverData = <?= $this->session->flashdata('need_cover_generation'); ?>;
            console.log('Need to generate cover:', coverData);
            
            // แสดง modal loading
            showCoverGenerationModal();
            
            // เริ่มสร้างหน้าปก
            setTimeout(() => {
                generateAutoThumbnail(coverData.pdf_file, coverData.cover_file, coverData.e_mag_id);
            }, 1000);
        <?php endif; ?>
    });

    // แสดง Modal Loading สำหรับการสร้างหน้าปก
    function showCoverGenerationModal() {
        Swal.fire({
            title: 'กำลังสร้างหน้าปก',
            html: `
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-2">กำลังสร้างหน้าปกจากหน้าแรกของ PDF...</p>
                    <small class="text-muted">กรุณารอสักครู่</small>
                </div>
            `,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // ฟังก์ชันสร้างหน้าปกอัตโนมัติ
    async function generateAutoThumbnail(pdfFileName, coverFileName, emagId) {
        try {
            console.log('Starting auto cover generation for:', pdfFileName);
            
            // โหลด PDF
            const pdfUrl = '<?= base_url("docs/file/"); ?>' + pdfFileName;
            console.log('Loading PDF from:', pdfUrl);
            
            const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
            console.log('PDF loaded successfully, pages:', pdf.numPages);
            
            // ดึงหน้าแรก
            const page = await pdf.getPage(1);
            
            // สร้าง canvas
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            // กำหนดขนาดหน้าปก (สัดส่วน 3:4)
            const coverWidth = 400;
            const coverHeight = 533;
            
            canvas.width = coverWidth;
            canvas.height = coverHeight;
            
            // คำนวณ scale
            const viewport = page.getViewport({ scale: 1 });
            const scaleX = coverWidth / viewport.width;
            const scaleY = coverHeight / viewport.height;
            const scale = Math.min(scaleX, scaleY);
            
            const scaledViewport = page.getViewport({ scale: scale });
            
            // วาดพื้นหลังสีขาว
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, coverWidth, coverHeight);
            
            // คำนวณตำแหน่งให้อยู่กลาง
            const offsetX = (coverWidth - scaledViewport.width) / 2;
            const offsetY = (coverHeight - scaledViewport.height) / 2;
            
            // Render PDF page
            await page.render({
                canvasContext: ctx,
                viewport: scaledViewport,
                transform: [1, 0, 0, 1, offsetX, offsetY]
            }).promise;
            
            console.log('PDF page rendered to canvas');
            
            // แปลงเป็น base64
            const coverDataUrl = canvas.toDataURL('image/png', 0.8);
            
            // ส่งไปบันทึกที่ server
            const saveResult = await saveCoverToServer(coverFileName, coverDataUrl);
            
            if (saveResult.status === 'success') {
                console.log('Cover saved successfully');
                
                Swal.fire({
                    icon: 'success',
                    title: 'สร้างหน้าปกสำเร็จ!',
                    text: 'ระบบได้สร้างหน้าปกจากหน้าแรกของ PDF เรียบร้อยแล้ว',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location.href = '<?= site_url("e_mags_backend"); ?>';
                });
                
            } else {
                throw new Error(saveResult.message);
            }
            
        } catch (error) {
            console.error('Error generating cover:', error);
            
            Swal.fire({
                icon: 'warning',
                title: 'ไม่สามารถสร้างหน้าปกได้',
                html: `
                    <p>เกิดข้อผิดพลาดในการสร้างหน้าปกอัตโนมัติ</p>
                    <small class="text-muted">${error.message}</small>
                    <br><br>
                    <p><strong>E-Magazine ถูกเพิ่มเรียบร้อยแล้ว</strong> แต่คุณสามารถเพิ่มรูปหน้าปกทีหลังได้</p>
                `,
                confirmButtonText: 'ตกลง'
            }).then(() => {
                window.location.href = '<?= site_url("e_mags_backend"); ?>';
            });
        }
    }

    // ส่งข้อมูลหน้าปกไปบันทึกที่ server
    async function saveCoverToServer(coverFileName, coverDataUrl) {
        try {
            console.log('Saving cover to server:', coverFileName);
            
            const formData = new FormData();
            formData.append('cover_filename', coverFileName);
            formData.append('cover_data', coverDataUrl);
            
            const response = await fetch('<?= base_url("e_mags_backend/save_generated_cover"); ?>', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const result = await response.json();
            console.log('Server response:', result);
            
            return result;
            
        } catch (error) {
            console.error('Error saving cover:', error);
            return {
                status: 'error',
                message: 'ไม่สามารถเชื่อมต่อกับ server ได้: ' + error.message
            };
        }
    }

    // ฟังก์ชันตรวจสอบฟอร์ม (รวมทุกอย่างเป็นฟังก์ชันเดียว)
    function validateForm() {
        const originalName = document.getElementById('original_name').value.trim();
        const pdfFile = document.getElementById('pdf_file').files[0];
        const coverImage = document.getElementById('cover_image').files[0];
        
        // ตรวจสอบชื่อ E-Magazine
        if (!originalName) {
            Swal.fire({
                icon: 'error',
                title: 'กรุณาใส่ชื่อ E-Magazine',
                text: 'ชื่อ E-Magazine เป็นข้อมูลที่จำเป็น'
            });
            return false;
        }
        
        // ตรวจสอบไฟล์ PDF
        if (!pdfFile) {
            Swal.fire({
                icon: 'error',
                title: 'กรุณาเลือกไฟล์ PDF',
                text: 'ไฟล์ PDF เป็นข้อมูลที่จำเป็น'
            });
            return false;
        }
        
        // ตรวจสอบประเภทไฟล์ PDF
        if (pdfFile.type !== 'application/pdf') {
            Swal.fire({
                icon: 'error',
                title: 'ประเภทไฟล์ไม่ถูกต้อง',
                text: 'กรุณาเลือกไฟล์ PDF เท่านั้น'
            });
            return false;
        }
        
        // ตรวจสอบขนาดไฟล์ PDF (10MB = 10 * 1024 * 1024)
        if (pdfFile.size > 10 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'ไฟล์ PDF มีขนาดใหญ่เกินไป',
                text: 'ขนาดไฟล์ PDF ต้องไม่เกิน 10MB'
            });
            return false;
        }
        
        // ตรวจสอบรูปหน้าปก (ไม่บังคับ)
        if (coverImage) {
            // ตรวจสอบประเภทไฟล์รูปภาพ
            const allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedImageTypes.includes(coverImage.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'ประเภทไฟล์รูปภาพไม่ถูกต้อง',
                    text: 'กรุณาเลือกไฟล์ .JPG, .JPEG หรือ .PNG เท่านั้น'
                });
                return false;
            }
            
            // ตรวจสอบขนาดไฟล์รูปภาพ (2MB = 2 * 1024 * 1024)
            if (coverImage.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไฟล์รูปภาพมีขนาดใหญ่เกินไป',
                    text: 'ขนาดไฟล์รูปภาพต้องไม่เกิน 2MB'
                });
                return false;
            }
            
            // มีรูปหน้าปก - ส่งฟอร์มปกติ
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> กำลังบันทึก...';
            submitBtn.disabled = true;
            return true;
        } else {
            // ไม่มีรูปหน้าปก - แสดงข้อความยืนยัน
            Swal.fire({
                title: 'ไม่มีรูปหน้าปก',
                text: 'ระบบจะสร้างหน้าปกจากหน้าแรกของ PDF ให้อัตโนมัติ',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'ดำเนินการต่อ',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    // แสดงการโหลด
                    const submitBtn = document.getElementById('submit-btn');
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> กำลังบันทึก...';
                    submitBtn.disabled = true;
                    
                    // ส่งฟอร์มจริง
                    document.querySelector('form').submit();
                } else {
                    // รีเซ็ตปุ่ม submit
                    const submitBtn = document.getElementById('submit-btn');
                    submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> บันทึกข้อมูล';
                    submitBtn.disabled = false;
                }
            });
            
            return false; // หยุดการส่งฟอร์มชั่วคราว
        }
    }
    
    // ฟังก์ชันแปลงขนาดไฟล์
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Event listener สำหรับไฟล์ PDF
    document.getElementById('pdf_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('pdf-preview');
        
        if (file) {
            // ตรวจสอบประเภทไฟล์
            if (file.type !== 'application/pdf') {
                Swal.fire({
                    icon: 'error',
                    title: 'ประเภทไฟล์ไม่ถูกต้อง',
                    text: 'กรุณาเลือกไฟล์ PDF เท่านั้น'
                });
                e.target.value = '';
                preview.style.display = 'none';
                return;
            }
            
            // ตรวจสอบขนาดไฟล์
            if (file.size > 30 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไฟล์มีขนาดใหญ่เกินไป',
                    text: 'ขนาดไฟล์ PDF ต้องไม่เกิน 30MB'
                });
                e.target.value = '';
                preview.style.display = 'none';
                return;
            }
            
            // แสดงตัวอย่าง
            document.getElementById('pdf-name').textContent = file.name;
            document.getElementById('pdf-size').textContent = formatFileSize(file.size);
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    });
    
    // Event listener สำหรับรูปหน้าปก
    document.getElementById('cover_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('cover-preview');
        const previewImg = document.getElementById('cover-preview-img');
        
        if (file) {
            // ตรวจสอบประเภทไฟล์
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'ประเภทไฟล์ไม่ถูกต้อง',
                    text: 'กรุณาเลือกไฟล์ .JPG, .JPEG หรือ .PNG เท่านั้น'
                });
                e.target.value = '';
                preview.style.display = 'none';
                return;
            }
            
            // ตรวจสอบขนาดไฟล์
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไฟล์มีขนาดใหญ่เกินไป',
                    text: 'ขนาดไฟล์รูปภาพต้องไม่เกิน 2MB'
                });
                e.target.value = '';
                preview.style.display = 'none';
                return;
            }
            
            // แสดงตัวอย่างรูปภาพ
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                document.getElementById('cover-name').textContent = file.name;
                document.getElementById('cover-size').textContent = formatFileSize(file.size);
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
    
    // ป้องกันการส่งฟอร์มซ้ำ
    document.querySelector('form').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submit-btn');
        setTimeout(() => {
            submitBtn.disabled = true;
        }, 100);
    });

    // เพิ่ม CSS
    const style = document.createElement('style');
    style.textContent = `
        .spinner-border {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: text-bottom;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
        }
        
        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
        
        .visually-hidden {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
    `;
    document.head.appendChild(style);
</script>
<style>
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .cover-preview-container {
        text-align: center;
        padding: 15px;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
    
    .cover-preview-container img {
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }
    
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        border-radius: 10px 10px 0 0 !important;
    }
</style>