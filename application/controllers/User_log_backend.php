<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_log_backend extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        if ($this->session->userdata('m_system') != 'system_admin') {
            redirect('User/logout', 'refresh');
        }
        
       

        $this->load->model('member_model');
        $this->load->model('user_log_model');
        $this->load->library('pagination');
        $this->load->helper('url');
    }

   

    public function index()
    {
        // การตั้งค่าการแบ่งหน้า
        $config['base_url'] = base_url('User_log_backend/index');
        $config['total_rows'] = $this->user_log_model->count_all_activities();
        $config['per_page'] = 15;
        $config['uri_segment'] = 3;

        // กำหนดสไตล์ของ pagination ให้ทำงานกับ Bootstrap
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'หน้าแรก';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'หน้าสุดท้าย';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        // รับข้อมูลจาก search form
    $search = $this->input->get('search');
    $activity_type = $this->input->get('activity_type');
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');
    
    // ตรวจสอบค่า segment ก่อนที่จะใช้งาน
    $page = $this->uri->segment(3);
    $page = (!is_null($page) && is_numeric($page)) ? (int)$page : 0;
    
    // เพิ่มการตรวจสอบ current page ในการตั้งค่า pagination
    $config['cur_page'] = $page;
    
    // Initialize pagination หลังจากตั้งค่าทั้งหมด
    $this->pagination->initialize($config);
    
    // กรองข้อมูลตามเงื่อนไข
    if (!empty($start_date) || !empty($end_date)) {
        // กรณีค้นหาตามช่วงเวลา
        $data['activities'] = $this->user_log_model->filter_by_date_range($start_date, $end_date, $config['per_page'], $page);
        $config['total_rows'] = $this->user_log_model->count_by_date_range($start_date, $end_date);
        $this->pagination->initialize($config);
    } else if (!empty($search)) {
        // กรณีค้นหาด้วยคำค้น
        $data['activities'] = $this->user_log_model->search_activities($search, $config['per_page'], $page);
        $config['total_rows'] = $this->user_log_model->count_search_activities($search);
        $this->pagination->initialize($config);
    } else if (!empty($activity_type)) {
        // กรณีกรองตามประเภทกิจกรรม
        $data['activities'] = $this->user_log_model->filter_by_activity_type($activity_type, $config['per_page'], $page);
        $this->db->where('activity_type', $activity_type);
        $config['total_rows'] = $this->db->count_all_results('tbl_member_activity_logs');
        $this->pagination->initialize($config);
    } else {
        // กรณีไม่มีการกรอง
        $data['activities'] = $this->user_log_model->get_all_activities($config['per_page'], $page);
    }
    
    // รับข้อมูลสถิติสำหรับแสดงในกราฟหรือแดชบอร์ด
    $data['stats'] = $this->user_log_model->get_activity_stats();
    $data['pagination'] = $this->create_custom_pagination_links($config);
    $data['search'] = $search;
    $data['activity_type'] = $activity_type;
    $data['start_date'] = $start_date;
    $data['end_date'] = $end_date;
	
	// เพิ่มหลังบรรทัดนี้: $data['stats'] = $this->user_log_model->get_activity_stats();
	// ดึงข้อมูลผู้ใช้ที่เข้าสู่ระบบบ่อยที่สุด 10 อันดับแรก
	$this->db->select('username, COUNT(*) as login_count');
	$this->db->from('tbl_member_activity_logs');
	$this->db->where('activity_type', 'login');

	// ถ้ามีการกรองด้วยวันที่
	if (!empty($start_date)) {
		$this->db->where('DATE(created_at) >=', $start_date);
	}
	if (!empty($end_date)) {
		$this->db->where('DATE(created_at) <=', $end_date);
	}

	$this->db->group_by('username');
	$this->db->order_by('login_count', 'DESC');
	$this->db->limit(10);

	$user_login_query = $this->db->get();
	$user_login_data = $user_login_query->result();

	// เตรียมข้อมูลสำหรับส่งไปยัง JavaScript
	$userLabels = [];
	$userLoginCounts = [];

	foreach ($user_login_data as $user) {
		$userLabels[] = $user->username;
		$userLoginCounts[] = (int)$user->login_count;
	}

	// เพิ่มข้อมูลเข้าไปในตัวแปรที่ส่งไปยัง view
	$data['userLabels'] = json_encode($userLabels);
	$data['userLoginCounts'] = json_encode($userLoginCounts);
    
    // โหลด views
    $this->load->view('templat/header');
    $this->load->view('asset/css');
    $this->load->view('templat/navbar_system_admin');
    $this->load->view('system_admin/user_log_list', $data);
    $this->load->view('asset/js');
		
	$this->load->view('asset/js/member_log_charts.php');	
	$this->load->view('asset/js/member_log_daily_chart.php');
	$this->load->view('asset/js/member_log_popover_utils.php');	
	$this->load->view('asset/js/member_log_search_filter.php');	
	$this->load->view('asset/js/member_log_user_log.php');	
		
    $this->load->view('templat/footer');
}
    // เมธอดสร้าง pagination links แบบกำหนดเอง
    private function create_custom_pagination_links($config) {
        $output = '<ul class="pagination">';
        
        $total_pages = ceil($config['total_rows'] / $config['per_page']);
        $current_page = $this->uri->segment($config['uri_segment']) ? (int)$this->uri->segment($config['uri_segment']) : 0;
        $current_page = floor($current_page / $config['per_page']) + 1;
        
        // สร้าง query string จาก GET parameters
        $get_params = $_GET;
        $query_string = '';
        if (!empty($get_params)) {
            $query_string = '?' . http_build_query($get_params);
        }
        
        // เปลี่ยนวิธีการสร้าง URL จาก site_url() เป็นการใช้ URI ของ controller โดยตรง
        $base_uri = 'User_log_backend/index';
        
        // First page
        if ($current_page > 1) {
            $output .= '<li class="page-item"><a href="'.base_url($base_uri).$query_string.'" class="page-link">หน้าแรก</a></li>';
        }
        
        // Previous
        if ($current_page > 1) {
            $prev = ($current_page - 2) * $config['per_page'];
            $output .= '<li class="page-item"><a href="'.base_url($base_uri.'/'.$prev).$query_string.'" class="page-link">&laquo;</a></li>';
        }
        
        // Pages
        $start = max(1, $current_page - 2);
        $end = min($total_pages, $current_page + 2);
        
        for ($i = $start; $i <= $end; $i++) {
            $page_offset = ($i - 1) * $config['per_page'];
            if ($current_page == $i) {
                $output .= '<li class="page-item active"><a class="page-link" href="#">'.$i.'</a></li>';
            } else {
                $output .= '<li class="page-item"><a class="page-link" href="'.base_url($base_uri.'/'.$page_offset).$query_string.'">'.$i.'</a></li>';
            }
        }
        
        // Next
        if ($current_page < $total_pages) {
            $next = $current_page * $config['per_page'];
            $output .= '<li class="page-item"><a href="'.base_url($base_uri.'/'.$next).$query_string.'" class="page-link">&raquo;</a></li>';
        }
        
        // Last page
        if ($current_page < $total_pages) {
            $last = ($total_pages - 1) * $config['per_page'];
            $output .= '<li class="page-item"><a href="'.base_url($base_uri.'/'.$last).$query_string.'" class="page-link">หน้าสุดท้าย</a></li>';
        }
        
        $output .= '</ul>';
        return $output;
    }

    public function ajax_chart_data()
    {
        // สำหรับโหลดข้อมูลกราฟแบบ AJAX
        $stats = $this->user_log_model->get_activity_stats();

        // แปลงข้อมูลให้อยู่ในรูปแบบที่เหมาะสมกับกราฟ
        $chart_data = [];

        // ข้อมูลกราฟประเภทกิจกรรม
        $activity_data = [];
        foreach ($stats['activity_types'] as $type) {
            $activity_data[] = [
                'name' => $type->activity_type,
                'value' => $type->count
            ];
        }

        // ข้อมูลกราฟประเภทอุปกรณ์
        $device_data = [];
        foreach ($stats['device_stats'] as $type => $count) {
            $device_data[] = [
                'name' => $type,
                'value' => $count
            ];
        }

        $chart_data = [
            'activity_types' => $activity_data,
            'device_stats' => $device_data
        ];

        header('Content-Type: application/json');
        echo json_encode($chart_data);
    }
    

    
    // อีกวิธีสำหรับการส่งออกเป็น CSV (ในกรณีที่ไม่มี PHPExcel)
  public function export_csv() {
    // รับข้อมูลจาก search form
    $search = $this->input->get('search');
    $activity_type = $this->input->get('activity_type');
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');
    
    // ดึงข้อมูลทั้งหมดโดยไม่มีการแบ่งหน้า
    if (!empty($start_date) || !empty($end_date)) {
        $activities = $this->user_log_model->filter_by_date_range($start_date, $end_date, 999999, 0);
    } else if (!empty($search)) {
        $activities = $this->user_log_model->search_activities($search, 999999, 0);
    } else if (!empty($activity_type)) {
        $activities = $this->user_log_model->filter_by_activity_type($activity_type, 999999, 0);
    } else {
        $activities = $this->user_log_model->get_all_activities(999999, 0);
    }
    
    // ตั้งค่าหัวข้อของ CSV
    $filename = 'รายงานกิจกรรมผู้ใช้_' . date('Y-m-d') . '.csv';
    header("Content-Description: File Transfer"); 
    header("Content-Disposition: attachment; filename=$filename"); 
    header("Content-Type: application/csv; charset=UTF-8");
    
    // เปิดไฟล์ output stream
    $output = fopen("php://output", "w");
    
    // เพิ่ม BOM (Byte Order Mark) สำหรับการแสดงผลภาษาไทยใน Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // เพิ่มหัวข้อตาราง
    fputcsv($output, array(
        'ลำดับ',
        'ผู้ใช้',
        'ชื่อเต็ม',
        'ประเภทกิจกรรม',
        'รายละเอียด',
        'โมดูล',
        'IP Address',
        'อุปกรณ์',
        'เบราว์เซอร์',
        'วันที่และเวลา'
    ));
    
    // วนลูปเพิ่มข้อมูล
    $i = 1;
    foreach ($activities as $activity) {
        $device_info = !empty($activity->device_info) ? json_decode($activity->device_info, true) : [];
        
        // ตรวจสอบข้อมูลอุปกรณ์
        $device = '';
        $browser = '';
        if (isset($device_info['type']) && isset($device_info['device'])) {
            $device = $device_info['type'] . ' - ' . $device_info['device'];
        }
        if (isset($device_info['browser']) && isset($device_info['browser_version'])) {
            $browser = $device_info['browser'] . ' ' . $device_info['browser_version'];
        }
        
        // เพิ่มข้อมูลลงใน CSV
        fputcsv($output, array(
            $i,
            $activity->username,
            $activity->full_name,
            $activity->activity_type,
            $activity->activity_description,
            $activity->module,
            $activity->ip_address,
            $device,
            $browser,
            $activity->created_at
        ));
        $i++;
    }
    
    fclose($output);
    exit();
}
	
/**
 * ดึงข้อมูลการเข้าสู่ระบบรายวันสำหรับเดือนและปีที่ระบุ
 */
public function get_daily_login_data()
{
    // รับค่าเดือนและปีจาก GET parameters
    $month = $this->input->get('month') ? $this->input->get('month') : date('m');
    $year = $this->input->get('year') ? $this->input->get('year') : date('Y');
    
    // สร้างรูปแบบวันที่เริ่มต้นและสิ้นสุดของเดือน
    $start_date = $year . '-' . $month . '-01';
    $end_date = $year . '-' . $month . '-' . date('t', strtotime($start_date));
    
    // ดึงข้อมูลการเข้าสู่ระบบสำเร็จรายวัน
    $this->db->select('DAY(created_at) as day, COUNT(*) as count');
    $this->db->from('tbl_member_activity_logs');
    $this->db->where('activity_type', 'login');
    $this->db->where('created_at >=', $start_date . ' 00:00:00');
    $this->db->where('created_at <=', $end_date . ' 23:59:59');
    $this->db->group_by('DAY(created_at)');
    $success_data = $this->db->get()->result();
    
    // ดึงข้อมูลการเข้าสู่ระบบล้มเหลวรายวัน
    $this->db->select('DAY(created_at) as day, COUNT(*) as count');
    $this->db->from('tbl_member_activity_logs');
    $this->db->where('activity_type', 'failed');
    $this->db->where('created_at >=', $start_date . ' 00:00:00');
    $this->db->where('created_at <=', $end_date . ' 23:59:59');
    $this->db->group_by('DAY(created_at)');
    $failed_data = $this->db->get()->result();
    
    // สร้างข้อมูล JSON สำหรับส่งกลับ
    $response = [
        'success' => $success_data,
        'failed' => $failed_data
    ];
    
    // ส่งคืนข้อมูลในรูปแบบ JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
}