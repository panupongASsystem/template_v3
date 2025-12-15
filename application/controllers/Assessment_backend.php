<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assessment_backend extends MY_Controller
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

    

    public function index()
    {
        $data['query'] = $this->assessment_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/assessment', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function del_assessment($assessment_id)
    {
        $this->assessment_model->del_assessment($assessment_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('assessment_backend');
    }
	
	    public function export_csv()
    {
        // ตั้งค่า header
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="แบบประเมินความพึงพอใจในการให้บริการ_' . date('Y-m-d') . '.csv"');

        // เปิด output stream
        $output = fopen('php://output', 'w');

        // เพิ่ม BOM สำหรับ UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // สร้าง header row
        fputcsv($output, array(
            'ลำดับ',
            'เพศ',
            'อายุ',
            'ระดับการศึกษา',
            'อาชีพ',
            'อาชีพอื่นๆ',
            'การให้บริการเป็นไปตามระยะเวลาที่กำหนด',
            'ความรวดเร็วในการให้บริการ',
            'ได้รับบริการตรงตามความต้องการ',
            'ความพึงพอใจโดยภาพรวม',
            'ความเหมาะสมในการแต่งกาย',
            'ความเต็มใจและความพร้อมในการให้บริการ',
            'ความรู้ความสามารถในการให้บริการ',
            'การให้บริการเหมือนกันทุกราย',
            'ความซื่อสัตย์สุจริต',
            'ความสุภาพ กิริยามารยาท',
            'สถานที่ตั้งของหน่วยงาน',
            'ความชัดเจนของป้าย',
            'ความเพียงพอของสิ่งอำนวยความสะดวก',
            'ความสะอาด',
            'ความเป็นระเบียบ',
            'ข้อเสนอแนะ',
            'วันที่ประเมิน'
        ));

        // ดึงข้อมูล
        $data = $this->assessment_model->list_all();

        // เพิ่มข้อมูลแต่ละแถว
        $i = 1;
        foreach ($data as $row) {
            fputcsv($output, array(
                $i,
                $row->assessment_gender,
                $row->assessment_age,
                $row->assessment_study,
                $row->assessment_occupation,
                $row->assessment_occupation_etc,
                $row->assessment_11,
                $row->assessment_12,
                $row->assessment_13,
                $row->assessment_14,
                $row->assessment_21,
                $row->assessment_22,
                $row->assessment_23,
                $row->assessment_24,
                $row->assessment_25,
                $row->assessment_26,
                $row->assessment_31,
                $row->assessment_32,
                $row->assessment_33,
                $row->assessment_34,
                $row->assessment_35,
                $row->assessment_suggestion,
                date('d/m/Y H:i', strtotime($row->assessment_datesave . '+543 years'))
            ));
            $i++;
        }

        fclose($output);
        exit;
    }
}
