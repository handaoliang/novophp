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
 * 框架初始化文件
**/
define('ENVIRONMENT', isset($_SERVER['NOVO_RUNNING_ENV']) ? $_SERVER['NOVO_RUNNING_ENV'] : 'production');

header("X-Powered-By:NovoPHP");

switch (ENVIRONMENT)
{
    //开发环境
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;
    //测试环境
    case 'testing':
        error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
        ini_set('display_errors', 1);
        break;
    //仿真环境
    case 'emulation':
        error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
        ini_set('display_errors', 1);
        break;
    //生产环境
    case 'production':
        error_reporting(0);
        ini_set('display_errors', 0);
        break;
    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

//NovoPHP路径
if(!defined("NOVOPHP_DIR")){//系统所在目录，应该在command.config.php目录的上两层。
    define("NOVOPHP_DIR", dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."NovoPHP");
}
define("NOVOPHP_CORE_DIR", NOVOPHP_DIR.DIRECTORY_SEPARATOR."core");//全局的func及class文件的路径。
define("NOVOPHP_LIB_DIR", NOVOPHP_DIR.DIRECTORY_SEPARATOR."lib");//全局的func及class文件的路径。
define("NOVOPHP_VENDOR_DIR", NOVOPHP_DIR.DIRECTORY_SEPARATOR."vendor");//第三方开源的模块以及functions等。

//Common文件夹路径
if(!defined("COMMON_DIR")){//系统所在目录，应该在command.config.php目录的上两层。
    define("COMMON_DIR", dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."common");
}

define("COMMON_CONFIG_DIR",     COMMON_DIR.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR.ENVIRONMENT);     //各应用的Config文件的路径。
define("COMMON_LIB_DIR",        COMMON_DIR.DIRECTORY_SEPARATOR."lib");        //各应用的func及class文件的路径。
define("COMMON_API_DIR",        COMMON_DIR.DIRECTORY_SEPARATOR."api");        //各应用的func及class文件的路径。
define("COMMON_HELPER_DIR",     COMMON_DIR.DIRECTORY_SEPARATOR."helper");     //辅助类目录。
define("COMMON_MODEL_DIR",      COMMON_DIR.DIRECTORY_SEPARATOR."model");      //和数据库打交道的Models文件夹。

$_AUTO_LOAD_DIR = array(
    NOVOPHP_CORE_DIR,
    NOVOPHP_LIB_DIR,
    COMMON_LIB_DIR,
    COMMON_API_DIR,
    COMMON_MODEL_DIR,
    APPS_LIB_DIR,
);

/* 开启SESSION */
session_start();

if(version_compare(PHP_VERSION, '5.3.0', '<')){
	/* 关闭set_magic_quotes_runtime */
	@set_magic_quotes_runtime(0);
}

/* 调整时区 */
if (version_compare(PHP_VERSION, '5.1.0', '>='))
{
    date_default_timezone_set('Asia/Shanghai');
}

/**
 * 包含基础文件，整站调用。
 */
//整站全局配置文件
if(file_exists(COMMON_CONFIG_DIR.DIRECTORY_SEPARATOR."common.config.php"))
{
    require_once COMMON_CONFIG_DIR.DIRECTORY_SEPARATOR."common.config.php";
}else{
    die("Common config file not found! please check your configuration.");
}



/**
 * @abstract: 自动加载class文件
 */
function NovoAutoload($ClassName)
{
    global $_AUTO_LOAD_DIR;
    foreach($_AUTO_LOAD_DIR as $_VAL){
        $_AUTO_LOAD_FILE_NAME = $_VAL.DIRECTORY_SEPARATOR.$ClassName.'.class.php';
        //echo $ClassName."-------".$_AUTO_LOAD_FILE_NAME."<br />";
        if(file_exists($_AUTO_LOAD_FILE_NAME)){
            require_once $_AUTO_LOAD_FILE_NAME;
            return true;
        }
    }
}

spl_autoload_register("NovoAutoload");
