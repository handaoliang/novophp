<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: NovoStringEncrypt.class.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2013/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 通用基础字符串加密类。
**/
class NovoStringEncrypt
{
    protected static $strsArray;
    protected static $baseChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    protected static $baseKey   = "d@2#6^w&KoB*_sY0^=-LG_L+n#I%@G9n+_i=y*G@~UiFs_h.Zh@L!Oapl^@SnA";

    public static function disorganizeString($normalString)
    {
        self::convertStringToArray($normalString);
        $arr = self::MakeIntArray(self::$strsArray);
        for($i=0;$i<count(self::$strsArray);$i++)
        {
            $ch = self::$strsArray[$i];
            self::$strsArray[$i] = self::$strsArray[$arr[$i]];
            self::$strsArray[$arr[$i]] = $ch;
        }
        return self::revertToString(self::$strsArray);
    }

    public static function unDisorganizeString($encryptedString)
    {
        self::convertStringToArray($encryptedString);
        $arr = self::MakeIntArray(self::$strsArray);
        for($i=count(self::$strsArray)-1;$i>=0;$i--)
        {
            $ch = self::$strsArray[$i];
            self::$strsArray[$i] = self::$strsArray[$arr[$i]];
            self::$strsArray[$arr[$i]] = $ch;
        }
        return self::revertToString(self::$strsArray);
    }

    public static function encryptString($normalString, $key){
        $nh1 = rand(0,64);
        $nh2 = rand(0,64);
        $nh3 = rand(0,64);
        $ch1 = self::$baseChars[$nh1];
        $ch2 = self::$baseChars[$nh2];
        $ch3 = self::$baseChars[$nh3];
        $nhnum = $nh1 + $nh2 + $nh3;
        $knum = 0;$i = 0;
        while(isset($key[$i])){
            $knum += ord($key[$i++]);
        }
        $mdKey = substr(md5(md5(md5($key.$ch1).$ch2.self::$baseKey).$ch3),$nhnum%8,$knum%8+16);
        $str = base64_encode($normalString);
        $str = str_replace(array('+','/','='),array('-','_','.'),$str);
        $tmp = '';
        $j=0;$k = 0;
        $tlen = strlen($str);
        $klen = strlen($mdKey);
        for ($i=0; $i<$tlen; $i++) {
            $k = $k == $klen ? 0 : $k;
            $j = ($nhnum+strpos(self::$baseChars,$str[$i])+ord($mdKey[$k++]))%64;
            $tmp .= self::$baseChars[$j];
        }

        $tmplen = strlen($tmp);
        $tmp = substr_replace($tmp,$ch3,$nh2 % ++$tmplen,0);
        $tmp = substr_replace($tmp,$ch2,$nh1 % ++$tmplen,0);
        $tmp = substr_replace($tmp,$ch1,$knum % ++$tmplen,0);
        return $tmp;
    }

    public static function decryptString($encryptedString, $key)
    {
        $knum = 0;$i = 0;
        $tlen = strlen($encryptedString);
        while(isset($key[$i])){
            $knum += ord($key[$i++]);
        }
        $ch1 = $encryptedString[$knum % $tlen];
        $nh1 = strpos(self::$baseChars,$ch1);
        $str = substr_replace($encryptedString,'',$knum % $tlen--,1);
        $ch2 = $str[$nh1 % $tlen];
        $nh2 = strpos(self::$baseChars,$ch2);
        $str = substr_replace($str,'',$nh1 % $tlen--,1);
        $ch3 = $str[$nh2 % $tlen];
        $nh3 = strpos(self::$baseChars,$ch3);
        $str = substr_replace($str,'',$nh2 % $tlen--,1);
        $nhnum = $nh1 + $nh2 + $nh3;
        $mdKey = substr(md5(md5(md5($key.$ch1).$ch2.self::$baseKey).$ch3),$nhnum % 8,$knum % 8 + 16);
        $tmp = '';
        $j=0; $k = 0;
        $tlen = strlen($str);
        $klen = strlen($mdKey);
        for ($i=0; $i<$tlen; $i++) {
            $k = $k == $klen ? 0 : $k;
            $j = strpos(self::$baseChars,$str[$i])-$nhnum - ord($mdKey[$k++]);
            while ($j<0) $j+=64;
            $tmp .= self::$baseChars[$j];
        }
        $tmp = str_replace(array('-','_','.'),array('+','/','='),$tmp);
        return trim(base64_decode($tmp));
    }

    public static function xxteaEncrypt($str, $key) {
        if ($str == "") {
            return "";
        }
        $v = self::str2long($str, true);
        $k = self::str2long($key, false);
        if (count($k) < 4) {
            for ($i = count($k); $i < 4; $i++) {
                $k[$i] = 0;
            }
        }
        $n = count($v) - 1;

        $z = $v[$n];
        $y = $v[0];
        $delta = 0x9E3779B9;
        $q = floor(6 + 52/($n + 1));
        $sum = 0;
        while (0 < $q--) {
            $sum = self::int32($sum + $delta);
            $e = $sum >> 2 & 3;
            for ($p = 0; $p < $n; $p++) {
                $y = $v[$p + 1];
                $mx = self::int32((($z>>5&0x07ffffff)^$y<<2)+(($y>>3&0x1fffffff)^$z<<4))^self::int32(($sum^$y)+($k[$p&3^$e]^$z));
                $z = $v[$p] = self::int32($v[$p] + $mx);
            }
            $y = $v[0];
            $mx = self::int32((($z>>5&0x07ffffff)^$y<<2)+(($y>>3&0x1fffffff)^$z<<4))^self::int32(($sum^$y)+($k[$p&3^$e]^$z));
            $z = $v[$n] = self::int32($v[$n] + $mx);
        }
        return self::long2str($v, false);
    }

    public static function xxteaDecrypt($str, $key) {
        if ($str == "") {
            return "";
        }
        $v = self::str2long($str, false);
        $k = self::str2long($key, false);
        if (count($k) < 4) {
            for ($i = count($k); $i < 4; $i++) {
                $k[$i] = 0;
            }
        }
        $n = count($v) - 1;

        $z = $v[$n];
        $y = $v[0];
        $delta = 0x9E3779B9;
        $q = floor(6 + 52 / ($n + 1));
        $sum = self::int32($q * $delta);
        while ($sum != 0) {
            $e = $sum >> 2 & 3;
            for ($p = $n; $p > 0; $p--) {
                $z = $v[$p - 1];
                $mx = self::int32((($z>>5&0x07ffffff)^$y<<2)+(($y>>3&0x1fffffff)^$z<<4))^self::int32(($sum^$y)+($k[$p&3^$e]^$z));
                $y = $v[$p] = self::int32($v[$p] - $mx);
            }
            $z = $v[$n];
            $mx = self::int32((($z>>5&0x07ffffff)^$y<<2)+(($y>>3&0x1fffffff)^$z<<4))^self::int32(($sum^$y)+($k[$p&3^$e]^$z));
            $y = $v[0] = self::int32($v[0] - $mx);
            $sum = self::int32($sum - $delta);
        }
        return self::long2str($v, true);
    }

    private static function convertStringToArray($currentString)
    {
        $string = preg_replace("/(.{1})/", "\\1,",$currentString);
        self::$strsArray = explode(",", substr($string, 0,-1));
    }

    private static function MakeIntArray($arr)
    {
        $seed = ((count($arr)*count($arr) * (3719/2)) + 3719) % 65535;
        $max = count($arr);
        for($i=0;$i<count($arr);$i++){
            $arr[$i] = $seed % $max;
        }
        return $arr;
    }

    private static function revertToString($arr)
    {
        $string = "";
        foreach ($arr as $str)
            $string.= $str;
        return $string;
    }

    private static function long2str($v, $w) {
        $len = count($v);
        $n = ($len - 1) << 2;
        if ($w) {
            $m = $v[$len - 1];
            if (($m < $n - 3) || ($m > $n)) return false;
            $n = $m;
        }
        $s = array();
        for ($i = 0; $i < $len; $i++) {
            $s[$i] = pack("V", $v[$i]);
        }
        if ($w) {
            return substr(join('', $s), 0, $n);
        } else {
            return join('', $s);
        }
    }

    private static function str2long($s, $w) {
        $v = unpack("V*", $s. str_repeat("\0", (4 - strlen($s) % 4) & 3));
        $v = array_values($v);
        if ($w) {
            $v[count($v)] = strlen($s);
        }
        return $v;
    }

    private static function int32($n) {
        while ($n >= 2147483648) $n -= 4294967296;
        while ($n <= -2147483649) $n += 4294967296;
        return (int)$n;
    }
}

/*
$stringCn = "卡桑德拉附近阿斯頓法律薩拉丁發生地發";
$stringEn = "abcdefghijklmn";
$key = "!#$!@SDAD*SFD*GFG_H#$%DFSGDF*GHSDsaa(*sldfklsadf)";
echo "\n\n";
echo $stringEn;
echo "\n\n";
echo $stringCn;
echo "\n\n=================\n\n";
$aa = NovoStringEncrypt::disorganizeString($stringEn);
echo $aa;
echo "\n\n";
echo NovoStringEncrypt::unDisorganizeString($aa);
echo "\n\n=================\n\n";
$bb = NovoStringEncrypt::encryptString($stringCn, $key);
echo $bb;
echo "\n\n";
echo NovoStringEncrypt::decryptString($bb, $key);
echo "\n\n=================\n\n";
$cc = NovoStringEncrypt::xxteaEncrypt($stringCn, $key);
echo $cc;
echo "\n\n";
echo NovoStringEncrypt::xxteaDecrypt($cc, $key);
echo "\n\n=================\n\n";
 */
