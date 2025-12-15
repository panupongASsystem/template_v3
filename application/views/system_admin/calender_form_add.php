<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูลปฏิทินกิจกรรม</h4>
            <form action=" <?php echo site_url('calender_backend/add_calender'); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <!-- <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">หัวเรื่อง</div>
                    <div class="col-sm-9">
                        <textarea name="calender_name" id="calender_name"></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#calender_name'), {
                                    toolbar: {
                                        items: [
                                            'undo', 'redo',
                                            '|', 'heading',
                                            '|', 'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor',
                                            '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                                            '|', 'alignment',
                                            '|', 'bulletedList', 'numberedList', 'todoList',
                                            '|', 'insertTable', 'horizontalLine',
                                            '|', 'removeFormat', 'insertImage', 'insertVideo', 'insertFile',
                                            '|', 'undo', 'redo'
                                        ]
                                    },
                                    shouldNotGroupWhenFull: true
                                })
                                .catch(error => {
                                    console.error(error);
                                });
                        </script>
                    </div>
                </div> -->
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รายละเอียด</div>
                    <div class="col-sm-9">
                        <textarea name="calender_detail" id="calender_detail"></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#calender_detail'), {
                                    toolbar: {
                                        items: [
                                            'undo', 'redo',
                                            '|', 'heading',
                                            '|', 'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor',
                                            '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                                            '|', 'alignment',
                                            '|', 'bulletedList', 'numberedList', 'todoList',
                                            '|', 'horizontalLine',
                                            '|', 'removeFormat',
                                            '|', 'undo', 'redo'
                                        ]
                                    },
                                    shouldNotGroupWhenFull: true
                                })
                                .catch(error => {
                                    console.error(error);
                                });
                        </script>
                    </div>
                </div>
                <br>
                <!-- <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="calender_timeopen" class="control-label">วันที่เริ่มกิจกรรม</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" name="calender_timeopen" required class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="calender_timeclose" class="control-label">วันที่สิ้นสุด</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" name="calender_timeclose" required class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <br> -->
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่เริ่มกิจกรรม</div>
                    <div class="col-sm-5">
                        <input type="date" name="calender_date" id="calender_date" class="form-control" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่สิ้นสุดกิจกรรม</div>
                    <div class="col-sm-5">
                        <input type="date" name="calender_date_end" id="calender_date_end" class="form-control" required>
                    </div>
                </div>
                <br>
                <!-- <div class="form-group row">
                    <div class="col-sm-3 control-label">แหล่งที่มา</div>
                    <div class="col-sm-9">
                        <input type="text" name="calender_refer" id="calender_refer" class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพหน้าปก</div>
                    <div class="col-sm-6">
                        <input type="file" name="calender_img" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพเพิ่มเติม</div>
                    <div class="col-sm-6">
                        <input type="file" name="calender_imgs[]" class="form-control" accept="image/*" multiple>
                        <span class="black-add">สามารถอัพโหลดได้หลายไฟล์</span>
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ .JPG/.JPEG/.jfif/.PNG)</span>
                    </div>
                </div>
                <br> -->
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('calender_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>