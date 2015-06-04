<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: Admin.common.func.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2013/11/30 18:20:18 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 各应用的通用方法集合。
 * 当前为后台管理平台的通用方法集合。
**/

/**
 * Check if user had sign in.
 * @Param NULL
 * @Return UserID Or number Zero.
 */
function checkUserSignIn()
{
    if(isset($_SESSION["user_id"])){
        return $_SESSION["user_id"];
    }else{
        return 0;
    }
}

