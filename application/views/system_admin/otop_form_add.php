<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูลผลิตภัณฑ์ชุมชน</h4>
            <form action=" <?php echo site_url('otop_backend/add'); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 col-form-label">ชื่อสินค้า</div>
                    <div class="col-sm-8">
                        <input type="text" name="otop_name" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ราคาสินค้า</div>
                    <div class="col-sm-4">
                        <input type="text" name="otop_price" required class="form-control">
                    </div>
                    <!-- <div class="col-sm-5 text-right"> -->
                    <div class="col-sm-5">
                        <span>บาท</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ประเภทสินค้า</div>
                    <div class="col-sm-8">
                        <input type="text" name="otop_type" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ขนาด</div>
                    <div class="col-sm-4">
                        <input type="text" name="otop_size" class="form-control">
                    </div>
                    <div class="col-sm-5">
                        <span>เซนติเมตร</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">น้ำหนัก</div>
                    <div class="col-sm-4">
                        <input type="number" step="0.001" name="otop_weight" class="form-control">
                    </div>
                    <div class="col-sm-5">
                        <span>กิโลกรัม</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รายละเอียด</div>
                    <div class="col-sm-9">
                        <textarea name="otop_detail" id="otop_detail"></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#otop_detail'), {
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
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ที่ตั้ง</div>
                    <div class="col-sm-8">
                        <input type="text" name="otop_location" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ผู้จำหน่าย</div>
                    <div class="col-sm-8">
                        <input type="text" name="otop_seller" required class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">Facebook</div>
                    <div class="col-sm-8">
                        <input type="text" name="otop_fb" class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เบอร์ติดต่อ</div>
                    <div class="col-sm-4">
                        <input type="text" name="otop_phone" required class="form-control" pattern="\d{9,10}" title="กรุณากรอกเบอร์มือถือเป็นตัวเลข 9 หรือ 10 ตัว">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่อัพโหลด</div>
                    <div class="col-sm-5">
                        <input type="datetime-local" name="otop_date" id="otop_date" class="form-control" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพหน้าปก</div>
                    <div class="col-sm-6">
                        <input type="file" name="otop_img" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพเพิ่มเติม</div>
                    <div class="col-sm-6">
                        <input type="file" name="otop_img_img[]" class="form-control" accept="image/*" multiple>
                        <span class="black-add">สามารถอัพโหลดได้หลายไฟล์</span>
                        <br>
                        <span class="red-add">(เฉพาะไฟล์ .JPG/.JPEG/.jfif/.PNG)</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('otop_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>