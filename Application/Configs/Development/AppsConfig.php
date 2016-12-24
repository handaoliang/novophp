<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: WebConfig.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2012/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 应用的全局配置文件
**/

//系统的WEB路径配置---------------------------------------------------------
define("WEB_ROOT_PATH",  "http://www.novophp.com");//网页地址
define("RES_ROOT_PATH",  "http://www.novophp.com/statics/");//网站图片及CSS等资源的路径。
define("FILES_PATH",     "http://file.novophp.com");//文件资源的WEB路径。
define("IMG_FILES_PATH", "http://img.novophp.com");//图片文件资源的WEB路径。

//Application路径。
if(!defined("APPS_BASE_DIR")){
    define("APPS_BASE_DIR", dirname(dirname(dirname(__FILE__))));
}

define("APPS_CONFIGS_DIR",     dirname(__FILE__));//各应用的Config文件的路径。
define("APPS_LIBS_DIR",        APPS_BASE_DIR.DIRECTORY_SEPARATOR."Libs");//各应用的func及class文件的路径。
define("APPS_HELPERS_DIR",     APPS_BASE_DIR.DIRECTORY_SEPARATOR."Helpers");//辅助类目录。
define("APPS_MODELS_DIR",      APPS_BASE_DIR.DIRECTORY_SEPARATOR."Models");//和数据库打交道的Models文件夹。
define("APPS_VIEWS_DIR",       APPS_BASE_DIR.DIRECTORY_SEPARATOR."Views");//模版文件夹。
define("APPS_CONTROLLERS_DIR", APPS_BASE_DIR.DIRECTORY_SEPARATOR."Controllers");//MVC的C层控制文件目录
define("APPS_WEB_ROOT_DIR",    APPS_BASE_DIR.DIRECTORY_SEPARATOR."WebRoot");//网站可访问根目录。

//Smarty缓存配置
define("SMARTY_ALLOW_CACHE",  true);//是否开启Smarty的缓存。
define("SMARTY_CACHING_TYPE", "file");//Smarty的缓存方式，file|memcache
define("SMARTY_CACHE_TIME",   24*60*60);//Smarty缓存文件存活时间。默认为24小时。
define("SMARTY_DEBUGGING",    false);//是否开启Smarty调试模式

//如果是文件缓存，需要配置这个。
define("APPS_CACHE_DIR",     APPS_BASE_DIR.DIRECTORY_SEPARATOR."Cache");//缓存地址。
define("SMARTY_CACHE_DIR",   APPS_CACHE_DIR.DIRECTORY_SEPARATOR."smarty_cache");//Smarty的cache目录。
define("SMARTY_TEMPLATES_C", APPS_CACHE_DIR.DIRECTORY_SEPARATOR."templates_c");//Smarty的编译目录。

//如果是Redis缓存，需要配置这个。
define("SMARTY_CACHE_MEMCACHE_ADDRESS", "127.0.0.1");
define("SMARTY_CACHE_MEMCACHE_PORT",    "11211");

//Cookies && SESSION Configure
define("COOKIES_DOMAIN", ".novophp.com");

//加密Key
define("ENCRYPT_PUB_KEY",        "5d860908f76d01371825e64b126310ac");
define("ENCRYPT_PUB_KEY_BAK",    "73498390d2cee24d88b46ceb39b11856");
define("USER_PASSWORD_SALT",     "b4a203920f42cef8c4bf660874e66a55");
define("USER_COOKIE_TOKEN_SALT", "6b9b6a26edc8c0da7fd0a50ce7453921");

//PHPResque 配置
define("RESQUE_SERVER_REDIS", "127.0.0.1:6379");

//Service Email-Address Config
define("SERVICE_EMAIL",  'service@novophp.com');
define("FEEDBACK_EMAIL", 'feedback@novophp.com');

//upload images config
define("IMAGES_FILE_MAX_SIZE", 10 * 1024 * 1024);
define("IMAGES_ALLOW_FILE_TYPE", "jpg,jpeg,gif,png");
