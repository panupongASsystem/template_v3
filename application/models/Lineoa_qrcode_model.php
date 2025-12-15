<?php
class Lineoa_qrcode_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        $this->load->model('log_model');
    }

    public function add_lineoa()
    {
        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        if (!empty($_FILES['lineoa_img']['name'])) {
            $total_space_required += $_FILES['lineoa_img']['size'];
        }

        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('publicize_ita_backend/adding_lineoa');
            return;
        }

        // Upload configuration
        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('lineoa_img')) {
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('publicize_ita_backend/adding_lineoa');
            return;
        }

        $data = $this->upload->data();
        $filename = $data['file_name'];

        $data = array(
            'lineoa_name' => $this->input->post('lineoa_name'),
            'lineoa_link' => $this->input->post('lineoa_link'), // เพิ่มบรรทัดนี้
            'lineoa_by' => $this->session->userdata('m_fname'),
            'lineoa_img' => $filename
        );

        $query = $this->db->insert('tbl_lineoa_qrcode', $data);
        $this->space_model->update_server_current();

        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        }
    }

    public function list_all()
    {
        $this->db->order_by('lineoa_id', 'DESC');
        $query = $this->db->get('tbl_lineoa_qrcode');
        return $query->result();
    }

    public function read($lineoa_id)
    {
        $this->db->where('lineoa_id', $lineoa_id);
        $query = $this->db->get('tbl_lineoa_qrcode');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return FALSE;
    }

    public function edit_lineoa($lineoa_id)
    {
        $old_data = $this->read($lineoa_id);
        $old_document = $this->db->get_where('tbl_lineoa_qrcode', array('lineoa_id' => $lineoa_id))->row();

        $update_doc_file = !empty($_FILES['lineoa_img']['name']) &&
            (!empty($old_document->lineoa_img) && $old_document->lineoa_img != $_FILES['lineoa_img']['name']);

        if ($update_doc_file) {
            // ลบไฟล์เดิมถ้ามี
            if (!empty($old_document->lineoa_img)) {
                $old_file_path = './docs/img/' . $old_document->lineoa_img;
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
            }

            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['lineoa_img']['name'])) {
                $total_space_required += $_FILES['lineoa_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('publicize_ita_backend/editing_lineoa/' . $lineoa_id);
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('lineoa_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } elseif (!empty($_FILES['lineoa_img']['name'])) {
            // กรณีอัปโหลดใหม่ (ไม่มีไฟล์เดิม)
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = $_FILES['lineoa_img']['size'];

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('publicize_ita_backend/editing_lineoa/' . $lineoa_id);
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('lineoa_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ไม่ได้อัปโหลดใหม่ - ใช้ไฟล์เดิม (หรือ null ถ้าไม่มี)
            $filename = !empty($old_document->lineoa_img) ? $old_document->lineoa_img : null;
        }

        $data = array(
            'lineoa_name' => $this->input->post('lineoa_name'),
            'lineoa_link' => $this->input->post('lineoa_link'),
            'lineoa_by' => $this->session->userdata('m_fname'),
            'lineoa_img' => $filename
        );

        $this->db->where('lineoa_id', $lineoa_id);
        $query = $this->db->update('tbl_lineoa_qrcode', $data);
        $this->space_model->update_server_current();

        // เก็บ log การแก้ไขข้อมูล
        $changes = array();
        if ($old_data) {
            if ($old_data->lineoa_name != $data['lineoa_name']) {
                $changes['lineoa_name'] = array(
                    'old' => $old_data->lineoa_name,
                    'new' => $data['lineoa_name']
                );
            }
            if (isset($old_data->lineoa_link) && $old_data->lineoa_link != $data['lineoa_link']) {
                $changes['lineoa_link'] = array(
                    'old' => $old_data->lineoa_link,
                    'new' => $data['lineoa_link']
                );
            }
            if ($old_data->lineoa_img != $data['lineoa_img']) {
                $changes['lineoa_img'] = array(
                    'old' => $old_data->lineoa_img,
                    'new' => $data['lineoa_img']
                );
            }
        }

        $this->log_model->add_log(
            'แก้ไข',
            'LINE OA QR Code',
            $data['lineoa_name'],
            $lineoa_id,
            array('changes' => $changes)
        );

        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        }
    }

    public function del_lineoa($lineoa_id)
    {
        $old_document = $this->db->get_where('tbl_lineoa_qrcode', array('lineoa_id' => $lineoa_id))->row();

        $old_file_path = './docs/img/' . $old_document->lineoa_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_lineoa_qrcode', array('lineoa_id' => $lineoa_id));
    }

    public function update_lineoa_status()
    {
        if ($this->input->post()) {
            $lineoa_id = $this->input->post('lineoa_id');
            $newStatus = $this->input->post('new_status');

            $data = array('lineoa_status' => $newStatus);
            $this->db->where('lineoa_id', $lineoa_id);
            $this->db->update('tbl_lineoa_qrcode', $data);

            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            show_404();
        }
    }

    // ดึงข้อมูล QR Code ที่เปิดแสดงผลสำหรับหน้า Frontend
    public function get_active_qrcode()
    {
        $this->db->where('lineoa_status', 'show');
        $this->db->order_by('lineoa_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('tbl_lineoa_qrcode');

        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }
}
