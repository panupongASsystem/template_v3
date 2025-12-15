<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลผลิตภัณฑ์ชุมชน</h4>
            <form action=" <?php echo site_url('otop_backend/edit/' . $rsedit->otop_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 col-form-label">ชื่อสินค้า</div>
                    <div class="col-sm-8">
                        <input type="text" name="otop_name" required class="form-control" value="<?= $rsedit->otop_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ราคาสินค้า</div>
                    <div class="col-sm-4">
                        <input type="text" name="otop_price" required class="form-control" value="<?= $rsedit->otop_price; ?>">
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
                        <input type="text" name="otop_type" required class="form-control" value="<?= $rsedit->otop_type; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ขนาด</div>
                    <div class="col-sm-4">
                        <input type="text" name="otop_size" class="form-control" value="<?= $rsedit->otop_size; ?>">
                    </div>
                    <div class="col-sm-5">
                        <span>เซนติเมตร</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">น้ำหนัก</div>
                    <div class="col-sm-4">
                        <input type="number" step="0.001" name="otop_weight" class="form-control" value="<?= $rsedit->otop_weight; ?>">
                    </div>
                    <div class="col-sm-5">
                        <span>กิโลกรัม</span>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รายละเอียด</div>
                    <div class="col-sm-9">
                        <textarea name="otop_detail" id="otop_detail"><?= $rsedit->otop_detail; ?></textarea>
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
                        <input type="text" name="otop_location" required class="form-control" value="<?= $rsedit->otop_location; ?>">
                    </div>
                </div>
                <br>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ผู้จำหน่าย</div>
                    <div class="col-sm-8">
                        <input type="text" name="otop_seller" required class="form-control" value="<?= $rsedit->otop_seller; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">Facebook</div>
                    <div class="col-sm-8">
                        <input type="text" name="otop_fb" class="form-control" value="<?= $rsedit->otop_fb; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เบอร์ติดต่อ</div>
                    <div class="col-sm-4">
                        <input type="number" name="otop_phone" value="<?= $rsedit->otop_phone; ?>" required class="form-control" pattern="\d{9,10}" title="กรุณากรอกเบอร์มือถือเป็นตัวเลข 9 หรือ 10 ตัว">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่อัพโหลด</div>
                    <div class="col-sm-5">
                        <input type="datetime-local" name="otop_date" id="otop_date" class="form-control" value="<?= $rsedit->otop_date; ?>" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพหน้าปก</div>
                    <div class="col-sm-6">
                        ภาพเก่า <br>
                        <?php if (!empty($rsedit->otop_img)) : ?>
                            <img src="<?= base_url('docs/img/' . $rsedit->otop_img); ?>" width="250px" height="210">
                        <?php else : ?>
                            <img src="<?= base_url('docs/logo.png'); ?>" width="250px" height="210">
                        <?php endif; ?>
                        <br>
                        เลือกใหม่
                        <br>
                        <input type="file" name="otop_img" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพเพิ่มเติม</div>
                    <div class="col-sm-6">
                        รูปภาพเก่า: <br>
                        <?php if (!empty($qimg)) { ?>
                            <?php foreach ($qimg as $img) { ?>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <img src="<?= base_url('docs/img/' . $img->otop_img_img); ?>" width="140px" height="100px">
                                        <a class="btn btn-danger btn-sm mb-2" href="#" role="button" onclick="confirmDeleteImg(<?= $img->otop_img_id; ?>, '<?= $img->otop_img_img; ?>');">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                                            </svg> ลบไฟล์
                                        </a>
                                    </div>
                                </div>
                                <script>
                                    function confirmDeleteImg(file_id, file_name) {
                                        Swal.fire({
                                            title: 'คุณแน่ใจหรือไม่?',
                                            text: 'คุณต้องการลบไฟล์ ' + file_name + ' ใช่หรือไม่?',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'ใช่, ต้องการลบ!',
                                            cancelButtonText: 'ยกเลิก'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                // หลังจากคลิกยืนยันให้เรียก Controller ที่ใช้ในการลบไฟล์ PDF
                                                window.location.href = "<?= site_url('otop_backend/del_img/'); ?>" + file_id;
                                            }
                                        });
                                    }
                                </script>
                            <?php } ?>
                        <?php } ?>
                        เลือกใหม่: <br>
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