<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assessment_report_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
               $this->check_access_permission(['1', '125']); // 1=ทั้งหมด


        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('assessment_model');
    }

    private function check_session_timeout()
    {
        $timeout = 900; // 15 นาที
        $last_activity = $this->session->userdata('last_activity');

        if ($last_activity && (time() - $last_activity > $timeout)) {
            $this->session->sess_destroy();
            redirect('User/logout', 'refresh');
        } else {
            $this->session->set_userdata('last_activity', time());
        }
    }

    public function index()
    {
        // รับปีจาก request (ตัวอย่างใช้ GET)
        $year = $this->input->get('year'); // หรือ $this->input->post('year');

        // แบ่งตามเพศ
        $data['gender_all'] = $this->assessment_model->count_gender_all($year);
        $data['gender_male'] = $this->assessment_model->count_gender_male($year);
        $data['gender_female'] = $this->assessment_model->count_gender_female($year);

        // แบ่งอายุ
        $data['age_20_down_all'] = $this->assessment_model->count_age_20_down_all($year);
        $data['age_20_down_female'] = $this->assessment_model->count_age_20_down_female($year);
        $data['age_20_down_male'] = $this->assessment_model->count_age_20_down_male($year);
        $data['age_21_40_all'] = $this->assessment_model->count_age_21_40_all($year);
        $data['age_21_40_female'] = $this->assessment_model->count_age_21_40_female($year);
        $data['age_21_40_male'] = $this->assessment_model->count_age_21_40_male($year);
        $data['age_41_60_all'] = $this->assessment_model->count_age_41_60_all($year);
        $data['age_41_60_female'] = $this->assessment_model->count_age_41_60_female($year);
        $data['age_41_60_male'] = $this->assessment_model->count_age_41_60_male($year);
        $data['age_60_up_all'] = $this->assessment_model->count_age_60_up_all($year);
        $data['age_60_up_female'] = $this->assessment_model->count_age_60_up_female($year);
        $data['age_60_up_male'] = $this->assessment_model->count_age_60_up_male($year);

        // แบ่งตามการศึกษา
        $data['study_primary_all'] = $this->assessment_model->count_study_primary_all($year);
        $data['study_primary_male'] = $this->assessment_model->count_study_primary_male($year);
        $data['study_primary_female'] = $this->assessment_model->count_study_primary_female($year);
        $data['study_high_all'] = $this->assessment_model->count_study_high_all($year);
        $data['study_high_male'] = $this->assessment_model->count_study_high_male($year);
        $data['study_high_female'] = $this->assessment_model->count_study_high_female($year);
        $data['study_bachelor_all'] = $this->assessment_model->count_study_bachelor_all($year);
        $data['study_bachelor_male'] = $this->assessment_model->count_study_bachelor_male($year);
        $data['study_bachelor_female'] = $this->assessment_model->count_study_bachelor_female($year);
        $data['study_up_bachelor_all'] = $this->assessment_model->count_study_up_bachelor_all($year);
        $data['study_up_bachelor_male'] = $this->assessment_model->count_study_up_bachelor_male($year);
        $data['study_up_bachelor_female'] = $this->assessment_model->count_study_up_bachelor_female($year);

        // แบ่งตามอาชีพ
        $data['occupation_student_all'] = $this->assessment_model->count_occupation_student_all($year);
        $data['occupation_student_male'] = $this->assessment_model->count_occupation_student_male($year);
        $data['occupation_student_female'] = $this->assessment_model->count_occupation_student_female($year);
        $data['occupation_gov_all'] = $this->assessment_model->count_occupation_gov_all($year);
        $data['occupation_gov_male'] = $this->assessment_model->count_occupation_gov_male($year);
        $data['occupation_gov_female'] = $this->assessment_model->count_occupation_gov_female($year);
        $data['occupation_private_all'] = $this->assessment_model->count_occupation_private_all($year);
        $data['occupation_private_male'] = $this->assessment_model->count_occupation_private_male($year);
        $data['occupation_private_female'] = $this->assessment_model->count_occupation_private_female($year);
        $data['occupation_community_all'] = $this->assessment_model->count_occupation_community_all($year);
        $data['occupation_community_male'] = $this->assessment_model->count_occupation_community_male($year);
        $data['occupation_community_female'] = $this->assessment_model->count_occupation_community_female($year);
        $data['occupation_farmer_all'] = $this->assessment_model->count_occupation_farmer_all($year);
        $data['occupation_farmer_male'] = $this->assessment_model->count_occupation_farmer_male($year);
        $data['occupation_farmer_female'] = $this->assessment_model->count_occupation_farmer_female($year);
        $data['occupation_other_all'] = $this->assessment_model->count_occupation_other_all($year);
        $data['occupation_other_male'] = $this->assessment_model->count_occupation_other_male($year);
        $data['occupation_other_female'] = $this->assessment_model->count_occupation_other_female($year);

        // แบ่งตามด้านต่างๆ
        $data['count_assessment_id'] = $this->assessment_model->count_assessment_id($year);
        $data['sum_assessment_1'] = $this->assessment_model->sum_assessment_1($year);
        $data['sum_assessment_2'] = $this->assessment_model->sum_assessment_2($year);
        $data['sum_assessment_3'] = $this->assessment_model->sum_assessment_3($year);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/assessment_report', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function select_year()
    {
        // รับปีจาก input (ตัวอย่างอาจใช้ post หรือ get ขึ้นอยู่กับการใช้งาน)
        $year = $this->input->get('year'); // หรือ $this->input->post('year');

        // เรียกใช้งาน model
        $data['total'] = $this->assessment_model->count_gender_all($year);

        // ส่งข้อมูลไปยัง view
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/assessment_report', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
}
