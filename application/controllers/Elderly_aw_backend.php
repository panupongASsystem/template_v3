<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Elderly_aw_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
         $this->check_access_permission(['1', '51']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('elderly_aw_model');
        $this->load->library('csvimport');
    }
    

public function index()
    {
        $data['query'] = $this->elderly_aw_model->list();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/elderly_aw', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding_elderly_aw()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/elderly_aw_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_elderly_aw()
    {
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->elderly_aw_model->add_elderly_aw();
        redirect('elderly_aw_backend', 'refresh');
    }

    public function editing_elderly_aw($elderly_aw_id)
    {
        $data['rsedit'] = $this->elderly_aw_model->read_elderly_aw($elderly_aw_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/elderly_aw_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_elderly_aw($elderly_aw_id)
    {
        $this->elderly_aw_model->edit_elderly_aw($elderly_aw_id);
        redirect('elderly_aw_backend', 'refresh');
    }

    public function del_elderly_aw($elderly_aw_id)
    {
        $this->elderly_aw_model->del_elderly_aw($elderly_aw_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('elderly_aw_backend', 'refresh');
    }

    public function importcsv()
    {
        $data['query'] = $this->elderly_aw_model->list();
        $data['error'] = '';    //initialize image upload error array to empty

        $config['upload_path'] = './docs/file/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = '10000';

        $this->load->library('upload', $config);

        // If upload failed, display error
        if (!$this->upload->do_upload()) {
            $data['error'] = $this->upload->display_errors();

            $this->load->view('templat/header');
            $this->load->view('asset/css');
            $this->load->view('templat/navbar_system_admin');
            $this->load->view('system_admin/elderly_aw', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        } else {
            $file_data = $this->upload->data();
            $file_path = './docs/file/' . $file_data['file_name'];

            if ($csv_array = $this->csvimport->get_array($file_path)) {
                // Define expected columns and their alternative names
                $column_map = array(
                    'elderly_aw_id_num_eligible' => ['elderly_aw_id_num_eligible'],
                    'elderly_aw_name_eligible' => ['elderly_aw_name_eligible'],
                    'elderly_aw_id_num_owner' => ['elderly_aw_id_num_owner', 'elderly_aw_id_num_owne'], // Allow for alternative names
                    'elderly_aw_name_owner' => ['elderly_aw_name_owner', 'elderly_aw_name_owne'],
                    'elderly_aw_agency' => ['elderly_aw_agency'],
                    'elderly_aw_bank' => ['elderly_aw_bank'],
                    'elderly_aw_type_payment' => ['elderly_aw_type_payment', 'elderly_aw_type_payme'],
                    'elderly_aw_bank_num' => ['elderly_aw_bank_num'],
                    'elderly_aw_period_payment' => ['elderly_aw_period_payment', 'elderly_aw_period_payme'],
                    'elderly_aw_money' => ['elderly_aw_money'],
                    'elderly_aw_note' => ['elderly_aw_note']
                );

                foreach ($csv_array as $row) {
                    $insert_data = array();

                    // Map CSV row data to expected columns
                    foreach ($column_map as $expected_col => $possible_names) {
                        foreach ($possible_names as $name) {
                            if (isset($row[$name])) {
                                $insert_data[$expected_col] = mb_convert_encoding($row[$name], 'UTF-8', 'auto');
                                break;
                            } else {
                                // If no matching column is found, set an empty string or handle it as needed
                                $insert_data[$expected_col] = '';
                            }
                        }
                    }
                    $this->elderly_aw_model->insert_csv($insert_data);
                }

                $this->session->set_flashdata('save_success', TRUE);
                redirect(base_url() . 'Elderly_aw_backend');
            } else {
                $data['error'] = "Error occurred";
                $this->load->view('templat/header');
                $this->load->view('asset/css');
                $this->load->view('templat/navbar_system_admin');
                $this->load->view('system_admin/elderly_aw', $data);
                $this->load->view('asset/js');
                $this->load->view('templat/footer');
            }
        }
    }
}
