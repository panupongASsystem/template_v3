<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HotNews_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '4']); // 1=ทั้งหมด


        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('hotNews_model');
    }



    public function index()
    {
        $data['query'] = $this->hotNews_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/hotNews', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function editing_hotNews($hotNews_id)
    {
        $data['rsedit'] = $this->hotNews_model->read($hotNews_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/hotNews_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_hotNews($hotNews_id)
    {
        $this->hotNews_model->edit_hotNews($hotNews_id);
        redirect('HotNews_backend', 'refresh');
    }

    public function del_hotNews($hotNews_id)
    {
        $this->hotNews_model->del_hotNews($hotNews_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('Hotnews_backend', 'refresh');
    }

    public function updateHotNewsStatus()
    {
        $this->hotNews_model->updateHotNewsStatus();
    }

    public function insert_hotNews()
    {
        // ✅ ID ที่ต้องการรันทั้งหมด
        $ids = [1, 2, 3];

        foreach ($ids as $hotNews_id) {
            switch ($hotNews_id) {
                case 1:
                    $hotNews_text = get_config_value('fname') .
                        ' อำเภอ' . get_config_value('district') .
                        ' จังหวัด' . get_config_value('province') .
                        ' ยินดีต้อนรับ';
                    break;

                case 2:
                    $hotNews_text = 'ติดต่อ ' . get_config_value('abbreviation') .
                        ' ได้ที่ โทร ' . get_config_value('phone_1') .
                        ', โทรสาร ' . get_config_value('fax');
                    break;

                case 3:
                    $hotNews_text = 'อีเมล : ' . get_config_value('email_1');
                    break;

                default:
                    $hotNews_text = 'ข้อมูลทั่วไปของหน่วยงาน';
                    break;
            }

            // ✅ เตรียมข้อมูล
            $data = [
                'hotNews_id'     => $hotNews_id,
                'hotNews_text'   => $hotNews_text,
                'hotNews_by'     => $this->session->userdata('user_name') ?? 'System',
                'hotNews_status' => 'show'
            ];

            // ✅ ถ้ามีอยู่แล้ว → อัปเดต, ถ้าไม่มี → เพิ่มใหม่
            $exists = $this->db->where('hotNews_id', $hotNews_id)
                ->get('tbl_hotnews')
                ->num_rows();

            if ($exists > 0) {
                // UPDATE
                $this->db->where('hotNews_id', $hotNews_id)
                    ->update('tbl_hotnews', $data);
            } else {
                // INSERT
                $this->db->insert('tbl_hotnews', $data);
            }
        }

        echo "<h3 style='color:green'>✅ เพิ่มหรืออัปเดตข่าว HotNews ทั้ง 3 รายการสำเร็จ</h3>";
    }
}
