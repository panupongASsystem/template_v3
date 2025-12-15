<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_mags_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '133']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('E_mag_model');
        $this->load->library('upload');
    }

    public function index()
    {
        $e_mags = $this->E_mag_model->get_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/e_mags', ['e_mags' => $e_mags]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/e_mags_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        // ตรวจสอบข้อมูล
        if (empty($_POST['original_name'])) {
            $this->session->set_flashdata('error', 'กรุณาใส่ชื่อ E-Magazine');
            redirect('e_mags_backend/adding');
            return;
        }

        // ตั้งค่าสำหรับอัปโหลด
        $pdf_path = './docs/file/';
        $cover_path = './docs/img/';

        // สร้างโฟลเดอร์หากยังไม่มี
        if (!is_dir($pdf_path)) {
            mkdir($pdf_path, 0777, TRUE);
        }
        if (!is_dir($cover_path)) {
            mkdir($cover_path, 0777, TRUE);
        }

        // 1. อัปโหลดไฟล์ PDF
        $config_pdf['upload_path'] = $pdf_path;
        $config_pdf['allowed_types'] = 'pdf';
        $config_pdf['encrypt_name'] = TRUE;
        $config_pdf['max_size'] = 30720; // 30MB

        $this->upload->initialize($config_pdf);

        if (!$this->upload->do_upload('pdf_file')) {
            $this->session->set_flashdata('error', 'ไม่สามารถอัปโหลดไฟล์ PDF: ' . $this->upload->display_errors());
            redirect('e_mags_backend/adding');
            return;
        }
        $pdf_data = $this->upload->data();

        // 2. ตรวจสอบว่ามีการอัปโหลดรูปหน้าปกหรือไม่
        $cover_filename = '';
        $use_auto_cover = false;

        if (!empty($_FILES['cover_image']['name'])) {
            // มีการอัปโหลดรูปหน้าปก - ใช้รูปที่อัปโหลด
            $config_cover['upload_path'] = $cover_path;
            $config_cover['allowed_types'] = 'jpg|jpeg|png';
            $config_cover['encrypt_name'] = TRUE;
            $config_cover['max_size'] = 2048; // 2MB

            $this->upload->initialize($config_cover);

            if ($this->upload->do_upload('cover_image')) {
                $cover_data = $this->upload->data();
                $cover_filename = $cover_data['file_name'];
            } else {
                // หากอัปโหลดรูปหน้าปกไม่สำเร็จ ใช้ auto-generate
                $use_auto_cover = true;
            }
        } else {
            // ไม่มีการอัปโหลดรูปหน้าปก - ใช้ auto-generate
            $use_auto_cover = true;
        }

        // 3. สร้างรูปหน้าปกอัตโนมัติ (ถ้าไม่มีหรืออัปโหลดไม่สำเร็จ)
        if ($use_auto_cover) {
            $cover_filename = $pdf_data['raw_name'] . '.png';
            // ไม่ต้องสร้างรูปตอนนี้ - จะให้ JavaScript สร้างใน frontend
        }

        // 4. บันทึกข้อมูลลงฐานข้อมูล
        $db_data = [
            'file_name' => $pdf_data['file_name'],
            'original_name' => $this->input->post('original_name'),
            'cover_image' => $cover_filename,
            'uploaded_by' => $this->session->userdata('m_fname'),
            // ลบ auto_cover ออก เพราะไม่มีคอลัมน์นี้ในฐานข้อมูล
        ];

        if ($this->E_mag_model->add_pdf($db_data)) {
            $e_mag_id = $this->db->insert_id(); // ได้ ID ที่เพิ่งเพิ่ม

            if ($use_auto_cover) {
                // ส่งข้อมูลเพื่อให้ frontend สร้างรูปหน้าปก
                $this->session->set_flashdata('success', 'เพิ่ม E-Magazine สำเร็จ');
                $this->session->set_flashdata('need_cover_generation', json_encode([
                    'pdf_file' => $pdf_data['file_name'],
                    'cover_file' => $cover_filename,
                    'e_mag_id' => $e_mag_id
                ]));
                redirect('e_mags_backend/adding'); // กลับไปหน้า form เพื่อให้ JS ทำงาน
            } else {
                $this->session->set_flashdata('save_success', 'เพิ่ม E-Magazine สำเร็จ');
                redirect('e_mags_backend'); // ไปหน้ารายการปกติ
            }
        } else {
            // หากบันทึก DB ไม่สำเร็จ ให้ลบไฟล์ทั้งหมด
            unlink($pdf_data['full_path']);
            if (!$use_auto_cover && file_exists($cover_path . $cover_filename)) {
                unlink($cover_path . $cover_filename);
            }
            $this->session->set_flashdata('error', 'ไม่สามารถบันทึกข้อมูลลงฐานข้อมูลได้');
            redirect('e_mags_backend/adding');
        }
    }

    public function editing($id)
    {
        $data['e_mag'] = $this->E_mag_model->get_by_id($id);

        if (!$data['e_mag']) {
            $this->session->set_flashdata('error', 'ไม่พบข้อมูล E-Magazine');
            redirect('e_mags_backend');
            return;
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/e_mags_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($id)
    {
        // ตรวจสอบข้อมูล
        if (empty($_POST['original_name'])) {
            $this->session->set_flashdata('error', 'กรุณาใส่ชื่อ E-Magazine');
            redirect('e_mags_backend/editing/' . $id);
            return;
        }

        // ดึงข้อมูลเดิม
        $old_data = $this->E_mag_model->get_by_id($id);
        if (!$old_data) {
            $this->session->set_flashdata('error', 'ไม่พบข้อมูล E-Magazine');
            redirect('e_mags_backend');
            return;
        }

        // ข้อมูลที่จะอัปเดต
        $update_data = [
            'original_name' => $this->input->post('original_name')
        ];

        $need_cover_generation = false;
        $new_pdf_filename = '';

        // ตรวจสอบการอัปโหลดไฟล์ PDF ใหม่
        if (!empty($_FILES['pdf_file']['name'])) {
            $pdf_path = './docs/file/';

            $config_pdf['upload_path'] = $pdf_path;
            $config_pdf['allowed_types'] = 'pdf';
            $config_pdf['encrypt_name'] = TRUE;
            $config_pdf['max_size'] = 30720; // 10MB

            $this->upload->initialize($config_pdf);

            if ($this->upload->do_upload('pdf_file')) {
                $pdf_data = $this->upload->data();
                $update_data['file_name'] = $pdf_data['file_name'];
                $new_pdf_filename = $pdf_data['file_name'];

                // ลบไฟล์เก่า
                if (file_exists('./docs/file/' . $old_data->file_name)) {
                    unlink('./docs/file/' . $old_data->file_name);
                }

                // ถ้าเปลี่ยน PDF และไม่มีการอัปโหลดรูปใหม่ ให้สร้างรูปหน้าปกใหม่
                if (empty($_FILES['cover_image']['name'])) {
                    $need_cover_generation = true;
                    $update_data['cover_image'] = $pdf_data['raw_name'] . '.png';
                    // ลบ auto_cover ออก
                }
            } else {
                $this->session->set_flashdata('error', 'ไม่สามารถอัปโหลดไฟล์ PDF: ' . $this->upload->display_errors());
                redirect('e_mags_backend/editing/' . $id);
                return;
            }
        }

        // ตรวจสอบการอัปโหลดรูปหน้าปกใหม่
        if (!empty($_FILES['cover_image']['name'])) {
            $cover_path = './docs/img/';

            $config_cover['upload_path'] = $cover_path;
            $config_cover['allowed_types'] = 'jpg|jpeg|png';
            $config_cover['encrypt_name'] = TRUE;
            $config_cover['max_size'] = 2048; // 2MB

            $this->upload->initialize($config_cover);

            if ($this->upload->do_upload('cover_image')) {
                $cover_data = $this->upload->data();
                $update_data['cover_image'] = $cover_data['file_name'];
                // ลบ auto_cover ออก

                // ลบไฟล์เก่า
                if (file_exists('./docs/img/' . $old_data->cover_image)) {
                    unlink('./docs/img/' . $old_data->cover_image);
                }
            } else {
                $this->session->set_flashdata('error', 'ไม่สามารถอัปโหลดรูปหน้าปก: ' . $this->upload->display_errors());
                redirect('e_mags_backend/editing/' . $id);
                return;
            }
        }

        // บันทึกการแก้ไข
        if ($this->E_mag_model->update($id, $update_data)) {
            if ($need_cover_generation) {
                $this->session->set_flashdata('success', 'แก้ไข E-Magazine สำเร็จ (กำลังสร้างรูปหน้าปกใหม่...)');
                $this->session->set_flashdata('need_cover_generation', json_encode([
                    'pdf_file' => $new_pdf_filename,
                    'cover_file' => $update_data['cover_image'],
                    'uploaded_by' => $this->session->userdata('m_fname'),
                    'e_mag_id' => $id
                ]));
                redirect('e_mags_backend/editing/' . $id); // กลับไปหน้า edit เพื่อให้ JS ทำงาน
            } else {
                $this->session->set_flashdata('save_success', 'แก้ไข E-Magazine สำเร็จ');
                redirect('e_mags_backend'); // ไปหน้ารายการปกติ
            }
        } else {
            $this->session->set_flashdata('error', 'ไม่สามารถบันทึกการแก้ไขได้');
            redirect('e_mags_backend/editing/' . $id);
        }
    }

    /**
     * API สำหรับสร้างรูปหน้าปกจาก PDF
     */
    public function generate_cover()
    {
        header('Content-Type: application/json');

        $pdf_filename = $this->input->post('pdf_filename');
        $cover_filename = $this->input->post('cover_filename');

        if (empty($pdf_filename) || empty($cover_filename)) {
            echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
            return;
        }

        // ตรวจสอบว่าไฟล์ PDF มีอยู่จริง
        $pdf_path = './docs/file/' . $pdf_filename;
        if (!file_exists($pdf_path)) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบไฟล์ PDF']);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'พร้อมสร้างรูปหน้าปก',
            'pdf_url' => base_url('docs/file/' . $pdf_filename),
            'cover_path' => './docs/img/' . $cover_filename
        ]);
    }

    /**
     * API สำหรับบันทึกรูปหน้าปกที่สร้างแล้ว
     */
    public function save_generated_cover()
    {
        header('Content-Type: application/json');

        $cover_filename = $this->input->post('cover_filename');
        $cover_data = $this->input->post('cover_data'); // base64 image data

        if (empty($cover_filename) || empty($cover_data)) {
            echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
            return;
        }

        // ลบ "data:image/png;base64," prefix
        $cover_data = str_replace('data:image/png;base64,', '', $cover_data);
        $cover_data = base64_decode($cover_data);

        // บันทึกไฟล์
        $cover_path = './docs/img/' . $cover_filename;
        if (file_put_contents($cover_path, $cover_data)) {
            echo json_encode(['status' => 'success', 'message' => 'บันทึกรูปหน้าปกสำเร็จ']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถบันทึกรูปหน้าปกได้']);
        }
    }

    public function del($id)
    {
        // ตรวจสอบว่าส่ง ID มาหรือไม่
        if (empty($id) || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'ไม่พบรหัส E-Magazine ที่ต้องการลบ');
            redirect('e_mags_backend');
            return;
        }

        // ตรวจสอบว่ามีข้อมูลอยู่ในฐานข้อมูลหรือไม่
        $e_mag_data = $this->E_mag_model->get_by_id($id);

        if (!$e_mag_data) {
            $this->session->set_flashdata('error', 'ไม่พบข้อมูล E-Magazine ที่ต้องการลบ');
            redirect('e_mags_backend');
            return;
        }

        // เริ่มการลบไฟล์และข้อมูล
        $deletion_success = true;
        $error_messages = [];

        // 1. ลบไฟล์ PDF
        $pdf_path = './docs/file/' . $e_mag_data->file_name;
        if (!empty($e_mag_data->file_name) && file_exists($pdf_path)) {
            if (!unlink($pdf_path)) {
                $deletion_success = false;
                $error_messages[] = 'ไม่สามารถลบไฟล์ PDF ได้';
            }
        }

        // 2. ลบไฟล์รูปหน้าปก
        $cover_path = './docs/img/' . $e_mag_data->cover_image;
        if (!empty($e_mag_data->cover_image) && file_exists($cover_path)) {
            if (!unlink($cover_path)) {
                $deletion_success = false;
                $error_messages[] = 'ไม่สามารถลบรูปหน้าปกได้';
            }
        }

        // 3. ลบข้อมูลจากฐานข้อมูล
        if (!$this->E_mag_model->delete($id)) {
            $deletion_success = false;
            $error_messages[] = 'ไม่สามารถลบข้อมูลจากฐานข้อมูลได้';
        }

        // แสดงผลลัพธ์
        if ($deletion_success) {
            $this->session->set_flashdata('del_success', 'ลบ E-Magazine "' . $e_mag_data->original_name . '" สำเร็จ', TRUE);
        } else {
            $error_message = 'เกิดข้อผิดพลาดในการลบ E-Magazine: ' . implode(', ', $error_messages);
            $this->session->set_flashdata('error', $error_message);
        }
        redirect('e_mags_backend');
    }
}
