<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['recaptcha'] = array(
    'site_key' => get_config_value('recaptcha'),
    'secret_key' => get_config_value('secret_key_recaptchar'),
    'enabled' => true,
    'debug_mode' => (ENVIRONMENT === 'development'),
    'min_score_default' => 0.5,
    'min_score_staff' => 0.6,
    'min_score_citizen' => 0.5,
    'min_score_admin' => 0.8,
    'timeout' => 10
);