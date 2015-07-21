<?php
namespace api;

/**
 * API服务接口
 * @package warmup\api
 */
interface ApiInterface
{

    /**
     * 获取服务实例方法
     * @param $curl \Curl\Curl
     */
    public static function getInstance($di=null);

}