<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: NovoEmailServerJobs.class.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2012/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 邮件服务器基础文件，调用的亚马逊SDK来进行邮件发送。
**/
if(!defined('NOVOPHP_VENDOR_DIR')){
	echo "NOVOPHP_VENDOR_DIR is not defined, BaseServerJobs.Class";
	exit;
}

require_once NOVOPHP_VENDOR_DIR.'/AmazonSDK/sdk.class.php';

abstract class NovoEmailServerJobs{

    protected $emailConnetHandler = NULL;
    protected $connect_count = array(
        "apn_conn"       =>0,
        "email_conn"     =>0,
    );
    protected $smarty = NULL;

    public function AmazonSMTPConnect()
    {
        print "Initialization Amazon SES Service\r\n";
        $this->emailConnetHandler = new AmazonSES();
    }

    public function getMailBody($templateDir, $mailArguments, $templateName)
    {
        $this->smarty = new NovoSmarty;
        $this->smarty->setNewTemplateDir($templateDir);
        foreach($mailArguments as $key=>$value){
            $this->smarty->assign($key, $value);
        }
        $mailBody = $this->smarty->fetch($templateName.".tpl");   
        return $mailBody;
    }
}
