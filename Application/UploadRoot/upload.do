<?php

/*兼用flash传递session*/
if (isset($_POST['PHPSESSID'])) {
    session_id($_POST['PHPSESSID']);
}
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Configs".DIRECTORY_SEPARATOR."init.php";
class Upload {
    protected $agentAction = array('subject', 'uploadApp');
    protected $chkip = false;

    function __construct() {
        if (isset($_POST['agt']) && is_string($_POST['agt'])) {
            $agt = strtolower($_POST['agt']);
            if (in_array($agt, $this->agentAction) || is_callable("Upload", $agt."Action")) {
                call_user_func_array(array($this, $agt."Action"), array());
            } else {
                $this->action(); 
            }
        } else {
            $this->action(); 
        }
    }
    function action() {
        echo "Error Params Sending, Please Check It!";
        exit;
    }
    function subjectAction() {
        //代理
        $_GET['controller'] = 'uploader';
        $_GET['action']     = 'do_upload_subject_item_pic';
        $_GET['request_data_type'] = 'json';
        BaseInterface::initInterface();
    }
    function uploadAppAction() {
        //代理
        $_GET['controller'] = 'uploader';
        $_GET['action']     = 'do_upload_android_app';
        $_GET['request_data_type'] = 'json';
        BaseInterface::initInterface();
    }
    function uploadAppLogoAction() {
        //代理
        $_GET['controller'] = 'uploader';
        $_GET['action']     = 'do_upload_android_app_logo';
        $_GET['request_data_type'] = 'json';
        BaseInterface::initInterface();
    }
    function uploadAppThumbnailAction() {
        //代理
        $_GET['controller'] = 'uploader';
        $_GET['action']     = 'do_upload_android_app_thumbnail';
        $_GET['request_data_type'] = 'json';
        BaseInterface::initInterface();
    }


}
new Upload();
?>
