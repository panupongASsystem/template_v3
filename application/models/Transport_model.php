<?php
class Transport_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    public function list_transport()
    {
        $this->db->order_by('transport_type_id', 'DESC');
        $query = $this->db->get('tbl_transport_type');
        return $query->result();
    }

    public function list_all()
    {
        $this->db->select('t.*,tt.transport_type_name');
        $this->db->from('tbl_transport as t');
        $this->db->join('tbl_transport_type as tt', 't.transport_ref_type_id=tt.transport_type_id ');
        $this->db->order_by('t.transport_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function read($transport_id)
    {
        $this->db->select('t.*,tt.transport_type_name');
        $this->db->from('tbl_transport as t');
        $this->db->join('tbl_transport_type as tt', 't.transport_ref_type_id=tt.transport_type_id ');
        $this->db->where('t.transport_id', $transport_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return false;
    }

    public function edittransport($transport_id)
    {
        $old_document = $this->db->get_where('tbl_transport', array('transport_id' => $transport_id))->row();

        $update_doc_file = !empty($_FILES['transport_img']['name']) && $old_document->transport_img != $_FILES['transport_img']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->transport_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['transport_img']['name'])) {
                $total_space_required += $_FILES['transport_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('transport/edit');
                return;
            }

            $config['upload_path'] = './docs/img/';
            $config['allowed_types'] = 'gif|jpg|png';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('transport_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->transport_img;
        }

        $data = array(
            'transport_head' => $this->input->post('transport_head'),
            'transport_head2' => $this->input->post('transport_head2'),
            'transport_head3' => $this->input->post('transport_head3'),
            'transport_detail' => $this->input->post('transport_detail'),
            'transport_detail2' => $this->input->post('transport_detail2'),
            'transport_img' => $filename
        );

        $this->db->where('transport_id', $transport_id);
        $query = $this->db->update('tbl_transport', $data);
        $this->space_model->update_server_current();

        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('Error !');";
            echo "</script>";
        }
    }

    // ตรวจสอบการใช้พื้นที่ของโฟล์เดอร์ folder storage
    public function get_used_space()
    {
        $upload_folder = './docs'; // ตำแหน่งของโฟลเดอร์ที่คุณต้องการ

        $used_space = $this->calculateFolderSize($upload_folder);

        $used_space_mb = $used_space / (1024 * 1024 * 1024);
        return $used_space_mb;
    }
    private function calculateFolderSize($folder)
    {
        $used_space = 0;
        $files = scandir($folder);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $path = $folder . '/' . $file;
                if (is_file($path)) {
                    $used_space += filesize($path);
                } elseif (is_dir($path)) {
                    $used_space += $this->calculateFolderSize($path);
                }
            }
        }
        return $used_space;
    }


    public function deldata($pid)
    {
        $this->db->delete('tbl_position', array('pid' => $pid));
        $this->space_model->update_server_current();
    }
}
