<?php
namespace library\helper;
use \Phalcon\Tag;

class ViewTag extends Tag
{

    /**
     * 覆盖setTitle方法，可在控制器和视图下调用
     * @param strng  $title 标题
     * @param boolean $cover 覆盖，true覆盖定义的标签前缀，false不覆盖
     * @return string
     */
    public static function setTitle($title,$cover=true){
        $preTitle = self::getTitle();
        $str= trim(preg_replace("/<(\/?title.*?)>/si","",$preTitle));

        if($cover){
            return parent::setTitle($title);
        }
        return parent::setTitle($str.' '.$title);
    }

    public static function getTitle($tag=null){
        $title = parent::getTitle();
        $str= trim(preg_replace("/<(\/?title.*?)>/si","",$title));
        return $str;
    }

    public static function getFavicon() {
        $link = '<link rel="shortcut icon" href="http://warmup.qiniudn.com/@/public/favicon.ico" type="image/x-icon" sizes="64x64">';

        if (APP_ENV === 'development') {
            $link = '<link rel="shortcut icon" href="http://warmup.qiniudn.com/@/public/favicon-test.ico" type="image/x-icon" sizes="64x64">';
        }

        echo $link;
    }
}
