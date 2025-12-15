<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลโครงสร้างบุคลากร</h4>
            <form action="<?php echo site_url('position_backend/edit/' . $rsedit->pid); ?> " method="post" class="form-horizontal">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อ</div>
                    <div class="col-sm-9">
                        <input type="text" name="pname" id="pname" class="form-control" value="<?= $rsedit->pname; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('position_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>