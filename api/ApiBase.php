<?php
namespace api;

use \Curl\Curl;
use \Mobile_Detect;

/**
 * api接口基础抽象类
 */
abstract class ApiBase extends Curl implements ApiInterface
{

    /**
     * @var 服务类实例(单例模式)
     */
    private static $service;

    /**
     * 设备检测
     * @var object
     */
    protected $detect;

    /**
     * 代理ip
     * @var string
     */
    private $PROXY = '210.21.113.236';

    /**
     * 代理端口
     * @var string
     */
    private $PROXYPORT = '80';

    private $di;

    /**
     * @param $serviceClass 服务类class
     * @param $di \Phalcon\DiInterface
     *
     * @return ServiceBase
     */
    protected static function init($serviceClass,$di)
    {

        if (empty(self::$service)) {
            return new $serviceClass($di);
        }
        return self::$service;
    }

    /**
     * api代理请求
     * @param string $url 请求api链接地址
     * @param array $params 请求参数
     * @param string $method curd请求方法
     */
    public function Start($url, $params=[],$method='get'){
        if(empty($this->detect)){
            $this->detect = new Mobile_Detect();
        }
        try{
            if(APP_PROXY_OPTION){ //应用代理是否使用
                //设置curl代理请求地址和请求模式
                // $this->curl->setOpt(CURLOPT_PROXYAUTH,CURLAUTH_BASIC);
                $this->setOpt(CURLOPT_PROXYPORT, $this->PROXYPORT);
                $this->curl->setOpt(CURLOPT_PROXY, $this->PROXY);
                $this->curl->setOpt(CURLOPT_PROXYTYPE,CURLPROXY_HTTP);
            }

            $this->setHeader('X_CLIENT','web');
            //用户登陆cookies
            $user = $this->getCookies();
            if(!empty($user) && is_array($user)){
                // $headers = ['X_CLIENT: web',"X_ACCESS_TOKEN: {$user['token']}"];
                $this->setHeader('X_ACCESS_TOKEN',$user['token']);
                
            }

            //执行curl方法
            $this->{$method}($this->gethost().$url,$params);

            //判断是否错误
            if ($this->error) {
                echo 'error：'.$this->error_message;exit;
            }

            //检查请求返回结果
            $this->checkResult();

            // return $res;
        }catch (\Exception $e){
            return $e->getMessage();
        }

    }

    /**
     * 获取用户登陆cookies
     * @return array|null
     */
    public function getCookies(){
        if(!empty($this->_di)){
            $user = $this->_di->getCookies()->get('user')->getValue();
            if(!empty($user)){
                return json_decode($user,true);
            }
        }
        return null;
    }

    /**
     * 设置代理ip
     * @param [type] $ip [description]
     */
    public function setProxy($ip){
        $this->PROXY = $ip;
    }

    /**
     * 设置代理端口
     * @param [type] $port [description]
     */
    public function setProxyPort($port){
        $this->PROXYPORT = $port;
    }

    public function gethost(){
        $url = 'http://test.api.warmup.cc';
        if(APP_ENV === 'production'){
            $url = 'http://api2.warmup.cc';
        }

        return $url;
    }

    /**
     * 检查请求返回结果
     */
    protected function checkResult(){
        // var_dump($this->response);exit;
        if(($this->response->status == 10003 || $this->response->status == 10004) && !empty($this->_di) ){
            if(!$this->_di->get('request')->isAjax()){
                $this->_di->get('response')->redirect('/login');
            }   
        }

        if($this->response->status !== 200){
            if(!$this->_di->get('request')->isAjax()){
                $this->_di->get('dispatcher')->forward([
                    'controller' => 'errors',
                    'action' => 'showErrorMessage',
                    'params' => ['msg'=>$this->response->result]
                ]);
            }
        }
    }
}
