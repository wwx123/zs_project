<?php
/**
 * 嘟嘟医生
 */
final class Url
{
	
	const URL = 'index.html';
	
	/**
	 *	医生绑定
	 */
	const DOCTOR_LOGIN = 101;

    /**
	 *
	 */
	public static $_URL = array(
		
		self :: DOCTOR_LOGIN     => 'login',
	);
	
	/**
	 *	根据嘟嘟医生API错误信息 获取WEB嘟嘟医生报错信息码
	 *	@param string $code 嘟嘟医生API错误信息
	 *	@return WEB嘟嘟医生错误码
	 */
	public static function getUrl($url) {
		
		logger::debug('getUrl $url:', $url);
		
		if(isset(self::$_URL[$url])){
			return self :: URL . self::$_URL[$url];
		}
	}
	
}
?>