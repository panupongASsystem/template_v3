<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Line_backend extends CI_Controller
{
    private $channel_id;
    private $channel_secret;
    private $callback_url;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('line_model');
        $this->load->library('session');
        $this->load->helper('url');

        // ใช้ helper function เพื่อดึงค่า config
        $this->channel_id = get_config_value('line_channel_id');
        $this->channel_secret = get_config_value('line_channel_secret');
        $this->callback_url = site_url('Line_backend/callback');

        $this->check_session_timeout();
    }

    public function login()
    {
        if (empty($this->channel_id) || empty($this->callback_url)) {
            show_error('LINE Login configuration is incomplete');
            return;
        }

        $state = bin2hex(random_bytes(16));
        $this->session->set_userdata('line_login_state', $state);

        $login_url = "https://access.line.me/oauth2/v2.1/authorize?";
        $login_url .= "response_type=code";
        $login_url .= "&client_id=" . urlencode($this->channel_id);
        $login_url .= "&redirect_uri=" . urlencode($this->callback_url);
        $login_url .= "&state=" . $state;
        $login_url .= "&scope=profile%20openid";

        redirect($login_url);
    }

    public function callback()
    {
        if ($this->input->get('error')) {
            show_error('Authentication failed: ' . $this->input->get('error_description'));
            return;
        }

        $saved_state = $this->session->userdata('line_login_state');
        $returned_state = $this->input->get('state');

        if ($saved_state !== $returned_state) {
            show_error('Invalid state parameter');
            return;
        }

        $code = $this->input->get('code');
        if (!$code) {
            show_error('Authorization code not received');
            return;
        }

        $token_url = "https://api.line.me/oauth2/v2.1/token";

        $post_data = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->callback_url,
            'client_id' => $this->channel_id,
            'client_secret' => $this->channel_secret
        );

        $ch = curl_init($token_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $token_data = json_decode($response, true);

        if (!isset($token_data['access_token'])) {
            show_error('Failed to get access token');
            return;
        }

        $profile_url = "https://api.line.me/v2/profile";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $profile_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token_data['access_token']
        ));

        $profile_response = curl_exec($ch);
        curl_close($ch);

        $profile_data = json_decode($profile_response, true);

        if (isset($profile_data['userId'])) {
            $line_data = array(
                'line_user_id' => $profile_data['userId'],
                'line_name' => $profile_data['displayName'],
                'line_picture' => $profile_data['pictureUrl'],
                'line_status' => 'show'
            );

            if ($this->line_model->save_line_user($line_data)) {
                // ส่ง response กลับเป็น HTML ที่มี JavaScript
                echo '
                <script>
                    alert("ลงทะเบียนเรียบร้อย");
                    window.location.href = "' . site_url('Line_backend') . '";
                </script>';
                exit;
            } else {
                show_error('Failed to save LINE user data');
            }
        } else {
            show_error('Failed to get user profile');
        }
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
        // print_r($_SESSION);
        $data['query'] = $this->line_model->list();
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        // exit;
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/line', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function add()
    {
        // 		echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;

        $this->line_model->add();
        redirect('line_backend', 'refresh');
    }

    public function editing($line_id)
    {
        $data['rsedit'] = $this->line_model->read($line_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/line_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function edit($line_id)
    {
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->line_model->edit($line_id);
        redirect('line_backend', 'refresh');
    }

    public function del($line_id)
    {
        // print_r($_POST);
        $this->line_model->del($line_id);
        redirect('line_backend', 'refresh');
    }

    public function update_line_status()
    {
        $this->line_model->update_line_status();
    }

    public function testLine()
    {
        $this->load->model('complain_model');  // load model ของคุณ
        $this->complain_model->testGetLineUsers();
    }
}
