<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูล System Config</h4>

            <?php if ($this->session->flashdata('save_success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>สำเร็จ!</strong> บันทึกข้อมูลเรียบร้อยแล้ว
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- 🆕 กรณี: Address Field -->
            <?php if (!empty($is_address_field)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>โหมดแก้ไขที่อยู่:</strong> กรุณากรอกข้อมูลให้ครบทั้ง 4 ช่อง (ตำบล, อำเภอ, จังหวัด, รหัสไปรษณีย์) 
                    ระบบจะค้นหารหัสอัตโนมัติเพื่อตรวจสอบ (รหัสจะไม่ถูกบันทึก)
                </div>

                <form action="<?= site_url('system_config_backend/edit/' . $rsedit->id); ?>" method="post" class="form-horizontal" id="addressForm">
                    <br>
                    
                    <!-- ชื่อตำบล -->
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">ชื่อตำบล <span class="text-danger">*</span></div>
                        <div class="col-sm-9">
                            <input type="text" 
                                   name="subdistric" 
                                   id="subdistric" 
                                   class="form-control" 
                                   value="<?= isset($address_data['subdistric']) ? htmlspecialchars($address_data['subdistric']) : '' ?>" 
                                   placeholder="เช่น ในเมือง" 
                                   required>
                        </div>
                    </div>
                    <br>

                    <!-- รหัสตำบล (แสดงผลอย่างเดียว - ไม่บันทึก) -->
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">รหัสตำบล (อัตโนมัติ)</div>
                        <div class="col-sm-9">
                            <input type="text" 
                                   id="subdistric_id" 
                                   class="form-control bg-light" 
                                   value="" 
                                   placeholder="รอข้อมูล..." 
                                   readonly>
                            <small class="text-muted">จะค้นหาอัตโนมัติเมื่อกรอกครบ (ไม่บันทึกลง DB)</small>
                        </div>
                    </div>
                    <br>

                    <!-- ชื่ออำเภอ -->
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">ชื่ออำเภอ <span class="text-danger">*</span></div>
                        <div class="col-sm-9">
                            <input type="text" 
                                   name="district" 
                                   id="district" 
                                   class="form-control" 
                                   value="<?= isset($address_data['district']) ? htmlspecialchars($address_data['district']) : '' ?>" 
                                   placeholder="เช่น เมืองขอนแก่น" 
                                   required>
                        </div>
                    </div>
                    <br>

                    <!-- รหัสอำเภอ (แสดงผลอย่างเดียว - ไม่บันทึก) -->
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">รหัสอำเภอ (อัตโนมัติ)</div>
                        <div class="col-sm-9">
                            <input type="text" 
                                   id="district_id" 
                                   class="form-control bg-light" 
                                   value="" 
                                   placeholder="รอข้อมูล..." 
                                   readonly>
                            <small class="text-muted">จะค้นหาอัตโนมัติเมื่อกรอกครบ (ไม่บันทึกลง DB)</small>
                        </div>
                    </div>
                    <br>

                    <!-- ชื่อจังหวัด -->
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">ชื่อจังหวัด <span class="text-danger">*</span></div>
                        <div class="col-sm-9">
                            <input type="text" 
                                   name="province" 
                                   id="province" 
                                   class="form-control" 
                                   value="<?= isset($address_data['province']) ? htmlspecialchars($address_data['province']) : '' ?>" 
                                   placeholder="เช่น ขอนแก่น" 
                                   required>
                        </div>
                    </div>
                    <br>

                    <!-- รหัสจังหวัด (แสดงผลอย่างเดียว - ไม่บันทึก) -->
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">รหัสจังหวัด (อัตโนมัติ)</div>
                        <div class="col-sm-9">
                            <input type="text" 
                                   id="province_id" 
                                   class="form-control bg-light" 
                                   value="" 
                                   placeholder="รอข้อมูล..." 
                                   readonly>
                            <small class="text-muted">จะค้นหาอัตโนมัติเมื่อกรอกครบ (ไม่บันทึกลง DB)</small>
                        </div>
                    </div>
                    <br>

                    <!-- รหัสไปรษณีย์ -->
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">รหัสไปรษณีย์ <span class="text-danger">*</span></div>
                        <div class="col-sm-9">
                            <input type="text" 
                                   name="zip_code" 
                                   id="zip_code" 
                                   class="form-control" 
                                   value="<?= isset($address_data['zip_code']) ? htmlspecialchars($address_data['zip_code']) : '' ?>" 
                                   placeholder="เช่น 40000" 
                                   maxlength="5" 
                                   pattern="[0-9]{5}" 
                                   required>
                            <small class="text-muted">กรอก 5 หลัก</small>
                        </div>
                    </div>
                    <br>

                    <!-- Warning Container -->
                    <div id="warning-container"></div>

                    <!-- Loading -->
                    <div id="loading-indicator" style="display: none;" class="alert alert-info">
                        <i class="bi bi-hourglass-split"></i> กำลังค้นหารหัส...
                    </div>

                    <!-- 🆕 API Test Result Container -->
                    <div id="api-test-container" style="display: none;"></div>

                    <!-- Buttons -->
                    <div class="form-group row">
                        <div class="col-sm-3 control-label"></div>
                        <div class="col-sm-9">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> บันทึกข้อมูล
                            </button>
                            <a class="btn btn-danger" href="<?= site_url('system_config_backend/address'); ?>" role="button">
                                ยกเลิก
                            </a>
                        </div>
                    </div>
                </form>

                <!-- 🆕 JavaScript สำหรับ Address Form -->
                <script>
                $(document).ready(function() {
                    console.log('🚀 Address Form JavaScript Loaded');

                    let searchTimeout;

                    // ✅ ฟังก์ชันค้นหารหัส (เพื่อแสดงผลและตรวจสอบเท่านั้น - ไม่บันทึก)
                    function searchLocationCodes() {
                        const subdistric = $('#subdistric').val().trim();
                        const district = $('#district').val().trim();
                        const province = $('#province').val().trim();
                        const zip_code = $('#zip_code').val().trim();

                        console.log('📍 Searching:', {subdistric, district, province, zip_code});

                        if (!subdistric || !district || !province || !zip_code) {
                            console.log('⚠️ Not all fields filled');
                            return;
                        }

                        if (zip_code.length !== 5 || !/^\d{5}$/.test(zip_code)) {
                            showWarning('กรุณากรอกรหัสไปรษณีย์ 5 หลัก');
                            return;
                        }

                        $('#loading-indicator').show();
                        $('#warning-container').empty();
                        $('#api-test-container').hide().empty();

                        $.ajax({
                            url: '<?= site_url("System_config_backend/ajax_get_location_codes") ?>',
                            type: 'POST',
                            data: {
                                subdistric: subdistric,
                                district: district,
                                province: province,
                                zip_code: zip_code
                            },
                            dataType: 'json',
                            success: function(response) {
                                console.log('✅ Response:', response);
                                $('#loading-indicator').hide();

                                if (response.status === 'success') {
                                    // แสดงรหัสที่ค้นหาได้ (แต่ไม่บันทึกลง DB)
                                    $('#subdistric_id').val(response.subdistric_id || '');
                                    $('#district_id').val(response.district_id || '');
                                    $('#province_id').val(response.province_id || '');

                                    if (response.warnings && response.warnings.length > 0) {
                                        response.warnings.forEach(function(warning) {
                                            showWarning(warning);
                                        });
                                    } else {
                                        showSuccess('✅ ค้นหารหัสสำเร็จ (แสดงผลเพื่อตรวจสอบ)');
                                    }

                                    // 🆕 แสดงผลการทดสอบ API
                                    if (response.api_test_result) {
                                        showApiTestResult(response.api_test_result);
                                    }

                                    // Highlight ฟิลด์ที่ไม่พบรหัส
                                    $('#subdistric_id').toggleClass('border-warning', !response.subdistric_id);
                                    $('#district_id').toggleClass('border-warning', !response.district_id);
                                    $('#province_id').toggleClass('border-warning', !response.province_id);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('❌ Error:', error);
                                $('#loading-indicator').hide();
                                showError('ไม่สามารถเชื่อมต่อ API ได้');
                            }
                        });
                    }

                    // 🆕 แสดงผลการทดสอบ Population API
                    function showApiTestResult(result) {
                        let html = '';
                        
                        if (result.success) {
                            // ✅ กรณีเชื่อมต่อสำเร็จ
                            html = `
                                <div class="alert alert-success alert-dismissible fade show">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    <h6 class="alert-heading">
                                        <i class="bi bi-check-circle-fill"></i> 
                                        <strong>ทดสอบ API ประชากร: สำเร็จ</strong>
                                    </h6>
                                    <hr>
                                    <p class="mb-2"><strong>📊 ${result.message}</strong></p>
                                    <small class="text-muted">
                                        ⚡ เวลาตอบสนอง: ${result.response_time} ms
                                    </small>`;
                            
                            // แสดงข้อมูลตัวอย่าง
                            if (result.sample_village) {
                                html += `
                                    <hr>
                                    <p class="mb-1"><strong>ตัวอย่างข้อมูลหมู่บ้าน:</strong></p>
                                    <ul class="mb-0" style="font-size: 0.9em;">
                                        <li>ชื่อ: ${result.sample_village.name}</li>
                                        <li>ประชากร: ชาย ${result.sample_village.male} | หญิง ${result.sample_village.female} | รวม ${result.sample_village.total} คน</li>
                                    </ul>`;
                            }
                            
                            html += `</div>`;
                        } else {
                            // ❌ กรณีเชื่อมต่อไม่สำเร็จ
                            html = `
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    <h6 class="alert-heading">
                                        <i class="bi bi-x-circle-fill"></i> 
                                        <strong>ทดสอบ API ประชากร: ล้มเหลว</strong>
                                    </h6>
                                    <hr>
                                    <p class="mb-2"><strong>❌ ${result.message}</strong></p>
                                    <small class="text-muted">
                                        HTTP Status: ${result.http_code} | 
                                        เวลาตอบสนอง: ${result.response_time} ms
                                    </small>
                                </div>`;
                        }
                        
                        $('#api-test-container').html(html).show();
                    }

                    // Event: เมื่อพิมพ์
                    $('#subdistric, #district, #province, #zip_code').on('input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(searchLocationCodes, 800);
                    });

                    // Event: เมื่อออกจาก field
                    $('#subdistric, #district, #province, #zip_code').on('blur', function() {
                        clearTimeout(searchTimeout);
                        searchLocationCodes();
                    });

                    // Validate ก่อน submit (ตรวจสอบเฉพาะว่ากรอกครบหรือไม่)
                    $('#addressForm').on('submit', function(e) {
                        const allFilled = $('#subdistric').val() && $('#district').val() && 
                                        $('#province').val() && $('#zip_code').val();
                        
                        if (!allFilled) {
                            e.preventDefault();
                            alert('❌ กรุณากรอกข้อมูลให้ครบทั้ง 4 ช่อง');
                            return false;
                        }
                        
                        console.log('✅ Form validation passed - submitting only 4 name fields');
                    });

                    function showWarning(msg) {
                        $('#warning-container').append(`
                            <div class="alert alert-warning alert-dismissible fade show">
                                <i class="bi bi-exclamation-triangle"></i> ${msg}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                    }

                    function showSuccess(msg) {
                        $('#warning-container').html(`
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="bi bi-check-circle"></i> ${msg}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                    }

                    function showError(msg) {
                        $('#warning-container').html(`
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-x-circle"></i> ${msg}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                    }

                    // Auto-search on page load
                    if ($('#subdistric').val() && $('#district').val() && $('#province').val() && $('#zip_code').val()) {
                        searchLocationCodes();
                    }
                });
                </script>

            <!-- 🔄 กรณี: Telesales (เดิม - ไม่แก้ไข) -->
            <?php elseif (!empty($is_telesales)): ?>
                <form action="<?= site_url('system_config_backend/edit/' . $rsedit->id); ?>" method="post" class="form-horizontal">
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-2 control-label">Keyword</div>
                        <div class="col-sm-10">
                            <input type="text" name="keyword" class="form-control" value="<?= htmlspecialchars($rsedit->keyword); ?>" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-2 control-label">Value</div>
                        <div class="col-sm-10">
                            <select name="value" class="form-control">
                                <?php foreach ($sales_options as $opt): ?>
                                    <option value="<?= htmlspecialchars($opt['value']); ?>" <?= !empty($opt['selected']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($opt['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-2 control-label">Type</div>
                        <div class="col-sm-10">
                            <input type="text" name="type" class="form-control" value="<?= htmlspecialchars($rsedit->type); ?>" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-2 control-label">Description</div>
                        <div class="col-sm-10">
                            <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($rsedit->description); ?>" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-4 control-label"></div>
                        <div class="col-sm-8">
                            <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                            <a class="btn btn-danger" href="<?= site_url('system_config_backend'); ?>" role="button">ยกเลิก</a>
                        </div>
                    </div>
                </form>

            <!-- 🔄 กรณี: ปกติ (เดิม - ไม่แก้ไข) -->
            <?php else: ?>
                <form action="<?= site_url('system_config_backend/edit/' . $rsedit->id); ?>" method="post" class="form-horizontal">
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-2 control-label">Keyword</div>
                        <div class="col-sm-10">
                            <input type="text" name="keyword" class="form-control" value="<?= htmlspecialchars($rsedit->keyword); ?>" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-2 control-label">Value</div>
                        <div class="col-sm-10">
                            <input type="text" name="value" class="form-control" value="<?= htmlspecialchars($rsedit->value); ?>">
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-2 control-label">Type</div>
                        <div class="col-sm-10">
                            <input type="text" name="type" class="form-control" value="<?= htmlspecialchars($rsedit->type); ?>" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-2 control-label">Description</div>
                        <div class="col-sm-10">
                            <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($rsedit->description); ?>" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-4 control-label"></div>
                        <div class="col-sm-8">
                            <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                            <a class="btn btn-danger" href="<?= site_url('system_config_backend'); ?>" role="button">ยกเลิก</a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>

        </div>
    </div>
</div>