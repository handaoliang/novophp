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

//网站站入口文件
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."app.init.php";
//print_r($_GET);exit;
//如果用户没有登录，而且存在自动登录的Cookie，则需要自动登录。
$userSignInStatus = AppsFunc::checkUserSignIn();
if($userSignInStatus===0 && isset($_COOKIE["auth_token"]))
{
    header("location:/auto_signin".$_SERVER["REQUEST_URI"]);
}
NovoInterface::initInterface();
