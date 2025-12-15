<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ethics_strategy_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '128']); // 1=ทั้งหมด
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('ethics_strategy_model');
    }

    

    public function index()
    {
        $ethics_strategy = $this->ethics_strategy_model->list_all();

        foreach ($ethics_strategy as $pdf) {
            $pdf->pdf = $this->ethics_strategy_model->list_all_pdf($pdf->ethics_strategy_id);
        }
        foreach ($ethics_strategy as $doc) {
            $doc->doc = $this->ethics_strategy_model->list_all_doc($doc->ethics_strategy_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ethics_strategy', ['ethics_strategy' => $ethics_strategy]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ethics_strategy_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->ethics_strategy_model->add();
        redirect('ethics_strategy_backend');
    }


    public function editing($ethics_strategy_id)
    {
        $data['rsedit'] = $this->ethics_strategy_model->read($ethics_strategy_id);
        $data['rsPdf'] = $this->ethics_strategy_model->read_pdf($ethics_strategy_id);
        $data['rsDoc'] = $this->ethics_strategy_model->read_doc($ethics_strategy_id);
        $data['rsImg'] = $this->ethics_strategy_model->read_img($ethics_strategy_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ethics_strategy_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($ethics_strategy_id)
    {
        $this->ethics_strategy_model->edit($ethics_strategy_id);
        redirect('ethics_strategy_backend');
    }

    public function update_ethics_strategy_status()
    {
        $this->ethics_strategy_model->update_ethics_strategy_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->ethics_strategy_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->ethics_strategy_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->ethics_strategy_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_ethics_strategy($ethics_strategy_id)
    {
        $this->ethics_strategy_model->del_ethics_strategy_img($ethics_strategy_id);
        $this->ethics_strategy_model->del_ethics_strategy_pdf($ethics_strategy_id);
        $this->ethics_strategy_model->del_ethics_strategy_doc($ethics_strategy_id);
        $this->ethics_strategy_model->del_ethics_strategy($ethics_strategy_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('ethics_strategy_backend');
    }
}
