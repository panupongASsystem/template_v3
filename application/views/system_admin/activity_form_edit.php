<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลข่าวสาร / กิจกรรม</h4>
            <form action="<?php echo site_url('activity_backend/edit_Activity/' . $rsedit->activity_id); ?>"
                method="post" class="form-horizontal" enctype="multipart/form-data" id="activityForm">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ย่อหน้ากิจกรรม <span class="red-add">*</span></div>
                    <div class="col-sm-9">
					<input type="text" name="activity_name" id="activity_name" class="form-control" value="<?= $rsedit->activity_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รายละเอียด</div>
                    <div class="col-sm-9">
                        <textarea name="activity_detail"
                            id="activity_detail"><?= $rsedit->activity_detail; ?></textarea>
                        <script>
                            if (typeof ClassicEditor !== 'undefined') {
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
                            }
                        </script>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่อัพโหลด <span class="red-add">*</span></div>
                    <div class="col-sm-5">
                        <input type="datetime-local" name="activity_date" id="activity_date" class="form-control"
                            value="<?= $rsedit->activity_date; ?>" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">แหล่งที่มา</div>
                    <div class="col-sm-9">
                        <input type="text" name="activity_refer" id="activity_refer" class="form-control"
                            value="<?= $rsedit->activity_refer; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพหน้าปก <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        ภาพเก่า <br>
                        <?php if (!empty($rsedit->activity_img)): ?>
                            <img src="<?= base_url('docs/img/' . $rsedit->activity_img); ?>" width="250px" height="210">
                        <?php else: ?>
                            <img src="<?= base_url('docs/logo.png'); ?>" width="250px" height="210">
                        <?php endif; ?>
                        <br>
                        เลือกใหม่
                        <br>
                        <input type="file" name="activity_img" id="activity_img_cover" class="form-control"
                            accept="image/jpeg,image/jpg,image/png,image/jfif" onchange="previewCoverImage(this)">
                        <div id="coverPreview" style="margin-top: 10px;"></div>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพเพิ่มเติม <span class="red-add">*</span></div>
                    <div class="col-sm-9">
                        <!-- ปุ่มจัดการรูปภาพและปุ่มลบที่เลือก -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <a href="<?= site_url('images_position/index/tbl_activity_img/' . $rsedit->activity_id); ?>"
                                    class="btn btn-primary btn-sm me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-grid align-middle" viewBox="0 0 16 16">
                                        <path
                                            d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z" />
                                    </svg>
                                    <span class="align-middle">จัดตำแหน่งรูปภาพ</span>
                                </a>
                                <button type="button" id="delete-selected" class="btn btn-danger btn-sm" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-trash align-middle" viewBox="0 0 16 16">
                                        <path
                                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                                        <path
                                            d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
                                    </svg>
                                    <span class="align-middle">ลบรูปที่เลือก</span>
                                </button>
                            </div>
                            <div>
                                <button type="button" id="select-all"
                                    class="btn btn-secondary btn-sm me-2">เลือกทั้งหมด</button>
                                <span class="badge bg-secondary"><?= !empty($qimg) ? count($qimg) : '0' ?> รูป</span>
                            </div>
                        </div>

                        <!-- รูปภาพที่มีอยู่ -->
                        <?php if (!empty($qimg)) { ?>
                            <div id="image-container" class="row g-2 mb-2">
                                <?php foreach ($qimg as $img) {
                                    $img_url = base_url('docs/img/' . $img->activity_img_img);
                                    $img_name = $img->activity_img_img;
                                    $short_name = (strlen($img_name) > 10) ? substr($img_name, 0, 8) . '...' : $img_name;
                                    ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                                        <div class="image-item">
                                            <div class="position-relative">
                                                <div class="image-checkbox">
                                                    <input type="checkbox" name="selected_images[]"
                                                        value="<?= $img->activity_img_id; ?>"
                                                        id="img-<?= $img->activity_img_id; ?>" class="image-select">
                                                    <label for="img-<?= $img->activity_img_id; ?>"></label>
                                                </div>
                                                <a href="<?= $img_url; ?>" data-fancybox="gallery"
                                                    data-caption="<?= $img_name; ?>">
                                                    <div class="img-wrapper">
                                                        <img src="<?= $img_url; ?>" class="img-fluid" alt="<?= $img_name; ?>">
                                                    </div>
                                                </a>
                                                <div class="img-name"><?= $short_name; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-light py-2 mb-2">
                                <p class="text-muted mb-0 small">ยังไม่มีรูปภาพ</p>
                            </div>
                        <?php } ?>

                        <!-- อัพโหลดรูปภาพใหม่ -->
                        <div class="form-group">
                            <label for="activity_img_img" class="small">เพิ่มรูปภาพใหม่:</label>
                            <input type="file" name="activity_img_img[]" id="activity_img_img"
                                class="form-control form-control-sm" accept="image/jpeg,image/jpg,image/png,image/jfif"
                                multiple onchange="handleMultipleImages(this)">
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="text-danger small">เฉพาะไฟล์ .JPG/.JPEG/.PNG/.JFIF</span>
                                <div id="selected-files-count" class="badge bg-primary" style="display: none;">
                                    0 ไฟล์
                                </div>
                            </div>
                        </div>

                        <!-- Image Options -->
                        <div id="imageOptions" style="margin-top: 15px; display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="small">ขนาดรูปภาพ (%)</label>
                                    <input type="range" class="form-control-range" id="imageScale" min="30" max="100"
                                        value="100" oninput="updateScaleValue(this.value)">
                                    <span id="scaleValue">100%</span>
                                </div>
                                <div class="col-md-6">
                                    <label class="small">แปลงเป็นไฟล์</label>
                                    <select class="form-control form-control-sm" id="convertType">
                                        <option value="original">ไฟล์ต้นฉบับ</option>
                                        <option value="image/jpeg">JPEG</option>
                                        <option value="image/png">PNG</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- แสดงตัวอย่างรูปที่เลือก -->
                        <div id="file-preview" class="row g-2 mt-2"></div>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="button" class="btn btn-success" onclick="submitForm()">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('activity_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .image-item {
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
        height: 100%;
        position: relative;
        background-color: #f8f9fa;
        margin-bottom: 5px;
        transition: border-color 0.3s;
    }

    .image-checkbox {
        position: absolute;
        top: 5px;
        left: 5px;
        z-index: 10;
    }

    .image-checkbox input[type="checkbox"] {
        display: none;
    }

    .image-checkbox label {
        display: block;
        width: 20px;
        height: 20px;
        background-color: #fff;
        border: 2px solid #ddd;
        border-radius: 3px;
        cursor: pointer;
        position: relative;
    }

    .image-checkbox input[type="checkbox"]:checked+label {
        background-color: #007bff;
        border-color: #007bff;
    }

    .image-checkbox input[type="checkbox"]:checked+label:after {
        content: "✓";
        position: absolute;
        top: 0;
        left: 4px;
        color: white;
        font-size: 14px;
    }

    .image-item.selected {
        border: 2px solid #007bff;
    }

    .img-wrapper {
        width: 100%;
        padding-top: 75%;
        position: relative;
        overflow: hidden;
    }

    .img-wrapper img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        background-color: #f8f9fa;
    }

    .img-name {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.6);
        color: white;
        padding: 2px 5px;
        font-size: 10px;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .image-preview-item {
        display: inline-block;
        margin: 10px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: #f9f9f9;
        text-align: center;
    }

    .image-preview-item img {
        max-width: 150px;
        max-height: 150px;
        display: block;
        margin-bottom: 5px;
    }

    .image-preview-item .image-info {
        font-size: 12px;
        color: #666;
    }

    #file-preview .preview-item {
        position: relative;
        margin-bottom: 5px;
    }

    #file-preview .preview-wrapper {
        width: 100%;
        padding-top: 75%;
        position: relative;
        overflow: hidden;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    #file-preview img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        background-color: #f8f9fa;
    }

    #file-preview .preview-name {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.6);
        color: white;
        padding: 2px 5px;
        font-size: 10px;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .rotate-controls {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #e0e0e0;
    }

    .rotate-controls .btn {
        padding: 2px 6px;
        font-size: 10px;
        margin: 1px;
    }

    .rotation-indicator {
        font-size: 10px;
        color: #007bff;
        font-weight: bold;
        margin-top: 4px;
    }

    .btn svg {
        vertical-align: middle;
        margin-top: -3px;
    }

    .align-middle {
        vertical-align: middle;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let selectedFiles = [];
    let isProcessing = false;
    let previewTimeout = null;
    let imageRotations = {};

    console.log('[Edit Form] Script loaded');

    function updateScaleValue(value) {
        console.log('[Scale] Changed to:', value + '%');
        document.getElementById('scaleValue').textContent = value + '%';
    }

    function rotateImage(index, degrees) {
        if (!imageRotations[index]) {
            imageRotations[index] = 0;
        }

        if (degrees === 0) {
            imageRotations[index] = 0;
        } else {
            imageRotations[index] = (imageRotations[index] + degrees) % 360;
        }

        console.log('[Rotate] Image', index, 'set to:', imageRotations[index] + '°');
        previewSingleImage(index);
    }

    function previewSingleImage(index) {
        const file = selectedFiles[index];
        const scale = document.getElementById('imageScale').value / 100;
        const convertType = document.getElementById('convertType').value;
        const rotation = imageRotations[index] || 0;

        console.log('[Preview Single] Processing file', index, ':', file.name, 'Rotation:', rotation + '°');

        const reader = new FileReader();

        reader.onload = function (e) {
            const img = new Image();
            img.onload = function () {
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
                const previewUrl = canvas.toDataURL(outputType, 0.92);

                const base64Length = previewUrl.length - 'data:image/png;base64,'.length;
                const estimatedSize = (base64Length * 0.75) / 1024;

                const previewCol = document.querySelector(`[data-image-index="${index}"]`);
                if (previewCol) {
                    const shortName = file.name.length > 10 ? file.name.substring(0, 8) + '...' : file.name;
                    previewCol.innerHTML = `
                        <div class="preview-item">
                            <div class="preview-wrapper">
                                <img src="${previewUrl}" class="img-fluid">
                                <div class="preview-name" title="${file.name}\nขนาดใหม่: ${canvas.width}x${canvas.height}\nScale: ${(scale * 100).toFixed(0)}%\nประมาณ: ${estimatedSize.toFixed(2)} KB">${shortName}</div>
                            </div>
                            <div class="rotate-controls">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateImage(${index}, 90)" title="หมุนขวา 90°">↻ 90°</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateImage(${index}, 180)" title="หมุน 180°">↻ 180°</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateImage(${index}, 270)" title="หมุนซ้าย 90°">↺ 90°</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="rotateImage(${index}, 0)" title="รีเซ็ต">⟲</button>
                                <div class="rotation-indicator">หมุน: ${rotation}°</div>
                            </div>
                        </div>
                    `;
                }
            };

            img.src = e.target.result;
        };

        reader.readAsDataURL(file);
    }

    function previewCoverImage(input) {
        console.log('[Cover] Preview called');
        const preview = document.getElementById('coverPreview');
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const file = input.files[0];
            console.log('[Cover] File selected:', file.name, 'Size:', (file.size / 1024).toFixed(2), 'KB');

            const reader = new FileReader();

            reader.onload = function (e) {
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
        imageRotations = {};

        const filesCount = document.getElementById('selected-files-count');

        if (selectedFiles.length > 0) {
            console.log('[Multiple] File list:', selectedFiles.map(f => f.name));
            document.getElementById('imageOptions').style.display = 'block';
            filesCount.style.display = 'inline-block';
            filesCount.textContent = selectedFiles.length + ' ไฟล์';
            previewImages();
        } else {
            console.log('[Multiple] No files selected');
            document.getElementById('imageOptions').style.display = 'none';
            filesCount.style.display = 'none';
            document.getElementById('file-preview').innerHTML = '';
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

    function generatePreviews() {
        console.log('[Preview] Starting preview generation');
        isProcessing = true;

        const container = document.getElementById('file-preview');
        container.innerHTML = '<p class="small text-muted">กำลังสร้างตัวอย่าง...</p>';

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

            reader.onload = function (e) {
                const img = new Image();
                img.onload = function () {
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

                    console.log('[Preview] File', index + 1, '- Original:', img.width, 'x', img.height, 'New:', canvas.width, 'x', canvas.height, 'Rotation:', rotation + '°');

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
                    const previewUrl = canvas.toDataURL(outputType, 0.92);

                    const base64Length = previewUrl.length - 'data:image/png;base64,'.length;
                    const estimatedSize = (base64Length * 0.75) / 1024;

                    console.log('[Preview] File', index + 1, '- Estimated size:', estimatedSize.toFixed(2), 'KB');

                    const col = document.createElement('div');
                    col.className = 'col-lg-3 col-md-4 col-sm-6 col-6';
                    col.setAttribute('data-image-index', index);

                    const shortName = file.name.length > 10 ? file.name.substring(0, 8) + '...' : file.name;

                    col.innerHTML = `
                        <div class="preview-item">
                            <div class="preview-wrapper">
                                <img src="${previewUrl}" class="img-fluid">
                                <div class="preview-name" title="${file.name}\nขนาดใหม่: ${canvas.width}x${canvas.height}\nScale: ${(scale * 100).toFixed(0)}%\nประมาณ: ${estimatedSize.toFixed(2)} KB">${shortName}</div>
                            </div>
                            <div class="rotate-controls">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateImage(${index}, 90)" title="หมุนขวา 90°">↻ 90°</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateImage(${index}, 180)" title="หมุน 180°">↻ 180°</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="rotateImage(${index}, 270)" title="หมุนซ้าย 90°">↺ 90°</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="rotateImage(${index}, 0)" title="รีเซ็ต">⟲</button>
                                <div class="rotation-indicator">หมุน: ${rotation}°</div>
                            </div>
                        </div>
                    `;

                    container.appendChild(col);

                    completed++;
                    console.log('[Preview] Completed', completed, '/', selectedFiles.length);

                    if (completed === selectedFiles.length) {
                        isProcessing = false;
                        console.log('[Preview] All previews generated');
                    }
                };

                img.onerror = function () {
                    console.error('[Preview] Failed to load image', index + 1);
                    completed++;
                    if (completed === selectedFiles.length) {
                        isProcessing = false;
                    }
                };

                img.src = e.target.result;
            };

            reader.onerror = function () {
                console.error('[Preview] Failed to read file', index + 1);
                completed++;
                if (completed === selectedFiles.length) {
                    isProcessing = false;
                }
            };

            reader.readAsDataURL(file);
        });
    }

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

                reader.onload = function (e) {
                    const img = new Image();

                    img.onload = function () {
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

                        canvas.toBlob(function (blob) {
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

                    img.onerror = function () {
                        console.error('[Process] File', index + 1, '- Failed to load image');
                        reject(new Error('Failed to load image: ' + file.name));
                    };

                    img.src = e.target.result;
                };

                reader.onerror = function () {
                    console.error('[Process] File', index + 1, '- Failed to read file');
                    reject(new Error('Failed to read file: ' + file.name));
                };

                reader.readAsDataURL(file);
            });
        }));
    }

    function validateForm() {
        console.log('[Validate] Starting form validation');
        const imageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/jfif'];
        const fileInputs = document.querySelectorAll('input[type="file"]');

        for (const input of fileInputs) {
            if (input.files.length > 0) {
                for (const file of input.files) {
                    console.log('[Validate] Checking file:', file.name, 'Type:', file.type);

                    if (input.accept.includes('image/')) {
                        const ext = getFileExtension(file.name);
                        const validExtensions = ['jpg', 'jpeg', 'png', 'jfif'];
                        const isValidType = imageTypes.includes(file.type);
                        const isValidExt = validExtensions.includes(ext);

                        console.log('[Validate] Extension:', ext, 'Valid type:', isValidType, 'Valid ext:', isValidExt);

                        if (!isValidType && !isValidExt) {
                            console.error('[Validate] Invalid file type:', file.type, 'Extension:', ext);
                            Swal.fire({
                                title: 'รูปแบบไฟล์ไม่ถูกต้อง!',
                                html: `ไฟล์ <b>${file.name}</b> ไม่ใช่รูปภาพที่รองรับ<br><small>รองรับเฉพาะ .JPG/.JPEG/.PNG/.JFIF</small>`,
                                icon: 'error',
                                confirmButtonText: 'เข้าใจแล้ว'
                            });
                            return false;
                        }
                    }
                }
            }
        }

        console.log('[Validate] Form validation passed');
        return true;
    }

    function submitForm() {
        console.log('[Submit] Form submission initiated');

        if (!validateForm()) {
            console.log('[Submit] Validation failed');
            return;
        }

        if (selectedFiles.length > 0) {
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

                document.getElementById('activity_img_img').files = dataTransfer.files;
                console.log('[Submit] Updated form files');

                Swal.fire({
                    title: 'กำลังบันทึกข้อมูล...',
                    text: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        document.getElementById('activityForm').submit();
                    }
                });
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
            Swal.fire({
                title: 'กำลังบันทึกข้อมูล...',
                text: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    document.getElementById('activityForm').submit();
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        console.log('[Init] Document loaded');

        const scaleInput = document.getElementById('imageScale');
        const convertSelect = document.getElementById('convertType');

        if (scaleInput) {
            scaleInput.addEventListener('input', function () {
                updateScaleValue(this.value);
                previewImages();
            });
            console.log('[Init] Scale slider initialized');
        }

        if (convertSelect) {
            convertSelect.addEventListener('change', function () {
                console.log('[Convert] Type changed to:', this.value);
                previewImages();
            });
            console.log('[Init] Convert select initialized');
        }

        const checkboxes = document.querySelectorAll('.image-select');
        const deleteSelectedBtn = document.getElementById('delete-selected');
        const selectAllBtn = document.getElementById('select-all');

        function updateDeleteButtonState() {
            const selectedImages = document.querySelectorAll('.image-select:checked');
            if (deleteSelectedBtn) {
                deleteSelectedBtn.disabled = selectedImages.length === 0;
            }

            if (checkboxes.length > 0 && selectAllBtn) {
                selectAllBtn.textContent = (selectedImages.length === checkboxes.length) ? 'ยกเลิกเลือกทั้งหมด' : 'เลือกทั้งหมด';
            }

            checkboxes.forEach(checkbox => {
                const imageItem = findClosestParent(checkbox, '.image-item');
                if (imageItem) {
                    if (checkbox.checked) {
                        imageItem.classList.add('selected');
                    } else {
                        imageItem.classList.remove('selected');
                    }
                }
            });
        }

        function findClosestParent(element, selector) {
            while (element) {
                if (element.matches && element.matches(selector)) {
                    return element;
                }
                element = element.parentElement;
            }
            return null;
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateDeleteButtonState);
        });

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function () {
                const isAllSelected = document.querySelectorAll('.image-select:checked').length === checkboxes.length;

                checkboxes.forEach(checkbox => {
                    checkbox.checked = !isAllSelected;
                });

                updateDeleteButtonState();
            });
        }

        if (deleteSelectedBtn) {
            deleteSelectedBtn.addEventListener('click', function () {
                const selectedImages = document.querySelectorAll('.image-select:checked');

                if (selectedImages.length === 0) {
                    Swal.fire({
                        title: 'คำเตือน!',
                        text: 'กรุณาเลือกรูปภาพที่ต้องการลบ',
                        icon: 'warning',
                        confirmButtonText: 'เข้าใจแล้ว'
                    });
                    return;
                }

                const imageIds = Array.from(selectedImages).map(checkbox => checkbox.value);

                Swal.fire({
                    title: 'ยืนยันการลบรูปภาพ?',
                    html: 'คุณต้องการลบรูปภาพที่เลือกจำนวน <b>' + selectedImages.length + ' รูป</b> ใช่หรือไม่?<br><small class="text-danger">การลบนี้ไม่สามารถยกเลิกได้</small>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'กำลังลบรูปภาพ...',
                            html: 'กรุณารอสักครู่',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('<?= site_url('activity_backend/delete_multiple_images'); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                image_ids: imageIds,
                                activity_id: <?= $rsedit->activity_id; ?>
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Delete Response:', data);
                                if (data.success) {
                                    Swal.fire({
                                        title: 'ลบรูปภาพสำเร็จ!',
                                        html: 'ลบรูปภาพจำนวน <b>' + data.count + ' รูป</b> เรียบร้อยแล้ว',
                                        icon: 'success',
                                        confirmButtonText: 'ตกลง'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'เกิดข้อผิดพลาด!',
                                        text: data.message || 'ไม่สามารถลบรูปภาพได้',
                                        icon: 'error',
                                        confirmButtonText: 'ตกลง'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'เกิดข้อผิดพลาด!',
                                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                                    icon: 'error',
                                    confirmButtonText: 'ตกลง'
                                });
                            });
                    }
                });
            });
        }

        updateDeleteButtonState();

        <?php if ($this->session->flashdata('save_success')) { ?>
            Swal.fire({
                title: 'บันทึกข้อมูลสำเร็จ!',
                text: 'ข้อมูลได้รับการบันทึกเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        <?php } ?>

        <?php if ($this->session->flashdata('save_error')) { ?>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        <?php } ?>

        <?php if ($this->session->flashdata('del_success')) { ?>
            Swal.fire({
                title: 'ลบข้อมูลสำเร็จ!',
                text: 'ข้อมูลถูกลบเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        <?php } ?>

        <?php if ($this->session->flashdata('del_error')) { ?>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: '<?= $this->session->flashdata('del_error'); ?>',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        <?php } ?>
    });
</script>
