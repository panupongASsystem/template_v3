<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลกรองคำหยาบ</h4>
            <form action="<?php echo site_url('vulgar_backend/edit/' . $rsedit->vulgar_id); ?> " method="post" class="form-horizontal">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ข้อความ</div>
                    <div class="col-sm-9">
                        <input type="text" name="vulgar_com" id="vulgar_com" class="form-control" value="<?= $rsedit->vulgar_com; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('vulgar_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>