<?php
namespace api;
use api\ApiBase;
use library\LinkManager;
/**
*
*/
class Ajax extends ApiBase
{

  protected $_di;

  public function __construct($di=null){
    parent::__construct();
    if(empty($this->_di)){
      $this->_di = $di;
    }
  }
  /**
     * 获取服务实例方法
     * @param $di
     *
     * @return CategoriesService
     */
    public static function getInstance($di=null)
    {
        return self::init(self::class,$di);
    }

    public function getResult($api, $data = [], $method = 'get') {
      $this->Start($api, $data, $method);

      return $this->response;
    }
}
