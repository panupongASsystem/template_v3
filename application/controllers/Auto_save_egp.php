<?php
class Auto_save_egp extends CI_Controller
{
    private $api_key = 'TH3JFBwJZlaXdDCpcVfSFGuoofCJ1heX';
    private $dept_code;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('auto_save_egp_model');
        
        // ดึงค่า dept_code จากฐานข้อมูล
        $this->dept_code = get_config_value('dept_code');
    }

    public function save_all_years()
    {
        // สร้าง array เก็บผลลัพธ์
        $results = array();

        // เรียกใช้ทุกฟังก์ชันการบันทึกข้อมูล
        $functions = array(
            'save_data_egp_to_database_y2573',
            'save_data_egp_to_database_y2572',
            'save_data_egp_to_database_y2571',
            'save_data_egp_to_database_y2570',
            'save_data_egp_to_database_y2569',
            'save_data_egp_to_database_y2568',
            'save_data_egp_to_database_y2567',
            'save_data_egp_to_database_y2566',
            'save_data_egp_to_database_y2565',
            'save_data_egp_to_database_y2564',
            'save_data_egp_to_database_y2563'
        );

        // วนลูปเรียกใช้แต่ละฟังก์ชัน
        foreach ($functions as $function) {
            ob_start(); // เริ่มเก็บ output
            $this->$function(); // เรียกใช้ฟังก์ชัน
            $result = ob_get_clean(); // รับ output และล้าง buffer

            // เก็บผลลัพธ์
            $results[$function] = $result;
        }

        // แสดงผลลัพธ์ทั้งหมด
        header('Content-Type: application/json');
        echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * ฟังก์ชันกลางสำหรับบันทึกข้อมูล EGP
     * 
     * @param int $year ปีปัจจุบันที่ต้องการดึงข้อมูล (พ.ศ.)
     * @param int $prev_year ปีก่อนหน้า (พ.ศ.)
     * @param string $model_function ชื่อฟังก์ชันใน model ที่ใช้บันทึกข้อมูล
     * @return void
     */
    private function save_data_egp_to_database($year, $prev_year, $model_function)
    {
        $month = date('m'); // เดือนปัจจุบัน
        
        // ตรวจสอบว่ามีการกำหนดค่า dept_code หรือไม่
        if (empty($this->dept_code)) {
            echo "กรุณากำหนดค่า dept_code ในฐานข้อมูล";
            return;
        }

        // กำหนดปีที่จะใช้ดึงข้อมูล ตามเดือนปัจจุบัน
        $query_year = ($month >= 1 && $month <= 11) ? $year : $prev_year;
        
        // สร้าง URL สำหรับเรียก API
        $json_url = "https://govspending.data.go.th/api/service/cgdcontract?api-key={$this->api_key}&dept_code={$this->dept_code}&year={$query_year}&limit=500";
        
        // ดึงข้อมูลจาก API และบันทึกลงในฐานข้อมูล
        $inserted_rows = $this->auto_save_egp_model->$model_function($json_url);
        
        // ตรวจสอบว่ามีข้อมูลถูกบันทึกลงในฐานข้อมูลหรือไม่
        if ($inserted_rows > 0) {
            echo "Data inserted successfully.";
        } else {
            echo "No new data inserted.";
        }
    }

    // ฟังก์ชันสำหรับแต่ละปี เรียกใช้ฟังก์ชันกลาง
    public function save_data_egp_to_database_y2573()
    {
        $this->save_data_egp_to_database(2573, 2572, 'save_data_egp_y2573');
    }
    
    public function save_data_egp_to_database_y2572()
    {
        $this->save_data_egp_to_database(2572, 2571, 'save_data_egp_y2572');
    }
    
    public function save_data_egp_to_database_y2571()
    {
        $this->save_data_egp_to_database(2571, 2570, 'save_data_egp_y2571');
    }
    
    public function save_data_egp_to_database_y2570()
    {
        $this->save_data_egp_to_database(2570, 2569, 'save_data_egp_y2570');
    }
    
    public function save_data_egp_to_database_y2569()
    {
        $this->save_data_egp_to_database(2569, 2568, 'save_data_egp_y2569');
    }
    
    public function save_data_egp_to_database_y2568()
    {
        $this->save_data_egp_to_database(2568, 2567, 'save_data_egp_y2568');
    }
    
    public function save_data_egp_to_database_y2567()
    {
        $this->save_data_egp_to_database(2567, 2566, 'save_data_egp_y2567');
    }
    
    public function save_data_egp_to_database_y2566()
    {
        $this->save_data_egp_to_database(2566, 2565, 'save_data_egp_y2566');
    }
    
    public function save_data_egp_to_database_y2565()
    {
        $this->save_data_egp_to_database(2565, 2564, 'save_data_egp_y2565');
    }
    
    public function save_data_egp_to_database_y2564()
    {
        $this->save_data_egp_to_database(2564, 2563, 'save_data_egp_y2564');
    }
    
    public function save_data_egp_to_database_y2563()
    {
        $this->save_data_egp_to_database(2563, 2562, 'save_data_egp_y2563');
    }
}