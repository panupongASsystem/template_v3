<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูลกิจกรรมช่วยเหลือชุมชน</h4>
            <form action=" <?php echo site_url('activity_backend/edit_User_Activity/' . $rsedit->user_activity_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ย่อหน้ากิจกรรม</div>
                    <div class="col-sm-6">
                        <textarea name="user_activity_name" cols="40" rows="5"><?= $rsedit->user_activity_name; ?></textarea>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รายละเอียด</div>
                    <div class="col-sm-8">
                        <textarea name="user_activity_detail" id="user_activity_detail"><?= $rsedit->user_activity_detail; ?></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#user_activity_detail'))
                                .catch(error => {
                                    console.error(error);
                                });
                        </script>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">เบอร์ติดต่อ</div>
                    <div class="col-sm-6">
                        <input type="number" name="user_activity_phone" required class="form-control" max="9999999999" value="<?= $rsedit->user_activity_phone; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปภาพหน้าปก</div>
                    <div class="col-sm-6">
                        ภาพเก่า <br>
                        <img src="<?= base_url('docs/img/' . $rsedit->user_activity_img); ?>" width="250px" height="210">
                        <br>
                        เลือกใหม่
                        <br>
                        <input type="file" name="user_activity_img" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปภาพเพิ่มเติม</div>
                    <div class="col-sm-6">
                        รูปภาพเก่า: <br>
                        <?php foreach ($qimg as $img) { ?>
                            <img src="<?= base_url('docs/img/' . $img->user_activity_img_img); ?>" width="140px" height="100px">&nbsp;
                        <?php } ?>
                        <br>
                        เลือกใหม่: <br>
                        <input type="file" name="user_activity_img_img[]" class="form-control" accept="image/*" multiple>
                        <span class="black-add">สามารถอัพโหลดได้หลายไฟล์</span>
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ .JPG/.JPEG/.jfif/.PNG)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('activity_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>