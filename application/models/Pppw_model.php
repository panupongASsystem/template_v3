<?php
class Pppw_model extends CI_Model
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

        // กำหนดค่าใน $pppw_data
        $pppw_data = array(
            'pppw_name' => $this->input->post('pppw_name'),
            'pppw_detail' => $this->input->post('pppw_detail'),
            'pppw_date' => $this->input->post('pppw_date'),
            'pppw_link' => $this->input->post('pppw_link'),
            'pppw_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('pppw_img');
        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            // ถ้ามีการอัปโหลดรูปภาพ
            $pppw_data['pppw_img'] = $this->img_upload->data('file_name');
        }
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_pppw', $pppw_data);
        $pppw_id = $this->db->insert_id();

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['pppw_img_img'])) {
            foreach ($_FILES['pppw_img_img']['name'] as $index => $name) {
                if (isset($_FILES['pppw_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['pppw_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['pppw_pdf_pdf'])) {
            foreach ($_FILES['pppw_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['pppw_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['pppw_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['pppw_file_doc'])) {
            foreach ($_FILES['pppw_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['pppw_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['pppw_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('pppw_backend/adding');
            return;
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['pppw_img_img']['name'][0])) {
            foreach ($_FILES['pppw_img_img']['name'] as $index => $name) {
                $_FILES['pppw_img_img_multiple']['name'] = $name;
                $_FILES['pppw_img_img_multiple']['type'] = $_FILES['pppw_img_img']['type'][$index];
                $_FILES['pppw_img_img_multiple']['tmp_name'] = $_FILES['pppw_img_img']['tmp_name'][$index];
                $_FILES['pppw_img_img_multiple']['error'] = $_FILES['pppw_img_img']['error'][$index];
                $_FILES['pppw_img_img_multiple']['size'] = $_FILES['pppw_img_img']['size'][$index];

                if ($this->img_upload->do_upload('pppw_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'pppw_img_ref_id' => $pppw_id,
                        'pppw_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_pppw_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์PDFเพิ่มเติมหรือไม่
        if (!empty($_FILES['pppw_pdf_pdf']['name'][0])) {
            foreach ($_FILES['pppw_pdf_pdf']['name'] as $index => $name) {
                $_FILES['pppw_pdf_pdf_multiple']['name'] = $name;
                $_FILES['pppw_pdf_pdf_multiple']['type'] = $_FILES['pppw_pdf_pdf']['type'][$index];
                $_FILES['pppw_pdf_pdf_multiple']['tmp_name'] = $_FILES['pppw_pdf_pdf']['tmp_name'][$index];
                $_FILES['pppw_pdf_pdf_multiple']['error'] = $_FILES['pppw_pdf_pdf']['error'][$index];
                $_FILES['pppw_pdf_pdf_multiple']['size'] = $_FILES['pppw_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('pppw_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'pppw_pdf_ref_id' => $pppw_id,
                        'pppw_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_pppw_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์Docเพิ่มเติมหรือไม่
        if (!empty($_FILES['pppw_file_doc']['name'][0])) {
            foreach ($_FILES['pppw_file_doc']['name'] as $index => $name) {
                $_FILES['pppw_file_doc_multiple']['name'] = $name;
                $_FILES['pppw_file_doc_multiple']['type'] = $_FILES['pppw_file_doc']['type'][$index];
                $_FILES['pppw_file_doc_multiple']['tmp_name'] = $_FILES['pppw_file_doc']['tmp_name'][$index];
                $_FILES['pppw_file_doc_multiple']['error'] = $_FILES['pppw_file_doc']['error'][$index];
                $_FILES['pppw_file_doc_multiple']['size'] = $_FILES['pppw_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('pppw_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'pppw_file_ref_id' => $pppw_id,
                        'pppw_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_pppw_file', $doc_data);
        }
        $this->space_model->update_server_current();
		// บันทึก log การเพิ่มข้อมูล =================================================
        $this->log_model->add_log(
            'เพิ่ม',
            'พรบ./พรก ที่ใช้การปฏิบัติงาน',
            $pppw_data['pppw_name'],
            $pppw_id,
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
        $this->db->from('tbl_pppw');
        $this->db->group_by('tbl_pppw.pppw_id');
        $this->db->order_by('tbl_pppw.pppw_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_all_pdf($pppw_id)
    {
        $this->db->select('pppw_pdf_pdf');
        $this->db->from('tbl_pppw_pdf');
        $this->db->where('pppw_pdf_ref_id', $pppw_id);
        return $this->db->get()->result();
    }
    public function list_all_doc($pppw_id)
    {
        $this->db->select('pppw_file_doc');
        $this->db->from('tbl_pppw_file');
        $this->db->where('pppw_file_ref_id', $pppw_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read($pppw_id)
    {
        $this->db->where('pppw_id', $pppw_id);
        $query = $this->db->get('tbl_pppw');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($pppw_id)
    {
        $this->db->where('pppw_pdf_ref_id', $pppw_id);
        $this->db->order_by('pppw_pdf_id', 'DESC');
        $query = $this->db->get('tbl_pppw_pdf');
        $results = $query->result();

        // Sort by PDF file names
        usort($results, function ($a, $b) {
            return strnatcmp($a->pppw_pdf_pdf, $b->pppw_pdf_pdf);
        });

        return $results;
    }

    public function read_doc($pppw_id)
    {
        $this->db->where('pppw_file_ref_id', $pppw_id);
        $this->db->order_by('pppw_file_id', 'DESC');
        $query = $this->db->get('tbl_pppw_file');
        $results = $query->result();

        // Sort by DOC file names
        usort($results, function ($a, $b) {
            return strnatcmp($a->pppw_file_doc, $b->pppw_file_doc);
        });

        return $results;
    }


    public function read_img($pppw_id)
    {
        $this->db->where('pppw_img_ref_id', $pppw_id);
        $query = $this->db->get('tbl_pppw_img');
        $results = $query->result();
        usort($results, function ($a, $b) {
            return strnatcmp($a->pppw_img_img, $b->pppw_img_img);
        });
        return $results;
    }

    public function del_pdf($pdf_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
        $this->db->select('pppw_pdf_pdf, pppw_pdf_ref_id');
        $this->db->where('pppw_pdf_id', $pdf_id);
        $query = $this->db->get('tbl_pppw_pdf');
        $file_data = $query->row();
		
		 // บันทึก log การลบไฟล์ PDF =============================================
        $old_data = $this->read($file_data->pppw_pdf_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->pppw_pdf_pdf;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('pppw_pdf_id', $pdf_id);
        $this->db->delete('tbl_pppw_pdf');
        $this->space_model->update_server_current();
		 // บันทึก log การลบไฟล์ PDF =============================================
        $this->log_model->add_log(
            'ลบ',
            'พรบ./พรก ที่ใช้การปฏิบัติงาน',
            'ไฟล์ PDF: ' . $file_data->pppw_pdf_pdf . ' จาก: ' . ($old_data ? $old_data->pppw_name : 'ไม่ระบุ'),
            $file_data->pppw_pdf_ref_id,
            array('file_type' => 'PDF', 'file_name' => $file_data->pppw_pdf_pdf)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_doc($doc_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $doc_id
        $this->db->select('pppw_file_doc, pppw_file_ref_id');
        $this->db->where('pppw_file_id', $doc_id);
        $query = $this->db->get('tbl_pppw_file');
        $file_data = $query->row();
		
		// บันทึก log การลบไฟล์ DOC ========================================
        $old_data = $this->read($file_data->pppw_file_ref_id);
        // ================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->pppw_file_doc;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('pppw_file_id', $doc_id);
        $this->db->delete('tbl_pppw_file');
        $this->space_model->update_server_current();
		 // บันทึก log การลบไฟล์ DOC ========================================
        $this->log_model->add_log(
            'ลบ',
            'พรบ./พรก ที่ใช้การปฏิบัติงาน',
            'ไฟล์ DOC: ' . $file_data->pppw_file_doc . ' จาก: ' . ($old_data ? $old_data->pppw_name : 'ไม่ระบุ'),
            $file_data->pppw_file_ref_id,
            array('file_type' => 'DOC', 'file_name' => $file_data->pppw_file_doc)
        );
        // ================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('pppw_img_img, pppw_img_ref_id');
        $this->db->where('pppw_img_id', $file_id);
        $query = $this->db->get('tbl_pppw_img');
        $file_data = $query->row();
		
		// บันทึก log การลบรูปภาพ =================================================
        $old_data = $this->read($file_data->pppw_img_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->pppw_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('pppw_img_id', $file_id);
        $this->db->delete('tbl_pppw_img');
        $this->space_model->update_server_current();
		// บันทึก log การลบรูปภาพ =================================================
        $this->log_model->add_log(
            'ลบ',
            'พรบ./พรก ที่ใช้การปฏิบัติงาน',
            'รูปภาพ: ' . $file_data->pppw_img_img . ' จาก: ' . ($old_data ? $old_data->pppw_name : 'ไม่ระบุ'),
            $file_data->pppw_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->pppw_img_img)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }


    public function edit($pppw_id)
    {
		// ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($pppw_id);
        // Update pppw information
        $data = array(
            'pppw_name' => $this->input->post('pppw_name'),
            'pppw_detail' => $this->input->post('pppw_detail'),
            'pppw_date' => $this->input->post('pppw_date'),
            'pppw_link' => $this->input->post('pppw_link'),
            'pppw_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('pppw_id', $pppw_id);
        $this->db->update('tbl_pppw', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['pppw_img_img'])) {
            foreach ($_FILES['pppw_img_img']['name'] as $index => $name) {
                if (isset($_FILES['pppw_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['pppw_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['pppw_pdf_pdf'])) {
            foreach ($_FILES['pppw_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['pppw_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['pppw_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['pppw_file_doc'])) {
            foreach ($_FILES['pppw_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['pppw_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['pppw_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('pppw_backend/adding');
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
        $img_main = $this->img_upload->do_upload('pppw_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_pppw', array('pppw_id' => $pppw_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->pppw_img) {
                $old_file_path = './docs/img/' . $old_document->pppw_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['pppw_img'] = $this->img_upload->data('file_name');
            $this->db->where('pppw_id', $pppw_id);
            $this->db->update('tbl_pppw', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['pppw_img_img']['name'][0])) {

            foreach ($_FILES['pppw_img_img']['name'] as $index => $name) {
                $_FILES['pppw_img_img_multiple']['name'] = $name;
                $_FILES['pppw_img_img_multiple']['type'] = $_FILES['pppw_img_img']['type'][$index];
                $_FILES['pppw_img_img_multiple']['tmp_name'] = $_FILES['pppw_img_img']['tmp_name'][$index];
                $_FILES['pppw_img_img_multiple']['error'] = $_FILES['pppw_img_img']['error'][$index];
                $_FILES['pppw_img_img_multiple']['size'] = $_FILES['pppw_img_img']['size'][$index];

                if ($this->img_upload->do_upload('pppw_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'pppw_img_ref_id' => $pppw_id,
                        'pppw_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_pppw_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['pppw_pdf_pdf']['name'][0])) {
            foreach ($_FILES['pppw_pdf_pdf']['name'] as $index => $name) {
                $_FILES['pppw_pdf_pdf_multiple']['name'] = $name;
                $_FILES['pppw_pdf_pdf_multiple']['type'] = $_FILES['pppw_pdf_pdf']['type'][$index];
                $_FILES['pppw_pdf_pdf_multiple']['tmp_name'] = $_FILES['pppw_pdf_pdf']['tmp_name'][$index];
                $_FILES['pppw_pdf_pdf_multiple']['error'] = $_FILES['pppw_pdf_pdf']['error'][$index];
                $_FILES['pppw_pdf_pdf_multiple']['size'] = $_FILES['pppw_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('pppw_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'pppw_pdf_ref_id' => $pppw_id,
                        'pppw_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_pppw_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
        if (!empty($_FILES['pppw_file_doc']['name'][0])) {
            foreach ($_FILES['pppw_file_doc']['name'] as $index => $name) {
                $_FILES['pppw_file_doc_multiple']['name'] = $name;
                $_FILES['pppw_file_doc_multiple']['type'] = $_FILES['pppw_file_doc']['type'][$index];
                $_FILES['pppw_file_doc_multiple']['tmp_name'] = $_FILES['pppw_file_doc']['tmp_name'][$index];
                $_FILES['pppw_file_doc_multiple']['error'] = $_FILES['pppw_file_doc']['error'][$index];
                $_FILES['pppw_file_doc_multiple']['size'] = $_FILES['pppw_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('pppw_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'pppw_file_ref_id' => $pppw_id,
                        'pppw_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_pppw_file', $doc_data);
        }
        $this->space_model->update_server_current();
		// === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->pppw_name != $data['pppw_name']) {
                $changes['pppw_name'] = array(
                    'old' => $old_data->pppw_name,
                    'new' => $data['pppw_name']
                );
            }
            if ($old_data->pppw_detail != $data['pppw_detail']) {
                $changes['pppw_detail'] = array(
                    'old' => 'มีการเปลี่ยนแปลง',
                    'new' => 'รายละเอียดใหม่'
                );
            }
            if ($old_data->pppw_date != $data['pppw_date']) {
                $changes['pppw_date'] = array(
                    'old' => $old_data->pppw_date,
                    'new' => $data['pppw_date']
                );
            }
            if ($old_data->pppw_link != $data['pppw_link']) {
                $changes['pppw_link'] = array(
                    'old' => $old_data->pppw_link,
                    'new' => $data['pppw_link']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'pppw_img_img');
        }
        if (!empty($pdf_data)) {
            $files_info['pdfs_added'] = count($pdf_data);
            $files_info['pdf_names'] = array_column($pdf_data, 'pppw_pdf_pdf');
        }
        if (!empty($doc_data)) {
            $files_info['docs_added'] = count($doc_data);
            $files_info['doc_names'] = array_column($doc_data, 'pppw_file_doc');
        }

        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'พรบ./พรก ที่ใช้การปฏิบัติงาน',
            $data['pppw_name'],
            $pppw_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data) + count($pdf_data) + count($doc_data)
            )
        );
        // =======================================================================
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_pppw($pppw_id)
    {
        $old_document = $this->db->get_where('tbl_pppw', array('pppw_id' => $pppw_id))->row();

        $old_file_path = './docs/img/' . $old_document->pppw_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_pppw', array('pppw_id' => $pppw_id));
        $this->space_model->update_server_current();
		// บันทึก log การลบ =================================================
        if ($old_document) {
            $this->log_model->add_log(
                'ลบ',
                'พรบ./พรก ที่ใช้การปฏิบัติงาน',
                $old_document->pppw_name,
                $pppw_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // =======================================================================
    }

    public function del_pppw_pdf($pppw_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_pppw_pdf', array('pppw_pdf_ref_id' => $pppw_id))->result();

        // ลบ pdf จากตาราง tbl_pppw_pdf
        $this->db->where('pppw_pdf_ref_id', $pppw_id);
        $this->db->delete('tbl_pppw_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->pppw_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_pppw_doc($pppw_id)
    {
        // ดึงข้อมูลรายการ doc ก่อน
        $files = $this->db->get_where('tbl_pppw_file', array('pppw_file_ref_id' => $pppw_id))->result();

        // ลบ doc จากตาราง tbl_pppw_file
        $this->db->where('pppw_file_ref_id', $pppw_id);
        $this->db->delete('tbl_pppw_file');

        // ลบไฟล์ doc ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->pppw_file_doc;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_pppw_img($pppw_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_pppw_img', array('pppw_img_ref_id' => $pppw_id))->result();

        // ลบรูปภาพจากตาราง tbl_pppw_file
        $this->db->where('pppw_img_ref_id', $pppw_id);
        $this->db->delete('tbl_pppw_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->pppw_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function update_pppw_status()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $pppwId = $this->input->post('pppw_id'); // รับค่า pppw_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_pppw ในฐานข้อมูลของคุณ
            $data = array(
                'pppw_status' => $newStatus
            );
            $this->db->where('pppw_id', $pppwId); // ระบุ pppw_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_pppw', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function pppw_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_pppw');
        $this->db->where('tbl_pppw.pppw_status', 'show');
        $this->db->limit(8);
        $this->db->order_by('tbl_pppw.pppw_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function pppw_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_pppw');
        $this->db->where('tbl_pppw.pppw_status', 'show');
        $this->db->order_by('tbl_pppw.pppw_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view($pppw_id)
    {
        $this->db->where('pppw_id', $pppw_id);
        $this->db->set('pppw_view', 'pppw_view + 1', false); // บวกค่า pppw_view ทีละ 1
        $this->db->update('tbl_pppw');
    }
    // ใน pppw_model
    public function increment_download_pppw($pppw_file_id)
    {
        $this->db->where('pppw_file_id', $pppw_file_id);
        $this->db->set('pppw_file_download', 'pppw_file_download + 1', false); // บวกค่า pppw_download ทีละ 1
        $this->db->update('tbl_pppw_file');
    }
}
