<?php
class Employee_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function addemp()
    {
        $upload_limit_mb = $this->session->userdata('upload_limit_mb');

        // Check used space
        $this->load->model('doc_model');
        $used_space_mb = $this->doc_model->get_used_space();

        // Calculate the total space required for all files
        $total_space_required = 0;
        if (!empty($_FILES['emp_img']['name'])) {
            $total_space_required += $_FILES['emp_img']['size'];
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            echo "<script>";
            echo "alert('พื้นที่เต็ม !');";
            echo "</script>";
            return;
        }

        // Upload configuration
        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $config);

        // Upload main file
        if (!$this->upload->do_upload('emp_img')) {
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('employee/addingemp'); // กลับไปหน้าเดิม
            return;
        }
        $data = $this->upload->data();
        $filename =  $data['file_name'];

        $data = array(
            'emp_nickname' => $this->input->post('emp_nickname'),
            'emp_name' => $this->input->post('emp_name'),
            'emp_lastname' => $this->input->post('emp_lastname'),
            'emp_role' => $this->input->post('emp_role'),
            'emp_phone' => $this->input->post('emp_phone'),
            'emp_img' => $filename
        );

        $query = $this->db->insert('tbl_employee', $data);

        if ($query) {
            echo "<script>";
            echo "alert('upload success !');";
            echo "</script>";
        } else {
            echo "<script>";
            echo "alert('Error !');";
            echo "</script>";
        }
    }

    public function list_all()
    {
        $this->db->order_by('emp_id', 'DESC');
        $query = $this->db->get('tbl_employee');
        return $query->result();
    }

    //show form edit
    public function read($emp_id)
    {
        $this->db->where('emp_id', $emp_id);
        $query = $this->db->get('tbl_employee');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

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

    public function editemp($emp_id, $emp_status, $emp_nickname, $emp_name, $emp_lastname, $emp_role, $emp_phone)
    {
        $old_document = $this->db->get_where('tbl_employee', array('emp_id' => $emp_id))->row();

        $update_doc_file = !empty($_FILES['emp_img']['name']) && $old_document->emp_img != $_FILES['emp_img']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->emp_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            $upload_limit_mb = $this->session->userdata('upload_limit_mb');
            $used_space_mb = $this->get_used_space();

            $total_space_required = 0;
            if (!empty($_FILES['emp_img']['name'])) {
                $total_space_required += $_FILES['emp_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                echo "<script>";
                echo "alert('พื้นที่เต็ม !');";
                echo "</script>";
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('emp_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->emp_img;
        }



        $data = array(
            'emp_status' => $emp_status,
            'emp_nickname' => $emp_nickname,
            'emp_name' => $emp_name,
            'emp_lastname' => $emp_lastname,
            'emp_role' => $emp_role,
            'emp_phone' => $emp_phone,
            'emp_img' => $filename
        );

        $this->db->where('emp_id', $emp_id);
        $query = $this->db->update('tbl_employee', $data);

        if ($query) {
            echo "<script>";
            echo "alert('อัปเดตข้อมูลสำเร็จ !');";
            echo "</script>";
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function del_emp($emp_id)
    {
        $old_document = $this->db->get_where('tbl_employee', array('emp_id' => $emp_id))->row();

        $old_file_path = './docs/img/' . $old_document->emp_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_employee', array('emp_id' => $emp_id));
    }

    public function emp_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_employee as e');
        $this->db->where('e.emp_status', 'show');
        $this->db->order_by('e.emp_save', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
}
