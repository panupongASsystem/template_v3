<div class="text-center pages-head">
    <span class="font-pages-head">วิดีทัศน์</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-detail">
        <div class="font-pages-content-head"><?= $rsData->video_name; ?></div>
        <div class="pages-content break-word mt-5">
            <!-- <span class="font-pages-content-detail"><?= $rsData->video_detail; ?></span> -->
            <div class="text-center">
                <?php if ($rsData->video_link != "") : ?>
                    <?php if (preg_match("/youtu\.be\/|youtube\.com\/watch/", $rsData->video_link)) :
                        parse_str(parse_url($rsData->video_link, PHP_URL_QUERY), $query);
                        $video_id = $query['v'] ?? '';
                        if (!empty($video_id)) : ?>
                            <div class="text-center">
                                <iframe width="840" height="473" src="https://www.youtube-nocookie.com/embed/<?= $video_id; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                            </div>
                        <?php else : ?>
                            <span class="font-pages-content-detail">ลิงค์เพิ่มเติม:</span>&nbsp;<a class="font-26" href="<?= $rsData->video_link; ?>" target="_blank"><?= $rsData->video_link; ?></a>
                        <?php endif; ?>
                    <?php else : ?>
                        <span class="font-pages-content-detail">ลิงค์เพิ่มเติม:</span>&nbsp;<a class="font-26" href="<?= $rsData->video_link; ?>" target="_blank"><?= $rsData->video_link; ?></a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="d-flex justify-content-start">
            <span class="font-page-detail-view-news">จำนวนผู้เข้าชม <?= $rsData->video_view; ?> ครั้ง</span>
        </div>
    </div>
</div><br><br><br>