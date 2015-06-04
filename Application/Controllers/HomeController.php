<?php
class HomeController extends BaseController {

    protected $ActionsMap = array(
        "index"         =>"doIndex",
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function doIndex(){
        if(checkUserSignIn()){
            header("location:/dashboard/");
        }
        $homeArray = array(
            "frame_name"        =>"NovoPHP",
            "frame_version"     =>"1.0.3",
        );
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
