<div class="text-center pages-head">
    <span class="font-pages-head">วิดีทัศน์</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-news">
        <?php
        $count = count($query);
        $itemsPerPage = 27; // จำนวนรายการต่อหน้า
        $totalPages = ceil($count / $itemsPerPage);

        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // ปรับตำแหน่งที่กำหนดค่า $numToShow
        $numToShow = 3; // จำนวนปุ่มที่ต้องการแสดง
        $half = floor($numToShow / 2);

        $startPage = max($currentPage - $half, 1);
        $endPage = min($startPage + $numToShow - 1, $totalPages);

        $startIndex = ($currentPage - 1) * $itemsPerPage;
        $endIndex = min($startIndex + $itemsPerPage - 1, $count - 1);
        ?>

        <div class="video-grid">
            <?php
            for ($i = $startIndex; $i <= $endIndex; $i++) {
                $video = $query[$i];
            ?>
                <div class="video-card">
                    <?php if (!empty($video->video_link)) : ?>
                        <?php
                        // Check if it's a YouTube link
                        if (preg_match("/youtu\.be\/|youtube\.com\/watch|youtube\.com\/shorts/", $video->video_link)) :
                            if (preg_match("/youtu\.be\/([a-zA-Z0-9_-]+)/", $video->video_link, $matches)) {
                                $video_id = $matches[1];
                            } elseif (preg_match("/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/", $video->video_link, $matches)) {
                                $video_id = $matches[1];
                            } elseif (preg_match("/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/", $video->video_link, $matches)) {
                                $video_id = $matches[1];
                            }
                            if (!empty($video_id)) : ?>
                                <div class="text-center">
                                    <iframe class="video-iframe" width="100%" height="315"
                                        src="https://www.youtube-nocookie.com/embed/<?= htmlspecialchars($video_id); ?>"
                                        title="YouTube video player" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen
                                        style="border-radius: 16px;"></iframe>
                                </div>
                            <?php endif; ?>

                            <?php
                        // Check if it's a Facebook video
                        elseif (preg_match("/facebook\.com\/(?:watch\?v=|.*\/videos\/|reel\/)([0-9]+)/", $video->video_link, $matches)) :
                            $fb_video_id = $matches[1] ?? '';
                            if (!empty($fb_video_id)) : ?>
                                <div class="text-center">
                                    <iframe src="https://www.facebook.com/plugins/video.php?href=https://www.facebook.com/watch?v=<?= htmlspecialchars($fb_video_id); ?>"
                                        width="100%" height="315"
                                        style="border:none;overflow:hidden;border-radius:16px;"
                                        scrolling="no" frameborder="0" allowTransparency="true"
                                        allow="encrypted-media" allowfullscreen>
                                    </iframe>
                                </div>
                            <?php endif; ?>

                        <?php else : ?>
                            <p>Unsupported video link</p>
                        <?php endif; ?>

                    <?php elseif (!empty($video->video_video)) : ?>
                        <!-- ถ้ามีไฟล์วิดีโอใน docs/video -->
                        <div class="text-center">
                            <video width="100%" height="315" controls style="border-radius:16px;">
                                <source src="<?= base_url('docs/video/' . $video->video_video); ?>" type="video/mp4">
                                <?= htmlspecialchars($video->video_video); ?>
                            </video>
                        </div>

                    <?php else : ?>
                        <p class="text-muted">No video available</p>
                    <?php endif; ?>

                    <h3 class="two-line-ellipsis"><?= htmlspecialchars($video->video_name); ?></h3>
                </div>
            <?php } ?>
        </div>
        <br><br><br><br>

        <!-- จัดการหน้า -->
        <div class="pagination-container d-flex justify-content-end">
            <div class="pagination-pages">
                <ul class="pagination">
                    <!-- ปุ่ม "กลับไปหน้าแรก" -->
                    <?php if ($currentPage > 1) : ?>
                        <li class="page-item pagination-item">
                            <a class="" href="?page=1" aria-label="First">
                                <img src="<?php echo base_url('docs/s.pages-first.png'); ?>" class="pages-first">
                                <span aria-hidden="true"></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- ปุ่ม Previous -->
                    <?php if ($currentPage > 1) : ?>
                        <li class="page-item" style="width: 55px; margin-left: -12px;">
                            <a class="" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                                <img src="<?php echo base_url('docs/s.pages-pre.png'); ?>" alt="Previous" class="pages-pre">
                                <span aria-hidden="true"></span>
                            </a>
                        </li>
                    <?php endif; ?>



                    <!-- แสดงปุ่ม "กลับไปหน้าแรก" ถ้าหน้าปัจจุบันไม่ได้ต่อเนื่องจากหน้าแรก -->
                    <?php
                    $numToShow = 3; // จำนวนปุ่มที่ต้องการแสดง
                    $half = floor($numToShow / 2);

                    // ปุ่มหน้าเริ่มต้น
                    $startPage = max($currentPage - $half, 1);

                    // ปุ่มหน้าสุดท้าย
                    $endPage = min($startPage + $numToShow - 1, $totalPages);

                    // แสดงปุ่ม "กลับไปหน้าแรก" ถ้าหน้าปัจจุบันไม่ได้ต่อเนื่องจากหน้าแรก
                    if ($startPage > 1) {
                    ?>
                        <li class="page-item pagination-item">
                            <a class="page-link" href="?page=1">1</a>
                        </li>
                        <?php if ($startPage > 2) : ?>
                            <li class="page-item pagination-item">
                                <a class="page-link" href="?page=2">2</a>
                            </li>
                            <li class="page-item pagination-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php
                    }

                    // แสดงปุ่มหน้า
                    for ($i = $startPage; $i <= $endPage; $i++) {
                    ?>
                        <li class="page-item pagination-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php
                    }

                    // แสดงปุ่ม "..." ถ้าหน้าไม่ได้ต่อเนื่อง และรองสุดท้าย
                    if ($endPage < $totalPages - 1) {
                    ?>
                        <li class="page-item pagination-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item pagination-item">
                            <a class="page-link" href="?page=<?php echo $totalPages - 1; ?>"><?php echo $totalPages - 1; ?></a>
                        </li>
                    <?php
                    }

                    // แสดงปุ่มสุดท้าย
                    if ($endPage < $totalPages) {
                    ?>
                        <li class="page-item pagination-item <?php echo ($totalPages == $currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
                        </li>
                    <?php
                    }
                    ?>
                    <!-- ปุ่ม Next -->
                    <?php if ($currentPage < $totalPages) : ?>
                        <li class="page-item" style="width: 55px; margin-left: -10px;">
                            <a class="" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                                <img src="<?php echo base_url('docs/s.pages-next.png'); ?>" alt="Next" class="pages-next">
                                <span aria-hidden="true"></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- ปุ่ม "ไปหน้าสุดท้าย" -->
                    <?php if ($currentPage < $totalPages) : ?>
                        <li class="page-item pagination-item">
                            <a class="" href="?page=<?php echo $totalPages; ?>" aria-label="Last">
                                <img src="<?php echo base_url('docs/s.pages-last.png'); ?>" alt="Last" class="pages-last">
                                <span aria-hidden="true"></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- ฟอร์มกรอกหมายเลขหน้า -->
        <div class="pagination-jump-to-page d-flex justify-content-end">
            <form action="" method="GET" class="d-flex" id="pageForm" onsubmit="return validatePageInput();">

                <label style="font-size: 24px;">ไปหน้าที่&nbsp;&nbsp;</label>
                <input type="number" name="page" min="1" max="<?php echo $totalPages; ?>" value="<?php echo $currentPage; ?>" class="form-control" style="width: 60px; margin-right: 10px;" id="pageInput">
                <input type="image" src="<?php echo base_url('docs/s.pages-go.png'); ?>" alt="Go" class="pages-go" style="width: 40px; height: 40px;">
            </form>
        </div>
    </div>
</div><br><br><br>