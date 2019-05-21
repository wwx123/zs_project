<?php
/**
 *	微信患者端--答题 紫米上传
 *  @author wangweixi
 *  @date 2019-05-21
 */

class QuestionAction extends CPUAction
{
    //初始化父类，获取公共属性、方法
    public function __construct(){
        parent::__construct();
    }

    /**
     * 获取题目列表
     */
    public function get(){

        //初始化Test Model类,对应文件名为TestModel.class.php
        $model = M('Question');

        //请求对应TestModel里的get方法
        $questionInfo = $model->get();

        if (!$questionInfo) {
            //错误码配置文件再Common文件下 Error.class.php
            $return['code'] = Error :: QUESTION_IS_NULL;
            $return['message'] = Error:: getErrorMessage(Error :: QUESTION_IS_NULL);
            $return['content'] = '';

            //返回json的公共方法
            wxPrintJson($return);
        }

        // 将结果放到统一数组
        $return['code'] = Error :: SUCCESS;
        $return['message'] = Error:: getErrorMessage(Error :: SUCCESS);
        $return['content'] = $questionInfo;

        // 按指定格式返回结果（JSON）
        wxPrintJson($return);
    }

    /**
     * 添加新的题目
     * @param   string  cotent (问题)
     * @param   int     sort (可选，排序)
     */
    public function add()
    {
        //获取请求参数
        $content = $this->getParam('content', '');
        $sort    = $this->getParam('sort', 0);

        if (empty($content) || empty($options)) {
            // 问题不能为空
            $return['code'] = Error :: QUESTION_CONTENT_IS_NULL;
            $return['message'] = Error:: getErrorMessage(Error :: QUESTION_CONTENT_IS_NULL);
            $return['content'] = '';

            // 按指定格式返回结果（JSON）
            wxPrintJson($return);
            exit;
        }

        $model = M('Question');

        // 插入数据
        $data = array();
        $data['q_name'] = $content;
        $data['q_sort'] = $sort;
        $pid = $model->insert($data);

        if ($pid > 0) {
            $return['code'] = Error :: SUCCESS;
            $return['message'] = Error:: getErrorMessage(Error :: SUCCESS);
            $return['content'] = $pid;

            // 按指定格式返回结果（JSON）
            wxPrintJson($return);
        } else {
            $return['code'] = Error :: QUESTION_INSERT_IS_FAIL;
            $return['message'] = Error:: getErrorMessage(Error :: QUESTION_INSERT_IS_FAIL);
            $return['content'] = '';

            // 按指定格式返回结果（JSON）
            wxPrintJson($return);
        }
    }

    /**
     * 添加新的选项
     * @param   string  content（问题）
     * @param
     */
    public function add_option()
    {
        //获取请求参数
        $content = $this->getParam('content', '');
        $sort    = $this->getParam('sort', 0);

        $model = M('Question');

        // 插入数据
        $data = array();
        $data['o_name'] = $content;
        $data['q_id'] = $qid;
        $data['o_sorce'] = $qid;
        $data['q_id'] = $qid;
        $data['q_id'] = $qid;

    }

    /**
     * 判断答题得分
     * @param   string  content (问题和答题)
     * @return  array   答对的题目数目，总得分
     */
    public function grade()
    {
        $model = M('Question');
    }

}
