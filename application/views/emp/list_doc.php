<div class="container">
	<!-- DataTales Example -->
	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">จัดการข้อมูลบุคลากร</h6>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
								<td><?php echo date('d-n-Y', strtotime($row->doc_save)); ?></td>
								<td><?php echo $row->doc_num; ?></td>
								<td><?php
									echo $row->dname; ?></td>
								<!-- <td><?php
											echo '<font color="blue">';
											echo 'ประเภท ';
											echo '<b>';
											echo $row->dname;
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
</div>
<!-- /.container-fluid -->