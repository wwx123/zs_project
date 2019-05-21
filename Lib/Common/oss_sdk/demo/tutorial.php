<?php
/**
 * 加载sdk包以及错误代码包
 */
require_once '../sdk.class.php';

$oss_sdk_service = new ALIOSS();
//设置是否打开curl调试模式
$oss_sdk_service->set_debug_mode(false);
//设置开启三级域名，三级域名需要注意，域名不支持一些特殊符号，所以在创建bucket的时候若想使用三级域名，最好不要使用特殊字符
$oss_sdk_service->set_enable_domain_style(TRUE);

/**
 * 测试程序
 * 目前SDK存在一个bug，在文中如果含有-&的时候，会出现找不到相关资源
 */
try{
	get_service($oss_sdk_service);//Service相关操作   库列表
	
	/**
	 * Bucket相关操作
	 */
//库
// 	create_bucket($oss_sdk_service,'ziseyiliaofile','READ');//oss-cn-qingdao使用青岛数据中心，如不设置默认创建的是杭州数据中心
// 	delete_bucket($oss_sdk_service,'ziseyiliaotest');

//库权限
//  set_bucket_acl($oss_sdk_service,'ziseyiliaotest','READ');
// 	get_bucket_acl($oss_sdk_service,'ziseyiliaotest');
	
//日志
// 	set_bucket_logging($oss_sdk_service,'ziseyiliaotest','ziseyiliaofile','testlogs/');
// 	get_bucket_logging($oss_sdk_service,'ziseyiliaotest');
// 	delete_bucket_logging($oss_sdk_service,'ziseyiliaotest');
	

	/**
	 * Object相关操作
	 */
//     $options = array(
//             'delimiter' => '/',//是一个用于对Object名字进行分组的字符。所有名字包含指定的前缀且第一次出现delimiter字符之间的object作为一组元素,即目录分隔符，如为空表示递归目录下所有子目录中的文件全部列出，为/表示只查询当前目录下的文件和文件夹
//             'prefix' => '',//限定返回的object key必须以prefix作为前缀。注意使用prefix查询时，返回的key中仍会包含prefix。，即目录名
//             'max-keys' => 10,//max-keys用于限定此次返回object的最大数，如果不设定，默认为100，max-keys取值不能大于1000小于0
//             //'marker' => 'myobject-1330850469.pdf',//用户设定结果从marker之后按字母排序的第一个开始返回。
//     );
    
// 	list_object($oss_sdk_service,'ziseyiliaotest',$options);//查询列表
// 	create_directory($oss_sdk_service,'ziseyiliaotest','olds/');//上传一个目录
//     upload_by_content($oss_sdk_service,'ziseyiliaotest','olds/',$content,$object);//根据内容上传，适合上传小文件，因为是在请求body体中上传
//    	upload_by_file($oss_sdk_service,'ziseyiliaotest','voices/replies/6edbb3ad7dbfde65f128803510d08ea8.mp3','C:\Users\w7\Desktop\6edbb3ad7dbfde65f128803510d08ea8.mp3');//根据文件路径上传，适合上传较大文件，小于100M
	//copy_object($oss_sdk_service);//复制
	//get_object_meta($oss_sdk_service); //获取文件head信息
	//delete_object($oss_sdk_service);    //删除一个文件
	//delete_objects($oss_sdk_service);   //删除多个文件
	//get_object($oss_sdk_service);       //获取文件
	//is_object_exist($oss_sdk_service);   //判断文件是否存在
	//upload_by_multi_part($oss_sdk_service); //分片上传单个文件，文件小于分包大小将按普通文件路径方式上传，默认分包5M
	//upload_by_dir($oss_sdk_service); //按目录上传
//     $options = array(
//             'bucket' 	=> 'ziseyiliaotest',
//             'object'	=> 'images',//只能for循环设置每个目录，只上传每个目录下的文件，不递归，不需要加上后缀/表示目录 默认附加
//             'directory' => 'C:\Users\w7\Desktop\image',
// //             'exclude'   => '',//排除   默认 .|..|.svn
//             'recursive' => true,//递归，将指定目录下所有的子目录的文件全部上传到指定的object目录下，没有保存子目录结构
//     );
// 	batch_upload_file($oss_sdk_service,$options);//适合上传超过100M，未知文件大小，网速差，需要续传的， 如果上传的文件小于分包partSize,则直接使用普通文件路径方式上传，而不用拆包上传
	
    
    
}catch (Exception $ex){
	die($ex->getMessage());
}

/**
 * 函数定义
 */
/*%**************************************************************************************************************%*/
// Service 相关

//获取bucket列表
function get_service($obj){
	$response = $obj->list_bucket();
// 	echo '<pre>';
// 	var_dump($response);
	_format($response);
}

/*%**************************************************************************************************************%*/
// Bucket 相关

/*
 * ------------------------------------------------------
 *  Bucket命名规范:● 只能包括小写字母，数字● 必须以小写字母或者数字开头● 长度必须在3-255字节之间一个用户最多创建10个。Bucket名在整个OSS中具有全局唯一性，且不能修改
 *  创建bucket
 *  ziseyiliaovoices 语音
    ziseyiliaoimage 图片
    ziseyiliaovideos 视频
    ziseyiliaofile 文件,logs
    ziseyiliaotest 测试，所有文件
 * ------------------------------------------------------
 */
 
function create_bucket($obj,$bucket,$acl){
	switch ($acl){
		case 'PRIVATE':
    	   $acl = ALIOSS::OSS_ACL_TYPE_PRIVATE;
		    break;
		case 'READ':
        	$acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ;
        	break;
		case 'READ_WRITE':
	       $acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ_WRITE;
		    break;
	    default:
    	   $acl = ALIOSS::OSS_ACL_TYPE_PRIVATE;
            break;		        
	}
	
	$response = $obj->create_bucket($bucket,$acl);
	_format($response);
}

//删除bucket
function delete_bucket($obj,$bucket){
	$response = $obj->delete_bucket($bucket);
	_format($response);
}


/*
 * ------------------------------------------------------
 *  设置bucket ACL
 * ------------------------------------------------------
 */
 
function set_bucket_acl($obj,$bucket,$acl){
	switch ($acl){
		case 'PRIVATE':
		    $acl = ALIOSS::OSS_ACL_TYPE_PRIVATE;//只有库创建者有权限读取删除写入权限，不设置创建库时默认配置此权限
		    break;
		case 'READ':
		    $acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ;//只有库创建者有写，删除权限 ，其他用户只能读取
		    break;
		case 'READ_WRITE':
		    $acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ_WRITE;//所有人都可以读和写，删除
		    break;
		default:
		    $acl = ALIOSS::OSS_ACL_TYPE_PRIVATE;
		    break;
	}
	$response = $obj->set_bucket_acl($bucket,$acl);
	_format($response);
}

//获取bucket ACL
function get_bucket_acl($obj,$bucket){
	$options = array(
		ALIOSS::OSS_CONTENT_TYPE => 'text/xml',
	);
		
	$response = $obj->get_bucket_acl($bucket,$options);
	_format($response);	
}

/*
 * ------------------------------------------------------
 *  设置bucket logging
 *  $bucket         需要开启访问日志的库
 *  $target_prefix  日志保存位置的库
 *  $target_prefix  保存日志objct的前缀，可以为空，所有库的日志都保存在ziseyiliaofile中，需要使用前缀区别
 * ------------------------------------------------------
 */
 
function  set_bucket_logging($obj,$bucket,$target_bucket,$target_prefix){
	$response = $obj->set_bucket_logging($bucket,$target_bucket,$target_prefix);
	_format($response);	
}

//获取bucket logging
function  get_bucket_logging($obj,$bucket){
	$response = $obj->get_bucket_logging($bucket);
	_format($response);	
}

//删除bucket logging
function  delete_bucket_logging($obj,$bucket){
	$response = $obj->delete_bucket_logging($bucket);
	_format($response);	
}




/*%**************************************************************************************************************%*/
// Object 相关

//获取object列表
function list_object($obj,$bucket,$options){
	$response = $obj->list_object($bucket,$options);	
	_format($response);
}

/*
 * ------------------------------------------------------
 *  创建目录
 *  文件和目录的区别就是后面有没有 / 符号
 * ------------------------------------------------------
 */
 
function create_directory($obj,$bucket,$dir){
	$response  = $obj->create_object_dir($bucket,$dir);
	_format($response);
}


/*
 * ------------------------------------------------------
 *  通过内容上传文件
 *  @bucket 库
 *  @folder 目录 qwe/
 *  @content 文件内容字符串
 *  @object 文件名
 * ------------------------------------------------------
 */
 
function upload_by_content($obj,$bucket,$folder,$content,$object){
		$object = $folder.'&#26;&#26;_'.$object;
		$upload_file_options = array(
			'content' => $content,
			'length' => strlen($content),
			ALIOSS::OSS_HEADERS => array(
				'Expires' => date('Y-m-d H:i:s'),//过期时间'2012-10-01 08:00:00'
			),
		);
		
		$response = $obj->upload_file_by_content($bucket,$object,$upload_file_options);	
		echo 'upload file {'.$object.'}'.($response->isOk()?'ok':'fail')."\n";
}

//通过路径上传文件
function upload_by_file($obj,$bucket,$object,$file_path){
// 	$bucket = 'phpsdk1349849394';
// 	$object = 'netbeans-7.1.2-ml-cpp-linux.sh';	
// 	$file_path = "D:\\TDDOWNLOAD\\netbeans-7.1.2-ml-cpp-linux.sh";
	
	$response = $obj->upload_file_by_file($bucket,$object,$file_path);
	_format($response);
}

//拷贝object
function copy_object($obj){
		//copy object
		$from_bucket = 'invalidxml';
		$from_object = '&#26;&#26;_100.txt';
		$to_bucket = 'invalidxml';
		$to_object = '&#26;&#26;_100.txt';
		$options = array(
			'content-type' => 'application/json',
		);

		$response = $obj->copy_object($from_bucket,$from_object,$to_bucket,$to_object,$options);
		_format($response);
}

//获取object meta
function get_object_meta($obj,$bucket,$object){
// 	$bucket = 'invalidxml';
// 	$object = '&#26;&#26;_100.txt'; 

	$response = $obj->get_object_meta($bucket,$object);
	_format($response);
}

//删除object
function delete_object($obj,$bucket,$object){
// 	$bucket = 'invalidxml';
// 	$object = '&#26;&#26;_100.txt'; 
	$response = $obj->delete_object($bucket,$object);
	_format($response);
}

//删除objects
function delete_objects($obj){
	$bucket = 'phpsdk1349849394';
	$objects = array('myfoloder-1349850940/','myfoloder-1349850941/',);   
	
	$options = array(
		'quiet' => false,
		//ALIOSS::OSS_CONTENT_TYPE => 'text/xml',
	);
	
	$response = $obj->delete_objects($bucket,$objects,$options);
	_format($response);
}

//获取object
function get_object($obj){
	$bucket = 'phpsdk1349849394';
	$object = 'netbeans-7.1.2-ml-cpp-linux.sh'; 
	
	$options = array(
		ALIOSS::OSS_FILE_DOWNLOAD => "d:\\cccccccccc.sh",
		//ALIOSS::OSS_CONTENT_TYPE => 'txt/html',
	);	
	
	$response = $obj->get_object($bucket,$object,$options);
	_format($response);
}

//检测object是否存在
function is_object_exist($obj){
	$bucket = 'phpsdk1349849394';
	$object = 'netbeans-7.1.2-ml-cpp-linux.sh';  
							
	$response = $obj->is_object_exist($bucket,$object);
	_format($response);
}

//通过multipart上传文件
function upload_by_multi_part($obj){
	$bucket = 'phpsdk1349849394';
	$object = 'Mining.the.Social.Web-'.time().'.pdf';  //英文
	$filepath = "D:\\Book\\Mining.the.Social.Web.pdf";  //英文
		
	$options = array(
		ALIOSS::OSS_FILE_UPLOAD => $filepath,
		'partSize' => 5242880,
	);

	$response = $obj->create_mpu_object($bucket, $object,$options);
	_format($response);
}

//通过multipart上传整个目录
function upload_by_dir($obj){
	$bucket = 'phpsdk1349849394';
	$dir = "D:\\alidata\\www\\logs\\aliyun.com\\oss\\";
	$recursive = false;
	
	$response = $obj->create_mtu_object_by_dir($bucket,$dir,$recursive);
	var_dump($response);	
}

//通过multi-part上传整个目录(新版)
function batch_upload_file($obj,$options){
	$response = $obj->batch_upload_file($options);
}
//网站静态托管******************************************************************************

//设置bucket website
function  set_bucket_website($obj){
    $bucket = 'phpsdk1349849394';
    $index_document='index.html';
    $error_document='error.html';

    $response = $obj->set_bucket_website($bucket,$index_document,$error_document);
    _format($response);
}

//获取bucket website
function  get_bucket_website($obj){
    $bucket = 'phpsdk1349849394';

    $response = $obj->get_bucket_website($bucket);
    _format($response);
}

//删除bucket website
function  delete_bucket_website($obj){
    $bucket = 'phpsdk1349849394';

    $response = $obj->delete_bucket_website($bucket);
    _format($response);
}

/*%**************************************************************************************************************%*/
//跨域资源共享(CORS)

//设置bucket cors
function  set_bucket_cors($obj){
    $bucket = 'phpsdk1349849394';

    $cors_rule[ALIOSS::OSS_CORS_ALLOWED_HEADER]=array("x-oss-test");
    $cors_rule[ALIOSS::OSS_CORS_ALLOWED_METHOD]=array("GET");
    $cors_rule[ALIOSS::OSS_CORS_ALLOWED_ORIGIN]=array("http://www.b.com");
    $cors_rule[ALIOSS::OSS_CORS_EXPOSE_HEADER]=array("x-oss-test1");
    $cors_rule[ALIOSS::OSS_CORS_MAX_AGE_SECONDS] = 10;
    $cors_rules=array($cors_rule);

    $response = $obj->set_bucket_cors($bucket, $cors_rules);
    _format($response);
}

//获取bucket cors
function  get_bucket_cors($obj){
    $bucket = 'phpsdk1349849394';

    $response = $obj->get_bucket_cors($bucket);
    _format($response);
}

//删除bucket cors
function  delete_bucket_cors($obj){
    $bucket = 'phpsdk1349849394';

    $response = $obj->delete_bucket_cors($bucket);
    _format($response);
}

//options object
function  options_object($obj){
    $bucket = 'phpsdk1349849394';
    $object='1.jpg';
    $origin='http://www.b.com';
    $request_method='GET';
    $request_headers='x-oss-test';

    $response = $obj->options_object($bucket, $object, $origin, $request_method, $request_headers);
    _format($response);
}

/*%**************************************************************************************************************%*/
// 签名url 相关

//生成签名url,主要用户私有权限下的访问控制   设置私有权限的文件，向外提供授权限时访问连接
function get_sign_url($obj){
	$bucket = 'phpsdk1349849394';
	$object = 'netbeans-7.1.2-ml-cpp-linux.sh';
	$timeout = 3600;

	$response = $obj->get_sign_url($bucket,$object,$timeout);
	var_dump($response);
}

/*%**************************************************************************************************************%*/
// 结果 相关

//格式化返回结果
function _format($response) {
    echo '<pre>';
	echo '|-----------------------Start---------------------------------------------------------------------------------------------------'."\n";
	echo '|-Status:' . $response->status . "\n";
	echo '|-Body:' ."\n"; 
    var_dump($response->body) . "\n";
	echo "|-Header:\n";
	print_r ( $response->header );
	echo '-----------------------End-----------------------------------------------------------------------------------------------------'."\n\n";
}




