<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: Common.func.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2013/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 辅助方法集合。
**/

class HelperFunc{

    /**
     * Create user hash password
     * @Param Original Password and password salt
     * @Return hash password
     */
    public static function createHashPassword($originalPassword, $passwordSalt)
    {
        return md5(substr($passwordSalt,3,23).md5($originalPassword).md5(USER_PASSWORD_SALT));
    }

    /**
     * 散列存储
     * @param $savePath - 本地保存的路径
     * @param $fileName - 原始文件名
     * @param $isHashSaving - 是否散列存储。
     * @param $randomFileName - 是否生成随机的Hash文件名。
     * @return array
     **/
    public static function hashFileSavePath($savePath, $fileName='', $isHashSaving=true, $randomFileName=true)
    {
        $hashFileName = $randomFileName ? md5(randStr(20).$fileName.getMicrotime().uniqid()) : md5($fileName);

        $fileSaveDir = $savePath;
        $hashFilePath = '';
        //是否散列存储。
        if($isHashSaving){
            $hashFilePath = substr($hashFileName, 0, 1).DIRECTORY_SEPARATOR.substr($hashFileName, 1, 2);
            $fileSaveDir = $savePath.DIRECTORY_SEPARATOR.$hashFilePath;
        }

        $fileInfo = array(
            "file_path"     =>$hashFilePath,
            "file_name"     =>$hashFileName,
            "error"         =>0
        );

        if(!is_dir($fileSaveDir)){
            $result = mkdir($fileSaveDir, 0777, true);
            if(!$result){
                $fileInfo["error"] = 1;
            }
        }
        return $fileInfo;
    }

    /**
     * 获取散列存储路径
     * @param $fileName, $localFilePath
     * @return string
     **/
    public static function getHashFileSavePath($fileName='', $localFilePath='')
    {
        if($fileName == ''){
            return false;
        }
        if($fileName == "default.png" 
            || $fileName == "default_female.png" 
            || $fileName == "default_male.png"
        ){
            return "web";
        }
        $hashFilePath = substr($fileName, 0, 1).DIRECTORY_SEPARATOR.substr($fileName, 1, 2);
        if($localFilePath != ""){
            return $localFilePath.DIRECTORY_SEPARATOR.$hashFilePath;
        }
        return $hashFilePath;
    }

    /**
     * 获取用户头像
     * @param $fileName, $avatarSize
     * @return string
     **/
    public static function getHashFileWebPath($fileName, $baseWebPath="")
    {
        $pattern = "/(http[s]?:\/\/)/is";
        if(preg_match($pattern, $fileName)){
            return $fileName;
        }else{
            $hashFilePath = substr($fileName, 0, 1)."/".substr($fileName, 1, 2);
            return $baseWebPath."/".$hashFilePath."/".$fileName;
        }
    }

    /**
     * 取得验证Token，用于防止RSCF攻击。
     * @param NULL
     * @return string
     **/
    public static function getVerifyToken(){
        if(isset($_COOKIE["v_token"])){
            $hashToken = md5($_COOKIE["v_token"]);
        }else{
            $verifyToken = md5(createToken());
            setcookie('v_token', $verifyToken, 0, "/", COOKIES_DOMAIN);
            $hashToken = md5($verifyToken);
        }
        return $hashToken;
    }

    /**
     * 检查验证Token。
     * @param $hashToken
     * @return boolean
     **/
    public static function checkVerifyToken($hashToken)
    {
        if(md5($_COOKIE["v_token"]) == $hashToken){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 计算百分比
     * @param $numerator, $denominator
     * @return float number
     **/
    public static function calculatePercent($numerator, $denominator)
    {
        $result = 0;
        if($denominator != 0)
        {
            $result = ($numerator/$denominator)*100;
        }
        return $result;
    }
}
