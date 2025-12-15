<div class="bg-pages ">
    <div class="container-pages-detail">
        <div class="page-center">
            <div class="head-pages">
                <span class="font-pages-head">กฏหมายที่เกี่ยวข้อง</span>
            </div>
        </div>
        <div class="row">
            <div class="path1-1">
                <span class="font-path-1 underline"><a href="<?php echo site_url('Home'); ?>">หน้าแรก</a></span>
            </div>
            <div class="path2-4">
                <span class="font-path-2 underline"><a href="#">กฏหมายที่เกี่ยวข้อง</a></span>
            </div>
        </div>
        <div class="bg-pages-in ">
            <div class="scrollable-container-news">
                <div class="font-laws-head">กฎหมายที่ประเมินกรณี รมว.มท.รักษาการร่วม </div>
                <div class="pages-content break-word mt-2 laws_ral_content">
                    <?php
                    $Index = 1;
                    ?>
                    <?php foreach ($query as $rs) { ?>
                        <span class="font-laws-content"><?= $Index; ?>.<?= $rs->laws_ec_name; ?></span>
                        <br>
                    <?php
                        $Index++;
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div><br><br><br>