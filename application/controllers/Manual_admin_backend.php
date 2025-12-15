<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manual_admin_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->check_access_permission(['1', '134']); // 1=ทั้งหมด
        $this->load->model('Manual_admin_model');
        $this->load->model('log_model');
        $this->load->library('upload');
        $this->load->helper('file');
    }

    /**
     * 🆕 หน้าแสดงคู่มือทั้งหมด (แยก 2 หมวด)
     */
    public function index()
    {
        // ดึงคู่มือจาก Database
        $data['manuals'] = $this->Manual_admin_model->get_all();
        
        // 🆕 เช็คไฟล์ LINE OA Manual
        $line_manual_path = FCPATH . 'docs/คู่มือการใช้งานแชท LINE OA.pdf';
        $data['has_line_manual'] = file_exists($line_manual_path);
        $data['line_manual_path'] = 'docs/คู่มือการใช้งานแชท LINE OA.pdf';
        
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/manual_admin', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * 🆕 หน้าฟอร์มเพิ่มคู่มือใหม่
     */
    public function create()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/manual_admin_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ✅ เพิ่มคู่มือใหม่ (ปรับปรุงแล้ว)
     */
    public function insert_manual_admin()
    {
        // Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('manual_admin_name', 'ชื่อคู่มือ', 
            'required|min_length[3]|max_length[100]|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('manual_admin_backend/create');
            return;
        }

        // Upload Configuration
        $config['upload_path'] = './docs/file/';
        $config['allowed_types'] = 'pdf';
        $config['max_size'] = 20480; // 20 MB
        $config['file_name'] = time() . '_' . str_replace(' ', '_', $_FILES['manual_admin_pdf']['name']);
        $this->upload->initialize($config);

        $file_name = '';
        if ($this->upload->do_upload('manual_admin_pdf')) {
            $upload_data = $this->upload->data();
            
            // ✅ เพิ่มการตรวจสอบ MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $upload_data['full_path']);
            finfo_close($finfo);
            
            if ($mime !== 'application/pdf') {
                unlink($upload_data['full_path']);
                $this->session->set_flashdata('error', 'ไฟล์ต้องเป็น PDF เท่านั้น');
                redirect('manual_admin_backend/create');
                return;
            }
            
            $file_name = $upload_data['file_name'];
        } else {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            redirect('manual_admin_backend/create');
            return;
        }

        // บันทึกข้อมูล
        $data = array(
            'manual_admin_name' => $this->input->post('manual_admin_name', TRUE),
            'manual_admin_pdf' => $file_name,
            'manual_admin_view' => 0,
            'manual_admin_download' => 0,
            'manual_admin_by' => $this->session->userdata('username')
        );

        $this->Manual_admin_model->insert_manual_admin($data);

        $this->log_model->add_log(
            'เพิ่ม',
            'คู่มือการใช้งาน',
            $data['manual_admin_name'],
            $this->db->insert_id(),
            array(
                'files_uploaded' => array(
                    'pdfs' => !empty($data['manual_admin_pdf']) ? 1 : 0
                )
            )
        );

        $this->session->set_flashdata('success', 'เพิ่มคู่มือสำเร็จ');
        redirect('manual_admin_backend');
    }

    /**
     * แก้ไขคู่มือ
     */
    public function edit($id)
    {
        $data['manual'] = $this->Manual_admin_model->get_by_id($id);
        
        if (!$data['manual']) {
            show_404();
        }
        
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/manual_admin_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ✅ อัปเดตคู่มือ (ปรับปรุงแล้ว)
     */
    public function update_manual_admin($id)
    {
        // Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('manual_admin_name', 'ชื่อคู่มือ', 
            'required|min_length[3]|max_length[100]|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('manual_admin_backend/edit/' . $id);
            return;
        }

        // ดึงข้อมูลเดิม
        $manual = $this->Manual_admin_model->get_by_id($id);
        if (!$manual) {
            show_404();
        }

        $file_name = $manual->manual_admin_pdf;

        // อัปโหลดไฟล์ใหม่ (ถ้ามี)
        if (!empty($_FILES['manual_admin_pdf']['name'])) {
            $config['upload_path'] = './docs/file/';
            $config['allowed_types'] = 'pdf';
            $config['max_size'] = 20480;
            $config['file_name'] = time() . '_' . str_replace(' ', '_', $_FILES['manual_admin_pdf']['name']);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('manual_admin_pdf')) {
                $upload_data = $this->upload->data();
                
                // ตรวจสอบ MIME type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $upload_data['full_path']);
                finfo_close($finfo);
                
                if ($mime !== 'application/pdf') {
                    unlink($upload_data['full_path']);
                    $this->session->set_flashdata('error', 'ไฟล์ต้องเป็น PDF เท่านั้น');
                    redirect('manual_admin_backend/edit/' . $id);
                    return;
                }
                
                $file_name = $upload_data['file_name'];

                // ✅ ลบไฟล์เก่า (ถ้ามี)
                if (!empty($manual->manual_admin_pdf) && file_exists('./docs/file/' . $manual->manual_admin_pdf)) {
                    unlink('./docs/file/' . $manual->manual_admin_pdf);
                }
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('manual_admin_backend/edit/' . $id);
                return;
            }
        }

        // บันทึกข้อมูล
        $data = array(
            'manual_admin_name' => $this->input->post('manual_admin_name', TRUE),
            'manual_admin_pdf' => $file_name,
            'manual_admin_by' => $this->session->userdata('username')
        );

        $this->Manual_admin_model->update_manual_admin($id, $data);

        $this->log_model->add_log(
            'แก้ไข',
            'คู่มือการใช้งาน',
            $data['manual_admin_name'],
            $id,
            array(
                'files_uploaded' => array(
                    'pdfs' => !empty($data['manual_admin_pdf']) ? 1 : 0
                )
            )
        );

        $this->session->set_flashdata('success', 'อัปเดตคู่มือสำเร็จ');
        redirect('manual_admin_backend');
    }

    /**
     * ✅ ลบคู่มือ (แก้ไข bug แล้ว)
     */
    public function delete($id)
    {
        // 1. ดึงข้อมูลก่อนลบ
        $manual = $this->Manual_admin_model->get_by_id($id);
        
        if (!$manual) {
            $this->session->set_flashdata('error', 'ไม่พบคู่มือที่ต้องการลบ');
            redirect('manual_admin_backend');
            return;
        }

        // 2. ลบไฟล์ PDF (ถ้ามี)
        if (!empty($manual->manual_admin_pdf)) {
            $file_path = './docs/file/' . $manual->manual_admin_pdf;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // 3. ลบข้อมูลจาก Database (เพียงครั้งเดียว)
        $this->Manual_admin_model->delete_manual_admin($id);

        // 4. Log
        $this->log_model->add_log(
            'ลบ',
            'คู่มือการใช้งาน',
            $manual->manual_admin_name,
            $id,
            array()
        );

        $this->session->set_flashdata('success', 'ลบคู่มือสำเร็จ');
        redirect('manual_admin_backend');
    }

    /**
     * ✅ ดาวน์โหลดคู่มือ (ปรับปรุงแล้ว)
     */
    public function download($id)
    {
        $manual = $this->Manual_admin_model->get_by_id($id);

        if (!$manual || empty($manual->manual_admin_pdf)) {
            show_404();
            return;
        }

        // ✅ Validate ชื่อไฟล์ (ป้องกัน Path Traversal)
        $filename = basename($manual->manual_admin_pdf);
        $file = FCPATH . 'docs/file/' . $filename;

        // ✅ เช็คว่าไฟล์อยู่ใน directory ที่ถูกต้อง
        $realpath = realpath($file);
        if (!$realpath || strpos($realpath, realpath(FCPATH . 'docs/file/')) !== 0) {
            show_404();
            return;
        }

        if (file_exists($file)) {
            // เพิ่มจำนวนดาวน์โหลด
            $this->Manual_admin_model->increment_download_manual_admin($id);

            // Force download
            $this->load->helper('download');
            force_download($file, NULL);
        } else {
            show_404();
        }
    }

    /**
     * 🆕 ดาวน์โหลดคู่มือ LINE OA (ไม่นับจำนวน)
     */
    public function download_line_manual()
    {
        $file = FCPATH . 'docs/คู่มือการใช้งานแชท LINE OA.pdf';

        if (file_exists($file)) {
            // ✅ ไม่นับจำนวนดาวน์โหลด - ให้ดาวน์โหลดได้เลย
            $this->load->helper('download');
            force_download($file, NULL);
        } else {
            show_404();
        }
    }
}