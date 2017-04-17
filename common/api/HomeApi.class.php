<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @Package
 * @File Name           $RCSfile: HomeApi.class.php,v $
 * @Version             $Revision: 1.0 $
 * @Create Date         $Date: 2017-03-30 00:51:58 $
 * @Last Modified       $Date: 2017-03-30 00:51:58 $
 * @Modified By         $Author: handaoliang <handaoliang@gmail.com> $
 * @Copy Right          Copyright (c) 2017, Comnovo Inc All Rights Reserved.
**/
/**
 * 首页管理 API 
**/
class HomeApi
{
    public static $instances = array();
    public static $model     = NULL;
    public static $appid     = 0;

    public function __construct($appid)
    {
        self::$appid = $appid;
    }

    //初始化API，不同的应用传入不同的ID。
    public static function init($appid = 0)
    {
        if (!isset(self::$instances[$appid])) {
            self::$instances[$appid] = new HomeApi($appid);
            self::$model = new HomeModel();
        }
        return self::$instances[$appid];
    }

    public function getHomeData()
    {
        return self::$model->getHomeData();
    }
}
