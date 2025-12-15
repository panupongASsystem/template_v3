<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูลตรวจสอบเงินอุดหนุนเด็กแรกเกิด</h4>
            <form action="<?php echo site_url('kid_aw_backend/edit_kid_aw/' . $rsedit->kid_aw_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="form-group row">
                    <div class="col-sm-4 control-label">เลขประจำตัวประชาชนผู้มีสิทธิ</div>
                    <div class="col-sm-8">
                        <input type="text" name="kid_aw_id_num_eligible" class="form-control" pattern="\d{13}" title="กรุณากรอกเลขประจำตัวประชาชนผู้มีสิทธิเป็นตัวเลข 13 หลัก" value="<?= $rsedit->kid_aw_id_num_eligible; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-4 control-label">ชื่อ- สกุลของผู้มีสิทธิ</div>
                    <div class="col-sm-8">
                        <input type="text" name="kid_aw_name_eligible" class="form-control" value="<?= $rsedit->kid_aw_name_eligible; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-4 control-label">เลขประจำตัวประชาชนเจ้าของบัญชี</div>
                    <div class="col-sm-8">
                        <input type="text" name="kid_aw_id_num_owner" class="form-control" pattern="\d{13}" title="กรุณากรอกเลขประจำตัวประชาชนเจ้าของบัญชีเป็นตัวเลข 13 หลัก" value="<?= $rsedit->kid_aw_id_num_owner; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-4 control-label">ชื่อ- สกุลของเจ้าของบัญชี</div>
                    <div class="col-sm-8">
                        <input type="text" name="kid_aw_name_owner" class="form-control" value="<?= $rsedit->kid_aw_name_owner; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-4 control-label">ชื่อหน่วยงาน</div>
                    <div class="col-sm-8">
                        <input type="text" name="kid_aw_agency" class="form-control" value="<?= $rsedit->kid_aw_agency; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-4 control-label">ชื่อธนาคาร</div>
                    <div class="col-sm-8">
                        <input type="text" name="kid_aw_bank" class="form-control" value="<?= $rsedit->kid_aw_bank; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-4 control-label">ประเภทการจ่าย</div>
                    <div class="col-sm-8">
                        <input type="text" name="kid_aw_type_payment" class="form-control" value="<?= $rsedit->kid_aw_type_payment; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-4 control-label">เลขที่บัญชีธนาคาร/เลขบัตร</div>
                    <div class="col-sm-8">
                        <input type="number" name="kid_aw_bank_num" class="form-control" value="<?= $rsedit->kid_aw_bank_num; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-4 control-label">งวดเงินที่จ่าย</div>
                    <div class="col-sm-8">
                        <input type="text" name="kid_aw_period_payment" class="form-control" value="<?= $rsedit->kid_aw_period_payment; ?>">
                    <span class="red-add">เช่น 05/2567</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-4 control-label">จำนวนเงิน</div>
                    <div class="col-sm-8">
                        <input type="number" name="kid_aw_money" class="form-control" step="0.01" min="0" value="<?= $rsedit->kid_aw_money; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-4 control-label">สาเหตุระงับจ่าย</div>
                    <div class="col-sm-8">
                        <input type="text" name="kid_aw_note" class="form-control" value="<?= $rsedit->kid_aw_note; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('kid_aw_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>