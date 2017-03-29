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
    //@������֤��ͼƬ�߶�
    private $imagesHeight;
    //@������֤��ͼƬ���
    private $imagesWidth;
    //@������֤���ַ�����
    private $captchaCharacterNum;
    //@������֤���ַ�����
    private $textContent;
    //@�����ַ���ɫ
    private $fontColor;
    //@�����������������ɫ
    private $randFontColor;
    //@���������С
    private $fontSize;
    //@��������
    private $fontFamily;
    //@���屳����ɫ
    private $bgColor;
    //@����������ı�����ɫ
    private $randBgColor;
    //@�����ַ�����
    private $textLang;
    //@������ŵ�����
    private $noisePoint;
    //@�������������
    private $noiseLine;
    //@�����Ƿ�Ť��
    private $distortion;
    //@����Ť��ͼƬԴ
    private $distortionImage;
    //@�����Ƿ��б߿�
    private $showBorder;
    //@������֤��ͼƬԴ
    private $image;
    //@�����Ƿ�Y�������
    private $extRandY;

    //@Constructor ���캯��
    public function  __construct()
    {
        $this->captchaCharacterNum = 4;
        $this->fontSize = 16;
        //������������
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

    //@����ͼƬ���
    public function setImagesWidth($w)
    {
        $this->imagesWidth = $w;
    }

    //@����ͼƬ�߶�
    public function setImagesHeight($h)
    {
        $this->imagesHeight = $h;
    }

    //@�����ַ�����
    public function setCaptchaCharacterNum($captchaCharacterNum)
    {
        $this->captchaCharacterNum = $captchaCharacterNum;
    }

    //@�����ַ���ɫ
    public function setFontColor($fc)
    {
        $this->fontColor = sscanf($fc,'#%2x%2x%2x');
    }

    //@�����ֺ�
    public function setFontSize($n)
    {
        $this->fontSize = $n;
    }

    //@��������
    public function setFontFamily($ffUrl)
    {
        $this->fontFamily = $ffUrl;
    }

    //@�����ַ�����
    public function setTextLang($lang)
    {
        $this->textLang = $lang;
    }

    //@����ͼƬ����
    public function setBgColor($bc)
    {
        $this->bgColor = sscanf($bc,'#%2x%2x%2x');
    }

    //@���ø��ŵ�����
    public function setNoisePoint($n)
    {
        $this->noisePoint = $n;
    }

    //@���ø���������
    public function setNoiseLine($n)
    {
        $this->noiseLine = $n;
    }

    //@�����Ƿ�Ť��
    public function setDistortion($b)
    {
        $this->distortion = $b;
    }

    //@�����Ƿ���ʾ�߿�
    public function setShowBorder($border)
    {
        $this->showBorder = $border;
    }

    //@��ʼ����֤��ͼƬ
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

    //@��������ַ�
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
                $string = iconv('GB2312','UTF-8',$string); //ת�����뵽utf8
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


    //@������ֵ���֤��
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

    //@���ɸ��ŵ�
    public function createNoisePoint()
    {
        for($i=0; $i<$this->noisePoint; $i++){
            $pointColor = imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($this->image,mt_rand(0,$this->imagesWidth), mt_rand(0,$this->imagesHeight), $pointColor);
        }

    }

    //@����������
    public function createNoiseLine()
    {
        for($i=0; $i<$this->noiseLine; $i++) {
            $lineColor = imagecolorallocate($this->image, mt_rand(0,255), mt_rand(0,255),20);
            imageline($this->image, 0, mt_rand(0,$this->imagesWidth), $this->imagesWidth, mt_rand(0,$this->imagesHeight), $lineColor);
        }
    }

    //@Ť������
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

    //@������֤��ͼƬ
    public function createCaptchaImage()
    {
        $this->initImage(); //��������ͼƬ
        $this->createText(); //�����֤���ַ�
        //Ť������
        if($this->distortion){
            $this->distortionText();
        }
        $this->createNoisePoint(); //�������ŵ�
        $this->createNoiseLine(); //����������
        //��ӱ߿�
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

//@������֤����
//$captchaObj->setImagesWidth(200);

//@������֤��߶�
//$captchaObj->setImagesHeight(50);

//@�����ַ�����
$captchaObj->setCaptchaCharacterNum(5);

//@�����ַ���ɫ
//$captchaObj->setFontColor('#ff9900');

//@�����ֺŴ�С
//$captchaObj->setFontSize(25);

//@��������
$captchaObj->setFontFamily('c:\windows\fonts\STXINGKA.TTF');

//@��������
$captchaObj->setTextLang('cn');

//@���ñ�����ɫ
//$captchaObj->setBgColor('#000000');

//@���ø��ŵ�����
//$captchaObj->setNoisePoint(600);

//@���ø���������
//$captchaObj->setNoiseLine(10);

//@�����Ƿ�Ť��
//$captchaObj->setDistortion(true);

//@�����Ƿ���ʾ�߿�
$captchaObj->setShowBorder(true);

//�����֤��
$code = $captchaObj->createCaptchaImage();
//$_SESSION['captcha']['content'] = $code;
//$_SESSION['captcha']['time'] = microtime();
 */
