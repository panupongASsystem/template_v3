<?php
class Line_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function save_line_user($data) {
        // ตรวจสอบว่ามี user_id นี้อยู่แล้วหรือไม่
        $this->db->where('line_user_id', $data['line_user_id']);
        $query = $this->db->get('tbl_line');
        
        if ($query->num_rows() > 0) {
            // ถ้ามีอยู่แล้ว ให้อัพเดต
            $this->db->where('line_user_id', $data['line_user_id']);
            return $this->db->update('tbl_line', $data);
        } else {  
            return $this->db->insert('tbl_line', $data);
        }
    }

    // เช็คข้อมูลซ้ำ
    public function add()
    {
        $line_name = $this->input->post('line_name');
        $this->db->select('line_name');
        $this->db->where('line_name', $line_name);
        $query = $this->db->get('tbl_line');
        $num = $query->num_rows();
        if ($num > 0) {
            $this->session->set_flashdata('save_again', TRUE);
        } else {

            $data = array(
                'line_name' => $this->input->post('line_name'),
                'line_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            );
            $query = $this->db->insert('tbl_line', $data);

            $this->session->set_flashdata('save_success', TRUE);
        }
    }

    public function list()
    {
        $this->db->select('*');
        $this->db->from('tbl_line');
        $this->db->order_by('tbl_line.line_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function read($line_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_line');
        $this->db->where('tbl_line.line_id', $line_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return false;
    }

    public function edit($line_id)
    {
        $line_name = $this->input->post('line_name');

        // Check if the new line_name value is not already in the database for other records.
        $this->db->where('line_name', $line_name);
        $this->db->where_not_in('line_id', $line_id); // Exclude the current record being edited.
        $query = $this->db->get('tbl_line');
        $num = $query->num_rows();

        if ($num > 0) {
            // A record with the same line_name already exists in the database.
            $this->session->set_flashdata('save_again', TRUE);
        } else {
            // Update the record.
            $data = array(
                'line_name' => $line_name,
                'line_by' => $this->session->userdata('m_fname'), // Add the name of the person updating the record.
            );

            $this->db->where('line_id', $line_id);
            $this->db->update('tbl_line', $data);

            $this->session->set_flashdata('save_success', TRUE);
        }
    }

    public function del($line_id)
    {
        $this->db->delete('tbl_line', array('line_id' => $line_id));
    }

    public function update_line_status()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $lineId = $this->input->post('line_id'); // รับค่า line_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_line ในฐานข้อมูลของคุณ
            $data = array(
                'line_status' => $newStatus
            );
            $this->db->where('line_id', $lineId); // ระบุ line_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_line', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function update_line_user_id($line_name, $line_user_id) {
        $data = array(
            'line_user_id' => $line_user_id
        );
        
        $this->db->where('line_name', $line_name);
        $this->db->where('line_status', 'show');
        return $this->db->update('tbl_line', $data);
    }
}
