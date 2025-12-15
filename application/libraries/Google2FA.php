<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Authenticator Library for CodeIgniter 3
 * 
 * ไฟล์นี้ต้องบันทึกเป็น: application/libraries/Google2FA.php
 */

class Google2FA
{
    private $CI;
    private $passCodeLength = 6;
    private $pinModulo;
    
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->pinModulo = pow(10, $this->passCodeLength);
    }
    
    /**
     * สร้าง Secret Key สำหรับ user
     */
    public function generateSecretKey($length = 16)
    {
        $validChars = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
            'Y', 'Z', '2', '3', '4', '5', '6', '7'
        );
        
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $validChars[array_rand($validChars)];
        }
        
        return $secret;
    }
    
    /**
     * สร้าง QR Code URL สำหรับ Google Authenticator
     */
    public function getQRCodeUrl($name, $secret, $title = null, $params = array())
    {
        $width = !empty($params['width']) && (int) $params['width'] > 0 ? (int) $params['width'] : 200;
        $height = !empty($params['height']) && (int) $params['height'] > 0 ? (int) $params['height'] : 200;
        $level = !empty($params['level']) && array_search($params['level'], array('L', 'M', 'Q', 'H')) !== false ? $params['level'] : 'M';
        
        $urlencoded = urlencode('otpauth://totp/'.$name.'?secret='.$secret.'');
        if(isset($title)) {
            $urlencoded .= urlencode('&issuer='.urlencode($title));
        }
        
        return "https://api.qrserver.com/v1/create-qr-code/?size={$width}x{$height}&ecc={$level}&data={$urlencoded}";
    }
    
    /**
     * ตรวจสอบ OTP Code
     */
    public function verifyKey($secret, $key, $window = 4, $useTimeStamp = null)
    {
        $keyInt = (int)$key;
        if ($useTimeStamp === null) {
            $timeStamp = $this->getTimeStamp();
        } else {
            $timeStamp = (int)$useTimeStamp;
        }
        
        $timeStamp = (int)($timeStamp / 30);
        
        for ($i = -$window; $i <= $window; $i++) {
            if ($this->getCode($secret, $timeStamp + $i) == $keyInt) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * สร้าง OTP Code ปัจจุบัน
     */
    public function getCode($secret, $timeSlice = null)
    {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }
        
        $secretkey = $this->base32Decode($secret);
        
        // Pack time into binary string
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);
        // Hash it with users secret key
        $hm = hash_hmac('SHA1', $time, $secretkey, true);
        // Use last nipple of result as index/offset
        $offset = ord(substr($hm, -1)) & 0x0F;
        // grab 4 bytes of the result
        $hashpart = substr($hm, $offset, 4);
        
        // Unpak binary value
        $value = unpack('N', $hashpart);
        $value = $value[1];
        // Only 32 bits
        $value = $value & 0x7FFFFFFF;
        
        $modulo = pow(10, $this->passCodeLength);
        
        return str_pad($value % $modulo, $this->passCodeLength, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get timestamp
     */
    public function getTimeStamp()
    {
        return time();
    }
    
    /**
     * Base32 Decode
     */
    private function base32Decode($secret)
    {
        if (empty($secret)) return '';
        
        $base32chars = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
            'Y', 'Z', '2', '3', '4', '5', '6', '7'
        );
        
        $base32charsFlipped = array_flip($base32chars);
        
        $paddingCharCount = substr_count($secret, '=');
        $allowedValues = array(6, 4, 3, 1, 0);
        if (!in_array($paddingCharCount, $allowedValues)) return false;
        for ($i = 0; $i < 4; $i++){
            if ($paddingCharCount == $allowedValues[$i] &&
                substr($secret, -($allowedValues[$i])) != str_repeat('=', $allowedValues[$i])) return false;
        }
        $secret = str_replace('=','', $secret);
        $secret = str_split($secret);
        $binaryString = "";
        for ($i = 0; $i < count($secret); $i = $i+8) {
            $x = "";
            if (!in_array($secret[$i], $base32chars)) return false;
            for ($j = 0; $j < 8; $j++) {
                $x .= str_pad(base_convert(@$base32charsFlipped[@$secret[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }
            $eightBits = str_split($x, 8);
            for ($z = 0; $z < count($eightBits); $z++) {
                $binaryString .= ( ($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48 ) ? $y:"";
            }
        }
        return $binaryString;
    }
    
    /**
     * Fix the issue with the padding
     */
    private function fixT($inp) {
        if(strlen($inp) % 8 == 0) return $inp;
        return $this->fixT($inp.'=');
    }
	
	
public function getQRCodeGoogleUrl($company, $holder, $secret, $size = 200, $issuer = null)
{
    $params = array('width' => $size, 'height' => $size);
    
    // ใช้ format ที่ต้องการ: "ยืนยันตัวตนประชาชน : example.com : user@email.com"
    $name = $company;
    if ($issuer) {
        $name = $issuer . ' : ' . $holder;  // เพิ่มช่องว่างรอบ :
    } else {
        $name = $company . ' : ' . $holder;  // เพิ่มช่องว่างรอบ :
    }
    
    return $this->getQRCodeUrl($name, $secret, $issuer, $params);
}
	
	
}