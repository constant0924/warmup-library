<?php
namespace library\http;

use \library\logger\Logger;

/**
 * 分页类
 * @package warmup\library\http
 */
class PageParam
{

    /**
     * 开始页码
     * @var int
     */
    private $pageStart;

    /**
     * 页码
     * @var int
     */
    private $page;

    /**
     * 分页获取限量
     * @var
     */
    private $limit;

    public function __construct($page, $pageStart, $limit, $limitDefault, $limitMax)
    {

        $page = (int)$page;
        $limit = (int)$limit;

        if (!is_int($page) || $page < 0) {
            $page = $pageStart;
        }

        if (!is_int($limit) || $limit <= 0) {
            $limit = $limitDefault;
        }

        if ($limit > $limitMax) {
            $limit = $limitMax;
        }

        $this->page = $page;
        $this->limit = $limit;
        $this->pageStart = $pageStart;

        Logger::debug("page:{$page} pageStart:{$pageStart} limit:{$limit} limitDefault:{$limitDefault} limitMax:{$limitMax} offset:{$this->dbOffset()}");
    }

    /**
     * 页码
     */
    public function page()
    {
        return $this->page;
    }

    /**
     * 获取限量
     */
    public function limit()
    {
        return $this->limit;
    }

    /**
     * 数据库开始偏移量
     */
    public function dbOffset()
    {
        $page = $this->page - $this->pageStart;
        if ($page < 0) {
            $page = 0;
        }
        return $page * $this->limit;
    }

}