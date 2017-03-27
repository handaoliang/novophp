<?php
class HomeController extends AppsBaseController {

    //页面需要身份验证才能进行操作。
    //public $isAuthRequire = true;

    protected $ActionsMap = array(
        "index"         =>"doIndex",
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function doIndex($name=NULL, $password=NULL){
        if(checkUserSignIn()){
            header("location:/dashboard/");
        }
        $homeArray = array(
            "frame_name"        =>"NovoPHP",
            "frame_version"     =>"1.0.3",
        );
        var_dump($name);
        echo "<br />";
        var_dump($password);
        echo "<br />";
        $homeArrayString = simplePackArray($homeArray);
        echo "Pack Array is: " . $homeArrayString . "<br />";
        print_r(simpleUnpackArray($homeArrayString));
        echo "<br />";
        $uriStr = packURIString(12311123);
        echo $uriStr."<br />";
        echo unpackURIString($uriStr)."<br />";
        $homeModels = $this->getModelByName("home");
        $homeData = $homeModels->getHomeData();
        $this->smarty->assign("home_data", $homeData);
        $this->smarty->assign("timestamp", time());
        $this->smarty->display("Home/indexView.tpl");
    }

}
