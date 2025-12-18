<div class="text-center pages-head">
    <span class="font-pages-head">ข่าวจัดซื้อจัดจ้าง</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<div class="bg-pages">
    <div class="container-pages-news">
        <!-- ฟอร์มค้นหา -->
        <div class="search-container">
            <h5 class="text-center font-head-egp-buy">ค้นหาประกาศจัดชื้อจัดจ้าง</h5>
            <br>
            <div class="form-group row">
                <div class="col-sm-2 control-label mt-2 font-label-egp-buy">ประเภทประกาศ</div>
                <div class="col-sm-8">
                    <div class="custom-select-egp ">
                        <?php $selectedOption = $this->session->userdata('selected_option'); ?>
                        <select id="searchOption" class="form-control">
							<option value="procurement_tbl_all_search" <?= ($selectedOption == 'procurement_tbl_all_search') ? 'selected' : ''; ?>>ทั้งหมด</option>
                            <option value="procurement_tbl_w0_search" <?= ($selectedOption == 'procurement_tbl_w0_search') ? 'selected' : ''; ?>>ประกาศรายชื่อผู้ชนะการเสนอราคา</option>
                            <option value="procurement_tbl_p0_search" <?= ($selectedOption == 'procurement_tbl_p0_search') ? 'selected' : ''; ?>>แผนการจัดซื้อจัดจ้าง</option>
                            <option value="procurement_tbl_15_search" <?= ($selectedOption == 'procurement_tbl_15_search') ? 'selected' : ''; ?>>ประกาศราคากลาง</option>
                            <option value="procurement_tbl_b0_search" <?= ($selectedOption == 'procurement_tbl_b0_search') ? 'selected' : ''; ?>>ร่างเอกสารประกวดราคา (e Bidding) และร่างเอกสารซื้อหรือจ้างด้วยวิธีสอบราคา</option>
                            <option value="procurement_tbl_d0_search" <?= ($selectedOption == 'procurement_tbl_d0_search') ? 'selected' : ''; ?>>ประกาศเชิญชวน</option>
                            <option value="procurement_tbl_d1_search" <?= ($selectedOption == 'procurement_tbl_d1_search') ? 'selected' : ''; ?>>ยกเลิกประกาศเชิญชวน</option>
                            <option value="procurement_tbl_w1_search" <?= ($selectedOption == 'procurement_tbl_w1_search') ? 'selected' : ''; ?>>ยกเลิกประกาศรายชื่อผู้ชนะการเสนอราคา</option>
                            <option value="procurement_tbl_d2_search" <?= ($selectedOption == 'procurement_tbl_d2_search') ? 'selected' : ''; ?>>เปลี่ยนแปลงประกาศเชิญชวน</option>
                            <option value="procurement_tbl_w2_search" <?= ($selectedOption == 'procurement_tbl_w2_search') ? 'selected' : ''; ?>>เปลี่ยนแปลงประกาศรายชื่อผู้ชนะการเสนอราคา</option>
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <form id="searchForm" action="<?= site_url('Pages/' . $selectedOption); ?>" method="post">
                <div class="form-group row">
                    <div class="col-sm-2 control-label mt-1 font-label-egp-buy">ค้นหาตามวันที่</div>
                    <div class="col-sm-4">
                        <input id="startDate_egp" type="text" name="start_date" class="form-control start_date" style="height: 40px;" placeholder="วันที่ (วัน/เดือน/ปี)" value="<?php echo set_value('start_date'); ?>">
                    </div>
                    <div class="col-sm-1 control-label mt-1 font-label-egp-buy">&nbsp;&nbsp;&nbsp;ถึง</div>
                    <div class="col-sm-4">
                        <input id="endDate_egp" type="text" name="end_date" style="height: 40px;" class="form-control end_date"  placeholder="วันที่ (วัน/เดือน/ปี)" value="<?php echo set_value('end_date'); ?>">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label font-label-egp-buy">ค้นหาตามคำ</div>
                    <div class="col-sm-4">
                        <input type="text" name="search" class="searchTerm form-control" style="height: 40px;"  placeholder="ค้นหา" value="<?php echo set_value('search'); ?>">
                    </div>
                    <div class="col-sm-1 control-label mt-2"></div>
                    <div class="col-sm-4">
                        <button type="button" id="resetButton" class="btn btn-clear-date-egp">ล้างข้อมูล</button>
                    </div>
                </div>
                <br>
                <div class="input-group-append text-center">
                    <button type="submit" class="btn btn-search-egp">ค้นหา</button>
                </div>
                <input type="hidden" id="selectedOptionInput" name="searchOption" value="<?= $selectedOption; ?>">
            </form>
        </div>
        <br>

        <?php
$count = count($query);
$itemsPerPage = 25;
$totalPages = ceil($count / $itemsPerPage);

$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

$numToShow = 3;
$half = floor($numToShow / 2);

$startPage = max($currentPage - $half, 1);
$endPage = min($startPage + $numToShow - 1, $totalPages);

$startIndex = ($currentPage - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage - 1, $count - 1);

// ฟังก์ชันแปลงชื่อตารางเป็นชื่อประเภทภาษาไทย
if (!function_exists('getTableTypeName')) {
    function getTableTypeName($tableName) {
        $types = [
            'tbl_w0' => 'ประกาศรายชื่อผู้ชนะการเสนอราคา',
            'tbl_p0' => 'แผนการจัดซื้อจัดจ้าง',
            'tbl_15' => 'ประกาศราคากลาง',
            'tbl_b0' => 'ร่างเอกสารประกวดราคา (e Bidding) และร่างเอกสารซื้อหรือจ้างด้วยวิธีสอบราคา',
            'tbl_d0' => 'ประกาศเชิญชวน',
            'tbl_d1' => 'ยกเลิกประกาศเชิญชวน',
            'tbl_w1' => 'ยกเลิกประกาศรายชื่อผู้ชนะการเสนอราคา',
            'tbl_d2' => 'เปลี่ยนแปลงประกาศเชิญชวน',
            'tbl_w2' => 'เปลี่ยนแปลงประกาศรายชื่อผู้ชนะการเสนอราคา'
        ];
        return isset($types[$tableName]) ? $types[$tableName] : '';
    }
}

for ($i = $startIndex; $i <= $endIndex; $i++) {
    $rs = $query[$i];
?>
    <div class="pages-select-dla">
        <div class="row mt-2">
            <div class="col-2 span-time-pages-news">
                <span>
                    <img src="<?php echo base_url('docs/icon-calender-egp.png'); ?>">&nbsp;&nbsp;
                    <?php
                    if (!function_exists('formatDateThai')) {
                        function formatDateThai($dateStr)
                        {
                            $thaiMonths = [
                                '01' => 'มกราคม',
                                '02' => 'กุมภาพันธ์',
                                '03' => 'มีนาคม',
                                '04' => 'เมษายน',
                                '05' => 'พฤษภาคม',
                                '06' => 'มิถุนายน',
                                '07' => 'กรกฎาคม',
                                '08' => 'สิงหาคม',
                                '09' => 'กันยายน',
                                '10' => 'ตุลาคม',
                                '11' => 'พฤศจิกายน',
                                '12' => 'ธันวาคม',
                            ];

                            $date = new DateTime($dateStr);
                            $day = $date->format('d');
                            $month = $date->format('m');
                            $year = $date->format('Y') + 543;

                            return $day . ' ' . $thaiMonths[$month] . ' ' . $year;
                        }
                    }
                    echo formatDateThai($rs['item_date']);
                    ?>
                </span>
            </div>
            
            <?php if ($selectedOption == 'procurement_tbl_all_search') : ?>
            <div class="col-2">
                <?php 
                // แสดงประเภทเฉพาะเมื่อเลือก "ดูทั้งหมด"
                if (isset($rs['source_table'])) {
                    $typeName = getTableTypeName($rs['source_table']);
                    if (!empty($typeName)) {
                        echo '<span class="procurement-type-badge">
                                <i class="fa fa-tag"></i> ' . $typeName . '
                              </span>';
                    }
                }
                ?>
            </div>
            <div class="col-8 font-pages-content" style="padding-top: 1px;">
            <?php else : ?>
            <div class="col-10 font-pages-content" style="padding-top: 1px;">
            <?php endif; ?>
                <a class="underline" href="<?php echo $rs['item_url']; ?>" target="_blank">
                    <?php echo $rs['item_title']; ?>
                </a>
				
				 <?php
                // คำนวณหาความต่างของวัน
                $item_date = new DateTime($rs['item_date']);
                $current_date = new DateTime();
                $interval = $current_date->diff($item_date);
                $days_difference = $interval->days;

                // แสดง new badge ถ้าไม่เกิน 30 วัน
                if ($days_difference <= 30) {
                    echo '<div class="bt-new-dla"><span class="text-new-dla">new</span></div>';
                }
                ?>
            </div>
        </div>
    </div>
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
    </div>
</div><br><br><br>

<style>
.procurement-type-badge {
    display: inline-block;
    margin-top: 15px;
    padding: 3px 10px;
    background-color: #e3f2fd;
    color: #1976d2;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 500;
}
</style>

<script>
     // input date thai ******************************************************* */
     document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#startDate_egp", {
                dateFormat: "Y-m-d",
                disableMobile: true,
                locale: "th"
            });
            flatpickr("#endDate_egp", {
                dateFormat: "Y-m-d",
                disableMobile: true,
                locale: "th"
            });
        });
    // *************************************************************************** */
    // รีเซ็ต ข้อมูลในฟอร์ม egp ******************************************************* */
    document.getElementById('resetButton').addEventListener('click', function() {
        document.getElementById('startDate_egp').value = '';
        document.getElementById('endDate_egp').value = '';
        document.querySelector('input[name="search"]').value = '';
        document.getElementById('selectedOptionInput').value = 'procurement_tbl_w0_search';
        document.getElementById('searchOption').value = 'procurement_tbl_w0_search';
        document.getElementById('searchForm').action = '<?= site_url('Pages/procurement_tbl_w0_search'); ?>';
    });
    // *************************************************************************** */
    // ตัวสลับฟังก์ชั่นใน controller pages egp ******************************************************* */
    $(document).ready(function() {
        $('#searchOption').on('change', function() {
            var selectedOption = $(this).val();
            var formAction = "<?= site_url('Pages/'); ?>" + selectedOption;

            $('#searchForm').attr('action', formAction); // เปลี่ยนค่า action ของฟอร์ม
            $('#selectedOptionInput').val(selectedOption); // บันทึกค่า searchOption ลงในฟอร์ม
        });
    });
    // $('#searchOption').on('change', function() {
    //     var formAction = $(this).val(); // รับค่า action จาก option ที่เลือก
    //     $('#searchForm').attr('action', "<?= site_url('Pages/'); ?>" + formAction);
    // });
    // *************************************************************************** */
</script>