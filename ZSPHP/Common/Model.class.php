<?php
/**
 * Model.class.php
 *
 * 紫色医疗框架Model父类
 * 
 * @author WangXiaohui<wangxiaohui0312@gmail.com>
 */

class Model extends Base
{
	protected static $DBS = array();	//db类集合
	
	protected $error = '';
	protected $errorCode = 0;
	
	/**
	*	构造函数
	*/
	public function __construct(){
		parent :: __construct();
	}
	
	/**
	*	获取db类
	*	@param string $dbName
	*	@param string $dbClass
	*	@return object
	*/
	public function getDb($dbName = 'default', $dbClass = 'Mysql'){
		
		//获取db库配置
		$dbId = $dbClass . ':' . $dbName;
		$config = ZSPHPConfig::getDbConfig($dbName);
		
		if (!$config) {
			return false;
		}
		
		if (!isset(self::$DBS[$dbId])) {
			$db = new $dbClass($config['DB_HOST'], $config['DB_USER'], $config['DB_PWD'], $config['DB_NAME']);
			self::$DBS[$dbId] = $db;
		}
		
		return self::$DBS[$dbId];
	}
	
	/**
	 * 返回模型的错误信息
	 * @access public
	 * @return string
	 */
	public function getError(){
		return $this->error;
	}

	/**
	 * 返回模型的错误code
	 * @access public
	 * @return int
	 */
	public function getErrorCode(){
		return $this->errorCode;
	}
	
	/**
	 * 根据host 和 dbname 获取数据
	 * @param string $host host:port
	 * @param string $user db user
	 * @param string $pass db password
	 * @param string $dbname
	 * @return object
	 */
	public static function getDbByHost ($host = '', $user = '', $pass = '', $dbname = '', $dbClass = 'MySql') {
		
		$dbId = $host . '_' . $dbname;
		
		if(isset($DBS[$dbId])){
			return $DBS[$dbId];
		}
		
		$db = new $dbClass($host, $user, $pass, $dbname);
		
		return self::$DBS[$dbId] = $db;
	}
	
	/**
	*	获取db类
	*	@param string $dbName
	*	@param string $dbClass
	*	@return object
	*/
	public static function getInstance($dbName = 'default', $dbClass = 'Mysql'){
		
		//获取db库配置
		$dbId = $dbClass . ':' . $dbName;
		$config = ZSPHPConfig::getDbConfig($dbName);
		
		if (!$config) {
			return false;
		}
		
		if (!isset(self::$DBS[$dbId])) {
			$db = new $dbClass($config['DB_HOST'], $config['DB_USER'], $config['DB_PWD'], $config['DB_NAME']);
			self::$DBS[$dbId] = $db;
		}
		
		return self::$DBS[$dbId];
	}

	/**
	 * 设置错误信息
	 * @param int $code
	 * @param string $msg
	 */
	protected function setError($code, $msg)
	{
		$this->errorCode = $code;
		$this->error = $msg;
	}
}

?>