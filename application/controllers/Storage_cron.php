<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Storage Cron Controller
 * สำหรับรัน cron job อัปเดตข้อมูลพื้นที่จัดเก็บ
 */
class Storage_cron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // ตรวจสอบว่าถูกเรียกจาก command line หรือไม่
        if (!$this->input->is_cli_request() && !$this->is_authorized_request()) {
            show_404();
        }
        
        $this->load->model('Storage_updater_model');
    }

    /**
     * ตรวจสอบว่าเป็นการเรียกที่ได้รับอนุญาต
     */
    private function is_authorized_request()
    {
        // ตรวจสอบ secret key สำหรับเรียกผ่าน web
        $secret = $this->input->get('secret') ?: $this->input->post('secret');
        $expected_secret = 'your_secret_key_here'; // เปลี่ยนเป็น secret key ของคุณ
        
        return $secret === $expected_secret;
    }

    /**
     * 🤖 ฟังก์ชันหลักสำหรับ cron job
     * เรียกทุก 30 นาที: */30 * * * * /usr/bin/php /path/to/your/project/index.php Storage_cron update_storage
     */
    public function update_storage()
    {
        echo "=== Storage Update Started at " . date('Y-m-d H:i:s') . " ===\n";
        
        $result = $this->Storage_updater_model->update_storage_usage();
        
        if ($result['success']) {
            echo "✅ Storage updated successfully!\n";
            echo "Total Space: " . $result['total_space'] . " GB\n";
            echo "Used Space: " . $result['used_space'] . " GB\n";
            echo "Updated at: " . $result['updated_at'] . "\n";
        } else {
            echo "❌ Storage update failed!\n";
            echo "Error: " . $result['error'] . "\n";
        }
        
        echo "=== Storage Update Completed ===\n\n";
    }

    /**
     * 🔄 อัปเดตแบบ manual ผ่าน web interface
     */
    public function manual_update()
    {
        // ตรวจสอบสิทธิ์ admin
        if (!$this->session->userdata('m_id') || !$this->is_admin()) {
            echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์ในการดำเนินการ']);
            return;
        }
        
        $result = $this->Storage_updater_model->manual_update();
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * ตรวจสอบว่าเป็น admin หรือไม่
     */
    private function is_admin()
    {
        $user_system = $this->session->userdata('m_system');
        return in_array($user_system, ['system_admin', 'super_admin']);
    }

    /**
     * 📊 ดึงสถิติการใช้งานปัจจุบัน
     */
    public function get_statistics()
    {
        $stats = $this->Storage_updater_model->get_usage_statistics();
        
        header('Content-Type: application/json');
        echo json_encode($stats);
    }

    /**
     * 🔧 ทดสอบระบบ
     */
    public function test()
    {
        echo "=== Storage System Test ===\n";
        
        // ทดสอบการคำนวณพื้นที่
        echo "Testing storage calculation...\n";
        $result = $this->Storage_updater_model->update_storage_usage();
        
        if ($result['success']) {
            echo "✅ Test passed!\n";
            print_r($result);
        } else {
            echo "❌ Test failed!\n";
            echo "Error: " . $result['error'] . "\n";
        }
        
        echo "=== Test Completed ===\n";
    }
}