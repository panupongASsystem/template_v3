<div class="text-center pages-head">
    <span class="font-pages-head">รายงานผลการดำเนินงานจัดซื้อจัดจ้าง</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages">
    <div class="container-pages-detail">
        <div class="font-pages-content-head"><?= $rsData->p_rpo_name; ?></div>
        <div class="pages-content break-word mt-2">
            <span class="font-pages-content-detail"><?= $rsData->p_rpo_detail; ?></span>
            <br>
            <?php
            if ($rsData->p_rpo_link != "") {
                echo '<span class="font-pages-content-detail">ลิ้งค์เพิ่มเติม:</span>&nbsp;<a class="font-26" href="' . $rsData->p_rpo_link . '" target="_blank">' . $rsData->p_rpo_link . '</a>';
            }
            ?>
            <br>
            <?php
            // ส่วนที่แก้ไข - ใช้ Microsoft Office Online Viewer สำหรับทุกประเภทไฟล์ Office
            if (!empty($rsDoc)) { ?>
                <span class="doc-section-title">ไฟล์เอกสารเพิ่มเติม:</span>
                <?php foreach ($rsDoc as $doc) {
                    // ดึงข้อมูลของไฟล์
                    $fileInfo = pathinfo($doc->p_rpo_file_doc);
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
                    <div class="doc-preview-container">
                        <div class="doc-header-wrapper">
                            <div class="doc-file-info">
                                <img src="<?php echo base_url($iconImage); ?>" class="doc-icon-image">
                                <div class="doc-file-details">
                                    <a class="doc-title-link" href="<?= base_url('docs/file/' . $doc->p_rpo_file_doc); ?>" target="_blank"><?= $doc->p_rpo_file_doc; ?></a>
                                    <div class="doc-download-stats">
                                        <span class="doc-download-count">ดาวน์โหลดแล้ว <?= isset($doc->p_rpo_file_download) ? $doc->p_rpo_file_download : 0; ?> ครั้ง</span>
                                    </div>
                                </div>
                            </div>
                            <a onclick="downloadDocFile(event, <?= $doc->p_rpo_file_id; ?>)" href="<?= base_url('docs/file/' . $doc->p_rpo_file_doc); ?>" download class="doc-download-button">ดาวน์โหลด</a>
                        </div>

                        <?php
                        // กำหนดประเภทไฟล์ที่รองรับ Microsoft Office Online Viewer
                        $officeExtensions = ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'];

                        // ตรวจสอบประเภทไฟล์และแสดงพรีวิวด้วย Microsoft Office Online Viewer
                        if (in_array($fileExtension, $officeExtensions)) {
                            // สร้าง URL สำหรับ Microsoft Office Online Viewer
                            $fileUrl = base_url('docs/file/' . $doc->p_rpo_file_doc);
                            $encodedUrl = urlencode($fileUrl);
                            
                            // ใช้ Microsoft Office Online Viewer สำหรับทุกประเภทไฟล์ Office
                            $viewerUrl = "https://view.officeapps.live.com/op/embed.aspx?src=" . $encodedUrl;

                            // กำหนดหัวข้อตามประเภทไฟล์
                            $fileTypeHeading = '';
                            if (in_array($fileExtension, ['doc', 'docx'])) {
                                $fileTypeHeading = 'Word';
                            } elseif (in_array($fileExtension, ['ppt', 'pptx'])) {
                                $fileTypeHeading = 'PowerPoint';
                            } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                $fileTypeHeading = 'Excel';
                            }
                        ?>
                            <div class="doc-preview-card">
                                <div class="doc-preview-header">
                                    <strong class="doc-preview-title">พรีวิว <?= $fileTypeHeading; ?>: <?= $doc->p_rpo_file_doc; ?></strong>
                                </div>
                                <div class="doc-preview-body">
                                    <iframe src="<?= $viewerUrl; ?>" class="doc-preview-iframe" style="width: 100%; height: 600px; border: none;"></iframe>
                                </div>
                                <div class="doc-preview-footer">
                                    <div class="doc-alert doc-alert-info">
                                        <i class="fas fa-info-circle doc-alert-icon"></i> หมายเหตุ: หากพรีวิวไม่แสดงผล กรุณาดาวน์โหลดไฟล์
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="doc-alert doc-alert-info">
                                <i class="fas fa-exclamation-circle doc-alert-icon"></i> ไม่สามารถแสดงพรีวิวไฟล์นี้ได้ กรุณาดาวน์โหลดเพื่อเปิดดู
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                
                <script>
                    function downloadDocFile(event, p_rpo_file_id) {
                        // ทำการส่งคำร้องขอ AJAX ไปยัง URL ที่บันทึกการดาวน์โหลดพร้อมกับ ID
                        var xhr = new XMLHttpRequest();
                        xhr.open('GET', '<?= base_url('Pages/increment_download_doc/'); ?>' + p_rpo_file_id, true);
                        xhr.send();

                        // ดาวน์โหลดไฟล์
                        var link = document.createElement('a');
                        link.href = event.currentTarget.href;
                        link.download = '';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        
                        // ป้องกันการ redirect
                        event.preventDefault();
                        return false;
                    }
                </script>
            <?php } ?>
        </div>

        <?php foreach ($rsImg as $img) { ?>
            <div class="text-center">
                <img class="rounded-all" src="<?php echo base_url('docs/img/' . $img->p_rpo_img_img); ?>" width="1035" height="100%">
            </div>
            <br>
        <?php } ?>

        <?php foreach ($rsPdf as $file) { ?>
            <div class="row">
                <div class="col-6 mt-2">
                    <div class="d-flex justify-content-start">
                        <span class="font-page-detail-view-news">ดาวโหลดแล้ว <?= $file->p_rpo_pdf_download; ?> ครั้ง</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex justify-content-end">
                        <a onclick="downloadFile(event, <?= $file->p_rpo_pdf_id; ?>)" href="<?= base_url('docs/file/' . $file->p_rpo_pdf_pdf); ?>" download>
                            <img src="<?php echo base_url("docs/btn-download.png"); ?>" class="btn-download">
                        </a>
                        <script>
                            function downloadFile(event, p_rpo_pdf_id) {
                                // ทำการส่งคำร้องขอ AJAX ไปยัง URL ที่บันทึกการดาวน์โหลดพร้อมกับ ID
                                var xhr = new XMLHttpRequest();
                                xhr.open('GET', '<?= base_url('Pages/increment_download_p_rpo/'); ?>' + p_rpo_pdf_id, true);
                                xhr.send();

                                // ทำการเปิดไฟล์ PDF ในหน้าต่างใหม่
                                window.open(event.currentTarget.href, '_blank');
                            }
                        </script>
                    </div>
                </div>
            </div>
            <div class="blog-text mt-3 mb-5">
                <object data="<?= base_url('docs/file/' . $file->p_rpo_pdf_pdf); ?>" type="application/pdf" width="100%" height="1500px"></object>
            </div>
        <?php } ?>
        <div class="d-flex justify-content-start">
            <span class="font-page-detail-view-news">จำนวนผู้เข้าชม <?= $rsData->p_rpo_view; ?> ครั้ง</span>
        </div>
    </div>
</div><br><br><br>