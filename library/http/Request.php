<?php
namespace library\http;

/**
 * 自定义Http请求类
 */
class Request extends \Phalcon\Http\Request
{
    /**
     * 所有请求数据
     *
     * @var array
     */
    private $params = false;

    /**
     * 分页对象
     * @var PageParam
     */
    private $page = false;

    /**
     * 对参数进行处理
     */
    private function paramDeal()
    {
        //除去特殊参数
        unset($this->params['_url']);
    }

    /**
     * 设置多个参数
     * @param $params
     */
    public function setParams($params)
    {
        $this->params = $this->getParams() + $params;
        $this->paramDeal();
    }

    /**
     * 设置单个参数
     * @param $key
     * @param $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }


    /**
     * @return array
     */
    public function getParams()
    {
        if ($this->params === false)
        {
            $this->params = [];
            foreach ($this->getQuery() as $key => $value) {
                if ($this->hasPost($key)) {
                    $this->params[$key] = $this->getPost($key);
                } else if ($this->has($key)) {
                    $this->params[$key] = $this->get($key);
                }
            }
            if ($this->isPut()) {
                parse_str($this->getRawBody(), $putParams);
                foreach ($putParams as $key => $value) {
                    $this->params[$key] = $value;
                }
            }
            $this->paramDeal();
        }

        return $this->params;
    }

    /**
     * 获取指定的请求数据
     * @param $key string 数据键名
     * @param $defaultValue mix 默认值
     * @return mix
     */
    public function getParam($key, $defaultValue = false)
    {
        return isset($this->params[$key]) ? $this->params[$key] : $defaultValue;
    }

    /**
     * 是否有指定参数
     *
     * @param $key
     *
     * @return boolean
     */
    public function hasParam($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * 获取分页实例
     *
     * @param $limitMax int 最大获取限量
     * @param $option array 其他可选参数 默认值：['pageStart' => 1, 'limitDefault' => 10, 'limitMax'=>50, 'pageParam' => 'page', 'limitParam' => 'count'] 可单独修改某个参数
     * @return PageParam
     */
    public function getPage($option = [])
    {
        if ($this->page === false) {
            $pageOption = array_merge(['pageStart' => 1, 'limitDefault' => 10, 'limitMax' => 50, 'pageParam' => 'page', 'limitParam' => 'count'], $option);
            $pageStart = $pageOption['pageStart'];
            $limitDefault = $pageOption['limitDefault'];
            $limitMax = $pageOption['limitMax'];

            $page = $this->getParam($pageOption['pageParam'], $pageStart);
            $limit = $this->getParam($pageOption['limitParam'], $limitDefault);

            $this->page = new PageParam($page, $pageStart, $limit, $limitDefault, $limitMax);
        }
        return $this->page;
    }

}