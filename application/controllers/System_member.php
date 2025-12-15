<?php
defined('BASEPATH') or exit('No direct script access allowed');

class System_member extends CI_Controller
{


  public function __construct()
  {
    parent::__construct();
    // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
    if (
      $this->session->userdata('m_system') == 'system_admin' &&
      $this->session->userdata('m_system') == 'super_admin'
    ) {
      redirect('User/choice', 'refresh');
    }

    // ตั้งค่าเวลาหมดอายุของเซสชัน
    // $this->check_session_timeout();
    $this->load->model('member_model');
    $this->load->model('position_model');
    $this->load->library('pagination');
    $this->load->model('user_log_model');
  }

  // private function check_session_timeout()
  // {
  //     $timeout = 900; // 15 นาที
  //    $last_activity = $this->session->userdata('last_activity');

  //    if ($last_activity && (time() - $last_activity > $timeout)) {
  //        $this->session->sess_destroy();
  //       redirect('User/logout', 'refresh');
  //  } else {
  //      $this->session->set_userdata('last_activity', time());
  //  }
  //}

  public function index()
  {
    // ดึงข้อมูลประเภทผู้ใช้งานปัจจุบัน
    $user_system = $this->session->userdata('m_system');

    // ดึงข้อมูลตำแหน่งและจำนวนสมาชิก
    $data['positions'] = $this->member_model->get_positions();
    $data['member_counts'] = [];
    foreach ($data['positions'] as $position) {
      // นับจำนวนสมาชิกแต่ละตำแหน่งตามสิทธิ์การเข้าถึง
      $data['member_counts'][$position->pid] =
        $this->member_model->count_member_by_position($position->pid, $user_system);
    }

    // Get filter parameters
    $search = $this->input->get('search');
    $status = $this->input->get('status');
    $page = $this->input->get('page') ? $this->input->get('page') : 0;

    // Pagination config
    $config['base_url'] = site_url('System_member');
    $config['total_rows'] = $this->member_model->count_filtered_members($search, $status, $user_system);
    $config['per_page'] = 20;
    $config['page_query_string'] = TRUE;
    $config['query_string_segment'] = 'page';
    $config['reuse_query_string'] = TRUE;

    // Pagination styling settings (คงเดิม)
    $config['full_tag_open'] = '<div class="flex items-center space-x-2">';
    $config['full_tag_close'] = '</div>';

    // ปุ่มก่อนหน้า
    $config['prev_link'] = '<i class="fas fa-chevron-left"></i>';
    $config['prev_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['prev_tag_close'] = '</button>';

    // ปุ่มถัดไป
    $config['next_link'] = '<i class="fas fa-chevron-right"></i>';
    $config['next_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['next_tag_close'] = '</button>';

    // ตัวเลขหน้า
    $config['num_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['num_tag_close'] = '</button>';

    // หน้าปัจจุบัน
    $config['cur_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded">';
    $config['cur_tag_close'] = '</button>';

    // First & Last
    $config['first_link'] = '<i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left ml-[-0.5rem]"></i>';
    $config['first_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['first_tag_close'] = '</button>';

    $config['last_link'] = '<i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right ml-[-0.5rem]"></i>';
    $config['last_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['last_tag_close'] = '</button>';

    $this->pagination->initialize($config);

    // คำนวณ start_row และ end_row
    $start_row = $page + 1;
    $end_row = min($page + $config['per_page'], $config['total_rows']);

    // ดึงข้อมูลสมาชิกตามเงื่อนไข
    $data['members'] = $this->member_model->get_filtered_members(
      $search,
      $status,
      $config['per_page'],
      $page,
      $user_system // ส่งประเภทผู้ใช้งานไปด้วย
    );

    // ข้อมูลสำหรับ pagination และการแสดงผล
    $data['pagination'] = $this->pagination->create_links();
    $data['total_rows'] = $config['total_rows'];
    $data['start_row'] = $start_row;
    $data['end_row'] = $end_row;

    // ข้อมูลเพิ่มเติม
    $data['user_system'] = $user_system;
    $data['total_members'] = $this->member_model->count_total_members($user_system);
    $data['count_member'] = $this->member_model->count_member($user_system);

    // ดึงข้อมูลสำหรับรายงานสรุปทั้งหมด
    // 1. ดึงจำนวนสมาชิกภายนอก
    $this->db->from('tbl_member_public');
    $data['external_members_count'] = $this->db->count_all_results();

    // 2. ดึงข้อมูลโมดูลระบบ
    $this->db->from('tbl_member_modules');
    $this->db->where('status', 1);
    $this->db->where('is_trial', 0);
    $data['full_modules'] = $this->db->count_all_results();

    $this->db->from('tbl_member_modules');
    $this->db->where('status', 1);
    $this->db->where('is_trial', 1);
    $data['trial_modules'] = $this->db->count_all_results();

    // 3. ดึงข้อมูลผู้ใช้งานระบบ
    $this->db->select('COUNT(DISTINCT m_id) as count');
    $this->db->from('tbl_member_systems');
    $result = $this->db->get();
    $data['active_users'] = $result->row()->count;

    // Load views
    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/dashboard', $data);
    $this->load->view('member/js');
	  
	 
	  
	  
    $this->load->view('member/footer');
  }

	

	


  public function dashboard()
  {
    // เช็คการล็อกอิน
    if ($this->session->userdata('m_id') == '') {
      redirect('User/login');
    }

    // ดึงข้อมูลประเภทผู้ใช้งานปัจจุบัน
    $user_system = $this->session->userdata('m_system');

    // ดึงข้อมูลตำแหน่งและจำนวนสมาชิก
    $data['positions'] = $this->member_model->get_positions();
    $data['member_counts'] = [];
    foreach ($data['positions'] as $position) {
      $data['member_counts'][$position->pid] =
        $this->member_model->count_member_by_position($position->pid, $user_system);
    }

    // จัดการการค้นหาและตัวกรอง
    $search = $this->input->get('search');
    $status = $this->input->get('status');
    $position_filter = $this->input->get('position');
    $type_filter = $this->input->get('type');
    $page = $this->input->get('page') ? $this->input->get('page') : 0;

    // กำหนดค่า Pagination
    $config['base_url'] = site_url('System_member/dashboard');
    $config['total_rows'] = $this->member_model->count_filtered_members(
      $search,
      $status,
      $position_filter,
      $type_filter,
      $user_system
    );
    $config['per_page'] = 20;
    $config['page_query_string'] = TRUE;
    $config['query_string_segment'] = 'page';
    $config['reuse_query_string'] = TRUE;

    // Pagination styling
    $config['full_tag_open'] = '<div class="flex items-center space-x-2">';
    $config['full_tag_close'] = '</div>';

    // First & Last buttons
    $config['first_link'] = '<i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left ml-[-0.5rem]"></i>';
    $config['first_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['first_tag_close'] = '</button>';

    $config['last_link'] = '<i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right ml-[-0.5rem]"></i>';
    $config['last_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['last_tag_close'] = '</button>';

    // Next & Previous buttons
    $config['next_link'] = '<i class="fas fa-chevron-right"></i>';
    $config['next_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['next_tag_close'] = '</button>';

    $config['prev_link'] = '<i class="fas fa-chevron-left"></i>';
    $config['prev_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['prev_tag_close'] = '</button>';

    // Current page
    $config['cur_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded">';
    $config['cur_tag_close'] = '</button>';

    // Number links
    $config['num_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['num_tag_close'] = '</button>';

    // Initialize pagination
    $this->pagination->initialize($config);

    // คำนวณ rows ที่แสดง
    $start_row = $page + 1;
    $end_row = min($page + $config['per_page'], $config['total_rows']);

    // ดึงข้อมูลสมาชิก
    $data['members'] = $this->member_model->get_filtered_members(
      $search,
      $status,
      $position_filter,
      $type_filter,
      $config['per_page'],
      $page,
      $user_system
    );

    // จัดเตรียมข้อมูลสำหรับ view
    $data['pagination'] = $this->pagination->create_links();
    $data['total_rows'] = $config['total_rows'];
    $data['start_row'] = $start_row;
    $data['end_row'] = $end_row;
    $data['search'] = $search;
    $data['status'] = $status;
    $data['position_filter'] = $position_filter;
    $data['type_filter'] = $type_filter;
    $data['user_system'] = $user_system;

    // ข้อมูลเพิ่มเติม
    $data['total_members'] = $this->member_model->count_total_members($user_system);

    // จัดเตรียมข้อมูล system types สำหรับการกรอง
    $data['system_types'] = [
      'system_admin' => [
        'name' => 'ผู้ดูแลระบบสูงสุด',
        'gradient_class' => 'from-red-100 to-red-50',
        'icon_color' => 'text-red-800',
        'count' => $this->member_model->count_members_by_type('system_admin')
      ],
      'super_admin' => [
        'name' => 'ผู้ดูแลระบบ',
        'gradient_class' => 'from-blue-100 to-blue-50',
        'icon_color' => 'text-blue-800',
        'count' => $this->member_model->count_members_by_type('super_admin')
      ],
      'user_admin' => [
        'name' => 'ผู้ดูแลเฉพาะส่วน',
        'gradient_class' => 'from-green-100 to-green-50',
        'icon_color' => 'text-green-800',
        'count' => $this->member_model->count_members_by_type('user_admin')
      ]
    ];

    // ดึงข้อมูลสำหรับรายงานสรุปทั้งหมด
    // 1. ดึงจำนวนสมาชิกภายนอก
    $this->db->from('tbl_member_public');
    $data['external_members_count'] = $this->db->count_all_results();

    // 2. ดึงข้อมูลโมดูลระบบ
    $this->db->from('tbl_member_modules');
    $this->db->where('status', 1);
    $this->db->where('is_trial', 0);
    $data['full_modules'] = $this->db->count_all_results();

    $this->db->from('tbl_member_modules');
    $this->db->where('status', 1);
    $this->db->where('is_trial', 1);
    $data['trial_modules'] = $this->db->count_all_results();

    // 3. ดึงข้อมูลผู้ใช้งานระบบ
    $this->db->select('COUNT(DISTINCT m_id) as count');
    $this->db->from('tbl_member_systems');
    $result = $this->db->get();
    $data['active_users'] = $result->row()->count;

    // Load views
    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/dashboard', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
  }






  public function add_member()
  {
    if ($this->session->userdata('m_id') == '') {
      redirect('User/login');
    }

    // ดึงข้อมูล modules ที่เปิดใช้งาน
    $data['modules'] = $this->db->select('*')
      ->from('tbl_member_modules')
      ->where('status', 1)
      ->where('is_trial', 0) // เฉพาะที่ไม่ใช่ trial version
      ->order_by('display_order', 'ASC')
      ->get()
      ->result();

    // ดึงข้อมูลตำแหน่ง
    $data['positions'] = $this->db->where('pstatus', 'show')
      ->order_by('pid')
      ->get('tbl_position')
      ->result();

    $data['grant_users'] = $this->db->get('tbl_grant_user')->result();

    // เพิ่มข้อมูลสิทธิ์ของแต่ละโมดูล
    foreach ($data['modules'] as $module) {
      // ดึงสิทธิ์การใช้งานของแต่ละโมดูล
      $module->permissions = $this->db->select('ms.*, m.m_fname')
        ->from('tbl_member_systems ms')
        ->join('tbl_member m', 'm.m_id = ms.m_id')
        ->where('ms.module_id', $module->id)
        ->where('ms.status', 1)
        ->get()
        ->result();
    }

    // เพิ่มข้อมูลอื่นๆ ที่จำเป็น
    $data['user_type'] = $this->session->userdata('m_system');
    $data['is_system_admin'] = ($data['user_type'] === 'system_admin');
    $data['is_super_admin'] = ($data['user_type'] === 'super_admin');

    // กำหนด systems เพื่อความเข้ากันได้กับโค้ดเดิม
    $data['systems'] = $data['modules'];

    // Load views                           
    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/add_member', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
  }





 public function add_member_db()
{
    try {
        $this->db->trans_start();

        // 1. บันทึกข้อมูลผู้ใช้หลัก
        $member_data = array(
            'm_fname' => $this->input->post('m_fname'),
            'm_lname' => $this->input->post('m_lname'),
            'm_username' => $this->input->post('m_username'),
            'm_password' => sha1($this->input->post('m_password')),
            'm_email' => $this->input->post('m_username'),
            'm_phone' => $this->input->post('m_phone'),
            'm_system' => $this->input->post('m_system'),
            'm_by' => $this->session->userdata('m_id'),
            'm_datesave' => date('Y-m-d H:i:s')
        );

        // อัพเดตตำแหน่ง (ref_pid) เมื่อไม่ใช่ system_admin
        if ($this->input->post('m_system') !== 'system_admin' && $this->input->post('ref_pid')) {
            $member_data['ref_pid'] = $this->input->post('ref_pid');
        } else {
            $member_data['ref_pid'] = 1; // default สำหรับ system_admin คือ 1
        }

        // 2. จัดการสิทธิ์การใช้งานระบบ
        $selected_modules = $this->input->post('grant_system_ref_id');
        $grant_user_ids = $this->input->post('grant_user_ids');
        $grant_system_ref_ids = [];

        if (!empty($selected_modules)) {
            foreach ($selected_modules as $module_id) {
                // ถ้าเป็นโมดูลจัดการเว็บไซต์ (ID = 2)
                if ($module_id == '2' && !empty($grant_user_ids)) {
                    // ตรวจสอบว่าเลือก "ทั้งหมด" (value = 1) หรือไม่
                    if (in_array('1', $grant_user_ids)) {
                        // ถ้าเลือก "ทั้งหมด" ให้เพิ่ม grant_user_ids ที่สำคัญเข้าไป
                        $important_grant_ids = [1, 50, 53, 105, 106, 107, 108, 109, 125];
                        
                        // รวม grant_user_ids ที่มีอยู่กับ important_grant_ids
                        $combined_grant_ids = array_unique(array_merge($grant_user_ids, $important_grant_ids));
                        
                        // เรียงลำดับ grant_user_ids
                        sort($combined_grant_ids);
                        
                        $member_data['grant_user_ref_id'] = implode(',', $combined_grant_ids);
                        
                        // Log สำหรับการ debug
                        log_message('info', 'Selected "All Access" - Combined grant_user_ids: ' . $member_data['grant_user_ref_id']);
                    } else {
                        // ถ้าไม่ได้เลือก "ทั้งหมด" ให้ใช้ grant_user_ids ที่เลือก
                        $member_data['grant_user_ref_id'] = implode(',', array_unique($grant_user_ids));
                        
                        log_message('info', 'Selected specific permissions - grant_user_ids: ' . $member_data['grant_user_ref_id']);
                    }
                }
                $grant_system_ref_ids[] = $module_id;
            }
        }

        // รวม grant_system_ref_id เป็น string
        $member_data['grant_system_ref_id'] = !empty($grant_system_ref_ids) ?
            implode(',', array_unique($grant_system_ref_ids)) : '';

        // Log สำหรับการตรวจสอบ
        log_message('info', 'Final grant_system_ref_id: ' . $member_data['grant_system_ref_id']);
        log_message('info', 'Final grant_user_ref_id: ' . (isset($member_data['grant_user_ref_id']) ? $member_data['grant_user_ref_id'] : 'Not set'));

        // 3. บันทึกรูปภาพ (ถ้ามี)
        $avatar_url = $this->input->post('avatar_url');

        // ตรวจสอบว่ามีการเลือก Avatar URL หรือไม่
        if (!empty($avatar_url)) {
            // ดาวน์โหลด Avatar จาก URL และบันทึกเป็นไฟล์
            $avatar_image = file_get_contents($avatar_url);
            if ($avatar_image !== false) {
                // สร้างชื่อไฟล์
                $avatar_filename = 'avatar_' . time() . '.png';
                $avatar_path = './docs/img/avatar/' . $avatar_filename;

                // บันทึกไฟล์
                if (file_put_contents($avatar_path, $avatar_image)) {
                    $member_data['m_img'] = $avatar_filename;
                }
            }
        }
        // ถ้าไม่ได้เลือก Avatar URL ให้ตรวจสอบการอัปโหลดไฟล์
        else if (!empty($_FILES['m_img']['name'])) {
            // โค้ดจัดการรูปภาพ
            $config['upload_path'] = './docs/img/avatar/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = 2048;
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('m_img')) {
                $upload_data = $this->upload->data();
                $member_data['m_img'] = $upload_data['file_name'];
            }
        }

        // 4. บันทึกข้อมูลสมาชิก
        $this->db->insert('tbl_member', $member_data);
        $member_id = $this->db->insert_id();

        // 5. เพิ่มข้อมูลใน tbl_member_systems
        if (!empty($selected_modules)) {
            foreach ($selected_modules as $module_id) {
                $module = $this->db->where('id', $module_id)
                    ->get('tbl_member_modules')
                    ->row();

                if ($module) {
                    $this->db->insert('tbl_member_systems', [
                        'm_id' => $member_id,
                        'module_id' => $module_id,
                        'status' => 1,
                        'name' => $module->name,
                        'description' => $module->description,
                        'is_active' => 1,
                        'created_by' => $this->session->userdata('m_id'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        // 6. บันทึกสิทธิ์การใช้งานเมนู
        $selected_menus = $this->input->post('module_menu_ids');
        if (!empty($selected_menus)) {
            foreach ($selected_menus as $menu_id) {
                $menu = $this->db->where('id', $menu_id)
                    ->get('tbl_member_module_menus')
                    ->row();

                if ($menu) {
                    $permission_data = [
                        'member_id' => $member_id,
                        'system_id' => $menu_id,
                        'permission_code' => $menu->code,
                        'is_active' => 1,
                        'created_by' => $this->session->userdata('m_id'),
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('tbl_member_user_permissions', $permission_data);
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            throw new Exception('การบันทึกข้อมูลไม่สมบูรณ์');
        }

        // 7. บันทึกสำเร็จ สร้าง response
        $response = [
            'success' => true,
            'message' => 'เพิ่มสมาชิกใหม่เรียบร้อยแล้ว',
            'member_id' => $member_id
        ];

        // ส่งคืน JSON response หรือทำ redirect
        if ($this->input->is_ajax_request()) {
            echo json_encode($response);
        } else {
            redirect('System_member/member_web?highlight=' . $member_id);
        }

    } catch (Exception $e) {
        $this->db->trans_rollback();

        $response = [
            'success' => false,
            'message' => $e->getMessage()
        ];

        // ส่งคืน JSON response
        echo json_encode($response);
    }
}





  public function get_member_permissions($user_id)
  {
    header('Content-Type: application/json');

    try {
      if (!$user_id) {
        throw new Exception('ไม่พบรหัสผู้ใช้');
      }

      $module_id = $this->input->get('module_id');
      if (!$module_id) {
        throw new Exception('ไม่พบรหัสโมดูล');
      }

      // ดึงข้อมูลใช้วิธีเดียวกับใน module() function
      $query = $this->db->query("
            SELECT 
                mmm.id,
                mmm.name,
                mmm.code,
                mmm.module_id,
                mmm.display_order,
                CASE WHEN mup.id IS NOT NULL THEN 1 ELSE 0 END as is_checked
            FROM 
                tbl_member_module_menus mmm
            LEFT JOIN 
                (SELECT * FROM tbl_member_user_permissions WHERE member_id = ?) mup 
                ON mmm.id = mup.system_id
            WHERE 
                mmm.module_id = ? AND mmm.status = 1
            ORDER BY 
                mmm.display_order ASC, mmm.name ASC
        ", array($user_id, $module_id));

      if (!$query) {
        throw new Exception('เกิดข้อผิดพลาดในการดึงข้อมูล');
      }

      $menus = $query->result();

      // Log for debugging
      log_message('debug', 'User ID: ' . $user_id . ', Module ID: ' . $module_id . ', Found menus: ' . count($menus));

      echo json_encode([
        'success' => true,
        'data' => $menus
      ]);

    } catch (Exception $e) {
      log_message('error', 'Permission Error: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }





  public function edit_member($m_id)
  {
    try {
      // ดึงข้อมูลสมาชิก
      $this->db->select('m.*, p.pname, 
                          GROUP_CONCAT(DISTINCT ms.name) as system_names, 
                          GROUP_CONCAT(DISTINCT ms.module_id) as module_ids');
      $this->db->from('tbl_member as m');
      $this->db->join('tbl_position as p', 'm.ref_pid = p.pid', 'left');
      $this->db->join('tbl_member_systems as ms', 'm.m_id = ms.m_id', 'left');
      $this->db->where('m.m_id', $m_id);
      $this->db->group_by('m.m_id');

      $member = $this->db->get()->row();

      if (!$member) {
        throw new Exception('ไม่พบข้อมูลสมาชิก');
      }

      // ดึงข้อมูล modules ที่เปิดใช้งาน
      $data['modules'] = $this->db->select('*')
        ->from('tbl_member_modules')
        ->where('status', 1)
        ->where('is_trial', 0)
        ->order_by('display_order', 'ASC')
        ->get()
        ->result();

      // ดึงข้อมูลสิทธิ์ของแต่ละโมดูล
      foreach ($data['modules'] as $module) {
        $module->permissions = $this->db->select('ms.*, m.m_fname')
          ->from('tbl_member_systems ms')
          ->join('tbl_member m', 'm.m_id = ms.m_id')
          ->where('ms.module_id', $module->id)
          ->where('ms.status', 1)
          ->get()
          ->result();
      }

      // จัดเตรียมข้อมูล
      $data['member'] = $member;
      $data['user_type'] = $this->session->userdata('m_system');
      $data['is_system_admin'] = ($data['user_type'] === 'system_admin');
      $data['is_super_admin'] = ($data['user_type'] === 'super_admin');
      $data['positions'] = $this->position_model->list_position_back_office();
      $data['grant_users'] = $this->db->get('tbl_grant_user')->result();

      // Load views
      $this->load->view('member/header');
      $this->load->view('member/css');
      $this->load->view('member/sidebar');
      $this->load->view('member/edit_member', $data);
      $this->load->view('member/js');
      $this->load->view('member/footer');

    } catch (Exception $e) {
      log_message('error', 'Error in edit_member: ' . $e->getMessage());
      $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการดึงข้อมูล');
      redirect('System_member');
    }
  }







  public function edit_member_db()
{
    try {
        $this->db->trans_start();

        $member_id = $this->input->post('m_id');
        $selected_modules = $this->input->post('grant_system_ref_id');
        $selected_menus = $this->input->post('module_menu_ids');
        $grant_user_ids = $this->input->post('grant_user_ids');

        // 1. อัพเดทข้อมูลหลักของสมาชิก
        $member_data = [
            'm_fname' => $this->input->post('m_fname'),
            'm_lname' => $this->input->post('m_lname'),
            'm_username' => $this->input->post('m_username'),
            'm_email' => $this->input->post('m_username'),
            'm_phone' => $this->input->post('m_phone'),
            'm_system' => $this->input->post('m_system'),
            'm_by' => $this->session->userdata('m_id'),
            'm_datesave' => date('Y-m-d H:i:s')
        ];

        // อัพเดตตำแหน่ง (ref_pid) เมื่อไม่ใช่ system_admin
        if ($this->input->post('m_system') !== 'system_admin' && $this->input->post('ref_pid')) {
            $member_data['ref_pid'] = $this->input->post('ref_pid');
        } else if ($this->input->post('m_system') === 'system_admin') {
            $member_data['ref_pid'] = 1; // default สำหรับ system_admin คือ 1
        }

        // จัดการรหัสผ่าน
        if ($this->input->post('m_password')) {
            $member_data['m_password'] = sha1($this->input->post('m_password'));
        }

        // 2. จัดการสิทธิ์การใช้งานระบบ
        if (!empty($selected_modules)) {
            $grant_system_ref_ids = [];
            
            foreach ($selected_modules as $module_id) {
                // ถ้าเป็นโมดูลจัดการเว็บไซต์ (ID = 2)
                if ($module_id == '2' && !empty($grant_user_ids)) {
                    // ตรวจสอบว่าเลือก "ทั้งหมด" (value = 1) หรือไม่
                    if (in_array('1', $grant_user_ids)) {
                        // ถ้าเลือก "ทั้งหมด" ให้เพิ่ม grant_user_ids ที่สำคัญเข้าไป
                        $important_grant_ids = [1, 50, 53, 105, 106, 107, 108, 109, 125];
                        
                        // รวม grant_user_ids ที่มีอยู่กับ important_grant_ids
                        $combined_grant_ids = array_unique(array_merge($grant_user_ids, $important_grant_ids));
                        
                        // เรียงลำดับ grant_user_ids
                        sort($combined_grant_ids);
                        
                        $member_data['grant_user_ref_id'] = implode(',', $combined_grant_ids);
                        
                        // Log สำหรับการ debug
                        log_message('info', 'Edit Member - Selected "All Access" - Combined grant_user_ids: ' . $member_data['grant_user_ref_id']);
                    } else {
                        // ถ้าไม่ได้เลือก "ทั้งหมด" ให้ใช้ grant_user_ids ที่เลือก
                        $member_data['grant_user_ref_id'] = implode(',', array_unique($grant_user_ids));
                        
                        log_message('info', 'Edit Member - Selected specific permissions - grant_user_ids: ' . $member_data['grant_user_ref_id']);
                    }
                }
                $grant_system_ref_ids[] = $module_id;
            }
            
            $member_data['grant_system_ref_id'] = implode(',', array_unique($grant_system_ref_ids));
        } else {
            $member_data['grant_system_ref_id'] = '';
            $member_data['grant_user_ref_id'] = '';
        }

        // Log สำหรับการตรวจสอบ
        log_message('info', 'Edit Member - Final grant_system_ref_id: ' . $member_data['grant_system_ref_id']);
        log_message('info', 'Edit Member - Final grant_user_ref_id: ' . (isset($member_data['grant_user_ref_id']) ? $member_data['grant_user_ref_id'] : 'Not set'));

        // 3. จัดการรูปภาพ
        $avatar_url = $this->input->post('avatar_url');

        // ตรวจสอบว่ามีการเลือก Avatar URL หรือไม่
        if (!empty($avatar_url)) {
            // ดาวน์โหลด Avatar จาก URL และบันทึกเป็นไฟล์
            $avatar_image = file_get_contents($avatar_url);
            if ($avatar_image !== false) {
                // สร้างชื่อไฟล์
                $avatar_filename = 'avatar_' . time() . '.png';
                $avatar_path = './docs/img/' . $avatar_filename;

                // บันทึกไฟล์
                if (file_put_contents($avatar_path, $avatar_image)) {
                    $member_data['m_img'] = $avatar_filename;
                }
            }
        }
        // ถ้าไม่ได้เลือก Avatar URL ให้ตรวจสอบการอัปโหลดไฟล์
        else if (!empty($_FILES['m_img']['name'])) {
            // โค้ดจัดการรูปภาพ
            $config['upload_path'] = './docs/img/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = 2048;
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('m_img')) {
                $upload_data = $this->upload->data();
                $member_data['m_img'] = $upload_data['file_name'];
            }
        }

        // 4. อัพเดทข้อมูลสมาชิก
        $this->db->where('m_id', $member_id)
            ->update('tbl_member', $member_data);

        // 5. จัดการสิทธิ์การใช้งานระบบ
        if (!empty($selected_modules)) {
            // ลบข้อมูลเก่า
            $this->db->where('member_id', $member_id)
                ->delete('tbl_member_user_permissions');
            $this->db->where('m_id', $member_id)
                ->delete('tbl_member_systems');

            // เพิ่มข้อมูล tbl_member_systems
            foreach ($selected_modules as $module_id) {
                $module = $this->db->where('id', $module_id)
                    ->get('tbl_member_modules')
                    ->row();

                if ($module) {
                    $this->db->insert('tbl_member_systems', [
                        'm_id' => $member_id,
                        'module_id' => $module_id,
                        'status' => 1,
                        'name' => $module->name,
                        'description' => $module->description,
                        'is_active' => 1,
                        'created_by' => $this->session->userdata('m_id'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            // 6. จัดการสิทธิ์เมนู
            if (!empty($selected_menus)) {
                foreach ($selected_menus as $menu_id) {
                    $menu = $this->db->where('id', $menu_id)
                        ->get('tbl_member_module_menus')
                        ->row();

                    if ($menu && in_array($menu->module_id, $selected_modules)) {
                        $this->db->insert('tbl_member_user_permissions', [
                            'member_id' => $member_id,
                            'system_id' => $menu_id,
                            'permission_code' => $menu->code,
                            'is_active' => 1,
                            'created_by' => $this->session->userdata('m_id'),
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            throw new Exception('การบันทึกข้อมูลไม่สมบูรณ์');
        }

        // ส่งคืน JSON response หรือทำ redirect
        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'success' => true,
                'message' => 'อัพเดทข้อมูลเรียบร้อยแล้ว'
            ]);
        } else {
            redirect('System_member/member_web?highlight=' . $member_id);
        }

    } catch (Exception $e) {
        $this->db->trans_rollback();

        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } else {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('System_member/edit_member/' . $member_id);
        }
    }
}




  public function del_member($m_id)
  {
    try {
      // ตรวจสอบข้อมูลสมาชิก
      $member = $this->db->get_where('tbl_member', ['m_id' => $m_id])->row();
      if (!$member) {
        throw new Exception('ไม่พบข้อมูลสมาชิก');
      }

      $this->db->trans_start();

      // ลบข้อมูลที่เกี่ยวข้อง
      $this->db->delete('tbl_member_systems', ['m_id' => $m_id]);
      $this->db->delete('tbl_member_user_permissions', ['member_id' => $m_id]);

      // ลบรูปภาพ (ถ้ามี)
      if ($member->m_img && file_exists('./docs/img/' . $member->m_img)) {
        unlink('./docs/img/' . $member->m_img);
      }

      // บันทึก log การลบ
      $this->db->insert('tbl_log_delete', [
        'table_name' => 'tbl_member',
        'row_id' => $m_id,
        'deleted_by' => $this->session->userdata('m_fname'),
        'deleted_data' => json_encode($member),
        'delete_date' => date('Y-m-d H:i:s')
      ]);

      // ลบข้อมูลสมาชิก
      $this->db->delete('tbl_member', ['m_id' => $m_id]);

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('เกิดข้อผิดพลาดในการลบข้อมูล');
      }

      $response = ['status' => true, 'message' => 'ลบข้อมูลเรียบร้อยแล้ว'];
    } catch (Exception $e) {
      $this->db->trans_rollback();
      $response = ['status' => false, 'message' => $e->getMessage()];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
  }




  public function permanent_delete($log_id)
  {
    // ตรวจสอบสิทธิการเข้าถึง
    if (
      !in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])
    ) {
      redirect('User/logout', 'refresh');
    }

    // ดึงข้อมูลล็อกที่จะลบถาวร
    $log = $this->db->get_where('tbl_log_delete', ['log_id' => $log_id])->row();

    if (!$log) {
      // ไม่พบล็อกที่ระบุ
      $this->session->set_flashdata('restore_error', TRUE);
      $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลล็อกที่ระบุ');
      redirect('System_member/delete_log', 'refresh');
    }

    try {
      // เริ่ม transaction
      $this->db->trans_start();

      // บันทึกข้อมูลการลบถาวรลงใน log (ถ้าต้องการเก็บประวัติการลบถาวร)
      $permanent_delete_log = [
        'original_log_id' => $log_id,
        'table_name' => $log->table_name,
        'row_id' => $log->row_id,
        'deleted_by' => $this->session->userdata('m_fname'),
        'deleted_data' => $log->deleted_data,
        'permanent_delete_date' => date('Y-m-d H:i:s'),
        'reason' => 'ลบถาวรโดยผู้ดูแลระบบ'
      ];

      // สร้างตารางใหม่ถ้ายังไม่มี (ถ้าต้องการเก็บประวัติการลบถาวร)
      if (!$this->db->table_exists('tbl_permanent_delete_log')) {
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `tbl_permanent_delete_log` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `original_log_id` int(11) NOT NULL,
                  `table_name` varchar(50) NOT NULL,
                  `row_id` int(11) NOT NULL,
                  `deleted_by` varchar(100) NOT NULL,
                  `deleted_data` text NOT NULL,
                  `permanent_delete_date` datetime NOT NULL,
                  `reason` varchar(255) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
      }

      // บันทึกประวัติการลบถาวร
      $this->db->insert('tbl_permanent_delete_log', $permanent_delete_log);

      // ลบข้อมูลออกจากตาราง log_delete
      $this->db->delete('tbl_log_delete', ['log_id' => $log_id]);

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception("ไม่สามารถลบข้อมูลได้");
      }

      // แสดงข้อความสำเร็จ
      $this->session->set_flashdata('delete_success', TRUE);

    } catch (Exception $e) {
      $this->db->trans_rollback();
      $this->session->set_flashdata('restore_error', TRUE);
      $this->session->set_flashdata('error_message', $e->getMessage());
    }

    redirect('System_member/delete_log');
  }






  public function member_web()
  {
    // Get current user's system type
    $user_system = $this->session->userdata('m_system');

    // Get filter parameters
    $search = $this->input->get('search');
    $status = $this->input->get('status');
    $page = ($this->input->get('page')) ? $this->input->get('page') : 0;

    // เพิ่มพารามิเตอร์การเรียงลำดับ
    $sort_by = $this->input->get('sort_by') ? $this->input->get('sort_by') : 'm_id';
    $sort_order = $this->input->get('sort_order') ? $this->input->get('sort_order') : 'desc';

    // กำหนดจำนวนรายการต่อหน้า
    $per_page = 20;

    // Get member data and stats
    $data = array();
    $data['user_system'] = $user_system;

    // นับจำนวนสมาชิกทั้งหมด
    $data['total_members'] = $this->member_model->count_filtered_members_web($search, $status, $user_system);

    // ข้อมูลตำแหน่ง
    $data['positions'] = $this->member_model->get_positions();
    $data['member_counts'] = [];
    foreach ($data['positions'] as $position) {
      $data['member_counts'][$position->pid] =
        $this->member_model->count_member_by_position($position->pid, $user_system);
    }



    // Pagination config
    $config['base_url'] = site_url('System_member/member_web');
    $config['total_rows'] = $data['total_members'];
    $config['per_page'] = $per_page;
    $config['page_query_string'] = TRUE;
    $config['query_string_segment'] = 'page';
    $config['reuse_query_string'] = TRUE;

    // Pagination styling
    $config['full_tag_open'] = '<div class="flex items-center space-x-2">';
    $config['full_tag_close'] = '</div>';

    $config['first_link'] = '<i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left ml-[-0.5rem]"></i>';
    $config['first_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['first_tag_close'] = '</button>';

    $config['last_link'] = '<i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right ml-[-0.5rem]"></i>';
    $config['last_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['last_tag_close'] = '</button>';

    $config['next_link'] = '<i class="fas fa-chevron-right"></i>';
    $config['next_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['next_tag_close'] = '</button>';

    $config['prev_link'] = '<i class="fas fa-chevron-left"></i>';
    $config['prev_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['prev_tag_close'] = '</button>';

    $config['cur_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded">';
    $config['cur_tag_close'] = '</button>';

    $config['num_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['num_tag_close'] = '</button>';

    $this->pagination->initialize($config);

    // Calculate rows
    $start_row = $page + 1;
    $end_row = min($page + $per_page, $config['total_rows']);

    // Get member list data
    $data['members'] = $this->member_model->get_filtered_members_web(
      $search,
      $status,
      $per_page,
      $page,
      $user_system,
      $sort_by,   // เพิ่มพารามิเตอร์
      $sort_order // เพิ่มพารามิเตอร์
    );

    // ส่งค่าไปยัง view
    $data['sort_by'] = $sort_by;
    $data['sort_order'] = $sort_order;

    // Set additional view data
    $data['pagination'] = $this->pagination->create_links();
    $data['total_rows'] = $config['total_rows'];
    $data['start_row'] = $start_row;
    $data['end_row'] = $end_row;

    // Get member stats
    $data['count_member'] = $this->member_model->count_member($user_system);
    $data['status_counts'] = $this->member_model->get_members_status_count_web($user_system);
    $data['position_counts'] = $this->member_model->get_members_position_count_web($user_system);

    // Check if highlight parameter exists
    $highlight_id = $this->input->get('highlight');
    if ($highlight_id) {
      $data['highlight_id'] = $highlight_id;
    }

    // Load views
    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/member_web', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
  }







  public function toggle_status_saraban()
  {
    header('Content-Type: application/json');
    error_reporting(0);

    if (!$this->input->is_ajax_request()) {
      echo json_encode(['success' => false, 'message' => 'Invalid request']);
      return;
    }

    $member_id = $this->input->post('member_id');
    $status = $this->input->post('status');

    if (empty($member_id)) {
      echo json_encode(['success' => false, 'message' => 'Invalid member ID']);
      return;
    }

    try {
      $this->db->trans_start();

      $existing = $this->db->where('member_id', $member_id)
        ->get('tbl_member_saraban')
        ->row();

      if ($existing) {
        // Update
        $this->db->where('member_id', $member_id)
          ->update('tbl_member_saraban', [
            'can_sign' => $status,
            'ms_by' => $this->session->userdata('m_fname'), // บันทึกชื่อคนที่ toggle
            'ms_date' => date('Y-m-d H:i:s')
          ]);
      } else {
        // Insert
        $this->db->insert('tbl_member_saraban', [
          'member_id' => $member_id,
          'can_sign' => $status,
          'ms_by' => $this->session->userdata('m_fname'),
          'ms_date' => date('Y-m-d H:i:s')
        ]);
      }

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('Database error');
      }

      echo json_encode([
        'success' => true,
        'message' => 'อัพเดทสถานะเรียบร้อยแล้ว',
        'updated_by' => $this->session->userdata('m_fname'),
        'updated_at' => date('Y-m-d H:i:s')
      ]);

    } catch (Exception $e) {
      $this->db->trans_rollback();
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }
















  public function toggle_status_system()
  {
    header('Content-Type: application/json; charset=utf-8');

    // ป้องกัน error reporting แสดงใน output
    error_reporting(0);

    if (!$this->input->is_ajax_request() || $this->session->userdata('m_system') !== 'system_admin') {
      echo json_encode([
        'success' => false,
        'message' => 'ไม่มีสิทธิ์ดำเนินการ'
      ]);
      return;
    }

    try {
      $grant_system_id = $this->input->post('grant_system_id');
      $type = $this->input->post('type');
      $status = $this->input->post('status');

      // Validate input
      if (!$grant_system_id || !in_array($type, ['active', 'menu']) || !in_array($status, ['0', '1'])) {
        throw new Exception('ข้อมูลไม่ถูกต้อง');
      }

      // Determine which column to update
      $column = ($type === 'active') ? 'grant_system_active' : 'grant_system_menu';

      // Update database
      $this->db->where('grant_system_id', $grant_system_id);
      $success = $this->db->update('tbl_grant_system', [$column => $status]);

      if (!$success) {
        throw new Exception($this->db->error()['message']);
      }

      $action_text = $type === 'active' ?
        ($status == 1 ? 'Full Version' : 'Trial Version') :
        ($status == 1 ? 'เปิดเมนู' : 'ปิดเมนู');

      echo json_encode([
        'success' => true,
        'message' => "เปลี่ยนเป็น {$action_text} เรียบร้อยแล้ว"
      ]);

    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
    exit;
  }



  public function delete_log()
  {

    // Get filter parameters
    $search_user = $this->input->get('search_user');
    $date_from = $this->input->get('date_from');
    $date_to = $this->input->get('date_to');

    // Pagination config
    $config['base_url'] = site_url('System_member/delete_log');
    $config['total_rows'] = $this->member_model->count_filtered_logs($search_user, $date_from, $date_to);
    $config['per_page'] = 20;
    $config['page_query_string'] = TRUE;
    $config['query_string_segment'] = 'page';
    $config['reuse_query_string'] = TRUE;

    // Add pagination styling (เหมือนกับที่ใช้ในหน้า dashboard)

    $this->pagination->initialize($config);

    // Calculate rows
    $page = $this->input->get('page') ? $this->input->get('page') : 0;
    $start_row = $page + 1;
    $end_row = min($page + $config['per_page'], $config['total_rows']);

    // Get data
    $data['logs'] = $this->member_model->get_filtered_logs($search_user, $date_from, $date_to, $config['per_page'], $page);
    $data['pagination'] = $this->pagination->create_links();
    $data['total_rows'] = $config['total_rows'];
    $data['start_row'] = $start_row;
    $data['end_row'] = $end_row;

    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/delete_log', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
  }

  public function restore_member($log_id)
  {
    // ดึงข้อมูล log ที่จะกู้คืน
    $log = $this->member_model->get_log_by_id($log_id);

    if ($log) {
      // แปลง JSON string เป็น array
      $member_data = json_decode($log->deleted_data, true);

      // เริ่ม transaction
      $this->db->trans_start();

      try {
        // ตรวจสอบว่าเป็นข้อมูลจากตารางอะไร
        $table_name = $log->table_name;

        // ลบ primary key เพื่อให้ระบบสร้าง id ใหม่
        if ($table_name == 'tbl_member') {
          unset($member_data['m_id']);

          // เพิ่มข้อมูลผู้กู้คืนและเวลา
          $member_data['restored_by'] = $this->session->userdata('m_fname');
          $member_data['restored_date'] = date('Y-m-d H:i:s');

          // ตรวจสอบว่า username ซ้ำหรือไม่
          $existing_user = $this->db->get_where('tbl_member', ['m_username' => $member_data['m_username']])->row();
          if ($existing_user) {
            // ถ้าซ้ำให้เพิ่ม timestamp ต่อท้าย
            $member_data['m_username'] = $member_data['m_username'] . '_' . time();
          }

          // ตรวจสอบและลบคอลัมน์ที่ไม่มีในตาราง
          $columns = $this->db->list_fields('tbl_member');
          foreach (array_keys($member_data) as $key) {
            if (!in_array($key, $columns)) {
              unset($member_data[$key]);
            }
          }

          // เพิ่มข้อมูลกลับเข้าตาราง member
          $insert = $this->db->insert('tbl_member', $member_data);
          if (!$insert) {
            throw new Exception("ไม่สามารถเพิ่มข้อมูลสมาชิกได้");
          }

          // เก็บ ID ที่เพิ่งถูกสร้าง
          $new_member_id = $this->db->insert_id();

        } else if ($table_name == 'tbl_member_public') {
          unset($member_data['mp_id']);

          // เพิ่มข้อมูลผู้กู้คืนและเวลา
          $member_data['mp_updated_by'] = $this->session->userdata('m_fname');
          $member_data['mp_updated_date'] = date('Y-m-d H:i:s');

          // ตรวจสอบว่า email ซ้ำหรือไม่
          $existing_user = $this->db->get_where('tbl_member_public', ['mp_email' => $member_data['mp_email']])->row();
          if ($existing_user) {
            // ถ้าซ้ำให้เพิ่ม timestamp ต่อท้าย
            $member_data['mp_email'] = $member_data['mp_email'] . '_' . time();
          }

          // ตรวจสอบและลบคอลัมน์ที่ไม่มีในตาราง
          $columns = $this->db->list_fields('tbl_member_public');
          foreach (array_keys($member_data) as $key) {
            if (!in_array($key, $columns)) {
              unset($member_data[$key]);
            }
          }

          // เพิ่มข้อมูลกลับเข้าตาราง member_public
          $insert = $this->db->insert('tbl_member_public', $member_data);
          if (!$insert) {
            throw new Exception("ไม่สามารถเพิ่มข้อมูลสมาชิกภายนอกได้");
          }

          // เก็บ ID ที่เพิ่งถูกสร้าง
          $new_member_id = $this->db->insert_id();

        } else {
          throw new Exception("ไม่รองรับการกู้คืนข้อมูลจากตาราง {$table_name}");
        }

        // ลบข้อมูลออกจากตาราง log_delete
        $delete_log = $this->db->delete('tbl_log_delete', ['log_id' => $log_id]);
        if (!$delete_log) {
          throw new Exception("ไม่สามารถลบข้อมูล log เก่าได้");
        }

        // บันทึก log การกู้คืน
        $restore_log = array(
          'table_name' => $table_name,
          'action' => 'restore',
          'row_id' => $new_member_id,
          'restored_by' => $this->session->userdata('m_fname'),
          'restored_data' => json_encode($member_data)
        );

        $insert_restore_log = $this->db->insert('tbl_log_restore', $restore_log);
        if (!$insert_restore_log) {
          throw new Exception("ไม่สามารถบันทึก log การกู้คืนได้");
        }

        // เสร็จสิ้น transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
          $this->session->set_flashdata('restore_error', TRUE);
        } else {
          $this->session->set_flashdata('restore_success', TRUE);
        }
      } catch (Exception $e) {
        $this->db->trans_rollback();
        $this->session->set_flashdata('restore_error', TRUE);
        $this->session->set_flashdata('error_message', $e->getMessage());
      }
    } else {
      $this->session->set_flashdata('restore_error', TRUE);
    }

    redirect('System_member/delete_log');
  }



  // เพิ่มฟังก์ชันใน Model (member_model.php)
  public function count_filtered_members_10($search = null, $status = null)
  {
    $this->db->from('tbl_member m');
    $this->db->where("FIND_IN_SET('20', m.grant_system_ref_id) >", 0);

    if ($search) {
      $this->db->group_start();
      $this->db->like('m.m_fname', $search);
      $this->db->or_like('m.m_lname', $search);
      $this->db->or_like('m.m_email', $search);
      $this->db->group_end();
    }

    if ($status) {
      $this->db->where('m.m_status', $status);
    }

    return $this->db->count_all_results();
  }

























  public function check_email_available()
  {
    $email = $this->input->post('email');

    // Validate email format first
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo json_encode([
        'available' => false,
        'message' => 'รูปแบบอีเมลไม่ถูกต้อง'
      ]);
      return;
    }

    // Check if email exists in database
    $exists = $this->member_model->check_email_exists($email);

    echo json_encode([
      'available' => !$exists,
      'message' => $exists ? 'อีเมลนี้มีผู้ใช้งานแล้ว' : 'อีเมลนี้สามารถใช้งานได้'
    ]);
  }



  public function toggle_status_car()
  {
    if (!$this->input->is_ajax_request()) {
      exit('No direct script access allowed');
    }

    $member_id = $this->input->post('member_id');
    $status_type = $this->input->post('status_type'); // 'head' or 'user'
    $status = $this->input->post('status');

    // Get the system limit
    $system_limit = $this->db->get_where('tbl_grant_system', ['grant_system_id' => 5])->row()->grant_system_limit;

    // Count current active users (both head and regular users)
    $current_active = $this->db->where('is_head = 1 OR is_user = 1', NULL, FALSE)
      ->count_all_results('tbl_member_car');

    // Check limit only when activating
    if ($status == 1 && $current_active >= $system_limit) {
      echo json_encode([
        'success' => false,
        'message' => "ไม่สามารถเปิดใช้งานได้ เนื่องจากเกินจำนวนที่กำหนด ($system_limit คน)",
        'limit_reached' => true
      ]);
      return;
    }

    $success = $this->member_model->toggle_status_car($member_id, $status_type, $status);

    echo json_encode([
      'success' => $success,
      'message' => $success ? 'อัพเดทสถานะสำเร็จ' : 'เกิดข้อผิดพลาดในการอัพเดทสถานะ'
    ]);
  }

  public function report_error()
  {
    $description = $this->input->post('description');
    $page = $this->input->post('page');
    $timestamp = $this->input->post('timestamp');

    // บันทึกข้อมูลลงในฐานข้อมูลหรือไฟล์ log
    $log_data = array(
      'description' => $description,
      'page' => $page,
      'timestamp' => $timestamp,
      'user_id' => $this->session->userdata('m_id'),
      'user_agent' => $this->input->user_agent(),
      'ip_address' => $this->input->ip_address()
    );

    // บันทึกลง log file
    log_message('error', 'User Report: ' . json_encode($log_data));

    // ส่ง email แจ้งผู้ดูแลระบบ (ถ้าต้องการ)

    echo json_encode(['success' => true]);
  }

  public function member_backoffice()
  {
    // เช็คสิทธิ์การเข้าถึง
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
      redirect('User/login');
    }

    // Get filter parameters
    $search = $this->input->get('search');
    $status = $this->input->get('status');
    $page = $this->input->get('page') ? $this->input->get('page') : 0;

    // Pagination config
    $config['base_url'] = site_url('System_member/member_backoffice');
    $config['total_rows'] = $this->member_model->count_filtered_members_3($search, $status);
    $config['per_page'] = 20;
    $config['page_query_string'] = TRUE;
    $config['query_string_segment'] = 'page';
    $config['reuse_query_string'] = TRUE;

    // Pagination styling
    $config['full_tag_open'] = '<div class="flex items-center space-x-2">';
    $config['full_tag_close'] = '</div>';

    // ปุ่มก่อนหน้า
    $config['prev_link'] = '<i class="fas fa-chevron-left"></i>';
    $config['prev_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['prev_tag_close'] = '</button>';

    // ปุ่มถัดไป
    $config['next_link'] = '<i class="fas fa-chevron-right"></i>';
    $config['next_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['next_tag_close'] = '</button>';

    // ตัวเลขหน้า
    $config['num_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['num_tag_close'] = '</button>';

    // หน้าปัจจุบัน  
    $config['cur_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded">';
    $config['cur_tag_close'] = '</button>';

    // First & Last
    $config['first_link'] = '<i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left ml-[-0.5rem]"></i>';
    $config['first_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['first_tag_close'] = '</button>';

    $config['last_link'] = '<i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right ml-[-0.5rem]"></i>';
    $config['last_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['last_tag_close'] = '</button>';

    $this->pagination->initialize($config);

    // Calculate rows
    $start_row = $page + 1;
    $end_row = min($page + $config['per_page'], $config['total_rows']);

    // Get data for table
    $data['members'] = $this->member_model->get_filtered_members_3($search, $status, $config['per_page'], $page);

    // Get additional data
    $data['pagination'] = $this->pagination->create_links();
    $data['total_rows'] = $config['total_rows'];
    $data['start_row'] = $start_row;
    $data['end_row'] = $end_row;

    // ข้อมูลสำหรับการแสดงผลเพิ่มเติม
    $data['search'] = $search;
    $data['status'] = $status;
    $data['user_system'] = $this->session->userdata('m_system');

    // Load views
    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/member_backoffice', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
  }






  public function toggle_member_status()
  {
    header('Content-Type: application/json');

    try {
      // ตรวจสอบการเข้าถึง (เฉพาะผู้ดูแลระบบ)
      if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        throw new Exception('ไม่มีสิทธิ์ดำเนินการ');
      }

      // รับค่าจาก request
      $member_id = $this->input->post('member_id');
      $status = $this->input->post('status');

      // บันทึก logs สำหรับการ debug
      log_message('debug', 'Toggle member status: ID=' . $member_id . ', Status=' . $status);

      if (!$member_id) {
        throw new Exception('ไม่พบรหัสสมาชิก');
      }

      // ตรวจสอบว่าไม่ได้กำลังจะปิดการใช้งานตัวเอง
      if ($member_id == $this->session->userdata('m_id') && $status == 0) {
        throw new Exception('ไม่สามารถปิดการใช้งานบัญชีของตัวเองได้');
      }

      // ตรวจสอบว่าถ้าเป็น super_admin ไม่สามารถเปลี่ยนสถานะของ system_admin ได้
      if ($this->session->userdata('m_system') == 'super_admin') {
        $member = $this->db->where('m_id', $member_id)->get('tbl_member')->row();
        if ($member && $member->m_system == 'system_admin') {
          throw new Exception('ไม่มีสิทธิ์เปลี่ยนสถานะของผู้ดูแลระบบสูงสุด');
        }
      }

      // อัพเดทสถานะ
      $update_data = [
        'm_status' => $status
      ];

      // ถ้ามีฟิลด์ m_lastupdate ในตาราง
      if ($this->db->field_exists('m_lastupdate', 'tbl_member')) {
        $update_data['m_lastupdate'] = date('Y-m-d H:i:s');
      }

      $this->db->where('m_id', $member_id)
        ->update('tbl_member', $update_data);

      // ตรวจสอบว่าอัพเดทสำเร็จหรือไม่
      $affected_rows = $this->db->affected_rows();
      if ($affected_rows == 0) {
        // อาจจะเป็นเพราะสถานะเดิมตรงกับสถานะใหม่ หรือไม่พบข้อมูล
        $member = $this->db->where('m_id', $member_id)->get('tbl_member')->row();
        if (!$member) {
          throw new Exception('ไม่พบข้อมูลสมาชิก');
        }
        // ถ้าสถานะไม่ตรงกับที่ส่งมา แสดงว่ามีปัญหา
        if ($member->m_status != $status) {
          throw new Exception('ไม่สามารถอัพเดทสถานะได้');
        }
      }

      // บันทึกประวัติการเปลี่ยนสถานะ (ถ้ามีตาราง log)
      if (method_exists($this, 'user_log_model') && method_exists($this->user_log_model, 'add_log')) {
        $this->user_log_model->add_log([
          'action' => 'toggle_status',
          'detail' => 'Toggle member status to ' . ($status == 1 ? 'active' : 'inactive'),
          'member_id' => $member_id,
          'by_user' => $this->session->userdata('m_id')
        ]);
      }

      echo json_encode([
        'success' => true,
        'message' => 'อัพเดทสถานะเรียบร้อยแล้ว'
      ]);

    } catch (Exception $e) {
      log_message('error', 'Toggle member status error: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }




  public function site_map()
  {
    if ($this->session->userdata('m_id') == '') {
      redirect('User/login');
    }

    // เช็คว่าเป็น system_admin หรือไม่
    $data['is_system_admin'] = ($this->session->userdata('m_system') === 'system_admin');

    // ดึงข้อมูลโมดูลทั้งหมด
    $sql = "SELECT 
    mm.id,
    mm.name,
    mm.code,
    mm.description,
    mm.status,
    mm.is_trial,
    mm.display_order,
    (SELECT COUNT(DISTINCT ms.m_id)
     FROM tbl_member_systems ms
     WHERE ms.module_id = mm.id) as user_count
FROM tbl_member_modules mm
ORDER BY mm.display_order ASC, mm.id ASC";

    $data['modules'] = $this->db->query($sql)->result();

    // Pagination config
    $config['base_url'] = base_url('System_member/site_map');
    $config['total_rows'] = count($data['modules']);
    $config['per_page'] = 10;

    // Pagination styling
    $config['full_tag_open'] = '<div class="flex items-center space-x-2">';
    $config['full_tag_close'] = '</div>';

    // First & Last links
    $config['first_link'] = '<i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left ml-[-0.5rem]"></i>';
    $config['first_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['first_tag_close'] = '</button>';

    $config['last_link'] = '<i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right ml-[-0.5rem]"></i>';
    $config['last_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['last_tag_close'] = '</button>';

    // Next & Prev links
    $config['next_link'] = '<i class="fas fa-chevron-right"></i>';
    $config['next_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['next_tag_close'] = '</button>';

    $config['prev_link'] = '<i class="fas fa-chevron-left"></i>';
    $config['prev_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['prev_tag_close'] = '</button>';

    // Current page
    $config['cur_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded">';
    $config['cur_tag_close'] = '</button>';

    // Number links
    $config['num_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['num_tag_close'] = '</button>';

    $this->pagination->initialize($config);

    $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

    // คำนวณข้อมูลการแสดงผล
    $data['start_row'] = $page + 1;
    $data['end_row'] = min($page + $config['per_page'], $config['total_rows']);
    $data['total_rows'] = $config['total_rows'];
    $data['pagination'] = $this->pagination->create_links();

    // Load views
    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/site_map', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
  }



  public function toggle_module_status()
  {
    header('Content-Type: application/json; charset=utf-8');

    try {
      if (
        !$this->session->userdata('m_id') ||
        $this->session->userdata('m_system') !== 'system_admin'
      ) {
        throw new Exception('ไม่มีสิทธิ์ดำเนินการ');
      }

      $module_id = $this->input->post('module_id');
      $type = $this->input->post('type');
      $status = $this->input->post('status');

      log_message('debug', 'Input data - module_id: ' . $module_id . ', type: ' . $type . ', status: ' . $status);

      if (!$module_id || !$type || !isset($status)) {
        throw new Exception('ข้อมูลไม่ครบถ้วน');
      }

      $this->db->trans_start();

      // ตรวจสอบข้อมูลปัจจุบัน
      $existing_module = $this->db->get_where('tbl_member_modules', ['id' => $module_id])->row();
      if (!$existing_module) {
        throw new Exception('ไม่พบข้อมูลโมดูล');
      }

      $update_data = [
        'updated_at' => date('Y-m-d H:i:s'),
        'updated_by' => $this->session->userdata('m_id')
      ];


      if ($type === 'trial') {
        $update_data['is_trial'] = $status ? 0 : 1;  // กลับค่า status
      } else {
        $update_data['status'] = $status;
      }

      log_message('debug', 'Update data: ' . json_encode($update_data));

      $this->db->where('id', $module_id);
      $result = $this->db->update('tbl_member_modules', $update_data);

      log_message('debug', 'Last query: ' . $this->db->last_query());

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('การอัพเดทข้อมูลล้มเหลว');
      }

      // ดึงข้อมูลหลังอัพเดทเพื่อตรวจสอบ
      $updated_module = $this->db->get_where('tbl_member_modules', ['id' => $module_id])->row();

      $message = $type === 'trial' ?
        ($status ? 'เปลี่ยนเป็น Full Version เรียบร้อยแล้ว' : 'เปลี่ยนเป็น Trial Version เรียบร้อยแล้ว') :
        ($status ? 'เปิดการใช้งานเรียบร้อยแล้ว' : 'ปิดการใช้งานเรียบร้อยแล้ว');

      echo json_encode([
        'success' => true,
        'message' => $message,
        'updated_data' => $updated_module
      ]);

    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Toggle module status error: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }






  private function manage_display_order($new_order, $current_id = null)
  {
    // ถ้ามีโมดูลที่มี display_order เท่ากัน ให้เลื่อนลำดับถัดไปขึ้นไป
    $this->db->trans_start();

    if ($current_id) {
      // กรณีแก้ไข
      $this->db->where('display_order >=', $new_order)
        ->where('id !=', $current_id)
        ->set('display_order', 'display_order + 1', false)
        ->update('tbl_member_modules');
    } else {
      // กรณีสร้างใหม่
      $this->db->where('display_order >=', $new_order)
        ->set('display_order', 'display_order + 1', false)
        ->update('tbl_member_modules');
    }

    $this->db->trans_complete();
  }





  public function create_module()
  {
    header('Content-Type: application/json');

    if (
      !$this->session->userdata('m_id') ||
      $this->session->userdata('m_system') !== 'system_admin'
    ) {
      echo json_encode([
        'success' => false,
        'message' => 'ไม่มีสิทธิ์ดำเนินการ'
      ]);
      return;
    }

    try {
      $name = $this->input->post('name');
      $code = $this->input->post('code');
      $description = $this->input->post('description');
      $display_order = $this->input->post('display_order');

      // Log input data
      log_message('debug', 'Create module input: ' . json_encode($_POST));

      if (empty($name) || empty($code)) {
        throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
      }

      // เช็คว่ามี code ซ้ำหรือไม่
      $existing = $this->db->where('code', $code)->get('tbl_member_modules')->row();
      if ($existing) {
        throw new Exception('รหัสระบบนี้มีอยู่แล้ว');
      }

      $data = [
        'name' => $name,
        'code' => $code,
        'description' => $description,
        'display_order' => $display_order ? $display_order : 0,
        'status' => 1,
        'is_trial' => 1,
        'created_by' => $this->session->userdata('m_id'),
        'updated_by' => $this->session->userdata('m_id')
      ];

      $this->db->trans_start();
      $this->db->insert('tbl_member_modules', $data);
      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('ไม่สามารถบันทึกข้อมูลได้');
      }

      echo json_encode([
        'success' => true,
        'message' => 'สร้างระบบใหม่เรียบร้อยแล้ว'
      ]);

    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Create module error: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }




  public function update_module()
  {
    header('Content-Type: application/json');

    if (
      !$this->session->userdata('m_id') ||
      $this->session->userdata('m_system') !== 'system_admin'
    ) {
      echo json_encode([
        'success' => false,
        'message' => 'ไม่มีสิทธิ์ดำเนินการ'
      ]);
      return;
    }

    try {
      $id = $this->input->post('id');
      $name = $this->input->post('name');
      $code = $this->input->post('code');
      $description = $this->input->post('description');
      $display_order = $this->input->post('display_order');

      if (!$id || !$name || !$code || !$display_order) {
        throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
      }

      // ตรวจสอบว่ามีรหัสซ้ำหรือไม่ (ยกเว้นรหัสของตัวเอง)
      $exists = $this->db->where('code', $code)
        ->where('id !=', $id)
        ->get('tbl_member_modules')
        ->num_rows();
      if ($exists > 0) {
        throw new Exception('รหัสระบบนี้มีอยู่แล้ว');
      }

      // ตรวจสอบค่า display_order ต้องเป็นตัวเลขมากกว่า 0
      if (!is_numeric($display_order) || $display_order < 1) {
        throw new Exception('ลำดับการแสดงผลต้องเป็นตัวเลขมากกว่า 0');
      }

      $data = [
        'name' => $name,
        'code' => $code,
        'description' => $description,
        'display_order' => $display_order,
        'updated_by' => $this->session->userdata('m_id'),
        'updated_at' => date('Y-m-d H:i:s')
      ];

      $this->db->trans_start();
      $this->db->where('id', $id)->update('tbl_member_modules', $data);
      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('ไม่สามารถบันทึกข้อมูลได้');
      }

      echo json_encode([
        'success' => true,
        'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว'
      ]);

    } catch (Exception $e) {
      $this->db->trans_rollback();
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }




  private function checkModuleUsage($module_id)
  {
    // 1. ตรวจสอบการใช้งานในเมนู
    $menu_count = $this->db
      ->from('tbl_member_module_menus')
      ->where('module_id', $module_id)
      ->count_all_results();

    if ($menu_count > 0) {
      // หาว่ามีผู้ใช้งานที่มีสิทธิ์ในเมนูนี้หรือไม่
      $users_with_permissions = $this->db
        ->select('DISTINCT tbl_member_user_permissions.member_id')
        ->from('tbl_member_user_permissions')
        ->join('tbl_member_module_menus', 'tbl_member_user_permissions.system_id = tbl_member_module_menus.id')
        ->where('tbl_member_module_menus.module_id', $module_id)
        ->get()
        ->num_rows();

      if ($users_with_permissions > 0) {
        return [
          'can_delete' => false,
          'message' => 'ไม่สามารถลบได้ เนื่องจากมีผู้ใช้งานที่มีสิทธิ์ในระบบนี้'
        ];
      }
    }

    // 2. ตรวจสอบการใช้งานในระบบ
    $system_count = $this->db
      ->from('tbl_member_systems')
      ->where('module_id', $module_id)
      ->count_all_results();

    if ($system_count > 0) {
      return [
        'can_delete' => false,
        'message' => 'ไม่สามารถลบได้ เนื่องจากมีการใช้งานระบบนี้อยู่'
      ];
    }

    return ['can_delete' => true];
  }

  public function delete_module($id)
  {
    header('Content-Type: application/json');

    try {
      // 1. ตรวจสอบสิทธิ์
      if (
        !$this->session->userdata('m_id') ||
        $this->session->userdata('m_system') !== 'system_admin'
      ) {
        throw new Exception('ไม่มีสิทธิ์ดำเนินการ');
      }

      // 2. ตรวจสอบว่ามีโมดูลนี้อยู่จริง
      $module = $this->db->where('id', $id)
        ->get('tbl_member_modules')
        ->row();

      if (!$module) {
        throw new Exception('ไม่พบข้อมูลระบบที่ต้องการลบ');
      }

      // 3. ตรวจสอบการใช้งานกับ tbl_member_systems
      $systems_using = $this->db->where('module_id', $id)
        ->count_all_results('tbl_member_systems');

      if ($systems_using > 0) {
        throw new Exception('ไม่สามารถลบได้ เนื่องจากมีการใช้งานในระบบอยู่');
      }

      // 4. หา menu_ids ที่เกี่ยวข้อง
      $menu_ids = $this->db->select('id')
        ->where('module_id', $id)
        ->get('tbl_member_module_menus')
        ->result_array();

      $menu_ids = array_column($menu_ids, 'id');

      // 5. ตรวจสอบการใช้งานใน permissions
      if (!empty($menu_ids)) {
        $permissions_using = $this->db->where_in('system_id', $menu_ids)
          ->count_all_results('tbl_member_user_permissions');

        if ($permissions_using > 0) {
          throw new Exception('ไม่สามารถลบได้ เนื่องจากมีการกำหนดสิทธิ์การใช้งานอยู่');
        }
      }

      $this->db->trans_start();

      try {
        // 6. เก็บ log ก่อนลบ
        $log_data = [
          'module_id' => $module->id,
          'module_name' => $module->name,
          'module_code' => $module->code,
          'deleted_by' => $this->session->userdata('m_id'),
          'deleted_at' => date('Y-m-d H:i:s'),
          'module_data' => json_encode($module)
        ];

        $this->db->insert('tbl_member_modules_deleted_log', $log_data);

        // 7. ลบ permissions ถ้ามี
        if (!empty($menu_ids)) {
          $this->db->where_in('system_id', $menu_ids)
            ->delete('tbl_member_user_permissions');
        }

        // 8. ลบ menus
        $this->db->where('module_id', $id)
          ->delete('tbl_member_module_menus');

        // 9. ลบ module
        $this->db->where('id', $id)
          ->delete('tbl_member_modules');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
          throw new Exception('การลบข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        }

        echo json_encode([
          'success' => true,
          'message' => 'ลบข้อมูลเรียบร้อยแล้ว'
        ]);

      } catch (Exception $e) {
        $this->db->trans_rollback();
        throw $e;
      }

    } catch (Exception $e) {
      log_message('error', 'Delete module error: ' . $e->getMessage());

      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }


  public function save_module()
  {
    header('Content-Type: application/json; charset=utf-8');

    if (
      !$this->session->userdata('m_id') ||
      $this->session->userdata('m_system') !== 'system_admin'
    ) {
      echo json_encode([
        'success' => false,
        'message' => 'ไม่มีสิทธิ์ดำเนินการ'
      ]);
      return;
    }

    try {
      $this->db->trans_start();

      // รับค่าจาก form
      $id = $this->input->post('id');
      $name = $this->input->post('name');
      $code = $this->input->post('code');
      $description = $this->input->post('description');
      $display_order = $this->input->post('display_order');
      $menus_json = $this->input->post('menus');

      // Debug log
      log_message('debug', 'Save Module - POST data: ' . json_encode($_POST));

      // Validate required fields
      if (empty($name) || empty($code) || empty($display_order)) {
        throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
      }

      // เตรียมข้อมูลโมดูล
      $module_data = [
        'name' => $name,
        'code' => $code,
        'description' => $description,
        'display_order' => $display_order,
        'updated_by' => $this->session->userdata('m_id'),
        'updated_at' => date('Y-m-d H:i:s')
      ];

      $module_id = $id;

      if ($id) { // กรณีแก้ไข
        // อัพเดทโมดูล
        $this->db->where('id', $id)
          ->update('tbl_member_modules', $module_data);
      } else {
        // เพิ่มโมดูลใหม่
        $module_data['created_by'] = $this->session->userdata('m_id');
        $module_data['status'] = 1;
        $module_data['is_trial'] = 1;

        $this->db->insert('tbl_member_modules', $module_data);
        $module_id = $this->db->insert_id();
      }

      // แปลง JSON string เป็น array
      $menus = json_decode($menus_json, true);

      if (empty($menus)) {
        $menus = []; // ป้องกัน null
      }

      // ดึงเมนูเดิมทั้งหมดเพื่อเปรียบเทียบ
      $existing_menus = $this->db->where('module_id', $module_id)
        ->get('tbl_member_module_menus')
        ->result_array();

      $existing_menu_ids = []; // เก็บ ID ที่มีอยู่
      $existing_menu_codes = []; // เก็บ code ที่มีอยู่

      foreach ($existing_menus as $existing) {
        $existing_menu_ids[$existing['id']] = $existing;
        $existing_menu_codes[$existing['code']] = $existing['id'];
      }

      $processed_ids = []; // เก็บ ID ที่ได้ดำเนินการแล้ว

      // จัดการเมนู - อัพเดทหรือเพิ่มใหม่
      foreach ($menus as $menu) {
        $menu_id = isset($menu['id']) ? $menu['id'] : null;

        $menu_data = [
          'module_id' => $module_id,
          'name' => $menu['name'],
          'code' => $menu['code'],
          'url' => isset($menu['url']) ? $menu['url'] : '',
          'icon' => isset($menu['icon']) ? $menu['icon'] : '',
          'parent_id' => !empty($menu['parent_code']) ? $menu['parent_code'] : null,
          'display_order' => isset($menu['display_order']) ? $menu['display_order'] : 0,
          'status' => isset($menu['status']) ? $menu['status'] : 1,
          'updated_by' => $this->session->userdata('m_id'),
          'updated_at' => date('Y-m-d H:i:s')
        ];

        // กรณีมี ID และมีอยู่ในระบบ (แก้ไข)
        if ($menu_id && isset($existing_menu_ids[$menu_id])) {
          $this->db->where('id', $menu_id)
            ->update('tbl_member_module_menus', $menu_data);

          $processed_ids[] = $menu_id;
        }
        // กรณีมี code ที่ตรงกับเมนูเดิม (อัพเดทแทนการสร้างใหม่)
        else if (isset($existing_menu_codes[$menu['code']])) {
          $existing_id = $existing_menu_codes[$menu['code']];

          $this->db->where('id', $existing_id)
            ->update('tbl_member_module_menus', $menu_data);

          $processed_ids[] = $existing_id;
        }
        // กรณีเป็นเมนูใหม่
        else {
          $menu_data['created_by'] = $this->session->userdata('m_id');
          $menu_data['created_at'] = date('Y-m-d H:i:s');

          $this->db->insert('tbl_member_module_menus', $menu_data);
          $new_id = $this->db->insert_id();

          $processed_ids[] = $new_id;
        }
      }

      // ลบเมนูที่ไม่ได้ส่งมาแล้ว
      $delete_ids = array_diff(array_keys($existing_menu_ids), $processed_ids);

      if (!empty($delete_ids)) {
        $this->db->where_in('id', $delete_ids)
          ->delete('tbl_member_module_menus');

        // ลบ permissions ที่เกี่ยวข้องกับเมนูที่ถูกลบ
        $this->db->where_in('system_id', $delete_ids)
          ->delete('tbl_member_user_permissions');

        log_message('info', 'Deleted menu IDs: ' . json_encode($delete_ids));
      }

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('ไม่สามารถบันทึกข้อมูลได้');
      }

      echo json_encode([
        'success' => true,
        'message' => ($id ? 'แก้ไขข้อมูล' : 'เพิ่มข้อมูล') . 'เรียบร้อยแล้ว',
        'module_id' => $module_id
      ]);

    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Save module error: ' . $e->getMessage());
      log_message('error', 'Last query: ' . $this->db->last_query());

      echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
      ]);
    }
  }

  // ฟังก์ชันสำหรับดึงข้อมูลโมดูลและเมนู
  public function get_module($id)
  {
    header('Content-Type: application/json');

    try {
      // ดึงข้อมูลโมดูล
      $this->db->select('*');
      $this->db->from('tbl_member_modules');
      $this->db->where('id', $id);
      $module = $this->db->get()->row();

      if (!$module) {
        throw new Exception('ไม่พบข้อมูลระบบ');
      }

      // ดึงข้อมูลเมนูทั้งหมดของโมดูล
      $this->db->select('menu.*, parent.name as parent_menu_name');
      $this->db->from('tbl_member_module_menus as menu');
      $this->db->join(
        'tbl_member_module_menus as parent',
        'menu.parent_id = parent.code',
        'left'
      );
      $this->db->where('menu.module_id', $id);
      $this->db->order_by('menu.display_order', 'ASC');

      $menus = $this->db->get()->result();

      // Log for debugging
      log_message('debug', 'Module data: ' . json_encode($module));
      log_message('debug', 'Menus data: ' . json_encode($menus));

      $formatted_menus = [];
      foreach ($menus as $menu) {
        $formatted_menus[] = [
          'id' => $menu->id,
          'name' => $menu->name,
          'code' => $menu->code,
          'url' => $menu->url,
          'icon' => $menu->icon,
          'parent_id' => $menu->parent_id,
          'parent_name' => $menu->parent_menu_name,
          'display_order' => $menu->display_order,
          'status' => (bool) $menu->status
        ];
      }

      echo json_encode([
        'success' => true,
        'data' => [
          'module' => $module,
          'menus' => $formatted_menus
        ]
      ]);

    } catch (Exception $e) {
      log_message('error', 'Error in get_module: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }

  // ฟังก์ชันโหลดข้อมูลเมนูเดิม (กรณีแก้ไข)
  private function load_existing_menus($moduleId)
  {
    $menuHtml = '';
    $menus = $this->db->where('module_id', $moduleId)
      ->order_by('display_order', 'ASC')
      ->get('tbl_member_module_menus')
      ->result();

    foreach ($menus as $menu) {
      $menuHtml .= $this->load->view('member/menu_item', [
        'menu' => $menu,
        'parent_menus' => $this->get_available_parent_menus($moduleId, $menu->id)
      ], true);
    }

    return $menuHtml;
  }



  public function get_available_parent_menus()
  {
    header('Content-Type: application/json');

    try {
      $module_id = $this->input->get('module_id');
      $exclude_id = $this->input->get('exclude_id');

      if (!$module_id) {
        throw new Exception('กรุณาระบุ Module ID');
      }

      $this->db->select('id, name, code');
      $this->db->from('tbl_member_module_menus');
      $this->db->where('module_id', $module_id);

      // ไม่ดึงตัวเองและเมนูย่อยของตัวเอง
      if ($exclude_id) {
        $this->db->where('id !=', $exclude_id);

        // ดึง child menus ทั้งหมดของเมนูที่กำลังแก้ไข
        $child_menus = $this->get_all_child_menus($exclude_id);
        if (!empty($child_menus)) {
          $this->db->where_not_in('id', $child_menus);
        }
      }

      $menus = $this->db->get()->result();

      echo json_encode([
        'success' => true,
        'data' => $menus
      ]);

    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }

  private function get_all_child_menus($parent_id)
  {
    $child_ids = [];

    $children = $this->db->where('parent_id', $parent_id)
      ->get('tbl_member_module_menus')
      ->result();

    foreach ($children as $child) {
      $child_ids[] = $child->id;
      $grandchildren = $this->get_all_child_menus($child->id);
      $child_ids = array_merge($child_ids, $grandchildren);
    }

    return $child_ids;
  }


  public function module($code = NULL)
  {
    // เช็คการล็อกอิน
    if (!$this->session->userdata('m_id')) {
      redirect('User/login');
    }

    // เช็คสิทธิ์การเข้าถึง
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
      $this->session->set_flashdata('error', 'ไม่มีสิทธิ์เข้าถึงหน้านี้');
      redirect('System_member');
    }

    try {
      // ถ้าไม่มี code ให้ redirect ไป site_map
      if (!$code) {
        redirect('System_member/site_map');
      }

      // ดึงข้อมูลโมดูล
      $module = $this->db->select('*')
        ->from('tbl_member_modules')
        ->where('code', $code)
        ->get()
        ->row();

      if (!$module) {
        throw new Exception('ไม่พบข้อมูลโมดูล');
      }

      // ดึงข้อมูลเมนูของโมดูล
      $module_menus = $this->db->select('mmm.*, COUNT(mup.id) as user_count')
        ->from('tbl_member_module_menus mmm')
        ->join('tbl_member_user_permissions mup', 'mmm.id = mup.system_id', 'left')
        ->where('mmm.module_id', $module->id)
        ->where('mmm.status', 1)
        ->group_by('mmm.id')
        ->order_by('mmm.display_order', 'ASC')
        ->get()
        ->result();

      // ดึงข้อมูลผู้ใช้ที่มีสิทธิ์ในโมดูลนี้ 
      $module_users = $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, 
                              GROUP_CONCAT(DISTINCT mmm.name) as permissions')
        ->from('tbl_member_user_permissions mup')
        ->join('tbl_member m', 'm.m_id = mup.member_id')
        ->join('tbl_member_module_menus mmm', 'mup.system_id = mmm.id')
        ->where('mmm.module_id', $module->id)
        ->where('mup.is_active', 1)
        ->group_by('m.m_id, m.m_fname, m.m_lname, m.m_email')
        ->get()
        ->result();

      // นับจำนวนผู้ใช้งานทั้งหมด
      $total_users = count($module_users);

      // ดึง system limit
      $module_data = $this->db->select('id, name, status')
        ->where('id', $module->id)
        ->where('status', 1)
        ->get('tbl_member_modules')
        ->row();

      // จัดเตรียมข้อมูลสำหรับ view
      $data = [
        'module' => $module,
        'module_menus' => $module_menus,
        'module_users' => $module_users,
        'total_users' => $total_users,
        // ถ้าต้องการจำกัดจำนวนผู้ใช้งาน สามารถกำหนดค่าเริ่มต้นได้ เช่น 100 users
        'system_limit' => 100,
        'remaining_slots' => 100 - $total_users,
        'is_system_admin' => ($this->session->userdata('m_system') === 'system_admin')
      ];

      // Load views
      $this->load->view('member/header');
      $this->load->view('member/css');
      $this->load->view('member/sidebar');
      $this->load->view('member/module', $data);
      $this->load->view('member/js');
      $this->load->view('member/footer');

    } catch (Exception $e) {
      log_message('error', 'Module error: ' . $e->getMessage());
      $this->session->set_flashdata('error', $e->getMessage());
      redirect('System_member/site_map');
    }
  }



  // บันทึกการเปลี่ยนแปลงสิทธิ์ของโมดูล
  public function save_module_permission()
  {
    header('Content-Type: application/json');

    try {
      if (
        !$this->session->userdata('m_id') ||
        !in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])
      ) {
        throw new Exception('ไม่มีสิทธิ์ดำเนินการ');
      }

      $module_id = $this->input->post('module_id');
      $module_active = $this->input->post('module_active');
      $menu_permissions = $this->input->post('menu_permissions');

      $this->db->trans_start();

      // อัพเดทสถานะโมดูล
      $this->db->where('id', $module_id)
        ->update('tbl_member_modules', [
          'status' => $module_active ? 1 : 0,
          'updated_by' => $this->session->userdata('m_id'),
          'updated_at' => date('Y-m-d H:i:s')
        ]);

      // อัพเดทสิทธิ์เมนู
      if ($menu_permissions) {
        // ลบสิทธิ์เก่า
        $this->db->where('module_id', $module_id)
          ->update('tbl_member_module_menus', ['status' => 0]);

        // เพิ่มสิทธิ์ใหม่
        $this->db->where_in('id', $menu_permissions)
          ->update('tbl_member_module_menus', ['status' => 1]);
      }

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
      }

      echo json_encode([
        'success' => true,
        'message' => 'บันทึกการเปลี่ยนแปลงเรียบร้อยแล้ว'
      ]);

    } catch (Exception $e) {
      $this->db->trans_rollback();
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }







  public function get_available_users()
  {
    header('Content-Type: application/json; charset=utf-8');

    try {
      $module_id = $this->input->get('module_id');

      if (!$module_id) {
        throw new Exception('ไม่พบรหัสโมดูล');
      }

      // บันทึก log เพื่อการ debug
      log_message('debug', 'Fetching available users for module_id: ' . $module_id);

      // ดึงข้อมูลจากตาราง tbl_member_systems เป็นหลัก (ไม่ใช่ tbl_member_user_permissions)
      $assigned_users = $this->db->select('m_id')
        ->from('tbl_member_systems')
        ->where('module_id', $module_id)
        ->get()
        ->result_array();

      $assigned_user_ids = array_column($assigned_users, 'm_id');

      // ดึงผู้ใช้ทั้งหมดที่ไม่ได้อยู่ในโมดูลนี้
      $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email');
      $this->db->from('tbl_member m');
      $this->db->where('m.m_status', 1); // เฉพาะผู้ใช้ที่เปิดใช้งาน

      if (!empty($assigned_user_ids)) {
        $this->db->where_not_in('m.m_id', $assigned_user_ids);
      }

      $users = $this->db->get()->result();

      log_message('debug', 'Found ' . count($users) . ' available users, SQL: ' . $this->db->last_query());

      echo json_encode([
        'success' => true,
        'data' => $users
      ]);

    } catch (Exception $e) {
      log_message('error', 'Get available users error: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }




  public function import_module_users()
  {
    header('Content-Type: application/json; charset=utf-8');
    error_reporting(0);

    try {
      $raw_data = file_get_contents('php://input');
      $data = json_decode($raw_data, true);

      // Debug log
      log_message('debug', 'Import data: ' . json_encode($data));

      if (!isset($data['module_id']) || !isset($data['user_ids']) || !isset($data['permissions'])) {
        throw new Exception('ข้อมูลไม่ครบถ้วน');
      }

      $module_id = $data['module_id'];
      $user_ids = $data['user_ids'];
      $permissions = $data['permissions']; // รับค่า permissions จาก frontend

      // ดึงข้อมูลโมดูล
      $module = $this->db->where('id', $module_id)
        ->where('status', 1)
        ->get('tbl_member_modules')
        ->row();

      if (!$module) {
        throw new Exception('ไม่พบข้อมูลโมดูลหรือโมดูลถูกปิดใช้งาน');
      }

      $this->db->trans_start();

      foreach ($user_ids as $user_id) {
        // 1. อัพเดต grant_system_ref_id ในตาราง tbl_member
        $member = $this->db->where('m_id', $user_id)->get('tbl_member')->row();
        if ($member) {
          // ตรวจสอบว่ามี module_id นี้อยู่แล้วหรือไม่
          $current_grants = $member->grant_system_ref_id ? explode(',', $member->grant_system_ref_id) : [];

          if (!in_array($module_id, $current_grants)) {
            // ถ้าไม่มีให้เพิ่มเข้าไป
            $current_grants[] = $module_id;
            // อัพเดตกลับไปที่ตาราง
            $this->db->where('m_id', $user_id)
              ->update('tbl_member', [
                'grant_system_ref_id' => implode(',', $current_grants)
              ]);

            log_message('info', 'Updated grant_system_ref_id for user_id: ' . $user_id . ' adding module_id: ' . $module_id);
          }
        }

        // 2. เพิ่มสิทธิ์การเข้าถึงระบบ (tbl_member_systems)
        $exists = $this->db->where('m_id', $user_id)
          ->where('module_id', $module_id)
          ->get('tbl_member_systems')
          ->num_rows();

        if ($exists == 0) {
          $this->db->insert('tbl_member_systems', [
            'm_id' => $user_id,
            'module_id' => $module_id,
            'status' => 1,
            'name' => $module->name,
            'description' => $module->description,
            'is_active' => 1,
            'created_by' => $this->session->userdata('m_id'),
            'created_at' => date('Y-m-d H:i:s')
          ]);
        }

        // 3. เพิ่มสิทธิ์การใช้งานเมนูตามที่เลือก
        if (!empty($permissions)) {
          foreach ($permissions as $menu_id) {
            $menu = $this->db->where('id', $menu_id)
              ->get('tbl_member_module_menus')
              ->row();

            if ($menu) {
              $exists = $this->db->where('member_id', $user_id)
                ->where('system_id', $menu_id)
                ->get('tbl_member_user_permissions')
                ->num_rows();

              if ($exists == 0) {
                $this->db->insert('tbl_member_user_permissions', [
                  'member_id' => $user_id,
                  'system_id' => $menu_id,
                  'permission_code' => $menu->code,
                  'is_active' => 1,
                  'created_by' => $this->session->userdata('m_id'),
                  'created_at' => date('Y-m-d H:i:s')
                ]);
              }
            }
          }
        }
      }

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('เกิดข้อผิดพลาดในการนำเข้าข้อมูล');
      }

      echo json_encode([
        'success' => true,
        'message' => 'นำเข้าผู้ใช้เรียบร้อยแล้ว'
      ]);

    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Import module users error: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }






  // เพิ่มผู้ใช้ใหม่ในโมดูล
  public function add_module_user()
  {
    header('Content-Type: application/json');

    try {
      $module_id = $this->input->post('module_id');
      $user_id = $this->input->post('user_id');
      $permissions = $this->input->post('permissions');

      // ตรวจสอบข้อมูล
      if (!$module_id || !$user_id || !$permissions) {
        throw new Exception('กรุณาระบุข้อมูลให้ครบถ้วน');
      }

      $this->db->trans_start();

      // เพิ่มผู้ใช้ในโมดูล
      $this->db->insert('tbl_member_systems', [
        'm_id' => $user_id,
        'module_id' => $module_id,
        'status' => 1,
        'created_by' => $this->session->userdata('m_id'),
        'created_at' => date('Y-m-d H:i:s')
      ]);

      // เพิ่มสิทธิ์ของผู้ใช้
      foreach ($permissions as $menu_id) {
        $this->db->insert('tbl_member_user_permissions', [
          'member_id' => $user_id,
          'system_id' => $menu_id,
          'is_active' => 1,
          'created_by' => $this->session->userdata('m_id'),
          'created_at' => date('Y-m-d H:i:s')
        ]);
      }

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('เกิดข้อผิดพลาดในการเพิ่มผู้ใช้');
      }

      echo json_encode([
        'success' => true,
        'message' => 'เพิ่มผู้ใช้เรียบร้อยแล้ว'
      ]);

    } catch (Exception $e) {
      $this->db->trans_rollback();
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }

  // อัพเดทสิทธิ์ของผู้ใช้ในโมดูล
  public function update_user_permissions()
  {
    header('Content-Type: application/json');

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data['user_id']) || !isset($data['module_id']) || !isset($data['permissions'])) {
        throw new Exception('ข้อมูลไม่ครบถ้วน');
      }

      $user_id = $data['user_id'];
      $module_id = $data['module_id'];
      $permissions = $data['permissions'];

      $this->db->trans_start();

      // 1. ตรวจสอบและคงค่า module_id ใน grant_system_ref_id
      $member = $this->db->where('m_id', $user_id)->get('tbl_member')->row();

      if ($member) {
        // ตรวจสอบว่ามี module_id นี้อยู่แล้วหรือไม่
        $current_grants = $member->grant_system_ref_id ? explode(',', $member->grant_system_ref_id) : [];

        if (!in_array($module_id, $current_grants)) {
          // ถ้าไม่มีให้เพิ่มเข้าไป
          $current_grants[] = $module_id;
          // อัพเดตกลับไปที่ตาราง
          $this->db->where('m_id', $user_id)
            ->update('tbl_member', [
              'grant_system_ref_id' => implode(',', $current_grants)
            ]);

          log_message('info', 'Updated grant_system_ref_id for user_id: ' . $user_id . ' adding module_id: ' . $module_id);
        }
      }

      // 2. ลบสิทธิ์เดิมสำหรับโมดูลนี้
      $menu_ids = $this->db->select('id')
        ->from('tbl_member_module_menus')
        ->where('module_id', $module_id)
        ->get()
        ->result_array();

      if (!empty($menu_ids)) {
        $menu_ids = array_column($menu_ids, 'id');
        $this->db->where('member_id', $user_id)
          ->where_in('system_id', $menu_ids)
          ->delete('tbl_member_user_permissions');
      }

      // 3. เพิ่มสิทธิ์ใหม่ตามที่เลือก
      if (!empty($permissions)) {
        $batch_data = [];
        foreach ($permissions as $menu_id) {
          // Get menu details
          $menu = $this->db->where('id', $menu_id)
            ->get('tbl_member_module_menus')
            ->row();

          if ($menu) {
            $batch_data[] = [
              'member_id' => $user_id,
              'system_id' => $menu_id,
              'permission_code' => $menu->code,
              'is_active' => 1,
              'created_by' => $this->session->userdata('m_id'),
              'created_at' => date('Y-m-d H:i:s')
            ];
          }
        }

        if (!empty($batch_data)) {
          $this->db->insert_batch('tbl_member_user_permissions', $batch_data);
        }
      }

      // 4. ตรวจสอบว่ายังมีสิทธิ์ในโมดูลนี้หรือไม่
      $remaining_permissions = $this->db->where('member_id', $user_id)
        ->where_in('system_id', $menu_ids)
        ->count_all_results('tbl_member_user_permissions');

      // ถ้าไม่มีสิทธิ์ใดๆ เหลืออยู่เลย ให้ลบข้อมูลจาก tbl_member_systems และลบ module_id ออกจาก grant_system_ref_id
      if ($remaining_permissions == 0) {
        // ลบข้อมูลจาก tbl_member_systems
        $this->db->where('m_id', $user_id)
          ->where('module_id', $module_id)
          ->delete('tbl_member_systems');

        // ลบ module_id ออกจาก grant_system_ref_id
        if ($member && !empty($member->grant_system_ref_id)) {
          $grants = explode(',', $member->grant_system_ref_id);
          $grants = array_diff($grants, [$module_id]);

          $this->db->where('m_id', $user_id)
            ->update('tbl_member', [
              'grant_system_ref_id' => implode(',', $grants)
            ]);
        }
      }

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('ไม่สามารถบันทึกข้อมูลได้');
      }

      echo json_encode([
        'success' => true,
        'message' => 'อัพเดทสิทธิ์เรียบร้อยแล้ว'
      ]);

    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Update permissions error: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }




  public function remove_module_user()
  {
    header('Content-Type: application/json');

    try {
      // ตรวจสอบสิทธิ์
      if (!$this->session->userdata('m_id')) {
        throw new Exception('กรุณาเข้าสู่ระบบ');
      }

      // รับค่า input
      $json = file_get_contents('php://input');
      $data = json_decode($json);

      if (!isset($data->user_id) || !isset($data->module_id)) {
        throw new Exception('ข้อมูลไม่ครบถ้วน');
      }

      $user_id = $data->user_id;
      $module_id = $data->module_id;

      // เริ่ม transaction
      $this->db->trans_start();

      try {
        // 1. ลบ module_id ออกจาก grant_system_ref_id ในตาราง tbl_member
        $member = $this->db->where('m_id', $user_id)->get('tbl_member')->row();

        if ($member && !empty($member->grant_system_ref_id)) {
          $grants = explode(',', $member->grant_system_ref_id);

          // ลบ module_id ออกจาก array
          $grants = array_diff($grants, [$module_id]);

          // อัพเดตกลับไปที่ตาราง
          $this->db->where('m_id', $user_id)
            ->update('tbl_member', [
              'grant_system_ref_id' => implode(',', $grants)
            ]);

          log_message('info', 'Removed module_id: ' . $module_id . ' from grant_system_ref_id for user_id: ' . $user_id);
        }

        // 2. ลบข้อมูลจาก tbl_member_user_permissions
        $menu_ids = $this->db->select('id')
          ->from('tbl_member_module_menus')
          ->where('module_id', $module_id)
          ->get()
          ->result_array();

        if (!empty($menu_ids)) {
          $menu_ids = array_column($menu_ids, 'id');
          $this->db->where('member_id', $user_id)
            ->where_in('system_id', $menu_ids)
            ->delete('tbl_member_user_permissions');
        }

        // 3. ลบข้อมูลจาก tbl_member_systems
        $this->db->where('m_id', $user_id)
          ->where('module_id', $module_id)
          ->delete('tbl_member_systems');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
          throw new Exception('เกิดข้อผิดพลาดในการลบข้อมูล');
        }

        echo json_encode([
          'success' => true,
          'message' => 'ลบข้อมูลเรียบร้อยแล้ว'
        ]);

      } catch (Exception $e) {
        $this->db->trans_rollback();
        throw $e;
      }

    } catch (Exception $e) {
      log_message('error', 'Remove module user error: ' . $e->getMessage());

      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }


  public function get_member_systems($m_id)
  {
    header('Content-Type: application/json');

    try {
      // Get member systems with usage details
      $query = $this->db->query("
            SELECT 
                ms.module_id,
                mm.name as module_name,
                GROUP_CONCAT(DISTINCT mmm.name) as menu_permissions,
                (SELECT COUNT(*) FROM tbl_member_user_permissions mup 
                 WHERE mup.member_id = ms.m_id 
                 AND mup.system_id IN (SELECT id FROM tbl_member_module_menus WHERE module_id = ms.module_id)) as permission_count
            FROM 
                tbl_member_systems ms
            JOIN 
                tbl_member_modules mm ON ms.module_id = mm.id
            LEFT JOIN 
                tbl_member_user_permissions mup ON ms.m_id = mup.member_id
            LEFT JOIN 
                tbl_member_module_menus mmm ON mup.system_id = mmm.id
            WHERE 
                ms.m_id = ?
            GROUP BY 
                ms.module_id, mm.name
        ", [$m_id]);

      $systems = $query->result();

      echo json_encode([
        'success' => true,
        'data' => $systems
      ]);

    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }


  public function website_management()
  {
    // เช็คล็อกอิน
    if ($this->session->userdata('m_id') == '') {
      redirect('User/login');
    }

    // เช็คสิทธิ์การเข้าถึง
    $member_id = $this->session->userdata('m_id');
    $has_permission = false;

    // ถ้าเป็น system_admin หรือ super_admin ให้ผ่านเลย
    if (in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
      $has_permission = true;
    } else {
      // เช็คสิทธิ์จาก grant_system_ref_id
      $member = $this->db->where('m_id', $member_id)->get('tbl_member')->row();
      if ($member) {
        $grant_systems = explode(',', $member->grant_system_ref_id);
        $has_permission = in_array('2', $grant_systems);
      }
    }

    if (!$has_permission) {
      $this->session->set_flashdata('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
      redirect('System_member');
    }

    // ตัวแปรสำหรับการแบ่งหน้า
    $page = $this->input->get('page') ? $this->input->get('page') : 0;
    $per_page = 20; // จำนวนรายการต่อหน้า

    // ดึงจำนวนผู้ใช้ทั้งหมดที่มีสิทธิ์จัดการเว็บไซต์
    $total_users_query = $this->db->select('COUNT(*) as count')
      ->from('tbl_member')
      ->where("FIND_IN_SET('2', grant_system_ref_id) >", 0)
      ->get();
    $total_users = $total_users_query->row()->count;

    // ดึงข้อมูลผู้ใช้ที่มีสิทธิ์จัดการเว็บไซต์ (module_id = 2) แบบแบ่งหน้า
    $users_query = $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, m.grant_user_ref_id')
      ->from('tbl_member as m')
      ->where("FIND_IN_SET('2', m.grant_system_ref_id) >", 0)
      ->limit($per_page, $page)
      ->get();

    $web_users = $users_query->result();

    // จัดเตรียมข้อมูลสำหรับ pagination
    $config['base_url'] = site_url('System_member/website_management');
    $config['total_rows'] = $total_users;
    $config['per_page'] = $per_page;
    $config['page_query_string'] = TRUE;
    $config['query_string_segment'] = 'page';
    $config['reuse_query_string'] = TRUE;

    // Pagination styling
    $config['full_tag_open'] = '<div class="flex items-center space-x-2 mt-4">';
    $config['full_tag_close'] = '</div>';

    // ปุ่มก่อนหน้า
    $config['prev_link'] = '<i class="fas fa-chevron-left"></i>';
    $config['prev_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['prev_tag_close'] = '</button>';

    // ปุ่มถัดไป
    $config['next_link'] = '<i class="fas fa-chevron-right"></i>';
    $config['next_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['next_tag_close'] = '</button>';

    // ตัวเลขหน้า
    $config['num_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['num_tag_close'] = '</button>';

    // หน้าปัจจุบัน
    $config['cur_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded">';
    $config['cur_tag_close'] = '</button>';

    // First & Last
    $config['first_link'] = '<i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left ml-[-0.5rem]"></i>';
    $config['first_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['first_tag_close'] = '</button>';

    $config['last_link'] = '<i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right ml-[-0.5rem]"></i>';
    $config['last_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['last_tag_close'] = '</button>';

    $this->pagination->initialize($config);

    // ดึงข้อมูลสิทธิ์ทั้งหมดจากตาราง tbl_grant_user
    $all_permissions = $this->db->get('tbl_grant_user')->result();

    // ประมวลผลข้อมูลเพื่อให้ง่ายต่อการแสดงผล
    foreach ($web_users as $user) {
      $user->permissions = [];
      $user->permissions_text = '';

      if (!empty($user->grant_user_ref_id)) {
        $user_perms = explode(',', $user->grant_user_ref_id);

        // ถ้ามีสิทธิ์ทั้งหมด (grant_user_ref_id = 1)
        if (in_array('1', $user_perms)) {
          $user->has_all_permissions = true;
          $user->permissions_text = 'มีสิทธิ์ทั้งหมด';
        } else {
          $user->has_all_permissions = false;
          $perm_names = [];

          foreach ($all_permissions as $perm) {
            if (in_array($perm->grant_user_id, $user_perms)) {
              $perm_names[] = $perm->grant_user_name;
            }
          }

          $user->permissions = $perm_names;
          $user->permissions_text = implode(', ', $perm_names);
        }
      }
    }

    // คำนวณ start_row และ end_row
    $start_row = $page + 1;
    $end_row = min($page + $per_page, $total_users);

    // จัดเตรียมข้อมูลสำหรับแสดงผล
    $data = [
      'web_users' => $web_users,
      'all_permissions' => $all_permissions,
      'pagination' => $this->pagination->create_links(),
      'total_rows' => $total_users,
      'start_row' => $start_row,
      'end_row' => $end_row
    ];

    // โหลดวิว
    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/website_management', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
  }


  public function get_available_web_users()
  {
    header('Content-Type: application/json; charset=utf-8');

    try {
      // ดึงผู้ใช้ที่ยังไม่มีสิทธิ์ในการจัดการเว็บไซต์
      $users = $this->db->select('m_id, m_fname, m_lname, m_email')
        ->from('tbl_member')
        ->where("FIND_IN_SET('2', grant_system_ref_id) =", 0)
        ->or_where('grant_system_ref_id IS NULL')
        ->or_where('grant_system_ref_id = ""')
        ->get()
        ->result();

      echo json_encode([
        'success' => true,
        'data' => $users
      ]);

    } catch (Exception $e) {
      log_message('error', 'Get available web users error: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }



  public function update_web_user_permissions()
  {
    header('Content-Type: application/json');

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data['user_id']) || !isset($data['permissions'])) {
        throw new Exception('ข้อมูลไม่ครบถ้วน');
      }

      $user_id = $data['user_id'];
      $permissions = $data['permissions']; // เป็น array ของ grant_user_id

      // ดึงข้อมูลผู้ใช้
      $user = $this->db->where('m_id', $user_id)->get('tbl_member')->row();
      if (!$user) {
        throw new Exception('ไม่พบข้อมูลผู้ใช้');
      }

      // ตรวจสอบว่ามีสิทธิ์ในการจัดการเว็บไซต์หรือไม่
      $grant_systems = [];
      if (!empty($user->grant_system_ref_id)) {
        $grant_systems = explode(',', $user->grant_system_ref_id);
      }

      // เพิ่มสิทธิ์ในการจัดการเว็บไซต์ (2) ถ้ายังไม่มี
      if (!in_array('2', $grant_systems)) {
        $grant_systems[] = '2';
      }

      // เช็คว่าเลือกสิทธิ์ทั้งหมดหรือไม่
      $grant_user_ref_id = '';
      if (in_array('1', $permissions)) {
        // ถ้าเลือกสิทธิ์ทั้งหมด ให้ set grant_user_ref_id = 1
        $grant_user_ref_id = '1';
      } else {
        // ถ้าไม่ได้เลือกสิทธิ์ทั้งหมด ให้ใช้สิทธิ์ที่เลือก
        $grant_user_ref_id = implode(',', $permissions);
      }

      // อัพเดทสิทธิ์
      $this->db->where('m_id', $user_id)
        ->update('tbl_member', [
          'grant_system_ref_id' => implode(',', array_unique($grant_systems)),
          'grant_user_ref_id' => $grant_user_ref_id
        ]);

      echo json_encode([
        'success' => true,
        'message' => 'อัพเดทสิทธิ์เรียบร้อยแล้ว'
      ]);

    } catch (Exception $e) {
      log_message('error', 'Update web user permissions error: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }


  public function add_web_users()
  {
    header('Content-Type: application/json');

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data['user_ids']) || !isset($data['permissions'])) {
        throw new Exception('ข้อมูลไม่ครบถ้วน');
      }

      $user_ids = $data['user_ids'];
      $permissions = $data['permissions']; // เป็น array ของ grant_user_id

      // เช็คว่าเลือกสิทธิ์ทั้งหมดหรือไม่
      $grant_user_ref_id = '';
      if (in_array('1', $permissions)) {
        // ถ้าเลือกสิทธิ์ทั้งหมด ให้ set grant_user_ref_id = 1
        $grant_user_ref_id = '1';
      } else {
        // ถ้าไม่ได้เลือกสิทธิ์ทั้งหมด ให้ใช้สิทธิ์ที่เลือก
        $grant_user_ref_id = implode(',', $permissions);
      }

      $this->db->trans_start();

      foreach ($user_ids as $user_id) {
        // ดึงข้อมูลผู้ใช้
        $user = $this->db->where('m_id', $user_id)->get('tbl_member')->row();
        if ($user) {
          // ตรวจสอบสิทธิ์ที่มีอยู่
          $grant_systems = [];
          if (!empty($user->grant_system_ref_id)) {
            $grant_systems = explode(',', $user->grant_system_ref_id);
          }

          // เพิ่มสิทธิ์ในการจัดการเว็บไซต์ (2) ถ้ายังไม่มี
          if (!in_array('2', $grant_systems)) {
            $grant_systems[] = '2';
          }

          // อัพเดทสิทธิ์
          $this->db->where('m_id', $user_id)
            ->update('tbl_member', [
              'grant_system_ref_id' => implode(',', array_unique($grant_systems)),
              'grant_user_ref_id' => $grant_user_ref_id
            ]);
        }
      }

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('ไม่สามารถบันทึกข้อมูลได้');
      }

      echo json_encode([
        'success' => true,
        'message' => 'เพิ่มผู้ใช้เรียบร้อยแล้ว'
      ]);

    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Add web users error: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }


  public function remove_web_user()
  {
    header('Content-Type: application/json');

    try {
      $data = json_decode(file_get_contents('php://input'), true);

      if (!isset($data['user_id'])) {
        throw new Exception('ข้อมูลไม่ครบถ้วน');
      }

      $user_id = $data['user_id'];

      // ดึงข้อมูลผู้ใช้
      $user = $this->db->where('m_id', $user_id)->get('tbl_member')->row();
      if (!$user) {
        throw new Exception('ไม่พบข้อมูลผู้ใช้');
      }

      // ตรวจสอบสิทธิ์ที่มีอยู่
      $grant_systems = [];
      if (!empty($user->grant_system_ref_id)) {
        $grant_systems = explode(',', $user->grant_system_ref_id);
      }

      // ลบสิทธิ์ในการจัดการเว็บไซต์ (2)
      $grant_systems = array_diff($grant_systems, ['2']);

      // อัพเดทสิทธิ์
      $this->db->where('m_id', $user_id)
        ->update('tbl_member', [
          'grant_system_ref_id' => implode(',', $grant_systems),
          'grant_user_ref_id' => '' // ลบสิทธิ์การจัดการเว็บไซต์ทั้งหมด
        ]);

      echo json_encode([
        'success' => true,
        'message' => 'ลบผู้ใช้เรียบร้อยแล้ว'
      ]);

    } catch (Exception $e) {
      log_message('error', 'Remove web user error: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }








  public function get_module_permissions()
  {
    header('Content-Type: application/json');

    try {
      $module_id = $this->input->get('module_id');
      if (!$module_id) {
        throw new Exception('ไม่พบรหัสโมดูล');
      }

      // ดึงข้อมูลเมนูของโมดูล
      $permissions = $this->db->select('id, name, code')
        ->from('tbl_member_module_menus')
        ->where('module_id', $module_id)
        ->where('status', 1)
        ->order_by('display_order', 'ASC')
        ->get()
        ->result();

      echo json_encode([
        'success' => true,
        'data' => $permissions
      ]);
    } catch (Exception $e) {
      log_message('error', 'Get module permissions error: ' . $e->getMessage());
      echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }



  public function member_web_external()
  {
    // เช็คล็อกอิน
    if ($this->session->userdata('m_id') == '') {
      redirect('User/login');
    }

    $user_system = $this->session->userdata('m_system');

    // Get filter parameters
    $search = $this->input->get('search');
    $page = ($this->input->get('page')) ? $this->input->get('page') : 0;

    // เพิ่มพารามิเตอร์การเรียงลำดับ
    $sort_by = $this->input->get('sort_by') ? $this->input->get('sort_by') : 'mp_id';
    $sort_order = $this->input->get('sort_order') ? $this->input->get('sort_order') : 'desc';

    // กำหนดจำนวนรายการต่อหน้า
    $per_page = 20;

    // โหลด Model สำหรับสมาชิกภายนอก
    $this->load->model('member_public_model');

    // นับจำนวนสมาชิกทั้งหมด
    $data['total_members'] = $this->member_public_model->count_filtered_members($search);

    // สถิติอื่นๆ (ไม่กรองด้วย status)
    $data['verified_members'] = $data['total_members']; // ตั้งค่าเริ่มต้นเป็นจำนวนทั้งหมด
    $data['new_members_this_month'] = 0; // ตั้งค่าเริ่มต้นเป็น 0

    // ส่งค่า user_system ไปยัง view เพื่อควบคุมสิทธิ์
    $data['user_system'] = $user_system;

    // Pagination config
    $config['base_url'] = site_url('System_member/member_web_external');
    $config['total_rows'] = $data['total_members'];
    $config['per_page'] = $per_page;
    $config['page_query_string'] = TRUE;
    $config['query_string_segment'] = 'page';
    $config['reuse_query_string'] = TRUE;

    // Pagination styling (เหมือนเดิม)
    $config['full_tag_open'] = '<div class="flex items-center space-x-2">';
    $config['full_tag_close'] = '</div>';

    $config['first_link'] = '<i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left ml-[-0.5rem]"></i>';
    $config['first_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['first_tag_close'] = '</button>';

    $config['last_link'] = '<i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right ml-[-0.5rem]"></i>';
    $config['last_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['last_tag_close'] = '</button>';

    $config['next_link'] = '<i class="fas fa-chevron-right"></i>';
    $config['next_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['next_tag_close'] = '</button>';

    $config['prev_link'] = '<i class="fas fa-chevron-left"></i>';
    $config['prev_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['prev_tag_close'] = '</button>';

    $config['cur_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded">';
    $config['cur_tag_close'] = '</button>';

    $config['num_tag_open'] = '<button class="flex items-center justify-center w-8 h-8 border rounded hover:bg-gray-50">';
    $config['num_tag_close'] = '</button>';

    $this->pagination->initialize($config);

    // Calculate rows
    $start_row = $page + 1;
    $end_row = min($page + $per_page, $config['total_rows']);

    // Get member list data - ไม่ส่งพารามิเตอร์ status
    $data['members'] = $this->member_public_model->get_filtered_members(
      $search,
      $per_page,
      $page,
      $sort_by,
      $sort_order
    );

    // ส่งค่าไปยัง view
    $data['sort_by'] = $sort_by;
    $data['sort_order'] = $sort_order;

    // Set additional view data
    $data['pagination'] = $this->pagination->create_links();
    $data['total_rows'] = $config['total_rows'];
    $data['start_row'] = $start_row;
    $data['end_row'] = $end_row;

    // Load views
    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/member_web_external', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
  }

  public function export_member_csv()
  {
    // เช็คล็อกอิน
    if ($this->session->userdata('m_id') == '') {
      redirect('User/login');
    }

    // เช็คสิทธิ์ - เฉพาะ system_admin เท่านั้น
    $user_system = $this->session->userdata('m_system');
    if ($user_system != 'system_admin') {
      // แสดงข้อความแจ้งเตือนและ redirect กลับ
      $this->session->set_flashdata('error', 'คุณไม่มีสิทธิ์ในการ Export ข้อมูล');
      redirect('System_member/member_web_external');
    }

    // รับพารามิเตอร์ search
    $search = $this->input->get('search');

    // โหลด Model
    $this->load->model('member_public_model');

    // ดึงข้อมูลทั้งหมดสำหรับ export
    $members = $this->member_public_model->get_all_members_for_export($search);

    // ตั้งค่า Header สำหรับ CSV download
    $filename = 'member_public_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // สร้าง output stream
    $output = fopen('php://output', 'w');

    // เพิ่ม BOM สำหรับ UTF-8 (เพื่อให้ Excel แสดงภาษาไทยถูกต้อง)
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Header ของ CSV ตามโครงสร้าง table
    $headers = array(
      'ID',
      'Member ID',
      'Email',
      'คำนำหน้า',
      'ชื่อ',
      'นามสกุล',
      'เบอร์โทรศัพท์',
      'เลขบัตรประชาชน',
      'ที่อยู่',
      'ตำบล',
      'อำเภอ',
      'จังหวัด',
      'รหัสไปรษณีย์',
      'รูปโปรไฟล์',
      'ผู้สร้าง',
      'สถานะ',
      'วันที่ลงทะเบียน',
      'ผู้อัปเดตล่าสุด',
      'วันที่อัปเดตล่าสุด',
      'ความสมบูรณ์โปรไฟล์ (%)'
    );

    fputcsv($output, $headers);

    // เขียนข้อมูลแต่ละแถว
    foreach ($members as $member) {
      $row = array(
        $member->id,
        $member->mp_id,
        $member->mp_email,
        $member->mp_prefix,
        $member->mp_fname,
        $member->mp_lname,
        $member->mp_phone,
        $member->mp_number,
        $member->mp_address,
        $member->mp_district,
        $member->mp_amphoe,
        $member->mp_province,
        $member->mp_zipcode,
        $member->mp_img,
        $member->mp_by,
        ($member->mp_status == 1) ? 'เปิดใช้งาน' : 'ปิดใช้งาน',
        $member->mp_registered_date,
        $member->mp_updated_by,
        $member->mp_updated_date,
        $member->profile_completion
      );

      fputcsv($output, $row);
    }

    fclose($output);
    exit();
  }




  public function toggle_member_status_external()
  {
    // ตรวจสอบว่าเป็นการเรียกผ่าน AJAX หรือไม่
    if (!$this->input->is_ajax_request()) {
      exit('No direct script access allowed');
    }

    // รับค่า POST parameters
    $member_id = $this->input->post('member_id');
    $status = $this->input->post('status');

    // ตรวจสอบว่ามีค่าทั้งหมดหรือไม่
    if (!$member_id || !isset($status)) {
      $response = array(
        'success' => false,
        'message' => 'ไม่พบข้อมูลที่ต้องการอัพเดต'
      );
      echo json_encode($response);
      return;
    }

    // อัพเดตสถานะในฐานข้อมูล
    $data = array(
      'mp_status' => $status,
      'mp_updated_by' => $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname'),
      'mp_updated_date' => date('Y-m-d H:i:s')
    );

    $this->db->where('mp_id', $member_id);
    $result = $this->db->update('tbl_member_public', $data);

    // ส่งผลลัพธ์กลับ
    if ($result) {
      $response = array(
        'success' => true,
        'message' => 'อัพเดตสถานะเรียบร้อยแล้ว'
      );
    } else {
      $response = array(
        'success' => false,
        'message' => 'ไม่สามารถอัพเดตสถานะได้'
      );
    }

    echo json_encode($response);
  }




  public function del_member_external($mp_id)
  {
    header('Content-Type: application/json');

    try {
      // ตรวจสอบการเข้าถึง
      if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        throw new Exception('ไม่มีสิทธิ์ดำเนินการ');
      }

      // โหลด Model
      $this->load->model('member_public_model');

      // ดึงข้อมูลสมาชิกเพื่อเก็บประวัติการลบ
      $member = $this->member_public_model->get_member_by_id($mp_id);

      if (!$member) {
        throw new Exception('ไม่พบข้อมูลสมาชิก');
      }

      // เริ่ม transaction
      $this->db->trans_start();

      // บันทึกประวัติการลบ (ถ้ามีตาราง log_delete)
      if ($this->db->table_exists('tbl_log_delete')) {
        $log_data = [
          'table_name' => 'tbl_member_public',
          'row_id' => $mp_id,
          'deleted_by' => $this->session->userdata('m_fname'),
          'deleted_data' => json_encode($member),
          'delete_date' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('tbl_log_delete', $log_data);
      }

      // ลบข้อมูลรูปภาพ (ถ้ามี)
      if (!empty($member->mp_img)) {
        $image_path = './docs/img/' . $member->mp_img;
        if (file_exists($image_path)) {
          unlink($image_path);
        }
      }

      // ลบข้อมูลสมาชิก
      $this->member_public_model->delete_member($mp_id);

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('เกิดข้อผิดพลาดในการลบข้อมูล');
      }

      echo json_encode([
        'status' => true,
        'message' => 'ลบข้อมูลเรียบร้อยแล้ว'
      ]);

    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Delete external member error: ' . $e->getMessage());

      echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
      ]);
    }
  }





  public function add_member_external()
  {
    // เช็คล็อกอิน
    if ($this->session->userdata('m_id') == '') {
      redirect('User/login');
    }

    // ตรวจสอบว่ามีสิทธิ์เข้าถึงหรือไม่
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
      $this->session->set_flashdata('error', 'ไม่มีสิทธิ์เข้าถึงหน้านี้');
      redirect('System_member/member_web_external');
    }

    // Load views
    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/add_member_external');
    $this->load->view('member/js');
    $this->load->view('member/footer');
  }

  public function add_member_external_db()
  {
    try {
      $this->db->trans_start();

      // ข้อมูลจากฟอร์ม
      $data = array(
        'mp_email' => $this->input->post('mp_email'),
        'mp_password' => sha1($this->input->post('mp_password')),
        'mp_fname' => $this->input->post('mp_fname'),
        'mp_lname' => $this->input->post('mp_lname'),
        'mp_phone' => $this->input->post('mp_phone'),
        'mp_number' => $this->input->post('mp_number'),
        'mp_address' => $this->input->post('mp_address'),
        'mp_by' => $this->session->userdata('m_fname'),
        'mp_status' => 1, // เปิดใช้งานโดยค่าเริ่มต้น
        'mp_registered_date' => date('Y-m-d H:i:s')
      );

      // อัปโหลดรูปภาพ (ถ้ามี)
      if (!empty($_FILES['mp_img']['name'])) {
        $config['upload_path'] = './docs/img/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('mp_img')) {
          $upload_data = $this->upload->data();
          $data['mp_img'] = $upload_data['file_name'];
        } else {
          throw new Exception('ไม่สามารถอัปโหลดรูปภาพได้: ' . $this->upload->display_errors('', ''));
        }
      }

      // ตรวจสอบอีเมลซ้ำ
      $this->load->model('member_public_model');
      $email_exists = $this->member_public_model->check_email_exists($data['mp_email']);

      if ($email_exists) {
        throw new Exception('อีเมลนี้มีในระบบแล้ว กรุณาใช้อีเมลอื่น');
      }

      // ตรวจสอบเลขบัตรประชาชนซ้ำ
      $id_card_exists = $this->member_public_model->check_id_card_exists($data['mp_number']);

      if ($id_card_exists) {
        throw new Exception('เลขบัตรประจำตัวประชาชนนี้มีในระบบแล้ว');
      }

      // บันทึกข้อมูล
      $this->db->insert('tbl_member_public', $data);
      $member_id = $this->db->insert_id();

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('การบันทึกข้อมูลไม่สมบูรณ์');
      }

      // บันทึกสำเร็จ
      $this->session->set_flashdata('save_success', TRUE);
      redirect('System_member/member_web_external');

    } catch (Exception $e) {
      $this->db->trans_rollback();
      $this->session->set_flashdata('save_error', $e->getMessage());
      redirect('System_member/add_member_external');
    }
  }

  public function edit_member_external($mp_id)
  {
    // เช็คล็อกอิน
    if ($this->session->userdata('m_id') == '') {
      redirect('User/login');
    }

    // ตรวจสอบว่ามีสิทธิ์เข้าถึงหรือไม่
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
      $this->session->set_flashdata('error', 'ไม่มีสิทธิ์เข้าถึงหน้านี้');
      redirect('System_member/member_web_external');
    }

    try {
      // โหลด Model และดึงข้อมูลสมาชิก
      $this->load->model('member_public_model');
      $data['member'] = $this->member_public_model->get_member_by_id($mp_id);

      if (!$data['member']) {
        throw new Exception('ไม่พบข้อมูลสมาชิก');
      }

      // Load views
      $this->load->view('member/header');
      $this->load->view('member/css');
      $this->load->view('member/sidebar');
      $this->load->view('member/edit_member_external', $data);
      $this->load->view('member/js');
      $this->load->view('member/footer');

    } catch (Exception $e) {
      $this->session->set_flashdata('error', $e->getMessage());
      redirect('System_member/member_web_external');
    }
  }

  public function edit_member_external_db()
  {
    try {
      $mp_id = $this->input->post('mp_id');

      if (!$mp_id) {
        throw new Exception('ไม่พบรหัสสมาชิก');
      }

      $this->db->trans_start();

      // โหลด Model และดึงข้อมูลสมาชิกเดิม
      $this->load->model('member_public_model');
      $old_member = $this->member_public_model->get_member_by_id($mp_id);

      if (!$old_member) {
        throw new Exception('ไม่พบข้อมูลสมาชิก');
      }

      // ข้อมูลที่จะอัพเดท
      $data = array(
        'mp_fname' => $this->input->post('mp_fname'),
        'mp_lname' => $this->input->post('mp_lname'),
        'mp_phone' => $this->input->post('mp_phone'),
        'mp_number' => $this->input->post('mp_number'),
        'mp_address' => $this->input->post('mp_address'),
        'mp_updated_by' => $this->session->userdata('m_fname'),
        'mp_updated_date' => date('Y-m-d H:i:s')
      );

      // ตรวจสอบเลขบัตรประชาชนซ้ำ (ถ้ามีการเปลี่ยนแปลง)
      if ($data['mp_number'] != $old_member->mp_number) {
        $id_card_exists = $this->member_public_model->check_id_card_exists($data['mp_number'], $mp_id);

        if ($id_card_exists) {
          throw new Exception('เลขบัตรประจำตัวประชาชนนี้มีในระบบแล้ว');
        }
      }

      // ถ้ามีการเปลี่ยนอีเมล
      $new_email = $this->input->post('mp_email');
      if ($new_email != $old_member->mp_email) {
        // ตรวจสอบอีเมลซ้ำ
        $email_exists = $this->member_public_model->check_email_exists($new_email, $mp_id);

        if ($email_exists) {
          throw new Exception('อีเมลนี้มีในระบบแล้ว กรุณาใช้อีเมลอื่น');
        }

        $data['mp_email'] = $new_email;
      }

      // ถ้ามีการเปลี่ยนรหัสผ่าน
      $new_password = $this->input->post('mp_password');
      if (!empty($new_password)) {
        $data['mp_password'] = sha1($new_password);
      }

      // อัปโหลดรูปภาพ (ถ้ามี)
      if (!empty($_FILES['mp_img']['name'])) {
        $config['upload_path'] = './docs/img/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('mp_img')) {
          // ลบรูปเก่า (ถ้ามี)
          if (!empty($old_member->mp_img)) {
            $old_image_path = './docs/img/' . $old_member->mp_img;
            if (file_exists($old_image_path)) {
              unlink($old_image_path);
            }
          }

          $upload_data = $this->upload->data();
          $data['mp_img'] = $upload_data['file_name'];
        } else {
          throw new Exception('ไม่สามารถอัปโหลดรูปภาพได้: ' . $this->upload->display_errors('', ''));
        }
      }

      // บันทึกข้อมูล
      $this->db->where('mp_id', $mp_id);
      $this->db->update('tbl_member_public', $data);

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('การบันทึกข้อมูลไม่สมบูรณ์');
      }

      // บันทึกสำเร็จ
      $this->session->set_flashdata('save_success', TRUE);
      redirect('System_member/member_web_external');

    } catch (Exception $e) {
      $this->db->trans_rollback();
      $this->session->set_flashdata('save_error', $e->getMessage());
      redirect('System_member/edit_member_external/' . $mp_id);
    }
  }



  // เพิ่มฟังก์ชันเหล่านี้ในไฟล์ controllers/System_member.php

  public function check_email_exists_external()
  {
    header('Content-Type: application/json');

    try {
      $email = $this->input->post('email');
      $mp_id = $this->input->post('mp_id'); // ส่งมาเฉพาะตอนแก้ไข

      if (empty($email)) {
        throw new Exception('กรุณาระบุอีเมล');
      }

      // โหลด Model
      $this->load->model('member_public_model');

      // ตรวจสอบอีเมลซ้ำ
      $exists = $this->member_public_model->check_email_exists($email, $mp_id);

      echo json_encode([
        'exists' => $exists,
        'message' => $exists ? 'อีเมลนี้มีในระบบแล้ว' : 'อีเมลนี้สามารถใช้ได้'
      ]);

    } catch (Exception $e) {
      echo json_encode([
        'exists' => false,
        'message' => $e->getMessage()
      ]);
    }
  }

  public function check_id_card_exists_external()
  {
    header('Content-Type: application/json');

    try {
      $id_card = $this->input->post('id_card');
      $mp_id = $this->input->post('mp_id'); // ส่งมาเฉพาะตอนแก้ไข

      if (empty($id_card)) {
        throw new Exception('กรุณาระบุเลขบัตรประชาชน');
      }

      if (strlen($id_card) !== 13 || !is_numeric($id_card)) {
        throw new Exception('เลขบัตรประชาชนไม่ถูกต้อง');
      }

      // โหลด Model
      $this->load->model('member_public_model');

      // ตรวจสอบเลขบัตรประชาชนซ้ำ
      $exists = $this->member_public_model->check_id_card_exists($id_card, $mp_id);

      echo json_encode([
        'exists' => $exists,
        'message' => $exists ? 'เลขบัตรประชาชนนี้มีในระบบแล้ว' : 'เลขบัตรประชาชนนี้สามารถใช้ได้'
      ]);

    } catch (Exception $e) {
      echo json_encode([
        'exists' => false,
        'message' => $e->getMessage()
      ]);
    }
  }

  // ฟังก์ชันตรวจสอบความถูกต้องของเลขบัตรประชาชน
  private function validate_thai_id_card($id_card)
  {
    // ตรวจสอบความยาว
    if (strlen($id_card) != 13 || !is_numeric($id_card)) {
      return false;
    }

    // ตรวจสอบรหัสตามสูตรการคำนวณ
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
      $sum += (int) ($id_card[$i]) * (13 - $i);
    }

    $check_digit = (11 - ($sum % 11)) % 10;

    return ($check_digit == (int) ($id_card[12]));
  }

  // ฟังก์ชันแสดงรายงานสมาชิกภายนอก
  public function report_external_members()
  {
    // เช็คล็อกอิน
    if ($this->session->userdata('m_id') == '') {
      redirect('User/login');
    }

    // โหลด Model
    $this->load->model('member_public_model');

    // ข้อมูลสำหรับรายงาน
    $data['total_members'] = $this->member_public_model->count_filtered_members();
    $data['active_members'] = $this->member_public_model->count_filtered_members('', 1);
    $data['inactive_members'] = $this->member_public_model->count_filtered_members('', 0);
    $data['new_members_this_month'] = $this->member_public_model->count_new_members_this_month();

    // ข้อมูลกราฟ
    $data['monthly_stats'] = $this->member_public_model->get_monthly_registration_stats();

    // Load views
    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/report_external_members', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
  }

  // ฟังก์ชันส่งออกข้อมูลสมาชิกภายนอก
  public function export_external_members()
  {
    // เช็คล็อกอิน
    if ($this->session->userdata('m_id') == '') {
      redirect('User/login');
    }

    // ตรวจสอบสิทธิ์การเข้าถึง
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
      $this->session->set_flashdata('error', 'ไม่มีสิทธิ์เข้าถึง');
      redirect('System_member/member_web_external');
    }

    // โหลด Model
    $this->load->model('member_public_model');

    // ดึงข้อมูลทั้งหมด (ไม่มีการแบ่งหน้า)
    $members = $this->member_public_model->get_filtered_members('', '');

    // โหลด library สำหรับส่งออก Excel
    $this->load->library('excel');

    // สร้างไฟล์ Excel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // ตั้งชื่อ sheet
    $sheet->setTitle('รายชื่อสมาชิกภายนอก');

    // กำหนดหัวตาราง
    $sheet->setCellValue('A1', 'ลำดับ');
    $sheet->setCellValue('B1', 'ชื่อ-นามสกุล');
    $sheet->setCellValue('C1', 'อีเมล');
    $sheet->setCellValue('D1', 'เบอร์โทรศัพท์');
    $sheet->setCellValue('E1', 'เลขบัตรประชาชน');
    $sheet->setCellValue('F1', 'ที่อยู่');
    $sheet->setCellValue('G1', 'สถานะ');

    // กำหนดสไตล์หัวตาราง
    $styleArray = [
      'font' => [
        'bold' => true,
      ],
      'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
          'rgb' => 'E2EFDA',
        ],
      ],
    ];

    $sheet->getStyle('A1:G1')->applyFromArray($styleArray);

    // เพิ่มข้อมูล
    $row = 2;
    foreach ($members as $index => $member) {
      $sheet->setCellValue('A' . $row, $index + 1);
      $sheet->setCellValue('B' . $row, $member->mp_fname . ' ' . $member->mp_lname);
      $sheet->setCellValue('C' . $row, $member->mp_email);
      $sheet->setCellValue('D' . $row, $member->mp_phone);

      // จัดรูปแบบเลขบัตรประชาชน
      $id_card = $member->mp_number;
      $formatted_id_card = substr($id_card, 0, 1) . '-' .
        substr($id_card, 1, 4) . '-' .
        substr($id_card, 5, 5) . '-' .
        substr($id_card, 10, 2) . '-' .
        substr($id_card, 12, 1);
      $sheet->setCellValue('E' . $row, $formatted_id_card);

      $sheet->setCellValue('F' . $row, $member->mp_address);
      $sheet->setCellValue('G' . $row, isset($member->mp_status) && $member->mp_status == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน');

      $row++;
    }

    // ปรับความกว้างคอลัมน์ให้พอดี
    foreach (range('A', 'G') as $col) {
      $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // สร้างไฟล์
    $filename = 'รายชื่อสมาชิกภายนอก_' . date('Y-m-d_H-i-s') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
  }






}
