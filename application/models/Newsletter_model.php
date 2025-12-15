<?php
class Newsletter_model extends CI_Model
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

        // กำหนดค่าใน $newsletter_data
        $newsletter_data = array(
            'newsletter_name' => $this->input->post('newsletter_name'),
            'newsletter_detail' => $this->input->post('newsletter_detail'),
            'newsletter_date' => $this->input->post('newsletter_date'),
            'newsletter_link' => $this->input->post('newsletter_link'),
            'newsletter_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('newsletter_img');
        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            // ถ้ามีการอัปโหลดรูปภาพ
            $newsletter_data['newsletter_img'] = $this->img_upload->data('file_name');
        }
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_newsletter', $newsletter_data);
        $newsletter_id = $this->db->insert_id();

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['newsletter_img_img'])) {
            foreach ($_FILES['newsletter_img_img']['name'] as $index => $name) {
                if (isset($_FILES['newsletter_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['newsletter_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['newsletter_pdf_pdf'])) {
            foreach ($_FILES['newsletter_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['newsletter_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['newsletter_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['newsletter_file_doc'])) {
            foreach ($_FILES['newsletter_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['newsletter_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['newsletter_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('newsletter_backend/adding');
            return;
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['newsletter_img_img']['name'][0])) {
            foreach ($_FILES['newsletter_img_img']['name'] as $index => $name) {
                $_FILES['newsletter_img_img_multiple']['name'] = $name;
                $_FILES['newsletter_img_img_multiple']['type'] = $_FILES['newsletter_img_img']['type'][$index];
                $_FILES['newsletter_img_img_multiple']['tmp_name'] = $_FILES['newsletter_img_img']['tmp_name'][$index];
                $_FILES['newsletter_img_img_multiple']['error'] = $_FILES['newsletter_img_img']['error'][$index];
                $_FILES['newsletter_img_img_multiple']['size'] = $_FILES['newsletter_img_img']['size'][$index];

                if ($this->img_upload->do_upload('newsletter_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'newsletter_img_ref_id' => $newsletter_id,
                        'newsletter_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_newsletter_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์PDFเพิ่มเติมหรือไม่
        if (!empty($_FILES['newsletter_pdf_pdf']['name'][0])) {
            foreach ($_FILES['newsletter_pdf_pdf']['name'] as $index => $name) {
                $_FILES['newsletter_pdf_pdf_multiple']['name'] = $name;
                $_FILES['newsletter_pdf_pdf_multiple']['type'] = $_FILES['newsletter_pdf_pdf']['type'][$index];
                $_FILES['newsletter_pdf_pdf_multiple']['tmp_name'] = $_FILES['newsletter_pdf_pdf']['tmp_name'][$index];
                $_FILES['newsletter_pdf_pdf_multiple']['error'] = $_FILES['newsletter_pdf_pdf']['error'][$index];
                $_FILES['newsletter_pdf_pdf_multiple']['size'] = $_FILES['newsletter_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('newsletter_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'newsletter_pdf_ref_id' => $newsletter_id,
                        'newsletter_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_newsletter_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์Docเพิ่มเติมหรือไม่
        if (!empty($_FILES['newsletter_file_doc']['name'][0])) {
            foreach ($_FILES['newsletter_file_doc']['name'] as $index => $name) {
                $_FILES['newsletter_file_doc_multiple']['name'] = $name;
                $_FILES['newsletter_file_doc_multiple']['type'] = $_FILES['newsletter_file_doc']['type'][$index];
                $_FILES['newsletter_file_doc_multiple']['tmp_name'] = $_FILES['newsletter_file_doc']['tmp_name'][$index];
                $_FILES['newsletter_file_doc_multiple']['error'] = $_FILES['newsletter_file_doc']['error'][$index];
                $_FILES['newsletter_file_doc_multiple']['size'] = $_FILES['newsletter_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('newsletter_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'newsletter_file_ref_id' => $newsletter_id,
                        'newsletter_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_newsletter_file', $doc_data);
        }
        $this->space_model->update_server_current();
		// บันทึก log การเพิ่มข้อมูล =================================================
        $this->log_model->add_log(
            'เพิ่ม',
            'จดหมายข่าว',
            $newsletter_data['newsletter_name'],
            $newsletter_id,
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
        $this->db->from('tbl_newsletter');
        $this->db->group_by('tbl_newsletter.newsletter_id');
        $this->db->order_by('tbl_newsletter.newsletter_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_all_pdf($newsletter_id)
    {
        $this->db->select('newsletter_pdf_pdf');
        $this->db->from('tbl_newsletter_pdf');
        $this->db->where('newsletter_pdf_ref_id', $newsletter_id);
        return $this->db->get()->result();
    }
    public function list_all_doc($newsletter_id)
    {
        $this->db->select('newsletter_file_doc');
        $this->db->from('tbl_newsletter_file');
        $this->db->where('newsletter_file_ref_id', $newsletter_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read($newsletter_id)
    {
        $this->db->where('newsletter_id', $newsletter_id);
        $query = $this->db->get('tbl_newsletter');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($newsletter_id)
    {
        $this->db->where('newsletter_pdf_ref_id', $newsletter_id);
        $this->db->order_by('newsletter_pdf_id', 'DESC');
        $query = $this->db->get('tbl_newsletter_pdf');
        $results = $query->result();

        // เรียงลำดับตามชื่อไฟล์ PDF
        usort($results, function ($a, $b) {
            return strnatcmp($a->newsletter_pdf_pdf, $b->newsletter_pdf_pdf);
        });

        return $results;
    }

    public function read_doc($newsletter_id)
    {
        $this->db->where('newsletter_file_ref_id', $newsletter_id);
        $this->db->order_by('newsletter_file_id', 'DESC');
        $query = $this->db->get('tbl_newsletter_file');
        $results = $query->result();

        // เรียงลำดับตามชื่อไฟล์ DOC
        usort($results, function ($a, $b) {
            return strnatcmp($a->newsletter_file_doc, $b->newsletter_file_doc);
        });

        return $results;
    }

    public function read_img($newsletter_id)
    {
        $this->db->where('newsletter_img_ref_id', $newsletter_id);
        $query = $this->db->get('tbl_newsletter_img');
        $results = $query->result();
        usort($results, function ($a, $b) {
            return strnatcmp($a->newsletter_img_img, $b->newsletter_img_img);
        });
        return $results;
    }


    public function del_pdf($pdf_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
        $this->db->select('newsletter_pdf_pdf, newsletter_pdf_ref_id');
        $this->db->where('newsletter_pdf_id', $pdf_id);
        $query = $this->db->get('tbl_newsletter_pdf');
        $file_data = $query->row();

        // บันทึก log การลบไฟล์ PDF =============================================
        $old_data = $this->read($file_data->newsletter_pdf_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->newsletter_pdf_pdf;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('newsletter_pdf_id', $pdf_id);
        $this->db->delete('tbl_newsletter_pdf');
        $this->space_model->update_server_current();

        // บันทึก log การลบไฟล์ PDF =============================================
        $this->log_model->add_log(
            'ลบ',
            'จดหมายข่าว',
            'ไฟล์ PDF: ' . $file_data->newsletter_pdf_pdf . ' จาก: ' . ($old_data ? $old_data->newsletter_name : 'ไม่ระบุ'),
            $file_data->newsletter_pdf_ref_id,
            array('file_type' => 'PDF', 'file_name' => $file_data->newsletter_pdf_pdf)
        );
        // =======================================================================

        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_doc($doc_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $doc_id
        $this->db->select('newsletter_file_doc, newsletter_file_ref_id');
        $this->db->where('newsletter_file_id', $doc_id);
        $query = $this->db->get('tbl_newsletter_file');
        $file_data = $query->row();

        // บันทึก log การลบไฟล์ DOC ========================================
        $old_data = $this->read($file_data->newsletter_file_ref_id);
        // ================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->newsletter_file_doc;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('newsletter_file_id', $doc_id);
        $this->db->delete('tbl_newsletter_file');
        $this->space_model->update_server_current();

        // บันทึก log การลบไฟล์ DOC ========================================
        $this->log_model->add_log(
            'ลบ',
            'จดหมายข่าว',
            'ไฟล์ DOC: ' . $file_data->newsletter_file_doc . ' จาก: ' . ($old_data ? $old_data->newsletter_name : 'ไม่ระบุ'),
            $file_data->newsletter_file_ref_id,
            array('file_type' => 'DOC', 'file_name' => $file_data->newsletter_file_doc)
        );
        // ================================================================

        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('newsletter_img_img, newsletter_img_ref_id');
        $this->db->where('newsletter_img_id', $file_id);
        $query = $this->db->get('tbl_newsletter_img');
        $file_data = $query->row();

        // บันทึก log การลบรูปภาพ =================================================
        $old_data = $this->read($file_data->newsletter_img_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->newsletter_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // บันทึก log การลบรูปภาพ =================================================
        $this->log_model->add_log(
            'ลบ',
            'จดหมายข่าว',
            'รูปภาพ: ' . $file_data->newsletter_img_img . ' จาก: ' . ($old_data ? $old_data->newsletter_name : 'ไม่ระบุ'),
            $file_data->newsletter_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->newsletter_img_img)
        );
        // =======================================================================

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('newsletter_img_id', $file_id);
        $this->db->delete('tbl_newsletter_img');
        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', TRUE);
    }


    public function edit($newsletter_id)
    {
        // Update newsletter information
        $data = array(
            'newsletter_name' => $this->input->post('newsletter_name'),
            'newsletter_detail' => $this->input->post('newsletter_detail'),
            'newsletter_date' => $this->input->post('newsletter_date'),
            'newsletter_link' => $this->input->post('newsletter_link'),
            'newsletter_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('newsletter_id', $newsletter_id);
        $this->db->update('tbl_newsletter', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['newsletter_img_img'])) {
            foreach ($_FILES['newsletter_img_img']['name'] as $index => $name) {
                if (isset($_FILES['newsletter_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['newsletter_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['newsletter_pdf_pdf'])) {
            foreach ($_FILES['newsletter_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['newsletter_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['newsletter_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['newsletter_file_doc'])) {
            foreach ($_FILES['newsletter_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['newsletter_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['newsletter_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('newsletter_backend/adding');
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
        $img_main = $this->img_upload->do_upload('newsletter_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_newsletter', array('newsletter_id' => $newsletter_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->newsletter_img) {
                $old_file_path = './docs/img/' . $old_document->newsletter_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['newsletter_img'] = $this->img_upload->data('file_name');
            $this->db->where('newsletter_id', $newsletter_id);
            $this->db->update('tbl_newsletter', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['newsletter_img_img']['name'][0])) {

            foreach ($_FILES['newsletter_img_img']['name'] as $index => $name) {
                $_FILES['newsletter_img_img_multiple']['name'] = $name;
                $_FILES['newsletter_img_img_multiple']['type'] = $_FILES['newsletter_img_img']['type'][$index];
                $_FILES['newsletter_img_img_multiple']['tmp_name'] = $_FILES['newsletter_img_img']['tmp_name'][$index];
                $_FILES['newsletter_img_img_multiple']['error'] = $_FILES['newsletter_img_img']['error'][$index];
                $_FILES['newsletter_img_img_multiple']['size'] = $_FILES['newsletter_img_img']['size'][$index];

                if ($this->img_upload->do_upload('newsletter_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'newsletter_img_ref_id' => $newsletter_id,
                        'newsletter_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_newsletter_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['newsletter_pdf_pdf']['name'][0])) {
            foreach ($_FILES['newsletter_pdf_pdf']['name'] as $index => $name) {
                $_FILES['newsletter_pdf_pdf_multiple']['name'] = $name;
                $_FILES['newsletter_pdf_pdf_multiple']['type'] = $_FILES['newsletter_pdf_pdf']['type'][$index];
                $_FILES['newsletter_pdf_pdf_multiple']['tmp_name'] = $_FILES['newsletter_pdf_pdf']['tmp_name'][$index];
                $_FILES['newsletter_pdf_pdf_multiple']['error'] = $_FILES['newsletter_pdf_pdf']['error'][$index];
                $_FILES['newsletter_pdf_pdf_multiple']['size'] = $_FILES['newsletter_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('newsletter_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'newsletter_pdf_ref_id' => $newsletter_id,
                        'newsletter_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_newsletter_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
        if (!empty($_FILES['newsletter_file_doc']['name'][0])) {
            foreach ($_FILES['newsletter_file_doc']['name'] as $index => $name) {
                $_FILES['newsletter_file_doc_multiple']['name'] = $name;
                $_FILES['newsletter_file_doc_multiple']['type'] = $_FILES['newsletter_file_doc']['type'][$index];
                $_FILES['newsletter_file_doc_multiple']['tmp_name'] = $_FILES['newsletter_file_doc']['tmp_name'][$index];
                $_FILES['newsletter_file_doc_multiple']['error'] = $_FILES['newsletter_file_doc']['error'][$index];
                $_FILES['newsletter_file_doc_multiple']['size'] = $_FILES['newsletter_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('newsletter_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'newsletter_file_ref_id' => $newsletter_id,
                        'newsletter_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_newsletter_file', $doc_data);
        }
        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_newsletter($newsletter_id)
    {
        $old_document = $this->db->get_where('tbl_newsletter', array('newsletter_id' => $newsletter_id))->row();

        $old_file_path = './docs/img/' . $old_document->newsletter_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_newsletter', array('newsletter_id' => $newsletter_id));
        $this->space_model->update_server_current();
		// บันทึก log การลบ =================================================
        if ($old_document) {
            $this->log_model->add_log(
                'ลบ',
                'จดหมายข่าว',
                $old_document->newsletter_name,
                $newsletter_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // =======================================================================
    }

    public function del_newsletter_pdf($newsletter_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_newsletter_pdf', array('newsletter_pdf_ref_id' => $newsletter_id))->result();

        // ลบ pdf จากตาราง tbl_newsletter_pdf
        $this->db->where('newsletter_pdf_ref_id', $newsletter_id);
        $this->db->delete('tbl_newsletter_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->newsletter_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_newsletter_doc($newsletter_id)
    {
        // ดึงข้อมูลรายการ doc ก่อน
        $files = $this->db->get_where('tbl_newsletter_file', array('newsletter_file_ref_id' => $newsletter_id))->result();

        // ลบ doc จากตาราง tbl_newsletter_file
        $this->db->where('newsletter_file_ref_id', $newsletter_id);
        $this->db->delete('tbl_newsletter_file');

        // ลบไฟล์ doc ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->newsletter_file_doc;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_newsletter_img($newsletter_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_newsletter_img', array('newsletter_img_ref_id' => $newsletter_id))->result();

        // ลบรูปภาพจากตาราง tbl_newsletter_file
        $this->db->where('newsletter_img_ref_id', $newsletter_id);
        $this->db->delete('tbl_newsletter_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->newsletter_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function update_newsletter_status()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $newsletterId = $this->input->post('newsletter_id'); // รับค่า newsletter_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_newsletter ในฐานข้อมูลของคุณ
            $data = array(
                'newsletter_status' => $newStatus
            );
            $this->db->where('newsletter_id', $newsletterId); // ระบุ newsletter_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_newsletter', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function newsletter_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_newsletter');
        $this->db->where('tbl_newsletter.newsletter_status', 'show');
        $this->db->order_by('tbl_newsletter.newsletter_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function newsletter_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_newsletter');
        $this->db->where('tbl_newsletter.newsletter_status', 'show');
        $this->db->order_by('tbl_newsletter.newsletter_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view($newsletter_id)
    {
        $this->db->where('newsletter_id', $newsletter_id);
        $this->db->set('newsletter_view', 'newsletter_view + 1', false); // บวกค่า newsletter_view ทีละ 1
        $this->db->update('tbl_newsletter');
    }
    // ใน newsletter_model
    public function increment_download_newsletter($newsletter_file_id)
    {
        $this->db->where('newsletter_file_id', $newsletter_file_id);
        $this->db->set('newsletter_file_download', 'newsletter_file_download + 1', false); // บวกค่า newsletter_download ทีละ 1
        $this->db->update('tbl_newsletter_file');
    }
}
