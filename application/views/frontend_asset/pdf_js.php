<!-- PDF.js Library - ต้องโหลดก่อน Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
// Configure PDF.js worker
if (typeof pdfjsLib !== 'undefined') {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    console.log('PDF.js loaded successfully');
} else {
    console.error('PDF.js failed to load');
}
</script>

<!-- Bootstrap และ libraries อื่นๆ -->
<script src="<?= base_url('asset/'); ?>boostrap/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<!-- เหลือโค้ดเดิม... -->