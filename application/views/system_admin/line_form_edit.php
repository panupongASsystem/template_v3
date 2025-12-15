<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูล Line oa แจ้งเตือน</h4>
            <form action="<?php echo site_url('line_backend/edit/' . $rsedit->line_id); ?> " method="post" class="form-horizontal">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อไลน์</div>
                    <div class="col-sm-9">
                        <input type="text" name="line_name" id="line_name" class="form-control" value="<?= $rsedit->line_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('line_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>