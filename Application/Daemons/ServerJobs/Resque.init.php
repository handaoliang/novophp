<?php
date_default_timezone_set('GMT');

//NovoPHP路径
if(!defined("NOVOPHP_CORE_DIR")){//系统所在目录，应该在command.config.php目录的上两层。
    define("NOVOPHP_CORE_DIR", dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR."NovoPHP");
}

require_once NOVOPHP_CORE_DIR."/Configs/init.php";
require_once NOVOPHP_LIBS_DIR."/BaseEmailServerJobs.Class.php";

/* 所有独立的队列方法都要包含进来 */
require_once dirname(__FILE__).'/WebAccountsActiveEmail_Job.php';
require_once dirname(__FILE__).'/ResetPasswordEmail_Job.php';

require_once NOVOPHP_VENDORS_DIR.'/PHPResque/resque.php';
