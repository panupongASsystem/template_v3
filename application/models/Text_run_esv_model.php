<?php
class Text_run_esv_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    public function add_text_run_esv()
    {
        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        // Calculate the total space required for all files
        $total_space_required = 0;
        if (!empty($_FILES['text_run_esv_img']['name'])) {
            $total_space_required += $_FILES['text_run_esv_img']['size'];
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('text_run_esv/adding_text_run_esv');
            return;
        }

        // Upload configuration
        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $config);

        // Upload main file
        if (!$this->upload->do_upload('text_run_esv_img')) {
            // If the file size exceeds the max_size, set flash data and redirect
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('text_run_esv/adding_text_run_esv');
            return;
        }

        $data = $this->upload->data();
        $filename = $data['file_name'];

        $data = array(
            'text_run_esv_name' => $this->input->post('text_run_esv_name'),
            'text_run_esv_link' => $this->input->post('text_run_esv_link'),
            'text_run_esv_by' => $this->session->userdata('m_fname'),
            'text_run_esv_img' => $filename
        );

        $query = $this->db->insert('tbl_text_run_esv', $data);

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
        $this->db->order_by('text_run_esv_id', 'DESC');
        $query = $this->db->get('tbl_text_run_esv');
        return $query->result();
    }

    //show form edit
    public function read($text_run_esv_id)
    {
        $this->db->where('text_run_esv_id', $text_run_esv_id);
        $query = $this->db->get('tbl_text_run_esv');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit_text_run_esv($text_run_esv_id)
    {
        $old_document = $this->db->get_where('tbl_text_run_esv', array('text_run_esv_id' => $text_run_esv_id))->row();

        $update_doc_file = !empty($_FILES['text_run_esv_img']['name']) && $old_document->text_run_esv_img != $_FILES['text_run_esv_img']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->text_run_esv_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['text_run_esv_img']['name'])) {
                $total_space_required += $_FILES['text_run_esv_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('text_run_esv/editing_text_run_esv');
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('text_run_esv_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->text_run_esv_img;
        }

        $data = array(
            'text_run_esv_name' => $this->input->post('text_run_esv_name'),
            'text_run_esv_link' => $this->input->post('text_run_esv_link'),
            'text_run_esv_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            'text_run_esv_img' => $filename
        );

        $this->db->where('text_run_esv_id', $text_run_esv_id);
        $query = $this->db->update('tbl_text_run_esv', $data);

        $this->space_model->update_server_current();


        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function del_text_run_esv($text_run_esv_id)
    {
        $old_document = $this->db->get_where('tbl_text_run_esv', array('text_run_esv_id' => $text_run_esv_id))->row();

        $old_file_path = './docs/img/' . $old_document->text_run_esv_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_text_run_esv', array('text_run_esv_id' => $text_run_esv_id));
    }

    public function updatetext_run_esvStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $text_run_esvId = $this->input->post('text_run_esv_id'); // รับค่า text_run_esv_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_text_run_esv ในฐานข้อมูลของคุณ
            $data = array(
                'text_run_esv_status' => $newStatus
            );
            $this->db->where('text_run_esv_id', $text_run_esvId); // ระบุ text_run_esv_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_text_run_esv', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function text_run_esv_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_text_run_esv');
        $this->db->where('tbl_text_run_esv.text_run_esv_status', 'show');
        $this->db->order_by('tbl_text_run_esv.text_run_esv_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
}
