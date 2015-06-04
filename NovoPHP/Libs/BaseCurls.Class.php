<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: BaseCurls.Class.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2013/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 基础CURL文件。
**/
class BaseCurls
{
    /**
     * 当前curl对话
     * @var string
     */
    private $_ch;

    /**
     * 当前发送的地址
     * @var string
     */
    private $_url;

    /**
     * 调试信息
     * @var string
     */
    private $_debugInfo;

    /**
     * 返回是否包括header头
     * @var int
     */
    private $_header = 0;



    /**
     * 构造函数
     *
     * @access public
     * @param string $url 请求地址
     * @return void
     */
    public function __construct($url)
    {
        $this->_url = $url;
    }

    /**
     * 析构函数 关闭句柄
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->close();
    }


    /**
     * 设定返回是否包括header头信息
     *
     * @param int $header 1-返回头信息，0不返回
     * @return void
     * @access public
     */
    public function setHeader($header = 0)
    {
        $this->_header = $header;
    }

    /**
     * 初始化curl对话
     *
     * @param void
     * @access private
     * @return boolean
     */
    private function _init()
    {	
        $this->_ch = @curl_init();
        if (!$this->_ch)
        {
            return false;
        }
        $this->_basic();
        return true;
    }

    /**
     * 基本选项
     *
     * @return void
     * @access private
     */
    private function _basic()
    {
        curl_setopt($this->_ch, CURLOPT_URL, $this->_url);
        curl_setopt($this->_ch, CURLOPT_HEADER, $this->_header);
    }

    /**
     * 设置选项
     *
     * @param array $options
     * @access public
     * @return void
     */
    public function setOptions($options = array())
    {
        if (is_array($options))
        {
            foreach ($options as $key => $value)
            {
                $this->$key = $value;
            }
        }
        //如果HTTP返回大于300, 是否显示错误
        if (isset($this->onerror) && $this->onerror)
        {
            curl_setopt($this->_ch, CURLOPT_FAILONERROR, 1);
        }

        //是否有返回值
        if (isset($this->return) && $this->return == true && !isset($this->file))
        {
            curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, 1);
        }

        //HTTP 认证
        if (isset($this->username) && $this->username != "")
        {
            curl_setopt($this->_ch, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
        }

        //SSL 检查
        if (isset($this->sslVersion))
        {
            curl_setopt($this->_ch, CURLOPT_SSLVERSION, $this->sslVersion);
        }
        if (isset($this->sslCert))
        {
            curl_setopt($this->_ch, CURLOPT_SSLCERT, $this->sslCert);
        }
        if (isset($this->sslCertPasswd))
        {
            curl_setopt($this->_ch, CURLOPT_SSLCERTPASSWD, $this->sslCertPasswd);
        }

        //代理服务器
        if (isset($this->proxy))
        {
            curl_setopt($this->_ch, CURLOPT_PROXY, $this->proxy);
        }
        if (isset($this->proxyUser) || isset($this->proxyPassword))
        {
            curl_setopt($this->_ch, CURLOPT_PROXYUSERPWD, "{$this->proxyUser}:{$this->proxyPassword}");
        }

        //传输类型
        if (isset($this->type))
        {
            switch (strtolower($this->type))
            {
            case "post":
                curl_setopt($this->_ch, CURLOPT_POST, 1);
                break;
            case "put":
                curl_setopt($this->_ch, CURLOPT_PUT, 1);
                break;
            }
        }

        //上传相关
        if (isset($this->file))
        {
            if (!isset($this->filesize))
            {
                $this->filesize = filesize($this->file);
            }
            curl_setopt($this->_ch, CURLOPT_INFILE, $this->file);
            curl_setopt($this->_ch, CURLOPT_INFILESIZE, $this->filesize);
            curl_setopt($this->_ch, CURLOPT_UPLOAD, 1);
        }

        //数据发送
        if (isset($this->fields))
        {
            if (!is_array($this->fields))
            {
                if (!isset($this->type))
                {
                    $this->type = "post";
                    curl_setopt($this->_ch, CURLOPT_POST, 1);
                }
                curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $this->fields);
            }
            else
            {
                if (!empty($this->fields))
                {
                    $p = array();
                    foreach ($this->fields as $key=>$value)
                    {
                        $p[] = $key . "=" . urlencode($value);
                    }
                    if (!isset($this->type))
                    {
                        $this->type = "post";
                        curl_setopt($this->_ch, CURLOPT_POST, 1);
                    }
                    curl_setopt($this->_ch, CURLOPT_POSTFIELDS, implode("&", $p));
                }
            }
        }

        //错误相关
        if (isset($this->progress) && $this->progress == true)
        {
            curl_setopt($this->_ch, CURLOPT_PROGRESS, 1);
        }
        if (isset($this->verbose) && $this->verbose == true)
        {
            curl_setopt($this->_ch, CURLOPT_VERBOSE, 1);
        }
        if (isset($this->mute) && !$this->mute)
        {
            curl_setopt($this->_ch, CURLOPT_MUTE, 0);
        }

        //其它相关
        if (isset($this->followLocation))
        {
            curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, $this->followLocation);
        }
        if (isset($this->timeout) && $this->timeout>0)
        {
            curl_setopt($this->_ch, CURLOPT_TIMEOUT, $this->timeout);
        }
        else
        {
            curl_setopt($this->_ch, CURLOPT_TIMEOUT, 20);
        }
        if (isset($this->connecttimeout) && $this->connecttimeout>0)
        {
            curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        }
        else
        {
            curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, 5);
        }
        if (isset($this->userAgent))
        {
            curl_setopt($this->_ch, CURLOPT_USERAGENT, $this->userAgent);
        }

        //cookie 相关
        if (isset($this->cookie))
        {
            $cookieData = "";
            foreach ($this->cookie as $name => $value)
            {
                $cookieData .= urlencode($name) . "=" . urlencode($value) . ";";
            }
            curl_setopt($this->_ch, CURLOPT_COOKIE, $cookieData);
        }


        //设置端口
        if (isset($this->port))
        {
            curl_setopt($this->_ch, CURLOPT_PORT, $this->port);
        }

        //前向连接
        if (isset($this->referer))
        {
            curl_setopt($this->_ch, CURLOPT_REFERER, $this->referer);
        }

        //Accept-Encoding的值
        if (isset($this->encoding))
        {
            curl_setopt($this->_ch, CURLOPT_ENCODING, $this->encoding);
        }

        //自定义http头
        if (isset($this->httpHeaders))
        {
            curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $this->httpHeaders );
        }
    }

    /**
     * 设置返回结果选项，并返回结果
     *
     * @param void
     * @access public
     * @return string 返回结果
     */
    public function getResult()
    {
        $result = curl_exec($this->_ch);
        return $result;
    }

    /**
     * 关闭当前curl对话
     *
     * @return void
     * @access public
     */
    public function close()
    {
        @curl_close($this->_ch);
    }

    /**
     * 得到对话中产生的错误描述
     *
     * @return string 错误描述
     * @access public
     */
    public function getError()
    {
        return curl_error($this->_ch);
    }

    /**
     * 得到对话中产生的错误号
     *
     * @return integer 错误号
     * @access public
     */
    public function getErrno()
    {
        return curl_errno($this->_ch);
    }

    /**
     * 中断执行，并输出错误信息
     *
     * @param string $msg 错误信息
     * @return void
     * @access private
     */
    private function _halt($msg)
    {
        $message = "\n<br>信息:{$msg}";
        $message .= "\n<br>错误号:".$this->getErrno();
        $message .= "\n<br>错误:".$this->getError();
        echo $message;
        exit;
    }

    /**
     * 调试信息
     *
     * @return void
     * @access private
     */
    private function _debug()
    {
        $message .= "\n<br>错误号:".$this->getErrno();
        $message .= "\n<br>错误:".$this->getError();
        $this->_debugInfo = $message;
    }

    /**
     * 获得以POST方式发送的结果
     *
     * @param array/string $fields 发送的数据
     * @return string 返回的结果
     * @access public
     */
    public function post($fields = array(), $setoption = array())
    {
        //默认参数
        $setoption['type'] = "post";
        $setoption['fields'] = $fields;
        if(!isset($setoption['return']))
        {
            $setoption['return'] = 1;
        }
        if(!isset($setoption['onerror']))
        {
            $setoption['onerror'] = 1;
        }

        if ($this->_init())
        {
            $this->setOptions($setoption);
            $result = $this->getResult();
            //$this->close();
            return $result;
        }
        else
        {
            return false;
        }
    }

    /**
     * 获得以GET方式发送的结果
     *
     * @param array $option
     * @return string 返回的结果
     * @access public
     */
    public function get($setoption = array())
    {
        //默认参数
        $setoption['return'] = 1;
        $setoption['onerror'] = 1;
        if($this->_init()){
            $this->setOptions($setoption);
            $result = $this->getResult();
            //$this->close();
            //返回数据
            return $result;
        }
        else
        {
            //设置错误信息
            return false;
        }
    }

    /**
     * 静态调用，获得以COOKIE方式发送的结果
     *
     * @param string $url 发送的地址
     * @param array/string $fields 发送的数据
     * @return string 返回的结果
     * @access public
     */
    public function cookie($fields = array(), $setoption = array())
    {
        //默认参数
        $setoption['cookie'] = $fields;
        $setoption['return'] = 1;
        $setoption['onerror'] = 1;

        if ($this->_init())
        {
            $this->setOptions($setoption);
            $result = $this->getResult();
            //$this->close();
            return $result;
        }
        else
        {
            return false;
        }
    }


    /**
     * 静态调用，获得以FILE方式发送的结果
     * @param stream resource $file 发送的文件流
     * @param integer $filesize 发送的文件流的大小
     * @param array $setOption 参数设定
     * @return string 返回的结果
     * @access public
     */
    private function file($file, $fileSize, $setoption = array())
    {
        //默认参数
        $setoption['type'] = "put";
        $setoption['file'] = $fields;
        $setoption['filesize'] = $fileSize;
        $setoption['return'] = 1;
        $setoption['onerror'] = 1;

        if ($this->_init())
        {
            $this->setOptions($setoption);
            $result = $this->getResult();
            //$this->close();
            return $result;
        }
        else
        {
            return false;
        }
    }
}
