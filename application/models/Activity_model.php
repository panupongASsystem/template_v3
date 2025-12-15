<?php
class Activity_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
		// log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function add_Activity()
    {
        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        $activity_imgs = $_FILES['activity_imgs'];

        foreach ($activity_imgs['size'] as $size) {
            $total_space_required += $size;
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('activity_backend/adding_Activity');
            return;
        }

        $activity_data = array(
            'activity_name' => $this->input->post('activity_name'),
            'activity_detail' => $this->input->post('activity_detail'),
            'activity_date' => $this->input->post('activity_date'),
            'activity_refer' => $this->input->post('activity_refer'),
            'activity_by' => $this->session->userdata('m_fname') // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $config['max_size'] = 10240; // จำกัดขนาดไฟล์สูงสุดเป็น 10MB
        $this->load->library('upload', $config);

        $activity_img = $_FILES['activity_img'];
        $activity_imgs = $_FILES['activity_imgs'];

        $this->db->trans_start();
        $this->db->insert('tbl_activity', $activity_data);
        $activity_id = $this->db->insert_id();

        // Upload and update activity_img (เปลี่ยนชื่อไฟล์ก่อนอัปโหลด)
        $activity_img_name = pathinfo($activity_img['name'], PATHINFO_FILENAME); // ชื่อไฟล์ไม่รวม .jpg หรือ .png
        $activity_img_ext = pathinfo($activity_img['name'], PATHINFO_EXTENSION); // ส่วนขยายไฟล์

        // เปลี่ยนชื่อไฟล์ เพิ่มเลข 0 เข้าไป
        $new_activity_img_name = $activity_img_name . '_0.' . $activity_img_ext;

        // ตั้งค่าใหม่ใน $_FILES
        $_FILES['activity_img']['name'] = $new_activity_img_name;
        $_FILES['activity_img']['type'] = $activity_img['type'];
        $_FILES['activity_img']['tmp_name'] = $activity_img['tmp_name'];
        $_FILES['activity_img']['error'] = $activity_img['error'];
        $_FILES['activity_img']['size'] = $activity_img['size'];

        // Upload main file
        if (!$this->upload->do_upload('activity_img')) {
            echo '<script>';
            echo 'alert("ไม่สามารถอัปโหลดได้ ขนาดแต่ละไฟล์ต้องไม่เกิน 10MB!");';
            echo 'window.location.href = "' . site_url('activity_backend/adding_Activity') . '";'; // แก้ไขหน้าโดยไปยังหน้าเดิม
            echo '</script>';
            return; // เพื่อให้แน่ใจว่าไม่ทำต่อ
        }

        $upload_data = $this->upload->data();
        $activity_img_file = $upload_data['file_name'];

        // Update activity_img column with the uploaded image
        $activity_img_data = array('activity_img' => $activity_img_file);
        $this->db->where('activity_id', $activity_id);
        $this->db->update('tbl_activity', $activity_img_data);

        // Upload and insert data into tbl_activity_img
        $image_data = array(); // Initialize the array
        foreach ($activity_imgs['name'] as $index => $name) {
            $activity_img_name = pathinfo($name, PATHINFO_FILENAME); // ชื่อไฟล์ไม่รวม .jpg หรือ .png
            $activity_img_ext = pathinfo($name, PATHINFO_EXTENSION); // ส่วนขยายไฟล์

            // เปลี่ยนชื่อไฟล์ เพิ่มเลข 0 เข้าไป
            $new_activity_img_name = $activity_img_name . '_0.' . $activity_img_ext;

            $_FILES['activity_img']['name'] = $new_activity_img_name;
            $_FILES['activity_img']['type'] = $activity_imgs['type'][$index];
            $_FILES['activity_img']['tmp_name'] = $activity_imgs['tmp_name'][$index];
            $_FILES['activity_img']['error'] = $activity_imgs['error'][$index];
            $_FILES['activity_img']['size'] = $activity_imgs['size'][$index];

            if (!$this->upload->do_upload('activity_img')) {
                echo '<script>';
                echo 'alert("ไม่สามารถอัปโหลดได้ ขนาดแต่ละไฟล์ต้องไม่เกิน 10MB!");';
                echo 'window.location.href = "' . site_url('activity_backend/adding_Activity') . '";'; // แก้ไขหน้าโดยไปยังหน้าเดิม
                echo '</script>';
                return; // เพื่อให้แน่ใจว่าไม่ทำต่อ
            }

            $upload_data = $this->upload->data();
            $image_data[] = array(
                'activity_img_ref_id' => $activity_id,
                'activity_img_img' => $upload_data['file_name']
            );
        }

        $this->db->insert_batch('tbl_activity_img', $image_data);

        $this->space_model->update_server_current();
		
		// Generate the share URL
        $share_url = base_url('Pages/activity_detail/' . $activity_id);
        $facebook_share_link = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($share_url);
		
// บันทึก log การเพิ่มข้อมูลกิจกรรม =====================================
$this->log_model->add_log(
    'เพิ่ม',
    'ข่าวสาร / กิจกรรม',
    $activity_data['activity_name'],
    $activity_id,
    array(
        'activity_date' => $activity_data['activity_date'],
        'activity_refer' => $activity_data['activity_refer'],
        'images_uploaded' => array(
            'main_image' => $activity_img_file,
            'gallery_images' => count($image_data),
            'gallery_filenames' => array_column($image_data, 'activity_img_img')
        ),
        'total_images' => count($image_data) + 1, // +1 สำหรับรูปหลัก
        'share_url' => $share_url,
        'facebook_share_link' => $facebook_share_link
    )
);
// ====================================================================

        $this->db->trans_complete();

        // Save the facebook_share_link to session or pass it to the view
        $this->session->set_flashdata('facebook_share_link', $facebook_share_link);


        $this->session->set_flashdata('save_share', TRUE);
    }


    public function list_admin()
    {
        $this->db->select('a.*, GROUP_CONCAT(ai.activity_img_img) as additional_images');
        $this->db->from('tbl_activity as a');
        $this->db->join('tbl_activity_img as ai', 'a.activity_id = ai.activity_img_ref_id', 'left');
        $this->db->group_by('a.activity_id');
        $this->db->order_by('a.activity_date', 'DESC'); // เรียงตามวันที่ใหม่ล่าสุด    
        $query = $this->db->get();
        return $query->result();
    }

    // public function list_user()
    // {
    //     $this->db->select('a.*, GROUP_CONCAT(ai.user_activity_img_img) as additional_images');
    //     $this->db->from('tbl_user_activity as a');
    //     $this->db->join('tbl_user_activity_img as ai', 'a.user_activity_id = ai.user_activity_img_ref_id', 'left');
    //     $this->db->group_by('a.user_activity_id');
    //     $this->db->order_by('a.user_activity_id', 'DESC');
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    // public function list_all()
    // {
    //     $this->db->select('a.*, GROUP_CONCAT(ai.activity_img_img) as additional_images');
    //     $this->db->from('tbl_activity as a');
    //     $this->db->join('tbl_activity_img as ai', 'a.activity_id = ai.activity_img_ref_id', 'left');
    //     $this->db->group_by('a.activity_id');
    //     $this->db->order_by('a.activity_id', 'DESC');
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    //show form edit
    public function read_activity($activity_id)
    {
        $this->db->where('activity_id', $activity_id);
        $query = $this->db->get('tbl_activity');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    // public function read_img_activity($activity_id)
    // {
    //     $this->db->where('activity_img_ref_id', $activity_id);
    //     $query = $this->db->get('tbl_activity_img');
    //     $results = $query->result();
    //     usort($results, function ($a, $b) {
    //         return strnatcmp($a->activity_img_img, $b->activity_img_img);
    //     });
    //     return $results;
    // }


    public function read_com_activity($activity_id)
    {
        $this->db->where('activity_com_ref_id', $activity_id);
        $this->db->order_by('activity_com_ref_id', 'DESC');
        $query = $this->db->get('tbl_activity_com');
        return $query->result();
    }

    public function read_com_reply_activity($activity_com_id)
    {
        $this->db->where('activity_com_reply_ref_id', $activity_com_id);
        $query = $this->db->get('tbl_activity_com_reply');
        return $query->result();
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

    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('activity_img_img, activity_img_ref_id');
        $this->db->where('activity_img_id', $file_id);
        $query = $this->db->get('tbl_activity_img');
        $file_data = $query->row();
		
		// บันทึก log การลบรูปภาพ =================================================
        $old_data = $this->read($file_data->activity_img_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->activity_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('activity_img_id', $file_id);
        $this->db->delete('tbl_activity_img');
        $this->space_model->update_server_current();
		// บันทึก log การลบรูปภาพ =================================================
        $this->log_model->add_log(
            'ลบ',
            'ข้อมูลข่าวสาร / กิจกรรม',
            'รูปภาพ: ' . $file_data->activity_img_img . ' จาก: ' . ($old_data ? $old_data->activity_name : 'ไม่ระบุ'),
            $file_data->activity_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->activity_img_img)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }


    public function edit_Activity($activity_id)
    {
		// ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read_activity($activity_id);
		
        // Update activity information
        $data = array(
            'activity_name' => $this->input->post('activity_name'),
            'activity_detail' => $this->input->post('activity_detail'),
            'activity_date' => $this->input->post('activity_date'),
            'activity_refer' => $this->input->post('activity_refer'),
            'activity_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูลy
        );

        $this->db->where('activity_id', $activity_id);
        $this->db->update('tbl_activity', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['activity_img_img'])) {
            foreach ($_FILES['activity_img_img']['name'] as $index => $name) {
                if (isset($_FILES['activity_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['activity_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['activity_pdf_pdf'])) {
            foreach ($_FILES['activity_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['activity_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['activity_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['activity_file_doc'])) {
            foreach ($_FILES['activity_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['activity_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['activity_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('activity_backend/adding');
            return;
        }

        $pdf_config['upload_path'] = './docs/file';
        $pdf_config['allowed_types'] = 'pdf';
        $this->load->library('upload', $pdf_config, 'pdf_upload');

        $doc_config['upload_path'] = './docs/file';
        $doc_config['allowed_types'] = 'doc|docx|xls|xlsx|ppt|pptx';
        $this->load->library('upload', $doc_config, 'doc_upload');

        $img_config['upload_path'] = './docs/img';
        $img_config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $img_config, 'img_upload');

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('activity_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_activity', array('activity_id' => $activity_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->activity_img) {
                $old_file_path = './docs/img/' . $old_document->activity_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['activity_img'] = $this->img_upload->data('file_name');
            $this->db->where('activity_id', $activity_id);
            $this->db->update('tbl_activity', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['activity_img_img']['name'][0])) {

            foreach ($_FILES['activity_img_img']['name'] as $index => $name) {
                $_FILES['activity_img_img_multiple']['name'] = $name;
                $_FILES['activity_img_img_multiple']['type'] = $_FILES['activity_img_img']['type'][$index];
                $_FILES['activity_img_img_multiple']['tmp_name'] = $_FILES['activity_img_img']['tmp_name'][$index];
                $_FILES['activity_img_img_multiple']['error'] = $_FILES['activity_img_img']['error'][$index];
                $_FILES['activity_img_img_multiple']['size'] = $_FILES['activity_img_img']['size'][$index];

                if ($this->img_upload->do_upload('activity_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'activity_img_ref_id' => $activity_id,
                        'activity_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_activity_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['activity_pdf_pdf']['name'][0])) {
            foreach ($_FILES['activity_pdf_pdf']['name'] as $index => $name) {
                $_FILES['activity_pdf_pdf_multiple']['name'] = $name;
                $_FILES['activity_pdf_pdf_multiple']['type'] = $_FILES['activity_pdf_pdf']['type'][$index];
                $_FILES['activity_pdf_pdf_multiple']['tmp_name'] = $_FILES['activity_pdf_pdf']['tmp_name'][$index];
                $_FILES['activity_pdf_pdf_multiple']['error'] = $_FILES['activity_pdf_pdf']['error'][$index];
                $_FILES['activity_pdf_pdf_multiple']['size'] = $_FILES['activity_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('activity_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'activity_pdf_ref_id' => $activity_id,
                        'activity_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_activity_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
        if (!empty($_FILES['activity_file_doc']['name'][0])) {
            foreach ($_FILES['activity_file_doc']['name'] as $index => $name) {
                $_FILES['activity_file_doc_multiple']['name'] = $name;
                $_FILES['activity_file_doc_multiple']['type'] = $_FILES['activity_file_doc']['type'][$index];
                $_FILES['activity_file_doc_multiple']['tmp_name'] = $_FILES['activity_file_doc']['tmp_name'][$index];
                $_FILES['activity_file_doc_multiple']['error'] = $_FILES['activity_file_doc']['error'][$index];
                $_FILES['activity_file_doc_multiple']['size'] = $_FILES['activity_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('activity_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'activity_file_ref_id' => $activity_id,
                        'activity_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_activity_file', $doc_data);
        }
        $this->space_model->update_server_current();
		// === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->activity_name != $data['activity_name']) {
                $changes['activity_name'] = array(
                    'old' => $old_data->activity_name,
                    'new' => $data['activity_name']
                );
            }
            if ($old_data->activity_detail != $data['activity_detail']) {
                $changes['activity_detail'] = array(
                    'old' => 'มีการเปลี่ยนแปลง',
                    'new' => 'รายละเอียดใหม่'
                );
            }
            if ($old_data->activity_date != $data['activity_date']) {
                $changes['activity_date'] = array(
                    'old' => $old_data->activity_date,
                    'new' => $data['activity_date']
                );
            }
            if ($old_data->activity_refer != $data['activity_refer']) {
                $changes['activity_refer'] = array(
                    'old' => $old_data->activity_refer,
                    'new' => $data['activity_refer']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'activity_img_img');
        }

        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'ข้อมูลข่าวสาร / กิจกรรม',
            $data['activity_name'],
            $activity_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data)
            )
        );
        // =======================================================================
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_Activity($activity_id)
    {
        $old_document = $this->db->get_where('tbl_activity', array('activity_id' => $activity_id))->row();

        $old_file_path = './docs/img/' . $old_document->activity_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_activity', array('activity_id' => $activity_id));
		 // บันทึก log การลบ =================================================
        if ($old_document) {
            $this->log_model->add_log(
                'ลบ',
                'ข้อมูลข่าวสาร / กิจกรรม',
                $old_document->activity_name,
                $activity_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // =======================================================================
        $this->space_model->update_server_current();
    }

    public function del_activity_img($activity_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $images = $this->db->get_where('tbl_activity_img', array('activity_img_ref_id' => $activity_id))->result();

        // ลบรูปภาพจากตาราง tbl_activity_img
        $this->db->where('activity_img_ref_id', $activity_id);
        $this->db->delete('tbl_activity_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($images as $image) {
            $image_path = './docs/img/' . $image->activity_img_img;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }

    public function updateActivityStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $activityId = $this->input->post('activity_id'); // รับค่า activity_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_activity ในฐานข้อมูลของคุณ
            $data = array(
                'activity_status' => $newStatus
            );
            $this->db->where('activity_id', $activityId); // ระบุ activity_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_activity', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function del_com($activity_com_id)
    {
        return $this->db->where('activity_com_id', $activity_com_id)->delete('tbl_activity_com');
    }

    public function del_reply($activity_com_id)
    {
        return $this->db->where('activity_com_reply_ref_id', $activity_com_id)->delete('tbl_activity_com_reply');
    }

    public function del_com_reply($activity_com_reply_id)
    {
        return $this->db->where('activity_com_reply_id', $activity_com_reply_id)->delete('tbl_activity_com_reply');
    }

    // ส่วนของ user ***************************************************************************************************************************************************************************************************************************************************************
    public function read_user_activity($user_activity_id)
    {
        $this->db->where('user_activity_id', $user_activity_id);
        $query = $this->db->get('tbl_user_activity');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_user_img_activity($user_activity_id)
    {
        $this->db->where('user_activity_img_ref_id', $user_activity_id);
        $this->db->order_by('user_activity_img_id', 'DESC');
        $query = $this->db->get('tbl_user_activity_img');
        return $query->result();
    }

    public function read_user_com_activity($user_activity_id)
    {
        $this->db->where('user_activity_com_ref_id', $user_activity_id);
        $this->db->order_by('user_activity_com_ref_id', 'DESC');
        $query = $this->db->get('tbl_user_activity_com');
        return $query->result();
    }

    public function read_user_com_reply_activity($user_activity_com_id)
    {
        $this->db->where('user_activity_com_reply_ref_id', $user_activity_com_id);
        $query = $this->db->get('tbl_user_activity_com_reply');
        return $query->result();
    }

    public function edit_User_Activity($user_activity_id, $user_activity_name, $user_activity_detail, $user_activity_phone)
    {
        $old_document = $this->db->get_where('tbl_user_activity', array('user_activity_id' => $user_activity_id))->row();

        $update_doc_file = !empty($_FILES['user_activity_img']['name']) && $old_document->user_activity_img != $_FILES['user_activity_img']['name'];

        // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
        if ($update_doc_file) {
            $old_file_path = './docs/img/' . $old_document->user_activity_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Check used space
            $used_space_mb = $this->space_model->get_used_space();
            $upload_limit_mb = $this->space_model->get_limit_storage();

            $total_space_required = 0;
            if (!empty($_FILES['user_activity_img']['name'])) {
                $total_space_required += $_FILES['user_activity_img']['size'];
            }

            if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('activity/editing_User_Activity');
                return;
            }

            $config['upload_path'] = './docs/img';
            $config['allowed_types'] = 'gif|jpg|png';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('user_activity_img')) {
                echo $this->upload->display_errors();
                return;
            }

            $data = $this->upload->data();
            $filename = $data['file_name'];
        } else {
            // ใช้รูปภาพเดิม
            $filename = $old_document->user_activity_img;
        }

        // Update user_activity information
        $data = array(
            'user_activity_name' => $user_activity_name,
            'user_activity_detail' => $user_activity_detail,
            'user_activity_phone' => $user_activity_phone,
            'user_activity_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
            'user_activity_img' => $filename
        );

        $this->db->where('user_activity_id', $user_activity_id);
        $this->db->update('tbl_user_activity', $data);

        // อัปโหลดและบันทึกไฟล์ใหม่ลงโฟลเดอร์
        if (!empty($_FILES['user_activity_img_img']['name'])) {
            $upload_config['upload_path'] = './docs/img';
            $upload_config['allowed_types'] = 'gif|jpg|png';
            $this->load->library('upload', $upload_config);

            $upload_success = true; // ตั้งค่าเริ่มต้นเป็น true

            foreach ($_FILES['user_activity_img_img']['name'] as $index => $name) {
                $_FILES['user_activity_img']['name'] = $name;
                $_FILES['user_activity_img']['type'] = $_FILES['user_activity_img_img']['type'][$index];
                $_FILES['user_activity_img']['tmp_name'] = $_FILES['user_activity_img_img']['tmp_name'][$index];
                $_FILES['user_activity_img']['error'] = $_FILES['user_activity_img_img']['error'][$index];
                $_FILES['user_activity_img']['size'] = $_FILES['user_activity_img_img']['size'][$index];

                if (!$this->upload->do_upload('user_activity_img')) {
                    // echo $this->upload->display_errors();
                    $upload_success = false; // หากเกิดข้อผิดพลาดในการอัปโหลด ตั้งค่าเป็น false
                    break; // หยุดการทำงานลูป
                }

                $upload_data = $this->upload->data();
                $image_path = $upload_data['file_name'];

                // สร้างข้อมูลสำหรับบันทึกลงฐานข้อมูล tbl_user_activity_img
                $image_data = array(
                    'user_activity_img_ref_id' => $user_activity_id,
                    'user_activity_img_img' => $image_path
                );

                $this->db->insert('tbl_user_activity_img', $image_data);
            }

            if ($upload_success) {
                // ลบรูปภาพเก่าที่เกี่ยวข้องกับกิจกรรม
                $this->db->where('user_activity_img_ref_id', $user_activity_id);
                $existing_images = $this->db->get('tbl_user_activity_img')->result();

                foreach ($existing_images as $existing_image) {
                    $old_file_path = './docs/img/' . $existing_image->user_activity_img_img;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }

                $this->db->where('user_activity_img_ref_id', $user_activity_id);
                $this->db->delete('tbl_user_activity_img');

                // เพิ่มรูปภาพใหม่ลงไป
                foreach ($_FILES['user_activity_img_img']['name'] as $index => $name) {
                    $_FILES['user_activity_img']['name'] = $name;
                    $_FILES['user_activity_img']['type'] = $_FILES['user_activity_img_img']['type'][$index];
                    $_FILES['user_activity_img']['tmp_name'] = $_FILES['user_activity_img_img']['tmp_name'][$index];
                    $_FILES['user_activity_img']['error'] = $_FILES['user_activity_img_img']['error'][$index];
                    $_FILES['user_activity_img']['size'] = $_FILES['user_activity_img_img']['size'][$index];

                    if (!$this->upload->do_upload('user_activity_img')) {
                        // echo $this->upload->display_errors();
                        break; // หยุดการทำงานลูปหากรูปภาพมีปัญหา
                    }

                    $upload_data = $this->upload->data();
                    $image_path = $upload_data['file_name'];

                    // สร้างข้อมูลสำหรับบันทึกลงฐานข้อมูล tbl_user_activity_img
                    $image_data = array(
                        'user_activity_img_ref_id' => $user_activity_id,
                        'user_activity_img_img' => $image_path
                    );

                    $this->db->insert('tbl_user_activity_img', $image_data);
                }
            }
        }
        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_user_activity($user_activity_id)
    {
        $old_document = $this->db->get_where('tbl_user_activity', array('user_activity_id' => $user_activity_id))->row();

        $old_file_path = './docs/img/' . $old_document->user_activity_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_user_activity', array('user_activity_id' => $user_activity_id));
        $this->space_model->update_server_current();
    }

    public function del_user_activity_img($user_activity_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $images = $this->db->get_where('tbl_user_activity_img', array('user_activity_img_ref_id' => $user_activity_id))->result();

        // ลบรูปภาพจากตาราง tbl_user_activity_img
        $this->db->where('user_activity_img_ref_id', $user_activity_id);
        $this->db->delete('tbl_user_activity_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($images as $image) {
            $image_path = './docs/img/' . $image->user_activity_img_img;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }

    public function updateUserActivityStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $useractivityId = $this->input->post('user_activity_id'); // รับค่า activity_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_activity ในฐานข้อมูลของคุณ
            $data = array(
                'user_activity_status' => $newStatus
            );
            $this->db->where('user_activity_id', $useractivityId); // ระบุ activity_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_user_activity', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function del_com_user($user_activity_com_id)
    {
        return $this->db->where('user_activity_com_id', $user_activity_com_id)->delete('tbl_user_activity_com');
    }

    public function del_reply_user($user_activity_com_id)
    {
        return $this->db->where('user_activity_com_reply_ref_id', $user_activity_com_id)->delete('tbl_user_activity_com_reply');
    }

    public function del_com_reply_user($user_activity_com_reply_id)
    {
        return $this->db->where('user_activity_com_reply_id', $user_activity_com_reply_id)->delete('tbl_user_activity_com_reply');
    }

    // ************************************************************************************************************

    public function sum_activity_views()
    {
        // คำนวณผลรวมของ tbl_activity
        $this->db->select('SUM(activity_view) as total_views');
        $this->db->from('tbl_activity');
        $query_activity = $this->db->get();

        // คำนวณผลรวมของ tbl_user_activity
        // $this->db->select('SUM(user_activity_view) as total_user_views');
        // $this->db->from('tbl_user_activity');
        // $query_user_activity = $this->db->get();

        // นำผลรวมของทั้งสองตารางมาบวกกัน
        // $total_views = $query_activity->row()->total_views + $query_user_activity->row()->total_user_views;
        $total_views = $query_activity->row()->total_views;

        return $total_views;
    }

    public function sum_activity_likes()
    {
        // คำนวณผลรวมของ tbl_activity
        $this->db->select('SUM(activity_like_like) as total_likes');
        $this->db->from('tbl_activity_like');
        $query_activity = $this->db->get();

        // คำนวณผลรวมของ tbl_user_activity
        // $this->db->select('SUM(user_activity_like_like) as total_user_likes');
        // $this->db->from('tbl_user_activity_like');
        // $query_user_activity = $this->db->get();

        // นำผลรวมของทั้งสองตารางมาบวกกัน
        // $total_likes = $query_activity->row()->total_likes + $query_user_activity->row()->total_user_likes;
        $total_likes = $query_activity->row()->total_likes;

        return $total_likes;
    }

    public function sum_activity_id()
    {
        // หาวันที่แรกของเดือนปัจจุบัน
        $start_of_current_month = date('Y-m-01');

        // หาวันที่แรกของเดือนถัดไป
        $start_of_next_month = date('Y-m-01', strtotime('+1 month'));

        // คำนวณผลรวมของ tbl_activity ที่มีวันที่อยู่ในเดือนปัจจุบันหรือเดือนถัดไป
        $this->db->select('SUM(activity_id) as total_id');
        $this->db->from('tbl_activity');
        $this->db->where('activity_datesave >=', $start_of_current_month);
        $this->db->where('activity_datesave <', $start_of_next_month);
        $query_activity = $this->db->get();

        // คำนวณผลรวมของ tbl_user_activity ที่มีวันที่อยู่ในเดือนปัจจุบันหรือเดือนถัดไป
        // $this->db->select('SUM(user_activity_id) as total_user_id');
        // $this->db->from('tbl_user_activity');
        // $this->db->where('user_activity_datesave >=', $start_of_current_month);
        // $this->db->where('user_activity_datesave <', $start_of_next_month);
        // $query_user_activity = $this->db->get();

        // นำผลรวมของทั้งสองตารางมาบวกกัน
        // $total_id = $query_activity->row()->total_id + $query_user_activity->row()->total_user_id;
        $total_id = $query_activity->row()->total_id;

        return $total_id;
    }

    public function activity_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_activity');
        $this->db->where('tbl_activity.activity_status', 'show');
        $this->db->limit(10);
        $this->db->order_by('tbl_activity.activity_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function activity_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_activity');
        $this->db->where('tbl_activity.activity_status', 'show');
        $this->db->order_by('tbl_activity.activity_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function read_img_activity_font($activity_id)
    {
        $this->db->where('activity_img_ref_id', $activity_id);
        $this->db->order_by('activity_img_order', 'ASC'); // เรียงตามลำดับที่กำหนดจากการลากเลื่อน
        $this->db->order_by('activity_img_id', 'ASC'); // กรณีไม่มีค่า activity_img_order ให้เรียงตาม ID
        $query = $this->db->get('tbl_activity_img');
        return $query->result();
    }

    public function increment_activity_view($activity_id)
    {
        $this->db->where('activity_id', $activity_id);
        $this->db->set('activity_view', 'activity_view + 1', false); // บวกค่า activity_view ทีละ 1
        $this->db->update('tbl_activity');
    }

    public function update_image_order($image_order)
    {
        if (!empty($image_order)) {
            $this->db->trans_start();

            foreach ($image_order as $item) {
                if (isset($item['id']) && isset($item['order'])) {
                    $this->db->where('activity_img_id', $item['id']);
                    $this->db->update('tbl_activity_img', ['activity_img_order' => $item['order']]);
                }
            }

            $this->db->trans_complete();
            return $this->db->trans_status();
        }
        return false;
    }

    public function read_img_activity($activity_id)
    {
        $this->db->where('activity_img_ref_id', $activity_id);
        $this->db->order_by('activity_img_order', 'ASC'); // เรียงตามลำดับที่กำหนดจากการลากเลื่อน
        $this->db->order_by('activity_img_id', 'ASC'); // กรณีไม่มีค่า activity_img_order ให้เรียงตาม ID
        $query = $this->db->get('tbl_activity_img');
        return $query->result();
    }
}
