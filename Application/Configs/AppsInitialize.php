<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: init.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2013/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 网站应用初始化文件
**/

//NovoPHP路径
if(!defined("NOVOPHP_CORE_DIR")){//系统所在目录，应该在command.config.php目录的上两层。
    define("NOVOPHP_CORE_DIR", dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."NovoPHP");
}

/**
 * 包含基础文件，整站调用。
 */
//整站全局配置文件
var_dump(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Configs".DIRECTORY_SEPARATOR.ucfirst(ENVIRONMENT).DIRECTORY_SEPARATOR."AppsConfig.php");
var_dump(file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Configs".DIRECTORY_SEPARATOR.ucfirst(ENVIRONMENT).DIRECTORY_SEPARATOR."AppsConfig.php"));exit;
if(file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Configs".DIRECTORY_SEPARATOR.ucfirst(ENVIRONMENT).DIRECTORY_SEPARATOR."AppsConfig.php")){
    require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Configs".DIRECTORY_SEPARATOR.ucfirst(ENVIRONMENT).DIRECTORY_SEPARATOR."AppsConfig.php";
}else{
    die("Apps config file not found! please check your configuration.");
}

// 整站入口文件
require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."NovoPHP".DIRECTORY_SEPARATOR."Configs".DIRECTORY_SEPARATOR."NovoInitialize.php";

/* 应用本身公共函数库 */
require_once APPS_LIBS_DIR.DIRECTORY_SEPARATOR."AppsCommon.func.php";

/* URI操作相关的类 */
require_once APPS_LIBS_DIR.DIRECTORY_SEPARATOR."NovoURI.class.php";

/* 应用的基础类本身 */
require_once APPS_LIBS_DIR.DIRECTORY_SEPARATOR."AppsBaseController.class.php";
