<div class="menu-item bg-gray-50 p-3 rounded-lg border relative">
    <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-red-500" onclick="removeMenuItem(this)">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อเมนู *</label>
            <input type="text" name="menu_names[]" value="<?= $menu->name ?? '' ?>" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">รหัสเมนู *</label>
            <input type="text" name="menu_codes[]" value="<?= $menu->code ?? '' ?>" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
            <input type="text" name="menu_urls[]" value="<?= $menu->url ?? '' ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ไอคอน</label>
            <input type="text" name="menu_icons[]" value="<?= $menu->icon ?? '' ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">เมนูหลัก</label>
            <select name="menu_parents[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">-- ไม่มี --</option>
                <?php foreach ($parent_menus as $parent): ?>
                <option value="<?= $parent->code ?>" <?= ($menu->parent_id == $parent->id) ? 'selected' : '' ?>>
                    <?= $parent->name ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ลำดับ</label>
            <input type="number" name="menu_orders[]" min="1" value="<?= $menu->display_order ?? 1 ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <div class="col-span-2">
            <label class="inline-flex items-center">
                <input type="checkbox" name="menu_status[]" value="1" <?= ($menu->status ?? 1) ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-600">เปิดใช้งาน</span>
            </label>
        </div>
    </div>
</div>