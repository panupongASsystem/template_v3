<?php
class Space_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ตรวจสอบการใช้พื้นที่ของโฟล์เดอร์ folder storage
    public function get_used_space()
    {
        $upload_folder = './docs'; // ตำแหน่งของโฟลเดอร์ที่คุณต้องการ

        $used_space = $this->calculateFolderSize($upload_folder);

        $used_space_mb = $used_space / (1024 * 1024 * 1024); // แปลงจาก byte เป็น GB
        return $used_space_mb;
    }

    private function calculateFolderSize($folder)
    {
        $used_space = 0;
        $files = scandir($folder);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $path = $folder . '/' . $file;
                if (is_file($path)) {
                    $used_space += filesize($path);
                } elseif (is_dir($path)) {
                    $used_space += $this->calculateFolderSize($path);
                }
            }
        }
        return $used_space;
    }

    public function update_server_current()
    {
        // อ่านค่าพื้นที่ที่ใช้งานปัจจุบัน
        $used_space_mb = $this->space_model->get_used_space();

        // อัพเดตค่า server_current ในฐานข้อมูล
        $this->load->database();
        $this->db->where('server_id', 1); // แทนตามความเหมาะสมกับโครงสร้างของฐานข้อมูลของคุณ
        $this->db->set('server_current', $used_space_mb);
        $this->db->update('tbl_server'); // แทนตามชื่อตารางของคุณ
        // echo 'Server current updated successfully.';
    }

    public function list_all()
    {
        $this->db->order_by('server_storage', 'DESC');
        $query = $this->db->get('tbl_server');
        return $query->result();
    }

    public function get_limit_storage()
    {
        // ดึงข้อมูล server_storage จากตาราง tbl_server
        $this->db->select('server_storage');
        $query = $this->db->get('tbl_server');
        
        // ตรวจสอบว่าคิวรี่สำเร็จหรือไม่
        if ($query->num_rows() > 0) {
            // ดึงข้อมูลแถวแรก
            $row = $query->row();
            
            // นำค่า server_storage มาใช้เป็นค่า upload_limit_mb
            $upload_limit_mb = $row->server_storage;
            
            // ส่งค่า upload_limit_mb กลับ
            return $upload_limit_mb;
        } else {
            // ถ้าไม่มีข้อมูลในตาราง tbl_server ให้ส่งค่าเริ่มต้นที่คุณต้องการหรือโยนข้อผิดพลาด
            return 0; // หรือให้กำหนดค่าเริ่มต้นตามที่คุณต้องการ
        }
    }
    
	// ตัดชื่อไฟล์ให้สั้้นลง
    public function sanitize_filename($filename, $max_length = 250)
    {
        if (strlen($filename) <= $max_length) {
            return $filename;
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);

        // คำนวณความยาวที่เหลือ
        $available_length = $max_length - strlen($extension) - 1; // -1 สำหรับจุด

        return substr($name, 0, $available_length) . '.' . $extension;
    }
}
