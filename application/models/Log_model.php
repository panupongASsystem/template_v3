<?php
class Log_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * บันทึก log การดำเนินการของผู้ใช้
     * 
     * @param string $action การดำเนินการ (เพิ่ม, แก้ไข, ลบ, เข้าชม, ดาวน์โหลด)
     * @param string $menu ชื่อเมนู
     * @param string $item_name ชื่อรายการที่ดำเนินการ
     * @param int $item_id ID ของรายการที่ดำเนินการ
     * @param array $additional_data ข้อมูลเพิ่มเติม (optional)
     */
    public function add_log($action, $menu, $item_name = null, $item_id = null, $additional_data = null)
    {
        // ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
        if (!$this->session->userdata('m_id')) {
            return false;
        }

        $log_data = array(
            'user_id' => $this->session->userdata('m_id'),
            'username' => $this->session->userdata('m_username'),
            'full_name' => $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname'),
            'action' => $action,
            'menu' => $menu,
            'item_name' => $item_name,
            'item_id' => $item_id,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'additional_data' => $additional_data ? json_encode($additional_data, JSON_UNESCAPED_UNICODE) : null
        );

        return $this->db->insert('tbl_user_logs', $log_data);
    }

    /**
     * ดึงรายการ logs ทั้งหมด
     * 
     * @param int $limit จำนวนข้อมูลที่ต้องการ
     * @param int $offset เริ่มต้นจากตำแหน่งไหน
     * @param array $filters ตัวกรองข้อมูล
     * @return array
     */
    public function get_logs($limit = 100, $offset = 0, $filters = array())
    {
        $this->db->select('*');
        $this->db->from('tbl_user_logs');

        // ใช้ filters
        if (!empty($filters['user_id'])) {
            $this->db->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['action'])) {
            $this->db->where('action', $filters['action']);
        }
        if (!empty($filters['menu'])) {
            $this->db->where('menu', $filters['menu']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(created_at) >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(created_at) <=', $filters['date_to']);
        }

        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * นับจำนวน logs ทั้งหมด
     */
    public function count_logs($filters = array())
    {
        $this->db->from('tbl_user_logs');

        if (!empty($filters['user_id'])) {
            $this->db->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['action'])) {
            $this->db->where('action', $filters['action']);
        }
        if (!empty($filters['menu'])) {
            $this->db->where('menu', $filters['menu']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(created_at) >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(created_at) <=', $filters['date_to']);
        }

        return $this->db->count_all_results();
    }

    /**
     * ดึง logs ของผู้ใช้คนหนึ่ง
     */
    public function get_user_logs($user_id, $limit = 50)
    {
        $this->db->select('*');
        $this->db->from('tbl_user_logs');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * ดึงสถิติการใช้งาน
     */
    public function get_activity_stats($days = 30)
    {
        // สถิติการดำเนินการในช่วง x วันที่ผ่านมา
        $this->db->select('action, COUNT(*) as count');
        $this->db->from('tbl_user_logs');
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $this->db->group_by('action');
        $this->db->order_by('count', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * ลบ logs เก่าที่เกินกำหนด
     */
    public function clean_old_logs($days = 90)
    {
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $this->db->where('created_at <', $cutoff_date);
        return $this->db->delete('tbl_user_logs');
    }
	
	public function get_dashboard_stats()
{
    $stats = [];
    
    // จำนวนกิจกรรมวันนี้
    $this->db->where('DATE(created_at)', date('Y-m-d'));
    $stats['today_count'] = $this->db->count_all_results('tbl_user_logs');
    
    // จำนวนผู้ใช้ที่ active วันนี้
    $this->db->select('DISTINCT user_id');
    $this->db->where('DATE(created_at)', date('Y-m-d'));
    $stats['active_users_today'] = $this->db->count_all_results('tbl_user_logs');
    
    // กิจกรรมที่นิยมในสัปดาห์นี้
    $this->db->select('action, COUNT(*) as count');
    $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')));
    $this->db->group_by('action');
    $this->db->order_by('count', 'DESC');
    $this->db->limit(3);
    $stats['popular_actions'] = $this->db->get('tbl_user_logs')->result();
    
    return $stats;
}

public function get_hourly_activity($date = null)
{
    if (!$date) $date = date('Y-m-d');
    
    $this->db->select('HOUR(created_at) as hour, COUNT(*) as count');
    $this->db->where('DATE(created_at)', $date);
    $this->db->group_by('HOUR(created_at)');
    $this->db->order_by('hour', 'ASC');
    
    return $this->db->get('tbl_user_logs')->result();
}

public function get_recent_logins($limit = 10)
{
    $this->db->select('*');
    $this->db->where('action', 'เข้าสู่ระบบ');
    $this->db->order_by('created_at', 'DESC');
    $this->db->limit($limit);
    
    return $this->db->get('tbl_user_logs')->result();
}
}