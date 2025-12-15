<div class="text-center pages-head">
    <span class="font-pages-head">กฎหมายที่เกี่ยวข้อง</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages" >
    <div class="container-pages-detail" style="position: relative; z-index: 10;" >
        <div class="font-laws-head"><?= $query_topic->laws_topic_topic; ?></div>
        <div class="pages-content break-word mt-2 laws_ral_content">
            <?php foreach ($query as $rs) { ?>
                <a class="font-laws-content dot-laws" target="_blank" href="<?php echo base_url('docs/file/' . $rs->laws_pdf); ?>"><?= $rs->laws_name; ?></a>
                <br>
            <?php   } ?>
        </div>
    </div>
</div><br><br><br>