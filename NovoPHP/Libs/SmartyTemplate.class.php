<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: SmartyTemplate.class.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2013/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 自建smarty类，用于模板应用接口。当前应用的smarty版本是：Smarty 3.1.13 Released Jan 15th, 2013
 * 随时升级smarty版本，以免因smarty而产生系统漏洞。
 * 此自定义smarty类里面，把smarty语法从{}改成了用{{}}引用，
 * 因此，如果在VIM中使用smarty语法高亮，需要修改:$VIM_PATH/share/syntax/smarty.vim。
 * 并将{}改成{{}}。注：在VIM中，倘模板文件的后缀为.tpl，则会自适应smarty语法高亮。
**/

if(!defined('NOVOPHP_VENDORS_DIR')){
	echo "NOVOPHP_VENDORS_DIR is not defined,Error File:MySmarty.class.php";
	exit;
}
require_once NOVOPHP_VENDORS_DIR."/Smarty/Smarty.class.php";

class SmartyTemplate extends Smarty {
    /**
     * 类构造函数。
     **/
    function __construct() {
        parent::__construct();
        $this->setTemplateDir(APPS_VIEWS_DIR);
        $this->setCompileDir(SMARTY_TEMPLATES_C);
        $this->setCacheDir(SMARTY_CACHE_DIR);
        $this->setConfigDir(APPS_CONFIGS_DIR);

        $this->caching_type = SMARTY_CACHING_TYPE;
        $this->debugging = SMARTY_DEBUGGING;
        $this->caching = SMARTY_ALLOW_CACHE;
        $this->cache_lifetime = SMARTY_CACHE_TIME;

        $this->left_delimiter="{{";
        $this->right_delimiter="}}";
    }

    /*
     * 函数名：setNewTemplateDir();
     * 功　能：用于重新设置模板路径！
     * 参　数：无
     * 返　回：无
     */
    function setNewTemplateDir($curTemplateDir)
    {
        $this->template_dir = $curTemplateDir;
    }
}

class Smarty_CacheResource_Memcache extends Smarty_CacheResource_KeyValueStore
{
    /**
     * memcache instance
     *
     * @var Memcache
     */
    protected $memcache = null;

    public function __construct()
    {
        $this->memcache = new Memcache();
        $this->memcache->addServer(SMARTY_CACHE_MEMCACHE_ADDRESS, SMARTY_CACHE_MEMCACHE_PORT);
    }

    /**
     * Read values for a set of keys from cache
     *
     * @param  array $keys list of keys to fetch
     *
     * @return array   list of values with the given keys used as indexes
     * @return boolean true on success, false on failure
     */
    protected function read(array $keys)
    {
        $_keys = $lookup = array();
        foreach ($keys as $k) {
            $_k = sha1($k);
            $_keys[] = $_k;
            $lookup[$_k] = $k;
        }
        $_res = array();
        $res = $this->memcache->get($_keys);
        foreach ($res as $k => $v) {
            $_res[$lookup[$k]] = $v;
        }

        return $_res;
    }

    /**
     * Save values for a set of keys to cache
     *
     * @param  array $keys   list of values to save
     * @param  int   $expire expiration time
     *
     * @return boolean true on success, false on failure
     */
    protected function write(array $keys, $expire = null)
    {
        foreach ($keys as $k => $v) {
            $k = sha1($k);
            $this->memcache->set($k, $v, 0, $expire);
        }

        return true;
    }

    /**
     * Remove values from cache
     *
     * @param  array $keys list of keys to delete
     *
     * @return boolean true on success, false on failure
     */
    protected function delete(array $keys)
    {
        foreach ($keys as $k) {
            $k = sha1($k);
            $this->memcache->delete($k);
        }

        return true;
    }

    /**
     * Remove *all* values from cache
     *
     * @return boolean true on success, false on failure
     */
    protected function purge()
    {
        $this->memcache->flush();
    }
}
