<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลปฏิทินกิจกรรม</h4>
            <form action="<?php echo site_url('calender_backend/edit_calender/' . $rsedit->calender_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รายละเอียด</div>
                    <div class="col-sm-9">
                        <textarea name="calender_detail" id="calender_detail"><?= $rsedit->calender_detail; ?></textarea>
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
                                <input type="datetime-local" name="calender_timeopen" required class="form-control"  value="<?= $rsedit->calender_timeopen; ?>" >
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="calender_timeclose" class="control-label">วันที่สิ้นสุด</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" name="calender_timeclose" required class="form-control" value="<?= $rsedit->calender_timeclose; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <br> -->
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่เริ่มกิจกรรม</div>
                    <div class="col-sm-5">
                        <input type="date" name="calender_date" id="calender_date" class="form-control" value="<?= $rsedit->calender_date; ?>" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่สิ้นสุดกิจกรรม</div>
                    <div class="col-sm-5">
                        <input type="date" name="calender_date_end" id="calender_date_end" class="form-control" value="<?= $rsedit->calender_date_end; ?>" required>
                    </div>
                </div>
                <br>
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