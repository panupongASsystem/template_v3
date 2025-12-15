<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลกฎหมายที่เกี่ยวข้องแบบไฟล์</h4>
            <form action=" <?php echo site_url('laws_rl_file_backend/edit/' . $rsedit->laws_rl_file_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อกฎ <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="text" name="laws_rl_file_topic" required class="form-control" value="<?= $rsedit->laws_rl_file_topic; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อไฟล์ <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="text" name="laws_rl_file_name" required class="form-control" value="<?= $rsedit->laws_rl_file_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">วันที่อัพโหลด <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="datetime-local" name="laws_rl_file_date" id="laws_rl_file_date" class="form-control" value="<?= $rsedit->laws_rl_file_date; ?>" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ไฟล์ <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <a class="mt-1" href="<?php echo base_url('docs/file/' . $rsedit->laws_rl_file_file); ?>" download="<?= $rsedit->laws_rl_file_file; ?>">
                            <?= $rsedit->laws_rl_file_file; ?>
                        </a>
                        <br>
                        <input type="file" name="laws_rl_file_file" class="form-control" accept="application/pdf">
                        <span class="red-add">(เฉพาะไฟล์ PDF)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('laws_rl_file_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>