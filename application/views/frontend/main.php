<?php
try {
   date_default_timezone_set('Asia/Bangkok');
   
   $current_domain = $_SERVER['HTTP_HOST'];
   $current_domain = preg_replace('#^https?://#', '', $current_domain);
   $current_domain = preg_replace('/^www\./', '', $current_domain);
   $current_domain = strtok($current_domain, '/');
   $current_domain = strtolower(trim($current_domain));
   
   error_log('Cleaned domain: ' . $current_domain);
   
   $ch = curl_init();
   curl_setopt_array($ch, [
       CURLOPT_URL => 'https://assystem.co.th/api/day/check_important_day/' . urlencode($current_domain),
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_SSL_VERIFYPEER => false,
       CURLOPT_SSL_VERIFYHOST => false,
       CURLOPT_TIMEOUT => 30,
       CURLOPT_HTTPHEADER => ['X-Original-Domain: ' . $current_domain]
   ]);
   
   $response = curl_exec($ch);
   $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
   curl_close($ch);
   
   error_log('API Response Code: ' . $http_code);
   error_log('API Response: ' . $response);
   
   if ($response && $http_code === 200) {
       $result = json_decode($response, true);

       if ($result['is_important_day'] === false) { // เปลี่ยนจาก !isset หรือ === false
        $redirect_url = 'https://' . $current_domain . '/home';
        header('Location: ' . $redirect_url);
        exit;
    }
       
       if ($result['is_important_day'] === true) {
           $ch = curl_init();
           $template_url = sprintf(
               'https://assystem.co.th/day/templates/%s?site_domain=%s',
               $result['template_name'],
               urlencode($current_domain)
           );
           
           curl_setopt_array($ch, [
               CURLOPT_URL => $template_url,
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_SSL_VERIFYPEER => false,
               CURLOPT_SSL_VERIFYHOST => false,
               CURLOPT_TIMEOUT => 30,
               CURLOPT_HTTPHEADER => ['X-Original-Domain: ' . $current_domain]
           ]);
           
           $template_content = curl_exec($ch);
           $template_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
           curl_close($ch);
           
           if ($template_content && $template_http_code === 200) {
               echo $template_content;
               exit;
           }
           throw new Exception('Template fetch failed: ' . $template_http_code);
       }
   }
} catch (Exception $e) {
   error_log('Error: ' . $e->getMessage());
}

$redirect_url = 'https://' . $current_domain . '/home';
header('Location: ' . $redirect_url);
exit;