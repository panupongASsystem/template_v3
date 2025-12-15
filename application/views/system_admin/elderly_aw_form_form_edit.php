<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลเอกสารเบี้ยผู้สูงอายุ</h4>
            <form action=" <?php echo site_url('elderly_aw_form_backend/edit/' . $rsedit->elderly_aw_form_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เรื่อง</div>
                    <div class="col-sm-9">
                        <input type="text" name="elderly_aw_form_name" id="elderly_aw_form_name" class="form-control" value="<?= $rsedit->elderly_aw_form_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ไฟล์เอกสารเพิ่มเติม</div>
                    <div class="col-sm-6">
                        <?php if (!empty($rsedit->elderly_aw_form_file)) : ?>
                            <a class="btn btn-info btn-sm mb-2" href="<?= base_url('docs/file/' . $rsedit->elderly_aw_form_file); ?>" target="_blank">ดูไฟล์ <?= $rsedit->elderly_aw_form_file; ?></a>
                        <?php endif; ?>
                        <br>
                        <input type="file" name="elderly_aw_form_file" class="form-control mt-1" accept=".pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx ">
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ PDF, Doc, Docx, xls, xlsx, ppt, pptx)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('elderly_aw_form_backend'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>