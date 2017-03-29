<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: AppsFunc.class.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2016/11/14 11:20:18 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 应用的通用函数方法集合。
**/
class AppsFunc {
    /**
     * Check if user had sign in.
     * @Param NULL
     * @Return UserID Or number Zero.
     */

    public static function checkUserSignIn()
    {
        if(isset($_SESSION["user_id"])){
            return $_SESSION["user_id"];
        }else{
            return 0;
        }
    }
}

