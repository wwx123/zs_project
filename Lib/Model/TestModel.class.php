<?php
/**
 *	医生紫米表
 */
class TestModel extends Model
{
    private $_table = 'doctor';    //医生表

    /*
    *	构造函数
    */
    public function __construct()
    {
        parent::__construct();

        $this->_db = $this->getDb(Config::DB_ZS_TEST);

        $this->_Title = "微信医生端";    //设置主标题
    }

    /**
     * 查看用户列表
     * @param doctorId int
     * @return bool|array
     */
    public function get($doctorId){

        $sql = "SELECT * FROM $this->_table WHERE doctor_id = $doctorId";

        try {
            //框架封装数据方法再ZSPHP/DB/DB.class.php
            $result = $this->_db->getAll($sql);

            return $result;

        } catch ( Exception $e ) {
            Logger::error($this->_Title.' TestModel->get() 查询出错:', $e->getMessage() . "\n" . $this->_db->getLastSql());
            return false;
        }
    }
}