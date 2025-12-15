<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h4>แสดงข้อมูลหนังสือ/เอกสารตามช่วงเวลา</h4>
            <form action="<?php echo site_url('report/getform') ;?>" method="post" class="form-horizontal">
                <div class="form-group">
                    <div class="col-sm-1">
                        start
                    </div>
                    <div class="col-sm-3">
                        <input type="date" name="ds" class="form-control" required>
                    </div>
                    <br>
                    <div class="col-sm-1">
                        end
                    </div>
                    <div class="col-sm-3">
                        <input type="date" name="de" class="form-control" required>
                    </div>
                    <br>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-info" >ดูเอกสาร</button>
                    </div>
                </div>
            </form>
            <br>
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th>ว/ด/ปี</th>
						<th>เลขที่</th>
						<th>ประเภท</th>
						<th>ชื่อเอกสาร</th>
						<th>จาก</th>
						<th>ถึง</th>
						<th>เอกสาร</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($query as $row) { ?>
						<tr>
                        <td><?php echo date('d-n-Y', strtotime($row->doc_save . ' +543 years')); ?></td>
							<td><?php echo $row->doc_num; ?></td>
							<td><?php
								echo $row->doc_name;?></td>
							<!-- <td><?php
										echo '<font color="blue">';
										echo 'ประเภท ';
										echo '<b>';
										echo $row->doc_name;
										echo '</b>';
										echo '</font>';
										echo br();
										echo $row->doc_num;
										echo ' ( ลว. ';
										echo date('d/m/Y', strtotime($row->doc_date));
										echo ' )';
										?></td> -->
							<td><?php echo $row->doc_name; ?></td>
							<td><?php echo $row->doc_from; ?></td>
							<td><?php echo $row->doc_to; ?></td>
							<!-- show file pdf -->
							<td align="center"><?php $df = $row->doc_file;
								if ($df != '') { ?>
									<a href="<?php echo base_url('docs/' . $row->doc_file); ?>" target="_blank" class="btn btn-info btn-sm">เปิดดูเอกสาร</a>
								<?php } else {
									echo '-';
								} ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>