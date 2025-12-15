<!-- แสดงฟอร์มค้นหา -->
<form method="post" action="<?php echo base_url('report_backend/report_user'); ?>">
    <div class="form-group row">
        <label class="col-sm-2 control-label">ชื่อผู้ใช้งาน</label> <!-- เพิ่ม label ด้านหน้า select -->
        <div class="col-sm-4">
            <select class="form-control" name="m_id" required>
                <option value="">เลือกข้อมูล</option>
                <?php foreach ($rsemp as $rs) { ?>
                    <option value="<?php echo $rs->m_id; ?>"><?php echo $rs->m_name; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-sm-2">
            <button type="submit" class="btn btn-primary">ค้นหา</button>
        </div>
    </div>
</form>



<!-- แสดงผลลัพธ์การค้นหา  แบบนับจำนวน-->
<?php if (isset($searched_user_name) && isset($rsfood)) { ?>
    <div class="shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ชื่อผู้ใช้งาน : <span style="color: black;"><?php echo $searched_user_name; ?></span></h6>
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
                        <?php foreach ($rsnews as $rs) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('news'); scrollToTable('news');"><span style="color: black;">ข้อมูลข่าวสารประจำเดือน </span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $rs->news_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($rsactivity as $rs) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('activity'); scrollToTable('activity');"><span style="color: black;">ข้อมูลกิจกรรมช่วยเหลือชุมชน </span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $rs->activity_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($rshealth as $rs) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('health'); scrollToTable('health');"><span style="color: black;">ข้อมูลสาธารณสุข </span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $rs->health_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($rstravel as $row) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('travel'); scrollToTable('travel');"><span style="color: black;">ข้อมูลสถานที่ท่องเที่ยว </span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $row->travel_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($rsfood as $row) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('food'); scrollToTable('food');"><span style="color: black;">ข้อมูลร้านอาหาร </span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $row->food_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($rsotop as $row) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('otop'); scrollToTable('otop');"><span style="color: black;">ข้อมูลสินค้า OTOP </span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $row->otop_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($rsstore as $row) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('store'); scrollToTable('store');"><span style="color: black;">ข้อมูลร้านค้าและบริการ </span> <span style="color: red;">Admin</span></a></td>
                                <td><?php echo $row->store_count; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($rsstore as $row) { ?>
                            <tr role="row">
                                <td><a href="javascript:void(0);" onclick="toggleTable('storeUser'); scrollToTable('storeUser');"><span style="color: black;">ข้อมูลร้านค้าและบริการ </span> <span style="color: blue;">User</span></a></td>
                                <td><?php echo $row->store_count; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ตารางแสดงรายละเอียดข้อมูลจาก -->
    <?php if (!empty($news_data)) { ?>
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
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($news_data as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->news_name; ?></td>
                                    <td><?php echo $row->news_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($activity_data)) { ?>
        <div class="card shadow mb-4" id="activity" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">กิจกรรมช่วยเหลือชุมชน <span style="color: red;">Admin</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportTableActivity" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="color: black;">ลำดับ</th>
                                <th style="color: black;">ชื่อกิจกรรม</th>
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activity_data as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->activity_name; ?></td>
                                    <td><?php echo $row->activity_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($health_data)) { ?>
        <div class="card shadow mb-4" id="health"  style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลสาธารณสุข <span style="color: red;">Admin</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportTableHealth" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="color: black;">ลำดับ</th>
                                <th style="color: black;">ชื่อกิจกรรม</th>
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($health_data as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->health_name; ?></td>
                                    <td><?php echo $row->health_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($travel_data)) { ?>
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
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($travel_data as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->travel_name; ?></td>
                                    <td><?php echo $row->travel_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($food_data)) { ?>
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
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($food_data as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->food_name; ?></td>
                                    <td><?php echo $row->food_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($otop_data)) { ?>
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
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($otop_data as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->otop_name; ?></td>
                                    <td><?php echo $row->otop_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($store_data)) { ?>
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
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($store_data as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->store_name; ?></td>
                                    <td><?php echo $row->store_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($user_store_data)) { ?>
        <div class="card shadow mb-4" id="storeUser"  style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลร้านค้าและบริการ <span style="color: blue;">User</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportTableStoreUser" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="color: black;">ลำดับ</th>
                                <th style="color: black;">ชื่อกิจกรรม</th>
                                <th style="color: black;">วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_store_data as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->user_store_name; ?></td>
                                    <td><?php echo $row->user_store_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- <?php if (!empty($user_activity_data)) { ?>
        <div class="card shadow mb-4" id="user_activity">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">กิจกรรมช่วยเหลือชุมชน <span style="color: blue;">User</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="newdataTables" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ลำดับ</th>
                                <th>ชื่อกิจกรรม</th>
                                <th>วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_activity_data as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->user_activity_name; ?></td>
                                    <td><?php echo $row->user_activity_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($user_travel_data)) { ?>
        <div class="card shadow mb-4" id="user_travel">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลการท่องเที่ยว <span style="color: blue;">User</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="newdataTables" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ลำดับ</th>
                                <th>ชื่อสถานที่</th>
                                <th>วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_travel_data as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->user_travel_name; ?></td>
                                    <td><?php echo $row->user_travel_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($user_food_data)) { ?>
        <div class="card shadow mb-4" id="user_food">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลร้านอาหาร <span style="color: blue;">User</span></h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="newdataTables" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ลำดับ</th>
                                <th>ชื่อร้านอาหาร</th>
                                <th>วันที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_food_data as $index => $row) { ?>
                                <tr role="row">
                                    <td align="center"><?= $index + 1; ?></td>
                                    <td><?php echo $row->user_food_name; ?></td>
                                    <td><?php echo $row->user_food_datesave; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?> -->

<?php } ?>