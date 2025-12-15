<?php
defined('BASEPATH') or exit('No direct script access allowed');

class System_config_backend extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('system_config_model');
        $this->load->helper(['url', 'form']);
        $this->load->library('session');

        if ($this->session->userdata('m_system') != 'system_admin') {
            redirect('User/logout', 'refresh');
        }

        // ตั้งค่าเวลาหมดอายุของเซสชัน
        $this->check_session_timeout();
    }

    private function check_session_timeout()
    {
        $timeout = 900; // 15 นาที
        $last_activity = $this->session->userdata('last_activity');

        if ($last_activity && (time() - $last_activity > $timeout)) {
            $this->session->sess_destroy();
            redirect('User/logout', 'refresh');
        } else {
            $this->session->set_userdata('last_activity', time());
        }
    }

    public function index()
    {
        $data['query'] = $this->system_config_model->list();
        $data['query'] = $this->decorate_telesales_display($data['query']);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/system_config', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function address()
    {
        $data['query'] = $this->system_config_model->list_by_type('address');
        $data['type'] = 'address';
        $data['content'] = 'system_config';
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/system_config', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function link()
    {
        $data['query'] = $this->system_config_model->list_by_type('link');
        $data['type'] = 'link';
        $data['content'] = 'system_config';
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/system_config', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function key_token()
    {
        $data['query'] = $this->system_config_model->list_by_type('key_token');
        $data['type'] = 'key_token';
        $data['content'] = 'system_config';
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/system_config', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function update_domain()
    {
        $full_domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $full_domain = preg_replace('/^www\./', '', $full_domain);
        $domain_parts = explode('.', $full_domain);
        $domain = isset($domain_parts[0]) ? $domain_parts[0] : '';

        if ($full_domain == 'localhost' || preg_match('/^[0-9\.]+$/', $full_domain)) {
            $path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
            $path_parts = explode('/', trim($path, '/'));
            $domain = !empty($path_parts) ? $path_parts[0] : $domain;
        }

        $stored_domain = get_config_value('domain');

        if ($stored_domain != $domain && !empty($domain)) {
            $this->system_config_model->update_domain('domain', $domain);
        }
        redirect('system_config_backend');
    }

    // ==== BEGIN helpers ====
    private function fetch_sales(): array
    {
        $ch = curl_init('https://www.assystem.co.th/sale_api/index.php');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($resp, true);
        return is_array($data)
            ? (isset($data['data']) && is_array($data['data']) ? $data['data'] : $data)
            : [];
    }

    private function pick_nickname(array $row): string
    {
        foreach (['sale_nickname', 'nickname', 'sale_name', 'name'] as $k) {
            if (!empty($row[$k]))
                return (string) $row[$k];
        }
        return '';
    }

    private function pick_phone(array $row): string
    {
        foreach (['sale_phone', 'phone', 'tel', 'mobile'] as $k) {
            if (!empty($row[$k]))
                return (string) $row[$k];
        }
        return '';
    }

    private function make_label(string $nick, string $phone): string
    {
        if ($nick !== '' && $phone !== '')
            return $nick . ' / ' . $phone;
        if ($nick !== '')
            return $nick;
        if ($phone !== '')
            return $phone;
        return 'ไม่ระบุชื่อ/เบอร์';
    }

    private function decorate_telesales_display(array $rows): array
    {
        if (!$rows)
            return $rows;
        $sales = $this->fetch_sales();

        $index = [];
        foreach ($sales as $s) {
            $sid = strtoupper((string) ($s['sale_id'] ?? ''));
            if ($sid !== '')
                $index[$sid] = $this->make_label($this->pick_nickname($s), $this->pick_phone($s));
        }

        foreach ($rows as &$r) {
            $isTelesales = ((int) $r->id === 15) || (!empty($r->keyword) && $r->keyword === 'telesales');
            if ($isTelesales) {
                $sid = strtoupper((string) ($r->value ?? ''));
                $r->display_value = $index[$sid] ?? $r->value;
            }
        }
        unset($r);
        return $rows;
    }
    // ==== END helpers ====

    private function normalize_sale_id(string $currentValue): ?string
    {
        $currentValue = trim($currentValue);
        if ($currentValue === '')
            return null;

        if (preg_match('/^\d+$/', $currentValue)) {
            return 'ID' . $currentValue;
        }
        if (preg_match('/^ID\d+$/i', $currentValue)) {
            return strtoupper($currentValue);
        }
        return null;
    }

    private function build_sales_options(array $sales, string $currentValue): array
    {
        $cv = trim((string) $currentValue);
        $cvUpper = strtoupper($cv);
        $cvDigits = preg_replace('/\D+/', '', $cv);

        $options = [];
        $alreadySelected = false;

        foreach ($sales as $s) {
            $saleIdRaw = (string) ($s['sale_id'] ?? '');
            if ($saleIdRaw === '')
                continue;

            $saleIdUpper = strtoupper($saleIdRaw);
            $saleIdDigits = preg_replace('/\D+/', '', $saleIdRaw);

            $nick = $this->pick_nickname($s);
            $phone = $this->pick_phone($s);
            if ($nick !== '' && $phone !== '')
                $label = $nick . ' / ' . $phone;
            elseif ($nick !== '')
                $label = $nick;
            elseif ($phone !== '')
                $label = $phone;
            else
                $label = 'ไม่ระบุชื่อ/เบอร์';

            $isSelected = false;
            if ($cv !== '' && !$alreadySelected) {
                $isSelected =
                    ($cv === $saleIdRaw) ||
                    ($cvUpper === 'ID' . $saleIdUpper) ||
                    ($cvUpper === $saleIdUpper . 'ID') ||
                    ($cvDigits !== '' && $cvDigits === $saleIdDigits) ||
                    ($cvDigits !== '' && $cvDigits === preg_replace('/\D+/', '', $phone));
            }

            if ($isSelected)
                $alreadySelected = true;

            $options[] = [
                'value' => $saleIdRaw,
                'label' => $label,
                'selected' => $isSelected,
            ];
        }

        return $options;
    }

    public function editing($id)
    {
        $data['rsedit'] = $this->system_config_model->read($id);

        $data['is_telesales'] = false;
        $data['sales_options'] = [];

        $data['is_address_field'] = false;
        $address_keywords = ['subdistric', 'district', 'province', 'zip_code'];

        if (!empty($data['rsedit'])) {
            $row = $data['rsedit'];

            $isTelesales = ((int) $row->id === 15) || (isset($row->keyword) && $row->keyword === 'telesales');
            $data['is_telesales'] = $isTelesales;

            if ($isTelesales) {
                $sales = $this->fetch_sales();
                $currentValue = (string) ($row->value ?? '');
                $data['sales_options'] = $this->build_sales_options($sales, $currentValue);
            }

            if (isset($row->keyword) && in_array($row->keyword, $address_keywords)) {
                $data['is_address_field'] = true;
                $data['address_data'] = $this->system_config_model->get_address_group();
                log_message('info', 'Editing address field: ' . $row->keyword);
            }
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/system_config_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($id)
    {
        log_message('info', "=== START edit id=$id ===");

        $row = $this->system_config_model->read($id);
        $address_keywords = ['subdistric', 'district', 'province', 'zip_code'];

        if ($row && in_array($row->keyword, $address_keywords)) {
            log_message('info', 'Detected address field edit: ' . $row->keyword);

            $this->system_config_model->update_address_only([
                'subdistric' => $this->input->post('subdistric'),
                'district' => $this->input->post('district'),
                'province' => $this->input->post('province'),
                'zip_code' => $this->input->post('zip_code')
            ]);
        } else {
            $this->system_config_model->edit($id);
        }

        log_message('info', "=== END edit id=$id ===");
        redirect('system_config_backend', 'refresh');
    }

    public function adding()
    {
        $data['type'] = $this->input->get('type') ?? '';
        $data['existing_types'] = $this->system_config_model->get_distinct_types();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/system_config_form_add', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->system_config_model->add();

        $type = $this->input->post('type');
        if ($type == 'address') {
            redirect('system_config_backend/address', 'refresh');
        } else if ($type == 'link') {
            redirect('system_config_backend/link', 'refresh');
        } else if ($type == 'key_token') {
            redirect('system_config_backend/key_token', 'refresh');
        } else {
            redirect('system_config_backend', 'refresh');
        }
    }

    public function delete($id)
    {
        $data = $this->system_config_model->read($id);
        if (!$data) {
            redirect('system_config_backend', 'refresh');
        }

        $result = $this->system_config_model->delete($id);

        if ($result) {
            $this->session->set_flashdata('del_success', TRUE);
        }

        if (isset($data->type)) {
            if ($data->type == 'address') {
                redirect('system_config_backend/address', 'refresh');
            } else if ($data->type == 'link') {
                redirect('system_config_backend/link', 'refresh');
            } else if ($data->type == 'key_token') {
                redirect('system_config_backend/key_token', 'refresh');
            }
        }

        redirect('system_config_backend', 'refresh');
    }

    public function dark_mode_settings()
    {
        $config = $this->system_config_model->get_all_config();

        $data = array(
            'title' => 'การตั้งค่าการแสดงผล',
            'dark_mode_enabled' => $config['dark_mode_enabled'] ?? '0',
            'mourning_ribbon_enabled' => $config['mourning_ribbon_enabled'] ?? '0',
            'mourning_ribbon_image' => $config['mourning_ribbon_image'] ?? 'docs/ribbon.png'
        );

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/dark_mode_settings', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function update_dark_mode()
    {
        $enabled = $this->input->post('dark_mode_enabled') ?? '0';
        $confirmation = $this->input->post('confirmation');

        $current_status = $this->system_config_model->get_config_by_key('dark_mode_enabled') ?? '0';

        if ($enabled == '1' && $current_status != '1') {
            if (strtolower(trim($confirmation)) !== 'yes') {
                $this->session->set_flashdata('error', 'กรุณาพิมพ์ "yes" เพื่อยืนยันการเปิด Dark Mode');
                redirect('system_config_backend/dark_mode_settings', 'refresh');
                return;
            }
        }

        $this->system_config_model->update_by_keyword('dark_mode_enabled', $enabled);

        if ($enabled == '1') {
            $this->session->set_flashdata('success', 'เปิดใช้งาน Dark Mode สำเร็จ');
        } else {
            $this->session->set_flashdata('success', 'ปิดการใช้งาน Dark Mode สำเร็จ');
        }

        redirect('system_config_backend/dark_mode_settings', 'refresh');
    }

    public function update_display_settings()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }

        $dark_mode_enabled = $this->input->post('dark_mode_enabled') == '1' ? '1' : '0';
        $mourning_ribbon_enabled = $this->input->post('mourning_ribbon_enabled') == '1' ? '1' : '0';
        $confirmation = $this->input->post('confirmation');

        $config = $this->system_config_model->get_all_config();
        $current_dark_mode = $config['dark_mode_enabled'] ?? '0';
        $current_ribbon = $config['mourning_ribbon_enabled'] ?? '0';

        $is_enabling = ($dark_mode_enabled == '1' && $current_dark_mode == '0') ||
            ($mourning_ribbon_enabled == '1' && $current_ribbon == '0');

        if ($is_enabling && $confirmation !== 'yes') {
            $this->session->set_flashdata('error', 'กรุณายืนยันการเปลี่ยนแปลงด้วยการพิมพ์ "yes"');
            redirect('system_config_backend/dark_mode_settings');
            return;
        }

        $result1 = $this->system_config_model->update_by_keyword('dark_mode_enabled', $dark_mode_enabled);
        $result2 = $this->system_config_model->update_by_keyword('mourning_ribbon_enabled', $mourning_ribbon_enabled);

        if ($result1 && $result2) {
            $messages = [];
            if ($dark_mode_enabled == '1')
                $messages[] = 'Dark Mode เปิดแล้ว';
            elseif ($current_dark_mode == '1')
                $messages[] = 'Dark Mode ปิดแล้ว';

            if ($mourning_ribbon_enabled == '1')
                $messages[] = 'โบว์ไว้อาลัยแสดงแล้ว';
            elseif ($current_ribbon == '1')
                $messages[] = 'โบว์ไว้อาลัยซ่อนแล้ว';

            $message = !empty($messages) ? implode(' และ ', $messages) : 'บันทึกการตั้งค่าเรียบร้อย';
            $this->session->set_flashdata('success', $message);
        } else {
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        }

        redirect('system_config_backend/dark_mode_settings');
    }

    /**
     * 🆕 AJAX: ค้นหารหัสที่อยู่จาก API (สำหรับแสดงผลและตรวจสอบเท่านั้น)
     */
    public function ajax_get_location_codes()
    {
        log_message('info', '=== START ajax_get_location_codes ===');

        $subdistric = $this->input->post('subdistric');
        $district = $this->input->post('district');
        $province = $this->input->post('province');
        $zip_code = $this->input->post('zip_code');

        log_message('info', "Input - subdistric: $subdistric, district: $district, province: $province, zip_code: $zip_code");

        $response = [
            'status' => 'success',
            'subdistric_id' => null,
            'district_id' => null,
            'province_id' => null,
            'warnings' => [],
            'api_test_result' => null  // 🆕 เพิ่มผลการทดสอบ API
        ];

        // ✅ 1. หารหัสจังหวัด (จาก hardcoded list)
        $response['province_id'] = $this->get_province_code($province);
        if (!$response['province_id']) {
            $response['warnings'][] = 'ไม่พบรหัสจังหวัด';
            log_message('error', "Province code not found for: $province");
        } else {
            log_message('info', "Province code found: " . $response['province_id']);
        }

        // ✅ 2. หารหัสอำเภอและตำบล (จาก API)
        if ($zip_code && strlen($zip_code) == 5) {
            $api_data = $this->call_address_api($zip_code);

            if ($api_data) {
                // หารหัสอำเภอ
                foreach ($api_data as $item) {
                    if (isset($item['amphoe_name']) && $this->compare_names($item['amphoe_name'], $district)) {
                        $response['district_id'] = $item['amphoe_code'] ?? null;
                        log_message('info', "District code found: " . $response['district_id']);
                        break;
                    }
                }

                // หารหัสตำบล
                foreach ($api_data as $item) {
                    if (isset($item['district_name']) && $this->compare_names($item['district_name'], $subdistric)) {
                        $response['subdistric_id'] = $item['district_code'] ?? null;
                        log_message('info', "Subdistric code found: " . $response['subdistric_id']);
                        break;
                    }
                }
            } else {
                $response['warnings'][] = 'API ไม่ตอบสนอง';
                log_message('error', 'API returned no data for zipcode: ' . $zip_code);
            }

            // ตรวจสอบว่าหาเจอหรือไม่
            if (!$response['district_id']) {
                $response['warnings'][] = 'ไม่พบรหัสอำเภอ';
            }
            if (!$response['subdistric_id']) {
                $response['warnings'][] = 'ไม่พบรหัสตำบล';
            }

            // 🆕 3. ทดสอบ Population API หากหารหัสครบ
            if ($response['province_id'] && $response['district_id'] && $response['subdistric_id']) {
                $test_result = $this->test_population_api(
                    $response['province_id'],
                    $response['district_id'],
                    $response['subdistric_id']
                );
                $response['api_test_result'] = $test_result;
            }
        } else {
            $response['warnings'][] = 'กรุณากรอกรหัสไปรษณีย์ 5 หลัก';
            log_message('error', 'Invalid zipcode: ' . $zip_code);
        }

        log_message('info', '=== END ajax_get_location_codes ===');
        log_message('debug', 'Response: ' . json_encode($response));

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * 🆕 ทดสอบการเชื่อมต่อ Population API
     */
    private function test_population_api($province_code, $district_code, $subdistric_code)
    {
        log_message('info', '=== START test_population_api ===');
        
        // ใช้เดือนปัจจุบัน -1
        $current_year = (int) date('Y') + 543;
        $current_month = (int) date('m');
        $current_month--;
        if ($current_month < 1) {
            $current_month = 12;
            $current_year--;
        }
        $yymm = substr($current_year, -2) . str_pad($current_month, 2, '0', STR_PAD_LEFT);

        $api_url = "https://stat.bora.dopa.go.th/stat/statnew/connectSAPI/stat_forward.php?API=/api/statpophouse/v1/statpop/list?action=45&yymmBegin={$yymm}&yymmEnd={$yymm}&statType=0&statSubType=999&subType=99&cc={$province_code}&rcode={$district_code}&tt={$subdistric_code}";

        log_message('info', 'Testing Population API: ' . $api_url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $start_time = microtime(true);
        $response = curl_exec($ch);
        $end_time = microtime(true);
        $response_time = round(($end_time - $start_time) * 1000, 2);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        $result = [
            'success' => false,
            'http_code' => $http_code,
            'response_time' => $response_time,
            'message' => '',
            'record_count' => 0,
            'sample_village' => null
        ];

        if ($http_code != 200 || empty($response)) {
            $result['message'] = 'API ไม่ตอบสนอง (HTTP ' . $http_code . ')';
            if (!empty($curl_error)) {
                $result['message'] .= ' - ' . $curl_error;
            }
            log_message('error', 'Population API test failed: ' . $result['message']);
            return $result;
        }

        $data = json_decode($response, true);

        if (empty($data) || !is_array($data)) {
            $result['message'] = 'API ส่งข้อมูลไม่ถูกต้อง';
            log_message('error', 'Population API returned invalid data');
            return $result;
        }

        $result['success'] = true;
        $result['record_count'] = count($data);
        $result['message'] = 'เชื่อมต่อสำเร็จ - พบข้อมูล ' . count($data) . ' หมู่บ้าน';

        // ดึงข้อมูลตัวอย่างหมู่บ้านแรก
        if (!empty($data[0])) {
            $result['sample_village'] = [
                'name' => $data[0]['lsmmDesc'] ?? 'N/A',
                'male' => $data[0]['lssumtotMale'] ?? 0,
                'female' => $data[0]['lssumtotFemale'] ?? 0,
                'total' => $data[0]['lssumtotTot'] ?? 0
            ];
        }

        log_message('info', 'Population API test success: ' . $result['message']);
        log_message('info', '=== END test_population_api ===');

        return $result;
    }

    /**
     * 🆕 เรียก API ค้นหาข้อมูลที่อยู่
     */
    private function call_address_api($zipcode)
    {
        log_message('info', "Calling API for zipcode: $zipcode");

        $url = "https://addr.assystem.co.th/index.php/zip_api/address/$zipcode";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code != 200) {
            log_message('error', "API returned HTTP $http_code");
            return null;
        }

        $data = json_decode($response, true);

        if (isset($data['status']) && $data['status'] === 'success' && isset($data['data'])) {
            log_message('info', 'API success, found ' . count($data['data']) . ' records');
            return $data['data'];
        }

        log_message('error', 'API returned invalid data');
        return null;
    }

    /**
     * 🆕 หารหัสจังหวัด (Hardcoded)
     */
    private function get_province_code($province_name)
    {
        log_message('info', "Searching province code for: $province_name");

        $provinces = [
            '10' => ['กรุงเทพมหานคร', 'กทม', 'Bangkok'],
            '11' => ['สมุทรปราการ', 'Samut Prakan'],
            '12' => ['นนทบุรี', 'Nonthaburi'],
            '13' => ['ปทุมธานี', 'Pathum Thani'],
            '14' => ['พระนครศรีอยุธยา', 'อยุธยา', 'Phra Nakhon Si Ayutthaya'],
            '15' => ['อ่างทอง', 'Ang Thong'],
            '16' => ['ลพบุรี', 'Lopburi'],
            '17' => ['สิงห์บุรี', 'Sing Buri'],
            '18' => ['ชัยนาท', 'Chai Nat'],
            '19' => ['สระบุรี', 'Saraburi'],
            '20' => ['ชลบุรี', 'Chonburi'],
            '21' => ['ระยอง', 'Rayong'],
            '22' => ['จันทบุรี', 'Chanthaburi'],
            '23' => ['ตราด', 'Trat'],
            '24' => ['ฉะเชิงเทรา', 'Chachoengsao'],
            '25' => ['ปราจีนบุรี', 'Prachin Buri'],
            '26' => ['นครนายก', 'Nakhon Nayok'],
            '27' => ['สระแก้ว', 'Sa Kaeo'],
            '30' => ['นครราชสีมา', 'โคราช', 'Nakhon Ratchasima'],
            '31' => ['บุรีรัมย์', 'Buriram'],
            '32' => ['สุรินทร์', 'Surin'],
            '33' => ['ศีสะเกษ', 'Sisaket'],
            '34' => ['อุบลราชธานี', 'Ubon Ratchathani'],
            '35' => ['ยโสธร', 'Yasothon'],
            '36' => ['ชัยภูมิ', 'Chaiyaphum'],
            '37' => ['อำนาจเจริญ', 'Amnat Charoen'],
            '38' => ['บึงกาฬ', 'Bueng Kan'],
            '39' => ['หนองบัวลำภู', 'Nong Bua Lam Phu'],
            '40' => ['ขอนแก่น', 'Khon Kaen'],
            '41' => ['อุดรธานี', 'Udon Thani'],
            '42' => ['เลย', 'Loei'],
            '43' => ['หนองคาย', 'Nong Khai'],
            '44' => ['มหาสารคาม', 'Maha Sarakham'],
            '45' => ['ร้อยเอ็ด', 'Roi Et'],
            '46' => ['กาฬสินธุ์', 'Kalasin'],
            '47' => ['สกลนคร', 'Sakon Nakhon'],
            '48' => ['นครพนม', 'Nakhon Phanom'],
            '49' => ['มุกดาหาร', 'Mukdahan'],
            '50' => ['เชียงใหม่', 'Chiang Mai'],
            '51' => ['ลำพูน', 'Lamphun'],
            '52' => ['ลำปาง', 'Lampang'],
            '53' => ['อุตรดิตถ์', 'Uttaradit'],
            '54' => ['แพร่', 'Phrae'],
            '55' => ['น่าน', 'Nan'],
            '56' => ['พะเยา', 'Phayao'],
            '57' => ['เชียงราย', 'Chiang Rai'],
            '58' => ['แม่ฮ่องสอน', 'Mae Hong Son'],
            '60' => ['นครสวรรค์', 'Nakhon Sawan'],
            '61' => ['อุทัยธานี', 'Uthai Thani'],
            '62' => ['กำแพงเพชร', 'Kamphaeng Phet'],
            '63' => ['ตาก', 'Tak'],
            '64' => ['สุโขทัย', 'Sukhothai'],
            '65' => ['พิษณุโลก', 'Phitsanulok'],
            '66' => ['พิจิตร', 'Phichit'],
            '67' => ['เพชรบูรณ์', 'Phetchabun'],
            '70' => ['ราชบุรี', 'Ratchaburi'],
            '71' => ['กาญจนบุรี', 'Kanchanaburi'],
            '72' => ['สุพรรณบุรี', 'Suphan Buri'],
            '73' => ['นครปฐม', 'Nakhon Pathom'],
            '74' => ['สมุทรสาคร', 'Samut Sakhon'],
            '75' => ['สมุทรสงคราม', 'Samut Songkhram'],
            '76' => ['เพชรบุรี', 'Phetchaburi'],
            '77' => ['ประจวบคีรีขันธ์', 'Prachuap Khiri Khan'],
            '80' => ['นครศรีธรรมราช', 'Nakhon Si Thammarat'],
            '81' => ['กระบี่', 'Krabi'],
            '82' => ['พังงา', 'Phang Nga'],
            '83' => ['ภูเก็ต', 'Phuket'],
            '84' => ['สุราษฎร์ธานี', 'Surat Thani'],
            '85' => ['ระนอง', 'Ranong'],
            '86' => ['ชุมพร', 'Chumphon'],
            '90' => ['สงขลา', 'Songkhla'],
            '91' => ['สตูล', 'Satun'],
            '92' => ['ตรัง', 'Trang'],
            '93' => ['พัทลุง', 'Phatthalung'],
            '94' => ['ปัตตานี', 'Pattani'],
            '95' => ['ยะลา', 'Yala'],
            '96' => ['นราธิวาส', 'Narathiwat']
        ];

        foreach ($provinces as $code => $names) {
            foreach ($names as $name) {
                if ($this->compare_names($name, $province_name)) {
                    log_message('info', "Province code found: $code for $province_name");
                    return $code;
                }
            }
        }

        log_message('error', "Province code not found for: $province_name");
        return null;
    }

    /**
     * 🆕 เปรียบเทียบชื่อ (ไม่สนใจตัวพิมพ์/ช่องว่าง)
     */
    private function compare_names($name1, $name2)
    {
        $clean1 = mb_strtolower(trim($name1));
        $clean2 = mb_strtolower(trim($name2));
        return $clean1 === $clean2;
    }
}