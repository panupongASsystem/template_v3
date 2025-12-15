<?php
defined('BASEPATH') or exit('No direct script access allowed');

class News_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
       // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
		$this->check_access_permission(['1', '9']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('news_model');
    }

    

public function index()
    {
        $news = $this->news_model->list_all();

        foreach ($news as $pdf) {
            $pdf->pdf = $this->news_model->list_all_pdf($pdf->news_id);
        }
        foreach ($news as $doc) {
            $doc->doc = $this->news_model->list_all_doc($doc->news_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/news', ['news' => $news]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/news_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->news_model->add();
        redirect('news_backend');
    }


    public function editing($news_id)
    {
        $data['rsedit'] = $this->news_model->read($news_id);
        $data['rsPdf'] = $this->news_model->read_pdf($news_id);
        $data['rsDoc'] = $this->news_model->read_doc($news_id);
        $data['rsImg'] = $this->news_model->read_img($news_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/news_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($news_id)
    {
        $this->news_model->edit($news_id);
        redirect('news_backend');
    }

    public function update_news_status()
    {
        $this->news_model->update_news_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->news_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->news_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->news_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_news($news_id)
    {
        $this->news_model->del_news_img($news_id);
        $this->news_model->del_news_pdf($news_id);
        $this->news_model->del_news_doc($news_id);
        $this->news_model->del_news($news_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('news_backend');
    }
}

