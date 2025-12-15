    <div class="row">
        <div class="col-sm-6 col-md-6">
            <form method="get" action="<?= site_url('Assessment_report_backend/index'); ?>">
                <div class="form-group row btn-print">
                    <div class="control-label">เลือกปี : </div>
                    <div class="col-sm-2">
                        <select name="year" id="year" class="form-control">
                            <?php
                            // รับค่า year จาก URL parameter
                            $selected_year = $this->input->get('year');
                            ?>
                            <option value="ทั้งหมด" <?= ($selected_year == 'ทั้งหมด' || empty($selected_year)) ? 'selected' : ''; ?>>ทั้งหมด</option>
                            <?php
                            // แสดงปีในรูปแบบ พ.ศ.
                            for ($i = date('Y') + 543; $i >= 2566; $i--) {
                                $year_value = $i - 543; // แปลงเป็น ค.ศ.
                                $selected = ($selected_year == $year_value) ? 'selected' : '';
                                echo "<option value='{$year_value}' {$selected}>{$i}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <input type="submit" class="btn btn-primary" value="ค้นหา">
                    </div>
                </div>
            </form>
        </div>
        <div class="col-sm-6 col-md-6 d-flex justify-content-end ">
            <div style="text-align: center; margin-top: 10px;">
                <div style="position: relative; display: inline-block;">
                    <i class="fas fa-print" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color:white; "></i>
                    <input class="btn btn-secondary btn-print" type="button" value="พิมพ์เอกสาร" onclick="printReport()" style="padding-left: 30px;" />
                </div>
            </div>
        </div>
    </div>


    <div class="print">
        <h2 class="m-0 font-weight-bold text-black text-center">
            รายงานแบบประเมินความพึงพอใจการให้บริการ
            <small class="d-block mt-2">
                <?php
                if ($selected_year == 'ทั้งหมด' || empty($selected_year)) {
                    echo "(แสดงข้อมูลทั้งหมด)";
                } else {
                    $year_th = $selected_year + 543;
                    echo "ประจำปี พ.ศ. " . $year_th;
                }
                ?>
            </small>
        </h2> <br>
        <div class="flex">
            <div id="chart_age"></div>
            <div id="chart_study"></div>
        </div>
        <div class="flex mt-3">
            <div id="chart_gender"></div>
            <div id="chart_occupation"></div>
        </div>
        <div class="flex mt-3">
            <div id="chart_assessment1"></div>
            <div id="chart_assessment2"></div>
            <div id="chart_assessment3"></div>
        </div>
        <div class="flex">
            <div id="chart_assessment1_message" class="chart-message">
                <span class="statusSquare" id="statusSquare1"></span>
                <span class="statusText" id="statusText1"></span>
            </div>
            <div id="chart_assessment2_message" class="chart-message">
                <span class="statusSquare" id="statusSquare2"></span>
                <span class="statusText" id="statusText2"></span>
            </div>
            <div id="chart_assessment3_message" class="chart-message">
                <span class="statusSquare" id="statusSquare3"></span>
                <span class="statusText" id="statusText3"></span>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        function printReport() {
            // บันทึกค่าความกว้างเดิมของแผนภูมิ
            var originalWidths = [];
            document.querySelectorAll('#chart_gender, #chart_age, #chart_study, #chart_occupation').forEach(function(chart, index) {
                originalWidths[index] = chart.style.width || ''; // เก็บค่าเดิม หรือค่าว่างถ้าไม่มีการตั้งค่า
                chart.style.width = '100%'; // เปลี่ยนความกว้างเป็น 100% ก่อนพิมพ์
            });

            // เรียกใช้คำสั่งพิมพ์
            window.print();

            // ตรวจจับการออกจากโหมดการพิมพ์
            window.matchMedia('print').addEventListener('change', function(event) {
                if (!event.matches) {
                    // เมื่อออกจากโหมดการพิมพ์ ให้เปลี่ยนความกว้างกลับไปที่ค่าเดิม
                    document.querySelectorAll('#chart_gender, #chart_age, #chart_study, #chart_occupation, #chart_assessment1, #chart_assessment2, #chart_assessment3').forEach(function(chart, index) {
                        chart.style.width = originalWidths[index]; // คืนค่าความกว้างเดิม
                    });
                }
            });
        }

        // ข้อมูลสำหรับกราฟเพศ
        var gender_all = <?php echo json_encode($gender_all->total); ?>; // ดึงค่า total มาใช้งานโดยตรง
        var gender_male = <?php echo json_encode($gender_male->total); ?>;
        var gender_female = <?php echo json_encode($gender_female->total); ?>;
        // เพิ่มเพดานของกราฟอีก 25%
        var yAxisMaxGender = Math.ceil(gender_all * 1.25);

        // ข้อมูลสำหรับกราฟอายุ
        var age_20_down_all = <?php echo json_encode($age_20_down_all->total); ?>;
        var age_20_down_male = <?php echo json_encode($age_20_down_male->total); ?>;
        var age_20_down_female = <?php echo json_encode($age_20_down_female->total); ?>;
        var age_21_40_all = <?php echo json_encode($age_21_40_all->total); ?>;
        var age_21_40_male = <?php echo json_encode($age_21_40_male->total); ?>;
        var age_21_40_female = <?php echo json_encode($age_21_40_female->total); ?>;
        var age_41_60_all = <?php echo json_encode($age_41_60_all->total); ?>;
        var age_41_60_male = <?php echo json_encode($age_41_60_male->total); ?>;
        var age_41_60_female = <?php echo json_encode($age_41_60_female->total); ?>;
        var age_60_up_all = <?php echo json_encode($age_60_up_all->total); ?>;
        var age_60_up_male = <?php echo json_encode($age_60_up_male->total); ?>;
        var age_60_up_female = <?php echo json_encode($age_60_up_female->total); ?>;
        // เพิ่มเพดานของกราฟอีก 25%
        var maxValueAge = Math.max(...age_20_down_all, ...age_21_40_all, ...age_41_60_all, ...age_60_up_all);
        var yAxisMaxAge = Math.ceil(maxValueAge * 1.25);

        // ข้อมูลสำหรับกราฟการศึกษา
        var study_primary_all = <?php echo json_encode($study_primary_all->total); ?>;
        var study_primary_male = <?php echo json_encode($study_primary_male->total); ?>;
        var study_primary_female = <?php echo json_encode($study_primary_female->total); ?>;
        var study_high_all = <?php echo json_encode($study_high_all->total); ?>;
        var study_high_male = <?php echo json_encode($study_high_male->total); ?>;
        var study_high_female = <?php echo json_encode($study_high_female->total); ?>;
        var study_bachelor_all = <?php echo json_encode($study_bachelor_all->total); ?>;
        var study_bachelor_male = <?php echo json_encode($study_bachelor_male->total); ?>;
        var study_bachelor_female = <?php echo json_encode($study_bachelor_female->total); ?>;
        var study_up_bachelor_all = <?php echo json_encode($study_up_bachelor_all->total); ?>;
        var study_up_bachelor_male = <?php echo json_encode($study_up_bachelor_male->total); ?>;
        var study_up_bachelor_female = <?php echo json_encode($study_bachelor_female->total); ?>;
        // เพิ่มเพดานของกราฟอีก 25%
        var maxValueStudy = Math.max(...study_primary_all, ...study_high_all, ...study_bachelor_all, ...study_up_bachelor_all);
        var yAxisMaxStudy = Math.ceil(maxValueStudy * 1.25);

        // ข้อมูลสำหรับกราฟการอาชีพ
        var occupation_student_all = <?php echo json_encode($occupation_student_all->total); ?>;
        var occupation_student_male = <?php echo json_encode($occupation_student_male->total); ?>;
        var occupation_student_female = <?php echo json_encode($occupation_student_female->total); ?>;
        var occupation_gov_all = <?php echo json_encode($occupation_gov_all->total); ?>;
        var occupation_gov_male = <?php echo json_encode($occupation_gov_male->total); ?>;
        var occupation_gov_female = <?php echo json_encode($occupation_gov_female->total); ?>;
        var occupation_private_all = <?php echo json_encode($occupation_private_all->total); ?>;
        var occupation_private_male = <?php echo json_encode($occupation_private_male->total); ?>;
        var occupation_private_female = <?php echo json_encode($occupation_private_female->total); ?>;
        var occupation_community_all = <?php echo json_encode($occupation_community_all->total); ?>;
        var occupation_community_male = <?php echo json_encode($occupation_community_male->total); ?>;
        var occupation_community_female = <?php echo json_encode($occupation_community_female->total); ?>;
        var occupation_farmer_all = <?php echo json_encode($occupation_farmer_all->total); ?>;
        var occupation_farmer_male = <?php echo json_encode($occupation_farmer_male->total); ?>;
        var occupation_farmer_female = <?php echo json_encode($occupation_farmer_female->total); ?>;
        var occupation_other_all = <?php echo json_encode($occupation_other_all->total); ?>;
        var occupation_other_male = <?php echo json_encode($occupation_other_male->total); ?>;
        var occupation_other_female = <?php echo json_encode($occupation_other_female->total); ?>;
        // เพิ่มเพดานของกราฟอีก 25%
        var maxValueOccupation = Math.max(...occupation_student_all, ...occupation_gov_all, ...occupation_private_all, ...occupation_community_all, ...occupation_farmer_all, ...occupation_other_all);
        var yAxisMaxOccupation = Math.ceil(maxValueOccupation * 1.25);

        // ข้อมูลสำหรับกราฟด้านการให้บริการ
        var sum_assessment_1 = Number(<?php echo json_encode($sum_assessment_1->total); ?>);
        var count_assessment1_id = Number(<?php echo json_encode($count_assessment_id->total); ?>);
        var total_assessment1_id = Math.ceil((5 * 4) * count_assessment1_id);
        var score_assessment1_id = Math.ceil((5 * 4) * count_assessment1_id - sum_assessment_1);

        // คำนวณเปอร์เซ็นต์และข้อความสำหรับกราฟด้านการให้บริการ
        var percentage1 = (sum_assessment_1 / total_assessment1_id) * 100;
        var message1 = getMessage(percentage1);
        document.getElementById('statusText1').innerText = message1;
        setSquareColor(percentage1, 'statusSquare1');

        // ข้อมูลสำหรับกราฟด้านบุคลากรผู้ให้บริการ
        var sum_assessment_2 = Number(<?php echo json_encode($sum_assessment_2->total); ?>);
        var count_assessment2_id = Number(<?php echo json_encode($count_assessment_id->total); ?>);
        var total_assessment2_id = Math.ceil((5 * 6) * count_assessment2_id);
        var score_assessment2_id = Math.ceil((5 * 6) * count_assessment2_id - sum_assessment_2);

        // คำนวณเปอร์เซ็นต์และข้อความสำหรับกราฟด้านบุคลากรผู้ให้บริการ
        var percentage2 = (sum_assessment_2 / total_assessment2_id) * 100;
        var message2 = getMessage(percentage2);
        document.getElementById('statusText2').innerText = message2;
        setSquareColor(percentage2, 'statusSquare2');

        // ข้อมูลสำหรับกราฟด้านสถานที่และสิ่งอำนวยความสะดวก
        var sum_assessment_3 = Number(<?php echo json_encode($sum_assessment_3->total); ?>);
        var count_assessment3_id = Number(<?php echo json_encode($count_assessment_id->total); ?>);
        var total_assessment3_id = Math.ceil((5 * 5) * count_assessment3_id);
        var score_assessment3_id = Math.ceil((5 * 5) * count_assessment3_id - sum_assessment_3);

        // คำนวณเปอร์เซ็นต์และข้อความสำหรับกราฟด้านสถานที่และสิ่งอำนวยความสะดวก
        var percentage3 = (sum_assessment_3 / total_assessment3_id) * 100;
        var message3 = getMessage(percentage3);
        document.getElementById('statusText3').innerText = message3;
        setSquareColor(percentage3, 'statusSquare3');

        // ฟังก์ชันสำหรับกำหนดข้อความตามเปอร์เซ็นต์
        function getMessage(percentage) {
            if (percentage >= 80 && percentage <= 100) {
                return 'ดีมาก';
            } else if (percentage >= 60 && percentage < 80) {
                return 'ดี';
            } else if (percentage >= 40 && percentage < 60) {
                return 'ปานกลาง';
            } else if (percentage >= 20 && percentage < 40) {
                return 'พอใช้';
            } else {
                return 'ควรปรับปรุง';
            }
        }

        // ฟังก์ชันสำหรับกำหนดสีของสี่เหลี่ยม
        function setSquareColor(percentage, squareId) {
            var square = document.getElementById(squareId);
            if (percentage >= 80 && percentage <= 100) {
                square.style.backgroundColor = '#4CAF50'; // สีเขียวเข้ม
            } else if (percentage >= 60 && percentage < 80) {
                square.style.backgroundColor = '#8BC34A'; // สีเขียวอ่อน
            } else if (percentage >= 40 && percentage < 60) {
                square.style.backgroundColor = '#FFEB3B'; // สีเหลือง
            } else if (percentage >= 20 && percentage < 40) {
                square.style.backgroundColor = '#FF9800'; // สีส้ม
            } else {
                square.style.backgroundColor = '#F44336'; // สีแดง
            }
        }

        // console.log(sum_assessment_1);
        // console.log(typeof sum_assessment_1);
        // console.log("คะแนนทั้งหมด:" + score_assessment_id);
        // console.log("คะแนนที่ได้:" + sum_assessment_1);

        // กราฟเพศ
        var genderOptions = {
            series: [{
                name: 'รวม',
                data: [gender_all]
            }, {
                name: 'ชาย',
                data: [gender_male]
            }, {
                name: 'หญิง',
                data: [gender_female]
            }],
            chart: {
                type: 'bar',
                height: 350,
                width: '100%',
            },
            title: { // ใช้ title ออกนอก chart
                text: 'กราฟแสดงจำนวนประชากรตามเพศ', // ชื่อกราฟ
                align: 'center', // จัดกลาง
                style: {
                    fontSize: '20px', // ขนาดตัวอักษร
                    fontWeight: 'bold', // หนักตัว
                    color: '#333', // สีของตัวอักษร
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: [''], // ชื่อกราฟด้านล่าง
            },
            yaxis: {
                max: yAxisMaxGender, // ตั้งค่าเพดานของกราฟ
                min: 0, // ตั้งค่าเพดานของกราฟ
                title: {
                    text: 'จำนวนคน' // ชื่อกราฟด้านซ้าย
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " ครั้ง";
                    }
                }
            }
        };

        var chartGender = new ApexCharts(document.querySelector("#chart_gender"), genderOptions);
        chartGender.render();

        // กราฟอายุ
        var ageOptions = {
            series: [{
                name: 'รวม',
                data: [age_20_down_all, age_21_40_all, age_41_60_all, age_60_up_all]
            }, {
                name: 'ชาย',
                data: [age_20_down_male, age_21_40_male, age_41_60_male, age_60_up_male]
            }, {
                name: 'หญิง',
                data: [age_20_down_female, age_21_40_female, age_41_60_female, age_60_up_female]
            }],
            chart: {
                type: 'bar',
                height: 350,
                width: '100%'
            },
            title: { // ใช้ title ออกนอก chart
                text: 'กราฟแสดงจำนวนประชากรตามช่วงอายุ', // ชื่อกราฟ
                align: 'center', // จัดกลาง
                style: {
                    fontSize: '20px', // ขนาดตัวอักษร
                    fontWeight: 'bold', // หนักตัว
                    color: '#333', // สีของตัวอักษร
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['ต่ำกว่า 20 ปี', 'อายุ 21-40 ปี', 'อายุ 41-60 ปี', 'มากกว่า 60 ปี'], // ชื่อกราฟด้านซ้าย
            },
            yaxis: {
                max: yAxisMaxAge, // ตั้งค่าเพดานของกราฟ
                min: 0,
                title: {
                    text: 'จำนวนคน' // ชื่อกราฟด้านซ้าย
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " ครั้ง";
                    }
                }
            }
        };

        var chartAge = new ApexCharts(document.querySelector("#chart_age"), ageOptions);
        chartAge.render();

        // กราฟระดับการศึกษา
        var studyOptions = {
            series: [{
                name: 'รวม',
                data: [study_primary_all, study_high_all, study_bachelor_all, study_up_bachelor_all]
            }, {
                name: 'ชาย',
                data: [study_primary_male, study_high_male, study_bachelor_male, study_up_bachelor_male]
            }, {
                name: 'หญิง',
                data: [study_primary_female, study_high_female, study_bachelor_female, study_up_bachelor_female]
            }],
            chart: {
                type: 'bar',
                height: 350,
                width: '100%'
            },
            title: { // ใช้ title ออกนอก chart
                text: 'กราฟแสดงจำนวนประชากรตามระดับการศึกษา', // ชื่อกราฟ
                align: 'center', // จัดกลาง
                style: {
                    fontSize: '20px', // ขนาดตัวอักษร
                    fontWeight: 'bold', // หนักตัว
                    color: '#333', // สีของตัวอักษร
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['ประถมศึกษา', 'มัธยมศึกษา/เทียบเท่า', 'ปริญญาตรี', 'สูงกว่าปริญญาตรี'], // ชื่อกราฟด้านซ้าย
            },
            yaxis: {
                max: yAxisMaxStudy, // ตั้งค่าเพดานของกราฟ
                min: 0,
                title: {
                    text: 'จำนวนคน' // ชื่อกราฟด้านซ้าย
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " ครั้ง";
                    }
                }
            }
        };

        var chartStudy = new ApexCharts(document.querySelector("#chart_study"), studyOptions);
        chartStudy.render();

        // กราฟระดับการศึกษา
        var occupationOptions = {
            series: [{
                name: 'รวม',
                data: [occupation_student_all, occupation_gov_all, occupation_private_all, occupation_community_all, occupation_farmer_all, occupation_other_all]
            }, {
                name: 'ชาย',
                data: [occupation_student_male, occupation_gov_male, occupation_private_male, occupation_community_male, occupation_farmer_male, occupation_other_male]
            }, {
                name: 'หญิง',
                data: [occupation_student_female, occupation_gov_female, occupation_private_female, occupation_community_female, occupation_farmer_female, occupation_other_female]
            }],
            chart: {
                type: 'bar',
                height: 350,
                width: '100%'
            },
            title: { // ใช้ title ออกนอก chart
                text: 'กราฟแสดงจำนวนประชากรตามอาชีพ', // ชื่อกราฟ
                align: 'center', // จัดกลาง
                style: {
                    fontSize: '20px', // ขนาดตัวอักษร
                    fontWeight: 'bold', // หนักตัว
                    color: '#333', // สีของตัวอักษร
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['นักเรียน/นักศึกษา', 'ข้าราชการ/เจ้าหน้าที่รัฐ', 'ผู้ประกอบการเอกชน', 'องค์กรเครือข่ายชุมชน', 'เกษตรกร', 'อื่นๆ'], // ชื่อกราฟด้านซ้าย
            },
            yaxis: {
                max: yAxisMaxOccupation, // ตั้งค่าเพดานของกราฟ
                min: 0,
                title: {
                    text: 'จำนวนคน' // ชื่อกราฟด้านซ้าย
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " ครั้ง";
                    }
                }
            }
        };

        var chartOccupation = new ApexCharts(document.querySelector("#chart_occupation"), occupationOptions);
        chartOccupation.render();

        // กราฟด้านการให้บริการ
        var as1Options = {
            series: [0, sum_assessment_1, score_assessment1_id],
            chart: {
                width: '80%',
                type: 'pie',
            },
            title: { // ใช้ title ออกนอก chart
                text: 'กราฟด้านการให้บริการ', // ชื่อกราฟ
                align: 'left', // จัดกลาง
                style: {
                    fontSize: '20px', // ขนาดตัวอักษร
                    fontWeight: 'bold', // หนักตัว
                    color: '#333', // สีของตัวอักษร
                }
            },
            labels: [
                'คะแนนเต็ม: ' + total_assessment1_id,
                'คะแนนที่ได้: ' + sum_assessment_1,
                'คะแนนที่หายไป: ' + score_assessment1_id,
            ],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chartAs1 = new ApexCharts(document.querySelector("#chart_assessment1"), as1Options);
        chartAs1.render();

        // กราฟด้านบุคลากรผู้ให้บริการ
        var as2Options = {
            series: [0, sum_assessment_2, score_assessment2_id],
            chart: {
                width: '80%',
                type: 'pie',
            },
            title: { // ใช้ title ออกนอก chart
                text: 'กราฟด้านบุคลากรผู้ให้บริการ', // ชื่อกราฟ
                align: 'left', // จัดกลาง
                style: {
                    fontSize: '20px', // ขนาดตัวอักษร
                    fontWeight: 'bold', // หนักตัว
                    color: '#333', // สีของตัวอักษร
                }
            },
            labels: [
                'คะแนนเต็ม: ' + total_assessment2_id,
                'คะแนนที่ได้: ' + sum_assessment_2,
                'คะแนนที่หายไป: ' + score_assessment2_id,
            ],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chartAs2 = new ApexCharts(document.querySelector("#chart_assessment2"), as2Options);
        chartAs2.render();

        // กราฟด้านสถานที่และสิ่งอำนวยความสะดวก
        var as3Options = {
            series: [0, sum_assessment_3, score_assessment3_id],
            chart: {
                width: '80%',
                type: 'pie',
            },
            title: { // ใช้ title ออกนอก chart
                text: 'กราฟด้านสถานที่และสิ่งอำนวยความสะดวก', // ชื่อกราฟ
                align: 'left', // จัดกลาง
                style: {
                    fontSize: '20px', // ขนาดตัวอักษร
                    fontWeight: 'bold', // หนักตัว
                    color: '#333', // สีของตัวอักษร
                }
            },
            labels: [
                'คะแนนเต็ม: ' + total_assessment3_id,
                'คะแนนที่ได้: ' + sum_assessment_3,
                'คะแนนที่หายไป: ' + score_assessment3_id,
            ],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chartAs3 = new ApexCharts(document.querySelector("#chart_assessment3"), as3Options);
        chartAs3.render();
    </script>

    <!-- <div id="chart_gender"></div>
    <div id="chart_age"></div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // ข้อมูลสำหรับกราฟเพศ
        var m_gender_all = <?php echo json_encode($m_gender_all); ?>;
        var m_gender_male = <?php echo json_encode($m_gender_male); ?>;
        var m_gender_female = <?php echo json_encode($m_gender_female); ?>;
        
        // ข้อมูลสำหรับกราฟอายุ
        var m_age_20_down = <?php echo json_encode($m_age_20_down); ?>;
        var m_age_21_40 = <?php echo json_encode($m_age_21_40); ?>;
        var m_age_41_60 = <?php echo json_encode($m_age_41_60); ?>;
        var m_age_60_up = <?php echo json_encode($m_age_60_up); ?>;

        // กราฟเพศ
        var genderOptions = {
            series: [
            //     {
            //     name: 'รวม',
            //     data: m_gender_all
            // }, 
            {
                name: 'ชาย',
                data: m_gender_male
            }, {
                name: 'หญิง',
                data: m_gender_female
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            yaxis: {
                title: {
                    text: 'กราฟเพศ'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " ครั้ง";
                    }
                }
            }
        };

        var chartGender = new ApexCharts(document.querySelector("#chart_gender"), genderOptions);
        chartGender.render();

        // กราฟอายุ
        var ageOptions = {
            series: [{
                name: 'ต่ำกว่า 20 ปี',
                data: m_age_20_down
            }, {
                name: 'อายุ 21-40 ปี',
                data: m_age_21_40
            }, {
                name: 'อายุ 41-59 ปี',
                data: m_age_41_60
            }, {
                name: 'อายุ 60 ปีขึ้นไป',
                data: m_age_60_up
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            yaxis: {
                title: {
                    text: 'กราฟช่วงอายุ'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " ครั้ง";
                    }
                }
            }
        };

        var chartAge = new ApexCharts(document.querySelector("#chart_age"), ageOptions);
        chartAge.render();
    </script> -->