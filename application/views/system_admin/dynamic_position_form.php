<!-- dynamic_position_form.php - ฟอร์มเพิ่ม/แก้ไขข้อมูลตำแหน่ง -->
<div class="container">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>
                    <i class="fas fa-<?= isset($position) && $position ? 'edit' : 'plus' ?> text-<?= isset($position) && $position ? 'warning' : 'success' ?>"></i>
                    <?= isset($position) && $position ? 'แก้ไข' : 'เพิ่ม' ?>ข้อมูล<?= $type->pname ?>
                    <?php if (isset($slot_id)): ?>
                        <span class="badge badge-info">Slot #<?= $slot_id ?></span>
                    <?php endif; ?>
                </h4>
                <div>
                    <a href="<?= site_url('dynamic_position_backend/manage/' . $type->peng) ?>"
                        class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> กลับ
                    </a>
                    <?php if (isset($position) && $position): ?>
                        <button type="button" class="btn btn-danger ml-2"
                            onclick="confirmClearSlot(<?= $slot_id ?? $position->position_id ?>)">
                            <i class="fas fa-trash"></i> ลบข้อมูล
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- แสดงข้อความแจ้งเตือน -->
            <?php if ($this->session->flashdata('save_error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>ผิดพลาด!</strong> ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('validation_errors')): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <strong>กรุณาตรวจสอบข้อมูล!</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($this->session->flashdata('validation_errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- คำแนะนำการใช้งาน -->
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle"></i> คำแนะนำ</h6>
                <ul class="mb-0">
                    <li>ฟิลด์ที่มี <span class="text-danger">*</span> จำเป็นต้องกรอก</li>
                    <li>รูปภาพควรมีขนาดไม่เกิน 5MB และเป็นไฟล์ JPG, PNG, GIF</li>
                    <li>ข้อมูลจะถูกบันทึกในตำแหน่ง Slot #<?= $slot_id ?? 'ใหม่' ?></li>
                </ul>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <form action="<?= $action_url ?>" method="post" enctype="multipart/form-data"
                        class="form-horizontal" onsubmit="return validateForm()">

                        <?php if (isset($fields) && !empty($fields)): ?>
                            <?php foreach ($fields as $field): ?>
                                <div class="form-group row">
                                    <div class="col-sm-3 control-label">
                                        <label for="<?= $field->field_name ?>">
                                            <?= $field->field_label ?>
                                            <?php if ($field->field_required): ?>
                                                <span class="text-danger">*</span>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <?php
                                        $value = '';
                                        if (isset($position) && $position && isset($position->data[$field->field_name])) {
                                            $value = $position->data[$field->field_name];
                                        }
                                        $required = $field->field_required ? 'required' : '';
                                        $field_id = $field->field_name;
                                        ?>

                                        <?php if ($field->field_type === 'text'): ?>
                                            <input type="text"
                                                name="<?= $field->field_name ?>"
                                                id="<?= $field_id ?>"
                                                class="form-control"
                                                value="<?= htmlspecialchars($value) ?>"
                                                <?= $required ?>
                                                <?= $field->field_max_length ? 'maxlength="' . $field->field_max_length . '"' : '' ?>
                                                placeholder="กรอก<?= $field->field_label ?>">

                                        <?php elseif ($field->field_type === 'email'): ?>
                                            <input type="email"
                                                name="<?= $field->field_name ?>"
                                                id="<?= $field_id ?>"
                                                class="form-control"
                                                value="<?= htmlspecialchars($value) ?>"
                                                <?= $required ?>
                                                placeholder="example@email.com">

                                        <?php elseif ($field->field_type === 'tel'): ?>
                                            <input type="tel"
                                                name="<?= $field->field_name ?>"
                                                id="<?= $field_id ?>"
                                                class="form-control"
                                                value="<?= htmlspecialchars($value) ?>"
                                                <?= $required ?>
                                                pattern="[0-9\-\+\s\(\)]*"
                                                placeholder="เช่น 081-234-5678">

                                        <?php elseif ($field->field_type === 'number'): ?>
                                            <input type="number"
                                                name="<?= $field->field_name ?>"
                                                id="<?= $field_id ?>"
                                                class="form-control"
                                                value="<?= htmlspecialchars($value) ?>"
                                                <?= $required ?>
                                                placeholder="กรอก<?= $field->field_label ?>">

                                        <?php elseif ($field->field_type === 'textarea'): ?>
                                            <textarea name="<?= $field->field_name ?>"
                                                id="<?= $field_id ?>"
                                                class="form-control"
                                                rows="4"
                                                <?= $required ?>
                                                <?= $field->field_max_length ? 'maxlength="' . $field->field_max_length . '"' : '' ?>
                                                placeholder="กรอก<?= $field->field_label ?>"><?= htmlspecialchars($value) ?></textarea>

                                        <?php elseif ($field->field_type === 'select'): ?>
                                            <select name="<?= $field->field_name ?>"
                                                id="<?= $field_id ?>"
                                                class="form-control" <?= $required ?>>
                                                <option value="">-- เลือก<?= $field->field_label ?> --</option>
                                                <?php
                                                $options = json_decode($field->field_options, true) ?? [];
                                                foreach ($options as $option):
                                                ?>
                                                    <option value="<?= htmlspecialchars($option) ?>"
                                                        <?= ($value === $option) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($option) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                        <?php elseif ($field->field_type === 'file'): ?>
                                            <?php if (isset($position) && $position && !empty($value)): ?>
                                                <div class="mb-3">
                                                    <label class="form-label">รูปภาพปัจจุบัน:</label><br>
                                                    <?php if (in_array(strtolower(pathinfo($value, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'])): ?>
                                                        <img src="<?= base_url('docs/img/' . $value) ?>"
                                                            style="max-width: 200px; max-height: 200px; object-fit: cover;"
                                                            class="img-thumbnail mb-2">
                                                    <?php else: ?>
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            ไฟล์ปัจจุบันไม่ใช่รูปภาพ: <?= $value ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="mt-2">
                                                        <small class="text-muted">ชื่อไฟล์: <?= $value ?></small>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <input type="file"
                                                name="<?= $field->field_name ?>"
                                                id="<?= $field_id ?>"
                                                class="form-control"
                                                accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/jfif"
                                                onchange="previewFile(this, '<?= $field_id ?>_preview')">
                                            <small class="form-text text-muted">
                                                รองรับเฉพาะรูปภาพ: JPG, JPEG, PNG, GIF, WEBP (สูงสุด 5MB)
                                            </small>
                                            <div id="<?= $field_id ?>_preview" class="mt-2"></div>

                                        <?php endif; ?>

                                        <?php if ($field->field_max_length): ?>
                                            <small class="form-text text-muted char-count-<?= $field_id ?>">
                                                สูงสุด <?= $field->field_max_length ?> ตัวอักษร
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <br>
                            <?php endforeach; ?>

                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                ไม่พบฟิลด์สำหรับประเภทตำแหน่งนี้ กรุณาติดต่อผู้ดูแลระบบ
                            </div>
                        <?php endif; ?>

                        <hr>

                        <div class="form-group row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save"></i> บันทึกข้อมูล
                                </button>
                                <a href="<?= site_url('dynamic_position_backend/manage/' . $type->peng) ?>"
                                    class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-times"></i> ยกเลิก
                                </a>

                                <!-- ปุ่มตัวอย่าง (สำหรับ preview) -->
                                <!-- <button type="button" class="btn btn-info btn-lg ml-2" onclick="previewData()">
                                    <i class="fas fa-eye"></i> ตัวอย่าง
                                </button> -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>

<!-- Modal สำหรับตัวอย่างข้อมูล -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye"></i> ตัวอย่างการแสดงผล
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="previewContent">
                    <!-- จะถูกเติมด้วย JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript สำหรับ validation และ functionality -->
<script>
    function validateForm() {
        // ตรวจสอบฟิลด์ที่จำเป็น
        const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        let firstErrorField = null;

        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                if (!firstErrorField) {
                    firstErrorField = field;
                }
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                text: 'มีฟิลด์ที่จำเป็นยังไม่ได้กรอกข้อมูล'
            });

            if (firstErrorField) {
                firstErrorField.focus();
                firstErrorField.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
            return false;
        }

        // ตรวจสอบขนาดไฟล์
        const fileInputs = document.querySelectorAll('input[type="file"]');
        for (let input of fileInputs) {
            if (input.files.length > 0) {
                const file = input.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB

                // ตรวจสอบประเภทไฟล์
                if (!file.type.startsWith('image/')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ประเภทไฟล์ไม่ถูกต้อง',
                        text: 'กรุณาเลือกไฟล์รูปภาพเท่านั้น (JPG, PNG, GIF, WEBP)'
                    });
                    input.focus();
                    return false;
                }

                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไฟล์ใหญ่เกินไป',
                        text: `ขนาดไฟล์ ${(file.size/1024/1024).toFixed(2)} MB เกินขีดจำกัด 5MB`
                    });
                    input.focus();
                    return false;
                }
            }
        }

        // แสดง loading
        Swal.fire({
            title: 'กำลังบันทึกข้อมูล...',
            html: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        return true;
    }

    // ตัวอย่างไฟล์
    function previewFile(input, previewId) {
        const preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // ตรวจสอบประเภทไฟล์
            if (!file.type.startsWith('image/')) {
                preview.innerHTML = `
                <div class="mt-2">
                    <div class="alert alert-danger p-2">
                        <i class="fas fa-times"></i> ไฟล์ที่เลือกไม่ใช่รูปภาพ กรุณาเลือกไฟล์รูปภาพเท่านั้น
                    </div>
                </div>
            `;
                input.value = ''; // ล้างค่าไฟล์ที่เลือก
                return;
            }

            // ตรวจสอบขนาดไฟล์
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                preview.innerHTML = `
                <div class="mt-2">
                    <div class="alert alert-danger p-2">
                        <i class="fas fa-times"></i> ไฟล์ใหญ่เกินไป (${(file.size/1024/1024).toFixed(2)} MB) กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 5MB
                    </div>
                </div>
            `;
                input.value = ''; // ล้างค่าไฟล์ที่เลือก
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                <div class="mt-2">
                    <img src="${e.target.result}" 
                         style="max-width: 200px; max-height: 200px; object-fit: cover;" 
                         class="img-thumbnail">
                    <div class="mt-1">
                        <small class="text-success">
                            <i class="fas fa-check"></i> ${file.name} (${(file.size/1024/1024).toFixed(2)} MB)
                        </small>
                    </div>
                </div>
            `;
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
        }
    }

    // ตัวอย่างการแสดงผล
    function previewData() {
        const formData = new FormData(document.querySelector('form'));
        let previewHtml = '<div class="card-personnel text-center">';

        // รูปภาพ
        const imageFile = formData.get('image');
        if (imageFile && imageFile.size > 0) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('#previewContent img').src = e.target.result;
            };
            reader.readAsDataURL(imageFile);
            previewHtml += '<img src="" width="150px" height="150px" class="img-thumbnail mb-3" style="object-fit: cover;">';
        } else {
            previewHtml += '<div style="width: 150px; height: 150px; background: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;"><i class="fas fa-user fa-3x text-muted"></i></div>';
        }

        // ข้อมูลอื่นๆ
        const name = formData.get('name') || 'ไม่ระบุชื่อ';
        const position = formData.get('position') || '';
        const phone = formData.get('phone') || '';
        const email = formData.get('email') || '';

        previewHtml += `
        <div>
            <strong style="font-size: 16px;">${name}</strong><br>
            ${position ? position + '<br>' : ''}
            ${phone ? phone + '<br>' : ''}
            ${email ? email : ''}
        </div>
    `;

        previewHtml += '</div>';

        document.getElementById('previewContent').innerHTML = previewHtml;
        $('#previewModal').modal('show');
    }

    // ยืนยันการลบ
    <?php if (isset($position) && $position): ?>

        function confirmClearSlot(slotId) {
            Swal.fire({
                title: 'ยืนยันการลบข้อมูล?',
                text: "ข้อมูลในตำแหน่งนี้จะถูกลบทั้งหมด แต่ตำแหน่งจะยังคงอยู่",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบข้อมูล!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= site_url('dynamic_position_backend/clear_slot/' . $type->peng . '/') ?>" + slotId;
                }
            });
        }
    <?php endif; ?>

    // Auto-resize textarea
    document.addEventListener('DOMContentLoaded', function() {
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(function(textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });

        // Real-time character count
        const fieldsWithMaxLength = document.querySelectorAll('input[maxlength], textarea[maxlength]');
        fieldsWithMaxLength.forEach(function(field) {
            const maxLength = field.getAttribute('maxlength');
            const fieldId = field.getAttribute('id');
            const small = document.querySelector('.char-count-' + fieldId);

            if (small) {
                function updateCount() {
                    const currentLength = field.value.length;
                    const remaining = maxLength - currentLength;
                    small.textContent = `เหลือ ${remaining} ตัวอักษร (สูงสุด ${maxLength})`;

                    if (remaining < 10) {
                        small.className = small.className.replace(/text-\w+/, 'text-danger');
                    } else if (remaining < 50) {
                        small.className = small.className.replace(/text-\w+/, 'text-warning');
                    } else {
                        small.className = small.className.replace(/text-\w+/, 'text-muted');
                    }
                }

                field.addEventListener('input', updateCount);
                updateCount(); // เรียกครั้งแรก
            }
        });

        // Real-time validation
        const allInputs = document.querySelectorAll('input, select, textarea');
        allInputs.forEach(function(input) {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required')) {
                    if (!this.value.trim()) {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                }
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid') && this.value.trim()) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
        });
    });
</script>

<!-- CSS เพิ่มเติม -->
<style>
    .form-control.is-valid {
        border-color: #28a745;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.58-.4.58-.4-.58-.4c-.39-.29-.39-.62 0-.91L6.4 1.55c.39-.29.39-.62 0-.91-.39-.29-1.04-.29-1.43 0L1.95 3.47c-.39.29-.39.62 0 .91z'/%3e%3c/svg%3e");
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6-.9 5.2.9.2.9-5.2-.9-.2z'/%3e%3cpath d='M5.8 8.2h.4v.4h-.4z'/%3e%3c/svg%3e");
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-body {
        padding: 2rem;
    }

    .form-group.row {
        margin-bottom: 1.5rem;
    }

    .control-label {
        font-weight: 600;
        color: #495057;
        padding-top: calc(0.375rem + 1px);
    }

    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1.125rem;
    }

    .img-thumbnail {
        border: 2px solid #dee2e6;
    }

    .alert {
        border: none;
        border-radius: 0.5rem;
    }

    .badge {
        font-size: 0.8em;
    }

    /* Loading animation */
    .swal2-loading {
        border-width: 4px;
    }

    /* Modal */
    .modal-content {
        border-radius: 0.5rem;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .card-personnel {
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        background: #fff;
        min-height: 250px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding: 0 15px;
        }

        .col-md-10 {
            padding: 0;
        }

        .card-body {
            padding: 1rem;
        }

        .btn-lg {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            width: 100%;
        }

        .form-group.row {
            margin-bottom: 1rem;
        }

        .col-sm-3,
        .col-sm-9 {
            padding-left: 0;
            padding-right: 0;
        }
    }
</style>