<?php

define('DOCTOR_DEFULT_HEAD', '/doctorheads/mr_doctor.jpg');

Class Config
{
	const DB_ZS_TEST = 'DB_ZS_TEST';
	const DB_OPEN_WX = 'DB_OPEN_WX';

	public static $_CONFIGS = array(

		'VERSION' => '171211',

		'DEBUGGING' => false,//debug 模式
		'ENABLE_RUN_LOG' => TRUE,//是否开启运行日志
		'ENABLE_SQL_LOG' => TRUE,//是否开启sql日志
		'ENABLE_SYSTEM_LOG' => FALSE,//是否开启system日志
		'SESSION' => TRUE,	//是否启用session
		'RUN_SHELL' => false, //运行方式是否为脚本方式
		'RUN_LOG_LEVEL' => LOG_E_INFO,//运行日志级别

		'SMS_CODE_NUM' => 4, //验证码条数

		'PRODUCE' => TRUE,

		'LOG_PATH' => '/mnt/logs/',//日志目录，以“/”结束

		'PHP_CLI_PATH' => 'php',//php脚本命令		
		'DEFAULT_ACTION' => 'Index',//默认ACTION
		'DEFAULT_METHOD' => 'index',//默认METHOD

		//数据库配置
		'DB_CONFIGS' => array(


			'DB_ZS_TEST' => array(
				'DB_HOST'=>'127.0.0.1',
				'DB_NAME'=>'sz',
				'DB_USER'=>'root',
				'DB_PWD'=>'root',
			),

			'DB_OPEN_WX' => array(
				'DB_HOST'=>'127.0.0.1',
			    'DB_NAME'=>'sz',
			    'DB_USER'=>'root',
				'DB_PWD'=>'root',
			),
		),
	);
}
