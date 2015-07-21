<?php 
/**
* 基本应用程序管理器
* 默认公共配置信息，新应用继承此类执行main方法即可
*/
abstract class BaseAppManager extends \Phalcon\Mvc\Application
{
	/**
	 * di
	 * @var object
	 */
	protected $di;

	/**
	 * 加载器
	 * @var [type]
	 */
	protected $loder;

	/**
	 * 初始化执行方法
	 */
	public function init(){
		if(empty($this->di)){
			$this->di = new \Phalcon\DI\FactoryDefault();
		}
		if(empty($this->loader)){
			$this->loader = new \Phalcon\Loader();
		}   
	}

	/**
	 * 注册服务 | 默认公共使用的服务
	 */
	public function registerServices(){

        /**
         * 替换默认Http请求实现
         */
        $this->di->set('request', new \library\http\Request());

        /**
         * URL地址，设置baseUri
         */
        $this->di->set('url', function () {
            $url = new Phalcon\Mvc\Url();

            $url->setBaseUri('/');
            // if (APP_ENV === 'production') {
            //     $url->setBaseUri('http://api2.warmup.cc/');
            // }elseif(APP_ENV === 'localhost_liheng'){
            //     $url->setBaseUri('/');
            // }

            return $url;
        });

        /**
         * 注册cookies服务
         */
        $this->di->set('cookies', function() {
            $cookies = new Phalcon\Http\Response\Cookies();
            $cookies->useEncryption(false);
            return $cookies;
        });

        /**
         * 注册tag服务
         */
        $this->di->set('tag', function() {
            $tag = new library\helper\ViewTag();
            return $tag;
        });

        /**
		 * 加载配置文件（如果存在）
		 */
		if (is_readable(APP_PATH . '/config/config.php')) {
		    require APP_PATH . '/config/config.php';

		    $config = $config[APP_ENV];

		    $this->di->set('config', new \Phalcon\Config($config));
		}

	}

    public function serLoader($dirs = [], $namespace=[] ){
        //注册自动加载目录 和 注册自动加载命名空间
        $this->loader->registerDirs($dirs)->registerNamespaces($namespace)->register();
        // $this->loader->registerDirs([
        //     APP_PATH.'/library/',
        //     APP_PATH.'/config/routes/',
        //     APP_PATH.'/api/',
        // ])->registerNamespaces([
        //         'warmupweb' => APP_PATH,
        //         'warmupbackend' => realpath('../..').'/warmupbackend/app'
        // ])->register();
    }

	protected function setDispatcher($di,$namespace){
		/**
         * Mvc事件分发拦截器
         */
        $di->set('dispatcher', function () use ($di,$namespace) {

            $evManager = $di->getShared('eventsManager');
            // 注册自定义拦截器
            $evManager->attach('dispatch', new \library\AppMvcDispatcher($di));
            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setDefaultNamespace($namespace); //设置默认明名空间调度
            $dispatcher->setEventsManager($evManager);

            return $dispatcher;
        }, true);
	}

	protected function setViewServer($dir,$compiledPath){
		//注册视图组件
        $this->di->set('view', function() use($dir,$compiledPath) {
            $view = new \Namedfish\Mvc\View();
            $view->setViewsDir($dir); //设置视图目录

            //注册模板引擎
            $view->registerEngines(array(
                ".html" => function ($view, $di) use($compiledPath){
                    $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

                    //set some options here
                    $volt->setOptions(array(
                        "compiledPath" => $compiledPath
                    ));

                    // $compiler = $volt->getCompiler();
                    $compiler = new \library\func\Functions($volt);

                    return $volt;
                }
            ));

            return $view;
        });
	}

    protected function setTagServer($tags=[]){
        if(!empty($tags)){
            foreach ($tags as $key => $value) {
                $this->di->set($key,function() use($value) {
                    return $value;
                });
            }
        }
    }

	/**
	 * 应用执行方法
	 */
	public function main(){}
}