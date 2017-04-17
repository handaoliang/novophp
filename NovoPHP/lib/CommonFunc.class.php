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
 * 通用方法集合。
**/
class CommonFunc {

    /*
     * 判断并获取GET数据
     * @param $name:field name;
     * @return 如果存在，则返回GET的数据，否则返回空!
     * @超全局变量$_GET是做过urldecode的，无须解码。
     */
    public static function exGet($name)
    {
        if (array_key_exists($name,$_GET)) {
            return $_GET[$name];
        } else {
            return "";
        }
    }

    /**
     * 判断并获取POST数据
     * @param $name:field name;
     * @return 如果存在，则返回POST的数据，否则返回空!
     * @超全局变量$_POST的值没有经过解码，所以在这里进行一下urldecode。
     */
    public static function exPost($name)
    {
        if (array_key_exists($name,$_POST)) {
            return urldecode($_POST[$name]);
        } else {
            return "";
        }
    }

    /**
     * 判断并获取REQUEST数据
     * @param $name:field name;
     * @return 如果存在，则返回REQUEST的数据，否则返回空!
     * @超全局变量$_REQUEST是做过urldecode的，无须解码。
     */
    public static function exRequest($name)
    {
        if (array_key_exists($name, $_REQUEST)) {
            return $_REQUEST[$name];
        } else {
            return "";
        }
    }

    /**
     * 判断字符串是否为UTF-8编码。
     * @param $content:字符串
     * @return 转换过的字符串
     **/
    public static function isUTF8($string)
    {
        // From http://w3.org/International/questions/qa-forms-utf-8.html
        return preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E] # ASCII
            | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
        )*$%xs', $string);
    }

    /**
     * 转GB2312编码到UTF-8编码。
     * @param $content:字符串
     * @return 转换过的字符串
     **/
    public static function GBK2UTF8($content)
    {
        return @iconv("GBK", "UTF-8//IGNORE", $content);
    }
    /**
     * 转UTF-8编码到GBK编码。
     * @param $content:字符串
     * @return 转换过的字符串
     **/
    public static function UTF82GBK($content)
    {
        return @iconv("UTF-8//IGNORE", "GBK", $content);
    }

    /**
     * 比较两个字符串是否相等。
     * @param $str1 $str2 - 需要比较的两个字符串
     * @return boolean
     **/
    public static function cmpString($str1,$str2)
    {
        if(0 == strcmp(trim($str1),trim($str2))) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * 根据时间字符串返回时间戳
     * @param $data:时间字符串
     * @return 时间戳
     */
    public static function getTimestamp($date)
    {
        $returnValue = false;
        if (preg_match("/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/", $date, $m)) {
            $returnValue = mktime($m[4], $m[5], $m[6], $m[2], $m[3], $m[1]);
        }else {
            if (preg_match("/(\d+)-(\d+)-(\d+)/", $date, $m)) {
                $returnValue = mktime(00, 00, 00, $m[2], $m[3], $m[1]);
            }
        }
        return $returnValue;
    }
    /**
     * 当用Nginx作为代理服务器时取真实IP的方法
     * 注意：如果前端配置以Nginx作为服务器负载均衡，那么，Nginx的配置中要带上proxy_set_header：
        upstream www_novophp_com{
            #server  127.0.0.1:8299;
            server unix:/www/novophp.sock fail_timeout=0;

            proxy_set_header    Host            $host;
            proxy_set_header    X-Real-IP       $remote_addr;
            proxy_set_header    X-Forwarded-For $proxy_add_x_forwarded_for;
        }
     * 另外Nginx的编译参数中，要加上： --with-http_realip_module
    **/
    public static function getRealIpAddressForNginx(){
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }
        if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        return $_SERVER["REMOTE_ADDR"];
    }

    /**
     * 获取用户真实IP地址
     * @param $phpMode:PHP的安装模式;
     * @return 用户真实IP;
     * 几点说明备忘：
     * 1. 标准实现的 proxy 会在代理的请求中增加一个 HTTP 头部 X-Forwarded-For，记录源 IP 信息
     * 2. 对于多级 Proxy 代理的，会在 X-Forwarded-For 字段后增加信息，比如：X-Forwarded-For: client1, proxy1, proxy2
     * 3. PHP 可以通过 $_SERVER['HTTP_X_FORWARDED_FOR'] 获取该头部字段，然后判断该变量是否存在，再决定是否采取 $_SERVER['REMOTE_ADDR']
     * 4. 也有 apache module 让这一切对编程者更加透明，请参考 mod_extract_forwarded(http://web.warhound.org/mod_extract_forwarded/)
     启动该模块后，可以统一采用 $_SERVER['REMOTE_ADDR'] 了，同时可以通过 $_SERVER['PROXY_ADDR'] 来获取实际连接的 proxy 地址
     */
    public static function getRealIpAddressForApache($phpMode=1)
    {
        //PHP的安装模式，0为CGI模式，1为模块方式安装
        if($phpMode){
            //需要判断客户端是否使用了代理
            if(array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER)) {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }else{
                return $_SERVER["REMOTE_ADDR"];
            }
        }else{
            $myIpAddress = getenv("HTTP_X_FORWARDED_FOR");
            if($myIpAddress && $myIpAddress != "unknown"){
                return $myIpAddress;
            }
            $myIpAddress = getenv("HTTP_CLIENT_IP");
            if($myIpAddress && $myIpAddress != "unknown"){
                return $myIpAddress;
            }
            $myIpAddress = getenv("REMOTE_ADDR");
            if($myIpAddress && $myIpAddress != "unknown"){
                return $myIpAddress;
            }
        }
    }

    /**
     * 依据指定长度截取字符串
     * 传入值：$sourceStr　－　源字符串，即需要截取的字符串。
     $outStrLen　－　输出字符串的长度！
     * 返回值：经过处理后的字符串！
     **/
    public static function mbString($sourceStr,$outStrLen)
    {
        $curStrLen = mb_strlen($sourceStr,"UTF-8");
        if($curStrLen > $outStrLen){
            $echoStr = mb_substr($sourceStr, 0, $outStrLen, "UTF-8")."...";
        }else{
            //如果小于，则输出全部的字符。
            $echoStr = $sourceStr;
        }
        return $echoStr;
    }

    /**
     * 把网页中的相对路径转化成绝对路径
     * @param $content,$feed_url
     * @return 转化完后的路径。
     **/
    public static function relative2Absolute($content, $feed_url)
    {
        preg_match('/(http|https|ftp):\/\//', $feed_url, $protocol);
        $server_url = preg_replace("/(http|https|ftp|news):\/\//", "", $feed_url);
        $server_url = preg_replace("/\/.*/", "", $server_url);

        if ($server_url == '') {
            return $content;
        }

        if (isset($protocol[0])) {
            $new_content = preg_replace('/href="\//', 'href="'.$protocol[0].$server_url.'/', $content);
            $new_content = preg_replace('/src="\//', 'src="'.$protocol[0].$server_url.'/', $new_content);
        } else {
            $new_content = $content;
        }
        return $new_content;
    }

    /**
     * 快速排序算法。
     * @param $Array
     * @return 排序后的Array。
     **/
    public static function quickSort($array)
    {
        if (count($array) <= 1) return $array;

        $key = $array[0];
        $left_arr = array();
        $right_arr = array();
        for ($i=1; $i<count($array); $i++){
            if ($array[$i] <= $key){
                $left_arr[] = $array[$i];
            }else{
                $right_arr[] = $array[$i];
            }
        }
        $left_arr = self::quickSort($left_arr);
        $right_arr = self::quickSort($right_arr);

        return array_merge($left_arr, array($key), $right_arr);
    }

    /**
     * 对二维数组进行排序
     * @param $Array - sort array
     $sort - sort by
     $d - sort desc
     * @return 排序后的Array。
     **/
    public static function array_sort($a,$sort,$d='asc')
    {
        $num=count($a);
        if($d == 'asc'){
            for($i=0;$i<$num;$i++){
                for($j=0;$j<$num-1;$j++){
                    if($a[$j][$sort] > $a[$j+1][$sort]){
                        foreach ($a[$j] as $key=>$temp){
                            $t=$a[$j+1][$key];
                            $a[$j+1][$key]=$a[$j][$key];
                            $a[$j][$key]=$t;
                        }
                    }
                }
            }
        }else{
            for($i=0;$i<$num;$i++){
                for($j=0;$j<$num-1;$j++){
                    if($a[$j][$sort] < $a[$j+1][$sort]){
                        foreach ($a[$j] as $key=>$temp){
                            $t=$a[$j+1][$key];
                            $a[$j+1][$key]=$a[$j][$key];
                            $a[$j][$key]=$t;
                        }
                    }
                }
            }
        }
        return $a;
    }

    /**
     * 取得微秒时间
     * @param NULL
     * @return: float microtime value.
     **/
    public static function microtimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * 正则替换文本中的URL
     * @param: string.
     * @return: string.
     */
    public static function ParseURL($str)
    {
        return preg_replace(
            array(
                "/(?<=[^\]A-Za-z0-9-=\"'\\/])(https?|ftp|gopher|news|telnet|mms){1}:\/\/([A-Za-z0-9\/\-_+=.~!%@?#%&;:$\\()|]+)/is",
                //"/([\n\s])www\.([a-z0-9\-]+)\.([A-Za-z0-9\/\-_+=.~!%@?#%&;:$\[\]\\()|]+)((?:[^\x7f-\xff,\s]*)?)/is",
                "/([^\/\/])www\.([a-z0-9\-]+)\.([A-Za-z0-9\/\-_+=.~!%@?#%&;:$\[\]\\()|]+)((?:[^\x7f-\xff,\s]*)?)/is",
                "/(?<=[^\]A-Za-z0-9\/\-_.~?=:.])([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,4}))/si"
            ),
            array(
                "<a href=\"\\1://\\2\" target=\"_blank\">\\1://\\2</a>",
                "\\1<a href=\"http://www.\\2.\\3\\4\">[url]www.\\2.\\3\\4[/url]</a>",
                "<a href=\"mailto:\\0\">\\0</a>"
            ),
            ' '.$str
        );
    }

    /**
     * 随机产生字符串。
     * @param: string length
     * @return: rand string.
     */
    public static function randStr($len=5, $type="normal")
    {
        switch($type){
        case "num":
            $chars = '0123456789';
            $chars_len = 10;
            break;
        case "lowercase":
            $chars = 'abcdefghijklmnopqrstuvwxyz';
            $chars_len = 26;
            break;
        case "uppercase":
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $chars_len = 26;
            break;
        default:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
            $chars_len = 62;
            break;
        }
        $string = '';
        for($len; $len>=1; $len--)
        {
            $position = rand() % $chars_len;//62 is the length of $chars
            $string .= substr($chars, $position, 1);
        }
        return $string;
    }

    /**
     * 将10进制的数据转换成62进制，要用到GMP函数库。
     * @param $hashString
     * @return 62进制字符串
     **/
    public static function hash10To62($hashString) {
        return gmp_strval(gmp_init($hashString, 10), 62);
    }

    /**
     * 将62进制的数据转换成10进制，要用到GMP函数库。
     * @param $hashString
     * @return 10进制字符串
     **/
    public static function hash62To10($hashString) {
        return gmp_strval(gmp_init($hashString, 62), 10);
    }

    /**
     * 将16进制的数据转换成62进制，要用到GMP函数库。
     * @param $hashString
     * @return 62进制字符串
     **/
    public static function hash16To62($hashString) {
        return gmp_strval(gmp_init($hashString, 16), 62);
    }

    /**
     * 将62进制的数据转换成16进制，要用到GMP函数库。
     * @param $hashString
     * @return 16进制字符串
     **/
    public static function hash62To16($hashString) {
        return gmp_strval(gmp_init($hashString, 62), 16);
    }

    /*
     * Pacakage Replace string
     */
    public static function packageReplaceString($encryptString)
    {
        return preg_replace_callback(
            '/(.)/',
            function($r){
                return str_pad(dechex(ord($r[1])),2,'0',STR_PAD_LEFT);
            },
            $encryptString 
        );
    }

    /*
     * Unpacakage Replace string
     */
    public static function unPackageReplaceString($inString)
    {
        return preg_replace_callback(
            '/(\w{2})/',
            function ($r){
                return chr(hexdec($r[1]));
            },
            self::hash62To16($inString)
        );
    }

    /**
     * 简单的打包URI字符串，不想ID暴露出去。
     * @param $inString
     * @return 打包后的字符串
     **/
    public static function packURIString($inString)
    {
        $encryptString = substr(base64_encode(rand().time()),0,3).base64_encode(NovoStringEncrypt::xxteaEncrypt($inString, md5(ENCRYPT_PUB_KEY)));
        //return self::hash16To62(preg_replace('/(.)/es',"str_pad(dechex(ord('\\1')),2,'0',STR_PAD_LEFT)", $encryptString));
        return self::hash16To62(self::packageReplaceString($encryptString));
    }

    /**
     * 简单的解包字符串
     * @param $inString
     * @return 解包出来的字符串
     **/
    public static function unpackURIString($inString)
    {
        //$inString = base64_decode(substr(preg_replace('/(\w{2})/e',"chr(hexdec('\\1'))", self::hash62To16($inString)), 3));
        $inString = base64_decode(substr(self::unPackageReplaceString($inString), 3));
        return NovoStringEncrypt::xxteaDecrypt($inString,md5(ENCRYPT_PUB_KEY));
    }

    /**
     * 简单的打包Array数组
     * @param $inArray
     * @return 打包后的字符串
     **/
    public static function simplePackArray($inArray)
    {
        $encryptString = substr(base64_encode(rand().time()),0,9).base64_encode(serialize($inArray));
        //return self::hash16To62(preg_replace('/(.)/es',"str_pad(dechex(ord('\\1')),2,'0',STR_PAD_LEFT)", $encryptString));
        return self::hash16To62(self::packageReplaceString($encryptString));
    }

    /**
     * 简单的解包Array数组
     * @param $inString
     * @return 解包出来的Array数组
     **/
    public static function simpleUnpackArray($inString)
    {
        //$inString = base64_decode(substr(preg_replace('/(\w{2})/e',"chr(hexdec('\\1'))", self::hash62To16($inString)), 9));
        $inString = base64_decode(substr(self::unPackageReplaceString($inString), 9));
        return unserialize($inString);
    }

    /**
     * 打包Array数组
     * @param $inArray
     * @return 打包后的字符串
     **/
    public static function packArray($inArray)
    {
        return self::packString(serialize($inArray));
    }

    /**
     * 解包Array数组
     * @param $inString
     * @return 解包出来的Array数组
     **/
    public static function unpackArray($inString)
    {
        return unserialize(self::unpackString($inString));
    }

    /**
     * 打包一个字符串。并且进行urlencode编码。
     * @param $string
     * @return 打包后的字符串
     **/
    public static function packString($str)
    {
        $encode_str = substr(base64_encode(rand().time()),0,6).base64_encode(NovoStringEncrypt::xxteaEncrypt($str,md5(ENCRYPT_PUB_KEY)));
        return self::hash16To62(NovoStringEncrypt::disorganizeString(self::packageReplaceString($encode_str)));
    }

    /**
     * 解包一个字符串。并且进行urldecode解码。
     * @param $string
     * @return 解包后的字符串
     **/
    public static function unpackString($str)
    {
        return NovoStringEncrypt::xxteaDecrypt(base64_decode(substr(preg_replace_callback('/(\w{2})/',function($r){return chr(hexdec($r[1]));},
            NovoStringEncrypt::unDisorganizeString(self::hash62To16($str))),6)),md5(ENCRYPT_PUB_KEY));
    }

    /**
     * 打包Array数组
     * @param $inArray
     * @return 打包后的字符串
     **/
    public static function normalPackArray($inArray)
    {
        $encode_str = NovoStringEncrypt::xxteaEncrypt(serialize($inArray),ENCRYPT_PUB_KEY);
        return NovoStringEncrypt::encryptString(substr(base64_encode(rand().time()),0,9).base64_encode($encode_str), ENCRYPT_PUB_KEY_BAK);
    }

    /**
     * 解包Array数组
     * @param $inString
     * @return 解包出来的Array数组
     **/
    public static function normalUnpackArray($inString)
    {
        return unserialize(NovoStringEncrypt::xxteaDecrypt(base64_decode(substr(NovoStringEncrypt::decryptString($inString, ENCRYPT_PUB_KEY_BAK),9)),ENCRYPT_PUB_KEY));
    }

    /**
     * Base64打包一个数组
     * @param $inArray
     * @return 打包后的字符串
     **/
    public static function iBase64Encode($inArray)
    {
        return base64_encode(serialize($inArray));
    }

    /**
     * Base64解包一个字符串
     * @param $inString
     * @return 解包出来的Array数组
     **/
    public static function iBase64Decode($inString)
    {
        return unserialize(base64_decode($inString));
    }

    /**
     * 将UTF-8编码的字符串转成十六进制用于Ajax传输
     * @param $string
     * @return 编码后的字符串
     **/
    public static function _BIN2HEX($str)
    {
        $arr = @unpack("H*", $str);
        return $arr[1];
    }

    /**
     * 在服务器端将传入的十六进制Ajax内容解码！
     * @param $string
     * @return 解码后的字符串
     **/
    public static function _HEX2BIN($str)
    {
        return @pack("H*", $str);
    }

    /**
     * 创建一个可用的Token串。
     * @param empty
     * @return token
     **/
    public static function createToken()
    {
        $randString = self::randStr(16);
        $hashString = md5(base64_encode(pack('N5', mt_rand(), mt_rand(), mt_rand(), mt_rand(), uniqid())));
        return md5($hashString.$randString.self::getMicrotime().uniqid());
    }

    /**
     * 创建一个带时间戳的Token串。
     * @param empty
     * @return token
     **/
    public static function createTSToken()
    {
        return self::createToken().time();
    }

    /**
     * 取得微秒时间
     * @param NULL
     * @return: float microtime value.
     **/
    public static function getMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * 替换多余的转义
     * @param $str
     * @return string
     **/
    public static function reverseEscape($str)
    {
        $search=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
        $replace=array("\\","\0","\n","\r","\x1a","'",'"');
        return str_replace($search,$replace,$str);
    }

    /**
     * convert ip to int number
     * @Param string $IPAddress
     * @Return int $IPAddressNumber
     */
    public static function ipToInt($IPAddress) {
        $ipArr = explode('.', $IPAddress);
        if (count($ipArr) != 4) return 0;
        $intIP = 0;
        foreach ($ipArr as $k => $v){
            $intIP += (int)$v*pow(256, intval(3-$k));
        }
        return $intIP;
    }

    /*
       check out if the $needle is the start of heystack 
    */
    public static function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /*
       check out if the $needle is the end of heystack 
    */
    public static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        $start  = $length * -1; //negative
        return (substr($haystack, $start) === $needle);
    }

    /*
     * 由生日算年龄
     * @param $birthday;
     * @return int age.
     */
    public static function birthday2age($birthday){
        $timeStamp = time();
        $birthdayTimeStamp = strtotime($birthday);
        $age = date('Y', $timeStamp) - date('Y', $birthdayTimeStamp) - 1;
        if (date('m', $timeStamp) == date('m', $birthdayTimeStamp)){
            if (date('d', $timeStamp) > date('d', $birthdayTimeStamp)){
                $age++;
            }
        }elseif (date('m', $timeStamp) > date('m', $birthdayTimeStamp)){
            $age++;
        }
        return $age;
    }

    /*
     * 将任意日期的值转换成只含年月日的数据
     * @param $timeStr;
     * @return string date.
     */
    public static function strTimeToDate($timeStr){
        $timeStamp = strtotime($timeStr);
        return date("Y-m-d", $timeStamp);
    }

    /*
     * 格式化千分位的数据
     * @param $number $num;
     * @return string number data.
     */
    public static function formatNumber($number, $num){
        return number_format($number, $num, '.', ',');
    }

    /*
     * 转换TEXT格式内容到HTML内容。
     * @param $contents, $showBRTag;
     * @return string $contents.
     */
    public static function Text2HTML($contents, $showBRTag=true)
    {
        $pre_tags = array();

        if (trim($contents) === ''){
            return '';
        }

        // just to make things a little easier, pad the end
        $contents = $contents . "\n";

        if ( strpos($contents, '<pre') !== false ) {
            $contents_parts = explode( '</pre>', $contents );
            $last_pee = array_pop($contents_parts);
            $contents = '';
            $i = 0;

            foreach ( $contents_parts as $contents_part ) {
                $start = strpos($contents_part, '<pre');

                // Malformed html?
                if ( $start === false ) {
                    $contents .= $contents_part;
                    continue;
                }

                $name = "<pre pre-tag-$i></pre>";
                $pre_tags[$name] = substr( $contents_part, $start ) . '</pre>';

                $contents .= substr( $contents_part, 0, $start ) . $name;
                $i++;
            }

            $contents .= $last_pee;
        }

        $contents = preg_replace('|<br />\s*<br />|', "\n\n", $contents);
        // Space things out a little
        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|option|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|noscript|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
        $contents = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $contents);
        $contents = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $contents);
        // cross-platform newlines
        $contents = str_replace(array("\r\n", "\r"), "\n", $contents);
        if ( strpos($contents, '<object') !== false ) {
            // no pee inside object/embed
            $contents = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $contents);
            $contents = preg_replace('|\s*</embed>\s*|', '</embed>', $contents);
        }

        // take care of duplicates
        $contents = preg_replace("/\n\n+/", "\n\n", $contents);
        // make paragraphs, including one at the end
        $contentss = preg_split('/\n\s*\n/', $contents, -1, PREG_SPLIT_NO_EMPTY);
        $contents = '';
        foreach ( $contentss as $tinkle ){
            $contents .= '<p>' . trim($tinkle, "\n") . "</p>\n";
        }
        // under certain strange conditions it could create a P of entirely whitespace
        $contents = preg_replace('|<p>\s*</p>|', '', $contents);
        $contents = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $contents);
        // don't pee all over a tag
        $contents = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $contents);
        // problem with nested lists
        $contents = preg_replace("|<p>(<li.+?)</p>|", "$1", $contents);
        $contents = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $contents);
        $contents = str_replace('</blockquote></p>', '</p></blockquote>', $contents);
        $contents = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $contents);
        $contents = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $contents);
        if ($showBRTag) {
            $contents = preg_replace_callback('/<(script|style).*?<\/\\1>/s', '_autop_newline_preservation_helper', $contents);
            // optionally make line breaks
            $contents = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $contents);
        }
        $contents = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $contents);
        $contents = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $contents);
        $contents = preg_replace( "|\n</p>$|", '</p>', $contents );

        if ( !empty($pre_tags) )
            $contents = str_replace(array_keys($pre_tags), array_values($pre_tags), $contents);

        return $contents;
    }

    /*
     * 重新生成URL
     * @param $parameter, $escape_query_key;
     * @return string $URI.
     * @example:rebuildURI("order=price&aa=bb&cc=dd", "from&pid&sid")
     */
    public static function rebuildURI($parameter, $escape_query_key = "")
    {
        $query_parameter_array = $parameter;
        if(is_string($parameter) && $parameter != ""){
            $query_parameter_array = array();
            $tmp = explode("&", $parameter);
            foreach($tmp as $v){
                $tmp_arr = explode("=", $v);
                $query_parameter_array[$tmp_arr[0]] = $tmp_arr[1];
            }
        }

        $escape_query_key_array = $escape_query_key;
        if(is_string($escape_query_key) && $escape_query_key != "")
        {
            $escape_query_key_array = explode("&", $escape_query_key);
        }

        $url = $_SERVER['REQUEST_URI'];
        $parse = parse_url($url);

        $params = array();
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            if(count($escape_query_key_array) != 0){
                foreach($escape_query_key_array as $v){
                    unset($params[$v]);
                }
            }
        }
        $params = array_merge($params, $query_parameter_array);
        $url = $parse['path'].'?'.http_build_query($params);
        return $url;
    }

    /*
     * 判断是否为URL
     * @param string $url;
     * @return int 0|1.
     */
    public static function isURL($url){
        return preg_match('/^(http|https|ftp):\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/', $url);
    }

    /*
     * 判断是否为手机号码
     * @param int $mobileNumber;
     * @return int 0|1.
     */
    public static function isMobileNumber($mobileNumber){
        return preg_match('/^((\(\d{3}\))|(\d{3}\-))?13\d{9}$/', $mobileNumber);
    }

    /*
     * 删除URI里的空格
     * @param string $str;
     * @return string $str;
     */
    public static function trimURIString(&$str) 
    { 
        $str = preg_replace('/\s+/u','', trim($str));
    }

    /*
     * 切换里的Mimes头。
     * @param string $str;
     * @return NULL;
     */
    public static function switchMimesHeader($requestDataType)
    {
        switch($requestDataType)
        {
            case "json":
                header('Cache-Control: no-cache, must-revalidate');
                header("Content-Type:application/json; charset=UTF-8");
                break;
            case "txt":
                header("Content-Type:text/plain; charset=UTF-8");
                break;
            case "shtml":
                header("Content-Type:text/html; charset=UTF-8");
                break;
            default:
                header("Content-Type:text/html; charset=UTF-8");
                break;
        }
    }

    /*
     * 解JSON字符串到Array数组
     * @param string $str;
     * @return Array;
     */
    public static function decodeJSONString($str)
    {
        return json_decode($str, TRUE);
    }

    /*
     * 打包并输出JSON字符串
     * @param string $Array;
     * @return NULL;
     */
    public static function echoJSONData($array)
    {
        $jsonString = json_encode($array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if(!$jsonString){
            echo "Generate JSON Failed - Illegal key, value pair.";
            exit;
        }
        self::switchMimesHeader("json");
        echo $jsonString;
    }
}
