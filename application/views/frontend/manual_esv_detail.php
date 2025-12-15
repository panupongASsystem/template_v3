<div class="text-center pages-head">
    <span class="font-pages-head">คู่มือการใช้งาน e-Service</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-detail">
        <!-- <div class="font-pages-content-head"><?= $rsData->news_name; ?></div> -->
        <div class="pages-content break-word mt-2">
            <div class="row">
                <!-- <div class="col-1"></div> -->
                <div class="col-9 mt-2">
                    <span class="font-pages-content-detail">วิดีโอคู่มือการใช้งาน e-Service ท่านสามารถดาวน์โหลดเป็นไฟล์ pdf ได้จากลิงค์นี้ </span>
                </div>
                <div class="col-2">
                    <?php foreach ($rsPdf as $file) : ?>
                        <a onclick="downloadFile(event, <?= $file->manual_esv_pdf_id; ?>)" class="underline" href="<?= base_url('docs/file/' . $file->manual_esv_pdf_pdf); ?>" download>
                            <div class="btn-download-el-aw">ดาวน์โหลด</div>
                        </a>
                        <script>
                            function downloadFile(event, manual_esv_pdf_id) {
                                var xhr = new XMLHttpRequest();
                                xhr.open('GET', '<?= base_url('Pages/increment_download_manual_esv/'); ?>' + manual_esv_pdf_id, true);
                                xhr.send();
                                window.open(event.currentTarget.href, '_blank');
                            }
                        </script>
                </div>
                <div class="col-1"></div>
            <?php endforeach; ?>
            </div>
            <!-- <br> -->
            <!-- <?php if (!empty($rsDoc)) : ?> -->
            <!-- <span class="font-pages-content-detail">ไฟล์เอกสารเพิ่มเติม:</span> -->
            <!-- <?php foreach ($rsDoc as $doc) :
                            $fileInfo = pathinfo($doc->news_file_doc);
                            $fileExtension = strtolower($fileInfo['extension']);
                            $iconImage = "";
                            switch ($fileExtension) {
                                case 'pdf':
                                    $iconImage = "docs/icon-file-pdf.png";
                                    break;
                                case 'doc':
                                case 'docx':
                                    $iconImage = "docs/icon-file-doc.png";
                                    break;
                                case 'xls':
                                case 'xlsx':
                                    $iconImage = "docs/icon-file-xls.png";
                                    break;
                                case 'ppt':
                                case 'pptx':
                                    $iconImage = "docs/icon-file-ppt.png";
                                    break;
                            }
                    ?> -->
            <!-- <br> -->
            <!-- <img src="<?= base_url($iconImage); ?>" style="padding: 0 30px;">
                    <a class="font-doc" href="<?= base_url('docs/file/' . $doc->news_file_doc); ?>" target="_blank"><?= $doc->news_file_doc; ?></a>, &nbsp; -->
            <!-- <?php endforeach; ?>
            <?php endif; ?> -->
            <div class="text-center mt-5">
                <?php if ($rsData->manual_esv_link != "") : ?>
                    <?php if (preg_match("/youtu\.be\/|youtube\.com\/watch/", $rsData->manual_esv_link)) :
                        parse_str(parse_url($rsData->manual_esv_link, PHP_URL_QUERY), $query);
                        $video_id = $query['v'] ?? '';
                        if (!empty($video_id)) : ?>
                            <div class="text-center">
                                <iframe width="840" height="473" src="https://www.youtube-nocookie.com/embed/<?= $video_id; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                            </div>
                        <?php else : ?>
                            <span class="font-pages-content-detail">ลิงค์เพิ่มเติม:</span>&nbsp;<a class="font-26" href="<?= $rsData->manual_esv_link; ?>" target="_blank"><?= $rsData->manual_esv_link; ?></a>
                        <?php endif; ?>
                    <?php else : ?>
                        <span class="font-pages-content-detail">ลิงค์เพิ่มเติม:</span>&nbsp;<a class="font-26" href="<?= $rsData->manual_esv_link; ?>" target="_blank"><?= $rsData->manual_esv_link; ?></a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <br>
        </div>

        <!-- <?php foreach ($rsImg as $img) : ?>
            <div class="text-center">
                <img class="rounded-all" src="<?= base_url('docs/img/' . $img->news_img_img); ?>" width="1035" height="100%">
            </div>
            <br>
        <?php endforeach; ?> -->

        <!-- <?php foreach ($rsPdf as $file) : ?>
            <div class="row">
                <div class="col-6 mt-2">
                    <div class="d-flex justify-content-start">
                        <span class="font-page-detail-view-news">ดาวโหลดแล้ว <?= $file->news_pdf_download; ?> ครั้ง</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex justify-content-end">
                        <a onclick="downloadFile(event, <?= $file->news_pdf_id; ?>)" href="<?= base_url('docs/file/' . $file->news_pdf_pdf); ?>" download>
                              <div class="btn-download-el-aw">ดาวน์โหลด</div>
                        </a>
                        <script>
                            function downloadFile(event, news_pdf_id) {
                                var xhr = new XMLHttpRequest();
                                xhr.open('GET', '<?= base_url('Pages/increment_download_news/'); ?>' + news_pdf_id, true);
                                xhr.send();
                                window.open(event.currentTarget.href, '_blank');
                            }
                        </script>
                    </div>
                </div>
            </div>
            <div class="blog-text mt-3 mb-5">
                <object data="<?= base_url('docs/file/' . $file->news_pdf_pdf); ?>" type="application/pdf" width="100%" height="1500px"></object>
            </div>
        <?php endforeach; ?> -->
        <div class="d-flex justify-content-start">
            <span class="font-page-detail-view-news">จำนวนผู้เข้าชม <?= $rsData->manual_esv_view; ?> ครั้ง</span>
        </div>
    </div>
</div><br><br><br>