<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // ไม่ต้องเช็ค session ในกรณีนี้ เพราะเป็น API endpoint
		$this->load->model('Api_model');
		// Log เมื่อ Controller ถูกสร้าง และโหลด Model สำเร็จ
        log_message('debug', 'API Controller Initialized and models loaded.');
    }

    public function upload_file()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');

        // รับค่า token และ tenant_code
        $token = $this->input->post('token');
        $tenant_code = $this->input->post('tenant_code');

        // ตรวจสอบข้อมูลที่จำเป็น
        if (!$token || !$tenant_code || !isset($_FILES['image'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing required parameters'
            ]);
            return;
        }

        // ตรวจสอบ token
        $valid_token = $this->db->where([
            'token' => $token,
            'tenant_code' => $tenant_code,
            'expires_at >' => date('Y-m-d H:i:s')
        ])->get('auth_tokens')->row();

        if (!$valid_token) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid or expired token'
            ]);
            return;
        }

        // สร้างโฟลเดอร์ตาม tenant_code ถ้ายังไม่มี
        $upload_path = './docs/img/tax';
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        // ตั้งค่าการอัพโหลด
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = false;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('image')) {
            $data = $this->upload->data();

            // สร้าง URL สำหรับเข้าถึงรูปภาพ
            $image_url = base_url('docs/img/tax/' . $data['file_name']);

            echo json_encode([
                'status' => 'success',
                'filename' => $data['file_name'],
                'url' => $image_url
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => $this->upload->display_errors('', '')
            ]);
        }
    }

         // *** เพิ่ม API endpoint ใหม่สำหรับ News System ***
public function upload_back_office()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');

        // รับค่า token, tenant_code และ type
        $token = $this->input->post('token');
        $tenant_code = $this->input->post('tenant_code');
        $upload_type = $this->input->post('type'); // 'image', 'file', หรือ 'video'

        // ตรวจสอบข้อมูลที่จำเป็น
        if (!$token || !$tenant_code || !$upload_type || !isset($_FILES['file'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing required parameters',
                'debug' => [
                    'token' => $token ? 'present' : 'missing',
                    'tenant_code' => $tenant_code ? 'present' : 'missing',
                    'type' => $upload_type ? $upload_type : 'missing',
                    'file' => isset($_FILES['file']) ? 'present' : 'missing'
                ]
            ]);
            return;
        }

        // ตรวจสอบ token (ข้ามการตรวจสอบชั่วคราวเพื่อทดสอบ)
        /*
        $valid_token = $this->db->where([
            'token' => $token,
            'tenant_code' => $tenant_code,
            'expires_at >' => date('Y-m-d H:i:s')
        ])->get('auth_tokens')->row();

        if (!$valid_token) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid or expired token'
            ]);
            return;
        }
        */

        // กำหนด path และ allowed types ตาม type
        $settings = $this->getUploadSettings($upload_type);
        if (!$settings) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid upload type. Supported types: image, file, video'
            ]);
            return;
        }

        // *** ตรวจสอบและสร้างโฟลเดอร์อย่างละเอียด ***
        $upload_path = $settings['path'];
        
        // Log การทำงาน
        log_message('debug', 'Attempting to ensure upload directory: ' . $upload_path);
        log_message('debug', 'Current working directory: ' . getcwd());
        
        // สร้างโฟลเดอร์ parent ก่อนถ้าจำเป็น
        $parent_dirs = [
            './docs/',
            './docs/back_office/'
        ];
        
        foreach ($parent_dirs as $parent_dir) {
            if (!is_dir($parent_dir)) {
                if (mkdir($parent_dir, 0755, true)) {
                    chmod($parent_dir, 0755);
                    log_message('info', 'Created parent directory: ' . $parent_dir);
                } else {
                    log_message('error', 'Failed to create parent directory: ' . $parent_dir);
                }
            }
        }
        
        // สร้างโฟลเดอร์หลัก
        if (!$this->ensureUploadDirectory($upload_path)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Cannot create or access upload directory: ' . $upload_path,
                'debug' => [
                    'upload_path' => $upload_path,
                    'current_dir' => getcwd(),
                    'exists' => is_dir($upload_path) ? 'Yes' : 'No',
                    'writable' => is_writable($upload_path) ? 'Yes' : 'No',
                    'parent_exists' => is_dir(dirname($upload_path)) ? 'Yes' : 'No',
                    'parent_writable' => is_writable(dirname($upload_path)) ? 'Yes' : 'No',
                    'permissions' => is_dir($upload_path) ? substr(sprintf('%o', fileperms($upload_path)), -4) : 'N/A'
                ]
            ]);
            return;
        }
        
        log_message('info', 'Upload directory is ready: ' . $upload_path);

        // ตั้งค่าการอัพโหลด
        $config = array(
            'upload_path' => $upload_path,
            'allowed_types' => $settings['allowed_types'],
            'max_size' => $settings['max_size'],
            'encrypt_name' => FALSE,
            'remove_spaces' => TRUE
        );

        // *** เพิ่ม timeout สำหรับวิดีโอไฟล์ขนาดใหญ่ ***
        if ($upload_type === 'video' || $upload_type === 'file') {
            ini_set('max_execution_time', 300); // 5 นาที
            ini_set('memory_limit', '256M');
        }

        // *** สร้าง upload library instance ใหม่ ***
        $this->load->library('upload');
        $this->upload->initialize($config); // initialize แทน load ใหม่

        // *** Debug config ที่ใช้ ***
        log_message('debug', 'Upload config: ' . json_encode($config));
        log_message('debug', 'Upload path exists: ' . (is_dir($upload_path) ? 'Yes' : 'No'));
        log_message('debug', 'Upload path writable: ' . (is_writable($upload_path) ? 'Yes' : 'No'));
        log_message('debug', 'File info: ' . json_encode($_FILES['file']));

        if ($this->upload->do_upload('file')) {
            $data = $this->upload->data();

            // สร้าง URL สำหรับเข้าถึงไฟล์
            $file_url = base_url($settings['url_path'] . $data['file_name']);

            log_message('info', 'File uploaded successfully: ' . $data['file_name'] . ' (Type: ' . $upload_type . ')');

            echo json_encode([
                'status' => 'success',
                'filename' => $data['file_name'],
                'url' => $file_url,
                'type' => $upload_type,
                'size' => $data['file_size'],
                'file_ext' => $data['file_ext'],
                'file_type' => $data['file_type']
            ]);
        } else {
            $upload_errors = $this->upload->display_errors('', '');
            log_message('error', 'Upload failed: ' . $upload_errors);
            
            echo json_encode([
                'status' => 'error',
                'message' => $upload_errors,
                'debug' => [
                    'upload_path' => $upload_path,
                    'upload_path_exists' => is_dir($upload_path) ? 'Yes' : 'No',
                    'upload_path_writable' => is_writable($upload_path) ? 'Yes' : 'No',
                    'upload_path_realpath' => realpath($upload_path),
                    'current_dir' => getcwd(),
                    'allowed_types' => $settings['allowed_types'],
                    'max_size' => $settings['max_size'],
                    'file_info' => $_FILES['file'],
                    'config_used' => $config
                ]
            ]);
        }
    }

    // *** ฟังก์ชันช่วยสำหรับกำหนดการตั้งค่า ***
    private function getUploadSettings($type)
    {
        $settings = [
            'image' => [
                'path' => './docs/back_office/img/',
                'url_path' => 'docs/back_office/img/',
                'allowed_types' => 'gif|jpg|jpeg|png|webp',
                'max_size' => 2048 // 2MB
            ],
            'file' => [
                'path' => './docs/back_office/file/',
                'url_path' => 'docs/back_office/file/',
                'allowed_types' => 'doc|docx|xls|xlsx|ppt|pptx|pdf|txt|csv|mp4|webm|ogg|avi|m4v|mov|mpg|mpeg|wmv|flv|3gp',
                'max_size' => 102400 // 100MB สำหรับไฟล์และวิดีโอ
            ],
            'video' => [
                'path' => './docs/back_office/video/',
                'url_path' => 'docs/back_office/video/',
                'allowed_types' => 'mp4|webm|ogg|avi|m4v|mov|mpg|mpeg|wmv|flv|3gp',
                'max_size' => 102400 // 100MB สำหรับวิดีโอ
            ]
        ];

        return isset($settings[$type]) ? $settings[$type] : false;
    }

    // *** เพิ่มฟังก์ชันสำหรับตรวจสอบและสร้างโฟลเดอร์ ***
    private function ensureUploadDirectory($path)
    {
        // ถ้าโฟลเดอร์มีอยู่แล้วและเขียนได้ ให้ return true
        if (is_dir($path) && is_writable($path)) {
            return true;
        }

        // ถ้าไม่มีโฟลเดอร์ ให้สร้าง
        if (!is_dir($path)) {
            // สร้างโฟลเดอร์แบบ recursive
            if (!mkdir($path, 0755, true)) {
                log_message('error', 'Cannot create directory: ' . $path);
                return false;
            }
            log_message('info', 'Created directory: ' . $path);
        }

        // ตั้งสิทธิ์ให้ถูกต้อง
        if (!chmod($path, 0755)) {
            log_message('warning', 'Cannot set permissions for: ' . $path);
        }

        // ตรวจสอบว่าสามารถเขียนได้หรือไม่
        if (!is_writable($path)) {
            // ลองปรับสิทธิ์ใหม่
            chmod($path, 0777);
            
            // ตรวจสอบอีกครั้ง
            if (!is_writable($path)) {
                log_message('error', 'Directory not writable after chmod: ' . $path);
                return false;
            }
        }

        log_message('info', 'Directory ready: ' . $path);
        return true;
    }

    // *** API เดิมที่มีอยู่แล้ว ***
    public function upload_img_back_office()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');

        // รับค่า token และ tenant_code
        $token = $this->input->post('token');
        $tenant_code = $this->input->post('tenant_code');

        // ตรวจสอบข้อมูลที่จำเป็น
        if (!$token || !$tenant_code || !isset($_FILES['image'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing required parameters'
            ]);
            return;
        }

        // ตรวจสอบ token (ข้ามชั่วคราว)
        /*
        $valid_token = $this->db->where([
            'token' => $token,
            'tenant_code' => $tenant_code,
            'expires_at >' => date('Y-m-d H:i:s')
        ])->get('auth_tokens')->row();

        if (!$valid_token) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid or expired token'
            ]);
            return;
        }
        */

        // สร้างโฟลเดอร์ตาม tenant_code ถ้ายังไม่มี
        $upload_path = './docs/back_office/img/';
        if (!$this->ensureUploadDirectory($upload_path)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Cannot create upload directory: ' . $upload_path
            ]);
            return;
        }

        // ตั้งค่าการอัพโหลด
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = false;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('image')) {
            $data = $this->upload->data();

            // สร้าง URL สำหรับเข้าถึงรูปภาพ
            $image_url = base_url('docs/back_office/img/' . $data['file_name']);

            echo json_encode([
                'status' => 'success',
                'filename' => $data['file_name'],
                'url' => $image_url
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => $this->upload->display_errors('', '')
            ]);
        }
    }

    public function upload_file_back_office()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');

        // รับค่า token และ tenant_code
        $token = $this->input->post('token');
        $tenant_code = $this->input->post('tenant_code');

        // ตรวจสอบข้อมูลที่จำเป็น
        if (!$token || !$tenant_code || !isset($_FILES['image'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing required parameters'
            ]);
            return;
        }

        // ตรวจสอบ token (ข้ามชั่วคราว)
        /*
        $valid_token = $this->db->where([
            'token' => $token,
            'tenant_code' => $tenant_code,
            'expires_at >' => date('Y-m-d H:i:s')
        ])->get('auth_tokens')->row();

        if (!$valid_token) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid or expired token'
            ]);
            return;
        }
        */

        // สร้างโฟลเดอร์ตาม tenant_code ถ้ายังไม่มี
        $upload_path = './docs/back_office/file/';
        if (!$this->ensureUploadDirectory($upload_path)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Cannot create upload directory: ' . $upload_path
            ]);
            return;
        }

        // ตั้งค่าการอัพโหลด
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'doc|docx|xls|xlsx|ppt|pptx|pdf|txt|csv';
        $config['max_size'] = 5120;
        $config['encrypt_name'] = false;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('image')) { // *** ตรงนี้ควรเป็น 'file' ***
            $data = $this->upload->data();

            // สร้าง URL สำหรับเข้าถึงไฟล์
            $file_url = base_url('docs/back_office/file/' . $data['file_name']);

            echo json_encode([
                'status' => 'success',
                'filename' => $data['file_name'],
                'url' => $file_url
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => $this->upload->display_errors('', '')
            ]);
        }
    }

    // *** เพิ่ม API สำหรับทดสอบ ***
    public function test_connection()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'API is working',
            'timestamp' => date('Y-m-d H:i:s'),
            'server' => $_SERVER['SERVER_NAME'] ?? 'localhost',
            'endpoints' => [
                'upload_back_office' => 'For All Files (requires: token, tenant_code, type[image|file|video], file)',
                'delete_file' => 'For Delete Files (requires: token, tenant_code, filename, type[image|file|video])',
                'test_connection' => 'For Testing API',
                'debug_directories' => 'For Debug Directories'
            ],
            'supported_types' => [
                'image' => 'gif|jpg|jpeg|png|webp (Max: 2MB)',
                'file' => 'doc|docx|xls|xlsx|ppt|pptx|pdf|txt|csv|mp4|webm|ogg|avi|m4v|mov|mpg|mpeg|wmv|flv|3gp (Max: 100MB)',
                'video' => 'mp4|webm|ogg|avi|m4v|mov|mpg|mpeg|wmv|flv|3gp (Max: 100MB)'
            ]
        ]);
    }

    // *** เพิ่ม API สำหรับ debug directories ***
    public function debug_directories()
    {
        header('Content-Type: application/json');
        
        $directories = [
            './docs/',
            './docs/back_office/',
            './docs/back_office/img/',
            './docs/back_office/file/',
            './docs/back_office/video/' // เพิ่มโฟลเดอร์ video
        ];
        
        $results = [];
        foreach ($directories as $dir) {
            $results[$dir] = [
                'exists' => is_dir($dir) ? 'Yes' : 'No',
                'writable' => is_writable($dir) ? 'Yes' : 'No',
                'permissions' => is_dir($dir) ? substr(sprintf('%o', fileperms($dir)), -4) : 'N/A',
                'can_create' => !is_dir($dir) ? (mkdir($dir, 0755, true) ? 'Yes' : 'No') : 'Already exists'
            ];
            
            // ถ้าสร้างได้ ให้ตั้งสิทธิ์
            if (is_dir($dir)) {
                chmod($dir, 0755);
                $results[$dir]['writable_after_chmod'] = is_writable($dir) ? 'Yes' : 'No';
            }
        }
        
        echo json_encode([
            'current_directory' => getcwd(),
            'directories' => $results,
            'php_settings' => [
                'max_execution_time' => ini_get('max_execution_time'),
                'memory_limit' => ini_get('memory_limit'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size')
            ]
        ], JSON_PRETTY_PRINT);
    }
	
	/**
 * API สำหรับลบไฟล์
 */
public function delete_file()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, DELETE');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');

        // รับค่า
        $token = $this->input->post('token');
        $tenant_code = $this->input->post('tenant_code');
        $filename = $this->input->post('filename');
        $type = $this->input->post('type'); // 'image', 'file', หรือ 'video'

        // ตรวจสอบข้อมูล
        if (!$token || !$tenant_code || !$filename || !$type) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing required parameters'
            ]);
            return;
        }

        try {
            // กำหนด path ตาม type
            switch ($type) {
                case 'image':
                    $path = './docs/back_office/img/';
                    break;
                case 'video':
                    $path = './docs/back_office/video/';
                    break;
                case 'file':
                default:
                    $path = './docs/back_office/file/';
                    break;
            }
            
            $file_path = $path . $filename;

            // ลบไฟล์
            if (file_exists($file_path)) {
                if (unlink($file_path)) {
                    log_message('info', 'File deleted successfully: ' . $file_path);
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'File deleted successfully',
                        'filename' => $filename,
                        'type' => $type
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to delete file'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'File not found',
                    'file_path' => $file_path
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
	
	/**
     * API สำหรับคำนวณพื้นที่ใช้งาน
     */
    public function get_folder_size()
    {
        header('Content-Type: application/json');
        
        try {
            $upload_folder = './docs';
            $used_space = $this->calculateFolderSize($upload_folder);
            $used_space_gb = $used_space / (1024 * 1024 * 1024);
            
            echo json_encode([
                'status' => 'success',
                'used_space_gb' => round($used_space_gb, 4),
                'used_space_bytes' => $used_space,
                'folder_path' => $upload_folder
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * ฟังก์ชันคำนวณขนาดโฟลเดอร์
     */
    private function calculateFolderSize($folder)
    {
        $used_space = 0;
        
        if (!is_dir($folder)) {
            return 0;
        }

        try {
            $files = scandir($folder);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $path = $folder . '/' . $file;
                    if (is_file($path)) {
                        $used_space += filesize($path);
                    } elseif (is_dir($path)) {
                        $used_space += $this->calculateFolderSize($path);
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error calculating folder size: ' . $e->getMessage());
        }
        
        return $used_space;
    }
	
    /**
     * เมธอดสำหรับสร้างการจองห้องประชุมใหม่
     * รับข้อมูลผ่าน HTTP POST method
     */
    public function create_booking()
    {
        // Log เมื่อฟังก์ชันถูกเรียก
        log_message('debug', 'create_booking method started.');

        // --- การตั้งค่า Response Header เป็น JSON ---
        header('Content-Type: application/json');

        // --- ตรวจสอบว่า Request มาจากเมธอด POST หรือไม่ ---
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            log_message('warning', 'Invalid request method for create_booking. Method: ' . $this->input->server('REQUEST_METHOD'));
            $response = [
                'status'  => 'error',
                'message' => 'Invalid request method. Please use POST.'
            ];
            $this->output->set_status_header(405);
            echo json_encode($response);
            return;
        }

        // Log ข้อมูล POST ทั้งหมดที่ได้รับ
        log_message('debug', 'Received POST data: ' . json_encode($this->input->post()));

        // --- รับค่าจาก POST request ---
        $detail   = $this->input->post('calender_detail');
        $date     = $this->input->post('calender_date');
        $date_end = $this->input->post('calender_date_end');
        $by       = $this->input->post('calender_by');
        $view     = $this->input->post('calender_view');

        // --- ตรวจสอบข้อมูลที่จำเป็น ---
        if (empty($detail) || empty($date_end) || empty($by) || !isset($view)) {
            log_message('error', 'Missing required fields for create_booking. Data: ' . json_encode($this->input->post()));
            $response = [
                'status'  => 'error',
                'message' => 'Missing required fields: calender_detail, calender_date_end, calender_by, calender_view are required.'
            ];
            $this->output->set_status_header(400);
            echo json_encode($response);
            return;
        }

        // --- เตรียมข้อมูลสำหรับส่งให้ Model ---
        $data = [
            'calender_detail'   => $detail,
            'calender_date'     => empty($date) ? NULL : $date,
            'calender_date_end' => $date_end,
            'calender_by'       => $by,
            'calender_view'     => $view
        ];
        
        // Log ข้อมูลที่เตรียมจะส่งให้ Model
        log_message('debug', 'Data prepared for model: ' . json_encode($data));

        // --- เรียกใช้ Model เพื่อบันทึกข้อมูล ---
        if ($this->Api_model->insert_booking($data)) {
            log_message('info', 'Booking created successfully via API.');
            $response = [
                'status'  => 'success',
                'message' => 'Booking created successfully.'
            ];
            $this->output->set_status_header(201); // Created
        } else {
            log_message('error', 'Failed to create booking. Api_model->insert_booking() returned false.');
            $response = [
                'status'  => 'error',
                'message' => 'Failed to create booking. Please check your data or contact administrator.'
            ];
            $this->output->set_status_header(500); // Internal Server Error
        }
        
        echo json_encode($response);
    }
}
?>