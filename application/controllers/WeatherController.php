<?php
defined('BASEPATH') or exit('No direct script access allowed');

class WeatherController extends CI_Controller {
    public function loadWeatherData() {
        $weatherData = $this->fetchWeatherData();
        if ($weatherData !== FALSE) {
            echo json_encode($weatherData);
        } else {
            echo json_encode([]);
        }
    }

    private function fetchWeatherData() {
        // URL ของ API
        $api_url = 'https://www.tmd.go.th/api/xml/weather-report?stationnumber=48374';

        // ตั้งค่า options สำหรับการร้องขอ HTTP
        $options = [
            'http' => [
                'method' => 'GET',
                'ignore_errors' => true, // ละเว้นข้อผิดพลาด HTTP เพื่อจัดการเอง
				'timeout' => 20, // ตั้งค่า timeout เป็น 20 วินาที
            ],
        ];

        // สร้าง context สำหรับการร้องขอด้วย options ที่ตั้งไว้
        $context = stream_context_create($options);

        // ดึงข้อมูลจาก API โดยใช้ file_get_contents พร้อม context ที่ตั้งไว้
        $api_data = @file_get_contents($api_url, false, $context);

        // ตรวจสอบว่าข้อมูลถูกดึงมาสำเร็จหรือไม่
        if ($api_data === FALSE) {
            // Log ข้อผิดพลาดหรือแคชข้อมูลเก่าเพื่อใช้ในกรณีที่การดึงข้อมูลล้มเหลว
            // echo 'Failed to fetch data from API.';
            return FALSE;
        }

        // แปลงข้อมูลจาก XML เป็น SimpleXMLElement
        $xml_data = @simplexml_load_string($api_data, "SimpleXMLElement", LIBXML_NOCDATA);

        // ตรวจสอบว่าการแปลง XML เป็น Object สำเร็จหรือไม่
        if ($xml_data === FALSE) {
            echo 'Failed to decode XML data.';
            return FALSE;
        }

        // แปลง Object เป็น Array
        $json_data = json_decode(json_encode($xml_data), TRUE);

        return $json_data;
    }
}