<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chat_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Chat_model');
        $this->load->helper(['form', 'url']);
        $this->load->library(['form_validation', 'session']);
		
		        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '0']); // 1=ทั้งหมด
    }

    /**
     * หน้าจัดการ config
     */
    public function index()
    {
        $data['configs'] = $this->Chat_model->get_all_configs();
        $data['stats'] = $this->Chat_model->get_usage_stats(null, date('Y-m-d', strtotime('-7 days')));

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/chat_config', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * อัพเดท config
     */
    public function update_config()
    {
        if ($this->input->method() !== 'post') {
            redirect('Chat_backend');
        }

        $config_name = $this->input->post('config_name');
        $config_value = $this->input->post('config_value');
        $config_type = $this->input->post('config_type');
        $description = $this->input->post('description');

        // Validation
        $this->form_validation->set_rules('config_name', 'Config Name', 'required|trim');
        $this->form_validation->set_rules('config_value', 'Config Value', 'required');
        $this->form_validation->set_rules('config_type', 'Config Type', 'required|in_list[text,json,number]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('Chat_backend');
        }

        // Validate JSON if type is json
        if ($config_type === 'json') {
            $decoded = json_decode($config_value, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->session->set_flashdata('error', 'Invalid JSON format');
                redirect('Chat_backend');
            }
        }

        // Validate number if type is number
        if ($config_type === 'number' && !is_numeric($config_value)) {
            $this->session->set_flashdata('error', 'Value must be a number');
            redirect('Chat_backend');
        }

        if ($this->Chat_model->set_config($config_name, $config_value, $config_type, $description)) {
            $this->session->set_flashdata('success', 'Config updated successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to update config');
        }

        redirect('Chat_backend');
    }

    /**
     * เปิด/ปิดการใช้งาน config
     */
    public function toggle_config($config_name, $status)
    {
        if (!in_array($status, ['0', '1'])) {
            show_404();
        }

        if ($this->Chat_model->toggle_config($config_name, (bool)$status)) {
            $this->session->set_flashdata('success', 'Config status updated');
        } else {
            $this->session->set_flashdata('error', 'Failed to update config status');
        }

        redirect('Chat_backend');
    }

    /**
     * ดูสถิติการใช้งาน
     */
    public function stats()
    {
        $date_from = $this->input->get('date_from') ?: date('Y-m-d', strtotime('-30 days'));
        $date_to = $this->input->get('date_to') ?: date('Y-m-d');

        $data['stats'] = $this->Chat_model->get_usage_stats(null, $date_from, $date_to);
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;

        // ตรวจสอบว่าตาราง chat_logs มีข้อมูลหรือไม่
        $table_exists = $this->db->table_exists('chat_logs');
        if (!$table_exists) {
            $data['error'] = 'Chat logs table does not exist. Please run the database migration first.';
            $data['total_conversations'] = 0;
            $data['unique_users'] = 0;
            $data['avg_response_time'] = 0;
            $data['user_types'] = [];
            $data['hourly_usage'] = array_fill(0, 24, 0);
        } else {
            // รายละเอียดเพิ่มเติม - ป้องกัน error ด้วย try-catch
            try {
                // Total conversations
                $query = $this->db->select('COUNT(*) as total_conversations')
                    ->where('created_at >=', $date_from . ' 00:00:00')
                    ->where('created_at <=', $date_to . ' 23:59:59')
                    ->get('chat_logs');
                $result = $query->row();
                $data['total_conversations'] = $result ? $result->total_conversations : 0;

                // Unique users
                $query = $this->db->select('COUNT(DISTINCT user_id) as unique_users')
                    ->where('created_at >=', $date_from . ' 00:00:00')
                    ->where('created_at <=', $date_to . ' 23:59:59')
                    ->get('chat_logs');
                $result = $query->row();
                $data['unique_users'] = $result ? $result->unique_users : 0;

                // Average response time
                $query = $this->db->select('AVG(response_time) as avg_response_time')
                    ->where('created_at >=', $date_from . ' 00:00:00')
                    ->where('created_at <=', $date_to . ' 23:59:59')
                    ->where('response_time IS NOT NULL')
                    ->where('response_time > 0')
                    ->get('chat_logs');
                $result = $query->row();
                $data['avg_response_time'] = $result && $result->avg_response_time ?
                    round($result->avg_response_time, 2) : 0;

                // User types distribution
                $query = $this->db->select('user_type, COUNT(*) as count')
                    ->where('created_at >=', $date_from . ' 00:00:00')
                    ->where('created_at <=', $date_to . ' 23:59:59')
                    ->group_by('user_type')
                    ->get('chat_logs');
                $data['user_types'] = $query->result_array();

                // ถ้าไม่มีข้อมูล user_types ให้ใส่ข้อมูลจำลอง
                if (empty($data['user_types'])) {
                    $data['user_types'] = [
                        ['user_type' => 'guest', 'count' => 0],
                        ['user_type' => 'member', 'count' => 0]
                    ];
                }

                // Hourly usage pattern
                $query = $this->db->select('HOUR(created_at) as hour, COUNT(*) as count')
                    ->where('created_at >=', $date_from . ' 00:00:00')
                    ->where('created_at <=', $date_to . ' 23:59:59')
                    ->group_by('HOUR(created_at)')
                    ->get('chat_logs');
                $hourly_data = $query->result_array();

                // สร้าง array 24 ชั่วโมง (0-23)
                $data['hourly_usage'] = array_fill(0, 24, 0);
                foreach ($hourly_data as $row) {
                    $hour = intval($row['hour']);
                    if ($hour >= 0 && $hour <= 23) {
                        $data['hourly_usage'][$hour] = intval($row['count']);
                    }
                }
            } catch (Exception $e) {
                log_message('error', '[Admin Chat Stats] Database error: ' . $e->getMessage());
                $data['error'] = 'Database error: ' . $e->getMessage();
                $data['total_conversations'] = 0;
                $data['unique_users'] = 0;
                $data['avg_response_time'] = 0;
                $data['user_types'] = [];
                $data['hourly_usage'] = array_fill(0, 24, 0);
            }
        }

        // Debug information (เอาออกใน production)
        if (ENVIRONMENT === 'development') {
            $data['debug'] = [
                'date_from' => $date_from,
                'date_to' => $date_to,
                'stats_count' => count($data['stats']),
                'user_types_count' => count($data['user_types']),
                'hourly_usage_sum' => array_sum($data['hourly_usage']),
                'table_exists' => $table_exists
            ];
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/chat_stats', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ล้างข้อมูลเก่า
     */
    public function cleanup()
    {
        $days = (int)$this->input->post('days') ?: 30;

        $result = $this->Chat_model->cleanup_old_data($days);

        $this->session->set_flashdata(
            'success',
            "ล้างข้อมูลเรียบร้อย - Rate limits: {$result['rate_limits_deleted']}, Logs: {$result['logs_deleted']}"
        );

        redirect('Chat_backend');
    }

    /**
     * ทดสอบ API
     */
    public function test_api()
    {
        // ทดสอบการเชื่อมต่อ API
        $this->load->library('curl');

        $test_result = [
            'timestamp' => date('Y-m-d H:i:s'),
            'database_connection' => $this->db->initialize() ? 'OK' : 'Failed',
            'configs_count' => count($this->Chat_model->get_all_configs())
        ];

        header('Content-Type: application/json');
        echo json_encode($test_result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Export ข้อมูล
     */
    public function export_logs()
    {
        $date_from = $this->input->get('date_from') ?: date('Y-m-d', strtotime('-7 days'));
        $date_to = $this->input->get('date_to') ?: date('Y-m-d');

        $this->db->select('*');
        $this->db->where('created_at >=', $date_from . ' 00:00:00');
        $this->db->where('created_at <=', $date_to . ' 23:59:59');
        $this->db->order_by('created_at', 'DESC');
        $logs = $this->db->get('chat_logs')->result_array();

        // ส่งเป็น CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="chat_logs_' . $date_from . '_to_' . $date_to . '.csv"');

        $output = fopen('php://output', 'w');

        // Header
        if (!empty($logs)) {
            fputcsv($output, array_keys($logs[0]));

            // Data
            foreach ($logs as $log) {
                fputcsv($output, $log);
            }
        }

        fclose($output);
    }
}

/* End of file Chat_backend.php */
/* Location: ./application/controllers/Chat_backend.php */