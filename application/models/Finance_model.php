<?php
class Finance_model extends CI_Model
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
            'finance_type_name' => $this->input->post('finance_type_name'),
            'finance_type_date' => $this->input->post('finance_type_date'),
            'finance_type_by' => $this->session->userdata('m_fname'),
        );

        $query = $this->db->insert('tbl_finance_type', $data);
		// บันทึก log การเพิ่มข้อมูล =================================================
        $finance_type_id = $this->db->insert_id();
        // =======================================================================

        $this->space_model->update_server_current();
		
		     // บันทึก log การเพิ่มข้อมูล =================================================
            $this->log_model->add_log(
                'เพิ่ม',
                'งานการเงินและการบัญชี',
                'หัวข้อ : ' . $data['finance_type_name'],
                $finance_type_id,
                array(
                    'info' => array(
                        'finance_type_date' => $data['finance_type_date'],
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

public function edit_type($finance_type_id)
{
    // ดึงข้อมูลเก่าก่อนแก้ไข
    $old_data = $this->read_type($finance_type_id);

    $data = array(
        'finance_type_name' => $this->input->post('finance_type_name'),
        'finance_type_date' => $this->input->post('finance_type_date'),
        'finance_type_by' => $this->session->userdata('m_fname'),
    );

    $this->db->where('finance_type_id', $finance_type_id);
    $this->db->update('tbl_finance_type', $data);

    $this->space_model->update_server_current();

    if ($data) {
        // บันทึก log การแก้ไขหัวข้อ ========================================
        $changes = array();
        if ($old_data) {
            if ($old_data->finance_type_name != $data['finance_type_name']) {
                $changes['finance_type_name'] = array(
                    'old' => $old_data->finance_type_name,
                    'new' => $data['finance_type_name']
                );
            }
            if ($old_data->finance_type_date != $data['finance_type_date']) {
                $changes['finance_type_date'] = array(
                    'old' => $old_data->finance_type_date,
                    'new' => $data['finance_type_date']
                );
            }
        }

        $this->log_model->add_log(
            'แก้ไข',
            'งานการเงินและการบัญชี',
            'หัวข้อ: ' . $data['finance_type_name'],
            $finance_type_id,
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
        $this->db->from('tbl_finance_type');
        $this->db->group_by('tbl_finance_type.finance_type_id');
        $this->db->order_by('tbl_finance_type.finance_type_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function list_finance_type()
    {
        $this->db->order_by('finance_type_id', 'DESC');
        $query = $this->db->get('tbl_finance_type');
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

    // กำหนดค่าใน $finance_data
    $finance_data = array(
        'finance_ref_id' => $this->input->post('finance_ref_id'),
        'finance_name' => $this->input->post('finance_name'),
        'finance_detail' => $this->input->post('finance_detail'),
        'finance_date' => $this->input->post('finance_date'),
        'finance_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
    );

    // ทำการอัปโหลดรูปภาพ
    $img_main = $this->img_upload->do_upload('finance_img');
    // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
    if (!empty($img_main)) {
        // ถ้ามีการอัปโหลดรูปภาพ
        $finance_data['finance_img'] = $this->img_upload->data('file_name');
    }
    // เพิ่มข้อมูลลงในฐานข้อมูล
    $this->db->insert('tbl_finance', $finance_data);
    $finance_id = $this->db->insert_id();

    // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
    $used_space = $this->space_model->get_used_space();
    $upload_limit = $this->space_model->get_limit_storage();

    $total_space_required = 0;
    // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
    if (isset($_FILES['finance_img_img'])) {
        foreach ($_FILES['finance_img_img']['name'] as $index => $name) {
            if (isset($_FILES['finance_img_img']['size'][$index])) {
                $total_space_required += $_FILES['finance_img_img']['size'][$index];
            }
        }
    }

    // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
    if (isset($_FILES['finance_pdf_pdf'])) {
        foreach ($_FILES['finance_pdf_pdf']['name'] as $index => $name) {
            if (isset($_FILES['finance_pdf_pdf']['size'][$index])) {
                $total_space_required += $_FILES['finance_pdf_pdf']['size'][$index];
            }
        }
    }

    // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
    if (isset($_FILES['finance_file_doc'])) {
        foreach ($_FILES['finance_file_doc']['name'] as $index => $name) {
            if (isset($_FILES['finance_file_doc']['size'][$index])) {
                $total_space_required += $_FILES['finance_file_doc']['size'][$index];
            }
        }
    }

    // เช็คค่าว่าง
    if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
        $this->session->set_flashdata('save_error', TRUE);
        redirect('finance_backend/adding');
        return;
    }

    $imgs_data = array();

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
    if (!empty($_FILES['finance_img_img']['name'][0])) {
        foreach ($_FILES['finance_img_img']['name'] as $index => $name) {
            $_FILES['finance_img_img_multiple']['name'] = $name;
            $_FILES['finance_img_img_multiple']['type'] = $_FILES['finance_img_img']['type'][$index];
            $_FILES['finance_img_img_multiple']['tmp_name'] = $_FILES['finance_img_img']['tmp_name'][$index];
            $_FILES['finance_img_img_multiple']['error'] = $_FILES['finance_img_img']['error'][$index];
            $_FILES['finance_img_img_multiple']['size'] = $_FILES['finance_img_img']['size'][$index];

            if ($this->img_upload->do_upload('finance_img_img_multiple')) {
                $upload_data = $this->img_upload->data();
                $imgs_data[] = array(
                    'finance_img_ref_id' => $finance_id,
                    'finance_img_img' => $upload_data['file_name']
                );
            }
        }
        $this->db->insert_batch('tbl_finance_img', $imgs_data);
    }

    $pdf_data = array();

    // ตรวจสอบว่ามีการอัปโหลดไฟล์PDFเพิ่มเติมหรือไม่
    if (!empty($_FILES['finance_pdf_pdf']['name'][0])) {
        foreach ($_FILES['finance_pdf_pdf']['name'] as $index => $name) {
            $_FILES['finance_pdf_pdf_multiple']['name'] = $name;
            $_FILES['finance_pdf_pdf_multiple']['type'] = $_FILES['finance_pdf_pdf']['type'][$index];
            $_FILES['finance_pdf_pdf_multiple']['tmp_name'] = $_FILES['finance_pdf_pdf']['tmp_name'][$index];
            $_FILES['finance_pdf_pdf_multiple']['error'] = $_FILES['finance_pdf_pdf']['error'][$index];
            $_FILES['finance_pdf_pdf_multiple']['size'] = $_FILES['finance_pdf_pdf']['size'][$index];

            if ($this->pdf_upload->do_upload('finance_pdf_pdf_multiple')) {
                $upload_data = $this->pdf_upload->data();
                $pdf_data[] = array(
                    'finance_pdf_ref_id' => $finance_id,
                    'finance_pdf_pdf' => $upload_data['file_name']
                );
            }
        }
        $this->db->insert_batch('tbl_finance_pdf', $pdf_data);
    }

    $doc_data = array();

    // ตรวจสอบว่ามีการอัปโหลดไฟล์Docเพิ่มเติมหรือไม่
    if (!empty($_FILES['finance_file_doc']['name'][0])) {
        foreach ($_FILES['finance_file_doc']['name'] as $index => $name) {
            $_FILES['finance_file_doc_multiple']['name'] = $name;
            $_FILES['finance_file_doc_multiple']['type'] = $_FILES['finance_file_doc']['type'][$index];
            $_FILES['finance_file_doc_multiple']['tmp_name'] = $_FILES['finance_file_doc']['tmp_name'][$index];
            $_FILES['finance_file_doc_multiple']['error'] = $_FILES['finance_file_doc']['error'][$index];
            $_FILES['finance_file_doc_multiple']['size'] = $_FILES['finance_file_doc']['size'][$index];

            if ($this->doc_upload->do_upload('finance_file_doc_multiple')) {
                $upload_data = $this->doc_upload->data();
                $doc_data[] = array(
                    'finance_file_ref_id' => $finance_id,
                    'finance_file_doc' => $upload_data['file_name']
                );
            }
        }
        $this->db->insert_batch('tbl_finance_file', $doc_data);
    }
    $this->space_model->update_server_current();

    // บันทึก log การเพิ่มข้อมูล การเงิน ======================================
    $this->log_model->add_log(
        'เพิ่ม',
        'งานการเงินและการบัญชี',
        $finance_data['finance_name'],
        $finance_id,
        array(
            'finance_ref_id' => $finance_data['finance_ref_id'],
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

    public function list_all($finance_type_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_finance');
        $this->db->join('tbl_finance_type', 'tbl_finance.finance_ref_id = tbl_finance_type.finance_type_id', 'inner'); // เปลี่ยนเป็น INNER JOIN
        $this->db->where('tbl_finance_type.finance_type_id', $finance_type_id);
        $query = $this->db->get();
        return $query->result();
    }


    public function list_all_doc($finance_id)
    {
        $this->db->select('finance_file_doc');
        $this->db->from('tbl_finance_file');
        $this->db->where('finance_file_ref_id', $finance_id);
        return $this->db->get()->result();
    }
    public function list_all_pdf($finance_id)
    {
        $this->db->select('finance_pdf_pdf');
        $this->db->from('tbl_finance_pdf');
        $this->db->where('finance_pdf_ref_id', $finance_id);
        return $this->db->get()->result();
    }

    //show form edit
    public function read_type($finance_id)
    {
        $this->db->where('finance_type_id', $finance_id);
        $query = $this->db->get('tbl_finance_type');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read($finance_id)
    {
        $this->db->select('m.*,t.finance_type_name');
        $this->db->from('tbl_finance as m');
        $this->db->join('tbl_finance_type as t', 'm.finance_ref_id=t.finance_type_id');
        $this->db->where('m.finance_id', $finance_id);
        $query = $this->db->get('tbl_finance');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    public function read_pdf($finance_id)
    {
        $this->db->where('finance_pdf_ref_id', $finance_id);
        $this->db->order_by('finance_pdf_id', 'DESC');
        $query = $this->db->get('tbl_finance_pdf');
        return $query->result();
    }
    public function read_doc($finance_id)
    {
        $this->db->where('finance_file_ref_id', $finance_id);
        $this->db->order_by('finance_file_id', 'DESC');
        $query = $this->db->get('tbl_finance_file');
        return $query->result();
    }

    public function read_img($finance_id)
    {
        $this->db->where('finance_img_ref_id', $finance_id);
        $this->db->order_by('finance_img_id', 'DESC');
        $query = $this->db->get('tbl_finance_img');
        return $query->result();
    }

    public function del_pdf($pdf_id)
{
    // ดึงชื่อไฟล์ PDF จากฐานข้อมูลโดยใช้ $pdf_id
    $this->db->select('finance_pdf_pdf, finance_pdf_ref_id');
    $this->db->where('finance_pdf_id', $pdf_id);
    $query = $this->db->get('tbl_finance_pdf');
    $file_data = $query->row();

    // ดึงชื่อข้อมูลการเงินสำหรับ log
    $finance_data = $this->read($file_data->finance_pdf_ref_id);

    // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
    $file_path = './docs/file/' . $file_data->finance_pdf_pdf;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // ลบข้อมูลของไฟล์จากฐานข้อมูล
    $this->db->where('finance_pdf_id', $pdf_id);
    $this->db->delete('tbl_finance_pdf');

    // บันทึก log การลบไฟล์ PDF ============================================
    $this->log_model->add_log(
        'ลบ',
        'งานการเงินและการบัญชี',
        'ไฟล์ PDF: ' . $file_data->finance_pdf_pdf . ' จากเอกสาร: ' . ($finance_data ? $finance_data->finance_name : 'ไม่ระบุ'),
        $file_data->finance_pdf_ref_id,
        array('file_type' => 'PDF', 'file_name' => $file_data->finance_pdf_pdf)
    );
    // ==================================================================

    $this->space_model->update_server_current();
    $this->session->set_flashdata('del_success', TRUE);
}

public function del_doc($doc_id)
{
    // ดึงชื่อไฟล์ DOC จากฐานข้อมูลโดยใช้ $doc_id
    $this->db->select('finance_file_doc, finance_file_ref_id');
    $this->db->where('finance_file_id', $doc_id);
    $query = $this->db->get('tbl_finance_file');
    $file_data = $query->row();

    // ดึงชื่อข้อมูลการเงินสำหรับ log
    $finance_data = $this->read($file_data->finance_file_ref_id);

    // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
    $file_path = './docs/file/' . $file_data->finance_file_doc;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // ลบข้อมูลของไฟล์จากฐานข้อมูล
    $this->db->where('finance_file_id', $doc_id);
    $this->db->delete('tbl_finance_file');

    // บันทึก log การลบไฟล์ DOC ==========================================
    $this->log_model->add_log(
        'ลบ',
        'งานการเงินและการบัญชี',
        'ไฟล์ DOC: ' . $file_data->finance_file_doc . ' จากเอกสาร: ' . ($finance_data ? $finance_data->finance_name : 'ไม่ระบุ'),
        $file_data->finance_file_ref_id,
        array('file_type' => 'DOC', 'file_name' => $file_data->finance_file_doc)
    );
    // ==================================================================

    $this->space_model->update_server_current();
    $this->session->set_flashdata('del_success', TRUE);
}

public function del_img($file_id)
{
    // ดึงชื่อไฟล์ IMG จากฐานข้อมูลโดยใช้ $file_id
    $this->db->select('finance_img_img, finance_img_ref_id');
    $this->db->where('finance_img_id', $file_id);
    $query = $this->db->get('tbl_finance_img');
    $file_data = $query->row();

    // ดึงชื่อข้อมูลการเงินสำหรับ log
    $finance_data = $this->read($file_data->finance_img_ref_id);

    // ลบไฟล์จากแหล่งที่เก็บไฟล์ (อาจต้องใช้ unlink หรือวิธีอื่น)
    $file_path = './docs/img/' . $file_data->finance_img_img;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // บันทึก log การลบรูปภาพ ===============================================
    $this->log_model->add_log(
        'ลบ',
        'งานการเงินและการบัญชี',
        'รูปภาพ: ' . $file_data->finance_img_img . ' จากเอกสาร: ' . ($finance_data ? $finance_data->finance_name : 'ไม่ระบุ'),
        $file_data->finance_img_ref_id,
        array('file_type' => 'IMAGE', 'file_name' => $file_data->finance_img_img)
    );
    // ==================================================================

    // ลบข้อมูลของไฟล์จากฐานข้อมูล
    $this->db->where('finance_img_id', $file_id);
    $this->db->delete('tbl_finance_img');
    $this->space_model->update_server_current();
    $this->session->set_flashdata('del_success', TRUE);
}

public function edit($finance_id)
{
    // ดึงข้อมูลเก่าก่อนแก้ไข
    $old_data = $this->read($finance_id);

    // Update finance information
    $data = array(
        'finance_ref_id' => $this->input->post('finance_ref_id'),
        'finance_name' => $this->input->post('finance_name'),
        'finance_detail' => $this->input->post('finance_detail'),
        'finance_date' => $this->input->post('finance_date'),
        'finance_link' => $this->input->post('finance_link'),
        'finance_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่แก้ไขข้อมูล
    );

    $this->db->where('finance_id', $finance_id);
    $this->db->update('tbl_finance', $data);

    // หาพื้นที่ว่าง และอัพโหลดlimit จากฐานข้อมูล
    $used_space = $this->space_model->get_used_space();
    $upload_limit = $this->space_model->get_limit_storage();

    $total_space_required = 0;
    // ตรวจสอบว่ามีข้อมูลรูปภาพเพิ่มเติมหรือไม่
    if (isset($_FILES['finance_img_img'])) {
        foreach ($_FILES['finance_img_img']['name'] as $index => $name) {
            if (isset($_FILES['finance_img_img']['size'][$index])) {
                $total_space_required += $_FILES['finance_img_img']['size'][$index];
            }
        }
    }

    // ตรวจสอบว่ามีข้อมูลไฟล์ PDF หรือไม่
    if (isset($_FILES['finance_pdf_pdf'])) {
        foreach ($_FILES['finance_pdf_pdf']['name'] as $index => $name) {
            if (isset($_FILES['finance_pdf_pdf']['size'][$index])) {
                $total_space_required += $_FILES['finance_pdf_pdf']['size'][$index];
            }
        }
    }

    // ตรวจสอบว่ามีข้อมูลไฟล์ doc หรือไม่
    if (isset($_FILES['finance_file_doc'])) {
        foreach ($_FILES['finance_file_doc']['name'] as $index => $name) {
            if (isset($_FILES['finance_file_doc']['size'][$index])) {
                $total_space_required += $_FILES['finance_file_doc']['size'][$index];
            }
        }
    }

    // เช็คค่าว่าง
    if ($used_space + ($total_space_required / (1024 * 1024 * 1024)) >= $upload_limit) {
        $this->session->set_flashdata('save_error', TRUE);
        redirect('finance_backend/adding');
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
    $img_main = $this->img_upload->do_upload('finance_img');

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
    if (!empty($img_main)) {
        $this->db->trans_start(); // เริ่ม Transaction

        // ดึงข้อมูลรูปเก่า
        $old_document = $this->db->get_where('tbl_finance', array('finance_id' => $finance_id))->row();

        // ตรวจสอบว่ามีไฟล์เก่าหรือไม่
        if ($old_document && $old_document->finance_img) {
            $old_file_path = './docs/img/' . $old_document->finance_img;

            if (file_exists($old_file_path)) {
                unlink($old_file_path); // ลบไฟล์เก่า
            }
        }

        // ถ้ามีการอัปโหลดรูปภาพใหม่
        $img_data['finance_img'] = $this->img_upload->data('file_name');
        $this->db->where('finance_id', $finance_id);
        $this->db->update('tbl_finance', $img_data);

        $this->db->trans_complete(); // สิ้นสุด Transaction
    }

    $imgs_data = array();

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพเพิ่มเติมหรือไม่
    if (!empty($_FILES['finance_img_img']['name'][0])) {

        foreach ($_FILES['finance_img_img']['name'] as $index => $name) {
            $_FILES['finance_img_img_multiple']['name'] = $name;
            $_FILES['finance_img_img_multiple']['type'] = $_FILES['finance_img_img']['type'][$index];
            $_FILES['finance_img_img_multiple']['tmp_name'] = $_FILES['finance_img_img']['tmp_name'][$index];
            $_FILES['finance_img_img_multiple']['error'] = $_FILES['finance_img_img']['error'][$index];
            $_FILES['finance_img_img_multiple']['size'] = $_FILES['finance_img_img']['size'][$index];

            if ($this->img_upload->do_upload('finance_img_img_multiple')) {
                $upload_data = $this->img_upload->data();
                $imgs_data[] = array(
                    'finance_img_ref_id' => $finance_id,
                    'finance_img_img' => $upload_data['file_name']
                );
            }
        }
        $this->db->insert_batch('tbl_finance_img', $imgs_data);
    }

    $pdf_data = array();

    // ตรวจสอบว่ามีการอัปโหลด pdf เพิ่มเติมหรือไม่
    if (!empty($_FILES['finance_pdf_pdf']['name'][0])) {
        foreach ($_FILES['finance_pdf_pdf']['name'] as $index => $name) {
            $_FILES['finance_pdf_pdf_multiple']['name'] = $name;
            $_FILES['finance_pdf_pdf_multiple']['type'] = $_FILES['finance_pdf_pdf']['type'][$index];
            $_FILES['finance_pdf_pdf_multiple']['tmp_name'] = $_FILES['finance_pdf_pdf']['tmp_name'][$index];
            $_FILES['finance_pdf_pdf_multiple']['error'] = $_FILES['finance_pdf_pdf']['error'][$index];
            $_FILES['finance_pdf_pdf_multiple']['size'] = $_FILES['finance_pdf_pdf']['size'][$index];

            if ($this->pdf_upload->do_upload('finance_pdf_pdf_multiple')) {
                $upload_data = $this->pdf_upload->data();
                $pdf_data[] = array(
                    'finance_pdf_ref_id' => $finance_id,
                    'finance_pdf_pdf' => $upload_data['file_name']
                );
            }
        }
        $this->db->insert_batch('tbl_finance_pdf', $pdf_data);
    }

    $doc_data = array();

    // ตรวจสอบว่ามีการอัปโหลด doc เพิ่มเติมหรือไม่
    if (!empty($_FILES['finance_file_doc']['name'][0])) {
        foreach ($_FILES['finance_file_doc']['name'] as $index => $name) {
            $_FILES['finance_file_doc_multiple']['name'] = $name;
            $_FILES['finance_file_doc_multiple']['type'] = $_FILES['finance_file_doc']['type'][$index];
            $_FILES['finance_file_doc_multiple']['tmp_name'] = $_FILES['finance_file_doc']['tmp_name'][$index];
            $_FILES['finance_file_doc_multiple']['error'] = $_FILES['finance_file_doc']['error'][$index];
            $_FILES['finance_file_doc_multiple']['size'] = $_FILES['finance_file_doc']['size'][$index];

            if ($this->doc_upload->do_upload('finance_file_doc_multiple')) {
                $upload_data = $this->doc_upload->data();
                $doc_data[] = array(
                    'finance_file_ref_id' => $finance_id,
                    'finance_file_doc' => $upload_data['file_name']
                );
            }
        }
        $this->db->insert_batch('tbl_finance_file', $doc_data);
    }
    $this->space_model->update_server_current();

    // บันทึก log การแก้ไขข้อมูลการเงิน =====================================
    // เปรียบเทียบการเปลี่ยนแปลงข้อมูล
    $changes = array();
    if ($old_data) {
        if ($old_data->finance_ref_id != $data['finance_ref_id']) {
            $changes['finance_ref_id'] = array(
                'old' => $old_data->finance_ref_id,
                'new' => $data['finance_ref_id']
            );
        }
        if ($old_data->finance_name != $data['finance_name']) {
            $changes['finance_name'] = array(
                'old' => $old_data->finance_name,
                'new' => $data['finance_name']
            );
        }
        if ($old_data->finance_detail != $data['finance_detail']) {
            $changes['finance_detail'] = array(
                'old' => 'มีการเปลี่ยนแปลง',
                'new' => 'รายละเอียดใหม่'
            );
        }
        if ($old_data->finance_date != $data['finance_date']) {
            $changes['finance_date'] = array(
                'old' => $old_data->finance_date,
                'new' => $data['finance_date']
            );
        }
        if (isset($data['finance_link']) && $old_data->finance_link != $data['finance_link']) {
            $changes['finance_link'] = array(
                'old' => $old_data->finance_link,
                'new' => $data['finance_link']
            );
        }
    }

    // เพิ่มข้อมูลไฟล์ที่อัพโหลด
    $files_info = array();
    if (!empty($imgs_data)) {
        $files_info['images_added'] = count($imgs_data);
        $files_info['image_names'] = array_column($imgs_data, 'finance_img_img');
    }
    if (!empty($pdf_data)) {
        $files_info['pdfs_added'] = count($pdf_data);
        $files_info['pdf_names'] = array_column($pdf_data, 'finance_pdf_pdf');
    }
    if (!empty($doc_data)) {
        $files_info['docs_added'] = count($doc_data);
        $files_info['doc_names'] = array_column($doc_data, 'finance_file_doc');
    }

    // บันทึก log การแก้ไขข้อมูลการเงิน
    $this->log_model->add_log(
        'แก้ไข',
        'งานการเงินและการบัญชี',
        $data['finance_name'],
        $finance_id,
        array(
            'changes' => $changes,
            'files_uploaded' => $files_info,
            'total_files_added' => count($imgs_data) + count($pdf_data) + count($doc_data)
        )
    );
    // ====================================================================

    $this->session->set_flashdata('save_success', TRUE);
}

    public function del_finance($finance_id)
{
    // ดึงข้อมูลก่อนลบ
    $finance_data = $this->read($finance_id);

    $old_document = $this->db->get_where('tbl_finance', array('finance_id' => $finance_id))->row();

    $old_file_path = './docs/img/' . $old_document->finance_img;
    if (file_exists($old_file_path)) {
        unlink($old_file_path);
    }

    $this->db->delete('tbl_finance', array('finance_id' => $finance_id));

    // บันทึก log การลบข้อมูลการเงิน =====================================
    if ($finance_data) {
        $this->log_model->add_log(
            'ลบ',
            'งานการเงินและการบัญชี',
            $finance_data->finance_name,
            $finance_id,
            array('deleted_date' => date('Y-m-d H:i:s'))
        );
    }
    // ==================================================================

    $this->space_model->update_server_current();
}

    public function del_finance_pdf($finance_id)
    {
        // ดึงข้อมูลรายการ pdf ก่อน
        $files = $this->db->get_where('tbl_finance_pdf', array('finance_pdf_ref_id' => $finance_id))->result();

        // ลบ pdf จากตาราง tbl_finance_pdf
        $this->db->where('finance_pdf_ref_id', $finance_id);
        $this->db->delete('tbl_finance_pdf');

        // ลบไฟล์ pdf ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->finance_pdf_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_finance_doc($finance_id)
    {
        // ดึงข้อมูลรายการ doc ก่อน
        $files = $this->db->get_where('tbl_finance_file', array('finance_file_ref_id' => $finance_id))->result();

        // ลบ doc จากตาราง tbl_finance_file
        $this->db->where('finance_file_ref_id', $finance_id);
        $this->db->delete('tbl_finance_file');

        // ลบไฟล์ doc ที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/file/' . $files->finance_file_doc;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    public function del_finance_img($finance_id)
    {
        // ดึงข้อมูลรายการรูปภาพก่อน
        $files = $this->db->get_where('tbl_finance_img', array('finance_img_ref_id' => $finance_id))->result();

        // ลบรูปภาพจากตาราง tbl_finance_file
        $this->db->where('finance_img_ref_id', $finance_id);
        $this->db->delete('tbl_finance_img');

        // ลบไฟล์รูปภาพที่เกี่ยวข้อง
        foreach ($files as $files) {
            $file_path = './docs/img/' . $files->finance_img_img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }


    public function del_type_finance_type($finance_type_id)
{
    // ดึงข้อมูลหัวข้อก่อนลบ
    $type_data = $this->read_type($finance_type_id);

    $this->db->delete('tbl_finance_type', array('finance_type_id' => $finance_type_id));

    // บันทึก log การลบหัวข้อการเงิน ===================================
    if ($type_data) {
        $this->log_model->add_log(
            'ลบ',
            'งานการเงินและการบัญชี',
            'ลบหัวข้อ: ' . $type_data->finance_type_name,
            $finance_type_id,
            array('deleted_date' => date('Y-m-d H:i:s'))
        );
    }
    // ================================================================

    $this->space_model->update_server_current();
}
    public function del_type_finance($finance_type_id)
    {
        // ดึงข้อมูลทั้งหมดที่ต้องการลบ
        $documents = $this->db->get_where('tbl_finance', array('finance_ref_id' => $finance_type_id))->result();

        // ถ้ามีข้อมูล
        if ($documents) {
            foreach ($documents as $doc) {
                // ลบไฟล์รูปภาพ ถ้ามี
                if (!empty($doc->finance_img)) {
                    $old_file_path = './docs/img/' . $doc->finance_img;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
            }
        }

        // ลบข้อมูลจากฐานข้อมูล
        $this->db->delete('tbl_finance', array('finance_ref_id' => $finance_type_id));
        $this->space_model->update_server_current();
    }

    public function del_type_finance_pdf($finance_type_id)
    {
        $finance_ids = $this->get_finance_id($finance_type_id);

        if ($finance_ids) {
            // แปลง array เป็น string โดยคั่นด้วย ','
            $finance_ids_string = implode(',', $finance_ids);

            // สร้าง SQL query โดยใส่ ' รอบแต่ละค่า
            $sql = "SELECT * FROM `tbl_finance_pdf` WHERE finance_pdf_ref_id IN ($finance_ids_string)";

            // ดึงข้อมูลรายการรูปภาพที่ต้องการลบ
            $files = $this->db->query($sql)->result();

            // ลบรูปภาพจากตาราง tbl_finance_pdf
            $this->db->where_in('finance_pdf_ref_id', $finance_ids);
            $this->db->delete('tbl_finance_pdf');

            // ลบไฟล์รูปภาพที่เกี่ยวข้อง
            foreach ($files as $file) {
                $file_path = './docs/file/' . $file->finance_pdf_pdf;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
    }

    public function del_type_finance_doc($finance_type_id)
    {
        $finance_ids = $this->get_finance_id($finance_type_id);

        if ($finance_ids) {
            // แปลง array เป็น string โดยคั่นด้วย ','
            $finance_ids_string = implode(',', $finance_ids);

            // สร้าง SQL query โดยใส่ ' รอบแต่ละค่า
            $sql = "SELECT * FROM `tbl_finance_file` WHERE finance_file_ref_id IN ($finance_ids_string)";

            // ดึงข้อมูลรายการรูปภาพที่ต้องการลบ
            $files = $this->db->query($sql)->result();

            // ลบรูปภาพจากตาราง tbl_finance_file
            $this->db->where_in('finance_file_ref_id', $finance_ids);
            $this->db->delete('tbl_finance_file');

            // ลบไฟล์รูปภาพที่เกี่ยวข้อง
            foreach ($files as $file) {
                $file_path = './docs/file/' . $file->finance_file_doc;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
    }

    public function del_type_finance_img($finance_type_id)
    {
        $finance_ids = $this->get_finance_id($finance_type_id);

        if ($finance_ids) {
            // แปลง array เป็น string โดยคั่นด้วย ','
            $finance_ids_string = implode(',', $finance_ids);

            // สร้าง SQL query โดยใส่ ' รอบแต่ละค่า
            $sql = "SELECT * FROM `tbl_finance_img` WHERE finance_img_ref_id IN ($finance_ids_string)";

            // ดึงข้อมูลรายการรูปภาพที่ต้องการลบ
            $files = $this->db->query($sql)->result();

            // ลบรูปภาพจากตาราง tbl_finance_img
            $this->db->where_in('finance_img_ref_id', $finance_ids);
            $this->db->delete('tbl_finance_img');

            // ลบไฟล์รูปภาพที่เกี่ยวข้อง
            foreach ($files as $file) {
                $file_path = './docs/img/' . $file->finance_img_img;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
    }

    public function get_finance_id($finance_type_id)
    {
        $query = $this->db->select('finance_id')
            ->from('tbl_finance')
            ->where('finance_ref_id', $finance_type_id)
            ->get();

        if ($query->num_rows() > 0) {
            $result = array();
            foreach ($query->result() as $row) {
                $result[] = $row->finance_id;
            }
            return $result; // คืน Array ของ finance_id ทั้งหมด
        } else {
            return null; // หรือค่าที่เหมาะสมเมื่อไม่พบข้อมูล
        }
    }

    public function finance_topic_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_finance_type');
        $this->db->limit(8);
        $this->db->order_by('tbl_finance_type.finance_type_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function finance_frontend_list_topic()
    {
        $this->db->select('*');
        $this->db->from('tbl_finance_type');
        $this->db->order_by('tbl_finance_type.finance_type_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    public function finance_frontend_list($finance_type_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_finance');
        $this->db->join('tbl_finance_type', 'tbl_finance.finance_ref_id = tbl_finance_type.finance_type_id', 'inner'); // เปลี่ยนเป็น INNER JOIN
        $this->db->where('tbl_finance_type.finance_type_id', $finance_type_id);
        $this->db->where('tbl_finance.finance_status', 'show');
        $this->db->order_by('tbl_finance.finance_date', 'DESC'); // เพิ่มบรรทัดนี้
        $query = $this->db->get();
        return $query->result();
    }
    public function increment_view($finance_id)
    {
        $this->db->where('finance_id', $finance_id);
        $this->db->set('finance_view', 'finance_view + 1', false); // บวกค่า finance_view ทีละ 1
        $this->db->update('tbl_finance');
    }
    // ใน finance_model
    public function increment_download_finance($finance_file_id)
    {
        $this->db->where('finance_file_id', $finance_file_id);
        $this->db->set('finance_file_download', 'finance_file_download + 1', false); // บวกค่า finance_download ทีละ 1
        $this->db->update('tbl_finance_file');
    }
}
