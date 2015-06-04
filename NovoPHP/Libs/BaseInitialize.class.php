<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: BaseInitialize.class.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2012/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 基础配置控制文件，多数据库及多缓存控制。
**/
class BaseInitialize {

	/**
	 * 加载配置文件
	 * @param string $configName 要加载的配置
	 * @param boolean $isReload 强制重新加载。
	 */
    public static function loadAppsConfig($configName, $isReload=false)
    {
        static $novoConfigs = array();

        //如果已经存在。
        if(!$isReload && isset($novoConfigs[$configName])){
            return $novoConfigs[$configName];
        }

        //取得配置文件路径。
        $configFilePath = APPS_CONFIGS_DIR.DIRECTORY_SEPARATOR.ucfirst(trim($configName)).'Config.php';
        if (file_exists($configFilePath)) {
            $novoConfigs[$configName] = include $configFilePath;
        }else{
            die("Initialize Error, File: ".$configFilePath." Not Found, Please Check. ");
        }
        return $novoConfigs[$configName];
    }
}
