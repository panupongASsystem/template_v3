<?php
class Laws_rm_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    public function add()
    {
        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        // Calculate the total space required for all files
        $total_space_required = 0;
        if (!empty($_FILES['laws_rm_pdf']['name'])) {
            $total_space_required += $_FILES['laws_rm_pdf']['size'];
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('laws_rm/adding_laws_rm');
            return;
        }

        // Upload configuration
        $config['upload_path'] = './docs/file';
        $config['allowed_types'] = 'pdf';
        $this->load->library('upload', $config);

        // Upload main file
        if (!$this->upload->do_upload('laws_rm_pdf')) {
            // If the file size exceeds the max_size, set flash data and redirect
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('laws_rm/adding_laws_rm');
            return;
        }

        $data = $this->upload->data();
        $filename = $data['file_name'];

        $data = array(
            'laws_rm_name' => $this->input->post('laws_rm_name'),
            'laws_rm_date' => $this->input->post('laws_rm_date'),
            'laws_rm_by' => $this->session->userdata('m_fname'),
            'laws_rm_pdf' => $filename
        );

        $query = $this->db->insert('tbl_laws_rm', $data);

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
        $this->db->order_by('laws_rm_id', 'DESC');
        $query = $this->db->get('tbl_laws_rm');
        return $query->result();
    }

    //show form edit
    public function read($laws_rm_id)
    {
        $this->db->where('laws_rm_id', $laws_rm_id);
        $query = $this->db->get('tbl_laws_rm');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit($laws_rm_id)
    {
        $old_document = $this->db->get_where('tbl_laws_rm', array('laws_rm_id' => $laws_rm_id))->row();

        $update_doc_file = !empty($_FILES['laws_rm_pdf']['name']) && $old_document->laws_rm_pdf != $_FILES['laws_rm_pdf']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/file/' . $old_document->laws_rm_pdf;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['laws_rm_pdf']['name'])) {
                $total_space_required += $_FILES['laws_rm_pdf']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('laws_rm/editing_laws_rm');
                return;
            }

            $config['upload_path'] = './docs/file';
            $config['allowed_types'] = 'pdf';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('laws_rm_pdf')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->laws_rm_pdf;
        }

        $data = array(
            'laws_rm_name' => $this->input->post('laws_rm_name'),
            'laws_rm_date' => $this->input->post('laws_rm_date'),
            'laws_rm_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            'laws_rm_pdf' => $filename
        );

        $this->db->where('laws_rm_id', $laws_rm_id);
        $query = $this->db->update('tbl_laws_rm', $data);

        $this->space_model->update_server_current();


        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function del_laws_rm($laws_rm_id)
    {
        $old_document = $this->db->get_where('tbl_laws_rm', array('laws_rm_id' => $laws_rm_id))->row();

        $old_file_path = './docs/file/' . $old_document->laws_rm_pdf;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_laws_rm', array('laws_rm_id' => $laws_rm_id));
    }

    public function updatelaws_rmStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $laws_rmId = $this->input->post('laws_rm_id'); // รับค่า laws_rm_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_laws_rm ในฐานข้อมูลของคุณ
            $data = array(
                'laws_rm_status' => $newStatus
            );
            $this->db->where('laws_rm_id', $laws_rmId); // ระบุ laws_rm_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_laws_rm', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function laws_rm_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_laws_rm');
        $this->db->where('tbl_laws_rm.laws_rm_status', 'show');
        $this->db->order_by('tbl_laws_rm.laws_rm_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
}
