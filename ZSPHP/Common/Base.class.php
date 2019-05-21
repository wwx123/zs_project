<?php
/**
*	Base.class.php
*	
*	Action & Model 共用父类
*
*	@author WangXiaohui<wangxiaohui0312@gmail.com>
*/

class Base
{
	/**
	*	构造函数
	*/
	public function __construct(){
		
	}
	
	/**
	*	获取post | get 参数
	*/
	public function getParam($name = null, $default = null, $htmQuotes = true, $tags = null){
		return HttpRequest::get($name, $default, $htmQuotes, $tags);
	}
}
?>