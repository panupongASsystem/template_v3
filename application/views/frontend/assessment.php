<div class="text-center pages-head">
    <span class="font-pages-head">แบบประเมินความพึงพอใจการให้บริการ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages ">
    <div class="container-pages-news" style="position: relative; z-index: 10;">
        <div class=" underline">
            <span class="font-assessment"><b>ข้อชี้แจง</b> กรุณาทำเครื่องหมาย <i class="fa-solid fa-check"></i> ในข้อที่ตรงกับความเป็นจริงและในช่องที่ตรงกับความคิดเห็นของของท่านมากที่สุด</span>
            <form id="reCAPTCHA3" action="<?php echo site_url('Pages/add_assessment'); ?> " method="post" class="form-horizontal">
                <br>
                <span class="font-assessment"><b>ตอนที่ 1 ข้อมูลทั่วไปของ ผู้ตอบ</b></span>
                <br>
                <div class="form-group">
                    <div class="col-sm-3 control-label font-assessment">1.เพศ <span class="red-font">*</span></div>
                    <div class="col-sm-12">
                        <label>
                            <input type="radio" class="radio" value="ชาย" name="assessment_gender" /> ชาย
                        </label>
                        <label>
                            <input type="radio" class="radio" value="หญิง" name="assessment_gender" /> หญิง
                        </label>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <div class="col-sm-3 control-label font-assessment">2.อายุ <span class="red-font">*</span></div>
                    <div class="col-sm-12">
                        <label>
                            <input type="radio" class="radio" value="ต่ำกว่า 20 ปี" name="assessment_age" /> ต่ำกว่า 20 ปี
                        </label>
                        <label>
                            <input type="radio" class="radio" value="21 - 40 ปี" name="assessment_age" /> 21 - 40 ปี
                        </label>
                        <label>
                            <input type="radio" class="radio" value="41 - 60 ปี" name="assessment_age" /> 41 - 60 ปี
                        </label>
                        <label>
                            <input type="radio" class="radio" value="มากกว่า 60 ปี" name="assessment_age" /> มากกว่า 60 ปี
                        </label>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <div class="col-sm-3 control-label font-assessment">3.ระดับการศึกษา <span class="red-font">*</span></div>
                    <div class="col-sm-12">
                        <label>
                            <input type="radio" class="radio" value="ประถมศึกษา" name="assessment_study" /> ประถมศึกษา
                        </label>
                        <label>
                            <input type="radio" class="radio" value="มัธยมศึกษา/เทียบเท่า" name="assessment_study" /> มัธยมศึกษา/เทียบเท่า
                        </label>
                        <label>
                            <input type="radio" class="radio" value="ปริญญาตรี" name="assessment_study" /> ปริญญาตรี
                        </label>
                        <label>
                            <input type="radio" class="radio" value="สูงกว่าปริญญาตรี" name="assessment_study" /> สูงกว่าปริญญาตรี
                        </label>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <div class="col-sm-3 control-label font-assessment">4.อาชีพ <span class="red-font">*</span></div>
                    <div class="col-sm-12">
                        <label>
                            <input type="radio" class="radio" value="นักเรียน/นักศึกษา" name="assessment_occupation" /> นักเรียน/นักศึกษา
                        </label>
                        <label>
                            <input type="radio" class="radio" value="ข้าราชการ/เจ้าหน้าที่รัฐ" name="assessment_occupation" /> ข้าราชการ/เจ้าหน้าที่รัฐ
                        </label>
                        <label>
                            <input type="radio" class="radio" value="ผู้ประกอบการเอกชน" name="assessment_occupation" /> ผู้ประกอบการเอกชน
                        </label>
                        <label>
                            <input type="radio" class="radio" value="องค์กรเครือข่ายชุมชน" name="assessment_occupation" /> องค์กรเครือข่ายชุมชน
                        </label>
                        <label>
                            <input type="radio" class="radio" style="margin-left: -20px;" value="เกษตรกร" name="assessment_occupation" /> เกษตรกร
                        </label>
                        <label>
                            <input type="radio" class="radio" value="อื่นๆ" name="assessment_occupation" /> อื่นๆ
                        </label>
                        <label>
                            <input type="text" name="assessment_occupation_etc" class="form-control" id="occupation-etc" disabled>
                        </label>
                    </div>
                </div>
                <br>
                <span class="font-assessment"><b>ตอนที่ 2 คำถามเกี่ยวกับคุณภาพการบริการ กรุณาทำเครื่องหมาย <i class="fa-solid fa-check"></i></b></span><br>
                <div class="row mt-2">
                    <div class="col-3"></div>
                    <div class="col-4">
                        <span class="font-assessment">ระดับความพึงพอใจ ดีมาก</span><br>
                        <span class="font-assessment">ระดับความพึงพอใจ ดี</span><br>
                        <span class="font-assessment">ระดับความพึงพอใจ ปานกลาง</span><br>
                        <span class="font-assessment">ระดับความพึงพอใจ พอใช้</span><br>
                        <span class="font-assessment">ระดับความพึงพอใจ ควรปรับปรุง</span><br>
                    </div>
                    <div class="col-5">
                        <span class="font-assessment">มีค่าเท่ากับ 5 คะแนน</span><br>
                        <span class="font-assessment">มีค่าเท่ากับ 4 คะแนน</span><br>
                        <span class="font-assessment">มีค่าเท่ากับ 3 คะแนน</span><br>
                        <span class="font-assessment">มีค่าเท่ากับ 2 คะแนน</span><br>
                        <span class="font-assessment">มีค่าเท่ากับ 1 คะแนน</span><br>
                    </div>
                </div>
                <br>
                <div class="assessment-table">
                    <table>
                        <tr class="text-center">
                            <th rowspan="2" style="width: 60%;">คุณภาพการบริการ</th>
                            <th colspan="5">ระดับความพึงพอใจ</th>
                        </tr>
                        <tr class="text-center">
                            <td><b>1</b></td>
                            <td><b>2</b></td>
                            <td><b>3</b></td>
                            <td><b>4</b></td>
                            <td><b>5</b></td>
                        </tr>
                        <tr class="category">
                            <td colspan="6">1.ด้านการให้บริการ</td>
                        </tr>
                        <tr>
                            <td>1.1 การให้บริการเป็นไปตามระยะเวลาที่กำหนด</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_11" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_11" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_11" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_11" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_11" /></td>
                        </tr>
                        <tr>
                            <td>1.2 ความรวดเร็วในการให้บริการ</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_12" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_12" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_12" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_12" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_12" /></td>
                        </tr>
                        <tr>
                            <td>1.3 ได้รับบริการตรงตามความต้องการ</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_13" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_13" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_13" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_13" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_13" /></td>
                        </tr>
                        <tr>
                            <td>1.4 ความพึงพอใจโดยภาพรวมของท่านที่ได้รับจากการบริการของหน่วยงาน</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_14" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_14" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_14" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_14" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_14" /></td>
                        </tr>
                        <tr class="category">
                            <td colspan="6">2.ด้านบุคลากรผู้ให้บริการ</td>
                        </tr>
                        <tr>
                            <td>2.1 ความเหมาะสมในการแต่งกายของผู้ให้บริการ</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_21" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_21" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_21" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_21" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_21" /></td>
                        </tr>
                        <tr>
                            <td>2.2 ความเต็มใจและความพร้อมในการให้บริการอย่างสุภาพ</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_22" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_22" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_22" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_22" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_22" /></td>
                        </tr>
                        <tr>
                            <td>2.3 ความรู้ความสามารถในการให้บริการ เช่น สามารถตอบคำถาม ชี้แจงข้อสงสัยให้คำแนะนำได้เป็นต้น</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_23" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_23" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_23" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_23" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_23" /></td>
                        </tr>
                        <tr>
                            <td>2.4 การให้บริการเหมือนกันทุกรายโดยไม่เลือกปฏิบัติ</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_24" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_24" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_24" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_24" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_24" /></td>
                        </tr>
                        <tr>
                            <td>2.5 ความซื่อสัตย์สุจริตในการปฏิบัติหน้าที่</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_25" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_25" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_25" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_25" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_25" /></td>
                        </tr>
                        <tr>
                            <td>2.6 ความสุภาพ กิริยามารยาทของเจ้าหน้าที่ผู้ให้บริการ (เป็นมิตร/มีรอยยิ้ม/อัธยาศัยดี)</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_26" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_26" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_26" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_26" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_26" /></td>
                        </tr>
                        <tr class="category">
                            <td colspan="6">3.ด้านสถานที่และสิ่งอำนวยความสะดวก</td>
                        </tr>
                        <tr>
                            <td>3.1 สถานที่ตั้งของหน่วยงาน สะดวกในการเดินทางมารับบริการ</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_31" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_31" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_31" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_31" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_31" /></td>
                        </tr>
                        <tr>
                            <td>3.2 ความชัดเจนของป้ายสัญลักษณ์ ประชาสัมพันธ์บอกจุดบริการ</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_32" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_32" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_32" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_32" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_32" /></td>
                        </tr>
                        <tr>
                            <td>3.3 ความเพียงพอของสิ่งอำนวยความสะดวก เช่น ที่จอดรถ ห้องน้ำ เก้าอี้ที่นั่งคอยรับบริการ บริการน้ำดื่ม เป็นต้น</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_33" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_33" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_33" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_33" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_33" /></td>
                        </tr>
                        <tr>
                            <td>3.4 ความสะอาดของสถานที่โดยรวม</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_34" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_34" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_34" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_34" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_34" /></td>
                        </tr>
                        <tr>
                            <td>3.5 ความเป็นระเบียบของสถานที่และอุปกรณ์ในการติดต่อใช้บริการ</td>
                            <td class="text-center"><input type="radio" class="radio" value="1" name="assessment_35" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="2" name="assessment_35" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="3" name="assessment_35" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="4" name="assessment_35" /></td>
                            <td class="text-center"><input type="radio" class="radio" value="5" name="assessment_35" /></td>
                        </tr>
                    </table>
                </div>
                <br>
                <span class="font-assessment"><b>ตอนที่ 3 ข้อเสนอแนะในการให้บริการ</b></span>
                <textarea name="assessment_suggestion" class="form-control font-assessment lined-textarea" rows="5"></textarea>
                <div class="row mt-4">
                    <div class="col-9">
                        <div class="d-flex justify-content-end">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="d-flex justify-content-end">
                            <button data-action='submit' data-callback='onSubmit' data-sitekey="<?php echo get_config_value('recaptcha'); ?>" type="submit" id="loginBtn" class="btn g-recaptcha"><img src="<?php echo base_url("docs/s.btn-add-q-a.png"); ?>"></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .font-assessment {
        color: #000;
        font-size: 24px;
        font-style: normal;
        font-weight: 500;
    }

    .radio {
        width: 20px;
        height: 20px;
        text-indent: 10px;
    }

    label:first-of-type {
        font-size: 24px;
        /* ไม่มี text-indent */
        text-indent: 0;
    }

    label {
        font-size: 24px;
        text-indent: 20px;
    }

    /* ใช้ class หรือ id เฉพาะสำหรับตารางแบบประเมิน */
    .assessment-table {
        width: 100%;
        max-width: 1060px;
        margin: 0 auto;
    }

    .assessment-table table,
    .assessment-table th,
    .assessment-table td {
        border: 1px solid black;
        font-size: 24px;
        padding: 10px 20px;
    }

    .assessment-table td {
        height: 60px;
    }

    /* แยก style สำหรับ search */
    .gcse-search-container {
        width: 309px;
        margin: auto;
        padding-top: 30px;
    }

    /* ถ้าจำเป็นต้องกำหนดขนาดให้กับ gcse-search โดยเฉพาะ */
    .gcse-search-container .gcse-search {
        width: 100%;
    }

    table .category {
        background-color: #eee;
        width: 100%;
    }

    .lined-textarea {
        background-image: linear-gradient(transparent 95%, #ccc 95%);
        background-size: 100% 50px;
        /* ปรับความสูงของแต่ละเส้น */
        line-height: 50px;
        /* ระยะห่างระหว่างบรรทัด */
        padding: 5px;
        font-size: 24px;
    }
</style>

<script>
    // จับเหตุการณ์เมื่อมีการเปลี่ยนการเลือกใน radio
    document.querySelectorAll('input[name="assessment_occupation"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var occupationEtcInput = document.getElementById('occupation-etc');

            if (this.value === 'อื่นๆ') {
                // ถ้าเลือก 'อื่นๆ' ให้เปิดใช้งาน input สำหรับกรอกข้อมูล
                occupationEtcInput.disabled = false;
            } else {
                // ถ้าเลือกอย่างอื่น ให้ปิดการใช้งาน input และล้างค่าข้อมูล
                occupationEtcInput.disabled = true;
                occupationEtcInput.value = '';
            }
        });
    });

    // เลือกฟอร์มด้วย class 'reCAPTCHA3'
    document.querySelector('#reCAPTCHA3').addEventListener('submit', function() {
        var occupationEtcInput = document.getElementById('occupation-etc');

        // ตรวจสอบว่าช่อง disabled หรือไม่ ถ้าใช่ให้ตั้งค่าว่าง
        if (occupationEtcInput.disabled) {
            occupationEtcInput.disabled = false; // เปิดใช้งานเพื่อให้ส่งค่าว่างไปด้วย
            occupationEtcInput.value = ''; // ตั้งค่าว่าง
        }
    });
</script>