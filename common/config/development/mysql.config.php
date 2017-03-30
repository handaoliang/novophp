<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @Package
 * @File Name            $RCSfile: mysql.config.php,v $
 * @Version              $Revision: 1.0 $
 * @Modified By          $Author: handaoliang $
 * @Last Modified        $Date: 2016-12-16 21:12:11 $
 * @Copy Right           Copyright (c) 2013, Comnovo Inc.
**/
/**
 * MySQL集群配置文件。
**/
return array (
    "common_db" => array(
        "db_table_pre"    =>"novophp_",

        "master" => array (
            "db_host"         =>"127.0.0.1",
            "db_port"         =>3306,
            "db_user"         =>"root",
            "db_password"     =>"",
            "db_name"         =>"novophp_com",
            "db_charset"      =>"utf8mb4",
            "db_debug"        =>true,
        ),

        "slave" => array (
            "db_host"         =>"127.0.0.1",
            "db_port"         =>3306,
            "db_user"         =>"root",
            "db_password"     =>"",
            "db_name"         =>"novophp_com",
            "db_charset"      =>"utf8mb4",
            "db_debug"        =>true,
        ),
    ),
);
