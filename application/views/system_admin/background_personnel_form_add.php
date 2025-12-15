<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูลแบนเนอร์บุคลากร</h4>
            <form action=" <?php echo site_url('background_personnel_backend/add_background_personnel'); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อ</div>
                    <div class="col-sm-6">
                        <input type="text" name="background_personnel_name" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ตำแหน่ง</div>
                    <div class="col-sm-6">
                        <input type="text" name="background_personnel_rank" class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">เบอร์มือถือ</div>
                    <div class="col-sm-6">
                        <input type="text" name="background_personnel_phone" class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปที่ 1</div>
                    <div class="col-sm-6">
                        <input type="file" name="background_personnel_img1" class="form-control" accept="image/*" required>
                        <small class="text-muted">รูปหลัก (จำเป็น)</small>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปที่ 2</div>
                    <div class="col-sm-6">
                        <input type="file" name="background_personnel_img2" class="form-control" accept="image/*">
                        <small class="text-muted">รูปเสริม (ไม่จำเป็น)</small>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปที่ 3</div>
                    <div class="col-sm-6">
                        <input type="file" name="background_personnel_img3" class="form-control" accept="image/*">
                        <small class="text-muted">รูปเสริม (ไม่จำเป็น)</small>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('background_personnel_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function validateForm() {
        const img1 = document.getElementsByName('background_personnel_img1')[0];
        if (!img1.files.length) {
            alert('กรุณาเลือกรูปที่ 1 (รูปหลัก)');
            return false;
        }
        return true;
    }
</script>