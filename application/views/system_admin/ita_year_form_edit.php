<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูล ITA ประจำปี : <?= $rsedit->ita_year_year; ?></h4>
            <form action=" <?php echo site_url('ita_year_backend/edit_year/' . $rsedit->ita_year_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ปี</div>
                    <div class="col-sm-6">
                        <input type="text" name="ita_year_year" class="form-control" value="<?= $rsedit->ita_year_year; ?>">
                        <span class="red-add">( ใส่เฉพาะตัวเลข เช่น 2567)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('ita_year_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>