<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: BaseCaptcha.class.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2016/04/10 12:02:11 $
 * @copyright            Copyright (c) 2016, Comnovo Inc.
**/
/**
 * Captcha Class base on PHP GD Lib
 **/
class NovoCaptcha
{
    //@定义验证码图片高度
    private $imagesHeight;
    //@定义验证码图片宽度
    private $imagesWidth;
    //@定义验证码字符个数
    private $captchaCharacterNum;
    //@定义验证码字符内容
    private $textContent;
    //@定义字符颜色
    private $fontColor;
    //@定义随机出的文字颜色
    private $randFontColor;
    //@定义字体大小
    private $fontSize;
    //@定义字体
    private $fontFamily;
    //@定义背景颜色
    private $bgColor;
    //@定义随机出的背景颜色
    private $randBgColor;
    //@定义字符语言
    private $textLang;
    //@定义干扰点数量
    private $noisePoint;
    //@定义干扰线数量
    private $noiseLine;
    //@定义是否扭曲
    private $distortion;
    //@定义扭曲图片源
    private $distortionImage;
    //@定义是否有边框
    private $showBorder;
    //@定义验证码图片源
    private $image;
    //@定义是否Y轴随机。
    private $extRandY;

    //@Constructor 构造函数
    public function  __construct()
    {
        $this->captchaCharacterNum = 4;
        $this->fontSize = 16;
        //设置中文字体
        $this->fontFamily = NOVOPHP_VENDOR_DIR.'/Fonts/Arial.ttf';
        $this->textLang = 'en';
        $this->noisePoint = 30;
        $this->noiseLine = 3;
        $this->distortion = false;
        $this->showBorder = false;
        $this->extRandY = false;
    }

    public function setRandY($ry)
    {
        $this->extRandY = $ry;
    }

    //@设置图片宽度
    public function setImagesWidth($w)
    {
        $this->imagesWidth = $w;
    }

    //@设置图片高度
    public function setImagesHeight($h)
    {
        $this->imagesHeight = $h;
    }

    //@设置字符个数
    public function setCaptchaCharacterNum($captchaCharacterNum)
    {
        $this->captchaCharacterNum = $captchaCharacterNum;
    }

    //@设置字符颜色
    public function setFontColor($fc)
    {
        $this->fontColor = sscanf($fc,'#%2x%2x%2x');
    }

    //@设置字号
    public function setFontSize($n)
    {
        $this->fontSize = $n;
    }

    //@设置字体
    public function setFontFamily($ffUrl)
    {
        $this->fontFamily = $ffUrl;
    }

    //@设置字符语言
    public function setTextLang($lang)
    {
        $this->textLang = $lang;
    }

    //@设置图片背景
    public function setBgColor($bc)
    {
        $this->bgColor = sscanf($bc,'#%2x%2x%2x');
    }

    //@设置干扰点数量
    public function setNoisePoint($n)
    {
        $this->noisePoint = $n;
    }

    //@设置干扰线数量
    public function setNoiseLine($n)
    {
        $this->noiseLine = $n;
    }

    //@设置是否扭曲
    public function setDistortion($b)
    {
        $this->distortion = $b;
    }

    //@设置是否显示边框
    public function setShowBorder($border)
    {
        $this->showBorder = $border;
    }

    //@初始化验证码图片
    public function initImage()
    {
        if(empty($this->imagesWidth))
        {
            $this->imagesWidth = floor($this->fontSize*1.3)*$this->captchaCharacterNum+10;
        }
        if(empty($this->imagesHeight))
        {
            $this->imagesHeight = $this->fontSize*2;
        }
        $this->image = imagecreatetruecolor($this->imagesWidth, $this->imagesHeight);
        if(empty($this->bgColor))
        {
            $this->randBgColor = imagecolorallocate($this->image,mt_rand(100,255),mt_rand(100,255),mt_rand(100,255));
        }else{
            $this->randBgColor = imagecolorallocate($this->image,$this->bgColor[0],$this->bgColor[1],$this->bgColor[2]);
        }
        imagefill($this->image,0,0,$this->randBgColor);
    }

    //@产生随机字符
    public function randText($type)
    {
        $lowerChars = 'abcdefghijklmnopqrstuvwxyz';
        $capitalChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $number = '0123456789';

        $string = '';
        switch($type){
            case 'en':
                //$str = $lowerChars.$capitalChars.$number;
                //$str = $capitalChars.$number;
                $str = $capitalChars;
                for($i=0; $i<$this->captchaCharacterNum; $i++){
                    $start = rand(1,strlen($str) - 1);
                    $string .=  ','.substr($str,$start,1);
                }
                break;
            case 'cn':
                for($i=0; $i<$this->captchaCharacterNum; $i++) {
                    $string .=  ','.chr(rand(0xB0,0xCC)).chr(rand(0xA1,0xBB));
                }
                $string = iconv('GB2312','UTF-8',$string); //转换编码到utf8
                break;
        }
        return substr($string,1);
    }

    private function getYCoordinate ()
    {
        if ($this->extRandY) {
            return rand(intval($this->fontSize+5), intval($this->imagesHeight-5));
        } else {
            return floor($this->imagesHeight*0.75);
        }
    }


    //@输出文字到验证码
    public function createText()
    {
        $textArray = explode(',',$this->randText($this->textLang));
        $this->textContent = join('',$textArray);
        if(empty($this->fontColor)){
            $this->randFontColor = imagecolorallocate($this->image,mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
        }else{
            $this->randFontColor = imagecolorallocate($this->image,$this->fontColor[0],$this->fontColor[1],$this->fontColor[2]);
        }
        for($i=0; $i<$this->captchaCharacterNum; $i++){
            $angle = mt_rand(-1,1)*mt_rand(1,20);
            $yCoordinate = $this->getYCoordinate();
            imagettftext($this->image,$this->fontSize,$angle,5+$i*floor($this->fontSize*1.3),$yCoordinate,$this->randFontColor,$this->fontFamily,$textArray[$i]);
        }
    }

    //@生成干扰点
    public function createNoisePoint()
    {
        for($i=0; $i<$this->noisePoint; $i++){
            $pointColor = imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($this->image,mt_rand(0,$this->imagesWidth), mt_rand(0,$this->imagesHeight), $pointColor);
        }

    }

    //@产生干扰线
    public function createNoiseLine()
    {
        for($i=0; $i<$this->noiseLine; $i++) {
            $lineColor = imagecolorallocate($this->image, mt_rand(0,255), mt_rand(0,255),20);
            imageline($this->image, 0, mt_rand(0,$this->imagesWidth), $this->imagesWidth, mt_rand(0,$this->imagesHeight), $lineColor);
        }
    }

    //@扭曲文字
    public function distortionText()
    {
        $this->distortionImage = imagecreatetruecolor($this->imagesWidth,$this->imagesHeight);
        imagefill($this->distortionImage,0,0,$this->randBgColor);
        for($x=0; $x<$this->imagesWidth; $x++){
            for($y=0; $y<$this->imagesHeight; $y++){
                $rgbColor = imagecolorat($this->image,$x,$y);
                imagesetpixel($this->distortionImage,(int)($x+sin($y/$this->imagesHeight*2*M_PI-M_PI*0.5)*3),$y,$rgbColor);
            }
        }
        $this->image = $this->distortionImage;
    }

    //@生成验证码图片
    public function createCaptchaImage()
    {
        $this->initImage(); //创建基本图片
        $this->createText(); //输出验证码字符
        //扭曲文字
        if($this->distortion){
            $this->distortionText();
        }
        $this->createNoisePoint(); //产生干扰点
        $this->createNoiseLine(); //产生干扰线
        //添加边框
        if($this->showBorder){
            imagerectangle($this->image, 0, 0, $this->imagesWidth-1, $this->imagesHeight-1, $this->randFontColor);
        }
        imagepng($this->image);
        imagedestroy($this->image);
        if($this->distortion){
            imagedestroy($this->$distortionImage);
        }
        return $this->textContent;
    }

}
/*
//session_start();
header("Content-type:image/png");
$captchaObj = new BaseCaptcha();

//@设置验证码宽度
//$captchaObj->setImagesWidth(200);

//@设置验证码高度
//$captchaObj->setImagesHeight(50);

//@设置字符个数
$captchaObj->setCaptchaCharacterNum(5);

//@设置字符颜色
//$captchaObj->setFontColor('#ff9900');

//@设置字号大小
//$captchaObj->setFontSize(25);

//@设置字体
$captchaObj->setFontFamily('c:\windows\fonts\STXINGKA.TTF');

//@设置语言
$captchaObj->setTextLang('cn');

//@设置背景颜色
//$captchaObj->setBgColor('#000000');

//@设置干扰点数量
//$captchaObj->setNoisePoint(600);

//@设置干扰线数量
//$captchaObj->setNoiseLine(10);

//@设置是否扭曲
//$captchaObj->setDistortion(true);

//@设置是否显示边框
$captchaObj->setShowBorder(true);

//输出验证码
$code = $captchaObj->createCaptchaImage();
//$_SESSION['captcha']['content'] = $code;
//$_SESSION['captcha']['time'] = microtime();
 */
