<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('get_sales_phone')) {
    function get_sales_phone($sale_id = null)
    {
        if (empty($sale_id)) {
            $sale_id = get_config_value('telesales');
        }

        // ตรวจสอบ cache
        $cache_file = APPPATH . 'cache/' . md5('sales_data') . '.cache';

        if (file_exists($cache_file)) {
            $cache_data = unserialize(file_get_contents($cache_file));

            if ($cache_data['expires'] > time()) {
                $sales_data = $cache_data['data'];

                foreach ($sales_data as $sale) {
                    if ($sale['sale_id'] === $sale_id) {
                        return $sale['sale_phone'];
                    }
                }
            }
        }

        // ถ้าไม่เจอใน cache หรือหมดอายุ ให้เรียก API
        $api_url = 'https://www.assystem.co.th/sale_api/index.php';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $sales_data = json_decode($response, true);

            // บันทึก cache
            $cache_data = array(
                'data' => $sales_data,
                'expires' => time() + 300
            );
            file_put_contents($cache_file, serialize($cache_data));

            // หาเบอร์โทร
            foreach ($sales_data as $sale) {
                if ($sale['sale_id'] === $sale_id) {
                    return $sale['sale_phone'];
                }
            }
        }

        // ถ้าหาไม่เจอ return ID เดิม
        return $sale_id;
    }
}
