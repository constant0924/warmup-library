<?php 
namespace library\func;
use \api\Template;
/**
* 模板添加方法类
*/
class Functions
{
	private $_volt = null;

	private $_compiler;

	public function __construct(\Phalcon\Mvc\View\Engine\Volt $volt=null)
	{
		try {
			if(!empty($volt)){
				$this->_volt = $volt;
				$this->init();
				$this->addFunc();
			}
		} catch (Exception $e) {
			echo 'Exception：'.$e->getMessage();	
		}
		
	}

	public function init(){
		$this->_compiler = $this->_volt->getCompiler();
	}

	private function addFunc(){
		//在编译器注册方法
		// $this->_compiler->addFunction('getDocTitle', function() {
		// 	var_dump($this);exit;
		// 	// return 1;
		// });
	}

	private function addExtension(){
		//在编译器注册扩展
		// $compiler->addExtension(new PhpFunctionExtension());
	}
}