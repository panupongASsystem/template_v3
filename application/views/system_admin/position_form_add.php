<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูลตำแหน่งงาน</h4>
            <form action=" <?php echo site_url('position_backend/adddata') ; ?> " method="post" class="form-horizontal">
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อตำแหน่งงาน</div>
                    <div class="col-sm-6">
                        <input type="text" name="pname" required class="form-control" placeholder="ชื่อตำแหน่งงาน">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success" >บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('position_backend/index');?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>