<?php
namespace library\http;

use \library\util\NumberUtil;

/**
 * json返回类,自动对json格式进行处理
 */
class ResponseJson implements \JsonSerializable
{

    private $responseData;

    /**
     * @param $data 返回数据
     */
    public function __construct($data)
    {
//        $this->responseData = $data;
        $this->responseData = json_encode($data);
        $this->responseData = \Qiniu\json_decode($this->responseData, true);
    }

    function jsonSerialize()
    {
        if (is_array($this->responseData)) {
            $this->jsonArray($this->responseData);
        }
        return $this->responseData;
    }

    private function jsonArray(&$data)
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                $this->jsonArray($value);
            } else {
                $this->jsonObject($value);
            }
        }
    }

    private function jsonObject(&$data)
    {
        if (is_string($data)) {
            if (is_numeric($data) && $data < PHP_INT_MAX) {
                if (NumberUtil::is_int($data)) {
                    $data = intval($data);
                } else if (NumberUtil::is_float($data)) {
                    $data = floatval($data);
                }
            }else if(empty($data)){
                $data = null;
            }
        }
    }

}