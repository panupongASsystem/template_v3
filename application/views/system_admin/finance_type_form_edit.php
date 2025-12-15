<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลงานการเงินและการบัญชี</h4>
            <form action="<?php echo site_url('finance_backend/edit_type/' . $rs_type->finance_type_id); ?> " method="post" class="form-horizontal">
                <div class="form-group row">
                    <div class="col-sm-2 control-label">หัวข้อ <span class="red-add">*</span></div>
                    <div class="col-sm-12">
                        <input type="text" name="finance_type_name" required class="form-control" value="<?= $rs_type->finance_type_name; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12 control-label">วันที่อัพโหลด <span class="red-add">*</span></div>
                    <br>
                    <div class="col-sm-5">
                        <input type="datetime-local" name="finance_type_date" class="form-control" required value="<?= $rs_type->finance_type_date; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('finance_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>