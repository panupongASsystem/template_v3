<?php
class Operation_meeting_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        // log เก็บข้อมูล
        $this->load->model('log_model');
    }

    public function add_type()
    {

        $data = array(
            'operation_meeting_type_name' => $this->input->post('operation_meeting_type_name'),
            'operation_meeting_type_date' => $this->input->post('operation_meeting_type_date'),
            'operation_meeting_type_by' => $this->session->userdata('m_fname'),
        );

        $query = $this->db->insert('tbl_operation_meeting_type', $data);
        // บันทึก log การเพิ่มข้อมูล =================================================
        $operation_meeting_type_id = $this->db->insert_id();
        // =======================================================================

        $this->space_model->update_server_current();

        // บันทึก log การเพิ่มข้อมูล =================================================
        $this->log_model->add_log(
            'เพิ่ม',
            'รายงานการประชุมสภา',
            'หัวข้อ : ' . $data['operation_meeting_type_name'],
            $operation_meeting_type_id,
            array(
                'info' => array(
                    'operation_meeting_type_date' => $data['operation_meeting_type_date'],
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

    public function edit_type($operation_meeting_type_id)
    {
        // ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read_type($operation_meeting_type_id);

        $data = array(
            'operation_meeting_type_name' => $this->input->post('operation_meeting_type_name'),
            'operation_meeting_type_date' => $this->input->post('operation_meeting_type_date'),
            'operation_meeting_type_by' => $this->session->userdata('m_fname'),
        );

        $this->db->where('operation_meeting_type_id', $operation_meeting_type_id);
        $this->db->update('tbl_operation_meeting_type', $data);

        $this->space_model->update_server_current();

        if ($data) {
            // บันทึก log การแก้ไขหัวข้อ ========================================
            $changes = array();
            if ($old_data) {
                if ($old_data->operation_meeting_type_name != $data['operation_meeting_type_name']) {
                    $changes['operation_meeting_type_name'] = array(
                        'old' => $old_data->operation_meeting_type_name,
                        'new' => $data['operation_meeting_type_name']
                    );
                }
                if ($old_data->operation_meeting_type_date != $data['operation_meeting_type_date']) {
                    $changes['operation_meeting_type_date'] = array(
                        'old' => $old_data->operation_meeting_type_date,
                        'new' => $data['operation_meeting_type_date']
                    );
                }
            }

            $this->log_model->add_log(
                'แก้ไข',
                'รายงานการประชุมสภา',
                'หัวข้อ: ' . $data['operation_meeting_type_name'],
                $operation_meeting_type_id,
                array(
                    'changes' => $changes,
                    'total_changes' => count($changes)
                )
            );
            // =================================================================

            $this->session->set_flashdata('save_success', TRUE);
        } else {
            echo "<script>";
            echo "alert('Error !');";
            echo "</script>";
        }
    }

    public function list_type()
    {
        $this->db->select('*');
        $this->db->from('tbl_operation_meeting_type');
        $this->db->group_by('tbl_operation_meeting_type.operation_meeting_type_id');
        $this->db->order_by('tbl_operation_meeting_type.operation_meeting_type_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_operation_meeting_type()
    {
        $this->db->order_by('operation_meeting_type_id', 'DESC');
        $query = $this->db->get('tbl_operation_meeting_type');
        return $query->result();
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

        // กำหนดค่าใน $operation_meeting_data
        $operation_meeting_data = array(
            'operation_meeting_ref_id' => $this->input->post('operation_meeting_ref_id'),
            'operation_meeting_name' => $this->input->post('operation_meeting_name'),
            'operation_meeting_detail' => $this->input->post('operation_meeting_detail'),
            'operation_meeting_date' => $this->input->post('operation_meeting_date'),
            'operation_meeting_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('operation_meeting_img');
        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            // ถ้ามีการอัปโหลดรูปภาพ
            $operation_meeting_data['operation_meeting_img'] = $this->img_upload->data('file_name');
        }
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_operation_meeting', $operation_meeting_data);
        $operation_meeting_id = $this->db->insert_id();

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['operation_meeting_img_img'])) {
            foreach ($_FILES['operation_meeting_img_img']['name'] as $index => $name) {
                if (isset($_FILES['operation_meeting_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['operation_meeting_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['operation_meeting_pdf_pdf'])) {
            foreach ($_FILES['operation_meeting_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['operation_meeting_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['operation_meeting_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['operation_meeting_file_doc'])) {
            foreach ($_FILES['operation_meeting_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['operation_meeting_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['operation_meeting_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('operation_meeting_backend/adding');
            return;
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['operation_meeting_img_img']['name'][0])) {
            foreach ($_FILES['operation_meeting_img_img']['name'] as $index => $name) {
                $_FILES['operation_meeting_img_img_multiple']['name'] = $name;
                $_FILES['operation_meeting_img_img_multiple']['type'] = $_FILES['operation_meeting_img_img']['type'][$index];
                $_FILES['operation_meeting_img_img_multiple']['tmp_name'] = $_FILES['operation_meeting_img_img']['tmp_name'][$index];
                $_FILES['operation_meeting_img_img_multiple']['error'] = $_FILES['operation_meeting_img_img']['error'][$index];
                $_FILES['operation_meeting_img_img_multiple']['size'] = $_FILES['operation_meeting_img_img']['size'][$index];

                if ($this->img_upload->do_upload('operation_meeting_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'operation_meeting_img_ref_id' => $operation_meeting_id,
                        'operation_meeting_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_operation_meeting_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์PDFเพิ่มเติมหรือไม่
        if (!empty($_FILES['operation_meeting_pdf_pdf']['name'][0])) {
            foreach ($_FILES['operation_meeting_pdf_pdf']['name'] as $index => $name) {
                $_FILES['operation_meeting_pdf_pdf_multiple']['name'] = $name;
                $_FILES['operation_meeting_pdf_pdf_multiple']['type'] = $_FILES['operation_meeting_pdf_pdf']['type'][$index];
                $_FILES['operation_meeting_pdf_pdf_multiple']['tmp_name'] = $_FILES['operation_meeting_pdf_pdf']['tmp_name'][$index];
                $_FILES['operation_meeting_pdf_pdf_multiple']['error'] = $_FILES['operation_meeting_pdf_pdf']['error'][$index];
                $_FILES['operation_meeting_pdf_pdf_multiple']['size'] = $_FILES['operation_meeting_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('operation_meeting_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'operation_meeting_pdf_ref_id' => $operation_meeting_id,
                        'operation_meeting_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_operation_meeting_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์Docเพิ่มเติมหรือไม่
        if (!empty($_FILES['operation_meeting_file_doc']['name'][0])) {
            foreach ($_FILES['operation_meeting_file_doc']['name'] as $index => $name) {
                $_FILES['operation_meeting_file_doc_multiple']['name'] = $name;
                $_FILES['operation_meeting_file_doc_multiple']['type'] = $_FILES['operation_meeting_file_doc']['type'][$index];
                $_FILES['operation_meeting_file_doc_multiple']['tmp_name'] = $_FILES['operation_meeting_file_doc']['tmp_name'][$index];
                $_FILES['operation_meeting_file_doc_multiple']['error'] = $_FILES['operation_meeting_file_doc']['error'][$index];
                $_FILES['operation_meeting_file_doc_multiple']['size'] = $_FILES['operation_meeting_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('operation_meeting_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'operation_meeting_file_ref_id' => $operation_meeting_id,
                        'operation_meeting_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_operation_meeting_file', $doc_data);
        }
        $this->space_model->update_server_current();

        // บันทึก log การเพิ่มข้อมูล การเงิน ======================================
        $this->log_model->add_log(
            'เพิ่ม',
            'รายงานการประชุมสภา',
            $operation_meeting_data['operation_meeting_name'],
            $operation_meeting_id,
            array(
                'operation_meeting_ref_id' => $operation_meeting_data['operation_meeting_ref_id'],
                'files_uploaded' => array(
                    'images' => count($imgs_data),
                    'pdfs' => count($pdf_data),
                    'docs' => count($doc_data)
                )
            )
        );
        // ====================================================================

        $this->session->set_flashdata('save_success', TRUE);
    }

    public function list_all($operation_meeting_type_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_operation_meeting');
        $this->db->join('tbl_operation_meeting_type', 'tbl_operation_meeting.operation_meeting_ref_id = tbl_operation_meeting_type.operation_meeting_type_id', 'inner'); // เปลี่ยนเป็น INNER JOIN
        $this->db->where('tbl_operation_meeting_type.operation_meeting_type_id', $operation_meeting_type_id);
        $query = $this->db->get();
        return $query->result();
    }


    public function list_all_doc($operation_meeting_id)
    {
        $this->db->select('operation_meeting_file_doc');
        $this->db->from('tbl_operation_meeting_file');
        $this->db->where('operation_meeting_file_ref_id', $operation_meeting_id);
        return $this->db->get()->result();
    }
    public function list_all_pdf($operation_meeting_id)
    {
        $this->db->select('operation_meeting_pdf_pdf');
        $this->db->from('tbl_operation_meeting_pdf');
        $this->db->where('operation_meeting_pdf_ref_id', $operation_meeting_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read_type($operation_meeting_id)
    {
        $this->db->where('operation_meeting_type_id', $operation_meeting_id);
        $query = $this->db->get('tbl_operation_meeting_type');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read($operation_meeting_id)
    {
        $this->db->select('m.*,t.operation_meeting_type_name');
        $this->db->from('tbl_operation_meeting as m');
        $this->db->join('tbl_operation_meeting_type as t', 'm.operation_meeting_ref_id=t.operation_meeting_type_id');
        $this->db->where('m.operation_meeting_id', $operation_meeting_id);
        $query = $this->db->get('tbl_operation_meeting');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($operation_meeting_id)
    {
        $this->db->where('operation_meeting_pdf_ref_id', $operation_meeting_id);
        $this->db->order_by('operation_meeting_pdf_id', 'DESC');
        $query = $this->db->get('tbl_operation_meeting_pdf');
        return $query->result();
    }
    public function read_doc($operation_meeting_id)
    {
        $this->db->where('operation_meeting_file_ref_id', $operation_meeting_id);
        $this->db->order_by('operation_meeting_file_id', 'DESC');
        $query = $this->db->get('tbl_operation_meeting_file');
        return $query->result();
    }

    public function read_img($operation_meeting_id)
    {
        $this->db->where('operation_meeting_img_ref_id', $operation_meeting_id);
        $this->db->order_by('operation_meeting_img_id', 'DESC');
        $query = $this->db->get('tbl_operation_meeting_img');
        return $query->result();
    }

    public function del_pdf($pdf_id)
    {
        // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
        $this->db->select('operation_meeting_pdf_pdf, operation_meeting_pdf_ref_id');
        $this->db->where('operation_meeting_pdf_id', $pdf_id);
        $query = $this->db->get('tbl_operation_meeting_pdf');
        $file_data = $query->row();

        // ดึงชื่อข้อมูลการเงินสำหรับ log
        $operation_meeting_data = $this->read($file_data->operation_meeting_pdf_ref_id);

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->operation_meeting_pdf_pdf;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('operation_meeting_pdf_id', $pdf_id);
        $this->db->delete('tbl_operation_meeting_pdf');

        // บันทึก log การลบไฟล์ PDF ============================================
        $this->log_model->add_log(
            'ลบ',
            'รายงานการประชุมสภา',
            'ไฟล์ PDF: ' . $file_data->operation_meeting_pdf_pdf . ' จากเอกสาร: ' . ($operation_meeting_data ? $operation_meeting_data->operation_meeting_name : 'ไม่ระบุ'),
            $file_data->operation_meeting_pdf_ref_id,
            array('file_type' => 'PDF', 'file_name' => $file_data->operation_meeting_pdf_pdf)
        );
        // ==================================================================

        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_doc($doc_id)
    {
        // ดึงชื่อไฟล์ DOC จากฐานข้อมูลโดยใช้ $doc_id
        $this->db->select('operation_meeting_file_doc, operation_meeting_file_ref_id');
        $this->db->where('operation_meeting_file_id', $doc_id);
        $query = $this->db->get('tbl_operation_meeting_file');
        $file_data = $query->row();

        // ดึงชื่อข้อมูลการเงินสำหรับ log
        $operation_meeting_data = $this->read($file_data->operation_meeting_file_ref_id);

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/file/' . $file_data->operation_meeting_file_doc;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('operation_meeting_file_id', $doc_id);
        $this->db->delete('tbl_operation_meeting_file');

        // บันทึก log การลบไฟล์ DOC ==========================================
        $this->log_model->add_log(
            'ลบ',
            'รายงานการประชุมสภา',
            'ไฟล์ DOC: ' . $file_data->operation_meeting_file_doc . ' จากเอกสาร: ' . ($operation_meeting_data ? $operation_meeting_data->operation_meeting_name : 'ไม่ระบุ'),
            $file_data->operation_meeting_file_ref_id,
            array('file_type' => 'DOC', 'file_name' => $file_data->operation_meeting_file_doc)
        );
        // ==================================================================

        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_img($file_id)
    {
        // ดึงชื่อไฟล์ IMG จากฐานข้อมูลโดยใช้ $file_id
        $this->db->select('operation_meeting_img_img, operation_meeting_img_ref_id');
        $this->db->where('operation_meeting_img_id', $file_id);
        $query = $this->db->get('tbl_operation_meeting_img');
        $file_data = $query->row();

        // ดึงชื่อข้อมูลการเงินสำหรับ log
        $operation_meeting_data = $this->read($file_data->operation_meeting_img_ref_id);

        // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
        $file_path = './docs/img/' . $file_data->operation_meeting_img_img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // บันทึก log การลบรูปภาพ ===============================================
        $this->log_model->add_log(
            'ลบ',
            'รายงานการประชุมสภา',
            'รูปภาพ: ' . $file_data->operation_meeting_img_img . ' จากเอกสาร: ' . ($operation_meeting_data ? $operation_meeting_data->operation_meeting_name : 'ไม่ระบุ'),
            $file_data->operation_meeting_img_ref_id,
            array('file_type' => 'IMAGE', 'file_name' => $file_data->operation_meeting_img_img)
        );
        // ==================================================================

        // ลบข้อมูลของไฟล์จากฐานข้อมูล
        $this->db->where('operation_meeting_img_id', $file_id);
        $this->db->delete('tbl_operation_meeting_img');
        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function edit($operation_meeting_id)
    {
        // ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($operation_meeting_id);

        // Update operation_meeting information
        $data = array(
            'operation_meeting_ref_id' => $this->input->post('operation_meeting_ref_id'),
            'operation_meeting_name' => $this->input->post('operation_meeting_name'),
            'operation_meeting_detail' => $this->input->post('operation_meeting_detail'),
            'operation_meeting_date' => $this->input->post('operation_meeting_date'),
            'operation_meeting_link' => $this->input->post('operation_meeting_link'),
            'operation_meeting_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('operation_meeting_id', $operation_meeting_id);
        $this->db->update('tbl_operation_meeting', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['operation_meeting_img_img'])) {
            foreach ($_FILES['operation_meeting_img_img']['name'] as $index => $name) {
                if (isset($_FILES['operation_meeting_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['operation_meeting_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['operation_meeting_pdf_pdf'])) {
            foreach ($_FILES['operation_meeting_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['operation_meeting_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['operation_meeting_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['operation_meeting_file_doc'])) {
            foreach ($_FILES['operation_meeting_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['operation_meeting_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['operation_meeting_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('operation_meeting_backend/adding');
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
        $img_main = $this->img_upload->do_upload('operation_meeting_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_operation_meeting', array('operation_meeting_id' => $operation_meeting_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->operation_meeting_img) {
                $old_file_path = './docs/img/' . $old_document->operation_meeting_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['operation_meeting_img'] = $this->img_upload->data('file_name');
            $this->db->where('operation_meeting_id', $operation_meeting_id);
            $this->db->update('tbl_operation_meeting', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['operation_meeting_img_img']['name'][0])) {

            foreach ($_FILES['operation_meeting_img_img']['name'] as $index => $name) {
                $_FILES['operation_meeting_img_img_multiple']['name'] = $name;
                $_FILES['operation_meeting_img_img_multiple']['type'] = $_FILES['operation_meeting_img_img']['type'][$index];
                $_FILES['operation_meeting_img_img_multiple']['tmp_name'] = $_FILES['operation_meeting_img_img']['tmp_name'][$index];
                $_FILES['operation_meeting_img_img_multiple']['error'] = $_FILES['operation_meeting_img_img']['error'][$index];
                $_FILES['operation_meeting_img_img_multiple']['size'] = $_FILES['operation_meeting_img_img']['size'][$index];

                if ($this->img_upload->do_upload('operation_meeting_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'operation_meeting_img_ref_id' => $operation_meeting_id,
                        'operation_meeting_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_operation_meeting_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['operation_meeting_pdf_pdf']['name'][0])) {
            foreach ($_FILES['operation_meeting_pdf_pdf']['name'] as $index => $name) {
                $_FILES['operation_meeting_pdf_pdf_multiple']['name'] = $name;
                $_FILES['operation_meeting_pdf_pdf_multiple']['type'] = $_FILES['operation_meeting_pdf_pdf']['type'][$index];
                $_FILES['operation_meeting_pdf_pdf_multiple']['tmp_name'] = $_FILES['operation_meeting_pdf_pdf']['tmp_name'][$index];
                $_FILES['operation_meeting_pdf_pdf_multiple']['error'] = $_FILES['operation_meeting_pdf_pdf']['error'][$index];
                $_FILES['operation_meeting_pdf_pdf_multiple']['size'] = $_FILES['operation_meeting_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('operation_meeting_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'operation_meeting_pdf_ref_id' => $operation_meeting_id,
                        'operation_meeting_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_operation_meeting_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
        if (!empty($_FILES['operation_meeting_file_doc']['name'][0])) {
            foreach ($_FILES['operation_meeting_file_doc']['name'] as $index => $name) {
                $_FILES['operation_meeting_file_doc_multiple']['name'] = $name;
                $_FILES['operation_meeting_file_doc_multiple']['type'] = $_FILES['operation_meeting_file_doc']['type'][$index];
                $_FILES['operation_meeting_file_doc_multiple']['tmp_name'] = $_FILES['operation_meeting_file_doc']['tmp_name'][$index];
                $_FILES['operation_meeting_file_doc_multiple']['error'] = $_FILES['operation_meeting_file_doc']['error'][$index];
                $_FILES['operation_meeting_file_doc_multiple']['size'] = $_FILES['operation_meeting_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('operation_meeting_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'operation_meeting_file_ref_id' => $operation_meeting_id,
                        'operation_meeting_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_operation_meeting_file', $doc_data);
        }
        $this->space_model->update_server_current();

        // บันทึก log การแก้ไขข้อมูลการเงิน =====================================
        // เปรียบเทียบการเปลี่ยนแปลงข้อมูล
        $changes = array();
        if ($old_data) {
            if ($old_data->operation_meeting_ref_id != $data['operation_meeting_ref_id']) {
                $changes['operation_meeting_ref_id'] = array(
                    'old' => $old_data->operation_meeting_ref_id,
                    'new' => $data['operation_meeting_ref_id']
                );
            }
            if ($old_data->operation_meeting_name != $data['operation_meeting_name']) {
                $changes['operation_meeting_name'] = array(
                    'old' => $old_data->operation_meeting_name,
                    'new' => $data['operation_meeting_name']
                );
            }
            if ($old_data->operation_meeting_detail != $data['operation_meeting_detail']) {
                $changes['operation_meeting_detail'] = array(
                    'old' => 'มีการเปลี่ยนแปลง',
                    'new' => 'รายละเอียดใหม่'
                );
            }
            if ($old_data->operation_meeting_date != $data['operation_meeting_date']) {
                $changes['operation_meeting_date'] = array(
                    'old' => $old_data->operation_meeting_date,
                    'new' => $data['operation_meeting_date']
                );
            }
            if (isset($data['operation_meeting_link']) && $old_data->operation_meeting_link != $data['operation_meeting_link']) {
                $changes['operation_meeting_link'] = array(
                    'old' => $old_data->operation_meeting_link,
                    'new' => $data['operation_meeting_link']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'operation_meeting_img_img');
        }
        if (!empty($pdf_data)) {
            $files_info['pdfs_added'] = count($pdf_data);
            $files_info['pdf_names'] = array_column($pdf_data, 'operation_meeting_pdf_pdf');
        }
        if (!empty($doc_data)) {
            $files_info['docs_added'] = count($doc_data);
            $files_info['doc_names'] = array_column($doc_data, 'operation_meeting_file_doc');
        }

        // บันทึก log การแก้ไขข้อมูลการเงิน
        $this->log_model->add_log(
            'แก้ไข',
            'รายงานการประชุมสภา',
            $data['operation_meeting_name'],
            $operation_meeting_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data) + count($pdf_data) + count($doc_data)
            )
        );
        // ====================================================================

        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_operation_meeting($operation_meeting_id)
    {
        // ดึงข้อมูลก่อนลบ (ใช้ครั้งเดียวพอ)
        $operation_meeting_data = $this->read($operation_meeting_id);

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$operation_meeting_data) {
            return false; // หรือ throw exception
        }

        // ลบไฟล์รูปภาพ (ถ้ามี)
        if (!empty($operation_meeting_data->operation_meeting_img)) {
            $old_file_path = './docs/img/' . $operation_meeting_data->operation_meeting_img;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
        }

        // ลบข้อมูลจากฐานข้อมูล
        $this->db->where('operation_meeting_id', $operation_meeting_id);
        $delete_result = $this->db->delete('tbl_operation_meeting');

        // ตรวจสอบผลการลบ
        if ($delete_result) {
            // บันทึก log การลบข้อมูล
            $this->log_model->add_log(
                'ลบ',
                'รายงานการประชุมสภา',
                $operation_meeting_data->operation_meeting_name,
                $operation_meeting_id,
                array(
                    'deleted_date' => date('Y-m-d H:i:s'),
                    'file_deleted' => !empty($operation_meeting_data->operation_meeting_img) ? $operation_meeting_data->operation_meeting_img : null
                )
            );

            // อัพเดตพื้นที่เก็บข้อมูล
            $this->space_model->update_server_current();

            return true;
        }

        return false;
    }

    public function del_operation_meeting_pdf($operation_meeting_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_operation_meeting_pdf', array('operation_meeting_pdf_ref_id' => $operation_meeting_id))->result();

        // ลบ pdf จากตาราง tbl_operation_meeting_pdf
        $this->db->where('operation_meeting_pdf_ref_id', $operation_meeting_id);
        $this->db->delete('tbl_operation_meeting_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->operation_meeting_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_operation_meeting_doc($operation_meeting_id)
    {
        // ดึงข้อมูลรายการ doc ก่อน
        $files = $this->db->get_where('tbl_operation_meeting_file', array('operation_meeting_file_ref_id' => $operation_meeting_id))->result();

        // ลบ doc จากตาราง tbl_operation_meeting_file
        $this->db->where('operation_meeting_file_ref_id', $operation_meeting_id);
        $this->db->delete('tbl_operation_meeting_file');

        // ลบไฟล์ doc ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->operation_meeting_file_doc;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_operation_meeting_img($operation_meeting_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_operation_meeting_img', array('operation_meeting_img_ref_id' => $operation_meeting_id))->result();

        // ลบรูปภาพจากตาราง tbl_operation_meeting_file
        $this->db->where('operation_meeting_img_ref_id', $operation_meeting_id);
        $this->db->delete('tbl_operation_meeting_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->operation_meeting_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }


    public function del_type_operation_meeting_type($operation_meeting_type_id)
    {
        // ดึงข้อมูลหัวข้อก่อนลบ
        $type_data = $this->read_type($operation_meeting_type_id);

        $this->db->delete('tbl_operation_meeting_type', array('operation_meeting_type_id' => $operation_meeting_type_id));

        // บันทึก log การลบหัวข้อการเงิน ===================================
        if ($type_data) {
            $this->log_model->add_log(
                'ลบ',
                'รายงานการประชุมสภา',
                'ลบหัวข้อ: ' . $type_data->operation_meeting_type_name,
                $operation_meeting_type_id,
                array('deleted_date' => date('Y-m-d H:i:s'))
            );
        }
        // ================================================================

        $this->space_model->update_server_current();
    }
    public function del_type_operation_meeting($operation_meeting_type_id)
    {
        // ดึงข้อมูลทั้งหมดที่ต้องการลบ
        $documents = $this->db->get_where('tbl_operation_meeting', array('operation_meeting_ref_id' => $operation_meeting_type_id))->result();

        // ถ้ามีข้อมูล
        if ($documents) {
            foreach ($documents as $doc) {
                // ลบไฟล์รูปภาพ ถ้ามี
                if (!empty($doc->operation_meeting_img)) {
                    $old_file_path = './docs/img/' . $doc->operation_meeting_img;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
            }
        }

        // ลบข้อมูลจากฐานข้อมูล
        $this->db->delete('tbl_operation_meeting', array('operation_meeting_ref_id' => $operation_meeting_type_id));
        $this->space_model->update_server_current();
    }

    public function del_type_operation_meeting_pdf($operation_meeting_type_id)
    {
        $operation_meeting_ids = $this->get_operation_meeting_id($operation_meeting_type_id);

        if ($operation_meeting_ids) {
            // แปลง array เป็น string โดยคั่นด้วย ','
            $operation_meeting_ids_string = implode(',', $operation_meeting_ids);

            // สร้าง SQL query โดยใส่ ' รอบแต่ละค่า
            $sql = "SELECT * FROM `tbl_operation_meeting_pdf` WHERE operation_meeting_pdf_ref_id IN ($operation_meeting_ids_string)";

            // ดึงข้อมูลรายการรูปภาพที่ต้องการลบ
            $files = $this->db->query($sql)->result();

            // ลบรูปภาพจากตาราง tbl_operation_meeting_pdf
            $this->db->where_in('operation_meeting_pdf_ref_id', $operation_meeting_ids);
            $this->db->delete('tbl_operation_meeting_pdf');

            // ลบไฟล์รูปภาพที่เกี่ยวข้อง
            foreach ($files as $file) {
                $file_path = './docs/file/' . $file->operation_meeting_pdf_pdf;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
    }

    public function del_type_operation_meeting_doc($operation_meeting_type_id)
    {
        $operation_meeting_ids = $this->get_operation_meeting_id($operation_meeting_type_id);

        if ($operation_meeting_ids) {
            // แปลง array เป็น string โดยคั่นด้วย ','
            $operation_meeting_ids_string = implode(',', $operation_meeting_ids);

            // สร้าง SQL query โดยใส่ ' รอบแต่ละค่า
            $sql = "SELECT * FROM `tbl_operation_meeting_file` WHERE operation_meeting_file_ref_id IN ($operation_meeting_ids_string)";

            // ดึงข้อมูลรายการรูปภาพที่ต้องการลบ
            $files = $this->db->query($sql)->result();

            // ลบรูปภาพจากตาราง tbl_operation_meeting_file
            $this->db->where_in('operation_meeting_file_ref_id', $operation_meeting_ids);
            $this->db->delete('tbl_operation_meeting_file');

            // ลบไฟล์รูปภาพที่เกี่ยวข้อง
            foreach ($files as $file) {
                $file_path = './docs/file/' . $file->operation_meeting_file_doc;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
    }

    public function del_type_operation_meeting_img($operation_meeting_type_id)
    {
        $operation_meeting_ids = $this->get_operation_meeting_id($operation_meeting_type_id);

        if ($operation_meeting_ids) {
            // แปลง array เป็น string โดยคั่นด้วย ','
            $operation_meeting_ids_string = implode(',', $operation_meeting_ids);

            // สร้าง SQL query โดยใส่ ' รอบแต่ละค่า
            $sql = "SELECT * FROM `tbl_operation_meeting_img` WHERE operation_meeting_img_ref_id IN ($operation_meeting_ids_string)";

            // ดึงข้อมูลรายการรูปภาพที่ต้องการลบ
            $files = $this->db->query($sql)->result();

            // ลบรูปภาพจากตาราง tbl_operation_meeting_img
            $this->db->where_in('operation_meeting_img_ref_id', $operation_meeting_ids);
            $this->db->delete('tbl_operation_meeting_img');

            // ลบไฟล์รูปภาพที่เกี่ยวข้อง
            foreach ($files as $file) {
                $file_path = './docs/img/' . $file->operation_meeting_img_img;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
    }

    public function get_operation_meeting_id($operation_meeting_type_id)
    {
        $query = $this->db->select('operation_meeting_id')
            ->from('tbl_operation_meeting')
            ->where('operation_meeting_ref_id', $operation_meeting_type_id)
            ->get();

        if ($query->num_rows() > 0) {
            $result = array();
            foreach ($query->result() as $row) {
                $result[] = $row->operation_meeting_id;
            }
            return $result; // คืน Array ของ operation_meeting_id ทั้งหมด
        } else {
            return null; // หรือค่าที่เหมาะสมเมื่อไม่พบข้อมูล
        }
    }

    public function operation_meeting_topic_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_operation_meeting_type');
        $this->db->limit(8);
        $this->db->order_by('tbl_operation_meeting_type.operation_meeting_type_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function operation_meeting_frontend_list_topic()
    {
        $this->db->select('*');
        $this->db->from('tbl_operation_meeting_type');
        $this->db->order_by('tbl_operation_meeting_type.operation_meeting_type_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function operation_meeting_frontend_list($operation_meeting_type_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_operation_meeting');
        $this->db->join('tbl_operation_meeting_type', 'tbl_operation_meeting.operation_meeting_ref_id = tbl_operation_meeting_type.operation_meeting_type_id', 'inner'); // เปลี่ยนเป็น INNER JOIN
        $this->db->where('tbl_operation_meeting_type.operation_meeting_type_id', $operation_meeting_type_id);
        $this->db->where('tbl_operation_meeting.operation_meeting_status', 'show');
        $this->db->order_by('tbl_operation_meeting.operation_meeting_date', 'DESC'); // เพิ่มบรรทัดนี้
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view($operation_meeting_id)
    {
        $this->db->where('operation_meeting_id', $operation_meeting_id);
        $this->db->set('operation_meeting_view', 'operation_meeting_view + 1', false); // บวกค่า operation_meeting_view ทีละ 1
        $this->db->update('tbl_operation_meeting');
    }
    // ใน operation_meeting_model
    public function increment_download_operation_meeting($operation_meeting_file_id)
    {
        $this->db->where('operation_meeting_file_id', $operation_meeting_file_id);
        $this->db->set('operation_meeting_file_download', 'operation_meeting_file_download + 1', false); // บวกค่า operation_meeting_download ทีละ 1
        $this->db->update('tbl_operation_meeting_file');
    }
}
