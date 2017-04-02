<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @Project             NovoPHP Project
 * @file                $RCSfile: HomeModel.class.php,v $
 * @version             $Revision: 1.0 $
 * @modifiedby          $Author: handaoliang $
 * @copyright           Copyright (c) 2013, Comnovo Inc.
**/
/**
 * - getHomeData()
 * 
 **/
class HomeModel extends NovoMySQLiData
{
    protected $AppsDBVolumes = "common_db";
    protected $AppsQueryDB = "master";

    public function __construct()
    {
        $this->MySQLDBConfig = NovoLoader::loadConfig('mysql', $this->AppsDBVolumes);
        $this->DBTablePre = $this->MySQLDBConfig["db_table_pre"];
        $this->MySQLQueryDB = $this->AppsQueryDB;

        parent::__construct();

        $memcacheConfig = NovoLoader::loadConfig('memcache');
        if(count($memcacheConfig) == 0
            || !isset($memcacheConfig["memcache_namespace"])
            || !isset($memcacheConfig["memcache_server"])
        ) {
            die("Memcache Config files Error...Please Check...");
        }
        $this->memcacheObj = new NovoMemcached($memcacheConfig["memcache_server"], $memcacheConfig["memcache_namespace"]);
        if ($this->memcacheObj->checkStatus())
        {   
            $this->memcacheObj->setDataVersion("home");
        }  
    }

    public function  getHomeData()
    {
        $dbName = $this->DBTablePre."options";
        $sql = "SELECT * FROM {$dbName} limit 10";
        return $this->getAll($sql);
    }
}
