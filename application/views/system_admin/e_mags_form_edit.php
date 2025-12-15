<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-black">แก้ไขข้อมูล E-Magazine</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo site_url('E_mags_backend/edit/' . $e_mag->id); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                        
                        <!-- ชื่อ E-Magazine -->
                        <div class="form-group row mb-4">
                            <label class="col-sm-3 col-form-label">ชื่อ E-Magazine <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="original_name" id="original_name" class="form-control" value="<?= htmlspecialchars($e_mag->original_name); ?>" required>
                                <small class="form-text text-muted">ชื่อที่จะแสดงให้ผู้ใช้เห็น</small>
                            </div>
                        </div>

                        <!-- ไฟล์ PDF ปัจจุบัน -->
                        <div class="form-group row mb-4">
                            <label class="col-sm-3 col-form-label">ไฟล์ PDF ปัจจุบัน</label>
                            <div class="col-sm-9">
                                <div class="current-file-container">
                                    <?php if (!empty($e_mag->file_name)) : ?>
                                        <div class="current-file-info">
                                            <div class="file-preview">
                                                <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 2rem;"></i>
                                                <div class="file-details">
                                                    <h6 class="mb-1"><?= htmlspecialchars($e_mag->original_name); ?></h6>
                                                    <small class="text-muted">
                                                        <?= $e_mag->file_name; ?>
                                                        <?php
                                                        $pdf_path = './docs/file/' . $e_mag->file_name;
                                                        if (file_exists($pdf_path)) {
                                                            $file_size = filesize($pdf_path);
                                                            echo ' • ' . formatFileSize($file_size);
                                                        }
                                                        ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="file-actions">
                                                <a href="<?= base_url('docs/file/' . $e_mag->file_name); ?>" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   target="_blank">
                                                    <i class="bi bi-eye"></i> ดูไฟล์
                                                </a>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            ไม่พบไฟล์ PDF
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- อัปโหลดไฟล์ PDF ใหม่ -->
                        <div class="form-group row mb-4">
                            <label class="col-sm-3 col-form-label">เปลี่ยนไฟล์ PDF</label>
                            <div class="col-sm-9">
                                <input type="file" name="pdf_file" id="pdf_file" class="form-control" accept=".pdf">
                                <small class="form-text text-muted">
                                    <i class="bi bi-info-circle text-info"></i>
                                    เลือกไฟล์ PDF ใหม่หากต้องการเปลี่ยน (ขนาดไม่เกิน 30MB)
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

                        <!-- รูปหน้าปกปัจจุบัน -->
                        <div class="form-group row mb-4">
                            <label class="col-sm-3 col-form-label">รูปหน้าปกปัจจุบัน</label>
                            <div class="col-sm-9">
                                <div class="current-cover-container">
                                    <?php if (!empty($e_mag->cover_image)) : ?>
                                        <div class="current-cover-preview">
                                            <a href="<?= base_url('docs/img/' . $e_mag->cover_image); ?>" 
                                               data-fancybox="current-cover" 
                                               data-caption="<?= htmlspecialchars($e_mag->original_name); ?>">
                                                <img src="<?= base_url('docs/img/' . $e_mag->cover_image); ?>" 
                                                     class="img-thumbnail current-cover-img" 
                                                     alt="รูปหน้าปกปัจจุบัน">
                                            </a>
                                            <div class="cover-info">
                                                <small class="text-muted">
                                                    <?= $e_mag->cover_image; ?>
                                                    <?php
                                                    $cover_path = './docs/img/' . $e_mag->cover_image;
                                                    if (file_exists($cover_path)) {
                                                        $cover_size = filesize($cover_path);
                                                        echo ' • ' . formatFileSize($cover_size);
                                                    }
                                                    ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            ไม่พบรูปหน้าปก
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- อัปโหลดรูปหน้าปกใหม่ -->
                        <div class="form-group row mb-4">
                            <label class="col-sm-3 col-form-label">เปลี่ยนรูปหน้าปก</label>
                            <div class="col-sm-9">
                                <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/*">
                                <small class="form-text text-muted">
                                    <i class="bi bi-info-circle text-info"></i>
                                    เลือกรูปภาพใหม่หากต้องการเปลี่ยน (ขนาดไม่เกิน 2MB)
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

                        <!-- ข้อมูลเพิ่มเติม -->
                        <div class="form-group row mb-4">
                            <label class="col-sm-3 col-form-label">ข้อมูลเพิ่มเติม</label>
                            <div class="col-sm-9">
                                <div class="info-container">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-event"></i>
                                                วันที่อัปโหลด: 
                                                <?php
                                                $upload_date = new DateTime($e_mag->uploaded_at);
                                                $thai_date = $upload_date->format('d/m/') . ($upload_date->format('Y') + 543);
                                                $time = $upload_date->format('H:i');
                                                echo $thai_date . ' ' . $time . ' น.';
                                                ?>
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="bi bi-hash"></i>
                                                ID: <?= $e_mag->id; ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ปุ่มบันทึก -->
                        <div class="form-group row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-success btn-lg me-2" id="submit-btn">
                                    <i class="bi bi-check-circle"></i> บันทึกการแก้ไข
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

<!-- Fancybox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">

<!-- เพิ่มหลังจาก Fancybox scripts -->
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
            title: 'กำลังสร้างหน้าปกใหม่',
            html: `
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-2">กำลังสร้างหน้าปกจากหน้าแรกของ PDF ใหม่...</p>
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
                    title: 'สร้างหน้าปกใหม่สำเร็จ!',
                    text: 'ระบบได้สร้างหน้าปกจากหน้าแรกของ PDF ใหม่เรียบร้อยแล้ว',
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
                    <p><strong>การแก้ไข E-Magazine สำเร็จแล้ว</strong> แต่คุณสามารถเพิ่มรูปหน้าปกทีหลังได้</p>
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

    // แก้ไขฟังก์ชัน validateForm เดิม
    const originalValidateForm = validateForm;
    
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
        
        // ตรวจสอบไฟล์ PDF (ถ้ามีการเลือกไฟล์ใหม่)
        if (pdfFile) {
            if (pdfFile.type !== 'application/pdf') {
                Swal.fire({
                    icon: 'error',
                    title: 'ประเภทไฟล์ไม่ถูกต้อง',
                    text: 'กรุณาเลือกไฟล์ PDF เท่านั้น'
                });
                return false;
            }
            
            if (pdfFile.size > 10 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไฟล์ PDF มีขนาดใหญ่เกินไป',
                    text: 'ขนาดไฟล์ PDF ต้องไม่เกิน 10MB'
                });
                return false;
            }
            
            // ถ้าเปลี่ยน PDF และไม่เลือกรูปหน้าปกใหม่ -> จะสร้างหน้าปกอัตโนมัติ
            if (!coverImage) {
                Swal.fire({
                    title: 'เปลี่ยน PDF แต่ไม่เปลี่ยนหน้าปก',
                    text: 'ระบบจะสร้างหน้าปกใหม่จากหน้าแรกของ PDF ใหม่ให้อัตโนมัติ',
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
                        submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> บันทึกการแก้ไข';
                        submitBtn.disabled = false;
                    }
                });
                
                return false; // หยุดการส่งฟอร์มชั่วคราว
            }
        }
        
        // ตรวจสอบรูปหน้าปก (ถ้ามีการเลือกรูปใหม่)
        if (coverImage) {
            const allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedImageTypes.includes(coverImage.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'ประเภทไฟล์รูปภาพไม่ถูกต้อง',
                    text: 'กรุณาเลือกไฟล์ .JPG, .JPEG หรือ .PNG เท่านั้น'
                });
                return false;
            }
            
            if (coverImage.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไฟล์รูปภาพมีขนาดใหญ่เกินไป',
                    text: 'ขนาดไฟล์รูปภาพต้องไม่เกิน 2MB'
                });
                return false;
            }
        }
        
        // แสดงการโหลด
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> กำลังบันทึก...';
        submitBtn.disabled = true;
        
        return true;
    }

    // เพิ่ม CSS สำหรับ spinner
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

<!-- Fancybox JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    // Initialize Fancybox
    Fancybox.bind('[data-fancybox]', {
        Thumbs: {
            autoStart: false,
        },
        Toolbar: {
            display: {
                left: ["infobar"],
                middle: [
                    "zoomIn",
                    "zoomOut",
                    "toggle1to1",
                    "rotateCCW",
                    "rotateCW",
                    "flipX",
                    "flipY",
                ],
                right: ["slideshow", "download", "thumbs", "close"],
            },
        },
    });
</script>

<style>
    .current-file-container,
    .current-cover-container {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        background-color: #f8f9fa;
        margin-bottom: 15px;
    }
    
    .current-file-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .file-preview {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .file-details h6 {
        margin: 0;
        color: #495057;
    }
    
    .current-cover-preview {
        text-align: center;
    }
    
    .current-cover-img {
        max-width: 200px;
        max-height: 250px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    
    .current-cover-img:hover {
        transform: scale(1.05);
    }
    
    .cover-info {
        margin-top: 10px;
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
    
    .info-container {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
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
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>

<?php
// ฟังก์ชันแปลงขนาดไฟล์สำหรับ PHP
function formatFileSize($bytes) {
    if ($bytes == 0) return '0 B';
    $units = array('B', 'KB', 'MB', 'GB');
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}
?>