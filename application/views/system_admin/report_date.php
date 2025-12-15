<!-- แสดงฟอร์มค้นหา -->
<form method="post" action="<?php echo base_url('report_backend/report_date'); ?>">
    <div class="form-group row">
        <label class="col-sm-2 control-label">วันที่เริ่มต้น</label>
        <div class="col-sm-4">
            <input type="date" class="form-control" name="start_date" required value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : ''; ?>">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 control-label">วันที่สิ้นสุด</label>
        <div class="col-sm-4">
            <input type="date" class="form-control" name="end_date" required value="<?php echo isset($_POST['end_date']) ? $_POST['end_date'] : ''; ?>">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2"></div>
        <div class="col-sm-4">
            <button type="submit" class="btn btn-primary">ค้นหา</button>
        </div>
    </div>
</form>

<!-- แสดงผลลัพธ์การค้นหา  แบบนับจำนวน-->
<?php if (isset($news_count) && isset($activity_count) && isset($travel_count) && isset($food_count)) { ?>
    <div class="shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ข้อมูลทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="newdataTables" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ประเภทเอกสาร</th>
                            <th>จำนวนเอกสาร</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($news_count > 0) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('news'); scrollToTable('news');"><span style="color: black;">ข้อมูลข่าวสารประจำเดือน</span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $news_count; ?></td>
                            </tr>
                        <?php } ?>

                        <?php if ($activity_count > 0) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('activity'); scrollToTable('activity');"><span style="color: black;">ข้อมูลกิจกรรมช่วยเหลือชุมชน</span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $activity_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($health_count > 0) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('health'); scrollToTable('health');"><span style="color: black;">ข้อมูลสาธารณสุข</span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $health_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($travel_count > 0) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('travel'); scrollToTable('travel');"><span style="color: black;">ข้อมูลสถานที่ท่องเที่ยว</span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $travel_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($food_count > 0) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('food'); scrollToTable('food');"><span style="color: black;">ข้อมูลร้านอาหาร</span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $food_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($otop_count > 0) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('otop'); scrollToTable('otop');"><span style="color: black;">ข้อมูลสินค้า OTOP</span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $otop_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($store_count > 0) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('store'); scrollToTable('store');"><span style="color: black;">ข้อมูลร้านค้าและบริการ</span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $store_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user_store_count > 0) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('storeUser'); scrollToTable('storeUser');"><span style="color: black;">ข้อมูลร้านค้าและบริการ</span> <span style="color: blue;">User</span></a></td>
                                <td><?php echo $user_store_count; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ตารางแสดงรายละเอียดข้อมูลจาก -->
    <?php if (!empty($news_detail)) { ?>
        <div class="card shadow mb-4" id="news" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลข่าวสารประจำเดือน <span style="color: red;">Admin</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportTableNews" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="color: black;">ลำดับ</th>
                                <th style="color: black;">ชื่อข่าว</th>
                                <th style="color: black;">ผู้อัพโหลด</th>
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($news_detail as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->news_name; ?></td>
                                    <td><?php echo $row->news_by; ?></td>
                                    <td><?php echo $row->news_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($activity_detail)) { ?>
        <div class="card shadow mb-4" id="activity" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลกิจกรรมช่วยเหลือชุมชน<span style="color: red;">Admin</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportTableActivity" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="color: black;">ลำดับ</th>
                                <th style="color: black;">ชื่อกิจกรรม</th>
                                <th style="color: black;">ผู้อัพโหลด</th>
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activity_detail as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->activity_name; ?></td>
                                    <td><?php echo $row->activity_by; ?></td>
                                    <td><?php echo $row->activity_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($health_detail)) { ?>
        <div class="card shadow mb-4" id="health" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลสาธารณสุข<span style="color: red;">Admin</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportTableHealth" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="color: black;">ลำดับ</th>
                                <th style="color: black;">ชื่อกิจกรรม</th>
                                <th style="color: black;">ผู้อัพโหลด</th>
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($health_detail as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->health_name; ?></td>
                                    <td><?php echo $row->health_by; ?></td>
                                    <td><?php echo $row->health_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($travel_detail)) { ?>
        <div class="card shadow mb-4" id="travel" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลการท่องเที่ยว <span style="color: red;">Admin</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportTableTravel" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="color: black;">ลำดับ</th>
                                <th style="color: black;">ชื่อสถานที่</th>
                                <th style="color: black;">ผู้อัพโหลด</th>
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($travel_detail as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->travel_name; ?></td>
                                    <td><?php echo $row->travel_by; ?></td>
                                    <td><?php echo $row->travel_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($food_detail)) { ?>
        <div class="card shadow mb-4" id="food" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลร้านอาหาร <span style="color: red;">Admin</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportTableFood" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="color: black;">ลำดับ</th>
                                <th style="color: black;">ชื่อร้านอาหาร</th>
                                <th style="color: black;">ผู้อัพโหลด</th>
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($food_detail as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->food_name; ?></td>
                                    <td><?php echo $row->food_by; ?></td>
                                    <td><?php echo $row->food_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($otop_detail)) { ?>
        <div class="card shadow mb-4" id="otop" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลสินค้า OTOP <span style="color: red;">Admin</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportTableOtop" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="color: black;">ลำดับ</th>
                                <th style="color: black;">ชื่อร้านอาหาร</th>
                                <th style="color: black;">ผู้อัพโหลด</th>
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($otop_detail as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->otop_name; ?></td>
                                    <td><?php echo $row->otop_by; ?></td>
                                    <td><?php echo $row->otop_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
    
    <?php if (!empty($store_detail)) { ?>
        <div class="card shadow mb-4" id="store" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลร้านค้าและบริการ <span style="color: red;">Admin</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportTableStore" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="color: black;">ลำดับ</th>
                                <th style="color: black;">ชื่อร้านอาหาร</th>
                                <th style="color: black;">ผู้อัพโหลด</th>
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($store_detail as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->store_name; ?></td>
                                    <td><?php echo $row->store_by; ?></td>
                                    <td><?php echo $row->store_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($user_store_detail)) { ?>
        <div class="card shadow mb-4" id="storeUser" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลร้านค้าและบริการ <span style="color: blue;">User</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportTableStoreUser" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="color: black;">ลำดับ</th>
                                <th style="color: black;">ชื่อร้านอาหาร</th>
                                <th style="color: black;">ผู้อัพโหลด</th>
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_store_detail as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->user_store_name; ?></td>
                                    <td><?php echo $row->user_store_by; ?></td>
                                    <td><?php echo $row->user_store_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>