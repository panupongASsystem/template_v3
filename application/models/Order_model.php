<?php
class Order_model extends CI_Model
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

        // กำหนดค่าใน $order_data
        $order_data = array(
            'order_name' => $this->input->post('order_name'),
            'order_detail' => $this->input->post('order_detail'),
            'order_date' => $this->input->post('order_date'),
            'order_link' => $this->input->post('order_link'),
            'order_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('order_img');
        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            // ถ้ามีการอัปโหลดรูปภาพ
            $order_data['order_img'] = $this->img_upload->data('file_name');
        }
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_order', $order_data);
        $order_id = $this->db->insert_id();

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['order_img_img'])) {
            foreach ($_FILES['order_img_img']['name'] as $index => $name) {
                if (isset($_FILES['order_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['order_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['order_pdf_pdf'])) {
            foreach ($_FILES['order_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['order_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['order_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['order_file_doc'])) {
            foreach ($_FILES['order_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['order_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['order_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('order_backend/adding');
            return;
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['order_img_img']['name'][0])) {
            foreach ($_FILES['order_img_img']['name'] as $index => $name) {
                $_FILES['order_img_img_multiple']['name'] = $name;
                $_FILES['order_img_img_multiple']['type'] = $_FILES['order_img_img']['type'][$index];
                $_FILES['order_img_img_multiple']['tmp_name'] = $_FILES['order_img_img']['tmp_name'][$index];
                $_FILES['order_img_img_multiple']['error'] = $_FILES['order_img_img']['error'][$index];
                $_FILES['order_img_img_multiple']['size'] = $_FILES['order_img_img']['size'][$index];

                if ($this->img_upload->do_upload('order_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'order_img_ref_id' => $order_id,
                        'order_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_order_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์PDFเพิ่มเติมหรือไม่
        if (!empty($_FILES['order_pdf_pdf']['name'][0])) {
            foreach ($_FILES['order_pdf_pdf']['name'] as $index => $name) {
                $_FILES['order_pdf_pdf_multiple']['name'] = $name;
                $_FILES['order_pdf_pdf_multiple']['type'] = $_FILES['order_pdf_pdf']['type'][$index];
                $_FILES['order_pdf_pdf_multiple']['tmp_name'] = $_FILES['order_pdf_pdf']['tmp_name'][$index];
                $_FILES['order_pdf_pdf_multiple']['error'] = $_FILES['order_pdf_pdf']['error'][$index];
                $_FILES['order_pdf_pdf_multiple']['size'] = $_FILES['order_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('order_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'order_pdf_ref_id' => $order_id,
                        'order_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_order_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์Docเพิ่มเติมหรือไม่
        if (!empty($_FILES['order_file_doc']['name'][0])) {
            foreach ($_FILES['order_file_doc']['name'] as $index => $name) {
                $_FILES['order_file_doc_multiple']['name'] = $name;
                $_FILES['order_file_doc_multiple']['type'] = $_FILES['order_file_doc']['type'][$index];
                $_FILES['order_file_doc_multiple']['tmp_name'] = $_FILES['order_file_doc']['tmp_name'][$index];
                $_FILES['order_file_doc_multiple']['error'] = $_FILES['order_file_doc']['error'][$index];
                $_FILES['order_file_doc_multiple']['size'] = $_FILES['order_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('order_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'order_file_ref_id' => $order_id,
                        'order_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_order_file', $doc_data);
        }
        $this->space_model->update_server_current();
		// บันทึก log การเพิ่มข้อมูล =================================================
        $this->log_model->add_log(
            'เพิ่ม',
            'คำสั่ง',
            $order_data['order_name'],
            $order_id,
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

    public function list_all()
    {
        $this->db->select('*');
        $this->db->from('tbl_order');
        $this->db->group_by('tbl_order.order_id');
        $this->db->order_by('tbl_order.order_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_all_pdf($order_id)
    {
        $this->db->select('order_pdf_pdf');
        $this->db->from('tbl_order_pdf');
        $this->db->where('order_pdf_ref_id', $order_id);
        return $this->db->get()->result();
    }
    public function list_all_doc($order_id)
    {
        $this->db->select('order_file_doc');
        $this->db->from('tbl_order_file');
        $this->db->where('order_file_ref_id', $order_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read($order_id)
    {
        $this->db->where('order_id', $order_id);
        $query = $this->db->get('tbl_order');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($order_id)
    {
        $this->db->where('order_pdf_ref_id', $order_id);
        $this->db->order_by('order_pdf_id', 'DESC');
        $query = $this->db->get('tbl_order_pdf');
        $results = $query->result();

        // เรียงลำดับตามชื่อไฟล์ PDF
        usort($results, function ($a, $b) {
            return strnatcmp($a->order_pdf_pdf, $b->order_pdf_pdf);
        });

        return $results;
    }

    public function read_doc($order_id)
    {
        $this->db->where('order_file_ref_id', $order_id);
        $this->db->order_by('order_file_id', 'DESC');
        $query = $this->db->get('tbl_order_file');
        $results = $query->result();

        // เรียงลำดับตามชื่อไฟล์ DOC
        usort($results, function ($a, $b) {
            return strnatcmp($a->order_file_doc, $b->order_file_doc);
        });

        return $results;
    }


    public function read_img($order_id)
    {
        $this->db->where('order_img_ref_id', $order_id);
        $query = $this->db->get('tbl_order_img');
        $results = $query->result();
        usort($results, function ($a, $b) {
            return strnatcmp($a->order_img_img, $b->order_img_img);
        });
        return $results;
    }


    public function del_pdf($pdf_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
        $this->db->select('order_pdf_pdf, order_pdf_ref_id');
        $this->db->where('order_pdf_id', $pdf_id);
        $query = $this->db->get('tbl_order_pdf');
        $file_data = $query->row();
		
        // บันทึก log การลบไฟล์ PDF =============================================
        $old_data = $this->read($file_data->order_pdf_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->order_pdf_pdf;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('order_pdf_id', $pdf_id);
        $this->db->delete('tbl_order_pdf');
        $this->space_model->update_server_current();
		// บันทึก log การลบไฟล์ PDF =============================================
        $this->log_model->add_log(
            'ลบ',
            'คำสั่ง',
            'ไฟล์ PDF: ' . $file_data->order_pdf_pdf . ' จาก: ' . ($old_data ? $old_data->order_name : 'ไม่ระบุ'),
            $file_data->order_pdf_ref_id,
            array('file_type' => 'PDF', 'file_name' => $file_data->order_pdf_pdf)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_doc($doc_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $doc_id
        $this->db->select('order_file_doc, order_file_ref_id');
        $this->db->where('order_file_id', $doc_id);
        $query = $this->db->get('tbl_order_file');
        $file_data = $query->row();
		
		 // บันทึก log การลบไฟล์ DOC ========================================
        $old_data = $this->read($file_data->order_file_ref_id);
        // ================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->order_file_doc;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('order_file_id', $doc_id);
        $this->db->delete('tbl_order_file');
        $this->space_model->update_server_current();
		// บันทึก log การลบไฟล์ DOC ========================================
        $this->log_model->add_log(
            'ลบ',
            'คำสั่ง',
            'ไฟล์ DOC: ' . $file_data->order_file_doc . ' จาก: ' . ($old_data ? $old_data->order_name : 'ไม่ระบุ'),
            $file_data->order_file_ref_id,
            array('file_type' => 'DOC', 'file_name' => $file_data->order_file_doc)
        );
        // ================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('order_img_img, order_img_ref_id');
        $this->db->where('order_img_id', $file_id);
        $query = $this->db->get('tbl_order_img');
        $file_data = $query->row();

		// บันทึก log การลบรูปภาพ =================================================
        $old_data = $this->read($file_data->order_img_ref_id);
        // =======================================================================
		
        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->order_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('order_img_id', $file_id);
        $this->db->delete('tbl_order_img');
        $this->space_model->update_server_current();
		// บันทึก log การลบรูปภาพ =================================================
        $this->log_model->add_log(
            'ลบ',
            'คำสั่ง',
            'รูปภาพ: ' . $file_data->order_img_img . ' จาก: ' . ($old_data ? $old_data->order_name : 'ไม่ระบุ'),
            $file_data->order_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->order_img_img)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }


    public function edit($order_id)
    {
		// ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($order_id);
		
        // Update order information
        $data = array(
            'order_name' => $this->input->post('order_name'),
            'order_detail' => $this->input->post('order_detail'),
            'order_date' => $this->input->post('order_date'),
            'order_link' => $this->input->post('order_link'),
            'order_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('order_id', $order_id);
        $this->db->update('tbl_order', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['order_img_img'])) {
            foreach ($_FILES['order_img_img']['name'] as $index => $name) {
                if (isset($_FILES['order_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['order_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['order_pdf_pdf'])) {
            foreach ($_FILES['order_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['order_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['order_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['order_file_doc'])) {
            foreach ($_FILES['order_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['order_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['order_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('order_backend/adding');
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
        $img_main = $this->img_upload->do_upload('order_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_order', array('order_id' => $order_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->order_img) {
                $old_file_path = './docs/img/' . $old_document->order_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['order_img'] = $this->img_upload->data('file_name');
            $this->db->where('order_id', $order_id);
            $this->db->update('tbl_order', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['order_img_img']['name'][0])) {

            foreach ($_FILES['order_img_img']['name'] as $index => $name) {
                $_FILES['order_img_img_multiple']['name'] = $name;
                $_FILES['order_img_img_multiple']['type'] = $_FILES['order_img_img']['type'][$index];
                $_FILES['order_img_img_multiple']['tmp_name'] = $_FILES['order_img_img']['tmp_name'][$index];
                $_FILES['order_img_img_multiple']['error'] = $_FILES['order_img_img']['error'][$index];
                $_FILES['order_img_img_multiple']['size'] = $_FILES['order_img_img']['size'][$index];

                if ($this->img_upload->do_upload('order_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'order_img_ref_id' => $order_id,
                        'order_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_order_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['order_pdf_pdf']['name'][0])) {
            foreach ($_FILES['order_pdf_pdf']['name'] as $index => $name) {
                $_FILES['order_pdf_pdf_multiple']['name'] = $name;
                $_FILES['order_pdf_pdf_multiple']['type'] = $_FILES['order_pdf_pdf']['type'][$index];
                $_FILES['order_pdf_pdf_multiple']['tmp_name'] = $_FILES['order_pdf_pdf']['tmp_name'][$index];
                $_FILES['order_pdf_pdf_multiple']['error'] = $_FILES['order_pdf_pdf']['error'][$index];
                $_FILES['order_pdf_pdf_multiple']['size'] = $_FILES['order_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('order_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'order_pdf_ref_id' => $order_id,
                        'order_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_order_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
        if (!empty($_FILES['order_file_doc']['name'][0])) {
            foreach ($_FILES['order_file_doc']['name'] as $index => $name) {
                $_FILES['order_file_doc_multiple']['name'] = $name;
                $_FILES['order_file_doc_multiple']['type'] = $_FILES['order_file_doc']['type'][$index];
                $_FILES['order_file_doc_multiple']['tmp_name'] = $_FILES['order_file_doc']['tmp_name'][$index];
                $_FILES['order_file_doc_multiple']['error'] = $_FILES['order_file_doc']['error'][$index];
                $_FILES['order_file_doc_multiple']['size'] = $_FILES['order_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('order_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'order_file_ref_id' => $order_id,
                        'order_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_order_file', $doc_data);
        }
        $this->space_model->update_server_current();
		 // === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->order_name != $data['order_name']) {
                $changes['order_name'] = array(
                    'old' => $old_data->order_name,
                    'new' => $data['order_name']
                );
            }
            if ($old_data->order_detail != $data['order_detail']) {
                $changes['order_detail'] = array(
                    'old' => 'มีการเปลี่ยนแปลง',
                    'new' => 'รายละเอียดใหม่'
                );
            }
            if ($old_data->order_date != $data['order_date']) {
                $changes['order_date'] = array(
                    'old' => $old_data->order_date,
                    'new' => $data['order_date']
                );
            }
            if ($old_data->order_link != $data['order_link']) {
                $changes['order_link'] = array(
                    'old' => $old_data->order_link,
                    'new' => $data['order_link']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'order_img_img');
        }
        if (!empty($pdf_data)) {
            $files_info['pdfs_added'] = count($pdf_data);
            $files_info['pdf_names'] = array_column($pdf_data, 'order_pdf_pdf');
        }
        if (!empty($doc_data)) {
            $files_info['docs_added'] = count($doc_data);
            $files_info['doc_names'] = array_column($doc_data, 'order_file_doc');
        }

        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'คำสั่ง',
            $data['order_name'],
            $order_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data) + count($pdf_data) + count($doc_data)
            )
        );
        // =======================================================================
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_order($order_id)
    {
        $old_document = $this->db->get_where('tbl_order', array('order_id' => $order_id))->row();

        $old_file_path = './docs/img/' . $old_document->order_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_order', array('order_id' => $order_id));
        $this->space_model->update_server_current();
		// บันทึก log การลบ =================================================
        if ($old_document) {
            $this->log_model->add_log(
                'ลบ',
                'คำสั่ง',
                $old_document->order_name,
                $order_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // =======================================================================
    }

    public function del_order_pdf($order_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_order_pdf', array('order_pdf_ref_id' => $order_id))->result();

        // ลบ pdf จากตาราง tbl_order_pdf
        $this->db->where('order_pdf_ref_id', $order_id);
        $this->db->delete('tbl_order_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->order_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_order_doc($order_id)
    {
        // ดึงข้อมูลรายการ doc ก่อน
        $files = $this->db->get_where('tbl_order_file', array('order_file_ref_id' => $order_id))->result();

        // ลบ doc จากตาราง tbl_order_file
        $this->db->where('order_file_ref_id', $order_id);
        $this->db->delete('tbl_order_file');

        // ลบไฟล์ doc ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->order_file_doc;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_order_img($order_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_order_img', array('order_img_ref_id' => $order_id))->result();

        // ลบรูปภาพจากตาราง tbl_order_file
        $this->db->where('order_img_ref_id', $order_id);
        $this->db->delete('tbl_order_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->order_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function update_order_status()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $orderId = $this->input->post('order_id'); // รับค่า order_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_order ในฐานข้อมูลของคุณ
            $data = array(
                'order_status' => $newStatus
            );
            $this->db->where('order_id', $orderId); // ระบุ order_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_order', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function order_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_order');
        $this->db->where('tbl_order.order_status', 'show');
        $this->db->limit(9);
        $this->db->order_by('tbl_order.order_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function order_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_order');
        $this->db->where('tbl_order.order_status', 'show');
        $this->db->order_by('tbl_order.order_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view($order_id)
    {
        $this->db->where('order_id', $order_id);
        $this->db->set('order_view', 'order_view + 1', false); // บวกค่า order_view ทีละ 1
        $this->db->update('tbl_order');
    }
    // ใน order_model
    public function increment_download_order($order_file_id)
    {
        $this->db->where('order_file_id', $order_file_id);
        $this->db->set('order_file_download', 'order_file_download + 1', false); // บวกค่า order_download ทีละ 1
        $this->db->update('tbl_order_file');
    }
}
