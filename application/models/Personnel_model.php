<?php
class Personnel_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    public function add_Personnel()
    {

        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        // Calculate the total space required for all files
        $total_space_required = 0;
        if (!empty($_FILES['personnel_img']['name'])) {
            $total_space_required += $_FILES['personnel_img']['size'];
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('personnel/adding_Personnel');
            return;
        }

        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $config);

        // Upload main file
        if (!$this->upload->do_upload('personnel_img')) {
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('personnel/adding_Personnel'); // กลับไปหน้าเดิม
            return;
        }
        $data = $this->upload->data();
        $filename =  $data['file_name'];


        $data = array(
            'personnel_name' => $this->input->post('personnel_name'),
            'personnel_lastname' => $this->input->post('personnel_lastname'),
            'personnel_role' => $this->input->post('personnel_role'),
            'personnel_phone' => $this->input->post('personnel_phone'),
            'personnel_gname' => $this->input->post('personnel_gname'),
            'personnel_dname' => $this->input->post('personnel_dname'),
            'personnel_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            'personnel_img' => $filename
        );

        $query = $this->db->insert('tbl_personnel', $data);

        $this->space_model->update_server_current();


        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('Error !');";
            echo "</script>";
        }
    }

    public function get_group()
    {
        $this->db->distinct();
        $this->db->select('pgroup_gname');
        $query = $this->db->get('tbl_personnel_group');
        return $query->result();
    }

    public function get_department_by_group($group_name)
    {
        $this->db->distinct();
        $this->db->select('pgroup_dname');
        $this->db->where('pgroup_gname', $group_name);
        $query = $this->db->get('tbl_personnel_group');
        return $query->result();
    }


    public function list_all()
    {
        $this->db->order_by('personnel_id', 'DESC');
        $query = $this->db->get('tbl_personnel');
        return $query->result();
    }

    //show form edit
    public function read($personnel_id)
    {
        $this->db->where('personnel_id', $personnel_id);
        $query = $this->db->get('tbl_personnel');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit_Personnel($personnel_id)
    {
        $old_document = $this->db->get_where('tbl_personnel', array('personnel_id' => $personnel_id))->row();

        $update_doc_file = !empty($_FILES['personnel_img']['name']) && $old_document->personnel_img != $_FILES['personnel_img']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->personnel_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['personnel_img']['name'])) {
                $total_space_required += $_FILES['personnel_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('personnel/editing_Personnel');
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('personnel_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->personnel_img;
        }

        $data = array(
            'personnel_name' => $this->input->post('personnel_name'),
            'personnel_lastname' => $this->input->post('personnel_lastname'),
            'personnel_role' => $this->input->post('personnel_role'),
            'personnel_phone' => $this->input->post('personnel_phone'),
            'personnel_gname' => $this->input->post('personnel_gname'),
            'personnel_dname' => $this->input->post('personnel_dname'),
            'personnel_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            'personnel_img' => $filename
        );

        $this->db->where('personnel_id', $personnel_id);
        $query = $this->db->update('tbl_personnel', $data);

        $this->space_model->update_server_current();


        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function del_Personnel($personnel_id)
    {
        $old_document = $this->db->get_where('tbl_personnel', array('personnel_id' => $personnel_id))->row();

        $old_file_path = './docs/img/' . $old_document->personnel_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_personnel', array('personnel_id' => $personnel_id));
    }

    public function updatePersonnelStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $personnelId = $this->input->post('personnel_id'); // รับค่า personnel_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_personnel ในฐานข้อมูลของคุณ
            $data = array(
                'personnel_status' => $newStatus
            );
            $this->db->where('personnel_id', $personnelId); // ระบุ personnel_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_personnel', $data);

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
