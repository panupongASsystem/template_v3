<div class="text-center pages-head">
    <span class="font-pages-head">แผนผังโครงสร้างภาพรวม</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-detail">
        <!-- <div class="font-pages-content-head"><?= $rsData->site_map_name; ?></div> -->
        <?php foreach ($rsImg as $img) { ?>
            <div class="text-center">
                <img class="rounded-all" src="<?php echo base_url('docs/img/' . $img->site_map_img_img); ?>" width="1035" height="100%">
            </div>
            <br>
        <?php } ?>

        <?php foreach ($rsPdf as $file) { ?>
            <div class="row">
                <div class="col-6 mt-2">
                    <div class="d-flex justify-content-start">
                        <span class="font-page-detail-view-news">ดาวโหลดแล้ว <?= $file->site_map_pdf_download; ?> ครั้ง</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex justify-content-end">
                        <a onclick="downloadFile(event, <?= $file->site_map_pdf_id; ?>)" href="<?= base_url('docs/file/' . $file->site_map_pdf_pdf); ?>" download>
                            <img src="<?php echo base_url("docs/btn-download.png"); ?>" class="btn-download">
                        </a>
                        <script>
                            function downloadFile(event, site_map_pdf_id) {
                                // ทำการส่งคำร้องขอ AJAX ไปยัง URL ที่บันทึกการดาวน์โหลดพร้อมกับ ID
                                var xhr = new XMLHttpRequest();
                                xhr.open('GET', '<?= base_url('Pages/increment_download_site_map/'); ?>' + site_map_pdf_id, true);
                                xhr.send();

                                // ทำการเปิดไฟล์ PDF ในหน้าต่างใหม่
                                window.open(event.currentTarget.href, '_blank');
                            }
                        </script>
                    </div>
                </div>
            </div>
            <div class="blog-text mt-3 mb-5">
                <object data="<?= base_url('docs/file/' . $file->site_map_pdf_pdf); ?>" type="application/pdf" width="100%" height="1500px"></object>
            </div>
        <?php } ?>
        <div class="d-flex justify-content-start">
            <span class="font-page-detail-view-news">จำนวนผู้เข้าชม <?= $rsData->site_map_view; ?> ครั้ง</span>
        </div>
    </div>
</div><br><br><br>