<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: BaseMemcached.class.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang$
 * @lastmodified         $Date: 2013/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 通用Memcached类。
 **/
class BaseMemcached
{
    private $MemcachedConn = null;
    private $NameSpace = null;
    private $DataVersion = 1;

    public function __construct($MemcacheServer, $NameSpace)
    {
        if(!class_exists('Memcached')){
            $this->MemcachedConn = false;
            return;
        }

        $this->MemcachedConn = new Memcached();

        if(count($MemcacheServer) > 1)
        {
            $MemcacheServerArr = array();

            foreach($MemcacheServer as $value)
            {
                array_push($MemcacheServerArr, array_values($value));
            }
            $this->MemcachedConn->addServers($MemcacheServerArr);
        } else {
            $this->MemcachedConn->addServer($MemcacheServer[0]['host'],$MemcacheServer[0]['port'], $MemcacheServer[0]['weight']);
        }
        $this->NameSpace = $NameSpace;
        //默认一个版本，各业务模块可以在生成对象后执行setDataVersion覆盖
        $this->setDataVersion();
    }

    /***
     * 检查Memcached是否连接成功
     * @return bool true成功，false 失败
     */
    public function checkStatus(){
        $memStatus = $this->MemcachedConn->getStats();
        if(empty($memStatus)){
            return false;
        }else{
            return true;
        }
    }


    /***
     * 添加缓存
     * @param $key key值
     * @param $value 缓存数据
     * @param $flag 0为MEMCACHE_COMPRESSED 1 为
     */
    public function setCache($key, $value, $expire=3600, $compress=false)
    {
        if(!$this->MemcachedConn){
            return;
        }
        $key = md5($key);
        $data = $this->MemcachedConn->get($key);
        if(empty($data)){
            if($compress){
                $this->MemcachedConn->setOption(Memcached::OPT_COMPRESSION, true);
                return $this->MemcachedConn->set($this->NameSpace.'_'.$this->DataVersion.'_'.$key, $value, $expire);
            }else{
                return $this->MemcachedConn->set($this->NameSpace.'_'.$this->DataVersion.'_'.$key, $value,  $expire);
            }
        }
    }

    /***
     * 获取缓存
     * @param $key值
     * @reutrn 缓存数据
     */
    public function getCache($key)
    {
        if(!$this->MemcachedConn){
            return;
        }

        $key = md5($key);
        return $this->MemcachedConn->get($this->NameSpace.'_'.$this->DataVersion.'_'.$key);
    }


    /***
     * 删除缓存
     * @param $key key值
     * @reutrn 缓存数据
     */
    public function deleteCache($key)
    {
        if(!$this->MemcachedConn){
            return;
        }
        $key = md5($key);
        return $this->MemcachedConn->delete($this->NameSpace.'_'.$this->DataVersion.'_'.$key);
    }


    /***
     * 将一个数值元素增加参数offset指定的大小
     * @param $key key值
     * @param $value value值
     * @reutrn 缓存数据
     */
    public function incrementCache($key, $offset=1)
    {
        if(!$this->MemcachedConn){
            return;
        }
        $key = md5($key);
        return $this->MemcachedConn->increment($this->NameSpace.'_'.$this->DataVersion.'_'.$key, $offset);
    }

    /**
     * 减小一个数值元素的值，减小多少由参数offset决定
     * @param $key key值
     * @param $value value值
     * @reutrn 缓存数据
     */
    public function decrementCache($key, $offset=1)
    {
        if(!$this->MemcachedConn){
            return;
        }
        $key = md5($key);
        return $this->MemcachedConn->decrement($this->NameSpace.'_'.$this->DataVersion.'_'.$key, $offset);
    }


    /**
     * 刷新缓存 根据$moduleName按模块批量刷新
     * @reutrn NULL
     */
    public function flushCache($ModuleName = "")
    {
        if(!$this->MemcachedConn){
            return;
        }
        $version_key = 'version_'.$this->NameSpace;
        if ($ModuleName != "")
        {
            $version_key .= '_'.$ModuleName;
        }
        $this->MemcachedConn->increment($version_key, 1);
    }

    /***
     * 按业务定制版本，方便批量刷新
     * @param ModuleName
     */
    public function setDataVersion($ModuleName = "")
    {
        $version_key = 'version_'.$this->NameSpace;
        if ($ModuleName != "")
        {
            $version_key .= '_'.$ModuleName;
        }

        $this->DataVersion = $this->MemcachedConn->get($version_key);
        if (empty($this->DataVersion))
        {
            $this->MemcachedConn->set($version_key, 1);
            $this->DataVersion = 1;
        }
    }

}
