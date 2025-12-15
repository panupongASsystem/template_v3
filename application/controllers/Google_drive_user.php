<?php
// application/controllers/Google_drive_user.php (แก้ไข 401 Error)
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Drive User Controller v1.1.0 (Token Fix)
 * แก้ไขปัญหา 401 Authentication Error
 * เพิ่มระบบตรวจสอบและ Refresh Token อัตโนมัติ
 * 
 * @author   System Developer
 * @version  1.1.0 (Token Fix)
 * @since    2025-01-20
 */
class Google_drive_user extends CI_Controller {

    private $google_client;
    private $drive_service;
    private $use_curl_mode = true; // ใช้ cURL เป็นหลักเพื่อหลีกเลี่ยงปัญหา

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Google_drive_model');
        $this->load->model('Google_drive_system_model');
        
        // ตรวจสอบการล็อกอิน
        if (!$this->session->userdata('m_id')) {
            redirect('User');
        }

        // เริ่มต้น Google Client (แบบปลอดภัย)
        $this->init_google_client_safe();
    }

    /**
     * เริ่มต้น Google Client แบบปลอดภัย (หลีกเลี่ยง Monolog Error)
     */
    private function init_google_client_safe() {
        try {
            // ใช้ cURL mode เป็นหลัก เนื่องจากมีความเสถียรมากกว่า
            $this->use_curl_mode = true;
            
            // ลองสร้าง Google Client สำหรับ basic operations
            if (class_exists('Google\\Client')) {
                try {
                    $client_id = $this->get_setting('google_client_id');
                    $client_secret = $this->get_setting('google_client_secret');
                    
                    if (!empty($client_id) && !empty($client_secret)) {
                        $this->google_client = new Google\Client();
                        $this->google_client->setClientId($client_id);
                        $this->google_client->setClientSecret($client_secret);
                        
                        // ไม่สร้าง Drive Service เพื่อหลีกเลี่ยง Monolog Error
                        log_message('info', 'Google Client initialized in safe mode (cURL primary)');
                    }
                } catch (Exception $e) {
                    log_message('warning', 'Google Client safe init failed: ' . $e->getMessage());
                    $this->google_client = null;
                }
            }
            
            log_message('info', 'Google Drive User Controller initialized with cURL mode');

        } catch (Exception $e) {
            log_message('error', 'Init Google Client safe error: ' . $e->getMessage());
        }
    }

    /**
     * หน้าแรกของ User Google Drive
     */
    public function index() {
        $data['user_info'] = $this->get_user_info();
        $data['shared_folders'] = $this->get_user_shared_folders();
        $data['available_folders'] = $this->get_available_folders_for_user();
        $data['user_google_account'] = $this->get_user_google_account();
        
        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_user_dashboard', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

    
    /**
     * ตรวจสอบและ Refresh System Token อัตโนมัติ
     */
    private function check_and_refresh_system_token() {
        try {
            $system_storage = $this->get_system_storage_info();
            if (!$system_storage) {
                return ['valid' => false, 'message' => 'ไม่พบ System Storage'];
            }

            if (!$system_storage->google_access_token) {
                return ['valid' => false, 'message' => 'ไม่พบ Access Token'];
            }

            $token_data = json_decode($system_storage->google_access_token, true);
            if (!$token_data || !isset($token_data['access_token'])) {
                return ['valid' => false, 'message' => 'Token format ไม่ถูกต้อง'];
            }

            // ตรวจสอบว่า Token หมดอายุหรือไม่
            if ($system_storage->google_token_expires) {
                $expires_time = strtotime($system_storage->google_token_expires);
                $current_time = time();
                
                // ถ้าหมดอายุภายใน 5 นาที ให้ลอง refresh
                if ($expires_time <= ($current_time + 300)) {
                    log_message('info', 'Access token expires soon, attempting refresh...');
                    
                    if (isset($token_data['refresh_token']) && !empty($token_data['refresh_token'])) {
                        $refresh_result = $this->refresh_access_token($token_data['refresh_token']);
                        if ($refresh_result) {
                            log_message('info', 'Successfully refreshed access token');
                            return ['valid' => true, 'message' => 'Token refreshed successfully'];
                        } else {
                            log_message('error', 'Failed to refresh access token');
                            return ['valid' => false, 'message' => 'ไม่สามารถ Refresh Token ได้'];
                        }
                    } else {
                        return ['valid' => false, 'message' => 'Token หมดอายุและไม่มี Refresh Token'];
                    }
                }
            }

            // ทดสอบ Token โดยการเรียก Google API
            if ($this->test_token_validity($token_data['access_token'])) {
                return ['valid' => true, 'message' => 'Token ใช้งานได้'];
            } else {
                return ['valid' => false, 'message' => 'Token ไม่สามารถใช้งานได้'];
            }

        } catch (Exception $e) {
            log_message('error', 'Check and refresh token error: ' . $e->getMessage());
            return ['valid' => false, 'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ Token'];
        }
    }

    /**
     * Refresh Access Token
     */
    private function refresh_access_token($refresh_token) {
        try {
            $client_id = $this->get_setting('google_client_id');
            $client_secret = $this->get_setting('google_client_secret');

            if (empty($client_id) || empty($client_secret)) {
                log_message('error', 'OAuth credentials not configured for token refresh');
                return false;
            }

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://oauth2.googleapis.com/token',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query([
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'refresh_token' => $refresh_token,
                    'grant_type' => 'refresh_token'
                ]),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/x-www-form-urlencoded'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                log_message('error', 'cURL error in token refresh: ' . $error);
                return false;
            }

            if ($http_code === 200) {
                $data = json_decode($response, true);
                if ($data && isset($data['access_token'])) {
                    // อัปเดต Token ในฐานข้อมูล
                    $new_token_data = [
                        'access_token' => $data['access_token'],
                        'token_type' => $data['token_type'] ?? 'Bearer',
                        'expires_in' => $data['expires_in'] ?? 3600,
                        'refresh_token' => $refresh_token // เก็บ refresh token เดิม
                    ];

                    if (isset($data['refresh_token'])) {
                        $new_token_data['refresh_token'] = $data['refresh_token'];
                    }

                    $this->update_system_token($new_token_data);
                    return true;
                }
            }

            log_message('error', 'Token refresh failed: HTTP ' . $http_code . ' - ' . $response);
            return false;

        } catch (Exception $e) {
            log_message('error', 'Refresh access token error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดต System Token
     */
    private function update_system_token($token_data) {
        try {
            $expires_at = date('Y-m-d H:i:s', time() + ($token_data['expires_in'] ?? 3600));
            
            $this->db->where('is_active', 1)
                    ->update('tbl_google_drive_system_storage', [
                        'google_access_token' => json_encode($token_data),
                        'google_token_expires' => $expires_at,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

            log_message('info', 'System token updated successfully');
            return true;

        } catch (Exception $e) {
            log_message('error', 'Update system token error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ทดสอบความถูกต้องของ Token
     */
    private function test_token_validity($access_token) {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . urlencode($access_token),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                log_message('error', 'Token validity test cURL error: ' . $error);
                return false;
            }

            if ($http_code === 200) {
                $data = json_decode($response, true);
                return ($data && isset($data['scope']));
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Test token validity error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * แชร์โฟลเดอร์ด้วย cURL แบบ Enhanced (Fixed 500 Error)
     */
    private function share_folder_with_curl_enhanced($folder_id, $user_email, $permission_level) {
        try {
            log_message('info', "Starting cURL enhanced folder sharing for folder: {$folder_id}");

            // ตรวจสอบ System Storage
            $system_storage = $this->get_system_storage_info();
            if (!$system_storage) {
                throw new Exception('No system storage found');
            }

            if (empty($system_storage->google_access_token)) {
                throw new Exception('No system storage access token');
            }

            // Parse token data
            $token_data = json_decode($system_storage->google_access_token, true);
            if (!$token_data || !isset($token_data['access_token'])) {
                throw new Exception('Invalid access token format');
            }

            $access_token = $token_data['access_token'];
            log_message('info', 'Access token retrieved successfully');

            // ตรวจสอบว่าโฟลเดอร์มีอยู่จริงก่อน
            if (!$this->verify_folder_exists($folder_id, $access_token)) {
                throw new Exception('Folder not found or inaccessible');
            }

            log_message('info', 'Folder existence verified, proceeding with permission creation');

            // สร้าง Permission data
            $permission_data = [
                'emailAddress' => $user_email,
                'type' => 'user',
                'role' => $permission_level
            ];

            // เตรียม cURL request
            $url = "https://www.googleapis.com/drive/v3/files/{$folder_id}/permissions";
            $email_message = urlencode('คุณได้รับการแชร์โฟลเดอร์จากระบบ Google Drive องค์กร');
            $url .= "?emailMessage={$email_message}";

            $ch = curl_init();
            
            // ตั้งค่า cURL options อย่างละเอียด
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($permission_data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_TIMEOUT => 90, // เพิ่ม timeout
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_MAXREDIRS => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'User-Agent: Google-Drive-System/1.0'
                ],
                CURLOPT_VERBOSE => false
            ]);

            log_message('info', 'Executing cURL request to Google Drive API');

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            $curl_info = curl_getinfo($ch);
            
            curl_close($ch);

            // Log detailed information
            log_message('info', "cURL response - HTTP Code: {$http_code}, Error: " . ($error ?: 'None') . ", Response length: " . strlen($response));

            // ตรวจสอบ cURL errors
            if ($error) {
                throw new Exception('cURL Error: ' . $error);
            }

            // ตรวจสอบ HTTP response
            if ($http_code === 200) {
                $permission_response = json_decode($response, true);
                
                if ($permission_response && isset($permission_response['id'])) {
                    log_message('info', 'Permission created successfully, fetching folder info');
                    
                    // ดึงข้อมูลโฟลเดอร์
                    $folder_info = $this->get_folder_info_with_curl($folder_id, $access_token);
                    
                    $result = [
                        'success' => true,
                        'permission_id' => $permission_response['id'],
                        'folder_url' => $folder_info['webViewLink'] ?? "https://drive.google.com/drive/folders/{$folder_id}",
                        'folder_name' => $folder_info['name'] ?? 'Unknown Folder',
                        'shared_at' => date('Y-m-d H:i:s')
                    ];
                    
                    log_message('info', 'Folder sharing completed successfully');
                    return $result;
                } else {
                    throw new Exception('Invalid permission response format - missing permission ID');
                }
            } else {
                // Parse error response
                $error_response = json_decode($response, true);
                $error_message = "HTTP Error: {$http_code}";
                
                if ($error_response) {
                    if (isset($error_response['error'])) {
                        if (isset($error_response['error']['message'])) {
                            $error_message .= ' - ' . $error_response['error']['message'];
                        }
                        if (isset($error_response['error']['code'])) {
                            $error_message .= ' (Error Code: ' . $error_response['error']['code'] . ')';
                        }
                        if (isset($error_response['error']['status'])) {
                            $error_message .= ' (Status: ' . $error_response['error']['status'] . ')';
                        }
                    }
                } else {
                    $error_message .= ' - Response: ' . substr($response, 0, 200);
                }
                
                throw new Exception($error_message);
            }

        } catch (Exception $e) {
            log_message('error', 'Share folder with cURL enhanced error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
    }

    /**
     * ตรวจสอบว่าโฟลเดอร์มีอยู่จริง
     */
    private function verify_folder_exists($folder_id, $access_token) {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$folder_id}?fields=id,name,mimeType",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $data = json_decode($response, true);
                return ($data && isset($data['id']));
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Verify folder exists error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูลโฟลเดอร์ด้วย cURL
     */
    private function get_folder_info_with_curl($folder_id, $access_token) {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$folder_id}?fields=name,webViewLink,mimeType",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                return json_decode($response, true);
            }

            return [];

        } catch (Exception $e) {
            log_message('error', 'Get folder info with cURL error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ตรวจสอบสถานะ Google Drive Service
     */
    public function check_service_status() {
        try {
            $status = [
                'google_client_available' => ($this->google_client !== null),
                'drive_service_available' => ($this->drive_service !== null),
                'system_storage_available' => false,
                'access_token_valid' => false,
                'can_share_folders' => false,
                'token_expires_at' => null,
                'token_status' => 'unknown'
            ];

            // ตรวจสอบ System Storage
            $system_storage = $this->get_system_storage_info();
            if ($system_storage) {
                $status['system_storage_available'] = true;
                
                if ($system_storage->google_access_token) {
                    $token_data = json_decode($system_storage->google_access_token, true);
                    if ($token_data && isset($token_data['access_token'])) {
                        
                        // ตรวจสอบวันหมดอายุ
                        if ($system_storage->google_token_expires) {
                            $status['token_expires_at'] = $system_storage->google_token_expires;
                            
                            $expires_time = strtotime($system_storage->google_token_expires);
                            $current_time = time();
                            
                            if ($expires_time > $current_time) {
                                $status['token_status'] = 'valid';
                                $status['access_token_valid'] = true;
                                
                                // ทดสอบการเข้าถึง Google Drive API
                                $status['can_share_folders'] = $this->test_google_drive_access($token_data['access_token']);
                            } else {
                                $status['token_status'] = 'expired';
                            }
                        } else {
                            // ไม่มีข้อมูลวันหมดอายุ ลองทดสอบ
                            if ($this->test_token_validity($token_data['access_token'])) {
                                $status['access_token_valid'] = true;
                                $status['token_status'] = 'valid';
                                $status['can_share_folders'] = $this->test_google_drive_access($token_data['access_token']);
                            } else {
                                $status['token_status'] = 'invalid';
                            }
                        }
                    } else {
                        $status['token_status'] = 'invalid_format';
                    }
                } else {
                    $status['token_status'] = 'missing';
                }
            }

            $this->output_json_success($status, 'ตรวจสอบสถานะเรียบร้อย');

        } catch (Exception $e) {
            $this->output_json_error('ไม่สามารถตรวจสอบสถานะได้: ' . $e->getMessage());
        }
    }

    /**
     * ทดสอบการเข้าถึง Google Drive API
     */
    private function test_google_drive_access($access_token) {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://www.googleapis.com/drive/v3/about?fields=user,storageQuota',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                log_message('error', 'Google Drive access test cURL error: ' . $error);
                return false;
            }

            if ($http_code === 200) {
                $data = json_decode($response, true);
                return ($data && isset($data['user']));
            }

            log_message('error', 'Google Drive access test HTTP error: ' . $http_code);
            return false;

        } catch (Exception $e) {
            log_message('error', 'Test Google Drive access error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * หน้าขอเข้าใช้งาน Google Drive
     */
    public function request_access() {
        if ($this->input->post()) {
            $this->process_access_request();
        } else {
            $data['user_info'] = $this->get_user_info();
            $data['system_storage'] = $this->get_system_storage_info();
            
            $this->load->view('member/header');
            $this->load->view('member/css');
            $this->load->view('member/sidebar');
            $this->load->view('member/google_drive_user_request', $data);
            $this->load->view('member/js');
            $this->load->view('member/footer');
        }
    }

    /**
     * ประมวลผลคำขอเข้าใช้งาน
     */
    private function process_access_request() {
        try {
            $user_google_email = $this->input->post('user_google_email');
            $requested_folders = $this->input->post('requested_folders');
            $access_reason = $this->input->post('access_reason');

            if (empty($user_google_email) || empty($requested_folders)) {
                $this->session->set_flashdata('error', 'กรุณากรอกข้อมูลให้ครบถ้วน');
                redirect('google_drive_user/request_access');
            }

            // ตรวจสอบว่าเป็น Google Email
            if (!filter_var($user_google_email, FILTER_VALIDATE_EMAIL)) {
                $this->session->set_flashdata('error', 'กรุณาใส่ Google Email ที่ถูกต้อง');
                redirect('google_drive_user/request_access');
            }

            // บันทึกคำขอ
            $request_data = [
                'member_id' => $this->session->userdata('m_id'),
                'user_google_email' => $user_google_email,
                'requested_folders' => json_encode($requested_folders),
                'access_reason' => $access_reason,
                'request_status' => 'pending',
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent()
            ];

            $this->save_access_request($request_data);

            // ถ้าเป็น auto-approve (ตามสิทธิ์)
            if ($this->should_auto_approve()) {
                $this->auto_approve_request($request_data);
                $this->session->set_flashdata('success', 'คำขอได้รับการอนุมัติโดยอัตโนมัติ กำลังแชร์โฟลเดอร์...');
            } else {
                $this->session->set_flashdata('success', 'ส่งคำขอเรียบร้อย รอการอนุมัติจากผู้ดูแลระบบ');
            }

            redirect('google_drive_user');

        } catch (Exception $e) {
            log_message('error', 'Process access request error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            redirect('google_drive_user/request_access');
        }
    }

    /**
     * Auto-approve คำขอ
     */
    private function auto_approve_request($request_data) {
        try {
            $requested_folders = json_decode($request_data['requested_folders'], true);
            $successful_shares = 0;
            $failed_shares = 0;
            
            foreach ($requested_folders as $folder_id) {
                $result = $this->share_folder_with_curl_enhanced(
                    $folder_id, 
                    $request_data['user_google_email'], 
                    'reader'
                );

                if ($result && $result['success']) {
                    $this->save_sharing_record([
                        'folder_id' => $folder_id,
                        'shared_by' => 0, // System auto-approve
                        'shared_to_email' => $request_data['user_google_email'],
                        'permission_level' => 'reader',
                        'google_permission_id' => $result['permission_id'],
                        'shared_at' => date('Y-m-d H:i:s'),
                        'auto_approved' => 1,
                        'method_used' => 'auto_approve_curl'
                    ]);
                    $successful_shares++;
                    
                    log_message('info', 'Auto-approved and shared folder: ' . $folder_id . ' to ' . $request_data['user_google_email']);
                } else {
                    $failed_shares++;
                    log_message('error', 'Failed to auto-approve folder: ' . $folder_id . ' - ' . ($result['error'] ?? 'Unknown error'));
                }
            }

            if ($successful_shares > 0) {
                log_message('info', "Auto-approved {$successful_shares} folders for " . $request_data['user_google_email']);
            }

            if ($failed_shares > 0) {
                log_message('warning', "Failed to auto-approve {$failed_shares} folders for " . $request_data['user_google_email']);
            }

        } catch (Exception $e) {
            log_message('error', 'Auto approve request error: ' . $e->getMessage());
        }
    }

    // ===========================================
    // Database Helper Methods
    // ===========================================

    /**
     * ดึงข้อมูล User
     */
    private function get_user_info() {
        return $this->db->select('m.*, p.pname')
                       ->from('tbl_member m')
                       ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                       ->where('m.m_id', $this->session->userdata('m_id'))
                       ->get()
                       ->row();
    }

    /**
     * ดึง Google Account ของ User
     */
    private function get_user_google_account() {
        $user = $this->get_user_info();
        return $user ? $user->google_email : null;
    }

    /**
     * ดึงโฟลเดอร์ที่ User ถูกแชร์
     */
    private function get_user_shared_folders() {
        try {
            $user = $this->get_user_info();
            if (!$user || !$user->google_email) {
                return [];
            }

            $this->create_sharing_table_if_not_exists();

            return $this->db->select('gs.*, gsf.folder_name, gsf.folder_path, gsf.folder_type')
                           ->from('tbl_google_drive_sharing gs')
                           ->join('tbl_google_drive_system_folders gsf', 'gs.folder_id = gsf.folder_id', 'left')
                           ->where('gs.shared_to_email', $user->google_email)
                           ->where('gs.is_active', 1)
                           ->order_by('gs.shared_at', 'desc')
                           ->get()
                           ->result();

        } catch (Exception $e) {
            log_message('error', 'Get user shared folders error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงโฟลเดอร์ที่ User สามารถขอเข้าถึงได้
     */
    private function get_available_folders_for_user() {
        try {
            $user = $this->get_user_info();
            if (!$user) {
                return [];
            }

            // ดึงโฟลเดอร์ตามสิทธิ์ของตำแหน่ง
            $position_id = $user->ref_pid;
            
            $this->create_system_folders_table_if_not_exists();

            $query = $this->db->select('*')
                            ->from('tbl_google_drive_system_folders')
                            ->where('is_active', 1);

            // กำหนดสิทธิ์ตามตำแหน่ง
            if (in_array($position_id, [1, 2])) {
                // Admin/Super Admin - เข้าถึงได้ทุกโฟลเดอร์
            } elseif ($position_id == 3) {
                // User Admin - เข้าถึงได้ทุกโฟลเดอร์ยกเว้น Admin
                $query->where('folder_type !=', 'admin');
            } else {
                // End User - เข้าถึงได้เฉพาะโฟลเดอร์ของตำแหน่งตัวเองและ Shared
                $query->where('(folder_type = "shared" OR created_for_position = ' . $position_id . ')');
            }

            return $query->order_by('folder_type', 'ASC')
                        ->order_by('folder_name', 'ASC')
                        ->get()
                        ->result();

        } catch (Exception $e) {
            log_message('error', 'Get available folders for user error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อมูล System Storage (Fixed)
     */
    private function get_system_storage_info() {
        try {
            // ตรวจสอบว่าตารางมีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                log_message('error', 'Table tbl_google_drive_system_storage does not exist');
                return null;
            }

            $result = $this->db->where('is_active', 1)
                              ->get('tbl_google_drive_system_storage')
                              ->row();

            if (!$result) {
                log_message('info', 'No active system storage found');
                return null;
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Get system storage info error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * บันทึกคำขอเข้าใช้งาน
     */
    private function save_access_request($data) {
        try {
            $this->create_access_requests_table_if_not_exists();
            return $this->db->insert('tbl_google_drive_access_requests', $data);

        } catch (Exception $e) {
            log_message('error', 'Save access request error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * บันทึกข้อมูลการแชร์ (Fixed)
     */
    private function save_sharing_record($data) {
        try {
            // สร้างตารางถ้ายังไม่มี
            $this->create_sharing_table_if_not_exists();
            
            // เพิ่มข้อมูลเพิ่มเติม
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['is_active'] = 1;
            
            $result = $this->db->insert('tbl_google_drive_sharing', $data);
            
            if (!$result) {
                log_message('error', 'Failed to save sharing record: ' . $this->db->last_query());
                log_message('error', 'Database error: ' . $this->db->error()['message']);
            } else {
                log_message('info', 'Sharing record saved successfully with ID: ' . $this->db->insert_id());
            }
            
            return $result;

        } catch (Exception $e) {
            log_message('error', 'Save sharing record error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบว่า User สามารถเข้าถึงโฟลเดอร์ได้หรือไม่
     */
    private function can_user_access_folder($member_id, $folder_id) {
        $user = $this->db->where('m_id', $member_id)->get('tbl_member')->row();
        if (!$user) return false;

        $folder = $this->db->where('folder_id', $folder_id)
                          ->get('tbl_google_drive_system_folders')
                          ->row();
        if (!$folder) return false;

        $position_id = $user->ref_pid;

        // Admin มีสิทธิ์ทุกโฟลเดอร์
        if (in_array($position_id, [1, 2])) {
            return true;
        }

        // User Admin เข้าถึงได้ทุกโฟลเดอร์ยกเว้น Admin
        if ($position_id == 3 && $folder->folder_type != 'admin') {
            return true;
        }

        // End User เข้าถึงได้เฉพาะโฟลเดอร์ของตำแหน่งและ Shared
        if ($folder->folder_type == 'shared' || 
            $folder->created_for_position == $position_id) {
            return true;
        }

        return false;
    }

    /**
     * ตรวจสอบว่าควร Auto-approve หรือไม่
     */
    private function should_auto_approve() {
        $user = $this->get_user_info();
        if (!$user) return false;

        // Auto-approve สำหรับ Admin และ User Admin
        return in_array($user->ref_pid, [1, 2, 3]);
    }

    /**
     * ดึงการตั้งค่า
     */
    private function get_setting($key, $default = null) {
        try {
            if ($this->db->table_exists('tbl_google_drive_settings')) {
                $result = $this->db->select('setting_value')
                                  ->from('tbl_google_drive_settings')
                                  ->where('setting_key', $key)
                                  ->where('is_active', 1)
                                  ->get()
                                  ->row();

                if ($result) {
                    return $result->setting_value;
                }
            }

            return $default;

        } catch (Exception $e) {
            return $default;
        }
    }

    // ===========================================
    // Table Creation Methods
    // ===========================================

    /**
     * สร้างตารางการแชร์
     */
    private function create_sharing_table_if_not_exists() {
        if (!$this->db->table_exists('tbl_google_drive_sharing')) {
            $sql = "
                CREATE TABLE IF NOT EXISTS `tbl_google_drive_sharing` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `folder_id` varchar(255) NOT NULL COMMENT 'Google Drive Folder ID',
                    `shared_by` int(11) NOT NULL COMMENT 'ผู้แชร์ (member_id)',
                    `shared_to_email` varchar(255) NOT NULL COMMENT 'Google Email ที่รับการแชร์',
                    `permission_level` enum('reader','commenter','writer') DEFAULT 'reader',
                    `google_permission_id` varchar(255) DEFAULT NULL COMMENT 'Permission ID จาก Google',
                    `shared_at` datetime DEFAULT current_timestamp(),
                    `is_active` tinyint(1) DEFAULT 1,
                    `revoked_at` datetime DEFAULT NULL,
                    `revoked_by` int(11) DEFAULT NULL,
                    `auto_approved` tinyint(1) DEFAULT 0,
                    `method_used` varchar(50) DEFAULT 'unknown',
                    PRIMARY KEY (`id`),
                    KEY `idx_folder_id` (`folder_id`),
                    KEY `idx_shared_to` (`shared_to_email`),
                    KEY `idx_shared_by` (`shared_by`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ";
            $this->db->query($sql);
        }
    }

    /**
     * สร้างตารางคำขอเข้าใช้งาน
     */
    private function create_access_requests_table_if_not_exists() {
        if (!$this->db->table_exists('tbl_google_drive_access_requests')) {
            $sql = "
                CREATE TABLE IF NOT EXISTS `tbl_google_drive_access_requests` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `member_id` int(11) NOT NULL COMMENT 'ผู้ขอ',
                    `user_google_email` varchar(255) NOT NULL COMMENT 'Google Email ของผู้ขอ',
                    `requested_folders` text NOT NULL COMMENT 'รายการโฟลเดอร์ที่ขอ (JSON)',
                    `access_reason` text DEFAULT NULL COMMENT 'เหตุผลในการขอ',
                    `request_status` enum('pending','approved','rejected') DEFAULT 'pending',
                    `approved_by` int(11) DEFAULT NULL,
                    `approved_at` datetime DEFAULT NULL,
                    `ip_address` varchar(45) DEFAULT NULL,
                    `user_agent` text DEFAULT NULL,
                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`),
                    KEY `idx_member_id` (`member_id`),
                    KEY `idx_status` (`request_status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ";
            $this->db->query($sql);
        }
    }

    /**
     * สร้างตาราง System Folders (ถ้ายังไม่มี)
     */
    private function create_system_folders_table_if_not_exists() {
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            $sql = "
                CREATE TABLE IF NOT EXISTS `tbl_google_drive_system_folders` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `folder_name` varchar(255) NOT NULL COMMENT 'ชื่อโฟลเดอร์',
                    `folder_id` varchar(255) NOT NULL COMMENT 'Google Drive Folder ID',
                    `parent_folder_id` varchar(255) DEFAULT NULL COMMENT 'Parent Folder ID',
                    `folder_type` enum('system','department','shared','user','admin') DEFAULT 'system' COMMENT 'ประเภทโฟลเดอร์',
                    `folder_path` varchar(500) DEFAULT NULL COMMENT 'Path ของโฟลเดอร์',
                    `created_for_position` int(11) DEFAULT NULL COMMENT 'สร้างสำหรับตำแหน่งไหน (อ้างอิง tbl_position)',
                    `permission_level` enum('public','restricted','private') DEFAULT 'restricted' COMMENT 'ระดับการเข้าถึง',
                    `folder_description` text DEFAULT NULL COMMENT 'คำอธิบายโฟลเดอร์',
                    `storage_quota` bigint(20) DEFAULT 1073741824 COMMENT 'Quota สำหรับโฟลเดอร์นี้ (1GB default)',
                    `storage_used` bigint(20) DEFAULT 0 COMMENT 'พื้นที่ที่ใช้ไปแล้ว',
                    `is_active` tinyint(1) DEFAULT 1 COMMENT 'สถานะการใช้งาน',
                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    `created_by` int(11) DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_folder_id` (`folder_id`),
                    KEY `idx_folder_type` (`folder_type`),
                    KEY `idx_parent_folder` (`parent_folder_id`),
                    KEY `idx_position` (`created_for_position`),
                    KEY `idx_active` (`is_active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='โครงสร้างโฟลเดอร์ใน System Storage';
            ";
            $this->db->query($sql);
        }
    }

    // ===========================================
    // Utility Methods
    // ===========================================

    /**
     * Output JSON Success (Fixed)
     */
    private function output_json_success($data = [], $message = 'Success') {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        // ตั้งค่า headers
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_header('Cache-Control: no-cache, must-revalidate')
            ->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT')
            ->set_output(json_encode([
                'success' => true,
                'message' => $message,
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        
        // Force output
        $this->output->_display();
        exit;
    }

    /**
     * Output JSON Error (Fixed)
     */
    private function output_json_error($message = 'Error', $status_code = 400) {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        // ตั้งค่า headers
        $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json', 'utf-8')
            ->set_header('Cache-Control: no-cache, must-revalidate')
            ->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT')
            ->set_output(json_encode([
                'success' => false,
                'message' => $message,
                'error_code' => $status_code,
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        
        // Force output
        $this->output->_display();
        exit;
    }

    // ===========================================
    // Additional Methods for Complete Functionality
    // ===========================================

    /**
     * ดูโฟลเดอร์ที่ถูกแชร์
     */
    public function my_shared_folders() {
        $data['user_info'] = $this->get_user_info();
        $data['shared_folders'] = $this->get_user_shared_folders();
        $data['access_requests'] = $this->get_user_access_requests();
        
        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_user_folders', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

    /**
     * ดึงคำขอเข้าใช้งานของ User
     */
    private function get_user_access_requests() {
        try {
            $this->create_access_requests_table_if_not_exists();

            return $this->db->select('*')
                           ->from('tbl_google_drive_access_requests')
                           ->where('member_id', $this->session->userdata('m_id'))
                           ->order_by('created_at', 'desc')
                           ->get()
                           ->result();

        } catch (Exception $e) {
            log_message('error', 'Get user access requests error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ทดสอบการแชร์โฟลเดอร์ (สำหรับ Debug)
     */
    public function test_share() {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
            }

            $test_folder_id = $this->input->post('folder_id');
            $test_email = $this->input->post('test_email');

            if (empty($test_folder_id) || empty($test_email)) {
                $this->output_json_error('กรุณาระบุ folder_id และ test_email');
                return;
            }

            // ตรวจสอบ Token ก่อน
            $token_check = $this->check_and_refresh_system_token();
            if (!$token_check['valid']) {
                $this->output_json_error('Token Error: ' . $token_check['message']);
                return;
            }

            // ทดสอบการแชร์
            $result = $this->share_folder_with_curl_enhanced($test_folder_id, $test_email, 'reader');

            if ($result && $result['success']) {
                $this->output_json_success($result, 'ทดสอบการแชร์สำเร็จ');
            } else {
                $this->output_json_error('ทดสอบการแชร์ล้มเหลว: ' . ($result['error'] ?? 'Unknown error'));
            }

        } catch (Exception $e) {
            $this->output_json_error('เกิดข้อผิดพลาดในการทดสอบ: ' . $e->getMessage());
        }
    }

    /**
     * Debug Token Status
     */
    public function debug_token() {
        echo "<h1>Debug Token Status</h1>";
        
        try {
            $system_storage = $this->get_system_storage_info();
            if (!$system_storage) {
                echo "<p>❌ No system storage found</p>";
                return;
            }

            echo "<h2>System Storage Info</h2>";
            echo "<p>Email: " . htmlspecialchars($system_storage->google_account_email) . "</p>";
            echo "<p>Token Expires: " . $system_storage->google_token_expires . "</p>";
            
            if ($system_storage->google_access_token) {
                $token_data = json_decode($system_storage->google_access_token, true);
                if ($token_data) {
                    echo "<h3>Token Data</h3>";
                    echo "<p>Has Access Token: " . (isset($token_data['access_token']) ? '✅' : '❌') . "</p>";
                    echo "<p>Has Refresh Token: " . (isset($token_data['refresh_token']) ? '✅' : '❌') . "</p>";
                    echo "<p>Token Type: " . ($token_data['token_type'] ?? 'Unknown') . "</p>";
                    
                    if (isset($token_data['access_token'])) {
                        echo "<h3>Token Validity Test</h3>";
                        if ($this->test_token_validity($token_data['access_token'])) {
                            echo "<p>✅ Token is valid</p>";
                            
                            if ($this->test_google_drive_access($token_data['access_token'])) {
                                echo "<p>✅ Google Drive API accessible</p>";
                            } else {
                                echo "<p>❌ Google Drive API not accessible</p>";
                            }
                        } else {
                            echo "<p>❌ Token is invalid or expired</p>";
                            
                            if (isset($token_data['refresh_token'])) {
                                echo "<h3>Refresh Token Test</h3>";
                                if ($this->refresh_access_token($token_data['refresh_token'])) {
                                    echo "<p>✅ Token refreshed successfully</p>";
                                } else {
                                    echo "<p>❌ Failed to refresh token</p>";
                                }
                            }
                        }
                    }
                } else {
                    echo "<p>❌ Invalid token format</p>";
                }
            } else {
                echo "<p>❌ No access token</p>";
            }

        } catch (Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
	
	
	
	public function minimal_share_test() {
    // เคลียร์ output buffer
    if (ob_get_level()) {
        ob_clean();
    }
    
    try {
        // Force JSON headers
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        $result = [
            'success' => true,
            'message' => 'Minimal test successful',
            'data' => [
                'timestamp' => date('Y-m-d H:i:s'),
                'method' => $this->input->method(),
                'is_ajax' => $this->input->is_ajax_request(),
                'session_exists' => !empty($this->session->userdata('m_id')),
                'post_data' => $this->input->post()
            ]
        ];
        
        echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
	
	
	
	/**
 * ✅ OVERRIDE share_folder - เพิ่ม Auto-refresh
 */
public function share_folder() {
    // ล้าง output buffer และตั้งค่า headers ก่อน
    if (ob_get_level()) {
        ob_clean();
    }
    
    // Force JSON response
    $this->output->set_content_type('application/json', 'utf-8');
    
    try {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method', 405);
            return;
        }

        // ตรวจสอบ session
        if (!$this->session->userdata('m_id')) {
            $this->output_json_error('Session หมดอายุ กรุณาเข้าสู่ระบบใหม่', 401);
            return;
        }

        // รับค่า input
        $folder_id = $this->input->post('folder_id');
        $user_google_email = $this->input->post('user_google_email');
        $permission_level = $this->input->post('permission_level', true) ?: 'reader';

        log_message('info', "Share folder request: folder_id={$folder_id}, email={$user_google_email}, permission={$permission_level}");

        // ตรวจสอบข้อมูลที่จำเป็น
        if (empty($folder_id) || empty($user_google_email)) {
            $this->output_json_error('ข้อมูลไม่ครบถ้วน: กรุณาระบุ Folder ID และ Google Email');
            return;
        }

        // ตรวจสอบรูปแบบ email
        if (!filter_var($user_google_email, FILTER_VALIDATE_EMAIL)) {
            $this->output_json_error('รูปแบบ Google Email ไม่ถูกต้อง');
            return;
        }

        // ตรวจสอบสิทธิ์ของ User ที่ขอแชร์
        if (!$this->can_user_access_folder($this->session->userdata('m_id'), $folder_id)) {
            $this->output_json_error('คุณไม่มีสิทธิ์เข้าถึงโฟลเดอร์นี้');
            return;
        }

        // ✅ AUTO-REFRESH TOKEN ก่อนดำเนินการ
        log_message('info', 'Performing auto-refresh check before sharing...');
        $token_check = $this->ensure_valid_system_token();
        if (!$token_check['valid']) {
            log_message('error', 'System token auto-refresh failed: ' . $token_check['message']);
            $this->output_json_error('ระบบมีปัญหา Access Token: ' . $token_check['message'] . ' กรุณาแจ้งผู้ดูแลระบบ');
            return;
        }

        log_message('info', "Auto-refresh check passed. Attempting to share folder {$folder_id} to {$user_google_email}");

        // ดำเนินการแชร์ด้วย cURL (มีความเสถียรสูง)
        $result = $this->share_folder_with_auto_refresh($folder_id, $user_google_email, $permission_level);

        if ($result && isset($result['success']) && $result['success']) {
            // บันทึกข้อมูลการแชร์
            $sharing_saved = $this->save_sharing_record([
                'folder_id' => $folder_id,
                'shared_by' => $this->session->userdata('m_id'),
                'shared_to_email' => $user_google_email,
                'permission_level' => $permission_level,
                'google_permission_id' => $result['permission_id'] ?? null,
                'shared_at' => date('Y-m-d H:i:s'),
                'method_used' => 'auto_refresh_curl'
            ]);

            log_message('info', "Successfully shared folder {$folder_id} to {$user_google_email} with auto-refresh");

            $this->output_json_success([
                'permission_id' => $result['permission_id'] ?? null,
                'folder_url' => $result['folder_url'] ?? "https://drive.google.com/drive/folders/{$folder_id}",
                'folder_name' => $result['folder_name'] ?? 'Unknown Folder',
                'method' => 'Auto-Refresh Enhanced cURL',
                'sharing_saved' => $sharing_saved
            ], 'แชร์โฟลเดอร์เรียบร้อย โฟลเดอร์จะปรากฏใน "แชร์กับฉัน" ของ Google Drive');
        } else {
            $error_msg = (isset($result['error']) ? $result['error'] : 'Unknown error occurred');
            log_message('error', "Failed to share folder {$folder_id} with auto-refresh: {$error_msg}");
            
            // วิเคราะห์ Error และให้คำแนะนำ
            if (strpos($error_msg, 'auto_refresh_failed') !== false) {
                $this->output_json_error('ระบบไม่สามารถต่ออายุ Token อัตโนมัติได้ กรุณาแจ้งผู้ดูแลระบบให้ตรวจสอบการเชื่อมต่อ Google Account');
            } elseif (strpos($error_msg, '401') !== false || strpos($error_msg, 'authentication') !== false) {
                $this->output_json_error('ปัญหา Authentication: กรุณาลองใหม่อีกครั้ง หรือแจ้งผู้ดูแลระบบ');
            } elseif (strpos($error_msg, '403') !== false || strpos($error_msg, 'permission') !== false) {
                $this->output_json_error('ปัญหา Permission: ไม่มีสิทธิ์เข้าถึงโฟลเดอร์นี้');
            } elseif (strpos($error_msg, '404') !== false || strpos($error_msg, 'not found') !== false) {
                $this->output_json_error('ไม่พบโฟลเดอร์: โฟลเดอร์อาจถูกลบหรือย้ายแล้ว');
            } else {
                $this->output_json_error('ไม่สามารถแชร์โฟลเดอร์ได้: ' . $error_msg);
            }
        }

    } catch (Exception $e) {
        log_message('error', 'Share folder with auto-refresh error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ✅ AUTO-REFRESH SYSTEM TOKEN (สำหรับ User Controller)
 */
private function ensure_valid_system_token($force_refresh = false) {
    try {
        $system_storage = $this->get_system_storage_info();
        if (!$system_storage) {
            return ['valid' => false, 'message' => 'ไม่พบ System Storage'];
        }

        if (!$system_storage->google_access_token) {
            return ['valid' => false, 'message' => 'ไม่พบ Access Token'];
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            return ['valid' => false, 'message' => 'Token format ไม่ถูกต้อง'];
        }

        // ตรวจสอบการบังคับ refresh
        if ($force_refresh) {
            log_message('info', 'User controller: Force refresh requested');
            return $this->perform_system_token_refresh($token_data, 'user_force_refresh');
        }

        // ตรวจสอบว่า Token หมดอายุหรือไม่
        if ($system_storage->google_token_expires) {
            $expires_time = strtotime($system_storage->google_token_expires);
            $current_time = time();
            $time_diff = $expires_time - $current_time;
            
            // Refresh ถ้าหมดอายุแล้ว หรือใกล้หมดอายุภายใน 5 นาที
            if ($time_diff <= 300) { // 5 minutes
                $reason = $time_diff <= 0 ? 'expired' : 'near_expiry';
                log_message('info', "User controller: Auto-refresh needed, token {$reason}, time_diff: {$time_diff} seconds");
                return $this->perform_system_token_refresh($token_data, $reason);
            }

            log_message('debug', "User controller: Token OK, expires in {$time_diff} seconds");
            return ['valid' => true, 'message' => 'Token ใช้งานได้'];
        }

        // ไม่มีข้อมูลวันหมดอายุ - ทดสอบ Token
        if ($this->test_token_validity($token_data['access_token'])) {
            return ['valid' => true, 'message' => 'Token ใช้งานได้'];
        } else {
            log_message('info', 'User controller: Token failed validity test, attempting refresh');
            return $this->perform_system_token_refresh($token_data, 'failed_test');
        }

    } catch (Exception $e) {
        log_message('error', 'User controller ensure_valid_system_token error: ' . $e->getMessage());
        return ['valid' => false, 'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ Token'];
    }
}

/**
 * ✅ ทำการ REFRESH TOKEN (User Controller)
 */
private function perform_system_token_refresh($token_data, $reason = 'unknown') {
    try {
        if (!isset($token_data['refresh_token']) || empty($token_data['refresh_token'])) {
            log_message('error', "User controller auto-refresh failed: No refresh token (reason: {$reason})");
            return ['valid' => false, 'message' => 'ไม่มี Refresh Token สำหรับต่ออายุ'];
        }

        log_message('info', "User controller: Starting token refresh (reason: {$reason})");

        $refresh_result = $this->refresh_access_token($token_data['refresh_token']);
        
        if ($refresh_result) {
            log_message('info', "User controller: Token refresh SUCCESS (reason: {$reason})");
            return ['valid' => true, 'message' => 'Token ถูกต่ออายุแล้ว'];
        } else {
            log_message('error', "User controller: Token refresh FAILED (reason: {$reason})");
            return ['valid' => false, 'message' => 'ไม่สามารถต่ออายุ Token ได้'];
        }

    } catch (Exception $e) {
        log_message('error', "User controller token refresh exception (reason: {$reason}): " . $e->getMessage());
        return ['valid' => false, 'message' => 'เกิดข้อผิดพลาดในการต่ออายุ Token'];
    }
}

/**
 * ✅ แชร์โฟลเดอร์พร้อม Auto-refresh
 */
private function share_folder_with_auto_refresh($folder_id, $user_email, $permission_level, $retry_count = 0) {
    try {
        log_message('info', "Starting auto-refresh folder sharing (retry: {$retry_count})");

        // ตรวจสอบ System Storage
        $system_storage = $this->get_system_storage_info();
        if (!$system_storage) {
            throw new Exception('No system storage found');
        }

        if (empty($system_storage->google_access_token)) {
            throw new Exception('No system storage access token');
        }

        // Parse token data
        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            throw new Exception('Invalid access token format');
        }

        $access_token = $token_data['access_token'];
        log_message('info', 'Access token retrieved for auto-refresh sharing');

        // ตรวจสอบว่าโฟลเดอร์มีอยู่จริงก่อน
        if (!$this->verify_folder_exists($folder_id, $access_token)) {
            throw new Exception('Folder not found or inaccessible');
        }

        log_message('info', 'Folder verified, proceeding with auto-refresh permission creation');

        // สร้าง Permission data
        $permission_data = [
            'emailAddress' => $user_email,
            'type' => 'user',
            'role' => $permission_level
        ];

        // เตรียม cURL request
        $url = "https://www.googleapis.com/drive/v3/files/{$folder_id}/permissions";
        $email_message = urlencode('คุณได้รับการแชร์โฟลเดอร์จากระบบ Google Drive องค์กร (Auto-refresh System)');
        $url .= "?emailMessage={$email_message}";

        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($permission_data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: Google-Drive-Auto-Refresh-System/1.0'
            ]
        ]);

        log_message('info', 'Executing auto-refresh cURL request to Google Drive API');

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        log_message('info', "Auto-refresh cURL response - HTTP Code: {$http_code}, Error: " . ($error ?: 'None'));

        // ตรวจสอบ cURL errors
        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }

        // ตรวจสอบ HTTP response
        if ($http_code === 200) {
            $permission_response = json_decode($response, true);
            
            if ($permission_response && isset($permission_response['id'])) {
                log_message('info', 'Auto-refresh permission created successfully');
                
                // ดึงข้อมูลโฟลเดอร์
                $folder_info = $this->get_folder_info_with_curl($folder_id, $access_token);
                
                return [
                    'success' => true,
                    'permission_id' => $permission_response['id'],
                    'folder_url' => $folder_info['webViewLink'] ?? "https://drive.google.com/drive/folders/{$folder_id}",
                    'folder_name' => $folder_info['name'] ?? 'Unknown Folder',
                    'shared_at' => date('Y-m-d H:i:s'),
                    'auto_refresh' => true
                ];
            } else {
                throw new Exception('Invalid permission response format - missing permission ID');
            }
        } elseif ($http_code === 401 && $retry_count < 2) {
            // 401 Error - ลอง Auto-refresh และ retry
            log_message('warning', "401 error detected, attempting emergency auto-refresh (retry: {$retry_count})");
            
            $refresh_check = $this->ensure_valid_system_token(true); // force refresh
            if ($refresh_check['valid']) {
                log_message('info', 'Emergency refresh successful, retrying share operation');
                return $this->share_folder_with_auto_refresh($folder_id, $user_email, $permission_level, $retry_count + 1);
            } else {
                throw new Exception('Auto-refresh failed after 401 error: ' . $refresh_check['message']);
            }
        } else {
            // Parse error response
            $error_response = json_decode($response, true);
            $error_message = "HTTP Error: {$http_code}";
            
            if ($error_response) {
                if (isset($error_response['error'])) {
                    if (isset($error_response['error']['message'])) {
                        $error_message .= ' - ' . $error_response['error']['message'];
                    }
                    if (isset($error_response['error']['code'])) {
                        $error_message .= ' (Error Code: ' . $error_response['error']['code'] . ')';
                    }
                }
            }
            
            throw new Exception($error_message);
        }

    } catch (Exception $e) {
        log_message('error', 'Share folder with auto-refresh error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s'),
            'auto_refresh' => true
        ];
    }
}

/**
 * ✅ ตรวจสอบสถานะ Auto-refresh (เพิ่มใน User Controller)
 */
public function check_auto_refresh_status() {
    try {
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $status = [
            'auto_refresh_enabled' => true,
            'system_token_valid' => false,
            'can_share_folders' => false,
            'last_check' => date('Y-m-d H:i:s'),
            'time_to_token_expiry' => null,
            'refresh_available' => false
        ];

        // ตรวจสอบ System Token
        $token_check = $this->ensure_valid_system_token();
        $status['system_token_valid'] = $token_check['valid'];
        $status['can_share_folders'] = $token_check['valid'];

        if (!$token_check['valid']) {
            $status['error_message'] = $token_check['message'];
        }

        // ตรวจสอบข้อมูล Token
        $system_storage = $this->get_system_storage_info();
        if ($system_storage && $system_storage->google_access_token) {
            $token_data = json_decode($system_storage->google_access_token, true);
            $status['refresh_available'] = isset($token_data['refresh_token']) && !empty($token_data['refresh_token']);
            
            if ($system_storage->google_token_expires) {
                $expires_time = strtotime($system_storage->google_token_expires);
                $status['time_to_token_expiry'] = max(0, $expires_time - time());
            }
        }

        $this->output_json_success($status, 'ตรวจสอบสถานะ Auto-refresh สำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	
	
	
	
	
}
?>