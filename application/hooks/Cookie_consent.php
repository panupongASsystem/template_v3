<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cookie_consent {
    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
    }

    public function check_consent() {
        $this->CI->load->helper('cookie');
        $cookie_consent = get_cookie('cookie');
        $this->CI->load->vars(['show_cookie_consent' => empty($cookie_consent)]);
    }
}