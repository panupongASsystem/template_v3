<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Theme_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        // เช็ค step 1 ระบบที่เลือกตรงมั้ย
        if (
            $this->session->userdata('m_system') != 'system_admin' &&
            $this->session->userdata('m_system') != 'super_admin'
        ) {
            redirect('User/logout', 'refresh');
        }

        $this->load->model('Theme_model');
        $this->load->helper('url');
    }

    public function index()
    {
        $data['current_theme'] = $this->Theme_model->get_current_theme();
        $data['theme_history'] = $this->Theme_model->get_theme_history(5); // ดึงประวัติ 5 รายการล่าสุด
        $data['page_title'] = 'จัดการธีมระบบ';

        $this->load->view('templat/header');
        $this->load->view('asset/css', $data);
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/theme_management', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function save_theme()
    {
        // ตรวจสอบว่าเป็น AJAX request
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // รับข้อมูลจากฟอร์ม
        $primary_color = $this->input->post('primary_color');
        $gradient_start = $this->input->post('gradient_start');
        $gradient_end = $this->input->post('gradient_end');
        $theme_name = $this->input->post('theme_name');

        // ตรวจสอบข้อมูล
        if (empty($primary_color) || empty($gradient_start) || empty($gradient_end)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน'
            ]);
            return;
        }

        // ตรวจสอบรูปแบบสี (Hex color)
        if (
            !$this->is_valid_hex_color($primary_color) ||
            !$this->is_valid_hex_color($gradient_start) ||
            !$this->is_valid_hex_color($gradient_end)
        ) {
            echo json_encode([
                'status' => 'error',
                'message' => 'รูปแบบสีไม่ถูกต้อง'
            ]);
            return;
        }

        $theme_data = array(
            'primary_color' => $primary_color,
            'gradient_start' => $gradient_start,
            'gradient_end' => $gradient_end,
            'theme_name' => $theme_name ?: 'Custom Theme',
            'updated_by' => $this->session->userdata('m_fname'), // เก็บ ID ของผู้อัปเดต
            'updated_at' => date('Y-m-d H:i:s')
        );

        try {
            $result = $this->Theme_model->save_theme($theme_data);

            if ($result) {
                // สร้างไฟล์ CSS ใหม่
                $this->generate_theme_css($theme_data);

                // ทำความสะอาดธีมเก่า (เก็บแค่ 10 records ล่าสุด)
                $this->Theme_model->cleanup_old_themes(10);

                // อัปเดต session หรือ cache ถ้าจำเป็น
                $this->session->set_userdata('theme_updated', time());

                echo json_encode([
                    'status' => 'success',
                    'message' => 'บันทึกธีมเรียบร้อยแล้ว',
                    'theme_id' => $result
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ไม่สามารถบันทึกธีมได้'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    public function reset_theme()
    {
        // รีเซ็ตกลับเป็นธีมเริ่มต้น
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $default_theme = array(
            'primary_color' => '#179CB1',
            'gradient_start' => '#667eea',
            'gradient_end' => '#764ba2',
            'theme_name' => 'Default Theme',
            'updated_by' => $this->session->userdata('m_fname'), // เก็บ ID ของผู้รีเซ็ต
            'updated_at' => date('Y-m-d H:i:s')
        );

        try {
            $result = $this->Theme_model->save_theme($default_theme);

            if ($result) {
                $this->generate_theme_css($default_theme);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'รีเซ็ตธีมเป็นค่าเริ่มต้นเรียบร้อยแล้ว'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ไม่สามารถรีเซ็ตธีมได้'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    public function get_theme_history()
    {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        try {
            // รับค่า limit จาก parameter (default = 10)
            $limit = $this->input->get('limit') ? (int)$this->input->get('limit') : 10;
            
            $history = $this->Theme_model->get_theme_history($limit);

            if (!empty($history)) {
                // จัดรูปแบบข้อมูลให้ดูง่าย
                $formatted_history = array();
                foreach ($history as $item) {
                    $formatted_history[] = array(
                        'id' => $item->id,
                        'theme_name' => $item->theme_name ?: 'Unknown Theme',
                        'primary_color' => $item->primary_color ?: '#179CB1',
                        'gradient_start' => $item->gradient_start ?: '#667eea',
                        'gradient_end' => $item->gradient_end ?: '#764ba2',
                        'updated_by' => $item->updated_by ?: 'N/A',
                        'updated_at' => $item->updated_at ? date('d/m/Y H:i', strtotime($item->updated_at)) : 'N/A',
                        'created_at' => $item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : 'N/A'
                    );
                }

                echo json_encode([
                    'status' => 'success',
                    'data' => $formatted_history,
                    'count' => count($formatted_history),
                    'total_records' => $this->Theme_model->count_themes()
                ]);
            } else {
                echo json_encode([
                    'status' => 'success',
                    'data' => [],
                    'message' => 'ไม่มีประวัติการเปลี่ยนธีม',
                    'count' => 0
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Get Theme History Error: ' . $e->getMessage());
            
            echo json_encode([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    public function apply_theme_from_history()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $theme_id = $this->input->post('theme_id');
        
        if (!$theme_id) {
            echo json_encode([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลธีมที่ต้องการ'
            ]);
            return;
        }

        try {
            $theme = $this->Theme_model->get_theme_by_id($theme_id);
            
            if ($theme) {
                // สร้าง record ใหม่สำหรับการใช้ธีมจากประวัติ
                $new_theme_data = array(
                    'primary_color' => $theme->primary_color,
                    'gradient_start' => $theme->gradient_start,
                    'gradient_end' => $theme->gradient_end,
                    'theme_name' => $theme->theme_name . ' (Applied from History)',
                    'updated_by' => $this->session->userdata('m_fname'),
                    'updated_at' => date('Y-m-d H:i:s')
                );

                $result = $this->Theme_model->save_theme($new_theme_data);

                if ($result) {
                    $this->generate_theme_css($new_theme_data);
                    
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'ใช้ธีมจากประวัติเรียบร้อยแล้ว'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'ไม่สามารถใช้ธีมได้'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ไม่พบธีมที่ต้องการ'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    private function generate_theme_css($theme_data)
    {
        // สร้างเนื้อหา CSS
        $css_content = "/* Auto-generated Theme CSS - " . date('Y-m-d H:i:s') . " */
        
:root {
    --primary-color: {$theme_data['primary_color']};
    --gradient-start: {$theme_data['gradient_start']};
    --gradient-end: {$theme_data['gradient_end']};
}

.bg-gradient-custom {
    background-color: {$theme_data['primary_color']} !important;
}

.btn-custom {
    background-color: {$theme_data['primary_color']} !important;
    border-color: {$theme_data['primary_color']} !important;
}

.btn-custom:hover {
    background-color: {$theme_data['primary_color']} !important;
    border-color: {$theme_data['primary_color']} !important;
    opacity: 0.9;
}

.btn-custom:focus, .btn-custom:active {
    background-color: {$theme_data['primary_color']} !important;
    border-color: {$theme_data['primary_color']} !important;
    box-shadow: 0 0 0 0.2rem rgba(" . $this->hex_to_rgb($theme_data['primary_color']) . ", 0.25);
}

/* Sidebar Gradient */
.sidebar.bg-gradient-custom {
    background: linear-gradient(135deg, {$theme_data['primary_color']} 0%, {$theme_data['gradient_start']} 100%) !important;
}

/* Footer Gradient */
.sticky-footer {
    background: linear-gradient(135deg, {$theme_data['gradient_start']} 0%, {$theme_data['gradient_end']} 100%) !important;
}

/* Scroll to top button */
.scroll-to-top {
    background: linear-gradient(135deg, {$theme_data['gradient_start']}, {$theme_data['gradient_end']}) !important;
}

/* Additional theme elements */
.text-primary {
    color: {$theme_data['primary_color']} !important;
}

.border-primary {
    border-color: {$theme_data['primary_color']} !important;
}

/* Progress bars */
.progress-bar {
    background-color: {$theme_data['primary_color']} !important;
}

/* Links */
a {
    color: {$theme_data['primary_color']};
}

a:hover {
    color: {$theme_data['primary_color']};
    opacity: 0.8;
}
";

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        $css_dir = FCPATH . 'assets/css/';
        if (!is_dir($css_dir)) {
            mkdir($css_dir, 0755, true);
        }

        // บันทึกไฟล์ CSS
        $css_file = $css_dir . 'theme.css';
        if (file_put_contents($css_file, $css_content) === false) {
            throw new Exception('ไม่สามารถสร้างไฟล์ CSS ได้');
        }

        return true;
    }

    private function is_valid_hex_color($color)
    {
        return preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color);
    }

    private function hex_to_rgb($hex)
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return "$r, $g, $b";
    }

    // ฟังก์ชัน debug สำหรับตรวจสอบข้อมูล
    public function debug_theme_data()
    {
        // ใช้เฉพาะในโหมด development
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }
        
        echo "<h3>Theme Debug Information</h3>";
        
        // ตรวจสอบโครงสร้างตาราง
        $fields = $this->db->list_fields('system_theme');
        echo "<h4>Table Fields:</h4><pre>";
        print_r($fields);
        echo "</pre>";
        
        // ตรวจสอบข้อมูลในตาราง
        $query = $this->db->get('system_theme');
        echo "<h4>Table Data:</h4><pre>";
        print_r($query->result());
        echo "</pre>";
        
        echo "<h4>Total Records: " . $query->num_rows() . "</h4>";
        
        // ตรวจสอบ current theme
        $current = $this->Theme_model->get_current_theme();
        echo "<h4>Current Theme:</h4><pre>";
        print_r($current);
        echo "</pre>";
    }

    public function test_history()
    {
        $history = $this->Theme_model->get_theme_history(10);
        echo '<pre>';
        print_r($history);
        echo '</pre>';

        echo "จำนวนข้อมูล: " . count($history);
    }
}