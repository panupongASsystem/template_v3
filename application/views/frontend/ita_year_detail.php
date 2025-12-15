<div class="text-center pages-head">
    <span class="font-pages-head">การประเมินคุณธรรมและความโปร่งใส ITA</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages">
    <div class="container-pages-detail" style="position: relative; z-index: 10;">
        <?php foreach ($query_topic as $rs) { ?>
            <div class="bg-ita-empty">
                <div class="d-flex justify-content-start mt-3">
                    <span class="font-ita-head"><?= $rs->ita_year_topic_name; ?></span>
                </div>
                <div class="ita-detail-content">
                    <?php
                    $linkDataArray = json_decode('[' . $rs->link_data . ']', true);
                    foreach ($linkDataArray as $linkData) {
                        echo '<span class="font-ita-content">' . $linkData['ita_year_link_name'] . '</span>';
                        echo '<div class="row mt-2">';
                        echo '<div class="col-10">';

                        // ตรวจสอบและแสดงค่า title แทนคำว่า "ลิงค์"
                        foreach (range(1, 5) as $i) {
                            $linkKey = 'ita_year_link_link' . $i;
                            $titleKey = 'ita_year_link_title' . $i;

                            if (!empty($linkData[$linkKey])) {
                                // ถ้ามี title ให้แสดง title
                                if (!empty($linkData[$titleKey])) {
                                    echo '<a class="font-ita-content-detail" target="_blank" href=' . $linkData[$linkKey] . '>' . $i . '.' . $linkData[$titleKey] . '</a><br><br>';
                                } else {
                                    // ถ้าไม่มี title ให้แสดงคำว่า "ลิงค์" พร้อมกับลิงค์
                                    echo '<a class="font-ita-content-detail" target="_blank" href=' . $linkData[$linkKey] . '>' . $i . '. ลิงค์ - ' . $linkData[$linkKey] . '</a><br><br>';
                                }
                            }
                        }

                        echo '</div>';
                        /*echo '<div class="col-6">';

                        // แสดงลิงค์
                        foreach (range(1, 5) as $i) {
                            $linkKey = 'ita_year_link_link' . $i;
                            if (!empty($linkData[$linkKey])) {
                                echo '<span class="font-ita-content-detail-link one-line-ellipsis">' . $linkData[$linkKey] . '</span><br><br>';
                            }
                        }

                        echo '</div>'; */
                        echo '<div class="col-2">';

                        // แสดงปุ่มเปิดลิงค์
                        foreach (range(1, 5) as $i) {
                            $linkKey = 'ita_year_link_link' . $i;
                            if (!empty($linkData[$linkKey])) {
                                echo '<a class="btn btn-ita-open" target="_blank" href="' . $linkData[$linkKey] . '">เปิด</a><br>';
                            }
                        }

                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        <?php } ?>
    </div>
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