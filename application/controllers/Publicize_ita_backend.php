<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Publicize_ita_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '10']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('publicize_ita_model');
        $this->load->model('lineoa_qrcode_model'); // เพิ่มบรรทัดนี้
    }


    // ==================== Publicize ITA Functions ====================

    public function index()
    {
        $data['query'] = $this->publicize_ita_model->list_all();
        $data['query_lineoa'] = $this->lineoa_qrcode_model->list_all(); // เพิ่มบรรทัดนี้

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/publicize_ita', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding_publicize_ita()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/publicize_ita_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_publicize_ita()
    {
        $this->publicize_ita_model->add_publicize_ita();
        redirect('publicize_ita_backend', 'refresh');
    }

    public function editing_publicize_ita($publicize_ita_id)
    {
        $data['rsedit'] = $this->publicize_ita_model->read($publicize_ita_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/publicize_ita_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_publicize_ita($publicize_ita_id)
    {
        $this->publicize_ita_model->edit_publicize_ita($publicize_ita_id);
        redirect('publicize_ita_backend', 'refresh');
    }

    public function del_publicize_ita($publicize_ita_id)
    {
        $this->publicize_ita_model->del_publicize_ita($publicize_ita_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('publicize_ita_backend', 'refresh');
    }

    public function updatepublicize_itaStatus()
    {
        $this->publicize_ita_model->updatepublicize_itaStatus();
    }

    // ==================== LINE OA QR Code Functions ====================

    public function adding_lineoa()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/lineoa_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_lineoa()
    {
        $this->lineoa_qrcode_model->add_lineoa();
        redirect('publicize_ita_backend', 'refresh');
    }

    public function editing_lineoa($lineoa_id)
    {
        $data['rsedit'] = $this->lineoa_qrcode_model->read($lineoa_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/lineoa_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_lineoa($lineoa_id)
    {
        $this->lineoa_qrcode_model->edit_lineoa($lineoa_id);
        redirect('publicize_ita_backend', 'refresh');
    }

    public function del_lineoa($lineoa_id)
    {
        $this->lineoa_qrcode_model->del_lineoa($lineoa_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('publicize_ita_backend', 'refresh');
    }

    public function update_lineoa_status()
    {
        $this->lineoa_qrcode_model->update_lineoa_status();
    }
}
