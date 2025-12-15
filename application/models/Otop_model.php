<?php
class Otop_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
		// log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function add()
    {
        // Configure image upload
        $img_config['upload_path'] = './docs/img';
        $img_config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $img_config, 'img_upload');

        // Configure Doc upload
        $doc_config['upload_path'] = './docs/file';
        $doc_config['allowed_types'] = 'doc|docx|xls|xlsx|ppt|pptx';
        $this->load->library('upload', $doc_config, 'doc_upload');

        // กำหนดค่าใน $data
        $data = array(
            'otop_name' => $this->input->post('otop_name'),
            'otop_price' => $this->input->post('otop_price'),
            'otop_type' => $this->input->post('otop_type'),
            'otop_size' => $this->input->post('otop_size'),
            'otop_weight' => $this->input->post('otop_weight'),
            'otop_detail' => $this->input->post('otop_detail'),
            'otop_location' => $this->input->post('otop_location'),
            'otop_seller' => $this->input->post('otop_seller'),
            'otop_fb' => $this->input->post('otop_fb'),
            'otop_phone' => $this->input->post('otop_phone'),
            'otop_date' => $this->input->post('otop_date'),
            'otop_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('otop_img');
        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            // ถ้ามีการอัปโหลดรูปภาพ
            $data['otop_img'] = $this->img_upload->data('file_name');
        }
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_otop', $data);
        $otop_id = $this->db->insert_id();

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['otop_img_img'])) {
            foreach ($_FILES['otop_img_img']['name'] as $index => $name) {
                if (isset($_FILES['otop_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['otop_img_img']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('otop_backend/adding');
            return;
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['otop_img_img']['name'][0])) {
            foreach ($_FILES['otop_img_img']['name'] as $index => $name) {
                $_FILES['otop_img_img_multiple']['name'] = $name;
                $_FILES['otop_img_img_multiple']['type'] = $_FILES['otop_img_img']['type'][$index];
                $_FILES['otop_img_img_multiple']['tmp_name'] = $_FILES['otop_img_img']['tmp_name'][$index];
                $_FILES['otop_img_img_multiple']['error'] = $_FILES['otop_img_img']['error'][$index];
                $_FILES['otop_img_img_multiple']['size'] = $_FILES['otop_img_img']['size'][$index];

                if ($this->img_upload->do_upload('otop_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'otop_img_ref_id' => $otop_id,
                        'otop_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_otop_img', $imgs_data);
        }

        $this->space_model->update_server_current();
		// บันทึก log การเพิ่มข้อมูลผลิตภัณฑ์ชุมชน ===================================
$this->log_model->add_log(
    'เพิ่ม',
    'ผลิตภัณฑ์ชุมชน',
    $data['otop_name'],
    $otop_id,
    array(
        'otop_type' => $data['otop_type'],
        'otop_price' => $data['otop_price'],
        'otop_location' => $data['otop_location'],
        'otop_seller' => $data['otop_seller'],
        'images_uploaded' => array(
            'main_image' => isset($data['otop_img']) ? $data['otop_img'] : null,
            'gallery_images' => count($imgs_data),
            'gallery_filenames' => array_column($imgs_data, 'otop_img_img')
        ),
        'total_images' => count($imgs_data) + (isset($data['otop_img']) ? 1 : 0) // +1 สำหรับรูปหลัก (ถ้ามี)
    )
);
// ========================================================================
        $this->session->set_flashdata('save_success', TRUE);
    }

    // public function add_old()
    // {
    //     // Check used space
    //     $used_space_mb = $this->space_model->get_used_space();
    //     $upload_limit_mb = $this->space_model->get_limit_storage();

    //     // Calculate the total space required for all files
    //     $total_space_required = 0;
    //     if (!empty($_FILES['otop_img']['name'])) {
    //         $total_space_required += $_FILES['otop_img']['size'];
    //     }

    //     // Check if there's enough space
    //     if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
    //         $this->session->set_flashdata('save_error', TRUE);
    //         redirect('otop/adding_otop');
    //         return;
    //     }

    //     // Upload configuration
    //     $config['upload_path'] = './docs/img';
    //     $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
    //     $this->load->library('upload', $config);

    //     // Upload main file
    //     if (!$this->upload->do_upload('otop_img')) {
    //         // If the file size exceeds the max_size, set flash data and redirect
    //         $this->session->set_flashdata('save_maxsize', TRUE);
    //         redirect('otop/adding_otop');
    //         return;
    //     }

    //     $data = $this->upload->data();
    //     $filename = $data['file_name'];

    //     $data = array(
    //         'otop_name' => $this->input->post('otop_name'),
    //         'otop_price' => $this->input->post('otop_price'),
    //         'otop_type' => $this->input->post('otop_type'),
    //         'otop_size' => $this->input->post('otop_size'),
    //         'otop_weight' => $this->input->post('otop_weight'),
    //         'otop_detail' => $this->input->post('otop_detail'),
    //         'otop_location' => $this->input->post('otop_location'),
    //         'otop_seller' => $this->input->post('otop_seller'),
    //         'otop_fb' => $this->input->post('otop_fb'),
    //         'otop_phone' => $this->input->post('otop_phone'),
    //         'otop_date' => $this->input->post('otop_date'),
    //         'otop_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
    //         'otop_img' => $filename
    //     );

    //     $query = $this->db->insert('tbl_otop', $data);

    //     $this->space_model->update_server_current();

    //     if ($query) {
    //         $this->session->set_flashdata('save_success', TRUE);
    //     } else {
    //         echo "<script>";
    //         echo "alert('Error !');";
    //         echo "</script>";
    //     }
    // }

    public function list_admin()
    {
        $this->db->select('o.*, GROUP_CONCAT(oi.otop_img_img) as additional_images');
        $this->db->from('tbl_otop as o');
        $this->db->join('tbl_otop_img as oi', 'o.otop_id = oi.otop_img_ref_id', 'left');
        $this->db->group_by('o.otop_id');
        $this->db->order_by('o.otop_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    // public function list_user()
    // {
    //     $this->db->select('f.*, GROUP_CONCAT(fi.user_food_img_img) as additional_images');
    //     $this->db->from('tbl_user_food as f');
    //     $this->db->join('tbl_user_food_img as fi', 'f.user_food_id = fi.user_food_img_ref_id', 'left');
    //     $this->db->group_by('f.user_food_id');
    //     $this->db->order_by('f.user_food_id', 'DESC');
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    // public function list_all()
    // {
    //     $this->db->select('t.*, GROUP_CONCAT(ti.food_img_img) as additional_images');
    //     $this->db->from('tbl_food as t');
    //     $this->db->join('tbl_food_img as ti', 't.food_id = ti.food_img_ref_id', 'left');
    //     $this->db->group_by('t.food_id');
    //     $this->db->order_by('t.food_id', 'DESC');
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    //show form edit
    public function read_otop($otop_id)
    {
        $this->db->where('otop_id', $otop_id);
        $query = $this->db->get('tbl_otop');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_img($otop_id)
    {
        $this->db->where('otop_img_ref_id', $otop_id);
        $query = $this->db->get('tbl_otop_img');
        $results = $query->result();
        usort($results, function ($a, $b) {
            return strnatcmp($a->otop_img_img, $b->otop_img_img);
        });
        return $results;
    }

    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('otop_img_img, otop_img_ref_id');
        $this->db->where('otop_img_id', $file_id);
        $query = $this->db->get('tbl_otop_img');
        $file_data = $query->row();
		
		// บันทึก log การลบรูปภาพ =================================================
        $old_data = $this->read_otop($file_data->otop_img_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->otop_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('otop_img_id', $file_id);
        $this->db->delete('tbl_otop_img');
        $this->space_model->update_server_current();
		// บันทึก log การลบรูปภาพ =================================================
        $this->log_model->add_log(
            'ลบ',
            'ผลิตภัณฑ์ชุมชน',
            'รูปภาพ: ' . $file_data->otop_img_img . ' จาก: ' . ($old_data ? $old_data->otop_name : 'ไม่ระบุ'),
            $file_data->otop_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->otop_img_img)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function edit($otop_id)
    {
		        // ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read_otop($otop_id);
		
        // Update otop information
        $data = array(
            'otop_name' => $this->input->post('otop_name'),
            'otop_price' => $this->input->post('otop_price'),
            'otop_type' => $this->input->post('otop_type'),
            'otop_size' => $this->input->post('otop_size'),
            'otop_weight' => $this->input->post('otop_weight'),
            'otop_detail' => $this->input->post('otop_detail'),
            'otop_location' => $this->input->post('otop_location'),
            'otop_seller' => $this->input->post('otop_seller'),
            'otop_fb' => $this->input->post('otop_fb'),
            'otop_phone' => $this->input->post('otop_phone'),
            'otop_date' => $this->input->post('otop_date'),
            'otop_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('otop_id', $otop_id);
        $this->db->update('tbl_otop', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['otop_img_img'])) {
            foreach ($_FILES['otop_img_img']['name'] as $index => $name) {
                if (isset($_FILES['otop_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['otop_img_img']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('otop_backend/adding');
            return;
        }

        $img_config['upload_path'] = './docs/img';
        $img_config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $img_config, 'img_upload');

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('otop_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_otop', array('otop_id' => $otop_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->otop_img) {
                $old_file_path = './docs/img/' . $old_document->otop_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['otop_img'] = $this->img_upload->data('file_name');
            $this->db->where('otop_id', $otop_id);
            $this->db->update('tbl_otop', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['otop_img_img']['name'][0])) {

            foreach ($_FILES['otop_img_img']['name'] as $index => $name) {
                $_FILES['otop_img_img_multiple']['name'] = $name;
                $_FILES['otop_img_img_multiple']['type'] = $_FILES['otop_img_img']['type'][$index];
                $_FILES['otop_img_img_multiple']['tmp_name'] = $_FILES['otop_img_img']['tmp_name'][$index];
                $_FILES['otop_img_img_multiple']['error'] = $_FILES['otop_img_img']['error'][$index];
                $_FILES['otop_img_img_multiple']['size'] = $_FILES['otop_img_img']['size'][$index];

                if ($this->img_upload->do_upload('otop_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'otop_img_ref_id' => $otop_id,
                        'otop_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_otop_img', $imgs_data);
        }
        $this->space_model->update_server_current();
		// === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->otop_name != $data['otop_name']) {
                $changes['otop_name'] = array(
                    'old' => $old_data->otop_name,
                    'new' => $data['otop_name']
                );
            }
            if ($old_data->otop_type != $data['otop_type']) {
                $changes['otop_type'] = array(
                    'old' => $old_data->otop_type,
                    'new' => $data['otop_type']
                );
            }
            if ($old_data->otop_detail != $data['otop_detail']) {
                $changes['otop_detail'] = array(
                    'old' => $old_data->otop_detail,
                    'new' => $data['otop_detail']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'otop_img_img');
        }

        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'ผลิตภัณฑ์ชุมชน',
            $data['otop_name'],
            $otop_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data)
            )
        );
        // =======================================================================
        $this->session->set_flashdata('save_success', TRUE);
    }


    // public function read_com_otop($otop_id)
    // {
    //     $this->db->where('otop_com_ref_id', $otop_id);
    //     $this->db->order_by('otop_com_ref_id', 'DESC');
    //     $query = $this->db->get('tbl_otop_com');
    //     return $query->result();
    // }

    // public function read_com_reply_otop($otop_com_id)
    // {
    //     $this->db->where('otop_com_reply_ref_id', $otop_com_id);
    //     $query = $this->db->get('tbl_otop_com_reply');
    //     return $query->result();
    // }

    // public function edit($otop_id)
    // {
    //     $old_document = $this->db->get_where('tbl_otop', array('otop_id' => $otop_id))->row();

    //     $update_doc_file = !empty($_FILES['otop_img']['name']) && $old_document->otop_img != $_FILES['otop_img']['name'];

    //     // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
    //     if ($update_doc_file) {
    //         $old_file_path = './docs/img/' . $old_document->otop_img;
    //         if (file_exists($old_file_path)) {
    //             unlink($old_file_path);
    //         }

    //         // Check used space
    //         $used_space_mb = $this->space_model->get_used_space();
    //         $upload_limit_mb = $this->space_model->get_limit_storage();

    //         $total_space_required = 0;
    //         if (!empty($_FILES['otop_img']['name'])) {
    //             $total_space_required += $_FILES['otop_img']['size'];
    //         }

    //         if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
    //             $this->session->set_flashdata('save_error', TRUE);
    //             redirect('otop/editing');
    //             return;
    //         }

    //         $config['upload_path'] = './docs/img';
    //         $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
    //         $this->load->library('upload', $config);

    //         if (!$this->upload->do_upload('otop_img')) {
    //             echo $this->upload->display_errors();
    //             return;
    //         }

    //         $data = $this->upload->data();
    //         $filename = $data['file_name'];
    //     } else {
    //         // ใช้รูปภาพเดิม
    //         $filename = $old_document->otop_img;
    //     }

    //     // Update otop information
    //     $data = array(
    //         'otop_name' => $this->input->post('otop_name'),
    //         'otop_price' => $this->input->post('otop_price'),
    //         'otop_type' => $this->input->post('otop_type'),
    //         'otop_size' => $this->input->post('otop_size'),
    //         'otop_weight' => $this->input->post('otop_weight'),
    //         'otop_detail' => $this->input->post('otop_detail'),
    //         'otop_location' => $this->input->post('otop_location'),
    //         'otop_seller' => $this->input->post('otop_seller'),
    //         'otop_fb' => $this->input->post('otop_fb'),
    //         'otop_phone' => $this->input->post('otop_phone'),
    //         'otop_date' => $this->input->post('otop_date'),
    //         'otop_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
    //         'otop_img' => $filename
    //     );

    //     $this->db->where('otop_id', $otop_id);
    //     $this->db->update('tbl_otop', $data);

    //     // อัปโหลดและบันทึกไฟล์ใหม่ลงโฟลเดอร์
    //     if (!empty($_FILES['otop_img_img']['name'])) {
    //         $upload_config['upload_path'] = './docs/img';
    //         $upload_config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
    //         $this->load->library('upload', $upload_config);

    //         $upload_success = true; // ตั้งค่าเริ่มต้นเป็น true

    //         foreach ($_FILES['otop_img_img']['name'] as $index => $name) {
    //             $_FILES['otop_img']['name'] = $name;
    //             $_FILES['otop_img']['type'] = $_FILES['otop_img_img']['type'][$index];
    //             $_FILES['otop_img']['tmp_name'] = $_FILES['otop_img_img']['tmp_name'][$index];
    //             $_FILES['otop_img']['error'] = $_FILES['otop_img_img']['error'][$index];
    //             $_FILES['otop_img']['size'] = $_FILES['otop_img_img']['size'][$index];

    //             if (!$this->upload->do_upload('otop_img')) {
    //                 // echo $this->upload->display_errors();
    //                 $upload_success = false; // หากเกิดข้อผิดพลาดในการอัปโหลด ตั้งค่าเป็น false
    //                 break; // หยุดการทำงานลูป
    //             }

    //             $upload_data = $this->upload->data();
    //             $image_path = $upload_data['file_name'];

    //             // สร้างข้อมูลสำหรับบันทึกลงฐานข้อมูล tbl_otop_img
    //             $image_data = array(
    //                 'otop_img_ref_id' => $otop_id,
    //                 'otop_img_img' => $image_path
    //             );

    //             $this->db->insert('tbl_otop_img', $image_data);
    //         }

    //         if ($upload_success) {
    //             // ลบรูปภาพเก่าที่เกี่ยวข้องกับกิจกรรม
    //             $this->db->where('otop_img_ref_id', $otop_id);
    //             $existing_images = $this->db->get('tbl_otop_img')->result();

    //             foreach ($existing_images as $existing_image) {
    //                 $old_file_path = './docs/img/' . $existing_image->otop_img_img;
    //                 if (file_exists($old_file_path)) {
    //                     unlink($old_file_path);
    //                 }
    //             }

    //             $this->db->where('otop_img_ref_id', $otop_id);
    //             $this->db->delete('tbl_otop_img');

    //             // เพิ่มรูปภาพใหม่ลงไป
    //             foreach ($_FILES['otop_img_img']['name'] as $index => $name) {
    //                 $_FILES['otop_img']['name'] = $name;
    //                 $_FILES['otop_img']['type'] = $_FILES['otop_img_img']['type'][$index];
    //                 $_FILES['otop_img']['tmp_name'] = $_FILES['otop_img_img']['tmp_name'][$index];
    //                 $_FILES['otop_img']['error'] = $_FILES['otop_img_img']['error'][$index];
    //                 $_FILES['otop_img']['size'] = $_FILES['otop_img_img']['size'][$index];

    //                 if (!$this->upload->do_upload('otop_img')) {
    //                     // echo $this->upload->display_errors();
    //                     break; // หยุดการทำงานลูปหากรูปภาพมีปัญหา
    //                 }

    //                 $upload_data = $this->upload->data();
    //                 $image_path = $upload_data['file_name'];

    //                 // สร้างข้อมูลสำหรับบันทึกลงฐานข้อมูล tbl_otop_img
    //                 $image_data = array(
    //                     'otop_img_ref_id' => $otop_id,
    //                     'otop_img_img' => $image_path
    //                 );

    //                 $this->db->insert('tbl_otop_img', $image_data);
    //             }
    //         }
    //     }
    //     $this->space_model->update_server_current();
    //     $this->session->set_flashdata('save_success', TRUE);
    // }


    public function del_otop($otop_id)
    {
        $old_document = $this->db->get_where('tbl_otop', array('otop_id' => $otop_id))->row();

        $old_file_path = './docs/img/' . $old_document->otop_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_otop', array('otop_id' => $otop_id));
        $this->space_model->update_server_current();
		// บันทึก log การลบ =================================================
        if ($old_document) {
            $this->log_model->add_log(
                'ลบ',
                'ผลิตภัณฑ์ชุมชน',
                $old_document->otop_name,
                $otop_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // =======================================================================
    }

    public function del_otop_img($otop_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $images = $this->db->get_where('tbl_otop_img', array('otop_img_ref_id' => $otop_id))->result();

        // ลบรูปภาพจากตาราง tbl_otop_img
        $this->db->where('otop_img_ref_id', $otop_id);
        $this->db->delete('tbl_otop_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($images as $image) {
            $image_path = './docs/img/' . $image->otop_img_img;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }

    public function updateOtopStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $otopId = $this->input->post('otop_id'); // รับค่า otop_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_otop ในฐานข้อมูลของคุณ
            $data = array(
                'otop_status' => $newStatus
            );
            $this->db->where('otop_id', $otopId); // ระบุ otop_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_otop', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function get_otops()
    {
        $this->db->select('*');
        $this->db->from('tbl_otop');
        $this->db->order_by('otop_datesave', 'DESC');
        return $this->db->get()->result();
    }


    public function get_images_for_otop($otop_id)
    {
        $this->db->select('otop_img_img');
        $this->db->from('tbl_otop_img');
        $this->db->where('otop_img_ref_id', $otop_id);
        return $this->db->get()->result();
    }

    public function sum_food_views()
    {
        // คำนวณผลรวมของ tbl_food
        $this->db->select('SUM(food_view) as total_views');
        $this->db->from('tbl_food');
        $query_food = $this->db->get();

        // คำนวณผลรวมของ tbl_user_food
        $this->db->select('SUM(user_food_view) as total_user_views');
        $this->db->from('tbl_user_food');
        $query_user_food = $this->db->get();

        // นำผลรวมของทั้งสองตารางมาบวกกัน
        $total_views = $query_food->row()->total_views + $query_user_food->row()->total_user_views;

        return $total_views;
    }

    public function sum_food_likes()
    {
        // คำนวณผลรวมของ tbl_food
        $this->db->select('SUM(food_like_like) as total_likes');
        $this->db->from('tbl_food_like');
        $query_food = $this->db->get();

        // คำนวณผลรวมของ tbl_user_food
        $this->db->select('SUM(user_food_like_like) as total_user_likes');
        $this->db->from('tbl_user_food_like');
        $query_user_food = $this->db->get();

        // นำผลรวมของทั้งสองตารางมาบวกกัน
        $total_likes = $query_food->row()->total_likes + $query_user_food->row()->total_user_likes;

        return $total_likes;
    }

    public function otop_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_otop');
        $this->db->where('tbl_otop.otop_status', 'show');
        $this->db->order_by('tbl_otop.otop_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function search($searchTerm)
    {
        $this->db->select('*');
        $this->db->from('tbl_otop');
        $this->db->where('tbl_otop.otop_status', 'show');
        $this->db->like('tbl_otop.otop_name', $searchTerm);  // แทน 'otop_name' ด้วยฟิลด์ที่คุณต้องการค้นหา
        $this->db->or_like('tbl_otop.otop_datesave', $searchTerm);
        $this->db->or_like('tbl_otop.otop_detail', $searchTerm);
        $this->db->or_like('tbl_otop.otop_type', $searchTerm);
        $this->db->or_like('tbl_otop.otop_size', $searchTerm);
        $this->db->or_like('tbl_otop.otop_weight', $searchTerm);
        $this->db->or_like('tbl_otop.otop_price', $searchTerm);
        $this->db->or_like('tbl_otop.otop_location', $searchTerm);
        $this->db->or_like('tbl_otop.otop_phone', $searchTerm);
        $this->db->or_like('tbl_otop.otop_fb', $searchTerm);
        $this->db->order_by('tbl_otop.otop_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }


    // public function del_com($otop_com_id)
    // {
    //     return $this->db->where('otop_com_id', $otop_com_id)->delete('tbl_otop_com');
    // }

    // public function del_reply($otop_com_id)
    // {
    //     return $this->db->where('otop_com_reply_ref_id', $otop_com_id)->delete('tbl_otop_com_reply');
    // }

    // public function del_com_reply($otop_com_reply_id)
    // {
    //     return $this->db->where('otop_com_reply_id', $otop_com_reply_id)->delete('tbl_otop_com_reply');
    // }

    // ส่วนของ user ***************************************************************************************************************************************************************************************************************************************************************
    // public function read_user_food($user_food_id)
    // {
    //     $this->db->where('user_food_id', $user_food_id);
    //     $query = $this->db->get('tbl_user_food');
    //     if ($query->num_rows() > 0) {
    //         $data = $query->row();
    //         return $data;
    //     }
    //     return FALSE;
    // }

    // public function read_img_user_food($user_food_id)
    // {
    //     $this->db->where('user_food_img_ref_id', $user_food_id);
    //     $this->db->order_by('user_food_img_id', 'DESC');
    //     $query = $this->db->get('tbl_user_food_img');
    //     return $query->result();
    // }

    // public function read_user_com_food($user_food_id)
    // {
    //     $this->db->where('user_food_com_ref_id', $user_food_id);
    //     $this->db->order_by('user_food_com_ref_id', 'DESC');
    //     $query = $this->db->get('tbl_user_food_com');
    //     return $query->result();
    // }

    // public function read_user_com_reply_food($user_food_com_id)
    // {
    //     $this->db->where('user_food_com_reply_ref_id', $user_food_com_id);
    //     $query = $this->db->get('tbl_user_food_com_reply');
    //     return $query->result();
    // }


    // public function edit_User_Food($user_food_id, $user_food_name, $user_food_detail, $user_food_location, $user_food_timeopen, $user_food_timeclose, $user_food_date, $user_food_phone, $user_food_youtube, $user_food_map)
    // {
    //     $old_document = $this->db->get_where('tbl_user_food', array('user_food_id' => $user_food_id))->row();

    //     $update_doc_file = !empty($_FILES['user_food_img']['name']) && $old_document->user_food_img != $_FILES['user_food_img']['name'];

    //     // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
    //     if ($update_doc_file) {
    //         $old_file_path = './docs/img/' . $old_document->user_food_img;
    //         if (file_exists($old_file_path)) {
    //             unlink($old_file_path);
    //         }

    //         // Check used space
    //         $used_space_mb = $this->space_model->get_used_space();
    //         $upload_limit_mb = $this->space_model->get_limit_storage();

    //         $total_space_required = 0;
    //         if (!empty($_FILES['user_food_img']['name'])) {
    //             $total_space_required += $_FILES['user_food_img']['size'];
    //         }

    //         if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
    //             $this->session->set_flashdata('save_error', TRUE);
    //             redirect('food/editing_User_Food');
    //             return;
    //         }

    //         $config['upload_path'] = './docs/img';
    //         $config['allowed_types'] = 'gif|jpg|png';
    //         $config['max_size'] = $upload_limit_mb * 1024;
    //         $this->load->library('upload', $config);

    //         if (!$this->upload->do_upload('user_food_img')) {
    //             echo $this->upload->display_errors();
    //             return;
    //         }

    //         $data = $this->upload->data();
    //         $filename = $data['file_name'];
    //     } else {
    //         // ใช้รูปภาพเดิม
    //         $filename = $old_document->user_food_img;
    //     }

    //     // Update user_food information
    //     $data = array(
    //         'user_food_name' => $this->input->post('user_food_name'),
    //         'user_food_detail' => $this->input->post('user_food_detail'),
    //         'user_food_location' => $this->input->post('user_food_location'),
    //         'user_food_timeopen' => $this->input->post('user_food_timeopen'),
    //         'user_food_timeclose' => $this->input->post('user_food_timeclose'),
    //         'user_food_date' => $this->input->post('user_food_date'),
    //         'user_food_phone' => $this->input->post('user_food_phone'),
    //         'user_food_youtube' => $this->input->post('user_food_youtube'),
    //         'user_food_map' => $this->input->post('user_food_map'),
    //         'user_food_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
    //         'user_food_img' => $filename
    //     );

    //     $this->db->where('user_food_id', $user_food_id);
    //     $this->db->update('tbl_user_food', $data);

    //     // อัปโหลดและบันทึกไฟล์ใหม่ลงโฟลเดอร์
    //     if (!empty($_FILES['user_food_img_img']['name'])) {
    //         $upload_config['upload_path'] = './docs/img';
    //         $upload_config['allowed_types'] = 'gif|jpg|png';
    //         $this->load->library('upload', $upload_config);

    //         $upload_success = true; // ตั้งค่าเริ่มต้นเป็น true

    //         foreach ($_FILES['user_food_img_img']['name'] as $index => $name) {
    //             $_FILES['user_food_img']['name'] = $name;
    //             $_FILES['user_food_img']['type'] = $_FILES['user_food_img_img']['type'][$index];
    //             $_FILES['user_food_img']['tmp_name'] = $_FILES['user_food_img_img']['tmp_name'][$index];
    //             $_FILES['user_food_img']['error'] = $_FILES['user_food_img_img']['error'][$index];
    //             $_FILES['user_food_img']['size'] = $_FILES['user_food_img_img']['size'][$index];

    //             if (!$this->upload->do_upload('user_food_img')) {
    //                 // echo $this->upload->display_errors();
    //                 $upload_success = false; // หากเกิดข้อผิดพลาดในการอัปโหลด ตั้งค่าเป็น false
    //                 break; // หยุดการทำงานลูป
    //             }

    //             $upload_data = $this->upload->data();
    //             $image_path = $upload_data['file_name'];

    //             // สร้างข้อมูลสำหรับบันทึกลงฐานข้อมูล tbl_user_food_img
    //             $image_data = array(
    //                 'user_food_img_ref_id' => $user_food_id,
    //                 'user_food_img_img' => $image_path
    //             );

    //             $this->db->insert('tbl_user_food_img', $image_data);
    //         }

    //         if ($upload_success) {
    //             // ลบรูปภาพเก่าที่เกี่ยวข้องกับกิจกรรม
    //             $this->db->where('user_food_img_ref_id', $user_food_id);
    //             $existing_images = $this->db->get('tbl_user_food_img')->result();

    //             foreach ($existing_images as $existing_image) {
    //                 $old_file_path = './docs/img/' . $existing_image->user_food_img_img;
    //                 if (file_exists($old_file_path)) {
    //                     unlink($old_file_path);
    //                 }
    //             }

    //             $this->db->where('user_food_img_ref_id', $user_food_id);
    //             $this->db->delete('tbl_user_food_img');

    //             // เพิ่มรูปภาพใหม่ลงไป
    //             foreach ($_FILES['user_food_img_img']['name'] as $index => $name) {
    //                 $_FILES['user_food_img']['name'] = $name;
    //                 $_FILES['user_food_img']['type'] = $_FILES['user_food_img_img']['type'][$index];
    //                 $_FILES['user_food_img']['tmp_name'] = $_FILES['user_food_img_img']['tmp_name'][$index];
    //                 $_FILES['user_food_img']['error'] = $_FILES['user_food_img_img']['error'][$index];
    //                 $_FILES['user_food_img']['size'] = $_FILES['user_food_img_img']['size'][$index];

    //                 if (!$this->upload->do_upload('user_food_img')) {
    //                     // echo $this->upload->display_errors();
    //                     break; // หยุดการทำงานลูปหากรูปภาพมีปัญหา
    //                 }

    //                 $upload_data = $this->upload->data();
    //                 $image_path = $upload_data['file_name'];

    //                 // สร้างข้อมูลสำหรับบันทึกลงฐานข้อมูล tbl_user_food_img
    //                 $image_data = array(
    //                     'user_food_img_ref_id' => $user_food_id,
    //                     'user_food_img_img' => $image_path
    //                 );

    //                 $this->db->insert('tbl_user_food_img', $image_data);
    //             }
    //         }
    //     }
    //     $this->space_model->update_server_current();
    //     $this->session->set_flashdata('save_success', TRUE);
    // }

    // public function del_user_food($user_food_id)
    // {
    //     $old_document = $this->db->get_where('tbl_user_food', array('user_food_id' => $user_food_id))->row();

    //     $old_file_path = './docs/img/' . $old_document->user_food_img;
    //     if (file_exists($old_file_path)) {
    //         unlink($old_file_path);
    //     }

    //     $this->db->delete('tbl_user_food', array('user_food_id' => $user_food_id));
    //     $this->space_model->update_server_current();
    // }

    // public function del_user_food_img($user_food_id)
    // {
    //     // ดึงข้อมูลรายการรูปภาพก่อน
    //     $images = $this->db->get_where('tbl_user_food_img', array('user_food_img_ref_id' => $user_food_id))->result();

    //     // ลบรูปภาพจากตาราง tbl_food_img
    //     $this->db->where('user_food_img_ref_id', $user_food_id);
    //     $this->db->delete('tbl_user_food_img');

    //     // ลบไฟล์รูปภาพที่เกี่ยวข้อง
    //     foreach ($images as $image) {
    //         $image_path = './docs/img/' . $image->user_food_img_img;
    //         if (file_exists($image_path)) {
    //             unlink($image_path);
    //         }
    //     }
    // }

    // public function updateUserFoodStatus()
    // {
    //     // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
    //     if ($this->input->post()) {
    //         $userfoodId = $this->input->post('user_food_id'); // รับค่า food_id
    //         $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

    //         // ทำการอัพเดตค่าในตาราง tbl_food ในฐานข้อมูลของคุณ
    //         $data = array(
    //             'user_food_status' => $newStatus
    //         );
    //         $this->db->where('user_food_id', $userfoodId); // ระบุ food_id ของแถวที่ต้องการอัพเดต
    //         $this->db->update('tbl_user_food', $data);

    //         // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
    //         // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
    //         $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
    //         echo json_encode($response);
    //     } else {
    //         // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
    //         show_404();
    //     }
    // }

    // public function del_com_user($user_food_com_id)
    // {
    //     return $this->db->where('user_food_com_id', $user_food_com_id)->delete('tbl_user_food_com');
    // }

    // public function del_reply_user($user_food_com_id)
    // {
    //     return $this->db->where('user_food_com_reply_ref_id', $user_food_com_id)->delete('tbl_user_food_com_reply');
    // }

    // public function del_com_reply_user($user_food_com_reply_id)
    // {
    //     return $this->db->where('user_food_com_reply_id', $user_food_com_reply_id)->delete('tbl_user_food_com_reply');
    // }

    // ************************************************************************************************************


}
