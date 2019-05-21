<?php
/*******************************************************
 * ALIOSS.php
 *
 * 阿里云存储
 *
 * @author		tongxingsheng <294865887@qq.com>
 * @link			
 * @since		Version 2.7
 * @time		2014-9-16
 *
 *******************************************************/

include_once LIB_PATH . '/Common/oss_sdk/Ossali.php';

class Oss  {

    protected $oss_sdk_service;

    function __construct() {
	    
        $this->oss_sdk_service = new Ossali();
        //设置是否打开curl调试模式
        $this->oss_sdk_service->set_debug_mode(false);
        //设置开启三级域名，三级域名需要注意，域名不支持一些特殊符号，所以在创建bucket的时候若想使用三级域名，最好不要使用特殊字符
        $this->oss_sdk_service->set_enable_domain_style(TRUE);
    }

    /*
     * ------------------------------------------------------
     *  Service 相关   获取bucket列表
     * ------------------------------------------------------
     */
     
    function get_service(){
        $response = $this->oss_sdk_service->list_bucket();
        // 	echo '<pre>';
        // 	var_dump($response);
        return $response;
    }
    
    /*
     * ------------------------------------------------------
    *  Bucket命名规范:● 只能包括小写字母，数字● 必须以小写字母或者数字开头● 长度必须在3-255字节之间一个用户最多创建10个。Bucket名在整个OSS中具有全局唯一性，且不能修改
    *  创建bucket oss-cn-qingdao使用青岛数据中心，如不设置默认创建的是杭州数据中心
    *  ziseyiliaovoices 语音
    ziseyiliaoimage 图片
    ziseyiliaovideos 视频
    ziseyiliaofile 文件,logs
    ziseyiliaotest 测试，所有文件
    * ------------------------------------------------------
    */
    
    function create_bucket($bucket,$acl){
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
    
        $response = $this->oss_sdk_service->create_bucket($bucket,$acl);
        return $response;
    }
    
    //删除bucket
    function delete_bucket($bucket){
        $response = $this->oss_sdk_service->delete_bucket($bucket);
        return($response);
        
    }
    
    
    /*
     * ------------------------------------------------------
    *  设置bucket ACL
    * ------------------------------------------------------
    */
    
    function set_bucket_acl($bucket,$acl){
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
        $response = $this->oss_sdk_service->set_bucket_acl($bucket,$acl);
        return $response;
    }
    
    //获取bucket ACL
    function get_bucket_acl($bucket,$options){
        $options = array(
                ALIOSS::OSS_CONTENT_TYPE => 'text/xml',
        );
    
        $response = $this->oss_sdk_service->get_bucket_acl($bucket,$options);
        return $response;
    }
    
    /*
     * ------------------------------------------------------
    *  设置bucket logging
    *  $bucket         需要开启访问日志的库
    *  $target_prefix  日志保存位置的库
    *  $target_prefix  保存日志objct的前缀，可以为空，所有库的日志都保存在ziseyiliaofile中，需要使用前缀区别
    * ------------------------------------------------------
    */
    
    function  set_bucket_logging($bucket,$target_bucket,$target_prefix){
        $response = $this->oss_sdk_service->set_bucket_logging($bucket,$target_bucket,$target_prefix);
        return $response;
    }
    
    //获取bucket logging
    function  get_bucket_logging($bucket){
        $response = $this->oss_sdk_service->get_bucket_logging($bucket);
        return $response;
    }
    
    //删除bucket logging
    function  delete_bucket_logging($bucket){
        $response = $this->oss_sdk_service->delete_bucket_logging($bucket);
        return $response;
    }
    
    
    /*
     * ------------------------------------------------------
     *  查询object列表
     * ------------------------------------------------------
     */
    function list_object($bucket,$options){
        $options = array(
                'delimiter' => '/',//是一个用于对Object名字进行分组的字符。所有名字包含指定的前缀且第一次出现delimiter字符之间的object作为一组元素,即目录分隔符，如为空表示递归目录下所有子目录中的文件全部列出，为/表示只查询当前目录下的文件和文件夹
                'prefix' => '',//限定返回的object key必须以prefix作为前缀。注意使用prefix查询时，返回的key中仍会包含prefix。，即目录名
                'max-keys' => 10,//max-keys用于限定此次返回object的最大数，如果不设定，默认为100，max-keys取值不能大于1000小于0
                //'marker' => 'myobject-1330850469.pdf',//用户设定结果从marker之后按字母排序的第一个开始返回。
        );
        $response = $this->oss_sdk_service->list_object($bucket,$options);
        return($response);
    }
    
    /*
     * ------------------------------------------------------
    *  创建目录  上传一个目录
    *  文件和目录的区别就是后面有没有 / 符号
    * ------------------------------------------------------
    */
    
    function create_directory($bucket,$dir){
        $response  = $this->oss_sdk_service->create_object_dir($bucket,$dir);
        return $response;
    }
    
    
    /*
     * ------------------------------------------------------
    *  通过内容上传文件  根据内容上传，适合上传小文件，因为是在请求body体中上传
    *  @bucket 库
    *  @content 文件内容字符串
    *  @object 文件路径及文件名完整地址
    * ------------------------------------------------------
    */
    
    function upload_by_content($obj,$bucket,$content,$object,$expires = 3600){
        $expires = time()+$expires;
        $upload_file_options = array(
                'content' => $content,
                'length' => strlen($content),
                ALIOSS::OSS_HEADERS => array(
                        'Expires' => date('Y-m-d H:i:s',$expires),//过期时间'2012-10-01 08:00:00'
                ),
        );
    
        $response = $this->oss_sdk_service->upload_file_by_content($bucket,$object,$upload_file_options);
        return $response;            
    }
    
    /*
     * ------------------------------------------------------
     *  通过路径上传文件  根据文件路径上传，适合上传较大文件，小于100M
     *  $file_path      本地文件路径及文件名完整地址
     * ------------------------------------------------------
     */
     
    function upload_by_file($bucket,$object,$file_path){
        $response = $this->oss_sdk_service->upload_file_by_file($bucket,$object,$file_path);
        return $response;
    }
    
    //拷贝object
    function copy_object($from_bucket,$from_object,$to_bucket,$to_object){
        $options = array(
                'content-type' => 'application/json',
        );
    
        $response = $this->oss_sdk_service->copy_object($from_bucket,$from_object,$to_bucket,$to_object,$options);
        return $response;
    }
    
    //获取object meta
    function get_object_meta($bucket,$object){
        $response = $this->oss_sdk_service->get_object_meta($bucket,$object);
        return $response;
    }
    
    //删除object
    function delete_object($bucket,$object){
        $response = $this->oss_sdk_service->delete_object($bucket,$object);
        return $response;
    }
    
    //删除objects
    
    /*
     * ------------------------------------------------------
     *  删除多个文件 最多删除1000个
     *  $objects = array('myfoloder-1349850940/','myfoloder-1349850941/',);
     * ------------------------------------------------------
     */
     
    function delete_objects($bucket,$objects,$quiet=false){
        $options = array(
                'quiet' => $quiet,//响应模式开关 
                //ALIOSS::OSS_CONTENT_TYPE => 'text/xml',
        );
    
        $response = $this->oss_sdk_service->delete_objects($bucket,$objects,$options);
        return $response;
    }
    
    
    /*
     * ------------------------------------------------------
     *  下载object
     *  $filepath  下载文件保存路径及文件名完整地址
     * ------------------------------------------------------
     */
     
    function get_object($bucket,$object,$filepath){
    
        $options = array(
                ALIOSS::OSS_FILE_DOWNLOAD => $filepath,
                //ALIOSS::OSS_CONTENT_TYPE => 'txt/html',
        );
    
        $response = $this->oss_sdk_service->get_object($bucket,$object,$options);
        return $response;
    }
    
    //检测object是否存在
    function is_object_exist($bucket,$object){
        $response = $this->oss_sdk_service->is_object_exist($bucket,$object);
        return $response;
    }
    
    
    /*
     * ------------------------------------------------------
     *  通过multipart上传文件     分片上传单个文件，文件小于分包大小将按普通文件路径方式上传，默认分包5M
     *  $object     上传文件名
     *  $filepath   本地文件路径
     *  $partSize   切片大小
     * ------------------------------------------------------
     */
     
    function upload_by_multi_part($bucket, $object,$filepath,$partSize=5242880){
        $options = array(
                ALIOSS::OSS_FILE_UPLOAD => $filepath,
                'partSize' => $partSize,
        );
    
        $response = $this->oss_sdk_service->create_mpu_object($bucket, $object,$options);
        return $response;
    }
    
    /*
     * ------------------------------------------------------
     *  通过multipart上传整个目录
     *  $dir    本地目录
     *  $recursive  是否递归
     * ------------------------------------------------------
     */
     
    function upload_by_dir($bucket,$dir,$recursive = false){
        $response = $this->oss_sdk_service->create_mtu_object_by_dir($bucket,$dir,$recursive);
        return $response;
    }
    
    /*
     * ------------------------------------------------------
     *  通过multi-part上传整个目录(新版)
     *  $options = array(
     *          'bucket' 	=> 'ziseyiliaotest',
                'object'	=> 'images',                    只能for循环设置每个目录，只上传每个目录下的文件，不递归，不需要加上后缀/表示目录 默认附加
                'directory' => 'C:\Users\w7\Desktop\image', 本地目录
                'exclude'   => '',                          排除   默认 .|..|.svn
                'recursive' => true,                        递归，将指定目录下所有的子目录的文件全部上传到指定的object目录下，没有保存子目录结构
                )
     * ------------------------------------------------------
     */
     
    function batch_upload_file($options){
        $response = $this->oss_sdk_service->batch_upload_file($options);
        return $response;
    }
    

    /*
     * ------------------------------------------------------
     *  设置网站静态托管
     *  $index_document 静态网站的索引文件地址，即一个object文件
     *  $error_document 静态网站的错误文件地址，也是一个object文件
     * ------------------------------------------------------
     */
     
    function  set_bucket_website($bucket,$index_document,$error_document){
        $response = $this->oss_sdk_service->set_bucket_website($bucket,$index_document,$error_document);
        return $response;
    }
    
    //获取bucket website
    function  get_bucket_website($bucket){
        $response = $this->oss_sdk_service->get_bucket_website($bucket);
        return $response;
    }
    
    //删除bucket website
    function  delete_bucket_website($bucket){
        $response = $this->oss_sdk_service->delete_bucket_website($bucket);
        return $response;
    }
    
    /*%**************************************************************************************************************%*/
    //跨域资源共享(CORS)
    
    //设置bucket cors
    function  set_bucket_cors($bucket,$header,$exposseHeader,$origin=array("*"),$method=array("GET"),$maxAgeSeconds=10){
    
        //控制在OPTIONS预取指令中Access-Control-Request-Headers头中指定的header是否允许。在Access-Control-Request-Headers中指定的每个header都必须在AllowedHeader中有一条对应的项。允许使用最多一个*通配符
        $cors_rule[ALIOSS::OSS_CORS_ALLOWED_HEADER] = array("x-oss-test");
        $cors_rule[ALIOSS::OSS_CORS_ALLOWED_METHOD] = $method;                  //允许请求的方法GET,PUT,DELETE,POST,HEAD      
        $cors_rule[ALIOSS::OSS_CORS_ALLOWED_ORIGIN] = $origin;                  //允许的授权来源，可以使用 ×通配符,或 array('www.baidu.com')
        $cors_rule[ALIOSS::OSS_CORS_EXPOSE_HEADER]  = array("x-oss-test1");     //指定允许用户从应用程序中访问的响应头（例如一个Javascript的XMLHttpRequest对象。不允许使用*通配符。
        $cors_rule[ALIOSS::OSS_CORS_MAX_AGE_SECONDS]= $maxAgeSeconds;           //指定浏览器对特定资源的预取（OPTIONS）请求返回结果的缓存时间，单位为秒。
        $cors_rules=array($cors_rule);
    
        $response = $this->oss_sdk_service->set_bucket_cors($bucket, $cors_rules);
        return $response;
    }
    
    //获取bucket cors
    function  get_bucket_cors($bucket){
        $response = $this->oss_sdk_service->get_bucket_cors($bucket);
        return $response;
    }
    
    //删除bucket cors
    function  delete_bucket_cors($bucket){
        $response = $this->oss_sdk_service->delete_bucket_cors($bucket);
        return $response ;
    }
    
    //options object
    function  options_object($bucket, $object, $origin, $request_method, $request_headers){
//         $bucket = 'phpsdk1349849394';
//         $object='1.jpg';
//         $origin='http://www.b.com';
//         $request_method='GET';
//         $request_headers='x-oss-test';
    
        $response = $this->oss_sdk_service->options_object($bucket, $object, $origin, $request_method, $request_headers);
        return $response;
    }
    

/*
 * ------------------------------------------------------
 *  生成签名url,主要用户私有权限下的访问控制   设置私有权限的文件，向外提供授权限时访问连接
 *  $timeout 超时时间
 * ------------------------------------------------------
 */
     
    //
    function get_sign_url($bucket,$object,$timeout){
        $response = $this->oss_sdk_service->get_sign_url($bucket,$object,$timeout);
        return $response;
    }
    
    
}
 
 