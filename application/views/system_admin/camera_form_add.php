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
            <h4>เพิ่มข้อมูลCCTV</h4>
            <form action=" <?php echo site_url('camera_backend/addCamera'); ?> " method="post" class="form-horizontal">
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ชื่อCCTV</div>
                    <div class="col-sm-6">
                        <input type="text" name="camera_name" class="form-control" required>
                    </div>
                </div>
                <br>
                <div id="map"></div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ละติจูด</div>
                    <div class="col-sm-6">
                        <input type="text" id="camera_lat" name="camera_lat" class="form-control" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">ลองจิจูด</div>
                    <div class="col-sm-6">
                        <input type="text" id="camera_long" name="camera_long" class="form-control" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">Link ของกล้อง</div>
                    <div class="col-sm-10">
                        <input type="text" name="camera_api" class="form-control" required>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label"></div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?php echo site_url('camera_backend'); ?>">ยกเลิก</a>
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
                lat: 16.5463601,
                lng: 104.555
            },
            zoom: 10
        });

        // เมื่อคลิกที่แผนที่เพื่อเพิ่มตำแหน่ง
        map.addListener('click', function(event) {
            placeMarker(event.latLng);
        });

        // กำหนดตำแหน่งเริ่มต้น (ถ้ามี)
        const initialLat = parseFloat(document.getElementById('camera_lat').value);
        const initialLng = parseFloat(document.getElementById('camera_long').value);

        if (!isNaN(initialLat) && !isNaN(initialLng)) {
            const initialLatLng = {
                lat: initialLat,
                lng: initialLng
            };
            placeMarker(initialLatLng);
        }
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
        document.getElementById('camera_lat').value = location.lat();
        document.getElementById('camera_long').value = location.lng();
    }
</script>