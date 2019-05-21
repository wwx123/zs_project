<?php
/**
 * Action.class.php
 *
 * 紫色医疗框架Action父类
 * 
 * @author WangXiaohui<wangxiaohui0312@gmail.com>
 */
 class Action extends Base
 {
	private $_error = 0;
	/**
	*	构造函数
	*/
	public function __construct(){
		parent :: __construct();
	}
	
	/**
	*	判断是否报错
	*/
	public function isError(){
		return 0 !== $this->_error;
	}
	
	/**
	*	设置错误码
	*/
	public function setError($error){
		$this->_error = $error;
	}
	
	/**
	*	获取错误码
	*/
	public function getError(){
		return $this->_error;
	}
	
	/**
	 *	设置模版变量
	 */
	function assign($tpl_var, $value = null)
	{
		Template :: assign($tpl_var, $value);
	}

	/**
	*	显示模版
	*/
	function display($tpl_name = null)
	{
		Template :: display($tpl_name);
	}
	
 }
?>