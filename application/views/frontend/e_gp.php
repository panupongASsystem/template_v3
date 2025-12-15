<div class="text-center pages-head">
    <span class="font-pages-head">ข่าวจัดซื้อจัดจ้าง e-GP</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<?php
// รับปีปัจจุบันในรูปแบบคริสต์ศักราช
$yearAD = date("Y");

// แปลงเป็นปีพุทธศักราชโดยเพิ่ม 543 ปี
$yearBE = $yearAD + 543;
// ปีที่เลือกบน dropdown
$selectYear = $yearBE;
$zeroyear = $yearBE;
$oneyear = ($yearBE - 1);
$twoyear = ($yearBE - 2);
$threeyear = ($yearBE - 3);
$fouryear = ($yearBE - 4); ?>

<div class="bg-pages ">
    <div class="container-pages-detail">
        <div class="row align-items-center justify-content-end">
            <div class="col-auto ms-auto">
                <label class="fs-4">ปี: </label>
            </div>
            <select id="myDropdown" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" style="width: 100px;" onchange="handleSelectionChange()">
                <?php
                $years = $yearBE;
                for ($i = 0; $i < 5; $i++) {
                    //echo "<option value='" . $i . "'>" . $i . "</option>";
                    echo "<option value='" . $years . "'>" . $years . "</option>";
                    $years -= 1;
                }
                ?>
            </select>
        </div>

        <div id="content1" style="display: block;">
            <?php foreach ($q2567 as $egp) { ?>
                <div class="pages-select-e-gp underline">
                    <div class="row">
                        <div class="col-2" style="padding-left: 30px;">
                            <img src="<?php echo base_url("docs/e-gp.png"); ?>" style="width: 65px; height:65px;">
                        </div>
                        <div class="col-10">
                            <span class="font-24">
                                <a href="https://process3.gprocurement.go.th/egp2procmainWeb/jsp/procsearch.sch?servlet=gojsp&proc_id=ShowHTMLFile&processFlows=Procure&projectId=<?= $egp->project_id; ?>&templateType=W2&temp_Announ=A&temp_itemNo=0&seqNo=1" target="_blank"> <?= $egp->project_name; ?></a>
                                <br><span><strong><?= "เผยแพร่เมื่อวันที่" ?>:</strong> <?php
                                                                                        // สมมติว่าค่าที่ได้รับมาจากตัวแปร $rs['doc_date'] อยู่ในรูปแบบ "10 ม.ค. 67"
                                                                                        $dateStr = $egp->transaction_date;

                                                                                        // แปลงเดือนจากชื่อไทยย่อเป็นชื่อเต็ม
                                                                                        $thaiMonths = [
                                                                                            'ม.ค.' => 'มกราคม',
                                                                                            'ก.พ.' => 'กุมภาพันธ์',
                                                                                            'มี.ค.' => 'มีนาคม',
                                                                                            'เม.ย.' => 'เมษายน',
                                                                                            'พ.ค.' => 'พฤษภาคม',
                                                                                            'มิ.ย.' => 'มิถุนายน',
                                                                                            'ก.ค.' => 'กรกฎาคม',
                                                                                            'ส.ค.' => 'สิงหาคม',
                                                                                            'ก.ย.' => 'กันยายน',
                                                                                            'ต.ค.' => 'ตุลาคม',
                                                                                            'พ.ย.' => 'พฤศจิกายน',
                                                                                            'ธ.ค.' => 'ธันวาคม',
                                                                                        ];

                                                                                        // แปลงเดือนใน $dateStr โดยใช้การแทนที่จาก array $thaiMonths
                                                                                        foreach ($thaiMonths as $shortMonth => $fullMonth) {
                                                                                            if (strpos($dateStr, $shortMonth) !== false) {
                                                                                                $dateStr = str_replace($shortMonth, $fullMonth, $dateStr);
                                                                                                break; // ออกจาก loop เมื่อเจอการแทนที่แล้ว
                                                                                            }
                                                                                        }

                                                                                        // แปลงปีคริสต์ศักราช (สองหลัก) เป็นปีพุทธศักราช (สี่หลัก)
                                                                                        preg_match('/\d{2}$/', $dateStr, $matches);
                                                                                        if ($matches) {
                                                                                            $year = $matches[0]; // ดึงตัวเลขสองหลักท้ายสุด ซึ่งคือปีในรูปแบบ 67
                                                                                            $fullYear = (int)$year < 50 ? '25' . $year : '25' . $year; // เพิ่ม '25' ข้างหน้าปี
                                                                                            $dateStr = str_replace($year, $fullYear, $dateStr); // แทนที่ปีด้วยปีที่เพิ่ม '25' ข้างหน้า
                                                                                        }

                                                                                        // แสดงผลลัพธ์
                                                                                        echo $dateStr; // ตัวอย่างเช่น "10 มกราคม 2567"
                                                                                        ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div id="content2" style="display: none;">
            <?php foreach ($q2566 as $egp) { ?>
                <div class="pages-select-e-gp underline">
                    <div class="row">
                        <div class="col-2" style="padding-left: 30px;">
                            <img src="<?php echo base_url("docs/e-gp.png"); ?>" style="width: 65px; height:65px;">
                        </div>
                        <div class="col-10">
                            <span class="font-24">
                                <a href="https://process3.gprocurement.go.th/egp2procmainWeb/jsp/procsearch.sch?servlet=gojsp&proc_id=ShowHTMLFile&processFlows=Procure&projectId=<?= $egp->project_id; ?>&templateType=W2&temp_Announ=A&temp_itemNo=0&seqNo=1" target="_blank"> <?= $egp->project_name; ?></a>
                                <br><span><strong><?= "เผยแพร่เมื่อวันที่" ?>:</strong> <?php
                                                                                        // สมมติว่าค่าที่ได้รับมาจากตัวแปร $rs['doc_date'] อยู่ในรูปแบบ "10 ม.ค. 67"
                                                                                        $dateStr = $egp->transaction_date;

                                                                                        // แปลงเดือนจากชื่อไทยย่อเป็นชื่อเต็ม
                                                                                        $thaiMonths = [
                                                                                            'ม.ค.' => 'มกราคม',
                                                                                            'ก.พ.' => 'กุมภาพันธ์',
                                                                                            'มี.ค.' => 'มีนาคม',
                                                                                            'เม.ย.' => 'เมษายน',
                                                                                            'พ.ค.' => 'พฤษภาคม',
                                                                                            'มิ.ย.' => 'มิถุนายน',
                                                                                            'ก.ค.' => 'กรกฎาคม',
                                                                                            'ส.ค.' => 'สิงหาคม',
                                                                                            'ก.ย.' => 'กันยายน',
                                                                                            'ต.ค.' => 'ตุลาคม',
                                                                                            'พ.ย.' => 'พฤศจิกายน',
                                                                                            'ธ.ค.' => 'ธันวาคม',
                                                                                        ];

                                                                                        // แปลงเดือนใน $dateStr โดยใช้การแทนที่จาก array $thaiMonths
                                                                                        foreach ($thaiMonths as $shortMonth => $fullMonth) {
                                                                                            if (strpos($dateStr, $shortMonth) !== false) {
                                                                                                $dateStr = str_replace($shortMonth, $fullMonth, $dateStr);
                                                                                                break; // ออกจาก loop เมื่อเจอการแทนที่แล้ว
                                                                                            }
                                                                                        }

                                                                                        // แปลงปีคริสต์ศักราช (สองหลัก) เป็นปีพุทธศักราช (สี่หลัก)
                                                                                        preg_match('/\d{2}$/', $dateStr, $matches);
                                                                                        if ($matches) {
                                                                                            $year = $matches[0]; // ดึงตัวเลขสองหลักท้ายสุด ซึ่งคือปีในรูปแบบ 67
                                                                                            $fullYear = (int)$year < 50 ? '25' . $year : '25' . $year; // เพิ่ม '25' ข้างหน้าปี
                                                                                            $dateStr = str_replace($year, $fullYear, $dateStr); // แทนที่ปีด้วยปีที่เพิ่ม '25' ข้างหน้า
                                                                                        }

                                                                                        // แสดงผลลัพธ์
                                                                                        echo $dateStr; // ตัวอย่างเช่น "10 มกราคม 2567"
                                                                                        ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div id="content3" style="display: none;">
            <?php foreach ($q2565 as $egp) { ?>
                <div class="pages-select-e-gp underline">
                    <div class="row">
                        <div class="col-2" style="padding-left: 30px;">
                            <img src="<?php echo base_url("docs/e-gp.png"); ?>" style="width: 65px; height:65px;">
                        </div>
                        <div class="col-10">
                            <span class="font-24">
                                <a href="https://process3.gprocurement.go.th/egp2procmainWeb/jsp/procsearch.sch?servlet=gojsp&proc_id=ShowHTMLFile&processFlows=Procure&projectId=<?= $egp->project_id; ?>&templateType=W2&temp_Announ=A&temp_itemNo=0&seqNo=1" target="_blank"> <?= $egp->project_name; ?></a>
                                <br><span><strong><?= "เผยแพร่เมื่อวันที่" ?>:</strong> <?php
                                                                                        // สมมติว่าค่าที่ได้รับมาจากตัวแปร $rs['doc_date'] อยู่ในรูปแบบ "10 ม.ค. 67"
                                                                                        $dateStr = $egp->transaction_date;

                                                                                        // แปลงเดือนจากชื่อไทยย่อเป็นชื่อเต็ม
                                                                                        $thaiMonths = [
                                                                                            'ม.ค.' => 'มกราคม',
                                                                                            'ก.พ.' => 'กุมภาพันธ์',
                                                                                            'มี.ค.' => 'มีนาคม',
                                                                                            'เม.ย.' => 'เมษายน',
                                                                                            'พ.ค.' => 'พฤษภาคม',
                                                                                            'มิ.ย.' => 'มิถุนายน',
                                                                                            'ก.ค.' => 'กรกฎาคม',
                                                                                            'ส.ค.' => 'สิงหาคม',
                                                                                            'ก.ย.' => 'กันยายน',
                                                                                            'ต.ค.' => 'ตุลาคม',
                                                                                            'พ.ย.' => 'พฤศจิกายน',
                                                                                            'ธ.ค.' => 'ธันวาคม',
                                                                                        ];

                                                                                        // แปลงเดือนใน $dateStr โดยใช้การแทนที่จาก array $thaiMonths
                                                                                        foreach ($thaiMonths as $shortMonth => $fullMonth) {
                                                                                            if (strpos($dateStr, $shortMonth) !== false) {
                                                                                                $dateStr = str_replace($shortMonth, $fullMonth, $dateStr);
                                                                                                break; // ออกจาก loop เมื่อเจอการแทนที่แล้ว
                                                                                            }
                                                                                        }

                                                                                        // แปลงปีคริสต์ศักราช (สองหลัก) เป็นปีพุทธศักราช (สี่หลัก)
                                                                                        preg_match('/\d{2}$/', $dateStr, $matches);
                                                                                        if ($matches) {
                                                                                            $year = $matches[0]; // ดึงตัวเลขสองหลักท้ายสุด ซึ่งคือปีในรูปแบบ 67
                                                                                            $fullYear = (int)$year < 50 ? '25' . $year : '25' . $year; // เพิ่ม '25' ข้างหน้าปี
                                                                                            $dateStr = str_replace($year, $fullYear, $dateStr); // แทนที่ปีด้วยปีที่เพิ่ม '25' ข้างหน้า
                                                                                        }

                                                                                        // แสดงผลลัพธ์
                                                                                        echo $dateStr; // ตัวอย่างเช่น "10 มกราคม 2567"
                                                                                        ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div id="content4" style="display: none;">
            <?php foreach ($q2564 as $egp) { ?>
                <div class="pages-select-e-gp underline">
                    <div class="row">
                        <div class="col-2" style="padding-left: 30px;">
                            <img src="<?php echo base_url("docs/e-gp.png"); ?>" style="width: 65px; height:65px;">
                        </div>
                        <div class="col-10">
                            <span class="font-24">
                                <a href="https://process3.gprocurement.go.th/egp2procmainWeb/jsp/procsearch.sch?servlet=gojsp&proc_id=ShowHTMLFile&processFlows=Procure&projectId=<?= $egp->project_id; ?>&templateType=W2&temp_Announ=A&temp_itemNo=0&seqNo=1" target="_blank"> <?= $egp->project_name; ?></a>
                                <br><span><strong><?= "เผยแพร่เมื่อวันที่" ?>:</strong> <?php
                                                                                        // สมมติว่าค่าที่ได้รับมาจากตัวแปร $rs['doc_date'] อยู่ในรูปแบบ "10 ม.ค. 67"
                                                                                        $dateStr = $egp->transaction_date;

                                                                                        // แปลงเดือนจากชื่อไทยย่อเป็นชื่อเต็ม
                                                                                        $thaiMonths = [
                                                                                            'ม.ค.' => 'มกราคม',
                                                                                            'ก.พ.' => 'กุมภาพันธ์',
                                                                                            'มี.ค.' => 'มีนาคม',
                                                                                            'เม.ย.' => 'เมษายน',
                                                                                            'พ.ค.' => 'พฤษภาคม',
                                                                                            'มิ.ย.' => 'มิถุนายน',
                                                                                            'ก.ค.' => 'กรกฎาคม',
                                                                                            'ส.ค.' => 'สิงหาคม',
                                                                                            'ก.ย.' => 'กันยายน',
                                                                                            'ต.ค.' => 'ตุลาคม',
                                                                                            'พ.ย.' => 'พฤศจิกายน',
                                                                                            'ธ.ค.' => 'ธันวาคม',
                                                                                        ];

                                                                                        // แปลงเดือนใน $dateStr โดยใช้การแทนที่จาก array $thaiMonths
                                                                                        foreach ($thaiMonths as $shortMonth => $fullMonth) {
                                                                                            if (strpos($dateStr, $shortMonth) !== false) {
                                                                                                $dateStr = str_replace($shortMonth, $fullMonth, $dateStr);
                                                                                                break; // ออกจาก loop เมื่อเจอการแทนที่แล้ว
                                                                                            }
                                                                                        }

                                                                                        // แปลงปีคริสต์ศักราช (สองหลัก) เป็นปีพุทธศักราช (สี่หลัก)
                                                                                        preg_match('/\d{2}$/', $dateStr, $matches);
                                                                                        if ($matches) {
                                                                                            $year = $matches[0]; // ดึงตัวเลขสองหลักท้ายสุด ซึ่งคือปีในรูปแบบ 67
                                                                                            $fullYear = (int)$year < 50 ? '25' . $year : '25' . $year; // เพิ่ม '25' ข้างหน้าปี
                                                                                            $dateStr = str_replace($year, $fullYear, $dateStr); // แทนที่ปีด้วยปีที่เพิ่ม '25' ข้างหน้า
                                                                                        }

                                                                                        // แสดงผลลัพธ์
                                                                                        echo $dateStr; // ตัวอย่างเช่น "10 มกราคม 2567"
                                                                                        ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div id="content5" style="display: none;">
            <?php foreach ($q2563 as $egp) { ?>
                <div class="pages-select-e-gp underline">
                    <div class="row">
                        <div class="col-2" style="padding-left: 30px;">
                            <img src="<?php echo base_url("docs/e-gp.png"); ?>" style="width: 65px; height:65px;">
                        </div>
                        <div class="col-10">
                            <span class="font-24">
                                <a href="https://process3.gprocurement.go.th/egp2procmainWeb/jsp/procsearch.sch?servlet=gojsp&proc_id=ShowHTMLFile&processFlows=Procure&projectId=<?= $egp->project_id; ?>&templateType=W2&temp_Announ=A&temp_itemNo=0&seqNo=1" target="_blank"> <?= $egp->project_name; ?></a>
                                <br><span><strong><?= "เผยแพร่เมื่อวันที่" ?>:</strong> <?php
                                                                                        // สมมติว่าค่าที่ได้รับมาจากตัวแปร $rs['doc_date'] อยู่ในรูปแบบ "10 ม.ค. 67"
                                                                                        $dateStr = $egp->transaction_date;

                                                                                        // แปลงเดือนจากชื่อไทยย่อเป็นชื่อเต็ม
                                                                                        $thaiMonths = [
                                                                                            'ม.ค.' => 'มกราคม',
                                                                                            'ก.พ.' => 'กุมภาพันธ์',
                                                                                            'มี.ค.' => 'มีนาคม',
                                                                                            'เม.ย.' => 'เมษายน',
                                                                                            'พ.ค.' => 'พฤษภาคม',
                                                                                            'มิ.ย.' => 'มิถุนายน',
                                                                                            'ก.ค.' => 'กรกฎาคม',
                                                                                            'ส.ค.' => 'สิงหาคม',
                                                                                            'ก.ย.' => 'กันยายน',
                                                                                            'ต.ค.' => 'ตุลาคม',
                                                                                            'พ.ย.' => 'พฤศจิกายน',
                                                                                            'ธ.ค.' => 'ธันวาคม',
                                                                                        ];

                                                                                        // แปลงเดือนใน $dateStr โดยใช้การแทนที่จาก array $thaiMonths
                                                                                        foreach ($thaiMonths as $shortMonth => $fullMonth) {
                                                                                            if (strpos($dateStr, $shortMonth) !== false) {
                                                                                                $dateStr = str_replace($shortMonth, $fullMonth, $dateStr);
                                                                                                break; // ออกจาก loop เมื่อเจอการแทนที่แล้ว
                                                                                            }
                                                                                        }

                                                                                        // แปลงปีคริสต์ศักราช (สองหลัก) เป็นปีพุทธศักราช (สี่หลัก)
                                                                                        preg_match('/\d{2}$/', $dateStr, $matches);
                                                                                        if ($matches) {
                                                                                            $year = $matches[0]; // ดึงตัวเลขสองหลักท้ายสุด ซึ่งคือปีในรูปแบบ 67
                                                                                            $fullYear = (int)$year < 50 ? '25' . $year : '25' . $year; // เพิ่ม '25' ข้างหน้าปี
                                                                                            $dateStr = str_replace($year, $fullYear, $dateStr); // แทนที่ปีด้วยปีที่เพิ่ม '25' ข้างหน้า
                                                                                        }

                                                                                        // แสดงผลลัพธ์
                                                                                        echo $dateStr; // ตัวอย่างเช่น "10 มกราคม 2567"
                                                                                        ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    function getsElementID(index) {
        let content = document.getElementById('content1');
        let content2 = document.getElementById('content2');
        let content3 = document.getElementById('content3');
        let content4 = document.getElementById('content4');
        let content5 = document.getElementById('content5');

        // Reset display for all contents
        content.style.display = 'none';
        content2.style.display = 'none';
        content3.style.display = 'none';
        content4.style.display = 'none';
        content5.style.display = 'none';

        // Set display for the selected content
        switch (index) {
            case 0:
                content.style.display = 'block';
                break;
            case 1:
                content2.style.display = 'block';
                break;
            case 2:
                content3.style.display = 'block';
                break;
            case 3:
                content4.style.display = 'block';
                break;
            default:
                content5.style.display = 'block';
                break;
        }
    }

    function handleSelectionChange() {
        const dropdown = document.getElementById('myDropdown');
        let selectedValue = dropdown.value;

        // วันปีล่าสุด
        let currentDate = new Date();
        let currentYear = currentDate.getFullYear() + 543;

        selectedValue = Math.abs(currentYear - selectedValue);
        //alert('You selected: ' + selectedValue);
        getsElementID(selectedValue);
    }
</script>