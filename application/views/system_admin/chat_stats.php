<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถิติการใช้งานระบบแชท - ผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/date-fns@2.29.3/index.min.js"></script>
    <style>
        body { 
            font-family: 'Sarabun', 'Prompt', sans-serif; 
            font-size: 16px;
            line-height: 1.6;
        }
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
        }
        .card-title {
            font-weight: 500;
        }
        .table th {
            font-weight: 600;
        }
        .thai-number {
            font-family: 'Sarabun', sans-serif;
            font-weight: 600;
        }
        .badge {
            font-family: 'Sarabun', sans-serif;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-graph-up"></i> สถิติการใช้งานระบบแชท</h2>
                    <div>
                        <a href="<?= site_url('chat_backend') ?>" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i> กลับไปการตั้งค่า
                        </a>
                        <a href="<?= site_url('chat_backend/export_logs') ?>?date_from=<?= $date_from ?>&date_to=<?= $date_to ?>" 
                           class="btn btn-success">
                            <i class="bi bi-download"></i> ส่งออก CSV
                        </a>
                    </div>
                </div>

                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $this->session->flashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $this->session->flashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>ข้อผิดพลาด:</strong> <?= html_escape($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <hr>
                        <p class="mb-0">
                            <strong>วิธีแก้ไข:</strong><br>
                            1. ตรวจสอบให้แน่ใจว่าได้สร้างตารางฐานข้อมูลแล้ว<br>
                            2. รันสคริปต์ SQL เพื่อสร้างตาราง chat_logs<br>
                            3. ตรวจสอบการตั้งค่าการเชื่อมต่อฐานข้อมูล
                        </p>
                    </div>
                <?php endif; ?>

                <!-- ข้อมูลดีบัก (โหมดพัฒนาเท่านั้น) -->
                <?php if (isset($debug) && ENVIRONMENT === 'development'): ?>
                    <div class="alert alert-info">
                        <strong>ข้อมูลดีบัก:</strong>
                        <pre><?= print_r($debug, true) ?></pre>
                    </div>
                <?php endif; ?>

                <!-- ตัวกรองช่วงวันที่ -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="get" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">วันที่เริ่มต้น</label>
                                <input type="date" class="form-control" name="date_from" value="<?= $date_from ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">วันที่สิ้นสุด</label>
                                <input type="date" class="form-control" name="date_to" value="<?= $date_to ?>" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> ค้นหา
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="setQuickDate('7')">
                                    7 วันที่แล้ว
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-1" onclick="setQuickDate('30')">
                                    30 วันที่แล้ว
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- สรุปข้อมูลรวม -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-bg-primary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title mb-0">การสนทนาทั้งหมด</h6>
                                        <h2 class="mt-2 thai-number"><?= number_format($total_conversations ?? 0) ?></h2>
                                        <small>ในช่วงเวลาที่เลือก</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-chat-left-text-fill fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-bg-success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title mb-0">ผู้ใช้ที่ไม่ซ้ำ</h6>
                                        <h2 class="mt-2 thai-number"><?= number_format($unique_users ?? 0) ?></h2>
                                        <small>ผู้ใช้ที่เข้ามาใช้งาน</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-people-fill fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-bg-info h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title mb-0">เวลาตอบสนองเฉลี่ย</h6>
                                        <h2 class="mt-2 thai-number"><?= number_format(($avg_response_time ?? 0), 2) ?> วิ</h2>
                                        <small>เวลาตอบสนองเฉลี่ย</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-speedometer2 fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-bg-warning h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title mb-0">เฉลี่ยต่อวัน</h6>
                                        <h2 class="mt-2 thai-number">
                                            <?php 
                                            $days = max(1, (strtotime($date_to) - strtotime($date_from)) / 86400 + 1);
                                            echo number_format(($total_conversations ?? 0) / $days, 1); 
                                            ?>
                                        </h2>
                                        <small>การสนทนา/วัน</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-calendar-day fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- แถวกราฟ -->
                <div class="row mb-4">
                    <!-- กราฟการสนทนารายวัน -->
                    <div class="col-lg-8 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5><i class="bi bi-bar-chart"></i> การสนทนารายวัน</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="conversationsChart" style="max-height: 400px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- กราฟเวลาตอบสนอง -->
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5><i class="bi bi-clock"></i> แนวโน้มเวลาตอบสนอง</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="responseTimeChart" style="max-height: 400px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- วิเคราะห์ผู้ใช้ -->
                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-person-circle"></i> การกระจายประเภทผู้ใช้</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="userTypeChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-clock-history"></i> รูปแบบการใช้งานตามชั่วโมง</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="hourlyChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ตารางรายละเอียด -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-table"></i> รายละเอียดรายวัน</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>วันที่</th>
                                        <th>การสนทนา</th>
                                        <th>ผู้ใช้ไม่ซ้ำ</th>
                                        <th>เวลาตอบสนองเฉลี่ย</th>
                                        <th>อัตราความสำเร็จ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($stats)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">ไม่มีข้อมูลในช่วงเวลาที่เลือก</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach (array_reverse($stats) as $stat): ?>
                                            <tr>
                                                <td><?= date('d/m/Y', strtotime($stat['date'])) ?></td>
                                                <td>
                                                    <span class="badge bg-primary rounded-pill">
                                                        <?= number_format($stat['total_conversations']) ?>
                                                    </span>
                                                </td>
                                                <td class="thai-number"><?= number_format($stat['unique_users']) ?></td>
                                                <td>
                                                    <?php if ($stat['avg_response_time'] > 0): ?>
                                                        <span class="<?= $stat['avg_response_time'] > 3 ? 'text-warning' : 'text-success' ?> thai-number">
                                                            <?= number_format($stat['avg_response_time'], 2) ?> วิ
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">ไม่มีข้อมูล</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $success_rate = $stat['total_conversations'] > 0 ? 
                                                        (($stat['total_conversations'] - 0) / $stat['total_conversations']) * 100 : 0;
                                                    ?>
                                                    <div class="progress" style="width: 80px; height: 20px;">
                                                        <div class="progress-bar bg-success" 
                                                             style="width: <?= $success_rate ?>%"
                                                             title="<?= number_format($success_rate, 1) ?>%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // เตรียมข้อมูลจาก PHP พร้อมตรวจสอบข้อผิดพลาด
        let statsData = [];
        let userTypes = [];
        let hourlyData = Array(24).fill(0);

        try {
            statsData = <?= json_encode(array_values($stats ?? []), JSON_NUMERIC_CHECK) ?>;
            userTypes = <?= json_encode($user_types ?? [], JSON_NUMERIC_CHECK) ?>;
            hourlyData = <?= json_encode($hourly_usage ?? array_fill(0, 24, 0), JSON_NUMERIC_CHECK) ?>;

            console.log('ข้อมูลถูกโหลดแล้ว:', {
                จำนวนสถิติ: statsData.length,
                จำนวนประเภทผู้ใช้: userTypes.length,
                รวมการใช้งานรายชั่วโมง: hourlyData.reduce((a, b) => a + b, 0)
            });
        } catch (e) {
            console.error('เกิดข้อผิดพลาดในการแปลงข้อมูลจาก PHP:', e);
            // ใช้ข้อมูลสำรอง
            statsData = [];
            userTypes = [
                {user_type: 'guest', count: 0},
                {user_type: 'member', count: 0}
            ];
            hourlyData = Array(24).fill(0);
        }
        
        // ฟังก์ชันเลือกวันที่แบบเร็ว
        function setQuickDate(days) {
            const today = new Date();
            const fromDate = new Date(today.getTime() - (days * 24 * 60 * 60 * 1000));
            
            document.querySelector('input[name="date_from"]').value = fromDate.toISOString().split('T')[0];
            document.querySelector('input[name="date_to"]').value = today.toISOString().split('T')[0];
        }

        // สีของกราฟ
        const colors = {
            primary: '#0d6efd',
            success: '#198754',
            info: '#0dcaf0',
            warning: '#ffc107',
            danger: '#dc3545',
            secondary: '#6c757d'
        };

        // ตรวจสอบว่า Chart.js ถูกโหลดหรือไม่
        if (typeof Chart === 'undefined') {
            console.error('Chart.js ไม่ได้ถูกโหลด');
            document.querySelectorAll('canvas').forEach(canvas => {
                canvas.parentNode.innerHTML = '<div class="text-danger">Chart.js ไม่สามารถโหลดได้</div>';
            });
        } else {
            // 1. กราฟการสนทนารายวัน
            try {
                const conversationsCtx = document.getElementById('conversationsChart');
                if (conversationsCtx) {
                    new Chart(conversationsCtx, {
                        type: 'line',
                        data: {
                            labels: statsData.map(item => {
                                if (!item.date) return 'ไม่มีข้อมูล';
                                const date = new Date(item.date);
                                return date.toLocaleDateString('th-TH', { 
                                    month: 'short', 
                                    day: 'numeric',
                                    year: 'numeric'
                                });
                            }),
                            datasets: [{
                                label: 'การสนทนา',
                                data: statsData.map(item => parseInt(item.total_conversations) || 0),
                                borderColor: colors.primary,
                                backgroundColor: colors.primary + '20',
                                tension: 0.4,
                                fill: true
                            }, {
                                label: 'ผู้ใช้ไม่ซ้ำ',
                                data: statsData.map(item => parseInt(item.unique_users) || 0),
                                borderColor: colors.success,
                                backgroundColor: colors.success + '20',
                                tension: 0.4,
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: {
                                            family: 'Sarabun'
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0,
                                        font: {
                                            family: 'Sarabun'
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            family: 'Sarabun'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (e) {
                console.error('เกิดข้อผิดพลาดในการสร้างกราฟการสนทนา:', e);
                document.getElementById('conversationsChart').parentNode.innerHTML = 
                    '<div class="text-danger">เกิดข้อผิดพลาดในการโหลดกราฟ: ' + e.message + '</div>';
            }

            // 2. กราฟเวลาตอบสนอง
            try {
                const responseTimeCtx = document.getElementById('responseTimeChart');
                if (responseTimeCtx) {
                    const recentStats = statsData.slice(-7);
                    new Chart(responseTimeCtx, {
                        type: 'bar',
                        data: {
                            labels: recentStats.map(item => {
                                if (!item.date) return 'ไม่มีข้อมูล';
                                const date = new Date(item.date);
                                return date.toLocaleDateString('th-TH', { 
                                    month: 'short', 
                                    day: 'numeric' 
                                });
                            }),
                            datasets: [{
                                label: 'เวลาตอบสนอง (วินาที)',
                                data: recentStats.map(item => parseFloat(item.avg_response_time) || 0),
                                backgroundColor: colors.info,
                                borderColor: colors.info,
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
                                        callback: function(value) {
                                            return value + ' วิ';
                                        },
                                        font: {
                                            family: 'Sarabun'
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            family: 'Sarabun'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (e) {
                console.error('เกิดข้อผิดพลาดในการสร้างกราฟเวลาตอบสนอง:', e);
                document.getElementById('responseTimeChart').parentNode.innerHTML = 
                    '<div class="text-danger">เกิดข้อผิดพลาดในการโหลดกราฟ: ' + e.message + '</div>';
            }

            // 3. กราฟประเภทผู้ใช้
            try {
                const userTypeCtx = document.getElementById('userTypeChart');
                if (userTypeCtx && userTypes.length > 0) {
                    new Chart(userTypeCtx, {
                        type: 'doughnut',
                        data: {
                            labels: userTypes.map(item => {
                                switch(item.user_type) {
                                    case 'guest': return 'ผู้เยี่ยมชม';
                                    case 'member': return 'สมาชิก';
                                    default: return item.user_type;
                                }
                            }),
                            datasets: [{
                                data: userTypes.map(item => parseInt(item.count) || 0),
                                backgroundColor: [
                                    colors.primary,
                                    colors.success,
                                    colors.warning,
                                    colors.info
                                ],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        font: {
                                            family: 'Sarabun'
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    document.getElementById('userTypeChart').parentNode.innerHTML = 
                        '<div class="text-muted">ไม่มีข้อมูลประเภทผู้ใช้</div>';
                }
            } catch (e) {
                console.error('เกิดข้อผิดพลาดในการสร้างกราฟประเภทผู้ใช้:', e);
                document.getElementById('userTypeChart').parentNode.innerHTML = 
                    '<div class="text-danger">เกิดข้อผิดพลาดในการโหลดกราฟ: ' + e.message + '</div>';
            }

            // 4. กราฟการใช้งานรายชั่วโมง
            try {
                const hourlyCtx = document.getElementById('hourlyChart');
                if (hourlyCtx) {
                    new Chart(hourlyCtx, {
                        type: 'line',
                        data: {
                            labels: Array.from({length: 24}, (_, i) => String(i).padStart(2, '0') + ':00'),
                            datasets: [{
                                label: 'ข้อความต่อชั่วโมง',
                                data: hourlyData.map(count => parseInt(count) || 0),
                                borderColor: colors.warning,
                                backgroundColor: colors.warning + '20',
                                tension: 0.4,
                                fill: true
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
                                        precision: 0,
                                        font: {
                                            family: 'Sarabun'
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            family: 'Sarabun'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (e) {
                console.error('เกิดข้อผิดพลาดในการสร้างกราฟรายชั่วโมง:', e);
                document.getElementById('hourlyChart').parentNode.innerHTML = 
                    '<div class="text-danger">เกิดข้อผิดพลาดในการโหลดกราฟ: ' + e.message + '</div>';
            }
        }

        // รีเฟรชอัตโนมัติทุก 5 นาที (เฉพาะเมื่อไม่มีข้อผิดพลาด)
        <?php if (!isset($error)): ?>
        setTimeout(() => {
            console.log('กำลังรีเฟรชหน้าสถิติอัตโนมัติ...');
            window.location.reload();
        }, 5 * 60 * 1000);
        <?php endif; ?>

        // ฟังก์ชันสำหรับการพิมพ์
        function printStats() {
            window.print();
        }
    </script>

    <style>
        /* สไตล์สำหรับการพิมพ์ */
        @media print {
            .btn, .card-header { 
                background: #f8f9fa !important; 
                color: #000 !important; 
            }
            .text-bg-primary, .text-bg-success, .text-bg-info, .text-bg-warning {
                background: #f8f9fa !important;
                color: #000 !important;
            }
        }

        .progress {
            background-color: #e9ecef;
        }
        
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        
        .table th {
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        canvas {
            max-height: 400px;
        }

        /* เพิ่มเอฟเฟกต์สำหรับการ์ด */
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        /* เพิ่มสไตล์สำหรับ badge */
        .badge {
            font-size: 0.9em;
        }

        /* สไตล์สำหรับตัวเลขไทย */
        .thai-number {
            letter-spacing: -0.5px;
        }

        /* เพิ่มการเน้นสำหรับหัวข้อ */
        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
        }

        .card-header h5 {
            margin-bottom: 0;
            color: #495057;
        }

        /* สไตล์สำหรับปุ่ม */
        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</body>
</html>