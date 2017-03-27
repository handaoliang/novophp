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

	public $uri_string = '';


    public static function initInterface()
    {
		//phpinfo();exit;
		$uri = self::_parseRequestURI();

		if(strpos($uri, ".") !== false){
			list($uri, $requestDataType) = explode(".", $uri, 2);
            trimURIString($requestDataType);
        }else{
            $requestDataType = "html";
        }

		$segments = array();
		foreach (explode('/', trim($uri, '/')) as $val)
		{
			if ($val !== '')
			{
				array_push($segments, $val);
			}
		}
        array_walk($segments, "trimURIString");

        $tempArr = array();
        for($i=0; $i<2; $i++){
            array_push($tempArr, NULL);
        }
        $segments = $segments+$tempArr;

        $segmentsArray = array_slice($segments, 2);

        list($userController, $userAction) = $segments;

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
