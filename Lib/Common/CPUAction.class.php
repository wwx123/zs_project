<?php
/**
 *	微信医生端－基类
 *	@author
 *	@date 2016-8-5
 */

class CPUAction extends Action
{

	/**
	 *	doctor id
	 */
	public $_did = 0;

	/**
	 *	openid
	 */
	public $_openid = '';

    /**
     *	unionid
     */
    public $_unionid = '';

	/**
	 * 是否绑定
	 */
	public $_isbinding = 0;

    /**
     * 医生信息
     */
    public $_doctorInfo = array();

	/**
	 * 请求action
	 */
	private $_action = '';

	/**
	 * 请求method
	 */
	private $_method = '';

	/**
	 *	构造函数
	 */
	public function __construct(){
		parent::__construct();

		//微信用户授权
		$this->_auth();

		//判断是否已绑定
		if(!empty($this->_openid)){

		}
	}

	/**
	 *	微信用户授权
	 */
	private function _auth(){

		$this->_openid = $this->getParam('openid', '');
		$param = $this->getParam();
		$time = $this->getParam('time','');

		//TODO 纪录SESSION
		if($this->_openid){
			if(!$time || time()-$time > 20 || $time > time()){
				
				$toUrl = '';

				header("location:{$toUrl}");
				exit();
			}
			$_SESSION['openid'] = $this->_openid;
    	}

		if(!$this->_openid && isset($_SESSION['openid'])){
			$this->_openid = $_SESSION['openid'];
		}

		$this->_action = strtolower(__ACTION_NAME__);
		$this->_method = strtolower(__ACTION_METHOD__);

		logger :: debug('param______', $param);
	}

}
