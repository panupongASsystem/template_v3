<div class="ml-72 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">ประวัติการเข้าใช้งานระบบ</h2>
            <p class="text-gray-600">ข้อมูลการเข้าใช้งานระบบของผู้ใช้ทั้งหมด</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-lg shadow-md shadow">
        <!-- Search Form -->
        <div class="p-6 border-b">
            <form method="get" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- User Select -->
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ผู้ใช้งาน</label>
                        <select name="user_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- ทั้งหมด --</option>
                            <!-- <?php if (!empty($users_member)): ?>
                                <optgroup label="เจ้าหน้าที่">
                                    <?php foreach ($users_member as $user) : ?>
                                        <option value="<?= $user->m_id ?>" <?= ($this->input->get('user_id') == $user->m_id ? 'selected' : '') ?>>
                                            <?= $user->m_fname . ' ' . $user->m_lname ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?> -->
                            <?php if (!empty($users_public)): ?>
                                <optgroup label="ประชาชน">
                                    <?php foreach ($users_public as $user) : ?>
                                        <option value="<?= $user->mp_id ?>" <?= ($this->input->get('user_id') == $user->mp_id ? 'selected' : '') ?>>
                                            <?= $user->mp_fname . ' ' . $user->mp_lname ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">วันที่เริ่มต้น</label>
                        <input type="date" name="start_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="<?= $this->input->get('start_date') ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">วันที่สิ้นสุด</label>
                        <input type="date" name="end_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="<?= $this->input->get('end_date') ?>">
                    </div>

                    <!-- Search Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
                        <input type="text" name="search" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="ค้นหาด้วยชื่อ, IP..." value="<?= $this->input->get('search') ?>">
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-search mr-2"></i>ค้นหา
                        </button>
                        <a href="<?= site_url('System_tax/user_logs') ?>"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <i class="fas fa-redo mr-2"></i>รีเซ็ต
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto shadow">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ผู้ใช้งาน</th>
                        <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ประเภท</th> -->
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การกระทำ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เวลา</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Agent</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($logs as $index => $log) : ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= $start_row + $index ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= $log->user_name ?></div>
                            </td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($log->user_type == 'member_public'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        ประชาชน
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        เจ้าหน้าที่
                                    </span>
                                <?php endif; ?>
                            </td> -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php switch ($log->action) {
                                    case 'login':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">เข้าสู่ระบบ</span>';
                                        break;
                                    case 'logout':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">ออกจากระบบ</span>';
                                        break;
                                    case 'session_timeout':
                                        echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">หมดเวลาใช้งาน</span>';
                                        break;
                                } ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d/m/Y H:i:s', strtotime($log->action_time)) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $log->ip_address ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="<?= $log->user_agent ?>">
                                <?= $log->user_agent ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)) : ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                ไม่พบข้อมูล
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <!-- Pagination -->
        <div class="p-6 border-t">
            <div class="flex justify-between items-center">
                <div class="text-gray-600">
                    แสดง <?php echo $start_row; ?>-<?php echo $end_row; ?> จาก <?php echo $total_rows; ?> รายการ
                </div>
                <?php echo $pagination; ?>
            </div>
        </div>
        </div>

        
    </div>
</div>
