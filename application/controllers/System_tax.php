<?php
defined('BASEPATH') or exit('No direct script access allowed');

class System_tax extends CI_Controller
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
        $this->check_session_timeout();
        $this->load->model('member_model');
        $this->load->model('member_public_model');
        $this->load->model('position_model');
        $this->load->library('pagination');
        $this->load->helper('my_date');
        $this->load->model('tax_model');
        $this->load->model('tax_user_log_model');
        $this->load->model('tax_due_date_model');
        $this->load->model('tax_penalty_model');
    }

    private function check_session_timeout()
    {
        $timeout = 900; // 15 นาที
        $last_activity = $this->session->userdata('last_activity');

        if ($last_activity && (time() - $last_activity > $timeout)) {
            $this->session->sess_destroy();
            redirect('User/logout', 'refresh');
        } else {
            $this->session->set_userdata('last_activity', time());
        }
    }

    public function index()
    {
        $this->tax_penalty_model->calculate_all_tax_penalties();
        // เพิ่มข้อมูลสรุปใหม่
        $data['total_paid_amount'] = $this->tax_model->get_total_paid_amount();
        $data['total_arrears_amount'] = $this->tax_model->get_total_arrears_amount();
        $data['current_month_amount'] = $this->tax_model->get_current_month_amount();
        $data['current_year_amount'] = $this->tax_model->get_current_year_amount();
        $data['unique_taxpayers'] = $this->tax_model->get_unique_taxpayers();


        $data['payment_status_pending'] = $this->tax_model->payment_status_pending();
        $data['payment_status_verified'] = $this->tax_model->payment_status_verified();
        $data['payment_status_rejected'] = $this->tax_model->payment_status_rejected();
        $data['payment_status_all'] = $this->tax_model->payment_status_all();
        $data['payment_status_arrears'] = $this->tax_model->payment_status_arrears();

        $current_year = date('Y') + 543; // ปีปัจจุบันเป็น พ.ศ.
        $data['monthly_data'] = $this->tax_model->get_monthly_payments($current_year);
        $data['tax_type_data'] = $this->tax_model->get_payment_by_type($current_year);
        $data['status_data'] = $this->tax_model->get_payment_by_status($current_year);
        $data['tax_yearly_data'] = $this->tax_model->get_payment_by_type_yearly($current_year);
        $data['arrears_data'] = $this->tax_model->get_arrears_by_type();

        // echo "<pre>";
        // print_r($data['tax_yearly_data']);
        // print_r($data['arrears_data']);
        // echo "</pre>";
        // exit();

        $this->load->view('system_tax/header');
        $this->load->view('system_tax/css');
        $this->load->view('system_tax/sidebar');
        $this->load->view('system_tax/dashboard', $data);
        $this->load->view('system_tax/js');
        $this->load->view('system_tax/chart', $data);
        $this->load->view('system_tax/footer');
    }

    public function main()
    {
        $data['settings'] = $this->tax_model->get_settings();

        $data['payment_status_pending'] = $this->tax_model->payment_status_pending();
        $data['payment_status_verified'] = $this->tax_model->payment_status_verified();
        $data['payment_status_rejected'] = $this->tax_model->payment_status_rejected();
        $data['payment_status_all'] = $this->tax_model->payment_status_all();
        $data['payment_status_arrears'] = $this->tax_model->payment_status_arrears();

        // echo '<pre>';
        // print_r($data['payment_status_pending']);
        // echo '</pre>';
        // exit();
        // ค้นหา
        $search = $this->input->get('search');

        // Pagination
        $config['base_url'] = base_url('System_tax/main');
        $config['total_rows'] = $this->tax_model->count_all_payments($search);
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = TRUE;

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
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $start = ($page - 1) * $config['per_page'];

        $data['payments'] = $this->tax_model->get_all_payments($config['per_page'], $start, $search);
        $data['pagination'] = $this->pagination->create_links();
        $data['start_row'] = $start + 1;
        $data['end_row'] = min($start + $config['per_page'], $config['total_rows']);
        $data['total_rows'] = $config['total_rows'];

        $this->load->view('system_tax/header');
        $this->load->view('system_tax/css');
        $this->load->view('system_tax/sidebar');
        $this->load->view('system_tax/main', $data);
        $this->load->view('system_tax/js');
        $this->load->view('system_tax/footer');
    }

    public function verify($id)
    {
        // อัพเดทสถานะเป็น verified
        $data = array(
            'payment_status' => 'verified',
            'verification_date' => date('Y-m-d H:i:s'),
            'admin_comment' => 'ตรวจสอบและอนุมัติแล้ว',
            'verified_by' => $this->session->userdata('m_id')
        );

        $this->db->where('id', $id);
        $result = $this->db->update('tbl_tax_payments', $data);

        if ($result) {
            $this->session->set_flashdata('verify_success', TRUE);
        } else {
            $this->session->set_flashdata('verify_reject_error', TRUE);
        }

        // เช็คว่ามี referer หรือไม่
        $redirect_url = $this->input->server('HTTP_REFERER');
        if (empty($redirect_url)) {
            // ถ้าไม่มี referer ให้ไปหน้า main
            redirect('System_tax');
        } else {
            // ถ้ามี referer ให้กลับไปหน้าเดิม
            redirect($redirect_url);
        }
    }

    public function reject($id)
    {
        $comment = $this->input->get('comment') ?: 'ไม่อนุมัติการชำระภาษี';
        $data = array(
            'payment_status' => 'rejected',
            'verification_date' => date('Y-m-d H:i:s'),
            'admin_comment' => $comment,
            'verified_by' => $this->session->userdata('m_id')
        );

        $result = $this->db->where('id', $id)->update('tbl_tax_payments', $data);
        $this->session->set_flashdata($result ? 'reject_success' : 'verify_reject_error', TRUE);
        // เช็คว่ามี referer หรือไม่
        $redirect_url = $this->input->server('HTTP_REFERER');
        if (empty($redirect_url)) {
            // ถ้าไม่มี referer ให้ไปหน้า main
            redirect('System_tax');
        } else {
            // ถ้ามี referer ให้กลับไปหน้าเดิม
            redirect($redirect_url);
        }
    }

    public function update_payment_settings()
    {

        $config['upload_path'] = './docs/img/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        $data = array(
            'bank_name' => $this->input->post('bank_name'),
            'account_name' => $this->input->post('account_name'),
            'account_number' => $this->input->post('account_number'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $this->session->userdata('m_id'),
        );

        // ถ้ามีการอัพโหลดรูป
        if ($this->upload->do_upload('qr_code_image')) {
            $upload_data = $this->upload->data();
            $data['qr_code_image'] = $upload_data['file_name'];

            // ลบรูปเก่าถ้ามี
            $old_settings = $this->tax_model->get_settings();
            if ($old_settings && $old_settings->qr_code_image) {
                $old_file = './docs/img/' . $old_settings->qr_code_image;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
        }

        $result = $this->tax_model->update_settings($data);

        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'บันทึกข้อมูลสำเร็จ'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
            ]);
        }
    }

    public function get_payment_settings()
    {
        $settings = $this->tax_model->get_settings();
        if ($settings) {
            echo json_encode([
                'status' => 'success',
                'settings' => $settings  // เปลี่ยนจาก 'data' เป็น 'settings'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูล'
            ]);
        }
    }

    public function add_arrears()
    {
        $data = array(
            'citizen_id' => $this->input->post('citizen_id'),
            'firstname' => $this->input->post('firstname'),
            'lastname' => $this->input->post('lastname'),
            'tax_type' => $this->input->post('tax_type'),
            'tax_year' => $this->input->post('tax_year'),
            'amount' => $this->input->post('amount'),
            'total_amount' => $this->input->post('amount'),
            'payment_status' => 'required',
            'admin_comment' => $this->input->post('admin_comment'),
            'created_at' => date('Y-m-d H:i:s'),
            'verified_by' => $this->session->userdata('m_id')
        );

        $result = $this->tax_model->insert_payment_tax($data);

        if ($result) {
            $this->session->set_flashdata('save_success', TRUE);

            // redirect ตามประเภทภาษี
            $tax_type = $this->input->post('tax_type');
            switch ($tax_type) {
                case 'signboard':
                    $this->tax_model->check_and_update_payment_status();
                    redirect('System_tax/signboard_tax');
                    break;
                case 'land':
                    $this->tax_model->check_and_update_payment_status();
                    redirect('System_tax/land_tax');
                    break;
                case 'local':
                    $this->tax_penalty_model->calculate_local_tax_penalties();
                    redirect('System_tax/local_tax');
                    break;
                default:
                    redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('save_error', TRUE);
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function import_excel()
    {
        // ตั้งค่าการอัพโหลดไฟล์
        $config['upload_path'] = './docs/file/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = 40960; // 20MB

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('excel_file')) {
            $upload_data = $this->upload->data();
            $file_path = $upload_data['full_path'];

            try {
                // อ่านไฟล์ CSV
                $handle = fopen($file_path, "r");

                // ข้าม header row
                fgetcsv($handle);

                $success_count = 0;
                $error_count = 0;

                // อ่านทีละแถว
                while (($row = fgetcsv($handle)) !== FALSE) {
                    // แปลงประเภทภาษีจากภาษาไทยเป็นภาษาอังกฤษ
                    $tax_type = $this->convert_tax_type($row[3]);

                    $data = array(
                        'citizen_id' => $row[0],
                        'firstname' => $row[1],
                        'lastname' => $row[2],
                        'tax_type' => $tax_type,
                        'tax_year' => $row[4],
                        'amount' => $row[5],
                        'payment_status' => 'arrears',
                        'admin_comment' => isset($row[6]) ? $row[6] : '',
                        'created_at' => date('Y-m-d H:i:s'),
                        'verified_by' => $this->session->userdata('m_id')
                    );

                    if ($this->tax_model->insert_payment_tax($data)) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }

                fclose($handle);

                // ลบไฟล์หลังจากใช้งานเสร็จ
                unlink($file_path);

                $this->session->set_flashdata('import_success', "นำเข้าข้อมูลสำเร็จ $success_count รายการ, ผิดพลาด $error_count รายการ");
            } catch (Exception $e) {
                $this->session->set_flashdata('import_error', 'เกิดข้อผิดพลาดในการนำเข้าข้อมูล: ' . $e->getMessage());
            }
        } else {
            $this->session->set_flashdata('import_error', $this->upload->display_errors());
        }

        // เช็คว่ามี referer หรือไม่
        $redirect_url = $this->input->server('HTTP_REFERER');
        if (empty($redirect_url)) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('System_tax');
        } else {
            $this->session->set_flashdata('save_success', TRUE);
            redirect($redirect_url);
        }
    }

    // เพิ่มฟังก์ชันใหม่สำหรับแปลงประเภทภาษี
    private function convert_tax_type($thai_type)
    {
        // ทำ trim และแปลงเป็นตัวพิมพ์เล็กเพื่อป้องกันปัญหาการเปรียบเทียบ
        $thai_type = trim(strtolower($thai_type));

        // แปลงประเภทภาษี
        $tax_types = [
            'ภาษีที่ดินและสิ่งปลูกสร้าง' => 'land',
            'ภาษีป้าย' => 'signboard',
            'ภาษีท้องถิ่น' => 'local'
        ];

        // ถ้าเจอในตารางแปลงค่า ให้ใช้ค่าที่แปลงแล้ว
        foreach ($tax_types as $thai => $eng) {
            if (strpos($thai_type, strtolower($thai)) !== false) {
                return $eng;
            }
        }

        // ถ้าไม่เจอในตารางแปลงค่า ตรวจสอบว่าเป็นรหัสภาษาอังกฤษที่ถูกต้องหรือไม่
        $valid_eng_types = ['land', 'signboard', 'local'];
        if (in_array($thai_type, $valid_eng_types)) {
            return $thai_type;
        }

        // ถ้าไม่เจอทั้งภาษาไทยและอังกฤษ ให้ใช้ค่าเริ่มต้น
        return 'land'; // หรือจะ throw exception หรือจัดการตามที่ต้องการ
    }

    public function user_logs()
    {
        // รับค่าการค้นหาและกรอง
        $user_id = $this->input->get('user_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $search = $this->input->get('search');
        $user_type = $this->input->get('user_type');

        // Pagination config
        $config['base_url'] = base_url('System_tax/user_logs');
        $config['total_rows'] = $this->tax_user_log_model->count_all_logs($search, $user_id, $start_date, $end_date);
        $config['per_page'] = 20;
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = TRUE;

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
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $start = ($page - 1) * $config['per_page'];

        // ดึงข้อมูล logs
        $data['logs'] = $this->tax_user_log_model->get_user_logs_with_details(
            $config['per_page'],
            $start,
            $search,
            $user_id,
            $start_date,
            $end_date,
            $user_type
        );

        $data['pagination'] = $this->pagination->create_links();

        // ข้อมูลสำหรับแสดงผลจำนวนรายการ
        $data['start_row'] = $start + 1;
        $data['end_row'] = min($start + $config['per_page'], $config['total_rows']);
        $data['total_rows'] = $config['total_rows'];

        // ดึงรายชื่อผู้ใช้ทั้งหมดสำหรับ dropdown filter (ทั้งเจ้าหน้าที่และประชาชน)
        $data['users_member'] = $this->member_model->get_members();
        $data['users_public'] = $this->member_public_model->get_all_members();

        // Load views
        $this->load->view('system_tax/header');
        $this->load->view('system_tax/css');
        $this->load->view('system_tax/sidebar');
        $this->load->view('system_tax/user_logs', $data);
        $this->load->view('system_tax/js');
        $this->load->view('system_tax/footer');
    }

    public function due_dates()
    {
        // โหลดโมเดล
        $this->load->model('tax_due_date_model');

        $data['due_dates'] = $this->tax_due_date_model->get_all_due_dates();
        $data['tax_types'] = [
            'land' => 'ภาษีที่ดินและสิ่งปลูกสร้าง',
            'signboard' => 'ภาษีป้าย',
            'local' => 'ภาษีท้องถิ่น'
        ];

        $data['thai_month_arr'] = [
            "01" => "มกราคม",
            "02" => "กุมภาพันธ์",
            "03" => "มีนาคม",
            "04" => "เมษายน",
            "05" => "พฤษภาคม",
            "06" => "มิถุนายน",
            "07" => "กรกฎาคม",
            "08" => "สิงหาคม",
            "09" => "กันยายน",
            "10" => "ตุลาคม",
            "11" => "พฤศจิกายน",
            "12" => "ธันวาคม"
        ];

        // โหลดวิว
        $this->load->view('system_tax/header');
        $this->load->view('system_tax/css');
        $this->load->view('system_tax/sidebar');
        $this->load->view('system_tax/tax_due_dates', $data);
        $this->load->view('system_tax/js');
        $this->load->view('system_tax/footer');
    }

    public function add_due_date()
    {
        $notification_date = sprintf(
            '%s-%s',
            $this->input->post('notification_day'),
            $this->input->post('notification_month')
        );

        $due_date = sprintf(
            '%s-%s',
            $this->input->post('due_day'),
            $this->input->post('due_month')
        );

        $data = [
            'tax_type' => $this->input->post('tax_type'),
            'notification_date' => $notification_date,
            'due_date' => $due_date,
            'updated_by' => $this->session->userdata('m_id')
        ];

        $result = $this->tax_due_date_model->add_due_date($data);

        if ($result) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            $this->session->set_flashdata('save_error', TRUE);
        }

        redirect('System_tax/due_dates');
    }

    public function get_due_date_by_id($id)
    {
        $this->load->model('tax_due_date_model');
        $due_date = $this->tax_due_date_model->get_by_id($id);
        echo json_encode($due_date);
    }

    public function update_due_date()
    {
        $id = $this->input->post('id');

        // สร้างรูปแบบวันที่ DD-MM
        $notification_date = sprintf(
            '%s-%s',
            $this->input->post('notification_day'),
            $this->input->post('notification_month')
        );

        $due_date = sprintf(
            '%s-%s',
            $this->input->post('due_day'),
            $this->input->post('due_month')
        );

        $data = [
            // 'tax_type' => $this->input->post('tax_type'),
            'due_date' => $due_date,
            'notification_date' => $notification_date,
            'updated_by' => $this->session->userdata('m_id')
        ];

        $this->load->model('tax_due_date_model');
        $result = $this->tax_due_date_model->update_due_date($id, $data);

        if ($result) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            $this->session->set_flashdata('save_error', TRUE);
        }

        redirect('System_tax/due_dates');
    }

    public function delete_due_date($id)
    {
        $this->load->model('tax_due_date_model');
        $result = $this->tax_due_date_model->delete_due_date($id);

        if ($result) {
            $this->session->set_flashdata('delete_success', TRUE);
        } else {
            $this->session->set_flashdata('delete_error', TRUE);
        }

        redirect('System_tax/due_dates');
    }

    public function update_payment_statuses()
    {
        $this->tax_model->check_and_update_payment_status();
    }

    public function land_tax()
    {
        $this->load_tax_page('land', 'ภาษีที่ดินและสิ่งปลูกสร้าง');
    }

    public function signboard_tax()
    {
        $this->load_tax_page('signboard', 'ภาษีป้าย');
    }

    public function local_tax()
    {
        $this->load_tax_page('local', 'ภาษีท้องถิ่น');
    }

    private function load_tax_page($tax_type, $title)
    {
        // เหมือนกับ main() แต่เพิ่มเงื่อนไข tax_type
        $data['settings'] = $this->tax_model->get_settings();
        $search = $this->input->get('search');

        // Pagination config เหมือนเดิม...
        $config['base_url'] = base_url("System_tax/{$tax_type}_tax");
        $config['total_rows'] = $this->tax_model->count_payments_by_type($tax_type, $search);
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = TRUE;

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
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $start = ($page - 1) * $config['per_page'];

        $data['payments'] = $this->tax_model->get_payments_by_type(
            $tax_type,
            $config['per_page'],
            $start,
            $search
        );
        $data['pagination'] = $this->pagination->create_links();
        $data['title'] = $title;
        $data['tax_type'] = $tax_type;
        $data['start_row'] = $start + 1;
        $data['end_row'] = min($start + $config['per_page'], $config['total_rows']);
        $data['total_rows'] = $config['total_rows'];

        // Load views
        $this->load->view('system_tax/header');
        $this->load->view('system_tax/css');
        $this->load->view('system_tax/sidebar');
        $this->load->view('system_tax/tax_type_view', $data);
        $this->load->view('system_tax/js');
        $this->load->view('system_tax/footer');
    }

    public function add_signboard()
    {
        // Debug
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Files: ' . print_r($_FILES, true));
        // บันทึกข้อมูลหลัก
        $payment_data = array(
            'citizen_id' => $this->input->post('citizen_id'),
            'firstname' => $this->input->post('firstname'),
            'lastname' => $this->input->post('lastname'),
            'tax_type' => 'signboard',
            'tax_year' => $this->input->post('tax_year'),
            'amount' => $this->input->post('amount'),
            'total_amount' => $this->input->post('amount'),
            'payment_status' => 'required',
            'admin_comment' => $this->input->post('admin_comment'),
            'created_at' => date('Y-m-d H:i:s'),
            'verified_by' => $this->session->userdata('m_id')
        );

        // บันทึกข้อมูลหลักและรับ ID ที่ถูกบันทึก
        if ($this->tax_model->insert_payment_tax($payment_data)) {
            $payment_id = $this->db->insert_id(); // รับ ID ที่เพิ่งถูกบันทึก

            // บันทึกรายละเอียดป้าย
            $signboard_items = json_decode($this->input->post('signboard_items'), true);
            $success = true;

            foreach ($signboard_items as $item) {
                $detail_data = array(
                    'payment_id' => $payment_id,
                    'area' => $item['area'],
                    'amount' => $item['amount'],
                    'image' => $item['image']
                );

                if (!$this->tax_model->insert_signboard_detail($detail_data)) {
                    $success = false;
                }
            }

            if ($success) {
                $this->session->set_flashdata('save_success', TRUE);
            } else {
                $this->session->set_flashdata('save_error', TRUE);
            }
        } else {
            $this->session->set_flashdata('save_error', TRUE);
        }
        $this->tax_model->check_and_update_payment_status();
        redirect('System_tax/signboard_tax');
    }

    public function upload_signboard_image()
    {
        $config['upload_path'] = './docs/img/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('image')) {
            $data = $this->upload->data();
            echo json_encode([
                'status' => 'success',
                'filename' => $data['file_name']
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => $this->upload->display_errors()
            ]);
        }
    }

    public function update_payment()
    {
        $payment_id = $this->input->post('payment_id');
        $payment = $this->tax_model->get_payment_by_id($payment_id);

        $data = array(
            'citizen_id' => $this->input->post('citizen_id'),
            'firstname' => $this->input->post('firstname'),
            'lastname' => $this->input->post('lastname'),
            'verified_by' => $this->session->userdata('m_id'),
            'admin_comment' => $this->input->post('admin_comment'),
            'tax_year' => $this->input->post('tax_year')
        );

        if ($payment && $payment->tax_type === 'signboard') {
            $total_amount = 0;

            // อัพเดทป้ายเดิม
            $signboard_data = $this->input->post('signboard');
            if ($signboard_data) {
                foreach ($signboard_data as $index => $item) {
                    if (isset($_FILES["signboard_{$index}_new_image"]) && $_FILES["signboard_{$index}_new_image"]['size'] > 0) {
                        $config['upload_path'] = './docs/img/';
                        $config['allowed_types'] = 'gif|jpg|jpeg|png';
                        $config['encrypt_name'] = TRUE;

                        $this->load->library('upload', $config);
                        if ($this->upload->do_upload("signboard_{$index}_new_image")) {
                            $upload_data = $this->upload->data();
                            $item['image'] = $upload_data['file_name'];
                        }
                    }

                    $this->tax_model->update_signboard_detail($item['id'], array(
                        'area' => $item['area'],
                        'amount' => $item['amount'],
                        'image' => $item['image']
                    ));

                    $total_amount += floatval($item['amount']);
                }
            }

            // เพิ่มป้ายใหม่
            $new_signboard = $this->input->post('new_signboard');
            if ($new_signboard) {
                foreach ($new_signboard as $index => $item) {
                    // อัพโหลดรูปภาพใหม่
                    $config['upload_path'] = './docs/img/';
                    $config['allowed_types'] = 'gif|jpg|jpeg|png';
                    $config['encrypt_name'] = TRUE;

                    $this->load->library('upload', $config);
                    if ($this->upload->do_upload("new_signboard_{$index}_image")) {
                        $upload_data = $this->upload->data();

                        // บันทึกข้อมูลป้ายใหม่
                        $new_detail = array(
                            'payment_id' => $payment_id,
                            'area' => $item['area'],
                            'amount' => $item['amount'],
                            'image' => $upload_data['file_name']
                        );
                        $this->tax_model->insert_signboard_detail($new_detail);

                        $total_amount += floatval($item['amount']);
                    }
                }
            }

            // อัพเดทยอดรวม
            $data['amount'] = $total_amount;
        } else {
            $data['amount'] = $this->input->post('amount');
        }
        $this->tax_penalty_model->calculate_local_tax_penalties();

        $result = $this->tax_model->update_payment_tax($payment_id, $data);

        if ($result) {
            // $penalty = $this->tax_penalty_model->calculate_late_payment_penalty($payment_id);
            // if ($penalty) {
            //     $this->tax_model->update_payment_tax($payment_id, [
            //         'penalty_amount' => $penalty['penalty_amount'],
            //         'total_amount' => $data['amount'] + $penalty['penalty_amount']
            //     ]);
            // }

            $this->session->set_flashdata('save_success', TRUE);

            // Redirect ตามประเภทภาษี
            if ($payment->tax_type === 'signboard') {
                $this->tax_penalty_model->calculate_all_signboard_penalties();
                redirect('System_tax/signboard_tax');
            } else if ($payment->tax_type === 'land') {
                $this->tax_penalty_model->calculate_late_payment_penalty();
                redirect('System_tax/land_tax');
            } else {
                $this->tax_penalty_model->calculate_local_tax_penalties();
                redirect('System_tax/local_tax');
            }
        } else {
            $this->session->set_flashdata('save_error', TRUE);
            redirect($_SERVER['HTTP_REFERER']); // กลับไปหน้าเดิม
        }
    }

    public function get_signboard_details($payment_id)
    {
        $details = $this->tax_model->get_signboard_details($payment_id);
        echo json_encode(['status' => 'success', 'details' => $details]);
    }

    public function delete_signboard_detail($id)
    {
        $result = $this->tax_model->delete_signboard_detail($id);

        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'ลบข้อมูลสำเร็จ' : 'เกิดข้อผิดพลาดในการลบข้อมูล'
        ]);
    }


    // เพิ่มเมธอดต่อไปนี้ในคลาส System_tax
    public function lan_tax_penalty_settings()
    {
        $this->load->model('tax_penalty_model');

        $data['settings'] = $this->tax_penalty_model->get_lan_tax_penalty_settings('land');

        $this->load->view('system_tax/header');
        $this->load->view('system_tax/css');
        $this->load->view('system_tax/sidebar');
        $this->load->view('system_tax/lan_tax_penalty_settings', $data);
        $this->load->view('system_tax/js');
        $this->load->view('system_tax/footer');
    }

    public function update_lan_tax_penalty_settings()
    {
        $this->load->model('tax_penalty_model');

        $data = [
            // 'no_filing_fine' => $this->input->post('no_filing_fine'),
            // 'incorrect_filing_fine' => $this->input->post('incorrect_filing_fine'),
            'late_payment_1month' => $this->input->post('late_payment_1month'),
            'late_payment_2month' => $this->input->post('late_payment_2month'),
            'late_payment_3month' => $this->input->post('late_payment_3month'),
            'late_payment_4month' => $this->input->post('late_payment_4month'),
            'backdate_years_no_filing' => $this->input->post('backdate_years_no_filing'),
            'backdate_years_incorrect' => $this->input->post('backdate_years_incorrect'),
            'updated_by' => $this->session->userdata('m_id'),
        ];

        $result = $this->tax_penalty_model->update_lan_tax_penalty_settings('land', $data);

        if ($result) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            $this->session->set_flashdata('save_error', TRUE);
        }

        redirect('System_tax/lan_tax_penalty_settings');
    }

    public function calculate_penalties($payment_id)
    {
        $this->load->model('tax_penalty_model');

        $payment = $this->tax_model->get_payment_by_id($payment_id);
        if (!$payment) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลการชำระเงิน']);
            return;
        }

        // คำนวณค่าปรับตามเงื่อนไขต่างๆ
        $penalties = [];
        $total_penalty = 0;

        // ค่าปรับจ่ายล่าช้า
        if ($payment->payment_status == 'verified') {
            $late_penalty = $this->tax_penalty_model->calculate_late_payment_penalty($payment_id);
            if ($late_penalty) {
                $penalties['late_payment'] = $late_penalty;
                $total_penalty += $late_penalty['penalty_amount'];
            }
        }

        // ค่าปรับไม่ยื่นแบบ
        if ($this->input->post('no_filing') == 'true') {
            $no_filing_penalty = $this->tax_penalty_model->calculate_no_filing_penalty($payment_id);
            if ($no_filing_penalty) {
                $penalties['no_filing'] = $no_filing_penalty;
                $total_penalty += $no_filing_penalty;
            }
        }

        // ค่าปรับยื่นแบบไม่ถูกต้อง
        if ($this->input->post('incorrect_filing') == 'true') {
            $incorrect_penalty = $this->tax_penalty_model->calculate_incorrect_filing_penalty($payment_id);
            if ($incorrect_penalty) {
                $penalties['incorrect_filing'] = $incorrect_penalty;
                $total_penalty += $incorrect_penalty;
            }
        }

        echo json_encode([
            'status' => 'success',
            'penalties' => $penalties,
            'total_penalty' => $total_penalty
        ]);
    }

    public function get_payment_penalties($payment_id)
    {
        $this->load->model('tax_penalty_model');

        $penalties = $this->tax_penalty_model->get_penalties_by_payment($payment_id);
        $total_penalty = $this->tax_penalty_model->get_total_penalty_amount($payment_id);

        echo json_encode([
            'status' => 'success',
            'penalties' => $penalties,
            'total_penalty' => $total_penalty
        ]);
    }

    public function delete_penalty($penalty_id)
    {
        $this->load->model('tax_penalty_model');

        $result = $this->tax_penalty_model->delete_penalty($penalty_id);

        if ($result) {
            $this->session->set_flashdata('delete_success', TRUE);
        } else {
            $this->session->set_flashdata('delete_error', TRUE);
        }

        redirect('System_tax/lan_tax_penalty_settings');
    }

    public function get_penalty_details($penalty_id)
    {
        $this->load->model('tax_penalty_model');

        $penalty = $this->tax_penalty_model->get_penalty_by_id($penalty_id);
        if (!$penalty) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลค่าปรับ']);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'penalty' => $penalty
        ]);
    }

    public function update_penalty($penalty_id)
    {
        $this->load->model('tax_penalty_model');

        $data = [
            'penalty_amount' => $this->input->post('penalty_amount'),
            'admin_comment' => $this->input->post('admin_comment'),
            'updated_by' => $this->session->userdata('m_id'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->tax_penalty_model->update_penalty($penalty_id, $data);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'อัปเดตข้อมูลค่าปรับเรียบร้อย']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล']);
        }
    }

    // เพิ่มเมธอดสำหรับแสดงรายงานค่าปรับ
    public function penalty_report()
    {
        $this->load->model('tax_penalty_model');

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $penalty_type = $this->input->get('penalty_type');

        $data['penalties'] = $this->tax_penalty_model->get_penalties_report($start_date, $end_date, $penalty_type);
        $data['total_amount'] = $this->tax_penalty_model->get_total_penalties($start_date, $end_date, $penalty_type);

        $this->load->view('system_tax/header');
        $this->load->view('system_tax/css');
        $this->load->view('system_tax/sidebar');
        $this->load->view('system_tax/penalty_report', $data);
        $this->load->view('system_tax/js');
        $this->load->view('system_tax/footer');
    }

    public function recalculate_penalty($payment_id)
    {
        $penalty = $this->tax_penalty_model->calculate_late_payment_penalty($payment_id);
        $payment = $this->tax_model->get_payment_by_id($payment_id);

        $this->db->where('id', $payment_id)->update('tbl_tax_payments', [
            'penalty_amount' => $penalty['penalty_amount'],
            'total_amount' => $payment->amount + $penalty['penalty_amount']
        ]);

        echo json_encode(['status' => 'success']);
    }

    // ตั้งค่าการปรับภาษีท้องถิ่น ------------
    public function local_tax_penalty_settings()
    {

        $data['settings'] = $this->tax_penalty_model->get_local_tax_settings();
        $data['settings_history'] = $this->tax_penalty_model->get_local_tax_settings_history();

        $this->load->view('system_tax/header');
        $this->load->view('system_tax/css');
        $this->load->view('system_tax/sidebar');
        $this->load->view('system_tax/local_tax_penalty_settings', $data);
        $this->load->view('system_tax/js');
        $this->load->view('system_tax/footer');
    }

    public function update_local_tax_penalty_settings()
    {
        $this->load->model('tax_penalty_model');

        $data = [
            // 'no_filing_percent' => $this->input->post('no_filing_percent'),
            // 'incorrect_filing_percent' => $this->input->post('incorrect_filing_percent'),
            // 'incorrect_area_multiplier' => $this->input->post('incorrect_area_multiplier'),
            'late_payment_yearly_percent' => $this->input->post('late_payment_yearly_percent')
        ];

        if ($this->tax_penalty_model->update_local_tax_settings($data)) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            $this->session->set_flashdata('save_error', TRUE);
        }

        redirect('system_tax/local_tax_penalty_settings');
    }

    public function get_local_tax_calculation_example()
    {
        $this->load->model('tax_penalty_model');

        $amount = $this->input->get('amount') ?? 10000;
        $settings = $this->tax_penalty_model->get_settings();

        if (!$settings) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบการตั้งค่า']);
            return;
        }

        $examples = [
            'no_filing' => [
                'description' => 'ไม่ยื่นแบบภายในกำหนด',
                'amount' => $amount * ($settings->no_filing_percent / 100),
                'calculation' => "{$amount} × {$settings->no_filing_percent}%"
            ],
            'incorrect_filing' => [
                'description' => 'ยื่นรายการไม่ถูกต้อง',
                'amount' => $amount * ($settings->incorrect_filing_percent / 100),
                'calculation' => "{$amount} × {$settings->incorrect_filing_percent}%"
            ],
            'incorrect_area' => [
                'description' => 'ชี้เขตแจ้งเนื้อที่ไม่ถูกต้อง',
                'amount' => $amount * $settings->incorrect_area_multiplier,
                'calculation' => "{$amount} × {$settings->incorrect_area_multiplier} เท่า"
            ],
            'late_payment' => [
                'description' => 'ชำระภาษีเกินกำหนด (1 ปี)',
                'amount' => $amount * ($settings->late_payment_yearly_percent / 100),
                'calculation' => "{$amount} × {$settings->late_payment_yearly_percent}% ต่อปี"
            ]
        ];

        echo json_encode([
            'status' => 'success',
            'data' => [
                'baseAmount' => $amount,
                'examples' => $examples
            ]
        ]);
    }

    // เพิ่มเมธอดสำหรับการคำนวณค่าปรับจริง
    public function calculate_local_tax_penalties()
    {

        $payment_id = $this->input->post('payment_id');
        if (!$payment_id) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลการชำระเงิน']);
            return;
        }

        $result = $this->tax_penalty_model->calculate_local_tax_penalties($payment_id);

        if ($result) {
            echo json_encode([
                'status' => 'success',
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ไม่สามารถคำนวณค่าปรับได้'
            ]);
        }
    }

    // เพิ่มเมธอดสำหรับการยกเลิกค่าปรับ
    public function cancel_local_tax_penalty()
    {
        $this->load->model('tax_penalty_model');

        $payment_id = $this->input->post('payment_id');
        $reason = $this->input->post('reason');

        $result = $this->tax_penalty_model->cancel_penalty($payment_id, [
            'cancel_reason' => $reason,
            'cancelled_by' => $this->session->userdata('m_id'),
            'cancelled_at' => date('Y-m-d H:i:s')
        ]);

        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'ยกเลิกค่าปรับเรียบร้อยแล้ว'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ไม่สามารถยกเลิกค่าปรับได้'
            ]);
        }
    }

    // เพิ่มเมธอดสำหรับการพิมพ์ใบแจ้งค่าปรับ
    public function print_local_tax_penalty($payment_id)
    {
        $this->load->model('tax_penalty_model');

        $data['payment'] = $this->db->get_where('tbl_tax_payments', ['id' => $payment_id])->row();
        $data['penalties'] = $this->tax_penalty_model->calculate_penalties($payment_id);
        $data['settings'] = $this->tax_penalty_model->get_settings();

        // โหลด view สำหรับพิมพ์
        $this->load->view('system_tax/print_local_tax_penalty', $data);
    }
    // -------------------------------

    // ตั้งค่าการปรับภาษีป้าย ------------
    public function signboard_tax_penalty_settings()
    {
        $this->load->model('tax_penalty_model');

        $data['settings'] = $this->tax_penalty_model->get_signboard_tax_settings();
        $data['settings_history'] = $this->tax_penalty_model->get_signboard_tax_settings_history();

        $this->load->view('system_tax/header');
        $this->load->view('system_tax/css');
        $this->load->view('system_tax/sidebar');
        $this->load->view('system_tax/signboard_tax_penalty_settings', $data);
        $this->load->view('system_tax/js');
        $this->load->view('system_tax/footer');
    }

    public function update_signboard_tax_penalty_settings()
    {
        $this->load->model('tax_penalty_model');

        $data = [
            // 'no_filing_percent' => $this->input->post('no_filing_percent'),
            // 'incorrect_filing_percent' => $this->input->post('incorrect_filing_percent'),
            'late_payment_monthly_percent' => $this->input->post('late_payment_monthly_percent')
        ];

        if ($this->tax_penalty_model->update_signboard_tax_settings($data)) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            $this->session->set_flashdata('save_error', TRUE);
        }

        redirect('system_tax/signboard_tax_penalty_settings');
    }

    // เพิ่มเมธอดอัพเดตสถานะและค่าปรับอัตโนมัติ
    // public function update_signboard_tax_status()
    // {

    //     try {
    //         // ดึงรายการภาษีป้ายที่ต้องจ่ายหรือค้างชำระทั้งหมด
    //         $payments = $this->db->where('tax_type', 'signboard')
    //             ->where_in('payment_status', ['required', 'arrears'])
    //             ->get('tbl_tax_payments')
    //             ->result();

    //         foreach ($payments as $payment) {
    //             $total_penalty = 0;

    //             // 1. คำนวณค่าปรับไม่ยื่นแบบ
    //             $no_filing_penalty = $this->tax_penalty_model->calculate_signboard_no_filing_penalty($payment->id);
    //             if ($no_filing_penalty) {
    //                 $total_penalty += $no_filing_penalty;
    //             }

    //             // 2. คำนวณค่าปรับยื่นแบบไม่ถูกต้อง
    //             $incorrect_filing_penalty = $this->tax_penalty_model->calculate_signboard_incorrect_filing_penalty($payment->id);
    //             if ($incorrect_filing_penalty) {
    //                 $total_penalty += $incorrect_filing_penalty;
    //             }

    //             // 3. คำนวณค่าปรับชำระล่าช้า
    //             $late_payment_penalty = $this->tax_penalty_model->calculate_all_signboard_penalties($payment->id);
    //             if ($late_payment_penalty) {
    //                 $total_penalty += $late_payment_penalty['penalty_amount'];
    //             }

    //             // อัพเดตข้อมูลในตาราง
    //             if ($total_penalty > 0) {
    //                 $this->db->where('id', $payment->id)
    //                     ->update('tbl_tax_payments', [
    //                         'payment_status' => 'arrears',
    //                         'penalty_amount' => $total_penalty,
    //                         'total_amount' => $payment->amount + $total_penalty,
    //                         'updated_at' => date('Y-m-d H:i:s')
    //                     ]);
    //             }
    //         }

    //         return true;
    //     } catch (Exception $e) {
    //         log_message('error', 'Error updating signboard tax penalties: ' . $e->getMessage());
    //         return false;
    //     }
    // }
    // -------------------------------
}
