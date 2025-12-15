<?php
class Pbsv_utilities_model extends CI_Model
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

        // กำหนดค่าใน $pbsv_utilities_data
        $pbsv_utilities_data = array(
            'pbsv_utilities_name' => $this->input->post('pbsv_utilities_name'),
            'pbsv_utilities_detail' => $this->input->post('pbsv_utilities_detail'),
            'pbsv_utilities_date' => $this->input->post('pbsv_utilities_date'),
            'pbsv_utilities_link' => $this->input->post('pbsv_utilities_link'),
            'pbsv_utilities_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        // ทำการอัปโหลดรูปภาพ
        $img_main = $this->img_upload->do_upload('pbsv_utilities_img');
        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            // ถ้ามีการอัปโหลดรูปภาพ
            $pbsv_utilities_data['pbsv_utilities_img'] = $this->img_upload->data('file_name');
        }
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $this->db->insert('tbl_pbsv_utilities', $pbsv_utilities_data);
        $pbsv_utilities_id = $this->db->insert_id();

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['pbsv_utilities_img_img'])) {
            foreach ($_FILES['pbsv_utilities_img_img']['name'] as $index => $name) {
                if (isset($_FILES['pbsv_utilities_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['pbsv_utilities_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['pbsv_utilities_pdf_pdf'])) {
            foreach ($_FILES['pbsv_utilities_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['pbsv_utilities_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['pbsv_utilities_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['pbsv_utilities_file_doc'])) {
            foreach ($_FILES['pbsv_utilities_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['pbsv_utilities_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['pbsv_utilities_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('pbsv_utilities_backend/adding');
            return;
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['pbsv_utilities_img_img']['name'][0])) {
            foreach ($_FILES['pbsv_utilities_img_img']['name'] as $index => $name) {
                $_FILES['pbsv_utilities_img_img_multiple']['name'] = $name;
                $_FILES['pbsv_utilities_img_img_multiple']['type'] = $_FILES['pbsv_utilities_img_img']['type'][$index];
                $_FILES['pbsv_utilities_img_img_multiple']['tmp_name'] = $_FILES['pbsv_utilities_img_img']['tmp_name'][$index];
                $_FILES['pbsv_utilities_img_img_multiple']['error'] = $_FILES['pbsv_utilities_img_img']['error'][$index];
                $_FILES['pbsv_utilities_img_img_multiple']['size'] = $_FILES['pbsv_utilities_img_img']['size'][$index];

                if ($this->img_upload->do_upload('pbsv_utilities_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'pbsv_utilities_img_ref_id' => $pbsv_utilities_id,
                        'pbsv_utilities_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_pbsv_utilities_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์PDFเพิ่มเติมหรือไม่
        if (!empty($_FILES['pbsv_utilities_pdf_pdf']['name'][0])) {
            foreach ($_FILES['pbsv_utilities_pdf_pdf']['name'] as $index => $name) {
                $_FILES['pbsv_utilities_pdf_pdf_multiple']['name'] = $name;
                $_FILES['pbsv_utilities_pdf_pdf_multiple']['type'] = $_FILES['pbsv_utilities_pdf_pdf']['type'][$index];
                $_FILES['pbsv_utilities_pdf_pdf_multiple']['tmp_name'] = $_FILES['pbsv_utilities_pdf_pdf']['tmp_name'][$index];
                $_FILES['pbsv_utilities_pdf_pdf_multiple']['error'] = $_FILES['pbsv_utilities_pdf_pdf']['error'][$index];
                $_FILES['pbsv_utilities_pdf_pdf_multiple']['size'] = $_FILES['pbsv_utilities_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('pbsv_utilities_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'pbsv_utilities_pdf_ref_id' => $pbsv_utilities_id,
                        'pbsv_utilities_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_pbsv_utilities_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลดไฟล์Docเพิ่มเติมหรือไม่
        if (!empty($_FILES['pbsv_utilities_file_doc']['name'][0])) {
            foreach ($_FILES['pbsv_utilities_file_doc']['name'] as $index => $name) {
                $_FILES['pbsv_utilities_file_doc_multiple']['name'] = $name;
                $_FILES['pbsv_utilities_file_doc_multiple']['type'] = $_FILES['pbsv_utilities_file_doc']['type'][$index];
                $_FILES['pbsv_utilities_file_doc_multiple']['tmp_name'] = $_FILES['pbsv_utilities_file_doc']['tmp_name'][$index];
                $_FILES['pbsv_utilities_file_doc_multiple']['error'] = $_FILES['pbsv_utilities_file_doc']['error'][$index];
                $_FILES['pbsv_utilities_file_doc_multiple']['size'] = $_FILES['pbsv_utilities_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('pbsv_utilities_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'pbsv_utilities_file_ref_id' => $pbsv_utilities_id,
                        'pbsv_utilities_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_pbsv_utilities_file', $doc_data);
        }
        $this->space_model->update_server_current();
		// บันทึก log การเพิ่มข้อมูล =================================================
        $this->log_model->add_log(
            'เพิ่ม',
            'สาธารณูปโภค',
            $pbsv_utilities_data['pbsv_utilities_name'],
            $pbsv_utilities_id,
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
        $this->db->from('tbl_pbsv_utilities');
        $this->db->group_by('tbl_pbsv_utilities.pbsv_utilities_id');
        $this->db->order_by('tbl_pbsv_utilities.pbsv_utilities_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_all_pdf($pbsv_utilities_id)
    {
        $this->db->select('pbsv_utilities_pdf_pdf');
        $this->db->from('tbl_pbsv_utilities_pdf');
        $this->db->where('pbsv_utilities_pdf_ref_id', $pbsv_utilities_id);
        return $this->db->get()->result();
    }
    public function list_all_doc($pbsv_utilities_id)
    {
        $this->db->select('pbsv_utilities_file_doc');
        $this->db->from('tbl_pbsv_utilities_file');
        $this->db->where('pbsv_utilities_file_ref_id', $pbsv_utilities_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read($pbsv_utilities_id)
    {
        $this->db->where('pbsv_utilities_id', $pbsv_utilities_id);
        $query = $this->db->get('tbl_pbsv_utilities');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($pbsv_utilities_id)
    {
        $this->db->where('pbsv_utilities_pdf_ref_id', $pbsv_utilities_id);
        $this->db->order_by('pbsv_utilities_pdf_id', 'DESC');
        $query = $this->db->get('tbl_pbsv_utilities_pdf');
        return $query->result();
    }
    public function read_doc($pbsv_utilities_id)
    {
        $this->db->where('pbsv_utilities_file_ref_id', $pbsv_utilities_id);
        $this->db->order_by('pbsv_utilities_file_id', 'DESC');
        $query = $this->db->get('tbl_pbsv_utilities_file');
        return $query->result();
    }

    public function read_img($pbsv_utilities_id)
    {
        $this->db->where('pbsv_utilities_img_ref_id', $pbsv_utilities_id);
        $this->db->order_by('pbsv_utilities_img_id', 'DESC');
        $query = $this->db->get('tbl_pbsv_utilities_img');
        return $query->result();
    }

// แก้ไข del_pdf function
public function del_pdf($pdf_id)
{
    // ดึงข้อมูลไฟล์และ ref_id สำหรับ logging
    $this->db->select('pbsv_utilities_pdf_pdf, pbsv_utilities_pdf_ref_id');
    $this->db->where('pbsv_utilities_pdf_id', $pdf_id);
    $query = $this->db->get('tbl_pbsv_utilities_pdf');
    $file_data = $query->row();

    // ดึงข้อมูลเอกสารหลักสำหรับ log (สมมติว่ามี function read หรือ get_by_id)
    $pbsv_utilities_data = null;
    if ($file_data && $file_data->pbsv_utilities_pdf_ref_id) {
        $pbsv_utilities_data = $this->db->get_where('tbl_pbsv_utilities', array('pbsv_utilities_id' => $file_data->pbsv_utilities_pdf_ref_id))->row();
    }

    // ลบไฟล์จากแหล่งที่เก็บไฟล์
    $file_path = './docs/file/' . $file_data->pbsv_utilities_pdf_pdf;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // ลบข้อมูลของไฟล์จากฐานข้อมูล
    $this->db->where('pbsv_utilities_pdf_id', $pdf_id);
    $this->db->delete('tbl_pbsv_utilities_pdf');

    // บันทึก log การลบไฟล์ PDF ============================================
    $this->log_model->add_log(
        'ลบ',
        'สาธารณูปโภค', // ปรับชื่อเมนูตามที่ใช้จริง
        'ไฟล์ PDF: ' . $file_data->pbsv_utilities_pdf_pdf . ' จากเอกสาร: ' . ($pbsv_utilities_data ? $pbsv_utilities_data->pbsv_utilities_name : 'ไม่ระบุ'),
        $file_data->pbsv_utilities_pdf_ref_id,
        array(
            'file_type' => 'PDF', 
            'file_name' => $file_data->pbsv_utilities_pdf_pdf,
            'pdf_id' => $pdf_id
        )
    );
    // ==================================================================

    $this->space_model->update_server_current();
    $this->session->set_flashdata('del_success', TRUE);
}

// แก้ไข del_doc function
public function del_doc($doc_id)
{
    // ดึงข้อมูลไฟล์และ ref_id สำหรับ logging
    $this->db->select('pbsv_utilities_file_doc, pbsv_utilities_file_ref_id');
    $this->db->where('pbsv_utilities_file_id', $doc_id);
    $query = $this->db->get('tbl_pbsv_utilities_file');
    $file_data = $query->row();

    // ดึงข้อมูลเอกสารหลักสำหรับ log
    $pbsv_utilities_data = null;
    if ($file_data && $file_data->pbsv_utilities_file_ref_id) {
        $pbsv_utilities_data = $this->db->get_where('tbl_pbsv_utilities', array('pbsv_utilities_id' => $file_data->pbsv_utilities_file_ref_id))->row();
    }

    // ลบไฟล์จากแหล่งที่เก็บไฟล์
    $file_path = './docs/file/' . $file_data->pbsv_utilities_file_doc;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // ลบข้อมูลของไฟล์จากฐานข้อมูล
    $this->db->where('pbsv_utilities_file_id', $doc_id);
    $this->db->delete('tbl_pbsv_utilities_file');

    // บันทึก log การลบไฟล์ DOC ==========================================
    $this->log_model->add_log(
        'ลบ',
        'สาธารณูปโภค', // ปรับชื่อเมนูตามที่ใช้จริง
        'ไฟล์ DOC: ' . $file_data->pbsv_utilities_file_doc . ' จากเอกสาร: ' . ($pbsv_utilities_data ? $pbsv_utilities_data->pbsv_utilities_name : 'ไม่ระบุ'),
        $file_data->pbsv_utilities_file_ref_id,
        array(
            'file_type' => 'DOC', 
            'file_name' => $file_data->pbsv_utilities_file_doc,
            'doc_id' => $doc_id
        )
    );
    // ==================================================================

    $this->space_model->update_server_current();
    $this->session->set_flashdata('del_success', TRUE);
}

// แก้ไข del_img function
public function del_img($file_id)
{
    // ดึงข้อมูลไฟล์และ ref_id สำหรับ logging
    $this->db->select('pbsv_utilities_img_img, pbsv_utilities_img_ref_id');
    $this->db->where('pbsv_utilities_img_id', $file_id);
    $query = $this->db->get('tbl_pbsv_utilities_img');
    $file_data = $query->row();

    // ดึงข้อมูลเอกสารหลักสำหรับ log
    $pbsv_utilities_data = null;
    if ($file_data && $file_data->pbsv_utilities_img_ref_id) {
        $pbsv_utilities_data = $this->db->get_where('tbl_pbsv_utilities', array('pbsv_utilities_id' => $file_data->pbsv_utilities_img_ref_id))->row();
    }

    // ลบไฟล์จากแหล่งที่เก็บไฟล์
    $file_path = './docs/img/' . $file_data->pbsv_utilities_img_img;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // บันทึก log การลบรูปภาพ ===============================================
    $this->log_model->add_log(
        'ลบ',
        'สาธารณูปโภค', // ปรับชื่อเมนูตามที่ใช้จริง
        'รูปภาพ: ' . $file_data->pbsv_utilities_img_img . ' จากเอกสาร: ' . ($pbsv_utilities_data ? $pbsv_utilities_data->pbsv_utilities_name : 'ไม่ระบุ'),
        $file_data->pbsv_utilities_img_ref_id,
        array(
            'file_type' => 'IMAGE', 
            'file_name' => $file_data->pbsv_utilities_img_img,
            'img_id' => $file_id
        )
    );
    // ==================================================================

    // ลบข้อมูลของไฟล์จากฐานข้อมูล
    $this->db->where('pbsv_utilities_img_id', $file_id);
    $this->db->delete('tbl_pbsv_utilities_img');
    $this->space_model->update_server_current();
    $this->session->set_flashdata('del_success', TRUE);
}


    public function edit($pbsv_utilities_id)
    {
		// ดึงข้อมูลเก่าก่อนแก้ไข
        $old_data = $this->read($pbsv_utilities_id);
		
        // Update pbsv_utilities information
        $data = array(
            'pbsv_utilities_name' => $this->input->post('pbsv_utilities_name'),
            'pbsv_utilities_detail' => $this->input->post('pbsv_utilities_detail'),
            'pbsv_utilities_date' => $this->input->post('pbsv_utilities_date'),
            'pbsv_utilities_link' => $this->input->post('pbsv_utilities_link'),
            'pbsv_utilities_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
        );

        $this->db->where('pbsv_utilities_id', $pbsv_utilities_id);
        $this->db->update('tbl_pbsv_utilities', $data);

        // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
        $used_space = $this->space_model->get_used_space();
        $upload_limit = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
        if (isset($_FILES['pbsv_utilities_img_img'])) {
            foreach ($_FILES['pbsv_utilities_img_img']['name'] as $index => $name) {
                if (isset($_FILES['pbsv_utilities_img_img']['size'][$index])) {
                    $total_space_required += $_FILES['pbsv_utilities_img_img']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
        if (isset($_FILES['pbsv_utilities_pdf_pdf'])) {
            foreach ($_FILES['pbsv_utilities_pdf_pdf']['name'] as $index => $name) {
                if (isset($_FILES['pbsv_utilities_pdf_pdf']['size'][$index])) {
                    $total_space_required += $_FILES['pbsv_utilities_pdf_pdf']['size'][$index];
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
        if (isset($_FILES['pbsv_utilities_file_doc'])) {
            foreach ($_FILES['pbsv_utilities_file_doc']['name'] as $index => $name) {
                if (isset($_FILES['pbsv_utilities_file_doc']['size'][$index])) {
                    $total_space_required += $_FILES['pbsv_utilities_file_doc']['size'][$index];
                }
            }
        }

        // เช็คค่าว่าง
        if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('pbsv_utilities_backend/adding');
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
        $img_main = $this->img_upload->do_upload('pbsv_utilities_img');

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if (!empty($img_main)) {
            $this->db->trans_start(); // เริ่ม Transaction

            // ดึงข้อมูลรูปเก่า
            $old_document = $this->db->get_where('tbl_pbsv_utilities', array('pbsv_utilities_id' => $pbsv_utilities_id))->row();

            // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
            if ($old_document && $old_document->pbsv_utilities_img) {
                $old_file_path = './docs/img/' . $old_document->pbsv_utilities_img;

                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // ลบไฟล์เก่า
                }
            }

            // ถ้ามีการอัปโหลดรูปภาพใหม่
            $img_data['pbsv_utilities_img'] = $this->img_upload->data('file_name');
            $this->db->where('pbsv_utilities_id', $pbsv_utilities_id);
            $this->db->update('tbl_pbsv_utilities', $img_data);

            $this->db->trans_complete(); // สิ้นสุด Transaction
        }

        $imgs_data = array();

        // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
        if (!empty($_FILES['pbsv_utilities_img_img']['name'][0])) {

            foreach ($_FILES['pbsv_utilities_img_img']['name'] as $index => $name) {
                $_FILES['pbsv_utilities_img_img_multiple']['name'] = $name;
                $_FILES['pbsv_utilities_img_img_multiple']['type'] = $_FILES['pbsv_utilities_img_img']['type'][$index];
                $_FILES['pbsv_utilities_img_img_multiple']['tmp_name'] = $_FILES['pbsv_utilities_img_img']['tmp_name'][$index];
                $_FILES['pbsv_utilities_img_img_multiple']['error'] = $_FILES['pbsv_utilities_img_img']['error'][$index];
                $_FILES['pbsv_utilities_img_img_multiple']['size'] = $_FILES['pbsv_utilities_img_img']['size'][$index];

                if ($this->img_upload->do_upload('pbsv_utilities_img_img_multiple')) {
                    $upload_data = $this->img_upload->data();
                    $imgs_data[] = array(
                        'pbsv_utilities_img_ref_id' => $pbsv_utilities_id,
                        'pbsv_utilities_img_img' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_pbsv_utilities_img', $imgs_data);
        }

        $pdf_data = array();

        // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
        if (!empty($_FILES['pbsv_utilities_pdf_pdf']['name'][0])) {
            foreach ($_FILES['pbsv_utilities_pdf_pdf']['name'] as $index => $name) {
                $_FILES['pbsv_utilities_pdf_pdf_multiple']['name'] = $name;
                $_FILES['pbsv_utilities_pdf_pdf_multiple']['type'] = $_FILES['pbsv_utilities_pdf_pdf']['type'][$index];
                $_FILES['pbsv_utilities_pdf_pdf_multiple']['tmp_name'] = $_FILES['pbsv_utilities_pdf_pdf']['tmp_name'][$index];
                $_FILES['pbsv_utilities_pdf_pdf_multiple']['error'] = $_FILES['pbsv_utilities_pdf_pdf']['error'][$index];
                $_FILES['pbsv_utilities_pdf_pdf_multiple']['size'] = $_FILES['pbsv_utilities_pdf_pdf']['size'][$index];

                if ($this->pdf_upload->do_upload('pbsv_utilities_pdf_pdf_multiple')) {
                    $upload_data = $this->pdf_upload->data();
                    $pdf_data[] = array(
                        'pbsv_utilities_pdf_ref_id' => $pbsv_utilities_id,
                        'pbsv_utilities_pdf_pdf' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_pbsv_utilities_pdf', $pdf_data);
        }

        $doc_data = array();

        // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
        if (!empty($_FILES['pbsv_utilities_file_doc']['name'][0])) {
            foreach ($_FILES['pbsv_utilities_file_doc']['name'] as $index => $name) {
                $_FILES['pbsv_utilities_file_doc_multiple']['name'] = $name;
                $_FILES['pbsv_utilities_file_doc_multiple']['type'] = $_FILES['pbsv_utilities_file_doc']['type'][$index];
                $_FILES['pbsv_utilities_file_doc_multiple']['tmp_name'] = $_FILES['pbsv_utilities_file_doc']['tmp_name'][$index];
                $_FILES['pbsv_utilities_file_doc_multiple']['error'] = $_FILES['pbsv_utilities_file_doc']['error'][$index];
                $_FILES['pbsv_utilities_file_doc_multiple']['size'] = $_FILES['pbsv_utilities_file_doc']['size'][$index];

                if ($this->doc_upload->do_upload('pbsv_utilities_file_doc_multiple')) {
                    $upload_data = $this->doc_upload->data();
                    $doc_data[] = array(
                        'pbsv_utilities_file_ref_id' => $pbsv_utilities_id,
                        'pbsv_utilities_file_doc' => $upload_data['file_name']
                    );
                }
            }
            $this->db->insert_batch('tbl_pbsv_utilities_file', $doc_data);
        }
        $this->space_model->update_server_current();
				// === เก็บ log แก้ไขข้อมูล ==============================================
        $changes = array();
        if ($old_data) {
            if ($old_data->pbsv_utilities_name != $data['pbsv_utilities_name']) {
                $changes['pbsv_utilities_name'] = array(
                    'old' => $old_data->pbsv_utilities_name,
                    'new' => $data['pbsv_utilities_name']
                );
            }
            if ($old_data->pbsv_utilities_detail != $data['pbsv_utilities_detail']) {
                $changes['pbsv_utilities_detail'] = array(
                    'old' => 'มีการเปลี่ยนแปลง',
                    'new' => 'รายละเอียดใหม่'
                );
            }
            if ($old_data->pbsv_utilities_date != $data['pbsv_utilities_date']) {
                $changes['pbsv_utilities_date'] = array(
                    'old' => $old_data->pbsv_utilities_date,
                    'new' => $data['pbsv_utilities_date']
                );
            }
            if ($old_data->pbsv_utilities_link != $data['pbsv_utilities_link']) {
                $changes['pbsv_utilities_link'] = array(
                    'old' => $old_data->pbsv_utilities_link,
                    'new' => $data['pbsv_utilities_link']
                );
            }
        }

        // เพิ่มข้อมูลไฟล์ที่อัพโหลด
        $files_info = array();
        if (!empty($imgs_data)) {
            $files_info['images_added'] = count($imgs_data);
            $files_info['image_names'] = array_column($imgs_data, 'pbsv_utilities_img_img');
        }
        if (!empty($pdf_data)) {
            $files_info['pdfs_added'] = count($pdf_data);
            $files_info['pdf_names'] = array_column($pdf_data, 'pbsv_utilities_pdf_pdf');
        }
        if (!empty($doc_data)) {
            $files_info['docs_added'] = count($doc_data);
            $files_info['doc_names'] = array_column($doc_data, 'pbsv_utilities_file_doc');
        }

        // บันทึก log การแก้ไข
        $this->log_model->add_log(
            'แก้ไข',
            'สาธารณูปโภค',
            $data['pbsv_utilities_name'],
            $pbsv_utilities_id,
            array(
                'changes' => $changes,
                'files_uploaded' => $files_info,
                'total_files_added' => count($imgs_data) + count($pdf_data) + count($doc_data)
            )
        );
        // =======================================================================
        $this->session->set_flashdata('save_success', TRUE);
    }

    public function del_pbsv_utilities($pbsv_utilities_id)
{
    // ดึงข้อมูลก่อนลบสำหรับ logging
    $pbsv_utilities_data = $this->db->get_where('tbl_pbsv_utilities', array('pbsv_utilities_id' => $pbsv_utilities_id))->row();

    // ลบรูปภาพหลัก
    if ($pbsv_utilities_data && $pbsv_utilities_data->pbsv_utilities_img) {
        $old_file_path = './docs/img/' . $pbsv_utilities_data->pbsv_utilities_img;
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }
    }

    // ลบข้อมูลจากฐานข้อมูล
    $this->db->delete('tbl_pbsv_utilities', array('pbsv_utilities_id' => $pbsv_utilities_id));

    // บันทึก log การลบเอกสารรายได้แผนดิน ===============================
    if ($pbsv_utilities_data) {
        $this->log_model->add_log(
            'ลบ',
            'สาธารณูปโภค', // ปรับชื่อเมนูตามที่ใช้จริง
            $pbsv_utilities_data->pbsv_utilities_name ?? 'เอกสารรายได้แผนดิน',
            $pbsv_utilities_id,
            array(
                'deleted_date' => date('Y-m-d H:i:s'),
                'main_image' => $pbsv_utilities_data->pbsv_utilities_img ?? null,
                'document_type' => 'pbsv_utilities_main'
            )
        );
    }
    // ====================================================================

    $this->space_model->update_server_current();
}

    public function del_pbsv_utilities_pdf($pbsv_utilities_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_pbsv_utilities_pdf', array('pbsv_utilities_pdf_ref_id' => $pbsv_utilities_id))->result();

        // ลบ pdf จากตาราง tbl_pbsv_utilities_pdf
        $this->db->where('pbsv_utilities_pdf_ref_id', $pbsv_utilities_id);
        $this->db->delete('tbl_pbsv_utilities_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->pbsv_utilities_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_pbsv_utilities_doc($pbsv_utilities_id)
    {
        // ดึงข้อมูลรายการ doc ก่อน
        $files = $this->db->get_where('tbl_pbsv_utilities_file', array('pbsv_utilities_file_ref_id' => $pbsv_utilities_id))->result();

        // ลบ doc จากตาราง tbl_pbsv_utilities_file
        $this->db->where('pbsv_utilities_file_ref_id', $pbsv_utilities_id);
        $this->db->delete('tbl_pbsv_utilities_file');

        // ลบไฟล์ doc ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->pbsv_utilities_file_doc;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_pbsv_utilities_img($pbsv_utilities_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_pbsv_utilities_img', array('pbsv_utilities_img_ref_id' => $pbsv_utilities_id))->result();

        // ลบรูปภาพจากตาราง tbl_pbsv_utilities_file
        $this->db->where('pbsv_utilities_img_ref_id', $pbsv_utilities_id);
        $this->db->delete('tbl_pbsv_utilities_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->pbsv_utilities_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function update_pbsv_utilities_status()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $pbsv_utilitiesId = $this->input->post('pbsv_utilities_id'); // รับค่า pbsv_utilities_id
            $newStatus = $this->input->post('new_status'); // รับค่าใหม่จาก switch checkbox

            // ทำการอัพเดตค่าในตาราง tbl_pbsv_utilities ในฐานข้อมูลของคุณ
            $data = array(
                'pbsv_utilities_status' => $newStatus
            );
            $this->db->where('pbsv_utilities_id', $pbsv_utilitiesId); // ระบุ pbsv_utilities_id ของแถวที่ต้องการอัพเดต
            $this->db->update('tbl_pbsv_utilities', $data);

            // ส่งการตอบกลับ (response) กลับไปยังเว็บไซต์หรือแอพพลิเคชันของคุณ
            // โดยเช่นปกติคุณอาจส่ง JSON response กลับมาเพื่ออัพเดตหน้าเว็บ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            // ถ้าไม่มีข้อมูล POST ส่งมา ให้รีเดอร์เปรียบเสมอ
            show_404();
        }
    }

    public function pbsv_utilities_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_pbsv_utilities');
        $this->db->where('tbl_pbsv_utilities.pbsv_utilities_status', 'show');
        $this->db->limit(8);
        $this->db->order_by('tbl_pbsv_utilities.pbsv_utilities_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function pbsv_utilities_frontend_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_pbsv_utilities');
        $this->db->where('tbl_pbsv_utilities.pbsv_utilities_status', 'show');
        $this->db->order_by('tbl_pbsv_utilities.pbsv_utilities_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view($pbsv_utilities_id)
    {
        $this->db->where('pbsv_utilities_id', $pbsv_utilities_id);
        $this->db->set('pbsv_utilities_view', 'pbsv_utilities_view + 1', false); // บวกค่า pbsv_utilities_view ทีละ 1
        $this->db->update('tbl_pbsv_utilities');
    }
    // ใน pbsv_utilities_model
    public function increment_download_pbsv_utilities($pbsv_utilities_file_id)
    {
        $this->db->where('pbsv_utilities_file_id', $pbsv_utilities_file_id);
        $this->db->set('pbsv_utilities_file_download', 'pbsv_utilities_file_download + 1', false); // บวกค่า pbsv_utilities_download ทีละ 1
        $this->db->update('tbl_pbsv_utilities_file');
    }
}
