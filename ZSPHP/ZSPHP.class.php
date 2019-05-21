<?php
/**
 * ZSPHP.class.php
 *
 * 紫色医疗框架入口文件
 * 
 * @author WangXiaohui<wangxiaohui0312@gmail.com>
 */
 
//判断LIP_PATH是否定义
if (!defined("LIB_PATH") || !LIB_PATH) {
	exit('请定义常量 LIB_PATH 路径后重试');
}
//判断APP_NAME是否定义
if (!defined("APP_NAME") || !APP_NAME) {
	exit('请定义常量 APP_NAME 路径后重试');
}

date_default_timezone_set('PRC');	//设置时区

//定义框架
define("ZS_PHP_PATH", dirname(__FILE__));

//框架基类
include_once ZS_PHP_PATH . '/Config/ZSPHPConfig.class.php';		//框架配置
include_once ZS_PHP_PATH . '/Common/FunctionBase.php';			//框架共用方法库
include_once ZS_PHP_PATH . '/Common/HttpRequest.class.php';		//http请求处理类库
include_once ZS_PHP_PATH . '/Common/Base.class.php';			//action & model 父类
include_once ZS_PHP_PATH . '/Common/Action.class.php';
include_once ZS_PHP_PATH . '/Common/Model.class.php';
include_once ZS_PHP_PATH . '/Common/Template.class.php';		//模版类
include_once ZS_PHP_PATH . '/Common/GlobalConfig.class.php';		//模版类

//框架DB类
include_once ZS_PHP_PATH . '/DB/DB.class.php';
include_once ZS_PHP_PATH . '/DB/MySql.class.php';

//缓存类
include_once ZS_PHP_PATH . '/Common/Cache.class.php';

//框架日志类
include_once ZS_PHP_PATH . '/Log/Logger.class.php';
include_once ZS_PHP_PATH . '/Log/SystemLog.class.php';


class ZSPHP
{
	/**
	 * 运行
	 */
	public static function run()
	{
		self::_run();
	}

	/**
	 * 验证通过后运行数据层
	 */
	protected static function _run()
	{
		$actionName = ZSPHPConfig::getAction();
		$method = ZSPHPConfig::getMethod();
		
		define("__ACTION_NAME__", $actionName);
		define("__ACTION_METHOD__", $method);
		define("__APP_GROUP__", ZSPHPConfig::getAppGroup());

		if (ZSPHPConfig::getAppGroup()) {
			$actionName = ZSPHPConfig::getAppGroup() . '.' . $actionName;
		}
		
		$action = loadAction($actionName);

		if (! $action || ! method_exists($action, $method)) {
			
			if (ZSPHPConfig::get('DEBUGGING') === true) {
				Logger::error('_run error: action not exist, action: '.__ACTION_NAME__.', method: '.__ACTION_METHOD__,
				HttpRequest::get());
				throw new Exception('action not exist, action: '.__ACTION_NAME__.', method: '.__ACTION_METHOD__);
			} else {
				Logger::error('_run error: action not exist, action: '.__ACTION_NAME__.', method: '.__ACTION_METHOD__,
				HttpRequest::get());
				myExit(); //TODO () 转向到404 页面
			}
		}
		
		$action->$method(HttpRequest::get());
	}
	
	/**
	 * 初始化配置数据
	 * @param  $config
	 */
	public static function init ($config) {
		ZSPHPConfig::setArray($config);
		
		if(C('SESSION')){
			session_start();
		}
		
		// set php log
		$phpLogDir = ZSPHPConfig::getPhpLogDir();
		if ($phpLogDir) {
			if (is_dir ( $phpLogDir)) {
				if (! is_writable ( $phpLogDir )) {
					trigger_error ( "set php log path is not writable： " . $phpLogDir, E_USER_WARNING);
				} else {
					//设置php log 路径文件
					ini_set("error_log",$phpLogDir. date('Y-m-d') . ".log");
				}
			} else {
				if (! @mkdir ( $phpLogDir, 0777, true )) {
					trigger_error ( "create php log path error： " . $phpLogDir, E_USER_WARNING);
				} else {
					//设置php log 路径文件
					ini_set("error_log",$phpLogDir. date('Y-m-d') . ".log");
				}
			}
		}
		
		HttpRequest::init();
		Logger::init();
		Template::init();
	}
}
?>