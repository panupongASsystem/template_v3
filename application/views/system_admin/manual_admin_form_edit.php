<div class="container mt-4">
    <h2>คู่มือการใช้งานเว็บไซต์สำหรับแอดมิน</h2>
    <form method="post" enctype="multipart/form-data" action="<?php echo site_url('manual_admin_backend/update_manual_admin/' . $manual->manual_admin_id); ?>">
        <div class="mb-3">
            <label class="form-label">Manual Name</label>
            <input type="text" name="manual_admin_name" class="form-control" value="<?php echo $manual->manual_admin_name; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload PDF</label>
            <?php if ($manual->manual_admin_pdf): ?>
                <p>Current File: <a href="<?php echo base_url('docs/file/' . $manual->manual_admin_pdf); ?>" target="_blank">View</a></p>
                <input type="hidden" name="old_pdf" value="<?php echo $manual->manual_admin_pdf; ?>">
            <?php endif; ?>
            <input type="file" name="manual_admin_pdf" class="form-control" accept="application/pdf">
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>