<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Policy_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->dbforge();
        $this->load->helper('date');
        
        // สร้างตารางอัตโนมัติถ้ายังไม่มี
        $this->create_tables_if_not_exists();
        
        // ตรวจสอบและเพิ่มข้อมูลเริ่มต้น
        $this->initialize_default_data();
    }
    
    /**
     * สร้างตารางอัตโนมัติถ้ายังไม่มี
     */
    private function create_tables_if_not_exists()
    {
        // สร้างตาราง tbl_system_config ถ้ายังไม่มี
        if (!$this->db->table_exists('tbl_system_config')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ],
                'keyword' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'value' => [
                    'type' => 'VARCHAR',
                    'constraint' => 555
                ],
                'description' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100
                ],
                'type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'update_by' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('tbl_system_config');
        }
        
        // สร้างตาราง policy_types
        if (!$this->db->table_exists('policy_types')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ],
                'code' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'unique' => TRUE
                ],
                'title' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255
                ],
                'title_en' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'icon' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => TRUE
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'sort_order' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default' => 'active'
                ],
                'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
                'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ]);
            $this->dbforge->add_key('id', TRUE);
            // ไม่ต้องเพิ่ม key 'code' เพราะกำหนด unique แล้ว
            $this->dbforge->add_key('status');
            $this->dbforge->create_table('policy_types');
        }
        
        // สร้างตาราง policy_versions
        if (!$this->db->table_exists('policy_versions')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ],
                'policy_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'version' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20
                ],
                'title' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'content' => [
                    'type' => 'LONGTEXT',
                    'null' => TRUE
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['draft', 'active', 'archived'],
                    'default' => 'active'
                ],
                'effective_date' => [
                    'type' => 'DATE',
                    'null' => TRUE
                ],
                'created_by' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ],
                'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
                'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('policy_type');
            $this->dbforge->add_key('status');
            $this->dbforge->create_table('policy_versions');
        }
        
        // สร้างตาราง policy_consents
        if (!$this->db->table_exists('policy_consents')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ],
                'user_ip' => [
                    'type' => 'VARCHAR',
                    'constraint' => 45,
                    'null' => TRUE
                ],
                'user_agent' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'policy_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'policy_version' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20
                ],
                'consent_type' => [
                    'type' => 'ENUM',
                    'constraint' => ['accept', 'reject', 'partial'],
                    'default' => 'accept'
                ],
                'consent_details' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'created_at DATETIME DEFAULT CURRENT_TIMESTAMP'
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('user_id');
            $this->dbforge->add_key('policy_type');
            $this->dbforge->add_key('consent_type');
            $this->dbforge->add_key('created_at');
            $this->dbforge->create_table('policy_consents');
        }
        
        // สร้างตาราง policy_views
        if (!$this->db->table_exists('policy_views')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ],
                'policy_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ],
                'ip_address' => [
                    'type' => 'VARCHAR',
                    'constraint' => 45,
                    'null' => TRUE
                ],
                'user_agent' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'referer' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'viewed_at DATETIME DEFAULT CURRENT_TIMESTAMP'
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('policy_type');
            $this->dbforge->add_key('user_id');
            $this->dbforge->add_key('viewed_at');
            $this->dbforge->create_table('policy_views');
        }
        
        // สร้างตาราง cookie_categories
        if (!$this->db->table_exists('cookie_categories')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ],
                'category_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100
                ],
                'name_en' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => TRUE
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'required' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'cookies_list' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'sort_order' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default' => 'active'
                ],
                'created_at DATETIME DEFAULT CURRENT_TIMESTAMP'
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('category_id');
            $this->dbforge->create_table('cookie_categories');
        }
        
        // สร้างตาราง pdpa_rights
        if (!$this->db->table_exists('pdpa_rights')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ],
                'title' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255
                ],
                'title_en' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'icon' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => TRUE
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'sort_order' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default' => 'active'
                ],
                'created_at DATETIME DEFAULT CURRENT_TIMESTAMP'
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('pdpa_rights');
        }
        
        // สร้างตาราง legal_basis
        if (!$this->db->table_exists('legal_basis')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ],
                'basis_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'title' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255
                ],
                'title_en' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'icon' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => TRUE
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'sort_order' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default' => 'active'
                ],
                'created_at DATETIME DEFAULT CURRENT_TIMESTAMP'
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('basis_id');
            $this->dbforge->create_table('legal_basis');
        }
        
        // สร้างตาราง policy_faqs
        if (!$this->db->table_exists('policy_faqs')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ],
                'policy_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'question' => [
                    'type' => 'TEXT'
                ],
                'answer' => [
                    'type' => 'TEXT'
                ],
                'sort_order' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default' => 'active'
                ],
                'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
                'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('policy_type');
            $this->dbforge->add_key('status');
            $this->dbforge->create_table('policy_faqs');
        }
    }
    
    /**
     * เพิ่มข้อมูลเริ่มต้นในตาราง
     */
    private function initialize_default_data()
    {
        // เพิ่มข้อมูล policy_types ถ้ายังไม่มี
        if ($this->db->count_all('policy_types') == 0) {
            $policy_types = [
                ['code' => 'terms', 'title' => 'นโยบายเว็บไซต์และข้อกำหนดการใช้งาน', 'title_en' => 'Terms of Service', 'icon' => 'fas fa-file-contract', 'description' => 'ข้อกำหนดและเงื่อนไขในการใช้งานเว็บไซต์', 'sort_order' => 1],
                ['code' => 'security', 'title' => 'นโยบายการรักษาความมั่นคงปลอดภัยเว็บไซต์', 'title_en' => 'Website Security Policy', 'icon' => 'fas fa-shield-alt', 'description' => 'มาตรการรักษาความปลอดภัยและการป้องกันภัยคุกคามทางไซเบอร์', 'sort_order' => 2],
                ['code' => 'pdpa', 'title' => 'นโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA)', 'title_en' => 'Personal Data Protection Act', 'icon' => 'fas fa-user-shield', 'description' => 'นโยบายตาม พ.ร.บ.คุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562', 'sort_order' => 3],
                ['code' => 'privacy', 'title' => 'ประกาศความเป็นส่วนตัว', 'title_en' => 'Privacy Notice', 'icon' => 'fas fa-user-lock', 'description' => 'การเก็บรวบรวม ใช้ และเปิดเผยข้อมูลส่วนบุคคล', 'sort_order' => 4],
                ['code' => 'cookie', 'title' => 'นโยบายการใช้คุกกี้', 'title_en' => 'Cookie Policy', 'icon' => 'fas fa-cookie-bite', 'description' => 'การใช้คุกกี้และเทคโนโลยีการติดตาม', 'sort_order' => 5],
                ['code' => 'membership', 'title' => 'ข้อกำหนดและเงื่อนไขการสมัครสมาชิก', 'title_en' => 'Membership Terms and Conditions', 'icon' => 'fas fa-users', 'description' => 'เงื่อนไขการสมัครและการเป็นสมาชิกของเว็บไซต์', 'sort_order' => 6]
            ];
            $this->db->insert_batch('policy_types', $policy_types);
        }
        
        // เพิ่มข้อมูล policy_versions ถ้ายังไม่มี
        if ($this->db->count_all('policy_versions') == 0) {
            $versions = [
                ['policy_type' => 'terms', 'version' => '2.0', 'title' => 'นโยบายเว็บไซต์และข้อกำหนดการใช้งาน', 'status' => 'active', 'effective_date' => date('Y-m-d')],
                ['policy_type' => 'security', 'version' => '2.0', 'title' => 'นโยบายการรักษาความมั่นคงปลอดภัยเว็บไซต์', 'status' => 'active', 'effective_date' => date('Y-m-d')],
                ['policy_type' => 'pdpa', 'version' => '2.0', 'title' => 'นโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA)', 'status' => 'active', 'effective_date' => date('Y-m-d')],
                ['policy_type' => 'privacy', 'version' => '2.0', 'title' => 'ประกาศความเป็นส่วนตัว', 'status' => 'active', 'effective_date' => date('Y-m-d')],
                ['policy_type' => 'cookie', 'version' => '1.5', 'title' => 'นโยบายการใช้คุกกี้', 'status' => 'active', 'effective_date' => date('Y-m-d')],
                ['policy_type' => 'membership', 'version' => '1.5', 'title' => 'ข้อกำหนดและเงื่อนไขการสมัครสมาชิก', 'status' => 'active', 'effective_date' => date('Y-m-d')]
            ];
            $this->db->insert_batch('policy_versions', $versions);
        }
        
        // เพิ่มข้อมูล cookie_categories ถ้ายังไม่มี
        if ($this->db->count_all('cookie_categories') == 0) {
            $categories = [
                [
                    'category_id' => 'necessary',
                    'name' => 'คุกกี้ที่จำเป็น',
                    'name_en' => 'Necessary Cookies',
                    'description' => 'คุกกี้ที่จำเป็นสำหรับการทำงานของเว็บไซต์ ไม่สามารถปิดได้',
                    'required' => 1,
                    'cookies_list' => json_encode(['PHPSESSID' => 'เก็บ session ของผู้ใช้', 'csrf_token' => 'ป้องกันการโจมตี CSRF', 'cookie_consent' => 'เก็บการยินยอมใช้คุกกี้']),
                    'sort_order' => 1
                ],
                [
                    'category_id' => 'functional',
                    'name' => 'คุกกี้เพื่อการทำงาน',
                    'name_en' => 'Functional Cookies',
                    'description' => 'ช่วยให้เว็บไซต์จดจำการตั้งค่าของผู้ใช้',
                    'required' => 0,
                    'cookies_list' => json_encode(['language' => 'เก็บภาษาที่เลือก', 'theme' => 'เก็บธีมที่เลือก', 'font_size' => 'เก็บขนาดตัวอักษร']),
                    'sort_order' => 2
                ],
                [
                    'category_id' => 'analytics',
                    'name' => 'คุกกี้เพื่อการวิเคราะห์',
                    'name_en' => 'Analytics Cookies',
                    'description' => 'ช่วยวิเคราะห์การใช้งานเว็บไซต์',
                    'required' => 0,
                    'cookies_list' => json_encode(['_ga' => 'Google Analytics', '_gid' => 'Google Analytics ID', '_gat' => 'Google Analytics Tracking']),
                    'sort_order' => 3
                ],
                [
                    'category_id' => 'marketing',
                    'name' => 'คุกกี้เพื่อการตลาด',
                    'name_en' => 'Marketing Cookies',
                    'description' => 'ใช้ในการแสดงโฆษณาที่เกี่ยวข้อง',
                    'required' => 0,
                    'cookies_list' => json_encode(['fbp' => 'Facebook Pixel', 'fr' => 'Facebook', 'IDE' => 'Google Ads']),
                    'sort_order' => 4
                ]
            ];
            $this->db->insert_batch('cookie_categories', $categories);
        }
        
        // เพิ่มข้อมูล pdpa_rights ถ้ายังไม่มี
        if ($this->db->count_all('pdpa_rights') == 0) {
            $rights = [
                ['title' => 'สิทธิในการเข้าถึงข้อมูล', 'title_en' => 'Right of Access', 'icon' => 'fas fa-eye', 'description' => 'ท่านมีสิทธิขอเข้าถึงและขอรับสำเนาข้อมูลส่วนบุคคลของท่านที่เรามีอยู่', 'sort_order' => 1],
                ['title' => 'สิทธิในการแก้ไขข้อมูล', 'title_en' => 'Right to Rectification', 'icon' => 'fas fa-edit', 'description' => 'ท่านมีสิทธิขอแก้ไขข้อมูลส่วนบุคคลให้ถูกต้อง เป็นปัจจุบัน สมบูรณ์', 'sort_order' => 2],
                ['title' => 'สิทธิในการลบข้อมูล', 'title_en' => 'Right to Erasure', 'icon' => 'fas fa-trash', 'description' => 'ท่านมีสิทธิขอให้ลบหรือทำลายข้อมูลส่วนบุคคล (Right to be Forgotten)', 'sort_order' => 3],
                ['title' => 'สิทธิในการระงับการใช้ข้อมูล', 'title_en' => 'Right to Restriction', 'icon' => 'fas fa-pause', 'description' => 'ท่านมีสิทธิขอให้ระงับการใช้ข้อมูลส่วนบุคคลชั่วคราว', 'sort_order' => 4],
                ['title' => 'สิทธิในการคัดค้าน', 'title_en' => 'Right to Object', 'icon' => 'fas fa-hand-paper', 'description' => 'ท่านมีสิทธิคัดค้านการประมวลผลข้อมูลส่วนบุคคลในบางกรณี', 'sort_order' => 5],
                ['title' => 'สิทธิในการพกพาข้อมูล', 'title_en' => 'Right to Data Portability', 'icon' => 'fas fa-exchange-alt', 'description' => 'ท่านมีสิทธิขอรับข้อมูลในรูปแบบที่สามารถอ่านได้ด้วยเครื่องมืออัตโนมัติ', 'sort_order' => 6],
                ['title' => 'สิทธิในการเพิกถอนความยินยอม', 'title_en' => 'Right to Withdraw Consent', 'icon' => 'fas fa-times-circle', 'description' => 'ท่านมีสิทธิเพิกถอนความยินยอมเมื่อใดก็ได้', 'sort_order' => 7],
                ['title' => 'สิทธิในการร้องเรียน', 'title_en' => 'Right to Complaint', 'icon' => 'fas fa-flag', 'description' => 'ท่านมีสิทธิร้องเรียนต่อสำนักงาน คปส. หากเห็นว่าไม่ปฏิบัติตามกฎหมาย', 'sort_order' => 8]
            ];
            $this->db->insert_batch('pdpa_rights', $rights);
        }
        
        // เพิ่มข้อมูล legal_basis ถ้ายังไม่มี
        if ($this->db->count_all('legal_basis') == 0) {
            $legal_basis = [
                ['basis_id' => 'consent', 'title' => 'ความยินยอม', 'title_en' => 'Consent', 'icon' => 'fas fa-handshake', 'description' => 'เมื่อท่านให้ความยินยอมในการประมวลผลข้อมูลส่วนบุคคล', 'sort_order' => 1],
                ['basis_id' => 'contract', 'title' => 'การปฏิบัติตามสัญญา', 'title_en' => 'Contract', 'icon' => 'fas fa-file-contract', 'description' => 'เพื่อการปฏิบัติตามสัญญาหรือการให้บริการตามคำขอ', 'sort_order' => 2],
                ['basis_id' => 'legal_obligation', 'title' => 'หน้าที่ตามกฎหมาย', 'title_en' => 'Legal Obligation', 'icon' => 'fas fa-balance-scale', 'description' => 'เพื่อปฏิบัติตามกฎหมายที่องค์กรต้องปฏิบัติ', 'sort_order' => 3],
                ['basis_id' => 'vital_interests', 'title' => 'ประโยชน์สำคัญต่อชีวิต', 'title_en' => 'Vital Interests', 'icon' => 'fas fa-heartbeat', 'description' => 'เพื่อป้องกันหรือระงับอันตรายต่อชีวิต ร่างกาย หรือสุขภาพ', 'sort_order' => 4],
                ['basis_id' => 'public_task', 'title' => 'ภารกิจเพื่อประโยชน์สาธารณะ', 'title_en' => 'Public Task', 'icon' => 'fas fa-users', 'description' => 'การดำเนินภารกิจเพื่อประโยชน์สาธารณะหรือการใช้อำนาจรัฐ', 'sort_order' => 5],
                ['basis_id' => 'legitimate_interests', 'title' => 'ประโยชน์โดยชอบด้วยกฎหมาย', 'title_en' => 'Legitimate Interests', 'icon' => 'fas fa-check-circle', 'description' => 'เพื่อประโยชน์โดยชอบด้วยกฎหมายขององค์กร', 'sort_order' => 6]
            ];
            $this->db->insert_batch('legal_basis', $legal_basis);
        }
        
        // เพิ่มข้อมูล policy_faqs ถ้ายังไม่มี
        if ($this->db->count_all('policy_faqs') == 0) {
            $faqs = [
                // PDPA FAQs
                ['policy_type' => 'pdpa', 'question' => 'ข้อมูลของฉันจะถูกเก็บไว้นานแค่ไหน?', 'answer' => 'เราจะเก็บข้อมูลตามระยะเวลาที่จำเป็นเพื่อให้บรรลุวัตถุประสงค์ หรือตามที่กฎหมายกำหนด โดยทั่วไปข้อมูลการให้บริการเก็บไว้ 5 ปีหลังสิ้นสุดการให้บริการ', 'sort_order' => 1],
                ['policy_type' => 'pdpa', 'question' => 'ฉันจะขอลบข้อมูลส่วนบุคคลได้อย่างไร?', 'answer' => 'ท่านสามารถติดต่อเจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคล (DPO) เพื่อยื่นคำร้องขอลบข้อมูล เราจะพิจารณาและดำเนินการภายใน 30 วัน', 'sort_order' => 2],
                ['policy_type' => 'pdpa', 'question' => 'ข้อมูลของฉันจะถูกแชร์ให้ใครบ้าง?', 'answer' => 'เราจะไม่ขาย ให้เช่า หรือแลกเปลี่ยนข้อมูลของท่าน การเปิดเผยจะทำเฉพาะกรณีที่จำเป็นตามกฎหมาย', 'sort_order' => 3],
                
                // Cookie FAQs
                ['policy_type' => 'cookie', 'question' => 'คุกกี้คืออะไร?', 'answer' => 'คุกกี้คือไฟล์ข้อมูลขนาดเล็กที่เว็บไซต์ส่งไปเก็บไว้ในคอมพิวเตอร์หรืออุปกรณ์ของท่าน เพื่อจดจำข้อมูลการใช้งาน', 'sort_order' => 1],
                ['policy_type' => 'cookie', 'question' => 'จะปิดการใช้งานคุกกี้ได้อย่างไร?', 'answer' => 'ท่านสามารถตั้งค่า Browser เพื่อปฏิเสธคุกกี้ทั้งหมดหรือแจ้งเตือนเมื่อมีการส่งคุกกี้ แต่บางส่วนของเว็บไซต์อาจไม่ทำงาน', 'sort_order' => 2],
                ['policy_type' => 'cookie', 'question' => 'เว็บไซต์ใช้คุกกี้อะไรบ้าง?', 'answer' => 'เราใช้คุกกี้ที่จำเป็น, คุกกี้เพื่อการทำงาน, คุกกี้เพื่อการวิเคราะห์ และคุกกี้เพื่อการตลาด โดยท่านสามารถเลือกได้', 'sort_order' => 3]
            ];
            $this->db->insert_batch('policy_faqs', $faqs);
        }
    }

    
    /**
     * ดึงข้อมูลนโยบายทั้งหมดจากฐานข้อมูล
     * @return array
     */
    public function get_all_policies()
    {
        $this->db->select('pt.*, pv.version, pv.updated_at as version_updated');
        $this->db->from('policy_types pt');
        $this->db->join('policy_versions pv', 'pt.code = pv.policy_type AND pv.status = "active"', 'left');
        $this->db->where('pt.status', 'active');
        $this->db->order_by('pt.sort_order', 'ASC');
        $query = $this->db->get();
        
        $policies = [];
        foreach ($query->result_array() as $row) {
            $row['url'] = site_url('policy/' . $row['code']);
            $policies[] = $row;
        }
        
        return $policies;
    }

    /**
     * ดึงข้อมูลนโยบายตาม code จากฐานข้อมูล
     * @param string $code
     * @return array|null
     */
    public function get_policy_by_code($code)
    {
        $this->db->select('pt.*, pv.version, pv.content, pv.updated_at as version_updated, pv.effective_date');
        $this->db->from('policy_types pt');
        $this->db->join('policy_versions pv', 'pt.code = pv.policy_type AND pv.status = "active"', 'left');
        $this->db->where('pt.code', $code);
        $this->db->where('pt.status', 'active');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $policy = $query->row_array();
            $policy['url'] = site_url('policy/' . $policy['code']);
            return $policy;
        }
        
        return null;
    }

    /**
     * ดึงเวอร์ชันของนโยบายจากฐานข้อมูล
     * @param string $policy_type
     * @return string
     */
    public function get_policy_version($policy_type)
    {
        $this->db->select('version');
        $this->db->where('policy_type', $policy_type);
        $this->db->where('status', 'active');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('policy_versions');
        
        if ($query->num_rows() > 0) {
            return $query->row()->version;
        }
        
        return '1.0';
    }

    /**
     * ดึงวันที่อัปเดตล่าสุดของนโยบาย
     * @param string $policy_type
     * @return string
     */
    public function get_policy_updated($policy_type)
    {
        $this->db->select('updated_at');
        $this->db->where('policy_type', $policy_type);
        $this->db->where('status', 'active');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('policy_versions');
        
        if ($query->num_rows() > 0) {
            return $query->row()->updated_at;
        }
        
        return date('Y-m-d H:i:s');
    }

    /**
     * บันทึกการยอมรับนโยบายลงฐานข้อมูล
     * @param array $data
     * @return bool
     */
    public function save_policy_consent($data)
    {
        $insert_data = [
            'user_id' => $data['user_id'] ?? null,
            'user_ip' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'policy_type' => $data['policy_type'],
            'policy_version' => $this->get_policy_version($data['policy_type']),
            'consent_type' => $data['consent_type'], // 'accept', 'reject', 'partial'
            'consent_details' => isset($data['details']) ? json_encode($data['details']) : null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('policy_consents', $insert_data);
    }

    /**
     * ดึงประวัติการยอมรับนโยบายของผู้ใช้
     * @param int $user_id
     * @param string $policy_type (optional)
     * @return array
     */
    public function get_user_consent_history($user_id, $policy_type = null)
    {
        $this->db->where('user_id', $user_id);
        
        if ($policy_type) {
            $this->db->where('policy_type', $policy_type);
        }
        
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('policy_consents');
        
        return $query->result_array();
    }

    /**
     * ตรวจสอบว่าผู้ใช้ยอมรับนโยบายล่าสุดหรือไม่
     * @param int $user_id
     * @param string $policy_type
     * @return bool
     */
    public function check_user_consent($user_id, $policy_type)
    {
        $current_version = $this->get_policy_version($policy_type);
        
        $this->db->where('user_id', $user_id);
        $this->db->where('policy_type', $policy_type);
        $this->db->where('policy_version', $current_version);
        $this->db->where('consent_type', 'accept');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        
        $query = $this->db->get('policy_consents');
        
        return $query->num_rows() > 0;
    }

    /**
     * ดึงข้อมูล Cookie Categories จากฐานข้อมูล
     * @return array
     */
    public function get_cookie_categories()
    {
        $this->db->where('status', 'active');
        $this->db->order_by('sort_order', 'ASC');
        $query = $this->db->get('cookie_categories');
        
        $categories = [];
        foreach ($query->result_array() as $row) {
            $row['cookies'] = json_decode($row['cookies_list'], true);
            unset($row['cookies_list']);
            $categories[] = $row;
        }
        
        return $categories;
    }

    /**
     * ดึงข้อมูล PDPA Rights จากฐานข้อมูล
     * @return array
     */
    public function get_pdpa_rights()
    {
        $this->db->where('status', 'active');
        $this->db->order_by('sort_order', 'ASC');
        $query = $this->db->get('pdpa_rights');
        
        return $query->result_array();
    }

    /**
     * ดึงข้อมูล Legal Basis สำหรับ PDPA จากฐานข้อมูล
     * @return array
     */
    public function get_legal_basis()
    {
        $this->db->where('status', 'active');
        $this->db->order_by('sort_order', 'ASC');
        $query = $this->db->get('legal_basis');
        
        return $query->result_array();
    }

    /**
     * บันทึก Log การเข้าดูนโยบาย
     * @param string $policy_type
     * @param int|null $user_id
     * @return bool
     */
    public function log_policy_view($policy_type, $user_id = null)
    {
        $log_data = [
            'policy_type' => $policy_type,
            'user_id' => $user_id,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'referer' => $this->input->server('HTTP_REFERER'),
            'viewed_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('policy_views', $log_data);
    }

    /**
     * สถิติการเข้าดูนโยบาย
     * @param string $period ('today', 'week', 'month', 'year')
     * @return array
     */
    public function get_policy_statistics($period = 'month')
    {
        $date_condition = '';
        switch ($period) {
            case 'today':
                $date_condition = "DATE(viewed_at) = CURDATE()";
                break;
            case 'week':
                $date_condition = "viewed_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $date_condition = "viewed_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
            case 'year':
                $date_condition = "viewed_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                break;
            default:
                $date_condition = "1=1";
        }

        $sql = "
            SELECT 
                pv.policy_type,
                pt.title,
                pt.icon,
                COUNT(*) as view_count,
                COUNT(DISTINCT pv.ip_address) as unique_visitors
            FROM policy_views pv
            LEFT JOIN policy_types pt ON pv.policy_type = pt.code
            WHERE {$date_condition}
            GROUP BY pv.policy_type
            ORDER BY view_count DESC
        ";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * ดึงข้อมูล FAQ สำหรับแต่ละนโยบาย
     * @param string $policy_type
     * @return array
     */
    public function get_policy_faq($policy_type)
    {
        $this->db->where('policy_type', $policy_type);
        $this->db->where('status', 'active');
        $this->db->order_by('sort_order', 'ASC');
        $query = $this->db->get('policy_faqs');
        
        return $query->result_array();
    }

    /**
     * อัปเดตเวอร์ชันนโยบาย
     * @param string $policy_type
     * @param string $new_version
     * @param string $content (optional)
     * @return bool
     */
    public function update_policy_version($policy_type, $new_version, $content = null)
    {
        // ปิด version เก่า
        $this->db->where('policy_type', $policy_type);
        $this->db->where('status', 'active');
        $this->db->update('policy_versions', ['status' => 'archived']);
        
        // เพิ่ม version ใหม่
        $data = [
            'policy_type' => $policy_type,
            'version' => $new_version,
            'content' => $content,
            'status' => 'active',
            'effective_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('policy_versions', $data);
    }

    /**
     * เพิ่ม FAQ ใหม่
     * @param array $data
     * @return bool
     */
    public function add_faq($data)
    {
        return $this->db->insert('policy_faqs', $data);
    }

    /**
     * อัปเดต FAQ
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_faq($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('policy_faqs', $data);
    }

    /**
     * ลบ FAQ
     * @param int $id
     * @return bool
     */
    public function delete_faq($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('policy_faqs');
    }

    /**
     * ดึงสถิติการยอมรับนโยบาย
     * @param string $policy_type (optional)
     * @return array
     */
    public function get_consent_statistics($policy_type = null)
    {
        $sql = "
            SELECT 
                policy_type,
                consent_type,
                COUNT(*) as count,
                DATE(created_at) as date
            FROM policy_consents
        ";
        
        if ($policy_type) {
            $sql .= " WHERE policy_type = " . $this->db->escape($policy_type);
        }
        
        $sql .= " GROUP BY policy_type, consent_type, DATE(created_at)
                  ORDER BY date DESC";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * ค้นหา consent ตาม IP
     * @param string $ip_address
     * @return array
     */
    public function get_consent_by_ip($ip_address)
    {
        $this->db->where('user_ip', $ip_address);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get('policy_consents');
        
        return $query->result_array();
    }

    /**
     * ตรวจสอบว่ามีการอัปเดตนโยบายหลังจากที่ผู้ใช้ยอมรับครั้งล่าสุดหรือไม่
     * @param int $user_id
     * @param string $policy_type
     * @return bool
     */
    public function has_policy_updated_since_consent($user_id, $policy_type)
    {
        // ดึงวันที่ยอมรับล่าสุด
        $this->db->select('created_at');
        $this->db->where('user_id', $user_id);
        $this->db->where('policy_type', $policy_type);
        $this->db->where('consent_type', 'accept');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        $consent_query = $this->db->get('policy_consents');
        
        if ($consent_query->num_rows() == 0) {
            return true; // ยังไม่เคยยอมรับเลย
        }
        
        $last_consent_date = $consent_query->row()->created_at;
        
        // ตรวจสอบว่ามีการอัปเดตนโยบายหลังจากนั้นหรือไม่
        $this->db->where('policy_type', $policy_type);
        $this->db->where('status', 'active');
        $this->db->where('updated_at >', $last_consent_date);
        $policy_query = $this->db->get('policy_versions');
        
        return $policy_query->num_rows() > 0;
    }

    /**
     * ดึงข้อมูลนโยบายทั้งหมดสำหรับ Admin
     * @return array
     */
    public function get_all_policies_admin()
    {
        $this->db->select('pt.*, COUNT(DISTINCT pv.id) as version_count');
        $this->db->from('policy_types pt');
        $this->db->join('policy_versions pv', 'pt.code = pv.policy_type', 'left');
        $this->db->group_by('pt.id');
        $this->db->order_by('pt.sort_order', 'ASC');
        $query = $this->db->get();
        
        return $query->result_array();
    }

    /**
     * ดึงประวัติเวอร์ชันของนโยบาย
     * @param string $policy_type
     * @return array
     */
    public function get_policy_version_history($policy_type)
    {
        $this->db->where('policy_type', $policy_type);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('policy_versions');
        
        return $query->result_array();
    }
}