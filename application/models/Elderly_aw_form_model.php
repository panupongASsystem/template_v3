<?php
class Elderly_aw_form_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    public function list_all()
    {
        $this->db->order_by('elderly_aw_form_id', 'asc');
        $query = $this->db->get('tbl_elderly_aw_form');
        return $query->result();
    }

    //show form edit
    public function read($elderly_aw_form_id)
    {
        $this->db->where('elderly_aw_form_id', $elderly_aw_form_id);
        $query = $this->db->get('tbl_elderly_aw_form');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit($elderly_aw_form_id)
    {
        $old_document = $this->db->get_where('tbl_elderly_aw_form', array('elderly_aw_form_id' => $elderly_aw_form_id))->row();

        $update_doc_file = !empty($_FILES['elderly_aw_form_file']['name']) && $old_document->elderly_aw_form_file != $_FILES['elderly_aw_form_file']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/file/' . $old_document->elderly_aw_form_file;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['elderly_aw_form_file']['name'])) {
                $total_space_required += $_FILES['elderly_aw_form_file']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('elderly_aw_form/editing');
                return;
            }

            $config['upload_path'] = './docs/file';
            $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|ppt|pptx';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('elderly_aw_form_file')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->elderly_aw_form_file;
        }

        $data = array(
            'elderly_aw_form_name' => $this->input->post('elderly_aw_form_name'),
            'elderly_aw_form_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            'elderly_aw_form_file' => $filename
        );

        $this->db->where('elderly_aw_form_id', $elderly_aw_form_id);
        $query = $this->db->update('tbl_elderly_aw_form', $data);

        $this->space_model->update_server_current();


        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function elderly_aw_form_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_elderly_aw_form');
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view()
    {
        $this->db->where('elderly_aw_form_id', 1);
        $this->db->set('elderly_aw_form_view', 'elderly_aw_form_view + 1', false); // บวกค่า elderly_aw_form_view ทีละ 1
        $this->db->update('tbl_elderly_aw_form');
    }
    public function increment_download_elderly_aw_form()
    {
        $this->db->where('elderly_aw_form_id', 1);
        $this->db->set('elderly_aw_form_download', 'elderly_aw_form_download + 1', false); // บวกค่า elderly_aw_form_view ทีละ 1
        $this->db->update('tbl_elderly_aw_form');
    }
}
