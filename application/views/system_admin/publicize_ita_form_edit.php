<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลประชาสัมพันธ์ EIT/IIT</h4>
            <form action=" <?php echo site_url('publicize_ita_backend/edit_publicize_ita/' . $rsedit->publicize_ita_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อ</div>
                    <div class="col-sm-6">
                        <input type="text" name="publicize_ita_name" required class="form-control" value="<?= $rsedit->publicize_ita_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">link</div>
                    <div class="col-sm-6">
                        <input type="text" name="publicize_ita_link" required class="form-control" value="<?= $rsedit->publicize_ita_link; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ไฟล์รูป</div>
                    <div class="col-sm-6">
                        ภาพเก่า <br>
                        <img src="<?= base_url('docs/img/' . $rsedit->publicize_ita_img); ?>" width="220px" height="180">
                        <br>
                        เลือกใหม่
                        <br>
                        <input type="file" name="publicize_ita_img" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>
                
                <!-- เพิ่มส่วนการกำหนดการแสดง -->
                <div class="form-group row">
                    <div class="col-sm-2 control-label">การแสดง</div>
                    <div class="col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="publicize_ita_display_type" id="display_always" value="always" 
                                <?= (isset($rsedit->publicize_ita_display_type) && $rsedit->publicize_ita_display_type == 'always') || !isset($rsedit->publicize_ita_display_type) ? 'checked' : ''; ?> 
                                onchange="toggleDateFields()">
                            <label class="form-check-label" for="display_always">
                                แสดงตลอด
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="publicize_ita_display_type" id="display_period" value="period" 
                                <?= (isset($rsedit->publicize_ita_display_type) && $rsedit->publicize_ita_display_type == 'period') ? 'checked' : ''; ?> 
                                onchange="toggleDateFields()">
                            <label class="form-check-label" for="display_period">
                                กำหนดช่วงเวลาแสดง
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- ส่วนกำหนดวันที่ -->
                <div id="date_fields" style="display: <?= (isset($rsedit->publicize_ita_display_type) && $rsedit->publicize_ita_display_type == 'period') ? 'block' : 'none'; ?>;">
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-2 control-label">วันที่เริ่มแสดง</div>
                        <div class="col-sm-3">
                            <select name="publicize_ita_start_day" class="form-control" id="start_day">
                                <option value="">เลือกวัน</option>
                                <?php for($i = 1; $i <= 31; $i++): ?>
                                <option value="<?= $i; ?>" <?= (isset($rsedit->publicize_ita_start_day) && $rsedit->publicize_ita_start_day == $i) ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select name="publicize_ita_start_month" class="form-control" id="start_month">
                                <option value="">เลือกเดือน</option>
                                <?php 
                                $months = array(
                                    1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
                                    5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
                                    9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
                                );
                                foreach($months as $num => $name): ?>
                                <option value="<?= $num; ?>" <?= (isset($rsedit->publicize_ita_start_month) && $rsedit->publicize_ita_start_month == $num) ? 'selected' : ''; ?>><?= $name; ?></option>
                                <?php endforeach; ?>
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
                                <option value="<?= $i; ?>" <?= (isset($rsedit->publicize_ita_end_day) && $rsedit->publicize_ita_end_day == $i) ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select name="publicize_ita_end_month" class="form-control" id="end_month">
                                <option value="">เลือกเดือน</option>
                                <?php foreach($months as $num => $name): ?>
                                <option value="<?= $num; ?>" <?= (isset($rsedit->publicize_ita_end_month) && $rsedit->publicize_ita_end_month == $num) ? 'selected' : ''; ?>><?= $name; ?></option>
                                <?php endforeach; ?>
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
        const startDate = new Date(2024, startMonth - 1, startDay);
        const endDate = new Date(2024, endMonth - 1, endDay);
        
        // หากข้ามปี (เช่น ธันวาคม ถึง มกราคม)
        if (endMonth < startMonth || (endMonth === startMonth && endDay <= startDay)) {
            endDate.setFullYear(2025);
        }
        
        if (endDate <= startDate) {
            alert('วันที่สิ้นสุดต้องมากกว่าวันที่เริ่มต้น');
            return false;
        }
    }
    
    return true;
}

// เรียกใช้ฟังก์ชันเมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function() {
    toggleDateFields();
});
</script>