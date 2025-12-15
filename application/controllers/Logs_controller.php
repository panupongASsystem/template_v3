<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Logs_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็คสิทธิ์การเข้าถึง - เฉพาะ admin
        if (
            $this->session->userdata('m_system') != 'system_admin' &&
            $this->session->userdata('m_system') != 'super_admin'
        ) {
            redirect('User/logout', 'refresh');
        }

        $this->load->model('log_model');
        $this->load->model('member_model');
    }

    /**
     * แสดง logs ทั้งหมด
     */
    public function index()
    {
        // รับ filters จาก GET parameters
        $filters = array(
            'menu' => $this->input->get('menu'),
            'action' => $this->input->get('action'),
            'user_id' => $this->input->get('user_id'),
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to')
        );

        // Pagination
        $config['base_url'] = site_url('logs_controller/index');
        $config['total_rows'] = $this->log_model->count_logs($filters);
        $config['per_page'] = 50;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'per_page';

        // สำหรับ pagination ที่มี query string
        $query_string = '';
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $query_string .= '&' . $key . '=' . urlencode($value);
            }
        }
        $config['suffix'] = $query_string;
        $config['first_url'] = $config['base_url'] . '?' . $config['query_string_segment'] . '=0' . $query_string;

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;
        $data['logs'] = $this->log_model->get_logs($config['per_page'], $page, $filters);
        $data['pagination'] = $this->pagination->create_links();
        $data['filters'] = $filters;

        // ดึงรายการเมนูที่มีใน logs
        $this->db->distinct();
        $this->db->select('menu');
        $this->db->from('tbl_user_logs');
        $this->db->where('menu IS NOT NULL');
        $this->db->where('menu !=', '');
        $this->db->order_by('menu', 'ASC');
        $menu_query = $this->db->get();
        $data['available_menus'] = $menu_query->result();

        // ดึงรายชื่อผู้ใช้สำหรับ filter
        $this->db->select('m_id, m_username, m_fname, m_lname');
        $this->db->from('tbl_member');
        $this->db->order_by('m_username', 'ASC');
        $data['users'] = $this->db->get()->result();

        // // บันทึก log การเข้าดู logs
        // $this->log_model->add_log(
        //     'เข้าชม', 
        //     'ระบบจัดการ', 
        //     'หน้าดู logs การใช้งานทั้งหมด'
        // );

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/all_logs', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * แสดงสถิติการใช้งาน
     */
    public function statistics()
    {
        $days = $this->input->get('days') ? $this->input->get('days') : 30;

        $data['activity_stats'] = $this->log_model->get_activity_stats($days);
        $data['days'] = $days;

        // สถิติตาม menu
        $this->db->select('menu, COUNT(*) as count');
        $this->db->from('tbl_user_logs');
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $this->db->group_by('menu');
        $this->db->order_by('count', 'DESC');
        $data['menu_stats'] = $this->db->get()->result();

        // สถิติตามผู้ใช้
        $this->db->select('username, full_name, COUNT(*) as count');
        $this->db->from('tbl_user_logs');
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $this->db->group_by('user_id');
        $this->db->order_by('count', 'DESC');
        $this->db->limit(10);
        $data['user_stats'] = $this->db->get()->result();

        // // บันทึก log การเข้าดูสถิติ
        // $this->log_model->add_log(
        //     'เข้าชม', 
        //     'ระบบจัดการ', 
        //     'หน้าสถิติการใช้งาน (ช่วง ' . $days . ' วัน)'
        // );

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/logs_statistics', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ส่งออกข้อมูล logs เป็น CSV
     */
    public function export_csv()
    {
        // รับ filters
        $filters = array(
            'menu' => $this->input->get('menu'),
            'action' => $this->input->get('action'),
            'user_id' => $this->input->get('user_id'),
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to')
        );

        // ดึงข้อมูล logs ทั้งหมดตาม filter
        $logs = $this->log_model->get_logs(10000, 0, $filters); // จำกัด 10,000 รายการ

        // สร้างไฟล์ CSV
        $filename = 'user_logs_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        // เขียน BOM สำหรับ UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header ของ CSV
        fputcsv($output, array(
            'วันที่-เวลา',
            'ผู้ใช้',
            'ชื่อเต็ม',
            'การดำเนินการ',
            'เมนู',
            'รายละเอียด',
            'IP Address',
            'User Agent'
        ));

        // เขียนข้อมูล
        foreach ($logs as $log) {
            fputcsv($output, array(
                date('d/m/Y H:i:s', strtotime($log->created_at . '+543 years')),
                $log->username,
                $log->full_name,
                $log->action,
                $log->menu,
                $log->item_name,
                $log->ip_address,
                $log->user_agent
            ));
        }

        fclose($output);

        // // บันทึก log การส่งออกข้อมูล
        // $this->log_model->add_log(
        //     'ดาวน์โหลด', 
        //     'ระบบจัดการ', 
        //     'ส่งออกข้อมูล logs เป็น CSV (' . count($logs) . ' รายการ)'
        // );
    }

    /**
     * ลบ logs เก่า
     */
    public function clean_old_logs()
    {
        if ($this->input->post()) {
            $days = $this->input->post('days');
            $deleted_count = $this->log_model->clean_old_logs($days);

            // บันทึก log การลบข้อมูลเก่า
            $this->log_model->add_log(
                'ลบ',
                'ระบบจัดการ',
                'ลบ logs เก่าเกิน ' . $days . ' วัน (ลบ ' . $deleted_count . ' รายการ)'
            );

            $this->session->set_flashdata('clean_success', 'ลบข้อมูล logs เก่าเรียบร้อยแล้ว (' . $deleted_count . ' รายการ)');
            redirect('logs_controller/index');
        } else {
            show_404();
        }
    }

    /**
     * ดูรายละเอียด log แต่ละรายการ
     */
    public function view_detail($log_id)
    {
        $this->db->where('log_id', $log_id);
        $data['log'] = $this->db->get('tbl_user_logs')->row();

        if (!$data['log']) {
            show_404();
        }

        // // บันทึก log การเข้าดูรายละเอียด
        // $this->log_model->add_log(
        //     'เข้าชม', 
        //     'ระบบจัดการ', 
        //     'ดูรายละเอียด log ID: ' . $log_id
        // );

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/log_detail', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
}
