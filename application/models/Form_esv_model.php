<?php
class Form_esv_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    public function add_topic()
    {
        // ดึงค่าจากฟอร์ม
        $form_esv_topic_name = $this->input->post('form_esv_topic_name');

        // ตรวจสอบว่ามีข้อมูลซ้ำหรือไม่
        $duplicate_check = $this->db->get_where('tbl_form_esv_topic', array('form_esv_topic_name' => $form_esv_topic_name));

        if ($duplicate_check->num_rows() > 0) {
            // ถ้ามีข้อมูลซ้ำ
            $this->session->set_flashdata('save_again', TRUE);
        } else {
            // ถ้าไม่มีข้อมูลซ้ำ, ทำการเพิ่มข้อมูล
            $data = array(
                'form_esv_topic_name' => $form_esv_topic_name,
                'form_esv_topic_by' => $this->session->userdata('m_fname'),
            );

            $query = $this->db->insert('tbl_form_esv_topic', $data);

            $this->space_model->update_server_current();

            if ($query) {
                $this->session->set_flashdata('save_success', TRUE);
            } else {
                echo "<script>";
                echo "alert('Error !');";
                echo "</script>";
            }
        }
    }

    public function list_topic()
    {
        $this->db->order_by('form_esv_topic_id', 'asc');
        $query = $this->db->get('tbl_form_esv_topic');
        return $query->result();
    }

    //show form edit
    public function read_topic($form_esv_topic_id)
    {
        $this->db->where('form_esv_topic_id', $form_esv_topic_id);
        $query = $this->db->get('tbl_form_esv_topic');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit_topic($form_esv_topic_id)
    {

        $data = array(
            'form_esv_topic_name' => $this->input->post('form_esv_topic_name'),
            'form_esv_topic_by' => $this->session->userdata('m_fname'),
        );

        $this->db->where('form_esv_topic_id', $form_esv_topic_id);
        $query = $this->db->update('tbl_form_esv_topic', $data);

        $this->space_model->update_server_current();


        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function list_all_topic($form_esv_topic_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_form_esv');
        $this->db->join('tbl_form_esv_topic', 'tbl_form_esv.form_esv_ref_id = tbl_form_esv_topic.form_esv_topic_id');
        $this->db->where('tbl_form_esv.form_esv_ref_id', $form_esv_topic_id);
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function add()
    {
        // ตรวจสอบพื้นที่ที่ใช้
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        // คำนวณพื้นที่ที่ต้องการสำหรับไฟล์ทั้งหมด
        $total_space_required = 0;
        if (!empty($_FILES['form_esv_file']['name'])) {
            $total_space_required += $_FILES['form_esv_file']['size'];
        }

        // ตรวจสอบว่ามีพื้นที่เพียงพอหรือไม่
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('form_esv_backend/adding_form_esv');
            return;
        }

        // การตั้งค่าสำหรับการอัพโหลดไฟล์
        $config['upload_path'] = './docs/file';
        $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|ppt|pptx';
        $config['max_size'] = 10240; // กำหนดขนาดไฟล์สูงสุดในหน่วย KB
        $this->load->library('upload', $config);

        // ตรวจสอบว่าไฟล์ที่อัพโหลดมีอยู่หรือไม่
        if (empty($_FILES['form_esv_file']['name'])) {
            echo "<script>alert('No file selected for upload.');</script>";
            return;
        }

        // อัพโหลดไฟล์หลัก
        if (!$this->upload->do_upload('form_esv_file')) {
            // ตรวจสอบข้อผิดพลาดและแสดงข้อความ
            $error = $this->upload->display_errors();
            echo "<script>alert('File upload error: " . $error . "');</script>";
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('form_esv_backend/adding_form_esv');
            return;
        }

        // เก็บข้อมูลไฟล์ที่อัพโหลด
        $data = $this->upload->data();
        $filename = $data['file_name'];

        // เตรียมข้อมูลสำหรับการบันทึกลงฐานข้อมูล
        $data = array(
            'form_esv_ref_id' => $this->input->post('form_esv_ref_id'),
            'form_esv_name' => $this->input->post('form_esv_name'),
            'form_esv_by' => $this->session->userdata('m_fname'),
            'form_esv_file' => $filename
        );

        // ตรวจสอบข้อมูลก่อนการบันทึก
        if (empty($data['form_esv_ref_id']) || empty($data['form_esv_name']) || empty($data['form_esv_by']) || empty($data['form_esv_file'])) {
            echo "<script>alert('Some required data is missing.');</script>";
            return;
        }

        // บันทึกข้อมูลลงฐานข้อมูล
        $query = $this->db->insert('tbl_form_esv', $data);

        // อัพเดตพื้นที่เซิร์ฟเวอร์
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
        $this->db->order_by('form_esv_id', 'asc');
        $query = $this->db->get('tbl_form_esv');
        return $query->result();
    }

    //show form edit
    public function read($form_esv_id)
    {
        $this->db->where('form_esv_id', $form_esv_id);
        $query = $this->db->get('tbl_form_esv');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit($form_esv_id)
    {
        $old_document = $this->db->get_where('tbl_form_esv', array('form_esv_id' => $form_esv_id))->row();

        $update_doc_file = !empty($_FILES['form_esv_file']['name']) && $old_document->form_esv_file != $_FILES['form_esv_file']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/file/' . $old_document->form_esv_file;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['form_esv_file']['name'])) {
                $total_space_required += $_FILES['form_esv_file']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('form_esv/editing_form_esv');
                return;
            }

            $config['upload_path'] = './docs/file';
            $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|ppt|pptx';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('form_esv_file')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->form_esv_file;
        }

        $data = array(
            'form_esv_name' => $this->input->post('form_esv_name'),
            'form_esv_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            'form_esv_file' => $filename
        );

        $this->db->where('form_esv_id', $form_esv_id);
        $query = $this->db->update('tbl_form_esv', $data);

        $this->space_model->update_server_current();


        if ($query) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function del_form_esv($form_esv_id)
    {
        $old_document = $this->db->get_where('tbl_form_esv', array('form_esv_id' => $form_esv_id))->row();

        $old_file_path = './docs/file/' . $old_document->form_esv_file;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_form_esv', array('form_esv_id' => $form_esv_id));
    }

    public function del_form_esv_topic_all($form_esv_topic_id)
    {
        // ดึงเอกสารทั้งหมดที่มี form_esv_ref_id ตรงกับ form_esv_topic_id
        $documents = $this->db->get_where('tbl_form_esv', array('form_esv_ref_id' => $form_esv_topic_id))->result();

        // ตรวจสอบและลบไฟล์ที่เกี่ยวข้องทั้งหมด
        foreach ($documents as $document) {
            $old_file_path = './docs/file/' . $document->form_esv_file;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
        }

        // ลบข้อมูลทั้งหมดที่มี form_esv_ref_id ตรงกับ form_esv_topic_id ในฐานข้อมูล
        $this->db->delete('tbl_form_esv', array('form_esv_ref_id' => $form_esv_topic_id));
    }

    public function del_form_esv_topic($form_esv_topic_id)
    {
        // ลบข้อมูลใน tbl_form_esv
        $this->db->where('form_esv_topic_id', $form_esv_topic_id);
        $this->db->delete('tbl_form_esv_topic');
    }

    // public function form_esv_frontend_1()
    // {
    //     $this->db->select('*');
    //     $this->db->from('tbl_form_esv');
    //     $this->db->where('tbl_form_esv.form_esv_id', 1); // ระบุ id ที่ต้องการ
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    // public function form_esv_frontend_2()
    // {
    //     $this->db->select('*');
    //     $this->db->from('tbl_form_esv');
    //     $this->db->where('tbl_form_esv.form_esv_id', 2); // ระบุ id ที่ต้องการ
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    // public function form_esv_frontend_3()
    // {
    //     $this->db->select('*');
    //     $this->db->from('tbl_form_esv');
    //     $this->db->where('tbl_form_esv.form_esv_id', 3); // ระบุ id ที่ต้องการ
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    // public function form_esv_frontend_4()
    // {
    //     $this->db->select('*');
    //     $this->db->from('tbl_form_esv');
    //     $this->db->where('tbl_form_esv.form_esv_id', 4); // ระบุ id ที่ต้องการ
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    // public function form_esv_frontend_5()
    // {
    //     $this->db->select('*');
    //     $this->db->from('tbl_form_esv');
    //     $this->db->where('tbl_form_esv.form_esv_id', 5); // ระบุ id ที่ต้องการ
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    // public function form_esv_frontend_6()
    // {
    //     $this->db->select('*');
    //     $this->db->from('tbl_form_esv');
    //     $this->db->where('tbl_form_esv.form_esv_id', 6); // ระบุ id ที่ต้องการ
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    // public function form_esv_frontend_7()
    // {
    //     $this->db->select('*');
    //     $this->db->from('tbl_form_esv');
    //     $this->db->where('tbl_form_esv.form_esv_id', 7); // ระบุ id ที่ต้องการ
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    // public function form_esv_frontend_8()
    // {
    //     $this->db->select('*');
    //     $this->db->from('tbl_form_esv');
    //     $this->db->where('tbl_form_esv.form_esv_id', 8); // ระบุ id ที่ต้องการ
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    public function list_all_topic_with_details()
    {
        $this->db->select('
            tbl_form_esv.form_esv_name,
            tbl_form_esv.form_esv_file,
            tbl_form_esv_topic.form_esv_topic_name
        ');
        $this->db->from('tbl_form_esv');
        $this->db->join('tbl_form_esv_topic', 'tbl_form_esv.form_esv_ref_id = tbl_form_esv_topic.form_esv_topic_id');
        $this->db->order_by('tbl_form_esv_topic.form_esv_topic_id', 'ASC'); // เรียงตามชื่อหัวข้อ

        $query = $this->db->get();
        return $query->result();
    }
}
