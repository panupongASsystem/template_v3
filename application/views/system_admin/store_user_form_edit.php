<style>
    /* เพิ่มสไตล์ของแผนที่ */
    #map {
        height: 400px;
        width: 100%;
    }
</style>
<div class="container">
    <dov class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>แก้ไขข้อมูลร้านค้า</h4>
            <form action=" <?php echo site_url('store_backend/edit_user_store/' . $rsedit->user_store_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ประเภทของร้าน</div>
                    <div class="col-sm-3">
                        <select class="form-control" name="user_store_type" required>
                            <option value="<?= $rsedit->user_store_type; ?>"><?= $rsedit->user_store_type; ?></option>
                            <option value="" disabled>เลือกข้อมูล</option>
                            <option value="ซ่อมแซม">ซ่อมแซม/บ้าน</option>
                            <option value="แอร์">แอร์</option>
                            <option value="รถ/ยานพาหนะ">รถ/ยานพาหนะ</option>
                            <option value="ไฟฟ้า">ไฟฟ้า</option>
                            <option value="เทคโนโลยี">เทคโนโลยี</option>
                            <option value="ประปา">ประปา</option>
                            <option value="สัตว์เลี้ยง">สัตว์เลี้ยง</option>
                            <option value="เสริมสวย">เสริมสวย</option>
                            <option value="คนสวน/แม่บ้าน">คนสวน/แม่บ้าน</option>
                            <option value="คาเฟ่">คาเฟ่</option>
                            <option value="โรงแรม">โรงแรม</option>
                            <option value="อื่นๆ">อื่นๆ</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ชื่อร้าน</div>
                    <div class="col-sm-6">
                        <input type="text" name="user_store_name" required class="form-control" value="<?= $rsedit->user_store_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">วันที่ทำการ</div>
                    <div class="col-sm-3">
                        <select class="form-control" name="user_store_date" required>
                            <option value="<?= $rsedit->user_store_date; ?>"><?= $rsedit->user_store_date; ?></option>
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
                    <div class="col-sm-4">
                        <input type="time" name="user_store_timeopen" required class="form-control" value="<?= $rsedit->user_store_timeopen; ?>">
                    </div>
                    ถึง
                    <div class="col-sm-4">
                        <input type="time" name="user_store_timeclose" required class="form-control" value="<?= $rsedit->user_store_timeclose; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">เบอร์ติดต่อ</div>
                    <div class="col-sm-6">
                        <input type="number" name="user_store_phone" required class="form-control" max="9999999999" value="<?= $rsedit->user_store_phone; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">ที่ตั้ง</div>
                    <div class="col-sm-6">
                        <input type="text" name="user_store_location" required class="form-control" value="<?= $rsedit->user_store_location; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-3 control-label">คำอธิบาย</div>
                    <div class="col-sm-9">
                        <textarea name="user_store_detail" id="user_store_detail"><?= $rsedit->user_store_detail; ?></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#user_store_detail'), {
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

                    <div class="mt-4 mb-4" id="map"></div>

                    <div class="form-group row">
                        <div class="col-sm-3 control-label">ละติจูด</div>
                        <div class="col-sm-6">
                            <input type="text" name="user_store_lat" id="user_store_lat" class="form-control" required value="<?php echo $rsedit->user_store_lat; ?>">
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">ลองจิจูด</div>
                        <div class="col-sm-6">
                            <input type="text" name="user_store_long" id="user_store_long" class="form-control" required value="<?php echo $rsedit->user_store_long; ?>">
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">รูปภาพหน้าปก</div>
                        <div class="col-sm-6">
                            ภาพเก่า <br>
                            <img src="<?= base_url('docs/img/' . $rsedit->user_store_img); ?>" width="250px" height="210">
                            <br>
                            เลือกใหม่
                            <br>
                            <input type="file" name="user_store_img" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-3 control-label">รูปภาพเพิ่มเติม(สามารถเพิ่มได้หลายรูป)</div>
                        <div class="col-sm-6">
                            รูปภาพเก่า: <br>
                            <?php foreach ($qimg as $img) { ?>
                                <img src="<?= base_url('docs/img/' . $img->user_store_img_img); ?>" width="140px" height="100px">&nbsp;
                            <?php } ?>
                            <br>
                            เลือกใหม่: <br>
                            <input type="file" name="user_store_img_img[]" class="form-control" accept="image/*" multiple>
                        </div>
                    </div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-3 control-label"></div>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                            <a class="btn btn-danger" href="<?= site_url('store_backend'); ?>" role="button">ยกเลิก</a>
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
                lat: parseFloat(document.getElementById('user_store_lat').value),
                lng: parseFloat(document.getElementById('user_store_long').value)
            },
            zoom: 15
        });

        // สร้างมาร์คเริ่มต้นตามค่าละติจูดและลองจิจูดในฟอร์ม
        marker = new google.maps.Marker({
            position: {
                lat: parseFloat(document.getElementById('user_store_lat').value),
                lng: parseFloat(document.getElementById('user_store_long').value)
            },
            map: map,
            draggable: true
        });

        // อัพเดทค่าละติจูดและลองจิจูดในฟอร์มเมื่อมาร์คถูกลาก
        marker.addListener('dragend', function(event) {
            document.getElementById('user_store_lat').value = event.latLng.lat();
            document.getElementById('user_store_long').value = event.latLng.lng();
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
        document.getElementById('user_store_lat').value = location.lat();
        document.getElementById('user_store_long').value = location.lng();
    }
</script>