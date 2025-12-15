<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลกิจการสภา</h4>
            <form action=" <?php echo site_url('operation_aa_backend/edit/' . $rsedit->operation_aa_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เรื่อง</div>
                    <div class="col-sm-9">
                        <input type="text" name="operation_aa_name" id="operation_aa_name" class="form-control" value="<?= $rsedit->operation_aa_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รายละเอียด</div>
                    <div class="col-sm-9">
                        <textarea name="operation_aa_detail" id="operation_aa_detail"><?= $rsedit->operation_aa_detail; ?></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#operation_aa_detail'), {
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
                    <div class="col-sm-3 control-label">วันที่อัพโหลด</div>
                    <div class="col-sm-5">
                        <input type="datetime-local" name="operation_aa_date" id="operation_aa_date" class="form-control" value="<?= $rsedit->operation_aa_date; ?>" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ลิงค์เพิ่มเติม</div>
                    <div class="col-sm-9">
                        <input type="text" name="operation_aa_link" id="operation_aa_link" class="form-control" value="<?= $rsedit->operation_aa_link; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพหน้าปก</div>
                    <div class="col-sm-6">
                        ภาพเก่า <br>
                        <?php if (!empty($rsedit->operation_aa_img)) : ?>
                            <img src="<?= base_url('docs/img/' . $rsedit->operation_aa_img); ?>" width="250px" height="210">
                        <?php else : ?>
                            <img src="<?= base_url('docs/logo.png'); ?>" width="250px" height="210">
                        <?php endif; ?>
                        <br>
                        เลือกใหม่
                        <br>
                        <input type="file" name="operation_aa_img" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพเพิ่มเติม</div>
                    <div class="col-sm-9">
                        <?php if (!empty($rsImg)) { ?>
                            <!-- เพิ่มปุ่มและการควบคุม -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <a href="<?= site_url('images_position/index/tbl_operation_aa_img/' . $rsedit->operation_aa_id . '/' . urlencode('กิจการสภา')); ?>" class="btn btn-primary btn-sm me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-grid align-middle" viewBox="0 0 16 16">
                                            <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z" />
                                        </svg>
                                        <span class="align-middle">จัดตำแหน่งรูปภาพ</span>
                                    </a>
                                    <button type="button" id="delete-selected" class="btn btn-danger btn-sm" disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash align-middle" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
                                        </svg>
                                        <span class="align-middle">ลบรูปที่เลือก</span>
                                    </button>
                                </div>
                                <div>
                                    <button type="button" id="select-all" class="btn btn-secondary btn-sm me-2">เลือกทั้งหมด</button>
                                    <span class="badge bg-secondary"><?= !empty($rsImg) ? count($rsImg) : '0' ?> รูป</span>
                                </div>
                            </div>

                            <!-- รูปภาพที่มีอยู่ -->
                            <div id="image-container" class="row g-2 mb-2">
                                <?php foreach ($rsImg as $img) {
                                    $img_url = base_url('docs/img/' . $img->operation_aa_img_img);
                                    $img_name = $img->operation_aa_img_img;
                                    // ตัดชื่อไฟล์ให้สั้นลงถ้ายาวเกินไป
                                    $short_name = (strlen($img_name) > 10) ? substr($img_name, 0, 8) . '...' : $img_name;
                                ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                                        <div class="image-item">
                                            <div class="position-relative">
                                                <div class="image-checkbox">
                                                    <input type="checkbox" name="selected_images[]" value="<?= $img->operation_aa_img_id; ?>" id="img-<?= $img->operation_aa_img_id; ?>" class="image-select">
                                                    <label for="img-<?= $img->operation_aa_img_id; ?>"></label>
                                                </div>
                                                <a href="<?= $img_url; ?>" data-fancybox="gallery" data-caption="<?= $img_name; ?>">
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
                            <label for="operation_aa_img_img" class="small">เพิ่มรูปภาพใหม่:</label>
                            <input type="file" name="operation_aa_img_img[]" id="operation_aa_img_img" class="form-control form-control-sm" accept="image/*" multiple>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="text-danger small">เฉพาะไฟล์ .JPG/.JPEG/.PNG/.GIF</span>
                                <div id="selected-files-count" class="badge bg-primary" style="display: none;">
                                    0 ไฟล์
                                </div>
                            </div>
                        </div>

                        <!-- แสดงตัวอย่างรูปที่เลือก -->
                        <div id="file-preview" class="row g-1 mt-2"></div>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ไฟล์เอกสารเพิ่มเติม</div>
                    <div class="col-sm-6">
                        <?php if (!empty($rsPdf)) { ?>
                            <!-- เพิ่มปุ่มจัดตำแหน่ง PDF -->
                            <a href="<?= site_url('pdf_position/index/tbl_operation_aa_pdf/' . $rsedit->operation_aa_id . '/' . urlencode('กิจการสภา')); ?>" class="btn btn-primary btn-sm mb-2">
                                <i class="fas fa-sort mr-1"></i> จัดลำดับไฟล์เอกสาร
                            </a>
                            <br>

                            <?php foreach ($rsPdf as $pdf) { ?>
                                <div class="pdf-item mb-2 d-flex align-items-center">
                                    <span class="badge bg-secondary me-2"><?= $pdf->operation_aa_pdf_order > 0 ? $pdf->operation_aa_pdf_order : '-' ?></span>
                                    <a class="btn btn-primary btn-sm me-2" href="<?= base_url('docs/file/' . $pdf->operation_aa_pdf_pdf); ?>" target="_blank">
                                        <i class="fas fa-file-pdf mr-1"></i> <?= (strlen($pdf->operation_aa_pdf_pdf) > 20) ? substr($pdf->operation_aa_pdf_pdf, 0, 18) . '...' : $pdf->operation_aa_pdf_pdf; ?>
                                    </a>
                                    <a class="btn btn-danger btn-sm" href="#" role="button" onclick="confirmDeletePdf(<?= $pdf->operation_aa_pdf_id; ?>, '<?= $pdf->operation_aa_pdf_pdf; ?>');">
                                        <i class="fas fa-trash mr-1"></i>
                                    </a>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="alert alert-light py-2 mb-3">
                                <p class="text-muted mb-0 small">ยังไม่มีไฟล์เอกสาร</p>
                            </div>
                        <?php } ?>
                        <script>
                            function confirmDeletePdf(pdf_id, pdf_name) {
                                Swal.fire({
                                    title: 'คุณแน่ใจหรือไม่?',
                                    text: 'คุณต้องการลบไฟล์ ' + pdf_name + ' ใช่หรือไม่?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'ใช่, ต้องการลบ!',
                                    cancelButtonText: 'ยกเลิก'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // หลังจากคลิกยืนยันให้เรียก Controller ที่ใช้ในการลบไฟล์ PDF
                                        window.location.href = "<?= site_url('operation_aa_backend/del_pdf/'); ?>" + pdf_id;
                                    }
                                });
                            }
                        </script>
                        <input type="file" name="operation_aa_pdf_pdf[]" class="form-control mt-3" accept="application/pdf" multiple>
                        <span class="black-add">สามารถอัพโหลดได้หลายไฟล์</span>
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ PDF)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ไฟล์เอกสารเพิ่มเติม</div>
                    <div class="col-sm-6">
                        <?php if (!empty($rsDoc)) { ?>
                            <!-- เพิ่มปุ่มจัดตำแหน่งไฟล์เอกสาร -->
                            <a href="<?= site_url('doc_position/index/tbl_operation_aa_file/' . $rsedit->operation_aa_id . '/' . urlencode('กิจการสภา')); ?>" class="btn btn-primary btn-sm mb-2">
                                <i class="fas fa-sort mr-1"></i> จัดลำดับไฟล์เอกสาร Office
                            </a>
                            <br>

                            <?php foreach ($rsDoc as $doc) { ?>
                                <div class="doc-item mb-2 d-flex align-items-center">
                                    <span class="badge bg-secondary me-2"><?= $doc->operation_aa_file_order > 0 ? $doc->operation_aa_file_order : '-' ?></span>
                                    <a class="btn btn-info btn-sm me-2" href="<?= base_url('docs/file/' . $doc->operation_aa_file_doc); ?>" target="_blank">
                                        <?php
                                        $file_ext = pathinfo($doc->operation_aa_file_doc, PATHINFO_EXTENSION);
                                        $icon_class = 'fas fa-file';

                                        if (in_array($file_ext, ['doc', 'docx'])) {
                                            $icon_class = 'fas fa-file-word';
                                        } elseif (in_array($file_ext, ['xls', 'xlsx'])) {
                                            $icon_class = 'fas fa-file-excel';
                                        } elseif (in_array($file_ext, ['ppt', 'pptx'])) {
                                            $icon_class = 'fas fa-file-powerpoint';
                                        }
                                        ?>
                                        <i class="<?= $icon_class; ?> mr-1"></i> <?= (strlen($doc->operation_aa_file_doc) > 20) ? substr($doc->operation_aa_file_doc, 0, 18) . '...' : $doc->operation_aa_file_doc; ?>
                                    </a>
                                    <a class="btn btn-danger btn-sm" href="#" role="button" onclick="confirmDeleteDoc(<?= $doc->operation_aa_file_id; ?>, '<?= $doc->operation_aa_file_doc; ?>');">
                                        <i class="fas fa-trash mr-1"></i>
                                    </a>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="alert alert-light py-2 mb-3">
                                <p class="text-muted mb-0 small">ยังไม่มีไฟล์เอกสาร</p>
                            </div>
                        <?php } ?>
                        <script>
                            function confirmDeleteDoc(doc_id, doc_name) {
                                Swal.fire({
                                    title: 'คุณแน่ใจหรือไม่?',
                                    text: 'คุณต้องการลบไฟล์ ' + doc_name + ' ใช่หรือไม่?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'ใช่, ต้องการลบ!',
                                    cancelButtonText: 'ยกเลิก'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // หลังจากคลิกยืนยันให้เรียก Controller ที่ใช้ในการลบไฟล์
                                        window.location.href = "<?= site_url('operation_aa_backend/del_doc/'); ?>" + doc_id;
                                    }
                                });
                            }
                        </script>
                        <input type="file" name="operation_aa_file_doc[]" class="form-control mt-3" accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" multiple>
                        <span class="black-add">สามารถอัพโหลดได้หลายไฟล์</span>
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ .doc .docx .ppt .pptx .xls .xlsx)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('operation_aa_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ตรวจจับการเลือกไฟล์
        const fileInput = document.getElementById('operation_aa_img_img');
        const filePreview = document.getElementById('file-preview');
        const filesCount = document.getElementById('selected-files-count');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                filePreview.innerHTML = '';

                if (this.files.length > 0) {
                    filesCount.style.display = 'inline-block';
                    filesCount.textContent = this.files.length + ' ไฟล์';

                    for (let i = 0; i < this.files.length; i++) {
                        const file = this.files[i];
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const col = document.createElement('div');
                            col.className = 'col-lg-3 col-md-4 col-sm-6 col-6';

                            const previewItem = document.createElement('div');
                            previewItem.className = 'preview-item';

                            const previewWrapper = document.createElement('div');
                            previewWrapper.className = 'preview-wrapper';

                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'img-fluid';

                            const imgName = document.createElement('div');
                            imgName.className = 'preview-name';
                            imgName.title = file.name;
                            imgName.textContent = file.name.length > 10 ? file.name.substring(0, 8) + '...' : file.name;

                            previewWrapper.appendChild(img);
                            previewWrapper.appendChild(imgName);
                            previewItem.appendChild(previewWrapper);
                            col.appendChild(previewItem);
                            filePreview.appendChild(col);
                        };

                        reader.readAsDataURL(file);
                    }
                } else {
                    filesCount.style.display = 'none';
                }
            });
        }

        // จัดการการเลือกรูปภาพและปุ่มลบ
        const checkboxes = document.querySelectorAll('.image-select');
        const deleteSelectedBtn = document.getElementById('delete-selected');
        const selectAllBtn = document.getElementById('select-all');

        // ตรวจสอบสถานะปุ่มลบเมื่อมีการเลือก/ยกเลิกการเลือกรูปภาพ
        function updateDeleteButtonState() {
            const selectedImages = document.querySelectorAll('.image-select:checked');
            if (deleteSelectedBtn) {
                deleteSelectedBtn.disabled = selectedImages.length === 0;
            }

            // อัพเดตสถานะ select all button
            if (checkboxes.length > 0 && selectAllBtn) {
                selectAllBtn.textContent = (selectedImages.length === checkboxes.length) ? 'ยกเลิกเลือกทั้งหมด' : 'เลือกทั้งหมด';
            }

            // เพิ่มหรือลบคลาส selected ให้กับ image-item
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

        // ฟังก์ชันหา parent element ที่มี class ที่กำหนด
        function findClosestParent(element, selector) {
            while (element) {
                if (element.matches && element.matches(selector)) {
                    return element;
                }
                element = element.parentElement;
            }
            return null;
        }

        // เพิ่ม event listener ให้กับ checkbox ทุกตัว
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateDeleteButtonState);
        });

        // เพิ่ม event listener ให้กับปุ่มเลือกทั้งหมด
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                const isAllSelected = document.querySelectorAll('.image-select:checked').length === checkboxes.length;

                checkboxes.forEach(checkbox => {
                    checkbox.checked = !isAllSelected;
                });

                updateDeleteButtonState();
            });
        }

        // เพิ่ม event listener ให้กับปุ่มลบรูปที่เลือก
        if (deleteSelectedBtn) {
            deleteSelectedBtn.addEventListener('click', function() {
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

                // ใช้ SweetAlert2 สำหรับการยืนยันการลบ
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
                        // แสดง loading
                        Swal.fire({
                            title: 'กำลังลบรูปภาพ...',
                            html: 'กรุณารอสักครู่',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // ส่งข้อมูลด้วย fetch API
                        fetch('<?= site_url('operation_aa_backend/delete_multiple_images'); ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    image_ids: imageIds,
                                    operation_aa_id: <?= $rsedit->operation_aa_id; ?>
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Response:', data);
                                if (data.success) {
                                    Swal.fire({
                                        title: 'ลบรูปภาพสำเร็จ!',
                                        html: 'ลบรูปภาพจำนวน <b>' + data.count + ' รูป</b> เรียบร้อยแล้ว',
                                        icon: 'success',
                                        confirmButtonText: 'ตกลง'
                                    }).then(() => {
                                        // รีโหลดหน้าเพื่อแสดงการเปลี่ยนแปลง
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

        // ทำการ initialize สถานะปุ่มลบเมื่อโหลดหน้า
        updateDeleteButtonState();
    });
</script>