<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: RedisConfig.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2013/11/30 20:51:09 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * MySQL集群配置文件。
**/
return array (
    "common_db" => array(
        "master" => array (
            "db_host"         =>"127.0.0.1",
            "db_port"         =>6739,
            "db_name"         =>"www_novophp_com",
            "db_passsword"    =>"",
            "db_timeout"      =>0,
        ),

        "slave" => array (
            "db_host"         =>"127.0.0.1",
            "db_port"         =>6739,
            "db_name"         =>"www_novophp_com",
            "db_passsword"    =>"",
            "db_timeout"      =>0,
        ),
    ),
);
