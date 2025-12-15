<div class="text-center pages-head">
    <span class="font-pages-head">นโยบายของผู้บริหาร</span>
</div>
<div class="text-center" style="padding-top: 50px">
    <img src="<?php echo base_url('docs/logo.png'); ?>" width="174px" height="174px">
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages mt-1">
    <?php foreach ($executivepolicy as $rs) { ?>
        <div class="bg-pages-in">
            <div class="crop">
                <!-- <div class="scrollable-container-gi"> -->
                <!-- <span class="font-other-head">สภาพทั่วไป</span> -->
                <div class="pages-content break-word mt-5">
                    <?php if (!empty($rs->executivepolicy_detail)) : ?>
                        <span class="font-other-content"><?= $rs->executivepolicy_detail; ?></span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($rs->img)) : ?>
                    <div class="text-center">
                        <?php foreach ($rs->img as $img) : ?>
                            <img src="<?php echo base_url('docs/img/' . $img->executivepolicy_img_img); ?>" style="max-width: 1035px; width: 100%; height: auto; object-fit: contain;"><br>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php foreach ($rs->pdf as $file) : ?>
                    <div class="row">
                        <div class="col-6 mt-2">
                            <div class="d-flex justify-content-start">
                                <span class="font-page-detail-view-news">ดาวโหลดแล้ว <?= $file->executivepolicy_pdf_download; ?> ครั้ง</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-end underline">
                                <a onclick="downloadFile(event, <?= $file->executivepolicy_pdf_id; ?>)" href="<?= base_url('docs/file/' . $file->executivepolicy_pdf_pdf); ?>" download>
                                    <div class="btn-download-el-aw">ดาวน์โหลด</div>
                                </a>
                                <script>
                                    function downloadFile(event, executivepolicy_pdf_id) {
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('GET', '<?= base_url('Pages/increment_download_executivepolicy/'); ?>' + executivepolicy_pdf_id, true);
                                        xhr.send();
                                        window.open(event.currentTarget.href, '_blank');
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                    <div class="blog-text mt-3 mb-5">
                        <object data="<?= base_url('docs/file/' . $file->executivepolicy_pdf_pdf); ?>" type="application/pdf" width="100%" height="1500px"></object>
                    </div>
                <?php endforeach; ?>
                <div class="d-flex justify-content-start">
                    <span class="font-page-detail-view-news">จำนวนผู้เข้าชม <?= $rs->executivepolicy_view; ?> ครั้ง</span>
                </div>
            </div>
        </div>
    <?php } ?>
</div><br><br>