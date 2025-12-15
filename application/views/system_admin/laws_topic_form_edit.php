<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลชื่อหัวข้อกฎหมาย</h4>
            <form action=" <?php echo site_url('laws_backend/edit_topic/' . $rsedit->laws_topic_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อหัวข้อ</div>
                    <div class="col-sm-6">
                        <input type="text" name="laws_topic_topic" class="form-control" value="<?= $rsedit->laws_topic_topic; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" onclick="history.go(-1);">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>