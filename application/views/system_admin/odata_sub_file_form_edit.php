<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลฐานข้อมูลเปิดภาครัฐ (Open Data)</h4>
            <form action=" <?php echo site_url('odata_backend/edit_odata_sub_file/' . $rsedit->odata_sub_file_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อ </div>
                    <div class="col-sm-7">
                        <input type="text" name="odata_sub_file_name" class="form-control" value="<?= $rsedit->odata_sub_file_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ไฟล์เอกสาร</div>
                    <div class="col-sm-7">
                        <?php if (!empty($rsedit->odata_sub_file_name)) { ?>
                            <a class="btn btn-info btn-sm mb-2" href="<?= base_url('docs/file/' . $rsedit->odata_sub_file_doc); ?>" target="_blank">ดูไฟล์เดิม</a>
                        <?php } ?>
                        <input type="file" name="odata_sub_file_doc" class="form-control" accept=".pdf, .docx, .xls, .doc, .xlsx, .ppt, .pptx">
                    <span class="red-add">( ใส่เฉพาะตัวไฟล์ pdf, doc, docx, xls, xlsx, ppt, pptx)</span>
                    </div>
                </div>
                <br>
                <input type="hidden" name="odata_sub_file_ref_id" value="<?= $rsedit->odata_sub_file_ref_id; ?>" class="form-control">
                <input type="hidden" name="odata_sub_file_id" value="<?= $rsedit->odata_sub_file_id; ?>" class="form-control">
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('Odata_backend/index_odata_sub_file/' . $rsedit->odata_sub_file_ref_id); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>