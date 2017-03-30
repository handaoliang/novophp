<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: app.config.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2012/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 应用的全局配置文件
**/

//系统的WEB路径配置---------------------------------------------------------
define("WEB_ROOT_PATH",  "http://www.novophp.com");              //网页地址
define("RES_ROOT_PATH",  "http://www.novophp.com/statics");      //网站图片及CSS等资源的路径。
define("FILES_PATH",     "http://www.novophp.com/upload_files"); //文件资源的WEB路径。

//主题文件所在路径，为了可更换主题。
define("APPS_THEME_DIR", "default");

//Smarty缓存配置
define("SMARTY_ALLOW_CACHE",  true);                                             //是否开启Smarty的缓存。
define("SMARTY_CACHING_TYPE", "file");                                           //Smarty的缓存方式，file|memcache
define("SMARTY_CACHE_TIME",   24*60*60);                                         //Smarty缓存文件存活时间。默认为24小时。
define("SMARTY_DEBUGGING",    false);                                            //是否开启Smarty调试模式
define("SMARTY_TEMPLATES_DIR", APPS_VIEW_DIR.DIRECTORY_SEPARATOR.APPS_THEME_DIR); //Smarty模板所在路径。

//如果是Smarty缓存，需要配置这个。
define("SMARTY_CACHE_MEMCACHE_ADDRESS", "127.0.0.1");
define("SMARTY_CACHE_MEMCACHE_PORT",    "11211");

//Cookies && SESSION Configure
define("COOKIES_DOMAIN", ".novophp.com");
