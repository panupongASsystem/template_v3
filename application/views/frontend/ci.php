<div class="text-center pages-head">
    <span class="font-pages-head">ข้อมูลชุมชน</span>
</div>

<div class="text-center" style="padding-top: 50px">
    <img src="<?php echo base_url('docs/logo.png'); ?>" width="174px" height="174px">
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<!-- Custom CSS for Modern White Theme -->
<style>
    /* Container Styling */
    .modern-container {
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin: 30px auto;
        max-width: 1400px;
    }

    /* Title Styling */
    .modern-title {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
        letter-spacing: -0.3px;
    }

    /* Dropdown Section */
    .dropdown-section {
        background: white;
        padding: 18px 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin: 20px 0;
        display: inline-block;
    }

    .dropdown-section label {
        font-weight: 600;
        color: #34495e;
        margin-right: 8px;
        font-size: 14px;
    }

    .dropdown-section select {
        padding: 8px 15px;
        border: 2px solid #e8ecef;
        border-radius: 8px;
        font-size: 14px;
        color: #2c3e50;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 130px;
        margin: 0 5px;
    }

    .dropdown-section select:hover {
        border-color: #95a5a6;
    }

    .dropdown-section select:focus {
        outline: none;
        border-color: #7f8c8d;
        box-shadow: 0 0 0 3px rgba(149, 165, 166, 0.1);
    }

    /* Modern Table Styling */
    .modern-table {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
        margin-top: 25px;
    }

    .modern-table table {
        margin-bottom: 0;
        border: none;
        width: 100%;
        table-layout: auto;
    }

    .modern-table thead tr:first-child th {
        background: #95a5a6;
        color: white;
        font-weight: 600;
        padding: 12px 8px;
        border: 15;
        font-size: 14px;
        letter-spacing: 0.3px;
    }

    .modern-table thead tr:last-child th {
        background: #a8b4b6;
        color: white;
        font-weight: 500;
        padding: 10px 8px;
        border: 15;
        font-size: 13px;
    }

    .modern-table tbody tr {
        transition: all 0.3s ease;
    }

    .modern-table tbody tr:hover {
        background-color: #f8f9ff;
    }

    .modern-table tbody td {
        padding: 10px 8px;
        border: 1px solid #f0f2f5;
        color: #2c3e50;
        font-size: 13px;
        vertical-align: middle;
    }

    .modern-table tbody tr:last-child {
        background: #ffeaea;
        font-weight: 700;
    }

    .modern-table tbody tr:last-child td {
        color: #e74c3c;
        border-top: 3px solid #e74c3c;
        padding: 12px 8px;
        font-size: 14px;
    }

    /* Source and Note Section */
    .info-section {
        background: white;
        padding: 20px 30px;
        border-radius: 12px;
        margin-top: 25px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.04);
    }

    .source-label {
        color: #5a6c7d;
        font-size: 13px;
        line-height: 1.6;
        margin: 0;
        text-align: center;
    }

    .source-label a {
        color: #7f8c8d;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .source-label a:hover {
        color: #5a6c7d;
        text-decoration: underline;
    }

    .note-section {
        background: #f5f6f7;
        padding: 15px 25px;
        border-radius: 10px;
        margin-top: 15px;
        border-left: 4px solid #95a5a6;
    }

    .note-text {
        color: #5a6c7d;
        font-size: 13px;
        line-height: 1.7;
        margin: 0;
    }

    .note-text strong {
        color: #7f8c8d;
        font-weight: 600;
    }

    /* Loading State */
    .loading-cell {
        background: linear-gradient(90deg, #f0f2f5 0%, #ffffff 50%, #f0f2f5 100%);
        background-size: 200% 100%;
        animation: loading 1.5s ease-in-out infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .modern-container {
            padding: 20px;
            border-radius: 15px;
        }

        .modern-title {
            font-size: 22px;
        }

        .dropdown-section {
            padding: 15px;
        }

        .dropdown-section select {
            min-width: 120px;
            font-size: 14px;
        }

        .modern-table thead tr th {
            font-size: 12px;
            padding: 12px 8px;
        }

        .modern-table tbody td {
            font-size: 13px;
            padding: 12px 8px;
        }
    }

    /* Table First Column (Village Names) */
    .modern-table tbody td:first-child {
        font-weight: 600;
        color: #34495e;
        background-color: #fafbfc;
    }

    /* Number Cells Alignment */
    .modern-table tbody td:not(:first-child) {
        text-align: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
</style>

<div class="modern-container">
    <div class="text-center">
        <h1 class="modern-title" id="table_title">ข้อมูลสถิติจำนวนประชากร</h1>

        <!-- ส่วนเลือกเดือน-ปี (แสดงเฉพาะเมื่อมีข้อมูลจาก API) -->
        <div id="month_year_selector" class="dropdown-section"
            style="<?php echo (isset($data_source) && $data_source == 'database') ? 'display: none;' : ''; ?>">
            <label for="select_month">📅 เดือน:</label>
            <select id="select_month">
            </select>

            <label for="select_year">📆 ปี พ.ศ.:</label>
            <select id="select_year">
            </select>
        </div>

        <!-- Modern Table -->
        <div class="modern-table">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align: middle;">พื้นที่</th>
                        <th colspan="3" style="text-align:center;">
                            <img src="<?php echo base_url('docs/img/thailand-flag-icon.png'); ?>"
                                style="width: 15px; vertical-align:middle;"> ไทย
                        </th>
                        <th colspan="3">🌍 ไม่ใช่ไทย</th>
                        <th colspan="3">📊 รวม</th>
                    </tr>
                    <tr>
                        <th>ชาย</th>
                        <th>หญิง</th>
                        <th>รวม</th>
                        <th>ชาย</th>
                        <th>หญิง</th>
                        <th>รวม</th>
                        <th>ชาย</th>
                        <th>หญิง</th>
                        <th>รวม</th>
                    </tr>
                </thead>
                <tbody id="table_body">
                    <?php
                    $total_male_thai = 0;
                    $total_female_thai = 0;
                    $total_thai = 0;
                    $total_male_foreign = 0;
                    $total_female_foreign = 0;
                    $total_foreign = 0;
                    $total_male = 0;
                    $total_female = 0;
                    $total_all = 0;

                    foreach ($qCi as $rs) {
                        // ชื่อหมู่บ้าน
                        $village_name = isset($rs->ci_name) ? $rs->ci_name : (isset($rs->lsmmDesc) ? $rs->lsmmDesc : '-');

                        // ข้อมูลไทย
                        $male_thai = isset($rs->male_thai) ? $rs->male_thai : (isset($rs->lssumtotMaleThai) ? $rs->lssumtotMaleThai : (isset($rs->ci_man) ? $rs->ci_man : 0));
                        $female_thai = isset($rs->female_thai) ? $rs->female_thai : (isset($rs->lssumtotFemaleThai) ? $rs->lssumtotFemaleThai : (isset($rs->ci_woman) ? $rs->ci_woman : 0));
                        $total_village_thai = isset($rs->total_thai) ? $rs->total_thai : (isset($rs->lssumtotTotThai) ? $rs->lssumtotTotThai : (isset($rs->ci_total) ? $rs->ci_total : 0));

                        // ข้อมูลรวมทั้งหมด
                        $male_all = isset($rs->male_all) ? $rs->male_all : (isset($rs->lssumtotMale) ? $rs->lssumtotMale : (isset($rs->ci_man) ? $rs->ci_man : 0));
                        $female_all = isset($rs->female_all) ? $rs->female_all : (isset($rs->lssumtotFemale) ? $rs->lssumtotFemale : (isset($rs->ci_woman) ? $rs->ci_woman : 0));
                        $total_village_all = isset($rs->total_all) ? $rs->total_all : (isset($rs->lssumtotTot) ? $rs->lssumtotTot : (isset($rs->ci_total) ? $rs->ci_total : 0));

                        // ข้อมูลไม่ใช่ไทย (คำนวณแล้วจาก Controller)
                        $male_foreign = isset($rs->male_foreign) ? $rs->male_foreign : ($male_all - $male_thai);
                        $female_foreign = isset($rs->female_foreign) ? $rs->female_foreign : ($female_all - $female_thai);
                        $total_village_foreign = isset($rs->total_foreign) ? $rs->total_foreign : ($male_foreign + $female_foreign);

                        // คำนวณผลรวม
                        $total_male_thai += $male_thai;
                        $total_female_thai += $female_thai;
                        $total_thai += $total_village_thai;
                        $total_male_foreign += $male_foreign;
                        $total_female_foreign += $female_foreign;
                        $total_foreign += $total_village_foreign;
                        $total_male += $male_all;
                        $total_female += $female_all;
                        $total_all += $total_village_all;
                        ?>
                        <tr>
                            <td><?= $village_name; ?></td>
                            <td><?= number_format($male_thai); ?></td>
                            <td><?= number_format($female_thai); ?></td>
                            <td><?= number_format($total_village_thai); ?></td>
                            <td><?= number_format($male_foreign); ?></td>
                            <td><?= number_format($female_foreign); ?></td>
                            <td><?= number_format($total_village_foreign); ?></td>
                            <td><?= number_format($male_all); ?></td>
                            <td><?= number_format($female_all); ?></td>
                            <td><?= number_format($total_village_all); ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>📌 รวมทั้งหมด</td>
                        <td><?= number_format($total_male_thai); ?></td>
                        <td><?= number_format($total_female_thai); ?></td>
                        <td><?= number_format($total_thai); ?></td>
                        <td><?= number_format($total_male_foreign); ?></td>
                        <td><?= number_format($total_female_foreign); ?></td>
                        <td><?= number_format($total_foreign); ?></td>
                        <td><?= number_format($total_male); ?></td>
                        <td><?= number_format($total_female); ?></td>
                        <td><?= number_format($total_all); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ที่มา -->
        <div class="info-section">
            <p class="source-label" id="data_source_label">
                <strong>ที่มา:</strong>
                <?php
                if (isset($data_source) && $data_source == 'api') {
                    echo 'ระบบสถิติทางการทะเบียน กรมการปกครอง - <a href="https://stat.bora.dopa.go.th/stat/statnew/statMenu/newStat/home.php" target="_blank">stat.bora.dopa.go.th</a>';
                } else {
                    echo 'ฐานข้อมูล' . get_config_value('fname');
                }
                ?>
            </p>
        </div>

        <!-- หมายเหตุ -->
        <div id="note_section" class="note-section" style="display: none;">
            <p class="note-text" id="note_text"></p>
        </div>
    </div>
</div>

<br><br>

<script>
    $(document).ready(function () {
        const thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];

        // ตรวจสอบว่าเป็น database source หรือไม่
        var isDbSource = <?php echo (isset($data_source) && $data_source == 'database') ? 'true' : 'false'; ?>;

        // วันที่ปัจจุบัน
        let currentDate = new Date();
        let currentMonth = currentDate.getMonth() + 1; // 1-12
        let currentYear = currentDate.getFullYear() + 543; // พ.ศ.

        // คำนวณเดือน-ปีที่เลือกเริ่มต้น (เดือนที่แล้ว)
        let defaultDate = new Date();
        defaultDate.setMonth(defaultDate.getMonth() - 1);
        let defaultMonth = defaultDate.getMonth() + 1;
        let defaultYear = defaultDate.getFullYear() + 543;

        // สร้าง dropdown เฉพาะเมื่อไม่ใช่ database source
        if (!isDbSource) {
            // สร้าง dropdown เดือน (ไม่แสดงเดือนปัจจุบันและเดือนในอนาคต)
            for (let i = 1; i <= 12; i++) {
                // ถ้าเป็นปีปัจจุบัน ให้แสดงเฉพาะเดือนที่ผ่านมาแล้ว (ไม่รวมเดือนปัจจุบัน)
                let canShow = true;
                if (defaultYear === currentYear && i >= currentMonth) {
                    canShow = false;
                }

                if (canShow) {
                    $('#select_month').append(`<option value="${i}" ${i == defaultMonth ? 'selected' : ''}>${thaiMonths[i - 1]}</option>`);
                }
            }

            // สร้าง dropdown ปี (3 ปีย้อนหลัง)
            for (let i = 0; i < 3; i++) {
                let year = currentYear - i;
                $('#select_year').append(`<option value="${year}" ${year == defaultYear ? 'selected' : ''}>${year}</option>`);
            }

            // อัปเดต dropdown เดือนเมื่อเปลี่ยนปี
            $('#select_year').on('change', function () {
                updateMonthDropdown();
            });
        }

        function updateMonthDropdown() {
            let selectedYear = parseInt($('#select_year').val());
            let selectedMonth = parseInt($('#select_month').val());

            $('#select_month').empty();

            // ถ้าเลือกปีปัจจุบัน ให้แสดงเฉพาะเดือนที่ผ่านมาแล้ว (ไม่รวมเดือนปัจจุบัน)
            for (let i = 1; i <= 12; i++) {
                let canShow = true;
                if (selectedYear === currentYear && i >= currentMonth) {
                    canShow = false;
                }

                if (canShow) {
                    $('#select_month').append(`<option value="${i}" ${i == selectedMonth ? 'selected' : ''}>${thaiMonths[i - 1]}</option>`);
                }
            }

            // ถ้าเดือนที่เลือกไว้ไม่มีใน dropdown ใหม่ ให้เลือกเดือนสุดท้าย
            if ($('#select_month option[value="' + selectedMonth + '"]').length === 0) {
                $('#select_month').val($('#select_month option:last').val());
            }
        }

        function updateTableTitle() {
            // ถ้าใช้ข้อมูลจาก database ให้แสดงเฉพาะ "ข้อมูลสถิติจำนวนประชากร"
            if (isDbSource) {
                $('#table_title').html('ข้อมูลสถิติจำนวนประชากร');
            } else {
                let month = parseInt($('#select_month').val());
                let year = $('#select_year').val();
                $('#table_title').html('ข้อมูลสถิติจำนวนประชากร<br><small style="font-size: 18px; color: #7f8c8d;">เดือน ' + thaiMonths[month - 1] + ' ' + year + '</small>');
            }
        }

        function updateNoteSection(dataSource) {
            if (dataSource === 'database') {
                $('#note_text').html('<strong>หมายเหตุ:</strong> ข้อมูลส่วนนี้อยู่ในขั้นตอนการดำเนินการโดยเจ้าหน้าที่ผู้รับผิดชอบ หากต้องการสอบถามเพิ่มเติม กรุณาติดต่อผู้ดูแลระบบ');
                $('#note_section').show();
            } else if (dataSource === 'api') {
                $('#note_text').html('<strong>หมายเหตุ:</strong> ชุดข้อมูลดังกล่าวเป็นข้อมูลสถิติจำนวนประชากรในรูปแบบสรุปรายเดือน และระบบสามารถแสดงผลได้เฉพาะข้อมูลของเดือนก่อนหน้าเท่านั้น');
                $('#note_section').show();
            } else {
                $('#note_section').hide();
            }
        }

        updateTableTitle();
        updateNoteSection(isDbSource ? 'database' : 'api');

        function loadPopulationData() {
            // ถ้าใช้ข้อมูลจาก database ไม่ให้โหลดใหม่
            if (isDbSource) {
                return;
            }

            let month = $('#select_month').val();
            let year = $('#select_year').val();
            updateTableTitle();

            let yy = year.toString().slice(-2);
            let mm = month.toString().padStart(2, '0');
            let yymm = yy + mm;

            $('#table_body').html('<tr><td colspan="10" class="text-center loading-cell" style="padding: 40px;">🔄 กำลังโหลดข้อมูล...</td></tr>');

            $.ajax({
                url: '<?php echo base_url("pages/ci"); ?>',
                method: 'GET',
                data: { yymm: yymm },
                dataType: 'json',
                success: function (response) {
                    // ถ้า API ไม่มีข้อมูล ให้ reload หน้าเว็บเพื่อแสดงข้อมูลจาก DB และซ่อน dropdown
                    if (response.data_source === 'database') {
                        location.reload();
                        return;
                    }

                    let tbody = '';
                    if (response.qCi && response.qCi.length > 0) {
                        let totalMaleThai = 0, totalFemaleThai = 0, totalThai = 0;
                        let totalMaleForeign = 0, totalFemaleForeign = 0, totalForeign = 0;
                        let totalMale = 0, totalFemale = 0, totalAll = 0;

                        response.qCi.forEach(function (item) {
                            // ชื่อหมู่บ้าน
                            let villageName = item.ci_name || item.lsmmDesc || '-';

                            // ข้อมูลไทย
                            let maleThai = parseInt(item.male_thai || item.lssumtotMaleThai || item.ci_man || 0);
                            let femaleThai = parseInt(item.female_thai || item.lssumtotFemaleThai || item.ci_woman || 0);
                            let totalVillageThai = parseInt(item.total_thai || item.lssumtotTotThai || item.ci_total || 0);

                            // ข้อมูลรวมทั้งหมด
                            let maleAll = parseInt(item.male_all || item.lssumtotMale || item.ci_man || 0);
                            let femaleAll = parseInt(item.female_all || item.lssumtotFemale || item.ci_woman || 0);
                            let totalVillageAll = parseInt(item.total_all || item.lssumtotTot || item.ci_total || 0);

                            // ข้อมูลไม่ใช่ไทย (ใช้จาก Controller หรือคำนวณใหม่)
                            let maleForeign = parseInt(item.male_foreign || (maleAll - maleThai));
                            let femaleForeign = parseInt(item.female_foreign || (femaleAll - femaleThai));
                            let totalVillageForeign = parseInt(item.total_foreign || (maleForeign + femaleForeign));

                            // คำนวณผลรวม
                            totalMaleThai += maleThai;
                            totalFemaleThai += femaleThai;
                            totalThai += totalVillageThai;
                            totalMaleForeign += maleForeign;
                            totalFemaleForeign += femaleForeign;
                            totalForeign += totalVillageForeign;
                            totalMale += maleAll;
                            totalFemale += femaleAll;
                            totalAll += totalVillageAll;

                            // สร้างแถวตาราง
                            tbody += `<tr>
                            <td>${villageName}</td>
                            <td>${maleThai.toLocaleString()}</td>
                            <td>${femaleThai.toLocaleString()}</td>
                            <td>${totalVillageThai.toLocaleString()}</td>
                            <td>${maleForeign.toLocaleString()}</td>
                            <td>${femaleForeign.toLocaleString()}</td>
                            <td>${totalVillageForeign.toLocaleString()}</td>
                            <td>${maleAll.toLocaleString()}</td>
                            <td>${femaleAll.toLocaleString()}</td>
                            <td>${totalVillageAll.toLocaleString()}</td>
                        </tr>`;
                        });

                        // แถวยอดรวม
                        tbody += `<tr>
                        <td>📌 ยอดรวมทั้งหมด</td>
                        <td>${totalMaleThai.toLocaleString()}</td>
                        <td>${totalFemaleThai.toLocaleString()}</td>
                        <td>${totalThai.toLocaleString()}</td>
                        <td>${totalMaleForeign.toLocaleString()}</td>
                        <td>${totalFemaleForeign.toLocaleString()}</td>
                        <td>${totalForeign.toLocaleString()}</td>
                        <td>${totalMale.toLocaleString()}</td>
                        <td>${totalFemale.toLocaleString()}</td>
                        <td>${totalAll.toLocaleString()}</td>
                    </tr>`;
                    } else {
                        tbody = '<tr><td colspan="10" class="text-center" style="padding: 40px; color: #95a5a6;">ℹ️ ไม่พบข้อมูล</td></tr>';
                    }
                    $('#table_body').html(tbody);

                    // อัปเดตที่มาและหมายเหตุ
                    $('#data_source_label').html(
                        '<strong>ที่มา:</strong> ' +
                        (response.data_source == 'api'
                            ? 'ระบบสถิติทางการทะเบียน กรมการปกครอง - <a href="https://stat.bora.dopa.go.th/stat/statnew/statMenu/newStat/home.php" target="_blank">stat.bora.dopa.go.th</a>'
                            : 'ฐานข้อมูล<?php echo get_config_value("fname"); ?>'
                        )
                    );
                    updateNoteSection(response.data_source);

                },
                error: function () {
                    $('#table_body').html('<tr><td colspan="10" class="text-center" style="padding: 40px; color: #e74c3c;">⚠️ เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>');
                }
            });
        }

        // ถ้าไม่ได้ใช้ database source ให้ enable การเปลี่ยนแปลงเดือน-ปี
        if (!isDbSource) {
            $('#select_month, #select_year').on('change', loadPopulationData);
        }
    });
</script>