<?php
namespace library\util;
/**
 * 数字工具类
 */

class NumberUtil
{

    /**
     * 整型判断
     * @param $var
     * @return bool
     */
    public static function is_int($var)
    {
        return is_int($var + 0);
    }

    /**
     * 浮点数判断(php中float和double判断方法一样)
     * @param $var
     * @return bool
     */
    public static function is_float($var)
    {
        return is_float($var + 0);
    }

    /**
     * 字符串转数字
     *
     * 转换失败返回false
     */
    public static function numberval($var){
        if (self::is_int($var)) {
            return intval($var);
        } else if (self::is_float($var)) {
           return floatval($var);
        }

        return false;
    }

}