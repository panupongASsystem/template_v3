<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller สำหรับทดสอบการตั้งค่าและการเชื่อมต่อ API ตรวจสอบคำไม่สุภาพ
 */
class As_con_test extends CI_Controller
{
    // API Key สำหรับเชื่อมต่อกับระบบตรวจสอบคำไม่สุภาพ
    private $api_key;

    // URL ของ API endpoint
    private $api_url;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');

        // โหลด config ใหม่
        $this->config->load('vulgar_config', TRUE);

        // กำหนดค่า API Key จาก config ไฟล์ใหม่
        $this->api_key = $this->config->item('api_key', 'vulgar_config') ?: '';

        // กำหนด URL ของ API endpoint จาก config ไฟล์ใหม่
        $this->api_url = $this->config->item('api_url', 'vulgar_config') ?: '';

        // โหลด library Vulgar_check
        $this->load->library('vulgar_check');
    }

    /**
     * หน้าหลักแสดงเมนูทดสอบ
     */
    public function index()
    {
        echo '<!DOCTYPE html>
    <html>
    <head>
        <title>ทดสอบระบบตรวจสอบคำไม่สุภาพ</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    </head>
    <body>
    <div class="container mt-4">
        <h1>ทดสอบระบบตรวจสอบคำไม่สุภาพ</h1>
        <div class="alert alert-info">
            <p><strong>สถานะการตั้งค่า:</strong> โค้ดกำลังใช้ค่าที่ตั้งไว้โดยตรงในโค้ด</p>
            <p>API Key: ' . substr($this->api_key, 0, 10) . '...' . substr($this->api_key, -10) . '</p>
            <p>API URL: ' . $this->api_url . '</p>
        </div>
        
        <div class="list-group mt-4">
            <a href="' . site_url('as_con_test/simple_test') . '" class="list-group-item list-group-item-action">
                <h5 class="mb-1">1. ทดสอบอย่างง่าย</h5>
                <p class="mb-1">แสดงค่า API Key และ URL ที่ใช้</p>
            </a>
            <a href="' . site_url('as_con_test/test_api_connection') . '" class="list-group-item list-group-item-action">
                <h5 class="mb-1">2. ทดสอบการเชื่อมต่อกับ API</h5>
                <p class="mb-1">ทดสอบการเชื่อมต่อกับ API ว่าทำงานได้หรือไม่</p>
            </a>
            <a href="' . site_url('as_con_test/interactive_test') . '" class="list-group-item list-group-item-action">
                <h5 class="mb-1">3. ทดสอบแบบโต้ตอบ</h5>
                <p class="mb-1">ทดสอบการตรวจสอบข้อความแบบโต้ตอบ</p>
            </a>
            <a href="' . site_url('as_con_test/test_both_methods') . '" class="list-group-item list-group-item-action">
                <h5 class="mb-1">4. ทดสอบการตรวจสอบทั้งจาก DB Local และ API</h5>
                <p class="mb-1">ทดสอบการตรวจสอบแบบ 2 ชั้น (DB Local และ API) เพื่อแสดงแหล่งที่พบคำไม่สุภาพ</p>
            </a>
        </div>
    </div>
    </body>
    </html>';
    }

    /**
     * ทดสอบแบบไม่ใช้ config file (แสดงค่า API Key และ URL)
     */
    public function simple_test()
    {
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>ทดสอบอย่างง่าย</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
        <div class="container mt-4">
            <h1>ทดสอบระบบโดยไม่ใช้ config file</h1>
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">ค่าที่ใช้ในการเชื่อมต่อกับ API</h5>
                </div>
                <div class="card-body">
                    <p><strong>API Key:</strong> ' . $this->api_key . '</p>
                    <p><strong>API URL:</strong> ' . $this->api_url . '</p>
                    <p>หากต้องการเปลี่ยนแปลงค่าเหล่านี้ ให้แก้ไขใน constructor ของ controller</p>
                </div>
            </div>
            <div class="mt-3">
                <a href="' . site_url('as_con_test') . '" class="btn btn-secondary">กลับหน้าหลัก</a>
            </div>
        </div>
        </body>
        </html>';
    }

    /**
     * ทดสอบการเชื่อมต่อกับ API
     */
    public function test_api_connection()
    {
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>ทดสอบการเชื่อมต่อกับ API</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
        <div class="container mt-4">
            <h1>ทดสอบการเชื่อมต่อกับ API</h1>
            
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">ทดสอบการเชื่อมต่อกับ API ตรวจสอบคำไม่สุภาพ</h5>
                </div>
                <div class="card-body">';

        // ข้อความสำหรับทดสอบ (ปกติและคำไม่สุภาพ)
        $test_texts = [
            'normal' => 'นี่คือข้อความทดสอบปกติที่ไม่มีคำไม่สุภาพ เพื่อทดสอบการเชื่อมต่อกับ API',
            'vulgar' => 'นี่คือข้อความทดสอบที่มีคำไม่สุภาพ เช่น กระหรี่ ควย เย็ด' // ตัวอย่างคำไม่สุภาพ (อาจมีหรือไม่มีในดาต้าเบส)
        ];

        foreach ($test_texts as $type => $text) {
            echo '<h5 class="mt-3">ทดสอบ: ' . ($type == 'normal' ? 'ข้อความปกติ' : 'ข้อความที่อาจมีคำไม่สุภาพ') . '</h5>';
            echo '<p>ข้อความที่ทดสอบ: "' . htmlspecialchars($text) . '"</p>';

            // เริ่มจับเวลา
            $start_time = microtime(true);

            // ทดลองส่งไปยัง API
            $result = $this->_check_with_api($text);

            // สิ้นสุดการจับเวลา
            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time) * 1000; // เวลาในหน่วย ms

            echo '<div class="alert ' . ($result['status'] === 'success' ? 'alert-success' : 'alert-danger') . '">';
            echo '<h6>ผลลัพธ์:</h6>';
            echo '<p>สถานะ: ' . $result['status'] . '</p>';

            if ($result['status'] === 'success') {
                echo '<p>พบคำไม่สุภาพ: ' . ($result['data']['has_vulgar_words'] ? 'ใช่' : 'ไม่') . '</p>';

                if (isset($result['data']['has_vulgar_words']) && $result['data']['has_vulgar_words']) {
                    echo '<p>คำไม่สุภาพที่พบ: ' . implode(', ', $result['data']['vulgar_words']) . '</p>';
                    echo '<p>ข้อความที่ถูกเซ็นเซอร์: ' . $result['data']['censored_content'] . '</p>';
                }
            } else {
                echo '<p>ข้อความผิดพลาด: ' . $result['message'] . '</p>';
            }

            echo '<p>เวลาที่ใช้: ' . number_format($execution_time, 2) . ' ms</p>';
            echo '</div>';

            echo '<pre class="bg-light p-3">';
            print_r($result);
            echo '</pre>';

            echo '<hr>';
        }

        echo '
                    <div class="mt-3">
                        <a href="' . site_url('as_con_test') . '" class="btn btn-secondary">กลับหน้าหลัก</a>
                    </div>
                </div>
            </div>
        </div>
        </body>
        </html>';
    }

    /**
     * ทดสอบแบบโต้ตอบ (ให้ผู้ใช้กรอกข้อความเอง)
     */
    public function interactive_test()
    {
        echo '<!DOCTYPE html>
    <html>
    <head>
        <title>ทดสอบแบบโต้ตอบ</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    </head>
    <body>
    <div class="container mt-4">
        <h1>ทดสอบแบบโต้ตอบ</h1>
        
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">ทดสอบการตรวจสอบคำไม่สุภาพแบบ 2 ชั้น</h5>
            </div>
            <div class="card-body">
                <p class="card-text">กรอกข้อความที่ต้องการตรวจสอบ ระบบจะตรวจสอบว่ามีคำไม่สุภาพหรือไม่ โดยตรวจสอบจาก DB Local ก่อน แล้วจึงตรวจสอบจาก API</p>
                
                <div class="form-group">
                    <label for="test_text" class="form-label">ข้อความที่ต้องการตรวจสอบ</label>
                    <textarea class="form-control" id="test_text" rows="4" placeholder="กรอกข้อความที่ต้องการตรวจสอบ..."></textarea>
                </div>
                
                <div class="mt-3">
                    <button id="btn_check" class="btn btn-primary">ตรวจสอบ</button>
                </div>
                
                <div id="result_container" class="mt-3" style="display:none;">
                    <div class="alert" id="result_alert" role="alert"></div>
                </div>
                
                <div id="raw_result" class="mt-3" style="display:none;">
                    <h5>ข้อมูลการตอบกลับ:</h5>
                    <pre id="raw_response" class="bg-light p-3"></pre>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="' . site_url('as_con_test') . '" class="btn btn-secondary">กลับหน้าหลัก</a>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        $("#btn_check").click(function() {
            const text = $("#test_text").val();
            
            if (!text) {
                alert("กรุณากรอกข้อความที่ต้องการตรวจสอบ");
                return;
            }
            
            // แสดง loading
            $(this).prop("disabled", true).html("<span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span> กำลังตรวจสอบ...");
            
            $.ajax({
                url: "' . site_url('as_con_test/ajax_check_text') . '",
                type: "POST",
                data: { text: text },
                dataType: "json",
                success: function(response) {
                    // คืนค่าปุ่ม
                    $("#btn_check").prop("disabled", false).text("ตรวจสอบ");
                    
                    if (response.status === "success") {
                        if (response.data && response.data.has_vulgar_words) {
                            // พบคำไม่สุภาพ
                            
                            // ตรวจสอบว่ามีข้อมูลแหล่งที่มาหรือไม่
                            let sourceHTML = "";
                            if (response.source) {
                                const sourceBadgeClass = response.source === "local" ? "bg-warning text-dark" : "bg-danger";
                                const sourceText = response.source === "local" ? "ฐานข้อมูล Local" : "API ภายนอก";
                                sourceHTML = `<p><strong>แหล่งที่พบ:</strong> <span class="badge ${sourceBadgeClass}">${sourceText}</span></p>`;
                            }
                            
                            $("#result_alert").removeClass("alert-success").addClass("alert-danger")
                                .html(`<strong>พบคำไม่สุภาพ!</strong> 
                                     <p>คำที่พบ: ${response.data.vulgar_words.join(", ")}</p>
                                     ${sourceHTML}
                                     <hr>
                                     <p>ข้อความที่ถูกเซ็นเซอร์:</p>
                                     <p>${response.data.censored_content}</p>`);
                        } else {
                            // ไม่พบคำไม่สุภาพ
                            $("#result_alert").removeClass("alert-danger").addClass("alert-success")
                                .html("<strong>ไม่พบคำไม่สุภาพ</strong> ข้อความนี้สามารถใช้ได้");
                        }
                    } else {
                        // เกิดข้อผิดพลาด
                        $("#result_alert").removeClass("alert-success").addClass("alert-danger")
                            .html("<strong>เกิดข้อผิดพลาด!</strong> " + response.message);
                    }
                    
                    $("#result_container").show();
                    
                    // แสดงข้อมูล response ทั้งหมด
                    $("#raw_response").text(JSON.stringify(response, null, 2));
                    $("#raw_result").show();
                },
                error: function(xhr, status, error) {
                    // คืนค่าปุ่ม
                    $("#btn_check").prop("disabled", false).text("ตรวจสอบ");
                    
                    // แสดงข้อผิดพลาด
                    $("#result_alert").removeClass("alert-success").addClass("alert-danger")
                        .html("<strong>เกิดข้อผิดพลาดในการเชื่อมต่อ!</strong> กรุณาลองอีกครั้ง");
                    
                    $("#result_container").show();
                    
                    // แสดงข้อมูลข้อผิดพลาด
                    $("#raw_response").text("Error: " + error + "\nStatus: " + status + "\nResponse: " + xhr.responseText);
                    $("#raw_result").show();
                }
            });
        });
    });
    </script>
    </body>
    </html>';
    }

    /**
     * API endpoint สำหรับตรวจสอบข้อความผ่าน AJAX
     */
    public function ajax_check_text()
    {
        // ตรวจสอบว่าเป็นการเรียกผ่าน AJAX หรือไม่
        if (!$this->input->is_ajax_request()) {
            $response = [
                'status' => 'error',
                'message' => 'ต้องเรียกใช้ผ่าน AJAX เท่านั้น'
            ];

            echo json_encode($response);
            return;
        }

        // รับข้อความจาก POST
        $text = $this->input->post('text');

        if (empty($text)) {
            $response = [
                'status' => 'error',
                'message' => 'กรุณาระบุข้อความที่ต้องการตรวจสอบ'
            ];

            echo json_encode($response);
            return;
        }

        // 1. ตรวจสอบจากฐานข้อมูล Local ก่อน
        $local_result = $this->_check_from_local_db($text);

        // ถ้าพบคำไม่สุภาพจาก Local DB
        if (
            $local_result['status'] === 'success' &&
            isset($local_result['data']['has_vulgar_words']) &&
            $local_result['data']['has_vulgar_words']
        ) {

            // เพิ่มข้อมูลแหล่งที่มา
            $local_result['source'] = 'local';

            // ส่งผลลัพธ์กลับ
            echo json_encode($local_result);
            return;
        }

        // 2. ถ้าไม่พบคำไม่สุภาพใน Local DB ให้ตรวจสอบต่อที่ API
        $api_result = $this->_check_with_api($text);

        // ถ้าพบคำไม่สุภาพจาก API
        if (
            $api_result['status'] === 'success' &&
            isset($api_result['data']['has_vulgar_words']) &&
            $api_result['data']['has_vulgar_words']
        ) {

            // เพิ่มข้อมูลแหล่งที่มา
            $api_result['source'] = 'api';
        }

        // ส่งผลลัพธ์กลับไป
        echo json_encode($api_result);
    }

    /**
     * ฟังก์ชันสำหรับตรวจสอบข้อความกับ API
     * เป็นฟังก์ชันกลางที่ทุกฟังก์ชันใช้ร่วมกัน
     * 
     * @param string $text ข้อความที่ต้องการตรวจสอบ
     * @return array ผลการตรวจสอบ
     */
    private function _check_with_api($text)
    {
        // ตั้งค่า cURL
        $ch = curl_init();

        // เตรียมข้อมูลสำหรับส่ง
        $post_data = [
            'api_key' => $this->api_key,
            'content' => $text
        ];

        // กำหนดค่า options สำหรับ cURL
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        // ดำเนินการ cURL และรับผลลัพธ์
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // ปิดการเชื่อมต่อ cURL
        curl_close($ch);

        // ตรวจสอบการเชื่อมต่อ
        if ($error) {
            return [
                'status' => 'error',
                'message' => 'ไม่สามารถเชื่อมต่อกับ API ได้: ' . $error
            ];
        }

        // ตรวจสอบ HTTP status code
        if ($http_code != 200) {
            return [
                'status' => 'error',
                'message' => 'API ส่งค่า HTTP status code ผิดพลาด: ' . $http_code
            ];
        }

        // แปลงข้อมูล JSON เป็น array
        $result = json_decode($response, true);

        // ตรวจสอบว่าสามารถแปลงข้อมูลได้หรือไม่
        if ($result === null) {
            return [
                'status' => 'error',
                'message' => 'ไม่สามารถแปลงข้อมูล JSON ได้',
                'raw_response' => $response
            ];
        }

        // ตรวจสอบว่า API ส่งข้อมูลกลับมาในรูปแบบที่ถูกต้องหรือไม่
        if (!isset($result['status'])) {
            return [
                'status' => 'error',
                'message' => 'API ส่งข้อมูลกลับมาในรูปแบบที่ไม่ถูกต้อง',
                'response' => $result
            ];
        }

        // ส่งผลลัพธ์กลับไป
        return $result;
    }

    /**
     * ทดสอบการตรวจสอบทั้งจาก DB Local และ API
     */
    public function test_both_methods()
    {
        echo '<!DOCTYPE html>
    <html>
    <head>
        <title>ทดสอบการตรวจสอบทั้งจาก DB Local และ API</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container mt-4">
        <h1>ทดสอบการตรวจสอบคำไม่สุภาพจาก 2 แหล่ง</h1>
        
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">ทดสอบการตรวจสอบคำไม่สุภาพจาก DB Local และ API</h5>
            </div>
            <div class="card-body">';

        // ข้อความสำหรับทดสอบ
        $test_texts = [
            'normal' => 'นี่คือข้อความทดสอบที่มีคำไม่สุภาพ เช่น กระหรี่ ควย เย็ด',
            'vulgar_local' => 'นี่คือข้อความทดสอบที่คาดว่าจะมีคำไม่สุภาพใน local DB เช่น กระหรี่ ควย เย็ด',
            'vulgar_api' => 'นี่คือข้อความทดสอบปกติที่ไม่มีคำไม่สุภาพ เพื่อทดสอบการเชื่อมต่อกับระบบ' // ตัวอย่างคำไม่สุภาพที่น่าจะมีใน API
        ];

        // ทดสอบการเชื่อมต่อกับ DB Local
        echo '<h4 class="mt-3">1. ตรวจสอบการเชื่อมต่อกับฐานข้อมูล Local</h4>';
        try {
            $this->load->database();
            $query = $this->db->get('tbl_vulgar');

            if ($query && $query->num_rows() > 0) {
                echo '<div class="alert alert-success">
                <strong>เชื่อมต่อสำเร็จ!</strong> พบข้อมูลในตาราง tbl_vulgar จำนวน ' . $query->num_rows() . ' รายการ
            </div>';

                echo '<h5>ตัวอย่างคำไม่สุภาพในฐานข้อมูล Local:</h5>';
                echo '<ul class="list-group mb-3">';
                foreach ($query->result_array() as $row) {
                    $word_field = isset($row['word']) ? 'word' : (isset($row['vulgar_word']) ? 'vulgar_word' : key($row));
                    echo '<li class="list-group-item">' . htmlspecialchars($row[$word_field]) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<div class="alert alert-warning">
                <strong>เชื่อมต่อสำเร็จ!</strong> แต่ไม่พบข้อมูลในตาราง tbl_vulgar
            </div>';
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">
            <strong>เกิดข้อผิดพลาด!</strong> ไม่สามารถเชื่อมต่อกับฐานข้อมูล: ' . $e->getMessage() . '
        </div>';
        }

        echo '<hr>';

        // ทดสอบการตรวจสอบข้อความแบบ 2 ชั้น
        echo '<h4 class="mt-3">2. ทดสอบการตรวจสอบแบบ 2 ชั้น (DB Local และ API)</h4>';

        foreach ($test_texts as $type => $text) {
            echo '<h5 class="mt-3">ทดสอบ: ' .
                ($type == 'normal' ? 'ข้อความปกติ' :
                    ($type == 'vulgar_local' ? 'ข้อความที่คาดว่าจะพบใน local DB' : 'ข้อความที่คาดว่าจะพบใน API')) .
                '</h5>';
            echo '<p>ข้อความที่ทดสอบ: "' . htmlspecialchars($text) . '"</p>';

            // เริ่มจับเวลา
            $start_time = microtime(true);

            // ทดสอบการตรวจสอบแบบ 2 ชั้น
            $result = $this->_test_combined_check($text);

            // สิ้นสุดการจับเวลา
            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time) * 1000; // เวลาในหน่วย ms

            // แสดงผลลัพธ์
            if ($result['found_vulgar']) {
                $alert_class = 'alert-danger';
                $result_text = '<strong>พบคำไม่สุภาพ!</strong> ';
                $result_text .= 'แหล่งที่ตรวจพบ: <span class="badge ' .
                    ($result['source'] == 'local' ? 'bg-warning' : 'bg-danger') .
                    '">' . ($result['source'] == 'local' ? 'ฐานข้อมูล Local' : 'API ภายนอก') . '</span><br>';
                $result_text .= 'คำที่พบ: ' . implode(', ', $result['vulgar_words']) . '<br>';
                $result_text .= 'ข้อความที่ถูกเซ็นเซอร์: ' . $result['censored_text'];
            } else {
                $alert_class = 'alert-success';
                $result_text = '<strong>ไม่พบคำไม่สุภาพ</strong> ข้อความนี้สามารถใช้ได้';
            }

            echo '<div class="alert ' . $alert_class . '">' . $result_text . '</div>';
            echo '<p>เวลาที่ใช้: ' . number_format($execution_time, 2) . ' ms</p>';

            echo '<hr>';
        }

        echo '
                <div class="mt-3">
                    <a href="' . site_url('as_con_test') . '" class="btn btn-secondary">กลับหน้าหลัก</a>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>';
    }

    /**
     * ฟังก์ชันทดสอบการตรวจสอบคำไม่สุภาพทั้งจาก DB Local และ API
     * เพื่อให้เห็นว่าคำไม่สุภาพถูกตรวจพบจากแหล่งใด
     * 
     * @param string $text ข้อความที่ต้องการตรวจสอบ
     * @return array ผลการตรวจสอบพร้อมแหล่งที่มา
     */
    private function _test_combined_check($text)
    {
        $result = [
            'found_vulgar' => false,
            'source' => '',
            'vulgar_words' => [],
            'censored_text' => $text
        ];

        // 1. ตรวจสอบจากฐานข้อมูล Local ก่อน
        $local_check = $this->_check_from_local_db($text);

        if (
            $local_check['status'] === 'success' &&
            isset($local_check['data']['has_vulgar_words']) &&
            $local_check['data']['has_vulgar_words']
        ) {

            // พบคำไม่สุภาพจาก Local DB
            $result['found_vulgar'] = true;
            $result['source'] = 'local';
            $result['vulgar_words'] = $local_check['data']['vulgar_words'];
            $result['censored_text'] = $local_check['data']['censored_content'];

            return $result;
        }

        // 2. ถ้าไม่พบใน Local DB ให้ตรวจสอบจาก API
        $api_check = $this->_check_with_api($text);

        if (
            $api_check['status'] === 'success' &&
            isset($api_check['data']['has_vulgar_words']) &&
            $api_check['data']['has_vulgar_words']
        ) {

            // พบคำไม่สุภาพจาก API
            $result['found_vulgar'] = true;
            $result['source'] = 'api';
            $result['vulgar_words'] = $api_check['data']['vulgar_words'];
            $result['censored_text'] = $api_check['data']['censored_content'];
        }

        return $result;
    }

    /**
     * ตรวจสอบคำไม่สุภาพจากฐานข้อมูล local
     * 
     * @param string $text ข้อความที่ต้องการตรวจสอบ
     * @return array ผลการตรวจสอบ
     */
    private function _check_from_local_db($text)
    {
        // เตรียมผลลัพธ์เริ่มต้น
        $result = [
            'status' => 'success',
            'data' => [
                'has_vulgar_words' => false,
                'vulgar_words' => [],
                'censored_content' => $text
            ]
        ];

        try {
            // ดึงข้อมูลคำไม่สุภาพจากฐานข้อมูล
            $this->load->database();

            // บันทึกข้อมูลการเรียกใช้เพื่อการ debug
            error_log("Checking for vulgar words in: " . $text);

            $query = $this->db->get('tbl_vulgar');
            error_log("Found " . $query->num_rows() . " vulgar words in database");

            if ($query && $query->num_rows() > 0) {
                $vulgar_words = $query->result_array();
                $found_words = [];
                $censored_text = $text;

                foreach ($vulgar_words as $word_data) {
                    // จากรูปภาพ คอลัมน์ที่เก็บคำไม่สุภาพคือ vulgar_com
                    $vulgar_word = isset($word_data['vulgar_com']) ? $word_data['vulgar_com'] : '';

                    if (empty($vulgar_word))
                        continue;

                    error_log("Checking for vulgar word: " . $vulgar_word);

                    // แก้ไขการใช้ regex ให้ค้นหาคำไม่สุภาพได้ดีขึ้น
                    // ลบ word boundary (\b) เพราะอาจทำให้ไม่ตรงกับคำภาษาไทย
                    $pattern = '/' . preg_quote($vulgar_word, '/') . '/ui';

                    if (preg_match($pattern, $text)) {
                        error_log("Found vulgar word: " . $vulgar_word);
                        $found_words[] = $vulgar_word;

                        // เซ็นเซอร์คำไม่สุภาพ
                        $replacement = str_repeat('*', mb_strlen($vulgar_word, 'UTF-8'));
                        $censored_text = preg_replace($pattern, $replacement, $censored_text);
                    }
                }

                // ถ้าพบคำไม่สุภาพ
                if (!empty($found_words)) {
                    $result['data']['has_vulgar_words'] = true;
                    $result['data']['vulgar_words'] = $found_words;
                    $result['data']['censored_content'] = $censored_text;
                    error_log("Found " . count($found_words) . " vulgar words: " . implode(', ', $found_words));
                } else {
                    error_log("No vulgar words found in local DB");
                }
            } else {
                error_log("No vulgar words in database or query failed");
            }

        } catch (Exception $e) {
            error_log("Error checking local DB: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'ไม่สามารถเชื่อมต่อกับฐานข้อมูลได้: ' . $e->getMessage()
            ];
        }

        return $result;
    }
}