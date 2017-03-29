<?php
class HomeController extends AppsController {

    //页面需要身份验证才能进行操作。
    //public $isAuthRequire = true;

    protected $ActionsMap = array(
        "index"         =>"doIndex",
        "login"         =>"doLogin",
    );

    public function __construct()
    {
        parent::__construct();
    }

    //可以这样访问：http://www.novophp.com/home/index/your_name/your_password.html
    public function doIndex($name=NULL, $password=NULL){
        if(AppsFunc::checkUserSignIn()){
            header("location:/dashboard/");
        }
        $homeArray = array(
            "frame_name"        =>"NovoPHP",
            "frame_version"     =>"1.0.5",
        );
        var_dump($name);
        echo "<br />";
        var_dump($password);
        echo "<br />";
        $homeArrayString = CommonFunc::simplePackArray($homeArray);
        echo "Pack Array is: " . $homeArrayString . "<br />";
        print_r(CommonFunc::simpleUnpackArray($homeArrayString));
        echo "<br />";
        $uriStr = CommonFunc::packURIString(12311123);
        echo $uriStr."<br />";
        echo CommonFunc::unpackURIString($uriStr)."<br />";
        $homeModels = $this->getModelByName("home");
        $homeData = $homeModels->getHomeData();
        $this->smarty->assign("home_data", $homeData);
        $this->smarty->assign("timestamp", time());
        $this->smarty->display("Home/indexView.tpl");
    }
    public function doLogin(){
        echo "This is Login method..";
    }

}