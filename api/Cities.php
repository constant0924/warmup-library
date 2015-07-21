<?php
namespace api;
use \api\ApiBase;
/**
*
*/
class Cities extends ApiBase
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

    public function getCitys(){
        $this->Start('/api/admin/classes/areas');
        // var_dump($this->_di);exit;
        return $this->response;
    }
}
