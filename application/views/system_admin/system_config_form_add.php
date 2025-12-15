<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7">
            <h4>เพิ่มข้อมูล System Config</h4>
            <form action="<?php echo site_url('system_config_backend/add'); ?>" method="post" class="form-horizontal">
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">Type</div>
                    <div class="col-sm-10">
                        <input type="text" name="type" id="type" class="form-control" value="<?= $type; ?>" placeholder="ระบุประเภท (เช่น address, link, key_token หรือประเภทอื่นๆ)">
                        <small class="text-muted">* สามารถระบุประเภทได้อิสระ ไม่จำกัดเฉพาะที่มีอยู่</small>
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">Keyword</div>
                    <div class="col-sm-10">
                        <input type="text" name="keyword" id="keyword" class="form-control" required placeholder="ระบุ Keyword (ตัวอย่าง: site_name, domain)">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">Value</div>
                    <div class="col-sm-10">
                        <input type="text" name="value" id="value" class="form-control" required placeholder="ระบุค่า Value">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-2 control-label">Description</div>
                    <div class="col-sm-10">
                        <input type="text" name="description" id="description" class="form-control" required placeholder="ระบุคำอธิบาย">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-sm-4 control-label"></div>
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <a class="btn btn-danger" href="<?= site_url('system_config_backend'); ?>" role="button">ยกเลิก</a>
                    </div>
                </div>
            </form>
            
            <!-- แสดงประเภทที่มีอยู่ในระบบ -->
            <div class="mt-5">
                <h5>ประเภท (Type) ที่มีในระบบ</h5>
                <div class="card">
                    <div class="card-body">
                        <?php if(isset($existing_types) && !empty($existing_types)): ?>
                            <div class="row">
                                <?php foreach($existing_types as $type_item): ?>
                                    <div class="col-md-4 mb-2">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary type-tag" data-type="<?= $type_item->type; ?>">
                                            <?= $type_item->type; ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">ยังไม่มีประเภทข้อมูลในระบบ</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // เพิ่ม event listener สำหรับปุ่มประเภท
    var typeTags = document.querySelectorAll('.type-tag');
    typeTags.forEach(function(tag) {
        tag.addEventListener('click', function() {
            document.getElementById('type').value = this.dataset.type;
        });
    });
});
</script>