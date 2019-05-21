<?php
/**
 *	微信医生端 紫米上传
 *  @author wangxiaohui
 *  @date 2016-10-9
 */

class TestAction extends CPUAction
{
    //初始化父类，获取公共属性、方法
    public function __construct(){
        parent::__construct();
    }

    /**
     * 测试方法
     * @param doctorId
     */
    public function get(){

        //获取请求参数
        $doctorId = $this->getParam('doctorId', 0);
        
        //初始化Test Model类,对应文件名为TestModel.class.php
        $testModel = M('Test');
        
        //请求对应TestModel里的get方法
        $doctorInfo = $testModel->get($doctorId);

        if (!$doctorId) {
            //错误码配置文件再Common文件下 Error.class.php
            $return['code'] = Error :: ERROR;
		    $return['message'] = Error:: getErrorMessage(Error :: ERROR);
		    $return['content'] = '';

            //返回json的公共方法
		    wxPrintJson($return);
        }

        $return['code'] = Error :: SUCCESS;
		$return['message'] = Error:: getErrorMessage(Error :: SUCCESS);
		$return['content'] = array(
            'foo' => "bar"
        );

		wxPrintJson($return);
    }
}
