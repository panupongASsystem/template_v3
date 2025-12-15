<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>ข่าวประชาสัมพันธ์</h4>
            <div style=" margin: auto;
                        width: 30%;
                        padding: 10px">
                <img src="<?= base_url('docs/img/' . $rsedit->news_img); ?>" width="250px" height="210">
                <br>
                <div style="margin-top: 10px;">
                    <?php foreach ($qimg as $img) { ?>
                        <img src="<?= base_url('docs/img/' . $img->news_img_img); ?>" width="140px" height="100px">&nbsp;
                    <?php } ?>
                </div>
            </div>
            <h5><?= $rsedit->news_name; ?></h5>
            <span><?= $rsedit->news_detail; ?></span>
        </div>
    </div>
</div>