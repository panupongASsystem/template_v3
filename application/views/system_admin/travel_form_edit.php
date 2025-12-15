<style>
    /* เพิ่มสไตล์ของแผนที่ */
    #map {
        height: 400px;
        width: 100%;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลสถานที่สำคัญ-ท่องเที่ยว</h4>
            <form action=" <?php echo site_url('travel_backend/edit_Travel/' . $rsedit->travel_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อสถานที่</div>
                    <div class="col-sm-6">
                        <input type="text" name="travel_name" required class="form-control" value="<?= $rsedit->travel_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รายละเอียด</div>
                    <div class="col-sm-9">
                        <textarea name="travel_detail" id="travel_detail"><?= $rsedit->travel_detail; ?></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#travel_detail'), {
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
                    <div class="col-sm-9">
                        <input type="text" name="travel_location" required class="form-control" value="<?= $rsedit->travel_location; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เวลาเปิด-ปิด</div>
                    <div class="col-sm-4">
                        <input type="time" name="travel_timeopen" required class="form-control" value="<?= $rsedit->travel_timeopen; ?>">
                    </div>
                    ถึง
                    <div class="col-sm-4">
                        <input type="time" name="travel_timeclose" required class="form-control" value="<?= $rsedit->travel_timeclose; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่ทำการ</div>
                    <div class="col-sm-3">
                        <select class="form-control" name="travel_day" required>
                            <option value="<?= $rsedit->travel_day; ?>"><?= $rsedit->travel_day; ?></option>
                            <option value="">เลือกข้อมูล</option>
                            <option value="ทุกวัน">ทุกวัน</option>
                            <option value="จันทร์-วันศุกร์">วันจันทร์-วันศุกร์</option>
                            <option value="วันเสาร์-วันอาทิตย์">วันเสาร์-วันอาทิตย์</option>
                            <option value="หยุดวันจันทร์">หยุดทุกวันจันทร์</option>
                            <option value="หยุดทุกวันอังคาร">หยุดทุกวันอังคาร</option>
                            <option value="หยุดทุกวันพุธ">หยุดทุกวันพุธ</option>
                            <option value="หยุดทุกวันพฤหัสบดี">หยุดทุกวันพฤหัสบดี</option>
                            <option value="หยุดทุกวันวันศุกร์">หยุดทุกวันวันศุกร์</option>
                            <option value="หยุดทุกวันเสาร์">หยุดทุกวันเสาร์</option>
                            <option value="หยุดทุกวันอาทิตย์">หยุดทุกวันอาทิตย์</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เบอร์ติดต่อ</div>
                    <div class="col-sm-6">
                        <input type="text" name="travel_phone" required class="form-control" pattern="\d{9,10}" title="กรุณากรอกเบอร์มือถือเป็นตัวเลข 9 หรือ 10 ตัว" value="<?= $rsedit->travel_phone; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <div class="col-sm-3 control-label">Link youtube</div>
                    <div class="col-sm-10">
                        <input type="text" name="travel_youtube" class="form-control" value="<?= $rsedit->travel_youtube; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <div class="col-sm-3 control-label">แหล่งที่มา</div>
                    <div class="col-sm-10">
                        <input type="text" name="travel_refer" class="form-control" value="<?= $rsedit->travel_refer; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่อัพโหลด</div>
                    <div class="col-sm-5">
                        <input type="datetime-local" name="travel_date" id="travel_date" class="form-control" value="<?= $rsedit->travel_date; ?>" required>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <div class="col-sm-3 control-label">Link Google map</div>
                    <div class="col-sm-10">
                        <input type="text" name="travel_map" class="form-control" value="<?= $rsedit->travel_map; ?>">
                    </div>
                </div>
                <!-- <div id="map"></div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ละติจูด</div>
                    <div class="col-sm-6">
                        <input type="text" name="travel_lat" id="travel_lat" class="form-control" required value="<?php echo $rsedit->travel_lat; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ลองจิจูด</div>
                    <div class="col-sm-6">
                        <input type="text" name="travel_long" id="travel_long" class="form-control" required value="<?php echo $rsedit->travel_long; ?>">
                    </div>
                </div> -->
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพหน้าปก</div>
                    <div class="col-sm-6">
                        ภาพเก่า <br>
                        <img src="<?= base_url('docs/img/' . $rsedit->travel_img); ?>" width="250px" height="210">
                        <br>
                        เลือกใหม่
                        <br>
                        <input type="file" name="travel_img" class="form-control" accept="image/*">
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
                                        <img src="<?= base_url('docs/img/' . $img->travel_img_img); ?>" width="140px" height="100px">
                                        <a class="btn btn-danger btn-sm mb-2" href="#" role="button" onclick="confirmDeleteImg(<?= $img->travel_img_id; ?>, '<?= $img->travel_img_img; ?>');">
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
                                                window.location.href = "<?= site_url('travel_backend/del_img/'); ?>" + file_id;
                                            }
                                        });
                                    }
                                </script>
                            <?php } ?>
                        <?php } ?>
                        เลือกใหม่: <br>
                        <input type="file" name="travel_img_img[]" class="form-control" accept="image/*" multiple>
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
                        <a class="btn btn-danger" href="<?= site_url('travel_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCz5M0j4ysiYbUiYOAoidfE0hbEXJIq7MI&callback=initMap" async defer></script>

<script>
    let map;
    let marker;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: parseFloat(document.getElementById('travel_lat').value),
                lng: parseFloat(document.getElementById('travel_long').value)
            },
            zoom: 15
        });

        // สร้างมาร์คเริ่มต้นตามค่าละติจูดและลองจิจูดในฟอร์ม
        marker = new google.maps.Marker({
            position: {
                lat: parseFloat(document.getElementById('travel_lat').value),
                lng: parseFloat(document.getElementById('travel_long').value)
            },
            map: map,
            draggable: true
        });

        // อัพเดทค่าละติจูดและลองจิจูดในฟอร์มเมื่อมาร์คถูกลาก
        marker.addListener('dragend', function(event) {
            document.getElementById('travel_lat').value = event.latLng.lat();
            document.getElementById('travel_long').value = event.latLng.lng();
        });

        // เมื่อคลิกที่แผนที่เพื่อเปลี่ยนตำแหน่ง
        map.addListener('click', function(event) {
            placeMarker(event.latLng);
        });
    }

    function placeMarker(location) {
        if (marker) {
            marker.setPosition(location);
        } else {
            marker = new google.maps.Marker({
                position: location,
                map: map,
                draggable: true
            });
        }

        // อัพเดทค่าละติจูดและลองจิจูดในฟอร์ม
        document.getElementById('travel_lat').value = location.lat();
        document.getElementById('travel_long').value = location.lng();
    }
</script>