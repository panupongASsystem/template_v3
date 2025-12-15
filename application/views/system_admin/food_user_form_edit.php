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
            <form action=" <?php echo site_url('food_backend/edit_User_Food/' . $rsedit->user_food_id); ?> " method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อร้าน</div>
                    <div class="col-sm-6">
                        <input type="text" name="user_food_name" required class="form-control" value="<?= $rsedit->user_food_name; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ประเภทอาหาร</div>
                    <div class="col-sm-10">
                        <input type="text" name="user_food_title" required class="form-control" value="<?= $rsedit->user_food_title; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ที่ตั้ง</div>
                    <div class="col-sm-10">
                        <input type="text" name="user_food_location" required class="form-control" value="<?= $rsedit->user_food_location; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">เวลาเปิด-ปิด</div>
                    <div class="col-sm-3">
                        <input type="time" name="user_food_timeopen" required class="form-control" value="<?= $rsedit->user_food_timeopen; ?>">
                    </div>
                    ถึง
                    <div class="col-sm-3">
                        <input type="time" name="user_food_timeclose" required class="form-control" value="<?= $rsedit->user_food_timeclose; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">วันที่ทำการ</div>
                    <div class="col-sm-8">
                        <input type="text" name="user_food_date" required class="form-control" value="<?= $rsedit->user_food_date; ?>">
                        <p>ตัวอย่าง หยุดทุกวันพุธ หรือ เปิดวันจันทร์-วันศุกร์</p>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">เบอร์ติดต่อ</div>
                    <div class="col-sm-6">
                        <input type="number" name="user_food_phone" required class="form-control" max="9999999999" value="<?= $rsedit->user_food_phone; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">link youtube</div>
                    <div class="col-sm-6">
                        <input type="text" name="user_food_youtube" class="form-control" value="<?= $rsedit->user_food_youtube; ?>">
                    </div>
                </div>
                <br>
                <div id="map"></div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ละติจูด</div>
                    <div class="col-sm-6">
                        <input type="text" name="user_food_lat" id="user_food_lat" class="form-control" required value="<?php echo $rsedit->user_food_lat; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ลองจิจูด</div>
                    <div class="col-sm-6">
                        <input type="text" name="user_food_long" id="user_food_long" class="form-control" required value="<?php echo $rsedit->user_food_long; ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปภาพหน้าปก</div>
                    <div class="col-sm-6">
                        ภาพเก่า <br>
                        <img src="<?= base_url('docs/img/' . $rsedit->user_food_img); ?>" width="250px" height="210">
                        <br>
                        เลือกใหม่
                        <br>
                        <input type="file" name="user_food_img" class="form-control" accept="image/*">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">รูปภาพเพิ่มเติม(สามารถเพิ่มได้หลายรูป)</div>
                    <div class="col-sm-6">
                        รูปภาพเก่า: <br>
                        <?php foreach ($qimg as $img) { ?>
                            <img src="<?= base_url('docs/img/' . $img->user_food_img_img); ?>" width="140px" height="100px">&nbsp;
                        <?php } ?>
                        <br>
                        เลือกใหม่: <br>
                        <input type="file" name="user_food_img_img[]" class="form-control" accept="image/*" multiple>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
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
                lat: parseFloat(document.getElementById('user_food_lat').value),
                lng: parseFloat(document.getElementById('user_food_long').value)
            },
            zoom: 15
        });

        // สร้างมาร์คเริ่มต้นตามค่าละติจูดและลองจิจูดในฟอร์ม
        marker = new google.maps.Marker({
            position: {
                lat: parseFloat(document.getElementById('user_food_lat').value),
                lng: parseFloat(document.getElementById('user_food_long').value)
            },
            map: map,
            draggable: true
        });

        // อัพเดทค่าละติจูดและลองจิจูดในฟอร์มเมื่อมาร์คถูกลาก
        marker.addListener('dragend', function(event) {
            document.getElementById('user_food_lat').value = event.latLng.lat();
            document.getElementById('user_food_long').value = event.latLng.lng();
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
        document.getElementById('user_food_lat').value = location.lat();
        document.getElementById('user_food_long').value = location.lng();
    }
</script>