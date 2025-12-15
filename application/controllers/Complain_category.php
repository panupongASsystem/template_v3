<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Complain_category extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('m_id')) redirect('user');
        $this->load->model('complain_category_model');
    }
    
    public function index() {
        $data['categories'] = $this->complain_category_model->get_all_categories();
        $this->load_view('category/list', $data);
    }
    
    public function save() {
        $data = [
            'cat_name' => $this->input->post('cat_name'),
            'cat_icon' => $this->input->post('cat_icon') ?: 'fas fa-exclamation-circle',
            'cat_color' => $this->input->post('cat_color') ?: '#e55a2b',
            'cat_order' => $this->input->post('cat_order') ?: 0,
            'cat_status' => $this->input->post('cat_status') ? 1 : 0,
            'cat_created_by' => $this->session->userdata('m_fname')
        ];
        
        if ($this->complain_category_model->add_category($data)) {
            $this->session->set_flashdata('success', 'เพิ่มหมวดหมู่เรียบร้อย');
        }
        redirect('complain_category');
    }
    
    public function update($id) {
        $data = [
            'cat_name' => $this->input->post('cat_name'),
            'cat_icon' => $this->input->post('cat_icon'),
            'cat_color' => $this->input->post('cat_color'),
            'cat_order' => $this->input->post('cat_order'),
            'cat_status' => $this->input->post('cat_status') ? 1 : 0
        ];
        
        if ($this->complain_category_model->update_category($id, $data)) {
            $this->session->set_flashdata('success', 'แก้ไขหมวดหมู่เรียบร้อย');
        }
        redirect('complain_category');
    }
    
    public function delete($id) {
        if ($this->complain_category_model->delete_category($id)) {
            echo json_encode(['success' => true, 'message' => 'ลบเรียบร้อย']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบได้ มีการใช้งานอยู่']);
        }
    }
    
    public function reorder() {
        $order = $this->input->post('order');
        if ($this->complain_category_model->reorder($order)) {
            echo json_encode(['success' => true]);
        }
    }
    
    private function load_view($view, $data = []) {
        $this->load->view('admin/header');
        $this->load->view('admin/navbar');
        $this->load->view('admin/' . $view, $data);
        $this->load->view('admin/footer');
    }
}
