<!-- dynamic_position_types.php - หน้าแรกแสดงประเภทตำแหน่งทั้งหมด (ปรับปรุงแล้ว) -->
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">จัดการข้อมูลบุคลากร</h1>
        <div>
            <a href="<?= site_url('dynamic_position_backend/create_new_type') ?>"
                class="btn btn-success">
                <i class="fas fa-plus"></i> สร้างประเภทใหม่
            </a>
        </div>
    </div>

    <!-- แสดงข้อความแจ้งเตือน -->
    <?php if ($this->session->flashdata('save_success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>สำเร็จ!</strong> บันทึกข้อมูลเรียบร้อยแล้ว
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('del_success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>สำเร็จ!</strong> ลบข้อมูลเรียบร้อยแล้ว
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('update_success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>สำเร็จ!</strong> อัพเดตข้อมูลเรียบร้อยแล้ว
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('status_success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>สำเร็จ!</strong> เปลี่ยนสถานะเรียบร้อยแล้ว
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <!-- แท็บสำหรับกรองการแสดงผล -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <ul class="nav nav-pills" id="statusTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="all-tab" data-toggle="pill" href="#all"
                        role="tab" aria-controls="all" aria-selected="true">
                        <i class="fas fa-list"></i> ทั้งหมด
                        <span class="badge badge-secondary ml-1"><?= count($position_types) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="active-tab" data-toggle="pill" href="#active"
                        role="tab" aria-controls="active" aria-selected="false">
                        <i class="fas fa-eye"></i> กำลังแสดง
                        <span class="badge badge-success ml-1">
                            <?= count(array_filter($position_types, function ($t) {
                                return $t->pstatus === 'show';
                            })) ?>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="hidden-tab" data-toggle="pill" href="#hidden"
                        role="tab" aria-controls="hidden" aria-selected="false">
                        <i class="fas fa-eye-slash"></i> ซ่อนอยู่
                        <span class="badge badge-warning ml-1">
                            <?= count(array_filter($position_types, function ($t) {
                                return $t->pstatus === 'hide';
                            })) ?>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- เนื้อหาทั้งหมด -->
    <div class="tab-content" id="statusTabContent">
        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
            <div class="row">
                <?php foreach ($position_types as $type): ?>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-<?= $type->pstatus === 'show' ? 'primary' : 'secondary' ?> shadow h-100 py-2 position-type-card"
                            data-status="<?= $type->pstatus ?>"
                            data-show-status="<?= $type->pstatus ?>"
                            data-type-peng="<?= $type->peng ?>">

                            <!-- Status indicator -->
                            <div class="position-absolute" style="top: 10px; right: 10px;">
                                <?php if ($type->pstatus === 'show'): ?>
                                    <span class="badge badge-success">
                                        <i class="fas fa-eye"></i> แสดง
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-eye-slash"></i> ซ่อน
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-md font-weight-bold text-primary text-uppercase mb-1">
                                            <?= $type->pname ?>
                                            <small class="text-muted">(<?= $type->peng ?>)</small>
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <span data-stat="filled"><?= $type->filled_positions ?></span>/<span data-stat="total"><?= $type->total_positions ?></span> ตำแหน่ง

                                            <!-- เพิ่มสถานะการใช้งาน -->
                                            <span class="badge badge-<?= $type->usage_color ?> ml-2" style="font-size: 0.7em;">
                                                <?php if ($type->usage_status === 'full'): ?>
                                                    <i class="fas fa-exclamation-triangle"></i> เต็มเกือบหมด
                                                <?php elseif ($type->usage_status === 'high'): ?>
                                                    <i class="fas fa-chart-line"></i> ใช้งานสูง
                                                <?php elseif ($type->usage_status === 'medium'): ?>
                                                    <i class="fas fa-chart-bar"></i> ปานกลาง
                                                <?php else: ?>
                                                    <i class="fas fa-battery-quarter"></i> ใช้งานต่ำ
                                                <?php endif; ?>
                                            </span>
                                        </div>

                                        <div class="text-xs text-gray-600 mt-2">
                                            <?= $type->pdescription ?>

                                            <!-- เพิ่มข้อมูลสถิติ -->
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    ว่าง: <?= $type->empty_positions ?> ช่อง
                                                    <?php if ($type->usage_status === 'full'): ?>
                                                        <span class="text-warning">⚠️ ควรเพิ่มตำแหน่ง</span>
                                                    <?php elseif ($type->usage_status === 'low'): ?>
                                                        <span class="text-info">💡 ควรเพิ่มข้อมูล</span>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>

                                        <?php if ($type->total_positions > 0): ?>
                                            <div class="mt-2">
                                                <?php
                                                $percentage = $type->usage_percentage;
                                                $color_class = $type->usage_color;
                                                ?>
                                                <div class="progress-bar bg-<?= $color_class ?>"
                                                    role="progressbar"
                                                    style="width: <?= $percentage ?>%"
                                                    aria-valuenow="<?= $percentage ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100"
                                                    data-stat="progress">
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <small class="text-muted"><span data-stat="percent"><?= $percentage ?></span>% ใช้งาน</small>
                                                    <small class="text-muted"><span data-stat="total"><?= $type->total_positions ?></span> ตำแหน่ง</small>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>

                                <!-- ปุ่มจัดการหลัก -->
                                <div class="mt-3 mb-2">
                                    <a href="<?= site_url('dynamic_position_backend/manage/' . $type->peng) ?>"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-cog"></i> จัดการ
                                    </a>

                                    <!-- <?php if ($type->total_positions > 0): ?>
                                        <a href="<?= site_url('dynamic_position_backend/add_to_slot/' . $type->peng . '/' . ($type->total_positions + 1)) ?>"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-plus"></i> เพิ่ม
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= site_url('dynamic_position_backend/add_to_slot/' . $type->peng . '/1') ?>"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-plus"></i> เพิ่มแรก
                                        </a>
                                    <?php endif; ?> -->

                                    <?php if ($type->filled_positions > 0): ?>
                                        <a href="<?= site_url('dynamic_position_backend/statistics/' . $type->peng) ?>"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-chart-bar"></i> สถิติ
                                        </a>
                                    <?php endif; ?>

                                    <!-- ปุ่มเพิ่มตำแหน่ง (แสดงเมื่อใช้งานสูง) -->
                                    <?php if ($type->usage_status === 'full' || $type->usage_status === 'high'): ?>
                                        <button type="button" class="btn btn-warning btn-sm"
                                            onclick="quickAddSlots('<?= $type->peng ?>', <?= $type->total_positions ?>)">
                                            <i class="fas fa-expand"></i> +ตำแหน่ง
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- ปุ่มการจัดการเพิ่มเติม -->
                                <div class="btn-group w-100" role="group">
                                    <!-- ปุ่มแก้ไข -->
                                    <button type="button" class="btn btn-outline-warning btn-sm"
                                        onclick="editPositionType(<?= $type->pid ?>, '<?= htmlspecialchars($type->pname, ENT_QUOTES) ?>', '<?= htmlspecialchars($type->peng, ENT_QUOTES) ?>', '<?= htmlspecialchars($type->pdescription, ENT_QUOTES) ?>', <?= $type->porder ?>, <?= $type->psub ?>)">
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </button>

                                    <!-- ปุ่มเปิด/ปิดสถานะ -->
                                    <button type="button" class="btn btn-outline-<?= $type->pstatus === 'show' ? 'secondary' : 'success' ?> btn-sm"
                                        onclick="toggleStatus(<?= $type->pid ?>, '<?= $type->pstatus ?>', '<?= htmlspecialchars($type->pname, ENT_QUOTES) ?>')">
                                        <?php if ($type->pstatus === 'show'): ?>
                                            <i class="fas fa-eye-slash"></i> ซ่อน
                                        <?php else: ?>
                                            <i class="fas fa-eye"></i> แสดง
                                        <?php endif; ?>
                                    </button>

                                    <!-- ปุ่มลบ -->
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                        onclick="deletePositionType(<?= $type->pid ?>, '<?= htmlspecialchars($type->pname, ENT_QUOTES) ?>', <?= isset($type->filled_positions) ? $type->filled_positions : 0 ?>)">
                                        <i class="fas fa-trash"></i> ลบ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- การ์ดสำหรับสร้างประเภทใหม่ -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2" style="border-style: dashed;">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="fas fa-plus-circle fa-3x text-success"></i>
                                </div>
                                <h6 class="font-weight-bold text-success">สร้างประเภทตำแหน่งใหม่</h6>
                                <p class="text-muted small mb-3">
                                    เพิ่มประเภทตำแหน่งใหม่พร้อม 61 ช่องอัตโนมัติ
                                </p>
                                <a href="<?= site_url('dynamic_position_backend/create_new_type') ?>"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> สร้างใหม่
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- แท็บสำหรับแสดงเฉพาะที่กำลังแสดง -->
        <div class="tab-pane fade" id="active" role="tabpanel" aria-labelledby="active-tab">
            <div class="row">
                <?php foreach ($position_types as $type): ?>
                    <?php if ($type->pstatus === 'show'): ?>
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2 position-type-card"
                                data-status="<?= $type->pstatus ?>"
                                data-show-status="<?= $type->pstatus ?>">

                                <!-- Status indicator -->
                                <div class="position-absolute" style="top: 10px; right: 10px;">
                                    <span class="badge badge-success">
                                        <i class="fas fa-eye"></i> แสดง
                                    </span>
                                </div>

                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                <?= $type->pname ?>
                                                <small class="text-muted">(<?= $type->peng ?>)</small>
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $type->filled_positions ?>/<?= $type->total_positions ?> ตำแหน่ง

                                                <!-- เพิ่มสถานะการใช้งาน -->
                                                <span class="badge badge-<?= $type->usage_color ?> ml-2" style="font-size: 0.7em;">
                                                    <?php if ($type->usage_status === 'full'): ?>
                                                        <i class="fas fa-exclamation-triangle"></i> เต็มเกือบหมด
                                                    <?php elseif ($type->usage_status === 'high'): ?>
                                                        <i class="fas fa-chart-line"></i> ใช้งานสูง
                                                    <?php elseif ($type->usage_status === 'medium'): ?>
                                                        <i class="fas fa-chart-bar"></i> ปานกลาง
                                                    <?php else: ?>
                                                        <i class="fas fa-battery-quarter"></i> ใช้งานต่ำ
                                                    <?php endif; ?>
                                                </span>
                                            </div>

                                            <div class="text-xs text-gray-600 mt-2">
                                                <?= $type->pdescription ?>

                                                <!-- เพิ่มข้อมูลสถิติ -->
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        ว่าง: <?= $type->empty_positions ?> ช่อง
                                                        <?php if ($type->usage_status === 'full'): ?>
                                                            <span class="text-warning">⚠️ ควรเพิ่มตำแหน่ง</span>
                                                        <?php elseif ($type->usage_status === 'low'): ?>
                                                            <span class="text-info">💡 ควรเพิ่มข้อมูล</span>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            </div>

                                            <?php if ($type->total_positions > 0): ?>
                                                <div class="mt-2">
                                                    <?php
                                                    $percentage = $type->usage_percentage;
                                                    $color_class = $type->usage_color;
                                                    ?>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-<?= $color_class ?>"
                                                            role="progressbar"
                                                            style="width: <?= $percentage ?>%"
                                                            aria-valuenow="<?= $percentage ?>"
                                                            aria-valuemin="0"
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <small class="text-muted"><?= $percentage ?>% ใช้งาน</small>
                                                        <small class="text-muted"><?= $type->total_positions ?> ตำแหน่ง</small>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>

                                    <!-- ปุ่มจัดการหลัก -->
                                    <div class="mt-3 mb-2">
                                        <a href="<?= site_url('dynamic_position_backend/manage/' . $type->peng) ?>"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-cog"></i> จัดการ
                                        </a>

                                        <!-- <?php if ($type->total_positions > 0): ?>
                                            <a href="<?= site_url('dynamic_position_backend/add_to_slot/' . $type->peng . '/' . ($type->total_positions + 1)) ?>"
                                                class="btn btn-success btn-sm">
                                                <i class="fas fa-plus"></i> เพิ่ม
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= site_url('dynamic_position_backend/add_to_slot/' . $type->peng . '/1') ?>"
                                                class="btn btn-success btn-sm">
                                                <i class="fas fa-plus"></i> เพิ่มแรก
                                            </a>
                                        <?php endif; ?> -->

                                        <?php if ($type->filled_positions > 0): ?>
                                            <a href="<?= site_url('dynamic_position_backend/statistics/' . $type->peng) ?>"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-chart-bar"></i> สถิติ
                                            </a>
                                        <?php endif; ?>

                                        <!-- ปุ่มเพิ่มตำแหน่ง (แสดงเมื่อใช้งานสูง) -->
                                        <?php if ($type->usage_status === 'full' || $type->usage_status === 'high'): ?>
                                            <button type="button" class="btn btn-warning btn-sm"
                                                onclick="quickAddSlots('<?= $type->peng ?>', <?= $type->total_positions ?>)">
                                                <i class="fas fa-expand"></i> +ตำแหน่ง
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <!-- ปุ่มการจัดการเพิ่มเติม -->
                                    <div class="btn-group w-100" role="group">
                                        <!-- ปุ่มแก้ไข -->
                                        <button type="button" class="btn btn-outline-warning btn-sm"
                                            onclick="editPositionType(<?= $type->pid ?>, '<?= htmlspecialchars($type->pname, ENT_QUOTES) ?>', '<?= htmlspecialchars($type->peng, ENT_QUOTES) ?>', '<?= htmlspecialchars($type->pdescription, ENT_QUOTES) ?>', <?= $type->porder ?>, <?= $type->psub ?>)">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </button>

                                        <!-- ปุ่มเปิด/ปิดสถานะ -->
                                        <button type="button" class="btn btn-outline-warning btn-sm"
                                            onclick="toggleStatus(<?= $type->pid ?>, '<?= $type->pstatus ?>', '<?= htmlspecialchars($type->pname, ENT_QUOTES) ?>')">
                                            <i class="fas fa-eye-slash"></i> ซ่อน
                                        </button>

                                        <!-- ปุ่มลบ -->
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="deletePositionType(<?= $type->pid ?>, '<?= htmlspecialchars($type->pname, ENT_QUOTES) ?>', <?= isset($type->filled_positions) ? $type->filled_positions : 0 ?>)">
                                            <i class="fas fa-trash"></i> ลบ
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- แท็บสำหรับแสดงเฉพาะที่ซ่อนอยู่ -->
        <div class="tab-pane fade" id="hidden" role="tabpanel" aria-labelledby="hidden-tab">
            <div class="row">
                <?php
                $hidden_types = array_filter($position_types, function ($t) {
                    return $t->pstatus === 'hide';
                });
                if (empty($hidden_types)):
                ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h5>ไม่มีประเภทตำแหน่งที่ซ่อนอยู่</h5>
                            <p class="mb-0">ประเภทตำแหน่งทั้งหมดกำลังแสดงอยู่ในหน้าเว็บไซต์</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($position_types as $type): ?>
                        <?php if ($type->pstatus === 'hide'): ?>
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-secondary shadow h-100 py-2 position-type-card"
                                    data-status="<?= $type->pstatus ?>"
                                    data-show-status="<?= $type->pstatus ?>">

                                    <!-- Status indicator -->
                                    <div class="position-absolute" style="top: 10px; right: 10px;">
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-eye-slash"></i> ซ่อน
                                        </span>
                                    </div>

                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                                    <?= $type->pname ?>
                                                    <small class="text-muted">(<?= $type->peng ?>)</small>
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-600">
                                                    <?= isset($type->filled_positions) ? $type->filled_positions : 0 ?>/<?= isset($type->total_positions) ? $type->total_positions : 61 ?> ตำแหน่ง
                                                </div>
                                                <div class="text-xs text-gray-600 mt-2">
                                                    <?= $type->pdescription ?>
                                                </div>
                                                <?php if (isset($type->total_positions) && $type->total_positions > 0): ?>
                                                    <div class="mt-2">
                                                        <?php
                                                        $percentage = round(($type->filled_positions / $type->total_positions) * 100, 1);
                                                        ?>
                                                        <div class="progress" style="height: 8px;">
                                                            <div class="progress-bar bg-secondary"
                                                                role="progressbar"
                                                                style="width: <?= $percentage ?>%"
                                                                aria-valuenow="<?= $percentage ?>"
                                                                aria-valuemin="0"
                                                                aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <small class="text-muted"><?= $percentage ?>% ใช้งาน (ซ่อนอยู่)</small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-users fa-2x text-gray-400"></i>
                                            </div>
                                        </div>

                                        <!-- แสดงข้อความแจ้งเตือน -->
                                        <div class="alert alert-warning mt-3 mb-3">
                                            <small>
                                                <i class="fas fa-exclamation-triangle"></i>
                                                ประเภทนี้ถูกซ่อนและไม่แสดงในหน้าเว็บไซต์
                                            </small>
                                        </div>

                                        <!-- ปุ่มจัดการหลัก -->
                                        <div class="mt-3 mb-2">
                                            <a href="<?= site_url('dynamic_position_backend/manage/' . $type->peng) ?>"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-cog"></i> จัดการ
                                            </a>

                                            <!-- <?php if ($type->total_positions > 0): ?>
                                                <a href="<?= site_url('dynamic_position_backend/add_to_slot/' . $type->peng . '/' . ($type->total_positions + 1)) ?>"
                                                    class="btn btn-success btn-sm">
                                                    <i class="fas fa-plus"></i> เพิ่ม
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= site_url('dynamic_position_backend/add_to_slot/' . $type->peng . '/1') ?>"
                                                    class="btn btn-success btn-sm">
                                                    <i class="fas fa-plus"></i> เพิ่มแรก
                                                </a>
                                            <?php endif; ?> -->

                                            <?php if ($type->filled_positions > 0): ?>
                                                <a href="<?= site_url('dynamic_position_backend/statistics/' . $type->peng) ?>"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-chart-bar"></i> สถิติ
                                                </a>
                                            <?php endif; ?>

                                            <!-- ปุ่มเพิ่มตำแหน่ง (แสดงเมื่อใช้งานสูง) -->
                                            <?php if ($type->usage_status === 'full' || $type->usage_status === 'high'): ?>
                                                <button type="button" class="btn btn-warning btn-sm"
                                                    onclick="quickAddSlots('<?= $type->peng ?>', <?= $type->total_positions ?>)">
                                                    <i class="fas fa-expand"></i> +ตำแหน่ง
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                        <!-- ปุ่มการจัดการเพิ่มเติม -->
                                        <div class="btn-group w-100" role="group">
                                            <!-- ปุ่มแก้ไข -->
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                onclick="editPositionType(<?= $type->pid ?>, '<?= htmlspecialchars($type->pname, ENT_QUOTES) ?>', '<?= htmlspecialchars($type->peng, ENT_QUOTES) ?>', '<?= htmlspecialchars($type->pdescription, ENT_QUOTES) ?>', <?= $type->porder ?>, <?= $type->psub ?>)">
                                                <i class="fas fa-edit"></i> แก้ไข
                                            </button>

                                            <!-- ปุ่มเปิด/ปิดสถานะ -->
                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                onclick="toggleStatus(<?= $type->pid ?>, '<?= $type->pstatus ?>', '<?= htmlspecialchars($type->pname, ENT_QUOTES) ?>')">
                                                <i class="fas fa-eye"></i> แสดง
                                            </button>

                                            <!-- ปุ่มลบ -->
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deletePositionType(<?= $type->pid ?>, '<?= htmlspecialchars($type->pname, ENT_QUOTES) ?>', <?= isset($type->filled_positions) ? $type->filled_positions : 0 ?>)">
                                                <i class="fas fa-trash"></i> ลบ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal แก้ไขประเภทตำแหน่ง -->
<div class="modal fade" id="editTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit text-warning"></i> แก้ไขประเภทตำแหน่ง
                </h5>
                <button type="button" class="close" onclick="$('#editTypeModal').modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTypeForm" action="<?= site_url('dynamic_position_backend/update_position_type') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" id="edit_type_id" name="type_id">

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">ชื่อประเภท (ภาษาอังกฤษ)</label>
                        <div class="col-sm-9">
                            <input type="text" id="edit_peng" name="peng" class="form-control" readonly>
                            <small class="form-text text-muted">ไม่สามารถแก้ไขได้หลังจากสร้างแล้ว</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">ชื่อแสดงผล <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" id="edit_pname" name="pname" class="form-control" required
                                placeholder="เช่น คณาจารย์, เจ้าหน้าที่, นักศึกษาช่วยงาน">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">คำอธิบาย</label>
                        <div class="col-sm-9">
                            <textarea id="edit_pdescription" name="pdescription" class="form-control" rows="3"
                                placeholder="คำอธิบายเกี่ยวกับประเภทตำแหน่งนี้"></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">ลำดับการแสดงผล</label>
                        <div class="col-sm-9">
                            <input type="number" id="edit_porder" name="porder" class="form-control"
                                min="0" max="999" placeholder="0">
                            <small class="form-text text-muted">ตัวเลขน้อยจะแสดงก่อน</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">การแสดงผลซับเมนู</label>
                        <div class="col-sm-9">
                            <input type="number" id="edit_psub" name="psub" class="form-control"
                                min="0" max="1" placeholder="0">
                            <small class="form-text text-muted"> ต้องตั้งลำดับการแสดงผลให้ถูกต้องก่อน (เช่น 0=ไม่เป็น, 1=เป็น,)</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="$('#editTypeModal').modal('hide')" aria-label="Close">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript สำหรับการทำงาน -->
<script>
    // Quick add slots function
    function quickAddSlots(peng, currentTotal) {
        let suggestedCount = 6;

        Swal.fire({
            title: 'เพิ่มตำแหน่งใหม่',
            html: `
            <div class="text-left">
                <p>ตำแหน่งปัจจุบัน: <strong>${currentTotal}</strong></p>
                <p>แนะนำให้เพิ่ม: <strong>${suggestedCount}</strong> ตำแหน่ง</p>
                <hr>
                <label for="quick-slots-count">จำนวนที่ต้องการเพิ่ม:</label>
                <select id="quick-slots-count" class="form-control">
                    <option value="3">3 ตำแหน่ง</option>
                    <option value="6" selected>6 ตำแหน่ง</option>
                    <option value="9">9 ตำแหน่ง</option>
                    <option value="12">12 ตำแหน่ง</option>
                </select>
            </div>
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'เพิ่มตำแหน่ง',
            cancelButtonText: 'ยกเลิก',
            showDenyButton: true,
            denyButtonText: 'ไปหน้าจัดการ',
            denyButtonColor: '#007bff',
            preConfirm: () => {
                return document.getElementById('quick-slots-count').value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const count = result.value;
                performQuickAddSlots(peng, count);
            } else if (result.isDenied) {
                window.location.href = `<?= site_url('dynamic_position_backend/manage/') ?>${peng}`;
            }
        });
    }

    function performQuickAddSlots(peng, count) {
        // แสดง loading
        Swal.fire({
            title: 'กำลังเพิ่มตำแหน่ง...',
            html: `กำลังสร้าง ${count} ตำแหน่งใหม่`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // ส่งข้อมูลไป backend
        fetch(`<?= site_url('dynamic_position_backend/add_slots/') ?>${peng}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    count: parseInt(count)
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'เพิ่มตำแหน่งเรียบร้อย!',
                        html: `
                    <div class="text-left">
                        <p>เพิ่มแล้ว: <strong>${data.added_slots.length}</strong> ตำแหน่ง</p>
                        <p>ตำแหน่งใหม่: ${data.new_slots_range.start} - ${data.new_slots_range.end}</p>
                        <p>รวมทั้งหมด: <strong>${data.total_slots}</strong> ตำแหน่ง</p>
                    </div>
                `,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        // รีโหลดหน้า
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: data.message || 'ไม่สามารถเพิ่มตำแหน่งได้'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                });
            });
    }

    // Auto-refresh สถิติทุก 5 นาที
    document.addEventListener('DOMContentLoaded', function() {
        setInterval(function() {
            // อัปเดตสถิติแต่ละการ์ดโดยไม่รีโหลดทั้งหน้า
            updateAllTypeStats();
        }, 300000); // 5 นาที
    });

    function updateAllTypeStats() {
        const typeCards = document.querySelectorAll('[data-type-peng]');

        typeCards.forEach(function(card) {
            const peng = card.getAttribute('data-type-peng');
            updateTypeStats(peng, card);
        });
    }

    function updateTypeStats(peng, cardElement) {
        fetch(`<?= site_url('dynamic_position_backend/get_slot_stats/') ?>${peng}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCardStats(cardElement, data.stats);
                }
            })
            .catch(error => {
                console.log(`Failed to refresh stats for ${peng}:`, error);
            });
    }

    function updateCardStats(cardElement, stats) {
        // อัปเดตตัวเลขในการ์ด
        const filledElement = cardElement.querySelector('[data-stat="filled"]');
        const totalElement = cardElement.querySelector('[data-stat="total"]');
        const percentElement = cardElement.querySelector('[data-stat="percent"]');
        const progressBar = cardElement.querySelector('.progress-bar');

        if (filledElement) filledElement.textContent = stats.filled_slots;
        if (totalElement) totalElement.textContent = stats.total_slots;
        if (percentElement) percentElement.textContent = stats.usage_percentage + '%';

        if (progressBar) {
            progressBar.style.width = stats.usage_percentage + '%';
            progressBar.setAttribute('aria-valuenow', stats.usage_percentage);

            // อัปเดตสีของ progress bar ตามเปอร์เซ็นต์
            progressBar.className = progressBar.className.replace(/bg-\w+/, '');
            if (stats.usage_percentage >= 90) {
                progressBar.classList.add('bg-danger');
            } else if (stats.usage_percentage >= 70) {
                progressBar.classList.add('bg-warning');
            } else if (stats.usage_percentage >= 40) {
                progressBar.classList.add('bg-info');
            } else {
                progressBar.classList.add('bg-secondary');
            }
        }
    }

    // แก้ไขประเภทตำแหน่ง
    function editPositionType(typeId, pname, peng, pdescription, porder, psub) {
        document.getElementById('edit_type_id').value = typeId;
        document.getElementById('edit_peng').value = peng;
        document.getElementById('edit_pname').value = pname;
        document.getElementById('edit_pdescription').value = pdescription;
        document.getElementById('edit_porder').value = porder;
        document.getElementById('edit_psub').value = psub;

        $('#editTypeModal').modal('show');
    }

    // เปลี่ยนสถานะ
    function toggleStatus(typeId, currentStatus, typeName) {
        const newStatus = currentStatus === 'show' ? 'hide' : 'show';
        const actionText = newStatus === 'show' ? 'แสดง' : 'ซ่อน';

        Swal.fire({
            title: `ยืนยันการ${actionText}ประเภทตำแหน่ง?`,
            html: `
                <div class="text-left">
                    <strong>ประเภทตำแหน่ง:</strong> ${typeName}<br>
                    <strong>สถานะปัจจุบัน:</strong> ${currentStatus === 'show' ? 'แสดงผล' : 'ซ่อน'}<br>
                    <strong>สถานะใหม่:</strong> ${newStatus === 'show' ? 'แสดงผล' : 'ซ่อน'}<br><br>
                    <small class="text-muted">
                        ${newStatus === 'show' ? 
                            'ประเภทตำแหน่งนี้จะแสดงในหน้าเว็บไซต์' : 
                            'ประเภทตำแหน่งนี้จะไม่แสดงในหน้าเว็บไซต์ แต่ยังคงสามารถจัดการข้อมูลได้'}
                    </small>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: newStatus === 'show' ? '#28a745' : '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `ใช่, ${actionText}!`,
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // แสดง loading
                Swal.fire({
                    title: `กำลัง${actionText}ประเภทตำแหน่ง...`,
                    html: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // ส่งข้อมูลด้วย AJAX หรือ redirect
                window.location.href = `<?= site_url('dynamic_position_backend/toggle_status/') ?>${typeId}/${newStatus}`;
            }
        });
    }

    // ลบประเภทตำแหน่ง
    function deletePositionType(typeId, typeName, filledPositions) {
        // ตรวจสอบว่ามีข้อมูลบุคลากรหรือไม่
        if (filledPositions > 0) {
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถลบได้',
                html: `
                    <div class="text-left">
                        <p>ประเภทตำแหน่ง "<strong>${typeName}</strong>" มีข้อมูลบุคลากรอยู่ <strong>${filledPositions}</strong> คน</p>
                        <p>กรุณาลบข้อมูลบุคลากรทั้งหมดก่อน หรือย้ายไปประเภทอื่น</p>
                    </div>
                `,
                confirmButtonText: 'เข้าใจแล้ว'
            });
            return;
        }

        Swal.fire({
            title: 'ยืนยันการลบประเภทตำแหน่ง?',
            html: `
                <div class="text-left">
                    <strong>ประเภทตำแหน่ง:</strong> ${typeName}<br>
                    <strong>จำนวนข้อมูล:</strong> ${filledPositions} คน<br><br>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>คำเตือน:</strong> การลบนี้ไม่สามารถยกเลิกได้!<br>
                        ข้อมูลทั้งหมดในประเภทนี้จะถูกลบถาวร
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // แสดง loading
                Swal.fire({
                    title: 'กำลังลบข้อมูล...',
                    html: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // ส่งข้อมูลด้วย AJAX หรือ redirect
                window.location.href = `<?= site_url('dynamic_position_backend/delete_position_type/') ?>${typeId}`;
            }
        });
    }

    // Validation สำหรับฟอร์มแก้ไข
    document.getElementById('editTypeForm').addEventListener('submit', function(e) {
        const pname = document.getElementById('edit_pname').value.trim();

        if (!pname) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'กรุณากรอกชื่อแสดงผล',
                text: 'ชื่อแสดงผลจำเป็นต้องกรอก'
            });
            return false;
        }

        // แสดง loading
        Swal.fire({
            title: 'กำลังบันทึกข้อมูล...',
            html: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // เอฟเฟกต์ hover สำหรับการ์ด
        const cards = document.querySelectorAll('.card');
        cards.forEach(function(card) {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'transform 0.3s ease';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // อัพเดตสถิติแบบ real-time
        setInterval(function() {
            // TODO: เรียก AJAX เพื่ออัพเดตสถิติ
        }, 30000); // ทุก 30 วินาที
    });
</script>

<!-- CSS เพิ่มเติม -->
<style>
    .card {
        transition: all 0.3s ease;
        position: relative;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .position-type-card[data-status="hide"] {
        opacity: 0.7;
        background-color: #f8f9fa;
    }

    .position-type-card[data-status="hide"] .card-body {
        position: relative;
    }

    .position-type-card[data-status="hide"]::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: repeating-linear-gradient(45deg,
                transparent,
                transparent 10px,
                rgba(0, 0, 0, 0.05) 10px,
                rgba(0, 0, 0, 0.05) 20px);
        pointer-events: none;
        border-radius: 0.375rem;
    }

    .progress {
        background-color: rgba(0, 0, 0, 0.1);
    }

    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-secondary {
        border-left: 0.25rem solid #6c757d !important;
    }

    .text-secondary {
        color: #6c757d !important;
    }

    /* แท็บ Navigation */
    .nav-pills .nav-link {
        border-radius: 0.5rem;
        margin-right: 0.5rem;
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link.active {
        background-color: #007bff;
        border-color: #007bff;
    }

    .nav-pills .nav-link:not(.active) {
        background-color: transparent;
        border: 1px solid #dee2e6;
        color: #495057;
    }

    .nav-pills .nav-link:not(.active):hover {
        background-color: #f8f9fa;
        border-color: #007bff;
        color: #007bff;
    }

    /* การ์ดที่ซ่อน */
    .position-type-card[data-status="hide"] {
        opacity: 0.85;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .position-type-card[data-status="hide"]::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: repeating-linear-gradient(45deg,
                transparent,
                transparent 8px,
                rgba(108, 117, 125, 0.1) 8px,
                rgba(108, 117, 125, 0.1) 16px);
        pointer-events: none;
        border-radius: 0.375rem;
        z-index: 1;
    }

    .position-type-card[data-status="hide"] .card-body {
        position: relative;
        z-index: 2;
    }

    /* Alert สำหรับข้อมูลที่ซ่อน */
    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeaa7;
        color: #856404;
        border-radius: 0.5rem;
    }

    /* Badge ใน Tab */
    .nav-pills .badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .text-primary {
        color: #4e73df !important;
    }

    .text-success {
        color: #1cc88a !important;
    }

    .btn-group .btn {
        flex: 1;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .btn-group .btn i {
        font-size: 0.8em;
    }

    /* แอนิเมชันสำหรับ progress bar */
    .progress-bar {
        transition: width 0.6s ease;
    }

    /* Modal styles */
    .modal-content {
        border-radius: 0.5rem;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        border-bottom: 1px solid #dee2e6;
        background-color: #f8f9fc;
    }

    .modal-footer {
        border-top: 1px solid #dee2e6;
        background-color: #f8f9fc;
    }

    /* Badge positioning */
    .position-absolute {
        z-index: 10;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .col-xl-4 {
            margin-bottom: 1rem;
        }

        .btn-group .btn {
            font-size: 0.7rem;
            padding: 0.2rem 0.3rem;
        }

        .btn-group .btn i {
            font-size: 0.7em;
        }

        .modal-dialog {
            margin: 1rem;
        }

        .form-group.row .col-sm-3,
        .form-group.row .col-sm-9 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .form-group.row .col-sm-3 {
            margin-bottom: 0.5rem;
        }
    }

    /* Loading animation */
    .swal2-loading {
        border-width: 4px;
    }

    /* Status badges */
    .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.5rem;
    }

    /* แก้ไขปัญหา Modal backdrop ทึบ */
    .modal-backdrop {
        z-index: 1040 !important;
    }

    .modal {
        z-index: 1050 !important;
    }

    .modal-dialog {
        z-index: 1060 !important;
    }

    /* แก้ไขการแสดงผล Modal */
    .modal.fade .modal-dialog {
        transform: translate(0, -50px);
        transition: transform 0.3s ease-out;
    }

    .modal.show .modal-dialog {
        transform: none;
    }

    /* แก้ไข overlay ที่อาจชนกัน */
    .swal2-container {
        z-index: 1070 !important;
    }

    /* ป้องกัน Modal ซ้อนกัน */
    body.modal-open {
        overflow: hidden;
    }

    /* แก้ไขการ scroll */
    .modal-open .modal {
        overflow-x: hidden;
        overflow-y: auto;
    }

    /* สีสำหรับสถานะการใช้งาน */
    .badge-danger {
        background-color: #dc3545 !important;
    }

    .badge-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    .badge-info {
        background-color: #17a2b8 !important;
    }

    .badge-secondary {
        background-color: #6c757d !important;
    }

    /* Hover effects สำหรับปุ่มเพิ่มตำแหน่ง */
    .btn-warning.btn-sm:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
    }

    /* Animation สำหรับการอัปเดตสถิติ */
    @keyframes statUpdate {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
            color: #007bff;
        }

        100% {
            transform: scale(1);
        }
    }

    .stat-updating {
        animation: statUpdate 0.5s ease;
    }

    /* Progress bar transitions */
    .progress-bar {
        transition: width 0.6s ease, background-color 0.3s ease;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .btn-group .btn-sm {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }

        .badge {
            font-size: 0.6em;
        }
    }

    /* Loading states */
    .card-loading {
        opacity: 0.7;
        pointer-events: none;
    }

    .card-loading::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-loading::before {
        content: '\f110';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.5rem;
        color: #007bff;
        animation: spin 1s linear infinite;
        z-index: 10;
    }
</style>