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
 * 整站初始化文件
**/
/* 开启SESSION */
session_start();

/* error reporting */
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

/* 关闭set_magic_quotes_runtime */
@set_magic_quotes_runtime(0);

/* 调整时区 */
if (version_compare(PHP_VERSION, '5.1.0', '>='))
{
    date_default_timezone_set('Asia/Shanghai');
}

//NovoPHP路径
if(!defined("NOVOPHP_CORE_DIR")){//系统所在目录，应该在command.config.php目录的上两层。
    define("NOVOPHP_CORE_DIR", dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."NovoPHP");
}

/**
 * 包含基础文件，整站调用。
 */
//整站全局配置文件
if(file_exists(NOVOPHP_CORE_DIR.DIRECTORY_SEPARATOR."Configs".DIRECTORY_SEPARATOR."CommonConfig.php"))
{
    require_once NOVOPHP_CORE_DIR.DIRECTORY_SEPARATOR."Configs".DIRECTORY_SEPARATOR."CommonConfig.php";
}else{
    die("Common config file not found! please check your configuration.");
}

/* 公共函数库 */
require_once NOVOPHP_LIBS_DIR.DIRECTORY_SEPARATOR."Common.func.php";
/* 公共辅助函数库 */
require_once NOVOPHP_LIBS_DIR.DIRECTORY_SEPARATOR."Helper.func.php";
/* 基础加载类库 */
require_once NOVOPHP_LIBS_DIR.DIRECTORY_SEPARATOR."BaseInitialize.class.php";
/* 基础InterFace类 */
require_once NOVOPHP_LIBS_DIR.DIRECTORY_SEPARATOR."BaseInterface.class.php";
/* 基础Controller类 */
require_once NOVOPHP_LIBS_DIR.DIRECTORY_SEPARATOR."BaseController.class.php";
/* 基础数据库操作类 */
require_once NOVOPHP_LIBS_DIR.DIRECTORY_SEPARATOR."BaseMySQLiData.class.php";
/* 基础字符串加密类 */
require_once NOVOPHP_LIBS_DIR.DIRECTORY_SEPARATOR."BaseStringEncrypt.class.php";
/* 基础分页类 */
require_once NOVOPHP_LIBS_DIR.DIRECTORY_SEPARATOR."BasePage.class.php";
//Memcached操作类
require_once NOVOPHP_LIBS_DIR.DIRECTORY_SEPARATOR."BaseMemcached.class.php";
/* 基础上传类 */
require_once NOVOPHP_LIBS_DIR.DIRECTORY_SEPARATOR."BaseUploader.class.php";
