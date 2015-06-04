<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: UserHelper.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2012/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 用户相关的辅助文件
**/
class UsersHelper extends BaseController
{
    public function sendAccountsActiveEmail($args)
    {
        require NOVOPHP_VENDORS_DIR.'/PHPResque/lib/Resque.php';
        date_default_timezone_set('GMT');
        Resque::setBackend(RESQUE_SERVER_REDIS);
        $jobId = Resque::enqueue("email", "WebAccountsActiveEmail_Job", $args, true);
        return $jobId;
    }

    public function sendResetPasswordEmail($args)
    {
        require NOVOPHP_VENDORS_DIR.'/PHPResque/lib/Resque.php';
        date_default_timezone_set('GMT');
        Resque::setBackend(RESQUE_SERVER_REDIS);
        $jobId = Resque::enqueue("email", "ResetPasswordEmail_Job", $args, true);
        return $jobId;
    }

}

