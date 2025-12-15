<?php
class Background_personnel_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        // log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function add_background_personnel()
    {
        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        // Calculate the total space required for all files
        $total_space_required = 0;
        $file_fields = ['background_personnel_img1', 'background_personnel_img2', 'background_personnel_img3'];

        foreach ($file_fields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $total_space_required += $_FILES[$field]['size'];
            }
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('Background_personnel_backend/adding_background_personnel');
            return;
        }

        // Upload configuration
        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif|webp';
        $this->load->library('upload', $config);

        $uploaded_files = [];

        // Upload each file
        foreach ($file_fields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                if (!$this->upload->do_upload($field)) {
                    // If upload fails, delete previously uploaded files
                    foreach ($uploaded_files as $uploaded_file) {
                        if (file_exists('./docs/img/' . $uploaded_file)) {
                            unlink('./docs/img/' . $uploaded_file);
                        }
                    }
                    $this->session->set_flashdata('save_maxsize', TRUE);
                    redirect('Background_personnel_backend/adding_background_personnel');
                    return;
                }
                $data = $this->upload->data();
                $uploaded_files[$field] = $data['file_name'];
            } else {
                $uploaded_files[$field] = null;
            }
        }

        // Check if at least img1 is uploaded
        if (empty($uploaded_files['background_personnel_img1'])) {
            $this->session->set_flashdata('save_error_msg', 'รูปที่ 1 เป็นข้อมูลจำเป็น');
            redirect('Background_personnel_backend/adding_background_personnel');
            return;
        }

        $data = array(
            'background_personnel_name' => $this->input->post('background_personnel_name'),
            'background_personnel_rank' => $this->input->post('background_personnel_rank'),
            'background_personnel_phone' => $this->input->post('background_personnel_phone'),
            'background_personnel_by' => $this->session->userdata('m_fname'),
            'background_personnel_img1' => $uploaded_files['background_personnel_img1'],
            'background_personnel_img2' => $uploaded_files['background_personnel_img2'],
            'background_personnel_img3' => $uploaded_files['background_personnel_img3']
        );

        $query = $this->db->insert('tbl_background_personnel', $data);
        $background_personnel_id = $this->db->insert_id();

        $this->space_model->update_server_current();

        if ($query) {
            // บันทึก log การเพิ่มข้อมูล
            $this->log_model->add_log(
                'เพิ่ม',
                'แบนเนอร์บุคลากร',
                $data['background_personnel_name'],
                $background_personnel_id,
                array(
                    'personnel_info' => array(
                        'rank' => $data['background_personnel_rank'],
                        'phone' => $data['background_personnel_phone'],
                        'image_files' => array_filter($uploaded_files)
                    )
                )
            );

            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('Error !');";
            echo "</script>";
        }
    }

    public function list_all()
    {
        $this->db->order_by('background_personnel_id', 'ASC');
        $query = $this->db->get('tbl_background_personnel');
        return $query->result();
    }

    //show form edit
    public function read($background_personnel_id)
    {
        $this->db->where('background_personnel_id', $background_personnel_id);
        $query = $this->db->get('tbl_background_personnel');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit_background_personnel($background_personnel_id)
    {
        $old_document = $this->db->get_where('tbl_background_personnel', array('background_personnel_id' => $background_personnel_id))->row();

        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        $file_fields = ['background_personnel_img1', 'background_personnel_img2', 'background_personnel_img3'];

        foreach ($file_fields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $total_space_required += $_FILES[$field]['size'];
            }
        }

        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('Background_personnel_backend/editing_background_personnel/' . $background_personnel_id);
            return;
        }

        // Upload configuration
        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif|webp';
        $this->load->library('upload', $config);

        $uploaded_files = [];

        // Process each image file
        foreach ($file_fields as $field) {
            $old_field_value = $old_document->$field;

            // Check if user wants to delete the image
            $delete_field = str_replace('background_personnel_', 'delete_', $field);
            if ($this->input->post($delete_field) == '1') {
                // Delete old file if exists
                if (!empty($old_field_value) && file_exists('./docs/img/' . $old_field_value)) {
                    unlink('./docs/img/' . $old_field_value);
                }
                $uploaded_files[$field] = null;
            }
            // Check if new file is uploaded
            elseif (!empty($_FILES[$field]['name'])) {
                // Delete old file if exists
                if (!empty($old_field_value) && file_exists('./docs/img/' . $old_field_value)) {
                    unlink('./docs/img/' . $old_field_value);
                }

                if (!$this->upload->do_upload($field)) {
                    $this->session->set_flashdata('save_maxsize', TRUE);
                    redirect('Background_personnel_backend/editing_background_personnel/' . $background_personnel_id);
                    return;
                }

                $data = $this->upload->data();
                $uploaded_files[$field] = $data['file_name'];
            } else {
                // Keep old file
                $uploaded_files[$field] = $old_field_value;
            }
        }

        // Ensure img1 is not empty
        if (empty($uploaded_files['background_personnel_img1'])) {
            $this->session->set_flashdata('save_error_msg', 'รูปที่ 1 เป็นข้อมูลจำเป็น');
            redirect('Background_personnel_backend/editing_background_personnel/' . $background_personnel_id);
            return;
        }

        $data = array(
            'background_personnel_name' => $this->input->post('background_personnel_name'),
            'background_personnel_rank' => $this->input->post('background_personnel_rank'),
            'background_personnel_phone' => $this->input->post('background_personnel_phone'),
            'background_personnel_by' => $this->session->userdata('m_fname'),
            'background_personnel_img1' => $uploaded_files['background_personnel_img1'],
            'background_personnel_img2' => $uploaded_files['background_personnel_img2'],
            'background_personnel_img3' => $uploaded_files['background_personnel_img3']
        );

        $this->db->where('background_personnel_id', $background_personnel_id);
        $query = $this->db->update('tbl_background_personnel', $data);

        $this->space_model->update_server_current();

        if ($query) {
            // เก็บ log แก้ไขข้อมูล
            $changes = array();
            if ($old_document) {
                if ($old_document->background_personnel_name != $data['background_personnel_name']) {
                    $changes['background_personnel_name'] = array(
                        'old' => $old_document->background_personnel_name,
                        'new' => $data['background_personnel_name']
                    );
                }
                if ($old_document->background_personnel_rank != $data['background_personnel_rank']) {
                    $changes['background_personnel_rank'] = array(
                        'old' => $old_document->background_personnel_rank,
                        'new' => $data['background_personnel_rank']
                    );
                }
                if ($old_document->background_personnel_phone != $data['background_personnel_phone']) {
                    $changes['background_personnel_phone'] = array(
                        'old' => $old_document->background_personnel_phone,
                        'new' => $data['background_personnel_phone']
                    );
                }
                if ($old_document->background_personnel_img1 != $data['background_personnel_img1']) {
                    $changes['background_personnel_img1'] = array(
                        'old' => $old_document->background_personnel_img1,
                        'new' => $data['background_personnel_img1']
                    );
                }
                if ($old_document->background_personnel_img2 != $data['background_personnel_img2']) {
                    $changes['background_personnel_img2'] = array(
                        'old' => $old_document->background_personnel_img2,
                        'new' => $data['background_personnel_img2']
                    );
                }
                if ($old_document->background_personnel_img3 != $data['background_personnel_img3']) {
                    $changes['background_personnel_img3'] = array(
                        'old' => $old_document->background_personnel_img3,
                        'new' => $data['background_personnel_img3']
                    );
                }
            }

            // บันทึก log การแก้ไขข่าว
            $this->log_model->add_log(
                'แก้ไข',
                'แบนเนอร์บุคลากร',
                $data['background_personnel_name'],
                $background_personnel_id,
                array(
                    'changes' => $changes,
                )
            );

            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function del_background_personnel($background_personnel_id)
    {
        // ดึงข้อมูลข่าวก่อนลบ
        $old_data = $this->read($background_personnel_id);

        $old_document = $this->db->get_where('tbl_background_personnel', array('background_personnel_id' => $background_personnel_id))->row();

        // Delete all image files
        $image_fields = ['background_personnel_img1', 'background_personnel_img2', 'background_personnel_img3'];
        foreach ($image_fields as $field) {
            if (!empty($old_document->$field)) {
                $old_file_path = './docs/img/' . $old_document->$field;
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
            }
        }

        $this->db->delete('tbl_background_personnel', array('background_personnel_id' => $background_personnel_id));

        // บันทึก log การลบ
        if ($old_data) {
            $this->log_model->add_log(
                'ลบ',
                'แบนเนอร์บุคลากร',
                $old_data->background_personnel_name,
                $background_personnel_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
    }

    public function updatebackground_personnelStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $background_personnelId = $this->input->post('background_personnel_id');
            $newStatus = $this->input->post('new_status');

            // ทำการอัพเดตค่าในตาราง tbl_background_personnel ในฐานข้อมูลของคุณ
            $data = array(
                'background_personnel_status' => $newStatus
            );
            $this->db->where('background_personnel_id', $background_personnelId);
            $this->db->update('tbl_background_personnel', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function background_personnel_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_background_personnel');
        $this->db->where('tbl_background_personnel.background_personnel_status', 'show');
        $this->db->order_by('tbl_background_personnel.background_personnel_id', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
}
