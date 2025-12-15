   <!-- DataTales Example -->
   <div class="card shadow mb-4">
       <div class="card-header py-3">
           <h6 class="m-0 font-weight-bold text-black">จัดการข้อมูลคำร้องเบี้ยผู้สูงอายุ</h6>
       </div>
       <div class="card-body">
           <div class="table-responsive">

               <?php
                $Index = 1;
                ?>
               <table id="newdataTables" class="table">
                   <thead>
                       <tr>
                           <th style="width: 5%;">ลำดับ</th>
                           <th style="width: 15%;">ชื่อ - นามสกุล</th>
                           <th style="width: 10%;">เบอร์โทรศัพท์</th>
                           <th style="width: 15%;">หมายเลขประจำตัวประชาชน</th>
                           <th style="width: 17%;">ที่อยู่</th>
                           <th style="width: 10%;">เอกสารสำเนาบัตรประชาชน</th>
                           <th style="width: 10%;">เอกสารขึ้นทะเบียนผู้พิการ/ลงทะเบียนเบี้ยยังชีพผู้สูงอายุ</th>
                           <th style="width: 10%;">เอกสารหนังสือมอบอำนาจ</th>
                           <th style="width: 7%;">วันที่อัพโหลด</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php foreach ($query as $rs) { ?>
                           <tr role="row">
                               <td align="center"><?= $Index; ?></td>
                               <td><?= $rs->elderly_aw_ods_by; ?></td>
                               <td><?= $rs->elderly_aw_ods_phone; ?></td>
                               <td><?= $rs->elderly_aw_ods_number; ?></td>
                               <td><?= $rs->elderly_aw_ods_address; ?></td>
                               <td>
                                   <?php if (!empty($rs->elderly_aw_ods_file1)) : ?>
                                       <a class="btn btn-info btn-sm mb-2" href="<?= base_url('docs/file/' . $rs->elderly_aw_ods_file1); ?>" target="_blank">ดูไฟล์ <?= $rs->elderly_aw_ods_file1; ?></a>
                                   <?php endif; ?>
                               </td>
                               <td>
                                   <?php if (!empty($rs->elderly_aw_ods_file2)) : ?>
                                       <a class="btn btn-info btn-sm mb-2" href="<?= base_url('docs/file/' . $rs->elderly_aw_ods_file2); ?>" target="_blank">ดูไฟล์ <?= $rs->elderly_aw_ods_file2; ?></a>
                                   <?php endif; ?>
                               </td>
                               <td>
                                   <?php if (!empty($rs->elderly_aw_ods_file3)) : ?>
                                       <a class="btn btn-info btn-sm mb-2" href="<?= base_url('docs/file/' . $rs->elderly_aw_ods_file3); ?>" target="_blank">ดูไฟล์ <?= $rs->elderly_aw_ods_file3; ?></a>
                                   <?php endif; ?>
                               </td>
                               <td><?= date('d/m/Y H:i', strtotime($rs->elderly_aw_ods_datesave . '+543 years')) ?> น.</td>
                           </tr>
                       <?php
                            $Index++;
                        } ?>
                   </tbody>
               </table>
           </div>
       </div>
   </div>