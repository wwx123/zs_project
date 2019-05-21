<?php
/**
 * PKCS7Encoder class * 
 * 提供基于PKCS7算法的加解密接口.
 */
class PKCS7Encoder {
    
	public static $block_size = 32;
	
	/**
	 * 对需要加密的明文进行填充补位
	 * @param text 需要进行填充补位操作的明文
	 * @return 补齐128位的明文字符串
	 */
    static public function encode ($text) {
 		$block_size = self::$block_size;
		$text_length = strlen($text);
		//计算需要填充的位数
		$amount_to_pad = $block_size - ($text_length % $block_size);
		if ($amount_to_pad == 0) {
			$amount_to_pad = self::block_size;
		}		
		//获得补位所用的字符
		$pad_chr = chr( $amount_to_pad );
		$tmp = "";
		for($index = 0; $index < $amount_to_pad; $index ++) {
			$tmp .= $pad_chr;
		}
		return $text.$tmp;
	}
	
	/**
	 * 对解密后的明文进行补位删除
	 * @param decrypted 解密后的明文
	 * @return 删除填充补位后的明文
	 */
	static public function decode ($text) {		
		$pad = ord( substr($text, -1 ) );
 		if($pad<1 || $pad >31){
			$pad = 0;
		} 
		return substr( $text, 0 , ( strlen( $text )- $pad ));
	}
}

/**
 * aes加密类
 */
class Aes {

    static public function encrypt($text,$password,$appid) {
        $password = base64_decode($password . "=");
        //获得16位随机字符串，填充到明文之前
        $random = getRandStr();   
        $text = $random . pack("N", strlen($text)) . $text . $appid;
        $size = mcrypt_get_block_size ( MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC );
        $module = mcrypt_module_open ( MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '' );
        $iv = substr ( $password, 0, 16 );
        //使用自定义的填充方式对明文进行补位填充 
        $text = PKCS7Encoder::encode($text);
        mcrypt_generic_init ( $module, $password, $iv );
        //加密
        $encrypted = mcrypt_generic ( $module, $text );
        mcrypt_generic_deinit ( $module );
        mcrypt_module_close ( $module );
        //使用BASE64对加密后的字符串进行编码
        return base64_encode ( $encrypted );        
    }

    static public function decrypt($encrypted,$password) {
        $password = base64_decode($password . "=");
        //使用BASE64对需要解密的字符串进行解码
        $ciphertext_dec = base64_decode ( $encrypted );
        $module = mcrypt_module_open ( MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '' );
        $iv = substr ( $password, 0, 16 );
        mcrypt_generic_init ( $module, $password, $iv );
        //解密
        $decrypted = mdecrypt_generic ( $module, $ciphertext_dec );
        mcrypt_generic_deinit ( $module );
        mcrypt_module_close ( $module );
        //去除补位字符     
        $result = PKCS7Encoder::decode ( $decrypted );
        //去除16位随机字符串        
        $content = substr($result, 16, strlen($result));
        $len_list = unpack("N", substr($content, 0, 4));
        $xml_len = $len_list[1];
        $xml_content = substr($content, 4, $xml_len);
        $from_appid = substr($content, $xml_len + 4);        
        return array('content'=>$xml_content,'app_id'=>$from_appid);
    }
}