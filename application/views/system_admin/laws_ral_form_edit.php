<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลกฎหมายและระเบียบที่เกี่ยวข้อง</h4>
            <form action=" <?php echo site_url('laws_ral_backend/edit/' . $rsedit->laws_ral_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อ <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="text" name="laws_ral_name" required class="form-control" value="<?= $rsedit->laws_ral_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">วันที่อัพโหลด <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="datetime-local" name="laws_ral_date" id="laws_ral_date" class="form-control" value="<?= $rsedit->laws_ral_date; ?>" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ไฟล์ PDF <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <a class="btn btn-info btn-sm mb-2" href="<?php echo base_url('docs/file/' . $rsedit->laws_ral_pdf); ?>" target="_blank">ดูไฟล์เดิม!</a>
                        <br>
                        <input type="file" name="laws_ral_pdf" class="form-control" accept="application/pdf">
                        <span class="red-add">(เฉพาะไฟล์ PDF)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('laws_ral_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>