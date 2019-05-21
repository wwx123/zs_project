<?php
function curl_post($url,$data,$header,$post=1){
	$ch = curl_init();
	//参数设置
	$res= curl_setopt ($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt ($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, $post);
	if($post)
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
	$result = curl_exec ($ch);
	//连接失败
	if($result == FALSE){
			$result = "{\"statusCode\":\"172001\",\"statusMsg\":\"网络错误\"}";
	}
	
	curl_close($ch);
	return $result;
}

function getMonthNum($date){
    $date1_stamp = strtotime($date);
    $date2_stamp = time();
    
    list($date_1['y'],$date_1['m']) = explode("-",date('Y-m',$date1_stamp));
    list($date_2['y'],$date_2['m']) = explode("-",date('Y-m',$date2_stamp));
    
    return abs($date_1['y']-$date_2['y']) * 12 + $date_2['m']-$date_1['m'];
 }

//返回字节数
function transfer_age($str, $ageType = false){
	
	$m = getMonthNum($str);
	
	$age = '';
	
	if($m >= 12){
		$arr = explode('-', $str);
		
		$age = date('Y') - $arr[0];
			
		$age = $ageType ? $age . 'y' : $age . '岁';

	}else{
		$age = $m . "个月";
	}
	
	return $age;
}

/* 
*功能：php完美实现下载远程文件保存到本地 
*参数：文件url,保存文件目录,保存文件名称，使用的下载方式 
*当保存文件名称为空时则使用远程文件原来的名称 
*/
function getFile($url, $save_dir = '', $filename = '', $type = 1){
    if(trim($url)==''){
        return array('file_name'=>'','save_path'=>'','error'=>1);
    }
    if(trim($save_dir) == ''){
        $save_dir='./';
    }
    if(trim($filename) == ''){//保存文件名  
        $ext=strrchr($url,'.');

        $filename=time().$ext;
    }

    if(0!==strrpos($save_dir,'/')){
        $save_dir.='/';
    }

    //创建保存目录  
    if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
        return array('file_name'=>'','save_path'=>'','error'=>5);
    }

    if($type){
        $ch=curl_init();
        $timeout=3;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $img=curl_exec($ch);
        curl_close($ch);
    }else{
        ob_start();
        readfile($url);
        $img=ob_get_contents();
        ob_end_clean();
    }

        //$size=strlen($img);

    //文件大小
    $fp2=@fopen($save_dir.$filename,'w');
    fwrite($fp2,$img);
    fclose($fp2);
    //unset($img,$url);

    $urlData = pathinfo($save_dir . $filename);


//    logger :: error('filename', $urlData);

//    if($urlData['']){
//
//    }

//    if($urlData['extension'] != 'amr'){
//        image_sy($url, $save_dir . $filename);
//    }

    $fileMd5 = md5_file($save_dir . $filename);

    return array(
        'file_name' => $filename,
        'save_path' => $save_dir . $filename,
        'save_dir' => $save_dir,
        'file_md5' => $fileMd5,
        'error' => 0
    );
}

function getHead($url, $save_dir, $filename = ''){
	
	if(trim($url)==''){  
        return array('file_name'=>'','save_path'=>'','error'=>1);  
    }  
    if(trim($save_dir) == ''){  
        $save_dir='./';  
    }  
    if(trim($filename) == ''){//保存文件名  
        $ext=strrchr($url,'.');
        $filename=time().$ext;  
    }
    
    if(0!==strrpos($save_dir,'/')){  
        $save_dir.='/';  
    }

    //创建保存目录  
    if(!file_exists($save_dir)&&!mkdir($save_dir, 0777, true)){
        return array('file_name'=>'','save_path'=>'','error'=>5);  
    }
    
    
    $img = file_get_contents($url);
	
	$fp2 = @fopen($save_dir . $filename, 'w');
    fwrite($fp2, $img);
    fclose($fp2);
    unset($img, $url);
	
	return array(
    	'file_name' => $filename, 
    	'save_path' => $save_dir . $filename, 
    	'save_dir' => $save_dir, 
    	'error' => 0
    	);  
}

function upFileToOss($path, $file){
	$oss = New Oss();
	$oss->upload_by_file(C('ZI_ALIOSS_BUCKET'), $path, $file);
}

//返回字节数
function len_char($str){
    return (strlen($str) + mb_strlen($str,'UTF8')) / 2;
}

function wxPrintJson($data){
	
//	$data['redirect'] = Error :: getRedirect($data['code']);
	
	logger::debug('wxPrintJson : ', var_export($data, true));
	
	echo json_encode($data);
	die;
}

function arrayLcfirst($data){
	
	foreach($data as $key => $val){
		
		$lKey = lcfirst($key);
		
		unset($data[$key]);
		
		if(is_array($val)){
			$val = arrayLcfirst($val);
		}
		
		$data[$lKey] = $val;
	}
	
	return $data;
}

function wzPrintJson($data){
	
	logger::debug('wzPrintJson : ', var_export($data, true));
	
	$data = arrayLcfirst($data);
	
	echo json_encode($data);
	die;
}


/*
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function createLinkstring($para) {
	$arg  = "";
	while (list ($key, $val) = each ($para)) {
		$arg.=$key."=".$val."&";
	}
	//去掉最后一个&字符
	$arg = substr($arg,0,count($arg)-2);

	//如果存在转义字符，那么去掉转义
	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

	return $arg;
}

/*
 * 对数组排序
 * @param $para 排序前的数组
 * return 排序后的数组
 */
function argSort($para) {
	ksort($para);
	reset($para);
	return $para;
}



function getZKToken($appid, $appSecret){
	$tokenCacherId = 'WDYS_ACCESS_TOKEN2';

	//获取accesstoken
	$cacher = Cache :: init();
	$token = $cacher->get ( $tokenCacherId );

	logger::debug('getWxJsSign token:' , $token);

	if ( ! $token ){

		$token = getToken($appid, $appSecret);

		$cacher->set($tokenCacherId, $token, 7000);//缓存10分钟
	}

	return $token;
}

//雁阵证件号，银行卡是否合法
function luhn($no) {

	$arr_no = str_split($no);
	$last_n = $arr_no[count($arr_no)-1];
	krsort($arr_no);
	$i = 1;
	$total = 0;
	foreach ($arr_no as $n){
		if($i%2==0){
			$ix = $n*2;
			if($ix>=10){
				$nx = 1 + ($ix % 10);
				$total += $nx;
			}else{
				$total += $ix;
			}
		}else{
			$total += $n;
		}
		$i++;
	}

	$total -= $last_n;
	$total *= 9;

	if($last_n == ($total%10)){
		return true;
	}

	return false;
}


function ageToDate($age){
	$age = rtrim($age);
	//格式化年龄
    $ageType = substr($age, -1, strlen($age));
    $ageNum = substr($age, 0, -1);

    if(!is_numeric($ageNum)){
	    return false;
    }

    if($ageType == 'm')
    {
        $agedayes = $ageNum * 30;
        return date('Y-m', strtotime('-' . $agedayes . ' days'));

    }else if($ageType == 'y')
    {
        return date('Y') - $ageNum . '-01';
    }

    return false;
}

function isMobile($tel){
	$isMob="/^1[3-9]{1}[0-9]{9}$/";
	$isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
	if(!preg_match($isMob,$tel) && !preg_match($isTel,$tel)){
		return false;
	}

	return true;
}
