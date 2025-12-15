<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Notification Controller - พร้อมระบบ Archive
 */
class Notification extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Notification_model');
        $this->load->library('Notification_lib');
    }

    /**
     * แสดงรายการการแจ้งเตือน (ไม่รวม archived)
     */
    public function index()
    {
        $user_id = $this->session->userdata('m_id');
        $page = (int)($this->input->get('page') ?: 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'type' => $this->input->get('type'),
            'priority' => $this->input->get('priority'),
            'is_read' => $this->input->get('is_read'),
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to')
        ];

        $data['notifications'] = $this->Notification_model->get_notifications($user_id, $limit, $offset, $filters);
        $data['unread_count'] = $this->Notification_model->count_unread($user_id);
        $data['total_count'] = $this->Notification_model->count_total($user_id);
        $data['filters'] = $filters;
        $data['current_page'] = $page;

        // ✅ เพิ่มสถิติ
        $data['statistics'] = $this->Notification_model->get_notification_statistics($user_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/notifications', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ✅ ใหม่: แสดงรายการการแจ้งเตือนที่ถูก Archive
     */
    public function archived()
    {
        $user_id = $this->session->userdata('m_id');
        $page = (int)($this->input->get('page') ?: 1);
        $limit = 50; // แสดงมากกว่าปกติเพราะเป็น archive
        $offset = ($page - 1) * $limit;

        $filters = [
            'type' => $this->input->get('type'),
            'priority' => $this->input->get('priority'),
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to')
        ];

        $data['notifications'] = $this->Notification_model->get_archived_notifications($user_id, $limit, $offset, $filters);
        $data['filters'] = $filters;
        $data['current_page'] = $page;
        $data['page_title'] = 'การแจ้งเตือนที่เก็บไว้ (Archive)';

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/notifications_archived', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ✅ ใหม่: หน้าสถิติการแจ้งเตือน
     */
    public function statistics()
    {
        $user_id = $this->session->userdata('m_id');
        $days = (int)($this->input->get('days') ?: 30);

        $data['statistics'] = $this->Notification_model->get_notification_statistics($user_id, $days);
        $data['days'] = $days;
        $data['page_title'] = 'สถิติการแจ้งเตือน';

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/notification_statistics', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * API: ดึงการแจ้งเตือนแบบ AJAX
     */
    public function api_get_notifications()
    {
        header('Content-Type: application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        $user_id = $this->session->userdata('m_id');
        $limit = (int)($this->input->get('limit') ?: 10);
        $include_archived = $this->input->get('include_archived') === 'true';
        
        $filters = ['include_archived' => $include_archived];
        $notifications = $this->Notification_model->get_notifications($user_id, $limit, 0, $filters);
        $unread_count = $this->Notification_model->count_unread($user_id);

        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unread_count,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * API: ทำเครื่องหมายว่าอ่านแล้ว
     */
    public function api_mark_as_read()
    {
        header('Content-Type: application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        $notification_id = $this->input->post('notification_id');
        $user_id = $this->session->userdata('m_id');

        if (!$notification_id) {
            echo json_encode(['success' => false, 'error' => 'Missing notification ID']);
            return;
        }

        $result = $this->Notification_model->mark_as_read($notification_id, $user_id);
        $unread_count = $this->Notification_model->count_unread($user_id);

        echo json_encode([
            'success' => $result,
            'unread_count' => $unread_count,
            'message' => $result ? 'ทำเครื่องหมายแล้ว' : 'เกิดข้อผิดพลาด'
        ]);
    }

    /**
     * API: ทำเครื่องหมายทั้งหมดว่าอ่านแล้ว
     */
    public function api_mark_all_as_read()
    {
        header('Content-Type: application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        $user_id = $this->session->userdata('m_id');
        $result = $this->Notification_model->mark_all_as_read($user_id);

        echo json_encode([
            'success' => $result,
            'unread_count' => 0,
            'message' => $result ? 'ทำเครื่องหมายทั้งหมดแล้ว' : 'เกิดข้อผิดพลาด'
        ]);
    }

    /**
     * ✅ แก้ไข: API Archive การแจ้งเตือน (แทนการลบ)
     */
    public function api_archive()
    {
        header('Content-Type: application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        $notification_id = $this->input->post('notification_id');
        $user_id = $this->session->userdata('m_id');
        
        if (!$notification_id) {
            echo json_encode(['success' => false, 'error' => 'Missing notification ID']);
            return;
        }

        $result = $this->Notification_model->archive_notification($notification_id, $user_id);
        $unread_count = $this->Notification_model->count_unread($user_id);

        echo json_encode([
            'success' => $result,
            'unread_count' => $unread_count,
            'message' => $result ? 'เก็บเข้า Archive แล้ว' : 'เกิดข้อผิดพลาด'
        ]);
    }

    /**
     * ✅ ใหม่: API ย้ายกลับจาก Archive
     */
    public function api_unarchive()
    {
        header('Content-Type: application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        $notification_id = $this->input->post('notification_id');
        
        if (!$notification_id) {
            echo json_encode(['success' => false, 'error' => 'Missing notification ID']);
            return;
        }

        $result = $this->Notification_model->unarchive_notification($notification_id);

        echo json_encode([
            'success' => $result,
            'message' => $result ? 'ย้ายกลับจาก Archive แล้ว' : 'เกิดข้อผิดพลาด'
        ]);
    }

    /**
     * ✅ ใหม่: API ลบถาวร (เฉพาะ system_admin)
     */
    public function api_hard_delete()
    {
        header('Content-Type: application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        // ตรวจสอบสิทธิ์ system_admin
        if ($this->session->userdata('m_system') !== 'system_admin') {
            echo json_encode(['success' => false, 'error' => 'ไม่มีสิทธิ์ลบถาวร']);
            return;
        }

        $notification_id = $this->input->post('notification_id');
        $confirm = $this->input->post('confirm') === 'DELETE_FOREVER';
        
        if (!$notification_id || !$confirm) {
            echo json_encode(['success' => false, 'error' => 'ข้อมูลไม่ครบหรือไม่ได้ยืนยัน']);
            return;
        }

        $result = $this->Notification_model->hard_delete_notification($notification_id);

        // บันทึก log การลบ
        if ($result) {
            log_message('warning', 'Hard deleted notification ID: ' . $notification_id . ' by user: ' . $this->session->userdata('m_id'));
        }

        echo json_encode([
            'success' => $result,
            'message' => $result ? 'ลบถาวรแล้ว' : 'เกิดข้อผิดพลาด'
        ]);
    }

    /**
     * API: ดึงจำนวนการแจ้งเตือนที่ยังไม่อ่าน
     */
    public function api_unread_count()
    {
        header('Content-Type: application/json');
        
        $user_id = $this->session->userdata('m_id');
        $unread_count = $this->Notification_model->count_unread($user_id);

        echo json_encode([
            'success' => true,
            'unread_count' => $unread_count,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * ✅ แก้ไข: Archive การแจ้งเตือนเก่าแทนการลบ (สำหรับ admin)
     */
    public function archive_old()
    {
        if ($this->session->userdata('m_system') !== 'system_admin') {
            show_404();
            return;
        }

        $days = (int)($this->input->get('days') ?: 90);
        $result = $this->Notification_model->archive_old_read_notifications($days);

        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $result,
                'message' => $result ? "Archive การแจ้งเตือนเก่าเรียบร้อย ({$result} รายการ)" : "เกิดข้อผิดพลาด",
                'archived_count' => $result
            ]);
        } else {
            $this->session->set_flashdata('message', $result ? "Archive การแจ้งเตือนเก่าเรียบร้อย ({$result} รายการ)" : 'เกิดข้อผิดพลาด');
            redirect('Notification');
        }
    }

    /**
     * ✅ ใหม่: Export ข้อมูลการแจ้งเตือน (CSV)
     */
    public function export_csv()
    {
        // ตรวจสอบสิทธิ์
        if ($this->session->userdata('m_system') !== 'system_admin') {
            show_404();
            return;
        }

        $filters = [
            'type' => $this->input->get('type'),
            'priority' => $this->input->get('priority'),
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to'),
            'include_archived' => true // รวม archived ในการ export
        ];

        $notifications = $this->Notification_model->get_notifications(null, 10000, 0, $filters);

        // ตั้งค่า CSV
        $filename = 'notification_log_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF"); // BOM สำหรับ UTF-8

        // Header
        fputcsv($output, [
            'ID',
            'ประเภท',
            'หัวข้อ',
            'ข้อความ',
            'ระดับความสำคัญ',
            'เป้าหมาย',
            'สถานะการอ่าน',
            'สถานะ Archive',
            'วันที่สร้าง',
            'ผู้สร้าง',
            'วันที่อ่าน',
            'วันที่ Archive'
        ], ',', '"');

        // ข้อมูล
        foreach ($notifications as $notification) {
            fputcsv($output, [
                $notification->notification_id,
                $notification->type,
                $notification->title,
                $notification->message,
                $notification->priority,
                $notification->target_role ?: $notification->target_user_id,
                $notification->is_read ? 'อ่านแล้ว' : 'ยังไม่อ่าน',
                $notification->is_archived ? 'Archive แล้ว' : 'ปกติ',
                $notification->created_at,
                ($notification->m_fname && $notification->m_lname) ? $notification->m_fname . ' ' . $notification->m_lname : 'ระบบ',
                $notification->read_at ?: '-',
                $notification->archived_at ?: '-'
            ], ',', '"');
        }

        fclose($output);
    }

    /**
     * ✅ ใหม่: API สถิติแบบ Real-time
     */
    public function api_statistics()
    {
        header('Content-Type: application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        $user_id = $this->session->userdata('m_id');
        $days = (int)($this->input->get('days') ?: 7);
        $statistics = $this->Notification_model->get_notification_statistics($user_id, $days);

        echo json_encode([
            'success' => true,
            'statistics' => $statistics,
            'period' => $days . ' วันล่าสุด',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * สร้างตาราง (สำหรับติดตั้งครั้งแรก)
     */
    public function install()
    {
        if ($this->session->userdata('m_system') !== 'system_admin') {
            show_404();
            return;
        }

        $result = $this->Notification_model->create_table();
        
        if ($result) {
            $this->session->set_flashdata('message', 'สร้างตารางการแจ้งเตือนเรียบร้อย');
        } else {
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการสร้างตาราง');
        }
        
        redirect('Notification');
    }

    /**
     * ทดสอบการส่งการแจ้งเตือน
     */
    public function test()
    {
        if ($this->session->userdata('m_system') !== 'system_admin') {
            show_404();
            return;
        }

        // ทดสอบการแจ้งเตือนต่างๆ
        $this->Notification_lib->storage_warning(85, 85, 100);
        $this->Notification_lib->new_complain(999, 'ทดสอบเรื่องร้องเรียน', 'ผู้ทดสอบ');
        $this->Notification_lib->system_alert('ทดสอบระบบ', 'นี่คือการทดสอบการแจ้งเตือน พร้อมระบบ Archive');

        $this->session->set_flashdata('message', 'ส่งการแจ้งเตือนทดสอบเรียบร้อย');
        redirect('Notification');
    }
}