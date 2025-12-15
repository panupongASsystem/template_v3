<?php
class Health_model extends CI_Model
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

        $total_space_required = 0;
        $health_img = $_FILES['health_img'];
        $health_imgs = $_FILES['health_imgs'];

        $health_file = isset($_FILES['health_file']) ? $_FILES['health_file'] : null; // Check if health_file is set

        foreach ($health_imgs['size'] as $size) {
            $total_space_required += $size;
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('health/adding');
            return;
        }

        $health_data = array(
            'health_name' => $this->input->post('health_name'),
            'health_detail' => $this->input->post('health_detail'),
            'health_by' => $this->session->userdata('m_fname')
        );

        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $config);

        $this->db->trans_start();
        $this->db->insert('tbl_health', $health_data);
        $health_id = $this->db->insert_id();

        // Upload and update health_img
        $_FILES['health_img']['name'] = $health_img['name'];
        $_FILES['health_img']['type'] = $health_img['type'];
        $_FILES['health_img']['tmp_name'] = $health_img['tmp_name'];
        $_FILES['health_img']['error'] = $health_img['error'];
        $_FILES['health_img']['size'] = $health_img['size'];

        if (!$this->upload->do_upload('health_img')) {
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('health/adding'); // กลับไปหน้าเดิม
            return;
        }

        $upload_data = $this->upload->data();
        $health_img_file = $upload_data['file_name'];

        // Update health_img column with the uploaded image
        $health_img_data = array('health_img' => $health_img_file);
        $this->db->where('health_id', $health_id);
        $this->db->update('tbl_health', $health_img_data);

        // Check if a PDF file is selected
        if ($health_file !== null && $_FILES['health_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $pdf_config['upload_path'] = './docs/file';
            $pdf_config['allowed_types'] = 'pdf';
            $this->load->library('upload', $pdf_config, 'pdf_upload');

            $_FILES['health_file']['name'] = $health_file['name'];
            $_FILES['health_file']['type'] = $health_file['type'];
            $_FILES['health_file']['tmp_name'] = $health_file['tmp_name'];
            $_FILES['health_file']['size'] = $health_file['size'];

            if (!$this->pdf_upload->do_upload('health_file')) {
                echo $this->pdf_upload->display_errors();
                $this->db->trans_rollback();
                return;
            }

            $upload_pdf_data = $this->pdf_upload->data();
            $health_file_file = $upload_pdf_data['file_name'];

            // Update health_file column with the uploaded PDF
            $healthfile_data = array('health_file' => $health_file_file);
            $this->db->where('health_id', $health_id);
            $this->db->update('tbl_health', $healthfile_data);
        }
        // Upload and insert data into tbl_health_img
        $image_data = array();
        foreach ($health_imgs['name'] as $index => $name) {
            if (!empty($name)) {
                $_FILES['health_img']['name'] = $name;
                $_FILES['health_img']['type'] = $health_imgs['type'][$index];
                $_FILES['health_img']['tmp_name'] = $health_imgs['tmp_name'][$index];
                $_FILES['health_img']['error'] = $health_imgs['error'][$index];
                $_FILES['health_img']['size'] = $health_imgs['size'][$index];

                if (!$this->upload->do_upload('health_img')) {
                    $this->session->set_flashdata('save_maxsize', TRUE);
                    redirect('health/adding'); // กลับไปหน้าเดิม
                    return;
                }

                $upload_data = $this->upload->data();
                $image_data[] = array(
                    'health_img_ref_id' => $health_id,
                    'health_img_img' => $upload_data['file_name']
                );
            }
        }

        $this->db->insert_batch('tbl_health_img', $image_data);

        $this->space_model->update_server_current();

        $this->db->trans_complete();
        $this->session->set_flashdata('save_success', TRUE);
    }
    public function list_all()
    {
        $this->db->select('h.*, GROUP_CONCAT(hi.health_img_img) as additional_images');
        $this->db->from('tbl_health as h');
        $this->db->join('tbl_health_img as hi', 'h.health_id = hi.health_img_ref_id', 'left');
        $this->db->group_by('h.health_id');
        $this->db->order_by('h.health_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    //show form edit
    public function read($health_id)
    {
        $this->db->where('health_id', $health_id);
        $query = $this->db->get('tbl_health');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_img($health_id)
    {
        $this->db->where('health_img_ref_id', $health_id);
        $query = $this->db->get('tbl_health_img');
        $results = $query->result();
        usort($results, function ($a, $b) {
            return strnatcmp($a->health_img_img, $b->health_img_img);
        });
        return $results;
    }


    public function read_com_health($health_id)
    {
        $this->db->where('health_com_ref_id', $health_id);
        $this->db->order_by('health_com_ref_id', 'DESC');
        $query = $this->db->get('tbl_health_com');
        return $query->result();
    }

    public function read_com_reply_health($health_com_id)
    {
        $this->db->where('health_com_reply_ref_id', $health_com_id);
        $query = $this->db->get('tbl_health_com_reply');
        return $query->result();
    }

    public function edit($health_id)
    {
        $old_health = $this->db->get_where('tbl_health', array('health_id' => $health_id))->row();
        $update_img_file = !empty($_FILES['health_img']['name']) && $old_health->health_img != $_FILES['health_img']['name'];
        $update_pdf_file = !empty($_FILES['health_file']['name']) && $old_health->health_file != $_FILES['health_file']['name'];

        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        if (!empty($_FILES['health_img']['name'])) {
            $total_space_required += $_FILES['health_img']['size'];
        }

        if (!empty($_FILES['health_file']['name'])) {
            $total_space_required += $_FILES['health_file']['size'];
        }

        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('health/editing');
            return; // เพิ่ม return เพื่อหยุดการทำงานของฟังก์ชันในกรณีที่มีข้อผิดพลาด
        }

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพหรือ PDF ใหม่หรือไม่
        if ($update_img_file) {
            // เช็คและลบรูปภาพเก่า
            $old_img_path = './docs/img/' . $old_health->health_img;
            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }
        }

        if ($update_pdf_file) {
            // เช็คและลบไฟล์ PDF เก่า
            $old_pdf_path = './docs/file/' . $old_health->health_file;
            if (file_exists($old_pdf_path)) {
                unlink($old_pdf_path);
            }
        }

        // อัปโหลดรูปภาพใหม่ (ถ้ามีการอัพโหลด)
        if (!empty($_FILES['health_img']['name'])) {
            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('health_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $img_data = $this->upload->data();
            $img_filename = $img_data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $img_filename = $old_health->health_img;
        }

        // อัปโหลดไฟล์ PDF ใหม่ (ถ้ามีการอัพโหลด)
        if (!empty($_FILES['health_file']['name'])) {
            $config['upload_path'] = './docs/file';
            $config['allowed_types'] = 'pdf|doc';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('health_file')) {
                echo $this->upload->display_errors();
                return;
            }

            $pdf_data = $this->upload->data();
            $pdf_filename = $pdf_data['file_name'];
        } else {
            // ใช้ไฟล์ PDF เดิม
            $pdf_filename = $old_health->health_file;
        }

        // Update health information
        $data = array(
            'health_name' => $this->input->post('health_name'),
            'health_detail' => $this->input->post('health_detail'),
            'health_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
            'health_img' => $img_filename,
            'health_file' => $pdf_filename
        );

        $this->db->where('health_id', $health_id);
        $this->db->update('tbl_health', $data);

        // อัปโหลดและบันทึกไฟล์ใหม่ลงโฟลเดอร์
        if (!empty($_FILES['health_img_img']['name'])) {
            $upload_config['upload_path'] = './docs/img';
            $upload_config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $upload_config);

            $upload_success = true; // ตั้งค่าเริ่มต้นเป็น true

            foreach ($_FILES['health_img_img']['name'] as $index => $name) {
                $_FILES['health_img']['name'] = $name;
                $_FILES['health_img']['type'] = $_FILES['health_img_img']['type'][$index];
                $_FILES['health_img']['tmp_name'] = $_FILES['health_img_img']['tmp_name'][$index];
                $_FILES['health_img']['error'] = $_FILES['health_img_img']['error'][$index];
                $_FILES['health_img']['size'] = $_FILES['health_img_img']['size'][$index];

                if (!$this->upload->do_upload('health_img')) {
                    // echo $this->upload->display_errors();
                    $upload_success = false; // หากเกิดข้อผิดพลาดในการอัปโหลด ตั้งค่าเป็น false
                    break; // หยุดการทำงานลูป
                }

                $upload_data = $this->upload->data();
                $image_path = $upload_data['file_name'];

                // สร้างข้อมูลสำหรับบันทึกลงฐานข้อมูล tbl_health_img
                $image_data = array(
                    'health_img_ref_id' => $health_id,
                    'health_img_img' => $image_path
                );

                $this->db->insert('tbl_health_img', $image_data);
            }

            if ($upload_success) {
                // ลบรูปภาพเก่าที่เกี่ยวข้องกับกิจกรรม
                $this->db->where('health_img_ref_id', $health_id);
                $existing_images = $this->db->get('tbl_health_img')->result();

                foreach ($existing_images as $existing_image) {
                    $old_file_path = './docs/img/' . $existing_image->health_img_img;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }

                $this->db->where('health_img_ref_id', $health_id);
                $this->db->delete('tbl_health_img');

                // เพิ่มรูปภาพใหม่ลงไป
                foreach ($_FILES['health_img_img']['name'] as $index => $name) {
                    $_FILES['health_img']['name'] = $name;
                    $_FILES['health_img']['type'] = $_FILES['health_img_img']['type'][$index];
                    $_FILES['health_img']['tmp_name'] = $_FILES['health_img_img']['tmp_name'][$index];
                    $_FILES['health_img']['error'] = $_FILES['health_img_img']['error'][$index];
                    $_FILES['health_img']['size'] = $_FILES['health_img_img']['size'][$index];

                    if (!$this->upload->do_upload('health_img')) {
                        // echo $this->upload->display_errors();
                        break; // หยุดการทำงานลูปหากรูปภาพมีปัญหา
                    }

                    $upload_data = $this->upload->data();
                    $image_path = $upload_data['file_name'];

                    // สร้างข้อมูลสำหรับบันทึกลงฐานข้อมูล tbl_health_img
                    $image_data = array(
                        'health_img_ref_id' => $health_id,
                        'health_img_img' => $image_path
                    );

                    $this->db->insert('tbl_health_img', $image_data);
                }
            }
        }
        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }


    public function del_health($health_id)
    {
        $old_document = $this->db->get_where('tbl_health', array('health_id' => $health_id))->row();

        // ลบไฟล์รูปภาพ
        $old_img_path = './docs/img/' . $old_document->health_img;
        if (file_exists($old_img_path) && is_file($old_img_path)) {
            unlink($old_img_path);
        }

        // ลบไฟล์ pdf doc
        $old_file_path = './docs/file/' . $old_document->health_file;
        if (file_exists($old_file_path) && is_file($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_health', array('health_id' => $health_id));
        $this->space_model->update_server_current();
    }


    public function del_health_img($health_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $images = $this->db->get_where('tbl_health_img', array('health_img_ref_id' => $health_id))->result();

        // ลบรูปภาพจากตาราง tbl_health_img
        $this->db->where('health_img_ref_id', $health_id);
        $this->db->delete('tbl_health_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($images as $image) {
            $image_path = './docs/img/' . $image->health_img_img;
            if (file_exists($image_path) && is_file($image_path)) {
                unlink($image_path);
            }
        }
    }

    public function updateHealthStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $healthId = $this->input->post('health_id'); // รับค่า health_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_health ในฐานข้อมูลของคุณ
            $data = array(
                'health_status' => $newStatus
            );
            $this->db->where('health_id', $healthId); // ระบุ health_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_health', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function del_com($health_com_id)
    {
        return $this->db->where('health_com_id', $health_com_id)->delete('tbl_health_com');
    }

    public function del_reply($health_com_id)
    {
        return $this->db->where('health_com_reply_ref_id', $health_com_id)->delete('tbl_health_com_reply');
    }

    public function del_com_reply($health_com_reply_id)
    {
        return $this->db->where('health_com_reply_id', $health_com_reply_id)->delete('tbl_health_com_reply');
    }

    public function sum_health_views()
    {
        $this->db->select('SUM(health_view) as total_views');
        $this->db->from('tbl_health');
        $query = $this->db->get();

        return $query->row()->total_views;
    }
}
