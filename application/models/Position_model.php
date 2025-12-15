<?php
class Position_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');

    }

    public function list_position()
    {
        $this->db->order_by('pid', 'ASC');
        $query = $this->db->get('tbl_position');
        return $query->result();
    }

    public function list_position_admin()
    {
        $this->db->order_by('pid', 'ASC');
        $this->db->where_in('pid', [1, 2, 3]); // ใช้ where_in สำหรับหลายค่า
        $query = $this->db->get('tbl_position');
        return $query->result();
    }


    public function list_position_back_office()
    {
        $this->db->select('*');
        $this->db->order_by('pid', 'ASC');
        $this->db->where_not_in('tbl_position.pid', [1, 2, 3]);
        // $this->db->where_in('pid', [2, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,]); // ใช้ where_in สำหรับหลายค่า
        $this->db->where('tbl_position.pstatus', 'show');
        $query = $this->db->get('tbl_position');
        return $query->result();
    }

    public function list_grant_user()
    {
        $this->db->order_by('grant_user_name', 'ASC');
        $this->db->where_not_in('grant_user_id', [1]);
        $query = $this->db->get('tbl_grant_user');
        return $query->result();
    }


    public function list()
    {
        $this->db->select('*');
        $this->db->from('tbl_position');
        $this->db->where_not_in('tbl_position.pid', [1, 2, 3]); // ปรับจาก != เป็น where_not_in
        $this->db->order_by('tbl_position.pid', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function read($pid)
    {
        $this->db->select('*');
        $this->db->from('tbl_position');
        $this->db->where('tbl_position.pid', $pid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return false;
    }

    public function edit($pid)
    {
        $pname = $this->input->post('pname');

        // Check if the new pname value is not already in the database for other records.
        $this->db->where('pname', $pname);
        $this->db->where_not_in('pid', $pid); // Exclude the current record being edited.
        $query = $this->db->get('tbl_position');
        $num = $query->num_rows();

        if ($num > 0) {
            // A record with the same pname already exists in the database.
            $this->session->set_flashdata('save_again', TRUE);
        } else {
            // Update the record.
            $data = array(
                'pname' => $pname,
                'pby' => $this->session->userdata('m_fname'), // Add the name of the person updating the record.
            );

            $this->db->where('pid', $pid);
            $this->db->update('tbl_position', $data);

            $this->space_model->update_server_current();
            $this->session->set_flashdata('save_success', TRUE);
        }
    }

    public function updateStructure_personnelStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $structure_personnelId = $this->input->post('pid'); // รับค่า pid
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_position ในฐานข้อมูลของคุณ
            $data = array(
                'pstatus' => $newStatus
            );
            $this->db->where('pid', $structure_personnelId); // ระบุ pid ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_position', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    // ใช้ใน helper และการแสดงผลทั้งเว็บไซต์
    public function get_personnel_by_id($pid)
    {
        $this->db->select('*');
        $this->db->from('tbl_position');
        $this->db->where('tbl_position.pid', $pid);
        $this->db->where('tbl_position.pstatus', 'show');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }
}