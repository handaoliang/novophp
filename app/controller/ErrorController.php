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

    public function __construct()
    {
        parent::__construct();
    }

    public function do_index(){
        header("location:/");
    }
    public function do_404(){
        $this->smarty->display("error/404.tpl");
    }

}
