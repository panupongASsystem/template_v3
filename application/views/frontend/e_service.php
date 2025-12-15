<div class="text-center pages-head">
    <span class="font-pages-head">แบบฟอร์มออนไลน์</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-news">
        <!-- <div class="scrollable-container-e-service"> -->
        <div class="mt-4"></div>
        <div class="text-center">
            <span class="font-e-service-head">ดาวน์โหลดแบบฟอร์มและยื่นคำร้อง</span>
        </div>
        <div class="mt-4"></div>
        <span class="font-e-service-top ">ท่านสามารถใช้งานระบบ E-Services ในรูปแบบ One Stop Service โดยคลิกเลือกแบบฟอร์มที่ท่านต้องการ ดังนี้</span>
        <div class="bg-how-e-service mt-4">
            <span class="font-e-service-how">ขั้นตอนที่ 1 ดาวน์โหลดเอกสารออนไลน์</span>
        </div>
        <!-- <div class="bg-head-e-service">
            <img src="<?php echo base_url('docs/icon-topic-e-service1.png'); ?>"><span class="font-head-topic">แบบยื่นคำร้อง</span>
        </div>-->
        <!-- <div class="bg-content-e-service">
                 <div class="row mt-1">
                <div class="col-9 mt-2">
                    <span class="font-e-service-content">เหตุร้องทุกข์</span>
                </div>
                <div class="col-3">
                    <a href="<?php echo site_url('Pages/adding_complain'); ?>"><img src="<?php echo base_url("docs/btn-e-service-click.png"); ?>"></a>
                </div>
            </div>  -->
        <!-- <?php foreach ($query1 as $rs) { ?>
                <div class="row mt-1">
                    <div class="col-9 mt-2">
                        <span class="font-e-service-content">การขอข้อมูลเอกสาร</span>
                    </div>
                    <div class="col-3">
                        <a class="btn btn-esv-download" href="<?php echo base_url('docs/file/' . $rs->form_esv_file); ?>" target="_blank">แบบฟอร์ม</a>
                    </div>
                </div>
            <?php  } ?> -->
        <!-- <?php foreach ($query3 as $rs) { ?>
                        <div class="row mt-1">
                            <div class="col-9 mt-2">
                                <span class="font-e-service-content">ขอความอนุเคราะห์ตามอำนาจหน้าที่ของ อปท.</span>
                            </div>
                            <div class="col-3">
                                <a class="btn btn-esv-download" href="<?php echo base_url('docs/file/' . $rs->form_esv_file); ?>" target="_blank">แบบฟอร์ม</a>
                            </div>
                        </div>
                    <?php  } ?> -->
        <!-- </div> -->

        <?php
        $group_counter = 0; // ตัวนับสำหรับกลุ่ม
        foreach ($grouped_topics as $topic_name => $grouped_data) :
            $group_counter++; // เพิ่มตัวนับเมื่อเริ่มกลุ่มใหม่
        ?>
            <div class="bg-head-e-service">
                <img src="<?php echo base_url('docs/icon-topic-e-service2.png'); ?>">
                <span class="font-head-topic"><?= $topic_name; ?></span>
            </div>
            <div class="bg-content-e-service">
                <?php
                $item_counter = 0; // ตัวนับสำหรับรายการในกลุ่ม
                foreach ($grouped_data as $data) :
                    $item_counter++; // เพิ่มตัวนับเมื่อเริ่มรายการใหม่
                ?>
                    <?php if ($group_counter == 1 && $item_counter == 1) : // เงื่อนไขสำหรับกลุ่มแรกและรายการแรก 
                    ?>
                        <div class="row mt-1">
                            <div class="col-9 mt-2">
                                <span class="font-e-service-content">เหตุร้องทุกข์</span>
                            </div>
                            <div class="col-3">
                                <a class="btn btn-esv-download" href="<?php echo site_url('Pages/adding_complain'); ?>">
                                    คลิก
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row mt-2">
                        <div class="col-9 mt-2">
                            <span class="font-e-service-content"><?= $data['form_esv_name']; ?></span>
                        </div>
                        <div class="col-3">
                            <a class="btn btn-esv-download" href="<?php echo base_url('docs/file/' . $data['form_esv_file']); ?>" target="_blank">
                                แบบฟอร์ม
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <!-- <div class="bg-head-e-service">
            <img src="<?php echo base_url('docs/icon-topic-e-service3.png'); ?>"><span class="font-head-topic">แบบฟอร์มลงทะเบียนผู้สูงอายุ</span>
        </div>
        <div class="bg-content-e-service">
            <?php foreach ($query5 as $rs) { ?>
                <div class="row">
                    <div class="col-9 mt-2">
                        <span class="font-e-service-content">แบบฟอร์มลงทะเบียน</span>
                    </div>
                    <div class="col-3">
                        <a class="btn btn-esv-download" href="<?php echo base_url('docs/file/' . $rs->form_esv_file); ?>" target="_blank">แบบฟอร์ม</a>
                    </div>
                </div>
            <?php  } ?>
            <?php foreach ($query6 as $rs) { ?>
                <div class="row mt-1">
                    <div class="col-9 mt-2">
                        <span class="font-e-service-content">แบบฟอร์มหนังสือมอบอำนาจ</span>
                    </div>
                    <div class="col-3">
                        <a class="btn btn-esv-download" href="<?php echo base_url('docs/file/' . $rs->form_esv_file); ?>" target="_blank">แบบฟอร์ม</a>
                    </div>
                </div>
            <?php  } ?>
            <?php foreach ($query7 as $rs) { ?>
                <div class="row mt-1">
                    <div class="col-9 mt-2">
                        <span class="font-e-service-content">แบบฟอร์มขึ้นทะเบียนผู้พิการ</span>
                    </div>
                    <div class="col-3">
                        <a class="btn btn-esv-download" href="<?php echo base_url('docs/file/' . $rs->form_esv_file); ?>" target="_blank">แบบฟอร์ม</a>
                    </div>
                </div>
            <?php  } ?>
        </div> -->
        <div class="mt-4">
            <span class="font-e-service-danger"><b>หมายเหตุ</b> โปรดเตรียมไฟล์เอกสารแนบประกอบคำขอให้ครบถ้วน เช่น สำเนาบัตรประชาชน สำเนาทะเบียนบ้าน สำเนาหน้าบัญชีสมุดธนาคาร หนังสือมอบอำนาจพร้อมติดอากร เป็นต้น</span>
        </div>
        <div class="bg-how-e-service mt-4">
            <span class="font-e-service-how">ขั้นตอนที่ 2 ยื่นเอกสารออนไลน์</span>
        </div>
        <div class="bg-content-e-service" style="margin-bottom: 70px;">
            <div class="row">
                <div class="col-9 mt-2">
                    <span class="font-e-service-content">คลิกเพื่อยื่นเอกสาร</span>
                </div>
                <div class="col-3" style="z-index: 3;">
                <a class="btn btn-esv-download" href="<?php echo site_url('Pages/adding_esv_ods'); ?>">คลิก</a>
                </div>
            </div>
        </div>
    </div>
</div>