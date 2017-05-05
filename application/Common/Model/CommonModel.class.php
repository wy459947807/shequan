<?php

/* * 
 * 公共模型
 */

namespace Common\Model;
use Think\Model;

class CommonModel extends Model {
    
    
    public $result = array(
        "status" => 200, 
        "msg" => "操作成功！",
        "data" => ""
    );//返回数据格式
    
    public $page=1;             //当前页码
    public $pageLimit=10;       //每页显示条数
    
    public $sqlFrom="";         //数据库查询表
    public $sqlField="*";       //数据库查询字段
    public $sqlWhere=" (1=1) "; //数据库查询条件
    public $sqlGroupby="" ;     //数据库查询分组
    public $sqlLimit="";        //数据库查询限制条数
    public $sqlOrder="";        //数据库查询排序
    public $bindValues=array(); //数据库查询pdo字段绑定
    
    
    public function getPageList(){
        $model = M();
        $offset = ($this->page - 1) * $this->pageLimit;
        $sqlLimit=" limit {$offset},{$this->pageLimit} ";
        
        $list_sql="SELECT ".$this->sqlField." FROM ".$this->sqlFrom." WHERE  ".$this->sqlWhere.$this->sqlOrder.$this->sqlGroupby.$sqlLimit;
        $count_sql = "SELECT COUNT(*) as num FROM " .$this->sqlFrom." WHERE  ".$this->sqlWhere;
        
        $count = $model->query($count_sql,$this->bindValues);      
        $list  = $model->query($list_sql,$this->bindValues);
               
        $resultData['list']=$list;
        
        //分页信息
        $resultData['pageInfo']=array();
        $resultData['pageInfo']['page']=$this->page;                                    //当前页数
        $resultData['pageInfo']['pageLimit']=$this->pageLimit;                          //每页显示条数
        $resultData['pageInfo']['num']=$count[0]['num'];                                //总条数
        $resultData['pageInfo']['pageNum']= ceil($count[0]['num']/$this->pageLimit);    //总页数
        $this->result['data']=$resultData;
        return $this->result;  
                
    }
    
    public function getAll(){
        $model = M();
        $list_sql="SELECT ".$this->sqlField." FROM ".$this->sqlFrom." WHERE  ".$this->sqlWhere.$this->sqlOrder.$this->sqlGroupby.$this->sqlLimit;

        $list  =  $model->query($list_sql,$this->bindValues);
        $this->result['data']=$list;
        return $this->result;  
    }
    
    public function getOne(){
        $model = M();
        $data_sql="SELECT ".$this->sqlField." FROM ".$this->sqlFrom." WHERE  ".$this->sqlWhere.$this->sqlOrder.$this->sqlGroupby.$this->sqlLimit;
        $data    = $model->query($data_sql,$this->bindValues);
        $this->result['data']=$data[0];
        return $this->result;  
    }
    

    /**
     * 删除表
     */
    final public function drop_table($tablename) {
        $tablename = C("DB_PREFIX") . $tablename;
        return $this->query("DROP TABLE $tablename");
    }

    /**
     * 读取全部表名
     */
    final public function list_tables() {
        $tables = array();
        $data = $this->query("SHOW TABLES");
        foreach ($data as $k => $v) {
            $tables[] = $v['tables_in_' . strtolower(C("DB_NAME"))];
        }
        return $tables;
    }

    /**
     * 检查表是否存在 
     * $table 不带表前缀
     */
    final public function table_exists($table) {
        $tables = $this->list_tables();
        return in_array(C("DB_PREFIX") . $table, $tables) ? true : false;
    }

    /**
     * 获取表字段 
     * $table 不带表前缀
     */
    final public function get_fields($table) {
        $fields = array();
        $table = C("DB_PREFIX") . $table;
        $data = $this->query("SHOW COLUMNS FROM $table");
        foreach ($data as $v) {
            $fields[$v['Field']] = $v['Type'];
        }
        return $fields;
    }

    /**
     * 检查字段是否存在
     * $table 不带表前缀
     */
    final public function field_exists($table, $field) {
        $fields = $this->get_fields($table);
        return array_key_exists($field, $fields);
    }
    
    protected function _before_write(&$data) {
        
    }

}

