<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูลบุคลากร</h4>
            <form action=" <?php echo site_url('personnel_backend/add_Personnel'); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">หมวด</div>
                    <div class="col-sm-5">
                        <select class="form-control" id="personnelGroup" name="personnel_gname">
                            <option value="">เลือกแผนก</option>
                            <?php foreach ($personnelGroup as $personnelGroup) : ?>
                                <option value="<?php echo $personnelGroup->pgroup_gname; ?>"><?php echo $personnelGroup->pgroup_gname; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ส่วนงาน</div>
                    <div class="col-sm-5">
                        <select class="form-control" id="personnelDepartment" name="personnel_dname">
                            <option value="">เลือกส่วนงาน</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อจริง</div>
                    <div class="col-sm-5">
                        <input type="text" name="personnel_name" required class="form-control">
                        <span>กรุณากรอกคำนำหน้า</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">นามสุกล</div>
                    <div class="col-sm-5">
                        <input type="text" name="personnel_lastname" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ตำแหน่ง</div>
                    <div class="col-sm-10">
                        <input type="text" name="personnel_role" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">เบอร์มือถือ</div>
                    <div class="col-sm-4">
                        <input type="number" name="personnel_phone" pattern="\d{10}" title="กรุณากรอกเบอร์มือถือเป็นตัวเลข 10 ตัว" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ไฟล์รูป</div>
                    <div class="col-sm-6">
                        <input type="file" name="personnel_img" class="form-control" required accept="image/*">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('personnel_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>