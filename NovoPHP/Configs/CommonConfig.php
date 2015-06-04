<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: CommonConfig.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2012/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 全局配置文件
**/
//系统字符集配置-------------------------------------------------
define("SYSTEM_CHARSET","UTF-8");//系统字符集，可以为UTF8,GBK等。
define("SYSTEM_LANG","zh_CN");//系统语言，默认为zh_CN。

//NovoPHP路径
if(!defined("NOVOPHP_CORE_DIR")){//系统所在目录，应该在command.config.php目录的上两层。
    define("NOVOPHP_CORE_DIR", dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."NovoPHP");
}

//基本模板块所在的路径，底层模块所在路径
define("NOVOPHP_LIBS_DIR", NOVOPHP_CORE_DIR.DIRECTORY_SEPARATOR."Libs");//全局的func及class文件的路径。
define("NOVOPHP_VENDORS_DIR", NOVOPHP_CORE_DIR.DIRECTORY_SEPARATOR."Vendors");//第三方开源的模块以及functions等。
define("NOVOPHP_CONFIGS_DIR", NOVOPHP_CORE_DIR.DIRECTORY_SEPARATOR."Configs");//配置文件文件夹。
