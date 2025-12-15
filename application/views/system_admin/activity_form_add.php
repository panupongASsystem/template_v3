<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูลข่าวสาร / กิจกรรม</h4>
            <form action=" <?php echo site_url('activity_backend/add_Activity'); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()" id="activityForm">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ย่อหน้างิจกรรม <span class="red-add">*</span></div>
                    <div class="col-sm-9">
						<input type="text" name="activity_name" id="activity_name" class="form-control" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รายละเอียด</div>
                    <div class="col-sm-9">
                        <textarea name="activity_detail" id="activity_detail"></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#activity_detail'), {
                                    toolbar: {
                                        items: [
                                            'undo', 'redo',
                                            '|', 'heading',
                                            '|', 'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor',
                                            '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                                            '|', 'alignment',
                                            '|', 'bulletedList', 'numberedList', 'todoList',
                                            '|', 'horizontalLine',
                                            '|', 'removeFormat',
                                            '|', 'undo', 'redo'
                                        ]
                                    },
                                    shouldNotGroupWhenFull: true
                                })
                                .catch(error => {
                                    console.error(error);
                                });
                        </script>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่อัพโหลด <span class="red-add">*</span></div>
                    <div class="col-sm-4">
                        <input type="datetime-local" name="activity_date" id="activity_date" class="form-control" required>
						
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">แหล่งที่มา</div>
                    <div class="col-sm-9">
                        <input type="text" name="activity_refer" id="activity_refer" class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพหน้าปก <span class="red-add">*</span></div>
                    <div class="col-sm-4">
                        <input type="file" name="activity_img" id="activity_img" class="form-control" accept="image/jpeg,image/jpg,image/png,image/jfif" required onchange="previewCoverImage(this)">
                        <div id="coverPreview" style="margin-top: 10px;"></div>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพเพิ่มเติม <span class="red-add">*</span></div>
                    <div class="col-sm-9">
                        <input type="file" name="activity_imgs[]" id="activity_imgs" class="form-control" accept="image/jpeg,image/jpg,image/png,image/jfif" required multiple onchange="handleMultipleImages(this)">
                        <span class="black-add">สามารถอัพโหลดได้หลายไฟล์</span>
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ .JPG/.JPEG/.jfif/.PNG)</span>
                        
                        <!-- Image Options -->
                        <div id="imageOptions" style="margin-top: 15px; display: none;">
                            <div class="image-options-card">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="control-label-modern">ขนาดรูปภาพ (%)</label>
                                        <input type="range" class="form-control-range modern-slider" id="imageScale" min="30" max="100" value="100" oninput="updateScaleValue(this.value)">
                                        <span class="scale-value" id="scaleValue">100%</span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label-modern">แปลงเป็นไฟล์</label>
                                        <select class="form-control modern-select" id="convertType">
                                            <option value="original">ไฟล์ต้นฉบับ</option>
                                            <option value="image/jpeg">JPEG</option>
                                            <option value="image/png">PNG</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label-modern">หมุนภาพทั้งหมด</label>
                                        <div class="btn-group-modern" role="group">
                                            <button type="button" class="btn btn-modern" onclick="rotateImages(90)" title="หมุนขวา 90°">
                                                <span class="rotate-icon">↻</span> 90°
                                            </button>
                                            <button type="button" class="btn btn-modern" onclick="rotateImages(180)" title="หมุน 180°">
                                                <span class="rotate-icon">↻</span> 180°
                                            </button>
                                            <button type="button" class="btn btn-modern" onclick="rotateImages(270)" title="หมุนซ้าย 90°">
                                                <span class="rotate-icon">↺</span> 90°
                                            </button>
                                            <button type="button" class="btn btn-modern btn-reset" onclick="rotateImages(0)" title="รีเซ็ต">
                                                <span class="rotate-icon">⟲</span>
                                            </button>
                                        </div>
                                        <small class="rotation-display">หมุน: <span id="rotationValue">0°</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Area -->
                        <div id="imagePreviewContainer"></div>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('activity_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ========== Image Options Card ========== */
.image-options-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}

.image-options-card .row {
    margin-left: -8px;
    margin-right: -8px;
}

.image-options-card .row > [class*="col-"] {
    padding-left: 8px;
    padding-right: 8px;
}

.control-label-modern {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 6px;
}

/* Modern Slider */
.modern-slider {
    width: 100%;
    height: 6px;
    border-radius: 3px;
    background: #dee2e6;
    outline: none;
    margin-bottom: 5px;
}

.modern-slider::-webkit-slider-thumb {
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #007bff;
    cursor: pointer;
    transition: all 0.2s ease;
}

.modern-slider::-webkit-slider-thumb:hover {
    background: #0056b3;
    transform: scale(1.1);
}

.modern-slider::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #007bff;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
}

.modern-slider::-moz-range-thumb:hover {
    background: #0056b3;
    transform: scale(1.1);
}

.scale-value {
    display: inline-block;
    font-size: 14px;
    font-weight: 600;
    color: #007bff;
    padding: 2px 8px;
    background: #e7f3ff;
    border-radius: 4px;
}

/* Modern Select */
.modern-select {
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 12px;
    height: 34px;
    transition: all 0.2s ease;
    width: 100%;
}

.modern-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
}

/* Modern Button Group */
.btn-group-modern {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 6px;
}

.btn-modern {
    padding: 6px 8px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background: #ffffff;
    color: #495057;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    white-space: nowrap;
}

.btn-modern:hover {
    background: #007bff;
    border-color: #007bff;
    color: #ffffff;
    transform: translateY(-1px);
}

.btn-modern.btn-reset {
    background: #ffffff;
    border-color: #dc3545;
    color: #dc3545;
}

.btn-modern.btn-reset:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: #ffffff;
}

.rotate-icon {
    font-size: 14px;
    font-weight: bold;
}

.rotation-display {
    display: block;
    text-align: center;
    font-size: 11px;
    color: #6c757d;
    margin-top: 6px;
    font-weight: 500;
}

.rotation-display span {
    color: #007bff;
    font-weight: 600;
}

/* ========== Preview Container - Grid Layout ========== */
#imagePreviewContainer {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

/* Preview Item Card */
.image-preview-item {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.image-preview-item:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

/* Image Container */
.image-preview-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 12px;
    background: #f5f5f5;
}

/* Image Info */
.image-preview-item .image-info {
    font-size: 13px;
    color: #495057;
    line-height: 1.6;
    padding: 8px 0;
}

.image-preview-item .image-info strong {
    display: block;
    color: #212529;
    font-size: 14px;
    margin-bottom: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 600;
}

.image-preview-item .image-info br {
    line-height: 1.8;
}

/* Rotate Controls */
.rotate-controls {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.rotate-controls .btn-group {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 4px;
}

.rotate-controls .btn {
    padding: 6px 8px;
    font-size: 12px;
    border-radius: 6px;
    transition: all 0.2s ease;
    font-weight: 500;
}

.rotate-controls .btn:hover {
    transform: scale(1.05);
}

.rotate-controls .btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
}

.rotate-controls .btn-outline-secondary:hover {
    background: #6c757d;
    color: #ffffff;
}

.rotate-controls .btn-outline-danger {
    border-color: #dc3545;
    color: #dc3545;
}

.rotate-controls .btn-outline-danger:hover {
    background: #dc3545;
    color: #ffffff;
}

.rotation-indicator {
    font-size: 12px;
    color: #007bff;
    font-weight: 600;
    text-align: center;
    padding: 6px 8px;
    background: linear-gradient(135deg, #e7f3ff 0%, #cce5ff 100%);
    border-radius: 6px;
}

/* Cover Preview */
#coverPreview {
    margin-top: 15px;
}

#coverPreview .image-preview-item {
    display: inline-block;
    max-width: 300px;
}

#coverPreview .image-preview-item img {
    max-width: 300px;
    height: auto;
}

/* ========== Responsive ========== */
@media (max-width: 768px) {
    #imagePreviewContainer {
        grid-template-columns: 1fr;
    }
    
    .btn-group-modern {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 769px) and (max-width: 1200px) {
    #imagePreviewContainer {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1201px) {
    #imagePreviewContainer {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* ========== Loading State ========== */
.loading-preview {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    color: #6c757d;
    font-size: 14px;
}

.loading-preview::before {
    content: "";
    width: 20px;
    height: 20px;
    border: 3px solid #e9ecef;
    border-top-color: #007bff;
    border-radius: 50%;
    margin-right: 10px;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let selectedFiles = [];
let processedFiles = [];
let isProcessing = false;
let previewTimeout = null;
let imageRotations = {}; // เก็บมุมการหมุนของแต่ละรูป {index: degrees}

function updateScaleValue(value) {
    console.log('[Scale] Changed to:', value + '%');
    document.getElementById('scaleValue').textContent = value + '%';
}

function rotateImages(degrees) {
    console.log('[Rotate All] Rotating all images by:', degrees + '°');
    
    // อัพเดทมุมหมุนให้ทุกรูป
    selectedFiles.forEach((file, index) => {
        if (degrees === 0) {
            imageRotations[index] = 0; // รีเซ็ต
        } else {
            if (!imageRotations[index]) {
                imageRotations[index] = 0;
            }
            imageRotations[index] = (imageRotations[index] + degrees) % 360;
        }
    });
    
    // อัพเดท UI แสดงมุมหมุน
    document.getElementById('rotationValue').textContent = degrees === 0 ? '0°' : `+${degrees}°`;
    
    // สร้าง preview ใหม่ทั้งหมด
    previewImages();
}

function rotateImage(index, degrees) {
    // บันทึกหรืออัพเดทมุมการหมุนของรูปที่เลือก
    if (!imageRotations[index]) {
        imageRotations[index] = 0;
    }
    
    if (degrees === 0) {
        imageRotations[index] = 0; // รีเซ็ต
    } else {
        imageRotations[index] = (imageRotations[index] + degrees) % 360;
    }
    
    console.log('[Rotate] Image', index, 'set to:', imageRotations[index] + '°');
    
    // อัพเดท preview เฉพาะรูปนั้น
    previewSingleImage(index);
}

function previewCoverImage(input) {
    console.log('[Cover] Preview called');
    const preview = document.getElementById('coverPreview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        console.log('[Cover] File selected:', file.name, 'Size:', (file.size / 1024).toFixed(2), 'KB');
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'image-preview-item';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Cover Preview">
                <div class="image-info">
                    <strong>${file.name}</strong><br>
                    ขนาด: ${(file.size / 1024).toFixed(2)} KB
                </div>
            `;
            preview.appendChild(div);
            console.log('[Cover] Preview rendered');
        };
        
        reader.readAsDataURL(file);
    }
}

function handleMultipleImages(input) {
    console.log('[Multiple] Files selected:', input.files.length);
    selectedFiles = Array.from(input.files);
    imageRotations = {}; // รีเซ็ตมุมการหมุนทั้งหมด
    
    if (selectedFiles.length > 0) {
        console.log('[Multiple] File list:', selectedFiles.map(f => f.name));
        document.getElementById('imageOptions').style.display = 'block';
        previewImages();
    } else {
        console.log('[Multiple] No files selected');
        document.getElementById('imageOptions').style.display = 'none';
        document.getElementById('imagePreviewContainer').innerHTML = '';
    }
}

function previewImages() {
    if (isProcessing) {
        console.log('[Preview] Already processing, skipped');
        return;
    }
    
    clearTimeout(previewTimeout);
    previewTimeout = setTimeout(() => {
        generatePreviews();
    }, 300);
}

function previewSingleImage(index) {
    // Preview รูปเดียวที่ถูกหมุน
    const file = selectedFiles[index];
    const scale = document.getElementById('imageScale').value / 100;
    const convertType = document.getElementById('convertType').value;
    const rotation = imageRotations[index] || 0;
    
    console.log('[Preview Single] Processing file', index, ':', file.name, 'Rotation:', rotation + '°');
    
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            let drawWidth, drawHeight;
            
            if (rotation === 90 || rotation === 270) {
                drawWidth = Math.round(img.height * scale);
                drawHeight = Math.round(img.width * scale);
                canvas.width = drawWidth;
                canvas.height = drawHeight;
            } else {
                drawWidth = Math.round(img.width * scale);
                drawHeight = Math.round(img.height * scale);
                canvas.width = drawWidth;
                canvas.height = drawHeight;
            }
            
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            ctx.save();
            
            if (rotation === 90) {
                ctx.translate(canvas.width, 0);
                ctx.rotate(90 * Math.PI / 180);
                ctx.drawImage(img, 0, 0, drawHeight, drawWidth);
            } else if (rotation === 180) {
                ctx.translate(canvas.width, canvas.height);
                ctx.rotate(180 * Math.PI / 180);
                ctx.drawImage(img, 0, 0, drawWidth, drawHeight);
            } else if (rotation === 270) {
                ctx.translate(0, canvas.height);
                ctx.rotate(270 * Math.PI / 180);
                ctx.drawImage(img, 0, 0, drawHeight, drawWidth);
            } else {
                ctx.drawImage(img, 0, 0, drawWidth, drawHeight);
            }
            
            ctx.restore();
            
            const outputType = convertType === 'original' ? file.type : convertType;
            const previewUrl = canvas.toDataURL(outputType, 0.85);
            
            const base64Length = previewUrl.length - 'data:image/png;base64,'.length;
            const estimatedSize = (base64Length * 0.75) / 1024;
            
            // อัพเดทเฉพาะ div ของรูปนี้
            const previewDiv = document.querySelector(`[data-image-index="${index}"]`);
            if (previewDiv) {
                previewDiv.innerHTML = `
                    <img src="${previewUrl}" alt="Preview ${index + 1}">
                    <div class="image-info">
                        <strong>${file.name}</strong><br>
                        ขนาดเดิม: ${img.width} x ${img.height}<br>
                        ขนาดใหม่: ${canvas.width} x ${canvas.height}<br>
                        Scale: ${(scale * 100).toFixed(0)}%<br>
                        Type: ${outputType}<br>
                        ประมาณ: ${estimatedSize.toFixed(2)} KB
                    </div>
                    <div class="rotate-controls">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateImage(${index}, 90)" title="หมุนขวา 90°">
                                ↻ 90°
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateImage(${index}, 180)" title="หมุน 180°">
                                ↻ 180°
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateImage(${index}, 270)" title="หมุนซ้าย 90°">
                                ↺ 90°
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="rotateImage(${index}, 0)" title="รีเซ็ต">
                                ⟲
                            </button>
                        </div>
                        <div class="rotation-indicator">หมุน: ${rotation}°</div>
                    </div>
                `;
            }
        };
        
        img.src = e.target.result;
    };
    
    reader.readAsDataURL(file);
}

function generatePreviews() {
    console.log('[Preview] Starting preview generation');
    isProcessing = true;
    
    const container = document.getElementById('imagePreviewContainer');
    container.innerHTML = '<div class="loading-preview">กำลังสร้างตัวอย่าง...</div>';
    
    const scale = document.getElementById('imageScale').value / 100;
    const convertType = document.getElementById('convertType').value;
    
    console.log('[Preview] Settings - Scale:', (scale * 100) + '%', 'Convert:', convertType);
    console.log('[Preview] Processing', selectedFiles.length, 'files');
    
    let completed = 0;
    container.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const rotation = imageRotations[index] || 0;
        console.log('[Preview] Processing file', index + 1, ':', file.name, 'Rotation:', rotation + '°');
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                let drawWidth, drawHeight;
                
                if (rotation === 90 || rotation === 270) {
                    drawWidth = Math.round(img.height * scale);
                    drawHeight = Math.round(img.width * scale);
                    canvas.width = drawWidth;
                    canvas.height = drawHeight;
                } else {
                    drawWidth = Math.round(img.width * scale);
                    drawHeight = Math.round(img.height * scale);
                    canvas.width = drawWidth;
                    canvas.height = drawHeight;
                }
                
                console.log('[Preview] File', index + 1, '- Original:', img.width, 'x', img.height, 'Canvas:', canvas.width, 'x', canvas.height, 'Rotation:', rotation + '°');
                
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.save();
                
                if (rotation === 90) {
                    ctx.translate(canvas.width, 0);
                    ctx.rotate(90 * Math.PI / 180);
                    ctx.drawImage(img, 0, 0, drawHeight, drawWidth);
                } else if (rotation === 180) {
                    ctx.translate(canvas.width, canvas.height);
                    ctx.rotate(180 * Math.PI / 180);
                    ctx.drawImage(img, 0, 0, drawWidth, drawHeight);
                } else if (rotation === 270) {
                    ctx.translate(0, canvas.height);
                    ctx.rotate(270 * Math.PI / 180);
                    ctx.drawImage(img, 0, 0, drawHeight, drawWidth);
                } else {
                    ctx.drawImage(img, 0, 0, drawWidth, drawHeight);
                }
                
                ctx.restore();
                
                const outputType = convertType === 'original' ? file.type : convertType;
                const previewUrl = canvas.toDataURL(outputType, 0.85);
                
                const base64Length = previewUrl.length - 'data:image/png;base64,'.length;
                const estimatedSize = (base64Length * 0.75) / 1024;
                
                console.log('[Preview] File', index + 1, '- Estimated size:', estimatedSize.toFixed(2), 'KB');
                
                const div = document.createElement('div');
                div.className = 'image-preview-item';
                div.setAttribute('data-image-index', index);
                div.innerHTML = `
                    <img src="${previewUrl}" alt="Preview ${index + 1}">
                    <div class="image-info">
                        <strong>${file.name}</strong><br>
                        ขนาดเดิม: ${img.width} x ${img.height}<br>
                        ขนาดใหม่: ${canvas.width} x ${canvas.height}<br>
                        Scale: ${(scale * 100).toFixed(0)}%<br>
                        Type: ${outputType}<br>
                        ประมาณ: ${estimatedSize.toFixed(2)} KB
                    </div>
                    <div class="rotate-controls">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateImage(${index}, 90)" title="หมุนขวา 90°">
                                ↻ 90°
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateImage(${index}, 180)" title="หมุน 180°">
                                ↻ 180°
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateImage(${index}, 270)" title="หมุนซ้าย 90°">
                                ↺ 90°
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="rotateImage(${index}, 0)" title="รีเซ็ต">
                                ⟲
                            </button>
                        </div>
                        <div class="rotation-indicator">หมุน: ${rotation}°</div>
                    </div>
                `;
                container.appendChild(div);
                
                completed++;
                console.log('[Preview] Completed', completed, '/', selectedFiles.length);
                
                if (completed === selectedFiles.length) {
                    isProcessing = false;
                    console.log('[Preview] All previews generated');
                }
            };
            
            img.onerror = function() {
                console.error('[Preview] Failed to load image', index + 1);
                completed++;
                if (completed === selectedFiles.length) {
                    isProcessing = false;
                }
            };
            
            img.src = e.target.result;
        };
        
        reader.onerror = function() {
            console.error('[Preview] Failed to read file', index + 1);
            completed++;
            if (completed === selectedFiles.length) {
                isProcessing = false;
            }
        };
        
        reader.readAsDataURL(file);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('[Init] Document loaded');
    
    const scaleInput = document.getElementById('imageScale');
    const convertSelect = document.getElementById('convertType');
    
    if (scaleInput) {
        scaleInput.addEventListener('input', function() {
            updateScaleValue(this.value);
            previewImages();
        });
        console.log('[Init] Scale slider initialized');
    }
    
    if (convertSelect) {
        convertSelect.addEventListener('change', function() {
            console.log('[Convert] Type changed to:', this.value);
            previewImages();
        });
        console.log('[Init] Convert select initialized');
    }
});

document.getElementById('activityForm').addEventListener('submit', function(e) {
    console.log('[Submit] Form submitted');
    
    if (selectedFiles.length > 0) {
        e.preventDefault();
        console.log('[Submit] Processing', selectedFiles.length, 'images before submit');
        
        const scale = document.getElementById('imageScale').value / 100;
        const convertType = document.getElementById('convertType').value;
        
        console.log('[Submit] Settings - Scale:', (scale * 100) + '%', 'Convert:', convertType);
        console.log('[Submit] Image rotations:', imageRotations);
        
        Swal.fire({
            title: 'กำลังประมวลผลรูปภาพ...',
            html: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        processImages(selectedFiles, scale, convertType, imageRotations).then(processedFiles => {
            console.log('[Submit] Processing complete. Files:', processedFiles.length);
            
            processedFiles.forEach((file, index) => {
                console.log('[Submit] Processed file', index + 1, ':', file.name, 'Size:', (file.size / 1024).toFixed(2), 'KB');
            });
            
            const oversizedFiles = processedFiles.filter(f => f.size > 10 * 1024 * 1024);
            if (oversizedFiles.length > 0) {
                console.error('[Submit] Files over 10MB:', oversizedFiles.map(f => f.name));
                Swal.fire({
                    icon: 'error',
                    title: 'ไฟล์ใหญ่เกินไป',
                    html: 'ไฟล์เหล่านี้มีขนาดเกิน 10MB:<br>' + oversizedFiles.map(f => `${f.name} (${(f.size / 1024 / 1024).toFixed(2)} MB)`).join('<br>'),
                    text: 'กรุณาลดขนาดรูปภาพหรือเลือก scale ที่เล็กกว่า'
                });
                return;
            }
            
            const dataTransfer = new DataTransfer();
            
            processedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });
            
            document.getElementById('activity_imgs').files = dataTransfer.files;
            console.log('[Submit] Updated form files');
            
            Swal.close();
            
            console.log('[Submit] Submitting form to server');
            this.submit();
        }).catch(error => {
            console.error('[Submit] Error processing images:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถประมวลผลรูปภาพได้: ' + error.message
            });
        });
    } else {
        console.log('[Submit] No files to process, submitting normally');
    }
});

function getFileExtension(filename) {
    return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2).toLowerCase();
}

function getMimeTypeFromExtension(ext) {
    const mimeTypes = {
        'jpg': 'image/jpeg',
        'jpeg': 'image/jpeg',
        'jfif': 'image/jpeg',
        'png': 'image/png'
    };
    return mimeTypes[ext] || 'image/jpeg';
}

function processImages(files, scale, convertType, rotations) {
    console.log('[Process] Starting image processing');
    return Promise.all(files.map((file, index) => {
        return new Promise((resolve, reject) => {
            const rotation = rotations[index] || 0;
            console.log('[Process] Processing file', index + 1, ':', file.name, 'Rotation:', rotation + '°');
            console.log('[Process] File', index + 1, '- Original type:', file.type);
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const img = new Image();
                
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    let canvasWidth, canvasHeight, drawWidth, drawHeight;
                    
                    if (rotation === 90 || rotation === 270) {
                        drawWidth = Math.round(img.height * scale);
                        drawHeight = Math.round(img.width * scale);
                        canvasWidth = drawWidth;
                        canvasHeight = drawHeight;
                    } else {
                        drawWidth = Math.round(img.width * scale);
                        drawHeight = Math.round(img.height * scale);
                        canvasWidth = drawWidth;
                        canvasHeight = drawHeight;
                    }
                    
                    canvas.width = canvasWidth;
                    canvas.height = canvasHeight;
                    
                    console.log('[Process] File', index + 1, '- Resizing from', img.width, 'x', img.height, 'to', canvasWidth, 'x', canvasHeight);
                    console.log('[Process] File', index + 1, '- Rotation:', rotation + '°');
                    
                    ctx.imageSmoothingEnabled = true;
                    ctx.imageSmoothingQuality = 'high';
                    ctx.save();
                    
                    if (rotation === 90) {
                        ctx.translate(canvas.width, 0);
                        ctx.rotate(90 * Math.PI / 180);
                        ctx.drawImage(img, 0, 0, drawHeight, drawWidth);
                    } else if (rotation === 180) {
                        ctx.translate(canvas.width, canvas.height);
                        ctx.rotate(180 * Math.PI / 180);
                        ctx.drawImage(img, 0, 0, drawWidth, drawHeight);
                    } else if (rotation === 270) {
                        ctx.translate(0, canvas.height);
                        ctx.rotate(270 * Math.PI / 180);
                        ctx.drawImage(img, 0, 0, drawHeight, drawWidth);
                    } else {
                        ctx.drawImage(img, 0, 0, drawWidth, drawHeight);
                    }
                    
                    ctx.restore();
                    
                    let mimeType, newFileName;
                    const originalExt = getFileExtension(file.name);
                    const fileNameWithoutExt = file.name.substring(0, file.name.lastIndexOf('.'));
                    
                    if (convertType === 'original') {
                        mimeType = file.type || getMimeTypeFromExtension(originalExt);
                        newFileName = file.name;
                    } else {
                        mimeType = convertType;
                        const newExt = convertType.split('/')[1];
                        newFileName = fileNameWithoutExt + '.' + (newExt === 'jpeg' ? 'jpg' : newExt);
                    }
                    
                    console.log('[Process] File', index + 1, '- Target MIME type:', mimeType);
                    console.log('[Process] File', index + 1, '- New filename:', newFileName);
                    
                    let quality = 0.92;
                    if (mimeType === 'image/jpeg') {
                        quality = 0.92;
                    }
                    
                    console.log('[Process] File', index + 1, '- Converting with quality', quality);
                    
                    canvas.toBlob(function(blob) {
                        if (!blob) {
                            console.error('[Process] File', index + 1, '- Failed to create blob');
                            reject(new Error('Failed to create blob for ' + file.name));
                            return;
                        }
                        
                        console.log('[Process] File', index + 1, '- Blob created, size:', (blob.size / 1024).toFixed(2), 'KB');
                        console.log('[Process] File', index + 1, '- Blob type:', blob.type);
                        
                        const newFile = new File([blob], newFileName, {
                            type: mimeType,
                            lastModified: Date.now()
                        });
                        
                        console.log('[Process] File', index + 1, '- New File created');
                        console.log('[Process] File', index + 1, '- Name:', newFile.name);
                        console.log('[Process] File', index + 1, '- Type:', newFile.type);
                        console.log('[Process] File', index + 1, '- Size:', (newFile.size / 1024).toFixed(2), 'KB');
                        
                        resolve(newFile);
                    }, mimeType, quality);
                };
                
                img.onerror = function() {
                    console.error('[Process] File', index + 1, '- Failed to load image');
                    reject(new Error('Failed to load image: ' + file.name));
                };
                
                img.src = e.target.result;
            };
            
            reader.onerror = function() {
                console.error('[Process] File', index + 1, '- Failed to read file');
                reject(new Error('Failed to read file: ' + file.name));
            };
            
            reader.readAsDataURL(file);
        });
    }));
}

$(document).ready(function() {
    console.log('[jQuery] Document ready');
    
    <?php if ($this->session->flashdata('save_maxsize')) { ?>
        Swal.fire({
            icon: 'error',
            title: 'ตรวจพบปัญหา',
            text: 'ขนาดรูปภาพต้องไม่เกิน 10MB!',
            footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
        });
    <?php } elseif ($this->session->flashdata('save_error')) { ?>
        Swal.fire({
            icon: 'error',
            title: 'ตรวจพบปัญหา',
            text: 'พื้นที่จัดเก็บไม่เพียงพอ!',
            footer: '<a href="#">ติดต่อผู้ดูแลระบบ?</a>'
        });
    <?php } elseif ($this->session->flashdata('save_success')) { ?>
        Swal.fire({
            icon: 'success',
            title: 'บันทึกสำเร็จ',
            text: 'กิจกรรมถูกบันทึกเรียบร้อยแล้ว!',
        });
    <?php } ?>
});
</script>