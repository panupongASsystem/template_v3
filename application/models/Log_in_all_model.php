<?php
class Log_in_all_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        // log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function add_Travel()
    {
        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        $travel_imgs = $_FILES['travel_imgs'];

        foreach ($travel_imgs['size'] as $size) {
            $total_space_required += $size;
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('travel/adding_Travel');
            return;
        }

        $travel_data = array(
            'travel_name' => $this->input->post('travel_name'),
            'travel_refer' => $this->input->post('travel_refer'),
            'travel_detail' => $this->input->post('travel_detail'),
            'travel_location' => $this->input->post('travel_location'),
            'travel_timeopen' => $this->input->post('travel_timeopen'),
            'travel_timeclose' => $this->input->post('travel_timeclose'),
            'travel_day' => $this->input->post('travel_day'),
            'travel_date' => $this->input->post('travel_date'),
            'travel_phone' => $this->input->post('travel_phone'),
            'travel_youtube' => $this->input->post('travel_youtube'),
            'travel_map' => $this->input->post('travel_map'),
            // 'travel_lat' => $this->input->post('travel_lat'),
            // 'travel_long' => $this->input->post('travel_long'),
            'travel_by' => $this->session->userdata('m_fname') // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $config);

        $travel_img = $_FILES['travel_img'];
        $travel_imgs = $_FILES['travel_imgs'];

        $this->db->trans_start();
        $this->db->insert('tbl_travel', $travel_data);
        $travel_id = $this->db->insert_id();

        // Upload and update travel_img
        $_FILES['travel_img']['name'] = $travel_img['name'];
        $_FILES['travel_img']['type'] = $travel_img['type'];
        $_FILES['travel_img']['tmp_name'] = $travel_img['tmp_name'];
        $_FILES['travel_img']['error'] = $travel_img['error'];
        $_FILES['travel_img']['size'] = $travel_img['size'];

        if (!$this->upload->do_upload('travel_img')) {
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('travel/adding_Travel'); // กลับไปหน้าเดิม
            return;
        }

        $upload_data = $this->upload->data();
        $travel_img_file = $upload_data['file_name'];

        // Update travel_img column with the uploaded image
        $travel_img_data = array('travel_img' => $travel_img_file);
        $this->db->where('travel_id', $travel_id);
        $this->db->update('tbl_travel', $travel_img_data);

        // Upload and insert data into tbl_travel_img
        $image_data = array(); // Initialize the array
        foreach ($travel_imgs['name'] as $index => $name) {
            $_FILES['travel_img']['name'] = $name;
            $_FILES['travel_img']['type'] = $travel_imgs['type'][$index];
            $_FILES['travel_img']['tmp_name'] = $travel_imgs['tmp_name'][$index];
            $_FILES['travel_img']['error'] = $travel_imgs['error'][$index];
            $_FILES['travel_img']['size'] = $travel_imgs['size'][$index];

            if (!$this->upload->do_upload('travel_img')) {
                $this->session->set_flashdata('save_maxsize', TRUE);
                redirect('travel/adding_Travel'); // กลับไปหน้าเดิม
                return;
            }

            $upload_data = $this->upload->data();
            $image_data[] = array(
                'travel_img_ref_id' => $travel_id,
                'travel_img_img' => $upload_data['file_name']
            );
        }

        $this->db->insert_batch('tbl_travel_img', $image_data);

        $this->db->trans_complete();

        $this->space_model->update_server_current();

        // บันทึก log การเพิ่มข้อมูลการท่องเที่ยว ===================================
        $this->log_model->add_log(
            'เพิ่ม',
            'จัดการสถานที่ท่องเที่ยว',
            $travel_data['travel_name'],
            $travel_id,
            array(
                'travel_location' => $travel_data['travel_location'],
                'travel_refer' => $travel_data['travel_refer'],
                'images_uploaded' => array(
                    'main_image' => $travel_img_file,
                    'gallery_images' => count($image_data),
                    'gallery_filenames' => array_column($image_data, 'travel_img_img')
                ),
                'total_images' => count($image_data) + 1 // +1 สำหรับรูปหลัก
            )
        );
        // ========================================================================

        $this->session->set_flashdata('save_success', TRUE);
    }

    public function add_background_personnel()
    {
        // Check used space
        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        // Calculate the total space required for all files
        $total_space_required = 0;
        if (!empty($_FILES['background_personnel_img']['name'])) {
            $total_space_required += $_FILES['background_personnel_img']['size'];
        }

        // Check if there's enough space
        if ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit_mb) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('Background_personnel_backend/adding_background_personnel');
            return;
        }

        // Upload configuration
        $config['upload_path'] = './docs/img';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|jfif';
        $this->load->library('upload', $config);

        // Upload main file
        if (!$this->upload->do_upload('background_personnel_img')) {
            // If the file size exceeds the max_size, set flash data and redirect
            $this->session->set_flashdata('save_maxsize', TRUE);
            redirect('Background_personnel_backend/adding_background_personnel');
            return;
        }

        $upload_data = $this->upload->data();
        $filename = $upload_data['file_name'];

        $personnel_data = array(
            'background_personnel_name' => $this->input->post('background_personnel_name'),
            'background_personnel_rank' => $this->input->post('background_personnel_rank'),
            'background_personnel_phone' => $this->input->post('background_personnel_phone'),
            'background_personnel_by' => $this->session->userdata('m_fname'),
            'background_personnel_img' => $filename
        );

        $query = $this->db->insert('tbl_background_personnel', $personnel_data);
        // บันทึก log การเพิ่มข้อมูล =================================================
        $background_personnel_id = $this->db->insert_id();
        // =======================================================================

        $this->space_model->update_server_current();

        if ($query) {
            // บันทึก log การเพิ่มข้อมูล =================================================
            $this->log_model->add_log(
                'เพิ่ม',
                'บุคลากร',
                $personnel_data['background_personnel_name'],
                $background_personnel_id,
                array(
                    'info' => array(
                        'rank' => $personnel_data['background_personnel_rank'],
                        'phone' => $personnel_data['background_personnel_phone'],
                        'image_file' => $filename,
                        'file_size' => $upload_data['file_size'] . ' KB',
                        'image_width' => $upload_data['image_width'] . 'px',
                        'image_height' => $upload_data['image_height'] . 'px'
                    )
                )
            );
            // =======================================================================

            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('Error !');";
            echo "</script>";
        }
    }

    public function add()
    {
        // Configure PDF upload
        $pdf_config['upload_path'] = './docs/file';
        $pdf_config['allowed_types'] = 'pdf';
        $this->load->library('upload', $pdf_config, 'pdf_upload');

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
            'news_name' => $this->input->post('news_name'),
            'news_detail' => $this->input->post('news_detail'),
            'news_date' => $this->input->post('news_date'),
            'news_link' => $this->input->post('news_link'),
            'news_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('news_img');
        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            // ถ้ามีการอัปโหลดรูปภาพ
            $data['news_img'] = $this->img_upload->data('file_name');
        }
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_news', $data);
        $news_id = $this->db->insert_id();

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['news_img_img'])) {
            foreach ($_FILES['news_img_img']['name'] as $index => $name) {
                if (isset($_FILES['news_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['news_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['news_pdf_pdf'])) {
            foreach ($_FILES['news_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['news_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['news_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['news_file_doc'])) {
            foreach ($_FILES['news_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['news_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['news_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('news_backend/adding');
            return;
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['news_img_img']['name'][0])) {
            foreach ($_FILES['news_img_img']['name'] as $index => $name) {
                $_FILES['news_img_img_multiple']['name'] = $name;
                $_FILES['news_img_img_multiple']['type'] = $_FILES['news_img_img']['type'][$index];
                $_FILES['news_img_img_multiple']['tmp_name'] = $_FILES['news_img_img']['tmp_name'][$index];
                $_FILES['news_img_img_multiple']['error'] = $_FILES['news_img_img']['error'][$index];
                $_FILES['news_img_img_multiple']['size'] = $_FILES['news_img_img']['size'][$index];

                if ($this->img_upload->do_upload('news_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'news_img_ref_id' => $news_id,
                        'news_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_news_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์PDFเพิ่มเติมหรือไม่
        if (!empty($_FILES['news_pdf_pdf']['name'][0])) {
            foreach ($_FILES['news_pdf_pdf']['name'] as $index => $name) {
                $_FILES['news_pdf_pdf_multiple']['name'] = $name;
                $_FILES['news_pdf_pdf_multiple']['type'] = $_FILES['news_pdf_pdf']['type'][$index];
                $_FILES['news_pdf_pdf_multiple']['tmp_name'] = $_FILES['news_pdf_pdf']['tmp_name'][$index];
                $_FILES['news_pdf_pdf_multiple']['error'] = $_FILES['news_pdf_pdf']['error'][$index];
                $_FILES['news_pdf_pdf_multiple']['size'] = $_FILES['news_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('news_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'news_pdf_ref_id' => $news_id,
                        'news_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_news_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์Docเพิ่มเติมหรือไม่
        if (!empty($_FILES['news_file_doc']['name'][0])) {
            foreach ($_FILES['news_file_doc']['name'] as $index => $name) {
                $_FILES['news_file_doc_multiple']['name'] = $name;
                $_FILES['news_file_doc_multiple']['type'] = $_FILES['news_file_doc']['type'][$index];
                $_FILES['news_file_doc_multiple']['tmp_name'] = $_FILES['news_file_doc']['tmp_name'][$index];
                $_FILES['news_file_doc_multiple']['error'] = $_FILES['news_file_doc']['error'][$index];
                $_FILES['news_file_doc_multiple']['size'] = $_FILES['news_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('news_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'news_file_ref_id' => $news_id,
                        'news_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_news_file', $doc_data);
        }
        $this->space_model->update_server_current();

        // บันทึก log การเพิ่มข้อมูล =================================================
        $this->log_model->add_log(
            'เพิ่ม',
            'ข่าวประชาสัมพันธ์',
            $data['news_name'],
            $news_id,
            array(
                'files_uploaded' => array(
                    'images' => count($imgs_data),
                    'pdfs' => count($pdf_data),
                    'docs' => count($doc_data)
                )
            )
        );
        // =======================================================================

        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_pdf($pdf_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
        $this->db->select('news_pdf_pdf, news_pdf_ref_id');
        $this->db->where('news_pdf_id', $pdf_id);
        $query = $this->db->get('tbl_news_pdf');
        $file_data = $query->row();

        // บันทึก log การลบไฟล์ PDF =============================================
        $old_data = $this->read($file_data->news_pdf_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->news_pdf_pdf;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('news_pdf_id', $pdf_id);
        $this->db->delete('tbl_news_pdf');
        $this->space_model->update_server_current();

        // บันทึก log การลบไฟล์ PDF =============================================
        $this->log_model->add_log(
            'ลบ',
            'ข่าวประชาสัมพันธ์',
            'ไฟล์ PDF: ' . $file_data->news_pdf_pdf . ' จาก: ' . ($old_data ? $old_data->news_name : 'ไม่ระบุ'),
            $file_data->news_pdf_ref_id,
            array('file_type' => 'PDF', 'file_name' => $file_data->news_pdf_pdf)
        );
        // =======================================================================

        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_doc($doc_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $doc_id
        $this->db->select('news_file_doc, news_file_ref_id');
        $this->db->where('news_file_id', $doc_id);
        $query = $this->db->get('tbl_news_file');
        $file_data = $query->row();

        // บันทึก log การลบไฟล์ DOC ========================================
        $old_data = $this->read($file_data->news_file_ref_id);
        // ================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->news_file_doc;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('news_file_id', $doc_id);
        $this->db->delete('tbl_news_file');
        $this->space_model->update_server_current();

        // บันทึก log การลบไฟล์ DOC ========================================
        $this->log_model->add_log(
            'ลบ',
            'ข่าวประชาสัมพันธ์',
            'ไฟล์ DOC: ' . $file_data->news_file_doc . ' จาก: ' . ($old_data ? $old_data->news_name : 'ไม่ระบุ'),
            $file_data->news_file_ref_id,
            array('file_type' => 'DOC', 'file_name' => $file_data->news_file_doc)
        );
        // ================================================================

        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('news_img_img, news_img_ref_id');
        $this->db->where('news_img_id', $file_id);
        $query = $this->db->get('tbl_news_img');
        $file_data = $query->row();

        // บันทึก log การลบรูปภาพ =================================================
        $old_data = $this->read($file_data->news_img_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->news_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // บันทึก log การลบรูปภาพ =================================================
        $this->log_model->add_log(
            'ลบ',
            'ข่าวประชาสัมพันธ์',
            'รูปภาพ: ' . $file_data->news_img_img . ' จาก: ' . ($old_data ? $old_data->news_name : 'ไม่ระบุ'),
            $file_data->news_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->news_img_img)
        );
        // =======================================================================

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('news_img_id', $file_id);
        $this->db->delete('tbl_news_img');
        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', TRUE);
    }


    public function edit($news_id)
    {
        // ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($news_id);

        // Update news information
        $data = array(
            'news_name' => $this->input->post('news_name'),
            'news_detail' => $this->input->post('news_detail'),
            'news_date' => $this->input->post('news_date'),
            'news_link' => $this->input->post('news_link'),
            'news_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('news_id', $news_id);
        $this->db->update('tbl_news', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['news_img_img'])) {
            foreach ($_FILES['news_img_img']['name'] as $index => $name) {
                if (isset($_FILES['news_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['news_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['news_pdf_pdf'])) {
            foreach ($_FILES['news_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['news_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['news_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['news_file_doc'])) {
            foreach ($_FILES['news_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['news_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['news_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('news_backend/adding');
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
        $img_main = $this->img_upload->do_upload('news_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_news', array('news_id' => $news_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->news_img) {
                $old_file_path = './docs/img/' . $old_document->news_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['news_img'] = $this->img_upload->data('file_name');
            $this->db->where('news_id', $news_id);
            $this->db->update('tbl_news', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['news_img_img']['name'][0])) {

            foreach ($_FILES['news_img_img']['name'] as $index => $name) {
                $_FILES['news_img_img_multiple']['name'] = $name;
                $_FILES['news_img_img_multiple']['type'] = $_FILES['news_img_img']['type'][$index];
                $_FILES['news_img_img_multiple']['tmp_name'] = $_FILES['news_img_img']['tmp_name'][$index];
                $_FILES['news_img_img_multiple']['error'] = $_FILES['news_img_img']['error'][$index];
                $_FILES['news_img_img_multiple']['size'] = $_FILES['news_img_img']['size'][$index];

                if ($this->img_upload->do_upload('news_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'news_img_ref_id' => $news_id,
                        'news_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_news_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['news_pdf_pdf']['name'][0])) {
            foreach ($_FILES['news_pdf_pdf']['name'] as $index => $name) {
                $_FILES['news_pdf_pdf_multiple']['name'] = $name;
                $_FILES['news_pdf_pdf_multiple']['type'] = $_FILES['news_pdf_pdf']['type'][$index];
                $_FILES['news_pdf_pdf_multiple']['tmp_name'] = $_FILES['news_pdf_pdf']['tmp_name'][$index];
                $_FILES['news_pdf_pdf_multiple']['error'] = $_FILES['news_pdf_pdf']['error'][$index];
                $_FILES['news_pdf_pdf_multiple']['size'] = $_FILES['news_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('news_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'news_pdf_ref_id' => $news_id,
                        'news_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_news_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
        if (!empty($_FILES['news_file_doc']['name'][0])) {
            foreach ($_FILES['news_file_doc']['name'] as $index => $name) {
                $_FILES['news_file_doc_multiple']['name'] = $name;
                $_FILES['news_file_doc_multiple']['type'] = $_FILES['news_file_doc']['type'][$index];
                $_FILES['news_file_doc_multiple']['tmp_name'] = $_FILES['news_file_doc']['tmp_name'][$index];
                $_FILES['news_file_doc_multiple']['error'] = $_FILES['news_file_doc']['error'][$index];
                $_FILES['news_file_doc_multiple']['size'] = $_FILES['news_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('news_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'news_file_ref_id' => $news_id,
                        'news_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_news_file', $doc_data);
        }
        $this->space_model->update_server_current();

        // === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->news_name != $data['news_name']) {
                $changes['news_name'] = array(
                    'old' => $old_data->news_name,
                    'new' => $data['news_name']
                );
            }
            if ($old_data->news_detail != $data['news_detail']) {
                $changes['news_detail'] = array(
                    'old' => 'มีการเปลี่ยนแปลง',
                    'new' => 'รายละเอียดใหม่'
                );
            }
            if ($old_data->news_date != $data['news_date']) {
                $changes['news_date'] = array(
                    'old' => $old_data->news_date,
                    'new' => $data['news_date']
                );
            }
            if ($old_data->news_link != $data['news_link']) {
                $changes['news_link'] = array(
                    'old' => $old_data->news_link,
                    'new' => $data['news_link']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'news_img_img');
        }
        if (!empty($pdf_data)) {
            $files_info['pdfs_added'] = count($pdf_data);
            $files_info['pdf_names'] = array_column($pdf_data, 'news_pdf_pdf');
        }
        if (!empty($doc_data)) {
            $files_info['docs_added'] = count($doc_data);
            $files_info['doc_names'] = array_column($doc_data, 'news_file_doc');
        }

        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'ข่าวประชาสัมพันธ์',
            $data['news_name'],
            $news_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data) + count($pdf_data) + count($doc_data)
            )
        );
        // =======================================================================

        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_news($news_id)
    {
        $old_document = $this->db->get_where('tbl_news', array('news_id' => $news_id))->row();

        $old_file_path = './docs/img/' . $old_document->news_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_news', array('news_id' => $news_id));
        $this->space_model->update_server_current();
        // บันทึก log การลบ =================================================
        if ($old_document) {
            $this->log_model->add_log(
                'ลบ',
                'ข่าวประชาสัมพันธ์',
                $old_document->news_name,
                $news_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // =======================================================================
    }
}
