<div class="text-center pages-head">
    <span class="font-pages-head">ติดต่อเรา</span>
</div>
<div class="text-center" style="padding-top: 50px">
    <img src="<?php echo base_url('docs/logo.png'); ?>" width="174px" height="174px">
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages" style="margin-top: 1px; margin-bottom: 20px;">
    <div class="container-pages-news">
        <div class="path text-center">
            <div class="mt-4">
                <span class="font-contact-1"><?php echo get_config_value('fname'); ?><br><br><?php echo get_config_value('address'); ?> ตำบล<?php echo get_config_value('subdistric'); ?> อำเภอ<?php echo get_config_value('district'); ?> จังหวัด<?php echo get_config_value('province'); ?> รหัสไปรษณีย์ <?php echo get_config_value('zip_code'); ?></span><br>
                <br>
                <span class="font-contact-2">
                    <?php if (!empty(get_config_value('email_1'))) { ?>
    อีเมล : 
        <?php 
        echo get_config_value('email_1');
        if (!empty(get_config_value('email_2'))) {
            echo ', ' . get_config_value('email_2');
        }
        ?>
    <br><br>
<?php } ?>
                    <?php if (!empty(get_config_value('phone_1'))) { ?>
    โทรศัพท์ : 
        <?php 
        echo get_config_value('phone_1');
        if (!empty(get_config_value('phone_2'))) {
            echo ', ' . get_config_value('phone_2');
        }
        ?>
    <br><br>
<?php } ?>
					<?php if (!empty(get_config_value('fax'))) { ?>
    โทรสาร : <?php echo get_config_value('fax'); ?><br><br>
<?php } ?>
                    Facebook : <a href="<?php echo get_config_value('facebook'); ?>" target="_blank" rel="noopener noreferrer">www.facebook.com/<?php echo get_config_value('fname'); ?></a>
                </span>
            </div>
            <div class="mt-5 mb-3">
                <span class="font-contact-map">แผนที่หน่วยงาน</span>
            </div>
            <iframe src="<?php echo get_config_value('google_map'); ?>" width="873" height="595" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div><br><br><br>

<script>
    // เมื่อ reCAPTCHA ผ่านการตรวจสอบ
    function enableLoginButton() {
        document.getElementById("loginBtn").removeAttribute("disabled");
    }
</script>