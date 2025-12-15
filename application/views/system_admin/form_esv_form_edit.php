<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลแบบฟอร์มออนไลน์</h4>
            <form action=" <?php echo site_url('form_esv_backend/edit/' . $rsedit->form_esv_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อ <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <input type="text" name="form_esv_name" required class="form-control" value="<?= $rsedit->form_esv_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ไฟล์ PDF <span class="red-add">*</span></div>
                    <div class="col-sm-6">
                        <a class="btn btn-info btn-sm mb-2" href="<?php echo base_url('docs/file/' . $rsedit->form_esv_file); ?>" target="_blank">ดูไฟล์เดิม!</a>
                        <br>
                        <input type="file" name="form_esv_file" class="form-control"  accept=".pdf, .xls, .xlsx, .doc, .docx, .ppt, .pptx">
                        <span class="red-add">(เฉพาะไฟล์ .pdf .doc .docx .ppt .pptx .xls .xlsx)</span>
                    </div>
                </div>
                <br>
                <input type="hidden" name="form_esv_ref_id" value="<?= $rsedit->form_esv_ref_id; ?>" class="form-control">
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" onclick="goBack()">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>