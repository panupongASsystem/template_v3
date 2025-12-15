<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">สถิติการใช้งานระบบ</h1>
        <div>
            <a href="<?= site_url('logs_controller'); ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-list"></i> ดู Logs ทั้งหมด
            </a>
        </div>
    </div>

    <!-- ตัวเลือกช่วงเวลา -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar"></i> เลือกช่วงเวลา
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= site_url('logs_controller/statistics'); ?>" class="form-inline">
                <div class="form-group mr-3">
                    <label for="days" class="mr-2">ช่วงเวลา:</label>
                    <select name="days" class="form-control" onchange="this.form.submit()">
                        <option value="7" <?= ($days == 7) ? 'selected' : ''; ?>>7 วันที่ผ่านมา</option>
                        <option value="30" <?= ($days == 30) ? 'selected' : ''; ?>>30 วันที่ผ่านมา</option>
                        <option value="60" <?= ($days == 60) ? 'selected' : ''; ?>>60 วันที่ผ่านมา</option>
                        <option value="90" <?= ($days == 90) ? 'selected' : ''; ?>>90 วันที่ผ่านมา</option>
                        <option value="180" <?= ($days == 180) ? 'selected' : ''; ?>>180 วันที่ผ่านมา</option>
                        <option value="365" <?= ($days == 365) ? 'selected' : ''; ?>>1 ปีที่ผ่านมา</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- สรุปภาพรวม -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                การดำเนินการทั้งหมด
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $total = 0;
                                if (!empty($activity_stats)) {
                                    foreach ($activity_stats as $stat) {
                                        $total += $stat->count;
                                    }
                                }
                                echo number_format($total);
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                ผู้ใช้ที่มีกิจกรรม
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= !empty($user_stats) ? count($user_stats) : '0'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                เมนูที่ใช้งาน
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= !empty($menu_stats) ? count($menu_stats) : '0'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                ค่าเฉลี่ยต่อวัน
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $days > 0 ? number_format($total / $days, 1) : '0'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- สถิติการดำเนินการ -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie"></i> สถิติการดำเนินการ (<?= $days; ?> วันที่ผ่านมา)
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($activity_stats)): ?>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="activityChart"></canvas>
                        </div>
                        <div class="mt-3">
                            <?php foreach ($activity_stats as $stat): ?>
                                <?php
                                $badge_class = '';
                                switch($stat->action) {
                                    case 'เพิ่ม': $badge_class = 'badge-success'; break;
                                    case 'แก้ไข': $badge_class = 'badge-warning'; break;
                                    case 'ลบ': $badge_class = 'badge-danger'; break;
                                    case 'เข้าชม': $badge_class = 'badge-info'; break;
                                    case 'ดาวน์โหลด': $badge_class = 'badge-primary'; break;
                                    default: $badge_class = 'badge-secondary';
                                }
                                ?>
                                <span class="badge <?= $badge_class; ?> mr-2 mb-1">
                                    <?= $stat->action; ?>: <?= number_format($stat->count); ?> ครั้ง
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-chart-pie fa-3x mb-3"></i>
                            <p>ไม่มีข้อมูลสถิติในช่วงเวลานี้</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
                    </div>