<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h4>แก้ไขข้อมูลตำแหน่งงาน</h4>
            <form action=" <?php echo site_url('position_backend/editdata') ; ?> " method="post" class="form-horizontal">
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อตำแหน่งงาน</div>
                    <div class="col-sm-4">
                        <input type="text" name="pname" required class="form-control" value="<?php echo $rsedit->pname;?>">
                        <input type="hidden" name="pid" value="<?php echo $rsedit->pid;?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-success" >บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('position_backend/index');?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
    </dov>
</div>