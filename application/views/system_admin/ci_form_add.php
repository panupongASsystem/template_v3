<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อข้อมูลชุมชน</h4>
            <form action=" <?php echo site_url('Ci_backend/add'); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อหมู่บ้าน</div>
                    <div class="col-sm-5">
                        <input type="text" name="ci_name" class="form-control" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">จำนวนประชากรทั้งหมด</div>
                    <div class="col-sm-5">
                        <input type="text" name="ci_total" class="form-control" required pattern="\d{1,3}(,\d{3})*" title="กรุณากรอกตัวเลขที่ถูกต้อง" />
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">จำนวนประชากรชาย</div>
                    <div class="col-sm-5">
                        <input type="text" name="ci_man" class="form-control" required pattern="\d{1,3}(,\d{3})*" title="กรุณากรอกตัวเลขที่ถูกต้อง" />
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">จำนวนประชากรหญิง</div>
                    <div class="col-sm-5">
                        <input type="text" name="ci_woman" class="form-control" required pattern="\d{1,3}(,\d{3})*" title="กรุณากรอกตัวเลขที่ถูกต้อง" />
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">จำนวนครัวเรือน</div>
                    <div class="col-sm-5">
                        <input type="text" name="ci_home" class="form-control" pattern="\d{1,3}(,\d{3})*" title="กรุณากรอกตัวเลขที่ถูกต้อง" />
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('Ci_backend/index'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>