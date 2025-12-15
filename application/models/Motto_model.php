<?php
class Motto_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
		// log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function list_all()
    {
        $this->db->order_by('motto_id', 'DESC');
        $query = $this->db->get('tbl_motto');
        return $query->result();
    }

    //show form edit
    public function read($motto_id)
    {
        $this->db->where('motto_id', $motto_id);
        $query = $this->db->get('tbl_motto');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function add()
    {
        $filename = '';
        if (!empty($_FILES['motto_img']['name'])) {
            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('motto_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        }

        $data = array(
            'motto_detail' => $this->input->post('motto_detail'),
            'motto_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            'motto_img' => $filename
        );

        return $this->db->insert('tbl_motto', $data);
    }


    public function edit($motto_id)
    {
        $old_document = $this->db->get_where('tbl_motto', array('motto_id' => $motto_id))->row();

        $update_doc_file = !empty($_FILES['motto_img']['name']) && $old_document->motto_img != $_FILES['motto_img']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->motto_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['motto_img']['name'])) {
                $total_space_required += $_FILES['motto_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('motto/editing_motto');
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('motto_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->motto_img;
        }

        $data = array(
            'motto_detail' => $this->input->post('motto_detail'),
            'motto_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            'motto_img' => $filename
        );

        $this->db->where('motto_id', $motto_id);
        $query = $this->db->update('tbl_motto', $data);

        $this->space_model->update_server_current();

		 // บันทึก log การลบ =================================================
            $this->log_model->add_log(
                'แก้ไข',
                'ข้อมูลคำขวัญ',
                $old_document->motto_detail,
                $motto_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
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

    public function motto_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_motto');
        $query = $this->db->get();
        return $query->result();
    }
}
