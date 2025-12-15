<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vulgar_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        $this->load->model('vulgar_model');
        $this->load->library('vulgar_check'); // เพิ่ม library
        $this->check_access_permission(['1', '113']); // 1=ทั้งหมด
        
        // ตรวจสอบและสร้างตาราง whitelist หากยังไม่มี
        if (!$this->vulgar_model->check_whitelist_table()) {
            $this->vulgar_model->create_whitelist_table();
            $this->vulgar_model->add_default_whitelist();
        }
    }
    
    public function index()
    {
        // ดึงข้อมูลคำหยาบ
        $data['query'] = $this->vulgar_model->list();
        
        // ดึงข้อมูล whitelist
        $data['whitelist'] = $this->vulgar_model->get_whitelist();
        
        // ดึงสถิติ
        $data['statistics'] = $this->vulgar_model->get_statistics();
        
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/vulgar', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    // ==================== Vulgar Word Functions (ใช้เดิม) ====================
    
    public function add()
    {
        $this->vulgar_model->add();
        redirect('vulgar_backend', 'refresh');
    }

    public function editing($vulgar_id)
    {
        $data['rsedit'] = $this->vulgar_model->read($vulgar_id);
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/vulgar_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($vulgar_id)
    {
        $this->vulgar_model->edit($vulgar_id);
        redirect('vulgar_backend', 'refresh');
    }

    public function del($vulgar_id)
    {
        $this->vulgar_model->del($vulgar_id);
        redirect('vulgar_backend', 'refresh');
    }

    // ==================== Whitelist Management Functions ====================

    /**
     * เพิ่มคำใน whitelist
     */
    public function add_whitelist()
    {
        $word = trim($this->input->post('whitelist_word'));
        $description = trim($this->input->post('whitelist_desc'));
        
        if (empty($word)) {
            $this->session->set_flashdata('error', 'กรุณาระบุคำที่ต้องการเพิ่ม');
        } else {
            $result = $this->vulgar_model->add_whitelist();
            // Flash message จะถูกตั้งค่าใน Model แล้ว
        }
        
        redirect('vulgar_backend', 'refresh');
    }

    /**
     * แสดงหน้าแก้ไข whitelist
     */
    public function edit_whitelist($whitelist_id)
    {
        $whitelist_data = $this->vulgar_model->read_whitelist($whitelist_id);
        
        if (!$whitelist_data) {
            $this->session->set_flashdata('error', 'ไม่พบข้อมูล Whitelist');
            redirect('vulgar_backend', 'refresh');
        }
        
        $data['whitelist_data'] = $whitelist_data;
        
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/whitelist_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * อัปเดต whitelist
     */
    public function update_whitelist($whitelist_id)
    {
        $word = trim($this->input->post('whitelist_word'));
        
        if (empty($word)) {
            $this->session->set_flashdata('error', 'กรุณาระบุคำที่ต้องการแก้ไข');
        } else {
            $result = $this->vulgar_model->edit_whitelist($whitelist_id);
            // Flash message จะถูกตั้งค่าใน Model แล้ว
        }
        
        redirect('vulgar_backend', 'refresh');
    }

    /**
     * ลบคำจาก whitelist
     */
    public function del_whitelist($whitelist_id)
    {
        $this->vulgar_model->del_whitelist($whitelist_id);
        redirect('vulgar_backend', 'refresh');
    }

    /**
     * ทดสอบระบบ Vulgar Check
     */
    public function test_vulgar()
    {
        if ($this->input->post('test_text')) {
            $test_text = $this->input->post('test_text');
            $result = $this->vulgar_check->debug_test($test_text);
            
            echo json_encode([
                'status' => 'success',
                'test_text' => $test_text,
                'result' => $result
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'กรุณาระบุข้อความที่ต้องการทดสอบ'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * แสดงสถิติการใช้งาน
     */
    public function statistics()
    {
        $data['statistics'] = $this->vulgar_model->get_statistics();
        
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/vulgar_statistics', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ค้นหา whitelist
     */
    public function search_whitelist()
    {
        $keyword = $this->input->post('keyword');
        
        if (!empty($keyword)) {
            $data['whitelist'] = $this->vulgar_model->search_whitelist($keyword);
            $data['search_keyword'] = $keyword;
        } else {
            $data['whitelist'] = $this->vulgar_model->get_whitelist();
            $data['search_keyword'] = '';
        }
        
        $data['query'] = $this->vulgar_model->list();
        $data['statistics'] = $this->vulgar_model->get_statistics();
        
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/vulgar', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * Import/Export Whitelist
     */
    public function export_whitelist()
    {
        $data = $this->vulgar_model->export_whitelist();
        
        // สร้างไฟล์ CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=whitelist_export_' . date('Y-m-d_H-i-s') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // เขียน BOM สำหรับ UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // เขียน header
        fputcsv($output, array('คำที่อนุญาต', 'คำอธิบาย', 'วันที่สร้าง'));
        
        // เขียนข้อมูล
        foreach ($data as $row) {
            fputcsv($output, array(
                $row['whitelist_word'],
                $row['whitelist_desc'],
                $row['created_at']
            ));
        }
        
        fclose($output);
    }

    /**
     * Reset whitelist เป็นค่าเริ่มต้น
     */
    public function reset_whitelist()
    {
        // ลบข้อมูลเดิม
        $this->db->truncate('tbl_vulgar_whitelist');
        
        // เพิ่มข้อมูลเริ่มต้นใหม่
        $count = $this->vulgar_model->add_default_whitelist();
        
        $this->session->set_flashdata('success', "รีเซ็ต Whitelist สำเร็จ เพิ่มข้อมูลเริ่มต้น {$count} คำ");
        redirect('vulgar_backend', 'refresh');
    }

    /**
     * ทดสอบข้อความหลายๆ ข้อความพร้อมกัน
     */
    public function bulk_test()
    {
        $test_texts = $this->input->post('test_texts'); // array ของข้อความ
        $results = array();
        
        if (!empty($test_texts) && is_array($test_texts)) {
            foreach ($test_texts as $text) {
                if (!empty(trim($text))) {
                    $result = $this->vulgar_check->debug_test(trim($text));
                    $results[] = array(
                        'text' => $text,
                        'result' => $result
                    );
                }
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'results' => $results
        ], JSON_UNESCAPED_UNICODE);
    }
}
?>