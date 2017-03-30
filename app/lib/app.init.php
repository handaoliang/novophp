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
if(!defined("NOVOPHP_DIR")){//系统所在目录，应该在command.config.php目录的上两层。
    define("NOVOPHP_DIR", dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."NovoPHP");
}

//Application路径。
if(!defined("APPS_BASE_DIR")){
    define("APPS_BASE_DIR", dirname(dirname(__FILE__)));
}

define("APPS_LIB_DIR",        APPS_BASE_DIR.DIRECTORY_SEPARATOR."lib");        //各应用的func及class文件的路径。
define("APPS_VIEW_DIR",       APPS_BASE_DIR.DIRECTORY_SEPARATOR."view");       //模版文件夹。
define("APPS_CONTROLLER_DIR", APPS_BASE_DIR.DIRECTORY_SEPARATOR."controller"); //MVC的C层控制文件目录
define("APPS_WEB_ROOT_DIR",    APPS_BASE_DIR.DIRECTORY_SEPARATOR."webroot");    //网站可访问根目录。

//如果是文件缓存，需要配置这个。
define("APPS_CACHE_DIR",     APPS_BASE_DIR.DIRECTORY_SEPARATOR."cache");         //缓存地址。
define("SMARTY_CACHE_DIR",   APPS_CACHE_DIR.DIRECTORY_SEPARATOR."smarty_cache"); //Smarty的cache目录。
define("SMARTY_TEMPLATES_C", APPS_CACHE_DIR.DIRECTORY_SEPARATOR."templates_c");  //Smarty的编译目录。

// 框架入口文件
require_once NOVOPHP_DIR.DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."NovoInitialize.php";

/**
 * 包含基础文件，整站调用。
 */
//APP全局配置文件，这个地方是要修改的。
if(file_exists(COMMON_CONFIG_DIR.DIRECTORY_SEPARATOR."web.config.php")){
    require_once COMMON_CONFIG_DIR.DIRECTORY_SEPARATOR."web.config.php";
}else{
    die("Apps config file not found! please check your configuration.");
}
