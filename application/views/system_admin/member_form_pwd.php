<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูล password</h4>
            <form action=" <?php echo site_url('member_backend/editpwd'); ?> " method="post" class="form-horizontal">
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อตำแหน่งงาน</div>
                    <div class="col-sm-3">
                        <select class="form-control" name="ref_pid" required disabled>
                            <option value="<?php echo $rsedit->ref_pid; ?>"><?php echo $rsedit->pname; ?></option>
                            <option value="">>-เลือกข้อมูล-< <?php foreach ($rspo as $rs) { ?> <option value="<?php echo $rs->pid; ?>">>-<?php echo $rs->pname; ?>-< <?php } ?> </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">username</div>
                    <div class="col-sm-3">
                        <input type="text" name="m_username" class="form-control" required value="<?php echo $rsedit->m_username; ?>" disabled>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">คำนำหน้าชื่อ</div>
                    <div class="col-sm-3">
                        <select class="form-control" name="m_fname" required disabled>
                            <option value="<?php echo $rsedit->m_fname; ?>"><?php echo $rsedit->m_fname; ?></option>
                            <option value="">-เลือกข้อมูล-/option>
                            <option value="นาย">นาย</option>
                            <option value="นาง">นาง</option>
                            <option value="นางสาว">นางสาว</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อ</div>
                    <div class="col-sm-6">
                        <input type="text" name="m_name" class="form-control" required value="<?php echo $rsedit->m_name; ?>" disabled>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">นามสกุล</div>
                    <div class="col-sm-6">
                        <input type="text" name="m_lname" class="form-control" required value="<?php echo $rsedit->m_lname; ?>" disabled>

                        <input type="hidden" name="m_id" class="form-control" required value="<?php echo $rsedit->m_id; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">password</div>
                    <div class="col-sm-3">
                        <input type="text" name="m_password" class="form-control" required minlength="3">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('member_backend/index'); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>