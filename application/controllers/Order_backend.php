<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
       // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
       $this->check_access_permission(['1', '91']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('order_model');
    }

   
	
	
	
public function index()
    {
        $order = $this->order_model->list_all();

        foreach ($order as $pdf) {
            $pdf->pdf = $this->order_model->list_all_pdf($pdf->order_id);
        }
        foreach ($order as $doc) {
            $doc->doc = $this->order_model->list_all_doc($doc->order_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/order', ['order' => $order]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/order_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->order_model->add();
        redirect('order_backend');
    }


    public function editing($order_id)
    {
        $data['rsedit'] = $this->order_model->read($order_id);
        $data['rsPdf'] = $this->order_model->read_pdf($order_id);
        $data['rsDoc'] = $this->order_model->read_doc($order_id);
        $data['rsImg'] = $this->order_model->read_img($order_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/order_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($order_id)
    {
        $this->order_model->edit($order_id);
        redirect('order_backend');
    }

    public function update_order_status()
    {
        $this->order_model->update_order_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->order_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->order_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->order_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_order($order_id)
    {
        $this->order_model->del_order_img($order_id);
        $this->order_model->del_order_pdf($order_id);
        $this->order_model->del_order_doc($order_id);
        $this->order_model->del_order($order_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('order_backend');
    }
}
