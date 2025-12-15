<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลข่าวด่วน</h4>
            <form action=" <?php echo site_url('HotNews_backend/edit_hotNews/' . $rsedit->hotNews_id); ?> " method="post" class="form-horizontal">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รายละเอียด</div>
                    <div class="col-sm-10">
                        <textarea name="hotNews_text" id="hotNews_text" class="form-control large-textarea"><?= $rsedit->hotNews_text; ?></textarea>
                    </div>
                </div>

                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('HotNews_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>