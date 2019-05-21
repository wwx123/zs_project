<?php
/**
 * 错误代码定义
 */
final class Error
{
	/****{系统通用报错}****/
	/** 问卷相关报错以7开头 **/
    /**
     * SIGN_ERROR
     */
    const SIGN_ERROR = 11;
    /**
     * TIME_OUT
     */
    const TIME_OUT = 10;

    /**
     * QUESTION_IS_NULL
     */
    const QUESTION_IS_NULL = 700;
    const QUESTION_CONTENT_IS_NULL = 701;
    const QUESTION_INSERT_IS_FAIL  = 702;

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

	    // 问卷
        self :: QUESTION_IS_NULL => '暂无问卷问题',
        self :: QUESTION_CONTENT_IS_NULL => '问卷问题不能为空',
        self :: QUESTION_INSERT_IS_FAIL  => '问卷问题插入失败',

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