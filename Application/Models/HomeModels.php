<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @Project             www.comnovo.com
 * @file                $RCSfile: HomeModels.php,v $
 * @version             $Revision: 1.0 $
 * @modifiedby          $Author: handaoliang $
 * @copyright           Copyright (c) 2013, Comnovo Inc.
**/
/**
 * - getHomeData()
 * 
 **/
class HomeModels extends BaseMySQLiData{

    public function __construct()
    {
        $this->MySQLDBConfig = BaseInitialize::loadAppsConfig('mysql');
        $this->MySQLDBSetting = "master";
        parent::__construct();

        $memcacheConfig = BaseInitialize::loadAppsConfig('memcache');
        if(count($memcacheConfig) == 0
            || !isset($memcacheConfig["memcache_namespace"])
            || !isset($memcacheConfig["memcache_server"])
        ) {
            die("Memcache Config files Error...Please Check...");
        }
        $memcacheServer = $memcacheConfig["memcache_server"];
        $this->memcacheObj = new BaseMemcached($memcacheServer, $memcacheConfig["memcache_namespace"]);
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
