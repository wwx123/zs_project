<?php

/**
 * 微信JS-SDK Pay签名生成类
 * 
 * @author wangxiaohui
 * @Date : 2015-08-07
 */
 
include_once ZS_PHP_PATH . "/Api/WeiXinApiCore.class.php";

class WxPaySign
{
	/**
	 * 微信 appId 参数,第三方用户唯一凭证
	 */
	public $appId = null;
	/**
	*
	*/
	
	/**
	 * 构造函数
	 * 
	 * @param string $appid 微信APPID
	 * @return void
	 */
	public function __construct($appid = '')
	{
		$this->appId = $appid;
		
		$payApi = WeiXinApiCore::getWXPayClient();
	}
	
	/**
	*	生成签名
	*/
	private function _CreateSign(){
		$this->_data = $this->_createData();
		
		if(!$this->_data){
			return false;
		}
		
		//排序key
		ksort($this->_data);
		
		$tmpArray = array();
		
		foreach($this->_data as $k => $v){
			$tmpArray[] = $k . '=' . $v;
		}
		
		$string = implode('&', $tmpArray);
		
		$this->_sign = strtoupper(md5($string.'&key='.$this->key));
		
		$this->_data['sign'] = $this->_sign;
		
		return true;
	}
}
?>