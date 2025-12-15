<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Notifications Controller - แก้ไข path ของ view
 */
class Notifications extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // ตรวจสอบ session login
        if (!$this->session->userdata('mp_id')) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Please login first'
                    ]));
                return;
            } else {
                redirect('User');
                return;
            }
        }
        
        $this->load->library('notification_lib');
        $this->load->model('Notification_model');
        $this->load->helper(['timeago', 'url']); // โหลด helper
    }
    
    /**
     * ดึงการแจ้งเตือนล่าสุด (AJAX)
     */
public function get_recent()
{
    try {
        // *** แก้ไข: รองรับทั้ง AJAX และ GET request สำหรับ debugging ***
        if (!$this->input->is_ajax_request() && !$this->input->get()) {
            show_404();
            return;
        }
        
        $limit = $this->input->get('limit') ? (int)$this->input->get('limit') : 5;
        
        // *** แก้ไข: ตรวจสอบว่ามี notification_lib หรือไม่ ***
        if (!isset($this->notification_lib)) {
            // *** แก้ไข: ดึง User ID ที่ถูกต้องจาก database แทน mp_id ***
            $mp_email = $this->session->userdata('mp_email');
            
            if (!$mp_email) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'กรุณาเข้าสู่ระบบ'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }
            
            // *** แก้ไข: ดึง id ที่ถูกต้องจาก tbl_member_public ***
            $public_user = $this->db->select('id, mp_id')
                                   ->where('mp_email', $mp_email)
                                   ->get('tbl_member_public')
                                   ->row();
            
            if (!$public_user) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'ไม่พบข้อมูลผู้ใช้'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }
            
            $user_id = $public_user->id; // ใช้ id แทน mp_id
            
            // ดึงข้อมูลจาก Model โดยตรง
            $notifications = $this->Notification_model->get_by_role_for_user('public', $user_id, 'public', $limit);
            $unread_count = $this->Notification_model->count_unread_for_user('public', $user_id, 'public');
            
            // จัดรูปแบบข้อมูล
            $formatted_notifications = [];
            if (!empty($notifications)) {
                foreach ($notifications as $notification) {
                    $formatted_notifications[] = [
                        'id' => $notification->notification_id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'type' => $notification->type,
                        'priority' => $notification->priority,
                        'is_read' => isset($notification->is_read_by_user) ? (int)$notification->is_read_by_user : 0,
                        'created_at' => $notification->created_at,
                        'time_ago' => $this->calculate_time_ago($notification->created_at),
                        'url' => $notification->url ?? '#',
                        'icon' => $this->get_notification_icon($notification->type)
                    ];
                }
            }
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'notifications' => $formatted_notifications,
                    'unread_count' => $unread_count,
                    'total_count' => count($notifications),
                    'debug' => [
                        'user_id' => $user_id,
                        'mp_id' => $public_user->mp_id,
                        'email' => $mp_email,
                        'limit' => $limit,
                        'method' => 'direct_model'
                    ]
                ], JSON_UNESCAPED_UNICODE));
            
        } else {
            // *** ใช้ notification_lib หากมี ***
            $notifications = $this->notification_lib->get_user_notifications('public', $limit);
            $unread_count = $this->notification_lib->get_unread_count('public');
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'notifications' => $notifications,
                    'unread_count' => $unread_count,
                    'debug' => [
                        'method' => 'notification_lib'
                    ]
                ], JSON_UNESCAPED_UNICODE));
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error in get_recent notifications: ' . $e->getMessage());
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล',
                'debug' => [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile())
                ]
            ], JSON_UNESCAPED_UNICODE));
    }
}
    
	
	
	private function calculate_time_ago($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) {
        return 'เมื่อสักครู่';
    } elseif ($time < 3600) {
        return floor($time / 60) . ' นาทีที่แล้ว';
    } elseif ($time < 86400) {
        return floor($time / 3600) . ' ชั่วโมงที่แล้ว';
    } elseif ($time < 2592000) {
        return floor($time / 86400) . ' วันที่แล้ว';
    } elseif ($time < 31536000) {
        return floor($time / 2592000) . ' เดือนที่แล้ว';
    } else {
        return floor($time / 31536000) . ' ปีที่แล้ว';
    }
}

	
	private function get_notification_icon($type) {
    $icons = [
        'qa_new' => 'bi bi-chat-square-dots',
        'qa_reply' => 'bi bi-reply',
        'qa' => 'bi bi-question-circle',
        'test' => 'bi bi-flask',
        'system' => 'bi bi-gear',
        'critical' => 'bi bi-exclamation-triangle',
        'info' => 'bi bi-info-circle',
        'warning' => 'bi bi-exclamation-circle',
        'success' => 'bi bi-check-circle'
    ];
    
    return $icons[$type] ?? 'bi bi-bell';
}
	
    /**
     * ทำเครื่องหมายการแจ้งเตือนว่าอ่านแล้ว (AJAX)
     */
    public function mark_as_read()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }
            
            $notification_id = $this->input->post('notification_id');
            
            if (!$notification_id) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'ไม่พบ notification ID'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }
            
            // *** ใช้ method ใหม่ที่รองรับ Individual Read Status ***
            $result = $this->notification_lib->mark_as_read($notification_id);
            
            if ($result) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'message' => 'อัปเดตสถานะสำเร็จ'
                    ], JSON_UNESCAPED_UNICODE));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'ไม่สามารถอัปเดตสถานะได้'
                    ], JSON_UNESCAPED_UNICODE));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error in mark_as_read: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการอัปเดต'
                ], JSON_UNESCAPED_UNICODE));
        }
    }
	
	
	
    /**
     * หน้าแสดงการแจ้งเตือนทั้งหมด
     * *** แก้ไข path ของ view ***
     */
  public function all()
{
    try {
        $data = array();
        $data['page_title'] = 'การแจ้งเตือนทั้งหมด';
        
        // Pagination config
        $this->load->library('pagination');
        $limit = 20;
        $start = $this->uri->segment(3) ? $this->uri->segment(3) : 0;
        
        // *** แก้ไข: ดึง user_id จาก tbl_member_public.id โดยตรง ***
        $mp_email = $this->session->userdata('mp_email');
        $user_id = null;
        
        if ($mp_email) {
            $public_user = $this->db->select('id')
                                   ->where('mp_email', $mp_email)
                                   ->get('tbl_member_public')
                                   ->row();
            if ($public_user) {
                $user_id = $public_user->id;
            }
        }
        
        if ($user_id) {
            // ใช้ระบบ Individual Read Status
            $data['notifications'] = $this->Notification_model->get_by_role_for_user(
                'public', 
                $user_id, 
                'public', 
                $limit, 
                $start
            );
            
            $data['total_notifications'] = $this->Notification_model->count_notifications_by_role('public');
            $data['unread_count'] = $this->Notification_model->count_unread_for_user(
                'public', 
                $user_id, 
                'public'
            );
        } else {
            $data['notifications'] = [];
            $data['total_notifications'] = 0;
            $data['unread_count'] = 0;
        }
        
        // Pagination config
        $config['base_url'] = site_url('notifications/all');
        $config['total_rows'] = $data['total_notifications'];
        $config['per_page'] = $limit;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'หน้าแรก';
        $config['last_link'] = 'หน้าสุดท้าย';
        $config['next_link'] = 'ถัดไป';
        $config['prev_link'] = 'ก่อนหน้า';
        $config['full_tag_open'] = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '</span></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');
        
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        
        // โหลด view
        $this->load->view('public_user/templates/header', $data);
        $this->load->view('public_user/notifications_all', $data);
        $this->load->view('public_user/templates/footer', $data);
        
    } catch (Exception $e) {
        log_message('error', 'Error in notifications/all: ' . $e->getMessage());
        show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage());
    }
}
    
    
    /**
     * ทำเครื่องหมายทุกการแจ้งเตือนว่าอ่านแล้ว
     */
   public function mark_all_as_read()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }
            
            // *** ใช้ method ใหม่ที่รองรับ Individual Read Status ***
            $result = $this->notification_lib->mark_all_as_read('public');
            
            if ($result) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'message' => 'ทำเครื่องหมายทั้งหมดสำเร็จ'
                    ], JSON_UNESCAPED_UNICODE));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'ไม่สามารถทำเครื่องหมายได้'
                    ], JSON_UNESCAPED_UNICODE));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error in mark_all_as_read: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการอัปเดต'
                ], JSON_UNESCAPED_UNICODE));
        }
    }
	
	
	
	
	private function get_current_user_info()
{
    $mp_email = $this->session->userdata('mp_email');
    $m_id = $this->session->userdata('m_id');
    $m_email = $this->session->userdata('m_email');
    
    if ($mp_email) {
        // ดึง id จาก tbl_member_public
        $public_user = $this->db->select('id')
                               ->where('mp_email', $mp_email)
                               ->get('tbl_member_public')
                               ->row();
        
        if ($public_user) {
            return [
                'user_id' => $public_user->id, // ใช้ id แทน mp_id
                'user_type' => 'public',
                'role' => 'public'
            ];
        }
    } elseif ($m_id && $m_email) {
        return [
            'user_id' => $m_id,
            'user_type' => 'staff',
            'role' => 'admin'
        ];
    }
    
    return [
        'user_id' => null,
        'user_type' => 'guest',
        'role' => 'guest'
    ];

 }		
    
    /**
     * ลบการแจ้งเตือน (archive)
     */
   public function archive()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }
            
            $notification_id = $this->input->post('notification_id');
            
            if (!$notification_id) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'ไม่พบ notification ID'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }
            
            $result = $this->Notification_model->archive_notification($notification_id, $this->session->userdata('mp_id'));
            
            if ($result) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'message' => 'ลบการแจ้งเตือนสำเร็จ'
                    ], JSON_UNESCAPED_UNICODE));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'ไม่สามารถลบได้'
                    ], JSON_UNESCAPED_UNICODE));
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error in archive notification: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการลบ'
                ], JSON_UNESCAPED_UNICODE));
        }
    }
    
    /**
     * สร้างการแจ้งเตือนทดสอบ (เฉพาะ development)
     */
    public function test_notification()
    {
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }
        
        try {
            // สร้างการแจ้งเตือนทดสอบ
            $result = $this->notification_lib->create([
                'type' => 'test',
                'title' => 'การแจ้งเตือนทดสอบ',
                'message' => 'นี่คือการแจ้งเตือนทดสอบระบบ เวลา: ' . date('H:i:s'),
                'priority' => 'normal',
                'icon' => 'fas fa-flask',
                'target_role' => 'public',
                'url' => site_url('notifications/all'),
                'data' => [
                    'test' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'user_agent' => $this->input->user_agent()
                ]
            ]);
            
            if ($result) {
                echo "<h3>✅ การแจ้งเตือนทดสอบถูกสร้างเรียบร้อยแล้ว</h3>";
                echo "<p>ตรวจสอบการแจ้งเตือนได้ที่: <a href='" . site_url('notifications/all') . "'>ดูการแจ้งเตือนทั้งหมด</a></p>";
                echo "<p>กลับไปหน้าหลัก: <a href='" . site_url('service_systems') . "'>หน้าระบบบริการ</a></p>";
            } else {
                echo "<h3>❌ เกิดข้อผิดพลาดในการสร้างการแจ้งเตือน</h3>";
            }
            
        } catch (Exception $e) {
            echo "<h3>❌ Error: " . $e->getMessage() . "</h3>";
        }
    }

    /**
     * Debug ระบบการแจ้งเตือน
     */
    public function debug()
    {
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }

        echo "<h2>🔍 Debug ระบบการแจ้งเตือน</h2>";
        echo "<style>body{font-family: Arial; margin: 20px;} .debug{background: #f5f5f5; padding: 10px; margin: 10px 0; border-radius: 5px;}</style>";
        
        // ตรวจสอบ session
        echo "<div class='debug'>";
        echo "<h3>📋 Session Information</h3>";
        echo "mp_id: " . ($this->session->userdata('mp_id') ?: 'NULL') . "<br>";
        echo "m_id: " . ($this->session->userdata('m_id') ?: 'NULL') . "<br>";
        echo "</div>";

        // ตรวจสอบการเชื่อมต่อฐานข้อมูล
        echo "<div class='debug'>";
        echo "<h3>🗄️ Database Connection</h3>";
        $test = $this->Notification_model->test_connection();
        echo "Status: " . $test['status'] . "<br>";
        echo "Database: " . $test['database'] . "<br>";
        if (isset($test['total_notifications'])) {
            echo "Total Notifications: " . $test['total_notifications'] . "<br>";
        }
        echo "</div>";

        // ตรวจสอบการแจ้งเตือน
        echo "<div class='debug'>";
        echo "<h3>🔔 Notifications Check</h3>";
        $notifications = $this->Notification_model->get_notifications_by_role('public', 5);
        $unread_count = $this->Notification_model->count_unread_by_role('public');
        echo "Public Notifications Found: " . count($notifications) . "<br>";
        echo "Unread Count: " . $unread_count . "<br>";
        
        if (!empty($notifications)) {
            echo "<h4>Recent Notifications:</h4>";
            foreach ($notifications as $i => $notif) {
                echo ($i + 1) . ". " . htmlspecialchars($notif->title) . " (ID: {$notif->notification_id}, Read: " . ($notif->is_read ? 'Yes' : 'No') . ")<br>";
            }
        }
        echo "</div>";

        echo "<div class='debug'>";
        echo "<h3>🔗 Useful Links</h3>";
        echo "<a href='" . site_url('notifications/test_notification') . "'>สร้างการแจ้งเตือนทดสอบ</a><br>";
        echo "<a href='" . site_url('notifications/all') . "'>ดูการแจ้งเตือนทั้งหมด</a><br>";
        echo "<a href='" . site_url('service_systems') . "'>กลับหน้าหลัก</a><br>";
        echo "</div>";
    }
	
	
	
	
	
	

	
	
}

?>