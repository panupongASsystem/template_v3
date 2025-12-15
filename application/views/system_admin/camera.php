        <style>
            /* เพิ่มสไตล์ของแผนที่ */
            #map {
                height: 400px;
                width: 100%;
            }
        </style>
        <h1>พิกัดCCTV</h1>
        <!-- ส่วนแสดงแผนที่ -->
        <div id="map"></div>
        <br>
        <a class="btn add-btn" href="<?= site_url('camera_backend/addingCamera'); ?>" role="button"><i class="bi bi-plus-circle"></i> เพิ่มข้อมูล</a>
        <a class="btn btn-light" href="<?= site_url('camera_backend'); ?>" role="button"><i class="bi bi-arrow-clockwise"></i> Refresh Data</a>
        <br>
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูล CCTV</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <!-- <table class="table table-bordered" id="dataTable" cellspacing="0"> -->
                    <table id="newdataTables" class="table" cellspacing="0">
                        <thead>
                            <tr>
                                <th tabindex="0" rowspan="1" colspan="1">ลำดับ</th>
                                <th tabindex="0" rowspan="1" colspan="1">ชื่อกล้อง</th>
                                <th tabindex="0" rowspan="1" colspan="1">ละติจูด/ลองจิจูด</th>
                                <th tabindex="0" rowspan="1" colspan="1">link CCTV</th>
                                <th tabindex="0" rowspan="1" colspan="1">เวลาที่บันทึก</th>
                                <th tabindex="0" rowspan="1" colspan="1">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($query as $index => $rs) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?= $rs->camera_name; ?></td>
                                    <td><?= $rs->camera_lat; ?>,<br><?= $rs->camera_long; ?></td>
                                    <td><?= mb_substr($rs->camera_api, 0, 50, 'UTF-8'); ?>...</td>
                                    <!-- <td><?= date('d/m/Y', strtotime($rs->camera_datesave . '+543 years')) ?></td> ไม่มีนาที -->
                                    <td><?= date('d/m/Y : H:i', strtotime($rs->camera_datesave . '+543 years')) ?></td>
                                    <td>
                                        <a href="<?= site_url('camera_backend/edit/' . $rs->camera_id); ?>"><i class="bi bi-pencil-square fa-lg "></i></a>
                                        <a href="#" role="button" onclick="confirmDelete('<?= $rs->camera_id; ?>');"><i class="bi bi-trash fa-lg "></i></a>
                                        <script>
                                            function confirmDelete(camera_id) {
                                                Swal.fire({
                                                    title: 'กดเพื่อยืนยัน?',
                                                    text: "คุณจะไม่สามรถกู้คืนได้อีก!",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'ใช่, ต้องการลบ!',
                                                    cancelButtonText: 'ยกเลิก' // เปลี่ยนข้อความปุ่ม Cancel เป็นภาษาไทย
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location.href = "<?= site_url('camera_backend/del_Camera/'); ?>" + camera_id;
                                                    }
                                                });
                                            }
                                        </script>
                                        <!-- <a href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical fa-lg "></i>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <li><a class="dropdown-item" href="<?= site_url('camera/cctv/' . $rs->camera_id); ?>">ดูกล้องแบบเต็มจอ</a></li>
                                        </ul> -->
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- โหลด Google Maps JavaScript API -->
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCz5M0j4ysiYbUiYOAoidfE0hbEXJIq7MI&callback=initMap" async defer></script>

        <script>
            let map;
            let currentMarker; // เพิ่มตัวแปรเก็บมาร์คปัจจุบัน

            function initMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: {
                        lat: 16.5463601,
                        lng: 104.555
                    },
                    zoom: 10
                });

                // เรียกใช้งาน API เพื่อดึงข้อมูลจากตาราง tbl_camera
                $.get('<?php echo base_url("camera_backend/getCameras"); ?>', function(data) {
                    const cameras = JSON.parse(data);

                    // วนลูปเพื่อสร้างมาร์คบนแผนที่
                    cameras.forEach(camera => {
                        const marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(camera.camera_lat),
                                lng: parseFloat(camera.camera_long)
                            },
                            map: map,
                            icon: {
                                url: '<?php echo base_url("docs/icon-cctv.png"); ?>',
                                scaledSize: new google.maps.Size(32, 32) // ปรับขนาดตามที่คุณต้องการ
                            },
                            title: camera.camera_name
                        });

                        // เพิ่มเหตุการณ์คลิกลงบน Marker เพื่อแสดง Pop-up Info Window
                        const videoContainer = document.getElementById('videoContainer');
                        const cameraNameSpan = document.getElementById('cameraName');
                        const player = videojs('cameraVideo');
                        const videoSource = document.getElementById('videoSource');

                        marker.addListener('click', () => {
                            videoSource.src = camera.camera_api;
                            player.src({
                                type: 'application/x-mpegURL',
                                src: camera.camera_api
                            });

                            // กำหนดชื่อของกล้องใน <span>
                            cameraNameSpan.textContent = camera.camera_name;
                            // แสดง Video Container
                            videoContainer.style.display = 'block';
                            // แสดง InfoWindow
                            const infoWindow = new google.maps.InfoWindow({
                                content: videoContainer
                            });

                            if (currentMarker) {
                                currentMarker.infoWindow.close();
                            }

                            infoWindow.open(map, marker);
                            currentMarker = {
                                marker: marker,
                                infoWindow: infoWindow
                            };
                        });

                    });
                });
            }
        </script>

        <div id="videoContainer" style="display: none;">
            <h4 id="cameraName"></h4>
            <video id="cameraVideo" class="video-js" controls preload="auto" width="300" height="160" data-setup="{}">
                <source id="videoSource" src="" type="application/x-mpegURL">
            </video>
        </div>