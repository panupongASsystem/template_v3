    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">จำนวนเอกสารทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="newdataTables" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <!-- <th tabindex="0" rowspan="1" colspan="1" style="width: 2%;">ลำดับ</th> -->
                            <th tabindex="0" rowspan="1" colspan="1">ประเภทเอกสาร</th>
                            <th tabindex="0" rowspan="1" colspan="1">จำนวน</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sum_news as $rs) { ?>
                            <tr role="row">
                                <td>ข้อมูลข่าวประชาสัมพันธ์ <span style="color: red;">Admin</span></td>
                                <td><?php echo $rs->news_total; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($sum_activity as $rs) { ?>
                            <tr role="row">
                                <td>กิจกรรมช่วยเหลือชุมชน <span style="color: red;">Admin</span></td>
                                <td><?php echo $rs->activity_total; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($sum_health as $rs) { ?>
                            <tr role="row">
                                <td>ข้อมูลสาธารณสุข <span style="color: red;">Admin</span></td>
                                <td><?php echo $rs->health_total; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($sum_travel as $rs) { ?>
                            <tr role="row">
                                <td>สถานที่ท่องเที่ยว <span style="color: red;">Admin</span></td>
                                <td><?php echo $rs->travel_total; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($sum_food as $rs) { ?>
                            <tr role="row">
                                <td>ร้านอาหาร <span style="color: red;">Admin</span></td>
                                <td><?php echo $rs->food_total; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($sum_otop as $rs) { ?>
                            <tr role="row">
                                <td>สินค้า OTOP <span style="color: red;">Admin</span></td>
                                <td><?php echo $rs->otop_total; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($sum_store as $rs) { ?>
                            <tr role="row">
                                <td>ข้อมูลร้านค้าและบริการ <span style="color: red;">Admin</span></td>
                                <td><?php echo $rs->store_total; ?></td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($sum_user_store as $rs) { ?>
                            <tr role="row">
                                <td>ข้อมูลร้านค้าและบริการ <span style="color: blue;">user</span></td>
                                <td><?php echo $rs->user_store_total; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>