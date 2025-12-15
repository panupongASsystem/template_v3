<div class="text-center pages-head">
    <span class="font-pages-head">กระทู้ถาม - ตอบ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-news">
        <div class="detail-q-a">
            <div class="row">
                <div class="col-3" style="width: 130px;">
                    <span class="font-q-a-chat-color"><svg xmlns="http://www.w3.org/2000/svg" width="17" height="19" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                        </svg>&nbsp;ผู้ตั้งกระทู้</span>
                </div>
                <div class="col-9" style="width: 800px; margin-top: -2px;">
                    <span class="font-q-a-chat-color"><?= $rsData->q_a_by; ?> : </span><span class="font-q-a-chat-black"><?= $rsData->q_a_msg; ?></span>
                </div>
            </div>
            <div class="border-q-a"></div>
            <div class="mt-2 mb-1">
                <span class="font-q-a-chat-black"><?= $rsData->q_a_detail; ?></span>
            </div>
            <?php
            function formatDateThai($date)
            {
                $strMonth = array(
                    "01" => "มกราคม",
                    "02" => "กุมภาพันธ์",
                    "03" => "มีนาคม",
                    "04" => "เมษายน",
                    "05" => "พฤษภาคม",
                    "06" => "มิถุนายน",
                    "07" => "กรกฎาคม",
                    "08" => "สิงหาคม",
                    "09" => "กันยายน",
                    "10" => "ตุลาคม",
                    "11" => "พฤศจิกายน",
                    "12" => "ธันวาคม"
                );
                $strDate = $date->format('d');
                $strMonthThai = $strMonth[$date->format('m')];
                $strYear = $date->format('Y') + 543; // แปลงปี พ.ศ.
                return "$strDate $strMonthThai $strYear";
            }
            ?>

            <div class="mt-4 mb-1">
                <span class="span-time-q-a">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" fill="currentColor" class="bi bi-calendar-minus-fill" viewBox="0 0 16 16">
                        <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM6 10h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1 0-1z" />
                    </svg>
                    <?php
                    $date = new DateTime($rsData->q_a_datesave);
                    echo formatDateThai($date);
                    ?>
                </span>&nbsp;
                <span class="span-time-q-a">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" fill="currentColor" class="bi bi-clock-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                    </svg>
                    <?php
                    $formattedTime = $date->format('H:i'); // เวลา
                    echo $formattedTime;
                    ?>
                    น.
                </span>
            </div>
        </div>
        <?php
        $count = count($rsReply);
        $itemsPerPage = 4; // จำนวนรายการต่อหน้า
        $totalPages = ceil($count / $itemsPerPage);

        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

        // ปรับตำแหน่งที่กำหนดค่า $numToShow
        $numToShow = 3; // จำนวนปุ่มที่ต้องการแสดง
        $half = floor($numToShow / 2);

        $startPage = max($currentPage - $half, 1);
        $endPage = min($startPage + $numToShow - 1, $totalPages);

        $startIndex = ($currentPage - 1) * $itemsPerPage;
        $endIndex = min($startIndex + $itemsPerPage - 1, $count - 1);

        for ($i = $startIndex; $i <= $endIndex; $i++) {
            $rs = $rsReply[$i];
        ?>
            <?php foreach ($rsReply as $reply) : ?>
                <div class="detail-q-a mt-4 mb-4">
                    <div class="row">
                        <div class="col-3" style="width: 130px;">
                            <span class="color-q-a font-label-e-service-complainb">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="19" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                                </svg>&nbsp;ผู้ตอบ
                            </span>
                        </div>
                        <div class="col-9 mt-2" style="width: 800px;">
                            <span class="font-label-e-service-complain one-line-ellipsis"><?= $reply->q_a_reply_by; ?></span>
                        </div>
                    </div>
                    <div class="border-q-a"></div>
                    <div class="mt-2 mb-1">
                        <span class="font-q-a-chat-black"><?= $reply->q_a_reply_detail; ?></span>
                    </div>
                    <div class="mt-4 mb-1">
                        <?php if (isset($reply->q_a_reply_datesave)) : ?>
                            <span class="span-time-q-a">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" fill="currentColor" class="bi bi-calendar-minus-fill" viewBox="0 0 16 16">
                                    <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM6 10h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1 0-1z" />
                                </svg>
                                <?php
                                $date = new DateTime($reply->q_a_reply_datesave);
                                echo formatDateThai($date);
                                ?>
                            </span>&nbsp;
                            <span class="span-time-q-a">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" fill="currentColor" class="bi bi-clock-fill" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                                </svg>
                                <?php
                                $formattedTime = $date->format('H:i');
                                echo $formattedTime;
                                ?>
                                น.
                            </span>
                        <?php else : ?>
                            <span class="span-time-q-a">ข้อมูลวันที่ไม่พร้อมใช้งาน</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php } ?>
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

        <div class="pages-select-q-a-chat underline">
            <form id="reCAPTCHA3" action="<?php echo site_url('Pages/add_reply_q_a'); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                <br>
                <input type="hidden" name="q_a_reply_ref_id" class="form-control font-label-e-service-complain" required value="<?= $rsData->q_a_id; ?>">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <div class="col-sm-2 control-label font-e-service-complain">ชื่อ<span class="red-font">*</span></div>
                            <div class="col-sm-12 mt-2">
                                <input type="text" name="q_a_reply_by" class="form-control font-label-e-service-complain" required placeholder="กรอกชื่อผู้ตอบกลับ" value="<?php echo set_value('q_a_reply_by'); ?>">
                                <span class="red"><?= form_error('q_a_reply_by'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <div class="col-sm-2 control-label font-e-service-complain">อีเมล</div>
                            <div class="col-sm-12 mt-2">
                                <input type="email" name="q_a_reply_email" class="form-control font-label-e-service-complain" required placeholder="example@youremail.com" value="<?php echo set_value('q_a_reply_email'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <label for="exampleFormControlTextarea1" class="form-label font-e-service-complain">รายละเอียด<span class="red-font">*</span></label>
                    <div class="col-sm-12 mt-2">
                        <textarea name="q_a_reply_detail" class="form-control font-label-e-service-complain" id="exampleFormControlTextarea1" rows="6" placeholder="กรอกรายละเอียดเพิ่มเติม..." required><?php echo set_value('q_a_reply_detail'); ?></textarea>
                        <span class="red"><?= form_error('q_a_reply_detail'); ?></span>
                    </div>
                </div>
                <br>
        </div>
        <div class="row">
            <div class="col-9">
                <div class="d-flex justify-content-end">
                    <!-- <div class="g-recaptcha" style="display: none;" data-sitekey="6LeVTA0qAAAAAOdDlKxZMwoexrXjITwDiIfZ3P9C" data-callback="onSubmit"></div> -->
                    <!-- <div class="g-recaptcha" data-sitekey="6LcKoPcnAAAAAKGgUMRtkBs6chDKzC8XOoVnaZg_" data-callback="enableLoginButton"></div> -->
                </div>
            </div>
            <div class="col-3">
                <div class="d-flex justify-content-end">
                    <!-- <button type="submit" id="loginBtn" class="btn" disabled><img src="<?php echo base_url("docs/s.btn-add-q-a.png"); ?>"></button> -->
                    <button data-action='submit' data-callback='onSubmit' data-sitekey="<?php echo get_config_value('recaptcha'); ?>" type="submit" id="loginBtn" class="btn g-recaptcha"><img src="<?php echo base_url("docs/s.btn-add-q-a.png"); ?>"></button>
                </div>
            </div>
        </div>
        </form>

    </div>
</div><br><br><br>

<script>
    // เมื่อ reCAPTCHA ผ่านการตรวจสอบ
    // function enableLoginButton() {
    //     document.getElementById("loginBtn").removeAttribute("disabled");
    // }
    // function onSubmit(token) {
    //     document.getElementById("loginBtn").removeAttribute("disabled");
    // }
    // grecaptcha.ready(function() {
    //     grecaptcha.execute('6LeVTA0qAAAAAOdDlKxZMwoexrXjITwDiIfZ3P9C', {
    //         action: 'submit'
    //     }).then(onSubmit);
    // });
    //     function onSubmit(token) {
    //      document.getElementById("reCAPTCHA3").submit();
    //    }
</script>