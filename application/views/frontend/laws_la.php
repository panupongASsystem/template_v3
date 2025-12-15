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
                <div class="font-laws-head"><?= $rsData->laws_la_name; ?></div>
                <div class="pages-content break-word mt-2 laws_ral_content">
                    <a href="<?= $rsData->laws_la_link; ?>" target="_blank" rel="noopener noreferrer">
                        <img class="border-radius34" src="<?php echo base_url('docs/img/' . $rsData->laws_la_img); ?>" width="100%">
                    </a>
                </div>
            </div>
        </div>
    </div>
</div><br><br><br>