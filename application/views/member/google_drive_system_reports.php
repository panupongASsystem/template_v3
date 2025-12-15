<?php
// application/views/member/google_drive_system_reports.php
?>
<div class="ml-72 p-8 bg-gray-50 min-h-screen">
    <!-- Simple Header Section -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                        <?php echo $page_title; ?>
                    </h1>
                    <p class="text-gray-600 mt-1">
                        ช่วงเวลา: <span class="font-medium"><?php echo date('d/m/Y', strtotime($date_range['start'])); ?></span> 
                        ถึง <span class="font-medium"><?php echo date('d/m/Y', strtotime($date_range['end'])); ?></span>
                    </p>
                </div>
                
                <div class="flex gap-3">
                    <button onclick="refreshReport()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>รีเฟรช
                    </button>
                    <button onclick="exportReport()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                    <a href="<?php echo site_url('google_drive_system/dashboard'); ?>" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>กลับ Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Tab Navigation -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-sm border">
            <nav class="flex p-1">
                <button onclick="switchReportType('activities')" 
                        id="tab-activities" 
                        class="report-tab <?php echo $report_type === 'activities' ? 'active' : ''; ?>">
                    <i class="fas fa-history mr-2"></i>กิจกรรม
                </button>
                <button onclick="switchReportType('storage')" 
                        id="tab-storage" 
                        class="report-tab <?php echo $report_type === 'storage' ? 'active' : ''; ?>">
                    <i class="fas fa-hdd mr-2"></i>Storage
                </button>
                <button onclick="switchReportType('users')" 
                        id="tab-users" 
                        class="report-tab <?php echo $report_type === 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users mr-2"></i>ผู้ใช้งาน
                </button>
                <button onclick="switchReportType('folders')" 
                        id="tab-folders" 
                        class="report-tab <?php echo $report_type === 'folders' ? 'active' : ''; ?>">
                    <i class="fas fa-folder-tree mr-2"></i>โฟลเดอร์
                </button>
                <button onclick="switchReportType('logs')" 
                        id="tab-logs" 
                        class="report-tab <?php echo $report_type === 'logs' ? 'active' : ''; ?>">
                    <i class="fas fa-list-alt mr-2"></i>Activity Logs
                </button>
            </nav>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">
                    <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                    ตัวกรองช่วงเวลา
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">วันที่เริ่มต้น</label>
                        <input type="date" 
                               id="startDate" 
                               value="<?php echo $date_range['start']; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">วันที่สิ้นสุด</label>
                        <input type="date" 
                               id="endDate" 
                               value="<?php echo $date_range['end']; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ช่วงเวลาด่วน</label>
                        <select id="quickDateRange" 
                                onchange="setQuickDateRange(this.value)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">เลือกช่วงเวลา</option>
                            <option value="today">วันนี้</option>
                            <option value="yesterday">เมื่อวาน</option>
                            <option value="week">7 วันที่แล้ว</option>
                            <option value="month">30 วันที่แล้ว</option>
                            <option value="quarter">90 วันที่แล้ว</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button onclick="applyDateFilter()" 
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>กรองข้อมูล
                        </button>
                    </div>
                    <div class="flex items-end">
                        <button onclick="resetDateFilter()" 
                                class="w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-undo mr-2"></i>รีเซ็ต
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities Report -->
    <?php if ($report_type === 'activities'): ?>
    <div id="activitiesReport" class="space-y-8">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-chart-bar text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">กิจกรรมทั้งหมด</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($activities_summary['total'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">สำเร็จ</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($activities_summary['success'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">ล้มเหลว</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($activities_summary['failed'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-gray-100 rounded-lg">
                        <i class="fas fa-percentage text-gray-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">อัตราสำเร็จ</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php 
                            $total = $activities_summary['total'] ?? 0;
                            $success = $activities_summary['success'] ?? 0;
                            echo $total > 0 ? number_format(($success / $total) * 100, 1) : '0';
                            ?>%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">
                        <i class="fas fa-pie-chart text-blue-600 mr-2"></i>
                        ประเภทกิจกรรม
                    </h3>
                </div>
                <div class="p-6">
                    <div class="h-64">
                        <canvas id="activityTypesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">
                        <i class="fas fa-chart-line text-green-600 mr-2"></i>
                        กิจกรรมรายวัน
                    </h3>
                </div>
                <div class="p-6">
                    <div class="h-64">
                        <canvas id="dailyActivitiesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Users -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">
                    <i class="fas fa-user-friends text-purple-600 mr-2"></i>
                    ผู้ใช้ที่ใช้งานมากที่สุด
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php if (!empty($top_users)): ?>
                        <?php $rank = 1; foreach (array_slice($top_users, 0, 6) as $user => $count): ?>
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold mr-3">
                                    <?php echo $rank; ?>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($user); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo number_format($count); ?> กิจกรรม</p>
                                </div>
                            </div>
                            <?php $rank++; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-8 text-gray-500">
                            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                            <p>ไม่มีข้อมูลผู้ใช้งาน</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Activities Table -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-800">
                        <i class="fas fa-list text-blue-600 mr-2"></i>
                        กิจกรรมล่าสุด
                    </h3>
                    <div class="flex space-x-3">
                        <select id="activityFilter" onchange="filterActivities()" 
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">ทุกประเภท</option>
                            <option value="success">สำเร็จ</option>
                            <option value="failed">ล้มเหลว</option>
                            <option value="pending">รอดำเนินการ</option>
                        </select>
                        <input type="search" 
                               id="activitySearch" 
                               placeholder="ค้นหากิจกรรม..."
                               onkeyup="searchActivities()"
                               class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">เวลา</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ผู้ใช้</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">กิจกรรม</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">รายละเอียด</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">สถานะ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                        </tr>
                    </thead>
                    <tbody id="activitiesTableBody" class="divide-y divide-gray-200">
                        <?php if (!empty($activities)): ?>
                            <?php foreach (array_slice($activities, 0, 50) as $activity): ?>
                                <tr class="activity-row hover:bg-gray-50" 
                                    data-status="<?php echo $activity->status; ?>"
                                    data-search="<?php echo strtolower($activity->action_description . ' ' . $activity->first_name . ' ' . $activity->last_name); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo date('d/m/Y', strtotime($activity->created_at)); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo date('H:i:s', strtotime($activity->created_at)); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-gray-600 text-xs"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars(trim($activity->first_name . ' ' . $activity->last_name)); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($activity->username); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo getActivityTypeColor($activity->action_type); ?>">
                                            <i class="<?php echo getActivityTypeIcon($activity->action_type); ?> mr-1"></i>
                                            <?php echo getActivityTypeName($activity->action_type); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate" title="<?php echo htmlspecialchars($activity->action_description); ?>">
                                            <?php echo htmlspecialchars($activity->action_description); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($activity->status === 'success'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>สำเร็จ
                                            </span>
                                        <?php elseif ($activity->status === 'failed'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i>ล้มเหลว
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i><?php echo $activity->status; ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($activity->ip_address ?? '-'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                                        <p>ไม่มีข้อมูลกิจกรรมในช่วงเวลาที่เลือก</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Activity Logs Report -->
    <?php if ($report_type === 'logs'): ?>
    <div id="logsReport" class="space-y-8">
        <!-- Logs Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-list-alt text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">ทั้งหมด</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($activity_logs_summary['total'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-user text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">จำนวนผู้ใช้</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($activity_logs_summary['unique_users'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-calendar-day text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">วันนี้</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($activity_logs_summary['today'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <i class="fas fa-calendar-week text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">สัปดาห์นี้</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($activity_logs_summary['this_week'] ?? 0); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Logs Table -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-800">
                        <i class="fas fa-list-alt text-blue-600 mr-2"></i>
                        Activity Logs
                    </h3>
                    <div class="flex space-x-3">
                        <select id="logsFilter" onchange="filterLogs()" 
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">ทุกประเภท</option>
                            <option value="login">เข้าสู่ระบบ</option>
                            <option value="logout">ออกจากระบบ</option>
                            <option value="create">สร้าง</option>
                            <option value="update">แก้ไข</option>
                            <option value="delete">ลบ</option>
                            <option value="view">ดู</option>
                            <option value="download">ดาวน์โหลด</option>
                            <option value="upload">อัปโหลด</option>
                        </select>
                        <input type="search" 
                               id="logsSearch" 
                               placeholder="ค้นหา logs..."
                               onkeyup="searchLogs()"
                               class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">เวลา</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ผู้ใช้</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ประเภท</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">โมดูล</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">รายละเอียด</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody" class="divide-y divide-gray-200">
                        <?php if (!empty($activity_logs)): ?>
                            <?php foreach (array_slice($activity_logs, 0, 100) as $log): ?>
                                <tr class="log-row hover:bg-gray-50" 
                                    data-type="<?php echo $log->activity_type; ?>"
                                    data-search="<?php echo strtolower($log->activity_description . ' ' . $log->username . ' ' . $log->full_name); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo date('d/m/Y', strtotime($log->created_at)); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo date('H:i:s', strtotime($log->created_at)); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-gray-600 text-xs"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($log->full_name ?: $log->username); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($log->username); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo getLogTypeColor($log->activity_type); ?>">
                                            <i class="<?php echo getLogTypeIcon($log->activity_type); ?> mr-1"></i>
                                            <?php echo getLogTypeName($log->activity_type); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                            <?php echo htmlspecialchars($log->module ?? 'System'); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate" title="<?php echo htmlspecialchars($log->activity_description); ?>">
                                            <?php echo htmlspecialchars($log->activity_description); ?>
                                        </div>
                                        <?php if ($log->record_id): ?>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Record ID: <?php echo htmlspecialchars($log->record_id); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($log->ip_address ?? '-'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-list-alt text-4xl text-gray-300 mb-4"></i>
                                        <p>ไม่มีข้อมูล Activity Logs ในช่วงเวลาที่เลือก</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Logs by Type Chart -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                    กิจกรรมตามประเภท
                </h3>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="logsByTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Storage Report -->
    <?php if ($report_type === 'storage'): ?>
    <div id="storageReport" class="space-y-8">
        <!-- Storage Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-database text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">ใช้งานแล้ว</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo isset($storage_usage['current_usage']) ? format_bytes($storage_usage['current_usage']) : '0 B'; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-hdd text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">ขีดจำกัด</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo isset($storage_usage['storage_limit']) ? format_bytes($storage_usage['storage_limit']) : '0 B'; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">พื้นที่ว่าง</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo isset($storage_usage['available_space']) ? format_bytes($storage_usage['available_space']) : '0 B'; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <i class="fas fa-percentage text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">การใช้งาน</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo isset($storage_usage['usage_percent']) ? number_format($storage_usage['usage_percent'], 1) : '0'; ?>%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Chart -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">
                    <i class="fas fa-chart-ring text-purple-600 mr-2"></i>
                    การใช้งาน Storage
                </h3>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="storageUsageChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Users Report -->
    <?php if ($report_type === 'users'): ?>
    <div id="usersReport" class="space-y-8">
        <!-- User Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">ผู้ใช้ทั้งหมด</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($user_stats['total_users'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">ผู้ใช้ที่ใช้งาน</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($user_stats['active_users'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-database text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">มีสิทธิ์ Storage</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($user_stats['with_storage_access'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">ใช้งานล่าสุด</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($user_stats['recently_active'] ?? 0); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Activities Table -->
        <?php if (!empty($user_activities)): ?>
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">
                    <i class="fas fa-user-chart text-blue-600 mr-2"></i>
                    กิจกรรมของผู้ใช้
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ผู้ใช้</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ตำแหน่ง</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">จำนวนกิจกรรม</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ใช้งานล่าสุด</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($user_activities as $user): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($user->m_fname . ' ' . $user->m_lname); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($user->m_email); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($user->pname ?? '-'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo number_format($user->activity_count); ?> กิจกรรม
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('d/m/Y H:i', strtotime($user->last_activity)); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Folders Report -->
    <?php if ($report_type === 'folders'): ?>
    <div id="foldersReport" class="space-y-8">
        <!-- Folder Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-folder text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">โฟลเดอร์ทั้งหมด</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($folder_summary['total_folders'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-layer-group text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">ประเภทโฟลเดอร์</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($folder_summary['by_type'] ?? []); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-sitemap text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">ตำแหน่งที่มีโฟลเดอร์</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($folder_summary['by_position'] ?? []); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Folder Structure -->
        <?php if (!empty($folder_structure)): ?>
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">
                    <i class="fas fa-folder-tree text-green-600 mr-2"></i>
                    โครงสร้างโฟลเดอร์
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <?php foreach ($folder_structure as $folder): ?>
                        <?php $level = isset($folder->folder_path) ? substr_count(trim($folder->folder_path, '/'), '/') : 0; ?>
                        <div class="flex items-center py-2 px-3 hover:bg-gray-50 rounded-lg"
                             style="margin-left: <?php echo $level * 20; ?>px;">
                            <i class="fas fa-folder text-blue-500 mr-3"></i>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-800 font-medium"><?php echo htmlspecialchars($folder->folder_name); ?></span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                                            <?php echo getFolderTypeName($folder->folder_type); ?>
                                        </span>
                                        <?php if (isset($folder->position_name) && $folder->position_name): ?>
                                            <span class="text-xs text-gray-500"><?php echo htmlspecialchars($folder->position_name); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (isset($folder->folder_description) && $folder->folder_description): ?>
                                    <p class="text-sm text-gray-500 mt-1"><?php echo htmlspecialchars($folder->folder_description); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Simple Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-8 flex items-center space-x-4 shadow-xl">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span class="text-gray-700 font-medium">กำลังโหลดข้อมูล...</span>
    </div>
</div>

<?php
// Helper Functions
function getActivityTypeIcon($type) {
    $icons = [
        'connect' => 'fas fa-link',
        'disconnect' => 'fas fa-unlink',
        'create_folder' => 'fas fa-folder-plus',
        'upload_file' => 'fas fa-upload',
        'download_file' => 'fas fa-download',
        'share_file' => 'fas fa-share-alt',
        'delete_item' => 'fas fa-trash',
        'dashboard_view' => 'fas fa-tachometer-alt',
        'system_update' => 'fas fa-cogs'
    ];
    
    return $icons[$type] ?? 'fas fa-info-circle';
}

function getActivityTypeColor($type) {
    $colors = [
        'connect' => 'bg-green-100 text-green-800',
        'disconnect' => 'bg-red-100 text-red-800',
        'create_folder' => 'bg-blue-100 text-blue-800',
        'upload_file' => 'bg-purple-100 text-purple-800',
        'download_file' => 'bg-indigo-100 text-indigo-800',
        'share_file' => 'bg-yellow-100 text-yellow-800',
        'delete_item' => 'bg-red-100 text-red-800',
        'dashboard_view' => 'bg-gray-100 text-gray-800',
        'system_update' => 'bg-blue-100 text-blue-800'
    ];
    
    return $colors[$type] ?? 'bg-gray-100 text-gray-800';
}

function getActivityTypeName($type) {
    $names = [
        'connect' => 'เชื่อมต่อ',
        'disconnect' => 'ตัดการเชื่อมต่อ',
        'create_folder' => 'สร้างโฟลเดอร์',
        'upload_file' => 'อัปโหลดไฟล์',
        'download_file' => 'ดาวน์โหลดไฟล์',
        'share_file' => 'แชร์ไฟล์',
        'delete_item' => 'ลบรายการ',
        'dashboard_view' => 'เข้าดู Dashboard',
        'system_update' => 'อัปเดตระบบ'
    ];
    
    return $names[$type] ?? ucfirst(str_replace('_', ' ', $type));
}

function getLogTypeIcon($type) {
    $icons = [
        'login' => 'fas fa-sign-in-alt',
        'logout' => 'fas fa-sign-out-alt',
        'create' => 'fas fa-plus',
        'update' => 'fas fa-edit',
        'delete' => 'fas fa-trash',
        'view' => 'fas fa-eye',
        'download' => 'fas fa-download',
        'upload' => 'fas fa-upload',
        'access' => 'fas fa-key',
        'error' => 'fas fa-exclamation-triangle'
    ];
    
    return $icons[$type] ?? 'fas fa-info-circle';
}

function getLogTypeColor($type) {
    $colors = [
        'login' => 'bg-green-100 text-green-800',
        'logout' => 'bg-orange-100 text-orange-800',
        'create' => 'bg-blue-100 text-blue-800',
        'update' => 'bg-yellow-100 text-yellow-800',
        'delete' => 'bg-red-100 text-red-800',
        'view' => 'bg-gray-100 text-gray-800',
        'download' => 'bg-indigo-100 text-indigo-800',
        'upload' => 'bg-purple-100 text-purple-800',
        'access' => 'bg-cyan-100 text-cyan-800',
        'error' => 'bg-red-100 text-red-800'
    ];
    
    return $colors[$type] ?? 'bg-gray-100 text-gray-800';
}

function getLogTypeName($type) {
    $names = [
        'login' => 'เข้าสู่ระบบ',
        'logout' => 'ออกจากระบบ',
        'create' => 'สร้าง',
        'update' => 'แก้ไข',
        'delete' => 'ลบ',
        'view' => 'ดู',
        'download' => 'ดาวน์โหลด',
        'upload' => 'อัปโหลด',
        'access' => 'เข้าถึง',
        'error' => 'ข้อผิดพลาด'
    ];
    
    return $names[$type] ?? ucfirst($type);
}

function getFolderTypeName($type) {
    $names = [
        'system' => 'ระบบ',
        'department' => 'แผนก',
        'shared' => 'ส่วนกลาง',
        'user' => 'ผู้ใช้',
        'admin' => 'ผู้ดูแล'
    ];
    
    return $names[$type] ?? ucfirst($type);
}

function format_bytes($bytes, $precision = 2) {
    $bytes = max(0, (int)$bytes);
    
    if ($bytes === 0) {
        return '0 B';
    }
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $pow = floor(log($bytes, 1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<style>
/* Simple Tab Styles */
.report-tab {
    @apply px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors cursor-pointer;
}

.report-tab.active {
    @apply text-blue-600 bg-blue-50 border border-blue-200;
}

/* Simple scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Global Variables
let currentReportType = '<?php echo $report_type; ?>';

// Tab Switching
function switchReportType(tabName) {
    document.querySelectorAll('.report-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    document.getElementById('tab-' + tabName).classList.add('active');
    
    const url = new URL(window.location);
    url.searchParams.set('type', tabName);
    window.history.pushState({}, '', url);
    
    currentReportType = tabName;
    
    showLoading();
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

// Date Range Functions
function setQuickDateRange(range) {
    const today = new Date();
    let startDate, endDate;
    
    switch(range) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = yesterday.toISOString().split('T')[0];
            break;
        case 'week':
            const weekAgo = new Date(today);
            weekAgo.setDate(weekAgo.getDate() - 7);
            startDate = weekAgo.toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'month':
            const monthAgo = new Date(today);
            monthAgo.setDate(monthAgo.getDate() - 30);
            startDate = monthAgo.toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'quarter':
            const quarterAgo = new Date(today);
            quarterAgo.setDate(quarterAgo.getDate() - 90);
            startDate = quarterAgo.toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
    }
    
    if (startDate && endDate) {
        document.getElementById('startDate').value = startDate;
        document.getElementById('endDate').value = endDate;
    }
}

function applyDateFilter() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate || !endDate) {
        alert('กรุณาเลือกวันที่เริ่มต้นและสิ้นสุด');
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        alert('วันที่เริ่มต้นต้องไม่เกินวันที่สิ้นสุด');
        return;
    }
    
    showLoading();
    
    const url = new URL(window.location);
    url.searchParams.set('start_date', startDate);
    url.searchParams.set('end_date', endDate);
    window.history.pushState({}, '', url);
    
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

function resetDateFilter() {
    const today = new Date();
    const thirtyDaysAgo = new Date(today);
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    
    document.getElementById('startDate').value = thirtyDaysAgo.toISOString().split('T')[0];
    document.getElementById('endDate').value = today.toISOString().split('T')[0];
    document.getElementById('quickDateRange').value = '';
}

// Filter Functions
function filterActivities() {
    const filter = document.getElementById('activityFilter').value;
    const rows = document.querySelectorAll('.activity-row');
    
    rows.forEach(row => {
        if (filter === '' || row.dataset.status === filter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function searchActivities() {
    const searchTerm = document.getElementById('activitySearch').value.toLowerCase();
    const rows = document.querySelectorAll('.activity-row');
    
    rows.forEach(row => {
        const searchData = row.dataset.search;
        if (searchData.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function filterLogs() {
    const filter = document.getElementById('logsFilter').value;
    const rows = document.querySelectorAll('.log-row');
    
    rows.forEach(row => {
        if (filter === '' || row.dataset.type === filter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function searchLogs() {
    const searchTerm = document.getElementById('logsSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.log-row');
    
    rows.forEach(row => {
        const searchData = row.dataset.search;
        if (searchData.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Report Functions
function refreshReport() {
    showLoading();
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

function exportReport() {
    const reportType = currentReportType;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    const exportUrl = `<?php echo site_url('google_drive_system/export_report'); ?>?type=${reportType}&start_date=${startDate}&end_date=${endDate}&format=excel`;
    
    window.open(exportUrl, '_blank');
}

// Utility Functions
function showLoading() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.classList.remove('hidden');
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.classList.add('hidden');
}

// Chart Initialization
function initializeCharts() {
    // Activity Types Chart
    const activityTypesCtx = document.getElementById('activityTypesChart');
    if (activityTypesCtx) {
        const activityData = <?php echo json_encode($activities_summary['by_type'] ?? []); ?>;
        const labels = Object.keys(activityData);
        const values = Object.values(activityData);
        
        new Chart(activityTypesCtx, {
            type: 'doughnut',
            data: {
                labels: labels.map(label => getActivityTypeName(label)),
                datasets: [{
                    data: values,
                    backgroundColor: [
                        '#3B82F6', '#10B981', '#8B5CF6', '#F59E0B', 
                        '#EF4444', '#06B6D4', '#84CC16', '#F97316'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    // Daily Activities Chart
    const dailyActivitiesCtx = document.getElementById('dailyActivitiesChart');
    if (dailyActivitiesCtx) {
        const dailyData = <?php echo json_encode($activities_summary['by_day'] ?? []); ?>;
        const labels = Object.keys(dailyData).sort();
        const values = labels.map(date => dailyData[date] || 0);
        
        new Chart(dailyActivitiesCtx, {
            type: 'line',
            data: {
                labels: labels.map(date => {
                    const d = new Date(date);
                    return d.toLocaleDateString('th-TH', { day: 'numeric', month: 'short' });
                }),
                datasets: [{
                    label: 'กิจกรรมรายวัน',
                    data: values,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
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
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Storage Usage Chart
    const storageUsageCtx = document.getElementById('storageUsageChart');
    if (storageUsageCtx) {
        const storageData = <?php echo json_encode($storage_usage ?? []); ?>;
        
        if (storageData && storageData.current_usage !== undefined) {
            const used = storageData.current_usage || 0;
            const available = Math.max(0, (storageData.storage_limit || 0) - used);
            
            new Chart(storageUsageCtx, {
                type: 'pie',
                data: {
                    labels: ['ใช้งานแล้ว', 'พื้นที่ว่าง'],
                    datasets: [{
                        data: [used, available],
                        backgroundColor: ['#EF4444', '#10B981'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed;
                                    const formatted = formatBytes(value);
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${context.label}: ${formatted} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }
    
    // Logs by Type Chart
    const logsByTypeCtx = document.getElementById('logsByTypeChart');
    if (logsByTypeCtx) {
        const logsData = <?php echo json_encode($activity_logs_summary['by_type'] ?? []); ?>;
        const labels = Object.keys(logsData);
        const values = Object.values(logsData);
        
        new Chart(logsByTypeCtx, {
            type: 'bar',
            data: {
                labels: labels.map(label => getLogTypeName(label)),
                datasets: [{
                    label: 'จำนวนกิจกรรม',
                    data: values,
                    backgroundColor: '#3B82F6',
                    borderColor: '#1D4ED8',
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
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Helper Functions
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 B';
    
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

function getActivityTypeName(type) {
    const names = {
        'connect': 'เชื่อมต่อ',
        'disconnect': 'ตัดการเชื่อมต่อ',
        'create_folder': 'สร้างโฟลเดอร์',
        'upload_file': 'อัปโหลดไฟล์',
        'download_file': 'ดาวน์โหลดไฟล์',
        'share_file': 'แชร์ไฟล์',
        'delete_item': 'ลบรายการ',
        'dashboard_view': 'เข้าดู Dashboard',
        'system_update': 'อัปเดตระบบ'
    };
    
    return names[type] || type.replace('_', ' ');
}

function getLogTypeName(type) {
    const names = {
        'login': 'เข้าสู่ระบบ',
        'logout': 'ออกจากระบบ',
        'create': 'สร้าง',
        'update': 'แก้ไข',
        'delete': 'ลบ',
        'view': 'ดู',
        'download': 'ดาวน์โหลด',
        'upload': 'อัปโหลด',
        'access': 'เข้าถึง',
        'error': 'ข้อผิดพลาด'
    };
    
    return names[type] || type;
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeCharts, 500);
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            refreshReport();
        }
        
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            exportReport();
        }
        
        if (e.key === 'Escape') {
            hideLoading();
        }
    });
});
</script>