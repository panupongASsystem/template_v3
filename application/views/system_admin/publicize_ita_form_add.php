<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูลประชาสัมพันธ์ EIT/IIT</h4>
            <form action=" <?php echo site_url('publicize_ita_backend/add_publicize_ita/'); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อ</div>
                    <div class="col-sm-6">
                        <input type="text" name="publicize_ita_name" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">link</div>
                    <div class="col-sm-6">
                        <input type="text" name="publicize_ita_link" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ไฟล์รูป</div>
                    <div class="col-sm-6">
                        <input type="file" name="publicize_ita_img" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>
                
                <!-- เพิ่มส่วนการกำหนดการแสดง -->
                <div class="form-group row">
                    <div class="col-sm-2 control-label">การแสดง</div>
                    <div class="col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="publicize_ita_display_type" id="display_always" value="always" checked onchange="toggleDateFields()">
                            <label class="form-check-label" for="display_always">
                                แสดงตลอด
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="publicize_ita_display_type" id="display_period" value="period" onchange="toggleDateFields()">
                            <label class="form-check-label" for="display_period">
                                กำหนดช่วงเวลาแสดง
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- ส่วนกำหนดวันที่ (ซ่อนไว้ตอนแรก) -->
                <div id="date_fields" style="display: none;">
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-2 control-label">วันที่เริ่มแสดง</div>
                        <div class="col-sm-3">
                            <select name="publicize_ita_start_day" class="form-control" id="start_day">
                                <option value="">เลือกวัน</option>
                                <?php for($i = 1; $i <= 31; $i++): ?>
                                <option value="<?= $i; ?>"><?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select name="publicize_ita_start_month" class="form-control" id="start_month">
                                <option value="">เลือกเดือน</option>
                                <option value="1">มกราคม</option>
                                <option value="2">กุมภาพันธ์</option>
                                <option value="3">มีนาคม</option>
                                <option value="4">เมษายน</option>
                                <option value="5">พฤษภาคม</option>
                                <option value="6">มิถุนายน</option>
                                <option value="7">กรกฎาคม</option>
                                <option value="8">สิงหาคม</option>
                                <option value="9">กันยายน</option>
                                <option value="10">ตุลาคม</option>
                                <option value="11">พฤศจิกายน</option>
                                <option value="12">ธันวาคม</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-2 control-label">วันที่สิ้นสุด</div>
                        <div class="col-sm-3">
                            <select name="publicize_ita_end_day" class="form-control" id="end_day">
                                <option value="">เลือกวัน</option>
                                <?php for($i = 1; $i <= 31; $i++): ?>
                                <option value="<?= $i; ?>"><?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select name="publicize_ita_end_month" class="form-control" id="end_month">
                                <option value="">เลือกเดือน</option>
                                <option value="1">มกราคม</option>
                                <option value="2">กุมภาพันธ์</option>
                                <option value="3">มีนาคม</option>
                                <option value="4">เมษายน</option>
                                <option value="5">พฤษภาคม</option>
                                <option value="6">มิถุนายน</option>
                                <option value="7">กรกฎาคม</option>
                                <option value="8">สิงหาคม</option>
                                <option value="9">กันยายน</option>
                                <option value="10">ตุลาคม</option>
                                <option value="11">พฤศจิกายน</option>
                                <option value="12">ธันวาคม</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('publicize_ita_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDateFields() {
    const displayType = document.querySelector('input[name="publicize_ita_display_type"]:checked').value;
    const dateFields = document.getElementById('date_fields');
    const startDay = document.getElementById('start_day');
    const startMonth = document.getElementById('start_month');
    const endDay = document.getElementById('end_day');
    const endMonth = document.getElementById('end_month');
    
    if (displayType === 'period') {
        dateFields.style.display = 'block';
        startDay.required = true;
        startMonth.required = true;
        endDay.required = true;
        endMonth.required = true;
    } else {
        dateFields.style.display = 'none';
        startDay.required = false;
        startMonth.required = false;
        endDay.required = false;
        endMonth.required = false;
        startDay.value = '';
        startMonth.value = '';
        endDay.value = '';
        endMonth.value = '';
    }
}

function validateForm() {
    const displayType = document.querySelector('input[name="publicize_ita_display_type"]:checked').value;
    
    if (displayType === 'period') {
        const startDay = parseInt(document.getElementById('start_day').value);
        const startMonth = parseInt(document.getElementById('start_month').value);
        const endDay = parseInt(document.getElementById('end_day').value);
        const endMonth = parseInt(document.getElementById('end_month').value);
        
        if (!startDay || !startMonth || !endDay || !endMonth) {
            alert('กรุณาเลือกวันที่และเดือนให้ครบถ้วน');
            return false;
        }
        
        // ตรวจสอบว่าวันที่สิ้นสุดมากกว่าวันที่เริ่มต้น
        const startDate = new Date(2024, startMonth - 1, startDay); // ใช้ปี 2024 เป็นตัวอย่าง
        const endDate = new Date(2024, endMonth - 1, endDay);
        
        // หากข้ามปี (เช่น ธันวาคม ถึง มกราคม)
        if (endMonth < startMonth || (endMonth === startMonth && endDay <= startDay)) {
            endDate.setFullYear(2025); // เพิ่มปี
        }
        
        if (endDate <= startDate) {
            alert('วันที่สิ้นสุดต้องมากกว่าวันที่เริ่มต้น');
            return false;
        }
    }
    
    return true;
}
</script>