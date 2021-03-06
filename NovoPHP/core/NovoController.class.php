<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: BaseController.class.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2012/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 基础控制文件，所有的Controller文件都要继承这个文件。默认支持Smarty模板引擎。
**/
abstract class NovoController 
{
    protected $actionsName;
    protected $controllerName;
    protected $className;
    protected $smarty;
    protected $requestDataType;
    protected $paramsArray;

    public function __construct()
    {
        //init smarty template object.
        $this->smarty = new NovoSmarty;
    }

    public function setClassName($className)
    {
        $this->className = $className;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    public function getControllerName()
    {
        return $this->controllerName;
    }

    public function setActionsName($actionsName)
    {
        $this->actionsName = $actionsName;
    }

    public function getActionsName()
    {
        return $this->actionsName;
    }

    public function setRequestDataType($requestDataType)
    {
        $this->requestDataType = $requestDataType;
    }

    public function getRequestDataType()
    {
        return $this->requestDataType;
    }

    public function setParams($paramsArr)
    {
        $this->paramsArray = $paramsArr;
    }

    public function getModelByName($name)
    {
        $modelClassName = ucfirst($name)."Model";
        $modelFile = COMMON_MODEL_DIR."/".$modelClassName.".class.php";
        require_once $modelFile;
        return new $modelClassName();
    }

    public function dispatchAction()
    {
        //执行模板呈现之前，将Controller和Actions的名字先预设上，模板可能要调用。
        $this->smarty->assign("controller", $this->controllerName);
        $this->smarty->assign("actions", $this->actionsName);

        $actionMethod = "do_".strtolower($this->actionsName);

        //检查是否存在此方法，如果不存在，显示出错页面。
        if(method_exists($this, $actionMethod)){
            call_user_func_array(array($this, $actionMethod), $this->paramsArray);
        }else{
            //@todo: need werite a log..
            $this->smarty->assign("error_msg", "对不起，您所访问的页面没有找到");
            $this->smarty->display("error/error.tpl");
            exit;
        }

    }
}
