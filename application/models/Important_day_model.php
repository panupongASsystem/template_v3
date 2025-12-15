<?php
class Important_day_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    // public function get_control_important_days($control_important_day_status = null)
    // {
    //     $this->db->select('*');
    //     $this->db->from('tbl_control_important_day');

    //     if ($control_important_day_status) {
    //         $this->db->where('control_important_day_status', $control_important_day_status);
    //     }

    //     return $this->db->get()->result();
    // }

    // public function updateControlStatus($controlImportantDayId, $controlImportantDayStatus)
    // {
    //     $data = array(
    //         'control_important_day_status' => $controlImportantDayStatus
    //     );

    //     $this->db->where('control_important_day_id', $controlImportantDayId);
    //     $result = $this->db->update('tbl_control_important_day', $data);

    //     return $result;
    // }

    public function add_important_day()
    {
        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        // Calculate the total space required for all files
        $total_space_required = 0;
        if (!empty($_FILES['important_day_img']['name'])) {
            $total_space_required += $_FILES['important_day_img']['size'];
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('important_day/adding_important_day');
            return;
        }

        // Upload configuration
        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $config);

        // Upload main file
        if (!$this->upload->do_upload('important_day_img')) {
            // If the file size exceeds the max_size, set flash data and redirect
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('important_day/adding_important_day');
            return;
        }

        $data = $this->upload->data();
        $filename = $data['file_name'];

        $data = array(
            'important_day_name' => $this->input->post('important_day_name'),
            'important_day_link' => $this->input->post('important_day_link'),
            'important_day_by' => $this->session->userdata('m_fname'),
            'important_day_img' => $filename
        );

        $query = $this->db->insert('tbl_important_day', $data);

        $this->space_model->update_server_current();

        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('Error !');";
            echo "</script>";
        }
    }

    public function list_all()
    {
        $this->db->order_by('important_day_id', 'ASC');
        $query = $this->db->get('tbl_important_day');
        return $query->result();
    }

    //show form edit
    public function read($important_day_id)
    {
        $this->db->where('important_day_id', $important_day_id);
        $query = $this->db->get('tbl_important_day');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit_important_day($important_day_id)
    {
        $old_document = $this->db->get_where('tbl_important_day', array('important_day_id' => $important_day_id))->row();

        $update_doc_file = !empty($_FILES['important_day_img']['name']) && $old_document->important_day_img != $_FILES['important_day_img']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->important_day_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['important_day_img']['name'])) {
                $total_space_required += $_FILES['important_day_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('important_day/editing_important_day');
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('important_day_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->important_day_img;
        }

        $data = array(
            'important_day_name' => $this->input->post('important_day_name'),
            'important_day_link' => $this->input->post('important_day_link'),
            'important_day_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            'important_day_img' => $filename
        );

        $this->db->where('important_day_id', $important_day_id);
        $query = $this->db->update('tbl_important_day', $data);

        $this->space_model->update_server_current();


        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function del_important_day($important_day_id)
    {
        $old_document = $this->db->get_where('tbl_important_day', array('important_day_id' => $important_day_id))->row();

        $old_file_path = './docs/img/' . $old_document->important_day_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_important_day', array('important_day_id' => $important_day_id));
    }

    public function updateimportant_dayStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $important_dayId = $this->input->post('important_day_id'); // รับค่า important_day_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_important_day ในฐานข้อมูลของคุณ
            $data = array(
                'important_day_status' => $newStatus
            );
            $this->db->where('important_day_id', $important_dayId); // ระบุ important_day_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_important_day', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function important_day_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_important_day');
        $this->db->where('tbl_important_day.important_day_status', 'show');
        $query = $this->db->get();
        return $query->result();
    }

    public function control_list_all()
    {
        $this->db->order_by('control_important_day_id', 'ASC');
        $query = $this->db->get('tbl_control_important_day');
        return $query->result();
    }

    public function control_important_day_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_control_important_day');
        $this->db->where('tbl_control_important_day.control_important_day_id', '1');
        $query = $this->db->get();
        return $query->result();
    }

    public function updateControl_important_dayStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $control_important_dayId = $this->input->post('control_important_day_id'); // รับค่า important_day_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_important_day ในฐานข้อมูลของคุณ
            $data = array(
                'control_important_day_status' => $newStatus
            );
            $this->db->where('control_important_day_id', $control_important_dayId); // ระบุ important_day_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_control_important_day', $data);

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
