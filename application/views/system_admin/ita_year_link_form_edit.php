<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลลิงค์ ITA ประจำปี</h4>
            <form action=" <?php echo site_url('ita_year_backend/edit_link/' . $rsedit->ita_year_link_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อ </div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_link_name"  class="form-control" value="<?= $rsedit->ita_year_link_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อของลิงค์เพิ่มเติม 1</div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_link_title1"  class="form-control" value="<?= $rsedit->ita_year_link_title1; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ลิงค์เพิ่มเติม 1</div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_link_link1"  class="form-control" value="<?= $rsedit->ita_year_link_link1; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อของลิงค์เพิ่มเติม 2</div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_link_title2"  class="form-control" value="<?= $rsedit->ita_year_link_title2; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ลิงค์เพิ่มเติม 2</div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_link_link2"  class="form-control" value="<?= $rsedit->ita_year_link_link2; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อของลิงค์เพิ่มเติม 3</div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_link_title3"  class="form-control" value="<?= $rsedit->ita_year_link_title3; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ลิงค์เพิ่มเติม 3</div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_link_link3"  class="form-control" value="<?= $rsedit->ita_year_link_link3; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อของลิงค์เพิ่มเติม 4</div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_link_title4"  class="form-control" value="<?= $rsedit->ita_year_link_title4; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ลิงค์เพิ่มเติม 4</div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_link_link4"  class="form-control" value="<?= $rsedit->ita_year_link_link4; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อของลิงค์เพิ่มเติม 5</div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_link_title5"  class="form-control" value="<?= $rsedit->ita_year_link_title5; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ลิงค์เพิ่มเติม 5</div>
                    <div class="col-sm-5">
                        <input type="text" name="ita_year_link_link5"  class="form-control" value="<?= $rsedit->ita_year_link_link5; ?>">
                    </div>
                </div>
                <br>
                <input type="hidden" name="ita_year_link_ref_id" value="<?= $rsedit->ita_year_link_ref_id; ?>" class="form-control">
                <input type="hidden" name="ita_year_link_id" value="<?= $rsedit->ita_year_link_id; ?>" class="form-control">
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('Ita_year_backend/index_link/' . $rsedit->ita_year_link_ref_id); ?>">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>