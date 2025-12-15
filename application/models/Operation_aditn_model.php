<?php
class Operation_aditn_model extends CI_Model
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

        // กำหนดค่าใน $operation_aditn_data
        $operation_aditn_data = array(
            'operation_aditn_name' => $this->input->post('operation_aditn_name'),
            'operation_aditn_detail' => $this->input->post('operation_aditn_detail'),
            'operation_aditn_date' => $this->input->post('operation_aditn_date'),
            'operation_aditn_link' => $this->input->post('operation_aditn_link'),
            'operation_aditn_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('operation_aditn_img');
        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            // ถ้ามีการอัปโหลดรูปภาพ
            $operation_aditn_data['operation_aditn_img'] = $this->img_upload->data('file_name');
        }
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_operation_aditn', $operation_aditn_data);
        $operation_aditn_id = $this->db->insert_id();

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['operation_aditn_img_img'])) {
            foreach ($_FILES['operation_aditn_img_img']['name'] as $index => $name) {
                if (isset($_FILES['operation_aditn_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['operation_aditn_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['operation_aditn_pdf_pdf'])) {
            foreach ($_FILES['operation_aditn_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['operation_aditn_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['operation_aditn_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['operation_aditn_file_doc'])) {
            foreach ($_FILES['operation_aditn_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['operation_aditn_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['operation_aditn_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('operation_aditn_backend/adding');
            return;
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['operation_aditn_img_img']['name'][0])) {
            foreach ($_FILES['operation_aditn_img_img']['name'] as $index => $name) {
                $_FILES['operation_aditn_img_img_multiple']['name'] = $name;
                $_FILES['operation_aditn_img_img_multiple']['type'] = $_FILES['operation_aditn_img_img']['type'][$index];
                $_FILES['operation_aditn_img_img_multiple']['tmp_name'] = $_FILES['operation_aditn_img_img']['tmp_name'][$index];
                $_FILES['operation_aditn_img_img_multiple']['error'] = $_FILES['operation_aditn_img_img']['error'][$index];
                $_FILES['operation_aditn_img_img_multiple']['size'] = $_FILES['operation_aditn_img_img']['size'][$index];

                if ($this->img_upload->do_upload('operation_aditn_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'operation_aditn_img_ref_id' => $operation_aditn_id,
                        'operation_aditn_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_operation_aditn_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์PDFเพิ่มเติมหรือไม่
        if (!empty($_FILES['operation_aditn_pdf_pdf']['name'][0])) {
            foreach ($_FILES['operation_aditn_pdf_pdf']['name'] as $index => $name) {
                $_FILES['operation_aditn_pdf_pdf_multiple']['name'] = $name;
                $_FILES['operation_aditn_pdf_pdf_multiple']['type'] = $_FILES['operation_aditn_pdf_pdf']['type'][$index];
                $_FILES['operation_aditn_pdf_pdf_multiple']['tmp_name'] = $_FILES['operation_aditn_pdf_pdf']['tmp_name'][$index];
                $_FILES['operation_aditn_pdf_pdf_multiple']['error'] = $_FILES['operation_aditn_pdf_pdf']['error'][$index];
                $_FILES['operation_aditn_pdf_pdf_multiple']['size'] = $_FILES['operation_aditn_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('operation_aditn_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'operation_aditn_pdf_ref_id' => $operation_aditn_id,
                        'operation_aditn_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_operation_aditn_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์Docเพิ่มเติมหรือไม่
        if (!empty($_FILES['operation_aditn_file_doc']['name'][0])) {
            foreach ($_FILES['operation_aditn_file_doc']['name'] as $index => $name) {
                $_FILES['operation_aditn_file_doc_multiple']['name'] = $name;
                $_FILES['operation_aditn_file_doc_multiple']['type'] = $_FILES['operation_aditn_file_doc']['type'][$index];
                $_FILES['operation_aditn_file_doc_multiple']['tmp_name'] = $_FILES['operation_aditn_file_doc']['tmp_name'][$index];
                $_FILES['operation_aditn_file_doc_multiple']['error'] = $_FILES['operation_aditn_file_doc']['error'][$index];
                $_FILES['operation_aditn_file_doc_multiple']['size'] = $_FILES['operation_aditn_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('operation_aditn_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'operation_aditn_file_ref_id' => $operation_aditn_id,
                        'operation_aditn_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_operation_aditn_file', $doc_data);
        }
        $this->space_model->update_server_current();
		// บันทึก log การเพิ่มข้อมูล =================================================
        $this->log_model->add_log(
            'เพิ่ม',
            'ตรวจสอบภายใน',
            $operation_aditn_data['operation_aditn_name'],
            $operation_aditn_id,
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
        $this->db->from('tbl_operation_aditn');
        $this->db->group_by('tbl_operation_aditn.operation_aditn_id');
        $this->db->order_by('tbl_operation_aditn.operation_aditn_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_all_pdf($operation_aditn_id)
    {
        $this->db->select('operation_aditn_pdf_pdf');
        $this->db->from('tbl_operation_aditn_pdf');
        $this->db->where('operation_aditn_pdf_ref_id', $operation_aditn_id);
        return $this->db->get()->result();
    }
    public function list_all_doc($operation_aditn_id)
    {
        $this->db->select('operation_aditn_file_doc');
        $this->db->from('tbl_operation_aditn_file');
        $this->db->where('operation_aditn_file_ref_id', $operation_aditn_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read($operation_aditn_id)
    {
        $this->db->where('operation_aditn_id', $operation_aditn_id);
        $query = $this->db->get('tbl_operation_aditn');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($operation_aditn_id)
    {
        $this->db->where('operation_aditn_pdf_ref_id', $operation_aditn_id);
        $this->db->order_by('operation_aditn_pdf_id', 'DESC');
        $query = $this->db->get('tbl_operation_aditn_pdf');
        $results = $query->result();

        // เรียงลำดับตามชื่อไฟล์ PDF
        usort($results, function ($a, $b) {
            return strnatcmp($a->operation_aditn_pdf_pdf, $b->operation_aditn_pdf_pdf);
        });

        return $results;
    }

    public function read_doc($operation_aditn_id)
    {
        $this->db->where('operation_aditn_file_ref_id', $operation_aditn_id);
        $this->db->order_by('operation_aditn_file_id', 'DESC');
        $query = $this->db->get('tbl_operation_aditn_file');
        $results = $query->result();

        // เรียงลำดับตามชื่อไฟล์ DOC
        usort($results, function ($a, $b) {
            return strnatcmp($a->operation_aditn_file_doc, $b->operation_aditn_file_doc);
        });

        return $results;
    }

    public function read_img($operation_aditn_id)
    {
        $this->db->where('operation_aditn_img_ref_id', $operation_aditn_id);
        $query = $this->db->get('tbl_operation_aditn_img');
        $results = $query->result();
        usort($results, function ($a, $b) {
            return strnatcmp($a->operation_aditn_img_img, $b->operation_aditn_img_img);
        });
        return $results;
    }


    public function del_pdf($pdf_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
        $this->db->select('operation_aditn_pdf_pdf, operation_aditn_pdf_ref_id');
        $this->db->where('operation_aditn_pdf_id', $pdf_id);
        $query = $this->db->get('tbl_operation_aditn_pdf');
        $file_data = $query->row();
		
		// บันทึก log การลบไฟล์ PDF =============================================
        $old_data = $this->read($file_data->operation_aditn_pdf_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->operation_aditn_pdf_pdf;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('operation_aditn_pdf_id', $pdf_id);
        $this->db->delete('tbl_operation_aditn_pdf');
        $this->space_model->update_server_current();
		// บันทึก log การลบไฟล์ PDF =============================================
        $this->log_model->add_log(
            'ลบ',
            'ตรวจสอบภายใน',
            'ไฟล์ PDF: ' . $file_data->operation_aditn_pdf_pdf . ' จาก: ' . ($old_data ? $old_data->operation_aditn_name : 'ไม่ระบุ'),
            $file_data->operation_aditn_pdf_ref_id,
            array('file_type' => 'PDF', 'file_name' => $file_data->operation_aditn_pdf_pdf)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_doc($doc_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $doc_id
        $this->db->select('operation_aditn_file_doc, operation_aditn_file_ref_id');
        $this->db->where('operation_aditn_file_id', $doc_id);
        $query = $this->db->get('tbl_operation_aditn_file');
        $file_data = $query->row();
		
		// บันทึก log การลบไฟล์ DOC ========================================
        $old_data = $this->read($file_data->operation_aditn_file_ref_id);
        // ================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->operation_aditn_file_doc;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('operation_aditn_file_id', $doc_id);
        $this->db->delete('tbl_operation_aditn_file');
        $this->space_model->update_server_current();
		// บันทึก log การลบไฟล์ DOC ========================================
        $this->log_model->add_log(
            'ลบ',
            'ตรวจสอบภายใน',
            'ไฟล์ DOC: ' . $file_data->operation_aditn_file_doc . ' จาก: ' . ($old_data ? $old_data->operation_aditn_name : 'ไม่ระบุ'),
            $file_data->operation_aditn_file_ref_id,
            array('file_type' => 'DOC', 'file_name' => $file_data->operation_aditn_file_doc)
        );
        // ================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('operation_aditn_img_img, operation_aditn_img_ref_id');
        $this->db->where('operation_aditn_img_id', $file_id);
        $query = $this->db->get('tbl_operation_aditn_img');
        $file_data = $query->row();
		
		// บันทึก log การลบรูปภาพ =================================================
        $old_data = $this->read($file_data->operation_aditn_img_ref_id);
        // =======================================================================

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->operation_aditn_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('operation_aditn_img_id', $file_id);
        $this->db->delete('tbl_operation_aditn_img');
        $this->space_model->update_server_current();
		// บันทึก log การลบรูปภาพ =================================================
        $this->log_model->add_log(
            'ลบ',
            'ตรวจสอบภายใน',
            'รูปภาพ: ' . $file_data->operation_aditn_img_img . ' จาก: ' . ($old_data ? $old_data->operation_aditn_name : 'ไม่ระบุ'),
            $file_data->operation_aditn_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->operation_aditn_img_img)
        );
        // =======================================================================
        $this->session->set_flashdata('del_success', TRUE);
    }


    public function edit($operation_aditn_id)
    {
		// ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($operation_aditn_id);
		
        // Update operation_aditn information
        $data = array(
            'operation_aditn_name' => $this->input->post('operation_aditn_name'),
            'operation_aditn_detail' => $this->input->post('operation_aditn_detail'),
            'operation_aditn_date' => $this->input->post('operation_aditn_date'),
            'operation_aditn_link' => $this->input->post('operation_aditn_link'),
            'operation_aditn_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('operation_aditn_id', $operation_aditn_id);
        $this->db->update('tbl_operation_aditn', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['operation_aditn_img_img'])) {
            foreach ($_FILES['operation_aditn_img_img']['name'] as $index => $name) {
                if (isset($_FILES['operation_aditn_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['operation_aditn_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['operation_aditn_pdf_pdf'])) {
            foreach ($_FILES['operation_aditn_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['operation_aditn_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['operation_aditn_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['operation_aditn_file_doc'])) {
            foreach ($_FILES['operation_aditn_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['operation_aditn_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['operation_aditn_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('operation_aditn_backend/adding');
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
        $img_main = $this->img_upload->do_upload('operation_aditn_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_operation_aditn', array('operation_aditn_id' => $operation_aditn_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->operation_aditn_img) {
                $old_file_path = './docs/img/' . $old_document->operation_aditn_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['operation_aditn_img'] = $this->img_upload->data('file_name');
            $this->db->where('operation_aditn_id', $operation_aditn_id);
            $this->db->update('tbl_operation_aditn', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['operation_aditn_img_img']['name'][0])) {

            foreach ($_FILES['operation_aditn_img_img']['name'] as $index => $name) {
                $_FILES['operation_aditn_img_img_multiple']['name'] = $name;
                $_FILES['operation_aditn_img_img_multiple']['type'] = $_FILES['operation_aditn_img_img']['type'][$index];
                $_FILES['operation_aditn_img_img_multiple']['tmp_name'] = $_FILES['operation_aditn_img_img']['tmp_name'][$index];
                $_FILES['operation_aditn_img_img_multiple']['error'] = $_FILES['operation_aditn_img_img']['error'][$index];
                $_FILES['operation_aditn_img_img_multiple']['size'] = $_FILES['operation_aditn_img_img']['size'][$index];

                if ($this->img_upload->do_upload('operation_aditn_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'operation_aditn_img_ref_id' => $operation_aditn_id,
                        'operation_aditn_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_operation_aditn_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['operation_aditn_pdf_pdf']['name'][0])) {
            foreach ($_FILES['operation_aditn_pdf_pdf']['name'] as $index => $name) {
                $_FILES['operation_aditn_pdf_pdf_multiple']['name'] = $name;
                $_FILES['operation_aditn_pdf_pdf_multiple']['type'] = $_FILES['operation_aditn_pdf_pdf']['type'][$index];
                $_FILES['operation_aditn_pdf_pdf_multiple']['tmp_name'] = $_FILES['operation_aditn_pdf_pdf']['tmp_name'][$index];
                $_FILES['operation_aditn_pdf_pdf_multiple']['error'] = $_FILES['operation_aditn_pdf_pdf']['error'][$index];
                $_FILES['operation_aditn_pdf_pdf_multiple']['size'] = $_FILES['operation_aditn_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('operation_aditn_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'operation_aditn_pdf_ref_id' => $operation_aditn_id,
                        'operation_aditn_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_operation_aditn_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
        if (!empty($_FILES['operation_aditn_file_doc']['name'][0])) {
            foreach ($_FILES['operation_aditn_file_doc']['name'] as $index => $name) {
                $_FILES['operation_aditn_file_doc_multiple']['name'] = $name;
                $_FILES['operation_aditn_file_doc_multiple']['type'] = $_FILES['operation_aditn_file_doc']['type'][$index];
                $_FILES['operation_aditn_file_doc_multiple']['tmp_name'] = $_FILES['operation_aditn_file_doc']['tmp_name'][$index];
                $_FILES['operation_aditn_file_doc_multiple']['error'] = $_FILES['operation_aditn_file_doc']['error'][$index];
                $_FILES['operation_aditn_file_doc_multiple']['size'] = $_FILES['operation_aditn_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('operation_aditn_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'operation_aditn_file_ref_id' => $operation_aditn_id,
                        'operation_aditn_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_operation_aditn_file', $doc_data);
        }
        $this->space_model->update_server_current();
		// === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->operation_aditn_name != $data['operation_aditn_name']) {
                $changes['operation_aditn_name'] = array(
                    'old' => $old_data->operation_aditn_name,
                    'new' => $data['operation_aditn_name']
                );
            }
            if ($old_data->operation_aditn_detail != $data['operation_aditn_detail']) {
                $changes['operation_aditn_detail'] = array(
                    'old' => 'มีการเปลี่ยนแปลง',
                    'new' => 'รายละเอียดใหม่'
                );
            }
            if ($old_data->operation_aditn_date != $data['operation_aditn_date']) {
                $changes['operation_aditn_date'] = array(
                    'old' => $old_data->operation_aditn_date,
                    'new' => $data['operation_aditn_date']
                );
            }
            if ($old_data->operation_aditn_link != $data['operation_aditn_link']) {
                $changes['operation_aditn_link'] = array(
                    'old' => $old_data->operation_aditn_link,
                    'new' => $data['operation_aditn_link']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'operation_aditn_img_img');
        }
        if (!empty($pdf_data)) {
            $files_info['pdfs_added'] = count($pdf_data);
            $files_info['pdf_names'] = array_column($pdf_data, 'operation_aditn_pdf_pdf');
        }
        if (!empty($doc_data)) {
            $files_info['docs_added'] = count($doc_data);
            $files_info['doc_names'] = array_column($doc_data, 'operation_aditn_file_doc');
        }

        // บันทึก log การแก้ไขข่าว
        $this->log_model->add_log(
            'แก้ไข',
            'ตรวจสอบภายใน',
            $data['operation_aditn_name'],
            $operation_aditn_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data) + count($pdf_data) + count($doc_data)
            )
        );
        // =======================================================================
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_operation_aditn($operation_aditn_id)
    {
        $old_document = $this->db->get_where('tbl_operation_aditn', array('operation_aditn_id' => $operation_aditn_id))->row();

        $old_file_path = './docs/img/' . $old_document->operation_aditn_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        $this->db->delete('tbl_operation_aditn', array('operation_aditn_id' => $operation_aditn_id));
        $this->space_model->update_server_current();
		// บันทึก log การลบ =================================================
        if ($old_document) {
            $this->log_model->add_log(
                'ลบ',
                'ตรวจสอบภายใน',
                $old_document->operation_aditn_name,
                $operation_aditn_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // =======================================================================
    }

    public function del_operation_aditn_pdf($operation_aditn_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_operation_aditn_pdf', array('operation_aditn_pdf_ref_id' => $operation_aditn_id))->result();

        // ลบ pdf จากตาราง tbl_operation_aditn_pdf
        $this->db->where('operation_aditn_pdf_ref_id', $operation_aditn_id);
        $this->db->delete('tbl_operation_aditn_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->operation_aditn_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_operation_aditn_doc($operation_aditn_id)
    {
        // ดึงข้อมูลรายการ doc ก่อน
        $files = $this->db->get_where('tbl_operation_aditn_file', array('operation_aditn_file_ref_id' => $operation_aditn_id))->result();

        // ลบ doc จากตาราง tbl_operation_aditn_file
        $this->db->where('operation_aditn_file_ref_id', $operation_aditn_id);
        $this->db->delete('tbl_operation_aditn_file');

        // ลบไฟล์ doc ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->operation_aditn_file_doc;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_operation_aditn_img($operation_aditn_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_operation_aditn_img', array('operation_aditn_img_ref_id' => $operation_aditn_id))->result();

        // ลบรูปภาพจากตาราง tbl_operation_aditn_file
        $this->db->where('operation_aditn_img_ref_id', $operation_aditn_id);
        $this->db->delete('tbl_operation_aditn_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->operation_aditn_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function update_operation_aditn_status()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $operation_aditnId = $this->input->post('operation_aditn_id'); // รับค่า operation_aditn_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_operation_aditn ในฐานข้อมูลของคุณ
            $data = array(
                'operation_aditn_status' => $newStatus
            );
            $this->db->where('operation_aditn_id', $operation_aditnId); // ระบุ operation_aditn_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_operation_aditn', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function operation_aditn_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_operation_aditn');
        $this->db->where('tbl_operation_aditn.operation_aditn_status', 'show');
        $this->db->limit(8);
        $this->db->order_by('tbl_operation_aditn.operation_aditn_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function operation_aditn_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_operation_aditn');
        $this->db->where('tbl_operation_aditn.operation_aditn_status', 'show');
        $this->db->order_by('tbl_operation_aditn.operation_aditn_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view($operation_aditn_id)
    {
        $this->db->where('operation_aditn_id', $operation_aditn_id);
        $this->db->set('operation_aditn_view', 'operation_aditn_view + 1', false); // บวกค่า operation_aditn_view ทีละ 1
        $this->db->update('tbl_operation_aditn');
    }
    // ใน operation_aditn_model
    public function increment_download_operation_aditn($operation_aditn_file_id)
    {
        $this->db->where('operation_aditn_file_id', $operation_aditn_file_id);
        $this->db->set('operation_aditn_file_download', 'operation_aditn_file_download + 1', false); // บวกค่า operation_aditn_download ทีละ 1
        $this->db->update('tbl_operation_aditn_file');
    }
}
