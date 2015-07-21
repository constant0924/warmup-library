<?php
namespace library\util;
/**
 * HTML整理
 * 自定义HTML允许使用的标签和属性
 * 变量$allow_tag说明
 * 键为标签名 值是允许使用的属性 *代表全部运行 如果不允许使用属性配置NULL或者空
 * 需要支持更多html标签$allow_tag设置
 * 需要统一支持某个属性$allow_public_attribute配置
 * 此功能需要服务器支持tidy模块
 * TidyHtmlUtil::filter($html) 成功返回处理后的数据 如果不支持或者失败返回原数据
 *
 */
use \library\logger\Logger;
use tidyNode;
class TidyHtmlUtil {

    /**
     * 需要处理的HTML
     *
     * @var array
     */
    public static  $tree_html = '';

    /**
     * 单标签HTML
     *
     * @var array
     */
    public static $single_tag = array('img');

    /**
     * 公共允许使用的的属性
     * @var array
     */
    public static $allow_public_attribute = array(
        'align'
    );

    /**
     * 允许使用的标签
     *
     * @var array
     */
    public static $allow_tag = array(
        'a' => array('href'),
        'b' =>[],
        'i' =>[],
        'u' =>[],
        'p' =>[],
        'ol' =>[],
        'li' =>[],
        'ul' =>[],
        'br' =>[],
        'img' => array('src'),
        'div' => array('align'),
        'span' => array('style')
    );



    /**
     * 过滤HTML
     * @param string $html html
     * @return string
     */
    public static function filter($html,$handler='clean') {
        // 服务器需要支持tidy
        if ( ! function_exists('tidy_parse_string')) {
            return $html;
        } else {
            $html = @tidy_repair_string($html,array(), 'UTF8');
            $tree_html = @tidy_parse_string($html,array(),'UTF8');
            self::$tree_html = $tree_html->body()->child;
        }
        self::$tree_html = self::$handler(self::$tree_html);
        return self::$tree_html;
    }

    /**
     * @param array $data
     * @return string
     */
    public static function format(Array $data) {
        $new_data = '';
        if ($data) {
            foreach($data as $r) {

                if($r->isText() && !empty($r->value) && (!$r->isPhp() || $r->isJste() || !$r->isAsp()  ))
                {
                    $new_data .= '<p class="p-class">'.$r->value.'</p>';
                }

                if($r->name=='br')
                {
                    $new_data .='<br/>';
                }

                if($r->name=='img')
                {
                    $new_data .= '<p class="img-class"><'.$r->name;
                    if ($r->attribute && self::$allow_tag[$r->name] != 'none') {
                        foreach ($r->attribute as $key => $value) { // 属性
                            if (self::$allow_tag[$r->name] == '*') {
                                $new_data .= ' '.$key.'="'.$value.'"';
                            } elseif ((is_array(self::$allow_tag[$r->name]) && in_array($key, self::$allow_tag[$r->name]))
                                || in_array($key, self::$allow_public_attribute)) { // 允许使用的属性
                                $new_data .= ' '.$key.'="'.$value.'"';
                            }
                        }
                    }
                    $new_data .= '/></p>';

                }
                if ($r->child) {
                    $new_data .= self::format($r->child);
                }
            }
        }
        return $new_data;
    }


    /**
     * 只保留被允许的标签
     * @param $data
     * @return string
     */
    public static function clean($data) {
        $new_data = '';
        if ($data) {
            foreach($data as $r) {
                if ($r->name) { // 标签名
                    if (isset(self::$allow_tag[$r->name])) { // 是否允许使用的标签
                        $new_data .= '<'.$r->name;
                        if ($r->attribute && self::$allow_tag[$r->name] != 'none') {
                            foreach ($r->attribute as $key => $value) { // 属性
                                if (self::$allow_tag[$r->name] == '*') {
                                    $new_data .= ' '.$key.'="'.$value.'"';
                                } elseif ((is_array(self::$allow_tag[$r->name]) && in_array($key, self::$allow_tag[$r->name]))
                                    || in_array($key, self::$allow_public_attribute)) { // 允许使用的属性
                                    $new_data .= ' '.$key.'="'.$value.'"';
                                }
                            }
                        }
                        if (in_array($r->name, self::$single_tag)) { // 单标签
                            $new_data .= ' /';
                        }
                        $new_data .= '>';
                    }
                } else { // 标签内的值
                    $new_data .= trim($r->value);
                }

                if ($r->child) {
                    $new_data .= self::clean($r->child);
                }
                if ($r->name && isset(self::$allow_tag[$r->name])) {
                    if (in_array($r->name, self::$single_tag) === FALSE) { // 不是单标签
                        $new_data .= '</'.$r->name.'>';
                    }
                }
            }
        }
        return $new_data;
    }

    public static function qiniuImageResize($data)
    {
        $new_data = '';
        if ($data) {
            foreach($data as $r) {
                if($r->isText() && !empty($r->value) && (!$r->isPhp() || $r->isJste() || !$r->isAsp()  ))
                {
                    $new_data .= '<p class="p-class">'.$r->value.'</p>';
                }
                if($r->name=='img')
                {
                    $new_data .= '<p class="img-class"><'.$r->name;
                    if ($r->attribute && self::$allow_tag[$r->name] != 'none') {
                        foreach ($r->attribute as $key => $value) { // 属性
                            if (self::$allow_tag[$r->name] == '*') {
                                $new_data .= ' '.$key.'="'.$value.'"';
                            } elseif ((is_array(self::$allow_tag[$r->name]) && in_array($key, self::$allow_tag[$r->name]))
                                || in_array($key, self::$allow_public_attribute)) { // 允许使用的属性
                                if($key=='src')
                                {
                                    $value .='?imageView2/2/w/800';
                                }
                                $new_data .= ' '.$key.'="'.$value.'"';
                            }
                        }
                    }
                    $new_data .= '/></p>';

                }
                if ($r->child) {
                    $new_data .= self::qiniuImageResize($r->child);
                }
            }
        }
        return $new_data;
    }



}
