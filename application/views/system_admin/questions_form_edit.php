<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลคำถามที่พบบ่อย</h4>
            <form action=" <?php echo site_url('Questions_backend/edit/' . $rsedit->questions_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">คำถาม</div>
                    <div class="col-sm-9">
                        <input type="text" name="questions_ask" id="questions_ask" class="form-control" value="<?= $rsedit->questions_ask; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">คำตอบ</div>
                    <div class="col-sm-9">
                        <input type="text" name="questions_reply" id="questions_reply" class="form-control" value="<?= $rsedit->questions_reply; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('Questions_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>