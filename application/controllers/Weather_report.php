<?php defined('BASEPATH') or exit('No direct script access allowed');

class Weather_report extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Weather_report_model');
    }

    public function save_xml()
    {
        // ดึงค่า latitude,longitude จากฐานข้อมูล
        $weather_latlong = get_config_value('weather_latlong');
        
        // URL สำหรับดึงข้อมูล JSON 
        $json_url = 'https://api.weatherapi.com/v1/current.json?key=0101e803f1c94976ae781914240808&q=' . $weather_latlong . '&aqi=no';
        
        // ดึงข้อมูล JSON 
        $json_string = file_get_contents($json_url);
        if ($json_string === false) {
            echo 'ไม่สามารถดึงข้อมูล JSON ได้';
            return;
        }

        $jsonObj = json_decode($json_string, true); // แปลงเป็น array แทนที่จะเป็น object 
        if ($jsonObj === null) {
            echo 'ข้อมูล JSON ไม่ถูกต้อง';
            return;
        }

        $wind_dir = $jsonObj['current']['wind_dir'];

        switch ($wind_dir) {
            case "w":
                $wind_dir = "ทิศตะวันตก";
                break;
            case "e":
                $wind_dir = "ทิศตะวันออก";
                break;
            case "n":
                $wind_dir = "ทิศเหนือ";
                break;
            default:
                $wind_dir = "ทิศใต้";
        }

        // ดึงข้อมูลจังหวัดและอำเภอจากฐานข้อมูล
        $province = get_config_value('province');
        $district = get_config_value('district');
        
        $title = 'รายงานสภาวะอากาศ - ' . $province . ' : อ.' . $district . ' จ.' . $province . ' วันที่ : ' . date("d/m/Y") . ' เวลา ' . date("H:i") . ' นาฬิกา';
        $description = 'อุณหภูมิ : ' . $jsonObj['current']['temp_c'] . ' องศาเซลเซียส ความชื้นสัมพัทธ์ : ' . $jsonObj['current']['humidity'] . ' % ความกดอากาศ : ' . $jsonObj['current']['pressure_mb'] . ' มิลลิบาร์ ทิศทางลม : ' . $wind_dir . ' ความเร็ว ' . $jsonObj['current']['wind_kph'] . ' กม./ชม. ทัศนวิสัย : ' . $jsonObj['current']['vis_km'] . ' กิโลเมตร ฝนสะสมวันนี้ : ' . $jsonObj['current']['precip_mm'] . ' มิลลิเมตร';
        $data = array(
            'title' => (string) $title,
            'pub_date' => date('Y-m-d H:i:s', strtotime((string) date("Y-m-d H:i:s"))),
            'guid' => (string) '$item->guid',
            'link' => (string) '$item->link',
            'author' => (string) '$item->author',
            'description' => strip_tags((string) $description),
            'created_at' => date('Y-m-d H:i:s')
        );
        
        // ลบข้อมูลเดิมออกจากฐานข้อมูล 
        $this->Weather_report_model->clear_weather_reports();
        // บันทึกข้อมูลลงฐานข้อมูล 
        if ($this->Weather_report_model->save_weather_report($data)) {
            echo 'บันทึกรายงานสภาพอากาศสำเร็จ!';
        } else {
            echo 'ไม่สามารถบันทึกรายงานสภาพอากาศได้';
        }
    }
}