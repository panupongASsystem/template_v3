<?php
class P_rpobuy_model extends CI_Model
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

        // กำหนดค่าใน $p_rpobuy_data
        $p_rpobuy_data = array(
            'p_rpobuy_name' => $this->input->post('p_rpobuy_name'),
            'p_rpobuy_detail' => $this->input->post('p_rpobuy_detail'),
            'p_rpobuy_date' => $this->input->post('p_rpobuy_date'),
            'p_rpobuy_link' => $this->input->post('p_rpobuy_link'),
            'p_rpobuy_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('p_rpobuy_img');
        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            // ถ้ามีการอัปโหลดรูปภาพ
            $p_rpobuy_data['p_rpobuy_img'] = $this->img_upload->data('file_name');
        }
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_p_rpobuy', $p_rpobuy_data);
        $p_rpobuy_id = $this->db->insert_id();

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['p_rpobuy_img_img'])) {
            foreach ($_FILES['p_rpobuy_img_img']['name'] as $index => $name) {
                if (isset($_FILES['p_rpobuy_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['p_rpobuy_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['p_rpobuy_pdf_pdf'])) {
            foreach ($_FILES['p_rpobuy_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['p_rpobuy_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['p_rpobuy_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['p_rpobuy_file_doc'])) {
            foreach ($_FILES['p_rpobuy_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['p_rpobuy_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['p_rpobuy_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('p_rpobuy_backend/adding');
            return;
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['p_rpobuy_img_img']['name'][0])) {
            foreach ($_FILES['p_rpobuy_img_img']['name'] as $index => $name) {
                $name = $this->space_model->sanitize_filename($name);
                $_FILES['p_rpobuy_img_img_multiple']['name'] = $name;
                $_FILES['p_rpobuy_img_img_multiple']['type'] = $_FILES['p_rpobuy_img_img']['type'][$index];
                $_FILES['p_rpobuy_img_img_multiple']['tmp_name'] = $_FILES['p_rpobuy_img_img']['tmp_name'][$index];
                $_FILES['p_rpobuy_img_img_multiple']['error'] = $_FILES['p_rpobuy_img_img']['error'][$index];
                $_FILES['p_rpobuy_img_img_multiple']['size'] = $_FILES['p_rpobuy_img_img']['size'][$index];

                if ($this->img_upload->do_upload('p_rpobuy_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'p_rpobuy_img_ref_id' => $p_rpobuy_id,
                        'p_rpobuy_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_p_rpobuy_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์PDFเพิ่มเติมหรือไม่
        if (!empty($_FILES['p_rpobuy_pdf_pdf']['name'][0])) {
            foreach ($_FILES['p_rpobuy_pdf_pdf']['name'] as $index => $name) {
                $name = $this->space_model->sanitize_filename($name);
                $_FILES['p_rpobuy_pdf_pdf_multiple']['name'] = $name;
                $_FILES['p_rpobuy_pdf_pdf_multiple']['type'] = $_FILES['p_rpobuy_pdf_pdf']['type'][$index];
                $_FILES['p_rpobuy_pdf_pdf_multiple']['tmp_name'] = $_FILES['p_rpobuy_pdf_pdf']['tmp_name'][$index];
                $_FILES['p_rpobuy_pdf_pdf_multiple']['error'] = $_FILES['p_rpobuy_pdf_pdf']['error'][$index];
                $_FILES['p_rpobuy_pdf_pdf_multiple']['size'] = $_FILES['p_rpobuy_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('p_rpobuy_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'p_rpobuy_pdf_ref_id' => $p_rpobuy_id,
                        'p_rpobuy_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_p_rpobuy_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์Docเพิ่มเติมหรือไม่
        if (!empty($_FILES['p_rpobuy_file_doc']['name'][0])) {
            foreach ($_FILES['p_rpobuy_file_doc']['name'] as $index => $name) {
                $name = $this->space_model->sanitize_filename($name);
                $_FILES['p_rpobuy_file_doc_multiple']['name'] = $name;
                $_FILES['p_rpobuy_file_doc_multiple']['type'] = $_FILES['p_rpobuy_file_doc']['type'][$index];
                $_FILES['p_rpobuy_file_doc_multiple']['tmp_name'] = $_FILES['p_rpobuy_file_doc']['tmp_name'][$index];
                $_FILES['p_rpobuy_file_doc_multiple']['error'] = $_FILES['p_rpobuy_file_doc']['error'][$index];
                $_FILES['p_rpobuy_file_doc_multiple']['size'] = $_FILES['p_rpobuy_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('p_rpobuy_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'p_rpobuy_file_ref_id' => $p_rpobuy_id,
                        'p_rpobuy_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_p_rpobuy_file', $doc_data);
        }
        $this->space_model->update_server_current();
		// บันทึก log การเพิ่มข้อมูล =================================================
        $this->log_model->add_log(
            'เพิ่ม',
            'รายการผลจัดซื้อจัดจ้าง / จัดหาพัสดุประจำปี',
            $p_rpobuy_data['p_rpobuy_name'],
            $p_rpobuy_id,
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
        $this->db->from('tbl_p_rpobuy');
        $this->db->group_by('tbl_p_rpobuy.p_rpobuy_id');
        $this->db->order_by('tbl_p_rpobuy.p_rpobuy_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_all_pdf($p_rpobuy_id)
    {
        $this->db->select('p_rpobuy_pdf_pdf');
        $this->db->from('tbl_p_rpobuy_pdf');
        $this->db->where('p_rpobuy_pdf_ref_id', $p_rpobuy_id);
        return $this->db->get()->result();
    }
    public function list_all_doc($p_rpobuy_id)
    {
        $this->db->select('p_rpobuy_file_doc');
        $this->db->from('tbl_p_rpobuy_file');
        $this->db->where('p_rpobuy_file_ref_id', $p_rpobuy_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read($p_rpobuy_id)
    {
        $this->db->where('p_rpobuy_id', $p_rpobuy_id);
        $query = $this->db->get('tbl_p_rpobuy');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($p_rpobuy_id)
    {
        $this->db->where('p_rpobuy_pdf_ref_id', $p_rpobuy_id);
        $this->db->order_by('p_rpobuy_pdf_id', 'DESC');
        $query = $this->db->get('tbl_p_rpobuy_pdf');
        $results = $query->result();

        // เรียงลำดับตามชื่อไฟล์ PDF
        usort($results, function ($a, $b) {
            return strnatcmp($a->p_rpobuy_pdf_pdf, $b->p_rpobuy_pdf_pdf);
        });

        return $results;
    }

    public function read_doc($p_rpobuy_id)
    {
        $this->db->where('p_rpobuy_file_ref_id', $p_rpobuy_id);
        $this->db->order_by('p_rpobuy_file_id', 'DESC');
        $query = $this->db->get('tbl_p_rpobuy_file');
        $results = $query->result();

        // เรียงลำดับตามชื่อไฟล์ DOC
        usort($results, function ($a, $b) {
            return strnatcmp($a->p_rpobuy_file_doc, $b->p_rpobuy_file_doc);
        });

        return $results;
    }


    public function read_img($p_rpobuy_id)
    {
        $this->db->where('p_rpobuy_img_ref_id', $p_rpobuy_id);
        $query = $this->db->get('tbl_p_rpobuy_img');
        $results = $query->result();
        usort($results, function ($a, $b) {
            return strnatcmp($a->p_rpobuy_img_img, $b->p_rpobuy_img_img);
        });
        return $results;
    }

    public function del_pdf($pdf_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
        $this->db->select('p_rpobuy_pdf_pdf, p_rpobuy_pdf_ref_id');
        $this->db->where('p_rpobuy_pdf_id', $pdf_id);
        $query = $this->db->get('tbl_p_rpobuy_pdf');
        $file_data = $query->row();
		
		// บันทึก log การลบไฟล์ PDF =============================================
        $old_data = $this->read($file_data->p_rpobuy_pdf_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->p_rpobuy_pdf_pdf;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('p_rpobuy_pdf_id', $pdf_id);
        $this->db->delete('tbl_p_rpobuy_pdf');
        $this->space_model->update_server_current();
		// บันทึก log การลบไฟล์ PDF =============================================
        $this->log_model->add_log(
            'ลบ',
            'รายการผลจัดซื้อจัดจ้าง / จัดหาพัสดุประจำปี',
            'ไฟล์ PDF: ' . $file_data->p_rpobuy_pdf_pdf . ' จาก: ' . ($old_data ? $old_data->p_rpobuy_name : 'ไม่ระบุ'),
            $file_data->p_rpobuy_pdf_ref_id,
            array('file_type' => 'PDF', 'file_name' => $file_data->p_rpobuy_pdf_pdf)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_doc($doc_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $doc_id
        $this->db->select('p_rpobuy_file_doc, p_rpobuy_file_ref_id');
        $this->db->where('p_rpobuy_file_id', $doc_id);
        $query = $this->db->get('tbl_p_rpobuy_file');
        $file_data = $query->row();
		
		// บันทึก log การลบไฟล์ DOC ========================================
        $old_data = $this->read($file_data->p_rpobuy_file_ref_id);
        // ================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->p_rpobuy_file_doc;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('p_rpobuy_file_id', $doc_id);
        $this->db->delete('tbl_p_rpobuy_file');
        $this->space_model->update_server_current();
		// บันทึก log การลบไฟล์ DOC ========================================
        $this->log_model->add_log(
            'ลบ',
            'รายการผลจัดซื้อจัดจ้าง / จัดหาพัสดุประจำปี',
            'ไฟล์ DOC: ' . $file_data->p_rpobuy_file_doc . ' จาก: ' . ($old_data ? $old_data->p_rpobuy_name : 'ไม่ระบุ'),
            $file_data->p_rpobuy_file_ref_id,
            array('file_type' => 'DOC', 'file_name' => $file_data->p_rpobuy_file_doc)
        );
        // ================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('p_rpobuy_img_img, p_rpobuy_img_ref_id');
        $this->db->where('p_rpobuy_img_id', $file_id);
        $query = $this->db->get('tbl_p_rpobuy_img');
        $file_data = $query->row();
		
		// บันทึก log การลบรูปภาพ =================================================
        $old_data = $this->read($file_data->p_rpobuy_img_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->p_rpobuy_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('p_rpobuy_img_id', $file_id);
        $this->db->delete('tbl_p_rpobuy_img');
        $this->space_model->update_server_current();
		// บันทึก log การลบรูปภาพ =================================================
        $this->log_model->add_log(
            'ลบ',
            'รายการผลจัดซื้อจัดจ้าง / จัดหาพัสดุประจำปี',
            'รูปภาพ: ' . $file_data->p_rpobuy_img_img . ' จาก: ' . ($old_data ? $old_data->p_rpobuy_name : 'ไม่ระบุ'),
            $file_data->p_rpobuy_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->p_rpobuy_img_img)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }


    public function edit($p_rpobuy_id)
    {
		// ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($p_rpobuy_id);
		
        // Update p_rpobuy information
        $data = array(
            'p_rpobuy_name' => $this->input->post('p_rpobuy_name'),
            'p_rpobuy_detail' => $this->input->post('p_rpobuy_detail'),
            'p_rpobuy_date' => $this->input->post('p_rpobuy_date'),
            'p_rpobuy_link' => $this->input->post('p_rpobuy_link'),
            'p_rpobuy_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('p_rpobuy_id', $p_rpobuy_id);
        $this->db->update('tbl_p_rpobuy', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['p_rpobuy_img_img'])) {
            foreach ($_FILES['p_rpobuy_img_img']['name'] as $index => $name) {
                if (isset($_FILES['p_rpobuy_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['p_rpobuy_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['p_rpobuy_pdf_pdf'])) {
            foreach ($_FILES['p_rpobuy_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['p_rpobuy_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['p_rpobuy_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['p_rpobuy_file_doc'])) {
            foreach ($_FILES['p_rpobuy_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['p_rpobuy_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['p_rpobuy_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('p_rpobuy_backend/adding');
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
        $img_main = $this->img_upload->do_upload('p_rpobuy_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_p_rpobuy', array('p_rpobuy_id' => $p_rpobuy_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->p_rpobuy_img) {
                $old_file_path = './docs/img/' . $old_document->p_rpobuy_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['p_rpobuy_img'] = $this->img_upload->data('file_name');
            $this->db->where('p_rpobuy_id', $p_rpobuy_id);
            $this->db->update('tbl_p_rpobuy', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['p_rpobuy_img_img']['name'][0])) {

            foreach ($_FILES['p_rpobuy_img_img']['name'] as $index => $name) {
                $name = $this->space_model->sanitize_filename($name);
                $_FILES['p_rpobuy_img_img_multiple']['name'] = $name;
                $_FILES['p_rpobuy_img_img_multiple']['type'] = $_FILES['p_rpobuy_img_img']['type'][$index];
                $_FILES['p_rpobuy_img_img_multiple']['tmp_name'] = $_FILES['p_rpobuy_img_img']['tmp_name'][$index];
                $_FILES['p_rpobuy_img_img_multiple']['error'] = $_FILES['p_rpobuy_img_img']['error'][$index];
                $_FILES['p_rpobuy_img_img_multiple']['size'] = $_FILES['p_rpobuy_img_img']['size'][$index];

                if ($this->img_upload->do_upload('p_rpobuy_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'p_rpobuy_img_ref_id' => $p_rpobuy_id,
                        'p_rpobuy_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_p_rpobuy_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['p_rpobuy_pdf_pdf']['name'][0])) {
            foreach ($_FILES['p_rpobuy_pdf_pdf']['name'] as $index => $name) {
                $name = $this->space_model->sanitize_filename($name);
                $_FILES['p_rpobuy_pdf_pdf_multiple']['name'] = $name;
                $_FILES['p_rpobuy_pdf_pdf_multiple']['type'] = $_FILES['p_rpobuy_pdf_pdf']['type'][$index];
                $_FILES['p_rpobuy_pdf_pdf_multiple']['tmp_name'] = $_FILES['p_rpobuy_pdf_pdf']['tmp_name'][$index];
                $_FILES['p_rpobuy_pdf_pdf_multiple']['error'] = $_FILES['p_rpobuy_pdf_pdf']['error'][$index];
                $_FILES['p_rpobuy_pdf_pdf_multiple']['size'] = $_FILES['p_rpobuy_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('p_rpobuy_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'p_rpobuy_pdf_ref_id' => $p_rpobuy_id,
                        'p_rpobuy_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_p_rpobuy_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
        if (!empty($_FILES['p_rpobuy_file_doc']['name'][0])) {
            foreach ($_FILES['p_rpobuy_file_doc']['name'] as $index => $name) {
                $name = $this->space_model->sanitize_filename($name);
                $_FILES['p_rpobuy_file_doc_multiple']['name'] = $name;
                $_FILES['p_rpobuy_file_doc_multiple']['type'] = $_FILES['p_rpobuy_file_doc']['type'][$index];
                $_FILES['p_rpobuy_file_doc_multiple']['tmp_name'] = $_FILES['p_rpobuy_file_doc']['tmp_name'][$index];
                $_FILES['p_rpobuy_file_doc_multiple']['error'] = $_FILES['p_rpobuy_file_doc']['error'][$index];
                $_FILES['p_rpobuy_file_doc_multiple']['size'] = $_FILES['p_rpobuy_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('p_rpobuy_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'p_rpobuy_file_ref_id' => $p_rpobuy_id,
                        'p_rpobuy_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_p_rpobuy_file', $doc_data);
        }
        $this->space_model->update_server_current();
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_p_rpobuy($p_rpobuy_id)
    {
        $old_document = $this->db->get_where('tbl_p_rpobuy', array('p_rpobuy_id' => $p_rpobuy_id))->row();

        $old_file_path = './docs/img/' . $old_document->p_rpobuy_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_p_rpobuy', array('p_rpobuy_id' => $p_rpobuy_id));
        $this->space_model->update_server_current();
    }

    public function del_p_rpobuy_pdf($p_rpobuy_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_p_rpobuy_pdf', array('p_rpobuy_pdf_ref_id' => $p_rpobuy_id))->result();

        // ลบ pdf จากตาราง tbl_p_rpobuy_pdf
        $this->db->where('p_rpobuy_pdf_ref_id', $p_rpobuy_id);
        $this->db->delete('tbl_p_rpobuy_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->p_rpobuy_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_p_rpobuy_doc($p_rpobuy_id)
    {
        // ดึงข้อมูลรายการ doc ก่อน
        $files = $this->db->get_where('tbl_p_rpobuy_file', array('p_rpobuy_file_ref_id' => $p_rpobuy_id))->result();

        // ลบ doc จากตาราง tbl_p_rpobuy_file
        $this->db->where('p_rpobuy_file_ref_id', $p_rpobuy_id);
        $this->db->delete('tbl_p_rpobuy_file');

        // ลบไฟล์ doc ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->p_rpobuy_file_doc;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_p_rpobuy_img($p_rpobuy_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_p_rpobuy_img', array('p_rpobuy_img_ref_id' => $p_rpobuy_id))->result();

        // ลบรูปภาพจากตาราง tbl_p_rpobuy_file
        $this->db->where('p_rpobuy_img_ref_id', $p_rpobuy_id);
        $this->db->delete('tbl_p_rpobuy_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->p_rpobuy_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function update_p_rpobuy_status()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $p_rpobuyId = $this->input->post('p_rpobuy_id'); // รับค่า p_rpobuy_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_p_rpobuy ในฐานข้อมูลของคุณ
            $data = array(
                'p_rpobuy_status' => $newStatus
            );
            $this->db->where('p_rpobuy_id', $p_rpobuyId); // ระบุ p_rpobuy_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_p_rpobuy', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function p_rpobuy_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_p_rpobuy');
        $this->db->where('tbl_p_rpobuy.p_rpobuy_status', 'show');
        $this->db->limit(8);
        $this->db->order_by('tbl_p_rpobuy.p_rpobuy_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function p_rpobuy_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_p_rpobuy');
        $this->db->where('tbl_p_rpobuy.p_rpobuy_status', 'show');
        $this->db->order_by('tbl_p_rpobuy.p_rpobuy_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view($p_rpobuy_id)
    {
        $this->db->where('p_rpobuy_id', $p_rpobuy_id);
        $this->db->set('p_rpobuy_view', 'p_rpobuy_view + 1', false); // บวกค่า p_rpobuy_view ทีละ 1
        $this->db->update('tbl_p_rpobuy');
    }
    // ใน p_rpobuy_model
    public function increment_download_p_rpobuy($p_rpobuy_file_id)
    {
        $this->db->where('p_rpobuy_file_id', $p_rpobuy_file_id);
        $this->db->set('p_rpobuy_file_download', 'p_rpobuy_file_download + 1', false); // บวกค่า p_rpobuy_download ทีละ 1
        $this->db->update('tbl_p_rpobuy_file');
    }
}
