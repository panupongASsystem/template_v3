<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qrcode_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function request_qr_payment()
    {
        $ch = curl_init();
        $form_field = array();
        // กำหนดค่าต่าง ๆ ที่จำเป็น
        $form_field['partnerTxnUid']  = substr(uniqid(rand(), true), 0, 15);
        $form_field['partnerId']  = 'ใส่ Partner ID ';
        $form_field['partnerSecret']  = 'ใส่ Client Secret';
        $form_field['requestDt']  = '2018-01-03T12:30:00+07:00';
        $form_field['merchantId']  = 'ใส่ Merchant ID ดูที่ User Info';
        $form_field['terminalId']  = 'term1';
        $form_field['qrType']  = '3';
        $form_field['txnAmount']  = 100.50;
        $form_field['txnCurrencyCode']  = 'THB';
        $form_field['reference1']  = 'INV001';
        $form_field['reference2']  = NULL;
        $form_field['reference2']  = NULL;
        $form_field['reference4']  = NULL;
        $form_field['metadata']  = 'ปลาร้าสับแพคถุง 100 บาท ยั่ว ๆ จ้าาา';

        $post_string = json_encode($form_field);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Cache-Control:no-cache'
            )
        );

        curl_setopt($ch, CURLOPT_URL, 'https://apiportal.kasikornbank.com:12002/pos/qr_request');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);
        $response = json_decode($data);

        // ส่งผลลัพธ์กลับ
        return $response;
    }
}
