<?php

class AppsController extends NovoController {

    public $jsonData  = array(
        'error'     =>0,
        'msg'       =>'',
        'data'      =>array(),
    );

    public function __construct() {

        parent::__construct();

        $this->smarty->assign("_G", $GLOBALS);
        $this->smarty->assign("_C", $this->getNamespace()[1]);
        $this->smarty->assign("uri", $_SERVER['REQUEST_URI']);

        //load PrependCss
        $this->loadPrependCss();

        //assign common VAR.
        $this->smarty->assign("charset", SYSTEM_CHARSET);
        $this->smarty->assign("lang",    SYSTEM_LANG);
        $this->smarty->assign("web",     WEB_ROOT_PATH);
        $this->smarty->assign("res",     RES_ROOT_PATH);
        $this->smarty->assign("files",   FILES_PATH);

    }

    /**
     * 获得当前命名空间名称和控制器名
     * @return list($nameSpace, $controller)
     */
    protected function getNamespace()
    {
        $className  = explode('\\', get_class($this))?:[];
        $controller = array_pop($className);
        return [implode('\\', $className), $controller];
    }

    /**
     * 预先判断加载的CSS样式
     * user.css
     */
    protected function loadPrependCss()
    {
        //User namespace
        list($nameSpace, $controller) = $this->getNamespace();
        if (strtolower($nameSpace)==='user') {
            $this->loadCss('css/user.css');
            $this->loadJs([
                'js/libs/jquery_easing.js',
                'js/app/cart.js',
            ]);
        }
        //login signup 
        $controllers = ['signupcontroller', 'logincontroller'];
        if (in_array(strtolower($controller), $controllers)) {
            $this->loadCss('css/user.css');
        }
    }

    /**
     * 页面需要加载的特定CSS样式
     * @param  string $styleSheet css/user.css
     * @return 
     */
    protected function loadCss($styleSheet)
    {
        if (is_array($styleSheet)) {
            $this->pageStyles = array_merge($this->pageStyles, $styleSheet);
        } else {
            $this->pageStyles[] = $styleSheet;
        }
        $this->pageStyles = array_filter($this->pageStyles);
        $this->smarty->assign("pageStyles", $this->pageStyles);
    }

    /**
     * 页面需要加载的特定js
     * @param  string $javascript
     * @return 
     */
    protected function loadJs($javascript)
    {
        if (is_array($javascript)) {
            $this->pageJScripts = array_merge($this->pageJScripts, $javascript);
        } else {
            $this->pageJScripts[] = $javascript;
        }
        $this->pageJScripts = array_filter($this->pageJScripts);
        $this->smarty->assign("pageJScripts", $this->pageJScripts);
    }


    /**
     * json  response
     * @param  int     $code    
     * @param  array   $data
     * @param  string  $message
     * @param  boolean $continue
     * @return exit json 
     */
    public function json($code, $data = [] , $message = '', $redirect = '')
    {
        header('Cache-Control: no-cache, must-revalidate');
        header("Content-Type:application/json; charset=UTF-8");

        $ret = [
            'code'     => $code,
            'message'  => $message,
            'redirect' => $redirect,
            'data'     => $data ?: [],
        ];

        exit(json_encode($ret ,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    protected function jsonRet($data, $continue = false)
    {
        $str = json_encode($data ?: [] ,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!$continue) {
            exit($str);
        } else {
            return $str;
        }
    }

    /**
        * @Synopsis 生成表单token
        *
        * @Returns 
        */
    public function getCSRFToken() {
        $expired   = 600;
        $uuid      = Cookie::get("user_key", array('prefix'=>'u_'));
        $csrfToken = md5($uuid.uniqid().microtime(true));
        $verifyVal = $this->getCsrfVerify($csrfToken);

        MyPDOCacher::getInstance()->setCache($csrfToken, $verifyVal, $expired);

        return $csrfToken;
    }

    
    /**
        * @Synopsis 验证表单token
        *
        * @Param $token
        * @Param $expire 超时时间
        *
        * @Returns 
     */
    public function checkCSRFToken($token, $expire= 600) {
        $csrfVerifyVal = MyPDOCacher::getInstance()->getCache($token);
        //是不是 csrfToken
        $isCsrfToken   = $csrfVerifyVal===$this->getCsrfVerify($token);
        //有且存在csrfToken
        if ($csrfVerifyVal && $isCsrfToken) return true;
        return false;
    }

    protected function getCsrfVerify($csrfToken)
    {
        return substr($csrfToken, 4, 8);
    }

    /**
     * @note 判断是否ajax请求
     */
    public function isAjaxRequest(){
        $status=false;
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])&&strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest'){
            $status=true;
        }
        return $status;
    }

    /**
     * @param $param str int array
     * @note 404跳转
     */
    public function fourToFour($param){
        if(empty($param)) {
            header("location:".NovoURI::url('/error/404'));
        }
    }

    /**
        * @Synopsis 通用JSON数据返回
        *
        * @Param $error
        * @Param $msg
        *
        * @Returns 
        */
    public function responseJSONData($error, $msg) {
        $this->jsonData['error'] = $error;
        $this->jsonData['msg']   = $msg;
        echo json_encode($this->jsonData);
        exit;
    }
}
