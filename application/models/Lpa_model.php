<?php
class Lpa_model extends CI_Model
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

        // กำหนดค่าใน $lpa_data
        $lpa_data = array(
            'lpa_name' => $this->input->post('lpa_name'),
            'lpa_detail' => $this->input->post('lpa_detail'),
            'lpa_date' => $this->input->post('lpa_date'),
            'lpa_link' => $this->input->post('lpa_link'),
            'lpa_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('lpa_img');
        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            // ถ้ามีการอัปโหลดรูปภาพ
            $lpa_data['lpa_img'] = $this->img_upload->data('file_name');
        }
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_lpa', $lpa_data);
        $lpa_id = $this->db->insert_id();

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['lpa_img_img'])) {
            foreach ($_FILES['lpa_img_img']['name'] as $index => $name) {
                if (isset($_FILES['lpa_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['lpa_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['lpa_pdf_pdf'])) {
            foreach ($_FILES['lpa_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['lpa_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['lpa_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['lpa_file_doc'])) {
            foreach ($_FILES['lpa_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['lpa_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['lpa_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('lpa_backend/adding');
            return;
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['lpa_img_img']['name'][0])) {
            foreach ($_FILES['lpa_img_img']['name'] as $index => $name) {
                $_FILES['lpa_img_img_multiple']['name'] = $name;
                $_FILES['lpa_img_img_multiple']['type'] = $_FILES['lpa_img_img']['type'][$index];
                $_FILES['lpa_img_img_multiple']['tmp_name'] = $_FILES['lpa_img_img']['tmp_name'][$index];
                $_FILES['lpa_img_img_multiple']['error'] = $_FILES['lpa_img_img']['error'][$index];
                $_FILES['lpa_img_img_multiple']['size'] = $_FILES['lpa_img_img']['size'][$index];

                if ($this->img_upload->do_upload('lpa_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'lpa_img_ref_id' => $lpa_id,
                        'lpa_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_lpa_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์PDFเพิ่มเติมหรือไม่
        if (!empty($_FILES['lpa_pdf_pdf']['name'][0])) {
            foreach ($_FILES['lpa_pdf_pdf']['name'] as $index => $name) {
                $_FILES['lpa_pdf_pdf_multiple']['name'] = $name;
                $_FILES['lpa_pdf_pdf_multiple']['type'] = $_FILES['lpa_pdf_pdf']['type'][$index];
                $_FILES['lpa_pdf_pdf_multiple']['tmp_name'] = $_FILES['lpa_pdf_pdf']['tmp_name'][$index];
                $_FILES['lpa_pdf_pdf_multiple']['error'] = $_FILES['lpa_pdf_pdf']['error'][$index];
                $_FILES['lpa_pdf_pdf_multiple']['size'] = $_FILES['lpa_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('lpa_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'lpa_pdf_ref_id' => $lpa_id,
                        'lpa_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_lpa_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์Docเพิ่มเติมหรือไม่
        if (!empty($_FILES['lpa_file_doc']['name'][0])) {
            foreach ($_FILES['lpa_file_doc']['name'] as $index => $name) {
                $_FILES['lpa_file_doc_multiple']['name'] = $name;
                $_FILES['lpa_file_doc_multiple']['type'] = $_FILES['lpa_file_doc']['type'][$index];
                $_FILES['lpa_file_doc_multiple']['tmp_name'] = $_FILES['lpa_file_doc']['tmp_name'][$index];
                $_FILES['lpa_file_doc_multiple']['error'] = $_FILES['lpa_file_doc']['error'][$index];
                $_FILES['lpa_file_doc_multiple']['size'] = $_FILES['lpa_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('lpa_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'lpa_file_ref_id' => $lpa_id,
                        'lpa_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_lpa_file', $doc_data);
        }
        $this->space_model->update_server_current();
		// บันทึก log การเพิ่มข้อมูล =================================================
        $this->log_model->add_log(
            'เพิ่ม',
            'LPA การประเมินประสิทธิภาพขององค์กร',
            $lpa_data['lpa_name'],
            $lpa_id,
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
        $this->db->from('tbl_lpa');
        $this->db->group_by('tbl_lpa.lpa_id');
        $this->db->order_by('tbl_lpa.lpa_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_all_pdf($lpa_id)
    {
        $this->db->select('lpa_pdf_pdf');
        $this->db->from('tbl_lpa_pdf');
        $this->db->where('lpa_pdf_ref_id', $lpa_id);
        return $this->db->get()->result();
    }
    public function list_all_doc($lpa_id)
    {
        $this->db->select('lpa_file_doc');
        $this->db->from('tbl_lpa_file');
        $this->db->where('lpa_file_ref_id', $lpa_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read($lpa_id)
    {
        $this->db->where('lpa_id', $lpa_id);
        $query = $this->db->get('tbl_lpa');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($lpa_id)
    {
        $this->db->where('lpa_pdf_ref_id', $lpa_id);
        $this->db->order_by('lpa_pdf_id', 'DESC');
        $query = $this->db->get('tbl_lpa_pdf');
        $results = $query->result();

        // เรียงลำดับตามชื่อไฟล์ PDF
        usort($results, function ($a, $b) {
            return strnatcmp($a->lpa_pdf_pdf, $b->lpa_pdf_pdf);
        });

        return $results;
    }

    public function read_doc($lpa_id)
    {
        $this->db->where('lpa_file_ref_id', $lpa_id);
        $this->db->order_by('lpa_file_id', 'DESC');
        $query = $this->db->get('tbl_lpa_file');
        $results = $query->result();

        // เรียงลำดับตามชื่อไฟล์ DOC
        usort($results, function ($a, $b) {
            return strnatcmp($a->lpa_file_doc, $b->lpa_file_doc);
        });

        return $results;
    }

    public function read_img($lpa_img_id)
    {
        $this->db->where('lpa_img_ref_id', $lpa_img_id);
        $query = $this->db->get('tbl_lpa_img');
        $results = $query->result();
        usort($results, function ($a, $b) {
            return strnatcmp($a->lpa_img_img, $b->lpa_img_img);
        });
        return $results;
    }


    public function del_pdf($pdf_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
        $this->db->select('lpa_pdf_pdf, lpa_pdf_ref_id');
        $this->db->where('lpa_pdf_id', $pdf_id);
        $query = $this->db->get('tbl_lpa_pdf');
        $file_data = $query->row();
		
		// บันทึก log การลบไฟล์ PDF =============================================
        $old_data = $this->read($file_data->lpa_pdf_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->lpa_pdf_pdf;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('lpa_pdf_id', $pdf_id);
        $this->db->delete('tbl_lpa_pdf');
        $this->space_model->update_server_current();
		// บันทึก log การลบไฟล์ PDF =============================================
        $this->log_model->add_log(
            'ลบ',
            'LPA การประเมินประสิทธิภาพขององค์กร',
            'ไฟล์ PDF: ' . $file_data->lpa_pdf_pdf . ' จาก: ' . ($old_data ? $old_data->lpa_name : 'ไม่ระบุ'),
            $file_data->lpa_pdf_ref_id,
            array('file_type' => 'PDF', 'file_name' => $file_data->lpa_pdf_pdf)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_doc($doc_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $doc_id
        $this->db->select('lpa_file_doc, lpa_file_ref_id');
        $this->db->where('lpa_file_id', $doc_id);
        $query = $this->db->get('tbl_lpa_file');
        $file_data = $query->row();
		
		 // บันทึก log การลบไฟล์ DOC ========================================
        $old_data = $this->read($file_data->lpa_file_ref_id);
        // ================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->lpa_file_doc;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('lpa_file_id', $doc_id);
        $this->db->delete('tbl_lpa_file');
        $this->space_model->update_server_current();
		// บันทึก log การลบไฟล์ DOC ========================================
        $this->log_model->add_log(
            'ลบ',
            'LPA การประเมินประสิทธิภาพขององค์กร',
            'ไฟล์ DOC: ' . $file_data->lpa_file_doc . ' จาก: ' . ($old_data ? $old_data->lpa_name : 'ไม่ระบุ'),
            $file_data->lpa_file_ref_id,
            array('file_type' => 'DOC', 'file_name' => $file_data->lpa_file_doc)
        );
        // ================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('lpa_img_img, lpa_img_ref_id');
        $this->db->where('lpa_img_id', $file_id);
        $query = $this->db->get('tbl_lpa_img');
        $file_data = $query->row();
		
		// บันทึก log การลบรูปภาพ =================================================
        $old_data = $this->read($file_data->lpa_img_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->lpa_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('lpa_img_id', $file_id);
        $this->db->delete('tbl_lpa_img');
        $this->space_model->update_server_current();
		// บันทึก log การลบรูปภาพ =================================================
        $this->log_model->add_log(
            'ลบ',
            'LPA การประเมินประสิทธิภาพขององค์กร',
            'รูปภาพ: ' . $file_data->lpa_img_img . ' จาก: ' . ($old_data ? $old_data->lpa_name : 'ไม่ระบุ'),
            $file_data->lpa_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->lpa_img_img)
        );
        // =======================================================================

        $this->session->set_flashdata('del_success', TRUE);
    }


    public function edit($lpa_id)
    {
		// ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($lpa_id);
		
        // Update lpa information
        $data = array(
            'lpa_name' => $this->input->post('lpa_name'),
            'lpa_detail' => $this->input->post('lpa_detail'),
            'lpa_date' => $this->input->post('lpa_date'),
            'lpa_link' => $this->input->post('lpa_link'),
            'lpa_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('lpa_id', $lpa_id);
        $this->db->update('tbl_lpa', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['lpa_img_img'])) {
            foreach ($_FILES['lpa_img_img']['name'] as $index => $name) {
                if (isset($_FILES['lpa_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['lpa_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['lpa_pdf_pdf'])) {
            foreach ($_FILES['lpa_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['lpa_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['lpa_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['lpa_file_doc'])) {
            foreach ($_FILES['lpa_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['lpa_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['lpa_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('lpa_backend/adding');
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
        $img_main = $this->img_upload->do_upload('lpa_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_lpa', array('lpa_id' => $lpa_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->lpa_img) {
                $old_file_path = './docs/img/' . $old_document->lpa_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['lpa_img'] = $this->img_upload->data('file_name');
            $this->db->where('lpa_id', $lpa_id);
            $this->db->update('tbl_lpa', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['lpa_img_img']['name'][0])) {

            foreach ($_FILES['lpa_img_img']['name'] as $index => $name) {
                $_FILES['lpa_img_img_multiple']['name'] = $name;
                $_FILES['lpa_img_img_multiple']['type'] = $_FILES['lpa_img_img']['type'][$index];
                $_FILES['lpa_img_img_multiple']['tmp_name'] = $_FILES['lpa_img_img']['tmp_name'][$index];
                $_FILES['lpa_img_img_multiple']['error'] = $_FILES['lpa_img_img']['error'][$index];
                $_FILES['lpa_img_img_multiple']['size'] = $_FILES['lpa_img_img']['size'][$index];

                if ($this->img_upload->do_upload('lpa_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'lpa_img_ref_id' => $lpa_id,
                        'lpa_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_lpa_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['lpa_pdf_pdf']['name'][0])) {
            foreach ($_FILES['lpa_pdf_pdf']['name'] as $index => $name) {
                $_FILES['lpa_pdf_pdf_multiple']['name'] = $name;
                $_FILES['lpa_pdf_pdf_multiple']['type'] = $_FILES['lpa_pdf_pdf']['type'][$index];
                $_FILES['lpa_pdf_pdf_multiple']['tmp_name'] = $_FILES['lpa_pdf_pdf']['tmp_name'][$index];
                $_FILES['lpa_pdf_pdf_multiple']['error'] = $_FILES['lpa_pdf_pdf']['error'][$index];
                $_FILES['lpa_pdf_pdf_multiple']['size'] = $_FILES['lpa_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('lpa_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'lpa_pdf_ref_id' => $lpa_id,
                        'lpa_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_lpa_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
        if (!empty($_FILES['lpa_file_doc']['name'][0])) {
            foreach ($_FILES['lpa_file_doc']['name'] as $index => $name) {
                $_FILES['lpa_file_doc_multiple']['name'] = $name;
                $_FILES['lpa_file_doc_multiple']['type'] = $_FILES['lpa_file_doc']['type'][$index];
                $_FILES['lpa_file_doc_multiple']['tmp_name'] = $_FILES['lpa_file_doc']['tmp_name'][$index];
                $_FILES['lpa_file_doc_multiple']['error'] = $_FILES['lpa_file_doc']['error'][$index];
                $_FILES['lpa_file_doc_multiple']['size'] = $_FILES['lpa_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('lpa_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'lpa_file_ref_id' => $lpa_id,
                        'lpa_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_lpa_file', $doc_data);
        }
        $this->space_model->update_server_current();
		// === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->lpa_name != $data['lpa_name']) {
                $changes['lpa_name'] = array(
                    'old' => $old_data->lpa_name,
                    'new' => $data['lpa_name']
                );
            }
            if ($old_data->lpa_detail != $data['lpa_detail']) {
                $changes['lpa_detail'] = array(
                    'old' => 'มีการเปลี่ยนแปลง',
                    'new' => 'รายละเอียดใหม่'
                );
            }
            if ($old_data->lpa_date != $data['lpa_date']) {
                $changes['lpa_date'] = array(
                    'old' => $old_data->lpa_date,
                    'new' => $data['lpa_date']
                );
            }
            if ($old_data->lpa_link != $data['lpa_link']) {
                $changes['lpa_link'] = array(
                    'old' => $old_data->lpa_link,
                    'new' => $data['lpa_link']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'lpa_img_img');
        }
        if (!empty($pdf_data)) {
            $files_info['pdfs_added'] = count($pdf_data);
            $files_info['pdf_names'] = array_column($pdf_data, 'lpa_pdf_pdf');
        }
        if (!empty($doc_data)) {
            $files_info['docs_added'] = count($doc_data);
            $files_info['doc_names'] = array_column($doc_data, 'lpa_file_doc');
        }

        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'LPA การประเมินประสิทธิภาพขององค์กร',
            $data['lpa_name'],
            $lpa_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data) + count($pdf_data) + count($doc_data)
            )
        );
        // =======================================================================
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_lpa($lpa_id)
    {
        $old_document = $this->db->get_where('tbl_lpa', array('lpa_id' => $lpa_id))->row();

        $old_file_path = './docs/img/' . $old_document->lpa_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_lpa', array('lpa_id' => $lpa_id));
        $this->space_model->update_server_current();
		// บันทึก log การลบ =================================================
        if ($old_document) {
            $this->log_model->add_log(
                'ลบ',
                'LPA การประเมินประสิทธิภาพขององค์กร',
                $old_document->lpa_name,
                $lpa_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // =======================================================================
    }

    public function del_lpa_pdf($lpa_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_lpa_pdf', array('lpa_pdf_ref_id' => $lpa_id))->result();

        // ลบ pdf จากตาราง tbl_lpa_pdf
        $this->db->where('lpa_pdf_ref_id', $lpa_id);
        $this->db->delete('tbl_lpa_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->lpa_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_lpa_doc($lpa_id)
    {
        // ดึงข้อมูลรายการ doc ก่อน
        $files = $this->db->get_where('tbl_lpa_file', array('lpa_file_ref_id' => $lpa_id))->result();

        // ลบ doc จากตาราง tbl_lpa_file
        $this->db->where('lpa_file_ref_id', $lpa_id);
        $this->db->delete('tbl_lpa_file');

        // ลบไฟล์ doc ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->lpa_file_doc;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_lpa_img($lpa_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_lpa_img', array('lpa_img_ref_id' => $lpa_id))->result();

        // ลบรูปภาพจากตาราง tbl_lpa_file
        $this->db->where('lpa_img_ref_id', $lpa_id);
        $this->db->delete('tbl_lpa_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->lpa_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function update_lpa_status()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $lpaId = $this->input->post('lpa_id'); // รับค่า lpa_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_lpa ในฐานข้อมูลของคุณ
            $data = array(
                'lpa_status' => $newStatus
            );
            $this->db->where('lpa_id', $lpaId); // ระบุ lpa_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_lpa', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function lpa_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_lpa');
        $this->db->where('tbl_lpa.lpa_status', 'show');
        $this->db->limit(8);
        $this->db->order_by('tbl_lpa.lpa_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function lpa_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_lpa');
        $this->db->where('tbl_lpa.lpa_status', 'show');
        $this->db->order_by('tbl_lpa.lpa_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view($lpa_id)
    {
        $this->db->where('lpa_id', $lpa_id);
        $this->db->set('lpa_view', 'lpa_view + 1', false); // บวกค่า lpa_view ทีละ 1
        $this->db->update('tbl_lpa');
    }
    // ใน lpa_model
    public function increment_download_lpa($lpa_file_id)
    {
        $this->db->where('lpa_file_id', $lpa_file_id);
        $this->db->set('lpa_file_download', 'lpa_file_download + 1', false); // บวกค่า lpa_download ทีละ 1
        $this->db->update('tbl_lpa_file');
    }
}
