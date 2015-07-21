<?php
namespace library\helper;
class Helper
{

	/**
	 * 功能:将多维数组合并为一位数组
	 * @param array $array :需要合并的数组
	 * @param boolean $clearRepeated:是否清除并后的数组中得重复值
	 * @return array
	 */
	public static function array_multiToSingle($array,$clearRepeated=false){
	    if(!isset($array)||!is_array($array)||empty($array)){
	        return false;
	    }
	    if(!in_array($clearRepeated,array('true','false',''))){
	        return false;
	    }
	    static $result_array=array();
	    foreach($array as $value){
	        if(is_array($value)){
	            self::array_multiToSingle($value);
	        }else{
	            $result_array[]=$value;
	        }
	    }
	    if($clearRepeated){
	        $result_array=array_unique($result_array);
	    }
	    return $result_array;
	}

	/**
	 * (PHP 5 >= 5.5.0)
	 * array_column — 返回数组中指定的一列
	 * array array_column ( array $input , mixed $column_key [, mixed $index_key ] )
	 * @see http://www.php.net/function.array-column
	 * @param array $input
	 * @param mixed $column_key
	 * @param mixed $index_key
	 * @return array 从多维数组中返回单列数组
	 */
    public static function array_column(array $input, $column_key, $index_key = null)
	{
		if(!function_exists('array_column')) {
			if($index_key !== null) {
				// Collect the keys
				$keys = array();
				$i = 0; // Counter for numerical keys when key does not exist

				foreach($input as $row) {
					if(array_key_exists($index_key, $row)) {
						// Update counter for numerical keys
						if(is_numeric($row[$index_key]) || is_bool($row[$index_key])) {
							$i = max($i, (int)$row[$index_key] + 1);
						}

						// Get the key from a single column of the array
						$keys[] = $row[$index_key];
					} else {
						// The key does not exist, use numerical indexing
						$keys[] = $i++;
					}
				}
			}

			if($column_key !== null) {
				// Collect the values
				$values = array();
				$i = 0; // Counter for removing keys

				foreach($input as $row) {
					if(array_key_exists($column_key, $row)) {
						// Get the values from a single column of the input array
						$values[] = $row[$column_key];
						$i++;
					} elseif(isset($keys)) {
						// Values does not exist, also drop the key for it
						array_splice($keys, $i, 1);
					}
				}
			} else {
				// Get the full arrays
				$values = array_values($input);
			}

			if($index_key !== null) {
				return array_combine($keys, $values);
			}

			return $values;
		} else {
			return array_column($input, $column_key, $index_key);
		}
	}	

	/**
	 * 对象转数组
	 * @param  obj $e 对象
	 * @return array
	 */
	public static function objectToArray($e){
		$e=(array)$e;
		foreach($e as $k=>$v){
		if( gettype($v)=='resource' ) return;
		if( gettype($v)=='object' || gettype($v)=='array' )
			$e[$k]=(array)objectToArray($v);
		}
		return $e;
	}

    /**
     * 二维数组去重复项函数
     * @param array $array2D;
     * @param boolen $stkeep;//是否保留一级数组键
     * @param boolen $ndformat;//是否保留二级数组键
     */
    public static function unique_arr($array2D,$stkeep=false,$ndformat=true)  {
        // 判断是否保留一级数组键 (一级数组键可以为非数字)
        if($stkeep) $stArr = array_keys($array2D);

        // 判断是否保留二级数组键 (所有二级数组键必须相同)
        if($ndformat) $ndArr = array_keys(end($array2D));

        //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
        foreach ($array2D as $v){
            $v = join(",",$v);
            $temp[] = $v;
        }

        //去掉重复的字符串,也就是重复的一维数组
        $temp = array_unique($temp);

        //再将拆开的数组重新组装
        foreach ($temp as $k => $v)
        {
            if($stkeep) $k = $stArr[$k];
            if($ndformat)
            {
                $tempArr = explode(",",$v);
                foreach($tempArr as $ndkey => $ndval) $output[$k][$ndArr[$ndkey]] = $ndval;
            }
            else $output[$k] = explode(",",$v);
        }

        return $output;
    }

    //二维数组去掉重复值
    public static function new_array_unique($array)//写的比较好
    {
        $out = array();
        foreach ($array as $key=>$value) {
            if (!in_array($value, $out))
            {
                $out[$key] = $value;
            }
        }
        return $out;
    }

    /**
     * 检查头像
     * @param $url
     * @return string
     */
    public static function checkAvatarUrl($url){
        if(strpos($url,'http') !== 0){
            return 'http://warmup.qiniudn.com/@/default/user_avatar_1.4.png';
        }else{
            return $url;
        }

    }

    /**
     * 转换时间日期
     * @param int $time 时间戳
     * @return date
     */
    public static function changeDate($time){
        return date('Y-m-d H:i:s',$time);
    }

    /**
     * 字符串打断
     *
     * @param string $string 字符串
     * @param int $legth 字符串长度
     * @param string $etc 结尾符号
     * @return string
     */
    public static function truncate_utf8($string, $length, $etc = '...')
    {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for($i = 0; (($i < $strlen) && ($length > 0)); $i++) {
            if($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if($i < $strlen) {
            $result .= $etc;
        }
        return trim($result);
    }

    /**
     * PHP清除html、css、js格式并去除空格的PHP函数
     * @param $string
     * @param $sublen
     * @return mixed|string
     */
    public static function cutstr_html($string, $sublen)
    {
        $string = strip_tags($string);
        $string = preg_replace ('/\n/is', '', $string);
        $string = preg_replace ('/ |　/is', '', $string);
        $string = preg_replace ('/&nbsp;/is', '', $string);

        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $t_string);
        if(count($t_string[0]) - 0 > $sublen) $string = join('', array_slice($t_string[0], 0, $sublen))."…";
        else $string = join('', array_slice($t_string[0], 0, $sublen));

        return $string;
    }

    /**
     * 压缩空格，变为一行
     */
    public static function CompressSpace($content)
    {
        $stripStr = "";
        $tokens = token_get_all($content);
        $last_space = FALSE;
        $i = 0;
        $j = count($tokens);
        for(; $i < $j; ++$i) {
            if(is_string($tokens[$i])) {
                $last_space = FALSE;
                $stripStr .= $tokens[$i];
            } else {
                switch($tokens[$i][0]) {
                    case T_COMMENT :
                    case T_DOC_COMMENT :
                    case T_WHITESPACE :
                        if($last_space) {
                            break;
                        }
                        $stripStr .= " ";
                        $last_space = TRUE;
                        break;
                    default :
                        $last_space = FALSE;
                        $stripStr .= $tokens[$i][1];
                }
            }
        }
        return $stripStr;
    }

    /**
     * 传入文件名的md5值，返回存放地址
     * @param $md5
     * @param string $type
     * @return string
     */
    public static function md52Path( $md5, $type = 'excel' )
    {
        if (empty($md5) || strlen($md5) != 32) return '';

        $base_path = APP_PATH."/storage/";

        $sourcePath = '';
        if ($type == 'excel') {
            $sub1 = substr($md5, 0, 1);
            $sub2 = substr($md5, 1, 2);

            $sourcePath = $base_path.sprintf("excel/%s/%s/%s.xls", $sub1, $sub2, $md5);
        }

        return $sourcePath;
    }


    //for test remove later
    public static function format_nodes(\tidyNode &$node)
    {
//        if($node->name)
//        {
//            echo $node->name.PHP_EOL;
//        }
//        if($node->type)
//        {
//            echo $node->type.PHP_EOL;
//        }
        if($node->attribute)
        {
            $node->attribute=[];
        }
        // check if the current node is of requested type
        if($node->isText()) {
//            if($node->attribute)
//            {
//                echo $node->value.implode(',',$node->attribute).PHP_EOL;
//            }else{
//                echo $node->value.PHP_EOL;
//            }
        }



        // check if the current node has childrens
        if($node->hasChildren()) {
            foreach($node->child as $child) {
                self::format_nodes($child);
            }
        }
    }


}	
?>