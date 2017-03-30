<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @Project             www.novophp.com
 * @file                $RCSfile: ErrorController.php,v $
 * @version             $Revision: 1.0 $
 * @modifiedby          $Author: handaoliang $
 * @copyright           Copyright (c) 2013, Comnovo Inc.
**/
class ErrorController extends AppsController {

    protected $ActionsMap = array(
        "index" =>"doIndex",
        "404"   =>"do404Error",
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function doIndex(){
        header("location:/");
    }
    public function do404Error(){
        $this->smarty->display("Error/404ErrorView.tpl");
    }

}
