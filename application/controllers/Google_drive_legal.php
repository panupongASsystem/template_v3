<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Drive Legal Controller v1.0.0
 * จัดการหน้า Terms of Service และ Privacy Policy สำหรับ Google Drive
 * 
 * Features:
 * ✅ Terms of Service page
 * ✅ Privacy Policy page  
 * ✅ Responsive design
 * ✅ Multi-language support (Thai/English)
 * ✅ Apple-inspired UI
 * 
 * Routes:
 * - google_drive_legal/terms
 * - google_drive_legal/privacy
 * 
 * @author   System Developer
 * @version  1.0.0
 * @since    2025-08-05
 */
class Google_drive_legal extends CI_Controller {

    private $member_id;
    private $user_data;
    private $language = 'th'; // Default language

    public function __construct() {
        parent::__construct();
        
        // โหลด libraries ที่จำเป็น
        $this->load->helper(['url', 'language', 'cookie']);
        $this->load->library(['session', 'user_agent']);
        $this->load->database();
        
        // ตรวจสอบการ login (optional สำหรับหน้า legal)
        if ($this->session->userdata('m_id')) {
            $this->member_id = $this->session->userdata('m_id');
            $this->load_user_data();
        }
        
        // ตรวจสอบภาษาจาก session หรือ cookie
        $this->detect_language();
        
        // ตั้งค่า timezone
        date_default_timezone_set('Asia/Bangkok');
    }

    /**
     * โหลดข้อมูลผู้ใช้
     */
    private function load_user_data() {
        try {
            if ($this->member_id) {
                $query = $this->db->select('m_fname, m_lname, m_email, m_system')
                                 ->where('m_id', $this->member_id)
                                 ->get('tbl_member');
                
                if ($query->num_rows() > 0) {
                    $this->user_data = $query->row();
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Load user data error: ' . $e->getMessage());
        }
    }

    /**
     * ตรวจจับภาษาจาก session, cookie หรือ browser
     */
    private function detect_language() {
        // จาก URL parameter
        $lang_param = $this->input->get('lang');
        if (in_array($lang_param, ['th', 'en'])) {
            $this->language = $lang_param;
            $this->session->set_userdata('preferred_language', $lang_param);
            return;
        }

        // จาก session
        $session_lang = $this->session->userdata('preferred_language');
        if (in_array($session_lang, ['th', 'en'])) {
            $this->language = $session_lang;
            return;
        }

        // จาก cookie
        $cookie_lang = get_cookie('preferred_language');
        if (in_array($cookie_lang, ['th', 'en'])) {
            $this->language = $cookie_lang;
            return;
        }

        // จาก browser (ตรวจสอบ Accept-Language header)
        $browser_lang = $this->input->server('HTTP_ACCEPT_LANGUAGE');
        if (strpos($browser_lang, 'th') !== false) {
            $this->language = 'th';
        } elseif (strpos($browser_lang, 'en') !== false) {
            $this->language = 'en';
        }
    }

    /**
     * หน้า Terms of Service
     * URL: google_drive_legal/terms
     */
    public function terms() {
        $data = [
            'page_title' => $this->get_text('terms_of_service'),
            'page_type' => 'terms',
            'language' => $this->language,
            'user_data' => $this->user_data,
            'member_id' => $this->member_id,
            'current_url' => current_url(),
            'base_url' => base_url(),
            'last_updated' => '2025-08-05',
            'version' => '1.0.0',
            'meta_description' => $this->get_text('terms_meta_description'),
            'breadcrumb' => [
                ['title' => $this->get_text('home'), 'url' => base_url()],
                ['title' => 'Google Drive', 'url' => site_url('google_drive_files')],
                ['title' => $this->get_text('terms_of_service'), 'url' => '']
            ]
        ];

        // เพิ่มข้อมูลเฉพาะสำหรับ Terms of Service
        $data['terms_sections'] = $this->get_terms_sections();
        
        $this->load->view('google_drive/Terms_of_Service_Google_Drive', $data);
    }

    /**
     * หน้า Privacy Policy  
     * URL: google_drive_legal/privacy
     */
    public function privacy() {
        $data = [
            'page_title' => $this->get_text('privacy_policy'),
            'page_type' => 'privacy',
            'language' => $this->language,
            'user_data' => $this->user_data,
            'member_id' => $this->member_id,
            'current_url' => current_url(),
            'base_url' => base_url(),
            'last_updated' => '2025-08-05',
            'version' => '1.0.0',
            'meta_description' => $this->get_text('privacy_meta_description'),
            'breadcrumb' => [
                ['title' => $this->get_text('home'), 'url' => base_url()],
                ['title' => 'Google Drive', 'url' => site_url('google_drive_files')],
                ['title' => $this->get_text('privacy_policy'), 'url' => '']
            ]
        ];

        // เพิ่มข้อมูลเฉพาะสำหรับ Privacy Policy
        $data['privacy_sections'] = $this->get_privacy_sections();
        
        $this->load->view('google_drive/Privacy_Policy_Google_drive', $data);
    }

    /**
     * เปลี่ยนภาษา
     * URL: google_drive_legal/change_language/en
     */
    public function change_language($lang = 'th') {
        if (in_array($lang, ['th', 'en'])) {
            $this->session->set_userdata('preferred_language', $lang);
            
            // ตั้งค่า cookie (อายุ 30 วัน)
            $cookie_data = [
                'name' => 'preferred_language',
                'value' => $lang,
                'expire' => 2592000, // 30 days
                'path' => '/',
                'secure' => is_https()
            ];
            $this->input->set_cookie($cookie_data);
        }

        // Redirect กลับไปหน้าเดิม
        $redirect_url = $this->input->server('HTTP_REFERER');
        if (empty($redirect_url) || strpos($redirect_url, site_url()) === false) {
            $redirect_url = site_url('google_drive_files');
        }
        
        redirect($redirect_url);
    }

    /**
     * API: ข้อมูล Terms of Service (JSON)
     * URL: google_drive_legal/api_terms
     */
    public function api_terms() {
        header('Content-Type: application/json; charset=utf-8');
        
        $data = [
            'success' => true,
            'language' => $this->language,
            'last_updated' => '2025-08-05',
            'sections' => $this->get_terms_sections(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * API: ข้อมูล Privacy Policy (JSON)
     * URL: google_drive_legal/api_privacy
     */
    public function api_privacy() {
        header('Content-Type: application/json; charset=utf-8');
        
        $data = [
            'success' => true,
            'language' => $this->language,
            'last_updated' => '2025-08-05',
            'sections' => $this->get_privacy_sections(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * ดาวน์โหลด Terms of Service เป็น PDF
     * URL: google_drive_legal/download_terms_pdf
     */
    public function download_terms_pdf() {
        // สำหรับอนาคต: สร้าง PDF จาก Terms of Service
        // ตอนนี้ redirect ไปหน้า terms
        redirect('google_drive_legal/terms');
    }

    /**
     * ดาวน์โหลด Privacy Policy เป็น PDF
     * URL: google_drive_legal/download_privacy_pdf  
     */
    public function download_privacy_pdf() {
        // สำหรับอนาคต: สร้าง PDF จาก Privacy Policy
        // ตอนนี้ redirect ไปหน้า privacy
        redirect('google_drive_legal/privacy');
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * ดึงข้อความตามภาษา
     */
    private function get_text($key) {
        $texts = [
            'th' => [
                'terms_of_service' => 'ข้อกำหนดการใช้งาน',
                'privacy_policy' => 'นโยบายความเป็นส่วนตัว',
                'home' => 'หน้าแรก',
                'terms_meta_description' => 'ข้อกำหนดการใช้งาน Google Drive สำหรับระบบจัดการไฟล์',
                'privacy_meta_description' => 'นโยบายความเป็นส่วนตัวสำหรับการใช้งาน Google Drive',
                'last_updated' => 'อัปเดตล่าสุด',
                'version' => 'เวอร์ชัน',
                'effective_date' => 'มีผลตั้งแต่',
                'contact_info' => 'ข้อมูลติดต่อ',
                'accept_terms' => 'ยอมรับข้อกำหนด',
                'download_pdf' => 'ดาวน์โหลด PDF'
            ],
            'en' => [
                'terms_of_service' => 'Terms of Service',
                'privacy_policy' => 'Privacy Policy', 
                'home' => 'Home',
                'terms_meta_description' => 'Terms of Service for Google Drive file management system',
                'privacy_meta_description' => 'Privacy Policy for Google Drive usage',
                'last_updated' => 'Last Updated',
                'version' => 'Version',
                'effective_date' => 'Effective Date',
                'contact_info' => 'Contact Information',
                'accept_terms' => 'Accept Terms',
                'download_pdf' => 'Download PDF'
            ]
        ];

        return $texts[$this->language][$key] ?? $texts['th'][$key] ?? $key;
    }

    /**
     * ดึงข้อมูล Terms of Service แบ่งเป็น sections
     */
    private function get_terms_sections() {
        if ($this->language === 'en') {
            return $this->get_terms_sections_en();
        }
        
        return [
            [
                'id' => 'introduction',
                'title' => '1. บทนำ',
                'content' => 'ข้อกำหนดการใช้งานนี้ควบคุมการใช้งานบริการ Google Drive ที่ผสานเข้ากับระบบจัดการไฟล์ของเรา'
            ],
            [
                'id' => 'acceptance',
                'title' => '2. การยอมรับข้อกำหนด',
                'content' => 'การใช้งานบริการนี้ถือว่าคุณได้อ่านและยอมรับข้อกำหนดทั้งหมด'
            ],
            [
                'id' => 'google_drive_integration',
                'title' => '3. การผสานเข้ากับ Google Drive',
                'content' => 'บริการของเราใช้ Google Drive API เพื่อจัดเก็บและจัดการไฟล์ คุณต้องมีบัญชี Google และยอมรับข้อกำหนดของ Google'
            ],
            [
                'id' => 'data_storage',
                'title' => '4. การจัดเก็บข้อมูล',
                'content' => 'ไฟล์ของคุณจะถูกจัดเก็บบน Google Drive พื้นที่จัดเก็บขึ้นอยู่กับแพ็คเกจที่คุณเลือก'
            ],
            [
                'id' => 'user_responsibilities',
                'title' => '5. ความรับผิดชอบของผู้ใช้',
                'content' => 'คุณมีความรับผิดชอบในการใช้งานบริการอย่างถูกต้องและไม่นำไปใช้ในกิจกรรมที่ผิดกฎหมาย'
            ],
            [
                'id' => 'limitations',
                'title' => '6. ข้อจำกัดการใช้งาน',
                'content' => 'มีข้อจำกัดเรื่องขนาดไฟล์ ประเภทไฟล์ และการใช้งาน API ตามที่ Google กำหนด'
            ],
            [
                'id' => 'termination',
                'title' => '7. การยกเลิกบริการ',
                'content' => 'เราขอสงวนสิทธิ์ในการยกเลิกบริการหากมีการใช้งานที่ไม่เหมาะสม'
            ],
            [
                'id' => 'changes',
                'title' => '8. การเปลี่ยนแปลงข้อกำหนด',
                'content' => 'เราอาจเปลี่ยนแปลงข้อกำหนดได้ โดยจะแจ้งให้ทราบล่วงหน้า'
            ]
        ];
    }

    /**
     * ดึงข้อมูล Terms of Service ภาษาอังกฤษ
     */
    private function get_terms_sections_en() {
        return [
            [
                'id' => 'introduction',
                'title' => '1. Introduction',
                'content' => 'These Terms of Service govern your use of our Google Drive integrated file management service.'
            ],
            [
                'id' => 'acceptance',
                'title' => '2. Acceptance of Terms',
                'content' => 'By using this service, you agree to be bound by these terms and conditions.'
            ],
            [
                'id' => 'google_drive_integration',
                'title' => '3. Google Drive Integration',
                'content' => 'Our service uses Google Drive API for file storage and management. You must have a Google account and accept Google\'s terms.'
            ],
            [
                'id' => 'data_storage',
                'title' => '4. Data Storage',
                'content' => 'Your files are stored on Google Drive. Storage space depends on your selected package.'
            ],
            [
                'id' => 'user_responsibilities',
                'title' => '5. User Responsibilities',
                'content' => 'You are responsible for using the service appropriately and not for illegal activities.'
            ],
            [
                'id' => 'limitations',
                'title' => '6. Usage Limitations',
                'content' => 'There are limitations on file size, file types, and API usage as defined by Google.'
            ],
            [
                'id' => 'termination',
                'title' => '7. Service Termination',
                'content' => 'We reserve the right to terminate service for inappropriate usage.'
            ],
            [
                'id' => 'changes',
                'title' => '8. Changes to Terms',
                'content' => 'We may modify these terms with advance notice.'
            ]
        ];
    }

    /**
     * ดึงข้อมูล Privacy Policy แบ่งเป็น sections
     */
    private function get_privacy_sections() {
        if ($this->language === 'en') {
            return $this->get_privacy_sections_en();
        }
        
        return [
            [
                'id' => 'information_collection',
                'title' => '1. การเก็บรวบรวมข้อมูล',
                'content' => 'เราเก็บรวบรวมข้อมูลที่จำเป็นสำหรับการให้บริการ เช่น ข้อมูลการลงทะเบียน และข้อมูลการใช้งาน'
            ],
            [
                'id' => 'google_data',
                'title' => '2. ข้อมูลจาก Google',
                'content' => 'เราเข้าถึงข้อมูลจาก Google Drive ของคุณเฉพาะในส่วนที่จำเป็นสำหรับการให้บริการ'
            ],
            [
                'id' => 'data_usage',
                'title' => '3. การใช้ข้อมูล',
                'content' => 'ข้อมูลของคุณใช้สำหรับการให้บริการ การปรับปรุงระบบ และการติดต่อสื่อสาร'
            ],
            [
                'id' => 'data_sharing',
                'title' => '4. การแบ่งปันข้อมูล',
                'content' => 'เราไม่แบ่งปันข้อมูลส่วนบุคคลของคุณกับบุคคลที่สาม เว้นแต่จำเป็นตามกฎหมาย'
            ],
            [
                'id' => 'data_security',
                'title' => '5. ความปลอดภัยของข้อมูล',
                'content' => 'เราใช้มาตรการรักษาความปลอดภัยที่เหมาะสมเพื่อปกป้องข้อมูลของคุณ'
            ],
            [
                'id' => 'cookies',
                'title' => '6. คุกกี้และเทคโนโลยีติดตาม',
                'content' => 'เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานและจดจำการตั้งค่าของคุณ'
            ],
            [
                'id' => 'user_rights',
                'title' => '7. สิทธิของผู้ใช้',
                'content' => 'คุณมีสิทธิ์ในการขอดู แก้ไข หรือลบข้อมูลส่วนบุคคลของคุณ'
            ],
            [
                'id' => 'contact',
                'title' => '8. การติดต่อ',
                'content' => 'หากมีคำถามเกี่ยวกับนโยบายความเป็นส่วนตัว กรุณาติดต่อเรา'
            ]
        ];
    }

    /**
     * ดึงข้อมูล Privacy Policy ภาษาอังกฤษ
     */
    private function get_privacy_sections_en() {
        return [
            [
                'id' => 'information_collection',
                'title' => '1. Information Collection',
                'content' => 'We collect information necessary for service provision, such as registration and usage data.'
            ],
            [
                'id' => 'google_data',
                'title' => '2. Google Data',
                'content' => 'We access your Google Drive data only as necessary for service provision.'
            ],
            [
                'id' => 'data_usage',
                'title' => '3. Data Usage',
                'content' => 'Your data is used for service provision, system improvement, and communication.'
            ],
            [
                'id' => 'data_sharing',
                'title' => '4. Data Sharing',
                'content' => 'We do not share your personal data with third parties except as required by law.'
            ],
            [
                'id' => 'data_security',
                'title' => '5. Data Security',
                'content' => 'We use appropriate security measures to protect your data.'
            ],
            [
                'id' => 'cookies',
                'title' => '6. Cookies and Tracking Technologies',
                'content' => 'We use cookies to improve user experience and remember your preferences.'
            ],
            [
                'id' => 'user_rights',
                'title' => '7. User Rights',
                'content' => 'You have the right to view, modify, or delete your personal data.'
            ],
            [
                'id' => 'contact',
                'title' => '8. Contact',
                'content' => 'If you have questions about this privacy policy, please contact us.'
            ]
        ];
    }
}
