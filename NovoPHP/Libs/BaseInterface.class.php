<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: BaseInterface.class.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2013/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 全站入口文件，在面向前端的程序中调用。
**/
class BaseInterface {

    public static function initInterface()
    {
        $userController = exGet("controller");
        $userAction = exGet("action");
        $requestDataType = exGet("request_data_type");

        $userController = !empty($userController) ? $userController : "home";
        $userAction = !empty($userAction) ? $userAction : "index";

        //允许使用类似user_profile，这样的命名空间，这个命名空间将被转化成UserProfile。
        //转换“_”符号到本框架类名的命名空间。
        $tempClassNameArr = explode("_", $userController);
        $tempClassNameStr = "";
        foreach($tempClassNameArr as $value){
            $tempClassNameStr .= ucfirst(trim($value));
        }
        $callClassName = $tempClassNameStr."Controller";
        unset($tempClassNameArr);
        unset($tempClassNameStr);

        $classFile = APPS_CONTROLLERS_DIR."/".$callClassName.".php";
        if (!is_file($classFile)) {
            header("location:/error/404.html");
            exit;
        }

        //将类文件包含进来。
        require_once $classFile;

        //初始化对象。并且调用ActionsController.class.php里的dispatchAction去调配Actions。
        $controllerObj = new $callClassName();
        $controllerObj->setControllerName($userController);
        $controllerObj->setActionsName($userAction);
        $controllerObj->setClassName($callClassName);
        $controllerObj->setRequestDataType($requestDataType);
        $controllerObj->dispatchAction();
    }
}
