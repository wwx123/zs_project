<?php
/**
 * Logger.class.php
 *
 * 紫色医疗框架日志基类
 * 
 * @author WangXiaohui<wangxiaohui0312@gmail.com>
 */
include_once ZS_PHP_PATH . '/Log/LogBase.class.php';
class Logger extends LoggerBase
{
	public static function init () {
		self::setLogDir(ZSPHPConfig::getLogDir());
		self::enabled(ZSPHPConfig::get('ENABLE_RUN_LOG'));
		self::setLogLevel(ZSPHPConfig::get('RUN_LOG_LEVEL'));
		parent::init();
	}
}
