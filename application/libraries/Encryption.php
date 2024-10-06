<?php
/**
 * Created by PhpStorm.
 * @author: Hussain Ahmad <engrhussainahmad@gmail.com>
 * Date: 5/23/18
 * Time: 11:33 AM
 */

class CI_Encryption
{
    public $key = "&E$#)@*@#^&!(!$^";

    /**
     * Safe Mode Encode
     *
     * @param $string
     * @return mixed|string
     */
    public  function safe_b64encode($string) {

        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    /**
     * Safe Mode Decode
     *
     * @param $string
     * @return bool|string
     */
    public function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    /**
     * Encode Value
     *
     * @param $value
     * @return bool|string
     */
    public  function encode($value){

        if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext));
    }

    /**
     * Decode String
     *
     * @param $value
     * @return bool|string
     */
    public function decode($value){

        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
}