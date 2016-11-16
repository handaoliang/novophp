<?php

class AppsBaseController extends BaseController {

    //页面是否要求身份认证。默认不需要（False）
    public $isAuthRequire     = false;

    protected $userInfo  = [];
    protected $userModel = null;

    public $request_method = '';

    public $jsonData  = array(
        'error'     =>0,
        'msg'       =>'',
        'data'      =>array(),
    );

    public $table_prefix = MYSQL_TABLE_PREFIX;

    public function __construct() {
        $this->userInfo  = $this->getUserInfo();

        if ($this->isAuthRequire) {
            if (!$this->userInfo) {
                $this->redirect(NovoURI::url('/login'));
            } 
        }

        //request method
        $this->request_method = strtolower($_SERVER['REQUEST_METHOD']);
        parent::__construct();

        $this->smarty->assign("_G", $GLOBALS);
        $this->smarty->assign("_C", $this->getNamespace()[1]);
        $this->smarty->assign("uri", $_SERVER['REQUEST_URI']);

        //同步登录脚本，在footer未知调用
        $this->smarty->assign("syncScripts", $this->getSyncScript());
        $this->smarty->assign("userInfo",    $this->userInfo);

        //load PrependCss
        $this->loadPrependCss();

        //assign common VAR.
        $this->smarty->assign("charset",SYSTEM_CHARSET);
        $this->smarty->assign("lang",SYSTEM_LANG);
        $this->smarty->assign("web",WEB_ROOT_PATH);
        $this->smarty->assign("res",RES_ROOT_PATH);
        $this->smarty->assign("files",FILES_PATH);

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
     * 密码验证后授权，1分钟内有效
     */
    public function setSudo()
    {
        $_SESSION['user']['sudo'] = time(); 
        return $_SESSION['user']['sudo'];
    }

    /**
     * 密码验证后授权，5分钟内有效
     * 检测是否有授权
     * @return boolean
     */
    public function checkSudo()
    {
        $expired = 300;
        if (!empty($_SESSION['user']['sudo'])) {
            if (time() - $_SESSION['user']['sudo'] < $expired) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 设置登陆用户信息到 父控制器
     * @param array  $userInfo 
     * @param string $loginkey
     */
    protected function setUserLogin($userInfo, $loginkey) 
    {
        $_SESSION['user']['user_id']  = exValue($userInfo, 'user_id', 0);
        $_SESSION['user']['loginkey'] = $loginkey;
        if (exPost('remember')) {
            //记住密码，种个一年的cookie
            $cookiesLifeTime = 365 * 60*60*24;
            setcookie(session_name() ,session_id(), time() + $cookiesLifeTime, COOKIES_PATH, COOKIES_DOMAIN);
        }
        $this->userInfo = $this->getUserInfo();
    }

    /**
     * 设置注销登陆，清除session，清除当前实例属性 userInfo
     * @param array $userInfo 
     */
    protected function setUserLogout() 
    {
        $this->userInfo = [];
        $res = MyUserCenterSDK::logout($_SESSION['user']['loginkey']);
        if ($res['error']==0) {
            $this->setSyncScript(exValue($res['data'], 'syncLogin', null));
        }
        session_destroy();
        unset($_SESSION['user']);
        //退出登陆后，继续维持session_id()本次session有效，以维护此用户身份
        setcookie(session_name(), session_id(), 0, COOKIES_PATH, COOKIES_DOMAIN);
    }

    /**
     * 保存 同步登录需要加载到页面的js脚本 到 $_SESSION
     * @param array $syncScript 
     */
    protected function setSyncScript($syncScript) 
    {
        $_SESSION['user']['syncScript'] = $syncScript;
    }

    /**
     * 取得同步登录需要加载到页面的js脚本
     * @return array
     */
    protected function getSyncScript() 
    {
        $syncScript = [];
        if (isset($_SESSION['user']['syncScript'])) {
            $syncScript = $_SESSION['user']['syncScript'];
            unset($_SESSION['user']['syncScript']);
        }
        return $syncScript;
    }

    
    /**
     * 加载 UsersModel
     * @return [type] [description]
     */
    protected function loadUserModel()
    {
        if (!$this->userModel) {
            return $this->userModel = $this->getModelByName('users');
        } else {
            return $this->userModel;
        }
    }

    /**
     * 获取session中的 user_id
     * @return mixed user_id
     */
    protected function getUserId($default = null)
    {
        return isset($_SESSION['user']['user_id']) ? $_SESSION['user']['user_id'] : $default;
    }

    /**
     * 通过session 中存储的 user_id 获取当前登录用户信息
     * @return mixed 
     */
    protected function getUserInfo() {
        $userId = $this->getUserId();
        //no login user
        if (!$userId) return [];

        //userInfo got in the construct
        if ($this->userInfo) return $this->userInfo;

        //cache query for user info
        $userInfo = $this->loadUserModel()->cacheFind($userId);
        //不使用缓存
        //$userInfo = $this->loadUserModel()->cacheFind($userId, false);

        return $userInfo;
    }

    /**
     * 检测是否登陆，如果登陆了，则跳转到来源或者首页
     * 
     * @param  boolean $isJson 是否是json请求，如果是则，通过$this->json()返回跳转字符串
     */
    protected function loginRedirct($source = null, $isJson = false)
    {
        $source = $source ?  NovoURI::url($source) : urldecode(exGet('source'));
        $source = $source ?: NovoURI::url('/');
        if ($this->userInfo || $this->getUserInfo()) {
            $this->redirect(urldecode($source), $isJson);
        }
        return $source;
    }

    /**
     * 页面跳转
     * @param  boolean $isJson 是否是json请求，如果是则返回跳转json
     */
    public function redirect($url, $isJson = false) {
        $url = $url ?: $_SERVER['HTTP_REFERER'];
        if ($isJson) {
            $this->json(STATUS_SUCCESS, [], '', $url);
        } else {
            header("Location:".$url);
            exit;
        }
    }

    public function returnJsonData() {
        echo json_encode($this->jsonData);
        exit;
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
