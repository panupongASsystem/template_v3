<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ita_year_import extends CI_Controller
{
    private $api_base_url = 'https://assystem.co.th/'; // URL ของ API ต้นทาง
    private $source_base_url;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ita_year_model');
        $this->load->model('ita_year_import_model');

        if ($this->session->userdata('m_system') != 'system_admin') {
            redirect('User/logout', 'refresh');
        }
		
		// โหลด URL helper
    	$this->load->helper('url');
    
    	// ใช้ base_url ของเว็บปลายทาง (เว็บปัจจุบัน)
    	$this->source_base_url = base_url();
    }

    /**
     * หน้าแสดง UI สำหรับเลือกนำเข้าข้อมูล
     */
    public function index()
    {
        // ดึงรายการปีที่มีจาก API
        $years = $this->fetch_available_years();
        $data['years'] = $years;
		$data['existing_years'] = array_column(
            $this->ita_year_model->list_all(), 
            'ita_year_year'
        );
		
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('ita_year/ita_year_import', $data);
		$this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ดึงรายการปีที่มีจาก API
     */
    private function fetch_available_years()
    {
        $api_url = $this->api_base_url . 'ita_year_api/get_all';
        $response = file_get_contents($api_url);
        
        if ($response) {
            $data = json_decode($response, true);
            if ($data['status'] == 'success') {
                return $data['data'];
            }
        }
        return array();
    }

    /**
     * ===============================================
     * API METHODS สำหรับ Import Control
     * ===============================================
     */

    /**
     * API: นำเข้าข้อมูลทั้งหมด (สำหรับ Import Control เรียกใช้)
     * URL: /ita_year_import/api_import_all
     */
    public function api_import_all()
    {
        // ตั้งค่า Header เป็น JSON
        header('Content-Type: application/json');
        
        $api_url = $this->api_base_url . 'ita_year_api/get_all_full_data';
        
        // ใช้ cURL แทน file_get_contents สำหรับความเสถียร
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่สามารถเชื่อมต่อ API ได้: ' . $error
            ]);
            return;
        }
        
        if (!$response) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่สามารถเชื่อมต่อ API ได้'
            ]);
            return;
        }
        
        $data = json_decode($response, true);
        
        if ($data['status'] != 'success') {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $data['message']
            ]);
            return;
        }
        
        // เริ่ม Transaction
        $this->db->trans_start();
        
        // ลบข้อมูลทั้งหมดใน 3 ตารางก่อน
        $this->db->truncate('tbl_ita_year_link');
        $this->db->truncate('tbl_ita_year_topic');
        $this->db->truncate('tbl_ita_year');
        
        // เริ่มนำเข้าข้อมูล
        $result = $this->process_import_data($data['data']);
        
        // สิ้นสุด Transaction
        $this->db->trans_complete();
        
        // ส่ง Response กลับ
        if ($this->db->trans_status() === FALSE || !$result['success']) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการนำเข้าข้อมูล: ' . ($result['message'] ?? 'Transaction failed')
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => sprintf(
                    'Import สำเร็จ %d ปี, %d หัวข้อ, %d ลิงก์',
                    $result['summary']['years'],
                    $result['summary']['topics'],
                    $result['summary']['links']
                ),
                'data' => $result['summary']
            ]);
        }
    }

    /**
     * API: นำเข้าข้อมูลเฉพาะปี (สำหรับ Import Control เรียกใช้)
     * URL: /ita_year_import/api_import_by_year?year=2567
     */
    public function api_import_by_year()
    {
        // ตั้งค่า Header เป็น JSON
        header('Content-Type: application/json');
        
        $year = $this->input->get('year');
        
        if (!$year) {
            echo json_encode([
                'success' => false,
                'message' => 'กรุณาระบุปี (year parameter)'
            ]);
            return;
        }

        $api_url = $this->api_base_url . 'ita_year_api/get_full_data_by_year/' . $year;
        
        // ใช้ cURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่สามารถเชื่อมต่อ API ได้: ' . $error
            ]);
            return;
        }
        
        if (!$response) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่สามารถเชื่อมต่อ API ได้'
            ]);
            return;
        }

        $data = json_decode($response, true);
        
        if ($data['status'] != 'success') {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $data['message']
            ]);
            return;
        }

        // แปลงข้อมูลให้อยู่ในรูปแบบ array
        $import_data = array($data['data']);
        
        // เริ่ม Transaction
        $this->db->trans_start();
        
        // เริ่มนำเข้าข้อมูล
        $result = $this->process_import_data($import_data);
        
        // สิ้นสุด Transaction
        $this->db->trans_complete();
        
        // ส่ง Response กลับ
        if ($this->db->trans_status() === FALSE || !$result['success']) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการนำเข้าข้อมูล: ' . ($result['message'] ?? 'Transaction failed')
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => sprintf(
                    'Import ปี %s สำเร็จ - %d หัวข้อ, %d ลิงก์',
                    $year,
                    $result['summary']['topics'],
                    $result['summary']['links']
                ),
                'data' => $result['summary']
            ]);
        }
    }

    /**
     * ===============================================
     * UI METHODS (เดิม - สำหรับใช้ผ่านหน้าเว็บ)
     * ===============================================
     */

    /**
     * UI: นำเข้าข้อมูลทั้งหมด
     */
    public function import_all()
    {
        $api_url = $this->api_base_url . 'ita_year_api/get_all_full_data';
        $response = file_get_contents($api_url);
        
        if (!$response) {
            $this->session->set_flashdata('error', 'ไม่สามารถเชื่อมต่อ API ได้');
            redirect('ita_year_import');
            return;
        }
        
        $data = json_decode($response, true);
        
        if ($data['status'] != 'success') {
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $data['message']);
            redirect('ita_year_import');
            return;
        }
        
        // เริ่ม Transaction
        $this->db->trans_start();
        
        // ลบข้อมูลทั้งหมดใน 3 ตารางก่อน
        $this->db->truncate('tbl_ita_year_link');
        $this->db->truncate('tbl_ita_year_topic');
        $this->db->truncate('tbl_ita_year');
        
        // เริ่มนำเข้าข้อมูล
        $result = $this->process_import_data($data['data']);
        
        // สิ้นสุด Transaction
        $this->db->trans_complete();
        
        // ตรวจสอบผลลัพธ์
        if ($this->db->trans_status() === FALSE || !$result['success']) {
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการนำเข้าข้อมูล: ' . ($result['message'] ?? 'Transaction failed'));
        } else {
            $this->session->set_flashdata('save_success', TRUE);
            $this->session->set_flashdata('import_summary', $result['summary']);
        }
        
        redirect('ita_year_import');
    }

    /**
     * UI: นำเข้าข้อมูลเฉพาะปี
     */
    public function import_by_year($year = null)
    {
        if (!$year) {
            $year = $this->input->post('year');
        }

        if (!$year) {
            $this->session->set_flashdata('error', 'กรุณาเลือกปี');
            redirect('ita_year_import');
            return;
        }

        $api_url = $this->api_base_url . 'ita_year_api/get_full_data_by_year/' . $year;
        $response = file_get_contents($api_url);
        
        if (!$response) {
            $this->session->set_flashdata('error', 'ไม่สามารถเชื่อมต่อ API ได้');
            redirect('ita_year_import');
            return;
        }

        $data = json_decode($response, true);
        
        if ($data['status'] != 'success') {
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $data['message']);
            redirect('ita_year_import');
            return;
        }

        // แปลงข้อมูลให้อยู่ในรูปแบบ array
        $import_data = array($data['data']);
        
        // เริ่มนำเข้าข้อมูล
        $result = $this->process_import_data($import_data);
        
        if ($result['success']) {
            $this->session->set_flashdata('save_success', TRUE);
            $this->session->set_flashdata('import_summary', $result['summary']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }
        
        redirect('ita_year_import');
    }

    /**
     * ===============================================
     * SHARED METHODS
     * ===============================================
     */

    /**
     * ตรวจสอบว่า array เป็น associative หรือไม่
     */
    private function is_assoc_array($arr) {
        if (!is_array($arr)) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * ประมวลผลและบันทึกข้อมูล
     */
    private function process_import_data($data_array)
    {
        if (!is_array($data_array)) {
            return ['success' => false, 'message' => 'รูปแบบข้อมูลไม่ถูกต้อง (root)'];
        }

        $this->db->trans_start();
        $year_count = $topic_count = $link_count = 0;

        try {
            foreach ($data_array as $row_idx => $year_data) {
                if (is_object($year_data)) $year_data = (array) $year_data;

                $year_info = $year_data['year_info'] ?? null;
                $topics    = $year_data['topics']    ?? [];

                if (is_object($year_info)) $year_info = (array) $year_info;
                if (!is_array($year_info)) {
                    log_message('error', "import: year_info[$row_idx] ไม่ใช่ array/object"); 
                    continue;
                }

                $year_val = $year_info['ita_year_year'] ?? null;
                if ($year_val === null) {
                    log_message('error', "import: ไม่มี ita_year_year ที่แถว $row_idx");
                    continue;
                }

                $existing_year = $this->ita_year_import_model->check_year_exists($year_val);
                if ($existing_year) {
                    $this->ita_year_import_model->delete_year_data($existing_year->ita_year_id);
                }

                $new_year_id = $this->ita_year_import_model->insert_year($year_info);
                $year_count++;

                if (!is_array($topics)) $topics = [$topics];

                foreach ($topics as $t_idx => $topic) {
                    if (is_object($topic)) $topic = (array) $topic;
                    if (!is_array($topic)) {
                        log_message('error', "import: topic[$t_idx] ปี $year_val เป็น ".gettype($topic));
                        continue;
                    }

                    $topic_data = [
                        'ita_year_topic_ref_id' => $new_year_id,
                        'ita_year_topic_name'   => $topic['topic_name'] ?? '',
                        'ita_year_topic_msg'    => $topic['topic_msg']  ?? '',
                        'ita_year_topic_by'     => 'import_system'
                    ];
                    $new_topic_id = $this->ita_year_import_model->insert_topic($topic_data);
                    $topic_count++;

                    // --------- FIX: normalize links ---------
                    $links = $topic['links'] ?? [];
                    if ($links === null || $links === '' ) $links = [];
                    if (is_object($links)) $links = (array) $links;

                    if (is_array($links)) {
                        // ถ้าเป็น associative (ก้อนเดียว) → ห่อเป็นลิสต์ 1 ชิ้น
                        if ($this->is_assoc_array($links)) {
                            $links_array = [$links];
                        } else {
                            // เป็นลิสต์อยู่แล้ว ([]) 
                            $links_array = $links;
                        }
                    } else {
                        // เป็นสเกลาร์ → ข้าม
                        $links_array = [];
                    }

                    foreach ($links_array as $l_idx => $link) {
                        if (is_object($link)) $link = (array) $link;
                        if (!is_array($link)) {
                            log_message('error', "import: link[$l_idx] ของ topic[$t_idx] ปี $year_val เป็น ".gettype($link));
                            continue;
                        }

                        $link_data = [
                            'ita_year_link_ref_id' => $new_topic_id,
                            'ita_year_link_name'   => $link['ita_year_link_name'] ?? '',
                            'ita_year_link_by'     => 'import_system'
                        ];

                        for ($i = 1; $i <= 5; $i++) {
                            $link_key  = 'ita_year_link_link'  . $i;
                            $title_key = 'ita_year_link_title' . $i;

                            $link_value  = $link[$link_key]  ?? null;
                            $title_value = $link[$title_key] ?? null;

                            if (!empty($link_value)) {
                                if (!preg_match('#^https?://#', $link_value)) {
                                    $link_value = ltrim($link_value, '/');
                                    $link_data[$link_key] = $this->source_base_url . $link_value;
                                } else {
                                    $link_data[$link_key] = $link_value;
                                }
                            } else {
                                $link_data[$link_key] = null;
                            }
                            $link_data[$title_key] = $title_value;
                        }

                        $this->ita_year_import_model->insert_link($link_data);
                        $link_count++;
                    }
                    // --------- END FIX ----------
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'];
            }

            return [
                'success' => true,
                'summary' => ['years'=>$year_count, 'topics'=>$topic_count, 'links'=>$link_count]
            ];

        } catch (Exception $e) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: '.$e->getMessage()];
        }
    }
}
