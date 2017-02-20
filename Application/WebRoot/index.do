<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: index.do,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2012/04/15 17:02:11 $
 * @copyright            Copyright (c) 2012, Comnovo Inc.
**/
/**
 * 整站入口文件。当然，以后可能还会涉及到其它入口，那么这个需要扩展。
 * 扩展的话，将此文件提出来，做为一个单独的类供其它入口方法使用
 *
 * 注意：所有此文件夹下面的文件，均必须为.do结尾的文件，否则不能执行。
 * 对于整个站点而言，可供外界访问到的PHP文件，都必须是.do后缀的。
 *
 **/
define('ENVIRONMENT', isset($_SERVER['NOVO_RUNNING_ENV']) ? $_SERVER['NOVO_RUNNING_ENV'] : 'production');

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

//网站站入口文件
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Configs".DIRECTORY_SEPARATOR."AppsInitialize.php";
//print_r($_GET);exit;
//如果用户没有登录，而且存在自动登录的Cookie，则需要自动登录。
$userSignInStatus = checkUserSignIn();
if($userSignInStatus===0 && isset($_COOKIE["auth_token"]))
{
    header("location:/auto_signin".$_SERVER["REQUEST_URI"]);
}
BaseInterface::initInterface();
