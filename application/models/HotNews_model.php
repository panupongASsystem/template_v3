<?php
class HotNews_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        $this->load->model('hotnews_model');
		// log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function list_all()
    {
        $this->db->order_by('hotNews_id', 'asc');
        $query = $this->db->get('tbl_hotnews');
        return $query->result();
    }

    //show form edit
    public function read($hotNews_id)
    {
        $this->db->where('hotNews_id', $hotNews_id);
        $query = $this->db->get('tbl_hotnews');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit_hotNews($hotNews_id)
    {
        // ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($hotNews_id);
		
        $data = array(
            'hotNews_text' => $this->input->post('hotNews_text'),
            'hotNews_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
        );

        $this->db->where('hotNews_id', $hotNews_id);
        $query = $this->db->update('tbl_hotnews', $data);

        $this->space_model->update_server_current();
		
		// === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->hotNews_text != $data['hotNews_text']) {
                $changes['hotNews_text'] = array(
                    'old' => $old_data->hotNews_text,
                    'new' => $data['hotNews_text']
                );
            }
        }

        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'ข่าวด่วน',
            $data['hotNews_text'],
            $hotNews_id,
            array(
                'changes' => $changes
            )
        );
        // =======================================================================


        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function del_hotNews($hotNews_id)
    {
        $this->db->delete('tbl_hotnews', array('hotNews_id' => $hotNews_id));
    }

    public function updateHotNewsStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $hotNewsId = $this->input->post('hotNews_id'); // รับค่า hotNews_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_hotNews ในฐานข้อมูลของคุณ
            $data = array(
                'hotNews_status' => $newStatus
            );
            $this->db->where('hotNews_id', $hotNewsId); // ระบุ hotNews_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_hotnews', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function hotnews_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_hotnews');
        $this->db->where('tbl_hotnews.hotNews_status', 'show');
        $this->db->order_by('tbl_hotnews.hotNews_id', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
}
