<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูลกฎหมายที่ประเมินกรณี รมว.มท.รักษาการร่วม</h4>
            <form action="<?php echo site_url('Laws_ec_backend/add'); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อ <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="text" name="laws_ec_name" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">วันที่อัพโหลด <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="datetime-local" name="laws_ec_date" id="laws_ec_date" class="form-control" required>
                    </div>
                </div>
                <br>
                <!-- <div class="form-group row">
                    <div class="col-sm-2 control-label">ไฟล์ <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="file" name="laws_ec_pdf" class="form-control" accept="application/pdf" required>
                        <span class="red-add">(เฉพาะไฟล์ PDF)</span>
                    </div>
                </div>
                <br> -->
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('laws_ec_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>