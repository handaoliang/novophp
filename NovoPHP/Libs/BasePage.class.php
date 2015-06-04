<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: BasePage.class.php,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2013/04/10 12:02:11 $
 * @copyright            Copyright (c) 2013, Comnovo Inc.
**/
/**
 * 基础分页类，用于分页用。
**/
class BasePage
{
    public     $first_row;        //起始行数
    public     $list_rows;        //列表每页显示行数
    public     $total_pages;      //总页数
    public     $now_page;         //当前页数
    protected  $total_rows;       //总行数
    protected  $method  = 'defalut'; //处理情况 Ajax分页 Html分页(静态化时) 普通get方式
    protected  $parameter = '';
    //希望从URL中剔除掉的query key
    //例如：from=baidu_com，如果希望在分页中剔除掉，只需要加上："escape_query_key=array('from')"即可
    protected  $escape_query_key = array();
    protected  $page_name;        //分页参数的名称
    protected  $ajax_func_name;
    public     $plus = 3;         //分页偏移量
    protected  $url;

    /**
     * 构造函数
     * @param unknown_type $data
     */
    public function __construct($data = array())
    {
        $this->total_rows = $data['total_rows'];

        $this->parameter = !empty($data['parameter']) ? $data['parameter'] : '';
        $this->escape_query_key = !empty($data['escape_query_key']) ? $data['escape_query_key'] : array();
        $this->list_rows = !empty($data['list_rows']) && $data['list_rows'] <= 100 ? $data['list_rows'] : 20;
        $this->total_pages = ceil($this->total_rows / $this->list_rows);
        $this->page_name = !empty($data['page_name']) ? $data['page_name'] : 'p';
        $this->ajax_func_name = !empty($data['ajax_func_name']) ? $data['ajax_func_name'] : '';
        $this->method = !empty($data['method']) ? $data['method'] : '';

        /* 当前页面 */
        if(!empty($data['now_page']))
        {
            $this->now_page = intval($data['now_page']);
        }else{
            $this->now_page = !empty($_GET[$this->page_name]) ? intval($_GET[$this->page_name]):1;
        }
        $this->now_page = $this->now_page <= 0 ? 1 : $this->now_page;

        if(!empty($this->total_pages) && $this->now_page > $this->total_pages)
        {
            $this->now_page = $this->total_pages;
        }
        $this->first_row = $this->list_rows * ($this->now_page - 1);
    }

    /**
     * 得到当前连接
     * @param $page
     * @param $text
     * @return string
     */
    protected function _get_link($page,$text)
    {
        switch ($this->method) {
            case 'ajax':
                $parameter = '';
                if($this->parameter)
                {
                    $parameter = ','.$this->parameter;
                }
                return '<a onclick="' . $this->ajax_func_name . '(\'' . $page . '\''.$parameter.')" href="javascript:void(0)">' . $text . '</a>' . "\n";
            break;

            case 'html':
                $url = str_replace('?', $page, $this->parameter);
                return '<a href="' .$url . '">' . $text . '</a>' . "\n";
            break;
            case 'classevent':
                return "<a href='javascript:;' class='condition_link' id='page_{$page}'>{$text}</a>"."\n";
            break;
            default:
                return '<a href="' . $this->_get_url($page) . '">' . $text . '</a>' . "\n";
            break;
        }
    }

    /**
     * 设置当前页面链接
     */
    protected function _set_url()
    {
        $url = $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$this->page_name]);

            if($this->escape_query_key != ""){
                foreach($this->escape_query_key as $v){
                    unset($params[$v]);
                }
            }

            $url = $parse['path'].'?'.http_build_query($params);
        }

        if(!empty($params))
        {
            $url .= '&';
        }
        $this->url = $url;
    }

    /**
     * 得到$page的url
     * @param $page 页面
     * @return string
     */
    protected function _get_url($page)
    {
        if($this->url === NULL)
        {
            $this->_set_url();
        }
    //  $lable = strpos('&', $this->url) === FALSE ? '' : '&';
        return $this->url . $this->page_name . '=' . $page;
    }

    /**
     * 得到第一页
     * @return string
     */
    public function first_page($name = '第一页')
    {
        if($this->now_page > 5)
        {
            return $this->_get_link('1', $name);
        }
        return '';
    }

    /**
     * 最后一页
     * @param $name
     * @return string
     */
    public function last_page($name = '最后一页')
    {
        if($this->now_page < $this->total_pages - 5)
        {
            return $this->_get_link($this->total_pages, $name);
        }
        return '';
    }

    /**
     * 上一页
     * @return string
     */
    public function up_page($name = '上一页')
    {
        if($this->now_page != 1)
        {
            return $this->_get_link($this->now_page - 1, $name);
        }
        return '';
    }

    /**
     * 下一页
     * @return string
     */
    public function down_page($name = '下一页')
    {
        if($this->now_page < $this->total_pages)
        {
            return $this->_get_link($this->now_page + 1, $name);
        }
        return '';
    }

    /**
     * 分页样式输出
     * @param $param
     * @return string
     */
    public function show($param = 1)
    {
        if($this->total_rows < 1)
        {
            return '';
        }

        $className = 'show_' . $param;

        $classNames = get_class_methods($this);

        if(in_array($className, $classNames))
        {
            return $this->$className();
        }
        return '';
    }

    protected function show_2()
    {
        if($this->total_pages != 1)
        {
            $return = '';
            $return .= $this->up_page('上一页');
            for($i = 1;$i<=$this->total_pages;$i++)
            {
                if($i == $this->now_page)
                {
                    $return .= "<a class='current'>$i</a>\n"; //now_page
                }
                else
                {
                    if($this->now_page-$i>=4 && $i != 1)
                    {
                        $return .="<span class='page_more'>...</span>\n";
                        $i = $this->now_page-3;
                    }
                    else
                    {
                        if($i >= $this->now_page+5 && $i != $this->total_pages)
                        {
                            $return .="<span class='page_more'>...</span>\n";
                            $i = $this->total_pages;
                        }
                        $return .= $this->_get_link($i, $i) . "\n";
                    }
                }
            }
            $return .= $this->down_page('下一页');
            return $return;
        }
    }

    protected function show_1()
    {
        $plus = $this->plus;
        if( $plus + $this->now_page > $this->total_pages)
        {
            $begin = $this->total_pages - $plus * 2;
        }else{
            $begin = $this->now_page - $plus;
        }

        $begin = ($begin >= 1) ? $begin : 1;
        $return = '';
        $return .= $this->first_page();
        $return .= $this->up_page();
        for ($i = $begin; $i <= $begin + $plus * 2;$i++)
        {
            if($i>$this->total_pages)
            {
                break;
            }
            if($i == $this->now_page)
            {
                $return .= "<a class='now_page'>$i</a>\n";
            }
            else
            {
                $return .= $this->_get_link($i, $i) . "\n";
            }
        }
        $return .= $this->down_page();
        $return .= $this->last_page();
        return $return;
    }

    protected function show_3()
    {
        $plus = $this->plus;
        if( $plus + $this->now_page > $this->total_pages)
        {
            $begin = $this->total_pages - $plus * 2;
        }else{
            $begin = $this->now_page - $plus;
        }
        $begin = ($begin >= 1) ? $begin : 1;
        $return = '总计 ' .$this->total_rows. ' 个记录分为 ' .$this->total_pages. ' 页, 当前第 ' . $this->now_page . ' 页 ';
        $return .= ',每页 ';
        $return .= '<input type="text" value="'.$this->list_rows.'" id="pageSize" size="3"> ';
        $return .= $this->first_page()."\n";
        $return .= $this->up_page()."\n";
        $return .= $this->down_page()."\n";
        $return .= $this->last_page()."\n";
        $return .= '<select onchange="'.$this->ajax_func_name.'(this.value)" id="gotoPage">';

        for ($i = $begin;$i<=$begin+10;$i++)
        {
            if($i>$this->total_pages)
            {
                break;
            }
            if($i == $this->now_page)
            {
                $return .= '<option selected="true" value="'.$i.'">'.$i.'</option>';
            }
            else
            {
                $return .= '<option value="' .$i. '">' .$i. '</option>';
            }
        }
         $return .= '</select>';
        return $return;
    }

    protected function show_4()
    {
        if($this->total_pages != 1)
        {
            $return = '';
            $return .= $this->up_page('上一页');
            for($i = 1;$i<=$this->total_pages;$i++)
            {
                if($i == $this->now_page)
                {
                    $return .= "<a class='current'>$i</a>\n"; //now_page
                }
                else
                {
                    if($this->now_page-$i>=4 && $i != 1)
                    {
                        $return .="<span class='page_more'>...</span>\n";
                        $i = $this->now_page-3;
                    }
                    else
                    {
                        if($i >= $this->now_page+5 && $i != $this->total_pages)
                        {
                            $return .="<span class='page_more'>...</span>\n";
                            $i = $this->total_pages;
                        }
                        $return .= $this->_get_link($i, $i) . "\n";
                    }
                }
            }
            $return .= $this->down_page('下一页');
            return $return;
        }
    }

    protected function show_5()
    {
        if($this->total_pages != 1)
        {
            $return = '';
            $return .= $this->up_page('上一页');
            for($i = 1;$i<=$this->total_pages;$i++)
            {
                if($i == $this->now_page)
                {
                    $return .= "<a href='javascript:;' class='cur condition_link' id='page_{$i}'>$i</a>\n"; //now_page
                }
                else
                {
                    if($this->now_page-$i>=4 && $i != 1)
                    {
                        $return .="<span class='page_more'>...</span>\n";
                        $i = $this->now_page-3;
                    }
                    else
                    {
                        if($i >= $this->now_page+5 && $i != $this->total_pages)
                        {
                            $return .="<span class='page_more'>...</span>\n";
                            $i = $this->total_pages;
                        }
                        $return .= $this->_get_link($i, $i) . "\n";
                    }
                }
            }
            $return .= $this->down_page('下一页');
            return $return;
        }
    }


    /**
     * 为Bootstrap定制的一个方法。生成想要的分页。
     */
    protected function show_6()
    {
        if($this->total_pages != 1)
        {
            $return = '<a href='.$this->_get_url(1).' class="first ui-button"><i class="icon-fast-backward"></i></a>';
            if($this->now_page != 1)
            {
                $return .= '<a class="previous ui-button" href="'.$this->_get_url($this->now_page-1).'"><i class="icon-backward"></i></a>';
            }
            $return .= '<span>';
            for($i = 1;$i<=$this->total_pages;$i++)
            {
                if($i == $this->now_page)
                {
                    $return .= '<a class="ui-button ui-state-disabled current">'.$i.'</a>';
                } else {
                    if($this->now_page-$i>=4 && $i!=1)
                    {
                        $return .= '<span class="ui-button">...</span>';
                        $i = $this->now_page-3;
                    } else {
                        if($i >= $this->now_page+5 && $i != $this->total_pages)
                        {
                            $return .= '<span class="ui-button more_page">...</span>';
                            $i = $this->total_pages;
                        }
                        $return .= '<a class="ui-button" href="'.$this->_get_url($i).'">'.$i.'</a>';
                    }
                }
            }
            $return .= '</span>';
            
            if($this->now_page < $this->total_pages)
            {
                $return .= '<a class="next ui-button" href="'.$this->_get_url($this->now_page+1).'"><i class="icon-forward"></i></a>';
            }
            $return .= '<a href="'.$this->_get_url($this->total_pages).'" class="last ui-button"><i class="icon-fast-forward"></i></a>';
            return $return;
        }
    }

}
