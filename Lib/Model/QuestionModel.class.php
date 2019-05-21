<?php
/**
 *	问题紫米表
 */
class QuestionModel extends Model
{
    private $_table = 'questions';    //医生表
    private $_field = ' q_id,q_name,q_sort ';
    private $_answer_table = 'options';    //选项表
    private $_answer_field = ' o_id,o_name,q_id,o_score,o_is_answer ';

    /*
    *	构造函数
    */
    public function __construct()
    {
        parent::__construct();

        $this->_db = $this->getDb(Config::DB_ZS_TEST);

        $this->_Title = "入组问卷";    //设置主标题
    }

    /**
     * 查看问题列表
     * @param doctorId int
     * @return bool|array
     */
    public function get(){

        $options = self::getOptions();

        $sql = "SELECT $this->_field FROM $this->_table";

        try {
            //框架封装数据方法再ZSPHP/DB/DB.class.php
            $result = $this->_db->getAll($sql);

            if ($result != false) {
                foreach ($result as $key => $value) {
                    $result[$key]['options'] = isset($options[$value['q_id']]) ? $options[$value['q_id']] : array();
                }
            }

            return $result;

        } catch ( Exception $e ) {
            Logger::error($this->_Title.' QuestionModel->get() 查询出错:', $e->getMessage() . "\n" . $this->_db->getLastSql());
            return false;
        }
    }

    /**
     * 插入选项
     * @param   array   $data  $content（问题）$sort（排序）
     * @return  bool|int
     */
    public function insert($data){
        try {

            $result = $this->_db->insert($this->_table, $data);

            return $result;

        } catch ( Exception $e ) {
            Logger::error($this->_Title.' QuestionModel->insert() 插入出错:', $e->getMessage() . "\n" . $this->_db->getLastSql());
            return false;
        }
    }

    /**
     * 添加选项
     * @param   array   $options
     * @return bool
     */
    public function option_insert($options){
        if (empty($options)) {
            return false;
        }

        try {
            $result = $this->_db->insert($this->_answer_table, $options);

            return $result;

        } catch ( Exception $e ) {
            Logger::error($this->_Title.' QuestionModel->option_insert() 插入出错:', $e->getMessage() . "\n" . $this->_db->getLastSql());
            return false;
        }
    }

    /**
     * 获取数据库所有选项
     * @return bool|array
     */
    private function getOptions(){

        $sql = "SELECT $this->_answer_field FROM $this->_answer_table ORDER BY o_sort DESC,o_id ASC";

        try {
            $result = $this->_db->getAll($sql);

            if ($result != false) {
                $res = array();
                $data = array();
                foreach ($result as $key => $value) {
                    $data['content'] = $value['o_name'];
                    $data['score']   = $value['o_score'];
                    $data['is_answer'] = $value['o_is_answer'];
                    $res[$value['q_id']][] = $data;
                }
                $result = $res;
            }

            return $result;
        } catch ( Exception $e ) {
            Logger::error($this->_Title.' QuestionModel->getOptions() 查询出错:', $e->getMessage() . "\n" . $this->_db->getLastSql());
            return false;
        }
    }
}