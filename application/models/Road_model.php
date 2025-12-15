<?php
class Road_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    public function list_all()
    {
        $this->db->order_by('road_id', 'DESC');
        $query = $this->db->get('tbl_road');
        return $query->result();
    }

    //show form edit
    public function read($road_id)
    {
        $this->db->where('road_id', $road_id);
        $query = $this->db->get('tbl_road');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit($road_id)
    {

        $data = array(
            'road_code' => $this->input->post('road_code'),
            'road_name' => $this->input->post('road_name'),
            'road_distance' => $this->input->post('road_distance'),
            'road_explore' => $this->input->post('road_explore'),
            'road_responsibility' => $this->input->post('road_responsibility'),
            'road_light' => $this->input->post('road_light'),
            'road_phone' => $this->input->post('road_phone'),
            'road_gravel' => $this->input->post('road_gravel'),
            'road_gravel_width' => $this->input->post('road_gravel_width'),
            'road_gravel_repair' => $this->input->post('road_gravel_repair'),
            'road_concrete' => $this->input->post('road_concrete'),
            'road_concrete_width' => $this->input->post('road_concrete_width'),
            'road_concrete_repair' => $this->input->post('road_concrete_repair'),
            'road_paved' => $this->input->post('road_paved'),
            'road_paved_width' => $this->input->post('road_paved_width'),
            'road_paved_repair' => $this->input->post('road_paved_repair'),
            'road_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
        );

        $this->db->where('road_id', $road_id);
        $query = $this->db->update('tbl_road', $data);

        $this->space_model->update_server_current();


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
}
