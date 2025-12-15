<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลหัวข้อ ITA ประจำปี</h4>
            <form action=" <?php echo site_url('ita_year_backend/edit_topic/' . $rsedit->ita_year_topic_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="form-group row">
                    <div class="col-sm-2 control-label">หัวข้อ</div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_topic_name" required class="form-control" value="<?= $rsedit->ita_year_topic_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">หมายเหตุ</div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_topic_msg" value="<?= $rsedit->ita_year_topic_msg; ?>" class="form-control">
                    </div>
                </div>
                <br>
                <input type="hidden" name="ita_year_topic_ref_id" value="<?= $rsedit->ita_year_topic_ref_id; ?>" class="form-control">
                <input type="hidden" name="ita_year_topic_id" value="<?= $rsedit->ita_year_topic_id; ?>" class="form-control">
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('Ita_year_backend/index_topic/' . $rsedit->ita_year_topic_ref_id); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>