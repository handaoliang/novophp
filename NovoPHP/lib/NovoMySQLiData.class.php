<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: NovoMySQLiData.class.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2014/12/09 12:55:38 $
 * @copyright            Copyright (c) 2014, Comnovo Inc.
**/
/**
 * 通用MySQL数据库连接类。
 * 有些函数依赖于CommonModel.func.php，须先Include通用函数库。
 **/
class NovoMySQLiData
{
    protected $MySQLDBConn    = NULL;
    protected $MySQLDBConfig  = "";
    protected $MySQLQueryDB   = "master";

    public $DBTablePre = "";

    /**
     * 构造函数
     * @return void 
     */
    public function __construct()
    {
        if(!is_array($this->MySQLDBConfig) || empty($this->MySQLDBConfig))
        {
            die('Can\'t connect MySQL Database : MySQL Database Config Error, Please Check.');
        }
        //print_r($this->MySQLDBConfig);

        $this->MySQLConn(
            $this->MySQLDBConfig[$this->MySQLQueryDB]["db_host"],
            $this->MySQLDBConfig[$this->MySQLQueryDB]["db_port"],
            $this->MySQLDBConfig[$this->MySQLQueryDB]["db_user"],
            $this->MySQLDBConfig[$this->MySQLQueryDB]["db_password"],
            $this->MySQLDBConfig[$this->MySQLQueryDB]["db_name"],
            $this->MySQLDBConfig[$this->MySQLQueryDB]["db_charset"],
            $this->MySQLDBConfig[$this->MySQLQueryDB]["db_debug"]
        );
    }

    /**
     * MySQL数据库连接。
     * @param mysql config value
     * @return NULL
     */
    public function MySQLConn($DBHost, $DBPort, $DBUser, $DBPwd, $DBName, $DBCharset="utf8", $DBDebug=false)
    {
        $this->MySQLDBConn = @mysqli_connect($DBHost, $DBUser, $DBPwd, $DBName, $DBPort);

        if(!$this->MySQLDBConn){
            die('Can\'t connect MySQL Database : '.mysqli_connect_error().' , Error No:'. mysqli_connect_errno());
        }
        //设置字符集。
        mysqli_set_charset($this->MySQLDBConn, $DBCharset);
    }

    /**
     * 运行Sql语句，返回插入ID或者影响的行数。
     * @param string $sql
     * @return insert_id | affect Rows
     */
    public function query($sql)
    {
        if(!$this->MySQLDBConn){
            return false;
        }
        $result = mysqli_query($this->MySQLDBConn, $sql);
        if($result) {
            $insert_id = mysqli_insert_id($this->MySQLDBConn);
            if($insert_id > 0){
                return array("insert_id" => strval($insert_id));
            }
            return mysqli_affected_rows($this->MySQLDBConn);
        }else{
            return false;
        }
    }


    /**
     * 运行Sql,以多维数组方式返回结果集
     * @param string $sql
     * @return array 成功返回数组，失败时返回空数组
     */
    public function getAll($sql)
    {
        $returnData = array();
        //如果连接已经失效，返回空数组。
        if(!$this->MySQLDBConn){
            return $returnData;
        }
        $queryResult = mysqli_query($this->MySQLDBConn, $sql);
        if(is_bool($queryResult)){
            return $returnData;
        }else{
            $returnData = $this->_FetchAll($queryResult);
        }
        mysqli_free_result($queryResult);
        return $returnData;
    }

    /**
     * 运行Sql,以数组方式返回结果集第一行记录
     * @param string $sql
     * @return array 成功返回数组，失败时返回NULL
     */
    public function getRow($sql)
    {
        $returnData = NULL;
        if(!$this->MySQLDBConn){
            return $returnData;
        }
        $queryResult = mysqli_query($this->MySQLDBConn, $sql);
        if(is_bool($queryResult)){
            return $returnData;
        }else{
            if($result = mysqli_fetch_array($queryResult, MYSQLI_ASSOC)){
                $returnData = $result;
            }
        }
        mysqli_free_result($queryResult);
        return $returnData;
    }

    /**
     * 往数据库里新增数据
     * @param string  $tableName   表名
     * @param array   $params    array('列名'=>'值')
     * @param bool    $isReturnID 默认false返回操作是否成功(true|false)，true返回新增的ID
     * @return $isReturnID=true 返回新增ID  $isReturnID=false 返回新增是否成功
     */
    public function insert($tableName, $params, $isDebug=false, $isReturnID=false, $replace=false)
    {
        
        if(!is_array($params) || $tableName == '' || count($params) == 0) {
            return false;
        }

        $filedsArr = array(); //数据库列名
        $valuesArr = array(); //要插入的值
        foreach ($params as $key => $value) {
            array_push($filedsArr, "`".$key."`");
            array_push($valuesArr, "'".$value."'");
        }

        $filedsStr = implode(',', $filedsArr);
        $valuesStr = implode(',', $valuesArr);

        $cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
        $sql = $cmd." `".$tableName."` (".$filedsStr.") VALUES (".$valuesStr.")";

        if($isDebug){ return $sql; }

        $result = $this->_QueryData($sql);

        return $isReturnID ? $this->getInsertID() : $result;
    }

    /**
     * 更新数据库的数据操作。
     * @param string    $tableName     表名
     * @param array     $params         array('列名'=》$value)
     * @param array     $conditions     array('列名'=》$value)
     * @return bool true 修改成功； false 修改失败
     */
    public function update($tableName, $params, $conditions, $isDebug=false)
    {
        // 更改的值
        $paramsArr = array();
        foreach ($params as $key=>$value) {
            if(is_null($value)){
                array_push($paramsArr, "`".$key."`=NULL");
            }else{
                array_push($paramsArr, "`".$key."`='".$value."'");
            }
        }
        // 更改条件
        $conditionsArr = array();
        foreach ($conditions as $key=>$value) {
            array_push($conditionsArr, "`".$key."`='".$value."'");
        }

        $filedsStr = implode(',', $paramsArr);
        $whereStr = implode(' and ', $conditionsArr);

        $sql = "UPDATE `".$tableName."` SET ".$filedsStr." WHERE ".$whereStr;

        return $isDebug ? $sql : $this->_QueryData($sql);
    }

    /**
     * 删除数据库里的数据操作
     * @param string    $tableName     表名
     * @param array     $conditions     array('列名'=》$value)
     * @return bool 操作是否成功
     */
    public function delete($tableName, $conditions, $isDebug=false)
    {
        // 更改条件
        $conditionsArr = array();
        foreach ($conditions as $key=>$value) {
            array_push($conditionsArr, "`".$key."`='".$value."'");
        }
        $whereStr = implode(' and ', $conditionsArr);

        $sql = "DELETE FROM `".$tableName."` WHERE ".$whereStr;

        return $isDebug ? $sql : $this->_QueryData($sql);
    }

    /**
     * 根据条件获取记录总条数的操作
     * @param string    $tableName     表名
     * @param array     $where   条件 
     * @return int Count的数目
     */
    public function count($tableName, $where='')
    {
        $where = trim($where) == "" ? "" : " WHERE ".$where;
        $sql = "SELECT count(*) AS num FROM `{$tableName}`{$where}";
        $result = $this->getRow($sql);

        return is_array($result) ? $result['num'] : 0;
    }

    /**
     * 获取受影响的行数
     * @param string $sql
     * @return int
     */
    public function queryAffectedRows()
    {
        $affectRows = 0;
        if(!$this->MySQLDBConn){
            return $affectRows;
        }
        return mysqli_affected_rows($this->MySQLDBConn);
    }

    /**
     * 获取新增的id
     * @return int 成功返回last_id,失败时返回false
     */
    public function getInsertID()
    {
        return mysqli_insert_id($this->MySQLDBConn);
    }

    /**
     * 运行Sql,返回结果集第一条记录的第一个字段值
     * @param string $sql
     * @return mixxed 成功时返回一个值，失败时返回false
     */
    public function getVar($sql)
    {
        $data = $this->getRow($sql);
        if ($data) {
            return $data[@reset(@array_keys($data))];
        } else {
            return false;
        }
    }

    /**
     * 运行Sql，以列的方式返回结果集
     * @param string $sql
     * @return mixxed 成功时返回多维数组，失败时返回空数组
     */
    public function getColumn($sql)
    {
        $returnData = array();
        if(!$this->MySQLDBConn){
            return $returnData;
        }
        if($queryResult = mysqli_query($this->MySQLDBConn, $sql)) {
            if($result = $this->_FetchAll($queryResult)) {
                foreach($result as $row) {
                    foreach($row as $name=>$value) {
                        $returnData[] = $value;
                    }
                }
            }
        }
        return $returnData;
    }

    /**
     * 过滤执行入库的数据，连同SpecialHtml一起过滤掉。
     * @param string
     * @return string
     */
    public function escapeQueryString($string)
    {
        $string = htmlspecialchars($string);
        return mysqli_real_escape_string($this->MySQLDBConn, $string);
    }


    /**
     * 过滤执行入库的数据。
     * @param string
     * @return string
     */
    public function simpleEscapeQueryString($string)
    {
        return mysqli_real_escape_string($this->MySQLDBConn, $string);
    }


    /**
     * 关闭数据库连接
     * @param NULL
     * @return NULL
     */
    public function close()
    {
        @mysqli_close($this->MySQLDBConn);
    }

    /**
     * 运行Sql,以多维数组方式返回结果集
     * @param string $sql
     * @return array 成功返回数组，失败时返回空数组
     */
    private function _FetchAll($result)
    {
        $returnData = array();
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $returnData[] = $this->_StripslashesDeep($row);
        }
        return $returnData;
    }

    /**
     * 运行Sql语句，返回执行结果，有别于$this->query，不对结果做处理。
     * @param string $sql
     * @return bool mysqli_result
     */
    private function _QueryData($sql)
    {
        if(!$this->MySQLDBConn){
            return false;
        }
        return mysqli_query($this->MySQLDBConn, $sql);
    }

    /**
     * 深度过滤多余的转义。
     * @return NULL
     */
    private function _StripslashesDeep($value)
    {
        return is_array($value) ? array_map('self::reverseEscape', $value) : self::reverseEscape($value);
    }

    /**
     * 替换多余的转义
     * @param $str
     * @return string
     **/
    private static function reverseEscape($str)
    {
        $search=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
        $replace=array("\\","\0","\n","\r","\x1a","'",'"');
        return str_replace($search,$replace,$str);
    }

    /**
     * 对字段两边加反引号，以保证数据库安全
     * @param $value 数组值
     */
    public function escapeSpecialChar(&$value) {
        if('*' == $value 
            || false !== strpos($value, '(') 
            || false !== strpos($value, '.') 
            || false !== strpos ( $value, '`')
        ) {
            //不处理包含* 或者 使用了sql方法。
        } else {
            $value = '`'.trim($value).'`';
        }
        if (preg_match("/\b(select|insert|update|delete)\b/i", $value)) {
            $value = preg_replace("/\b(select|insert|update|delete)\b/i", '', $value);
        }
        return $value;
    }

    /**
     * 对字段值两边加引号，以保证数据库安全
     * @param $value 数组值
     * @param $key 数组key
     * @param $quotation
     */
    public function escapeString(&$value, $key='', $quotation = 1) {
        if ($quotation) {
            $q = '\'';
        } else {
            $q = '';
        }
        $value = $q.$value.$q;
        return $value;
    }
}
