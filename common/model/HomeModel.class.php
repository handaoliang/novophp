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

    public function __construct()
    {
        $this->MySQLDBConfig = NovoLoader::loadAppsConfig('mysql');
        $this->MySQLDBSetting = "master";
        parent::__construct();

        $memcacheConfig = NovoLoader::loadAppsConfig('memcache');
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
        return "www.novophp.com";
    }
}
