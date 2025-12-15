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
            <h4>แก้ไขข้อมูลร้านอาหาร</h4>
            <form action=" <?php echo site_url('food_backend/edit_food/' . $rsedit->food_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อร้าน</div>
                    <div class="col-sm-6">
                        <input type="text" name="food_name" required class="form-control" value="<?= $rsedit->food_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รายละเอียด</div>
                    <div class="col-sm-9">
                        <textarea name="food_detail" id="food_detail"><?= $rsedit->food_detail; ?></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#food_detail'), {
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
                        <input type="text" name="food_location" required class="form-control" value="<?= $rsedit->food_location; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่ทำการ</div>
                    <div class="col-sm-3">
                        <select class="form-control" name="food_date" required>
                            <option value="<?= $rsedit->food_date; ?>"><?= $rsedit->food_date; ?></option>
                            <option value="" disabled>เลือกข้อมูล</option>
                            <option value="เปิดทุกวัน">เปิดทุกวัน</option>
                            <option value="เปิดทุกวันจันทร์ - วันศุกร์">เปิดทุกวันจันทร์ - วันศุกร์</option>
                            <option value="เปิดทุกวันจันทร์ - วันเสาร์">เปิดทุกวันจันทร์ - วันเสาร์</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เวลาเปิด-ปิด</div>
                    <div class="col-sm-3">
                        <input type="time" name="food_timeopen" required class="form-control" value="<?= $rsedit->food_timeopen; ?>">
                    </div>
                    ถึง
                    <div class="col-sm-3">
                        <input type="time" name="food_timeclose" required class="form-control" value="<?= $rsedit->food_timeclose; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เบอร์ติดต่อ</div>
                    <div class="col-sm-6">
                        <input type="number" name="food_phone" required class="form-control" max="9999999999" value="<?= $rsedit->food_phone; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">link youtube</div>
                    <div class="col-sm-9">
                        <input type="text" name="food_youtube" class="form-control" value="<?= $rsedit->food_youtube; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">แหล่งที่มา</div>
                    <div class="col-sm-6">
                        <input type="text" name="food_refer" required class="form-control" value="<?= $rsedit->food_refer; ?>">
                    </div>
                </div>
                <br>
                <div id="map"></div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ละติจูด</div>
                    <div class="col-sm-6">
                        <input type="text" name="food_lat" id="food_lat" class="form-control" required value="<?php echo $rsedit->food_lat; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ลองจิจูด</div>
                    <div class="col-sm-6">
                        <input type="text" name="food_long" id="food_long" class="form-control" required value="<?php echo $rsedit->food_long; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพหน้าปก</div>
                    <div class="col-sm-6">
                        ภาพเก่า <br>
                        <?php if (!empty($rsedit->food_img)) : ?>
                            <img src="<?= base_url('docs/img/' . $rsedit->food_img); ?>" width="250px" height="210">
                        <?php else : ?>
                            <img src="<?= base_url('docs/logo.png'); ?>" width="250px" height="210">
                        <?php endif; ?>
                        <br>
                        เลือกใหม่
                        <br>
                        <input type="file" name="food_img" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">รูปภาพเพิ่มเติม</div>
                    <div class="col-sm-6">
                        รูปภาพเก่า: <br>
                        <?php foreach ($qimg as $img) { ?>
                            <img src="<?= base_url('docs/img/' . $img->food_img_img); ?>" width="140px" height="100px">&nbsp;
                        <?php } ?>
                        <br>
                        เลือกใหม่: <br>
                        <input type="file" name="food_img_img[]" class="form-control" accept="image/*" multiple>
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
                        <a class="btn btn-danger" href="<?= site_url('food_backend'); ?>" role="button">ยกเลิก</a>
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
                lat: parseFloat(document.getElementById('food_lat').value),
                lng: parseFloat(document.getElementById('food_long').value)
            },
            zoom: 15
        });

        // สร้างมาร์คเริ่มต้นตามค่าละติจูดและลองจิจูดในฟอร์ม
        marker = new google.maps.Marker({
            position: {
                lat: parseFloat(document.getElementById('food_lat').value),
                lng: parseFloat(document.getElementById('food_long').value)
            },
            map: map,
            draggable: true
        });

        // อัพเดทค่าละติจูดและลองจิจูดในฟอร์มเมื่อมาร์คถูกลาก
        marker.addListener('dragend', function(event) {
            document.getElementById('food_lat').value = event.latLng.lat();
            document.getElementById('food_long').value = event.latLng.lng();
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
        document.getElementById('food_lat').value = location.lat();
        document.getElementById('food_long').value = location.lng();
    }
</script>