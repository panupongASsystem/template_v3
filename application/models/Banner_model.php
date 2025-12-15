<?php
class Banner_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
		// log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function add_Banner()
    {
        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        // Calculate the total space required for all files
        $total_space_required = 0;
        if (!empty($_FILES['banner_img']['name'])) {
            $total_space_required += $_FILES['banner_img']['size'];
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('Banner_backend/adding_Banner');
            return;
        }

        // Upload configuration
        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $config);

        // Upload main file
        if (!$this->upload->do_upload('banner_img')) {
            // If the file size exceeds the max_size, set flash data and redirect
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('Banner_backend/adding_Banner');
            return;
        }

        $data = $this->upload->data();
        $filename = $data['file_name'];

        $data = array(
            'banner_name' => $this->input->post('banner_name'),
            'banner_link' => $this->input->post('banner_link'),
            'banner_by' => $this->session->userdata('m_fname'),
            'banner_img' => $filename
        );

        $query = $this->db->insert('tbl_banner', $data);
		$banner_id = $this->db->insert_id(); // ได้ ID ของ banner ที่เพิ่งเพิ่ม

        $this->space_model->update_server_current();
		
 // บันทึก log การเพิ่มข้อมูล =================================================
    $this->log_model->add_log(
        'เพิ่ม',
        'แบนเนอร์/สไลด์',
        $data['banner_name'],
        $banner_id, // ใช้ banner_id แทน news_id
        array(
            'banner_info' => array(
                'banner_link' => $data['banner_link'],
                'image_file' => $filename,
            )
        )
    );
    // =======================================================================

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
        $this->db->order_by('banner_id', 'DESC');
        $query = $this->db->get('tbl_banner');
        return $query->result();
    }

    //show form edit
    public function read($banner_id)
    {
        $this->db->where('banner_id', $banner_id);
        $query = $this->db->get('tbl_banner');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function edit_Banner($banner_id)
    {
        $old_document = $this->db->get_where('tbl_banner', array('banner_id' => $banner_id))->row();

        $update_doc_file = !empty($_FILES['banner_img']['name']) && $old_document->banner_img != $_FILES['banner_img']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->banner_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['banner_img']['name'])) {
                $total_space_required += $_FILES['banner_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('Banner_backend/editing_Banner');
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('banner_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->banner_img;
        }

        $data = array(
            'banner_name' => $this->input->post('banner_name'),
            'banner_link' => $this->input->post('banner_link'),
            'banner_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
            'banner_img' => $filename
        );

        $this->db->where('banner_id', $banner_id);
        $query = $this->db->update('tbl_banner', $data);

        $this->space_model->update_server_current();


        if ($query) {
			// === เพิ่ม LOGGING ตรงนี้ ==============================================
        
        // เปรียบเทียบการเปลี่ยนแปลงข้อมูล
        $changes = array();
        if ($old_document->banner_name != $data['banner_name']) {
            $changes['banner_name'] = array(
                'old' => $old_document->banner_name,
                'new' => $data['banner_name']
            );
        }
        if ($old_document->banner_link != $data['banner_link']) {
            $changes['banner_link'] = array(
                'old' => $old_document->banner_link,
                'new' => $data['banner_link']
            );
        }
        if ($update_doc_file) {
            $changes['banner_img'] = array(
                'old' => $old_document->banner_img,
                'new' => $filename
            );
        }
        
        // เพิ่มข้อมูลไฟล์ที่อัพโหลด (ถ้ามี)
        $files_info = array();
        if ($update_doc_file && $upload_data) {
            $files_info['new_image'] = $upload_data['file_name'];
            $files_info['file_size'] = $upload_data['file_size'] . ' KB';
            $files_info['old_image_deleted'] = $old_document->banner_img;
        }
        
        // บันทึก log การแก้ไข banner
        $this->log_model->add_log(
            'แก้ไข',
            'แบนเนอร์/สไลด์',
            $data['banner_name'],
            $banner_id,
            array(
                'changes' => $changes,
                'files_info' => $files_info,
                'image_updated' => $update_doc_file
            )
        );
        // =======================================================================
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล !');";
            echo "</script>";
        }
    }

    public function del_banner($banner_id)
    {
        $old_document = $this->db->get_where('tbl_banner', array('banner_id' => $banner_id))->row();

        $old_file_path = './docs/img/' . $old_document->banner_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_banner', array('banner_id' => $banner_id));
		
		// บันทึก log การลบ =================================================
        $this->log_model->add_log(
            'ลบ',
            'แบนเนอร์/สไลด์',
            $old_document->banner_name,
            $banner_id,
            array(
                'deleted_banner_info' => array(
                    'banner_link' => $old_document->banner_link,
                    'image_file' => $old_document->banner_img,
                    'created_by' => $old_document->banner_by,
                    'deleted_date' => date('Y-m-d H:i:s')
                )
            )
        );
        // =======================================================================
    }

    public function updateBannerStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $bannerId = $this->input->post('banner_id'); // รับค่า banner_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_banner ในฐานข้อมูลของคุณ
            $data = array(
                'banner_status' => $newStatus
            );
            $this->db->where('banner_id', $bannerId); // ระบุ banner_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_banner', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function banner_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_banner');
        $this->db->where('tbl_banner.banner_status', 'show');
        $this->db->order_by('tbl_banner.banner_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
}
