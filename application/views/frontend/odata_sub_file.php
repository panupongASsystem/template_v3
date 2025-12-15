<div class="text-center pages-head">
    <span class="font-pages-head">ฐานข้อมูลเปิดภาครัฐ (Open Data)</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-news">

        <!-- <div class="bg-pages-ita"> -->
        <!-- <div class="scrollable-container-news"> -->
        <span class="font-ita-head"><b><?= $query->odata_sub_name; ?></b></span>
        <?php foreach ($query_odata_sub_file as $rs) {
            // ดึงข้อมูลของไฟล์
            $fileInfo = pathinfo($rs->odata_sub_file_doc);

            // ตรวจสอบลงท้ายของไฟล์
            $fileExtension = strtolower($fileInfo['extension']);

            // กำหนดรูปภาพตามลงท้ายของไฟล์
            $iconImage = "";
            if ($fileExtension === 'pdf') {
                $iconImage = "docs/icon-file-pdf.png";
            } elseif ($fileExtension === 'doc' || $fileExtension === 'docx') {
                $iconImage = "docs/icon-file-doc.png";
            } elseif ($fileExtension === 'xls' || $fileExtension === 'xlsx') {
                $iconImage = "docs/icon-file-xls.png";
            } elseif ($fileExtension === 'pptx' || $fileExtension === 'ppt') {
                $iconImage = "docs/icon-file-ppt.png";
            }
        ?>
            <div class="pages-select-pdf underline">

                <!-- <div class="bg-ita-empty">
                    <div class="row mb-2 mt-3">
                        <div class="col-10 ml-5" style="margin-left: 80px;">
                            <div class="row">
                                <div class="col-1 mt-2">
                                    <img src="<?php echo base_url($iconImage); ?>" width="50px">
                                </div>
                                <div class="col-11">
                                    <span class="font-ita-head"><?= $rs->odata_sub_file_name; ?></span>
                                    <br>
                                    <span class="font-doc">&nbsp;&nbsp;&nbsp;&nbsp;จำนวนดาวน์โหลด <?= $rs->odata_sub_file_download; ?> ครั้ง</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <a onclick="downloadFile(event, <?= $rs->odata_sub_file_id; ?>)" href="<?= base_url('docs/file/' . $rs->odata_sub_file_doc); ?>" download>
                                <img src="<?php echo base_url("docs/b.dowload-odata.png"); ?>">
                            </a>
                            <script>
                                function downloadFile(event, odata_sub_file_id) {
                                    // ทำการส่งคำร้องขอ AJAX ไปยัง URL ที่บันทึกการดาวน์โหลดพร้อมกับ ID
                                    var xhr = new XMLHttpRequest();
                                    xhr.open('GET', '<?= base_url('Pages/increment_download_odata_sub_file/'); ?>' + odata_sub_file_id, true);
                                    xhr.send();

                                    // ทำการเปิดไฟล์ PDF ในหน้าต่างใหม่
                                    window.open(event.currentTarget.href, '_blank');
                                }
                            </script>
                        </div>
                    </div>
                </div> -->

                <div class="row">
                    <div class="col-1" style="padding-left: 30px;">
						 <a onclick="downloadFile(event, <?= $rs->odata_sub_file_id; ?>)" href="<?= base_url('docs/file/' . $rs->odata_sub_file_doc); ?>" download>
                        <img src="<?php echo base_url($iconImage); ?>" width="50px">
							 </a>
                    </div>
                    <div class="col-11">
                        <a onclick="downloadFile(event, <?= $rs->odata_sub_file_id; ?>)" href="<?= base_url('docs/file/' . $rs->odata_sub_file_doc); ?>" download>
							 <script>
                                function downloadFile(event, odata_sub_file_id) {
                                    // ทำการส่งคำร้องขอ AJAX ไปยัง URL ที่บันทึกการดาวน์โหลดพร้อมกับ ID
                                    var xhr = new XMLHttpRequest();
                                    xhr.open('GET', '<?= base_url('Pages/increment_download_odata_sub_file/'); ?>' + odata_sub_file_id, true);
                                    xhr.send();

                                    // ทำการเปิดไฟล์ PDF ในหน้าต่างใหม่
                                    window.open(event.currentTarget.href, '_blank');
                                }
                            </script>
                            <span class="font-ita-head"><?= $rs->odata_sub_file_name; ?></span>
                        </a>
                        <br>
                        <span class="font-doc">&nbsp;&nbsp;&nbsp;&nbsp;จำนวนดาวน์โหลด <?= $rs->odata_sub_file_download; ?> ครั้ง</span>
                    </div>
                </div>

            </div>
        <?php } ?>
        <!-- </div> -->
        <!-- </div> -->
    </div><br><br><br>

    <!-- <div id="popup-ita" class="popup-ita">
        <div class="popup-ita-content">
            <h4><b>test</b></h4>
            <div class="row">
                <div class="col-7">
                    <div class="d-flex justify-content-start">
                        <h5><b>ลิงค์</b></h5>
                        <span id="popup-ita-link"></span>
                    </div>
                </div>
                <div class="col-5">
                    <div class="d-flex justify-content-start">
                        <h5><b>คำอธิบาย</b></h5>
                    </div>
                </div>
            </div>
            <br>
            <div class="d-flex justify-content-end">
                <button class="btn-close-ita" onclick="closePopupIta()">ปิด</button>
            </div>
        </div>
    </div>

    <script>
        function closePopupIta() {
            document.getElementById("popup-ita").style.display = "none";
        }

        function openPopupIta(ita_year_link_id) {
            document.getElementById("popup-ita").style.display = "block";

            $.ajax({
                url: 'Pages/ita_year_link_list/' + ita_year_link_id,
                type: 'GET',
                success: function(response) {
                    // ดำเนินการต่อไปตามที่คุณต้องการ
                    console.log(response);
                    // เรียกฟังก์ชันสำหรับแสดงข้อมูลใน popup
                    displayPopupData(response);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }


        function displayPopupData(data) {
            // แสดงข้อมูลที่ได้รับมาจาก AJAX ใน console
            console.log(data);

            // ตรวจสอบโครงสร้างข้อมูลและดึงค่าที่ต้องการ
            // ตัวอย่างเช่น
            if (data) {
                document.getElementById("popup-ita-link").innerText = data.ita_year_link_link1;
            } else {
                console.error('Invalid data structure:', data);
            }
        }
    </script> -->