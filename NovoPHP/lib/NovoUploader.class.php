<?php

if(!defined('NOVOPHP_VENDOR_DIR')){
	echo "NOVOPHP_VENDOR_DIR is not defined,Error File: NovoUploader.class.php";
	exit;
}
require_once NOVOPHP_VENDOR_DIR.'/Asido/class.asido.php';

class NovoUploader
{
    private $allowedExtensions;
    private $sizeLimit;
    private $file;
    private $fileInput;

    protected $returnData = array(
        "error"                 =>1,
        "msg"                   =>"System Error: Process Upload File Error.",
        "file_name"             =>"",
        "file_ext"              =>"",
        "file_hash_name"        =>"",
        "file_path"             =>"",
        "file_web_path"         =>"",
        "original_file_name"    =>"",
    );

    public function initialize($allowedExtensions, $sizeLimit, $fileInput)
    {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;
        $this->fileInput = $fileInput;

        $this->checkServerSettings();

        if (isset($_GET[$this->fileInput])) {
            $this->file = new FileUploaderAjax($fileInput);
        } elseif (isset($_FILES[$this->fileInput])) {
            $this->file = new FileUploaderForm($fileInput);
        } else {
            $this->file = false;
        }
    }

    private function checkServerSettings()
    {
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            $this->returnData["msg"] = "increase php.ini post_max_size and upload_max_filesize to ".$size;
            echo json_encode($this->returnData);
            exit;
        }
    }

    private function toBytes($str)
    {
        $number = intval(substr(trim($str), 0, -1));
        switch(strtoupper(substr($str,-1))) {
            case 'G':
                return $number * pow(1024, 3);
            case 'M':
                return $number * pow(1024, 2);
            case 'K':
                return $number * pow(1024);
        }
    }

    public function asidoResizeImg($args=array(), $crop=false)
    {
        asido::driver('gd');
        $source_image = $args["source_image"];
        $target_image = $args["target_image"];
        $width = $args["width"];
        $height = $args["height"];

        // process crop images
        $i1 = asido::image($source_image, $target_image);
        // fit and add white frame
        if($crop){
            $x = $args["x"];
            $y = $args["y"];
            Asido::crop($i1, $x, $y, $width, $height);
        }else{
            Asido::frame($i1, $width, $height, Asido::Color(255, 255, 255));
        }
        $i1->Save(ASIDO_OVERWRITE_ENABLED);
    }

    function processUploadFile($fileWebPath, $uploadDirectory, $replaceOldFile=false, $isHashSaving=true)
    {
        if(!is_dir($uploadDirectory)){
            $result = mkdir($uploadDirectory, 0777);
            if(!$result){
                $this->returnData['msg'] = "上传文件夹不存在，请手工创建！";
                return $this->returnData;
            }
        }
        if (!is_writable($uploadDirectory)){
            $this->returnData['msg'] = "上传文件夹不可写！";
            return $this->returnData;
        }

        if (!$this->file){
            $this->returnData['msg'] = "没有上传任何文件！";
            return $this->returnData;
        }

        $currentFileName = $this->file->getName();
        $this->returnData["original_file_name"] = $currentFileName;

        //解包文件，取得后缀。
        $pathinfo = pathinfo($currentFileName);

        //判断文件大小情况
        $size = $this->file->getSize();
        if ($size == 0) {
            $this->returnData['msg'] = "文件是空的！";
            return $this->returnData;
        }

        if ($size > $this->sizeLimit) {
            if ($this->sizeLimit >= 1024 * 1024)
            {
                $limitSize = $this->sizeLimit/1024/1024;
                $this->returnData['msg'] = "文件大小不要超过".$limitSize."MB！";
            }
            else if ($this->sizeLimit >= 1024)
            {
                $limitSize = $this->sizeLimit/1024;
                $this->returnData['msg'] = "文件大小不要超过".$limitSize."KB！";
            }

            return $this->returnData;
        }

        //判断扩展类型
        $ext = strtolower($pathinfo['extension']);
        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $ext_str = implode(', ', $this->allowedExtensions);
            $this->returnData['msg'] = "错误的文件类型，必须是：". $ext_str . " 文件";
            //$this->returnData['msg'] = "错误的文件类型！";
            return $this->returnData;
        }

        //创建保存路径
        $hashFileInfo = HelperFunc::hashFileSavePath($uploadDirectory, $currentFileName, $isHashSaving);

        $hashFileName = $hashFileInfo["file_name"];
        $hashFilePath = $hashFileInfo["file_path"];

        $fileSavePath = $uploadDirectory;
        if($isHashSaving){
            $fileSavePath = $uploadDirectory.DIRECTORY_SEPARATOR.$hashFilePath;
        }

        if(!$replaceOldFile){
            //don't overwrite previous files that were uploaded
            if(file_exists($fileSavePath.DIRECTORY_SEPARATOR.$hashFileName.'.'.$ext)) {
                $hashFileInfo = HelperFunc::hashFileSavePath($uploadDirectory, $currentFileName, $isHashSaving);
                $hashFileName = $hashFileInfo["file_name"];
                $hashFilePath = $hashFileInfo["file_path"];
                $fileSavePath = $uploadDirectory;
                if($isHashSaving){
                    $fileSavePath = $uploadDirectory.DIRECTORY_SEPARATOR.$hashFilePath;
                }
            }
        }

        //保存文件到指定文件夹。
        if ($this->file->save($fileSavePath.DIRECTORY_SEPARATOR.$hashFileName.'.'.$ext)){
            $this->returnData['file_name'] = $hashFileName.'.'.$ext;
            $this->returnData['file_ext'] = $ext;
            $this->returnData['file_hash_name'] = $hashFileName;
            $this->returnData['file_path'] = $hashFilePath;
            $this->returnData['file_size'] = $size;
            $this->returnData['file_web_path'] = $fileWebPath.DIRECTORY_SEPARATOR.$hashFilePath;
            $this->returnData['msg'] = "文件上传成功";
            $this->returnData['error'] = 0;
        } else {
            $this->returnData['msg'] = "无法保存文件，请联系管理员。";
        }
        return $this->returnData;
    }
}

/**
 * Handle file uploads via Ajax(XMLHttpRequest)
 */
class FileUploaderAjax
{
    private $fileInput;
    public function __construct($fileInput)
    {
        $this->fileInput = $fileInput;
    }

    function save($path)
    {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()){
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }

    function getFileInfo()
    {
        //@todo USE Ajax How to get info..
        return $_GET[$this->fileInput];
    }

    function getName()
    {
        return $_GET[$this->fileInput];
    }

    function getSize()
    {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class FileUploaderForm
{
    private $fileInput;
    public function __construct($fileInput)
    {
        $this->fileInput = $fileInput;
    }

    function save($path)
    {
        if(!move_uploaded_file($_FILES[$this->fileInput]['tmp_name'], $path)){
            return false;
        }
        return true;
    }

    function getFileInfo()
    {
        return $_FILES[$this->fileInput];
    }

    function getName()
    {
        return $_FILES[$this->fileInput]['name'];
    }

    function getSize()
    {
        return $_FILES[$this->fileInput]['size'];
    }
}
