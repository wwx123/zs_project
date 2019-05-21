<?php
/**
 * FunctionBase.php
 *
 * 紫色医疗框架全局共用方法
 * 
 * @author WangXiaohui<wangxiaohui0312@gmail.com>
 */

/**
*	设置或获取配置
*	@param string $name
*	@param string $value
*	return bool | string
*/
function C ($name = null, $value = null) {
	
	if($name == null){
		return false;
	}
	
	if ($value !== null) {
		return ZSPHPConfig::set($name, $value);
	}
	
	return ZSPHPConfig::get($name);
}

//加载model
function M ($model) {
	return loadModel($model);
}
//加载action
function A ($action) {
	return loadAction($action);
}

/**
 * 加载action
 *
 * @param string $action package 格式 从Action目录开始使用 "."分割
 */
function loadAction($action) {
	if (empty ( $action ))
		return null;
	static $actions = array ();
	if (isset ( $actions [$action] )) {
		return $actions [$action];
	}
	$actArr = explode ( '.', $action );
	if (count ( $actArr ) > 1) {
		$actionName = $actArr [count ( $actArr ) - 1] . 'Action';
	} else {
		$actionName = $action . 'Action';
	}
	$file = LIB_PATH . '/Action/' . str_replace ( '.', '/', $action ) . 'Action.class.php';
	if (! file_exists ( $file )) {
		if (class_exists('Logger')) {
			Logger::error ( "action file not exist : " . $file );
		}
		return null;
	}
	
	include_once ($file);
	$actions [$action] = new $actionName ();
	return $actions [$action];
}

/**
 * 加载model
 *
 * @param string $model	package 格式 从Model目录开始使用 "."分割
 */
function loadModel($model) {
	if (empty ( $model ))
		return null;
	static $models = array ();
	if (isset ( $models [$model] )) {
		return $models [$model];
	}
	$modelArr = explode ( '.', $model );
	if (count ( $modelArr ) > 1) {
		$modelName = $modelArr [count ( $modelArr ) - 1] . 'Model';
	} else {
		$modelName = $model . 'Model';
	}
	$file = LIB_PATH . '/Model/' . str_replace ( '.', '/', $model ) . 'Model.class.php';
	if (! file_exists ( $file ) && class_exists('Logger')) {
		Logger::error ( "model file not exist : " . $file );
		return null;
	}
	include_once ($file);
	$models [$model] = new $modelName ();
	return $models [$model];
}

/**
* 加载模版
*/
function Tpl($fileName){
	Template::include_tpl($fileName);
}

/**
*	加载外部类库
*/
function ORG($name, $param = ''){
	loadORG($name, $param);
}

/**
*	加载外部类库
*/
function loadORG($name, $param = ''){
	if (empty ( $name ))
		return null;
		
	static $orgs = array ();
	if (isset ( $orgs [$name] )) {
		return $orgs [$name];
	}
	$orgArr = explode ( '.', $name );
	if (count ( $orgArr ) > 1) {
		$orgName = $orgArr [count ( $orgArr ) - 1];
	} else {
		$orgName = $name;
	}
	$file = ZS_PHP_PATH . '/Org/' . str_replace ( '.', '/', $orgName ) . '.class.php';
	if (! file_exists ( $file ) && class_exists('Logger')) {
		Logger::error ( "org file not exist : " . $file );
		return null;
	}
	
	include_once ($file);
	
	//$orgs [$name] = new $orgName ();
	//return $orgs [$name];
}

/**
 * 获取微信token
 *
 * @param string $appId
 * @param string $appSercet
 * @param bool $refresh  如果cache中不存在是否刷新cache
 * @return string
 */
function getToken($appId, $appSercet, $refresh = true){
	$cacherId = 'WX_API_TOKEN' . $appId;
	
	$cacher = Cache :: init();
	$token = $cacher->get($cacherId);
	
	logger::debug('getToken cache:' . $cacherId, $token);
	
	if ((! $token) || ($refresh == false)) {
		if (! class_exists ( "WeiXinClient" )) {
			include_once ZS_PHP_PATH . "/Api/WeiXinApiCore.class.php";
		}
		
		$weixnApi = WeiXinApiCore::getClient ( $appId, $appSercet );
		$result = $weixnApi->getToken();
		
		logger::debug('getToken $result:', $result);
		
		if ($result) {
			$token = $result->token;
			$expires_in = $result->expires_in - 200;
			
			$cacher->set ( $cacherId, $token, $expires_in);
		}
	}
	
	logger::debug('getToken return :', $token);
	
	return $token;
}

/**
*	生成url
*/
function url($action = null, $method = null, $params = array(), $prefixUrl = null) {
	$params[ZSPHPConfig::ACTION_NAME] = $action;
	$params[ZSPHPConfig::METHOD_NAME] = $method;
	$query = http_build_query($params);
	if(!isset($prefixUrl) || $prefixUrl == null){
		return  HttpRequest::getUri(). '/index.php' . ($query ? '?'.$query : '');
	}else{
		$prefixUrl = ltrim($prefixUrl,'/');
		return resetUrl(HttpRequest::getUri(). '/'.$prefixUrl,$params);		
	}
}

/**
*	生成url
*/
function echoUrl($action = null, $method = null, $params = array(), $prefixUrl = null) {
	$params[ZSPHPConfig::ACTION_NAME] = $action;
	$params[ZSPHPConfig::METHOD_NAME] = $method;
	$query = http_build_query($params);
	if(!isset($prefixUrl) || $prefixUrl == null){
		echo HttpRequest::getUri(). '/index.php' . ($query ? '?'.$query : '');
	}else{
		$prefixUrl = ltrim($prefixUrl,'/');
		echo resetUrl(HttpRequest::getUri(). '/'.$prefixUrl,$params);		
	}
}

//将参数添加到指定url后
function resetUrl ($url, $queryData = array(), $html = false) {
	
	$fragment = '';
	$findex = strpos($url, '#');
	
	if (false !== $findex) {
		$fragment = substr($url, $findex);
	}
	$url = rtrim(str_replace($fragment, '', $url), '&');
	
	if($html){
		$url = $url.'?'.$fragment. ($queryData ? (false == strrpos($url, '?')?'?':'&').http_build_query($queryData) : '');
	}else{
		$url = $url.($queryData ? (false == strrpos($url, '?')?'?':'&').http_build_query($queryData).$fragment : '');
	}
	
	return $url;
}

/**
 * 终止程序函数
 */
function myExit($msg = '') {
	$obj = new SystemLog();
	$obj->setLogFormat(SystemLog::FORMAT_JSON);
	$obj->setLogpath(ZSPHPConfig::getSysLogDir());
	//TODO 处理终止前程序
	$obj->start();
	$obj->flush();
	
	if ($msg) echo $msg;
	exit();
}

/**
 * 获取系统日志实例
 * @return SystemLog
 */
 function getSystemLog () {
	 
	$obj = new SystemLog();
	$obj->setLogFormat(SystemLog::FORMAT_JSON);
	$obj->setLogpath(ZSPHPConfig::getSysLogDir());
	$SYS_LOG = $obj;
	
	return $SYS_LOG;
}

/**
 * 转义字符
 *
 * @param mixed $var
 */
function faddslashes($var) {
	if (is_array ( $var )) {
		foreach ( $var as $k => $v ) {
			$var [$k] = faddslashes ( $v );
		}
	} else {
		$var = addslashes ( $var );
	}
	return $var;
}

/**
 * 转义html字符
 *
 * @param string|array $var
 */
function fhtmlspecialchars($var) {
	if (is_array ( $var )) {
		foreach ( $var as $k => $v ) {
			$var [$k] = fhtmlspecialchars ( $v );
		}
	} else if (is_string ( $var )) {
		$var = htmlspecialchars ( $var, ENT_COMPAT, 'UTF-8' );
	}
	return $var;
}

/**
 * 过滤html标签.
 *
 * @param string $var target string
 * @param string $tags 允许保留到标签,all 为去全部
 */
function fstripTags($var, $tags = 'all') {
	$tags = strval ( $tags );
	if ($tags !== 'all') {
		if (is_array ( $var )) {
			foreach ( $var as $k => $v ) {
				$var [$k] = fstripTags ( $v );
			}
		} else if (is_string ( $var )) {
			$var = strip_tags ( $var, $tags );
		}
	}
	return $var;
}

/**
*	检测手机号是否合法
*	@param int $mobile
*	@return bool
*/
function checkMobile($mobile){
	if(empty($mobile)){
		return false;
	}
    return preg_match('/^1[3-9][0-9]{9}$/',$mobile);
}

/**
*	输出json
*	@param array $data
*/
function printJson($data){
	//logger::debug('printJson : ', var_export($data, true));
	echo json_encode($data);
	die;
}

/**
*	输出 sse
*/
function sseJson($data){
	
	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache');
	
	logger::debug('sseJson : ', var_export($data, true));
	echo 'data:' . json_encode($data) ."\n\n";
	die;
}

/**
 * 获取微信组件token
 *
 * @param string $appId
 * @param string $appSercet
 * @param bool $refresh  如果cache中不存在是否刷新cache
 * @return string
 */
function getComponectToken($appId, $appSercet, $ticket, $refresh = true){

	$cacherId = GlobalCatchId::WX_API_COMPONECT_TOKEN . $appId;
	
	$cacher = Cache :: init();
	$token = $cacher->get($cacherId);
	
	if (true !== $refresh) {
		return $token;
	}
	if (! $token) {
		// 引入微信api
		if (! class_exists ( "WeiXinComponectClient" )) {
			include_once ZS_PHP_PATH . "/Api/WeiXinApiCore.class.php";
		}
		$weixnComponectApi = WeiXinApiCore::getComponectClient ( $appId, $appSercet,$ticket );
		$token = $weixnComponectApi->getToken ();
		if ($token) {
			
			$expires_in = $token['expires_in'];
			$token = $token['component_access_token'];
			$cacher->set ( $cacherId, $token, ($expires_in - 600)/*一小时50分钟*/);
		}
	}
	
	return $token;
}

/**
 * 获取微信组件通信的ticket
 *
 * @param string $appId 组件的appid
 * @return string 从缓存里取，取不到由应用到数据库中取。
 * 		该值是微信定时推送过来的。
 */
function getComponectTicket(){
	$cacherId = C('APP_ID') . '_' . GlobalCatchId::WX_API_COMPONECT_TICKET;
	$cacher = Cache :: init();
	$ticket = $cacher->get ( $cacherId );
	Logger::debug("getComponectTicket 1 cacherId:$cacherId ticket:$ticket");
	return $ticket;
}

/**
 * 获取微信组件预授权码
 *
 * @param string $appId
 * @param string $appSercet
 * @param string $token
 * @return string
 */
function getPreAuthCode($appId, $appSercet, $ticket, $token){
	
	if ($token) {
		// 引入微信api
		if (! class_exists ( "WeiXinComponectClient" )) {
			include_once ZS_PHP_PATH . "/Api/WeiXinApiCore.class.php";
		}
		
		$weixnComponectApi = WeiXinApiCore::getComponectClient ( $appId, $appSercet, $ticket, $token);
		
		$code = $weixnComponectApi->getPreAuthCode();
		
		return $code;
	}
}

/**
*	验证来源
*	@param string $token		签名token
*	@param string $sign			微信签名
*	@param string $timestamp	时间
*	@param string $nonce		随机字符串
*	@return boolen
*/
function checkSignature($token, $sign, $timestamp, $nonce){
	
	$tmpArr = array($token, $timestamp, $nonce);
	
	sort($tmpArr, SORT_STRING);
	$tmpStr = implode( $tmpArr );
	$tmpStr = sha1( $tmpStr );
	
	if( $tmpStr == $sign ){
		return true;
	}else{
		return false;
	}
}

/**
* 获取授权方accessToken
* 
* @param string $appId
* @param string $appSercet
* @param string $ticket
* @param string $token
* @param string $refreshToken
* @param string $refresh 如果cache中不存在是否刷新cache
*/
function getAuthToken($appId, $appSercet, $ticket, $CPCtoken, $authAppId, $refreshToken, $refresh = true){
	
	$cacherId = GlobalCatchId::WX_WEB_AUTH_TOKEN . $authAppId;
	$cacher = Cache :: init();
	
	if($authAppId == ''){
		$token = false;
	}else{
		$token = $cacher->get ( $cacherId );
	}
	
	
	Logger::debug("getAuthToken 1 cacherId:$cacherId");
	
	if (!$refresh) {
		Logger::debug("getAuthToken YES 1 cacherId:$cacherId TOKEN: $token");
		return $token;
	}
	if (!$token) {
		// 引入微信api
		if (! class_exists ( "WeiXinComponectClient" )) {
			include_once ZS_PHP_PATH . "/Api/WeiXinApiCore.class.php";
		}
		Logger::debug("getAuthToken 2 cacherId:$cacherId");
		$weixnComponectApi = WeiXinApiCore::getComponectClient ( $appId, $appSercet, $ticket, $CPCtoken);
		$token = $weixnComponectApi->flushAuthorizationToken($authAppId, $refreshToken);
		
		if ($token) {
			Logger::debug("getAuthToken 3 cacherId:$cacherId");
			$expires_in = $token['expires_in'];
			$token = $token['authorizer_access_token'];
			$cacher->set ( $cacherId, $token, ($expires_in - 600));
		}
		
	}
	Logger::debug("getAuthToken YES 2 cacherId:$cacherId TOKEN: $token");
	return $token;
}

/**
*	获取客户端ip
*/
function getIp(){
	
	if (getenv("HTTP_X_FORWARDED_FOR")) 
	{ 
		$ip = getenv("HTTP_X_FORWARDED_FOR"); 
	} 
	elseif (getenv("HTTP_CLIENT_IP")) 
	{ 
		$ip = getenv("HTTP_CLIENT_IP"); 
	} 
	elseif (getenv("REMOTE_ADDR"))
	{ 
		$ip = getenv("REMOTE_ADDR"); 
	} 
	else 
	{ 
		return false;
	}
	
	return $ip;
}

/**
* 生成随机字符串
*/
function randChar($length = 32)
{
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$str = "";
	for($i = 0; $i < $length; $i ++) {
		$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	}
	return $str;
}

function isWxWebKit(){
$user_agent = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($user_agent, 'MicroMessenger') === false) {
	    return false;
	}else{
		return true;
	}
}

function cliExit($msg){
	exit($msg . "\r\n");
}

/**
 *	获取企业授权用户信息
 *	@param string $code
 *	@return array
 */
function getCPCUserInfoByCode($appId, $appSercet, $authAppId, $code){
	
	//获取TICKET
	$ticket = getComponectTicket();
	//获取TOKEN
	$cpcToken = getComponectToken($appId, $appSercet, $ticket);
	
	$getTokenUrl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid={$authAppId}&code={$code}&grant_type=authorization_code&component_appid={$appId}&component_access_token={$cpcToken}";
	
	//获取TOKEN
	$tokenResult = file_get_contents($getTokenUrl);
	
	$tokenData = json_decode($tokenResult, true);
	
	if(!isset($tokenData['access_token'])){
		return false;
	}
	
	
	$userInfoUrl = "https://api.weixin.qq.com/sns/userinfo?access_token={$tokenData['access_token']}&openid={$tokenData['openid']}&lang=zh_CN";
	
	
	//获取TOKEN
	$userInfoResult = file_get_contents($userInfoUrl);
	
	$userInfo = json_decode($userInfoResult, true);
	
	return $userInfo;
}

?>