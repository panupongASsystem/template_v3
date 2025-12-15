<?php
// application/controllers/Google_drive_user.php
defined('BASEPATH') OR exit('No direct script access allowed');
// ===========================================
// Model สำหรับ Google Drive User System
// ===========================================

/**
 * Google Drive User Model
 * สำหรับจัดการข้อมูลการแชร์และคำขอเข้าใช้งาน
 */
class Google_drive_user_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * ดึงโฟลเดอร์ที่ User สามารถเข้าถึงได้
     */
    public function get_accessible_folders($member_id) {
        $user = $this->db->where('m_id', $member_id)->get('tbl_member')->row();
        if (!$user) return [];

        $position_id = $user->ref_pid;
        
        $this->db->select('*')
                ->from('tbl_google_drive_system_folders')
                ->where('is_active', 1);

        // กำหนดสิทธิ์ตามตำแหน่ง
        if (in_array($position_id, [1, 2])) {
            // Admin - ทุกโฟลเดอร์
        } elseif ($position_id == 3) {
            // User Admin - ยกเว้น Admin folder
            $this->db->where('folder_type !=', 'admin');
        } else {
            // End User - เฉพาะตำแหน่งและ Shared
            $this->db->where('(folder_type = "shared" OR created_for_position = ' . $position_id . ')');
        }

        return $this->db->order_by('folder_type', 'ASC')
                       ->order_by('folder_name', 'ASC')
                       ->get()
                       ->result();
    }

    /**
     * ดึงรายการคำขอที่รออนุมัติ (สำหรับ Admin)
     */
    public function get_pending_requests() {
        return $this->db->select('ar.*, m.m_fname, m.m_lname, p.pname')
                       ->from('tbl_google_drive_access_requests ar')
                       ->join('tbl_member m', 'ar.member_id = m.m_id')
                       ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                       ->where('ar.request_status', 'pending')
                       ->order_by('ar.created_at', 'desc')
                       ->get()
                       ->result();
    }

    /**
     * อนุมัติคำขอเข้าใช้งาน
     */
    public function approve_request($request_id, $approved_by) {
        return $this->db->where('id', $request_id)
                       ->update('tbl_google_drive_access_requests', [
                           'request_status' => 'approved',
                           'approved_by' => $approved_by,
                           'approved_at' => date('Y-m-d H:i:s')
                       ]);
    }

    /**
     * ปฏิเสธคำขอเข้าใช้งาน
     */
    public function reject_request($request_id, $rejected_by) {
        return $this->db->where('id', $request_id)
                       ->update('tbl_google_drive_access_requests', [
                           'request_status' => 'rejected',
                           'approved_by' => $rejected_by,
                           'approved_at' => date('Y-m-d H:i:s')
                       ]);
    }
}
?>