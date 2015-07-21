<?php
namespace api;
use \api\ApiBase;

class Label extends ApiBase
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

    public function getLabels(){
        $this->Start('/api/admin/categories');

        return $this->response;
    }
}
