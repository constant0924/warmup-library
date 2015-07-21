<?php
namespace library;

use \library\logger\Logger;
use \library\ApiResponseHelper;
use \Phalcon\Mvc\Dispatcher as MvcDispatcher;

/**
 * MVC事件分发处理类
 */
class AppMvcDispatcher extends \Phalcon\Mvc\User\Plugin
{

    /**
     * 开始分发前,这里还不知执行哪个控制器和动作,只知道从Router传递过来的信息
     *
     * @param $event \Phalcon\Events\Event
     * @param $dispatcher \Phalcon\Mvc\Dispatcher
     *
     * @return 返回false可终止后续执行
     */
    function beforeDispatch($event, $dispatcher)
    {
//        \Logger::debug("beforeDispatch ctrlName:{$dispatcher->getControllerName()} actionName:{$dispatcher->getActionName()}");

        if ($dispatcher->getControllerName())

            //获取url中的参数
            $params = $dispatcher->getParams();
        //检查提交数据类型
        $contentType = $dispatcher->getDI()->get('request')->getHeader('CONTENT_TYPE');

        if (stripos($contentType, "application/json") === 0) {//提交数据为json类型
            //提交的原始数据
            $rawBody = $dispatcher->getDI()->get('request')->getRawBody();
            if (!empty($rawBody)) {
                // $params += json_decode($rawBody, true);
                $params += $this->resetParams($rawBody);
            }
        }

//        \Logger::file(\Logger::$LOG_INFO, "loginaccount content-type:$contentType content:" . $this->request->getRawBody());

        //将url中的参数加入到request中方便获取
        $this->request->setParams($params);
    }

    /**
     * 开始路由到指定控制器前,这里已经知道要执行的控制器和动作
     *
     * @param $event \Phalcon\Events\Event
     * @param $dispatcher \Phalcon\Mvc\Dispatcher
     *
     *
     * @return boolean 返回false可终止后续执行
     */
    function beforeExecuteRoute($event, $dispatcher)
    {
        $defNamespace = $dispatcher->getDefaultNamespace();
        $namespace = $dispatcher->getNamespaceName();
        $controllerName = $dispatcher->getControllerName();
        $actionName = $dispatcher->getActionName();
    }

    /**
     * 路由执行完毕
     *
     * @param $event \Phalcon\Events\Event
     * @param $dispatcher \Phalcon\Mvc\Dispatcher
     *
     * @return 不可终止后续执行
     */
    function afterExecuteRoute($event, $dispatcher)
    {
//        \Logger::debug("afterExecuteRoute");
    }

    /**
     * 找不到对应的控制器动作
     *
     * @param $event \Phalcon\Events\Event
     * @param $dispatcher \Phalcon\Mvc\Dispatcher
     *
     * @return 返回false可终止后续执行
     */
    function beforeNotFoundAction($event, $dispatcher)
    {
//        \Logger::debug("beforeNotFoundAction");
        $this->actionNotFound($dispatcher);

        return false;
    }

    /**
     * 抛出异常前，注意只有在路由分发和执行控制器动作阶段的异常才会分发到这里,其他的异常在最外层的try...catch捕获
     *
     * @param $event \Phalcon\Events\Event
     * @param $dispatcher \Phalcon\Mvc\Dispatcher
     * @param $exception \Phalcon\Exception
     *
     * @return 返回false可终止后续执行
     */
    function beforeException($event, $dispatcher, $exception)
    {
        Logger::error("beforeException file:{$exception->getFile()} line:{{$exception->getLine()}}  code:{$exception->getCode()} msg:{$exception->getMessage()}");
        switch ($exception->getCode()) {
            case MvcDispatcher::EXCEPTION_HANDLER_NOT_FOUND: //找不到对应的请求类
            case MvcDispatcher::EXCEPTION_ACTION_NOT_FOUND:
                $this->actionNotFound($dispatcher);

                return false;
        }
    }

    /**
     * 没有找到对应控制器和操作
     */
    private function actionNotFound($dispatcher)
    {
        //根据项目命名空间设置404页面
        $action = 'show404';
        switch ($dispatcher->getNamespaceName()) {
            case 'warmupbackend\controllers\\':
                $action = 'show404';
                break;
            case 'modules\frontend\controllers\\':
                $action = 'upgrading';
                break;
        }

        //找不到对应的动作
        $this->dispatcher->forward(array(
            'controller' => 'errors',
            'action' => $action
        ));
        return false;
    }

    private function resetParams($string){
        $arr = explode('&',$string);
        $arr2 = [];
        foreach ($arr as $key => $value) {
            $temp = explode('=',$value);
            if (count($temp) > 1) $arr2[$temp[0]] = urldecode($temp[1]);
        }
        return $arr2;
    }
}
