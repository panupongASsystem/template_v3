<!-- position_statistics.php - หน้าสถิติการใช้งาน -->
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar text-info"></i>
            สถิติการใช้งาน: <?= $type->pname ?>
        </h1>
        <div>
            <a href="<?= site_url('dynamic_position_backend/manage/' . $type->peng) ?>"
                class="btn btn-primary">
                <i class="fas fa-cog"></i> จัดการข้อมูล
            </a>
            <a href="<?= site_url('dynamic_position_backend') ?>"
                class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>
    </div>

    <!-- สถิติรวม -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                ตำแหน่งทั้งหมด
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" data-stat="total-slots">
                                <?= $total_slots ?> ช่อง
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-th fa-2x text-gray-300"></i>
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
                                ตำแหน่งที่มีข้อมูล
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $filled_slots ?> ช่อง
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                                ตำแหน่งว่าง
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $empty_slots ?> ช่อง
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
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
                                อัตราการใช้งาน
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $usage_percentage ?>%
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: <?= $usage_percentage ?>%"
                                            aria-valuenow="<?= $usage_percentage ?>"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- กราฟและตาราง -->
    <div class="row">
        <!-- กราฟแสดงสถิติ -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">กราฟแสดงการใช้งาน</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <div class="dropdown-header">ตัวเลือก:</div>
                            <a class="dropdown-item" href="#" onclick="exportChart()">ส่งออกกราฟ</a>
                            <a class="dropdown-item" href="#" onclick="refreshChart()">รีเฟรช</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="usageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- สถิติแบ่งตามหมวดหมู่ -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">สรุปการใช้งาน</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <canvas id="pieChart" width="200" height="200"></canvas>
                    </div>
                    <div class="mt-4">
                        <div class="small">
                            <span class="mr-2">
                                <i class="fas fa-circle text-success"></i> มีข้อมูล
                            </span>
                            <?= $filled_slots ?> ช่อง (<?= $usage_percentage ?>%)
                        </div>
                        <div class="small">
                            <span class="mr-2">
                                <i class="fas fa-circle text-secondary"></i> ว่าง
                            </span>
                            <?= $empty_slots ?> ช่อง (<?= 100 - $usage_percentage ?>%)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ตารางสถิติรายแถว -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">สถิติการใช้งานรายแถว</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>แถวที่</th>
                                    <th>ตำแหน่งทั้งหมด</th>
                                    <th>มีข้อมูล</th>
                                    <th>ว่าง</th>
                                    <th>อัตราการใช้งาน</th>
                                    <th>แสดงผล</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($row_stats) && !empty($row_stats)): ?>
                                    <?php foreach ($row_stats as $row_num => $stats): ?>
                                        <?php if ($stats['total'] > 0): ?>
                                            <?php
                                            $row_percentage = round(($stats['filled'] / $stats['total']) * 100, 1);
                                            $progress_color = $row_percentage >= 80 ? 'success' : ($row_percentage >= 50 ? 'warning' : 'danger');
                                            ?>
                                            <tr>
                                                <td>แถว <?= $row_num ?></td>
                                                <td><?= $stats['total'] ?></td>
                                                <td>
                                                    <span class="badge badge-success"><?= $stats['filled'] ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary"><?= $stats['empty'] ?></span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 mr-2" style="height: 15px;">
                                                            <div class="progress-bar bg-<?= $progress_color ?>"
                                                                role="progressbar"
                                                                style="width: <?= $row_percentage ?>%"
                                                                aria-valuenow="<?= $row_percentage ?>"
                                                                aria-valuemin="0"
                                                                aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <span class="text-sm"><?= $row_percentage ?>%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="<?= site_url('dynamic_position_backend/manage/' . $type->peng . '?row=' . $row_num) ?>"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye"></i> ดู
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">ไม่มีข้อมูลสถิติ</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-primary shadow">
                <div class="card-body text-white">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="text-white mb-2">
                                <i class="fas fa-lightbulb"></i> การดำเนินการที่แนะนำ
                            </h5>
                            <?php
                            $capacity_ratio = $total_slots > 0 ? ($filled_slots / $total_slots) : 0;
                            ?>

                            <?php if ($usage_percentage < 30): ?>
                                <p class="mb-2">
                                    อัตราการใช้งานยังต่ำมาก (<?= $usage_percentage ?>%)
                                    ควรเพิ่มข้อมูลบุคลากรหรือลดจำนวนตำแหน่งให้เหมาะสม
                                </p>
                            <?php elseif ($usage_percentage < 50): ?>
                                <p class="mb-2">
                                    อัตราการใช้งานต่ำ (<?= $usage_percentage ?>%)
                                    ควรเพิ่มข้อมูลบุคลากรเพิ่มเติม
                                </p>
                            <?php elseif ($usage_percentage < 80): ?>
                                <p class="mb-2">
                                    อัตราการใช้งานปานกลาง (<?= $usage_percentage ?>%)
                                    สามารถเพิ่มข้อมูลเพื่อความสมบูรณ์
                                </p>
                            <?php elseif ($usage_percentage < 95): ?>
                                <p class="mb-2">
                                    อัตราการใช้งานสูง (<?= $usage_percentage ?>%)
                                    ข้อมูลค่อนข้างครบถ้วนแล้ว
                                </p>
                            <?php else: ?>
                                <p class="mb-2">
                                    อัตราการใช้งานเต็มเกือบหมด (<?= $usage_percentage ?>%)
                                    ควรพิจารณาเพิ่มตำแหน่งใหม่หากมีบุคลากรเพิ่มเติม
                                </p>
                            <?php endif; ?>

                            <small class="text-light">
                                ตำแหน่งทั้งหมด: <?= $total_slots ?> |
                                ใช้งานแล้ว: <?= $filled_slots ?> |
                                ว่าง: <?= $empty_slots ?>
                            </small>
                        </div>
                        <div class="col-md-4 text-right">
                            <?php if ($usage_percentage < 95): ?>
                                <a href="<?= site_url('dynamic_position_backend/add_to_slot/' . $type->peng . '/' . ($total_slots > 0 ? $total_slots + 1 : 1)) ?>"
                                    class="btn btn-light">
                                    <i class="fas fa-plus"></i> เพิ่มข้อมูล
                                </a>
                            <?php endif; ?>

                            <?php if ($usage_percentage >= 90): ?>
                                <button type="button" class="btn btn-warning ml-2" onclick="suggestAddSlots()">
                                    <i class="fas fa-expand"></i> เพิ่มตำแหน่ง
                                </button>
                            <?php endif; ?>

                            <a href="<?= site_url('dynamic_position_backend/export_csv/' . $type->peng) ?>"
                                class="btn btn-success ml-2">
                                <i class="fas fa-file-excel"></i> ส่งออก CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
    // เพิ่ม JavaScript สำหรับแนะนำการเพิ่ม slots
    function suggestAddSlots() {
        const currentSlots = <?= $total_slots ?>;
        const filledSlots = <?= $filled_slots ?>;
        const usagePercent = <?= $usage_percentage ?>;

        let suggestedCount = 6; // เริ่มต้นแนะนำ 6 ตำแหน่ง

        if (usagePercent >= 98) {
            suggestedCount = 12;
        } else if (usagePercent >= 95) {
            suggestedCount = 9;
        }

        Swal.fire({
            title: 'แนะนำการเพิ่มตำแหน่ง',
            html: `
            <div class="text-left">
                <p>อัตราการใช้งานปัจจุบัน: <strong>${usagePercent}%</strong></p>
                <p>ตำแหน่งที่ใช้งาน: <strong>${filledSlots}/${currentSlots}</strong></p>
                <hr>
                <p class="text-info">💡 <strong>คำแนะนำ:</strong></p>
                <p>เนื่องจากอัตราการใช้งานสูง ควรเพิ่มตำแหน่งเพื่อรองรับบุคลากรใหม่</p>
                <p>แนะนำให้เพิ่ม <strong>${suggestedCount}</strong> ตำแหน่ง</p>
            </div>
        `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `เพิ่ม ${suggestedCount} ตำแหน่ง`,
            cancelButtonText: 'ปิด',
            showDenyButton: true,
            denyButtonText: 'ไปหน้าจัดการ',
            denyButtonColor: '#007bff'
        }).then((result) => {
            if (result.isConfirmed) {
                // ไปหน้าจัดการแล้วเปิด modal เพิ่ม slots
                window.location.href = '<?= site_url("dynamic_position_backend/manage/" . $type->peng) ?>?auto_add_slots=' + suggestedCount;
            } else if (result.isDenied) {
                // ไปหน้าจัดการ
                window.location.href = '<?= site_url("dynamic_position_backend/manage/" . $type->peng) ?>';
            }
        });
    }

    // Auto-refresh สถิติทุก 2 นาที
    setInterval(function() {
        // อัปเดตเฉพาะตัวเลขโดยไม่รีโหลดทั้งหน้า
        fetch('<?= site_url("dynamic_position_backend/get_slot_stats/" . $type->peng) ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // อัปเดตตัวเลขในหน้า
                    updateStatsDisplay(data.stats);
                }
            })
            .catch(error => {
                console.log('Failed to refresh stats:', error);
            });
    }, 120000); // 2 นาที

    function updateStatsDisplay(stats) {
        // อัปเดตเฉพาะส่วนที่จำเป็น
        const elements = {
            totalSlots: document.querySelector('[data-stat="total-slots"]'),
            filledSlots: document.querySelector('[data-stat="filled-slots"]'),
            emptySlots: document.querySelector('[data-stat="empty-slots"]'),
            usagePercent: document.querySelector('[data-stat="usage-percent"]')
        };

        if (elements.totalSlots) elements.totalSlots.textContent = stats.total_slots + ' ช่อง';
        if (elements.filledSlots) elements.filledSlots.textContent = stats.filled_slots + ' ช่อง';
        if (elements.emptySlots) elements.emptySlots.textContent = stats.empty_slots + ' ช่อง';
        if (elements.usagePercent) elements.usagePercent.textContent = stats.usage_percentage + '%';
    }

    // กราฟแสดงการใช้งาน
    document.addEventListener('DOMContentLoaded', function() {
        // Bar Chart
        const ctx = document.getElementById('usageChart').getContext('2d');
        const usageChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['ตำแหน่งทั้งหมด', 'มีข้อมูล', 'ว่าง'],
                datasets: [{
                    label: 'จำนวน',
                    data: [<?= $total_slots ?>, <?= $filled_slots ?>, <?= $empty_slots ?>],
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(54, 185, 204, 0.8)'
                    ],
                    borderColor: [
                        'rgba(78, 115, 223, 1)',
                        'rgba(28, 200, 138, 1)',
                        'rgba(54, 185, 204, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['มีข้อมูล', 'ว่าง'],
                datasets: [{
                    data: [<?= $filled_slots ?>, <?= $empty_slots ?>],
                    backgroundColor: [
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(133, 135, 150, 0.8)'
                    ],
                    borderColor: [
                        'rgba(28, 200, 138, 1)',
                        'rgba(133, 135, 150, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });

    function exportChart() {
        const canvas = document.getElementById('usageChart');
        const url = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = 'statistics_<?= $type->peng ?>_<?= date("Y-m-d") ?>.png';
        link.href = url;
        link.click();
    }

    function refreshChart() {
        location.reload();
    }

    // Auto refresh ทุก 5 นาที
    setInterval(function() {
        location.reload();
    }, 300000);
</script>

<!-- CSS เพิ่มเติม -->
<style>
    .chart-area {
        position: relative;
        height: 300px;
    }

    .progress-sm {
        height: 0.5rem;
        width: 100px;
    }

    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }

    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .table th {
        border-top: none;
        font-weight: 600;
        background-color: #f8f9fc;
    }

    .badge {
        font-size: 0.8em;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .chart-area {
            height: 250px;
        }

        .col-xl-3,
        .col-md-6 {
            margin-bottom: 1rem;
        }
    }
</style>