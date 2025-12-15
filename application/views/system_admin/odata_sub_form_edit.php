<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลฐานข้อมูลเปิดภาครัฐ (Open Data)</h4>
            <form action=" <?php echo site_url('Odata_backend/edit_odata_sub/' . $rsedit->odata_sub_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="form-group row">
                    <div class="col-sm-2 control-label">หัวข้อ</div>
                    <div class="col-sm-5">
                        <input type="text" name="odata_sub_name" required class="form-control" value="<?= $rsedit->odata_sub_name; ?>">
                    </div>
                </div>
                <br>
                <input type="hidden" name="odata_sub_ref_id" value="<?= $rsedit->odata_sub_ref_id; ?>" class="form-control">
                <input type="hidden" name="odata_sub_id" value="<?= $rsedit->odata_sub_id; ?>" class="form-control">
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('Odata_backend/index_odata_sub/' . $rsedit->odata_sub_ref_id); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>