<?php
header("Content-type: text/html; charset=utf-8");

define("LIB_PATH", dirname(__FILE__));
define("VERSION", "v1.0");
define("APP_NAME", 'weixin_doctor');

error_reporting(E_ALL);
ini_set("display_errors", true);

include_once LIB_PATH . '/Config/Config.php';
include_once LIB_PATH . '/Common/Function.php';

include_once LIB_PATH . '../../ZSPHP/ZSPHP.class.php';

include_once LIB_PATH . '/Common/CPUAction.class.php';
include_once LIB_PATH . '/Common/Error.class.php';
include_once LIB_PATH . '/Common/Url.class.php';
include_once LIB_PATH . '/Common/Oss.php';
ZSPHP::init(Config::$_CONFIGS);