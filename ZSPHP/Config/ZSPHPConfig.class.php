<?php
/**
 * ZSPHPConfig.class.php
 *
 * 紫色医疗框架配置文件
 * 
 * @author WangXiaohui<wangxiaohui0312@gmail.com>
 */
 class ZSPHPConfig
 {
	const ACTION_NAME = 'a';	//action tag
	const METHOD_NAME = 'm';	//method tag
	const TEMPLATE_DIR = '/View/';	//以lib目录为根
	const TEMPLATE_PREFIX = '.php';	//模版文件后缀名
	const APP_NAME = APP_NAME;	//应用名称

	protected static $_CONFIGS = array(
			'PUBLIC_SERVICE' => false,//是否为正式服务，否：cache不会启用redis
			'DEBUGGING' => TRUE,//debug 模式
			'ENABLE_RUN_LOG' => TRUE,//是否开启运行日志
			'ENABLE_SQL_LOG' => TRUE,//是否开启sql日志
			'ENABLE_SYSTEM_LOG' => TRUE,//是否开启system日志
			'RUN_SHELL' => false, //运行方式是否为脚本方式
			'RUN_LOG_LEVEL' => LOG_E_ALL,//运行日志级别
			'LOG_PATH' => './',//日志目录，以“/”结束
			'PHP_CLI_PATH' => '/usr/local/php/bin/php',//php脚本命令

			'DEFAULT_ACTION' => 'Index',//默认ACTION
			'DEFAULT_METHOD' => 'index',//默认METHOD
			'APP_GROUP'  => '',//App GROUP
			'VAR_AJAX_SUBMIT' => 'ajax',//ajax请求标识
			
			//数据库配置
			'DB_CONFIGS' => array(),
			
			);

	//数据库配置
	protected static $DB_CONFIGS = array ();

	/**
	 * 获取配置数据
	 */
	public static function get($name) {
		if (!$name) {
			return self::$_CONFIGS;
		}

		return @self::$_CONFIGS[$name];
	}
	/**
	 * 获取配置数据
	 */
	public static function set($name, $value) {
		if (!$name) {
			return false;
		}
		
		self::$_CONFIGS[$name] = $value;
		
		return true;
	}

	/**
	 * 设置配置数据
	 */
	public static function setArray($config) {
		if (!is_array($config)) {
			return;
		}
		foreach ($config as $key => $value) {
			if ('DB_CONFIGS' == $key) {
				self::setDbConfig($value);
			}
			self::$_CONFIGS[$key] = $value;
		}
	}

	/**
	 * 获得实际的操作名称
	 * @return string
	 */
	static public function getAction()
	{
		$action = HttpRequest::get(self::ACTION_NAME);
		$action = !empty($action) ? $action : self::get('DEFAULT_ACTION');
		
		//命令行模式
		if(php_sapi_name() == 'cli'){
			$args = getopt('a:m:');
			$action = !empty($args['a']) ? $args['a'] : self::get('DEFAULT_ACTION');
		}
		
		return ucwords($action);
	}

	/**
	 * 获得实际的模块名称
	 * @return string
	 */
	static public function getMethod()
	{
		$method = HttpRequest::get(self::METHOD_NAME);
		$method = !empty($method) ? $method : self::get('DEFAULT_METHOD');
		
		//命令行模式
		if(php_sapi_name() == 'cli'){
			$args = getopt('a:m:');
			$method = !empty($args['m']) ? $args['m'] : self::get('DEFAULT_METHOD');
		}
		
		return $method;
	}
	
	/**
	 * 获得实际的app_group名称
	 * @return string
	 */
	public static function getAppGroup()
	{
		return self::$_CONFIGS['APP_GROUP'];
	}

	//获取sql日志目录
	static function getSqlLogDir() {
		return self::getBaseLogDir() . 'sql/';
	}
	
	//获取运行日志路径
	static function getLogDir() {
		return self::getBaseLogDir() . 'log/';
	}
	
	//获取php日志路径
	static function getPhpLogDir()
	{
		if(php_sapi_name() == 'cli'){
			$dir = self::$_CONFIGS['LOG_PATH'].'shell/' . self::APP_NAME.'/';
		}else{
			$dir = self::$_CONFIGS['LOG_PATH'].'web/' . self::APP_NAME.'/';
		}
		
		$dir .= 'php_log/';
		
		return $dir;
	}
	
	//获取log基础目录
	private static function getBaseLogDir () {
		
		if(php_sapi_name() == 'cli'){
			$dir = self::$_CONFIGS['LOG_PATH'].'shell/' . self::APP_NAME.'/';
		}else{
			$dir = self::$_CONFIGS['LOG_PATH'].'web/' . self::APP_NAME.'/';
		}
		
		if (self::$_CONFIGS['APP_GROUP']) {
			$dir .= self::$_CONFIGS['APP_GROUP'].'/';
		}
		return $dir;
	}
	
	
	/**
	 * 获取db配置
	 * @param string $dbName
	 * @param string
	 */
	static function setDbConfig($config) {
		if (!$config || !is_array($config)) return;
		self::$DB_CONFIGS = $config;
	}
	/**
	 * 获取db配置
	 * @param string $dbName
	 */
	static function getDbConfig($dbName) {
		return @self::$DB_CONFIGS[$dbName];
	}
	
	//获取system日志路径
	static function getSysLogDir()
	{
		if(php_sapi_name() == 'cli'){
			$dir = self::$_CONFIGS['LOG_PATH'].'shell/' . self::APP_NAME.'/';
		}else{
			$dir = self::$_CONFIGS['LOG_PATH'].'web/' . self::APP_NAME.'/';
		}

		$dir .= 'sys/';
		
		return $dir;
	}
	
}

?>