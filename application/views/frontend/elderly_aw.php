<div class="text-center pages-head">
    <span class="font-pages-head">เบี้ยยังชีพผู้สูงอายุ / ผู้พิการ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages" style="margin-top: 0px;">
    <div class="row">
        <div class="col-6 d-flex justify-content-end underline">
            <a href="<?php echo site_url('Pages/elderly_aw_ods'); ?>">
                <div class="bg-btn-head-elderly-aw">
                    <span>ยื่นเอกสารเบี้ยยังชีพผู้สูงอายุ / ผู้พิการ</span>
                </div>
            </a>
        </div>
        <div class="col-6 d-flex justify-content-start underline">
            <a href="#">
                <div class="bg-btn-head-elderly-aw-active">
                    <span>ตรวจสอบเบี้ยยังชีพผู้สูงอายุ / ผู้พิการ</span>
                </div>
            </a>
        </div>
    </div>
    <div class="container-pages-news" style="position: relative; z-index: 10;">
        <!-- <div class="d-flex justify-content-end">
            <div class="form-group">
                <div class="col-sm-12">
                    <select class="form-select custom-select" id="ChangPagesComplain">
                        <option value="" disabled selected>ติดตามสถานะเรื่องร้องเรียน</option>
                        <option value="complain">ร้องเรียน/ร้องทุกข์</option>
                        <option value="esv_ods">ยื่นเอกสารออนไลน์</option>
                        <option value="suggestions">รับฟังความคิดเห็น</option>
                        <option value="corruption">แจ้งเรื่องทุจริตหน่วยงานภาครัฐ</option>
                    </select>
                </div>
            </div>
        </div> -->
        <div class="pages-follow-elderly-aw underline">
            <!-- ฟอร์มกรอกข้อมูล -->
            <form id="reCAPTCHA3" action="<?php echo site_url('Pages/elderly_aw'); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                <div class="form-group">
                    <br>
                    <!-- กรอกหมายเลขประจำตัวประชาชนของผู้มีสิทธิ -->
                    <div class="col-sm-12 control-label font-e-service-elderly_aw text-center">กรุณากรอกหมายเลขประจำตัวประชาชนของผู้มีสิทธิ</div>
                    <div class="center-center mt-4">
                        <div class="col-sm-4 mb-4">
                            <input type="text" name="elderly_aw_id_num_eligible" class="form-control font-label-elderly_aw input-radius" required placeholder="เลขประจำตัวประชาชน 13 หลัก" pattern="\d{10}" title="กรุณากรอกเป็นตัวเลข" value="<?php echo set_value('elderly_aw_id_num_eligible'); ?>">
                            <span class="red"><?= form_error('elderly_aw_id_num_eligible'); ?></span>
                        </div>
                    </div>

                    <!-- เลือกงวดเงินที่จ่าย (ปี พ.ศ.) -->
                    <div class="col-sm-12 control-label font-e-service-elderly_aw text-center">กรุณาเลือกงวดเงินที่จ่าย (ปี พ.ศ.)</div>
                    <div class="center-center mt-4">
                        <div class="col-sm-4 mb-4">
                            <select name="elderly_aw_period_payment" class="form-control font-label-elderly_aw input-radius" required>
                                <option value="">ทุกปี</option>
                                <?php
                                // เริ่มต้นปีในพ.ศ.
                                $current_year = date('Y') + 543; // แปลงจาก ค.ศ. เป็น พ.ศ.
                                $start_year = $current_year - 10; // เริ่มที่ 10 ปีที่แล้ว
                                $end_year = $current_year + 0; // จบที่ 10 ปีข้างหน้า

                                for ($year = $start_year; $year <= $end_year; $year++) {
                                    echo "<option value='{$year}'>{$year}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center" style="margin-left: 80px;">
                        <div class="col-sm-2 ">

                            <!-- reCAPTCHA 3  หน้านี้มีเปลี่ยน 1 จุด นี่จุด 1 -->
                            <button data-action='submit' data-callback='onSubmit' data-sitekey="<?php echo get_config_value('recaptcha'); ?>" type="submit" id="loginBtn" class="btn g-recaptcha">
                                <img src="<?php echo base_url("docs/s.btn-add-q-a.png"); ?>">
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>

        <div class="pages-follow-elderly-aw-detail" <?php if (!empty($elderly_aw_data) || isset($_POST['elderly_aw_id_num_eligible'])) : ?>style="display: block;" <?php else : ?>style="display: none;" <?php endif; ?>>
            <?php if (!empty($elderly_aw_data)) : ?>
                <table class="table table-bordered text-center">
                    <thead>
                        <tr class="">
                            <th style="width: 15%;">เลขประจำตัวประชาชน ผู้มีสิทธิ</th>
                            <th style="width: 15%;">ชื่อ - นามสกุลของ ผู้มีสิทธิ</th>
                            <!-- <th style="width: 14%;">เลขประจำตัวประชาชนเจ้าของบัญชี</th> -->
                            <th style="width: 15%;">ชื่อ - นามสกุล เจ้าของบัญชี</th>
                            <th style="width: 15%;">หน่วยงาน</th>
                            <th style="width: 6%;">ธนาคาร</th>
                            <th style="width: 10%;">ประเภทการจ่าย</th>
                            <!-- <th style="width: 11%;">เลขที่บัญชีธนาคาร/เลขบัตร</th> -->
                            <th style="width: 8%;">งวดเงินที่จ่าย</th>
                            <th style="width: 6%;">จำนวนเงิน</th>
                            <th style="width: 10%;">สาเหตุระงับจ่าย</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($elderly_aw_data as $row) : ?>
                            <tr role="row">
                                <td><?php echo $row['elderly_aw_id_num_eligible']; ?></td>
                                <td><?php echo $row['elderly_aw_name_eligible']; ?></td>
                                <!-- <td><?php echo $row['elderly_aw_id_num_owner']; ?></td> -->
                                <td><?php echo $row['elderly_aw_name_owner']; ?></td>
                                <td><?php echo $row['elderly_aw_agency']; ?></td>
                                <td><?php echo $row['elderly_aw_bank']; ?></td>
                                <td><?php echo $row['elderly_aw_type_payment']; ?></td>
                                <!-- <td><?php echo $row['elderly_aw_bank_num']; ?></td> -->
                                <td><?php echo $row['elderly_aw_period_payment']; ?></td>
                                <td><?php echo $row['elderly_aw_money']; ?></td>
                                <td><?php echo $row['elderly_aw_note']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif (!empty($error_message)) : ?>
                <div class="error-message">
                    <h1 class="red text-center">ไม่พบหมายเลขประจำตัวประชาชนที่ท่านเลือก !</h1>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- <?php if (!empty($elderly_aw_data)) : ?>
            <div class="results">
                <h3>ข้อมูลจากฐานข้อมูล:</h3>
                <p>หมายเลขประจำตัวประชาชน: <?php echo $elderly_aw_data['elderly_aw_id_num_eligible']; ?></p>
                <p>ชื่อ: <?php echo $elderly_aw_data['elderly_aw_name_eligible']; ?></p>
            </div>
            <?php elseif (!empty($error_message)) : ?>
            <div class="error-message">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?> -->