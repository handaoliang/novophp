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
