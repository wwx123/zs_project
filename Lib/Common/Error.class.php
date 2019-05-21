<?php
/**
 * 错误代码定义
 */
final class Error
{
	/****{系统通用报错}****/
    /**
     * SIGN_ERROR
     */
    const SIGN_ERROR = 11;
    /**
     * TIME_OUT
     */
    const TIME_OUT = 10;

	/**
	 *	成功
	 */
	const SUCCESS = 1;

	/**
	 *	错误
	 */
	const ERROR = 0;

	/**
	 *	错误码对应报错信息
	 */
	public static $_ERROR = array(
	    //SYS
	    self :: TIME_OUT => '请求超时',
	    self :: SIGN_ERROR => '签名错误',

	    self :: ERROR => '网络超时',
	    self :: SUCCESS => 'SUCCESS',
	);
	
	/**
	 *	
	 */
	public static function getErrorMessage($code){
		return self::$_ERROR[$code];
	}
}