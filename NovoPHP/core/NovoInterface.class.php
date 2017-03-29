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
class NovoInterface {

    public static function initInterface()
    {
        $uri = self::_parseRequestURI();

        $segments = array();
        $segmentsArray = array();

        $userController = !empty(CommonFunc::exGet("controller")) ? $_GET["controller"] : CommonFunc::exGet("c");
        $userAction = !empty(CommonFunc::exGet("action")) ? $_GET["action"] : CommonFunc::exGet("m");
        $requestDataType = !empty(CommonFunc::exGet("request_data_type")) ? $_GET["request_data_type"] : CommonFunc::exGet("t");

        CommonFunc::trimURIString($userController);
        CommonFunc::trimURIString($userAction);
        CommonFunc::trimURIString($requestDataType);

        //如果不是以参数的形式传Controller和Actions，则解析URI
        if(empty($userController) || empty($userAction)){
            if(strpos($uri, ".") !== false){
                list($uri, $requestDataType) = explode(".", $uri, 2);
                CommonFunc::trimURIString($requestDataType);
            }else{
                $requestDataType = "html";
            }

            foreach (explode('/', trim($uri, '/')) as $val)
            {
                if ($val !== '')
                {
                    array_push($segments, $val);
                }
            }
            array_walk($segments, "CommonFunc::trimURIString");

            //生成一个全部值为Null的数组，2个值。
            $tempArr = array();
            for($i=0; $i<2; $i++){
                array_push($tempArr, NULL);
            }
            //将两个数组相加，如果$segments为空数组或者只有一个元素，则一样会得到一个2个值的数组，方便后面List用。
            $segments = $segments+$tempArr;

            //将参数取出来，其实是匹配类似于这样的URL：/controller/actions/args_a/args_b
            //这样的话，就可以将args_a和args_b传给Actions方法以进行操作。
            $segmentsArray = array_slice($segments, 2);

            list($userController, $userAction) = $segments;
        }

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

        $classFile = APPS_CONTROLLER_DIR."/".$callClassName.".php";
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
        $controllerObj->setParams($segmentsArray);
        $controllerObj->dispatchAction();
    }

    protected static function _parseRequestURI()
    {
        if ( ! isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']))
        {
            return '';
        }

        $uri = parse_url('http://dummy'.$_SERVER['REQUEST_URI']);
        $query = isset($uri['query']) ? $uri['query'] : '';
        $uri = isset($uri['path']) ? $uri['path'] : '';

        if (isset($_SERVER['SCRIPT_NAME'][0]))
        {
            if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
            {
                $uri = (string) substr($uri, strlen($_SERVER['SCRIPT_NAME']));
            }
            elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
            {
                $uri = (string) substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            }
        }

        if (trim($uri, '/') === '' && strncmp($query, '/', 1) === 0)
        {
            $query = explode('?', $query, 2);
            $uri = $query[0];
            $_SERVER['QUERY_STRING'] = isset($query[1]) ? $query[1] : '';
        }
        else
        {
            $_SERVER['QUERY_STRING'] = $query;
        }

        parse_str($_SERVER['QUERY_STRING'], $_GET);

        if ($uri === '/' OR $uri === '')
        {
            return '/';
        }

        return self::_removeRelativeDirectory($uri);
    }

    protected static function _removeRelativeDirectory($uri)
    {
        $uris = array();
        $tok = strtok($uri, '/');
        while ($tok !== FALSE)
        {
            if (( ! empty($tok) OR $tok === '0') && $tok !== '..')
            {
                $uris[] = $tok;
            }
            $tok = strtok('/');
        }

        $uri_temp_str = implode('/', $uris);
        return trim(self::_removeInvisibleCharacters($uri_temp_str, FALSE), '/');
    }

    protected static function _removeInvisibleCharacters($str, $url_encoded = TRUE)
    {
        $non_displayables = array();

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($url_encoded)
        {
            $non_displayables[] = '/%0[0-8bcef]/i'; // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/i';  // url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';   // 00-08, 11, 12, 14-31, 127

        do
        {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return urldecode($str);
    }

}
