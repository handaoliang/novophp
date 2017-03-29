<?php
class HelperController extends BaseController {

    protected $ActionsMap = array(
        "signup_captcha"                =>"doCreateSignupCaptcha",
        "signin_captcha"                =>"doCreateSigninCaptcha",
        "forgot_passwd_captcha"         =>"doCreateForgotPasswdCaptcha",
        "check_captcha"                 =>"doCheckCaptcha",
    );

    protected $returnData = array(
        "error"     =>1,
        "msg"       =>"",
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function doCreateSignupCaptcha()
    {
        header("Content-type:image/png");
        $captchaObj = new BaseCaptcha();
        $captchaObj->setImagesHeight(33);
        $captchaObj->setRandY(true);
        $captchaObj->setBgColor('#E1FFFF');
        $code = $captchaObj->createCaptchaImage();
        $_SESSION['signup_captcha']['content']=$code;
        $_SESSION['signup_captcha']['time']=microtime();
    }

    public function doCreateSigninCaptcha()
    {
        header("Content-type:image/png");
        $captchaObj = new BaseCaptcha();
        $captchaObj->setImagesHeight(33);
        $captchaObj->setRandY(true);
        $captchaObj->setBgColor('#E1FFFF');
        $code = $captchaObj->createCaptchaImage();
        $_SESSION['signin_captcha']['content']=$code;
        $_SESSION['signin_captcha']['time']=microtime();
    }


    public function doCreateForgotPasswdCaptcha()
    {
        header("Content-type:image/png");
        $captchaObj = new BaseCaptcha();
        $captchaObj->setImagesHeight(33);
        $captchaObj->setRandY(true);
        $captchaObj->setBgColor('#E1FFFF');
        $code = $captchaObj->createCaptchaImage();
        $_SESSION['forgot_pwd_captcha']['content']=$code;
        $_SESSION['forgot_pwd_captcha']['time']=microtime();
    }

    public function doCheckCaptcha()
    {
        $userCaptcha = trim(exPost("user_captcha"));
        $userActions = strtolower(trim(exPost("user_actions")));
        if(!isset($_SESSION[$userActions."_captcha"])){
            $this->returnData["msg"] = "验证码错误，请勿重复提交数据！";
            echo json_encode($this->returnData);
            exit;
        }

        $sessionCaptcha = $_SESSION[$userActions."_captcha"]["content"];
        if(strtolower($userCaptcha) != strtolower($sessionCaptcha))
        {
            $this->returnData["msg"] = "验证码错误，请重填！";
            echo json_encode($this->returnData);
            exit;
        }else{
            $this->returnData["error"] = 0;
            echo json_encode($this->returnData);
            exit;
        }
    }
}
