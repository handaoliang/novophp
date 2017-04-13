<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: suto_signin.do,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2012/04/15 17:02:11 $
 * @copyright            Copyright (c) 2012, Comnovo Inc.
**/

//网站站入口文件
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."app.init.php";

$_GET["c"] = "users";
$_GET["m"] = "auto_sign_in";
$_GET["t"] = "json";

NovoInterface::initInterface();
