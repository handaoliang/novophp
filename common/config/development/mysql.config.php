<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @Package
 * @File Name            $RCSfile: MysqlConfig.php,v $
 * @Version              $Revision: 1.0 $
 * @Modified By          $Author: handaoliang $
 * @Last Modified        $Date: 2016-12-16 21:12:11 $
 * @Copy Right           Copyright (c) 2013, Comnovo Inc.
**/
/**
 * MySQL集群配置文件。
**/
return array (
    "master" => array (
        "db_host"         =>"localhost",
        "db_port"         =>3306,
        "db_user"         =>"root",
        "db_password"     =>"",
        "db_name"         =>"novophp_com",
        "db_table_pre"    =>"novophp_",
        "db_charset"      =>"utf8",
        "db_type"         =>"mysql",
        "db_debug"        =>true,
    ),

    "slave" => array (
        "db_host"         =>"localhost",
        "db_port"         =>3306,
        "db_user"         =>"root",
        "db_password"     =>"",
        "db_name"         =>"novophp_com",
        "db_table_pre"    =>"novophp_",
        "db_charset"      =>"utf8",
        "db_type"         =>"mysql",
        "db_debug"        =>true,
    ),
);
